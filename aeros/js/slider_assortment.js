$('.slider_assortment').slick({
  arrows: false,
  dots: true,
  infinite: true,
  slidesToScroll: 1,
  slidesToShow:2,
  variableWidth: false,
  mobileFirst: true,
  responsive: [
    {
    breakpoint: 992,
    settings: 'unslick'
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1,
        centerMode: false
      }
    },
    {
      breakpoint: 0,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        centerMode: false
      }
    }
]
});
$('.slider_assortment').slick('setPosition');

    
