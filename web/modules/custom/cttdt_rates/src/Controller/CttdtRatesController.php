<?php

namespace Drupal\cttdt_rates\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CttdtRatesController extends ControllerBase {

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

  /**
   * return rate co quan theme
   */
  public function rateCoQuan() {
    $co_quan = \Drupal::service('cttdt_rates.rate')->getCoQuanBanNganh();
    return [
      '#theme' => 'cttdt_rates_co_quan',
      '#data' => $co_quan
    ];
  }

  /**
   * return boolean
   */
  public function saveRate(Request $request) {
    $don_vi = $request->get('don_vi');
    $level = $request->get('level');

    $result = \Drupal::service('cttdt_rates.rate')->rateCoQuanBanNganh($don_vi, $level);
    return new JsonResponse(json_encode($result));
  }

  /**
   * return array rates
   */
  public function rateResult(Request $request) {
    $don_vi = $request->get('don_vi');
    $result = \Drupal::service('cttdt_rates.rate')->getResultRate($don_vi);
    return new JsonResponse($result);
  }

  public function tkRateChatLuongPhucVu() {
    $form = $this->formBuilder->getForm('Drupal\cttdt_rates\Form\RateChatLuongPhucVuForm');
    $month_year = date("m") .'-'. date("Y");
    $result = \Drupal::service('cttdt_rates.rate')->tkRateChatLuong($month_year);
    return [
      '#theme' => 'tk_rate_chat_luong',
      '#form' => $form,
      '#data' => $result
    ];
  }
}
