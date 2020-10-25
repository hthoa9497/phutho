<?php

namespace Drupal\cttdt_rates;

/**
 * Interface CttdtRatesServiceInterface.
 */
interface CttdtRatesServiceInterface {

  public function getCoQuanBanNganh();
  public function rateCoQuanBanNganh($donVi, $level);
  public function getResultRate($donVi);
  public function tkRateChatLuong($monthYear);
}
