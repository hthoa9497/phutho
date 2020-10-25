<?php

namespace Drupal\cttdt_rates\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the cttdt rates entity edit forms.
 */
class CttdtRatesForm extends ContentEntityForm {

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
      $this->messenger()->addStatus($this->t('New cttdt rates %label has been created.', $message_arguments));
      $this->logger('cttdt_rates')->notice('Created new cttdt rates %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The cttdt rates %label has been updated.', $message_arguments));
      $this->logger('cttdt_rates')->notice('Updated new cttdt rates %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.cttdt_rates.canonical', ['cttdt_rates' => $entity->id()]);
  }

}
