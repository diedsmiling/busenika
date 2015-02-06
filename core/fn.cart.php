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
// $Id: fn.cart.php 10596 2010-09-03 10:43:56Z lori $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// Get product description to show it in the cart
//
function fn_get_cart_product_data($hash, &$product, $skip_promotion, &$cart, &$auth, $promotion_amount = 0)
{
	if (!empty($product['product_id'])) {

		$_p_statuses = array('A', 'H');
		$_c_statuses = array('A', 'H');

		$avail_cond = (AREA == 'C') ? " AND (" . fn_find_array_in_set($auth['usergroup_ids'], '?:categories.usergroup_ids', true) . ")" : '';
		$avail_cond .= (AREA == 'C') ? " AND (" . fn_find_array_in_set($auth['usergroup_ids'], '?:products.usergroup_ids', true) . ")" : '';
		$avail_cond .= (AREA == 'C') ? db_quote(' AND ?:categories.status IN (?a) AND ?:products.status IN (?a)', $_c_statuses, $_p_statuses) : '';
		$avail_cond .= (AREA == 'C') ? fn_get_localizations_condition('?:products.localization') : '';

		$join = " INNER JOIN ?:products_categories ON ?:products_categories.product_id = ?:products.product_id INNER JOIN ?:categories ON ?:categories.category_id = ?:products_categories.category_id $avail_cond";

		$_pdata = db_get_row("SELECT ?:products.product_id, ?:products.company_id, GROUP_CONCAT(IF(?:products_categories.link_type = 'M', CONCAT(?:products_categories.category_id, 'M'), ?:products_categories.category_id)) as category_ids, ?:products.product_code, ?:products.weight, ?:products.tracking, ?:product_descriptions.product, ?:product_descriptions.short_description, ?:products.is_edp, ?:products.edp_shipping, ?:products.shipping_freight, ?:products.free_shipping, ?:products.zero_price_action, ?:products.tax_ids, ?:products.qty_step, ?:products.list_qty_count, ?:products.max_qty, ?:products.min_qty, ?:products.amount as in_stock FROM ?:products LEFT JOIN ?:product_descriptions ON ?:product_descriptions.product_id = ?:products.product_id AND ?:product_descriptions.lang_code = ?s ?p WHERE ?:products.product_id = ?i GROUP BY ?:products.product_id", CART_LANGUAGE, $join, $product['product_id']);

		$companies = &Registry::get('s_companies');
		// delete product from cart if supplier or vendor was disabled.
		if (empty($_pdata) || (!empty($_pdata['company_id']) && ($companies[$_pdata['company_id']]['status'] != 'A' || ((PRODUCT_TYPE == 'PROFESSIONAL' || PRODUCT_TYPE == 'COMMUNITY') && Registry::get('settings.Suppliers.enable_suppliers') != 'Y') ))) {
			return false;
		}

		$_pdata['category_ids'] = fn_convert_categories($_pdata['category_ids']);

		$_pdata['options_count'] = db_get_field("SELECT COUNT(*) FROM ?:product_options WHERE product_id = ?i AND status = 'A'", $product['product_id']);

		$_pdata['price'] = fn_get_product_price($product['product_id'], $product['amount'], $auth);

		$_pdata['base_price'] = (isset($product['stored_price']) && $product['stored_price'] == 'Y') ? $product['price'] : $_pdata['price'];

		fn_set_hook('get_cart_product_data', $product['product_id'], $_pdata, $product);

		$product['stored_price'] = empty($product['stored_price']) ? 'N' : $product['stored_price'];
		$product['stored_discount'] = empty($product['stored_discount']) ? 'N' : $product['stored_discount'];
		$product['product_options'] = empty($product['product_options']) ? array() : $product['product_options'];

		if (empty($_pdata['product_id'])) { // FIXME - for deleted products for OM
			unset($cart['products'][$hash]);
			return array();
		}

		if (!empty($_pdata['options_count']) && empty($product['product_options'])) {
			$cart['products'][$hash]['product_options'] = fn_get_default_product_options($product['product_id']);
		}

		if (Registry::get('settings.General.inventory_tracking') == 'Y' && !empty($_pdata['tracking']) && $_pdata['tracking'] == 'O' && !empty($product['selectable_cart_id'])) {
			$_pdata['in_stock'] = db_get_field("SELECT amount FROM ?:product_options_inventory WHERE combination_hash = ?i", $product['selectable_cart_id']);
		}

		if (fn_check_amount_in_stock($product['product_id'], $product['amount'], $product['product_options'], $hash, $_pdata['is_edp'], !empty($product['original_amount']) ? $product['original_amount'] : 0, $cart) == false) {
			unset($cart['products'][$hash]);
			$out_of_stock = true;
			return false;
		}
		$exceptions = fn_get_product_exceptions($product['product_id'], true);
		if (!isset($product['options_type']) || !isset($product['exceptions_type'])) {
			$product = array_merge($product, db_get_row('SELECT options_type, exceptions_type FROM ?:products WHERE product_id = ?i', $product['product_id']));
		}
		
		if (!fn_is_allowed_options_exceptions($exceptions, $product['product_options'], $product['options_type'], $product['exceptions_type']) && !defined('GET_OPTIONS')) {
			fn_set_notification('E', fn_get_lang_var('notice'), str_replace('[product]', $_pdata['product'], fn_get_lang_var('product_options_forbidden_combination')));
			unset($cart['products'][$hash]);
			
			return false;
		}
		if (isset($product['extra']['custom_files'])) {
			$_pdata['extra']['custom_files'] = $product['extra']['custom_files'];
		}

		$_pdata['calculation'] = array();

		if (isset($product['extra']['exclude_from_calculate'])) {
			$_pdata['exclude_from_calculate'] = $product['extra']['exclude_from_calculate'];
			$_pdata['aoc'] = !empty($product['extra']['aoc']);
			$_pdata['price'] = 0;
		} else {
			if ($product['stored_price'] == 'Y') {
				$_pdata['price'] = $product['price'];
			}
		}

		// If price defined and zero price action allows add zero priced product to cart, get price from the database
		if (isset($_pdata['price']) && $_pdata['zero_price_action'] != 'A') {
			$product['price'] = floatval($_pdata['price']);
			$cart['products'][$hash]['price'] = $product['price'];
		}

		$_pdata['original_price'] = $product['price'];

		if ($product['stored_price'] != 'Y' && !isset($product['extra']['exclude_from_calculate'])) {
			if ($_pdata['zero_price_action'] != 'A' || $_pdata['zero_price_action'] == 'A' && empty($product['modifiers_price'])) {
				$_tmp = $product['price'];
				$product['price'] = fn_apply_options_modifiers($product['product_options'], $product['price'], 'P');
				$product['modifiers_price'] = $_pdata['modifiers_price'] = $product['price'] - $_tmp; // modifiers
			}
		} else {
			$product['modifiers_price'] = $_pdata['modifiers_price'] = 0;
		}

		if (isset($product['modifiers_price']) && $_pdata['zero_price_action'] == 'A') {
			$_pdata['base_price'] = $product['price'] - $product['modifiers_price'];
		}

		$_pdata['weight'] = fn_apply_options_modifiers($product['product_options'], $_pdata['weight'], 'W');
		$_pdata['amount'] = $product['amount'];
		$_pdata['price'] = $_pdata['original_price'] = fn_format_price($product['price']);

		$_pdata['stored_price'] = $product['stored_price'];

		if ($cart['options_style'] == 'F') {
			$_pdata['product_options'] = fn_get_selected_product_options($product['product_id'], $product['product_options'], CART_LANGUAGE);
		} elseif ($cart['options_style'] == 'I') {
			$_pdata['product_options'] = fn_get_selected_product_options_info($product['product_options'], CART_LANGUAGE);
		} else {
			$_pdata['product_options'] = $product['product_options'];
		}

		if (($_pdata['free_shipping'] != 'Y' || AREA == 'A') && ($_pdata['is_edp'] != 'Y' || ($_pdata['is_edp'] == 'Y' && $_pdata['edp_shipping'] == 'Y'))) {
			$cart['shipping_required'] = true;
		}

		$cart['products'][$hash]['is_edp'] = (!empty($_pdata['is_edp']) && $_pdata['is_edp'] == 'Y') ? 'Y' : 'N';
		$cart['products'][$hash]['edp_shipping'] = (!empty($_pdata['edp_shipping']) && $_pdata['edp_shipping'] == 'Y') ? 'Y' : 'N';

		if (empty($cart['products'][$hash]['extra']['parent'])) { // count only products without parent
			if ($skip_promotion == true && !empty($promotion_amount)){
				$cart['amount'] += $promotion_amount;
			}else{
				$cart['amount'] += $product['amount'];
			}
		}

		if ($skip_promotion == false) {
			if (empty($cart['order_id'])) {
				fn_promotion_apply('catalog', $_pdata, $auth);
			} else {
				if (empty($product['original_discount'])) {
					$product['original_discount'] = !empty($product['discount']) ? $product['discount'] : 0;
					$cart['products'][$hash]['original_discount'] = $product['original_discount'];
				}
				if (isset($product['discount'])) {
					$_pdata['discount'] = $product['discount'];
					$_pdata['price'] -= $product['discount'];

					if ($_pdata['price'] < 0) {
						$_pdata['discount'] += $_pdata['price'];
						$_pdata['price'] = 0;
					}
				}
			}

			// apply discount to the product
			if (!empty($_pdata['discount'])) {
				$cart['use_discount'] = true;
			}
		}

		if (!empty($product['object_id'])) {
			$_pdata['object_id'] = $product['object_id'];
		}

		$_pdata['stored_discount'] = $product['stored_discount'];
		$cart['products'][$hash]['modifiers_price'] = $product['modifiers_price'];

		$_pdata['subtotal'] = $_pdata['price'] * $product['amount'];
		$cart['original_subtotal'] += $_pdata['original_price'] * $product['amount'];
		$cart['subtotal'] += $_pdata['subtotal'];

		return $_pdata;
	}

	return array();
}

function fn_update_cart_data(&$cart, &$cart_products)
{
	foreach ($cart_products as $k => $v) {
		if (isset($cart['products'][$k])) {
			if (!isset($v['base_price'])) {
				$cart['products'][$k]['base_price'] = $v['base_price'] = $cart['products'][$k]['stored_price'] != 'Y' ? $v['price'] : $cart['products'][$k]['price'];
			} else {
				if ($cart['products'][$k]['stored_price'] == 'Y') {
					$cart_products[$k]['base_price'] = $cart['products'][$k]['price'];
				}
			}

			$cart['products'][$k]['base_price'] = $cart['products'][$k]['stored_price'] != 'Y' ? $v['base_price'] : $cart['products'][$k]['price'];
			$cart['products'][$k]['price'] = $cart['products'][$k]['stored_price'] != 'Y' ? $v['price'] : $cart['products'][$k]['price'];
			if (isset($v['discount'])) {
				$cart['products'][$k]['discount'] = $v['discount'];
			}
			if (isset($v['promotions'])) {
				$cart['products'][$k]['promotions'] = $v['promotions'];
			}
		}
	}
}

//
// Get payment methods
//
function fn_get_payment_methods(&$auth, $lang_code = CART_LANGUAGE)
{
	$condition = '';
	if (AREA == 'C') {
		$condition .= " AND (" . fn_find_array_in_set($auth['usergroup_ids'], '?:payments.usergroup_ids', true) . ")";
		$condition .= " AND ?:payments.status = 'A' AND (?:payment_processors.type != 'C' OR ?:payments.processor_id = 0)";
		$condition .= fn_get_localizations_condition('?:payments.localization');
	}

	$payment_methods = db_get_hash_array("SELECT ?:payments.payment_id, ?:payments.a_surcharge, ?:payments.p_surcharge, ?:payment_descriptions.* FROM ?:payments LEFT JOIN ?:payment_descriptions ON ?:payments.payment_id = ?:payment_descriptions.payment_id AND ?:payment_descriptions.lang_code = ?s LEFT JOIN ?:payment_processors ON ?:payment_processors.processor_id = ?:payments.processor_id WHERE 1 $condition ORDER BY ?:payments.position", 'payment_id', $lang_code);
	fn_set_hook('get_payment_methods', $payment_methods);

	return $payment_methods;
}

function fn_get_simple_payment_methods($lang_code = CART_LANGUAGE)
{
	return db_get_hash_single_array("SELECT ?:payments.payment_id, ?:payment_descriptions.payment FROM ?:payments LEFT JOIN ?:payment_descriptions ON ?:payments.payment_id = ?:payment_descriptions.payment_id AND ?:payment_descriptions.lang_code = ?s WHERE status = 'A' ORDER BY ?:payments.position, ?:payment_descriptions.payment", array('payment_id', 'payment'), $lang_code);
}

//
// Get payment method data
//
function fn_get_payment_method_data($payment_id, $lang_code = CART_LANGUAGE)
{
	$payment = db_get_row("SELECT ?:payments.*, ?:payment_descriptions.*, ?:payment_processors.processor, ?:payments.params FROM ?:payments LEFT JOIN ?:payment_processors ON ?:payment_processors.processor_id = ?:payments.processor_id LEFT JOIN ?:payment_descriptions ON ?:payments.payment_id = ?:payment_descriptions.payment_id AND ?:payment_descriptions.lang_code = ?s WHERE ?:payments.payment_id = ?i", $lang_code, $payment_id);

	$payment['params'] = (!empty($payment['params'])) ? unserialize($payment['params']) : '';

	fn_set_hook('summary_get_payment_method', $payment_id, $payment);

	return $payment;
}

//
// Update product amount
//
// returns true if inventory successfully updated and false if amount
// is negative is allow_negative_amount option set to false

function fn_update_product_amount($product_id, $amount, $product_options, $sign)
{
	if (Registry::get('settings.General.inventory_tracking') != 'Y') {
		return true;
	}

	$tracking = db_get_field("SELECT tracking FROM ?:products WHERE product_id = ?i", $product_id);

	if ($tracking == 'D') {
		return true;
	}

	if ($tracking == 'B') {
		$product = db_get_row("SELECT amount, product_code FROM ?:products WHERE product_id = ?i", $product_id);
		$current_amount = $product['amount'];
		$product_code = $product['product_code'];
	} else {
		$cart_id = fn_generate_cart_id($product_id, array('product_options' => $product_options), true);
		$product = db_get_row("SELECT amount, product_code FROM ?:product_options_inventory WHERE combination_hash = ?i", $cart_id);
		$current_amount = empty($product['amount']) ? 0 : $product['amount'];
		
		if (empty($product['product_code'])) {
			$product_code = db_get_field("SELECT product_code FROM ?:products WHERE product_id = ?i", $product_id);
		} else {
			$product_code = $product['product_code'];
		}
	}

	if ($sign == '-') {
		$new_amount = $current_amount - $amount;

		// Notify administrator about inventory low stock
		if ($new_amount <= Registry::get('settings.General.low_stock_threshold')) {
			// Log product low-stock
			fn_log_event('products', 'low_stock', array (
				'product_id' => $product_id
			));

			$lang_code = Registry::get('settings.Appearance.admin_default_language');
			Registry::get('view_mail')->assign('new_amount', $new_amount);
			Registry::get('view_mail')->assign('product_id', $product_id);
			Registry::get('view_mail')->assign('product_code', $product_code);
			Registry::get('view_mail')->assign('product', db_get_field("SELECT product FROM ?:product_descriptions WHERE product_id = ?i AND lang_code = ?s", $product_id, $lang_code));
			if ($tracking == 'O') {
				Registry::get('view_mail')->assign('product_options', fn_get_selected_product_options_info($product_options, $lang_code));
			}
			fn_send_mail(Registry::get('settings.Company.company_orders_department'), Registry::get('settings.Company.company_orders_department'), 'orders/low_stock_subj.tpl', 'orders/low_stock.tpl', '', $lang_code);
		}

		if ($new_amount < 0 && Registry::get('settings.General.allow_negative_amount') != 'Y') {
			return false;
		}
	} else {
		$new_amount = $current_amount + $amount;
	}

	fn_set_hook('update_product_amount', $new_amount, $product_id);

	if ($tracking == 'B') {
		db_query("UPDATE ?:products SET amount = ?i WHERE product_id = ?i", $new_amount, $product_id);
	} else {
		db_query("UPDATE ?:product_options_inventory SET amount = ?i WHERE combination_hash = ?i", $new_amount, $cart_id);
	}

	return true;
}

/**
 * Order placing function
 *
 * @param array $cart
 * @param array $auth
 * @param string $action
 * @return int order_id or bool FALSE
 */
function fn_place_order(&$cart, &$auth, $action = '', $parent_order_id = 0)
{
	$allow = true;

	fn_set_hook('pre_place_order', $cart, $allow);

	if ($allow == true && !fn_cart_is_empty($cart)) {
		$ip = fn_get_ip();
		$__order_status = STATUS_INCOMPLETED_ORDER;
		$order = fn_check_table_fields($cart, 'orders');
		$order = fn_array_merge($order, fn_check_table_fields($cart['user_data'], 'orders'));

		// filter hidden fields, which were hidden to checkout
		fn_filter_hidden_profile_fields($order, 'O');

		// If the contact information fields were disabled, fill the information from the billing/shipping
		Registry::get('settings.General.address_position') == 'billing_first' ? $address_zone = 'b' : $address_zone = 's';
		if (!empty($order['firstname']) || !empty($order[$address_zone . '_firstname'])) {
			$order['firstname'] = empty($order['firstname']) && !empty($order[$address_zone . '_firstname']) ? $order[$address_zone . '_firstname'] : $order['firstname'];
		}
		if (!empty($order['lastname']) || !empty($order[$address_zone . '_lastname'])) {
			$order['lastname'] = empty($order['lastname']) && !empty($order[$address_zone . '_lastname']) ? $order[$address_zone . '_lastname'] : $order['lastname'];
		}
		if (!empty($order['phone']) || !empty($order[$address_zone . '_phone'])) {
			$order['phone'] = empty($order['phone']) && !empty($order[$address_zone . '_phone']) ? $order[$address_zone . '_phone'] : $order['phone'];
		}

		$order['user_id'] = $auth['user_id'];
		$order['timestamp'] = TIME;
		$order['lang_code'] = CART_LANGUAGE;
		$order['tax_exempt'] = $auth['tax_exempt'];
		$order['status'] = STATUS_INCOMPLETED_ORDER; // incomplete by default to increase inventory
		$order['ip_address'] = $ip['host'];

		$cart['companies'] = fn_get_products_companies($cart['products']);

		$order['is_parent_order'] = 'N';
		if (PRODUCT_TYPE == 'MULTIVENDOR') {
			$order['parent_order_id'] = $parent_order_id;
			if (count($cart['companies']) > 1) {
				$order['is_parent_order'] = 'Y';
				$__order_status = $order['status'] = STATUS_PARENT_ORDER;
			} else {
				$order['company_id'] = key($cart['companies']);
			}
		}

		$order['promotions'] = serialize(!empty($cart['promotions']) ? $cart['promotions'] : array());
		if (!empty($cart['promotions'])) {
			$order['promotion_ids'] = implode(', ', array_keys($cart['promotions']));
		}

		$order['shipping_ids'] = !empty($cart['shipping']) ? fn_create_set(array_keys($cart['shipping'])) : '';

		if (!empty($cart['payment_surcharge'])) {
			$cart['total'] += $cart['payment_surcharge'];
			$order['total'] = $cart['total'];
		}

		if (!empty($cart['payment_info'])) {
			$ccards = fn_get_static_data_section('C', true);
			if (!empty($cart['payment_info']['card']) && !empty($ccards[$cart['payment_info']['card']])) {
				// Check if cvv2 number required and unset it if not
				if ($ccards[$cart['payment_info']['card']]['param_2'] != 'Y') {
					unset($cart['payment_info']['cvv2']);
				}
				// Check if start date exists and required and convert it to string
				if ($ccards[$cart['payment_info']['card']]['param_3'] != 'Y') {
					unset($cart['payment_info']['start_year'], $cart['payment_info']['start_month']);
				}
				// Check if issue number required
				if ($ccards[$cart['payment_info']['card']]['param_4'] != 'Y') {
					unset($cart['payment_info']['issue_number']);
				}
			}
		}

		// We're editing existing order
		if (!empty($order['order_id']) && $order['is_parent_order'] != 'Y') {

			$_tmp = db_get_row("SELECT status, ip_address, details, timestamp, lang_code FROM ?:orders WHERE order_id = ?i", $order['order_id']);
			$order['ip_address'] = $_tmp['ip_address']; // Leave original customers IP address
			$order['details'] = $_tmp['details']; // Leave order details
			$order['timestamp'] = $_tmp['timestamp']; // Leave the original date
			$order['lang_code'] = $_tmp['lang_code']; // Leave the original language

			if ($action == 'save') {
				$__order_status = $_tmp['status']; // Get the original order status
			}

			fn_change_order_status($order['order_id'], STATUS_INCOMPLETED_ORDER, $_tmp['status'], fn_get_notification_rules(array(), false)); // incomplete the order to increase inventory amount.

			db_query("DELETE FROM ?:orders WHERE order_id = ?i", $order['order_id']);
			db_query("DELETE FROM ?:order_details WHERE order_id = ?i", $order['order_id']);
			db_query("DELETE FROM ?:profile_fields_data WHERE object_id = ?i AND object_type = 'O'", $order['order_id']);
			db_query("DELETE FROM ?:order_data WHERE order_id = ?i AND type IN ('T', 'C', 'P')", $order['order_id']);

			fn_set_hook('edit_place_order', $order['order_id']);
		}

		if (!empty($cart['rewrite_order_id'])) {
			$order['order_id'] = array_shift($cart['rewrite_order_id']);
		}
		$order_id = db_query("INSERT INTO ?:orders ?e", $order);

		// Log order creation
		fn_log_event('orders', 'create', array(
			'order_id' => $order_id
		));

		fn_store_profile_fields($cart['user_data'], $order_id, 'O');

		$order['order_id'] = $order_id;
		// If customer is not logged in, store order ids in the session
		if (empty($auth['user_id'])) {
			$auth['order_ids'][] = $order_id;
		}
		// Add order details data
		if (!empty($order_id)) {
			if (!empty($cart['products'])) {
				foreach ((array)$cart['products'] as $k => $v) {
					$product_code = '';
					$extra = empty($v['extra']) ? array() : $v['extra'];
					$v['discount'] = empty($v['discount']) ? 0 : $v['discount'];

					$extra['product'] = empty($v['product']) ? fn_get_product_name($v['product_id']) : $v['product'];

					$extra['company_id'] = $v['company_id'];

					if (isset($v['is_edp'])) {
						$extra['is_edp'] = $v['is_edp'];
					}
					if (isset($v['edp_shipping'])) {
						$extra['edp_shipping'] = $v['edp_shipping'];
					}
					if (!empty($v['discount'])) {
						$extra['discount'] = $v['discount'];
					}
					if (isset($v['base_price'])) {
						$extra['base_price'] = floatval($v['base_price']);
					}
					if (!empty($v['promotions'])) {
						$extra['promotions'] = $v['promotions'];
					}
					if (!empty($v['stored_price']) && $v['stored_price'] == 'Y') {
						$extra['stored_price'] = 'Y';
					}

					if (!empty($v['product_options'])) {
						$_options = fn_get_product_options($v['product_id']);
						if (!empty($_options)) {
							foreach ($_options as $option_id => $option) {
								if (!isset($v['product_options'][$option_id])) {
									$v['product_options'][$option_id] = '';
								}
							}
						}
						
						$extra['product_options'] = $v['product_options'];
						$cart_id = fn_generate_cart_id($v['product_id'], array('product_options' => $v['product_options']), true);
						$tracking = db_get_field("SELECT tracking FROM ?:products WHERE product_id = ?i", $v['product_id']);
						
						if ($tracking == 'O') {
							$product_code = db_get_field("SELECT product_code FROM ?:product_options_inventory WHERE combination_hash = ?i", $cart_id);
						}

						$extra['product_options_value'] = fn_get_selected_product_options_info($v['product_options']);
					} else {
						$v['product_options'] = array();
					}

					if (empty($product_code)) {
						$product_code = db_get_field("SELECT product_code FROM ?:products WHERE product_id = ?i", $v['product_id']);
					}

					// Check the cart custom files
					if (isset($extra['custom_files'])) {
						$dir_path = DIR_CUSTOM_FILES . 'order_data/' . $order_id;
						$sess_dir_path = DIR_CUSTOM_FILES . 'sess_data';
						
						if (!is_dir($dir_path)) {
							fn_mkdir($dir_path);
						}
						
						foreach ($extra['custom_files'] as $option_id => $files) {
							if (is_array($files)) {
								foreach ($files as $file_id => $file) {
									$file['path'] = $sess_dir_path . '/' . basename($file['path']);
									
									fn_copy($file['path'], $dir_path . '/' . $file['file']);
									fn_rm($file['path']);
									fn_rm($file['path'] . '_thumb');
									
									$extra['custom_files'][$option_id][$file_id]['path'] = $dir_path . '/' . $file['file'];
								}
							}
						}
					}

					$order_details = array (
						'item_id' => $k,
						'order_id' => $order_id,
						'product_id' => $v['product_id'],
						'product_code' => $product_code,
						'price' => (!empty($v['stored_price']) && $v['stored_price'] == 'Y') ? $v['price'] - $v['discount'] : $v['price'],
						'amount' => $v['amount'],
						'extra' => serialize($extra)
					);

					db_query("INSERT INTO ?:order_details ?e", $order_details);
					
					// Increase product popularity
					$_data = array (
						'product_id' => $v['product_id'],
						'bought' => 1,
						'total' => POPULARITY_BUY
					);
					
					db_query("INSERT INTO ?:product_popularity ?e ON DUPLICATE KEY UPDATE bought = bought + 1, total = total + ?i", $_data, POPULARITY_BUY);
				}
			}

			// Save shipping information
			if (!empty($cart['shipping'])) {
				// Get carriers and tracking number
				$data = db_get_field("SELECT data FROM ?:order_data WHERE order_id = ?i AND type = 'L'", $order_id);
				if (!empty($data)) {
					$data = unserialize($data);
					foreach ($cart['shipping'] as $sh_id => $_d) {
						if (!empty($data[$sh_id]['carrier'])) {
							$cart['shipping'][$sh_id]['carrier'] = $data[$sh_id]['carrier'];
						}

						if (!empty($data[$sh_id]['tracking_number'])) {
							$cart['shipping'][$sh_id]['tracking_number'] = $data[$sh_id]['tracking_number'];
						}
					}
				}
				$_data = array (
					'order_id' => $order_id,
					'type' => 'L', //shipping information
					'data' => serialize($cart['shipping'])
				);
				db_query("REPLACE INTO ?:order_data ?e", $_data);
			}

			// Save taxes
			if (!empty($cart['taxes'])) {
				$_data = array (
					'order_id' => $order_id,
					'type' => 'T', //taxes information
					'data' => serialize($cart['taxes']),
				);
				db_query("REPLACE INTO ?:order_data ?e", $_data);
			}

			// Save payment information
			if (!empty($cart['payment_info'])) {
				$_data = array (
					'order_id' => $order_id,
					'type' => 'P', //payment information
					'data' => fn_encrypt_text(serialize($cart['payment_info'])),
				);
				db_query("REPLACE INTO ?:order_data ?e", $_data);
			}

			// Save coupons information
			if (!empty($cart['coupons'])) {
				$_data = array (
					'order_id' => $order_id,
					'type' => 'C', //coupons
					'data' => serialize($cart['coupons']),
				);
				db_query("REPLACE INTO ?:order_data ?e", $_data);
			}

			// Save secondary currency (for order notifications from payments with feedback requests) 
			$_data = array (
				'order_id' => $order_id,
				'type' => 'R', //secondary currency
				'data' => serialize(CART_SECONDARY_CURRENCY),
			);
			db_query("REPLACE INTO ?:order_data ?e", $_data);

			//
			// Place the order_id to new_orders table for all admin profiles
			//
			$admins = db_get_fields("SELECT user_id FROM ?:users WHERE user_type = 'A'");
			foreach ($admins as $k => $v) {
				db_query("REPLACE INTO ?:new_orders (order_id, user_id) VALUES (?i, ?i)", $order_id, $v);
			}

			fn_set_hook('place_order', $order_id, $action, $__order_status, $cart);

			// If order total is zero, just save the order without any processing procedures
			if (floatval($cart['total']) == 0) {
				$action = 'save';
				$__order_status = 'P';
			}
			
			list($is_processor_script, ) = fn_check_processor_script($cart['payment_id'], $action, true);

			if (!$is_processor_script && $__order_status == STATUS_INCOMPLETED_ORDER) {
				$__order_status = 'O';
			}

			// Set new order status
			fn_change_order_status($order_id, $__order_status, '', (($is_processor_script || $__order_status == STATUS_PARENT_ORDER) ? fn_get_notification_rules(array(), true) : fn_get_notification_rules(array())), true);

			$cart['processed_order_id'] = array();
			$cart['processed_order_id'][] = $order_id;

			if (!$parent_order_id && count($cart['companies']) > 1 && PRODUCT_TYPE == 'MULTIVENDOR') {
				fn_companies_place_suborders($order_id, $cart, $auth, $action);
				$child_orders = db_get_fields("SELECT order_id FROM ?:orders WHERE parent_order_id = ?i", $order_id);
				array_unshift($child_orders, $order_id);
				$cart['processed_order_id'] = $child_orders;
			}

			return array($order_id, $action != 'save');
		}
	}

	return array(false, false);
}

/**
 * Order payment processing
 *
 * @param array $payment payment data
 * @param int $order_id order ID
 * @param bool $force_notification force user notification (true - notify, false - do not notify, order status properties will be skipped)
 */
function fn_start_payment($order_id, $force_notification = array())
{
	$order_info = fn_get_order_info($order_id);
	
	list($is_processor_script, $processor_data) = fn_check_processor_script($order_info['payment_id'], '');
	if ($is_processor_script) {
		set_time_limit(300);
		$idata = array (
			'order_id' => $order_id,
			'type' => 'S',
			'data' => TIME,
		);
		db_query("REPLACE INTO ?:order_data ?e", $idata);


		$index_script = INDEX_SCRIPT;
		$mode = MODE;

		include(DIR_PAYMENT_FILES . $processor_data['processor_script']);

		return fn_finish_payment($order_id, $pp_response, $force_notification);
	}

	return false;
}

/**
 * Finish order paymnent
 *
 * @param int $order_id order ID
 * @param array $pp_response payment response
 * @param bool $force_notification force user notification (true - notify, false - do not notify, order status properties will be skipped)
 */
function fn_finish_payment($order_id, $pp_response, $force_notification = array())
{
	// Change order status
	$valid_id = db_get_field("SELECT order_id FROM ?:order_data WHERE order_id = ?i AND type = 'S'", $order_id);

	if (!empty($valid_id)) {
		db_query("DELETE FROM ?:order_data WHERE order_id = ?i AND type = 'S'", $order_id);

		fn_update_order_payment_info($order_id, $pp_response);

		if ($pp_response['order_status'] == 'N' && !empty($_SESSION['cart']['placement_action']) && $_SESSION['cart']['placement_action'] == 'repay') {
			$pp_response['order_status'] = 'I';
		}

		fn_set_hook('finish_payment', $order_id, $pp_response, $force_notification);

		fn_change_order_status($order_id, $pp_response['order_status'], '', $force_notification);
	}
}

//
// Store cart content in the customer's profile
//
function fn_save_cart_content(&$cart, $user_id, $type = 'C', $user_type = 'R')
{
	if (empty($user_id)) {
		if (fn_get_cookie('cu_id')) {
			$user_id = fn_get_cookie('cu_id');
		} else {
			$user_id = fn_crc32(uniqid(TIME));
			fn_set_cookie('cu_id', $user_id, COOKIE_ALIVE_TIME);
		}
		$user_type = 'U';
	}

	if (!empty($user_id)) {
		db_query("DELETE FROM ?:user_session_products WHERE user_id = ?i AND type = ?s AND user_type = ?s", $user_id, $type, $user_type);
		if (!empty($cart['products']) && is_array($cart['products'])) {
			$_cart_prods = $cart['products'];
			foreach ($_cart_prods as $_item_id => $_prod) {
				$_cart_prods[$_item_id]['user_id'] = $user_id;
				$_cart_prods[$_item_id]['timestamp'] = TIME;
				$_cart_prods[$_item_id]['type'] = $type;
				$_cart_prods[$_item_id]['user_type'] = $user_type;
				$_cart_prods[$_item_id]['item_id'] = $_item_id;
				$_cart_prods[$_item_id]['item_type'] = 'P';
				$_cart_prods[$_item_id]['extra'] = serialize($_prod);
				$_cart_prods[$_item_id]['amount'] = empty($_cart_prods[$_item_id]['amount']) ? 1 : $_cart_prods[$_item_id]['amount'];
				$_cart_prods[$_item_id]['session_id'] = Session::get_id();

				if (!empty($_cart_prods[$_item_id])) {
					db_query('REPLACE INTO ?:user_session_products ?e', $_cart_prods[$_item_id]);
				}
			}
		}

		fn_set_hook('save_cart', $cart, $user_id, $type);

	}
	return true;
}

/**
 * Extract cart content from the customer's profile.
 * $type : C - cart, W - wishlist
 *
 * @param array $cart
 * @param integer $user_id
 * @param char $type
 *
 * @return void
 */
function fn_extract_cart_content(&$cart, $user_id, $type = 'C', $user_type = 'R')
{
	$auth = & $_SESSION['auth'];
	$old_session_id = '';

	// Restore cart content
	if (!empty($user_id)) {
		$item_types = fn_get_cart_content_item_types('X');
		$_prods = db_get_hash_array("SELECT * FROM ?:user_session_products WHERE user_id = ?i AND type = ?s AND user_type = ?s AND item_type IN (?a)", 'item_id', $user_id, $type, $user_type, $item_types);
		if (!empty($_prods) && is_array($_prods)) {
			$cart['products'] = empty($cart['products']) ? array() : $cart['products'];
			foreach ($_prods as $_item_id => $_prod) {
				$old_session_id = $_prod['session_id'];
				$_prod_extra = unserialize($_prod['extra']);
				unset($_prod['extra']);
				$cart['products'][$_item_id] = empty($cart['products'][$_item_id]) ? fn_array_merge($_prod, $_prod_extra, true) : $cart['products'][$_item_id];
			}
		}
	}

	fn_set_hook('extract_cart', $cart, $user_id, $type, $user_type);

	if ($type == 'C') {
		fn_calculate_cart_content($cart, $auth, 'S', false, 'I');
	}
}
/**
 * get cart content item types
 *
 * @param char $action
 * V - for View mode
 * X - for eXtract mode
 * @return array
 */
function fn_get_cart_content_item_types($action = 'V')
{
	$item_types = array('P');

	fn_set_hook('get_cart_item_types', $item_types, $action);

	return $item_types;
}

//
// Get order name
//
function fn_get_order_name($order_id)
{
	$total = db_get_field("SELECT total FROM ?:orders WHERE order_id = ?i", $order_id);
	if ($total == '') {
		return false;
	}

	if (Registry::get('settings.General.alternative_currency') == "Y") {
		$result = fn_format_price_by_currency($total, CART_PRIMARY_CURRENCY);
		if (CART_SECONDARY_CURRENCY != CART_PRIMARY_CURRENCY) {
			$result .= ' (' . fn_format_price_by_currency($total) . ')';
		}
	} else {
		$result = fn_format_price_by_currency($total);
	}

	return $order_id . ' - ' . $result;
}

function fn_format_price_by_currency($price, $currency_code = CART_SECONDARY_CURRENCY)
{
	$currencies = Registry::get('currencies');
	$currency = $currencies[$currency_code];
	$result = fn_format_rate_value($price, 'F', $currency['decimals'], $currency['decimals_separator'], $currency['thousands_separator'], $currency['coefficient']);
	if ($currency['after'] == 'Y') {
		$result .= ' ' . $currency['symbol'];
	} else {
		$result = $currency['symbol'] . $result;
	}

	return $result;
}

//
// Get order info
//
function fn_get_order_info($order_id, $native_language = false, $format_info = true, $get_edp_files = false, $skip_static_values = false)
{
   
	if (!empty($order_id)) {       
		$condition = fn_get_company_condition();
		$order = db_get_row("SELECT * FROM ?:orders WHERE ?:orders.order_id = ?i $condition", $order_id);
          
		if (empty($order)) {
			return false;
		}

		$lang_code = ($native_language == true) ? $order['lang_code'] : CART_LANGUAGE;

		$order['payment_method'] = fn_get_payment_method_data($order['payment_id'], $lang_code);

		if (!empty($order)) {
			// Get additional profile fields
			$additional_fields = db_get_hash_single_array("SELECT field_id, value FROM ?:profile_fields_data WHERE object_id = ?i AND object_type = 'O'", array('field_id', 'value'), $order_id);
			$order['fields'] = $additional_fields;

			$order['items'] = db_get_hash_array("SELECT ?:order_details.*, ?:product_descriptions.product, ?:product_descriptions.short_description FROM ?:order_details LEFT JOIN ?:product_descriptions ON ?:order_details.product_id = ?:product_descriptions.product_id AND ?:product_descriptions.lang_code = ?s WHERE ?:order_details.order_id = ?i ORDER BY ?:product_descriptions.product", 'item_id', $lang_code, $order_id);

			$order['promotions'] = unserialize($order['promotions']);
			if (!empty($order['promotions'])) { // collect additional data
				$params = array (
					'promotion_id' => array_keys($order['promotions']),
				);
				list($promotions) = fn_get_promotions($params);
				foreach ($promotions as $pr_id => $p) {
					$order['promotions'][$pr_id]['name'] = $p['name'];
					$order['promotions'][$pr_id]['short_description'] = $p['short_description'];
				}
			}

			// Get additional data
			$additional_data = db_get_hash_single_array("SELECT type, data FROM ?:order_data WHERE order_id = ?i", array('type', 'data'), $order_id);

			$order['taxes'] = array();
			$order['tax_subtotal'] = 0;
			$order['display_shipping_cost'] = $order['shipping_cost'];

			// Replace country, state and title values with their descriptions
			fn_add_user_data_descriptions($order, $lang_code);
			
			$order['need_shipping'] = false;
			$deps = array();

				// Get shipments common information
			if (Registry::get('settings.General.use_shipments') == 'Y') {
				$order['shipment_ids'] = db_get_fields('SELECT sh.shipment_id FROM ?:shipments AS sh LEFT JOIN ?:shipment_items AS s_items ON (sh.shipment_id = s_items.shipment_id) WHERE s_items.order_id = ?i GROUP BY s_items.shipment_id', $order_id);
				
				$_products = db_get_array("SELECT item_id, SUM(amount) AS amount FROM ?:shipment_items WHERE order_id = ?i GROUP BY item_id", $order_id);
				$shipped_products = array();
				
				if (!empty($_products)) {
					foreach ($_products as $_product) {
						$shipped_products[$_product['item_id']] = $_product['amount'];
					}
				}
				unset($_products);
			}
				foreach ($order['items'] as $k => $v) {
				//Check for product existance
				if (empty($v['product'])) {
					$order['items'][$k]['deleted_product'] = true;
				}
				$order['items'][$k]['discount'] = 0;

				$v['extra'] = @unserialize($v['extra']);
				if ($skip_static_values == false && !empty($v['extra']['product'])) {
					$order['items'][$k]['product'] = $v['extra']['product'];
					preg_match("/\[(.*?)\]/",$v['extra']['product'],$resmath);
					$order['items'][$k]['art']=$resmath[1];
				}

				$order['items'][$k]['company_id'] = empty($v['extra']['company_id']) ? 0 : $v['extra']['company_id'];

				if (!empty($v['extra']['discount']) && floatval($v['extra']['discount'])) {
					$order['items'][$k]['discount'] = $v['extra']['discount'];
					$order['use_discount'] = true;
				}

				if (!empty($v['extra']['promotions'])) {
					$order['items'][$k]['promotions'] = $v['extra']['promotions'];
				}

				if (isset($v['extra']['base_price'])) {
					$order['items'][$k]['base_price'] = floatval($v['extra']['base_price']);
				} else {
					$order['items'][$k]['base_price'] = $v['price'];
				}
				$order['items'][$k]['original_price'] = $order['items'][$k]['base_price'];

				// Form hash key for this product
				$order['items'][$k]['cart_id'] = $v['item_id'];
				$deps['P_'.$order['items'][$k]['cart_id']] = $k;

//$order['items'][$k]['sklad'] = fn_numsklad($order['items'][$k]['product']);

				// Unserialize and collect product options information
				if (!empty($v['extra']['product_options'])) {
					if ($format_info == true) {
						$order['items'][$k]['product_options'] = ($skip_static_values == false && !empty($v['extra']['product_options_value'])) ? $v['extra']['product_options_value'] : fn_get_selected_product_options_info($v['extra']['product_options'], $lang_code);
					}

					if (empty($v['extra']['stored_price'])) { // apply modifiers if this is not the custom price
						$order['items'][$k]['original_price'] = fn_apply_options_modifiers($v['extra']['product_options'], $order['items'][$k]['base_price'], 'P', ($skip_static_values == false && !empty($v['extra']['product_options_value'])) ? $v['extra']['product_options_value'] : array());
					}
				}
				 
$img = db_get_field("SELECT cscart_images.image_path FROM cscart_images_links LEFT 
JOIN cscart_images ON cscart_images_links.image_id = cscart_images.image_id  
WHERE cscart_images_links.object_id = ?i", $order['items'][$k]['product_id']);
$imgid = db_get_field("SELECT cscart_images_links.image_id FROM cscart_images_links  
WHERE cscart_images_links.object_id = ?i", $order['items'][$k]['product_id']);

				$order['items'][$k]['extra'] = $v['extra'];
				$order['items'][$k]['img'] = fn_get_image($imgid,"product");
				$order['items'][$k]['tax_value'] = 0;
				$order['items'][$k]['display_subtotal'] = $order['items'][$k]['subtotal'] = ($v['price'] * $v['amount']);
				$order['items'][$k]['display_per_subtotal'] = ($order['items'][$k]['original_price'] * $v['amount']);

				// Get information about edp
				if ($get_edp_files == true && $order['items'][$k]['extra']['is_edp'] == 'Y') {
					$order['items'][$k]['files'] = db_get_array("SELECT ?:product_files.file_id, ?:product_files.activation_type, ?:product_files.max_downloads, ?:product_file_descriptions.file_name, ?:product_file_ekeys.active, ?:product_file_ekeys.downloads, ?:product_file_ekeys.ekey, ?:product_file_ekeys.ttl FROM ?:product_files LEFT JOIN ?:product_file_descriptions ON ?:product_file_descriptions.file_id = ?:product_files.file_id AND ?:product_file_descriptions.lang_code = ?s LEFT JOIN ?:product_file_ekeys ON ?:product_file_ekeys.file_id = ?:product_files.file_id AND ?:product_file_ekeys.order_id = ?i WHERE ?:product_files.product_id = ?i", $lang_code, $order_id, $v['product_id']);
				}
						// Get shipments information
				if (Registry::get('settings.General.use_shipments') == 'Y') {
					if (isset($shipped_products[$k])) {
						$order['items'][$k]['shipped_amount'] = $shipped_products[$k];
						$order['items'][$k]['shipment_amount'] = $v['amount'] - $shipped_products[$k];

					} else {
						$order['items'][$k]['shipped_amount'] = 0;
						$order['items'][$k]['shipment_amount'] = $v['amount'];
					}
					
					if ($order['items'][$k]['shipped_amount'] < $order['items'][$k]['amount']) {
						$order['need_shipment'] = true;
					}
				}
						// Check if the order needs the shipping method
				if (!($v['extra']['is_edp'] == 'Y' && (!isset($v['extra']['edp_shipping']) || $v['extra']['edp_shipping'] != 'Y'))) {
					$order['need_shipping'] = true;
				}
			}

			if (fn_check_suppliers_functionality ()) {
				$order['companies'] = fn_get_products_companies($order['items']);
				$order['have_suppliers'] = fn_check_companies_have_suppliers($order['companies']);
			}

			// Unserialize and collect taxes information
			if (!empty($additional_data['T'])) {
				$order['taxes'] = unserialize($additional_data['T']);
				if (is_array($order['taxes'])) {
					foreach ($order['taxes'] as  $tax_id => $tax_data) {
						foreach ($tax_data['applies'] as $_id => $value) {
							if (strpos($_id, 'P_') !== false && isset($deps[$_id])) {
								$order['items'][$deps[$_id]]['tax_value'] += $value;
								if ($tax_data['price_includes_tax'] != 'Y') {
									$order['items'][$deps[$_id]]['subtotal'] += $value;
									$order['items'][$deps[$_id]]['display_subtotal'] += (Registry::get('settings.Appearance.cart_prices_w_taxes') == 'Y') ? $value : 0;
									$order['tax_subtotal'] += $value;
								}
							}
							if (strpos($_id, 'S_') !== false && Registry::get('settings.Appearance.cart_prices_w_taxes') == 'Y') {
								if ($tax_data['price_includes_tax'] != 'Y') {
									$order['display_shipping_cost'] += $value;
								}
							}
						}
					}
				} else {
					$order['taxes'] = array();
				}
			}

			if (!empty($additional_data['C'])) {
				$order['coupons'] = unserialize($additional_data['C']);
			}

			if (!empty($additional_data['R'])) {
				$order['secondary_currency'] = unserialize($additional_data['R']);
			}

			// Recalculate subtotal
			$order['subtotal'] = $order['display_subtotal'] = 0;
			foreach ($order['items'] as $v) {
				$order['subtotal'] += $v['subtotal'];
				$order['display_per_subtotal'] += $v['display_per_subtotal'];
				$order['display_subtotal'] += $v['display_subtotal'];
			}

			// Unserialize and collect payment information
			if (!empty($additional_data['P'])) {
				$order['payment_info'] = unserialize(fn_decrypt_text($additional_data['P']));
			}

			if (empty($order['payment_info']) || !is_array($order['payment_info'])) {
				$order['payment_info'] = array();
			}

			// Get shipping information
			if (!empty($additional_data['L'])) {
				$order['shipping'] = unserialize($additional_data['L']);
			}

			$order['doc_ids'] = db_get_hash_single_array("SELECT type, doc_id FROM ?:order_docs WHERE order_id = ?i", array('type', 'doc_id'), $order_id);
		}

		fn_set_hook('get_order_info', $order, $additional_data);

//echo $_SERVER['REMOTE_ADDR'];
  
if ($_SERVER['REMOTE_ADDR'] == "188.123.241.2")
{
//	echo ;
}		
		
		$order['subtotal_discount'] = ($order['subtotal_discount']=="0.00")? $order['discount']: $order['subtotal_discount'];
		$metro = db_get_field("SELECT description FROM cscart_profile_field_descriptions WHERE object_type = 'V' AND object_id = ?i", $order['fields'][44]);
		//$firma = db_get_field("SELECT description FROM cscart_profile_field_descriptions WHERE object_type = 'V' AND object_id = ?i", $order['fields'][65]);
		$q = "SELECT value FROM cscart_profile_fields_data  WHERE  object_id =".$order_id." AND field_id = 65";
	
		$vals = db_get_array($q);
		//var_dump($vals);
		//$firma = db_get_field("SELECT value FROM cscart_profile_fields_data WHERE  object_id = ?i", $order['fields'][65]);
		
		$order['metro']=$metro;
		$order['firma']=$vals[0]["value"];
		
		return $order;
	}

	return false;
}

//
// Get order short info
//
function fn_get_order_short_info($order_id)
{
	if (!empty($order_id)) {
		$order = db_get_row("SELECT total, status, firstname, lastname, timestamp FROM ?:orders WHERE order_id = ?i", $order_id);

		return $order;
	}

	return false;
}

//
// Change order status
//
function fn_change_order_status($order_id, $status_to, $status_from = '', $force_notification = array(), $place_order = false)
{
	$order_info = fn_get_order_info($order_id, true);
	$order_statuses = fn_get_statuses(STATUSES_ORDER, false, true);

	if (empty($status_from)) {
		$status_from = $order_info['status'];
	}

	if (empty($order_info) || empty($status_to) || $status_from == $status_to) {
		return false;
	}

	$_updated_ids = array();
	$_error = false;

	foreach ($order_info['items'] as $k => $v) {

		// Generate ekey if EDP is ordered
		if (!empty($v['extra']['is_edp']) && $v['extra']['is_edp'] == 'Y') {
			continue; // don't track inventory
		}

		// Update product amount if inventory tracking is enabled
		if (Registry::get('settings.General.inventory_tracking') == 'Y') {
			if ($order_statuses[$status_to]['inventory'] == 'D' && $order_statuses[$status_from]['inventory'] == 'I') {
				// decrease amount
				if (fn_update_product_amount($v['product_id'], $v['amount'], @$v['extra']['product_options'], '-') == false) {
					$status_to = 'B'; //backorder
					$_error = true;
					$msg = str_replace('[product]', fn_get_product_name($v['product_id']) . ' #' . $v['product_id'], fn_get_lang_var('low_stock_subj'));
					fn_set_notification('W', fn_get_lang_var('warning'), $msg);

					break;
				} else {
					$_updated_ids[] = $k;
				}
			} elseif ($order_statuses[$status_to]['inventory'] == 'I' && $order_statuses[$status_from]['inventory'] == 'D') {
				// increase amount
				fn_update_product_amount($v['product_id'], $v['amount'], @$v['extra']['product_options'], '+');
			}
		}
	}

	if ($_error) {
		if (!empty($_updated_ids)) {
			foreach ($_updated_ids as $id) {
				// increase amount
				fn_update_product_amount($order_info['items'][$id]['product_id'], $order_info['items'][$id]['amount'], @$order_info['items'][$id]['extra']['product_options'], '+');
			}
			unset($_updated_ids);
		}
		
		if ($status_from == $status_to) {
			return false;
		}
	}

	fn_promotion_post_processing($status_to, $status_from, $order_info, $force_notification);

	fn_set_hook('change_order_status', $status_to, $status_from, $order_info, $force_notification, $order_statuses, $place_order);

	// Log order status change
	fn_log_event('orders', 'status', array (
		'order_id' => $order_id,
		'status_from' => $status_from,
		'status_to' => $status_to,
	));

	if (!empty($order_statuses[$status_to]['appearance_type']) && ($order_statuses[$status_to]['appearance_type'] == 'I' || $order_statuses[$status_to]['appearance_type'] == 'C') && !db_get_field("SELECT doc_id FROM ?:order_docs WHERE type = ?s AND order_id = ?i", $order_statuses[$status_to]['appearance_type'], $order_id)) {
		$_data = array (
			'order_id' => $order_id,
			'type' => $order_statuses[$status_to]['appearance_type']
		);
		$order_info['doc_ids'][$order_statuses[$status_to]['appearance_type']] = db_query("INSERT INTO ?:order_docs ?e", $_data);
	}

	// Check if we need to remove CC info
	if (!empty($order_statuses[$status_to]['remove_cc_info']) && $order_statuses[$status_to]['remove_cc_info'] == 'Y' && !empty($order_info['payment_info'])) {
		fn_cleanup_payment_info($order_id, $order_info['payment_info'], true);
	}

	$edp_data = fn_generate_ekeys_for_edp(array('status_from' => $status_from, 'status_to' => $status_to), $order_info);
	$order_info['status'] = $status_to;

	fn_order_notification($order_info, $edp_data, $force_notification);

	db_query("UPDATE ?:orders SET status = ?s WHERE order_id = ?i", $status_to, $order_id);
	
	if ($order_info['is_parent_order'] == 'Y') {
		$child_ids = db_get_fields("SELECT order_id FROM ?:orders WHERE parent_order_id = ?i", $order_id);
		foreach ($child_ids as $child_order_id) {
			fn_change_order_status($child_order_id, $status_to, $status_from, $force_notification, $place_order);
		}
	}

	return true;
}

/**
 * Function delete order
 *
 * @param int $order_id
 */
function fn_delete_order($order_id)
{
	// Log order deletion
	fn_log_event('orders', 'delete', array (
		'order_id' => $order_id,
	));

	fn_change_order_status($order_id, STATUS_INCOMPLETED_ORDER, '', fn_get_notification_rules(array(), false)); // incomplete to increase inventory

	fn_set_hook('delete_order', $order_id);

	db_query("DELETE FROM ?:new_orders WHERE order_id = ?i", $order_id);
	db_query("DELETE FROM ?:order_data WHERE order_id = ?i", $order_id);
	db_query("DELETE FROM ?:order_details WHERE order_id = ?i", $order_id);
	db_query("DELETE FROM ?:orders WHERE order_id = ?i", $order_id);
	db_query("DELETE FROM ?:product_file_ekeys WHERE order_id = ?i", $order_id);
	db_query("DELETE FROM ?:profile_fields_data WHERE object_id = ?i AND object_type='O'", $order_id);
	db_query("DELETE FROM ?:order_docs WHERE order_id = ?i", $order_id);
}

/**
 * Function generate edp ekeys for email notification
 *
 * @param array $statuses order statuses
 * @param array $order_info order information
 * @param array $active_files array with file download statuses
 * @return array $edp_data
 */

function fn_generate_ekeys_for_edp($statuses, $order_info, $active_files = array())
{
	$edp_data = array();
	$order_statuses = fn_get_statuses(STATUSES_ORDER, false, true);

	foreach ($order_info['items'] as $v) {

		// Generate ekey if EDP is ordered
		if (!empty($v['extra']['is_edp']) && $v['extra']['is_edp'] == 'Y') {

			$activations = db_get_hash_single_array("SELECT activation_type, file_id FROM ?:product_files WHERE product_id = ?i", array('file_id', 'activation_type'), $v['product_id']);

			foreach ($activations as $file_id => $activation_type) {

				$send_notification = false;

				// Check if ekey already was generated for this file
				$_ekey = db_get_row("SELECT ekey, active, file_id, product_id, order_id, ekey FROM ?:product_file_ekeys WHERE file_id = ?i AND order_id = ?i", $file_id, $order_info['order_id']);
				if (!empty($_ekey)) {
					// If order status changed to "Processed"
					if (($activation_type == 'P') && !empty($statuses)) {
						if ($order_statuses[$statuses['status_to']]['inventory'] == 'D' && substr_count('O', $statuses['status_to']) == 0 && ($order_statuses[$statuses['status_from']]['inventory'] != 'D' || substr_count('O', $statuses['status_from']) > 0)) {
							$active_files[$v['product_id']][$file_id] = 'Y';
						} elseif (($order_statuses[$statuses['status_to']]['inventory'] != 'D' && substr_count('O', $statuses['status_from']) == 0 || substr_count('O', $statuses['status_to']) > 0) && $order_statuses[$statuses['status_from']]['inventory'] == 'D') {
							$active_files[$v['product_id']][$file_id] = 'N';
						}
					}

					if (!empty($active_files[$v['product_id']][$file_id])) {
						db_query('UPDATE ?:product_file_ekeys SET ?u WHERE file_id = ?i AND product_id = ?i AND order_id = ?i', array('active' => $active_files[$v['product_id']][$file_id]), $_ekey['file_id'], $_ekey['product_id'], $_ekey['order_id']);

						if ($active_files[$v['product_id']][$file_id] == 'Y' && $_ekey['active'] !== 'Y') {
							$edp_data[$v['product_id']]['files'][$file_id] = $_ekey;
						}
					}

				} else {
					$_data = array (
						'file_id' => $file_id,
						'product_id' => $v['product_id'],
						'ekey' => md5(uniqid(rand())),
						'ttl' => (TIME + (Registry::get('settings.General.edp_key_ttl') * 60 * 60)),
						'order_id' => $order_info['order_id'],
						'activation' => $activation_type
					);

					// Activate the file if type is "Immediately" or "After full payment" and order statuses is from "paid" group
					if ($activation_type == 'I' || !empty($active_files[$v['product_id']][$file_id]) && $active_files[$v['product_id']][$file_id] == 'Y' || ($activation_type == 'P' && $order_statuses[$statuses['status_to']]['inventory'] == 'D' && substr_count('O', $statuses['status_to']) == 0 && ($order_statuses[$statuses['status_from']]['inventory'] != 'D' || substr_count('O', $statuses['status_from']) > 0 ))) {
						$_data['active'] = 'Y';
						$edp_data[$v['product_id']]['files'][$file_id] = $_data;
					}

					db_query('REPLACE INTO ?:product_file_ekeys ?e', $_data);
				}

				if (!empty($edp_data[$v['product_id']]['files'][$file_id])) {
					$edp_data[$v['product_id']]['files'][$file_id]['file_size'] = db_get_field("SELECT file_size FROM ?:product_files WHERE file_id = ?i", $file_id);
					$edp_data[$v['product_id']]['files'][$file_id]['file_name'] = db_get_field("SELECT file_name FROM ?:product_file_descriptions WHERE file_id = ?i AND lang_code = ?s", $file_id, CART_LANGUAGE);
				}
			}
		}
	}

	return $edp_data;
}

//
// Update order payment information
//
function fn_update_order_payment_info($order_id, $pp_response)
{
	if (empty($order_id) || empty($pp_response) || !is_array($pp_response)) {
		return false;
	}

	$payment_info = db_get_field("SELECT data FROM ?:order_data WHERE order_id = ?i AND type = 'P'", $order_id);
	if (!empty($payment_info)) {
		$payment_info = unserialize(fn_decrypt_text($payment_info));
	} else {
		$payment_info = array();
	}


	foreach ($pp_response as $k => $v) {
		$payment_info[$k] = $v;
	}

	$data = array (
		'data' => fn_encrypt_text(serialize($payment_info)),
		'order_id' => $order_id,
		'type' => 'P'
	);

	db_query("REPLACE INTO ?:order_data ?e", $data);
	
	$child_orders_ids = db_get_fields("SELECT order_id FROM ?:orders WHERE parent_order_id = ?i", $order_id);
	if (!empty($child_orders_ids)) {
		foreach ($child_orders_ids as $child_id) {
			fn_update_order_payment_info($child_id, $pp_response);
		}
	}

	return true;
}

//
// Get all shippings list
//


function fn_get_shippings($simple, $lang_code = CART_LANGUAGE)
{
	$conditions = '1';

	if (AREA == 'C') {
		$conditions .= " AND (" . fn_find_array_in_set($_SESSION['auth']['usergroup_ids'], 'a.usergroup_ids', true) . ")";
		$conditions .= " AND a.status = 'A'";
		$conditions .= fn_get_localizations_condition('a.localization');
	}

	if ($simple == true) {
		return db_get_hash_single_array("SELECT a.shipping_id, b.shipping FROM ?:shippings as a LEFT JOIN ?:shipping_descriptions as b ON a.shipping_id = b.shipping_id AND b.lang_code = ?s WHERE ?p ORDER BY a.position", array('shipping_id', 'shipping'), $lang_code, $conditions);
	} else {
		// -- 20.11.2014 - added a.self_service to query
        // -- 06.02.2015 - added a.min_total_price to query
		return db_get_array("SELECT a.shipping_id, a.min_weight, a.max_weight, a.position, a.status, a.self_service, a.min_total_price,  b.shipping, b.delivery_time, a.usergroup_ids FROM ?:shippings as a LEFT JOIN ?:shipping_descriptions as b ON a.shipping_id = b.shipping_id AND b.lang_code = ?s WHERE ?p ORDER BY a.position", $lang_code, $conditions);
	}
}

//
// Get all rates for specific shipping
//
function fn_get_shipping_rates($shipping_id)
{
	if (!empty($shipping_id)) {
		return db_get_array("SELECT rate_id, ?:shipping_rates.destination_id, destination FROM ?:shipping_rates LEFT JOIN ?:destination_descriptions ON ?:destination_descriptions.destination_id = ?:shipping_rates.destination_id AND ?:destination_descriptions.lang_code = ?s WHERE shipping_id = ?i", CART_LANGUAGE, $shipping_id);
	} else {
		return false;
	}
}

//
// Get shipping name
//
function fn_get_shipping_name($shipping_id, $lang_code = CART_LANGUAGE)
{
	if (!empty($shipping_id)) {
		return db_get_field("SELECT shipping FROM ?:shipping_descriptions WHERE shipping_id = ?i AND lang_code = ?s", $shipping_id, $lang_code);
	}

	return false;
}

//
// Get all taxes list
//
function fn_get_taxes($lang_code = '')
{
	if (empty($lang_code)) {
		$lang_code = CART_LANGUAGE;
	}

	return db_get_hash_array("SELECT a.*, b.tax FROM ?:taxes as a LEFT JOIN ?:tax_descriptions as b ON b.tax_id = a.tax_id AND b.lang_code = ?s ORDER BY a.priority", 'tax_id', $lang_code);
}

//
// Get tax name
//
function fn_get_tax_name($tax_id = 0, $lang_code = CART_LANGUAGE, $as_array = false)
{
	if (!empty($tax_id)) {
		if (!is_array($tax_id) && strpos($tax_id, ',') !== false) {
			$tax_id = explode(',', $tax_id);
		}
		if (is_array($tax_id) || $as_array == true) {
			return db_get_hash_single_array("SELECT tax_id, tax FROM ?:tax_descriptions WHERE tax_id IN (?n) AND lang_code = ?s", array('tax_id', 'tax'), $tax_id, $lang_code);
		} else {
			return db_get_field("SELECT tax FROM ?:tax_descriptions WHERE tax_id = ?i AND lang_code = ?s", $tax_id, $lang_code);
		}
	}

	return false;
}

//
// Get all rates for specific tax
//
function fn_get_tax_rates($tax_id, $destination_id = 0)
{
	if (empty($tax_id)) {
		return false;
	}
	return db_get_array("SELECT * FROM ?:tax_rates WHERE tax_id = ?i AND destination_id = ?i", $tax_id, $destination_id);
}

//
// Get selected taxes
//
function fn_get_set_taxes($taxes_set)
{
	if (empty($taxes_set)) {
		return false;
	}

	return db_get_hash_array("SELECT tax_id, address_type, priority, price_includes_tax, regnumber FROM ?:taxes WHERE tax_id IN (?n) AND status = 'A' ORDER BY priority", 'tax_id', explode(',', $taxes_set));
}

function fn_add_exclude_products(&$cart, &$auth)
{
	$subtotal = 0;
	$original_subtotal = 0;

	if (isset($cart['products']) && is_array($cart['products'])) {
		foreach($cart['products'] as $cart_id => $product) {
			if (empty($product['product_id'])) {
				continue;
			}

			if (isset($product['extra']['exclude_from_calculate'])) {
				if (empty($cart['order_id'])) {
					unset($cart['products'][$cart_id]);
				}
			} else {
				if (!isset($product['product_options'])) {
					$product['product_options'] = array();
				}
				
				$product_subtotal = fn_apply_options_modifiers($product['product_options'], $product['price'], 'P') * $product['amount'];
				$original_subtotal += $product_subtotal;
				$subtotal += $product_subtotal - ((isset($product['discount'])) ? $product['discount'] : 0);
			}
		}
	}

	fn_set_hook('exclude_products_from_calculation', $cart, $auth, $original_subtotal, $subtotal);

}

//
// Calculate cart content
//
// options style:
// F - full
// S - skip selection
// I - info
// calculate_shipping:
// A - calculate all available methods
// E - calculate selected methods only (from cart[shipping])
// S - skip calculation

// Products prices definition
// base_price - price without options modifiers
// original_price - price without discounts (with options modifiers)
// price - price includes discount and taxes
// original_subtotal - original_price * product qty
// subtotal - price * product qty
// discount - discount for this product
// display_price - the displayed price (price does not use in the calculaton)
// display_subtotal - the displayed subtotal (price does not use in the calculaton)

// Cart prices definition
// shipping_cost - total shipping cost
// subtotal - sum (price * amount) of all products
// original_subtotal - sum (original_price * amount) of all products
// tax_subtotal - sum of all the tax values
// display_subtotal - the displayed subtotal (does not use in the calculaton)
// subtotal_discount - the order discount
// discount - sum of all products discounts (except subtotal_discount)
// total - order total

function fn_calculate_cart_content(&$cart, $auth, $calculate_shipping = 'A', $calculate_taxes = true, $options_style = 'F', $apply_cart_promotions = true)
{
	$shipping_rates = array();
	$cart_products = array();
	$cart['subtotal'] = $cart['original_subtotal'] = $cart['amount'] = $cart['total'] = $cart['discount'] = $cart['tax_subtotal'] = 0;
	$cart['use_discount'] = false;
	$cart['shipping_required'] = false;
	$cart['shipping_failed'] = false;
	$cart['stored_taxes'] = empty($cart['stored_taxes']) ? 'N': $cart['stored_taxes'];
	$cart['display_shipping_cost'] = $cart['shipping_cost'] = 0;
	$cart['coupons'] = empty($cart['coupons']) ? array() : $cart['coupons'];
	$cart['recalculate'] = isset($cart['recalculate']) ? $cart['recalculate'] : false;
	$cart['free_shipping'] = array();
	$cart['options_style'] = $options_style;

	fn_add_exclude_products($cart, $auth);

	if (isset($cart['products']) && is_array($cart['products']))
  {
		// Collect product data
		foreach ($cart['products'] as $k => $v) {

			$_cproduct = fn_get_cart_product_data($k, $cart['products'][$k], false, $cart, $auth);
			if (empty($_cproduct)) { // FIXME - for deleted products for OM
				unset($cart['products'][$k]);
				continue;
			}

			$cart_products[$k] = $_cproduct;
		}

		fn_set_hook('calculate_cart_items', $cart, $cart_products, $auth);
		// Apply cart promotions
		if ($apply_cart_promotions == true && $cart['subtotal'] > 0 && empty($cart['order_id'])) {
			fn_promotion_apply('cart', $cart, $auth, $cart_products);
		}

		if (Registry::get('settings.Shippings.disable_shipping') == 'Y') {
			$cart['shipping_required'] = false;
		}

		// Apply shipping fee
		if ($calculate_shipping != 'S' && $cart['shipping_required'] == true) {
			if (defined('CACHED_SHIPPING_RATES') && $cart['recalculate'] == false  ) {
				$shipping_rates = $_SESSION['shipping_rates'];
			} else {
				$shipping_rates = fn_calculate_shipping_rates($cart, $cart_products, $auth, ($calculate_shipping == 'E'));
			}

			fn_apply_cart_shipping_rates($cart, $cart_products, $auth, $shipping_rates);

			if (!empty($cart['stored_shipping'])) {
				$total_cost = 0;
				foreach ($cart['shipping'] as $sh_id => $method) {
					if (isset($cart['stored_shipping'][$sh_id])) {
						$piece = fn_format_price($cart['stored_shipping'][$sh_id] / count($method['rates']));
						foreach ($method['rates'] as $k => $v) {
							$cart['shipping'][$sh_id]['rates'][$k] = $piece;
							$total_cost += $piece;
						}
						if (($sum = array_sum($cart['shipping'][$sh_id]['rates'])) != $cart['stored_shipping'][$sh_id]) {
							$deviation = $cart['stored_shipping'][$sh_id] - $sum;
							$value = reset($cart['shipping'][$sh_id]['rates']);
							$key = key($cart['shipping'][$sh_id]['rates']);
							$cart['shipping'][$sh_id]['rates'][$key] = $value + $deviation;
							$total_cost += $deviation;
						}
					} else {
						if (!empty($method['rates'])) {
							$total_cost += array_sum($method['rates']);
						}
					}
				}
				$cart['shipping_cost'] = $total_cost;
			}

		} else {
			if (!empty($cart['shipping'])) {
				$cart['chosen_shipping'] = $cart['shipping'];
			}
			$cart['shipping'] = $shipping_rates = array();
			$cart['shipping_cost'] = 0;
		}

		$cart['display_shipping_cost'] = $cart['shipping_cost'];

		// Calculate taxes
		if ($cart['subtotal'] > 0 && $calculate_taxes == true && $auth['tax_exempt'] != 'Y') {
			fn_calculate_taxes($cart, $cart_products, $shipping_rates, $auth);
		} elseif ($cart['stored_taxes'] != 'Y') {
			$cart['taxes'] = array();
		}

		$cart['subtotal'] = $cart['display_subtotal'] = 0;

		fn_update_cart_data($cart, $cart_products);

		// Calculate totals
		foreach ($cart_products as $k => $v) {
			$_tax = (!empty($cart_products[$k]['tax_summary']) ? ($cart_products[$k]['tax_summary']['added'] / $v['amount']) : 0);
			$cart_products[$k]['display_price'] = $cart_products[$k]['price'] + (Registry::get('settings.Appearance.cart_prices_w_taxes') == 'Y' ? $_tax : 0);
			$cart_products[$k]['subtotal'] = $cart_products[$k]['price'] * $v['amount'];

			$cart_products[$k]['display_subtotal'] = $cart_products[$k]['display_price'] * $v['amount'];

			$cart['subtotal'] += $cart_products[$k]['subtotal'];
			$cart['display_subtotal'] += $cart_products[$k]['display_subtotal'];
			$cart['products'][$k]['display_price'] = $cart_products[$k]['display_price'];

			$cart['tax_subtotal'] += (!empty($cart_products[$k]['tax_summary']) ? ($cart_products[$k]['tax_summary']['added']) : 0);

			$cart['total'] += ($cart_products[$k]['price'] - 0) * $v['amount'];

			if (!empty($v['discount'])) {
				$cart['discount'] += $v['discount'] * $v['amount'];
			}
		}

		if (Registry::get('settings.General.tax_calculation') == 'subtotal') {
			$cart['tax_subtotal'] += (!empty($cart['tax_summary']['added']) ? ($cart['tax_summary']['added']) : 0);
		}

		$cart['subtotal'] = fn_format_price($cart['subtotal']);
		$cart['display_subtotal'] = fn_format_price($cart['display_subtotal']);

		$cart['total'] += $cart['tax_subtotal'];

		$cart['total'] = fn_format_price($cart['total'] + $cart['shipping_cost']);

		if (!empty($cart['subtotal_discount'])) {
			$cart['total'] -= ($cart['subtotal_discount'] < $cart['total']) ? $cart['subtotal_discount'] : $cart['total'];
		}
	}

	if (fn_check_suppliers_functionality ()) {
		$cart['companies'] = fn_get_products_companies($cart_products);
		$cart['have_suppliers'] = fn_check_companies_have_suppliers($cart['companies']);
	}

	fn_set_hook('calculate_cart', $cart, $cart_products, $auth, $calculate_shipping, $calculate_taxes, $apply_cart_promotions);

	$cart['recalculate'] = false;

	return array (
		$cart_products,
		$shipping_rates
	);
}

function fn_cart_is_empty($cart)
{
	$result = true;

	if (!empty($cart['products'])) {
		foreach ($cart['products'] as $v) {
			if (!isset($v['extra']['exclude_from_calculate']) && empty($v['extra']['parent'])) {
				$result = false;
				break;
			}
		}
	}

	fn_set_hook('is_cart_empty', $cart, $result);

	return $result;
}

/**
 * Calculate total cost of products in cart
 *
 * @param array $cart cart information
 * @param array $cart_products cart products
 * @param char $type S - cost for shipping, A - all, C - all, exception excluded from calculation
 * @return int products cost
 */
function fn_get_products_cost($cart, $cart_products, $type = 'S')
{
	$cost = 0;

	if (is_array($cart_products)) {
		foreach ($cart_products as $k => $v) {
			if ($type == 'S') {
				if (($v['is_edp'] == 'Y' && $v['edp_shipping'] != 'Y') || $v['free_shipping'] == 'Y' || fn_exclude_from_shipping_calculate($cart['products'][$k])) {
					continue;
				}
			} elseif ($type == 'C') {
				if (isset($v['exclude_from_calculate'])) {
					continue;
				}
			}
			if (isset($v['price'])) {
				$cost += $v['subtotal'];
			}
		}
	}

	return $cost;
}

/**
 * Calculate total weight of products in cart
 *
 * @param array $cart cart information
 * @param array $cart_products cart products
 * @param char $type S - weight for shipping, A - all, C - all, exception excluded from calculation
 * @return int products weight
 */
function fn_get_products_weight($cart, $cart_products, $type = 'S')
{
	$weight = 0;

	if (is_array($cart_products)) {
		foreach ($cart_products as $k => $v) {
			if ($type == 'S') {
				if (($v['is_edp'] == 'Y' && $v['edp_shipping'] != 'Y') || $v['free_shipping'] == 'Y' || fn_exclude_from_shipping_calculate($cart['products'][$k])) {
					continue;
				}
			} elseif ($type == 'C') {
				if (isset($v['exclude_from_calculate'])) {
					continue;
				}
			}

			if (isset($v['weight'])) {
				$weight += ($v['weight'] * $v['amount']);
			}
		}
	}

	return !empty($weight) ? sprintf("%.2f", $weight) : '0.01';
}

/**
 * Calculate total quantity of products in cart
 *
 * @param array $cart cart information
 * @param array $cart_products cart products
 * @param char $type S - quantity for shipping, A - all, C - all, exception excluded from calculation
 * @return int products quantity
 */
function fn_get_products_amount($cart, $cart_products, $type = 'S')
{
	$amount = 0;

	foreach ($cart_products as $k => $v) {
		if ($type == 'S') {
			if (($v['is_edp'] == 'Y' && $v['edp_shipping'] != 'Y') || $v['free_shipping'] == 'Y' || fn_exclude_from_shipping_calculate($cart['products'][$k])) {
				continue;
			}
		} elseif ($type == 'C') {
			if (isset($v['exclude_from_calculate'])) {
				continue;
			}
		}

		$amount += $v['amount'];
	}

	return $amount;
}

// Get Payment processor data
function fn_get_processor_data($payment_id)
{
	$pdata = db_get_row("SELECT processor_id, params FROM ?:payments WHERE payment_id = ?i", $payment_id);
	if (empty($pdata)) {
		return false;
	}

	$processor_data = db_get_row("SELECT * FROM ?:payment_processors WHERE processor_id = ?i", $pdata['processor_id']);
	$processor_data['params'] = unserialize($pdata['params']);

	$processor_data['currencies'] = (!empty($processor_data['currencies'])) ? explode(',', $processor_data['currencies']) : array();

	return $processor_data;
}

//
// Calculate shipping rate
//
function fn_calculate_shipping_rate($package, $rate_value)
{
	$rate_value = unserialize($rate_value);

	$base_cost = $package['C'];
	$shipping_cost = 0;

	foreach ($package as $type => $amount) {
		if (isset($rate_value[$type]) && is_array($rate_value[$type])) {
			$__rval = array_reverse($rate_value[$type], true);
			foreach ($__rval as $__amnt => $__data) {
				if ($__amnt < $amount) {
					/*if (!empty($__data['per_unit']) && $__data['per_unit'] == 'Y') {
					$__data['value'] = (($__data['type'] == 'F') ? $__data['value'] : ($base_cost * $__data['value'])/100) * $package[$type];
					}*/

					$shipping_cost += (($__data['type'] == 'F') ? $__data['value'] : ($base_cost * $__data['value'])/100) * ((!empty($__data['per_unit']) && $__data['per_unit'] == 'Y') ? $package[$type] : 1);
					break;
				}
			}
		}
	}

	fn_set_hook('calculate_shipping_rate', $shipping_cost, $package, $rate_value);

	return fn_format_price($shipping_cost);
}

//
// Calculate shipping rates based on cart data and user info
//
function fn_calculate_shipping_rates(&$cart, &$cart_products, $auth, $calculate_selected = false)
{
	$shipping_rates = array();

	$condition = '';
	if ($calculate_selected == true) {
		$shipping_ids = !empty($cart['shipping']) ? array_keys($cart['shipping']) : array();
		if (!empty($shipping_ids)) {
			$condition = db_quote(" AND a.shipping_id IN (?n)", $shipping_ids);
		} else {
			return array();
		}
	}


	$condition .= fn_get_localizations_condition('a.localization');

	$location = fn_get_customer_location($auth, $cart);
	$destination_id = fn_get_available_destination($location);


	$package_infos = fn_prepare_package_info($cart, $cart_products);

	foreach ($package_infos as $o_id => $package_info) {
		
		$c = fn_get_company_condition('a.company_id', false, $o_id, false, true);
		$companies = Registry::get('s_companies');
		
		if (!empty($companies) && !empty($companies[$o_id]['shippings'])) {
			if (trim($c)) {
				$c = "$c OR ";
			}
			$c .= db_quote('a.shipping_id IN (?n)', explode(',', $companies[$o_id]['shippings']));
			$c = "($c)";
		}
		if (trim($c)) {
			$c = " AND $c";
		}
		
		//TODO select companies shippings
		fn_set_hook('calculate_shipping_rates', $c, $o_id);

		if (AREA == 'C') {
			$condition .= " AND (" . fn_find_array_in_set($auth['usergroup_ids'], 'a.usergroup_ids', true) . ")";
		}

		$shipping_methods = db_get_hash_array("SELECT a.shipping_id, a.rate_calculation, a.service_id, b.shipping as name, b.delivery_time FROM ?:shippings as a LEFT JOIN ?:shipping_descriptions as b ON a.shipping_id = b.shipping_id AND b.lang_code = ?s WHERE (a.min_weight <= ?d AND (a.max_weight >= ?d OR a.max_weight = 0.00)) AND a.status = 'A' ?p ?p ORDER BY a.position", 'shipping_id', CART_LANGUAGE, $package_info['W'], $package_info['W'], $condition, $c);
		
		if (empty($shipping_methods)) {
			continue;
		}

		$found_rates = array();
		foreach ($shipping_methods as $method)
	{
			// Manual rate calculation
			if ($method['rate_calculation'] == 'M') {
				if ($destination_id !== false) {
					$rate_data = db_get_row("SELECT rate_id, rate_value FROM ?:shipping_rates WHERE shipping_id = ?i AND destination_id = ?i", $method['shipping_id'], $destination_id);

					if (!empty($rate_data)) {
						$found_rates[$method['shipping_id']] = fn_calculate_shipping_rate($package_info, $rate_data['rate_value']);
					}
				}

				// Realtime rate calculation
			} else {
				$charge = db_get_field("SELECT rate_value FROM ?:shipping_rates WHERE shipping_id = ?i AND destination_id = 0", $method['shipping_id']);
				$rate_data = fn_calculate_realtime_shipping_rate($method['service_id'], $location, $package_info, $auth);

				if ($rate_data !== false) {
					$found_rates[$method['shipping_id']] = $rate_data['cost'];
					$found_rates[$method['shipping_id']] += fn_calculate_shipping_rate($package_info, $charge);
				}
			}
		}

		$shipping_freight = 0;
		foreach ($cart_products as $v) {
			if (($v['is_edp'] != 'Y' || ($v['is_edp'] == 'Y' && $v['edp_shipping'] == 'Y')) && $v['free_shipping'] != 'Y') {
				$shipping_freight += ($v['shipping_freight'] * $v['amount']);
			}
		}

	foreach ($shipping_methods as $method) {
		$shipping_rates[$method['shipping_id']]['name'] = $method['name'];
			$shipping_rates[$method['shipping_id']]['delivery_time'] = $method['delivery_time'];
	  $shipping_rates[$method['shipping_id']]['rates'] = array();
	}

		foreach ($found_rates as $shipping_id => $rate_value) {
			/*if (!isset($shipping_rates[$shipping_id])) {
				$shipping_rates[$shipping_id]['name'] = $shipping_methods[$shipping_id]['name'];
				$shipping_rates[$shipping_id]['delivery_time'] = $shipping_methods[$shipping_id]['delivery_time'];
			}*/

			$shipping_rates[$shipping_id]['rates'][$o_id] = $rate_value + $shipping_freight;
		}
	}

	return $shipping_rates;
}

//
// Returns customer location or default location
//
function fn_get_customer_location($auth, $cart, $billing = false)
{

	$s_info = array();
	$prefix = 's';
	if ($billing == true) {
		$prefix = 'b';
	}

	$fields = array (
		'country',
		'state',
		'city',
		'zipcode',
		'address',
		'address_2',
	);

	$u_info = (!empty($cart['user_data'])) ? $cart['user_data'] : ((empty($cart['user_data']) && !empty($auth['user_id'])) ? fn_get_user_info($auth['user_id'], true, $cart['profile_id']) : array());

	// Fill basic fields
	foreach ($fields as $field) {
		$s_info[$field] = !empty($u_info[$prefix . '_' . $field]) ? $u_info[$prefix . '_' . $field] : Registry::get("settings.General.default_$field");
	}

	// Add phone
	$s_info['phone'] = !empty($u_info['phone']) ? $u_info['phone'] : Registry::get('settings.General.default_phone');

	// Add residential address flag
	$s_info['residential_address'] = (!empty($u_info['residential_address'])) ? $u_info['residential_address'] : 'Y';

	// Get First and Last names
	$u_info['firstname'] = !empty($u_info['firstname']) ? $u_info['firstname'] : 'John';
	$u_info['lastname'] = !empty($u_info['lastname']) ? $u_info['lastname'] : 'Doe';

	if ($prefix == 'b') {
		$s_info['firstname'] = (!empty($u_info['b_firstname'])) ? $u_info['b_firstname'] : $u_info['firstname'];
		$s_info['lastname'] = (!empty($u_info['b_lastname'])) ? $u_info['b_lastname'] : $u_info['lastname'];
	} else {
		$s_info['firstname'] = (!empty($u_info['s_firstname'])) ? $u_info['s_firstname'] : (!empty($u_info['b_firstname']) ? $u_info['b_firstname'] : $u_info['firstname']);
		$s_info['lastname'] = (!empty($u_info['s_lastname'])) ? $u_info['s_lastname'] : (!empty($u_info['b_lastname']) ? $u_info['b_lastname'] : $u_info['lastname']);
	}

	// Get country/state descriptions
	$avail_country = db_get_field("SELECT COUNT(*) FROM ?:countries WHERE code = ?s AND status = 'A'", $s_info['country']);
	if (empty($avail_country)) {
		return array();
	}

	$avail_state = db_get_field("SELECT COUNT(*) FROM ?:states WHERE country_code = ?s AND code = ?s AND status = 'A'", $s_info['country'], $s_info['state']);
	if (empty($avail_state)) {
		$s_info['state'] = '';
	}

	return $s_info;
}

//
// Calculate taxes for the products
//
function fn_calculate_taxes(&$cart, &$cart_products, &$shipping_rates, $auth)
{
	$calculated_data = array();
	
	if (Registry::get('settings.General.tax_calculation') == 'unit_price') {
		// Tax calculation method based on UNIT PRICE
		
		// Calculate product taxes
		foreach ($cart_products as $k => $product) {
			$taxes = fn_get_product_taxes($k, $cart, $cart_products);
			
			if (empty($taxes)) {
				continue;
			}

			if (isset($product['subtotal'])) {
				$price = fn_format_price($product['subtotal'] / $product['amount']);
				
				$calculated_data['P_' . $k] = fn_calculate_tax_rates($taxes, $price, $product['amount'], $auth, $cart);

				$cart_products[$k]['tax_summary'] = array('included' => 0, 'added' => 0, 'total' => 0); // tax summary for 1 unit of product

				// Apply taxes to product subtotal
				if (!empty($calculated_data['P_' . $k])) {
					foreach ($calculated_data['P_' . $k] as $_k => $v) {
						$cart_products[$k]['taxes'][$_k] = $v;
						if ($taxes[$_k]['price_includes_tax'] != 'Y') {
							$cart_products[$k]['tax_summary']['added'] += $v['tax_subtotal'];
						} else {
							$cart_products[$k]['tax_summary']['included'] += $v['tax_subtotal'];
						}
					}
					$cart_products[$k]['tax_summary']['total'] = $cart_products[$k]['tax_summary']['added'] + $cart_products[$k]['tax_summary']['included'];
				}
			}
		}
		
		// Calculate shipping taxes
		if (!empty($shipping_rates)) {
			foreach ($shipping_rates as $shipping_id => $shipping) {
				$taxes = fn_get_shipping_taxes($shipping_id, $shipping_rates, $cart);

				if (!empty($taxes)) {

					$shipping_rates[$shipping_id]['taxes'] = array();
					$calculate_rate = true;

					if (!empty($cart['shipping'][$shipping_id])) {
						foreach ($cart['shipping'][$shipping_id]['rates'] as $k => $v) {
							$calculated_data['S_' . $shipping_id . '_' . $k] = fn_calculate_tax_rates($taxes, $v, 1, $auth, $cart);
							
							if (!empty($calculated_data['S_' . $shipping_id . '_' . $k])) {
								foreach ($calculated_data['S_' . $shipping_id . '_' . $k] as $__k => $__v) {
									if ($taxes[$__k]['price_includes_tax'] != 'Y') {
										$cart['display_shipping_cost'] += Registry::get('settings.Appearance.cart_prices_w_taxes') == 'Y' ? $__v['tax_subtotal'] : 0;
										$cart['tax_subtotal'] += $__v['tax_subtotal'];
									}

									if ($cart['stored_taxes'] == 'Y') {
										$cart['taxes'][$__k]['applies']['S_' . $shipping_id . '_' . $k] = $__v['tax_subtotal'];
									}
								}
								
								$shipping_rates[$shipping_id]['taxes']['S_' . $shipping_id . '_' . $k] = $calculated_data['S_' . $shipping_id . '_' . $k];
								$calculate_rate = false;
							}
						}
					}

					if ($calculate_rate) {
						foreach ($shipping['rates'] as $k => $v) {
							if (isset($shipping['rates'][$k])) {
								$cur_shipping_rates = fn_calculate_tax_rates($taxes, $v, 1, $auth, $cart);
								if (!empty($cur_shipping_rates)) {
									$shipping_rates[$shipping_id]['taxes']['S_' . $shipping_id . '_' . $k] = $cur_shipping_rates;
								}
							}
						}
					}
				}
			}
		}
		
	} else {
		// Tax calculation method based on SUBTOTAL
		
		// Calculate discounted subtotal
		if (!isset($cart['subtotal_discount'])) {
			$cart['subtotal_discount'] = 0;
		}
		$discounted_subtotal = $cart['original_subtotal'] - $cart['subtotal_discount'];
		
		// Get discount distribution coefficient (DDC) between taxes
		$ddc = $discounted_subtotal / $cart['original_subtotal'];
		
		//
		// Group subtotal by taxes
		//
		$subtotal = array();
		
		// Get products taxes
		foreach ($cart_products as $k => $product) {
			$taxes = fn_get_product_taxes($k, $cart, $cart_products);
			
			if (!empty($taxes)) {
				foreach ($taxes as $tax_id => $tax) {
					if (empty($subtotal[$tax_id])) {
						$subtotal[$tax_id] = $tax;
						$subtotal[$tax_id]['subtotal'] = $subtotal[$tax_id]['applies']['P'] = $subtotal[$tax_id]['applies']['S'] = 0;
						$subtotal[$tax_id]['applies']['items']['P'] = $subtotal[$tax_id]['applies']['items']['S'] = array();
					}
					
					$subtotal[$tax_id]['subtotal'] += $product['subtotal'];
					$subtotal[$tax_id]['applies']['P'] += $product['subtotal'];
					$subtotal[$tax_id]['applies']['items']['P'][$k] = true;
				}
			}
		}
		
		// Get shipping taxes
		if (!empty($shipping_rates)) {
			foreach ($shipping_rates as $shipping_id => $shipping) {
				// Calculate taxes for each shipping rate
				$taxes = fn_get_shipping_taxes($shipping_id, $shipping_rates, $cart);

				if (!empty($taxes)) {
					$shipping_rates[$shipping_id]['taxes'] = array();
					foreach ($shipping['rates'] as $k => $v) {
						$tax = fn_calculate_tax_rates($taxes, fn_format_price($v), 1, $auth, $cart);
						$shipping_rates[$shipping_id]['taxes']['S_' . $shipping_id . '_' . $k] = $tax;
					}
				}
				
				if (!isset($cart['shipping'][$shipping_id])) {
					continue;
				}

				if (!empty($taxes)) {
					foreach ($taxes as $tax_id => $tax) {
						if (empty($subtotal[$tax_id])) {
							$subtotal[$tax_id] = $tax;
							$subtotal[$tax_id]['subtotal'] = $subtotal[$tax_id]['applies']['P'] = $subtotal[$tax_id]['applies']['S'] = 0;
							$subtotal[$tax_id]['applies']['items']['P'] = $subtotal[$tax_id]['applies']['items']['S'] = array();
						}
						
						$subtotal[$tax_id]['subtotal'] += array_sum($cart['shipping'][$shipping_id]['rates']);
						$subtotal[$tax_id]['applies']['S'] += array_sum($cart['shipping'][$shipping_id]['rates']);
						$subtotal[$tax_id]['applies']['items']['S'][$shipping_id] = true;
					}
				}
			}
		}
		
		// Apply DDC and calculate tax rates
		$calculated_taxes = array();
		
		foreach ($subtotal as $tax_id => $_st) {
			if (empty($_st['tax_id'])) {
				$_st['tax_id'] = $tax_id;
			}
			$tax = fn_calculate_tax_rates(array($_st), fn_format_price($_st['applies']['P'] * $ddc + $_st['applies']['S']), 1, $auth, $cart);
			
			if (empty($tax)) {
				continue;
			}
			
			$calculated_data[$tax_id] = reset($tax);
			$products_tax = fn_calculate_tax_rates(array($_st), fn_format_price($_st['applies']['P'] * $ddc), 1, $auth, $cart);
			
			$calculated_data[$tax_id]['applies']['P'] = $products_tax[$tax_id]['tax_subtotal'];
			
			$shipping_tax = fn_calculate_tax_rates(array($_st), fn_format_price($_st['applies']['S']), 1, $auth, $cart);
			$calculated_data[$tax_id]['applies']['S'] = $shipping_tax[$tax_id]['tax_subtotal'];
			
			$calculated_data[$tax_id]['tax_subtotal'] = $products_tax[$tax_id]['tax_subtotal'] + $shipping_tax[$tax_id]['tax_subtotal'];
			$calculated_data[$tax_id]['applies']['items'] = $_st['applies']['items'];
		}
	}
	
	fn_apply_calculated_taxes($calculated_data, $cart);

	return false;
}

function fn_get_product_taxes($idx, $cart, $cart_products)
{
	if ($cart['stored_taxes'] == 'Y') {
		$_idx = '';
		if (isset($cart['products'][$idx]['original_product_data']['cart_id'])) {
			$_idx = $cart['products'][$idx]['original_product_data']['cart_id'];
		}
		
		$taxes = array();
		foreach ((array)$cart['taxes'] as $_k => $_v) {
			$tax = array();
			if (!empty($_v['applies']['P_'.$idx]) || isset($_v['applies']['items']['P'][$idx]) || !empty($_v['applies']['P_'.$_idx]) || isset($_v['applies']['items']['P'][$_idx])) {
				$taxes[$_k] = $_v;
			}
		}
	} else {
		$taxes = fn_get_set_taxes($cart_products[$idx]['tax_ids']);
	}
	
	return $taxes;
}

function fn_get_shipping_taxes($shipping_id, $shipping_rates, $cart)
{
	static $_taxes;
	
	$tax_ids = array();
	if (defined('ORDER_MANAGEMENT')) {
		if (empty($_taxes)) {
			$_taxes = db_get_hash_single_array("SELECT tax_ids, shipping_id FROM ?:shippings WHERE shipping_id IN (?n)", array('shipping_id', 'tax_ids'), array_keys($shipping_rates));
		}
		
		if (!empty($_taxes)) {
			foreach ($_taxes as $_ship => $_tax) {
				if (!empty($_tax)) {
					$_tids = explode(',', $_tax);
					foreach ($_tids as $_tid) {
						$tax_ids[$_ship] = $_tid;
					}
				}
			}
		}
	}
	
	if ($cart['stored_taxes'] == 'Y') {
		$taxes = array();

		foreach ((array)$cart['taxes'] as $_k => $_v) {
			isset($_v['applies']['items']['S'][$shipping_id]) ? $exists = true : $exists = false;
			foreach ($_v['applies'] as $aid => $av) {
				if (strpos($aid, 'S_' . $shipping_id . '_') !== false) {
					$exists = true;

				}
			}
			if ($exists == true || (!empty($tax_ids[$shipping_id]) && $tax_ids[$shipping_id] == $_k)) {
				$taxes[$_k] = $_v;
				$taxes[$_k]['applies'] = array();
			}
		}
	} else {
		$taxes = array();
		$tax_ids = db_get_field("SELECT tax_ids FROM ?:shippings WHERE shipping_id = ?i", $shipping_id);
		if (!empty($tax_ids)) {
			$taxes = db_get_hash_array("SELECT tax_id, address_type, priority, price_includes_tax, regnumber FROM ?:taxes WHERE tax_id IN (?n) AND status = 'A' ORDER BY priority", 'tax_id', explode(',', $tax_ids));
		}
	}
	
	return $taxes;
}

function fn_apply_calculated_taxes($calculated_data, &$cart)
{
	if (!empty($calculated_data)) {
		if (Registry::get('settings.General.tax_calculation') == 'unit_price') {
			// Based on the unit price
			$taxes_data = array();
			foreach ($calculated_data as $id => $_taxes) {
				if (empty($_taxes)) {
					continue;
				}
				foreach ($_taxes as $k => $v) {
					if (empty($taxes_data[$k])) {
						$taxes_data[$k] = $v;
						$taxes_data[$k]['tax_subtotal'] = 0;
					}
					$taxes_data[$k]['applies'][$id] = $v['tax_subtotal'];
					$taxes_data[$k]['tax_subtotal'] += $v['tax_subtotal'];
				}
			}
			
			$cart['taxes'] = $taxes_data;
			
		} else {
			// Based on the order subtotal
			$cart['tax_subtotal'] = 0;
			$cart['tax_summary'] = array(
				'included' => 0,
				'added' => 0,
				'total' => 0
			);
			
			foreach ($calculated_data as $tax_id => $v) {
				$cart['taxes'][$tax_id] = $v;
				
				if ($v['price_includes_tax'] == 'Y') {
					$cart['tax_summary']['included'] += $v['tax_subtotal'];
				} else {
					$cart['tax_summary']['added'] += $v['tax_subtotal'];
				}
				
				$cart['tax_summary']['total'] += $v['tax_subtotal'];
			}
		}
		
	} else { // FIXME!!! Test on order management
		$cart['taxes'] = array();
		$cart['tax_summary'] = array();
	}
	
	return true;
}

function fn_format_rate_value($rate_value, $rate_type, $decimals='2', $dec_point='.', $thousands_sep=',', $coefficient = '')
{
	if (!empty($coefficient) && @$rate_type != 'P') {
		$rate_value = $rate_value / floatval($coefficient);
	}

	if (empty($rate_type)) {
		$rate_type = 'F';
	}

	$value = number_format(fn_format_price($rate_value, '', $decimals), $decimals, $dec_point, $thousands_sep);
	if ($rate_type == 'F') { // Flat rate
		return $value;
	}
	elseif ($rate_type == 'P') { // Percent rate
		return $value.'%';
	}

	return $rate_value;

}

function fn_check_amount_in_stock($product_id, $amount, $product_options, $cart_id, $is_edp, $original_amount, &$cart, $update_id = 0)
{
	fn_set_hook('check_amount_in_stock', $product_id, $amount, $product_options, $cart_id, $is_edp, $original_amount, $cart);

	// If the product is EDP don't track the inventory
	if ($is_edp == 'Y') {
		return 1;
	}

	$product = db_get_row("SELECT ?:products.tracking, ?:products.amount, ?:products.min_qty, ?:products.max_qty, ?:products.qty_step, ?:products.list_qty_count, ?:product_descriptions.product FROM ?:products LEFT JOIN ?:product_descriptions ON ?:product_descriptions.product_id = ?:products.product_id AND lang_code = ?s WHERE ?:products.product_id = ?i", CART_LANGUAGE, $product_id);

	if (Registry::get('settings.General.inventory_tracking') == 'Y') {
		// Track amount for ordinary product
		if ($product['tracking'] == 'B') {
			$current_amount = $product['amount'];

		// Track amount for product with options
		} elseif ($product['tracking'] == 'O') {
			$selectable_cart_id = fn_generate_cart_id($product_id, array('product_options' => $product_options), true);
			$current_amount = db_get_field("SELECT amount FROM ?:product_options_inventory WHERE combination_hash = ?i", $selectable_cart_id);
			$current_amount = intval($current_amount);
		}

		if (!empty($cart['products']) && is_array($cart['products'])) {
			$product_not_in_cart = true;
			foreach ($cart['products'] as $k => $v) {
				if ($k != $cart_id){ // Check if the product with the same selectable options already exists ( for tracking = O)
					if (($product['tracking'] == 'B' && $v['product_id'] == $product_id) || ($product['tracking'] == 'O' && @$v['selectable_cart_id'] == $selectable_cart_id)) {
						$current_amount -= $v['amount'];
					}
				} else {
					$product_not_in_cart = false;
				}
			}

			if ($product['tracking'] == 'B' && !empty($update_id) && $product_not_in_cart && !empty($cart['products'][$update_id])) {
				$current_amount += $cart['products'][$update_id]['amount'];
			}

			if ($product['tracking'] == 'O') {
				// Store cart_id for selectable options in cart variable, so if the same product is added to
				// the cart with the same selectable options, but different text options,
				// the total amount will be tracked anyway as it is the one product
				if (!empty($selectable_cart_id) && isset($cart['products'][$cart_id])) {
					$cart['products'][$cart_id]['selectable_cart_id'] = $selectable_cart_id;
				}
			}
		}
	}

	$min_qty = 1;

	if (!empty($product['min_qty']) && $product['min_qty'] > $min_qty) {
		$min_qty = $product['min_qty'];
	}

	if (!empty($product['qty_step']) && $product['qty_step'] > $min_qty) {
		$min_qty = $product['qty_step'];
	}

	if (empty($product['list_qty_count']) && !empty($product['qty_step'])) {
		$product['list_qty_count'] = intval((isset($current_amount) ? $current_amount : $product['amount']) / $product['qty_step']);
	}

	if (!empty($product['qty_step']) && !empty($product['list_qty_count'])) {
		$per_item = 0;
		$amount_corrected = false;
		if (Registry::get('settings.General.allow_negative_amount') == 'Y' && !empty($product['max_qty'])) {
			$_amount = $product['max_qty'];
		} else {
			$_amount = isset($current_amount) ? $current_amount : $product['amount'];
		}
		for ($i = 1; $per_item <= ($_amount - $product['qty_step']); $i++) {
			$per_item = $product['qty_step'] * $i;

			if ($i > $product['list_qty_count'] && $amount <= $per_item - $product['qty_step']) {
				break;
			}

			if ((!empty($product['max_qty']) && $per_item > $product['max_qty']) || (!empty($product['min_qty']) && $per_item < $product['min_qty'])) {
				continue;
			}

			if ($amount == $per_item) {
				break;
			}

			if ($amount != $per_item && $amount < $per_item) {
				$amount = $per_item;
				$amount_corrected = true;
				break;
			}
		}
		if ($amount > $per_item) {
			$amount = $per_item;
			$amount_corrected = true;
		}
		if ($amount_corrected) {
			fn_set_notification('N', fn_get_lang_var('notice'), str_replace('[product]', $product['product'], fn_get_lang_var('text_cart_amount_changed')));
		}
	}

	if ($amount < $min_qty) {
		fn_set_notification('W', fn_get_lang_var('notice'), str_replace(array('[product]' , '[quantity]'), array($product['product'] , $min_qty), fn_get_lang_var('text_cart_min_qty')));
		$amount = $min_qty;
	} elseif (!empty($product['max_qty']) && $amount > $product['max_qty']) {
		$amount = $product['max_qty'];
		fn_set_notification('W', fn_get_lang_var('notice'), str_replace(array('[product]' , '[quantity]'), array($product['product'], $product['max_qty']), fn_get_lang_var('text_cart_max_qty')));
	} elseif (isset($current_amount) && $current_amount >= 0 && $current_amount - $amount < 0 && Registry::get('settings.General.allow_negative_amount') != 'Y') {
		// For order edit: add original amount to existent amount
		$current_amount += $original_amount;

		if ($current_amount > 0 && $current_amount - $amount < 0 && Registry::get('settings.General.allow_negative_amount') != 'Y') {
			$amount = $current_amount;
			fn_set_notification('N', fn_get_lang_var('notice'), str_replace('[product]', $product['product'], fn_get_lang_var('text_cart_amount_corrected')));
		} elseif ($current_amount - $amount < 0 && Registry::get('settings.General.allow_negative_amount') != 'Y') {
			fn_set_notification('E', fn_get_lang_var('notice'), str_replace('[product]', $product['product'], fn_get_lang_var('text_cart_zero_inventory')));
			return false;
		} elseif ($current_amount <= 0 && $amount <= 0 && Registry::get('settings.General.allow_negative_amount') != 'Y') {
			fn_set_notification('E', fn_get_lang_var('notice'), str_replace('[product]', $product['product'], fn_get_lang_var('text_cart_zero_inventory_and_removed')));
			return false;
		}
	}

	return empty($amount) ? false : $amount;
}

//
// Calculate unique product id in the cart
//
function fn_generate_cart_id($product_id, $extra, $only_selectable = false)
{
	$_cid = array();

	if (!empty($extra['product_options']) && is_array($extra['product_options'])) {
		foreach ($extra['product_options'] as $k => $v) {
			if ($only_selectable == true && ((string)intval($v) != $v || db_get_field("SELECT inventory FROM ?:product_options WHERE option_id = ?i", $k) != 'Y')) {
				continue;
			}
			$_cid[] = $v;
		}
	}

	if (isset($extra['exclude_from_calculate'])) {
		$_cid[] = $extra['exclude_from_calculate'];
	}

	fn_set_hook('generate_cart_id', $_cid, $extra, $only_selectable);

	natsort($_cid);
	array_unshift($_cid, $product_id);
	$cart_id = fn_crc32(implode('_', $_cid));

	return $cart_id;
}


//
// Normalize product amount
//
function fn_normalize_amount($amount = '1')
{
	$amount = abs(intval($amount));

	return empty($amount) ? 0 : $amount;
}

function fn_order_placement_routines($order_id, $force_notification = array(), $clear_cart = true, $action = '')
{
	$order_info = fn_get_order_info($order_id, true);

	if (!empty($_SESSION['cart']['placement_action'])) {
		if (empty($action)) {
			$action = $_SESSION['cart']['placement_action'];
		}
		unset($_SESSION['cart']['placement_action']);
	}

	if (AREA == 'C' && !empty($order_info['user_id'])) {
		$__fake = '';
		fn_save_cart_content($__fake, $order_info['user_id']);
	}

	$edp_data = fn_generate_ekeys_for_edp(array(), $order_info);
	fn_order_notification($order_info, $edp_data, $force_notification);

	$_error = false;

	if ($action == 'save') {
		fn_set_notification('N', fn_get_lang_var('congratulations'), fn_get_lang_var('text_order_saved_successfully'));
	} else {
		if ($order_info['status'] == STATUS_PARENT_ORDER) {
			$child_orders = db_get_hash_single_array("SELECT order_id, status FROM ?:orders WHERE parent_order_id = ?i", array('order_id', 'status'), $order_id);
			$status = reset($child_orders);
			$child_orders = array_keys($child_orders);
		} else {
			$status = $order_info['status'];
		}
		if (substr_count('OP', $status) > 0) {
			if ($action == 'repay') {
				fn_set_notification('N', fn_get_lang_var('congratulations'), fn_get_lang_var('text_order_repayed_successfully'));
			} else {
				fn_set_notification('N', fn_get_lang_var('order_placed'), fn_get_lang_var('text_order_placed_successfully'));
			}
		} elseif ($status == 'B') {
			fn_set_notification('N', fn_get_lang_var('order_placed'), fn_get_lang_var('text_order_backordered'));
		} else {
			if (AREA == 'A' || $action == 'repay') {
				if ($status != 'I') {
					fn_set_notification('E', fn_get_lang_var('order_placed'), fn_get_lang_var('text_order_placed_error'));
				}
			} else {
				$_error = true;
				if (!empty($child_orders)) {
					array_unshift($child_orders, $order_id);
				} else {
					$child_orders = array();
					$child_orders[] = $order_id;
				}
				$_SESSION['cart'][($status == 'N' ? 'processed_order_id' : 'failed_order_id')] = $child_orders;
			}
			if ($status == 'N' || ($action == 'repay' && $status == 'I')) {
				fn_set_notification('N', fn_get_lang_var('cancelled'), fn_get_lang_var('text_transaction_cancelled'));
			}
		}
	}

	// Empty cart
	if ($clear_cart == true && $_error == false) {
		$_SESSION['cart'] = array(
			'user_data' => !empty($_SESSION['cart']['user_data']) ? $_SESSION['cart']['user_data'] : array(), 
			'profile_id' => !empty($_SESSION['cart']['profile_id']) ? $_SESSION['cart']['profile_id'] : 0, 
			'user_id' => !empty($_SESSION['cart']['user_id']) ? $_SESSION['cart']['user_id'] : 0,
		);
		
		db_query('DELETE FROM ?:user_session_products WHERE session_id = ?s AND type = ?s', Session::get_id(), 'C');
	}

	fn_set_hook('order_placement_routines', $order_id, $force_notification, $order_info);

	$prefix = ((Registry::get('settings.General.secure_auth') == 'Y') && (AREA == 'C')) ? Registry::get('config.https_location') . '/' : '';

	if (AREA == 'A' || $action == 'repay') {
		fn_redirect($prefix . INDEX_SCRIPT . "?dispatch=orders.details&order_id=$order_id", true);
	} else {
		fn_redirect($prefix . INDEX_SCRIPT . "?dispatch=checkout." . ($_error == true ? (Registry::get('settings.General.one_page_checkout') == 'Y' ? "checkout" : "summary") : "complete&order_id=$order_id"), true);
	}
}

//
// Calculate difference
//
function fn_less_zero($first_arg, $second_arg = 0, $zero = false)
{
	if (!empty($second_arg)) {
		if ($first_arg - $second_arg > 0) {
			return $first_arg - $second_arg;
		} else {
			return 0;
		}
	} else {
		if (empty($zero)) {
			return $first_arg;
		} else {
			return 0;
		}
	}
}

//
// Add product to cart
//
// @param array $product_data array with data for the product to add)(product_id, price, amount, product_options, is_edp)
// @return mixed cart ID for the product if addition is successful and false otherwise
//
function fn_add_product_to_cart($product_data, &$cart, &$auth, $update = false)
{
	$ids = array();
	if (!empty($product_data) && is_array($product_data)) {
		if (!defined('GET_OPTIONS')) {
			list($product_data, $cart) = fn_add_product_options_files($product_data, $cart, $auth, $update);
		}

		fn_set_hook('pre_add_to_cart', $product_data, $cart, $auth, $update);

		foreach ($product_data as $key => $data) {
			if (empty($key)) {
				continue;
			}
			if (empty($data['amount'])) {
				continue;
			}

			$data['stored_price'] = (!empty($data['stored_price']) && AREA != 'C') ? $data['stored_price'] : 'N';

			if (empty($data['extra'])) {
				$data['extra'] = array();
			}

			$product_id = (!empty($data['product_id'])) ? $data['product_id'] : $key;

			// Check if product options exist
			if (!isset($data['product_options'])) {
				$data['product_options'] = fn_get_default_product_options($product_id);
			}

			// Generate cart id
			$data['extra']['product_options'] = $data['product_options'];

			$_id = fn_generate_cart_id($product_id, $data['extra'], false);

			if (isset($data['extra']['exclude_from_calculate'])) {
				if (!empty($cart['products'][$key]) && !empty($cart['products'][$key]['extra']['aoc'])) {
					$cart['saved_product_options'][$cart['products'][$key]['extra']['saved_options_key']] = $data['product_options'];
				}
				if (isset($cart['deleted_exclude_products'][$data['extra']['exclude_from_calculate']][$_id])) {
					continue;
				}
			}
			$amount = fn_normalize_amount(@$data['amount']);

			if (!isset($data['extra']['exclude_from_calculate'])) {
				if ($data['stored_price'] != 'Y') {
					// Check if the product price with options modifiers equals to zero
					$price = fn_get_product_price($product_id, $amount, $auth);
					$price = fn_apply_options_modifiers($data['product_options'], $price, 'P');
					if (!floatval($price)) {
						$data['price'] = isset($data['price']) ? fn_parse_price($data['price']) : 0;
						$zero_price_action = db_get_field("SELECT zero_price_action FROM ?:products WHERE product_id = ?i", $product_id);
						if (($zero_price_action == 'R' || ($zero_price_action == 'A' && floatval($data['price']) < 0)) && AREA == 'C') {
							if ($zero_price_action == 'A') {
								fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('incorrect_price_warning'));
							}
							continue;
						}
						$price = empty($data['price']) ? 0 : $data['price'];
					}
				} else {
					$price = empty($data['price']) ? 0 : $data['price'];
				}
			} else {
				$price = 0;
			}

			$data['is_edp'] = db_get_field('SELECT is_edp FROM ?:products WHERE product_id = ?i', $product_id);
			
			if (!isset($cart['products'][$_id])) { // If product doesn't exists in the cart
				$amount = empty($data['original_amount']) ? fn_check_amount_in_stock($product_id, $amount, $data['product_options'], $_id, $data['is_edp'], 0, $cart, $update == true ? $key : 0) : $data['original_amount'];
				if ($amount === false) {
					continue;
				}

				$cart['products'][$_id]['product_id'] = $product_id;
				$cart['products'][$_id]['amount'] = $amount;
				$cart['products'][$_id]['product_options'] = $data['product_options'];
				$cart['products'][$_id]['price'] = $price;
				$cart['products'][$_id]['stored_price'] = $data['stored_price'];

				fn_define_original_amount($product_id, $_id, $cart['products'][$_id], $data);

				if ($update == true && $key != $_id) {
					unset($cart['products'][$key]);
				}

			} else { // If product is already exist in the cart

				$_initial_amount = empty($cart['products'][$_id]['original_amount']) ? $cart['products'][$_id]['amount'] : $cart['products'][$_id]['original_amount'];

				// If ID changed (options were changed), summ the total amount of old and new products
				if ($update == true && $key != $_id) {
					$amount += $_initial_amount;
					unset($cart['products'][$key]);
				}

				$cart['products'][$_id]['amount'] = fn_check_amount_in_stock($product_id, (($update == true) ? 0 : $_initial_amount) + $amount, $data['product_options'], $_id, (!empty($data['is_edp']) && $data['is_edp'] == 'Y' ? 'Y' : 'N'), 0, $cart, $update == true ? $key : 0);
			}

			$cart['products'][$_id]['extra'] = (empty($data['extra'])) ? array() : $data['extra'];
			$cart['products'][$_id]['stored_discount'] = @$data['stored_discount'];
			if (defined('ORDER_MANAGEMENT')) {
				$cart['products'][$_id]['discount'] = @$data['discount'];
			}

			// Increase product popularity
			if (empty($_SESSION['products_popularity']['added'][$product_id])) {
				$_data = array (
					'product_id' => $product_id,
					'added' => 1,
					'total' => POPULARITY_ADD_TO_CART
				);
				
				db_query("INSERT INTO ?:product_popularity ?e ON DUPLICATE KEY UPDATE added = added + 1, total = total + ?i", $_data, POPULARITY_ADD_TO_CART);
				
				$_SESSION['products_popularity']['added'][$product_id] = true;
			}
			
			$company_id = db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i", $product_id);
			$cart['products'][$_id]['company_id'] = $company_id;
			
			fn_set_hook('add_to_cart', $cart, $product_id, $_id);

			$ids[$_id] = $product_id;
		}

		$cart['recalculate'] = true;
		return $ids;

	} else {
		return false;
	}
}

function fn_form_cart($order_id, &$cart, &$auth)
{
	$order_info = fn_get_order_info($order_id, false, false);

	// Fill the cart
	foreach ($order_info['items'] as $_id => $item) {
		$_item = array (
			$item['product_id'] => array (
				'amount' => $item['amount'],
				'product_options' => @$item['extra']['product_options'],
				'price' => $item['original_price'],
				'stored_discount' => 'Y',
				'stored_price' => 'Y',
				'discount' => @$item['extra']['discount'],
				'original_amount' => $item['amount'], // the original amount, that stored in order
				'original_product_data' => array ( // the original cart ID and amount, that stored in order
					'cart_id' => $_id,
					'amount' => $item['amount'],
				),
			),
		);
		if (isset($item['extra'])) {
			$_item[$item['product_id']]['extra'] = $item['extra'];
		}
		fn_add_product_to_cart($_item, $cart, $auth);
	}

	// Restore custom files
	$dir_path = DIR_CUSTOM_FILES . 'order_data/' . $order_id;
	
	if (is_dir($dir_path)) {
		fn_mkdir(DIR_CUSTOM_FILES . 'sess_data');
		fn_copy($dir_path, DIR_CUSTOM_FILES . 'sess_data');
	}

	$cart['payment_id'] = $order_info['payment_id'];
	$cart['stored_taxes'] = 'Y';
	$cart['stored_discount'] = 'Y';
	$cart['taxes'] = $order_info['taxes'];
	$cart['promotions'] = !empty($order_info['promotions']) ? $order_info['promotions'] : array();

	$cart['shipping'] = (!empty($order_info['shipping'])) ? $order_info['shipping'] : array();
	$cart['stored_shipping'] = array();
	foreach ($cart['shipping'] as $sh_id => $v) {
		if (!empty($v['rates'])) {
			$cart['stored_shipping'][$sh_id] = array_sum($v['rates']);
		}
	}

	$cart['notes'] = $order_info['notes'];
	$cart['payment_info'] = @$order_info['payment_info'];

	// Add order discount
	if (floatval($order_info['subtotal_discount'])) {
		$cart['stored_subtotal_discount'] = 'Y';
		$cart['subtotal_discount'] = $cart['original_subtotal_discount'] = fn_format_price($order_info['subtotal_discount']);
	}

	// Fill the cart with the coupons
	if (!empty($order_info['coupons'])) {
		$cart['coupons'] = $order_info['coupons'];
	}

	// Set the customer if exists
	$_data = array();
	if (!empty($order_info['user_id'])) {
		$_data = db_get_row("SELECT user_id, user_login as login FROM ?:users WHERE user_id = ?i", $order_info['user_id']);
	}
	$auth = fn_fill_auth($_data, array(), false, 'C');
	$auth['tax_exempt'] = $order_info['tax_exempt'];

	// Fill customer info
	$cart['user_data'] = fn_check_table_fields($order_info, 'user_profiles');
	$cart['user_data'] = fn_array_merge(fn_check_table_fields($order_info, 'users'), $cart['user_data']);
	if (!empty($order_info['fields'])) {
		$cart['user_data']['fields'] = $order_info['fields'];
	}
	fn_add_user_data_descriptions($cart['user_data']);

	fn_set_hook('form_cart', $order_info, $cart);

}

//
// Calculate taxes for products or shippings
//
function fn_calculate_tax_rates($taxes, $price, $amount, $auth, &$cart)
{
	static $destination_id;
	static $tax_description;
	static $user_data;

	$taxed_price = $price;

	if (!empty($cart['user_data'])) {
		$profile_fields = fn_get_profile_fields('O', $auth);
		$billing_population = fn_check_profile_fields_population($cart['user_data'], 'B', $profile_fields);
		$shipping_population = fn_check_profile_fields_population($cart['user_data'], 'S', $profile_fields);
	}

	if (empty($auth['user_id']) && (empty($cart['user_data']) || fn_is_empty($cart['user_data']) || $billing_population != true || $shipping_population != true) && defined('CHECKOUT') && Registry::get('settings.Appearance.taxes_using_default_address') !== 'Y') {
		return false;
	}

	if ((empty($destination_id) || $user_data != @$cart['user_data'])) {
		// Get billing location
		$location = fn_get_customer_location($auth, $cart, true);
		$destination_id['B'] = fn_get_available_destination($location);

		// Get shipping location
		$location = fn_get_customer_location($auth, $cart);
		$destination_id['S'] = fn_get_available_destination($location);
	}

	if (!empty($cart['user_data'])) {
		$user_data = $cart['user_data'];
	}
	$_tax = 0;
	$previous_priority = 0;

	foreach ($taxes as $key => $tax) {
		if (empty($tax['tax_id'])) {
			$tax['tax_id'] = $key;
		}

		if (empty($tax['priority'])) {
			$tax['priority'] = 1;
		}

		$_is_zero = floatval($taxed_price);
		if (empty($_is_zero)) {
			continue;
		}

		if (!empty($cart['stored_taxes']) && $cart['stored_taxes'] == 'Y' && !empty($tax['rate_type'])) {
			$rate = array (
				'rate_value' => $tax['rate_value'],
				'rate_type' => $tax['rate_type'],
			);
		} else {
			if (!isset($destination_id[$tax['address_type']])) {
				continue;
			}

			$rate = db_get_row("SELECT destination_id, apply_to, rate_value, rate_type FROM ?:tax_rates WHERE tax_id = ?i AND destination_id = ?i", $tax['tax_id'], $destination_id[$tax['address_type']]);
			if (!@floatval($rate['rate_value'])) {
				continue;
			}
		}


		$base_price = ($tax['priority'] == $previous_priority) ? $previous_price : $taxed_price;

		if ($rate['rate_type'] == 'P') { // Percent dependence
			// If tax is included into the price
			if ($tax['price_includes_tax'] == 'Y') {
				$_tax = fn_format_price($base_price - $base_price / ( 1 + ($rate['rate_value'] / 100)));
				// If tax is NOT included into the price
			} else {
				$_tax = fn_format_price($base_price * ($rate['rate_value'] / 100));
				$taxed_price += $_tax;
			}

		} else {
			$_tax = fn_format_price($rate['rate_value']);
			// If tax is NOT included into the price
			if ($tax['price_includes_tax'] != 'Y') {
				$taxed_price += $_tax;
			}
		}

		$previous_priority = $tax['priority'];
		$previous_price = $base_price;

		if (empty($tax_description[$tax['tax_id']])) {
			$tax_description[$tax['tax_id']] = db_get_field("SELECT tax FROM ?:tax_descriptions WHERE tax_id = ?i AND lang_code = ?s", $tax['tax_id'], CART_LANGUAGE);
		}

		$taxes_data[$tax['tax_id']] = array (
			'rate_type' => $rate['rate_type'],
			'rate_value' => $rate['rate_value'],
			'price_includes_tax' => $tax['price_includes_tax'],
			'regnumber' => @$tax['regnumber'],
			'priority' => @$tax['priority'],
			'tax_subtotal' => fn_format_price($_tax * $amount),
			'description' => $tax_description[$tax['tax_id']],
		);
	}

	return empty($taxes_data) ? false : $taxes_data;
}

//
// Get order status data
//
function fn_get_status_data($status, $type = STATUSES_ORDER, $object_id = 0, $lang_code = CART_LANGUAGE)
{
	$data = db_get_row("SELECT * FROM ?:status_descriptions WHERE status = ?s AND type = ?s AND lang_code = ?s", $status, $type, $lang_code);

	fn_set_hook('get_status_data', $data, $status, $type, $object_id, $lang_code);

	return $data;
}

//
// Get all order statuses
//
function fn_get_statuses($type = STATUSES_ORDER, $simple = false, $additional_statuses = false, $exclude_parent = false, $lang_code = CART_LANGUAGE)
{
	if ($simple) {
		$statuses = db_get_hash_single_array("SELECT a.status, b.description FROM ?:statuses as a LEFT JOIN ?:status_descriptions as b ON b.status = a.status AND b.type = a.type AND b.lang_code = ?s WHERE a.type = ?s ORDER BY b.description", array('status', 'description'), $lang_code, $type);
		if ($type == STATUSES_ORDER && !empty($additional_statuses)) {
			$statuses['N'] = fn_get_lang_var('incompleted', $lang_code);
			if (empty($exclude_parent)) {
				$statuses['T'] = fn_get_lang_var('parent_order', $lang_code);
			}
		}
	} else {
		$statuses = db_get_hash_array("SELECT a.status, b.description FROM ?:statuses as a LEFT JOIN ?:status_descriptions as b ON b.status = a.status AND b.type = a.type AND b.lang_code = ?s WHERE a.type = ?s ORDER BY b.description", 'status', $lang_code, $type);
		foreach ($statuses as $status => $data) {
			$statuses[$status] = fn_array_merge($statuses[$status], fn_get_status_params($status, $type));
		}
		if ($type == STATUSES_ORDER && !empty($additional_statuses)) {
			$statuses[STATUS_INCOMPLETED_ORDER] = array (
				'status' => STATUS_INCOMPLETED_ORDER,
				'description' => fn_get_lang_var('incompleted', $lang_code),
				'inventory' => 'I',
				'type' => STATUSES_ORDER,
			);
			if (empty($exclude_parent)) {
				$statuses[STATUS_PARENT_ORDER] = array (
					'status' => STATUS_PARENT_ORDER,
					'description' => fn_get_lang_var('parent_order', $lang_code),
					'inventory' => 'I',
					'type' => STATUSES_ORDER,
				);
			}
		}
	}

	return $statuses;
}

function fn_get_status_params($status, $type = STATUSES_ORDER)
{

	return db_get_hash_single_array("SELECT param, value FROM ?:status_data WHERE status = ?s AND type = ?s", array('param', 'value'), $status, $type);
}


//
// Delete product from the cart
//
function fn_delete_cart_product(&$cart, $cart_id, $full_erase = true)
{
	fn_set_hook('delete_cart_product', $cart, $cart_id, $full_erase);

	if (!empty($cart_id) && !empty($cart['products'][$cart_id])) {
		// Decrease product popularity
		$product_id = $cart['products'][$cart_id]['product_id'];
		
		$_data = array (
			'product_id' => $product_id,
			'deleted' => 1,
			'total' => 0
		);
		
		db_query("INSERT INTO ?:product_popularity ?e ON DUPLICATE KEY UPDATE deleted = deleted + 1, total = total - ?i", $_data, POPULARITY_DELETE_FROM_CART);
		
		unset($_SESSION['products_popularity']['added'][$product_id]);
		
		// Delete saved product files
		if (isset($cart['products'][$cart_id]['extra']['custom_files'])) {
			foreach ($cart['products'][$cart_id]['extra']['custom_files'] as $option_id => $images) {
				if (!empty($images)) {
					foreach ($images as $image) {
						@unlink($image['path']);
						@unlink($image['path'] . '_thumb');
					}
				}
			}
		}
		
		unset($cart['products'][$cart_id]);
		$cart['recalculate'] = true;
	}

	return true;
}

//
// Checks whether this order used the current payment and calls the payment_cc_complete.php file
//
function fn_check_payment_script($script_name, $order_id, &$processor_data = null)
{
	$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $order_id);
	$processor_data = fn_get_processor_data($payment_id);
	if ($processor_data['processor_script'] == $script_name) {
		return true;
	}
	return false;
}

//
// This function calculates product prices without taxes and with taxes
//
function fn_get_taxed_and_clean_prices(&$product, &$auth)
{
	$tax_value = 0;
	$included_tax = false;

	if (empty($product) || empty($product['product_id']) || empty($product['tax_ids'])) {
		return false;
	}
	if (isset($product['subtotal'])) {
		$tx_price =  $product['subtotal'];
	} elseif (empty($product['price'])) {
		$tx_price = 0;
	} elseif (isset($product['discounted_price'])) {
		$tx_price = $product['discounted_price'];
	} else {
		$tx_price = $product['price'];
	}

	$product_taxes = fn_get_set_taxes($product['tax_ids']);

	$calculated_data = fn_calculate_tax_rates($product_taxes, $tx_price, 1, $auth, $_SESSION['cart']);
	// Apply taxes to product subtotal
	if (!empty($calculated_data)) {
		foreach ($calculated_data as $_k => $v) {
			$tax_value += $v['tax_subtotal'];
			if ($v['price_includes_tax'] != 'Y') {
				$included_tax = true;
				$tx_price += $v['tax_subtotal'];
			}
		}
	}

	$product['clean_price'] = $tx_price - $tax_value;
	$product['taxed_price'] = $tx_price;
	$product['taxes'] = $calculated_data;
	$product['included_tax'] = $included_tax;

	return true;
}

function fn_clear_cart(&$cart, $complete = false, $clear_all = false)
{
	fn_set_hook('clear_cart', $cart, $complete, $clear_all);
	
	// Decrease products popularity
	if (!empty($cart['products'])) {
		$pids = array();
		
		foreach ($cart['products'] as $product) {
			$pids[] = $product['product_id'];
			unset($_SESSION['products_popularity']['added'][$product['product_id']]);
		}
		
		db_query("UPDATE ?:product_popularity SET deleted = deleted + 1, total = total - ?i WHERE product_id IN (?n)", POPULARITY_DELETE_FROM_CART, $pids);
	}
	
	if ($clear_all) {
		$cart = array();
	} else {
		$cart = array (
			'products' => array(),
			'recalculate' => false,
			'user_data' => !empty($cart['user_data']) && $complete == false ? $cart['user_data'] : array(),
		);
	}
	
	return true;
}

function fn_apply_cart_shipping_rates(&$cart, &$cart_products, &$auth, &$shipping_rates)
{
	$cart['shipping_failed'] = true;

	if (!fn_is_empty($shipping_rates)) {

		// Delete all free shippings
		foreach ($shipping_rates as $k => $v) {
			if (!empty($v['free_shipping']) && !in_array($k, $cart['free_shipping'])) {
				if (!empty($v['original_rates'])) {
					$shipping_rates[$k]['rates'] = $v['original_rates'];
					unset($shipping_rates[$k]['free_shipping']);
				} else {
					unset($shipping_rates[$k]);
				}
			}
		}

		// Set free shipping rates
		if (!empty($cart['free_shipping'])) {
			foreach ($cart['free_shipping'] as $sh_id) {
				if (isset($shipping_rates[$sh_id])) {
					if (empty($shipping_rates[$sh_id]['added_manually'])) {
						if (empty($shipping_rates[$sh_id]['original_rates'])) { // save original rates
							$shipping_rates[$sh_id]['original_rates'] = $shipping_rates[$sh_id]['rates'];
						}
						foreach ($shipping_rates[$sh_id]['rates'] as $_k => $_v) { // null rates
							$shipping_rates[$sh_id]['rates'][$_k] = 0;
						}
					}
				} else {
					$name = db_get_row("SELECT b.shipping as name, b.delivery_time FROM ?:shippings as a LEFT JOIN ?:shipping_descriptions as b ON a.shipping_id = b.shipping_id AND b.lang_code = ?s WHERE a.shipping_id = ?i AND a.status = 'A'", CART_LANGUAGE, $sh_id);
                    if (!empty($name)) {
						$shipping_rates[$sh_id] = $name;
						$shipping_rates[$sh_id]['rates'] = array(0);
						$shipping_rates[$sh_id]['added_manually'] = true;
					}
				}

				if (isset($shipping_rates[$sh_id])) {
					$shipping_rates[$sh_id]['free_shipping'] = true;
				}
			}
		}
		// Delete not existent rates
		if (!empty($cart['shipping'])) {
			foreach ($cart['shipping'] as $sh_id => $v) {
				foreach ($v['rates'] as $o_id => $r) {
                    //03.11.2014 -- added && isset($r) because manual rate ($o_id = '') is being ignored and whole shipping is deleted
					if (!isset($shipping_rates[$sh_id]['rates'][$o_id]) && !isset($r) && empty($shipping_rates[$sh_id]['added_manually'])) {
						unset($cart['shipping'][$sh_id]);
					}
				}
			}
		}

		if (fn_check_suppliers_functionality()) {
			fn_companies_apply_cart_shipping_rates($cart, $cart_products, $auth, $shipping_rates);
		} else {
			if (isset($cart['shipping']) && false != reset($cart['shipping']) && isset($shipping_rates[key($cart['shipping'])])) {
				$k = key($cart['shipping']);
				$first_method = $shipping_rates[$k];

				// enables to select the last chosen shipping method on checkout
			} elseif (isset($cart['chosen_shipping']) && false != reset($cart['chosen_shipping']) && isset($shipping_rates[key($cart['chosen_shipping'])])) {
				$cart['shipping'] = $cart['chosen_shipping'];
				$k = key($cart['chosen_shipping']);
				$first_method = $shipping_rates[$k];
			} else {
				$cart['shipping'] = array();
				$first_method = reset($shipping_rates);
				$k = key($shipping_rates);
			}

			$cart['shipping_cost'] = reset($first_method['rates']);
			$cart['shipping'] = fn_array_merge(isset($cart['shipping']) ? $cart['shipping'] : array(), array(
				$k => array(
					'shipping' => $first_method['name'],
					'rates' => $first_method['rates']
				)
			));
		}

		if (!empty($cart['shipping'])) {
			$cart['shipping_failed'] = false;
		}

		fn_set_hook('apply_cart_shipping_rates', $cart, $cart_products, $auth, $shipping_rates);
	}
}

function fn_external_discounts($product)
{
	$discounts = 0;

	fn_set_hook('get_external_discounts', $product, $discounts);

	return $discounts;
}

// FIX-EVENT - must be revbuilt to check edp, free, etc
function fn_exclude_from_shipping_calculate($product)
{
	$exclude = false;

	fn_set_hook('exclude_from_shipping_calculation', $product, $exclude);

	return $exclude;
}
//
// This function is used to find out the total shipping cost. Used in payments, quickbooks
//

function fn_order_shipping_cost($order_info)
{
	$cost = (floatval($order_info['shipping_cost'])) ? $order_info['shipping_cost'] : 0;

	if (floatval($order_info['shipping_cost'])) {
		foreach($order_info['taxes'] as $tax) {
			if ($tax['price_includes_tax'] == 'N') {
				foreach ($tax['applies'] as $_id => $value) {
					if (strpos($_id, 'S_') !== false) {
						$cost += $value;
					}
				}
			}
		}
	}

	return $cost ? fn_format_price($cost) : 0;
}

//
// Cleanup payment information
//
function fn_cleanup_payment_info($order_id, $payment_info, $silent = false)
{

	if ($silent == false) {
		$processing_msg = fn_get_lang_var('processing_order');
		$done_msg = fn_get_lang_var('uc_ok');
		echo $processing_msg . '&nbsp;<b>#'.$order_id.'</b>...';
		fn_flush();
	}

	if (!is_array($payment_info)) {
		$info = @unserialize(fn_decrypt_text($payment_info));
	} else {
		$info = $payment_info;
	}

	if (!empty($info['cvv2'])) {
		$info['cvv2'] = 'XXX';
	}
	if (!empty($info['card_number'])) {
		$info['card_number'] = substr_replace($info['card_number'], str_repeat('X', strlen($info['card_number']) - 4), 0, strlen($info['card_number']) - 4);
	}

	foreach (array('start_month', 'start_year', 'expiry_month', 'expiry_year') as $v) {
		if (!empty($info[$v])) {
			$info[$v] = 'XX';
		}
	}

	$_data = fn_encrypt_text(serialize($info));
	db_query("UPDATE ?:order_data SET data = ?s WHERE order_id = ?i AND type = 'P'", $_data, $order_id);

	if ($silent == false) {
		echo $done_msg .'<br />';
	}
}

//
// Checks if order can be placed
//
function fn_allow_place_order(&$cart)
{
	$total = Registry::get('settings.General.min_order_amount_type') == "S" ? $cart['total'] : $cart['subtotal'];

	fn_set_hook('allow_place_order', $total, $cart);

	$cart['amount_failed'] = (Registry::get('settings.General.min_order_amount') > $total && floatval($total));

	if (!empty($cart['amount_failed']) || !empty($cart['shipping_failed'])) {
		return false;
	}

	return true;
}

/**
 * Returns orders
 *
 * @param array $params array with search params
 * @param int $items_per_page
 * @param bool $get_totals
 * @param string $lang_code
 * @return array
 */

function fn_get_orders($params, $items_per_page = 0, $get_totals = false, $lang_code = CART_LANGUAGE, $f_notJoin = 0)
{
	// Init filter
	$params = fn_init_view('orders', $params);

	// Set default values to input params
	$params['page'] = empty($params['page']) ? 1 : $params['page']; // default page is 1

	$params['include_incompleted'] = empty($params['include_incompleted']) ? false : $params['include_incompleted']; // default incomplited orders should not be displayed
	if (!empty($params['status']) && (is_array($params['status']) && in_array(STATUS_INCOMPLETED_ORDER, $params['status']) || !is_array($params['status']) && $params['status'] == STATUS_INCOMPLETED_ORDER)) {
		$params['include_incompleted'] = true;
	}

	// Define fields that should be retrieved
	$fields = array (
		"distinct ?:orders.order_id",
		"?:orders.user_id",
		"?:orders.is_parent_order",
		"?:orders.parent_order_id",
		"?:orders.company_id",
		"?:orders.timestamp",
		"?:orders.date_to_delivery",
		"?:orders.firstname",
		"?:orders.lastname",
		"?:orders.email",
		"?:orders.status",
		"?:orders.total",
		"?:payment_descriptions.payment",
		//"invoice_docs.doc_id as invoice_id",
		//"memo_docs.doc_id as credit_memo_id"
	);
	if($f_notJoin == 0){
		$fields[] = 'invoice_docs.doc_id as invoice_id';
		$fields[] = 'memo_docs.doc_id as credit_memo_id';
	}
	
	// Define sort fields
	$sortings = array (
		'order_id' => "?:orders.order_id",
		'status' => "?:orders.status",
		'customer' => array("?:orders.lastname", "?:orders.firstname"),
		'email' => "?:orders.email",
		'date' => "?:orders.timestamp",
		'deliv' => "?:orders.date_to_delivery",
		'total' => "?:orders.total",
		'payment' => "?:orders.payment_id"
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		$params['sort_order'] = 'desc';
	}

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = 'date';
	}

	$sorting = (is_array($sortings[$params['sort_by']]) ? implode(' ' . $directions[$params['sort_order']]. ', ', $sortings[$params['sort_by']]): $sortings[$params['sort_by']]). " " .$directions[$params['sort_order']];

	// Reverse sorting (for usage in view)
	$params['sort_order'] = $params['sort_order'] == 'asc' ? 'desc' : 'asc';

	$condition = $join = $group = '';
	
	$condition .= " AND ?:orders.is_parent_order != 'Y' ";
	$condition .= fn_get_company_condition('?:orders.company_id');

	if (isset($params['cname']) && fn_string_no_empty($params['cname'])) {
		$arr = fn_explode(' ', $params['cname']);
		foreach ($arr as $k => $v) {
			if (!fn_string_no_empty($v)) {
				unset($arr[$k]);
			}
		}
		if (sizeof($arr) == 2) {
			$condition .= db_quote(" AND ?:orders.firstname LIKE ?l AND ?:orders.lastname LIKE ?l", "%".array_shift($arr)."%", "%".array_shift($arr)."%");
		} else {
			$condition .= db_quote(" AND (?:orders.firstname LIKE ?l OR ?:orders.lastname LIKE ?l)", "%".trim($params['cname'])."%", "%".trim($params['cname'])."%");
		}
	}

	if (!empty($params['tax_exempt'])) {
		$condition .= db_quote(" AND ?:orders.tax_exempt = ?s", $params['tax_exempt']);
	}

	if (isset($params['email']) && fn_string_no_empty($params['email'])) {
		$condition .= db_quote(" AND ?:orders.email LIKE ?l", "%".trim($params['email'])."%");
	}

	if (!empty($params['user_id'])){		
		$condition .= db_quote(' AND ?:orders.user_id IN (?n)', $params['user_id']);
	}

	if (isset($params['total_from']) && fn_is_numeric($params['total_from'])) {
		$condition .= db_quote(" AND ?:orders.total >= ?d", fn_convert_price($params['total_from']));
	}

	if (!empty($params['total_to']) && fn_is_numeric($params['total_to'])) {
		$condition .= db_quote(" AND ?:orders.total <= ?d", fn_convert_price($params['total_to']));
	}

	if (!empty($params['status'])) {
		$condition .= db_quote(' AND ?:orders.status IN (?a)', $params['status']);
	}

	if (empty($params['include_incompleted'])) {
		$condition .= db_quote(' AND ?:orders.status != ?s', STATUS_INCOMPLETED_ORDER);
	}

	if (!empty($params['order_id'])) {
		$condition .= db_quote(' AND ?:orders.order_id IN (?n)', (!is_array($params['order_id']) && (strpos($params['order_id'], ',') !== false) ? explode(',', $params['order_id']) : $params['order_id']));
	}

	if (!empty($params['p_ids']) || !empty($params['product_view_id'])) {
		$arr = (strpos($params['p_ids'], ',') !== false || !is_array($params['p_ids'])) ? explode(',', $params['p_ids']) : $params['p_ids'];

		if (empty($params['product_view_id'])) {
			$condition .= db_quote(" AND ?:order_details.product_id IN (?n)", $arr);
		} else {
			$condition .= db_quote(" AND ?:order_details.product_id IN (?n)", db_get_fields(fn_get_products(array('view_id' => $params['product_view_id'], 'get_query' => true))));
		}

		$join .= " LEFT JOIN ?:order_details ON ?:order_details.order_id = ?:orders.order_id";
		$group .=  " GROUP BY ?:orders.order_id ";
	}

	$join .= " LEFT JOIN ?:payment_descriptions ON ?:payment_descriptions.payment_id = ?:orders.payment_id";	
	
	if (!empty($params['admin_user_id'])) {
		$condition .= db_quote(" AND ?:new_orders.user_id = ?i", $params['admin_user_id']);
		$join .= " LEFT JOIN ?:new_orders ON ?:new_orders.order_id = ?:orders.order_id";
	}
	
	//$join .= " LEFT JOIN ?:profile_fields_data ON orders.user_id = profile_fields_data.object_id";	
	
	$docs_conditions = array();
	if (!empty($params['invoice_id']) || !empty($params['has_invoice'])) {
		if (!empty($params['has_invoice'])) {
			$docs_conditions[] = "invoice_docs.doc_id IS NOT NULL";
		} elseif (!empty($params['invoice_id'])) {
			$docs_conditions[] = db_quote("invoice_docs.doc_id = ?i", $params['invoice_id']);
		}
	}
	if($f_notJoin == 0)	
		$join .= " LEFT JOIN ?:order_docs as invoice_docs ON invoice_docs.order_id = ?:orders.order_id AND invoice_docs.type = 'I'";

	if (!empty($params['credit_memo_id']) || !empty($params['has_credit_memo'])) {
		if (!empty($params['has_credit_memo'])) {
			$docs_conditions[] = "memo_docs.doc_id IS NOT NULL";
		} elseif (!empty($params['credit_memo_id'])) {
			$docs_conditions[] = db_quote("memo_docs.doc_id = ?i", $params['credit_memo_id']);
		}
	}
	if($f_notJoin == 0)	
		$join .= " LEFT JOIN ?:order_docs as memo_docs ON memo_docs.order_id = ?:orders.order_id AND memo_docs.type = 'C'";

	if (!empty($docs_conditions)) {
		$condition .= " AND (" . implode(' OR ', $docs_conditions) . ")";
	}

	if (!empty($params['shippings'])) {
		$set_conditions = array();
		foreach ($params['shippings'] as $v) {
			$set_conditions[] = db_quote("FIND_IN_SET(?s, ?:orders.shipping_ids)", $v);
		}
		$condition .= " AND (" . implode(' OR ', $set_conditions) . ")";
	}

	if (!empty($params['period']) && $params['period'] != 'A') {
		list($params['time_from'], $params['time_to']) = fn_create_periods($params);

		$condition .= db_quote(" AND (?:orders.timestamp >= ?i AND ?:orders.timestamp <= ?i)", $params['time_from'], $params['time_to']);
	}
	
	if (!empty($params['custom_files']) && $params['custom_files'] == 'Y') {
		$condition .= db_quote(" AND ?:order_details.extra LIKE ?l", "%custom_files%");
		
		if (empty($params['p_ids']) && empty($params['product_view_id'])) {
			$join .= " LEFT JOIN ?:order_details ON ?:order_details.order_id = ?:orders.order_id";
		}
	}
	
	//$condition .= db_quote(" AND profile_fields_data.field_id = 44");
	fn_set_hook('get_orders', $params, $fields, $sortings, $condition, $join);

	$limit = '';
	if (!empty($items_per_page)) {
		$total = db_get_field("SELECT COUNT(DISTINCT (?:orders.order_id)) FROM ?:orders $join WHERE 1 $condition");
		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}
	
	$ts = 'SELECT ' . implode(', ', $fields) . " FROM ?:orders $join WHERE 1 $condition $group ORDER BY $sorting $limit";
	//var_dump('SELECT ' . implode(", ", $fields) . ' FROM ?:orders '.$join.' WHERE 1 '.$condition.' '.$group.' ORDER BY '.$sorting.' '.$limit.'');
	
	$orders = db_get_array('SELECT ' . implode(', ', $fields) . " FROM ?:orders $join WHERE 1 $condition $group ORDER BY $sorting $limit");
	
	if ($get_totals == true) {
		$totals = array (
			'gross_total' => db_get_field("SELECT sum(t.total) FROM ( SELECT total FROM ?:orders $join WHERE 1 $condition $group) as t"),
			'totally_paid' => db_get_field("SELECT sum(t.total) FROM ( SELECT total FROM ?:orders $join WHERE status IN ('P', 'C') $condition $group) as t"),
		);
	}

	return array($orders, $params, ($get_totals == true ? $totals : array()));
}

/**
 * Calculate shipping rates using real-time shipping processors
 *
 * @param int $service_id shipping service ID
 * @param array $location customer location
 * @param array $package_info package information (weight, subtotal, qty)
 * @param array $auth customer session information
 * @param array $substitution_settings settings what can replace default shipping origination
 * @return mixed array with rates if calculated, false otherwise
 */
function fn_calculate_realtime_shipping_rate($service_id, $location, $package_info, &$auth)
{
	static $shipping_settings = array();
	static $included_modules = array();

	if (!class_exists('XMLDocument')) {
		include(DIR_LIB . 'xmldocument/xmldocument.php');
	}

	if (empty($shipping_settings)) {
		$shipping_settings = fn_get_settings('Shippings');
	}

	$code = db_get_row("SELECT intershipper_code, code, module FROM ?:shipping_services WHERE service_id = ?i AND status = 'A'", $service_id);

	if (empty($code)) {
		return false;
	}

	$weight = fn_expand_weight($package_info['W']);

	if (!empty($code['intershipper_code']) && $shipping_settings['intershipper_enabled'] == 'Y') { // FIXME - hardcoded intershipper
		$code['module'] = 'intershipper';
		$code['code'] = $code['intershipper_code'];
	}

	if (empty($included_modules[$code['module']])) {
		include(DIR_SHIPPING_FILES . $code['module'] . '.php');
		$included_modules[$code['module']] = true;
	}

	$func = 'fn_get_' . $code['module'] . '_rates';

	return $func($code['code'], $weight, $location, $auth, $shipping_settings, $package_info, $package_info['origination'], $service_id);
}

/**
 * Convert weight to pounds/ounces
 *
 * @param float $weight weight
 * @return array converted data
 */
function fn_expand_weight($weight)
{
	$full_ounces = ceil(round($weight * Registry::get('settings.General.weight_symbol_grams') / 28.35, 3));
	$full_pounds = sprintf("%.1f", $full_ounces/16);
	$pounds = floor($full_ounces/16);
	$ounces = $full_ounces - $pounds * 16;

	return array (
		'full_ounces' => $full_ounces,
		'full_pounds' => $full_pounds,
		'pounds' => $pounds,
		'ounces' => $ounces,
		'plain' => $weight,
	);
}

/**
 * Generate unique ID to cache rates calculation results
 *
 * @param mixed parameters to generate unique ID from
 * @return mixed array with rates if calculated, false otherwise
 */
function fn_generate_cached_rate_id()
{
	$data = array();
	$num_args = func_num_args();
	for($i = 0; $i < $num_args; $i++){
		$variable = func_get_arg($i);
		if (is_array($variable)){
			$data = fn_array_merge($data, $variable, false);
		} else {
			$data[] = $variable;
		}
	}

	natsort($data);

	return fn_crc32(implode('_', $data));
}

/**
 * Send order notification
 *
 * @param array $order_info order information
 * @param array $edp_data information about downloadable products
 * @param mixed $force_notification user notification flag (true/false), if not set, will be retrieved from status parameters
 * @return array structured data
 */
function fn_order_notification(&$order_info, $edp_data = array(), $force_notification = array())
{
	static $notified = array();

	if ((!empty($notified[$order_info['order_id']][$order_info['status']]) && $notified[$order_info['order_id']][$order_info['status']]) || $order_info['status'] == STATUS_INCOMPLETED_ORDER || $order_info['status'] == STATUS_PARENT_ORDER) {
		return true;
	}
	
	if (!is_array($force_notification)) {
		$force_notification = fn_get_notification_rules($force_notification, !$force_notification);	
	}

	$order_statuses = fn_get_statuses(STATUSES_ORDER, false, true);
	$status_params = $order_statuses[$order_info['status']];

	$notify_user = isset($force_notification['C']) ? $force_notification['C'] : (!empty($status_params['notify']) && $status_params['notify'] == 'Y' ? true : false);
	$notify_department = isset($force_notification['A']) ? $force_notification['A'] : (!empty($status_params['notify_department']) && $status_params['notify_department'] == 'Y' ? true : false);

	if ($notify_user == true || $notify_department == true) {
		$notified[$order_info['order_id']][$order_info['status']] = true;
		Registry::get('view_mail')->assign('order_info', $order_info);
		Registry::get('view_mail')->assign('order_status', fn_get_status_data($order_info['status'], STATUSES_ORDER, $order_info['order_id'], $order_info['lang_code']));
		Registry::get('view_mail')->assign('status_settings', $order_statuses[$order_info['status']]);
		$companies = &Registry::get('s_companies');
		Registry::get('view_mail')->assign('companies', $companies);
		
		// restore secondary currency
		if (!empty($order_info['secondary_currency']) && Registry::get("currencies.{$order_info['secondary_currency']}")) {
			Registry::get('view_mail')->assign('secondary_currency', $order_info['secondary_currency']);
		}

		$company_id = $order_info['company_id'];
		$old_mail_manifest = Registry::get('view_mail')->get_var('manifest');

		// Notify customer
		if ($notify_user == true) {
			$manifest = fn_get_manifest('customer', $order_info['lang_code'], $company_id);
			Registry::get('view_mail')->assign('manifest', $manifest);
			
			fn_send_mail($order_info['email'], Registry::get('settings.Company.company_newsletter_email'), 'orders/order_notification_subj.tpl', 'orders/order_notification.tpl', '', $order_info['lang_code']);
	//		fn_send_mail('diedsmiling@gmail.com', Registry::get('settings.Company.company_orders_department'), 'orders/order_notification_subj.tpl', 'orders/order_notification.tpl', '', $order_info['lang_code']);
		}

		if ($notify_department == true) {
			// Translate descriptions to admin language
			fn_translate_products($order_info['items'], 'product', Registry::get('settings.Appearance.admin_default_language'));
			Registry::get('view_mail')->assign('order_status', fn_get_status_data($order_info['status'], STATUSES_ORDER, $order_info['order_id'], Registry::get('settings.Appearance.admin_default_language')));

			$company_email = !empty($companies[$company_id]['email']) ? $companies[$company_id]['email'] : Registry::get('settings.Company.company_orders_department');

			$lang_code = Registry::get('settings.Appearance.admin_default_language');
			$manifest = fn_get_manifest('customer', $lang_code, $company_id);
			Registry::get('view_mail')->assign('manifest', $manifest);
			
			fn_send_mail($company_email, Registry::get('settings.Company.company_orders_department'), 'orders/order_notification_subj.tpl', 'orders/order_notification.tpl', '', $lang_code, $order_info['email']);
			if (Registry::get('settings.Suppliers.notify_order_department') == 'Y') {
				Registry::get('view_mail')->assign('manifest', $old_mail_manifest);
				fn_send_mail(Registry::get('settings.Company.company_orders_department'), Registry::get('settings.Company.company_orders_department'), 'orders/order_notification_subj.tpl', 'orders/order_notification.tpl', '', $lang_code, $order_info['email']);
			}
		}

		Registry::get('view_mail')->assign('manifest', $old_mail_manifest);

		if (!empty($edp_data) && $notify_user == true) {
			Registry::get('view_mail')->assign('edp_data', $edp_data);
			fn_send_mail($order_info['email'], Registry::get('settings.Company.company_orders_department'), 'orders/edp_access_subj.tpl', 'orders/edp_access.tpl', '', $order_info['lang_code']);
		}
	}
	if (!empty($edp_data) && !$notify_user) {
		// Send out download links for EDP with "Immediately" Activation mode

		// TRUE if the EDP download links e-mail has already been sent. Used to avoid sending duplicate e-mails.
		$download_email_sent = false;
		foreach ($edp_data as $edp_item) {
			foreach ($edp_item['files'] as $file) {
				if (!empty($file['activation']) && $file['activation'] == 'I' && !$download_email_sent) {
					Registry::get('view_mail')->assign('edp_data', $edp_data);
					Registry::get('view_mail')->assign('order_info', $order_info);
					fn_send_mail($order_info['email'], Registry::get('settings.Company.company_orders_department'), 'orders/edp_access_subj.tpl', 'orders/edp_access.tpl', '', $order_info['lang_code']);
					$download_email_sent = true;
					break;
				}
			}
		}
	}


	if (PRODUCT_TYPE == 'PROFESSIONAL') {
		fn_companies_suppliers_order_notification($order_info, $order_statuses, $force_notification);
	}

	fn_set_hook('order_notification', $order_info, $order_statuses, $force_notification);
}

function fn_prepare_package_info(&$cart, &$cart_products)
{
	$package_infos = array();
	
	$groupped_products = array();

	foreach ($cart['products'] as $k => $v) {
		if (!isset($v['company_id'])) {
			// for old saved carts
			$cart['products'][$k]['company_id'] = $v['company_id'] = $cart_products[$k]['company_id'] = 0;
		}
		$groupped_products[$v['company_id']][$k] = $cart_products[$k];
	}
	
	foreach ($groupped_products as $_cid => $products) {
		$package_infos[$_cid]['C'] = fn_get_products_cost($cart, $products);
		$package_infos[$_cid]['W'] = fn_get_products_weight($cart, $products);
		$package_infos[$_cid]['I'] = fn_get_products_amount($cart, $products);

		if (empty($package_infos[$_cid]['origination'])) {
			if (!$_cid) {
				$package_infos[$_cid]['origination'] = array(
					'name' => Registry::get('settings.Company.company_name'),
					'address' => Registry::get('settings.Company.company_address'),
					'city' => Registry::get('settings.Company.company_city'),
					'country' => Registry::get('settings.Company.company_country'),
					'state' => Registry::get('settings.Company.company_state'),
					'zipcode' => Registry::get('settings.Company.company_zipcode'),
					'phone' => Registry::get('settings.Company.company_phone'),
					'fax' => Registry::get('settings.Company.company_fax'),
				);
			} else {
				$supplier_data = fn_get_company_data($_cid);
				$package_infos[$_cid]['origination'] = array(
					'name' => !empty($supplier_data['company']) ? $supplier_data['company'] : '',
					'phone' => !empty($supplier_data['phone']) ? $supplier_data['phone'] : '',
					'fax' => !empty($supplier_data['fax']) ? $supplier_data['fax'] : '',
					'country' => !empty($supplier_data['country']) ? $supplier_data['country'] : '',
					'state' => !empty($supplier_data['state']) ? $supplier_data['state'] : '',
					'zipcode' => !empty($supplier_data['zipcode']) ? $supplier_data['zipcode'] : '',
					'city' => !empty($supplier_data['city']) ? $supplier_data['city'] : '',
					'address' => !empty($supplier_data['address']) ? $supplier_data['address'] : '',
				);
			}
		}
	}	
	
	fn_set_hook('prepare_package_info', $cart, $cart_products, $package_infos);

	return $package_infos;
}

/**
 *
 * @param int $payment_id payment ID
 * @param string $action action 
  * @return array (boolean, string) 
 */
function fn_check_processor_script($payment_id, $action, $additional_params = false)
{
	
	if ($additional_params) {
		if ($action == 'save' || (!empty($_REQUEST['skip_payment']) && AREA == 'C')){
			return array(false, '');
		}
	}	
	
	$payment = fn_get_payment_method_data((int)$payment_id);

	if (!empty($payment['processor_id'])) {
		$processor_data = fn_get_processor_data($payment['payment_id']);
		if (!empty($processor_data['processor_script']) && file_exists(DIR_PAYMENT_FILES . $processor_data['processor_script'])) {
			return array(true, $processor_data);
		}
	}

	return array(false, '');
}

function fn_add_product_options_files($product_data, &$cart, &$auth, $update = false, $location = 'cart')
{
	// Check if products have cusom images
	if (!$update) {
		$uploaded_data = fn_filter_uploaded_data('product_data');
	} else {
		$uploaded_data = fn_filter_uploaded_data('cart_products');
	}
	
	$dir_path = DIR_CUSTOM_FILES . 'sess_data';
	
	// Check for the already uploaded files
	if (!empty($product_data['custom_files']['uploaded'])) {
		foreach ($product_data['custom_files']['uploaded'] as $file_id => $file_data) {
			if (file_exists($dir_path . '/' . basename($file_data['path']))) {
				$id = $file_data['product_id'] . $file_data['option_id'] . $file_id;
				$uploaded_data[$id] = array(
					'name' => $file_data['name'],
					'path' => $dir_path . '/' . basename($file_data['path']),
				);
				
				$product_data['custom_files'][$id] = $file_data['product_id'] . '_' . $file_data['option_id'];
			}
		}
	}
	
	if (!empty($uploaded_data) && !empty($product_data['custom_files'])) {
		$files_data = array();
		
		foreach ($uploaded_data as $key => $file) {
			$file_info = pathinfo($file['name']);
			$file['extension'] = empty($file_info['extension']) ? '' : $file_info['extension'];
			
			$file_info = getimagesize($file['path']);
			$file['type'] = $file_info['mime'];
			$file['is_image'] = fn_get_image_extension($file_info['mime']);
			
			$_data = explode('_', $product_data['custom_files'][$key]);
			$product_id = empty($_data[0]) ? 0 : $_data[0];
			$option_id = empty($_data[1]) ? 0 : $_data[1];
			$file_id = str_replace($option_id . $product_id, '', $key);
			
			if (empty($file_id)) {
				$files_data[$product_id][$option_id][] = $file;
			} else {
				$files_data[$product_id][$option_id][$file_id] = $file;
			}
		}
		
		if (!is_dir($dir_path)) {
			if (!fn_mkdir($dir_path)) {
				// Unable to create a directory
				fn_set_notification('E', fn_get_lang_var('error'), str_replace('[directory]', DIR_CUSTOM_FILES, fn_get_lang_var('text_cannot_write_directory')));
			}
		}
	}

	unset($product_data['custom_files']);

	foreach ($product_data as $key => $data) {
		$product_id = (!empty($data['product_id'])) ? $data['product_id'] : $key;
		
		// Check if product has cusom images
		if ($update || isset($files_data[$key])) {
			$hash = $key;
		} else {
			$hash = $product_id;
		}
		
		if (!empty($files_data[$hash]) && is_array($files_data[$hash])) {
			$_options = fn_get_product_options($product_id);
			
			foreach ($files_data[$hash] as $option_id => $files) {
				foreach ($files as $file_id => $file) {
					// Check for the allowed extensions
					if (!empty($_options[$option_id]['allowed_extensions'])) {
						if ((empty($file['extension']) && !empty($_options[$option_id]['allowed_extensions'])) || !preg_match("/\b" . $file['extension'] . "\b/i", $_options[$option_id]['allowed_extensions'])) {
							$message = fn_get_lang_var('text_forbidden_uploaded_file_extension');
							$message = str_replace('[ext]', $file['extension'], $message);
							$message = str_replace('[exts]', $_options[$option_id]['allowed_extensions'], $message);
							
							fn_set_notification('E', fn_get_lang_var('error'), $file['name'] . ': ' . $message);
							unset($files_data[$hash][$option_id][$file_id]);
							continue;
						}
					}
					
					// Check for the max file size
					
					if (!empty($_options[$option_id]['max_file_size'])) {
						if (empty($file['size'])) {
							$file['size'] = filesize($file['path']);
						}
						
						if ($file['size'] > $_options[$option_id]['max_file_size'] * 1024) {
							fn_set_notification('E', fn_get_lang_var('error'), str_replace('[size]', $_options[$option_id]['max_file_size'] . ' kb', $file['name'] . ': ' . fn_get_lang_var('text_forbidden_uploaded_file_size')));
							unset($files_data[$hash][$option_id][$file_id]);
							continue;
						}
					}
					
					$_file_path = tempnam($dir_path, 'file_');
					if (!fn_copy($file['path'], $_file_path)) {
						fn_set_notification('E', fn_get_lang_var('error'), str_replace('[file]', $file['name'], fn_get_lang_var('text_cannot_create_file')));
						
						unset($files_data[$hash][$option_id][$file_id]);
						continue;
					}
					
					$file['path'] = $_file_path;
					$file['file'] = basename($file['path']);
					
					if ($file['is_image']) {
						$file['thumbnail'] = 'image.custom_image&image=' . $file['file'] . '&type=T';
						$file['detailed'] = 'image.custom_image&image=' . $file['file'] . '&type=D';
					}
					
					$file['location'] = $location;
					
					if ($update) {
						$cart['products'][$key]['extra']['custom_files'][$option_id][] = $file;
					} else {
						$data['extra']['custom_files'][$option_id][] = $file;
						
					}
				}
				
				if ($update) {
					if (!empty($cart['products'][$key]['product_options'][$option_id])) {
						$cart['products'][$key]['product_options'][$option_id] = md5(serialize($cart['products'][$key]['extra']['custom_files'][$option_id]));
					}
				} else {
					if (!empty($data['extra']['custom_files'][$option_id])) {
						$data['product_options'][$option_id] = md5(serialize($data['extra']['custom_files'][$option_id]));
					}
				}
			}
			
			// Check the required options
			if (empty($data['extra']['parent'])) {
				foreach ($_options as $option) {
					if ($option['option_type'] == 'F' && $option['required'] == 'Y' && !$update) {
						if (empty($data['product_options'][$option['option_id']])) {
							fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('product_cannot_be_added'));
							
							unset($product_data[$key]);
							return array($product_data, $cart);
						}
					}
				}
			}
			
		} else {
			if (empty($data['extra']['parent'])) {
				$_options = fn_get_product_options($product_id);
				
				foreach ($_options as $option) {
					if ($option['option_type'] == 'F' && $option['required'] == 'Y' && empty($cart['products'][$hash]['extra']['custom_files'][$option['option_id']]) && empty($data['extra']['custom_files'][$option['option_id']])) {
						fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('product_cannot_be_added'));
						
						unset($product_data[$key]);
						return array($product_data, $cart);
					}
				}
			}
		}
		
		if (isset($cart['products'][$key]['extra']['custom_files'])) {
			foreach ($cart['products'][$key]['extra']['custom_files'] as $option_id => $files) {
				foreach ($files as $file) {
					$data['extra']['custom_files'][$option_id][] = $file;
				}
				
				$data['product_options'][$option_id] = md5(serialize($files));
			}
		}
		
		$product_data[$key] = $data;
	}
	
	return array($product_data, $cart);
}

/**
 *   save stored taxes for products
 * @param array $cart cart
 * @param int $update_id   key of $cart['products'] to be updated
 * @param int $new_id  new key
 * @param bool $consider_existing  whether consider or not existing key
 */
function fn_update_stored_cart_taxes(&$cart, $update_id, $new_id, $consider_existing = false)
{
	if (!empty($cart['taxes']) && is_array($cart['taxes'])) {
		foreach ($cart['taxes'] as $t_id => $s_tax) {
			if (!empty($s_tax['applies']) && is_array($s_tax['applies'])) {
				$compare_key = 'P_' . $update_id;
				$new_key = 'P_' . $new_id;
				if (array_key_exists($compare_key, $s_tax['applies'])) {
					$cart['taxes'][$t_id]['applies'][$new_key] = (isset($s_tax['applies'][$new_key]) && $consider_existing ? $s_tax['applies'][$new_key] : 0) + $s_tax['applies'][$compare_key];
					unset($cart['taxes'][$t_id]['applies'][$compare_key]);
				}
			}
		}
	}
}

function fn_define_original_amount($product_id, $cart_id, &$product, $prev_product)
{
	if (!empty($prev_product['original_product_data']) && !empty($prev_product['original_product_data']['amount'])) {
		$tracking = db_get_field("SELECT tracking FROM ?:products WHERE product_id = ?i", $product_id);
		if ($tracking != 'O' || $tracking == 'O' && $prev_product['original_product_data']['cart_id'] == $cart_id) {
			$product['original_amount'] = $prev_product['original_product_data']['amount'];
		}
		$product['original_product_data'] = $prev_product['original_product_data'];
	} elseif (!empty($prev_product['original_amount'])) {
		$product['original_amount'] = $prev_product['original_amount'];
	}
}

function fn_get_shipments_info($params, $items_per_page = SHIPMENTS_PER_PAGE)
{
	// Init view params
	$params = fn_init_view('shipments', $params);
	
	// Set default values to input params
	$params['page'] = empty($params['page']) ? 1 : $params['page']; // default page is 1
	
	$fields_list = array(
		'?:shipments.shipment_id',
		'?:shipments.timestamp AS shipment_timestamp',
		'?:shipments.comments',
		'?:shipment_items.order_id',
		'?:orders.timestamp AS order_timestamp',
		'?:orders.s_firstname',
		'?:orders.s_lastname',
	);
	
	$joins = array(
		'LEFT JOIN ?:shipment_items ON (?:shipments.shipment_id = ?:shipment_items.shipment_id)',
		'LEFT JOIN ?:orders ON (?:shipment_items.order_id = ?:orders.order_id)',
	);
	
	$group = array(
		'?:shipments.shipment_id',
	);
	
	// Define sort fields
	$sortings = array (
		'id' => "?:shipments.shipment_id",
		'order_id' => "?:orders.order_id",
		'shipment_date' => "?:shipments.timestamp",
		'order_date' => "?:orders.timestamp",
		'customer' => array("?:orders.s_lastname", "?:orders.s_firstname"),
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		$params['sort_order'] = 'desc';
	}

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = 'id';
	}

	$sorting = (is_array($sortings[$params['sort_by']]) ? implode(' ' . $directions[$params['sort_order']] . ', ', $sortings[$params['sort_by']]) : $sortings[$params['sort_by']]) . " " . $directions[$params['sort_order']];

	// Reverse sorting (for usage in view)
	$params['sort_order'] = $params['sort_order'] == 'asc' ? 'desc' : 'asc';
	
	$condition = '';
	
	if (isset($params['advanced_info']) && $params['advanced_info']) {
		$fields_list[] = '?:shipping_descriptions.shipping AS shipping';
		$fields_list[] = '?:shipments.tracking_number';
		$fields_list[] = '?:shipments.carrier';
		
		$joins[] = ' LEFT JOIN ?:shippings ON (?:shipments.shipping_id = ?:shippings.shipping_id)';
		$joins[] = ' LEFT JOIN ?:shipping_descriptions ON (?:shippings.shipping_id = ?:shipping_descriptions.shipping_id)';
		
		$condition .= db_quote(' AND ?:shipping_descriptions.lang_code = ?s', DESCR_SL);
	}
	
	if (!empty($params['order_id'])) {
		$condition .= db_quote(' AND ?:shipment_items.order_id = ?i', $params['order_id']);
	}
	
	if (!empty($params['shipment_id'])) {
		$condition .= db_quote(' AND ?:shipments.shipment_id = ?i', $params['shipment_id']);
	}
	
	if (isset($params['cname']) && fn_string_no_empty($params['cname'])) {
		$arr = fn_explode(' ', $params['cname']);
		foreach ($arr as $k => $v) {
			if (!fn_string_no_empty($v)) {
				unset($arr[$k]);
			}
		}
		if (sizeof($arr) == 2) {
			$condition .= db_quote(" AND ?:orders.firstname LIKE ?l AND ?:orders.lastname LIKE ?l", "%".array_shift($arr)."%", "%".array_shift($arr)."%");
		} else {
			$condition .= db_quote(" AND (?:orders.firstname LIKE ?l OR ?:orders.lastname LIKE ?l)", "%".trim($params['cname'])."%", "%".trim($params['cname'])."%");
		}
	}

	if (!empty($params['p_ids']) || !empty($params['product_view_id'])) {
		$arr = (strpos($params['p_ids'], ',') !== false || !is_array($params['p_ids'])) ? explode(',', $params['p_ids']) : $params['p_ids'];

		if (empty($params['product_view_id'])) {
			$condition .= db_quote(" AND ?:shipment_items.product_id IN (?n)", $arr);
		} else {
			$condition .= db_quote(" AND ?:shipment_items.product_id IN (?n)", db_get_fields(fn_get_products(array('view_id' => $params['product_view_id'], 'get_query' => true)), ','));
		}

		$joins[] = "LEFT JOIN ?:order_details ON ?:order_details.order_id = ?:orders.order_id";
	}
	
	if (!empty($params['shipment_period']) && $params['shipment_period'] != 'A') {
		$params['time_from'] = $params['shipment_time_from'];
		$params['time_to'] = $params['shipment_time_to'];
		$params['period'] = $params['shipment_period'];
		
		list($params['shipment_time_from'], $params['shipment_time_to']) = fn_create_periods($params);

		$condition .= db_quote(" AND (?:shipments.timestamp >= ?i AND ?:shipments.timestamp <= ?i)", $params['shipment_time_from'], $params['shipment_time_to']);
	}
	
	if (!empty($params['order_period']) && $params['order_period'] != 'A') {
		$params['time_from'] = $params['order_time_from'];
		$params['time_to'] = $params['order_time_to'];
		$params['period'] = $params['order_period'];
		
		list($params['order_time_from'], $params['order_time_to']) = fn_create_periods($params);

		$condition .= db_quote(" AND (?:orders.timestamp >= ?i AND ?:orders.timestamp <= ?i)", $params['order_time_from'], $params['order_time_to']);
	}
	
	fn_set_hook('get_shipments', $params, $fields_list, $joins, $condition, $group);
	
	$fields_list = implode(', ', $fields_list);
	$joins = implode(' ', $joins);
	$group = implode(', ', $group);
	
	if (!empty($group)) {
		$group = ' GROUP BY ' . $group;
	}
	
	$limit = '';
	if (!empty($items_per_page)) {
		$total = db_get_field("SELECT COUNT(DISTINCT(?:shipments.shipment_id)) FROM ?:shipments $joins WHERE 1 $condition");
		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}
	
	$shipments = db_get_array("SELECT $fields_list FROM ?:shipments $joins WHERE 1 $condition $group ORDER BY $sorting $limit");
	
	if (isset($params['advanced_info']) && $params['advanced_info'] && !empty($shipments)) {
		foreach ($shipments as $id => $shipment) {
			$items = db_get_array('SELECT item_id, amount FROM ?:shipment_items WHERE shipment_id = ?i', $shipment['shipment_id']);
			if (!empty($items)) {
				foreach ($items as $item) {
					$shipments[$id]['items'][$item['item_id']] = $item['amount'];
				}
			}
		}
	}
	
	return array($shipments, $params, $total);
}

function fn_delete_shipping($shipping_id)
{
	db_query("DELETE FROM ?:shipping_rates WHERE shipping_id = ?i", $shipping_id);
	db_query("DELETE FROM ?:shipping_descriptions WHERE shipping_id = ?i", $shipping_id);
	db_query("DELETE FROM ?:shippings WHERE shipping_id = ?i", $shipping_id);
}
function fn_numsklad($name){
preg_match("/\[(.*?)\]/",$name,$art);

//$art[1]="C296";
$file_handle = fopen("http://korzin.net/temp/1c.txt", "r");
while (!feof($file_handle)) {
   $line = fgets($file_handle);
   $sp=preg_split("/;/",$line);
   if($sp[0]==$art[1]) {
   	fclose($file_handle);
   	return " (На складе ".$sp[2]."шт. - ".$sp[3]."р.)";	
	}
}
fclose($file_handle);
return "";	
}
?>