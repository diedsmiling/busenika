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
// $Id: order_items.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array (
	'section' => 'orders',
	'pattern_id' => 'order_items',
	'name' => fn_get_lang_var('order_items'),
	'key' => array('item_id', 'order_id'),
	'table' => 'order_details',
	'range_options' => array (
		'selector_url' => 'orders.manage',
		'object_name' => fn_get_lang_var('orders'),
	),
	'export_fields' => array (
		'Order ID' => array (
			'db_field' => 'order_id',
			'alt_key' => true,
			'required' => true,
		),
		'Item ID' => array (
			'db_field' => 'item_id',
			'alt_key' => true,
			'required' => true,
		),
		'Product ID' => array (
			'db_field' => 'product_id'
		),
		'Product code' => array (
			'db_field' => 'product_code'
		),
		'Price' => array (
			'db_field' => 'price'
		),
		'Quantity' => array (
			'db_field' => 'amount'
		),
		'Extra' => array (
			'linked' => true,
			'process_get' => array('fn_exim_orders_get_extra', '#this'),
			'process_put' => array('fn_exim_orders_set_extra', '#key', '#this')
		),
	),
);

// ------------- Utility functions ---------------

//
// Get item extra information
// Parameters:
// @data - extra data

function fn_exim_orders_get_extra($data)
{
	if (!empty($data)) {
		$data = @unserialize($data);

		return YAML_Parser::serialize($data);
	}

	return '';
}

//
// Set extra information
// Parameters:
// @ids - item ids (order_id/item_id)
// @data - data to set
function fn_exim_orders_set_extra($ids, $data)
{

	$data = YAML_Parser::unserialize($data);

	if (is_array($data)) {
		$data = serialize($data);
		$insert = array (
			'extra' => $data,
		);

		db_query("UPDATE ?:order_details SET ?u WHERE order_id = ?i AND item_id = ?i", $insert, $ids['order_id'], $ids['item_id']);
	}

	return true;
}
?>