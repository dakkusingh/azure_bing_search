<?php

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

  $data['azure_bing_search']['name'] = [
    'title' => t('Webpage Name'),
    'help' => t('Use this name along with url to create a hyperlink that when clicked takes the user to the webpage.'),
    'field' => [
      // See /core/modules/views/src/Plugin/views/field
      'id' => 'standard',
    ],
  ];

  $data['azure_bing_search']['url'] = [
    'title' => t('URL'),
    'help' => t('Use this URL along with name to create a hyperlink that when clicked takes the user to the webpage.'),
    'field' => [
      // See /core/modules/views/src/Plugin/views/field
      'id' => 'standard',
    ],
  ];

  $data['azure_bing_search']['snippet'] = [
    'title' => t('Snippet'),
    'help' => t('A snippet of text from the webpage that describes its contents.'),
    'field' => [
      // See /core/modules/views/src/Plugin/views/field
      'id' => 'standard',
    ],
  ];

//  $data['azure_bing_search']['id'] = [
//    'title' => t('ID'),
//    'help' => t('ID'),
//    'field' => [
//      'id' => 'standard',
//    ],
//  ];
  //
//  $data['azure_bing_search']['urlPingSuffix'] = [
//    'title' => t('Height'),
//    'help' => t('Fibit users\'s height.'),
//    'field' => [
//      'id' => 'numeric',
//      'float' => TRUE,
//    ],
//  ];
//
//  $data['azure_bing_search']['about'] = [
//    'title' => t('Height'),
//    'help' => t('Fibit users\'s height.'),
//    'field' => [
//      'id' => 'numeric',
//      'float' => TRUE,
//    ],
//  ];
//
//  $data['azure_bing_search']['isFamilyFriendly'] = [
//    'title' => t('Height'),
//    'help' => t('Fibit users\'s height.'),
//    'field' => [
//      'id' => 'numeric',
//      'float' => TRUE,
//    ],
//  ];
//
//  $data['azure_bing_search']['displayUrl'] = [
//    'title' => t('Height'),
//    'help' => t('Fibit users\'s height.'),
//    'field' => [
//      'id' => 'numeric',
//      'float' => TRUE,
//    ],
//  ];

//  $data['azure_bing_search']['deepLinks'] = [
//    'title' => t('Height'),
//    'help' => t('Fibit users\'s height.'),
//    'field' => [
//      'id' => 'numeric',
//      'float' => TRUE,
//    ],
//  ];
//
//  $data['azure_bing_search']['dateLastCrawled'] = [
//    'title' => t('Height'),
//    'help' => t('Fibit users\'s height.'),
//    'field' => [
//      'id' => 'numeric',
//      'float' => TRUE,
//    ],
//  ];
//
//  $data['azure_bing_search']['fixedPosition'] = [
//    'title' => t('Height'),
//    'help' => t('Fibit users\'s height.'),
//    'field' => [
//      'id' => 'numeric',
//      'float' => TRUE,
//    ],
//  ];
//
//  $data['azure_bing_search']['language'] = [
//    'title' => t('Height'),
//    'help' => t('Fibit users\'s height.'),
//    'field' => [
//      'id' => 'numeric',
//      'float' => TRUE,
//    ],
//  ];
//
//  $data['azure_bing_search']['isNavigational'] = [
//    'title' => t('Height'),
//    'help' => t('Fibit users\'s height.'),
//    'field' => [
//      'id' => 'numeric',
//      'float' => TRUE,
//    ],
//  ];

  return $data;
}