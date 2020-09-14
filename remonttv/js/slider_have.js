  $('.slider_have').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      rows: 2,
      infinite: false,
      variableWidth: true
  });
  $('.slider_have').slick('setPosition');
  $('.mobile_slider-have').slick({
    dots: true,
    centerMode:true,
    centerPadding: '0',
    infinite: false,
    slidesToShow: 1,
    slidesToScroll: 1,
    adaptiveHeight:true,
    appendDots: $('.nav_sl-mobile'),
    appendArrows: $('.nav_sl-mobile'),
    nextArrow: '<button type = "button" class = "slick-next btn_next"><img src="img/right_2.png" class="img-fluid"></ button>',
    prevArrow: '<button type = "button" class = "slick-prev"><img src="img/white_left_2.png" class="img-fluid"></ button>',
    customPaging: function (slider, i) {
      var current = i + 1;
      current = current < 10 ? " " + current : current;

      var total = slider.slideCount;
      total = total < 10 ? " " + total : total;

      return (
        '<button type="button" role="button" tabindex="0" class="slick-dots-button">\
			<span class="slick-dots-current">' + current + '</span>\
			<span class="slick-dots-separator"></span>\
			<span class="slick-dots-total">' + total + '</span>\
		</button>'
      );
    }
  });







  /* $(".btn_next").click(function () {
    var $page = $(".index_per");
    $page.val(parseInt($page.val()) + 1);
    $page.change();
  });*/





