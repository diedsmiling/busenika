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
// $Id: schema.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array(
	'products.update' => array(
		'func' => array('fn_get_product_name', '@product_id'),
		'icon' => 'product-item',
		'text' => 'product'
	),
	'orders.details' => array(
		'func' => array('fn_get_order_name', '@order_id'),
		'icon' => 'order-item',
		'text' => 'order'
	),
	'categories.update' => array(
		'func' => array('fn_get_category_name', '@category_id'),
		'text' => 'category'
	),
	'profiles.update' => array(
		'func' => array('fn_get_user_name', '@user_id'),
		'text' => 'user'
	),
	'usergroups.assign_privileges' => array(
		'func' => array('fn_get_usergroup_name', '@usergroup_id'),
		'text' => 'usergroup'
	),
	'shippings.update' => array(
		'func' => array('fn_get_shipping_name', '@shipping_id'),
		'text' => 'shipping_method'
	),
	'taxes.update' => array(
		'func' => array('fn_get_tax_name', '@tax_id'),
		'text' => 'tax'
	),
	'destinations.update' => array(
		'func' => array('fn_get_destination_name', '@destination_id'),
		'text' => 'destination'
	),
	'payments.manage' => array(
		'text' => 'payment_methods'
	),
	'pages.update' => array(
		'func' => array('fn_get_page_name', '@page_id'),
		'text' => 'page'
	)
);

?>