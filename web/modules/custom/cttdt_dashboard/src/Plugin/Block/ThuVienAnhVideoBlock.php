<?php

namespace Drupal\cttdt_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Render\Markup;

/**
 * Provides a 'ThuVienAnhVideo' Block.
 *
 * @Block(
 *   id = "thu_vien_anh_video_block",
 *   admin_label = @Translation("Thu vien anh, video block"),
 * )
 */
class ThuVienAnhVideoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'anh_video_library',
      '#title' => 'Anh Video Library',
      '#description' => 'my custom desc'
    );
  }
}
