// $Id: quick_menu.js 9727 2010-06-07 10:26:01Z klerik $

var url_prefix = 'http://';

jQuery.extend({
	dispatch_quick_menu_event: function(e)
	{
		var jelm = $(e.target);

		if (e.type == 'click' && jQuery.browser.mozilla && e.which != 1) {
			return true;
		}

		if (e.type == 'click') {
			if (jelm.hasClass('cm-delete-section') && jelm.parents('#quick_menu').length) {
				var root = jelm.parents('tr:first');

				jQuery.ajaxRequest(fn_url('tools.remove_quick_menu_item'), {data: {id: root.attr('item'), parent_id: root.attr('parent_id')}, result_ids: 'quick_menu', callback: fn_quick_menu_content_switch_callback});

			} else if (jelm.hasClass('cm-add-link') && jelm.parents('#quick_menu').length) {
				fn_show_quick_box('', jelm.parents('tr:first').attr('item'), '', url_prefix, '');
				return false;

			} else if (jelm.hasClass('cm-add-section') && jelm.parents('#quick_menu').length) {
				fn_show_quick_box('', 0, '', '', '');
				return false;

			} else if (jelm.hasClass('cm-update-item') && jelm.parents('#quick_menu').length) {
				var root = jelm.parents('tr:first');
				var name_holder = $('.cm-qm-name:first', root);

				fn_show_quick_box(root.attr('item'), Number(root.attr('parent_id')), name_holder.text(), (name_holder.attr('href') ? name_holder.attr('href') : ''), root.attr('pos'));
				return false;

			} else if (jelm.attr('id') == 'qm_current_link') {
				$('#qm_item_link').val(location.href);
				return false;

			} else if (jelm.hasClass('cm-lang-link') && jelm.parents('.cm-select-list').length) {
				fn_change_language(jelm.attr('name'));
				jQuery.ajaxRequest(jelm.attr('href'), {data: {id: $('#qm_item_id').val()}, caching: false, callback: fn_change_quick_box});

				jelm.parents('.cm-popup-box:first').hide();
				return false;
			}
		}
	}
});

function fn_change_language(lang_code)
{
	var sl = $('.select-lang', $('#quick_box'));
	if (sl.children().length) {
		var jelm = $('a[name=' + lang_code +']', sl);
		var icon = jelm.css('background-image');
		icon = icon.str_replace('url(', '');
		icon = icon.str_replace(')', '');

		$('img.icons', sl).attr('src', icon); // set new image
		$('a.cm-combination', sl).text(jelm.text()); // set new text
		$('#qm_descr_sl').val(lang_code); // change descriptions language
	}
}

function fn_change_quick_box(data) 
{
	$('#qm_item_name').val(data.description);
}

function fn_show_quick_box(id, parent_id, name, url, pos) 
{
	var quick_box = $('#quick_box');

	$('#qm_item_id').val(id);
	$('#qm_item_parent').val(parent_id);
	$('#qm_item_name').val(name);
	$('#qm_item_link').val(url);
	$('#qm_item_position').val(pos);

	fn_change_language(cart_language);

	var sl = $('.select-lang', quick_box);
	if (sl.children().length) {
		$('ul.cm-select-list a', sl).addClass('cm-lang-link');
	}

	var link_holder = $('#qm_item_link').parents('.form-field:first');

	if (parent_id) {
		link_holder.show();
		$('label', link_holder).addClass('cm-required');
		$('#qm_current_link').parents('.form-field:first').show();
		$('h3:first', quick_box).hide();
		$('h3:last', quick_box).show();
	} else {
		link_holder.hide();
		$('label', link_holder).removeClass('cm-required');
		$('#qm_current_link').parents('.form-field:first').hide();
		$('h3:first', quick_box).show();
		$('h3:last', quick_box).hide();
	}

	var w = jQuery.get_window_sizes();
	quick_box.show();
	var y = w.offset_y + (w.view_height - quick_box.height()) / 2;
	var x = w.offset_x + (w.view_width - quick_box.width()) / 2;
	quick_box.css({'top': y + 'px', 'left': x + 'px'});
}

function fn_callback_quick_menu_form(data) 
{
	$('#quick_box').hide();
	fn_quick_menu_content_switch_callback();
}

function fn_init_quick_box() 
{
	$('#quick_box').easydrag();
	$('#quick_box').prependTo('body');
	fn_quick_menu_content_switch_callback();
}

$(document).bind('click', function(e) {
	return jQuery.dispatch_quick_menu_event(e);
});

jQuery.ajaxRequest(fn_url('tools.show_quick_menu.edit'), {data: {popup: true}, result_ids: 'quick_menu', callback: fn_init_quick_box});