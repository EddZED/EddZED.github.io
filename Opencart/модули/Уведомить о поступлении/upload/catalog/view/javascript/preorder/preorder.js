function submitPreOrder() {
	$.ajax({
		url: 'index.php?route=extension/module/preorder',
		type: 'post',
		dataType: 'json',
		data: $("#form-preorder").serialize(),
		success: function(json) {
			$('.alert-success, .alert-danger').remove();
				
			if (json['error']) {
				$('#form-preorder').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}

			if (json['success']) {
				$('#form-preorder').before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
				$('#form-preorder').get(0).reset();

				setTimeout(function () {
					$('#preorder-box').modal('hide');
				}, 2000);
			}
		}
	});
}

function addPreOrder(module_id, product_id) {
	getPreorderForm();
	
	setTimeout(function () {
		if (module_id) {
			var product = '.product' + module_id + product_id;
		} else {
			var product = '#product';
		}

		$.ajax({
			url: 'index.php?route=extension/module/preorder/add&product_id=' + product_id,
			type: 'post',
			dataType: 'json',
			data: $(product + ' input[type=\'radio\']:checked, ' + product + ' input[type=\'checkbox\']:checked, ' + product + ' select, ' + product + ' input[type=\'hidden\']'),
			success: function(json) {
				$('.alert-success, .alert-danger').remove();

				if (json['name']) {
					
					if (json['option']) {
						$.each(json['option'], function(index, value) {
							$('#preorder_option').append('<input type="hidden" name="preorder_option[' + index + ']" value="' + value + '">');
						}); 
					}
					
					$('input[name=\'preorder_product_id\']').val(product_id);
					$('#preorder_name').html(json['name']);
					$("#preorder-box").modal('show');
				}
			}
		});
	}, 100);
}

function changeOptionPreOrder(product_id) {
	$.ajax({
		url: 'index.php?route=extension/module/preorder/option&product_id=' + product_id,
		type: 'post',
		data: $('#product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select'),
		dataType: 'json',
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			if (json['success']) {
				$('#button-preorder').hide(); 
				$('#button-cart').show();
			} else {
				$('#button-preorder').show(); 
				$('#button-cart').hide();
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function getPreorderForm() {
	$('#preorder-box').remove();
	
	$.ajax({
		url: 'index.php?route=extension/module/preorder/form',
		type: 'post',
		dataType: 'json',
		success: function(json) {
			html   = '<div id="preorder-box" class="modal fade">';
			html  += '<div class="modal-dialog">';
			html  += '<div class="modal-content">';
			html  += '<div class="modal-header">';
			html  += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
			html  += '<h3 class="modal-title">' + json['text_title'] + '</h3>';
			html  += '<h4 id="preorder_name"></h4>';
			html  += '</div>';
			html  += '<div class="modal-body">';
			html  += '<form class="form-horizontal" id="form-preorder">';
			
			if (json['preorder_email'] > 0) {
				html  += '<div class="form-group' + ((json['preorder_email'] == 2) ? ' required' : '') + '">';
				html  += '<div class="col-sm-4">';
				html  += '<label class="control-label" for="input-preorder-email">' + json['entry_email'] + '</label>';	  
				html  += '</div>';
				html  += '<div class="col-sm-8">';
				html  += '<input type="email" name="preorder_email" value="' + json['email'] + '" id="input-preorder-email" class="form-control" placeholder="' + json['entry_email'] + '" />';
				html  += '</div>';
				html  += '</div>';
			}
			
			if (json['preorder_phone'] > 0) {
				html  += '<div class="form-group' + ((json['preorder_phone'] == 2) ? ' required' : '') + '">';
				html  += '<div class="col-sm-4">';
				html  += '<label class="control-label" for="input-preorder-phone">' + json['entry_phone'] + '</label>';	  
				html  += '</div>';
				html  += '<div class="col-sm-8">';
				html  += '<input type="tel" name="preorder_phone" value="' + json['phone'] + '" id="input-preorder-phone" class="form-control" placeholder="' + json['entry_phone'] + '" />';
				html  += '</div>';
				html  += '</div>';
			}
			
			html  += '<input type="hidden" name="preorder_product_id" value="" />';
			
			if (json['captcha']) {
				html  += json['captcha'];
			}
			
			html  += '<div id="preorder_option"></div>';
			html  += '</form>';
			html  += '</div>';
			html  += '<div class="modal-footer">';
			html  += '<button type="button" class="btn btn-warning" onclick="submitPreOrder();">' + json['button_submit'] + '</button>';
			html  += '</div>';
			html  += '</div>';
			html  += '</div>';
			html  += '</div>';
			
			$('#content').parent().before(html);
		}
	});
}