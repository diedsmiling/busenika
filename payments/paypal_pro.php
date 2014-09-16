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
// $Id: paypal_pro.php 9440 2010-05-13 13:04:30Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

include_once(DIR_PAYMENT_FILES . 'cmpi.php');

$use_cardinal = (!empty($processor_data['params']['use_cardinal']) && $processor_data['params']['use_cardinal'] == 'Y') ? true : false;
if ($use_cardinal) {
	if (!defined('CMPI_PROCESSED')) {
		fn_cmpi_lookup($processor_data, $order_info);	
	}
} else {
	define('DO_DIRECT_PAYMENT', true);
}

if (defined('DO_DIRECT_PAYMENT')) {
	
$processor_error['avs'] = array(
	"A" => "Address Address only (no ZIP)",
	"B" => "International 'A'. Address only (no ZIP)",
	"C" => "International 'N'",
	"D" => "International 'X'. Address and Postal Code",
	"E" => "Not allowed for MOTO (Internet/Phone) transactions",
	"F" => "UK-specific X Address and Postal Code",
	"G" => "Global Unavailable",
	"I" => "International Unavailable",
	"N" => "None",
	"P" => "Postal Code only (no Address)",
	"R" => "Retry",
	"S" => "Service not Supported",
	"U" => "Unavailable",
	"W" => "Nine-digit ZIP code (no Address)",
	"X" => "Exact match. Address and five-digit ZIP code",
	"Y" => "Address and five-digit ZIP",
	"Z" => "Five-digit ZIP code (no Address)"
);

$processor_error['cvv'] = array(
	"M" => "Match",
	"N" => "No match",
	"P" => "Not Processed",
	"S" => "Service not Supported",
	"U" => "Unavailable",
	"X" => "No response"
);

$paypal_username = $processor_data['params']['username'];
$paypal_password = $processor_data['params']['password'];

$sandbox = ($processor_data['params']['mode'] == 'test') ? '.sandbox' : '';

if (!empty($processor_data['params']['authentication_method']) && $processor_data['params']['authentication_method'] == 'signature') {
	$paypal_signature = '<Signature>' . $processor_data['params']['signature'] . '</Signature>';	
	$paypal_sslcertpath = '';
	$paypal_url = "https://api-3t$sandbox.paypal.com:443/2.0/";
} else {
	$paypal_signature = '';	
	$paypal_sslcertpath = DIR_ROOT . '/payments/certificates/' . $processor_data['params']['certificate_filename'];
	$paypal_url = "https://api$sandbox.paypal.com:443/2.0/";
}

$paypal_notify_url = '';
$paypal_payment_action = 'Sale'; // FIXME: Should be configurable
$paypal_currency = $processor_data['params']['currency'];
//Order Total
$paypal_total = fn_format_price($order_info['total']);
$paypal_order_id = $processor_data['params']['order_prefix'] . (($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id);

//Credit Card
$paypal_card_types = array (
	'vis' => 'Visa',
	'amx' => 'Amex',
	'dsc' => 'Discover',
	'mcd' => 'MasterCard',
	'sol' => 'Solo',
	'swi' => 'Switch',
);

$paypal_card = $paypal_card_types[$order_info['payment_info']['card']];
$paypal_card_number = $order_info['payment_info']['card_number'];
$paypal_card_exp_month = $order_info['payment_info']['expiry_month'];
$paypal_card_exp_year = '20' . $order_info['payment_info']['expiry_year'];
$paypal_card_cvv2 = !empty($order_info['payment_info']['cvv2']) ? $order_info['payment_info']['cvv2'] : '';

$paypal_cc_start = '';
if (!empty($order_info['payment_info']['start_month'])) {
	$paypal_card_start_month = $order_info['payment_info']['start_month'];
	$paypal_card_start_year = '20' . $order_info['payment_info']['start_year'];

	$paypal_cc_start = <<<EOT
	<StartMonth>$paypal_card_start_month</StartMonth>
	<StartYear>$paypal_card_start_year</StartYear>
EOT;
}

$paypal_cc_issue = '';
if (!empty($order_info['payment_info']['issue_number'])) {
	$paypal_card_issue_number = $order_info['payment_info']['issue_number'];
	$paypal_cc_issue = <<<EOT
	<IssueNumber>$paypal_card_issue_number</IssueNumber>
EOT;
}

$paypal_3dsecure = '';
if ($use_cardinal) {
	$fields = array('eci_flag', 'pares', 'xid', 'cavv', 'enrolled');
	foreach ($fields as $field) {
		$_SESSION['cmpi'][$field] = isset($_SESSION['cmpi'][$field]) ? $_SESSION['cmpi'][$field] : '';
	}
	
	$paypal_3dsecure = <<<EOT
<ThreeDSecureRequest>
<AuthStatus3ds>{$_SESSION['cmpi']['pares']}</AuthStatus3ds>
<MpiVendor3ds>{$_SESSION['cmpi']['enrolled']}</MpiVendor3ds>
<Cavv>{$_SESSION['cmpi']['cavv']}</Cavv>
<Eci3ds>{$_SESSION['cmpi']['eci_flag']}</Eci3ds>
<XID>{$_SESSION['cmpi']['xid']}</XID>
</ThreeDSecureRequest>
EOT;
}

$paypal_request = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$paypal_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$paypal_password</ebl:Password>
        $paypal_signature
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <DoDirectPaymentReq xmlns="urn:ebay:api:PayPalAPI">
      <DoDirectPaymentRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">59.00</Version>
        <DoDirectPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <PaymentAction>$paypal_payment_action</PaymentAction>
          <PaymentDetails>
            <OrderTotal currencyID="$paypal_currency">$paypal_total</OrderTotal>
		    <ButtonSource>ST_ShoppingCart_DP_US</ButtonSource>
            <NotifyURL>$paypal_notify_url</NotifyURL>
			<ShipToAddress>
              <Name>{$order_info['s_firstname']} {$order_info['s_lastname']}</Name>
              <Street1>{$order_info['s_address']}</Street1>
              <Street2>{$order_info['s_address_2']}</Street2>
              <CityName>{$order_info['s_city']}</CityName>
              <StateOrProvince>{$order_info['s_state']}</StateOrProvince>
              <PostalCode>{$order_info['s_zipcode']}</PostalCode>
              <Country>{$order_info['s_country']}</Country>
            </ShipToAddress>
			<InvoiceID>$paypal_order_id</InvoiceID>
          </PaymentDetails>
          <CreditCard>
            <CreditCardType>$paypal_card</CreditCardType>
            <CreditCardNumber>$paypal_card_number</CreditCardNumber>
            <ExpMonth>$paypal_card_exp_month</ExpMonth>
            <ExpYear>$paypal_card_exp_year</ExpYear>
			$paypal_cc_start
			$paypal_cc_issue
            <CardOwner>
              <PayerStatus>verified</PayerStatus>
              <Payer>{$order_info['email']}</Payer>
              <PayerName>
                <FirstName>{$order_info['b_firstname']}</FirstName>
                <LastName>{$order_info['b_lastname']}</LastName>
              </PayerName>
              <PayerCountry>{$order_info['b_country']}</PayerCountry>
              <Address>
                <Street1>{$order_info['b_address']}</Street1>
                <Street2>{$order_info['b_address_2']}</Street2>
                <CityName>{$order_info['b_city']}</CityName>
                <StateOrProvince>{$order_info['b_state']}</StateOrProvince>
                <Country>{$order_info['b_country']}</Country>
                <PostalCode>{$order_info['b_zipcode']}</PostalCode>
              </Address>
            </CardOwner>
            <CVV2>$paypal_card_cvv2</CVV2>
          </CreditCard>
          <IPAddress>{$_SERVER['REMOTE_ADDR']}</IPAddress>
          $paypal_3dsecure
        </DoDirectPaymentRequestDetails>
      </DoDirectPaymentRequest>
    </DoDirectPaymentReq>
  </soap:Body>
</soap:Envelope>
EOT;

$post = explode("\n", $paypal_request);
Registry::set('log_cut_data', array('CreditCardType', 'CreditCardNumber', 'ExpMonth', 'ExpYear', 'CVV2', 'StartMonth', 'StartYear'));
list ($headers, $response_data) = fn_https_request('POST', $paypal_url, $post, '', '', 'text/xml', '', $paypal_sslcertpath);

$paypal_response = array();
$paypal_response['reason_text'] = '';

if (strpos($response_data, '<faultstring') !== false) {
	if (preg_match('!<faultstring[^>]*>([^>]+)</faultstring>!', $response_data, $matches)) {
		$paypal_response['reason_text'] = $matches[1];
	}

	$paypal_response['order_status'] = 'F'; // FIXME. Shouldn't be hardcoded
}

if (strpos($response_data, '<Errors') !== false) {
	if (preg_match('!<LongMessage[^>]*>([^>]+)</LongMessage>!', $response_data, $matches)) {
		$paypal_response['reason_text'] = $matches[1];
	}

	$paypal_response['order_status'] = 'F'; // FIXME. Shouldn't be hardcoded
}

if (preg_match('/<TransactionID>([^>]+)<\/TransactionID>/', $response_data, $matches)) {
	$paypal_response['transaction_id'] = $matches[1];
	$paypal_response['order_status'] = 'P';
}
if (preg_match('!<AVSCode[^>]+>([^>]+)</AVSCode>!', $response_data, $matches)) {
	$paypal_response['avs_code'] = $matches[1];
	if (empty($processor_error['avs'][trim($paypal_response['avs_code'])])) {
		$paypal_response['order_status'] = 'F';
		$paypal_response['reason_text'] .= 'AVS Verification failed'; // FIXME!!! 
	}
}
if (preg_match('!<CVVCode[^>]+>([^>]+)</CVVCode>!', $response_data, $matches)) {
	$paypal_response['cvv_code'] = $matches[1];
	if (empty($processor_error['cvv'][trim($paypal_response['cvv_code'])])) {
		$paypal_response['order_status'] = 'F';
		$paypal_response['reason_text'] .= 'CVV Verification failed'; // FIXME!!! 
	}
}

if (empty($paypal_response['order_status'])) {
	$paypal_response['order_status'] = 'F';
}

if (empty($paypal_response['reason_text'])) {
	$paypal_response['reason_text'] = '';
}

$pp_response = array();
$pp_response['order_status'] = $paypal_response['order_status'];
$pp_response['reason_text'] = $paypal_response['reason_text'];
$pp_response['transaction_id'] = (!empty($paypal_response['transaction_id'])) ? $paypal_response['transaction_id'] : '';

$pp_response['descr_avs'] = (!empty($paypal_response['avs_code'])) ? $processor_error['avs'][$paypal_response['avs_code']] : '';
$pp_response['descr_cvv'] = (!empty($paypal_response['cvv_code'])) ? $processor_error['cvv'][$paypal_response['cvv_code']] : '';

} // if (defined('DO_DIRECT_PAYMENT'))

if (!empty($_SESSION['cmpi']['auth_error'])) {
	$pp_response['order_status'] = 'F';
	$pp_response['reason_text'] = (isset($pp_response['reason_text']) ? $pp_response['reason_text'] . "\n" : '') . '3DSecure authentication failed'; 
}

if (isset($_SESSION['cmpi']['err_desc']) && is_array($_SESSION['cmpi']['err_desc'])) {
	$transaction_id = !empty($_SESSION['cmpi']['transaction_id']) ? $_SESSION['cmpi']['transaction_id'] : ''; 
	foreach ($_SESSION['cmpi']['err_desc'] as $k => $desc) {
		if ($desc) {
			$pp_response['reason_text'] = (isset($pp_response['reason_text']) ? $pp_response['reason_text'] . "\n" : '') . '3DSecure error: ' . $_SESSION['cmpi']['err_no'][$k] . ': ' . $desc;
			if ($transaction_id) {
				$pp_response['reason_text'] .= ' (Transaction ID: ' . $transaction_id . ')'; 		
			}
		}
	}
}

?>