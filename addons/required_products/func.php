<?php

//
// $Id: func.php 10555 2010-08-31 08:06:47Z alexions $
//

if (!defined('AREA')) { die('Access denied'); }

function fn_required_products_get_additional_product_data(&$product, $auth, $get_options)
{
	if (empty($product['users_only'])) {
		$ids = db_get_fields('SELECT required_id FROM ?:product_required_products WHERE product_id = ?i', $product['product_id']);
		$have = fn_required_products_get_existent($auth, $ids);

		if (count($ids)) {
			$product['have_required'] = 'Y';

			if (!empty($have) && $have >= $ids) {
				$product['required_products_'] = 'Y';
			} else {
				$product['required_products_'] = 'N';
			}

			if (!empty($have) && count($have) >= count($ids)) {
				$product['can_add_to_cart'] = 'Y';
			} else {
				$product['can_add_to_cart'] = 'N';
			}
		} else {
			$product['have_required'] = 'N';
		}
	}
}

function fn_required_products_get_products($params, $fields, $sortings, $condition, $join, $sorting, $group_by)
{
	if (!empty($params['for_required_product'])) {
		$join .= " LEFT JOIN ?:product_required_products ON products.product_id = ?:product_required_products.required_id";
		$condition .= db_quote(" AND ?:product_required_products.product_id = ?i", $params['for_required_product']);
	}

}

function fn_required_products_get_product_data_more(&$product, &$auth)
{
	if (!empty($product['product_id'])) {

		list($requered) = fn_get_products(array ('for_required_product' => $product['product_id'], 'type' => 'extended'));

		if (count($requered)) {
			$product['have_required'] = 'Y';

			$ids = array ();
			foreach ($requered as $entry) {
				$ids[] = $entry['product_id'];
			}

			$have = fn_required_products_get_existent($auth, $ids, false);

			$product['required_products'] = array ();

			foreach ($requered as $entry) {
				$id = $entry['product_id'];

				fn_gather_additional_product_data($entry, true, true, true, true, true);

				$product['required_products'][$id] = $entry;
				$product['required_products'][$id]['bought'] = ($have && in_array($id, $have)) ? 'Y' : 'N';
			}

			if (!empty($have) && count($have) >= count($ids)) {
				$product['can_add_to_cart'] = 'Y';
			} else {
				$product['can_add_to_cart'] = 'N';
			}
		} else {
			$product['have_required'] = 'N';
		}
	}
}

function fn_required_products_in_cart($auth, $ids)
{
	$data = array ();

	if (!empty($_SESSION['cart']) && !empty($_SESSION['cart']['products'])) {
		foreach ($ids as $id) {
			foreach ($_SESSION['cart']['products'] as $entry) {
				if ($entry['product_id'] == $id) {
					$data[] = $id;
				}
			}
		}
	}

	$data = array_unique($data);

	return $data;
}

function fn_required_products_get_existent($auth, $ids, $look_in_cart = true)
{
	if (empty($ids)) {
		return false;
	}

	if (!empty($auth['user_id'])) {
		$data = db_get_fields('SELECT ?:order_details.product_id FROM ?:orders LEFT JOIN ?:order_details ON ?:orders.order_id = ?:order_details.order_id WHERE ?:orders.status IN (?a) AND ?:orders.user_id = ?i AND ?:order_details.product_id IN (?n) GROUP BY ?:order_details.product_id', array ('P', 'C'), $auth['user_id'], $ids);
	} else {
		$data = array ();
	}

	if ($look_in_cart) {
		$data = array_merge($data, fn_required_products_in_cart($auth, $ids));
		$data = array_unique($data);
	}

	return $data;
}

function fn_required_products_pre_add_to_cart(&$product_data, &$cart, $auth, $update)
{
	foreach ($product_data as $key => $entry) {
		if (!empty($entry['amount']) && !empty($entry['product_id'])) {
			$ids = db_get_fields('SELECT req_prod.required_id FROM ?:product_required_products as req_prod LEFT JOIN ?:products ON req_prod.required_id = ?:products.product_id WHERE req_prod.product_id = ?i AND ?:products.status != ?s', $entry['product_id'], 'D');

			if (!empty($ids)) {
				$have = fn_required_products_get_existent($auth, $ids);

				if (empty($have) || count($have) != count($ids)) {
					$products_to_cart = array_diff($ids, $have);
					$out_of_stock = array();
					$amounts = array();
					foreach ($products_to_cart as $id) {
						$amounts[$id] = fn_check_amount_in_stock($id, 1, fn_get_default_product_options($id), 0, 'N', 0, $cart);
						if (!$amounts[$id]) {
							$out_of_stock[] = $id;
						}
					}
					if (empty($out_of_stock)) {
						$msg = fn_get_lang_var('required_products_added');
						foreach ($amounts as $id => $v) {
							$product_data[$id] = array('product_id' => $id, 'amount' => $v);
							$msg .= "<br />" . fn_get_product_name($id);
						}
					} else {
						unset($product_data[$key]);
						$msg = fn_get_lang_var('required_products_out_of_stock');
						foreach ($out_of_stock as $id) {
							$msg .= "<br />" . fn_get_product_name($id);
						}
					}
					fn_set_notification('N', fn_get_lang_var('notice'), $msg);
				}
			}
		}
	}

	return true;
}

function fn_required_products_delete_cart_product(&$cart, $cart_id, $full_erase)
{
	$auth = !empty($_SESSION['auth']['user_id']) ? $_SESSION['auth']['user_id'] : array();

	if (!empty($cart_id) && !empty($cart['products'][$cart_id])) {
		$product_id = $cart['products'][$cart_id]['product_id'];

		$products = db_get_fields('SELECT product_id FROM ?:product_required_products WHERE required_id = ?i', $product_id);

		if (count($products)) {
			foreach ($cart['products'] as $key => $product) {
				if (in_array($product['product_id'], $products)) {
					$haved = fn_required_products_get_existent($auth, array ($product_id), false);

					if (!$haved) {
						unset($cart['products'][$key]);
					}
				}
			}
		}
	}

	return true;
}

function fn_required_products_calculate_cart_items(&$cart, &$cart_products, $auth)
{
	if (!empty($cart['products'])) {
		foreach ($cart['products'] as $key => $entry) {
			if (!empty($entry['product_id'])) {
				$ids = db_get_fields('SELECT req_prod.required_id FROM ?:product_required_products as req_prod LEFT JOIN ?:products ON req_prod.required_id = ?:products.product_id WHERE req_prod.product_id = ?i AND ?:products.status != ?s', $entry['product_id'], 'D');

				if (!empty($ids)) {
					$have = fn_required_products_get_existent($auth, $ids);
					if (empty($have) || count($have) != count($ids)) {
						if (empty($entry['extra']['parent'])) {
							$cart['amount'] -= $entry['amount'];
						}
						unset($cart['products'][$key]);
						unset($cart_products[$key]);
					}
				}
			}
		}
	}
}
?>