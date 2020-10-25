(function ($, Drupal) {
  function loadThoiTiet(lat, lon) {
    $.ajax({
      method: 'GET',
      url: 'https://api.openweathermap.org/data/2.5/weather?lat='+lat+'&lon='+lon+'&lang=vi&appid=70de8cc526325e2875f1c4c36c0295da',
      dataType: "json",
      success: function (data) {
        $('.temperature').html(Math.round(data.main.temp - 273.15) + "ºC");
        $('.description-thoi-tiet').html(data.weather[0].description);
        $('.status-weather img').attr('src', 'http://openweathermap.org/img/w/' + data.weather[0].icon + '.png');
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });

    $.ajax({
      method: 'GET',
      url: 'https://api.openweathermap.org/data/2.5/find?lat='+lat+'&lon='+lon+'&cnt=40&lang=vi&appid=70de8cc526325e2875f1c4c36c0295da',
      dataType: "json",
      success: function (data) {
        var minTempCurDay = Math.round(data.list[0].main.temp_min - 273.15);
        var maxTempCurDay = Math.round(data.list[0].main.temp_max - 273.15);
        for (var i = 1; i <= 7; i++) {
          var temp = Math.round(data.list[i].main.temp_min - 273.15);
          if (minTempCurDay > temp) {
            minTempCurDay = temp;
          }
          temp = Math.round(data.list[i].main.temp_max - 273.15);
          if (maxTempCurDay < temp) {
            maxTempCurDay = temp;
          }
        }

        if (minTempCurDay != maxTempCurDay) {
          $('.average-temperature').html("Dự báo 24h: " + minTempCurDay + " - " + maxTempCurDay + "ºC");
        }
        else {
          $('.average-temperature').html("Dự báo 24h: " + minTempCurDay + "ºC");
        }
      },
      error: function (xhr, status, error) {
        console.log(error);
      }
    });
  }

  function videoLibrary() {
    $('.video-library-mobile').not('.slick-initialized').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      autoplay: false,
      autoplaySpeed: 3000,
      arrows: true
    });
    imagesLibrary();
  }

  function imagesLibrary() {
    $('#block-thuvienanhvideoblock li:last-child > a').once('tab_image_click').on('click', function () {
      $('.images-library-mobile').hide();
      setTimeout(function () {
        $('.images-library-mobile').not('.slick-initialized').slick({
          slidesToShow: 2,
          slidesToScroll: 2,
          autoplay: true,
          autoplaySpeed: 3000,
          responsive: [
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
                slidesToScroll: 2
              }
            }
          ]
        });
        $('.images-library-mobile').show();
      }, 1000);
      videoLibrary();
    });
  }

  Drupal.behaviors.header = {
    attach: function (context, settings) {
      $.fn.blindLeftToggle = function (duration, easing, complete) {
        return this.animate({
          marginLeft: parseFloat(this.css('marginLeft')) < 0 ? 0 : -this.outerWidth()
        }, $.speed(duration, easing, complete));
      };

      $('.m-danh-muc').once('toggle_menu_mobile').on('click', function () {
        $('.region-navigation-mobile').slideToggle(500);
      });

      if($(window).width() < 768) {
        $('.mobile-main-menu').css('margin-left', -$('.mobile-main-menu').outerWidth());
      }

      $('.nav-main-menu').once('toggle_main_menu').on('click', function () {
        $('.mobile-main-menu').blindLeftToggle('slow');
      });

      $(window).once('window_click').on('click', function (e) {
        if ($('.mobile-main-menu').css('marginLeft') == '0px' && $(window).width() < 768) {
          $('.mobile-main-menu').blindLeftToggle('slow');
          e.preventDefault();
        }
      });

      $('.mobile-main-menu').once('mobile_main_menu_click').on('click', function (event) {
        event.stopPropagation();
      });

      $('.main-header .user-name').once('username_click').on('click', function () {
        if ($('.main-header .profile-control').hasClass('open')) {
          $('.main-header .profile-control').removeClass('open');
        }
        else {
          $('.main-header .profile-control').addClass('open');
        }
      });

      $(window).once('window_click').on('click', function() {
        if ($('.main-header .profile-control').hasClass('open')) {
          $('.main-header .profile-control').removeClass('open');
        }
      });

      $('.user-logged-info').once('user_info_click').on('click', function(event){
        event.stopPropagation();
      });

      $("#thoi-tiet").once('thoi_tiet_change').on('change', function() {
        var lon = $(this).find(':selected').data('lon');
        var lat = $(this).find(':selected').data('lat');

        loadThoiTiet(lat, lon);
      });

      $(document, context).once('header').each( function() {
        loadThoiTiet('21.32', '105.4');
      });

      // Slider thu vien anh
      $('.slider-thu-vien-anh').not('.slick-initialized').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: false,
        autoplay: true,
        asNavFor: '.slider-nav-thumbnails',
      });

      $('.slider-nav-thumbnails').not('.slick-initialized').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        asNavFor: '.slider-thu-vien-anh',
        dots: false,
        autoplay: true,
        focusOnSelect: true
      });

      $('.slider-nav-thumbnails .slick-slide').removeClass('slick-active');
      $('.slider-nav-thumbnails .slick-slide').eq(0).addClass('slick-active');
      $('.slider-thu-vien-anh').on('beforeChange', function (event, slick, currentSlide, nextSlide) {
        var mySlideNumber = nextSlide;
        $('.slider-nav-thumbnails .slick-slide').removeClass('slick-active');
        $('.slider-nav-thumbnails .slick-slide').eq(mySlideNumber).addClass('slick-active');
      });

      // Slider thu vien anh highlight
      $('.slider-anh-library-highlight').not('.slick-initialized').slick({
        dots: false,
        infinite: true,
        speed: 500,
        fade: true,
        autoplay: true,
        cssEase: 'linear'
      });

      imagesLibrary();
      videoLibrary();
    }
  };
})(jQuery, Drupal);
