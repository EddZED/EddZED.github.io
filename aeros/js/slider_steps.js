$('.slider_step_brizers').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  TouchMove: false,
  arrows: false,
  initialSlide: 0,
  draggable: false,
  fade: false,
  focusOnSelect: false
});
$('a[data-slide]').click(function(e) {
   e.preventDefault();
   var slideno = $(this).data('slide');
   $('.slider_step_brizers').slick('slickGoTo', slideno - 1);
 });