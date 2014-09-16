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
// $Id: piraeus.php 10601 2010-09-03 12:26:54Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {

	if ($mode == 'notify') {

		$order_id = (strpos($_REQUEST['MerchantReference'], '_')) ? substr($_REQUEST['MerchantReference'], 0, strpos($_REQUEST['MerchantReference'], '_')) : $_REQUEST['MerchantReference'];
		$order_info = fn_get_order_info($order_id);
		$processor_data = fn_get_payment_method_data($order_info['payment_id']);

		$pp_response['SupportReferenceID'] = $_REQUEST['SupportReferenceID'];
		$pp_response['ResultCode'] = $_REQUEST['ResultCode'];
		$pp_response['ResultDescription'] = $_REQUEST['ResultDescription'];
		$pp_response['StatusFlag'] = $_REQUEST['StatusFlag'];
		$pp_response['ResponseCode'] = $_REQUEST['ResponseCode'];
		$pp_response['ResponseDescription'] = $_REQUEST['ResponseDescription'];
		$pp_response['TransactionDateTime'] = $_REQUEST['TransactionDateTime'];
		$pp_response['TransactionId'] = $_REQUEST['TransactionId'];
		$pp_response['CardType'] = $_REQUEST['CardType'];
		$pp_response['PackageNo'] = $_REQUEST['PackageNo'];
		$pp_response['ApprovalCode'] = $_REQUEST['ApprovalCode'];

		$pp_response['RetrievalRef'] = $_REQUEST['RetrievalRef'];
		$pp_response['AuthStatus'] = $_REQUEST['AuthStatus'];
		$pp_response['Parameters'] = $_REQUEST['Parameters'];

		if ($_REQUEST['ResultCode'] == 0) {
			if ( $_REQUEST['StatusFlag'] == 'Success' && in_array($_REQUEST['ResponseCode'], array('00', '08', '10', '11', '16')) ) {

				$cart_hashkey_str = $order_info['payment_info']['TranTicket'] . $processor_data['params']['posid']. $processor_data['params']['acquirerid'] . $_REQUEST['MerchantReference'] . $_REQUEST['ApprovalCode'] . $_REQUEST['Parameters'] . $_REQUEST['ResponseCode'] . $_REQUEST['SupportReferenceID'] . $_REQUEST['AuthStatus'] . $_REQUEST['PackageNo'] . $_REQUEST['StatusFlag'];
				$cart_hashkey = strtoupper(hash( 'sha256', $cart_hashkey_str ));

				if ($cart_hashkey == $_REQUEST['HashKey']) {
					$pp_response['order_status'] = 'P';
					$pp_response['reason_text'] = 'Processed';
				} else {
					$pp_response['order_status'] = 'F';
					$pp_response['reason_text'] = 'Hash value is incorrect';
				}
			} elseif ( $_REQUEST['StatusFlag'] == 'Failure' ) {
				$pp_response['order_status'] = 'F';
				$pp_response['reason_text'] = 'Failed';
			} elseif ( $_REQUEST['StatusFlag'] == 'Asynchronous' ) {
				$pp_response['order_status'] = 'O';
				$pp_response['reason_text'] = 'Asynchronous';
			}

		} else {
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = $_REQUEST['ResultDescription'];
		}

		if (fn_check_payment_script('piraeus.php', $order_id)) {
			fn_finish_payment($order_id, $pp_response, false);
		}

		fn_order_placement_routines($order_id);
	}

	if ($mode == 'cancel') {

		if (!empty($_SESSION['stored_piraeus_orderid'])) {
			$order_id = $_SESSION['stored_piraeus_orderid'];
			unset($_SESSION['stored_piraeus_orderid']);
		} else {
			$prefix = ((Registry::get('settings.General.secure_auth') == 'Y') && (AREA == 'C')) ? Registry::get('config.https_location') . '/' : '';
			fn_redirect($prefix . INDEX_SCRIPT . "?dispatch=checkout.checkout", true);
		}

		$pp_response['order_status'] = 'N';
		$pp_response["reason_text"] = fn_get_lang_var('text_transaction_cancelled');

		if (fn_check_payment_script('piraeus.php', $order_id)) {
			fn_finish_payment($order_id, $pp_response, false);
		}

		fn_order_placement_routines($order_id);
	}

} else {

	$ticketing_url = "https://paycenter.winbank.gr/services/tickets/issuer.asmx?WSDL";

	$ticketing_data = Array (
		'AcquirerId' => $processor_data['params']['acquirerid'],
		'MerchantId' => $processor_data['params']['merchantid'],
		'PosId' => $processor_data['params']['posid'],
		'Username' => $processor_data['params']['username'],
		'Password' => md5($processor_data['params']['password']),
		'RequestType' => $processor_data['params']['requesttype'],
		'CurrencyCode' => $processor_data['params']['currencycode'],
		'MerchantReference' => (($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id),
		'Amount' => $order_info['total'],
		'Installments' => 0,
		'ExpirePreauth' => (($processor_data['params']['requesttype'] == '00') ? $processor_data['params']['expirepreauth'] : '0'),
		'Parameters' => ''
	);

	$str = <<<EOT
	<?xml version="1.0" encoding="utf-8"?>
	<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		<soap:Body>
			<IssueNewTicket xmlns="http://piraeusbank.gr/paycenter/redirection">
				<Request>
					<Username>{$ticketing_data['Username']}</Username>
					<Password>{$ticketing_data['Password']}</Password>
					<PosId>{$ticketing_data['PosId']}</PosId>
					<MerchantId>{$ticketing_data['MerchantId']}</MerchantId>
					<AcquirerId>{$ticketing_data['AcquirerId']}</AcquirerId>
					<MerchantReference>{$ticketing_data['MerchantReference']}</MerchantReference>
					<RequestType>{$ticketing_data['RequestType']}</RequestType>
					<ExpirePreauth>{$ticketing_data['ExpirePreauth']}</ExpirePreauth>
					<Amount>{$ticketing_data['Amount']}</Amount>
					<CurrencyCode>{$ticketing_data['CurrencyCode']}</CurrencyCode>
					<Installments>{$ticketing_data['Installments']}</Installments>
					<Bnpl>0</Bnpl>
					<Parameters>{$ticketing_data['Parameters']}</Parameters>
				</Request>
			</IssueNewTicket>
		</soap:Body>
	</soap:Envelope>
EOT;
	$str = str_replace(array("\t", "\n", "\r"), '', $str);
	
	list($headers, $response_data) = fn_https_request("POST", "https://paycenter.winbank.gr/services/tickets/issuer.asmx", array($str), "", "", "text/xml", "", "", "", array("SOAPAction: \"http://piraeusbank.gr/paycenter/redirection/IssueNewTicket\""));

	$resultcode = true;
	$pp_response = array();

	if (strpos($response_data, '<ResultCode') !== false) {
		if (preg_match('!<ResultCode[^>]*>([^>]+)</ResultCode>!', $response_data, $matches)) {
			$resultcode = $matches[1];
		}
	}

	if ($resultcode == "0") {

		if (strpos($response_data, '<TranTicket') !== false) {
			if (preg_match('!<TranTicket[^>]*>([^>]+)</TranTicket>!', $response_data, $matches)) {
				$pp_response['TranTicket'] = $matches[1];
			}
		}

		if (strpos($response_data, '<Timestamp') !== false) {
			if (preg_match('!<Timestamp[^>]*>([^>]+)</Timestamp>!', $response_data, $matches)) {
				$pp_response['Timestamp'] = $matches[1];
			}
		}

		fn_update_order_payment_info($order_id, $pp_response);

		$post_url = 'https://paycenter.winbank.gr/redirection/pay.aspx';

		$post_data = array (
			'AcquirerId' => $processor_data['params']['acquirerid'],
			'MerchantId' => $processor_data['params']['merchantid'],
			'PosId' => $processor_data['params']['posid'],
			'User' => $processor_data['params']['username'],
			'LanguageCode' => $processor_data['params']['languagecode'],
			'MerchantReference' => (($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id),
			'ParamBackLink' => ""
		);

		$_SESSION['stored_piraeus_orderid'] = $order_id;

		echo <<<EOT
		<html>
		<body onload="javascript: document.process.submit();">
		<form method="post" action="{$post_url}" name="process">
			<input type="hidden" name="AcquirerId" value="{$post_data['AcquirerId']}" />
			<input type="hidden" name="MerchantId" value="{$post_data['MerchantId']}" />
			<input type="hidden" name="PosId" value="{$post_data['PosId']}" />
			<input type="hidden" name="User" value="{$post_data['User']}" />
			<input type="hidden" name="LanguageCode" value="{$post_data['LanguageCode']}" />
			<input type="hidden" name="MerchantReference" value="{$post_data['MerchantReference']}" />
			<input type="hidden" name="ParamBackLink" value="{$post_data['ParamBackLink']}" />
EOT;
		$msg = fn_get_lang_var('text_cc_processor_connection');
		$msg = str_replace('[processor]', 'Piraeus server', $msg);
		echo <<<EOT
			</form>
			<p><div align=center>{$msg}</div></p>
		</body>
		</html>
EOT;
	exit;

	} else {
		$pp_response['order_status'] = 'F';
		$pp_response["ResultCode"] = $resultcode;

		if (strpos($response_data, '<ResultDescription') !== false) {
			if (preg_match('!<ResultDescription[^>]*>([^>]+)</ResultDescription>!', $response_data, $matches)) {
				$pp_response["reason_text"] = $matches[1];
			}
		}
	}

}

?>