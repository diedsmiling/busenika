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
// $Id: totalwebpp.php 7502 2009-05-19 14:54:59Z zeke $
//
if (!defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {
	// Get the password
	$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $_REQUEST['order_id']);
	$processor_data = fn_get_payment_method_data($payment_id);

	$transid = $_REQUEST['TransID'];
	$status  = $_REQUEST['Status'];
	$amount  = $_REQUEST['Amount'];
	$crypt   = $_REQUEST['Crypt'];

	// need to verify the integrity of the parameters to ensure they are not spoofed
	$cryptcheck = md5($status . $transid . $amount . $processor_data['params']['password']);

	if ($status == 'Success' && ($crypt == $cryptcheck)) {
		$pp_response['order_status'] = ($processor_data['params']['transaction_type'] == 'PAYMENT') ? 'P' : 'O';
		$pp_response['reason_text'] = 'Payment Approved';
		$pp_response['transaction_id'] = $transid;
	} else {
		if ($status == 'Fail' ) {
			$pp_response['order_status'] = 'D';
			$pp_response['reason_text'] = 'Status: Declined';

		} elseif ($crypt != $cryptcheck) {
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = "Status: Password Check Failed $crypt $cryptcheck ";

		} else {
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = 'Status: Problem with confirming payment';
		}
	}

	fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
	fn_order_placement_routines($_REQUEST['order_id']);

} else {
	$http_location = Registry::get('config.http_location');
	$post_address = ($processor_data['params']['testmode'] != "N") ? "https://testsecure.totalwebsecure.com/paypage/clear.asp" : "https://secure.totalwebsecure.com/paypage/clear.asp";

	$msg = fn_get_lang_var('text_cc_processor_connection');
	$msg = str_replace('[processor]', 'Total Web Solutions Pay Page', $msg);

echo <<<EOT
<html>
<body onLoad="document.process.submit();">
  <form action="{$post_address}" method="POST" name="process">
	<input type="hidden" name="CustomerID" value="{$processor_data['params']['vendor']}" />
	<input type="hidden" name="Notes" value="{$processor_data['params']['order_prefix']}{$order_id}" />
	<input type="hidden" name="TransactionAmount" value="{$order_info['total']}" />
	<input type="hidden" name="Amount" value="{$order_info['total']}" />
	<input type="hidden" name="TransactionCurrency" value="{$processor_data['params']['currency']}" />
	<input type="hidden" name="redirectorfailed" value="{$http_location}/{$index_script}?dispatch=payment_notification.notify&payment=totalwebpp&order_id={$order_id}" />
	<input type="hidden" name="PayPageType" value="4"/>
	<input type="hidden" name="redirectorsuccess" value="{$http_location}/{$index_script}?dispatch=payment_notification.notify&payment=totalwebpp&order_id={$order_id}" />
	<input type="hidden" name="CustomerEmail" value="{$order_info['email']}" />
	<p>
	<div align=center>{$msg}</div>
	</p>
 </body>
</html>
EOT;
}

exit;

?>
