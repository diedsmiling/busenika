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
// $Id: cardia.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {
	// Get the token
	$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $_REQUEST['order_id']);
	$processor_data = fn_get_payment_method_data($payment_id);
	// end

	$str = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
	<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:si=\"http://soapinterop.org/xsd\" xmlns:nu=\"http://testuri.org\">
	<SOAP-ENV:Body>
	<ReturnTransactionStatus xmlns=\"https://secure.cardia.no/Service/Card/Transaction/1.2/Transaction.asmx\">
		<merchantToken>" . $processor_data["params"]["merchanttoken"]. "</merchantToken>
		<merchantReference>" . $_REQUEST['order_id'] . "</merchantReference>
	</ReturnTransactionStatus>
	</SOAP-ENV:Body>
	</SOAP-ENV:Envelope>";

	list($headers, $return) = fn_https_request("POST", "https://secure.cardia.no:443/Service/Card/Transaction/1.2/Transaction.asmx",array($str), "","","text/xml","","","",array("SOAPAction: \"https://secure.cardia.no/Service/Card/Transaction/1.2/Transaction.asmx/ReturnTransactionStatus\""));

	preg_match("/<StatusCode>(\d+)<\/StatusCode>/", $return, $status);
	preg_match("/<ResponseCode>(\d+)<\/ResponseCode>/", $return, $resp);
	$pp_response = array();
	$response = array(
		"1" => "Transaction is approved",
		"2" => "Transaction not approved",
		"3" => "No transaction registered",
		"4" => "General error",
		"5" => "Transaction is approved, but voided afterwards. "
	);

	if (!$status) {
		$pp_response['order_status'] = 'D';
	
	} elseif ($status[1] =='1') {
		$pp_response['order_status'] = 'P';
		$pp_response['reason_text'] = fn_get_lang_var('order_id') . '-' . $_REQUEST['order_id'];
	
	} elseif ($status[1] == '2') {
		$pp_response['order_status'] = 'D';
		$pp_response['reason_text'] = $response['2'];
	
	} elseif ($status[1]=='3') {
		$pp_response['order_status'] = 'D';
		$pp_response['reason_text'] = $response['3'];
	
	} elseif ($status[1]=='4') {
		$pp_response['order_status'] = 'D';
		$pp_response['reason_text'] = $response['4'];
	
	} elseif ($status[1]=='5') {
		$pp_response['order_status'] = 'D';
		$pp_response['reason_text'] = fn_get_lang_var('order_id') . '-' . $_REQUEST['order_id'];
	}

	$pp_response['transaction_id'] = '';
	fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
	fn_order_placement_routines($_REQUEST['order_id']);

} else {

$str = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:si=\"http://soapinterop.org/xsd\">
<SOAP-ENV:Body>
<PrepareTransaction xmlns=\"https://secure.cardia.no/Service/Card/Transaction/1.2/Transaction.asmx\">
	<merchantToken>" . $processor_data["params"]["merchanttoken"] . "</merchantToken>
	<applicationIdentifier></applicationIdentifier>
	<store>" . $processor_data["params"]["store"] . "</store>
	<orderDescription>Order#" . $order_id . "</orderDescription>
	<merchantReference>" .(($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : "$order_id") . "</merchantReference>
	<currencyCode>" . $processor_data["params"]["currency"]. "</currencyCode>
	<successfulTransactionUrl>" . htmlspecialchars($processor_data["params"]["postbackurl"] . "&order_id=" . $order_id) . "</successfulTransactionUrl>
	<unsuccessfulTransactionUrl>" . htmlspecialchars($processor_data["params"]["postbackurl"] . "&order_id=" . $order_id) . "</unsuccessfulTransactionUrl>
	<authorizedNotAuthenticatedUrl></authorizedNotAuthenticatedUrl>
	<amount>" .str_replace("," , ". ", $order_info["total"]) . "</amount>
	<skipFirstPage>" . $processor_data["params"]["skipFirstPage"] . "</skipFirstPage>
	<skipLastPage>" . $processor_data["params"]["skipLastPage"] . "</skipLastPage>
	<isOnHold>" . $processor_data["params"]["isOnHold"] . "</isOnHold>
	<useThirdPartySecurity>" . $processor_data["params"]["useThirdPartySecurity"] . "</useThirdPartySecurity>
	<paymentMethod>3000</paymentMethod>
</PrepareTransaction>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>";

	$str = str_replace("\t", '', $str);
	$str = str_replace("\n", '', $str);

	list($headers, $response) = fn_https_request("POST","https://secure.cardia.no:443/Service/Card/Transaction/1.2/Transaction.asmx", array($str), "", "", "text/xml", "", "", "", array("SOAPAction: \"https://secure.cardia.no/Service/Card/Transaction/1.2/Transaction.asmx/PrepareTransaction\""));

	if (preg_match("/Address>([^<]+)<\/Address/", $response, $a_addr) && preg_match("/ReferenceGuid>([^<]+)<\/ReferenceGuid/", $response, $a_guid)) {
		$addr = $a_addr[1];
		$guid = $a_guid[1];

		if (!empty($guid) && !empty($addr)) {
			$msg = fn_get_lang_var('text_cc_processor_connection');
			$msg = str_replace('[processor]', 'Cardia Shop', $msg);

			$cardia_request=<<<EOT
			<html>
			<body onload="document.process.submit();">
			<form action="{$addr}" name="process" method="get">
			<input type="hidden" name="guid" value="{$guid}">
			</form>
			<div align=center>{$msg}</div>
			</body>
			</html>
EOT;
			echo $cardia_request;
			fn_flush();
			die();
		} else {
			$pp_response['order_status'] = 'F';
		}

	} elseif (preg_match("/Error>([^<]+)<\/Error/", $response, $matches)) {
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = $matches[1];
	}
}

?>
