$(document).ready(function() {
    $('.calc__block_el').on('click', function() {
		var tbb = parseInt($('.tbb.active').data('vi'));
        $('.tbc.active .calc__block_el').removeClass('active');
        $(this).addClass('active');
		if (tbb > 0) {
		    $('#tabl_canvas').css('background', 'transparent');
			if ($(window).width() > 767)
			$('#tabl_canvas .tabl_img').attr('style', 'width: auto !important');
	    } else {
			var fon = '[data-id="' + $('#select_fon' + tbb).val() + '"]';
			if ($(fon).parent().data('fon') != undefined) {
		        $('#tabl_canvas').css('background', 'url(/image/' + $(fon).parent().data('fon') + ') 100% 100%');
			} else {
				$('#tabl_canvas').css('background', '#fff');
			}
			//$('#tabl_canvas .tabl_img').attr('style', 'width: 100% !important');
		}
    });
	
    $('.calc__number_enter').on('focus', function() {
        var radio = document.getElementsByName('pre_quantity');
        for (var index = 0; index < 3; index++) {
            //radio[index].checked = false;
        }
    });
	
	$('.tbb').on('click', function() {
		var tbb = $(this).attr('data-vi');
		var e = $(this);
		var vid = $(this).data('vi');
		var fon = $('#tabl_canvas').attr('style');
		var prov = ($(this).hasClass('prov') ? '&can=1' : '');
	 	if (!$('.tbb').hasClass('ger')) {
		$.get('/index.php?route=common/home/loads&vid=' + vid + prov + '&cid=' + $('[name="constructor"]').val(), function(g){
			tbb = $('.tbb.active').data('vi');
			$('#product2').replaceWith(g);
			$('#tabl_canvas').attr('style', fon);
			$('.tbc.active .active').trigger('click');
			$('[data-vid]').fadeOut(0);
	        $('[data-vid="' + tbb + '"]:not(.dops)').fadeIn(0);
		});
		} else {
			$('[data-vid]').fadeOut(0);
	        $('[data-vid="' + tbb + '"]:not(.dops)').fadeIn(0);
			setTimeout(function(){
			    $('.tbc.active').find('.calc__block_el.active').trigger('click');
			}, 500);
		}
	var text_options = $('[data-vid="' + tbb + '"] .extra-option:checked').map(function() {
            return $(this).siblings('.checkbox_label').text();
        }).get();

	$('#product_option [type="radio"][name^="option["]:checked, #product_option [type="checkbox"][name^="option["]:checked, #product_option select[name^="option["]').each(function(i, item){
	    var el = $(item);
	    if(el[0].tagName.toLowerCase() == 'select'){
        	text_options.push(el.siblings('.calc__add_label').text() + ': ' + el.find('option[value="'+el.val()+'"]').text());
        	return;
    	    }
	    text_options.push(el.siblings('.checkbox_label').text());
	});
        
	if($('#qty').val()){
        qty = $('#qty').val() + ' С€С‚.';
        $('input[name="quantity"]').val($('#qty').val());
    } else {
        qty = $('input[name="pre_quantity"]:checked').val() + ' С€С‚.';
        $('input[name="quantity"]').val($('input[name="pre_quantity"]:checked').val());
    }
        
	text_options.push(qty);
    $('#opti').text(text_options.join(', '));
    });
});
$('#tabl_canvas img').load(function(){
    $(this).data('loaded', 'loaded').removeClass('v_lazy');
});
function getPosition( el ) {
    var body = document.documentElement || document.body;
    var scrollX = window.pageXOffset || body.scrollLeft;
    var scrollY = window.pageYOffset || body.scrollTop;
    _x = el.getBoundingClientRect().left;// + scrollX;
    _y = el.getBoundingClientRect().top;// + scrollY;
    return { top: _y, left: _x , scrollTop: _y+scrollY, scrollLeft: _x+scrollX};
}

function printProject(){
    var project = document.querySelector('#tabl_canvas');
    var pos = getPosition(project);
    var w = project.clientWidth + 30;
    var h = project.clientHeight + 10;
    return html2canvas(project, {
        allowTaint: true,
        width: w,
        height: h,
        scrollX: 0,
        scrollY: pos.scrollTop,
        x: pos.left,
        y: pos.scrollTop
    });
}

$(document).on('click', '.ner', function(){
		$(this).prev().fadeIn(0);
		$(this).remove();
	});
	
function sendForm2(id, pid) {
	$(id).fadeOut(0).after('<span class="ner" data-id="' + pid + '" style="cursor: pointer;font-family: \'Proxima_Nova_semibold\';color: #b9d500;"><img style="width: auto;display: inline-block;" src="/image/checked.png" /> Р”РѕР±Р°РІР»РµРЅРѕ РІ Р·Р°РєР°Р·</span>');
}

$(document).on('change', '[name="pre_quantity"]', function(){
	var tbb = $('.tbb.active').data('vi');
	if ($(this).val() == 1) {
		$('.dops[data-vid="' + tbb + '"]').fadeOut(60);
	} else {
		$('.dops[data-vid="' + tbb + '"]').fadeIn(60);
	}
});

function setImg(el) {
    $(el).find('input').prop('checked', true);
    var model_id = $(el).find('input').val();
    getPriceTable(model_id);
	$('.calc__info').addClass('noi');
	if ($('.calc__address > div:first input').length) {
	    $('#canvas').fadeOut(60);
	}
	var tbb = $('.tbb.active').data('vi');
    var text_options = [];
    $('[data-vid="' + tbb + '"] .extra-option:checked').each(function() {
        text_options.push($(this).siblings('.checkbox_label').text());
    });
    $('#product_option [type="radio"][name^="option["]:checked, #product_option [type="checkbox"][name^="option["]:checked, #product_option select[name^="option["]').each(function(i, item){
	var el = $(item);
	if(el[0].tagName.toLowerCase() == 'select'){
            text_options.push(el.siblings('.calc__add_label').text() + ': ' + el.find('option[value="'+el.val()+'"]').text());
            return;
        }
        text_options.push(el.siblings('.checkbox_label').text());
    });
        
	text_options.push(qty);
    $('#opti').text(text_options.join(', '));
	
	$('#tabl_canvas .tabl_img').data('loaded', 'loading').attr('src', $(el).find('img').attr('src'));
}

function getPriceTable(product_id, option_price){
    $.post('/index.php?route=extension/module/cons/price_table', {product_id: product_id, option_price: option_price}, function(result){
        if(result.status == 'success'){
            $('.price_table').html(result.price_table);
            if(option_price == undefined){
                $('#product_option').html(result.product_options);
                changePrice();
            }
           
            /*var price = '';
            if(result.special.length){
                price = '<span class="price-new" style="display: inline-block;">' + result.special + '</span>';
                    //+ '<span class="price-old" style="display: inline-block;">' + result.price + '</span>';
            }else{
                price = '<span class="price" style="display: inline-block;">' + result.price + '</span>';
            }
            $('.product_model_price').html('<span class="calc__add_label">Р¦РµРЅР°:</span> ' + price);
            $('.calc__discount_sale').html(result.sale ? result.sale + '%' : '');*/
        }
    });
}

function setColor(el) {
	var tbb = $('.tbb.active').data('vi');
	$('.so input[type="radio"]').each(function(){
		$(this).prop('checked', false);
	});
	$('#input-27830').prop('checked', false);
	$('#select_inver' + tbb).val(''); 
    $('#select_color' + tbb).val($(el).data('id'));
    selTabl(1);
}

function setInver(el) {
	var tbb = $('.tbb.active').data('vi');
	$('.soc input[type="radio"]').each(function(){
		$(this).prop('checked', false);
	});
	
	$('.so input').prop('checked', false);
	$(el).parent().prop('checked', true);
	$('#input-27830').prop('checked', true);
	$('#select_inver' + tbb).val($(el).data('id'));
	selTabl(1);
}

function setfon(el) {
	var tbb = $('.tbb.active').data('vi');
	$('#select_fon' + tbb).val($(el).data('id'));
	selTabl(3);
}

function setTab(el) {
	$('.calc__info').removeClass('noi');
    $('#select_tab').val(el.id);
	$('.heds').removeClass('active');
	$('#real-' + el.id).addClass('active');
	$(el).find('input').prop('checked', true);
	$('#canvas').fadeIn(60);
    selTabl(1);
    var model_id = $(el).find('input').val();
    getPriceTable(model_id);
}
function scrollToCalculator() {
	$('html, body').animate({ scrollTop: $('#product').offset().top-150}, 1200);
}
function setQty(el, des){
    var qty = $(el);
    if(!qty.length){
        return;
    }
    if(des == '-'){
        qty.val(qty.val()*1-1);
    }
    if(des == '+'){
        qty.val(qty.val()*1+1);
    }
    qty.trigger('change');
}
function checkQty(el){
	var qty = $(el);
	if(!qty.length){
	}
	if(qty.val() <= 1){
		qty.val(1);
	}
    changePrice();
}

$(document).off('change', '#tabl_options [name^="option["]').on('change', '#tabl_options [name^="option["]', changePrice);

function changePrice(){
    var model_id = $('.tab-pane.tbc.active').find('input[type=radio].hidden:checked');
    if(model_id.length && parseInt(model_id.val())){
        var form = $('#tabl_options [name^="option["]').serialize();
        $.post('index.php?route=product/live_options/index&product_id=' + model_id.val(), 'product_id=' + model_id.val() + '&quantity=' + $('#qty').val() + '&' + form, function(result){
            var option_price = result.option_price || 0;
            getPriceTable(model_id.val(), option_price);
            
            var old_price = result.old_price, current_price = result.price, price = '', sale = 0;
            result = result.new_price;
            if(result.special){
                price = '<span class="price-new" style="display: inline-block;">' + result.special + '</span>';
                sale = Math.ceil( 100 - parseFloat( result.special.replace(/[\s]/g, "")) *100 / parseFloat( result.price.replace(/[\s]/g, "")) );
            }else{
                if(parseFloat(old_price) > parseFloat(current_price)){
                    sale = Math.ceil( 100 - parseFloat(current_price)*100 / parseFloat(old_price));
                }
                price = '<span class="price" style="display: inline-block;">' + result.price + '</span>';
            }
            animatePrice('.calc__add_price', price);
            animatePrice('.calc__discount_sale', sale ? sale + '%' : 'РќР•Рў');
        });
    }else{
        animatePrice('.calc__add_price', 'NaN');
        animatePrice('.calc__discount_sale', 'РќР•Рў');
    }
    $('.checkbox_label').children('span').html('');
    var tbb = $('.tbb.active').data('vi');
    var text_options = $('[data-vid="' + tbb + '"] .extra-option:checked').map(function() {
        return $(this).siblings('.checkbox_label').text();
    }).get();
    $('#product_option [type="radio"][name^="option["]:checked, #product_option [type="checkbox"][name^="option["]:checked, #product_option select[name^="option["]').each(function(i, item){
        var el = $(item);
        if(el[0].tagName.toLowerCase() == 'select'){
            text_options.push(el.siblings('.calc__add_label').text() + ': ' + el.find('option[value="'+el.val()+'"]').text());
            return;
        }
	text_options.push(el.siblings('.checkbox_label').text());
    });
    if($('#qty').val()){
        qty = $('#qty').val() + ' С€С‚.';
        $('input[name="quantity"]').val($('#qty').val());
    } else {
        qty = $('input[name="pre_quantity"]:checked').val() + ' С€С‚.';
        $('input[name="quantity"]').val($('input[name="pre_quantity"]:checked').val());
    }
        
    text_options.push(qty);
    $('#opti').text(text_options.join(', '));

}

function animatePrice(class_selector, context){
    $(class_selector).fadeOut(150, function() {
        $(this).html(context).fadeIn(50);
    });
}

$('[name="tip"], [name="adr"], [name="nom"]').on('change input', function(){
	var tbb = parseInt($('.tbb.active').data('vi'));
	var acr = $('.calc__block_el.active');
        $('.tbc.active .calc__block_el').removeClass('active');
        acr.addClass('active');
		if (tbb > 0) {
		    $('#tabl_canvas').css('background', 'transparent');
			if ($(window).width() > 767)
			$('#tabl_canvas .tabl_img').attr('style', 'width: auto !important');
	    } else {
			var fon = '[data-id="' + $('#select_fon' + tbb).val() + '"]';
			if ($(fon).parent().data('fon') != undefined) {
		        $('#tabl_canvas').css('background', 'url(/image/' + $(fon).parent().data('fon') + ') 100% 100%');
			} else {
				$('#tabl_canvas').css('background', '#fff');
			}
			//$('#tabl_canvas .tabl_img').attr('style', 'width: 100% !important');
		}
	selTabl(acr);
});

function selTabl(e) {
    
    var model_id = $('.tab-pane.tbc.active').find('input[type=radio].hidden:checked');
    if(!model_id.length){
        getPriceTable($('.tab-pane.tbc.active').find('input[type=radio].hidden').first().prop('checked', true).val());
    }else{
        animatePrice('.calc__add_price', '');
        $('.calc__discount_sale').html('РќР•Рў');
    }
	if (!$('.calc__address > div:first input').length) return false;
	var tbb = $('.tbb.active').attr('data-vi');
	e = parseInt(e);
	var fon = '[data-id="' + $('#select_fon' + tbb).val() + '"]';	
	if ($('#select_inver' + tbb).val()) {
	    var inver = $('.' + $('#select_inver' + tbb).val().replace('i','')).css('background-color');
	} else {
	    var inver = '#fff';
	}
	
	var b_canvas = document.getElementById("canvas");
    var ctx = b_canvas.getContext("2d");
    var myFactor = 0.45;
	ctx.fillRect(0,0,400,250); 
    ctx.clearRect(0, 0, 400, 250); 
	
	if($('#select_tab').val() != 'at017' && $('#select_tab').val() != 'at018' && $('#select_tab').val() != 'at019') {
    	var title = $('.calc__address > div:first input').val().toUpperCase();
		var street = $('.calc__address > div:nth-child(2) input').val().toUpperCase();
	} else {
		var title = $('.calc__address > div:first input').val(); 
		var street = $('.calc__address > div:nth-child(2) input').val();
	}
    var house = $('.calc__address > div:nth-child(3) input').val();
	var arr = '';
	
	if ($('#select_color' + tbb).val() == 'blue') {
		arr = 's';
	} else if ($('#select_color' + tbb).val() == 'brown') {
		arr = 'ko';
	} else if ($('#select_color' + tbb).val() == 'green') {
		arr = 'z';
	} else if ($('#select_color' + tbb).val() == 'red') {
		arr = 'kr';
	} else if ($('#select_color' + tbb).val() == 'black') {
		arr = 'ch';
	} else if ($('#select_color' + tbb).val() == 'red-brown') {
		arr = 'vish';
	}
	if ($('#select_inver' + tbb).val() == 'iblue') {
		arr = 's-inv';
	} else if ($('#select_inver' + tbb).val() == 'ibrown') {
		arr = 'ko-inv';
	} else if ($('#select_inver' + tbb).val() == 'igreen') {
		arr = 'z-inv';
	} else if ($('#select_inver' + tbb).val() == 'ired') {
		arr = 'kr-inv';
	} else if ($('#select_inver' + tbb).val() == 'iblack') {
		arr = 'ch-inv';
	} else if ($('#select_inver' + tbb).val() == 'ired-brown') {
		arr = 'vish-inv';
	}

	if ($('.tbb.active a').text() == 'РџР»РѕСЃРєРёРµ') {
    	var img = '/image/catalog/adr/' + $('#select_tab').val() + arr + '.png';
	    img = img.replace('adr/at','adr/');
	} else {
		var img = '/image/catalog/adr/' + $('#select_tab').val() + arr + '.png';
	    img = img.replace('adr/at','adr/');
	}

	if (e == 3 && tbb == 0) {
		$('#tabl_canvas').css('background', 'url(/image/' + $(fon).parent().data('fon') + ') 100% 100%');
	}	
	
	if ($('.tbb.active a').text() == 'РџР»РѕСЃРєРёРµ' && e == 999) {
		$('#tabl_canvas img').data('loaded', 'loading').attr('src', img);
	}
	
	if (e != 0 && e != 999) {
	$('#tabl_canvas > div').css({opacity: 0});
	$('#tabl_canvas img').data('loaded', 'loading').attr('src', img);
	
	$('#tabl_canvas > div').animate({opacity: 1}, 1000);
	}

    if(!title) {
		if($('#select_tab').val() == 'at006' || $('#select_tab').val() == 'at019') {	
            title = 'СѓР».';
		} else if($('#select_tab').val() == 'at017') {
			title = 'СѓР»РёС†Р°';
		} else {
			title = 'улица';
		}
    }
    if(!street) {
        street = 'ФЛОТСКАЯ';
    }
    if(!house) {
        house = '15';
    }
	
	if ($('#select_tab').val() == 'at019') {
    	street = title + ' ' + street;
	} else if ($('#select_tab').val() == 'at020') {
	    street = street.toUpperCase();
	}
	
	tip = $('[name="tip"]').val();
	adr = $('[name="adr"]').val();
	nom = $('[name="nom"]').val();

	if($('#select_tab').val() == 'at000') {
		ctx.font = "bold 16px Antiqua";	
        ctx.textBaseline = "middle";
        var angle = 0.20 + title.length / 100;
        var tx = 200;
        var ty = 680;
        var radius = myFactor*1400;
        ctx.save();
        ctx.translate(tx, ty);
        ctx.rotate(-1 * angle / 2);	
        ctx.rotate(-1 * (angle / title.length) / 2);
        for (var n = 0; n < title.length; n++) {
            var char = title[n];
            ctx.rotate(angle / title.length);
            ctx.save();
            ctx.translate(0, -1 * radius);  
            var no_rotate = (-angle / 2 + (n+0.5)*(angle/title.length)).toFixed(2);
            ctx.transform(1, 0, no_rotate, 1, 0, 0);
            if (title.length >= 10) {ctx.scale(width_char, 1);}
            if("РЁР©Р–Р®".indexOf(char.toUpperCase()) >=0) {ctx.scale(0.8, 1);}
            if("РњР«".indexOf(char.toUpperCase()) >=0)   {ctx.scale(0.9, 1);}
            if("Р“Р•Р Р—Р¬".indexOf(char.toUpperCase()) >=0)   {ctx.scale(1.1, 1);}
		    ctx.fillStyle = inver;
            ctx.fillText(char, 0, 0);
            ctx.restore();
        }
        ctx.restore();
        ctx.save();
        ctx.textAlign = "center"; 
		ctx.fillStyle = "#fff";	
				
        ctx.font = "bold 45px Antiqua";	
        ctx.textBaseline = "middle";
        street = street.toUpperCase();
        var angle = 0.38;
        var tx = myFactor*450;
        var ty = myFactor*2010;
        var radius = myFactor*1800;	
        ctx.save();
        ctx.translate(tx, ty);
        ctx.rotate(-1 * angle / 2);	
        ctx.rotate(-1 * (angle / street.length) / 2);
        for (var n = 0; n < street.length; n++) {
            var char = street[n];
            ctx.rotate(angle / street.length);
            ctx.save();
            ctx.translate(0, -1 * radius);  
            var no_rotate = (-angle / 2 + (n+0.5)*(angle/street.length)).toFixed(2);
            var width_char = (10 / street.length + 0.1).toFixed(2);
            if (street.length >= 10) {ctx.scale(width_char, 1);}
            if("РЁР©Р–Р®".indexOf(char.toUpperCase()) >=0) {ctx.scale(0.8, 1);}
            if("РњР«".indexOf(char.toUpperCase()) >=0)   {ctx.scale(0.9, 1);}
            if("Р“Р•Р Р—Р¬".indexOf(char.toUpperCase()) >=0)   {ctx.scale(1.1, 1);}
			ctx.fillStyle = inver; 
            ctx.fillText(char, 0, 0, 30);
            ctx.restore();
        }
        ctx.restore(); 
        ctx.save();
        ctx.textAlign = "center"; 
		if ($('#select_inver' + tbb).val()) {
	        ctx.fillStyle = '#fff';
	    } else {
			ctx.fillStyle = $('[data-id="' + $('#select_color' + tbb).val() + '"]').css('background-color');
	    }

        ctx.font = "bold 50px Antiqua";	
        ctx.textBaseline = "middle";
        ctx.fillText(house, 200, 165, 100); 
        ctx.restore();
		var price = '100';
		document.getElementById('price').value = '100';

	} else if($('#select_tab').val() == 'at001') {
		ctx.font = "bold 16px Antiqua";	
        ctx.textBaseline = "middle";
        var angle = 0.20 + title.length / 100;
        var tx = 200;
        var ty = 685;
        var radius = myFactor*1400;
        ctx.save();
        ctx.translate(tx, ty);
        ctx.rotate(-1 * angle / 2);	
        ctx.rotate(-1 * (angle / title.length) / 2);
        for (var n = 0; n < title.length; n++) {
            var char = title[n];
            ctx.rotate(angle / title.length);
            ctx.save();
            ctx.translate(0, -1 * radius);  
            var no_rotate = (-angle / 2 + (n+0.5)*(angle/title.length)).toFixed(2);
            ctx.transform(1, 0, no_rotate, 1, 0, 0);
            if (title.length >= 10) {ctx.scale(width_char, 1);}
            if("РЁР©Р–Р®".indexOf(char.toUpperCase()) >=0) {ctx.scale(0.8, 1);}
            if("РњР«".indexOf(char.toUpperCase()) >=0)   {ctx.scale(0.9, 1);}
            if("Р“Р•Р Р—Р¬".indexOf(char.toUpperCase()) >=0)   {ctx.scale(1.1, 1);}
		    ctx.fillStyle = inver;
            ctx.fillText(char, 0, 0);
            ctx.restore();
        }
        ctx.restore();
        ctx.save();
        ctx.textAlign = "center"; 
		ctx.fillStyle = "#fff";	
				
        ctx.font = "bold 45px Antiqua";	
        ctx.textBaseline = "middle";
        street = street.toUpperCase();
        var angle = 0.38;
        var tx = myFactor*450;
        var ty = myFactor*2030;
        var radius = myFactor*1800;	
        ctx.save();
        ctx.translate(tx, ty);
        ctx.rotate(-1 * angle / 2);	
        ctx.rotate(-1 * (angle / street.length) / 2);
        for (var n = 0; n < street.length; n++) {
            var char = street[n];
            ctx.rotate(angle / street.length);
            ctx.save();
            ctx.translate(0, -1 * radius);  
            var no_rotate = (-angle / 2 + (n+0.5)*(angle/street.length)).toFixed(2);
            var width_char = (10 / street.length + 0.1).toFixed(2);
            if (street.length >= 10) {ctx.scale(width_char, 1);}
            if("РЁР©Р–Р®".indexOf(char.toUpperCase()) >=0) {ctx.scale(0.8, 1);}
            if("РњР«".indexOf(char.toUpperCase()) >=0)   {ctx.scale(0.9, 1);}
            if("Р“Р•Р Р—Р¬".indexOf(char.toUpperCase()) >=0)   {ctx.scale(1.1, 1);}
			ctx.fillStyle = inver; 
            ctx.fillText(char, 0, 0, 30);
            ctx.restore();
        }
        ctx.restore(); 
        ctx.save();
        ctx.textAlign = "center"; 
		if ($('#select_inver' + tbb).val()) {
	        ctx.fillStyle = '#fff';
	    } else {
			ctx.fillStyle = $('[data-id="' + $('#select_color' + tbb).val() + '"]').css('background-color');
	    }

        ctx.font = "bold 50px Antiqua";	
        ctx.textBaseline = "middle";
        ctx.fillText(house, 200, 170, 100); 
        ctx.restore();
		var price = '50';
		document.getElementById('price').value = '50';
		
		
	} else if($('#select_tab').val() == 'at002') {
		ctx.font = "bold 16px Antiqua";	
        ctx.textBaseline = "middle";
        var angle = 0.15 + title.length / 100;
        var tx = 200;
        var ty = 680;
        var radius = myFactor*1380;
        ctx.save();
        ctx.translate(tx, ty);
        ctx.rotate(-1 * angle / 2);	
        ctx.rotate(-1 * (angle / title.length) / 2);
        for (var n = 0; n < title.length; n++) {
            var char = title[n];
            ctx.rotate(angle / title.length);
            ctx.save();
            ctx.translate(0, -1 * radius);  
            var no_rotate = (-angle / 2 + (n+0.5)*(angle/title.length)).toFixed(2);
            ctx.transform(1, 0, no_rotate, 1, 0, 0);
            if (title.length >= 10) {ctx.scale(width_char, 1);}
            if("РЁР©Р–Р®".indexOf(char.toUpperCase()) >=0) {ctx.scale(0.8, 1);}
            if("РњР«".indexOf(char.toUpperCase()) >=0)   {ctx.scale(0.9, 1);}
            if("Р“Р•Р Р—Р¬".indexOf(char.toUpperCase()) >=0)   {ctx.scale(1.1, 1);}
		    ctx.fillStyle = inver;
            ctx.fillText(char, 0, 0);
            ctx.restore();
        }
        ctx.restore();
        ctx.save();
        ctx.textAlign = "center"; 
		ctx.fillStyle = "#fff";	
				
        ctx.font = "bold 45px Antiqua";	
        ctx.textBaseline = "middle";
        street = street.toUpperCase();
        var angle = 0.38;
        var tx = myFactor*450;
        var ty = myFactor*2030;
        var radius = myFactor*1800;	
        ctx.save();
        ctx.translate(tx, ty);
        ctx.rotate(-1 * angle / 2);	
        ctx.rotate(-1 * (angle / street.length) / 2);
        for (var n = 0; n < street.length; n++) {
            var char = street[n];
            ctx.rotate(angle / street.length);
            ctx.save();
            ctx.translate(0, -1 * radius);  
            var no_rotate = (-angle / 2 + (n+0.5)*(angle/street.length)).toFixed(2);
            var width_char = (10 / street.length + 0.1).toFixed(2);
            if (street.length >= 10) {ctx.scale(width_char, 1);}
            if("РЁР©Р–Р®".indexOf(char.toUpperCase()) >=0) {ctx.scale(0.8, 1);}
            if("РњР«".indexOf(char.toUpperCase()) >=0)   {ctx.scale(0.9, 1);}
            if("Р“Р•Р Р—Р¬".indexOf(char.toUpperCase()) >=0)   {ctx.scale(1.1, 1);}
			ctx.fillStyle = inver; 
            ctx.fillText(char, 0, 0, 30);
            ctx.restore();
        }
        ctx.restore(); 
        ctx.save();
        ctx.textAlign = "center"; 
		ctx.font = "bold 50px Antiqua";	
        ctx.textBaseline = "middle";
        ctx.fillText(house, 200, 170, 100); 
        ctx.restore();
		var price = '200';
		document.getElementById('price').value = '200';
		
		
	} else if($('#select_tab').val() == 'at003') {
		ctx.textAlign = "center"; 
		ctx.fillStyle = inver;
        ctx.font = "bold 16px Arial Black";	
        ctx.textBaseline = "middle";
        ctx.fillText(title.split("").join(" "), 200, 60, 90); 
        ctx.restore();
        ctx.save();

        ctx.textAlign = "center";
		ctx.fillStyle = inver;
        ctx.font = "bold 55px Antiqua";	
        ctx.textBaseline = "middle";
        street_qty = street.length
        if(street_qty <= 4){
            street = street.split("").join("  ")
        } else if(street_qty <= 6) {
            street = street.split("").join(" ")
        }
        ctx.fillText(street.toUpperCase(), 200, 105, 280); 
        ctx.restore();
        ctx.save();
        
        ctx.textAlign = "center"; 
		ctx.fillStyle = inver;
        ctx.font = "bold 65px Antiqua";	
        ctx.textBaseline = "middle";
        ctx.fillText(house, 200, 160, 80); 
        ctx.restore();
        ctx.save();
		var price = '300';
		document.getElementById('price').value = '300';
		
		
	} else if($('#select_tab').val() == 'at015') {
		ctx.textAlign = "center"; 
		ctx.fillStyle = inver;
        ctx.font = "bold 16px Antiqua";	
        ctx.textBaseline = "middle";
        ctx.fillText(title.split("").join(" "), 200, 160, 90); 
        ctx.restore();
        ctx.save();
				
        ctx.textAlign = "center";
		ctx.fillStyle = inver;
        ctx.font = "bold 40px Antiqua";	
        ctx.textBaseline = "middle";	
        ctx.fillText(street.toUpperCase().split("").join(" "), 200, 135, 240); 
        ctx.restore();
        ctx.save();
        
        ctx.textAlign = "center"; 
		ctx.fillStyle = inver;
        ctx.font = "bold 50px Antiqua";	
        ctx.textBaseline = "middle";
        ctx.fillText(house, 200, 85, 110); 
        ctx.restore();
        ctx.save();
		
	} else if($('#select_tab').val() == 'at016') {
		ctx.textAlign = "center"; 
		ctx.fillStyle = inver;
		ctx.font = "bold 18px Antiqua";	
        ctx.textBaseline = "middle";
        ctx.fillText(title.split("").join(" "), 200, 80, 140); 
        ctx.restore();
        ctx.save();
	
        ctx.textAlign = "center";
		ctx.fillStyle = inver;
		ctx.font = "bold 50px Antiqua";	
        ctx.textBaseline = "middle";
        ctx.fillText((street.toUpperCase().split("").join(" ") + ', ' + house), 202, 135, 270); 
        ctx.restore();
        ctx.save();
		
	} else if($('#select_tab').val() == 'at005') {
		ctx.textAlign = "center"; 
		ctx.fillStyle = inver;
        ctx.font = "bold 16px Antiqua";	
        ctx.textBaseline = "middle";
        ctx.fillText(title.split("").join(" "), 200, 0, 90); 
        ctx.restore();
        ctx.save();
				
        ctx.textAlign = "center";
		ctx.fillStyle = inver;
        ctx.font = "bold 26px Antiqua";	
        ctx.textBaseline = "middle";	
        ctx.fillText(street.toUpperCase(), 205, 185, 160); 
        ctx.restore();
        ctx.save();
        
        ctx.textAlign = "center"; 
		ctx.fillStyle = inver;
        ctx.font = "bold 110px Antiqua";	
        ctx.textBaseline = "middle";
        ctx.fillText(house, 200, 110, 138); 
        ctx.restore();
        ctx.save();
	} else if($('#select_tab').val() == 'at004') {
		ctx.textAlign = "center"; 
		ctx.fillStyle = inver;
        ctx.font = "bold 16px Antiqua";	
        ctx.textBaseline = "middle";
        ctx.fillText(title.split("").join(" "), 240, 100, 70); 
        ctx.restore();
        ctx.save();
		
		
        ctx.textAlign = "center";
		ctx.fillStyle = inver;
        ctx.font = "bold 50px Antiqua";	
        ctx.textBaseline = "middle";	
        ctx.fillText(street.toUpperCase().split("").join(" "), 160, 145, 240); 
        ctx.restore();
        ctx.save();
        
        if ($('#select_inver' + tbb).val()) {
	        ctx.fillStyle = '#fff';
	    } else {
			ctx.fillStyle = $('[data-id="' + $('#select_color' + tbb).val() + '"]').css('background-color');
	    }

        ctx.font = "bold 60px Antiqua";	
        ctx.textBaseline = "middle";
        ctx.fillText(house, 325, 130, 60); 
        ctx.restore();
        ctx.save();
	}	
	document.getElementById('calc__add_price').innerHTML = price *2;
	
    var text_options = $('[data-vid="' + tbb + '"] .extra-option:checked').map(function() {
        return $(this).siblings('.checkbox_label').text();
    }).get();
    $('#product_option [type="radio"][name^="option["]:checked, #product_option [type="checkbox"][name^="option["]:checked, #product_option select[name^="option["]').each(function(i, item){
	var el = $(item);
	if(el[0].tagName.toLowerCase() == 'select'){
            text_options.push(el.siblings('.calc__add_label').text() + ': ' + el.find('option[value="'+el.val()+'"]').text());
            return;
        }
	text_options.push(el.siblings('.checkbox_label').text());
    });
        
	if($('#qty').val()){
        qty = $('#qty').val() + ' С€С‚.';
        $('input[name="quantity"]').val($('#qty').val());
    } else {
        qty = $('input[name="pre_quantity"]:checked').val() + ' С€С‚.';
        $('input[name="quantity"]').val($('input[name="pre_quantity"]:checked').val());
    }
        
	text_options.push(qty);
    $('#opti').text(text_options.join(', '));
}

$(window).on("load", function() {
if ($('.tbb.active a').text() == 'РџР»РѕСЃРєРёРµ') {
    $('#tabl_canvas').css('background',"url('/image/catalog/texture2.jpg') 100% 100%");
}
$('#slideshow34').owlCarousel({
	autoPlay:'3000',
	singleItem: true,
	navigation: true,
	stopOnHover:true,
	mouseDrag:false,
	navigationText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
	pagination: true
});
});