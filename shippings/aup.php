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
// $Id: aup.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_get_aup_rates($code, $weight_data, $location, &$auth, $shipping_settings, $package_info, $origination)
{
	if ($shipping_settings['aup_enabled'] != 'Y') {
		return false;
	}
	$weight = $weight_data['full_pounds'] * 453.6;

	//Registered Post International: price as Air Mail, plus $5, weight limit of 2kg.
	if ($code == 'RPI' && $weight > 2000) {
		return array('error' => fn_get_lang_var('illegal_item_weight'));
	}
	$request = array (
		'Pickup_Postcode' => $origination['zipcode'],
		'Destination_Postcode' => $location['zipcode'],
		'Country' => $location['country'],
		'Weight' => $weight,
		'Length' => ($shipping_settings['aup']['length'] * 10),
		'Width' => ($shipping_settings['aup']['width'] * 10),
		'Height' => ($shipping_settings['aup']['height'] * 10),
		'Service_type' => ($code == 'RPI')? 'AIR' : $code,
		'Quantity' => 1,
	);
	list ($header, $result) = fn_http_request('GET', 'http://drc.edeliver.com.au/ratecalc.asp', $request);

	if (!empty($result)) {
		$result = explode("\n", $result);
		if (preg_match("/charge=([\d\.]+)/i", $result[0], $matches)) {
			if (!empty($matches[1])) {
				$cost = (double)trim($matches[1]);
				if ($code == 'RPI') {
					$cost += (double)$shipping_settings['aup']['rpi_fee'];
				}
				if ($shipping_settings['aup']['use_delivery_confirmation'] == 'Y') {
					$cost += ($code == 'STANDARD' || $code == 'EXPRESS')? (double)$shipping_settings['aup']['delivery_confirmation_cost'] : (double)$shipping_settings['aup']['delivery_confirmation_international_cost'];
				}
				return array('cost' => $cost);
			} else {
				if (defined('SHIPPING_DEBUG') && preg_match("/err_msg=([\w ]*)/i", $result[2], $matches)) {
					return array('error' => $matches[1]);
				}
			}
		}
	}

	return false;
}

?>