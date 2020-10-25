<?php

namespace Drupal\cttdt_dashboard\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class StatisticNodeCountFilter extends FormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'statistic_node_count_filter_form';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $page_title = \Drupal::request()->query->get('title');
    $post_type = \Drupal::request()->query->get('post_category');

    $cateogry_terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('tin_tuc_su_kien', 0, 1, TRUE);
    $term_data[] = $this->t('All');
    foreach ($cateogry_terms as $key => $term) {
      $term_data[$term->id()] = $term->getName();
    }

    $form['filters'] = [
      '#type'  => 'fieldset',
      '#title' => $this->t('Filter'),
      '#open'  => true,
    ];

    $form['filters']['title'] = [
      '#title' => $this->t('Tiêu đề'),
      '#type' => 'textfield',
      '#default_value' => (isset($page_title) && !empty($page_title)) ? $page_title : ''
    ];

    $form['filters']['post_category'] = [
      '#title' => $this->t('Chuyên mục'),
      '#type' => 'select',
      '#options' => $term_data,
      '#default_value' => (isset($post_type) && !empty($post_type)) ? $post_type : '',
    ];

    $form['filters']['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('Tìm kiếm')

    ];

    return $form;
  }

  /**
   * Validate the title and the checkbox of the form
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $field = $form_state->getValues();
    $params = [];
    if (isset($field["title"]) && !empty($field["title"])) {
      $params['title'] = $field["title"];
    }

    if (isset($field["post_category"]) && !empty($field["post_category"])) {
      $params['post_category'] = $field["post_category"];
    }

    $url = \Drupal\Core\Url::fromRoute('cttdt_dashboard.admin_statistics_view_count')
      ->setRouteParameters($params);
    $form_state->setRedirectUrl($url);
  }

}
