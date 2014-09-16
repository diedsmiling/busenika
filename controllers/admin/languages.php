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
// $Id: languages.php 10521 2010-08-26 08:19:46Z andyye $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	fn_trusted_vars("lang_data", "new_lang_data");

	//
	// Edit language variables
	//
	if ($mode == 'update_variables') {

		if (is_array($_REQUEST['lang_data'])) {
			$error_flag = false;
			foreach ($_REQUEST['lang_data'] as $k => $v) {
				if (!empty($v['name'])) {
					preg_match("/(^[a-zA-z0-9][a-zA-Z0-9_]*)/", $v['name'], $matches);
					if (@strlen($matches[0])==strlen($v['name'])) {
						$v['lang_code'] = DESCR_SL;
						db_query("REPLACE INTO ?:language_values ?e", $v);
					} elseif ($error_flag == false) {
						fn_set_notification('E', fn_get_lang_var('warning'), fn_get_lang_var('warning_lanvar_incorrect_name'));
						$error_flag = true;
					}
				}
			}
		}
	}

	//
	// Edit language variables
	//
	if ($mode == 'delete_variables') {
		if (!empty($_REQUEST['names'])) {
			db_query("DELETE FROM ?:language_values WHERE name IN (?a)", $_REQUEST['names']);
		}
	}

	//
	// Add new language variable
	// NOTE: variable will be added for all defined languages
	//
	if ($mode == 'add_variables') {
		$error_flag = false;

		if (!empty($_REQUEST['new_lang_data'])) {
			foreach ((array)Registry::get('languages') as $lc => $_v) {
				foreach ($_REQUEST['new_lang_data'] as $k1 => $v1) {
					if (!empty($v1['name'])) {
						preg_match("/(^[a-zA-z0-9][a-zA-Z0-9_]*)/", $v1['name'], $matches);
						if (strlen($matches[0]) == strlen($v1['name'])) {
							$v1['lang_code'] = $lc;
							db_query("REPLACE INTO ?:language_values ?e", $v1);
						} elseif ($error_flag == false) {
							fn_set_notification('E', fn_get_lang_var('warning'), fn_get_lang_var('warning_lanvar_incorrect_name'));
							$error_flag = true;
						}
					}
				}
			}
		}
	}
	//
	// Update languages
	//
	if ($mode == 'update_languages') {

		if (is_array($_REQUEST['update_language'])) {
			foreach ($_REQUEST['update_language'] as $__lang_code => $__data) {
				db_query("UPDATE ?:languages SET ?u WHERE lang_code = ?s", $__data, $__lang_code);
			}
			fn_check_languages_availability();
		}
	}

	//
	// Delete languages
	//
	if ($mode == 'delete_languages') {

		if (!empty($_REQUEST['lang_codes'])) {
			fn_delete_languages($_REQUEST['lang_codes']);
		}
	}

	//
	// Add languages
	//
	if ($mode == 'add_languages') {
		$new_language = $_REQUEST['new_language'];
		if (!empty($new_language['lang_code']) && !empty($new_language['name'])) {
			$is_exists = db_get_field("SELECT COUNT(*) FROM ?:languages WHERE lang_code = ?s", $new_language['lang_code']);
			if (empty($is_exists)) {
				db_query("INSERT INTO ?:languages ?e", $new_language);
				// Adding new language descriptions for all objects

				$db_descr_tables = db_get_fields("SHOW TABLES LIKE '%_descriptions'");
				$db_descr_tables[] = 'language_values';
				$db_descr_tables[] = 'product_features_values';

				foreach ($db_descr_tables as $table) {
					$table = str_replace(TABLE_PREFIX, '', $table);
					$fields_insert = $fields_select = fn_get_table_fields($table, array(), true);
					$k = array_search('`lang_code`', $fields_select);
					$fields_select[$k] = db_quote("?s as lang_code", $new_language['lang_code']);
					db_query("REPLACE INTO ?:$table (" . implode(', ', $fields_insert) . ") SELECT " . implode(', ', $fields_select) . " FROM ?:$table WHERE lang_code = 'EN'");
				}
			} else {
				fn_set_notification('E', fn_get_lang_var('error'), str_replace('[code]', $new_language['lang_code'], fn_get_lang_var('error_lang_code_exists')));
			}
		}
	}
	$q = (empty($_REQUEST['q'])) ? '' : $_REQUEST['q'];

	return array(CONTROLLER_STATUS_OK, "languages.manage?q=$q");
}

//
// Get language variables values
//
if ($mode == 'manage') {
	if (isset($_REQUEST['q']) && fn_string_no_empty($_REQUEST['q'])) {
		$_query = db_quote(" lang_code = ?s AND (name LIKE ?l OR value LIKE ?l)", DESCR_SL, "%".trim($_REQUEST['q'])."%", "%".trim($_REQUEST['q'])."%");
	} else {
		$_query = db_quote(" lang_code = ?s", DESCR_SL);
	}

	$page = empty($_REQUEST['page']) ? 1 : $_REQUEST['page'];

	$lang_data_count = db_get_field("SELECT COUNT(*) FROM ?:language_values WHERE $_query");
	$limit = fn_paginate($page, $lang_data_count, Registry::get('settings.Appearance.admin_elements_per_page'));
	$lang_data = db_get_array("SELECT name, value FROM ?:language_values WHERE $_query ORDER BY name $limit");
	Registry::set('navigation.tabs', array (
		'translations' => array (
			'title' => fn_get_lang_var('translations'),
			'js' => true
		),
		'languages' => array (
			'title' => fn_get_lang_var('languages'),
			'js' => true
		),
	));
	$view->assign('lang_data', $lang_data);
	$view->assign('langs', Registry::get('languages'));

} elseif ($mode == 'delete_variable') {
	if (!empty($_REQUEST['name'])) {
		db_query("DELETE FROM ?:language_values WHERE name = ?s", $_REQUEST['name']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "languages.manage");

//
// Delete languages
//
} elseif ($mode == 'delete_language') {

	if (!empty($_REQUEST['lang_code'])) {
		fn_delete_languages($_REQUEST['lang_code']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "languages.manage?selected_section=languages");
}

function fn_delete_languages($lang_codes)
{
	$db_descr_tables = db_get_fields("SHOW TABLES LIKE '%_descriptions'");
	$db_descr_tables[] = '?:language_values';
	$db_descr_tables[] = '?:product_features_values';

	foreach ((array)$lang_codes as $v) {
		db_query("DELETE FROM ?:languages WHERE lang_code = ?s", $v);
			db_query("DELETE FROM ?:localization_elements WHERE element_type = 'L' AND element = ?s", $v);
			foreach ($db_descr_tables as $table) {
			db_query("DELETE FROM $table WHERE lang_code = ?s", $v);
		}
	}
	fn_check_languages_availability();
}

function fn_check_languages_availability()
{
	$avail = db_get_field("SELECT COUNT(*) FROM ?:languages WHERE status = 'A'");
	if (empty($avail)) {
		db_query("UPDATE ?:languages SET status = 'A' WHERE lang_code = 'EN'");
	}

	$first_avail_code = db_get_field("SELECT lang_code FROM ?:languages WHERE status = 'A' LIMIT 1");

	$is_customer_lang_avail = db_get_field("SELECT COUNT(*) FROM ?:languages WHERE lang_code = ?s AND status = 'A'", Registry::get('settings.Appearance.customer_default_language'));

	// Set default language for customer zone
	if (empty($is_customer_lang_avail)) {
		db_query("UPDATE ?:settings SET value = ?s WHERE option_name = 'customer_default_language' AND section_id = 'Appearance'", $first_avail_code);
	}

	$is_admin_lang_avail = db_get_field("SELECT COUNT(*) FROM ?:languages WHERE lang_code = ?s AND status = 'A'", Registry::get('settings.Appearance.admin_default_language'));
	// Set default language for admin zone
	if (empty($is_admin_lang_avail)) {
		db_query("UPDATE ?:settings SET value = ?s WHERE option_name = 'admin_default_language' AND section_id = 'Appearance'", $first_avail_code);
	}

	if (empty($is_customer_lang_avail) || empty($is_admin_lang_avail)) {
		$href = fn_url('settings.manage?section_id=Appearance');
		fn_set_notification('W', fn_get_lang_var('warning'), str_replace('[link]', "<a href='$href'>Settings::Appearance</a>", fn_get_lang_var('warning_default_language_disabled')));
	}
}

?>