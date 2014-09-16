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
// $Id: reward_points.post.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	return;
}

if ($mode == 'userlog') {

	$user_id = (AREA == 'A') ? $_REQUEST['user_id'] : $auth['user_id']; 
	$user_isset = db_get_field("SELECT user_id FROM ?:users WHERE user_id = ?i", $user_id);
	
	if (AREA == 'A') {
		fn_add_breadcrumb(fn_get_lang_var('users'), "profiles.manage");
		fn_add_breadcrumb(fn_get_lang_var('user_details_page'), "profiles.update?user_id=" . $user_id);
	} else {
		fn_add_breadcrumb(fn_get_lang_var('reward_points_log'));
	}

	if (!empty($user_isset)) {
		if (AREA == 'A') { // FIXME: What do we need it for? Replace with get_user_info possibly

			$params = array (
				'user_id' => $user_id,
				'exclude_user_types' => array ('A', 'S'),
			);

			list($users) = fn_get_users($params, $auth, Registry::get('settings.Appearance.admin_elements_per_page'));
			$view->assign('users', $users);		
		}

		$sortings = array (
			'timestamp' => 'timestamp',
			'amount' => 'amount'
		);

		$directions = array (
			'asc' => 'asc',
			'desc' => 'desc'
		);

		$sort_order = empty($_REQUEST['sort_order']) ? '' : $_REQUEST['sort_order'];
		$sort_by = empty($_REQUEST['sort_by']) ? '' : $_REQUEST['sort_by'];

		if (empty($sort_order) || !isset($directions[$sort_order])) {
			$sort_order = 'desc';
		}

		if (empty($sort_by) || !isset($sortings[$sort_by])) {
			$sort_by = 'timestamp';
		}

		$log_count = db_get_field("SELECT COUNT(change_id) FROM ?:reward_point_changes WHERE user_id = ?i", $user_id);
		$limit = fn_paginate(@$_REQUEST['page'], $log_count, Registry::get('addons.reward_points.log_per_page')); // FIXME
		
		$userlog = db_get_array("SELECT change_id, action, timestamp, amount, reason FROM ?:reward_point_changes WHERE user_id = ?i ORDER BY $sort_by $sort_order $limit", $user_id);

		$view->assign('sort_order', ($sort_order == 'asc') ? 'desc' : 'asc');
		$view->assign('sort_by', $sort_by);		
		$view->assign('userlog', $userlog);

	} else {
		if (empty($auth['user_id'])) {
			return array(CONTROLLER_STATUS_REDIRECT, "auth.login_form?return_url=" . urlencode(Registry::get('config.current_url')));
		} else {
			return array(CONTROLLER_STATUS_NO_PAGE);
		}
	}
}

?>