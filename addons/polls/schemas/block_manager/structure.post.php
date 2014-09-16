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

$schema['polls'] = array (
	'fillings' => array (
		'manually',
	),
	'appearances' => array (
		'addons/polls/blocks/sidebox.tpl' => array (
			'params' => array ()
		),
		'addons/polls/blocks/central.tpl' => array ()
	),
	'dispatch' => 'pages.update',
	'object_id' => 'page_id',
	'object_name' => 'polls',
	'picker_props' => array (
		'picker' => 'addons/polls/pickers/polls_picker.tpl',
		'params' => array (
			'multiple' => true,
		),
	),
	
);

?>