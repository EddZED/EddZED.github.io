$('.top_image_slider_cart').slick({
  slidesToShow: 1,
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
          dots: false
        }
      }
  ]
  });
$('.top_image_slider_cart').slick('setPosition');