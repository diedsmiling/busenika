<?php

//
// $Id: order_management.pre.php 8234 2009-11-16 09:34:29Z ivan $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//
	// Delete products from the cart
	//
	if ($mode == 'delete') {

		foreach ($_REQUEST['cart_ids'] as $cart_id) {
			if (isset($cart['products'][$cart_id])) {
				$product = $cart['products'][$cart_id];
				if (!empty($product['extra']['exclude_from_calculate']) && $product['extra']['exclude_from_calculate'] == GIFT_CERTIFICATE_EXCLUDE_PRODUCTS) {
					$cart['deleted_exclude_products'][GIFT_CERTIFICATE_EXCLUDE_PRODUCTS][$cart_id] = array(
						'product_id' => $product['product_id'],
						'in_use_certificate' => $product['extra']['in_use_certificate']
					);
				}

				if (isset($product['extra']['parent']['certificate'])) {
					unset($cart['gift_certificates'][$product['extra']['parent']['certificate']]['products'][$product['product_id']]);
				}
			}
		}
	}
}

?>