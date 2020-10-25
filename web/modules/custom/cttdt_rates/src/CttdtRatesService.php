<?php

namespace Drupal\cttdt_rates;

use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\cttdt_rates\Entity\CttdtRates;
use Drupal\taxonomy\Entity\Term;

class CttdtRatesService implements CttdtRatesServiceInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    $entityTypeManager,
    AccountProxyInterface $accountProxy,
    Connection $connection
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $accountProxy;
    $this->connection = $connection;
  }

  public function getCoQuanBanNganh() {
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('don_vi_danh_gia');

    $co_quan = [];
    foreach ($terms as $key => $term) {
      $co_quan[$key]['term_id'] = $term->tid;
      $co_quan[$key]['term_name'] = $term->name;
    }

    return $co_quan;
  }

  public function rateCoQuanBanNganh($donVi, $level) {
    $current_date = date('d/m/Y H:i');

    $rate_coquan = CttdtRates::create([
      'title' => 'Đánh giá sự phục vụ của cơ quan chính phủ nhà nước, ngày ' . $current_date . ' by uid: '.$this->currentUser->id(),
      'field_muc_do_danh_gia' => $level,
      'field_co_quan_ban_nganh' => ['target_id' => $donVi],
      'uid' => $this->currentUser->id()
    ]);

    if ($rate_coquan->save()) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function getResultRate($donVi) {
    $ratesStorage = $this->entityTypeManager->getStorage('cttdt_rates');
    $query = $ratesStorage->getQuery();
    $query->condition('field_co_quan_ban_nganh', $donVi);
    $ids = $query->execute();

    if (!count($ids)) {
      return NULL;
    }
    $rates = $ratesStorage->loadMultiple($ids);

    $rat_hai_long = 0;
    $hai_long = 0;
    $chap_nhan_duoc = 0;
    $k_hai_long = 0;
    $k_the_chap_nhan = 0;
    foreach ($rates as $rate) {
      if ($rate->field_muc_do_danh_gia->value == 0) {
        $rat_hai_long++;
      } elseif ($rate->field_muc_do_danh_gia->value == 1) {
        $hai_long++;
      } elseif ($rate->field_muc_do_danh_gia->value == 2) {
        $chap_nhan_duoc++;
      } elseif ($rate->field_muc_do_danh_gia->value == 3) {
        $k_hai_long++;
      } else {
        $k_the_chap_nhan++;
      }
    }
    $result = array(
      'rat_hai_long' => $rat_hai_long,
      'hai_long' => $hai_long,
      'chap_nhan_duoc' => $chap_nhan_duoc,
      'k_hai_long' => $k_hai_long,
      'k_the_chap_nhan' => $k_the_chap_nhan
    );

    return $result;
  }

  public function tkRateChatLuong($monthYear) {
    $database = $this->connection;
    $query = $database->query("SELECT DATE_FORMAT(FROM_UNIXTIME(t.created), '%m-%Y') as month, t.id
                                      FROM {cttdt_rates_field_data} t
                                      WHERE DATE_FORMAT(FROM_UNIXTIME(t.created), '%m-%Y') = '".$monthYear."'
                                      GROUP BY DATE_FORMAT(FROM_UNIXTIME(t.created), '%m-%Y'), t.id DESC");

    $query = $query->fetchAll();
    $rate_ids = [];

    if (!empty($query)) {
      foreach ($query as $item) {
        if (!empty($item->id)) {
          array_push($rate_ids, $item->id);
        }
      }
    }

    $rate_detail = $this->getRateChatLuongDetail($rate_ids);
    $result = [];
    foreach ($query as $item) {
      foreach ($rate_detail as $rate) {
        if ($item->id == $rate['rate_id']) {
          $don_vi_name = '';
          if (!empty($rate['don_vi'])) {
            $term_dv = Term::load($rate['don_vi']);
            $don_vi_name = $term_dv->getName();
          }

          $result[$item->month][$rate['don_vi']]['don_vi'] = $don_vi_name;
          $result[$item->month][$rate['don_vi']]['rates'][] = [
            "level" => $rate['level']
          ];
        }
      }
    }

    $result_final = [];

    foreach ($result as $k => $item) {
      foreach ($item as $key => $val) {
        $arr_temp = [];
        foreach ($val['rates'] as $lev) {
          array_push($arr_temp, $lev['level']);
        }
        $result_final[$key]['don_vi'] = $val['don_vi'];
        $result_final[$key]['rate_data'] = array_count_values($arr_temp);
      }
    }

    return $result_final;
  }

  private function getRateChatLuongDetail($ids) {
    $ratesStorage = $this->entityTypeManager->getStorage('cttdt_rates');
    if (!count($ids)) {
      return NULL;
    }
    $rates = $ratesStorage->loadMultiple($ids);
    $rate_data = [];
    foreach ($rates as $key => $rate) {
      $don_vi = !empty($rate->field_co_quan_ban_nganh) ? $rate->field_co_quan_ban_nganh->target_id : NULL;
      $rate_data[$key]['rate_id'] = $rate->id();
      $rate_data[$key]['don_vi'] = $don_vi;
      $rate_data[$key]['level'] = $rate->field_muc_do_danh_gia->value;
    }

    return $rate_data;
  }
}
