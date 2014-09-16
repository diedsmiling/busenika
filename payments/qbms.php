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
// $Id: qbms.php 10229 2010-07-27 14:21:39Z 2tl $
//
if ( !defined('AREA') ) { die('Access denied'); }

// This payment requires Version 1.3.4sp3 or highier. If you have a lower version than copy the 'xmldocument.php';
include(DIR_ROOT . '/lib/xmldocument/xmldocument.php');

$sslcert = DIR_ROOT . '/payments/certificates/' . $processor_data['params']['certificate_filename'];
$post_url = ($processor_data["params"]["mode"] == 'test') ? "https://webmerchantaccount.ptc.quickbooks.com/j/AppGateway" : "https://webmerchantaccount.quickbooks.com/j/AppGateway";

$post_data = array();
$post_data[] = "<?xml version=\"1.0\"?>";
$post_data[] = "<?qbmsxml version=\"2.0\"?>";
$post_data[] = "<QBMSXML>";
$post_data[] = " <SignonMsgsRq>";
$post_data[] = "  <SignonAppCertRq>";
$post_data[] = "   <ClientDateTime>".date("Y-d-m\TH:i:s")."</ClientDateTime>";
$post_data[] = "   <ApplicationLogin>".$processor_data["params"]["app_login"]."</ApplicationLogin>";
$post_data[] = "   <ConnectionTicket>".$processor_data["params"]["connection_ticket"]."</ConnectionTicket>";
$post_data[] = "  </SignonAppCertRq>";
$post_data[] = " </SignonMsgsRq>";
$post_data[] = "</QBMSXML>";

list($a, $_response) = fn_https_request("POST", $post_url, $post_data, "", "", "application/x-qbmsxml", "", $sslcert, $sslcert);

$root = fn_qb_get_xml_body($_response);
if (is_object($root)) {
	$session_ticket = $root->getValueByPath('SignonMsgsRs/SignonAppCertRs/SessionTicket');

	// POST Credit Card information
	$post_data = array();
	$post_data[] = '<?xml version="1.0"?>';
	$post_data[] = '<?qbmsxml version="2.0"?>';
	$post_data[] = '<QBMSXML>';
	$post_data[] = '<SignonMsgsRq>';
	$post_data[] = '<SignonTicketRq>';
	$post_data[] = '<ClientDateTime>' . date('Y-d-m\TH:i:s') . '</ClientDateTime>';
	$post_data[] = '<SessionTicket>' . $session_ticket . '</SessionTicket>';
	$post_data[] = '</SignonTicketRq>';
	$post_data[] = '</SignonMsgsRq>';
	$post_data[] = '<QBMSXMLMsgsRq>';
	$post_data[] = '<CustomerCreditCardChargeRq requestID="1">';
	$post_data[] = '<TransRequestID>' . $processor_data['params']['order_prefix'] . (($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id) . '</TransRequestID>';
	$post_data[] = '<CreditCardNumber>' . $order_info['payment_info']['card_number'] . '</CreditCardNumber>';
	$post_data[] = '<ExpirationMonth>' . $order_info['payment_info']['expiry_month'] . '</ExpirationMonth>';
	$post_data[] = '<ExpirationYear>20' . $order_info['payment_info']['expiry_year'] . '</ExpirationYear>';
	$post_data[] = '<IsCardPresent>1</IsCardPresent>';
	$post_data[] = '<Amount>' . $order_info['total'] . '</Amount>';
	$post_data[] = '<NameOnCard>' . $order_info['payment_info']['cardholder_name'] . '</NameOnCard>';
	$post_data[] = '<CreditCardAddress>' . $order_info['b_address'] . '</CreditCardAddress>';
	$post_data[] = '<CreditCardPostalCode>' . $order_info['b_zipcode'] . '</CreditCardPostalCode>';
	$post_data[] = '<CardSecurityCode>' . $order_info['payment_info']['cvv2'] . '</CardSecurityCode>';
	$post_data[] = '</CustomerCreditCardChargeRq>';
	$post_data[] = '</QBMSXMLMsgsRq>';
	$post_data[] = '</QBMSXML>';

	// Make a request to the QBMS Server
	Registry::set('log_cut_data', array('CreditCardNumber', 'ExpirationMonth', 'ExpirationYear', 'CardSecurityCode'));
	list($a, $__response) = fn_https_request("POST", $post_url, $post_data, "", "", "application/x-qbmsxml", "", $sslcert, $sslcert);

	// Parse the Response from the Server
	$root = fn_qb_get_xml_body($__response);
	$signon = $root->getElementByPath("SignonMsgsRs/SignonTicketRs");
	$response['signon_status'] = $signon->getAttribute("statusCode");
	$customer = $root->getElementByPath("QBMSXMLMsgsRs/CustomerCreditCardChargeRs");
	$response['customer_status'] = $customer->getAttribute("statusCode");

	// Got Signon error
	if (!empty($response['signon_status'])) {
		$pp_response['order_status'] = 'F';
		$pp_response['reason_text'] = $response['signon_status'] . ': ' . $signon->getAttribute("statusMessage");

	// Got Customer error
	} elseif (!empty($response['customer_status'])) {
		$pp_response['order_status'] = 'F';
		$pp_response['reason_text'] = $response['customer_status'] . ': ' . $customer->getAttribute("statusMessage");

	// Transaction is successfull
	} else {
		$pp_response['order_status'] = 'P';
		$pp_response['reason_text'] = $customer->getValueByPath('/PaymentStatus'). '; Auth Code: ' . $customer->getValueByPath('/AuthorizationCode');
		$pp_response['transaction_id'] = $customer->getValueByPath('/CreditCardTransID') .'; '. fn_get_lang_var('customer').': '. $customer->getValueByPath('/ClientTransID');
		$pp_response['descr_avs'] = 'AVSStreet: '.$customer->getValueByPath('/AVSStreet') .'; AVSZip: '.$customer->getValueByPath('/AVSZip');
		$customer->getValueByPath('/CardSecurityCodeMatch');
	}
} else {
	$pp_response['order_status'] = 'F';
}

function fn_qb_get_xml_body($response)
{
	$doc = new XMLDocument();
	$xp = new XMLParser();
	$xp->setDocument($doc);
	$xp->parse($response);
	$doc = $xp->getDocument();
	$root = $doc->getRoot();
	
	return $root;
}

?>