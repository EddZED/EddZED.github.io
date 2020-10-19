$('#serv_slide').slick({
      dots: false,
      infinite: false,
      speed: 300,
      slidesToShow: 3,
      slidesToScroll: 1,
      prevArrow:'<button type = "button" class = "slick_prev"><i class="fas fa-chevron-left"></i></ button>',
      nextArrow:'<button type = "button" class = "slick_next"><i class="fas fa-chevron-right"></i></ button>',
      centerPadding:'20px',
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
            infinite: true,
            dots: false
          }
        },
        {
          breakpoint: 991,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
            swipe:true
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            swipe:true
          }
        }
      ]
    });