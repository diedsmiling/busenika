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
// $Id: profiles.php 10490 2010-08-23 11:30:06Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($mode == 'm_delete') {

		if (!empty($_REQUEST['user_ids'])) {
			foreach ($_REQUEST['user_ids'] as $v) {
				fn_delete_user($v);
			}
		}

		return array(CONTROLLER_STATUS_OK, "profiles.manage" . (isset($_REQUEST['user_type']) ? "?user_type=" . $_REQUEST['user_type'] : '' ));
	}

	if ($mode == 'export_range') {
		if (!empty($_REQUEST['user_ids'])) {

			if (empty($_SESSION['export_ranges'])) {
				$_SESSION['export_ranges'] = array();
			}

			if (empty($_SESSION['export_ranges']['users'])) {
				$_SESSION['export_ranges']['users'] = array('pattern_id' => 'users');
			}

			$_SESSION['export_ranges']['users']['data'] = array('user_id' => $_REQUEST['user_ids']);

			unset($_REQUEST['redirect_url']);
			return array(CONTROLLER_STATUS_REDIRECT, "exim.export?section=users&pattern_id=" . $_SESSION['export_ranges']['users']['pattern_id']);
		}
	}
}

if ($mode == 'manage') {

	list($users, $search) = fn_get_users($_REQUEST, $auth, Registry::get('settings.Appearance.admin_elements_per_page'));
	$view->assign('users', $users);
	$view->assign('search', $search);

	if (!empty($search['user_type'])) {
		$view->assign('user_type_description', fn_get_user_type_description($search['user_type']));
	}

	$view->assign('user_types', fn_get_user_types());
	$view->assign('countries', fn_get_countries(CART_LANGUAGE, true));
	$view->assign('states', fn_get_all_states());
	$view->assign('usergroups', fn_get_usergroups('F', DESCR_SL));

} elseif ($mode == 'act_as_user') {

	if (fn_is_restricted_admin($_REQUEST) == true) {
		return array(CONTROLLER_STATUS_DENIED);
	}

	$condition = fn_get_company_condition();
	$user_data = db_get_row("SELECT * FROM ?:users WHERE user_id = ?i $condition", $_REQUEST['user_id']);

	if (!empty($user_data)) {
		$user_type = empty($_REQUEST['area']) ? (($user_data['user_type'] == 'A') ? 'A' : 'C') : $_REQUEST['area']; // 'area' variable was used for loging in to the area different from the user type.

		$sess_data = array(
			'auth' => fn_fill_auth($user_data, array(), true, $user_type)
		);

		fn_init_user_session_data($sess_data, $_REQUEST['user_id']);

		Session::save(Session::get_id(), $sess_data, $user_type);

		return array(CONTROLLER_STATUS_REDIRECT, ($user_type == 'A') ? Registry::get('config.admin_index') : Registry::get('config.customer_index'));
	}

} elseif ($mode == 'picker') {
	$params = $_REQUEST;
	$params['exclude_user_types'] = array ('A', 'S');
	$params['skip_view'] = 'Y';
	
	list($users, $search) = fn_get_users($params, $auth, Registry::get('settings.Appearance.admin_elements_per_page'));
	$view->assign('users', $users);
	$view->assign('search', $search);

	$view->assign('countries', fn_get_countries(CART_LANGUAGE, true));
	$view->assign('states', fn_get_all_states());
	$view->assign('usergroups', fn_get_usergroups('F', CART_LANGUAGE));

	$view->display('pickers/users_picker_contents.tpl');
	exit;

} elseif ($mode == 'update' || $mode == 'add') {
	
	$companies = fn_get_short_companies();
	$view->assign('companies', $companies);
	
	Registry::set('navigation.tabs', array (
		'general' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'addons' => array (
			'title' => fn_get_lang_var('addons'),
			'js' => true
		)
	));

} elseif ($mode == 'delete') {

	$a = fn_delete_user($_REQUEST['user_id']);

	return array(CONTROLLER_STATUS_REDIRECT);

} elseif ($mode == 'update_status') {

	$condition = fn_get_company_condition();
	$user_data = db_get_row("SELECT * FROM ?:users WHERE user_id = ?i $condition", $_REQUEST['id']);
	if (!empty($user_data)) {
		$result = db_query("UPDATE ?:users SET status = ?s WHERE user_id = ?i", $_REQUEST['status'], $_REQUEST['id']);
		if ($result && $_REQUEST['id'] != 1) {
			fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('status_changed'));
			$force_notification = fn_get_notification_rules($_REQUEST);
			if (!empty($force_notification['C']) && $_REQUEST['status'] == 'A' && $user_data['status'] == 'D') {
				Registry::get('view_mail')->assign('user_data', $user_data);
				fn_send_mail($user_data['email'], Registry::get('settings.Company.company_users_department'), 'profiles/profile_activated_subj.tpl', 'profiles/profile_activated.tpl', '', ($_REQUEST['id'] != 1 ? $user_data['lang_code'] : CART_LANGUAGE));
			}
		} else {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('error_status_not_changed'));
			$ajax->assign('return_status', $user_data['status']);
		}
	}

	exit;
} elseif ($mode == 'password_reminder') {
	
	$cron_password = Registry::get('settings.Security.cron_password');
	
	if ((!isset($_REQUEST['cron_password']) || $cron_password != $_REQUEST['cron_password']) && (!empty($cron_password))) {
		die(fn_get_lang_var('access_denied'));
	}
	
	$expire = Registry::get('settings.Security.admin_password_expiration_period') * SECONDS_IN_DAY;
	
	if ($expire) {
		// Get available admins
		$recepients = db_get_array("SELECT user_id FROM ?:users WHERE user_type = 'A' AND status = 'A' AND (UNIX_TIMESTAMP() - password_change_timestamp) >= ?i", $expire);
		if (!empty($recepients)) {
			foreach ($recepients as $v) {
				$_user_data = fn_get_user_info($v['user_id'], true);
				
				$days = round((TIME - $_user_data['password_change_timestamp']) / SECONDS_IN_DAY);
				Registry::get('view_mail')->assign('days', $days);
				Registry::get('view_mail')->assign('user_data', $_user_data);
				Registry::get('view_mail')->assign('link', fn_url('auth.password_change', 'A', (Registry::get('settings.General.secure_admin') == "Y")? 'https' : 'http', '&'));
				
				fn_send_mail($_user_data['email'], Registry::get('settings.Company.company_users_department'), 'profiles/reminder_subj.tpl', 'profiles/reminder.tpl', '', $_user_data['lang_code']);
			}
		}

		fn_echo(str_replace('[count]', count($recepients), fn_get_lang_var('administrators_notified')));
	}

	exit;
}

?>