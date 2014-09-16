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
// $Id: profiles.post.php 10556 2010-08-31 10:27:30Z 2tl $
//
if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if (AREA == 'A') {
		$_auth = NULL;
	} else {
		$_auth = &$auth;
	}

	//
	// Create new user
	//
	if ($mode == 'add') {

		if (fn_is_restricted_admin($_REQUEST) == true) {
			return array(CONTROLLER_STATUS_DENIED);
		}

		if (AREA != 'A') {
			if (Registry::get('settings.Image_verification.use_for_register') == 'Y' && fn_image_verification('register', empty($_REQUEST['verification_answer']) ? '' : $_REQUEST['verification_answer']) == false) {
				fn_save_post_data();
				$suffix = (strpos($_SERVER['HTTP_REFERER'], '?') !== false ? '&' : '?') . 'login_type=register';

				return array(CONTROLLER_STATUS_REDIRECT, $_SERVER['HTTP_REFERER'] . $suffix);
			}
		}
		
		if (isset($_REQUEST['copy_address']) && empty($_REQUEST['copy_address'])) {
			$_REQUEST['ship_to_another'] = 'Y';
		}
		if ($res = fn_update_user(0, $_REQUEST['user_data'], $_auth, !empty($_REQUEST['ship_to_another']), (AREA == 'A' ? !empty($_REQUEST['notify_customer']) : true))) {
			$suffix = 'update';
			list($user_id, $profile_id) = $res;

			// Cleanup user info stored in cart
			if (!empty($_SESSION['cart']) && !empty($_SESSION['cart']['user_data']) && AREA != 'A') {
				unset($_SESSION['cart']['user_data']);
			}

			if (Registry::get('settings.General.user_multiple_profiles') == 'Y') {
				$suffix .= "?profile_id=$profile_id";
			}

			if (AREA == 'A') {
				$suffix .= "?user_id=$user_id";
			}

			// Delete anonymous authentication
			if (AREA != 'A') {
				if ($cu_id = fn_get_cookie('cu_id') && !empty($auth['user_id'])) {
					fn_delete_cookies('cu_id');
				}
			}
		} else {
			$suffix = 'add';
		}

		return array(CONTROLLER_STATUS_OK, "profiles." . $suffix);
	}

	//
	// Update user
	//
	if ($mode == 'update') {

		if (fn_is_restricted_admin($_REQUEST) == true) {
			return array(CONTROLLER_STATUS_DENIED);
		}
		
		$user_id = (AREA == 'A' && !empty($_REQUEST['user_id'])) ? $_REQUEST['user_id'] : $auth['user_id'];
		$suffix = '';

		if (!empty($_REQUEST['default_cc'])) {
			$cards_data = db_get_field("SELECT credit_cards FROM ?:user_profiles WHERE profile_id = ?i", $_REQUEST['profile_id']);
			if (!empty($cards_data)) {
				$cards = unserialize(fn_decrypt_text($cards_data));
				foreach ($cards as $cc_id => $val) {
					$cards[$cc_id]['default'] = $_REQUEST['default_cc'] == $cc_id ? true : false;
				}
				$cards_data = array (
					'credit_cards' => fn_encrypt_text(serialize($cards))
				);
				db_query('UPDATE ?:user_profiles SET ?u WHERE profile_id = ?i', $cards_data, $_REQUEST['profile_id']);
			}
		}
		
		if (isset($_REQUEST['copy_address']) && empty($_REQUEST['copy_address'])) {
			$_REQUEST['ship_to_another'] = 'Y';
		}
		//fn_check_company_id($_REQUEST['user_data']);
		if ($res = fn_update_user($user_id, $_REQUEST['user_data'], $_auth, !empty($_REQUEST['ship_to_another']), (AREA == 'A' ? !empty($_REQUEST['notify_customer']) : true))) {
			list($user_id, $profile_id) = $res;

			// Cleanup user info stored in cart
			if (!empty($_SESSION['cart']) && !empty($_SESSION['cart']['user_data']) && AREA != 'A') {
				unset($_SESSION['cart']['user_data']);
			}

			if (!empty($_REQUEST['return_url'])) {
				return array(CONTROLLER_STATUS_OK, $_REQUEST['return_url']);			
			}
			
			if (Registry::get('settings.General.user_multiple_profiles') == 'Y') {
				$suffix = "?profile_id=$profile_id";
			}
		}

		if (AREA == 'A' && !empty($_REQUEST['user_id'])) {
			$suffix .= "?user_id=$_REQUEST[user_id]";
		}

		if (!empty($_REQUEST['return_url'])) {
			$suffix .= '?return_url=' . urlencode($_REQUEST['return_url']);
		}
		
		return array(CONTROLLER_STATUS_OK, "profiles.update" . $suffix);
	}

	if ($mode == 'update_cards') {
		if (fn_is_restricted_admin($_REQUEST) == true) {
			return array(CONTROLLER_STATUS_DENIED);
		}
		$suffix = '';
		if (!empty($_REQUEST['profile_id']) && !empty($_REQUEST['payment_info'])) {
			$cards_data = db_get_field("SELECT credit_cards FROM ?:user_profiles WHERE profile_id = ?i", $_REQUEST['profile_id']);
			$cards = empty($cards_data) ? array() : unserialize(fn_decrypt_text($cards_data));

			$id = empty($_REQUEST['card_id']) ? 'cc_' . TIME : $_REQUEST['card_id'];
			$cards[$id] = $_REQUEST['payment_info'];
			$cards[$id]['default'] = empty($cards_data) ? true : (empty($_REQUEST['default_cc']) ? false : true);

			$cards_data = array (
				'credit_cards' => fn_encrypt_text(serialize($cards))
			);
			db_query('UPDATE ?:user_profiles SET ?u WHERE profile_id = ?i', $cards_data, $_REQUEST['profile_id']);

			if (Registry::get('settings.General.user_multiple_profiles') == 'Y') {
				$suffix = "?profile_id=$_REQUEST[profile_id]";
			}
			if (AREA == 'A' && !empty($_REQUEST['user_id'])) {
				$suffix .= "?user_id=$_REQUEST[user_id]";
			}
		}
		return array(CONTROLLER_STATUS_OK, "profiles.update" . $suffix);
	}
}

if ($mode == 'add' || $mode == 'update') {

	if (AREA == 'A' && fn_is_restricted_admin($_REQUEST) == true) {
		return array(CONTROLLER_STATUS_DENIED);
	}

	$uid = 0;
	$user_data = array();
	$profile_id = empty($_REQUEST['profile_id']) ? 0 : $_REQUEST['profile_id'];
	if (AREA == 'A') {
		$uid = empty($_REQUEST['user_id']) ? (($mode == 'add') ? '' : $auth['user_id']) : $_REQUEST['user_id'];
	} elseif ($mode == 'update') {
		fn_add_breadcrumb(fn_get_lang_var(($mode == 'add') ? 'new_profile' : 'editing_profile'));
		$uid = $auth['user_id'];
	}

	if (!empty($_SESSION['saved_post_data']['user_data'])) {
		foreach ((array)$_SESSION['saved_post_data'] as $k => $v) {
			$view->assign($k, $v);
		}

		$user_data = $_SESSION['saved_post_data']['user_data'];
		unset($_SESSION['saved_post_data']['user_data']);

	} else {
		if ($mode == 'update') {

			if (!empty($profile_id)) {
				$is_allowed = db_get_field("SELECT user_id FROM ?:user_profiles WHERE user_id = ?i AND profile_id = ?i", $uid, $profile_id);
				if (empty($is_allowed)) {

					return array(CONTROLLER_STATUS_REDIRECT, "profiles.update" . (!empty($_REQUEST['user_id']) ? "?user_id=$_REQUEST[user_id]" : ''));
				}
			}


			if (!empty($profile_id)) {
				$user_data = fn_get_user_info($uid, true, $profile_id);
			} elseif (!empty($_REQUEST['profile']) && $_REQUEST['profile'] == 'new') {
				$user_data = fn_get_user_info($uid, false, $profile_id);
			} else {
				$user_data = fn_get_user_info($uid, true, $profile_id);
			}

			if (empty($user_data)) {
				return array(CONTROLLER_STATUS_NO_PAGE);
			}
		}

		if ($mode == 'add' && !empty($_SESSION['cart']) && !empty($_SESSION['cart']['user_data']) && AREA != 'A') {
			$user_data = $_SESSION['cart']['user_data'];
		}
	}

	$user_type = (!empty($_REQUEST['user_type'])) ? ($_REQUEST['user_type']) : (!empty($user_data['user_type']) ? $user_data['user_type'] : 'C');
	if (AREA == 'A') {
		fn_add_breadcrumb(fn_get_lang_var('users'), "profiles.manage.reset_view");
		fn_add_breadcrumb(fn_get_lang_var('search_results'), "profiles.manage.last_view");
		fn_add_breadcrumb(fn_get_user_type_description($user_type, true), "profiles.manage?user_type=" . $user_type);
	} else {
		Registry::set('navigation.tabs.general', array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		));
		if ($mode == 'update' && Registry::get('settings.General.user_store_cc') == 'Y') {
			Registry::set('navigation.tabs.credit_cards', array (
				'title' => fn_get_lang_var('credit_cards'),
				'js' => true
			));
			$credit_cards = fn_get_static_data_section('C', true, 'credit_card');
			$view->assign('credit_cards', $credit_cards);
			$card_names = array();
			foreach ($credit_cards as $val) {
				$card_names[$val['param']] = $val['descr'];
			}
			$view->assign('card_names', $card_names);
			$view->assign('profile_cards', empty($user_data['credit_cards']) ? array() : unserialize(fn_decrypt_text($user_data['credit_cards'])));
		}
	}
	$usergroups = fn_get_usergroups((!empty($user_data['user_type']) && $user_data['user_type'] == 'A' ? 'F' : 'C'), CART_LANGUAGE);
	if (AREA != 'A' && Registry::get('settings.General.allow_usergroup_signup') != 'Y') {
		$hide_tab = true;
		if (!empty($user_data['usergroups'])) {
			foreach ($user_data['usergroups'] as $_user_group) {
				if ($_user_group['status'] == 'A') {
					$hide_tab = false;
					break;
				}
			}
		}
		if ($hide_tab) {
			$usergroups = array();
		}
	}
	$user_data['user_type'] = empty($user_data['user_type']) ? 'C' : $user_data['user_type'];
	$user_data['user_id'] = empty($user_data['user_id']) ? '0' : $user_data['user_id'];
	if ($mode == 'update' && 
		(
			AREA == 'A' 
			&& 
			(
				($user_data['user_type'] != 'A' && !defined('COMPANY_ID'))
				|| 
				($user_data['user_type'] == 'A' && !defined('COMPANY_ID') && $auth['is_root'] == 'Y' && (!empty($user_data['company_id']) || (empty($user_data['company_id']) && $user_data['is_root'] != 'Y')))
				||
				($user_data['user_type'] == 'A' && defined('COMPANY_ID') && $auth['is_root'] == 'Y' && $user_data['user_id'] != $auth['user_id'] && $user_data['company_id'] == COMPANY_ID)
			) 
			|| 
			AREA != 'A' && $user_data['user_type'] != 'A' && !empty($usergroups)
		)
	) {
		Registry::set('navigation.tabs.usergroups', array (
			'title' => fn_get_lang_var('usergroups'),
			'js' => true
		));
	}
	$view->assign('usergroups', $usergroups);
	$profile_fields = fn_get_profile_fields($user_type);
	$view->assign('user_type', $user_type);
	$view->assign('profile_fields', $profile_fields);
	$view->assign('user_data', $user_data);
	$view->assign('ship_to_another', fn_check_shipping_billing($user_data, $profile_fields));
	$view->assign('titles', fn_get_static_data_section('T'));
	$view->assign('countries', fn_get_countries(CART_LANGUAGE, true));
	$view->assign('states', fn_get_all_states());
	$view->assign('uid', $uid);
	if (Registry::get('settings.General.user_multiple_profiles') == 'Y' && !empty($uid)) {
		$view->assign('user_profiles', fn_get_user_profiles($uid));
	}

// Delete profile
} elseif ($mode == 'delete_profile') {

	if (AREA == 'A' && (fn_is_restricted_admin($_REQUEST) == true || defined('COMPANY_ID'))) {
		return array(CONTROLLER_STATUS_DENIED);
	}

	if (AREA == 'A') {
		$uid = empty($_REQUEST['user_id']) ? $auth['user_id'] : $_REQUEST['user_id'];
	} else {
		$uid = $auth['user_id'];
	}

	$can_delete = db_get_field("SELECT profile_id FROM ?:user_profiles WHERE user_id = ?i AND profile_id = ?i AND profile_type = 'S'", $uid, $_REQUEST['profile_id']);
	if (!empty($can_delete)) {
		db_query("DELETE FROM ?:user_profiles WHERE profile_id = ?i", $_REQUEST['profile_id']);
	}

	return array(CONTROLLER_STATUS_OK, "profiles.update?user_id=" . $uid);

} elseif ($mode == 'delete_card') {

	if (AREA == 'A' && fn_is_restricted_admin($_REQUEST) == true) {
		return array(CONTROLLER_STATUS_DENIED);
	}

	if (!empty($_REQUEST['card_id']) && !empty($_REQUEST['profile_id'])) {
		$cards_data = db_get_field("SELECT credit_cards FROM ?:user_profiles WHERE profile_id = ?i", $_REQUEST['profile_id']);
		if (!empty($cards_data)) {
			$cards = unserialize(fn_decrypt_text($cards_data));

			$is_default = $cards[$_REQUEST['card_id']]['default'];
			unset($cards[$_REQUEST['card_id']]);
			if ($is_default && !empty($cards)) {
				reset($cards);
				$cards[key($cards)]['default'] = true;
			}

			$cards_data = array (
				'credit_cards' => empty($cards) ? '' : fn_encrypt_text(serialize($cards))
			);
			db_query('UPDATE ?:user_profiles SET ?u WHERE profile_id = ?i', $cards_data, $_REQUEST['profile_id']);

			if (AREA == 'A') {
				$uid = empty($_REQUEST['user_id']) ? $auth['user_id'] : $_REQUEST['user_id'];
			} else {
				$uid = $auth['user_id'];
			}
			return array(CONTROLLER_STATUS_OK, "profiles.update?user_id=$uid&profile_id=$_REQUEST[profile_id]");
		}
	}
	exit;
} elseif ($mode == 'request_usergroup') {

	if (AREA == 'A' && fn_is_restricted_admin($_REQUEST) == true) {
		return array(CONTROLLER_STATUS_DENIED);
	}

	$uid = $auth['user_id'];
	if (!empty($uid)) {
		$_data = array(
			'user_id' => $uid,
			'usergroup_id' => $_REQUEST['usergroup_id'],
		);

		if ($_REQUEST['status'] == 'A' || $_REQUEST['status'] == 'P') {
			$_data['status'] = 'F';

		} elseif ($_REQUEST['status'] == 'F' || $_REQUEST['status'] == 'D') {
			$_data['status'] = 'P';
			$usergroup_request = true;
		}

		db_query("REPLACE INTO ?:usergroup_links SET ?u", $_data);

		if (!empty($usergroup_request)) {
			$user_data = fn_get_user_info($uid);

			Registry::get('view_mail')->assign('user_data', $user_data);
			Registry::get('view_mail')->assign('usergroups', fn_get_usergroups('F', Registry::get('settings.Appearance.admin_default_language')));
			Registry::get('view_mail')->assign('usergroup_id', $_REQUEST['usergroup_id']);

			fn_send_mail(Registry::get('settings.Company.company_users_department'), Registry::get('settings.Company.company_users_department'), 'profiles/usergroup_request_subj.tpl', 'profiles/usergroup_request.tpl', '', Registry::get('settings.Appearance.admin_default_language'), $user_data['email']);
		}
	}

	return array(CONTROLLER_STATUS_OK, "profiles.update");
}

?>