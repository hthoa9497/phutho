<?php

namespace Drupal\cttdt_hoi_dap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HoiDapController extends ControllerBase {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  public function __construct(FormBuilder $form_builder) {
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }

  public function getHoiDap($id) {
    $node_hoi_dap = Node::load($id);
    $arr_hoidap = [];
    $moderation_state = $node_hoi_dap->get('moderation_state')->getValue();
    $moderation_state = $moderation_state[0]['value'];
    if (!empty($node_hoi_dap)) {
      if ($moderation_state === 'phan_cong') {

        $file_src = '';
        if (!empty($node_hoi_dap->field_gui_file_dinh_kem)) {
          $file_id = $node_hoi_dap->field_gui_file_dinh_kem->target_id;
          if (!empty($file_id)) {
            $file_dk = File::load($file_id);
            $file_src = file_create_url($file_dk->getFileUri());
          }
        }

        $file_tl_src = '';
        if (!empty($node_hoi_dap->field_file_dinh_kem_tra_loi)) {
          $file_id_tl = $node_hoi_dap->field_file_dinh_kem_tra_loi->target_id;
          if (!empty($file_id_tl)) {
            $file_dk_tl = File::load($file_id_tl);
            $file_tl_src = file_create_url($file_dk_tl->getFileUri());
          }
        }
        $arr_hoidap['ho_ten'] = $node_hoi_dap->field_hoi_dap_ho_ten->value;
        $arr_hoidap['email'] = $node_hoi_dap->field_hoi_dap_email->value;
        $arr_hoidap['dia_chi'] = $node_hoi_dap->field_hoi_dap_dia_chi->value;
        $arr_hoidap['dien_thoai'] = $node_hoi_dap->field_hoi_dap_dien_thoai->value;
        $arr_hoidap['title'] = $node_hoi_dap->getTitle();
        $arr_hoidap['cau_hoi'] = $node_hoi_dap->body->value;
        $arr_hoidap['file_dk'] = !empty($file_src) ? $file_src : NULL;
        $arr_hoidap['file_dk_tl'] = !empty($file_tl_src) ? $file_tl_src : NULL;
        $arr_hoidap['thoi_gian_tra_loi'] = $node_hoi_dap->field_thoi_gian_tra_loi->value;
        $arr_hoidap['tra_loi'] = $node_hoi_dap->field_tra_loi->value;
        if (!empty($node_hoi_dap->field_don_vi_xu_ly)) {
          $don_vi = Term::load($node_hoi_dap->field_don_vi_xu_ly->target_id);
        }

        $arr_hoidap['don_vi'] = !empty($don_vi) ? $don_vi->getName() : NULL;
      }
    }
    return new JsonResponse($arr_hoidap);
  }

  public function getHoiDapDN($id) {
    $node_hoi_dap = Node::load($id);
    $arr_hoidap = [];
    $moderation_state = $node_hoi_dap->get('moderation_state')->getValue();
    $moderation_state = $moderation_state[0]['value'];
    if (!empty($node_hoi_dap)) {
      if ($moderation_state === 'phan_cong') {

        $file_src = '';
        if (!empty($node_hoi_dap->field_gui_file_dinh_kem)) {
          $file_id = $node_hoi_dap->field_gui_file_dinh_kem->target_id;
          if (!empty($file_id)) {
            $file_dk = File::load($file_id);
            $file_src = file_create_url($file_dk->getFileUri());
          }
        }

        $file_tl_src = '';
        if (!empty($node_hoi_dap->field_file_dinh_kem_tra_loi)) {
          $file_id_tl = $node_hoi_dap->field_file_dinh_kem_tra_loi->target_id;
          if (!empty($file_id_tl)) {
            $file_dk_tl = File::load($file_id_tl);
            $file_tl_src = file_create_url($file_dk_tl->getFileUri());
          }
        }
        $arr_hoidap['doanh_nghiep'] = $node_hoi_dap->field_doanh_nghiep->value;
        $arr_hoidap['email'] = $node_hoi_dap->field_hoi_dap_email->value;
        $arr_hoidap['dia_chi'] = $node_hoi_dap->field_hoi_dap_dia_chi->value;
        $arr_hoidap['dien_thoai'] = $node_hoi_dap->field_hoi_dap_dien_thoai->value;
        $arr_hoidap['title'] = $node_hoi_dap->getTitle();
        $arr_hoidap['cau_hoi'] = $node_hoi_dap->body->value;
        $arr_hoidap['file_dk'] = !empty($file_src) ? $file_src : NULL;
        $arr_hoidap['file_dk_tl'] = !empty($file_tl_src) ? $file_tl_src : NULL;
        $arr_hoidap['thoi_gian_tra_loi'] = $node_hoi_dap->field_thoi_gian_tra_loi->value;
        $arr_hoidap['tra_loi'] = $node_hoi_dap->field_tra_loi->value;
        if (!empty($node_hoi_dap->field_don_vi_xu_ly)) {
          $don_vi = Term::load($node_hoi_dap->field_don_vi_xu_ly->target_id);
        }

        $arr_hoidap['don_vi'] = !empty($don_vi) ? $don_vi->getName() : NULL;
      }
    }
    return new JsonResponse($arr_hoidap);
  }

  public function traLoiHoiDap(Request $request) {

    $tra_loi = $request->get('tra_loi');
    $id = $request->get('id');
    $file_dk_tra_loi = $request->get('file_dk_tl');
    $moderation = $request->get('moderation');


    if (!empty($id)) {
      $node = Node::load($id);
      if (!empty($node)) {
        $doc = system_retrieve_file($file_dk_tra_loi, 'public://hoi-dap-files/', FALSE, FILE_EXISTS_REPLACE);
        $file_dk = '';
        if (!empty($doc)) {
          $file_dk = \Drupal::service('cttdt_migration.migration')->drupal_add_existing_file($doc);
        }

        $node->field_tra_loi->value = $tra_loi;
        $node->field_tra_loi->format = 'full_html';
        if (!empty($file_dk)) {
          $node->set('field_file_dinh_kem_tra_loi', array(
            'target_id' => $file_dk->id()
          ));
        }
        if (!empty($moderation) && $moderation === 'tra_loi') {
          $node->set('moderation_state', "tra_loi");
        }
        if ($node->save()) {
          return new JsonResponse(TRUE);
        }
      }
    }
    return new JsonResponse($tra_loi);
  }

  public function getHoiDapChuaTraLoi(Request $request) {
    $dv = $request->get('don_vi');
    $dv_id = \Drupal::service('cttdt_migration.migration')->getTidByName($dv);
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'hoi_dap');
    $and = $query->andConditionGroup();
    $and->condition('field_don_vi_xu_ly', $dv_id);
    $query->condition($and);
    $entity_ids = $query->execute();

    $result = [];
    if (!empty($entity_ids)) {
      $cau_hoi = Node::loadMultiple($entity_ids);
      $i = 0;
      foreach ($cau_hoi as $key => $item) {
        if (!empty($item)) {
          $moderation_state = $item->get('moderation_state')->getValue();
          $moderation_state = $moderation_state[0]['value'];

          if ($moderation_state === 'phan_cong') {
            $created = $item->getCreatedTime();

            $result[$i]['id'] = $item->id();
            $result[$i]['cau_hoi'] = $item->body->value;
            $result[$i]['ho_ten'] = $item->field_hoi_dap_ho_ten->value;
            $result[$i]['email'] = $item->field_hoi_dap_email->value;
            $result[$i]['dia_chi'] = $item->field_hoi_dap_dia_chi->value;
            $result[$i]['dien_thoai'] = $item->field_hoi_dap_dien_thoai->value;
            $result[$i]['ngay_tao'] = date('d/m/Y h:i:s', $created);
            $result[$i]['thoi_gian_tra_loi'] = !empty($item->field_thoi_gian_tra_loi->value) ? date('d/m/Y', strtotime($item->field_thoi_gian_tra_loi->value)) : '';
            $i++;
          }
        }
      }
    }
    return new JsonResponse($result);
  }

  public function getHoiDapChuaTraLoiDN(Request $request) {
    $dv = $request->get('don_vi');
    $dv_id = \Drupal::service('cttdt_migration.migration')->getTidByName($dv);
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'hoi_dap_doanh_nghiep');
    $and = $query->andConditionGroup();
    $and->condition('field_don_vi_xu_ly', $dv_id);
    $query->condition($and);
    $entity_ids = $query->execute();

    $result = [];
    if (!empty($entity_ids)) {
      $cau_hoi = Node::loadMultiple($entity_ids);
      $i = 0;
      foreach ($cau_hoi as $key => $item) {
        if (!empty($item)) {
          $moderation_state = $item->get('moderation_state')->getValue();
          $moderation_state = $moderation_state[0]['value'];

          if ($moderation_state === 'phan_cong') {
            $created = $item->getCreatedTime();

            $result[$i]['id'] = $item->id();
            $result[$i]['cau_hoi'] = $item->label();
            $result[$i]['doanh_nghiep'] = $item->field_doanh_nghiep->value;
            $result[$i]['email'] = $item->field_hoi_dap_email->value;
            $result[$i]['dia_chi'] = $item->field_hoi_dap_dia_chi->value;
            $result[$i]['dien_thoai'] = $item->field_hoi_dap_dien_thoai->value;
            $result[$i]['ngay_tao'] = date('d/m/Y h:i:s', $created);
            $result[$i]['thoi_gian_tra_loi'] = !empty($item->field_thoi_gian_tra_loi->value) ? date('d/m/Y', strtotime($item->field_thoi_gian_tra_loi->value)) : '';
            $i++;
          }
        }
      }
    }
    return new JsonResponse($result);
  }

  public function getListHoiDap(Request $request) {
    $dv = $request->get('don_vi');
    $dv_id = \Drupal::service('cttdt_migration.migration')->getTidByName($dv);
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'hoi_dap');
    $and = $query->andConditionGroup();
    $and->condition('field_don_vi_xu_ly', $dv_id);
    $query->condition($and);
    $entity_ids = $query->execute();

    $result = [];
    if (!empty($entity_ids)) {
      $cau_hoi = Node::loadMultiple($entity_ids);
      $i = 0;
      foreach ($cau_hoi as $key => $item) {
        if (!empty($item)) {
          $result[$i]['id'] = $item->id();
          $result[$i]['cau_hoi'] = $item->body->value;
          $result[$i]['ho_ten'] = $item->field_hoi_dap_ho_ten->value;
          $result[$i]['email'] = $item->field_hoi_dap_email->value;
          $result[$i]['dia_chi'] = $item->field_hoi_dap_dia_chi->value;
          $result[$i]['dien_thoai'] = $item->field_hoi_dap_dien_thoai->value;
          $created = $item->getCreatedTime();
          $result[$i]['ngay_tao'] = date('d/m/Y', $created);
          $result[$i]['thoi_gian_tra_loi'] = !empty($item->field_thoi_gian_tra_loi->value) ? date('d/m/Y', strtotime($item->field_thoi_gian_tra_loi->value)) : '';

          $moderation_state = $item->get('moderation_state')->getValue();
          $moderation_state = $moderation_state[0]['value'];
          $trang_thai = '';
          if ($moderation_state === 'phan_cong') {
            $trang_thai = t('Chưa trả lời');
          }
          elseif ($moderation_state === 'tra_loi') {
            $trang_thai = t('Đã trả lời');
          }
          elseif ($moderation_state === 'published') {
            $trang_thai = t('Đã xuất bản');
          }

          $result[$i]['status'] = $trang_thai;
          $result[$i]['moderation'] = $moderation_state;
          $i++;
        }
      }
    }
    return new JsonResponse($result);
  }

  public function getListHoiDapDN(Request $request) {
    $dv = $request->get('don_vi');
    $dv_id = \Drupal::service('cttdt_migration.migration')->getTidByName($dv);
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'hoi_dap_doanh_nghiep');
    $and = $query->andConditionGroup();
    $and->condition('field_don_vi_xu_ly', $dv_id);
    $query->condition($and);
    $entity_ids = $query->execute();

    $result = [];
    if (!empty($entity_ids)) {
      $cau_hoi = Node::loadMultiple($entity_ids);
      $i = 0;
      foreach ($cau_hoi as $key => $item) {
        if (!empty($item)) {
          $result[$i]['id'] = $item->id();
          $result[$i]['tieu_de'] = $item->label();
          $result[$i]['cau_hoi'] = $item->body->value;
          $result[$i]['doanh_nghiep'] = $item->field_doanh_nghiep->value;
          $result[$i]['email'] = $item->field_hoi_dap_email->value;
          $result[$i]['dia_chi'] = $item->field_hoi_dap_dia_chi->value;
          $result[$i]['dien_thoai'] = $item->field_hoi_dap_dien_thoai->value;
          $created = $item->getCreatedTime();
          $result[$i]['ngay_tao'] = date('d/m/Y', $created);
          $result[$i]['thoi_gian_tra_loi'] = !empty($item->field_thoi_gian_tra_loi->value) ? date('d/m/Y', strtotime($item->field_thoi_gian_tra_loi->value)) : '';

          $moderation_state = $item->get('moderation_state')->getValue();
          $moderation_state = $moderation_state[0]['value'];
          $trang_thai = '';
          if ($moderation_state === 'phan_cong') {
            $trang_thai = t('Chưa trả lời');
          }
          elseif ($moderation_state === 'tra_loi') {
            $trang_thai = t('Đã trả lời');
          }
          elseif ($moderation_state === 'published') {
            $trang_thai = t('Đã xuất bản');
          }

          $result[$i]['status'] = $trang_thai;
          $result[$i]['moderation'] = $moderation_state;
          $i++;
        }
      }
    }
    return new JsonResponse($result);
  }

  public function baoCaoDV() {
    $form = $this->formBuilder->getForm('Drupal\cttdt_hoi_dap\Form\BaoCaoDVFilterForm');
    $bao_cao = \Drupal::service('cttdt_hoi_dap.hoidap')->getBaoCaoDV();
    return [
      '#theme' => 'bao_cao_don_vi',
      '#form' => $form,
      '#data' => $bao_cao
    ];
  }

  public function getBaoCaoByDv(Request $request) {
    $dv = $request->get('don_vi');
    $dv_id = \Drupal::service('cttdt_migration.migration')->getTidByName($dv);
    $result = \Drupal::service('cttdt_hoi_dap.hoidap')->getBaoCaoDVFilter($dv_id, NULL, NULL);

    return new JsonResponse($result);
  }

  public function getChamTraLoi() {
    $form = $this->formBuilder->getForm('Drupal\cttdt_hoi_dap\Form\ChamTraLoiFilterForm');
    $data = \Drupal::service('cttdt_hoi_dap.hoidap')->getDsChamTraLoi(NULL, NULL, NULL);
    return [
      '#theme' => 'ds_cham_tra_loi',
      '#form' => $form,
      '#data' => $data
    ];
  }
}
