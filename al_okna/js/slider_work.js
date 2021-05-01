$('.slider_works').slick({
      slidesToShow: 1,
      sldiesPerRow: 1,
      rows: 1,
      autoplay: false,
      arrows: true,
      infinite: true,
      dots: false,
      variableWidth: true,
      initialSlide: 1,
      centerMode: true,
      //appendArrows: $('.slider_nav'),
      nextArrow: '<button class="slick-next slick-arrow" aria-label="Next" type="button" style=""><img src="img/sl_right.png" class="img-fluid"></button>',
      prevArrow: '<button class="slick-prev slick-arrow" aria-label="Prev" type="button" style=""><img src="img/sl_left.png" class="img-fluid"></button>'
      //responsive: [
      //  {
      //    breakpoint: 1199,
      //    settings: {
      //      slidesToShow: 3,
      //      sldiesPerRow: 1,
      //      rows: 1
      //    }
      //  },
      //  {
      //    breakpoint: 991,
      //    settings: {
      //      slidesToShow: 2,
      //      sldiesPerRow: 2,
      //      rows: 2
      //    }
      //  }
      // ]
    });
    $('.slider_works').slick('setPosition');