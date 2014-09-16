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
// $Id: admin.post.php 8887 2010-02-19 06:47:16Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['seo_rules'] = array (
	'modes' => array (
		'delete_rule' => array (
			'permissions' => 'manage_seo_rules'
		)
	),
	'permissions' => array ('GET' => 'view_seo_rules', 'POST' => 'manage_seo_rules')
);

?>