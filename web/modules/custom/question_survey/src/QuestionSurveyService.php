<?php

namespace Drupal\question_survey;

class QuestionSurveyService implements QuestionSurveyServiceInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs a new QuestionSurveyService object.
   */
  public function __construct($entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  public function getResultSurvey() {
    $surveysStorage = $this->entityTypeManager->getStorage('question_survey');
    $query = $surveysStorage->getQuery();
    $ids = $query->execute();

    if (!count($ids)) {
      return NULL;
    }
    $surveys = $surveysStorage->loadMultiple($ids);

    $day_du = 0;
    $kha_hc = 0;
    $can_bs = 0;
    foreach ($surveys as $survey) {
      if ($survey->field_list_survey->value == 1) {
        $day_du++;
      } elseif ($survey->field_list_survey->value == 2) {
        $kha_hc++;
      } elseif ($survey->field_list_survey->value == 3) {
        $can_bs++;
      }
    }
    $result = array(
      'day_du' => $day_du,
      'kha_hc' => $kha_hc,
      'can_bs' => $can_bs
    );

    return $result;
  }
}
