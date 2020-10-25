<?php

namespace Drupal\cttdt_content_cleaner\Form;

use DateTime;
use DateTimeZone;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

class BatchDeleteForm extends FormBase {
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['before_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Process to delete nodes which have been created before: '),
      '#required' => TRUE,
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete nodes in batches'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get all data to be processed.
    $before_date = $form_state->getValue('before_date');
    $submittedDate = $before_date;
    $newdate = new DateTime($submittedDate);
    $unixts = $newdate->getTimestamp();
    $all_nids = \Drupal::entityQuery('node')
      ->condition('type', 'article')
      ->condition('created', $unixts, '<=')
      ->execute();

    // Breakdown your process into small batches(operations).
    // Delete 50 nodes per batch.
    $operations = [];
    foreach (array_chunk($all_nids, 50) as $smaller_batch_data) {
      $operations[] = ['\Drupal\cttdt_content_cleaner\Form\BatchDeleteForm::batchDelete'
        , [$smaller_batch_data]];
    }

    // Setup and define batch informations.
    $batch = array(
      'title' => t('Deleting nodes in batch...'),
      'operations' => $operations,
      'finished' => '\Drupal\cttdt_content_cleaner\Form\BatchDeleteForm::batchFinished',
    );
    batch_set($batch);
  }

  // Implement the operation method.
  public static function batchDelete($smaller_batch_data, &$context) {
    // Deleting nodes.
    $storage_handler = \Drupal::entityTypeManager()->getStorage('node');
    $entities = $storage_handler->loadMultiple($smaller_batch_data);
    $storage_handler->delete($entities);

    // Display data while running batch.
    $batch_size=sizeof($smaller_batch_data);
    $batch_number=sizeof($context['results'])+1;
    $context['message'] = sprintf("Deleting %s nodes per batch. Batch #%s"
      , $batch_size, $batch_number);
    $context['results'][] = sizeof($smaller_batch_data);
  }

  // What to do after batch ran. Display success or error message.
  public static function batchFinished($success, $results, $operations) {
    if ($success)
      $message = count($results). ' batches processed.';
    else
      $message = 'Finished with an error.';

    drupal_set_message($message);
  }

  public function getFormId() {
    return 'cttdt_content_cleaner_batch_delete_form';
  }
}
