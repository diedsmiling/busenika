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
// $Id: order_management.post.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$cart = & $_SESSION['cart'];
$customer_auth = & $_SESSION['customer_auth'];

if (!empty($customer_auth['user_id']) && !isset($customer_auth['points'])) {
	$customer_auth['points'] = db_get_field("SELECT data FROM ?:user_data WHERE type='W' AND user_id = ?i", $customer_auth['user_id']);
	$view->assign('customer_auth', $customer_auth);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//
	// Update totals
	//
	if ($mode == 'update_totals') {	
		if (isset($_REQUEST['points_to_use'])) {
			$points_to_use = intval($_REQUEST['points_to_use']);		
			if (!empty($points_to_use) && abs($points_to_use) == $points_to_use) {
				$cart['points_info']['in_use']['points'] = $points_to_use;
			}
		}
	}

	return;
}

//
// Display totals
//
if ($mode == 'totals') {

	$view->assign('user_points', fn_get_user_additional_data(POINTS, $customer_auth['user_id']) + (!empty($cart['previous_points_info']['in_use']['points']) ? $cart['previous_points_info']['in_use']['points'] : 0));

//
// Delete point in use from the cart
//
} elseif ($mode == 'delete_points_in_use') {
	unset($cart['points_info']['in_use']);

	return array(CONTROLLER_STATUS_REDIRECT, "order_management.totals");
}

?>