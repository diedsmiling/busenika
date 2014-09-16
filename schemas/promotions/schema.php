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
// $Id: schema.php 10365 2010-08-05 14:18:33Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['conditions'] = array (
	'price' => array (
		'operators' => array ('eq', 'neq', 'lte', 'gte', 'lt', 'gt'),
		'type' => 'input',
		'field' => 'base_price',
		'zones' => array(
			'catalog',
			'cart'
		)
	),
	'categories' => array (
		'operators' => array ('in', 'nin'),
		'type' => 'picker',
		'picker_props' => array (
			'picker' => 'pickers/categories_picker.tpl',
			'params' => array (
				'multiple' => true,
			),
		),
		'field_function' => array('array_keys', '@product.category_ids'),
		'zones' => array(
			'catalog',
			'cart'
		)
	),
	'products' => array (
		'operators' => array ('in', 'nin'),
		'type' => 'picker',
		'picker_props' => array (
			'picker' => 'pickers/products_picker.tpl',
			'params_cart' => array (
				'type' => 'table',
				'aoc' => true
			),
			'params_catalog' => array (
				'type' => 'links',
			),
		),
		'field_function' => array('fn_promotion_validate_product', '#this', '@product', '@cart_products'),
		'zones' => array(
			'catalog',
			'cart'
		)
	),
	'users' => array (
		'operators' => array ('in', 'nin'),
		'type' => 'picker',
		'picker_props' => array (
			'picker' => 'pickers/users_picker.tpl',
			'params' => array (
				'disable_no_item_text' => false,
			),
		),
		'field' => '@auth.user_id',
		'zones' => array(
			'catalog',
			'cart'
		)
	),
	'feature' => array (
		'operators' => array ('eq', 'neq', 'lte', 'gte', 'lt', 'gt', 'in', 'nin', 'cont', 'ncont'),
		'type' => 'mixed',
		'conditions_function' => array('fn_promotions_get_features'),
		'field_function' => array('fn_promotions_check_features', '#this', '@product'),
		'zones' => array(
			'catalog',
			'cart'
		)
	),
	'usergroup' => array (
		'operators' => array ('eq', 'neq'),
		'type' => 'select',
		'variants_function' => array('fn_get_simple_usergroups', 'C'),
		'field' => '@auth.usergroup_ids',
		'zones' => array('catalog', 'cart')
	),
	'country' => array (
		'operators' => array ('eq', 'neq'),
		'type' => 'select',
		'variants_function' => array('fn_get_simple_countries', true),
		'field' => '@cart.user_data.s_country',
		'zones' => array('cart')
	),
	'state' => array (
		'operators' => array ('eq', 'neq', 'in', 'nin'),
		'type' => 'input',
		'field' => '@cart.user_data.s_state',
		'zones' => array('cart')
	),
	'zip_postal_code' => array (
		'operators' => array ('eq', 'neq', 'cont', 'ncont', 'in', 'nin'),
		'type' => 'input',
		'field' => '@cart.user_data.s_zipcode',
		'zones' => array('cart')
	),
	'subtotal' => array (
		'operators' => array ('eq', 'neq', 'lte', 'gte', 'lt', 'gt', 'in', 'nin'),
		'type' => 'input',
		'field' => 'subtotal',
		'zones' => array('cart')
	),
	'products_number' => array (
		'operators' => array ('eq', 'neq', 'lte', 'gte', 'lt', 'gt', 'in', 'nin'),
		'type' => 'input',
		'field_function' => array('fn_get_products_amount', '@cart', '@cart_products', 'C'),
		'zones' => array('cart')
	),
	'total_weight' => array (
		'operators' => array ('eq', 'neq', 'lte', 'gte', 'lt', 'gt', 'in', 'nin'),
		'type' => 'input',
		'field_function' => array('fn_get_products_weight', '@cart', '@cart_products', 'C'),
		'zones' => array('cart')
	),
	'payment' => array (
		'operators' => array ('eq', 'neq'),
		'type' => 'select',
		'variants_function' => array ('fn_get_simple_payment_methods'),
		'field' => 'payment_id',
		'zones' => array('cart')
	),
	'coupon_code' => array (
		'operators' => array ('eq', 'cont', 'in'),
		'type' => 'input',
		'field_function' => array('fn_promotion_validate_coupon', '#this', '@cart', '#id'),
		'zones' => array('cart'),
		'applicability' => array( // applicable for "positive" groups only
			'group' => array(
				'set_value' => true
			),
		),
	),
	'number_of_usages' => array (
		'operators' => array ('lte', 'lt'),
		'type' => 'input',
		'field_function' => array('fn_promotion_get_dynamic', '#id', '#this', 'number_of_usages', '@cart'),
		'zones' => array('cart')
	),
	'once_per_customer' => array (
		'type' => 'statement',
		'field_function' => array('fn_promotion_get_dynamic', '#id', '#this', 'once_per_customer', '@cart', '@auth'),
		'zones' => array('cart')
	),
	'auto_coupons' => array (
		'type' => 'list',
		'field_function' => array('fn_promotion_validate_coupon', '#this', '@cart', '#id'),
		'zones' => array('cart'),
		'applicability' => array( // applicable for "positive" groups only
			'group' => array(
				'set_value' => true
			),
		),
	),
);

$schema['bonuses'] = array (
	'product_discount' => array (
		'function' => array('fn_promotion_apply_catalog_rule', '#this', '@product', '@auth'),
		'discount_bonuses' => array('to_percentage', 'by_percentage', 'to_fixed', 'by_fixed'),
		'zones' => array('catalog'),
	),
	'order_discount' => array (
		'function' => array('fn_promotion_apply_cart_rule', '#this', '@cart', '@auth', '@cart_products'),
		'discount_bonuses' => array('to_percentage', 'by_percentage', 'to_fixed', 'by_fixed'),
		'zones' => array('cart'),
	),
	'discount_on_products' => array (
		'type' => 'picker',
		'picker_props' => array (
			'picker' => 'pickers/products_picker.tpl',
			'params' => array (
				'type' => 'links',
			),
		),
		'function' => array('fn_promotion_apply_cart_rule', '#this', '@cart', '@auth', '@cart_products'),
		'discount_bonuses' => array('to_percentage', 'by_percentage', 'to_fixed', 'by_fixed'),
		'zones' => array('cart'),
	),
	'discount_on_categories' => array (
		'type' => 'picker',
		'picker_props' => array (
			'picker' => 'pickers/categories_picker.tpl',
			'params' => array (
				'multiple' => true,
			),
		),
		'function' => array('fn_promotion_apply_cart_rule', '#this', '@cart', '@auth', '@cart_products'),
		'discount_bonuses' => array('to_percentage', 'by_percentage', 'to_fixed', 'by_fixed'),
		'zones' => array('cart'),
	),
	'give_usergroup' => array (
		'type' => 'select',
		'variants_function' => array('fn_get_simple_usergroups', 'C'),
		'function' => array('fn_promotion_apply_cart_rule', '#this', '@cart', '@auth', '@cart_products'),
		'zones' => array('cart'),
	),
	'give_coupon' => array (
		'type' => 'select',
		'function' => array('fn_promotion_apply_cart_rule', '#this', '@cart', '@auth', '@cart_products'),
		'variants_function' => array('fn_get_promotions', array('zone' => 'cart', 'auto_coupons' => true, 'simple' => true)),
		'zones' => array('cart'),
	),
	'free_shipping' => array (
		'type' => 'select',
		'function' => array('fn_promotion_apply_cart_rule', '#this', '@cart', '@auth', '@cart_products'),
		'variants_function' => array('fn_get_shippings', true),
		'zones' => array('cart'),
	),
	'free_products' => array (
		'type' => 'picker',
		'picker_props' => array (
			'picker' => 'pickers/products_picker.tpl',
			'params' => array (
				'type' => 'table',
				'aoc' => true
			),
		),
		'function' => array('fn_promotion_apply_cart_rule', '#this', '@cart', '@auth', '@cart_products'),
		'zones' => array('cart'),
	),
);

?>