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
// $Id: structure.php 7067 2009-03-18 10:24:55Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['tags'] = array (
	'fillings' => array (
		'tag_cloud' => array (
			'params' => array (
				'status' => 'A',
				'sort_by' => 'popularity',
				'sort_order' => 'desc',
				'sort_popular' => true,
			)
		),
		'my_tags' => array (
			'params' => array (
				'auth' => array(
					'user_id' => '%USER_ID%',
				),
				'sort_by' => 'tag',
				'sort_order' => 'asc',
				'see' => 'my'
			),
		)
	),
	'appearances' => array (
		'addons/tags/blocks/tag_cloud.tpl' => array (
			'conditions' => array (
				'fillings' => array ('tag_cloud')
			),
		),
		'addons/tags/blocks/user_tag_cloud.tpl' => array (
			'conditions' => array (
				'fillings' => array ('my_tags')
			)
		)
	),
	'dispatch' => 'tags.manage', // what for?
	'object_id' => 'tag_id',
	'object_name' => 'tag',
);

?>