<?php

namespace Drupal\question_survey\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Question Survey' Block.
 *
 * @Block(
 *   id = "question_survey",
 *   admin_label = @Translation("Question Survey block"),
 *   category = @Translation("Question Survey"),
 * )
 */
class QuestionSurveyBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\question_survey\Form\QuestionSurveyBlockForm');

    return $form;
  }

}
