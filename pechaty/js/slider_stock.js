$('.slider_stock_block').slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 2,
        slidesToScroll: 1,
        adaptiveHeight: true,
        appendArrows: $('.arrows_slider'),
        prevArrow: '<button type = "button" id="st_left" class = "slick-prev"><img class="img-fluid" src="img/arrow_left.svg"></ button>',
        nextArrow: '<button type = "button" id="st_right" class = "slick-next"><img class="img-fluid" src="img/arrow_right.svg"></ button>',
        responsive: [
          {
            breakpoint: 767,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });