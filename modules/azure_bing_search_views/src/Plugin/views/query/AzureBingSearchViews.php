<?php

namespace Drupal\azure_bing_search_views\Plugin\views\query;

use Drupal\azure_bing_search\Service\BingCustomSearch;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * AzureBingSearchViews views query plugin which wraps calls to the
 * Azure Bing Search API in order to expose the results to views.
 *
 * @ViewsQuery(
 *   id = "azure_bing_search_views",
 *   title = @Translation("Azure Bing Search Views"),
 *   help = @Translation("Query against the Azure Bing Search API.")
 * )
 */
class AzureBingSearchViews extends QueryPluginBase {

  /**
   * Bing Custom Search Service.
   *
   * @var \Drupal\azure_bing_search\Service\BingCustomSearch
   */
  private $bingCustomSearch;

  /**
   * Collection of filter criteria.
   *
   * @var array
   */
  protected $where;

  /**
   * Max number of items (`count`) via API.
   */
  const MAX_NUM = 50;

  /**
   * AzureBingSearchViews constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\azure_bing_search\Service\BingCustomSearch $bingCustomSearch
   *   Bing custom search service.
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              BingCustomSearch $bingCustomSearch) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->bingCustomSearch = $bingCustomSearch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container,
                                array $configuration,
                                $plugin_id,
                                $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('azure_bing_search.bingcustomsearch')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(ViewExecutable $view) {
    // Mostly modeled off of \Drupal\views\Plugin\views\query\Sql::build()
    // Store the view in the object to be able to use it later.
    $this->view = $view;

    $view->initPager();

    // Let the pager modify the query to add limits.
    $view->pager->query();

    // Export parameters for preview.
    $view->build_info['query'] = $this->query();
  }

  /**
   * {@inheritdoc}
   */
  public function query($get_count = FALSE) {
    // Fill up the $query array with properties that we will use in forming the
    // API request.
    $query = [];

    // Iterate over $this->where to gather up the filtering conditions to pass
    // along to the API. Note that views allows grouping of conditions, as well
    // as group operators. This does not apply to us, as the Bing Search API
    // has no such concept, nor do we support this concept for filtering.
    if (isset($this->where)) {
      foreach ($this->where as $where) {
        foreach ($where['conditions'] as $condition) {
          // Remove dot from beginning of the string.
          $field_name = ltrim($condition['field'], '.');
          $query[$field_name] = $condition['value'];
        }
      }
    }

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(ViewExecutable $view) {
    $view->result = [];
    $view->total_rows = 0;

    $results = $this->findResults($view->pager->getCurrentPage());

    if ($results['count'] != 0) {
      // Store the results.
      $view->pager->total_items = $view->total_rows = $results['count'];
      $view->pager->updatePageInfo();

      $index = 0;

      foreach ($results['items'] as $item) {
        $row = [];
        $row['name'] = $item['name'];
        $row['url'] = $item['url'];
        $row['snippet'] = $item['snippet'];

        // If we got some data back from the API for this keyword,
        // add defaults and expose as a row to views.
        if (!empty($row)) {
          $row['index'] = $index++;
          $view->result[] = new ResultRow($row);
        }

      }
    }
  }

  /**
   * Queries to find search results, and sets status messages.
   *
   * This method can assume that $this->isSearchExecutable() has already been
   * checked and returned TRUE.
   *
   * @return array|null
   *   Results from search query execute() method, or NULL if the search
   *   failed.
   */
  protected function findResults($page) {
    $results['count'] = 0;
    $results['items'] = 0;

    $page_size = $this->view->getItemsPerPage();

    // Reconcile items per page with API max 50.
    $n = $page_size < self::MAX_NUM ? $page_size : self::MAX_NUM;

    for ($i = 0; $i < $page_size; $i += self::MAX_NUM) {
      $offset = $page * $page_size + $i;

      if (!$response = $this->getResults($n, $offset)) {
        break;
      }

      if (isset($response['webPages']['value'])) {
        $results['count'] = $response['webPages']['totalEstimatedMatches'];
        $results['items'] = $response['webPages']['value'];
      }
      else {
        break;
      }
    }

    return $results;
  }

  /**
   * Get query result.
   *
   * @param int $n
   *   Number of items.
   * @param int $offset
   *   Offset of items (0-indexed).
   *
   * @return object|null
   *   Decoded response from Bing, or empty array on error.
   */
  protected function getResults($n = 1, $offset = 0) {
    $params = [
      'offset' => $offset,
      'count' => $n,
    ];

    // Grab data regarding conditions placed on the query.
    $query = $this->view->build_info['query'];

    // TODO find a better way.
    $keyword = $query['keyword'][0];

    return $this->bingCustomSearch->searchResults($keyword, $params);
  }

  /**
   * Adds a simple condition to the query. Collect data on the configured filter
   * criteria so that we can appropriately apply it in the query() and execute()
   * methods.
   *
   * @param $group
   *   The WHERE group to add these to; groups are used to create AND/OR
   *   sections. Groups cannot be nested. Use 0 as the default group.
   *   If the group does not yet exist it will be created as an AND group.
   * @param $field
   *   The name of the field to check.
   * @param $value
   *   The value to test the field against. In most cases, this is a scalar. For more
   *   complex options, it is an array. The meaning of each element in the array is
   *   dependent on the $operator.
   * @param $operator
   *   The comparison operator, such as =, <, or >=. It also accepts more
   *   complex options such as IN, LIKE, LIKE BINARY, or BETWEEN. Defaults to =.
   *   If $field is a string you have to use 'formula' here.
   *
   * @see \Drupal\Core\Database\Query\ConditionInterface::condition()
   * @see \Drupal\Core\Database\Query\Condition
   */
  public function addWhere($group, $field, $value = NULL, $operator = NULL) {
    // Ensure all variants of 0 are actually 0. Thus '', 0 and NULL are all
    // the default group.
    if (empty($group)) {
      $group = 0;
    }

    // Check for a group.
    if (!isset($this->where[$group])) {
      $this->setWhereGroup('AND', $group);
    }

    $this->where[$group]['conditions'][] = [
      'field' => $field,
      'value' => $value,
      'operator' => $operator,
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['safeSearch'] = [
      'default' => 'Off',
    ];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    // See https://docs.microsoft.com/en-us/rest/api/cognitiveservices-bingsearch/bing-custom-search-api-v7-reference#query-parameters
    $form['safeSearch'] = [
      '#title' => $this->t('Safe Search'),
      '#type' => 'select',
      '#options' => [
        'Off' => t('Off'),
        'Moderate' => t('Moderate'),
        'Strict' => t('Strict'),
      ],
      '#description' => $this->t('A filter used to filter webpages for adult content.'),
      '#default_value' => $this->options['safeSearch'],
    ];
  }

  /**
   * The following methods replicate the interface of Views' default SQL query
   * plugin backend to simplify the Views integration of the Azure Bing Search
   * API. It's necessary to define these, since many handlers assume they are
   * working against a SQL query plugin backend. There is an issue that details
   * this lack of an enforced contract as a bug
   * (https://www.drupal.org/node/2484565).
   *
   * @see https://www.drupal.org/node/2484565
   */

  /**
   * Ensures a table exists in the query.
   *
   * This replicates the interface of Views' default SQL backend to simplify
   * the Views integration of the Azure Bing Search API. Since the Azure Bing
   * Search API has no concept of "tables", this method implementation
   * does nothing.
   * See https://www.drupal.org/node/2484565 for more information.
   *
   * @return string
   *   An empty string.
   */
  public function ensureTable($table, $relationship = NULL) {
    return '';
  }

  /**
   * Adds a field to the table. In our case, the Azure Bing Search API has no
   * notion of limiting the fields that come back, so tracking a list
   * of fields to fetch is irrellevant for us. Hence this function body is more
   * or less empty and it serves only to satisfy handlers that may assume an
   * addField method is present b/c they were written against Views' default SQL
   * backend.
   *
   * This replicates the interface of Views' default SQL backend to simplify
   * the Views integration.
   *
   * @param string $table
   *   NULL in most cases, we could probably remove this altogether.
   * @param string $field
   *   The name of the metric/dimension/field to add.
   * @param string $alias
   *   Probably could get rid of this too.
   * @param array $params
   *   Probably could get rid of this too.
   *
   * @return string
   *   The name that this field can be referred to as.
   *
   * @see \Drupal\views\Plugin\views\query\Sql::addField()
   */
  public function addField($table, $field, $alias = '', $params = []) {
    return $field;
  }

}
