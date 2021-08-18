$('.intro_slider_view').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  arrows: false,
  centerMode: false,
  focusOnSelect: false,
  fade: true,
  asNavFor: '.intro_slider_nav'
});
$('.intro_slider_nav').slick({
  slidesToShow: 5,
  slidesToScroll: 1,
  asNavFor: '.intro_slider_view',
  dots: false,
  centerMode: false,
  centerPadding: '0px',
  focusOnSelect: false,
  prevArrow: '<button type = "button" class = "slick-prev"><img class="img-fluid" src="img/sl_left.png" alt="передъидущий слайд"></ button>',
  nextArrow: '<button type = "button" class = "slick-next"><img class="img-fluid" src="img/sl_right.png" alt="следующий слайд"></ button>',
  responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          infinite: false,
          centerMode: false,
          arrows: true,
          dots: true
        }
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
          arrows: true,
          dots: true
        }
      },
      {
        breakpoint: 565,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          centerMode: true,
          centerPadding: '0px',
          arrows: true,
          adaptiveHeight: false,
          dots: true
        }
      }
    ]
});
$('.intro_slider_view').slick('setPosition');
$('.intro_slider_nav').slick('setPosition');