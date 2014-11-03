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
// $Id: fn.catalog.php 10558 2010-08-31 12:50:29Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

// ------------------------- 'Products' object functions ------------------------------------

//
// Get full product data by its id
//
function fn_get_product_data($product_id, &$auth, $lang_code = CART_LANGUAGE, $field_list = '', $get_add_pairs = true, $get_main_pair = true, $get_taxes = true, $get_qty_discounts = false, $preview = false)
{
	if (!empty($product_id)) {

		if (empty($field_list)) {
			$descriptions_list = "?:product_descriptions.*";
			$field_list = "?:products.*, $descriptions_list";
		}
		$field_list .= ", MIN(?:product_prices.price) as price";
		$field_list .= ", GROUP_CONCAT(IF(?:products_categories.link_type = 'M', CONCAT(?:products_categories.category_id, 'M'), ?:products_categories.category_id)) as category_ids";
		$field_list .= ", popularity.total as popularity";

		$price_usergroup = db_quote(" AND ?:product_prices.usergroup_id IN (?n)", ((AREA == 'A' && !defined('ORDER_MANAGEMENT')) ? USERGROUP_ALL : array_merge(array(USERGROUP_ALL), $auth['usergroup_ids'])));

		$_p_statuses = array('A', 'H');
		$_c_statuses = array('A', 'H');

		$avail_cond = fn_get_company_condition('?:products.company_id');
		$avail_cond .= (AREA == 'C') ? ' AND (' . fn_find_array_in_set($auth['usergroup_ids'], "?:categories.usergroup_ids", true) . ')' : '';
		$avail_cond .= (AREA == 'C') ? ' AND (' . fn_find_array_in_set($auth['usergroup_ids'], "?:products.usergroup_ids", true) . ')' : '';
		$avail_cond .= (AREA == 'C' && empty($preview)) ? db_quote(' AND ?:categories.status IN (?a) AND ?:products.status IN (?a)', $_c_statuses, $_p_statuses) : '';

		$avail_cond .= fn_get_localizations_condition('?:categories.localization');

		$condition = $join = '';

		if (AREA == 'C' && !$preview) {
			if (fn_check_suppliers_functionality()) {
				// if MVE or suppliers enabled
				$condition .= " AND (companies.status = 'A' OR ?:products.company_id = 0) ";
				$join .= " LEFT JOIN ?:companies as companies ON companies.company_id = ?:products.company_id";
			} else {
				// if suppliers disabled
				$condition .= " AND ?:products.company_id = 0 ";
			}
		}

		$join .= " INNER JOIN ?:products_categories ON ?:products_categories.product_id = ?:products.product_id INNER JOIN ?:categories ON ?:categories.category_id = ?:products_categories.category_id $avail_cond";
		$join .= " LEFT JOIN ?:product_popularity as popularity ON popularity.product_id = ?:products.product_id";

		fn_set_hook('get_product_data', $product_id, $field_list, $join, $auth, $lang_code);

		$product_data = db_get_row("SELECT $field_list FROM ?:products LEFT JOIN ?:product_prices ON ?:product_prices.product_id = ?:products.product_id AND ?:product_prices.lower_limit = 1 ?p LEFT JOIN ?:product_descriptions ON ?:product_descriptions.product_id = ?:products.product_id AND ?:product_descriptions.lang_code = ?s ?p WHERE ?:products.product_id = ?i ?p GROUP BY ?:products.product_id", $price_usergroup, $lang_code, $join, $product_id, $condition);

		if (empty($product_data)) {
			return false;
		}

		$product_data['base_price'] = $product_data['price']; // save base price (without discounts, etc...)

		$product_data['category_ids'] = fn_convert_categories($product_data['category_ids']);

		// Generate meta description automatically
		if (!empty($product_data['full_description']) && empty($product_data['meta_description']) && defined('AUTO_META_DESCRIPTION') && AREA != 'A') {
			$product_data['meta_description'] = fn_generate_meta_description($product_data['full_description']);
		}

		// If tracking with options is enabled, check if at least one combination has positive amount
		if (!empty($product_data['tracking']) && $product_data['tracking'] == 'O') {
			$product_data['amount'] = db_get_field("SELECT MAX(amount) FROM ?:product_options_inventory WHERE product_id = ?i", $product_id);
		}

		$product_data['product_id'] = $product_id;

		// Form old-style categories data FIXME!!!
		foreach ($product_data['category_ids'] as $c => $t) {
			if ($t == 'M') {
				$product_data['main_category'] = $c;
			} else {
				$product_data['add_categories'][$c] = $c;
			}
		}

		// Get main image pair
		if ($get_main_pair == true) {
			$product_data['main_pair'] = fn_get_image_pairs($product_id, 'product', 'M', true, true, $lang_code);
		}

		// Get additional image pairs
		if ($get_add_pairs == true) {
			$product_data['image_pairs'] = fn_get_image_pairs($product_id, 'product', 'A', true, true, $lang_code);
		}

		// Get taxes
		if ($get_taxes == true) {
			$product_data['taxes'] = !empty($product_data['tax_ids']) ? explode(',', $product_data['tax_ids']) : array();
		}

		// Get qty discounts
		if ($get_qty_discounts == true) {

			// For customer
			if (AREA == 'C') {
				$_prices = db_get_hash_multi_array("SELECT * FROM ?:product_prices WHERE ?:product_prices.product_id = ?i AND lower_limit > 1 AND ?:product_prices.usergroup_id IN (?n) ORDER BY lower_limit", array('usergroup_id'), $product_id, array_merge(array(USERGROUP_ALL), $auth['usergroup_ids']));
				// If customer has usergroup and prices defined for this usergroup, get them
				if (!empty($auth['usergroup_ids'])) {
					foreach ($auth['usergroup_ids'] as $ug_id) {
						if (!empty($_prices[$ug_id]) && sizeof($_prices[$ug_id]) > 0) {
							if (empty($product_data['prices'])) {
								$product_data['prices'] = $_prices[$ug_id];
							} else {
								foreach ($_prices[$ug_id] as $comp_data) {
									$add_elm = true;
									foreach ($product_data['prices'] as $price_id => $price_data) {
										if ($price_data['lower_limit'] == $comp_data['lower_limit']) {
											$add_elm = false;
											if ($price_data['price'] > $comp_data['price']) {
												$product_data['prices'][$price_id] = $comp_data;
											}
											break;
										}
									}
									if ($add_elm) {
										$product_data['prices'][] = $comp_data;
									}
								}
							}
						}
					}
					if (!empty($product_data['prices'])) {
						$tmp = array();
						foreach ($product_data['prices'] as $price_id => $price_data) {
							$tmp[$price_id] = $price_data['lower_limit'];
						}
						array_multisort($tmp, SORT_ASC, $product_data['prices']);
					}
				}
				// else, get prices for not members
				if (empty($product_data['prices']) && !empty($_prices[0]) && sizeof($_prices[0]) > 0) {
					$product_data['prices'] = $_prices[0];
				}
			// Other - get all
			} else {
				$product_data['prices'] = db_get_array("SELECT price, lower_limit, usergroup_id FROM ?:product_prices WHERE product_id = ?i ORDER BY usergroup_id, lower_limit", $product_id);
			}
		}

		// Get product features
		$path = db_get_field("SELECT id_path FROM ?:categories WHERE category_id = ?i", $product_data['main_category']);
		$_params = array(
			'category_ids' => explode('/', $path),
			'product_id' => $product_id,
			'statuses' => AREA == 'C' ? array('A') : array('A', 'H'),
			'variants' => true,
			'plain' => false,
			'display_on' => AREA == 'A' ? '' : 'product',
			'existent_only' => (AREA != 'A')
		);
		list($product_data['product_features']) = fn_get_product_features($_params, 0, $lang_code);
	} else {
		return false;
	}
	
	$product_data['detailed_params']['info_type'] = 'D';

	fn_set_hook('get_product_data_more', $product_data, $auth);

	return (!empty($product_data) ? $product_data : false);
}

//
// Get product name by its id
//
function fn_get_product_name($product_id, $lang_code = CART_LANGUAGE, $as_array = false)
{
	if (!empty($product_id)) {
		if (!is_array($product_id) && strpos($product_id, ',') !== false) {
			$product_id = explode(',', $product_id);
		}
		if (is_array($product_id) || $as_array == true) {
			return db_get_hash_single_array("SELECT product_id, product FROM ?:product_descriptions WHERE product_id IN (?n) AND lang_code = ?s", array('product_id', 'product'), $product_id, $lang_code);
		} else {
			return db_get_field("SELECT product FROM ?:product_descriptions WHERE product_id = ?i AND lang_code = ?s", $product_id, $lang_code);
		}
	}

	return false;
}

/**
 * Get product price.
 *
 * @param int $product_id
 * @param int $amount optional parameter for wholesale prices, etc...
 * @param array $auth
 * @return price
 */

function fn_get_product_price($product_id, $amount, &$auth)
{
	$usergroup_condition = db_quote("AND ?:product_prices.usergroup_id IN (?n)", ((AREA == 'C' || defined('ORDER_MANAGEMENT')) ? array_merge(array(USERGROUP_ALL), $auth['usergroup_ids']) : USERGROUP_ALL));

	$price = db_get_field("SELECT MIN(?:product_prices.price) as price FROM ?:product_prices WHERE lower_limit <=?i AND ?:product_prices.product_id = ?i ?p ORDER BY lower_limit DESC LIMIT 1", $amount, $product_id, $usergroup_condition);

	return (empty($price))? 0 : floatval($price);
}

//
// Translate products descriptions to the selected language
//
function fn_translate_products(&$products, $fields = '',$lang_code = '')
{
	if (empty($fields)) {
		$fields = 'product, short_description, full_description';
	}

	foreach ($products as $k => $v) {
		$descriptions = db_get_row("SELECT $fields FROM ?:product_descriptions WHERE product_id = ?i AND lang_code = ?s", $v['product_id'], $lang_code);
		$products[$k] = fn_array_merge($v, $descriptions, true);
	}
}

//
// Build product-prices cache
//
function fn_build_products_cache($product_ids = array(), $category_ids = array())
{
	return false; // Temporarly disabled

	$condition = ' 1 ';
	$d_condition = ' 1 ';
	if (!empty($product_ids)) {
		$condition .= db_quote(" AND b.product_id IN (?n)", $product_ids);
	}
	if (!empty($category_ids)) {
		$condition .= db_quote(" AND b.category_id IN (?n)", $category_ids);
	}

	db_query("DELETE FROM ?:products_cache as b WHERE $condition");

	$_statuses = array('A', 'H');

	$total_rows = db_get_field("SELECT COUNT(*) FROM ?:products_categories as b INNER JOIN ?:categories as a ON a.category_id = b.category_id AND a.status IN (?a) WHERE ?p", $_statuses, $condition);

	for ($i = 0; $i < $total_rows; $i = $i + 50) {
		$data = db_get_array("SELECT a.category_id, b.position, a.usergroup_ids, b.product_id, c.price, c.usergroup_id as price_usergroup_id FROM ?:categories as a INNER JOIN ?:products_categories as b ON b.category_id = a.category_id INNER JOIN ?:product_prices as c ON c.product_id = b.product_id AND c.lower_limit = 1 WHERE ?p AND a.status IN (?a) LIMIT $i, 50", $_statuses, $condition);

		foreach ($data as $k => $v) {
			if (empty($v['usergroup_ids'])) {// category usergroup is empty
				$v['usergroup_ids'] = $v['price_usergroup_id'];
			}
			$ug_ids = explode(',', $v['usergroup_ids']);
			if (!empty($v['price_usergroup_id']) && !in_array($v['price_usergroup_id'], $ug_ids)) {
				continue;
			}

			unset($v['price_usergroup_id']);
			if (empty($product_ids) && empty($category_ids)) {
				echo ". ";
				fn_flush();
			}
			db_query("INSERT INTO ?:products_cache ?e", $v);
		}
	}
}

function fn_gather_additional_product_data(&$product, $get_icon = false, $get_detailed = false, $get_options = true, $get_discounts = true, $get_features = false)
{
	$auth = & $_SESSION['auth'];

	if ($get_icon == true || $get_detailed == true) {
		if (empty($product['main_pair'])) {
			$product['main_pair'] = fn_get_image_pairs($product['product_id'], 'product', 'M', $get_icon, $get_detailed, CART_LANGUAGE);
		}
	}

	if (!isset($product['base_price'])) {
		$product['base_price'] = $product['price']; // save base price (without discounts, etc...)
	}

	// Convert product categories
	if (!empty($product['category_ids']) && !is_array($product['category_ids'])) {
		$product['category_ids'] = fn_convert_categories($product['category_ids']);
	}

	$product['selected_options'] = empty($product['selected_options']) ? array() : $product['selected_options'];

	// Get product options
	if ($get_options == true) {
		if (!isset($product['options_type']) || !isset($product['exceptions_type'])) {
			$types = db_get_row('SELECT options_type, exceptions_type FROM ?:products WHERE product_id = ?i', $product['product_id']);
			$product['options_type'] = $types['options_type'];
			$product['exceptions_type'] = $types['exceptions_type'];
		}
		
		if (empty($product['product_options'])) {
			if (!empty($product['combination'])) {
				$selected_options = fn_get_product_options_by_combination($product['combination']);
			}
			
			$product['product_options'] = (!empty($selected_options)) ?  fn_get_selected_product_options($product['product_id'], $selected_options, CART_LANGUAGE) : fn_get_product_options($product['product_id'], CART_LANGUAGE);
		}
		
		$product = fn_apply_options_rules($product);
		
		if ($get_icon == true || $get_detailed == true) {
			// Get product options images
			if (!empty($product['combination_hash'])) {
				$image = fn_get_image_pairs($product['combination_hash'], 'product_option', 'M', $get_icon, $get_detailed, CART_LANGUAGE);
				if (!empty($image)) {
					$product['main_pair'] = $image;
				}
			}
		}
		$product['has_options'] = !empty($product['product_options']);
		$product = fn_apply_exceptions_rules($product);
	} else {
		$product['has_options'] = db_get_field("SELECT COUNT(*) FROM ?:product_options as a LEFT JOIN ?:product_global_option_links as c ON c.option_id = a.option_id WHERE (a.product_id = ?i OR c.product_id = ?i) AND a.status = 'A'", $product['product_id'], $product['product_id']);
	}

	fn_set_hook('get_additional_product_data_before_discounts', $product, $auth, $get_options);

	// Get product discounts
	if ($get_discounts == true && !isset($product['exclude_from_calculate'])) {
		fn_promotion_apply('catalog', $product, $auth);
		if (!empty($product['prices']) && is_array($product['prices'])){
			$product_copy = $product;
			foreach($product['prices'] as $pr_k => $pr_v){
				$product_copy['base_price'] = $product_copy['price'] = $pr_v['price'];
				fn_promotion_apply('catalog', $product_copy, $auth);
				$product['prices'][$pr_k]['price'] = $product_copy['price'];
			}
		}

		if (empty($product['discount']) && !empty($product['list_price']) && !empty($product['price']) && floatval($product['price']) && $product['list_price'] > $product['price']) {
			$product['list_discount'] = fn_format_price($product['list_price'] - $product['price']);
			$product['list_discount_prc'] = sprintf('%d', round($product['list_discount'] * 100 / $product['list_price']));
		}
	}

	// FIXME: old product options scheme
	$product['discounts'] = array('A' => 0, 'P' => 0);
	if (!empty($product['promotions'])) {
		foreach ($product['promotions'] as $v) {
			foreach ($v['bonuses'] as $a) {
				if ($a['discount_bonus'] == 'to_fixed') {
					$product['discounts']['A'] += $a['discount'];
				} elseif ($a['discount_bonus'] == 'by_fixed') {
					$product['discounts']['A'] += $a['discount_value'];
				} elseif ($a['discount_bonus'] == 'to_percentage') {
					$product['discounts']['P'] += 100 - $a['discount_value'];
				} elseif ($a['discount_bonus'] == 'by_percentage') {
					$product['discounts']['P'] += $a['discount_value'];
				}
			}
		}
	}

	// Add product prices with taxes and without taxes
	if (!empty($product) && AREA != 'A' && Registry::get('settings.Appearance.show_prices_taxed_clean') == 'Y' && $auth['tax_exempt'] != 'Y') {
		fn_get_taxed_and_clean_prices($product, $auth);
	}

	if (!isset($product['product_features']) && $get_features == true) {
		$product['product_features'] = fn_get_product_features_list($product['product_id']);
	}

	if (!empty($product['is_edp']) && $product['is_edp'] == 'Y') {
		$product['agreement'] = array(fn_get_edp_agreements($product['product_id']));
	}

	$qty_content = array();
	if (!empty($product['qty_step'])) {
		$per_item = 0;
		if (Registry::get('settings.General.allow_negative_amount') == 'Y' && !empty($product['max_qty'])) {
			$amount = $product['max_qty'];
		} else {
			$amount = isset($product['in_stock']) ? $product['in_stock'] : (isset($product['inventory_amount']) ? $product['inventory_amount'] : $product['amount']);
		}
		for ($i = 1; $per_item <= ($amount - $product['qty_step']); $i++) {
			$per_item = $product['qty_step'] * $i;

			if (!empty($product['list_qty_count']) && ($i > $product['list_qty_count'])) {
				break;
			}

			if ((!empty($product['max_qty']) && $per_item > $product['max_qty']) || (!empty($product['min_qty']) && $per_item < $product['min_qty'])) {
				continue;
			}

			$qty_content[$i] = $per_item;
		}
	}
	$product['qty_content'] = $qty_content;
	
	$product['detailed_params'] = empty($product['detailed_params']) ? array() : $product['detailed_params'];
	$product['detailed_params'] = array_merge($product['detailed_params'], array(
		'get_icon' => $get_icon,
		'get_detailed' => $get_detailed,
		'get_options' => $get_options,
		'get_discounts' => $get_discounts,
		'get_features' => $get_features,
	));

	fn_set_hook('get_additional_product_data', $product, $auth, $get_options);
}

/**
 * Return files attached to object
 *
 * @param int $product_id ID of product
 * @param bool $preview_check get files only with preview
 * @param int $order_id get order ekeys for the files
 * @return array files
 */

function fn_get_product_files($product_id, $preview_check = false, $order_id = 0)
{
	$fields = array(
		'?:product_files.*',
		'?:product_file_descriptions.file_name',
		'?:product_file_descriptions.license',
		'?:product_file_descriptions.readme'
	);

	$join = db_quote(" LEFT JOIN ?:product_file_descriptions ON ?:product_file_descriptions.file_id = ?:product_files.file_id AND ?:product_file_descriptions.lang_code = ?s", CART_LANGUAGE);

	if (!empty($order_id)) {
		$fields[] = '?:product_file_ekeys.active';
		$fields[] = '?:product_file_ekeys.downloads';
		$fields[] = '?:product_file_ekeys.ekey';

		$join .= db_quote(" LEFT JOIN ?:product_file_ekeys ON ?:product_file_ekeys.file_id = ?:product_files.file_id AND ?:product_file_ekeys.order_id = ?i", $order_id);
		$join .= (AREA == 'C') ? " AND ?:product_file_ekeys.active = 'Y'" : '';
	}

	$condition = db_quote("WHERE ?:product_files.product_id = ?i", $product_id);

	if ($preview_check == true) {
		$condition .= " AND preview_path != ''";
	}

	if (AREA == 'C') {
		$condition .= " AND ?:product_files.status = 'A'";
	}

	$files = db_get_array("SELECT " . implode(', ', $fields) . " FROM ?:product_files ?p ?p ORDER BY position", $join, $condition);

	if (!empty($files)) {
		foreach ($files as $k => $file) {
			if (!empty($file['license']) && $file['agreement'] == 'Y') {
				$files[$k]['agreements'] = array($file);
			}
			if (!empty($file['product_id']) && !empty($file['ekey'])) {
				$files[$k]['edp_info'] = fn_get_product_edp_info($file['product_id'], $file['ekey']);
			}
		}
	}

	return $files;
}

/**
 * Return edp ekey info
 *
 * @param int $product_id
 * @param string $ekey - download key
 * @return array download key info
 */
function fn_get_product_edp_info($product_id, $ekey)
{
	$unlimited = db_get_field("SELECT unlimited_download FROM ?:products WHERE product_id = ?i", $product_id);
	$ttl_condition = ($unlimited == 'Y') ? '' :  db_quote(" AND ttl > ?i", TIME);

	return db_get_row("SELECT product_id, order_id, file_id FROM ?:product_file_ekeys WHERE product_id = ?i AND active = 'Y' AND ekey = ?s ?p", $product_id, $ekey, $ttl_condition);
}

/**
 * Return agreemetns
 *
 * @param int $product_id
 * @param bool $file_name get file name
 * @return array
 */

function fn_get_edp_agreements($product_id, $file_name = false)
{
	$join = '';
	$fields = array(
		'?:product_files.file_id',
		'?:product_files.agreement',
		'?:product_file_descriptions.license'
	);

	if ($file_name == true) {
		$join .= db_quote(" LEFT JOIN ?:product_file_descriptions ON ?:product_file_descriptions.file_id = ?:product_files.file_id AND product_file_descriptions.lang_code = ?s", CART_LANGUAGE);
		$fields[] = '?:product_file_descriptions.file_name';
	}

	return db_get_array("SELECT " . implode(', ', $fields) . " FROM ?:product_files INNER JOIN ?:product_file_descriptions ON ?:product_file_descriptions.file_id = ?:product_files.file_id AND ?:product_file_descriptions.lang_code = ?s WHERE ?:product_files.product_id = ?i AND ?:product_file_descriptions.license != '' AND ?:product_files.agreement = 'Y'", CART_LANGUAGE, $product_id);
}

//-------------------------------------- 'Categories' object functions -----------------------------

//
// Get subcategories list for current category (first-level categories only)
//
function fn_get_subcategories($category_id = '0', $lang_code = CART_LANGUAGE)
{
	$params = array (
		'category_id' => $category_id,
		'visible' => true
	);

	fn_set_hook('get_subcategories', $category_id, $params, $lang_code);

	list($categories, ) = fn_get_categories($params, $lang_code);

	return $categories;
}

//
// Get categories tree (multidimensional) from the current category
//
function fn_get_categories_tree($category_id = '0', $simple = true, $lang_code = CART_LANGUAGE)
{
	$params = array (
		'category_id' => $category_id,
		'simple' => $simple
	);

	list($categories, ) = fn_get_categories($params, $lang_code);

	return $categories;
}

//
// Get categories tree (plain) from the current category
//
function fn_get_plain_categories_tree($category_id = '0', $simple = true, $lang_code = CART_LANGUAGE)
{
	$params = array (
		'category_id' => $category_id,
		'simple' => $simple,
		'visible' => false,
		'plain' => true
	);

	list($categories, ) = fn_get_categories($params, $lang_code);

	return $categories;
}

function fn_cat_sort($a, $b)
{
	if (empty($a["position"]) && empty($b['position'])) {
		return strnatcmp($a["category"], $b["category"]);
	} else {
		return strnatcmp($a["position"], $b["position"]);
	}
}

function fn_show_picker($table, $threshold)
{
	return db_get_field("SELECT COUNT(*) FROM ?:$table") > $threshold ? true : false;
}

//
// Get categories tree beginnig from category_id
//
// Params
// @category_id - root category
// @visible - get only visible categories
// @current_category_id - current node for visible categories
// @simple - get category path as set of category IDs
// @plain - return continues list of categories
// --------------------------------------
// Examples:
// Gets whole categories tree:
// fn_get_categories()
// --------------------------------------
// Gets subcategories tree of the category:
// fn_get_categories(123)
// --------------------------------------
// Gets all first-level nodes of the category
// fn_get_categories(123, true)
// --------------------------------------
// Gets all visible nodes of the category, start from the root
// fn_get_categories(0, true, 234)

function fn_get_categories($params = array(), $lang_code = CART_LANGUAGE)
{
	$default_params = array (
		'category_id' => 0,
		'visible' => false,
		'current_category_id' => 0,
		'simple' => true,
		'plain' => false,
		'sort_order' => 'desc',
		'limit' => 0,
		'sort_by' => 'position',
		'item_ids' => '',
		'group_by_level' => true,
		'get_images' => false,
		'category_delimiter' => '/'
	);
	
	$params = array_merge($default_params, $params);

	$sortings = array (
		'timestamp' => '?:categories.timestamp',
		'name' => '?:category_descriptions.category',
		'position' => array(
			'?:categories.position',
			'?:category_descriptions.category'
		)
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	$auth = & $_SESSION['auth'];

	$fields = array (
		'?:categories.category_id',
		'?:categories.parent_id',
		'?:categories.id_path',
		'?:category_descriptions.category',
		'?:categories.position',
		'?:categories.status'
	);

	if ($params['simple'] == false) {
		$fields[] = '?:categories.product_count';
	}

	if (empty($params['current_category_id']) && !empty($params['product_category_id'])) {
		$params['current_category_id'] = $params['product_category_id'];
	}

	$condition = '';

	if (defined('COMPANY_ID')) {
		$companies = Registry::get('s_companies');
		if (!empty($companies[COMPANY_ID]['categories'])) {
			$condition .= db_quote(' AND ?:categories.category_id IN (?n)', explode(',', $companies[COMPANY_ID]['categories']));
		}
	}
		
	if (AREA == 'C') {
		$_statuses = array('A'); // Show enabled products/categories
		$condition .= fn_get_localizations_condition('?:categories.localization', true);
		$condition .= " AND (" . fn_find_array_in_set($auth['usergroup_ids'], '?:categories.usergroup_ids', true) . ")";
		$condition .= db_quote(" AND ?:categories.status IN (?a)", $_statuses);
	}

	if ($params['visible'] == true) {
		if (!empty($params['current_category_id'])) {
			$cur_id_path = db_get_field("SELECT id_path FROM ?:categories WHERE category_id = ?i", $params['current_category_id']);
			if (!empty($cur_id_path)) {
				$categories_ids = explode('/', $cur_id_path);
			}
		}
		$categories_ids[] = $params['category_id'];
		$condition .= db_quote(" AND ?:categories.parent_id IN (?n)", $categories_ids);
	}

	if (!empty($params['category_id'])) {
		$from_id_path = db_get_field("SELECT id_path FROM ?:categories WHERE category_id = ?i", $params['category_id']);
		$condition .= db_quote(" AND ?:categories.id_path LIKE ?l", "$from_id_path/%");
	}

	if (!empty($params['item_ids'])) {
		$condition .= db_quote(' AND ?:categories.category_id IN (?n)', explode(',', $params['item_ids']));
	}

	if (!empty($params['except_id']) && (empty($params['item_ids']) || !empty($params['item_ids']) && !in_array($params['except_id'], explode(',', $params['item_ids'])))) {
		$condition .= db_quote(' AND ?:categories.category_id != ?i AND ?:categories.parent_id != ?i', $params['except_id'], $params['except_id']);
	}

	if (!empty($params['period']) && $params['period'] != 'A') {
		list($params['time_from'], $params['time_to']) = fn_create_periods($params);
		$condition .= db_quote(" AND (?:categories.timestamp >= ?i AND ?:categories.timestamp <= ?i)", $params['time_from'], $params['time_to']);
	}

	$limit = $join = $group_by = '';

	if (!empty($params['b_id'])) {
		$join .= " LEFT JOIN ?:block_links ON ?:block_links.object_id = ?:categories.category_id AND ?:block_links.location = 'categories'";
		$condition .= db_quote(' AND ?:block_links.block_id = ?i', $params['b_id']);
		$params['group_by_level'] = false;
	}

	fn_set_hook('get_categories', $params, $join, $condition, $fields, $group_by, $sortings);

	if (!empty($params['limit'])) {
		$limit = db_quote(' LIMIT 0, ?i', $params['limit']);
	}

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		$params['sort_order'] = 'asc';
	}

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = 'position';
	}

	// Reverse sorting (for usage in view)
	$params['sort_order'] = ($params['sort_order'] == 'asc') ? 'desc' : 'asc';

	$sorting = (is_array($sortings[$params['sort_by']]) ? implode(' ' . $directions[$params['sort_order']] . ', ', $sortings[$params['sort_by']]) : $sortings[$params['sort_by']]) . " " . $directions[$params['sort_order']];

	$categories = db_get_hash_array('SELECT ' . implode(',', $fields) . " FROM ?:categories LEFT JOIN ?:category_descriptions ON ?:categories.category_id = ?:category_descriptions.category_id AND ?:category_descriptions.lang_code = ?s $join WHERE 1 ?p $group_by ORDER BY $sorting ?p", 'category_id', $lang_code, $condition, $limit);

	fn_set_hook('get_categories_post', $categories);

	if (empty($categories)) {
		return array(array());
	}

	$tmp = array();
	if ($params['simple'] == true || $params['group_by_level'] == true) {
		$child_for = array_keys($categories);
		$where_condition = !empty($params['except_id']) ? db_quote(' AND category_id != ?i', $params['except_id']) : '';
		$has_children = db_get_hash_array("SELECT category_id, parent_id FROM ?:categories WHERE parent_id IN(?n) ?p", 'parent_id', $child_for, $where_condition);
	}
	// Group categories by the level (simple)
	if ($params['simple'] == true) {
		foreach ($categories as $k => $v) {
			$v['level'] = substr_count($v['id_path'], '/');
			if ((!empty($params['current_category_id']) || $v['level'] == 0) && isset($has_children[$k])) {
				$v['has_children'] = $has_children[$k]['category_id'];
			}
			$tmp[$v['level']][$v['category_id']] = $v;
			if ($params['get_images'] == true) {
				$tmp[$v['level']][$v['category_id']]['main_pair'] = fn_get_image_pairs($v['category_id'], 'category', 'M', true, true, $lang_code);
			}
		}
	} elseif ($params['group_by_level'] == true) {
		// Group categories by the level (simple) and literalize path
		foreach ($categories as $k => $v) {
			$path = explode('/', $v['id_path']);
			$category_path = array();
			foreach ($path as $__k => $__v) {
				$category_path[$__v] = @$categories[$__v]['category'];
			}
			$v['category_path'] = implode($params['category_delimiter'], $category_path);
			$v['level'] = substr_count($v['id_path'], "/");
			if ((!empty($params['current_category_id']) || $v['level'] == 0) && isset($has_children[$k])) {
				$v['has_children'] = $has_children[$k]['category_id'];
			}
			$tmp[$v['level']][$v['category_id']] = $v;
			if ($params['get_images'] == true) {
				$tmp[$v['level']][$v['category_id']]['main_pair'] = fn_get_image_pairs($v['category_id'], 'category', 'M', true, true, $lang_code);
			}
		}
	} else {
		$tmp = $categories;
		if ($params['get_images'] == true) {
			foreach ($tmp as $k => $v) {
				if ($params['get_images'] == true) {
					$tmp[$k]['main_pair'] = fn_get_image_pairs($v['category_id'], 'category', 'M', true, true, $lang_code);
				}
			}
		}
	}

	ksort($tmp, SORT_NUMERIC);
	$tmp = array_reverse($tmp);

	foreach ($tmp as $level => $v) {
		foreach ($v as $k => $data) {
			if (isset($data['parent_id']) && isset($tmp[$level + 1][$data['parent_id']])) {
				$tmp[$level + 1][$data['parent_id']]['subcategories'][] = $tmp[$level][$k];
				unset($tmp[$level][$k]);
			}
		}
	}

	if ($params['group_by_level'] == true) {
		$tmp = array_pop($tmp);
	}

	if ($params['plain'] == true) {
		$tmp = fn_multi_level_to_plain($tmp, 'subcategories');
	}

	if (!empty($params['item_ids'])) {
		$tmp = fn_sort_by_ids($tmp, explode(',', $params['item_ids']), 'category_id');
	}

	if (!empty($params['add_root'])) {
		array_unshift($tmp, array('category_id' => 0, 'category' => $params['add_root']));
	}

	return array($tmp, $params);
}

function fn_sort(&$array, $key, $function)
{
	usort($array, $function);
	foreach ($array as $k => $v) {
		if (!empty($v[$key])) {
			fn_sort($array[$k][$key], $key, $function);
		}
	}
}

//
// Get full category data by its id
//
function fn_get_category_data($category_id = 0, $lang_code = CART_LANGUAGE, $field_list = '', $get_main_pair = true)
{
	if (defined('COMPANY_ID')) {
		$companies = Registry::get('s_companies');
		if (!empty($companies[COMPANY_ID]['categories'])) {
			$allowed_categories = explode(',', $companies[COMPANY_ID]['categories']);
			if (!in_array($category_id, $allowed_categories)) {
				return false;
			}
		}
	}
	
	$auth = & $_SESSION['auth'];

	$conditions = array();
	if (AREA == 'C') {
		$conditions[] = "(" . fn_find_array_in_set($auth['usergroup_ids'], '?:categories.usergroup_ids', true) . ")";
	}

	if (!empty($conditions)) {
		$conditions = 'AND '. implode(' AND ', $conditions);
	} else {
		$conditions = '';
	}

	if (empty($field_list)) {
		$descriptions_list = "?:category_descriptions.*";
		$field_list = "?:categories.*, $descriptions_list";
	}

	$join = '';

	fn_set_hook('get_category_data', $category_id, $field_list, $join, $lang_code);

	$category_data = db_get_row("SELECT $field_list FROM ?:categories LEFT JOIN ?:category_descriptions ON ?:category_descriptions.category_id = ?:categories.category_id AND ?:category_descriptions.lang_code = ?s ?p WHERE ?:categories.category_id = ?i ?p", $lang_code, $join, $category_id, $conditions);

	if (!empty($category_data)) {
		$category_data['category_id'] = $category_id;

		// Generate meta description automatically
		if (empty($category_data['meta_description']) && defined('AUTO_META_DESCRIPTION') && AREA != 'A') {
			$category_data['meta_description'] = fn_generate_meta_description($category_data['description']);
		}

		if ($get_main_pair == true) {
			$category_data['main_pair'] = fn_get_image_pairs($category_id, 'category', 'M', true, true, $lang_code);
		}
		
		if (!empty($category_data['selected_layouts'])) {
			$category_data['selected_layouts'] = unserialize($category_data['selected_layouts']);
		} else {
			$category_data['selected_layouts'] = array();
		}
	}

	fn_set_hook('get_category_data_post', $category_data);

	return (!empty($category_data) ? $category_data : false);
}

//
// Get category name by its id
//
function fn_get_category_name($category_id = 0, $lang_code = CART_LANGUAGE, $as_array = false)
{
	if (!empty($category_id)) {
		if (!is_array($category_id) && strpos($category_id, ',') !== false) {
			$category_id = explode(',', $category_id);
		}
		if (is_array($category_id) || $as_array == true) {
			return db_get_hash_single_array("SELECT category_id, category FROM ?:category_descriptions WHERE category_id IN (?n) AND lang_code = ?s", array('category_id', 'category'), $category_id, $lang_code);
		} else {
			return db_get_field("SELECT category FROM ?:category_descriptions WHERE category_id = ?i AND lang_code = ?s", $category_id, $lang_code);
		}
	}

	return false;
}

//
// Get category path by its id
//
function fn_get_category_path($category_id = 0, $lang_code = CART_LANGUAGE, $path_separator = '/')
{
	if (!empty($category_id)) {

		$id_path = db_get_field("SELECT id_path FROM ?:categories WHERE category_id = ?i", $category_id);

		$category_path = db_get_hash_single_array("SELECT category_id, category FROM ?:category_descriptions WHERE category_id IN (?n) AND lang_code = ?s", array('category_id', 'category'), explode('/', $id_path), $lang_code);

		$path = explode('/', $id_path);
		$_category_path = '';
		foreach ($path as $v) {
			$_category_path .= $category_path[$v] . $path_separator;
		}
		$_category_path = rtrim($_category_path, $path_separator);

		return (!empty($_category_path) ? $_category_path : false);
	}

	return false;
}

//
// Delete product by its id
//
function fn_delete_product($product_id)
{
	$auth = & $_SESSION['auth'];

	if (!empty($product_id)) {
		
		if (defined('COMPANY_ID')) {
			$company_id = db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i", $product_id);
			if (COMPANY_ID != $company_id) {
				fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('access_denied'));
				return false;	
			}
		}

		fn_clean_block_items('products', $product_id);
		fn_clean_block_links('products', $product_id);

		// Log product deletion
		fn_log_event('products', 'delete', array(
			'product_id' => $product_id,
		));

		$category_ids = db_get_fields("SELECT category_id FROM ?:products_categories WHERE product_id = ?i", $product_id);
    	db_query("DELETE FROM ?:products_categories WHERE product_id = ?i", $product_id);
		fn_update_product_count($category_ids);

    	db_query("DELETE FROM ?:products WHERE product_id = ?i", $product_id);
    	db_query("DELETE FROM ?:product_descriptions WHERE product_id = ?i", $product_id);
    	db_query("DELETE FROM ?:product_prices WHERE product_id = ?i", $product_id);
    	db_query("DELETE FROM ?:product_features_values WHERE product_id = ?i", $product_id);
    	db_query("DELETE FROM ?:product_options_exceptions WHERE product_id = ?i", $product_id);
    	db_query("DELETE FROM ?:product_popularity WHERE product_id = ?i", $product_id);

		fn_delete_image_pairs($product_id, 'product');

		// Delete product options and inventory records for this product
		fn_poptions_delete_product($product_id);

		// Delete product files
		fn_rm(DIR_DOWNLOADS . $product_id);

		fn_build_products_cache(array($product_id));
		// Executing delete_product functions from active addons

		fn_set_hook('delete_product', $product_id);

		$pid = db_get_field("SELECT product_id FROM ?:products WHERE product_id = ?i", $product_id);

    	return (empty($pid) ? true : false);
	} else {
		return false;
	}
}

//
// Update product count for categories
//
function fn_update_product_count($category_ids)
{
	if (!empty($category_ids)) {
		foreach($category_ids as $category_id) {
			$product_count = db_get_field("SELECT COUNT(*) FROM ?:products_categories WHERE category_id = ?i", $category_id);
			db_query("UPDATE ?:categories SET product_count = ?i WHERE category_id = ?i", $product_count, $category_id);
		}
		return true;
	}
	return false;
}

//
// Add or update category by its id
//
function fn_update_category($category_data, $category_id = 0, $lang_code = CART_LANGUAGE)
{
	// category title required
	if (empty($category_data['category'])) {
		//return false; // FIXME: management page doesn't have category name
	}

	if (isset($category_data['localization'])) {
		$category_data['localization'] = empty($category_data['localization']) ? '' : fn_implode_localizations($category_data['localization']);
	}
	if (isset($category_data['usergroup_ids'])) {
		$category_data['usergroup_ids'] = empty($category_data['usergroup_ids']) ? '' : implode(',', $category_data['usergroup_ids']);
	}
	$_data = $category_data;

	if (isset($category_data['timestamp'])) {
		$_data['timestamp'] = fn_parse_date($category_data['timestamp']);
	}

	if (empty($_data['position']) && isset($_data['parent_id'])) {
		$_data['position'] = db_get_field("SELECT max(position) FROM ?:categories WHERE parent_id = ?i", $_data['parent_id']);
		$_data['position'] = $_data['position'] + 10;
	}
	
	if (!empty($_data['selected_layouts'])) {
		$_data['selected_layouts'] = serialize($_data['selected_layouts']);
	}
	
	if (isset($_data['use_custom_templates']) && $_data['use_custom_templates'] == 'N') {
		// Clear the layout settings if the category custom templates were disabled
		$_data['product_columns'] = $_data['selected_layouts'] = $_data['default_layout'] = '';
	}
	
	// create new category
	if (empty($category_id)) {
		$create = true;
		$category_id = db_query("INSERT INTO ?:categories ?e", $_data);

		if (empty($category_id)) {
			return false;
		}


		// now we need to update 'id_path' field, as we know $category_id
		/* Generate id_path for category */
		$parent_id = intval($_data['parent_id']);
		if ($parent_id == 0) {
			$id_path = $category_id;
		} else {
			$id_path = db_get_row("SELECT id_path FROM ?:categories WHERE category_id = ?i", $parent_id);
			$id_path = $id_path['id_path'] . '/' . $category_id;
		}

		db_query('UPDATE ?:categories SET ?u WHERE category_id = ?i', array('id_path' => $id_path), $category_id);


		//
		// Adding same category descriptions for all cart languages
		//
		$_data = $category_data;
		$_data['category_id'] =	$category_id;

		foreach ((array)Registry::get('languages') as $_data['lang_code'] => $v) {
			db_query("INSERT INTO ?:category_descriptions ?e", $_data);
		}

	// update existing category
	} else {

		/* regenerate id_path for all child categories of the updated category */
		if (isset($category_data['parent_id'])) {
			fn_change_category_parent($category_id, intval($category_data['parent_id']));
		}

		db_query("UPDATE ?:categories SET ?u WHERE category_id = ?i", $_data, $category_id);
		$_data = $category_data;
		db_query("UPDATE ?:category_descriptions SET ?u WHERE category_id = ?i AND lang_code = ?s", $_data, $category_id, $lang_code);
	}

	// Log category add/update
	fn_log_event('categories', !empty($create) ? 'create' : 'update', array(
		'category_id' => $category_id
	));

	// Assign usergroup to all subcategories
	if (!empty($category_data['usergroup_to_subcats']) && $category_data['usergroup_to_subcats'] == 'Y') {
		$id_path = db_get_field("SELECT id_path FROM ?:categories WHERE category_id = ?i", $category_id);
		db_query("UPDATE ?:categories SET usergroup_ids = ?s WHERE id_path LIKE ?l", $category_data['usergroup_ids'], "$id_path/%");
	}

	if (!empty($category_data['block_id'])) {
		fn_add_items_to_block($category_data['block_id'], $category_data['add_items'], $category_id, 'categories');
	}

	fn_set_hook('update_category', $category_data, $category_id, $lang_code);

	return $category_id;

}

//
// Change category parent
//
function fn_change_category_parent($category_id, $new_parent_id)
{
	if (!empty($category_id)) {

		$new_parent_path = db_get_field("SELECT id_path FROM ?:categories WHERE category_id = ?i", $new_parent_id);
		$current_path = db_get_field("SELECT id_path FROM ?:categories WHERE category_id = ?i", $category_id);

		if (!empty($new_parent_path) && !empty($current_path)) {
			db_query("UPDATE ?:categories SET parent_id = ?i, id_path = ?s WHERE category_id = ?i", $new_parent_id, "$new_parent_path/$category_id", $category_id);
			db_query("UPDATE ?:categories SET id_path = CONCAT(?s, SUBSTRING(id_path, ?i)) WHERE id_path LIKE ?l", "$new_parent_path/$category_id/", strlen($current_path . '/') + 1, "$current_path/%");
		} elseif (empty($new_parent_path) && !empty($current_path)) {
			db_query("UPDATE ?:categories SET parent_id = ?i, id_path = ?i WHERE category_id = ?i", $new_parent_id, $category_id, $category_id);
			db_query("UPDATE ?:categories SET id_path = CONCAT(?s, SUBSTRING(id_path, ?i)) WHERE id_path LIKE ?l", "$category_id/", strlen($current_path . '/') + 1, "$current_path/%");
		}

		return true;
	}

	return false;
}


//
// Delete options and it's variants by option_id
//
function fn_delete_product_option($option_id, $pid = 0)
{
	if (!empty($option_id)) {
		$condition = fn_get_company_condition();
		$_otps = db_get_row("SELECT product_id, inventory FROM ?:product_options WHERE option_id = ?i $condition", $option_id);
		if (empty($_otps)) {
			return false;
		}
		$product_id = $_otps['product_id'];
		$option_inventory = $_otps['inventory'];
		$product_link = db_get_fields("SELECT product_id FROM ?:product_global_option_links WHERE option_id = ?i AND product_id = ?i", $option_id, $pid);
		if (empty($product_id) && !empty($product_link)) {
			// Linked option
			$option_description =  db_get_field("SELECT option_name FROM ?:product_options_descriptions WHERE option_id = ?i AND lang_code = ?s", $option_id, CART_LANGUAGE);
			db_query("DELETE FROM ?:product_global_option_links WHERE product_id = ?i AND option_id = ?i", $pid, $option_id);
			fn_set_notification('W', fn_get_lang_var('warning'), str_replace('[option_name]', $option_description, fn_get_lang_var('option_unlinked')));
		} else {
			// Product option
			$_vars = db_get_fields("SELECT variant_id FROM ?:product_option_variants WHERE option_id = ?i", $option_id);
			db_query("DELETE FROM ?:product_options_descriptions WHERE option_id = ?i", $option_id);
			db_query("DELETE FROM ?:product_options WHERE option_id = ?i", $option_id);
			fn_delete_product_option_variants($option_id);
		}

		if ($option_inventory == "Y" && !empty($product_id)) {
			$c_ids = db_get_fields("SELECT combination_hash FROM ?:product_options_inventory WHERE product_id = ?i", $product_id);
			db_query("DELETE FROM ?:product_options_inventory WHERE product_id = ?i", $product_id);
			foreach ($c_ids as $c_id) {
				fn_delete_image_pairs($c_id, 'product_option', '');
			}
		}

		fn_set_hook('delete_product_option', $option_id, $pid);

		return true;
	}
	return false;
}


//
// Delete option variants
//
function fn_delete_product_option_variants($option_id = 0, $variant_ids = array())
{
	if (!empty($option_id)) {
		$_vars = db_get_fields("SELECT variant_id FROM ?:product_option_variants WHERE option_id = ?i", $option_id);
	} elseif (!empty($variant_ids)) {
		$_vars = db_get_fields("SELECT variant_id FROM ?:product_option_variants WHERE variant_id IN (?n)", $variant_ids);
	} 

	if (!empty($_vars)) {
		foreach ($_vars as $v_id) {
			db_query("DELETE FROM ?:product_option_variants_descriptions WHERE variant_id = ?i", $v_id);
			fn_delete_image_pairs($v_id, 'variant_image');
		}

		db_query("DELETE FROM ?:product_option_variants WHERE variant_id IN (?n)", $_vars);
	}

	return true;
}

//
// Get product options
//
// @only_selectable - this flag forces to retreive the options with
// the following types only: select, radio or checkbox
//
function fn_get_product_options($product_id, $lang_code = CART_LANGUAGE, $only_selectable = false, $inventory = false, $only_avail = false)
{
	$condition = $_status = '';
	$extra_variant_fields = '';
	if (AREA == 'C' || $only_avail == true) {
		$_status .= " AND status = 'A'";
	}
	if ($only_selectable == true) {
		$condition .= " AND a.option_type IN ('S', 'R', 'C')";
	}
	if ($inventory == true) {
		$condition .= " AND a.inventory = 'Y'";
	}

	fn_set_hook('get_product_options', $extra_variant_fields, $product_id, $lang_code);

	$condition .= fn_get_company_condition('a.company_id', true, '', true);
	$_opts = db_get_hash_array("SELECT a.*, b.option_name, b.option_text, b.description, b.inner_hint, b.incorrect_message, b.comment FROM ?:product_options as a LEFT JOIN ?:product_options_descriptions as b ON a.option_id = b.option_id AND b.lang_code = ?s LEFT JOIN ?:product_global_option_links as c ON c.option_id = a.option_id WHERE (a.product_id = ?i OR c.product_id = ?i) $condition $_status ORDER BY a.position", 'option_id', $lang_code, $product_id, $product_id);

	if (is_array($_opts)) {
		$_status = (AREA == 'A') ? '' : " AND a.status='A'";
		foreach ($_opts as $k => $v) {
			$_opts[$k]['variants'] = db_get_hash_array("SELECT a.variant_id, a.position, a.modifier, a.modifier_type, a.weight_modifier, a.weight_modifier_type, $extra_variant_fields b.variant_name FROM ?:product_option_variants as a LEFT JOIN ?:product_option_variants_descriptions as b ON a.variant_id = b.variant_id AND b.lang_code = ?s WHERE a.option_id = ?i $_status ORDER BY a.position", 'variant_id', $lang_code, $v['option_id']);

			foreach ($_opts[$k]['variants'] as $_k => $_v) {
				$_opts[$k]['variants'][$_k]['image_pair'] = fn_get_image_pairs($_v['variant_id'], 'variant_image', 'V', true, true, $lang_code);
			}
		}
	}

	return $_opts;
}

/**
 * Function returns a array of product options with values by combination
 *
 * @param string $combination
 * @return array
 */

function fn_get_product_options_by_combination($combination)
{
	$options = array();

	$_comb = explode('_', $combination);
	if (!empty($_comb) && is_array($_comb)) {
		$iterations = count($_comb);
		for ($i = 0; $i < $iterations; $i += 2) {
			$options[$_comb[$i]] = $_comb[$i + 1];
		}
	}

	return $options;
}

//
// Delete all product options from the product
//
function fn_poptions_delete_product($product_id)
{

	$_opts = db_get_fields("SELECT option_id FROM ?:product_options WHERE product_id = ?i", $product_id);
	if (!fn_is_empty($_opts)) {
		foreach ($_opts as $k => $v) {
			$_vars = db_get_fields("SELECT variant_id FROM ?:product_option_variants WHERE option_id = ?i", $v);
			db_query("DELETE FROM ?:product_options_descriptions WHERE option_id = ?i", $v);
			if (!fn_is_empty($_vars)) {
				foreach ($_vars as $k1 => $v1) {
					db_query("DELETE FROM ?:product_option_variants_descriptions WHERE variant_id = ?i", $v1);
				}
				db_query("DELETE FROM ?:product_option_variants WHERE option_id = ?i", $v);
			}
		}
	}
	db_query("DELETE FROM ?:product_options WHERE product_id = ?i", $product_id);
	db_query("DELETE FROM ?:product_options_exceptions WHERE product_id = ?i", $product_id);
	db_query("DELETE FROM ?:product_options_inventory WHERE product_id = ?i", $product_id);
}

//
// Get product options with select mark
//
function fn_get_selected_product_options($product_id, $selected_options, $lang_code = CART_LANGUAGE)
{
	$extra_variant_fields = '';

	fn_set_hook('get_selected_product_options', $extra_variant_fields);

	$_opts = db_get_array("SELECT a.option_id, a.option_type, a.position, a.inventory, a.product_id, a.regexp, a.required, a.multiupload, a.allowed_extensions, a.max_file_size, b.option_name, b.option_text, b.description, b.inner_hint, b.incorrect_message, b.comment, a.status FROM ?:product_options as a LEFT JOIN ?:product_options_descriptions as b ON a.option_id = b.option_id AND b.lang_code = ?s LEFT JOIN ?:product_global_option_links as c ON c.option_id = a.option_id WHERE (a.product_id = ?i OR c.product_id = ?i) AND a.status = 'A' ORDER BY a.position", $lang_code, $product_id, $product_id);
	if (is_array($_opts)) {
		$_status = (AREA == 'A') ? '' : " AND a.status = 'A'";
		foreach ($_opts as $k => $v) {
			$_vars = db_get_hash_array("SELECT a.variant_id, a.position, a.modifier, a.modifier_type, a.weight_modifier, a.weight_modifier_type, $extra_variant_fields  b.variant_name FROM ?:product_option_variants as a LEFT JOIN ?:product_option_variants_descriptions as b ON a.variant_id = b.variant_id AND b.lang_code = ?s WHERE a.option_id = ?i $_status ORDER BY a.position", 'variant_id', $lang_code, $v['option_id']);

			$_opts[$k]['value'] = (!empty($selected_options[$v['option_id']])) ? $selected_options[$v['option_id']] : '';
			$_opts[$k]['variants'] = $_vars;
		}

	}
	return $_opts;
}

//
// Calculate product price/weight with options modifiers
//
function fn_apply_options_modifiers($product_options, $base_value, $type, $orig_options = array())
{
	$fields = ($type == 'P') ? "modifier, modifier_type" : "weight_modifier as modifier, weight_modifier_type as modifier_type";

	fn_set_hook('apply_option_modifiers', $fields, $type);

	$orig_value = $base_value;
	if (!empty($product_options)) {

		// Check options type. We need to apply only Selectbox, radiogroup and checkbox modifiers
		if (empty($orig_options)) {
			$option_types = db_get_hash_single_array("SELECT option_type as type, option_id FROM ?:product_options WHERE option_id IN (?n)", array('option_id', 'type'), array_keys($product_options));
		} else {
			$option_types = array();
			foreach ($orig_options as $_opt) {
				$option_types[$_opt['option_id']] = $_opt['option_type'];
			}
		}

		foreach ($product_options as $option_id => $variant_id) {
			if (empty($option_types[$option_id]) || strpos('SRC', $option_types[$option_id]) === false) {
				continue;
			}
			if (empty($orig_options)) {
				$_mod = db_get_row("SELECT $fields FROM ?:product_option_variants WHERE variant_id = ?i", $variant_id);
			} else {
				foreach ($orig_options as $_opt) {
					if ($_opt['value'] == $variant_id) {
						$_mod = array();
						$_mod['modifier'] = $_opt['modifier'];
						$_mod['modifier_type'] = $_opt['modifier_type'];
					}
				}
			}

			if (!empty($_mod)) {
				if ($_mod['modifier_type'] == 'A') {
					// Absolute
					if ($_mod['modifier']{0} == '-') {
						$base_value = $base_value - floatval(substr($_mod['modifier'],1));
					} else {
						$base_value = $base_value + floatval($_mod['modifier']);
					}
				} else {
					// Percentage
					if ($_mod['modifier']{0} == '-') {
						$base_value = $base_value - ((floatval(substr($_mod['modifier'],1)) * $orig_value)/100);
					} else {
						$base_value = $base_value + ((floatval($_mod['modifier']) * $orig_value)/100);
					}
				}
			}
		}
	}

	return $base_value;
}

//
// Get selected product options
//
function fn_get_selected_product_options_info($selected_options, $lang_code = CART_LANGUAGE)
{
	if (empty($selected_options) || !is_array($selected_options)) {
		return false;
	}
	$result = array();
	foreach ($selected_options as $option_id => $variant_id) {
		$_opts = db_get_row("SELECT a.option_id, a.option_type, a.inventory, b.option_name, b.option_text, b.description, b.inner_hint, b.incorrect_message FROM ?:product_options as a LEFT JOIN ?:product_options_descriptions as b ON a.option_id = b.option_id AND b.lang_code = ?s WHERE a.option_id = ?i ORDER BY a.position", $lang_code, $option_id);

		if (empty($_opts)) {
			continue;
		}
		$_vars = array();
		if (strpos('SRC', $_opts['option_type']) !== false) {
			$_vars = db_get_row("SELECT a.modifier, a.modifier_type, a.position, b.variant_name FROM ?:product_option_variants as a LEFT JOIN ?:product_option_variants_descriptions as b ON a.variant_id = b.variant_id AND b.lang_code = ?s WHERE a.variant_id = ?i ORDER BY a.position", $lang_code, $variant_id);
		}

		if ($_opts['option_type'] == 'C') {
			$_vars['variant_name'] = (empty($_vars['position'])) ? fn_get_lang_var('no') : fn_get_lang_var('yes');
		} elseif ($_opts['option_type'] == 'I' || $_opts['option_type'] == 'T') {
			$_vars['variant_name'] = $variant_id;
		} elseif (!isset($_vars['variant_name'])) {
			$_vars['variant_name'] = '';
		}

		$_vars['value'] = $variant_id;

		$result[] = fn_array_merge($_opts ,$_vars);
	}

	return $result;
}

//
// Get default product options
//
function fn_get_default_product_options($product_id, $get_all = false)
{
	$result = $default = $exceptions = array();
	$exceptions = fn_get_product_exceptions($product_id, true);
	$exceptions_type = db_get_field('SELECT exceptions_type FROM ?:products WHERE product_id = ?i', $product_id);
	$track_with_options = db_get_field("SELECT tracking FROM ?:products WHERE product_id = ?i", $product_id);
	
	$product_options = fn_get_product_options($product_id, CART_LANGUAGE, true);
	
	if (!empty($product_options)) {
		foreach ($product_options as $option_id => $option) {
			if (!empty($option['variants'])) {
				$default[$option_id] = key($option['variants']);
				foreach ($option['variants'] as $variant_id => $variant) {
					$options[$option_id][$variant_id] = true;
				}
			}
		}
	} else {
		return array();
	}
	
	unset($product_options);
	if (empty($exceptions)) {
		if ($track_with_options == 'O') {
			$combination = db_get_field("SELECT combination FROM ?:product_options_inventory WHERE product_id = ?i AND amount > 0 AND combination != '' LIMIT 1", $product_id);
			if (!empty($combination)) {
				$result = fn_get_product_options_by_combination($combination);
			}
		}
		
		if (empty($result)) {
			foreach ((array)$options as $option_id => $variants) {
				$result[$option_id] = key($variants);
			}
		}
		
		return $result;
	}
	$inventory_combinations = array();
	if ($track_with_options == 'O') {
		$inventory_combinations = db_get_array("SELECT combination FROM ?:product_options_inventory WHERE product_id = ?i AND amount > 0 AND combination != ''", $product_id);
		if (!empty($inventory_combinations)) {
			$_combinations = array();
			foreach ($inventory_combinations as $_combination) {
				$_combinations[] = fn_get_product_options_by_combination($_combination['combination']);
			}
			$inventory_combinations = $_combinations;
			unset($_combinations);
		}
	}
	if ($exceptions_type == 'F') {
		// Forbidden combinations
		$_options = array_keys($options);
		$_variants = array_values($options);
		if (!empty($_variants)) {
			foreach ($_variants as $key => $variants) {
				$_variants[$key] = array_keys($variants);
			}
		}
		
		$combinations = fn_build_options_combinations($_options, $_variants, '', 0);
		
		foreach ($combinations as $combination) {
			$allowed = true;
			foreach ($exceptions as $exception) {
				$res = array_diff($exception, $combination);
				
				if (empty($res)) {
					$allowed = false;
					break;
					
				} else {
					foreach ($res as $option_id => $variant_id) {
						if ($variant_id == -1) {
							unset($res[$option_id]);
						}
					}
					
					if (empty($res)) {
						$allowed = false;
						break;
					}
				}
			}
			
			if ($allowed) {
				$result = $combination;
				
				if (empty($inventory_combinations)) {
					break;
				} else {
					foreach ($inventory_combinations as $_icombination) {
						$_res = array_diff($_icombination, $combination);
						if (empty($_res)) {
							break 2;
						}
					}
				}
			}
		}
		
	} else {
		// Allowed combinations
		foreach ($exceptions as $exception) {
			$result = array();
			foreach ($exception as $option_id => $variant_id) {
				if (isset($options[$option_id][$variant_id]) || $variant_id == -1) {
					$result[$option_id] = ($variant_id != -1) ? $variant_id : (isset($options[$option_id]) ? key($options[$option_id]) : '');
				} else {
					continue 2;
				}
			}
			
			$_opt = array_diff_key($options, $result);
			if (!empty($_opt)) {
				foreach ($_opt as $option_id => $variants) {
					$result[$option_id] = key($variants);
				}
			}
			
			if (empty($inventory_combinations)) {
				break;
			} else {
				foreach ($inventory_combinations as $_icombination) {
					$_res = array_diff($_icombination, $result);
					if (empty($_res)) {
						break 2;
					}
				}
			}
		}
	}
	return empty($result) ? $default : $result;
}

//
// Generate product variants combinations
//
function fn_look_through_variants($product_id, $amount, $options, $variants, $string, $cycle)
{
	static $position = 0;
	
	// Look through all variants
	foreach ($variants[$cycle] as $variant_id) {
		if (count($options)-1 > $cycle) {
			$string[$cycle][$options[$cycle]] = $variant_id;
			$cycle ++;
			$combination = fn_look_through_variants($product_id, $amount, $options, $variants, $string, $cycle);
			$cycle --;
			unset($string[$cycle]);
		} else {
			$_combination = array();
			if (!empty($string)) {
				foreach ($string as $val) {
					foreach ($val as $opt => $var) {
						$_combination[$opt] = $var;
					}
				}
			}
			$_combination[$options[$cycle]] = $variant_id;
			$combination[] = $_combination;
		}
	}
	// if any combinations generated than write them to the database
	if (!empty($combination)) {
		foreach ($combination as $k => $v) {
			$_data = array();
			$_data['product_id'] = $product_id;
			$variants = $v;
			$_data['combination_hash'] = fn_generate_cart_id($product_id, array('product_options' => $variants));
			$_data['combination'] = fn_get_options_combination($v);
			$_data['position'] = $position++;
			$__amount = db_get_row("SELECT combination_hash, amount FROM ?:product_options_inventory WHERE product_id = ?i AND combination_hash = ?i AND temp = 'Y'", $product_id, $_data['combination_hash']);
			$_data['amount'] = empty($__amount) ?  $amount :  $__amount['amount'];
			db_query("REPLACE INTO ?:product_options_inventory ?e", $_data);

			echo str_repeat('. ', count($combination));
		}
	}

	return $combination;
}
//
// Check and rebuild product options inventory if necessary
//
function fn_rebuild_product_options_inventory($product_id, $amount = 50)
{
	$_options = db_get_fields("SELECT a.option_id FROM ?:product_options as a LEFT JOIN ?:product_global_option_links as b ON a.option_id = b.option_id WHERE (a.product_id = ?i OR b.product_id = ?i) AND a.option_type IN ('S','R','C') AND a.inventory = 'Y' ORDER BY position", $product_id, $product_id);
	if (empty($_options)) {
		return;
	}

	db_query("UPDATE ?:product_options_inventory SET temp = 'Y' WHERE product_id = ?i", $product_id);
	foreach ($_options as $k => $option_id) {
		$variants[$k] = db_get_fields("SELECT variant_id FROM ?:product_option_variants WHERE option_id = ?i ORDER BY position", $option_id);
	}
	$combinations = fn_look_through_variants($product_id, $amount, $_options, $variants, '', 0);

	// Delete image pairs assigned to old combinations
	$hashes = db_get_fields("SELECT combination_hash FROM ?:product_options_inventory WHERE product_id = ?i AND temp = 'Y'", $product_id);
	foreach ($hashes as $v) {
		fn_delete_image_pairs($v, 'product_option');
	}

	// Delete old combinations
	db_query("DELETE FROM ?:product_options_inventory WHERE product_id = ?i AND temp = 'Y'", $product_id);
}

function fn_get_product_features($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
	// Init filter
	$params = fn_init_view('product_features', $params);

	$default_params = array(
		'product_id' => 0,
		'category_ids' => array(),
		'statuses' => AREA == 'C' ? array('A') : array(),
		'variants' => false,
		'plain' => false,
		'all' => false,
		'feature_types' => array(),
		'feature_id' => 0,
		'display_on' => '',
		'exclude_group' => false,
		'exclude_filters' => false,
		'page' => 1
	);

	$params = array_merge($default_params, $params);

	$base_fields = $fields = array (
		'?:product_features.feature_id',
		'?:product_features.feature_type',
		'?:product_features.parent_id',
		'?:product_features.display_on_product',
		'?:product_features.display_on_catalog',
		'?:product_features_descriptions.description',
		'?:product_features_descriptions.prefix',
		'?:product_features_descriptions.suffix',
		'?:product_features.categories_path',
		'?:product_features_descriptions.full_description',
		'?:product_features.status',
		'?:product_features.comparison',
		'?:product_features.position'
	);

	$condition = $join = $group = '';

	$join .= db_quote(" LEFT JOIN ?:product_features_descriptions ON ?:product_features_descriptions.feature_id = ?:product_features.feature_id AND ?:product_features_descriptions.lang_code = ?s", $lang_code);
	$join .= db_quote(" LEFT JOIN ?:product_features AS groups ON ?:product_features.parent_id = groups.feature_id");

	$fields[] = 'groups.position AS group_position';

	if (!empty($params['product_id'])) {
		$join .= db_quote(" LEFT JOIN ?:product_features_values ON ?:product_features_values.feature_id = ?:product_features.feature_id  AND ?:product_features_values.product_id = ?i AND ?:product_features_values.lang_code = ?s", $params['product_id'], $lang_code);

		if (!empty($params['existent_only'])) {
			$condition .= db_quote(" AND IF(?:product_features.feature_type = 'G' OR ?:product_features.feature_type = 'C', 1, ?:product_features_values.feature_id)");
		}

		$fields[] = '?:product_features_values.value';
		$fields[] = '?:product_features_values.variant_id';
		$fields[] = '?:product_features_values.value_int';
	}

	if (!empty($params['feature_id'])) {
		$condition .= db_quote(" AND ?:product_features.feature_id = ?i", $params['feature_id']);
	}

	if (!empty($params['exclude_group'])) {
		$condition .= db_quote(" AND ?:product_features.feature_type != 'G'");
	}

	if (isset($params['description']) && fn_string_no_empty($params['description'])) {
		$condition .= db_quote(" AND ?:product_features_descriptions.description LIKE ?l", "%".trim($params['description'])."%");
	}

	if (!empty($params['statuses'])) {
		$condition .= db_quote(" AND ?:product_features.status IN (?a)", $params['statuses']);
	}

	if (isset($params['parent_id']) && $params['parent_id'] !== '') {
		$condition .= db_quote(" AND ?:product_features.parent_id = ?i", $params['parent_id']);
	}

	if (!empty($params['display_on']) && in_array($params['display_on'], array('product', 'catalog'))) {
		$condition .= " AND ?:product_features.display_on_$params[display_on] = 1";
	}

	if (!empty($params['feature_types'])) {
		$condition .= db_quote(" AND ?:product_features.feature_type IN (?a)", $params['feature_types']);
	}

	if (!empty($params['category_ids'])) {
		$c_ids = is_array($params['category_ids']) ? $params['category_ids'] : fn_explode(',', $params['category_ids']);
		$find_set = array(
			" ?:product_features.categories_path = '' "
		);
		foreach ($c_ids as $k => $v) {
			$find_set[] = db_quote(" FIND_IN_SET(?i, ?:product_features.categories_path) ", $v);
		}
		$find_in_set = db_quote(" AND (?p)", implode('OR', $find_set));
		$condition .= $find_in_set;
	}
	if (!empty($params['exclude_filters'])) {
		$condition .= db_quote(" AND ?:product_features.feature_id NOT IN(SELECT ?:product_filters.feature_id FROM ?:product_filters GROUP BY ?:product_filters.feature_id)");
	}
	fn_set_hook('get_product_features', $fields, $join, $condition);

	$limit = '';
	if (!empty($items_per_page)) {
		$total = db_get_field("SELECT COUNT(*) FROM ?:product_features $join WHERE 1 $condition $group ORDER BY ?:product_features.position, ?:product_features_descriptions.description");
		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}

	$data = db_get_hash_array("SELECT " . implode(', ', $fields) . " FROM ?:product_features $join WHERE 1 $condition $group ORDER BY group_position, ?:product_features.position, ?:product_features_descriptions.description $limit", 'feature_id');

	$has_ungroupped = false;
	if (!empty($data)) {
		if ($params['variants'] == true) {
			foreach ($data as $k => $v) {
				if (in_array($v['feature_type'], array('S', 'M', 'N', 'E'))) {
					$data[$k]['variants'] = fn_get_product_feature_variants($v['feature_id'], $params['product_id'], $v['feature_type'], true, $lang_code);
				}
			}
		}

		
		if ($params['plain'] == false) {
			// Get groups
			if (!empty($params['exclude_group'])) {
				foreach ($data as $k => $v) {
					if (empty($v['parent_id'])) {
						$has_ungroupped = true;
						break;
					}
				}
				$groups = db_get_hash_array("SELECT " . implode(', ', $base_fields) . " FROM ?:product_features LEFT JOIN ?:product_features_descriptions ON ?:product_features_descriptions.feature_id = ?:product_features.feature_id AND ?:product_features_descriptions.lang_code = ?s WHERE ?:product_features.feature_type = 'G' ORDER BY ?:product_features.position, ?:product_features_descriptions.description", 'feature_id', $lang_code);
				$data = fn_array_merge($data, $groups);
			}

			$delete_keys = array();
			foreach ($data as $k => $v) {
				if (!empty($v['parent_id']) && !empty($data[$v['parent_id']])) {
					$data[$v['parent_id']]['subfeatures'][$v['feature_id']] = $v;
					$data[$k] = & $data[$v['parent_id']]['subfeatures'][$v['feature_id']];
					$delete_keys[] = $k;
				}

				if (!empty($params['get_descriptions']) && empty($v['parent_id'])) {
					$d = fn_get_categories_list($v['categories_path']);
					$data[$k]['feature_description'] = fn_get_lang_var('display_on') . ': <strong>' . implode(', ', $d) . '</strong>';
				}
			}

			foreach ($delete_keys as $k) {
				unset($data[$k]);
			}
		}
	}

	return array($data, $params, $has_ungroupped);
}


function fn_get_product_feature_data($feature_id, $get_variants = false, $get_variant_images = false, $lang_code = CART_LANGUAGE)
{
	$feature_data = db_get_row("SELECT ?:product_features.feature_id, ?:product_features.feature_type, ?:product_features.parent_id, ?:product_features.display_on_product, ?:product_features.display_on_catalog, ?:product_features_descriptions.description, ?:product_features_descriptions.prefix, ?:product_features_descriptions.suffix, ?:product_features.categories_path, ?:product_features_descriptions.full_description, ?:product_features.status, ?:product_features.comparison, ?:product_features.feature_type, ?:product_features.position FROM ?:product_features LEFT JOIN ?:product_features_descriptions ON ?:product_features_descriptions.feature_id = ?:product_features.feature_id AND ?:product_features_descriptions.lang_code = ?s WHERE ?:product_features.feature_id = ?i", $lang_code, $feature_id);

	if ($get_variants == true) {
		$feature_data['variants'] = fn_get_product_feature_variants($feature_id, 0, $feature_data['feature_type'], $get_variant_images, $lang_code);
	}

	return $feature_data;
}

function fn_get_product_features_list($product_id, $display_on = 'C', $lang_code = CART_LANGUAGE)
{
	static $cache = array();
	$hash = $product_id . $display_on;

	if (!isset($cache[$hash])) {

		if ($display_on == 'C') {
			$condition = " AND f.display_on_catalog = 1";
		} elseif ($display_on == 'CP') {
			$condition = " AND (f.display_on_catalog = 1 OR f.display_on_product = 1)";
		} else {
			$condition = " AND f.display_on_product = 1";
		}

		$_data = db_get_array("SELECT v.feature_id, v.value, v.value_int, v.variant_id, f.feature_type, fd.description, fd.prefix, fd.suffix, vd.variant, f.parent_id FROM ?:product_features_values as v LEFT JOIN ?:product_features as f ON f.feature_id = v.feature_id LEFT JOIN ?:product_features_descriptions as fd ON fd.feature_id = v.feature_id AND fd.lang_code = ?s LEFT JOIN ?:product_feature_variants fv ON fv.variant_id = v.variant_id LEFT JOIN ?:product_feature_variant_descriptions as vd ON vd.variant_id = fv.variant_id AND vd.lang_code = ?s WHERE f.status = 'A' AND IF(f.parent_id, (SELECT status FROM ?:product_features as df WHERE df.feature_id = f.parent_id), 'A') = 'A' AND v.product_id = ?i ?p AND (v.variant_id != 0 OR (f.feature_type != 'C' AND v.value != '') OR (f.feature_type = 'C' AND v.value != 'N') OR v.value_int != '') AND v.lang_code = ?s ORDER BY f.position, fd.description, fv.position", $lang_code, $lang_code, $product_id, $condition, $lang_code);

		if (!empty($_data)) {
			foreach ($_data as $k => $v) {
				if ($v['feature_type'] == 'C') {
					if ($v['value'] != 'Y') {
						unset($_data[$k]);
					}
				}

				if (empty($cache[$hash][$v['feature_id']])) {
					$cache[$hash][$v['feature_id']] = $v;
				} 

				if (!empty($v['variant_id'])) { // feature has several variants
					$cache[$hash][$v['feature_id']]['variants'][$v['variant_id']] = array(
						'value' => $v['value'],
						'value_int' => $v['value_int'],
						'variant_id' => $v['variant_id'],
						'variant' => $v['variant']
					);
				}
			}

			// Sort features by group
			$groups = array();
			foreach ($cache[$hash] as $f_id => $data) {
				$groups[$data['parent_id']][$f_id] = $data;
			}

			$cache[$hash] = !empty($groups[0]) ? $groups[0] : array();
			unset($groups[0]);
			if (!empty($groups)) {
				foreach ($groups as $g) {
					$cache[$hash] = fn_array_merge($cache[$hash], $g);
				}
			}
		} else {
			$cache[$hash] = array();
		}
	}

	return $cache[$hash];
}

//
// Get available product fields
//
function fn_get_avail_product_features($lang_code = CART_LANGUAGE, $simple = false, $get_hidden = true)
{
	$statuses = array('A');

	if ($get_hidden == false) {
		$statuses[] = 'D';
	}

	if ($simple == true) {
		$fields = db_get_hash_single_array("SELECT ?:product_features.feature_id, ?:product_features_descriptions.description FROM ?:product_features LEFT JOIN ?:product_features_descriptions ON ?:product_features_descriptions.feature_id = ?:product_features.feature_id AND ?:product_features_descriptions.lang_code = ?s WHERE ?:product_features.status IN (?a) AND ?:product_features.feature_type != 'G' ORDER BY ?:product_features.position", array('feature_id', 'description'), $lang_code, $statuses);
	} else {
		$fields = db_get_hash_array("SELECT ?:product_features.*, ?:product_features_descriptions.* FROM ?:product_features LEFT JOIN ?:product_features_descriptions ON ?:product_features_descriptions.feature_id = ?:product_features.feature_id AND ?:product_features_descriptions.lang_code = ?s WHERE ?:product_features.status IN (?a) AND ?:product_features.feature_type != 'G' ORDER BY ?:product_features.position", 'feature_id', $lang_code, $statuses);
	}
	return $fields;
}

//
// Get product feature variants
//
function fn_get_product_feature_variants($feature_id, $product_id, $feature_type, $get_images = false, $lang_code = CART_LANGUAGE)
{
	$fields = array(
		'?:product_feature_variant_descriptions.*',
		'?:product_feature_variants.*',
	);

	$condition = $group_by = $sorting = '';

	$join = db_quote(" LEFT JOIN ?:product_feature_variant_descriptions ON ?:product_feature_variant_descriptions.variant_id = ?:product_feature_variants.variant_id AND ?:product_feature_variant_descriptions.lang_code = ?s", $lang_code);
	$condition .= db_quote(" AND ?:product_feature_variants.feature_id = ?i", $feature_id);
	$sorting = db_quote("?:product_feature_variants.position, ?:product_feature_variant_descriptions.variant");

	if (!empty($product_id)) {
		$fields[] = '?:product_features_values.variant_id as selected';
		$fields[] = '?:product_features.feature_type';

		$join .= db_quote(" LEFT JOIN ?:product_features_values ON ?:product_features_values.variant_id = ?:product_feature_variants.variant_id AND ?:product_features_values.lang_code = ?s AND ?:product_features_values.product_id = ?i", $lang_code, $product_id);

		$join .= db_quote(" LEFT JOIN ?:product_features ON ?:product_features.feature_id = ?:product_feature_variants.feature_id");
		$group_by = db_quote(" GROUP BY ?:product_feature_variants.variant_id");
	}

	fn_set_hook('get_product_feature_variants', $fields, $join, $condition, $group_by, $sorting, $lang_code);

	$vars = db_get_hash_array('SELECT ' . implode(', ', $fields) . " FROM ?:product_feature_variants $join WHERE 1 $condition $group_by ORDER BY $sorting", 'variant_id');

	if ($get_images == true && $feature_type == 'E') {
		foreach ($vars as $k => $v) {
			$vars[$k]['image_pair'] = fn_get_image_pairs($v['variant_id'], 'feature_variant', 'V', true, true, $lang_code);
		}
	}

	return $vars;
}

//
// Get product feature variant
//
function fn_get_product_feature_variant($variant_id, $lang_code = CART_LANGUAGE)
{
	$var = db_get_row("SELECT * FROM ?:product_feature_variants LEFT JOIN ?:product_feature_variant_descriptions ON ?:product_feature_variant_descriptions.variant_id = ?:product_feature_variants.variant_id AND ?:product_feature_variant_descriptions.lang_code = ?s WHERE ?:product_feature_variants.variant_id = ?i ORDER BY ?:product_feature_variants.position, ?:product_feature_variant_descriptions.variant", $lang_code, $variant_id);
	if (empty($var)) {
		return false;
	}
	$var['image_pair'] = fn_get_image_pairs($variant_id, 'feature_variant', 'V', true, true, $lang_code);

	if (empty($var['meta_description']) && defined('AUTO_META_DESCRIPTION') && AREA != 'A') {
		$var['meta_description'] = fn_generate_meta_description($var['description']);
	}

	return $var;
}
function fn_get_simple_product_filters($lang_code = CART_LANGUAGE)
{
	return db_get_hash_single_array("SELECT ?:product_filters.filter_id, ?:product_filter_descriptions.filter FROM ?:product_filters LEFT JOIN ?:product_filter_descriptions ON ?:product_filter_descriptions.filter_id = ?:product_filters.filter_id AND ?:product_filter_descriptions.lang_code = ?s", array('filter_id', 'filter'), $lang_code);
}


function fn_get_product_filters($params = array(), $items_per_page = 0, $lang_code = DESCR_SL)
{
	// Init filter
	$params = fn_init_view('product_filters', $params);

	// Set default values to input params
	$params['page'] = empty($params['page']) ? 1 : $params['page']; // default page is 1

	$condition = $group = '';

	if (!empty($params['filter_id'])) {
		$condition .= db_quote(" AND ?:product_filters.filter_id = ?i", $params['filter_id']);
	}

	if (isset($params['filter_name']) && fn_string_no_empty($params['filter_name'])) {
		$condition .= db_quote(" AND ?:product_filter_descriptions.filter LIKE ?l", "%".trim($params['filter_name'])."%");
	}

	if (isset($params['feature_name']) && fn_string_no_empty($params['feature_name'])) {
		$condition .= db_quote(" AND ?:product_features_descriptions.description LIKE ?l", "%".trim($params['feature_name'])."%");
	}

	if (!empty($params['category_ids'])) {
		$c_ids = is_array($params['category_ids']) ? $params['category_ids'] : fn_explode(',', $params['category_ids']);
		$find_set = array(
			" ?:product_filters.categories_path = '' "
		);
		foreach ($c_ids as $k => $v) {
			$find_set[] = db_quote(" FIND_IN_SET(?i, ?:product_filters.categories_path) ", $v);
		}
		$find_in_set = db_quote(" AND (?p)", implode('OR', $find_set));
		$condition .= $find_in_set;	
	}

	$limit = '';
	if (!empty($items_per_page)) {
		$total = db_get_field("SELECT COUNT(*) FROM ?:product_filters LEFT JOIN ?:product_filter_descriptions ON ?:product_filter_descriptions.lang_code = ?s AND ?:product_filter_descriptions.filter_id = ?:product_filters.filter_id LEFT JOIN ?:product_features_descriptions ON ?:product_features_descriptions.feature_id = ?:product_filters.feature_id AND ?:product_features_descriptions.lang_code = ?s LEFT JOIN ?:product_features ON ?:product_features.feature_id = ?:product_filters.feature_id WHERE 1 ?p", $lang_code, $lang_code, $condition);

		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}

	$filters = db_get_hash_array("SELECT ?:product_filters.*, ?:product_filter_descriptions.filter, ?:product_features.feature_type, ?:product_features.parent_id, ?:product_features_descriptions.description as feature, ?:product_features_descriptions.prefix, ?:product_features_descriptions.suffix FROM ?:product_filters LEFT JOIN ?:product_filter_descriptions ON ?:product_filter_descriptions.lang_code = ?s AND ?:product_filter_descriptions.filter_id = ?:product_filters.filter_id LEFT JOIN ?:product_features_descriptions ON ?:product_features_descriptions.feature_id = ?:product_filters.feature_id AND ?:product_features_descriptions.lang_code = ?s LEFT JOIN ?:product_features ON ?:product_features.feature_id = ?:product_filters.feature_id WHERE 1 ?p GROUP BY ?:product_filters.filter_id ORDER BY ?:product_filters.position, ?:product_filter_descriptions.filter $limit", 'filter_id', $lang_code, $lang_code, $condition);

	if (!empty($filters)) {
		$fields = fn_get_product_filter_fields();

		// Get feature group if exist
		$parent_ids = array();
		foreach ($filters as $k => $v) {
			if (!empty($v['parent_id'])) {
				$parent_ids[$v['parent_id']] = true;
			}
		}
		$groups = db_get_hash_array("SELECT feature_id, description FROM ?:product_features_descriptions WHERE feature_id IN (?n) AND lang_code = ?s", 'feature_id', array_keys($parent_ids), $lang_code);

		foreach ($filters as $k => $filter) {

			if (!empty($filter['parent_id']) && !empty($groups[$filter['parent_id']])) {
				$filters[$k]['feature_group'] = $groups[$filter['parent_id']]['description'];
			}

			if (!empty($filter['field_type'])) {
				$filters[$k]['feature'] = fn_get_lang_var($fields[$filter['field_type']]['description']);
			}
			if (empty($filter['feature_id'])) {
				$filters[$k]['condition_type'] = $fields[$filter['field_type']]['condition_type'];
			}

			if (!empty($params['get_descriptions'])) {
				$d = array();
				$filters[$k]['filter_description'] = fn_get_lang_var('filter_by') . ': <strong>' . $filters[$k]['feature'] . (!empty($filters[$k]['feature_group']) ? ' (' . $filters[$k]['feature_group'] . ' )' : '') . '</strong>';

				if ($filter['show_on_home_page'] == 'Y') {
					$d[] = fn_get_lang_var('home_page');
				}

				$d = fn_array_merge($d, fn_get_categories_list($filter['categories_path'], $lang_code), false);
				$filters[$k]['filter_description'] .= ' | ' . fn_get_lang_var('display_on') . ': <strong>' . implode(', ', $d) . '</strong>';
			}

			if (!empty($params['get_variants'])){
				$filters[$k]['ranges'] = db_get_array("SELECT ?:product_filter_ranges.*, ?:product_filter_ranges_descriptions.range_name FROM ?:product_filter_ranges LEFT JOIN ?:product_filter_ranges_descriptions ON ?:product_filter_ranges_descriptions.range_id = ?:product_filter_ranges.range_id AND ?:product_filter_ranges_descriptions.lang_code = ?s WHERE filter_id = ?i ORDER BY position", $lang_code, $filter['filter_id']);
				if (empty($filters[$k]['ranges']) && !empty($filter['feature_id']) && $filter['feature_type'] != 'N') {
					$filters[$k]['ranges'] = fn_get_product_feature_variants($filter['feature_id'], 0, $filter['feature_type']);
				}
			}
		}
	}

	return array($filters, $params);
}


function fn_get_filters_products_count($params = array())
{
	$key = 'pfilters_' . md5(serialize($params));
	Registry::register_cache($key, array('products', 'product_features', 'product_filters', 'product_features_values'), CACHE_LEVEL_USER);

	if (Registry::is_exist($key) == false) {
		if (!empty($params['check_location'])) { // FIXME: this is bad style, should be refactored
			$valid_locations = array(
				'index.index',
				'products.search',
				'categories.view',
				'product_features.view'
			);

			if (!in_array($params['dispatch'], $valid_locations)) {
				return array();
			}

			if ($params['dispatch'] == 'categories.view') {
				$params['simple_link'] = true; // this parameter means that extended filters on this page should be displayed as simple
				$params['filter_custom_advanced'] = true; // this parameter means that extended filtering should be stayed on the same page
			} else {
				if ($params['dispatch'] == 'product_features.view') {
					$params['simple_link'] = true;
					$params['features_hash'] = (!empty($params['features_hash']) ? ($params['features_hash'] . '.') : '') . 'V' . $params['variant_id'];
					//$params['exclude_feature_id'] = db_get_field("SELECT feature_id FROM ?:product_features_values WHERE variant_id = ?i", $params['variant_id']);
				}

				$params['get_for_home'] = 'Y';
			}
		}

		if (!empty($params['skip_if_advanced']) && !empty($params['advanced_filter']) && $params['advanced_filter'] == 'Y') {
			return array();
		}

		// Base fields for the SELECT queries
		$values_fields = array (
			'?:product_features_values.feature_id',
			'COUNT(DISTINCT ?:products.product_id) as products',
			'?:product_features_values.variant_id as range_id',
			'?:product_feature_variant_descriptions.variant as range_name',
			'?:product_features.feature_type',
			'?:product_filters.filter_id'
		);

		$ranges_fields = array (
			'?:product_features_values.feature_id',
			'COUNT(DISTINCT ?:products.product_id) as products',
			'?:product_filter_ranges.range_id',
			'?:product_filter_ranges_descriptions.range_name',
			'?:product_filter_ranges.filter_id',
			'?:product_features.feature_type'
		);

		$condition = $where = $join = $filter_vq = $filter_rq = '';

		$variants_ids = $ranges_ids = $field_filters = $feature_ids = $field_ranges_ids = $field_ranges_counts = array();

		if (!empty($params['features_hash'])) {
			list($variants_ids, $ranges_ids, $_field_ranges_ids) = fn_parse_features_hash($params['features_hash']);
			$field_ranges_ids = array_flip($_field_ranges_ids);
		}

		if (!empty($params['category_id'])) {
			$id_path = db_get_field("SELECT id_path FROM ?:categories WHERE category_id = ?i", $params['category_id']);
			$category_ids = db_get_fields("SELECT category_id FROM ?:categories WHERE id_path LIKE ?l", $id_path . '/%');
			$category_ids[] = $params['category_id'];
			
			$condition .= db_quote(" AND (categories_path = '' OR FIND_IN_SET(?i, categories_path))", $params['category_id']);

			$where .= db_quote(" AND ?:products_categories.category_id IN (?n)", $category_ids);
		} elseif (empty($params['get_for_home']) && empty($params['get_custom'])) {
			$condition .= " AND categories_path = ''";
		}

		if (!empty($params['filter_id'])) {
			$condition .= db_quote(" AND ?:product_filters.filter_id = ?i", $params['filter_id']);
		}

		if (!empty($params['get_for_home'])) {
			$condition .= db_quote(" AND ?:product_filters.show_on_home_page = ?s", $params['get_for_home']);
		}

		if (!empty($params['exclude_feature_id'])) {
			$condition .= db_quote(" AND ?:product_filters.feature_id NOT IN (?n)", $params['exclude_feature_id']);
		}

		$filters = db_get_hash_array("SELECT ?:product_filters.feature_id, ?:product_filters.filter_id, ?:product_filters.field_type, ?:product_filter_descriptions.filter, ?:product_features_descriptions.prefix, ?:product_features_descriptions.suffix FROM ?:product_filters LEFT JOIN ?:product_filter_descriptions ON ?:product_filter_descriptions.filter_id = ?:product_filters.filter_id AND ?:product_filter_descriptions.lang_code = ?s LEFT JOIN ?:product_features_descriptions ON ?:product_features_descriptions.feature_id = ?:product_filters.feature_id AND ?:product_features_descriptions.lang_code = ?s WHERE ?:product_filters.status = 'A' ?p ORDER by position", 'filter_id', CART_LANGUAGE, CART_LANGUAGE, $condition);

		$fields = fn_get_product_filter_fields();

		if (empty($filters) && empty($params['advanced_filter'])) {
			return array(array(), false);
		} else {
			foreach ($filters as $k => $v) {
				if (!empty($v['feature_id'])) {
					// Feature filters
					$feature_ids[] = $v['feature_id'];
				} else {
					// Product field filters
					$_field = $fields[$v['field_type']];
					$field_filters[$v['filter_id']] = array_merge($v, $_field);
					$filters[$k]['condition_type'] = $_field['condition_type'];
				}
			}
		}
		// Variants
		if (!empty($variants_ids)) {
			$join .= " LEFT JOIN (SELECT product_id, GROUP_CONCAT(?:product_features_values.variant_id) AS simple_variants FROM ?:product_features_values WHERE lang_code = '" . CART_LANGUAGE . "' GROUP BY product_id) AS pfv_simple ON pfv_simple.product_id = ?:products.product_id";

			$where_condtions = array();
			foreach ($variants_ids as $k => $variant_id) {
				$where_condtions[] = db_quote(" FIND_IN_SET('?i', simple_variants)", $variant_id);
			}
			$where .= ' AND ' . implode(' AND ', $where_condtions);
		}
		// Ranges
		if (!empty($ranges_ids)) {
			$range_conditions = db_get_array("SELECT `from`, `to`, feature_id FROM ?:product_filter_ranges WHERE range_id IN (?n)", $ranges_ids);
			foreach ($range_conditions as $k => $condition) {
				$join .= db_quote(" LEFT JOIN ?:product_features_values as var_val_$k ON var_val_$k.product_id = ?:products.product_id AND var_val_$k.lang_code = ?s", CART_LANGUAGE);
				$where .= db_quote(" AND (var_val_$k.value_int >= ?i AND var_val_$k.value_int <= ?i AND var_val_$k.value = '' AND var_val_$k.feature_id = ?i)", $condition['from'], $condition['to'], $condition['feature_id']);
			}
		}

		if (!empty($params['filter_id']) && empty($params['view_all'])) {
			$filter_vq .= db_quote(" AND ?:product_filters.filter_id = ?i", $params['filter_id']);
			$filter_rq .= db_quote(" AND ?:product_filter_ranges.filter_id = ?i", $params['filter_id']);
		}

		if (!empty($params['view_all'])) {
			$values_fields[] = "UPPER(SUBSTRING(?:product_feature_variant_descriptions.variant, 1, 1)) AS `index`";
		}

		$_join = $join;

		// Build condition for the standart fields
		if (!empty($_field_ranges_ids)) {
			foreach ($_field_ranges_ids as $rid => $field_type) {
				$structure = $fields[$field_type];

				if ($structure['table'] !== 'products' && strpos($join, 'JOIN ?:' . $structure['table']) === false) {
					$join .= " LEFT JOIN ?:$structure[table] ON ?:$structure[table].product_id = ?:products.product_id";
				}

				if ($structure['condition_type'] == 'D') {
					$range_condition = db_get_row("SELECT `from`, `to` FROM ?:product_filter_ranges WHERE range_id = ?i", $rid);
					if (!empty($range_condition)) {
						$where .= db_quote(" AND ?:$structure[table].$structure[db_field] >= ?i AND ?:$structure[table].$structure[db_field] <= ?i", $range_condition['from'], $range_condition['to']);
					}
				} elseif ($structure['condition_type'] == 'F') {
					$where .= db_quote(" AND ?:$structure[table].$structure[db_field] = ?i", $rid);
				} elseif ($structure['condition_type'] == 'C') {
					$where .= db_quote(" AND ?:$structure[table].$structure[db_field] = ?s", ($rid == 1) ? 'Y' : 'N');
				}
				if (!empty($structure['join_params'])) {
					foreach ($structure['join_params'] as $field => $param) {
						$join .= db_quote(" AND ?:$structure[table].$field = ?s ", $param);
					}
				}
			}
		}

		// Product availability conditions
		$where .= ' AND (' . fn_find_array_in_set($_SESSION['auth']['usergroup_ids'], '?:categories.usergroup_ids', true) . ')';
		$where .= ' AND (' . fn_find_array_in_set($_SESSION['auth']['usergroup_ids'], '?:products.usergroup_ids', true) . ')';
		$where .= db_quote(" AND ?:categories.status IN (?a) AND ?:products.status IN (?a)", array('A', 'H'), array('A'));
		
		$_j = " INNER JOIN ?:products_categories ON ?:products_categories.product_id = ?:products.product_id LEFT JOIN ?:categories ON ?:categories.category_id = ?:products_categories.category_id";

		if (Registry::get('settings.General.show_out_of_stock_products') == 'N' && AREA == 'C') {
			$_j .= " LEFT JOIN ?:product_options_inventory as inventory ON inventory.product_id = ?:products.product_id";
			
			$where .= " AND IF(?:products.tracking = 'O', inventory.amount > 0, ?:products.amount > 0)";
		}

		$_join .= $_j;
		$join .= $_j;

		// Localization
		$where .= fn_get_localizations_condition('?:products.localization', true);
		$where .= fn_get_localizations_condition('?:categories.localization', true);

		$variants_counts = db_get_hash_multi_array("SELECT " . implode(', ', $values_fields) . " FROM ?:product_features_values LEFT JOIN ?:products ON ?:products.product_id = ?:product_features_values.product_id LEFT JOIN ?:product_filters ON ?:product_filters.feature_id = ?:product_features_values.feature_id LEFT JOIN ?:product_feature_variants ON ?:product_feature_variants.variant_id = ?:product_features_values.variant_id LEFT JOIN ?:product_feature_variant_descriptions ON ?:product_feature_variant_descriptions.variant_id = ?:product_feature_variants.variant_id AND ?:product_feature_variant_descriptions.lang_code = ?s LEFT JOIN ?:product_features ON ?:product_features.feature_id = ?:product_filters.feature_id ?p WHERE ?:product_features_values.feature_id IN (?n) AND ?:product_features_values.lang_code = ?s AND ?:product_features_values.variant_id ?p ?p AND ?:product_features.feature_type IN ('S', 'M', 'E') GROUP BY ?:product_features_values.variant_id ORDER BY ?:product_feature_variants.position, ?:product_feature_variant_descriptions.variant", array('filter_id', 'range_id'), CART_LANGUAGE, $join, $feature_ids, CART_LANGUAGE, $where, $filter_vq);

		$ranges_counts = db_get_hash_multi_array("SELECT " . implode(', ', $ranges_fields) . " FROM ?:product_filter_ranges LEFT JOIN ?:product_features_values ON ?:product_features_values.feature_id = ?:product_filter_ranges.feature_id AND ?:product_features_values.value_int >= ?:product_filter_ranges.from AND ?:product_features_values.value_int <= ?:product_filter_ranges.to LEFT JOIN ?:products ON ?:products.product_id = ?:product_features_values.product_id LEFT JOIN ?:product_filter_ranges_descriptions ON ?:product_filter_ranges_descriptions.range_id = ?:product_filter_ranges.range_id AND ?:product_filter_ranges_descriptions.lang_code = ?s LEFT JOIN ?:product_features ON ?:product_features.feature_id = ?:product_filter_ranges.feature_id ?p WHERE ?:product_features_values.feature_id IN (?n) AND ?:product_features_values.lang_code = ?s ?p ?p GROUP BY ?:product_filter_ranges.range_id ORDER BY ?:product_filter_ranges.position, ?:product_filter_ranges_descriptions.range_name", array('filter_id', 'range_id'), CART_LANGUAGE, $join, $feature_ids, CART_LANGUAGE, $where, $filter_rq);

		if (!empty($field_filters)) {
			// Field ranges

			foreach ($field_filters as $filter_id => $field) {

				$fields_join = $fields_where = '';

				// Dinamic ranges (price, amount etc)
				if ($field['condition_type'] == 'D') {

					$fields_join = " LEFT JOIN ?:$field[table] ON ?:$field[table].$field[db_field] >= ?:product_filter_ranges.from AND ?:$field[table].$field[db_field] <= ?:product_filter_ranges.to ";

					if (strpos($fields_join . $_join, 'JOIN ?:products ') === false) {
						$fields_join .= db_quote(" LEFT JOIN ?:products ON ?:products.product_id = ?:product_prices.product_id AND ?:product_prices.lower_limit = 1 AND ?:product_prices.usergroup_id IN (?n)", array_merge(array(USERGROUP_ALL), $_SESSION['auth']['usergroup_ids']));
					} elseif (strpos($fields_join . $_join, 'JOIN ?:product_prices ') === false) {
						$fields_join .= " LEFT JOIN ?:product_prices ON ?:product_prices.product_id = ?:products.product_id";
					}

					if ($field['table'] == 'product_prices'){
						$fields_join .= db_quote(" LEFT JOIN ?:product_prices as prices_2 ON ?:product_prices.product_id = prices_2.product_id AND ?:product_prices.price > prices_2.price AND prices_2.lower_limit = 1 AND prices_2.usergroup_id IN (?n)", array_merge(array(USERGROUP_ALL), $_SESSION['auth']['usergroup_ids']));
						
						$fields_where .= " AND prices_2.price IS NULL";
					}

					$field_ranges_counts[$filter_id] = db_get_hash_array("SELECT COUNT(DISTINCT ?:$field[table].product_id) as products, ?:product_filter_ranges.range_id, ?:product_filter_ranges_descriptions.range_name, ?:product_filter_ranges.filter_id FROM ?:product_filter_ranges LEFT JOIN ?:product_filter_ranges_descriptions ON ?:product_filter_ranges_descriptions.range_id = ?:product_filter_ranges.range_id AND ?:product_filter_ranges_descriptions.lang_code = ?s ?p WHERE ?:products.status IN ('A') AND ?:product_filter_ranges.filter_id = ?i ?p GROUP BY ?:product_filter_ranges.range_id HAVING products != 0 ORDER BY ?:product_filter_ranges.position, ?:product_filter_ranges_descriptions.range_name", 'range_id', CART_LANGUAGE, $fields_join . $_join, $filter_id, $where . $fields_where);

				// Char values (free shipping etc)
				} elseif ($field['condition_type'] == 'C') {
					$field_ranges_counts[$filter_id] = db_get_hash_array("SELECT COUNT(DISTINCT ?:$field[table].product_id) as products, ?:$field[table].$field[db_field] as range_name FROM ?:$field[table] ?p WHERE ?:products.status = 'A' ?p GROUP BY ?:$field[table].$field[db_field]", 'range_name', $join, $where);
					if (!empty($field_ranges_counts[$filter_id])) {
						foreach ($field_ranges_counts[$filter_id] as $range_key => $range) {
							$field_ranges_counts[$filter_id][$range_key]['range_name'] = $field['variant_descriptions'][$range['range_name']];
							$field_ranges_counts[$filter_id][$range_key]['range_id'] = ($range['range_name'] == 'Y') ? 1 : 0;
						}
					}
				// Fixed values (supplier etc)
				} elseif ($field['condition_type'] == 'F') {
					$field_ranges_counts[$filter_id] = db_get_hash_array("SELECT COUNT(DISTINCT ?:$field[table].product_id) as products, ?:$field[foreign_table].$field[range_name] as range_name, ?:$field[foreign_table].$field[foreign_index] as range_id FROM ?:$field[table] LEFT JOIN ?:$field[foreign_table] ON ?:$field[foreign_table].$field[foreign_index] = ?:$field[table].$field[db_field] ?p WHERE ?:products.status IN ('A') ?p GROUP BY ?:$field[table].$field[db_field]", 'range_id', $join, $where);
				}
			}
		}

		$merged = fn_array_merge($variants_counts, $ranges_counts, $field_ranges_counts);

		$view_all = array();

		foreach ($filters as $filter_id => $filter) {

			if (!empty($merged[$filter_id]) && empty($params['view_all']) || (!empty($params['filter_id']) && $params['filter_id'] != $filter_id)) {

				// Check if filter range was selected
				if (empty($filters[$filter_id]['feature_id'])) {
					$intersect = array_intersect(array_keys($merged[$filter_id]), $field_ranges_ids);
				} else {
					$intersect = array_intersect(array_keys($merged[$filter_id]), $variants_ids);
				}
				if (!empty($intersect)) {
					foreach ($merged[$filter_id] as $k => $v) {
						if (!in_array($v['range_id'], $intersect)) {
							// Unset unselected ranges
							unset($merged[$filter_id][$k]);
						}
					}
				}

				// Calculate number of ranges and compare with constant
				$count = count($merged[$filter_id]);
				if ($count > FILTERS_RANGES_MORE_COUNT && empty($params['get_all'])) {
					$merged[$filter_id] = array_slice($merged[$filter_id], 0, FILTERS_RANGES_MORE_COUNT, true);
					$filters[$filter_id]['more_cut'] = true;
				}
				$filters[$filter_id]['ranges'] = & $merged[$filter_id];

				// Add feature type to the filter
				$_first = reset($merged[$filter_id]);
				if (!empty($_first['feature_type'])) {
					$filters[$filter_id]['feature_type'] = $_first['feature_type'];
				}

				if (!empty($params['simple_link']) && $filters[$filter_id]['feature_type'] == 'E') {
					$filters[$filter_id]['simple_link'] = true;
				}

				if (empty($params['skip_other_variants'])) {
					foreach ($filters[$filter_id]['ranges'] as $_k => $r) {
						if (!fn_check_selected_filter($r['range_id'], !empty($r['feature_type']) ? $r['feature_type'] : '', $params, $filters[$filter_id]['field_type'])) { // selected variant
				
							$filters[$filter_id]['ranges'] = array( // remove all obsolete ranges
								$_k => $r
							);
							$filters[$filter_id]['ranges'][$_k]['selected'] = true; // mark selected variant

							// Get other variants
							$_params = $params;
							$_params['filter_id'] = $filter_id;
							$_params['req_range_id'] = $r['range_id'];
							$_params['features_hash'] =  fn_delete_range_from_url($params['features_hash'], $r, $filters[$filter_id]['field_type']);
							$_params['skip_other_variants'] = true;
							unset($_params['variant_id'], $_params['check_location']);

							list($_f) = fn_get_filters_products_count($_params);
							if (!empty($_f)) {
								$_f = reset($_f);
								// delete current range
								foreach ($_f['ranges'] as $_rid => $_rv) {
									if ($_rv['range_id'] == $r['range_id']) {
										unset($_f['ranges'][$_rid]);
										break;
									}
								}
								$filters[$filter_id]['other_variants'] = $_f['ranges'];
							}
							break;
						}
					}
				} else {
					if (!empty($params['variant_id']) && !empty($filters[$filter_id]['ranges'][$params['variant_id']])) {
						$filters[$filter_id]['ranges'][$params['variant_id']]['selected'] = true; // mark selected variant
					}
				}

				continue;
				// If its "view all" page, return all ranges
			} elseif (!empty($params['filter_id']) && $params['filter_id'] == $filter_id && !empty($merged[$filter_id])) {
				foreach ($merged[$filter_id] as $range) {
					if (!empty($range['index'])) { // feature
						$view_all[$range['index']][] = $range;
					} else { // custom range
						$view_all[$filters[$range['filter_id']]['filter']][] = $range;
					}
				}
				ksort($view_all);
			}
			// Unset filter if he is empty
			unset($filters[$filter_id]);
		}

		if (!empty($params['advanced_filter'])) {
			$_params = array(
				'feature_types' => array('C', 'T'),
				'plain' => true,
				'category_ids' => array(empty($params['category_id']) ? 0 : $params['category_id'])
			);
			list($features) = fn_get_product_features($_params);

			if (!empty($features)) {
				$filters = array_merge($filters, $features);
			}
		}

		Registry::set($key, array($filters, $view_all));
	} else {
		list($filters, $view_all) = Registry::get($key);
	}

	return array($filters, $view_all);
}

/**
 * Function check - selected filter or unselected
 *
 * @param int $element_id element from filter
 * @param string $feature_type feature type
 * @param array $request_params request array
 * @param string $field_type type of product field (A - amount, P - price, etc)
 * @return bool true if filter selected or false otherwise
 */

function fn_check_selected_filter($element_id, $feature_type = '', $request_params = array(), $field_type = '')
{
	$prefix = empty($field_type) ? (in_array($feature_type, array('N', 'O', 'D')) ? 'R' : 'V') : $field_type;

	if (empty($request_params['features_hash']) && empty($request_params['req_range_id'])) {
		return true;
	}

	if (!empty($request_params['req_range_id']) && $request_params['req_range_id'] == $element_id) {
		return false;
	} else {
		$_tmp = explode('.', $request_params['features_hash']);
		if (in_array($prefix . $element_id, $_tmp)) {
			return false;
		}
	}

	return true;
}

/**
 * Delete range from url (example - delete "R2" from "R2.V2.V11" - result "R2.V11")
 *
 * @param string $url url from wich will delete
 * @param array $range deleted element
 * @param string $field_type type of product field (A - amount, P - price, etc)
 * @return string
 */

function fn_delete_range_from_url($url, $range, $field_type = '')
{
	$prefix = empty($field_type) ? (in_array($range['feature_type'], array('N', 'O', 'D')) ? 'R' : 'V') : $field_type;

	$element = $prefix . $range['range_id'];
	$pattern = '/(' . $element . '[\.]?)|([\.]?' . $element . ')(?![\d]+)/';

	return preg_replace($pattern, '', $url);
}

/**
 * Function add range to hash (example - add "V2" to "R23.V11.R5" - result "R23.V11.R5.V2")
 *
 * @param string $hash hash to which will be added
 * @param array $range added element
 * @param string $prefix element prefix ("R" or "V")
 * @return string new hash
 */

function fn_add_range_to_url_hash($hash, $range, $field_type = '')
{
	$prefix = empty($field_type) ? (in_array($range['feature_type'], array('N', 'O', 'D')) ? 'R' : 'V') : $field_type;
	if (empty($hash)) {
		return $prefix . $range['range_id'];
	} else {
		return $hash . '.' . $prefix . $range['range_id'];
	}
}

function fn_add_filter_ranges_breadcrumbs($request, $url = '')
{
	if (empty($request['features_hash'])) {
		return false;
	}

	$parsed_ranges = fn_parse_features_hash($request['features_hash'], false);

	if (!empty($parsed_ranges[1])) {
		$features_hash = '';
		$last_type = array_pop($parsed_ranges[1]);
		$last_range_id = array_pop($parsed_ranges[2]);

		if (!empty($parsed_ranges)) {
			foreach ($parsed_ranges[1] as $k => $v) {
				$range = fn_get_filter_range_name($v, $parsed_ranges[2][$k]);
				$features_hash = fn_add_range_to_url_hash($features_hash, array('range_id' => $parsed_ranges[2][$k]), $v);
				fn_add_breadcrumb(html_entity_decode($range), "$url&features_hash=" . $features_hash . (!empty($request['subcats']) ? '&subcats=Y' : ''));
			}
		}
		$range = fn_get_filter_range_name($last_type, $last_range_id);
		fn_add_breadcrumb(html_entity_decode($range));

	}

	return true;
}

function fn_get_filter_range_name($range_type, $range_id)
{
	static $fields;

	if (!isset($fields)) {
		$fields = fn_get_product_filter_fields();
	}

	if ($range_type == 'F') {
		$range_name = $fields['F']['variant_descriptions'][$range_id == 1 ? 'Y' : 'N'];
	} else {
		$range_name = ($range_type == 'V') ? db_get_field("SELECT variant FROM ?:product_feature_variant_descriptions WHERE variant_id = ?i AND lang_code = ?s", $range_id, CART_LANGUAGE) : db_get_field("SELECT range_name FROM ?:product_filter_ranges_descriptions WHERE range_id = ?i AND lang_code = ?s", $range_id, CART_LANGUAGE);
	}

	return fn_text_placeholders($range_name);
}

function fn_delete_product_filter($filter_id)
{
	db_query("DELETE FROM ?:product_filters WHERE filter_id = ?i", $filter_id);
	db_query("DELETE FROM ?:product_filter_descriptions WHERE filter_id = ?i", $filter_id);

	$range_ids = db_get_fields("SELECT range_id FROM ?:product_filter_ranges WHERE filter_id = ?i", $filter_id);
	foreach ($range_ids as $range_id) {
		db_query("DELETE FROM ?:product_filter_ranges_descriptions WHERE range_id = ?i", $range_id);
	}

	db_query("DELETE FROM ?:product_filter_ranges WHERE filter_id = ?i", $filter_id);

	return true;
}
function fn_parse_features_hash($features_hash = '', $values = true)
{
	if (empty($features_hash)) {
		return array();
	} else {
		$variants_ids = $ranges_ids = $fields_ids = array();
		preg_match_all('/([A-Z]+)([\d]+)[,]?/', $features_hash, $vals);

		if ($values !== true) {
			return $vals;
		}

		if (!empty($vals) && !empty($vals[1]) && !empty($vals[2])) {
			foreach ($vals[1] as $key => $range_type) {
				if ($range_type == 'V') {
					// Feature variants
					$variants_ids[] = $vals[2][$key];
				} elseif ($range_type == 'R') {
					// Feature ranges
					$ranges_ids[] = $vals[2][$key];
				} else {
					// Product field ranges
					$fields_ids[$vals[2][$key]] = $vals[1][$key];
				}
			}
		}

		$variants_ids = array_map('intval', $variants_ids);
		$ranges_ids = array_map('intval', $ranges_ids);

		return array($variants_ids, $ranges_ids, $fields_ids);
	}
}

/**
 * Function generate fields for the product filters
 * Returns array with following structure:
 *
 * code => array (
 * 		'db_field' => db_field,
 * 		'table' => db_table,
 * 		'name' => lang_var_name,
 * 		'condition_type' => condition_type
 * );
 *
 * condition_type - contains "C" - char (example, free_shipping == "Y")
 * 							 "D" - dinamic (1.23 < price < 3.45)
 * 							 "F" - fixed (supplier_id = 3)
 *
 */

function fn_get_product_filter_fields()
{
	$filters = array (
		// price filter
		'P' => array (
			'db_field' => 'price',
			'table' => 'product_prices',
			'description' => 'price',
			'condition_type' => 'D',
			'join_params' => array (
				'lower_limit' => 1
			),
			'is_range' => true,
		),
		// amount filter
		'A' => array (
			'db_field' => 'amount',
			'table' => 'products',
			'description' => 'in_stock',
			'condition_type' => 'D',
			'is_range' => true,
		),
		// filter by free shipping
		'F' => array (
			'db_field' => 'free_shipping',
			'table' => 'products',
			'description' => 'free_shipping',
			'condition_type' => 'C',
			'variant_descriptions' => array (
				'Y' => fn_get_lang_var('yes'),
				'N' => fn_get_lang_var('no')
			)
		)
	);

	fn_set_hook('get_product_filter_fields', $filters);

	return $filters;
}
//
//Gets all combinations of options stored in exceptions
//
function fn_get_product_exceptions($product_id, $short_list = false)
{
	$exceptions = db_get_array("SELECT * FROM ?:product_options_exceptions WHERE product_id = ?i ORDER BY exception_id", $product_id);

	if (!empty($exceptions)) {
		foreach ($exceptions as $k => $v) {
			$exceptions[$k]['combination'] = unserialize($v['combination']);
			
			if ($short_list) {
				$_exceptions[$k] = $exceptions[$k]['combination'];
			}
		}
		if ($short_list) {
			return $_exceptions;
		} else {
			return $exceptions;
		}
	} else {
		return array();
	}
}


//
// Returnns true if such combination already exists
//
function fn_check_combination($combinations, $product_id)
{
	$exceptions = fn_get_product_exceptions($product_id);
	if (empty($exceptions)) {
		return false;
	}
	foreach ($exceptions as $k => $v) {
		$temp = array();
		$temp = $v['combination'];
		foreach ($combinations as $key => $value) {
			if ((in_array($value, $temp)) && ($temp[$key] == $value)) {
				unset($temp[$key]);
			}
		}
		if (empty($temp)) {
			return true;
		}
	}

	return false;
}

//
// Updates options exceptions using product_id;
//
function fn_update_exceptions($product_id)
{
	if ($product_id) {
		$exceptions = fn_get_product_exceptions($product_id);
		if (!empty($exceptions)) {
			db_query("DELETE FROM ?:product_options_exceptions WHERE product_id = ?i", $product_id);
			foreach ($exceptions as $k => $v) {
				$_options_order = db_get_fields("SELECT a.option_id FROM ?:product_options as a LEFT JOIN ?:product_global_option_links as b ON a.option_id = b.option_id WHERE a.product_id = ?i OR b.product_id = ?i ORDER BY position", $product_id, $product_id);

				if (empty($_options_order)) {
					return false;
				}
				$combination  = array();

				foreach ($_options_order as $option) {
					if (!empty($v['combination'][$option])) {
						$combination[$option] = $v['combination'][$option];
					} else {
						$combination[$option] = -1;
					}
				}

				$_data = array(
					'product_id' => $product_id,
					'exception_id' => $v['exception_id'],
					'combination' => serialize($combination),
				);
				db_query("INSERT INTO ?:product_options_exceptions ?e", $_data);

			}
			return true;
		}
		return false;
	}
}

//
// Clone exceptions
//
function fn_clone_options_exceptions(&$exceptions, $old_opt_id, $old_var_id, $new_opt_id, $new_var_id)
{

	foreach ($exceptions as $key => $value) {
		foreach ($value['combination'] as $option => $variant) {
			if ($option == $old_opt_id) {
				$exceptions[$key]['combination'][$new_opt_id] = $variant;
				unset($exceptions[$key]['combination'][$option]);

				if ($variant == $old_var_id) {
					$exceptions[$key]['combination'][$new_opt_id] = $new_var_id;
				}
			}
			if ($variant == $old_var_id) {
				$exceptions[$key]['combination'][$option] = $new_var_id;
			}
		}
	}
}
//
// This function clones options to product from a product or global options
//
function fn_clone_product_options($from_product_id, $to_product_id, $from_global = false)
{
	// Get all product options assigned to the product
	$id_req = (empty($from_global)) ? db_quote('product_id = ?i', $from_product_id) : db_quote('option_id = ?i', $from_global);
	$data = db_get_array("SELECT * FROM ?:product_options WHERE $id_req");
	$linked  = db_get_field("SELECT COUNT(option_id) FROM ?:product_global_option_links WHERE product_id = ?i", $from_product_id);
	if (!empty($data) || !empty($linked)) {
		// Get all exceptions for the product
		if (!empty($from_product_id)) {
				$exceptions = fn_get_product_exceptions($from_product_id);
				$inventory = db_get_field("SELECT COUNT(*) FROM ?:product_options_inventory WHERE product_id = ?i", $from_product_id);
		}
		// Fill array of options for linked global options options
		$change_options = array();
		$change_varaiants = array();
		// If global option are linked than ids will be the same
		$change_options = db_get_hash_single_array("SELECT option_id FROM ?:product_global_option_links WHERE product_id = ?i", array('option_id', 'option_id'), $from_product_id);
		if (!empty($change_options)) {
			foreach ($change_options as $value) {
				$change_varaiants = fn_array_merge(db_get_hash_single_array("SELECT variant_id FROM ?:product_option_variants WHERE option_id = ?i", array('variant_id', 'variant_id'), $value), $change_varaiants, true);
			}
		}
		foreach ($data as $v) {
			// Clone main data
			$option_id = $v['option_id'];
			$v['product_id'] = $to_product_id;
			unset($v['option_id']);
			$new_option_id = db_query("INSERT INTO ?:product_options ?e", $v);

			// Clone descriptions
			$_data = db_get_array("SELECT * FROM ?:product_options_descriptions WHERE option_id = ?i", $option_id);
			foreach ($_data as $_v) {
				$_v['option_id'] = $new_option_id;
				db_query("INSERT INTO ?:product_options_descriptions ?e", $_v);
			}

			$change_options[$option_id] = $new_option_id;
			// Clone variants if exists
			if ($v['option_type'] == 'S' || $v['option_type'] == 'R' || $v['option_type'] == 'C') {
				$_data = db_get_array("SELECT * FROM ?:product_option_variants WHERE option_id = ?i", $option_id);
				foreach ($_data as $_v) {
					$variant_id = $_v['variant_id'];
					$_v['option_id'] = $new_option_id;
					unset($_v['variant_id']);
					$new_variant_id = db_query("INSERT INTO ?:product_option_variants ?e", $_v);
						// Clone Exceptions
					if (!empty($exceptions)) {
						fn_clone_options_exceptions($exceptions, $option_id, $variant_id, $new_option_id, $new_variant_id);
					}
						$change_varaiants[$variant_id] = $new_variant_id;
					// Clone descriptions
					$__data = db_get_array("SELECT * FROM ?:product_option_variants_descriptions WHERE variant_id = ?i", $variant_id);
					foreach ($__data as $__v) {
						$__v['variant_id'] = $new_variant_id;
						db_query("INSERT INTO ?:product_option_variants_descriptions ?e", $__v);
					}

					// Clone variant images
					fn_clone_image_pairs($new_variant_id, $variant_id, 'variant_image');
				}
				unset($_data, $__data);
			}
		}
		// Clone Inventory
		if (!empty($inventory)) {
			fn_clone_options_inventory($from_product_id, $to_product_id, $change_options, $change_varaiants);
		}
		if (!empty($exceptions)) {
			foreach ($exceptions as $k => $v) {
				$_data = array(
					'product_id' => $to_product_id,
					'combination' => serialize($v['combination']),
				);
				db_query("INSERT INTO ?:product_options_exceptions ?e", $_data);
			}
		}
	}
}

//
// Clone Inventory
//
function fn_clone_options_inventory($from_product_id, $to_product_id, $options, $variants)
{
	$inventory = db_get_array("SELECT * FROM ?:product_options_inventory WHERE product_id = ?i", $from_product_id);

	foreach ($inventory as $key => $value) {
		$_variants = explode('_', $value['combination']);
		$inventory[$key]['combination'] = '';
		foreach ($_variants as $kk => $vv) {
			if (($kk % 2) == 0 && !empty($_variants[$kk + 1])) {
				$_comb[0] = $options[$vv];
				$_comb[1] = $variants[$_variants[$kk + 1]];

				$new_variants[$kk] = $_comb[1];
				$inventory[$key]['combination'] .= implode('_', $_comb) . (!empty($_variants[$kk + 2]) ? '_' : '');
			}
		}

		$_data['product_id'] = $to_product_id;
		$_data['combination_hash'] = fn_generate_cart_id($to_product_id, array('product_options' => $new_variants));
		$_data['combination'] = rtrim($inventory[$key]['combination'], "|");
		$_data['amount'] = $value['amount'];
		$_data['product_code'] = $value['product_code'];
		db_query("INSERT INTO ?:product_options_inventory ?e", $_data);

		// Clone option images
		fn_clone_image_pairs($_data['combination_hash'], $value['combination_hash'], 'product_option', null, $to_product_id, 'product');
	}
}

// Generate url-safe filename for the object
function fn_generate_name($str, $object_type = '', $object_id = 0)
{
	$d = SEO_DELIMITER;

	// Replace umlauts with their basic latin representation
	$chars = array(
		' ' => $d,
		'\'' => '',
		'"' => '',
		'\'' => '',
		'&' => $d.'and'.$d,
		"\xc3\xa5" => 'aa',
		"\xc3\xa4" => 'ae',
		"\xc3\xb6" => 'oe',
		"\xc3\x85" => 'aa',
		"\xc3\x84" => 'ae',
		"\xc3\x96" => 'oe',
	);

	$str = html_entity_decode($str, ENT_QUOTES, 'UTF-8'); // convert html special chars back to original chars
	$str = str_replace(array_keys($chars), $chars, $str);
	
	if (!empty($str)) {
		$str = strtr($str, array("\xc4\x84" => 'A', "\xc4\x85" => 'a', "\xc3\xa1" => 'a', "\xc3\x81" => 'A', "\xc3\xa0" => 'a', "\xc3\x80" => 'A', "\xc3\xa2" => 'a', "\xc3\x82" => 'A', "\xc3\xa3" => 'a', "\xc3\x83" => 'A', "\xc2\xaa" => 'a', "\xc4\x8c" => 'C', "\xc4\x8d" => 'c', "\xc3\xa7" => 'c', "\xc3\x87" => 'C', "\xc3\xa9" => 'e', "\xc3\x89" => 'E', "\xc3\xa8" => 'e', "\xc3\x88" => 'E', "\xc3\xaa" => 'e', "\xc3\x8a" => 'E', "\xc3\xab" => 'e', "\xc3\x8b" =>'E', "\xc4\x98" => 'E', "\xc4\x99" => 'e', "\xc4\x9a" => 'E', "\xc4\x9b" => 'e', "\xc4\x8f" => 'd', "\xc3\xad" => 'i', "\xc3\x8d" => 'I', "\xc3\xac" => 'i', "\xc3\x8c" => 'I', "\xc3\xae" => 'i', "\xc3\x8e" => 'I', "\xc3\xaf" => 'i', "\xc3\x8f" => 'I', "\xc4\xb9" => 'L', "\xc4\xba" => 'l', "\xc4\xbe" => 'l', "\xc5\x87" => 'N', "\xc5\x88" => 'n', "\xc3\xb1" => 'n', "\xc3\x91" => 'N', "\xc3\xb3" => 'o', "\xc3\x93" => 'O', "\xc3\xb2" => 'o', "\xc3\x92" => 'O', "\xc3\xb4" => 'o', "\xc3\x94" => 'O', "\xc3\xb5" => 'o', "\xc3\x95" => 'O', "\xd4\xa5" => 'o', "\xc3\x98" => 'O', "\xc2\xba" => 'o', "\xc3\xb0" => 'o', "\xc5\x94" => 'R', "\xc5\x95" => 'r', "\xc5\x98" => 'R', "\xc5\x99" => 'r', "\xc5\xa0" => 'S', "\xc5\xa1" => 's', "\xc5\xa5" => 't', "\xc3\xba" => 'u', "\xc3\x9a" => 'U', "\xc3\xb9" => 'u', "\xc3\x99" => 'U', "\xc3\xbb" => 'u', "\xc3\x9b" => 'U', "\xc3\xbc" => 'u', "\xc3\x9c" => 'U', "\xc5\xae" => 'U', "\xc5\xaf" => 'u', "\xc3\xbd" => 'y', "\xc3\x9d" => 'Y', "\xc3\xbf" => 'y', "\xc3\xa6" => 'a', "\xc3\x86" => 'A', "\xc3\x9f" => 's', "\xc5\xbd" => 'Z', "\xc5\xbe" => 'z', '?' => '-', ' ' => '-', '/' => '-', '&' => '-', '(' => '-', ')' => '-', '[' => '-', ']' => '-', '%' => '-', '#' => '-', ',' => '-', ':' => '-'));

		if (!empty($object_type)) {
			$str .= $d . $object_type . $object_id;
		}

		$str = strtolower($str); // only lower letters
		$str = preg_replace("/($d){2,}/", $d, $str); // replace double (and more) dashes with one dash
		$str = preg_replace("/[^a-z0-9-\.]/", '', $str); // URL can contain latin letters, numbers, dashes and points only

		return trim($str, '-'); // remove trailing dash if exist
	}

	return false;
}

/**
 * Function construct a string in format option1_variant1_option2_variant2...
 *
 * @param array $product_options
 * @return string
 */

function fn_get_options_combination($product_options)
{
	if (empty($product_options) && !is_array($product_options)) {
		return '';
	}

	$combination = '';
	foreach ($product_options as $option => $variant) {
		$combination .= $option . '_' . $variant . '_';
	}
	$combination = trim($combination, '_');

	return $combination;
}

/*
 *  added function
 */ 
function fn_get_neighbours($product_id){
	$category[0] = $_SESSION[current_category_id];
	
	//echo $product_id;
	$moreLess = array('-2', '-1', '', '+1');
	$morls = array('=', '=', '>', '>');
	for($i=0; $i<4; $i++){	 
		$result = db_get_fields("SELECT * 
								FROM (
									SELECT * , @i := @i +1 AS s
									FROM (
											SELECT p.product_id, pd.product, p.amount
											FROM cscart_products p
											LEFT OUTER JOIN cscart_products_categories c ON p.product_id = c.product_id
											LEFT OUTER JOIN cscart_product_descriptions pd ON p.product_id = pd.product_id
											WHERE c.category_id = ".$category[0]."
											AND pd.lang_code =  'RU'
											ORDER BY p.amount DESC , pd.product ASC
										) AS pp, 
										(
											SELECT @i :=0
										) AS t
									 ) AS oO
								WHERE oO.s ".$morls[$i]." ( 
									SELECT temp.position
									FROM (
										SELECT * , @j := @j +1 AS position
										FROM (

										SELECT p1.product_id, pd1.product, p1.amount
										FROM cscart_products p1
										LEFT OUTER JOIN cscart_products_categories c1 ON p1.product_id = c1.product_id
										LEFT OUTER JOIN cscart_product_descriptions pd1 ON p1.product_id = pd1.product_id
										WHERE c1.category_id = ".$category[0]."
										AND pd1.lang_code =  'RU'
										ORDER BY p1.amount DESC , pd1.product ASC
										) AS pp1, (

										SELECT @j :=0
									) AS t1 ) AS temp
									WHERE temp.product_id = ".(int)$product_id."
								) ".$moreLess[$i]." LIMIT 1");	
			$items[] = $result[0];	
		}
		if(empty($items[3])){
				
					$result = db_get_fields("SELECT * 
								FROM (
									SELECT * , @i := @i +1 AS s
									FROM (
											SELECT p.product_id, pd.product, p.amount
											FROM cscart_products p
											LEFT OUTER JOIN cscart_products_categories c ON p.product_id = c.product_id
											LEFT OUTER JOIN cscart_product_descriptions pd ON p.product_id = pd.product_id
											WHERE c.category_id = ".(int)$category[0]."
											AND pd.lang_code =  'RU'
											ORDER BY p.amount DESC , pd.product ASC
										) AS pp, 
										(
											SELECT @i :=0
										) AS t
									 ) AS oO
								WHERE oO.s > 0 LIMIT 2");
				if(empty($items[2])){
					$items[2] 	= $result[0];
					$items[3] 	= $result[1];
					
				}
				else{
					$items[3] = $result[0];
				}
					
			}
			
			if(empty($items[0])){
			$result = db_get_fields("
										 SELECT * 
							FROM (

							SELECT * , @i := @i +1 AS s
							FROM (

							SELECT p.product_id, pd.product, p.amount
							FROM cscart_products p
							LEFT OUTER JOIN cscart_products_categories c ON p.product_id = c.product_id
							LEFT OUTER JOIN cscart_product_descriptions pd ON p.product_id = pd.product_id
							WHERE c.category_id = ".$category[0]."
							AND pd.lang_code =  'RU'
							ORDER BY p.amount DESC , pd.product ASC
							) AS pp, (

							SELECT @i :=0
							) AS t
							) AS oO
							WHERE oO.s > ( 
							SELECT MAX( temp.position ) 
							FROM (

							SELECT * , @j := @j +1 AS position
							FROM (

							SELECT p1.product_id, pd1.product, p1.amount
							FROM cscart_products p1
							LEFT OUTER JOIN cscart_products_categories c1 ON p1.product_id = c1.product_id
							LEFT OUTER JOIN cscart_product_descriptions pd1 ON p1.product_id = pd1.product_id
							WHERE c1.category_id = ".$category[0]."
							AND pd1.lang_code =  'RU'
							ORDER BY p1.amount DESC , pd1.product ASC
							) AS pp1, (

							SELECT @j :=0
							) AS t1 ) AS temp
							) -2 LIMIT 2
				
				");
				
				if(empty($items[1])){
					$items[0] = $result[0];
					$items[1] = $result[1];
				}
				else{
					
					$items[0] = $result[1];
					
				}		
			}		
			
			return implode(", ", $items);
		
	
}

function fn_get_same_line_products($product_id){
	$currentSeria = db_get_field("SELECT seria FROM cscart_products WHERE product_id = $product_id");
	if(!empty($currentSeria)){
			$samePids = db_get_fields("SELECT product_id FROM cscart_products WHERE seria = '".mysql_real_escape_string($currentSeria)."' AND product_id NOT IN ($product_id)");
		if(!empty($samePids))
			return $samePids;
		else
			return false;				
	}
}	

function fn_get_sales_products(){
#очень большой костыль. БЕРЕМ ТОВАРЫ С БЛОКА РАСПРОДАЖ
	
	return db_get_fields("SELECT product_id FROM cscart_products WHERE sale = 1");
}	


function fn_get_products($params, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    $stopwatch = new StopWatch();
    $stopwatch->start();
	if(isset($params['doLinks']) && $params['doLinks'] ==1){
		$neighbours = fn_get_neighbours($_REQUEST['product_id']);
		
		$params['pid'] = $neighbours;
		$params['limit'] = 4;
	
	}
		
	if(isset($params['doSales']) && $params['doSales'] ==1){
		
		$params['pid'] = fn_get_sales_products();
	
		
	}
	if(isset($params['doSameLine']) && $params['doSameLine'] ==1){
		
		$sameLineProducts = fn_get_same_line_products($_REQUEST['product_id']);
		if($sameLineProducts){
			$params['pid'] = $sameLineProducts;
		}
		else	
			return false;

	}		
	
	// Init filter
	$relevanceField = '';
	$relevanceOrder = '';
	$params = fn_init_view('products', $params);

	// Set default values to input params
	$default_params = array (
		'pname' => '',
		'pshort' => '',
		'pfull' => '',
		'pkeywords' => '',
		'feature' => array(),
		'type' => 'simple',
		'page' => 1,
		'action' => '',
		'variants' => array(),
		'ranges' => array(),
		'custom_range' => array(),
		'field_range' => array(),
		'features_hash' => '',
		'limit' => 0,
		'bid' => 0,
		'match' => '',
		'search_tracking_flags' => array()
	);

	$params = array_merge($default_params, $params);

	if ((empty($params['pname']) || $params['pname'] != 'Y') && (empty($params['pshort']) || $params['pshort'] != 'Y') && (empty($params['pfull']) || $params['pfull'] != 'Y') && (empty($params['pkeywords']) || $params['pkeywords'] != 'Y') && (empty($params['feature']) || $params['feature'] != 'Y') && !empty($params['q'])) {
		$params['pname'] = 'Y';
	}

	$auth = & $_SESSION['auth'];

	// Define fields that should be retrieved
	$fields = array (
		'products.product_id',
		'products.company_id',
		'companies.company as company_name',
		'descr1.product as product',
		'products.tracking',
		'products.feature_comparison',
		'products.zero_price_action',
		'products.product_type',
		'products.tax_ids',
		'products.weight',
		"GROUP_CONCAT(IF(products_categories.link_type = 'M', CONCAT(products_categories.category_id, 'M'), products_categories.category_id)) as category_ids",
		'min_qty',
		'max_qty',
		'products.qty_step',
		'products.list_qty_count',
		'products.is_edp',
		'avail_since',
		'buy_in_advance',
		'products.options_type',
		'products.exceptions_type',
		'companies.company as company_name',
		
		
	);

	// Define sort fields
	$sortings = array (
		'code' => 'products.product_code',
		'status' => 'products.status',
		'product' => 'descr1.product',
		'position' => 'products_categories.position',
		'price' => 'prices.price',
		'list_price' => 'products.list_price',
		'weight' => 'products.weight',
		'amount' => 'products.amount',
		'timestamp' => 'products.timestamp',
		'popularity' => 'popularity.total',
		'company' => "company_name",
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	$join = $condition = $inventory_condition = '';

	$condition .= fn_get_company_condition('products.company_id');

	if (isset($params['company_id']) && $params['company_id'] != '') {
		$params['company_id'] = intval($params['company_id']);
		$condition .= db_quote(' AND products.company_id = ?i ', $params['company_id']);
	}

	if (AREA == 'C') {
		if (fn_check_suppliers_functionality()) {
			// if MVE or suppliers enabled
			$condition .= " AND (companies.status = 'A' OR products.company_id = 0) ";
		} else {
			// if suppliers disabled
			$condition .= fn_get_company_condition('products.company_id', true, '0', false, true);
		}
	}

	$join .= db_quote(" LEFT JOIN ?:companies companies ON companies.company_id = products.company_id ");
	$join .= db_quote(" LEFT JOIN ?:product_descriptions as descr1 ON descr1.product_id = products.product_id AND descr1.lang_code = ?s ", $lang_code);

	// Search string condition for SQL query
	if (isset($params['q']) && fn_string_no_empty($params['q'])) {

		$params['q'] = trim($params['q']);
		if ($params['match'] == 'any') {
			$pieces = fn_explode(' ', $params['q']);
			$search_type = ' OR ';
		} elseif ($params['match'] == 'all') {
			$pieces = fn_explode(' ', $params['q']);
			$search_type = ' AND ';
		} else {
			$pieces = array($params['q']);
			$search_type = '';
		}

		$_condition = array();
		foreach ($pieces as $piece) {
			if (strlen($piece) == 0) {
				continue;
			}
			$pieceTrimed = str_replace(array(' ','-'), '', $piece);
			
			$tmp = db_quote("(descr1.search_words LIKE ?l)", "%$piece%"); // check search words

			if ($params['pname'] == 'Y') {
				$tmp .= db_quote(" OR MATCH (product) AGAINST ('".mysql_real_escape_string($piece)."')");
				//$tmp .= db_quote(" OR MATCH (product) AGAINST ('$piece')");
				$tmp .= db_quote(" OR REPLACE( REPLACE(upper(descr1.product),  ' ',  '' ) ,  '-',  '' ) LIKE ?l", "%$pieceTrimed%");
				$relevanceField = " ,MATCH(product) AGAINST ('".mysql_real_escape_string($piece)."' IN BOOLEAN MODE) AS relevance";
				$relevanceOrder = "relevance DESC,";
			}
			if ($params['pshort'] == 'Y') {
				//$tmp .= db_quote(" OR descr1.short_description LIKE ?l", "%$piece%");
			}
			if ($params['pfull'] == 'Y') {
				//$tmp .= db_quote(" OR descr1.full_description LIKE ?l", "%$piece%");
			}
			if ($params['pkeywords'] == 'Y') {
			//	$tmp .= db_quote(" OR (descr1.meta_keywords LIKE ?l OR descr1.meta_description LIKE ?l)", "%$piece%", "%$piece%");
			}
			if (!empty($params['feature']) && $params['action'] != 'feature_search') {
			//	$tmp .= db_quote(" OR ?:product_features_values.value LIKE ?l", "%$piece%");
			}
			$tmp .= db_quote(" OR products.product_code LIKE ?l", "%$piece%");
			fn_set_hook('additional_fields_in_search', $params, $fields, $sortings, $condition, $join, $sorting, $group_by, $tmp);

			$_condition[] = '(' . $tmp . ')';
		}

		$_cond = implode($search_type, $_condition);

		if (!empty($_condition)) {
			$condition .= ' AND (' . $_cond . ') ';
		}

		if (!empty($params['feature']) && $params['action'] != 'feature_search') {
			$join .= " LEFT JOIN ?:product_features_values ON ?:product_features_values.product_id = products.product_id";
			$condition .= db_quote(" AND (?:product_features_values.feature_id IN (?n) OR ?:product_features_values.feature_id IS NULL)", array_values($params['feature']));
		}

		unset($_condition);
	}

	//
	// [Advanced and feature filters]
	//

	if (!empty($params['apply_limit']) && $params['apply_limit']) {
		$pids = array();
		foreach ($params['pid'] as $pid) {
			if ($pid != $params['exclude_pid']) {
				if (count($pids) == $params['limit']) {
					break;
				}
				else {
					$pids[] = $pid;
				}
			}
		}
		$params['pid'] = $pids;
	}
	if (!empty($params['features_hash']) || (!fn_is_empty($params['variants']))) {
		$join .= db_quote(" LEFT JOIN ?:product_features_values ON ?:product_features_values.product_id = products.product_id AND ?:product_features_values.lang_code = ?s", CART_LANGUAGE);
	}

	if (!empty($params['variants'])) {
		$params['features_hash'] .= implode('.', $params['variants']);
	}

	$advanced_variant_ids = $simple_variant_ids = $ranges_ids = $fields_ids = array();

	if (!empty($params['features_hash'])) {
		if (!empty($params['advanced_filter'])) {
			list($av_ids, $ranges_ids, $fields_ids) = fn_parse_features_hash($params['features_hash']);
			$advanced_variant_ids = db_get_hash_multi_array("SELECT feature_id, variant_id FROM ?:product_feature_variants WHERE variant_id IN (?n)", array('feature_id', 'variant_id'), $av_ids);
		} else {
			list($simple_variant_ids, $ranges_ids, $fields_ids) = fn_parse_features_hash($params['features_hash']);
		}
	}
	if (!empty($params['multiple_variants']) && !empty($params['advanced_filter'])) {
		$simple_variant_ids = $params['multiple_variants'];
	}

	if (!empty($advanced_variant_ids)) {
		$join .= db_quote(" LEFT JOIN (SELECT product_id, GROUP_CONCAT(?:product_features_values.variant_id) AS advanced_variants FROM ?:product_features_values WHERE lang_code = ?s GROUP BY product_id) AS pfv_advanced ON pfv_advanced.product_id = products.product_id", CART_LANGUAGE);

		$where_and_conditions = array();
		foreach ($advanced_variant_ids as $k => $variant_ids) {
			$where_or_conditions = array();
			foreach ($variant_ids as $variant_id => $v) {
				$where_or_conditions[] = db_quote(" FIND_IN_SET('?i', advanced_variants)", $variant_id);
			}
			$where_and_conditions[] = "(".implode(' OR ', $where_or_conditions).")";
		}
		$condition .= ' AND '.implode(' AND ', $where_and_conditions);
	}

	if (!empty($simple_variant_ids)) {
		$join .= db_quote(" LEFT JOIN (SELECT product_id, GROUP_CONCAT(?:product_features_values.variant_id) AS simple_variants FROM ?:product_features_values WHERE lang_code = ?s GROUP BY product_id) AS pfv_simple ON pfv_simple.product_id = products.product_id", CART_LANGUAGE);

		$where_conditions = array();
		foreach ($simple_variant_ids as $k => $variant_id) {
			$where_conditions[] = db_quote(" FIND_IN_SET('?i', simple_variants)", $variant_id);
		}
		$condition .= ' AND '.implode(' AND ', $where_conditions);
	}

	//
	// Ranges from text inputs
	//

	// Feature ranges
	if (!empty($params['custom_range'])) {
		foreach ($params['custom_range'] as $k => $v) {
			$k = intval($k);
			if (fn_string_no_empty($v['from']) || fn_string_no_empty($v['to'])) {
				if (!empty($v['type'])) {
					if ($v['type'] == 'D') {
						$v['from'] = fn_parse_date($v['from']);
						$v['to'] = fn_parse_date($v['to']);
					}
				}
				$join .= db_quote(" LEFT JOIN ?:product_features_values as custom_range_$k ON custom_range_$k.product_id = products.product_id AND custom_range_$k.lang_code = ?s", CART_LANGUAGE);
				if (fn_string_no_empty($v['from']) && fn_string_no_empty($v['to'])) {
					$condition .= db_quote(" AND (custom_range_$k.value_int >= ?i AND custom_range_$k.value_int <= ?i AND custom_range_$k.value = '' AND custom_range_$k.feature_id = ?i) ", $v['from'], $v['to'], $k);
				} else {
					$condition .= " AND custom_range_$k.value_int" . (fn_string_no_empty($v['from']) ? db_quote(' >= ?i', $v['from']) : db_quote(" <= ?i AND custom_range_$k.value = '' AND custom_range_$k.feature_id = ?i ", $v['to'], $k));
				}
			}
		}
	}
	// Product field ranges
	$filter_fields = fn_get_product_filter_fields();
	if (!empty($params['field_range'])) {
		foreach ($params['field_range'] as $field_type => $v) {
			$structure = $filter_fields[$field_type];
			if (!empty($structure) && (!empty($v['from']) || !empty($v['to']))) {
				$params["$structure[db_field]_from"] = trim($v['from']);
				$params["$structure[db_field]_to"] = trim($v['to']);
			}
		}
	}
	// Ranges from database
	if (!empty($ranges_ids)) {
		$range_conditions = db_get_array("SELECT `from`, `to`, feature_id FROM ?:product_filter_ranges WHERE range_id IN (?n)", $ranges_ids);
		foreach ($range_conditions as $k => $range_condition) {
			$join .= db_quote(" LEFT JOIN ?:product_features_values as var_val_$k ON var_val_$k.product_id = products.product_id AND var_val_$k.lang_code = ?s", CART_LANGUAGE);
			$condition .= db_quote(" AND (var_val_$k.value_int >= ?i AND var_val_$k.value_int <= ?i AND var_val_$k.value = '' AND var_val_$k.feature_id = ?i) ", $range_condition['from'], $range_condition['to'], $range_condition['feature_id']);
		}
	}

	// Field ranges
	$fields_ids = empty($params['fields_ids']) ? $fields_ids : $params['fields'];
	if (!empty($fields_ids)) {
		foreach ($fields_ids as $rid => $field_type) {
			$structure = $filter_fields[$field_type];
			if ($structure['condition_type'] == 'D') {
				$range_condition = db_get_row("SELECT `from`, `to`, range_id FROM ?:product_filter_ranges WHERE range_id = ?i", $rid);
				if (!empty($range_condition)) {
					$params["$structure[db_field]_from"] = $range_condition['from'];
					$params["$structure[db_field]_to"] = $range_condition['to'];
				}
			} elseif ($structure['condition_type'] == 'F') {
				$params[$structure['db_field']] = $rid;
			} elseif ($structure['condition_type'] == 'C') {
				$params[$structure['db_field']] = ($rid == 1) ? 'Y' : 'N';
			}
		}
	}

	// Checkbox features
	if (!empty($params['ch_filters']) && !fn_is_empty($params['ch_filters'])) {
		foreach ($params['ch_filters'] as $k => $v) {
			// Product field filter
			if (is_string($k) == true && !empty($v) && $structure = $filter_fields[$k]) {
				$condition .= db_quote(" AND $structure[table].$structure[db_field] IN (?a)", ($v == 'A' ? array('Y', 'N') : $v));
			// Feature filter
			} elseif (!empty($v)) {
				$fid = intval($k);
				$join .= db_quote(" LEFT JOIN ?:product_features_values as ch_features_$fid ON ch_features_$fid.product_id = products.product_id AND ch_features_$fid.lang_code = ?s", CART_LANGUAGE);
				$condition .= db_quote(" AND ch_features_$fid.feature_id = ?i AND ch_features_$fid.value IN (?a)", $fid, ($v == 'A' ? array('Y', 'N') : $v));
			}
		}
	}

	// Text features
	if (!empty($params['tx_features'])) {
		foreach ($params['tx_features'] as $k => $v) {
			if (fn_string_no_empty($v)) {
				$fid = intval($k);
				$join .= " LEFT JOIN ?:product_features_values as tx_features_$fid ON tx_features_$fid.product_id = products.product_id";
				$condition .= db_quote(" AND tx_features_$fid.value LIKE ?l AND tx_features_$fid.lang_code = ?s", "%" . trim($v) . "%", CART_LANGUAGE);
			}
		}
	}

	//
	// [/Advanced filters]
	//

	$feature_search_condition = '';
	if (!empty($params['feature'])) {
		// Extended search by product fields
		$_cond = array();
		$total_hits = 0;
		foreach ($params['feature'] as $f_id) {
			if (!empty($f_val)) {
				$total_hits++;
				$_cond[] = db_quote("(?:product_features_values.feature_id = ?i)", $f_id);
			}
		}

		if (!empty($_cond)) {
			$cache_feature_search = db_get_fields("SELECT product_id, COUNT(product_id) as cnt FROM ?:product_features_values WHERE (" . implode(' OR ', $_cond) . ") GROUP BY product_id HAVING cnt = $total_hits");

			$feature_search_condition .= db_quote(" AND products_categories.product_id IN (?n)", $cache_feature_search);
		}
	}

	// Category search condition for SQL query
	if (!empty($params['cid'])) {
		$cids = is_array($params['cid']) ? $params['cid'] : array($params['cid']);

		if (!empty($params['subcats']) && $params['subcats'] == 'Y') {
			$_ids = db_get_fields("SELECT a.category_id FROM ?:categories as a LEFT JOIN ?:categories as b ON b.category_id IN (?n) WHERE a.id_path LIKE CONCAT(b.id_path, '/%')", $cids);

			$cids = fn_array_merge($cids, $_ids, false);
		}

		$condition .= db_quote(" AND ?:categories.category_id IN (?n)", $cids);
	}

	// If we need to get the products by IDs and no IDs passed, don't search anything
	if (!empty($params['force_get_by_ids']) && empty($params['pid']) && empty($params['product_id'])) {
		return array(array(), array(), 0);
	}

	// Product ID search condition for SQL query
	if (!empty($params['pid'])) {
		if(isset($params['doLinks']) && $params['doLinks'] ==1){
			$condition .= db_quote(' AND products.product_id IN ('.$params['pid'].')', $params['pid']);
		}
		else{
			$condition .= db_quote(' AND products.product_id IN (?n)', $params['pid']);
		}
	}

	// Exclude products from search results
	if (!empty($params['exclude_pid'])) {
		$condition .= db_quote(' AND products.product_id NOT IN (?n)', $params['exclude_pid']);
	}

	// Search by feature comparison flag
	if (!empty($params['feature_comparison'])) {
		$condition .= db_quote(' AND products.feature_comparison = ?s', $params['feature_comparison']);
	}

	// Search products by localization
	$condition .= fn_get_localizations_condition('products.localization', true);
	$condition .= fn_get_localizations_condition('?:categories.localization', true);

	if (isset($params['price_from']) && fn_is_numeric($params['price_from'])) {
		$condition .= db_quote(' AND prices.price >= ?d', fn_convert_price(trim($params['price_from'])));
	}

	if (isset($params['price_to']) && fn_is_numeric($params['price_to'])) {
		$condition .= db_quote(' AND prices.price <= ?d', fn_convert_price(trim($params['price_to'])));
	}

	if (isset($params['weight_from']) && fn_is_numeric($params['weight_from'])) {
		$condition .= db_quote(' AND products.weight >= ?d', fn_convert_weight(trim($params['weight_from'])));
	}

	if (isset($params['weight_to']) && fn_is_numeric($params['weight_to'])) {
		$condition .= db_quote(' AND products.weight <= ?d', fn_convert_weight(trim($params['weight_to'])));
	}

	// search specific inventory status
	if (!empty($params['search_tracking_flags'])) {
		$condition .= db_quote(' AND products.tracking IN(?a)', $params['search_tracking_flags']);
	}

	if (isset($params['amount_from']) && fn_is_numeric($params['amount_from'])) {
		$condition .= db_quote(" AND IF(products.tracking = 'O', inventory.amount >= ?i, products.amount >= ?i)", $params['amount_from'], $params['amount_from']);
		$inventory_condition .= db_quote(' AND inventory.amount >= ?i', $params['amount_from']);
	}

	if (isset($params['amount_to']) && fn_is_numeric($params['amount_to'])) {
		$condition .= db_quote(" AND IF(products.tracking = 'O', inventory.amount <= ?i, products.amount <= ?i)", $params['amount_to'], $params['amount_to']);
		$inventory_condition .= db_quote(' AND inventory.amount <= ?i', $params['amount_to']);
	}

	if (Registry::get('settings.General.show_out_of_stock_products') == 'N' && AREA == 'C') { // FIXME? Registry in model
		$condition .= " AND IF(products.tracking = 'O', inventory.amount > 0, products.amount > 0)";
	}

	if (!empty($params['status'])) {
		$condition .= db_quote(' AND products.status IN (?a)', $params['status']);
	}

	if (!empty($params['shipping_freight_from'])) {
		$condition .= db_quote(' AND products.shipping_freight >= ?d', $params['shipping_freight_from']);
	}

	if (!empty($params['shipping_freight_to'])) {
		$condition .= db_quote(' AND products.shipping_freight <= ?d', $params['shipping_freight_to']);
	}

	if (!empty($params['free_shipping'])) {
		$condition .= db_quote(' AND products.free_shipping = ?s', $params['free_shipping']);
	}

	if (!empty($params['downloadable'])) {
		$condition .= db_quote(' AND products.is_edp = ?s', $params['downloadable']);
	}

	if (!empty($params['b_id'])) {
		$join .= " LEFT JOIN ?:block_links ON ?:block_links.object_id = products.product_id AND ?:block_links.location = 'products'";
		$condition .= db_quote(' AND ?:block_links.block_id = ?i', $params['b_id']);
	}

	if (isset($params['pcode']) && fn_string_no_empty($params['pcode'])) {
		$pcode = trim($params['pcode']);
		$fields[] = 'inventory.combination';
		$condition .= db_quote(" AND (inventory.product_code LIKE ?l OR products.product_code LIKE ?l)", "%$pcode%", "%$pcode%");
		$inventory_condition .= db_quote(" AND inventory.product_code LIKE ?l", "%$pcode%");
	}

	if ((isset($params['amount_to']) && fn_is_numeric($params['amount_to'])) || (isset($params['amount_from']) && fn_is_numeric($params['amount_from'])) || !empty($params['pcode']) || (Registry::get('settings.General.show_out_of_stock_products') == 'N' && AREA == 'C')) {
		$join .= " LEFT JOIN ?:product_options_inventory as inventory ON inventory.product_id = products.product_id $inventory_condition";
	}

	if (!empty($params['period']) && $params['period'] != 'A') {
		list($params['time_from'], $params['time_to']) = fn_create_periods($params);
		$condition .= db_quote(" AND (products.timestamp >= ?i AND products.timestamp <= ?i)", $params['time_from'], $params['time_to']);
	}

	if (!empty($params['item_ids'])) {
		$condition .= db_quote(" AND products.product_id IN (?n)", explode(',', $params['item_ids']));
	}

	if (isset($params['popularity_from']) && fn_is_numeric($params['popularity_from'])) {
		$condition .= db_quote(' AND popularity.total >= ?i', $params['popularity_from']);
	}

	if (isset($params['popularity_to']) && fn_is_numeric($params['popularity_to'])) {
		$condition .= db_quote(' AND popularity.total <= ?i', $params['popularity_to']);
	}

	// Extended search mode condition for SQL query
	if ($params['type'] == 'extended') {
		array_push($fields,
			'products.product_code',
			'products.amount',
			'MIN(prices.price) as price',
			'products.status',
			'products.list_price',
			'descr1.short_description',
			"IF(descr1.short_description = '', descr1.full_description, '') as full_description"
		);

		if (!empty($params['cid'])) {
			$fields[] = 'products_categories.position';
		}

	}
	
	$price_usergroup_cond = db_quote(' AND prices.usergroup_id IN (?n)', ((AREA == 'A') ? USERGROUP_ALL : array_merge(array(USERGROUP_ALL), $auth['usergroup_ids'])));
	
	$condition .= $price_usergroup_cond;
	
	$price_usergroup_cond_2 = str_replace('prices', 'prices_2', $price_usergroup_cond);

	$join .= " LEFT JOIN ?:product_prices as prices ON prices.product_id = products.product_id AND prices.lower_limit = 1 LEFT JOIN ?:product_prices as prices_2 ON prices.product_id = prices_2.product_id AND prices_2.lower_limit = 1 AND prices_2.price < prices.price " . $price_usergroup_cond_2;

	$condition .= ' AND prices_2.price IS NULL';

	// Show enabled products/categories
	$_p_statuses = array('A');
	$_c_statuses = array('A' , 'H');

	$avail_cond = (AREA == 'C') ? ' AND (' . fn_find_array_in_set($auth['usergroup_ids'], '?:categories.usergroup_ids', true) . ')' : '';
	$avail_cond .= (AREA == 'C') ? ' AND (' . fn_find_array_in_set($auth['usergroup_ids'], 'products.usergroup_ids', true) . ')' : '';
	$avail_cond .= (AREA == 'C') ? db_quote(" AND ?:categories.status IN (?a) AND products.status IN (?a)", $_c_statuses, $_p_statuses) : '';

	$join .= " INNER JOIN ?:products_categories as products_categories ON products_categories.product_id = products.product_id INNER JOIN ?:categories ON ?:categories.category_id = products_categories.category_id $avail_cond $feature_search_condition";
	$limit = '';
	$group_by = 'products.product_id';
	fn_set_hook('get_products', $params, $fields, $sortings, $condition, $join, $sorting, $group_by, $lang_code);

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = Registry::get('settings.Appearance.default_products_sorting');
		if (empty($sortings[$params['sort_by']])) {
			$_products_sortings = fn_get_products_sorting(false);
			$params['sort_by'] = key($_products_sortings);
		}
	}

	if ($params['type'] != 'extended' && $params['sort_by'] == 'price') {
		$params['sort_by'] = 'product';
	}

	$default_sorting = fn_get_products_sorting(false);

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		if (!empty($default_sorting[$params['sort_by']]['default_order'])) {
			$params['sort_order'] = $default_sorting[$params['sort_by']]['default_order'];
		} else {
			$params['sort_order'] = 'asc';
		}
	}

	if (AREA == 'A' || $params['sort_by'] == 'popularity') {
		$fields[]   = 'popularity.total as popularity';
		$join .= db_quote(" LEFT JOIN ?:product_popularity as popularity ON popularity.product_id = products.product_id");
	}

	$sorting = $sortings[$params['sort_by']] . ' ' . $directions[$params['sort_order']];

	// Reverse sorting (for usage in view)
	$params['sort_order'] = $params['sort_order'] == 'asc' ? 'desc' : 'asc';

	// Used for View cascading
	if (!empty($params['get_query'])) {
		return "SELECT products.product_id FROM ?:products as products $join WHERE 1 $condition GROUP BY products.product_id";
	}

	// Used for Extended search
	if (!empty($params['get_conditions'])) {
		return array($fields, $join, $condition);
	}

 	if (!empty($params['limit'])) {
		$limit = db_quote(" LIMIT 0, ?i", $params['limit']);
 	}
	
		
	$total = 0;
	if (!empty($items_per_page)) {
		if (!empty($params['limit']) && $total > $params['limit']) {
			$total = $params['limit'];
		}

		$limit = fn_paginate($params['page'], 0, $items_per_page, true);
	}
	
	if(isset($_GET['no_pagination'])){
		ini_set('memory_limit','128M');
		$limit = db_quote("");
	}

if(isset($params['doLinks']) && $params['doLinks'] ==1)
	{
		$sorting = "products.amount DESC, descr1.product ASC";
		
	}
	//http://local.busenki.ru/index.php?dispatch=categories.view&category_id=80&subcats=Y&features_hash=P10.R43.R37.V6.V22.R33.R19
    //http://local.busenki.ru/index.php?dispatch=categories.view&category_id=183&subcats=Y&features_hash=P8.V6.V22.R38
    $sqlquery = 'SELECT SQL_CALC_FOUND_ROWS ' . implode(', ', $fields) . ",products.amount $relevanceField FROM ?:products as products $join WHERE 1 $condition GROUP BY $group_by ORDER BY  $relevanceOrder `products`.`amount` DESC, $sorting $limit";
	$products = db_get_array($sqlquery);



	if (!empty($items_per_page)) {
		$total = db_get_found_rows();
		fn_paginate($params['page'], $total, $items_per_page);
	} else {
		$total = count($products);
	}

	// Post processing
	foreach ($products as $k => $v) {
		$products[$k]['category_ids'] = fn_convert_categories($v['category_ids']);
	}

	if (!empty($params['item_ids'])) {
		$products = fn_sort_by_ids($products, explode(',', $params['item_ids']));
	}
	if (!empty($params['pid']) && !empty($params['apply_limit']) && $params['apply_limit']) {
		$products = fn_sort_by_ids($products, $params['pid']);
	}

	fn_set_hook('get_products_post', $products);

	return array($products, $params, $total);
}


function fn_sort_by_ids($items, $ids, $field = 'product_id')
{
	$tmp = array();

	foreach ($items as $k => $item) {
		foreach ($ids as $key => $item_id) {
			if ($item_id == $item[$field]) {
				$tmp[$key] = $item;
				break;
			}
		}
	}

	ksort($tmp);

	return $tmp;
}

function fn_convert_categories($category_ids)
{
	$c_ids = explode(',', $category_ids);
	$result = array();
	foreach ($c_ids as $v) {
		$result[intval($v)] = (strpos($v, 'M') !== false) ? 'M' : 'A';
	}

	return $result;
}

/**
 * Update product option
 *
 * @param array $option_data option data array
 * @param int $option_id option ID (empty if we're adding the option)
 * @param string $lang_code language code to add/update option for
 * @return int ID of the added/updated option
 */
function fn_update_product_option($option_data, $option_id = 0, $lang_code = DESCR_SL)
{
	// Add option
	if (empty($option_id)) {

		if (empty($option_data['product_id'])) {
			$option_data['product_id'] = 0;
		}

		$option_data['option_id'] = $option_id = db_query('INSERT INTO ?:product_options ?e', $option_data);

		foreach ((array)Registry::get('languages') as $option_data['lang_code'] => $_v) {
			db_query("INSERT INTO ?:product_options_descriptions ?e", $option_data);
		}

	// Update option
	} else {
		db_query("UPDATE ?:product_options SET ?u WHERE option_id = ?i", $option_data, $option_id);
		db_query("UPDATE ?:product_options_descriptions SET ?u WHERE option_id = ?i AND lang_code = ?s", $option_data, $option_id, $lang_code);
	}


	if (!empty($option_data['variants'])) {
		$var_ids = array();

		// Generate special variants structure for checkbox (2 variants, 1 hidden)
		if ($option_data['option_type'] == 'C') {
			$option_data['variants'] = array_slice($option_data['variants'], 0, 1); // only 1 variant should be here
			reset($option_data['variants']);
			$_k = key($option_data['variants']);
			$option_data['variants'][$_k]['position'] = 1; // checked variant
			$v_id = db_get_field("SELECT variant_id FROM ?:product_option_variants WHERE option_id = ?i AND position = 0", $option_id);
			$option_data['variants'][] = array ( // unchecked variant
				'position' => 0,
				'variant_id' => $v_id
			);
		}
		
		$variant_images = array();
		foreach ($option_data['variants'] as $k => $v) {
			if ((!isset($v['variant_name']) || $v['variant_name'] == '') && $option_data['option_type'] != 'C') {
				continue;
			}

			// Update product options variants
			if (isset($v['modifier'])) {
				$v['modifier'] = floatval($v['modifier']);
				if (floatval($v['modifier']) > 0) {
					$v['modifier'] = '+' . $v['modifier'];
				}
			}

			if (isset($v['weight_modifier'])) {
				$v['weight_modifier'] = floatval($v['weight_modifier']);
				if (floatval($v['weight_modifier']) > 0) {
					$v['weight_modifier'] = '+' . $v['weight_modifier'];
				}
			}

			$v['option_id'] = $option_id;

			if (empty($v['variant_id']) || (!empty($v['variant_id']) && !db_get_field("SELECT variant_id FROM ?:product_option_variants WHERE variant_id = ?i", $v['variant_id']))) {
				$v['variant_id'] = db_query("INSERT INTO ?:product_option_variants ?e", $v);
				foreach ((array)Registry::get('languages') as $v['lang_code'] => $_v) {
					db_query("INSERT INTO ?:product_option_variants_descriptions ?e", $v);
				}
			} else {
				db_query("UPDATE ?:product_option_variants SET ?u WHERE variant_id = ?i", $v, $v['variant_id']);
				db_query("UPDATE ?:product_option_variants_descriptions SET ?u WHERE variant_id = ?i AND lang_code = ?s", $v, $v['variant_id'], $lang_code);
			}

			$var_ids[] = $v['variant_id'];

			if ($option_data['option_type'] == 'C') {
				fn_delete_image_pairs($v['variant_id'], 'variant_image'); // force deletion of variant image for "checkbox" option
			} else {
				$variant_images[$k] = $v['variant_id'];
			}
		}
		
		if ($option_data['option_type'] != 'C' && !empty($variant_images)) {
			fn_attach_image_pairs('variant_image', 'variant_image', 0, $lang_code, $variant_images);
		}

		// Delete obsolete variants
		$condition = !empty($var_ids) ? db_quote('AND variant_id NOT IN (?n)', $var_ids) : '';
		$deleted_variants = db_get_fields("SELECT variant_id FROM ?:product_option_variants WHERE option_id = ?i $condition", $option_id, $var_ids);
		if (!empty($deleted_variants)) {
			db_query("DELETE FROM ?:product_option_variants WHERE variant_id IN (?n)", $deleted_variants);
			db_query("DELETE FROM ?:product_option_variants_descriptions WHERE variant_id IN (?n)", $deleted_variants);
			foreach ($deleted_variants as $v_id) {
				fn_delete_image_pairs($v_id, 'variant_image');
			}
		}
	}
	// Rebuild exceptions
	if (!empty($option_data['product_id'])) {
		fn_update_exceptions($option_data['product_id']);
	}
	return $option_id;
}

function fn_convert_weight($weight)
{
	if (Registry::get('config.localization.weight_unit')) {
		$g = Registry::get('settings.General.weight_symbol_grams');
		$weight = $weight * Registry::get('config.localization.weight_unit') / $g;
	}
	return sprintf('%01.2f', $weight);
}

function fn_convert_price($price)
{
	$currencies = Registry::get('currencies');
	return $price * $currencies[CART_PRIMARY_CURRENCY]['coefficient'];
}

function fn_get_products_sorting($simple_mode = true)
{
	$sorting = array(
		'position' => array('description' => fn_get_lang_var('default'), 'default_order' => 'asc'),
		'product' => array('description' => fn_get_lang_var('name'), 'default_order' => 'asc'),
		'price' => array('description' => fn_get_lang_var('price'), 'default_order' => 'asc'),
		'popularity' => array('description' => fn_get_lang_var('popularity'), 'default_order' => 'desc')
	);
	
	fn_set_hook('products_sorting', $sorting);
	
	if ($simple_mode) {
		foreach ($sorting as &$sort_item) {
			$sort_item = $sort_item['description'];
		}
	}
	
	return $sorting;
}

function fn_get_products_views($simple_mode = true, $active = false)
{
	//Registry::register_cache('products_views', array(), CACHE_LEVEL_STATIC);
	
	$active_layouts = Registry::get('settings.Appearance.default_products_layout_templates');
	if (!is_array($active_layouts)) {
		parse_str($active_layouts, $active_layouts);
	}
	
	if (!array_key_exists(Registry::get('settings.Appearance.default_products_layout'), $active_layouts)) {
		$active_layouts[Registry::get('settings.Appearance.default_products_layout')] = 'Y';
	}
	
	/*if (Registry::is_exist('products_views') == true && AREA != 'A') {
		$products_views = Registry::get('products_views');
		
		foreach ($products_views as &$view) {
			$view['title'] = fn_get_lang_var($view['title']);
		}
		
		if ($simple_mode) {
			$products_views = Registry::get('products_views');
			
			foreach ($products_views as $key => $value) {
				$products_views[$key] = $value['title'];
			}
		}
		
		if ($active) {
			$products_views = array_intersect_key($products_views, $active_layouts);
		}
		
		return $products_views;
	}*/

	$products_views = array();
	
	$skin_name = Registry::get('settings.skin_name_customer');
	
	// Get all available product_list_templates dirs
	$templates_path[] = DIR_SKINS . $skin_name . '/customer/blocks/product_list_templates';
	
	foreach	((array)Registry::get('addons') as $addon_name => $data) {
		if ($data['status'] == 'A') {
			if (is_dir(DIR_SKINS . $skin_name . '/customer/addons/' . $addon_name . '/blocks/product_list_templates')) {
				$templates_path[] = DIR_SKINS . $skin_name . '/customer/addons/' . $addon_name . '/blocks/product_list_templates';
			}
		}
	}
	
	// Scan received directories and fill the "views" array
	foreach ($templates_path as &$path) {
		$view_templates = fn_get_dir_contents($path, false, true, 'tpl');
		
		if (!empty($view_templates)) {
			foreach ($view_templates as &$file) {
				if ($file != '.' && $file != '..') {
					preg_match("/(.*$skin_name\/customer\/)(.*)/", $path, $matches);
					
					$_path = $matches[2]. '/' . $file;
					
					// Check if the template has inner description (like a "block manager")
					$fd = fopen($path . '/' . $file, 'r');
					$counter = 1;
					$_descr = '';
					
					while (($s = fgets($fd, 4096)) && ($counter < 3)) {
						preg_match('/\{\*\* template-description:(\w+) \*\*\}/i', $s, $matches);
						if (!empty($matches[1])) {
							$_descr = $matches[1];
							break;
						}
					}
					
					fclose($fd);
					
					$_title = substr($file, 0, -4);
					
					$products_views[$_title] = array(
						'template' => $_path,
						'title' => empty($_descr) ? $_title : $_descr,
						'active' => array_key_exists($_title, $active_layouts)
					);
				}
			}
		}
	}
	
	//Registry::set('products_views',  $products_views);
	
	foreach ($products_views as &$view) {
		$view['title'] = fn_get_lang_var($view['title']);
	}
	
	if ($simple_mode) {
		foreach ($products_views as $key => $value) {
			$products_views[$key] = $value['title'];
		}
	}

	if ($active) {
		$products_views = array_intersect_key($products_views, $active_layouts);
	}
	
	return $products_views;
}

function fn_get_products_layout($params)
{
	if (!isset($_SESSION['products_layout'])) {
		$_SESSION['products_layout'] = Registry::get('settings.Appearance.save_selected_layout') == 'Y' ? array() : '';
	}

	$active_layouts = fn_get_products_views(false, true);
	$default_layout = Registry::get('settings.Appearance.default_products_layout');

	if (!empty($params['category_id'])) {
		$_layout = db_get_row("SELECT default_layout, selected_layouts FROM ?:categories WHERE category_id = ?i", $params['category_id']);
		$category_default_layout = $_layout['default_layout'];
		$category_layouts = unserialize($_layout['selected_layouts']);
		if (!empty($category_layouts)) {
			if (!empty($category_default_layout)) {
				$default_layout = $category_default_layout;
			}
			$active_layouts = $category_layouts;
		}
		$ext_id = $params['category_id'];
	} else {
		$ext_id = 'search';
	}

	if (!empty($params['layout'])) {
		$layout = $params['layout'];
	} elseif (Registry::get('settings.Appearance.save_selected_layout') == 'Y' && !empty($_SESSION['products_layout'][$ext_id])) {
		$layout = $_SESSION['products_layout'][$ext_id];
	} elseif (Registry::get('settings.Appearance.save_selected_layout') == 'N' && !empty($_SESSION['products_layout'])) {
		$layout = $_SESSION['products_layout'];
	}

	$selected_layout = (!empty($layout) && !empty($active_layouts[$layout])) ? $layout : $default_layout;

	if (!empty($params['layout']) && $params['layout'] == $selected_layout) {
		if (Registry::get('settings.Appearance.save_selected_layout') == 'Y') {
			if (!is_array($_SESSION['products_layout'])) {
				$_SESSION['products_layout'] = array();
			}
			$_SESSION['products_layout'][$ext_id] = $selected_layout;
		} else {
			$_SESSION['products_layout'] = $selected_layout;
		}
	}

	return $selected_layout;
}

function fn_get_categories_list($category_ids, $lang_code = CART_LANGUAGE)
{
	static $max_categories = 10;
	$c_names = array();
	if (!empty($category_ids)) {
		$c_ids = fn_explode(',', $category_ids);
		$tr_c_ids = array_slice($c_ids, 0, $max_categories);
		$c_names = fn_get_category_name($tr_c_ids, $lang_code);
		if (sizeof($tr_c_ids) < sizeof($c_ids)) {
			$c_names[] = '...';
		}
	} else {
		$c_names[] = fn_get_lang_var('all_categories');
	}

	return $c_names;
}

function fn_build_options_combinations($options, $variants, $string, $iteration)
{
	$combinations = array();
	foreach ($variants[$iteration] as $variant_id) {
		if (count($options) - 1 > $iteration) {
			$string[$iteration][$options[$iteration]] = $variant_id;
			$combinations = array_merge($combinations, fn_build_options_combinations($options, $variants, $string, $iteration + 1));
			unset($string[$iteration]);
		} else {
			$_combination = array();
			if (!empty($string)) {
				foreach ($string as $val) {
					foreach ($val as $opt => $var) {
						$_combination[$opt] = $var;
					}
				}
			}
			$_combination[$options[$iteration]] = $variant_id;
			$combinations[] = $_combination;
		}
	}

	return $combinations;
}

function fn_apply_options_rules($product)
{
	/*	Options type:
			P - simultaneous/parallel
			S - sequential
	*/
	// Check for the options and exceptions types
	if (!isset($product['options_type']) || !isset($product['exceptions_type'])) {
		$product = array_merge($product, db_get_row('SELECT options_type, exceptions_type FROM ?:products WHERE product_id = ?i', $product['product_id']));
	}
	
	// Get the selected options or get the default options
	$product['selected_options'] = empty($product['selected_options']) ? array() : $product['selected_options'];
	$product['options_update'] = ($product['options_type'] == 'S') ? true : false;
	
	// Conver the selected options text to the utf8 format
	if (!empty($product['product_options'])) {
		foreach ($product['product_options'] as $id => $option) {
			if (!empty($option['value'])) {
				$product['product_options'][$id]['value'] = fn_unicode_to_utf8($option['value']);
			}
			if (!empty($product['selected_options'][$option['option_id']])) {
				$product['selected_options'][$option['option_id']] = fn_unicode_to_utf8($product['selected_options'][$option['option_id']]);
			}
		}
	}
	
	$selected_options = &$product['selected_options'];
	$changed_option = empty($product['changed_option']) ? true : false;
	
	$simultaneous = array();
	$next = 0;
	
	foreach ($product['product_options'] as $_id => $option) {
		$simultaneous[$next] = $option['option_id'];
		$next = $option['option_id'];
		
		if (!empty($option['value'])) {
			$selected_options[$option['option_id']] = $option['value'];
		}
		
		if (!$changed_option && $product['changed_option'] == $option['option_id']) {
			$changed_option = true;
		}
		
		if (!empty($selected_options[$option['option_id']]) && ($selected_options[$option['option_id']] == 'checked' || $selected_options[$option['option_id']] == 'unchecked') && $option['option_type'] == 'C') {
			foreach ($option['variants'] as $variant) {
				if (($variant['position'] == 0 && $selected_options[$option['option_id']] == 'unchecked') || ($variant['position'] == 1 && $selected_options[$option['option_id']] == 'checked')) {
					$selected_options[$option['option_id']] = $variant['variant_id'];
					if ($changed_option) {
						$product['changed_option'] = $option['option_id'];
					}
				}
			}
		}
		
		// Check, if the product has any options modifiers
		if (!empty($product['product_options'][$_id]['variants'])) {
			foreach ($product['product_options'][$_id]['variants'] as $variant) {
				if (!empty($variant['modifier']) && floatval($variant['modifier'])) {
					$product['options_update'] = true;
				}
			}
		}
	}
	
	if (!empty($product['changed_option']) && empty($selected_options[$product['changed_option']]) && $product['options_type'] == 'S') {
		$product['changed_option'] = array_search($product['changed_option'], $simultaneous);
		if ($product['changed_option'] == 0) {
			unset($product['changed_option']);
			if (!empty($selected_options)) {
				foreach ($selected_options as $option_id => $variant_id) {
					if (!in_array($option['option_type'], array('I', 'T', 'F'))) {
						unset($selected_options[$option_id]);
					}
				}
			}
		}
	}
	
	if (empty($selected_options) && $product['options_type'] == 'P') {
		$selected_options = fn_get_default_product_options($product['product_id'], true);
	}
	
	if (empty($product['changed_option'])) {
		end($selected_options);
		$product['changed_option'] = key($selected_options);
	}
	
	if ($product['options_type'] == 'S') {
		empty($product['changed_option']) ? $allow = 1 : $allow = 0;
		
		foreach ($product['product_options'] as $_id => $option) {
			$product['product_options'][$_id]['disabled'] = false;
			
			if (in_array($option['option_type'], array('I', 'T', 'F'))) {
				continue;
			}
			
			$option_id = $option['option_id'];
			
			if ($allow >= 1) {
				unset($selected_options[$option_id]);
				$product['product_options'][$_id]['value'] = '';
			}
			
			if ($allow >= 2) {
				$product['product_options'][$_id]['disabled'] = true;
				continue;
			}
			
			if (empty($product['changed_option']) || (!empty($product['changed_option']) && $product['changed_option'] == $option_id) || $allow > 0) {
				$allow++;
			}
		}
		
		$product['simultaneous'] = $simultaneous;
	}
	
	// Restore selected values
	if (!empty($selected_options)) {
		foreach ($product['product_options'] as $_id => $option) {
			if (isset($selected_options[$option['option_id']])) {
				$product['product_options'][$_id]['value'] = $selected_options[$option['option_id']];
			}
		}
	}
	
	// Change price
	if (empty($product['modifiers_price'])) {
		$product['base_modifier'] = fn_apply_options_modifiers($selected_options, $product['base_price'], 'P');
		$old_price = $product['price'];
		$product['price'] = $product['original_price'] = fn_apply_options_modifiers($selected_options, $product['price'], 'P');
		$product['modifiers_price'] = $product['price'] - $old_price;
	}
	
	if (!empty($product['list_price'])) {
		$product['list_price'] = fn_apply_options_modifiers($selected_options, $product['list_price'], 'P');
	}
	
	// Generate combination hash to get images. (Also, if the tracking with options, get amount and product code)
	$combination_hash = fn_generate_cart_id($product['product_id'], array('product_options' => $selected_options), true);
	$product['combination_hash'] = $combination_hash;
	
	// Change product code and amount
	if (!empty($product['tracking']) && $product['tracking'] == 'O') {
		$combination = db_get_row("SELECT product_code, amount FROM ?:product_options_inventory WHERE combination_hash = ?i", $combination_hash);
		
		if (!empty($combination['product_code'])) {
			$product['product_code'] = $combination['product_code'];
		}
		
		if (isset($combination['amount'])) {
			$product['inventory_amount'] = $combination['amount'];
		} else {
			$product['inventory_amount'] = $product['amount'] = 0;
		}
	}
	
	if (!$product['options_update']) {
		$product['options_update'] = db_get_field('SELECT COUNT(*) FROM ?:product_options_inventory WHERE product_id = ?i', $product['product_id']);
	}
	
	fn_set_hook('apply_options_rules', $product);
	
	return $product;
}
function fn_apply_exceptions_rules($product)
{
	/*	Exceptions type:
			A - Allowed
			F - Forbidden
	*/
	if (empty($product['selected_options']) && $product['options_type'] == 'S') {
		return $product;
	}
	
	$exceptions = fn_get_product_exceptions($product['product_id'], true);
	
	if (empty($exceptions)) {
		return $product;
	}
	
	$product['options_update'] = true;
	$options = array();
	$disabled = array();
	
	if (Registry::get('settings.General.exception_style') == 'warning') {
		$result = fn_is_allowed_options_exceptions($exceptions, $product['selected_options'], $product['options_type'], $product['exceptions_type']);
		
		if (!$result) {
			$product['show_exception_warning'] = 'Y';
		}
		
		return $product;
	}
	
	foreach ($exceptions as $exception_id => $exception) {
		if ($product['options_type'] == 'S') {
			// Sequential exceptions type
			$_selected = array();
			
			foreach ($product['selected_options'] as $option_id => $variant_id) {
				$disable = true;
				$full = array();
				
				$_selected[$option_id] = $variant_id;
				$elms = array_diff($exception, $_selected);
				$_exception = $exception;
				
				if (!empty($elms)) {
					foreach ($elms as $opt_id => $var_id) {
						if ($var_id != -2 && $var_id != -1) {
							$disable = false;
						}
						if ($var_id == -1) {
							$full[$opt_id] = $var_id;
						}
						if (($product['exceptions_type'] == 'A' && $var_id == -1 && isset($_selected[$opt_id])) || ($product['exceptions_type'] != 'A' && $var_id == -1)) {
							unset($elms[$opt_id]);
							if ($product['exceptions_type'] != 'A') {
								unset($_exception[$opt_id]);
							}
						}
					}
				}
				
				if ($disable && !empty($elms) && count($elms) != count($full)) {
					foreach ($elms as $opt_id => $var_id) {
						$disabled[$opt_id] = true;
					}
				} elseif ($disable && !empty($full)) {
					foreach ($full as $opt_id => $var_id) {
						$options[$opt_id]['any'] = true;
					}
				} elseif (count($elms) == 1 && reset($elms) == -2) {
					$disabled[key($elms)] = true;
				} elseif (($product['exceptions_type'] == 'A' && count($elms) + count($_selected) != count($_exception)) || ($product['exceptions_type'] == 'F' && count($elms) != 1)) {
					continue;
				}
				
				if (!isset($product['simultaneous'][$option_id]) || (isset($product['simultaneous'][$option_id]) && !isset($elms[$product['simultaneous'][$option_id]]))) {
					continue;
				}
				
				$elms[$product['simultaneous'][$option_id]] = ($elms[$product['simultaneous'][$option_id]] == -1) ? 'any' : $elms[$product['simultaneous'][$option_id]];
				if (isset($product['simultaneous'][$option_id]) && !empty($elms) && isset($elms[$product['simultaneous'][$option_id]])) {
					$options[$product['simultaneous'][$option_id]][$elms[$product['simultaneous'][$option_id]]] = true;
				}
			}
		} else {
			// Parallel exceptions type
			$disable = true;
			$full = array();
			
			$elms = array_diff($exception, $product['selected_options']);
			
			if (!empty($elms)) {
				foreach ($elms as $opt_id => $var_id) {
					if ($var_id != -2 && $var_id != -1) {
						$disable = false;
					}
					
					if ($var_id == -1) {
						$full[$opt_id] = $var_id;
						unset($elms[$opt_id]);
					}
				}
			}
			
			if ($disable && !empty($elms)) {
				foreach ($elms as $opt_id => $var_id) {
					$disabled[$opt_id] = true;
				}
			} elseif ($disable && !empty($full)) {
				foreach ($full as $opt_id => $var_id) {
					$options[$opt_id]['any'] = true;
				}
			} elseif (count($elms) == 1 && reset($elms) == -2) {
				$disabled[key($elms)] = true;
			} elseif (count($elms) == 1 && !in_array(reset($elms), $product['selected_options'])) {
				list($option_id, $variant_id) = array(key($elms), reset($elms));
				$options[$option_id][$variant_id] = true;
			}
		}
	}
	
	if ($product['exceptions_type'] == 'A' && $product['options_type'] == 'P') {
		foreach ($product['selected_options'] as $option_id => $variant_id) {
			$options[$option_id][$variant_id] = true;
		}
	}
	
	$first_elm = array();
	$clear_variants = false;
	
	foreach ($product['product_options'] as $_id => &$option) {
		$option_id = $option['option_id'];
		
		if (!in_array($option['option_type'], array('I', 'T', 'F')) && empty($first_elm)) {
			$first_elm = $product['product_options'][$_id];
		}
		
		if (isset($disabled[$option_id])) {
			$option['disabled'] = true;
			$option['not_required'] = true;
		}
		
		if (($product['options_type'] == 'S' && $option['option_id'] == $first_elm['option_id']) || (in_array($option['option_type'], array('I', 'T', 'F')))) {
			continue;
		}
		
		if ($product['options_type'] == 'S' && $option['disabled']) {
			if ($clear_variants) {
				$option['variants'] = array();
			}
			
			continue;
		}
		
		if (!empty($option['variants'])) {
			foreach ($option['variants'] as $variant_id => $variant) {
				if ($product['exceptions_type'] == 'A') {
					// Allowed combinations
					if (empty($options[$option_id][$variant_id]) && !isset($options[$option_id]['any'])) {
						unset($option['variants'][$variant_id]);
					}
				} else {
					// Forbidden combinations
					if (!empty($options[$option_id][$variant_id]) || isset($options[$option_id]['any'])) {
						unset($option['variants'][$variant_id]);
					}
				}
			}
			
			if (!in_array($option['value'], array_keys($option['variants']))) {
				$option['value'] = '';
			}
		}
		
		if (empty($option['variants'])) {
			$clear_variants = true;
		}
	}
	
	fn_set_hook('apply_exceptions', $product, $exceptions);
	
	return $product;
}

function fn_is_allowed_options_exceptions($exceptions, $options, $o_type = 'P', $e_type = 'F')
{
	foreach ($options as $option_id => $variant_id) {
		if (empty($variant_id)) {
			unset($options[$option_id]);
		}
	}
	
	if ($e_type == 'A' && empty($options)) {
		return true;
	}
	
	$in_exception = false;
	foreach ($exceptions as $exception) {
		foreach ($options as $option_id => $variant_id) {
			if (!isset($exception[$option_id])) {
				unset($options[$option_id]);
			}
		}
		
		if (count($exception) != count($options)) {
			continue;
		}
		
		$in_exception = true;
		$diff = array_diff($exception, $options);
		
		if (!empty($diff)) {
			foreach ($diff as $option_id => $variant_id) {
				if ($variant_id == -1) {
					unset($diff[$option_id]);
				}
			}
		}
		
		if (empty($diff) && $e_type == 'A') {
			return true;
		} elseif (empty($diff)) {
			return false;
		}
	}
	
	if ($in_exception && $e_type == 'A') {
		return false;
	}
	
	return true;
}
function fn_get_product_details_views($get_default = 'default')
{
	$product_details_views = array();

	if ($get_default == 'category') {
	
		$parent_layout = Registry::get('settings.Appearance.default_product_details_layout');
		$product_details_views['default'] = str_replace('[default]', fn_get_lang_var($parent_layout), fn_get_lang_var('default_product_details_layout'));
		
	} elseif ($get_default != 'default') {
	
		$parent_layout = db_get_field("SELECT c.product_details_layout FROM ?:products_categories as pc LEFT JOIN ?:categories as c ON pc.category_id = c.category_id WHERE pc.product_id = ?i AND pc.link_type = 'M'", $get_default);
		if (empty($parent_layout) || $parent_layout == 'default') {
			$parent_layout = Registry::get('settings.Appearance.default_product_details_layout');
		}
		$product_details_views['default'] = str_replace('[default]', fn_get_lang_var($parent_layout), fn_get_lang_var('default_product_details_layout'));
	}
	
	$skin_name = Registry::get('settings.skin_name_customer');
	
	// Get all available product_templates dirs
	$templates_path[] = DIR_SKINS . $skin_name . '/customer/blocks/product_templates';
	
	foreach ((array)Registry::get('addons') as $addon_name => $data) {
		if ($data['status'] == 'A') {
			if (is_dir(DIR_SKINS . $skin_name . '/customer/addons/' . $addon_name . '/blocks/product_templates')) {
				$templates_path[] = DIR_SKINS . $skin_name . '/customer/addons/' . $addon_name . '/blocks/product_templates';
			}
		}
	}
	
	// Scan received directories and fill the "views" array
	foreach ($templates_path as &$path) {
		$view_templates = fn_get_dir_contents($path, false, true, 'tpl');
		
		if (!empty($view_templates)) {
			foreach ($view_templates as &$file) {
				if ($file != '.' && $file != '..') {
					preg_match("/(.*$skin_name\/customer\/)(.*)/", $path, $matches);
					
					$_path = $matches[2]. '/' . $file;
					
					// Check if the template has inner description (like a "block manager")
					$fd = fopen($path . '/' . $file, 'r');
					$counter = 1;
					$_descr = '';
					
					while (($s = fgets($fd, 4096)) && ($counter < 3)) {
						preg_match('/\{\*\* template-description:(\w+) \*\*\}/i', $s, $matches);
						if (!empty($matches[1])) {
							$_descr = $matches[1];
							break;
						}
					}
					
					fclose($fd);
					
					$_title = empty($_descr) ? substr($file, 0, -4) : $_descr;
					
					$product_details_views[$_title] = fn_get_lang_var($_title);
				}
			}
		}
	}
	return $product_details_views;
}

function fn_get_product_details_layout($template_path, $product_id)
{
	$selected_layout = Registry::get('settings.Appearance.default_product_details_layout');
	
	if (!empty($product_id)) {
	
		$selected_layout = db_get_field("SELECT details_layout FROM ?:products WHERE product_id = ?i", $product_id);

		if (empty($selected_layout) || $selected_layout == 'default') {
			$selected_layout = db_get_field("SELECT c.product_details_layout FROM ?:products_categories as pc LEFT JOIN ?:categories as c ON pc.category_id = c.category_id WHERE pc.product_id = ?i AND pc.link_type = 'M'", $product_id);
		}
		
		if (empty($selected_layout) || $selected_layout == 'default') {
			$selected_layout = Registry::get('settings.Appearance.default_product_details_layout');
		}
	}
	
	$selected_layout = $selected_layout . ".tpl";
	
	return $template_path.$selected_layout;
}