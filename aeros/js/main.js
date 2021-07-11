$(window).scroll(function () {
      var heightW = $(window).scrollTop();
      if (heightW > 60) {
        $('.header').addClass('active');
      } else {
        $('.header').removeClass('active');
      }
    });
