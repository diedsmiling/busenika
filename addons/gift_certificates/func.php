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
// $Id: func.php 10402 2010-08-12 08:18:09Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_get_gift_certificate_amount_variants()
{
	$variants = array();
	$min = intval(Registry::get('addons.gift_certificates.min_amount'));
	$max = intval(Registry::get('addons.gift_certificates.max_amount'));
	$step = intval(Registry::get('addons.gift_certificates.amount_step'));
	$direction = ($max >= $min) ? 1 : -1;

	if ((!empty($max) || !empty($min)) && $step > 0) {
		for ($i = (int)$min; $i * $direction <= $max * $direction; $i += $step * $direction) {
			$variants[] = $i;
		}
	}

	return $variants;
}

function fn_change_gift_certificate_status($gift_cert_id, $status_to, $status_from = '', $force_notification = array())
{
	if (empty($gift_cert_id)) {
		return false;
	}

	$gift_cert_data = fn_get_gift_certificate_info($gift_cert_id, 'B');

	if (empty($status_from)) {
		$status_from = $gift_cert_data['status'];
	}

	if (empty($status_to) || $status_from == $status_to) {
		return false;
	}

	$result = db_query('UPDATE ?:gift_certificates SET ?u WHERE gift_cert_id = ?i', array('status' => $status_to), $gift_cert_id);

	if ($result) {
		$gift_cert_data['status'] = $status_to;
		fn_gift_certificate_notification($gift_cert_data, $force_notification);
	}

	return $result;
}

function fn_add_gift_certificate_log_record($gift_cert_id, $before_info, $after_info, $order_id = 0)
{
	$auth = & $_SESSION['auth'];

	$_data = array(
		'area' => AREA,
		'timestamp' => TIME,
		'user_id' => $auth['user_id'],
		'gift_cert_id' => $gift_cert_id,
		'amount' => $before_info['amount'],
		'products' => $before_info['products'],
		'debit' => $after_info['amount'],
		'debit_products' => $after_info['products'],
		'order_id' => $order_id
	);

	return db_query("REPLACE INTO ?:gift_certificates_log ?e", $_data);

}

function fn_prepare_gift_certificate_log($fields, $data)
{
	$fields_array = explode(',', $fields);
	array_walk($fields_array, 'fn_trim_helper');
	$result = array();

	foreach ((array)$fields_array as $field) {
		$result[$field] = '';
		if (!empty($field) && !empty($data) && !empty($data[$field]) && Registry::get('addons.gift_certificates.free_products_allow') == 'Y') {
			$result[$field] = unserialize($data[$field]);
			if (is_array($result[$field])) {
				foreach ($result[$field] as $val) {
					fn_fill_certificate_products($result[$field]);
				}
			}
		}
	}
	return $result;
}

function fn_get_gift_certificate_log($gift_cert_id, $params)
{
	if (empty($gift_cert_id)) {
		return false;
	}

	$params['page'] = empty($params['page']) ? 1 : $params['page'];

	$sortings = array (
		'timestamp' => "?:gift_certificates_log.timestamp",
		'amount'    => "?:gift_certificates_log.amount",
		'debit'     => "?:gift_certificates_log.debit",
		'username'  => "?:users.user_login",
		'name'      => "?:users.lastname",
		'email'     => "?:users.email",
		'order_id'  => "?:gift_certificates_log.order_id",
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	$sort_order = (!empty($params['sort_order'])) ? $params['sort_order'] : '';
	$sort_by = (!empty($params['sort_by'])) ? $params['sort_by'] : '';

	if (empty($sort_order) || empty($directions[$sort_order])) {
		$sort_order = 'desc';
	}

	if (empty($sort_by) || empty($sortings[$sort_by])) {
		$sort_by = 'timestamp';
	}


	$sort = "ORDER BY " . $sortings[$sort_by] . " " . $directions[$sort_order];

	$q_fields = array (
		"?:gift_certificates_log.*",
		"?:users.user_login",
		"?:users.email",
		"?:users.firstname",
		"?:users.lastname",
		"?:orders.email as order_email",
		"?:orders.firstname as order_firstname",
		"?:orders.lastname as order_lastname",
	);

	$total_items = db_get_field("SELECT COUNT(*) FROM ?:gift_certificates_log WHERE gift_cert_id = ?i", $gift_cert_id);
	$items_per_page	 = Registry::get('settings.Appearance.' . (AREA == 'A') ? 'admin_elements_per_page' : 'elements_per_page') ;
	$limit = fn_paginate($params['page'], $total_items, $items_per_page);

	$log  = db_get_array("SELECT " . implode(',', $q_fields) . " FROM ?:gift_certificates_log LEFT JOIN ?:users ON ?:users.user_id = ?:gift_certificates_log.user_id LEFT JOIN ?:orders ON ?:orders.order_id = ?:gift_certificates_log.order_id WHERE gift_cert_id = ?i $sort $limit", $gift_cert_id);

	foreach ($log as $k => $v) {
		$prepared_log = fn_prepare_gift_certificate_log('products, debit_products', $v);
		$log[$k] = fn_array_merge($v, $prepared_log);
	}

	return array($log, (($sort_order == 'asc') ? 'desc' : 'asc'), $sort_by);
}

function fn_get_gift_certificate_name($cert_id)
{
	if (!empty($cert_id)) {
		return db_get_field("SELECT gift_cert_code FROM ?:gift_certificates WHERE gift_cert_id = ?i", $cert_id);
	}

	return false;
}

function fn_get_gift_certificate_info($certificate, $type = 'B', $stored_products = array(), $lang_code = CART_LANGUAGE)
{
	if ($type == 'B' && is_numeric($certificate)) {
		$_certificate = db_get_row("SELECT * FROM ?:gift_certificates WHERE gift_cert_id = ?i", $certificate);
	} elseif ($type == 'C' && is_numeric($certificate)) {
		$_certificate = fn_array_merge($_SESSION['cart']['gift_certificates'][$certificate], array('gift_cert_cart_id' => $certificate));
	} elseif ($type == 'P' && is_array($certificate)) {
		$_certificate = $certificate;
		if (empty($_certificate['gift_cert_code'])) {
			$_certificate['gift_cert_code'] = preg_replace('/[0-9A-Z]/', 'X', fn_generate_gift_certificate_code());
		}
	}

	fn_set_hook('get_gift_certificate_info', $_certificate, $certificate, $type);

	if (!empty($_certificate)) {
		//Prepare descriptions
		if (!empty($_certificate['title'])) {
			$titles = fn_get_static_data_section('T', false);
			$_certificate['descr_title'] = $titles[$_certificate['title']]['descr'];
		}
		if (!empty($_certificate['state'])) {
			$_descr_state = fn_get_state_name($_certificate['state'], $_certificate['country'], $lang_code);
			$_certificate['descr_state'] = (empty($_descr_state)) ? $_certificate['state'] : $_descr_state;
		}
		if (!empty($_certificate['country'])) {
			$_certificate['descr_country'] = fn_get_country_name($_certificate['country'], $lang_code);
		}
		if (!empty($_certificate['products'])) {
			if ($type == 'B') {
				$_certificate['products'] = @unserialize($_certificate['products']);
			}
			fn_fill_certificate_products($_certificate['products'], $stored_products, $lang_code);
		}
		if (!empty($_certificate['debit_products'])) {
			if ($type == 'B') {
				$_certificate['debit_products'] = @unserialize($_certificate['debit_products']);
			}
			fn_fill_certificate_products($_certificate['debit_products'], $stored_products, $lang_code);
		}
	}

	return $_certificate;
}


function fn_fill_certificate_products(&$certificate_products, $stored_products = array(), $lang_code = CART_LANGUAGE)
{
	if (!empty($certificate_products)) {
		foreach($certificate_products as $cp_id => $cp) {
			if (!empty($stored_products)) {
				$ok = false;
				foreach($stored_products as $sp_id => $sp) {
					if ($cp['product_id'] == $sp['product_id']) {
						if ((!empty($cp['product_options']) && !empty($sp['extra']['product_options']) && array_diff($cp['product_options'], $sp['extra']['product_options']) == array()) || (empty($cp['product_options']) && empty($sp['extra']['product_options']))) {
							if (!empty($sp['extra']['product_options_value'])) {
								$certificate_products[$cp_id]['product_options_value'] = $sp['extra']['product_options_value'];
							}
							if (!empty($sp['extra']['product'])) {
								$certificate_products[$cp_id]['product'] = $sp['extra']['product'];
							}
							break;
						}
					}
				}
			}
			if (empty($certificate_products[$cp_id]['product_options_value']) && !empty($cp['product_options'])) {
				$certificate_products[$cp_id]['product_options_value'] = fn_get_selected_product_options_info($cp['product_options']);
			}
			if (empty($certificate_products[$cp_id]['product'])) {
				$certificate_products[$cp_id]['product'] = fn_get_product_name($cp['product_id'], $lang_code);
			}
		}
	}
}


function fn_delete_gift_certificate($gift_cert_id, $extra = array())
{
	$gift_data = db_get_row("SELECT gift_cert_code, order_ids FROM ?:gift_certificates WHERE gift_cert_id = ?i", $gift_cert_id);

	if (!empty($gift_data['order_ids'])) {
		$srch = array('[code]', '[ids]');
		$repl = array($gift_data['gift_cert_code'], $gift_data['order_ids']);
		$msg = str_replace($srch, $repl, fn_get_lang_var('text_gift_cert_cannot_delete'));
		fn_set_notification('N', fn_get_lang_var('notice'), $msg);

		return false;
	}

	db_query("DELETE FROM ?:gift_certificates WHERE gift_cert_id = ?i", $gift_cert_id);
	db_query("DELETE FROM ?:gift_certificates_log WHERE gift_cert_id = ?i", $gift_cert_id);

	fn_set_hook('delete_gift_certificate', $gift_cert_id, $extra);

	return true;
}

//
// Function for cart routine
//

function fn_correct_gift_certificate(&$gift_cert_data)
{
	$currencies = Registry::get('currencies');

	if (!empty($gift_cert_data['products'])) {
		foreach ($gift_cert_data['products'] as $product_id => $v) {
			if (!is_numeric($v['product_id'])) {
				unset($gift_cert_data['products'][$product_id]);
			}
		}
	}

	$min_amount = Registry::get('addons.gift_certificates.min_amount');
	$max_amount = Registry::get('addons.gift_certificates.max_amount');
	$amount_to_compare = $gift_cert_data['amount'];
	if ($currencies[CART_SECONDARY_CURRENCY]['is_primary'] != 'Y' && $gift_cert_data['amount_type'] == 'I') {
		$amount_to_compare = fn_format_price($amount_to_compare * $currencies[CART_SECONDARY_CURRENCY]['coefficient']) ;
	}

	if ($amount_to_compare > $max_amount || $amount_to_compare < $min_amount) {
		$gift_cert_data['amount'] = $gift_cert_data['amount'] > $max_amount ? $max_amount : $min_amount;
		$msg = fn_get_lang_var('gift_cert_amount_changed') . "<br />" . fn_get_lang_var('text_gift_cert_amount_higher') . " " . $max_amount . " " . fn_get_lang_var('text_gift_cert_amount_less') . " " . $min_amount;
		fn_set_notification('N', fn_get_lang_var('notice'), $msg);
	}

	if (!isset($gift_cert_data['correct_amount'])) {
		$amount_to_format = $gift_cert_data['amount'];
		if ($currencies[CART_SECONDARY_CURRENCY]['is_primary'] != 'Y' && $gift_cert_data['amount_type'] == 'I') {
			$amount_to_format =$amount_to_format * $currencies[CART_SECONDARY_CURRENCY]['coefficient'];
		}
		$gift_cert_data['amount'] = fn_format_price($amount_to_format);
	} else {
		unset($gift_cert_data['correct_amount']);
	}
}

/**
 * Add gift certificate to cart
 *
 * @param array $gift_cert_data array with data for the certificate to add)
 * @param array $auth user session data
 * @return array array with gift certificate ID and data if addition is successful and empty array otherwise
 */
function fn_add_gift_certificate_to_cart($gift_cert_data, &$auth)
{
	if (!empty($gift_cert_data) && is_array($gift_cert_data)) {
		fn_correct_gift_certificate($gift_cert_data);
		$gift_cert_cart_id = fn_generate_gift_certificate_cart_id($gift_cert_data);

		if (isset($gift_cert_data['products']) && !empty($gift_cert_data['products'])) {
			foreach ((array)$gift_cert_data['products'] as $pr_id => $product_item) {
				$product_data = array();
				$product_data[$product_item['product_id']] = array(
					'product_id' => $product_item['product_id'],
					'amount' => $product_item['amount'],
					'extra' => array('parent' => array('certificate' => $gift_cert_cart_id))
				);
				if (isset($product_item['product_options'])) {
					$product_data[$product_item['product_id']]['product_options'] = $product_item['product_options'];
				}

				if (fn_add_product_to_cart($product_data, $_SESSION['cart'], $auth) == array()) {
					unset($gift_cert_data['products'][$pr_id]);
				}
			}
		}

		return array (
			$gift_cert_cart_id,
			$gift_cert_data
		);

	} else {

		return array();
	}
}

function fn_delete_cart_gift_certificate(&$cart, $gift_cert_id)
{
	if (!empty($gift_cert_id)) {
		if (isset($cart['products'])) {
			foreach ((array)$cart['products'] as $k=>$v) {
				if (isset($v['extra']['parent']['certificate']) && $v['extra']['parent']['certificate'] == $gift_cert_id) {
					unset($cart['products'][$k]);
				}
			}
		}
		unset($cart['gift_certificates'][$gift_cert_id]);
		if (empty($cart['gift_certificates'])) {
			unset($cart['gift_certificates']);
		}
	}
}

function fn_delete_gift_certificate_in_use($gift_cert_code, &$cart)
{
	if (!empty($gift_cert_code)) {
		foreach ((array)$cart['products'] as $k => $v) {
			if (isset($v['extra']['in_use_certificate'][$gift_cert_code])) {
				unset($cart['products'][$k]['extra']['in_use_certificate'][$gift_cert_code]);
				if (empty($cart['products'][$k]['extra']['in_use_certificate'])) {
					unset($cart['products'][$k]);
				} else {
					$cart['products'][$k]['amount'] -= $v['extra']['in_use_certificate'][$gift_cert_code];
				}
			}
		}

		if (!empty($cart['deleted_exclude_products'][GIFT_CERTIFICATE_EXCLUDE_PRODUCTS])) {
			foreach ($cart['deleted_exclude_products'][GIFT_CERTIFICATE_EXCLUDE_PRODUCTS] as $cart_id => $v) {
				if (isset($v['in_use_certificate'][$gift_cert_code])) {
					unset($cart['deleted_exclude_products'][GIFT_CERTIFICATE_EXCLUDE_PRODUCTS][$cart_id]);
				}
			}
		}

		if (!empty($cart['use_gift_certificates'][$gift_cert_code]['products'])) {
			unset($_SESSION['shipping_rates']);
		}
		$cart['reset_use_gift_certificates'][] = $cart['use_gift_certificates'][$gift_cert_code]['gift_cert_id'];
		unset($cart['use_gift_certificates'][$gift_cert_code]);
	}
}

function fn_generate_gift_certificate_cart_id($gift_cert_data)
{
	$_gc = array();
	$_gc[] = TIME;
	if (!empty($gift_cert_data)) {
		foreach ((array)$gift_cert_data as $k => $v) {
			if ($k == 'products') {
				if (!empty($v)) {
					foreach ($v as $product_item) {
						$_gc[] = $product_item['product_id'];
						$_gc[] = $product_item['amount'];
						if (isset($product_item['product_options'])) {
							$_gc[] = serialize($product_item['product_options']);
						}
					}
				}
			} elseif ($k == 'extra' && !empty($v)) {
				if (!empty($v)) {
					foreach ($v as $field => $data) {
						$_gc[] = $field;
						$_gc[] = is_array($data) ? serialize($data) : $data;
					}
				}
			} else {
				$_gc[] = $v;
			}
		}
	}
	return fn_crc32(implode('_', $_gc));
}

function fn_get_gift_certificate_templates()
{
	$templates = array();
	$dir = DIR_SKINS . Registry::get('config.skin_name') . '/mail/addons/gift_certificates/templates';
	$files = fn_get_dir_contents($dir, false, true, 'tpl');
	foreach ($files as $file) {
		$path_parts = explode(".", $file);
		$file_name = fn_get_lang_var($path_parts[0]);
		$templates[$file] = !empty($file_name) ? $file_name : ucfirst($path_parts[0]);
	}

	return 	$templates;
}

function fn_show_postal_card($gift_cert_data, $stored_products = array())
{
	if (!empty($gift_cert_data['template'])) {
		$templates = fn_get_gift_certificate_templates();
		$gift_cert_data['template'] = isset($templates[$gift_cert_data['template']]) ? $gift_cert_data['template'] : key($templates);

		$gc_data = fn_get_gift_certificate_info($gift_cert_data, 'P', $stored_products);

		Registry::get('view_mail')->assign('gift_cert_data', $gc_data);
		Registry::get('view_mail')->display('addons/gift_certificates/templates/' . $gift_cert_data['template']);
	}
}

function fn_generate_gift_certificate_code($quantity = 12)
{
	return fn_generate_code(Registry::get('addons.gift_certificates.code_prefix'), $quantity);
}

function fn_check_gift_certificate_code($code, $is_usable = false)
{
	$status = db_get_field("SELECT status FROM ?:gift_certificates WHERE gift_cert_code = ?s", $code);
	return (!empty($status)  ? ($is_usable == false) ? true : (strstr('A', $status)) ? true : false : false);
}

//
// INCLUDE FUNCTION
//

function fn_gift_certificates_generate_cart_id(&$_cid, $extra)
{

	if (isset($extra['parent']['certificate'])) {
		$_cid[] = $extra['parent']['certificate'];
	}

	return true;
}

function fn_gift_certificates_save_cart(&$cart, $user_id, $type = 'C')
{
	if (!empty($cart['products']) && is_array($cart['products'])) {
		foreach ($cart['products'] as $_item_id => $_prod) {
			if (isset($_prod['extra']['parent']['certificate'])) {
				db_query("UPDATE ?:user_session_products SET ?u WHERE item_id = ?i AND item_type = 'P' AND type = ?s AND user_id = ?i AND product_id = ?i", array('item_type' => 'C'), $_item_id, $type, $user_id, $_prod['product_id']);
				foreach ($cart['gift_certificates'][$_prod['extra']['parent']['certificate']]['products'] as $free_prod_id => $free_prod) {
					if ($free_prod['product_id'] == $_prod['product_id']) {
						$cart['gift_certificates'][$_prod['extra']['parent']['certificate']]['products'][$free_prod_id]['amount'] = $_prod['amount'];
						break;
					}
				}
			}
		}
	}

	if (!empty($cart['gift_certificates']) && is_array($cart['gift_certificates'])) {
		$_cart_gift_cert = $cart['gift_certificates'];
		foreach ($_cart_gift_cert as $_item_id => $_gift_cert) {
			$_cart_gift_cert[$_item_id]['user_id'] = $user_id;
			$_cart_gift_cert[$_item_id]['timestamp'] = TIME;
			$_cart_gift_cert[$_item_id]['type'] = $type;
			$_cart_gift_cert[$_item_id]['item_id'] = $_item_id;
			$_cart_gift_cert[$_item_id]['item_type'] = 'G';//Gift certificate
			$_cart_gift_cert[$_item_id]['extra'] = serialize($_gift_cert);
			$_cart_gift_cert[$_item_id]['price'] = $_gift_cert['amount'];
			$_cart_gift_cert[$_item_id]['amount'] = 1;
			$_cart_gift_cert[$_item_id]['user_type'] = empty($_SESSION['auth']['user_id']) ? 'U' : 'R';
			$_cart_gift_cert[$_item_id] = fn_check_table_fields($_cart_gift_cert[$_item_id], 'user_session_products');
			if (!empty($_cart_gift_cert[$_item_id])) {
				db_query('REPLACE INTO ?:user_session_products ?e', $_cart_gift_cert[$_item_id]);
			}
		}
	}
}

function fn_gift_certificates_get_cart_item_types(&$item_types, $action)
{
	$item_types[] = 'C';//product in Certificate
	if ($action == 'V') {
		$item_types[] = 'G';//Gift certificate
	}
}

function fn_gift_certificates_init_secure_controllers($controllers)
{
	$controllers[] = 'gift_certificates';
}

function fn_gift_certificates_extract_cart(&$cart, $user_id, $type = 'C', $user_type = 'R')
{
	if (!empty($user_id)) {
		$_cart_gift_cert = db_get_hash_array("SELECT * FROM ?:user_session_products WHERE user_id = ?i AND type = ?s AND item_type = 'G' AND user_type = ?s", 'item_id', $user_id, $type, $user_type);
		if (!empty($_cart_gift_cert) && is_array($_cart_gift_cert)) {
			$cart['gift_certificates'] = empty($cart['gift_certificates']) ? array() : $cart['gift_certificates'];
			foreach ($_cart_gift_cert as $_item_id => $_gift_cert) {
				$_gift_cert_extra = unserialize($_gift_cert['extra']);
				unset($_gift_cert['extra']);
				$cart['gift_certificates'][$_item_id] = empty($cart['gift_certificates'][$_item_id]) ? fn_array_merge($_gift_cert, $_gift_cert_extra, true) : $cart['gift_certificates'][$_item_id];
			}
		}
	}
}

function fn_gift_certificates_place_order($order_id, $fake, $fake1, &$cart)
{

	if (!empty($order_id)) {

		if (defined('ORDER_MANAGEMENT')) {
			// If the purchased certificate was deleted when editing, then it should be updated in the database
			if (!empty($cart['gift_certificates_previous_state'])) {
				$flip_gcps = array_flip(array_keys($cart['gift_certificates_previous_state']));
				$flip_gc = array_flip(array_keys((!empty($cart['gift_certificates'])) ? $cart['gift_certificates'] : array()));
				$diff = array_diff_key($flip_gcps, $flip_gc);
				if (!empty($diff)) {
					foreach ($diff as $gift_cert_cart_id => $v) {
						db_query("UPDATE ?:gift_certificates SET order_ids = ?p WHERE gift_cert_id = ?i", fn_remove_from_set('order_ids', $order_id), $cart['gift_certificates_previous_state'][$gift_cert_cart_id]['gift_cert_id']);
					}
					db_query("DELETE FROM ?:order_data WHERE order_id = ?i AND type = ?s", $order_id, ORDER_DATA_PURCHASED_GIFT_CERTIFICATES);
				}
			}
		}

		if (isset($cart['reset_use_gift_certificates'])) {
			foreach ($cart['reset_use_gift_certificates'] as $v) {
				db_query("UPDATE ?:gift_certificates SET order_ids = ?p WHERE gift_cert_id = ?i", fn_remove_from_set('order_ids', $order_id), $v);
			}
			unset($cart['reset_use_gift_certificates']);
		}

		if (isset($cart['gift_certificates'])) {

			foreach ($cart['gift_certificates'] as $k => $v) {
				if (defined('ORDER_MANAGEMENT') && !empty($v['gift_cert_code'])) {
					$code = $v['gift_cert_code'];
				} else {
					do {
						$code = fn_generate_gift_certificate_code();
					} while (true == fn_check_gift_certificate_code($code));
				}

				$_data = fn_check_table_fields($v, 'gift_certificates');
				$_data = fn_array_merge($_data, array('gift_cert_code' => $code, 'timestamp' => TIME, 'status' => 'P'));
				$_data['products'] = !empty($_data['products']) ? serialize($_data['products']) : '';

				$gift_cert_id = db_query('REPLACE INTO ?:gift_certificates ?e', $_data);
				$cart['gift_certificates'][$k] = fn_array_merge($v, array('gift_cert_id' => $gift_cert_id, 'gift_cert_code' => $code));
				db_query("UPDATE ?:gift_certificates SET order_ids = ?p WHERE gift_cert_id = ?i", fn_add_to_set('order_ids', $order_id), $gift_cert_id);
				if (defined('ORDER_MANAGEMENT')) {
				//If the certificate was not removed from the order, it is necessary to check  whether the products and amount have been changed and modify the log.
					$debit_info = db_get_row("SELECT debit AS amount, debit_products AS products FROM ?:gift_certificates_log WHERE gift_cert_id = ?i ORDER BY timestamp DESC", $gift_cert_id);
					if (empty($debit_info)) {
						$debit_info = db_get_row("SELECT amount, products FROM ?:gift_certificates WHERE gift_cert_id = ?i", $gift_cert_id);
					}

					if (($_data['amount'] - $debit_info['amount'] != 0) || (md5($_data['products']) != md5($debit_info['products']))) {
						$_info = array(
							'amount' => $_data['amount'],
							'products' => $_data['products']
						);
						fn_add_gift_certificate_log_record($gift_cert_id, $debit_info, $_info);
					}
				}
			}

			$order_data = array(
				'order_id' => $order_id,
				'type' => ORDER_DATA_PURCHASED_GIFT_CERTIFICATES,
				'data' => serialize($cart['gift_certificates'])
			);
			db_query("REPLACE INTO ?:order_data ?e", $order_data);
		}

//--> FIXME: optimize this code:
		if (!empty($cart['use_gift_certificates_previous_state'])) {
			$flip_ugcps = array_flip(array_keys($cart['use_gift_certificates_previous_state']));
			$flip_ugc = array_flip(array_keys((!empty($cart['use_gift_certificates'])) ? $cart['use_gift_certificates'] : array()));
			$diff = array_diff_key($flip_ugcps, $flip_ugc);
			if (!empty($diff)) {
				foreach ($diff as $gift_cert_code => $v) {
					$gc_data = $cart['use_gift_certificates_previous_state'][$gift_cert_code]['previous_state'];
					$log_records = db_get_array("SELECT log_id, amount, debit, products, debit_products FROM ?:gift_certificates_log WHERE log_id >= ?i AND gift_cert_id = ?i ORDER BY timestamp ASC", $gc_data['log_id'], $gc_data['gift_cert_id']);
					foreach ($log_records as $record) {
						if (!empty($gc_data['products'])) {
							if ($record['log_id'] != $gc_data['log_id']) {
								$record['products'] = unserialize($record['products']);
								foreach ($gc_data['products'] as $po_product_id => $po_quantity) {
									if (!isset($record['products'][$po_product_id])) {
										$record['products'][$po_product_id] = $po_quantity;
									} else {
										$record['products'][$po_product_id] += $po_quantity;
									}
									if (empty($record['products'][$po_product_id])) {
										unset($record['products'][$po_product_id]);
									}
								}
								$record['products'] = serialize($record['products']);
							}

							$record['debit_products'] = unserialize($record['debit_products']);
							foreach ($gc_data['products'] as $po_product_id => $po_quantity) {
								if (!isset($record['debit_products'][$po_product_id])) {
									$record['debit_products'][$po_product_id] = $po_quantity;
								} else {
									$record['debit_products'][$po_product_id] += $po_quantity;
								}
								if (empty($record['debit_products'][$po_product_id])) {
									unset($record['debit_products'][$po_product_id]);
								}
							}
							$record['debit_products'] = serialize($record['debit_products']);														}

						if ($record['log_id'] != $gc_data['log_id']) {
							$record['amount'] += $gc_data['cost'];
						}
						$record['debit'] += $gc_data['cost'];

						db_query("UPDATE ?:gift_certificates_log SET ?u WHERE log_id = ?i", $record, $record['log_id']);
						
						if (floatval($record['debit']) > 0 || unserialize($record['debit_products']) != array() && db_get_field("SELECT status FROM ?:gift_certificates WHERE gift_cert_id = ?", $gc_data['gift_cert_id']) == 'U') {
							fn_change_gift_certificate_status($gc_data['gift_cert_id'], 'A');
						}
					}
				}
			}
		}

		if (isset($cart['use_gift_certificates'])) {
			$debit_products = array();
			$use_gift_certificates = array();

			if (!empty($cart['deleted_exclude_products'][GIFT_CERTIFICATE_EXCLUDE_PRODUCTS])) {
				foreach ($cart['deleted_exclude_products'][GIFT_CERTIFICATE_EXCLUDE_PRODUCTS] as $cart_id => $v) {
					foreach ($v['in_use_certificate'] as $gift_cert_code => $amount) {
						$debit_products[$gift_cert_code]['products'][$v['product_id']] = $amount;
					}
				}
			}

			$use_gift_certificate_products = array();
			if (!empty($cart['products'])) {
				foreach ($cart['products'] as $product) {
					if (!empty($product['extra']['exclude_from_calculate']) && $product['extra']['exclude_from_calculate'] == GIFT_CERTIFICATE_EXCLUDE_PRODUCTS) {
						foreach ($product['extra']['in_use_certificate'] as $gift_cert_code => $quantity) {
							$use_gift_certificate_products[$gift_cert_code][$product['product_id']] = $quantity;
						}
					}
				}
			}

			foreach ($cart['use_gift_certificates'] as $k=>$v) {
				if (!empty($v['log_id'])) {
					$product_odds = array();
					$amount_odds = $v['previous_state']['cost'] - $v['cost'];
					$current_state_products = (!empty($use_gift_certificate_products[$k]) ? $use_gift_certificate_products[$k] : array());
					if (sizeof($v['previous_state']['products']) != sizeof($current_state_products) || serialize($v['previous_state']['products']) != serialize($current_state_products)) {
						if (!empty($v['previous_state']['products'])) {
							foreach ($v['previous_state']['products'] as $product_id => $quantity) {
								if (!isset($current_state_products[$product_id])) {
									$product_odds[$product_id] = $quantity;
								} else {
									$product_odds[$product_id] = $quantity - $current_state_products[$product_id];
								}
								if (empty($product_odds[$product_id])) {
									unset($product_odds[$product_id]);
								}
							}
						} elseif (!empty($current_state_products)) {
							foreach ($current_state_products as $product_id => $quantity) {
								$product_odds[$product_id] = -$quantity;
							}
						}
					}

					if ($amount_odds != 0 || !empty($product_odds)) {
						$log_records = db_get_array("SELECT log_id, amount, debit, products, debit_products FROM ?:gift_certificates_log WHERE log_id >= ?i AND gift_cert_id = ?i ORDER BY timestamp ASC", $v['log_id'], $v['gift_cert_id']);
						foreach ($log_records as $record) {
							if (!empty($product_odds)) {
								if ($record['log_id'] != $v['log_id']) {
									$record['products'] = unserialize($record['products']);
									foreach ($product_odds as $po_product_id => $po_quantity) {
										if (!isset($record['products'][$po_product_id])) {
											$record['products'][$po_product_id] = $po_quantity;
										} else {
											$record['products'][$po_product_id] += $po_quantity;
										}
										if (empty($record['products'][$po_product_id])) {
											unset($record['products'][$po_product_id]);
										}
									}
									$record['products'] = serialize($record['products']);
								}

								$record['debit_products'] = unserialize($record['debit_products']);
								foreach ($product_odds as $po_product_id => $po_quantity) {
									if (!isset($record['debit_products'][$po_product_id])) {
										$record['debit_products'][$po_product_id] = $po_quantity;
									} else {
										$record['debit_products'][$po_product_id] += $po_quantity;
									}
									if (empty($record['debit_products'][$po_product_id])) {
										unset($record['debit_products'][$po_product_id]);
									}
								}
								$record['debit_products'] = serialize($record['debit_products']);														} else {
								if ($record['log_id'] != $v['log_id']) {
									$record['amount'] += $amount_odds;
								}
								$record['debit'] += $amount_odds;
							}
							db_query("UPDATE ?:gift_certificates_log SET ?u WHERE log_id = ?i", $record, $record['log_id']);
							
							$use_gift_certificates[$k] = array (
								'gift_cert_id' => $v['gift_cert_id'],
								'amount' 	=> $v['previous_state']['amount'],
								'cost' => $v['cost'],
								'log_id' => $v['log_id']
							);
		
							if (floatval($record['debit']) <= 0 &&  unserialize($record['debit_products']) == array()) {
								fn_change_gift_certificate_status($v['gift_cert_id'], 'U');
							} elseif (floatval($record['debit']) > 0 || unserialize($record['debit_products']) != array() && db_get_field("SELECT status FROM ?:gift_certificates WHERE gift_cert_id = ?i", $v['gift_cert_id']) == 'U') {
								fn_change_gift_certificate_status($v['gift_cert_id'], 'A');
							}
						}
					}
//<-- FIXME: optimize this code
				} else {
					$before_info = array(
						'amount' 	=> $v['amount'],
						'products'  => serialize(!empty($v['products']) ? $v['products'] : array())
					);
					$after_info = array(
						'amount' 	=> fn_format_price($v['amount'] - $v['cost']),
						'products'  => serialize(!empty($debit_products[$k]['products']) ? $debit_products[$k]['products'] : array())
					);
					$log_id = fn_add_gift_certificate_log_record($v['gift_cert_id'], $before_info, $after_info, $order_id);

					$use_gift_certificates[$k] = array(
						'gift_cert_id' => $v['gift_cert_id'],
						'amount' 	=> $v['amount'],
						'cost' => $v['cost'],
						'log_id' => $log_id
					);
					if (floatval($v['amount'] - $v['cost']) <= 0 &&  !isset($debit_products[$k]['products'])) {
						fn_change_gift_certificate_status($v['gift_cert_id'], 'U');
					}
				}
				db_query("UPDATE ?:gift_certificates SET order_ids = ?p  WHERE gift_cert_id = ?i", fn_add_to_set('order_ids', $order_id), $v['gift_cert_id']);
			}

			$order_data = array(
				'order_id' => $order_id,
				'type' => 'U',
				'data' => serialize($use_gift_certificates)
			);
			db_query("REPLACE INTO ?:order_data ?e", $order_data);
		}
	}

}


function fn_gift_certificates_get_order_info(&$order, &$additional_data)
{
	if (!empty($additional_data[ORDER_DATA_PURCHASED_GIFT_CERTIFICATES])) {
		$subtotal = 0;
		$purchased_certificates = @unserialize($additional_data[ORDER_DATA_PURCHASED_GIFT_CERTIFICATES]);
		if (!empty($purchased_certificates)) {
			foreach ($purchased_certificates as $k => $v) {
				$purchased_certificates[$k]['subtotal'] = $v['amount'];
				$purchased_certificates[$k]['display_subtotal'] = $v['amount'];

				if (!isset($v['extra']['exclude_from_calculate'])) {
					$subtotal += $v['amount'];
				}

				if (!empty($v['products'])) {
					foreach ($order['items'] as $cart_id => $product) {
						if (!empty($product['extra']['parent']['certificate']) && $product['extra']['parent']['certificate'] == $k) {
							$purchased_certificates[$k]['subtotal'] += $product['subtotal'];
							$purchased_certificates[$k]['display_subtotal'] += $product['display_subtotal'];
						}
					}
				}
			}

			$order['subtotal'] += $subtotal;
			$order['display_subtotal'] += $subtotal;
			$order['pure_subtotal'] = (isset($order['pure_subtotal']) ? $order['pure_subtotal'] : 0) + $subtotal;
			$order['gift_certificates'] = $purchased_certificates;
		}
	}

	if (!empty($additional_data[ORDER_DATA_USE_GIFT_CERTIFICATES])) {
		$order['use_gift_certificates'] = @unserialize($additional_data[ORDER_DATA_USE_GIFT_CERTIFICATES]);
	}
}

function fn_gift_certificates_exclude_products_from_calculation(&$cart, &$auth, $pure_subtotal, $subtotal)
{

	if (isset($cart['gift_certificates']) && !fn_is_empty($cart['gift_certificates'])) {
		foreach ($cart['gift_certificates'] as $k => $v) {
			if (isset($v['extra']['exclude_from_calculate'])) {
				unset($cart['gift_certificates'][$k]);
			} else {
				$subtotal += $v['amount'];
				$pure_subtotal += $v['amount'];
			}
		}
	}

	if (!empty($cart['use_gift_certificates'])) {
		foreach ($cart['use_gift_certificates'] as $code => $value) {

			// This step is performed when editing the existent order only.
			if (is_array($value) && isset($value['log_id'])) {// indicates that the order is being edited

				$gift_cert_data = $value;

				// Merge with the current balance.
				$last_log_item = db_get_row("SELECT log_id, debit, debit_products FROM ?:gift_certificates_log WHERE gift_cert_id = ?i ORDER BY timestamp DESC", $value['gift_cert_id']);
				$last_log_item['debit_products'] = unserialize($last_log_item['debit_products']);

				$gift_cert_data['amount'] = $gift_cert_data['previous_state']['cost'] + $last_log_item['debit'];
				if (!empty($last_log_item['debit_products'])) {
					foreach ($last_log_item['debit_products'] as $product_id => $quantity) {
						if (!isset($gift_cert_data['products'][$product_id])) {
							$gift_cert_data['products'][$product_id] = $quantity['amount'];
						} else {
							$gift_cert_data['products'][$product_id] = (isset($gift_cert_data['previous_state']['products'][$product_id]) ? $gift_cert_data['previous_state']['products'][$product_id] : 0) + $quantity['amount'];
						}
					}
				}
				$cart['use_gift_certificates_previous_state'][$code] = $gift_cert_data;

			// This step is performed when editing the existent order only.
			} elseif (defined('ORDER_MANAGEMENT') && !empty($cart['use_gift_certificates_previous_state'][$code])) {
				//
				// If the certificate was deleted when editing, and then it was applied again.
				// It is necessary to set its data (not currect ones) again with the performed changes.
				//
				$gift_cert_data = $cart['use_gift_certificates_previous_state'][$code];

			// This step is performed only on Create order and in the customer area.
			} else {
				$gift_cert_data = db_get_row("SELECT gift_cert_id, amount, products  FROM ?:gift_certificates WHERE gift_cert_code = ?s", $code);
				$gift_cert_data['products'] = empty($gift_cert_data['products']) ? array() : @unserialize($gift_cert_data['products']);
				$debit_balance = db_get_row("SELECT debit AS amount, debit_products as products FROM ?:gift_certificates_log WHERE gift_cert_id = ?i ORDER BY timestamp DESC", $gift_cert_data['gift_cert_id']);
				if (!empty($debit_balance)) {
					$debit_balance['products'] = @unserialize($debit_balance['products']);
					$gift_cert_data  = fn_array_merge($gift_cert_data, $debit_balance);
				}
			}
			
			$cart['use_gift_certificates'][$code] = $gift_cert_data;
			
			if (!empty($gift_cert_data['products']) && AREA == 'C') {
				$product_data = array();
				foreach ((array)$gift_cert_data['products'] as $key => $product_item) {
					if (!empty($debit_balance) && !isset($debit_balance['products'][$key])) {
						continue;
					}
					
					$product_data[$product_item['product_id']] = array(
							'product_id' => $product_item['product_id'],
							'amount' => $product_item['amount'],
							'extra' => array(
								'exclude_from_calculate' => GIFT_CERTIFICATE_EXCLUDE_PRODUCTS,
								'in_use_certificate' => array($code => $product_item['amount'])
							)
					);
					if (isset($product_item['product_options'])) {
						$product_data[$product_item['product_id']]['product_options'] = $product_item['product_options'];
					}
					// Сhoose the option which the product had before editing.
					if (!empty($value['log_id']) && !empty($value['product_options'][$product_id])) {
						$product_data[$product_id]['product_options'] = $value['product_options'][$product_id];
					}
				}
				fn_add_product_to_cart($product_data, $cart, $auth);

				$cart['recalculate'] = true;
			}
		}
	}
}

function fn_gift_certificates_calculate_cart(&$cart, &$cart_products, &$auth)
{
	$subtotal = 0;

	if (isset($cart['additional_gift_certificates'])) {
		$cart['gift_certificates'] = fn_array_merge((!empty($cart['gift_certificates']) ? $cart['gift_certificates'] : array()), $cart['additional_gift_certificates']);
		unset($cart['additional_gift_certificates']);
	}

	if (!empty($cart['gift_certificates'])) {
		foreach ((array)$cart['gift_certificates'] as $k => $v) {
			$cart['gift_certificates'][$k]['subtotal'] = $v['amount'];
			$cart['gift_certificates'][$k]['display_subtotal'] = $v['amount'];
			$cart['gift_certificates'][$k]['tax_value'] = 0;

			if (!isset($v['extra']['exclude_from_calculate'])) {
				$subtotal += $v['amount'];
			}
			if (!empty($v['products'])) {
				foreach ($cart['products'] as $cart_id => $product) {
					if (!empty($product['extra']['parent']['certificate']) && $product['extra']['parent']['certificate'] == $k) {
						$cart['gift_certificates'][$k]['subtotal'] += $cart_products[$cart_id]['subtotal'];
						$cart['gift_certificates'][$k]['display_subtotal'] += $cart_products[$cart_id]['display_subtotal'];
						/*if (!empty($cart_products[$cart_id]['tax_summary']['added'])) {
							$cart['gift_certificates'][$k]['tax_value'] += $cart_products[$cart_id]['tax_summary']['added'];
						}*/
					}
				}
				foreach ($v['products'] as $id => $val) {
					$unset = false;
					foreach ($cart['products'] as $cart_id => $product) {
						if ($product['product_id'] == $val['product_id']) {
							$unset = true;
							break;
						}
					}
					if (!$unset) {
						unset($cart['gift_certificates'][$k]['products'][$id]);
					}
				}
			}
		}

		$cart['amount'] = (isset($cart['amount']) ? $cart['amount'] : 0) + sizeof($cart['gift_certificates']);
		$cart['total'] = (isset($cart['total']) ? $cart['total'] : 0) + $subtotal;
		$cart['subtotal'] += $subtotal;
		$cart['display_subtotal'] += $subtotal;

		$cart['pure_subtotal'] = (isset($cart['pure_subtotal']) ? $cart['pure_subtotal'] : 0) + $subtotal;
	}

	if (!empty($cart['use_gift_certificates'])) {

		$_original = $_subtotal = (Registry::get('addons.gift_certificates.redeem_shipping_cost') == 'Y') ? $cart['total'] : $cart['subtotal'];
		
		foreach ((array)$cart['use_gift_certificates'] as $code => $value) {
			$_subtotal -= $value['amount'];
			if ($_subtotal >= 0) {
				$cart['use_gift_certificates'][$code]['cost'] = $value['amount'];
			} else {
				$cart['use_gift_certificates'][$code]['cost'] = $value['amount'] + $_subtotal;
				$_subtotal = 0;
			}
		}

		$cart['total'] -= ($_original - $_subtotal);
		$cart['total'] = fn_format_price($cart['total']);
	}
}

function fn_gift_certificates_change_order_status($status_to, $status_from, &$order_info, $force_notification, $order_statuses)
{
	if (isset($order_info['gift_certificates'])) {
		foreach ($order_info['gift_certificates'] as $k => $v) {
			if (!empty($order_statuses[$status_to]['gift_cert_status'])) {
				fn_change_gift_certificate_status($v['gift_cert_id'], $order_statuses[$status_to]['gift_cert_status'], '', fn_get_notification_rules(array(), true)); // skip notification, it will be sent later in order_notification hook
			}
		}
	}
}

function fn_gift_certificates_order_notification(&$order_info, &$order_statuses, $force_notification = array())
{
	if (isset($order_info['gift_certificates'])) {
		foreach ($order_info['gift_certificates'] as $k => $v) {
			if (!empty($order_statuses[$order_info['status']]['gift_cert_status'])) {
				$gift_cert_data = fn_get_gift_certificate_info($v['gift_cert_id'], 'B');
				fn_gift_certificate_notification($gift_cert_data, $force_notification);
			}
		}
	}
}

function fn_gift_certificate_notification(&$gift_cert_data, $force_notification = array())
{
	static $notified = array();
	
	if (!empty($notified[$gift_cert_data['gift_cert_id']])) {
		return true;
	}
	
	$status_params = fn_get_status_params($gift_cert_data['status'], STATUSES_GIFT_CERTIFICATE);

	$notify_user = isset($force_notification['C']) ? $force_notification['C'] : (!empty($status_params['notify']) && $status_params['notify'] == 'Y' ? true : false);

	if ($notify_user == true && $gift_cert_data['email'] && $gift_cert_data['send_via'] == 'E') {
		$notified[$gift_cert_data['gift_cert_id']] = true;
		
		$templates = fn_get_gift_certificate_templates();
		$gift_cert_data['template'] = isset($templates[$gift_cert_data['template']]) ? $gift_cert_data['template'] : key($templates);

		Registry::get('view_mail')->assign('gift_cert_data', $gift_cert_data);
		Registry::get('view_mail')->assign('certificate_status', fn_get_status_data($gift_cert_data['status'], STATUSES_GIFT_CERTIFICATE, $gift_cert_data['gift_cert_id']));

		fn_send_mail($gift_cert_data['email'], Registry::get('settings.Company.company_orders_department'), 'addons/gift_certificates/gift_certificate_subj.tpl', 'addons/gift_certificates/gift_certificate.tpl');

		return true;
	}

	return false;
}


function fn_gift_certificates_is_cart_empty(&$cart, &$result)
{
	if (!empty($cart['gift_certificates'])) {
		$result = false;
	}

	if ($result && !empty($cart['products'])) {
		foreach ($cart['products'] as $v) {
			if (isset($v['extra']['exclude_from_calculate']) && $v['extra']['exclude_from_calculate'] == GIFT_CERTIFICATE_EXCLUDE_PRODUCTS) {
				$result = false;
				break;
			}
		}
	}
}

function fn_gift_certificates_delete_order($order_id)
{
	db_query("UPDATE ?:gift_certificates SET order_ids = ?p", fn_remove_from_set('order_ids', $order_id));
}

function fn_gift_certificates_delete_cart_product(&$cart, $cart_id)
{
	if (!empty($cart_id)) {
		if (isset($cart['products'][$cart_id]['extra']['parent']['certificate'])) {
			$gift_cert_cart_id = $cart['products'][$cart_id]['extra']['parent']['certificate'];
			$product_id = $cart['products'][$cart_id]['product_id'];

			if (isset($cart['gift_certificates'][$gift_cert_cart_id]['products'])) {
				foreach ($cart['gift_certificates'][$gift_cert_cart_id]['products'] as $id => $v) {
					if ($v['product_id'] == $product_id) {
						unset($cart['gift_certificates'][$gift_cert_cart_id]['products'][$id]);
						break;
					}
				}
			}

			if (empty($cart['gift_certificates'][$gift_cert_cart_id]['products'])) {
				unset($cart['gift_certificates'][$gift_cert_cart_id]['products']);
			}
		}
	}
}

function fn_create_return_gift_certificate($order_id, $amount)
{

	$order_info = fn_get_order_info($order_id);
	$templates = fn_get_gift_certificate_templates();

	$_data = array(
		'send_via'		 => 'E',
		'amount_type' 	 => 'I',
		'recipient' 	 => "$order_info[firstname] $order_info[lastname]",
		'sender' 		 => Registry::get('settings.Company.company_name'),
		'amount' 		 => $amount,
		'email' 		 => $order_info['email'],
		'address' 		 => $order_info['s_address'],
		'address_2' 	 => $order_info['s_address_2'],
		'city' 	 		 => $order_info['s_city'],
		'country' 		 => $order_info['s_country'],
		'state' 		 => $order_info['s_state'],
		'zipcode' 		 => $order_info['s_zipcode'],
		'phone' 		 => $order_info['phone'],
		'template'       => key($templates)
	);

	do {
		$code = fn_generate_gift_certificate_code();
	} while (true == fn_check_gift_certificate_code($code));

	$_data = fn_array_merge($_data, array('gift_cert_code' => $code, 'timestamp' => TIME));
	$gift_cert_id = db_query('INSERT INTO ?:gift_certificates ?e', $_data);

	return array($gift_cert_id => array('code' => $code, 'amount' => $amount));
}

function fn_gift_certificates_exclude_from_shipping_calculation(&$product, &$exclude)
{
	if (Registry::get('addons.gift_certificates.redeem_shipping_cost') != 'Y' && !empty($product['extra']['parent']['certificate'])) {
		$exclude = true;
	}
}

function fn_gift_certificates_form_cart(&$order_info, &$cart)
{
	if (!empty($order_info['gift_certificates'])) {
		$cart['gift_certificates'] = $cart['gift_certificates_previous_state'] = $order_info['gift_certificates'];
	}

	if (!empty($order_info['use_gift_certificates'])) {
		foreach ($order_info['use_gift_certificates'] as $gift_cert_code => $v) {
			$cart['use_gift_certificates'][$gift_cert_code] = $v;
			$cart['use_gift_certificates'][$gift_cert_code]['previous_state'] = $v;
			$cart['use_gift_certificates'][$gift_cert_code]['products'] =  array();
			$cart['use_gift_certificates'][$gift_cert_code]['previous_state']['products'] =  array();
		}

		// Set only those products that were used.
		foreach ($cart['products'] as $cart_id => $v) {
			if (!empty($v['extra']['exclude_from_calculate']) && $v['extra']['exclude_from_calculate'] == GIFT_CERTIFICATE_EXCLUDE_PRODUCTS) {
				foreach ($v['extra']['in_use_certificate'] as $gift_cert_code => $quantity) {
					$cart['use_gift_certificates'][$gift_cert_code]['products'][$v['product_id']] = $quantity;
					$cart['use_gift_certificates'][$gift_cert_code]['previous_state']['products'][$v['product_id']] = $quantity;
					if (!empty($v['product_options'])) {
						$cart['use_gift_certificates'][$gift_cert_code]['product_options'][$v['product_id']] = $v['product_options'];
					}
				}
			}
		}
	}
}

function fn_gift_certificates_allow_place_order(&$total, &$cart)
{
	if (!empty($cart['use_gift_certificates'])) {
		foreach ($cart['use_gift_certificates'] as $k => $v) {
			$total += $v['cost'];
		}
	}

	return true;
}

function fn_gift_certificates_get_orders(&$params, &$fields, &$sortings, &$condition, &$join)
{
	if (isset($params['gift_cert_code']) && fn_string_no_empty($params['gift_cert_code'])) {
		$condition .= db_quote(" AND gc_order_data.data LIKE ?l", "%" . trim($params['gift_cert_code']) . "%");
		$join .= db_quote(" LEFT JOIN ?:order_data as gc_order_data ON gc_order_data.order_id = ?:orders.order_id AND gc_order_data.type IN (?a)", explode('|', $params['gift_cert_in']));
	}

	return true;
}

function fn_get_gift_certificates($params)
{
	// Init filter
	$params = fn_init_view('gift_certs', $params);

	// Set default values to input params
	$params['page'] = empty($params['page']) ? 1 : $params['page'];

	// Define fields that should be retrieved
	$fields = array (
		'?:gift_certificates.gift_cert_id',
		'?:gift_certificates.gift_cert_code',
		'?:gift_certificates.timestamp',
		'?:gift_certificates.amount',
		'?:gift_certificates.status',
		'?:gift_certificates.recipient',
		'?:gift_certificates.sender',
		'?:gift_certificates.send_via',
		'?:gift_certificates.email',
	);

	// Define sort fields
	$sortings = array (
		'timestamp' => "?:gift_certificates.timestamp",
		'amount' => "?:gift_certificates.amount",
		'recipient' => "?:gift_certificates.recipient",
		'sender' => "?:gift_certificates.sender",
		'status' => "?:gift_certificates.status",
		'gift_cert_code' => "?:gift_certificates.gift_cert_code",
		'send_via' => "?:gift_certificates.send_via",
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		$params['sort_order'] = 'desc';
	}

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = 'timestamp';
	}

	$sort = $sortings[$params['sort_by']] . " " . $directions[$params['sort_order']];

	// Reverse sorting (for usage in view)
	$params['sort_order'] = $params['sort_order'] == 'asc' ? 'desc' : 'asc';

	$condition = $join = '';

	if (isset($params['sender']) && fn_string_no_empty($params['sender'])) {
		$condition .= db_quote(" AND ?:gift_certificates.sender LIKE ?l", "%" . trim($params['sender']) ."%");
	}

	if (isset($params['recipient']) && fn_string_no_empty($params['recipient'])) {
		$condition .= db_quote(" AND ?:gift_certificates.recipient LIKE ?l", "%" . trim($params['recipient']) ."%");
	}

	if (isset($params['email']) && fn_string_no_empty($params['email'])) {
		$condition .= db_quote(" AND ?:gift_certificates.email LIKE ?l", "%" . trim($params['email']) ."%");
	}

	if (!empty($params['amount_from'])) {
		$condition .= db_quote(" AND ?:gift_certificates.amount >= ?d", $params['amount_from']);
	}

	if (!empty($params['amount_to'])) {
		$condition .= db_quote(" AND ?:gift_certificates.amount <= ?d", $params['amount_to']);
	}

	if (!empty($params['gift_cert_ids'])) {
		$condition .= db_quote(" AND ?:gift_certificates.gift_cert_id IN (?n)", $params['gift_cert_ids']);
	}

	if (!empty($params['status'])) {
		$condition .= db_quote(" AND ?:gift_certificates.status IN (?a)", $params['status']);
	}

	if (isset($params['gift_cert_code']) && fn_string_no_empty($params['gift_cert_code'])) {
		$condition .= db_quote(" AND ?:gift_certificates.gift_cert_code LIKE ?l", "%" . trim($params['gift_cert_code']) ."%");
	}

	if (!empty($params['period']) && $params['period'] != 'A') {
		list($params['time_from'], $params['time_to']) = fn_create_periods($params);

		$condition .= db_quote(" AND (?:gift_certificates.timestamp >= ?i AND ?:gift_certificates.timestamp <= ?i)", $params['time_from'], $params['time_to']);
	}

	$items_per_page = Registry::get('addons.gift_certificates.cert_per_page');
	$total = db_get_field("SELECT COUNT(*) FROM ?:gift_certificates WHERE 1 $condition");
	$limit = fn_paginate($params['page'], $total, $items_per_page);

	$gift_certificates = db_get_array("SELECT " . implode(',', $fields) . " FROM ?:gift_certificates WHERE 1 $condition ORDER BY $sort $limit");

	foreach ($gift_certificates as $k => $v) {
		$debit_balance = db_get_row("SELECT debit, debit_products FROM ?:gift_certificates_log WHERE gift_cert_id = ?i ORDER BY timestamp DESC", $v['gift_cert_id']);
		$gift_certificates[$k]['debit'] = (empty($debit_balance)) ? $v['amount'] : $debit_balance['debit'];
	}

	return array($gift_certificates, $params);
}

function fn_gift_certificates_pre_add_to_cart($product_data, &$cart, $auth, $update)
{
	if ($update == true) {
		$certificate_products = array();
		foreach ($product_data as $k => $v) {
			if (isset($v['parent']['certificate'])) {
				$certificate_products[$v['parent']['certificate']][$v['product_id']] = $v['amount'];
			}
		}

		if (!empty($certificate_products)) {
			foreach ($certificate_products as $gift_cert_cart_id => $products) {
				$cart['gift_certificates'][$gift_cert_cart_id]['products'] = $products;
			}
		}
	}
}

function fn_gift_certificates_delete_cart_products(&$cart, $cart_id)
{
	$product = $cart['products'][$cart_id];

	if (!empty($product['extra']['exclude_from_calculate']) && $product['extra']['exclude_from_calculate'] == GIFT_CERTIFICATE_EXCLUDE_PRODUCTS) {
		$cart['deleted_exclude_products'][GIFT_CERTIFICATE_EXCLUDE_PRODUCTS][$cart_id] = array(
			'product_id' => $product['product_id'],
			'in_use_certificate' => $product['extra']['in_use_certificate']
		);
	}

	if (isset($product['extra']['parent']['certificate'])) {
		foreach ($cart['gift_certificates'][$product['extra']['parent']['certificate']]['products'] as $id => $v) {
			if ($v['product_id'] == $product['product_id']) {
				unset($cart['gift_certificates'][$product['extra']['parent']['certificate']]['products'][$id]);
				break;
			}
		}
	}
}

function fn_gift_certificates_promotion_gift_certificate($bonus, &$cart, &$auth, &$cart_products)
{
	$cart['promotions'][$bonus['promotion_id']]['bonuses'][$bonus['bonus']] = $bonus;

	if ($bonus['bonus'] == 'gift_certificate') {

		$_data = array(
			'send_via' => 'E', // email
			'amount_type' => 'I', // input
			'recipient' => !empty($cart['user_data']['firstname']) ? ($cart['user_data']['firstname'] . ' ' . $cart['user_data']['lastname']) : '',
			'sender' => Registry::get('settings.Company.company_name'),
			'amount' => $bonus['value'],
			'correct_amount' => 'N',
			'email' => !empty($cart['user_data']['email']) ? $cart['user_data']['email'] : '',
			'products' => array(),
			'extra' => array(
				'exclude_from_calculate' => 'PR',
			),
			'template' => 'default.tpl'
		);

		list($gc_cart_id, $gc) = fn_add_gift_certificate_to_cart($_data, $auth);

		if (!empty($gc_cart_id)) {
			$cart['additional_gift_certificates'][$gc_cart_id] = $gc;
		}

		$cart['promotions'][$bonus['promotion_id']]['bonuses'][$bonus['bonus']]['gc_cart_id'] = $gc_cart_id;
	}

	return true;
}

//
// Generate navigation
//
function fn_gift_certificates_generate_sections($section)
{
	Registry::set('navigation.dynamic.sections', array (
		'manage' => array (
			'title' => fn_get_lang_var('gift_certificates'),
			'href' => 'gift_certificates.manage',
		),
		'statuses' => array (
			'title' => fn_get_lang_var('gift_certificate_statuses'),
			'href' => 'statuses.manage?type=' . STATUSES_GIFT_CERTIFICATE,
		),
	));
	Registry::set('navigation.dynamic.active_section', $section);

	return true;
}

function fn_gift_certificates_get_status_params_definition(&$status_params, $type)
{
	if ($type == STATUSES_ORDER) {
		$status_params['gift_cert_status'] = array (
			'type' => 'status',
			'label' => 'change_gift_certificate_status',
			'status_type' => STATUSES_GIFT_CERTIFICATE
		);

	} elseif ($type == STATUSES_GIFT_CERTIFICATE) {
		$status_params = array (
			'notify' => array (
				'type' => 'checkbox',
				'label' => 'notify_customer'
			),
		);
	}

	return true;
}

function fn_gift_certificates_get_manifest_definition(&$areas)
{
	$areas['G'] = array (
		'skin' => 'customer',
		'path' => 'mail',
		'name' => 'Gift_certificate_logo',
		'text' => 'text_gift_certificate_logo'
	);
}

// Get the gift certificates codes from google request
function fn_gift_certificates_get_google_codes(&$cart, $xml_data, $codes)
{
	$cart['use_gift_certificates'] = array();

	$gift_certificates = $codes->getElementsByName('gift-certificate-adjustment');
	$gift_certificates_total = sizeof($gift_certificates);
	for ($j = 0; $j < $gift_certificates_total; $j++) {
		$code = $gift_certificates[$j] -> getValueByPath('/code');
		if (!empty($code)) {
			if (true == fn_check_gift_certificate_code($code, true)) {
				if (!isset($cart['use_gift_certificates'][$code])) {
					$cart['use_gift_certificates'][$code] = 'Y';
				}
			}
		}
	}

	return true;
}

//Check whether the code belongs to the gift certificates module
function fn_gift_certificates_apply_google_codes(&$cart, $codes)
{
	$cart['use_gift_certificates'] = array();

	foreach ($codes as $_code) {
		if (true == fn_check_gift_certificate_code($_code, true)) {
			if (!isset($cart['use_gift_certificates'][$_code])) {
				$cart['use_gift_certificates'][$_code] = 'Y';
			}
		}
	}

	return true;
}

//Form response for using gift certificate in the google_calculation
function fn_gift_certificates_form_google_codes_response(&$response, &$exist, $code, $cart, $currency_code)
{
	if (isset($cart['use_gift_certificates'][$code])) {
		$response[] = '<gift-certificate-result>';
		$response[] = '	<valid>true</valid>';
		$response[] = '	<code>' . $code . '</code>';
		$response[] = '	<calculated-amount currency="' . $currency_code . '">' . $cart['use_gift_certificates'][$code]['amount'] . '</calculated-amount>';
		$response[] = '	<message>Gift certificate is successfully applied.</message>';
		$response[] = '</gift-certificate-result>';

		$exist = true;
	}

	return true;
}

// Check whether we have some gift sertificates that could be used at google checkout
function fn_gift_certificates_google_coupons_calculation(&$string)
{
	$cert = db_get_field("SELECT COUNT(*) FROM ?:gift_certificates WHERE status='A'");
	$string .= '<accept-gift-certificates>' . ((!empty($cert)) ? 'true' : 'false') . '</accept-gift-certificates>';

	return true;
}

function fn_gift_certificates_get_google_add_items(&$_items, $cart, $_currency)
{
	// Check whether gift certificates exist
	if (!empty($cart['gift_certificates'])) {
		foreach ($cart['gift_certificates'] as $k => $v) {
			$_items .= "<item>" .
							"<item-name>" . fn_get_lang_var('gift_certificate') . "</item-name>" .
							"<item-description></item-description>" .
							"<unit-price currency='" . $_currency . "'>" . fn_format_price($v['amount']) . "</unit-price>" .
							"<quantity>1</quantity>" .
							"<digital-content>" .
								"<email-delivery>true</email-delivery>" .
							"</digital-content>" .
						"</item>";
		}
	}

	return true;
}

function fn_gift_certificates_reorder(&$order_info, &$cart, &$auth)
{
	// Check whether gift certificates exist
	if (isset($order_info['gift_certificates'])) {

		if (isset($order_info['items'])) {
			foreach ($order_info['items'] as $k => $item) {
				if (isset($order_info['items'][$k]['extra']['parent']['certificate'])) {
					unset($order_info['items'][$k]);
				}
			}
		}

		// Gift certificates is empty, create it
		if (empty($cart['gift_certificates'])) {
			$cart['gift_certificates'] = array();
		}

		foreach ($order_info['gift_certificates'] as $v) {
			unset($v['gift_cert_id']);
			unset($v['gift_cert_code']);
			unset($v['subtotal']);
			unset($v['display_subtotal']);
			unset($v['tax_value']);

			list($gift_cert_id, $gift_cert) = fn_add_gift_certificate_to_cart($v, $auth);

			if (!empty($gift_cert_id)) {
				$cart['gift_certificates'][$gift_cert_id] = $gift_cert;
			}
		}
	}

	return true;
}

/**
 * Add gift certificate to wishlist
 *
 * @param array $wishlist wishlist data storage
 * @param array $gift_cert_data array with data for the certificate to add
 * @return array array with gift certificate ID and data if addition is successful and empty array otherwise
 */
function fn_add_gift_certificate_to_wishlist(&$wishlist, $gift_cert_data)
{
	if (!empty($gift_cert_data) && is_array($gift_cert_data)) {
			fn_correct_gift_certificate($gift_cert_data);
			// Generate wishlist id
			$gift_cert_wishlist_id = fn_generate_gift_certificate_cart_id($gift_cert_data);
			$wishlist['gift_certificates'][$gift_cert_wishlist_id] = $gift_cert_data;
			if (!empty($gift_cert_data['products'])) {
				$product_data = array();

				foreach ($gift_cert_data['products'] as $w_id => $_data) {
					if (empty($_data['amount'])) {
						unset($gift_cert_data['products'][$w_id]);
						continue;
					}

					if (empty($_data['product_options'])) {
						$_data['product_options'] = fn_get_default_product_options($_data['product_id']);
					}

					$wishlist_id = fn_generate_cart_id($_data['product_id'], array('product_options' => $_data['product_options'], 'parent' => array('certificate' => $gift_cert_wishlist_id)), true);
					$product_data[$wishlist_id] = $_data;
					
					$wishlist['products'][$wishlist_id] = array(
						'product_id' => $_data['product_id'],
						'product_options' => $_data['product_options'],
						'amount' => $_data['amount'],
						'extra' => array(
							'parent' => array(
								'certificate' => $gift_cert_wishlist_id
							),
						),
					);
				}
				
				$gift_cert_data['products'] = $wishlist['gift_certificates'][$gift_cert_wishlist_id]['products'] = $product_data;
			}

			return array (
				$gift_cert_wishlist_id,
				$gift_cert_data
			);

	} else {

		return array();

	}
}

/**
 * Delete gift certificate from the wishlist
 *
 * @param array $wishlist wishlist data storage
 * @param int $gift_cert_wishlist_id gift certificate ID in the wishlist
 * @return boolean always true
 */
function fn_delete_wishlist_gift_certificate(&$wishlist, $gift_cert_wishlist_id)
{

	if (!empty($gift_cert_wishlist_id)) {
		$wishlist['products'] = empty($wishlist['products']) ? array() : $wishlist['products'];
		foreach ((array)$wishlist['products'] as $k=>$v) {
			if (isset($v['extra']['parent']['certificate']) && $v['extra']['parent']['certificate'] == $gift_cert_wishlist_id) {
				unset($wishlist['products'][$k]);
			}
		}
		unset($wishlist['gift_certificates'][$gift_cert_wishlist_id]);
		if (empty($wishlist['gift_certificates'])) {
			unset($wishlist['gift_certificates']);
		}
	}

	return true;
}

function fn_gift_certificates_order_placement_routines($order_id, $force_notification, $order_info)
{
	if ($order_info['status'] == 'N' && !empty($order_info['use_gift_certificates'])) {

		foreach ($order_info['use_gift_certificates'] as $k => $v) {
			db_query("UPDATE ?:gift_certificates SET status = 'A' WHERE gift_cert_id = ?i", $v['gift_cert_id']);
		}

		db_query("DELETE FROM ?:gift_certificates_log WHERE order_id = ?i", $order_id);
	}
}
?>