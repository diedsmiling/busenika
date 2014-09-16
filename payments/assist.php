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
// $Id: assist.php 0011 2010-01-01 11:11:11Z 2tl $
//

if (!defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {

	$order_id = $_REQUEST['order_id'];

	if (!fn_check_payment_script('assist.php', $order_id, $processor_data)) {
		exit;
	}
	
	$order_info = fn_get_order_info($order_id);
	$pp_response = array();
	
	if ($mode == 'place_order') {
		
		header('Content-Type: text/html; charset=windows-1251');

		$view->assign('order_action', fn_get_lang_var('placing_order'));
		$page = $view->fetch('views/orders/components/placing_order.tpl');
		$page = fn_convert_encoding('UTF-8', 'cp1251', $page);
		echo $page;
		fn_flush();
		
		$current_location = Registry::get('config.current_location');
	
		if ($processor_data['params']['mode'] == 'L') {
			//$url = 'https://secure.assist.ru/shops/cardpayment.cfm';
			$url = 'https://secure.assist.ru/pay/order.cfm';
		} else {
			//$url = 'https://test.assist.ru/shops/cardpayment.cfm';
			$url = 'https://test.paysecure.ru/pay/order.cfm';
		}
	
		$post = array();
		
		$post['Shop_IDP'] = $processor_data['params']['shop_idp'];
		$post['Merchant_ID'] = $processor_data['params']['shop_idp'];
		$post['Order_IDP'] = $processor_data['params']['order_prefix'] . $order_id . ($order_info['repaid'] ? "_{$order_info['repaid']}" : '');
		$post['OrderNumber'] = $processor_data['params']['order_prefix'] . $order_id . ($order_info['repaid'] ? "_{$order_info['repaid']}" : '');
	
		$post['Subtotal_P'] = $order_info['total'];
		$post['OrderAmount'] = $order_info['total'];
		$post['Currency'] = '';
	
		$post['Language'] = $processor_data['params']['language'];
	
		$post['Delay'] = 0;
	
		$md5_check = md5($order_id . 'key' . $processor_data['params']['secret_key'] . $order_info['total']);
	
		$post['URL_RETURN'] = "$current_location/$index_script?dispatch=payment_notification.return&payment=assist&order_id=$order_id";
		$post['URL_RETURN_OK'] = "$current_location/$index_script?dispatch=payment_notification.return_ok&payment=assist&md5_check=$md5_check&order_id=$order_id";
		$post['URL_RETURN_NO'] = "$current_location/$index_script?dispatch=payment_notification.return_no&payment=assist&order_id=$order_id";
	
		$post['FirstName'] = $order_info['b_firstname'];
		$post['LastName'] = $order_info['b_lastname'];
		$post['MiddleName'] = '';
		$post['Email'] = $order_info['email'];
		$post['Phone'] = $order_info['phone'];
	
		$post['Address'] = $order_info['b_address'];
		$post['Country'] = db_get_field("SELECT code_A3 FROM ?:countries WHERE code = ?s", $order_info['b_country']);
		$post['State'] = $order_info['b_state'];
		$post['City'] = $order_info['b_city'];
		$post['Zip'] = $order_info['b_zipcode'];
	
		$post['Comment'] = $order_info['notes'];
	
		//$ChoosenCardType = '';
		$post['CardPayment'] = 1;
		$post['WebMoneyPayment'] = 0;
		$post['PayCashPayment'] = 0;
		$post['EPortPayment'] = 0;
		$post['EPBeelinePayment'] = 0;
		$post['AssistIDCCPayment'] = 0;
		
		$DemoResult = ''; //failed
		$DemoResult = 'AS000'; //autorisation successful
	
		$page = <<<EOT
<html>
<body onLoad="javascript:document.process.submit();">
<form method="post" action="{$url}" name="process">
EOT;
	
		foreach ($post as $name => &$value) {
			$page .=  "<input type=\"hidden\" name=\"$name\" value=\"$value\" />\n";
		}
	
		if ($processor_data['params']['mode'] == 'T') {
			$page .= <<<EOT
<input type="hidden" name="DemoResult" value="{$DemoResult}" />
EOT;
		}
	
		$msg = fn_get_lang_var('text_cc_processor_connection');
		$msg = str_replace('[processor]', 'Assist server', $msg);
		$page .= <<<EOT
</form>
<p><div align=center>{$msg}</div></p>
</body>
</html>
EOT;

		$page = fn_convert_encoding('UTF-8', 'cp1251', $page);
		echo $page;
		exit;
		
		
	} elseif ($mode == 'return_ok') {
		$md5_check = md5($order_id . 'key' . $processor_data['params']['secret_key'] . $order_info['total']);
		
		$req_md5_chec = !empty($_REQUEST['md5_check']) ? $_REQUEST['md5_check'] : ''; 
		if ($req_md5_chec == $md5_check) {
			$pp_response['order_status'] = 'P';
			$pp_response['reason_text'] = fn_get_lang_var('transaction_approved');
		} else {
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = fn_get_lang_var('transaction_declined') . '; ' . fn_get_lang_var('md5_checksum_failed');
		}
	} elseif ($mode == 'return_no') {
		$pp_response['order_status'] = 'F';
		$pp_response['reason_text'] = fn_get_lang_var('transaction_declined');
	}

	fn_finish_payment($order_id, $pp_response);
	fn_order_placement_routines($order_id);
	
	exit;
	
} else {
	
	// making redirect for right encoding (cp1251)
	$current_location = Registry::get('config.current_location');
	$url = "$current_location/$index_script?dispatch=payment_notification.place_order&payment=assist&order_id=$order_id";
	
	echo <<<EOT
<html>
<body onLoad="document.process.submit();">
	<form action="{$url}" method="POST" name="process">
	</form>
</html>
EOT;
	exit;
	
}

?>