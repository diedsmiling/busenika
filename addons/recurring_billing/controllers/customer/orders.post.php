<?php

//
// $Id: orders.pre.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($mode == 'details') {
	$order_info = $view->get_var('order_info');

	$subscriptions = db_get_array("SELECT subscription_id FROM ?:recurring_subscriptions WHERE FIND_IN_SET(?i, order_ids)", $order_info['order_id']);

	if (!empty($subscriptions) && !fn_subscription_is_paid($order_info['status'])) {
		fn_prepare_repay_data(empty($_REQUEST['payment_id']) ? 0 : $_REQUEST['payment_id'], $order_info, $auth, $view);
		$view->assign('subscription_pay', true);
	}
}

?>
