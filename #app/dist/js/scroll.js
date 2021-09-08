var controller = new ScrollMagic.Controller();
var pixels = document.width;

$(document).ready(function() {

	
	var height = $( window ).height();
	var width = $( window ).width();

	if (width >= 320) {
		$(function () {

			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 1800, offset: 0, triggerHook: 0})
				.setPin("#thirdSection")	
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 5000, offset: 0, triggerHook: 0})
				.setClassToggle("#cont1", "show")
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 380, offset: 0, triggerHook: 0})
				.setClassToggle("#process-design", "animate")
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 5000, offset: 380, triggerHook: 0})
				.setClassToggle("#cont2", "show")
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 380, offset: 380, triggerHook: 0})
				.setClassToggle("#process-phone", "animate")
				.addTo(controller);	
			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 5000, offset: 500, triggerHook: 0})
				.setClassToggle("#cont3", "show")
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 380, offset: 500, triggerHook: 0})
				.setClassToggle("#process-phone", "animate2")
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 5000, offset: 650, triggerHook: 0})
				.setClassToggle("#cont4", "show")
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 380, offset: 650, triggerHook: 0})
				.setClassToggle("#process-phone", "animate3")
				.addTo(controller);
			// new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 380, offset: 420, triggerHook: 0})
			// 	.setClassToggle("#process-phone", "unanimate")
			// 	.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 250, offset: 800, triggerHook: 0})
				.setClassToggle("#process-marketing", "animate")
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 5000, offset: 800, triggerHook: 0})
				.setClassToggle("#cont5", "show")
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 5000, offset: 1050, triggerHook: 0})
				.setClassToggle("#process-launch", "animate")
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#thirdSection", duration: 5000, offset: 1050, triggerHook: 0})
				.setClassToggle("#cont6", "show")
				.addTo(controller);
			
   });
	} else {
		$(function () {
			
		});	
	};
});







    