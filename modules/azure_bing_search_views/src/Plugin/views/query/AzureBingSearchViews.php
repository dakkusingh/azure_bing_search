<?php
namespace Drupal\azure_bing_search_views\Plugin\views\query;

use Drupal\azure_bing_search\Service\BingCustomSearch;
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
  public function execute(ViewExecutable $view) {
  // TODO
  $params = [
    'offset' => 0,
    'count' => 10,
  ];

  $response = $this->bingCustomSearch->searchResults('post', $params);
  if (isset($response['webPages']['value'])) {
    $results = $response['webPages']['value'];

    //  ksm($results);
    //  ksm($response);
    $index = 0;

    foreach ($results as $item) {
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
   * the Views integration of the Fitbit API.
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
  public function addField($table, $field, $alias = '', $params = array()) {
    return $field;
  }

}