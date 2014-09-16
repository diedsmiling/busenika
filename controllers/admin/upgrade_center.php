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
// $Id: upgrade_center.php 10563 2010-09-01 08:36:59Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

fn_define('DIR_REPOSITORY', 'var/skins_repository/base/');
$custom_skin_files = array(
	'customer/styles_ie.css',
	'customer/dropdown.css',
	'customer/styles.css',
	'manifest.ini'
);

$skip_files = array(
	'manifest.ini'
);


set_time_limit(0);

$uc_settings = fn_get_settings('Upgrade_center');

// If we're performing the update, check if upgrade center override controller is exist in the package
if (!empty($_SESSION['uc_package']) && file_exists(DIR_UPGRADE . $_SESSION['uc_package'] . '/uc_override.php')) {
	return include(DIR_UPGRADE . $_SESSION['uc_package'] . '/uc_override.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'update_settings') {
		if (!empty($_REQUEST['settings_data'])) {
			foreach ($_REQUEST['settings_data'] as $k => $v) {
				db_query("UPDATE ?:settings SET value = ?s WHERE section_id = 'Upgrade_center' AND option_name = ?s", $v, $k);
			}
		}
	}

	return array(CONTROLLER_STATUS_REDIRECT);
}

if ($mode == 'manage') {

	// Create directory structure
	fn_uc_create_structure();

	$view->assign('installed_upgrades', fn_uc_check_installed_upgrades());

	/* NULLED BY FLIPMODE! @ 2010/09/06 */
	/* if (empty($uc_settings['license_number'])) {
		$view->assign('require_license_number', true);
	} else { */
		$view->assign('packages', fn_uc_get_packages($uc_settings));
	/* } */
	
	$view->assign('uc_settings', $uc_settings);

} elseif ($mode == 'refresh') {
	fn_rm(DIR_UPGRADE . 'packages.xml');

	return array(CONTROLLER_STATUS_OK, "upgrade_center.manage");

} elseif ($mode == 'get_upgrade') {

	$package = fn_uc_get_package_details($_REQUEST['package_id']);
	if (fn_uc_get_package($_REQUEST['package_id'], $_REQUEST['md5'], $package, $uc_settings) == true) {
		$_SESSION['uc_package'] = $package['file'];
		$suffix = '.check';
	} else {
		unset($_SESSION['uc_package']);
		$suffix = '.manage';
	}

	return array(CONTROLLER_STATUS_OK, "upgrade_center" . $suffix);

} elseif ($mode == 'check') {

	if (empty($_SESSION['uc_package'])) {
		return array(CONTROLLER_STATUS_REDIRECT, "upgrade_center.manage");
	}

	fn_add_breadcrumb(fn_get_lang_var('upgrade_center'), "upgrade_center.manage");

	fn_set_store_mode('closed'); // close the store
	
	$xml = simplexml_load_file(DIR_UPGRADE . $_SESSION['uc_package'] . '/uc.xml', NULL, LIBXML_NOERROR);

	if (!empty($xml)) {
		$hash_table = $result = array();

		// Get array with original files hashes
		if (isset($xml->original_files)) {
			foreach ($xml->original_files->item as $item) {
				$hash_table[(string)$item['file']] = (string)$item;
			}
		}

		fn_uc_ftp_connect($uc_settings);

		fn_uc_create_skins(DIR_UPGRADE . $_SESSION['uc_package'] . '/package', $_SESSION['uc_package'], $skip_files, $custom_skin_files);
		fn_uc_check_files(DIR_UPGRADE . $_SESSION['uc_package'] . '/package', $hash_table, $result, $_SESSION['uc_package'], $custom_skin_files);

		$udata = $data = array();
		if (file_exists(DIR_UPGRADE . 'installed_upgrades.php')) {
			include(DIR_UPGRADE . 'installed_upgrades.php');
		}

		if (!empty($result['changed'])) {
			foreach ($result['changed'] as $f) {
				$data[$f] = false;
			}
		}

		$udata[$_SESSION['uc_package']]['files'] = $data;
		fn_uc_update_installed_upgrades($udata);
	}

	$view->assign('check_results', $result);

} elseif ($mode == 'run_backup') {

	if (empty($_SESSION['uc_package'])) {
		return array(CONTROLLER_STATUS_REDIRECT, "upgrade_center.manage");
	}

	$backup_details = array(
		'files' => array(),
		'tables' => array()
	);

	fn_uc_backup_files(DIR_UPGRADE . $_SESSION['uc_package'] . '/package', DIR_ROOT, $backup_details['files'], $_SESSION['uc_package']);
	$obsolete_files = fn_uc_backup_obsolete_files(DIR_UPGRADE . $_SESSION['uc_package'] . '/backup/', DIR_ROOT, DIR_UPGRADE . $_SESSION['uc_package'] . '/uc.xml');
	$backup_details['files'] = array_merge($backup_details['files'], $obsolete_files);
	sort($backup_details['files']);
	
	$backup_details['tables'] = fn_uc_backup_database(DIR_UPGRADE . $_SESSION['uc_package']);

	$udata = array();
	if (file_exists(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');
	}

	$udata[$_SESSION['uc_package']]['backup_details'] = $backup_details;
	fn_uc_update_installed_upgrades($udata);

	return array(CONTROLLER_STATUS_OK, "upgrade_center.backup");


} elseif ($mode == 'backup') {

	if (empty($_SESSION['uc_package'])) {
		return array(CONTROLLER_STATUS_REDIRECT, "upgrade_center.manage");
	}

	if (file_exists(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');
	} else {
		return array(CONTROLLER_STATUS_REDIRECT, "upgrade_center.check");
	}

	// Put data to emergency restore script
	$c = fn_get_contents(DIR_UPGRADE . $_SESSION['uc_package'] . '/restore.php');

	$data = "\$uc_settings = " . var_export($uc_settings, true) . ";\n\n";
	$data .= "\$db = array (" . 
		"'db_host' => '" . Registry::get('config.db_host') . "'," . 
		"'db_user' => '" . Registry::get('config.db_user') . "'," . 
		"'db_password' => '" . Registry::get('config.db_password') . "'," . 
		"'db_name' => '" . Registry::get('config.db_name') . "'" . 
		");\n\n";

	$restore_key = md5(uniqid());
	$data .= "\$uak = '" . $restore_key . "';";

	$start = strpos($c, '//[params]') + strlen('//[params]') + 1;
	$end = strpos($c, '//[/params]') - 1;

	$c = substr_replace($c, $data, $start, $end - $start);
	fn_put_contents(DIR_UPGRADE . $_SESSION['uc_package'] . '/restore.php', $c);

	$view->assign('restore_key', $restore_key); 
	$view->assign('backup_details', $udata[$_SESSION['uc_package']]['backup_details']); 

} elseif ($mode == 'upgrade') {

	if (empty($_SESSION['uc_package'])) {
		return array(CONTROLLER_STATUS_REDIRECT, "upgrade_center.manage");
	}

	fn_uc_ftp_connect($uc_settings);
	fn_uc_copy_files(DIR_UPGRADE . $_SESSION['uc_package'] . '/package', DIR_ROOT);
	fn_uc_rm_files(DIR_ROOT, DIR_UPGRADE . $_SESSION['uc_package'] . '/uc.xml', 'deleted_files');

	db_import_sql_file(DIR_UPGRADE . $_SESSION['uc_package'] . '/uc.sql', 16384, true, 1, true, true, true);
	fn_uc_post_upgrade(DIR_UPGRADE . $_SESSION['uc_package'], 'upgrade');

	fn_uc_cleanup_cache($_SESSION['uc_package'], 'upgrade');
	$package = $_SESSION['uc_package'];
	unset($_SESSION['uc_package']);

	return array(CONTROLLER_STATUS_OK, "upgrade_center.summary?package=" . $package);

} elseif ($mode == 'revert') {

	fn_uc_ftp_connect($uc_settings);
	fn_uc_copy_files(DIR_UPGRADE . $_REQUEST['package'] . '/backup', DIR_ROOT);
	@fn_uc_rm(DIR_ROOT . '/uc.sql');
	fn_uc_rm_files(DIR_ROOT, DIR_UPGRADE . $_REQUEST['package'] . '/uc.xml', 'new_files');

	db_import_sql_file(DIR_UPGRADE . $_REQUEST['package'] . '/backup/uc.sql', 16384, true, 1, true, false, true);
	fn_uc_post_upgrade(DIR_UPGRADE . $_REQUEST['package'], 'revert');

	if (file_exists(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');

		if (isset($udata[$_REQUEST['package']])) {
			unset($udata[$_REQUEST['package']]);
		}

		if (!empty($udata)) {
			fn_uc_update_installed_upgrades($udata);
		} else {
			fn_rm(DIR_UPGRADE . 'installed_upgrades.php');
		}
	}

	fn_rm(DIR_UPGRADE . 'packages.xml'); // cleanup packages list
	fn_uc_cleanup_cache($_REQUEST['package'], 'revert');

	fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_uc_upgrade_reverted'));

	return array(CONTROLLER_STATUS_OK, "upgrade_center.manage");


} elseif ($mode == 'summary') {

	fn_rm(DIR_UPGRADE . 'packages.xml'); // cleanup packages list

} elseif ($mode == 'installed_upgrades') {

	fn_add_breadcrumb(fn_get_lang_var('upgrade_center'), "upgrade_center.manage");

	$udata = array();
	if (file_exists(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');
	}

	$packages = array();
	foreach ($udata as $pkg => $f) {
		$details = array();
		if (file_exists(DIR_UPGRADE . $pkg . '/package_details.php')) {
			$details = include(DIR_UPGRADE . $pkg . '/package_details.php');
		}
		$packages[$pkg] = array(
			'details' => $details,
			'files' => $f['files']
		);
	}

	if (empty($packages)) {
		return array(CONTROLLER_STATUS_REDIRECT, "upgrade_center.manage");
	}

	$view->assign('packages', $packages);

} elseif ($mode == 'diff') {

	fn_add_breadcrumb(fn_get_lang_var('upgrade_center'), "upgrade_center.manage");
	fn_add_breadcrumb(fn_get_lang_var('installed_upgrades'), "upgrade_center.installed_upgrades");

	$view->assign('diff', fn_text_diff(fn_get_contents(DIR_UPGRADE . $_REQUEST['package'] . '/backup/' . $_REQUEST['file']), fn_get_contents(DIR_ROOT . '/' . $_REQUEST['file'])));

} elseif ($mode == 'conflicts') {

	if (file_exists(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');

		if (isset($udata[$_REQUEST['package']]['files'][$_REQUEST['file']])) {
			$udata[$_REQUEST['package']]['files'][$_REQUEST['file']] = ($action == 'mark') ? true : false;

			fn_uc_update_installed_upgrades($udata);
		}
	}

	return array(CONTROLLER_STATUS_OK, "upgrade_center.installed_upgrades");

} elseif ($mode == 'remove') {

	if (!empty($_REQUEST['package'])) {
		$dirs = fn_get_dir_contents(DIR_UPGRADE, true, false);
		$delete_dirs = array();
		preg_match_all("/(\d+)\.(\d+)\.(\d+)\.tgz$/", $_REQUEST['package'], $v);
		$c_ver_int = $v[1][0] * 10000 + $v[2][0] * 1000 + $v[3][0];
		foreach ($dirs as $dir) {
			if (preg_match_all("/(\d+)\.(\d+)\.(\d+)\.tgz$/", $dir, $v)) {
				$ver_int = $v[1][0] * 10000 + $v[2][0] * 1000 + $v[3][0];
				if ($ver_int <= $c_ver_int) {
					$delete_dirs[] = $dir;
				}
			}
		}

		if (file_exists(DIR_UPGRADE . 'installed_upgrades.php')) {
			include(DIR_UPGRADE . 'installed_upgrades.php');
		}

		if (!empty($delete_dirs)) {
			foreach ($delete_dirs as $dir) {
				fn_rm(DIR_UPGRADE . $dir, true);

				if (!empty($udata[$dir])) {
					unset($udata[$dir]);
				}
			}
		}

		if (!empty($udata)) {
			fn_uc_update_installed_upgrades($udata);
		} else {
			fn_rm(DIR_UPGRADE . 'installed_upgrades.php');
		}
	}

	return array(CONTROLLER_STATUS_OK, "upgrade_center.installed_upgrades");

}

/**
 * Get upgrade packages list
 *
 * @param array $uc_settings Upgrade center settings
 * @return array packages list
 */
function fn_uc_get_packages($uc_settings)
{
	$result = array();

	// Cache packages list
	if (!file_exists(DIR_UPGRADE . 'packages.xml') || filemtime(DIR_UPGRADE . 'packages.xml') < (TIME - 60 * 60 * 24)) {
		$data = fn_get_contents($uc_settings['updates_server'] . '/index.php?target=product_updates&mode=get_available&ver=' . PRODUCT_VERSION);
		// $data = fn_get_contents($uc_settings['updates_server'] . '/index.php?target=product_updates&mode=get_available&ver=' . PRODUCT_VERSION . '&license_number=' . $uc_settings['license_number']);
		/* NULLED BY FLIPMODE! @ 2010/09/06 */
		fn_put_contents(DIR_UPGRADE . 'packages.xml', $data);
	} else {
		$data = fn_get_contents(DIR_UPGRADE . 'packages.xml');
	}

	if (!empty($data)) {
		$xml = simplexml_load_string($data, NULL, LIBXML_NOERROR);
		if (!empty($xml)) {
			// Get array with original files hashes
			if (isset($xml->packages)) {
				foreach ($xml->packages->item as $package) {

					$c = array();
					if (isset($package->contents)) {
						foreach ($package->contents->item as $item) {
							$c[] = str_replace('package/', '', (string)$item);
						}
					}

					$result[] = array(
						'md5' => (string)$package->file['md5'],
						'package_id' => (string)$package['id'],
						'file' => (string)$package->file,
						'name' => (string)$package->name,
						'timestamp' => (string)$package->timestamp,
						'description' => (string)$package->description,
						'from_version' => (string)$package->from_version,
						'to_version' => (string)$package->to_version,
						'size' => (string)$package->size,
						'is_avail' => (string)$package->is_avail,
						'purchase_time_limit' => (string)$package->purchase_time_limit,
						'contents' => $c
					);
				}
			}

			if (isset($xml->errors)) {
				foreach ($xml->errors->item as $error) {
					fn_set_notification('E', fn_get_lang_var('error'), (string)$error);
				}
				fn_rm(DIR_UPGRADE . 'packages.xml'); // if we have errors, do not cache server response
			}
		}
	}

	return $result;
}


/**
 * Get upgrade package details
 *
 * @param int $package_id package ID
 * @return array package details
 */
function fn_uc_get_package_details($package_id)
{
	$result = array();

	$data = fn_get_contents(DIR_UPGRADE . 'packages.xml');
	if (!empty($data)) {
		$xml = simplexml_load_string($data, NULL, LIBXML_NOERROR);
		if (!empty($xml)) {
			// Get array with original files hashes
			if (isset($xml->packages)) {
				foreach ($xml->packages->item as $p) {
					if ((string)$p['id'] == $package_id) {
						$result = array(
							'md5' => (string)$p->file['md5'],
							'package_id' => (string)$p['id'],
							'file' => (string)$p->file,
							'name' => (string)$p->name,
							'description' => (string)$p->description,
							'timestamp' => (string)$p->timestamp,
							'size' => (string)$p->size,
							'is_avail' => (string)$p->is_avail,
							'purchase_time_limit' => (string)$p->purchase_time_limit,
							'from_version' => (string)$p->from_version,
							'to_version' => (string)$p->to_version,
						);

						if (isset($p->contents)) {
							foreach ($p->contents->item as $item) {
								$result['contents'][] = (string)$item;
							}
						}

						break;
					}
				}
			}
		}
	}

	return $result;
}

/**
 * Get upgrade package
 *
 * @param int $package_id package ID
 * @param string $md5 md5 hash of package
 * @param array $package package details
 * @param array $uc_settings Upgrade center settings
 * @return boolean true if package downloaded and extracted successfully, false - otherwise
 */
function fn_uc_get_package($package_id, $md5, $package, $uc_settings)
{
	$result = true;
	
	if ($package['is_avail'] != 'Y'){
		return false;
	}
	
	$data = fn_get_contents($uc_settings['updates_server'] . '/index.php?target=product_updates&mode=get_package&package_id=' . $package_id);
	/* NULLED BY FLIPMODE! @ 2010/09/06 */
	// $data = fn_get_contents($uc_settings['updates_server'] . '/index.php?target=product_updates&mode=get_package&package_id=' . $package_id . '&license_number=' . $uc_settings['license_number']);
	if (!empty($data)) {

		fn_put_contents(DIR_UPGRADE . 'uc.tgz', $data);

		if (md5_file(DIR_UPGRADE . 'uc.tgz') == $md5) {
			$dir = basename($package['file']);
			fn_mkdir(DIR_UPGRADE . $dir);
			fn_put_contents(DIR_UPGRADE . $dir . '/package_details.php', "<?php\n return " . var_export($package, true) . "; \n?>");

			return fn_decompress_files(DIR_UPGRADE . 'uc.tgz', DIR_UPGRADE . $dir);
		} else {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_broken_package'));
			$result = false;
		}
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_cant_download_package'));
		$result = false;
	}

	return $result;
}

/**
 * Check if files can be upgraded
 *
 * @param string $path files path
 * @param array $hash_table table with hashes of original files
 * @param array $result resulting array
 * @param string $package package to check files from
 * @param array $custom_skin_files list of custom skin files
 * @return boolean always true
 */
function fn_uc_check_files($path, $hash_table, &$result, $package, $custom_skin_files)
{
	// Simple copy for a file
    if (is_file($path)) {
		// Get original file name
		$original_file = str_replace(DIR_UPGRADE . $package . '/package/', DIR_ROOT . '/', $path);
		$relative_file = str_replace(DIR_ROOT . '/', '', $original_file);
		$file_name = basename($original_file);

		if (file_exists($original_file)) {
			if (md5_file($original_file) != md5_file($path)) {

				$_relative_file = $relative_file;
				// For skins, convert relative path to skins_repository
				if (strpos($relative_file, 'skins/') === 0) {
					$_relative_file = preg_replace('/skins\/[\w]+\//', 'var/skins_repository/base/', $relative_file);

					// replace all skins except basic
					if (fn_uc_check_array_value($relative_file, $custom_skin_files) && strpos($relative_file, '/basic/') === false) {
						$_relative_file = preg_replace('/skins\/([\w]+)\//', 'var/skins_repository/${1}/', $relative_file);
					}
				}

				if (!empty($hash_table[$_relative_file])) {
					if (md5_file($original_file) != $hash_table[$_relative_file]) {
						$result['changed'][] = $relative_file;
					}
				} else {
					$result['changed'][] = $relative_file;
				}
			}
		} else {
			$result['new'][] = $relative_file;
		}

		$status = fn_uc_is_writable($original_file, true);
		if ($status['result'] == false) {
			$result['non_writable'][] = $relative_file;
		}

		if ($status['no_ftp'] == true) {
			$result['no_ftp'] = true;
		}

		return true;
    }

	if (is_dir($path)) {
		$dir = dir($path);
		while (false !== ($entry = $dir->read())) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			fn_uc_check_files(rtrim($path, '/') . '/' . $entry, $hash_table, $result, $package, $custom_skin_files);
		}
		// Clean up
		$dir->close();
		return true;
	} else {
		return false;
	}
}

/**
 * Check if file is writable
 *
 * @param string $path file path
 * @param boolean $extended return extended status
 * @return boolean true if file is writable, false - otherwise
 */
function fn_uc_is_writable($path, $extended = false)
{
	$result = false;
	$extended_result = array(
		'result' => false,
		'no_ftp' => false,
		'method' => ''
	);

	// File does not exist, check if directory is writable
	if (!file_exists($path)) {
		$a = explode('/', $path);
		do {
			array_pop($a);
		} while (!is_dir(implode('/', $a)));

		$path = implode('/', $a);
	}

	// Check if file can be written using php
	if (!fn_uc_is_writable_dest($path)) {
		$result = fn_uc_ftp_is_writable($path);
		if ($result == false) {
			$ftp = Registry::get('uc_ftp');
			if (!is_resource($ftp)) {
				$extended_result['no_ftp'] = true;
			}
		} else {
		    $extended_result['method'] = 'ftp';
		}
	} else {
		$result = true;
		$extended_result['method'] = 'fs';
	}

	$extended_result['result'] = $result;

	return ($extended) ? $extended_result : $result;
}

/**
 * Create directory taking into account accessibility via php/ftp
 *
 * @param string $dir directory
 * @return boolean true if directory created successfully, false - otherwise
 */
function fn_uc_mkdir($dir)
{
	// Try to make directory using php
	$r = fn_uc_is_writable($dir, true);

	$result = $r['result'];
	if ($r['method'] == 'fs') {
		$result = fn_mkdir($dir);
	} elseif ($r['method'] == 'ftp') {
		$result = fn_uc_ftp_mkdir($dir);
	}

	return $result;
}

/**
 * Copy file taking into account accessibility via php/ftp
 *
 * @param string $source source file
 * @param string $dest destination file/directory
 * @return boolean true if directory copied correctly, false - otherwise
 */
function fn_uc_copy($source, $dest)
{
	$result = false;
	$file_name = basename($source);

	if (!file_exists($dest)) {
		if (basename($dest) == $file_name) { // if we're copying the file, create parent directory
			fn_uc_mkdir(dirname($dest));
		} else {
			fn_uc_mkdir($dest);
		}
	}

	fn_echo(' .');

	if (fn_uc_is_writable_dest($dest) || (fn_uc_is_writable_dest(dirname($dest)) && !file_exists($dest))) {
		if (is_dir($dest)) {
			$dest .= '/' . basename($source);
		}
		$result = copy($source, $dest);
		@chmod($dest, DEFAULT_FILE_PERMISSIONS);
	} else { // try ftp
		$result = fn_uc_ftp_copy($source, $dest);
	}

	return $result;
}

/**
 * Check if destination is writable
 *
 * @param string $dest destination file/directory
 * @return boolean true if writable, false - if not
 */
function fn_uc_is_writable_dest($dest)
{
	$dest = rtrim($dest, '/');

	if (is_file($dest)) {
		$f = @fopen($dest, 'ab');
		if ($f === false) {
			return false;
		}
		fclose($f);
	} elseif (is_dir($dest)) {
		if (!fn_put_contents($dest . '/zzzz.zz', '1')) {
			return false;
		}
		fn_rm($dest . '/zzzz.zz');
	} else {
		return false;
	}

	return true;
}

/**
 * Copy files from one directory to another
 *
 * @param string $source source directory
 * @param string $dest destination directory
 * @return boolean true if directory copied correctly, false - otherwise
 */
function fn_uc_copy_files($source, $dest)
{
	// Simple copy for a file
    if (is_file($source)) {
		return fn_uc_copy($source, $dest);
    }

    // Loop through the folder
	if (is_dir($source)) {
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep copy directories
			if ($dest !== $source . '/' . $entry) {
				if (fn_uc_copy_files(rtrim($source, '/') . '/' . $entry, $dest . '/' . $entry) == false) {
					return false;
				}
			}
		}

		// Clean up
		$dir->close();

		return true;
	} else {
		return false;
	}
}

/**
 * Run post-upgrade script
 *
 * @param string $path directory with post-upgrade script
 * @param string $upgrade_type script execution type - "upgrade" or "revert"
 * @return boolean always true
 */
function fn_uc_post_upgrade($path, $upgrade_type)
{
	if (file_exists($path . '/uc.php')) {
		include($path . '/uc.php');
	}

	return true;
}

/**
 * Create directory structure for upgrade
 *
 * @return boolean true if structured created correctly, false - otherwise
 */
function fn_uc_create_structure()
{
	return fn_mkdir(DIR_UPGRADE);
}

/**
 * Create directory structure for current active skins and copy templates there
 *
 * @param string $path path with skins repository
 * @param string $package package to create skins structure in
 * @param array $skip_files list of files that should not be copied to installed skins
 * @param array $custom_skin_files list of custom skin files
 * @return boolean true if structured created correctly, false - otherwise
 */
function fn_uc_create_skins($path, $package, $skip_files, $custom_skin_files)
{
	static $installed_skins = array();
	if (empty($installed_skins)) {
		$installed_skins = fn_get_dir_contents(DIR_SKINS, true, false);
	}

	if (is_file($path)) {
		$files = array();
		if (strpos($path, DIR_REPOSITORY) !== false) {
			// customer skin
			if (strpos($path, DIR_REPOSITORY . 'customer/') !== false || strpos($path, DIR_REPOSITORY . 'mail/') !== false) {
				foreach ($installed_skins as $s) {
					if (!fn_uc_check_array_value($path, $custom_skin_files) || $s = 'basic') { // copy non-custom files only
						$files[] = str_replace(DIR_UPGRADE . $package . '/package/' . DIR_REPOSITORY, DIR_UPGRADE . $package . '/package/skins/' . $s . '/', $path);
					}
				}
			// admin skin
			} else {
				$files[] = str_replace(DIR_UPGRADE . $package . '/package/' . DIR_REPOSITORY, DIR_UPGRADE . $package . '/package/skins/' . Registry::get('settings.skin_name_admin') . '/', $path);
			}

		// Copy data from alternative skins
		} elseif (strpos($path, 'var/skins_repository/' . Registry::get('settings.skin_name_customer')) !== false) {
			$files[] = str_replace(DIR_UPGRADE . $package . '/package/var/skins_repository/', DIR_UPGRADE . $package . '/package/skins/', $path);
		}

		foreach ($files as $file) {
			$fname = basename($file);
			if (!in_array($fname, $skip_files) && !(file_exists($file) && strpos($path, '/base/') !== false)) {
				fn_mkdir(dirname($file));
				fn_copy($path, dirname($file));
			}
		}

		return true;
    }

	if (is_dir($path)) {
		$dir = dir($path);
		while (false !== ($entry = $dir->read())) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			fn_uc_create_skins(rtrim($path, '/') . '/' . $entry, $package, $skip_files, $custom_skin_files);
		}
		// Clean up
		$dir->close();
		return true;
	} else {
		return false;
	}
}
/**
 * Check if file is writable using ftp
 *
 * @param string $path file path
 * @return boolean true if file is writable, false - otherwise
 */
function fn_uc_ftp_is_writable($path)
{
	$result = false;
	// If ftp connection is available, check file/directory via ftp
	$ftp = Registry::get('uc_ftp');
	if (is_resource($ftp)) {
		$rel_path = ltrim(str_replace(DIR_ROOT, '', $path), '/');
		if (empty($rel_path)) {
			$rel_path = '.';
		}
		$ftp_path = (is_dir($path) || is_file($path)) ?  $rel_path : (dirname($rel_path));
		if (ftp_site($ftp, "CHMOD 0755 $ftp_path")) {
			$result = true;
		}
	}

	return $result;

}

/**
 * Copy file using ftp
 *
 * @param string $source source file
 * @param string $dest destination file/directory
 * @return boolean true if copied successfully, false - otherwise
 */
function fn_uc_ftp_copy($source, $dest)
{
	$result = false;

	$ftp = Registry::get('uc_ftp');
	if (is_resource($ftp)) {
		if (!is_dir($dest)) { // file
			$dest = dirname($dest);
		}
		$dest = rtrim($dest, '/') . '/'; // force adding trailing slash to path

		$rel_path = str_replace(DIR_ROOT . '/', '', $dest);
		$cdir = ftp_pwd($ftp);

		if (empty($rel_path)) { // if rel_path is empty, assume it's root directory
		    $rel_path = $cdir;
		}

		if (ftp_chdir($ftp, $rel_path) && ftp_put($ftp, basename($source), $source, FTP_BINARY)) {
			$ext = fn_get_file_ext($source);
			@ftp_site($ftp, "CHMOD " . ((in_array($ext, array('tpl', 'css'))) ? DEFAULT_FILE_PERMISSIONS : '0644') . " " . basename($source)); // set full permissions for templates and css files
			$result = true;
			ftp_chdir($ftp, $cdir);
		}
	}

	return $result;
}

/**
 * Create directory using ftp
 *
 * @param string $dir directory
 * @return boolean true if directory created successfully, false - otherwise
 */
function fn_uc_ftp_mkdir($dir)
{
	$ftp = Registry::get('uc_ftp');
	if (is_resource($ftp)) {
		if (@!is_dir($dir)) {
			$rel_path = str_replace(DIR_ROOT . '/', '', $dir);
			$path = '';
			$dir_arr = array();
			if (strstr($rel_path, '/')) {
				$dir_arr = explode('/', $rel_path);
			} else {
				$dir_arr[] = $rel_path;
			}

			foreach ($dir_arr as $k => $v) {
				$path .= (empty($k) ? '' : '/') . $v;
				if (!@is_dir(DIR_ROOT . '/' . $path)) {
					if (ftp_mkdir($ftp, $path)) {
						$result = true;
					} else {
						$result = false;
						break;
					}
				}
			}
		}

		return $result;
	}
}

/**
 * Connect to ftp server
 *
 * @param array $uc_settings upgrade center options
 * @return boolean true if connected successfully and working directory is correct, false - otherwise
 */
function fn_uc_ftp_connect($uc_settings)
{
	$result = true;

	if (function_exists('ftp_connect')) {
		if (!empty($uc_settings['ftp_hostname'])) {
			$ftp = ftp_connect($uc_settings['ftp_hostname']);
			if (!empty($ftp)) {
				if (@ftp_login($ftp, $uc_settings['ftp_username'], $uc_settings['ftp_password'])) {
					if (!empty($uc_settings['ftp_directory'])) {
						@ftp_chdir($ftp, $uc_settings['ftp_directory']);
					}

					$files = ftp_nlist($ftp, '.');
					if (!empty($files) && in_array('config.php', $files)) {
						Registry::set('uc_ftp', $ftp);
					} else {
						fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_ftp_cart_directory_not_found'));
						$result = false;					
					}
				} else {
					fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_ftp_login_failed'));
					$result = false;
				}
			} else {
				fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_ftp_connect_failed'));
				$result = false;
			}
		}
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_no_ftp_module'));
		$result = false;
	}

	return $result;
}

/**
 * Backup database data which will be affected during upgrade
 *
 * @param string $path path to backup directory
 * @return array backed up tables list
 */
function fn_uc_backup_database($path)
{
	$tables = array();

	if (file_exists($path . '/uc.sql')) {

		$f = fopen($path . '/uc.sql', 'rb');
		if (!empty($f))  {
			while (!feof($f)) {
				$s = fgets($f);

				if (preg_match_all("/(INSERT INTO|REPLACE INTO|UPDATE|ALTER TABLE|RENAME TABLE|DELETE FROM|DROP TABLE|CREATE TABLE)( IF EXISTS| IF NOT EXISTS)? [`]?(\w+)[`]?/", $s, $m)) {
					$tables[$m[3][0]] = true;
				}
			}
			fclose($f);
		}
	}

	$tables = array_keys($tables);
	@fn_uc_rm($path . '/backup/uc.sql');
	return fn_uc_backup_tables($tables, $path . '/backup/uc.sql');
}

/**
 * Backup files
 *
 * @param string $source upgrade package directory
 * @param string $dest working directory
 * @param array $result resulting list of backed up files
 * @param string $package package to make backup for
 * @return boolean true if directory copied correctly, false - otherwise
 */
function fn_uc_backup_files($source, $dest, &$result, $package)
{
	// Simple copy for a file
    if (is_file($source)) {
        return fn_uc_backup_file($source, $dest, $result, $package);
    }

    // Loop through the folder
	if (is_dir($source)) {
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep backup directories
			if ($dest !== $source . '/' . $entry) {
				if (fn_uc_backup_files(rtrim($source, '/') . '/' . $entry, $dest . '/' . $entry, $result, $package) == false) {
					return false;
				}
			}
		}

		// Clean up
		$dir->close();

		return true;
	} else {
		return false;
	}
}

/**
 * Backup certain file
 *
 * @param string $source source file
 * @param string $dest destination file/directory
 * @param array $result resulting list of backed up files
 * @param string $package package to make backup for
 * @return string filename of backed up file
 */
function fn_uc_backup_file($source, $dest, &$result, $package)
{
	$file_name = basename($source);

	if (is_file($dest)) {
		fn_echo(' .');
		$relative_path = str_replace(DIR_ROOT . '/', '', $dest);
		fn_mkdir(dirname(DIR_UPGRADE . $package . '/backup/' . $relative_path));
		fn_copy($dest, DIR_UPGRADE . $package . '/backup/' . $relative_path);
		$result[] = $relative_path;
	}

	return true;
}

/**
 * Function backup obsolete files before deleting
 *
 * @param sting $dest Destanation directory
 * @param string $source Source directory
 * @param string $xml_file Path to xml file with list of files.
 */
function fn_uc_backup_obsolete_files($dest, $source, $xml_file)
{
	$files_list = fn_uc_get_files_from_xml($xml_file, 'deleted_files');
	
	$skins_files = fn_uc_find_in_skins($files_list, $source);
	$files_list = array_merge($files_list, $skins_files);

	foreach ($files_list as $l) {
		fn_echo(' .');
		fn_mkdir(dirname($dest . $l));
		fn_copy($source . '/' . $l, $dest . $l);
	}
	
	return $files_list;
}

/**
 * Function remove obsolete or new files after upgrade or reverting upgrade.
 *
 * @param string $source Source directory
 * @param string $xml_file Path to xml file with list of files.
 * @param string $section section of the xml file
 */
function fn_uc_rm_files($source, $xml_file, $section)
{
	$files_list = fn_uc_get_files_from_xml($xml_file, $section);
	
	$skins_files = fn_uc_find_in_skins($files_list, $source);
	$files_list = array_merge($files_list, $skins_files);
	
	foreach ($files_list as $file) {
		fn_uc_rm($source . '/' . $file);
	}
}

/**
 * Function finds files from base skin in all installed skins and return full path to those files.
 *
 * @param array $files Array with relative name of the files
 * @param string $source Path to root folder
 * @return array Array of the copies of file from all installed skins.
 */
function fn_uc_find_in_skins($files, $source)
{
	$base_skin = 'var/skins_repository/base/';
	$len = strlen($base_skin);
	$installed_skins = fn_get_dir_contents(DIR_SKINS);
	
	$result = array();
	foreach ($files as $file) {
		if (substr($file, 0, $len) == $base_skin) {
			foreach ($installed_skins as $skin_name) {
				$relative_name = "skins/$skin_name/" . substr($file, $len);
				if (file_exists($source . '/' . $relative_name)) {
					$result[] = $relative_name;	   
				}
			} 
		}
	}
	
	return $result;
}

/**
 * Function get list of files from package xml. This list will be used for deleting obsolete or new files after upgrading or reverting.
 *
 * @param string $xml_file Path to the xml file
 * @param string $section section of the xml file
 *
 * @return array list of files
 */
function fn_uc_get_files_from_xml($xml_file, $section)
{
	$xml = simplexml_load_file($xml_file, NULL, LIBXML_NOERROR);

	$result = array();
	
	if (!empty($xml)) {
		// Get files list
		if (isset($xml->$section)) {
			foreach ($xml->$section->item as $item) {
				$result[] = (string) $item['file'];
			}
		}
	}
	
	return $result;
}

/**
 * Check installed upgrades
 *
 * @return array array which indicates, if any upgrade has conflicts and if any upgrade exist
 */
function fn_uc_check_installed_upgrades()
{
	$result = array(
		'has_conflicts' => false,
		'has_upgrades' => false,
	);

	if (file_exists(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');

		foreach ($udata as $p => $f) {
			if (!empty($f['files'])) {
				foreach ($f['files'] as $_f => $_s) {
					if ($_s == false) {
						$result['has_conflicts'] = true;
						break;
					}
				}
			}
		}

		$result['has_upgrades'] = sizeof($udata) > 0;
	}

	return $result;
}

function fn_uc_update_installed_upgrades($data)
{
	return fn_put_contents(DIR_UPGRADE . 'installed_upgrades.php', "<?php\n if ( !defined('AREA') )	{ die('Access denied');	}\n \$udata = " . var_export($data, true) . ";\n?>");
}

/**
 * Cleanup upgrade cache
 *
 * @param string $package package name
 * @param string $type upgrade type (upgrade/revert)
 * @return boolean always true
 */
function fn_uc_cleanup_cache($package, $type)
{
	if ($type == 'upgrade') {
		@unlink(DIR_UPGRADE . $package . '/backup/uc.sql.tmp');
	} else {
		@unlink(DIR_UPGRADE . $package . '/uc.sql.tmp');
	}
}

/**
 * Check if array item exists in the string
 *
 * @param string $value string to search array item in
 * @param string $array items list
 * @return boolean true if value found, false - otherwise
 */
function fn_uc_check_array_value($value, $array)
{
	foreach ($array as $v) {
		if (strpos($value, $v) !== false) {
			return true;
		}
	}

	return false;
}

function fn_uc_rm($path)
{
	// Try to make directory using php
	$r = fn_uc_is_writable($path, true);

	$result = $r['result'];
	if ($r['method'] == 'fs') {
		$result = fn_rm($path);
	} elseif ($r['method'] == 'ftp') {
		$result = fn_uc_ftp_rm($path);
	}

	return $result;
}

function fn_uc_ftp_rm($path)
{
	$ftp = Registry::get('uc_ftp');
	if (is_resource($ftp)) {
		$rel_path = str_replace(DIR_ROOT . '/', '', $path);
		if (is_file($path)) {
			return @ftp_delete($ftp, $rel_path);
		}

		// Loop through the folder
		if (is_dir($path)) {
			$dir = dir($path);
			while (false !== $entry = $dir->read()) {
				// Skip pointers
				if ($entry == '.' || $entry == '..') {
					continue;
				}
				if (fn_uc_ftp_rm($path . '/' . $entry) == false) {
					return false;
				}
			}
			// Clean up
			$dir->close();
			return @ftp_rmdir($ftp, $rel_path);
		} else {
			return false;
		}
	}

	return false;
}

/**
 * Functions creates dump of tables in $file.
 *
 * @param mixed $tables Array of tables
 * @param string $file Dump file name
 * @return Boolean False on failure
 */
function fn_uc_backup_tables($tables, $file)
{
	$rows_per_pass = 40;
	$max_row_size = 10000;

	if (!empty($tables)) {
		if (!is_array($tables)) {
			$tables = array($tables);
		}

		$t_status = db_get_hash_array("SHOW TABLE STATUS", 'Name');
		$f = fopen($file, 'ab');
		if (!empty($f)) {
			foreach ($tables as &$table) {
				$table = fn_check_db_prefix($table);

				fwrite($f, "\nDROP TABLE IF EXISTS " . str_replace('?:', TABLE_PREFIX, $table) . ";\n");
				if (empty($t_status[str_replace('?:', TABLE_PREFIX, $table)])) { // new table in upgrade, we need drop statement only
					continue;
				}
				$scheme = db_get_row("SHOW CREATE TABLE $table");
				fwrite($f, array_pop($scheme) . ";\n\n");

				$total_rows = db_get_field("SELECT COUNT(*) FROM $table");

				// Define iterator
				if ($t_status[str_replace('?:', TABLE_PREFIX, $table)]['Avg_row_length'] < $max_row_size) {
					$it = $rows_per_pass;
				} else {
					$it = 1;
				}

				fn_echo(' .');

				for ($i = 0; $i < $total_rows; $i = $i + $it) {
					$table_data = db_get_array("SELECT * FROM $table LIMIT $i, $it");
					foreach ($table_data as $_tdata) {
						$_tdata = fn_add_slashes($_tdata, true);
						fwrite($f, "INSERT INTO " . str_replace('?:', TABLE_PREFIX, $table) . " (`".implode('`, `', array_keys($_tdata))."`) VALUES ('".implode('\', \'', array_values($_tdata))."');\n");
					}
				}
			}
			fclose($f);
			@chmod($file, DEFAULT_FILE_PERMISSIONS);
			return $tables;
		}
	} else {
		return array();
	}
}

/**
 * Function updates language variables for all installed languages.
 *
 * @param string $table Name of table for updating
 * @param mixed $keys Array of primary keys in table for data comparison
 * @param boolean $show_process Echo or no ' .' for showing process.
 */
function fn_uc_update_alt_languages($table, $keys, $show_process = true)
{
	static $langs;

	if (empty($langs)) {
		$langs = db_get_fields("SELECT lang_code FROM ?:languages");
	}

	if (!is_array($keys)) {
		$keys = array($keys);
	}

	$i = 0;
	$step = 50;
	while ($items = db_get_array("SELECT * FROM ?:$table WHERE lang_code = 'EN' LIMIT $i, $step")) {
		$i += $step;
		foreach ($items as $v) {
			foreach ($langs as $lang) {
				$condition = array();
				foreach ($keys as $key) {
					$condition[] = "$key = '" . $v[$key] . "'";
				}
				$condition = implode(' AND ', $condition);
				$exists = db_get_field("SELECT COUNT(*) FROM ?:$table WHERE $condition AND lang_code = ?s", $lang);
				if (empty($exists)) {
					$v['lang_code'] = $lang;
					db_query("REPLACE INTO ?:$table ?e", $v);
					if ($show_process) {
						fn_echo(' .');
					}
				}
			}
		}
	}
}

?>