<?php

namespace Drupal\cttdt_hoi_dap\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class BaoCaoDVFilterForm extends FormBase {
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
    return 'bao_cao_dv_filter';
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
    $don_vi = \Drupal::service('cttdt_hoi_dap.hoidap')->getListDV();
    $options = [];

    if (!empty($don_vi)) {
      $options[0] = $this->t('-Tất cả-');
      foreach ($don_vi as $item) {
        $options[$item['id']] = $item['name'];
      }
    }
    $form['don_vi'] = array(
      '#type' => 'select',
      '#title' => t('Đơn vị'),
      '#options' => $options,
    );

    $form['so_ngay_cham'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Số ngày chậm')
    ];

    $form['date_from'] = array(
      '#type' => 'date',
      '#title' => t('Từ ngày'),
      '#date_date_format' => 'm/d/Y'
    );

    $form['date_to'] = array(
      '#type' => 'date',
      '#title' => t('Đến ngày'),
      '#date_date_format' => 'm/d/Y'
    );

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#attributes' => [],
      '#value' => $this->t('Tìm kiếm'),
      '#weight' => 11,
    ];

    $form['actions']['submit']['#ajax'] = [
      'callback' => '::submitSearchForm',
      'event' => 'click',
      'progress' => [
        'type' => 'throbber',
        'message' => '',
      ],
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
  }

  public function submitSearchForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $don_vi = !empty($form_state->getValue('don_vi')) ? $form_state->getValue('don_vi') : NULL;
    $date_from = !empty($form_state->getValue('date_from')) ? $form_state->getValue('date_from') : NULL;
    $date_to = !empty($form_state->getValue('date_to')) ? $form_state->getValue('date_to') : NULL;
    $ngay_cham = !empty($form_state->getValue('so_ngay_cham')) ? $form_state->getValue('so_ngay_cham') : NULL;

    $results = \Drupal::service('cttdt_hoi_dap.hoidap')->getBaoCaoDVFilter($don_vi, $date_from, $date_to);
    $response->addCommand(new RemoveCommand('.post-item', 'remove'));
    $output = '';
    $stt = 0;

    $total_questions = 0;
    $total_answered = 0;
    $not_answered = 0;
    $delay_answer = 0;
    foreach ($results as $item) {
      if (!empty($ngay_cham)) {
        if ($ngay_cham == $item['delay_answer']) {
          $stt++;
          $total_questions = $item['total_questions'] > 0 ? $item['total_questions'] + $total_questions : $total_questions;
          $total_answered = $item['total_answered'] > 0 ? $item['total_answered'] + $total_answered : $total_answered;
          $not_answered = $item['not_answered'] > 0 ? $item['not_answered'] + $not_answered : $not_answered;
          $delay_answer = $item['delay_answer'] > 0 ? $item['delay_answer'] + $delay_answer : $delay_answer;
          $output .= $this->renderItem($stt, $item['don_vi'], $item['total_questions'], $item['total_answered'], $item['not_answered'], $item['delay_answer']);
        }
      }
      else {
        $stt++;
        $total_questions = $item['total_questions'] > 0 ? $item['total_questions'] + $total_questions : $total_questions;
        $total_answered = $item['total_answered'] > 0 ? $item['total_answered'] + $total_answered : $total_answered;
        $not_answered = $item['not_answered'] > 0 ? $item['not_answered'] + $not_answered : $not_answered;
        $delay_answer = $item['delay_answer'] > 0 ? $item['delay_answer'] + $delay_answer : $delay_answer;
        $output .= $this->renderItem($stt, $item['don_vi'], $item['total_questions'], $item['total_answered'], $item['not_answered'], $item['delay_answer']);
      }
    }

    $output .= '<tr class="post-item">
            <td colspan="2" class="text-align-center"><b>'.$this->t('Tổng:').'</b></td>
            <td class="text-align-center">'.$total_questions.'</td>
            <td class="text-align-center">'.$total_answered.'</td>
            <td class="text-align-center">'.$not_answered.'</td>
            <td class="text-align-center">'.$delay_answer.'</td>
          </tr>';

    $response->addCommand(new AppendCommand('tbody', $output));

    return $response;
  }

  private function renderItem($stt, $dv, $total_question, $answered, $not_answered, $deplay) {
    return '<tr class="post-item">
              <th scope="row" class="text-align-center">'.$stt.'</th>
              <td>'.$dv.'</td>
              <td class="text-align-center">'.$total_question.'</td>
              <td class="text-align-center">'.$answered.'</td>
              <td class="text-align-center">'.$not_answered.'</td>
              <td class="text-align-center">'.$deplay.'</td>
            </tr>';
  }
}
