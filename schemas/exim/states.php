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
// $Id: states.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array (
	'section' => 'translations',
	'pattern_id' => 'states',
	'name' => fn_get_lang_var('states'),
	'key' => array('state_id'),
	'table' => 'states',
	'references' => array (
		'state_descriptions' => array (
			'reference_fields' => array ('state_id' => '#key', 'lang_code' => '@lang_code'),
			'join_type' => 'LEFT'
		),
	),
	'options' => array (
		'lang_code' => array (
			'title' => 'language',
			'type' => 'languages'
		),
	),
	'export_fields' => array (
		'State' => array (
			'db_field' => 'state',
			'table' => 'state_descriptions',
			'required' => true,
		),
		'Code' => array (
			'db_field' => 'code',
			'required' => true,
			'alt_key' => true,
		),
		'Country code' => array (
			'db_field' => 'country_code',
			'required' => true,
			'alt_key' => true,
		),
	),
);

?>