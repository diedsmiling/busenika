// $Id: fileuploader_scripts.js 10471 2010-08-20 11:29:27Z andyye $

var fileuploader = {

	result_id: '',
	
	set_file: function(name, tiny)
	{
		if (tiny) {
			parent.window.jQuery('iframe').contents().find('#advimage #src').val(name);
			parent.window.jQuery('iframe').contents().find('#advimage #src').trigger('change');
			tinyMCEPopup.close();
		} else {
			this.display_filename(this.result_id, 'server', name);
			$('.cm-popup-bg:last').click();
		}
	},

	show_image: function(name)
	{
		$('#fo_img').attr('src', current_path + '/' + name);
	},

	init: function(dialog_id, result_id)
	{
		this.result_id = result_id;

		jQuery.show_picker('view_' + dialog_id, '', '.object-container');
	},

	show_loader: function(elm_id)
	{
		var suffix = elm_id.str_replace('_local_', '').str_replace('server_', '').str_replace('url_', '');

		if (elm_id.indexOf('_local') != -1) {
			this.display_filename(suffix, 'local', $('#' + elm_id).val());
		}

		if (elm_id.indexOf('server') != -1) {
			fileuploader.init('box_server_upload', suffix);
		}

		if (elm_id.indexOf('url') != -1) {
			var e_url = $('#message_' + suffix + ' span').html();
			var n_url = '';

			if (n_url = prompt($('#' + elm_id).html(), (e_url.indexOf('://') !== -1) ? e_url : '')) {
				if (this.validate_url(n_url)) {
					this.display_filename(suffix, 'url', n_url);
				} else {
					alert(lang.text_invalid_url);
				}
			}
		}
	},

	display_filename: function(id, type, val)
	{
		// Highlight active link
		var types = ['local', 'server', 'url'];
		var file = $('#message_' + id + ' p.cm-fu-file');
		var no_file = $('#message_' + id + ' p.cm-fu-no-file');

		for (var i = 0; i < types.length ;i++ )	{
			if (types[i] == type) {
				$('#' + types[i] + '_' + id).addClass('active');
			} else {
				$('#' + types[i] + '_' + id).removeClass('active');
			}
		}

		$('#type_' + id).val(type); // switch type
		$('#file_' + id).val(val); // set file name

		if (val == '') {
			file.hide();
			no_file.show();
		} else {
			no_file.hide();
			
			// cut off the C:/Fakepath C:\Fake path or whatever standards compliant stuff proposed by IE7+ and Opera
			var pieces = val.split(/(\\|\/)/g);
			var val = pieces[pieces.length-1];
			$('span', file).html(val).attr('title', val); // display file name
			file.show();
		}
	},

	clean_selection: function(elm_id)
	{
		var suffix = elm_id.str_replace('clean_selection_', '');

		this.display_filename(suffix, '', '');

	},
	
	get_content_callback: function(data) 
	{
		if (data.content.indexOf('text:') == 0) {
			$('#fo_img').hide();
			$('#fo_no_preview').hide();
			$('#fo_preview').show();
			$('#fo_preview').val(data.content.substr(5));
		} else if (data.content.indexOf('image:') == 0) {
			$('#fo_img').show();
			$('#fo_no_preview').hide();
			$('#fo_preview').hide();
			fileuploader.show_image(data.content.substr(6));
		} else {
			$('#fo_img').hide();
			$('#fo_no_preview').show();
			$('#fo_preview').hide();
		}
	},

	validate_url: function(url)
	{
		var regexp = /^[A-Za-z]+:\/\/[A-Za-z0-9-_:@\.]+[A-Za-z0-9-\+_%~&\\?\/.=]+$/;
		return regexp.test(url);
	},
	
	check_required_field: function(id_var_name, label_id)
	{
		var found = false;
		var images_count = 0;
		
		$('#' + label_id).val('');
		
		$('div[id*=message_' + id_var_name + '] p:visible span').each(function(){
			if ($(this).html() != '') {
				$('#' + label_id).val(id_var_name).change();
				found = true;
			}
		});
		
		elm_id = $('div[id*=message_' + id_var_name + ']:last').attr('id').str_replace('message_', '');
		
		if (!found) {
			$('#local_' + elm_id).text(custom_labels[id_var_name]['upload_file']);
		} else {
			$('#local_' + elm_id).text(custom_labels[id_var_name]['upload_another_file']);
		}
		
		// Check an ability to delete already uploaded files
		$('div[id*=message_' + id_var_name + '_].cm-uploaded-image p:visible span').each(function(){
			if ($(this).html() != '') {
				images_count++;
			}
		});
		
		if (images_count == 0 || images_count > 1 || !($('label[for=' + label_id + ']').hasClass('cm-required'))) {
			// Allow to delete
			$('img[id*=clean_selection_' + id_var_name + '_]').show();
		} else {
			// Disallow to delete
			$('img[id*=clean_selection_' + id_var_name + '_]').hide();
		}
	},
	
	check_image: function(elm_id)
	{
		var suffix = elm_id.str_replace('_local_', '').str_replace('server_', '').str_replace('url_', '');
		
		if ($('#message_' + suffix + ' span').html() != '') {
			parent_id = $('#file_uploader_' + suffix).cloneNode(1).str_replace('file_uploader_', '');
			$('#link_container_' + suffix).hide();
			
			elm_id = parent_id;
			$('#local_' + parent_id).text(custom_labels[id_var_name]['upload_another_file']);
		}
	},
	
	toggle_links: function(elm_id, mode)
	{
		var suffix = elm_id.str_replace('_local_', '').str_replace('server_', '').str_replace('url_', '').str_replace('clean_selection_', '');
		
		if (mode == 'hide') {
			$('#link_container_' + suffix).hide();
		} else {
			$('#link_container_' + suffix).show();
		}
	}
}
