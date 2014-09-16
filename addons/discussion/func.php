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
// $Id: func.php 10447 2010-08-18 10:10:53Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_get_discussion($object_id, $object_type)
{
	static $cache = array();

	if (empty($cache["{$object_id}_{$object_type}"])) {
		$cache["{$object_id}_{$object_type}"] = db_get_row("SELECT thread_id, type, object_type FROM ?:discussion WHERE object_id = ?i AND object_type = ?s", $object_id, $object_type);

		fn_set_hook('get_discussion', $object_id, $object_type, $cache["{$object_id}_{$object_type}"]);
	}

	if (!empty($_SESSION['saved_post_data']) && !empty($_SESSION['saved_post_data']['post_data'])) {
		$cache["{$object_id}_{$object_type}"]['post_data'] = $_SESSION['saved_post_data']['post_data'];
		unset($_SESSION['saved_post_data']['post_data']);
	}

	return !empty($cache["{$object_id}_{$object_type}"]) ? $cache["{$object_id}_{$object_type}"] : false;
}

function fn_get_discussion_posts($thread_id = 0, $page = 0, $first_limit = '', $random = 'N')
{

	$sets = Registry::get('addons.discussion');
	$discussion_object_types = fn_get_discussion_objects();

	if (empty($thread_id)) {
		return false;
	}

	$thread_data = db_get_row("SELECT type, object_type FROM ?:discussion WHERE thread_id = ?i", $thread_id);

	if ($thread_data['type'] == 'D') {
		return false;
	}
	$join = $fields = '';

	if ($thread_data['type'] == 'C' || $thread_data['type'] == 'B') {
		$join .= " LEFT JOIN ?:discussion_messages ON ?:discussion_messages.post_id = ?:discussion_posts.post_id ";
		$fields .= ", ?:discussion_messages.message";
	}

	if ($thread_data['type'] == 'R' || $thread_data['type'] == 'B') {
		$join .= " LEFT JOIN ?:discussion_rating ON ?:discussion_rating.post_id = ?:discussion_posts.post_id ";
		$fields .= ", ?:discussion_rating.rating_value";
	}

	$status_cond = (AREA == 'A') ? '' : " AND ?:discussion_posts.status = 'A'";
	$total_pages = db_get_field("SELECT COUNT(*) FROM ?:discussion_posts WHERE thread_id = ?i $status_cond", $thread_id);

	if ($first_limit != '') {
		$limit = "LIMIT $first_limit";
	} else {
		$limit = fn_paginate($page, $total_pages, $sets[$discussion_object_types[$thread_data['object_type']] . '_posts_per_page']);
	}

	$order_by = $random == 'N' ? '?:discussion_posts.timestamp DESC' : 'RAND()';

	return db_get_array("SELECT ?:discussion_posts.* $fields FROM ?:discussion_posts $join WHERE ?:discussion_posts.thread_id = ?i $status_cond ORDER BY ?p $limit", $thread_id, $order_by);
}

function fn_delete_discussion($object_id, $object_type)
{
	$thread_id = db_get_field("SELECT thread_id FROM ?:discussion WHERE object_id IN (?n) AND object_type = ?s", $object_id, $object_type);

	if (!empty($thread_id)) {
		db_query("DELETE FROM ?:discussion_messages WHERE thread_id = ?i", $thread_id);
		db_query("DELETE FROM ?:discussion_posts WHERE thread_id = ?i", $thread_id);
		db_query("DELETE FROM ?:discussion_rating WHERE thread_id = ?i", $thread_id);
		db_query("DELETE FROM ?:discussion WHERE thread_id = ?i", $thread_id);
	}
}

function fn_discussion_update_product($product_data, $product_id)
{
	if (empty($product_data['discussion_type'])) {
		return false;
	}

	$discussion = array(
		'object_type' => 'P',
		'object_id' => $product_id,
		'type' => $product_data['discussion_type']
	);

	fn_update_discussion($discussion);
}

function fn_discussion_delete_product($product_id)
{
	return fn_delete_discussion($product_id, 'P');
}

function fn_discussion_update_category($category_data, $category_id)
{
	if (empty($category_data['discussion_type'])) {
		return false;
	}

	$discussion = array(
		'object_type' => 'C',
		'object_id' => $category_id,
		'type' => $category_data['discussion_type']
	);

	fn_update_discussion($discussion);
}

function fn_discussion_delete_category($category_id)
{
	return fn_delete_discussion($category_id, 'C');
}

function fn_discussion_delete_order($order_id)
{
	return fn_delete_discussion($order_id, 'O');
}

function fn_discussion_update_page($page_data, $page_id)
{
	if (empty($page_data['discussion_type'])) {
		return false;
	}

	$discussion = array(
		'object_type' => 'A',
		'object_id' => $page_id,
		'type' => $page_data['discussion_type']
	);

	fn_update_discussion($discussion);
}

function fn_discussion_delete_page($page_id)
{
	return fn_delete_discussion($page_id, 'A');
}

function fn_discussion_update_news($news_data, $news_id)
{
	if (empty($news_data['discussion_type'])) {
		return false;
	}

	$discussion = array(
		'object_type' => 'N',
		'object_id' => $news_id,
		'type' => $news_data['discussion_type']
	);

	fn_update_discussion($discussion);
}

// FIX-EVENTS
function fn_discussion_delete_news($news_id)
{
	return fn_delete_discussion($news_id, 'N');
}

function fn_discussion_update_event($event_data, $event_id)
{
	if (empty($event_data['discussion_type'])) {
		return false;
	}

	$discussion = array(
		'object_type' => 'G',
		'object_id' => $event_id,
		'type' => $event_data['discussion_type']
	);

	fn_update_discussion($discussion);
}

// FIX-EVENTS
function fn_discussion_delete_event($event_id)
{
	return fn_delete_discussion($event_id, 'G');
}

//
// Get average rating
//
function fn_get_discussion_rating($rating_value)
{
	static $cache = array();

	if (!isset($cache[$rating_value])) {
		$cache[$rating_value] = array();
		$cache[$rating_value]['full'] = floor($rating_value);
		$cache[$rating_value]['part'] = $rating_value - $cache[$rating_value]['full'];
		$cache[$rating_value]['empty'] = 5 - $cache[$rating_value]['full'] - (($cache[$rating_value]['part'] == 0) ? 0 : 1);

		if (!empty($cache[$rating_value]['part'])) {
			if ($cache[$rating_value]['part'] <= 0.25) {
				$cache[$rating_value]['part'] = 1;
			} elseif ($cache[$rating_value]['part'] <= 0.5) {
				$cache[$rating_value]['part'] = 2;
			} elseif ($cache[$rating_value]['part'] <= 0.75) {
				$cache[$rating_value]['part'] = 3;
			} elseif ($cache[$rating_value]['part'] <= 0.99) {
				$cache[$rating_value]['part'] = 4;
			}
		}
	}
	return $cache[$rating_value];
}

//
// Get thread average rating
//
function fn_get_average_rating($object_id, $object_type)
{

	$discussion = fn_get_discussion($object_id, $object_type);

	if (empty($discussion) || ($discussion['type'] != 'R' && $discussion['type'] != 'B')) {
		return false;
	}

	return db_get_field("SELECT AVG(a.rating_value) as val FROM ?:discussion_rating as a LEFT JOIN ?:discussion_posts as b ON a.post_id = b.post_id WHERE a.thread_id = ?i and b.status = 'A'", $discussion['thread_id']);
}

function fn_get_discussion_object_data($object_id, $object_type, $lang_code = CART_LANGUAGE)
{
	$data = array();

	// product
	if ($object_type == 'P') {
		$data['description'] = db_get_field("SELECT product FROM ?:product_descriptions WHERE product_id = ?i AND lang_code = ?s", $object_id, $lang_code);
		if (AREA == 'A') {
			$data['url'] = "products.update?product_id=$object_id&selected_section=discussion";
		} else {
			$data['url'] = "products.view?product_id=$object_id";
		}
	} elseif ($object_type == 'C') { // category
		$data['description'] = db_get_field("SELECT category FROM ?:category_descriptions WHERE category_id = ?i AND lang_code = ?s", $object_id, $lang_code);
		if (AREA == 'A') {
			$data['url'] = "categories.update?category_id=$object_id&selected_section=discussion";
		} else {
			$data['url'] = "categories.view?category_id=$object_id";
		}

	// order
	} elseif ($object_type == 'O') {
		$data['description'] = '#'.$object_id;
		if (AREA == 'A') {
			$data['url'] = "orders.details?order_id=$object_id&selected_section=discussion";
		} else {
			$data['url'] = "orders.details?order_id=$object_id";
		}

	// page
	} elseif ($object_type == 'A') {
		$data['description'] = db_get_field("SELECT page FROM ?:page_descriptions WHERE page_id = ?i AND lang_code = ?s", $object_id, $lang_code);

		if (AREA == 'A') {
			$data['url'] = "pages.update?page_id=$object_id&selected_section=discussion";
		} else {
			$data['url'] = "pages.view?page_id=$object_id";
		}

	// Site layout/testimonials
	} elseif ($object_type == 'E') {
		$data['description'] = fn_get_lang_var('discussion_title_home_page');
		if (AREA == 'A') {
			$data['url'] = "discussion.update?discussion_type=E";
		} else {
			$data['url'] = '';
		}
	}

	fn_set_hook('get_discussion_object_data', $data, $object_id, $object_type);

	return $data;
}

function fn_get_discussion_objects()
{
	static $discussion_object_types = array(
		'P' => 'product',
		'C' => 'category',
		'A' => 'page',
		'O' => 'order',
		'E' => 'home_page'
	);

	fn_set_hook('get_discussion_objects', $discussion_object_types);

	return $discussion_object_types;

}

//
// Clone discussion
//
function fn_clone_discussion($object_id, $new_object_id, $object_type)
{

	// Clone attachment
	$data = db_get_row("SELECT * FROM ?:discussion WHERE object_id = ?i AND object_type = ?s", $object_id, $object_type);

	if (empty($data)) {
		return false;
	}

	$old_thread_id = $data['thread_id'];
	$data['object_id'] = $new_object_id;
	unset($data['thread_id']);
	$thread_id = db_query("REPLACE INTO ?:discussion ?e", $data);

	// Clone posts
	$data = db_get_array("SELECT * FROM ?:discussion_posts WHERE thread_id = ?i", $old_thread_id);
	foreach ($data as $v) {
		$old_post_id = $v['post_id'];
		$v['thread_id'] = $thread_id;
		unset($v['post_id']);
		$post_id = db_query("INSERT INTO ?:discussion_posts ?e", $v);

		$message = db_get_row("SELECT * FROM ?:discussion_messages WHERE post_id = ?i", $old_post_id);
		$message['post_id'] = $post_id;
		$message['thread_id'] = $thread_id;
		$message['message'] = $message['message'];
		db_query("INSERT INTO ?:discussion_messages ?e", $message);

		$rating = db_get_row("SELECT * FROM ?:discussion_rating WHERE post_id = ?i", $old_post_id);
		$rating['post_id'] = $post_id;
		$rating['thread_id'] = $thread_id;
		db_query("INSERT INTO ?:discussion_rating ?e", $rating);
	}

	return true;
}

function fn_discussion_clone_product($product_id, $to_product_id)
{
	fn_clone_discussion($product_id, $to_product_id, 'P');
}


function fn_get_rating_list($object_type, $parent_object_id = '')
{

	$object2parent_links = array(
		'P' => array(	//	for product
			'table' => '?:categories',
			'field' => 'category_id',
			'join' => array('?:products_categories' => "?:discussion.object_id=?:products_categories.product_id AND ?:products_categories.link_type='M'",
							'?:categories' => "?:products_categories.category_id=?:categories.category_id"),
		)/*,
		'A' => array(	// for page
			'table' => '?:topics',
			'field' => 'topic_id',
			'join' => array('?:pages_topics' => "?:discussion.object_id=?:pages_topics.page_id AND ?:pages_topics.link_type='M'",
			'?:topics' => "?:pages_topics.topic_id=?:topics.topic_id"),
		)*/
	);

	$query = db_quote(" object_type = ?s AND ?:discussion.type IN ('R', 'B') AND !(?:discussion_rating.rating_value IS NULL) ", $object_type);
	$join = array();
	if (isset($object2parent_links[$object_type]) && !empty($parent_object_id)) {
		$path = db_get_field("SELECT id_path FROM {$object2parent_links[$object_type]['table']} WHERE {$object2parent_links[$object_type]['field']} = ?i", $parent_object_id);
		$parent_object_ids = db_get_fields("SELECT {$object2parent_links[$object_type]['field']} FROM {$object2parent_links[$object_type]['table']} WHERE id_path LIKE ?l", "$path/%");
		$parent_object_ids[] = $parent_object_id;
		$query .= " AND {$object2parent_links[$object_type]['table']}.{$object2parent_links[$object_type]['field']} IN ('" . implode("','", $parent_object_ids) . "') AND {$object2parent_links[$object_type]['table']}.status='A'";
		$join = $object2parent_links[$object_type]['join'];
	}

	if ($object_type == 'P') {
		// Adding condition for the "Show out of stock products" setting
		if (Registry::get('settings.General.show_out_of_stock_products') == 'N' && AREA == 'C') {
			$join["?:product_options_inventory AS inventory"] =  "inventory.product_id=?:discussion.object_id";
			$join['?:products'] = "?:products.product_id=?:discussion.object_id";
			$query .= " AND IF(?:products.tracking='O', inventory.amount>0, ?:products.amount>0)";
		}
	}

	$join_conditions = '';
	foreach ($join as $table => $j_cond) {
		$join_conditions .= " LEFT JOIN $table ON $j_cond ";
	}

	return db_get_hash_array("SELECT object_id, avg(rating_value) AS rating FROM ?:discussion LEFT JOIN ?:discussion_rating ON ?:discussion.thread_id=?:discussion_rating.thread_id $join_conditions WHERE $query GROUP BY ?:discussion.thread_id ORDER BY rating DESC", 'object_id');
}

function fn_is_accessible_discussion($data, &$auth)
{
	$access = false;

	if ($data['object_type'] == 'P') {//product
		$access = fn_get_product_data($data['object_id'], $auth, CART_LANGUAGE, $field_list = 'product_id', false, false, false);

	} elseif ($data['object_type'] == 'C') {//category
		$access = fn_get_category_data($data['object_id'], '', $field_list = 'category_id', false);

	} elseif ($data['object_type'] == 'O') {//order
		if (!empty($auth['user_id'])) {
			$access = db_get_field("SELECT order_id FROM ?:orders WHERE order_id = ?i AND user_id = ?i", $data['object_id'], $auth['user_id']);
		} elseif (!empty($auth['order_ids'])) {
			$access = in_array($data['object_id'], $auth['order_ids']);
		}

	} elseif ($data['object_type'] == 'A') {// page
		$access = fn_get_page_data($data['object_id'], CART_LANGUAGE);

	} elseif ($data['object_type'] == 'E') {// testimonials
		$access = true;
	}

	fn_set_hook('is_accessible_discussion', $data, $auth, $access);

	return !empty($access);
}

function fn_discussion_get_product_data($product_id, &$field_list, &$join)
{
	$field_list .= ", ?:discussion.type as discussion_type";
	$join .= " LEFT JOIN ?:discussion ON ?:discussion.object_id = ?:products.product_id AND ?:discussion.object_type = 'P'";

	return true;
}

function fn_update_discussion($params)
{
	$_data = fn_check_table_fields($params, 'discussion');
	$discussion = fn_get_discussion($params['object_id'], $params['object_type']);

	if (!empty($discussion['thread_id'])) {
		db_query("UPDATE ?:discussion SET ?u WHERE thread_id = ?i", $_data, $discussion['thread_id']);
	} else {
		db_query("REPLACE INTO ?:discussion ?e", $_data);
	}

	return true;
}

function fn_discussion_get_products(&$params, &$fields, &$sortings, &$condition, &$join, &$sorting, &$limit)
{
	if (!empty($params['rating'])) {
		$fields[] = 'avg(?:discussion_rating.rating_value) AS rating';
		$join .= db_quote(" INNER JOIN ?:discussion ON ?:discussion.object_id = products.product_id AND ?:discussion.object_type = 'P'");
		$join .= db_quote(" INNER JOIN ?:discussion_rating ON ?:discussion.thread_id=?:discussion_rating.thread_id");
		$join .= db_quote(" INNER JOIN ?:discussion_posts ON ?:discussion_posts.post_id=?:discussion_rating.post_id AND ?:discussion_posts.status = 'A'");

		$params['sort_by'] = 'rating';
		$params['sort_order'] = 'desc';
		$sortings['rating'] = 'rating';
	}

	return true;
}

function fn_discussion_get_categories(&$params, &$join, &$condition, &$fields, &$group_by, &$sortings)
{
	if (!empty($params['rating'])) {
		$fields[] = 'avg(?:discussion_rating.rating_value) AS rating';
		$join .= db_quote(" INNER JOIN ?:discussion ON ?:discussion.object_id = ?:categories.category_id AND ?:discussion.object_type = 'C'");
		$join .= db_quote(" INNER JOIN ?:discussion_rating ON ?:discussion.thread_id=?:discussion_rating.thread_id");
		$join .= db_quote(" INNER JOIN ?:discussion_posts ON ?:discussion_posts.post_id=?:discussion_rating.post_id AND ?:discussion_posts.status = 'A'");
		$group_by = 'GROUP BY ?:discussion_rating.thread_id';
		$sortings['rating'] = 'rating';
		$params['sort_by'] = 'rating';
		$params['sort_order'] = 'desc';
	}

	return true;
}

function fn_discussion_get_pages(&$params, &$join, &$conditions, &$fields, &$group_by, &$sortings)
{
	if (!empty($params['rating'])) {
		$fields[] = 'avg(?:discussion_rating.rating_value) AS rating';
		$join .= db_quote(" INNER JOIN ?:discussion ON ?:discussion.object_id = ?:pages.page_id AND ?:discussion.object_type = 'A'");
		$join .= db_quote(" INNER JOIN ?:discussion_rating ON ?:discussion.thread_id=?:discussion_rating.thread_id");
		$join .= db_quote(" INNER JOIN ?:discussion_posts ON ?:discussion_posts.post_id=?:discussion_rating.post_id AND ?:discussion_posts.status = 'A'");
		$group_by = '?:discussion_rating.thread_id';
		$sortings['rating'] = 'rating';
		$params['sort_by'] = 'rating';
		$params['sort_order'] = 'desc';
	}

	return true;
}
?>