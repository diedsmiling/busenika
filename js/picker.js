//
// $Id: picker.js 9769 2010-06-09 11:37:39Z lexa $
//

jQuery.extend({
	pickers_stack: [],
	//
	// Show picker
	//
	show_picker: function(id, target_id, inner_selector, iframe_obj)
	{
		var elm = $('#' + id);
		var data_id = target_id ? target_id.str_replace('opener_picker_', '') : '';
		var exist_picker = '';
		if (iframe_urls[data_id] && (exist_picker = $('iframe[src=' + iframe_urls[data_id] + ']').parents('.cm-picker:first').get(0)) && !$(exist_picker).is(':visible')) {
			elm = $('#' + $(exist_picker).attr('id'));
		}

		if (iframe_extra[data_id]) {
			var url_parts = iframe_urls[data_id].split(encodeURIComponent(iframe_extra[data_id]));
			if ((exist_picker = $('iframe[src^=' + url_parts[0] + '][src$=' + url_parts[1] + ']').parents('.cm-picker:first').get(0)) && !$(exist_picker).is(':visible')) {
				elm = $('#' + $(exist_picker).attr('id'));
			}
		}

		var last_picker = this.pickers_stack[this.pickers_stack.length - 1];
		if (last_picker && last_picker.link.get(0) == elm.get(0)) {
			return false;
		}

		if (parent.window != window) {
			if (!$(parent.document).contents().find('#' + id).length) {
				elm.wrap('<div id="wraper_' + id + '"></div>');
				$('body', parent.document).append($('#wraper_' + id).html());
				$('#wraper_' + id).remove();
				if (iframe_urls[data_id]) {
					parent.window.iframe_urls[data_id] = iframe_urls[data_id];
				}
				if (iframe_extra[data_id]) {
					parent.window.iframe_extra[data_id] = iframe_extra[data_id];
				}
			}
			for (var i = 0; i < parent.window.length; i++) {
				if (parent.window.frames[i] == window) {
					parent.window.jQuery.show_picker(id, target_id, inner_selector, parent.window.frames[i]);
					return false;
				}
			}
			return false;
		}

		var cur_picker = {};
		cur_picker.target_id = data_id;
		cur_picker.processed_js_item = '';
		cur_picker.dest_iframe = iframe_obj ? iframe_obj : null;
		var w = jQuery.get_window_sizes();
		var tree = $('#server_file_tree');

		if ((elm.parents(':hidden').length || elm.parents('.cm-popup-box').length || elm.parents('.cm-list-box').length || jQuery.browser.msie && jQuery.ua.version == '6.0') && !elm.parent().is('body')) {
			elm.appendTo(document.body);
		}

		var _cont = {};
		$('div.cm-picker-data-container', elm).each(function() {
			if ($(this).parents('.cm-picker').length == 1) {
				_cont = $(this);
			}
		});

		var _iframe = $('iframe', _cont);

		if ((_cont.length && !_iframe.length) && (!exist_picker || $(exist_picker).is(':visible')) && iframe_urls[data_id]) {
			$(_cont).append('<iframe src="' + iframe_urls[data_id] + '" id="iframe_' + data_id + '" frameborder="0" height="100" width="100%" scrolling="auto"></iframe>');
			_iframe = $('#iframe_' + data_id);
		}

		cur_picker.link = elm;

		if (_iframe.length && !_iframe.parents('.mceEditor').length && (!_iframe.get(0).contentWindow || !_iframe.get(0).contentWindow.window.document_loaded)) {
			_iframe.load(function () {
				$('#ajax_loading_box').hide();
				jQuery.show_picker(id, target_id, inner_selector, iframe_obj || null);
			});

			$('#ajax_loading_box').show();
			return false;
		}

		if (!jQuery.trim($('.cm-popup-content-footer', elm).html())) {
			$('.cm-popup-content-footer', elm).data('callback', function () {
				jQuery.show_picker(id, target_id, inner_selector, iframe_obj || null);
			});

			var a = $('a#opener_' + id);

			if (a.hasClass('cm-ajax-update')) {
				jQuery.loadAjaxLinks(a, true);
			} else {
				$('#ajax_loading_box').show();
			}
			return false;
		}

		var z_index = last_picker ? last_picker.z_index + 1 : 26;
		var popup_bg = $('<div class="cm-popup-bg"></div>').appendTo('body');
		popup_bg.css({'opacity': '0.1', 'z-index': z_index, 'display': 'block', 'height': (w.height < w.view_height ? w.view_height : w.height) + 'px'});
		popup_bg.click(function(){
			jQuery.hide_picker(false, true);
		});
		if (jQuery.browser.msie && jQuery.ua.version == '6.0') {
			var inner_bg = popup_bg.clone(true);
			$('<iframe frameborder="0" tabindex="-1" src="javascript:false;" style="display:block; position:absolute; z-index:-1; filter:Alpha(Opacity=\'0\');" width="100%" height="100%"></iframe>').appendTo(popup_bg);
			inner_bg.appendTo(popup_bg);
			inner_bg.click(function(){
				jQuery.hide_picker(false, true);
			});
		}
		cur_picker.popup_bg_link = popup_bg;

		cur_picker.z_index = z_index + 1;
		var padding_side = 110; // padding (sum) from screen left and right 
		var button_padding = 40;
		var padding_bottom = 75; // padding from screen bottom
		var child_offset = 10; // offset of each child picker regarding parent picker
		var scrollbar_size = 17; 
		var initial_padding_top = 45;
		var max_picker_width = 1024; // maximum picker width

		elm.css({
			'display': 'block', 
			'z-index': cur_picker.z_index, 
			'visibility': 'hidden', 
			'width': ((w.view_width >= max_picker_width + padding_side) ? max_picker_width : (w.view_width - padding_side)) + 'px'
		});

		this.pickers_stack.push(cur_picker);

		var padding_top = last_picker ? (parseInt(last_picker.link.css('top')) - w.offset_y + child_offset) : initial_padding_top;

		if (!inner_selector) {
			var iframe_size = _iframe.get(0).contentWindow.jQuery.get_window_sizes();
			var diff = elm.height() - _iframe.height();
			_iframe.height(w.view_height - initial_padding_top - padding_bottom - diff);
			_iframe.get(0).contentWindow.fn_reset_checkbox();
			if ($('#' + data_id).hasClass('cm-display-radio')) {
				var v = $('input.cm-picker-value', $('#' + data_id)).val() || 0;
				var ic = _iframe.get(0).contentWindow;
				ic.jQuery('.cm-item').removeAttr('checked');
				ic.jQuery('.cm-item[value=' + v + ']').attr('checked', 'checked');
			}
		} else {
			var parents = 100;
			var scroll_elm = null;
			$(inner_selector, elm).each(function() {
				if ($(this).parents('.cm-picker').length < parents) {
					parents = $(this).parents('.cm-picker').length;
					scroll_elm = $(this);
				}
			});
			var diff = elm.height() - scroll_elm.height();
			scroll_elm.css('height', '');
			scroll_elm.css({'height': (w.view_height - initial_padding_top - padding_bottom - diff) + 'px'});
			elm.data('scroll_elm', scroll_elm);
		}

		var padding_left = last_picker ? (parseInt(last_picker.link.css('left')) - w.offset_x + child_offset) : ((w.view_width - elm.outerWidth() - scrollbar_size) / 2);
		elm.css({'visibility': 'visible', 'left': w.offset_x + padding_left + 'px', 'top': w.offset_y + padding_top + 'px'});
		
		if (jQuery.browser.msie && jQuery.ua.version == '6.0') {
			$('.cm-popup-hor-resizer', elm).css('height', elm.height() + 'px');
			$('.cm-left-resizer', elm).css('filter', 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_w.png, sizingMethod=scale)');
			$('.cm-right-resizer', elm).css('filter', 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_e.png, sizingMethod=scale)');
			$('.cm-nw-resizer', elm).css('filter', 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_nw.png, sizingMethod=scale)');
			$('.cm-ne-resizer', elm).css('filter', 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_ne.png, sizingMethod=scale)');
			$('.cm-sw-resizer', elm).css('filter', 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_sw.png, sizingMethod=scale)');
			$('.cm-se-resizer', elm).css('filter', 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_se.png, sizingMethod=scale)');
			$('.cm-top-resizer', elm).css('filter', 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_n.png, sizingMethod=scale)');
			$('.cm-bottom-resizer', elm).css('filter', 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + images_dir + '/shadow_s.png, sizingMethod=scale)');
		}

		if (elm.data('callback')) {
			elm.data('callback')();
		}

		if (!elm.data('easydrag')) {
			elm.easydrag();
			elm.startdrag(function(e, element){
				$(element).css('height', elm.height() + 'px');
				$(element).children().hide();
				$('.clear', element).css('visibility', 'hidden');
				$(element).addClass('cm-dashed-box');
			});
			elm.ondrop(function(e, element){
				$(element).children().show();
				$('.clear', element).css('visibility', 'visible');
				$(element).removeClass('cm-dashed-box');
				$(element).css('height', '');
			});
			elm.children().not('.cm-popup-content-footer').filter('.cm-popup-vert-resizer,.cm-popup-hor-resizer,.cm-popup-corner-resizer').easydrag();
			elm.data('easydrag', true);
		}

		if (iframe_extra[data_id]) {
			_iframe.contents().find('form').attr('action', fn_url(index_script + '?' + iframe_extra[data_id]));
			_iframe.contents().find('input[name=extra]').val(iframe_extra[data_id]);
		}
	},

	hide_picker: function(restore, from_background)
	{
		if (from_background == true && !this.pickers_stack[this.pickers_stack.length - 1].link.hasClass('cm-bg-close')) {
			return;
		}
		var last_picker = this.pickers_stack.pop();
		if (last_picker.link.hasClass('cm-clear-content')) {
			$('.cm-popup-content-footer', last_picker.link).empty();
		}
		last_picker.link.hide();
		last_picker.popup_bg_link.remove();
		if (restore) {
			var reset_items = $('.cm-row-item.cm-delete-row', last_picker.link).length ? $('.cm-row-item.cm-delete-row .cm-delete-row', last_picker.link) : ($('tr.cm-delete-row', last_picker.link).length ? $('tr.cm-delete-row .cm-delete-row', last_picker.link) : null);
			if (reset_items) {
				reset_items.click();
			}
		}
		if (restore && last_picker.processed_js_item) {
			this.mass_delete_js_item(last_picker.processed_js_item, last_picker.target_id, last_picker.dest_iframe ? last_picker.dest_iframe.document : null);
		}
		jQuery.closePopups(null, $('.calendar-box:visible'));
	},

	redraw_picker: function(option_obj)
	{
		var elm = option_obj.parents('.cm-picker').eq(0);
		var w = jQuery.get_window_sizes();
		var padding_top = 10;
		var padding_bottom = 15;
		var max_height = w.view_height - padding_top - padding_bottom;
		var scroll_elm = $('iframe', elm).length ? $('iframe', elm) : (elm.data('scroll_elm') ? elm.data('scroll_elm') : null);

		if (scroll_elm) {
			if (elm.height() > max_height) {
				var diff = elm.height() - scroll_elm.height();
				scroll_elm.height(max_height - diff);
			}
			elm.animate({top: w.offset_y + padding_top + 'px'}, 400);
		}
	},

	update_comma_ids: function(ids_obj, delete_id)
	{
		var ids = ids_obj.val().split(',');
		var prod_id = delete_id.split('_');
		for(var i = 0; i < ids.length; i++) {
			if (ids[i] == delete_id || ids[i].indexOf(prod_id[0] + '=') == 0) {
				ids.splice(i, 1);
				i--;
			}
		}
		ids_obj.val(ids.join(','));
	},

	delete_js_item: function(root_id, delete_id, prefix, iframe_obj)
	{
		var last_picker = this.pickers_stack[this.pickers_stack.length - 1];
		var jid = '#' + root_id;
		var jdest = last_picker && last_picker.dest_iframe && $(jid, last_picker.dest_iframe.document).length ? $(jid, last_picker.dest_iframe.document) : (iframe_obj && $(jid, iframe_obj).length ? $(jid, iframe_obj) : $(jid));

		if (delete_id == 'delete_all') {
			$('.cm-js-item:visible', jdest).remove();
		} else {
			var obj_id = root_id + '_' + delete_id;
			$('#' + obj_id, jdest).remove();
		}

		var ids_id = '#' + prefix + root_id + '_ids';
		var ids_obj = last_picker && last_picker.dest_iframe && $(ids_id, last_picker.dest_iframe.document).length ? $(ids_id, last_picker.dest_iframe.document) : (iframe_obj && $(ids_id, iframe_obj).length ? $(ids_id, iframe_obj) : $(ids_id));
		if (ids_obj.length) {
			this.update_comma_ids(ids_obj, delete_id);
		}

		var no_item_id = '#' + root_id + '_no_item';
		var no_item = last_picker && last_picker.dest_iframe && $(no_item_id, last_picker.dest_iframe.document).length ? $(no_item_id, last_picker.dest_iframe.document) : (iframe_obj && $(no_item_id, iframe_obj).length ? $(no_item_id, iframe_obj) : $(no_item_id));
		if ($('.cm-js-item', jdest).length <= 1 && no_item.length) {
			if (!jdest.find('#' + root_id + '_no_item').length) {
				jdest.hide();
			}
			no_item.show();
		}
		$('.cm-js-item:visible:first .cm-comma', jdest).hide();
		if (jdest.parents('.cm-picker').length) {
			var openeer_id = '#opener_inner_' + root_id;
			var opener_link = last_picker && last_picker.dest_iframe && $(openeer_id, last_picker.dest_iframe.document).length ? $(openeer_id, last_picker.dest_iframe.document) : (iframe_obj && $(openeer_id, iframe_obj).length ? $(openeer_id, iframe_obj) : $(openeer_id));
			opener_link.text($('.cm-js-item', jdest).length - 1);
		}
	},

	mass_delete_js_item: function(items_str, target_id, iframe_obj)
	{
		var items = items_str.split(',');
		for (var id in items) {
			var parts = items[id].split(':');
			jQuery.delete_js_item(target_id, parts[1], parts[0], iframe_obj);
		}
	},

	add_js_item: function(js_items, prefix, exception_message, close_picker)
	{
		var last_picker = this.pickers_stack[this.pickers_stack.length - 1];
		var root_id = last_picker.target_id;
		var jid = '#' + root_id;
		var iframe_obj = null;
		var jroot = $(jid);
		if (last_picker.dest_iframe && $(jid, last_picker.dest_iframe.document).length) {
			iframe_obj = last_picker.dest_iframe;
			jroot = $(jid, last_picker.dest_iframe.document);
		}
		var root = jroot.get(0);
		var ids_obj = last_picker.dest_iframe && $('#' + prefix + root_id + '_ids', last_picker.dest_iframe.document).length ? $('#' + prefix + root_id + '_ids', last_picker.dest_iframe.document) : $('#' + prefix + root_id + '_ids');

		if (close_picker) {
			$('.cm-popup-bg:last').click();
		}

		if (ids_obj.length) {
			var ids = ids_obj.val() != "" ? ids_obj.val().split(',') : [];
		}

		for(var id in js_items) {
			if (jroot.hasClass('cm-display-radio')) {
				$('input.cm-picker-value', jroot).val(id);
				$('input.cm-picker-value-description', jroot).val(js_items[id]);
			} else {
				var child_id = root_id + '_' + id;
				var child = $('#' + child_id);
				var ids_item = id;
				if (!child.length && root){
					var append_obj = iframe_obj ? iframe_obj.window.jQuery(jid + ' .cm-clone').clone(true).appendTo(iframe_obj.window.jQuery(jid)).attr('id', child_id).removeClass('hidden cm-clone') : $('.cm-clone', jroot).clone(true).appendTo(jroot).attr('id', child_id).removeClass('hidden cm-clone');
					var append_obj_content = '';
					if (!close_picker) {
						last_picker.processed_js_item += (last_picker.processed_js_item == '' ? '' : ',') + prefix + ':' + id;
					}
					if (prefix == 'u') {
						append_obj_content = unescape(append_obj.html()).str_replace('{email}', js_items[id].email).str_replace('{user_name}', js_items[id].user_name).str_replace('{user_id}', id);
					} else if (prefix == 'o') {
						// for use in js-object window
						append_obj_content = unescape(append_obj.html()).str_replace('{order_id}', id);
						for (var index in js_items[id]) {
							append_obj_content = append_obj_content.str_replace('{' + index + '}', js_items[id][index]);
						}
					} else if (prefix == 'a') {
						append_obj_content = unescape(append_obj.html()).str_replace('{page_id}', id).str_replace('{page}', js_items[id]);
					} else if (prefix == 'c') {
						append_obj_content = unescape(append_obj.html()).str_replace('{category_id}', id).str_replace('{category}', js_items[id]);
					} else if (prefix == 'p') {
						if (!jroot.hasClass('cm-picker-options')) {
							append_obj_content = unescape(append_obj.html()).str_replace('{delete_id}', id).str_replace('{product}', js_items[id]);
						} else {
							var options_combination = id;
							for(var ind in js_items[id].option.path) {
								options_combination += "_" + ind + "_" + js_items[id].option.path[ind];
							}
							var product_id = jQuery.crc32(options_combination);
							if (!$('#' + root_id + "_" + product_id).length) {
								var input_prefix = $('input', append_obj).attr('name').str_replace('[{product_id}][amount]', '[' + product_id + ']');
								var inputs = '<input type="hidden" name="' + input_prefix + '[product_id]' + '" value="' + id + '" />';
								js_items[id]['product_id'] = product_id;
								
								for(var ind in js_items[id].option.path) {
									inputs += '<input type="hidden" name="' + input_prefix + '[product_options][' + ind + ']' + '" value="' + js_items[id].option.path[ind] + '" />';
								}
								$('input', append_obj).val(1);
								append_obj.attr('id', root_id + "_" + product_id);
								append_obj_content = unescape(append_obj.html()).str_replace('{product}', js_items[id].value).str_replace('{options}', js_items[id].option.desc + inputs).str_replace('{root_id}', root_id).str_replace('{delete_id}', product_id).str_replace('{product_id}', product_id);
							}
							if (exception_message) {
								alert(exception_message);
							}
						}
					}

					var hook_data = {
						'append_obj_content': append_obj_content,
						'var_prefix': prefix,
						'object_html': unescape(append_obj.html()),
						'var_id': id,
						'item_id': js_items[id]
					};
					fn_set_hook('add_js_item', hook_data);
					append_obj_content = hook_data.append_obj_content;

					if (append_obj_content) {
						append_obj.html(append_obj_content);
						if (ids_obj.length) {
							ids.push(ids_item);
						}
					} else {
						append_obj.remove();
					}
					$('input', append_obj).removeAttr('disabled');
					var comma = $('.cm-comma', append_obj);
					if ($('.cm-js-item', jroot).length > 2 && comma.length) {
						comma.show();
					}
				}
				if (ids_obj.length) {
					var ids_str = ids.join(',');
					ids_obj.val(ids_str);
				}
			}
		}

		if (jroot.parents('.cm-picker').length) {
			var opener_link = last_picker.dest_iframe && $('#opener_inner_' + root_id, last_picker.dest_iframe.document).length ? $('#opener_inner_' + root_id, last_picker.dest_iframe.document) : $('#opener_inner_' + root_id);
			opener_link.text($('.cm-js-item', jroot).length - 1);
		}

		if ($('.cm-js-item', jroot).length > 1) {
			jroot.show();
			var no_item = last_picker.dest_iframe && $('#' + last_picker.target_id + '_no_item', last_picker.dest_iframe.document).length ? $('#' + last_picker.target_id + '_no_item', last_picker.dest_iframe.document) : $('#' + last_picker.target_id + '_no_item');
			no_item.hide();
		}
	},

	submit_picker: function(picker_id, button_id, options_id)
	{
		options_id = options_id || '';
		if (options_id) {
		
			if (!jQuery.browser.msie){
				$(picker_id).contents().find('form:has(' + button_id + ')').append($(options_id).clone().hide());
			}else{
				$(picker_id).contents().find('form:has(' + button_id + ')').append('<div id="' + $(picker_id).attr('id') + '_cloned_opts"' + ' class="hidden">' + $(options_id).html() + '</div>');
				if (parseInt(jQuery.ua.version) > 7){
					
					 var disp_checkboxes = $(options_id).contents().find("input[type=checkbox]");
					 
					 if (disp_checkboxes.length > 0){
					 	
						 var opts_el =  $(picker_id).contents().find(picker_id + '_cloned_opts');
						 
						 var checkboxes = opts_el.contents().find("input[type=checkbox]");
						 
						 for(var i = 0; i < checkboxes.length; i++){
						 	checkboxes[i].checked = disp_checkboxes[i].checked;  
						 }
					}	 
				}
			}	
		    
		}
		$(picker_id).contents().find(button_id).click();
	}
});

