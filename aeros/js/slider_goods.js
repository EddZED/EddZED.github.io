$('.slider_cards_goods').slick({
  slidesToShow: 4,
  slidesToScroll: 1,
  TouchMove: true,
  arrows: true,
  draggable: true,
  fade: false,
  focusOnSelect: true,
  responsive: [
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          infinite: false,
          centerMode: false,
          arrows: true
        }
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
          arrows: true
        }
      },
      {
        breakpoint: 565,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: true,
          adaptiveHeight: false
         }
      }
    ]
});
$('.slider_cards_goods').slick('setPosition');