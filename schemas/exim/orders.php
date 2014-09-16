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
// $Id: orders.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array (
	'section' => 'orders',
	'pattern_id' => 'orders',
	'name' => fn_get_lang_var('orders'),
	'key' => array('order_id'),
	'table' => 'orders',
	'range_options' => array (
		'selector_url' => 'orders.manage',
		'object_name' => fn_get_lang_var('orders'),
	),
	'options' => array (
		'lang_code' => array (
			'title' => 'language',
			'type' => 'languages',
		),
	),
	'export_fields' => array (
		'Order ID' => array (
			'db_field' => 'order_id',
			'alt_key' => true,
			'required' => true,
		),
		'E-mail' => array (
			'db_field' => 'email',
			'alt_key' => true,
			'required' => true,
		),
		'User ID' => array (
			'db_field' => 'user_id'
		),
		'Total' => array (
			'db_field' => 'total'
		),
		'Subtotal' => array (
			'db_field' => 'subtotal'
		),
		'Discount' => array (
			'db_field' => 'discount'
		),
		'Payment surcharge' => array (
			'db_field' => 'payment_surcharge'
		),
		'Shipping cost' => array (
			'db_field' => 'shipping_cost'
		),
		'Date' => array (
			'db_field' => 'timestamp',
			'process_get' => array ('fn_timestamp_to_date', '#this'),
			'convert_put' => array ('fn_date_to_timestamp'),
		),
		'Status' => array (
			'db_field' => 'status',
		),
		'Notes' => array (
			'db_field' => 'notes',
		),
		'Payment ID' => array (
			'db_field' => 'payment_id',
		),
		'IP address' => array (
			'db_field' => 'ip_address',
		),
		'Details' => array (
			'db_field' => 'details',
		),
		'Payment information' => array (
			'linked' => false,
			'process_get' => array('fn_exim_orders_get_data', '#key', 'P'),
			'process_put' => array('fn_exim_orders_set_data', '#key', '#this', 'P')
		),
		'Taxes' => array (
			'linked' => false,
			'process_get' => array('fn_exim_orders_get_data', '#key', 'T'),
			'process_put' => array('fn_exim_orders_set_data', '#key', '#this', 'T')
		),
		'Coupons' => array (
			'linked' => false,
			'process_get' => array('fn_exim_orders_get_data', '#key', 'C'),
			'process_put' => array('fn_exim_orders_set_data', '#key', '#this', 'C')
		),
		'Shipping' => array (
			'linked' => false,
			'process_get' => array('fn_exim_orders_get_data', '#key', 'L'),
			'process_put' => array('fn_exim_orders_set_data', '#key', '#this', 'L')
		),
		'Invoice ID' => array (
			'linked' => false,
			'process_get' => array('fn_exim_orders_get_docs', '#key', 'I'),
			'process_put' => array('fn_exim_orders_set_docs', '#key', '#this', 'I')
		),
		'Credit memo ID' => array (
			'linked' => false,
			'process_get' => array('fn_exim_orders_get_docs', '#key', 'C'),
			'process_put' => array('fn_exim_orders_set_docs', '#key', '#this', 'C')
		),
		'Title' => array (
			'db_field' => 'title'
		),
		'First name' => array (
			'db_field' => 'firstname'
		),
		'Last name' => array (
			'db_field' => 'lastname'
		),
		'Company' => array (
			'db_field' => 'company'
		),
		'Fax' => array (
			'db_field' => 'fax'
		),
		'Phone' => array (
			'db_field' => 'phone'
		),
		'Web site' => array (
			'db_field' => 'url'
		),
		'Tax exempt' => array (
			'db_field' => 'tax_exempt'
		),
		'Language' => array (
			'db_field' => 'lang_code'
		),
		'Billing: title' => array (
			'db_field' => 'b_title',
		),
		'Billing: first name' => array (
			'db_field' => 'b_firstname',
		),
		'Billing: last name' => array (
			'db_field' => 'b_lastname',
		),
		'Billing: address' => array (
			'db_field' => 'b_address',
		),
		'Billing: address (line 2)' => array (
			'db_field' => 'b_address_2',
		),
		'Billing: city' => array (
			'db_field' => 'b_city',
		),
		'Billing: state' => array (
			'db_field' => 'b_state',
		),
		'Billing: country' => array (
			'db_field' => 'b_country',
		),
		'Billing: zipcode' => array (
			'db_field' => 'b_zipcode',
		),
		'Shipping: title' => array (
			'db_field' => 's_title',
		),
		'Shipping: first name' => array (
			'db_field' => 's_firstname',
		),
		'Shipping: last name' => array (
			'db_field' => 's_lastname',
		),
		'Shipping: address' => array (
			'db_field' => 's_address',
		),
		'Shipping: address (line 2)' => array (
			'db_field' => 's_address_2',
		),
		'Shipping: city' => array (
			'db_field' => 's_city',
		),
		'Shipping: state' => array (
			'db_field' => 's_state',
		),
		'Shipping: country' => array (
			'db_field' => 's_country',
		),
		'Shipping: zipcode' => array (
			'db_field' => 's_zipcode',
		),
		'Extra fields' => array (
			'linked' => false,
			'process_get' => array('fn_exim_orders_get_extra_fields', '#key', '@lang_code'),
			'process_put' => array('fn_exim_orders_set_extra_fields', '#this', '#key', '@lang_code')
		)
	),
);

// ------------- Utility functions ---------------

//
// Get order data information
// Parameters:
// @order_id - order ID
// @type - type of information

function fn_exim_orders_get_data($order_id, $type)
{

	$data = db_get_field("SELECT data FROM ?:order_data WHERE order_id = ?i AND type = ?s", $order_id, $type);
	if (!empty($data)) {

		// Payment information
		if ($type == 'P') {
			$data = @unserialize(fn_decrypt_text($data));
		// Coupons, Taxes and Shipping information
		} elseif (strpos('CTL', $type) !== false) {
			$data = @unserialize($data);
		}

		return YAML_Parser::serialize($data);
	}
}

//
// Get order data information
// Parameters:
// @order_id - order ID
// @data - data to set
// @type - type of information

function fn_exim_orders_set_data($order_id, $data, $type)
{

	$set_delimiter = ';';
	$pair_delimiter = ':';
	$left = '[';
	$right = ']';


	$data = YAML_Parser::unserialize($data);
	
	if (is_array($data)) {
		$data = serialize($data);
		if ($type == 'P') {
			$data = fn_encrypt_text($data);
		}
		$insert = array (
			'order_id' => $order_id,
			'type' => $type,
			'data' => $data
		);

		db_query("REPLACE INTO ?:order_data ?e", $insert);
	}

	return true;
}

function fn_exim_orders_get_extra_fields($order_id, $lang_code = CART_LANGUAGE)
{

	$fields = array();
	$_user = db_get_array("SELECT d.description, f.value, a.section FROM ?:profile_fields_data as f LEFT JOIN ?:profile_field_descriptions as d ON d.object_id = f.field_id AND d.object_type = 'F' AND d.lang_code = ?s LEFT JOIN ?:profile_fields as a ON a.field_id = f.field_id WHERE f.object_id = ?i  AND f.object_type = 'O'", $lang_code, $order_id);

	if (!empty($_user)) {
		foreach ($_user as $field) {
			if ($field['section'] == 'B') {
				$type = 'billing';
			} elseif ($field['section'] == 'S') {
				$type = 'shipping';
			} else {
				$type = 'user';
			}

			$fields[$type][$field['description']] = $field['value'];
		}
	}

	if (!empty($fields)) {
		return YAML_Parser::serialize($fields);
	}

	return '';
}

function fn_exim_orders_set_extra_fields($data, $order_id, $lang_code = CART_LANGUAGE)
{
	$data = YAML_Parser::unserialize($data);

	if (!empty($data) && is_array($data)) {
		foreach ($data as $type => $_data) {
			foreach ($_data as $field => $value) {
				// Check if field is exist
				if ($type == 'billing' || $type == 'shipping') {
					$section = strtoupper($type[0]);
					$field_id = db_get_field("SELECT object_id FROM ?:profile_field_descriptions LEFT JOIN ?:profile_fields ON ?:profile_fields.field_id = ?:profile_field_descriptions.object_id WHERE description = ?s AND lang_code = ?s AND ?:profile_fields.section = ?s AND object_type = 'F' LIMIT 1", $field, $lang_code, $section);
				} else {
					$field_id = db_get_field("SELECT object_id FROM ?:profile_field_descriptions WHERE description = ?s AND object_type = 'F' AND lang_code = ?s LIMIT 1", $field, $lang_code);
				}

				if (!empty($field_id)) {
					$update = array (
						'object_id' => $order_id,
						'object_type' => 'O',
						'field_id' => $field_id,
						'value' => $value
					);

					db_query('REPLACE INTO ?:profile_fields_data ?e', $update);
				}
			}
		}

		return true;
	}

	return false;
}

function fn_exim_orders_get_docs($order_id, $type)
{

	$data = db_get_field("SELECT doc_id FROM ?:order_docs WHERE order_id = ?i AND type = ?s", $order_id, $type);
	if (!empty($data)) {
		return $data;
	}
}

function fn_exim_orders_set_docs($order_id, $data, $type)
{
	if (!empty($data)) {
		$insert = array (
			'order_id' => $order_id,
			'type' => $type,
			'doc_id' => $data
		);

		db_query("REPLACE INTO ?:order_docs ?e", $insert);
	}

	return true;
}
?>