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

$schema['events'] = array (
	'permissions' => array ('GET' => 'view_events', 'POST' => 'manage_events'),
	'modes' => array (
		'delete_product' => array (
			'permissions' => 'manage_events'
		),
		'delete_events' => array (
			'permissions' => 'manage_events'
		),
		'delete_variant' => array (
			'permissions' => 'manage_events'
		),
		'delete_field' => array (
			'permissions' => 'manage_events'
		)
	),
);
$schema['tools']['modes']['update_status']['param_permissions']['table_names']['giftreg_fields'] = 'manage_events';

?>