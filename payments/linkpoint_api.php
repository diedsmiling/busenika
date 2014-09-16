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
// $Id: linkpoint_api.php 10229 2010-07-27 14:21:39Z 2tl $
//
if ( !defined('AREA') ) { die('Access denied'); }

$processor_error['avs'] = array(
	"YY" => "Address matches, zip code matches",
	"YN" => "Address matches, zip code does not match",
	"YX" => "Address matches, zip code comparison not available",
	"NY" => "Address does not match, zip code matches",
	"XY" => "Address comparison not available, zip code matches",
	"NN" => "Address comparison does not match, zip code does not match",
	"NX" => "Address does not match, zip code comparison not available",
	"XN" => "Address comparison not available, zip code does not match",
	"XX" => "Address comparisons not available, zip code comparison not available",
);

$processor_error['cvv'] = array(
	"M" => "Card Code Match",
	"N" => "Card code does not match",
	"P" => "Not processed",
	"S" => "Merchant has indicated that the card code is not present on the card",
	"U" => "Issuer is not certified and/or has not provided encryption keys"
);

$host = $processor_data['params']['secure_host'];
$port = $processor_data['params']['secure_port'];
$sert = DIR_ROOT . '/payments/certificates/' . $processor_data['params']['cert_path'];
$o_prefix = $processor_data['params']['order_prefix'];

$addrnum = preg_replace("/[^\d]/", "", $order_info['b_address']);
$test_mode = $processor_data['params']['test'];

$post_data = array();
$post_data[] = '<order><orderoptions>';
$post_data[] = '<ordertype>' . $processor_data['params']['transaction_type'] . '</ordertype>';
$post_data[] = '<result>' . $test_mode . '</result></orderoptions>';

$post_data[] = '<creditcard><cardnumber>' . $order_info['payment_info']['card_number'] . '</cardnumber>';
$post_data[] = '<cardexpmonth>' . $order_info['payment_info']['expiry_month'] . '</cardexpmonth>';
$post_data[] = '<cardexpyear>' . $order_info['payment_info']['expiry_year'] . '</cardexpyear>';
$post_data[] = '<cvmvalue>' . $order_info['payment_info']['cvv2'] . '</cvmvalue>';
$post_data[] = '<cvmindicator>' . $processor_data['params']['cvm_check'] . '</cvmindicator></creditcard>';

$post_data[] = '<merchantinfo><configfile>' . $processor_data['params']['store'] . '</configfile>';
$post_data[] = '<keyfile>' . $processor_data['params']['cert_path'] . '</keyfile>';
$post_data[] = '<host>' . $processor_data['params']['secure_host'] . '</host><port>' . $processor_data['params']['secure_port'] . '</port></merchantinfo>';

$post_data[] = '<payment><chargetotal>' . $order_info['total'] . '</chargetotal></payment>';

$post_data[] = '<billing>';
$post_data[] = '<name>' . $order_info['b_firstname'] . ' ' . $order_info['b_lastname'] . '</name>';
$post_data[] = '<address1>' . $order_info['b_address'] . '</address1>';
$post_data[] = '<addrnum>' . $addrnum . '</addrnum>';
$post_data[] = '<city>' . $order_info['b_city'] . '</city>';
$post_data[] = '<state>' . $order_info['b_state'] . '</state>';
$post_data[] = '<zip>' . $order_info['b_zipcode'] . '</zip>';
$post_data[] = '<country>' . $order_info['b_country'] . '</country>';
$post_data[] = '<phone>' . $order_info['phone'] . '</phone>';
$post_data[] = '<email>' . $order_info['email'] . '</email></billing>';

$post_data[] = '<transactiondetails>';
$post_data[] = '<oid>' . $o_prefix . (($order_info['repaid']) ? ($order_id  . '_' . $order_info['repaid']) : $order_id) . '</oid>';
$post_data[] = '</transactiondetails>';
$post_data[] = '</order>';

Registry::set('log_cut_data', array('cardnumber', 'cardexpmonth', 'cardexpyear', 'cvmvalue'));
list($a, $__response) = fn_https_request("POST","https://$host:$port/LSGSXML",$post_data,"","","application/x-www-form-urlencoded","",$sert,$sert);

$pp_response = array();
if (preg_match("/<r_approved>(.*)<\/r_approved>/", $__response, $out)) {
	if ($out[1] == "APPROVED") {	
		$pp_response['order_status'] = 'P';
		if (preg_match("/<r_code>(.*)<\/r_code>/", $__response, $out)) {
			if (preg_match("/^(\w{6})(\w{10}):(\w{2})(\w)(\w):(.*):$/", $out[1], $response_data)) {
				$pp_response['reason_text'] = "Approval number: " . $response_data[1] . "; Reference number: " . $response_data[2] . "; Leaseline transaction identifier: " . $response_data[6];
				$pp_response['transaction_id'] = (!empty($response_data[6])) ? $response_data[6] : '';
				$pp_response['descr_avs'] = (!empty($response_data[3])) ? $processor_error['avs'][$response_data[3]] : '';
				$pp_response['descr_cvv']  = (!empty($response_data[5])) ? $processor_error['cvv'][$response_data[5]] : '';
			}
		}
	} else {
		$pp_response['order_status'] = 'D';
		$pp_response['reason_text'] = "[" . $out[1] . "] ";
		if (preg_match("/<r_error>(.*)<\/r_error>/", $__response, $response_data)) {
			$pp_response['reason_text'] .= $response_data[1];
		}
	}
} else {
	$pp_response['order_status'] = 'F';
}

if (preg_match("/<r_authresponse>(.*)<\/r_authresponse>/",$__response,$response_data)) {
	$pp_response['reason_text'].= " (AuthResponce: " . $response_data[1] . ")";
}

if (preg_match("/<r_message>(.*)<\/r_message>/",$__response,$response_data)) {
	$pp_response['reason_text'].= " (Message: " . $response_data[1] . ")";
}

?>