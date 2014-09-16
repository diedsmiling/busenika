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
// $Id: buy_together.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	fn_trusted_vars('item_data');
	
	if ($mode == 'update') {
		fn_buy_together_update_chain($_REQUEST['item_id'], $_REQUEST['product_id'], $_REQUEST['item_data'], $auth, DESCR_SL);
		
		return array(CONTROLLER_STATUS_OK, "products.update?product_id=" . $_REQUEST['product_id'] . "&selected_section=buy_together");
	}

	return;
}

if ($mode == 'update') {

	$params = array(
		'chain_id' => $_REQUEST['chain_id'],
		'simple' => true,
		'full_info' => true,
	);
	
	$chain = fn_buy_together_get_chains($params, array(), DESCR_SL);

	$view->assign('item', $chain);
	
} elseif ($mode == 'delete') {
	if (!empty($_REQUEST['chain_id'])) {
		$product_id = db_get_field("SELECT product_id FROM ?:buy_together WHERE chain_id = ?i", $_REQUEST['chain_id']);
		
		db_query('DELETE FROM ?:buy_together WHERE chain_id = ?i', $_REQUEST['chain_id']);
		db_query('DELETE FROM ?:buy_together_descriptions WHERE chain_id = ?i', $_REQUEST['chain_id']);
		
		return array(CONTROLLER_STATUS_REDIRECT, "products.update?product_id=$product_id&selected_section=buy_together");
	}
}

?>