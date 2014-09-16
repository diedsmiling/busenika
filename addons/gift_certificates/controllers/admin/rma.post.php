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
// $Id: rma.post.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//
	// Creation gift certificate
	//	
	if ($mode == 'create_gift_certificate') {

		$change_return_status = $_REQUEST['change_return_status'];

		if (!empty($_REQUEST['accepted'])) {
			$total = 0;
			$return_info = fn_get_return_info($change_return_status['return_id']);
			foreach ((array)$_REQUEST['accepted'] as $item_id => $v) {
				if (isset($v['chosen']) && $v['chosen'] == 'Y') {
					$total += $v['amount'] * $return_info['items'][RETURN_PRODUCT_ACCEPTED][$item_id]['price'];
				}
			}

			if ($total > 0) {
				$certificate = fn_create_return_gift_certificate($return_info['order_id'], fn_format_price($total));
				
				if (!isset($return_info['extra']['gift_certificates'])) {
					$return_info['extra']['gift_certificates'] = array();
				}
				$return_info['extra']['gift_certificates'] = fn_array_merge($return_info['extra']['gift_certificates'], $certificate);

				$_data = array('extra' => serialize($return_info['extra']));

				db_query("UPDATE ?:rma_returns SET ?u WHERE return_id = ?i", $_data, $change_return_status['return_id']);
			}
		}

		return array(CONTROLLER_STATUS_REDIRECT, "rma.details?return_id=$change_return_status[return_id]");
	}
}

?>