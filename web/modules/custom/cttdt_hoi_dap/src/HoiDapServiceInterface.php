<?php

namespace Drupal\cttdt_hoi_dap;

/**
 * Interface HoiDapServiceInterface.
 */
interface HoiDapServiceInterface {
  public function getListDV();
  public function getBaoCaoDV();
  public function getBaoCaoDVFilter($don_vi = NULL, $date_form  = NULL, $date_to  = NULL);
  public function getDsChamTraLoi($don_vi = NULL, $date_form  = NULL, $date_to  = NULL);
}
