<?php

//
// $Id: orders.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($mode == 'reorder') {

	$order_info = fn_get_order_info($_REQUEST['order_id']);

	if (!empty($order_info['gift_certificates'])) {
		$_SESSION['cart']['gift_certificates'] = $order_info['gift_certificates'];
	}
}

?>
