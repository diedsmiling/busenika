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
// $Id: google_checkout_response.php 10329 2010-08-03 14:46:04Z klerik $
//

define('AREA', 'C');
define('AREA_NAME' ,'customer');
define('SKIP_SESSION_VALIDATION', true);

require './../prepare.php';
require './../init.php';

$avs_responses = array(
	'Y' =>	'Full AVS match (address and postal code)',
	'P' =>	'Partial AVS match (postal code only)',
	'A' =>	'Partial AVS match (address only)',
	'N' =>	'No AVS match',
	'U' =>	'AVS not supported by issuer'
);

$cvn_responses = array(
	'M' =>	'CVN match',
	'N' =>	'No CVN match',
	'U' =>	'CVN not available',
	'E' =>	'CVN error'
);

$pp_response = array();
fn_define('GOOGLE_ORDER_DATA', 'O');

include(DIR_LIB . 'xmldocument/xmldocument.php');

$schema_url = 'http://checkout.google.com/schema/2';

$xml_response = $GLOBALS['HTTP_RAW_POST_DATA'];

$doc = new XMLDocument();
$xp = new XMLParser();
$xp->setDocument($doc);
$xp->parse($xml_response);
$doc = $xp->getDocument();
if (is_object($doc->root)) {
	$root = $doc->getRoot();
	$message_recognizer = $root->getName();
} else {
	fn_google_xml_error('GCR: Failed to parse incoming XML data');
}

// Restart session
$google_sess_id = $root->getValueByPath('shopping-cart/merchant-private-data/additional_data/session_id');
if (!empty($google_sess_id)) {
	Session::reset_id($google_sess_id);
	$_SESSION['cart'] = empty($_SESSION['cart']) ? array() : $_SESSION['cart'];
	$cart = & $_SESSION['cart'];
	$_SESSION['auth'] = empty($_SESSION['auth']) ? array() : $_SESSION['auth'];
	$auth = & $_SESSION['auth'];
}

$transaction_id = $root->getValueByPath('/google-order-number');
if ($message_recognizer == 'new-order-notification') {

	$_SESSION['order_id'] = empty($_SESSION['order_id']) ? array() : $_SESSION['order_id'];
	$order_id = & $_SESSION['order_id'];
	$order_id = fn_prepare_to_place_order($root, $cart, $auth);

	$pp_response = array (
		'transaction_id' => $transaction_id
	);
	/*$buyer_marketing_preference = trim($root->getValueByPath('/buyer-marketing-preferences'));
	if ($buyer_marketing_preference != 'false') {
		$pp_response['buyer_marketing_preference'] = $buyer_marketing_preference;
	}*/

	$data = array (
		'order_id' => $order_id,
		'type' => 'E', // extra order ID
		'data' => $transaction_id,
	);
	db_query("REPLACE INTO ?:order_data ?e", $data);

	fn_update_order_payment_info($order_id, $pp_response);
	fn_change_google_info($order_id, array('risk_information' => fn_get_lang_var('risk_checking')));
	fn_associate_order_id($order_id, $transaction_id, $schema_url);
	fn_sendnotification_acknowledgment($schema_url);
}
elseif ($message_recognizer == 'order-state-change-notification') {
	$google_checkout['prev_state'] = $root->getValueByPath('/previous-financial-order-state');
	$google_checkout['order_state'] = $root->getValueByPath('/new-financial-order-state');
	$google_checkout['fulfillment_state'] = $root->getValueByPath('/new-fulfillment-order-state');
	fn_sendnotification_acknowledgment($schema_url);
	$order_id = db_get_field("SELECT order_id FROM ?:order_data WHERE type = 'E' AND data = ?s", $transaction_id);

	if (empty($order_id)) {
		fn_google_xml_error("GCR[order-state-change-notification]: Failed to get order_id by google-order-number ($transaction_id)");
	}
	fn_change_google_info($order_id, array('financial_state' => $google_checkout['order_state'], 'fulfillment_state' => $google_checkout['fulfillment_state']));

	$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $order_id);
	$processor_data = fn_get_payment_method_data($payment_id);

	// This section will be executed after 3 notifications are recieved from the google and we are ready to charge an order
	if ($google_checkout['order_state'] == "CHARGEABLE" && $google_checkout['prev_state'] == "REVIEWING" && $processor_data['params']['auto_charge'] == 'Y') {
		// Charge an Order

		$_currency = $processor_data['params']['currency'];
		$base_url = 'https://' . (($processor_data['params']['test'] == 'N') ? 'checkout.google.com' : 'sandbox.google.com/checkout') . '/cws/v2/Merchant/' . $processor_data['params']['merchant_id'];
		$request_url = $base_url . '/request';

		$order_info = fn_get_order_info($order_id);

		// Prepare an xml request to charge the order
		$post = array();
		$post[] = "<charge-order xmlns='" . $schema_url . "' google-order-number='" . $transaction_id."'>";
		$post[] = "<amount currency='" . $_currency . "'>" . $order_info['total'] . "</amount>";
		$post[] = '</charge-order>';

		$_id = base64_encode($processor_data['params']['merchant_id'] .":". $processor_data['params']['merchant_key']);
		$headers[] = "Authorization: Basic $_id";
		$headers[] = 'Accept: application/xml ';

		list($a, $return) = fn_https_request("POST", $request_url, $post, '', '', 'application/xml', '', '', '', $headers);
		exit;

	} elseif ($google_checkout['order_state'] != "CHARGEABLE" && $google_checkout['prev_state'] == "REVIEWING") {
		// The order is not chargable - the status will be failed
		$pp_response['order_status'] = 'F';
		fn_finish_payment($order_id, $pp_response);
		exit;
	}
}
elseif ($message_recognizer == 'risk-information-notification') {
	$order_id = db_get_field("SELECT order_id FROM ?:order_data WHERE type = 'E' AND data = ?s", $transaction_id);
	if (empty($order_id)) {
		fn_google_xml_error("GCR[risk-information-notification]: Failed to get order_id by google-order-number ($transaction_id)");
	}

	$avs = $root->getValueByPath('risk-information/avs-response');
	$cvn = $root->getValueByPath('risk-information/cvn-response');
	$eligable = $root->getValueByPath('risk-information/eligible-for-protection');
	$pp_response['reason_text'] = "AVS: " . $avs_responses[$avs] . ", CVN: " . $cvn_responses[$cvn]. ", Eligable for protection: " . $eligable;
	fn_update_order_payment_info($order_id, $pp_response);
	fn_change_google_info($order_id, array(), array('risk_information'));
	fn_sendnotification_acknowledgment($schema_url);
}
elseif ($message_recognizer == 'charge-amount-notification') {
	$order_id = db_get_field("SELECT order_id FROM ?:order_data WHERE type = 'E' AND data = ?s", $transaction_id);
	if (empty($order_id)) {
		fn_google_xml_error("GCR[charge-amount-notification]: Failed to get order_id by google-order-number ($transaction_id)");
	}

	$order_info = fn_get_order_info($order_id);
	$amount = $root->getValueByPath('/total-charge-amount');

	if (intval($amount) == intval($order_info['total'])) {
		fn_change_order_status($order_id, 'P', '', false);
	}

	fn_change_google_info($order_id, array('charged_amount' => $amount));
	fn_sendnotification_acknowledgment($schema_url);
} elseif ($message_recognizer == 'refund-amount-notification') {
	$order_id = db_get_field("SELECT order_id FROM ?:order_data WHERE type = 'E' AND data = ?s", $transaction_id);

	if (empty($order_id)) {
		fn_google_xml_error("GCR[charge-amount-notification]: Failed to get order_id by google-order-number ($transaction_id)");
	}

	$amount = $root->getValueByPath('/total-refund-amount');
	fn_change_google_info($order_id, array('refunded_amount' => $amount, 'financial_state' => 'REFUNDED'));

	fn_sendnotification_acknowledgment($schema_url);
} elseif ($message_recognizer == 'chargeback-amount-notification') {
	$order_id = db_get_field("SELECT order_id FROM ?:order_data WHERE type = 'E' AND data = ?s", $transaction_id);

	if (empty($order_id)) {
		fn_google_xml_error("GCR[charge-amount-notification]: Failed to get order_id by google-order-number ($transaction_id)");
	}

	$amount = $root->getValueByPath('/total-chargeback-amount');
	fn_change_google_info($order_id, array('chargeback_amount' => $amount));

	fn_sendnotification_acknowledgment($schema_url);
}

// The SendNotificationAcknowledgment function responds to a Google Checkout
// notification with a <notification-acknowledgment> message. If you do
// not send a <notification-acknowledgment> in response to a Google Checkout
// notification, Google Checkout will resend the notification multiple times.
function fn_sendnotification_acknowledgment($schema_url) 
{
    $acknowledgment = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
        "<notification-acknowledgment xmlns=\"" .
        $schema_url . "\"/>";

    echo $acknowledgment;
}

function fn_prepare_to_place_order(&$xml_data, &$cart, &$auth)
{
	// Update user info
	$bill = $xml_data->getElementByName("buyer-billing-address");
	$ship = $xml_data->getElementByName("buyer-shipping-address");
	$b_customer_name = $bill->getValueByPath("/contact-name");
	$s_customer_name = $ship->getValueByPath("/contact-name");
	$phone = ($ship->getValueByPath('/phone') != '') ? ($ship->getValueByPath('/phone')) : ($bill->getValueByPath('/phone'));

	$cart['user_data'] = array (
		'firstname' => substr($s_customer_name, 0, strpos($s_customer_name, ' ')),
		'lastname' => substr($s_customer_name, strpos($s_customer_name, ' ')),
		'email' => $ship->getValueByPath('/email'),
		'phone' => $phone,

		'b_firstname' => substr($b_customer_name, 0, strpos($b_customer_name, ' ')),
		'b_lastname' => substr($b_customer_name, strpos($b_customer_name, ' ')),
		'b_address' => $bill->getValueByPath('/address1'),
		'b_address_2' => $bill->getValueByPath('/address2'),
		'b_city' => $bill->getValueByPath('/city'),
		'b_state' => $bill->getValueByPath('/region'),
		'b_country' => $bill->getValueByPath('/country-code'),
		'b_zipcode' => $bill->getValueByPath('/postal-code'),

		's_firstname' => substr($s_customer_name, 0, strpos($s_customer_name, ' ')),
		's_lastname' => substr($s_customer_name, strpos($s_customer_name, ' ')),
		's_address' => $ship->getValueByPath('/address1'),
		's_address_2' => $ship->getValueByPath('/address2'),
		's_city' => $ship->getValueByPath('/city'),
		's_state' => $ship->getValueByPath('/region'),
		's_country' => $ship->getValueByPath('/country-code'),
		's_zipcode' => $ship->getValueByPath('/postal-code')
	);

	// Find whether coupons or gift certificates are used.
	fn_get_google_codes($cart, $xml_data);

	// Find whether surcharge is used
	$itm = $xml_data->getElementByPath('/shopping-cart/items');
	$items = $itm->getElementsByName('item');
	$total = sizeof($items);
	for ($i = 0; $i < $total; $i++) {
		if ($items[$i]->getValueByPath('/item-name') == fn_get_lang_var('surcharge')) {
			$cart['payment_surcharge'] = $items[$i]->getValueByPath('/unit-price');
		}
	}

	// Update shipping info
	$order_adj =  $xml_data->getElementByName("order-adjustment");
	if ($order_adj->getElementByPath('/shipping/merchant-calculated-shipping-adjustment')) {
	$order_shipping = $order_adj->getValueByPath('/shipping/merchant-calculated-shipping-adjustment/shipping-name');
	$gc_shippings = $xml_data->getElementByPath('shopping-cart/merchant-private-data/additional_data/shippings');
		if ($gc_shippings) {
			$gc_methods = $gc_shippings->getElementsByName('method');
			$gc_methods_total = sizeof($gc_methods);
			for ($k = 0; $k < $gc_methods_total; $k++) {
				if ($gc_methods[$k]->getAttribute('name') == $order_shipping) {
					$id = $gc_methods[$k]->getAttribute('id');
					fn_fill_google_shipping_info($id, $cart, $order_adj, $order_shipping);
					break;
				}
			}
		}
    }

	$cart['recalculate'] = true;
	fn_calculate_cart_content($cart, $auth, 'A', true, 'I', true);
	$cart['payment_id'] = db_get_field("SELECT a.payment_id FROM ?:payments as a LEFT JOIN ?:payment_processors as b ON a.processor_id = b.processor_id WHERE b.processor_script = 'google_checkout.php'");

	list($order_id) = fn_place_order($cart, $auth, 'save');
	// This string is here because payment_cc.php file wasn't executed
	db_query("REPLACE INTO ?:order_data (order_id, type, data) VALUES (?i, 'S', ?i)", $order_id, TIME);

	return $order_id;
}

function fn_google_xml_error($error)
{
	echo
		"<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
		"<cart-error>".htmlspecialchars($error)."</cart-error>";
	exit;
}

function fn_fill_google_shipping_info($id, & $cart, $order_adj, $order_shipping)
{

	if (is_numeric($id)) {
		$cart['shipping'] = array (
			$id => array(
				'shipping' => $order_shipping,
				'rates' => array (
					'0' => $order_adj->getValueByPath('/shipping/merchant-calculated-shipping-adjustment/shipping-cost')
				)
			));
	}

	if(fn_check_suppliers_functionality()) {
		fn_companies_fill_google_shipping_info($id, $cart, $order_adj);
	}

	fn_set_hook('fill_google_shipping_info', $id, $cart, $order_adj);

	return true;
}

function fn_companies_fill_google_shipping_info($id, &$cart, $order_adj)
{
	if (strpos($id, '_')) {
		$shippings = explode('_', $id);

		$needed_suppliers = array();
		foreach ($cart['products'] as $k => $v) {
			if (isset($v['extra']['supplier_id'])) {
				$needed_suppliers[$v['extra']['supplier_id']] = '';
			}
		}
		ksort($needed_suppliers);
		$_temp = array_keys($needed_suppliers);
		$cart['shipping'] = array();
		$total_suppliers = count($needed_suppliers);
		for ($i = 0; $i < $total_suppliers; $i++) {
			if (!is_array($cart['shipping'][$shippings[$i]])) {
				$cart['shipping'][$shippings[$i]] = array();
			}
			if (!is_array($cart['shipping'][$shippings[$i]]['rates'])) {
				$cart['shipping'][$shippings[$i]]['shipping'] = fn_get_shipping_name($shippings[$i], CART_LANGUAGE);
				$cart['shipping'][$shippings[$i]]['rates'] = array();
			}
			$cart['shipping'][$shippings[$i]]['rates'][$_temp[$i]] = '';
		}
	}

}

function fn_get_google_info($order_id)
{
	$google_info = db_get_field("SELECT data FROM ?:order_data WHERE order_id = ?i AND type = ?s", $order_id, GOOGLE_ORDER_DATA);

	return unserialize($google_info);
}

function fn_change_google_info($order_id, $info, $unset_values = '')
{
	$google_info = db_get_field("SELECT data FROM ?:order_data WHERE order_id = ?i AND type = ?s", $order_id, GOOGLE_ORDER_DATA);

	if (!empty($google_info)) {
		$google_info = unserialize($google_info);
	} else {
		$google_info = array();
	}

	if (!empty($unset_values)) {
		foreach ($unset_values as $k => $v) {
			unset($google_info[$v]);
		}
	}

	$google_info = fn_array_merge($google_info, $info);

	$_data = array (
		'order_id' => $order_id,
		'type' => GOOGLE_ORDER_DATA,
		'data' => serialize($google_info)
	);

	db_query('REPLACE INTO ?:order_data ?e', $_data);

	return true;
}

function fn_get_google_codes(&$cart, $xml_data)
{
	// Check discount coupon codes
	$codes = $xml_data->getElementByPath('/order-adjustment/merchant-codes');
	$coupons = $codes->getElementsByName('coupon-adjustment');
	$coupons_total = sizeof($coupons);

	// Cleanup
	$cart['coupons'] = array();
	$cart['pending_coupon'] = '';

	for ($j = 0; $j < $coupons_total; $j++) {
		$code = $coupons[$j]->getValueByPath('/code');
		if (!empty($code)) {
			$cart['pending_coupon'] = $code;
		}
	}

	fn_set_hook('get_google_codes', $cart, $xml_data, $codes);

	return true;
}

// This function sends the request to google to link cart order_id with their one.
function fn_associate_order_id($order_id, $transaction_id, $schema_url)
{
	$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $order_id);
	$processor_data = fn_get_payment_method_data($payment_id);

	$base_url = 'https://' . (($processor_data['params']['test'] == 'N') ? 'checkout.google.com' : 'sandbox.google.com/checkout') . '/cws/v2/Merchant/' . $processor_data['params']['merchant_id'];
	$request_url = $base_url . '/request';

	$post = array();
	$post[] = "<add-merchant-order-number xmlns='" . $schema_url . "' google-order-number='" . $transaction_id . "'>";
	$post[] = "<merchant-order-number>" . $order_id . "</merchant-order-number>";
	$post[] = "</add-merchant-order-number>";

	$_id = base64_encode($processor_data['params']['merchant_id'] . ":" . $processor_data['params']['merchant_key']);
	$headers[] = "Authorization: Basic $_id";
	$headers[] = "Accept: application/xml ";

	list($a, $return) = fn_https_request("POST", $request_url, $post, '', '', 'application/xml', '', '', '', $headers);

	return true;
}
?>
