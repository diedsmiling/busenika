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
// $Id: shipments.php 9820 2010-06-21 11:31:45Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$suffix = '.manage';

	if ($mode == 'add' && !empty($_REQUEST['shipment_data'])) {
		if (!empty($_REQUEST['shipment_data']['products']) && fn_check_shipped_products($_REQUEST['shipment_data']['products'])) {
			$order_info = fn_get_order_info($_REQUEST['shipment_data']['order_id'], false, true, true);
			$shipment_data = $_REQUEST['shipment_data'];
			$shipment_data['timestamp'] = time();
			
			$shipment_id = db_query("INSERT INTO ?:shipments ?e", $shipment_data);

			foreach ($_REQUEST['shipment_data']['products'] as $key => $amount) {
				if (isset($order_info['items'][$key])) {
					$amount = intval($amount);
					
					if ($amount > ($order_info['items'][$key]['amount'] - $order_info['items'][$key]['shipped_amount'])) {
						$amount = $order_info['items'][$key]['amount'] - $order_info['items'][$key]['shipped_amount'];
					}
					
					$order_info['items'][$key]['amount'] = $amount;
				}
				
				if ($amount == 0) {
					continue;
				}
				
				$_data = array(
					'item_id' => $key,
					'shipment_id' => $shipment_id,
					'order_id' => $_REQUEST['shipment_data']['order_id'],
					'product_id' => $order_info['items'][$key]['product_id'],
					'amount' => $amount,
				);
				
				db_query("INSERT INTO ?:shipment_items ?e", $_data);
			}

			$force_notification = fn_get_notification_rules($_REQUEST);
			if (!empty($force_notification['C'])) {
				$shipment = array(
					'shipment_id' => $shipment_id,
					'timestamp' => $shipment_data['timestamp'],
					'shipping' => db_get_field('SELECT shipping FROM ?:shipping_descriptions WHERE shipping_id = ?i', $shipment_data['shipping_id']),
					'tracking_number' => $shipment_data['tracking_number'],
					'carrier' => $shipment_data['carrier'],
					'comments' => $shipment_data['comments'],
					'items' => $_REQUEST['shipment_data']['products'],
				);
				
				$view_mail->assign('shipment', $shipment);
				$view_mail->assign('order_info', $order_info);
				fn_send_mail($order_info['email'], Registry::get('settings.Company.company_orders_department'), 'shipments/shipment_products_subj.tpl', 'shipments/shipment_products.tpl', '', $order_info['lang_code']);
			}
			
			if (!empty($shipment_data['order_status'])) {
				fn_change_order_status($_REQUEST['shipment_data']['order_id'], $shipment_data['order_status']);
			}
			
			fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('shipment_has_been_created'));
			
		} else {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('products_for_shipment_not_selected'));
		}
		
		$suffix = '.details?order_id=' . $_REQUEST['shipment_data']['order_id'];
		
	} elseif ($mode == 'packing_slip' && !empty($_REQUEST['shipment_ids'])) {
		$html = array();
		$params = $_REQUEST;
		
		foreach ($params['shipment_ids'] as $k => $v) {
			list($shipment, $order_info) = fn_get_packing_info($v);
			
			$view_mail->assign('order_info', $order_info);
			$view_mail->assign('shipment', $shipment);
			
			if (DISPATCH_EXTRA == 'pdf') {
				$html[] = $view_mail->display('orders/print_packing_slip.tpl', false);
			} else {
				$view_mail->display('orders/print_packing_slip.tpl');
				echo "<div style='page-break-before: always;'>&nbsp;</div>";
			}
		}
	
		exit;
		
	} elseif ($mode == 'delete' && !empty($_REQUEST['shipment_ids'])) {
		db_query('DELETE FROM ?:shipments WHERE shipment_id IN (?a)', $_REQUEST['shipment_ids']);
		db_query('DELETE FROM ?:shipment_items WHERE shipment_id IN (?a)', $_REQUEST['shipment_ids']);
		
		if (!empty($_REQUEST['redirect_url'])) {
			return array(CONTROLLER_STATUS_REDIRECT, $_REQUEST['redirect_url']);
		}
	}
	
	return array(CONTROLLER_STATUS_OK, 'orders' . $suffix);
}

$params = $_REQUEST;

if ($mode == 'details') {
	if (empty($params['order_id']) && empty($params['shipment_id'])) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}
	
	if (!empty($params['shipment_id'])) {
		$params['order_id'] = db_get_field('SELECT ?:shipment_items.order_id FROM ?:shipment_items WHERE ?:shipment_items.shipment_id = ?i', $params['shipment_id']);
	}
	
	$shippings = db_get_array("SELECT a.shipping_id, a.min_weight, a.max_weight, a.position, a.status, b.shipping, b.delivery_time, a.usergroup_ids FROM ?:shippings as a LEFT JOIN ?:shipping_descriptions as b ON a.shipping_id = b.shipping_id AND b.lang_code = ?s WHERE a.status = ?s ORDER BY a.position", DESCR_SL, 'A');

	$order_info = fn_get_order_info($params['order_id'], false, true, true);
	if (empty($order_info)) {
		return array(CONTROLLER_STATUS_NO_PAGE);	
	}
	if (!empty($params['shipment_id'])) {
		$params['advanced_info'] = true;
		
		list($shipment, $search, $total) = fn_get_shipments_info($params);
		
		if (!empty($shipment)) {
			$shipment = array_pop($shipment);
			
			foreach ($order_info['items'] as $item_id => $item) {
				if (isset($shipment['items'][$item_id])) {
					$order_info['items'][$item_id]['amount'] = $shipment['items'][$item_id];
				} else {
					$order_info['items'][$item_id]['amount'] = 0;
				}
			}
		} else {
			$shipment = array();
		}
		
		$view->assign('shipment', $shipment);
	}
	
	fn_add_breadcrumb(fn_get_lang_var('shipments'), 'shipments.manage.reset_view');
	fn_add_breadcrumb(fn_get_lang_var('search_results'), "shipments.manage.last_view");
	
	$view->assign('shippings', $shippings);
	$view->assign('order_info', $order_info);
	
} elseif ($mode == 'manage') {
	list($shipments, $search, $totals) = fn_get_shipments_info($params);
	
	$view->assign('shipments', $shipments);
	$view->assign('search', $search);
	$view->assign('totals', $totals);
	
} elseif ($mode == 'packing_slip' && !empty($_REQUEST['shipment_ids'])) {
	$html = array();
	$params = $_REQUEST;
	
	foreach ($params['shipment_ids'] as $k => $v) {
		list($shipment, $order_info) = fn_get_packing_info($v);
		
		$view_mail->assign('order_info', $order_info);
		$view_mail->assign('shipment', $shipment);
		
		if (DISPATCH_EXTRA == 'pdf') {
			$html[] = $view_mail->display('orders/print_packing_slip.tpl', false);
		} else {
			$view_mail->display('orders/print_packing_slip.tpl');
			echo "<div style='page-break-before: always;'>&nbsp;</div>";
		}
	}

	exit;
	
} elseif ($mode == 'delete' && !empty($_REQUEST['shipment_ids']) && is_array($_REQUEST['shipment_ids'])) {
	$shipment_ids = implode(',', $_REQUEST['shipment_ids']);
	
	db_query('DELETE FROM ?:shipments WHERE shipment_id IN (?a)', $shipment_ids);
	db_query('DELETE FROM ?:shipment_items WHERE shipment_id IN (?a)', $shipment_ids);
	
	return array(CONTROLLER_STATUS_OK, 'shipments.manage');
}

function fn_get_packing_info($shipment_id)
{
	$params['advanced_info'] = true;
	$params['shipment_id'] = $shipment_id;
	
	list($shipment, $search, $total) = fn_get_shipments_info($params);
	
	if (!empty($shipment)) {
		$shipment = array_pop($shipment);
		
		$order_info = fn_get_order_info($shipment['order_id'], false, true, true);
		$shippings = db_get_array("SELECT a.shipping_id, a.min_weight, a.max_weight, a.position, a.status, b.shipping, b.delivery_time, a.usergroup_ids FROM ?:shippings as a LEFT JOIN ?:shipping_descriptions as b ON a.shipping_id = b.shipping_id AND b.lang_code = ?s ORDER BY a.position", DESCR_SL);
		
		$_products = db_get_array("SELECT item_id, SUM(amount) AS amount FROM ?:shipment_items WHERE order_id = ?i GROUP BY item_id", $shipment['order_id']);
		$shipped_products = array();
		
		if (!empty($_products)) {
			foreach ($_products as $_product) {
				$shipped_products[$_product['item_id']] = $_product['amount'];
			}
		}
		
		foreach ($order_info['items'] as $k => $oi) {
			if (isset($shipped_products[$k])) {
				$order_info['items'][$k]['shipment_amount'] = $oi['amount'] - $shipped_products[$k];
			} else {
				$order_info['items'][$k]['shipment_amount'] = $order_info['items'][$k]['amount'];
			}
			
			if (isset($shipment['items'][$k])) {
				$order_info['items'][$k]['amount'] = $shipment['items'][$k];
			} else {
				$order_info['items'][$k]['amount'] = 0;
			}
		}
	} else {
		$shipment = $order_info = array();
	}
	
	return array($shipment, $order_info);
}

function fn_check_shipped_products($products)
{
	$allow = true;
	$total_amount = 0;
	
	if (!empty($products) && is_array($products)) {
		foreach ($products as $product) {
			$total_amount += empty($product['amount']) ? 0 : $product['amount'];
		}
		
		if ($total_amount == 0) {
			$allow = false;
		}
		
	} else {
		$allow = false;
	}
	
	return $allow;
}

?>