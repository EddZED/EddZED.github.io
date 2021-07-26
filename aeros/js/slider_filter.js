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
  asNavFor: '.slider_demonstr'
  });
  $('.slider_demonstr').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  TouchMove: true,
  arrows: false,
  infinite: false,
  dots: false,
  draggable: false,
  fade: false,
  focusOnSelect: false,
  asNavFor: '.slider_filter_cart'
  });
$('.slider_demonstr').slick('setPosition');