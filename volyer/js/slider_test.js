
$('.init_sl').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  dots: false,
  arrow: true,
  prevArrow: false,
  draggable: false,
  nextArrow: $('.btn_slide')
})
$('.modal-open .modal .init_sl').slick('SetPosition')