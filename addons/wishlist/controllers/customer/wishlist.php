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
// $Id: wishlist.php 10554 2010-08-31 07:48:59Z alexions $
//

if ( !defined('AREA') ) { die('Access denied'); }

$_SESSION['wishlist'] = isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : array();
$wishlist = & $_SESSION['wishlist'];
$_SESSION['continue_url'] = isset($_SESSION['continue_url']) ? $_SESSION['continue_url'] : '';
$auth = & $_SESSION['auth'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Add product to the wishlist
	if ($mode == 'add') {
		// wishlist is empty, create it
		if (empty($wishlist)) {
			$wishlist = array(
				'products' => array()
			);
		}

		$prev_wishlist = $wishlist['products'];

		$product_ids = fn_add_product_to_wishlist($_REQUEST['product_data'], $wishlist, $auth);

		fn_save_cart_content($wishlist, $auth['user_id'], 'W');

		$added_products = array_diff_assoc($wishlist['products'], $prev_wishlist);

		if (defined('AJAX_REQUEST')) {
			if (!empty($added_products)) {
				foreach ($added_products as $key => $data) {
					$product = fn_get_product_data($data['product_id'], $auth);
					$product['extra'] = !empty($data['extra']) ? $data['extra'] : array();
					fn_gather_additional_product_data($product, true);
					$added_products[$key]['product_option_data'] = fn_get_selected_product_options_info($data['product_options']);
					$added_products[$key]['display_price'] = $product['price'];
					$added_products[$key]['amount'] = empty($data['amount']) ? 1 : $data['amount'];
				}
				$view->assign('added_products', $added_products);
				$title = fn_get_lang_var('product_added_to_wl');
			} else {
				if ($product_ids) {
					$view->assign('empty_text', fn_get_lang_var('product_in_wishlist'));
					$title = fn_get_lang_var('notice');
				} else {
					exit;
				}
			}
			$view->assign('n_type', 'L');
			$msg = $view->display('views/products/components/product_notification.tpl', false);
			fn_set_notification('L', $title, $msg);
			exit;
		}
		unset($_REQUEST['redirect_url']);
	}

	return array(CONTROLLER_STATUS_OK, "wishlist.view");
}

if ($mode == 'clear') {
	$wishlist = array();

	fn_save_cart_content($wishlist, $auth['user_id'], 'W');
	return array(CONTROLLER_STATUS_REDIRECT, "wishlist.view");

} elseif ($mode == 'delete' && !empty($_REQUEST['cart_id'])) {
	fn_delete_wishlist_product($wishlist, $_REQUEST['cart_id']);

	fn_save_cart_content($wishlist, $auth['user_id'], 'W');
	return array(CONTROLLER_STATUS_OK, "wishlist.view");

} elseif ($mode == 'view') {

	fn_add_breadcrumb(fn_get_lang_var('wishlist_content'));

	$products = !empty($wishlist['products']) ? $wishlist['products'] : array();

	if (!empty($products)) {
		foreach($products as $k => $v) {
			$extra = $v['extra'];
			if (!empty($v['product_options'])) {
				$_options = $v['product_options'];
			}
			$products[$k] = fn_get_product_data($v['product_id'], $auth);
			if (empty($products[$k])) {
				unset($products[$k], $wishlist['products'][$k]);
				continue;
			}
			$products[$k]['extra'] = empty($products[$k]['extra']) ? array() : $products[$k]['extra'];
			$products[$k]['extra'] = array_merge($products[$k]['extra'], $extra);
			
			if (isset($products[$k]['extra']['product_options']) || $_options) {
				$products[$k]['selected_options'] = empty($products[$k]['extra']['product_options']) ? $_options : $products[$k]['extra']['product_options'];
			}
			
			fn_gather_additional_product_data($products[$k], true);
			/*$products[$k]['product_options'] = fn_get_selected_product_options($v['product_id'], $v['product_options'], CART_LANGUAGE);
			$products[$k]['price'] = fn_apply_options_modifiers($v['product_options'], $products[$k]['price'], 'P');*/
		}
	}

	$view->assign('products', $products);
	$view->assign('wishlist', $wishlist);
	$view->assign('continue_url', $_SESSION['continue_url']);
	
} elseif ($mode == 'delete_file' && isset($_REQUEST['cart_id'])) {
	if (isset($wishlist['products'][$_REQUEST['cart_id']]['extra']['custom_files'][$_REQUEST['option_id']][$_REQUEST['file']])) {
		// Delete saved custom file
		$file = $wishlist['products'][$_REQUEST['cart_id']]['extra']['custom_files'][$_REQUEST['option_id']][$_REQUEST['file']];
		
		@unlink($file['path']);
		@unlink($file['path'] . '_thumb');
		
		unset($wishlist['products'][$_REQUEST['cart_id']]['extra']['custom_files'][$_REQUEST['option_id']][$_REQUEST['file']]);
		
		if (defined('AJAX_REQUEST')) {
			fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_product_file_has_been_deleted'));
		}
	}

	return array(CONTROLLER_STATUS_REDIRECT, "wishlist.view");
}

/**
 * Add product to wishlist
 *
 * @param array $product_data array with data for the product to add)(product_id, price, amount, product_options, is_edp)
 * @param array $wishlist wishlist data storage
 * @param array $auth user session data
 * @return mixed array with wishlist IDs for the added products, false otherwise
 */
function fn_add_product_to_wishlist($product_data, &$wishlist, &$auth)
{
	// Check if products have cusom images
	list($product_data, $wishlist) = fn_add_product_options_files($product_data, $wishlist, $auth, false, 'wishlist');
	
	fn_set_hook('pre_add_to_wishlist', $product_data, $wishlist, $auth);

	if (!empty($product_data) && is_array($product_data)) {
		$wishlist_ids = array();
		foreach ($product_data as $product_id => $data) {
			if (empty($data['amount'])) {
				$data['amount'] = 1;
			}
			if (!empty($data['product_id'])) {
				$product_id = $data['product_id'];
			}

			if (empty($data['extra'])) {
				$data['extra'] = array();
			}

			// Add one product
			if (!isset($data['product_options'])) {
				$data['product_options'] = fn_get_default_product_options($product_id);
			}

			// Generate wishlist id
			$data['extra']['product_options'] = $data['product_options'];
			$wishlist_ids[] = $_id = fn_generate_cart_id($product_id, $data['extra']);
			$wishlist['products'][$_id]['product_id'] = $product_id;
			$wishlist['products'][$_id]['product_options'] = $data['product_options'];
			$wishlist['products'][$_id]['extra'] = $data['extra'];
		}

		return $wishlist_ids;
	} else {
		return false;
	}
}

/**
 * Delete product from the wishlist
 *
 * @param array $wishlist wishlist data storage
 * @param int $wishlist_id ID of the product to delete from wishlist
 * @return mixed array with wishlist IDs for the added products, false otherwise
 */
function fn_delete_wishlist_product(&$wishlist, $wishlist_id)
{
	fn_set_hook('delete_wishlist_product', $wishlist, $wishlist_id);

	if (!empty($wishlist_id)) {
		unset($wishlist['products'][$wishlist_id]);
	}

	return true;
}
?>