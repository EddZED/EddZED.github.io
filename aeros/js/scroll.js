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
				.setTween(TweenMax.to("#popular_categories p.sub_title_block", 1, {opacity: 0, x: -50}))						
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#popular_categories", duration: 300, offset: 700, triggerHook: 0.3})
				.setTween(TweenMax.to("#popular_categories p.sub_title_block", 1, {opacity: 0, x: -50}))						
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#available_test_drive", duration: 300, offset: 600, triggerHook: 0.5})
				.setTween(TweenMax.to("#available_test_drive p.sub_title_block", 1, {opacity: 0, x: -50}))						
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
			new ScrollMagic.Scene({triggerElement: "#new_goods", duration: 300, offset: 0, triggerHook: 0.5})
				.setTween(TweenMax.from("#new_goods p.sub_title_block", 1, {opacity: 0, y: 100}))				
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

			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 1700, offset: 0, triggerHook: 0})
				.setPin("#about_us")					
				.addTo(controller);	

			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 300, offset: -200, triggerHook: 0.5})
				.setTween(TweenMax.from("#about_us h3.title_block", 1, {opacity: 0, y: 40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 300, offset: -400, triggerHook: 0.3})
				.setTween(TweenMax.from("#about_us h2.light_gray_title", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 300, offset: 450*3, triggerHook: 0.1})
				.setTween(TweenMax.to("#about_us h3.title_block", 1, {opacity: 0, y: -40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 300, offset: 500*3, triggerHook: 0.3})
				.setTween(TweenMax.to("#about_us h2.light_gray_title", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			// new ScrollMagic.Scene({triggerElement: "#about_us", duration: 300, offset: 300, triggerHook: 0.3})
			//  	.setTween(TweenMax.to("#about_us .block_item_about:nth-of-type(1)", 1, {opacity: 0, y: 0, xPercent: 0}))		
			//  	.addTo(controller);
			var wipeAnimation = new TimelineMax().from("#cont_item .first_item", 1, {y: "100%"}).from("#cont_item .first_item", 0.5, {x: "100%"})	
			new ScrollMagic.Scene({triggerElement: "#about_us",	triggerHook: "onLeave",	duration: "80%"})
				.setTween(wipeAnimation)
				.addTo(controller);

			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 400, offset: 250*3, triggerHook: 0})
				.setTween(TweenMax.from("#cont_item .other_panel", 1, {opacity: 0, y: 600}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 400, offset: 1000*2, triggerHook: 0.1})
				.setTween(TweenMax.to("#cont_item .other_panel", 1, {opacity: 0, y: -600}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about_us", duration: 400, offset: 1000*2, triggerHook: 0.1})
				.setTween(TweenMax.to("#cont_item .first_item", 1, {opacity: 0, y: -600}))		
				.addTo(controller);




			//map styles
			new ScrollMagic.Scene({triggerElement: "#filials", duration: 2600, offset: 0, triggerHook: 0})
				.setPin("#filials")					
				.addTo(controller);	
			new ScrollMagic.Scene({triggerElement: "#filials", duration: 300, offset: -200, triggerHook: 0.5})
				.setTween(TweenMax.from("#filials h3.title_block", 1, {opacity: 0, y: 40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#filials", duration: 300, offset: -400, triggerHook: 0.3})
				.setTween(TweenMax.from("#filials h2.light_gray_title", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#filials", duration: 300, offset: 570*4, triggerHook: 0.1})
				.setTween(TweenMax.to("#filials h3.title_block", 1, {opacity: 0, y: -40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#filials", duration: 300, offset: 630*4, triggerHook: 0.3})
				.setTween(TweenMax.to("#filials h2.light_gray_title", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#filials", duration: 690, offset: 280})
			.setTween(TweenMax.from("#filials .map_city", 1, {opacity: 0, scale: 3}))
			.addTo(controller);
			var line = [
		  { w:"0",d:"100" },
		  { w:"60",d:"100" },
		  { w:"85",d:"100" },
		  { w:"152",d:"100" },
		  { w:"165",d:"100" },
		  { w:"240",d:"100" },
		  { w:"265",d:"100" },
		  { w:"405",d:"100" },
		  { w:"460",d:"100" },
		  { w:"560",d:"100" },
		  { w:"660",d:"100" },
		  { w:"735",d:"100" },
		  { w:"860",d:"100" },
		  { w:"1050",d:"100" },
		  { w:"1066.5",d:"100" }
		  ];
			/*var lineTween = new TimelineMax();
		for (let i = 0; i < line.length; i++) {
		  lineTween.to("#map .line", 0.1, {
			width: line[i].w+"px"
		  });
		}		
		new ScrollMagic.Scene({triggerElement: "#map", offset: 1000})
			.setTween(lineTween)
			.addTo(controller);*/
		for (let i = 0; i < line.length; i++) {
			var lineoffset = 1150 + (100 * i);
			var lineowidth = parseFloat(line[i].w);

			new ScrollMagic.Scene({triggerElement: "#filials", duration: 10, offset: lineoffset})
				.setTween(TweenMax.to("#filials .line_map", 1, {width: lineowidth+"px"}))
				.addTo(controller);
			/*new ScrollMagic.Scene({triggerElement: "#map", duration: 100, offset: lineoffset})
				.setClassToggle("#map .line", "line"+i)
				
				.addTo(controller);*/
			new ScrollMagic.Scene({triggerElement: "#filials", duration: 150, offset: lineoffset })
				.setTween(TweenMax.from("#city" + i, 1, {opacity: 0, x:30, scale: 1.5}))
				.addTo(controller);
		};
			new ScrollMagic.Scene({triggerElement: "#filials", duration: 690, offset: 850*4})
			.setTween(TweenMax.to("#filials .our_map", 1, {opacity: 0, scale: 0.5}))
			.addTo(controller);

		// 	var timelinegray = new TimelineMax();
		// timelinegray.add(TweenMax.from("#trust .wrap_company_block", 1, { 'filter': 'grayscale(100%)'})).add(TweenMax.to("#trust .wrap_company_block", 1, { 'filter': 'grayscale(50%)'})).add(TweenMax.to("#trust .wrap_company_block", 1, { 'filter': 'grayscale(0%)'}));

			new ScrollMagic.Scene({triggerElement: "#trust", duration: 400, offset: 100, triggerHook: 0.7})
				.setTween(TweenMax.from("#trust .wrap_company_block", 1, {opacity: 0, y: 100, 'filter': 'grayscale(100%)'}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#trust", duration: 300, offset: 500, triggerHook: 0.4})
				.setTween(TweenMax.to("#trust .wrap_company_block", 1, {opacity: 0, y: -100}))		
				.addTo(controller);

			
			new ScrollMagic.Scene({triggerElement: "#recomend_reading", duration: 500, offset: 0, triggerHook: 0})
				.setPin("#recomend_reading")	
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recomend_reading", duration: 300, offset: -100, triggerHook: 0.5})
				.setTween(TweenMax.from("#recomend_reading h3.title_block", 1, {opacity: 0, y: 40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recomend_reading", duration: 300, offset: -200, triggerHook: 0.3})
				.setTween(TweenMax.from("#recomend_reading h2.light_gray_title", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recomend_reading", duration: 300, offset: 400, triggerHook: 0.1})
				.setTween(TweenMax.to("#recomend_reading h3.title_block", 1, {opacity: 0, y: -40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recomend_reading", duration: 300, offset: 700, triggerHook: 0.3})
				.setTween(TweenMax.to("#recomend_reading h2.light_gray_title", 1, {opacity: 0, y: -100}))		
				.addTo(controller);	
			new ScrollMagic.Scene({triggerElement: "#recomend_reading", duration: 300, offset: 100, triggerHook: 0.1})
				.setTween(TweenMax.from("#recomend_reading .upper_head", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recomend_reading", duration: 300, offset: 100, triggerHook: 0.1})
				.setTween(TweenMax.from("#recomend_reading .knowledge_base .gradient_text", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recomend_reading", duration: 300, offset: 400*3, triggerHook: 0.4})
				.setTween(TweenMax.from("#recomend_reading .messanger_link_content .messanger_link:nth-of-type(1)", 1, {opacity: 0, x:-40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recomend_reading", duration: 300, offset: 450*3, triggerHook: 0.4})
				.setTween(TweenMax.from("#recomend_reading .messanger_link_content .messanger_link:nth-of-type(2)", 1, {opacity: 0, x:-40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#recomend_reading", duration: 300, offset: 500*3, triggerHook: 0.4})
				.setTween(TweenMax.from("#recomend_reading .messanger_link_content .messanger_link:nth-of-type(3)", 1, {opacity: 0, x:-40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#call_us", duration: 2000, offset: 0, triggerHook: 0})
				.setPin("#call_us")	
				.addTo(controller);
			
			new ScrollMagic.Scene({triggerElement: "#call_us", duration: 300, offset: 50, triggerHook: 0.1})
				.setTween(TweenMax.from("#call_us h2.light_gray_title", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#call_us", duration: 600, offset: 250*2, triggerHook: 0})
				.setTween(TweenMax.to("#call_us .circle_item", 1, {x: 40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#call_us", duration: 600, offset: 250*2, triggerHook: 0})
				.setTween(TweenMax.from("#call_us h3.title_block", 1, {opacity: 0, x: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#call_us", duration: 400, offset: 350*2, triggerHook: 0})
				.setTween(TweenMax.from("#call_us h3.title_block .border_bottom", 1, {opacity: 0, x: -200}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#call_us", duration: 400, offset: 450*2, triggerHook: 0})
				.setTween(TweenMax.from("#call_us .wrapper_messanger_item", 1, {opacity: 0, x: -200}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#call_us", duration: 400, offset: 470*2, triggerHook: 0})
				.setTween(TweenMax.from("#call_us .wrapper_messanger_item .messanger_link:nth-of-type(1)", 1, {opacity: 0, x: -50}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#call_us", duration: 400, offset: 490*2, triggerHook: 0})
				.setTween(TweenMax.from("#call_us .wrapper_messanger_item .messanger_link:nth-of-type(2)", 1, {opacity: 0, x: -70}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#call_us", duration: 400, offset: 510*2, triggerHook: 0})
				.setTween(TweenMax.from("#call_us .wrapper_messanger_item .messanger_link:nth-of-type(3)", 1, {opacity: 0, x: -90}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#call_us", duration: 400, offset: 600*2, triggerHook: 0})
				.setTween(TweenMax.from("#call_us .wrapper_messanger_item .text_messanger_item", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#call_us", duration: 400, offset: 650*2, triggerHook: 0})
				.setTween(TweenMax.from("#call_us .wrapper_bullit", 1, {opacity: 0, y: 100}))		
				.addTo(controller);

			new ScrollMagic.Scene({triggerElement: "#assortment", duration: 300, offset: -100, triggerHook: 0.5})
				.setTween(TweenMax.from("#assortment h3.title_block", 1, {opacity: 0, y: 40}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#assortment", duration: 300, offset: -200, triggerHook: 0.3})
				.setTween(TweenMax.from("#assortment h2.light_gray_title", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#assortment", duration: 300, offset: 100, triggerHook: 0.5})
				.setTween(TweenMax.from("#assortment .top_item", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#assortment", duration: 300, offset: 200, triggerHook: 0.5})
				.setTween(TweenMax.from("#assortment .name_goods_assort", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#assortment", duration: 300, offset: 300, triggerHook: 0.5})
				.setTween(TweenMax.from("#assortment .link_assortment", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#assortment", duration: 100, offset: 500, triggerHook: 0.3})
				.setTween(TweenMax.to("#assortment .top_item", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#assortment", duration: 300, offset: 500, triggerHook: 0.3})
				.setTween(TweenMax.to("#assortment .name_goods_assort", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#assortment", duration: 300, offset: 500, triggerHook: 0.3})
				.setTween(TweenMax.to("#assortment .link_assortment", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#assortment", duration: 300, offset: 500, triggerHook: 0.5})
				.setTween(TweenMax.from("#assortment .more_brend", 1, {opacity: 0}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#assortment", duration: 600, offset: 700, triggerHook: 0.5})
				.setClassToggle("#germany", "active")			
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#assortment", duration: 600, offset: 700, triggerHook: 0.5})
				.setClassToggle("#japanese", "active")			
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#assortment", duration: 200, offset: 750, triggerHook: 0.3})
				.setTween(TweenMax.to("#assortment .more_brend", 1, {opacity: 0}))		
				.addTo(controller);
			
			new ScrollMagic.Scene({triggerElement: "#about", duration: 200, offset: 0, triggerHook: 0.5})
				.setTween(TweenMax.from("#about h3.title_block", 1, {opacity: 0, y: -100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about", duration: 600, offset: 0, triggerHook: 0.6})
				.setTween(TweenMax.from("#about .our_brend_banner", 1, {opacity: 0, y: 100}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about", duration: 600, offset: 0, triggerHook: 0.6})
				.setTween(TweenMax.from("#about .our_brend_banner img", 1, {opacity: 0, y: 300, x: 300}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about", duration: 600, offset: 300, triggerHook: 0.3})
				.setTween(TweenMax.to("#about .our_brend_banner", 1, {opacity: 0, y: 200}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about", duration: 600, offset: 350, triggerHook: 0.5})
				.setClassToggle("#color", "gradient_text")			
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#about", duration: 600, offset: 400, triggerHook: 0.5})
				.setTween(TweenMax.to("#about .gradient_text", 1, {opacity: 0, y: 200}))		
				.addTo(controller);

			new ScrollMagic.Scene({triggerElement: "#banner_sldiers_section", duration: 500, offset: 200, triggerHook: 0.7})
				.setTween(TweenMax.from("#banner_sldiers_section .text_block_banner", 1, {opacity: 0, y: -200}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#banner_sldiers_section", duration: 300, offset: -300, triggerHook: 0})
				.setTween(TweenMax.from("#gray_1", 1, {opacity: 0, 'filter': 'grayscale(100%)'}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#banner_sldiers_section", duration: 300, offset: -300, triggerHook: 0})
				.setTween(TweenMax.from(".gradient_btn_other", 1, {opacity: 0, 'filter': 'grayscale(100%)'}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#banner_sldiers_section", duration: 300, offset: 100, triggerHook: 0.2})
				.setTween(TweenMax.from("#gray_2", 1, {opacity: 0, 'filter': 'grayscale(100%)'}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#banner_sldiers_section", duration: 300, offset: 200, triggerHook: 0.2})
				.setTween(TweenMax.from("#gray_3", 1, {opacity: 0, 'filter': 'grayscale(100%)'}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#banner_sldiers_section", duration: 300, offset: 350, triggerHook: 0.2})
				.setTween(TweenMax.from("#gray_4", 1, {opacity: 0, 'filter': 'grayscale(100%)'}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#banner_sldiers_section", duration: 300, offset: 350, triggerHook: 0.2})
				.setTween(TweenMax.from("#gray_5", 1, {opacity: 0, 'filter': 'grayscale(100%)'}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#banner_sldiers_section", duration: 300, offset: 450, triggerHook: 0.2})
				.setTween(TweenMax.from("#gray_6", 1, {opacity: 0, 'filter': 'grayscale(100%)'}))		
				.addTo(controller);
			new ScrollMagic.Scene({triggerElement: "#banner_sldiers_section", duration: 300, offset: 450, triggerHook: 0.2})
				.setTween(TweenMax.from("#gray_7", 1, {opacity: 0, 'filter': 'grayscale(100%)'}))		
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







    