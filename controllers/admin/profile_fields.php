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
// $Id: profile_fields.php 10229 2010-07-27 14:21:39Z 2tl $
//
if (!defined('AREA') ) { die('Access denied'); }

// ----------
// fields types:
// I - input
// T - textarea
// C - checkbox
// S - selectbox
// R - radiobutton
// H - header
// D - data
// P - phone
// N - number
// --
// L - titles
// A - states
// O - country
// M - usergroup
// W - password

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$suffix = '';

	if ($mode == 'add') {
		if (!empty($_REQUEST['add_fields_data'])) {
			fn_add_profile_fields($_REQUEST['add_fields_data']);
		}
	}

	if ($mode == 'update') {
		if (!empty($_REQUEST['fields_data'])) {
			foreach ($_REQUEST['fields_data'] as $field_id => $data) {

				if (!empty($_REQUEST['matches'][$field_id])) {
					$data['field_type'] = $_REQUEST['fields_data'][$_REQUEST['matches'][$field_id]]['field_type'];
				}

				$_data = fn_check_table_fields($data, 'profile_fields');
				db_query("UPDATE ?:profile_fields SET ?u WHERE field_id = ?i", $_data, $field_id);

				$_data = array (
					'object_id' => $field_id,
					'object_type' => 'F',
					'description' => $data['description'],
					'lang_code' => DESCR_SL
				);
				db_query("REPLACE INTO ?:profile_field_descriptions ?e", $_data);


				if (strpos('SR', $data['field_type']) !== false) {
					if (!empty($data['values'])) {
						foreach ($data['values'] as $value_id => $vdata) {
							$_data = fn_check_table_fields($vdata, 'profile_field_values');
							db_query("UPDATE ?:profile_field_values SET ?u WHERE value_id = ?i", $_data, $value_id);

							$_data = array (
								'object_id' => $value_id,
								'object_type' => 'V',
								'description' => $vdata['description'],
								'lang_code' => DESCR_SL
							);
							db_query("REPLACE INTO ?:profile_field_descriptions ?e", $_data);
						}
					}

					if (!empty($data['add_values']) && is_array($data['add_values'])) {
						fn_add_field_values($data['add_values'], $field_id);
					}
				} else {
					fn_delete_field_values($field_id);
				}
			}
		}
	}

	if ($mode == 'delete') {
		if (!empty($_REQUEST['field_ids'])) {
			foreach ($_REQUEST['field_ids'] as $field_id) {
				fn_delete_profile_field($field_id);
			}
		}

		if (!empty($_REQUEST['value_ids'])) {
			foreach ($_REQUEST['value_ids'] as $value_id) {
				db_query("DELETE FROM ?:profile_field_descriptions WHERE object_id = ?i AND object_type = 'V'", $value_id);
				db_query("DELETE FROM ?:profile_field_values WHERE value_id = ?i", $value_id);
			}
		}
	}

	return array(CONTROLLER_STATUS_OK, "profile_fields.manage");
}


if ($mode == 'manage') {

	$profile_fields = db_get_hash_multi_array("SELECT ?:profile_fields.*, IF(?:profile_fields.section = 'C', 1, IF(?:profile_fields.section = 'B', 2, 3)) as sec, ?:profile_field_descriptions.description FROM ?:profile_fields LEFT JOIN ?:profile_field_descriptions ON ?:profile_field_descriptions.object_id = ?:profile_fields.field_id AND ?:profile_field_descriptions.object_type = 'F' AND ?:profile_field_descriptions.lang_code = ?s ORDER BY sec, ?:profile_fields.position", array('section', 'field_id'), DESCR_SL);

	if (!empty($profile_fields)) {
		foreach ($profile_fields as $section => $fields) {
			foreach ($fields as $k => $v) {
				if ($v['field_type'] == 'S' || $v['field_type'] == 'R') {
					$profile_fields[$section][$k]['values'] = db_get_array("SELECT ?:profile_field_values.*, ?:profile_field_descriptions.description FROM ?:profile_field_values LEFT JOIN ?:profile_field_descriptions ON ?:profile_field_descriptions.object_id = ?:profile_field_values.value_id AND ?:profile_field_descriptions.object_type = 'V' AND ?:profile_field_descriptions.lang_code = ?s WHERE ?:profile_field_values.field_id = ?i ORDER BY ?:profile_field_values.position", DESCR_SL, $v['field_id']);
				}
			}
		}
	}

	$view->assign('profile_fields_areas', fn_profile_fields_areas());
	$view->assign('profile_fields', $profile_fields);

} elseif ($mode == 'delete') {
	if (!empty($_REQUEST['field_id'])) {
		fn_delete_profile_field($_REQUEST['field_id']);
	}

	if (!empty($_REQUEST['value_id'])) {
		db_query("DELETE FROM ?:profile_field_descriptions WHERE object_id = ?i AND object_type = 'V'", $_REQUEST['value_id']);
		db_query("DELETE FROM ?:profile_field_values WHERE value_id = ?i", $_REQUEST['value_id']);
	}
	if (defined('AJAX_REQUEST')) {
		exit;
	}

	return array(CONTROLLER_STATUS_REDIRECT, "profile_fields.manage");
}
// -------------- Functions ----------------
function fn_add_profile_fields($fields)
{
	if (empty($fields)) {
		return false;
	}

	foreach ($fields as $v) {
		$add_match = false;
		if (empty($v['description'])) {
			continue;
		}

		if ($v['section'] == 'BS') {
			$v['section'] = 'B';
			$add_match = true;
		}

		// Insert main data
		$_data = fn_check_table_fields($v, 'profile_fields');

		$field_id = db_query("INSERT INTO ?:profile_fields ?e", $_data);

		// Insert descriptions
		$_data = array (
			'object_id' => $field_id,
			'object_type' => 'F',
			'description' => $v['description'],
		);

		foreach ((array)Registry::get('languages') as $_data['lang_code'] => $_v) {
			db_query("INSERT INTO ?:profile_field_descriptions ?e", $_data);
		}
		if (substr_count('SR', $v['field_type']) && is_array($v['values']) && $add_match == false) {
			fn_add_field_values($v['values'], $field_id);
		}

		if ($add_match == true) {
			$v['section'] = 'S';
			$v['matching_id'] = $field_id;
			fn_add_profile_fields(array($v));
		}
	}
}

function fn_add_field_values($values = array(), $field_id = 0)
{
	if (empty($values) || empty($field_id)) {
		return false;
	}

	foreach ($values as $_v) {

		if (empty($_v['description'])) {
			continue;
		}
		// Insert main data
		$_data = fn_check_table_fields($_v, 'profile_field_values');
		$_data['field_id'] = $field_id;
		$value_id = db_query("INSERT INTO ?:profile_field_values ?e", $_data);

		// Insert descriptions
		$_data = array (
			'object_id' => $value_id,
			'object_type' => 'V',
			'description' => $_v['description'],
		);

		foreach ((array)Registry::get('languages') as $_data['lang_code'] => $_v) {
			db_query("INSERT INTO ?:profile_field_descriptions ?e", $_data);
		}
	}

	return true;
}

function fn_delete_field_values($field_id)
{

	$vars = db_get_fields("SELECT value_id FROM ?:profile_field_values WHERE field_id = ?i", $field_id);
	if (!empty($vars)) {
		foreach ($vars as $value_id) {
			db_query("DELETE FROM ?:profile_field_descriptions WHERE object_id = ?i AND object_type = 'V'", $value_id);
		}
		db_query("DELETE FROM ?:profile_field_values WHERE field_id = ?i", $field_id);
	}
}

function fn_delete_profile_field($field_id)
{

	$matching_id = db_get_field("SELECT matching_id FROM ?:profile_fields WHERE field_id = ?i", $field_id);
	if (!empty($matching_id)) {
		fn_delete_profile_field($matching_id);
	}

	fn_delete_field_values($field_id);
	db_query("DELETE FROM ?:profile_fields WHERE field_id = ?i", $field_id);
	db_query("DELETE FROM ?:profile_fields_data WHERE field_id = ?i", $field_id);
	db_query("DELETE FROM ?:profile_field_descriptions WHERE object_id = ?i AND object_type = 'F'", $field_id);
}

function fn_profile_fields_areas()
{
	$areas = array (
		'profile' => 'profile',
		'checkout' => 'checkout'
	);

	fn_set_hook('profile_fields_areas', $areas);

	return $areas;
}

?>