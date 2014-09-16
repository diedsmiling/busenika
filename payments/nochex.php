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
// $Id: nochex.php 9152 2010-03-25 09:09:22Z 2tl $
//

if (!defined('AREA') && is_array($_POST)) {
	DEFINE ('AREA', 'C');
	DEFINE ('AREA_NAME' ,'customer');

	require './../prepare.php';
	require './../init.php';

	$post[] = "transaction_id=" . $_REQUEST['transaction_id'];
	$post[] = "transaction_date=" . $_REQUEST['transaction_date'];
	$post[] = "from_email=" . $_REQUEST['from_email'];
	$post[] = "to_email=" . $_REQUEST['to_email'];
	$post[] = "order_id=" . $_REQUEST['order_id'];
	$post[] = "amount=" . $_REQUEST['amount'];
	$post[] = "security_key=" . $_REQUEST['security_key'];

	$order_id = (strpos($_REQUEST['order_id'], '_')) ? substr($_REQUEST['order_id'], 0, strpos($_REQUEST['order_id'], '_')) : $_REQUEST['order_id'];
	$order_info = fn_get_order_info($order_id);

	// Post a request and analyse the response
	list($a, $return) = fn_https_request("POST", "https://www.nochex.com/nochex.dll/apc/apc", $post);
	$result = str_replace("\n","&", $return);
	
	$order_info['total'] = fn_format_price($order_info['total']);
	$_REQUEST['amount']  = fn_format_price($_REQUEST['amount']);
	
	$pp_response['order_status'] = ($result == 'AUTHORISED' && $order_info['total'] == $_REQUEST['amount']) ? 'P' : 'F';
	$pp_response["reason_text"] = "SecurityKey: $_REQUEST[security_key], Transaction Date: $_REQUEST[transaction_date]";
	if ($order_info['total'] != $_REQUEST['amount']) {
		$pp_response["reason_text"] .= '; ' . fn_get_lang_var('order_total_not_correct');	
	}
	$pp_response["transaction_id"] = $_REQUEST['transaction_id'];

	fn_finish_payment($order_id, $pp_response);
	exit;

} elseif (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'notify') {
		$order_info = fn_get_order_info($_REQUEST['order_id']);
		if ($order_info['status'] == 'O') {
			$pp_response['order_status'] = 'F';
			fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
		}

		fn_order_placement_routines($_REQUEST['order_id']);
	}

} else {
	if (!defined('AREA') ) { die('Access denied'); }

	$return_url_s = Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.notify&payment=nochex&order_id=$order_id";
	$return_url_c = Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.notify&payment=nochex&order_id=$order_id";
	$responder_url = Registry::get('config.current_location') . "/payments/nochex.php";
	$_order_id = ($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id;

echo <<<EOT
<html>
<body onLoad="javascript: document.process.submit();">
<form method="post" action="https://secure.nochex.com/" name="process">
<input type="hidden" name="merchant_id" value="{$processor_data['params']['merchantid']}" />
<input type="hidden" name="amount" value="{$order_info['total']}" />
<input type="hidden" name="status" value="test" />
<input type="hidden" name="description" value="{$processor_data['params']['payment_description']}" />
<input type="hidden" name="order_id" value="{$_order_id}" />
<input type="hidden" name="success_url" value="{$return_url_s}" />
<input type="hidden" name="cancel_url" value="{$return_url_c}" />
<input type="hidden" name="callback_url" value="{$responder_url}" />

<input type="hidden" name="billing_fullname" value="{$order_info['b_firstname']} {$order_info['b_lastname']}" />
<input type="hidden" name="billing_address" value="{$order_info['b_address']} {$order_info['b_address_2']} {$order_info['b_city']} {$order_info['b_state']}" />
<input type="hidden" name="billing_postcode" value="{$order_info['b_zipcode']}" />
<input type="hidden" name="delivery_fullname" value="{$order_info['s_firstname']} {$order_info['s_lastname']}" />
<input type="hidden" name="delivery_address" value="{$order_info['s_address']} {$order_info['s_address_2']} {$order_info['s_city']} {$order_info['s_state']}" />
<input type="hidden" name="delivery_postcode" value="{$order_info['b_zipcode']}" />
<input type="hidden" name="email_address" value="{$order_info['email']}" />
<input type="hidden" name="customer_phone_number" value="{$order_info['phone']}" />
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'Nochex server', $msg);
echo <<<EOT
	</form>
   <p><div align=center>{$msg}</div></p>
 </body>
</html>
EOT;
}
exit;
?>