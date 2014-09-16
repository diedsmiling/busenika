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
// $Id: view_conditions.php 9672 2010-05-31 12:55:46Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['partners'] = array (
	'func' => 'fn_get_users',
	'auth' => true,
	'additional_data' => array('user_type' => 'P'),
	'item_id' => 'user_id'
);
$schema['payouts'] = array (
	'func' => 'fn_get_payouts',
	'item_id' => 'payout_id'
);

?>