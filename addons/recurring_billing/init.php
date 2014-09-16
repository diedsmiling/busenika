<?php
/******************************************************************************
*                                                                             *
*     Copyright (c) 2004 Simbirsk Technologies LTD. All rights reserved.      *
*                                                                             *
*******************************************************************************
*                                                                             *
* CS-Cart  is  commercial  software,  only  users  who have purchased a valid *
* license through  http://www.cs-cart.com/  and  accept  to the terms of this *
* License Agreement can install this product.                                 *
*                                                                             *
*******************************************************************************
* THIS  CS-CART  SHOP END-USER LICENSE AGREEMENT IS A LEGAL AGREEMENT BETWEEN *
* YOU  AND  YOUR  COMPANY (COLLECTIVELY, "YOU") AND SIMBIRSK TECHNOLOGIES LTD *
* (HEREINAFTER  REFERRED  TO   AS  "THE AUTHOR")  FOR  THE  SOFTWARE  PRODUCT *
* IDENTIFIED  ABOVE,  WHICH  INCLUDES  COMPUTER   SOFTWARE  AND  MAY  INCLUDE *
* ASSOCIATED   MEDIA,   PRINTED  M ATERIALS,   AND   "ONLINE"  OR  ELECTRONIC *
* DOCUMENTATION  (COLLECTIVELY,  THE  "SOFTWARE").  BY  USING  THE  SOFTWARE, *
* YOU  SIGNIFY YOUR AGREEMENT TO ALL TERMS, CONDITIONS, AND NOTICES CONTAINED *
* OR  REFERENCED  HEREIN.  IF  YOU  ARE NOT  WILLING  TO  BE  BOUND  BY  THIS *
* AGREEMENT, DO NOT INSTALL OR USE THE SOFTWARE.                              *
*                                                                             *
* PLEASE   READ  THE   FULL  TEXT  OF  SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS *
* ALSO AVAILABLE AT THE FOLLOWING URL: http://www.cs-cart.com/license.html    *
******************************************************************************/

//
// $Id: init.php 6788 2009-01-16 13:29:11Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

fn_register_hooks(
	'get_additional_product_data_before_discounts',
	'pre_add_to_cart',
	'generate_cart_id',
	'get_cart_product_data',
	'place_order',
	'get_status_data',
	'buy_together_pre_add_to_cart',
	'buy_together_restricted_product',
	'pre_add_to_wishlist',
	'change_order_status',
	'get_additional_information',
	'get_products',
	'after_options_calculation'
);

?>