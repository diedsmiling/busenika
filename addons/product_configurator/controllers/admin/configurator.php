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
// $Id: configurator.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Define trusted variables that shouldn't be stripped
	fn_trusted_vars('configurator_group_data');
	$suffix = '';

	//************************************ CONFIGURATOR STEPS **********************************\\
	//
	// Add new configurator step
	//
	if ($mode == 'add_steps' && isset($_REQUEST['add_step_data'])) {
		foreach ($_REQUEST['add_step_data'] as $v) {
			if (!empty($v['step_name'])) {
				$step_id = db_query("INSERT INTO ?:conf_steps ?e", $v);
				fn_create_description('conf_step_descriptions', 'step_id', $step_id, $v);
			}
		}
		$suffix = ".manage?selected_section=steps";
	}
	//
	//  Delete configurator step
	//
	if ($mode == 'delete_step') { 
		if (is_array($_REQUEST['delete'])) {
			fn_delete_configurator_steps($_REQUEST['delete']);
		}
		$suffix = ".manage?selected_section=steps";
	}
	//
	//  Update configurator steps
	//
	if ($mode == 'update_steps') {
		foreach ($_REQUEST['step_data'] as $k => $v) {
			db_query("UPDATE ?:conf_steps SET ?u WHERE step_id = ?i", $v, $k);
			db_query("UPDATE ?:conf_step_descriptions SET ?u WHERE step_id = ?i AND lang_code = ?s", $v, $k, DESCR_SL);
		}
		$suffix = ".manage?selected_section=steps";
	}

	//************************************ CONFIGURATOR GROUPS **********************************\\
	//
	// Updating existing configurator group
	//
	if ($mode == 'update_groups') {
		foreach ($_REQUEST['configurator_group_data'] as $k => $v) {
			db_query("UPDATE ?:conf_groups SET ?u WHERE group_id = ?i", $v, $k);
			db_query("UPDATE ?:conf_group_descriptions SET ?u WHERE group_id = ?i AND lang_code = ?s", $v, $k, DESCR_SL);
		}
		$suffix = ".manage?selected_section=groups";
	}
	//
	// Delete selected configurator groups
	//
	if ($mode == 'delete_groups') {
		if (!empty($_REQUEST['group_ids'])) {
			foreach ($_REQUEST['group_ids'] as $g_id) {
				fn_delete_group($g_id);
			}
		}
		$suffix = ".manage?selected_section=groups";
	}
	//
	// Updating existing configurator group
	//	
	if ($mode == 'update_group_properties') { 

		$group_id = fn_update_configurator_group($_REQUEST['configurator_group_data'], $_REQUEST['group_id'], DESCR_SL);

		$suffix = ".update_group?group_id=$group_id";
	}

	//************************************ CONFIGURATOR CLASSES **********************************\\
	//
	//  Update classes
	//
	if ($mode == 'update_classes') {
		foreach ($_REQUEST['class_data'] as $k => $v) {
			db_query("UPDATE ?:conf_classes SET ?u WHERE class_id = ?i", $v, $k);
			db_query("UPDATE ?:conf_class_descriptions SET ?u WHERE class_id = ?i AND lang_code = ?s", $v, $k, DESCR_SL);
		}
		$suffix = ".manage?page=$page&selected_section=classes";
	}	
	//
	//  Delete classes
	//
	if ($mode == 'delete_classes') {
		if (!empty($_REQUEST['class_ids'])) {
			foreach ($_REQUEST['class_ids'] as $c_id) {
				fn_delete_class($c_id);
			}
		}
		$suffix = ".manage?selected_section=classes";
	}

	//
	// Update class properties
	//
	if ($mode == 'update_class_properties') { 

		$class_id = fn_update_configurator_class($_REQUEST['update_class_data'], $_REQUEST['class_id'], DESCR_SL);

		$suffix = ".update_class?class_id=$class_id";
	}

	return array(CONTROLLER_STATUS_OK, "configurator$suffix");
}

if ($mode == 'update_group') {

	fn_add_breadcrumb(fn_get_lang_var('product_groups'), "configurator.manage?selected_section=groups");

	$configurator_group = db_get_row("SELECT ?:conf_groups.group_id, ?:conf_group_descriptions.configurator_group_name, ?:conf_group_descriptions.full_description, ?:conf_groups.step_id, ?:conf_groups.status, ?:conf_groups.configurator_group_type FROM ?:conf_groups LEFT JOIN ?:conf_group_descriptions ON ?:conf_group_descriptions.group_id = ?:conf_groups.group_id WHERE ?:conf_group_descriptions.lang_code = ?s AND ?:conf_groups.group_id = ?i", DESCR_SL, $_REQUEST['group_id']);

	Registry::set('navigation.tabs', array (
		'general' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'products' => array (
			'title' => fn_get_lang_var('products'),
			'js' => true
		)
	));

	$configurator_group['main_pair'] = fn_get_image_pairs($_REQUEST['group_id'], 'conf_group', 'M', true, true, DESCR_SL);

	$configurator_group['product_ids'] = db_get_fields("SELECT ?:conf_group_products.product_id FROM ?:conf_group_products WHERE ?:conf_group_products.group_id = ?i", $_REQUEST['group_id']);

	$view->assign('configurator_group', $configurator_group);

	list($steps, $search) = fn_get_configurator_steps($_REQUEST);
	$view->assign('steps', $steps);

} elseif ($mode == 'add_group') {

	fn_add_breadcrumb(fn_get_lang_var('product_groups'), "configurator.manage?selected_section=groups");

	Registry::set('navigation.tabs', array (
		'general' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'products' => array (
			'title' => fn_get_lang_var('products'),
			'js' => true
		)
	));

	list($steps) = fn_get_configurator_steps($_REQUEST);
	$view->assign('steps', $steps);
	
} elseif ($mode == 'update_class') {

	fn_add_breadcrumb(fn_get_lang_var('product_classes'), "configurator.manage?selected_section=classes");

	$product_class = db_get_row("SELECT ?:conf_classes.*, ?:conf_class_descriptions.class_name FROM ?:conf_classes LEFT JOIN ?:conf_class_descriptions ON ?:conf_class_descriptions.class_id = ?:conf_classes.class_id WHERE ?:conf_class_descriptions.lang_code = ?s AND ?:conf_classes.class_id = ?i", DESCR_SL, $_REQUEST['class_id']);

	// Get class products	
	$product_class['product_ids'] = db_get_fields("SELECT ?:products.product_id FROM ?:products LEFT JOIN ?:conf_class_products ON ?:conf_class_products.product_id = ?:products.product_id WHERE ?:conf_class_products.class_id = ?i", $_REQUEST['class_id']);

	$product_class['compatible_classes'] = db_get_fields("SELECT ?:conf_compatible_classes.slave_class_id FROM ?:conf_compatible_classes WHERE master_class_id = ?i", $_REQUEST['class_id']);

	Registry::set('navigation.tabs', array (
		'general' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'products' => array (
			'title' => fn_get_lang_var('products'),
			'js' => true
		)
	));

	$view->assign('product_class', $product_class);

	list($groups) = fn_get_configurator_groups($_REQUEST);
	$view->assign('groups', $groups);
	
	$view->assign('classes', fn_get_configurator_classes(array('simple' => true)));

} elseif ($mode == 'add_class') {

	fn_add_breadcrumb(fn_get_lang_var('product_classes'), "configurator.manage?selected_section=classes");

	Registry::set('navigation.tabs', array (
		'general' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'products' => array (
			'title' => fn_get_lang_var('products'),
			'js' => true
		)
	));

	list($groups) = fn_get_configurator_groups($_REQUEST);
	$view->assign('groups', $groups);
	
	$view->assign('classes', fn_get_configurator_classes(array('simple' => true)));

} elseif ($mode == 'manage') {

	list($classes, $search) = fn_get_configurator_classes($_REQUEST);
	$view->assign('classes', $classes);
	$view->assign('classes_search', $search);

	list($steps, $search) = fn_get_configurator_steps($_REQUEST);
	$view->assign('steps', $steps);
	$view->assign('steps_search', $search);


	list($groups, $search) = fn_get_configurator_groups($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'));	
	$view->assign('groups', $groups);
	$view->assign('groups_search', $search);

	list($all_groups) = fn_get_configurator_groups($_REQUEST);
	$view->assign('all_groups', $all_groups);

	Registry::set('navigation.tabs', array(
		'steps' => array (
			'title' => fn_get_lang_var('steps'),
			'js' => true
		),
		'groups' => array (
			'title' => fn_get_lang_var('product_groups'),
			'js' => true
		),
		'classes' => array (
			'title' => fn_get_lang_var('product_classes'),
			'js' => true
		),
	));

} elseif ($mode == 'delete_group') {
	if (!empty($_REQUEST['group_id'])) {
		fn_delete_group($_REQUEST['group_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "configurator.manage?selected_section=groups");

} elseif ($mode == 'delete_step') { 
	if (!empty($_REQUEST['step_id'])) {
		fn_delete_configurator_steps((array)$_REQUEST['step_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "configurator.manage?selected_section=steps");

} elseif ($mode == 'delete_class') {
	if (!empty($_REQUEST['class_id'])) {
		fn_delete_class($_REQUEST['class_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "configurator.manage?selected_section=classes");
}

function fn_delete_configurator_steps($step_ids)
{
	db_query("DELETE FROM ?:conf_steps WHERE step_id IN (?n)", $step_ids);
	db_query("DELETE FROM ?:conf_step_descriptions WHERE step_id IN (?n)", $step_ids);

	$_data = array (
		'step_id' => 0
	);

	db_query('UPDATE ?:conf_groups SET ?u WHERE step_id IN (?n)', $_data, $step_ids);
}

function fn_get_configurator_groups($params, $items_per_page = 0)
{
	$default_params = array (
		'page' => 1,
	);

	$params = array_merge($default_params, $params);

	$sortings = array (
		'group_id' => "?:conf_groups.group_id",
		'group_name' => "?:conf_group_descriptions.configurator_group_name",
		'step_name' => "?:conf_groups.step_id",
		'display_type' => "?:conf_groups.configurator_group_type",
		'status' => "?:conf_groups.status",
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	if (empty($params['group_sort_order']) || empty($directions[$params['group_sort_order']])) {
		$params['group_sort_order'] = 'asc';
	}

	if (empty($params['group_sort_by']) || empty($sortings[$params['group_sort_by']])) {
		$params['group_sort_by'] = 'group_name';
	}

	$sorting = $sortings[$params['group_sort_by']] . ' ' . $directions[$params['group_sort_order']];

	// Reverse sorting (for usage in view)
	$params['group_sort_order'] = $params['group_sort_order'] == 'asc' ? 'desc' : 'asc';

	$limit = '';
	if (!empty($items_per_page)) {
		$group_data_count = db_get_field("SELECT COUNT(*) FROM ?:conf_groups");
		$limit = fn_paginate($params['page'], $group_data_count, $items_per_page);
	}

	$groups = db_get_hash_array("SELECT ?:conf_groups.group_id, ?:conf_group_descriptions.configurator_group_name, ?:conf_group_descriptions.full_description, ?:conf_groups.step_id, ?:conf_groups.status, ?:conf_groups.configurator_group_type FROM ?:conf_groups LEFT JOIN ?:conf_group_descriptions ON ?:conf_group_descriptions.group_id = ?:conf_groups.group_id WHERE ?:conf_group_descriptions.lang_code = ?s ORDER BY $sorting $limit", 'group_id', DESCR_SL);

	return array($groups, $params);
}

function fn_get_configurator_steps($params)
{
	$sortings = array (
		'pos' => "?:conf_steps.position",
		'step_name' => "?:conf_step_descriptions.step_name",
		'status' => "?:conf_steps.status",
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	if (empty($params['step_sort_order']) || empty($directions[$params['step_sort_order']])) {
		$params['step_sort_order'] = 'asc';
	}

	if (empty($params['step_sort_by']) || empty($sortings[$params['step_sort_by']])) {
		$params['step_sort_by'] = 'pos';
	}

	$sorting = $sortings[$params['step_sort_by']] . ' ' . $directions[$params['step_sort_order']];

	// Reverse sorting (for usage in view)
	$params['step_sort_order'] = $params['step_sort_order'] == 'asc' ? 'desc' : 'asc';

	$steps = db_get_array("SELECT ?:conf_steps.*, ?:conf_step_descriptions.step_name, ?:conf_steps.status FROM ?:conf_steps LEFT JOIN ?:conf_step_descriptions ON ?:conf_step_descriptions.step_id = ?:conf_steps.step_id WHERE ?:conf_step_descriptions.lang_code = ?s ORDER BY $sorting ", DESCR_SL);

	return array($steps, $params);
}

function fn_get_configurator_classes($params)
{
	$sortings = array (
		'class_name' => "?:conf_class_descriptions.class_name",
		'group_name' => "?:conf_classes.group_id",
		'status' => "?:conf_classes.status",
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	if (empty($params['class_sort_order']) || empty($directions[$params['class_sort_order']])) {
		$params['class_sort_order'] = 'asc';
	}

	if (empty($params['class_sort_by']) || empty($sortings[$params['class_sort_by']])) {
		$params['class_sort_by'] = 'class_name';
	}

	$sorting = $sortings[$params['class_sort_by']] . ' ' . $directions[$params['class_sort_order']];

	// Reverse sorting (for usage in view)
	$params['class_sort_order'] = $params['class_sort_order'] == 'asc' ? 'desc' : 'asc';

	if (!empty($params['simple'])) {
		return db_get_hash_single_array("SELECT ?:conf_classes.class_id, ?:conf_class_descriptions.class_name FROM ?:conf_classes LEFT JOIN ?:conf_class_descriptions ON ?:conf_class_descriptions.class_id = ?:conf_classes.class_id WHERE ?:conf_class_descriptions.lang_code = ?s ORDER BY $sorting", array('class_id', 'class_name'), DESCR_SL);
	} else {
		$classes = db_get_hash_array("SELECT ?:conf_classes.*, ?:conf_class_descriptions.class_name FROM ?:conf_classes LEFT JOIN ?:conf_class_descriptions ON ?:conf_class_descriptions.class_id = ?:conf_classes.class_id WHERE ?:conf_class_descriptions.lang_code = ?s ORDER BY $sorting", 'class_id', DESCR_SL);

		return array($classes, $params);
	}
}

function fn_update_configurator_group($data, $group_id, $lang_code = DESCR_SL)
{
	if (!empty($group_id)) {
		db_query('UPDATE ?:conf_groups SET ?u WHERE group_id = ?i', $data, $group_id);
		db_query('UPDATE ?:conf_group_descriptions SET ?u WHERE group_id = ?i AND lang_code = ?s', $data, $group_id, $lang_code);
	} else {
		$group_id = $data['group_id'] = db_query('INSERT INTO ?:conf_groups ?e', $data);
		foreach ((array)Registry::get('languages') as $data['lang_code'] => $_v) {
			db_query("INSERT INTO ?:conf_group_descriptions ?e", $data);
		}
	}

	// Updating category images
	fn_attach_image_pairs('configurator_main', 'conf_group', $group_id, $lang_code);

	// Update group products
	db_query("DELETE FROM ?:conf_group_products WHERE group_id = ?i", $group_id);
	if (!empty($data['product_ids'])) {
		$p_ids = explode(',', $data['product_ids']);
		foreach ($p_ids as $p_id) {
			db_query("INSERT INTO ?:conf_group_products (group_id, product_id) VALUES (?i, ?i)", $group_id, $p_id);
		}
	}

	return $group_id;
}

function fn_update_configurator_class($data, $class_id, $lang_code = DESCR_SL)
{
	if (!empty($class_id)) {
		db_query('UPDATE ?:conf_classes SET ?u WHERE class_id = ?i', $data, $class_id);
		db_query('UPDATE ?:conf_class_descriptions SET ?u WHERE class_id = ?i AND lang_code = ?s', $data, $class_id, DESCR_SL);
	} else {
		$class_id = $data['class_id'] = db_query('INSERT INTO ?:conf_classes ?e', $data);
		foreach ((array)Registry::get('languages') as $data['lang_code'] => $_v) {
			db_query("INSERT INTO ?:conf_class_descriptions ?e", $data);
		}
	}

	// Updating compatility classes
	db_query("DELETE FROM ?:conf_compatible_classes WHERE master_class_id = ?i", $class_id);
	if (!empty($data['compatible_classes'])) {
		foreach ($data['compatible_classes'] as $c_id) {
			db_query("INSERT INTO ?:conf_compatible_classes (master_class_id, slave_class_id) VALUES (?i, ?i)", $class_id, $c_id);
		}
	}

	// Update group products
	db_query("DELETE FROM ?:conf_class_products WHERE class_id = ?i", $class_id);
	if (!empty($data['product_ids'])) {
		$p_ids = explode(',', $data['product_ids']);
		foreach ($p_ids as $p_id) {
			db_query("INSERT INTO ?:conf_class_products (class_id, product_id) VALUES (?i, ?i)", $class_id, $p_id);
		}
	}

	return $class_id;
}
?>