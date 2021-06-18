$('.slider_foto_block').slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 1,
        adaptiveHeight: true,
        appendArrows: $('.arrows_foto'),
        prevArrow: '<button type = "button" class = "slick-prev"><img class="img-fluid" src="img/arrow_left.svg"></ button>',
        nextArrow: '<button type = "button" class = "slick-next"><img class="img-fluid" src="img/arrow_right.svg"></ button>',
        responsive: [
          {
            breakpoint: 991,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 767,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });