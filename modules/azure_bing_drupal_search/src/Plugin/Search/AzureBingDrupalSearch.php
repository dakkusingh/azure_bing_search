<?php

namespace Drupal\azure_bing_drupal_search\Plugin\Search;

use Drupal\azure_bing_search\Service\BingCustomSearch;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessibleInterface;
use Drupal\search\Plugin\ConfigurableSearchPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles search using Bing Custom Search.
 *
 * @SearchPlugin(
 *   id = "azure_bing_drupal_search",
 *   title = @Translation("Azure Bing Custom Search")
 * )
 */
class AzureBingDrupalSearch extends ConfigurableSearchPluginBase implements AccessibleInterface {

  /**
   * Max number of items (`count`) via API.
   */
  const MAX_NUM = 50;

  /**
   * Total number of results.
   *
   * @var int
   */
  protected $count;

  /**
   * Bing Custom Search Service.
   *
   * @var \Drupal\azure_bing_search\Service\BingCustomSearch
   */
  private $bingCustomSearch;

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
   * Constructs a \Drupal\node\Plugin\Search\NodeSearch object.
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
    $this->bingCustomSearch = $bingCustomSearch;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    // Allow overrides, e.g. different search engines per language.
    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $values = [];
    $values['page_size'] = 10;
    $values['safe_search'] = 'Off';

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['page_size'] = [
      '#title' => $this->t('Page size'),
      '#type' => 'textfield',
      '#description' => $this->t('Number of results to display per page.'),
      '#default_value' => $this->configuration['page_size'],
      '#size' => 5,
      '#max_length' => 5,
    ];

    $form['safe_search'] = [
      '#title' => $this->t('Safe Search'),
      '#type' => 'select',
      '#options' => [
        'Off' => t('Off'),
        'Moderate' => t('Moderate'),
        'Strict' => t('Strict'),
      ],
      '#description' => $this->t('A filter used to filter webpages for adult content.'),
      '#default_value' => $this->configuration['safe_search'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $keys = [];

    $defaults = [
      'page_size',
      'safe_search',
    ];

    $keys = array_merge($keys, $defaults);
    foreach ($keys as $key) {
      $this->configuration[$key] = $form_state->getValue($key);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($operation = 'view',
                         AccountInterface $account = NULL,
                         $return_as_object = FALSE) {
    $result = AccessResult::allowedIfHasPermission($account, 'access content');
    return $return_as_object ? $result : $result->isAllowed();
  }

  /**
   * {@inheritdoc}
   */
  public function execute() {
    if ($this->isSearchExecutable()) {

      $page = pager_find_page();
      $results = $this->findResults($page);

      // API total results is unreliable. Sometimes when requesting a large
      // offset we get no results, and
      // $response->searchInformation->totalResults is 0. In this case return
      // the previous page's items.
      while ($page && !count($results)) {
        $results = $this->findResults(--$page);
      }

      pager_default_initialize($this->count, $this->configuration['page_size']);

      if ($results) {
        return $this->prepareResults($results);
      }
    }

    return [];
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
    $items = [];

    $page_size = $this->configuration['page_size'];

    // Reconcile items per page with API max 50.
    $n = $page_size < self::MAX_NUM ? $page_size : self::MAX_NUM;

    for ($i = 0; $i < $page_size; $i += self::MAX_NUM) {
      $offset = $page * $page_size + $i;

      if (!$response = $this->getResults($n, $offset)) {
        break;
      }

      if (isset($response['webPages']['value'])) {
        $this->count = $response['webPages']['totalEstimatedMatches'];
        $items = array_merge($items, $response['webPages']['value']);
      }
      else {
        break;
      }

    }

    return $items;
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

    return $this->bingCustomSearch->searchResults($this->getKeywords(), $params);
  }

  /**
   * Prepares search results for rendering.
   *
   * @param array $items
   *   Results found from a successful search query execute() method.
   *
   * @return array
   *   Array of search result item render arrays (empty array if no results).
   */
  protected function prepareResults(array $items) {
    $results = [];

    foreach ($items as $item) {
      $results[] = [
        'link' => $item['url'],
        'title' => $item['name'],
        'snippet' => [
          '#markup' => $item['snippet'],
        ],
      ];
    }

    return $results;
  }

  /**
   * Gets render array for search option links.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Symfony Request obj.
   *
   * @return array
   *   render array for search option links.
   */
  public function getSearchOptions(Request $request) {
    $options = [];

    if (count($options)) {
      $query = $this->getParameters();
      $active = empty($query['type']);

      if (!$active) {
        unset($query['type']);
      }

      $url = Url::createFromRequest($request);
      $url->setOption('query', $query);
      $url->setOption('attributes', $active ? ['class' => ['is-active']] : []);

      $options['all'] = [
        '#title' => $this->t('All'),
        '#type' => 'link',
        '#url' => $url,
        '#weight' => -1,
      ];

      return [
        '#theme' => 'item_list',
        '#items' => $options,
      ];
    }

    return [];
  }

}
