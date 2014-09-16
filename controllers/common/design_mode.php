<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2009 Simbirsk Technologies Ltd. All rights reserved.    *
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
// $Id: design_mode.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ( !defined('TRANSLATION_MODE') && !defined('CUSTOMIZATION_MODE')) { 
	die('Access denied'); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($mode == 'update_design_mode') {
		if (!empty($_REQUEST['design_mode'])) {
			$_restore = $_REQUEST['design_mode'] == 'translation_mode' ? 'customization_mode' : 'translation_mode';
			db_query("UPDATE ?:settings SET value = 'Y' WHERE option_name = ?s AND section_id = ?s", $_REQUEST['design_mode'], '');
			db_query("UPDATE ?:settings SET value = 'N' WHERE option_name = ?s AND section_id = ?s", $_restore, '');
			fn_rm(DIR_COMPILED . 'customer');
			fn_rm(DIR_COMPILED . 'admin');
		}

		return array(CONTROLLER_STATUS_OK, $_REQUEST['current_url']);
	}
}

if ($mode == 'update_langvar') {
	fn_trusted_vars('langvar_value');
	$name = empty($_REQUEST['langvar_name']) ? '' : strtolower($_REQUEST['langvar_name']);
	if (strpos($name, '-') !== false) {
		$params = explode('-', $name);
		$where = array();
		for ($i = 2; $i < count($params); $i += 2) {
			$where[$params[$i]] = $params[$i+1];
		}
		$where['lang_code'] = $_REQUEST['lang_code'];
		db_query("UPDATE ?:$params[0] SET $params[1] = ?s WHERE ?w", $_REQUEST['langvar_value'], $where);
	} else {
		db_query("UPDATE ?:language_values SET value = ?s WHERE name = ?s AND lang_code = ?s", $_REQUEST['langvar_value'], $_REQUEST['langvar_name'], $_REQUEST['lang_code']);
	}
	exit;

} elseif ($mode == 'get_langvar') {
	$name = empty($_REQUEST['langvar_name']) ? '' : strtolower($_REQUEST['langvar_name']);
	if (strpos($name, '-') !== false) {
		$params = explode('-', $name);
		$where = array();
		for ($i = 2; $i < count($params); $i += 2) {
			$where[$params[$i]] = $params[$i+1];
		}
		$where['lang_code'] = $_REQUEST['lang_code'];
		$ajax->assign('langvar_value', db_get_field("SELECT $params[1] FROM ?:$params[0] WHERE ?w", $where));
	} else {
		$ajax->assign('langvar_value', fn_get_lang_var($name, $_REQUEST['lang_code']));
	}
	exit;

} elseif ($mode == 'get_content') {
	$ext = strtolower(fn_get_file_ext($_REQUEST['file']));

	if ($ext == 'tpl') {
		$ajax->assign('content', fn_get_contents($_REQUEST['file'], DIR_SKINS . Registry::get('config.skin_name') . '/' . AREA_NAME . '/'));
	}
	exit;

} elseif ($mode == 'save_template') {
	fn_trusted_vars('content');
	if (defined('DEVELOPMENT')) {
		exit;
	}
	$ext = strtolower(fn_get_file_ext($_REQUEST['file']));
	if ($ext == 'tpl') {
		fn_put_contents($_REQUEST['file'], $_REQUEST['content'], DIR_SKINS . Registry::get('config.skin_name') . '/' . AREA_NAME . '/');
	}
	return array(CONTROLLER_STATUS_OK, $_REQUEST['current_url']);

} elseif ($mode == 'restore_template') {
	$copied = false;
	$full_path = DIR_SKINS . Registry::get('config.skin_name') . '/' . AREA_NAME . '/' . $_REQUEST['file'];
	if (fn_check_path($full_path)) {
		$c_name = fn_normalize_path($full_path);

		$r_name = str_replace('/skins/', '/var/skins_repository/', $c_name);
		if (is_file($r_name)) {
			$copied = fn_copy($r_name, $c_name);
		}
		$msg = $copied ? fn_get_lang_var("text_file_restored") : fn_get_lang_var("text_cannot_restore_file");

		fn_set_notification('N', fn_get_lang_var('notice'), str_replace("[file]", basename($_REQUEST['file']), $msg));
		if ($copied) {
			return array(CONTROLLER_STATUS_OK, $_REQUEST['current_url']);
		}
	}
	exit;

}
?>
