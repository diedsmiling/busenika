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
// $Id: paypal.php 10378 2010-08-06 12:36:48Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

// Return from paypal website
if (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'notify' && !empty($_REQUEST['order_id'])) {
		
		if (fn_check_payment_script('paypal.php', $_REQUEST['order_id'], $processor_data)) {

			$pp_response = array();
			
			$order_info = fn_get_order_info($_REQUEST['order_id']);
			
			$pp_mc_gross = !empty($_REQUEST['mc_gross']) ? $_REQUEST['mc_gross'] : 0;

			if (fn_format_price($pp_mc_gross) != fn_format_price($order_info['total'])) {
				$pp_response['order_status'] = 'F';
				$pp_response['reason_text'] = fn_get_lang_var('order_total_not_correct');
				$pp_response['transaction_id'] = @$_REQUEST['txn_id'];
			} elseif (stristr($_REQUEST['payment_status'], 'Completed')) {
				$params = $processor_data['params'];
				$paypal_host = ($params['mode'] == 'test' ? "www.sandbox.paypal.com" : "www.paypal.com");
				$post_data = array();
				$paypal_post = $_REQUEST;
				unset($paypal_post['dispatch']);
	
				$paypal_post["cmd"] = "_notify-validate";
				foreach ($paypal_post as $k => $v) {
					$post_data[] = "$k=$v";
				}
	
				list($headers, $result) = fn_https_request('POST', "https://$paypal_host:443/cgi-bin/webscr", $post_data);
	
				if (stristr($result, 'VERIFIED')) {
					$pp_response['order_status'] = 'P';
					$pp_response['reason_text'] = '';
					$pp_response['transaction_id'] = @$_REQUEST['txn_id'];
				} elseif (stristr($result, 'INVALID')) {
					$pp_response['order_status'] = 'D';
					$pp_response['reason_text'] = '';
					$pp_response['transaction_id'] = @$_REQUEST['txn_id'];
				} else {
					$pp_response['order_status'] = 'F';
					$pp_response['reason_text'] = '';
					$pp_response['transaction_id'] = @$_REQUEST['txn_id'];
				}
			} elseif (stristr($_REQUEST['payment_status'], 'Pending')) {
					$pp_response['order_status'] = 'O';
					$pp_response['reason_text'] = '';
					$pp_response['transaction_id'] = @$_REQUEST['txn_id'];
	
			} elseif (stristr($_REQUEST['payment_status'], 'Refunded')) {
					$_order = db_get_row("SELECT status, total FROM ?:orders WHERE order_id = ?i", $_REQUEST['order_id']);
	
					$pp_response['order_status'] = (floatval($_order['total']) - abs(floatval($_REQUEST['payment_gross'])) == 0) ? 'I' : $_order['status'];
					$pp_response['reason_text'] = '';
					$pp_response['transaction_id'] = @$_REQUEST['txn_id'];
	
			} else {
					$pp_response['order_status'] = 'D';
					$pp_response['reason_text'] = '';
					$pp_response['transaction_id'] = @$_REQUEST['txn_id'];
			}
	
			if (!empty($_REQUEST['payer_email'])) {
				$pp_response['customer_email'] = $_REQUEST['payer_email'];
			}
			if (!empty($_REQUEST['payer_id'])) {
				$pp_response['client_id'] = $_REQUEST['payer_id'];
			}
			if (!empty($_REQUEST['memo'])) {
				$pp_response['customer_notes'] = $_REQUEST['memo'];
			}
			fn_finish_payment($_REQUEST['order_id'], $pp_response);
		}
		exit;

	} elseif ($mode == 'return') {
		if (fn_check_payment_script('paypal.php', $_REQUEST['order_id'])) {
			$order_info = fn_get_order_info($_REQUEST['order_id'], true);
			if ($order_info['status'] == 'N') {
				fn_change_order_status($_REQUEST['order_id'], 'O', '', false);
			}
		}
		fn_order_placement_routines($_REQUEST['order_id'], false);

	} elseif ($mode == 'cancel') {
		$order_info = fn_get_order_info($_REQUEST['order_id']);

		$pp_response['order_status'] = 'N';
		$pp_response["reason_text"] = fn_get_lang_var('text_transaction_cancelled');

		if (!empty($_REQUEST['payer_email'])) {
			$pp_response['customer_email'] = $_REQUEST['payer_email'];	
		}
		if (!empty($_REQUEST['payer_id'])) {
			$pp_response['client_id'] = $_REQUEST['payer_id'];	
		}
		if (!empty($_REQUEST['memo'])) {
			$pp_response['customer_notes'] = $_REQUEST['memo'];
		}
		fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
		fn_order_placement_routines($_REQUEST['order_id']);
	}

} else {

	$paypal_account = $processor_data['params']['account'];

	$current_location = Registry::get('config.current_location');

	if ($processor_data['params']['mode'] == 'test') {
		$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	} else {
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
	}

	$paypal_currency = $processor_data['params']['currency'];
	$paypal_item_name = $processor_data['params']['item_name'];
	//Order Total
	$paypal_total = fn_format_price($order_info['total'] - fn_order_shipping_cost($order_info));
	$paypal_shipping = fn_order_shipping_cost($order_info);
	$paypal_order_id = $processor_data['params']['order_prefix'].(($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id);

	$_phone = preg_replace('/[^\d]/', '', $order_info['phone']);
	$_ph_a = $_ph_b = $_ph_c = '';
	
	if ($order_info['b_country'] == 'US') {
		$_phone = substr($_phone, -10);
		$_ph_a = substr($_phone, 0, 3);
		$_ph_b = substr($_phone, 3, 3);
		$_ph_c = substr($_phone, 6, 4);
	} elseif ($order_info['b_country'] == 'GB') {
		if ((strlen($_phone) == 11) && in_array(substr($_phone, 0, 2), array('01', '02', '07', '08'))) {
			$_ph_a = '44';
			$_ph_b = substr($_phone, 1);
		} elseif (substr($_phone, 0, 2) == '44') {
			$_ph_a = '44';
			$_ph_b = substr($_phone, 2);
		} else {
			$_ph_a = '44';
			$_ph_b = $_phone;
		}
	} elseif ($order_info['b_country'] == 'AU') {
		if ((strlen($_phone) == 10) && $_phone[0] == '0') {
			$_ph_a = '61';
			$_ph_b = substr($_phone, 1);
		} elseif (substr($_phone, 0, 2) == '61') {
			$_ph_a = '61';
			$_ph_b = substr($_phone, 2);
		} else {
			$_ph_a = '61';
			$_ph_b = $_phone;
		}
	} else {
		$_ph_a = substr($_phone, 0, 3);
		$_ph_b = substr($_phone, 3);
	}
	
	// US states
	if ($order_info['b_country'] == 'US') {
		$_b_state = $order_info['b_state'];
	// all other states
	} else {
		$_b_state = fn_get_state_name($order_info['b_state'], $order_info['b_country']);
	}

	$msg = fn_get_lang_var('text_cc_processor_connection');
	$msg = str_replace('[processor]', 'PayPal', $msg);
	echo <<<EOT
	<html>
	<body onLoad="document.paypal_form.submit();">
	<form action="{$paypal_url}" method="post" name="paypal_form">
	<input type=hidden name="charset" value="utf-8">
	<input type=hidden name="cmd" value="_cart">
	<input type=hidden name="custom" value="$order_id">
	<input type=hidden name="invoice" value="$paypal_order_id">
	<input type=hidden name="redirect_cmd" value="_xclick">
	<input type=hidden name="rm" value="2">
	<input type=hidden name="email" value="{$order_info['email']}">
	<input type=hidden name="first_name" value="{$order_info['b_firstname']}">
	<input type=hidden name="last_name" value="{$order_info['b_lastname']}">
	<input type=hidden name="address1" value="{$order_info['b_address']}">
	<input type=hidden name="address2" value="{$order_info['b_address_2']}">
	<input type=hidden name="country" value="{$order_info['b_country']}">
	<input type=hidden name="city" value="{$order_info['b_city']}">
	<input type=hidden name="state" value="{$_b_state}">
	<input type=hidden name="zip" value="{$order_info['b_zipcode']}">
	<input type=hidden name="day_phone_a" value="{$_ph_a}">
	<input type=hidden name="day_phone_b" value="{$_ph_b}">
	<input type=hidden name="day_phone_c" value="{$_ph_c}">
	<input type=hidden name="night_phone_a" value="{$_ph_a}">
	<input type=hidden name="night_phone_b" value="{$_ph_b}">
	<input type=hidden name="night_phone_c" value="{$_ph_c}">
	<input type=hidden name="business" value="{$paypal_account}">
	<input type=hidden name="item_name" value="{$paypal_item_name}">
	<input type=hidden name="amount" value="{$paypal_total}">
	<input type=hidden name="upload" value="1">
	<input type=hidden name="handling_cart" value="{$paypal_shipping}">
	<input type=hidden name="currency_code" value="{$paypal_currency}">
	<input type=hidden name="return" value="$current_location/$index_script?dispatch=payment_notification.return&payment=paypal&order_id=$order_id">
	<input type=hidden name="cancel_return" value="$current_location/$index_script?dispatch=payment_notification.cancel&payment=paypal&order_id=$order_id" />
	<input type=hidden name="notify_url" value="$current_location/$index_script?dispatch=payment_notification.notify&payment=paypal&order_id=$order_id">
	<input type=hidden name="bn" value="ST_ShoppingCart_Upload_US">
EOT;

$i = 1;
// Products
if (empty($order_info['use_gift_certificates']) && !floatval($order_info['subtotal_discount'])) {
	if (!empty($order_info['items'])) {
		foreach ($order_info['items'] as $k => $v) {
			$suffix = '_'.($i++);
			$v['product'] = htmlspecialchars($v['product']);
			$v['price'] = fn_format_price(($v['subtotal'] - fn_external_discounts($v)) / $v['amount']);
			echo <<<EOT
			<input type="hidden" name="item_name{$suffix}" value="{$v['product']}" />
			<input type="hidden" name="amount{$suffix}" value="{$v['price']}" />
			<input type="hidden" name="quantity{$suffix}" value="{$v['amount']}" />
EOT;
			if (!empty($v['product_options'])) {
				foreach ($v['product_options'] as $_k => $_v) {
					$_v['option_name'] = htmlspecialchars($_v['option_name']);
					$_v['variant_name'] = htmlspecialchars($_v['variant_name']);
					echo <<<EOT
						<input type="hidden" name="on{$_k}{$suffix}" value="{$_v['option_name']}" />
						<input type="hidden" name="os{$_k}{$suffix}" value="{$_v['variant_name']}" />
EOT;
				}
			}
		}
	}
	
	if (!empty($order_info['taxes']) && Registry::get('settings.General.tax_calculation') == 'subtotal') {
		foreach ($order_info['taxes'] as $tax_id => $tax) {
			if ($tax['price_includes_tax'] == 'Y') {
				continue;
			}
			$suffix = '_' . ($i++);
			$item_name = htmlspecialchars($tax['description']);
			$item_price = fn_format_price($tax['tax_subtotal']);
			echo <<<EOT
			<input type="hidden" name="item_name{$suffix}" value="{$item_name}" />
			<input type="hidden" name="amount{$suffix}" value="{$item_price}" />
			<input type="hidden" name="quantity{$suffix}" value="1" />
EOT;
		}
	}

	// Gift Certificates
	if (!empty($order_info['gift_certificates'])) {
		foreach ($order_info['gift_certificates'] as $k => $v) {
			$suffix = '_'.($i++);
			$v['gift_cert_code'] = htmlspecialchars($v['gift_cert_code']);
			$v['amount'] = (!empty($v['extra']['exclude_from_calculate'])) ? 0 : $v['amount'];
			echo <<<EOT
			<input type="hidden" name="item_name{$suffix}" value="{$v['gift_cert_code']}" />
			<input type="hidden" name="amount{$suffix}" value="{$v['amount']}" />
			<input type="hidden" name="quantity{$suffix}" value="1" />
EOT;
		}
	}

	// Payment surcharge
	if (floatval($order_info['payment_surcharge'])) {
		$suffix = '_' . ($i++);
		$name = fn_get_lang_var('surcharge');

		echo <<<EOT
		<input type="hidden" name="item_name{$suffix}" value="{$name}" />
		<input type="hidden" name="amount{$suffix}" value="{$order_info['payment_surcharge']}" />
		<input type="hidden" name="quantity{$suffix}" value="1" />
EOT;
	}
} else {
	$total_description = fn_get_lang_var('total_product_cost');
	echo <<<EOT
	<input type="hidden" name="item_name_1" value="{$total_description}" />
	<input type="hidden" name="amount_1" value="{$paypal_total}" />
	<input type="hidden" name="quantity_1" value="1" />
EOT;
}


	echo <<<EOT
	</form>
	<div align=center>{$msg}</div>
	</body>
	</html>
EOT;

	fn_flush();
}
exit;
?>