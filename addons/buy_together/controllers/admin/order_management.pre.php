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
// $Id: order_management.pre.php 9349 2010-04-30 13:31:02Z alexions $
//

if ( !defined('AREA') ) { die('Access denied'); }

$_SESSION['cart'] = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$cart = & $_SESSION['cart'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($mode == 'update') {
		if (!empty($cart['products'])) {
			foreach ($cart['products'] as $id => $product) {
				if (!empty($product['extra']['buy_together'])) {
					$is_valid = true;
					$_ids = array();
					
					if (isset($_REQUEST['cart_products'][$id])) {
						$allowed_amount = fn_check_amount_in_stock($product['product_id'], $_REQUEST['cart_products'][$id]['amount'], empty($_REQUEST['cart_products'][$id]['product_options']) ? array() : $_REQUEST['cart_products'][$id]['product_options'], $id, $is_edp = 'N', $product['amount'], $cart);
						
						if ($allowed_amount != $_REQUEST['cart_products'][$id]['amount']) {
							$is_valid = false;
						}
						
						$_ids[] = $id;
					}
					
					foreach ($cart['products'] as $aux_id => $aux_product) {
						if (isset($_REQUEST['cart_products'][$aux_id]) && isset($aux_product['extra']['parent']['buy_together']) && $aux_product['extra']['parent']['buy_together'] == $id) {
							if ($is_valid) {
								$amount = $aux_product['extra']['min_qty'] * $_REQUEST['cart_products'][$id]['amount'];
								$allowed_amount = fn_check_amount_in_stock($aux_product['product_id'], $amount, empty($_REQUEST['cart_products'][$aux_id]['product_options']) ? array() : $_REQUEST['cart_products'][$aux_id]['product_options'], $aux_id, $is_edp = 'N', $aux_product['amount'], $cart);
								
								if ($allowed_amount != $amount) {
									$is_valid = false;
								} else {
									$_REQUEST['cart_products'][$aux_id]['amount'] = $amount;
								}
							}
						
							$_ids[] = $id;
						}
					}
					
					if (!$is_valid) {
						foreach ($_ids as $id) {
							unset($_REQUEST['cart_products'][$id]);
						}
					}
				}
			}
		}
	}
}

?>