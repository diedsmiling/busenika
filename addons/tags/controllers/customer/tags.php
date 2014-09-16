<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2009 Simbirsk Technologies Ltd. All rights reserved.    *
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
// $Id: tags.php 7923 2009-09-01 08:52:37Z alexions $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($mode == 'update') {			

		fn_update_tags($_REQUEST['tags_data'], $auth['user_id']);
	}

	return array(CONTROLLER_STATUS_OK);
}

if ($mode == 'view') {	
	
	if (Registry::get('addons.tags.tags_for_products') == 'Y') {
		$params = $_REQUEST;
		$params['type'] = 'extended';	

		list($products, $search) = fn_get_products($params, Registry::get('settings.Appearance.products_per_page'));
		
		if (!empty($products)) {
			foreach ($products as $k => $v) {
				fn_gather_additional_product_data($products[$k], true, false, true, true, true);
			}
		}
		
		$selected_layout = fn_get_products_layout($params);
		
		$view->assign('selected_layout', $selected_layout);
		$view->assign('products', $products);
		$view->assign('search', $search);
	}

	if (Registry::get('addons.tags.tags_for_pages') == 'Y') {
		$params = $_REQUEST;
		$params['status'] = (!empty($_REQUEST['see']) && $_REQUEST['see'] == 'my') ? array('A', 'H') : array('A');

		list($pages, $params) = fn_get_pages($params);	
		$view->assign('pages', $pages);
	}

	if (empty($_REQUEST['see']) || $_REQUEST['see'] != 'my') {
		$view->assign('page_title', str_replace('[tag]', $_REQUEST['tag'], fn_get_lang_var('items_marked_by_tag')));	
	} else {
		$view->assign('page_title', str_replace('[tag]', $_REQUEST['tag'], fn_get_lang_var('my_items_marked_by_tag')));	
	}

	if (!empty($products) || !empty($pages)) {
		$view->assign('tag_objects_exist', true);
	}
	
// summary mode: tag - product list, tag - product list	
} elseif ($mode == 'summary') {
	if (!empty($auth['user_id'])) {		
		list($user_tags) = fn_get_tags(array('user_id' => $auth['user_id']));
		foreach ($user_tags as &$tag) {
			$tag['total'] = 0;
			if (Registry::get('addons.tags.tags_for_products') == 'Y') {
				$product_ids = db_get_fields("SELECT object_id FROM ?:tag_links WHERE object_type = ?s AND user_id = ?i AND tag_id = ?i", 'P', $auth['user_id'], $tag['tag_id']);
				$tag['products'] = fn_get_product_name($product_ids);
				$tag['total'] += count($product_ids);
			}

			if (Registry::get('addons.tags.tags_for_products') == 'Y') {
				$page_ids = db_get_fields("SELECT object_id FROM ?:tag_links WHERE object_type = ?s AND user_id = ?i AND tag_id = ?i", 'A', $auth['user_id'], $tag['tag_id']);
				$tag['pages'] = fn_get_page_name($page_ids);
				$tag['total'] += count($page_ids);
			}
		}
		
		$view->assign('tags_summary', $user_tags);
	}
	
// ajax autocomplete mode	
} elseif ($mode == 'list') {	
	if (defined('AJAX_REQUEST')) {				
		$tags = db_get_fields("SELECT tag FROM ?:tags WHERE tag LIKE ?l", $_REQUEST['q'] . '%');		
		Registry::get('ajax')->assign('autocomplete', $tags);	
		
		
		exit();	
	}

} elseif ($mode == 'delete' && !empty($auth['user_id']) && !empty($_REQUEST['tag_id'])) {
	$params = $_REQUEST;
	$params['user_id'] = $auth['user_id'];
	fn_delete_tags($params);
}

?>
