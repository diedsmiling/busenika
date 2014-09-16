<?php

//
// $Id: categories.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if (!defined('AREA')) { die('Access denied'); }

if ($mode == 'view' && !empty($_REQUEST['category_id'])) {
	list ($result, $category_id) = fn_age_verification_category_check($_REQUEST['category_id']);

	if ($category_id) {
		$message = db_get_field("SELECT age_warning_message FROM ?:category_descriptions WHERE category_id = ?i AND lang_code = ?s", $category_id, CART_LANGUAGE);

		$view->assign('age_warning_message', $message);
	}

	if ($result == 'form') {
		fn_add_breadcrumb(fn_get_lang_var('age_verification'));
		$view->assign('content_tpl', 'addons/age_verification/views/categories/components/form.tpl');

		return array (CONTROLLER_STATUS_OK);
	} elseif ($result == 'deny') {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('access_denied'));
		$view->assign('content_tpl', 'addons/age_verification/views/categories/components/deny.tpl');

		return array (CONTROLLER_STATUS_OK);
	}
}

?>