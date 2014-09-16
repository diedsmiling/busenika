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
// $Id: database.php 10302 2010-08-03 05:35:45Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

fn_define('DB_MAX_ROW_SIZE', 10000);
fn_define('DB_ROWS_PER_PASS', 40);

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

	set_time_limit(3600);

	$suffix = '';

	// Backup database
	if ($mode == 'backup') {

		$dbdump_filename = empty($_REQUEST['dbdump_filename']) ? 'dump_'.date('mdY').'.sql' : $_REQUEST['dbdump_filename'];

		if (!fn_mkdir(DIR_DATABASE . 'backup')) {
			$err_msg = str_replace('[directory]', DIR_DATABASE . 'backup',fn_get_lang_var('text_cannot_create_directory'));
			fn_set_notification('E', fn_get_lang_var('error'), $err_msg);
			return array(CONTROLLER_STATUS_REDIRECT, "database.manage");
		}
		$dump_file = DIR_DATABASE . 'backup/' . $dbdump_filename;
		if (is_file($dump_file)) {
			if (!is_writable($dump_file)) {
				fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('dump_file_not_writable'));
				return array(CONTROLLER_STATUS_REDIRECT, "database.manage");
			}
		}

		$fd = @fopen(DIR_DATABASE . 'backup/' . $dbdump_filename, 'w');
		if (!$fd) {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('dump_cant_create_file'));
			return array(CONTROLLER_STATUS_REDIRECT, "database.manage");
		}

		// Log database backup
		fn_log_event('database', 'backup');

		// set export format
		db_query("SET @SQL_MODE = 'MYSQL323'");

		fn_start_scroller();
		$create_statements = array();
		$insert_statements = array();

		$dbdump_tables = empty($_REQUEST['dbdump_tables']) ? array() : $_REQUEST['dbdump_tables'];

		// get status data
		$t_status = db_get_hash_array("SHOW TABLE STATUS", 'Name');

		foreach ($dbdump_tables as $k => $table) {
			if (!empty($_REQUEST['dbdump_schema']) && $_REQUEST['dbdump_schema'] == 'Y') {
				fn_echo('<br>'.fn_get_lang_var('backupping_schema').': <b>'.$table.'</b>');
				fwrite($fd, "\nDROP TABLE IF EXISTS ".$table.";\n");
				$__scheme = db_get_row("SHOW CREATE TABLE $table");
				fwrite($fd, array_pop($__scheme).";\n\n");
			}

			if (!empty($_REQUEST['dbdump_data']) &&  $_REQUEST['dbdump_data'] == 'Y') {
				fn_echo('<br />'.fn_get_lang_var('backupping_data').': <b>'.$table.'</b>&nbsp;&nbsp;');
				$total_rows = db_get_field("SELECT COUNT(*) FROM $table");

				// Define iterator
				if (!empty($t_status[$table]) && $t_status[$table]['Avg_row_length'] < DB_MAX_ROW_SIZE) {
					$it = DB_ROWS_PER_PASS;
				} else {
					$it = 1;
				}
				for ($i = 0; $i < $total_rows; $i = $i + $it) {
					$table_data = db_get_array("SELECT * FROM $table LIMIT $i, $it");
					foreach ($table_data as $_tdata) {
						$_tdata = fn_add_slashes($_tdata, true);
						fwrite($fd, "INSERT INTO $table (`".implode('`, `', array_keys($_tdata))."`) VALUES ('".implode('\', \'', array_values($_tdata))."');\n");
					}

					fn_echo(' .');
				}
			}
		}
		fclose($fd);
		@chmod(DIR_DATABASE . 'backup/' . $dbdump_filename, DEFAULT_FILE_PERMISSIONS);

		if ($_REQUEST['dbdump_compress'] == 'Y') {
			fn_echo('<br />'.fn_get_lang_var('compressing_backup').'...');

			fn_compress_files($dbdump_filename.'.tgz', $dbdump_filename, dirname($dump_file));
			unlink($dump_file);
		}

		fn_stop_scroller();
	}

	// Restore
	if ($mode == 'restore') {

		if (!empty($_REQUEST['backup_files'])) {

			// Log database restore
			fn_log_event('database', 'restore');
			fn_start_scroller();

			foreach ($_REQUEST['backup_files'] as $file) {

				fn_echo('<br />' . fn_get_lang_var('restoring_from') . ': <b>' . $file . '</b><hr width="100%" />');
				if (strpos($file, '.tgz') !== false) {
					fn_decompress_files($file, DIR_DATABASE . '/backup');
					$file = substr($file, 0, strpos($file,'.tgz'));
				}

				db_import_sql_file(DIR_DATABASE . 'backup/' . $file);
			}

			fn_stop_scroller();

			fn_rm(DIR_CACHE, false); // cleanup cache
		}

		$suffix = '?selected_section=restore';
	}

	if ($mode == 'delete') {
		if (!empty($_REQUEST['backup_files'])) {
			foreach ($_REQUEST['backup_files'] as $file) {
				@unlink(DIR_DATABASE . 'backup/' . $file);
			}
		}
	}

	if ($mode == 'upload') {
		$sql_dump = fn_filter_uploaded_data('sql_dump');

		if (!empty($sql_dump)) {
			$sql_dump = array_shift($sql_dump);
			fn_copy($sql_dump['path'], DIR_DATABASE . 'backup/' . $sql_dump['name']);
		}
	}

	if ($mode == 'optimize') {
		// Log database optimization
		fn_log_event('database', 'optimize');

		$all_tables = db_get_fields("SHOW TABLES");
		fn_start_scroller();
		foreach ($all_tables as $table) {
			fn_echo(fn_get_lang_var('optimizing_table') . "&nbsp;<b>$table</b>...<br />");

			db_query("OPTIMIZE TABLE $table");
			db_query("ANALYZE TABLE $table");
			$fields = db_get_hash_array("SHOW COLUMNS FROM $table", 'Field');

			if (!empty($fields['is_global'])) { // Sort table by is_global field
				fn_echo(fn_get_lang_var('sorting_data') . "&nbsp;<b>$table</b>...<br />");
				db_query("ALTER TABLE $table ORDER BY is_global DESC");
			}
			elseif (!empty($fields['position'])) { // Sort table by position field
				fn_echo(fn_get_lang_var('sorting_data') . "&nbsp;<b>$table</b>...<br />");
				db_query("ALTER TABLE $table ORDER BY position");
			}
		}
		fn_stop_scroller();
	}


	return array(CONTROLLER_STATUS_OK, "database.manage$suffix");
}


if ($mode == 'getfile' && !empty($_REQUEST['file'])) {
	fn_get_file(DIR_DATABASE . 'backup/' . basename($_REQUEST['file']));

} elseif ($mode == 'manage') {

	Registry::set('navigation.tabs', array (
		'backup' => array (
			'title' => fn_get_lang_var('backup'),
			'js' => true
		),
		'restore' => array (
			'title' => fn_get_lang_var('restore'),
			'js' => true
		),
		'maintenance' => array (
			'title' => fn_get_lang_var('maintenance'),
			'js' => true
		),
	));

	// Calculate database size and fill tables array
	$status_data = db_get_array("SHOW TABLE STATUS");
	$database_size = 0;
	$all_tables = array();
	foreach ($status_data as $k => $v) {
		$database_size += $v['Data_length'] + $v['Index_length'];
		$all_tables[] = $v['Name'];
	}

	$view->assign('database_size', $database_size);
	$view->assign('all_tables', $all_tables);

	$files = fn_get_dir_contents(DIR_DATABASE . 'backup', false, true, array('.sql', '.tgz'));
	sort($files, SORT_STRING);
	$backup_files = array();
	if (is_array($files)) {
		foreach ($files as $file) {
			$backup_files[$file]['size'] = filesize(DIR_DATABASE . 'backup/' . $file);
			$backup_files[$file]['type'] = strpos($file, '.tgz')===false ? 'sql' : 'tgz';
		}
	}

	$view->assign('backup_files', $backup_files);
	$view->assign('backup_dir', DIR_DATABASE . 'backup/');

} elseif ($mode == 'delete') {
	if (!empty($_REQUEST['backup_file'])) {
		@unlink(DIR_DATABASE . 'backup/' . $_REQUEST['backup_file']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "database.manage?selected_section=restore");
}

?>