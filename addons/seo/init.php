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
// $Id: init.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

fn_define('SEO_FILENAME_EXTENSION', '.html');

fn_register_hooks(
	'url',
	'get_route',
	/*'init_templater',*/
	'before_dispatch',

	'update_category',
	'get_category_data',
	'get_category_data_post',
	'get_categories',
	'get_categories_post',
	'delete_category',
	
	'update_product',
	'get_products',
	'get_products_post',
	'get_product_data',
	'delete_product',
	
	'update_page',
	'get_page_data',
	'delete_page',
	
	'get_product_feature_variants',
	'update_product_feature',
	
	'get_news',
	'get_news_post',
	'get_news_data',
	'update_news',
	'delete_news'
);

?>