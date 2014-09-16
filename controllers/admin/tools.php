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
// $Id: tools.php 9845 2010-06-23 10:59:32Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	if ($mode == 'update_quick_menu_item') {
		$_data = $_REQUEST['item'];

		if (empty($_data['position'])) {
			$_data['position'] = db_get_field("SELECT max(position) FROM ?:quick_menu WHERE parent_id = ?i", $_data['parent_id']);
			$_data['position'] = $_data['position'] + 10;
		}

		$_data['user_id'] = $auth['user_id'];
		
		// remove admin index script from the begining of the URL
		$exclude = array(Registry::get('config.https_location') . '/' . INDEX_SCRIPT, Registry::get('config.http_location') . '/' . INDEX_SCRIPT, INDEX_SCRIPT);
		foreach ($exclude as $e) {
			if (strpos($_data['url'], $e) === 0) {
				$_data['url'] = substr_replace($_data['url'], '[admin_index]', 0, strlen($e));
				break;
			}
		}
		
		if (empty($_data['id'])) {
			$id = db_query("INSERT INTO ?:quick_menu ?e", $_data);

			$_data = array (
				'object_id' => $id,
				'description' => $_data['name'],
				'object_holder' => 'quick_menu'
			);

			foreach ((array)Registry::get('languages') as $_data['lang_code'] => $v) {
				db_query("INSERT INTO ?:common_descriptions ?e", $_data);
			}
		} else {
			db_query("UPDATE ?:quick_menu SET ?u WHERE menu_id = ?i", $_data, $_data['id']);

			$__data = array(
				'description' => $_data['name']
			);
			db_query("UPDATE ?:common_descriptions SET ?u WHERE object_id = ?i AND object_holder = 'quick_menu' AND lang_code = ?s", $__data, $_data['id'], DESCR_SL);
		}

		return array(CONTROLLER_STATUS_OK, "tools.show_quick_menu.edit");
	}

	return;
}

if ($mode == 'phpinfo') {
	phpinfo();
	exit;

} elseif ($mode == 'show_quick_menu') {
	if (ACTION == 'edit') {
		$view->assign('edit_quick_menu', true);
	} else {
		$view->assign('expand_quick_menu', true);
	}

	if (!empty($_REQUEST['popup'])) {
		$view->assign('show_quick_popup', true);
	}

	$view->display('common_templates/quick_menu.tpl');
	exit;

} elseif ($mode == 'get_quick_menu_variant') {
	$ajax->assign('description', db_get_field("SELECT description FROM ?:common_descriptions WHERE object_id = ?i AND object_holder = 'quick_menu' AND lang_code = ?s", $_REQUEST['id'], DESCR_SL));
	exit;

} elseif ($mode == 'remove_quick_menu_item') {
	$where = '';
	if (intval($_REQUEST['parent_id']) == 0) {
		$where = db_quote(" OR parent_id = ?i", $_REQUEST['id']);
		$delete_ids = db_get_fields("SELECT menu_id FROM ?:quick_menu WHERE parent_id = ?i", $_REQUEST['id']);
		db_query("DELETE FROM ?:common_descriptions WHERE object_id IN (?n) AND object_holder = 'quick_menu'", $delete_ids);
	}

	db_query("DELETE FROM ?:quick_menu WHERE menu_id = ?i ?p", $_REQUEST['id'], $where);
	db_query("DELETE FROM ?:common_descriptions WHERE object_id = ?i AND object_holder = 'quick_menu'", $_REQUEST['id']);

	$view->assign('edit_quick_menu', true);
	$view->assign('quick_menu', fn_get_quick_menu_data());
	$view->display('common_templates/quick_menu.tpl');
	exit;

} elseif ($mode == 'update_quick_menu_handler') {
	if (!empty($_REQUEST['enable'])) {
		db_query("UPDATE ?:settings SET value = ?s WHERE option_name = 'show_menu_mouseover' AND section_id = ''", $_REQUEST['enable']);
		return array(CONTROLLER_STATUS_REDIRECT, "tools.show_quick_menu.edit");
	}
	exit;

} elseif ($mode == 'cleanup_history') {
	$_SESSION['last_edited_items'] = array();
	fn_save_user_additional_data('L', '');
	$view->assign('last_edited_items', '');
	$view->display('common_templates/last_viewed_items.tpl');
	exit;
	
} elseif ($mode == 'update_status') {
	if (preg_match("/^[a-z_]+$/", $_REQUEST['table'])) {
		$table_name = $_REQUEST['table'];
	} else {
		die; // incorrect table name
	}
	
	$old_status = db_get_field("SELECT status FROM ?:$table_name WHERE ?w", array($_REQUEST['id_name'] => $_REQUEST['id']));
	
	$permission = true;
	if (defined('COMPANY_ID')) {
		$cols = db_get_fields("SHOW COLUMNS FROM ?:$table_name");
		if (in_array('company_id', $cols)) {
			$condition = fn_get_company_condition();
			$permission = db_get_field("SELECT company_id FROM ?:$table_name WHERE ?w $condition", array($_REQUEST['id_name'] => $_REQUEST['id']));
		}
	}
	if (empty($permission)) {
		fn_set_notification('W',  fn_get_lang_var('warning'), fn_get_lang_var('access_denied'));
		$ajax->assign('return_status', $old_status);
		exit;
	}

	$result = db_query("UPDATE ?:$table_name SET status = ?s WHERE ?w", $_REQUEST['status'], array($_REQUEST['id_name'] => $_REQUEST['id']));
	if ($result) {
		fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('status_changed'));
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('error_status_not_changed'));
		$ajax->assign('return_status', $old_status);
	}

	exit;

// Open/close the store
} elseif ($mode == 'store_mode') {

	fn_set_store_mode($_REQUEST['state']);

	$view->assign('settings', Registry::get('settings'));
	$view->display('bottom.tpl');
	exit;

} elseif ($mode == 'update_position') {

	if (preg_match("/^[a-z_]+$/", $_REQUEST['table'])) {
		$table_name = $_REQUEST['table'];
	} else {
		die;
	}

	$id_name = $_REQUEST['id_name'];
	$ids = explode(',', $_REQUEST['ids']);
	$positions = explode(',', $_REQUEST['positions']);

	foreach ($ids as $k => $id) {
		db_query("UPDATE ?:$table_name SET position = ?i WHERE ?w", $positions[$k], array($id_name => $id));
	}

	fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('positions_updated'));	

	exit;
}

?>