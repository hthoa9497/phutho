<?php

namespace Drupal\question_survey\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the question survey entity edit forms.
 */
class QuestionSurveyForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New question survey %label has been created.', $message_arguments));
      $this->logger('question_survey')->notice('Created new question survey %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The question survey %label has been updated.', $message_arguments));
      $this->logger('question_survey')->notice('Updated new question survey %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.question_survey.canonical', ['question_survey' => $entity->id()]);
  }

}
