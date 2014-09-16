<?php

//
// $Id: order_management.post.php 8319 2009-11-25 09:46:37Z alexey $
//

if ( !defined('AREA') ) { die('Access denied'); }

$_SESSION['cart'] = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$cart = & $_SESSION['cart'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($mode == 'update') {
		if (!empty($cart['products'])) {
			foreach ($cart['products'] as $_id => $product) {
				if (!empty($product['extra']['configuration']) && !empty($product['prev_cart_id']) && $product['prev_cart_id'] != $_id) {
					foreach ($cart['products'] as $aux_id => $aux_product) {
						if (!empty($aux_product['extra']['parent']['configuration']) && $aux_product['extra']['parent']['configuration'] == $product['prev_cart_id']) {
							$cart['products'][$aux_id]['extra']['parent']['configuration'] = $_id;
							$cart['products'][$aux_id]['update_c_id'] = true;
						}
					}
				}
			}
			
			foreach ($cart['products'] as $upd_id => $upd_product) {
				if (!empty($upd_product['update_c_id']) && $upd_product['update_c_id'] == true) {
					$new_id = fn_generate_cart_id($upd_product['product_id'], $upd_product['extra'], false);
					
					if (!isset($cart['products'][$new_id])) {
						unset($upd_product['update_c_id']);
						$cart['products'][$new_id] = $upd_product;
						unset($cart['products'][$upd_id]);
						
						// update taxes
						fn_update_stored_cart_taxes($cart, $upd_id, $new_id, false);
					}
				}
			}
		}
	}
}

?>