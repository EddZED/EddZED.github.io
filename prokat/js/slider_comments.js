$('.slider_com_wrap').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  arrows: true,
  dots: false,
  fade: false,
  prevArrow: '.slick_prev',
  nextArrow: '.slick_next',
  asNavFor: '.slider_nav_com'
});
$('.slider_nav_com').slick({
  slidesToShow: 2,
  slidesToScroll: 1,
  asNavFor: '.slider_com_wrap',
  dots: false,
  centerMode: false,
  focusOnSelect: true,
  arrows: false
});