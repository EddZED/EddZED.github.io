$('#slider').slick({
      dots: false,
      arrows: true,
      infinite: true,
      speed: 300,
      slidesToShow: 1,
      centerMode: true,
      slidesToScroll: 1,
      centerPadding:'10px',
      fade:true,
      prevArrow: '<button type = "button" class = "slick-prev"><img src="img/btn_left.png" class="img-fluid"></ button>',
      nextArrow: '<button type = "button" class = "slick-next"><img src="img/btn_right.png" class="img-fluid"></ button>',
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            centerMode: true,
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
            centerPadding:'0px',
            swipe:true
          }
        },
        {
          breakpoint: 767,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: false
          }
        }
      ]
    });