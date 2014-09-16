<?php

//
// $Id: products.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if (!defined('AREA')) { die('Access denied'); }

if ($mode == 'view' && !empty($_REQUEST['product_id'])) {
	$data = db_get_row("SELECT product_id, age_verification, age_limit FROM ?:products WHERE product_id = ?i", $_REQUEST['product_id']);

	if ($data['age_verification'] == 'Y') {
		if (!empty($_SESSION['auth']['age'])) {
			$age = $_SESSION['auth']['age'];
		} else {
			$age = 0;
		}

		if (!$age) {
			fn_add_breadcrumb(fn_get_lang_var('age_verification'));
			$view->assign('content_tpl', 'addons/age_verification/views/products/components/form.tpl');

			return array (CONTROLLER_STATUS_OK);
		} else {
			if ($age < $data['age_limit']) {
				fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('access_denied'));
				$view->assign('content_tpl', 'addons/age_verification/views/products/components/deny.tpl');

				return array (CONTROLLER_STATUS_OK);
			}
		}
	}

	$data = db_get_array("SELECT * FROM ?:products_categories WHERE product_id = ?i", $data['product_id']);

	foreach ($data as $record) {
		list ($result, $category_id) = fn_age_verification_category_check($record['category_id']);

		if ($category_id) {
			$message = db_get_field("SELECT age_warning_message FROM ?:category_descriptions WHERE category_id = ?i AND lang_code = ?s", $category_id, CART_LANGUAGE);

			$view->assign('age_warning_message', $message);
		}

		if ($result == 'form') {
			fn_add_breadcrumb(fn_get_lang_var('age_verification'));
			$view->assign('content_tpl', 'addons/age_verification/views/products/components/form.tpl');

			return array (CONTROLLER_STATUS_OK);
		} elseif ($result == 'deny') {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('access_denied'));
			$view->assign('content_tpl', 'addons/age_verification/views/products/components/deny.tpl');

			return array (CONTROLLER_STATUS_OK);
		}
	}
}

?>