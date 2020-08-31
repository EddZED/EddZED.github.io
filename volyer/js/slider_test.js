
$('.init_sl').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  dots: false,
  arrow: true,
  prevArrow: false,
  draggable: false,
  nextArrow: $('.btn_slide')
})

$(document).ready(function () {
  $('.init_sl').slick();
});

$('.modal').on('shown.bs.modal', function (e) {
  $('.init_sl').slick('setPosition');
  $('.wrap-modal-slider').addClass('open');
})
