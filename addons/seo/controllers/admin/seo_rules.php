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
// $Id: seo_rules.php 10055 2010-07-14 10:15:19Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	if ($mode == 'add_rule') {
		if (!empty($_REQUEST['name']) && !empty($_REQUEST['controller'])) {
			foreach ((array)Registry::get('languages') as $lc => $_v) {
				fn_create_seo_name(0, 's', $_REQUEST['name'], 0, $_REQUEST['controller'], $lc);
			}
		}
	}

	if ($mode == 'update_rules') {
		if (!empty($_REQUEST['seo_data'])) {
			foreach ($_REQUEST['seo_data'] as $k => $v) {
				if (!empty($v['name'])) {
					fn_create_seo_name(0, 's', $v['name'], 0, $v['dispatch'], fn_get_corrected_seo_lang_code(DESCR_SL));
				}
			}
		}
	}

	if ($mode == 'delete_rules') {
		if (!empty($_REQUEST['controllers'])) {
			foreach ($_REQUEST['controllers'] as $v) {
				fn_delete_seo_name(0, 's', $v);
			}
		}
	}

	return array(CONTROLLER_STATUS_OK, "seo_rules.manage");
}

if ($mode == 'manage') {

	list($seo_data, $search) = fn_get_seo_rules($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'));

	$view->assign('seo_data', $seo_data);
	$view->assign('search', $search);

} elseif ($mode == 'delete_rule') {
	if (!empty($_REQUEST['controller'])) {
		fn_delete_seo_name(0, 's', $_REQUEST['controller']);
	}

	return array(CONTROLLER_STATUS_OK, "seo_rules.manage");
}

function fn_get_seo_rules($params = array(), $items_per_page = 0, $lang_code = DESCR_SL)
{
	$lang_code = fn_get_corrected_seo_lang_code($lang_code);
	
	$global_total = db_get_fields("SELECT dispatch FROM ?:seo_names WHERE object_id = '0' AND type = 's' GROUP BY dispatch");
	$local_total = db_get_fields("SELECT dispatch FROM ?:seo_names WHERE object_id = '0' AND type = 's' AND lang_code = ?s", $lang_code);
	if ($diff = array_diff($global_total, $local_total)) {
		foreach ($diff as $disp) {
			fn_create_seo_name(0, 's', str_replace('.', '-', $disp), 0, $disp, DESCR_SL);
		}
	}

	// Init filter
	$params = fn_init_view('seo_rules', $params);

	// Set default values to input params
	$params['page'] = empty($params['page']) ? 1 : $params['page']; // default page is 1

	$condition = '';

	if (isset($params['name']) && fn_string_no_empty($params['name'])) {
		$condition .= db_quote(" AND name LIKE ?l", "%".trim($params['name'])."%");
	}

	if (isset($params['controller']) && fn_string_no_empty($params['controller'])) {
		$condition .= db_quote(" AND dispatch LIKE ?l", "%".trim($params['controller'])."%");
	}

	$limit = '';
	if (!empty($items_per_page)) {
		$total = db_get_field("SELECT COUNT(*) FROM ?:seo_names WHERE object_id = '0' AND type = 's' AND lang_code = ?s ?p", $lang_code, $condition);
		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}

	$seo_data = db_get_array("SELECT name, dispatch FROM ?:seo_names WHERE object_id = '0' AND type = 's' AND lang_code = ?s ?p ORDER BY dispatch $limit", $lang_code, $condition);

	return array($seo_data, $params);
}
?>