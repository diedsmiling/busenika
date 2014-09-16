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
// $Id: language_variables.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array (
	'section' => 'translations',
	'pattern_id' => 'language_variables',
	'name' => fn_get_lang_var('language_variables'),
	'key' => array('name', 'lang_code'),
	'table' => 'language_values',
	'condition' => array('lang_code' => '@lang_code'),
	'options' => array (
		'lang_code' => array (
			'title' => 'language',
			'type' => 'languages'
		),
	),
	'export_fields' => array (
		'Name' => array (
			'db_field' => 'name',
			'alt_key' => true,
			'required' => true,
		),
		'Value' => array (
			'db_field' => 'value',
			'required' => true,
		),
		'Language' => array (
			'db_field' => 'lang_code',
			'alt_key' => true,
			'required' => true,
		),
	),
);

?>