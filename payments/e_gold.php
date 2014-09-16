<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2009 Simbirsk Technologies Ltd. All rights reserved.    *
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
// $Id: e_gold.php 7502 2009-05-19 14:54:59Z zeke $
//

if (!defined('AREA')) {

	if (!empty($_REQUEST['PAYEE_ACCOUNT'])) {
		// Settle data is received
		DEFINE ('AREA', 'C');
		DEFINE ('AREA_NAME' ,'customer');
		require './../prepare.php';
		require './../init.php';

		$order_id = $_REQUEST['ORDER_NUM'];
		$order_info = fn_get_order_info($order_id);
		$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $order_id);
		$processor_data = fn_get_payment_method_data($payment_id);
		$metal_desc = array('1' => 'Gold', '2' => 'Silver', '3' => 'Platinum', '4' => 'Palladium');

		$pass = strtoupper(md5($processor_data['params']['password']));
		$hash = strtoupper(md5($_REQUEST['PAYMENT_ID'] . $_REQUEST['PAYEE_ACCOUNT'] . $order_info['total'] . $processor_data['params']['currency'] . $_REQUEST['PAYMENT_METAL_ID'] . $_REQUEST['PAYMENT_BATCH_NUM'] . $_REQUEST['PAYER_ACCOUNT'] . $pass));

		if ($hash == $_REQUEST['HANDSHAKE_HASH']) {
			$pp_response['order_status'] = 'P';
			$pp_response["reason_text"] = 'Payer account: ' . $_REQUEST['PAYER_ACCOUNT'] . '; Actual payment ounces: ' . $_REQUEST['ACTUAL_PAYMENT_OUNCES'] . ' (' . $metal_desc[$_REQUEST['PAYMENT_METAL_ID']] . ')';
			$pp_response["transaction_id"] = $_REQUEST['PAYMENT_BATCH_NUM'];
			fn_finish_payment($order_id, $pp_response);
		}
		exit;
	} else { 
		die('Access denied'); 
	}
}


if (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'notify') {
		$order_info = fn_get_order_info($_REQUEST['order_id']);
		if ($order_info['status'] == 'O') {
			$pp_response['order_status'] = 'F';
			$pp_response["reason_text"] = fn_get_lang_var('text_transaction_declined');
			fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
		}

		fn_order_placement_routines($_REQUEST['order_id']);
	}

} else {

	$current_location = Registry::get('config.current_location');
	$return = $current_location . "/$index_script?dispatch=payment_notification.notify&payment_name=e_gold&order_id=$order_id";

echo <<<EOT
<html>
 <body onLoad="javascript: document.process.submit();">
   <form method="post" action="https://www.e-gold.com/sci_asp/payments.asp" name="process">
	<input type="hidden" name="PAYEE_ACCOUNT" value="{$processor_data['params']['account_id']}" />
	<input type="hidden" name="PAYEE_NAME" value="{$processor_data['params']['account_name']}" />
	<input type="hidden" name="PAYMENT_UNITS" value="{$processor_data['params']['currency']}" />
	<input type="hidden" name="PAYMENT_METAL_ID" value="0" />
	<input type="hidden" name="PAYMENT_URL" value="{$return}&succ=Y" />
	<input type="hidden" name="PAYMENT_URL_METHOD" value="POST" />
	<input type="hidden" name="STATUS_URL" value="{$current_location}/payments/e_gold.php" />
	<input type="hidden" name="NOPAYMENT_URL" value="{$return}&succ=N" />
	<input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST" />
	<input type="hidden" name="PAYMENT_AMOUNT" value="{$order_info['total']}" />
	<input type="hidden" name="BAGGAGE_FIELDS" value="ORDER_NUM">
	<input type="hidden" name="ORDER_NUM" value="{$order_id}">
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'e-gold server', $msg);
echo <<<EOT
   </form>
 </body>
</html>
EOT;
}
exit;
?>
