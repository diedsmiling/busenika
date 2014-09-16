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
// $Id: products.pre.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($mode == 'options') {
		if (!empty($_REQUEST['event_products']) && !empty($_REQUEST['appearance']['events'])) {
			$event_data['products'] = db_get_hash_array("SELECT * FROM ?:giftreg_event_products LEFT JOIN ?:product_descriptions ON ?:product_descriptions.product_id = ?:giftreg_event_products.product_id AND ?:product_descriptions.lang_code = ?s WHERE event_id = ?i", 'item_id', CART_LANGUAGE, $_REQUEST['appearance']['event_id']);
	
			foreach ($event_data['products'] as $k => $v) {
				$event_data['products'][$k]['extra'] = $event_data['products'][$k]['selected_options'] = @$_REQUEST['event_products'][$k]['product_options'];
				$product_options = $event_data['products'][$k]['extra'];
				$event_data['products'][$k]['product_options'] = fn_get_selected_product_options($v['product_id'], $product_options, CART_LANGUAGE);
				$event_data['products'][$k]['original_price'] = $event_data['products'][$k]['price'] = fn_get_product_price($v['product_id'], 1, $auth);
				$event_data['products'][$k]['avail_amount'] = $v['amount'] - $v['ordered_amount'];
				
				fn_gather_additional_product_data($event_data['products'][$k], true, false, true, true);
			}
			
			$view->assign('event_id', $_REQUEST['appearance']['event_id']);
			$view->assign('event_data', $event_data);
			
			$view->display('addons/gift_registry/views/events/components/event_products.tpl');
			exit;
		}
	}
}

?>