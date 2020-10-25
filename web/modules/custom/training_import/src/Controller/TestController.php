<?php

namespace Drupal\training_import\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class TestController.
 ** @package Drupal\training_import\Controller
 */
class TestController extends ControllerBase {
  public static function index() {
    return ["#markup" => 'This route is defined in training_import module.'];
  }
}
