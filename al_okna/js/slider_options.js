 $('.slider_wrapper_two').slick({
      slidesToShow: 4,
      sldiesPerRow: 1,
      rows: 1,
      autoplay: false,
      arrows: true,
      infinite: false,
      dots: false,
      variableWidth: false,
      centerMode: false,
      appendArrows: $('.slider_nav'),
      nextArrow: '<button class="button-next"><img src="img/right_ar.png" class="img-fluid"></button>',
      prevArrow: '<button class="button-prev"><img src="img/left_ar.png" class="img-fluid"></button>',
      responsive: [
        {
          breakpoint: 1199,
          settings: {
            slidesToShow: 3,
            sldiesPerRow: 1,
            rows: 1
          }
        },
        {
          breakpoint: 991,
          settings: {
            slidesToShow: 2,
            sldiesPerRow: 1,
            rows: 2
          }
        },
        {
          breakpoint: 565,
          settings: {
            slidesToShow: 1,
            sldiesPerRow: 1,
            rows: 1
          }
        },
      ]
    });
    $('.slider_wrapper_two').slick('setPosition');