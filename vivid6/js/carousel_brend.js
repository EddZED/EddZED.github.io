$('#brend').owlCarousel({
    interval:2000,
    loop:true,
    nav:false,
    dots:false,
    margin:20,
    autoplay:true,
    responsive:{
        0:{
            items:1,
            touchDrag:true,
            mouseDrag:false
        },
        767:{
            items:3,
            autoplayHoverPause:true
        },
        992:{
            items:4
        }
    }
})
var elem_blog = $('#brend');
elem_blog.owlCarousel();
$('#left_brend').click(function() {
    elem_blog.trigger('prev.owl.carousel', [200]);
})
$('#right_brend').click(function() {
    elem_blog.trigger('next.owl.carousel')
});
