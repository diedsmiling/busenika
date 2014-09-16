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
// $Id: static_data.php 10288 2010-07-30 12:56:02Z klerik $
//

if ( !defined('AREA') )	{ die('Access denied');	}

//
// Currently defined sections:
// C - Credit cards
// T - Titles
// N - Top navigation
// ----------------------

//
// Defaults
//
$section = empty($_REQUEST['section']) ? 'C' : $_REQUEST['section'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'update') {
		fn_update_static_data($_REQUEST['static_data'], $_REQUEST['param_id'], $section);
	}

	if ($mode == 'm_update') {
		if (!empty($_REQUEST['static_data'])) {
			foreach ($_REQUEST['static_data'] as $k => $v) {
				fn_update_static_data($v, $k, $section);
			}
		}
	}

	if ($mode == 'm_delete') {
		if (!empty($_REQUEST['static_data_ids'])) {
			foreach ($_REQUEST['static_data_ids'] as $k => $v) {
				fn_delete_static_data($v);
			}
		}
	}

	return array(CONTROLLER_STATUS_OK, "static_data.manage?section=$section");
}

//
// Delete
//
if ($mode == 'delete') {

	fn_delete_static_data($_REQUEST['param_id']);

	$static_data = db_get_field("SELECT COUNT(*) FROM ?:static_data WHERE ?:static_data.section = ?s", $_REQUEST['section']);
	if (empty($static_data)) {
		$view->display('views/static_data/manage.tpl');
	}
	exit;

} elseif ($mode == 'update') {

	$schema = fn_get_schema('static_data', 'schema');
	$section_data = $schema[$section];
	$view->assign('section_data', $section_data);

	$static_data = db_get_row("SELECT ?:static_data.*, ?:static_data_descriptions.descr FROM ?:static_data LEFT JOIN ?:static_data_descriptions ON ?:static_data.param_id = ?:static_data_descriptions.param_id AND ?:static_data_descriptions.lang_code = ?s WHERE ?:static_data.section = ?s AND ?:static_data.param_id = ?i", DESCR_SL, $_REQUEST['section'], $_REQUEST['param_id']);

	if (!empty($section_data['icon'])) {
		$static_data['icon'] = fn_get_image_pairs($static_data['param_id'], $section_data['icon']['type'], 'M', true, true, DESCR_SL);
	}

	if (!empty($section_data['multi_level'])) {
		$params = array(
			'section' => $_REQUEST['section'],
			'generate_levels' => true,
			'get_params' => true,
			'multi_level' => true,
			'plain' => true,
		);
		$view->assign('parent_items', fn_get_static_data($params));
	}

	$view->assign('static_data', $static_data);

	$view->assign('section', $section);

} elseif ($mode == 'manage') {

	$schema = fn_get_schema('static_data', 'schema');
	$section_data = $schema[$section];
	$view->assign('section_data', $section_data);

	$params = array(
		'section' => $_REQUEST['section'],
		'multi_level' => !empty($section_data['multi_level']),
		'generate_levels' => !empty($section_data['multi_level']),
		'icon_name' => !empty($section_data['icon']) ? $section_data['icon']['type'] : '',
		'get_params' => true
	);
	$static_data = fn_get_static_data($params);

	if (!empty($section_data['multi_level'])) {
		$params = array(
			'section' => $_REQUEST['section'],
			'generate_levels' => true,
			'get_params' => true,
			'multi_level' => true,
			'plain' => true,
		);
		$view->assign('parent_items', fn_get_static_data($params));
	}

	$view->assign('static_data', $static_data);
	$view->assign('section', $section);
}

function fn_update_static_data($data, $param_id, $section, $lang_code = DESCR_SL)
{
	$current_id_path = '';
	$schema = fn_get_schema('static_data', 'schema');
	$section_data = $schema[$section];

	if (!empty($section_data['has_localization'])) {
		$data['localization'] = empty($data['localization']) ? '' : fn_implode_localizations($data['localization']);
	}

	if (!empty($data['megabox'])) { // parse megabox value
		foreach ($data['megabox']['type'] as $p => $v) {
			if (!empty($v)) {
				$data[$p] = $v . ':' . intval($data[$p][$v]) . ':' . $data['megabox']['use_item'][$p];
			} else {
				$data[$p] = '';
			}
		}
	}

	if (!empty($param_id)) {
		$current_id_path = db_get_field("SELECT id_path FROM ?:static_data WHERE param_id = ?i", $param_id);
		db_query("UPDATE ?:static_data SET ?u WHERE param_id = ?i", $data, $param_id);
		db_query('UPDATE ?:static_data_descriptions SET ?u WHERE param_id = ?i AND lang_code = ?s', $data, $param_id, $lang_code);
	} else {
		$data['section'] = $section;

		$param_id = $data['param_id'] = db_query("INSERT INTO ?:static_data ?e", $data);
		foreach ((array)Registry::get('languages') as $data['lang_code'] => $_v) {
			db_query('REPLACE INTO ?:static_data_descriptions ?e', $data);
		}
	}

	// Generate ID path
	if (isset($data['parent_id'])) {
		if (!empty($data['parent_id'])) {
			$new_id_path = db_get_field("SELECT id_path FROM ?:static_data WHERE param_id = ?i", $data['parent_id']);
			$new_id_path .= '/' . $param_id;
		} else {
			$new_id_path = $param_id;
		}

		if (!empty($current_id_path) && $current_id_path != $new_id_path) {
			db_query("UPDATE ?:static_data SET id_path = CONCAT(?s, SUBSTRING(id_path, ?i)) WHERE id_path LIKE ?l", "$new_id_path/", strlen($current_id_path . '/') + 1, "$current_id_path/%");
		}
		db_query("UPDATE ?:static_data SET id_path = ?s WHERE param_id = ?i", $new_id_path, $param_id);
	}

	if (!empty($section_data['icon'])) {
		fn_attach_image_pairs('static_data_icon', $section_data['icon']['type'], $param_id, $lang_code);
	}

	return $param_id;
}

function fn_static_data_megabox($value)
{
	if (!empty($value)) {
		list($type, $id, $use_item) = explode(':', $value);

		return array(
			'types' => array(
				$type => array(
					'value' => intval($id)
				),
			),
			'use_item' => $use_item,
		);
	} else {
		return array();
	}
}

function fn_delete_static_data($param_id)
{
	if (!empty($param_id)) {
		$id_path = db_get_field("SELECT id_path FROM ?:static_data WHERE param_id = ?i", $param_id);
		$delete_ids = db_get_fields("SELECT param_id FROM ?:static_data WHERE param_id = ?i OR id_path LIKE ?l", $param_id, "$id_path/%");

		db_query("DELETE FROM ?:static_data WHERE param_id IN (?n)", $delete_ids);
		db_query("DELETE FROM ?:static_data_descriptions WHERE param_id IN (?n)", $delete_ids);
	}

	return true;
}

?>