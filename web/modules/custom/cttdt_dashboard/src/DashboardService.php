<?php

namespace Drupal\cttdt_dashboard;
use Drupal\author_management\Entity\AuthorManagement;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;
use Drupal\Core\Database\DatabaseAccessDeniedException;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\Entity\User;

/**
 * Class DashboardService.
 */
class DashboardService implements DashboardServiceInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

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
   * Constructs a new CttdtService object.
   */
  public function __construct(
    Connection $connection,
    AccountProxyInterface $accountProxy,
    $entityTypeManager
  ) {
    $this->connection = $connection;
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $accountProxy;
  }

  public function getTinChoBienTapNew($state) {
    $database = $this->connection;

    $query = $database->query("SELECT nid FROM {node_field_revision}
                                      GROUP BY nid HAVING count(nid) = 1");

    $query = $query->fetchAll();
    $result = [];
    if (!empty($query)) {
      foreach ($query as $item) {
        array_push($result, $item->nid);
      }
    }
    $result = implode("','",$result);

    $moder_query = $database->query("SELECT content_entity_id FROM {content_moderation_state_field_data}
                                      WHERE content_entity_id IN ('".$result."')
                                      AND moderation_state = '".$state."'");
    $result_final = [];
    if (!empty($moder_query)) {
      foreach ($moder_query as $item) {
        array_push($result_final, $item->content_entity_id);
      }
    }

    return $result_final;
  }

  public function getTinChoBienTapDo($state) {
    $database = $this->connection;

    $query = $database->query("SELECT nid FROM {node_field_revision}
                                      GROUP BY nid HAVING count(nid) > 1");

    $query = $query->fetchAll();
    $result = [];
    if (!empty($query)) {
      foreach ($query as $item) {
        array_push($result, $item->nid);
      }
    }
    $result = implode("','",$result);

    $moder_query = $database->query("SELECT content_entity_id FROM {content_moderation_state_field_data}
                                      WHERE content_entity_id IN ('".$result."')
                                      AND moderation_state = '".$state."'");
    $result_final = [];
    if (!empty($moder_query)) {
      foreach ($moder_query as $item) {
        array_push($result_final, $item->content_entity_id);
      }
    }

    return $result_final;
  }

  public function getTPGroupUser($month) {
    $nodesStorage = $this->entityTypeManager->getStorage('node');
    $query = $nodesStorage->getQuery()
      ->sort('changed', 'DESC')
      ->condition('type', 'article')
      ->condition('status', 1)
      ->condition('field_he_so_nhuan_but', NULL, 'IS NOT NULL');
    $ids = $query->execute();

    if (!count($ids)) {
      return NULL;
    }
    $nodes = $nodesStorage->loadMultiple($ids);

    $tk_ct = [];
    $month_year = $month.'/'.date("Y");

    if (!empty($nodes)) {
      foreach ($nodes as $key => $post) {
        $post_changed =  \Drupal::service('date.formatter')->format($post->getChangedTime(), 'custom', 'n/Y');
        if ($post_changed == $month_year) {
          $tk_ct[$post->getOwnerId()][$key]['post_title'] = $post->getTitle();
          $tk_ct[$post->getOwnerId()][$key]['post_tac_gia'] = !empty($post->field_tac_gia) ? $post->field_tac_gia->value : NULL;
          $tk_ct[$post->getOwnerId()][$key]['post_hs_nb'] = !empty($post->field_he_so_nhuan_but) ? $post->field_he_so_nhuan_but->value : NULL;
          $tk_ct[$post->getOwnerId()][$key]['post_changed'] = \Drupal::service('date.formatter')->format($post->getChangedTime(), 'custom', 'd/m/Y');;
          $the_loai_id = !empty($post->field_the_loai) ? $post->field_the_loai->target_id : NULL;
          if (!empty($the_loai_id)) {
            $the_loai_term = Term::load($the_loai_id);
            $the_loai = $the_loai_term->getName();
            $tk_ct[$post->getOwnerId()][$key]['post_the_loai'] = $the_loai;
          }
        }
      }

      if (!empty($tk_ct)) {
        foreach ($tk_ct as $k => $item) {
          $total_hs = 0;
          foreach ($item as $val) {
            $total_hs = $total_hs + $val['post_hs_nb'];
          }
          $tk_ct[$k]['total_hs'] = $total_hs;
        }
      }
    }

    return $tk_ct;
  }

  public function getNBGroupUser($month) {
    $nodesStorage = $this->entityTypeManager->getStorage('node');
    $query = $nodesStorage->getQuery()
      ->sort('changed', 'DESC')
      ->condition('type', 'article')
      ->condition('status', 1)
      ->condition('field_he_so_nhuan_but', NULL, 'IS NOT NULL');
    $ids = $query->execute();

    if (!count($ids)) {
      return NULL;
    }
    $nodes = $nodesStorage->loadMultiple($ids);

    $tk_nb = [];
    $month_year = $month.'/'.date("Y");
    $nb = [];
    if (!empty($nodes)) {
      foreach ($nodes as $key => $post) {
        $post_changed =  \Drupal::service('date.formatter')->format($post->getChangedTime(), 'custom', 'n/Y');
        if ($post_changed == $month_year) {
          $but_danh_total = !empty($post->field_but_danh) ? count($post->field_but_danh) : 0;
          if ($but_danh_total == 1) {
            $nb[$post->field_but_danh->target_id][$key]['post_hs_nb'] = !empty($post->field_he_so_nhuan_but) ? $post->field_he_so_nhuan_but->value : NULL;
          }
        }
      }

      if (!empty($nb)) {
        foreach ($nb as $k => $item) {
          $but_danh = AuthorManagement::load($k);
          if (!empty($but_danh)) {
            $tk_nb[$k]['post_ho_ten'] = $but_danh->title->value;
          }
          $total_hs = 0;
          foreach ($item as $val) {
            $total_hs = $total_hs + $val['post_hs_nb'];
          }

          $hs = 0;
          if (!empty($but_danh->field_he_so_khoan->value)) {
            $hs = $but_danh->field_he_so_khoan->value;
          }

          $config = \Drupal::config('cttdt_dashboard.cttdtconfig');
          $luong_co_so = ($config->get('luong_co_so') > 0) ? $config->get('luong_co_so') : 0;
          $tien_nhuan_but = (($total_hs - $hs) * ((10 * $luong_co_so)/100));
          $tien_nhuan_but = ($tien_nhuan_but > 0) ? number_format($tien_nhuan_but, 0, '', ',') : t('Không đủ điều kiện để tính nhuận bút');
          $tk_nb[$k]['tien_nhuan_but'] = $tien_nhuan_but;
        }
      }
    }

    return $tk_nb;
  }

  public function getNBGroupUserCommon($month) {
    $nodesStorage = $this->entityTypeManager->getStorage('node');
    $query = $nodesStorage->getQuery()
      ->sort('changed', 'DESC')
      ->condition('type', 'article')
      ->condition('status', 1)
      ->condition('field_he_so_nhuan_but', NULL, 'IS NOT NULL');
    $ids = $query->execute();

    if (!count($ids)) {
      return NULL;
    }
    $nodes = $nodesStorage->loadMultiple($ids);

    $tk_nb = [];
    $month_year = $month.'/'.date("Y");
    $nb = [];
    if (!empty($nodes)) {
      foreach ($nodes as $key => $post) {
        $post_changed =  \Drupal::service('date.formatter')->format($post->getChangedTime(), 'custom', 'n/Y');
        if ($post_changed == $month_year) {
          $but_danh_total = !empty($post->field_but_danh) ? count($post->field_but_danh) : 0;
          if ($but_danh_total > 1) {
            $nb[$key]['post_title'] = $post->title->value;
            $nb[$key]['post_created'] = $post->getCreatedTime();
            $nb[$key]['post_hs_nb'] = !empty($post->field_he_so_nhuan_but) ? $post->field_he_so_nhuan_but->value : NULL;
            foreach ($post->field_but_danh as $item) {
              $nb[$key]['post_tac_gia'][] = $item->target_id;
            }
          }
        }
      }

      if (!empty($nb)) {
        foreach ($nb as $k => $item) {
          $but_danh_list = [];
          foreach ($item['post_tac_gia'] as $author_id) {
            $but_danh = AuthorManagement::load($author_id);
            if (!empty($but_danh)) {
              array_push($but_danh_list, $but_danh->title->value);
            }
          }
          $tk_nb[$k]['title'] = $item['post_title'];
          $tk_nb[$k]['post_ho_ten'] = implode(" - ", $but_danh_list);
          $tk_nb[$k]['post_created'] = \Drupal::service('date.formatter')->format($item['post_created'], 'D, m/d/Y - H:i');
          $total_hs = 0;
          if ($item['post_hs_nb'] > 0) {
            $total_hs = $item['post_hs_nb'];
          }
          $tk_nb[$k]['hs_nhuan_but'] = $total_hs;
        }
      }
    }

    return $tk_nb;
  }

  protected function setRemoteConnection(){
    Database::setActiveConnection('remote');
  }

  protected function setDefaultConnection(){
    Database::setActiveConnection('default');
  }

  protected function logDatabaseError($name, $message){
    \Drupal::logger($name)->notice('Error connecting', $message);
  }

  private $list_crawler = array();

  public function getPostCrawler() {
    $this->setRemoteConnection();
    try{
      $db = Database::getConnection();

      $data = $db->select('articles', 'article_crawler')
        ->fields('article_crawler')
        ->condition('article_crawler.status', 0, '=')
        ->execute()->fetchAll();


    }catch (DatabaseAccessDeniedException $e) {
      $this->logDatabaseError('article_crawler getPostCrawler', $e->getMessage());
    }finally {
      $this->setDefaultConnection();
    }

    if(!$data){
      return $this->list_crawler;
    }

    foreach ($data as $row){
      $crawler = [];
      $crawler['id'] = $row->id;
      $crawler['title'] = $row->title;
      $crawler['url'] = $row->url;
      $crawler['summary'] = $row->summary;
      $crawler['image_url'] = $row->image_url;
      $crawler['date'] = $row->date;

      $this->list_crawler[] = $crawler;
    }
    return $this->list_crawler;
  }

  public function updateStatusPostCrawler($id) {
    $this->setRemoteConnection();
    try{
      $db = Database::getConnection();

      $data = $db->update('articles')
        ->fields(array('status' => 1))
        ->condition('id', $id, '=')
        ->execute();
    }catch (DatabaseAccessDeniedException $e) {
      $this->logDatabaseError('article_crawler updateStatusPostCrawler', $e->getMessage());
    }finally {
      $this->setDefaultConnection();
    }
  }

  public function fetch_http_file_contents($url) {
    $hostname = parse_url($url, PHP_URL_HOST);
    if ($hostname == FALSE) {
      return FALSE;
    }

    $host_has_ipv6 = FALSE;
    $host_has_ipv4 = FALSE;
    $file_response = FALSE;

    $dns_records = dns_get_record($hostname, DNS_AAAA + DNS_A);

    foreach ($dns_records as $dns_record) {
      if (isset($dns_record['type'])) {
        switch ($dns_record['type']) {
          case 'AAAA':
            $host_has_ipv6 = TRUE;
            break;
          case 'A':
            $host_has_ipv4 = TRUE;
            break;
        } } }

    if ($host_has_ipv6 === TRUE) {
      $file_response = $this->file_get_intbound_contents($url, '[0]:0');
    }
    if ($host_has_ipv4 === TRUE && $file_response == FALSE) {
      $file_response = $this->file_get_intbound_contents($url, '0:0');
    }

    return $file_response;
  }

  private function file_get_intbound_contents($url, $bindto_addr_family) {
    $stream_context = stream_context_create(
      array(
        'socket' => array(
          'bindto' => $bindto_addr_family
        ),
        'http' => array(
          'timeout'=>20,
          'method'=>'GET'
        ) ) );

    return file_get_contents($url, FALSE, $stream_context);
  }
}
