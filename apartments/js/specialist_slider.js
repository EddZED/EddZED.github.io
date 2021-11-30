$(document).on('ready', function() {
      $(".slider_specialist").slick({
        infinite: true,
        dots: false,
        arrows:true,
        slidesToScroll: 1,
        slidesToShow: 3,
        prevArrow: '<button type = "button" class = "slick-prev"><img class="img-fluid d-none d-lg-block" src="img/prev_arrow.png" alt=""><img class="img-fluid d-lg-none" src="img/ar_prev2.png" alt=""></ button>',
        nextArrow: '<button type = "button" class = "slick-next"><img class="img-fluid d-none d-lg-block" src="img/next_arrow.png" alt=""><img class="img-fluid d-lg-none" src="img/ar_next2.png" alt=""></ button>',
        responsive: [
                            {
                              breakpoint: 992,
                              settings: {
                                slidesToShow: 2,
                                slidesToScroll: 3,
                                infinite: true
                              }
                            },
                            {
                              breakpoint: 600,
                              settings: {
                                slidesToShow: 1,
                                slidesToScroll: 2
                              }
                            }
                          ]
      });
    });
    $('.slider_specialist').slick('setPosition');