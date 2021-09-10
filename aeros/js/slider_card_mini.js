$('.left_slider_card_block').slick({
  slidesToShow: 6,
  slidesToScroll: 1,
  TouchMove: false,
  arrows: true,
  infinite: false,
  dots: false,
  draggable: false,
  waitForAnimate: true,
  fade: false,
  focusOnSelect: true,
  vertical: true,
  verticalSwiping: false,
  asNavFor: '.right_slider_card_block',
  responsive: [
      {
        breakpoint: 991,
        settings: {
          dots: false
        }
      }
  ]
  });
$('.left_slider_card_block').slick('setPosition');
$('.right_slider_card_block').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  TouchMove: true,
  arrows: true,
  infinite: true,
  dots: false,
  draggable: false,
  fade: false,
  focusOnSelect: true,
  vertical: false,
  verticalSwiping: false,
  asNavFor: '.left_slider_card_block',
  responsive: [
      {
        breakpoint: 991,
        settings: {
          dots: false
        }
      }
  ]
  });
  $('.right_slider_card_block').slick('setPosition');