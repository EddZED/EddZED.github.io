$('#comments_carousel').owlCarousel({
    interval:5000,
    loop:true,
    nav:false,
    dots:true,
    margin:30,
    autoplay:true,
    responsive:{
        0:{
            items:1
        },
        576:{
            items:2
        },
        767:{
            items:3
        },
        992:{
            items:4,
            dots:true
        }
    }
})
var elem_2 = $('#comments_carousel');
elem_2.owlCarousel();
$('#com_left').click(function() {
    elem_2.trigger('prev.owl.carousel', [200]);
})
$('#com_right').click(function() {
    elem_2.trigger('next.owl.carousel')
});
