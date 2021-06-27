var controller = new ScrollMagic.Controller();
    var tl = new TimelineLite({});
      tl.from("header", 0.75, {
        top: -50
      });
      new ScrollMagic.Scene({ triggerElement: "header", duration: 0, offset: -1})
        .setTween(tl)
        .reverse(false)
        .addTo(controller);

    // Fade in
      var fadeInTimeline = new TimelineMax();
      var fadeInFrom = TweenMax.from(".text_card", 1, {
        autoAlpha: 0
      });
      var fadeInTo = TweenMax.to(".text_card", 1, {
        autoAlpha: 1
      });
      fadeInTimeline
        .add(fadeInFrom)
        .add(fadeInTo);

      new ScrollMagic.Scene({
        triggerElement: "#sl_nav ",
        triggerHook: "onCenter",
        offset: -250,
      })
        .setTween(fadeInTimeline)
        .duration(400)
        //.reverse(false)
        //.addIndicators() // add indicators (requires plugin)
        .addTo(controller);
    
    //Fly in from the bottom
      var fromBottomTimeline = new TimelineMax();
      var fromBottomFrom = TweenMax.from(".text_card", 1, {
        y: 100
      });
      var fromBottomTo = TweenMax.to(".text_card", 1, {
        y: 0
      });
      fromBottomTimeline
        .add(fromBottomFrom)
        .add(fromBottomTo);

      new ScrollMagic.Scene({
        triggerElement: "#sl_nav ",
        offset: -250,
      })
        .setTween(fromBottomTimeline)
        .duration(400)
        //.reverse(false)
        //.addIndicators() // add indicators (requires plugin)
        .addTo(controller);
    
    //Fly in from the bottom goods
      var fromBottomTimeline2 = new TimelineMax();
      var fromBottomFrom2 = TweenMax.from(".image_slider_item img", 1, {
        y: 50
      });
      var fromBottomTo2 = TweenMax.to(".image_slider_item img", 1, {
        y:-50
      });
      fromBottomTimeline2
        .add(fromBottomFrom2)
        .add(fromBottomTo2);

      new ScrollMagic.Scene({
        triggerElement: "#slider_goods ",
        offset: -200,
      })
        .setTween(fromBottomTimeline2)
        .duration(1000)
        //.reverse(false)
        //.addIndicators() // add indicators (requires plugin)
        .addTo(controller);
    // Fade in goods
      var fadeInTimeline3 = new TimelineMax();
      var fadeInFrom3 = TweenMax.from(".image_slider_item img", 1, {
        autoAlpha: 0
      });
      var fadeInTo3 = TweenMax.to(".image_slider_item img", 1, {
        autoAlpha: 1
      });
      fadeInTimeline3
        .add(fadeInFrom3)
        .add(fadeInTo3);

      new ScrollMagic.Scene({
        triggerElement: "#slider_goods ",
        triggerHook: "onCenter",
        offset: -200,
      })
        .setTween(fadeInTimeline3)
        .duration(1000)
        //.reverse(false)
        //.addIndicators() // add indicators (requires plugin)
        .addTo(controller);