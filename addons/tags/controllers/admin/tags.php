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
// $Id: tags.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

	if ($mode == 'approve') {
		db_query("UPDATE ?:tags SET status = 'A' WHERE tag_id IN (?n)", $_REQUEST['tag_ids']);
	}
	
	if ($mode == 'disapprove') {
		db_query("UPDATE ?:tags SET status = 'D' WHERE tag_id IN (?n)", $_REQUEST['tag_ids']);
	}
	
	if ($mode == 'delete') {
		fn_delete_tags_by_ids($_REQUEST['tag_ids']);
	}
	
	if ($mode == 'm_update') {
		foreach ($_REQUEST['tags_data'] as $tag_id => $tag) {		
			db_query("UPDATE ?:tags SET tag = ?s WHERE tag_id = ?i", $tag, $tag_id);
		}
	}
	
	if ($mode == 'add') {
		foreach ($_REQUEST['add_tag_data'] as $tag) {			
			if (db_get_field("SELECT COUNT(*) FROM ?:tags WHERE tag = ?s", $tag['tag'])) {	
				db_query("UPDATE ?:tags SET status = ?s WHERE tag = ?s", $tag['status'], $tag['tag']);
			} else {						
				$t = db_query("INSERT INTO ?:tags ?e", $tag);
			}
		}
	}	
	
	return array(CONTROLLER_STATUS_OK, "tags.manage");
}

if ($mode == 'manage') {
	$params = $_REQUEST;
	$params['count_objects'] = true;
	list($tags, $search) = fn_get_tags($params, Registry::get('settings.Appearance.admin_elements_per_page'));

	$view->assign('tags', $tags);
	$view->assign('search', $search);
	$view->assign('tag_objects', fn_get_tag_objects());
	
// ajax autocomplete mode	
} elseif ($mode == 'list') {
	if (defined('AJAX_REQUEST')) {				
		$tags = db_get_fields("SELECT tag FROM ?:tags WHERE tag LIKE ?l", $_REQUEST['q'] . '%');		
		Registry::get('ajax')->assign('autocomplete', $tags);	
		
		exit();	
	}

} elseif ($mode == 'delete') {
	if (!empty($_REQUEST['tag_id'])) {
		fn_delete_tags_by_ids((array)$_REQUEST['tag_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "tags.manage");
}

function fn_delete_tags_by_ids($tag_ids)
{
	db_query("DELETE FROM ?:tags WHERE tag_id IN (?n)", $tag_ids);
	db_query("DELETE FROM ?:tag_links WHERE tag_id IN (?n)", $tag_ids);
}

function fn_get_tag_objects()
{
	$types = array();

	if (Registry::get('addons.tags.tags_for_products') == 'Y') {
		$types['P'] = array(
			'name' => 'products',
			'url' => 'products.manage',
		);
	}
	if (Registry::get('addons.tags.tags_for_pages') == 'Y') {
		$types['A'] = array(
			'name' => 'pages',
			'url' => 'pages.manage',
		);
	}

	fn_set_hook('get_tag_objects', $types);

	return $types;
}

 
?>