// $Id: exceptions.js 10500 2010-08-24 13:07:07Z alexions $

function fn_change_options(obj_id, id, option_id)
{
	// Change cart status
	cart_changed = true;
	
	params = [];
	update_ids = [];
	
	parents = $('.cm-reload-' + obj_id);
	jQuery.each(parents, function(id, parent_elm) {
		update_ids.push(parent_elm.getAttribute('id'));
		
		elms = $(':input:not(:radio):not(:checkbox)', parent_elm);
		jQuery.each(elms, function(id, elm) {
			if (elm.type != 'submit' && elm.type != 'file' && !($(this).hasClass('cm-hint') && elm.value == elm.defaultValue) && elm.name.length != 0) {
				params.push({name: elm.name, value: elm.value});
			}
		});
	});
	
	radio = $(':radio:checked, :checkbox', parents);
	jQuery.each(radio, function(id, elm) {
		value = elm.value;
		if ($(elm).is(':checkbox:checked')) {
			if (!$(elm).hasClass('cm-no-change')) {
				value = 'checked';
			}
		} else if ($(elm).is(':checkbox')) {
			if (!$(elm).hasClass('cm-no-change')) {
				value = 'unchecked';
			} else {
				value = '';
			}
		}
		
		params.push({name: elm.name, value: value});
	});
	
	url = fn_url('products.options?changed_option[' + id + ']=' + option_id);
	
	for (i in params) {
		url += '&' + params[i]['name'] + '=' + escape(params[i]['value']);
	}
	
	jQuery.ajaxRequest(url, {
		result_ids: update_ids.join(',').toString(),
		caching: true,
		force_exec: true,
		pre_processing: fn_pre_process_form_files,
		callback: fn_post_process_form_files,
		method: 'post'
	});
	
}

function fn_set_option_value(id, option_id, value)
{
	elm = $('#option_' + id + '_' + option_id);
	if (elm.attr('type') == 'select-one') {
		elm.val(value).change();
	} else {
		elms = $('#option_' + id + '_' + option_id + '_group');
		$(':radio[value=' + value + ']', elms).click();
	}

	return true;
}

function fn_pre_process_form_files(data, params)
{
	if (data.html) {
		// Create temporarily div element
		$('body').append('<div id="file_container" class="hidden"></div>');
		var container = {};
		container = $('#file_container');
		
		// Move files blocks to the temporarily created container
		for (var k in data.html) {
			$('#' + k + ' .fileuploader').each(function(idx, elm){
				jelm = $(elm);
				jparent = jelm.parents('.form-field');
				jparent.appendTo(container);
				jparent.attr('id', 'moved_' + jparent.attr('id'));
			});
		}
	}
}

function fn_post_process_form_files(data, params)
{
	var container = {};
	container = $('#file_container');
	
	$('div.form-field', container).each(function(idx, elm){
		jelm = $(elm);
		elm_id = jelm.attr('id').replace('moved_', '');
		target = $('#' + elm_id);
		target.html('');
		jelm.children().appendTo(target);
	});
	
	container.remove();
}

function fn_change_varian_image(prefix, opt_id, var_id)
{
	var images = {};
	images = $('img[id*=variant_image_' + prefix + '_' + opt_id + ']');
	images.removeClass('product-variant-image-selected').addClass('product-variant-image-unselected');
	
	if (typeof(var_id) == 'undefined') {
		var_id = $('select[id*=_' + prefix + '_' + opt_id + ']').val();
	}
	$('img[id*=variant_image_' + prefix + '_' + opt_id + '_' + var_id + ']').removeClass('product-variant-image-unselected').addClass('product-variant-image-selected');
}