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
// $Id: shippings.php 10490 2010-08-23 11:30:06Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

$_REQUEST['shipping_id'] = empty($_REQUEST['shipping_id']) ? 0 : $_REQUEST['shipping_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$suffix = '';
	
	//
	// Add/Update shipping rates
	//
	if ($mode == 'update_rates') {
		
		if (fn_check_company_id('shippings', 'shipping_id', $_REQUEST['shipping_id'])) {

			$rate_exists = db_get_field("SELECT COUNT(*) FROM ?:shipping_rates WHERE shipping_id = ?i AND destination_id = ?i AND rate_id != ?i", $_REQUEST['shipping_id'], $_REQUEST['destination_id'], $_REQUEST['rate_id']);
	
			if (empty($rate_exists)) {
				$rate_types = array('C','W','I'); // Rate types: Cost, Weight, Items
				$normalized_data = array();
				foreach ($rate_types as $type) {
					// Update rate values
					if (is_array($_REQUEST['rate_data'][$type])) {
						foreach ($_REQUEST['rate_data'][$type] as $k => $v) {
							$v['amount'] = strval(($type == 'I') ? intval($v['amount']) : floatval($v['amount']));
							$v['value'] = fn_format_price($v['value']);
							$v['per_unit'] = empty($v['per_unit']) ? 'N' : $v['per_unit'];
							$normalized_data[$type]["$v[amount]"] = array ('value' => $v['value'], 'type' => $v['type'], 'per_unit' => $v['per_unit']);
						}
					}
	
					// Add new rate values
					if (is_array($_REQUEST['add_rate_data'][$type])) {
						foreach ($_REQUEST['add_rate_data'][$type] as $k => $v) {
							$v['amount'] = strval(($type == 'I') ? intval($v['amount']) : floatval($v['amount']));
							$v['value'] = fn_format_price($v['value']);
							$v['per_unit'] = empty($v['per_unit']) ? 'N' : $v['per_unit'];
	
							if (!isset($normalized_data[$type][$v['amount']]) || floatval($normalized_data[$type][$v['amount']]['value']) == 0) {
								$normalized_data[$type]["$v[amount]"] = array ('value' => $v['value'], 'type' => $v['type'], 'per_unit' => $v['per_unit']);
							}
						}
					}
	
					if (is_array($normalized_data[$type])) {
						ksort($normalized_data[$type], SORT_NUMERIC);
					}
				}
	
				if (is_array($normalized_data)) {
					foreach ($normalized_data as $k => $v) {
						if ((count($v)==1) && (floatval($v[0]['value'])==0)) {
							unset($normalized_data[$k]);
							continue;
						}
					}
				}
	
				if (fn_is_empty($normalized_data)) {
					db_query("DELETE FROM ?:shipping_rates WHERE rate_id = ?i", $_REQUEST['rate_id']);
				} else {
					$normalized_data = serialize($normalized_data);
					db_query("REPLACE INTO ?:shipping_rates (rate_value, destination_id, shipping_id) VALUES(?s, ?i, ?i)", $normalized_data, $_REQUEST['destination_id'], $_REQUEST['shipping_id']);
				}
	
			} else {
				fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_rate_zone_exists'));
			}
		}

		$suffix = ".update?shipping_id=$_REQUEST[shipping_id]&destination_id=$_REQUEST[destination_id]";
	}

	// Delete selected rates
	if ($mode == 'delete_rate_values') {

		if (fn_check_company_id('shippings', 'shipping_id', $_REQUEST['shipping_id'])) {
			fn_delete_rate_values($_REQUEST['rate_id'], $_REQUEST['delete_rate_data'], $_REQUEST['shipping_id'], $_REQUEST['destination_id']);
		}

		$suffix = ".update?shipping_id=$_REQUEST[shipping_id]&destination_id=$_REQUEST[destination_id]";
	}
	//
	// Update shipping method
	//
	if ($mode == 'update_shipping') {

		if ((!empty($_REQUEST['shipping_id']) && fn_check_company_id('shippings', 'shipping_id', $_REQUEST['shipping_id'])) || empty($_REQUEST['shipping_id'])) {
			fn_set_company_id($_REQUEST['shipping_data']);
			$_REQUEST['shipping_id'] = fn_update_shipping($_REQUEST['shipping_data'], $_REQUEST['shipping_id']);
		}

		$suffix = ".update?shipping_id=" . $_REQUEST['shipping_id'];
	}

	//
	// Update shipping methods
	//
	if ($mode == 'update_shippings') {

		if (is_array($_REQUEST['shipping_data'])) {
			foreach ($_REQUEST['shipping_data'] as $k => $v) {
				if (empty($v['shipping'])) {
					continue;
				}
				
				if (fn_check_company_id('shippings', 'shipping_id', $k)) {
					$v['usergroup_ids'] = empty($v['usergroup_ids']) ? '' : implode(',', $v['usergroup_ids']);
					db_query("UPDATE ?:shippings SET ?u WHERE shipping_id = ?i", $v, $k);
					db_query("UPDATE ?:shipping_descriptions SET ?u WHERE shipping_id = ?i AND lang_code = ?s", $v, $k, DESCR_SL);
				}
			}
		}

		$suffix .= '.manage';
	}

	//
	// Delete shipping methods
	//
	//TODO make security check for company_id
	if ($mode == 'delete_shippings') {

		if (!empty($_REQUEST['shipping_ids'])) {
			foreach ($_REQUEST['shipping_ids'] as $id) {
				if (fn_check_company_id('shippings', 'shipping_id', $id)) {
					fn_delete_shipping($id);
				}
			}
		}

		$suffix = '.manage';
	}

	return array(CONTROLLER_STATUS_OK, "shippings$suffix");
}

// -------------------------------------- GET requests -------------------------------


if ($mode == 'test') {

	define('SHIPPING_DEBUG', true);
	if (!empty($_REQUEST['service_id'])) {
		// Set package information (weight is only needed)
		$weight = floatval($_REQUEST['weight']);
		$weight = !empty($weight) ? sprintf("%.2f", $weight) : '0.01';
		
		$package_info = array(
			'W' => $weight,
			'C' => 100,
			'I' => 1,
			'origination' => array(
				'name' => Registry::get('settings.Company.company_name'),
				'address' => Registry::get('settings.Company.company_address'),
				'city' => Registry::get('settings.Company.company_city'),
				'country' => Registry::get('settings.Company.company_country'),
				'state' => Registry::get('settings.Company.company_state'),
				'zipcode' => Registry::get('settings.Company.company_zipcode'),
				'phone' => Registry::get('settings.Company.company_phone'),
				'fax' => Registry::get('settings.Company.company_fax'),
			)
		);

		// Set default location
		$location = fn_get_customer_location(array('user_id' => 0), array());
		$data = fn_calculate_realtime_shipping_rate($_REQUEST['service_id'], $location, $package_info, $auth);

		$view->assign('data', $data);
		$view->assign('weight', $_REQUEST['weight']);
		$view->assign('service', db_get_field("SELECT description FROM ?:shipping_service_descriptions WHERE service_id = ?i AND lang_code = ?s", $_REQUEST['service_id'], DESCR_SL));
	}

	$view->display('views/shippings/components/test.tpl');
	exit;

// Add new shipping method
} elseif ($mode == 'add') {

	$rate_data = array(
		'rate_value' => array(
			'C' => array(),
			'W' => array(),
			'I' => array(),
		)
	);

	fn_add_breadcrumb(fn_get_lang_var('shipping_methods'),"shippings.manage");

	$view->assign('shipping_settings', fn_get_settings('Shippings'));
	$view->assign('services', fn_get_shipping_services());
	$view->assign('rate_data', $rate_data);
	$view->assign('taxes', fn_get_taxes());
	$view->assign('usergroups', fn_get_usergroups('C', DESCR_SL));
	$view->assign('companies', fn_get_short_companies());

// Collect shipping methods data
} elseif ($mode == 'update') {
	$shipping = db_get_row("SELECT ?:shippings.*, ?:shipping_descriptions.shipping, ?:shipping_descriptions.delivery_time FROM ?:shippings LEFT JOIN ?:shipping_descriptions ON ?:shipping_descriptions.shipping_id = ?:shippings.shipping_id AND ?:shipping_descriptions.lang_code = ?s WHERE ?:shippings.shipping_id = ?i", DESCR_SL, $_REQUEST['shipping_id']);

	if (empty($shipping)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}
	
	$shipping['tax_ids'] = empty($shipping['tax_ids']) ? array() : fn_explode(',', $shipping['tax_ids']);
	$shipping['icon'] = fn_get_image_pairs($shipping['shipping_id'], 'shipping', 'M', true, true, DESCR_SL);

	Registry::set('navigation.tabs', array (
		'general' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'shipping_charges' => array (
			'title' => fn_get_lang_var('shipping_charges'),
			'js' => true
		),
	));

	$view->assign('shipping', $shipping);

	$destinations = array();
	if ($shipping['rate_calculation'] == 'M') {
		$destinations = fn_get_destinations();
		$destination_id = !isset($_REQUEST['destination_id']) ? $destinations[0]['destination_id'] : $_REQUEST['destination_id'];
		foreach ($destinations as $k => $v) {
			$destinations[$k]['rates_defined'] = db_get_field("SELECT IF(rate_value = '', 0, 1) FROM ?:shipping_rates WHERE shipping_id = ?i AND destination_id = ?i", $_REQUEST['shipping_id'], $v['destination_id']);
			if (!empty($shipping['localization'])) { // check available destinations, but skip default destination
				$_s = fn_explode(',', $shipping['localization']);
				$_l = fn_explode(',', $v['localization']);
				if (!array_intersect($_s, $_l)) {
					unset($destinations[$k]);
				}
			}
		}
	} else {
		$destination_id = 0;
	}

	$rate_data = db_get_row("SELECT rate_id, rate_value, destination_id FROM ?:shipping_rates WHERE shipping_id = ?i AND destination_id = ?i", $_REQUEST['shipping_id'], $destination_id);

	$view->assign('services', fn_get_shipping_services());

	if (!empty($rate_data)) {
		$rate_data['rate_value'] = unserialize($rate_data['rate_value']);
	}

	if (empty($rate_data['rate_value']['C'][0])) {
		$rate_data['rate_value']['C'][0] = array();
	}
	if (empty($rate_data['rate_value']['W'][0])) {
		$rate_data['rate_value']['W'][0] = array();
	}
	if (empty($rate_data['rate_value']['I'][0])) {
		$rate_data['rate_value']['I'][0] = array();
	}

	$view->assign('rate_data', $rate_data);
	unset($rate_data);

	fn_add_breadcrumb(fn_get_lang_var('shipping_methods'),"shippings.manage");

	$view->assign('destinations', $destinations);
	$view->assign('destination_id', $destination_id);
	$view->assign('taxes', fn_get_taxes());
	$view->assign('usergroups', fn_get_usergroups('C', DESCR_SL));
	$view->assign('companies', fn_get_short_companies());

// Show all shipping methods
} elseif ($mode == 'manage') {

	$view->assign('shippings', fn_get_all_shippings());
	
	$view->assign('usergroups', fn_get_usergroups('C', DESCR_SL));

// Delete shipping method
} elseif ($mode == 'delete_shipping') {

	if (!empty($_REQUEST['shipping_id']) && fn_check_company_id('shippings', 'shipping_id', $_REQUEST['shipping_id'])) {
		fn_delete_shipping($_REQUEST['shipping_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "shippings.manage");

// Delete selected rate
} elseif ($mode == 'delete_rate_value') {

	fn_delete_rate_values($_REQUEST['rate_id'], array($_REQUEST['rate_type'] => array($_REQUEST['amount'] => 'Y')), $_REQUEST['shipping_id'], $_REQUEST['destination_id']);

	return array(CONTROLLER_STATUS_REDIRECT, "shippings.update?shipping_id=$_REQUEST[shipping_id]&destination_id=$_REQUEST[destination_id]&selected_section=shipping_charges");
}

function fn_delete_rate_values($rate_id, $delete_rate_data, $shipping_id, $destination_id)
{
	$rate_values = db_get_field("SELECT rate_value FROM ?:shipping_rates WHERE shipping_id = ?i AND destination_id = ?i", $shipping_id, $destination_id);

	if (!empty($rate_values)) {
		$rate_values = unserialize($rate_values);
	}

	foreach ((array)$rate_values as $rate_type => $rd) {
		foreach ((array)$rd as $amount => $data) {
			if (isset($delete_rate_data[$rate_type][$amount]) && $delete_rate_data[$rate_type][$amount] == 'Y') {
				unset($rate_values[$rate_type][$amount]);
			}
		}
	}

	if (is_array($rate_values)) {
		foreach ($rate_values as $k => $v) {
			if ((count($v)==1) && (floatval($v[0]['value'])==0)) {
				unset($rate_values[$k]);
				continue;
			}
		}
	}

	if (fn_is_empty($rate_values)) {
			db_query("DELETE FROM ?:shipping_rates WHERE rate_id = ?i", $rate_id);
	} else {
		db_query("UPDATE ?:shipping_rates SET ?u WHERE shipping_id = ?i AND destination_id = ?i", array('rate_value' => serialize($rate_values)), $shipping_id, $destination_id);
	}
}

function fn_update_shipping($data, $shipping_id, $lang_code = DESCR_SL)
{
	$data['localization'] = empty($data['localization']) ? '' : fn_implode_localizations($data['localization']);
	$data['tax_ids'] = !empty($data['tax_ids']) ? fn_create_set($data['tax_ids']) : '';
	$data['usergroup_ids'] = empty($data['usergroup_ids']) ? '' : implode(',', $data['usergroup_ids']);

	fn_set_hook('update_shipping', $data, $shipping_id, $lang_code);

	if (!empty($shipping_id)) {
		db_query("UPDATE ?:shippings SET ?u WHERE shipping_id = ?i", $data, $shipping_id);
		db_query("UPDATE ?:shipping_descriptions SET ?u WHERE shipping_id = ?i AND lang_code = ?s", $data, $shipping_id, $lang_code);
	} else {
		$shipping_id = $data['shipping_id'] = db_query("INSERT INTO ?:shippings ?e", $data);

		foreach ((array)Registry::get('languages') as $data['lang_code'] => $_v) {
			db_query("INSERT INTO ?:shipping_descriptions ?e", $data);
		}
	}

	if ($shipping_id) {
		fn_attach_image_pairs('shipping', 'shipping', $shipping_id, $lang_code);
	}

	return $shipping_id;
}

function fn_get_shipping_services()
{
	$shipping_settings = fn_get_settings('Shippings');

	$enabled_services = array();
	foreach ($shipping_settings as $setting_name => $val) {
		if (strpos($setting_name, '_enabled') !== false && $val == 'Y') {
			$enabled_services[] = str_replace('_enabled', '', $setting_name);
		}
	}

	$services = empty($enabled_services) ? array() : db_get_array("SELECT ?:shipping_services.*, ?:shipping_service_descriptions.description FROM ?:shipping_services LEFT JOIN ?:shipping_service_descriptions ON ?:shipping_service_descriptions.service_id = ?:shipping_services.service_id AND ?:shipping_service_descriptions.lang_code = ?s WHERE ?:shipping_services.module IN (?a) ORDER BY ?:shipping_service_descriptions.description", DESCR_SL, $enabled_services);

	return $services;
}

function fn_get_all_shippings()
{
	$condition = '';
	if (defined('COMPANY_ID')) {
		$condition = fn_get_company_condition('a.company_id');
		$companies = Registry::get('s_companies');
		if (!empty($companies[COMPANY_ID]['shippings'])) {
			$condition .= db_quote(' OR a.shipping_id IN (?n)', explode(',', $companies[COMPANY_ID]['shippings']));
		}
	}

	$res = db_get_hash_array("SELECT a.shipping_id, a.company_id, a.min_weight, a.max_weight, a.position, a.status, b.shipping, b.delivery_time, a.usergroup_ids FROM ?:shippings as a LEFT JOIN ?:shipping_descriptions as b ON a.shipping_id = b.shipping_id AND b.lang_code = ?s WHERE 1 $condition ORDER BY a.position", 'shipping_id', DESCR_SL);
	return $res;
}

?>