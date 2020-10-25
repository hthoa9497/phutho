jQuery(document).ready(function ($) {
  $('#hoidap-traloi-form, #hoidap-traloi-dn-form').on('submit', function(e){
    e.preventDefault();
    var traLoi = CKEDITOR.instances['edit-tra-loi-value'].getData();
    var pathname = window.location.pathname;
    pathname = pathname.split('/');
    var id = pathname.pop() || pathname.pop();
    var fileDkTraLoi = $('.form-item-file-dinh-kem-tra-loi .js-form-managed-file a').attr('href');
    var datas = {
      id: id,
      tra_loi: traLoi,
      file_dk_tl: fileDkTraLoi
    };

    $.ajax({
      method: 'GET',
      url: '/hoi-dap-info',
      dataType: "json",
      success: function (data) {
        traLoiCauHoi(datas, data['cong_address']);
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });
  });

  $('.btn-tra-loi').on('click', function(e){
    e.preventDefault();
    var traLoi = CKEDITOR.instances['edit-tra-loi-value'].getData();
    var pathname = window.location.pathname;
    pathname = pathname.split('/');
    var id = pathname.pop() || pathname.pop();
    var fileDkTraLoi = $('.form-item-file-dinh-kem-tra-loi .js-form-managed-file a').attr('href');

    var datas = {
      id: id,
      tra_loi: traLoi,
      moderation: 'tra_loi',
      file_dk_tl: fileDkTraLoi
    };

    $.ajax({
      method: 'GET',
      url: '/hoi-dap-info',
      dataType: "json",
      success: function (data) {
        traLoiCauHoi(datas, data['cong_address']);
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });


  });


  $.ajax({
    method: 'GET',
    url: '/hoi-dap-info',
    dataType: "json",
    success: function (data) {
      getCauHoiChuaTraLoi(data['don_vi'], data['cong_address']);
      getCauHoiChuaTraLoiDN(data['don_vi'], data['cong_address']);
      getListCauHoi(data['don_vi'], data['cong_address']);
      getListCauHoiDN(data['don_vi'], data['cong_address']);
      getBaoCao(data['don_vi'], data['cong_address']);
    },
    error: function (xhr, status, error) {
      console.log(error);
    }
  });


  function traLoiCauHoi(data, congAddress) {
    $.ajax({
      method: 'POST',
      url: 'https://'+congAddress+'/tra-loi',
      data: data,
      dataType: "json",
      success: function (data) {
        $("html, body").animate({ scrollTop: 0 }, "slow");
        $('.alert-success').removeClass('d-none');
      },
      error: function (xhr, status, error) {
        $('.alert-danger').removeClass('d-none');
        console.log(error);
      }
    });
  }

  function getCauHoiChuaTraLoi(donVi, congAddress) {
    var data = {
      don_vi: donVi
    };
    $.ajax({
      method: 'GET',
      url: 'https://'+congAddress+'/hoi-dap',
      data: data,
      dataType: "json",
      success: function (data) {
        var obj = Object.keys(data).length;
        if (obj > 0) {
          for (var i = 0; i < obj; i++) {
            $('.table-cau-hoi-chua-tl-cd tbody').append('<tr>' +
              '<td>'+(i+1)+'</td>' +
              '<td>'+data[i]["cau_hoi"]+'</td>' +
              '<td>'+data[i]["ngay_tao"]+'</td>' +
              '<td>'+data[i]["thoi_gian_tra_loi"]+'</td>' +
              '<td><a class="btn btn-info" href="/hoi-dap-tra-loi/'+data[i]["id"]+'">'+Drupal.t("Trả lời")+'</a></td>' +
              '</tr>')
          }
        }
        else {
          $('.table-cau-hoi-chua-tl-cd').after('<span class="d-block text-align-center">'+Drupal.t("Không tìm thấy câu hỏi")+'</span>');
        }
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });
  }

  function getCauHoiChuaTraLoiDN(donVi, congAddress) {
    var data = {
      don_vi: donVi
    };
    $.ajax({
      method: 'GET',
      url: 'https://'+congAddress+'/hoi-dap-dn-chua-tra-loi',
      data: data,
      dataType: "json",
      success: function (data) {
        var obj = Object.keys(data).length;
        console.log(obj);
        if (obj > 0) {
          for (var i = 0; i < obj; i++) {
            $('.table-cau-hoi-chua-tl-dn tbody').append('<tr>' +
              '<td>'+(i+1)+'</td>' +
              '<td>'+data[i]["cau_hoi"]+'</td>' +
              '<td>'+data[i]["ngay_tao"]+'</td>' +
              '<td>'+data[i]["thoi_gian_tra_loi"]+'</td>' +
              '<td><a class="btn btn-info" href="/hoi-dap-tra-loi-dn/'+data[i]["id"]+'">'+Drupal.t("Trả lời")+'</a></td>' +
              '</tr>')
          }
        }
        else {
          $('.table-cau-hoi-chua-tl-dn').after('<span class="d-block text-align-center">'+Drupal.t("Không tìm thấy câu hỏi")+'</span>');
        }
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });
  }

  function getListCauHoi(donVi, congAddress) {
    var data = {
      don_vi: donVi
    };
    $.ajax({
      method: 'GET',
      url: 'https://'+congAddress+'/ds-hoi-dap',
      data: data,
      dataType: "json",
      success: function (data) {
        var obj = Object.keys(data).length;
        if (obj > 0) {
          for (var i = 0; i < obj; i++) {
            var link = '';
            if (data[i]["moderation"] == 'phan_cong') {
              link = '<a class="btn btn-info" href="/hoi-dap-tra-loi/'+data[i]["id"]+'">'+Drupal.t("Trả lời")+'</a>';
            }
            $('.table-ds-cau-hoi-cd tbody').append('<tr>' +
              '<td>'+(i+1)+'</td>' +
              '<td>'+data[i]["cau_hoi"]+'</td>' +
              '<td>'+data[i]["ngay_tao"]+'</td>' +
              '<td>'+data[i]["thoi_gian_tra_loi"]+'</td>' +
              '<td>'+data[i]["status"]+'</td>' +
              '<td>'+link+'</td>' +
              '</tr>')
          }
        }
        else {
          $('.table-ds-cau-hoi-cd').after('<span class="d-block text-align-center">'+Drupal.t("Không tìm thấy câu hỏi")+'</span>');
        }
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });
  }

  function getListCauHoiDN(donVi, congAddress) {
    var data = {
      don_vi: donVi
    };
    $.ajax({
      method: 'GET',
      url: 'https://'+congAddress+'/ds-hoi-dap-dn',
      data: data,
      dataType: "json",
      success: function (data) {
        var obj = Object.keys(data).length;
        if (obj > 0) {
          for (var i = 0; i < obj; i++) {
            var link = '';
            if (data[i]["moderation"] == 'phan_cong') {
              link = '<a class="btn btn-info" href="/hoi-dap-tra-loi-dn/'+data[i]["id"]+'">'+Drupal.t("Trả lời")+'</a>';
            }
            $('.table-ds-cau-hoi-dn tbody').append('<tr>' +
              '<td>'+(i+1)+'</td>' +
              '<td>'+data[i]["tieu_de"]+'</td>' +
              '<td>'+data[i]["ngay_tao"]+'</td>' +
              '<td>'+data[i]["thoi_gian_tra_loi"]+'</td>' +
              '<td>'+data[i]["status"]+'</td>' +
              '<td>'+link+'</td>' +
              '</tr>')
          }
        }
        else {
          $('.table-ds-cau-hoi-dn').after('<span class="d-block text-align-center">'+Drupal.t("Không tìm thấy câu hỏi")+'</span>');
        }
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });
  }

  function getBaoCao(donVi, congAddress) {
    var data = {
      don_vi: donVi
    };
    $.ajax({
      method: 'GET',
      url: 'https://'+congAddress+'/bao-cao-by-dv',
      data: data,
      dataType: "json",
      success: function (data) {
        var obj = Object.keys(data).length;
        var res = $.map(data, function(value, index) {
          return [value];
        });
        if (obj > 0) {
          $('.table-bao-cao tbody').append('<tr>' +
            '<td class="text-align-center">'+res[0]["total_questions"]+'</td>' +
            '<td class="text-align-center">'+res[0]["total_answered"]+'</td>' +
            '<td class="text-align-center">'+res[0]["not_answered"]+'</td>' +
            '<td class="text-align-center">'+res[0]["delay_answer"]+'</td>' +
            '</tr>')
        }
        else {
          $('.table-bao-cao').after('<span class="d-block text-align-center">'+Drupal.t("Không tìm thấy dữ liệu")+'</span>');
        }
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });
  }
});
