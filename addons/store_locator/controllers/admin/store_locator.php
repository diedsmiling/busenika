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
// $Id: store_locator.php 9578 2010-05-24 07:02:51Z  $
//

if (!defined('AREA')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$suffix = '';
	fn_trusted_vars('store_locations', 'add_store_location');

	if ($mode == 'update') {
		if (!empty($_REQUEST['store_locations'])) {
			foreach ($_REQUEST['store_locations'] as $key => $entry) {
				if ($key) {
					$entry['localization'] = empty($entry['localization']) ? '' : fn_implode_localizations($entry['localization']);
					db_query('UPDATE ?:store_locations SET ?u WHERE store_location_id = ?i', $entry, $key);
					db_query('UPDATE ?:store_location_descriptions SET ?u WHERE store_location_id = ?i AND lang_code = ?s', $entry, $key, DESCR_SL);
				}
			}
		}

		$suffix .= '.manage';
	}

	if ($mode == 'add') {
		if (!empty($_REQUEST['add_store_location'][0])) {
			$data = $_REQUEST['add_store_location'][0];

			if (!empty($data['name'])) {
				if (empty($data['position'])) {
					$data['position'] = db_get_field('SELECT MAX(position) FROM ?:store_locations');
					$data['position'] += 10;
				}

				$data['localization'] = empty($data['localization']) ? '' : fn_implode_localizations($data['localization']);

				$id = db_query('INSERT INTO ?:store_locations ?e', $data);

				if ($id) {
					$data['store_location_id'] = $id;

					foreach ((array)Registry::get('languages') as $data['lang_code'] => $v) {
						db_query("INSERT INTO ?:store_location_descriptions ?e", $data);
					}
				}
			}
		}

		$suffix .= '.manage';
	}

	return array (CONTROLLER_STATUS_OK, 'store_locator' . $suffix);
}

if ($mode == 'delete') {
	if (!empty($_REQUEST['store_location_id'])) {
		db_query('DELETE FROM ?:store_locations WHERE store_location_id = ?i', $_REQUEST['store_location_id']);
		db_query('DELETE FROM ?:store_location_descriptions WHERE store_location_id = ?i', $_REQUEST['store_location_id']);
	}
	return array(CONTROLLER_STATUS_REDIRECT, "store_locator.manage");

} elseif ($mode == 'manage') {
	header('X-UA-Compatible: IE=EmulateIE7');

	list($store_locations, $search, $total) = fn_get_store_locations($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

	$view->assign('store_locations', $store_locations);
	$view->assign('search', $search);

	if (empty($store_locations) && defined('AJAX_REQUEST')) {
		$ajax->assign('force_redirection', "store_locator.manage");
	}
	return array (CONTROLLER_STATUS_OK);
}

?>