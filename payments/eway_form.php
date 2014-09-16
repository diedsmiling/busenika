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
// $Id: eway_form.php 10229 2010-07-27 14:21:39Z 2tl $
//
if ( !defined('AREA') ) { die('Access denied'); }

$processor_error = array(
	"A4" => "A link error has occurred between the bank and the modem.",
	"A5" => "The secure PIN Pad unit is not responding.",
	"A6" => "No free PIN Pad slots were available to service the transaction request.",
	"A7" => "A generic interface request specified an illegal value in 'Polled' field.",
	"A8" => "An invalid amount was specified.",
	"AA" => "An invalid card number was specified.",
	"AB" => "An account invalid value for account was specified",
	"AC" => "A past date was specified for expiry",
	"AD" => "The specified account is not available on the server.",
	"AE" => "A queued Authorisation timed-out.",
	"AF" => "A journal lookup did not find the requested transaction.",
	"U9" => "A valid response was not received in time from the Bank Host.",
	"W6" => "The function requested is not supported by the OCV servers bank.",
);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['ewayTrxnStatus'])) {

	$order_info = fn_get_order_info($_REQUEST['order_id']);
	
	if (strtolower($_REQUEST['ewayTrxnStatus']) == 'true' && (fn_format_price(str_replace(array('$',','), '', $_REQUEST['eWAYReturnAmount'])) == fn_format_price($order_info['total']))) {
		$pp_response['order_status'] = 'P';
		$pp_response["reason_text"] = $_REQUEST['eWAYresponseText'];
	} else {
		$pp_response['order_status'] = 'F';
		$pp_response["reason_text"] = $_REQUEST['eWAYresponseText'] . ":" . @$processor_error[$_REQUEST['eWAYresponseCode']];
	}

	if (strtolower($_REQUEST['eWAYoption3']) == 'true') {
		$pp_response["reason_text"] .= "; This is a TEST transaction";
	}

	$pp_response["transaction_id"] = $_REQUEST['ewayTrxnReference'];

	if (fn_check_payment_script('eway_form.php', $_REQUEST['order_id'])) {
		fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
		fn_order_placement_routines($_REQUEST['order_id']);
	}

} else {
	$return_url = Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.notify&payment=eway_form&order_id=$order_id";
	$order_total = 100 * $order_info['total'];
	$testmode = ($processor_data['params']['test']=='Y') ? "TRUE" : "FALSE";
	$_order_id = $processor_data['params']['order_prefix'] . (($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id);

echo <<<EOT
<html>
<body onLoad="javascript: document.process.submit();">
<form method="post" action="https://www.eway.com.au/gateway/payment.asp" name="process">
	<input type="hidden" name="ewayCustomerID" value="{$processor_data['params']['client_id']}" />
	<input type="hidden" name="ewayTotalAmount" value="{$order_total}" />
	<input type="hidden" name="ewayCustomerInvoiceRef" value="{$_order_id}" />
	<input type="hidden" name="ewayCustomerFirstName" value="{$order_info['firstname']}" />
	<input type="hidden" name="ewayCustomerLastName" value="{$order_info['lastname']}" />
	<input type="hidden" name="ewayCustomerEmail" value="{$order_info['email']}" />
	<input type="hidden" name="ewayCustomerAddress" value="{$order_info['b_address']}" />
	<input type="hidden" name="ewayCustomerPostcode" value="{$order_info['b_zipcode']}" />
	<input type="hidden" name="ewayOption3" value="{$testmode}" />
	<input type="hidden" name="ewayURL" value="{$return_url}" />
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'eWAY server', $msg);
echo <<<EOT
	</form>
	<p><div align=center>{$msg}</div></p>
 </body>
</html>
EOT;
exit;
}
?>