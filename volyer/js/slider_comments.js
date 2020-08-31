$('.comments').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  dots: true,
  arrows: true,
  initialSlide: 1,
  adaptiveHeight: true,
  appendArrows: $('.slider_nav'),
  appendDots: $('.slider_nav'),
  prevArrow: '<button type = "button" class = "slick-prev"><img class="img-fluid" src="img/ar_left.png"></ button>',
  nextArrow: '<button type = "button" class = "slick-next"><img class="img-fluid" src="img/ar_right.png"></ button>'
})