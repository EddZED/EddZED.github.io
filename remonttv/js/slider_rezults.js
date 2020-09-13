$('.slider_rezults').slick({
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
    },
    {
      breakpoint: 480,
      settings: {
      }
    }
  ]
});
$('.slick-prev-2').click(function(event){
  $('.slider_rezults').slick('slickPrev');
});
$('.slick-next-2').click(function (event) {
  $('.slider_rezults').slick('slickNext');
})