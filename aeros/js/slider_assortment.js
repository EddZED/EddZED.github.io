$('.slider_assortment').slick({
  arrows: true,
  dots: false,
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
        centerMode: false,
        arrows: true
      }
    },
    {
      breakpoint: 0,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        centerMode: false,
        arrows: true
      }
    }
]
});
$('.slider_assortment').slick('setPosition');

    
