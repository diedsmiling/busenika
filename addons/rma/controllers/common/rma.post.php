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
// $Id: rma.post.php 10494 2010-08-23 14:17:09Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}


/* POST data processing */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($mode == 'add_return') {

		if (!empty($_REQUEST['returns'])) {
			$order_id = intval($_REQUEST['order_id']);
			$oder_lang_code = db_get_field("SELECT lang_code FROM ?:orders WHERE order_id = ?i", $order_id);
			$returns = (array) $_REQUEST['returns'];
			$user_id = intval($_REQUEST['user_id']);
			$action = $_REQUEST['action'];
			$comment = $_REQUEST['comment'];

			$total_amount = 0;
			foreach ($returns as $k => $v) {
				if (isset($v['chosen']) && $v['chosen'] == 'Y') {
					$total_amount += $v['amount'];
				}
			}

			$_data = array(
				'order_id' => $order_id,
				'user_id' => $user_id,
				'action' => $action,
				'timestamp' => TIME,
				'status' => RMA_DEFAULT_STATUS,
				'total_amount' => $total_amount,
				'comment' => $comment
			);
			$return_id = db_query('INSERT INTO ?:rma_returns ?e', $_data);

			$order_items = db_get_hash_array("SELECT item_id, order_id, extra, price, amount FROM ?:order_details WHERE order_id = ?i", 'item_id', $order_id);
			foreach ($returns as $item_id => $v) {
				if (isset($v['chosen']) && $v['chosen'] == 'Y') {
					if (true == fn_rma_declined_product_correction($order_id, $k, $v['available_amount'], $v['amount'])) {
						$_item = $order_items[$item_id];
						$extra = @unserialize($_item['extra']);
						$_data = array (
							'return_id' => $return_id,
							'item_id' => $item_id,
							'product_id' => $v['product_id'],
							'reason' => $v['reason'],
							'amount' => $v['amount'],
							'product_options' => !empty($extra['product_options_value']) ? serialize($extra['product_options_value']) : '',
							'price' => fn_format_price((((!isset($extra['exclude_from_calculate'])) ? $_item['price'] : 0) * $_item['amount']) / $_item['amount']),
							'product' => !empty($extra['product']) ? $extra['product'] : fn_get_product_name($v['product_id'], $oder_lang_code)
						);

						db_query('INSERT INTO ?:rma_return_products ?e', $_data);

						if (!isset($extra['returns'])) {
							$extra['returns'] = array();
						}
						$extra['returns'][$return_id] = array(
							'amount' => $v['amount'],
							'status' => RMA_DEFAULT_STATUS
						);
						db_query('UPDATE ?:order_details SET ?u WHERE item_id = ?i AND order_id = ?i', array('extra' => serialize($extra)), $item_id, $order_id);
					}
				}
			}

			//Send mail
			$return_info = fn_get_return_info($return_id);
			$order_info = fn_get_order_info($order_id);
			fn_send_return_mail($return_info, $order_info, array('C' => true));
		}

		return array(CONTROLLER_STATUS_OK, "rma.details?return_id=$return_id");
	}
}

if (empty($auth['user_id']) && !isset($auth['order_ids']) && AREA == 'C') {
	return array(CONTROLLER_STATUS_REDIRECT, "auth.login_form?return_url=" . urlencode(Registry::get('config.current_url')));
}

if ($mode == 'details' && !empty($_REQUEST['return_id'])) {
	$return_id = intval($_REQUEST['return_id']);

	// [Breadcrumbs]
	if (AREA != 'A') {
		fn_add_breadcrumb(fn_get_lang_var('return_requests'), "rma.returns");
		fn_add_breadcrumb(fn_get_lang_var('return_info'));
	} else {
		fn_add_breadcrumb(fn_get_lang_var('return_requests'), "rma.returns.reset_view");
		fn_add_breadcrumb(fn_get_lang_var('search_results'), "rma.returns.last_view");
	}
	// [/Breadcrumbs]


	Registry::set('navigation.tabs', array (
		'return_products' => array (
			'title' => fn_get_lang_var('return_products_information'),
			'js' => true
		),
		'declined_products' => array (
			'title' => fn_get_lang_var('declined_products_information'),
			'js' => true
		),
	));

	$return_info = fn_get_return_info($return_id);
	if (AREA == 'A') {
		Registry::set('navigation.tabs.comments', array (
			'title' => fn_get_lang_var('comments'),
			'js' => true
		));
		Registry::set('navigation.tabs.actions', array (
			'title' => fn_get_lang_var('actions'),
			'js' => true
		));

		$view->assign('is_refund', fn_is_refund_action($return_info['action']));
		$view->assign('order_info', fn_get_order_info($return_info['order_id']));
	}

	$view->assign('reasons', fn_get_rma_properties( RMA_REASON ));
	$view->assign('actions', fn_get_rma_properties( RMA_ACTION ));
	$view->assign('return_info', $return_info);

} elseif ($mode == 'print_slip' && !empty($_REQUEST['return_id'])) {
	$return_id = intval($_REQUEST['return_id']);
	$return_info = fn_get_return_info($return_id);
	$order_info = fn_get_order_info($return_info['order_id']);

	if (empty($return_info) || empty($order_info)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	$view_mail->assign('reasons', fn_get_rma_properties( RMA_REASON ));
	$view_mail->assign('actions', fn_get_rma_properties( RMA_ACTION ));
	$view_mail->assign('return_info', $return_info);
	$view_mail->assign('order_info', $order_info);

	$view_mail->display('addons/rma/print_slip.tpl');
	exit;

} elseif ($mode == 'returns') {

	// [Breadcrumbs]
	if (AREA != 'A') {
		fn_add_breadcrumb(fn_get_lang_var('return_requests'));
	}
	// [/Breadcrumbs]

	$params = $_REQUEST;
	if (AREA == 'C') {
		$params['user_id'] = $auth['user_id'];
		if (!empty($auth['order_ids'])) {
			$params['order_ids'] = $auth['order_ids'];
		}
	}

	list($return_requests, $search) = fn_get_rma_returns($params);
	$view->assign('return_requests', $return_requests);
	$view->assign('search', $search);

	fn_rma_generate_sections('requests');

	$view->assign('actions', fn_get_rma_properties(RMA_ACTION));

} elseif ($mode == 'create_return' && !empty($_REQUEST['order_id'])) {
	$order_id = intval($_REQUEST['order_id']);

	// [Breadcrumbs]
	fn_add_breadcrumb(fn_get_lang_var('order').' #'.$order_id, "orders.details?order_id=$order_id");
	if (AREA != 'A') {
		fn_add_breadcrumb(fn_get_lang_var('return_registration'));
	}
	// [/Breadcrumbs]

	$order_info = fn_get_order_info($order_id);
	$order_returnable_products = fn_get_order_returnable_products($order_info['items'], $order_info['products_delivery_date']);
	$order_info['items'] = $order_returnable_products['items'];

	if (!isset($order_info['allow_return'])) {
		return array(CONTROLLER_STATUS_DENIED);
	}

	$view->assign('order_info', $order_info);
	$view->assign('reasons', fn_get_rma_properties( RMA_REASON ));
	$view->assign('actions', fn_get_rma_properties( RMA_ACTION ));
}

function fn_get_rma_returns($params)
{
	// Init filter
	$params = fn_init_view('rma', $params);

	// Set default values to input params
	$params['page'] = empty($params['page']) ? 1 : $params['page'];

	// Define fields that should be retrieved
	$fields = array (
		'DISTINCT ?:rma_returns.return_id',
		'?:rma_returns.order_id',
		'?:rma_returns.timestamp',
		'?:rma_returns.status',
		'?:rma_returns.total_amount',
		'?:rma_property_descriptions.property AS action',
		'?:users.firstname',
		'?:users.lastname'
	);

	// Define sort fields
	$sortings = array (
		'return_id' => "?:rma_returns.return_id",
		'timestamp' => "?:rma_returns.timestamp",
		'order_id' => "?:rma_returns.order_id",
		'status' => "?:rma_returns.status",
		'amount' => "?:rma_returns.total_amount",
		'action' => "?:rma_returns.action",
		'customer' => "?:users.lastname"
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		$params['sort_order'] = 'desc';
	}

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = 'timestamp';
	}

	$sort = $sortings[$params['sort_by']] . " " . $directions[$params['sort_order']];

	// Reverse sorting (for usage in view)
	$params['sort_order'] = $params['sort_order'] == 'asc' ? 'desc' : 'asc';

	$join = $condition = $group = '';

	if (isset($params['cname']) && fn_string_no_empty($params['cname'])) {
		$arr = fn_explode(' ', $params['cname']);
		foreach ($arr as $k => $v) {
			if (!fn_string_no_empty($v)) {
				unset($arr[$k]);
			}
		}
		if (sizeof($arr) == 2) {
			$condition .= db_quote(" AND ?:users.firstname LIKE ?l AND ?:users.lastname LIKE ?l", "%".array_shift($arr)."%", "%".array_shift($arr)."%");
		} else {
			$condition .= db_quote(" AND (?:users.firstname LIKE ?l OR ?:users.lastname LIKE ?l)", "%".trim($params['cname'])."%", "%".trim($params['cname'])."%");
		}
	}

	if (isset($params['email']) && fn_string_no_empty($params['email'])) {
		$condition .= db_quote(" AND ?:users.email LIKE ?l", "%".trim($params['email'])."%");
	}

	if (isset($params['rma_amount_from']) && fn_is_numeric($params['rma_amount_from'])) {
		$condition .= db_quote("AND ?:rma_returns.total_amount >= ?d", $params['rma_amount_from']);
	}

	if (isset($params['rma_amount_to']) && fn_is_numeric($params['rma_amount_to'])) {
		$condition .= db_quote("AND ?:rma_returns.total_amount <= ?d", $params['rma_amount_to']);
	}

	if (!empty($params['action'])) {
		$condition .= db_quote(" AND ?:rma_returns.action = ?s", $params['action']);
	}

	if (!empty($params['return_id'])) {
		$condition .= db_quote(" AND ?:rma_returns.return_id = ?i", $params['return_id']);
	}

	if (!empty($params['request_status'])) {
		$condition .= db_quote(" AND ?:rma_returns.status IN (?a)", $params['request_status']);
	}

	if (!empty($params['period']) && $params['period'] != 'A') {
		list($params['time_from'], $params['time_to']) = fn_create_periods($params);
		$condition .= db_quote(" AND (?:rma_returns.timestamp >= ?i AND ?:rma_returns.timestamp <= ?i)", $params['time_from'], $params['time_to']);
	}

	if (!empty($params['order_id'])) {
		$condition .= db_quote(" AND ?:rma_returns.order_id = ?i", $params['order_id']);
	}

	if (!empty($params['user_id'])) {
		$condition .= db_quote(" AND ?:rma_returns.user_id = ?i", $params['user_id']);
	}

	if (!empty($params['order_status'])) {
		$condition .= db_quote(" AND ?:orders.status IN (?a)", $params['order_status']);
	}

	if (!empty($params['p_ids']) || !empty($params['product_view_id'])) {
		$arr = (strpos($params['p_ids'], ',') !== false || !is_array($params['p_ids'])) ? explode(',', $params['p_ids']) : $params['p_ids'];
		if (empty($params['product_view_id'])) {
			$condition .= db_quote(" AND ?:order_details.product_id IN (?n)", $arr);
		} else {
			$condition .= db_quote(" AND ?:order_details.product_id IN (?n)", db_get_fields(fn_get_products(array('view_id' => $params['product_view_id'], 'get_query' => true))));
		}

		$join .= " LEFT JOIN ?:order_details ON ?:order_details.order_id = ?:orders.order_id";
		$group .=  db_quote(" GROUP BY ?:rma_returns.return_id HAVING COUNT(?:orders.order_id) >= ?i", count($arr));
	}

	$items_per_page = Registry::get('settings.Appearance.' . (AREA == 'A') ? 'admin_elements_per_page' : 'elements_per_page');

	$total = db_get_field("SELECT COUNT(DISTINCT ?:rma_returns.return_id) FROM ?:rma_returns LEFT JOIN ?:rma_return_products ON ?:rma_return_products.return_id = ?:rma_returns.return_id LEFT JOIN ?:rma_property_descriptions ON ?:rma_property_descriptions.property_id = ?:rma_returns.action LEFT JOIN ?:users ON ?:rma_returns.user_id = ?:users.user_id LEFT JOIN ?:orders ON ?:rma_returns.order_id = ?:orders.order_id $join WHERE 1 $condition $group");

	$limit = fn_paginate($params['page'], $total, $items_per_page); // FIXME

	$return_requests = db_get_array("SELECT " . implode(', ', $fields) . " FROM ?:rma_returns LEFT JOIN ?:rma_return_products ON ?:rma_return_products.return_id = ?:rma_returns.return_id LEFT JOIN ?:rma_property_descriptions ON (?:rma_property_descriptions.property_id = ?:rma_returns.action AND ?:rma_property_descriptions.lang_code = ?s) LEFT JOIN ?:users ON ?:rma_returns.user_id = ?:users.user_id LEFT JOIN ?:orders ON ?:rma_returns.order_id = ?:orders.order_id $join WHERE 1 $condition $group ORDER BY $sort $limit", (AREA == 'C') ? CART_LANGUAGE : DESCR_SL);

	return array($return_requests, $params);
}


?>