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
// $Id: admin.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['buy_together'] = array (
	'permissions' => array ('GET' => 'view_catalog', 'POST' => 'manage_catalog'),
	'modes' => array (
		'delete' => array (
			'permissions' => 'manage_catalog'
		)
	),
);
$schema['tools']['modes']['update_status']['param_permissions']['table_names']['buy_together'] = 'manage_catalog';

?>