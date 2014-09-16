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
// $Id: deltapay.php 10229 2010-07-27 14:21:39Z 2tl $
//

if (!defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {

	$pp_response["order_status"] = (($_REQUEST['Result'] == "1") ? 'P' : 'F');

	if ($_REQUEST['Result'] == 2) {
		$pp_response["reason_text"] = "Error";

	} elseif ($_REQUEST['Result'] == 3) {
		$pp_response["order_status"] = 'I';
		$pp_response["reason_text"] = "Cancelled";
	}

	if (isset($_REQUEST['ErrorMessage'])) {
		$pp_response["reason_text"].= ": " . $_REQUEST['ErrorMessage'];
	}

	if (isset($_REQUEST['DeltaPayId'])) {
		$pp_response["transaction_id"] = $_REQUEST['DeltaPayId'];
	}

	$order_id = (strpos($_REQUEST['Param1'], '_')) ? substr($_REQUEST['Param1'], 0, strpos($_REQUEST['Param1'], '_')) : $_REQUEST['Param1'];

	if (fn_check_payment_script('deltapay.php', $order_id)) {
		fn_finish_payment($order_id, $pp_response, false);
		fn_order_placement_routines($order_id);
	}

} else {
	$amount = str_replace('.', ',', $order_info["total"]);
	$_order_id = ($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id;

echo <<<EOT
<html>
<body onLoad="javascript: document.process.submit();">
<form method="post" action="https://www.deltapay.gr/entry.asp" name="process">
	 <input type="hidden" name="merchantCode" value="{$processor_data['params']['merchant_id']}">
	 <input type="hidden" name="param1" value="{$_order_id}">
	 <input type="hidden" name="charge" value="{$amount}">
	 <input type="hidden" name="currencycode" value="{$processor_data['params']['currency']}">
	 <input type="hidden" name="transactiontype" value="1">
	 <input type="hidden" name="installments" value="0">
	 <input type="hidden" name="cardholderemail" value="{$order_info['email']}">
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'DeltaPay server', $msg);
echo <<<EOT
	</form>
   <p><div align=center>{$msg}</div></p>
 </body>
</html>
EOT;
exit;
}
?>