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
// $Id: fribetaling.php 7502 2009-05-19 14:54:59Z zeke $
//

if (!defined('AREA') ) { die('Access denied'); }
// Order=1786&Amount=4396&Currency=EUR&Transno=15600&Validatemd=A074BB0B7FC7DB5C1C9E03F9F2013F8B
// Order=1788&Amount=4396&Currency=EUR&Transno=15601

if (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'result') {

		if (!empty($_REQUEST['Validatemd'])) {
			$order_info = fn_get_order_info($_REQUEST['order_id']);

			$md5_string = '';
			if (!empty($processor_data['params'])) {
				$md5_string = strtoupper(md5($_REQUEST['Order'] . $_REQUEST['Amount'] . $_REQUEST['Currency'] . $_REQUEST['Transno'] . $processor_data['params']['mac_key']));
			}

			if ($_REQUEST['Validatemd'] == $md5_string) {
				$pp_response['order_status'] = 'P';

			} else {
				$pp_response['order_status'] = 'F';
				$pp_response['reason_text'] = 'MD5 string is not accepted';
			}
		} else {
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = fn_get_lang_var('text_transaction_declined');
			
			if (!empty($_REQUEST['Error'])) {
				$pp_response['reason_text'] .= " (" . $_REQUEST['Error'] . ")";
			}
		}

		$pp_response["transaction_id"] = $_REQUEST['Transno'];

		if (fn_check_payment_script('fribetaling.php', $_REQUEST['order_id'])) {
			fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
			fn_order_placement_routines($_REQUEST['order_id']);
		}
	}

} else {
	$total = $order_info['total'] * 100;
	$r_url = Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.result&payment=fribetaling&order_id=$order_id";
	$expmm = $order_info['payment_info']['expiry_month'];
	$expyy = $order_info['payment_info']['expiry_year'];

	if ($processor_data["params"]["mode"] == 'A') {
		$test_live = '<input type="hidden" name ="Testtransaction" value="A" />';
	} elseif ($processor_data["params"]["mode"] == 'D') {
		$test_live = '<input type="hidden" name ="Testtransaction" value="D" />';
	} else {
		$test_live = '';
	}
	$_order_id = ($order_info['repaid']) ? ($order_id .'z'. $order_info['repaid']) : $order_id;

echo <<<EOT
<html>
<body onLoad="javascript: document.process.submit();">
<form method="post" action="https://pgw.fribetaling.dk/betal.fri" name="process" target="_TOP" autocomplete="OFF">
	<input type="hidden" name ="Amount" value="{$total}" />
	<input type="hidden" name ="Currency" value="{$processor_data['params']['currency']}" />
	<input type="hidden" name ="Decline" value="{$r_url}" />
	<input type="hidden" name ="Accept" value="{$r_url}" />
	<input type="hidden" name ="Cardnumber" value="{$order_info['payment_info']['card_number']}" />
	<input type="hidden" name ="CVC" value="{$order_info['payment_info']['cvv2']}" />
	<input type="hidden" name ="Expmm" value="{$expmm}" />
	<input type="hidden" name ="Expyy" value="{$expyy}" />
	<input type="hidden" name ="Merchant" value="{$processor_data['params']['merchant_id']}" />
	<input type="hidden" name ="Ordernumber" value="{$order_id}" />
	{$test_live}
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'FRIbetaling server', $msg);
echo <<<EOT
	</form>
   <p><div align=center>{$msg}</div></p>
 </body>
</html>
EOT;
exit;
}
?>
