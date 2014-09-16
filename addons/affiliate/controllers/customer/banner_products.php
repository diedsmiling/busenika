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
// $Id: banner_products.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// View product details
//
if (!empty($_REQUEST['product_id'])) {
	$product = fn_get_product_data($_REQUEST['product_id'], $auth, CART_LANGUAGE);

	if (empty($product)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	fn_gather_additional_product_data($product, true, true);

	$view->assign('product', $product);
	$view->display('addons/affiliate/views/banner_products/view.tpl', true);
	exit;
}

?>