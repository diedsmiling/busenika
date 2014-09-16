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
// $Id: structure.post.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['banners'] = array (
	'fillings' => array (
		'manually',
		'newest' => array (
			'params' => array (
				'sort_by' => 'timestamp'
			)
		),
	),
	'appearances' => array (
		'addons/banners/blocks/original.tpl',
		'addons/banners/blocks/slider.tpl',		
	),
	'object_description' => 'banners',
	'object_id' => 'banner_id',
	'object_name' => 'banners',
	'picker_props' => array (
		'picker' => 'addons/banners/pickers/banners_picker.tpl',
		'params' => array (
		),
	),
);

?>
