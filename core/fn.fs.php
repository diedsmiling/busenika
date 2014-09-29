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
// $Id: fn.fs.php 10532 2010-08-26 16:30:29Z andyye $
//

if ( !defined('AREA') )	{ die('Access denied');	}

//
// File system functions definitions
//

/**
 * Delete file function
 *
 * @param string $file_path file location
 * @return bool true
 */
function fn_delete_file($file_path)
{
	if (!empty($file_path)) {
		if (is_file($file_path)) {
			@chmod($file_path, 0775);
			@unlink($file_path);
		}
	}

	return true;
}

/**
 * Normalize path: remove "../", "./" and duplicated slashes
 *
 * @param string $path
 * @param string $separator
 * @return string normilized path
 */
function fn_normalize_path($path, $separator = '/')
{

	$result = array();
	$path = preg_replace("/[\\\\\/]+/S", $separator, $path);
	$path_array = explode($separator, $path);
	if (!$path_array[0])  {
		$result[] = '';
	}

	foreach ($path_array as $key => $dir) {
		if ($dir == '..') {
			if (end($result) == '..') {
			   $result[] = '..';
			} elseif (!array_pop($result)) {
			   $result[] = '..';
			}
		} elseif ($dir != '' && $dir != '.') {
			$result[] = $dir;
		}
	}

	if (!end($path_array)) {
		$result[] = '';
	}

	return fn_is_empty($result) ? '' : implode($separator, $result);
}

/**
 * Create directory wrapper. Allows to create included directories
 *
 * @param string $dir
 * @param int $perms permission for new directory
 */
function fn_mkdir($dir, $perms = DEFAULT_DIR_PERMISSIONS)
{
	
	$result = false;

	// Truncate the full path to related to avoid problems with
	// some buggy hostings
	if (strpos($dir, DIR_ROOT) === 0) {
		$dir = './' . substr($dir, strlen(DIR_ROOT) + 1);
		$old_dir = getcwd();
		chdir(DIR_ROOT);
	}

	if (!empty($dir)) {
		$result = true;
		if (@!is_dir($dir)) {
			$dir = fn_normalize_path($dir, '/');
			$path = '';
			$dir_arr = array();
			if (strstr($dir, '/')) {
				$dir_arr = explode('/', $dir);
			} else {
				$dir_arr[] = $dir;
			}

			foreach ($dir_arr as $k => $v) {
				$path .= (empty($k) ? '' : '/') . $v;
				if (!@is_dir($path)) {
					umask(0);
					mkdir($path, $perms);
				}
			}
		}
	}

	if (!empty($old_dir)) {
		chdir($old_dir);
	}
	return $result;
}

/**
 * Compress files with Tar archiver
 *
 * @param string $archive_name - name of the compressed file will be created
 * @param string $file_list - list of files to place into archive
 * @param string $dirname - directory, where the files should be get from
 * @return bool true
 */
function fn_compress_files($archive_name, $file_list, $dirname = '')
{
	include_once(DIR_LIB . 'tar/tar.php');

	$tar = new Archive_Tar($archive_name, 'gz');

	if (!is_object($tar)) {
		fn_error(debug_backtrace(), 'Archiver initialization error', false);
	}

	if (!empty($dirname) && is_dir($dirname)) {
		chdir($dirname);
		$tar->create($file_list);
		chdir(DIR_ROOT);
	} else {
		$tar->create($file_list);
	}

	return true;
}

/**
 * Extract files with Tar archiver
 *
 * @param $archive_name - name of the compressed file will be created
 * @param $file_list - list of files to place into archive
 * @param $dirname - directory, where the files should be extracted to
 * @return bool true
 */
function fn_decompress_files($archive_name, $dirname = '')
{
	include_once(DIR_LIB . 'tar/tar.php');

	$tar = new Archive_Tar($archive_name, 'gz');

	if (!is_object($tar)) {
		fn_error(debug_backtrace(), 'Archiver initialization error', false);
	}

	if (!empty($dirname) && is_dir($dirname)) {
		chdir($dirname);
		$tar->extract('');
		chdir(DIR_ROOT);
	} else {
		$tar->extract('');
	}

	return true;
}

/**
 * Get mime type by the file name
 *
 * @param string $filename
 * @return string $file_type
 */
function fn_get_file_type($filename)
{
	$file_type = 'application/octet-stream';

	static $types = array (
		'zip' => 'application/zip',
		'tgz' => 'application/tgz',
		'rar' => 'application/rar',

		'exe' => 'application/exe',
		'com' => 'application/com',
		'bat' => 'application/bat',

		'png' => 'image/png',
		'jpg' => 'image/jpeg',
		'jpeg' => 'jpeg',
		'gif' => 'image/gif',
		'bmp' => 'image/bmp',
		'swf' => 'application/x-shockwave-flash',

		'csv' => 'text/csv',
		'txt' => 'text/plain',
		'doc' => 'application/msword',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pdf' => 'application/pdf'
	);

	$ext = substr($filename, strrpos($filename, '.') + 1);

	if (!empty($types[$ext])) {
		$file_type = $types[$ext];
    }

    return $file_type;
}

/**
 * Download the file
 *
 * @param string $path path to the file
 * @param string $filename file name to be displayed in download dialog
 */
function fn_get_file($path, $filename = '')
{
	$fd = fopen($path, 'r');
	if ($fd) {
		//Fixes: Filenames can't be sent to IE if there is any kind of traffic compression enabled on the server side 
		if (function_exists('apache_setenv')) {
			apache_setenv('no-gzip', '1');
		} 
		ini_set("zlib.output_compression", "Off");
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-type: " . fn_get_file_type($path));
		header("Content-Length: " . filesize($path));

		if (empty($filename)) {
			//Fixes: Non-ASCII filenames containing spaces and underscore characters are chunked 
			setlocale(LC_ALL, 'en_US.UTF8');
			$filename = basename($path);
		}
		if (USER_AGENT == 'ie') {
			//Fixes: During the file download with IE, non-ASCII filenames appears with a broken encoding 
			$filename = rawurlencode($filename);
		}
		header("Content-disposition: attachment; filename=\"$filename\"");

		while (!feof($fd)) {
			echo(fread($fd, 30000)); // Read by 30k blocks to avoid memory leaks
			fn_flush();
		}
		exit; // stop script execution after reading file contents
	}
	return true;
}

/**
 * Create temporary file for uploaded file
 *
 * @param $val file path
 * @return array $val
 */
function fn_get_server_data($val)
{
	$tmp = fn_strip_slashes($val);

	if (defined('IS_WINDOWS')) {
		$tmp = str_replace('\\', '/', $tmp);
	}
	if (strpos($tmp, DIR_ROOT) === 0) {
		$tmp = substr_replace($tmp, '', 0, strlen(DIR_ROOT));
	}

	$val = array();
	setlocale(LC_ALL, 'en_US.UTF8');
	$val['name'] = basename($tmp);
	$val['path'] = fn_normalize_path(DIR_ROOT . '/' . $tmp);
	$tempfile = fn_create_temp_file();
	fn_copy($val['path'], $tempfile);
	clearstatcache();
	$val['path'] = $tempfile;
	$val['size'] = filesize($val['path']);
	
	$cache = & Registry::get('temp_fs_data');

	if (!isset($cache[$val['path']])) { // cache file to allow multiple usage
		$cache[$val['path']] = $tempfile;
	}

	return $val;
}

/**
 * Rebuild $_FILES array to more user-friendly view
 *
 * @param string $name
 * @return array $rebuilt rebuilt file array
 */
function fn_rebuid_files($name)
{
	$rebuilt = array();

	if (!is_array(@$_FILES[$name])) {
		return $rebuilt;
	}

	if (isset($_FILES[$name]['error'])) {
		if (!is_array($_FILES[$name]['error'])) {
			return $_FILES[$name];
		}
	} elseif (fn_is_empty($_FILES[$name]['size'])) {
		return $_FILES[$name];
	}

	foreach ($_FILES[$name] as $k => $v) {
		if ($k == 'tmp_name') {
			$k = 'path';
		}
		$rebuilt = fn_array_multimerge($rebuilt, $v, $k);
	}

	return $rebuilt;
}

/**
 * Recursively copy directory (or just a file)
 *
 * @param string $source
 * @param string $dest
 * @param bool $silent
 */
function fn_copy($source, $dest, $silent = true)
{   //var_dump($silent);
//var_dump($source);
    // Simple copy for a file
    if (is_file($source)) {
    	if (@is_dir($dest)) {
			$dest .= '/' . basename($source);
		}
		if (filesize($source) == 0) {
			$fd = fopen($dest, 'w');
			fclose($fd);
			$res = true;
		} else {
			$res = @copy($source, $dest);
		}
		@chmod($dest, DEFAULT_FILE_PERMISSIONS);
        //var_dump('1');
        return $res;
    }

    // Make destination directory
	if ($silent == false) {
		fn_echo('Creating directory <b>' . ((strpos($dest, DIR_ROOT) === 0) ? str_replace(DIR_ROOT . '/', '', $dest) : $dest) . '</b><br />');
	}

	if (!@is_dir($dest)) {
        if (fn_mkdir($dest) == false) {
//			var_dump('2');
			return false;
		}
    }

    // Loop through the folder
    
	if (@is_dir($source)) {
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep copy directories
			if ($dest !== $source . '/' . $entry) {
				if (fn_copy($source . '/' . $entry, $dest . '/' . $entry, $silent) == false) {
//					var_dump('3');
					return false;
				}
			}
		}

		// Clean up
		$dir->close();

		return true;
	} else {
	//	var_dump('4');
		return false;
	}
}

/**
 * Recursively remove directory (or just a file)
 *
 * @param string $source
 * @param bool $delete_root
 * @param string $pattern
 * @return bool
 */
function fn_rm($source, $delete_root = true, $pattern = '')
{
    // Simple copy for a file
    if (is_file($source)) {
		$res = true;
		if (empty($pattern) || (!empty($pattern) && preg_match('/' . $pattern . '/', basename($source)))) {
			$res = @unlink($source);
		}
        return $res;
    }

    // Loop through the folder
	if (is_dir($source)) {
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}
	 		if (fn_rm($source . '/' . $entry, true, $pattern) == false) {
				return false;
			}
		}
		// Clean up
		$dir->close();
		return ($delete_root == true && empty($pattern)) ? @rmdir($source) : true;
	} else {
		return false;
	}
}

/**
 * Get file extension
 *
 * @param string $filename
 */
function fn_get_file_ext($filename)
{
	$i = strrpos($filename, '.');
	if ($i === false) {
		return '';
	}

	return substr($filename, $i + 1);
}

/**
 * Get directory contents
 *
 * @param string $dir directory path
 * @param bool $get_dirs get sub directories
 * @param bool $get_files
 * @param mixed $extension allowed file extensions
 * @param string $prefix file/dir path prefix
 * @return array $contents directory contents
 */
function fn_get_dir_contents($dir, $get_dirs = true, $get_files = false, $extension = '', $prefix = '', $recursive = false)
{

	$contents = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {

			// $extention - can be string or array. Transform to array.
			$extension = is_array($extension) ? $extension : array($extension);

			while (($file = readdir($dh)) !== false) {
				if ($file == '.' || $file == '..' || $file{0} == '.') {
					continue;
				}

				if ($recursive == true && is_dir($dir . '/' . $file)) {
					$contents = fn_array_merge($contents, fn_get_dir_contents($dir . '/' . $file, $get_dirs, $get_files, $extension, $prefix . $file . '/', $recursive), false);
				}

				if ((is_dir($dir . '/' . $file) && $get_dirs == true) || (is_file($dir . '/' . $file) && $get_files == true)) {
					if ($get_files == true && !fn_is_empty($extension)) {
						// Check all extentions for file
						foreach ($extension as $_ext) {
						 	if (substr($file, -strlen($_ext)) == $_ext) {
								$contents[] = $prefix . $file;
								break;
						 	}
						}
					} else {
						$contents[] = $prefix . $file;
					}
				}
			}
			closedir($dh);
		}
	}

	asort($contents, SORT_STRING);

	return $contents;
}

/**
 * Get file contents from local or remote filesystem
 *
 * @param string $location file location
 * @param string $base_dir
 * @return string $result
 */
function fn_get_contents($location, $base_dir = '')
{
	$result = '';
	$path = $base_dir . $location;

	if (!empty($base_dir) && !fn_check_path($path)) {
		return $result;
	}

	// Location is regular file
	if (is_file($path)) {
		$result = @file_get_contents($path);

	// Location is url
	} elseif (strpos($path, '://') !== false) {

		// Prepare url
		$path = str_replace(' ', '%20', $path);
		if (fn_get_ini_param('allow_url_fopen') == true) {
			$result = @file_get_contents($path);
		} else {
			list(, $result) = fn_http_request('GET', $path);
		}
	}

	return $result;
}

/**
 * Write a string to a file
 *
 * @param string $location file location
 * @param string $content
 * @param string $base_dir
 * @return string $result
 */
function fn_put_contents($location, $content, $base_dir = '')
{
	$result = '';
	$path = $base_dir . $location;

	if (!empty($base_dir) && !fn_check_path($path)) {
		return false;
	}

	// Location is regular file
	$result = @file_put_contents($path, $content);
	if ($result !== false) {
		@chmod($path, DEFAULT_FILE_PERMISSIONS);
	}
	return $result; 
}

/**
 * Get data from url
 *
 * @param string $val
 * @return array $val
 */
function fn_get_url_data($val)
{
	$tmp = fn_strip_slashes($val);
	$_data = fn_get_contents($tmp);

	if (!empty($_data)) {
		$val = array();
		$val['name'] = basename($tmp);

		// Check if the file is dynamically generated
		if (strpos($val['name'], '&') !== false || strpos($val['name'], '?') !== false) {
			$val['name'] = 'url_uploaded_file_'.uniqid(TIME);
		}
		$val['path'] = fn_create_temp_file();
		$val['size'] = strlen($_data);

		$fd = fopen($val['path'], 'wb');
		fwrite($fd, $_data, $val['size']);
		fclose($fd);
		@chmod($val['path'], DEFAULT_FILE_PERMISSIONS);
		
		$cache = & Registry::get('temp_fs_data');

		if (!isset($cache[$val['path']])) { // cache file to allow multiple usage
			$cache[$val['path']] = $val['path'];
		}
	}
	return $val;
}

/**
 * Function get local uploaded
 *
 * @param unknown_type $val
 * @staticvar array $cache
 * @return unknown
 */
function fn_get_local_data($val)
{
	
	
	$cache = & Registry::get('temp_fs_data');
		
	if (!isset($cache[$val['path']])) { // cache file to allow multiple usage
		$tempfile = fn_create_temp_file();
	//	var_dump($tempfile);
		/*error_reporting(E_ALL);
		ini_set('display_errors', '1');
		*/
		
		
		
		if (move_uploaded_file($val['path'], $tempfile) == true) {
			
			@chmod($tempfile, DEFAULT_FILE_PERMISSIONS);
			$cache[$val['path']] = $tempfile;
		} else {
			$cache[$val['path']] = '';
		}

	}

	if (defined('KEEP_UPLOADED_FILES')) {
		$tempfile = fn_create_temp_file();
		
		fn_copy($cache[$val['path']], $tempfile);
		
		clearstatcache();
		$val['path'] = $tempfile;
	} else {
		
		$val['path'] = $cache[$val['path']];
	//	$val['path'] = $val['path'];
	} 
	
	return $val;
}

/**
 * Finds the last key in the array and applies the custom function to it.
 *
 * @param array $arr
 * @param string $fn
 * @param bool $is_first
 */
function fn_get_last_key(&$arr, $fn = '', $is_first = false)
{
	if (!is_array($arr)&&$is_first == true) {
		$arr = call_user_func($fn, $arr);
		return;
	}

	foreach ($arr as $k => $v) {
		if (is_array($v) && count($v)) {
			fn_get_last_key($arr[$k], $fn);
		}
		elseif (!is_array($v)&&!empty($v)) {
			$arr[$k] = call_user_func($fn, $arr[$k]);
		}
	}
}

/**
 * Filter data from file uploader
 *
 * @param string $name
 * @return array $filtered
 */
function fn_filter_uploaded_data($name)
{
	$udata_local = fn_rebuid_files('file_' . $name);
	$udata_other = !empty($_REQUEST['file_' . $name]) ? $_REQUEST['file_' . $name] : array();
	$utype = !empty($_REQUEST['type_' . $name]) ? $_REQUEST['type_' . $name] : array();
	//var_dump($udata_local);var_dump($udata_other);var_dump($utype);

	if (empty($utype)) {
		return array();
	}

	$filtered = array();
	//var_dump($udata_local);
	foreach ($utype as $id => $type) {
		
		if ($type == 'local' && !fn_is_empty(@$udata_local[$id])) {
			$filtered[$id] = fn_get_local_data(fn_strip_slashes($udata_local[$id]));
			

		} elseif ($type == 'server' && !fn_is_empty(@$udata_other[$id]) && AREA == 'A') {
			fn_get_last_key($udata_other[$id], 'fn_get_server_data', true);
			$filtered[$id] = $udata_other[$id];

		} elseif ($type == 'url' && !fn_is_empty(@$udata_other[$id])) {
			fn_get_last_key($udata_other[$id], 'fn_get_url_data', true);
			$filtered[$id] = $udata_other[$id];
		} 

		if (!empty($filtered[$id]['name'])) {
			$filtered[$id]['name'] = str_replace(' ', '_', urldecode($filtered[$id]['name'])); // replace spaces with underscores
			
			$ext = fn_get_file_ext($filtered[$id]['name']);
			if (in_array($ext, Registry::get('config.forbidden_file_extensions'))) {
				unset($filtered[$id]);
				$msg = fn_get_lang_var('text_forbidden_file_extension');
				$msg = str_replace('[ext]', $ext, $msg);
				fn_set_notification('E', fn_get_lang_var('error'), $msg);
			}
		}
	}

	static $shutdown_inited;

	if (!$shutdown_inited) {
		$shutdown_inited = true;
		register_shutdown_function('fn_remove_temp_data');
	}
	//var_dump($filtered);
	//die();
	return $filtered;
}

/**
 * Remove temporary files
 */
function fn_remove_temp_data()
{
	$fs_data = Registry::get('temp_fs_data');
	if (!empty($fs_data)) {
		foreach ($fs_data as $file) {
			fn_delete_file($file);
		}
	}
}

/**
 * Create temporary file
 *
 * @return temporary file
 */
function fn_create_temp_file()
{
	
	
	return tempnam(DIR_COMPILED, 'ztemp');
}

/**
 * Returns correct path from url "path" component
 *
 * @param string $path
 * @return correct path
 */
function fn_get_url_path($path)
{
	$dir = dirname($path);

	if ($dir == '.' || $dir == '/') {
		return '';
	}

	return (IIS == true) ? str_replace('\\', '/', $dir) : $dir;
}

/**
 * Check path to file 
 *
 * @param string $path
 * @return bool
 */
function fn_check_path($path)
{
	$real_path = realpath($path);
	return str_replace('\\', '/', $real_path) == $path ? true : false;
}

/**
 * Gets line from file pointer and parse for CSV fields 
 *
 * @param handle $f a valid file pointer to a file successfully opened by fopen(), popen(), or fsockopen().
 * @param int $length maximum line length
 * @param string $d field delimiter
 * @param string $q the field enclosure character
 * @return array structured data
 */
function fn_fgetcsv($f, $length, $d = ',', $q = '"') 
{
	$list = array();
	$st = fgets($f, $length);
	if ($st === false || $st === null) {
		return $st;
	}

	if (trim($st) === '') {
		return array('');
	}
	
	$st = rtrim($st, "\n\r");
	if (substr($st, -strlen($d)) == $d){
		$st .= '""';
	}
	
	while ($st !== '' && $st !== false) {
		if ($st[0] !== $q) {
			// Non-quoted.
			list ($field) = explode($d, $st, 2);
			$st = substr($st, strlen($field) + strlen($d));
		} else {
			// Quoted field.
			$st = substr($st, 1);
			$field = '';
			while (1) {
				// Find until finishing quote (EXCLUDING) or eol (including)
				preg_match("/^((?:[^$q]+|$q$q)*)/sx", $st, $p);
				$part = $p[1];
				$partlen = strlen($part);
				$st = substr($st, strlen($p[0]));
				$field .= str_replace($q . $q, $q, $part);
				if (strlen($st) && $st[0] === $q) {
					// Found finishing quote.
					list ($dummy) = explode($d, $st, 2);
					$st = substr($st, strlen($dummy) + strlen($d));
					break;
				} else {
					// No finishing quote - newline.
					$st = fgets($f, $length);
				}
			}
		}

		$list[] = $field;
	}

	return $list;
}

/**
 * Wrapper for rename with chmod
 *
 * @param string $oldname The old name. The wrapper used in oldname must match the wrapper used in newname. 
 * @param string $newname The new name.
 * @param resource $context Note: Context support was added with PHP 5.0.0. For a description of contexts, refer to Stream Functions.
 * 
 * @return boolean Returns TRUE on success or FALSE on failure.
 */
function fn_rename($oldname, $newname, $context = null)
{
	$result = ($context === null) ? rename($oldname, $newname) : rename($oldname, $newname, $context);
	if ($result !== false) {
		@chmod($newname, DEFAULT_FILE_PERMISSIONS);
	}
	return $result; 
}

/**
 * Create a new filename with postfix
 *
 * @param string $path
 * @param string $file
 * @return array ($full_path, $new_filename)
 */
function fn_generate_file_name($path, $file)
{
	if (!file_exists($path . $file)) {
		return array($path . $file, $file);
	}
	
	$files = fn_get_dir_contents($path, false, true);
	$num = 1;
	$found = false;
	$file_ext = fn_get_file_ext($file);
	$file_name = basename($path . $file, '.' . $file_ext);
	
	while (!$found) {
		$new_filename = $file_name . '_' . $num . '.' . $file_ext;
		if (!in_array($new_filename, $files)) {
			break;
		}
		
		$num++;
	}
	
	
	return array($path . $new_filename, $new_filename);
}
function dev_image_upload()
	{
		//var_dump($_FILES['type_darkish_file']);
		$allowed_extensions = array('jpg', 'png', 'gif');
		$some_uploaded = 0;
		
		foreach($_FILES['type_darkish_file']['tmp_name'] as $key=>$tmp_names)
			{
			
				switch($_FILES['type_darkish_file']['type'][$key])
				{
					case 'image/jpeg': $fileExtension = 'jpg'; break;
					case 'image/pjpeg': $fileExtension = 'jpg'; break;
					case 'image/png': $fileExtension = 'png'; break;
					case 'image/x-png': $fileExtension = 'png'; break;
					case 'image/gif': $fileExtension = 'gif'; break;
					default: 
						$wick_ext_file = $_FILES['type_darkish_file']['name'][$key];
						
				}
				
				if(!empty($wick_ext_file))
				{	
					fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('Запрещенный формат у файла: '.$wick_ext_file.''));
				}
				else
				{	
					
					$newFileName = $_FILES['type_darkish_file']['name'][$key];				
					$destination = $destination = ''.DIR_ROOT.'/images/uploaded/products/'.$newFileName.'';
					
					if (!move_uploaded_file($_FILES['type_darkish_file']['tmp_name'][$key], $destination)) 
					{
						
						fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('Не удалось загрузить: '.$newFileName.''));
					}
					else
					{
						
						$some_uploaded = 1;	
					}	
				}
								
			}
			if($some_uploaded == 1)
					{
						fn_set_notification('E', fn_get_lang_var('ok'), fn_get_lang_var('Файлы были загружены.'));
						
					}
			else
					{
						fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('Ни один файл не удалось загрузить'));
					}	
				
		
	}
function dev_csv_execute($delete_links = null)
	{
		
			
		$allowed_extensions = array('csv');
		$some_uploaded = 0;
		
				switch($_FILES['type_darkish_csv']['type'])
				{
					case 'text/csv': $fileExtension = 'csv'; break;
					case 'application/vnd.ms-excel' : $fileExtension = 'csv'; break;
					case 'application/octet-stream' : $fileExtension = 'csv'; break;
					default: 
						$wick_ext_file = $_FILES['type_darkish_csv']['name'];
						
				}
				if(!empty($wick_ext_file))
				{	
					fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('Запрещенный формат у файла: '.$wick_ext_file.''));
				}
				else
				{	
					
					$newFileName = $_FILES['type_darkish_csv']['name'];				
					$destination = ''.DIR_ROOT.'/images/uploaded/csv/'.$newFileName.'';
					
					if (!move_uploaded_file($_FILES['type_darkish_csv']['tmp_name'], $destination)) 
					{
								
						fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('Не удалось загрузить: '.$newFileName.''));
					}
					else
					{
						
						$some_uploaded = 1;	
						dev_link_images($destination, $delete_links);
					}	
				}
			
		}
function dev_alt_execute($article, $image)
			{
				
				$image_file = 	''.DIR_ROOT.'/images/uploaded/products/'.$image.'';
				
				if(!is_file($image_file))
				{
					//fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('Не был загружен файл: '.$image.''));
				}	
				else
				{
					//находим размеры нужного изображения
					$sizes = getimagesize(''.DIR_ROOT.'/images/uploaded/products/'.$image.'');
					$sizes_small = getimagesize(''.DIR_ROOT.'/images/uploaded/products/'.$small.'');
					
					
					//находим id продукта с артикулем из цсв файла
					$q = db_query("SELECT `product_id` FROM  `cscart_products` WHERE  `product_code` =  '".$article."'");
					$product_id = mysql_result($q,0);
					//делаем проверку, есть ли уже изображение у этого товара
					$qu = db_query("SELECT  `image_id` FROM  `cscart_images_links` WHERE  `object_id` = '".$product_id."'");
					$s = mysql_num_rows($qu);	
					if($s>0)
						{
						 $s_image_id = mysql_result($qu, 0);
						} 
					
						
					if(!empty($s_image_id))
					{
						
						//создаем запись в базе данных
						db_query("INSERT INTO `cscart_images` (`image_id`, `image_path`, `image_x`, `image_y`) VALUES (NULL, '".$image."', '".$sizes[0]."', '".$sizes[1]."')");
						$image_id = mysql_insert_id(); //получаем айди записи 
						
						db_query("INSERT INTO `cscart_images` (`image_id`, `image_path`, `image_x`, `image_y`) VALUES (NULL, '".$small."', '".$sizes_small[0]."', '".$sizes_small[1]."')");
						$image_small_id = mysql_insert_id(); //получаем айди записи 
						
						//создаем запись в таблице images_links тем самым привязываем фото с продуктом,  помечаем изображение как альтернативное
						db_query("INSERT INTO `cscart_images_links` (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`) VALUES (NULL, '".$product_id."', 'product', '".$image_id."', '".$image_id."', 'A')");
						$folder = floor($image_id / 1000);
					
					}	
					else
					{
						
						db_query("INSERT INTO `cscart_images` (`image_id`, `image_path`, `image_x`, `image_y`) VALUES (NULL, '".$image."', '".$sizes[0]."', '".$sizes[1]."')");
						$image_id = mysql_insert_id(); //получаем айди записи 
						
						db_query("INSERT INTO `cscart_images` (`image_id`, `image_path`, `image_x`, `image_y`) VALUES (NULL, '".$small."', '".$sizes_small[0]."', '".$sizes_small[1]."')");
						$image_small_id = mysql_insert_id(); //получаем айди записи 
						
						//создаем запись в таблице images_links тем самым привязываем фото с продуктом,  помечаем изображение как альтернативное
						
						db_query("INSERT INTO `cscart_images_links` (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`) VALUES (NULL, '".$product_id."', 'product', '".$image_id."', '".$image_id."', 'A')");
						$folder = floor($image_id / 1000);
					}
				
					$dest = ''.DIR_ROOT.'/images/product/'.$folder.'/'.$image.'';
					$detailed = ''.DIR_ROOT.'/images/detailed/'.$folder.'/'.$image.'';
					$dest_f = ''.DIR_ROOT.'/images/product/'.$folder.'/';
					$detailed_f = ''.DIR_ROOT.'/images/detailed/'.$folder.'/';
					//если нет такой дериктории - создаем её
					if(!is_dir($dest_f))
					{
					 mkdir($dest_f, 0777);
					 mkdir($detailed_f, 0777);
					}				
					
				
					if(copy($image_file, $dest))
					{
						
						$some_uploaded = 1;	
						seal_watermark($dest, $detailed);
						//unlink($image_file);
					}
				}	
				
				
				
				
			}	
		
function dev_link_images($file, $delete_links = null){
		
/** 
 * Fucking parser
 * it sucks
 * return dick
 */ 		
	$handle = fopen($file, 'r');
	$i = 0; 
	$some_uploaded = 0;	
	$products = array();
	set_time_limit(2000000000);
	ini_set('memory_limit', '12582912222222222222222'); 
	while (($row = fgetcsv($handle, 1000, ';')) !== false){ 
	if ($i > 0){
	/* пропускаем первую строку с названиями колонок */
		$article = $row[0];
		$image = $row[3];
		$small = $row[1];
				
		//формируем массив для альтернативных изображений			
		for($j=3; $j<8; $j++)
			$alt[$j-1] = $row[$j];
			
		if($delete_links != null){
		//находим id продукта с артикулем из цсв файла
			$q = db_query("SELECT `product_id` FROM  `cscart_products` WHERE  `product_code` =  '".$article."'");
			$product_id = mysql_result($q,0);

			if(!empty($product_id)){
			//удаляем связи
				db_query("DELETE FROM `cscart_images_links` WHERE `object_id` = ".$product_id." AND  `type` = 'A' ");
				$s_image_id = null; //соответсвено записей больше нету.
				$deleted = 1;
			}
		}
				
		$image_file = 	''.DIR_ROOT.'/images/uploaded/products/'.$image.'';				
		if(!file_exists($image_file)){				
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('Не был загружен файл: '.$image.''));
				$ers = 1;
		}	
		else{
			//находим размеры нужного изображения
			$sizes = getimagesize(''.DIR_ROOT.'/images/uploaded/products/'.$image.'');
			$sizes_small = getimagesize(''.DIR_ROOT.'/images/uploaded/products/'.$small.'');
			
			//находим id продукта с артикулем из цсв файла											
			$q = db_query("SELECT * FROM  cscart_products WHERE  `product_code` =   '$article' ");									
			$product_id = mysql_result($q, 0);
						
			//делаем проверку, есть ли уже изображение у этого товара
			$qu = db_query("SELECT  `detailed_id` FROM  `cscart_images_links` WHERE  `object_id` = '".$product_id."' AND `type` = 'M'");
			$s = mysql_num_rows($qu);	
			if($s>0)
				$s_image_id = mysql_result($qu, 0);
									
			$pair_id_query = db_query("SELECT  `pair_id` FROM  `cscart_images_links` WHERE  `object_id` = '".(int)$product_id."' AND `type` = 'M'");
			$pair_id_rows = mysql_num_rows($pair_id_query);	
						
			if($pair_id_rows>0){
				$s_pair_id = mysql_result($pair_id_query, 0);
			} 	
							
			if($pair_id_rows>1){
				for($counter=1;$counter<$pair_id_rows;$counter++){	
					$to_del_pair_id = mysql_result($pair_id_query, $counter);				
						db_query("DELETE FROM `cscart_images_links` WHERE `pair_id` = ".$to_del_pair_id."");	
					}	
			}		
							
				/*		if($delete_links != null)
						{
							var_dump('vreadly');
							//удаляем связи
							db_query("DELETE FROM `cscart_images_links` WHERE `object_id` = ".$product_id."");
							$s_image_id = null; //соответсвено записей больше нету.
						}		
					*/		
			if(!empty($s_image_id)){
			//находим название старого файла
				$oldim = db_query("SELECT  `image_path` FROM  `cscart_images` WHERE  `image_id` = '".$s_image_id."'");
				$oldimg = mysql_result($oldim, 0);
								
								
				//обновляем запись
				db_query("UPDATE `cscart_images` SET  `image_path` =  '".$image."', `image_x` =  '".$sizes[0]."',
						 `image_y` =  '".$sizes[1]."'	WHERE  `cscart_images`.`image_id` = '".$s_image_id."'");
														
				//ОБНОВЛЯЕМ ОБЕ ПОЗИЦИИ
				db_query("UPDATE  `cscart_images_links` SET  `image_id` =  '".$s_image_id."', `TYPE` = 'M', `detailed_id` = '".$s_image_id."' WHERE  `pair_id` = '".$s_pair_id."' LIMIT 1 ;");
								
				//формируем полный путь к файлу который надо удалить
				$folder = floor($s_image_id / 1000);
				$dest_to_del = ''.DIR_ROOT.'/images/product/'.$folder.'/'.$oldimg.'';
				$detailed_to_del = ''.DIR_ROOT.'/images/detailed/'.$folder.'/'.$oldimg.'';
				//удаляем
				//unlink($dest_to_del);
				//unlink($detailed_to_del);
			}else{		
				db_query("INSERT INTO `cscart_images` (`image_id`, `image_path`, `image_x`, `image_y`) VALUES (NULL, '".$image."', '".$sizes[0]."', '".$sizes[1]."')");
				$image_id = mysql_insert_id(); //получаем айди записи 
							
				db_query("INSERT INTO `cscart_images` (`image_id`, `image_path`, `image_x`, `image_y`) VALUES (NULL, '".$small."', '".$sizes_small[0]."', '".$sizes_small[1]."')");
				$image_small_id = mysql_insert_id(); //получаем айди записи 
							
				//создаем запись в таблице images_links тем самым привязываем фото с продуктом,  помечаем изображение как альтернативное
				db_query("INSERT INTO `cscart_images_links` (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`) VALUES (NULL, '".$product_id."', 'product', '".$image_id."', '".$image_id."', 'M')");
				$folder = floor($image_id / 1000);
			}
					
			$dest = ''.DIR_ROOT.'/images/product/'.$folder.'/'.$image.'';
			$detailed = ''.DIR_ROOT.'/images/detailed/'.$folder.'/'.$image.'';
			$dest_f = ''.DIR_ROOT.'/images/product/'.$folder.'/';
			$detailed_f = ''.DIR_ROOT.'/images/detailed/'.$folder.'/';
			//если нет такой дериктории - создаем её
			if(!is_dir($dest_f)){
				mkdir($dest_f, 0777);
				mkdir($detailed_f, 0777);
			}				
			
			if(copy($image_file, $dest)){
				$some_uploaded = 1;	
				seal_watermark($dest, $detailed);
						//	unlink($image_file);
			}
		}
		foreach($alt as $al){
			if(!empty($al))			
				dev_alt_execute($article, $al);		
								 	
		}
			
	}
		$i++;
	
	}
	if($some_uploaded == 1)
		fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('Изображения были присвоены к товару!'));
		
	if($ers == 1)
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('не все изображения были загружены') . $image_file);
		
	if($deleted == 1)
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('Связи были удаленны!'));		
		
}
function seal_watermark($image, $detailed){
	$logo_file = ''.DIR_ROOT.'/li.png'; 
	$image_file = $image; 
	$targetfile = $detailed; 
	
	$photo = imagecreatefromjpeg($image_file); 
	$fotoW = imagesx($photo); 
	$fotoH = imagesy($photo); 
	
	$logoImage = imagecreatefrompng($logo_file); 
	$logoW = imagesx($logoImage); 
	$logoH = imagesy($logoImage); 	
	$logoH = $logoH - 50;
	$photoFrame = imagecreatetruecolor($fotoW,$fotoH); 
	$dest_x = $fotoW - $logoW; 
	$dest_y = $fotoH - $logoH; 
	
	imagecopyresampled($photoFrame, $photo, 0, 0, 0, 0, $fotoW, $fotoH, $fotoW, $fotoH); 
	imagecopy($photoFrame, $logoImage, $dest_x, $dest_y, 0, 0, $logoW, $logoH); 
	imagejpeg($photoFrame, $targetfile);  

}	

?>
