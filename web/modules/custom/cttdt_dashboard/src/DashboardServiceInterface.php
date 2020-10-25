<?php

namespace Drupal\cttdt_dashboard;

/**
 * Interface CttdtServiceInterface.
 */
interface DashboardServiceInterface {
  public function getTinChoBienTapNew($state);
  public function getTinChoBienTapDo($state);
  public function getTPGroupUser($month);
  public function getNBGroupUser($month);
  public function getPostCrawler();
  public function updateStatusPostCrawler($id);
}
