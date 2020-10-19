function getURLVar(key) {
	var value = [];

	var query = document.location.search.split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}

$(window).scroll(function(){		
		if($(this).scrollTop()>190) {
			$('.scroll_up').addClass('show');
		}else{
			$('.scroll_up').removeClass('show');
		}
	});

function quantity(p_id, minimum, flag) {
    var input = $('#input-quantity');
	var minimum = parseFloat(minimum);
	if(flag == '+') {
		input.val(parseFloat(input.val())+1).trigger('change');
	}
	if(flag == '-') {
		if(input.val() > minimum) {
			input.val(parseFloat(input.val())-1).trigger('change');
		}
	}
}

function scroll_to(hash) {		
	var destination = $(hash).offset().top-100;
	$('html, body').animate({scrollTop: destination}, 400);
}

$(document).ready(function() {
	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();

		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});
	
	$('#menu_top a').on('click', function(e) {
		e.preventDefault();
		var element = $(this).attr('href');

		if ($(element).length) {
			$('html, body').animate({ scrollTop: $(element).offset().top }, 1300);
		}
	});

	// Product List
	$('#list-view').click(function() {
		$('#content .product-grid > .clearfix').remove();

		$('#content .row > .product-grid').attr('class', 'product-layout product-list col-xs-12');
		$('#content .row > .product-price').attr('class', 'product-layout product-list col-xs-12');
		$('#grid-view').removeClass('active');
		$('#compact-view').removeClass('active');
		$('#list-view').addClass('active');
		autoheight();

		localStorage.setItem('display', 'list');
		return false;
	});

	// Product Grid
	$('#grid-view').click(function() {
		// What a shame bootstrap does not take into account dynamically loaded columns
		var cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
		} else if (cols == 1) {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
		} else {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
		}
		autoheight();

		$('#list-view').removeClass('active');
		$('#compact-view').removeClass('active');
		$('#grid-view').addClass('active');

		localStorage.setItem('display', 'grid');
		return false;
	});
	
	$('#compact-view').on('click', function() {
		compact_view();
	}); 

	if (localStorage.getItem('display') == 'list') {
		$('#list-view').trigger('click');
		$('#list-view').addClass('active');
	} else {
		$('#grid-view').trigger('click');
		$('#grid-view').addClass('active');
	}

	// Checkout
	$(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function(e) {
		if (e.keyCode == 13) {
			$('#collapse-checkout-option #button-login').trigger('click');
		}
	});

	// tooltips on hover
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body',trigger: 'hover'});

	// Makes tooltips work on ajax generated content
	$(document).ajaxStop(function() {
		$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
	});
});

$(document).ready(function(){
	$('#button-quick').on('click', function(){
		if ($('input[name=\'quantity\']').length) {
		    adds(this, $('input[name=\'quantity\']').val());
		} else {
			adds(this, 1);
		}
	});
});

function list_view() {
	$('#content .product-grid > .clearfix').remove();
	$('#content .product-grid, #content .product-price').attr('class', 'product-layout product-list col-xs-12');
	localStorage.setItem('display', 'list');
	autoheight();
}

function grid_view() {
	cols = $('#column-right, #column-left').length;
	menu = $('.breadcrumb.col-md-offset-4.col-lg-offset-3').length;

	if (cols == 2) {
		$('.product-grid, .product-list, .product-price').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
	} else if (cols == 1 || menu == 1) {
		$('.product-grid, .product-list, .product-price').attr('class', 'product-layout product-grid col-lg-4 col-md-6 col-sm-6 col-xs-12');
	} else {
		$('.product-grid, .product-list, .product-price').attr('class', 'product-layout product-grid col-lg-3 col-md-4 col-sm-4 col-xs-12');
	}
	autoheight();
		
	localStorage.setItem('display', 'grid');
}
	
function compact_view() {
	$('#content .row > .product-list, #content .row > .product-grid').attr('class', 'product-layout product-price col-xs-12');
	if(!$('.product-price .product-thumb div .caption').length) {
		$('.product-price .caption').wrap('<div></div>');
	}
	autoheight();
	localStorage.setItem('display', 'compact');
}

function select_view() {
	if (localStorage.getItem('display') == 'list') {
		list_view();
	} else if (localStorage.getItem('display') == 'compact')  {
		compact_view();
	} else {
		grid_view();
	}
}
select_view();
function module_type_view(type, id) {
	var items = [[0, 1], [580, 2], [720, 3], [1050, 4]];
	
	var columns = ($('#column-left '+id).length || $('#column-right '+id).length);
	
	block_resize = function() {
		if($(id).parent().parent().parent().hasClass('tab-content')) {
			var width = $(id).parent().parent().parent().width()+20;
		} else {
			var width = $(id).width();
		}
		
		width = Math.floor(parseFloat(width))

		if (type == 'grid' && !columns) {	
			for (i = 0; i < items.length; i += 1) {
				if (items[i][0] <= width) {
					itemsNEW = parseFloat(items[i][1]);
				}
			}
			$(id).find('.product-layout-1').attr('style', 'float:left;width:'+(Math.floor(width / itemsNEW)-0.2)+'px;padding:0 10px');
			autoheight();
		} else {
			if (!$('#product').length)
			$(id).owlCarousel({
				responsiveBaseWidth: id,
				itemsCustom: items,
				autoPlay: false,
				mouseDrag:false,
				navigation: true,
				navigationText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>'],
				pagination: false,
				afterUpdate: function () {
					autoheight();
				}
			});
		}
    }
	
	block_resize();
    $(window).resize(block_resize);
}

function autoheight() {
	$product = $('.product-thumb');
	$news = $('.news');
	$article = $('.article_module');

	max_height_div('.home_banner');
	max_height_div($('.category_list').find('p'));
	
	max_height_div($product.find('.caption > a'));
	max_height_div($product.find('.attribute, .description'));
	max_height_div($product.find('.option'));
	max_height_div($product.find('.reviews-description'));
	
	max_height_div($news.find('.name'));
	max_height_div($news.find('.description'));
	
	max_height_div($article.find('.name'));
	max_height_div($article.find('.description'));
}

function max_height_div(div) {
	block_height = function() {
		$(div).height('auto');
		var maxheight = 0;
		
		if($(window).width() > 500 && $(div).length) {
			$(div).each(function(){
				if($(this).height() > maxheight) {
					maxheight = $(this).height();
				}
			});
			$(div).height(maxheight);
		}
	}
	
	block_height();
	$(window).resize(block_height);
}

function adds(e, quantity) {
	$.ajax({
		url: 'index.php?route=checkout/cart/quick_price',
		type: 'post',
		data: 'quantity=' + quantity + '&' + $('.option.row input[type=\'text\'], .option.row input[type=\'hidden\'], .list-unstyled.price input[type=\'hidden\'], .option.row input[type=\'radio\']:checked, .option.row input[type=\'checkbox\']:checked, .option.row select, .option.row textarea').serialize(),
		dataType: 'json',
		beforeSend: function() {
			$(e).button('loading');
		},
		complete: function() {
			$(e).button('reset');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');

			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						var element = $('#input-option' + i.replace('_', '-'));

						if (element.parent().hasClass('input-group')) {
							element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						} else {
							element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						}
					}
				}

				if (json['error']['recurring']) {
					$('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
				}

				// Highlight any found errors
				$('.text-danger').parent().addClass('has-error');
			}

			if (json['success']) {
				var reco = '';
				if (json['reco']) {
					for (var r in json['reco']) {
						reco += '&pids[]=' + json['reco'][r];
					}
				}
				$.get('index.php?route=extension/module/quick&ct=1&product_id=' + $('input[name=\'product_id\']').val() + '&quantity=' + quantity + '&total=' + json['total'] + (reco ? reco : ''), function(html) {
				    $('#rem').remove();
				    $('body').append(html);
					$('#left-column-option').html(json['option']);
					$('.noc').length ? $('.noc').text($('[data-option-name="Р’С‹Р±РµСЂРёС‚Рµ С†РІРµС‚"]').find('input:checked').next().data('original-title')) : '';
					$('#quick').modal('show');
			    });
			}
		},
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
	});
}

// Cart add remove functions
var cart = {
	'add': function(product_id, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				$('.alert, .text-danger').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					html  = '<div id="modal-agree" class="modal agr">';
    			html += '  <div class="modal-dialog agr">';
	    		html += '    <div>';
	    		html += '      <div class="modal-body">' + json['success_new'] + '</div>';
    			html += '    </div';
	    		html += '  </div>';
		    	html += '</div>';

			    $('body').append(html);

			    $('#modal-agree').modal('show');
					// Need to set timeout otherwise it wont update the total
					setTimeout(function () {
						$('#cart-total').html(json['total']);
					}, 100);

					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'update': function(key, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);
				
				var now_location = String(document.location.pathname);

				if ((now_location == '/cart/') || (now_location == '/checkout/') || (getURLVar('route') == 'checkout/cart') || (getURLVar('route') == 'checkout/checkout')) {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var voucher = {
	'add': function() {

	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var wishlist = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=account/wishlist/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

				$('#wishlist-total span').html(json['total']);
				$('#wishlist-total').attr('title', json['total']);

				$('html, body').animate({ scrollTop: 0 }, 'slow');
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

var compare = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=product/compare/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert').remove();

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					$('#compare-total').html(json['total']);

					$('html, body').animate({ scrollTop: 0 }, 'slow');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function(e) {
	e.preventDefault();

	$('#modal-agree').remove();

	var element = this;

	$.ajax({
		url: $(element).attr('href'),
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-agree" class="modal">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
			html += '      </div>';
			html += '      <div class="modal-body">' + data + '</div>';
			html += '    </div';
			html += '  </div>';
			html += '</div>';

			$('body').append(html);

			$('#modal-agree').modal('show');
		}
	});
});

// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();

			$.extend(this, option);

			$(this).attr('autocomplete', 'off');

			// Focus
			$(this).on('focus', function() {
				this.request();
			});

			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});

			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});

			// Click
			this.click = function(event) {
				event.preventDefault();

				value = $(event.target).parent().attr('data-value');

				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}

			// Show
			this.show = function() {
				var pos = $(this).position();

				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});

				$(this).siblings('ul.dropdown-menu').show();
			}

			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}

			// Request
			this.request = function() {
				clearTimeout(this.timer);

				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}

			// Response
			this.response = function(json) {
				html = '';

				if (json.length) {
					for (i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}

					for (i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}

					// Get all the ones with a categories
					var category = new Array();

					for (i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}

							category[json[i]['category']]['item'].push(json[i]);
						}
					}

					for (i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}

				if (html) {
					this.show();
				} else {
					this.hide();
				}

				$(this).siblings('ul.dropdown-menu').html(html);
			}

			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

		});
	}
})(window.jQuery);