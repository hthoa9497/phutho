<?php

namespace Drupal\cttdt_dashboard\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CttdtConfigForm.
 */
class CttdtConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'cttdt_dashboard.cttdtconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cttdt_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('cttdt_dashboard.cttdtconfig');
    $form['luong_co_so'] = [
      '#type' => 'number',
      '#title' => $this->t('Mức tiền lương cơ sở'),
      '#description' => $this->t('Mức tiền lương cơ sở'),
      '#default_value' => $config->get('luong_co_so'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('cttdt_dashboard.cttdtconfig')
      ->set('luong_co_so', $form_state->getValue('luong_co_so'))
      ->save();
  }

}
