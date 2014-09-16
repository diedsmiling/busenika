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
// $Id: gift_certificates.post.php 10402 2010-08-12 08:18:09Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

fn_define('GC_PRODUCTS_PER_PAGE', 5);

if ($mode == 'print') {

	$order_info = fn_get_order_info($_REQUEST['order_id']);

	if (isset($order_info['gift_certificates'][$_REQUEST['gift_cert_cart_id']])) {

		$stored_products = array();
		if (!empty($order_info['items'])) {
			foreach($order_info['items'] as $id => $v) {
				if (isset($v['extra']['parent']['certificate']) && $v['extra']['parent']['certificate'] == $_REQUEST['gift_cert_cart_id']) {
					$stored_products[$id] = $v;
				}
			}
		}

		fn_show_postal_card($order_info['gift_certificates'][$_REQUEST['gift_cert_cart_id']], $stored_products);
		exit;
	}
}

?>