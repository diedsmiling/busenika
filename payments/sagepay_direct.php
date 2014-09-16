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
// $Id: sagepay_direct.php 10417 2010-08-13 08:58:57Z klerik $
//

if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_REQUEST['PaRes']) && !empty($_REQUEST['MD'])) {

	DEFINE ('AREA', 'C');
	DEFINE ('AREA_NAME' ,'customer');

	require './../prepare.php';
	require './../init.php';

	$post[] = 'MD=' . $_REQUEST['MD'];
	$post[] = 'PARes=' . $_REQUEST['PaRes'];
	$secure_verified_3d = true;
	$order_id = $_REQUEST['order_id'];
}

if (!defined('AREA') ) { die('Access denied'); }

$_SESSION['already_posted'] = empty($_SESSION['already_posted']) ? false : $_SESSION['already_posted'];
$already_posted = & $_SESSION['already_posted'];

if (!empty($secure_verified_3d) && empty($already_posted)) {
	if ($_REQUEST['payment_mode'] == 'Y') {
		$post_address = 'https://test.sagepay.com/gateway/service/direct3dcallback.vsp';
	} elseif ($_REQUEST['payment_mode'] == 'N') {
		$post_address = 'https://live.sagepay.com/gateway/service/direct3dcallback.vsp';
	} elseif ($_REQUEST['payment_mode'] == 'S') {
		$post_address = 'https://test.sagepay.com/Simulator/VSPDirectCallback.asp';
	}

	list($a, $return) = fn_https_request('POST', $post_address, $post);
	$already_posted = true;

} else {

	$pp_merch = $processor_data['params']['vendor'];
	$pp_curr = $processor_data['params']['currency'];

	if ($processor_data['params']['testmode'] == 'Y') {
		$post_address = 'https://test.sagepay.com/gateway/service/vspdirect-register.vsp';	
	} elseif ($processor_data['params']['testmode'] == 'N') {
		$post_address = 'https://live.sagepay.com/gateway/service/vspdirect-register.vsp';
	} elseif ($processor_data['params']['testmode'] == 'S') {
		$post_address = 'https://test.sagepay.com/Simulator/VSPDirectGateway.asp';
	}

	$already_posted = false;

	switch ($order_info['payment_info']['card']) {
	case 'vis':
		$card_type = 'VISA';
		break;
	case 'vie':
		$card_type = 'UKE';
		break;
	case 'mcd':
		$card_type = 'MC';
		break;
	case 'amx':
		$card_type = 'AMEX';
		break;
	case 'jsb':
		$card_type = 'JCB';
		break;
	case 'dnc':
		$card_type = 'DINERS';
		break;
	case 'enr':
		$card_type = 'DINERS';
		break;
	case 'sol':
		$card_type = 'SOLO';
		break;
	case 'swi':
		$card_type = 'SWITCH';
		break;
	case 'mae':
		$card_type = 'MAESTRO';
		break;
	case 'las':
		$card_type = 'LASER';
		break;
	}

	$post = array();
	$post[] = 'VPSProtocol=2.23';
	$post[] = 'TxType=' . $processor_data['params']['transaction_type'];
	$post[] = 'Vendor=' . $pp_merch;
	$post[] = 'VendorTxCode=' . ((!empty($processor_data['params']['order_prefix']) ? $processor_data['params']['order_prefix'] : 'O') . '-' . (($order_info['repaid']) ? ($order_info['order_id'] . '-' . $order_info['repaid']) : $order_info['order_id'])) . '-' . fn_date_format(time(), '%H_%M_%S');
	$post[] = 'Amount=' . $order_info["total"];
	$post[] = 'Currency=' . $pp_curr;
	$post[] = 'Description=Your Cart';
	$post[] = 'CardHolder=' . $order_info['payment_info']['cardholder_name'];
	$post[] = 'CardNumber=' . $order_info['payment_info']['card_number'];
	$post[] = 'ExpiryDate=' . $order_info['payment_info']['expiry_month'] . $order_info['payment_info']['expiry_year'];
	if ($card_type == 'SOLO' || $card_type == 'MAESTRO' || $card_type == 'SWITCH' || $card_type == 'LASER' || $card_type == 'AMEX') {
		$post[] = 'StartDate=' . $order_info['payment_info']['start_month'] . $order_info['payment_info']['start_year'];
	}
	if ($card_type == 'SOLO' || $card_type == 'MAESTRO' || $card_type == 'SWITCH' || $card_type == 'LASER') {
		$post[] = 'IssueNumber=' . $order_info['payment_info']['issue_number'];
	}
	$post[] = 'CV2=' . $order_info['payment_info']['cvv2'];
	$post[] = 'CardType=' . $card_type;
	$post[] = 'Apply3DSecure=0';

	$post[] = 'BillingAddress1=' . $order_info['b_address'];
	$post[] = 'BillingAddress2=' . $order_info['b_address_2'];
	$post[] = 'BillingPostCode=' . $order_info['b_zipcode'];
	$post[] = 'BillingCountry=' . $order_info['b_country'];
	if ($order_info['b_country'] == 'US') { // state is for US customers only
		$post[] = 'BillingState=' . $order_info['b_state'];
	}
	$post[] = 'BillingCity=' . $order_info['b_city'];
	$post[] = 'BillingFirstnames=' . $order_info['b_firstname'];
	$post[] = 'BillingSurname=' . $order_info['b_lastname'];

	$post[] = 'DeliveryAddress1=' . $order_info['s_address'];
	$post[] = 'DeliveryAddress2=' . $order_info['s_address_2'];
	$post[] = 'DeliveryPostCode=' . $order_info['s_zipcode'];
	$post[] = 'DeliveryCountry=' . $order_info['s_country'];

	if ($order_info['s_country'] == 'US') {// state is for US customers only
		$post[] = 'DeliveryState=' . $order_info['s_state'];
	}
	$post[] = 'DeliveryCity=' . $order_info['s_city'];
	$post[] = 'DeliveryFirstnames=' . $order_info['s_firstname'];
	$post[] = 'DeliverySurname=' . $order_info['s_lastname'];

	$post[] = 'CustomerName=' . $order_info['firstname'] . ' ' . $order_info['lastname'];
	$post[] = 'ContactNumber=' . $order_info['phone'];
	$post[] = 'ContactFax=' . $order_info['fax'];
	$post[] = 'CustomerEMail=' . $order_info['email'];

	// Form Cart products
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
			$products_string .= ":" . str_replace(':', ' ', $v['product']) . ':' . $v['amount'] . ':' . fn_format_price(($v['subtotal'] - fn_external_discounts($v)) / $v['amount']) . ':::' . fn_format_price($v['subtotal'] - fn_external_discounts($v));
		}
	}
	if (!empty($order_info['gift_certificates'])) {
		foreach ($order_info['gift_certificates'] as $v) {
			$v['amount'] = (!empty($v['extra']['exclude_from_calculate'])) ? 0 : $v['amount'];
			$products_string .= ':' . str_replace(':', ' ', $v['gift_cert_code']) . ':1:' . fn_format_price($v['amount']) . ':::' . fn_format_price($v['amount']);
		}
	}
	if (floatval($order_info['payment_surcharge'])) {
		$products_string .= ':Payment surcharge:---:---:---:---:' . fn_format_price($order_info['payment_surcharge']);
		$strings ++;
	}
	if (fn_order_shipping_cost($order_info)) {
		$products_string .= ':Shipping cost:---:---:---:---:' . fn_format_price($order_info['shipping_cost']);
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

	$post[] = 'Basket=' . $strings . $products_string;

	$post[] = 'ClientIPAddress=' . $_SERVER['REMOTE_ADDR'];

	Registry::set('log_cut_data', array('CardNumber', 'ExpiryDate', 'StartDate', 'IssueNumber', 'CV2'));
	list($a, $return) = fn_https_request('POST', $post_address, $post);
}

$ret = str_replace("\r\n", '&', $return);

$ret_arr = explode('&', $ret);
$response = array();
foreach ($ret_arr as $ret) {
	if (preg_match('/([^=]+?)=(.+)/', $ret, $matches)) {
		$response[$matches[1]] = trim($matches[2]);
	}
}

if ($response['Status'] == '3DAUTH') {

$payment_mode = $processor_data['params']['testmode'];

$https_location = Registry::get('config.https_location');

echo <<<EOT
<html>
<body onLoad="document.process.submit();">
	<form action="{$response['ACSURL']}" method="post" name="process">
		<input type="hidden" name="PaReq" value="{$response['PAReq']}"/>
		<input type="hidden" name="TermUrl" value="{$https_location}/payments/sagepay_direct.php?order_id={$order_info['order_id']}&payment_mode={$payment_mode}"/>
		<input type="hidden" name="MD" value="{$response['MD']}"/>
		<NOSCRIPT>
		<center><p>Please click button below to Authenticate your card</p><input type="submit" value="Authenticate"/></p></center>
		</NOSCRIPT>
		</form>
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', '3D Secure Server', $msg);
echo <<<EOT
		<p><div align=center>{$msg}</div></p>
	</form>
</body>
</html>
EOT;
exit;

} elseif ($response['Status'] == 'OK' || $response['Status'] == 'AUTHENTICATED'  || $response['Status'] == 'REGISTERED') {
	$pp_response['order_status'] = 'P';
	if (!empty($response['TxAuthNo'])) $pp_response['reason_text'] = 'AuthNo: '.@$response['TxAuthNo'];
	if (!empty($response['SecurityKey'])) {
		$pp_response['reason_text'] = 'SecurityKey: '.$response['SecurityKey'];
	} else {
		$pp_response['reason_text'] = '';
	}
} else {
	$pp_response['order_status'] = 'F';
	$pp_response['reason_text'] = '';
}

if (!empty($response['Status'])) {
	$pp_response['reason_text'] = 'Status: ' . @$response['StatusDetail'] . ' (' . $response['Status'] . ') ';
}
if (!empty($response['VPSTxId'])) {
	$pp_response['transaction_id'] = $response['VPSTxId'];
}
if (!empty($response['AVSCV2']) && $response['AVSCV2'] != 'DATA NOT CHECKED') {
	$pp_response['reason_text'] .= ' (AVS/CVV2: {' . $response['AVSCV2'] . '})  ';
}
if (!empty($response['AddressResult']) && $response['AddressResult'] != 'NOTPROVIDED') {
	$pp_response['reason_text'] .= ' (Address: {' . $response['AddressResult'] . '})  ';
}
if (!empty($response['PostCodeResult']) && $response['PostCodeResult'] != 'NOTPROVIDED') {
	$pp_response['reason_text'] .= ' (PostCode: {' . $response['PostCodeResult'] . '})  ';
}
if (!empty($response['CV2Result']) && $response['CV2Result'] != 'NOTPROVIDED') {
	$pp_response['reason_text'] .= ' (CV2: {' . $response['CV2Result'] . '})  ';
}
if (!empty($response['3DSecureStatus'])) {
	$pp_response['reason_text'] .= ' (3D Result: {' . $response['3DSecureStatus'] . '})  ';
}

if (!empty($secure_verified_3d) && !empty($order_id) && fn_check_payment_script('sagepay_direct.php', $order_id) == true) {
	unset($_SESSION['already_posted']);

	fn_finish_payment($order_id, $pp_response, false);
	fn_order_placement_routines($order_id);
}

?>