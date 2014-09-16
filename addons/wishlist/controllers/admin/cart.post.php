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
// $Id: cart.post.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($mode == 'cart_list') {
	
	$item_types = fn_get_cart_content_item_types();

	if (empty($_REQUEST['user_id'])) {

		$cart_list = $view->get_var('cart_list');

		if (!empty($cart_list)) {
			$all_wishlist_products = db_get_hash_single_array("SELECT user_id, COUNT(item_id) as count FROM ?:user_session_products WHERE user_id IN (?n) AND type = 'W' GROUP BY user_id", array('user_id', 'count'), array_keys($cart_list));

			foreach ($cart_list as $u_id => $cart) {
				$cart_list[$u_id]['wishlist_products'] = !empty($all_wishlist_products[$u_id]) ? $all_wishlist_products[$u_id] : 0;
			}
		}

		$view->assign('cart_list', $cart_list);

	} else {
		$products = db_get_array("SELECT ?:user_session_products.item_id, ?:user_session_products.item_type, ?:user_session_products.product_id, ?:user_session_products.amount, ?:user_session_products.price, ?:user_session_products.extra, ?:product_descriptions.product FROM ?:user_session_products LEFT JOIN ?:product_descriptions ON ?:user_session_products.product_id = ?:product_descriptions.product_id AND ?:product_descriptions.lang_code = ?s WHERE ?:user_session_products.user_id = ?i AND ?:user_session_products.type = 'W' AND ?:user_session_products.item_type IN (?a)", DESCR_SL, $_REQUEST['user_id'], $item_types);

		if (!empty($products)) {
			foreach ($products as $key => $product) {
				$products[$key]['extra'] = unserialize($product['extra']);
			}
		}

		$view->assign('wishlist_products', $products);
	}
}

?>