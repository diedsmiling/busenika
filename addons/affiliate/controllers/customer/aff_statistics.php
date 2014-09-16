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
// $Id: aff_statistics.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($mode == 'commissions') {
	$payout_types = Registry::get('payout_types');

	$view->assign('payout_types', $payout_types);
	$payout_options = array();
	foreach ($payout_types as $payout_id => $payout_data) {
		$payout_options[$payout_id] = fn_get_lang_var($payout_data['title']);
	}
	$view->assign('payout_options', $payout_options);

	$status_options = array(
		'A' => fn_get_lang_var('approved'),
		'N' => fn_get_lang_var('awaiting_approval'),
		'P' => fn_get_lang_var('paidup'),
	);
	$view->assign('status_options', $status_options);

	$_SESSION['statistic_conditions'] = empty($_SESSION['statistic_conditions']) ? array() : $_SESSION['statistic_conditions'];
	$statistic_conditions = & $_SESSION['statistic_conditions'];

	$_SESSION['statistic_search_data'] = empty($_SESSION['statistic_search_data']) ? array() : $_SESSION['statistic_search_data'];
	$statistic_search_data = & $_SESSION['statistic_search_data'];

	if ($action == 'reset_search' || empty($statistic_conditions)) {
		$statistic_conditions = " (amount != 0) ";
		$statistic_search_data = array();
	}

	$statistic_conditions = '1';
	if (empty($_REQUEST['statistic_search'])) {
		$statistic_search = array();
	} else {
		$statistic_search = $_REQUEST['statistic_search'];
	}

	$statistic_search_data = (empty($search_type) || $search_type != 'add') ? $statistic_search : fn_array_merge($statistic_search_data, $statistic_search);

	if (AREA == 'C') {
		$statistic_conditions .= db_quote(" AND (partner_id = ?i)", $auth['user_id']);
	} elseif (!empty($statistic_search_data['partner_id'])) {
		$statistic_conditions .= db_quote(" AND (partner_id = ?i)", $statistic_search_data['partner_id']);
	}
	if (!empty($_REQUEST['period']) && $_REQUEST['period'] != 'A') {
		list($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
		$statistic_search_data['period'] = $_REQUEST['period'];
		if ($_REQUEST['period'] == 'C') {
			$statistic_search_data['start_date'] = $_REQUEST['time_from'];
			$statistic_search_data['end_date'] = $_REQUEST['time_to'];
		}

		$statistic_conditions .= db_quote(" AND (date >= ?i AND date <= ?i)", $_REQUEST['time_from'], $_REQUEST['time_to']); // FIXME
	} else {
		$statistic_search_data['period'] = 'A';
	}

	if (!empty($statistic_search_data['plan_id'])) {
		$statistic_conditions .= db_quote(" AND (actions.plan_id = ?i) ", $statistic_search_data['plan_id']);
	}
	if (!empty($statistic_search_data['payout_id'])) {
		$_conditions = '';
		foreach ($statistic_search_data['payout_id'] as $_act) {
			$_conditions .= (empty($_conditions) ? '' : 'OR') . db_quote(" (action = ?s) ", $_act);
		}
		$statistic_conditions .= " AND ($_conditions) ";
	}
	if (!empty($statistic_search_data['status'])) {
		$_conditions = '';
		foreach ($statistic_search_data['status'] as $_status) {
			$_conditions .= empty($_conditions) ? '' : 'OR';
			if ($_status == 'P') {
				$_conditions .= " (payout_id != 0) ";
			} elseif ($_status == 'A') {
				$_conditions .= " (payout_id = 0 AND actions.approved = 'Y') ";
			} else {
				$_conditions .= " (actions.approved = 'N' AND payout_id = 0) ";
			}
		}
		$statistic_conditions .= " AND ($_conditions) ";
	}
	if (!empty($statistic_search_data['zero_actions']) && $statistic_search_data['zero_actions'] == 'Y' && AREA != 'C') {
		$statistic_conditions .= " AND (amount = 0) ";
	} elseif (empty($statistic_search_data['zero_actions']) || AREA == 'C') {
		$statistic_conditions .= " AND (amount != 0) ";
	}
	$statistic_search_data['amount_from'] = empty($statistic_search_data['amount_from']) ? 0 : floatval($statistic_search_data['amount_from']);
	if (!empty($statistic_search_data['amount_from'])) {
		$statistic_conditions .= db_quote(" AND (amount >= ?d) ", fn_convert_price($statistic_search_data['amount_from']));
	}
	$statistic_search_data['amount_to'] = empty($statistic_search_data['amount_to']) ? 0 : floatval($statistic_search_data['amount_to']);
	if (!empty($statistic_search_data['amount_to'])) {
		$statistic_conditions .= db_quote(" AND (amount <= ?d) ", fn_convert_price($statistic_search_data['amount_to']));
	}

	$view->assign('statistic_search', $statistic_search_data);

	$general_stats = db_get_hash_array("SELECT action, COUNT(action) as count, SUM(amount) as sum, AVG(amount) as avg, COUNT(distinct partner_id) as partners FROM ?:aff_partner_actions as actions WHERE $statistic_conditions GROUP BY action", 'action');

	$general_stats['total'] = db_get_row("SELECT 'total' as action, COUNT(action) as count, SUM(amount) as sum, AVG(amount) as avg, COUNT(distinct partner_id) as partners FROM ?:aff_partner_actions as actions WHERE $statistic_conditions");

	$view->assign('general_stats', $general_stats);
	$additional_stats = array();
	$additional_stats['click_vs_show'] = empty($general_stats['show']['count']) ? '---' : (empty($general_stats['click']['count']) ? '0' : round($general_stats['click']['count'] / $general_stats['show']['count'] * 100, 1) . '% (' . intval($general_stats['click']['count']) . '/' . intval($general_stats['show']['count']) . ')');
	$additional_stats['sale_vs_click'] = empty($general_stats['click']['count']) ? '---' : (empty($general_stats['sale']['count']) ? '0' : round($general_stats['sale']['count'] / $general_stats['click']['count'] * 100, 1) . '% (' . intval($general_stats['sale']['count']) . '/' . intval($general_stats['click']['count']) . ')');
	$view->assign('additional_stats', $additional_stats);

	$list_plans = fn_get_affiliate_plans_list();
	$view->assign('list_plans', $list_plans);

	$view->assign('affiliate_plan', fn_get_affiliate_plan_data_by_partner_id($auth['user_id']));

	$sort_order = empty($_REQUEST['sort_order']) ? 'desc' : $_REQUEST['sort_order'];
	$sort_by = empty($_REQUEST['sort_by']) ? 'date' : $_REQUEST['sort_by'];

	$list_stats = fn_get_affiliate_actions($_SESSION['statistic_conditions'], array('sort_order' => $sort_order, 'sort_by' => $sort_by), true, @$_REQUEST['page']);

	$view->assign('sort_order', $sort_order == 'asc' ? 'desc' : 'asc');
	$view->assign('sort_by', $sort_by);

	if (!empty($list_stats)) {
		$view->assign('list_stats', $list_stats);
	}

	$order_status_descr = fn_get_statuses(STATUSES_ORDER, true, true, true);
	$view->assign('order_status_descr', $order_status_descr);
}

?>