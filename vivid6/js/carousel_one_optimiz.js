$('#seo_audit').owlCarousel({
    interval:5000,
    loop:false,
    nav:false,
    dots:false,
    margin:30,
    rewind:true,
    responsive:{
        0:{
            items:1,
            autoplay:false,
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
var elem = $('#seo_audit');
elem.owlCarousel();
$('#optim_l').click(function() {
    elem.trigger('prev.owl.carousel', [200]);
})
$('#optim_r').click(function() {
    elem.trigger('next.owl.carousel')
});
