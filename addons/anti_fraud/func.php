<?php

//
// $Id: func.php 8137 2009-10-28 12:38:53Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }


function fn_anti_fraud_place_order($order_id, &$action, &$order_status)
{
	
	if ($action == 'save' || defined('ORDER_MANAGEMENT')) {
		return true;
	}

	$return = array();

	$af_settings = Registry::get('addons.anti_fraud');

	if (empty($af_settings['anti_fraud_key'])) {
		return false;
	}

	$order_info = fn_get_order_info($order_id);

	if (empty($order_info['ip_address'])) {
		$return['B'][] = 'af_ip_not_found';
	}

	$risk_factor = 1;

	$request = array (
		'license_key' => $af_settings['anti_fraud_key'],
		'i' => $order_info['ip_address'],
		'city' => $order_info['b_city'],
		'region' => $order_info['b_state'],
		'postal' => $order_info['b_zipcode'],
		'country' => $order_info['b_country'],
		'domain' => substr($order_info['email'], strpos($order_info['email'], '@') +1),
	//	'forwardedIP' => $company_data['x_forwarded_for'],
	//	'requested_type' => 'premium';	// Which level (free, city, premium) of CCFD to use
		'emailMD5' => $order_info['email'] // CreditCardFraudDetection.php will take MD5 hash of e-mail address passed to emailMD5 if it detects '@' in the string
	);

	list($header, $_result) = fn_http_request('GET', 'http://www.maxmind.com/app/ccv2r', $request);

	$result = array();
	$_result = explode(';', $_result);	
	if (is_array($_result)) {
		foreach ($_result as $v) {
			$tmp = explode('=', $v);
			$result[$tmp[0]] = $tmp[1];
		}
	}
	unset($_result);

	if (!empty($result['err'])) {
		$return['B'][] = 'af_'.strtolower($result['err']);
		$risk_factor *= AF_ERROR_FACTOR;
	} else {
		// Check if order total greater than defined
		if (!empty($af_settings['anti_fraud_max_order_total']) && floatval($order_info['total']) > floatval($af_settings['anti_fraud_max_order_total'])) {
			$risk_factor *= AF_ORDER_TOTAL_FACTOR;
			$return['B'][] = 'af_big_order_total';
		}

		if (!empty($order_info['user_id'])) {
		
			// Check if this customer has processed orders
			$amount = db_get_field("SELECT COUNT(*) FROM ?:orders WHERE status IN ('P','C') AND user_id = ?i", $order_info['user_id']);
			if (!empty($amount)) {
				$risk_factor /= AF_COMPLETED_ORDERS_FACTOR;
				$return['G'][] = 'af_has_successfull_orders';
			}

			// Check if this customer has failed orders
			$amount = db_get_field("SELECT COUNT(*) FROM ?:orders WHERE status IN ('D','F') AND user_id = ?i", $order_info['user_id']);
			if (!empty($amount)) {
				$risk_factor *= AF_FAILED_ORDERS_FACTOR;
				$return['B'][] = 'af_has_failed_orders';
			}
		}

		if ($result['countryMatch'] == 'No') {
			$return['B'][] = 'af_country_doesnt_match';
		}

		if ($result['highRiskCountry'] == 'Yes') {
			$return['B'][] = 'af_high_risk_country';
		}

		if (!empty($af_settings['anti_fraud_safe_distance']) && intval($result['distance']) > intval($af_settings['anti_fraud_safe_distance'])) {
			$return['B'][] = 'af_long_distance';
		}

		if ($result['carderEmail'] == 'Yes') {
			$return['B'][] = 'af_carder_email';
		}

		$risk_factor += floatval($result['score']);

		if ($risk_factor > 10) {
			$risk_factor = 10;
		}
	}

	$return['risk_factor'] = $risk_factor;
	
	if (floatval($risk_factor) >= floatval($af_settings['anti_fraud_risk_factor'])) {
		$action = 'save';
		$order_status = 'O';
		$return['B'][] = 'af_high_risk_factor';
	} else {
		$return['G'][] = 'af_low_risk_factor';
	}

	$return = serialize($return);
	$data = array (
		'order_id' => $order_id,
		'type' => 'F', //fraud checking data
		'data' => $return,
	);
	db_query("REPLACE INTO ?:order_data ?e", $data);
	
	return true;
}

function fn_anti_fraud_get_order_info(&$order, &$additional_data)
{
	
	if (!empty($additional_data['F'])) {
		$order['fraud_checking'] = @unserialize($additional_data['F']);
	}

	return true;
}

?>
