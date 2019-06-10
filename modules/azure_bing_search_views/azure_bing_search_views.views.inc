<?php

/**
 * @file
 */

/**
 * Implements hook_views_data().
 */
function azure_bing_search_views_views_data() {
  $data = [];

  // Base data.
  $data['azure_bing_search']['table']['group'] = t('Bing Search');

  $data['azure_bing_search']['table']['base'] = [
    'title' => t('Bing Search'),
    'help' => t('Bing search results'),
    'query_id' => 'azure_bing_search_views',
  ];

  // Fields.
  // See https://docs.microsoft.com/en-us/rest/api/cognitiveservices-bingsearch/bing-custom-search-api-v7-reference#webpage
  // Webpage Name field.
  $data['azure_bing_search']['name'] = [
    'title' => t('Webpage Name'),
    'help' => t('Use this name along with url to create a hyperlink that when clicked takes the user to the webpage.'),
    'field' => [
      // See Drupal\azure_bing_search_views\Plugin\views\field\AzureSearchResultTitle.
      'id' => 'azure_search_result_title',
    ],
  ];

  // URL field.
  $data['azure_bing_search']['url'] = [
    'title' => t('URL'),
    'help' => t('Use this URL along with name to create a hyperlink that when clicked takes the user to the webpage.'),
    'field' => [
      // See /core/modules/views/src/Plugin/views/field.
      'id' => 'standard',
    ],
  ];

  // Snippet field.
  $data['azure_bing_search']['snippet'] = [
    'title' => t('Snippet'),
    'help' => t('A snippet of text from the webpage that describes its contents.'),
    'field' => [
      // See Drupal\azure_bing_search_views\Plugin\views\field\AzureSearchResultSnippet.
      'id' => 'azure_search_result_snippet',
    ],
  ];

  // Keyword field.
  $data['azure_bing_search']['keyword'] = [
    'title' => t('Search Keywords'),
    'help' => t('The keywords to search for'),
    'filter' => [
      'id' => 'azure_bing_search_views_keyword',
    ],
  ];

  return $data;
}
