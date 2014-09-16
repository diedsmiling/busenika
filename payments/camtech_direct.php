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
// $Id: camtech_direct.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$test_mode = ($processor_data["params"]["test"] == "Y") ? "TRUE" : "";
$request_script = ($processor_data["params"]["test"] == "Y") ? "www.securepay.com.au/test/payment" : "www.securepay.com.au/xmlapi/payment";
$_order_id = $processor_data['params']['order_prefix'] . (($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id);
$camtech_password = $processor_data['params']['password'];
$camtech_username = $processor_data['params']['client_id'];

$timestamp = fn_camtech_getgmttimestamp();

$vars = 
"<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
"<SecurePayMessage>" .
	"<MessageInfo>" .
		"<messageID>8af793f9af34bea0cf40f5fb5c630c</messageID>" .
		"<messageTimestamp>" .urlencode($timestamp). "</messageTimestamp>" .
		"<timeoutValue>60</timeoutValue>" .
		"<apiVersion>xml-4.2</apiVersion>" .
	"</MessageInfo>" .
	"<MerchantInfo>" .
		"<merchantID>" . $camtech_username . "</merchantID>" .
		"<password>" . $camtech_password . "</password>" .
	"</MerchantInfo>" .
	"<RequestType>Payment</RequestType>" .
	"<Payment>" .
		"<TxnList count=\"1\">" .
			"<Txn ID=\"1\">" .
				"<txnType>0</txnType>" .
				"<txnSource>23</txnSource>" .
				"<amount>" . (100 * $order_info['total']) . "</amount>" .
				"<purchaseOrderNo>" . $_order_id . "</purchaseOrderNo>" .
				"<CreditCardInfo>" .
					"<cardNumber>" . $order_info['payment_info']['card_number'] . "</cardNumber>" .
					"<expiryDate>" . $order_info['payment_info']['expiry_month'] . "/" . $order_info['payment_info']['expiry_year'] . "</expiryDate>" .
					"<cvv>" . $order_info['payment_info']['cvv2'] . "</cvv>" .
				"</CreditCardInfo>" .
			"</Txn>" .
		"</TxnList>" .
	"</Payment>" .
"</SecurePayMessage>";

Registry::set('log_cut_data', array('cardNumber', 'expiryDate', 'cvv'));
list($a, $response) = fn_https_request('POST', $request_script, $vars, '', '', 'text/xml');

$xmlres = fn_camtech_makexmltree($response);

$status_code = trim($xmlres['SecurePayMessage']['Status']['statusCode']);
$status_description = trim($xmlres['SecurePayMessage']['Status']['statusDescription']);
$approved = !empty($xmlres['SecurePayMessage']['Payment']['TxnList']['Txn']['approved']) ? trim($xmlres['SecurePayMessage']['Payment']['TxnList']['Txn']['approved']) : 'No';
$response_code = !empty($xmlres['SecurePayMessage']['Payment']['TxnList']['Txn']['responseCode']) ? trim($xmlres['SecurePayMessage']['Payment']['TxnList']['Txn']['responseCode']) : '';
$response_text = !empty($xmlres['SecurePayMessage']['Payment']['TxnList']['Txn']['responseText']) ? trim($xmlres['SecurePayMessage']['Payment']['TxnList']['Txn']['responseText']) : '';
$txn_id = !empty($xmlres['SecurePayMessage']['Payment']['TxnList']['Txn']['txnID']) ? trim($xmlres['SecurePayMessage']['Payment']['TxnList']['Txn']['txnID']) : '';

if ($status_code == "000" && $approved == "Yes"){
	$pp_response['order_status'] = 'P';
	$pp_response["reason_text"] = "Response Code: " . $response_code . ", Trans ID: " . $txn_id;
} else {
	$pp_response['order_status'] = 'F';
	$pp_response["reason_text"] = "Status Code:" . $status_code . ", Description: " . $status_description . ", Response Code: ". $response_code;
}

function fn_camtech_getgmttimestamp()
{
	$stamp = date('YmdGis') . '000+1000';
	return $stamp;
}

function fn_camtech_makexmltree($data) 
{
	$output = array();
	$parser = xml_parser_create();

	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $data, $values, $tags);
	xml_parser_free($parser);

	$hash_stack = array();

	foreach ($values as $key => $val) {
		switch ($val['type']) {
			case 'open':
				array_push($hash_stack, $val['tag']);
				break;

			case 'close':
				array_pop($hash_stack);
				break;

			case 'complete':
				array_push($hash_stack, $val['tag']);
				eval("\$output['" . implode($hash_stack, "']['") . "'] = \"{$val['value']}\";");
				array_pop($hash_stack);
			break;
		}
	}

	return $output;
}

?>