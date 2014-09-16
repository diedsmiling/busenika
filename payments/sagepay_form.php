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
// $Id: sagepay_form.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {

	// Get the password
	$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $_REQUEST['order_id']);
	$processor_data = fn_get_payment_method_data($payment_id);

	$result = "&" . fn_sagepay_simplexor(fn_sagepay_base64decode($_REQUEST['crypt']), $processor_data["params"]["password"])."&";


	preg_match("/Status=(.+)&/U", $result, $a);

	if(trim($a[1]) == "OK") {
		$pp_response['order_status'] = ($processor_data["params"]["transaction_type"] == 'PAYMENT') ? 'P' : 'O';

		if (preg_match("/TxAuthNo=(.+)&/U", $result, $_authno)) {
			$pp_response["reason_text"] = "AuthNo: " . $_authno[1];
		}

		if (preg_match("/VPSTxID={(.+)}/U", $result, $transaction_id)) {
			$pp_response["transaction_id"] = $transaction_id[1];
		}

	} else {
		$pp_response['order_status'] = 'F';
		if (preg_match("/StatusDetail=(.+)&/U", $result, $stat)) {
			$pp_response["reason_text"] = "Status: " . trim($stat[1]) . " (".trim($a[1]) . ") ";
		}
	}

	if (preg_match("/AVSCV2=(.*)&/U", $result, $avs)) {
		$pp_response['descr_avs'] = $avs[1];
	}

	fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
	fn_order_placement_routines($_REQUEST['order_id']);

} else {

	if ($processor_data['params']['testmode'] == 'Y') {
		$post_address = "https://test.sagepay.com/gateway/service/vspform-register.vsp";	
	} elseif ($processor_data['params']['testmode'] == 'N') {
		$post_address = "https://live.sagepay.com/gateway/service/vspform-register.vsp";
	} elseif ($processor_data['params']['testmode'] == 'S') {
		$post_address = "https://test.sagepay.com/Simulator/VSPFormGateway.asp";
	}
	
	$post["VPSProtocol"] = "2.23";
	$post["TxType"] = $processor_data["params"]["transaction_type"];
	$post["Vendor"] = htmlspecialchars($processor_data["params"]["vendor"]);

	$post_encrypted = 'VendorTxCode=' . $processor_data['params']['order_prefix'] . (($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id) . "&";
	$post_encrypted .= 'Amount=' . $order_info['total'] . '&';
	$post_encrypted .= 'Currency=' . $processor_data['params']['currency'] . '&';
	$post_encrypted .= 'Description=Payment for Order' . $order_id . '&';
	$post_encrypted .= 'SuccessURL=' . Registry::get('config.http_location') . "/$index_script?dispatch=payment_notification.notify&payment=sagepay_form&order_id=$order_id" . '&';
	$post_encrypted .= 'FailureURL=' . Registry::get('config.http_location') . "/$index_script?dispatch=payment_notification.notify&payment=sagepay_form&order_id=$order_id" . '&';
	$post_encrypted .= 'CustomerEMail=' . $order_info['email'] . '&';
	$post_encrypted .= 'VendorEmail=' . Registry::get('settings.Company.company_orders_department') . '&';
	$post_encrypted .= 'CustomerName=' . $order_info['firstname'] . ' ' .$order_info['lastname'] . '&';
	$post_encrypted .= 'ContactNumber=' . $order_info['phone'] . '&';
	$post_encrypted .= 'ContactFax=' . $order_info['fax'] . '&';

	// Billing address
	$post_encrypted .= 'BillingAddress1=' . $order_info['b_address'] . '&';
	if (!empty($order_info['b_address_2'])) {
		$post_encrypted .= 'BillingAddress2=' . $order_info['b_address_2'] . '&';
	}
	$post_encrypted .= 'BillingPostCode=' . $order_info['b_zipcode'] . '&';
	$post_encrypted .= 'BillingCountry=' . $order_info['b_country'] . '&';
	if ($order_info['b_country'] == 'US') {
		$post_encrypted .= 'BillingState=' . $order_info['b_state'] . '&';
	}
	$post_encrypted .= 'BillingCity=' . $order_info['b_city'] . '&';
	$post_encrypted .= 'BillingFirstnames=' . $order_info['b_firstname'] . '&';
	$post_encrypted .= 'BillingSurname=' . $order_info['b_lastname'] . '&';

	// Shipping Address
	$post_encrypted .= 'DeliveryAddress1=' . $order_info['s_address'] . '&';
	if (!empty($order_info['s_address_2'])) {
		$post_encrypted .= 'DeliveryAddress2=' . $order_info['s_address_2'] . '&';
	}
	$post_encrypted .= 'DeliveryPostCode=' . $order_info['s_zipcode'] . '&';
	$post_encrypted .= 'DeliveryCountry=' . $order_info['s_country'] . '&';
	if ($order_info['s_country'] == 'US') {
		$post_encrypted .= 'DeliveryState=' . $order_info['s_state'] . '&';
	}
	$post_encrypted .= 'DeliveryCity=' . $order_info['s_city'] . '&';
	$post_encrypted .= 'DeliveryFirstnames=' . $order_info['s_firstname'] . '&';
	$post_encrypted .= 'DeliverySurname=' . $order_info['s_lastname'] . '&';

	// Form Ordered products
	$strings = 0;
	$products_string = '';
	if (!empty($order_info['items']) && is_array($order_info['items'])) {
		$strings += count($order_info['items']);
	}
	if (!empty($order_info['gift_certificates']) && is_array($order_info['gift_certificates'])) {
		$strings += count($order_info['gift_certificates']);
	}

	if (!empty($order_info['items'])) {
		foreach ($order_info['items'] as $v) {
	//		$v['discount'] = empty($v['discount']) ? 0 : $v['discount'];
			$products_string .= ":".str_replace(":", " ", $v['product']).":".$v['amount'].":".fn_format_price(($v['subtotal'] - fn_external_discounts($v)) / $v['amount']).":::".fn_format_price($v['subtotal'] - fn_external_discounts($v));
		}
	}
	if (!empty($order_info['gift_certificates'])) {
		foreach ($order_info['gift_certificates'] as $v) {
			$v['amount'] = (!empty($v['extra']['exclude_from_calculate'])) ? 0 : $v['amount'];
			$products_string .= ":".str_replace(":", " ", $v['gift_cert_code']).":1:".fn_format_price($v['amount']).":::".fn_format_price($v['amount']);
		}
	}
	if (floatval($order_info['payment_surcharge'])) {
		$products_string .= ":Payment surcharge:---:---:---:---:".fn_format_price($order_info['payment_surcharge']);
		$strings ++;
	}
	if (fn_order_shipping_cost($order_info)) {
		$products_string .= ":Shipping cost:---:---:---:---:".fn_order_shipping_cost($order_info);
		$strings ++;
	}

	if (floatval($order_info['subtotal_discount'])) {
		$desc = fn_get_lang_var('order_discount');
		$pr = fn_format_price($order_info['subtotal_discount']);
		$products_string .= ":{$desc}:---:---:---:---:-" . fn_format_price($order_info['subtotal_discount']);
		$strings ++;
	}

	if (!empty($order_info['taxes']) && Registry::get('settings.General.tax_calculation') == 'subtotal') {
		foreach ($order_info['taxes'] as $tax_id => $tax) {
			if ($tax['price_includes_tax'] == 'N') {
				$desc = $tax['description'];
				$products_string .= ":{$desc}:---:---:---:---:" . fn_format_price($tax['tax_subtotal']);
				$strings ++;
			}
		}
	}

	$post_encrypted .= "Basket=" . $strings . $products_string;

	$post["Crypt"] = base64_encode(fn_sagepay_simplexor($post_encrypted, $processor_data["params"]["password"]));
	$post["Crypt"] = htmlspecialchars($post["Crypt"]);

	$msg = fn_get_lang_var('text_cc_processor_connection');
	$msg = str_replace('[processor]', 'SagePay Server', $msg);


echo <<<EOT
<html>
<body onLoad="document.process.submit();">
  <form action="{$post_address}" method="POST" name="process">
	<INPUT type=hidden name="VPSProtocol" value="{$post['VPSProtocol']}">
	<INPUT type=hidden name="Vendor" value="{$post['Vendor']}">
    <INPUT type=hidden name="TxType" value="{$post['TxType']}">
    <INPUT type=hidden name="Crypt" value="{$post['Crypt']}">
	<p>
	<div align=center>{$msg}</div>
	</p>
 </body>
</html>
EOT;
}

exit;

//
// ---------------- Additional functions ------------
//
function fn_sagepay_simplexor($InString, $Key) 
{
	$KeyList = array();
	$output = '';

	for($i = 0; $i < strlen($Key); $i++){
		$KeyList[$i] = ord(substr($Key, $i, 1));
	}
	for($i = 0; $i < strlen($InString); $i++) {
		$output.= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
	}

	return $output;
}

function fn_sagepay_base64decode($scrambled) 
{
	// Initialise output variable
	$output = '';

	// Fix plus to space conversion issue
	$scrambled = str_replace(' ', '+', $scrambled);

	// Return the result
	return base64_decode($scrambled);
}

?>