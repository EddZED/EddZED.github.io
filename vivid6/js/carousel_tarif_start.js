$('#start').owlCarousel({
    interval:5000,
    loop:false,
    nav:false,
    dots:false,
    margin:30,
    rewind:true,
    responsive:{
        0:{
            items:1,
            autoplay:true,
            touchDrag:true,
            dots:true,
            mouseDrag:false,
            autoplayHoverPause:true
        },
        992:{
            items:3,
            nav:false,
            mouseDrag:false
        }
    }
})
var elemе = $('#start');
elemе.owlCarousel();
$('#start_l').click(function() {
    elemе.trigger('prev.owl.carousel', [200]);
})
$('#start_r').click(function() {
    elemе.trigger('next.owl.carousel')
});
