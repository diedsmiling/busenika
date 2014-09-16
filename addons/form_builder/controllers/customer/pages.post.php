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
// $Id: pages.post.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($mode == 'send_form') {

		if (Registry::get('settings.Image_verification.use_for_form_builder') == 'Y' && fn_image_verification('forms_' . $_REQUEST['page_id'], empty($_REQUEST['verification_answer']) ? '' : $_REQUEST['verification_answer']) == false) {

			fn_save_post_data();
			return array(CONTROLLER_STATUS_REDIRECT, "pages.view?page_id=$_REQUEST[page_id]");
		}

		fn_send_form($_REQUEST['page_id'], empty($_REQUEST['form_values']) ? array() : $_REQUEST['form_values']);

		return array(CONTROLLER_STATUS_OK, "pages.view?page_id=$_REQUEST[page_id]&sent=Y");
	}
	return;
}


if ($mode == 'view' && !empty($_REQUEST['page_id'])) {
	// if form is secure, redirect to https connection

	$page_is_https = db_get_field("SELECT value FROM ?:form_options WHERE element_type = ?s AND page_id = ?i", FORM_IS_SECURE, $_REQUEST['page_id']);
	if (!defined('HTTPS')) {
		if ($page_is_https == 'Y') {
			return array(CONTROLLER_STATUS_REDIRECT, Registry::get('config.https_location') . '/' . Registry::get('config.current_url'));
		}
	} elseif(Registry::get('settings.General.keep_https') != 'Y') {
		if ($page_is_https != 'Y') {
			return array(CONTROLLER_STATUS_REDIRECT, Registry::get('config.http_location') . '/' . Registry::get('config.current_url'));
		}
	}

	if (!empty($_SESSION['saved_post_data']) && !empty($_SESSION['saved_post_data']['form_values'])) {
		$view->assign('form_values', $_SESSION['saved_post_data']['form_values']);
		unset($_SESSION['saved_post_data']);
	}

} elseif ($mode == 'sent' && !empty($_REQUEST['page_id'])) {
	$page = fn_get_page_data($_REQUEST['page_id'], CART_LANGUAGE);
	$view->assign('page', $page);
}

?>