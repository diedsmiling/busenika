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
// $Id: xml_feeds.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$xml = '<?xml version="1.0" encoding="'. CHARSET .'"?>';


// Products management
if ($mode == 'get_products') {

	$_REQUEST['type'] = empty($_REQUEST['type'])? 'extended' : $_REQUEST['type'];

	list($products) = fn_get_products($_REQUEST, Registry::get('settings.Appearance.products_per_page'));

	if (!empty($products)) {
		foreach ($products as $k => $v) {
			fn_gather_additional_product_data($products[$k], true);
		}
	}

	$xml .= fn_array_to_xml($products, 'products');
}

//
// View product details
//
if ($mode == 'get_product') {

	$_REQUEST['product_id'] = empty($_REQUEST['product_id'])? 0 : $_REQUEST['product_id'];

	$product = fn_get_product_data($_REQUEST['product_id'], $auth, CART_LANGUAGE);
	if (!empty($product)) {
		if (!empty($_REQUEST['combination'])) {
			$product['combination'] = $combination;
		}

		fn_gather_additional_product_data($product, true, true);

		$xml .= fn_array_to_xml($product, 'product_data');
	}
}



if ($mode == 'get_categories') {

	$_REQUEST['category_id'] = empty($_REQUEST['category_id'])? 0 : $_REQUEST['category_id'];

	$params = array (
		'category_id' => $_REQUEST['category_id'],
		'visible' => false,
		'plain' => (!empty($_REQUEST['format']) && $_REQUEST['format'] == 'plain') ? true : false
	);
	list($categories, ) = fn_get_categories($params, CART_LANGUAGE);
	$xml .= fn_array_to_xml($categories, 'categories');
}

if ($mode == 'get_category') {

	$_REQUEST['category_id'] = empty($_REQUEST['category_id'])? 0 : $_REQUEST['category_id'];

	$category_data = fn_get_category_data($_REQUEST['category_id'], '*');
	if (!empty($category_data)) {
		$xml .= fn_array_to_xml($category_data, 'category_data');
	}
}

echo $xml;
exit;


function fn_array_to_xml(&$array, $name)
{
	$xml = "<$name>";
	$subname = 'item';

	foreach ($array as $k => $v) {
		if (!is_array($v)) {
			$param = is_int($k) ? $subname : $k;
			$id = is_int($k) ? " id=\"$k\"" : '';
			$xml .= "<$param$id>";
			$xml .= htmlspecialchars($v);
			$xml .= "</$param>";
		} else {
			$param = is_int($k) ? $subname : $k;
			$xml .= fn_array_to_xml($v, $param);
		}
	}

	$xml .= "</$name>\n";

	return $xml;
}

?>