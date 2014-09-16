<?php
/******************************************************************************
*                                                                             *
*     Copyright (c) 2004 Simbirsk Technologies LTD. All rights reserved.      *
*                                                                             *
*******************************************************************************
*                                                                             *
* CS-Cart  is  commercial  software,  only  users  who have purchased a valid *
* license through  http://www.cs-cart.com/  and  accept  to the terms of this *
* License Agreement can install this product.                                 *
*                                                                             *
*******************************************************************************
* THIS  CS-CART  SHOP END-USER LICENSE AGREEMENT IS A LEGAL AGREEMENT BETWEEN *
* YOU  AND  YOUR  COMPANY (COLLECTIVELY, "YOU") AND SIMBIRSK TECHNOLOGIES LTD *
* (HEREINAFTER  REFERRED  TO   AS  "THE AUTHOR")  FOR  THE  SOFTWARE  PRODUCT *
* IDENTIFIED  ABOVE,  WHICH  INCLUDES  COMPUTER   SOFTWARE  AND  MAY  INCLUDE *
* ASSOCIATED   MEDIA,   PRINTED  M ATERIALS,   AND   "ONLINE"  OR  ELECTRONIC *
* DOCUMENTATION  (COLLECTIVELY,  THE  "SOFTWARE").  BY  USING  THE  SOFTWARE, *
* YOU  SIGNIFY YOUR AGREEMENT TO ALL TERMS, CONDITIONS, AND NOTICES CONTAINED *
* OR  REFERENCED  HEREIN.  IF  YOU  ARE NOT  WILLING  TO  BE  BOUND  BY  THIS *
* AGREEMENT, DO NOT INSTALL OR USE THE SOFTWARE.                              *
*                                                                             *
* PLEASE   READ  THE   FULL  TEXT  OF  SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS *
* ALSO AVAILABLE AT THE FOLLOWING URL: http://www.cs-cart.com/license.html    *
******************************************************************************/

//
// $Id: func.php 6788 2009-01-16 13:29:11Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_get_recurring_plan_data($plan_id, $lang_code = CART_LANGUAGE)
{
	if (empty($plan_id)) {
		return false;
	}

	$plan_data = db_get_row("SELECT plans.*, d.object as name, d.description FROM ?:recurring_plans as plans LEFT JOIN ?:common_descriptions as d ON d.object_holder = 'recurring_plans' AND d.object_id = plans.plan_id AND d.lang_code = ?s WHERE plans.plan_id = ?i", $lang_code, $plan_id);

	if (!empty($plan_data)) {
		$plan_data['price'] = unserialize($plan_data['price']);
		if (!empty($plan_data['start_price'])) {
			$plan_data['start_price'] = unserialize($plan_data['start_price']);
		}
	}

	return $plan_data;
}

//
// Get data of recurring plans
//
function fn_get_recurring_plans($params, $lang_code = CART_LANGUAGE)
{
	$params['page'] = empty($params['page']) ? 1 : $params['page'];

	$total = db_get_field("SELECT COUNT(*) FROM ?:recurring_plans as plans LEFT JOIN ?:common_descriptions as com_descr ON object_holder = 'recurring_plans' AND object_id = plan_id AND lang_code = ?s WHERE 1 ORDER BY object", $lang_code);

	$items_per_page = Registry::get('settings.Appearance.admin_elements_per_page');
	$limit = fn_paginate($params['page'], $total, $items_per_page);

	return db_get_hash_array("SELECT plans.plan_id, descr.object as name, plans.period, plans.status FROM ?:recurring_plans as plans LEFT JOIN ?:common_descriptions as descr ON descr.object_holder = 'recurring_plans' AND descr.object_id = plans.plan_id AND descr.lang_code = ?s ORDER BY descr.object $limit ", 'plan_id', $lang_code);
}

function fn_settings_variants_addons_recurring_billing_rb_initial_order_status()
{
	$variants = array();
	$variants[''] = fn_get_lang_var('default');
	$statuses = fn_get_statuses(STATUSES_ORDER, true);
	foreach ($statuses as $key => $val) {
		$variants[$key] = $val;
	}
	return $variants;
}

function fn_recurring_billing_additional_settings(&$fields, $section, &$addon_options)
{
	foreach ($addon_options as $key => $val) {
		if (strpos($key, 'rb_order_status_') !== false) {
			unset($addon_options[$key]);
		}
	}

	$statuses = fn_get_statuses();
	foreach ($statuses as $key => $val) {
		$addon_options['rb_order_status_' . $key . '_email_subject'] = '%ML%';
		$addon_options['rb_order_status_' . $key . '_email_header'] = '%ML%';

		$fields[$section]['rb_order_status_' . $key . '_header'] = array (
			'type' => 'H',
			'description' => fn_get_lang_var('status') . ': ' . $val['description']
		);
		$fields[$section]['rb_order_status_' . $key . '_email_subject'] = array (
			'type' => 'I',
			'description' => fn_get_lang_var('email_subject')
		);
		$fields[$section]['rb_order_status_' . $key . '_email_header'] = array (
			'type' => 'T',
			'description' => fn_get_lang_var('email_header')
		);
	}
}

function fn_calculate_recurring_price($price, $cond)
{
	$new_price = $price;
	
	if ($cond['type'] == 'original') {
		$new_price = $price;

	} elseif ($cond['type'] == 'to_percentage') {
		$new_price = $price * $cond['value'] / 100;

	} elseif ($cond['type'] == 'by_percentage') {
		$new_price = $price * (100 - $cond['value']) / 100;

	} elseif ($cond['type'] == 'to_fixed') {
		$new_price = $cond['value'];

	} elseif ($cond['type'] == 'by_fixed') {
		$new_price = $price - $cond['value'];
	}

	if ($new_price < 0) {
		$new_price = 0;
	}

	return fn_format_price($new_price);
}

//
// Get recurring plan name
//
function fn_get_recurring_plan_name($plan_id, $lang_code = CART_LANGUAGE)
{
	if (!empty($plan_id)) {
		return db_get_field("SELECT object FROM ?:common_descriptions WHERE object_holder = 'recurring_plans' AND object_id = ?i AND lang_code = ?s", $plan_id, $lang_code);
	}

	return false;
}

function fn_get_recurring_subscriptions($params, $additional_data = array())
{
	// Init filter
	$params = fn_init_view('subscriptions', $params);

	$params['page'] = empty($params['page']) ? 1 : $params['page'];

	$fields = array (
		'?:recurring_subscriptions.*'
	);
	
	// Define sort fields
	$sortings = array (
		'subscription_id' => 'subscription_id',
		'order_id' => 'order_id',
		'status' => 'status',
		'customer' => array('lastname', 'firstname'),
		'email' => 'email',
		'date' => 'timestamp',
		'price' => 'price',
		'last_paid' => 'last_timestamp',
		'duration' => 'duration'
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		$params['sort_order'] = 'desc';
	}

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = 'date';
	}

	$sorting = (is_array($sortings[$params['sort_by']]) ? implode(' ' . $directions[$params['sort_order']]. ', ', $sortings[$params['sort_by']]): $sortings[$params['sort_by']]). " " .$directions[$params['sort_order']];

	// Reverse sorting (for usage in view)
	$params['sort_order'] = $params['sort_order'] == 'asc' ? 'desc' : 'asc';

	$condition = $join = $group = '';

	if (isset($params['cname']) && fn_string_no_empty($params['cname'])) {
		$arr = fn_explode(' ', $params['cname']);
		foreach ($arr as $k => $v) {
			if (!fn_string_no_empty($v)) {
				unset($arr[$k]);
			}
		}
		if (sizeof($arr) == 2) {
			$condition .= db_quote(" AND ?:recurring_subscriptions.firstname LIKE ?l AND ?:recurring_subscriptions.lastname LIKE ?l", "%".array_shift($arr)."%", "%".array_shift($arr)."%");
		} else {
			$condition .= db_quote(" AND (?:recurring_subscriptions.firstname LIKE ?l OR ?:recurring_subscriptions.lastname LIKE ?l)", "%".trim($params['cname'])."%", "%".trim($params['cname'])."%");
		}
	}

	if (isset($params['email']) && fn_string_no_empty($params['email'])) {
		$condition .= db_quote(" AND ?:recurring_subscriptions.email LIKE ?l", "%".trim($params['email'])."%");
	}

	if (isset($params['price_from']) && fn_is_numeric($params['price_from'])) {
		$condition .= db_quote(" AND ?:recurring_subscriptions.price >= ?d", fn_convert_price($params['price_from']));
	}

	if (isset($params['price_to']) && fn_is_numeric($params['price_to'])) {
		$condition .= db_quote(" AND ?:recurring_subscriptions.price <= ?d", fn_convert_price($params['price_to']));
	}

	if (!empty($params['status'])) {
		$condition .= db_quote(' AND ?:recurring_subscriptions.status = ?s', $params['status']);
	}

	if (!empty($params['order_id'])) {
		$condition .= db_quote(' AND ?:recurring_subscriptions.order_id IN (?n)', $params['order_id']);
	}

	if (!empty($params['plan_id'])) {
		$condition .= db_quote(' AND ?:recurring_subscriptions.plan_id IN (?n)', $params['plan_id']);
	}

	if (!empty($params['p_ids'])) {
		$arr = (strpos($params['p_ids'], ',') !== false || !is_array($params['p_ids'])) ? explode(',', $params['p_ids']) : $params['p_ids'];

		$condition .= db_quote(" AND ?:order_details.product_id IN (?a)", $arr);

		$join .= " LEFT JOIN ?:order_details ON ?:order_details.order_id = ?:recurring_subscriptions.order_id AND ?:order_details.extra LIKE '%recurring_plan_id%'";
		$group .= " GROUP BY ?:recurring_subscriptions.subscription_id HAVING COUNT(?:recurring_subscriptions.subscription_id) >= " . count($arr);
	}

	if (!empty($params['period_type']) && !empty($params['period']) && $params['period'] != 'A') {
		if ($params['period_type'] == 'D') {
			$timestamp = 'timestamp';
		} elseif ($params['period_type'] == 'L') {
			$timestamp = 'last_timestamp';
		} else {
			$timestamp = 'end_timestamp';
		}

		list($params['time_from'], $params['time_to']) = fn_create_periods($params);

		$condition .= db_quote(" AND (?:recurring_subscriptions.$timestamp >= ?i AND ?:recurring_subscriptions.$timestamp <= ?i)", $params['time_from'], $params['time_to']);
	}

	if (!empty($additional_data['plan_options'])) {
		$join .= " LEFT JOIN ?:recurring_plans ON ?:recurring_plans.plan_id = ?:recurring_subscriptions.plan_id";
		$fields[] = '?:recurring_plans.allow_unsubscribe';
		$fields[] = '?:recurring_plans.allow_change_duration';
	}

	if (!empty($additional_data['user_id'])) {
		$condition .= db_quote(" AND ?:recurring_subscriptions.user_id = ?i AND ?:recurring_subscriptions.status = 'A'", $additional_data['user_id']);

	} elseif (!empty($additional_data['order_ids'])) {
		$condition .= db_quote(" AND FIND_IN_SET(?:recurring_subscriptions.order_id, ?s) AND ?:recurring_subscriptions.status = 'A'", implode(',', $additional_data['order_ids']));
	}

	$total = db_get_field("SELECT DISTINCT(COUNT(?:recurring_subscriptions.subscription_id)) FROM ?:recurring_subscriptions $join WHERE 1 $condition $group");

	$items_per_page = Registry::get('settings.Appearance.admin_elements_per_page');
	$limit = fn_paginate($params['page'], $total, $items_per_page);

	$subscriptions = db_get_hash_array("SELECT " . implode(', ', $fields) . " FROM ?:recurring_subscriptions $join WHERE 1 $condition $group ORDER BY $sorting $limit", 'subscription_id');

	return array($subscriptions, $params);
}

function fn_subscription_is_paid($order_status)
{
	$order_statuses = fn_get_statuses(STATUSES_ORDER, false, true);
	
	return $order_statuses[$order_status]['inventory'] != 'D' || substr_count('O', $order_status) > 0 ? false : true;
}

function fn_get_recurring_subscription_info($subscription_id, $get_order_info = true, $get_plan_info = false)
{
	$subscription = db_get_row("SELECT * FROM ?:recurring_subscriptions WHERE subscription_id = ?i", $subscription_id);

	if (empty($subscription)) {
		return false;
	}

	if ($get_order_info) {
		$subscription['order_info'] = fn_get_order_info($subscription['order_id']);
	}

	if ($get_plan_info) {
		$subscription['plan_info'] = fn_get_recurring_plan_data($subscription['plan_id']);
	}

	return $subscription;
}

function fn_get_period_date($timestamp, $duration, $type = 'month')
{
	$date = getdate($timestamp);
	$new_date = $timestamp;

	if ($type == 'month') {
		$new_date = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'] + $duration, $date['mday'], $date['year']);
	} elseif ($type == 'day') {
		$new_date = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'] + $duration, $date['year']);
	} elseif ($type == 'year') {
		$new_date = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'], $date['year'] + $duration);
	}

	return $new_date;
}

function fn_change_recurring_subscription_status($subscription_id, $status_to, $status_from = '', $force_notification = array(), $display_notification = false)
{
	if (empty($status_from)) {
		$status_from = db_get_field("SELECT status FROM ?:recurring_subscriptions WHERE subscription_id = ?i", $subscription_id);
	}

	if ($status_from == 'U') {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('rb_unsubscribed_status_not_changed'));
		return false;
	}

	if (empty($status_to)) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('error_status_not_changed'));
		return false;
	}

	if ($status_to == 'A') {
		fn_apply_subscription_usergroup($subscription_id);
	} else {
		fn_remove_subscription_usergroup((array)$subscription_id);
	}

	if (!empty($force_notification['C'])) {
		Registry::get('view_mail')->assign('header', Registry::get('addons.recurring_billing.rb_changing_email_header'));
		Registry::get('view_mail')->assign('subj', Registry::get('addons.recurring_billing.rb_changing_email_subject'));

		$subscription = fn_get_recurring_subscription_info($subscription_id);
		Registry::get('view_mail')->assign('subscription_info', $subscription);

		fn_send_mail($subscription['email'], Registry::get('settings.Company.company_orders_department'), 'addons/recurring_billing/subscription_notification_subj.tpl', 'addons/recurring_billing/subscription_notification.tpl', '', $subscription['order_info']['lang_code']);
	}

	db_query("UPDATE ?:recurring_subscriptions SET status = ?s WHERE subscription_id = ?i", $status_to, $subscription_id);
	if ($display_notification) {
		fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('status_changed'));
	}

	return true;
}

function fn_change_subscription_dates($subscription_id, $plan_id, $start_date, $duration)
{
	if (!empty($subscription_id)) {
		db_query("DELETE FROM ?:recurring_events WHERE subscription_id = ?i AND event_type = 'P'", $subscription_id);
		$plan_data = fn_get_recurring_plan_data($plan_id);
		$end_date = fn_get_period_date($start_date, $duration);

		$date = getdate($start_date);
		if ($plan_data['period'] == 'A') {
			$next_date = mktime($date['hours'], $date['minutes'], $date['seconds'], 0, $plan_data['pay_day'], $date['year'] + 1);
			$next_duration = 1;
			$period = 'year';
		} elseif ($plan_data['period'] == 'Q') {
			$next_date = mktime($date['hours'], $date['minutes'], $date['seconds'], ceil($date['mon'] / 3) * 3 + 1, $plan_data['pay_day'], $date['year']);
			$period = 'month';
			$next_duration = 3;
		} elseif ($plan_data['period'] == 'M') {
			$next_date = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'] + 1, $plan_data['pay_day'], $date['year']);
			$period = 'month';
			$next_duration = 1;
		} elseif ($plan_data['period'] == 'W') {
			$day = $date['wday'] >= $plan_data['pay_day'] ? 7 - $date['wday'] + $plan_data['pay_day'] : $plan_data['pay_day'] - $date['wday'];
			$next_date = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'] + $day, $date['year']);
			$period = 'day';
			$next_duration = 7;
		} elseif ($plan_data['period'] == 'P') {
			$next_date = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'] + $plan_data['by_period'], $date['year']);
			$period = 'day';
			$next_duration = $plan_data['by_period'];
		}

		while ($next_date < $end_date) {
			$data = array (
				'subscription_id' => $subscription_id,
				'timestamp' => $next_date,
				'event_type' => 'P'
			);

			db_query("INSERT INTO ?:recurring_events ?e", $data);
			$next_date = fn_get_period_date($next_date, $next_duration, $period);
		}
	}
}

//
// Delete subscription
//
function fn_delete_recurring_subscriptions($subscription_ids)
{
	if (!empty($subscription_ids)) {
		if (!is_array($subscription_ids)) {
			$subscription_ids = explode(',', $subscription_ids);
		}
		fn_remove_subscription_usergroup($subscription_ids);
		db_query("DELETE FROM ?:recurring_subscriptions WHERE subscription_id IN (?n)", $subscription_ids);
		db_query("DELETE FROM ?:recurring_events WHERE subscription_id IN (?n)", $subscription_ids);
	}
}

function fn_remove_subscription_usergroup($subscription_ids)
{
	$plan_ids = db_get_hash_multi_array("SELECT user_id, plan_id FROM ?:recurring_subscriptions WHERE status = 'A' AND subscription_id IN(?n) GROUP BY plan_id, user_id", array('plan_id', 'user_id'), $subscription_ids);
	foreach ($plan_ids as $plan_id => $users) {
		$usergroups = db_get_hash_single_array("SELECT usergroup_id, recurring_plans_ids FROM ?:usergroups WHERE FIND_IN_SET(?i, recurring_plans_ids)", array('usergroup_id', 'recurring_plans_ids'), $plan_id);
		if (!empty($usergroups)) {
			foreach ($users as $user_id => $_data) {
				foreach ($usergroups as $ug_id => $_plan_ids) {
					$more_subscription = db_get_fields("SELECT subscription_id FROM ?:recurring_subscriptions WHERE status = 'A' AND plan_id IN($_plan_ids) AND user_id = ?i AND subscription_id NOT IN(?n)", $user_id, $subscription_ids);
					if (empty($more_subscription)) {
						db_query("DELETE FROM ?:usergroup_links WHERE user_id = ?i AND usergroup_id = ?i", $user_id, $ug_id);
					}
				}
			}
		}
	}
}

function fn_apply_subscription_usergroup($subscription_id)
{
	$subscription_data = fn_get_recurring_subscription_info($subscription_id, false);
	$usergroup_ids = db_get_fields("SELECT usergroup_id FROM ?:usergroups WHERE FIND_IN_SET(?i, recurring_plans_ids) AND status = 'A'", $subscription_data['plan_id']);
	if (!empty($usergroup_ids)) {
		foreach ($usergroup_ids as $usergroup_id) {
			$_data = array (
				'user_id' => $subscription_data['user_id'],
				'usergroup_id' => $usergroup_id,
				'status' => 'A'
			);
			db_query("REPLACE INTO ?:usergroup_links SET ?u", $_data);
		}
	}
}

function fn_get_recurring_events()
{
	$addon_settings = Registry::get('addons.recurring_billing');
	$date = getdate();
	$end_time = mktime(23, 59, 59, $date['mon'], $date['mday'], $date['year']);
	$last_day = fn_get_period_date($end_time, -1, 'day');
	$events = array();

	if (!empty($addon_settings['rb_manual_pay_duration'])) {
		$start_time = fn_get_period_date($end_time, - $addon_settings['rb_manual_pay_duration'], 'day');

		db_query("DELETE FROM ?:recurring_events WHERE event_type = 'M' AND timestamp < ?i", $start_time);

		$manual_sub = db_get_fields("SELECT subs.subscription_id FROM ?:recurring_subscriptions as subs LEFT JOIN ?:orders as ord ON subs.order_id = ord.order_id LEFT JOIN ?:payments as pm ON ord.payment_id = pm.payment_id LEFT JOIN ?:payment_processors as pp ON pm.processor_id = pp.processor_id LEFT JOIN ?:order_data as od ON subs.order_id = od.order_id AND od.type = 'P' WHERE subs.status = 'A' AND subs.last_timestamp > ?i AND subs.last_timestamp < ?i AND (pm.processor_id = 0 OR pp.callback != 'Y' OR pp.type != 'P' OR NOT od.data)", $start_time, $end_time);

		if (!empty($manual_sub)) {
			$manual_sub_done = db_get_fields("SELECT subscription_id FROM ?:recurring_events WHERE event_type = 'M' AND subscription_id IN (?n) AND timestamp > ?i", $manual_sub, $last_day);
			$manual_sub_need = array_diff($manual_sub, $manual_sub_done);
			$events['M'] = $manual_sub_need;
		}
	}

	if (!empty($addon_settings['rb_attempt_period']) && !empty($addon_settings['rb_fail_attempts'])) {
		$start_time = fn_get_period_date($end_time, - $addon_settings['rb_attempt_period'] * $addon_settings['rb_fail_attempts'], 'day');

		db_query("DELETE FROM ?:recurring_events WHERE event_type = 'A' AND timestamp < ?i", $start_time);

		$failed_sub = db_get_fields("SELECT subs.subscription_id FROM ?:orders as ord INNER JOIN ?:recurring_subscriptions as subs ON FIND_IN_SET(ord.order_id, subs.order_ids) WHERE subs.status = 'A' AND ord.timestamp > ?i AND ord.timestamp < ?i GROUP BY subs.subscription_id HAVING COUNT(subs.subscription_id) < ?i AND MAX(ord.order_id) IN(SELECT ord2.order_id FROM ?:orders as ord2 WHERE ord2.status = 'F')", $start_time, $end_time, $addon_settings['rb_fail_attempts']);

		if (!empty($failed_sub)) {
			$events['A'] = $failed_sub;
		}
	}

	if (!empty($addon_settings['rb_future_pay_duration'])) {
		$start_time = fn_get_period_date($end_time, $addon_settings['rb_future_pay_duration'], 'day');

		db_query("DELETE FROM ?:recurring_events WHERE event_type = 'F' AND timestamp < ?i", $last_day);

		$future_sub = db_get_fields("SELECT evt.subscription_id FROM ?:recurring_events as evt LEFT JOIN ?:recurring_subscriptions as subs ON evt.subscription_id = subs.subscription_id WHERE subs.status = 'A' AND evt.timestamp < ?i AND evt.timestamp > ?i AND evt.event_type = 'P' GROUP BY evt.subscription_id", $start_time, $end_time);

		if (!empty($future_sub)) {
			$future_sub_done = db_get_fields("SELECT subscription_id FROM ?:recurring_events WHERE event_type = 'F' AND subscription_id IN (?n) AND timestamp > ?i", $future_sub, $last_day);
			$future_sub_need = array_diff($future_sub, $future_sub_done);
			$events['F'] = $future_sub_need;
		}
	}

	db_query("DELETE FROM ?:recurring_events WHERE event_type = 'P' AND timestamp < ?i", $last_day);

	$charge_sub = db_get_fields("SELECT evt.subscription_id FROM ?:recurring_events as evt LEFT JOIN ?:recurring_subscriptions as subs ON evt.subscription_id = subs.subscription_id WHERE subs.status = 'A' AND evt.timestamp < ?i AND evt.timestamp > ?i AND evt.event_type = 'P'", $end_time, $last_day);

	if (!empty($charge_sub)) {
		$charge_sub_done = db_get_fields("SELECT subscription_id FROM ?:recurring_events WHERE event_type = 'C' AND subscription_id IN (?n) AND timestamp > ?i", $charge_sub, $last_day);
		$charge_sub_need = array_diff($charge_sub, $charge_sub_done);
		$events['C'] = $charge_sub_need;
	}

	//Finalizing subscription
	$completed_subscriptions = db_get_fields("SELECT subscription_id FROM ?:recurring_subscriptions WHERE status != 'C' AND status != 'U' AND subscription_id NOT IN(SELECT subscription_id FROM ?:recurring_events GROUP BY subscription_id)");

	if (!empty($completed_subscriptions)) {
		fn_remove_subscription_usergroup($completed_subscriptions);
		db_query("UPDATE ?:recurring_subscriptions SET status = 'C' WHERE subscription_id IN(?n)", $completed_subscriptions);
	}

	return $events;
}

function fn_get_recurring_period_name($period_key)
{
	static $recurring_billing_data;

	if (empty($recurring_billing_data)) {
		$recurring_billing_data = Registry::get('recurring_billing_data');
	}

	return fn_get_lang_var($recurring_billing_data['periods'][$period_key]);
}

function fn_recurring_billing_get_additional_product_data_before_discounts(&$product, $auth, $get_options)
{
	if (AREA != 'A' && !empty($product['product_id']) && empty($product['extra']['recurring_price_calculated']) && empty($product['extra']['parent']) && empty($product['exclude_from_calculate'])) {
		$plan_ids = db_get_fields("SELECT plan_id FROM ?:recurring_plans WHERE status = 'A' AND FIND_IN_SET(?i, product_ids)", $product['product_id']);
		if (!empty($plan_ids)) {
			$plans = array();
			$base_price = $product['base_price'];
			$_free_buy = false;

			if ($cut_plan_id = Registry::get('recurring_plan_id')) {
				$product['extra']['recurring_plan_id'] = $cut_plan_id;
				Registry::del('recurring_plan_id');
			}

			foreach ($plan_ids as $ind => $id) {
				$plans[$id] = fn_get_recurring_plan_data($id);
				$price_cond = empty($plans[$id]['start_duration']) ? $plans[$id]['price'] : $plans[$id]['start_price'];
				$plans[$id]['base_price'] = fn_apply_options_modifiers($product['selected_options'], fn_calculate_recurring_price($base_price, $price_cond), 'P');
				if ($plans[$id]['base_price'] < 0) {
					$plans[$id]['base_price'] = 0;
				}
				
				$plans[$id]['last_base_price'] = fn_apply_options_modifiers($product['selected_options'], fn_calculate_recurring_price($base_price, $plans[$id]['price']), 'P');
				if ($plans[$id]['last_base_price'] < 0) {
					$plans[$id]['last_base_price'] = 0;
				}
				if ($plans[$id]['allow_free_buy'] == 'Y') {
					$_free_buy = true;
				}
				if ($ind == 0 && empty($product['extra']['recurring_plan_id']) || !empty($product['extra']['recurring_plan_id']) && $product['extra']['recurring_plan_id'] == $id) {
					$_base_price = $plans[$id]['base_price'];
				}
			}
			if ($_free_buy) {
				array_unshift($plans, array(
					'plan_id' => 0,
					'base_price' => $base_price,
					'last_base_price' => $base_price
				));
			}
			if (!$_free_buy || ($_free_buy && !empty($product['extra']['recurring_plan_id']))) {
				$product['price'] = $product['base_price'] = $product['original_price'] = $product['display_price'] = $product['display_subtotal'] = $product['subtotal'] = $_base_price;
			}
			$product['recurring_plans'] = $plans;
			$product['extra']['recurring_price_calculated'] = true;
		}
	}
	
	if (!empty($product['recurring_plan_id']) && isset($product['recurring_plans'][$product['recurring_plan_id']])) {
		$product['price'] = $product['base_price'] = $product['original_price'] = $product['display_price'] = $product['display_subtotal'] = $product['subtotal'] = $product['recurring_plans'][$product['recurring_plan_id']]['base_price'];
	}
}

function fn_recurring_billing_pre_add_to_cart(&$product_data, &$cart, $auth, $update = false)
{
	foreach ($product_data as $k => $v) {
		if (!empty($v['cart_id'])) { // if we're editing the subscription, just delete product and add new
			fn_delete_cart_product($cart, $v['cart_id']);
		}
		if (!empty($product_data[$k]['extra']['recurring_plan_id']) || /*!empty($product_data[$k]['extra']['parent']) ||*/ (isset($v['recurring_plan_id']) && $v['recurring_plan_id'] == 0)) { //FIXME: Recurring billing should work correctly with the product configurator!
			continue;
		} elseif (!empty($v['recurring_plan_id'])) {
			$plan_data = fn_get_recurring_plan_data($v['recurring_plan_id']);
			if (!empty($plan_data) && $plan_data['status'] == 'A') {
				$product_data[$k]['extra']['recurring_plan_id'] = $v['recurring_plan_id'];
				if (isset($v['recurring_duration']) && $plan_data['allow_change_duration'] == 'Y') {
					$duration = intval($v['recurring_duration']);
					if ($duration > 0) {
						$product_data[$k]['extra']['recurring_duration'] = $duration;
					} else {
						fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('rb_duration_did_not_null'));
						unset($product_data[$k]);
					}
				} else {
					$product_data[$k]['extra']['recurring_duration'] = $plan_data['duration'];
				}
			}
			$product_data[$k]['extra']['recurring_plan'] = $plan_data;
		} elseif (isset($cart['products'][$k]['extra']['recurring_plan_id'])) {
			$product_data[$k]['extra']['recurring_plan_id'] = $cart['products'][$k]['extra']['recurring_plan_id'];
			$product_data[$k]['extra']['recurring_duration'] = $cart['products'][$k]['extra']['recurring_duration'];
			$product_data[$k]['extra']['recurring_plan'] = $cart['products'][$k]['extra']['recurring_plan'];
		} else {
			$plans = db_get_array("SELECT plan_id, allow_free_buy FROM ?:recurring_plans WHERE status = 'A' AND FIND_IN_SET(?i, product_ids)", empty($v['product_id']) ? $k : $v['product_id']);
			
			if (!empty($plans)) {
				$allow_free_buy = false;
				foreach ($plans as $plan) {
					if ($plan['allow_free_buy'] == 'Y') {
						$allow_free_buy = true;
					}
				}
				
				if ((AREA == 'A' || isset($product_data[$k]['extra']['exclude_from_calculate'])) || $allow_free_buy) {
					continue;
				}
				
				$plan = reset($plans);
				$plan_data = fn_get_recurring_plan_data($plan['plan_id']);
				
				$product_data[$k]['extra']['recurring_plan_id'] = $plan['plan_id'];
				$product_data[$k]['extra']['recurring_duration'] = $plan_data['duration'];
				$product_data[$k]['extra']['recurring_plan'] = $plan_data;
			}
		}
	}
}

function fn_recurring_billing_generate_cart_id(&$_cid, $extra, $only_selectable)
{
	if (isset($extra['recurring_plan_id'])) {
		$_cid[] = $extra['recurring_plan_id'];
		if (isset($extra['recurring_duration'])) {
			$plan = fn_get_recurring_plan_data($extra['recurring_plan_id']);
			if ($plan['allow_change_duration'] == 'Y' && $extra['recurring_duration'] != $plan['duration']) {
				$_cid[] = $extra['recurring_duration'];
			}
		}
	}
}

function fn_recurring_billing_get_cart_product_data($product_id, &$p_data, &$product)
{
	if ((AREA != 'A' || !empty($product['extra']['recurring_force_calculate'])) && !empty($product['extra']['recurring_plan_id'])) {
		$plan_data = fn_get_recurring_plan_data($product['extra']['recurring_plan_id']);
		if (empty($plan_data['start_duration'])) {
			$price_cond = $plan_data['price'];
		} else {
			if (!empty($product['extra']['recurring_subscription_id'])) {
				$subscription = fn_get_recurring_subscription_info($product['extra']['recurring_subscription_id'], false);
				$start_price_time = fn_get_period_date($subscription['timestamp'], $plan_data['start_duration'], ($plan_data['start_duration_type'] == 'D' ? 'day' : 'month'));
				$price_cond = $start_price_time < TIME ? $plan_data['price'] : $plan_data['start_price'];
			} else {
				$price_cond = $plan_data['start_price'];
			}
		}
		$p_data['base_price'] = $p_data['price'] = $p_data['original_price'] = $product['display_price'] = $product['display_subtotal'] = $product['subtotal'] = fn_calculate_recurring_price($p_data['price'], $price_cond);
		$p_data['extra']['recurring_price_calculated'] = true;
		$p_data['extra']['recurring_plan_id'] = $product['extra']['recurring_plan_id'];
		$p_data['extra']['recurring_plan'] = $product['extra']['recurring_plan'];
		$p_data['extra']['recurring_duration'] = $product['extra']['recurring_duration'];
	}
}

function fn_recurring_billing_change_order_status($status_to, $status_from, $order_info, $force_notification, $order_statuses)
{
	if (!empty($order_info['items'])) {
		if ($order_statuses[$status_to]['inventory'] == 'D' && substr_count('O', $status_to) == 0 && ($order_statuses[$status_from]['inventory'] != 'D' || substr_count('O', $status_from) > 0)) {
			$apply = true;
		} elseif (($order_statuses[$status_to]['inventory'] != 'D' && substr_count('O', $status_from) == 0 || substr_count('O', $status_to) > 0) && $order_statuses[$status_from]['inventory'] == 'D') {
			$apply = false;
		}
		if (isset($apply)) {
			foreach ($order_info['items'] as $product) {
				if (!empty($product['extra']['recurring_plan_id'])) {
					$usergroup_ids = db_get_fields("SELECT usergroup_id FROM ?:usergroups WHERE FIND_IN_SET(?i, recurring_plans_ids) AND status = 'A'", $product['extra']['recurring_plan_id']);
					if (!empty($usergroup_ids)) {
						foreach ($usergroup_ids as $usergroup_id) {
							if ($apply) {
								$_data = array (
									'user_id' => $order_info['user_id'],
									'usergroup_id' => $usergroup_id,
									'status' => 'A'
								);
								db_query("REPLACE INTO ?:usergroup_links SET ?u", $_data);
							} else {
								db_query("DELETE FROM ?:usergroup_links WHERE user_id = ?i AND usergroup_id = ?i", $order_info['user_id'], $usergroup_id);
							}
						}
					}
				}
			}
		}
	}
}

function fn_recurring_billing_place_order($order_id, $action, &$order_status, $cart)
{
	$order_info = fn_get_order_info($order_id);
	$subscription_products = array();
	$products = $order_info['items'];

	fn_set_hook('order_products_post', $products);

	foreach ($products as $prod) {
		if (!empty($prod['extra']['recurring_subscription_id'])) {
			$order_ids = db_get_field("SELECT order_ids FROM ?:recurring_subscriptions WHERE subscription_id = ?i", $prod['extra']['recurring_subscription_id']);
			$_data = array (
				'order_ids' => $order_ids . ',' . $order_id,
				'last_timestamp' => $order_info['timestamp']
			);
			db_query("UPDATE ?:recurring_subscriptions SET ?u WHERE subscription_id = ?i", $_data, $prod['extra']['recurring_subscription_id']);
			break;
		} elseif (!empty($prod['extra']['recurring_plan_id'])) {
			$subscription_products[$prod['extra']['recurring_plan_id']][$prod['extra']['recurring_duration']][$prod['product_id']] = $prod['subtotal'];
		}
	}
	if (Registry::get('addons.recurring_billing.rb_initial_order_status') != '' && (!empty($_data) || !empty($subscription_products)) && (empty($cart['order_id']) || $action != 'save') && !empty($cart['recurring_subscription_id'])) {
		$order_status = Registry::get('addons.recurring_billing.rb_initial_order_status');
	}
	if (!empty($subscription_products)) {
		foreach ($subscription_products as $plan_id => $duration_group) {
			foreach ($duration_group as $duration => $products) {
				$price = 0;
				foreach ($products as $item) {
					$price += $item;
				}
				$data = array (
					'plan_id' => $plan_id,
					'order_id' => $order_id,
					'order_ids' => $order_id,
					'user_id' => $order_info['user_id'],
					'firstname' => $order_info['firstname'],
					'lastname' => $order_info['lastname'],
					'email' => $order_info['email'],
					'timestamp' => $order_info['timestamp'],
					'last_timestamp' => $order_info['timestamp'],
					'end_timestamp' => fn_get_period_date($order_info['timestamp'], $duration),
					'product_ids' => implode(',', array_keys($products)),
					'price' => $price,
					'duration' => $duration,
					'orig_duration' => $duration
				);
				$subscription_id = db_query("INSERT INTO ?:recurring_subscriptions ?e", $data);
				fn_change_subscription_dates($subscription_id, $plan_id, $order_info['timestamp'], $duration);
			}
		}
	}
}

function fn_recurring_billing_get_status_data(&$data, $status, $type, $object_id, $lang_code)
{
	if ($type == STATUSES_ORDER && !empty($object_id)) {
		$subscriptions = db_get_array("SELECT subscription_id FROM ?:recurring_subscriptions WHERE FIND_IN_SET(?i, order_ids)", $object_id);
		if (!empty($subscriptions)) {
			$addon_settings = Registry::get('addons.recurring_billing');

			if (!empty($addon_settings['rb_order_status_' . $status . '_email_subject'])) {
				$data['email_subj'] = $addon_settings['rb_order_status_' . $status . '_email_subject'];
			}

			if (!empty($addon_settings['rb_order_status_' . $status . '_email_header'])) {
				$data['email_header'] = $addon_settings['rb_order_status_' . $status . '_email_header'];
			}
		}
	}
}

function fn_recurring_billing_buy_together_pre_add_to_cart(&$product_data, &$cart, $auth, $update)
{
	fn_recurring_billing_pre_add_to_cart($product_data, $cart, $auth, $update);
}

function fn_recurring_billing_buy_together_restricted_product($product_id, $auth, $is_restricted, $show_notification)
{
	if ($is_restricted) {
		return true;
	}
	
	$plan = db_get_row("SELECT plan_id, duration FROM ?:recurring_plans WHERE status = 'A' AND FIND_IN_SET(?i, product_ids)", $product_id);
	
	if (!empty($plan)) {
		$is_restricted = true;
	}
	
	if ($is_restricted && $show_notification) {
		$product_name = fn_get_product_name($product_id);
		fn_set_notification('E', fn_get_lang_var('error'), str_replace('[product_name]', $product_name, fn_get_lang_var('buy_together_is_not_compatible_with_recurring_billing')));
	}
}

function fn_recurring_billing_pre_add_to_wishlist(&$product_data, &$wishlist, &$auth)
{
	fn_recurring_billing_pre_add_to_cart($product_data, $wishlist, $auth);
}

function fn_recurring_billing_get_additional_information(&$product, $info)
{
	if (!empty($info['recurring_plan_id'])) {
		$product['recurring_plan_id'] = $info['recurring_plan_id'];
	}
}

function fn_recurring_billing_get_products(&$params, &$fields, &$sortings, &$condition, &$join, &$sorting, &$group_by)
{
	if (!empty($params['holder_name']) && $params['holder_name'] == 'gift_certificates') {
		$condition .= " AND IF(rec_plan.allow_free_buy IS NULL, 1, rec_plan.allow_free_buy = 'Y')";
		$join .= " LEFT JOIN ?:recurring_plans as rec_plan ON FIND_IN_SET(products.product_id, rec_plan.product_ids)";
	}
}

function fn_recurring_billing_after_options_calculation($mode, $data)
{
	if ($mode == 'view' || $mode == 'options') {
		if (!empty($data['cart_id'])) {
			$cart = & $_SESSION['cart'];
			if (isset($cart['products'][$data['cart_id']])) {
				Registry::get('view')->assign('subscription_object_id', $data['cart_id']);
				Registry::get('view')->assign('return_mode', !empty($data['return_to']) && $data['return_to'] == 'cart' ? 'cart' : 'checkout');
			}
			if (isset($cart['products'][$data['cart_id']]['extra']['recurring_duration'])) {
				$extra = $cart['products'][$data['cart_id']]['extra'];
				Registry::get('view')->assign('alt_recurring_duration', $extra['recurring_duration']);
				if ($mode != 'options') {
					Registry::get('view')->assign('selected_plan_id', $extra['recurring_plan_id']);
				}
			}
		}
	}
}

?>