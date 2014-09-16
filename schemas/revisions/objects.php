<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2009 Simbirsk Technologies Ltd. All rights reserved.    *
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
// $Id: objects.php 8091 2009-10-19 08:29:55Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }


// revisions init
$schema = array(
	'category' => array(
		'primary_key' => 'category_id',
		'edit_link' => 'dispatch=categories.update&category_id=',
		'manage_link' => 'dispatch=categories.manage',
		'manage_name' => 'manage_categories',
		'has_images' => true,
		'has_attachments' => false,
		'database' => array(
			'categories' => array(
				'keys' => array('category_id'),
				'parent' => array(),
				'children' => array(),
				'is_auto' => true,
				'auto_key' => 'category_id'
			),
			'category_descriptions' => array(
				'keys' => array('category_id', 'lang_code'),
				'parent' => array(),
				'children' => array(),
				'is_auto' => false
			),
		),
		'description' => array (
			'title' => 'category',
			'title_s' => 'category_s',
			'table' => 'category_descriptions',
			'field' => 'category',
			'object_name_function' => 'fn_get_category_name',
			'lang_code' => true
		),
	),

	'product' => array (
		'primary_key' => 'product_id',
		'edit_link' => 'dispatch=products.update&product_id=',
		'manage_link' => 'dispatch=products.manage',
		'manage_name' => 'manage_products',
		'has_images' => true,
		'image_objects' => array (
			'product_option' => array (
				'main_table' => 'product_options_inventory',
				'key' => 'combination_hash',
			),
		),
		'has_attachments' => true,
		'database' => array (
			'products' => array (
				'keys' => array ('product_id'),
				'parent' => array (),
				'children' => array (),
				'is_auto' => true,
				'auto_key' => 'product_id'
			),

			'product_descriptions' => array (
				'keys' => array ('product_id', 'lang_code'),
				'parent' => array (),
				'children' => array (),
				'is_auto' => false
			),

			'products_categories' => array (
				'keys' => array ('product_id', 'category_id'),
				'sort_by' => array('link_type', 'category_id'),
				'sort_order' => array('DESC', 'ASC'),
				'parent' => array (),
				'children' => array (),
				'is_auto' => false
			),

			'product_global_option_links' => array (
				'keys' => array ('product_id', 'option_id'),
				'parent' => array (),
				'children' => array (),
				'is_auto' => false
			),

			'product_options' => array (
				'keys' => array ('option_id'),
				'sort_by' => array('option_id'),
				'sort_order' => array('DESC'),
				'parent' => array (),
				'children' => array (
					array (
						'table' => 'product_options_descriptions',
						'key' => 'option_id'
					),
					array (
						'table' => 'product_option_variants',
						'key' => 'option_id'
					)
				),
				'is_auto' => true,
				'auto_key' => 'option_id',
				'skip_revision' => true
			),

			'product_options_inventory' => array (
				'keys' => array ('product_id', 'combination'),
				'parent' => array (),
				'children' => array (),
				'is_auto' => false,
			),

			'product_options_exceptions' => array (
				'keys' => array ('exception_id'),
				'parent' => array (),
				'children' => array (),
				'is_auto' => true,
				'auto_key' => 'exception_id'
			),

			'product_options_descriptions' => array (
				'keys' => array ('option_id', 'lang_code'),
				'sort_by' => array('option_id'),
				'sort_order' => array('DESC'),
				'parent' => array (
					'table' => 'product_options',
					'key' => 'option_id'
				),
				'children' => array (),
				'is_auto' => false
			),

			'product_option_variants' => array (
				'keys' => array ('variant_id'),
				'sort_by' => array('variant_id'),
				'sort_order' => array('DESC'),
				'parent' => array (
					'table' => 'product_options',
					'key' => 'option_id'
				),
				'children' => array (
					array (
						'table' => 'product_option_variants_descriptions',
						'key' => 'variant_id'
					)
				),
				'is_auto' => true,
				'auto_key' => 'variant_id'
			),

			'product_option_variants_descriptions' => array (
				'keys' => array ('variant_id', 'lang_code'),
				'sort_by' => array('variant_id'),
				'sort_order' => array('DESC'),
				'parent' => array (
					'table' => 'product_option_variants',
					'key' => 'variant_id',
				),
				'children' => array (),
				'is_auto' => false
			),

			'product_prices' => array (
				'keys' => array ('product_id', 'lower_limit', 'usergroup_id'),
				'parent' => array (),
				'children' => array (),
				'is_auto' => false,
			),

			'product_files' => array (
				'keys' => array ('file_id'),
				'parent' => array (),
				'children' => array (
					array (
						'table' => 'product_file_descriptions',
						'key' => 'file_id'
					)
				),
				'is_auto' => true,
				'auto_key' => 'file_id'
			),

			'product_file_descriptions' => array (
				'keys' => array ('file_id', 'lang_code'),
				'parent' => array (
					'table' => 'product_files',
					'key' => 'file_id',
				),
				'children' => array (),
				'is_auto' => false
			),

			'product_features_values' => array (
				'keys' => array ('product_id', 'feature_id', 'variant_id'),
				'parent' => array (),
				'children' => array (),
				'is_auto' => false,
			),
		),
		'description' => array(
			'title' => 'product',
			'title_s' => 'product_s',
			'table' => 'product_descriptions',
			'field' => 'product',
			'object_name_function' => 'fn_get_product_name',
			'lang_code' => true
		),
	),

	'pages' => array(
		'primary_key' => 'page_id',
		'edit_link' => 'dispatch=pages.update&page_id=',
		'manage_link' => 'dispatch=pages.manage',
		'manage_name' => 'manage_pages',
		'has_images' => false,
		'has_attachments' => false,
		'database' => array(
			'pages' => array (
				'keys' => array ('page_id'),
				'parent' => array (),
				'children' => array (),
				'is_auto' => true,
				'auto_key' => 'page_id'
			),

			'page_descriptions' => array (
				'keys' => array ('page_id', 'lang_code'),
				'parent' => array (),
				'children' => array (),
				'is_auto' => false
			),
		),
		'description' => array(
			'title' => 'page',
			'title_s' => 'page_s',
			'table' => 'page_descriptions',
			'field' => 'page',
			'object_name_function' => 'fn_get_page_name',
			'lang_code' => true
		),
	),
);

?>