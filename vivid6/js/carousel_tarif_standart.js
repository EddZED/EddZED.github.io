$('#standart').owlCarousel({
    interval:5000,
    loop:false,
    nav:false,
    dots:false,
    margin:30,
    rewind:true,
    responsive:{
        0:{
            items:1,
            autoPlay:true,
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
var owl = $('#standart');
owl.owlCarousel();
$('#start_left_st').click(function() {
    owl.trigger('prev.owl.carousel', [200]);
})
$('#start_right_st').click(function() {
    owl.trigger('next.owl.carousel')
});
