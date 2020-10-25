<?php

namespace Drupal\training_import\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

class ImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'training_import_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['text'] = [
      '#type' => 'textfield',
      '#title' => 'textt',
      '#value' => $form_state->getValue('text', '')
    ];
    $form['import'] = [
      '#type' => 'fieldset',
      '#tree' => FALSE
    ];

    $form['import']['import_file'] = [
      '#type'  => 'managed_file',
      '#name'  => 'import_file',
      '#title' => $this->t('File to import'),
      '#description' => $this->t('Choose a file to import (only *.xlsx allowed)'),
      '#required' => TRUE,
      '#upload_validators' => [
        'file_validate_extensions' => ['xlsx'],
      ],
      '#upload_location' => 'public://training-import-files/',
      '#weight' => 0,


      '#value' => $form_state->getValue('import_file', [])
    ];

    $form['import']['btn_preview'] = [
      '#type'   => 'submit',
      '#value'  => $this->t('Preview'),
      '#submit' => NULL,//['_training_core_preview_submit'],
      // '#prefix' => '<div id="btn-preview"></div>',
      '#button_type' => 'primary',
      '#ajax' => [
        'event'    => 'click',
        'callback' => '::previewAjaxCallback'
      ],
      '#states' => [
        'invisible' => [
          'input[name="import_file[fids]"]' => ['empty' => TRUE],
        ],
      ],
    ];

    $form['actions']['submit_lazy'] = [
      '#type'   => 'submit',
      '#value'  => $this->t('Lazy Import'),
      '#submit' => ['::lazyImport'],
    ];

    $form['actions']['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('Import'),
      '#button_type' => 'primary',
    ];

    $form['import_desc'] = [
      '#type' => 'item',
      'markup' => [
        '#markup' => $this->t('Lazy Import will put the data to queue, while normal import will create content right away.')
      ],
    ];

    return $form;
  }

  public function previewAjaxCallback(array &$form, FormStateInterface $form_state) {
    $fids = $form_state->getValue('import_file', '');
    $fid  = reset($fids);

    $spreadsheet = \Drupal::service('training_import.importer')
      ->getSheetFromFileID($fid);
  
    $sheet_value = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    $rows = array_slice($sheet_value, 1, 3);
  
    $elem_table = [
      'results' => [
        '#type'    => 'table',
        // '#header'  => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'],
        '#header'  => $sheet_value[1],
        '#rows'    => $rows,
        '#empty'   => $this->t('No data found.'),
      ],
      '#attached' => [
        'library' => ['core/drupal.dialog.ajax']
      ]
    ];

    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand(
      'Spreadsheet Preview',
      $elem_table,
      [
        'width' => '1000',
        'resizable' => TRUE,
        'draggable' => TRUE
      ])
    );
  
    return $response;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fids = $form_state->getValue('import_file', '');
    $fid = reset($fids);

    try {
      \Drupal::service('training_import.importer')
        ->doBatchImport($fid);
    }
    catch (\Exception $e) {
      $this->messenger()
        ->addError("Error when importing: " . $e->getMessage());
    }
  }

  public function lazyImport(array &$form, FormStateInterface $form_state) {
    $this->messenger()
      ->addWarning("You're so lazy!");
  }
}
