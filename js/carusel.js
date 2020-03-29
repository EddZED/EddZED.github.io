$('#slider').slick({
      dots: false,
      arrows: true,
      infinite: false,
      speed: 300,
      slidesToShow: 1,
      centerMode: true,
      slidesToScroll: 1,
      centerPadding:'10px',
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            centerMode: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: false,
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