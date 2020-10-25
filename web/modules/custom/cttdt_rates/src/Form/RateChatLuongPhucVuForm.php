<?php

namespace Drupal\cttdt_rates\Form;

use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RateChatLuongPhucVuForm extends FormBase {
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
    return 'tk_rate_chat_luong_pv';
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

    $month = [];
    for ($i = 1; $i <= 12; $i++) {
      $month[$i] = $i;
    }

    $year = [];
    for ($i = 0; $i < 4; $i++) {
      $year[date('Y') - $i] = date('Y') - $i;
    }
    $form['thang'] = array(
      '#type' => 'select',
      '#title' => t('Tháng:'),
      '#options' => $month,
      '#default_value' => $month[date('n')],
      '#required' => TRUE,
    );

    $form['year'] = array(
      '#type' => 'select',
      '#title' => t('Năm:'),
      '#options' => $year,
      '#default_value' => $year[date('Y')],
      '#required' => TRUE,
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
        'message' => $this->t('Đang tìm kiếm'),
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
    $month = trim($form_state->getValue('thang'));
    $month = $month < 10 ? '0'.$month : $month;
    $year = trim($form_state->getValue('year'));

    $month_year = $month .'-'. $year;
    $result = \Drupal::service('cttdt_rates.rate')->tkRateChatLuong($month_year);
    $response->addCommand(new RemoveCommand('.post-item', 'remove'));
    if (!empty($result)) {
      $response->addCommand(new InvokeCommand('.message-empty', 'addClass', ['d-none']));
      $output = '';
      $stt = 0;

      foreach ($result as $item) {
        $stt ++ ;
        $temp = 0;
        foreach ($item['rate_data'] as $key => $val) {
          $temp ++ ;
          if (count($item['rate_data']) == 1) {
            $output .= '<tr class="post-item">
                  <td class="align-middle text-align-center">'.$stt.'</td>
                  <td class="align-middle">'.$item['don_vi'].'</td>
                  <td>'. $this->getMucDoLabel($key) .'</td>
                  <td class="text-align-center">'.$val.'</td>
                </tr>';
          }
          else {
            if ($temp == 1) {
              $output .= '<tr class="post-item">
                  <td rowspan="'.count($item['rate_data']).'" class="align-middle text-align-center">'.$stt.'</td>
                  <td rowspan="'.count($item['rate_data']).'" class="align-middle">'.$item['don_vi'].'</td>
                  <td>'. $this->getMucDoLabel($key) .'</td>
                  <td class="text-align-center">'.$val.'</td>
                </tr>';
            }
            else {
              $output .= '<tr class="post-item">
                  <td>'.$this->getMucDoLabel($key).'</td>
                  <td class="text-align-center">'.$val.'</td>
                </tr>';
            }
          }
        }
      }
      $response->addCommand(new AfterCommand('.message-empty', $output));
    }
    else {
      $response->addCommand(new InvokeCommand('.message-empty', 'removeClass', ['d-none']));
    }

    return $response;
  }

  private function getMucDoLabel($key) {
    $result = '';
    switch ($key) {
      case 0:
        $result = $this->t('Rất hài lòng');
        break;
      case 1:
        $result = $this->t('Hài lòng');
        break;
      case 2:
        $result = $this->t('Chấp nhận được');
        break;
      case 3:
        $result = $this->t('Không hài lòng');
        break;
      case 4:
        $result = $this->t('Không thể chấp nhận được');
        break;
      default:
        break;
    }

    return $result;
  }
}
