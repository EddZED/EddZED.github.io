$('.slider_test').on('afterChange', function(event, slick, currentSlide){

  $('.progress__count').text(currentSlide + 1);
  $('.progress__performed').width( 20 * (currentSlide + 1) + '%' );

});