$('.slider_works').slick({
      slidesToShow: 1,
      sldiesPerRow: 1,
      rows: 1,
      autoplay: false,
      arrows: true,
      infinite: true,
      dots: false,
      variableWidth: true,
      initialSlide: 1,
      centerMode: true,
      centerPadding: '33px',
      //appendArrows: $('.slider_nav'),
      nextArrow: '<button class="slick-next slick-arrow" aria-label="Next" type="button" style=""><img src="img/sl_right.png" class="img-fluid"></button>',
      prevArrow: '<button class="slick-prev slick-arrow" aria-label="Prev" type="button" style=""><img src="img/sl_left.png" class="img-fluid"></button>',
      responsive: [
        {
          breakpoint: 320,
         settings: {
           slidesToShow: 1,
           sldiesPerRow: 1,
           variableWidth: true
          }
        },
        {
          breakpoint: 767,
         settings: {
           slidesToShow: 1,
           sldiesPerRow: 1,
           variableWidth: false
          }
        }
       ]
    });
    $('.slider_works').slick('setPosition');