$('.slider_wrapper').slick({
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
          breakpoint: 1200,
          settings: {
            slidesToShow: 3,
            sldiesPerRow: 1,
            rows: 1
          }
        },
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 2,
            sldiesPerRow: 2,
            rows: 2
          }
        }
      ]
    });
    $('.slider_wrapper').slick('setPosition');