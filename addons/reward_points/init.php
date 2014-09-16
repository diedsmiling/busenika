<?php

//
// $Id: init.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

fn_register_hooks(
	'place_order',
	'get_order_info', 
	'change_order_status', 
	'clone_product', 
	array('calculate_cart', 300), 
	'get_cart_product_data', 
	'get_additional_product_data', 
	'get_user_info', 
	'get_product_options', 
	'get_selected_product_options', 
	'apply_option_modifiers', 
	'get_external_discounts', 
	'form_cart', 
	'allow_place_order', 
	'rma_recalculate_order',
	'user_init',
	'get_users',
	'get_orders',
	'get_products',
	'get_product_data',
	'update_product',
	'update_category',
	'global_update',
	'delete_order'
);

?>