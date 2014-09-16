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
// $Id: fn.locations.php 10184 2010-07-23 11:11:24Z klerik $
//

if (!defined('AREA')) { die('Access denied'); }

//
// Get countries list
//
function fn_get_countries($lang_code = CART_LANGUAGE, $only_avail = false, $paginate = false)
{
	$only_avail = ($only_avail == true) ? " WHERE a.status = 'A'" : '';
	$limit = '';

	$page = empty($_REQUEST['page']) ? 1 : $_REQUEST['page'];

	if ($paginate == true) {
		$country_count = db_get_field("SELECT count(*) FROM ?:countries");
		$limit = fn_paginate($page, $country_count, Registry::get('settings.Appearance.admin_elements_per_page'));
	}

	return db_get_array("SELECT a.code, a.code_A3, a.code_N3, a.status, a.region, b.country FROM ?:countries as a LEFT JOIN ?:country_descriptions as b ON b.code = a.code AND b.lang_code = ?s $only_avail ORDER BY b.country $limit", $lang_code);
}

//
// Get countries simple list
//
function fn_get_simple_countries($avail_only = false, $lang_code = CART_LANGUAGE)
{
	$avail_cond = ($avail_only == true)  ? "WHERE a.status = 'A'" : '';

	return db_get_hash_single_array("SELECT a.code, b.country FROM ?:countries as a LEFT JOIN ?:country_descriptions as b ON b.code = a.code AND b.lang_code = ?s $avail_cond ORDER BY b.country", array('code', 'country'), $lang_code);
}

//
// Get states list for specific country
//
function fn_get_states($country = '', $lang_code = CART_LANGUAGE, $paginate = false)
{
	if (empty($country)) {
		return '';
	}

	$limit = '';
	if ($paginate == true) {
		$state_count = db_get_field("SELECT COUNT(*) FROM ?:states WHERE country_code = ?s", $country);
		$limit = fn_paginate(@$_REQUEST['page'], $state_count, Registry::get('settings.Appearance.admin_elements_per_page'));
	}

	return db_get_array("SELECT a.state_id, a.country_code, a.code, a.status, b.state FROM ?:states as a LEFT JOIN ?:state_descriptions as b ON b.state_id = a.state_id AND b.lang_code = ?s WHERE a.country_code = ?s ORDER BY b.state $limit", $lang_code, $country);
}

//
// Get states key-value list
//
function fn_get_simple_states($country = '', $lang_code = CART_LANGUAGE, $paginate = false)
{
	if (empty($country)) {
		$country = 'US'; //!!! FIXME: what is the logic should be?
	}

	return db_get_hash_single_array("SELECT a.code, b.state FROM ?:states as a LEFT JOIN ?:state_descriptions as b ON b.state_id = a.state_id AND b.lang_code = ?s WHERE a.country_code = ?s ORDER BY b.state", array('code', 'state'), $lang_code, $country);
}

//
// Get states list for specific country
//
function fn_get_all_states($lang_code = CART_LANGUAGE, $extended = false, $avail_only = true)
{
	$avail_cond = ($avail_only == true) ? " WHERE a.status = 'A' " : '';

	if ($extended == true) {
		return db_get_array("SELECT a.state_id, a.code, b.state, c.country FROM ?:states as a LEFT JOIN ?:state_descriptions as b ON b.state_id = a.state_id AND b.lang_code = ?s LEFT JOIN ?:country_descriptions as c ON c.code = a.country_code AND c.lang_code = ?s $avail_cond ORDER BY a.country_code, b.state", $lang_code, $lang_code);
	} else {
		return db_get_hash_multi_array("SELECT a.country_code, a.code, b.state FROM ?:states as a LEFT JOIN ?:state_descriptions as b ON b.state_id = a.state_id AND b.lang_code = ?s $avail_cond ORDER BY a.country_code, b.state", array('country_code'), $lang_code);
	}
}

// Get state name (results are cached)
function fn_get_state_name($state_code, $country_code, $lang_code = CART_LANGUAGE)
{
	static $states = array();

	if (empty($states[$country_code][$state_code])) {
		$states[$country_code][$state_code] =db_get_field("SELECT ?:state_descriptions.state FROM ?:states LEFT JOIN ?:state_descriptions ON ?:state_descriptions.state_id = ?:states.state_id AND ?:state_descriptions.lang_code = ?s WHERE ?:states.country_code = ?s AND ?:states.code = ?s", $lang_code, $country_code, $state_code);
	}

	return $states[$country_code][$state_code];
}

// Get country name (results are cached)
function fn_get_country_name($country_code, $lang_code = CART_LANGUAGE)
{
	static $countries = array();
	if (empty($countries[$country_code])) {
		$countries[$country_code] = db_get_field("SELECT country FROM ?:country_descriptions WHERE code = ?s AND lang_code = ?s", $country_code, $lang_code);
	}

	return $countries[$country_code];
}

//
// Get all destinations list
//
function fn_get_destinations($lang_code = CART_LANGUAGE)
{
	$destinations = db_get_hash_array("SELECT a.destination_id, a.status, a.localization, b.destination FROM ?:destinations as a LEFT JOIN ?:destination_descriptions as b ON a.destination_id = b.destination_id AND b.lang_code = ?s ORDER BY destination", 'destination_id', $lang_code);

	$default = $destinations[1];
	unset($destinations[1]);
	array_unshift($destinations, $default);
	return $destinations;
}

//
// Get destination name
//
function fn_get_destination_name($destination_id, $lang_code = CART_LANGUAGE)
{
	if (!empty($destination_id)) {
		return db_get_field("SELECT destination FROM ?:destination_descriptions WHERE destination_id = ?i AND lang_code = ?s", $destination_id, $lang_code);
	}

	return false;
}

//
// Helper for fn_get_available_destination function
//
// @$partial - check for partial equality
//
function fn_check_element($elms, $elm, $partial = false)
{
	if (empty($elm)) {
		return false;
	}
	$suitable = false;
	foreach ($elms as $k => $v) {
		if ($partial == true) {
			$__tmp = str_replace(array('*', '?', '/'), array('.*', '.', '\/'), $v);
			if (preg_match("/^$__tmp\$/iu", $elm)) {
				$suitable = true;
				break;
			}
		} else {
			if ($v == $elm) {
				$suitable = true;
				break;
			}
		}
	}

	return $suitable;
}

//
// Return most coincedence available destination by the following parameters...
//
function fn_get_available_destination($location)
{
	$country = !empty($location['country']) ? $location['country'] : '';
	$state = !empty($location['state']) ? $location['state'] : '';
	$zipcode = !empty($location['zipcode']) ? $location['zipcode'] : '';
	$city = !empty($location['city']) ? $location['city'] : '';
	$address = !empty($location['address']) ? $location['address'] : '';

	if (!empty($country)) {

		$state_id = fn_get_state_id($state, $country);
		
		$condition = '';
		if (AREA == 'C') {
			$condition .= fn_get_localizations_condition('localization');
		}

		$__dests = db_get_array("SELECT a.* FROM ?:destination_elements as a LEFT JOIN ?:destinations as b ON b.destination_id = a.destination_id WHERE b.status = 'A' AND (b.destination_id = 1 OR (1 ?p))", $condition);
		$destinations = array();
		foreach ($__dests as $k => $v) {
			$destinations[$v['destination_id']][$v['element_type']][] = $v['element'];
		}

		$concur_destinations = array();

		foreach ($destinations as $dest_id => $elm_types) {
			// Significance level. The more significance level means the most amount of coincidences
			$significance = 0;
			$dest_countries = !empty($elm_types['C']) ? $elm_types['C'] : array();
			foreach ($elm_types as $elm_type => $elms) {
				// Check country
				if ($elm_type == 'C') {
					$suitable = fn_check_element($elms, $country);
					if ($suitable == false) {
						break;
					}

					$significance += 1 * (1 / count($elms));
				}
				
				// Check state
				if ($elm_type == 'S') {
					// if country is in destanation_countries and it haven't got states, 
					// we suppose that destanation cover all country
					if (!in_array($country, $dest_countries) || fn_get_states($country)) {
						$suitable = fn_check_element($elms, $state_id);
						if ($suitable == false) {
							break;
						}
					} else {
						$suitable = true;	
					}
					$significance += 2 * (1 / count($elms));
				}
				// Check city
				if ($elm_type == 'T') {
					$suitable = fn_check_element($elms, $city, true);
					if ($suitable == false) {
						break;
					}
					$significance += 3 * (1 / count($elms));
				}
				// Check zipcode
				if ($elm_type == 'Z') {
					$suitable = fn_check_element($elms, $zipcode, true);
					if ($suitable == false) {
						break;
					}
					$significance += 4 * (1 / count($elms));
				}

				// Check address
				if ($elm_type == 'A') {
					$suitable = fn_check_element($elms, $address, true);
					if ($suitable == false) {
						break;
					}
					$significance += 5 * (1 / count($elms));
				}
			}

			$significance = number_format($significance, 2, '.', '');

			if ($suitable == true) {
				$concur_destinations[$significance][] = $dest_id;
			}
		}

		if (!empty($concur_destinations)) {
			ksort($concur_destinations, SORT_NUMERIC);
			$concur_destinations = array_pop($concur_destinations);
			return reset($concur_destinations);
		} else {
			return false;
		}
	}

	return false;
}

//
// Return state ID by it's code and country code
//
function fn_get_state_id($state = '', $country = '')
{
	static $state_ids;

	if (empty($state) || empty($country)) {
		return false;
	}

	if (empty($state_ids[$country][$state])) {
		$state_ids[$country][$state] = db_get_field("SELECT state_id FROM ?:states WHERE code = ?s AND country_code = ?s", $state, $country);
	}

	return $state_ids[$country][$state];
}

//
// Return lang_code from default browser language
//
function fn_get_browser_language($languages = array())
{
	if (empty($languages)) {
		return false;
	}

	$browser_language = false;

	if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$preg_string = strtolower(implode('|' , array_keys($languages)));
		if (preg_match("/($preg_string)+(-|;|,)?(.*)?/", $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches)) {
			$browser_language = strtoupper($matches[1]);
		}
	}

	return $browser_language;
}


//
// Localizations
//

function fn_get_localizations($lang_code = CART_LANGUAGE, $status_check = false)
{
	$localizations = array();
	$status_condition = ($status_check) ? " WHERE a.status = 'A'" : '';
	$localizations = db_get_hash_array("SELECT a.localization_id, a.status, b.localization, a.is_default FROM ?:localizations as a LEFT JOIN ?:localization_descriptions as b ON a.localization_id = b.localization_id AND b.lang_code = ?s ?p ORDER BY localization", 'localization_id', $lang_code, $status_condition);
	return $localizations;
}

function fn_get_simple_localizations($lang_code = CART_LANGUAGE, $status_check = false)
{
	$localizations = array();
	$status_condition = ($status_check) ? " WHERE a.status = 'A'" : '';
	$localizations = db_get_hash_single_array("SELECT a.localization_id, b.localization FROM ?:localizations as a LEFT JOIN ?:localization_descriptions as b ON a.localization_id = b.localization_id AND b.lang_code = ?s ?p ORDER BY localization", array('localization_id' , 'localization'), $lang_code, $status_condition);
	return 	$localizations;
}

function fn_get_localization_data($localization_id , $lang_code = CART_LANGUAGE, $additional_data = false)
{
	$loc_data = array();
	$loc_data['data'] = db_get_row("SELECT a.localization_id, a.status, a.custom_weight_settings, a.weight_symbol, a.weight_unit, a.is_default, b.localization FROM ?:localizations as a LEFT JOIN ?:localization_descriptions as b ON a.localization_id = b.localization_id AND b.lang_code = ?s WHERE a.localization_id = ?i ORDER BY localization", $lang_code, $localization_id);

	if (empty($loc_data['data'])) {
		return array();
	}

	if ($additional_data == true) {
		$loc_data['countries'] = db_get_hash_single_array("SELECT a.code, b.country FROM ?:countries as a LEFT JOIN ?:country_descriptions as b ON b.code = a.code AND b.lang_code = ?s LEFT JOIN ?:localization_elements as c ON c.element_type = 'C' AND c.element = a.code WHERE c.localization_id = ?i ORDER BY position" , array('code' , 'country'), $lang_code, $localization_id);
		$loc_data['currencies'] = db_get_hash_single_array("SELECT a.currency_code, a.description FROM ?:currency_descriptions as a LEFT JOIN ?:localization_elements as b ON b.element_type = 'M' AND b.element = a.currency_code WHERE b.localization_id = ?i AND a.lang_code = ?s ORDER BY position" , array('currency_code' , 'description'), $localization_id, $lang_code);
		$loc_data['languages'] = db_get_hash_single_array("SELECT a.lang_code, a.name FROM ?:languages as a LEFT JOIN ?:localization_elements as b ON b.element_type = 'L' AND b.element = a.lang_code WHERE b.localization_id = ?i ORDER BY position" , array('lang_code' , 'name'), $localization_id);
	}
	return $loc_data;
}

function fn_implode_localizations($localizations)
{
	return empty($localizations) ? '' : implode($localizations, ',');
}

function fn_explode_localizations($localizations)
{
	return empty($localizations) ? '' : explode(',' , $localizations);
}

function fn_get_localizations_condition($db_field, $and = true, $localization = '')
{
	$condition = '';
	$localization = !empty($localization) ? $localization : (defined('CART_LOCALIZATION') ? CART_LOCALIZATION : '');
	$condition = (empty($localization) || AREA != 'C') ? '' : (($and == true) ? ' AND' : '') . " (FIND_IN_SET($localization , $db_field))";
	return $condition;
}

function fn_get_country_by_ip($ip)
{
	static $loaded = false;

	if ($loaded == false) {
		include(DIR_LIB . 'geoip/geoip.php');
		$loaded = true;
	}

	$gi = geoip_open(DIR_LIB . 'geoip/geoip.dat', GEOIP_STANDARD);
	$code = geoip_country_code_by_addr($gi, $ip);
	geoip_close($gi);

	return $code;
}
?>