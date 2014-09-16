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
// $Id: paypal_express.php 10378 2010-08-06 12:36:48Z klerik $
//

if (defined('PAYMENT_NOTIFICATION')) {

	if ($mode == 'cancel') {

		$order_info = fn_get_order_info($_REQUEST['order_id']);

		if ($order_info['status'] == 'O' || $order_info['status'] == 'N') {
			$pp_response['order_status'] = 'N';
			$pp_response["reason_text"] = fn_get_lang_var('text_transaction_cancelled');
			fn_finish_payment($order_info['order_id'], $pp_response, false);
		}

		fn_order_placement_routines($_REQUEST['order_id']);

	} else {

		$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $_REQUEST['order_id']);
		$processor_data = fn_get_payment_method_data($payment_id);
		$order_info = fn_get_order_info($_REQUEST['order_id']);

		$pp_username =  $processor_data['params']['username'];
		$pp_password = $processor_data['params']['password'];
		$pp_currency = $processor_data['params']['currency'];

		$cert_file = $signature = $url_prefix = '';
		if (!empty($processor_data['params']['authentication_method']) && $processor_data['params']['authentication_method'] == 'signature') {
			$signature = '<Signature>' . $processor_data['params']['signature'] . '</Signature>';
			$url_prefix = '-3t';
		} else {
			$cert_file = DIR_PAYMENT_FILES . 'certificates/' . $processor_data['params']['certificate'];
		}
		
		$pp_order_id = $processor_data['params']['order_prefix'] . (($order_info['repaid']) ? ($_REQUEST['order_id'] . '_' . $order_info['repaid']) : $_REQUEST['order_id']);

		if ($processor_data['params']['mode'] == 'live') {
			$post_url = "https://api$url_prefix.paypal.com:443/2.0/";
		} else {
			$post_url = "https://api$url_prefix.sandbox.paypal.com:443/2.0/";
		}

		// finish ExpressCheckout
		$request =<<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
        $signature
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <DoExpressCheckoutPaymentReq xmlns="urn:ebay:api:PayPalAPI">
      <DoExpressCheckoutPaymentRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>
        <DoExpressCheckoutPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <PaymentAction>Sale</PaymentAction>
          <Token>$_REQUEST[token]</Token>
          <PayerID>$_REQUEST[PayerID]</PayerID>
          <PaymentDetails>
            <OrderTotal currencyID="$pp_currency">$order_info[total]</OrderTotal>
            <ButtonSource>ST_ShoppingCart_EC_US</ButtonSource>
            <InvoiceID>$pp_order_id</InvoiceID>
            <Custom>$_REQUEST[order_id]</Custom>
          </PaymentDetails>
        </DoExpressCheckoutPaymentRequestDetails>
      </DoExpressCheckoutPaymentRequest>
    </DoExpressCheckoutPaymentReq>
  </soap:Body>
</soap:Envelope>
EOT;

		$result = fn_paypal_request($request, $post_url, $cert_file);

		$pp_response['order_status'] = 'F';

		if (!strcasecmp($result['PaymentStatus'],'Completed') || !strcasecmp($result['PaymentStatus'],'Processed')) {
			$pp_response['order_status'] = 'P';
			$reason_text = 'Accepted';
		} elseif (!strcasecmp($result['PaymentStatus'],'Pending')) {
			$pp_response['order_status'] = 'O';
			$reason_text = 'Pending';
		} else {
			$reason_text = 'Declined';
		}

		$reason_text .= " Status: ".$result['PaymentStatus'];

		if (!empty($result['PendingReason']))
			$reason_text .= ' Reason: '.$result['PendingReason'];

		$additional_fields = array();
		foreach (array('TransactionID','TransactionType','PaymentType','GrossAmount','FeeAmount','SettleAmount','TaxAmount','ExchangeRate') as $add_field) {
			if (isset($result[$add_field]) && strlen($result[$add_field]) > 0)
				$additional_fields[] = ' ' . $add_field . ': ' . $result[$add_field];
		}

		if (!empty($additional_fields))
			$reason_text .= ' ('.implode(', ', $additional_fields).')';

		if (!empty($result['error'])) {
			$reason_text .= sprintf (
				" Error: %s (Code: %s, Severity: %s)",
				$result['error']['LongMessage'],
				$result['error']['ErrorCode'],
				$result['error']['Severity']);
		}

		$pp_response['reason_text'] = $reason_text;

		if (preg_match("/<TransactionID>(.*)<\/TransactionID>/", $result['response'], $transaction)) {
			$pp_response['transaction_id'] = $transaction[1];
		}

		if (fn_check_payment_script('paypal_express.php', $_REQUEST['order_id'])) {
			fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
			fn_order_placement_routines($_REQUEST['order_id']);
		}

	}
}

if (!empty($_payment_id) && !empty($_SESSION['cart']['products']))  {
	$checkout_buttons[] = '
		<html>
		<body>
		<br/>
		<form name="pp_express" action="'. Registry::get('config.current_location') . '/payments/paypal_express.php" method="post">
			<input name="_payment_id" value="'.$_payment_id.'" type="hidden" />
			<input src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" type="image" />
			<input name="mode" value="express" type="hidden" />
		</form>
		</body>
		</html>';

} else {
	$locale_codes = array("AU","DE","FR","GB","IT","JP","US");

	if (!defined('AREA')) {
		DEFINE ('AREA', 'C');
		DEFINE ('AREA_NAME' ,'customer');

		require './../prepare.php';
		require './../init.php';

		$_SESSION['cart'] = empty($_SESSION['cart']) ? array() : $_SESSION['cart'];
	}

	$pp_method = (!empty($order_id) && empty($_SESSION['pp_express_details'])) ? 'mf' : 'sf';
	$_payment_id = (empty($_REQUEST['_payment_id']) ? @$_SESSION['cart']['payment_id'] : $_REQUEST['_payment_id']);

	if (empty($processor_data)) {
		$processor_data = fn_get_payment_method_data($_payment_id);
	}

	$pp_username =  $processor_data['params']['username'];
	$pp_password = $processor_data['params']['password'];
	$pp_currency = $processor_data['params']['currency'];

	$cert_file = $signature = $url_prefix = '';
	if (!empty($processor_data['params']['authentication_method']) && $processor_data['params']['authentication_method'] == 'signature') {
		$signature = '<Signature>' . $processor_data['params']['signature'] . '</Signature>';
		$url_prefix = '-3t';
	} else {
		$cert_file = DIR_PAYMENT_FILES . 'certificates/' . $processor_data['params']['certificate'];
	}

	$pp_total = empty($order_id)? $_SESSION['cart']['total'] : $order_info['total'];

	if ($processor_data['params']['mode'] == "live") {
		$post_url = "https://api$url_prefix.paypal.com:443/2.0/";
		$payment_url = "https://www.paypal.com";
	} else {
		$post_url = "https://api$url_prefix.sandbox.paypal.com:443/2.0/";
		$payment_url = "https://www.sandbox.paypal.com";
	}

	if ((!empty($_payment_id) && !empty($_SESSION['cart']['products']) && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($_REQUEST['mode']) && $_REQUEST['mode'] == "express") || ($pp_method == 'mf')) {
		//  start express checkout

		if ($pp_method == 'sf') {
			$return_url = Registry::get('config.current_location') . "/payments/paypal_express.php?mode=express_return&amp;_payment_id=$_payment_id";
			$cancel_url = Registry::get('config.current_location') . '/' . Registry::get('config.customer_index') . "?dispatch=checkout.cart";

		} else {
			$return_url = Registry::get('config.current_location') . '/' . Registry::get('config.customer_index') . "?dispatch=payment_notification.notify&amp;payment=paypal_express&amp;order_id=$order_id";
			$cancel_url = Registry::get('config.current_location') . '/' . Registry::get('config.customer_index') . "?dispatch=payment_notification.cancel&amp;payment=paypal_express&amp;order_id=$order_id";
		}

		$pp_locale_code = "US";
	 	if (in_array(CART_LANGUAGE, $locale_codes)) {
 			$pp_locale_code = CART_LANGUAGE;
 		}

		$_address = '';
		if ($pp_method == 'mf' && !empty($processor_data['params']['send_adress']) && $processor_data['params']['send_adress'] == 'Y') {
$_address = <<<EOT
         <ReqConfirmShipping>0</ReqConfirmShipping>
         <AddressOverride>1</AddressOverride>
          <Address>
            	<Name>$order_info[s_firstname] $order_info[s_lastname]</Name>
            	<Street1>$order_info[s_address]</Street1>
            	<Street2>$order_info[s_address_2]</Street2>
            	<CityName>$order_info[s_city]</CityName>
            	<StateOrProvince>$order_info[s_state]</StateOrProvince>
                <PostalCode>$order_info[s_zipcode]</PostalCode>
                <Country>$order_info[s_country]</Country>
          </Address>
EOT;
		}
		// send SetExpressCheckoutRequest to PayPal
$xml_cart = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
        $signature
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <SetExpressCheckoutReq xmlns="urn:ebay:api:PayPalAPI">
      <SetExpressCheckoutRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>
        <SetExpressCheckoutRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <OrderTotal currencyID="$pp_currency">$pp_total</OrderTotal>
          <ReturnURL>$return_url</ReturnURL>
          <CancelURL>$cancel_url</CancelURL>
          <PaymentAction>Authorization</PaymentAction>
          <LocaleCode>$pp_locale_code</LocaleCode>
          {$_address}
        </SetExpressCheckoutRequestDetails>
      </SetExpressCheckoutRequest>
    </SetExpressCheckoutReq>
  </soap:Body>
</soap:Envelope>
EOT;

		$result = fn_paypal_request($xml_cart, $post_url, $cert_file);

		//  receive SetExpressCheckoutResponse
		if ($result['success'] && !empty($result['Token'])) {
			$pp_token = $result['Token'];
			// move to the PayPal
			$suffix = ($pp_method == 'mf') ? '&useraction=commit' : '';
			fn_redirect($payment_url . '/webscr?cmd=_express-checkout&token='.$result['Token'].$suffix, true, true);
		}

		if ($pp_method == 'mf') {

			$pp_response['order_status'] = 'F';
			$pp_response["reason_text"] = isset($result['error']['LongMessage'])? $result['error']['LongMessage'] : $result['error']['ShortMessage'];
			fn_finish_payment($order_info['order_id'], $pp_response, false);
			fn_order_placement_routines($order_info['order_id']);
		} else {

			fn_set_notification('E', fn_get_lang_var('error'), isset($result['error']['LongMessage'])? $result['error']['LongMessage'] : $result['error']['ShortMessage'] );
			fn_redirect($cancel_url, true);
		}

	} elseif ($_SERVER['REQUEST_METHOD'] == "GET" && $_REQUEST['mode'] == 'express_return' && !empty($_REQUEST['token'])) {
		// return from PayPal
		// send GetExpressCheckoutDetailsRequest

		$token = $_REQUEST['token'];
$request =<<<EOT
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
        $signature
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <GetExpressCheckoutDetailsReq xmlns="urn:ebay:api:PayPalAPI">
      <GetExpressCheckoutDetailsRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>
        <Token>$token</Token>
      </GetExpressCheckoutDetailsRequest>
    </GetExpressCheckoutDetailsReq>
  </soap:Body>
</soap:Envelope>
EOT;
		$result = fn_paypal_request($request, $post_url, $cert_file);

		$address = array (
			's_address' => $result['address']['Street1'],
			's_address_2' => !empty($result['address']['Street2']) ? $result['address']['Street2'] : '',
			's_city' => $result['address']['CityName'],
			's_county' => $result['address']['StateOrProvince'],
			's_state' => $result['address']['StateOrProvince'],
			's_country' => $result['address']['Country'],
			's_zipcode' => $result['address']['PostalCode']
		);

		$_SESSION['auth'] = empty($_SESSION['auth']) ? array() : $_SESSION['auth'];
		$auth = & $_SESSION['auth'];

		// Update currenct user info
		if (!empty($auth['user_id']) && $auth['area'] == 'C') {
			foreach ($address as $k => $v) {
				$_SESSION['cart']['user_data'][$k] = $v;
			}

			$profile_id = !empty($_SESSION['cart']['profile_id']) ? $_SESSION['cart']['profile_id'] : db_get_field("SELECT profile_id FROM ?:user_profiles WHERE user_id = ?i AND profile_type='P'", $auth['user_id']);
			db_query('UPDATE ?:user_profiles SET ?u WHERE profile_id = ?i', $_SESSION['cart']['user_data'], $profile_id);

		// Creat anonymous profile
		} else {
			// fill customer info
			$_SESSION['cart']['user_data'] = array(
				'firstname' => $result['FirstName'],
				'lastname' => $result['LastName'],
				'email' => $result['Payer'],
				'company' => '',
				'phone' => !empty($result['ContactPhone']) ? $result['ContactPhone'] : '1234567890',
				'fax' => '',
			);

			// Fill out the billing and shipping addresses
			foreach ($address as $k => $v) {
				$_SESSION['cart']['user_data'][$k] = $v;
				$_SESSION['cart']['user_data']['b_' . substr($k, 2)] = $v;
			}
		}

		$_SESSION['cart']['payment_id'] = $_payment_id;
		$_SESSION['pp_express_details'] = $result;

		fn_redirect(Registry::get('config.current_location') . '/' . Registry::get('config.customer_index') . "?dispatch=checkout.checkout&payment_id=" . $_payment_id);

	} elseif (!empty($mode) && $mode == 'place_order') {

		$pp_order_id = $processor_data['params']['order_prefix'] . (($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id);


		// finish ExpressCheckout
		$_address = '';
		if (!empty($processor_data['params']['send_adress']) && $processor_data['params']['send_adress'] == 'Y') {
$_address = <<<EOT
          <ShipToAddress>
                <Name>$order_info[s_firstname] $order_info[s_lastname]</Name>
                <Street1>$order_info[s_address]</Street1>
                <Street2>$order_info[s_address_2]</Street2>
                <CityName>$order_info[s_city]</CityName>
                <StateOrProvince>$order_info[s_state]</StateOrProvince>
                <PostalCode>$order_info[s_zipcode]</PostalCode>
                <Country>$order_info[s_country]</Country>
          </ShipToAddress>
EOT;
		}

$request =<<<EOT
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
        $signature
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <DoExpressCheckoutPaymentReq xmlns="urn:ebay:api:PayPalAPI">
      <DoExpressCheckoutPaymentRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>
        <DoExpressCheckoutPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <PaymentAction>Sale</PaymentAction>
          <Token>{$_SESSION['pp_express_details']['Token']}</Token>
          <PayerID>{$_SESSION['pp_express_details']['PayerID']}</PayerID>
          <PaymentDetails>
            <OrderTotal currencyID="$pp_currency">{$_SESSION['cart']['total']}</OrderTotal>
            <ButtonSource>ST_ShoppingCart_EC_US</ButtonSource>
            <NotifyURL></NotifyURL>
            <InvoiceID>$pp_order_id</InvoiceID>
            <Custom></Custom>
            $_address
          </PaymentDetails>
        </DoExpressCheckoutPaymentRequestDetails>
      </DoExpressCheckoutPaymentRequest>
    </DoExpressCheckoutPaymentReq>
  </soap:Body>
</soap:Envelope>
EOT;

		$result = fn_paypal_request($request, $post_url, $cert_file);

		$pp_response['order_status'] = 'F';

		if (isset($result['PaymentStatus']) && (!strcasecmp($result['PaymentStatus'],'Completed') || !strcasecmp($result['PaymentStatus'],'Processed'))) {
			$pp_response['order_status'] = 'P';
			$reason_text = 'Accepted';
		} elseif (isset($result['PaymentStatus']) && !strcasecmp($result['PaymentStatus'],'Pending')) {
			$pp_response['order_status'] = 'O';
			$reason_text = 'Pending';
		} else {
			$reason_text = 'Declined';
		}

		if (!empty($result['PaymentStatus'])) {
			$reason_text .= " Status: " . $result['PaymentStatus'];
		}
		if (!empty($result['PendingReason']))
			$reason_text .= ' Reason: ' . $result['PendingReason'];

		$additional_fields = array();
		foreach (array('TransactionID','TransactionType','PaymentType','GrossAmount','FeeAmount','SettleAmount','TaxAmount','ExchangeRate') as $add_field) {
			if (isset($result[$add_field]) && strlen($result[$add_field]) > 0)
				$additional_fields[] = ' ' . $add_field . ': ' . $result[$add_field];
		}

		if (!empty($additional_fields))
			$reason_text .= ' (' . implode(', ', $additional_fields) . ')';

		if (!empty($result['error'])) {
			$reason_text .= sprintf (
				" Error: %s (Code: %s%s)",
				$result['error']['LongMessage'],
				$result['error']['ErrorCode'],
				isset($result['error']['Severity'])? ' , Severity:' . $result['error']['Severity'] : '');
		}

		$pp_response['reason_text'] = $reason_text;

		if (preg_match("/<TransactionID>(.*)<\/TransactionID>/", $result['response'], $transaction)) {
			$pp_response['transaction_id'] = $transaction[1];
		}
		unset($_SESSION['pp_express_details']);

		if (fn_check_payment_script('paypal_express.php', $order_id)) {
			fn_finish_payment($order_id, $pp_response, false);
			fn_order_placement_routines($order_id);
		}
	}
}

function fn_paypal_request($request, $post_url, $cert_file) {

	$post = explode("\n",$request);
	list ($headers, $response) = fn_https_request("POST", $post_url, $post, "", "", "text/xml", "", $cert_file);

	if ($headers == "0") {
		return array(
			'success' => false,
			'error' => array ( 'ShortMessage' => $response, 'LongMessage' => $response, 'ErrorCode' => 0)
		);
	}

	$result = array (
		'headers' => $headers,
		'response' => $response
	);

	#
	# Parse and fill common fields
	#
	$result['success'] = false;

	$ord_fields = array (
		'Ack',
		'TransactionID',
		'Token', // Note: expires after three hours (Express Checkout Integration Guide, p30)
		'AVSCode',
		'CVV2Code',
		'PayerID',
		'PayerStatus',
		'FirstName',
		'LastName',
		'ContactPhone',
		'TransactionType', // e.g. express-checokut
		'PaymentStatus', // e.g. Pending
		'PendingReason', // e.g. authorization
		'ReasonCode',
		'GrossAmount',
		'FeeAmount',
		'SettleAmount',
		'TaxAmount',
		'ExchangeRate'
	);

	foreach ($ord_fields as $field) {
		if (preg_match('!<'.$field.'[^>]+>([^>]+)</'.$field.'>!', $response, $out)) {
			$result[$field] = $out[1];
		}
	}

	if (!strcasecmp($result['Ack'], 'Success') || !strcasecmp($result['Ack'], 'SuccessWithWarning'))
		$result['success'] = true;

	if (preg_match('!<Payer(?:\s[^>]*)?>([^>]+)</Payer>!', $response, $out)) {
		$result['Payer'] = $out[1]; // e-mail address
	}

	if (preg_match('!<Errors[^>]*>(.+)</Errors>!', $response, $out_err)) {
		$error = array();

		if (preg_match('!<SeverityCode[^>]*>([^>]+)</SeverityCode>!', $out_err[1], $out))
			$error['SeverityCode'] = $out[1];

		if (preg_match('!<ErrorCode[^>]*>([^>]+)</ErrorCode>!', $out_err[1], $out))
			$error['ErrorCode'] = $out[1];

		if (preg_match('!<ShortMessage[^>]*>([^>]+)</ShortMessage>!', $out_err[1], $out))
			$error['ShortMessage'] = $out[1];

		if (preg_match('!<LongMessage[^>]*>([^>]+)</LongMessage>!', $out_err[1], $out))
			$error['LongMessage'] = $out[1];

		$result['error'] = $error;
	}

	if (preg_match('!<Address[^>]*>(.+)</Address>!', $response, $out)) {
		$out_addr = $out[1];
		$address = array();

		if (preg_match('!<Street1[^>]*>([^>]+)</Street1>!', $out_addr, $out))
			$address['Street1'] = $out[1];
		if (preg_match('!<Street2[^>]*>([^>]+)</Street2>!', $out_addr, $out))
			$address['Street2'] = $out[1];

		if (preg_match('!<CityName[^>]*>([^>]+)</CityName>!', $out_addr, $out))
			$address['CityName'] = $out[1];

		if (preg_match('!<StateOrProvince[^>]*>([^>]+)</StateOrProvince>!', $out_addr, $out))
			$address['StateOrProvince'] = $out[1];

		if (preg_match('!<Country[^>]*>([^>]+)</Country>!', $out_addr, $out))
			$address['Country'] = $out[1];

		if (preg_match('!<PostalCode[^>]*>([^>]+)</PostalCode>!', $out_addr, $out))
			$address['PostalCode'] = $out[1];

		if (preg_match('!<AddressOwner[^>]*>([^>]+)</AddressOwner>!', $out_addr, $out))
			$address['AddressOwner'] = $out[1];

		if (preg_match('!<AddressStatus[^>]*>([^>]+)</AddressStatus>!', $out_addr, $out))
			$address['AddressStatus'] = $out[1];

		$result['address'] = $address;
	}

	return $result;
}

?>