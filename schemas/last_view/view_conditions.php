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
// $Id: view_conditions.php 9672 2010-05-31 12:55:46Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array (
		'products' => array (
			'func' => 'fn_get_products',
			'item_id' => 'product_id'
		),
		'profiles' => array (
			'func' => 'fn_get_users',
			'item_id' => 'user_id',
			'auth' => true
		),
		'orders' => array (
			'update_mode' => 'details',
			'func' => 'fn_get_orders',
			'item_id' => 'order_id'
		),
		'shipments' => array (
			'update_mode' => 'details',
			'func' => 'fn_get_shipments_info',
			'item_id' => 'shipment_id'
		)
	);

?>