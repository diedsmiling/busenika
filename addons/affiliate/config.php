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
// $Id: config.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (AREA == 'C') {
	if (empty($_SESSION['partner_data'])) {
		$_SESSION['partner_data'] = array();
	}

	if (!empty($_REQUEST['aff_id'])) {
		$_SESSION['partner_data']['partner_id'] = $_REQUEST['aff_id'];

	} elseif (!empty($_REQUEST['partner_id'])) {
		$_SESSION['partner_data']['partner_id'] = $_REQUEST['partner_id'];
	}

	if (!empty($_SESSION['partner_data']['partner_id'])) {
		fn_set_partner_cookie($_SESSION['partner_data']['partner_id']);
	}
}

//
// Commission types
//
Registry::set('payout_types', array(
	'show' => array (
		'id' => 'show',
		'title' => 'payout_show',
		'value_types' => array('A' => 'absolute'),
		'default' => 'Y',
	),
	'click' => array (
		'id' => 'click',
		'title' => 'payout_click',
		'value_types' => array('A' => 'absolute'),
		'default' => 'Y',
	),
	'sale' => array (
		'id' => 'sale',
		'title' => 'payout_sales',
		'value_types' => array('A' => 'absolute', 'P' => 'percent'),
		'default' => 'Y',
	),
	'new_customer' => array (
		'id' => 'new_customer',
		'title' => 'new_customer',
		'value_types' => array('A' => 'absolute'),
		'default' => 'Y',
	),
	'new_partner' => array (
		'id' => 'new_partner',
		'title' => 'payout_new_partner',
		'value_types' => array('A' => 'absolute'),
		'default' => 'Y',
	),
	'use_coupon' => array (
		'id' => 'use_coupon',
		'title' => 'payout_use_coupon',
		'value_types' => array('A' => 'absolute', 'P' => 'percent'),
		'default' => 'N',
	),
	'init_balance' => array (
		'id' => 'init_balance',
		'title' => 'payout_init_balance',
		'value_types' => array('A' => 'absolute'),
		'default' => 'N',
	),
));

Registry::set('affiliate_controllers', array (
	'aff_statistics', 'affiliate_plans', 'banner_products', 'banners_manager', 'partners', 'payouts'
));

?>