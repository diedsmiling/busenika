<?php

//
// $Id: init.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

fn_register_hooks(
	'update_product',
	'delete_product',
	'update_category',
	'delete_category',
	'delete_order',
	'update_news',
	'delete_news',
	'update_page',
	'delete_page',
	'update_event',
	'delete_event',
	'clone_product',
	'get_product_data',
	'get_products',
	'get_categories',
	'get_pages'
);

?>
