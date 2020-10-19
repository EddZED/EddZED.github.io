$('#comm_slider').slick({
      dots: false,
      infinite: true,
      speed: 300,
      slidesToShow: 1,
      centerMode: true,
      slidesToScroll: 1,
      centerPadding:'200px',
      prevArrow:'<button type = "button" class = "slick_prev"><i class="fas fa-chevron-left"></i></ button>',
      nextArrow:'<button type = "button" class = "slick_next"><i class="fas fa-chevron-right"></i></ button>',
      appendArrows:$('.slide_comment'),
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: true,
            dots: false
          }
        },
        {
          breakpoint: 991,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            centerPadding:'10px',
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