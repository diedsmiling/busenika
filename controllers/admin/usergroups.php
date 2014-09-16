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
// $Id: usergroups.php 9820 2010-06-21 11:31:45Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$suffix = '';
	//
	// Updating usergroups
	//
	if ($mode == 'update') {
		foreach ($_REQUEST['usergroup_data'] as $key => $v) {
			$old_status = db_get_field("SELECT status FROM ?:usergroups WHERE usergroup_id = ?i", $key);
			if (defined('RESTRICTED_ADMIN') && ($v['type'] == 'A' || $old_status == 'A')) {
				fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('access_denied'));
				continue;
			}
			if (!empty($v['usergroup'])) {
				db_query("UPDATE ?:usergroups SET ?u WHERE usergroup_id = ?i", $v, $key);
				db_query("UPDATE ?:usergroup_descriptions SET ?u WHERE usergroup_id = ?i AND lang_code = ?s", $v, $key, DESCR_SL);
			}
		}

		$suffix .= '.manage';
	}

	//
	// Deleting selected usergroups
	//
	if ($mode == 'delete') {
		if (!empty($_REQUEST['usergroup_ids'])) {
			fn_delete_usergroups($_REQUEST['usergroup_ids']);
		}

		$suffix .= '.manage';
	}

	//
	// Adding new usergroups
	//
	if ($mode == 'add') {

		foreach ($_REQUEST['usergroup_data'] as $k => $v) {
			if (defined('RESTRICTED_ADMIN') && $v['type'] == 'A') {
				fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('access_denied'));
				continue;
			}
			if (!empty($v['usergroup'])) {
				$usergroup_id = db_query("INSERT INTO ?:usergroups ?e", $v);

				if ($usergroup_id < ALLOW_USERGROUP_ID_FROM) {
					db_query("UPDATE ?:usergroups SET usergroup_id = ?i WHERE usergroup_id = ?i", ALLOW_USERGROUP_ID_FROM, $usergroup_id);
					$usergroup_id = ALLOW_USERGROUP_ID_FROM;
				}

				$v['usergroup_id'] = $usergroup_id;

				foreach ((array)Registry::get('languages') as $v['lang_code'] => $_v) {
					db_query("INSERT INTO ?:usergroup_descriptions ?e", $v);
				}
			}
		}

		$suffix .= '.manage';
	}

	// Set privileges
	if ($mode == 'assign_privileges') {
		db_query("DELETE FROM ?:usergroup_privileges WHERE usergroup_id = ?i", $_REQUEST['usergroup_id']);
		if (@is_array($_REQUEST['set_privileges'])) {
			$data = array (
				'usergroup_id' => $_REQUEST['usergroup_id']
			);
			foreach ($_REQUEST['set_privileges'] as $data['privilege'] => $v) {
				db_query("INSERT INTO ?:usergroup_privileges ?e", $data);
			}
		}

		$suffix = ".assign_privileges?usergroup_id=$_REQUEST[usergroup_id]";
	}

	if ($mode == 'privileges') {
		if ($action == 'update') {
			foreach ((array)$_REQUEST['section_name'] as $sec_id => $v) {
				db_query("UPDATE ?:privilege_section_descriptions SET description = ?s WHERE section_id = ?i AND lang_code = ?s", $v, $sec_id, DESCR_SL);
			}

			foreach ((array)$_REQUEST['privilege_descr'] as $pr_id => $v) {
				db_query("UPDATE ?:privilege_descriptions SET description = ?s WHERE privilege = ?s AND lang_code = ?s", $v, $pr_id, DESCR_SL);
			}
		}

		$suffix = ".privileges";
	}

	if ($mode == 'bulk_update_status') {
		if (!empty($_REQUEST['link_ids'])) {
			$new_status = $action == 'approve' ? 'A' : 'D';
			db_query("UPDATE ?:usergroup_links SET status = ?s WHERE link_id IN(?n)", $new_status, $_REQUEST['link_ids']);

			$force_notification = fn_get_notification_rules($_REQUEST);
			if (!empty($force_notification['C'])) {
				$usergroup_links = db_get_hash_multi_array("SELECT * FROM ?:usergroup_links WHERE link_id IN(?n)", array('user_id', 'usergroup_id'), $_REQUEST['link_ids']);
				foreach ($usergroup_links as $u_id => $val) {
					fn_send_usergroup_status_notification($u_id, array_keys($val), $new_status);
				}
			}
		}

		$suffix = ".requests";
	}

	return array(CONTROLLER_STATUS_OK, "usergroups$suffix");
}

if ($mode == 'assign_privileges' && !empty($_REQUEST['usergroup_id'])) {

	if (defined('RESTRICTED_ADMIN')) {
		$requested_mtype = db_get_field("SELECT type FROM ?:usergroups WHERE usergroup_id = ?i", $_REQUEST['usergroup_id']);
		if ($requested_mtype == 'A') {
			return array(CONTROLLER_STATUS_DENIED);
		}
	}

	$usergroup_name = db_get_field("SELECT usergroup FROM ?:usergroup_descriptions WHERE usergroup_id = ?i AND lang_code = ?s", $_REQUEST['usergroup_id'], DESCR_SL);

	fn_add_breadcrumb(fn_get_lang_var('usergroups'), "usergroups.manage");

	$usergroup_privileges = db_get_hash_single_array("SELECT privilege FROM ?:usergroup_privileges WHERE usergroup_id = ?i", array('privilege', 'privilege'), $_REQUEST['usergroup_id']);

	$view->assign('usergroup_privileges', $usergroup_privileges);
	$view->assign('usergroup_name', $usergroup_name);
	$view->assign('privileges', db_get_hash_multi_array("SELECT a.*, b.*, c.description as section FROM ?:privileges as a LEFT JOIN ?:privilege_descriptions as b ON b.privilege = a.privilege AND b.lang_code = ?s INNER JOIN ?:privilege_section_descriptions as c on c.section_id = b.section_id AND c.lang_code = ?s ORDER BY b.section_id, b.description", array('section_id'), CART_LANGUAGE, CART_LANGUAGE));

} elseif ($mode == 'privileges') {

	fn_add_breadcrumb(fn_get_lang_var('usergroups'), "usergroups.manage");

	$privileges = db_get_hash_multi_array("SELECT a.*, b.*, c.description as section FROM ?:privileges as a LEFT JOIN ?:privilege_descriptions as b ON b.privilege = a.privilege AND b.lang_code = ?s INNER JOIN ?:privilege_section_descriptions as c on c.section_id = b.section_id AND c.lang_code = ?s ORDER BY b.section_id, b.description", array('section_id'), DESCR_SL, DESCR_SL);
	
	$view->assign('privileges', $privileges);

} elseif ($mode == 'manage') {

	$where = defined('RESTRICTED_ADMIN') ? "a.type!='A' ": '1';

	$usergroups = db_get_array("SELECT a.usergroup_id, a.status, a.type, b.usergroup FROM ?:usergroups as a LEFT JOIN ?:usergroup_descriptions as b ON b.usergroup_id = a.usergroup_id AND b.lang_code = ?s WHERE $where ORDER BY usergroup", DESCR_SL);

	$view->assign('usergroups', $usergroups);

	Registry::set('navigation.tabs', array (
		'general_0' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
	));

} elseif ($mode == 'update') {

	$usergroup = db_get_row("SELECT a.usergroup_id, a.status, a.type, b.usergroup FROM ?:usergroups as a LEFT JOIN ?:usergroup_descriptions as b ON b.usergroup_id = a.usergroup_id AND b.lang_code = ?s WHERE a.usergroup_id = ?i", DESCR_SL, $_REQUEST['usergroup_id']);

	$view->assign('usergroup', $usergroup);

	Registry::set('navigation.tabs', array (
		'general_' . $_REQUEST['usergroup_id'] => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
	));

} elseif ($mode == 'delete') {
	if (!empty($_REQUEST['usergroup_id'])) {
		fn_delete_usergroups((array)$_REQUEST['usergroup_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "usergroups.manage");

} elseif ($mode == 'update_status') {
	$user_data = fn_get_user_info($_REQUEST['user_id']);
	if (empty($user_data) || (defined('COMPANY_ID') && $user_data['is_root'] == 'Y') || (defined('RESTRICTED_ADMIN') && ($auth['user_id'] == $_REQUEST['user_id'] || fn_is_restricted_admin(array('user_id' => $_REQUEST['user_id']))))) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('access_denied'));
		exit;
	}

	$old_status = db_get_field("SELECT status FROM ?:usergroup_links WHERE user_id = ?i AND usergroup_id = ?i", $_REQUEST['user_id'], $_REQUEST['id']);

	$result = fn_change_usergroup_status($_REQUEST['status'], $_REQUEST['user_id'], $_REQUEST['id'], fn_get_notification_rules($_REQUEST));
	if ($result) {
		fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('status_changed'));
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('error_status_not_changed'));
		$ajax->assign('return_status', empty($old_status) ? 'F' : $old_status);
	}

	exit;

} elseif ($mode == 'requests') {

	fn_add_breadcrumb(fn_get_lang_var('usergroups'), "usergroups.manage");

	list($requests, $search) = fn_get_usergroup_requests($_REQUEST, Registry::get('settings.Appearance.admin_orders_per_page'));

	$view->assign('usergroup_requests', $requests);
	$view->assign('search', $search);
}

function fn_get_usergroup_requests($params, $items_per_page = 0, $status = 'P', $lang_code = CART_LANGUAGE)
{
	// Set default values to input params
	$params['page'] = empty($params['page']) ? 1 : $params['page']; // default page is 1

	$fields = array (
		"?:usergroup_links.user_id",
		"?:usergroup_links.link_id",
		"?:usergroup_links.usergroup_id",
		"?:usergroup_links.status",
		"?:users.firstname",
		"?:users.lastname",
		"?:usergroup_descriptions.usergroup"
	);

	$sortings = array (
		'customer' => array("?:users.lastname", "?:users.firstname"),
		'usergroup' => "?:usergroup_descriptions.usergroup",
		'status' => "?:usergroup_links.status"
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		$params['sort_order'] = 'desc';
	}

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = 'customer';
	}

	$sorting = (is_array($sortings[$params['sort_by']]) ? implode(' ' . $directions[$params['sort_order']]. ', ', $sortings[$params['sort_by']]): $sortings[$params['sort_by']]). " " .$directions[$params['sort_order']];

	// Reverse sorting (for usage in view)
	$params['sort_order'] = $params['sort_order'] == 'asc' ? 'desc' : 'asc';

	$condition = '';

	if (!empty($params['cname'])) {
		$arr = explode(' ', $params['cname']);
		if (sizeof($arr) == 2) {
			$condition .= db_quote(" AND ?:users.firstname LIKE ?l AND ?:users.lastname LIKE ?l", "%$arr[0]%", "%$arr[1]%");
		} else {
			$condition .= db_quote(" AND (?:users.firstname LIKE ?l OR ?:users.lastname LIKE ?l)", "%$params[cname]%", "%$params[cname]%");
		}
	}

	if (!empty($params['ugname'])) {
		$condition .= db_quote(" AND ?:usergroup_descriptions.usergroup LIKE ?l", "%$params[ugname]%");
	}

	$join = db_quote("LEFT JOIN ?:users ON ?:usergroup_links.user_id = ?:users.user_id LEFT JOIN ?:usergroup_descriptions ON ?:usergroup_links.usergroup_id = ?:usergroup_descriptions.usergroup_id AND ?:usergroup_descriptions.lang_code = ?s", $lang_code);

	$limit = '';
	if (!empty($items_per_page)) {
		$total = db_get_field("SELECT COUNT(?:usergroup_links.link_id) FROM ?:usergroup_links $join WHERE ?:usergroup_links.status = ?s $condition", $status);
		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}

	$requests = db_get_array("SELECT " . implode(', ', $fields) . " FROM ?:usergroup_links $join WHERE ?:usergroup_links.status = ?s $condition ORDER BY $sorting $limit", $status);

	return array($requests, $params);
}

function fn_delete_usergroups($usergroup_ids)
{
	db_query("DELETE FROM ?:usergroups WHERE usergroup_id IN (?n)", $usergroup_ids);
	db_query("DELETE FROM ?:usergroup_descriptions WHERE usergroup_id IN (?n)", $usergroup_ids);
	db_query("DELETE FROM ?:usergroup_privileges WHERE usergroup_id IN (?n)", $usergroup_ids);
	db_query("DELETE FROM ?:usergroup_links WHERE usergroup_id IN (?n)", $usergroup_ids);
}

function fn_change_usergroup_status($status, $user_id, $usergroup_id, $force_notification = array())
{
	$data = array(
		'user_id' => $user_id,
		'usergroup_id' => $usergroup_id,
		'status' => $status
	);
	$result = db_query("REPLACE INTO ?:usergroup_links SET ?u", $data);

	if (!empty($force_notification['C'])) {
		fn_send_usergroup_status_notification($user_id, (array)$usergroup_id, $status);
	}

	return $result;
}

function fn_send_usergroup_status_notification($user_id, $usergroup_ids, $status)
{
	$user_data = fn_get_user_info($user_id);
	Registry::get('view_mail')->assign('user_data', $user_data);
	Registry::get('view_mail')->assign('usergroups', fn_get_usergroups('F', $user_data['lang_code']));
	Registry::get('view_mail')->assign('usergroup_ids', $usergroup_ids);

	$prefix = ($status == 'A') ? 'activation' : 'disactivation';
	fn_send_mail($user_data['email'], Registry::get('settings.Company.company_users_department'), 'profiles/usergroup_' . $prefix . '_subj.tpl', 'profiles/usergroup_' . $prefix . '.tpl', array(), $user_data['lang_code']);
}

?>