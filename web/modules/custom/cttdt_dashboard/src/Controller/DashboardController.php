<?php

namespace Drupal\cttdt_dashboard\Controller;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBuilder;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DashboardController extends ControllerBase {


  protected $formBuilder;
  private $injected_database;

  public function __construct(FormBuilder $form_builder, Connection $injected_database) {
    $this->formBuilder = $form_builder;
    $this->injected_database = $injected_database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder'),
      $container->get('database')
    );
  }

  /*
   * Return tin cho bt 1 moi page
   */
  public function choBT1New() {
    $new_posts = \Drupal::service('cttdt_dashboard.dashboard')->getTinChoBienTapNew('tin_cho_bien_tap_1');

    $header = [
      'image' => t('Hình ảnh'),
      'title' => t('Tiêu đề'),
      'danh_muc' => t('Danh mục'),
      'created' => t('Ngày tạo'),
      'opt' => '',
    ];

    $output = array();
    foreach ($new_posts as $node_id) {
      if (!empty($node_id)) {
        $post = Node::load($node_id);
        $danh_muc_id = $post->field_danh_muc->target_id;

        $name_dm = '';
        if (!empty($danh_muc_id)) {
          $term = Term::load($danh_muc_id);
          if (!empty($term)) {
            $name_dm = $term->getName();
          }
        }

        $post_created = $post->getCreatedTime();
        $post_created = \Drupal::service('date.formatter')->format($post_created, 'D, m/d/Y - H:i');

        $post_img = $this->_get_file_field_uri($post, 'field_image');
        $image_markup = NULL;

        if (!empty($post_img)) {
          $post_img_url = ImageStyle::load('104x73')->buildUrl($post_img);
          $image_markup = array('data' => new FormattableMarkup('<img src=":link" />',
            [':link' => $post_img_url])
          );
        }

        $edit = Url::fromRoute('entity.node.edit_form', ['node' => $node_id]);
        $path = '/node/' . (int) $node_id;
        $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $path_alias = \Drupal::service('path.alias_manager')->getAliasByPath($path, $langcode);
        $output[$node_id] = [
          'image' => $image_markup,
          'title' => array('data' => new FormattableMarkup('<a href=":link">@name</a>',
                        [':link' => $path_alias,
                          '@name' => $post->label()])
                      ),
          'danh_muc' => $name_dm,
          'created' => $post_created,
          'opt' => \Drupal::l('Sửa', $edit),
        ];
      }
    }

    $table = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $output,
      '#empty' => t('Không tìm thấy bài viết'),
    );

    return [
      '#theme' => 'cttdt_dashboard_chobtnew',
      '#data' => $table
    ];
  }

  /*
   * Return tin cho bt 1 do page
   */
  public function choBT1Do() {
    $new_posts = \Drupal::service('cttdt_dashboard.dashboard')->getTinChoBienTapDo('tin_cho_bien_tap_1');

    $header = [
      'image' => t('Hình ảnh'),
      'title' => t('Tiêu đề'),
      'danh_muc' => t('Danh mục'),
      'created' => t('Ngày tạo'),
      'opt' => '',
    ];

    $output = array();
    foreach ($new_posts as $node_id) {
      if (!empty($node_id)) {
        $post = Node::load($node_id);
        $danh_muc_id = $post->field_danh_muc->target_id;

        $name_dm = '';
        if (!empty($danh_muc_id)) {
          $term = Term::load($danh_muc_id);
          if (!empty($term)) {
            $name_dm = $term->getName();
          }
        }

        $post_created = $post->getCreatedTime();
        $post_created = \Drupal::service('date.formatter')->format($post_created, 'D, m/d/Y - H:i');

        $post_img = $this->_get_file_field_uri($post, 'field_image');
        $image_markup = NULL;

        if (!empty($post_img)) {
          $post_img_url = ImageStyle::load('104x73')->buildUrl($post_img);
          $image_markup = array('data' => new FormattableMarkup('<img src=":link" />',
            [':link' => $post_img_url])
          );
        }

        $edit = Url::fromRoute('entity.node.edit_form', ['node' => $node_id]);
        $path = '/node/' . (int) $node_id;
        $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $path_alias = \Drupal::service('path.alias_manager')->getAliasByPath($path, $langcode);
        $output[$node_id] = [
          'image' => $image_markup,
          'title' => array('data' => new FormattableMarkup('<a href=":link">@name</a>',
            [':link' => $path_alias,
              '@name' => $post->label()])
          ),
          'danh_muc' => $name_dm,
          'created' => $post_created,
          'opt' => \Drupal::l('Sửa', $edit),
        ];
      }
    }

    $table = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $output,
      '#empty' => t('Không tìm thấy bài viết'),
    );

    return [
      '#theme' => 'cttdt_dashboard_chobtdo',
      '#data' => $table
    ];
  }

  /*
   * Return tin cho bt 2 moi page
   */
  public function choBT2New() {
    $new_posts = \Drupal::service('cttdt_dashboard.dashboard')->getTinChoBienTapNew('tin_cho_bien_tap_2');

    $header = [
      'image' => t('Hình ảnh'),
      'title' => t('Tiêu đề'),
      'danh_muc' => t('Danh mục'),
      'created' => t('Ngày tạo'),
      'opt' => '',
    ];

    $output = array();
    foreach ($new_posts as $node_id) {
      if (!empty($node_id)) {
        $post = Node::load($node_id);
        $danh_muc_id = $post->field_danh_muc->target_id;

        $name_dm = '';
        if (!empty($danh_muc_id)) {
          $term = Term::load($danh_muc_id);
          if (!empty($term)) {
            $name_dm = $term->getName();
          }
        }

        $post_created = $post->getCreatedTime();
        $post_created = \Drupal::service('date.formatter')->format($post_created, 'D, m/d/Y - H:i');

        $post_img = $this->_get_file_field_uri($post, 'field_image');
        $image_markup = NULL;

        if (!empty($post_img)) {
          $post_img_url = ImageStyle::load('104x73')->buildUrl($post_img);
          $image_markup = array('data' => new FormattableMarkup('<img src=":link" />',
            [':link' => $post_img_url])
          );
        }

        $edit = Url::fromRoute('entity.node.edit_form', ['node' => $node_id]);
        $path = '/node/' . (int) $node_id;
        $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $path_alias = \Drupal::service('path.alias_manager')->getAliasByPath($path, $langcode);
        $output[$node_id] = [
          'image' => $image_markup,
          'title' => array('data' => new FormattableMarkup('<a href=":link">@name</a>',
            [':link' => $path_alias,
              '@name' => $post->label()])
          ),
          'danh_muc' => $name_dm,
          'created' => $post_created,
          'opt' => \Drupal::l('Sửa', $edit),
        ];
      }
    }

    $table = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $output,
      '#empty' => t('Không tìm thấy bài viết'),
    );

    return [
      '#theme' => 'cttdt_dashboard_chobt2new',
      '#data' => $table
    ];
  }

  /*
   * Return tin cho bt 1 do page
   */
  public function choBT2Do() {
    $new_posts = \Drupal::service('cttdt_dashboard.dashboard')->getTinChoBienTapDo('tin_cho_bien_tap_2');

    $header = [
      'image' => t('Hình ảnh'),
      'title' => t('Tiêu đề'),
      'danh_muc' => t('Danh mục'),
      'created' => t('Ngày tạo'),
      'opt' => '',
    ];

    $output = array();
    foreach ($new_posts as $node_id) {
      if (!empty($node_id)) {
        $post = Node::load($node_id);
        $danh_muc_id = $post->field_danh_muc->target_id;

        $name_dm = '';
        if (!empty($danh_muc_id)) {
          $term = Term::load($danh_muc_id);
          if (!empty($term)) {
            $name_dm = $term->getName();
          }
        }

        $post_created = $post->getCreatedTime();
        $post_created = \Drupal::service('date.formatter')->format($post_created, 'D, m/d/Y - H:i');

        $post_img = $this->_get_file_field_uri($post, 'field_image');
        $image_markup = NULL;

        if (!empty($post_img)) {
          $post_img_url = ImageStyle::load('104x73')->buildUrl($post_img);
          $image_markup = array('data' => new FormattableMarkup('<img src=":link" />',
            [':link' => $post_img_url])
          );
        }

        $edit = Url::fromRoute('entity.node.edit_form', ['node' => $node_id]);
        $path = '/node/' . (int) $node_id;
        $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $path_alias = \Drupal::service('path.alias_manager')->getAliasByPath($path, $langcode);
        $output[$node_id] = [
          'image' => $image_markup,
          'title' => array('data' => new FormattableMarkup('<a href=":link">@name</a>',
            [':link' => $path_alias,
              '@name' => $post->label()])
          ),
          'danh_muc' => $name_dm,
          'created' => $post_created,
          'opt' => \Drupal::l('Sửa', $edit),
        ];
      }
    }

    $table = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $output,
      '#empty' => t('Không tìm thấy bài viết'),
    );

    return [
      '#theme' => 'cttdt_dashboard_chobt2do',
      '#data' => $table
    ];
  }

  public function _get_file_field_uri($entity, $fieldName) {
    $image_uri = NULL;
    if( $entity->hasField($fieldName) ) {
      // Try loading from field values first.
      try {
        $field = $entity->{$fieldName};
        if ($field && $field->target_id) {
          if (!empty($field->target_id)) {
            $file = File::load($field->target_id);
          }

          if ($file) {
            $image_uri = $file->getFileUri();
          }
        }
      }
      catch (\Exception $e) {
        \Drupal::logger('get_image_uri')->notice($e->getMessage(), []);
      }
      // If a set value above wasn't found, try the default image.
      if (is_null($image_uri)) {
        try {
          $field = $entity->get($fieldName);
          if ($field) {
            $default_image = $field->getSetting('default_image');
            if ($default_image && $default_image['uuid']) {
              $entity_repository = Drupal::service('entity.repository');
              /** @var $defaultImageFile File */
              $defaultImageFile = $entity_repository->loadEntityByUuid('file', $default_image['uuid']);
              if ($defaultImageFile) {
                $image_uri = $defaultImageFile->getFileUri();
              }
            }
          }
        }
        catch (\Exception $e) {
          \Drupal::logger('get_image_uri')->notice($e->getMessage(), []);
        }
      }
    }
    return $image_uri;
  }

  public function tkTacPham() {
    $form = $this->formBuilder->getForm('Drupal\cttdt_dashboard\Form\TKTacPhamByMonth');

    $tk_ct = \Drupal::service('cttdt_dashboard.dashboard')->getTPGroupUser(date("n"));

    return [
      '#theme' => 'tk_tac_pham',
      '#form' => $form,
      '#data' => $tk_ct
    ];
  }

  public function tkNhuanBut() {
    $form = $this->formBuilder->getForm('Drupal\cttdt_dashboard\Form\TKNhuanButByMonth');

    $tk_nb = \Drupal::service('cttdt_dashboard.dashboard')->getNBGroupUser(date("n"));

    return [
      '#theme' => 'tk_nhuan_but',
      '#form' => $form,
      '#data' => $tk_nb
    ];
  }

  public function tkNhuanButCommon() {
    $form = $this->formBuilder->getForm('Drupal\cttdt_dashboard\Form\TKNhuanButCommonByMonth');

    $tk_nb = \Drupal::service('cttdt_dashboard.dashboard')->getNBGroupUserCommon(date("n"));

    return [
      '#theme' => 'tk_nhuan_but_common',
      '#form' => $form,
      '#data' => $tk_nb
    ];
  }

  public function statisticNodeViewCount() {
    $page_title = \Drupal::request()->query->get('title');
    $post_type = \Drupal::request()->query->get('post_category');
    $form['form'] = $this->formBuilder()->getForm('Drupal\cttdt_dashboard\Form\StatisticNodeCountFilter');

    $header = [
      'title' => $this->t('Tiêu đề'),
      'created' => $this->t('Ngày tạo'),
      'post_type' => $this->t('Chuyên mục'),
      'author' => $this->t('Tác giả'),
      'number_count'=> $this->t('Lượt xem')
    ];

    if ($page_title == "" && $post_type == "") {
      $result = $this->getListNodeViewCount("All","", "");
      $form['table'] = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $result,
        '#empty' => $this->t('No records found'),
      ];
    } else {
      $result = $this->getListNodeViewCount("", $page_title, $post_type);
      $form['table'] = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $result,
        '#empty' => $this->t('No records found'),
      ];
    }
    $form['pager'] = [
      '#type' => 'pager'
    ];
    return $form;
  }

  private function getListNodeViewCount($opt, $title, $post_type) {
    $all_child = [];
    $terms_child = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadChildren($post_type);
    foreach ($terms_child as $child) {
      array_push($all_child, $child->id());
    }

    $res = array();
    $ret = [];
    if ($opt == "All") {
      $results = $this->injected_database->select('nodeviewcount', 'st')
        ->extend('\Drupal\Core\Database\Query\PagerSelectExtender')
        ->limit(15);
      $results->join('node_field_data', 'n', 'n.nid = st.nid');
      $results->join('node__field_tin_crawl', 'crawl', 'crawl.entity_id = st.nid');
      $results->join('node__field_danh_muc', 'dm', 'dm.entity_id = st.nid');
      $results->join('node__field_tac_gia', 'tg', 'tg.entity_id = st.nid');
      $results->fields('st', array('nid'));
      $results->fields('n', array('title', 'changed'));
      $results->fields('dm', array('field_danh_muc_target_id'));
      $results->fields('tg', array('field_tac_gia_value'));
      $results->condition('crawl.field_tin_crawl_value', 0, '=');
      $results->groupBy('st.nid, n.title, n.changed, dm.field_danh_muc_target_id, tg.field_tac_gia_value');
      $results->orderBy('node_count', 'DESC');
      $results->addExpression('COUNT(st.id)', 'node_count');
    } else {
      $results = $this->injected_database->select('nodeviewcount', 'st')
        ->extend('\Drupal\Core\Database\Query\PagerSelectExtender')
        ->limit(15);
      $results->join('node_field_data', 'n', 'n.nid = st.nid');
      $results->join('node__field_tin_crawl', 'crawl', 'crawl.entity_id = st.nid');
      $results->join('node__field_danh_muc', 'dm', 'dm.entity_id = st.nid');
      $results->join('node__field_tac_gia', 'tg', 'tg.entity_id = st.nid');
      $results->fields('st', array('nid'));
      $results->fields('n', array('title', 'changed'));
      $results->fields('dm', array('field_danh_muc_target_id'));
      $results->fields('tg', array('field_tac_gia_value'));
      $results->condition('crawl.field_tin_crawl_value', 0, '=');
      if (!empty($title)) {
        $results->condition('n.title', "%" . $title . "%", 'LIKE');
      }
      if (!empty($post_type)) {
        if (count($all_child) > 0) {
          $results->condition('dm.field_danh_muc_target_id', $all_child, 'IN');
        }
        else {
          $results->condition('dm.field_danh_muc_target_id', $post_type, '=');
        }
      }
      $results->groupBy('st.nid, n.title, n.changed, dm.field_danh_muc_target_id, tg.field_tac_gia_value');
      $results->orderBy('node_count', 'DESC');
      $results->addExpression('COUNT(st.id)', 'node_count');
    }
    $res = $results->execute()->fetchAll();
    foreach ($res as $row) {
      $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $row->nid);
      if (empty($post_type)) {
        $term = Term::load($row->field_danh_muc_target_id);
      }
      else {
        $term = Term::load($post_type);
      }
      $term_name = '';
      if (!empty($term)) {
        $term_name = $term->getName();
      }
      $ret[] = [
        'title' => array('data' => new FormattableMarkup('<a href=":link">@name</a>',
          [':link' => $alias,
            '@name' => $row->title])),
        'created' => \Drupal::service('date.formatter')->format($row->changed, 'D, m/d/Y - H:i'),
        'post_type' => $term_name,
        'author' => $row->field_tac_gia_value,
        'number_count' => $row->node_count
      ];
    }
    return $ret;
  }
}
