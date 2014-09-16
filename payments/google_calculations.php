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
// $Id: google_calculations.php 10329 2010-08-03 14:46:04Z klerik $
//

define('AREA', 'C');
define('AREA_NAME' ,'customer');
define('SKIP_SESSION_VALIDATION', true);

require './../prepare.php';
require './../init.php';
include(DIR_LIB . 'xmldocument/xmldocument.php');

// TODO:
// 1. Add coupons calculation (merchant-code-string)
// 2. Price-included taxes - how to display? Now displays as zero tax

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
	fn_google_xml_error('GCC: failed to parse incoming XML');
}

if ($message_recognizer != 'merchant-calculation-callback') {
	fn_google_xml_error('GCC: incoming XML is not that we are expecting');
}

// Restart session
$google_sess_id = $root->getValueByPath('shopping-cart/merchant-private-data/additional_data/session_id');

if (empty($google_sess_id)) {
	fn_google_xml_error('GCC: failed to get session ID from XML');
}
Session::reset_id($google_sess_id);

$_SESSION['cart'] = empty($_SESSION['cart']) ? array() : $_SESSION['cart'];
$cart = & $_SESSION['cart'];

$currency_code = $root->getValueByPath('shopping-cart/merchant-private-data/additional_data/currency_code');
$response = array();
$adr = $root->getElementByPath('calculate/addresses');
$cds = $root->getElementByPath('calculate/merchant-code-strings');
if ($adr) {
	$addresses = $adr->getElementsByName('anonymous-address');
}

if ($cds) {
	$_codes = array();
	$codes = $cds->getElementsByName('merchant-code-string');
}

if (!empty($addresses)) {
	$total = sizeof($addresses);

	$gc_shippings = $root->getElementByPath('shopping-cart/merchant-private-data/additional_data/shippings');
	if ($gc_shippings) {
		$gc_methods = $gc_shippings->getElementsByName('method');
		$gc_methods_total = sizeof($gc_methods);
	}

	$response[] = '<merchant-calculation-results xmlns="http://checkout.google.com/schema/2">';
	$response[] = ' <results>';
	for ($i = 0; $i < $total; $i++) {
		$address_id = $addresses[$i]->getAttribute('id');

		$cart['user_data'] = array (
			's_address' => '',
			's_city' => $addresses[$i]->getValueByPath('/city'),
			's_state' => $addresses[$i]->getValueByPath('/region'),
			's_country' => $addresses[$i]->getValueByPath('/country-code'),
			's_zipcode' => $addresses[$i]->getValueByPath('/postal-code'),
			'b_address' => '',
			'b_city' => $addresses[$i]->getValueByPath('/city'),
			'b_state' => $addresses[$i]->getValueByPath('/region'),
			'b_country' => $addresses[$i]->getValueByPath('/country-code'),
			'b_zipcode' => $addresses[$i]->getValueByPath('/postal-code'),
			'phone' => '',
			'country' => '',
			'firstname' => '',
			'lastname' => '',
		);

		$country_fields = array(
			's_country' => 's_state', 
			'b_country' => 'b_state'
		);
		foreach ($country_fields as $_c => $_s) {
			// For UK google returns region description, instead of the code, so we need to get the state code manually
			if ($_c == 'UK') {
				$cart['user_data'][$_s] = db_get_field("SELECT a.code FROM ?:states as a LEFT JOIN ?:state_descriptions as b ON b.state_id = a.state_id AND lang_code = ?s WHERE a.country_code = ?s AND b.state = ?s", CART_LANGUAGE, $cart['user_data'][$_c], $cart['user_data'][$_s]);
			}
		}

		// Apply the codes entered on the Google side to the cart
		$_codes = fn_apply_google_codes($cart, $codes);

		// Find the shipping rates for each customer location
		list ($cart_products, $shipping_rates) = fn_calculate_cart_content($cart, $_SESSION['auth'], 'A', true, 'I', true);

		$use_taxes = false;
		$_taxes_list = fn_get_taxes();
		foreach ($_taxes_list as $v) {
			if ($v['price_includes_tax'] != 'Y') {
				$use_taxes = true;
				break;
			}
		}
		// Go throught all shipping methods, passes to google checkout and get rates (if calculated)
		for ($k = 0; $k < $gc_methods_total; $k++) {
			$_id = $gc_methods[$k]->getAttribute('id');

			$response[] = '   <result shipping-name="' . trim($gc_methods[$k]->getAttribute('name')) . '" address-id="' . $address_id . '">';
			if ($this_shipping = fn_get_google_shipping_rate($_id, $shipping_rates)) {
				fn_recalculate_taxes($cart, $shipping_rates, $this_shipping);

				$response[] = '    <shipping-rate currency="' . $currency_code . '">' . $this_shipping['rate'] . '</shipping-rate>';
				$response[] = '    <shippable>true</shippable>';
			} elseif ($_id == 'FREESHIPPING' && $cart['shipping_required'] != true) {
				$response[] = '    <shipping-rate currency="' . $currency_code . '">0</shipping-rate>';
				$response[] = '    <shippable>true</shippable>';
			} else {
				$response[] = '    <shipping-rate currency="' . $currency_code . '">0</shipping-rate>';
				$response[] = '    <shippable>false</shippable>';
			}

			if ($use_taxes) {
				$response[] = '    <total-tax currency="' . $currency_code . '">' . floatval($cart['tax_subtotal']) . '</total-tax>';
			}

			// Add information about COUPONS and GIFT CERTIFICATES
			if (is_array($_codes)) {
				fn_form_google_codes_response($response, $_codes, $currency_code);
			} else {
				$response[] = '    <merchant-code-results />';
			}
			$response[] = '   </result>';
		}
		
		if (empty($gc_methods_total)){
			$response[] = '<result address-id="'.$address_id.'"><total-tax currency="' . $currency_code . '">'.(floatval($cart['tax_subtotal']) ? $cart['tax_subtotal'] : 0).'</total-tax></result>';
		}
	}
	$response[] = ' </results>';
	$response[] = '</merchant-calculation-results>';
}

echo implode("\n", $response);
exit;

function fn_recalculate_taxes(&$cart, $shipping_rates, $shipping_data)
{
	$cart['tax_subtotal'] = 0;
	if (isset($cart['taxes']) && is_array($cart['taxes'])) {
		foreach ($cart['taxes'] as $tax) {
			if ($tax['price_includes_tax'] == 'N') {
				if (Registry::get('settings.General.tax_calculation') == 'unit_price') {
					foreach ($tax['applies'] as $k => $v) {
						if (strpos($k, 'P_') !== false) {
							$cart['tax_subtotal'] += $v;
						}
					}
				} else {
					$cart['tax_subtotal'] += $tax['applies']['P'];
				}
			}
		}
	}
	
	if (isset($shipping_data['ids']) && !empty($shipping_data['ids'])) {
		foreach ($shipping_data['ids'] as $_s_id) {
			if (isset($shipping_rates[$_s_id]['taxes']) && !empty($shipping_rates[$_s_id]['taxes'])) {
				foreach ($shipping_rates[$_s_id]['taxes'] as $tax_id => $tax) {
					if (in_array($tax_id, $shipping_data['tax_ids'])) {
						foreach ($tax as $val) {
							if ($val['price_includes_tax'] == 'N') {
								$cart['tax_subtotal'] += $val['tax_subtotal'];
							}
						}
					}
				}
			}
		}
	}
}

// FIXME: this function is place to google_checkout_response.php also
function fn_google_xml_error($error)
{
	echo
		"<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
		"<cart-error>".htmlspecialchars($error)."</cart-error>";
	exit;
}

function fn_get_google_shipping_rate($id, $shipping_rates)
{
	$shipping = '';

	if (is_array($shipping_rates) && !empty($shipping_rates[$id])) {
		$shipping['rate'] = !empty($shipping_rates[$id]['rates'][0]) ? $shipping_rates[$id]['rates'][0] : 0;
		$shipping['tax_ids'] = array('S_' . $id . '_0');
		$shipping['ids'] = array($id);
	}

	if(fn_check_suppliers_functionality()) {
		fn_companies_get_google_shipping_rate($id, $shipping);
	}

	fn_set_hook('get_google_shipping_rate', $id, $shipping);

	return $shipping;
}

function fn_companies_get_google_shipping_rate($id, &$shipping)
{
	$suppliers = Registry::get('view')->get_var('suppliers');

	if (!empty($suppliers)) {
		ksort($suppliers);
		$shippings_combination = explode("_", $id);
		$rate = 0;

		$temp = reset($suppliers);
		foreach ($shippings_combination as $v) {
			if (isset($temp['rates'][$v])) {
				$rate += $temp['rates'][$v]['rate'];
				$shipping['tax_ids'][] = 'S_' . $v . '_' . key($suppliers);
				$shipping['ids'][] = $v;
			} else {
				$shipping = '';
				return true;
			}
			$temp = next($suppliers);
		}
		$shipping['rate'] = $rate;
	}

	return true;
}

function fn_apply_google_codes(&$cart, $codes)
{
	$total_codes = sizeof($codes);
	$_codes = array();

	// Cleanup
	$cart['coupons'] = array();
	$cart['pending_coupon'] = '';

	for ($j = 0; $j < $total_codes; $j++) {
		$_code = $codes[$j]->getAttribute('code');
		$_codes[] = $_code;
		$cart['pending_coupon'] = $_code;
	}

	fn_set_hook('apply_google_codes', $cart, $_codes);

	return $_codes;
}

function fn_form_google_codes_response(&$response, $_codes, $currency_code)
{
 	$cart = & $_SESSION['cart'];

	$response[] = '    <merchant-code-results>';

	foreach ($_codes as $code) {
		$exist = false;
		if (isset($cart['coupons'][$code])) {

			$amount = 0;
			foreach ($cart['coupons'][$code] as $pr_id) {
				$amount += $cart['promotions'][$pr_id]['total_discount'];
			}

			$response[] = '<coupon-result>';
			$response[] = ' <valid>true</valid>';
			$response[] = ' <code>' . $code . '</code>';
			$response[] = ' <calculated-amount currency="' . $currency_code . '">' . $amount . '</calculated-amount>';
			$response[] = ' <message>Coupon is successfully applied</message>';
			$response[] = '</coupon-result>';

			$exist = true;
		}

		fn_set_hook('form_google_codes_response', $response, $exist, $code, $cart, $currency_code);

		if (!$exist) {
			$response[] = '<coupon-result>';
			$response[] = ' <valid>false</valid>';
			$response[] = ' <code>' . $code . '</code>';
			$response[] = ' <calculated-amount currency="' . $currency_code . '">0</calculated-amount>';
			$response[] = ' <message>No such coupon/gift certificate.</message>';
			$response[] = '</coupon-result>';
		}
	}

	$response[] = '    </merchant-code-results>';

	return true;
}
?>