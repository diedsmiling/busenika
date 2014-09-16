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
// $Id: func.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }


//
// Delete event
//
function fn_event_delete($event_id, $user_id = 0)
{
	if (!empty($user_id)) {
		$event_id = db_get_field("SELECT event_id FROM ?:giftreg_events WHERE event_id = ?i AND user_id = ?i", $event_id, $user_id);
		if (empty($event_id)) {
			return false;
		}
	}

	db_query("DELETE FROM ?:giftreg_events WHERE event_id = ?i", $event_id);
	db_query("DELETE FROM ?:giftreg_event_fields WHERE event_id = ?i", $event_id);
	db_query("DELETE FROM ?:giftreg_event_products WHERE event_id = ?i", $event_id);
	db_query("DELETE FROM ?:giftreg_event_subscribers WHERE event_id = ?i", $event_id);
	db_query("DELETE FROM ?:ekeys WHERE object_id = ?i AND object_type IN ('O', 'G')", $event_id);

	fn_set_hook('delete_event', $event_id);

	return true;
}

//
// Generate access key for private events and owner
//
function fn_event_generate_ekey($event_id, $owner = false)
{

	$ekey = md5(uniqid(rand()));

	$data = array(
		'object_id' => $event_id,
		'object_type' => ($owner == true) ? 'O' : 'G',
		'ekey' => $ekey,
		'ttl' => 0
	);
	db_query("INSERT INTO ?:ekeys ?e", $data);

	return $ekey;
}

//
// Delete expired events
//
function fn_event_update_status()
{

	if (TIME > Registry::get('settings.gift_registry_next_check')) {
		$time = TIME;
		db_query("UPDATE ?:giftreg_events SET status = IF(start_date > ?i, 'A', IF(end_date < ?i, 'F', 'P'))", $time, $time);

		$time += GIFTREG_STATUS_CHECK_PERIOD;
		db_query("UPDATE ?:settings SET value = ?i WHERE option_name = 'gift_registry_next_check'", $time);
	}
}

//
// Add fields to gift registry
//
function fn_giftreg_add_fields($fields)
{
	if (empty($fields)) {
		return false;
	}

	foreach ($fields as $v) {

		if (empty($v['description'])) {
			continue;
		}

		// Insert main data
		$_data = fn_check_table_fields($v, 'giftreg_fields');
		$field_id = db_query("INSERT INTO ?:giftreg_fields ?e", $_data);
		// Insert descriptions
		$_data = array(
			'object_id' => $field_id,
			'object_type' => 'F',
			'description' => $v['description'],
		);

		foreach (Registry::get('languages') as $_data['lang_code'] => $_v) {
			db_query("INSERT INTO ?:giftreg_descriptions ?e", $_data);
		}

		if (substr_count('SR', $v['field_type']) && is_array($v['variants'])) {
			fn_giftreg_add_field_variants($v['variants'], $field_id);
		}
	}
}

//
// Add variants for gift registry field
//
function fn_giftreg_add_field_variants($variants = array(), $field_id = 0)
{
	if (empty($variants) || empty($field_id)) {
		return false;
	}

	foreach ($variants as $_v) {

		if (empty($_v['description'])) {
			continue;
		}
		// Insert main data
		$_data = fn_check_table_fields($_v, 'giftreg_field_variants');
		$_data['field_id'] = $field_id;
		$variant_id = db_query("INSERT INTO ?:giftreg_field_variants ?e", $_data);

		// Insert descriptions
		$_data = array(
			'object_id' => $variant_id,
			'object_type' => 'V',
			'description' => $_v['description'],
		);

		foreach ((array)Registry::get('languages') as $_data['lang_code'] => $_v) {
			db_query("INSERT INTO ?:giftreg_descriptions ?e", $_data);
		}
	}

	return true;
}

//
// Delete variants of gift registry field
//
function fn_giftreg_delete_field_variants($field_id)
{

	$vars = db_get_fields("SELECT variant_id FROM ?:giftreg_field_variants WHERE field_id = ?i", $field_id);
	if (!empty($vars)) {
		foreach ($vars as $variant_id) {
			db_query("DELETE FROM ?:giftreg_descriptions WHERE object_id = ?i AND object_type = 'V'", $variant_ids);
		}
		db_query("DELETE FROM ?:giftreg_field_variants WHERE field_id = ?i", $field_id);
	}
}

function fn_update_event_subscribers($event_data, $event_id)
{

	$subscribers = array();
	if (!empty($event_data['subscribers'])) {
		$subscribers = $event_data['subscribers'];
	}
	if (!empty($event_data['add_subscribers'])) {
		$subscribers = fn_array_merge($subscribers, $event_data['add_subscribers'], false);
	}

	if (!empty($subscribers)) {
		$invalid_emails = array();
		db_query("DELETE FROM ?:giftreg_event_subscribers WHERE event_id = ?i", $event_id);
		foreach ($subscribers as $v) {
			if (empty($v['email']) || empty($v['name'])) {
				continue;
			}

			if (fn_validate_email($v['email']) == false) {
				$invalid_emails[] = $v['email'];
			}

			$v['event_id'] = $event_id;
			db_query("REPLACE INTO ?:giftreg_event_subscribers ?e", $v);
		}

		if (!empty($invalid_emails)) {
			$msg = fn_get_lang_var('error_invalid_emails');
			$msg = str_replace('[emails]', implode(", ", $invalid_emails), $msg);
			fn_set_notification('W', fn_get_lang_var('warning'), $msg);
		}
	}

	return true;
}

function fn_gift_registry_get_discussion_object_data(&$data, $object_id, $object_type)
{
	if ($object_type == 'G') { // gift registry
		$data['description'] = db_get_field("SELECT title FROM ?:giftreg_events WHERE event_id = ?i", $object_id);
		if (AREA == 'A') {
			$data['url'] = "events.update?event_id=$object_id&selected_section=discussion";
		} else {
			$data['url'] = "events?event_id=$object_id";
		}
	}
}

function fn_gift_registry_get_discussion_objects($objects)
{
	$objects['G'] = 'giftreg';
}

function fn_gift_registry_is_accessible_discussion($data, &$auth, &$access)
{

	if ($data['object_type'] == 'G') {// giftreg
		$_data = db_get_row("SELECT user_id, type FROM ?:giftreg_events WHERE event_id = ?i AND type != 'D'", $data['object_id']);

		// If event is private, ask for access code
		if (empty($_data['type']) || (!empty($_data['user_id']) && $auth['user_id'] != $_data['user_id'])) {
			$access = false;
		}

		if ($_data['type'] == 'U') {
			if (empty($_data['user_id'])) { // if this is anonymous event, ask for access key
				$access = false;
			} elseif ((!empty($_data['user_id']) && $auth['user_id'] == $_data['user_id'])) {
				$access = true;
			} else {
				$access = false;
			}
		} elseif (!empty($_data['user_id']) && $auth['user_id'] == $_data['user_id']) {
			$access = true;
		}
	}
}

function fn_gift_registry_change_order_status($status_to, $status_from, &$order_info)
{
	$order_statuses = fn_get_statuses(STATUSES_ORDER, false, true);

	if ($order_statuses[$status_to]['inventory'] == 'D' && $order_statuses[$status_from]['inventory'] == 'I') { // decrease amount
		$sign = '+';
	}
	elseif ($order_statuses[$status_to]['inventory'] == 'I' && $order_statuses[$status_from]['inventory'] == 'D') { // increase amount
		$sign = '-';
	}

	if (!empty($sign)) {
		foreach ($order_info['items'] as $v) {
			if (is_array($v['extra']) && !empty($v['extra']['events'])) {
				foreach ($v['extra']['events'] as $item_id => $amount) {
					db_query("UPDATE ?:giftreg_event_products SET ordered_amount = ordered_amount $sign ?i WHERE item_id = ?i", $amount, $item_id);
				}
			}
		}
	}
}

function fn_gift_registry_place_order($order_id)
{
	$order_info = fn_get_order_info($order_id);
	$status_from = 'B';
	fn_gift_registry_change_order_status($order_info['status'], $status_from, $order_info);
}

function fn_get_event_name($event_id)
{
	if (!empty($event_id)) {
		return db_get_field("SELECT title FROM ?:giftreg_events WHERE event_id = ?i", $event_id);
	}

	return false;
}

function fn_update_event($event_data, $event_id = 0)
{
	$event_data['start_date'] = fn_parse_date($event_data['start_date']);
	$event_data['end_date'] = fn_parse_date($event_data['end_date'], true);

	if ($event_data['start_date'] > TIME) {
		$event_data['status'] = 'A';
	} elseif ($event_data['end_date'] < TIME) {
		$event_data['status'] = 'F';
	} else {
		$event_data['status'] = 'P';
	}

	$_data = $event_data;
	$_data['user_id'] = $_SESSION['auth']['user_id'];

	if (empty($event_id)) {
		$event_id = db_query("INSERT INTO ?:giftreg_events ?e", $_data);
	} else {
		db_query("UPDATE ?:giftreg_events SET ?u WHERE event_id = ?i", $_data, $event_id);
	}

	fn_update_event_subscribers($event_data, $event_id);

	// Generate access key for editing this event
	if (AREA == 'C') {
		$access_key = fn_event_generate_ekey($event_id, true);
	}

	// Generate access key for event subscribers (for private event)
	if ($_data['type'] == 'U') {
		fn_event_generate_ekey($event_id);
	}

	if (!empty($event_data['fields'])) {
		$_data = array (
			'event_id' => $event_id,
		);

		db_query("DELETE FROM ?:giftreg_event_fields WHERE event_id = ?i", $event_id);

		foreach ($event_data['fields'] as $field_id => $value) {
			if (substr_count($value, '/') == 2) { // FIXME: it's date field
				$value = fn_parse_date($value);
			}
			$_data['field_id'] = $field_id;
			$_data['value'] = $value;
			db_query("INSERT INTO ?:giftreg_event_fields ?e", $_data);
		}
	}

	fn_set_hook('update_event', $event_data, $event_id);

	return array($event_id, empty($access_key) ? '' : $access_key);
}

function fn_delete_events_variant($variant_id)
{
	db_query("DELETE FROM ?:giftreg_field_variants WHERE variant_id = ?i", $variant_id);
	db_query("DELETE FROM ?:giftreg_descriptions WHERE object_id = ?i AND object_type = 'V'", $variant_id);
}

function fn_delete_events_field($field_id)
{
	fn_giftreg_delete_field_variants($field_id);
	db_query("DELETE FROM ?:giftreg_fields WHERE field_id = ?i", $field_id);
	db_query("DELETE FROM ?:giftreg_descriptions WHERE object_id = ?i AND object_type = 'F'", $field_id);
}

function fn_get_event_product($event_id, $items_per_page, $page, $lang_code = CART_LANGUAGE)
{
	$total = db_get_field("SELECT DISTINCT(COUNT(*)) FROM ?:giftreg_event_products LEFT JOIN ?:product_descriptions ON ?:product_descriptions.product_id = ?:giftreg_event_products.product_id AND ?:product_descriptions.lang_code = ?s WHERE event_id = ?i", $lang_code, $event_id);
	$limit = fn_paginate($page, $total, $items_per_page);

	return db_get_hash_array("SELECT * FROM ?:giftreg_event_products LEFT JOIN ?:product_descriptions ON ?:product_descriptions.product_id = ?:giftreg_event_products.product_id AND ?:product_descriptions.lang_code = ?s WHERE event_id = ?i $limit", 'item_id', $lang_code, $event_id);
}
?>