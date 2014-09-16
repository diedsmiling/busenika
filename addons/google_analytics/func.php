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
// $Id: func.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_google_analytics_change_order_status($status_to, $status_from, $order_info)
{
	if (Registry::get('addons.google_analytics.track_ecommerce') == 'N') {
		return false;
	}

	$order_statuses = fn_get_statuses(STATUSES_ORDER, false, true, true);

	if ($order_statuses[$status_to]['inventory'] == 'D' && $order_statuses[$status_from]['inventory'] == 'I') { // decrease amount

		fn_google_anaylitics_send(Registry::get('addons.google_analytics.tracking_code'), $order_info, false);

	} elseif ($order_statuses[$status_to]['inventory'] == 'I' && $order_statuses[$status_from]['inventory'] == 'D') { // increase amount

		fn_google_anaylitics_send(Registry::get('addons.google_analytics.tracking_code'), $order_info, true);

	}
}

function fn_google_anaylitics_send($account, $order_info, $refuse = false)
{
	$_uwv = '1';

	$url = "http://www.google-analytics.com/__utm.gif";

	$sign = ($refuse == true) ? '-' : '';
	$cookies = fn_google_analytics_cookies();

	// Transaction request
	// http://www.google-analytics.com/__utm.gif?utmwv=1&utmt=tran&utmn=262780020&utmtid=80&utmtto=3.96&utmttx=0&utmtsp=0.00&utmtci=Boston&utmtrg=MA&utmtco=United%20States&utmac=ASSASAS&utmcc=__utma%3D81851599.2062069588.1182951649.1183008786.1183012376.3%3B%2B__utmb%3D81851599%3B%2B__utmc%3D81851599%3B%2B__utmz%3D81851599.1182951649.1.1.utmccn%3D(direct)%7Cutmcsr%3D(direct)%7Cutmcmd%3D(none)%3B%2B

	$transaction = array (
		'utmwv' => $_uwv,
		'utmt' => 'tran',
		'utmn' => rand(0, 2147483647),
		'utmtid' => $order_info['order_id'],
		'utmtto' => $sign . $order_info['total'],
		'utmttx' => $order_info['tax_subtotal'],
		'utmtsp' => $order_info['shipping_cost'],
		'utmtci' => $order_info['b_city'],
		'utmtrg' => $order_info['b_state'],
		'utmtco' => $order_info['b_country_descr'],
		'utmac' => $account,
		'utmcc' => $cookies
	);

	list (, $result) = fn_http_request('GET', $url, $transaction);


	// Items request
	//http://www.google-analytics.com/__utm.gif?utmwv=1&utmt=item&utmn=812678190&utmtid=80&utmipc=B00078MG5M&utmipn=100%25%20Cotton%20Adult%2FYouth%20Beefy%20T-Shirt%20by%20Hanes%20(Style%23%205180)&utmipr=4.50&utmiqt=1&utmac=ASSASAS&utmcc=
	foreach ($order_info['items'] as $item) {
		$cat_id = db_get_field("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = 'M'", $item['product_id']);
		$i = array (
			'utmwv' => $_uwv,
			'utmt' => 'item',
			'utmn' => rand(0, 2147483647),
			'utmtid' => $order_info['order_id'],
			'utmipc' => $item['product_code'],
			'utmipn' => $item['product'],
			'utmiva' => fn_get_category_name($cat_id, $order_info['lang_code']),
			'utmipr' => $sign . fn_format_price($item['subtotal'] / $item['amount']),
			'utmiqt' => $item['amount'],
			'utmac' => $account,
			'utmcc' => $cookies,
		);

		list (, $result) = fn_http_request('GET', $url, $i);
	}
}

function fn_google_analytics_cookies() 
{
	$c = '';

	if (isset($_COOKIE['__utma'])) {
		$c .= "__utma=" . $_COOKIE['__utma'] . ";+";
	}
	if (isset($_COOKIE['__utmb'])) {
		$c .= "__utmb=" . $_COOKIE['__utmb'] . ";+";
	}
	if (isset($_COOKIE['__utmc'])) {
		$c .= "__utmc=" . $_COOKIE['__utmc'] . ";+";
	}
	if (isset($_COOKIE['__utmx'])) {
		$c .= "__utmx=" . $_COOKIE['__utmx'] . ";+";
	}
	if (isset($_COOKIE['__utmz'])) {
		$c .= "__utmz=" . $_COOKIE['__utmz'] . ";+";
	}
	if (isset($_COOKIE['__utmv'])) {
		$c .= "__utmv=" . $_COOKIE['__utmv'] . ";+";
	}

	if (substr($c, strlen($c) - 1, 1) == "+") {
		$c = substr($c, 0, strlen($c) - 1);
	}

	return $c;
}


?>