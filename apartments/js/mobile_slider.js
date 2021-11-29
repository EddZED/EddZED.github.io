$(document).on('ready', function() {
$(".links_block").slick({
        infinite: true,
        dots: false,
        arrows:true,
        slidesToScroll: 1,
        slidesToShow: 1,
        mobileFirst: true,
        prevArrow: '<button type = "button" class = "slick-prev"></ button>',
        nextArrow: '<button type = "button" class = "slick-next"></ button>',
        responsive: [
                            {
                              breakpoint: 992,
                              settings: 'unslick'
                            }
                          ]
      });
      $('.links_block').slick('setPosition');
    });
    