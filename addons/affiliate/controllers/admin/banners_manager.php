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
// $Id: banners_manager.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

	fn_trusted_vars('banner', 'banners_data');
	$suffix = '';

	if ($mode == 'delete') {

		if (!empty($_REQUEST['banner_ids'])) {
			$banners_names = array();
			foreach ($_REQUEST['banner_ids'] as $banner_id) {
				$banners_names[] = fn_get_aff_banner_name($banner_id, DESCR_SL);
				fn_delete_banner($banner_id);
			}
			if (!empty($banners_names)) {
				$banners_names = '&nbsp;-&nbsp;' . implode('<br />&nbsp;-&nbsp;', $banners_names);
				fn_set_notification('N', fn_get_lang_var('information'), fn_get_lang_var('deleted_banners').':<br />'.$banners_names);
			}
		} else {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('error_no_data'));
		}

		$suffix = ".manage?banner_type=$_REQUEST[banner_type]&link_to=$_REQUEST[link_to]";
	}

	if ($mode == 'm_update') {
		if (!empty($_REQUEST['banners_data']) && is_array($_REQUEST['banners_data'])) {
			$banners_data = $_REQUEST['banners_data'];

			foreach ($banners_data as $banner_id => $b_data) {
				$_b_data = fn_check_table_fields($b_data, 'aff_banners');
				db_query("UPDATE ?:aff_banners SET ?u WHERE banner_id = ?i", $_b_data, $banner_id);
			}
		}

		$suffix = ".manage&banner_type=$_REQUEST[banner_type]&link_to=$_REQUEST[link_to]";
	}

	if ($mode == 'update') {

		$banner_id = fn_update_banner($_REQUEST['banner'], $_REQUEST['banner_id'], DESCR_SL);

		$suffix = ".update?banner_id=$banner_id";
	}

	return array(CONTROLLER_STATUS_OK, "banners_manager$suffix");
}

if ($mode == 'update') {

	$banner = fn_get_aff_banner_data($_REQUEST['banner_id'], DESCR_SL);
	
	if (empty($banner)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	if ($banner['type'] != 'G') {
		$banner['code'] = fn_get_aff_banner_html('js', $banner, '', '', DESCR_SL);
	}

	if ($banner['link_to'] == 'G') {
		$view->assign('all_groups_list', fn_get_groups_list('D', DESCR_SL));
	}

	fn_add_breadcrumb(fn_get_lang_var('banners'), "banners_manager.manage?banner_type=$banner[type]&link_to=$banner[link_to]");

	$view->assign('banner', $banner);

	$view->assign('banner_type', $banner['type']);
	$view->assign('link_to', $banner['link_to']);

} elseif ($mode == 'add') {

	$banner_type = empty($_REQUEST['banner_type']) ? 'T' : $_REQUEST['banner_type'];
	$link_to = empty($_REQUEST['link_to']) ? 'G' : $_REQUEST['link_to'];

	if ($link_to == 'G') {
		$view->assign('all_groups_list', fn_get_groups_list('Y', DESCR_SL));
	}

	fn_add_breadcrumb(fn_get_lang_var('banners'), "banners_manager.manage?banner_type=$banner_type&link_to=$link_to");

	$view->assign('banner_type', $banner_type);
	$view->assign('link_to', $link_to);

} elseif ($mode == 'manage') {
	$banner_type = empty($_REQUEST['banner_type']) ? 'T' : $_REQUEST['banner_type'];
	$link_to = empty($_REQUEST['link_to']) ? ($banner_type == 'P' ? 'U' : 'G') : $_REQUEST['link_to'];

	// [Page sections]
	if ($banner_type != 'P') {
		Registry::set('navigation.tabs', array (
			'G' => array(
				'title' => fn_get_lang_var('product_groups'),
				'href' => "banners_manager.manage?banner_type=$banner_type&link_to=G",
				'ajax' => true
			),
			'C' => array(
				'title' => fn_get_lang_var('categories'),
				'href' => "banners_manager.manage?banner_type=$banner_type&link_to=C",
				'ajax' => true
			),
			'P' => array(
				'title' => fn_get_lang_var('products'),
				'href' => "banners_manager.manage?banner_type=$banner_type&link_to=P",
				'ajax' => true
			),
			'U' => array(
				'title' => fn_get_lang_var('url'),
				'href' => "banners_manager.manage?banner_type=$banner_type&link_to=U",
				'ajax' => true
			),
		));
	}
	// [/Page sections]

	$banners = fn_get_aff_banners($banner_type, $link_to, false, DESCR_SL); // FIXME
	$view->assign('banners', $banners);
	$view->assign('link_to', $link_to);
	$view->assign('banner_type', $banner_type);

	if ($link_to == 'G') {
		$all_groups_list = fn_get_groups_list('Y', DESCR_SL);
		$view->assign('all_groups_list', $all_groups_list);
	}

	Registry::set('navigation.dynamic.sections', array (
		'T' => array (
			'title' => fn_get_lang_var('text_banners'),
			'href' => "banners_manager.manage?banner_type=T",
		),
		'G' => array (
			'title' => fn_get_lang_var('graphic_banners'),
			'href' => "banners_manager.manage?banner_type=G",
		),
		'P' => array (
			'title' => fn_get_lang_var('product_banners'),
			'href' => "banners_manager.manage?banner_type=P",
		),
	));
	Registry::set('navigation.dynamic.active_section', $banner_type);

} elseif ($mode == 'delete') {

	if (!empty($_REQUEST['banner_id'])) {
		fn_delete_banner($_REQUEST['banner_id']);
		fn_set_notification('N', fn_get_lang_var('information'), fn_get_lang_var('banner_deleted'));
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('error_no_data'));
	}

	return array(CONTROLLER_STATUS_REDIRECT, "banners_manager.manage?banner_type=$_REQUEST[banner_type]&link_to=$_REQUEST[link_to]");
}


//
// Delete a banner
//
function fn_delete_banner($banner_id)
{
	if (empty($banner_id)) {
		return false;
	}

	$banner_type = db_get_field("SELECT type FROM ?:aff_banners WHERE banner_id = ?i", $banner_id);
	db_query("DELETE FROM ?:aff_banner_descriptions WHERE banner_id = ?i", $banner_id);
	db_query("DELETE FROM ?:aff_banners WHERE banner_id = ?i", $banner_id);

	if ($banner_type == 'G') {
		fn_delete_image_pairs($banner_id, 'common', 'aff_banners');
	}

	return true;
}

//
// Update a banner
//
function fn_update_banner($data, $banner_id, $lang_code = DESCR_SL)
{
	if (!empty($data['width'])) {
		$data['width'] = abs(intval($data['width']));
	}

	if (!empty($data['height'])) {
		$data['height'] = abs(intval($data['height']));
	}

	if ($data['type'] == 'P') {
		$data['data'] = serialize($data['data']);
	}

	if (!empty($banner_id)) {
		db_query("UPDATE ?:aff_banners SET ?u WHERE banner_id = ?i", $data, $banner_id);
		db_query("UPDATE ?:aff_banner_descriptions SET ?u WHERE banner_id = ?i AND lang_code = ?s", $data, $banner_id, $lang_code);
	} else {
		$banner_id = $data['banner_id'] = db_query("INSERT INTO ?:aff_banners ?e", $data);

		foreach ((array)Registry::get('languages') as $data['lang_code'] => $v) {
			db_query("INSERT INTO ?:aff_banner_descriptions ?e", $data);
		}
	}

	if ($data['type'] == 'G') {
		// Adding banner images pair
		fn_attach_image_pairs('banner', 'aff_banners', $banner_id, $lang_code);
	}

	return $banner_id;
}


?>