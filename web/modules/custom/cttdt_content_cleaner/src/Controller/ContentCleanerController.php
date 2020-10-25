<?php

namespace Drupal\cttdt_content_cleaner\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContentCleanerController extends ControllerBase {
  public function clearOldContent() {
    $query = \Drupal::entityQuery('node')
      ->condition('created', '1546300800' , '<=')
      ->condition('type', 'article')
      ->range(0,10);
    $nodes = $query->execute();
    entity_delete_multiple('node', $nodes);
    print_r('Successful'); exit;
  }

  public function clearUnusedTerm() {
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple();
    $unused_tids = [];
    foreach (array_keys($terms) as $tid ) {
      $nids = \Drupal::entityQuery('node')
        ->condition('type', 'article')
        ->condition('field_danh_muc', $tid)
        ->range(0,1)
        ->execute();
      if (empty($nids)) {
        $unused_tids[] = $tid;
      }
    }
    $controller = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $entities = $controller->loadMultiple($unused_tids);
    $controller->delete($entities);
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple();
    print_r($terms);
    exit;
  }
}
