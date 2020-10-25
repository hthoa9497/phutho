<?php

namespace Drupal\hoidap_client\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Class HoiDapTraLoiForm.
 */
class HoiDapTraLoiForm extends FormBase {

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
    return 'hoidap_traloi_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $hoi_dap_id = \Drupal::routeMatch()->getParameter('id');
    $config = \Drupal::config('hoidap_client.hoidapconfig');
    $host =  !empty($config->get('cong_address')) ? $config->get('cong_address') : NULL;
    $response = \Drupal::httpClient()
      ->get('https://'.$host.'/hoi-dap/'.$hoi_dap_id);
    $json_string = (string) $response->getBody();
    $hoi_dap = json_decode($json_string);
    if (!empty($hoi_dap)) {
      $is_dv = \Drupal::service('hoidap_client.hoidap')->checkUserIsDV($hoi_dap->don_vi);
      if ($is_dv) {
        $form['ho_ten'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Họ tên'),
          '#required' => TRUE,
          '#attributes' => array('readonly' => 'readonly'),
          '#default_value' => $hoi_dap->ho_ten
        ];
        $form['dia_chi'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Địa chỉ'),
          '#required' => FALSE,
          '#attributes' => array('readonly' => 'readonly'),
          '#default_value' => $hoi_dap->dia_chi
        ];
        $form['email'] = [
          '#type' => 'email',
          '#title' => $this->t('Email'),
          '#required' => FALSE,
          '#attributes' => array('readonly' => 'readonly'),
          '#default_value' => $hoi_dap->email
        ];
        $form['dien_thoai'] = [
          '#type' => 'tel',
          '#title' => $this->t('Điện thoại'),
          '#required' => TRUE,
          '#attributes' => array('readonly' => 'readonly'),
          '#default_value' => $hoi_dap->dien_thoai
        ];
        $form['title'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Tiêu đề'),
          '#required' => TRUE,
          '#attributes' => array('readonly' => 'readonly'),
          '#default_value' => $hoi_dap->title
        ];
        $form['cau_hoi'] = array(
          '#type' => 'text_format',
          '#title' => t('Nội dung câu hỏi'),
          '#format' => 'full_html',
          '#required' => TRUE,
          '#default_value' => $hoi_dap->cau_hoi
        );
        $file_dk = '';
        if (!empty($hoi_dap->file_dk)) {
          $doc = system_retrieve_file($hoi_dap->file_dk, 'public://hoi-dap-files/', FALSE, FILE_EXISTS_REPLACE);
          if (!empty($doc)) {
            $file_dk = $this->drupal_add_existing_file($doc);
          }
        }

        $form['gui_file_dinh_kem'] = [
          '#type' => 'managed_file',
          '#title' => $this->t('Gửi file đính kèm'),
          '#default_value' => !empty($file_dk) ? array($file_dk->id()) : '',
          '#upload_validators' => [
            'file_validate_extensions' => ['doc docx xls xlsx ppt pptx rtf txt zip pdf rar tar jpg png gif'],
          ],
          '#attributes' => array('readonly' => 'readonly'),
        ];
        $time_tra_loi = date('Y-m-d', strtotime($hoi_dap->thoi_gian_tra_loi));
        $form['thoi_gian_tra_loi'] = array (
          '#type' => 'date',
          '#date_date_format' => 'd/m/Y',
          '#title' => t('Thời gian trả lời'),
          '#required' => TRUE,
          '#attributes' => array('readonly' => 'readonly'),
          '#default_value' => $time_tra_loi
        );
        $form['tra_loi'] = array(
          '#type' => 'text_format',
          '#title' => 'Trả lời',
          '#format' => 'full_html',
          '#default_value' => !empty($hoi_dap->tra_loi) ? $hoi_dap->tra_loi : '',
          '#required' => TRUE,
        );
        $file_dk_tl = '';
        if (!empty($hoi_dap->file_dk_tl)) {
          $doc_tl = system_retrieve_file($hoi_dap->file_dk_tl, 'public://hoi-dap-files/', FALSE, FILE_EXISTS_REPLACE);
          if (!empty($doc_tl)) {
            $file_dk_tl = $this->drupal_add_existing_file($doc_tl);
          }

        }
        $form['file_dinh_kem_tra_loi'] = [
          '#type' => 'managed_file',
          '#title' => $this->t('File đính kèm trả lời'),
          '#upload_location' => 'public://hoi-dap-files/',
          '#default_value' => !empty($file_dk_tl) ? array($file_dk_tl->id()) : '',
          '#upload_validators' => [
            'file_validate_extensions' => ['doc docx xls xlsx ppt pptx rtf txt zip pdf rar tar jpg png gif'],
          ],
        ];

        $form['actions'] = [
          '#type' => 'actions',
        ];

        // Add a submit button that handles the submission of the form.
        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Lưu'),
          '#weight' => 11,
        ];

        $tra_loi = '<a href="#" class="btn btn-primary btn-tra-loi">'. t('Trả lời') .'</a>';
        $form['actions']['tra_loi_submit'] = array(
          '#type' => 'markup',
          '#markup' => $tra_loi,
          '#weight' => 20,
        );

        return $form;
      }
      else {
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
      }
    }
    else {
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  private function drupal_add_existing_file($file_drupal_path, $uid = 1, $status = FILE_STATUS_PERMANENT) {
    // check first if the file exists for the uri
    $files = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->loadByProperties(['uri' => $file_drupal_path]);
    $file = reset($files);

    // if not create a file
    if (!$file) {
      $file = File::create([
        'uri' => $file_drupal_path,
      ]);
      $file->save();
    }

    return $file;
  }

}
