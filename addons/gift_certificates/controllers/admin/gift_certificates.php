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
// $Id: gift_certificates.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

	// Define trusted variables that shouldn't be stripped
	fn_trusted_vars('gift_cert_data');

	if ($mode == 'add') {

		if (!empty($_REQUEST['gift_cert_data'])) {
			do {
				$code = fn_generate_gift_certificate_code();
			} while (true == fn_check_gift_certificate_code($code));

			$gift_cert_data = $_REQUEST['gift_cert_data'];
			fn_correct_gift_certificate($gift_cert_data);

			$_data = fn_check_table_fields($gift_cert_data, 'gift_certificates');
			$_data = fn_array_merge($_data, array('gift_cert_code' => $code, 'timestamp' => TIME));
			if (!empty($_data['products'])) {
				$_data['products'] = serialize($_data['products']);
			}
			
			$gift_cert_id = db_query("INSERT INTO ?:gift_certificates ?e", $_data);
			
			$gift_cert_data = fn_get_gift_certificate_info($gift_cert_id);
			fn_gift_certificate_notification($gift_cert_data, fn_get_notification_rules($_REQUEST));

			$suffix = ".update?gift_cert_id=$gift_cert_id";
		}
	}

	if ($mode == 'update') {

		if (!empty($_REQUEST['gift_cert_data'])) {
			$gift_cert_data = $_REQUEST['gift_cert_data'];
			fn_correct_gift_certificate($gift_cert_data);

			if (!isset($gift_cert_data['products'])) {
				$gift_cert_data['products'] = array();
			}

			// Change certfificate status
			fn_change_gift_certificate_status($_REQUEST['gift_cert_id'], $gift_cert_data['status'], $_REQUEST['certificate_status'], fn_get_notification_rules(array(), false));

			//if difference then add line in log
			$debit_info = db_get_row("SELECT debit AS amount, debit_products AS products FROM ?:gift_certificates_log WHERE gift_cert_id = ?i ORDER BY timestamp DESC", $_REQUEST['gift_cert_id']);

			if (empty($debit_info)) {
				$debit_info = db_get_row("SELECT amount, products FROM ?:gift_certificates WHERE gift_cert_id = ?i", $_REQUEST['gift_cert_id']);
			}

			$is_diff = (($gift_cert_data['amount'] - $debit_info['amount'] != 0) || (md5(serialize($gift_cert_data['products'])) != md5($debit_info['products']))) ? 'Y' : 'N';
			if ($is_diff == 'Y') {
				$_info = array(
					'amount' => $gift_cert_data['amount'],
					'products' => serialize($gift_cert_data['products'])
				);
				fn_add_gift_certificate_log_record($_REQUEST['gift_cert_id'], $debit_info, $_info);
			}

			//Update certificate data
			$_data = $gift_cert_data;
			$_data['products'] = (!empty($_data['products'])) ? serialize($_data['products']) : '';
			db_query("UPDATE ?:gift_certificates SET ?u WHERE gift_cert_id = ?i", $_data, $_REQUEST['gift_cert_id']);

			$gift_cert_data['gift_cert_id'] = $_REQUEST['gift_cert_id'];
			fn_gift_certificate_notification($gift_cert_data, fn_get_notification_rules($_REQUEST));

			$suffix = ".update?gift_cert_id=$_REQUEST[gift_cert_id]";
		}
	}

	if ($mode == 'preview') {

		if (!empty($_REQUEST['gift_cert_data'])) {
			fn_show_postal_card($_REQUEST['gift_cert_data']);
			exit;
		}
	}

	if ($mode == 'delete') {
		if (!empty($_REQUEST['gift_cert_ids'])) {
			foreach ($_REQUEST['gift_cert_ids'] as $v) {
				fn_delete_gift_certificate($v);
			}
		}
	}

	if ($mode == 'update_certificate_statuses' && is_array($_REQUEST['certificate_statuses'])) {
		foreach ($_REQUEST['certificate_statuses'] as $k => $v) {
			if ($_REQUEST['origin_statuses'][$k] != $v) {
				fn_change_gift_certificate_status($k, $v, $_REQUEST['origin_statuses'][$k], fn_get_notification_rules($_REQUEST)); // @GIFT_CERT_ID, @TO, @FROM
			}
		}
	}



	return array(CONTROLLER_STATUS_OK, !empty($_REQUEST['redirect_url']) ? $_REQUEST['redirect_url'] : "gift_certificates$suffix");
}


if ($mode == 'add') {

	fn_add_breadcrumb(fn_get_lang_var('gift_certificates'), "gift_certificates.manage");

	if (!empty($_REQUEST['user_id'])) {
		$user_data = fn_get_user_info($_REQUEST['user_id']);
		$gift_cert_data = array(
			'send_via'		 => 'E',
			'recipient' 	 => "$user_data[firstname] $user_data[lastname]",
			'sender' 		 => Registry::get('settings.Company.company_name'),
			'email' 		 => $user_data['email'],
			'address' 		 => $user_data['s_address'],
			'address_2' 	 => $user_data['s_address_2'],
			'city' 	 		 => $user_data['s_city'],
			'country' 		 => $user_data['s_country'],
			'state' 		 => $user_data['s_state'],
			'zipcode' 		 => $user_data['s_zipcode'],
			'phone' 		 => $user_data['phone']
		);
		$view->assign('gift_cert_data', $gift_cert_data);
	}

	$view->assign('templates', fn_get_gift_certificate_templates());
	$view->assign('states', fn_get_all_states());
	$view->assign('countries', fn_get_countries(CART_LANGUAGE, true));
	$view->assign('amount_variants', fn_get_gift_certificate_amount_variants());

} elseif ($mode == 'update') {

	fn_add_breadcrumb(fn_get_lang_var('gift_certificates'), "gift_certificates.manage.reset_view");
	fn_add_breadcrumb(fn_get_lang_var('search_results'), "gift_certificates.manage.last_view");

	$gift_cert_id = intval($_REQUEST['gift_cert_id']);
	$gift_cert_data = fn_get_gift_certificate_info($gift_cert_id);

	if (empty($gift_cert_data)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	// [Page sections]
	Registry::set('navigation.tabs', array (
		'detailed' => array (
			'title' => fn_get_lang_var('detailed_info'),
			'js' => true
		),
		'log' => array (
			'title' => fn_get_lang_var('history'),
			'js' => true
		),
	));
	// [/Page sections]

	list($log, $sort_order, $sort_by) = fn_get_gift_certificate_log($gift_cert_id, $_REQUEST);

	$view->assign('log', $log);
	$view->assign('sort_order', $sort_order);
	$view->assign('sort_by', $sort_by);

	if (false != ($last_item = reset($log))) {
		$gift_cert_data['amount'] = $last_item['debit'];
		$gift_cert_data['products'] = $last_item['debit_products'];
	}

	$view->assign('templates', fn_get_gift_certificate_templates());
	$view->assign('states', fn_get_all_states());
	$view->assign('countries', fn_get_countries(CART_LANGUAGE, true));
	$view->assign('amount_variants', fn_get_gift_certificate_amount_variants());

	$view->assign('gift_cert_data', $gift_cert_data);

} elseif ($mode == 'manage') {

	list($gift_certificates, $search) = fn_get_gift_certificates($_REQUEST);

	$view->assign('gift_certificates', $gift_certificates);
	$view->assign('search', $search);

	fn_gift_certificates_generate_sections('manage');

} elseif ($mode == 'delete') {

	if (!empty($_REQUEST['gift_cert_id'])) {
		$result = fn_delete_gift_certificate($_REQUEST['gift_cert_id'], @$_REQUEST['extra']);

		return array(CONTROLLER_STATUS_REDIRECT, !empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : "gift_certificates." . ($result ? "manage" : ("update?gift_cert_id=" . $_REQUEST['gift_cert_id'])));
	}

} elseif ($mode == 'update_status') {
	$old_status = db_get_field("SELECT status FROM ?:gift_certificates WHERE gift_cert_id = ?i", $_REQUEST['id']);
	if (fn_change_gift_certificate_status($_REQUEST['id'], $_REQUEST['status'], '', fn_get_notification_rules($_REQUEST))) {
		fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('status_changed'));
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('error_status_not_changed'));
		$ajax->assign('return_status', $old_status);
	}
	exit;
}

?>