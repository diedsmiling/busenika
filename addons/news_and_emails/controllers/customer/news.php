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
// $Id: news.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$_REQUEST['news_id'] = empty($_REQUEST['news_id']) ? 0 : $_REQUEST['news_id'];

if ($mode == 'view') {

	fn_add_breadcrumb(fn_get_lang_var('news'), "news.list");

	$news_data = fn_get_news_data($_REQUEST['news_id']);
	if (empty($news_data)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	fn_add_breadcrumb($news_data['news']);

	$view->assign('news', $news_data);

} elseif ($mode == 'list') {

	fn_add_breadcrumb(fn_get_lang_var('news'));

	$params = $_REQUEST;
	$params['paginate'] = true;

	list($news, ) = fn_get_news($params, CART_LANGUAGE);

	$view->assign('news', $news);
}

?>