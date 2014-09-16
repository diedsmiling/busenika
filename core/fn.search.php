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
// $Id: fn.search.php 10590 2010-09-03 08:37:58Z alexions $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// Add default search objects
//
function fn_init_search()
{
	fn_search_init_object();
	fn_search_register_object(
		'products', 
		'fn_create_products_condition', 
		array(
			'type' => 'extended',
			'pshort' => 'Y',
			'pfull' => 'Y',
			'pname' => 'Y',
			'pkeywords' => 'Y',
		),
		fn_get_lang_var('products'), 
		'fn_gather_additional_product_data_for_search'
	);

	fn_search_register_object(
		'pages', 
		'fn_create_pages_condition', 
		array(
			'pdescr' => 'Y',
			'pname' => 'Y',
		),
		fn_get_lang_var('pages'), 
		''
	);

	fn_set_hook('search_init');
}

//
// Init search data
//
function fn_search_init_object()
{
	$search_object = array (
		'conditions' => array(
			'functions' => array (),
			'values' => array ()
		),
		'more_data' => array(),
		'titles' => array(),
		'default' => '',
		'default_params' => array(),
	);

	Registry::set('search_object', $search_object);
}

function fn_search_get_objects()
{
	$data = array ();

	$search = Registry::get('search_object');

	if (!empty($search['conditions']['functions'])) {
		foreach ($search['conditions']['functions'] as $object => $entry) {
			if ($search['default'] == $object) {
				continue;
			}

			$data[$object] = $search['titles'][$object];
		}
	}

	return $data;
}

function fn_search_get_customer_objects()
{
	$data = array ();

	$search = Registry::get('search_object');

	$objects = Registry::get('settings.General.search_objects');

	foreach ($search['conditions']['functions'] as $object => $entry) {
		if ($search['default'] == $object) {
			continue;
		}

		if (!empty($objects[$object]) && $objects[$object] == 'Y') {
			$data[$object] = $search['titles'][$object];
		}
	}

	return $data;
}

//
// Add new search object
//
function fn_search_register_object($object, $condition_function, $default_params = array(), $title = '', $more_data_function = '')
{
	if (empty($title)) {
		$title = $object;
	}

	$search = &Registry::get('search_object');

	$search['conditions']['functions'][$object] = $condition_function;

	$search['titles'][$object] = $title;

	$search['more_data'][$object] = $more_data_function;

	$search['default_params'][$object] = $default_params;

	if (!$search['default']) {
		$search['default'] = $object;
	}
}

//
// Search
//
function fn_search($params, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
	$data = array ();

	$search = Registry::get('search_object');

	$pieces = array ();
	$search_type = '';

	if (empty($params['objects'])) {
		$params['objects'] = array ();
	}

	if (empty($params['page'])) {
		$params['page'] = 1;
	}

	foreach ($search['conditions']['functions'] as $object => $function) {
		if ($search['default'] == $object) {
			continue;
		}

		if (!in_array($object, $params['objects'])) {
			unset($search['conditions']['functions'][$object]);
		}
	}

	if (empty($params['q'])) {
		$params['q'] = '';
	}

	if (empty($params['match'])) {
		$params['match'] = 'any';
	}

	$params['search_string'] = $params['q'];

	foreach ($search['conditions']['functions'] as $object => $function) {
		if (!empty($function) && function_exists($function)) {
			$_params = $params;
			if (!empty($search['default_params'][$object])) {
				$_params = fn_array_merge($_params, $search['default_params'][$object]);
			}
			$search['conditions']['values'][$object] = $function($_params, $lang_code);
		}
	}

	fn_set_hook('search_by_objects', $search['conditions']['values']);

	if (count($search['conditions']['values']) == 1) {
		list ($object) = each($search['conditions']['values']);

		return fn_search_simple($params, $search, $object, $items_per_page, $lang_code);

	} elseif (count($search['conditions']['values'])) {
		db_query("CREATE TEMPORARY TABLE _search (id int NOT NULL, object varchar(30) NOT NULL, sort_field varchar(255) NOT NULL)ENGINE=HEAP;");

		foreach ($search['conditions']['values'] as $object => $entry) {
			$entry['table'] = !empty($entry['table']) ? $entry['table'] : "?:" . $object;

			$select = db_quote("SELECT $entry[table].$entry[key], '$object', $entry[sort] FROM ?:$object as $entry[table] $entry[join] WHERE $entry[condition] GROUP BY $entry[table].$entry[key]");
			if (AREA == 'A' && Registry::is_exist('revisions')) {
				fn_revisions_process_select($select);
			}

			db_query("INSERT INTO _search (id, object, sort_field) ?p", $select);
		}

		if ($items_per_page) {
			$total = db_get_field('SELECT COUNT(id) FROM _search');
			$limit = fn_paginate($params['page'], $total, $items_per_page);
			if (preg_match("/\s+(\d+),/", $limit, $begin)) {
				$begin = intval($begin[1]);
			} else {
				$begin = 0;
			}
		} else {
			$limit = '';
			$total = 0;
			$begin = 0;
		}

		$results = db_get_array('SELECT id, object FROM _search ORDER BY sort_field ' . $limit, 'id');

		if ($results) {
			$ids = array ();
			foreach ($results as $id => $entry) {
				$ids[$entry['object']][] = $entry['id'];
			}

			$_data = array ();

			foreach ($search['conditions']['values'] as $object => $entry) {
				if (empty($ids[$object]) || !count($ids[$object])) {
					continue;
				}

				$entry['table'] = !empty($entry['table']) ? $entry['table'] : "?:" . $object;

				$_data[$object] = db_get_hash_array("SELECT " . implode(', ', $entry['fields']) . " FROM ?:$object as $entry[table] $entry[join] WHERE $entry[condition] AND $entry[table].$entry[key] IN ('" . join("', '", $ids[$object]) . "') GROUP BY $entry[table].$entry[key]", $entry['key']);
			}

			$num = 0;

			foreach ($results as $key => $entry) {
				$data[$num] = $_data[$entry['object']][$entry['id']];

				$data[$num]['object'] = $entry['object'];

				if (!empty($search['more_data'][$entry['object']])) {
					$search['more_data'][$entry['object']]($data[$num]);
				}

				$data[$num]['result_number'] = $begin + $num + 1;

				if (count($_data) == 1) {
					$data[$num]['result_type'] = 'full';
				} else {
					$data[$num]['result_type'] = 'short';
				}

				$num++;
			}

			$data[0]['first'] = true;

			unset($_data);

			if (!$total) {
				$total = count($data);
			}
		}
	}

	return array($data, $params, $total);
}

function fn_search_simple($params, $search, $object, $items_per_page)
{
	$entry = $search['conditions']['values'][$object];
	$entry['table'] = !empty($entry['table']) ? $entry['table'] : "?:" . $object;

	$total = 0;

	if (empty($params['page'])) {
		$params['page'] = 1;
	}

	if ($items_per_page) {
		$total = db_get_field("SELECT COUNT(DISTINCT($entry[table].$entry[key])) FROM ?:$object as $entry[table] $entry[join] WHERE $entry[condition]");
		$limit = fn_paginate($params['page'], $total, $items_per_page);
		if (preg_match("/\s+(\d+),/", $limit, $begin)) {
			$begin = intval($begin[1]);
		} else {
			$begin = 0;
		}
	} else {
		$limit = '';
		$total = 0;
		$begin = 0;
	}

	$data = db_get_hash_array("SELECT " . implode(', ', $entry['fields']) . " FROM ?:$object as $entry[table] $entry[join] WHERE $entry[condition] GROUP BY $entry[table].$entry[key] ORDER BY $entry[sort] " . $limit, $entry['key']);

	$num = 0;

	foreach ($data as $key => $entry) {
		$data[$key]['id'] = $key;

		$data[$key]['object'] = $object;

		if (!empty($search['more_data'][$object])) {
			$search['more_data'][$object]($data[$key]);
		}

		if ($num == 0) {
			$data[$key]['first'] = true;
		}

		$data[$key]['result_number'] = $begin + $num + 1;

		$data[$key]['result_type'] = 'full';

		$num++;
	}

	if (!$total) {
		$total = count($data);
	}

	return array($data, $params, $total);
}

//
// Pages condition function
//
function fn_create_pages_condition($params, $lang_code = CART_LANGUAGE)
{
	$params['get_conditions'] = true;
	if (AREA != 'A') {
		$params['status'] = 'A';
	}

	list($fields, $join, $condition) = fn_get_pages($params, $lang_code);

	$data = array (
		'fields' => $fields,
		'join' => $join,
		'condition' => $condition,
		'table' => '?:pages',
		'key' => 'page_id',
		'sort' => '?:page_descriptions.page',
		'sort_table' => 'page_descriptions'
	);

	return $data;
}

//
// Products condition function
//
function fn_create_products_condition($params, $lang_code = CART_LANGUAGE)
{
	$params['get_conditions'] = true;

	list($fields, $join, $condition) = fn_get_products($params, 0, $lang_code);

	return array (
		'fields' => $fields,
		'join' => $join,
		'condition' => '1 ' . $condition,
		'table' => 'products',
		'key' => 'product_id',
		'sort' => 'descr1.product',
		'sort_table' => 'product_descriptions'
	);
}

function fn_gather_additional_product_data_for_search(&$product)
{
	fn_gather_additional_product_data($product, true, true);
}
?>