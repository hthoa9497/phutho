<?php

namespace Drupal\hoidap_client\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class HoiDapConfigForm.
 */
class HoiDapConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'hoidap_client.hoidapconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hoidap_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('hoidap_client.hoidapconfig');
    $form['cong_address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Địa chỉ cổng'),
      '#description' => $this->t('Domain Cổng thông tin tỉnh Phú Thọ'),
      '#default_value' => $config->get('cong_address'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('hoidap_client.hoidapconfig')
      ->set('cong_address', $form_state->getValue('cong_address'))
      ->save();
  }

}
