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
// $Id: admin.post.php 9088 2010-03-15 10:40:51Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['aff_statistics'] = array (
	'permissions' => 'manage_affiliate',
);
$schema['banners_manager'] = array (
	'permissions' => 'manage_affiliate',
);
$schema['payouts'] = array (
	'permissions' => 'manage_affiliate',
);
$schema['product_groups'] = array (
	'permissions' => 'manage_affiliate',
);
$schema['partners'] = array (
	'permissions' => 'manage_affiliate',
);
$schema['affiliate_plans'] = array (
	'permissions' => 'manage_affiliate',
);
$schema['tools']['modes']['update_status']['param_permissions']['table_names']['aff_groups'] = 'manage_affiliate';
$schema['tools']['modes']['update_status']['param_permissions']['table_names']['affiliate_plans'] = 'manage_affiliate';
$schema['tools']['modes']['update_status']['param_permissions']['table_names']['aff_banners'] = 'manage_affiliate';
$schema['tools']['modes']['update_status']['param_permissions']['table_names']['affiliate_payouts'] = 'manage_affiliate';

?>