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
// $Id: products.post.php 8238 2009-11-16 14:30:36Z alexions $

if ( !defined('AREA') ) { die('Access denied'); }

if ($mode == 'view' || $mode == 'options') {
	if (!empty($_REQUEST['cart_id'])) {
		$cart = & $_SESSION['cart'];
		if (isset($cart['products'][$_REQUEST['cart_id']]['extra']['recurring_plan_id'])) {
			Registry::set('recurring_plan_id', $cart['products'][$_REQUEST['cart_id']]['extra']['recurring_plan_id']);
		}
	}
}

?>