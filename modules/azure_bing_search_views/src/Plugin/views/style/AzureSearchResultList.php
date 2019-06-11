<?php

namespace Drupal\azure_bing_search_views\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Style plugin to render search result items in an ordered or unordered list.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "azure_search_result_list",
 *   title = @Translation("Bing Search Result"),
 *   help = @Translation("Displays search result in HTML List"),
 *   theme = "azure_bing_search_views_view_list",
 *   display_types = {"normal"}
 * )
 */
class AzureSearchResultList extends StylePluginBase {

  /**
   * {@inheritdoc}
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Does the style plugin support custom css class for the rows.
   *
   * @var bool
   */
  protected $usesRowClass = TRUE;

  /**
   * Set default options.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['type'] = ['default' => 'ul'];
    $options['class'] = ['default' => ''];
    $options['wrapper_class'] = ['default' => 'item-list'];

    return $options;
  }

  /**
   * Render the given style.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['type'] = [
      '#type' => 'radios',
      '#title' => $this->t('List type'),
      '#options' => ['ul' => $this->t('Unordered list'), 'ol' => $this->t('Ordered list')],
      '#default_value' => $this->options['type'],
    ];
    $form['wrapper_class'] = [
      '#title' => $this->t('Wrapper class'),
      '#description' => $this->t('The class to provide on the wrapper, outside the list.'),
      '#type' => 'textfield',
      '#size' => '30',
      '#default_value' => $this->options['wrapper_class'],
    ];
    $form['class'] = [
      '#title' => $this->t('List class'),
      '#description' => $this->t('The class to provide on the list element itself.'),
      '#type' => 'textfield',
      '#size' => '30',
      '#default_value' => $this->options['class'],
    ];
  }

}
