<?php

//
// $Id: init.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

fn_register_hooks(
	'delete_gift_certificate',
	'get_order_info',
	'change_order_status',
	'get_product_data', 
	'add_to_cart',
	'get_status_params_definition',
	'delete_order'
);

?>