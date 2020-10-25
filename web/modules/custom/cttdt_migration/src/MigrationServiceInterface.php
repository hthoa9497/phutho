<?php

namespace Drupal\cttdt_migration;

/**
 * Interface MigrationServiceInterface.
 */
interface MigrationServiceInterface {

  public function listOldPost();
  public function drupal_add_existing_file($file_drupal_path, $uid = 1, $status = FILE_STATUS_PERMANENT);
  public function getTidByName($name = NULL, $vid = NULL);
}
