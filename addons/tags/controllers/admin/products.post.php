<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2009 Simbirsk Technologies Ltd. All rights reserved.    *
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
// $Id: products.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	return;
}

//
// View product details
//
if ($mode == 'add' && Registry::get('addons.tags.tags_for_products') == 'Y') {
	Registry::set('navigation.tabs.tags', array(
		'title' => fn_get_lang_var('tags'),
		'js' => true
	));

} elseif ($mode == 'update' && Registry::get('addons.tags.tags_for_products') == 'Y') {
	Registry::set('navigation.tabs.tags', array(
		'title' => fn_get_lang_var('tags'),
		'js' => true
	));
	
	$product_data = $view->get_var('product_data');

	fn_gather_additional_product_data($product_data, false, false, false, false, false);

	$view->assign('product_data', $product_data);
}

?>
