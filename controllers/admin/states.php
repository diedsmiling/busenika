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
// $Id: states.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

/** Body **/

if (empty($_REQUEST['country_code'])) {
	$_REQUEST['country_code'] = Registry::get('settings.General.default_country');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//
	// Updating existing states
	//
	if ($mode == 'update') {
		foreach ($_REQUEST['states'] as $key => $_data) {
			if (!empty($_data)) {
				db_query("UPDATE ?:state_descriptions SET ?u WHERE state_id = ?i AND lang_code = ?s", $_data, $key, DESCR_SL);
			}
		}
	}

	//
	// Delete selected states
	//
	if ($mode == 'delete') {

		if (!empty($_REQUEST['state_ids'])) {
			foreach ($_REQUEST['state_ids'] as $v) {
				db_query("DELETE FROM ?:states WHERE state_id = ?i", $v);
				db_query("DELETE FROM ?:state_descriptions WHERE state_id = ?i", $v);
			}
		}
	}

	//
	// Adding new states
	//
	if ($mode == 'add') {
		foreach ($_REQUEST['state_data_add'] as $key => $value) {
			if (!empty($value['code']) && !empty($value['state'])) {
				$value['country_code'] = $_REQUEST['country_code'];
				$value['state_id'] = $state_id = db_query("REPLACE INTO ?:states ?e", $value);

				foreach ((array)Registry::get('languages') as $value['lang_code'] => $_v) {
					db_query('REPLACE INTO ?:state_descriptions ?e', $value);
				}
			}
		}
	}

	return array(CONTROLLER_STATUS_OK, "states.manage?country_code=$_REQUEST[country_code]");
}

if ($mode == 'manage') {

	$country_code = !empty($_REQUEST['country_code']) ? $_REQUEST['country_code'] : Registry::get('settings.General.default_country');

	$view->assign('states', fn_get_states($country_code, DESCR_SL, true));
	$view->assign('country_code', $country_code);
	$view->assign('countries', fn_get_countries());

} elseif ($mode == 'delete') {

	if (!empty($_REQUEST['state_id'])) {
		db_query("DELETE FROM ?:states WHERE state_id = ?i", $_REQUEST['state_id']);
		db_query("DELETE FROM ?:state_descriptions WHERE state_id = ?i", $_REQUEST['state_id']);
	}
	return array(CONTROLLER_STATUS_REDIRECT, "states.manage?country_code=$_REQUEST[country_code]");
}

/** /Body **/

?>