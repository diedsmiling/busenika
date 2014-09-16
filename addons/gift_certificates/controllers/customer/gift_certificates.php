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
// $Id: gift_certificates.php 9353 2010-05-04 06:10:09Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'do_verify') {
		return array(CONTROLLER_STATUS_REDIRECT, "gift_certificates.verify?verify_code=$_POST[verify_code]");
	}

	if ($mode == 'add') {

		if (!empty($_REQUEST['gift_cert_data']) && is_array($_REQUEST['gift_cert_data'])) {

			$gift_cert_data = $_REQUEST['gift_cert_data'];

			// Cart is empty, create it
			if (empty($_SESSION['cart'])) {
				fn_clear_cart($_SESSION['cart']);
			}

			if (!empty($_REQUEST['gift_cert_data']['email']) && !fn_validate_email($_REQUEST['gift_cert_data']['email'], true)) {
				if (defined('AJAX_REQUEST')) {
					exit();
				} else {
					return array(CONTROLLER_STATUS_OK, "gift_certificates.add");
				}
			}

			// Gift certificates is empty, create it
			if (empty($_SESSION['cart']['gift_certificates'])) {
				$_SESSION['cart']['gift_certificates'] = array();
			}

			$previous_cart_total = isset($_SESSION['cart']['total']) ? floatval($_SESSION['cart']['total']) : 0;
			list($gift_cert_id, $gift_cert) = fn_add_gift_certificate_to_cart($gift_cert_data, $auth);

			if (!empty($gift_cert_id)) {
				$_SESSION['cart']['gift_certificates'][$gift_cert_id] = $gift_cert;
			}

			fn_save_cart_content($_SESSION['cart'], $auth['user_id']);

			if (defined('AJAX_REQUEST')) {
				fn_calculate_cart_content($_SESSION['cart'], $auth, false, false, 'F', false);

				if ($previous_cart_total == floatval($_SESSION['cart']['total'])) {
					fn_delete_cart_gift_certificate($gift_cert_id);
					fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_failed_gift_certificate_addition'));
					exit;
				}

				$view->assign('cart_amount', $_SESSION['cart']['amount']);
				$view->assign('cart_subtotal', $_SESSION['cart']['subtotal']);

				fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_gift_cert_added_to_cart'));

				$view->display('views/checkout/components/cart_status.tpl');
				exit;
			}
		}
	}


	if ($mode == 'update') {

		if (!empty($_REQUEST['gift_cert_data']) && !empty($_REQUEST['gift_cert_id']) && $_REQUEST['type'] == 'C') {
			fn_delete_cart_gift_certificate($_SESSION['cart'], $_REQUEST['gift_cert_id']);

			list($gift_cert_id, $gift_cert) = fn_add_gift_certificate_to_cart($_REQUEST['gift_cert_data'], $auth);

			if (!empty($gift_cert_id)) {
				$_SESSION['cart']['gift_certificates'][$gift_cert_id] = $gift_cert;
			}

			fn_save_cart_content($_SESSION['cart'], $auth['user_id'], $_REQUEST['type']);
		}
	}

	if ($mode == 'preview') {
		if (!empty($_REQUEST['gift_cert_data'])) {
			fn_correct_gift_certificate($_REQUEST['gift_cert_data']);
			fn_show_postal_card($_REQUEST['gift_cert_data']);
			exit;
		}
	}

	return array(CONTROLLER_STATUS_OK, "checkout.cart");
}

if ($mode == 'verify') {

	fn_add_breadcrumb(fn_get_lang_var('gift_certificate_verification'));

	$verify_id = db_get_field("SELECT gift_cert_id FROM ?:gift_certificates WHERE gift_cert_code = ?s", $_REQUEST['verify_code']);
	if (!empty($verify_id)) {
		Registry::set('navigation.tabs', array (
			'detailed' => array (
				'title' => fn_get_lang_var('detailed_info'),
				'js' => true
			),
			'log' => array (
				'title' => fn_get_lang_var('history'),
				'js' => true
			)
		));

		list($log, $sort_order, $sort_by) = fn_get_gift_certificate_log($verify_id, $_REQUEST);

		$view->assign('log', $log);
		$view->assign('sort_order', $sort_order);
		$view->assign('sort_by', $sort_by);

		$verify_data = fn_get_gift_certificate_info($verify_id, 'B');
		if (false != ($last_item = reset($log))) {
			$verify_data['amount'] = $last_item['debit'];
			$verify_data['products'] = $last_item['debit_products'];
		}

		$view->assign('verify_data', $verify_data);
	}

} elseif ($mode == 'add') {

	fn_add_breadcrumb(fn_get_lang_var('gift_certificates'));

	$view->assign('templates', fn_get_gift_certificate_templates());
	$view->assign('states', fn_get_all_states());
	$view->assign('countries', fn_get_countries(CART_LANGUAGE, true));
	$view->assign('amount_variants', fn_get_gift_certificate_amount_variants());

} elseif ($mode == 'update') {

	if (!empty($_REQUEST['gift_cert_id']) && !isset($_SESSION['cart']['gift_certificates'][$_REQUEST['gift_cert_id']])) {
		return array(CONTROLLER_STATUS_REDIRECT, "gift_certificates.add");
	}

	fn_add_breadcrumb(fn_get_lang_var('gift_certificates'));

	if (!empty($_REQUEST['gift_cert_id'])) {
		$gift_cert_data = fn_get_gift_certificate_info($_REQUEST['gift_cert_id'], 'C');

		if (!empty($gift_cert_data['extra']['exclude_from_calculate'])) {
			return array(CONTROLLER_STATUS_NO_PAGE);
		}

		$view->assign('gift_cert_data', $gift_cert_data);
		$view->assign('gift_cert_id', $_REQUEST['gift_cert_id']);
	}

	$view->assign('templates', fn_get_gift_certificate_templates());
	$view->assign('states', fn_get_all_states());
	$view->assign('countries', fn_get_countries(CART_LANGUAGE, true));
	$view->assign('amount_variants', fn_get_gift_certificate_amount_variants());
	$view->assign('type', 'C');

} elseif ($mode == 'delete') {

	if (isset($_REQUEST['gift_cert_id'])) {
		fn_delete_cart_gift_certificate($_SESSION['cart'], $_REQUEST['gift_cert_id']);

		return array(CONTROLLER_STATUS_REDIRECT, "checkout.$_REQUEST[redirect_mode]");
	}
}

?>