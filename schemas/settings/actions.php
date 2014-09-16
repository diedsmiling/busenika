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
// $Id: actions.php 10055 2010-07-14 10:15:19Z klerik $
//
if ( !defined('AREA') ) { die('Access denied'); }

/**
 * Check if secure connection is available
 */
function fn_settings_actions_general_secure_auth(&$new_value, $old_value)
{
	if ($new_value == 'Y') {
		$content = fn_https_request('GET', Registry::get('config.https_location') . '/' . INDEX_SCRIPT . '?check_https=Y');
		if (empty($content[1]) || $content[1] != 'OK') {
			// Disable https
			db_query("UPDATE ?:settings SET value = 'N' WHERE section_id = 'General' AND option_name LIKE 'secure\_%'");
			$new_value = 'N';

			fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('warning_https_disabled'));
		}
	}
}

/**
 * Check if secure connection is available
 */
function fn_settings_actions_general_secure_checkout(&$new_value, $old_value) 
{
	return fn_settings_actions_general_secure_auth($new_value, $old_value);
}

/**
 * Check if secure connection is available
 */
function fn_settings_actions_general_secure_admin(&$new_value, $old_value)
{
	return fn_settings_actions_general_secure_auth($new_value, $old_value);
}

/**
 * Get FedEx meter number
 */
function fn_settings_actions_shippings_fedex_fedex_meter_get(&$new_value, $old_value)
{
	if ($new_value != 'Y') {
		return false;
	}

	echo ("Retreiving information from FEDEX server...");
	fn_flush();

	include_once(DIR_SHIPPING_FILES . 'fedex/fedexdc.php');

	$shipping_settings = fn_get_settings('Shippings', 'fedex');
	$account_number = $shipping_settings['account_number'];

	$params = array (
		'fedex_uri' => ($shipping_settings['test_mode'] == 'Y') ? 'https://gatewaybeta.fedex.com:443/GatewayDC' : 'https://gateway.fedex.com:443/GatewayDC',
	);

	$fed = new FedExDC($shipping_settings['account_number'], '', $params);

	$ship_ret = $fed->subscribe(
		array(
			1 => uniqid(TIME),
			4003 => $shipping_settings['fedex_meter_name'],
			4008 => $shipping_settings['fedex_meter_street'],
			4011 => $shipping_settings['fedex_meter_city'],
			4012 => $shipping_settings['fedex_meter_state'],
			4013 => $shipping_settings['fedex_meter_zipcode'],
			4014 => $shipping_settings['fedex_meter_country'],
			4015 => $shipping_settings['fedex_meter_phone'],
		)
	);
	echo ("<br />Done.");
	fn_flush();
	$error_tr = $fed->lookup('transaction_error_message');
	$error_req = $fed->getError();

	if ($error_tr || $error_req) {
		fn_set_notification('E', fn_get_lang_var('error'), (!empty($error_req) ? ($error_req . '<br />') : '') . (!empty($error_tr) ? $error_tr : ''));
	} else {
		$meter_number = $fed->lookup('meter_number');
		db_query("UPDATE ?:settings SET ?u WHERE option_name = 'meter_number' AND section_id = 'Shippings' AND subsection_id = 'fedex'", array('value' => $meter_number));
	}

	$new_value = 'N';
}

/**
 * Alter order initial ID
 */
function fn_settings_actions_general_order_start_id(&$new_value, $old_value)
{
	if (intval($new_value)) {
		db_query("ALTER TABLE ?:orders AUTO_INCREMENT = ?i", $new_value);
	}
}

/**
 * Enable/disable revisions objects
 */
function fn_settings_actions_general_active_revisions_objects(&$new_value, $old_value)
{
	$old = Registry::get('settings.General.active_revisions_objects');

	include_once(DIR_CORE . 'fn.revisions.php');
	fn_init_revisions();

	parse_str($new_value, $new);
	$revisions = Registry::get('revisions');

	$skip = array ();
	$show_notification = false;

	if ($revisions) {
		foreach ($old as $key => $rec) {
			if ($rec == 'N' && isset($new[$key])) {
				fn_create_revision_tables();
				fn_revisions_set_object_active($key);
				fn_echo(fn_get_lang_var('creating_revisions') . ' ' . fn_get_lang_var($revisions['objects'][$key]['title']));
				fn_revisions_delete_objects($key);
				fn_revisions_create_objects($key, true);
				fn_echo(' ' .fn_get_lang_var('done') . '<br>');
				$show_notification = true;
			} elseif ($rec == 'Y' && !isset($new[$key])) {
				fn_echo(fn_get_lang_var('deleting_revisions') . ' ' . fn_get_lang_var($revisions['objects'][$key]['title']));
				fn_revisions_delete_objects($key);
				fn_echo(' ' .fn_get_lang_var('done') . '<br>');
			}

			$skip[] = $key;
		}

		if (!empty($new)) {
			foreach ($new as $object => $_v) {
				if (!in_array($object, $skip)) {
					fn_create_revision_tables();
					fn_revisions_set_object_active($object);
					fn_echo(fn_get_lang_var('creating_revisions') . ' ' . fn_get_lang_var($revisions['objects'][$object]['title']));
					fn_revisions_delete_objects($object);
					fn_revisions_create_objects($object, true);
					fn_echo(' ' .fn_get_lang_var('done') . '<br>');
					$show_notification = true;
				}
			}
		}
		if ($show_notification) {
			$msg = fn_get_lang_var('warning_create_workflow');
			$msg = str_replace('[link]', fn_url("revisions_workflow.manage", 'A'), $msg);
			fn_set_notification('E', fn_get_lang_var('warning'), $msg, true);
		}
	}
}

/**
 * Enable/disable Canada Post
 */
function fn_settings_actions_shippings_can_enabled(&$new_value, $old_value)
{
	$currencies = Registry::get('currencies');
	if ($new_value == 'Y' && empty($currencies['CAD'])) {
		fn_set_notification('E', fn_get_lang_var('warning'), fn_get_lang_var('canada_post_activation_error'), true);
		$new_value = 'N';
	}
}

?>