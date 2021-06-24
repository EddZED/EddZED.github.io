$('.slider_step_brizers').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  TouchMove: false,
  arrows: false,
  initialSlide: 0,
  draggable: false,
  fade: false,
  focusOnSelect: false,
  asNavFor: '.slider_step_brizers_navigation'
});
$('.slider_step_brizers_navigation').slick({
  autoplay: false,
  slidesToShow: 3,
  slidesToScroll: 1,
  initialSlide: 1,
  infinite: false,
  TouchMove: false,
  asNavFor: '.slider_step_brizers',
  dots: false,
  focusOnSelect: true,
  responsive: [
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
          infinite: false,
          centerMode: false,
          arrows: true,
          dots: true
        }
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
          arrows: true,
          dots: true
        }
      },
      {
        breakpoint: 565,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          centerMode: true,
          centerPadding: '0px',
          arrows: true,
          adaptiveHeight: false,
          dots: true
         }
      }
    ]
});
$('a[data-slide]').click(function(e) {
   e.preventDefault();
   var slideno = $(this).data('slide');
   $('.slider_step_brizers').slick('slickGoTo', slideno - 1);
 });