<?php

/**
 * @file
 * Contains \Drupal\question_survey\Form\QuestionSurveyBlockForm.
 */
namespace Drupal\question_survey\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\question_survey\Entity\QuestionSurvey;

class QuestionSurveyBlockForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'question_survey_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $options = array(
      '1' => t('Thông tin thủ tục hành chính'),
      '2' => t('Thông tin kinh tế - xã hội'),
      '3' => t('Thông tin chỉ đạo điều hành')
    );

    $form['list_question_survey'] = array(
      '#type' => 'radios',
      '#title' => t('Thông tin nào trên Cổng Thông tin điện tử tỉnh Phú Thọ mà bạn quan tâm nhất?'),
      '#options' => $options,
      '#required' => TRUE,
    );

    $form['survey_message'] = array(
      '#type' => 'markup',
      '#markup' => '<span class="survey-message-success c-red d-none mb-2 text-align-left">'. t('Cảm ơn bạn đã bình chọn') .'</span>',
    );

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#attributes' => [],
      '#value' => $this->t('Ý kiến'),
      '#weight' => 11,
    ];

    $form['actions']['submit']['#ajax'] = [
      'callback' => '::submitSurveyForm',
      'event' => 'click',
      'progress' => array()
    ];

    $modal_ket_qua = '<a href="#" class="ket-qua-survey" data-toggle="modal" data-target="#ket-qua-survey-modal">'. t('Kết quả') .'</a>';
    $form['ket_qua'] = array(
      '#type' => 'markup',
      '#markup' => $modal_ket_qua,
    );
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

  public function submitSurveyForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $survey_value = trim($form_state->getValue('list_question_survey'));
    if ($survey_value > 0) {
      $current_date = date('d/m/Y H:i');

      $question_survey = QuestionSurvey::create([
        'title' => 'Câu hỏi khảo sát ngày '.$current_date,
        'field_list_survey' => $survey_value,
        'uid' => \Drupal::currentUser()->id()
      ]);

      if ($question_survey->save()) {
        $response->addCommand(new InvokeCommand('.survey-message-success', 'removeClass', array('d-none')));
        $response->addCommand(new InvokeCommand('.survey-message-success', 'addClass', array('d-block')));
      }
      else {
        $response->addCommand(new HtmlCommand('.survey-message-success', "Lỗi!. Không thể lưu ý kiến"));
      }
    }
    else {
      $response->addCommand(new AlertCommand('Phải chọn ít nhất 1 phương án !'));
    }

    return $response;
  }
}
