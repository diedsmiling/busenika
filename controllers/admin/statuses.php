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
// $Id: statuses.php 10581 2010-09-02 12:46:41Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	fn_trusted_vars('status_data');

	if ($mode == 'update') {
		fn_update_status($_REQUEST['status'], $_REQUEST['status_data'], $_REQUEST['type']);
	}

	return array(CONTROLLER_STATUS_OK, "statuses.manage?type=$_REQUEST[type]");
}

if ($mode == 'update') {

	$status_data = db_get_row("SELECT ?:statuses.*, ?:status_descriptions.* FROM ?:statuses LEFT JOIN ?:status_descriptions ON ?:statuses.status = ?:status_descriptions.status AND ?:statuses.type = ?:status_descriptions.type AND ?:status_descriptions.lang_code = ?s WHERE ?:statuses.status = ?s AND ?:statuses.type = ?s ORDER BY ?:status_descriptions.description", DESCR_SL, $_REQUEST['status'], $_REQUEST['type']);

	$status_data['params'] = db_get_hash_single_array("SELECT param, value FROM ?:status_data WHERE status = ?s AND type = ?s", array('param', 'value'), $_REQUEST['status'], $_REQUEST['type']);

	$view->assign('status_data', $status_data);
	$view->assign('type', $_REQUEST['type']);
	$view->assign('status_params', fn_get_status_params_definition($_REQUEST['type']));

} elseif ($mode == 'delete') {
	if (!empty($_REQUEST['status'])) {
		$can_delete = db_get_field("SELECT status FROM ?:statuses WHERE status = ?s AND type = ?s AND is_default = 'N'", $_REQUEST['status'], $_REQUEST['type']);
		if (!empty($can_delete)) {
			db_query("DELETE FROM ?:statuses WHERE status = ?s AND type = ?s", $_REQUEST['status'], $_REQUEST['type']);
			db_query("DELETE FROM ?:status_descriptions WHERE status = ?s AND type = ?s", $_REQUEST['status'], $_REQUEST['type']);
			db_query("DELETE FROM ?:status_data WHERE status = ?s AND type = ?s", $_REQUEST['status'], $_REQUEST['type']);
			$count = db_get_field("SELECT COUNT(*) FROM ?:statuses");
			if (empty($count)) {
				$view->display('views/statuses/manage.tpl');
			}
		}
	}
	exit;

} elseif ($mode == 'manage') {

	$section_data = array();

	$statuses = db_get_hash_array("SELECT ?:statuses.*, ?:status_descriptions.* FROM ?:statuses LEFT JOIN ?:status_descriptions ON ?:statuses.status = ?:status_descriptions.status AND ?:statuses.type = ?:status_descriptions.type AND ?:status_descriptions.lang_code = ?s AND ?:statuses.type = ?s ORDER BY ?:status_descriptions.description", 'status', DESCR_SL, $_REQUEST['type']);

	$view->assign('statuses', $statuses);

	$type = !empty($_REQUEST['type']) ? $_REQUEST['type'] : STATUSES_ORDER;
	$view->assign('type', $type);
	$view->assign('status_params', fn_get_status_params_definition($type));

	// Orders only
	if ($type == STATUSES_ORDER) {
		$view->assign('title', fn_get_lang_var('order_statuses'));
	}
}

function fn_update_status($status, $status_data, $type, $lang_code = DESCR_SL)
{
	if (empty($status)) {
		$status_data['type'] = $type;
		db_query("INSERT INTO ?:statuses ?e", $status_data);
		$status = $status_data['status'];

		foreach ((array)Registry::get('languages') as $status_data['lang_code'] => $_v) {
			db_query('REPLACE INTO ?:status_descriptions ?e', $status_data);
		}
	} else {
		db_query("UPDATE ?:statuses SET ?u WHERE status = ?s AND type = ?s", $status_data, $status, $type);
		db_query('UPDATE ?:status_descriptions SET ?u WHERE status = ?s AND type = ?s AND lang_code = ?s', $status_data, $status, $type, $lang_code);
	}

	if (!empty($status_data['params'])) {
		foreach ((array)$status_data['params'] as $k => $v) {
			$_data = array(
				'status' => $status,
				'type' => $type,
				'param' => $k,
				'value' => $v
			);
			db_query("REPLACE INTO ?:status_data ?e", $_data);
		}
	}
}

function fn_get_status_params_definition($type)
{
	$status_params = array();

	if ($type == STATUSES_ORDER) {
		$status_params = array (
			'notify' => array (
				'type' => 'checkbox',
				'label' => 'notify_customer'
			),
			'notify_department' => array (
				'type' => 'checkbox',
				'label' => 'notify_orders_department'
			),
			'notify_supplier' => array (
				'type' => 'checkbox',
				'label' => 'notify_supplier'
			),
			'inventory' => array (
				'type' => 'select',
				'label' => 'inventory',
				'variants' => array (
					'I' => 'increase',
					'D' => 'decrease',
				),
			),
			'remove_cc_info' => array (
				'type' => 'checkbox',
				'label' => 'remove_cc_info'
			),
			'repay' => array (
				'type' => 'checkbox',
				'label' => 'pay_order_again'
			),
			'appearance_type' => array (
				'type' => 'select',
				'label' => 'invoice_credit_memo',
				'variants' => array (
					'D' => 'default',
					'I' => 'invoice',
					'C' => 'credit_memo',
					'O' => 'order'
				),
			),
		);
		if (Registry::get('settings.Suppliers.enable_suppliers') != 'Y') {
			unset($status_params['notify_supplier']);
		}
	}

	fn_set_hook('get_status_params_definition', $status_params, $type);

	return $status_params;
}

?>