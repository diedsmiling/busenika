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
// $Id: subscriptions.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

	$suffix = '';

	if ($mode == 'update') {
		fn_change_recurring_subscription_status($_REQUEST['subscription_id'], $_REQUEST['status'], '', fn_get_notification_rules($_REQUEST));

		//Update shipping info
		if (!empty($_REQUEST['update_shipping'])) {
			$additional_data = db_get_hash_single_array("SELECT type, data FROM ?:order_data WHERE order_id = ?i", array('type', 'data'), $_REQUEST['order_id']);
			// Get shipping information
			if (!empty($additional_data['L'])) {
				$shippings = unserialize($additional_data['L']);
				if (!empty($shippings)) {
					foreach((array)$shippings as $shipping_id => $shipping) {
						$shippings[$shipping_id] = fn_array_merge($shipping, $_REQUEST['update_shipping'][$shipping_id]);
					}
					db_query("UPDATE ?:order_data SET ?u WHERE order_id = ?i AND type = 'L'", array('data' => serialize($shippings)), $_REQUEST['order_id']);
				}
			}
		}

		$suffix = '.update?subscription_id=' . $_REQUEST['subscription_id'];
	}

	if ($mode == 'delete') {
		if (!empty($_REQUEST['subscription_ids'])) {
			fn_delete_recurring_subscriptions($_REQUEST['subscription_ids']);
		}

		$suffix = '.manage';
	}

	if ($mode == 'bulk_charge') {

		define('ORDER_MANAGEMENT', true);

		if (!empty($_REQUEST['subscription_ids'])) {
			foreach($_REQUEST['subscription_ids'] as $_id) {
				fn_charge_subscription($_id);
			}
			fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var(sizeof($_REQUEST['subscription_ids']) == 1 ? 'rb_subscription_charged' : 'rb_subscriptions_charged'));
		}

		$suffix = '.manage';
	}

	if ($mode == 'process_events') {
		if (!empty($_REQUEST['process_subscriptions'])) {
			$subscriptions = $_REQUEST['process_subscriptions'];
			$events = Registry::get('recurring_billing_data.events');

			if (!fn_is_empty($subscriptions)) {
				$list_subscriptions = array();
				foreach ($events as $evt => $val) {
					if (!empty($subscriptions[$evt])) {
						foreach ($subscriptions[$evt] as $subs_id) {
							$list_subscriptions[] = array('id' => $subs_id, 'type' => $evt);
						}
					}
				}

				$cache_file = 'recurring_events_' . md5(uniqid(rand()));

				if (fn_put_contents(DIR_COMPILED . $cache_file, serialize($list_subscriptions))) {
					return array(CONTROLLER_STATUS_OK, "subscriptions.batch_process_events?cache_file=$cache_file");
				} else {
					$msg = fn_get_lang_var('cannot_write_file');
					$msg = str_replace('[file]', DIR_COMPILED . $cache_file, $msg);
					fn_set_notification('E', fn_get_lang_var('error'), $msg);
				}
			}
		}

		$suffix = '.events';
	}

	return array(CONTROLLER_STATUS_OK, "subscriptions$suffix");
}


// ---------------------- GET routines ---------------------------------------

if ($mode == 'batch_process_events' && !empty($_REQUEST['cache_file'])) {
	$data = fn_get_contents(DIR_COMPILED . $_REQUEST['cache_file']);
	if (!empty($data)) {
		$data = @unserialize($data);
	}

	if (is_array($data)) {
		foreach (array_splice($data, 0, Registry::get('recurring_billing_data.events_per_pass')) as $evt) {
			if ($evt['type'] == 'A' || $evt['type'] == 'C') {
				fn_charge_subscription($evt['id']);
			} elseif ($evt['type'] == 'F' || $evt['type'] == 'M') {
				fn_recurring_subscription_notification($evt['id'], $evt['type']);
			}
			fn_echo(fn_get_lang_var('rb_subscription') . ' #' . $evt['id'] . ' ' . strtolower(fn_get_lang_var('processed')) . '<br />');
		}

		if (!empty($data)) {
			fn_put_contents(DIR_COMPILED . $_REQUEST['cache_file'], serialize($data));
			return array(CONTROLLER_STATUS_OK, "subscriptions.batch_process_events?cache_file=" . $_REQUEST['cache_file']);
		} else {
			fn_rm(DIR_COMPILED . $_REQUEST['cache_file']);
			fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('rb_subscriptions_processed'));
		}
	} else {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('rb_no_subscriptions_to_process'));
	}

	return array(CONTROLLER_STATUS_OK, "subscriptions.events");

} elseif ($mode == 'update') {
	$subscription = fn_get_recurring_subscription_info($_REQUEST['subscription_id'], true, true);

	if (empty($subscription)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	fn_add_breadcrumb(fn_get_lang_var('rb_subscriptions'), "subscriptions.manage.reset_view");
	fn_add_breadcrumb(fn_get_lang_var('search_results'), "subscriptions.manage.last_view");

	Registry::set('navigation.tabs', array (
		'general' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'linked_products' => array (
			'title' => fn_get_lang_var('products'),
			'js' => true
		),
		'paids' => array (
			'title' => fn_get_lang_var('orders'),
			'js' => true
		),
	));

	$view->assign('subscription', $subscription);

} elseif ($mode == 'manage') {

	list($subscriptions, $search) = fn_get_recurring_subscriptions($_REQUEST);

	$view->assign('subscriptions', $subscriptions);
	$view->assign('search', $search);

} elseif ($mode == 'delete') {

	if (!empty($_REQUEST['subscription_id'])) {
		fn_delete_recurring_subscriptions((array)$_REQUEST['subscription_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "subscriptions.manage");

} elseif ($mode == 'update_status') {

	$old_status = db_get_field("SELECT status FROM ?:recurring_subscriptions WHERE subscription_id = ?i", $_REQUEST['id']);
	if (!fn_change_recurring_subscription_status($_REQUEST['id'], $_REQUEST['status'], $old_status, fn_get_notification_rules($_REQUEST), true)) {
		$ajax->assign('return_status', $old_status);
	}

	exit;

} elseif ($mode == 'charge') {

	define('ORDER_MANAGEMENT', true);

	if (!empty($_REQUEST['subscription_id'])) {
		fn_charge_subscription($_REQUEST['subscription_id']);
		fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('rb_subscription_charged'));
	}

	return array(CONTROLLER_STATUS_REDIRECT, "subscriptions.manage");

} elseif ($mode == 'confirmation') {

	// [Breadcrumbs]
	fn_add_breadcrumb(fn_get_lang_var('orders'), "orders.manage");
	// [/Breadcrumbs]

	$view->assign('order_ids', $_SESSION['order_ids']);
	unset($_SESSION['order_ids']);

	$view->assign('subscriptions', $_SESSION['subscriptions']);
	unset($_SESSION['subscriptions']);

	$view->assign('redirect_url', $_SESSION['redirect_url']);
	unset($_SESSION['redirect_url']);

} elseif ($mode == 'events') {

	fn_delete_notification('rb_events');
	$events = fn_get_recurring_events();

	if (!fn_is_empty($events)) {
		$view->assign('recurring_events', $events);
		$view->assign('recurring_billing_data', Registry::get('recurring_billing_data'));
	}

} elseif ($mode == 'process_event') {

	if (!empty($_REQUEST['subscription_id']) && !empty($_REQUEST['type'])) {
		$list_subscriptions = array(
			array(
				'id' => $_REQUEST['subscription_id'],
				'type' => $_REQUEST['type']
			)
		);

		$cache_file = 'recurring_events_' . md5(uniqid(rand()));

		if (fn_put_contents(DIR_COMPILED . $cache_file, serialize($list_subscriptions))) {
			return array(CONTROLLER_STATUS_OK, "subscriptions.batch_process_events?cache_file=$cache_file");
		} else {
			$msg = fn_get_lang_var('cannot_write_file');
			$msg = str_replace('[file]', DIR_COMPILED . $cache_file, $msg);
			fn_set_notification('E', fn_get_lang_var('error'), $msg);
		}
	}

	return array(CONTROLLER_STATUS_REDIRECT, "subscriptions.events");
}

//
// [Functions]
//

function fn_charge_subscription($subscription_id)
{
	$_SESSION['cart'] = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
	$cart = & $_SESSION['cart'];

	$_SESSION['customer_auth'] = isset($_SESSION['customer_auth']) ? $_SESSION['customer_auth'] : array();
	$customer_auth = & $_SESSION['customer_auth'];

	fn_clear_cart($cart, true);
	$customer_auth = fn_fill_auth();

	$subscription = fn_get_recurring_subscription_info($subscription_id);

	if ($subscription['status'] != 'A') {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('rb_subscription_inactive'));

	} else {
		$product_data = array();
		foreach ($subscription['order_info']['items'] as $k => $item) {
			if (!empty($subscription['order_info']['items'][$k]['extra']['recurring_plan_id']) && $subscription['order_info']['items'][$k]['extra']['recurring_plan_id'] == $subscription['plan_id'] && $subscription['order_info']['items'][$k]['extra']['recurring_duration'] == $subscription['orig_duration']) {
				$product_data[$subscription['order_info']['items'][$k]['product_id']] = array (
					'amount' => $subscription['order_info']['items'][$k]['amount'],
					'extra' => array (
						'recurring_plan_id' => $subscription['plan_id'],
						'recurring_force_calculate' => true,
						'recurring_subscription_id' => $subscription['subscription_id'],
						'recurring_plan' => $subscription['order_info']['items'][$k]['extra']['recurring_plan'],
						'recurring_duration' => $subscription['order_info']['items'][$k]['extra']['recurring_duration']
					)
				);
				if (!empty($subscription['order_info']['items'][$k]['extra']['product_options'])) {
					$product_data[$subscription['order_info']['items'][$k]['product_id']]['product_options'] = $subscription['order_info']['items'][$k]['extra']['product_options'];
				}
			}
		}

		$cart['user_id'] = $subscription['user_id'];
		$u_data = db_get_row("SELECT user_id, user_type, tax_exempt FROM ?:users WHERE user_id = ?i", $cart['user_id']);
		$customer_auth = fn_fill_auth($u_data);
		$cart['user_data'] = array();

		fn_add_product_to_cart($product_data, $cart, $customer_auth);

		$cart['profile_id'] = 0;
		$cart['user_data'] = fn_get_user_info($customer_auth['user_id'], true, $cart['profile_id']);

		if (!empty($cart['user_data'])) {
			$profile_fields = fn_get_profile_fields('O', $customer_auth);
			$cart['ship_to_another'] = fn_check_shipping_billing($cart['user_data'], $profile_fields);
		}

		fn_calculate_cart_content($cart, $customer_auth, 'A', true, 'I');

		$cart['payment_id'] = $subscription['order_info']['payment_id'];
		$cart['payment_info'] = $subscription['order_info']['payment_info'];
		$cart['recurring_subscription_id'] = $subscription_id;

		list($order_id, $process_payment) = fn_place_order($cart, $customer_auth);
		if (!empty($order_id)) {
			$order_info = fn_get_order_info($order_id, true);
			$evt_data = array (
				'subscription_id' => $subscription_id,
				'timestamp' => $order_info['timestamp'],
				'event_type' => 'C'
			);

			db_query("INSERT INTO ?:recurring_events ?e", $evt_data);

			if ($process_payment == true) {
				fn_start_payment($order_id);
			}

			$edp_data = fn_generate_ekeys_for_edp(array(), $order_info);
			fn_order_notification($order_info, $edp_data);
		}
	}
}

function fn_recurring_subscription_notification($subscription_id, $notification_type)
{
	$addon_settings = Registry::get('addons.recurring_billing');

	$data = fn_get_recurring_subscription_info($subscription_id, false);
	$evt_data = array (
		'subscription_id' => $subscription_id,
		'timestamp' => TIME,
	);

	if ($notification_type == 'F') {
		Registry::get('view_mail')->assign('header', $addon_settings['rb_future_pay_email_header']);
		Registry::get('view_mail')->assign('subj', $addon_settings['rb_future_pay_email_subject']);

		$subj = 'addons/recurring_billing/future_notification_subj.tpl';
		$body = 'addons/recurring_billing/future_notification.tpl';

		$data['next_timestamp'] = db_get_field("SELECT timestamp FROM ?:recurring_events WHERE subscription_id = ?i AND event_type = 'P' AND timestamp > ?i GROUP BY subscription_id", $subscription_id, TIME);

	} elseif ($notification_type == 'M') {
		Registry::get('view_mail')->assign('header', $addon_settings['rb_manual_pay_email_header']);
		Registry::get('view_mail')->assign('subj', $addon_settings['rb_manual_pay_email_subject']);

		$subj = 'addons/recurring_billing/manual_notification_subj.tpl';
		$body = 'addons/recurring_billing/manual_notification.tpl';

	} else {
		return false;
	}

	$evt_data['event_type'] = $notification_type;
	db_query("INSERT INTO ?:recurring_events ?e", $evt_data);

	Registry::get('view_mail')->assign('subscription_info', $data);

	fn_send_mail($data['email'], Registry::get('settings.Company.company_orders_department'), $subj, $body);

	return true;
}

//
// [/Functions]
//
?>