var controller = new ScrollMagic.Controller();
var pixels = document.width;

$(document).ready(function() {

	
	var height = $( window ).height();
	var width = $( window ).width();

	if (width >= 1280) {
		$(function () {
							
			new ScrollMagic.Scene({triggerElement: "#intro", duration: 500, offset: 0, triggerHook: 0})
							.setPin("#intro")					
							.addTo(controller);	
							
			new ScrollMagic.Scene({triggerElement: "#sl_nav", duration: 300, offset: -300, triggerHook: 0.5})
							.setTween(TweenMax.from("#sl_nav .text_card", 1, {opacity: 0, y: 100}))						
							.addTo(controller);				
			new ScrollMagic.Scene({triggerElement: "#sl_nav", duration: 300, offset: 400, triggerHook: 0.5})
							.setTween(TweenMax.to("#sl_nav .text_card", 1, {opacity: 0, y: -100}))						
							.addTo(controller);

			//new ScrollMagic.Scene({triggerElement: "#numbers", duration: 500, offset: 0, triggerHook: 0})
			//	.setPin("#numbers")	
			//	.addTo(controller);			
					
			new ScrollMagic.Scene({triggerElement: "#answer", duration: 400, offset: 0, triggerHook: 0.5})
				.setTween(TweenMax.from("#answer h2.title_block", 1, {opacity: 0, y: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_section", duration: 200, offset: -100, triggerHook: 0.5})
				.setTween(TweenMax.from("#test_section h3.title_block", 1, {opacity: 0, y: 40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_section", duration: 200, offset: -200, triggerHook: 0.3})
				.setTween(TweenMax.from("#test_section h2.light_gray_title", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#best_sales", duration: 300, offset: 0, triggerHook: 0})
				.setPin("#best_sales")					
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#best_sales", duration: 200, offset: -100, triggerHook: 0.5})
				.setTween(TweenMax.from("#best_sales h3.title_block", 1, {opacity: 0, y: 40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#best_sales", duration: 200, offset: -200, triggerHook: 0.3})
				.setTween(TweenMax.from("#best_sales h2.light_gray_title", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#best_sales", duration: 200, offset: -200, triggerHook: 0.3})
				.setTween(TweenMax.from("#best_sales p.sub_title_block", 1, {opacity: 0, x: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#best_sales", duration: 300, offset: 50, triggerHook: 0.3})
				.setTween(TweenMax.from("#best_sales .image_slider_item img", 1, {opacity: 0, y: 100}))						
				.addTo(controller);				
			new ScrollMagic.Scene({triggerElement: "#best_sales", duration: 500, offset: 400, triggerHook: 0.3})
				.setTween(TweenMax.to("#best_sales .image_slider_item img", 1, {opacity: 0, y: -100}))						
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#best_sales .green_label", duration: 300, offset: 200, triggerHook: 0.3})
				.setTween(TweenMax.to("#best_sales .green_label", 1, {opacity: 0, x: 50}))						
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#best_sales .green_label", duration: 500, offset: 600, triggerHook: 0.3})
				.setTween(TweenMax.to("#best_sales .green_label", 1, {opacity: 0, x: 50}))						
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#popular_categories", duration: 300, offset: 500, triggerHook: 0.3})
				.setTween(TweenMax.to("p.sub_title_block", 1, {opacity: 0, x: -50}))						
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#popular_categories", duration: 300, offset: 700, triggerHook: 0.3})
				.setTween(TweenMax.to("p.sub_title_block", 1, {opacity: 0, x: -50}))						
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#available_test_drive", duration: 300, offset: 600, triggerHook: 0.5})
				.setTween(TweenMax.to("p.sub_title_block", 1, {opacity: 0, x: -50}))						
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#available_test_drive", duration: 300, offset: -200, triggerHook: 0.3})
				.setTween(TweenMax.from("#available_test_drive .image_slider_item img", 1, {opacity: 0, y: 100}))				
				.addTo(controller);				
			new ScrollMagic.Scene({triggerElement: "#available_test_drive", duration: 200, offset: 400, triggerHook: 0.3})
				.setTween(TweenMax.to("#available_test_drive .image_slider_item img", 1, {opacity: 0, y: -100}))			
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#available_test_drive .green_label", duration: 400, offset: -300, triggerHook: 0.3})
				.setTween(TweenMax.from("#available_test_drive .green_label", 1, {opacity: 0, x: 50}))
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#available_test_drive .green_label", duration: 500, offset: 0, triggerHook: 0.3})
				.setTween(TweenMax.to("#available_test_drive .green_label", 1, {opacity: 0, x: 50}))				
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#banner_body", duration: 300, offset: 0, triggerHook: 0.6})
				.setTween(TweenMax.from("#banner_body .banner", 1, {opacity: 0, y: 150}))
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#banner_body", duration: 300, offset: 300, triggerHook: 0.4})
				.setTween(TweenMax.to("#banner_body .banner", 1, {opacity: 0, y: -150}))				
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#new_goods", duration: 300, offset: -200, triggerHook: 0.3})
				.setTween(TweenMax.from("#new_goods .image_slider_item img", 1, {opacity: 0, y: 100}))				
				.addTo(controller);				
			new ScrollMagic.Scene({triggerElement: "#new_goods", duration: 200, offset: 400, triggerHook: 0.3})
				.setTween(TweenMax.to("#new_goods .image_slider_item img", 1, {opacity: 0, y: -100}))			
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#new_goods .green_label", duration: 400, offset: -300, triggerHook: 0.3})
				.setTween(TweenMax.from("#new_goods .green_label", 1, {opacity: 0, x: 50}))
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#new_goods .green_label", duration: 500, offset: 0, triggerHook: 0.3})
				.setTween(TweenMax.to("#new_goods .green_label", 1, {opacity: 0, x: 50}))				
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 1400, offset: 0, triggerHook: 0})
				.setPin("#test_drive")					
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 200, offset: -100, triggerHook: 0.5})
				.setTween(TweenMax.from("#test_drive h3.title_block", 1, {opacity: 0, y: 40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 200, offset: -200, triggerHook: 0.3})
				.setTween(TweenMax.from("#test_drive h2.light_gray_title", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 200, offset: -300, triggerHook: 0.1})
				.setTween(TweenMax.from("#test_drive .gradient_text", 1, {opacity: 0, y: 150}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 200, offset: 300*3, triggerHook: 0.1})
				.setTween(TweenMax.to("#test_drive h3.title_block", 1, {opacity: 0, y: -40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 200, offset: 350*3, triggerHook: 0.3})
				.setTween(TweenMax.to("#test_drive h2.light_gray_title", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 200, offset: 450*3, triggerHook: 0.1})
				.setTween(TweenMax.to("#test_drive .gradient_text", 1, {opacity: 0, y: -150}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 500, offset: -200, triggerHook: 0.5})
				.setTween(TweenMax.from("#test_drive .left_item_decor", 1, {opacity: 0, x: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 500, offset: 400, triggerHook: 0.1})
				.setTween(TweenMax.to("#test_drive .left_item_decor", 1, {opacity: 1, y: -200}))		
				.addTo(controller);	
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 300, offset: 400, triggerHook: 0.1})
				.setTween(TweenMax.from("#test_drive .wrapper_blur", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 300, offset: 200, triggerHook: 0.3})
				.setTween(TweenMax.from("#test_drive .right_item_decor", 1, {opacity: 1, x: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 600, offset: 0, triggerHook: 0.1})
				.setTween(TweenMax.to("#test_drive .right_item_decor", 1, {opacity: 1, y: -300}))		
				.addTo(controller);	
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 600, offset: 500, triggerHook: 0.3})
				.setTween(TweenMax.from("#test_drive .briz_img", 1, {opacity: 0, y: 600}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 600, offset: 700*2, triggerHook: 0.4})
				.setTween(TweenMax.to("#test_drive .briz_img", 1, {opacity: 0, y: -600}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 600, offset: 300, triggerHook: 0.3})
				.setTween(TweenMax.from("#test_drive .green_label_big", 1, {opacity: 1, x: 400}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#test_drive", duration: 600, offset: 500*3, triggerHook: 0.3})
				.setTween(TweenMax.to("#test_drive .green_label_big", 1, {opacity: 1, x: 400}))		
				.addTo(controller);

			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 2000, offset: 0, triggerHook: 0})
				.setPin("#about_us")					
				.addTo(controller);	

			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 200, offset: -100, triggerHook: 0.5})
				.setTween(TweenMax.from("#about_us h3.title_block", 1, {opacity: 0, y: 40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 200, offset: -200, triggerHook: 0.3})
				.setTween(TweenMax.from("#about_us h2.light_gray_title", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 200, offset: 300*3, triggerHook: 0.1})
				.setTween(TweenMax.to("#about_us h3.title_block", 1, {opacity: 0, y: -40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 200, offset: 350*3, triggerHook: 0.3})
				.setTween(TweenMax.to("#about_us h2.light_gray_title", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 300, offset: 100, triggerHook: 0.3})
			 	.setTween(TweenMax.from("#about_us .block_item_about:nth-of-type(1)", 1, {opacity: 0, y: 500, x: +width}))		
			 	.addTo(controller);


		// var timeline1 = new TimelineMax();
		// 	timeline1.add(TweenMax.from("#best_sales p", 1, {className: "+=flex4a"})).add(TweenMax.from("#best_sales .image_slider_item img", 1, {className: "+=flex4a"}));		
		// 	new ScrollMagic.Scene({triggerElement: "#best_sales", duration: 512, offset: 0})
		// 				.setTween(timeline1)			
		// 				.addTo(controller);
		// 	new ScrollMagic.Scene({triggerElement: "#best_sales", duration: 512, offset: 300})				
		// 				.setTween(TweenMax.from("#best_sales p", 1, {opacity: 0}))
		// 				.addTo(controller);
		// 	new ScrollMagic.Scene({triggerElement: "#best_sales", duration: 512, offset: -300})				
		// 				.setTween(TweenMax.from("#best_sales .image_slider_item img", 1, {opacity: 0, y: -50}))
		// 				.addTo(controller);
						
	//		var timeline12 = new TimelineMax();
	//		timeline12.add(TweenMax.to("#flex4 p", 1, {className: "+=flex4a"})).add(TweenMax.to("#flex4 img", 1, {className: "+=flex4a"}));
	//		new ScrollMagic.Scene({triggerElement: "#flex4", duration: 512, offset: height/4})
	//					.setTween(timeline12)
	//					.addTo(controller);
	//	
	//		new ScrollMagic.Scene({triggerElement: "#flex4_2", duration: 256, offset: -200, triggerHook: 0})				
	//					.setTween(TweenMax.to("#flex4_2 p", 1, {opacity: 0}))
	//					.addTo(controller);
	//		new ScrollMagic.Scene({triggerElement: "#flex4_2", duration: 256, offset: -100, triggerHook: 0})				
	//					.setTween(TweenMax.to("#flex4_2 img", 1, {opacity: 0, y: -50}))
	//					.addTo(controller);	



			new ScrollMagic.Scene({triggerElement: "#numbers", duration: 300, offset: height-500, triggerHook: 0.9})
				.setTween(TweenMax.from("#numbers .flexwrap > div:nth-child(1)", 1, {opacity: 0, x: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#numbers", duration: 300, offset: (height-500)*2, triggerHook: 0.9})
				.setTween(TweenMax.from("#numbers .flexwrap > div:nth-child(2)", 1, {opacity: 0, x: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#numbers", duration: 300, offset: (height-500)*3, triggerHook: 0.9})
				.setTween(TweenMax.from("#numbers .flexwrap > div:nth-child(3)", 1, {opacity: 0, x: 100}))		
				.addTo(controller);
				
			new ScrollMagic.Scene({triggerElement: "#briz", duration: 400, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#briz .flexcolumn:nth-child(1) img", 1, {opacity: 0, y: -100}))					
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#briz", duration: 400, offset: 50, triggerHook: 0.7})
				.setTween(TweenMax.from("#briz .flexcolumn:nth-child(2) img", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#briz", duration: 400, offset: 100, triggerHook: 0.7})
				.setTween(TweenMax.from("#briz .flexcolumn:nth-child(3) img", 1, {opacity: 0, y: -100}))		
				.addTo(controller);			
			new ScrollMagic.Scene({triggerElement: "#briz", duration: 200, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#briz .flexcolumn:nth-child(1) h4", 1, {opacity: 0}))					
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#briz", duration: 200, offset: 50, triggerHook: 0.7})
				.setTween(TweenMax.from("#briz .flexcolumn:nth-child(2) h4", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#briz", duration: 200, offset: 100, triggerHook: 0.7})
				.setTween(TweenMax.from("#briz .flexcolumn:nth-child(3) h4", 1, {opacity: 0}))		
				.addTo(controller);
				
			new ScrollMagic.Scene({triggerElement: "#recuper", duration: 400, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#recuper .flexcolumn:nth-child(1) img", 1, {opacity: 0, y: -100}))					
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recuper", duration: 400, offset: 50, triggerHook: 0.7})
				.setTween(TweenMax.from("#recuper .flexcolumn:nth-child(2) img", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recuper", duration: 400, offset: 100, triggerHook: 0.7})
				.setTween(TweenMax.from("#recuper .flexcolumn:nth-child(3) img", 1, {opacity: 0, y: -100}))		
				.addTo(controller);			
			new ScrollMagic.Scene({triggerElement: "#recuper", duration: 200, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#recuper .flexcolumn:nth-child(1) h4", 1, {opacity: 0}))					
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recuper", duration: 200, offset: 50, triggerHook: 0.7})
				.setTween(TweenMax.from("#recuper .flexcolumn:nth-child(2) h4", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recuper", duration: 200, offset: 100, triggerHook: 0.7})
				.setTween(TweenMax.from("#recuper .flexcolumn:nth-child(3) h4", 1, {opacity: 0}))		
				.addTo(controller);

			var colors = [
			  { deg:"192" },
			  { deg:"182" },
			  { deg:"172" },
			  { deg:"162" },
			  { deg:"142" }
			];
			var gradientTween2 = new TimelineMax();
			var gradientTween3 = new TimelineMax();
			for (let i = 0; i < colors.length; i++) {
			  gradientTween2.to("#montage h2", 1, {
				backgroundImage:
				  "linear-gradient("+colors[i].deg+"deg, #0094D9 2.61%, #2E3191 32.28%, #EC1C24 59.13%, #F48120 93.05%"
			  });
			  gradientTween3.to("#montage .colortext", 1, {
				backgroundImage:
				  "linear-gradient("+colors[i].deg+"deg, #0094D9 2.61%, #2E3191 32.28%, #EC1C24 59.13%, #F48120 93.05%"
			  });
			}
			new ScrollMagic.Scene({triggerElement: "#montage", duration: 500, offset: 0, triggerHook: 0.5})
				.setTween(gradientTween2)
				.addTo(controller)
			new ScrollMagic.Scene({triggerElement: "#montage", duration: 500, offset: 0, triggerHook: 0.6})
				.setTween(gradientTween3)
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#montage", duration: 500, offset: 0, triggerHook: 0.6})
				.setTween(TweenMax.from("#montage .border", 1, {opacity: 0, x: 100}))
				.addTo(controller);
	
					
			new ScrollMagic.Scene({triggerElement: "#rules", duration: 200, offset: 0, triggerHook: 0.6})
				.setTween(TweenMax.from("#rules h2.black", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#rules", duration: 200, offset: 100, triggerHook: 0.6})
				.setTween(TweenMax.from("#rules h2.gray", 1, {opacity: 0, y: 100}))		
				.addTo(controller);				
			new ScrollMagic.Scene({triggerElement: "#rules", duration: 500, offset: height, triggerHook: 0})
				.setTween(TweenMax.to("#rules h2.black", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#rules", duration: 500, offset: height, triggerHook: 0})
				.setTween(TweenMax.to("#rules h2.gray", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#rules", duration: 500, offset: height+200, triggerHook: 0})
				.setTween(TweenMax.to("#rules p", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
				
			new ScrollMagic.Scene({triggerElement: "#form", duration: 400, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#form h2", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#form", duration: 200, offset: 0, triggerHook: 0.4})
				.setTween(TweenMax.from("#form input", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#form", duration: 200, offset: 0, triggerHook: 0.2})
				.setTween(TweenMax.from("#form button", 1, {opacity: 0}))		
				.addTo(controller);
								
			new ScrollMagic.Scene({triggerElement: "#video", duration: 200, offset: 0, triggerHook: 0.6})
				.setTween(TweenMax.from("#video h2.black", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#video", duration: 200, offset: 100, triggerHook: 0.6})
				.setTween(TweenMax.from("#video h2.gray", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
				
			new ScrollMagic.Scene({triggerElement: "#dontknow", duration: 500, offset: 0, triggerHook: 0})
				.setPin("#dontknow")	
				.addTo(controller);					
			new ScrollMagic.Scene({triggerElement: "#dontknow h2", duration: 400, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#dontknow h2", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#dontknow p", duration: 200, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#dontknow p", 1, {opacity: 0}))		
				.addTo(controller);	

			new ScrollMagic.Scene({triggerElement: "#dontknow #line", duration: 3000, offset: 0, triggerHook: 0.8})
				.setClassToggle("#line", "full")
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#dontknow", duration: 400, offset: 0, triggerHook: 0})
				.setTween(TweenMax.from("#dontknow a", 1, {color: "#000"}))		
				.addTo(controller);
				
		new ScrollMagic.Scene({triggerElement: "#footer", duration: 300, offset: 0, triggerHook: 0.91})
			.setTween(TweenMax.from("#whatsapp", 1, {opacity: 0, x: 100}))	
			.addTo(controller);
		new ScrollMagic.Scene({triggerElement: "#footer", duration: 300, offset: 0, triggerHook: 0.95})
			.setTween(TweenMax.from("#insta", 1, {opacity: 0, x: 100}))		
			.addTo(controller);
		});
	} else {
		$(function () {
			var colors = [
			  { deg:"142" },
			  { deg:"162" },
			  { deg:"172" },
			  { deg:"182" }
			];
			var gradientTween = new TimelineMax();
			for (let i = 0; i < colors.length; i++) {
			  gradientTween.to("header h1", 1, {
				backgroundImage:
				  "linear-gradient("+colors[i].deg+"deg, #0094D9 2.61%, #2E3191 32.28%, #EC1C24 59.13%, #F48120 93.05%"
			  });
			}
			new ScrollMagic.Scene({triggerElement: "header", duration: 300, offset: 0, triggerHook: 0})
				.setTween(gradientTween)
				.addTo(controller);
			
			new ScrollMagic.Scene({triggerElement: "header", duration: 500, offset: 0, triggerHook: 0})
							.setPin("header")					
							.addTo(controller);	
							
			new ScrollMagic.Scene({triggerElement: "#back", duration: 300, offset: 0, triggerHook: 0.5})
							.setTween(TweenMax.from("#back .text", 1, {opacity: 0, y: -100}))						
							.addTo(controller);				


				
			new ScrollMagic.Scene({triggerElement: "#numbers", duration: 200, offset: 0, triggerHook: 0.9})
				.setTween(TweenMax.from("#numbers h2.black", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#numbers", duration: 200, offset: 100, triggerHook: 0.9})
				.setTween(TweenMax.from("#numbers h2.gray", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#numbers .flexwrap > div:nth-child(1)", duration: 300, offset: 0, triggerHook: 0.9})
				.setTween(TweenMax.from("#numbers .flexwrap > div:nth-child(1)", 1, {opacity: 0, x: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#numbers .flexwrap > div:nth-child(2)", duration: 300, offset: 0, triggerHook: 0.9})
				.setTween(TweenMax.from("#numbers .flexwrap > div:nth-child(2)", 1, {opacity: 0, x: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#numbers .flexwrap > div:nth-child(3)", duration: 300, offset: 0, triggerHook: 0.9})
				.setTween(TweenMax.from("#numbers .flexwrap > div:nth-child(3)", 1, {opacity: 0, x: 100}))		
				.addTo(controller);
				
			new ScrollMagic.Scene({triggerElement: "#briz .flexcolumn:nth-child(1) img", duration: 400, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#briz .flexcolumn:nth-child(1) img", 1, {opacity: 0, y: -100}))					
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#briz .flexcolumn:nth-child(2) img", duration: 400, offset: 50, triggerHook: 0.7})
				.setTween(TweenMax.from("#briz .flexcolumn:nth-child(2) img", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#briz .flexcolumn:nth-child(3) img", duration: 400, offset: 100, triggerHook: 0.7})
				.setTween(TweenMax.from("#briz .flexcolumn:nth-child(3) img", 1, {opacity: 0, y: -100}))		
				.addTo(controller);			
			new ScrollMagic.Scene({triggerElement: "#briz .flexcolumn:nth-child(1) h4", duration: 200, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#briz .flexcolumn:nth-child(1) h4", 1, {opacity: 0}))					
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#briz .flexcolumn:nth-child(2) h4", duration: 200, offset: 50, triggerHook: 0.7})
				.setTween(TweenMax.from("#briz .flexcolumn:nth-child(2) h4", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#briz .flexcolumn:nth-child(3) h4", duration: 200, offset: 100, triggerHook: 0.7})
				.setTween(TweenMax.from("#briz .flexcolumn:nth-child(3) h4", 1, {opacity: 0}))		
				.addTo(controller);
				
			new ScrollMagic.Scene({triggerElement: "#recuper .flexcolumn:nth-child(1) img", duration: 400, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#recuper .flexcolumn:nth-child(1) img", 1, {opacity: 0, y: -100}))					
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recuper .flexcolumn:nth-child(2) img", duration: 400, offset: 50, triggerHook: 0.7})
				.setTween(TweenMax.from("#recuper .flexcolumn:nth-child(2) img", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recuper .flexcolumn:nth-child(3) img", duration: 400, offset: 100, triggerHook: 0.7})
				.setTween(TweenMax.from("#recuper .flexcolumn:nth-child(3) img", 1, {opacity: 0, y: -100}))		
				.addTo(controller);			
			new ScrollMagic.Scene({triggerElement: "#recuper .flexcolumn:nth-child(1) img", duration: 200, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#recuper .flexcolumn:nth-child(1) h4", 1, {opacity: 0}))					
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recuper .flexcolumn:nth-child(2) img", duration: 200, offset: 50, triggerHook: 0.7})
				.setTween(TweenMax.from("#recuper .flexcolumn:nth-child(2) h4", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recuper .flexcolumn:nth-child(3) img", duration: 200, offset: 100, triggerHook: 0.7})
				.setTween(TweenMax.from("#recuper .flexcolumn:nth-child(3) h4", 1, {opacity: 0}))		
				.addTo(controller);

			var colors = [
			  { deg:"192" },
			  { deg:"182" },
			  { deg:"172" },
			  { deg:"162" },
			  { deg:"142" }
			];
			var gradientTween2 = new TimelineMax();
			var gradientTween3 = new TimelineMax();
			for (let i = 0; i < colors.length; i++) {
			  gradientTween2.to("#montage h2", 1, {
				backgroundImage:
				  "linear-gradient("+colors[i].deg+"deg, #0094D9 2.61%, #2E3191 32.28%, #EC1C24 59.13%, #F48120 93.05%"
			  });
			  gradientTween3.to("#montage .colortext", 1, {
				backgroundImage:
				  "linear-gradient("+colors[i].deg+"deg, #0094D9 2.61%, #2E3191 32.28%, #EC1C24 59.13%, #F48120 93.05%"
			  });
			}
			new ScrollMagic.Scene({triggerElement: "#montage", duration: 500, offset: 0, triggerHook: 0.5})
				.setTween(gradientTween2)
				.addTo(controller)
			new ScrollMagic.Scene({triggerElement: "#montage", duration: 500, offset: 0, triggerHook: 0.6})
				.setTween(gradientTween3)
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#montage", duration: 500, offset: 0, triggerHook: 0.6})
				.setTween(TweenMax.from("#montage .border", 1, {opacity: 0, x: 100}))
				.addTo(controller);
				
			new ScrollMagic.Scene({triggerElement: "#rules", duration: 200, offset: 0, triggerHook: 0.6})
				.setTween(TweenMax.from("#rules h2.black", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#rules", duration: 200, offset: 100, triggerHook: 0.6})
				.setTween(TweenMax.from("#rules h2.gray", 1, {opacity: 0, y: 100}))		
				.addTo(controller);				
			new ScrollMagic.Scene({triggerElement: "#rules", duration: 500, offset: height, triggerHook: 0})
				.setTween(TweenMax.to("#rules h2.black", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#rules", duration: 500, offset: height, triggerHook: 0})
				.setTween(TweenMax.to("#rules h2.gray", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#rules", duration: 500, offset: height+200, triggerHook: 0})
				.setTween(TweenMax.to("#rules p", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
				
			new ScrollMagic.Scene({triggerElement: "#form", duration: 400, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#form h2", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#form", duration: 200, offset: 0, triggerHook: 0.4})
				.setTween(TweenMax.from("#form input", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#form", duration: 200, offset: 0, triggerHook: 0.2})
				.setTween(TweenMax.from("#form button", 1, {opacity: 0}))		
				.addTo(controller);
								
			new ScrollMagic.Scene({triggerElement: "#video", duration: 200, offset: 0, triggerHook: 0.6})
				.setTween(TweenMax.from("#video h2.black", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#video", duration: 200, offset: 100, triggerHook: 0.6})
				.setTween(TweenMax.from("#video h2.gray", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
				
			new ScrollMagic.Scene({triggerElement: "#dontknow", duration: 100, offset: 0, triggerHook: 0})
				.setPin("#dontknow")	
				.addTo(controller);					
			new ScrollMagic.Scene({triggerElement: "#dontknow h2", duration: 400, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#dontknow h2", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#dontknow p", duration: 200, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#dontknow p", 1, {opacity: 0}))		
				.addTo(controller);	
			new ScrollMagic.Scene({triggerElement: "#dontknow #line", duration: 3000, offset: 0, triggerHook: 0.8})
				.setClassToggle("#line", "full")
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#dontknow", duration: 400, offset: 0, triggerHook: 0.7})
				.setTween(TweenMax.from("#dontknow a", 1, {color: "#000"}))		
				.addTo(controller);
				
		new ScrollMagic.Scene({triggerElement: "#footer", duration: 300, offset: 0, triggerHook: 0.91})
			.setTween(TweenMax.from("#whatsapp", 1, {opacity: 0, x: 100}))	
			.addTo(controller);
		new ScrollMagic.Scene({triggerElement: "#footer", duration: 300, offset: 0, triggerHook: 0.95})
			.setTween(TweenMax.from("#insta", 1, {opacity: 0, x: 100}))		
			.addTo(controller);
		});	
	};
});







    