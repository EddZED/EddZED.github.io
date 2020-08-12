$('.slider_test').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  dots: false,
  arrow: true,
  appendArrows: $('.nav_slider'),
  nextArrow: '<button type="button" class="btn_header yellow_btn btn_slide">Следующий вопрос</button>',
  adaptiveHeight: true
})