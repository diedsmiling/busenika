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
// $Id: products.post.php 6788 2009-01-16 13:29:11Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($mode == 'clean_up') {

	fn_copy(DIR_ROOT . '/catalog/.htaccess', DIR_COMPILED . 'html_catalog_htaccess');
	fn_rm(DIR_ROOT . '/catalog', false);
	@fn_rename(DIR_COMPILED . 'html_catalog_htaccess', DIR_ROOT . '/catalog/.htaccess');

	fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_seo_html_catalog_cleaned_up'));
	
	return array(CONTROLLER_STATUS_REDIRECT, "addons.manage");

}
?>