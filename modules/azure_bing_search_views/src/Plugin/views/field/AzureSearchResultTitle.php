<?php

namespace Drupal\azure_bing_search_views\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Defines a field for rendering title with link to web page.
 *
 * @ViewsField("azure_search_result_title")
 */
class AzureSearchResultTitle extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['link_to_page'] = ['default' => isset($this->definition['link_to_page default']) ? $this->definition['link_to_page default'] : FALSE];
    return $options;
  }

  /**
   * Provide link to bing search results option.
   *
   * {@inheritDoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['link_to_page'] = [
      '#title' => $this->t('Link this field to the web page'),
      '#description' => $this->t("Enable to override this field's links."),
      '#type' => 'checkbox',
      '#default_value' => !empty($this->options['link_to_page']),
    ];

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * Prepares link to the web page.
   *
   * @param string $data
   *   The XSS safe string for the link text.
   * @param \Drupal\views\ResultRow $values
   *   The values retrieved from a single row of a view's query result.
   *
   * @return string
   *   Returns a string for the link text.
   */
  protected function renderLink($data, ResultRow $values) {
    if (!empty($this->options['link_to_page'])) {
      if ($data !== NULL && $data !== '' && $values->url) {
        $this->options['alter']['make_link'] = TRUE;
        $options = ['attributes' => ['class' => 'bing-result-link']];
        $this->options['alter']['url'] = Url::fromUri($values->url, $options);
      }
      else {
        $this->options['alter']['make_link'] = FALSE;
      }
    }
    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $value = $this->getValue($values);
    return [
      '#theme' => 'azure_bing_search_views_view_title',
      '#title' => $this->renderLink($this->sanitizeValue($value), $values),
    ];
  }

}
