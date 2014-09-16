<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2009 Simbirsk Technologies Ltd. All rights reserved.    *
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
// $Id: init.php 8249 2009-11-17 11:49:04Z alexey $
//

if ( !defined('AREA') ) { die('Access denied'); }

define('TAGS_MAX_LEVEL', 6);

fn_register_hooks(
	'delete_product',
	'clone_product',
	'update_product',
	'update_page',
	'delete_page',
	'clone_page',
	'get_additional_product_data',
	'get_page_data',
	'get_pages',
	'get_products',
	'get_users',
	'seo_is_indexed_page'
);

?>
