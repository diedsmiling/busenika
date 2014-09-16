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
// $Id: 2checkout.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {
	$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $_REQUEST['order_id']);
	$processor_data = fn_get_payment_method_data($payment_id);
	$order_info = fn_get_order_info($_REQUEST['order_id']);

	$order_number_id = ($processor_data['params']['mode'] == 'test') ? '1' : $_REQUEST['order_number'];

	$pp_response = array();
	if ((strtoupper(md5($processor_data['params']['secret_word'] . $processor_data['params']['account_number'] . $order_number_id . $order_info['total'])) == $_REQUEST['key']) && ($_REQUEST['credit_card_processed'] == 'Y')) {
		$pp_response['order_status'] = 'P';
		$pp_response['reason_text'] = fn_get_lang_var('order_id') . '-' . $_REQUEST['order_number'];

	} else {
		$pp_response['order_status'] = ($_REQUEST['credit_card_processed'] == 'K') ? 'O' : 'F';
		$pp_response['reason_text'] = ($_REQUEST['credit_card_processed'] == 'Y') ? "MD5 Hash is invalid" : fn_get_lang_var('order_id') . '-' . $_REQUEST['order_number'];
	}

	$pp_response['transaction_id'] = (!empty($_REQUEST['tcoid'])) ? $_REQUEST['tcoid'] : '';

	if (fn_check_payment_script('2checkout.php', $_REQUEST['order_id'])) {
		fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
		fn_order_placement_routines($_REQUEST['order_id']);
	}
	exit;

} else {
	$__bstate = $order_info['b_state'];
	if ($order_info['b_country'] != 'US' && $order_info['b_country'] != 'CA') {
		$__bstate = "XX";
	}
	$__sstate = @$order_info['s_state'];
	if ($order_info['s_country'] != 'US' && $order_info['s_country'] != 'CA') {
		$__sstate = "XX";
	}
	$is_test = ($processor_data['params']['mode'] == 'test') ? 'Y' : 'N';
	$cart_order_id = ($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id;
	$sh_cost = fn_order_shipping_cost($order_info);

echo <<<EOT
<html>
<body onLoad="document.process.submit();">
<form action="https://www2.2checkout.com/2co/buyer/purchase" method="POST" name="process">
	<input type="hidden" name="sid" value="{$processor_data['params']['account_number']}" />
	<input type="hidden" name="total" value="{$order_info['total']}" />
	<input type="hidden" name="cart_order_id" value="{$cart_order_id}" />
	<input type="hidden" name="card_holder_name" value="{$order_info['b_firstname']} {$order_info['b_lastname']}" />
	<input type="hidden" name="street_address" value="{$order_info['b_address']}" />
	<input type="hidden" name="city" value="{$order_info['b_city']}" />
	<input type="hidden" name="state" value="{$__bstate}" />
	<input type="hidden" name="zip" value="{$order_info['b_zipcode']}" />
	<input type="hidden" name="country" value="{$order_info['b_country']}" />
	<input type="hidden" name="email" value="{$order_info['email']}" />
	<input type="hidden" name="phone" value="{$order_info['phone']}" />
	<input type="hidden" name="ship_name" value="{$order_info['s_firstname']} {$order_info['s_lastname']}" />
	<input type="hidden" name="ship_street_address" value="{$order_info['s_address']}" />
	<input type="hidden" name="ship_city" value="{$order_info['s_city']}" />
	<input type="hidden" name="ship_state" value="{$__sstate}" />
	<input type="hidden" name="ship_zip" value="{$order_info['s_zipcode']}" />
	<input type="hidden" name="ship_country" value="{$order_info['s_country']}" />
	<input type="hidden" name="fixed" value="Y" />
	<input type="hidden" name="id_type" value="1" />
	<input type="hidden" name="sh_cost" value="{$sh_cost}" />
	<input type="hidden" name="demo" value="{$is_test}" />
	<input type="hidden" name="dispatch" value="payment_notification" />
	<input type="hidden" name="payment" value="2checkout" />
	<input type="hidden" name="order_id" value="{$order_id}" />
EOT;

// Products
$it = 0;
if (!empty($order_info['items'])) {
	foreach ($order_info['items'] as $k => $v) {
		$it++;
		$is_tangible = (!empty($v['extra']['is_edp']) && $v['extra']['is_edp'] == 'Y') ? 'N' : 'Y';
		$price = fn_format_price($v['price'] - (fn_external_discounts($v) / $v['amount']));
		$suffix = "_$it";
		echo <<<EOT
	<input type="hidden" name="c_prod{$suffix}" value="{$v['product_id']},{$v['amount']}" />
	<input type="hidden" name="c_name{$suffix}" value="{$v['product']}" />
	<input type="hidden" name="c_description{$suffix}" value="{$v['product']}" />
	<input type="hidden" name="c_price{$suffix}" value="{$price}" />
	<input type="hidden" name="c_tangible{$suffix}" value="{$is_tangible}" />
EOT;
	}
}
// Certificates
if (!empty($order_info['gift_certificates'])) {
	foreach ($order_info['gift_certificates'] as $k => $v) {
		$it++;
		$v['amount'] = (!empty($v['extra']['exclude_from_calculate'])) ? 0 : $v['amount'];
		$suffix = "_$it";
	echo <<<EOT
	<input type="hidden" name="c_prod{$suffix}" value="{$v['gift_cert_id']},1" />
	<input type="hidden" name="c_name{$suffix}" value="{$v['gift_cert_code']}" />
	<input type="hidden" name="c_description{$suffix}" value="{$v['gift_cert_code']}" />
	<input type="hidden" name="c_price{$suffix}" value="{$v['amount']}" />
	<input type="hidden" name="c_tangible{$suffix}" value="N" />
EOT;
	}
}

/*if (floatval($order_info['subtotal_discount'])) {
	$it++;
	$suffix = "_$it";
	$desc = fn_get_lang_var('order_discount');
	$pr = fn_format_price($order_info['subtotal_discount']);
	echo <<<EOT
	<input type="hidden" name="c_prod{$suffix}" value="ORDER_DISCOUNT,1" />
	<input type="hidden" name="c_name{$suffix}" value="{$desc}" />
	<input type="hidden" name="c_description{$suffix}" value="{$desc}" />
	<input type="hidden" name="c_price{$suffix}" value="{$pr}" />
	<input type="hidden" name="c_tangible{$suffix}" value="N" />
EOT;
}*/

$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', '2checkout.com', $msg);
echo <<<EOT
	</form>
	<div align=center>{$msg}</div>
 </body>
</html>
EOT;
}
exit;
?>