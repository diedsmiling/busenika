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
// $Id: winbank.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

/*
[transactionid] => 4358450
[merchantreference] => 640_2
[responsecode] => 00
[responsedescription] => Approved or completed successfully
[retrievalref] => 000345756257
[approvalcode] => 153152
[errorcode] => 0
[errordescription] =>
[amount] => 0.10
[installments] => 0
[cardtype] =>
[langid] => 1
[parameters] =>

«00», «08», «10», «11» or «16».
*/

if (defined('PAYMENT_NOTIFICATION')) {

	$order_id = intval($_REQUEST['order_id']);
	
	if ($mode == 'failed') {
		$pp_response['order_status'] = 'F';
		$pp_response["reason_text"] = 'User was manually returned to the shop';

	} else {

		$order_id = (strpos($_REQUEST['merchantreference'], '_')) ? substr($_REQUEST['merchantreference'], 0, strpos($_REQUEST['merchantreference'], '_')) : $_REQUEST['merchantreference'];
		$order_info = fn_get_order_info($order_id);

		if (in_array($_REQUEST['responsecode'], array('00', '08', '10', '11', '16'))) {
			$pp_response['order_status'] = 'P';
			$pp_response["reason_text"] = 'Response code: ' . $_REQUEST['responsecode'] . ' (' . $_REQUEST['responsedescription'] . ')';
		} else {
			$pp_response['order_status'] = 'F';
			if (!empty($_REQUEST['errordescription'])) {
				$pp_response["reason_text"] = 'Response code: ' . $_REQUEST['responsecode'] . ' (' . $_REQUEST['errordescription'] . ')';
			} else {
				$pp_response["reason_text"] = 'Response code: ' . $_REQUEST['responsecode'] . ' (' . $_REQUEST['responsedescription'] . ')';
			}
		}
		$pp_response['transaction_id'] = $_REQUEST['transactionid'];
	}

	if (fn_check_payment_script('winbank.php', $order_id)) {
		fn_finish_payment($order_id, $pp_response, false);
	}

	fn_order_placement_routines($order_id);

} else {

	$return_url = Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.failed&payment=winbank&order_id=$order_id";
	$_order_id = ($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id;
	$_order_total = $order_info['total'] * 100;

echo <<<EOT
<html>
<body onload="javascript: document.process.submit();">
<form method="post" action="https://paycenter.winbank.gr/ePos2003/winpay.asp" name="process">
	<input type="hidden" name="merchantid" value="{$processor_data['params']['merchant_id']}" />
	<input type="hidden" name="posid" value="{$processor_data['params']['pos_id']}" />
	<input type="hidden" name="user" value="{$processor_data['params']['user']}" />
	<input type="hidden" name="merchantreference" value="{$_order_id}" />
	<input type="hidden" name="currency" value="{$processor_data['params']['currency']}" />
	<input type="hidden" name="amount" value="{$_order_total}" />
	<input type="hidden" name="backlink" value="{$return_url}" />
	<input type="hidden" name="langid" value="{$processor_data['params']['language']}" />
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'WinBank server', $msg);
echo <<<EOT
	</form>
	<p><div align=center>{$msg}</div></p>
 </body>
</html>
EOT;
exit;
}
?>