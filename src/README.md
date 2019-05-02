# Azure Bing Search
The Bing Custom Search API enables you to create tailored ad-free search experiences for topics that you care about. You can specify the domains and webpages for Bing to search, as well as pin, boost, or demote specific content to create a custom view of the web and help your users quickly find relevant search results.

![Azure Bing Search](https://docs.microsoft.com/en-us/azure/cognitive-services/bing-custom-search/media/bcs-overview.png)

## Requirements
* To use Bing Custom Search, you need to create a custom search instance that defines your view or slice of the web. This instance contains the public domains, websites, and webpages that you want to search, along with any ranking adjustments you may want.
* You must have a [Cognitive Services API account](https://docs.microsoft.com/azure/cognitive-services/cognitive-services-apis-create-account) with access to the Bing Search APIs. If you don't have an Azure subscription, you can [create an account](https://azure.microsoft.com/try/cognitive-services/?api=bing-news-search-api) for free. Before continuing, You will need the access key provided after activating your free trial, or a paid subscription key from your Azure dashboard.

## Drupal Modules Provided
### Azure Bing Search API module
Integrates Azure Bing Search API with Drupal and provide a convienent service, which can be used in your custom modules.

#### Setup Azure Bing Search API module
* Create your first Bing Custom Search instance: [See instructions](https://docs.microsoft.com/en-us/azure/cognitive-services/bing-custom-search/quick-start)
* Visit settings page at `/admin/config/search/azure_bing_search/settings` 
* Add Custom Configuration ID - Generated in step 1 above
* Add `Ocp-Apim-Subscription-Key` as Subscription Key.
* Configure any other defaults you need to tweak.

#### Usage Example
Using Dependency Injection:
[See this example on Github](https://github.com/dakkusingh/azure_bing_search/blob/8.x-1.x/modules/azure_bing_drupal_search/src/Plugin/Search/AzureBingDrupalSearch.php#L229)


Not Using dependency Injection:
```
$bingCustomSearchService = \Drupal::service('azure_bing_search.bingcustomsearch');

$results = $bingCustomSearchService->searchResults('mykeyword');
```

### Azure Bing Drupal Search module
Integrates Azure Bing Search with Drupal Core Search. It provides a Search Plugin for the Drupal core search to use Bing Search as a results provider.

## Bing Custom Search API
The Custom Search API lets you send a search query to Bing and get back web pages from the slice of Web that your Custom Search instance defines. [See more here](https://docs.microsoft.com/en-us/rest/api/cognitiveservices/bing-custom-search-api-v7-reference)

### API References
* For information about headers that requests should include, see [Request Headers](https://docs.microsoft.com/en-us/rest/api/cognitiveservices/bing-custom-search-api-v7-reference#headers).
* For information about query parameters that requests should include, see [Query Parameters](https://docs.microsoft.com/en-us/rest/api/cognitiveservices/bing-custom-search-api-v7-reference#query-parameters).
* For information about the JSON objects that the response may include, see [Response Body](https://docs.microsoft.com/en-us/rest/api/cognitiveservices/bing-custom-search-api-v7-reference#response-objects).
* For information about permitted use and display of results, see [Bing Search API Use and Display requirements](https://docs.microsoft.com/azure/cognitive-services/bing-custom-search/use-and-display-requirements).
* For information about error codes, see [Error Codes](https://docs.microsoft.com/en-us/rest/api/cognitiveservices/bing-custom-search-api-v7-reference#error-codes).
* For information about Market and Country codes, see [Market & Country Codes](https://docs.microsoft.com/en-us/rest/api/cognitiveservices/bing-custom-search-api-v7-reference#market-codes).

## Quickstart: Create your first Bing Custom Search instance
To use Bing Custom Search, you need to create a custom search instance that defines your view or slice of the web. This instance contains the public domains, websites, and webpages that you want to search, along with any ranking adjustments you may want. [See more details on how to create your first Bing Custom Search instance](https://docs.microsoft.com/en-us/azure/cognitive-services/bing-custom-search/quick-start)