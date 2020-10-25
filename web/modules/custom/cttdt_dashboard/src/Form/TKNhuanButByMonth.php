<?php

namespace Drupal\cttdt_dashboard\Form;

use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class TKNhuanButByMonth extends FormBase {
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
    return 'tk_nhuan_but_by_month';
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
    $form['thang'] = array(
      '#type' => 'select',
      '#title' => t('Tháng:'),
      '#options' => $month,
      '#default_value' => $month[date('n')],
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
    $tk_nb = \Drupal::service('cttdt_dashboard.dashboard')->getNBGroupUser($month);

    if (empty($tk_nb)) {
      $response->addCommand(new RemoveCommand('.post-item', 'remove'));
      $response->addCommand(new InvokeCommand('.message-empty', 'removeClass', ['d-none']));
    }
    else {
      $response->addCommand(new InvokeCommand('.message-empty', 'addClass', ['d-none']));
      $stt = 0;
      $result = '';

      foreach ($tk_nb as $item) {
        $stt++;
        $result .= '<tr class="post-item">
                      <th scope="row" class="text-align-center">'.$stt.'</th>
                      <td class="text-align-center">'.$item['post_ho_ten'].'</td>
                      <td class="text-align-center">'.$item['tien_nhuan_but'].'</td>
                    </tr>';
      }
      $response->addCommand(new AfterCommand('.message-empty', $result));
    }
    return $response;
  }
}
