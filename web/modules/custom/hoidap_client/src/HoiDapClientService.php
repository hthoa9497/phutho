<?php

namespace Drupal\hoidap_client;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\Entity\User;

class HoiDapClientService implements HoiDapClientServiceInterface {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    AccountProxyInterface $accountProxy
  ) {
    $this->currentUser = $accountProxy;
  }

  public function checkUserIsDV($dv_name) {
    $current_user = User::load($this->currentUser->id());
    if (!empty($current_user)) {
      $don_vi_id = $current_user->field_user_don_vi->target_id;
      if (!empty($don_vi_id)) {
        $term_current = Term::load($don_vi_id);
        if (!empty($term_current)) {
          if ($term_current->getName() === $dv_name) {
            return TRUE;
          }
        }
      }
    }
    return FALSE;
  }

  public function getUserDV() {
    $current_user = User::load($this->currentUser->id());
    if (!empty($current_user)) {
      $don_vi_id = $current_user->field_user_don_vi->target_id;
      if (!empty($don_vi_id)) {
        $term_current = Term::load($don_vi_id);
        if (!empty($term_current)) {
          return $term_current->getName();
        }
      }
    }
    return NULL;
  }

  private function getTidByName($name = NULL, $vid = NULL) {
    $properties = [];
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vid)) {
      $properties['vid'] = $vid;
    }
    $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadByProperties($properties);
    $term = reset($terms);

    return !empty($term) ? $term->id() : 0;
  }
}
