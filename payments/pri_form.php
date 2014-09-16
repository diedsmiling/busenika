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
// $Id: pri_form.php 7502 2009-05-19 14:54:59Z zeke $
//

if (!defined('AREA')) {
	$avserr = array(
			"X" => "Exact match - 9 digit zip",
			"Y" => "Exact match - 5 digit zip",
			"A" => "Address match only",
			"W" => "9-digit zip match only",
			"Z" => "5-digit zip match only",
			"N" => "No address or zip match",
			"U" => "Address unavailable",
			"G" => "Non-U.S. Issuer",
			"R" => "Issuer system unavailable"
	);

	if (!empty($_REQUEST['RefNo']) && !empty($_REQUEST['Auth'])) {
		DEFINE ('AREA', 'C');
		DEFINE ('AREA_NAME', 'customer');
		require './../prepare.php';
		require './../init.php';

		$order_id = (strpos($_REQUEST['RefNo'], '_')) ? substr($_REQUEST['RefNo'], 0, strpos($_REQUEST['RefNo'], '_')) : $_REQUEST['RefNo'];

		if ($_REQUEST['Auth'] != "Declined") {
			$pp_response['order_status'] = 'P';
			$pp_response["reason_text"] = "AuthCode: " . $_REQUEST['Auth'];

		} else {
			$pp_response['order_status'] = 'F';
			$pp_response["reason_text"] = $_REQUEST['Auth']. ": " . $_REQUEST['Notes'];
		}

		if (!empty($_REQUEST['TransID'])) {
			$pp_response["transaction_id"] = $_REQUEST['TransID'];
		}

		if (!empty($_REQUEST['AVSCode'])) {
			$pp_response["descr_avs"] = empty($avserr[$_REQUEST['AVSCode']]) ? "AVS Code: " . $_REQUEST['AVSCode'] : $avserr[$_REQUEST['AVSCode']];
		}

		if (fn_check_payment_script('pri_form.php', $order_id)) {
			fn_finish_payment($order_id, $pp_response, false);
		}

		fn_redirect(Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.notify&payment=pri_form&order_id=$order_id");

	} else {
		die('Access denied');
	}
}

if (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'notify') {
		fn_order_placement_routines($_REQUEST['order_id']);
	}

} else {
	$_order_id = ($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id;

	$current_location = Registry::get('config.current_location');

echo <<<EOT
<html>
<body onLoad="javascript: document.process.submit();">
<form action="https://webservices.primerchants.com/billing/TransactionCentral/EnterTransaction.asp" method="POST" name="process">
	<input type="hidden" name="MerchantID" value="{$processor_data['params']['merchant_id']}" />
	<input type="hidden" name="RegKey" value="{$processor_data['params']['key']}" />
	<input type="hidden" name="Amount" value="{$order_info['total']}" />
	<input type="hidden" name="AVSADDR" value="{$order_info['b_address']}" />
	<input type="hidden" name="AVSZIP" value="{$order_info['b_zipcode']}" />
	<input type="hidden" name="REFID" value="{$_order_id}" />
	<input type="hidden" name="RURL" value="{$current_location}/payments/pri_form.php" />
	<input type="hidden" name="TransType" value="CC" />
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'PRI server', $msg);
echo <<<EOT
	</form>
	<p><div align=center>{$msg}</div></p>
 </body>
</html>
EOT;
exit;
}
?>
