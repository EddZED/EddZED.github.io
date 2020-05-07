$(document).ready(function(){
      $('.slider_keys').slick({
      	slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        centerMode:true,
        dots:true,
        infinite:false,
        initialSlide:true,
        centerPadding:'60px',
  });
   var filtered = false;
  $(".filter li").on('click', function(){ 
    if(filtered === false){
      $(".slider_keys").slick('slickFilter','.market');
      filtered = true;
    }
    else if(filtered === false){
      $(".slider_keys").slick('slickFilter','.seo');
      filtered = true;
    }
    else if(filtered === false){
      $(".slider_keys").slick('slickFilter','.web');
      filtered = true;
    }
    else if(filtered === false){
      
      $(".slider_keys").slick('slickUnfilter');
      filtered = true;
    }
    
  })
      });