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
// $Id: events.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$auth = & $_SESSION['auth'];

if ($mode == 'add' && Registry::get('addons.gift_registry.event_creators') == 'registered' && empty($auth['user_id'])) {
	return array(CONTROLLER_STATUS_REDIRECT, "auth.login_form?return_url=" . urlencode(Registry::get('config.current_url')));

}

if (!empty($_REQUEST['access_key'])) {
	$_REQUEST['event_id'] = 0;
	$data = db_get_row("SELECT object_id, object_type FROM ?:ekeys WHERE ekey = ?s", $_REQUEST['access_key']);
	if (!empty($data) && strpos('OG', $data['object_type']) !== false) {
		$_REQUEST['event_id'] = $data['object_id'];

		if ($data['object_type'] == 'G' && $mode == 'update') {
			return array(CONTROLLER_STATUS_REDIRECT, "events.view?access_key=$_REQUEST[access_key]");
		}

		if ($data['object_type'] == 'O') {// owner's key
			define('EVENT_OWNER', true);
		}

		$view->assign('access_key', $_REQUEST['access_key']);
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('error_invalid_access_key'));
		return array(CONTROLLER_STATUS_REDIRECT, "events.access_key");
	}

} elseif (!empty($_REQUEST['event_id'])) {
	$_data = db_get_row("SELECT user_id, type FROM ?:giftreg_events WHERE event_id = ?i AND type != 'D'", $_REQUEST['event_id']);

	// Check if the event exists
	if (empty($_data['type'])) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	// If event is private, ask for access code
	if ((!empty($_data['user_id']) && $auth['user_id'] != $_data['user_id']) && $mode == 'update') { // if this is user's event, go to login page
		return array(CONTROLLER_STATUS_REDIRECT, "auth.login_form?return_url=" . urlencode(Registry::get('config.current_url')));
	}

	if ($_data['type'] == 'U') {
		if ((!empty($_data['user_id']) && $auth['user_id'] == $_data['user_id'])) {
			define('EVENT_OWNER', true);
		} else {
			return array(CONTROLLER_STATUS_REDIRECT, "events.access_key");
		}
	} elseif (!empty($_data['user_id']) && $auth['user_id'] == $_data['user_id']) {
		define('EVENT_OWNER', true);
	}
}


if ($mode == 'delete_event') {
	if (defined('EVENT_OWNER')) {
		fn_event_delete($_REQUEST['event_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "events.search");

} elseif ($mode == 'unsubscribe') {
	db_query("DELETE FROM ?:giftreg_event_subscribers WHERE event_id = ?i AND email = ?s", $_REQUEST['event_id'], $_REQUEST['email']);
	fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_event_unsubscribe'));

	return array(CONTROLLER_STATUS_REDIRECT, "events.search");

} elseif ($mode == 'view') {
	$event_data = db_get_row("SELECT * FROM ?:giftreg_events WHERE event_id = ?i", $_REQUEST['event_id']);

	fn_add_breadcrumb($event_data['title']);

	$event_data['fields'] = db_get_hash_single_array("SELECT * FROM ?:giftreg_event_fields WHERE ?:giftreg_event_fields.event_id = ?i", array('field_id', 'value'), $_REQUEST['event_id']);

	$event_data['products'] = fn_get_event_product($_REQUEST['event_id'], Registry::get('settings.Appearance.products_per_page'), empty($_REQUEST['page']) ? 1 : $_REQUEST['page']);

	foreach ($event_data['products'] as $k => &$product) {
		$product['extra'] = unserialize($product['extra']);
		$product_options = $product['extra'];
		$product['product_options_ids'] = $product_options;
		$product['product_options'] = fn_get_selected_product_options_info($product_options, CART_LANGUAGE);
		$product['price'] = fn_get_product_price($product['product_id'], 1, $auth);
		$product['avail_amount'] = $product['amount'] - $product['ordered_amount'];

		// selected options combination
		$product['product_options_combination'] = fn_get_options_combination($product_options);

		fn_gather_additional_product_data($product, true, false, false, true);

		// If option combination image is exists than replace the main image with it
		$_options = $product_options;
		if (!empty($product['product_options']) && is_array($product['product_options'])) {
			foreach ($product['product_options'] as $_k => $_v) {
				if ($_v['inventory'] == 'N') {
					unset($_options[$_v['option_id']]);
				}
			}
		}
		$combination_hash = fn_generate_cart_id($product['product_id'], array('product_options' => $_options));
		if (!empty($product['option_image_pairs']) && is_array($product['option_image_pairs'])) {
			foreach ($product['option_image_pairs'] as $key => $opt_im) {
 				if ($opt_im['combination_hash'] == $combination_hash) {
					$product['main_pair'] = $opt_im;
					continue;
				}
			}
		}
	}

	$view->assign('event_id', $_REQUEST['event_id']);
	$view->assign('event_data', $event_data);

} elseif ($mode == 'access_key') {
	fn_add_breadcrumb(fn_get_lang_var('access_key'));
}

?>