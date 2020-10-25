<?php

namespace Drupal\cttdt_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Render\Markup;

/**
 * Provides a 'ThoiTiet' Block.
 *
 * @Block(
 *   id = "thoi_tiet_block",
 *   admin_label = @Translation("Thoi tiet block"),
 * )
 */
class ThoiTietBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $output = '<div class="status-weather">
                <div class="description description-thoi-tiet"></div>
                <img src="" alt="">
                <div class="temperature"></div>
              </div>
              <div class="thoi-tiet-group">
                <label for="">Các vùng khác</label>
                <select id="thoi-tiet">
                  <option data-lon="105.4" data-lat="21.32" value="0" selected>TP.Việt Trì</option>
                  <option data-lon="105.21" data-lat="21.4" value="1">TX.Phú Thọ</option>
                  <option data-lon="105.14" data-lat="21.42" value="2">Cẩm Khê</option>
                  <option data-lon="105.18" data-lat="21.62" value="3">Đoan Hùng</option>
                  <option data-lon="105.01" data-lat="21.56" value="4">Hạ Hòa</option>
                  <option data-lon="105.29" data-lat="21.32" value="5">Lâm Thao</option>
                  <option data-lon="105.29" data-lat="21.25" value="6">Tam Nông</option>
                  <option data-lon="105.14" data-lat="21.5" value="7">Thanh Ba</option>
                  <option data-lon="105.18" data-lat="21.21" value="8">Thanh Sơn</option>
                  <option data-lon="105.31" data-lat="21.2" value="9">Thanh Thủy</option>
                  <option data-lon="105.04" data-lat="21.21" value="10">Tân Sơn</option>
                  <option data-lon="105.31" data-lat="21.41" value="11">Phù Ninh</option>
                  <option data-lon="105.05" data-lat="21.36" value="12">Yên Lập</option>
                </select>
              </div>
              <div class="average-temperature">Dự báo 24h: <span></span></div>';

    return [
      '#markup' => Markup::create($output),
    ];
  }
}
