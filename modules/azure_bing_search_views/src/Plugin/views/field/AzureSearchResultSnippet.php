<?php

namespace Drupal\azure_bing_search_views\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Defines a field for rendering search result snippet.
 *
 * @ViewsField("azure_search_result_snippet")
 */
class AzureSearchResultSnippet extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['snippet_size'] = ['default' => isset($this->definition['snippet_size default']) ? $this->definition['snippet_size default'] : 0];
    return $options;
  }

  /**
   * Provide options to search results snippet field.
   *
   * {@inheritDoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['snippet_size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Number of characters to display in result snippet'),
      '#default_value' => $this->options['snippet_size'],
      '#description' => $this->t('Defaults to zero which means no limit'),
    ];

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $value = $this->getValue($values);
    if (!empty($this->options['snippet_size']) && strlen($value) > $this->options['snippet_size']) {
      $value = substr($value, 0, $this->options['snippet_size']) . '...';
    }
    return [
      '#theme' => 'azure_bing_search_views_view_snippet',
      '#snippet' => $this->sanitizeValue($value),
    ];
  }

}
