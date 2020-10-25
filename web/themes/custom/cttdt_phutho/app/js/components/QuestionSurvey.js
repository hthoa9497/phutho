(function ($, Drupal) {
  function getQuestionSurvey() {
    var dataPack1 = 0;
    var dataPack2 = 0;
    var dataPack3 = 0;
    $.ajax({
      method: 'GET',
      url: '/question-survey/result',
      dataType: "json",
      success: function (data) {
        var obj = Object.keys(data).length;
        if (obj > 0) {
          var totalSurvey = data['day_du'] + data['kha_hc'] + data['can_bs'];

          if (totalSurvey > 0) {
            dataPack1 = (data['day_du'] * 100) / totalSurvey;
            dataPack2 = (data['kha_hc'] * 100) / totalSurvey;
            dataPack3 = (data['can_bs'] * 100) / totalSurvey;
          }

          var ctx = document.getElementById("bar-chart-question-survey");
          ctx = ctx.getContext("2d");
          var surveyChart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels: [Drupal.t('Thông tin thủ tục hành chính'), Drupal.t('Thông tin kinh tế - xã hội'), Drupal.t('Thông tin chỉ đạo điều hành')],
              datasets: [{
                label: '',
                data: [dataPack1, dataPack2, dataPack3],
                backgroundColor: [
                  'rgb(144, 237, 125)',
                  'rgb(247, 163, 92)',
                  '#3575BB'
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
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });
  }
  Drupal.behaviors.questionSurvey = {
    attach: function (context, settings) {
      $('.ket-qua-survey').once('result_survey_click').on('click', function () {
        getQuestionSurvey();
      });
    }
  };
})(jQuery, Drupal);
