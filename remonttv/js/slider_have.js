  $('.slider_have').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      rows: 2,
      infinite: false,
      variableWidth: true
  });
  $('.slider_have').slick('setPosition');
  $('.mobile_slider-have').slick({
    centerMode:true,
    centerPadding: '0',
    infinite: false,
    slidesToShow: 1,
    slidesToScroll: 1,
    appendArrows: $('.nav_sl-mobile'),
    nextArrow: '<button type = "button" class = "slick-next"><img src="img/right_2.png" class="img-fluid"></ button>',
    prevArrow: '<button type = "button" class = "slick-prev"><img src="img/white_left_2.png" class="img-fluid"></ button>',
  })