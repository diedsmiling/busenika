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

function fn_get_tags($params = array(), $items_per_page = 0)
{
	// Init filter
	$params = fn_init_view('tags', $params);

	$default_params = array(
		'page' => 1,
	);

	$params = array_merge($default_params, $params);

	// Define sort fields
	$sortings = array (
		'tag' => '?:tags.tag',
		'status' => '?:tags.status',
		'popularity' => 'popularity',
		'users' => 'users'
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);
	
	$conditions = '';

	if (!empty($params['see']) && $params['see'] == 'my' && empty($params['user_id'])) {
		return array(array(), array());
	}

	if (!empty($params['object_type'])) {
		$conditions .= db_quote(" AND ?:tag_links.object_type = ?s", $params['object_type']);
	}

	if (!empty($params['status'])) {
		$conditions .= db_quote(" AND ?:tags.status IN (?a)", $params['status']);
	}

	if (!empty($params['object_id'])) {
		$conditions .= db_quote(" AND ?:tag_links.object_id = ?s", $params['object_id']);
	}

	if (!empty($params['user_id'])) {
		$conditions .= db_quote(" AND ?:tag_links.user_id = ?s", $params['user_id']);
	}

	if (isset($params['tag']) && fn_string_no_empty($params['tag'])) {
		$conditions .= db_quote(" AND ?:tags.tag LIKE ?l", "%".trim($params['tag'])."%");
	}

	$my_tag_field = '';
	if (!empty($params['user_and_popular'])) {
		$conditions .= db_quote(" AND IF(?:tag_links.user_id = ?i, 1, ?:tags.status IN ('A'))", $params['user_and_popular']);
		$my_tag_field = db_quote(", COUNT(IF(?:tag_links.user_id = ?i, 1, NULL)) as my_tag", $params['user_and_popular']);
	}

	if (!empty($params['period']) && $params['period'] != 'A') {
		list($params['time_from'], $params['time_to']) = fn_create_periods($params);

		$conditions .= db_quote(" AND (?:tags.timestamp >= ?i AND ?:tags.timestamp <= ?i)", $params['time_from'], $params['time_to']);
	}

	$limit = '';
	if (!empty($params['limit'])) {
		$limit = db_quote(' LIMIT 0, ?i', $params['limit']);
	}

	if (!empty($items_per_page)) {
		$total = db_get_field("SELECT COUNT(DISTINCT(?:tags.tag_id)) FROM ?:tags LEFT JOIN ?:tag_links ON ?:tags.tag_id = ?:tag_links.tag_id WHERE 1 ?p", $conditions);
		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		$params['sort_order'] = 'asc';
	}

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = 'tag';
	}

	$sorting = $sortings[$params['sort_by']] . ' ' . $directions[$params['sort_order']];

	// Reverse sorting (for usage in view)
	$params['sort_order'] = $params['sort_order'] == 'asc' ? 'desc' : 'asc';

	$tags = db_get_hash_array("SELECT ?:tags.tag_id, ?:tag_links.object_id, ?:tag_links.object_type, ?:tag_links.user_id, COUNT(?:tag_links.tag_id) as popularity, COUNT(DISTINCT(?:tag_links.user_id)) as users, ?:tags.tag, ?:tags.status $my_tag_field FROM ?:tags LEFT JOIN ?:tag_links ON ?:tag_links.tag_id = ?:tags.tag_id WHERE 1 ?p GROUP BY ?:tags.tag_id ORDER BY $sorting $limit", 'tag_id', $conditions);

	if (!empty($params['count_objects'])) {
		$objs = db_get_array("SELECT tag_id, COUNT(DISTINCT(object_id)) as count, object_type FROM ?:tag_links WHERE tag_id IN (?n) GROUP BY tag_id, object_type", array_keys($tags));
		foreach ($objs as $v) {
			$tags[$v['tag_id']]['objects_count'][$v['object_type']] = $v['count'];
		}
	}

	// Generate popularity level
	foreach ($tags as $k => $v) {
		$level = ceil(log($v['popularity']));
		$tags[$k]['level'] = ($level > TAGS_MAX_LEVEL) ? TAGS_MAX_LEVEL : $level;
	}

	if (!empty($params['sort_popular'])) {
		$tags = fn_sort_array_by_key($tags, 'tag', SORT_ASC);
	}

	return array($tags, $params);
}

function fn_tags_update_product($product_data, $product_id)
{	
	if (!empty($product_data['tags'])) {
		fn_update_tags(array(
			'object_type' => 'P', 
			'object_id' => $product_id, 
			'values' => $product_data['tags']
		), $_SESSION['auth']['user_id']);
	}
}

function fn_tags_update_page($page_data, $page_id)
{	
	if (!empty($page_data['tags'])) {
		fn_update_tags(array(
			'object_type' => 'A', 
			'object_id' => $page_id, 
			'values' => $page_data['tags']
		), $_SESSION['auth']['user_id']);
	}
}

function fn_delete_tags($params)
{
	$condition = '';
	if (!empty($params['object_id'])) {
		$condition .= db_quote(" AND object_id = ?i", $params['object_id']);
	}

	if (!empty($params['object_type'])) {
		$condition .= db_quote(" AND object_type = ?s", $params['object_type']);
	}

	if (!empty($params['user_id'])) {
		$condition .= db_quote(" AND user_id = ?i", $params['user_id']);
	}

	if (!empty($params['tag_id'])) {
		$condition .= db_quote(" AND tag_id = ?i", $params['tag_id']);
	}

	$tag_ids = db_get_fields("SELECT tag_id FROM ?:tag_links WHERE 1 ?p", $condition);
	db_query("DELETE FROM ?:tag_links WHERE 1 ?p", $condition);

	// Check if tags have links and delete them if not
	$_tag_ids = db_get_fields("SELECT tag_id FROM ?:tag_links WHERE tag_id IN (?n)", $tag_ids);
	$diff = array_diff($tag_ids, $_tag_ids);
	if (!empty($diff)) {
		db_query("DELETE FROM ?:tags WHERE tag_id IN (?n)", $diff);
	}

	return true;
}

function fn_tags_delete_product($product_id)
{
	return fn_delete_tags(array('object_id' => $product_id, 'object_type' => 'P'));
}

function fn_tags_delete_page($page_id)
{
	return fn_delete_tags(array('object_id' => $page_id, 'object_type' => 'A'));
}

//
// This function clones product tags
//
function fn_tags_clone_product($product_id, $pid)
{
	$tags = db_get_array("SELECT * FROM ?:tag_links WHERE object_type = 'P' AND object_id = ?i", $product_id);
	foreach ($tags as $tag) {
		$tag['object_id'] = $pid;
		db_query("INSERT INTO ?:tag_links ?e", $tag);
	}	
}


function fn_tags_clone_page($page_id, $pid)
{
	$tags = db_get_array("SELECT * FROM ?:tag_links WHERE object_type = 'A' AND object_id = ?i", $page_id);
	foreach ($tags as $tag) {
		$tag['object_id'] = $pid;
		db_query("INSERT INTO ?:tag_links ?e", $tag);
	}	
} 

function fn_update_tags($tags_data, $user_id = 0) 
{
	if (!empty($user_id)) {
		// delete all user links first
		db_query("DELETE FROM ?:tag_links WHERE object_id = ?i AND object_type = ?s AND user_id = ?i", $tags_data['object_id'], $tags_data['object_type'], $user_id);

		foreach ($tags_data['values'] as $tag) {
			if (empty($tag)) {
				continue;
			}

			$tag_id = db_get_field("SELECT tag_id FROM ?:tags WHERE tag = ?s", $tag);
			if (empty($tag_id)) {
				$_data = array(
					'tag' => $tag,
					'status' => (AREA == 'A') ? 'A' : 'P',
					'timestamp' => TIME
				);
				
				$tag_id = db_query("INSERT INTO ?:tags ?e", $_data);
			}

			
			//if this tag already exists for this user for this item, skip
			$_data = array(
				'user_id' => $user_id,
				'object_id' => $tags_data['object_id'],
				'object_type' => $tags_data['object_type'],
				'tag_id' => $tag_id
			);

			$exists = db_query("REPLACE INTO ?:tag_links ?e", $_data);
		}

		return true;
	}

	return false;
}

function fn_tags_get_products(&$params, &$fields, &$sortings, &$condition, &$join)
{
	if (isset($params['tag']) && fn_string_no_empty($params['tag'])) {
		$join .= db_quote(" LEFT JOIN ?:tag_links ON ?:tag_links.object_id = products.product_id AND ?:tag_links.object_type = 'P' INNER JOIN ?:tags ON ?:tag_links.tag_id = ?:tags.tag_id");
		$condition .= db_quote(" AND (?:tags.tag = ?s)", trim($params['tag']));
		if (!empty($params['see']) && $params['see'] == 'my') {
			$condition .= db_quote(" AND (?:tag_links.user_id = ?i)", $_SESSION['auth']['user_id']);

		}
	}
	return true;
}

function fn_tags_get_pages(&$params, &$join, &$conditions, &$fields, &$group_by, &$sortings)
{
	if (isset($params['tag']) && fn_string_no_empty($params['tag'])) {
		$fields[] = '?:tag_links.*, ?:tags.*';
		$join .= db_quote(" LEFT JOIN ?:tag_links ON ?:pages.page_id = ?:tag_links.object_id INNER JOIN ?:tags ON ?:tag_links.tag_id=?:tags.tag_id ");
		$conditions .= db_quote(" AND (?:tags.tag = ?s) AND ?:tag_links.object_type = 'A' ", trim($params['tag']));
		if (!empty($params['see']) && $params['see'] == 'my') {
			$conditions .= db_quote(" AND (?:tag_links.user_id = ?i)", $_SESSION['auth']['user_id']);
		}		
	}
	return true;
}

function fn_tags_get_additional_product_data(&$product, &$auth)
{
	$product['tags']['popular'] = $product['tags']['user'] = array();
	list($tags) = fn_get_tags(array('object_type' => 'P', 'object_id' => $product['product_id'], 'user_and_popular' => $auth['user_id']));

	foreach ($tags as $k => $v) {
		if (!empty($v['my_tag'])) {
			$product['tags']['user'][$v['tag_id']] = $v;
		}
		if ($v['status'] == 'A') {
			$product['tags']['popular'][$v['tag_id']] = $v;
		}
	}
}

function fn_tags_get_page_data($page)
{
	$page['tags']['popular'] = $page['tags']['user'] = array();
	list($tags) = fn_get_tags(array('object_type' => 'A', 'object_id' => $page['page_id'], 'user_and_popular' => $_SESSION['auth']['user_id']));

	foreach ($tags as $k => $v) {
		if (!empty($v['my_tag'])) {
			$page['tags']['user'][$v['tag_id']] = $v;
		}
		if ($v['status'] == 'A') {
			$page['tags']['popular'][$v['tag_id']] = $v;
		}
	}
}

function fn_tags_get_users(&$params, &$fields, &$sortings, &$condition, &$join)
{
	if (isset($params['tag']) && fn_string_no_empty($params['tag'])) {
		$join .= db_quote(" LEFT JOIN ?:tag_links ON ?:users.user_id = ?:tag_links.user_id INNER JOIN ?:tags ON ?:tag_links.tag_id = ?:tags.tag_id ");
		$condition .= db_quote(" AND ?:tags.tag = ?s", $params['tag']);
	}
}

function fn_tags_seo_is_indexed_page($indexed_dispatches)
{
	$indexed_dispatches['tags.view'] = array('index' => array('tag'));
}

?>