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
// $Id: fedex.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }


include(DIR_SHIPPING_FILES . 'fedex/fedexdc.php');

function fn_get_fedex_rates($code, $weight_data, $location, &$auth, $shipping_settings, $package_info, $origination, $service_id = 0)
{
	if ($shipping_settings['fedex_enabled'] != 'Y') {
		return false;
	}

	$ground_codes = array (
		'90',
		'92'
	);

	$account_number = $shipping_settings['fedex']['account_number'];
	$meter_number = $shipping_settings['fedex']['meter_number'];

	$package_type = $shipping_settings['fedex']['package_type'];
	$drop_off_type = $shipping_settings['fedex']['drop_off_type'];

	$height = $shipping_settings['fedex']['height'];
	$width = $shipping_settings['fedex']['width'];
	$length = $shipping_settings['fedex']['length'];

	// Normalize supplier address data
	$origination_address1 = substr($origination['address'], 0, 32);
	preg_match_all("/[\d\w]/", $origination['zipcode'], $matches);
	$origination_postal = (!empty($matches[0]) ? implode('', $matches[0]) : '');
	preg_match_all("/[\d]/", $origination['phone'], $matches);
	$origination_phone = (!empty($matches[0]) ? implode('', $matches[0]) : '8001234567');
	$origination_phone = (strlen($origination_phone) >= 10) ? substr($origination_phone, 0, 10) : str_pad($origination_phone, 10, '0');

	$origination_name = substr($origination['name'], 0, 35);
	$origination_city = $origination['city'];
	$origination_state = $origination['state'];
	$origination_country = $origination['country'];


	// Normalize customer address data
	$destination_address1 = substr($location['address'], 0, 32);
	preg_match_all("/[\d\w]/", $location['zipcode'], $matches);
	$destination_postal = (!empty($matches[0]) ? implode('', $matches[0]) : '');
	preg_match_all("/[\d]/", $location['phone'], $matches);
	$destination_phone = (!empty($matches[0]) ? implode('', $matches[0]) : '');
	$destination_phone = (strlen($destination_phone) >= 10) ? substr($destination_phone, 0, 10) : str_pad($destination_phone, 10, '0');

	$destination_name = $location['firstname'].' '.$location['lastname'];
	$destination_city = $location['city'];
	$destination_state = $location['state'];
	$destination_country = $location['country'];

	$is_domestic = ($destination_country == $origination_country) ? true : false;

	// define weight unit and value
	$weight_unit = 'LBS';
	$weight_unit_short = 'LB';
	$weight = $weight_data['full_pounds'];

	$params = array (
		'fedex_uri' => ($shipping_settings['fedex']['test_mode'] == 'Y') ? 'https://gatewaybeta.fedex.com:443/GatewayDC' : 'https://gateway.fedex.com:443/GatewayDC',
	);

	$fed = new FedExDC($account_number, $meter_number, $params);
	$ship_data = array(
			'weight_units' => $weight_unit
			,11 => $destination_name				// recipient_name
			,13 => $destination_address1			// recipient_address_line1
			,15 => $destination_city				// recipient_city
			,16 => $destination_state				// recipient_state
			,17 => $destination_postal				// recipient_postal_code
			,18 => $destination_phone				// recipient_phone
			,50 => $destination_country				// recipient_country

			,4 => $origination_name					// sender_company
			,5 => $origination_address1				// sender_address_line1
			,7 => $origination_city					// sender_city
			,8 => $origination_state				// sender_state
			,9 => $origination_postal				// sender_postal_code
			,117 => $origination_country			// sender_country
	        ,183 => $origination_phone				// sender_phone

			,57 => $height							// package_height
			,58 => $width							// package_width
			,59 => $length							// package_length

			,1116 => 'I'							// dim_unit

			,1273 => $package_type					// packaging_type

			,1274 => $code					// service_type

			,23 => '1'								// pay_type - '1' = bill sender

			,1333 => $drop_off_type					// drop_off_type

			,1401 => $weight						// total_package_weight

			,116 => '1'								// package_total
			,68 => 'USD'							// customs_declared_value_currency_type
			,2399 => '1'							// signature: 1 - without signature
		//	,1119 => 'N'							// future day shipment flag (for ground shipping)
//			,1529 => 2
	);

	if ($is_domestic == false) {
		$customs_total = fn_format_price($_SESSION['cart']['total']);
		$ship_data[70] = 1;							// INTL: who pay the duties (2 - recipient)
		$ship_data[79] = 'Good contents';					// INTL: description of content
		$ship_data[80] = $origination_country;				// INTL: producer of goods
		$ship_data[82] = 1;								// INTL: number of units in one good
		$ship_data[1408] = '1.000000';						// INTL: value of each unit
		$ship_data[414] = $weight_unit_short;				// INTL: weight unit measure
		$ship_data[1407] = '0.1';							// INTL: weight of each unit
		$ship_data[1411] = $customs_total;					// INTL: customs total
	}


	if (in_array($code, $ground_codes)) {
		if ($code == '90') {
			$ship_data[440] = 'Y';
		}
		$ship_data[1119] = 'N';
		$ship_data[2399] = '2'; // indirect signature
//		$ship_ret = $fed->ship_ground($ship_data);
	} else {
//		$ship_ret = $fed->ship_express($ship_data);
	}

	//Check whether we use a domestic or international shipping
	if ($is_domestic == true) {
		// If shipping is domestic and fedex shipping is international than return False
		if (in_array($service_id, array('27', '28', '32', '29'))) {
			return defined('SHIPPING_DEBUG') ? array('error' => fn_get_lang_var('int_ship_instead_domestic')): false;
		}
	} else {
		// If shipping is international and fedex shipping is domestic than return False
		if (in_array($service_id, array('18', '24', '25', '30'))) {
			return defined('SHIPPING_DEBUG') ? array('error' => fn_get_lang_var('domestic_ship_instead_int')) : false;
		}
	}

	$ship_ret = $fed->rate_services($ship_data);
	$charge = $fed->lookup('net_charge_amount');
	
	$currencies = Registry::get('currencies');

	$return_currency = $fed->lookup('rate_currency_type');
	
	if (!empty($currencies[strtoupper($return_currency)])) {
		$charge = floatval($charge * $currencies[strtoupper($return_currency)]['coefficient']);
	}
	
	$result = array (
		'cost' => $charge
	);

	if (!empty($charge)) {
		return $result;
	} else {
		if (defined('SHIPPING_DEBUG')) {
			$error_tr = $fed->lookup('transaction_error_message');
			$error_req = $fed->getError();

			return array('error' => (!empty($error_req) ? ($error_req . '<br />') : '') . (!empty($error_tr) ? $error_tr : ''));
		}
	}

	return false;
}

function fn_fedex_ship($data, &$auth, $shipping_settings, $origination)
{
	$ground_codes = array (
		'90',
		'92'
	);

	$account_number = $shipping_settings['fedex']['account_number'];
	$meter_number = $shipping_settings['fedex']['meter_number'];

	$package_type = $shipping_settings['fedex']['package_type'];
	$drop_off_type = $shipping_settings['fedex']['drop_off_type'];

	$height = $shipping_settings['fedex']['height'];
	$width = $shipping_settings['fedex']['width'];
	$length = $shipping_settings['fedex']['length'];

	// Normalize supplier address data
	$origination_address1 = substr($origination['address'], 0, 32);
	preg_match_all("/[\d\w]/", $origination['zipcode'], $matches);
	$origination_postal = (!empty($matches[0]) ? implode('', $matches[0]) : '');
	preg_match_all("/[\d]/", $origination['phone'], $matches);
	$origination_phone = (!empty($matches[0]) ? implode('', $matches[0]) : '8001234567');
	$origination_phone = (strlen($origination_phone) >= 10) ? substr($origination_phone, 0, 10) : str_pad($origination_phone, 10, '0');

	$origination_name = substr($origination['company'], 0, 35);
	$origination_city = $origination['city'];
	$origination_state = $origination['state'];
	$origination_country = $origination['country'];


	// Normalize customer address data
	$destination_address1 = substr($location['address'], 0, 32);
	preg_match_all("/[\d\w]/", $location['zipcode'], $matches);
	$destination_postal = (!empty($matches[0]) ? implode('', $matches[0]) : '');
	preg_match_all("/[\d]/", $location['phone'], $matches);
	$destination_phone = (!empty($matches[0]) ? implode('', $matches[0]) : '');
	$destination_phone = (strlen($destination_phone) >= 10) ? substr($destination_phone, 0, 10) : str_pad($destination_phone, 10, '0');

	$destination_name = $location['firstname'].' '.$location['lastname'];
	$destination_city = $location['city'];
	$destination_state = $location['state'];
	$destination_country = $location['country'];

	$is_domestic = ($destination_country == $origination_country) ? true : false;

	// define weight unit and value
	$weight_unit = 'LBS';
	$weight_unit_short = 'LB';
	$weight = $weight_data['full_pounds'];

	$fed = new FedExDC($account_number, $meter_number);
	$ship_data = array(
			'weight_units' => $weight_unit
			,11 => $destination_name				// recipient_name
			,13 => $destination_address1			// recipient_address_line1
			,15 => $destination_city				// recipient_city
			,16 => $destination_state				// recipient_state
			,17 => $destination_postal				// recipient_postal_code
			,18 => $destination_phone				// recipient_phone
			,50 => $destination_country				// recipient_country

			,4 => $origination_name					// sender_company
			,5 => $origination_address1				// sender_address_line1
			,7 => $origination_city					// sender_city
			,8 => $origination_state				// sender_state
			,9 => $origination_postal				// sender_postal_code
			,117 => $origination_country			// sender_country
	        ,183 => $origination_phone				// sender_phone

			,57 => $height							// package_height
			,58 => $width							// package_width
			,59 => $length							// package_length

			,1116 => 'I'							// dim_unit

			,1273 => $package_type					// packaging_type

			,1274 => $code					// service_type

			,23 => '1'								// pay_type - '1' = bill sender

			,1333 => $drop_off_type					// drop_off_type

			,1401 => $weight						// total_package_weight

			,116 => '1'								// package_total
			,68 => 'USD'							// customs_declared_value_currency_type
			,2399 => '1'							// signature: 1 - without signature
		//	,1119 => 'N'							// future day shipment flag (for ground shipping)
//			,1529 => 2
	);

	$today = getdate(TIME);
	if ($today['wday'] == 0 || $today['wday'] == 6) { // if today is saturday or sunday, set ship date manually
		$next_time = strtotime(($today['wday'] == 0) ? "+1 day" : "+2 days");
		$next_date = getdate($next_time);
		$ship_data[24] = $next_date['year'].$next_date['month'].$next_date['day'];
	}

	if ($is_domestic == false) {
		$customs_total = fn_format_price($_SESSION['cart']['total']);
		$ship_data[70] = 1;							// INTL: who pay the duties (2 - recipient)
		$ship_data[79] = 'Good contents';					// INTL: description of content
		$ship_data[80] = $origination_country;				// INTL: producer of goods
		$ship_data[82] = 1;								// INTL: number of units in one good
		$ship_data[1408] = '1.000000';						// INTL: value of each unit
		$ship_data[414] = $weight_unit_short;				// INTL: weight unit measure
		$ship_data[1407] = '0.1';							// INTL: weight of each unit
		$ship_data[1411] = $customs_total;					// INTL: customs total
	}


	if (in_array($code, $ground_codes)) {
		if ($code == '90') {
			$ship_data[440] = 'Y';
		}
		$ship_data[1119] = 'N';
		$ship_data[2399] = '2'; // indirect signature
//		$ship_ret = $fed->ship_ground($ship_data);
	} else {
//		$ship_ret = $fed->ship_express($ship_data);
	}

	$ship_ret = $fed->rate_services($ship_data);
	$charge = $fed->lookup('net_charge_amount');
	$result = array (
		'cost' => $charge
	);

	if (!empty($charge)) {
		return $result;
	} else {
		if (defined('SHIPPING_DEBUG')) {
			return array('error' => $fed->getError());
		}
	}

	return false;
}

?>