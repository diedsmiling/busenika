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
// $Id: index.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

// Generate dashboard
if ($mode == 'index') {
	// Check for feedback request
	if (!defined('COMPANY_ID') && Registry::get('settings.send_feedback') < time() && Registry::get('settings.General.feedback_type') == 'auto') {
		fn_redirect('feedback.send?action=auto');
	}

	$latest_orders = db_get_array("SELECT order_id, timestamp, firstname, lastname, total, user_id, status FROM ?:orders WHERE 1 " . fn_get_company_condition() . " ORDER BY timestamp DESC LIMIT 5");

	// Collect orders information
	$today = getdate(TIME);
	$orders_stats = $product_stats = $users_stats = array();
	$orders_stats['daily_orders'] = db_get_hash_array("SELECT status, COUNT(*) as amount FROM ?:orders WHERE timestamp >= ?i AND timestamp <= ?i " . fn_get_company_condition() . " GROUP BY status", 'status', mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']), TIME);
	$orders_stats['daily_orders']['totals'] = db_get_row("SELECT SUM(IF(status = 'C' OR status = 'P', total, 0)) as total_paid, SUM(total) as total, COUNT(*) as amount FROM ?:orders WHERE timestamp >= ?i AND timestamp <= ?i " . fn_get_company_condition(), mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']), TIME);

	$wday = empty($today['wday']) ? "6" : (($today['wday'] == 1) ? "0" : $today['wday'] - 1);
	$wstart = getdate(strtotime("-$wday day"));
	$orders_stats['weekly_orders'] = db_get_hash_array("SELECT status, COUNT(*) as amount FROM ?:orders WHERE timestamp >= ?i AND timestamp <= ?i " . fn_get_company_condition() . " GROUP BY status", 'status', mktime(0, 0, 0, $wstart['mon'], $wstart['mday'], $wstart['year']), TIME);
	$orders_stats['weekly_orders']['totals'] = db_get_row("SELECT SUM(IF(status = 'C' OR status = 'P', total, 0)) as total_paid, SUM(total) as total, COUNT(*) as amount FROM ?:orders WHERE timestamp >= ?i AND timestamp <= ?i " . fn_get_company_condition(), mktime(0, 0, 0, $wstart['mon'], $wstart['mday'], $wstart['year']), TIME);

	$orders_stats['monthly_orders'] = db_get_hash_array("SELECT status, COUNT(*) as amount, SUM(total) as total FROM ?:orders WHERE timestamp >= ?i AND timestamp <= ?i " . fn_get_company_condition() . " GROUP BY status", 'status', mktime(0, 0, 0, $today['mon'], 1, $today['year']), TIME);
	$orders_stats['monthly_orders']['totals'] = db_get_row("SELECT SUM(IF(status = 'C' OR status = 'P', total, 0)) as total_paid, SUM(total) as total, COUNT(*) as amount FROM ?:orders WHERE timestamp >= ?i  AND timestamp <= ?i " . fn_get_company_condition(), mktime(0, 0, 0, $today['mon'], 1, $today['year']), TIME);

	$orders_stats['year_orders'] = db_get_hash_array("SELECT status, COUNT(*) as amount, SUM(total) as total FROM ?:orders WHERE timestamp >= ?i AND timestamp <= ?i " . fn_get_company_condition() . " GROUP BY status", 'status', mktime(0, 0, 0, 1, 1, $today['year']), TIME);
	$orders_stats['year_orders']['totals'] = db_get_row("SELECT SUM(IF(status = 'C' OR status = 'P', total, 0)) as total_paid, SUM(total) as total, COUNT(*) as amount FROM ?:orders WHERE timestamp >= ?i AND timestamp <= ?i" . fn_get_company_condition(), mktime(0, 0, 0, 1, 1, $today['year']), TIME);
	$order_statuses = fn_get_statuses(STATUSES_ORDER, true, true, true);

	$product_stats['total'] = db_get_field("SELECT COUNT(*) as amount FROM ?:products WHERE 1" . fn_get_company_condition());
	$product_stats['status'] = db_get_hash_single_array("SELECT status, COUNT(*) as amount FROM ?:products WHERE 1" . fn_get_company_condition() . " GROUP BY status", array('status', 'amount'));

	$product_stats['configurable'] = db_get_field("SELECT COUNT(*) FROM ?:products WHERE product_type = 'C'" . fn_get_company_condition());
	$product_stats['downloadable'] = db_get_field("SELECT COUNT(*) FROM ?:products WHERE is_edp = 'Y'" . fn_get_company_condition());
	$product_stats['free_shipping'] = db_get_field("SELECT COUNT(*) FROM ?:products WHERE free_shipping = 'Y'" . fn_get_company_condition());

	$stock = db_get_hash_single_array("SELECT COUNT(product_id) as quantity, IF(amount > 0, 'in', 'out') as c FROM ?:products WHERE tracking = 'B' " . fn_get_company_condition() . " GROUP BY c", array('c', 'quantity'));
	$stock_o = db_get_hash_single_array("SELECT COUNT(DISTINCT(?:product_options_inventory.product_id))  as quantity, IF(?:product_options_inventory.amount > 0, 'in', 'out') as c FROM ?:product_options_inventory LEFT JOIN ?:products ON ?:products.product_id = ?:product_options_inventory.product_id WHERE ?:products.tracking = 'O'" . fn_get_company_condition() . " GROUP BY c", array('c', 'quantity'));

	$product_stats['in_stock'] = (!empty($stock['in']) ? $stock['in'] : 0) + (!empty($stock_o['in']) ? $stock_o['in'] : 0);
 	$product_stats['out_of_stock'] = (!empty($stock['out']) ? $stock['out'] : 0) + (!empty($stock_o['out']) ? $stock_o['out'] : 0);

	$category_stats['total'] = db_get_field("SELECT COUNT(*) FROM ?:categories");
	$category_stats['status'] =  db_get_hash_single_array("SELECT status, COUNT(*) as amount FROM ?:categories GROUP BY status", array('status', 'amount'));

	if (!defined('COMPANY_ID')) {
		
		$users_stats['total'] = db_get_hash_single_array("SELECT user_type, COUNT(*) as total FROM ?:users GROUP BY user_type", array('user_type', 'total'));
		$users_stats['total_all'] = db_get_field("SELECT COUNT(*) FROM ?:users");
		$users_stats['not_approved'] = db_get_field("SELECT COUNT(*) FROM ?:users WHERE status = 'D'");
		$usergroups = fn_get_usergroups('F', DESCR_SL);
		$usergroups_type = db_get_hash_single_array("SELECT type, COUNT(*) as total FROM ?:usergroups GROUP BY type", array('type', 'total'));
		$users_stats['usergroup']['A'] = db_get_hash_single_array("SELECT ul.usergroup_id, COUNT(*) as amount FROM ?:users as a LEFT JOIN ?:usergroup_links as ul ON a.user_id = ul.user_id AND ul.status = 'A' LEFT JOIN ?:usergroups as b ON ul.usergroup_id = b.usergroup_id WHERE b.type = 'A' GROUP BY ul.usergroup_id", array('usergroup_id', 'amount'));
		$users_stats['usergroup']['C'] = db_get_hash_single_array("SELECT ul.usergroup_id, COUNT(*) as amount FROM ?:users as a LEFT JOIN ?:usergroup_links as ul ON a.user_id = ul.user_id AND ul.status = 'A' LEFT JOIN ?:usergroups as b ON ul.usergroup_id = b.usergroup_id WHERE b.type = 'C' GROUP BY ul.usergroup_id", array('usergroup_id', 'amount'));
		$users_stats['usergroup']['P'] = db_get_hash_single_array("SELECT ul.usergroup_id, COUNT(*) as amount FROM ?:users as a LEFT JOIN ?:usergroup_links as ul ON a.user_id = ul.user_id AND ul.status = 'A' LEFT JOIN ?:usergroups as b ON ul.usergroup_id = b.usergroup_id WHERE b.type = 'P' GROUP BY ul.usergroup_id", array('usergroup_id', 'amount'));
		$users_stats['not_members'] = db_get_hash_single_array("SELECT a.user_type, COUNT(DISTINCT(a.user_id)) as amount FROM ?:users as a LEFT JOIN ?:usergroup_links as ul ON a.user_id = ul.user_id AND ul.status = 'A' WHERE ul.user_id IS NULL GROUP BY a.user_type", array('user_type', 'amount'));

		$view->assign('usergroups', $usergroups);
		$view->assign('usergroups_type', $usergroups_type);
		$view->assign('users_stats', $users_stats);
	}
/* NULLED BY FLIPMODE! @ 2010/09/06 */
	if (!defined('HTTPS')) {
		$view->assign('stats', base64_decode('PHNwYW4gc3R5bGU9InZpc2liaWxpdHk6IGhpZGRlbjsgZGlzcGxheTogbm9uZTsiPkZMSVBNT0RFITwvc3Bhbj4='));
		/* 'PGltZyBzcmM9Imh0dHA6Ly93d3cuY3MtY2FydC5jb20vaW1hZ2VzL2JhY2tncm91bmQuZ2lmIiBoZWlnaHQ9IjEiIHdpZHRoPSIxIiBhbHQ9IiIgLz4='; */
	}

	$view->assign('orders_stats', $orders_stats);
	$view->assign('order_statuses', $order_statuses);
	$view->assign('product_stats', $product_stats);
	$view->assign('category_stats', $category_stats);
	$view->assign('latest_orders', $latest_orders);
}

?>