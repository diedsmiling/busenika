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
// $Id: products.pre.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($mode == 'options') {
		if (!empty($_REQUEST['product_data']) && !empty($_REQUEST['appearance']['wishlist'])) {
			$wishlist = $_SESSION['wishlist'];
			$product_data = $_REQUEST['product_data'];
			
			foreach ($product_data as $id => $product) {
				if (isset($wishlist['products'][$id])) {
					$wishlist['products'][$id] = array_merge($wishlist['products'][$id], $product);
				}
			}
			
			$products = !empty($wishlist['products']) ? $wishlist['products'] : array();
			
			if (!empty($products)) {
				foreach($products as $k => $v) {
					$extra = $v['extra'];
					$products[$k] = fn_get_product_data($v['product_id'], $auth);
					if (empty($products[$k])) {
						unset($products[$k], $wishlist['products'][$k]);
						continue;
					}
					$products[$k]['extra'] = empty($products[$k]['extra']) ? array() : $products[$k]['extra'];
					$products[$k]['extra'] = array_merge($products[$k]['extra'], $extra);
					
					if (isset($products[$k]['extra']['product_options'])) {
						$products[$k]['selected_options'] = $product_data[$k]['product_options'];
					}
					
					fn_gather_additional_product_data($products[$k], true);
				}
			}
			
			$view->assign('products', $products);
			$view->assign('wishlist', $wishlist);
			
			$view->display('addons/wishlist/views/wishlist/view.tpl');
			exit;
		}
	}
}

?>