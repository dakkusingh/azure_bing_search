<?php

namespace Drupal\azure_bing_search_views\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Simple filter to handle filtering Azure Bing results by query.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("azure_bing_search_views_query")
 */
class AzureBingSearchQuery extends FilterPluginBase {

  /**
   * This filter is always considered multiple-valued.
   *
   * @var bool
   */
  protected $alwaysMultiple = FALSE;

  /**
   * @var bool
   * Disable the possibility to use operators.
   */
  public $no_operator = FALSE;

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $form['value'] = [
      '#type' => 'textfield',
      '#size' => 15,
      '#default_value' => $this->value,
      '#attributes' => ['title' => $this->t('Search keywords')],
      '#title' => !$form_state->get('exposed') ? $this->t('Keywords') : '',
    ];
  }

}
