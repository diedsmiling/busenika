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
// $Id: fn.companies.php 10581 2010-09-02 12:46:41Z 2tl $
//

function fn_get_short_companies($params = array())
{
	$condition = $limit = '';

	if (!empty($params['status'])) {
		$condition .= db_quote(" AND status = ?s ", $params['status']);
	}
	
	if (!empty($params['displayed_vendors'])) {
		$limit = 'LIMIT ' . $params['displayed_vendors'];
	}
	
	$condition .= defined('COMPANY_ID') ? fn_get_company_condition('company_id', true, COMPANY_ID) : '';
	
	$count = db_get_field('SELECT COUNT(*) FROM ?:companies WHERE 1' . $condition);
	
	$_companies = db_get_hash_single_array("SELECT company_id, company FROM ?:companies WHERE 1 $condition ORDER BY company $limit", array('company_id', 'company'));
	$companies[0] = Registry::get('settings.Company.company_name');
	$companies = $companies + $_companies;

	$return = array(
		'companies' => $companies,
		'count' => $count,
	);

	if (!empty($params)) {
		unset($return['companies'][0]);
		return array($return);
	}
	return $companies;
}
	
function fn_get_companies($params, &$auth, $items_per_page = 0)
{
	// Init filter
	$_view = 'companies';
	$params = fn_init_view($_view, $params);

	// Set default values to input params
	$params['page'] = empty($params['page']) ? 1 : $params['page'];

	// Define fields that should be retrieved
	$fields = array (
		"?:companies.company_id",
		"?:companies.email",
		"?:companies.company",
		"?:companies.timestamp",
		"?:companies.status",
	);

	// Define sort fields
	$sortings = array (
		'id' => "?:users.user_id",
		'company' => "?:companies.company",
		'email' => "?:companies.email",
		'date' => "?:companies.timestamp",
		'status' => "?:companies.status",
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	$condition = $join = $group = '';

	$condition .= fn_get_company_condition('?:companies.company_id');
	
	$group .= " GROUP BY ?:companies.company_id";

	/*
	 * TODO search
	if (!empty($params['company'])) {
		$condition .= db_quote(" AND ?:users.company LIKE ?l", "%$params[company]%");
	}

	*/

	fn_set_hook('get_companies', $params, $fields, $sortings, $condition, $join);

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		$params['sort_order'] = 'asc';
	}

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = 'company';
	}

	$sorting = (is_array($sortings[$params['sort_by']]) ? implode(' ' . $directions[$params['sort_order']]. ', ', $sortings[$params['sort_by']]) : $sortings[$params['sort_by']]). " " .$directions[$params['sort_order']];

	// Reverse sorting (for usage in view)
	$params['sort_order'] = $params['sort_order'] == 'asc' ? 'desc' : 'asc';

	// Paginate search results
	$limit = '';
	if (!empty($items_per_page)) {
		$total = db_get_field("SELECT COUNT(DISTINCT(?:companies.company_id)) FROM ?:companies $join WHERE 1 $condition");
		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}

	$companies = db_get_array("SELECT " . implode(', ', $fields) . " FROM ?:companies $join WHERE 1 $condition $group ORDER BY $sorting $limit");

	return array($companies, $params);
}

function fn_get_company_condition($db_field = 'company_id', $and = true, $company = '', $show_admin = false, $area_c = false)
{
    
	$company = ($company === '') ? (defined('COMPANY_ID') ? COMPANY_ID : '') : $company;
    
	return ($company === '' || $company === 'all' || (AREA == 'C' && !$area_c)) ? '' : ((($and == true) ? ' AND' : '') . (($show_admin && $company) ? " $db_field IN (0, $company)" : " $db_field = $company"));
}

function fn_get_company_data($company_id, $lang_code = DESCR_SL)
{
	if (!empty($company_id)) {

		if (PRODUCT_TYPE == 'MULTIVENDOR') {
			$descriptions_list = "?:company_descriptions.*";
			$field_list = "?:companies.*, $descriptions_list";
		} else {
			$field_list = "?:companies.*";
		}
		
		$join = '';

		$condition = fn_get_company_condition('?:companies.company_id');
		if (PRODUCT_TYPE == 'MULTIVENDOR') {
			$company_data = db_get_row("SELECT $field_list FROM ?:companies LEFT JOIN ?:company_descriptions ON ?:company_descriptions.company_id = ?:companies.company_id AND ?:company_descriptions.lang_code = ?s ?p WHERE ?:companies.company_id = ?i $condition", $lang_code, $join, $company_id);
		} else {
			$company_data = db_get_row("SELECT $field_list FROM ?:companies ?p WHERE ?:companies.company_id = ?i $condition", $join, $company_id);
		}

		if (empty($company_data)) {
			return false;
		}

		$company_data['category_ids'] = explode(',', $company_data['categories']);
		$company_data['shippings_ids'] = explode(',', $company_data['shippings']);
		
		$company_data['logos_data'] = unserialize($company_data['logos']);
		
		$company_data['company_id'] = $company_id;
	}
	
	return (!empty($company_data) ? $company_data : false);
}

function fn_companies_apply_cart_shipping_rates(&$cart, $cart_products, $auth, $shipping_rates)
{
	$cart['use_suppliers'] = false;
	$cart['shipping_failed'] = false;

	// Get suppliers products
	$supplier_products = array();
	$total_freight = 0;
	foreach ($cart_products as $k => $v) {
		$s_id = !empty($v['company_id']) ? $v['company_id'] : 0;
		if (($v['is_edp'] != 'Y' || ($v['is_edp'] == 'Y' && $v['edp_shipping'] == 'Y')) && $v['free_shipping'] != 'Y') {
			$supplier_products[$s_id][] = $k;
		}
	}

	if (!empty($supplier_products) && !defined('CACHED_SHIPPING_RATES')) {
		$supplier_rates = array();
		foreach ($supplier_products as $rate_id => $products) {
			foreach ($products as $cart_id) {
				$rate = $cart_products[$cart_id]['shipping_freight'] * $cart_products[$cart_id]['amount'];
				empty($supplier_rates[$rate_id]) ? $supplier_rates[$rate_id] = $rate : $supplier_rates[$rate_id] += $rate;
				$total_freight += $rate;
			}
		}
		
		if (!empty($supplier_rates)) {
			foreach ($shipping_rates as $shipping_id => $shipping) {
				if (!empty($shipping['rates'])) {
					foreach ($shipping['rates'] as $rate_id => $rate) {
						if (isset($supplier_rates[$rate_id])) {
							$shipping_rates[$shipping_id]['rates'][$rate_id] = $rate - $total_freight + $supplier_rates[$rate_id];
						} else {
							unset($shipping_rates[$shipping_id]['rates'][$rate_id]);
						}
					}
				}
			}
		}
	}

	// Add zero rates to free shipping
	foreach ($shipping_rates as $sh_id => $v) {
		if (!empty($v['added_manually'])) {
			$shipping_rates[$sh_id]['rates'] = fn_array_combine(array_keys($supplier_products), 0);
		}
	}

	// If all suppliers should be displayed in one box, filter them

	if (PRODUCT_TYPE != 'MULTIVENDOR' && Registry::get('settings.Suppliers.display_shipping_methods_separately') !== 'Y') {
		$s_ids = array_keys($supplier_products);

		foreach ($shipping_rates as $sh_id => $v) {
			if (sizeof(array_intersect($s_ids, array_keys($v['rates']))) != sizeof($s_ids)) {
				unset($shipping_rates[$sh_id]);
			}
		}
	}
	
	// Get suppliers and determine what shipping methods applicable to them
	$suppliers = array();
	foreach ($supplier_products as $s_id => $p_ids) {
		if (!empty($s_id)) {
			$s_data = fn_get_company_data($s_id);
			$cart['use_suppliers'] = true;
		} else {
			$s_data = array(
				'company' => Registry::get('settings.Company.company_name')
			);
		}

		$suppliers[$s_id] = array (
			'company' => $s_data['company'],
			'products' => $p_ids,
			'rates' => array()
		);

		// Get shipping methods
		foreach ($shipping_rates as $sh_id => $shipping) {
			if (isset($shipping['rates'][$s_id])) {
				$shipping['rate'] = $shipping['rates'][$s_id];
				unset($shipping['rates']);
				$suppliers[$s_id]['rates'][$sh_id] = $shipping;
			}
		}
	}

	// Select shipping for each supplier
	$cart_shipping = !empty($cart['shipping']) ? $cart['shipping'] : array();
	$cart['shipping'] = array();
	foreach ($suppliers as $s_id => $supplier) {
		
		if (!empty($supplier['products']) && is_array($supplier['products']) && $s_id === 0) {
			$all_edp_no_shipping = true;
			foreach ($supplier['products'] as $pcart_id) {
				$all_edp_no_shipping = $all_edp_no_shipping && ($cart_products[$pcart_id]['is_edp'] == "Y" && $cart_products[$pcart_id]['edp_shipping'] == "N");
				
			}
			$suppliers[$s_id]['all_edp_no_shipping'] = $all_edp_no_shipping;
		}

		if (empty($supplier['rates'])) {
			if (!empty($supplier['products']) && is_array($supplier['products'])) {
				foreach ((array)$supplier['products'] as $pcart_id) {
					if ($cart_products[$pcart_id]['free_shipping'] != "Y" && ($cart_products[$pcart_id]['is_edp'] != "Y" || ($cart_products[$pcart_id]['is_edp'] == "Y" && $cart_products[$pcart_id]['edp_shipping'] == "Y" ))) {
						$cart['shipping_failed'] = true;
						break;
					}
				}
			} else {
				$cart['shipping_failed'] = true;
			}
			continue;
		}

		$sh_ids = array_keys($supplier['rates']);
		$shipping_selected = false;

		// Check if shipping method from this supplier is selected
		foreach ($sh_ids as $sh_id) {
			if (isset($cart_shipping[$sh_id]) && isset($cart_shipping[$sh_id]['rates'][$s_id])) {
				if ($shipping_selected == false) {
					if (!isset($cart['shipping'][$sh_id])) {
						$cart['shipping'][$sh_id] = $cart_shipping[$sh_id];
					}
					$cart['shipping'][$sh_id]['rates'][$s_id] = $supplier['rates'][$sh_id]['rate']; // set new rate
					$shipping_selected = true;
				} else {
					//unset($cart['shipping'][$sh_id]['rates'][$s_id]);
				}
			}
		}

		if ($shipping_selected == false) {
			$sh_id = reset($sh_ids);
			if (empty($cart['shipping'][$sh_id])) {
				if (empty($cart_shipping[$sh_id])) {
					$cart['shipping'][$sh_id] = array(
						'shipping' => $supplier['rates'][$sh_id]['name'],
					);
				} else {
					$cart['shipping'][$sh_id] = $cart_shipping[$sh_id];
				}
			}

			$cart['shipping'][$sh_id]['rates'][$s_id] = $supplier['rates'][$sh_id]['rate'];
		}
	}

	// Calculate total shipping cost
	$cart['shipping_cost'] = 0;
	foreach ($cart['shipping'] as $sh_id => $shipping) {
		$cart['shipping_cost'] += array_sum($shipping['rates']);
	}

	ksort($suppliers);
	Registry::get('view')->assign('suppliers', $suppliers); // FIXME: That's bad...
	Registry::get('view')->assign('supplier_ids', array_keys($suppliers)); // FIXME: That's bad...

	return true;
}


function fn_get_company_id($table, $key, $key_id, $company_id = '')
{
	$condition = ($company_id !== '') ? db_quote(' AND company_id = ?i ', $company_id) : '';
	
	$id = db_get_field("SELECT company_id FROM ?:$table WHERE $key = ?i $condition", $key_id);
	
	return ($id !== NULL) ? $id : false;
}

function fn_check_company_id($table, $key, $key_id, $company_id = '')
{
if (!defined('COMPANY_ID')) {
		return true;
	}

	if ($company_id === '') {
		$company_id = COMPANY_ID;
	}

	$id = db_get_field("SELECT $key FROM ?:$table WHERE $key = ?i AND company_id = ?i", $key_id, $company_id);

	return (!empty($id)) ? true : false;
}

/**
 * Set company_id to actual company_id
 *
 * @param mixed $data Array with data
 */
function fn_set_company_id(&$data, $key_name = 'company_id')
{
	if (defined('COMPANY_ID')) {
		$data[$key_name] = COMPANY_ID;
	} else {
		if (!isset($data[$key_name])) {
			$data[$key_name] = 0;
		}
	}
}

function fn_get_products_companies($products)
{
	$companies = array();

	foreach ($products as $v) {
		$companies[$v['company_id']] = $v['company_id'];
	}

	return $companies;
}

function fn_companies_suppliers_order_notification($order_info, $order_statuses, $force_notification)
{
	static $notification_sent = array();

	if ((!empty($notification_sent[$order_info['order_id']][$order_info['status']]) && $notification_sent[$order_info['order_id']][$order_info['status']]) || $order_info['status'] == STATUS_INCOMPLETED_ORDER || $order_info['status'] == STATUS_PARENT_ORDER) {
		return true;
	}

	$status_params = $order_statuses[$order_info['status']];

	$notify_supplier = isset($force_notification['S']) ? $force_notification['S'] : (!empty($status_params['notify_supplier']) && $status_params['notify_supplier'] == 'Y' ? true : false);

	if ($notify_supplier == true) {
		$notification_sent[$order_info['order_id']][$order_info['status']] = true;
		$suppliers = array();

		foreach ($order_info['items'] as $k => $v) {
			if (isset($v['company_id'])) {
				$suppliers[$v['company_id']] = 0;
			}
		}

		if (!empty($suppliers)) {
			if (!empty($order_info['shipping'])) {
				foreach ($order_info['shipping'] as $shipping_id => $shipping) {
					foreach ((array)$shipping['rates'] as $supplier_id => $rate) {
						if (isset($suppliers[$supplier_id])) {
							$suppliers[$supplier_id] += $rate;
						}
					}
				}
			}

			Registry::get('view_mail')->assign('order_status', fn_get_status_data($order_info['status'], STATUSES_ORDER, $order_info['order_id'], Registry::get('settings.Appearance.admin_default_language')));
			Registry::get('view_mail')->assign('order_info', $order_info);
			Registry::get('view_mail')->assign('status_inventory', $order_statuses[$order_info['status']]['inventory']);
			foreach ($suppliers as $supplier_id => $shipping_cost) {
				if ($supplier_id != 0) {
					Registry::get('view_mail')->assign('shipping_cost', $shipping_cost);
					Registry::get('view_mail')->assign('supplier_id', $supplier_id);

					$supplier = fn_get_company_data($supplier_id);

					fn_send_mail($supplier['email'], Registry::get('settings.Company.company_orders_department'), 'orders/supplier_notification_subj.tpl', 'orders/supplier_notification.tpl', '', Registry::get('settings.Appearance.admin_default_language'));
				}
			}

			return true;
		}
	}

	return false;
}

function fn_check_suppliers_functionality()
{
	if (PRODUCT_TYPE == 'MULTIVENDOR' || Registry::get('settings.Suppliers.enable_suppliers') == 'Y') {
		return true;
	} else {
		return false;
	}
}

function fn_get_companies_shipping_ids($company_id)
{
	$shippings = array();

	$companies_shippings = explode(',', db_get_field("SELECT shippings FROM ?:companies WHERE company_id = ?i", $company_id));
	$default_shippings = db_get_fields("SELECT shipping_id FROM ?:shippings WHERE company_id = ?i", $company_id);
	$shippings = array_merge($companies_shippings, $default_shippings);

	return $shippings;
}

function fn_check_companies_have_suppliers($companies)
{
	unset($companies[0]);
	return !empty($companies) ? 'Y' : 'N';
}

function fn_update_company($company_data, $company_id = 0, $lang_code = CART_LANGUAGE)
{
	$_data = $company_data;

	// Check if company with same email already exists
	$is_exist = db_get_field("SELECT email FROM ?:companies WHERE email = ?s AND company_id != ?i", $_data['email'], $company_id);
	if (!empty($is_exist)) {
		fn_save_post_data();
		$_text = PRODUCT_TYPE == 'MULTIVENDOR' ? 'error_vendor_exists' : 'error_supplier_exists';
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var($_text));
		return false;
	}

	$_data['shippings'] = empty($company_data['shippings']) ? '' : fn_create_set($company_data['shippings']);

	// add new company
	if (empty($company_id)) {
		// company title can't be empty
		if(empty($company_data['company'])) {
			return false;
		}

		$company_id = db_query("INSERT INTO ?:companies ?e", $_data);

		if (empty($company_id)) {
			return false;
		}

		$old_logos = array();
		
		// Adding same company descriptions for all cart languages
		$_data = array(
			'company_id' => $company_id,
			'company_description' => !empty($company_data['company_description']) ? $company_data['company_description'] : '',
		);

		if (PRODUCT_TYPE == 'MULTIVENDOR') {
			foreach ((array)Registry::get('languages') as $_data['lang_code'] => $_v) {
				db_query("INSERT INTO ?:company_descriptions ?e", $_data);
			}
		}

	// update product
	} else {
		if (isset($company_data['company']) && empty($company_data['company'])) {
			unset($company_data['company']);
		}

		db_query("UPDATE ?:companies SET ?u WHERE company_id = ?i", $_data, $company_id);

		$old_logos = db_get_field("SELECT logos FROM ?:companies WHERE company_id = ?i", $company_id);
		$old_logos = !empty($old_logos) ? unserialize($old_logos) : array();

		if (PRODUCT_TYPE == 'MULTIVENDOR') {
			// Updating company description
			$descr = !empty($company_data['company_description']) ? $company_data['company_description'] : '';
			db_query("UPDATE ?:company_descriptions SET company_description = ?s WHERE company_id = ?i AND lang_code = ?s", $descr, $company_id, DESCR_SL);
		}
	}
	// Do not upload logo if a dummy company is being added.
	if (!empty($_data['email'])) {
		fn_companies_update_logos($company_id, $old_logos);
	}
	
/*
	if (empty($product_id)) {
		$create = true;
		// product title can't be empty
		if(empty($product_data['product'])) {
			return false;
		}

		$product_id = db_query("INSERT INTO ?:products ?e", $_data);

		if (empty($product_id)) {
			return false;
		}

		//
		// Adding same product descriptions for all cart languages
		//
		$_data = $product_data;
		$_data['product_id'] =	$product_id;
		$_data['product'] = trim($_data['product'], " -");

		foreach ((array)Registry::get('languages') as $_data['lang_code'] => $_v) {
			db_query("INSERT INTO ?:product_descriptions ?e", $_data);
		}

	// update product
	} else {
		if (isset($product_data['product']) && empty($product_data['product'])) {
			unset($product_data['product']);
		}

		db_query("UPDATE ?:products SET ?u WHERE product_id = ?i", $_data, $product_id);

		$_data = $product_data;
		if (!empty($_data['product'])){
			$_data['product'] = trim($_data['product'], " -");
		}
		db_query("UPDATE ?:product_descriptions SET ?u WHERE product_id = ?i AND lang_code = ?s", $_data, $product_id, $lang_code);
	}

	// Log product add/update
	fn_log_event('products', !empty($create) ? 'create' : 'update', array(
		'product_id' => $product_id
	));

	if (!empty($product_data['product_features'])) {
		$i_data = array(
			'product_id' => $product_id,
			'lang_code' => $lang_code
		);


		foreach ($product_data['product_features'] as $feature_id => $value) {

			// Check if feature is applicable for this product
			$id_paths = db_get_fields("SELECT ?:categories.id_path FROM ?:products_categories LEFT JOIN ?:categories ON ?:categories.category_id = ?:products_categories.category_id WHERE product_id = ?i", $product_id);

			$_params = array(
				'category_ids' => array_unique(explode('/', implode('/', $id_paths))),
				'feature_id' => $feature_id
			);
			list($_feature) = fn_get_product_features($_params);

			if (empty($_feature)) {
				$_feature = db_get_field("SELECT description FROM ?:product_features_descriptions WHERE feature_id = ?i AND lang_code = ?s", $feature_id, CART_LANGUAGE);
				$_product = db_get_field("SELECT product FROM ?:product_descriptions WHERE product_id = ?i AND lang_code = ?s", $product_id, CART_LANGUAGE);
				fn_set_notification('E', fn_get_lang_var('error'), str_replace(array('[feature_name]', '[product_name]'), array($_feature, $_product), fn_get_lang_var('product_feature_cannot_assigned')));
				continue;
			}

			$i_data['feature_id'] = $feature_id;
			unset($i_data['value']);
			unset($i_data['variant_id']);
			unset($i_data['value_int']);
			$feature_type = db_get_field("SELECT feature_type FROM ?:product_features WHERE feature_id = ?i", $feature_id);

			// Delete variants in current language
			if ($feature_type == 'T') {
				db_query("DELETE FROM ?:product_features_values WHERE feature_id = ?i AND product_id = ?i AND lang_code = ?s", $feature_id, $product_id, $lang_code);
			} else {
				db_query("DELETE FROM ?:product_features_values WHERE feature_id = ?i AND product_id = ?i", $feature_id, $product_id);
			}

			if ($feature_type == 'D') {
				$i_data['value_int'] = fn_parse_date($value);
			} elseif ($feature_type == 'M') {
				if (!empty($product_data['add_new_variant'][$feature_id]['variant'])) {
					$value = empty($value) ? array() : $value;
					$value[] = fn_add_feature_variant($feature_id, $product_data['add_new_variant'][$feature_id]);
				}
				if (!empty($value)) {
					foreach ($value as $variant_id) {
						foreach (Registry::get('languages') as $i_data['lang_code'] => $_d) { // insert for all languages
							$i_data['variant_id'] = $variant_id;
							db_query("REPLACE INTO ?:product_features_values ?e", $i_data);
						}
					}
				}
				continue;
			} elseif (in_array($feature_type, array('S', 'N', 'E'))) {
				if (!empty($product_data['add_new_variant'][$feature_id]['variant'])) {
					$i_data['variant_id'] = fn_add_feature_variant($feature_id, $product_data['add_new_variant'][$feature_id]);
				
				} elseif (!empty($value) && $value != 'disable_select') {
					if ($feature_type == 'N') {
						$i_data['value_int'] = db_get_field("SELECT variant FROM ?:product_feature_variant_descriptions WHERE variant_id = ?i AND lang_code = ?s", $value, CART_LANGUAGE);
					}
					$i_data['variant_id'] = $value;
				} else {
					continue;
				}
			} else {
				if ($value == '') {
					continue;
				}
				if ($feature_type == 'O') {
					$i_data['value_int'] = $value;
				} else {
					$i_data['value'] = $value;
				}
			}

			if ($feature_type != 'T') { // feature values are common for all languages, except text (T)
				foreach (Registry::get('languages') as $i_data['lang_code'] => $_d) {
					db_query("REPLACE INTO ?:product_features_values ?e", $i_data);
				}
			} else { // for text feature, update current language only
				$i_data['lang_code'] = $lang_code;
				db_query("INSERT INTO ?:product_features_values ?e", $i_data);
			}
		}
	}

	// Update product prices
	if (isset($product_data['price'])) {
		if (!isset($product_data['prices'])) {
			$product_data['prices'] = array();
			$skip_price_delete = true;
		}
		$_price = array (
			'price' => abs($product_data['price']),
			'lower_limit' => 1,
		);

		array_unshift($product_data['prices'], $_price);
	}

	if (!empty($product_data['prices'])) {
		if (empty($skip_price_delete)) {
			db_query("DELETE FROM ?:product_prices WHERE product_id = ?i", $product_id);
		}

		foreach ($product_data['prices'] as $v) {
			if (!empty($v['lower_limit'])) {
				$v['product_id'] = $product_id;
				db_query("REPLACE INTO ?:product_prices ?e", $v);
			}
		}
	}

	if (!empty($product_data['popularity'])) {
		$_data = array (
			'product_id' => $product_id,
			'total' => intval($product_data['popularity'])
		);
		
		db_query("INSERT INTO ?:product_popularity ?e ON DUPLICATE KEY UPDATE total = ?i", $_data, $product_data['popularity']);
	}

	fn_set_hook('update_product', $product_data, $product_id, $lang_code);
*/
	return $company_id;
}

function fn_companies_get_manifest_definition()
{
	$manifest_definition = fn_get_manifest_definition();

	$available_areas = array('C', 'M', 'A');
	
	foreach ($manifest_definition as $area => $v) {
		if (!in_array($area, $available_areas)) {
			unset($manifest_definition[$area]);
		}
	}

	return $manifest_definition;
}

function fn_companies_update_logos($company_id, $old_logos)
{
	$logotypes = fn_filter_uploaded_data('logotypes');

	$areas = fn_companies_get_manifest_definition();

	// Update company logotypes
	if (!empty($logotypes)) {
		$logos = $old_logos;
		foreach ($logotypes as $type => $logo) {
			$area = $areas[$type];

			$short_name = "company/{$company_id}/{$type}_{$logo['name']}";
			$filename = DIR_IMAGES . $short_name;
			fn_mkdir(dirname($filename));

			if (fn_copy($logo['path'], $filename)) {
				list($w, $h, ) = fn_get_image_size($filename);

				$logos[$area['name']] = array(
					'vendor' => 1,
					'filename' => $short_name,
					'width' => $w,
					'height' => $h,
				);
			} else {
				$text = fn_get_lang_var('text_cannot_create_file');
				$text = str_replace('[file]', $filename, $text);
				fn_set_notification('E', fn_get_lang_var('error'), $text);
			}
			@unlink($logo['path']);
		}
		$logos = serialize($logos);
		db_query("UPDATE ?:companies SET logos = ?s WHERE company_id = ?i", $logos, $company_id);
	}

	fn_save_logo_alt($areas, $company_id);
}

function fn_delete_company($company_id)
{
	if (empty($company_id)) {
		return false;
	}
	//TODO log_event
	// Log user deletion
	/*fn_log_event('companies', 'delete', array (
		'company_id' => $company_id
	));*/
	$condition = fn_get_company_condition('company_id');
	$company_id = db_get_field("SELECT company_id FROM ?:companies WHERE 1 $condition AND company_id = ?i", $company_id);
	if (empty($company_id)) {
		return false;
	}
	
	db_query("DELETE FROM ?:companies WHERE company_id = ?i", $company_id);

	// deleting products
	$product_ids = db_get_fields("SELECT product_id FROM ?:products WHERE company_id = ?i", $company_id);
	foreach ($product_ids as $product_id) {
		fn_delete_product($product_id);
	}

	// deleting shipping
	$shipping_ids = db_get_fields("SELECT shipping_id FROM ?:shippings WHERE company_id = ?i", $company_id);
	foreach ($shipping_ids as $shipping_id) {
		fn_delete_shipping($shipping_id);
	}
	
	if (PRODUCT_TYPE == 'MULTIVENDOR') {
		db_query("DELETE FROM ?:company_descriptions WHERE company_id = ?i", $company_id);

		// deleting product_options
		$option_ids = db_get_fields("SELECT option_id FROM ?:product_options WHERE company_id = ?i", $company_id);
		foreach ($option_ids as $option_id) {
			fn_delete_product_option($option_id);
		}

		// deleting orders
		$order_ids = db_get_fields("SELECT order_id FROM ?:orders WHERE company_id = ?i", $company_id);
		foreach ($order_ids as $order_id) {
		 fn_delete_order($order_id);
		}

		// deleting users
		$user_ids = db_get_fields("SELECT user_id FROM ?:users WHERE company_id = ?i", $company_id);
		foreach ($user_ids as $user_id) {
			fn_delete_user($user_id);
		}

		// deleting pages
		$page_ids = db_get_fields("SELECT page_id FROM ?:pages WHERE company_id = ?i", $company_id);
		foreach ($page_ids as $page_id) {
			fn_delete_page($page_id);
		}

		// deleting promotions
		$promotion_ids = db_get_fields("SELECT promotion_id FROM ?:promotions WHERE company_id = ?i", $company_id);
		fn_delete_promotions($promotion_ids);
	}

	//db_query("UPDATE ?:orders SET user_id = 0 WHERE company_id = ?i", $company_id);

	fn_set_hook('delete_company', $company_id);

	return true;
}

?>