<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2004 Simbirsk Technologies Ltd. All rights reserved.    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/


//
// $Id: settings_dev.php 10229 2010-07-27 14:21:39Z 2tl $
//
if (!defined('AREA') ) { die('Access denied'); }

$settings_ids['start'] = '7000';
$settings_ids['end'] = '10000';

$section_id = empty($_REQUEST['section_id']) ? 'General' : $_REQUEST['section_id'];

fn_trusted_vars('update');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'update') {
		fn_trusted_vars('dev_tooltip');

		if (!empty($_REQUEST['update'])) {
			$old_settings = db_get_hash_array("SELECT ?:settings.option_id, ?:settings.value FROM ?:settings WHERE ?:settings.section_id = ?s", 'option_id', $section_id);
			db_query("UPDATE ?:settings SET value = '' WHERE option_type IN ('C', 'M', 'N', 'G') AND section_id = ?s", $section_id);

			fn_get_schema('settings', 'actions', 'php', false, true);

			foreach ($_REQUEST['update'] as $k => $v) {
				if (!empty($v) && is_array($v)) { // If type is multiple selectbox
					$v = implode('=Y&', $v) . '=Y';
				}

				if (isset($old_settings[$k]) && $old_settings[$k]['value'] != $v) {
					$func = 'fn_settings_actions_' . strtolower($section_id) . '_' . (!empty($old_settings[$k]['subsection_id']) ? $old_settings[$k]['subsection_id'] . '_' : '') . $old_settings[$k]['option_name'];

					if (function_exists($func)) {
						$func($v, $old_settings[$k]['value']);
					}
				}

				db_query("UPDATE ?:settings SET value = ?s WHERE option_id = ?i", $v, $k);
			}
		}

		// FOR DEVELOPMENT PURPOSES ONLY!!!

		// Update subsections positions
		if (isset($_REQUEST['subsection_pos']) && is_array($_REQUEST['subsection_pos'])) {
			foreach ($_REQUEST['subsection_pos'] as $k => $v) {
				db_query("UPDATE ?:settings_subsections SET position = ?i WHERE subsection_id = ?s AND section_id = ?s", $v, $k, $section_id);
			}
		}

		// Update option descriptions
		if (!empty($_REQUEST['dev_descr'])) {
			foreach ($_REQUEST['dev_descr'] as $k => $v) {
				$tooltip = !empty($_REQUEST['dev_tooltip']) && !empty($_REQUEST['dev_tooltip'][$k]) ? $_REQUEST['dev_tooltip'][$k] : '';
				if (!empty($v)) {
					db_query("UPDATE ?:settings_descriptions SET tooltip = ?s, description = ?s WHERE object_id = ?s AND object_type = 'O' AND lang_code = ?s", trim($tooltip), trim($v), $k, CART_LANGUAGE);
				} else {
					db_query("UPDATE ?:settings_descriptions SET tooltip = ?s WHERE object_id = ?s AND object_type = 'O' AND lang_code = ?s", trim($tooltip), $k, CART_LANGUAGE);
				}
			}
		}

		// Update option subsection
		if (!empty($_REQUEST['dev_subsection'])) {
			foreach ($_REQUEST['dev_subsection'] as $k => $v) {
				db_query("UPDATE ?:settings SET subsection_id = ?s WHERE option_id = ?i", $v, $k);
			}
		}

		// Update option variants
		if (!empty($_REQUEST['dev_option_variants'])) {
			foreach ($_REQUEST['dev_option_variants'] as $k => $v) {
				foreach ($v as $variant_id => $val) {
					if (@$val['delete'] === 'Y') {
						db_query("DELETE FROM ?:settings_variants WHERE variant_id = ?i", $variant_id);
						db_query("DELETE FROM ?:settings_descriptions WHERE object_id = ?i AND object_type = 'V'", $variant_id);
						continue;
					}

					db_query("UPDATE ?:settings_variants SET ?u WHERE variant_id = ?i", $val, $variant_id);

					db_query("DELETE FROM ?:settings_descriptions WHERE object_id = ?i AND object_type = 'V' AND lang_code = ?s", $variant_id, CART_LANGUAGE);

					db_query("INSERT INTO ?:settings_descriptions (object_id, description, object_type, lang_code) VALUES (?i, ?s, 'V', ?s)", $variant_id, $val['vdesc'], CART_LANGUAGE);
				}
			}

			foreach ($_REQUEST['dev_add_option_variants'] as $k => $v) {
				if (!fn_is_empty($v)) {
					foreach ($v as $kk => $val) {
						$variant_id = db_get_field("SELECT max(variant_id) FROM ?:settings_variants WHERE variant_id >= ?i AND variant_id <= ?i", $settings_ids['start'], $settings_ids['end']);
						if (empty($variant_id)) {
							$variant_id = $settings_ids['start'];
						}
						$variant_id++;

						db_query("INSERT INTO ?:settings_variants (variant_id, option_id, variant_name, position) values (?i, ?i, ?s, ?i)", $variant_id, $k, $val['vname'], $val['vpos']);

						if (!empty($val['vdesc'])) {
							foreach ((array)Registry::get('languages') as $lc => $_v) {
								db_query("INSERT INTO ?:settings_descriptions (object_id, description, object_type, lang_code) VALUES (?i, ?s, 'V', ?s)", $variant_id, $val['vdesc'], $lc);
							}
						}

						$pos += 10;
					}
				}
			}
		}

		// Update option scope
		if (!empty($_REQUEST['is_global'])) {
			foreach ($_REQUEST['is_global'] as $k => $v) {
				db_query("UPDATE ?:settings SET is_global = ?s WHERE option_id = ?i", $v, $k);
			}
		}

		// Update option name
		if (!empty($_REQUEST['dev_option_name'])) {
			foreach ($_REQUEST['dev_option_name'] as $k => $v) {
				if (!empty($v)) {
					db_query("UPDATE ?:settings SET option_name = ?s WHERE option_id = ?i", strtolower(strtr(trim($v), array('-'=>'_',' '=>'_'))), $k);
				}
			}
		}
		// Update option position
		if (!empty($_REQUEST['position'])) {
			foreach ($_REQUEST['position'] as $k => $v) {
				db_query("UPDATE ?:settings SET position = ?i WHERE option_id = ?i AND section_id = ?s", $v, $k, $section_id);
			}
		}

		// Update option types
		if (!empty($_REQUEST['dev_option_type'])) {
			foreach ($_REQUEST['dev_option_type'] as $k => $v) {
				db_query("UPDATE ?:settings SET option_type = ?s WHERE option_id = ?i", $v, $k);
			}
		}

		// Delete selected options
		if (isset($_REQUEST['delete'])) {
			foreach ($_REQUEST['delete'] as $k => $v) {
				db_query("DELETE FROM ?:settings WHERE option_id = ?i", $k);
				db_query("DELETE FROM ?:settings_descriptions WHERE object_id = ?i AND object_type = 'O'", $k);

				$_opts = db_get_array("SELECT * FROM ?:settings_variants WHERE option_id = ?i", $k);
				foreach ($_opts as $_o => $_v) {
					db_query("DELETE FROM ?:settings_descriptions WHERE object_id = ?i AND object_type = 'V'", $_v['variant_id']);
				}
				db_query("DELETE FROM ?:settings_variants WHERE option_id = ?i", $k);
			}
		}

		// Update element position
		if (!empty($_REQUEST['elms_data'])) {
			foreach ($_REQUEST['elms_data'] as $elm_id => $v) {
				db_query("UPDATE ?:settings_elements SET ?u WHERE element_id = ?i", $v, $elm_id);
			}
		}

		// Update element description
		if (!empty($_REQUEST['descr_elm'])) {
			foreach ($_REQUEST['descr_elm'] as $k => $v) {
				db_query("UPDATE ?:settings_descriptions SET description = ?s WHERE object_id = ?s AND object_type = 'H' AND lang_code = ?s", $v, $k, DESCR_SL);
			}
		}

		// Delete element
		if (isset($_REQUEST['delete_elm']) && is_array($_REQUEST['delete_elm'])) {
			foreach ($_REQUEST['delete_elm'] as $k => $v) {
				db_query("DELETE FROM ?:settings_elements WHERE element_id = ?i", $k);
				db_query("DELETE FROM ?:settings_descriptions WHERE object_id = ?i AND object_type = 'H'", $k);
			}
		}
	}

	// ADD new section
	if ($mode == 'dev_add_section') {
		$dev = $_REQUEST['dev'];

		if (!empty($dev['section_id'])) {
			//$dev['section_id'] = ucfirst(strtolower(strtr(trim($dev['section_id']), array('-'=>'_',' '=>'_'))));
			$dev['section_id'] = (strtr(trim($dev['section_id']), array('-'=>'_',' '=>'_')));
			db_query("REPLACE INTO ?:settings_sections ?e", $dev);

			$dev['object_string_id'] = $dev['section_id'];
			$dev['object_type'] = 'S';
			foreach ((array)Registry::get('languages') as $dev['lang_code'] => $_v) {
				db_query("REPLACE INTO ?:settings_descriptions ?e", $dev);
			}
		}
	}

	// ADD new subsection
	if ($mode == 'dev_add_subsection') {
		$dev = $_REQUEST['dev'];

		if (!empty($dev['subsection_id'])) {
			$dev['subsection_id'] = strtolower(strtr(trim($dev['subsection_id']), array('-'=>'_',' '=>'_')));
			db_query("REPLACE INTO ?:settings_subsections ?e", $dev);

			$dev['object_string_id'] = $dev['subsection_id'];
			$dev['object_type'] = 'U';
			foreach ((array)Registry::get('languages') as $dev['lang_code'] => $_v) {
				db_query("INSERT INTO ?:settings_descriptions ?e", $dev);
			}
		}
	}

	// ADD new element
	if ($mode == 'dev_add_element') {
			$dev = $_REQUEST['dev'];

			$element_id = db_get_field("SELECT MAX(element_id) FROM ?:settings_elements WHERE element_id >= ?i AND element_id <= ?i", $settings_ids['start'], $settings_ids['end']);
			if (empty($element_id)) {
				$element_id = $settings_ids['start'];
			}
			$element_id++;
			$dev['element_id'] = $element_id;

			db_query("INSERT INTO ?:settings_elements ?e", $dev);

			if ($dev['element_type'] == 'H') {
				$dev['object_id'] = $element_id;
				$dev['object_type'] = $dev['element_type'];
				foreach ((array)Registry::get('languages') as $dev['lang_code'] => $_v) {
					db_query("INSERT INTO ?:settings_descriptions ?e", $dev);
				}
			}
	}

	// ADD new option
	if ($mode == 'dev_add_option') {
		$dev = $_REQUEST['dev'];

		$dev['option_name'] = strtolower(strtr(trim($dev['option_name']), array('-'=>'_',' '=>'_')));
		// Get next option_id number
		$option_id = db_get_field("SELECT MAX(option_id) FROM ?:settings WHERE option_id >= ?i AND option_id <= ?i", $settings_ids['start'], $settings_ids['end']);
		if (empty($option_id)) {
			$option_id = $settings_ids['start'];
		}
		$option_id++;
		$dev['option_id'] = $option_id;

		db_query('INSERT INTO ?:settings ?e', $dev);

		if (!empty($dev['section_id'])) {
			$dev['object_id'] = $option_id;
			$dev['object_type'] = 'O';
			foreach ((array)Registry::get('languages') as $dev['lang_code'] => $_v) {
				db_query("INSERT INTO ?:settings_descriptions ?e", $dev);
			}
		}

		if (!fn_is_empty(@$_REQUEST['dev_add_ovars']) && strstr('SRMN', $dev['option_type'])) {
			foreach ($_REQUEST['dev_add_ovars'] as $kk => $val) {
				$variant_id = db_get_field("SELECT MAX(variant_id) FROM ?:settings_variants WHERE variant_id >= ?i AND variant_id <= ?i", $settings_ids['start'], $settings_ids['end']);
				if (empty($variant_id)) {
					$variant_id = $settings_ids['start'];
				}
				$variant_id++;

				db_query("INSERT INTO ?:settings_variants (variant_id, option_id, variant_name, position) values (?i, ?i, ?s, ?i)", $variant_id,  $option_id, $val['vname'], $val['vpos']);

				if (!empty($val['vdesc'])) {
					foreach ((array)Registry::get('languages') as $lc => $_v) {
						db_query("INSERT INTO ?:settings_descriptions (object_id, description, object_type, lang_code) VALUES (?i, ?s, 'V', ?s)", $variant_id, $val['vdesc'], $lc);
					}
				}
			}
		}
	}

	if ((!empty($_REQUEST['set_global_section'])) && $_REQUEST['set_global_section'] == 'Y') {
		db_query("UPDATE ?:settings SET is_global = 'Y' WHERE section_id = ?s", $section_id);
	}
	if ((!empty($_REQUEST['rem_global_section'])) && $_REQUEST['rem_global_section'] == 'Y') {
		db_query("UPDATE ?:settings SET is_global = 'N' WHERE section_id = ?s", $section_id);
	}

	// \FOR DEVELOPMENT PURPOSES ONLY!!!
	return array(CONTROLLER_STATUS_OK, "settings_dev.manage?section_id=$section_id");
}


if ($mode == 'manage') {
	//
	// OUPUT routines
	//
	$descr = fn_settings_descr_query('section_id', 'S', CART_LANGUAGE, 'settings_sections', 'object_string_id');
	$sections = db_get_hash_array("SELECT ?:settings_sections.*, ?:settings_descriptions.description FROM ?:settings_sections $descr ORDER BY ?:settings_descriptions.description", 'section_id');

	$descr = fn_settings_descr_query('subsection_id', 'U', CART_LANGUAGE, 'settings_subsections', 'object_string_id');
	$subsections = db_get_hash_array("SELECT ?:settings_subsections.*, ?:settings_descriptions.description FROM ?:settings_subsections $descr WHERE ?:settings_subsections.section_id = ?s ORDER BY ?:settings_subsections.position", 'subsection_id', $section_id);

	$descr = fn_settings_descr_query('option_id', 'O', CART_LANGUAGE, 'settings');

	$options = db_get_hash_multi_array("SELECT ?:settings.*, ?:settings_descriptions.description, ?:settings_descriptions.tooltip FROM ?:settings $descr WHERE ?:settings.section_id = ?s ORDER BY ?:settings.position", array('subsection_id'), $section_id);

	$options['main'] = $options[''];
	unset($options['']);
	$descr = fn_settings_descr_query('variant_id', 'V', CART_LANGUAGE, 'settings_variants');

	fn_get_schema('settings', 'variants', 'php', false, true);
	fn_get_schema('settings', 'actions', 'php', false, true);

	foreach ($options as $sid => $sct) {
		$ssid = ($sid == 'main') ? '' : $sid;

		$elements = db_get_array("SELECT ?:settings_elements.*, ?:settings_descriptions.description FROM ?:settings_elements LEFT JOIN ?:settings_descriptions ON ?:settings_elements.element_id = ?:settings_descriptions.object_id AND ?:settings_descriptions.object_type = 'H' AND ?:settings_descriptions.lang_code = ?s WHERE ?:settings_elements.section_id = ?s AND ?:settings_elements.subsection_id = ?s ORDER BY ?:settings_elements.position", CART_LANGUAGE, $section_id, $ssid);

		foreach ($elements as $k => $v) {
			if (!empty($v['handler']) && $v['element_type'] == 'I') {
				$args = explode(',', $v['handler']);
				$func = array_shift($args);
				$elements[$k]['info'] = function_exists($func) ? call_user_func_array($func, $args) : 'Function not exists';
			}
		}

		foreach ($sct as $k => $v) {

			// Get variants list
			$func = 'fn_settings_variants_' . strtolower($v['section_id']) . '_' . (!empty($v['subsection_id']) ? $v['subsection_id'] . '_' : '') . $v['option_name'];

			if (function_exists($func)) {
				$options[$sid][$k]['variants'] = $func();
				$options[$sid][$k]['userfunc'] = true;

			} elseif (strstr('SRMN', $v['option_type'])) {
				$options[$sid][$k]['variants'] = db_get_hash_single_array("SELECT ?:settings_variants.*, ?:settings_descriptions.description FROM ?:settings_variants ?p WHERE  ?:settings_variants.option_id = ?i ORDER BY ?:settings_variants.position", array('variant_name', 'description'), $descr, $v['option_id']);

				$options[$sid][$k]['dev_variants'] = db_get_hash_array("SELECT ?:settings_variants.*, ?:settings_descriptions.description FROM ?:settings_variants ?p WHERE  ?:settings_variants.option_id = ?i ORDER BY ?:settings_variants.position", 'variant_name', $descr, $v['option_id']);
			}

			if ($v['option_type'] == 'M' || $v['option_type'] == 'N' || $v['option_type'] == 'G') {
				parse_str($v['value'], $options[$sid][$k]['value']);
			}
		}
		
		$options[$sid] = fn_array_merge($options[$sid], $elements, false);
		$options[$sid] = fn_sort_array_by_key($options[$sid], 'position');
	}

	// [Page sections]
	if (isset($options['main'])) {
		Registry::set('navigation.tabs.main', array (
			'title' => fn_get_lang_var('main'),
			'js' => true
		));

	}
	foreach ($subsections as $k => $v) {
		Registry::set('navigation.tabs.' . $k, array (
			'title' => $v['description'],
			'js' => true
		));


	}

	$view->assign('notabs', ((isset($options['main']) && sizeof($subsections) < 1) || (!isset($options['main']) && sizeof($subsections) <= 1)) ? 'Y' : 'N');
	// [/Page sections]


	$section_columns = 3; // number of columns with sections links (in the frontend)
	$section_cols = fn_split($sections, $section_columns);

	$view->assign('options', @$options);
	$view->assign('sections', @$sections);
	$view->assign('section_cols', @$section_cols);
	$view->assign('subsections', @$subsections);
	$view->assign('section_id', $section_id);
}


//-----------------------------------------------------------------------
//
// Settings related functions
//

// Return part of SQL query to get object description from settings_descriptions table;
function fn_settings_descr_query($object_id, $object_type, $lang_code = CART_LANGUAGE, $table, $oid_name = 'object_id')
{
	return db_quote(" LEFT JOIN ?:settings_descriptions ON ?:$table.$object_id = ?:settings_descriptions.$oid_name AND ?:settings_descriptions.object_type = ?s AND ?:settings_descriptions.lang_code = ?s", $object_type, $lang_code);

}

// Fake function to test "Variants function" option field
function fn_fake()
{
	return array('a'=>'B','c'=>'D', 'e'=>'a');
}

?>