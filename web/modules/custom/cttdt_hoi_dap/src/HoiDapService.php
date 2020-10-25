<?php

namespace Drupal\cttdt_hoi_dap;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\Entity\User;

class HoiDapService implements HoiDapServiceInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;
  const PUBLISHED = 'published';
  const ANSWERED = 'tra_loi';
  const NOT_ANSWER = 'phan_cong';

  /**
   * {@inheritdoc}
   */
  public function __construct(
    $entityTypeManager,
    AccountProxyInterface $accountProxy
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $accountProxy;
  }

  private function getTotalQuestionByDv($termId, $date_from = NULL, $date_to = NULL) {
    $questionsStorage = $this->entityTypeManager->getStorage('node');
    $query = $questionsStorage->getQuery();
    $query->condition('type', 'hoi_dap');
    $query->condition('field_don_vi_xu_ly', $termId);

    if (!empty($date_from)) {
      $query->condition('field_thoi_gian_tra_loi', $date_from, '>=');
    }
    if (!empty($date_to)) {
      $query->condition('field_thoi_gian_tra_loi', $date_to, '<=');
    }

    $ids = $query->execute();

    if (!count($ids)) {
      return NULL;
    }

    return count($ids);
  }

  private function getTotalQuestionStatusByDv($termId, $date_from = NULL, $date_to = NULL) {
    $questionsStorage = $this->entityTypeManager->getStorage('node');
    $query = $questionsStorage->getQuery();
    $query->condition('type', 'hoi_dap');
    $query->condition('field_don_vi_xu_ly', $termId);

    if (!empty($date_from)) {
      $query->condition('field_thoi_gian_tra_loi', $date_from, '>=');
    }
    if (!empty($date_to)) {
      $query->condition('field_thoi_gian_tra_loi', $date_to, '<=');
    }

    $ids = $query->execute();

    if (!count($ids)) {
      return NULL;
    }

    $questions = $questionsStorage->loadMultiple($ids);
    $answered = 0;
    $not_answer = 0;
    $delay_answer = 0;

    foreach ($questions as $item) {
      $moderation_state = $item->get('moderation_state')->getValue();
      $moderation_state = $moderation_state[0]['value'];

      if ($moderation_state === $this::ANSWERED || $moderation_state === $this::PUBLISHED) {
        $answered++;
      }
      if ($moderation_state === $this::NOT_ANSWER) {
        $not_answer++;
      }
      $time_tra_loi = $item->field_thoi_gian_tra_loi->value;
      $time_published = $item->field_thoi_gian_xuat_ban->value;
      if (strtotime($time_published) > strtotime($time_tra_loi)) {
        $delay_answer++;
      }
    }

    $total_question_status = array(
      'answered' => $answered,
      'not_answer' => $not_answer,
      'delay_answer' => $delay_answer
    );

    return $total_question_status;
  }

  public function getListDV() {
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree('don_vi');
    $don_vi = [];

    foreach ($terms as $term) {
      $don_vi[] = array(
        'id' => $term->tid,
        'name' => $term->name
      );
    }

    return $don_vi;
  }

  public function getBaoCaoDV() {
    $don_vi = $this->getListDV();

    $result = array();
    if (!empty($don_vi)) {
      foreach ($don_vi as $item) {
        $result[$item['id']]['don_vi'] = $item['name'];
        $result[$item['id']]['total_questions'] = $this->getTotalQuestionByDv($item['id']) > 0 ? $this->getTotalQuestionByDv($item['id']) : 0;
        $total_questions_status = $this->getTotalQuestionStatusByDv($item['id']) > 0 ? $this->getTotalQuestionStatusByDv($item['id']) : 0;
        $result[$item['id']]['total_answered'] = $total_questions_status['answered'] > 0 ? $total_questions_status['answered'] : 0;
        $result[$item['id']]['not_answered'] = $total_questions_status['not_answer'] > 0 ? $total_questions_status['not_answer'] : 0;
        $result[$item['id']]['delay_answer'] = $total_questions_status['delay_answer'] > 0 ? $total_questions_status['delay_answer'] : 0;
      }
    }

    return $result;
  }

  public function getBaoCaoDVFilter($don_vi = NULL, $date_form  = NULL, $date_to  = NULL) {
    $result = array();
    if (!empty($don_vi)) {
      $term = Term::load($don_vi);
      $result[$term->id()]['don_vi'] = $term->getName();
      $result[$term->id()]['total_questions'] = $this->getTotalQuestionByDv($term->id(), $date_form, $date_to) > 0 ? $this->getTotalQuestionByDv($term->id(), $date_form, $date_to) : 0;
      $total_questions_status = $this->getTotalQuestionStatusByDv($term->id(), $date_form, $date_to) > 0 ? $this->getTotalQuestionStatusByDv($term->id(), $date_form, $date_to) : 0;
      $result[$term->id()]['total_answered'] = $total_questions_status['answered'] > 0 ? $total_questions_status['answered'] : 0;
      $result[$term->id()]['not_answered'] = $total_questions_status['not_answer'] > 0 ? $total_questions_status['not_answer'] : 0;
      $result[$term->id()]['delay_answer'] = $total_questions_status['delay_answer'] > 0 ? $total_questions_status['delay_answer'] : 0;
    }
    else {
      $list_don_vi = $this->getListDV();
      if (!empty($list_don_vi)) {
        foreach ($list_don_vi as $item) {
          $result[$item['id']]['don_vi'] = $item['name'];
          $result[$item['id']]['total_questions'] = $this->getTotalQuestionByDv($item['id'], $date_form, $date_to) > 0 ? $this->getTotalQuestionByDv($item['id'], $date_form, $date_to) : 0;
          $total_questions_status = $this->getTotalQuestionStatusByDv($item['id'], $date_form, $date_to) > 0 ? $this->getTotalQuestionStatusByDv($item['id'], $date_form, $date_to) : 0;
          $result[$item['id']]['total_answered'] = $total_questions_status['answered'] > 0 ? $total_questions_status['answered'] : 0;
          $result[$item['id']]['not_answered'] = $total_questions_status['not_answer'] > 0 ? $total_questions_status['not_answer'] : 0;
          $result[$item['id']]['delay_answer'] = $total_questions_status['delay_answer'] > 0 ? $total_questions_status['delay_answer'] : 0;
        }
      }
    }

    return $result;
  }

  public function getDsChamTraLoi($don_vi = NULL, $date_form = NULL, $date_to = NULL) {
    $questionsStorage = $this->entityTypeManager->getStorage('node');
    $query = $questionsStorage->getQuery();
    $query->condition('type', 'hoi_dap');
    if (!empty($don_vi)) {
      $query->condition('field_don_vi_xu_ly', $don_vi);
    }

    if (!empty($date_form)) {
      $query->condition('field_thoi_gian_tra_loi', $date_form, '>=');
    }
    if (!empty($date_to)) {
      $query->condition('field_thoi_gian_tra_loi', $date_to, '<=');
    }

    $ids = $query->execute();

    if (!count($ids)) {
      return NULL;
    }

    $questions = $questionsStorage->loadMultiple($ids);
    $ds_delay_answers = [];
    foreach ($questions as $key => $item) {
      $time_tra_loi = $item->field_thoi_gian_tra_loi->value;
      $time_tra_loi_format = strtotime($time_tra_loi);
      $time_published = $item->field_thoi_gian_xuat_ban->value;
      $time_published_format = strtotime($time_published);

      if ($time_published_format > $time_tra_loi_format) {
        $total_delay = $time_published_format - $time_tra_loi_format;
        $ds_delay_answers[$key]['cau_hoi'] = $item->label();
        $term_id = !empty($item->field_don_vi_xu_ly) ? $item->field_don_vi_xu_ly->target_id : NULL;
        $don_vi_load = !empty($term_id) ? Term::load($term_id) : NULL;
        $ds_delay_answers[$key]['don_vi'] =$don_vi_load->getName();
        $ds_delay_answers[$key]['time_tra_loi'] = date('d/m/Y', strtotime($time_tra_loi));
        $ds_delay_answers[$key]['time_published'] = date('d/m/Y', strtotime($time_published));
        $ds_delay_answers[$key]['total_delay'] = date('j', $total_delay) - 1;
      }
    }

    return $ds_delay_answers;
  }
}
