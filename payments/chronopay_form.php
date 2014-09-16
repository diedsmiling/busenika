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
// $Id: chronopay_form.php 10229 2010-07-27 14:21:39Z 2tl $
//
if ( !defined('AREA') ) {
	if (!empty($_REQUEST['cs1'])) {
		// Settle data is received
		DEFINE ('AREA', 'C');
		DEFINE ('AREA_NAME' ,'customer');
		require './../prepare.php';
		require './../init.php';

		$order_id = $_REQUEST['cs1'];
		$order_info = fn_get_order_info($order_id);
		$processor_data = $order_info['payment_method'];

		$inner_sign = md5($processor_data['params']['sharedsec'] . $_REQUEST['customer_id'] . $_REQUEST['transaction_id'] . $_REQUEST['transaction_type'] . $order_info['total']);
		if ($_REQUEST['sign'] == $inner_sign) {
			fwrite($fp, "\nOK");
			$pp_response['order_status'] = 'P';
			$pp_response["reason_text"] = 'Approved; Customer ID: ' . $_REQUEST['customer_id'];
			$pp_response["transaction_id"] = $_REQUEST['transaction_id'];

			if (fn_check_payment_script('chronopay_form.php', $order_id)) {
				fn_finish_payment($order_id, $pp_response);
			}
		}
		exit;
	} else {
		die('Access denied');
	}
}

if (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'notify') {
		$order_id = (int) $_REQUEST['order_id'];
		$order_info = fn_get_order_info($order_id);
		$processor_data = $order_info['payment_method'];
		
		// We are trying to avoid mess with declined and success urls
		$sign = md5($processor_data['params']['product_id'] . '-' . $order_info['total'] . '-' . $processor_data['params']['sharedsec']);

		// Because the callback comes only after return we have to make sure that this redirect is successful
		if (in_array($order_info['status'], array('N', 'D')) && (empty($_REQUEST['sign']) || $sign != $_REQUEST['sign'])) {
			$pp_response['order_status'] = 'D';
			$pp_response["reason_text"] = fn_get_lang_var('text_transaction_declined');
			fn_finish_payment($order_id, $pp_response, false);
		} else {
			// Set open status until callback from chronopay service is recieved
			fn_change_order_status($order_id, 'O', $order_info['status'], false);
		}
		fn_order_placement_routines($order_id);
	}

} else {
	if (!defined('AREA') ) { die('Access denied'); }

	$post_url = Registry::get('config.current_location') . '/payments/chronopay_form.php';
	$return_url = Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.notify&payment=chronopay_form&order_id=$order_id";

	$country = db_get_field("SELECT code_A3 FROM ?:countries WHERE code = ?s", $order_info['b_country']);

	$product_name = "";
	// Products
	if (!empty($order_info['items'])) {
		foreach ($order_info['items'] as $v) {
			$product_name = $product_name . str_replace(', ', ' ', $v['product']) . ",<br>\n  ";
		}
	}
	// Certificates
	if (!empty($order_info['gift_certificates'])) {
		foreach ($order_info['gift_certificates'] as $v) {
			$product_name = $product_name . str_replace(', ', ' ', $v['gift_cert_code']) . ",<br>\n  ";
		}
	}
	// Shippings
	if (floatval($order_info['shipping_cost'])) {
		foreach ($order_info['shipping'] as $v) {
			$product_name .= str_replace(', ', ' ', $v['shipping']) . ",<br>\n  ";
		}
	}
	$sign = md5($processor_data['params']['product_id'] . '-' . $order_info['total'] . '-' . $processor_data['params']['sharedsec']);

$lang_code = CART_LANGUAGE;
echo <<<EOT
<html>
<body onLoad="javascript: document.process.submit();">
<form action="https://payments.chronopay.com/" method="POST" name="process"> 
<input type="hidden" name="product_id" value="{$processor_data['params']['product_id']}" />
<input type="hidden" name="product_name" value="{$product_name}" />
<input type="hidden" name="product_price" value="{$order_info['total']}" />
<input type="hidden" name="order_id" value="{$order_id}" />
<input type="hidden" name="cs1" value="{$order_id}" />
<input type="hidden" name="language" value="{$lang_code}" />
<input type="hidden" name="f_name" value="{$order_info['b_firstname']}" />
<input type="hidden" name="s_name" value="{$order_info['b_lastname']}" />
<input type="hidden" name="street" value="{$order_info['b_address']}" />
<input type="hidden" name="city" value="{$order_info['b_city']}" />
<input type="hidden" name="state" value="{$order_info['b_state']}" />
<input type="hidden" name="zip" value="{$order_info['b_zipcode']}" />
<input type="hidden" name="country" value="{$country}" />
<input type="hidden" name="phone" value="{$order_info['phone']}" />
<input type="hidden" name="email" value="{$order_info['email']}" />
<input type="hidden" name="cb_url" value="{$post_url}" />
<input type="hidden" name="success_url" value="{$return_url}&sign={$sign}" />
<input type="hidden" name="decline_url" value="{$return_url}" />
<input type="hidden" name="sign" value="{$sign}" />
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'Chronopay server', $msg);
echo <<<EOT
	</form>
	<p><div align=center>{$msg}</div></p>
 </body>
</html>
EOT;
die();
}
exit;
?>