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
// $Id: pages.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	return;
}


//
// View page details
//
if ($mode == 'view') {

	$_REQUEST['page_id'] = empty($_REQUEST['page_id']) ? 0 : $_REQUEST['page_id'];
	$preview = ($auth['area'] == 'A' && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'preview') ? true : false;
	$page = fn_get_page_data($_REQUEST['page_id'], CART_LANGUAGE, $preview);

	if (empty($page) || ($page['status'] == 'D' && !$preview)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	if (!empty($page['meta_description']) || !empty($page['meta_keywords'])) {
		$view->assign('meta_description', $page['meta_description']);
		$view->assign('meta_keywords', $page['meta_keywords']);
	}

	// If page title for this page is exist than assign it to template
	if (!empty($page['page_title'])) {
		$view->assign('page_title', $page['page_title']);
	}

	$parent_ids = explode('/', $page['id_path']);
	foreach($parent_ids as $p_id) {
		$_page = fn_get_page_data($p_id);
		fn_add_breadcrumb($_page['page'], ($p_id == $page['page_id']) ? '' : ($_page['page_type'] == PAGE_TYPE_LINK && !empty($_page['link']) ? $_page['link'] : "pages.view?page_id=$p_id"));
	}

	$view->assign('page', $page);
}

?>