$('#slider_project').slick({
      dots: false,
      infinite: false,
      speed: 300,
      slidesToShow: 1,
      centerMode: false,
      slidesToScroll: 1,
      centerPadding:'0px',
      prevArrow:'<button type = "button" class = "slick_prev"><i class="fas fa-arrow-left"></i></ button>',
      nextArrow:'<button type = "button" class = "slick_next"><i class="fas fa-arrow-right"></i></ button>',
      appendArrows:$('#slider_project'),
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: false
          }
        },
        {
          breakpoint: 991,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            centerPadding:'0px',
            swipe:true
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            centerPadding:'0px',
            swipe:true
          }
        }
      ]
    });