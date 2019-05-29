<?php

namespace Drupal\azure_bing_search\Service;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Exception;

/**
 * Provides Bing Custom Search Results.
 */
class BingCustomSearch {

  // TODO 1) Provide a Schema in config/schema/azure_bing_search.schema.yml
  // TODO 3) Improve exception output for better readability
  // TODO 4) Better handling of exception with response status codes.
  /**
   * Config.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  private $config;

  /**
   * Language Manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  private $languageManager;

  /**
   * The HTTP client to fetch the API data.
   *
   * @var \Drupal\Core\Http\ClientFactory
   */
  private $httpClientFactory;

  /**
   * LoggerChannelFactory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  public $loggerFactory;

  /**
   * BingCustomSearch constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Config.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   Language Manager.
   * @param \Drupal\Core\Http\ClientFactory $httpClientFactory
   *   A Guzzle client object.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   LoggerChannelFactory.
   */
  public function __construct(ConfigFactory $configFactory,
                              LanguageManagerInterface $languageManager,
                              ClientFactory $httpClientFactory,
                              LoggerChannelFactoryInterface $loggerFactory) {
    $this->config = $configFactory->get('azure_bing_search.settings');
    $this->apiOperations = $this->config->get('api_operations');
    $this->languageManager = $languageManager;
    $this->httpClientFactory = $httpClientFactory;
    $this->loggerFactory = $loggerFactory;
  }

  /**
   * Get Search Results from Bing.
   *
   * @param string $keywords
   *   Keyword to search for.
   * @param array $queryParams
   *   Additional query params.
   *
   * @return array|\Psr\Http\Message\StreamInterface
   *   Returns response body to be used by caller.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function searchResults($keywords, array $queryParams = []) {
    $defaultQueryParams = $this->defaultQueryParams();
    $allParams = array_merge($defaultQueryParams, $queryParams);

    $apiOptions = $this->apiOperations['bingcustomsearch'];
    $apiOptions['query'] = $allParams;
    $apiOptions['query']['q'] = $keywords;

    return $this->queryEndpoint($apiOptions);
  }

  /**
   * Call Bing API for data.
   *
   * @param array $options
   *   for Url building.
   *
   * @return array|\Psr\Http\Message\StreamInterface
   *   Returns response body to be used by caller.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function queryEndpoint(array $options = []) {
    $responseContents = [];

    // Make sure all the keys are set before attempting a search.
    if (isset($options['headers']['Ocp-Apim-Subscription-Key']) &&
      isset($options['query']['customConfig']) &&
      !empty($options['headers']['Ocp-Apim-Subscription-Key']) &&
      !empty($options['query']['customConfig'])) {
      try {
        $client = $this->httpClientFactory->fromOptions($options);
        $response = $client->request($options['http_method'], $options['uri']);
        $responseContents = Json::decode($response->getBody());
      }
      catch (\Exception $e) {
        // TODO: Better handling of exception with response status codes.
        $this->logError('Error querying the endpoint', $e);
      }
    }

    return $responseContents;
  }

  /**
   * Default Query Params.
   *
   * See: https://docs.microsoft.com/en-us/rest/api/cognitiveservices/bing-custom-search-api-v7-reference#query-parameters.
   *
   * @return array
   *   Default Query Params
   */
  protected function defaultQueryParams() {
    $query = $this->apiOperations['bingcustomsearch']['query'];

    $dynamicOptions = [
      // Unique identifier that identifies your custom search instance.
      'textDecorations' => $query['textDecorations'] ? 'true' : 'false',

      // The market where the results come from.
      // TODO Not yet implemented.
      // $language = $this->languageManager->getCurrentLanguage()->getId();
      // 'mkt' => $this->config->get('market_' . $language),.
    ];

    return array_merge($query, $dynamicOptions);
  }

  /**
   * Logs an error to the Drupal error log.
   *
   * @param string $message
   *   The error message.
   * @param \Exception $e
   *   The exception being handled.
   */
  public function logError($message, Exception $e) {
    $this->loggerFactory->get('azure_bing_search')->error(
      '@message - @exception', [
        '@message' => $message,
        // TODO Update the exception output for better readability.
        '@exception' => $e->getMessage(),
      ]
    );
  }

}
