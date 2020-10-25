<?php

namespace Drupal\question_survey\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class QuestionSurveyController extends ControllerBase {

  /**
   * return question survey result
   */
  public function getSurveyResult() {

    $result = \Drupal::service('question_survey.survey')->getResultSurvey();
    return new JsonResponse($result);
  }
}
