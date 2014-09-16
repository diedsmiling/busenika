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
// $Id: attachments.php 10229 2010-07-27 14:21:39Z 2tl $
//

if (!defined('AREA')) { die('Access denied'); }

if (defined('COMPANY_ID')) {
	$permission = false;
	if (!empty($_REQUEST['object_type']) && $_REQUEST['object_type'] == 'product' && !empty($_REQUEST['object_id'])) {
		$product_id = db_get_field("SELECT product_id FROM ?:products WHERE product_id = ?i " . fn_get_company_condition(), $_REQUEST['object_id']);
		if (!empty($product_id)) {
			$permission = true;		
		}
	}
	if (!$permission) {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('access_denied'));
		if (defined('AJAX_REQUEST')) {
			exit;
		} else {
			return array(CONTROLLER_STATUS_DENIED);
		}
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	//
	// Add attachments
	//

	if ($mode == 'add') {
		if (!empty($_REQUEST['attachment_data'])) {
			fn_add_attachments($_REQUEST['attachment_data'], $_REQUEST['object_type'], $_REQUEST['object_id']);
		}
	}

	//
	// Update attachments
	//
	if ($mode == 'update') {
		if (!empty($_REQUEST['attachment_data'])) {
			fn_update_attachments($_REQUEST['attachment_data'], $_REQUEST['attachment_id'], $_REQUEST['object_type'], $_REQUEST['object_id']);
		}
	}

	return array(CONTROLLER_STATUS_OK); // redirect should be performed via redirect_url always
}

if ($mode == 'getfile') {
	if (!empty($_REQUEST['attachment_id']) && !empty($_REQUEST['object_type']) && !empty($_REQUEST['object_id'])) {
		$data = fn_get_attachment($_REQUEST['attachment_id'], $_REQUEST['object_type'], $_REQUEST['object_id']);

		if (!empty($data['path'])) {
			fn_get_file($data['path']);
		}
	}
	exit;

} elseif ($mode == 'delete') {
	fn_delete_attachments(array($_REQUEST['attachment_id']), $_REQUEST['object_type'], $_REQUEST['object_id']);
	$attachments = fn_get_attachments($_REQUEST['object_type'], $_REQUEST['object_id']);
	if (empty($attachments)) {
		$view->display('addons/attachments/views/attachments/manage.tpl');
	}
	exit;

} elseif ($mode == 'update') {
	// Assign attachments files for products
	$attachments = fn_get_attachments($_REQUEST['object_type'], $_REQUEST['object_id']);

	Registry::set('navigation.tabs.attachments', array (
		'title' => fn_get_lang_var('attachments'),
		'js' => true
	));

	$view->assign('attachments', $attachments);
}

?>