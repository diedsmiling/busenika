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
// $Id: currencies.php 10184 2010-07-23 11:11:24Z klerik $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//
	// Update currency
	//
	if ($mode == 'update') {

		// Update currency data
		if (is_array($_REQUEST['currencies'])) {
			$null_rate = array();
			foreach ($_REQUEST['currencies'] as $k => $v) {
				if (empty($v['currency_code'])) {
					continue;
				} else {
					$v['currency_code'] = strtoupper($v['currency_code']);
				}
				if (isset($v['coefficient']) && (empty($v['coefficient']) || floatval($v['coefficient']) <= 0)) {
					$null_rate[] = $k;
					continue;
				}
				$is_exists = db_get_field("SELECT COUNT(*) FROM ?:currencies WHERE currency_code = ?s AND currency_code != ?s", $v['currency_code'], $k);
				if (!empty($is_exists)) {
					$msg = fn_get_lang_var('error_currency_exists');
					$msg = str_replace('[code]', $v['currency_code'], $msg);
					fn_set_notification('E', fn_get_lang_var('error'), $msg);
					continue;
				}

				$__data = fn_check_table_fields($v, 'currencies');
				if (!empty($_REQUEST['is_primary_currency'])) {
					$_primary_key = $_REQUEST['is_primary_currency'];

					db_query("UPDATE ?:currencies SET is_primary = 'N' WHERE is_primary = 'Y'");
				} else {
					$_primary_key = db_get_field("SELECT currency_code FROM ?:currencies WHERE is_primary = 'Y'");
				}
				$__data['is_primary'] = $_primary_key == $k ? 'Y' : 'N';
				$__data['coefficient'] = $_primary_key == $k ? '1' : $__data['coefficient'];
				db_query("UPDATE ?:currencies SET ?u WHERE currency_code = ?s", $__data, $k);
				db_query("UPDATE ?:currency_descriptions SET currency_code = ?s WHERE currency_code = ?s", $v['currency_code'], $k);
				db_query('UPDATE ?:currency_descriptions SET ?u WHERE currency_code = ?s AND lang_code = ?s', $_REQUEST['currency_description'][$k], $k, DESCR_SL);
			}
			if (!empty($null_rate)) {
				$currencies_name = db_get_fields("SELECT description FROM ?:currency_descriptions WHERE currency_code IN (?a) AND lang_code = ?s", $null_rate, DESCR_SL);
				$msg = fn_get_lang_var('currency_rate_greater_than_null_for');
				foreach ($currencies_name as $v) {
					$msg .= '<br />' . $v;
				}
				fn_set_notification('W', fn_get_lang_var('warning'), $msg);
			}
		}
	}
	//
	// Delete currency
	//
	if ($mode == 'delete') {

		// Delete selected currency
		if (!empty($_REQUEST['currency_codes'])) {
			foreach ($_REQUEST['currency_codes'] as $v) {
				// If user change primary currency and trying to delete cur that was primary earlier we should update prim.
				if ($v == CART_PRIMARY_CURRENCY) {
					db_query("UPDATE ?:currencies SET is_primary = 'Y' WHERE currency_code = ?s", $_REQUEST['is_primary_currency']);
				}
				// \end
				db_query("DELETE FROM ?:currencies WHERE currency_code = ?s", $v);
				db_query("DELETE FROM ?:currency_descriptions WHERE currency_code = ?s", $v);
			}
		}
	}

	//
	// Add currency
	//
	if ($mode == 'add_currency') {

		if (is_array($_REQUEST['currencies'])) {
			foreach ($_REQUEST['currencies'] as $k => $v) {
				if (empty($v['currency_code'])) {
					continue;
				} else {
					$v['currency_code'] = strtoupper($v['currency_code']);
				}
				if (empty($v['coefficient']) || floatval($v['coefficient']) <= 0) {
					fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('currency_rate_greater_than_null'));
					continue;
				}
				$is_exists = db_get_field("SELECT COUNT(*) FROM ?:currencies WHERE currency_code = ?s", $v['currency_code']);
				if (!empty($is_exists)) {
					$msg = fn_get_lang_var('error_currency_exists');
					$msg = str_replace('[code]', $v['currency_code'], $msg);
					fn_set_notification('E', fn_get_lang_var('error'), $msg);
					continue;
				}
				$__data = fn_check_table_fields($v, 'currencies');
				db_query("INSERT INTO ?:currencies ?e", $__data);
				fn_create_description('currency_descriptions', "currency_code", $v['currency_code'], $_REQUEST['currency_description'][$k]);
			}
		}
	}
	return array(CONTROLLER_STATUS_OK, "currencies.manage");
}

// ---------------------- GET routines ---------------------------------------

if ($mode == 'manage') {

	$currencies = db_get_array("SELECT a.*, b.description FROM ?:currencies as a LEFT JOIN ?:currency_descriptions as b ON a.currency_code = b.currency_code AND lang_code = ?s ORDER BY position", DESCR_SL);

	$view->assign('currencies_data', $currencies);
} elseif ($mode == 'delete') {

	if (!empty($_REQUEST['currency_code'])) {
		if ($_REQUEST['currency_code'] != CART_PRIMARY_CURRENCY) {
			db_query("DELETE FROM ?:currencies WHERE currency_code = ?s", $_REQUEST['currency_code']);
			db_query("DELETE FROM ?:currency_descriptions WHERE currency_code = ?s", $_REQUEST['currency_code']);
			fn_set_notification('N', fn_get_lang_var('congratulations'), fn_get_lang_var('currency_deleted'));
		} else {
			fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('base_currency_not_deleted'));
		}
	}

	return array(CONTROLLER_STATUS_REDIRECT, "currencies.manage");
} elseif ($mode == 'update') {

	if (!empty($_REQUEST['currency_code'])) {
		$currency = db_get_row("SELECT a.*, b.description FROM ?:currencies as a LEFT JOIN ?:currency_descriptions as b ON a.currency_code = b.currency_code AND lang_code = ?s WHERE a.currency_code = ?s", DESCR_SL, $_REQUEST['currency_code']);

		$view->assign('currency', $currency);
	}
}

?>