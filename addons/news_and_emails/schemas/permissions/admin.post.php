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

$schema['news'] = array (
	'modes' => array (
		'delete' => array (
			'permissions' => 'manage_news'
		)
	),
	'permissions' => array ('GET' => 'view_news', 'POST' => 'manage_news')
);
$schema['newsletters'] = array (
	'modes' => array (
		'delete' => array (
			'permissions' => 'manage_news'
		),
		'delete_campaign' => array (
			'permissions' => 'manage_news'
		)
	),
	'permissions' => array ('GET' => 'view_news', 'POST' => 'manage_news')
);
$schema['subscribers'] = array (
	'modes' => array (
		'delete' => array (
			'permissions' => 'manage_news'
		)
	),
	'permissions' => array ('GET' => 'view_news', 'POST' => 'manage_news')
);
$schema['campaigns'] = array (
	'permissions' => array ('GET' => 'view_news', 'POST' => 'manage_news')
);
$schema['mailing_lists'] = array (
	'modes' => array (
		'delete' => array (
			'permissions' => 'manage_news'
		)
	),
	'permissions' => array ('GET' => 'view_news', 'POST' => 'manage_news')
);
$schema['tools']['modes']['update_status']['param_permissions']['table_names']['newsletter_campaigns'] = 'manage_news';
$schema['tools']['modes']['update_status']['param_permissions']['table_names']['news'] = 'manage_news';
$schema['tools']['modes']['update_status']['param_permissions']['table_names']['mailing_lists'] = 'manage_news';

?>