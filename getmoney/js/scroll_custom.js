
    $(document).ready(function () {
    var tl = gsap.timeline();
    tl.to('.img__bg-intro', {duration:.5, opacity:1, ease: "power1.in" });
    tl.to('#messageOne', { duration:.5, opacity: 1});
    tl.to('#messageTwo', {duration:.5, opacity: 1});
    tl.to('#titletextAnim', {duration:1, opacity: 1});
    tl.to('#titletextAnim2', {duration:1, opacity: 1});
    tl.to('#buttonAnim', {duration:1, opacity: 1});
    gsap.from('.icon_card', {duration:1.5, opacity: 0, stagger: 1});
    
    gsap.from('.lowestprice__block .wrapper_blocks', {
      scrollTrigger:{
        trigger: '.lowestprice__block',
        start: '20px 75% ',
        markers:false
      },
      duration: .5, 
      opacity: 0, 
      stagger: .5
    });
    gsap.from('.lowestprice__block .head__main-bold',{
      scrollTrigger:{
        trigger: '.head__main-bold',
        markers:false
      },
      duration: 1, 
      opacity: 0,
      delay: 3
    });
    gsap.from('.lowestprice__block .text_intro',{
      scrollTrigger:{
        trigger: '.text_intro',
        markers:false
      },
      duration: 1, 
      opacity: 0,
      delay: 3.5
    });
    gsap.from('.third__section .head__main-bold',{
      scrollTrigger:{
        trigger: '.third__section',
        start: '20px 50%',
        markers:false
      },
      duration: 1, 
      opacity: 0
    });
    ScrollTrigger.create({
      trigger: '.third__section',
      toggleClass: 'active',
      start: '20px 55%',
      duration: 1
    });
    gsap.from('#textone', {
        scrollTrigger: {
          trigger: '.slider__card-body',
          start: '20px 50%',
          markers: false
        },
        duration: .5,
        opacity: 0
      });
    gsap.from('#namecard', {
        scrollTrigger: {
          trigger: '.slider__card-body',
          start: '20px 50%',
          markers: false
        },
        duration: .5,
        opacity: 0
      });
    gsap.from('#texttwo', {
        scrollTrigger: {
          trigger: '.slider__card-body',
          start: '20px 50%',
          markers: false
        },
        duration: .5,
        opacity: 0
      });
    gsap.from('.table__info', {
        scrollTrigger: {
          trigger: '.slider__card-body',
          start: '20px 50%',
          markers: false
        },
        duration: .5,
        opacity: 0
      });
    gsap.from('.button__box', {
        scrollTrigger: {
          trigger: '.slider__card-body',
          start: '20px 50%',
          markers: false
        },
        duration: .5,
        opacity: 0
      });
    gsap.from('.box__img', {
        scrollTrigger: {
          trigger: '.slider__card-body',
          start: '20px 50%',
          markers: false
        },
        duration: .5,
        opacity: 0
      });
    gsap.from('.slick-arrow', {
        scrollTrigger: {
          trigger: '.slider__card-body',
          start: '20px 50%',
          markers: false
        },
        duration: .5,
        opacity: 0
      });
    gsap.from('.fourth__section .head__main-bold', {
        scrollTrigger: {
          trigger: '.fourth__section',
          start: '20px 50%',
          markers: false
        },
        duration: 1,
        opacity: 0
      });
    gsap.from('.fourth__section .slider__range-box', {
        scrollTrigger: {
          trigger: '.calculation__block',
          start: '20px 60%',
          markers: false
        },
        duration: 1,
        opacity: 0
      });
    gsap.from('.fourth__section .dark__block', {
        scrollTrigger: {
          trigger: '.calculation__block',
          start: '20px 65%',
          markers: false
        },
        duration: 1,
        opacity: 0,
        delay: 1
      });
    ScrollTrigger.create({
        trigger: '.fifth__section',
        toggleClass: 'active',
        start: '20px 60%',
        duration: 1,
        markers: false
      });
    gsap.from('.fifth__section .head__main-bold', {
        scrollTrigger: {
          trigger: '.fifth__section ',
          start: '20px 50%',
          markers: false
        },
        duration: 1,
        opacity: 0
      });
    gsap.from('.fifth__section .wrapper_blocks', { 
      scrollTrigger: {
        trigger: '.wrapper__steps-block ',
        start: '-20px 55%',
        markers: false
      },
      duration: 1, 
      opacity: 0, 
      stagger: 1 
    });
    gsap.timeline({
      scrollTrigger:{
        trigger: '.sixth__section',
        start:'top 200%',
        end:'bottom 100%',
        markers: false,
        scrub: true
      }
    })
    .from('#videoBarOne',{x: innerWidth*1});
    gsap.timeline({
        scrollTrigger: {
          trigger: '.sixth__section',
          start: 'top 200%',
          end: 'bottom 100%',
          markers: false,
          scrub: true
        }
      })
        .from('#videoBarTwo', { x: innerWidth * -1 });
      ScrollTrigger.create({
        trigger: '.seventh__section',
        toggleClass: 'active',
        start: '20px 60%',
        duration: 1,
        markers: false
      });
    gsap.from('#phoneAnim', {
        scrollTrigger: {
          trigger: '.seventh__section',
          start: '-20px 75%',
          markers: false
        },
        duration: 1,
        opacity: 0,
        delay: .5
      });
    gsap.from('#messageThree', {
        scrollTrigger: {
          trigger: '.seventh__section',
          start: '20px 65%%',
          markers: false
        },
        duration: 1,
        opacity: 0,
        delay:.5
      });
    gsap.from('.seventh__section .head__main-bold', {
        scrollTrigger: {
          trigger: '.seventh__section',
          start: '20px 65%%',
          markers: false
        },
        duration: 1,
        opacity: 0,
        delay: 1
      });
    gsap.from('.seventh__section .button_block', {
        scrollTrigger: {
          trigger: '.seventh__section',
          start: '20px 65%%',
          markers: false
        },
        duration: 1,
        opacity: 0,
        delay: 2
      });
    gsap.from('.eighth__section .head__main-bold', {
        scrollTrigger: {
          trigger: '.eighth__section',
          start: '20px 75%%',
          markers: false
        },
        duration: 1,
        opacity: 0
      });
    gsap.from('.nav-item', {
        scrollTrigger: {
          trigger: '.eighth__section',
          start: '20px 75%%',
          markers: false
        },
        duration: 1,
        opacity: 0,
        stagger: .5
      });
    gsap.from('#myTabContent', {
        scrollTrigger: {
          trigger: '.eighth__section',
          start: '20px 75%%',
          markers: false
        },
        duration: 1,
        opacity: 0,
        delay: 2.5
      });
    })

    
  