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
// $Id: products.pre.php 10444 2010-08-18 07:45:01Z alexions $

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if  ($mode == 'options') {
		if (!empty($_REQUEST['product_data'])) {
			define('GET_OPTIONS', true);
			// Product data
			unset($_REQUEST['product_data']['custom_files']);
			list($product_id, $_data) = each($_REQUEST['product_data']);
			
			if (!empty($_data['configuration'])) {
				// Backup cart before changes
				$cart = $_SESSION['cart'];
				
				$_cart = &$_SESSION['cart'];
				fn_clear_cart($_cart);
				
				$_data['product_id'] = $product_id;
				
				$_data['amount'] = (isset($_data['amount']) && intval($_data['amount']) <= 0) ? 1 : $_data['amount'];
				
				fn_add_product_to_cart(array($product_id => $_data), $_cart, $auth);
				list ($cart_products) = fn_calculate_cart_content($_cart, $auth, 'S', true, 'F', false);

				if (!empty($_cart['points_info'])) {
					Registry::set("runtime.product_configurator.points_info.$product_id", $_cart['points_info']);
				}
				
				// Restore cart data
				$_SESSION['cart'] = $cart;
				
				if (!empty($_REQUEST['appearance'])) {
					foreach ($_REQUEST['appearance'] as $setting => $value) {
						$view->assign($setting, $value);
					}
					
					$view->assign('no_images', true);
				}
				
				$product = reset($cart_products);
				
				if (!empty($product['amount'])) {
					$product['selected_amount'] = $product['amount'];
				}
				
				$additional_data = fn_get_product_data($product['product_id'], $auth, CART_LANGUAGE, '', false, false, true, true);
				
				if (!empty($additional_data)) {
					$product = array_merge($additional_data, $product);
				}
				
				$product['configuration_mode'] = true;
				fn_gather_additional_product_data($product, true, false, true, false);
				
				// Clear all the user notifications
				$_SESSION['notifications'] = array();
				
				$view->assign('product', $product);
				$view->display('views/products/view.tpl');
				exit;
			}
			
		}
	}
}

?>