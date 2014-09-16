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
// $Id: google_checkout.php 10329 2010-08-03 14:46:04Z klerik $
//

if (!defined('AREA')) {die('Access denied');}

$index_script = Registry::get('customer_index');

if (defined('PAYMENT_NOTIFICATION')) {

	if (!empty($_SESSION['order_id'])) {
		fn_order_placement_routines($_SESSION['order_id']);
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('order_was_not_placed'));
		fn_redirect(Registry::get('config.http_location') . "/$index_script?dispatch=checkout.cart");
	}


} elseif (!empty($_payment_id) && !fn_cart_is_empty($cart) && $processor_data['params']['policy_agreement'] == 'Y') {

	$return_url = Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.notify&amp;payment=google_checkout&amp;" . SESS_NAME . '=' . Session::get_id();
	$edit_cart_url = Registry::get('config.current_location') . "/$index_script?dispatch=checkout.cart";
	$calculation_url = (($processor_data["params"]["test"] == 'N') ? Registry::get('config.https_location') : Registry::get('config.current_location')) . "/payments/google_calculations.php";

	$_currency = $processor_data['params']['currency'];
	$base_domain = 'https://' . (($processor_data['params']['test'] == 'N') ? 'checkout.google.com' : 'sandbox.google.com/checkout');
	$base_url = $base_domain . '/cws/v2/Merchant/' . $processor_data['params']['merchant_id'];
	$checkout_url = $base_url . '/checkout';
	$request_url = $base_url . '/request';

	// Form XML array with cart items
	$_items = '';
	$taxes = '';
	
	$google_products = $cart_products;
	
	fn_set_hook('google_products', $google_products, $cart);
	
	if (!empty($google_products) && is_array($google_products)) {
		foreach ($google_products as $k => $v) {
			$item_options = '';
			if (!empty($v['product_options'])) {
				$_options = fn_get_selected_product_options_info($cart['products'][$k]['product_options']);
				foreach ($_options as $opt) {
				    $item_options .= $opt['option_name'] . ': ' . $opt['variant_name'] . '; ';
				}
				$item_options = ' [' . trim($item_options, '; ') . ']';
			}

	$_items .= '<item>' .
					'<merchant-item-id>' . $v['product_id'] . '</merchant-item-id>' .
					'<item-name>' . strip_tags($v['product']) . $item_options . '</item-name>'.
					'<item-description>' . substr(strip_tags($v['short_description']), 0, 299) . '</item-description>' .
					"<unit-price currency='" . $_currency . "'>" . fn_format_price($v['price']) . '</unit-price>' .
					'<quantity>' . $v['amount'] . '</quantity>' .
				'</item>';
		}
	}

	fn_get_google_add_items($_items, $cart, $_currency);

	// Prepare taxes
	$_taxes_list = fn_get_taxes();
	$taxes = '';
	foreach ($_taxes_list as $v) {
		if ($v['price_includes_tax'] != 'Y') {
			$_tax_rate = !empty($processor_data['params']['default_taxes'][$v['tax_id']]) ? fn_format_price($processor_data['params']['default_taxes'][$v['tax_id']])/100 : 0;
			$taxes .= "
		<default-tax-rule>
		<shipping-taxed>false</shipping-taxed>
		<rate>$_tax_rate</rate>
		<tax-area>
			<us-country-area country-area=\"FULL_50_STATES\" />
		</tax-area>
		</default-tax-rule>";
		}
	}

	if (!empty($taxes)) {
		$taxes = "<tax-tables merchant-calculated=\"true\">
					<default-tax-table>
					<tax-rules>
						$taxes
					</tax-rules>
					</default-tax-table>
				</tax-tables>";
	}

	// ******************************** Prepare shippings *************************

	$_shipping_methods = fn_prepare_google_shippings($processor_data);
	// non-real shipping is for google to display that no shipping available (otherwise there will be no such massage and customer will place order)
	if (empty($_shipping_methods) && $cart['shipping_required'] == true){
		$_shipping_methods[0]['shipping'] = "--";
		$_shipping_methods[0]['shipping_id'] = 'id';
	}elseif($cart['shipping_required'] != true){
		$_shipping_methods = array();
	}

	$shippings = "<shipping-methods>";
	$private_ship_data = '<shippings>';
	foreach ($_shipping_methods as $_shipping) {
		$shipping_name = htmlspecialchars($_shipping['shipping'] . (!empty($_shipping['delivery_time']) ? ' ('.$_shipping['delivery_time'].')' : ''));
		// Get shipping_rate
		$_ship_rate = fn_google_default_shipping_rate($_shipping['shipping_id'], $processor_data);
		$private_ship_data .= "<method name=\"$shipping_name\" id=\"$_shipping[shipping_id]\" />\n";
		$shippings .= "
		<merchant-calculated-shipping name=\"$shipping_name\">
		  <price currency=\"$_currency\">$_ship_rate</price>
			 <shipping-restrictions>
			   <allowed-areas>
				 <world-area />
			   </allowed-areas>
			 </shipping-restrictions>
			 <address-filters>
			   <allowed-areas>
				 <world-area />
			   </allowed-areas>
			 </address-filters>
		</merchant-calculated-shipping>";
	}
	$private_ship_data .= '</shippings>';
	$shippings .= "</shipping-methods>";

	// ******************************** /Prepare shippings *************************

	 // Form a discount part of a form
	if (!empty($cart['subtotal_discount']) && floatval($cart['subtotal_discount'])) {
	$_items .= "
	  <item>
        <item-name>" . fn_get_lang_var('order_discount') . "</item-name>
        <item-description>" . fn_get_lang_var('order_discount') . "</item-description>
        <unit-price currency='" . $_currency . "'>" . -$cart['subtotal_discount'] . "</unit-price>
        <quantity>1</quantity>
      </item>";
	}

	// Form a surcharge part of the payment
	if (!empty($_payment_id)) {
		$_data = db_get_row("SELECT a_surcharge, p_surcharge FROM ?:payments WHERE payment_id = ?i", $_payment_id);
		$cart['payment_surcharge'] = 0;
		if (floatval($_data['a_surcharge'])) {
			$cart['payment_surcharge'] += $_data['a_surcharge'];
		}
		if (floatval($_data['p_surcharge'])) {
			$cart['payment_surcharge'] += fn_format_price($cart['total'] * $_data['p_surcharge'] / 100);
		}
		if (!empty($cart['payment_surcharge'])) {
			$_items .= "
			  <item>
				<item-name>" . fn_get_lang_var('surcharge') . "</item-name>
				<item-description>" . fn_get_lang_var('surcharge_for_the_payment') . "</item-description>
				<unit-price currency='" . $_currency . "'>" . $cart['payment_surcharge'] . "</unit-price>
				<quantity>1</quantity>
				<tax-table-selector>no_tax</tax-table-selector>
			  </item>";
		}
	}

	// The cart in XML format
	$xml_cart = "<?xml version='1.0' encoding='UTF-8'?>
	<checkout-shopping-cart xmlns='http://checkout.google.com/schema/2'>
	  <shopping-cart>
		<merchant-private-data>
		  <additional_data>
		   <session_id>" . Session::get_id() . "</session_id>
		   <currency_code>" . $_currency . "</currency_code>" . 
			$private_ship_data . 
		  "</additional_data>
		</merchant-private-data>
		<items>" . 
			$_items . 
		"</items>
	  </shopping-cart>
	  <checkout-flow-support>
		<merchant-checkout-flow-support>
		  <platform-id>971865505315434</platform-id>
		  <request-buyer-phone-number>true</request-buyer-phone-number>
		  <edit-cart-url>" . $edit_cart_url . "</edit-cart-url>
		  <merchant-calculations>
			<merchant-calculations-url>" . $calculation_url . "</merchant-calculations-url>
			" . fn_google_coupons_calculation($cart) . "
		  </merchant-calculations>
		  <continue-shopping-url>" . $return_url . "</continue-shopping-url>
		" . $shippings
		 . $taxes. "
		</merchant-checkout-flow-support>
	  </checkout-flow-support>
	</checkout-shopping-cart>";

	$signature = fn_calc_hmac_sha1($xml_cart, $processor_data['params']['merchant_key']);
	$b64_cart = base64_encode($xml_cart);
	$b64_signature = base64_encode($signature);

	$checkout_buttons[] = '
	<html>
	<body>
	<form method="post" action="' . $checkout_url . '" name="BB_BuyButtonForm">
		<input type="hidden" name="cart" value="' . $b64_cart . '" />
		<input type="hidden" name="signature" value="' . $b64_signature . '" />
		<input alt="" src="' . $base_domain . '/buttons/checkout.gif?merchant_id=' . $processor_data['params']['merchant_id'] . '&amp;w=160&amp;h=43&amp;style=' . $processor_data['params']['button_type'] . '&amp;variant=text&amp;loc=en_US" type="image"/>
		</form>
	 </body>
	</html>';
	
}

//
// The CalcHmacSha1 function computes the HMAC-SHA1 signature that you need
// to send a Checkout API request. The signature is used to verify the
// integrity of the data in your API request.
//
// @param    $data    message data
// @return   $hmac    value of the calculated HMAC-SHA1
function fn_calc_hmac_sha1($data, $key) 
{
	$blocksize = 64;
    $hashfunc = 'sha1';

    if (strlen($key) > $blocksize) {
        $key = pack('H*', $hashfunc($key));
    }

    $key = str_pad($key, $blocksize, chr(0x00));
    $ipad = str_repeat(chr(0x36), $blocksize);
    $opad = str_repeat(chr(0x5c), $blocksize);
    $hmac = pack(
                    'H*', $hashfunc(
                            ($key^$opad).pack(
                                    'H*', $hashfunc(
                                            ($key^$ipad).$data
                                    )
                            )
                    )
                );
    return $hmac;
}

function fn_prepare_google_shippings($processor_data)
{
	$_shipping_methods = db_get_array("SELECT a.shipping_id, b.shipping, b.delivery_time FROM ?:shippings as a LEFT JOIN ?:shipping_descriptions as b ON a.shipping_id = b.shipping_id AND b.lang_code = ?s WHERE a.status = 'A'", CART_LANGUAGE);

	// Add Free Shipping method
	if ($processor_data['params']['free_shipping'] == 'Y') {
		$_shipping_methods[] = array (
			'shipping_id' => 'FREESHIPPING',
			'shipping' => fn_get_lang_var('free_shipping'),
		);
	}

	if(fn_check_suppliers_functionality()) {
		fn_companies_prepare_google_shippings($_shipping_methods);
	}

	fn_set_hook('prepare_google_shippings', $_shipping_methods);

	return $_shipping_methods;
}

// Define strings whether coupons should be calculated or not
function fn_google_coupons_calculation($cart)
{
	$string = '';
	$string .= '<accept-merchant-coupons>' . ((!empty($cart['no_coupons'])) ? 'false' : 'true') . '</accept-merchant-coupons>';

	fn_set_hook('google_coupons_calculation', $string);

	return $string;
}

// Check some additional items that should be passed to the Google Checkout
function fn_get_google_add_items(&$_items, $cart, $_currency)
{
	fn_set_hook('get_google_add_items', $_items, $cart, $_currency);

	return true;
}

// Get default shipping rates
function fn_google_default_shipping_rate($shipping_id, $processor_data)
{
	$rate = isset($processor_data['params']['default_shippings'][$shipping_id]) ? $processor_data['params']['default_shippings'][$shipping_id] : 0;

	return empty($rate) ? 0 : $rate;
}

function fn_companies_prepare_google_shippings(&$shipping_methods)
{
	// Find out the needed suppliers and the shipping available for them
	$needed_suppliers = array();
	$needed_suppliers_empty = array();
	$is_default_filled = false;
	foreach ($_SESSION['cart']['products'] as $k => $v) {
		if (!empty($v['company_id'])) {
			$s_id = $v['company_id'];
			if (empty($needed_suppliers[$s_id])) {
				$needed_suppliers[$s_id] = array();
				$ship_suppliers = fn_get_companies_shipping_ids($s_id);

				foreach ($shipping_methods as $k => $v) {
					if (in_array($v['shipping_id'], $ship_suppliers)) {
						$needed_suppliers[$s_id][$v['shipping_id']] = $v['shipping'];
					}
				}

				if (empty($needed_suppliers[$s_id])) {
					$shipping_methods = array();
					return true;
				}
			}
		} elseif (!$is_default_filled && empty($needed_suppliers_empty[0])) {
			$is_default_filled = true;
			$ship_suppliers = db_get_fields("SELECT shipping_id FROM ?:shippings WHERE company_id = '0'");
			foreach ($shipping_methods as $k => $v) {
				if (in_array($v['shipping_id'], $ship_suppliers)) {
					$needed_suppliers_empty[0][$v['shipping_id']] = $v['shipping'];
				}
			}
		}
	}

	if (empty($needed_suppliers)) {
		return true;
	} else {
		$needed_suppliers = $needed_suppliers_empty + $needed_suppliers;
	}

	// Generate the available combiantions of all shippings for suppliers
	ksort($needed_suppliers, SORT_NUMERIC);

	$combination_count =1;
	$j = 0;
	$supp_count = count($needed_suppliers);
	$curr_pos = array();
	$curr_pos['ship_count'] = 1;

	foreach ($needed_suppliers as $s_id => $shippings) {
		$j++;
		$curr_count = count($shippings);
		$combination_count *= $curr_count;
		$shippings_count[$s_id] = $curr_count;
		$magic[$s_id] = 1;
		if ($j == $supp_count) {
			$curr_pos['supp_id'] = $s_id;
		}
	}

	$new = array();

	for ($i = 0; $i < $combination_count; $i++) {
		if ($i > 0) {
			if (fn_companies_update_magic_hash($magic, $shippings_count, $curr_pos) === false) {
				break;
			}
		}
		fn_companies_add_combination($i, $new, $magic, $needed_suppliers);

	}

	// Form the list of shippings
	$_shipping_methods = array();
	if (!empty($new)) {
		foreach ($new as $items) {

			$string = '';
			$is_identical = true;
			$f_s_id = key($items);
			$f_shipping_id = current($items);

			foreach ($items as $k => $v) {
				if ($f_shipping_id != $v) {
					$is_identical = false;
				}
				$string .= $needed_suppliers[$k][$v] . ' + ';
			}

			if (!$is_identical) {
				$string = rtrim($string, ' + ');
			} else {
				$string = $needed_suppliers[$f_s_id][$f_shipping_id];
			}

			if (array_key_exists(implode('_', $items), $_shipping_methods) == false) {
				$_shipping_methods[implode('_', $items)] = array (
					'shipping' => $string,
					'shipping_id' => implode('_', $items)
				);
			}

		}
	}

	$shipping_methods = $_shipping_methods;

	return true;
}

function fn_companies_add_combination($i, &$new, $magic, $needed_suppliers)
{
	foreach ($needed_suppliers as $s_id => $shs) {
		$new[$i][$s_id] = fn_companies_get_shipping_id($s_id, $magic[$s_id], $needed_suppliers);
	}
}

function fn_companies_update_magic_hash(&$magic, $shippings_count, &$curr_pos)
{
	// find the first supplier $supp_id from the end where shipping changing can be performed
	end($magic);
	end($shippings_count);

	$is_all_checked = false;
	$supp_id = -1;
	while ($is_all_checked === false) {
		if (current($magic) < current($shippings_count)) {
			$supp_id = key($magic);
			$is_all_checked = true;
		} else {
			prev($shippings_count);
			$is_all_checked = !prev($magic);
		}
	}

	if ($supp_id == -1) {
		return false;
	}

	reset($magic);
	reset($shippings_count);

	if ($curr_pos['supp_id'] == $supp_id) {
		if ($curr_pos['ship_count'] < $shippings_count[$supp_id]) {
			$magic[$supp_id]++;
			$curr_pos['ship_count'] = $magic[$supp_id];
		}
	} elseif ($curr_pos['supp_id'] > $supp_id) {
		foreach ($magic as $s_id => $ship_count) {
			if ($s_id > $supp_id) {
				$magic[$s_id] = 1;
			}
		}
		$magic[$supp_id]++;
		$curr_pos['ship_count'] = $magic[$supp_id];
		$curr_pos['supp_id'] = $supp_id;
	} else {
		$magic[$supp_id]++;
		$curr_pos['ship_count'] = $magic[$supp_id];
		$curr_pos['supp_id'] = $supp_id;
	}

	return true;

}

function fn_companies_get_shipping_id($s_id, $shipping_offset, $needed_suppliers)
{
	$c = 1;
	foreach ($needed_suppliers[$s_id] as $shipping_id => $shn) {
		if ($shipping_offset == $c) {
			return $shipping_id;
		}
		$c++;
	}
	return 0;
}

?>