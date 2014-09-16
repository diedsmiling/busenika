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
// $Id: datatrans.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {

	if ($mode == 'notify') {

		if (!empty($_REQUEST['refno'])) {
			$order_id = (strpos($_REQUEST['refno'], '_')) ? substr($_REQUEST['refno'], 0, strpos($_REQUEST['refno'], '_')) : $_REQUEST['refno'];
		} else {
			die('DataTrans: incorrect parameters');
		}

		$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $order_id);
		$processor_data = fn_get_payment_method_data($payment_id);

		$pp_response = array(
			'reason_text' => ''
		);	

		if (!empty($_REQUEST['uppTransactionId'])) {
			$pp_response['transaction_id'] = $_REQUEST['uppTransactionId'];
		}

		if (!empty($_REQUEST['authorizationCode'])) {
			$pp_response['reason_text'] .= "AuthCode: " . $_REQUEST['authorizationCode'] . "\n";
		}

		if (!empty($_REQUEST['responseMessage'])) {
			$pp_response['reason_text'] .= "Response Message: " . $_REQUEST['responseMessage'] . "\n";
		}

		if (!empty($_REQUEST['acqAuthorizationCode'])) {
			$pp_response['reason_text'] .= "CC Issuing Bank AuthCode: " . $_REQUEST['acqAuthorizationCode'] . "\n";
		}

		if (!empty($_REQUEST['status'])) {
			$pp_response['reason_text'] .= "Status: " . $_REQUEST['status'] . "\n";
		}

		if (!empty($_REQUEST['sign2'])) {
			$pp_response['reason_text'] .= "Sign: " . $_REQUEST['sign2'] . "\n";
		}

		if (!empty($_REQUEST['errorMessage'])) {
			$pp_response['reason_text'] .= "Error message: " . $_REQUEST['errorMessage'];
			if (!empty($_REQUEST['errorDetail'])) {
				$pp_response['reason_text'] .= "(" . $_REQUEST['errorDetail'] . ")";
			}

			$pp_response['reason_text'] .= "\n";
		}

		if ($_REQUEST['status'] == 'success' && $processor_data['params']['sign'] == $_REQUEST['sign']) {
			$pp_response['order_status'] = 'P';

		} elseif ($_REQUEST['status'] == 'success' && $processor_data['params']['sign'] != $_REQUEST['sign']) {
			$pp_response['order_status'] = 'F';
 			$pp_response['reason_text'] .= "Digital signature doesn't match\n";

		} elseif ($_REQUEST['status'] == 'error') {
			$pp_response['order_status'] = 'F';

		} elseif ($_REQUEST['status'] == 'cancel') {
			$pp_response['order_status'] = 'I';

		} else {
			$pp_response['order_status'] = 'F';
		}

		if (fn_check_payment_script('datatrans.php', $order_id)) {
			fn_finish_payment($order_id, $pp_response, false);
			if ($action == 'cancel') {
				$mode = 'result';
			}
		}
	}
	if ($mode == 'result') {
		fn_order_placement_routines($_REQUEST['order_id']);
	}
} else {

	$_order_id = ($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id;
	$pp_total = $order_info['total'] * 100;
	$pp_response_url = Registry::get('config.current_location') . '/' . INDEX_SCRIPT . "?dispatch=payment_notification.result&payment=datatrans&order_id=$order_id";
	$pp_cancel_url = Registry::get('config.current_location') . '/' . INDEX_SCRIPT . "?dispatch=payment_notification.notify.cancel&payment=datatrans&order_id=$order_id";
	
	if ($processor_data['params']['mode'] == 'test') {
		$pp_url = "https://pilot.datatrans.biz/upp/jsp/upStart.jsp";
	} else {
		$pp_url = "https://payment.datatrans.biz/upp/jsp/upStart.jsp";
	}

	if (CART_LANGUAGE == 'FR') {
		$language = 'fr';
	} elseif (CART_LANGUAGE == 'DE') {
		$language = 'de';
	} else {
		$language = 'en';
	}

	echo <<<EOT
<html>
<body onload="javascript: document.process.submit();">
<form action="{$pp_url}" method="POST" name="process">
	<input type="hidden" name="merchantId" value="{$processor_data['params']['merchant_id']}" />
	<input type="hidden" name="amount" value="{$pp_total}" />
	<input type="hidden" name="currency" value="{$processor_data['params']['currency']}" />
	<input type="hidden" name="refno" value="{$_order_id}" />
	<input type="hidden" name="successUrl" value="{$pp_response_url}" />
	<input type="hidden" name="errorUrl" value="{$pp_response_url}" />
	<input type="hidden" name="cancelUrl" value="{$pp_cancel_url}" />
	<input type="hidden" name="language" value="{$language}" />
	<input type="hidden" name="reqtype" value="{$processor_data['params']['transaction_type']}" />
	<input type="hidden" name="sign" value="{$processor_data['params']['sign']}" />
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'DataTrans server', $msg);
echo <<<EOT
	</form>
	<p><div align=center>{$msg}</div></p>
 </body>
</html>
EOT;
}
exit;
?>
