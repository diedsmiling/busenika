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
// $Id: products.post.php 10055 2010-07-14 10:15:19Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['references']['seo_names'] = array (
	'reference_fields' => array ('object_id' => '#key', 'type' => 'p', 'dispatch' => '', 'lang_code' => '@lang_code'),
	'join_type' => 'LEFT'
);

$schema['export_fields']['SEO name'] = array (
	'table' => 'seo_names',
	'db_field' => 'name',
	'process_put' => array ('fn_create_seo_name', '#key', 'p', '#this', 0, '', '@lang_code'),
);

?>