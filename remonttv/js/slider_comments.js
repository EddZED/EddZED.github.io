$('.slider_coments').slick({
  centerMode: true,
  centerPadding: '0',
  slidesToShow: 1,
  slidesToScroll: 1,
  arows: true,
  infinite: false,
  variableWidth: true,
  initialSlide: 1,
  nextArrow: '<button type = "button" class = "slick-next d-none d-xl-block"><img src="img/arr_right.png" class="img-fluid"></ button>',
  prevArrow: '<button type = "button" class = "slick-prev d-none d-xl-block"><img src="img/arr_left.png" class="img-fluid"></ button>',
  responsive: [
    {
      breakpoint: 992,
      settings: {
        appendArrows: $('nav_sl')
      }
    }
  ]
});
$('.slick-prev-com').click(function(event){
  $('.slider_coments').slick('slickPrev');
});
$('.slick-next-com').click(function (event) {
  $('.slider_coments').slick('slickNext');
})