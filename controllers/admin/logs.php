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
// $Id: logs.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($mode == 'clean') {
	db_query("TRUNCATE TABLE ?:logs");

	return array (CONTROLLER_STATUS_REDIRECT, "logs.manage");
}

if ($mode == 'manage') {

	list($logs, $search, $total) = fn_get_logs($_REQUEST);

	$view->assign('logs', $logs);
	$view->assign('search', $search);
	$view->assign('log_types', fn_get_log_types());
	$view->assign('total', $total);
}

function fn_get_logs($params, $items_per_page = null)
{
	// Init filter
	$params = fn_init_view('logs', $params);

	if ($items_per_page === null) {
		$items_per_page = Registry::get('settings.Appearance.admin_elements_per_page');
	}

	$sortings = array (
		'timestamp' => '?:logs.timestamp',
		'user' => array ('?:users.lastname', '?:users.firstname'),
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	$fields = array (
		'?:logs.*',
		'?:users.firstname',
		'?:users.lastname'
	);

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		$params['sort_order'] = 'asc';
	}

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = 'timestamp';
		$params['sort_order'] = 'desc';
	}

	$params['page'] = empty($params['page']) ? 1 : $params['page'];

	if (is_array($sortings[$params['sort_by']])) {
		$sorting = join(' ' . $directions[$params['sort_order']] . ', ', $sortings[$params['sort_by']]) . ' ' . $directions[$params['sort_order']];
	} else {
		$sorting = $sortings[$params['sort_by']] . ' ' . $directions[$params['sort_order']];
	}

	$join = "LEFT JOIN ?:users USING(user_id)";

	$condition = '';

	if (!empty($params['period']) && $params['period'] != 'A') {
		list($time_from, $time_to) = fn_create_periods($params);

		$condition .= db_quote(" AND (?:logs.timestamp >= ?i AND ?:logs.timestamp <= ?i)", $time_from, $time_to);
	}

	if (isset($params['q_user']) && fn_string_no_empty($params['q_user'])) {
		$condition .= db_quote(" AND (?:users.lastname LIKE ?l OR ?:users.firstname LIKE ?l)", "%".trim($params['q_user'])."%", "%".trim($params['q_user'])."%");
	}

	if (!empty($params['q_type'])) {
		$condition .= db_quote(" AND (?:logs.type LIKE ?l OR ?:logs.type LIKE ?l)", "%$params[q_type]%", "%$params[q_type]%");
	}

	if (!empty($params['q_action'])) {
		$condition .= db_quote(" AND (?:logs.action LIKE ?l OR ?:logs.action LIKE ?l)", "%$params[q_action]%", "%$params[q_action]%");
	}

	$limit = '';
	$total = 0;
	if (!empty($items_per_page)) {
		$total = db_get_field("SELECT COUNT(DISTINCT(?:logs.log_id)) FROM ?:logs ?p WHERE 1 ?p", $join, $condition);
		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}

	$data = db_get_array("SELECT " . join(', ', $fields) . " FROM ?:logs ?p WHERE 1 ?p ORDER BY $sorting $limit", $join, $condition);

	if (!$total) {
		$total = count($data);
	}

	foreach ($data as $k => $v) {
		$data[$k]['backtrace'] = !empty($v['backtrace']) ? unserialize($v['backtrace']) : array();
		$data[$k]['content'] = !empty($v['content']) ? unserialize($v['content']) : array();
	}

	return array ($data, $params, $total);
}

function fn_get_log_types()
{
	$types = db_get_array("SELECT a.option_id, a.option_name, b.description FROM ?:settings as a LEFT JOIN ?:settings_descriptions as b ON b.object_id = a.option_id AND b.object_type = 'O' AND lang_code = ?s WHERE a.section_id = 'Logging'", CART_LANGUAGE);

	foreach ($types as $k => $v) {
		$types[$k]['type'] = str_replace('log_type_', '', $v['option_name']);
		$types[$k]['actions'] = db_get_array("SELECT a.variant_name as action, b.description FROM ?:settings_variants as a LEFT JOIN ?:settings_descriptions as b ON b.object_id = a.variant_id AND b.object_type = 'V' AND lang_code = ?s WHERE a.option_id = ?i", CART_LANGUAGE, $v['option_id']);
	}

	return $types;
}

?>