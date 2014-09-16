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
// $Id: thaiepay.php 10080 2010-07-15 13:04:18Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {
	
	if (empty($_REQUEST['refno'])) {
		if (!empty($_SESSION['thaiepay_refno'])) {
			$_REQUEST['refno'] = $_SESSION['thaiepay_refno'];
			unset($_SESSION['thaiepay_refno']); 	
		} else {
			if ($mode == 'finish') {
				$prefix = ((Registry::get('settings.General.secure_auth') == 'Y') && (AREA == 'C')) ? Registry::get('config.https_location') . '/' : '';
				fn_redirect($prefix . INDEX_SCRIPT . "?dispatch=orders.search", true);
			}
			exit;
		}
	}
	$order_id = intval($_REQUEST['refno']);
	
	if (fn_check_payment_script('thaiepay.php', $order_id, $processor_data)) {
	
		if ($mode == 'notify') {
			
			$errors = array();
			$errors_desc = array (
				'additional_parameter' => fn_get_lang_var('additional_parameter_not_correct'),
				'total' => fn_get_lang_var('order_total_not_correct'),
			);
			
			if (isset($_REQUEST['total'])) {
				$order_info = fn_get_order_info($order_id);
				if (fn_format_price($order_info['total']) != fn_format_price($_REQUEST['total'])) {
					$errors['total'] = true;
				}
			}
					
			$param_name = !empty($processor_data['params']['add_param_name']) ? $processor_data['params']['add_param_name'] : ''; 
			$param_value = !empty($processor_data['params']['add_param_value']) ? $processor_data['params']['add_param_value'] : ''; 
			$sec_param = (!empty($param_name) && !empty($_REQUEST[$param_name])) ? $_REQUEST[$param_name] : '';

			if (empty($param_value) || empty($sec_param) || $sec_param != $param_value) {
				$errors['additional_parameter'] = true;	
			}
			
			$pp_response = array();
			$pp_response['reason_text'] = fn_get_lang_var('order_id') . '-' . $order_id;
			$pp_response['transaction_id'] = '';
			
			if ($errors) {
				$pp_response['order_status'] = 'F';
				foreach($errors as $error => $v) {
					$pp_response['reason_text'] = $pp_response['reason_text'] . "\n" . $errors_desc[$error];		
				}
			} else {
				$pp_response['order_status'] = 'P';
			}
			
			fn_finish_payment($order_id, $pp_response);
			exit;

		} elseif ($mode == 'finish') {
			$order_info = fn_get_order_info($order_id);
			if ($order_info['status'] == 'O') {
				$pp_response = array();
				$pp_response['order_status'] = 'F';
				$pp_response['reason_text'] = fn_get_lang_var('merchant_response_was_not_received');
				$pp_response['transaction_id'] = '';
				fn_finish_payment($order_id, $pp_response);
			}
			fn_order_placement_routines($order_id, false);
		}
	}
	
} else {
	$current_location = Registry::get('config.current_location');
	$lang_code = (CART_LANGUAGE == 'TH') ? 'TH' : 'EN';
	$sess = '&' . SESS_NAME . '=' . Session::get_id();
	$_SESSION['thaiepay_refno'] = $order_id;
echo <<<EOT
<html>
<body onLoad="document.process.submit();">
<form method="post" action="https://www.thaiepay.com/epaylink/payment.aspx" name="process">
	<input type="hidden" name="refno" value="{$order_id}">
	<input type="hidden" name="merchantid" value="{$processor_data['params']['merchantid']}">
	<input type="hidden" name="customeremail" value="{$order_info['email']}">
	<input type="hidden" name="productdetail" value="{$processor_data['params']['details']}">
	<input type="hidden" name="total" value="{$order_info['total']}">
	<input type="hidden" name="cc" value="{$processor_data['params']['currency']}">
	<input type="hidden" name="lang" value="{$lang_code}">
	<input type="hidden" name="returnurl" value="{$current_location}/{$index_script}?dispatch=payment_notification.finish&payment=thaiepay&refno={$order_id}{$sess}">
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'thaiepay.com', $msg);
echo <<<EOT
	</form>
	<div align=center>{$msg}</div>
 </body>
</html>
EOT;

}
exit;
?>