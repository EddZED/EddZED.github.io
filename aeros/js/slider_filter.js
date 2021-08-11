$('.slider_filter_cart').slick({
  slidesToShow: 2,
  slidesToScroll: 1,
  TouchMove: true,
  arrows: false,
  infinite: false,
  dots: true,
  draggable: true,
  fade: false,
  focusOnSelect: true,
  responsive: [
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          centerMode: false,
          draggable: false,
          dots: false,
          arrows: false
        }
      }
  ]
  });
$('.slider_filter_cart').slick('setPosition');