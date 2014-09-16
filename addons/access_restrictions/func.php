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
// $Id: func.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_validate_ip($ip, $show_error = false)
{
	if (empty($ip)) {
		return false;
	}

	$_ip = ip2long($ip);
	if (!empty($_ip) && $_ip != ip2long('')) {
		return true;
	} elseif ($show_error) {
		fn_set_notification('E', fn_get_lang_var('error'), str_replace('[ip]', $ip, fn_get_lang_var('text_not_valid_ip')));
	}

	return false;
}

function fn_validate_email_name($email, $show_error = false)
{
	if (empty($email)) {
		return false;
	}

	$email_name = (strpos($email, '@')) ? substr($email, 0, strpos($email, '@')) : $email;
	if (preg_match('/^([?*-\d\w][?*-.\d\w]*)$/', $email_name)) {
		return true;
	} elseif ($show_error) {
		fn_set_notification('E', fn_get_lang_var('error'), str_replace('[email]', $email, fn_get_lang_var('text_not_valid_email')));
	}

	return false;

}

function fn_validate_domain_name($name, $show_error = false)
{
	if (empty($name)) {
		return false;
	}

	if (preg_match('/^([?*-a-z0-9]+\.)+([*?a-z]{2,4}|[*]+)$/i', $name) || fn_validate_ip($name)) {
		return true;
	} elseif ($show_error) {
		fn_set_notification('E', fn_get_lang_var('error'), str_replace('[domain]', $name, fn_get_lang_var('text_not_valid_domain')));
	}

	return false;
}

// Please note this function only validate the cc number LENGTH
function fn_validate_cc_number($number, $show_error = false)
{
	if (empty($number)) {
		return false;
	}

	if (preg_match('/^([?0-9]{13,19}|[?*\d]+)$/', $number)) {
		return true;
	} elseif ($show_error) {
		fn_set_notification('E', fn_get_lang_var('error'), str_replace('[cc_number]', $number, fn_get_lang_var('text_not_valid_cc_number')));
	}
}

function fn_card_number_is_blocked($number)
{

	$restricted = db_get_field("SELECT COUNT(*) FROM ?:access_restriction WHERE type = 'cc' AND status = 'A' AND ?s LIKE REPLACE(REPLACE(value, '?', '_'), '*', '%')", $number);

	return !empty($restricted);
}

function fn_email_is_blocked($user_data, $reset_email = false)
{
	$auth = & $_SESSION['auth'];

	$email = trim($user_data['email']);

	if (!fn_validate_email($email, false)) {
		return false;
	}

	$restricted = db_get_field("SELECT COUNT(*) FROM ?:access_restriction WHERE type IN ('ed', 'es') AND status = 'A' AND ?s LIKE REPLACE(REPLACE(REPLACE(value, '_', '\_'), '?', '_'), '*', '%')", $email);

	if (!empty($restricted)) {
		if ($reset_email && $auth) {
			$uid = (AREA == 'C' || AREA == 'P' || empty($_REQUEST['user_id'])) ? $auth['user_id'] : $_REQUEST['user_id'];
			$_POST['user_data']['email'] = db_get_field("SELECT email FROM ?:users WHERE user_id = ?i", $uid);
		}

		fn_save_post_data();
		fn_set_notification('E', fn_get_lang_var('error'), str_replace('[email]', $user_data['email'], fn_get_lang_var('text_email_is_blocked')));

		return true;
	}

	return false;
}

function fn_domain_is_blocked($domain)
{
	$restricted = db_get_field("SELECT COUNT(*) FROM ?:access_restriction WHERE type='d' AND status = 'A' AND ?s LIKE REPLACE(REPLACE(REPLACE(value, '_', '\_'), '?', '_'), '*', '%')", $domain);

	if (!empty($restricted)) {
		die(fn_get_lang_var('text_ips_denied'));
	}

	return false;
}

function fn_access_restrictions_redirect()
{
	$auth = & $_SESSION['auth'];

	$ip = fn_get_ip(true);

	if (MODE == 'login' && CONTROLLER == 'auth' && empty($auth['user_id'])) {
		$ip_exist = db_get_row("SELECT * FROM ?:access_restriction_block WHERE ip = ?i", $ip['host']);
		$login_interval = (AREA == 'A') ? Registry::get('addons.access_restrictions.login_intervals') : Registry::get('addons.access_restrictions.login_intervals_customer');
		if ($ip_exist && $ip_exist['expires'] > TIME) {
			$ip_exist['tries'] ++;
			$ip_exist['expires'] += $login_interval;
			db_query('UPDATE ?:access_restriction_block SET ?u WHERE ip = ?i', $ip_exist, $ip['host']);
		} else {
			$ip_data = array(
				'ip' => $ip['host'],
				'tries' => 1,
				'timestamp' => TIME,
				'expires' => (TIME + $login_interval)
			);
			db_query("REPLACE INTO ?:access_restriction_block ?e", $ip_data);
		}
	} elseif (MODE == 'login' && CONTROLLER == 'auth' && !empty($auth['user_id'])) {
		db_query("DELETE FROM ?:access_restriction_block WHERE ip = ?i", $ip['host']);
	}

	return true;
}

function fn_access_restrictions_user_init(&$auth, &$user_info)
{
	$iplong = fn_get_ip(true);
	$acc_r = Registry::get('addons.access_restrictions');

	// Get block ip settings, if it should be blocked then add it to the restricted ips
	if ((AREA == 'A' && $acc_r['unsuccessful_attempts_login'] == 'Y') || (AREA != 'A' && $acc_r['unsuccessful_attempts_login_customer'] == 'Y')) {
		$block = db_get_row("SELECT * FROM ?:access_restriction_block WHERE ip >= ?i", $iplong['host']);
		$failed_atempts = (AREA == 'A') ? $acc_r['number_unsuccessful_attempts'] : $acc_r['number_unsuccessful_attempts_customer'];
		if (!empty($block) && $block['tries'] >= $failed_atempts) {
			$time_block = (AREA == 'A') ? $acc_r['time_block'] : $acc_r['time_block_customer'];
			$restrict_ip = array(
				'ip_from' => $iplong['host'],
				'ip_to' => $iplong['host'],
				'type' => ((AREA == 'A') ? 'aab' : 'ipb'),
				'timestamp' => TIME,
				'expires' => (TIME + round($time_block * 3600)),
				'status' => 'A'
			);
			$__data['item_id'] = db_query("REPLACE INTO ?:access_restriction ?e", $restrict_ip);
			$__data['type'] = ((AREA == 'A') ? 'aab' : 'ipb');

			foreach ((array)Registry::get('languages') as $__data['lang_code'] => $v) {
				$__data['reason'] = str_replace("[number]", $failed_atempts, fn_get_lang_var('text_ip_blocked_failed_login', $__data['lang_code']));
				db_query("REPLACE INTO ?:access_restriction_reason_descriptions ?e", $__data);
			}

			db_query("DELETE FROM ?:access_restriction_block WHERE ip = ?i", $block['ip']);
		}
	}

	db_query("DELETE FROM ?:access_restriction_block WHERE expires < ?i", TIME);
	db_query("DELETE FROM ?:access_restriction WHERE (type = 'ipb' OR type = 'aab') AND expires < ?i", TIME);

	$ar_type = (AREA != 'A') ? "a.type IN ('ips', 'ipr', 'ipb')" : "a.type IN ('aas', 'aar', 'aab')";
	$restricted = db_get_row("SELECT a.item_id, b.reason FROM ?:access_restriction as a LEFT JOIN ?:access_restriction_reason_descriptions as b ON a.item_id = b.item_id AND a.type = b.type AND lang_code = ?s WHERE ip_from <= ?i AND ip_to >= ?i AND $ar_type AND status = 'A'", CART_LANGUAGE, $iplong['host'], $iplong['host']);

	if ($restricted && (AREA != 'A' || $acc_r['admin_reverse_ip_access'] != 'Y')) {
		die((!empty($restricted['reason']) ? $restricted['reason'] : fn_get_lang_var('text_ip_is_blocked')));
	} elseif (!$restricted && $acc_r['admin_reverse_ip_access'] == 'Y' && AREA == 'A') {
		die(fn_get_lang_var('text_ips_denied'));
	}

	// Check for domain restrictions

	if (empty($_SESSION['access_domain'])) {
		$ip = fn_get_ip();
		fn_domain_is_blocked(gethostbyaddr($ip['host']));
		$_SESSION['access_domain'] = gethostbyaddr($ip['host']);
	}
}

?>