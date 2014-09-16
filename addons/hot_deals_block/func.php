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
// $Id: func.php 9088 2010-03-15 10:40:51Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_hot_deals_block_get_products($params, $fields, $sortings, $condition, $join, $sorting, $group_by) 
{
	if (!empty($params['hot_deals'])) {
		$fields[] = '?:category_descriptions.category';
		$join .= db_quote(" LEFT JOIN ?:category_descriptions ON ?:category_descriptions.category_id=products_categories.category_id AND products_categories.link_type = 'M' AND ?:category_descriptions.lang_code = ?s", CART_LANGUAGE);
		$condition .= " AND products_categories.link_type = 'M'";  
	}
}

?>