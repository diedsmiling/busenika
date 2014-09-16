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
// $Id: product_groups.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

	fn_trusted_vars('group', 'groups_data');
	$suffix = '';

	if ($mode == 'delete') {
		if (!empty($_REQUEST['group_ids'])) {
			fn_delete_affiliate_groups($_REQUEST['group_ids']);
		} else {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('error_no_data'));
		}

		$suffix = '.manage';
	}

	if ($mode == 'update') {

		$group_id = fn_update_affiliate_group($_REQUEST['group'], $_REQUEST['group_id'], DESCR_SL);

		$suffix = ".update?group_id=$group_id";
	}

	return array(CONTROLLER_STATUS_OK, "product_groups$suffix");
}

if ($mode == 'update') {
	$group = fn_get_group_data($_REQUEST['group_id'], DESCR_SL);
	
	if (empty($group)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	fn_add_breadcrumb(fn_get_lang_var('product_groups'), "product_groups.manage?link_to=$group[link_to]");

	$view->assign('group', $group);

} elseif ($mode == 'add') {

	$link_to = empty($_REQUEST['link_to']) ? 'C' : $_REQUEST['link_to'];

	fn_add_breadcrumb(fn_get_lang_var('product_groups'), "product_groups.manage?link_to=$link_to");

	$view->assign('link_to', $link_to);

} elseif ($mode == 'manage') {

	$link_to = empty($_REQUEST['link_to']) ? 'C' : $_REQUEST['link_to'];

	Registry::set('navigation.tabs', array (
		'C' => array(
			'title' => fn_get_lang_var('group_for_category'),
			'href' => "product_groups.manage?link_to=C",
			'ajax' => true
		),
		'P' => array(
			'title' => fn_get_lang_var('group_for_product'),
			'href' => "product_groups.manage?link_to=P",
			'ajax' => true
		),
		'U' => array(
			'title' => fn_get_lang_var('url'),
			'href' => "product_groups.manage?link_to=U",
			'ajax' => true
		),
	));
	// [/Page sections]

	$groups = fn_get_groups($link_to, false, @$_REQUEST['page'], DESCR_SL); // FIXME

	$view->assign('groups', $groups);
	$view->assign('link_to', $link_to);

} elseif ($mode == 'delete') {
	if (!empty($_REQUEST['group_id'])) {
		fn_delete_affiliate_groups((array)$_REQUEST['group_id']);
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('error_no_data'));
	}

	return array(CONTROLLER_STATUS_REDIRECT, "product_groups.manage");
}

function fn_delete_affiliate_groups($group_ids)
{
	$groups_names = array();
	foreach ($group_ids as $group_id) {
		$groups_names[] = fn_get_group_name($group_id, DESCR_SL);
		db_query("DELETE FROM ?:aff_group_descriptions WHERE group_id = ?i", $group_id);
		db_query("DELETE FROM ?:aff_groups WHERE group_id = ?i", $group_id);
	}

	if (!empty($groups_names)) {
		$groups_names = '&nbsp;-&nbsp;' . implode('<br />&nbsp;-&nbsp;', $groups_names);
		fn_set_notification('N', fn_get_lang_var('information'), fn_get_lang_var('deleted_product_groups').':<br />'.$groups_names);
	}
}

function fn_update_affiliate_group($data, $group_id, $lang_code = DESCR_SL)
{
	if (!empty($group_id)) {
		db_query("UPDATE ?:aff_groups SET ?u WHERE group_id = ?i", $data, $group_id);
		db_query("UPDATE ?:aff_group_descriptions SET ?u WHERE group_id = ?i AND lang_code = ?s", $data, $group_id, $lang_code);
	} else {
		$group_id = $data['group_id'] = db_query("INSERT INTO ?:aff_groups ?e", $data);

		foreach ((array)Registry::get('languages') as $data['lang_code'] => $v) {
			db_query("INSERT INTO ?:aff_group_descriptions ?e", $data);
		}
	}

	return $group_id;
}

function fn_get_groups($links_to = array('C', 'P', 'U'), $is_avail = false, $page = 1, $lang_code = CART_LANGUAGE)
{
	$condition = '';
	if (!empty($links_to)) {
		$condition .= db_quote(" AND ?:aff_groups.link_to IN (?a)", $links_to);
	}

	if (!empty($is_avail)) {
		$condition .= " AND ?:status = 'A'";
	}

	$limit = '';
	if (!empty($page)) {
		$total_items = db_get_field("SELECT COUNT(*) FROM ?:aff_groups LEFT JOIN ?:aff_group_descriptions ON ?:aff_group_descriptions.group_id = ?:aff_groups.group_id AND ?:aff_group_descriptions.lang_code = ?s WHERE 1 ?p ORDER BY ?:aff_group_descriptions.name", $lang_code, $condition);

		if (!empty($total_items)) {
			$items_per_page = (AREA == 'A') ? Registry::get('settings.Appearance.admin_elements_per_page') : Registry::get('settings.Appearance.elements_per_page');
			$limit = fn_paginate($page, $total_items, $items_per_page);
		}
	}

	$groups = db_get_hash_array("SELECT * FROM ?:aff_groups LEFT JOIN ?:aff_group_descriptions ON ?:aff_group_descriptions.group_id = ?:aff_groups.group_id AND ?:aff_group_descriptions.lang_code = ?s WHERE 1 ?p ORDER BY ?:aff_group_descriptions.name $limit", 'group_id', $lang_code, $condition);
	if (!empty($groups) && is_array($groups)) {
		foreach ($groups as $group_id => $group_data) {
			$groups[$group_id] = fn_convert_group_data($group_data);
		}
	}
	return !empty($groups) ? $groups : false ;
}

// \Functions
?>