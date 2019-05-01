<?php

namespace Drupal\azure_bing_search\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Admin form for Bing Search API settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'azure_bing_search_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'azure_bing_search.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormTitle() {
    return 'Bing Custom Search API Settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('azure_bing_search.settings');
    $apiOperations = $config->get('api_operations');
    $headers = $apiOperations['bingcustomsearch']['headers'];
    $query = $apiOperations['bingcustomsearch']['query'];

    $form['subscription_key'] = [
      '#title' => $this->t('Subscription Key'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $headers['Ocp-Apim-Subscription-Key'],
    ];

    $form['custom_config_id'] = [
      '#title' => $this->t('Custom Configuration ID'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $query['customConfig'],
    ];

    $form['count'] = [
      '#title' => $this->t('Page size'),
      '#type' => 'textfield',
      '#description' => $this->t('Number of results to display per page.'),
      '#default_value' => $query['count'],
      '#size' => 5,
      '#max_length' => 5,
    ];

    $form['safesearch'] = [
      '#title' => $this->t('Safe Search'),
      '#type' => 'select',
      '#options' => [
        'Off' => $this->t('Off'),
        'Moderate' => $this->t('Moderate'),
        'Strict' => $this->t('Strict'),
      ],
      '#description' => $this->t('A filter used to filter webpages for adult content.'),
      '#default_value' => $query['safeSearch'],
    ];

    $form['text_decorations'] = [
      '#title' => $this->t('Use text decorations'),
      '#type' => 'checkbox',
      '#description' => $this->t('Whether display strings should contain decoration markers such as hit highlighting characters.'),
      '#default_value' => $query['textDecorations'],
    ];

    $form['text_format'] = [
      '#title' => $this->t('Text format'),
      '#type' => 'select',
      '#options' => [
        'Raw' => $this->t('Raw'),
        'HTML' => $this->t('HTML'),
      ],
      '#description' => $this->t('Use Unicode characters or HTML tags to mark content that needs special formatting.'),
      '#default_value' => $query['textFormat'],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory()->getEditable('azure_bing_search.settings')
      ->set('api_operations.bingcustomsearch.query.customConfig', $form_state->getValue('custom_config_id'))
      ->set('api_operations.bingcustomsearch.query.count', $form_state->getValue('count'))
      ->set('api_operations.bingcustomsearch.query.safeSearch', $form_state->getValue('safesearch'))
      ->set('api_operations.bingcustomsearch.query.textDecorations', $form_state->getValue('text_decorations'))
      ->set('api_operations.bingcustomsearch.query.textFormat', $form_state->getValue('text_format'))
      ->set('api_operations.bingcustomsearch.headers.Ocp-Apim-Subscription-Key', $form_state->getValue('subscription_key'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
