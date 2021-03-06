$('.view_window-slider').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  arrows: true,
  fade: true,
  infinite: true,
  asNavFor: '.min_card-slider',
  prevArrow: '<button type = "button" class = "slick-prev"><img class="img-fluid" src="img/ar_left-slider.png"></ button>',
  nextArrow: '<button type = "button" class = "slick-next"><img class="img-fluid" src="img/ar_right-slider.png"></ button>',
  appendArrows: $('.view_window-slider')
});
$('.min_card-slider').slick({
  slidesToShow: 3,
  slidesToScroll: 1,
  asNavFor: '.view_window-slider',
  dots: false,
  centerMode: false,
  centerPadding: '0',
  draggable: true,
  swipeToSlide: true,
  focusOnSelect: true
});