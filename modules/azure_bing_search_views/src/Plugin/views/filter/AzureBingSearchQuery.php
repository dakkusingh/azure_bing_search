<?php

namespace Drupal\azure_bing_search_views\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
//use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\FilterPluginBase;
//use Drupal\views\ViewExecutable;

/**
 * Simple filter to handle filtering Azure Bing results by query.
 *
 * @ViewsFilter("azure_bing_search_views_query")
 */
class AzureBingSearchQuery extends FilterPluginBase {

  /**
   * This filter is always considered multiple-valued.
   *
   * @var bool
   */
//  protected $alwaysMultiple = FALSE;

  /**
   * @var bool
   * Disable the possibility to use operators.
   */
  public $no_operator = FALSE;

  /**
   * A search query to use for parsing search keywords.
   *
   * @var \Drupal\search\ViewsSearchQuery
   */
//  protected $searchQuery = NULL;

  /**
   * TRUE if the search query has been parsed.
   *
   * @var bool
   */
//  protected $parsed = FALSE;

  /**
   * The search type name (value of {search_index}.type in the database).
   *
   * @var string
   */
//  protected $searchType;

  /**
   * {@inheritdoc}
   */
//  public function init(ViewExecutable $view,
//                       DisplayPluginBase $display,
//                       array &$options = NULL) {
//    parent::init($view, $display, $options);
//
//    $this->searchType = $this->definition['search_type'];
//  }

  /**
   * {@inheritdoc}
   */
  public function adminSummary() {
    $summary = parent::adminSummary();
    if (!empty($this->options['exposed'])) {
      $summary = $this->t('exposed');
    }
    return $summary;
  }

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

  /**
   * {@inheritdoc}
   */
//  public function validateExposed(&$form, FormStateInterface $form_state) {
//    if (!isset($this->options['expose']['identifier'])) {
//      return;
//    }
//
//    $key = $this->options['expose']['identifier'];
//    if (!$form_state->isValueEmpty($key)) {
//      $this->queryParseSearchExpression($form_state->getValue($key));
//      if (count($this->searchQuery->words()) == 0) {
//        $form_state->setErrorByName($key, $this->formatPlural(\Drupal::config('search.settings')->get('index.minimum_word_size'), 'You must include at least one keyword to match in the content, and punctuation is ignored.', 'You must include at least one keyword to match in the content. Keywords must be at least @count characters, and punctuation is ignored.'));
//      }
//    }
//  }

  /**
   * Sets up and parses the search query.
   *
   * @param string $input
   *   The search keywords entered by the user.
   */
//  protected function queryParseSearchExpression($input) {
//    if (!isset($this->searchQuery)) {
//      $this->parsed = TRUE;
//      $this->searchQuery = db_select('search_index', 'i', ['target' => 'replica'])->extend('Drupal\search\ViewsSearchQuery');
//      $this->searchQuery->searchExpression($input, $this->searchType);
//      $this->searchQuery->publicParseSearchExpression();
//    }
//  }

  /**
   * {@inheritdoc}
   */
//  public function query() {
    // Since attachment views don't validate the exposed input, parse the search
    // expression if required.
//    if (!$this->parsed) {
//      $this->queryParseSearchExpression($this->value);
//    }
//
//    if (isset($this->searchQuery)) {
//      $words = $this->searchQuery->words();
//      if (empty($words)) {
//        ksm($words);
//      }
//    }
//
    // Set to NULL to prevent PDO exception when views object is cached.
//    $this->searchQuery = NULL;
//  }

}
