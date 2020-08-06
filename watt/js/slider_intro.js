var helpers = {
	addZeros: function (n) {
		return (n < 10) ? '0' + n : '' + n;
	}
};
function sliderInit() {
  var $slider = $('.slider_block');
  $slider.each(function() {
    var $sliderParent = $(this).parent();
    $(this).slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  dots: false,
  infinite: true,
  prevArrow: '<button type = "button" class = "slick-prev"><img class="img-fluid" src="img/left_long.png"></ button>',
  nextArrow: '<button type = "button" class = "slick-next"><img class="img-fluid" src="img/right_long.png"></ button>',
  appendArrows: $('.arrow_slider')
});
if ($(this).find('.slider_item').length > 1) {
  $(this).siblings('.arrow_slider').show();
}

$(this).on('afterChange', function(event, slick, currentSlide){
  $sliderParent.find('.arrow_slider .now_num').html(helpers.addZeros(currentSlide + 1));
});

var sliderItemsNum = $(this).find('.slick-slide').not('.slick-cloned').length;
$sliderParent.find('.arrow_slider .total').html(helpers.addZeros(sliderItemsNum));

});

$('.slick-next').on('click', function () {
console.log('test');
$('.slider-holder').slick('slickGoTo', 5);
});
};

sliderInit();