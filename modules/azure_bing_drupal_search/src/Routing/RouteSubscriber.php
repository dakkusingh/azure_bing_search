<?php

namespace Drupal\azure_bing_drupal_search\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
use Drupal\azure_bing_drupal_search\Plugin\Search\AzureBingDrupalSearch;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {

    // Alter search page controller for this module's plugin.
    /** @var \Drupal\search\SearchPageRepositoryInterface $searchPageRepository */
    $searchPageRepository = \Drupal::service('search.search_page_repository');

    foreach ($searchPageRepository->getActiveSearchPages() as $entity_id => $entity) {
      if ($entity->getPlugin() instanceof AzureBingDrupalSearch && $route = $collection->get("search.view_$entity_id")) {
        $route->setDefault(
          '_controller',
          'Drupal\azure_bing_drupal_search\Controller\AzureBingDrupalSearchController::view'
        );
      }
    }
  }

}
