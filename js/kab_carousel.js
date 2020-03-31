$('#slider_kab').slick({
      dots: false,
      arrows: true,
      infinite: true,
      speed: 300,
      slidesToShow: 1,
      centerMode: true,
      slidesToScroll: 1,
      centerPadding:'10px',
      fade:true,
      appendArrows: '.jak',
      prevArrow: '<button type = "button" class = "slick-prev prev_kub"></ button>',
      nextArrow: '<button type = "button" class = "slick-next next_kub"></ button>',
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