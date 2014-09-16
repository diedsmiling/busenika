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
// $Id: store_locator.php 10229 2010-07-27 14:21:39Z 2tl $
//

if (!defined('AREA')) { die('Access denied'); }

if ($mode == 'search') {
	fn_add_breadcrumb(fn_get_lang_var('store_locator'));

	list($store_locations, $search, $total) = fn_get_store_locations($_REQUEST);

	$view->assign('store_locations', $store_locations);
	$view->assign('store_locator_search', $search);
}
?>