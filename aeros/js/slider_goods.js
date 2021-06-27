$('.slider_cards_goods').slick({
  slidesToShow: 4,
  slidesToScroll: 1,
  TouchMove: false,
  arrows: true,
  draggable: false,
  fade: false,
  infinite: true,
  focusOnSelect: false,
  prevArrow: '<button type = "button" class = "slick-prev"><img class="img-fluid" src="svg/left_goods.svg" alt="передъидущий слайд"></ button>',
  nextArrow: '<button type = "button" class = "slick-next"><img class="img-fluid" src="svg/right_goods.svg" alt="следующий слайд"></ button>',
  responsive: [
      {
        breakpoint: 1199,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          centerMode: false,
          draggable: false,
          arrows: true
        }
      },
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          centerMode: false,
          arrows: true
        }
      },
      {
        breakpoint: 374,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          centerMode: false,
          arrows: true
        }
      }
    ]
});
$('.slider_cards_goods').slick('setPosition');