$(".polzunok-2").slider({
    min: 0,
    max: 100,
    value:7,
    range: "min",
    animate: "fast",
  });
  var playing = false;
  
$('#rightSwitch').click(function(){
    $('.block-bra').toggleClass('active');
    $(".block-izg").removeClass("active");
    $('.block-curt').removeClass('active');
    $('#inputSwitch').prop('checked', false);
    $('#shapeEnd').prop('checked', false);
    $('#audioSwitch').prop('checked', false);
    playing = false;
    $(".apeir-polzunok .ui-slider .ui-slider-handle").removeClass('active');
    $(".apeir-polzunok .ui-slider-horizontal .ui-slider-range-min").removeClass('active');
})

$('#inputSwitch').click(function(){
    $(".block-izg").toggleClass("active");
    $('.block-bra').removeClass('active');
    $('.block-curt').removeClass('active');
    $('#rightSwitch').prop('checked', false);
    $('#shapeEnd').prop('checked', false);
    $('#audioSwitch').prop('checked', false);
    playing = false;
    $(".apeir-polzunok .ui-slider .ui-slider-handle").removeClass('active');
    $(".apeir-polzunok .ui-slider-horizontal .ui-slider-range-min").removeClass('active');
})

$('#audioSwitch').click(function(){
    $(".block-izg").removeClass("active");
    $('.block-bra').removeClass('active');
    $('.block-curt').removeClass('active');
    $('#rightSwitch').prop('checked', false);
    $('#inputSwitch').prop('checked', false);
    $('#shapeEnd').prop('checked', false);
    $(".apeir-polzunok .ui-slider .ui-slider-handle").removeClass('active');
    $(".apeir-polzunok .ui-slider-horizontal .ui-slider-range-min").removeClass('active');
})


$('#shapeEnd').click(function(){
    $(".apeir-polzunok .ui-slider .ui-slider-handle").toggleClass('active');
    $(".apeir-polzunok .ui-slider-horizontal .ui-slider-range-min").toggleClass('active');
    $('#audioSwitch').prop('checked', false);
    playing = false;
    $('#inputSwitch').prop('checked', false);
    $('#rightSwitch').prop('checked', false);
    $(".block-izg").removeClass("active");
    $('.block-bra').removeClass('active');
    if ($('.block-curt3').hasClass('active')) {
        $('.block-curt3').removeClass('active')
        setTimeout(function(){
        $('.block-curt2').removeClass('active')
        }, 300)
        setTimeout(function(){
            $('.block-curt1').removeClass('active')
        }, 600)
    } else {
        $('.block-curt1').addClass('active')
        setTimeout(function(){
        $('.block-curt2').addClass('active')
        }, 300)
        setTimeout(function(){
            $('.block-curt3').addClass('active')
        }, 600)
    }
});

$('#audioSwitch').click(function() {

    if (playing == false) {
        document.getElementById('audioPlayer').play();
        playing = true;
        // $(this).text("stop sound");

    } else {
        document.getElementById('audioPlayer').pause();
        playing = false;
        // $(this).text("restart sound");
    }

});






$('.apeir-img-block-text.active').click(function(){
    $('.apeir-img-main').toggleClass("active")
    $('.apeir-img-main').removeClass("active2")
    $('.apeir-img-main').removeClass("active3")
    $('.apeir-img-main').toggleClass("active6")
    $('.apeir-img-block-text.active3').removeClass("d-none")
    $('.apeir-img-block-text.active2').addClass("d-none")
    $('.apeir-img-block-text.active').removeClass("active2")
    if ($('.apeir-img-main').hasClass('active6')) {
        $('.apeir-img-main').find('.apeir-img-block-text.active').html('<svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.292893 7.29289C-0.0976311 7.68342 -0.0976311 8.31658 0.292893 8.70711L6.65685 15.0711C7.04738 15.4616 7.68054 15.4616 8.07107 15.0711C8.46159 14.6805 8.46159 14.0474 8.07107 13.6569L2.41421 8L8.07107 2.34315C8.46159 1.95262 8.46159 1.31946 8.07107 0.928933C7.68054 0.538408 7.04738 0.538408 6.65685 0.928933L0.292893 7.29289ZM1 9L31 9L31 7L1 7L1 9Z" fill="white"></path></svg>В ванную');
        $('.apeir-img-main').find('.apeir-img-block-text.active2').html('Развернуться <svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M30.7071 8.70711C31.0976 8.31658 31.0976 7.68342 30.7071 7.29289L24.3431 0.928932C23.9526 0.538408 23.3195 0.538408 22.9289 0.928932C22.5384 1.31946 22.5384 1.95262 22.9289 2.34315L28.5858 8L22.9289 13.6569C22.5384 14.0474 22.5384 14.6805 22.9289 15.0711C23.3195 15.4616 23.9526 15.4616 24.3431 15.0711L30.7071 8.70711ZM30 7L0 7V9L30 9V7Z" fill="white"></path></svg>');
        $('.apeir-img-main').removeClass("active")
    } else {
        $('.apeir-img-main').addClass("active")
        $('.apeir-img-main').removeClass("active6")
    }
    if ($('.apeir-img-main').hasClass('active')) {
        $('.apeir-img-main').find('.apeir-img-block-text.active').html('<svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.292893 7.29289C-0.0976311 7.68342 -0.0976311 8.31658 0.292893 8.70711L6.65685 15.0711C7.04738 15.4616 7.68054 15.4616 8.07107 15.0711C8.46159 14.6805 8.46159 14.0474 8.07107 13.6569L2.41421 8L8.07107 2.34315C8.46159 1.95262 8.46159 1.31946 8.07107 0.928933C7.68054 0.538408 7.04738 0.538408 6.65685 0.928933L0.292893 7.29289ZM1 9L31 9L31 7L1 7L1 9Z" fill="white"></path></svg> В номер');
        $('.apeir-img-main').find('.apeir-img-block-text.active3').html('Развернуться <svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M30.7071 8.70711C31.0976 8.31658 31.0976 7.68342 30.7071 7.29289L24.3431 0.928932C23.9526 0.538408 23.3195 0.538408 22.9289 0.928932C22.5384 1.31946 22.5384 1.95262 22.9289 2.34315L28.5858 8L22.9289 13.6569C22.5384 14.0474 22.5384 14.6805 22.9289 15.0711C23.3195 15.4616 23.9526 15.4616 24.3431 15.0711L30.7071 8.70711ZM30 7L0 7V9L30 9V7Z" fill="white"></path></svg>');
    } else {
        $('.apeir-img-main').find('.apeir-img-block-text.active').html('<svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.292893 7.29289C-0.0976311 7.68342 -0.0976311 8.31658 0.292893 8.70711L6.65685 15.0711C7.04738 15.4616 7.68054 15.4616 8.07107 15.0711C8.46159 14.6805 8.46159 14.0474 8.07107 13.6569L2.41421 8L8.07107 2.34315C8.46159 1.95262 8.46159 1.31946 8.07107 0.928933C7.68054 0.538408 7.04738 0.538408 6.65685 0.928933L0.292893 7.29289ZM1 9L31 9L31 7L1 7L1 9Z" fill="white"></path></svg> В ванную');
        $('.apeir-img-main').find('.apeir-img-block-text.active3').html('Развернуться <svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M30.7071 8.70711C31.0976 8.31658 31.0976 7.68342 30.7071 7.29289L24.3431 0.928932C23.9526 0.538408 23.3195 0.538408 22.9289 0.928932C22.5384 1.31946 22.5384 1.95262 22.9289 2.34315L28.5858 8L22.9289 13.6569C22.5384 14.0474 22.5384 14.6805 22.9289 15.0711C23.3195 15.4616 23.9526 15.4616 24.3431 15.0711L30.7071 8.70711ZM30 7L0 7V9L30 9V7Z" fill="white"></path></svg>');
        $('.apeir-img-block-text.active3').addClass("d-none")
        $('.apeir-img-block-text.active2').removeClass("d-none")
    }

});

$('.apeir-img-block-text.active3').click(function(){
    $('.apeir-img-main').toggleClass("active3")
    
    // $(this).toggleClass("d-none")
    // $('.apeir-img-block-text.active2').toggleClass("d-none")
    if ($('.apeir-img-main').hasClass('active3')) {
        $('.apeir-img-main').find('.apeir-img-block-text.active').html('<svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.292893 7.29289C-0.0976311 7.68342 -0.0976311 8.31658 0.292893 8.70711L6.65685 15.0711C7.04738 15.4616 7.68054 15.4616 8.07107 15.0711C8.46159 14.6805 8.46159 14.0474 8.07107 13.6569L2.41421 8L8.07107 2.34315C8.46159 1.95262 8.46159 1.31946 8.07107 0.928933C7.68054 0.538408 7.04738 0.538408 6.65685 0.928933L0.292893 7.29289ZM1 9L31 9L31 7L1 7L1 9Z" fill="white"></path></svg> В номер');
        $('.apeir-img-main').find('.apeir-img-block-text.active3').html('Развернуться <svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M30.7071 8.70711C31.0976 8.31658 31.0976 7.68342 30.7071 7.29289L24.3431 0.928932C23.9526 0.538408 23.3195 0.538408 22.9289 0.928932C22.5384 1.31946 22.5384 1.95262 22.9289 2.34315L28.5858 8L22.9289 13.6569C22.5384 14.0474 22.5384 14.6805 22.9289 15.0711C23.3195 15.4616 23.9526 15.4616 24.3431 15.0711L30.7071 8.70711ZM30 7L0 7V9L30 9V7Z" fill="white"></path></svg>');
        $('.apeir-img-main').removeClass("active")
    } else {
        $('.apeir-img-main').find('.apeir-img-block-text.active').html('<svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.292893 7.29289C-0.0976311 7.68342 -0.0976311 8.31658 0.292893 8.70711L6.65685 15.0711C7.04738 15.4616 7.68054 15.4616 8.07107 15.0711C8.46159 14.6805 8.46159 14.0474 8.07107 13.6569L2.41421 8L8.07107 2.34315C8.46159 1.95262 8.46159 1.31946 8.07107 0.928933C7.68054 0.538408 7.04738 0.538408 6.65685 0.928933L0.292893 7.29289ZM1 9L31 9L31 7L1 7L1 9Z" fill="white"></path></svg> В номер');
        $('.apeir-img-main').find('.apeir-img-block-text.active3').html('Развернуться <svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M30.7071 8.70711C31.0976 8.31658 31.0976 7.68342 30.7071 7.29289L24.3431 0.928932C23.9526 0.538408 23.3195 0.538408 22.9289 0.928932C22.5384 1.31946 22.5384 1.95262 22.9289 2.34315L28.5858 8L22.9289 13.6569C22.5384 14.0474 22.5384 14.6805 22.9289 15.0711C23.3195 15.4616 23.9526 15.4616 24.3431 15.0711L30.7071 8.70711ZM30 7L0 7V9L30 9V7Z" fill="white"></path></svg>');
        $('.apeir-img-main').addClass("active")
    }

})

$('.apeir-img-block-text.active2').click(function(){
    $('.apeir-img-main').toggleClass("active2")
    $('.apeir-img-main').removeClass("active3")
    $('.apeir-img-main').removeClass("active6")
    $('.apeir-img-main').removeClass("active")

    // if ($('.apeir-img-main').hasClass('active6')) {
    //     $('.apeir-img-main').find('.apeir-img-block-text.active').html('<svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.292893 7.29289C-0.0976311 7.68342 -0.0976311 8.31658 0.292893 8.70711L6.65685 15.0711C7.04738 15.4616 7.68054 15.4616 8.07107 15.0711C8.46159 14.6805 8.46159 14.0474 8.07107 13.6569L2.41421 8L8.07107 2.34315C8.46159 1.95262 8.46159 1.31946 8.07107 0.928933C7.68054 0.538408 7.04738 0.538408 6.65685 0.928933L0.292893 7.29289ZM1 9L31 9L31 7L1 7L1 9Z" fill="white"></path></svg>В ванную');
    //     $('.apeir-img-main').find('.apeir-img-block-text.active2').html('Развернуться <svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M30.7071 8.70711C31.0976 8.31658 31.0976 7.68342 30.7071 7.29289L24.3431 0.928932C23.9526 0.538408 23.3195 0.538408 22.9289 0.928932C22.5384 1.31946 22.5384 1.95262 22.9289 2.34315L28.5858 8L22.9289 13.6569C22.5384 14.0474 22.5384 14.6805 22.9289 15.0711C23.3195 15.4616 23.9526 15.4616 24.3431 15.0711L30.7071 8.70711ZM30 7L0 7V9L30 9V7Z" fill="white"></path></svg>');
        
    // }
    if ($('.apeir-img-main').hasClass('active2')) {
        $('.apeir-img-main').find('.apeir-img-block-text.active').html('<svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.292893 7.29289C-0.0976311 7.68342 -0.0976311 8.31658 0.292893 8.70711L6.65685 15.0711C7.04738 15.4616 7.68054 15.4616 8.07107 15.0711C8.46159 14.6805 8.46159 14.0474 8.07107 13.6569L2.41421 8L8.07107 2.34315C8.46159 1.95262 8.46159 1.31946 8.07107 0.928933C7.68054 0.538408 7.04738 0.538408 6.65685 0.928933L0.292893 7.29289ZM1 9L31 9L31 7L1 7L1 9Z" fill="white"></path></svg> В номер');
        $('.apeir-img-main').find('.apeir-img-block-text.active2').html('Развернуться <svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M30.7071 8.70711C31.0976 8.31658 31.0976 7.68342 30.7071 7.29289L24.3431 0.928932C23.9526 0.538408 23.3195 0.538408 22.9289 0.928932C22.5384 1.31946 22.5384 1.95262 22.9289 2.34315L28.5858 8L22.9289 13.6569C22.5384 14.0474 22.5384 14.6805 22.9289 15.0711C23.3195 15.4616 23.9526 15.4616 24.3431 15.0711L30.7071 8.70711ZM30 7L0 7V9L30 9V7Z" fill="white"></path></svg>');
        // $('.apeir-img-main').removeClass("active")
    } else {
        $('.apeir-img-main').find('.apeir-img-block-text.active').html('<svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.292893 7.29289C-0.0976311 7.68342 -0.0976311 8.31658 0.292893 8.70711L6.65685 15.0711C7.04738 15.4616 7.68054 15.4616 8.07107 15.0711C8.46159 14.6805 8.46159 14.0474 8.07107 13.6569L2.41421 8L8.07107 2.34315C8.46159 1.95262 8.46159 1.31946 8.07107 0.928933C7.68054 0.538408 7.04738 0.538408 6.65685 0.928933L0.292893 7.29289ZM1 9L31 9L31 7L1 7L1 9Z" fill="white"></path></svg> В номер');
        $('.apeir-img-main').find('.apeir-img-block-text.active2').html('Развернуться <svg width="31" height="16" viewBox="0 0 31 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M30.7071 8.70711C31.0976 8.31658 31.0976 7.68342 30.7071 7.29289L24.3431 0.928932C23.9526 0.538408 23.3195 0.538408 22.9289 0.928932C22.5384 1.31946 22.5384 1.95262 22.9289 2.34315L28.5858 8L22.9289 13.6569C22.5384 14.0474 22.5384 14.6805 22.9289 15.0711C23.3195 15.4616 23.9526 15.4616 24.3431 15.0711L30.7071 8.70711ZM30 7L0 7V9L30 9V7Z" fill="white"></path></svg>');
        // $('.apeir-img-main').addClass("active")
        // $('.apeir-img-block-text.active2').toggleClass("d-none")
        // $('.apeir-img-block-text.active3').toggleClass("d-none")

    }
    

});
setTimeout(function(){
    $('.main-img-main').toggleClass('main-img-body2');    
    $('.main-img-body2').hover(function(){
        $(this).parent().find(".main-img-body3").toggleClass("active")
    })
},7000)