<?php

namespace Drupal\cttdt_dashboard\Form;

use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class TKTacPhamByMonth extends FormBase {
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
    return 'tk_tac_pham_by_month';
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
    $tk_ct = \Drupal::service('cttdt_dashboard.dashboard')->getTPGroupUser($month);

    if (empty($tk_ct)) {
      $response->addCommand(new RemoveCommand('.post-item', 'remove'));
      $response->addCommand(new InvokeCommand('.message-empty', 'removeClass', ['d-none']));
    }
    else {
      $response->addCommand(new InvokeCommand('.message-empty', 'addClass', ['d-none']));
      $stt = 0;
      $result = '';

      foreach ($tk_ct as $item) {
        $stt ++ ;
        $item_total = count($item) - 1;
        if ($item_total > 1) {
          $i = 0;
          foreach ($item as $post) {
            $i++;
            if ($i == 1) {
              $result .= '<tr class="post-item">
                          <th scope="row" rowspan="'.$item_total.'" class="align-middle text-align-center">'.$stt.'</th>
                          <td rowspan="'.$item_total.'" class="align-middle text-align-center">'.$post['post_tac_gia'].'</td>
                          <td>'.$post['post_title'].'</td>
                          <td>'.$post['post_changed'].'</td>
                          <td>'.$post['post_the_loai'].'</td>
                          <td>'.$post['post_hs_nb'].'</td>
                          <td rowspan="{{ item_total }}" class="align-middle text-align-center">'.$item['total_hs'].'</td>
                        </tr>';
            }
            else {
              if ($i < count($item)) {
                $result .= '<tr class="post-item">
                              <td>'.$post['post_title'].'</td>
                              <td>'.$post['post_changed'].'</td>
                              <td>'.$post['post_the_loai'].'</td>
                              <td>'.$post['post_hs_nb'].'</td>
                            </tr>';
              }
            }
          }
        }
        else {
          $result .= '<tr class="post-item">';
          $i = 0;
          foreach ($item as $post) {
            $i++;
            if ($i < count($item)) {
              $result .= '<th scope="row" class="align-middle text-align-center">'.$stt.'</th>
                          <td class="align-middle text-align-center">'.$post['post_tac_gia'].'</td>
                          <td>'.$post['post_title'].'</td>
                          <td>'.$post['post_changed'].'</td>
                          <td>'.$post['post_the_loai'].'</td>
                          <td>'.$post['post_hs_nb'].'</td>
                          <td class="align-middle text-align-center">'.$post['post_hs_nb'].'</td>';
            }
          }
          $result .= '</tr>';
        }
      }

      $response->addCommand(new AfterCommand('.message-empty', $result));
    }

    return $response;
  }
}
