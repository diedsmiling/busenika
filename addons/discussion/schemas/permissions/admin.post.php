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

$schema['discussion_manager'] = array (
	'permissions' => array ('GET' => 'view_discussions', 'POST' => 'manage_discussions'),
);
$schema['discussion'] = array (
	'permissions' => array ('GET' => 'view_discussions', 'POST' => 'manage_discussions'),
);

$schema['index']['modes']['delete_post'] = array (
	'permissions' => 'manage_discussions'
);

$schema['tools']['modes']['update_status']['param_permissions']['table_names']['discussion_posts'] = 'manage_discussions';

?>