(function ($, Drupal) {
  function rateCoQuan(donVi, level) {
    var data = {
      don_vi: donVi,
      level: level
    };

    $.ajax({
      method: 'POST',
      url: '/cttdt-rates/save-rate',
      data: data,
      dataType: "json",
      success: function (data) {
        if (data == 'true') {
          $('.message-success').remove();
          $('.co-quan__danh-gia-'+donVi).after('<span class="c-red message-success my-1">'+Drupal.t("Cảm ơn bạn đã đánh giá")+'</span>')
        }
        else {
          alert(Drupal.t("Lỗi!. Không thể lưu ý kiến"));
        }
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });
  }

  function rateResult(donVi) {
    var dataPack1 = 0;
    var dataPack2 = 0;
    var dataPack3 = 0;
    var dataPack4 = 0;
    var dataPack5 = 0;

    var data = {
      don_vi: donVi
    };

    $.ajax({
      method: 'POST',
      url: '/cttdt-rates/rate-result',
      data: data,
      dataType: "json",
      success: function (data) {
        var obj = Object.keys(data).length;
        if (obj > 0) {
          if (obj > 0) {
            var totalRate = data['rat_hai_long'] + data['hai_long'] + data['chap_nhan_duoc'] + data['k_hai_long'] + data['k_the_chap_nhan'];

            if (totalRate > 0) {
              dataPack1 = (data['rat_hai_long'] * 100) / totalRate;
              dataPack2 = (data['hai_long'] * 100) / totalRate;
              dataPack3 = (data['chap_nhan_duoc'] * 100) / totalRate;
              dataPack4 = (data['k_hai_long'] * 100) / totalRate;
              dataPack5 = (data['k_the_chap_nhan'] * 100) / totalRate;
            }

            var ctx = document.getElementById("bar-chart-rate-"+donVi);
            ctx = ctx.getContext("2d");
            var chartRate = new Chart(ctx, {
              type: 'bar',
              data: {
                labels: [Drupal.t('Rất hài lòng'), Drupal.t('Hài lòng'), Drupal.t('Chấp nhận được'), Drupal.t('Không hài lòng'), Drupal.t('Không thể chấp nhận được')],
                datasets: [{
                  label: '',
                  data: [dataPack1, dataPack2, dataPack3, dataPack4, dataPack5],
                  backgroundColor: [
                    'rgb(144, 237, 125)',
                    'rgb(247, 163, 92)',
                    '#3575BB',
                    '#de0403',
                    'rgb(253, 236, 109)'
                  ],
                  borderWidth: 0
                }]
              },
              options: {
                animation: {
                  duration: 10,
                },
                tooltips: {
                  mode: 'label',
                  callbacks: {
                    label: function(tooltipItem, data) {
                      return data.datasets[tooltipItem.datasetIndex].label + ": " + tooltipItem.yLabel + '% bình chọn';
                    }
                  }
                },
                legend: {display: false}
              }
            });
          }
        }

      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });
  }

  Drupal.behaviors.rateCoQuan = {
    attach: function (context, settings) {
      $('.btn--y-kien').once('y_kien_click').on('click', function () {
        var idDonVi = $(this).val();
        var elmentMucDo = $('.co-quan__danh-gia-'+idDonVi).find('.form-check');

        var mucDo = '';
        elmentMucDo.each(function () {
          var level = $(this).find('.form-check-input');
          if ($(this).find('.form-check-input').prop("checked")) {
            mucDo = level.val();
          }
        });

        if (mucDo !== '') {
          rateCoQuan(idDonVi, mucDo);
        } else {
          alert(Drupal.t("Phải chọn ít nhất 1 phương án !"));
        }
      });

      $('.btn--ket-qua').once('ket_qua_click').on('click', function () {
        var idDonVi = $(this).val();
        setTimeout(function () {
          rateResult(idDonVi);
        }, 500)
      });
    }
  };
})(jQuery, Drupal);
