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
// $Id: objects.php 7050 2009-03-16 13:50:26Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }


// revisions init
$schema = array(
	'descriptions' => array (
		'products' => array (
			'timestamp' => 'created_date',
			'avail_since' => 'available_since',
			'tax_ids' => 'taxes',
		),

		'products_categories' => array (
			'category_id' => 'category'
		),
	),
	'fields_data' => array (
		'*' => array (
			'timestamp' => array ('fn_date_format', '#value', Registry::get('settings.Appearance.date_format')),
			'date' => array ('fn_date_format', '#value', Registry::get('settings.Appearance.date_format')),
		),

		'products' => array (
			'tax_ids' => array ('fn_revisions_get_taxes', '#value'),
			'avail_since' => array ('fn_date_format', '#value', Registry::get('settings.Appearance.date_format')),
			'product_code' => array(),
			'product_type' => array(),
			'status' => array(),
			'list_price' => array(),
			'amount' => array(),
			'weight' => array(),
			'length' => array(),
			'width' => array(),
			'height' => array(),
			'shipping_freight' => array(),
			'low_avail_limit' => array(),
			'is_edp' => array(),
			'edp_shipping' => array(),
			'unlimited_download' => array(),
			'tracking' => array(),
			'free_shipping' => array(),
			'feature_comparison' => array(),
			'zero_price_action' => array(),
			'is_pbp' => array(),
			'is_op' => array(),
			'is_oper' => array(),
			'supplier_id' => array(),
			'is_returnable' => array(),
			'return_period' => array(),
			'avail_since' => array ('fn_date_format', '#value', Registry::get('settings.Appearance.date_format')),
			'buy_in_advance' => array(),
			'localization' => array(),
			'min_qty' => array(),
			'max_qty' => array(),
			'qty_step' => array(),
			'list_qty_count' => array(),
			'tax_ids' => array(),
			'age_verification' => array(),
			'age_limit' => array(),
		),

		'products_categories' => array (
			'category_id' => array ('fn_get_category_name', '#value'),
		),

		'pages' => array (
			'avail_from_timestamp' => array ('fn_date_format', '#value', Registry::get('settings.Appearance.date_format')),
			'avail_till_timestamp' => array ('fn_date_format', '#value', Registry::get('settings.Appearance.date_format')),
			'id_path' => array(),
			'status' => array(),
			'registred_only' => array(),
			'page_type' => array(),
			'position' => array(),
			'usergroup_id' => array(),
			'link' => array(),
			'localization' => array(),
			'new_window' => array(),
			'related_ids' => array(),
			'use_avail_period' => array(),
		),

		'categories' => array (
			'parent_id' => array ('fn_get_category_name', '#value'),
			'id_path' => array(),
			'owner_id' => array(),
			'usergroup_id' => array(),
			'status' => array(),
			'product_count' => array(),
			'position' => array(),
			'is_op' => array(),
			'localization' => array(),
			'age_verification' => array(),
			'age_limit' => array(),
			'parent_age_verification' => array(),
			'parent_age_limit' => array(),
		),

		'category_descriptions' => array (
			'category' => array(),
			'description' => array(),
			'meta_keywords' => array(),
			'meta_description' => array(),
			'page_title' => array(),
			'age_warning_message' => array(),
		),

		'product_descriptions' => array (
			'product' => array(),
			'shortname' => array(),
			'short_description' => array(),
			'full_description' => array(),
			'meta_keywords' => array(),
			'meta_description' => array(),
			'page_title' => array(),
			'age_warning_message' => array(),
			'search_words' => array(),
		),

		'product_options' => array (
			'status' => array (),
			'option_type' => array (),
			'inventory' => array (),
		),

		'product_options_inventory' => array (
			'combination_hash' => array (),
			'combination' => array (),
			'amount' => array (),
			'temp' => array (),
		),

		'product_options_exceptions' => array (
			'combination' => array (),
		),

		'product_options_descriptions' => array (
			'option_name' => array (),
			'option_text' => array (),
		),

		'product_option_variants' => array (
			'position' => array (),
			'modifier' => array (),
			'modifier_type' => array (),
			'weight_modifier' => array (),
			'weight_modifier_type' => array (),
			'point_modifier' => array (),
			'point_modifier_type' => array (),
			'status' => array (),
		),

		'product_option_variants_descriptions' => array (
			'variant_name' => array (),
		),

		'product_prices' => array (
			'price' => array (),
			'lower_limit' => array (),
			'usergroup_id' => array (),
		),

		'product_files' => array (
			'file_path' => array (),
			'preview_path' => array (),
			'file_size' => array (),
			'preview_size' => array (),
			'agreement' => array (),
			'max_downloads' => array (),
			'total_downloads' => array (),
			'activation_type' => array (),
			'position' => array (),
			'status' => array (),
		),

		'product_file_descriptions' => array (
			'file_name' => array (),
			'license' => array (),
			'readme' => array (),
		),

		'product_features_values' => array (
			'value' => array (),
			'value_int' => array (),
		),

		'page_descriptions' => array (
			'page' => array (),
			'description' => array (),
			'meta_keywords' => array (),
			'meta_description' => array (),
			'page_title' => array (),
		),
	),
);


function fn_revisions_get_taxes($value, $lang_code = CART_LANGUAGE)
{
	$taxes = fn_get_tax_name($value, $lang_code, true);

	return empty($taxes) ? '' : implode(', ', $taxes);
}

?>