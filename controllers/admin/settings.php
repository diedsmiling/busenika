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
// $Id: settings.php 10229 2010-07-27 14:21:39Z 2tl $
//
if (!defined('AREA') ) { die('Access denied'); }

$section_id = empty($_REQUEST['section_id']) ? 'General' : $_REQUEST['section_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	fn_trusted_vars('update');
	$_suffix = '';

	if ($mode == 'update') {
		if (is_array($_REQUEST['update'])) {
			$old_settings = db_get_hash_array("SELECT ?:settings.option_name, ?:settings.subsection_id, ?:settings.option_id, ?:settings.value FROM ?:settings WHERE ?:settings.section_id = ?s", 'option_id', $section_id);
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
		$_suffix = ".manage";
	}

	return array(CONTROLLER_STATUS_OK, "settings{$_suffix}?section_id=$section_id");
}

//
// OUPUT routines
//
if ($mode == 'manage') {
	$descr = fn_settings_descr_query('subsection_id', 'U', CART_LANGUAGE, 'settings_subsections', 'object_string_id');
	$subsections = db_get_hash_array("SELECT ?:settings_subsections.*, ?:settings_descriptions.description, ?:settings_descriptions.object_string_id, ?:settings_descriptions.object_type FROM ?:settings_subsections ?p WHERE ?:settings_subsections.section_id = ?s ORDER BY  ?:settings_descriptions.description", 'subsection_id', $descr, $section_id);

	$descr = fn_settings_descr_query('option_id', 'O', CART_LANGUAGE, 'settings');
	$options = db_get_hash_multi_array("SELECT ?:settings.*, IF(?:settings.subsection_id = '', 'main', ?:settings.subsection_id) as subsection_id, ?:settings_descriptions.description, ?:settings_descriptions.tooltip, ?:settings_descriptions.object_type FROM ?:settings ?p WHERE ?:settings.section_id = ?s ORDER BY ?:settings_descriptions.description", array('subsection_id'), $descr, $section_id);

	$descr = fn_settings_descr_query('variant_id', 'V', CART_LANGUAGE, 'settings_variants');

	fn_get_schema('settings', 'variants', 'php', false, true);

	foreach ($options as $sid => $sct) {
		$ssid = ($sid == 'main') ? '' : $sid;

		$elements = db_get_array("SELECT ?:settings_elements.*, ?:settings_descriptions.description FROM ?:settings_elements LEFT JOIN ?:settings_descriptions ON ?:settings_elements.element_id = ?:settings_descriptions.object_id AND ?:settings_descriptions.object_type = 'H' AND ?:settings_descriptions.lang_code = ?s WHERE ?:settings_elements.section_id = ?s AND ?:settings_elements.subsection_id = ?s ORDER BY ?:settings_elements.position", CART_LANGUAGE, $section_id, $ssid);
		foreach ($elements as $k => $v) {
			if (!empty($v['handler']) && $v['element_type'] == 'I') {
				$args = explode(',', $v['handler']);
				$func = array_shift($args);
				if (function_exists($func)) {
					$elements[$k]['info'] = call_user_func_array($func, $args);
				} else {
					$elements[$k]['info'] = "No function: $func";
				}

			}
		}

		foreach ($sct as $k => $v) {

			// Check if option has variants function
			$func = 'fn_settings_variants_' . strtolower($v['section_id']) . '_' . ($v['subsection_id'] != 'main' ? $v['subsection_id'] . '_' : '') . $v['option_name'];
			if (function_exists($func)) {
				$options[$sid][$k]['variants'] = $func();
				$options[$sid][$k]['userfunc'] = true;

			} elseif (strstr('SRMN', $v['option_type'])) {
				if (defined('TRANSLATION_MODE')) {
					$variants_array = db_get_array("SELECT ?:settings_variants.*, ?:settings_descriptions.description, ?:settings_descriptions.object_type FROM ?:settings_variants ?p WHERE  ?:settings_variants.option_id = ?i ORDER BY ?:settings_variants.position", $descr, $v['option_id']);
					fn_update_lang_objects('variants', $variants_array);
					$variants = array();
					foreach ($variants_array as $val) {
						$variants[$val['variant_name']] = $val['description'];
					}
				} else {
					$variants = db_get_hash_single_array("SELECT ?:settings_variants.*, ?:settings_descriptions.description FROM ?:settings_variants ?p WHERE  ?:settings_variants.option_id = ?i ORDER BY ?:settings_variants.position", array('variant_name', 'description'), $descr, $v['option_id']);
				}
				$options[$sid][$k]['variants'] = $variants;
			}

			if ($v['option_type'] == 'M' || $v['option_type'] == 'N' || $v['option_type'] == 'G') {
				parse_str($v['value'], $options[$sid][$k]['value']);
			}
		}

		$options[$sid] = fn_array_merge($options[$sid], $elements, false);
		$options[$sid] = fn_sort_array_by_key($options[$sid], 'position');
	}

	fn_update_lang_objects('subsections', $subsections);

	// [Page sections]
	if (!empty($subsections)) {
		Registry::set('navigation.tabs.main', array (
			'title' => fn_get_lang_var('main'),
			'js' => true
		));
		foreach ($subsections as $k => $v) {
			Registry::set('navigation.tabs.' . $k, array (
				'title' => $v['description'],
				'js' => true
			));
		}
	}
	// [/Page sections]


	// Set navigation menu
	$descr = fn_settings_descr_query('section_id', 'S', CART_LANGUAGE, 'settings_sections', 'object_string_id');
	$sections = db_get_hash_array("SELECT ?:settings_sections.section_id, ?:settings_descriptions.description as title, CONCAT(?s, ?:settings_sections.section_id) as href, ?:settings_descriptions.object_type FROM ?:settings_sections ?p ORDER BY ?:settings_descriptions.description", 'section_id', "settings.manage?section_id=", $descr);
	fn_update_lang_objects('sections', $sections);
	Registry::set('navigation.dynamic.sections', $sections);
	Registry::set('navigation.dynamic.active_section', $section_id);

	$view->assign('options', $options);
	$view->assign('subsections', $subsections);
	$view->assign('section_id', $section_id);
	$view->assign('settings_title', $sections[$section_id]['title']);
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

?>