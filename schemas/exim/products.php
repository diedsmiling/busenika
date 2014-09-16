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
// $Id: products.php 10580 2010-09-02 11:41:19Z andyye $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// Schema definition
//
$schema = array (
	'section' => 'products',
	'name' => fn_get_lang_var('products'),
	'pattern_id' => 'products',
	'key' => array('product_id'),
	'table' => 'products',
	'references' => array (
		'product_descriptions' => array (
			'reference_fields' => array ('product_id' => '#key', 'lang_code' => '@lang_code'),
			'join_type' => 'LEFT'
		),
		'product_prices' => array (
			'reference_fields' => array ('product_id' => '#key', 'lower_limit' => 1, 'usergroup_id' => 0),
			'join_type' => 'LEFT'
		),
		'images_links' => array (
			'reference_fields' => array('object_id' => '#key', 'object_type' => 'product', 'type' => 'M'),
			'join_type' => 'LEFT'
		),
	),
	'pre_processing' => array ('fn_exim_reset_inventory', '@reset_inventory'),
	'range_options' => array (
		'selector_url' => 'products.manage',
		'object_name' => fn_get_lang_var('products'),
	),
	'notes' => array (
		'text_exim_import_options_note',
		'text_exim_import_features_note',
		'text_exim_import_images_note',
		'text_exim_import_files_note',
	),
	'options' => array (
		'lang_code' => array (
			'title' => 'language',
			'type' => 'languages',
		),
		'category_delimiter' => array (
			'title' => 'category_delimiter',
			'description' => 'text_category_delimiter',
			'type' => 'input',
			'default_value' => '///'
		),
		'features_delimiter' => array (
			'title' => 'features_delimiter',
			'description' => 'text_features_delimiter',
			'type' => 'input',
			'default_value' => '///'
		),
		'images_path' => array (
			'title' => 'images_directory',
			'description' => 'text_images_directory',
			'type' => 'input',
			'default_value' => DIR_IMAGES . 'backup'
		),
		'files_path' => array (
			'title' => 'files_directory',
			'description' => 'text_files_directory',
			'type' => 'input',
			'default_value' => DIR_DOWNLOADS . 'backup'
		),
		'delete_files' => array (
			'title' => 'drop_existing_data',
			'type' => 'checkbox',
			'import_only' => true
		),
		'reset_inventory' => array (
			'title' => 'reset_inventory',
			'type' => 'checkbox',
			'import_only' => true
		),
		'price_dec_sign_delimiter' => array (
			'title' => 'price_dec_sign_delimiter',
			'description' => 'text_price_dec_sign_delimiter',
			'type' => 'input',
			'default_value' => '.'
		),
	),
	'export_fields' => array (
		'Product code' => array (
			'db_field' => 'product_code',
			'alt_key' => true,
			'required' => true,
		),
		'Category' => array (
			'process_get' => array ('fn_exim_get_product_categories', '#key', 'M', '@category_delimiter', '@lang_code'),
			'process_put' => array ('fn_exim_set_product_categories', '#key', 'M', '#this', '@category_delimiter', '@lang_code'),
			'linked' => false, // this field is not linked during import-export
			'default' => 'Products' // default value applies only when we creating new record
		),
		'List price' => array (
			'db_field' => 'list_price',
			'convert_put' => array ('fn_exim_import_price', '@price_dec_sign_delimiter'),
			'process_get' => array ('fn_exim_export_price', '#this', '@price_dec_sign_delimiter'),
		),
		'Sale' => array (
			'convert_put' => array ('fn_exim_import_price', '@price_dec_sign_delimiter'),
            'process_get' => array ('fn_exim_export_price', '#this', '@price_dec_sign_delimiter'),
			'db_field'=> 'sale',
			'linked' => false, // this field is not linked during import-export
			'default' => 'Products' // default value applies only when we creating new record
		),
		'Product line' => array (
			'convert_put' => array ('fn_exim_import_price', '@price_dec_sign_delimiter'),
            'process_get' => array ('fn_exim_export_price', '#this', '@price_dec_sign_delimiter'),
			'db_field'=> 'seria',
			'linked' => false, // this field is not linked during import-export
			'default' => 'Products' // default value applies only when we creating new record
		),
		'Price' => array (
			'table' => 'product_prices',
			'db_field' => 'price',
			'convert_put' => array ('fn_exim_import_price', '@price_dec_sign_delimiter'),
			'process_get' => array ('fn_exim_export_price', '#this', '@price_dec_sign_delimiter'),
		),
		'Status' => array (
			'db_field' => 'status'
		),
		'Quantity' => array (
			'db_field' => 'amount'
		),
		'Weight' => array (
			'db_field' => 'weight'
		),
		'Min quantity' => array (
			'db_field' => 'min_qty'
		),
		'Shipping freight' => array (
			'db_field' => 'shipping_freight',
			'convert_put' => array ('fn_exim_import_price', '@price_dec_sign_delimiter'),
			'process_get' => array ('fn_exim_export_price', '#this', '@price_dec_sign_delimiter'),
		),
		'Date added' => array (
			'db_field' => 'timestamp',
			'process_get' => array ('fn_timestamp_to_date', '#this'),
			'convert_put' => array ('fn_date_to_timestamp'),
			'return_result' => true
		),
		'Downloadable' => array (
			'db_field' => 'is_edp',
		),
		'Files' => array (
			'process_get' => array ('fn_exim_export_file', '#key', '@files_path'),
			'process_put' => array ('fn_exim_import_file', '#key', '#this', '@files_path', '@delete_files'),
			'linked' => false, // this field is not linked during import-export
		),
		'Ship downloadable' => array (
			'db_field' => 'edp_shipping',
		),
		'Inventory tracking' => array (
			'db_field' => 'tracking',
		),
		'Free shipping' => array (
			'db_field' => 'free_shipping',
		),
		'Feature comparison' => array (
			'db_field' => 'feature_comparison',
		),
		'Zero price action' => array (
			'db_field' => 'zero_price_action',
		),
		'Thumbnail' => array (
			'process_get' => array ('fn_export_image', '#this', 'product', '@images_path'),
			'table' => 'images_links',
			'db_field' => 'image_id',
			'use_put_from' => '%Detailed image%'
		),
		'Detailed image' => array (
			'process_get' => array ('fn_export_image', '#this', 'detailed', '@images_path'),
			'db_field' => 'detailed_id',
			'table' => 'images_links',
			'process_put' => array('fn_import_images', '@images_path', '%Thumbnail%', '#this', 'M', '#key', 'product')
		),
		'Product name' => array (
			'table' => 'product_descriptions',
			'db_field' => 'product'
		),
		'Description' => array (
			'table' => 'product_descriptions',
			'db_field' => 'full_description'
		),
		'Short description' => array (
			'table' => 'product_descriptions',
			'db_field' => 'short_description'
		),
		'Meta keywords' => array (
			'table' => 'product_descriptions',
			'db_field' => 'meta_keywords'
		),
		'Meta description' => array (
			'table' => 'product_descriptions',
			'db_field' => 'meta_description'
		),
		'Search words' => array (
			'table' => 'product_descriptions',
			'db_field' => 'search_words'
		),
		'Page title' => array (
			'table' => 'product_descriptions',
			'db_field' => 'page_title'
		),
		'Taxes' => array (
			'db_field' => 'tax_ids',
			'process_get' => array ('fn_exim_get_taxes', '#this', '@lang_code'),
			'process_put' => array ('fn_exim_set_taxes', '#key', '#this', '@lang_code'),
			'return_result' => true
		),
		'Localizations' => array (
			'db_field' => 'localization',
			'process_get' => array ('fn_exim_get_localizations', '#this', '@lang_code'),
			'process_put' => array ('fn_exim_set_localizations', '#key', '#this', '@lang_code'),
			'return_result' => true
		),
		'Features' => array (
			'process_get' => array ('fn_exim_get_product_features', '#key', '@features_delimiter', '@lang_code'),
			'process_put' => array ('fn_exim_set_product_features', '#key', '#this', '@features_delimiter', '@lang_code'),
			'linked' => false, // this field is not linked during import-export
		),
		'Options' => array (
			'process_get' => array ('fn_exim_get_product_options', '#key', '@lang_code'),
			'process_put' => array ('fn_exim_set_product_options', '#key', '#this', '@lang_code'),
			'linked' => false, // this field is not linked during import-export
		),
		'Secondary categories' => array (
			'process_get' => array ('fn_exim_get_product_categories', '#key', 'A', '@category_delimiter', '@lang_code'),
			'process_put' => array ('fn_exim_set_product_categories', '#key', 'A', '#this', '@category_delimiter', '@lang_code'),
			'linked' => false, // this field is not linked during import-export
		),
		'Product URL' => array (
			'process_get' => array ('fn_exim_get_product_url', '#key', '@lang_code'),
			'linked' => false
		),
		'Image URL' => array (
			'process_get' => array ('fn_exim_get_image_url', '#key', 'product', 'M', true, false, false), 
			'db_field' => 'image_id',
			'table' => 'images_links',
		),
	),
);

if (fn_check_suppliers_functionality ()) {

	$schema['references']['companies'] = array ('reference_fields' => array ('company_id' => '&company_id'), 'join_type' => 'LEFT', 'import_skip_db_processing' => true);

	if (PRODUCT_TYPE == 'MULTIVENDOR') {
		$schema['export_fields']['Vendor'] = array ('table' => 'companies', 'db_field' => 'company', 'process_put' => array ('fn_exim_set_company', '#key', '#this'));

	} elseif (PRODUCT_TYPE == 'PROFESSIONAL' || PRODUCT_TYPE == 'COMMUNITY') {
		$schema['export_fields']['Supplier'] = array ('table' => 'companies', 'db_field' => 'company', 'process_put' => array ('fn_exim_set_company', '#key', '#this'));
	}
}

// ------------- Utility functions ---------------

//
// Import product companies
// Parameters:
// @product_id - product ID
// @company_name - company name
if(!function_exists(fn_exim_set_company))
{

if(!function_exists(fn_exim_set_company))
{
	function fn_exim_set_company($product_id, $company_name)
{
	if (empty($company_name) || empty($product_id)) {
		return false;
	}

	$company_id = db_get_field('SELECT company_id FROM ?:companies WHERE company =  ?s', $company_name);

	if (!$company_id) {
		$company_data = array('company' => $company_name, 'email' => '');
		$company_id = fn_update_company ($company_data, 0);
	}
	db_query('UPDATE ?:products SET company_id = ?s WHERE product_id = ?i', $company_id, $product_id);

	return true;
}
}

//
// Creates categories tree by path
// Parameters:
// @product_id - product ID
// @link_type - M - main category, A - additional
// @data - categories path
// @category_delimiter - category delimiter

if(!function_exists(fn_exim_set_product_categories))
{
	function fn_exim_set_product_categories($product_id, $link_type, $data, $category_delimiter, $lang_code = CART_LANGUAGE)
	{
		if (empty($data)) {
			return false;
		}

		$set_delimiter = ';';

		$paths = array();
		$updated_categories = array();
		// Check if array is provided
		if (strpos($data, $set_delimiter) !== false) {
			$paths = explode($set_delimiter, $data);
			array_walk($paths, 'fn_trim_helper');
		} else {
			$paths[] = $data;
		}

		if (!fn_is_empty($paths)) {
			$old_data = db_get_hash_array("SELECT * FROM ?:products_categories WHERE product_id= ?i", 'category_id', $product_id);
			foreach ($old_data as $k => $v) {
				if ($v['link_type'] == $link_type) {
					$updated_categories[] = $k;
				}
			}
			db_query("DELETE FROM ?:products_categories WHERE product_id = ?i AND link_type = ?s", $product_id, $link_type);
		}

		foreach ($paths as $category) {
			$categories = (strpos($category, $category_delimiter) !== false) ? explode($category_delimiter, $category) : array($category);
			if (!empty($categories)) {
				$parent_id = '0';

				foreach ($categories as $cat) {
					$category_id = db_get_field("SELECT ?:categories.category_id FROM ?:category_descriptions INNER JOIN ?:categories ON ?:categories.category_id = ?:category_descriptions.category_id WHERE ?:category_descriptions.category = ?s AND lang_code = ?s AND parent_id = ?i", $cat, $lang_code, $parent_id);

					if (!empty($category_id)) {
						$parent_id = $category_id;
					} else {
						$category_data = array(
							'parent_id' => $parent_id,
							'category' => $cat,
						);

						$category_id = fn_update_category($category_data);
						$parent_id = $category_id;
					}
				}

				$data = array (
					'product_id' => $product_id,
					'category_id' => $category_id,
					'link_type' => $link_type,
				);

				if (!empty($old_data) && !empty($old_data[$category_id])) {
					$data = fn_array_merge($old_data[$category_id], $data);
				}

				db_query("REPLACE INTO ?:products_categories ?e", $data);

				$updated_categories[] = $category_id;
			}
		}

		if (!empty($updated_categories)) {
			fn_update_product_count($updated_categories);
			return true;
		}

		return false;
	}
}

//
// Export product categories
// Parameters:
// @product_id - product ID
// @link_type - M - main category, A - additional
// @delimter - path delimiter
// @lang_code - language code

if(!function_exists(fn_exim_get_product_categories))
{
	function fn_exim_get_product_categories($product_id, $link_type, $category_delimiter, $lang_code = '')
	{
		$set_delimiter = '; ';

		$category_ids = db_get_fields("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = ?s", $product_id, $link_type);

		$result = array();
		foreach ($category_ids as $c_id) {
			$result[] = fn_get_category_path($c_id, $lang_code, $category_delimiter);
		}

		return implode($set_delimiter, $result);
	}
}
//
// Export product taxes
// Parameters:
// @product_id - product ID
// @lang_code - language code

function fn_exim_get_taxes($product_taxes, $lang_code = '')
{
	$taxes = db_get_fields("SELECT tax FROM ?:tax_descriptions WHERE FIND_IN_SET(tax_id, ?s) AND lang_code = ?s", $product_taxes, $lang_code);

	return implode(', ', $taxes);
}

//
// Import product taxes
// Parameters:
// @product_id - product ID
// @data - comma delimited list of taxes
// @lang_code - language code

function fn_exim_set_taxes($product_id, $data, $lang_code = '')
{
	if (empty($data)) {
		db_query("UPDATE ?:products SET tax_ids = ''");
		return true;
	}

	$tax_ids = db_get_fields("SELECT tax_id FROM ?:tax_descriptions WHERE tax IN (?a) AND lang_code = ?s", fn_explode(',', $data), $lang_code);

	$_data = array (
		'tax_ids' => fn_create_set($tax_ids)
	);

	db_query('UPDATE ?:products SET ?u WHERE product_id = ?i', $_data, $product_id);

	return true;
}

//
// Export product features
// Parameters:
// @product_id - product ID
// @lang_code - language code
function fn_exim_get_product_features($product_id, $features_delimiter, $lang_code = CART_LANGUAGE)
{
	$pair_delimiter = ':';
	$set_delimiter = '; ';

	$result = array();
	$_params = array(
		'variants' => true, 
		'product_id' => $product_id,
		'plain' => true,
	);

	list($features) = fn_get_product_features($_params, 0, $lang_code);

	if (!empty($features)) {
		foreach ($features as $f) {
			$parent = '';
			if (!empty($f['parent_id'])) {
				$parent = '(' . $features[$f['parent_id']]['description'] . ') ';
			}

			if (!empty($f['value']) || !empty($f['value_int'])) {
				$result[] = fn_exim_post_item_id($f['feature_id']) . $parent . "{$f['description']}{$pair_delimiter} {$f['feature_type']}[" . (!empty($f['value']) ? $f['value'] : $f['value_int']) . ']';
			} elseif (!empty($f['variants'])) {
				$values = array();
				foreach ($f['variants'] as $v) {
					if (!empty($v['selected'])) {
						$values[] = $v['variant'];
					}
				}


				if (!empty($values)) {
					$result[] = fn_exim_post_item_id($f['feature_id']) . $parent . "{$f['description']}{$pair_delimiter} {$f['feature_type']}[" . implode($features_delimiter, $values) . ']';
				}
			}
		}
	}

	return !empty($result) ? implode($set_delimiter, $result) : '';
}

//
// Import product features
// Parameters:
// @product_id - product ID
// @data - delimited list of product features and their values
// @lang_code - language code
/*
value C (checkbox)
value T (text)

variant_id M (multi)
variant_id S (select)
variant_id N (select number)
variant_id E (select extended)

value_int O (number)
value_int D (date)

*/
function fn_exim_set_product_features($product_id, $data, $features_delimiter, $lang_code = '')
{
	$pair_delimiter = ':';
	$set_delimiter = ';';

	if (!empty($data)) {
		if (preg_match_all("/[ ]*(\{\d+\})?[ ]*(\(.+?\))?[ ]*(.+?):[ ]*([\w]{1})[ ]*\[(.*?)\];?/", $data, $features)) {

			// First, delete all links to features
			db_query("DELETE FROM ?:product_features_values WHERE product_id = ?i AND lang_code = ?s", $product_id, $lang_code);

			foreach ($features[3] as $k => $feature) {
				// feature data
				$feature_id_csv = fn_exim_get_item_id($features[1][$k]);
				$feature_type = $features[4][$k];
				$vars = fn_explode($features_delimiter, $features[5][$k]);

				// find group_id
				$group_id = 0;
				$group_exists = true;
				if (!empty($features[2][$k])) {
					$group_id = fn_exim_check_feature_group($features[2][$k], $lang_code, $group_exists);						
				}

				// try to find similar feature (same description, type, parent)
				$feature_id = 0; 
				if ($group_exists) {
					$feature_id = db_get_field(
						'SELECT ?:product_features.feature_id FROM ?:product_features_descriptions ' .
						'LEFT JOIN ?:product_features ON ?:product_features.feature_id = ?:product_features_descriptions.feature_id ' .
						'WHERE description = ?s AND lang_code = ?s AND feature_type = ?s AND parent_id = ?i LIMIT 1',
						$feature, $lang_code, $feature_type, $group_id
					);
				}
				
				if (empty($feature_id)) {
					// insert new feature
					
					$feature_data = array (
						'feature_type' => $feature_type,
						'parent_id' => $group_id, 
						'description' => $feature
					);
					if (!empty($feature_id_csv)) {
						$existent_id = db_get_field('SELECT feature_id FROM ?:product_features WHERE feature_id = ?i', $feature_id_csv);
						if (empty($existent_id)) {
							$feature_data['feature_id'] = $feature_id_csv;
						}
					}
					
					$feature_id = $feature_data['feature_id'] = db_query("INSERT INTO ?:product_features ?e", $feature_data);

					foreach ((array)Registry::get('languages') as $feature_data['lang_code'] => $_v) {
						db_query("REPLACE INTO ?:product_features_descriptions ?e", $feature_data);
					}
				}
				
				// import feature variants
				$i_data = array(
					'product_id' => $product_id,
					'lang_code' => $lang_code,
					'feature_id' => $feature_id,
				);

				if (strpos('MSNE', $feature_type) !== false) { // variant IDs
					$existent_variants = db_get_hash_single_array(
						'SELECT pfvd.variant_id, variant FROM ?:product_feature_variant_descriptions AS pfvd ' .
						'LEFT JOIN ?:product_feature_variants AS pfv ON pfv.variant_id = pfvd.variant_id ' . 
						'WHERE feature_id = ?i AND variant IN (?a) AND lang_code = ?s',
						array('variant_id', 'variant'), $feature_id, $vars, $lang_code
					);
					foreach ($vars as $v) {
						if (in_array($v, $existent_variants)) { // variant exists
							$i_data['variant_id'] = array_search($v, $existent_variants);
						} else { // add variant
							$v_data = array(
								'feature_id' => $feature_id,
							);
							$variant_id = db_query("INSERT INTO ?:product_feature_variants ?e", $v_data);

							$v_data = array(
								'variant_id' => $variant_id,
								'variant' => $v
							);
							foreach ((array)Registry::get('languages') as $v_data['lang_code'] => $_v) {
								db_query("INSERT INTO ?:product_feature_variant_descriptions ?e", $v_data);
							}

							$i_data['variant_id'] = $variant_id;
						}

						//db_query("REPLACE INTO ?:product_features_values ?e", $i_data);
						foreach ((array)Registry::get('languages') as $i_data['lang_code'] => $_v) {
							db_query("REPLACE INTO ?:product_features_values ?e", $i_data);
						}
					}
				} elseif (strpos('OD', $feature_type) !== false) { // value INT
					$i_data['value_int'] = ($feature_type == 'D' && strpos($vars[0], '/') !== false) ? fn_parse_date($vars[0]) : $vars[0];
					//db_query("REPLACE INTO ?:product_features_values ?e", $i_data);
					foreach ((array)Registry::get('languages') as $i_data['lang_code'] => $_v) {
						db_query("REPLACE INTO ?:product_features_values ?e", $i_data);
					}
				} else { // TEXT
					$i_data['value'] = $vars[0];
					//db_query("REPLACE INTO ?:product_features_values ?e", $i_data);
					foreach ((array)Registry::get('languages') as $i_data['lang_code'] => $_v) {
						db_query("REPLACE INTO ?:product_features_values ?e", $i_data);
					}
				}
			}
		}
	}

	return true;
}

/**
 * If feature group exists return it id, else create such groups for all available langs
 *
 * @param string $group group name
 * @param string $lang_code
 * @param boolean $group_exists true if group already exists, false otherwise.
 * 
 * @return integer ID of group 
 */
function fn_exim_check_feature_group($group, $lang_code, &$group_exists)
{
	$group = trim($group, '()');
	
	$group_id = db_get_field("SELECT feature_id FROM ?:product_features_descriptions WHERE description = ?s AND lang_code = ?s LIMIT 1", $group, $lang_code);
	
	if (empty($group_id)) {
		// Create new feature
		$group_data = array (
			'feature_type' => 'G',
			'status' => 'A',
		);
	
		$group_id = db_query("INSERT INTO ?:product_features ?e", $group_data);
	
		$group_data = array (
			'feature_id' => $group_id,
			'description' => $group,
		);
	
		foreach ((array)Registry::get('languages') as $group_data['lang_code'] => $_v) {
			db_query("REPLACE INTO ?:product_features_descriptions ?e", $group_data);
		}
		
		$group_exists = false;
	} else {
		$group_exists = true;
	}
	
	return $group_id;
}


//
// Export product options
// Parameters:
// @product_id - product ID
// @lang_code - language code
function fn_exim_get_product_options($product_id, $lang_code = '')
{
	$pair_delimiter = ':';
	$set_delimiter = '; ';
	$vars_delimiter = ',';

	$result = array();
	$options = fn_get_product_options($product_id, $lang_code);
	if (!empty($options)) {
		foreach ($options as $o) {
			$glob_opt = db_get_field("SELECT option_id FROM ?:product_global_option_links WHERE option_id = ?i AND product_id = ?i", $o['option_id'], $product_id);
			$str = fn_exim_post_item_id($o['option_id'] . (empty($glob_opt) ? "" : "_L")) . "$o[option_name]$pair_delimiter $o[option_type]";

			$variants = array();
			if (!empty($o['variants'])) {
				foreach ($o['variants'] as $v) {
					$variants[] = fn_exim_post_item_id($v['variant_id']) . $v['variant_name'];
				}
				$str .= '[' .implode($vars_delimiter, $variants). ']';
			}

			$result[] = $str;
		}
	}

	return !empty($result) ? implode($set_delimiter, $result) : '';
}

//
// Import product options
// Parameters:
// @product_id - product ID
// @data - delimited list of product options and their values
// @lang_code - language code
function fn_exim_set_product_options($product_id, $data, $lang_code = '')
{
	$pair_delimiter = ':';
	$set_delimiter = ';';
	$vars_delimiter = ',';

	if (!empty($data)) {
		$options = explode($set_delimiter, $data);
		if (!empty($options)) {
			$updated_ids = array(); // store updated ids, delete other (if exist)
			$o_position = 0;
			foreach ($options as $option) {
				$o_position += 10;

				$pair = explode($pair_delimiter, $option);
				$variants = '';
				if (is_array($pair)) {
					array_walk($pair, 'fn_trim_helper');

					if (($pos = strpos($pair[1], '[')) !== false) { // option has variants
						$variants = substr($pair[1], $pos + 1, strlen($pair[1]) - $pos - 2);
						$variants = explode($vars_delimiter, $variants);
					}
					$option_str = explode('_', fn_exim_get_item_id($pair[0]));
					$option_id = $option_str[0];
					$global_option = !empty($option_str[1]) && $option_str[1] == 'L';

					if ($global_option) {
						$glob_link = array(
							'option_id' => $option_id,
							'product_id' => $product_id
						);
						db_query('REPLACE INTO ?:product_global_option_links ?e', $glob_link);
					}

					$_restore = false;
					if (!empty($option_id)) {
						$_restore = !db_get_field("SELECT option_id FROM ?:product_options WHERE option_id = ?i", $option_id);
					}

					// Check if product option exists - FIXME!!! Global?
					if (empty($option_id)) {
						$option_id = db_get_field("SELECT o.option_id FROM ?:product_options_descriptions as d INNER JOIN ?:product_options as o ON o.option_id = d.option_id AND o.product_id = ?i WHERE d.option_name = ?s AND d.lang_code = ?s LIMIT 1", $product_id, $pair[0], $lang_code);
					}

					$option_type = substr($pair[1], 0, 1);

					// Generate array for variants
					$v_data = array();
					$v_data_ids = array();
					if (!empty($variants) && is_array($variants)) {
						$position = 0;
						foreach ($variants as $v) {
							$position += 10; 
							$v_data[] = array(
								'variant_name' => $v,
								'position' => $position
								); 
							if ($variant_id = fn_exim_get_item_id($v)) {
								$v_data_ids[] = array(
									'variant_id' => $variant_id,
									'variant_name' => $v,
									'position' => $position
								);
							}
						}
					}

					if ($option_type == 'C') {
						$v_data = $v_data_ids = array();
						if (!empty($option_id)) { // check if variant exist
							$v_data_ids = db_get_array("SELECT * FROM ?:product_option_variants WHERE option_id = ?i AND position = 1", $option_id);
						}

						// If not, generate default variant
						if (empty($v_data_ids)) {
							$v_data_ids = array(
								array(
									'position' => 1,
								),
							);
						}
					}

					$option_data = array(
						'option_name' => $pair[0],
						'option_type' => $option_type,
						'variants' => (empty($v_data_ids) ? $v_data : $v_data_ids)
					);

					// Option doesn't exist, create new
					if (empty($option_id) || $_restore) {
						if ($_restore) {
							$option_data['option_id'] = $option_id;
						}

						$option_data['product_id'] = !empty($global_option) ? 0 : $product_id;
						$option_data['position'] = $o_position;

						$updated_ids[] = fn_update_product_option($option_data, 0, $lang_code);

					// Option is exist, update it
					} else {
						$updated_ids[] = fn_update_product_option($option_data, $option_id, $lang_code);
					}
				}
			}

			// Delete all other options
			if (!empty($updated_ids)) {
				$obsolete_ids = db_get_fields("SELECT option_id FROM ?:product_options WHERE option_id NOT IN (?n) AND product_id = ?i", $updated_ids, $product_id);
				if (!empty($obsolete_ids)) {
					foreach ($obsolete_ids as $o_id) {
						fn_delete_product_option($o_id, $product_id);
					}
				}
			}
		}
	}

	return true;
}

//
// Export product files
// @product_id 0- product ID
// @path - path to store files
//
function fn_exim_export_file($product_id, $path)
{
	$files = db_get_array("SELECT file_path, preview_path FROM ?:product_files WHERE product_id = ?i", $product_id);
	if (!empty($files)) {
		// If backup path is set, check if it exists and copy files there
		if (!empty($path)) {
			if (!@is_dir($path) && fn_mkdir($path) == false) {
				$msg = str_replace('[directory]', $path, fn_get_lang_var('text_cannot_create_directory'));
				fn_set_notification('E', fn_get_lang_var('error'), $msg);
				return '';
			}
		}

		$_data = array();
		foreach ($files as $file) {
			@fn_copy(DIR_DOWNLOADS . $product_id . '/' . $file['file_path'], $path . '/' . $file['file_path']);
			if (!empty($file['preview_path'])) {
				@fn_copy(DIR_DOWNLOADS . $product_id . '/' . $file['preview_path'], $path . '/' . $file['preview_path']);
			}

			$_data[] = implode('#', (!empty($file['preview_path'])) ? array($file['file_path'], $file['preview_path']) : array($file['file_path']));
		}

		return implode(', ', $_data);
	}

	return '';
}

//
// Import product files
// @product_id 0- product ID
// @filename - file name
// @path - path to search files in
// @delete_files - flag - delete all product files before import
function fn_exim_import_file($product_id, $filename, $path, $delete_files = 'N')
{
	// Check if directory for storing files is exist
	if (!@is_dir(DIR_DOWNLOADS . $product_id) && fn_mkdir(DIR_DOWNLOADS . $product_id) == false) {
		$msg = str_replace('[directory]', DIR_DOWNLOADS . $product_id, fn_get_lang_var('text_cannot_create_directory'));
		fn_set_notification('E', fn_get_lang_var('error'), $msg);
		return false;
	}

	// Clean up the directory above if flag is set
	if ($delete_files == 'Y') {
		$file_ids = db_get_fields("SELECT file_id FROM ?:product_files WHERE product_id = ?i", $product_id);
		db_query("DELETE FROM ?:product_files WHERE product_id = ?i", $product_id);
		db_query("DELETE FROM ?:product_file_descriptions WHERE file_id IN (?n)", $file_ids);
		fn_rm(DIR_DOWNLOADS . $product_id, false);
	}

	// Check if we have several files
	$files = fn_explode(',', $filename);

	// Copy files
	foreach ($files as $file) {
		if (strpos($file, '#') !== false) {
			list($f, $pr) = fn_explode('#', $file);
		} else {
			$f = $file;
			$pr = '';
		}

		$file = fn_find_file($path, $f);
		if (!empty($file)) {
			$pr_res = false;
			list($_path, $_filename) = fn_generate_file_name(DIR_DOWNLOADS . $product_id . '/', basename($f));
			copy($file, $_path);
			if (!empty($pr)) {
				$file = fn_find_file($path, $pr);
				if (!empty($file)) {
					list($_prpath, $_prfilename) = fn_generate_file_name(DIR_DOWNLOADS . $product_id . '/', basename($pr));
					$pr_res = copy($file, $_prpath);
				}
			}

			$_data = array(
				'file_path' => basename($_filename),
				'file_size' => filesize(DIR_DOWNLOADS . $product_id . '/' . basename($_filename)),
				'preview_path' => !empty($pr_res) ? basename($_prfilename) : '',
				'preview_size' => !empty($pr_res) ? filesize(DIR_DOWNLOADS . $product_id . '/' . basename($_prfilename)) : 0,
				'product_id' => $product_id
			);

			$file_id = db_get_field("SELECT file_id FROM ?:product_files WHERE product_id = ?i AND file_path = ?s", $product_id, $_data['file_path']);
			if (!empty($file_id)) {
				db_query("UPDATE ?:product_files SET ?u WHERE file_id = ?i", $_data, $file_id);
			} else {
				$file_id = db_query("INSERT INTO ?:product_files ?e", $_data, $file_id);
				$_data = array(
					'file_id' => $file_id,
					'file_name' => basename($_filename)
				);

				foreach ((array)Registry::get('languages') as $_data['lang_code'] => $v) {
					db_query("INSERT INTO ?:product_file_descriptions ?e", $_data);
				}
			}

		}
	}
}

//
// Inserts ID of element to the csv file
// @item_id - id of an item
//
function fn_exim_post_item_id($item_id)
{
	return '{' . $item_id . '}';
}

//
// Gets element ID and updates the element name
// @element - element
//
function fn_exim_get_item_id(&$element)
{
	$item_id = '';
	if ($item_id = substr($element, 0, strpos($element, '}'))) {
		$element = substr($element, strpos($element, '}') + 1);
		$item_id = substr($item_id, 1);
	}

	return $item_id;
}

// Import preprocessor
function fn_exim_reset_inventory($reset_inventory)
{
	// Reset inventory to zero before import
	if ($reset_inventory == 'Y') {
		db_query("UPDATE ?:products SET amount = 0");
		db_query("UPDATE ?:product_options_inventory SET amount = 0");
	}
}
/**
 * Assign localizations to the product
 *
 * @param string $localization_ids - comma delimited list of localization IDs
 * @param string $lang_code - language code
 * @return string  - comma delimited list of localization names
 */
function fn_exim_get_localizations($localization_ids, $lang_code = '')
{
	$locs = db_get_fields("SELECT localization FROM ?:localization_descriptions WHERE FIND_IN_SET(localization_id, ?s) AND lang_code = ?s", $localization_ids, $lang_code);

	return implode(', ', $locs);
}

/**
 * Assign localizations to the product
 *
 * @param int $product_id Product ID
 * @param string $data - comma delimited list of localizations
 * @param string $lang_code - language code
 * @return boolean always true
 */
function fn_exim_set_localizations($product_id, $data, $lang_code = '')
{
	if (empty($data)) {
		db_query("UPDATE ?:products SET localization = ''");
		return true;
	}

	$loc_ids = db_get_fields("SELECT localization_id FROM ?:localization_descriptions WHERE localization IN (?a) AND lang_code = ?s", fn_explode(',', $data), $lang_code);

	$_data = array (
		'localization' => fn_create_set($loc_ids)
	);

	db_query('UPDATE ?:products SET ?u WHERE product_id = ?i', $_data, $product_id);

	return true;
}
}
?>
