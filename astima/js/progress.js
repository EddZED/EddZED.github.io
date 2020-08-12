$('.slider_test').on('afterChange', function(event, slick, currentSlide){
  $('.progress-bar').width( `${20 * (currentSlide + 1)}%` );
});
$(".btn_slide").click(function() {
  var $price = $(".index_per");
  $price.val(parseInt($price.val()) + 20 );
  $price.change();
});