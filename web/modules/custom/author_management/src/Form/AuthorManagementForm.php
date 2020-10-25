<?php

namespace Drupal\author_management\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the author management entity edit forms.
 */
class AuthorManagementForm extends ContentEntityForm {

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
      $this->messenger()->addStatus($this->t('New author management %label has been created.', $message_arguments));
      $this->logger('author_management')->notice('Created new author management %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The author management %label has been updated.', $message_arguments));
      $this->logger('author_management')->notice('Updated new author management %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.author_management.canonical', ['author_management' => $entity->id()]);
  }

}
