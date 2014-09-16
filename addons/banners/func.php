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
// $Id: func.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// Get banners
//
function fn_get_banners($params, $lang_code = CART_LANGUAGE)
{
	$default_params = array(
		'items_per_page' => 0,
		'sort_by' => 'name',
	);

	$params = array_merge($default_params, $params);

	$sortings = array(
		'timestamp' => '?:banners.timestamp',
		'name' => '?:banner_descriptions.banner',
	);

	$directions = array(
		'asc' => 'asc',
		'desc' => 'desc'
	);

	$condition = $limit = '';

	if (!empty($params['limit'])) {
		$limit = db_quote(' LIMIT 0, ?i', $params['limit']);
	}

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		$params['sort_order'] = 'asc';
	}

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = 'name';
	}

	$sorting = $sortings[$params['sort_by']] . ' ' . $directions[$params['sort_order']];

	$condition = (AREA == 'A') ? '' : " AND ?:banners.status = 'A' ";
	$condition .= fn_get_localizations_condition('?:banners.localization');

	if (!empty($params['item_ids'])) {
		$condition .= db_quote(' AND ?:banners.banner_id IN (?n)', explode(',', $params['item_ids']));
	}

	if (!empty($params['period']) && $params['period'] != 'A') {
		list($params['time_from'], $params['time_to']) = fn_create_periods($params);
		$condition .= db_quote(" AND (?:banners.timestamp >= ?i AND ?:banners.timestamp <= ?i)", $params['time_from'], $params['time_to']);
	}


	$banners = db_get_array("SELECT ?:banners.banner_id, ?:banners.type, ?:banners.target, ?:banners.status, ?:banners.url, ?:banner_descriptions.banner, ?:banner_descriptions.description FROM ?:banners LEFT JOIN ?:banner_descriptions ON ?:banner_descriptions.banner_id = ?:banners.banner_id AND ?:banner_descriptions.lang_code = ?s WHERE 1 ?p ORDER BY ?p ?p", $lang_code, $condition, $sorting, $limit);

	foreach ($banners as $k => $v) {
		$banners[$k]['main_pair'] = fn_get_image_pairs($v['banner_id'], 'banner', 'M', true, false, $lang_code);
	}

	if (!empty($params['item_ids'])) {
		$banners = fn_sort_by_ids($banners, explode(',', $params['item_ids']), 'banner_id');
	}

	fn_set_hook('get_banners', $banners);

	return array($banners, $params);
}


//
// Get specific banner data
//
function fn_get_banner_data($banner_id, $lang_code = CART_LANGUAGE)
{
	$status_condition = (AREA == 'A') ? '' : " AND ?:banners.status IN ('A', 'H') ";

	$banner = db_get_row("SELECT ?:banners.banner_id, ?:banners.status, ?:banners.url, ?:banner_descriptions.banner, ?:banners.type, ?:banners.target, ?:banners.localization, ?:banners.timestamp, ?:banner_descriptions.description FROM ?:banners LEFT JOIN ?:banner_descriptions ON ?:banner_descriptions.banner_id = ?:banners.banner_id AND ?:banner_descriptions.lang_code = ?s WHERE ?:banners.banner_id = ?i ?p", $lang_code, $banner_id, $status_condition);

	if (!empty($banner)) {
		$banner['main_pair'] = fn_get_image_pairs($banner['banner_id'], 'banner', 'M', true, false, $lang_code);
	}

	return $banner;
}

//
// Get banner name
//
function fn_get_banner_name($banner_id, $lang_code = CART_LANGUAGE)
{
	if (!empty($banner_id)) {
		return db_get_field("SELECT banner FROM ?:banner_descriptions WHERE banner_id = ?i AND lang_code = ?s", $banner_id, $lang_code);
	}

	return false;
}
function fn_banners_localization_objects(&$_tables)
{
	$_tables[] = 'banners';
}
?>