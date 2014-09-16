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
// $Id: order_management.post.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$cart = & $_SESSION['cart'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//
	// Update products
	//
	if ($mode == 'update') {
		if (!empty($cart['products'])) {
			foreach ($cart['products'] as $cart_id => $v) {
				if (isset($v['extra']['parent']['certificate'])) {
					$gift_certificates[$v['extra']['parent']['certificate']]['products'][$cart_id] = array(
						'product_id' => $v['product_id'],
						'product_options' => empty($v['extra']['product_options']) ? array() : $v['extra']['product_options'],
						'amount' => $v['amount'],
					);
				}
			}
			
			if (!empty($gift_certificates)) {
				foreach ($gift_certificates as $cert_id => $cert_data) {
					$cart['gift_certificates'][$cert_id]['products'] = $cert_data['products'];
				}
			}
		}
	}
	
	//
	// Update totals
	//
	if ($mode == 'update_totals') {

		if (!empty($_REQUEST['gift_cert_code'])) {
			if (fn_check_gift_certificate_code($_REQUEST['gift_cert_code'], true) == true) {
				if (!isset($cart['use_gift_certificates'][$_REQUEST['gift_cert_code']])) {
					$cart['use_gift_certificates'][$_REQUEST['gift_cert_code']] = 'Y';
				}
			}
		}
	}

	return;
}

//
// Delete attached certificate
//
if ($mode == 'delete_use_certificate') {
	fn_delete_gift_certificate_in_use($_REQUEST['gift_cert_code'], $cart);

	return array(CONTROLLER_STATUS_REDIRECT, "order_management.totals");

//
// Display totals
//
} elseif ($mode == 'totals') {
	$gift_certificate_condition = (!empty($cart['use_gift_certificates'])) ? db_quote(" AND gift_cert_code NOT IN (?a)", array_keys($cart['use_gift_certificates'])) : '';
	$view->assign('gift_certificates', db_get_fields("SELECT gift_cert_code FROM ?:gift_certificates WHERE status = 'A' $gift_certificate_condition"));

//
// Delete certificate from the cart
//
} elseif ($mode == 'delete_certificate') {
	if (!empty($_REQUEST['gift_cert_cart_id'])) {
		fn_delete_cart_gift_certificate($cart, $_REQUEST['gift_cert_cart_id']);
		
		return array(CONTROLLER_STATUS_REDIRECT, "order_management.totals");
	}
}

?>