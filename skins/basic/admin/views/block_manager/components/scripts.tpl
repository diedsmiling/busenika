{* $Id: scripts.tpl 10473 2010-08-20 12:05:47Z andyye $ *}

<script type="text/javascript">
//<![CDATA[
	var check_parent_url = '{"block_manager.check_parent"|fn_url:'A':'rel':'&'}';
	var settings = {$block_settings.dynamic|to_json};
	{foreach from=$block_settings.additional key="section" item="section_data"}
		{foreach from=$section_data.items key="object_name" item="listed_block"}
			settings['{$object_name}'] = {$listed_block};
		{/foreach}
	{/foreach}
	
	lang.not_applicable = '{$lang.not_applicable|escape:"javascript"}';

	block_properties = new Array();
	block_location = new Array();
	block_properties_used = new Array();

	{literal}
	function fn_check_block_params(new_block, location, block_id, owner, block_type)
	{
		var selected_status = new Array();

		var prefix = location + '_' + block_id + block_type + '_';
		var prop = new_block ? '' : block_properties[prefix];
		var prop_used = new_block ? '' : block_properties_used[prefix];
		var setting_name = '';

		selected_status['locations'] = new Array();
		selected_status['positions'] = '';

		// Define selected location (tab)
		if (_id = $('#add_selected_section_' + block_id + block_type).val()) {
			selected_status['locations'].push(_id);
		}

		section = $('#' + prefix + 'block_object').val();

		if (!settings[section]) {
			dis = true;
			section = 'products';
		} else {
			dis = false;
		}

		if (prop !== '' && prop_used == false) {
			selected_status = prop;
			block_properties_used[prefix] = true;
		} else {
			for (setting_name in settings[section]) {
				var _val = $('#'  + prefix + 'id_' + setting_name).val();

				if (!_val || !settings[section][setting_name][_val]) {
					for (var kk in settings[section][setting_name]) {
						_val = kk;
						break;
					}
				}

				selected_status[setting_name] = _val;
			}
		}

		for (setting_name in settings[section]) {
			// Disable static block
			current_dis = (setting_name) == 'positions' ? false : dis;

			$('#' + prefix + 'id_' + setting_name).attr('disabled', current_dis);
			var setting = settings[section][setting_name];
			var select = document.getElementById(prefix + 'id_' + setting_name);

			if (select && select.options) {
				i = 0;
				value = selected_status[setting_name] || $(select).val();
				select.options.length = 0;

				if (current_dis != true) {
					// Check current setting (selectbox), and rebuild selectbox
					for (val in setting) {
						// object, need check condition
						add_option = true;
						if ($(setting[val]).length == 1) {
							for (cond in setting[val].conditions) {
								add_option = false;
								if (selected_status[cond]) {
									for (var ii in setting[val].conditions[cond]) {
										if (setting[val].conditions[cond][ii] == selected_status[cond]) {
											add_option = true;
											break;
										}
									}
								}
							}
						}

						// Check if filling applicable to certain locations only
						if (setting_name == 'fillings' && setting[val]['locations'] && jQuery.inArray(location, setting[val]['locations']) == -1) {
							add_option = false;
						}

						if (add_option == true) {
							select.options[i] = new Option(setting[val]['name'] || setting[val], val);
							i++;
						}
					}

					selected_status[setting_name] = value;
					$(select).val(value);
					$('option', $(select)).each( function() {
						if (this.value == value) {
							this.defaultSelected = true;
						}
					});

					if (owner && select.options.length != 0) {
						if (select.id == prefix + 'id_fillings' && owner.id != prefix + 'id_positions' && owner.id != prefix + 'id_appearances') {
							fn_get_specific_settings($(select).val(), block_id, 'fillings', block_type);
						} else if (select.id == prefix + 'id_appearances') {
							fn_get_specific_settings($(select).val(), block_id, 'appearances', block_type);
						}
					}
				}

				if (select.options.length == 0 || current_dis == true) {
					// disabled option
					select.options[i] = new Option(lang.not_applicable, '');
					select.disabled = true;
					if (select.id == prefix + 'id_fillings') {
						$('#toggle_' + block_id + block_type + '_fillings').empty();
					} else if (select.id == prefix + 'id_appearances') {
						$('#toggle_' + block_id + block_type + '_appearances').empty();
					}
				}
			}
		}

		return true;
	}

	function fn_show_block_picker(data)
	{
		jQuery.show_picker('edit_block_picker', null, '.object-container');
	}

	function fn_get_specific_settings(value, block_id, type, block_type)
	{
		{/literal}
		jQuery.ajaxRequest('{"block_manager.specific_settings?type="|fn_url:'A':'rel':'&'}' + type + '&value=' + value + '&block_id=' + block_id + '&block_type=' + block_type, {ldelim}
		{literal}
			result_ids: 'toggle_' + block_id + block_type + '_' + type,
			caching: true,
			callback: function() {
				if ($('#toggle_' + block_id + block_type + '_' + type).html() == '') {
					$('#container_' + block_id + block_type + '_' + type).hide();
				} else {
					$('#container_' + block_id + block_type + '_' + type).show();
				}
			}
		});
	}

	function fn_check_block_parent(block_id, holder, location, object_id)
	{
		var data_obj = {};
		data_obj['block_id'] = block_id;
		data_obj['object_id'] = object_id;
		data_obj['location'] = location;
		jQuery.ajaxRequest(check_parent_url, {caching: false, data: data_obj, callback: fn_check_block_parent_callback, select_id: holder.id});
	}

	function fn_check_block_parent_callback(data, params)
	{
		if (typeof(data.confirm_text) != 'undefined') {
			$('#' + params.select_id + '_rewrite').val(confirm(data.confirm_text) ? 'Y' : 'N');
		}
	}
	{/literal}
//]]>
</script>
