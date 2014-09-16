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
// $Id: func.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// Delete all links to this product product congiguration module
//
function fn_delete_configurable_product($product_id)
{
	db_query("DELETE FROM ?:conf_class_products WHERE product_id = ?i", $product_id);
	db_query("DELETE FROM ?:conf_group_products WHERE product_id = ?i", $product_id);
	db_query("DELETE FROM ?:conf_product_groups WHERE product_id = ?i", $product_id);

	// If this product was set as default for selection in some group
	$default_ids = db_get_array("SELECT product_id, default_product_ids FROM ?:conf_product_groups WHERE default_product_ids LIKE ?l", "%$product_id%");
	foreach ($default_ids as $key => $value) {
		$def_pr = trim(str_replace("::", ":", str_replace($product_id, "", $value['default_product_ids'])), ":");
		db_query("UPDATE ?:conf_product_groups SET default_product_ids = ?s WHERE product_id = ?i", $def_pr, $value['product_id']);
	}
}

//
// Delete product configuration group
//
function fn_delete_group($group_id)
{
	db_query("DELETE FROM ?:conf_groups WHERE group_id = ?i", $group_id);
	db_query("DELETE FROM ?:conf_group_products WHERE group_id = ?i", $group_id);
	db_query("DELETE FROM ?:conf_product_groups WHERE group_id = ?i", $group_id);
	db_query("DELETE FROM ?:conf_group_descriptions WHERE group_id = ?i", $group_id);

	fn_delete_image_pairs($group_id, 'conf_group');

	// Reset all classes in this group
	db_query("UPDATE ?:conf_classes SET group_id = 0 WHERE group_id = ?i", $group_id);
}

//
// Delete product configuration class
//
function fn_delete_class($class_id)
{
	db_query("DELETE FROM ?:conf_classes WHERE class_id = ?i", $class_id);
	db_query("DELETE FROM ?:conf_class_products WHERE class_id = ?i", $class_id);
	db_query("DELETE FROM ?:conf_compatible_classes WHERE slave_class_id = ?i OR master_class_id = ?i", $class_id, $class_id);
	db_query("DELETE FROM ?:conf_class_descriptions WHERE class_id = ?i", $class_id);
}

function fn_product_configurator_get_group_name($group_id, $lang_code = CART_LANGUAGE)
{
	if (!empty($group_id)) {
		return db_get_field("SELECT configurator_group_name FROM ?:conf_group_descriptions WHERE group_id = ?i AND lang_code = ?s", $group_id, $lang_code);
	}

	return false;
}

function fn_product_configurator_get_class_name($class_id, $lang_code = CART_LANGUAGE)
{
	if (!empty($class_id)) {
		return db_get_field("SELECT class_name FROM ?:conf_class_descriptions WHERE class_id = ?i AND lang_code = ?s", $class_id, $lang_code);
	}

	return false;
}

function fn_product_configurator_calculate_cart(&$cart, &$cart_products)
{
	if (isset($cart['products']) && is_array($cart['products'])) {
		foreach ($cart['products'] as $key => $value) {
			if (!empty($value['extra']['configuration'])) {
				foreach ($cart_products as $k => $v) {
					if (!empty($cart['products'][$k]['extra']['parent']['configuration']) && $cart['products'][$k]['extra']['parent']['configuration'] == $key) {
						$cart_products[$key]['subtotal'] += $cart_products[$k]['subtotal'];
						$cart_products[$key]['display_subtotal'] += $cart_products[$k]['display_subtotal'];
						$cart_products[$key]['original_price'] += $cart_products[$k]['original_price'];
						$cart_products[$key]['price'] += $cart_products[$k]['price'];
						$cart_products[$key]['display_price'] += $cart_products[$k]['display_price'];

						if (!empty($cart_products[$k]['tax_summary'])) {
							if (isset($cart_products[$key]['tax_summary'])) {
								$cart_products[$key]['tax_summary']['included'] += $cart_products[$k]['tax_summary']['included'];
								$cart_products[$key]['tax_summary']['added'] += $cart_products[$k]['tax_summary']['added'];
								$cart_products[$key]['tax_summary']['total'] += $cart_products[$k]['tax_summary']['total'];
							} else {
								$cart_products[$key]['tax_summary']['included'] = $cart_products[$k]['tax_summary']['included'];
								$cart_products[$key]['tax_summary']['added'] = $cart_products[$k]['tax_summary']['added'];
								$cart_products[$key]['tax_summary']['total'] = $cart_products[$k]['tax_summary']['total'];
							}
						}
						if (!empty($cart_products[$k]['discount'])) {
							$cart_products[$key]['discount'] = (!empty($cart_products[$key]['discount']) ? $cart_products[$key]['discount'] : 0) + $cart_products[$k]['discount'];
						}
						if (!empty($cart_products[$k]['tax_value'])) {
							$cart_products[$key]['tax_value'] = (!empty($cart_products[$key]['tax_value']) ? $cart_products[$key]['tax_value'] : 0) + $cart_products[$k]['tax_value'];
						}
					}
				}
				$cart['products'][$key]['display_price'] = $cart_products[$key]['display_price'];
			}
		}
	}
}

//
// If product is configurable and we want to delete it then delete all its subproducts
//
function fn_product_configurator_delete_cart_product(&$cart, &$cart_id, $full_erase)
{

	if ($full_erase == false) {
		return false;
	}

	if (!empty($cart['products'][$cart_id]['extra']['configuration'])) {
		foreach ($cart['products'] as $key => $item) {
			if (!empty($item['extra']['parent']['configuration']) && $item['extra']['parent']['configuration'] == $cart_id) {
				unset($cart['products'][$key]);
			}
		}
	}
	if (!empty($cart['products'][$cart_id]['extra']['parent']['configuration'])) {
		// find the group of the product in configuration
		$product_id = $cart['products'][$cart_id]['product_id'];
		$conf_id = $cart['products'][$cart['products'][$cart_id]['extra']['parent']['configuration']]['product_id'];
		$groups = db_get_fields("SELECT group_id FROM ?:conf_group_products WHERE product_id = ?i", $product_id);
		// If this group is required then do not unset the product
		$required = db_get_field("SELECT required FROM ?:conf_product_groups WHERE group_id IN (?n) AND product_id = ?i", $groups, $conf_id);
		if ($required == 'Y') {
			$product_name = db_get_field("SELECT product FROM ?:product_descriptions WHERE product_id = ?i AND lang_code = ?s", $product_id, CART_LANGUAGE);
			fn_set_notification('W', fn_get_lang_var('warning'), str_replace('[product_name]', $product_name, fn_get_lang_var('required_configuration_group')));
			$cart_id = 0;
		}
	}
	return true;
}

//
// Update amount of all products in configuration due to the configurable product amount
//
function fn_update_conf_amount(&$cart, &$prev_amount)
{
	$rollback = array();
	foreach ($cart['products'] as $cart_id => $cart_item) {
		if (!empty($cart['products'][$cart_id]['extra']['configuration'])) {
			$coef = $cart['products'][$cart_id]['amount']/$prev_amount[$cart_id];
			foreach ($cart['products'] as $key => $item) {
				if (!empty($item['extra']['parent']['configuration']) && $item['extra']['parent']['configuration'] == $cart_id) {
					$new_amount = round($cart['products'][$key]['amount'] * $coef);
					$new_amount = (empty($new_amount)) ? 1 : $new_amount;

					$checked_amount = fn_check_amount_in_stock($item['product_id'], $new_amount, @$item['product_options'], $key, (!empty($item['is_edp']) && $item['is_edp'] == 'Y' ? 'Y' : 'N'), 0, $cart);

					if ($checked_amount < $new_amount) {
						$rollback[] = $cart_id;
						break;
					}

					$cart['products'][$key]['amount'] = $new_amount;
				}
			}
		}
	}

	// If amount of products is less than we try to update to, roll back to previous state
	if (!empty($rollback)) {
		foreach ($rollback as $cart_id) {
			if (!empty($cart['products'][$cart_id]['extra']['configuration'])) {
				foreach ($cart['products'] as $key => $item) {
					if (!empty($item['extra']['parent']['configuration']) && $item['extra']['parent']['configuration'] == $cart_id) {
						$cart['products'][$key]['amount'] = $prev_amount[$cart_id];
					}
				}
				$cart['products'][$cart_id]['amount'] = $prev_amount[$cart_id];
			}
		}
	}

	return true;
}

//
// This function regenerates the cart ID tahing into account the confirable properties of an item
//
function fn_product_configurator_generate_cart_id(&$_cid, $extra, $only_selectable = false)
{

	// Configurable product
	if (!empty($extra['configuration']) && is_array($extra['configuration'])) {
		foreach ($extra['configuration'] as $k => $v) {
			$_cid[] = $k;
			if (is_array($v)) {
				foreach ($v as $_val) {
					$_cid[] = $_val;
				}
			} else {
				$_cid[] = $v;
			}
			
		}
	}

	// Product in configuration
	if (!empty($extra['parent']['configuration'])) {
		$_cid[] = $extra['parent']['configuration'];
	}

	return true;
}

//
// This function clones product configuration
//
function fn_product_configurator_clone_product($product_id, $pid)
{

	$configuration = db_get_array("SELECT * FROM ?:conf_product_groups WHERE product_id = ?i", $product_id);
	if (empty($configuration)) {
		return false;
	}
	if (is_array($configuration)) {
		foreach ($configuration as $k => $v) {
			$v['product_id'] = $pid;
			db_query("INSERT INTO ?:conf_product_groups ?e", $v);
		}
	}

	return true;
}

function fn_product_configurator_get_products(&$params, &$fields, &$sortings, &$condition, &$join)
{
	$sortings['configurable'] = 'products.product_type';

	if (!empty($params['configurable'])) {
		$condition .= db_quote(' AND products.product_type = ?s', $params['configurable']);
	}

	return true;
}

function fn_product_configurator_get_products_post(&$products)
{
	foreach ($products as $pr_id => $product) {
		if ($product['product_type'] == 'C') {
			$conf_product_groups = db_get_hash_single_array("SELECT ?:conf_product_groups.group_id, ?:conf_product_groups.default_product_ids FROM ?:conf_product_groups LEFT JOIN ?:conf_groups ON ?:conf_product_groups.group_id = ?:conf_groups.group_id WHERE ?:conf_groups.status = 'A' AND ?:conf_product_groups.product_id = ?i", array('group_id', 'default_product_ids'), $product['product_id']);
			
			if (!empty($conf_product_groups)) {
				foreach ($conf_product_groups as $k => $v) {
					if (!empty($v)) {
						$_products = db_get_hash_single_array("SELECT ?:product_prices.product_id, ?:product_prices.price FROM ?:product_prices LEFT JOIN ?:conf_group_products ON ?:conf_group_products.product_id = ?:product_prices.product_id WHERE ?:conf_group_products.group_id = ?i AND ?:product_prices.lower_limit = 1", array('product_id', 'price'), $k);

						$tmp = explode(':', $v);
						foreach ($tmp as $pid) {
							if (!empty($_products[$pid]) && AREA != 'A') {
								$products[$pr_id]['price'] += $_products[$pid];
							}
						}
					}
				}
			}
		}
	}

	return true;
}

function fn_product_configurator_order_products_post(&$products)
{
	foreach ($products as $pr_id => $product) {
		if (!empty($product['extra']['configuration'])) {
			$p_ids = array_values($product['extra']['configuration']);
			$inner_ids = array();
			foreach ($p_ids as $_id => $item) {
				if (is_array($item)) {
					$inner_ids = array_merge($inner_ids, array_values($item));
					unset($p_ids[$_id]);
				}
			}
			$p_ids = array_merge($p_ids, $inner_ids);
			foreach ($products as $product_id => $prod) {
				if (in_array($prod['product_id'], $p_ids)) {
					$products[$pr_id]['subtotal'] += $prod['subtotal'];
				}
			}
		}
	}

	return true;
}

function fn_product_configurator_pre_add_to_cart(&$product_data, &$cart, &$auth, $update)
{
	if ($update == true) {
		foreach ($product_data as $key => $value) {
			if (!empty($cart['products'][$key]['extra']['configuration'])) {

				$product_data[$key]['extra']['configuration'] = $cart['products'][$key]['extra']['configuration'];
				if (!empty($value['product_options'])) {
					$product_data[$key]['extra']['product_options'] = $value['product_options'];
				}

				$cart_id = fn_generate_cart_id($value['product_id'], $product_data[$key]['extra'], false);

				foreach ($cart['products'] as $k => $v) {
					if (isset($v['extra']['parent']['configuration']) && $v['extra']['parent']['configuration'] == $key) {
						$product_data[$k] = array(
							'product_id' => $v['product_id'],
							'amount' => $value['amount'],
							'extra' => array(
								'parent' => array(
									'configuration' => $cart_id
								),
							),
						);
					}
				}
			}
		}

	} else {
		foreach ($product_data as $key => $value) {
			if (!empty($value['cart_id'])) { // if we're editing the configuration, just delete it and add new
				fn_delete_cart_product($cart, $value['cart_id']);
			}

			if (!empty($value['configuration'])) {
				$product_data[$key]['extra']['configuration'] = $value['configuration'];

				if (!empty($value['product_options'])) {
					$product_data[$key]['extra']['product_options'] = $value['product_options'];
				}

				$cart_id = fn_generate_cart_id($key, $product_data[$key]['extra'], false);

				foreach ($value['configuration'] as $group_id => $_product_id) {
					if (is_array($_product_id)) {
						foreach ($_product_id as $_id) {
							if (!isset($product_data[$_id])) {
								$product_data[$_id] = array();
								$product_data[$_id]['product_id'] = $_id;
								$product_data[$_id]['amount'] = $value['amount'];
								$product_data[$_id]['extra']['parent']['configuration'] = $cart_id;
							} elseif (isset($product_data[$_id]['extra']['parent']['configuration']) && $product_data[$_id]['extra']['parent']['configuration'] == $cart_id) {
								$product_data[$_id]['amount'] += $value['amount'];
							}
						}
					} else {
						if (!isset($product_data[$_product_id])) {
							$product_data[$_product_id] = array();
							$product_data[$_product_id]['product_id'] = $_product_id;
							$product_data[$_product_id]['amount'] = $value['amount'];
							$product_data[$_product_id]['extra']['parent']['configuration'] = $cart_id;
						} elseif (isset($product_data[$_product_id]['extra']['parent']['configuration']) &&  $product_data[$_product_id]['extra']['parent']['configuration'] == $cart_id) {
							$product_data[$_product_id]['amount'] += $value['amount'];
						}
					}
				}
				
				$product_data[$key]['extra']['configuration_id'] = $cart_id;
			}
		}
	}
}

function fn_product_configurator_add_to_cart(&$cart, &$product_id, &$_id)
{
	if (isset($cart['products'][$_id]['extra']['parent']['configuration'])) {
		foreach ($cart['products'] as $key => $product) {
			if (isset($product['extra']['configuration_id']) && $product['extra']['configuration_id'] == $cart['products'][$_id]['extra']['parent']['configuration']) {
				$cart['products'][$_id]['extra']['parent']['configuration'] = $key;
				break;
			}
		}
	}
}

/**
 * Prepare configurable product data to add it to wishlist
 *
 * @param array $product_data product data
 * @param array $wishlist wishlist storage
 * @param array $auth user session data
 * @return boolean always true
 */
function fn_product_configurator_pre_add_to_wishlist(&$product_data, &$wishlist, &$auth)
{
	fn_product_configurator_pre_add_to_cart($product_data, $wishlist, $auth, false);

	return true;
}

/**
 * Delete configurable product from the wishlist
 *
 * @param array $wishlist wishlist storage
 * @param array $wishlist_id ID of the product to delete
 * @return boolean always true
 */
function fn_product_configurator_delete_wishlist_product(&$wishlist, &$wishlist_id)
{
	if (!empty($wishlist['products'][$wishlist_id]['extra']['configuration'])) {
		foreach ($wishlist['products'] as $key => $item) {
			if (!empty($item['extra']['parent']['configuration']) && $item['extra']['parent']['configuration'] == $wishlist_id) {
				unset($wishlist['products'][$key]);
			}
		}
	}
	return true;
}

function fn_product_configurator_buy_together_restricted_product($product_id, $auth, $is_restricted, $show_notification)
{
	if ($is_restricted) {
		return true;
	}
	
	$product_data = Registry::get('view')->get_var('product_data');
	
	if (!empty($product_data)) {
		if ($product_data['product_type'] == 'C') {
			$is_restricted = true;
		}
		
	} elseif (!empty($product_id)) {
		$product_data = fn_get_product_data($product_id, $auth, CART_LANGUAGE, '', true, true, true, true);
		
		if ($product_data['product_type'] == 'C') {
			$is_restricted = true;
		}
	}
	
	if ($is_restricted && $show_notification) {
		fn_set_notification('E', fn_get_lang_var('error'), str_replace('[product_name]', $product_data['product'], fn_get_lang_var('buy_together_is_not_compatible_with_configurator')));
	}
}

function fn_product_configurator_calculate_options($cart_products, $cart, $auth)
{
	if (!empty($cart['products'])) {
		foreach ($cart['products'] as $id => &$product) {
			if (!empty($product['extra']['parent']['configuration']) && !empty($cart['products'][$product['extra']['parent']['configuration']]['object_id'])) {
				$product['extra']['parent']['configuration'] = $cart['products'][$product['extra']['parent']['configuration']]['object_id'];
			}
		}
	}
}

function fn_product_configurator_google_products($cart_products, $cart)
{
	if (!empty($cart['products'])) {
		foreach ($cart['products'] as $cart_id => $product) {
			if (!empty($product['extra']['configuration'])) {
				foreach ($cart['products'] as $_id => $_product) {
					if (isset($_product['extra']['parent']['configuration']) && $_product['extra']['parent']['configuration'] == $cart_id) {
						$cart_products[$cart_id]['price'] -= $cart_products[$_id]['price'];
					}
				}
			}
		}
	}
}

?>