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
// $Id: payouts.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$sort_order = empty($_REQUEST['sort_order']) ? '' : $_REQUEST['sort_order'];
$sort_by = empty($_REQUEST['sort_by']) ? '' : $_REQUEST['sort_by'];

if ($mode == 'list') {

	$sortings = array (
		'username' => "user_login",
		'partner' => "CONCAT(lastname, firstname)",
		'amount' => "?:affiliate_payouts.amount",
		'date' => "?:affiliate_payouts.date",
		'status' => "?:affiliate_payouts.status",
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	if (empty($sort_order) || empty($directions[$sort_order])) {
		$sort_order = 'desc';
	}

	if (empty($sort_by) || empty($sortings[$sort_by])) {
		$sort_by = 'date';
	}

	$view->assign('sort_order', (($sort_order == 'asc') ? 'desc' : 'asc'));
	$view->assign('sort_by', $sort_by);

	$sorting = $sortings[$sort_by] . " " . $directions[$sort_order];

	if (!empty($_REQUEST['payout_search'])) {
		$payout_search = $_REQUEST['payout_search'];
		$payout_search_data = $payout_search;
		$payout_search_condition = '1';
		if (!empty($_REQUEST['period']) && $_REQUEST['period'] != 'A') {
			list($time_from, $time_to) = fn_create_periods($_REQUEST);

			$payout_search_data['period'] = $_REQUEST['period'];
			$payout_search_data['time_from'] = $time_from;
			$payout_search_data['time_to'] = $time_to;

			$payout_search_condition .= db_quote(" AND (?:affiliate_payouts.date >= ?i AND ?:affiliate_payouts.date <= ?i)", $time_from, $time_to);
		} else {
			$payout_search_data['period'] = 'A';
		}

		if (!empty($payout_search['status'])) {
			$payout_search_condition .= db_quote(" AND ?:affiliate_payouts.status = ?s ", $payout_search['status']);
		}
		$payout_search_data['amount']['from'] = floatval(@$payout_search['amount']['from']);
		if (!empty($payout_search_data['amount']['from'])) {
			$payout_search_condition .= db_quote(" AND ?:affiliate_payouts.amount >= ?d ", fn_convert_price($payout_search_data['amount']['from']));
		} else {
			$payout_search_data['amount']['from'] = '';
		}
		$payout_search_data['amount']['to'] = floatval(@$payout_search['amount']['to']);
		if (!empty($payout_search_data['amount']['to'])) {
			$payout_search_condition .= db_quote(" AND ?:affiliate_payouts.amount <= ?d ", fn_convert_price($payout_search_data['amount']['to']));
		} else {
			$payout_search_data['amount']['to'] = '';
		}
	}

	if (empty($payout_search_data)) {
		$payout_search_data = array();
	}

	$view->assign('payout_search', $payout_search_data);

	if (empty($payout_search_condition)) {
		$payout_search_condition = " 1 ";
	}

	$payouts_cnt = db_get_field("SELECT COUNT(*) FROM ?:affiliate_payouts LEFT JOIN ?:users ON ?:affiliate_payouts.partner_id = ?:users.user_id WHERE ?p AND user_id = ?i", 'payout_id', $payout_search_condition, $auth['user_id']);

	$limit = fn_paginate(@$_REQUEST['page'], $payouts_cnt, Registry::get('settings.Appearance.admin_elements_per_page')); // FIXME: page

	$payouts = db_get_hash_array("SELECT ?:affiliate_payouts.*, ?:users.user_login, ?:users.firstname, ?:users.lastname FROM ?:affiliate_payouts LEFT JOIN ?:users ON ?:affiliate_payouts.partner_id=?:users.user_id WHERE ?p AND ?:users.user_id = ?i ORDER BY $sorting $limit", 'payout_id', $payout_search_condition, $auth['user_id']);

	$view->assign('payouts', $payouts);

} elseif ($mode == 'update') {

	if (empty($_REQUEST['payout_id'])) {

		return array(CONTROLLER_STATUS_NO_PAGE);

	} else {

		$payout_data = db_get_row("SELECT * FROM ?:affiliate_payouts WHERE payout_id = ?i", $_REQUEST['payout_id']);

		if (empty($payout_data)) {
			return array(CONTROLLER_STATUS_NO_PAGE);
		} else {
			if (!empty($payout_data['partner_id'])) {
				$payout_data['partner'] = fn_get_partner_data($payout_data['partner_id']);
			}

			if (!empty($payout_data['partner']['plan_id'])) {
				$payout_data['plan'] = fn_get_affiliate_plan_data($payout_data['partner']['plan_id']);
			}

			$payout_data['actions'] = fn_get_affiliate_actions($_REQUEST);
			$payout_data['date_range']['min'] = db_get_field("SELECT MIN(date) FROM ?:aff_partner_actions WHERE payout_id = ?i", $_REQUEST['payout_id']);
			$payout_data['date_range']['max'] = db_get_field("SELECT MAX(date) FROM ?:aff_partner_actions WHERE payout_id = ?i", $_REQUEST['payout_id']);

			// [Breadcrumbs]
			fn_add_breadcrumb(fn_get_lang_var('payouts'), "payouts.manage");
			// [/Breadcrumbs]

			$view->assign('affiliate_plan', fn_get_affiliate_plan_data_by_partner_id($auth['user_id']));
			$view->assign('payouts', array($payout_data['partner_id'] => $payout_data));
		}

		$view->assign('sort_order', ((empty($sort_order) || $sort_order == 'asc') ? 'desc' : 'asc'));
		$view->assign('sort_by', (empty($sort_by) ? 'date' : $sort_by));
	}
}

/** /Body **/
?>