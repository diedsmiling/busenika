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
// $Id: func.php 7502 2009-05-19 14:54:59Z zeke $
//

if (!defined('AREA')) { die('Access denied'); }

function fn_get_store_locations($params, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
	$default_params = array (
		'page' => 1,
		'q' => '',
		'match' => 'any'
	);

	$params = array_merge($default_params, $params);

	$fields = array (
		'?:store_locations.*',
		'?:store_location_descriptions.*',
		'?:country_descriptions.country as country_title'
	);

	$join = db_quote(" LEFT JOIN ?:store_location_descriptions ON ?:store_locations.store_location_id = ?:store_location_descriptions.store_location_id AND ?:store_location_descriptions.lang_code = ?s", $lang_code);

	$join .= db_quote(" LEFT JOIN ?:country_descriptions ON ?:store_locations.country = ?:country_descriptions.code AND ?:country_descriptions.lang_code = ?s", $lang_code);

	$condition = 1;

	if (AREA == 'C') {
		$condition .= " AND status = 'A'";
	}

	// Search string condition for SQL query
	if (!empty($params['q'])) {

		if ($params['match'] == 'any') {
			$pieces = explode(' ', $params['q']);
			$search_type = ' OR ';
		} elseif ($params['match'] == 'all') {
			$pieces = explode(' ', $params['q']);
			$search_type = ' AND ';
		} else {
			$pieces = array($params['q']);
			$search_type = '';
		}

		$_condition = array();
		foreach ($pieces as $piece) {
			$tmp = db_quote("?:store_location_descriptions.name LIKE ?l", "%$piece%"); // check search words

			$tmp .= db_quote(" OR ?:store_location_descriptions.description LIKE ?l", "%$piece%");

			$tmp .= db_quote(" OR ?:store_location_descriptions.city LIKE ?l", "%$piece%");

			$tmp .= db_quote(" OR ?:country_descriptions.country LIKE ?l", "%$piece%");

			$_condition[] = '(' . $tmp . ')';
		}

		$_cond = implode($search_type, $_condition);

		if (!empty($_condition)) {
			$condition .= ' AND (' . $_cond . ') ';
		}

		unset($_condition);
	}

	$condition .= (AREA == 'C' && defined('CART_LOCALIZATION')) ? fn_get_localizations_condition('?:store_locations.localization') : '';

	$sorting = "?:store_locations.position, ?:store_location_descriptions.name";

	$limit = '';
	$total = 0;
	if (!empty($items_per_page)) {
		$total = db_get_field("SELECT COUNT(?:store_locations.store_location_id) FROM ?:store_locations ?p WHERE ?p", $join, $condition);
		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}

	$data = db_get_array('SELECT ?p FROM ?:store_locations ?p WHERE ?p GROUP BY ?:store_locations.store_location_id ORDER BY ?p ?p', implode(', ', $fields), $join, $condition, $sorting, $limit);

	if (!$total) {
		$total = count($data);
	}

	return array($data, $params, $total);

}

function fn_store_locator_google_langs($lang_code)
{
	$supported_langs = array ('en', 'eu', 'ca', 'da', 'nl', 'fi', 'fr', 'gl', 'de', 'el', 'it', 'ja', 'no', 'nn', 'ru' , 'es', 'sv', 'th');

	$lang_code = strtolower($lang_code);

	if (in_array($lang_code, $supported_langs)) {
		return $lang_code;
	}

	return '';
}

?>