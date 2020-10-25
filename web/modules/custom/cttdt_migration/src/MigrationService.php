<?php

namespace Drupal\cttdt_migration;

use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;

class MigrationService implements MigrationServiceInterface {

  /**
   * Constructs a new MigrationService object.
   */
  public function __construct() {

  }

  public function listOldPost() {
    $strJsonFileContents = file_get_contents("migrations/TinTuc.json");
    $json_data = json_decode($strJsonFileContents,true);
    $old_post = [];
    foreach (array_reverse($json_data) as $key => $item) {
      $nd = $item['NoiDung'];
      $nd = str_replace("/Upload", "http://phutho.gov.vn/Upload", $nd);
      $old_post[$key]['TieuDe'] = $item['TieuDe'];
      $old_post[$key]['MoTa'] = $item['MoTa'];
      $old_post[$key]['NoiDung'] = $nd;
      $old_post[$key]['TacGia'] = $item['TacGia'];
      $old_post[$key]['NgayTao'] = $item['NgayTao'];
      $old_post[$key]['AnhDaiDien'] = $this->getFile($item['AnhDaiDien']);
      $old_post[$key]['ChuyenMuc'] = $this->getChuyenMuc($item['IDChuyenMuc']);
      $old_post[$key]['LoaiTin'] = $this->getLoaiTin($item['LoaiTin']);
      $old_post[$key]['NgayXuatBan'] = $item['NgayXuatBan'];
    }

    return $old_post;
  }

  public function listOldPostChuyenTrang($filename) {
    $strJsonFileContents = file_get_contents($filename);
    $json_data = json_decode($strJsonFileContents,true);
    $old_post = [];
    foreach (array_reverse($json_data) as $key => $item) {
      $nd = $item['NoiDung'];
      $nd = str_replace("/Upload", "http://phutho.gov.vn/Upload", $nd);
      $old_post[$key]['TieuDe'] = $item['TieuDe'];
      $old_post[$key]['MoTa'] = $item['MoTa'];
      $old_post[$key]['NoiDung'] = $nd;
      $old_post[$key]['TacGia'] = $item['TacGia'];
      $old_post[$key]['NgayTao'] = $item['NgayTao'];
      $old_post[$key]['AnhDaiDien'] = $this->getFile($item['AnhDaiDien']);
      $old_post[$key]['ChuyenMuc'] = $item['IDChuyenMuc'];
      $old_post[$key]['LoaiTin'] = $this->getLoaiTin($item['LoaiTin']);
      $old_post[$key]['NgayXuatBan'] = $item['NgayXuatBan'];
    }

    return $old_post;
  }

  public function listCrawler($filename) {
    $strJsonFileContents = file_get_contents($filename);
    $json_data = json_decode($strJsonFileContents,true);
    $old_post = [];
    foreach (array_reverse($json_data) as $key => $item) {
      $old_post[$key]['TieuDe'] = $item['TieuDe'];
      $old_post[$key]['MoTa'] = $item['MoTa'];
      $old_post[$key]['LienKet'] = $item['LienKet'];
      $old_post[$key]['NgayTao'] = $item['ThoiGianCrawl'];
      $anh_dai_dien = str_replace("/Upload", "http://phutho.gov.vn/Upload", $item['LinkAnhDaiDien']);
      $old_post[$key]['AnhDaiDien'] = $anh_dai_dien;
      $id_chu_de = str_replace(",", "", $item['IDs_ChuDe']);
      $old_post[$key]['ChuyenMuc'] = $id_chu_de;
      $old_post[$key]['NgayXuatBan'] = $item['NgayXuatBan'];
    }

    return $old_post;
  }

  public function listHoiDap($filename) {
    $strJsonFileContents = file_get_contents($filename);
    $json_data = json_decode($strJsonFileContents,true);
    $old_post = [];
    foreach (array_reverse($json_data) as $key => $item) {
      $old_post[$key]['HoTen'] = $item['HoTen'];
      $old_post[$key]['Email'] = $item['Email'];
      $old_post[$key]['DienThoai'] = $item['DienThoai'];
      $old_post[$key]['DiaChi'] = $item['DiaChi'];
      $old_post[$key]['CauHoi'] = $item['CauHoi'];
      $old_post[$key]['DonVi'] = $item['DonViTraLoi'];;
      $old_post[$key]['ThoiGianTao'] = $item['ThoiGianTao'];
      $old_post[$key]['ThoiGianXuatBan'] = $item['ThoiGianXuatBan'];
      $old_post[$key]['TieuDe'] = $item['TieuDe'];
      $old_post[$key]['ThoiGianPhanCong'] = $item['thoigianphancong'];
      $old_post[$key]['TraLoi'] = $item['TraLoi'];
    }

    return $old_post;
  }

  public function listThuTucHanhChinh() {
    $strJsonFileContents = file_get_contents('migration_db/BoTTHC.json');
    $json_data = json_decode($strJsonFileContents,true);
    $old_post = [];

    foreach (array_reverse($json_data) as $key => $item) {
      $old_post[$key]['ten_thu_tuc'] = $item['TenThuTucHanhChinh'];
      $old_post[$key]['created'] = $item['ThoiGianXuatBan'];
      $old_post[$key]['trinh_tu_thuc_hien'] = $item['TrinhTuThucHien'];
      $old_post[$key]['cach_thuc_thuc_hien'] = $item['CachThucThucHien'];
      $old_post[$key]['tp_ho_so'] = $item['ThanhPhanSoLuongHoSo'];
      $old_post[$key]['thoi_han_giai_quyet'] = $item['ThoiHanGiaiQuyet'];
      $old_post[$key]['doi_tuong_thuc_hien'] = $item['DoiTuongThucHien'];
      $old_post[$key]['ket_qua_thuc_hien'] = $item['KetQuaThucHien'];
      $old_post[$key]['chi_phi'] = $item['ChiPhi'];
      $old_post[$key]['mau_to_khai'] = $item['MauDonMauToKhai'];
      $old_post[$key]['yeu_cau'] = $item['YeuCau'];
      $old_post[$key]['co_so_phap_ly'] = $item['CoSoPhapLy'];
      $old_post[$key]['co_quan'] = $item['ID_CoQuan'];
      $old_post[$key]['linh_vuc'] = $this->getLinhVuc($item['ID_LinhVuc']);

      $id_files = $item['FileDinhKem'];
      $id_files = str_replace(",,", "", $id_files);
      $id_files = explode(",", $id_files);

      $files = [];
      if (!empty($id_files)) {
        foreach ($id_files as $file) {
          $file_item = $this->getFile($file);
          if (!empty($file_item)) {
            array_push($files, $file_item);
          }
        }
      }
      $old_post[$key]['file_dinh_kem'] = $files;

    }

    return $old_post;
  }

  public function albumVideo() {
    $strJsonFileContents = file_get_contents('migrations/Album_Video.json');
    $json_data = json_decode($strJsonFileContents,true);
    $old_post = [];

    foreach (array_reverse($json_data) as $key => $item) {
      $old_post[$key]['tieu_de'] = $item['TieuDe'];
      $old_post[$key]['mo_ta'] = $item['MoTa'];
      $old_post[$key]['created'] = $item['ThoiGianTao'];
      $old_post[$key]['tac_gia'] = $item['TacGia'];
      $old_post[$key]['chuyen_muc'] = $item['IDChuyenMucAlbumVideo'];
      $old_post[$key]['link_old_video'] = $item['LinkVideo'];
      $old_post[$key]['anh_dai_dien'] = $item['AnhDaiDien'];
    }

    return $old_post;
  }

  public function albumAnh() {
    $strJsonFileContents = file_get_contents('migrations/Album_Anh.json');
    $json_data = json_decode($strJsonFileContents,true);
    $old_post = [];

    foreach (array_reverse($json_data) as $key => $item) {
      $old_post[$key]['tieu_de'] = $item['TieuDe'];
      $old_post[$key]['mo_ta'] = $item['MoTa'];
      $old_post[$key]['created'] = $item['ThoiGianTao'];
      $old_post[$key]['tac_gia'] = $item['TacGia'];
      $old_post[$key]['chuyen_muc'] = $item['IDChuyenMucAlbumAnh'];
      $old_post[$key]['id_album_anh'] = $this->getFileAlbumAnh($item['ID']);
    }

    return $old_post;
  }

  public function getTidByChuyenTrang($tid) {
    $storage_tid = array(
      "126" => "439",
      "127" => "440"
    );

    foreach ($storage_tid as $k => $val) {
      if ($tid == $k) {
        return $val;
      }
    }
  }

  private function getChuyenMuc($id_cm) {
    $strJsonFileContents = file_get_contents("migrations/ChuyenMuc.json");
    $json_data = json_decode($strJsonFileContents,true);

    $old_chuyen_muc = '';
    foreach ($json_data as $key => $item) {
      if ($item['ID'] == $id_cm) {
        $old_chuyen_muc = $item['TenChuyenMuc'];
      }
    }

    return $old_chuyen_muc;
  }

  private function getCoQuan($id_co_quan) {
    $strJsonFileContents = file_get_contents("migrations/CoQuan.json");
    $json_data = json_decode($strJsonFileContents,true);

    $old_co_quan = '';
    foreach ($json_data as $key => $item) {
      if ($item['ID'] == $id_co_quan) {
        $old_co_quan = $item['TenCoQuan'];
      }
    }

    return $old_co_quan;
  }

  private function getLinhVuc($id_lv) {
    $strJsonFileContents = file_get_contents("migration_db/LinhVuc.json");
    $json_data = json_decode($strJsonFileContents,true);

    $old_lv = '';
    foreach ($json_data as $key => $item) {
      if ($item['ID'] == $id_lv) {
        $old_lv = $item['TenLinhVuc'];
      }
    }

    return $old_lv;
  }

  private function getFile($id_file) {
    $strJsonFileContents = file_get_contents("migration_db/Files.json");
    $json_data = json_decode($strJsonFileContents,true);

    $old_file_name = [];
    foreach ($json_data as $key => $item) {
      if ($item['ID'] == $id_file) {
        $old_file_name['LinkLuuTru'] = str_replace("/Upload", "http://oldpt.phutho.gov.vn/Upload", $item['LinkLuuTru']);
        $old_file_name['Ten'] = $item['Ten'];
      }

    }

    return $old_file_name;
  }

  private function getFileAlbumAnh($id_file) {
    $strJsonFileContents = file_get_contents("migrations/ABL_Anh.json");
    $json_data = json_decode($strJsonFileContents,true);

    $old_file_name = [];
    foreach ($json_data as $key => $item) {
      if ($item['IDAlbumAnh'] == $id_file) {
        $old_file_name[$key]['link_anh'] = str_replace("/Upload", "http://phutho.gov.vn/Upload", $item['LinkAnh']);
        $old_file_name[$key]['caption_image'] = $item['MoTa'];
        $old_file_name[$key]['thu_tu'] = $item['ThuTu'];
      }

    }

    return $old_file_name;
  }

  private function getLoaiTin($id_loai_tin) {
    $strJsonFileContents = file_get_contents("migration_db/LoaiTin.json");
    $json_data = json_decode($strJsonFileContents,true);

    $old_loai_tin = '';
    foreach ($json_data as $key => $item) {
      if ($item['ID'] == $id_loai_tin) {
        $old_loai_tin = $item['TenLoaiTin'];
      }

    }

    return $old_loai_tin;
  }

  public function drupal_add_existing_file($file_drupal_path, $uid = 1, $status = FILE_STATUS_PERMANENT) {
    // check first if the file exists for the uri
    $files = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->loadByProperties(['uri' => $file_drupal_path]);
    $file = reset($files);

    // if not create a file
    if (!$file) {
      $file = File::create([
        'uri' => $file_drupal_path,
      ]);
      $file->save();
    }

    return $file;
  }

  public function getTidByName($name = NULL, $vid = NULL) {
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

  public function getAllNodes() {
    $nids = \Drupal::entityQuery('node')
          ->condition('type',array('page', 'article'), 'IN')
          ->execute();
        return $nids;
  }
}
