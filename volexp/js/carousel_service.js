$('.owl-carousel').owlCarousel({
    loop:false,
    margin:0,
    nav:true,
    touchDrag:true,
    dots:false,
    URLhashListener:true,
    startPosition:'URLHash',
    lazyLoad:true,
    lazyLoadEager:4,
    navText: ['<i class="fas fa-angle-left"></i>','<i class="fas fa-angle-right"></i>'],
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:1,
            nav:true
        }
    }
})