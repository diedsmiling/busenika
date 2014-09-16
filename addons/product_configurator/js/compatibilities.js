// $Id: compatibilities.js 9146 2010-03-24 12:17:56Z alexions $

//var compatible_classes = {};
var error_message = 0;
var product_class = 0;
var compatible = {};
var group_has_selected = 0;
var current_step_id = 0;
var forbidden_groups = {}; // This variable will be used for hierarchical analysis of compatibilities

//
// Check required product for the step
//
function fn_check_required_products(step_id, show_section)
{
	for (var groups_ in conf[step_id]) {
		selected_product = fn_define_selected_product(step_id, groups_);
		if (conf[step_id][groups_]['required'] == 'Y' && selected_product == false) {
			if (show_section == 'Y') {
				alert(lang.text_required_group_product.replace('[group_name]', conf[step_id][groups_]['name']));
				//$('#' + step_id).click();
			}
			return false;
		}
	}
	return true;
}

//
// Check whole configurable product for all required groups has selected products
//
function fn_check_all_steps()
{
	for (var step_id in conf) {
		if (fn_check_required_products(step_id, 'N') == false) {
			return false;
		}
	}
	return true;
}

//
// Check if user can go to the section
//
function fn_check_step(new_step_id)
{
	// If we use the "Next" button then find out the new section
	var step_id = current_step_id;
	var get_next = false;
	var i;

	var sections = $('#tabs_configurator > li');
	for (i = 0; i < sections.length; i++) {
		if (!new_step_id) {
			if (get_next == true) {
				new_step_id = sections.eq(i).attr('id');
				get_next = false;
			}
			if (sections.eq(i).attr('id') == step_id) {
				get_next = true;
			}
		}
	}
	var j = sections.eq(i - 1).attr('id');

	// Check whether all required groups have products
	if (fn_check_required_products(step_id, 'Y') == false) {
		return false;
	}

	// if the last section is selected then hide the "Next" button, and show "Add to cart" button
	if (new_step_id == j) {
		$('#next_button').toggleBy(true);
		var sh = fn_check_all_steps();
		$('#pconf_buttons_block').toggleBy(!sh);
	} else {
		$('#next_button').toggleBy(false);
	}

	fn_swith_configurator_tabs(new_step_id);
	current_step_id = new_step_id;
	return true;
}

function fn_swith_configurator_tabs(tab_id)
{
	$('#tabs_configurator > li').each(function()
	{
		$(this).removeClass('cm-active');
		$('#content_' + $(this).attr('id')).hide();
	});

	$('#' + tab_id).addClass('cm-active');
	$('#content_' + tab_id).show();
}

// ************************************************** C O M P A T I B I L I T I E S ****************************************/
//
// Check all compatibilities
//
function fn_check_all_compatibilities()
{
	for (var step_id in conf) {
		for (var group_id in conf[step_id]) {
			selected_product = fn_define_selected_product(step_id, group_id);
			// If any product is selected then define compatibilities for it
			if (selected_product != false && selected_product.indexOf(':') != -1 && free_rec == 0) {
				do {
					fn_check_compatibilities(group_id, selected_product.substring(0, selected_product.indexOf(':')), conf[step_id][group_id]['type'], false);
					selected_product = selected_product.substr(selected_product.indexOf(':')+1);
				} while (selected_product.indexOf(':') != -1);
			} else if (selected_product != false && free_rec == 0) {
				fn_check_compatibilities(group_id, selected_product, conf[step_id][group_id]['type'], false);
			}
		}
	}
	// Check whether refresh was clicked on thу last step
	var s_section = $('#tabs_configurator > li.cm-active').attr('id');
	if (s_section == step_id) {
		$('#next_button').toggleBy(true);
	}
	var sh = fn_check_all_steps();
	$('#pconf_buttons_block').toggleBy(!sh);
}

//
// Check compatibilities for the selected product, update price and show/hide buttons
//
function fn_check_compatibilities(group_id, product_id, type, update_price)
{
	var initial_product_id = [];

	// Define configuration products
	if (type == 'S' && document.getElementById('group_'+group_id).value) {
		initial_product_id = [document.getElementById('group_'+group_id).value];
	} else if (type == 'R' && product_id) {
		initial_product_id = [product_id];
	} else if (type == 'C') {
		for (var k in conf_prod[group_id]) {
			if (document.getElementById('group_' + group_id + '_product_' + k).checked == true) {
				initial_product_id.push(k);
			}
		}
	}

	// Hide selectbox 'details' link if 'none' option selected
	var detail_link_holder = $('#select_' + group_id);
	if (detail_link_holder.length) {
		$('a', detail_link_holder).hide();
		if (type == 'S' && initial_product_id) {
			$('#opener_description_' + group_id + '_' + initial_product_id, detail_link_holder).show();
		}
	}

	// gets the compatible classes
	var compatible_classes = fn_get_compatible_classes(initial_product_id, group_id);
	if (product_id != 0 && compatible_classes) {
		fn_disable_incompatible(compatible_classes, group_id);
	}

	// Check if the last required product was chosen
	var sh = fn_check_all_steps();
	$('#pconf_buttons_block').toggleBy(!sh);
}

//
// This function gets the compatible classes for selected group id and product ids
//
function fn_get_compatible_classes(product_ids, group_id)
{
	// Define compatible classes and their configurator_group_id for selected product
	// Array Сompatible_classes consists of [key] = compatible class id and [value] = configurator_group_id configurator group of this class
	var compatible_classes = {};
	var exists = false;

	for (var i = 0 ; i < product_ids.length ; i++) {
		if (product_ids[i] && conf_prod[group_id][product_ids[i]] && conf_prod[group_id][product_ids[i]]['compatible_classes'] != 'undefined') {
			for (var _class_id in conf_prod[group_id][product_ids[i]]['compatible_classes']) {
				compatible_classes[_class_id] = conf_prod[group_id][product_ids[i]]['compatible_classes'][_class_id];
				exists = true;
			}
		}
	}

	return (exists) ? compatible_classes : false;
}


//
// This function restores current group and call the compatibility analyser function
//
function fn_disable_incompatible(compatible_classes, group_id)
{
	var i = 0;
	var s_selected = '';

	// Restore currrent group products
	if (document.getElementById('group_' + group_id) && document.getElementById('group_' + group_id).options) {
		sbox = document.getElementById('group_' + group_id);
		s_selected = sbox.value;
		sbox.options.length = 0;
	}

	for (var restore_product in conf_prod[group_id]) {
		if (conf_prod[group_id][restore_product]['type'] == 'R') {
			if (document.getElementById('group_' + group_id + '_product_' + restore_product)) {
				if (conf_prod[group_id][restore_product]['amount'] <= 0 && hide_amount) {
					document.getElementById('group_' + group_id + '_product_' + restore_product).disabled = true;
				} else {
					document.getElementById('group_' + group_id + '_product_' + restore_product).disabled = false;
				}
			}
		} else if (conf_prod[group_id][restore_product]['type'] == 'C') {
			if (document.getElementById('group_' + group_id + '_product_' + restore_product)) {
				if (conf_prod[group_id][restore_product]['amount'] <= 0 && hide_amount) {
					document.getElementById('group_' + group_id + '_product_' + restore_product).disabled = true;
				} else {
					document.getElementById('group_' + group_id + '_product_' + restore_product).disabled = false;
				}
			}
		} else if (conf_prod[group_id][restore_product]['type'] == 'S') {
			
			sbox.options[i] = new Option(conf_prod[group_id][restore_product]['product_name'], restore_product);
			if (sbox.options[i].value == s_selected) {
				sbox.options[i].selected = true;
			}
			
			if (conf_prod[group_id][restore_product]['amount'] <= 0 && hide_amount) {
				sbox.options[i].disabled = true;
			}
			
			i++;
		}
	}

	check_hierarchy_compatibilities(compatible_classes, group_id, 0, {});
}


//
// This function make search through the groups and checks the compatibilities
//
function check_hierarchy_compatibilities(compatible_classes, group_id, digging, forbidden_groups)
{
	var set_next = 0;
	var s_selected = '';
	var selected_product = 0;
	var checked_products = {};
	var i = 0;
	var digg = []; // The groups that should be analysed for compatibilities if depth is grater 0

	// Check all other groups corresponding to the current
	forbidden_groups[group_id] = true; // The main group already checked, forbid it for futher analysis
	for(var check_group_id in conf_prod) {
		if (check_group_id != group_id && forbidden_groups[check_group_id] != true) {
			//If checked group is selectbox then delete all selectbox variants for showing only compatible variants
			if (document.getElementById('group_'+check_group_id) && document.getElementById('group_'+check_group_id).options) {
				sbox = document.getElementById('group_'+check_group_id);
				s_selected = sbox.value; // save currently selected value to select it after rebuiding options
				sbox.options.length = 0;
			}
			i = 0;
			for(var check_product_id in conf_prod[check_group_id]) {
				compatible[check_group_id] = 1;
				// If checking group is in comp classes and this product has any class and this class is not in compatible classes then this product is incompatible
				for (var check_class_id in compatible_classes) {
					if ((check_group_id == compatible_classes[check_class_id]) && conf_prod[check_group_id][check_product_id]['class_id'] && conf_prod[check_group_id][check_product_id]['class_id'] != '') {
						// If we are here than it means that this group has some compatibilites with main
						if (!forbidden_groups[check_group_id]) {
							digg.push(check_group_id); // We will analyse this group futher. It's compatibilities with others, except forbidden
							forbidden_groups[check_group_id] = true;
						}
						if (conf_prod[check_group_id][check_product_id]['class_id'] == check_class_id) {
							compatible[check_group_id] = 1;
							//break;
						} else {
							compatible[check_group_id] = 0;
							checked_products[check_product_id] = true;
						}
					}
				}

				// If current product is compatible then release it
				if (compatible[check_group_id] == 1 && !checked_products[check_product_id]) {
					if (conf_prod[check_group_id][check_product_id]['type'] == 'R') {
						if (document.getElementById('group_' + check_group_id + '_product_' + check_product_id)) {
							if (conf_prod[check_group_id][check_product_id]['amount'] <= 0 && hide_amount) {
								document.getElementById('group_' + check_group_id + '_product_' + check_product_id).disabled = true;
							} else {
								document.getElementById('group_' + check_group_id + '_product_' + check_product_id).disabled = false;
							}
						}
						// Set next variant if existing is disabled
						if (set_next == 1) {
							document.getElementById('group_' + check_group_id + '_product_' + check_product_id).checked = true;
							set_next = 0;
						}
					} else if (conf_prod[check_group_id][check_product_id]['type'] == 'C') {
						if (document.getElementById('group_' + check_group_id + '_product_' + check_product_id)) {
							if (conf_prod[check_group_id][check_product_id]['amount'] <= 0 && hide_amount) {
								document.getElementById('group_' + check_group_id + '_product_' + check_product_id).disabled = true;
							} else {
								document.getElementById('group_' + check_group_id + '_product_' + check_product_id).disabled = false;
							}
						}
					} else if (conf_prod[check_group_id][check_product_id]['type'] == 'S') {

						sbox.options[i] = new Option(conf_prod[check_group_id][check_product_id]['product_name'], check_product_id);
						if (conf_prod[check_group_id][check_product_id]['amount'] <= 0 && hide_amount) {
							sbox.options[i].disabled = true;
						}
						i++;
					}
				// if current product is incompatible then disable it
				} else {
					if (conf_prod[check_group_id][check_product_id]['type'] == 'R') {
						if (document.getElementById('group_' + check_group_id + '_product_'+check_product_id)) {
							document.getElementById('group_' + check_group_id + '_product_'+check_product_id).disabled = true;
							if (document.getElementById('group_' + check_group_id + '_product_'+check_product_id).checked == true) {
								document.getElementById('group_' + check_group_id + '_product_'+check_product_id).checked = false;
								set_next = 1;
							}
						}
					} else if (conf_prod[check_group_id][check_product_id]['type'] == 'C') {
						if (document.getElementById('group_' + check_group_id + '_product_' + check_product_id)) {
							document.getElementById('group_' + check_group_id + '_product_' + check_product_id).disabled = true;
							if (document.getElementById('group_' + check_group_id + '_product_'+ check_product_id).checked == true) {
								document.getElementById('group_' + check_group_id + '_product_'+ check_product_id).checked = false;
							}
						}
					}
				}
			}
			if (set_next == 1) {
				for(c_product_id in conf_prod[check_group_id]) {
					if (document.getElementById('group_'+check_group_id+'_product_'+c_product_id) && document.getElementById('group_'+check_group_id+'_product_'+c_product_id).disabled == false && set_next == 1) {
						document.getElementById('group_'+check_group_id+'_product_'+c_product_id).checked = true;
						set_next = 0;
					}
				}
			}
			if (document.getElementById('group_'+check_group_id) && document.getElementById('group_'+check_group_id).options) {
				sbox.value = s_selected;
			}
		}
	}

	if (digging < depth) {
		for (var low_level_group in digg) {
			var _product_id = fn_define_selected_product(current_step_id, digg[low_level_group]) || 0;

			if (_product_id && _product_id.indexOf(':') != -1) {
				var p_ids = _product_id.split(':');
				compatible_classes = fn_get_compatible_classes(p_ids, digg[low_level_group]);
				check_hierarchy_compatibilities(compatible_classes, digg[low_level_group], digging++, {});

			} else {
				compatible_classes = fn_get_compatible_classes([_product_id], digg[low_level_group]);
				check_hierarchy_compatibilities(compatible_classes, digg[low_level_group], digging++, forbidden_groups);
			}
		}
	}
	return true;
}

//
// This defines the selected product in the current group
//
function fn_define_selected_product(step_id, group_id)
{
	var selected_product = false;
	// Define which product is selected in the group
	if (document.getElementById('group_one_'+group_id)) { // This means that this group contains only one product and is should be selected
		selected_product = document.getElementById('group_one_'+group_id).value;
	} else if (conf[step_id][group_id]['type'] == 'S') {
		selected_product = document.getElementById('group_'+group_id).value;
	} else if (conf[step_id][group_id]['type'] == 'R') {
		var d_form = document.getElementById('group_'+group_id).getElementsByTagName("INPUT");
		for(var elem=0; elem < d_form.length; elem++) {
			if (d_form[elem].type == "radio" && d_form[elem].checked == true) {
				selected_product = d_form[elem].value;
			}
		}
	} else if (conf[step_id][group_id]['type'] == 'C') {
		var d_form = document.getElementById('group_'+group_id).getElementsByTagName("INPUT");
		for(var elem=0; elem < d_form.length; elem++) {
			if (d_form[elem].type == "checkbox" && d_form[elem].checked == true) {
				if (selected_product == false) {
					selected_product = ''
				}
				selected_product += d_form[elem].value + ':';
			}
		}
	}
	return selected_product;
}

//
// FIXME in next version
//
function fn_convert_conf_price(value)
{
	if (value == parseFloat(value)) {
		return parseFloat(value);
	}

	value = value.toString();

	decimals_count = currencies.secondary.decimals;
	decimals_separator = currencies.secondary.decimals_separator;

	if (decimals_count != '0') {

		decimal_position = value.lastIndexOf(decimals_separator);

		thousands = value.substr(0, decimal_position);
		decimals = value.substr(decimal_position + 1);

		thousands = thousands.replace(currencies.secondary.thousands_separator, '');

		return parseFloat(thousands + '.' + decimals);
	} else {
		thousands = value.replace(currencies.secondary.thousands_separator, '');

		return parseFloat(thousands);
	}
}

function fn_product_configurator_check_exceptions(data)
{
	if (typeof(conf_product_id) != 'undefined' && conf_product_id == data.id) {
		fn_update_conf_price(data.id);
	}
}

function fn_product_configurator_get_price_function(data)
{
	if (typeof(conf_product_id) != 'undefined' && conf_product_id == data.id) {
		data.func = fn_update_conf_price;
	}
}
