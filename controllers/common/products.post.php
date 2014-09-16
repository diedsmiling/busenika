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
// $Id: products.post.php 10546 2010-08-30 13:52:15Z alexions $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'options') {
		define('GET_OPTIONS', true);
		$_auth = $auth;
		
		if (empty($_REQUEST['product_data']) && empty($_REQUEST['cart_products'])) {
			return array(CONTROLLER_STATUS_NO_PAGE);
		}
		
		// Apply the specific block template settings
		if (!empty($_REQUEST['appearance'])) {
			foreach ($_REQUEST['appearance'] as $setting => $value) {
				$view->assign($setting, $value);
			}
		}
		$view->assign('show_images', true);
		$view->assign('no_capture', true);
		
		if (!empty($_REQUEST['product_data'])) {
			// Product data
			unset($_REQUEST['product_data']['custom_files']);
			$product_data = $_REQUEST;
			list($product_id, $_data) = each($product_data['product_data']);
			
			$product_id = isset($_data['product_id']) ? $_data['product_id'] : $product_id;
			$selected_options = empty($_data['product_options']) ? array() : $_data['product_options'];
			unset($selected_options['AOC']);
			
			if (isset($product_data['additional_info']['info_type']) && $product_data['additional_info']['info_type'] == 'D') {
				$product = fn_get_product_data($product_id, $_auth, CART_LANGUAGE, '', true, true, true, true);
			} else {
				$params['type'] = 'extended';
				$params['pid'] = $product_id;
				
				list($product) = fn_get_products($params);
				$product = reset($product);
			}
		
			if (empty($product)) {
				return array(CONTROLLER_STATUS_NO_PAGE);
			}
			
			$product['changed_option'] = isset($product_data['changed_option']) ? reset($product_data['changed_option']) : '';
			$product['selected_options'] = $selected_options;
			if (!empty($_data['amount'])) {
				$product['selected_amount'] = $_data['amount'];
			}
			
			// Get specific settings
			$params = array(
				'get_icon' => isset($_REQUEST['additional_info']['get_icon']) ? $_REQUEST['additional_info']['get_icon'] : false,
				'get_detailed' => isset($_REQUEST['additional_info']['get_detailed']) ? $_REQUEST['additional_info']['get_detailed'] : false,
				'get_options' => isset($_REQUEST['additional_info']['get_options']) ? $_REQUEST['additional_info']['get_options'] : true,
				'get_discounts' => isset($_REQUEST['additional_info']['get_discounts']) ? $_REQUEST['additional_info']['get_discounts'] : true,
				'get_features' => isset($_REQUEST['additional_info']['get_features']) ? $_REQUEST['additional_info']['get_features'] : false,
			);
			
			fn_set_hook('get_additional_information', $product, $product_data);
			
			fn_gather_additional_product_data($product, $params['get_icon'], $params['get_detailed'], $params['get_options'], $params['get_discounts'], $params['get_features']);
			if (isset($product['inventory_amount'])) {
				$product['amount'] = $product['inventory_amount'];
			}
			
			$view->assign('product', $product);
			
			// Update the images in the list/grid templates
			if (!empty($_REQUEST['image'])) {
				foreach ($_REQUEST['image'] as $div_id => $value) {
					list($obj_id, $width, $height, $type) = explode(',', $value);
					$images_data[$div_id] = array(
						'obj_id' => $obj_id,
						'width' => $width,
						'height' => $height,
						'type' => $type,
					);
				}
				
				$view->assign('images', $images_data);
			}
			
			if (AREA == 'C') {
				if (!empty($_REQUEST['appearance']['details_page'])) {
					$display_tpl = 'views/products/view.tpl';
				} else {
					$display_tpl = 'common_templates/product_data.tpl';
				}
			} else {
				$display_tpl = 'views/products/components/select_product_options.tpl';
				$view->assign('product_options', $product['product_options']);
			}
			
		} else {
			// Cart data
			if (!defined('CHECKOUT')) {
				define('CHECKOUT', true);
			}
			
			unset($_REQUEST['cart_products']['custom_files']);
			$cart_products = $_REQUEST['cart_products'];
			if (!empty($cart_products)) {
				foreach ($cart_products as $cart_id => $product) {
					if (!empty($product['object_id'])) {
						unset($cart_products[$cart_id]);
						$cart_products[$product['object_id']] = $product;
					}
				}
			}
			
			$_cart = $_SESSION['cart'];
			
			if (AREA == 'A') {
				$_auth = $_SESSION['customer_auth'];
				if (empty($_auth)) {
					$_auth = fn_fill_auth(array(), array(), false, 'C');
				}
			}
			
			foreach ($cart_products as $cart_id => $item) {
				if (isset($_cart['products'][$cart_id])) {
					$amount = isset($item['amount']) ? $item['amount'] : 1;
					$amount = fn_check_amount_in_stock($item['product_id'], $amount, $item['product_options'], $cart_id, $_cart['products'][$cart_id]['is_edp'], 0, $_cart);
					
					if ($amount === false) {
						unset($_cart['products'][$cart_id]);
						continue;
					}
					
					$_cart['products'][$cart_id]['amount'] = $amount;
					$_cart['products'][$cart_id]['product_options'] = isset($item['product_options']) ? $item['product_options'] : array();
					
					if (!empty($item['object_id'])) {
						$_cart['products'][$cart_id]['object_id'] = $item['object_id'];
					}
				}
			}
			
			fn_set_hook('calculate_options', $cart_products, $_cart, $auth);
			
			list ($cart_products) = fn_calculate_cart_content($_cart, $_auth, 'S', true, 'F', true);
			
			if (count($_SESSION['cart']['products']) != count($_cart['products'])) {
				$_cart = $_SESSION['cart'];
				list ($cart_products) = fn_calculate_cart_content($_cart, $_auth, 'S', true, 'F', true);
			}
			
			// Restore the cart_id
			if (!empty($cart_products)) {
				foreach($cart_products as $k => $product) {
					if (!empty($product['object_id'])) {
						$c_product = !empty($_cart['products'][$k]) ? $_cart['products'][$k] : array();
						unset($cart_products[$k], $_cart['products'][$k]);
						$_cart['products'][$product['object_id']] = $c_product;
						$cart_products[$product['object_id']] = $product;
						$k = $product['object_id'];
					}
					
					$cart_products[$k]['changed_option'] = isset($product['object_id']) ? isset($_REQUEST['changed_option'][$product['object_id']]) ? $_REQUEST['changed_option'][$product['object_id']] : '' : isset($_REQUEST['changed_option'][$k]) ? $_REQUEST['changed_option'][$k] : '' ;
					fn_gather_additional_product_data($cart_products[$k], true, true, true, false);
				}
			}
			
			Registry::set('navigation', array());
			$view->assign('cart_products', $cart_products);
			$view->assign('cart', $_cart);
			
			if (AREA == 'C') {
				$display_tpl = 'views/checkout/components/cart_items.tpl';
			} else {
				$display_tpl = 'views/order_management/products.tpl';
			}
		}
		
		$data = isset($product_data) ? $product_data : $cart_products;
		fn_set_hook('after_options_calculation', $mode, $data);
		
		$view->display($display_tpl);
		
		exit;
	}
}

if ($mode == 'picker') {

	$params = $_REQUEST;
	$params['type'] = 'extended';
	$params['skip_view'] = 'Y';
	
	list($products, $search) = fn_get_products($params, AREA == 'C' ? Registry::get('settings.Appearance.products_per_page') : Registry::get('settings.Appearance.admin_products_per_page'));

	if (!empty($_REQUEST['display']) || (AREA == 'C' && !defined('EVENT_OWNER'))) {
		foreach ($products as $k => $v) {
			fn_gather_additional_product_data($products[$k], true, true, true, true);
		}
	}

	if (!empty($products)){
		foreach($products as $k=>$v){
			$products[$k]['options'] = fn_get_product_options($v['product_id'], DESCR_SL, true, false, true);
			$products[$k]['exceptions'] = fn_get_product_exceptions($v['product_id']);
			if (!empty($products[$k]['exceptions'])) {
				foreach($products[$k]['exceptions'] as $v) {
					$products[$k]['exception_combinations'][fn_get_options_combination($v['combination'])] = '';
				}
			}
		}
	}

	$view->assign('products', $products);
	$view->assign('search', $search);

	$view->display('pickers/products_picker_contents.tpl');
	exit;

}

?>