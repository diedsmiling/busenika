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
// $Id: banners.php 9836 2010-06-22 12:59:50Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

	fn_trusted_vars('banners', 'banner_data');
	$suffix = '';

	//
	// Delete banners
	//
	if ($mode == 'delete') {
		foreach ($_REQUEST['banner_ids'] as $v) {
			fn_delete_banner_by_id($v);
		}

		$suffix = '.manage';
	}

	//
	// Update banners
	//
	if ($mode == 'm_update') {

		if (!empty($_REQUEST['banners'])) {
			foreach ($_REQUEST['banners'] as $k => $v) {
				if (!empty($v['banner'])) {
					$v['localization'] = empty($v['localization']) ? '' : fn_implode_localizations($v['localization']);

					if (isset($v['timestamp'])) {
						$v['timestamp'] = fn_parse_date($v['timestamp']);
					}

					db_query("UPDATE ?:banners SET ?u WHERE banner_id = ?i", $v, $k);
					db_query("UPDATE ?:banner_descriptions SET ?u WHERE banner_id = ?i AND lang_code = ?s", $v, $k, DESCR_SL);
				}
			}
		}

		$suffix = '.manage';
	}

	//
	// Add/edit banners
	//
	if ($mode == 'update') {
		$banner_id = fn_update_banner($_REQUEST['banner_data'], $_REQUEST['banner_id'], DESCR_SL);

		$suffix = ".update?banner_id=$banner_id";
	}

	return array(CONTROLLER_STATUS_OK, "banners$suffix");
}

if ($mode == 'update') {
	$banner = fn_get_banner_data($_REQUEST['banner_id'], DESCR_SL);

	if (empty($banner)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	fn_add_breadcrumb(fn_get_lang_var('banners'), "banners.manage");

	$view->assign('banner', $banner);

} elseif ($mode == 'add') {

	fn_add_breadcrumb(fn_get_lang_var('banners'), "banners.manage");

} elseif ($mode == 'manage' || $mode == 'picker') {

	list($banners, ) = fn_get_banners(array(), DESCR_SL);

	$view->assign('banners', $banners);

} elseif ($mode == 'delete') {
	if (!empty($_REQUEST['banner_id'])) {
		fn_delete_banner_by_id($_REQUEST['banner_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "banners.manage");
}

//
// Categories picker
//
if ($mode == 'picker') {
	$view->display('addons/banners/pickers/banners_picker_contents.tpl');
	exit;
}

function fn_delete_banner_by_id($banner_id)
{
	db_query("DELETE FROM ?:banners WHERE banner_id = ?i", $banner_id);
	db_query("DELETE FROM ?:banner_descriptions WHERE banner_id = ?i", $banner_id);

	fn_set_hook('delete_banners', $banner_id);

	fn_clean_block_items('banners', $banner_id);

	fn_delete_image_pairs($banner_id, 'banner');
}

function fn_update_banner($data, $banner_id, $lang_code = DESCR_SL)
{
	if (isset($data['timestamp'])) {
		$data['timestamp'] = fn_parse_date($data['timestamp']);
	}

	$data['localization'] = empty($data['localization']) ? '' : fn_implode_localizations($data['localization']);

	if (!empty($banner_id)) {
		db_query("UPDATE ?:banners SET ?u WHERE banner_id = ?i", $data, $banner_id);
		db_query("UPDATE ?:banner_descriptions SET ?u WHERE banner_id = ?i AND lang_code = ?s", $data, $banner_id, $lang_code);
	} else {
		$banner_id = $data['banner_id'] = db_query("REPLACE INTO ?:banners ?e", $data);

		foreach ((array)Registry::get('languages') as $data['lang_code'] => $v) {
			db_query("REPLACE INTO ?:banner_descriptions ?e", $data);
		}
	}

	fn_attach_image_pairs('banners_main', 'banner', $banner_id, $lang_code);

	return $banner_id;
}

?>