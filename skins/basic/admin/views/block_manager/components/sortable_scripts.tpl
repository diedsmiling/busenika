{* $Id: scripts.tpl 8016 2009-09-18 14:29:21Z alexions $ *}

{script src="js/iutil.js"}
{script src="js/idrag.js"}
{script src="js/idrop.js"}
{script src="js/isortables.js"}

<script type="text/javascript">
//<![CDATA[

lang.text_position_updating = '{$lang.text_position_updating|escape:"javascript"}';
var selected_section = '{$location}';
var update_pos_url = '{"block_manager.save_layout"|fn_url:'A':'rel':'&'}';
var block_object_id = '{$object_id}';

{literal}
$(document).ready(function(){
	var h_height = 100;
	var h_width = 300;

	$('.cm-sortable-items').Sortable({
		accept: 'cm-list-box',
		helperclass: 'ui-select',
		handle: 'h4:not(.cm-fixed-block)',
		tolerance: 'intersect',
		opacity: 0.5,
		restriction: [{owner: 'cm-group-box', rest_rule: '.cm-disallowed-group'}, {owner: 'cm-tabs-block', rest_rule: ':not(.cm-product-details)'}],
		onStart: function(elm) {
			var w = jQuery.get_window_sizes();
			$('html,body').css('overflow-x', 'hidden');
			// FF scrolls page to top, after overflow-x changing ((
			if (w.offset_y && jQuery.ua.browser == 'Firefox') {
				$(document).scrollTop(w.offset_y);
			}
		
			jQuery.iDrag.helper.children().hide();
			var drag_height = $(elm).height() > h_height ? h_height : $(elm).height();
			jQuery.iDrag.helper.css('height', drag_height);
			jQuery.iDrag.helper.append('<div class="ui-drag"></div>');
			$('.ui-drag', jQuery.iDrag.helper).css({'height': drag_height, 'width' : h_width});
		},
		onStop: function(elm) {
			var w = jQuery.get_window_sizes();
			$('html,body').css('overflow-x', '');
			if (w.offset_y && jQuery.ua.browser == 'Firefox') {
				$(document).scrollTop(w.offset_y);
			}

			$('div.cm-sortable-items').each(function() {
				$('.cm-list-box', this).length == 2 ? $('p.no-items', this).show() : $('p.no-items', this).hide();
			});

			fn_save_blocks_position();
		},
		onDrag: function(elm) {
			var w = jQuery.get_window_sizes();
			var helper_obj = jQuery.iDrag.helper;
			var pos = helper_obj.offset();
			if (pos.top < w.offset_y) {
				$(document).scrollTop(w.offset_y - 20);
			} else if (pos.top + jQuery.iDrag.helper.height() > w.offset_y + w.view_height) {
				$(document).scrollTop(w.offset_y + w.view_height + 20 < $('body').height() ? w.offset_y + 20 : $('body').height() - w.view_height);
			}
		}
	});
});

function fn_save_blocks_position(user_choice)
{
	var positions = [];
	var str_positions;

	$('.grab-items').each(function() {
		var self = this;
		var group_id = $('input[name=group_id_' + self.id + ']').val();
		if (!positions[group_id]) {
			positions[group_id] = [];
		}
		$('#' + self.id + ' :input').filter('.block-position').each(function() {
			if ($(this).parents('.grab-items:first').attr('id') == self.id) {
				positions[group_id].push($(this).val());
			}
		});
	});

	var data_obj = {};
	for (var section in positions) {
		if (positions[section].length) {
			data_obj['block_positions[' + section.str_replace('block_content_', '') + ']'] = positions[section].join(',');
		}
	}
	data_obj['add_selected_section'] = selected_section;
	data_obj['object_id'] = block_object_id;
	if (typeof(user_choice) != 'undefined') {
		data_obj['user_choice'] = user_choice;
	}

	jQuery.ajaxRequest(update_pos_url, {method: 'post', caching: false, message: lang.text_position_updating, data: data_obj, callback: fn_save_blocks_position_callback});

	return true;
}

function fn_save_blocks_position_callback(data)
{
	if (typeof(data.confirm_text) != 'undefined') {
		fn_save_blocks_position(confirm(data.confirm_text) ? 'Y' : 'N');
	}
}
//]]>
</script>
{/literal}