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
// $Id: taxes.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

$_REQUEST['tax_id'] = empty($_REQUEST['tax_id']) ? 0 : $_REQUEST['tax_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$suffix = '';

	//
	// Update taxes
	//
	if ($mode == 'm_update') {

		// Update taxes data
		if (!empty($_REQUEST['tax_data'])) {
			foreach ($_REQUEST['tax_data'] as $k => $v) {

				db_query("UPDATE ?:taxes SET ?u WHERE tax_id = ?i", $v, $k);
				db_query("UPDATE ?:tax_descriptions SET ?u WHERE tax_id = ?i AND lang_code = ?s", $v, $k, DESCR_SL);
			}
		}

		$suffix = '.manage';
	}

	//
	// Delete taxes
	//
	if ($mode == 'delete') {

		// Delete selected taxes
		if (!empty($_REQUEST['tax_ids'])) {
			fn_delete_taxes($_REQUEST['tax_ids']);
		}

		$suffix = '.manage';
	}

	//
	// Update selected tax data
	//
	if ($mode == 'update') {

		$tax_id = fn_update_tax($_REQUEST['tax_data'], $_REQUEST['tax_id'], DESCR_SL);

		$suffix = ".update?tax_id=$tax_id";
	}

	if ($mode == 'apply_selected_taxes') {
		if (!empty($_REQUEST['tax_ids'])) {
			foreach ($_REQUEST['tax_ids'] as $v) {
				db_query("UPDATE ?:products SET tax_ids = ?p", fn_add_to_set('?:products.tax_ids', $v));

				$msg = str_replace('[tax]', $_REQUEST['tax_data'][$v]['tax'], fn_get_lang_var('text_tax_applied'));
				fn_set_notification('N', fn_get_lang_var('notice'), $msg);
			}
		}

		$suffix = '.manage';
	}

	if ($mode == 'unset_selected_taxes') {
		if (!empty($_REQUEST['tax_ids'])) {
			foreach ($_REQUEST['tax_ids'] as $v) {
				db_query("UPDATE ?:products SET tax_ids = ?p", fn_remove_from_set('?:products.tax_ids', $v));

				$msg = str_replace('[tax]', $_REQUEST['tax_data'][$v]['tax'], fn_get_lang_var('text_tax_unset'));
				fn_set_notification('N', fn_get_lang_var('notice'), $msg);
			}
		}

		$suffix = '.manage';
	}

	return array(CONTROLLER_STATUS_OK, "taxes$suffix");
}

// ---------------------- GET routines ---------------------------------------


// Edit tax rates
if ($mode == 'update') {
	$tax = db_get_row("SELECT a.*, tax FROM ?:taxes as a LEFT JOIN ?:tax_descriptions as b ON b.tax_id = a.tax_id AND b.lang_code = ?s WHERE a.tax_id = ?i", DESCR_SL, $_REQUEST['tax_id']);
	if (empty($tax)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	$destinations = fn_get_destinations();
	
	Registry::set('navigation.tabs', array (
		'general' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'tax_rates' => array (
			'title' => fn_get_lang_var('tax_rates'),
			'js' => true
		),
	));

	fn_add_breadcrumb(fn_get_lang_var('taxes'),"taxes.manage");

	$view->assign('tax', $tax);
	$view->assign('rates',  db_get_hash_array("SELECT * FROM ?:tax_rates WHERE tax_id = ?i", 'destination_id', $_REQUEST['tax_id']));
	$view->assign('destinations', $destinations);

// Add tax
} elseif ($mode == 'add') {

	fn_add_breadcrumb(fn_get_lang_var('taxes'),"taxes.manage");
	
	Registry::set('navigation.tabs', array (
		'general' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'tax_rates' => array (
			'title' => fn_get_lang_var('tax_rates'),
			'js' => true
		),
	));

	$view->assign('destinations', fn_get_destinations());

// Edit taxes
} elseif ($mode == 'manage') {

	$view->assign('taxes', fn_get_taxes(DESCR_SL));

} elseif ($mode == 'delete') {

	if (!empty($_REQUEST['tax_id'])) {
		fn_delete_taxes($_REQUEST['tax_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "taxes.manage");
}

function fn_delete_taxes($tax_ids)
{
	foreach ((array)$tax_ids as $v) {
		db_query("DELETE FROM ?:taxes WHERE tax_id = ?i", $v);
		db_query("DELETE FROM ?:tax_descriptions WHERE tax_id = ?i", $v);
		db_query("DELETE FROM ?:tax_rates WHERE tax_id = ?i", $v);
		db_query("UPDATE ?:products SET tax_ids = ?p", fn_remove_from_set('tax_ids', $v));
		db_query("UPDATE ?:shippings SET tax_ids = ?p", fn_remove_from_set('tax_ids', $v));
	}
}

function fn_update_tax($data, $tax_id, $lang_code = DESCR_SL)
{
	if (!empty($tax_id)) {
		db_query('UPDATE ?:taxes SET ?u WHERE tax_id = ?i', $data, $tax_id);
		db_query('UPDATE ?:tax_descriptions SET ?u WHERE tax_id = ?i AND lang_code = ?s', $data, $tax_id, $lang_code);
	} else {
		$tax_id = $data['tax_id'] = db_query("INSERT INTO ?:taxes ?e", $data);

		foreach ((array)Registry::get('languages') as $data['lang_code'] => $_v) {
			db_query("INSERT INTO ?:tax_descriptions ?e", $data);
		}
	}

	// Update ratees data
	if (!empty($data['rates'])) {
		foreach ($data['rates'] as $destination_id => $v) {
			$v['destination_id'] = $destination_id;
			$v['tax_id'] = $tax_id;
			if (!empty($v['rate_id'])) {
				db_query("UPDATE ?:tax_rates SET ?u WHERE rate_id = ?i", $v, $v['rate_id']);
			} else {
				db_query("INSERT INTO ?:tax_rates ?e", $v);
			}
		}
	}

	return $tax_id;
}

?>