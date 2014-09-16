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
// $Id: eway_direct.php 9440 2010-05-13 13:04:30Z zeke $
//


if ( !defined('AREA') ) { die('Access denied'); }

$test_mode = ($processor_data["params"]["test"] == "Y") ? "TRUE" : "";
$request_script = ($processor_data["params"]["test"] == "Y") ? "gateway/xmltest/TestPage.asp" : "gateway/xmlpayment.asp";
$_order_id = $processor_data['params']['order_prefix'] . (($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id);

$payment_description = 'Products:';
// Products
if (!empty($order_info['items'])) {
	foreach ($order_info['items'] as $v) {
		$payment_description .= (preg_replace('/[^\w\s]/i', '', $v['product']) ."; amount=" . $v['amount'] . ";");
	}
}
// Gift Certificates
if (!empty($order_info['gift_certificates'])) {
	foreach ($order_info['gift_certificates'] as $v) {
		$payment_description .= ($v['gift_cert_code'] ."; amount=1;");
	}
}

$post = array();
$post[] = '<ewaygateway>';
$post[] = '<ewayCustomerID>' . $processor_data['params']['client_id'] . '</ewayCustomerID>';
$post[] = '<ewayTotalAmount>' . (100*$order_info['total']) . '</ewayTotalAmount>';
$post[] = '<ewayCustomerFirstName>' . $order_info['b_firstname'] . '</ewayCustomerFirstName>';
$post[] = '<ewayCustomerLastName>' . $order_info['b_lastname'] . '</ewayCustomerLastName>';
$post[] = '<ewayCustomerEmail>' . $order_info['email'] . '</ewayCustomerEmail>';
$post[] = '<ewayCustomerAddress>' . $order_info['b_address'] . '</ewayCustomerAddress>';
$post[] = '<ewayCustomerPostcode>' . $order_info['b_zipcode'] . '</ewayCustomerPostcode>';
$post[] = '<ewayCustomerInvoiceDescription>' . $payment_description . '</ewayCustomerInvoiceDescription>';
$post[] = '<ewayCustomerInvoiceRef>' . $_order_id . '</ewayCustomerInvoiceRef>';
$post[] = '<ewayCardHoldersName>' . $order_info['payment_info']['cardholder_name'] . '</ewayCardHoldersName>';
$post[] = '<ewayCardNumber>' . $order_info['payment_info']['card_number'] . '</ewayCardNumber>';
$post[] = '<ewayCardExpiryMonth>' . $order_info['payment_info']['expiry_month'] . '</ewayCardExpiryMonth>';
$post[] = '<ewayCardExpiryYear>' . $order_info['payment_info']['expiry_year'] . '</ewayCardExpiryYear>';
$post[] = '<ewayTrxnNumber></ewayTrxnNumber>';
$post[] = '<ewayOption1></ewayOption1>';
$post[] = '<ewayOption2></ewayOption2>';
$post[] = '<ewayOption3>' . $test_mode . '</ewayOption3>';
$post[] = '</ewaygateway>';

Registry::set('log_cut_data', array('ewayCardNumber', 'ewayCardExpiryMonth', 'ewayCardExpiryYear'));
list($a, $return) = fn_https_request("POST", "https://www.eway.com.au:443/".$request_script , $post, "", "", "text/xml");

preg_match("/<ewayTrxnStatus>(.*)<\/ewayTrxnStatus>/", $return, $result);
preg_match("/<ewayReturnAmount>(.*)<\/ewayReturnAmount>/", $return, $amount);

if($result[1] == "True" && fn_format_price($amount[1]) == fn_format_price($order_info['total'] * 100)) {
	$pp_response['order_status'] = 'P';
	preg_match("/<ewayAuthCode>(.*)<\/ewayAuthCode>/", $return, $authno);
	$pp_response["reason_text"] = "AuthNo: ".$authno[1];

} else {
	$pp_response['order_status'] = 'F';
	preg_match("/<ewayTrxnError>(.*)<\/ewayTrxnError>/", $return, $error);
	if (!empty($error[1])) {
		$pp_response["reason_text"] = "Error:" .$error[1];
	}
}
preg_match("/<ewayTrxnNumber>(.*)<\/ewayTrxnNumber>/", $return, $transaction_id);
if (!empty($transaction_id[1])) {
	$pp_response["transaction_id"] = @$transaction_id[1];
}
preg_match("/<ewayOption3>(.*)<\/ewayOption3>/", $return, $test);
if (!empty($test[1])) {
	$pp_response["reason_text"] .= "; This is a TEST transaction";
}
?>