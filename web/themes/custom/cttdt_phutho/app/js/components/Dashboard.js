(function ($, Drupal) {

  function intButtonModeration() {
    if ($('.field--name-moderation-state input[type="hidden"]').val() == 'draft') {
      $('.field--name-moderation-state button[value="Tin chờ biên tập 2"]').text('Phê duyệt');
      $('.field--name-moderation-state button[value="Tin chờ biên tập 1"]').text('Phê duyệt');
      $('.field--name-moderation-state button[value="Tin chờ duyệt"]').text('Phê duyệt');
    }

    if ($('.field--name-moderation-state button:disabled').val() == 'Tin nháp') {
      $('.field--name-moderation-state button[value="Tin chờ biên tập 1"]').text('Phê duyệt');
      $('.field--name-moderation-state button[value="Tin chờ duyệt"]').text('Phê duyệt');
    }

    if ($('.field--name-moderation-state input[type="hidden"]').val() == 'tra_lai') {
      $('.field--name-moderation-state button[value="Tin chờ biên tập 1"]').text('Phê duyệt');
    }

    if ($('.field--name-moderation-state input[type="hidden"]').val() == 'tra_lai_bt_1') {
      $('.field--name-moderation-state button[value="Tin chờ biên tập 2"]').text('Phê duyệt');
      $('.field--name-moderation-state button[value="Trả lại"]').text('Từ chối');
    }

    if ($('.field--name-moderation-state input[type="hidden"]').val() == 'tra_lai_bt_2') {
      $('.field--name-moderation-state button[value="Tin chờ duyệt"]').text('Phê duyệt');
      $('.field--name-moderation-state button[value="Trả lại BT 1"]').text('Từ chối');
    }

    if ($('.field--name-moderation-state input[type="hidden"]').val() == 'tin_cho_bien_tap_1') {
      $('.field--name-moderation-state button[value="Tin chờ biên tập 2"]').text('Phê duyệt');
      $('.field--name-moderation-state button[value="Trả lại"]').text('Từ chối');
    }

    if ($('.field--name-moderation-state button:disabled').val() == 'Tin chờ biên tập 2') {
      $('.field--name-moderation-state button[value="Tin chờ duyệt"]').text('Phê duyệt');
      $('.field--name-moderation-state button[value="Trả lại BT 1"]').text('Từ chối');
    }

    if ($('.field--name-moderation-state button:disabled').val() == 'Tin chờ duyệt') {
      $('.field--name-moderation-state button[value="Tin đã duyệt"]').text('Phê duyệt');
      $('.field--name-moderation-state button[value="Trả lại BT 2"]').text('Từ chối');
    }

    if ($('.field--name-moderation-state button:disabled').val() == 'Tin đã duyệt') {
      $('.field--name-moderation-state button[value="Tin chờ duyệt"]').text('Từ chối');
    }
  }

  function afterBuildModeration() {

    if ($('.field--name-moderation-state button:nth-of-type(2):disabled').val() == 'Tin chờ biên tập 1'
      || $('.field--name-moderation-state button:nth-of-type(1):disabled').val() == 'Tin chờ biên tập 1') {

      $('.field--name-moderation-state button[value="Tin chờ biên tập 1"]').text('Phê duyệt');
      $('.field--name-moderation-state button[value="Tin chờ biên tập 1"]').show();
      $('.field--name-moderation-state button[value="Trả lại"]').hide();
    }

    if ($('.field--name-moderation-state button:nth-of-type(2):disabled').val() == 'Tin chờ biên tập 2'
      || $('.field--name-moderation-state button:nth-of-type(3):disabled').val() == 'Trả lại'
      || $('.field--name-moderation-state button:nth-of-type(1):disabled').val() == 'Tin chờ biên tập 2'
      || $('.field--name-moderation-state button:nth-of-type(2):disabled').val() == 'Trả lại') {
      $('.field--name-moderation-state button[value="Tin chờ biên tập 1"]').hide();
      $('.page-lanh_dao_phong_cong_ttdt .field--name-moderation-state button[value="Trả lại BT 1"]').hide();
      $('.page-lanh_dao_phong_cong_ttdt .field--name-moderation-state button[value="Tin chờ biên tập 2"]').text('Phê duyệt');
      $('.page-lanh_dao_phong_cong_ttdt .field--name-moderation-state button[value="Tin chờ biên tập 2"]').show();
      $('.page-lanh_dao_phong_cong_ttdt .field--name-moderation-state button[value="Trả lại"]').text('Từ chối');
      $('.page-lanh_dao_phong_cong_ttdt .field--name-moderation-state button[value="Trả lại"]').show();
    }

    if ($('.field--name-moderation-state button:nth-of-type(2):disabled').val() == 'Tin chờ duyệt'
      || $('.field--name-moderation-state button:nth-of-type(3):disabled').val() == 'Trả lại BT 1'
      || $('.field--name-moderation-state button:nth-of-type(1):disabled').val() == 'Tin chờ duyệt') {
      $('.field--name-moderation-state button[value="Tin chờ biên tập 2"]').hide();
      $('.field--name-moderation-state button[value="Tin nháp"]').hide();
      $('.page-pgd_tt_phu_trach_cong_ttdt .field--name-moderation-state button[value="Trả lại BT 2').hide();
      $('.page-pgd_tt_phu_trach_cong_ttdt .field--name-moderation-state button[value="Tin chờ duyệt"]').text('Phê duyệt');
      $('.page-pgd_tt_phu_trach_cong_ttdt .field--name-moderation-state button[value="Tin chờ duyệt"]').show();
      $('.page-pgd_tt_phu_trach_cong_ttdt .field--name-moderation-state button[value="Trả lại BT 1"]').text('Từ chối');
      $('.page-pgd_tt_phu_trach_cong_ttdt .field--name-moderation-state button[value="Trả lại BT 1').show();
    }

    if ($('.field--name-moderation-state button:nth-of-type(1):disabled').val() == 'Tin đã duyệt' || $('.field--name-moderation-state button:nth-of-type(3):disabled').val() == 'Trả lại BT 2') {
      $('.field--name-moderation-state button[value="Tin chờ duyệt"]').hide();
      $('.field--name-moderation-state button[value="Tin đã duyệt"]').text('Phê duyệt');
      $('.field--name-moderation-state button[value="Tin đã duyệt"]').show();
      $('.field--name-moderation-state button[value="Trả lại BT 2"]').text('Từ chối');
      $('.field--name-moderation-state button[value="Trả lại BT 2"]').show();
    }

    if ($('.field--name-moderation-state button:nth-of-type(2):disabled').val() == 'Xuất bản' || $('.field--name-moderation-state button:nth-of-type(3):disabled').val() == 'Tin chờ duyệt') {
      $('.page-truong_bbt .field--name-moderation-state button[value="Tin đã duyệt"]').hide();
      $('.page-truong_bbt .field--name-moderation-state button[value="Xuất bản"]').show();
      $('.page-truong_bbt .field--name-moderation-state button[value="Tin chờ duyệt"]').text('Từ chối');
      $('.page-truong_bbt .field--name-moderation-state button[value="Tin chờ duyệt"]').show();
    }
  }

  function fontPlus() {
    var divTemp = document.getElementById('post__summary');
    if (divTemp.style.fontSize == '') divTemp.style.fontSize = '14px';
    var s = divTemp.style.fontSize;
    if ((s.indexOf('px') > -1) && (s.indexOf('px') == (s.length - 2))) s = s.substring(0, s.indexOf('px'));
    divTemp.style.fontSize = (parseFloat(s) + 1) + 'px';
    divTemp = document.getElementById('post__content');
    if (divTemp.style.fontSize == '') divTemp.style.fontSize = '14px';
    s = divTemp.style.fontSize;
    if ((s.indexOf('px') > -1) && (s.indexOf('px') == (s.length - 2))) s = s.substring(0, s.indexOf('px'));
    divTemp.style.fontSize = (parseFloat(s) + 1) + 'px';
  }

  function fontMinus() {
    var divTemp = document.getElementById('post__summary');
    if (divTemp.style.fontSize == '') divTemp.style.fontSize = '14px';
    var s = divTemp.style.fontSize;
    if ((s.indexOf('px') > -1) && (s.indexOf('px') == (s.length - 2))) s = s.substring(0, s.indexOf('px'));
    divTemp.style.fontSize = (parseFloat(s) - 1) + 'px';
    divTemp = document.getElementById('post__content');
    if (divTemp.style.fontSize == '') divTemp.style.fontSize = '14px';
    s = divTemp.style.fontSize;
    if ((s.indexOf('px') > -1) && (s.indexOf('px') == (s.length - 2))) s = s.substring(0, s.indexOf('px'));
    divTemp.style.fontSize = (parseFloat(s) - 1) + 'px';
  }

  function searchPosts() {
    $('#search-block-form input.form-search').once('keyup_input').on('keyup', function (e) {
      if (e.keyCode === 13) {
        $('#search-block-form').submit();
      }
    });

    $('#search-block-form .js-form-submit').once('submit_click').on('click', function (e) {
      $('#search-block-form').submit();
    });
  }

  Drupal.behaviors.dashboard = {
    attach: function (context, settings) {
      var MODERATION = ['Tin nháp', 'Tin chờ biên tập 1', 'Tin chờ biên tập 2', 'Tin chờ duyệt', 'Tin đã duyệt', 'Xuất bản', 'Trả lại', 'Trả lại BT 1', 'Trả lại BT 2', 'Phân công'];

      $(".menu-dashboard li").once('menu_dashboard').each(function() {
        if ((window.location.pathname.indexOf($(this).find('a').attr('href'))) > -1) {
          $(this).addClass('active');
        }
      });

      $('.field--name-moderation-state button:disabled').hide();

      $( document ).once('AjaxComplete').ajaxComplete(function(event, xhr, settings) {
        if (typeof settings.extraData !== "undefined") {
          var currentModeration = settings.extraData._triggering_element_value;
          if ($.inArray(currentModeration, MODERATION) != '-1') {
            afterBuildModeration();
            setTimeout(function () {
              $('.node-form #edit-submit').trigger("click");
            }, 1000);
          }
        }
      });

      //Set moderation state
      intButtonModeration();

      var formArticle = $('.node-article-edit-form, .node-article-form, .node-hoi-dap-edit-form, .node-hoi-dap-form, .node-hoi-dap-doanh-nghiep-edit-form, .node-hoi-dap-doanh-nghiep-form');
      var widthAction = formArticle.find('.form-actions').width();
      $('.entity-content-form-footer').once('width_action').css('left', widthAction + 'px');

      $('.btn-print-tk-ct').once('tk_ct_click').on('click', function () {

        $('.table-tk').print({
          globalStyles: true,
          mediaPrint: false,
          stylesheet: null,
          noPrintSelector: ".no-print",
          iframe: true,
          append: null,
          prepend: null,
          manuallyCopyFormValues: true,
          deferred: $.Deferred(),
          timeout: 750,
          title: null,
          doctype: '<!doctype html>'
        });
      });

      $('.print-page').once('print_page_click').on('click', function () {
        $('article').print({
          globalStyles: true,
          mediaPrint: false,
          stylesheet: null,
          noPrintSelector: ".social-media-share-block, .head-danh-muc",
          iframe: true,
          append: null,
          prepend: null,
          manuallyCopyFormValues: true,
          deferred: $.Deferred(),
          timeout: 750,
          title: null,
          doctype: '<!doctype html>'
        });
      });

      $(".slick-slider-qc").not('.slick-initialized').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3,
              infinite: true,
              dots: true
            }
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 480,
            settings: {
              arrows: false,
              slidesToShow: 2,
              slidesToScroll: 1
            }
          }
        ]
      });

      $('#views-exposed-form-danh-sach-lay-y-kien-du-thao-van-ban-page-2 select option,' +
        '#views-exposed-form-danh-sach-y-kien-dong-gop-du-thao-van-ban-page-1 select option,' +
        '#views-exposed-form-danh-sach-cau-hoi-dap-page-ds-hoi-dap .js-form-item-status select option,' +
        '#views-exposed-form-danh-sach-cau-hoi-dap-page-ds-hoi-dap-dn .js-form-item-status select option').once('loop_status').each(function () {
        if ($(this).val() == 1) {
          $(this).text(Drupal.t('Đã xuất bản'));
        } else if ($(this).val() == 0) {
          $(this).text(Drupal.t('Chưa xuất bản'));
        }
      });

      $('#views-exposed-form-danh-sach-y-kien-dong-gop-du-thao-van-ban-page-1 .js-form-type-textfield input').once('add_label').before('<label>'+Drupal.t("Tiêu đề")+'</label>')

      $('#edit-phan-cong-tra-loi optgroup').once('loop_phan_cong').each(function () {
        $(this).remove();
      });

      $('#edit-phan-cong-tra-loi').append('<option value="hoi_dap-draft">'+Drupal.t("Chưa phân công")+'</option>' +
        '<option value="hoi_dap-phan_cong">'+Drupal.t("Đã phân công")+'</option>');

      $('.sub-menu').once('toggles_submenu').on('click', function () {
        $(this).toggleClass('active');
        $(this).find('ul').slideToggle("slow");
      });

      $('.increase-font').once('increase_font_click').on('click', function () {
        fontPlus();
      });
      $('.decrease-font').once('decrease_font_click').on('click', function () {
        fontMinus();
      });

      // Search form
      searchPosts();
    }
  };
})(jQuery, Drupal);
