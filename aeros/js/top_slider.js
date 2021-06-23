$('.intro_slider_view').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  arrows: false,
  fade: true,
  asNavFor: '.intro_slider_nav'
});
$('.intro_slider_nav').slick({
   slidesToShow: 5,
  slidesToScroll: 1,
  asNavFor: '.intro_slider_view',
  dots: false,
  centerMode: true,
  centerPadding: '10px',
  focusOnSelect: true
});