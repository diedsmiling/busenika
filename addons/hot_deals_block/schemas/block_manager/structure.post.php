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
// $Id: structure.post.php 10435 2010-08-17 11:44:18Z alexions $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['products']['appearances']['addons/hot_deals_block/blocks/hot_deals.tpl'] = array (
	'data_modifier' => array (
		'fn_gather_additional_product_data' => array (
			'product' => '#this',
			'get_icon' => true,
			'get_detailed' => true,
			'get_options' => false
		)
	),
	'params' => array (
		'hot_deals' => true
	)
);

?>