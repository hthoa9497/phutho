<?php

namespace Drupal\training_import\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


class TrainingConfigForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'training_config_form';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'training_import.settings'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['queue_runtime'] = [
      '#type'  => 'textfield',
      '#name'  => 'queue_runtime',
      '#title' => $this->t('Queue Runtime'),
      '#description' => $this->t('Runtime for Import Queue (in seconds)'),
      '#required'    => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('Save')
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fids = $form_state->getValue('import_file', '');
    $fid = reset($fids);

    try {
      $result = TrainingImportController::import($fid);
    }
    catch (\Exception $e) {
      $this->messenger()
        ->addError("Error when importing: " . $e->getMessage());
    }

    if ($result != NULL) {
      $this->messenger()
        ->addMessage("[Success] Imported: " . $result);
    } 
    else {
      $this->messenger()
      ->addError("Error when importing: " . $result);
    }
  }
}
