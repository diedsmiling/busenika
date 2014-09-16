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
// $Id: product_combinations.php 10069 2010-07-15 07:32:02Z alexions $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// Additional product images schema
//
$schema = array (
	'section' => 'products',
	'name' => fn_get_lang_var('product_combinations'),
	'pattern_id' => 'product_combinations',
	'key' => array('product_id'),
	'table' => 'product_options_inventory',
	'references' => array (
		'product_descriptions' => array (
			'reference_fields' => array('product_id' => '#key', 'lang_code' => '@lang_code'),
			'join_type' => 'LEFT',
		),
	),
	'range_options' => array (
		'selector_url' => 'products.manage',
		'object_name' => fn_get_lang_var('products'),
	),
	'options' => array (
		'lang_code' => array (
			'title' => 'language',
			'type' => 'languages'
		),
	),
	'import_skip_db_processing' => true,
	'export_fields' => array (
		'Product ID' => array (
			'required' => true,
			'alt_key' => true,
			'db_field' => 'product_id',
		),
		'Product name' => array (
			'required' => false,
			'table' => 'product_descriptions',
			'db_field' => 'product',
		),
		'Combination code' => array (
			'required' => false,
			'db_field' => 'product_code',
		),
		'Combination' => array (
			'required' => true,
			'db_field' => 'combination',
			'process_get' => array ('fn_exim_get_product_combination', '#key', '#this', '@lang_code'),
			'process_put' => array ('fn_exim_put_product_combination', '%Product ID%', '%Product name%', '%Combination code%', '#this', '%Amount%', '#counter', '@lang_code'),
		),
		'Amount' => array (
			'required' => false,
			'db_field' => 'amount',
		),
	),
);

function fn_exim_get_product_combination($product_id, $combination, $lang_code)
{
	$selected_options = fn_get_product_options_by_combination($combination);
	$options = fn_get_selected_product_options($product_id, $selected_options, $lang_code = CART_LANGUAGE);
	
	$return = array();
	
	if (!empty($options)) {
		foreach ($options as $option) {
			if (isset($selected_options[$option['option_id']])) {
				$return[] = $option['option_name'] . ': ' . $option['variants'][$selected_options[$option['option_id']]]['variant_name'];
			}
		}
	}
	
	return implode(', ', $return);
}

function fn_exim_put_product_combination($product_id, $product_name, $combination_code, $combination, $amount, &$counter, $lang_code = CART_LANGUAGE)
{
	$pair_delimiter = ':';
	$set_delimiter = ',';
	
	if (!empty($combination)) {
		// Get product_id
		$object_id = 0;
		if (!empty($product_id)) {
			$object_exists = db_get_field('SELECT COUNT(*) FROM ?:products WHERE product_id = ?i', $product_id);
			if ($object_exists) {
				$object_id = $product_id;
			}
		}
		
		if (empty($object_id) && !empty($product_name)) {
			$object_id = db_get_field('SELECT product_id FROM ?:product_descriptions WHERE product = ?s AND lang_code = ?s', $product_name, $lang_code);
		}
		
		if (empty($object_id)) {
			$counter['S']++;
			return false;
		}
		
		$options = explode($set_delimiter, $combination);
		if (!empty($options)) {
			$_combination = array();
			
			foreach ($options as $option_pair) {
				$pair = explode($pair_delimiter, $option_pair);
				if (is_array($pair)) {
					array_walk($pair, 'fn_trim_helper');
					$option_id = db_get_field("SELECT o.option_id FROM ?:product_options_descriptions as d INNER JOIN ?:product_options as o ON o.option_id = d.option_id AND o.product_id = ?i WHERE d.option_name = ?s AND d.lang_code = ?s LIMIT 1", $object_id, $pair[0], $lang_code);
					$variant_id = db_get_field("SELECT v.variant_id FROM ?:product_option_variants_descriptions as d INNER JOIN ?:product_option_variants as v ON v.variant_id = d.variant_id AND v.option_id = ?i WHERE d.variant_name = ?s AND d.lang_code = ?s LIMIT 1", $option_id, $pair[1], $lang_code);
					
					if (empty($option_id) || empty($variant_id)) {
						$counter['S']++;
						return false;
					}
					
					$_combination[$option_id] = $variant_id;
				}
			}
			
			$combination = fn_get_options_combination($_combination);
			$combination_hash = fn_generate_cart_id($object_id, array('product_options' => $_combination));
			
			$object_exists = db_get_field('SELECT COUNT(*) FROM ?:product_options_inventory WHERE combination_hash = ?i AND product_id = ?i', $combination_hash, $object_id);
			
			$_data = array(
				'product_id' => $object_id,
				'product_code' => $combination_code,
				'combination_hash' => $combination_hash,
				'combination' => $combination,
				'amount' => $amount,
			);
			
			if ($object_exists) {
				db_query('UPDATE ?:product_options_inventory SET ?u WHERE combination_hash = ?i', $_data, $combination_hash);
				fn_echo(fn_get_lang_var('updating') . ' ' . fn_get_lang_var('product_combinations') . '...');
				
				$counter['E']++;
				
			} else {
				db_query('INSERT INTO ?:product_options_inventory ?e', $_data);
				fn_echo(fn_get_lang_var('creating') . ' ' . fn_get_lang_var('product_combinations') . '...');
				
				$counter['N']++;
			}
			
			fn_echo('<b>' . $object_id . '</b>.<br />');
			
			return $combination;
		}
	}
	
	$counter['S']++;
	
	return false;
}

?>