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
// $Id: cresecure.php 10378 2010-08-06 12:36:48Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {

	if ($mode == 'return') {
		//NOTE: do not remove intval() !
		$order_id = intval($_REQUEST['order_id']);

		$pp_response = array();
		$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id=?i", $order_id);
		$processor_data = fn_get_processor_data($payment_id);

		if (empty($_REQUEST['error']) && !empty($_REQUEST['code']) && $_REQUEST['code'] == '000') {
			$pp_response['order_status'] = 'P';
			$pp_response['reason_text'] = $_REQUEST['msg'];
			$pp_response['transaction_id'] = $_REQUEST['TxnGUID'];

			$pp_response['card_number'] = $_REQUEST['mPAN'];
			$pp_response['card'] = $_REQUEST['type'];
			$pp_response['cardholder_name'] = $_REQUEST['name'];
			$pp_response['expiry_month'] = substr($_REQUEST['exp'], 0, 2);
			$pp_response['expiry_year'] = substr($_REQUEST['exp'], -2);

		} elseif (!empty($_REQUEST['error'])) {
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = !empty($_REQUEST['msg'])? $_REQUEST['msg'] : fn_get_lang_var('error');

		} else {
			$pp_response['order_status'] = 'N';
			$pp_response['reason_text'] = fn_get_lang_var('transaction_cancelled');
		}

		if (fn_check_payment_script('cresecure.php', $order_id)) {
			fn_finish_payment($order_id, $pp_response);
			fn_order_placement_routines($order_id);
		}
	}
} else {

	if ($processor_data['params']['test'] == 'live') {
		$post_address = "https://safe.cresecure.net/securepayments/a1/cc_collection.php";
	} else {
		$post_address = "https://safe.sandbox-cresecure.net/securepayments/a1/cc_collection.php";
	}

	$msg = fn_get_lang_var('text_cc_processor_connection');
	$msg = str_replace('[processor]', 'CRE Secure', $msg);

	$post = array();

	$post['total_amt'] = sprintf('%.2f', $order_info['total']);
	$post['return_url'] = Registry::get('config.https_location') . "/$index_script?dispatch=payment_notification.return&payment=cresecure&order_id=$order_id";
	$post['content_template_url'] = Registry::get('config.https_location') . "/payments/cresecure_template.php?order_id=$order_id";

	$post['b_country'] = db_get_field('SELECT a.code_A3 FROM ?:countries as a WHERE a.code = ?s', $order_info['b_country']);
	$post['s_country'] = db_get_field('SELECT a.code_A3 FROM ?:countries as a WHERE a.code = ?s', $order_info['s_country']);

	$post['customer_address'] = $order_info['b_address'] . ((!empty($order_info['b_address_2']))? ' ' . $order_info['b_address_2'] : '');
	$post['delivery_address'] = $order_info['s_address'] . ((!empty($order_info['s_address_2']))? ' ' . $order_info['s_address_2'] : '');

	$post['customer_phone'] = !empty($order_info['b_phone'])? $order_info['b_phone'] : '';
	$post['delivery_phone'] = !empty($order_info['s_phone'])? $order_info['s_phone'] : '';

	$post['allowed_types'] = !empty($processor_data['params']['allowed_types'])? join('|', $processor_data['params']['allowed_types']) : 'Visa|MasterCard';
	$post['sess_id'] = SESSION::get_id();
	$post['sess_name'] = SESS_NAME;

	$post['order_id'] = $order_info['order_id'];
	$post['currency'] = $processor_data['params']['currency'];

	echo <<<EOT
	<html>
	<body onLoad="document.process.submit();">
	<form action="{$post_address}" method="POST" name="process">
		<input type="hidden" name="CRESecureID" value="{$processor_data['params']['cresecureid']}" />
		<input type="hidden" name="CRESecureAPIToken" value="{$processor_data['params']['cresecureapitoken']}" />
		<input type="hidden" name="total_amt" value="{$post['total_amt']}" />
		<input type="hidden" name="return_url" value="{$post['return_url']}" />
		<input type="hidden" name="content_template_url" value="{$post['content_template_url']}" />
		<input type="hidden" name="order_id" value="{$post['order_id']}" />
		<input type="hidden" name="customer_id" value="{$order_info['user_id']}" />
		<input type="hidden" name="currency_code" value="{$post['currency']}" />

		<input type="hidden" name="customer_company" value="{$order_info['company']}" />
		<input type="hidden" name="customer_firstname" value="{$order_info['b_firstname']}" />
		<input type="hidden" name="customer_lastname" value="{$order_info['b_lastname']}" />
		<input type="hidden" name="customer_address" value="{$post['customer_address']}" />
		<input type="hidden" name="customer_email" value="{$order_info['email']}" />
		<input type="hidden" name="customer_phone" value="{$post['customer_phone']}" />
		<input type="hidden" name="customer_city" value="{$order_info['b_city']}" />
		<input type="hidden" name="customer_state" value="{$order_info['b_state']}" />
		<input type="hidden" name="customer_postal_code" value="{$order_info['b_zipcode']}" />
		<input type="hidden" name="customer_country" value="{$order_info['b_country']}" />

		<input type="hidden" name="delivery_firstname" value="{$order_info['s_firstname']}" />
		<input type="hidden" name="delivery_lastname" value="{$order_info['s_lastname']}" />
		<input type="hidden" name="delivery_address" value="{$post['delivery_address']}" />
		<input type="hidden" name="delivery_phone" value="{$post['delivery_phone']}" />
		<input type="hidden" name="delivery_city" value="{$order_info['s_city']}" />
		<input type="hidden" name="delivery_state" value="{$order_info['s_state']}" />
		<input type="hidden" name="delivery_postal_code" value="{$order_info['s_zipcode']}" />
		<input type="hidden" name="delivery_country" value="{$post['s_country']}" />

		<input type="hidden" name="allowed_types" value="{$post['allowed_types']}" />
		<input type="hidden" name="sess_id" value="{$post['sess_id']}" />
		<input type="hidden" name="sess_name" value="{$post['sess_name']}" />
		<input type="hidden" name="ip_address" value="{$_SERVER['REMOTE_ADDR']}" />
	</form>
	<p>
	<div align=center>{$msg}</div>
	</p>
</body>
</html>
EOT;
}

exit;

?>