<?php

namespace Drupal\hoidap_client\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class HoiDapClientController extends ControllerBase {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  public function __construct(FormBuilder $form_builder) {
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }

  public function hoiDapTraLoi() {
    $form = $this->formBuilder->getForm('Drupal\hoidap_client\Form\HoiDapTraLoiForm');
    return [
      '#theme' => 'hoi_dap_tra_loi',
      '#form' => $form,
      '#data' => ''
    ];
  }

  public function hoiDapTraLoiDN() {
    $form = $this->formBuilder->getForm('Drupal\hoidap_client\Form\HoiDapTraLoiDnForm');
    return [
      '#theme' => 'hoi_dap_tra_loi_dn',
      '#form' => $form,
      '#data' => ''
    ];
  }

  public function getCauHoiChuaTraLoi() {
    return [
      '#theme' => 'cau_hoi_chua_tra_loi',
      '#data' => ''
    ];
  }

  public function getCauHoiChuaTraLoiDN() {
    return [
      '#theme' => 'cau_hoi_chua_tra_loi_dn',
      '#data' => ''
    ];
  }

  public function getDSCauHoi() {
    return [
      '#theme' => 'ds_cau_hoi_hoi_dap',
      '#data' => ''
    ];
  }

  public function getDSCauHoiDN() {
    return [
      '#theme' => 'ds_cau_hoi_hoi_dap_dn',
      '#data' => ''
    ];
  }

  public function getBaoCaoDv() {
    return [
      '#theme' => 'bao_cao_don_vi',
      '#data' => ''
    ];
  }

  public function getHoiDapInfo() {
    $don_vi = \Drupal::service('hoidap_client.hoidap')->getUserDV();
    $config = \Drupal::config('hoidap_client.hoidapconfig');
    $cong_address =  !empty($config->get('cong_address')) ? $config->get('cong_address') : NULL;

    $arr_info = array(
      "don_vi" => $don_vi,
      "cong_address" => $cong_address
    );
    return new JsonResponse($arr_info);
  }
}
