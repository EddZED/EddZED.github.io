$('#car_blog').owlCarousel({
    interval:8000,
    loop:true,
    nav:false,
    dots:true,
    margin:20,
    autoplay:true,
    responsive:{
        0:{
            items:1,
            autoplay:false,
            touchDrag:true,
            dots:true,
            mouseDrag:false
        },
        767:{
            items:2,
            autoplayHoverPause:true
        },
        992:{
            items:3
        }
    }
})
var elemblog = $('#car_blog');
elemblog.owlCarousel();
$('#left_bl').click(function() {
    elemblog.trigger('prev.owl.carousel', [300]);
})
$('#right_bl').click(function() {
    elemblog.trigger('next.owl.carousel')
});
