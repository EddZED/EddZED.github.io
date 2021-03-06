$('.owl-carousel').owlCarousel({
    loop:true,
    nav:true,
    center:true,
    margin:30,
    touchDrag:true,
    dots:false,
    navText: ['<i class="fas fa-arrow-left"></i>','<i class="fas fa-arrow-right"></i>'],
    responsive:{
        0:{
            items:1,
            margin:0,
            stagePadding:0
        },
        600:{
            items:1,
            stagePadding:150
        },
        768:{
            items:1,
            stagePadding:250
        },
        1000:{
            items:1,
            nav:true,
            stagePadding:300
        }
    }
})