$('.knowledge_base_slider').slick({
  slidesToShow: 2,
  slidesToScroll: 1,
  TouchMove: true,
  arrows: true,
  dots: false,
  rows: 2,
  slidesPerRov: 1,
  draggable: false,
  fade: false,
  infinite: false,
  focusOnSelect: false,
  prevArrow: '<button type = "button" class = "slick-prev"><img class="img-fluid" src="svg/left_goods.svg" alt="передъидущий слайд"></ button>',
  nextArrow: '<button type = "button" class = "slick-next"><img class="img-fluid" src="svg/right_goods.svg" alt="следующий слайд"></ button>',
  responsive: [
      {
        breakpoint: 1199,
        settings: {
          slidesToShow: 2,
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
        breakpoint: 767,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          centerMode: false,
          arrows: true
        }
      }
    ]
});
$('.knowledge_base_slider').slick('setPosition');