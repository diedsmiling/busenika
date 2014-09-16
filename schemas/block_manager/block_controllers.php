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
// $Id: block_controllers.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array (
	'products' => array (
		'view' => 'products'
	),
	'categories' => array(
	 	'view' => 'categories'
	 ),
	'pages' => 'pages',
	'index' => 'index',
	'checkout' => array (
		'cart' => 'cart',
		'checkout' => 'checkout',
		'summary' => 'checkout',
		'customer_info' => 'checkout',
		'complete' => 'order_landing_page'
	)
);

?>