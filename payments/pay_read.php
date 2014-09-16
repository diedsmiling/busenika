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
// $Id: pay_read.php 10378 2010-08-06 12:36:48Z klerik $
//

if (!defined('AREA')) {
	if (!empty($_REQUEST['payread_payment_id']) || (!empty($_REQUEST['payer_callback_type']) && $_REQUEST['payer_callback_type'] == 'settle')) {

		// Settle data is received
		DEFINE ('AREA', 'C');
		DEFINE ('AREA_NAME' ,'customer');
		require './../prepare.php';
		require './../init.php';

		$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $_REQUEST['order_id']);
		$processor_data = fn_get_payment_method_data($payment_id);
		$order_info = fn_get_order_info($_REQUEST['order_id']);

		if ($order_info['status'] == 'N' || $order_info['status'] == 'O') {
			$pp_response = array();
			$req_url = ($_SERVER['SERVER_PORT'] == '80' ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$ok1 = (strtolower($_REQUEST['md5sum']) == strtolower(md5($processor_data['params']['key_1'] . substr($req_url, 0, strpos($req_url, '&md5sum')) . $processor_data['params']['key_2'])));
			$ok2 = in_array($_SERVER['REMOTE_ADDR'], array('83.241.130.100', '83.241.130.101', '192.168.100.40', '192.168.100.222', '127.0.0.1', '83.241.130.102', '217.151.207.84'));

			$pp_response['order_status'] = ($ok1 && $ok2) ? 'P' : 'F';
			$pp_response['reason_text'] = fn_get_lang_var('order_id') . '-' . $_REQUEST['order_id'];
			$pp_response['transaction_id'] = !empty($_REQUEST['payread_payment_id']) ? $_REQUEST['payread_payment_id'] : 'BANK';
			fn_finish_payment($_REQUEST['order_id'], $pp_response);
		}
		echo "TRUE";
		exit;

	} else {
		// Customer is redirected from the Pay&Read server
		DEFINE ('AREA', 'C');
		DEFINE ('AREA_NAME' ,'customer');
		require './../prepare.php';
		require './../init.php';

		// Check if the settle data was recieved and order status was upsated otherwise transaction is failed
		$order_info = fn_get_order_info($_REQUEST['order_id']);
		if ($order_info['status'] == 'N' || $order_info['status'] == 'O') {
			$pp_response = array();
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = fn_get_lang_var('order_id') . '-' . $_REQUEST['order_id'];
			fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
		}

		fn_redirect(Registry::get('config.current_location') . "/" . Registry::get('config.customer_index') . "?dispatch=payment_notification.notify&payment=pay_read&order_id=$_REQUEST[order_id]");
		exit;
	}

} elseif (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'notify') {
		fn_order_placement_routines($_REQUEST['order_id']);
	}

} else {

// Prepare payment data and submit the form
$post = "";
$post[] = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>";
$post[] = "<payread_post_api_0_2 xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"payread_post_api_0_2.xsd\">";
$post[] = "<seller_details>";
$post[] = "<agent_id>" . $processor_data["params"]["agent_id"]."</agent_id>";
$post[] = "</seller_details>";
// Buyer details
$post[] = "<buyer_details>";
$post[] = "<first_name>" . $order_info["b_firstname"]."</first_name>";
$post[] = "<last_name>" . $order_info["b_lastname"]."</last_name>";
$post[] = "<address_line_1>" . $order_info["b_address"]."</address_line_1>";
$post[] = "<address_line_2>" . $order_info["b_address_2"]."</address_line_2>";
$post[] = "<postal_code>" . $order_info["b_zipcode"]."</postal_code>";
$post[] = "<city>" . $order_info["b_city"]."</city>";
$post[] = "<country_code>" . $order_info["b_country"]."</country_code>";
$post[] = "<phone_home></phone_home>";
$post[] = "<phone_mobile></phone_mobile>";
$post[] = "<phone_work>" . $order_info["phone"]."</phone_work>";
$post[] = "<email>" . $order_info["email"]."</email>";
$post[] = "</buyer_details>";

// Purchase
$post[] = "<purchase>";
$post[] = "<currency>" . $processor_data["params"]["currency"]."</currency>";
$post[] = "<reference_id>". (($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id) . "</reference_id>";

$post[] = "<purchase_list>";

// Purchase list (catalog purchases)
$i = 0;
$_tax = 0;
$_tax_s = array();
$product_tax = array();
$shipping_tax = array();
// Calculate taxes
if (!empty($order_info['taxes'])) {
	foreach($order_info['taxes'] as $key => $value) {
		$_tax += (($value['rate_type'] == 'P') ? $value['rate_value'] : '0');
		foreach ($value['applies'] as $item_id => $tax_value) {
			$product_tax[substr($item_id, 2)] = @$product_tax[substr($item_id, 2)];
			if (substr($item_id, 0, 2) == 'S_') {
				$shipping_tax[(int)substr($item_id, 2)] = @$shipping_tax[(int)substr($item_id, 2)] + (($value['price_includes_tax'] == 'N') ? $tax_value : 0);
			}
			foreach ($order_info['shipping'] as $_k => $_v) {
				// Find shipping id in the item key
				if (substr($item_id, 0, strrpos($item_id, '_')) == ("S_" . $_k)) {
					$_tax_s[$_k] = $value['rate_value'];
				}
			}
		}
	}
}
// Products
if (!empty($order_info['items'])) {
	foreach($order_info['items'] as $k => $v) {
		$post[] = "<freeform_purchase>";
		$post[] = "<line_number>" . ++$i . "</line_number>";
		$post[] = "<description><![CDATA[" . $v['product'] . "]]></description>";
		$post[] = "<price_including_vat>" . fn_format_price(($v['subtotal'] - fn_external_discounts($v) + @$product_tax[$v['cart_id']]) / $v['amount']) . "</price_including_vat>";
		$post[] = "<vat_percentage>" . $_tax . "</vat_percentage>";
		$post[] = "<quantity>" . $v['amount'] . "</quantity>";
		$post[] = "</freeform_purchase>";
	}
}
// Gift Cartificates
if (!empty($order_info['gift_certificates'])) {
	foreach($order_info['gift_certificates'] as $k => $v) {
		$v['amount'] = (!empty($v['extra']['exclude_from_calculate'])) ? 0 : $v['amount'];
		$post[] = "<freeform_purchase>";
		$post[] = "<line_number>".++$i."</line_number>";
		$post[] = "<description><![CDATA[" . $v['gift_cert_code'] . "]]></description>";
		$post[] = "<price_including_vat>".fn_format_price($v['amount']) . "</price_including_vat>";
		$post[] = "<vat_percentage>0</vat_percentage>";
		$post[] = "<quantity>1</quantity>";
		$post[] = "</freeform_purchase>";
	}
}
// Surcharge
if (floatval($order_info['payment_surcharge'])) {
	$post[] = "<freeform_purchase>";
	$post[] = "<line_number>" . ++$i . "</line_number>";
	$post[] = "<description>" . fn_get_lang_var('surcharge') . "</description>";
	$post[] = "<price_including_vat>" . $order_info['payment_surcharge'] . "</price_including_vat>";
	$post[] = "<vat_percentage>0</vat_percentage>";
	$post[] = "<quantity>1</quantity>";
	$post[] = "</freeform_purchase>";
}

// Shipping
if (floatval($order_info['shipping_cost'])) {
	foreach($order_info['shipping'] as $key => $value) {
		$post[] = "<freeform_purchase>";
		$post[] = "<line_number>" . ++$i . "</line_number>";
		$post[] = "<description>" . $value['shipping']."</description>";
		$post[] = "<price_including_vat>" . fn_format_price(array_sum($value['rates']) + @$shipping_tax['1']) . "</price_including_vat>";
		$post[] = "<vat_percentage>" . (isset($_tax_s[$key]) ? $_tax_s[$key] : 0) . "</vat_percentage>";
		$post[] = "<quantity>1</quantity>";
		$post[] = "</freeform_purchase>";
	}
}

// Used Gift Cartificates
if (!empty($order_info['use_gift_certificates'])) {
	foreach($order_info['use_gift_certificates'] as $k => $v) {
		$post[] = "<freeform_purchase>";
		$post[] = "<line_number>" . ++$i . "</line_number>";
		$post[] = "<description>" . htmlentities($k) . "</description>";
		$post[] = "<price_including_vat>-" . fn_format_price($v['cost']) . "</price_including_vat>";
		$post[] = "<vat_percentage>0</vat_percentage>";
		$post[] = "<quantity>1</quantity>";
		$post[] = "</freeform_purchase>";
	}
}

// Order discounts
if (floatval($order_info['subtotal_discount'])) {
	$post[] = "<freeform_purchase>";
	$post[] = "<line_number>" . ++$i . "</line_number>";
	$post[] = "<description>" . htmlentities(fn_get_lang_var('order_discount')) . "</description>";
	$post[] = "<price_including_vat>-" . fn_format_price($order_info['subtotal_discount']) . "</price_including_vat>";
	$post[] = "<vat_percentage>0</vat_percentage>";
	$post[] = "<quantity>1</quantity>";
	$post[] = "</freeform_purchase>";
}

if (!empty($order_info['taxes']) && Registry::get('settings.General.tax_calculation') == 'subtotal') {
	foreach ($order_info['taxes'] as $tax_id => $tax) {
		if ($tax['price_includes_tax'] == 'Y') {
			continue;
		}
		$post[] = "<freeform_purchase>";
		$post[] = "<line_number>" . ++$i . "</line_number>";
		$post[] = "<description>" . htmlspecialchars($tax['description']) . "</description>";
		$post[] = "<price_including_vat>" .fn_format_price($tax['tax_subtotal']) . "</price_including_vat>";
		$post[] = "<vat_percentage>0</vat_percentage>";
		$post[] = "<quantity>1</quantity>";
		$post[] = "</freeform_purchase>";
	}
}

$post[] = "</purchase_list>";
$post[] = "</purchase>";

//Processing control
$url = Registry::get('config.current_location') . "/payments/pay_read.php?order_id=$order_id";
$post[] = "<processing_control>";
$post[] = "<success_redirect_url>" . $url . "</success_redirect_url>";
$post[] = "<authorize_notification_url>" . $url . "</authorize_notification_url>";
$post[] = "<settle_notification_url>" . $url . "</settle_notification_url>";
$post[] = "<redirect_back_to_shop_url>" . $url . "</redirect_back_to_shop_url>";
$post[] = "</processing_control>";

// Database overrides
$post[] = "<database_overrides>";
$post[] = "<accepted_payment_methods>";
$post[] = "<payment_method>card</payment_method>";
$post[] = "</accepted_payment_methods>";

// Debug mode
$post[] = "<debug_mode>silent</debug_mode>";
// Test mode
$post[] = "<test_mode>" . $processor_data["params"]["test"] . "</test_mode>";
// Language
$post[] = "<language>" . $processor_data["params"]["language"] . "</language>";
$post[] = "</database_overrides>";
$post[] = "</payread_post_api_0_2>";

$post_data = base64_encode(implode($post));
$post_data_checksum = md5($processor_data["params"]["key_1"] . $post_data . $processor_data["params"]["key_2"]);
echo <<<EOT
<html>
<body onLoad="javascript: document.process.submit();">
<form method="post" action="https://secure.pay-read.se/PostAPI_V1/InitPayFlow" name="process">
<input type="hidden" name="payread_agentid" value='{$processor_data["params"]["agent_id"]}' />
<input type="hidden" name="payread_xml_writer" value='payread_php_0_2' />
<input type="hidden" name="payread_data" value='{$post_data}' />
<input type="hidden" name="payread_checksum" value='{$post_data_checksum}' />
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'Pay&Read server', $msg);
echo <<<EOT
	</form>
	<p><div align=center>{$msg}</div></p>
 </body>
</html>
EOT;
exit;
}
?>
