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
// $Id: actions.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_settings_actions_addons_webmail(&$new_value, $old_value)
{
	if ($new_value == 'A') {
		// Copy data directory to "var"
		$dir_data = DIR_ROOT . '/var/webmail';

		if (fn_copy(DIR_ADDONS . 'webmail/lib/webmail/data', $dir_data) == false) {
			$msg = fn_get_lang_var('text_cannot_write_directory');
			fn_set_notification('E', fn_get_lang_var('error'), str_replace('[directory]', $dir_data, $msg));
			$new_value = 'D';
			return false;
		}
		
		$config = Registry::get('config');
		$_settings = $dir_data . '/settings/settings.xml';
		// 1 step, generate config file
		$xml = simplexml_load_file($_settings);

		$xml->Common->DBLogin = $config['db_user'];
		$xml->Common->DBPassword = $config['db_password'];
		$xml->Common->DBName = $config['db_name'];
		$xml->Common->DBHost = $config['db_host'];

		if (fn_put_contents($_settings, $xml->asXML()) == false) {
			$msg = fn_get_lang_var('cannot_write_file');
			fn_set_notification('E', fn_get_lang_var('error'), str_replace('[file]', $_settings, $msg));
			$new_value = 'D';
			return false;
		}

		include(DIR_ADDONS . 'webmail/lib/webmail/web/class_settings.php');
		include(DIR_ADDONS . 'webmail/lib/webmail/web/class_dbstorage.php');

		// Init mailbee core
		$null = null;
		$settings = &Settings::CreateInstance();
		$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
		$dbStorage->Connect();
		$dbStorage->CreateTables($settings->DbPrefix);
	}
}
?>