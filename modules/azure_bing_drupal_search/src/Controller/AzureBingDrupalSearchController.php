<?php

namespace Drupal\azure_bing_drupal_search\Controller;

use Drupal\search\Controller\SearchController;
use Drupal\search\SearchPageInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Route controller for Bing Custom Search.
 */
class AzureBingDrupalSearchController extends SearchController {

  /**
   * {@inheritdoc}
   */
  public function view(Request $request, SearchPageInterface $entity) {
    /** @var \Drupal\azure_bing_drupal_search\Plugin\Search\AzureBingDrupalSearch $plugin */
    $plugin = $entity->getPlugin();
    $build = parent::view($request, $entity);

    // Alter the pager to set # of page links.
    $build['pager']['#quantity'] = 10;

    return [
      '#cache' => $build['#cache'],
      '#title' => $build['#title'],
      'search_form' => $build['search_form'],
      'search_results_title' => @$build['search_results_title'],
      'links' => $plugin->getSearchOptions($request),
      'search_results' => $build['search_results'],
      'pager' => $build['pager'],
    ];
  }

}
