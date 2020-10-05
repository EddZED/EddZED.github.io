$('.slider_1').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  arrows: false,
  fade: true,
  swipe: false,
  asNavFor: '.slider_2'
});
$('.slider_2').slick({
  slidesToShow: 3,
  slidesToScroll: 3,
  asNavFor: '.slider_1',
  dots: false,
  centerMode: true,
  infinite: true,
  centerPadding: '0',
  focusOnSelect: true
});