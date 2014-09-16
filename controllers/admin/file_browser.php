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
// $Id: file_browser.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

$text_files_ext = array('csv', 'tpl', 'html', 'txt', 'css');
$image_files_ext = array('bmp', 'gif', 'jpg', 'jpeg', 'pcx', 'png', 'tif', 'tiff');

if ($mode == 'browse') {
	if ($_REQUEST['view_type'] != 'thumbs') {
		$dir =  $_REQUEST['dir'];

		$inner_opened_dir = !empty($_SESSION['file_browser_dir']) && empty($dir) ? $_SESSION['file_browser_dir'] : '';

		$dir = str_replace('../', '', $dir); // prevent illegal directory traversing
		$dir = urldecode($dir);
		if (!empty($dir)) {
			if ($_REQUEST['do_update'] == 'remove') {
				$dirs = explode('/', $dir);
				array_splice($dirs, count($dirs)-2, 1);
				$_SESSION['file_browser_dir'] = implode('/', $dirs);
			} else {
				$_SESSION['file_browser_dir'] = $dir;
			}
		}
		if ($_REQUEST['do_update'] == 'update') {
			$view->assign('file_list', fn_get_dir($dir, $inner_opened_dir, $_REQUEST['view_type']));
			$view->assign('current_dir', htmlentities($dir));
			$ajax->assign('file_list', $view->display('common_templates/file_browser_dirs.tpl', false));
		}
	} else {
		$view->assign('file_list', fn_get_dir(empty($_SESSION['file_browser_dir']) ? '' : $_SESSION['file_browser_dir'], '', $_REQUEST['view_type']));
		$view->assign('current_dir', htmlentities($_SESSION['file_browser_dir']));
		if ($_REQUEST['view_mode'] == 'list_view') {
			$ajax->assign('file_list', $view->display('common_templates/file_browser_dirs.tpl', false));
		} else if ($_REQUEST['view_mode'] == 'thumbs_view') {
			$ajax->assign('file_list', $view->display('common_templates/file_browser_thumbnails.tpl', false));
		}
	}

} elseif ($mode == 'get_content') {
	$file = $_REQUEST['file'];
	$ext = strtolower(fn_get_file_ext($file));
	
	if (array_search($ext, $text_files_ext) !== false) {
		$ajax->assign('content', 'text:' . fn_get_contents($file, DIR_ROOT . '/'));
	} elseif (array_search($ext, $image_files_ext) !== false) {
		$ajax->assign('content', 'image:' . $file);
	} else {
		$ajax->assign('content', '');
	}

} elseif ($mode == 'standalone') {
	$view->display('common_templates/file_browser_standalone.tpl');

} elseif ($mode == 'file_upload') {
	$uploaded_data = fn_filter_uploaded_data('upload_file');
	$error = "";
	if (!empty($uploaded_data)) {
		foreach ($uploaded_data as $k => $v) {
			if (!empty($v['error'])) {
				$error = fn_get_lang_var('error_exim_no_file_uploaded');
			} else {
				$cur_dir = empty($_SESSION['file_browser_dir']) ? './' : $_SESSION['file_browser_dir'];
				$target_dir = $cur_dir . $v['name'];
				fn_copy($v['path'], $target_dir);
				$ajax->assign('refresh', true);
				fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('file_uploaded'));
			}
		}
	} else {
		$error = fn_get_lang_var('error_exim_no_file_uploaded');
	}
	if ($error)	{
		$ajax->assign('refresh', false);
		fn_set_notification('E', fn_get_lang_var('error'), $error);
	}
	$ajax->content_type = 'text/html';
}

exit;

function fn_get_dir($dir, $inner_opened_dir, $view_type = 'all')
{
	$root = './';
	$file_list = array();
	$next_dir = '';
	
	if (!empty($inner_opened_dir)) {
		$dirs = explode('/', $inner_opened_dir);
		$next_dir = array_shift($dirs);
		$inner_opened_dir = implode('/', $dirs);
	}

	if (file_exists($root . $dir)) {
		$files = scandir($root . $dir);
		natcasesort($files);
		
		if (count($files) > 2) { /* The 2 accounts for . and .. */
			// All dirs
			if ($view_type != 'thumbs') {
				foreach ($files as $file) {
					if ($file != '.' && $file != '..' && file_exists($root . $dir . $file) && is_dir($root . $dir . $file)) {
						if ($file == $next_dir) {
							array_push($file_list, array('file' => $file, 'next' => fn_get_dir($dir . (empty($dir) ? '' : '/') . $next_dir . '/', $inner_opened_dir, $view_type)));
						} else {
							array_push($file_list, array('file' => $file));
						}
					}
				}
			}
			// All files
			if ($view_type != 'dirs') {
				foreach ($files as $file) {
					if ($file != '.' && $file != '..' && file_exists($root . $dir . $file) && !is_dir($root . $dir . $file)) {
						$ext = pathinfo($file, PATHINFO_EXTENSION);
						array_push($file_list, array('file' => $file, 'ext' => ($ext ? $ext : 'file')));
					}
				}
			}
			return $file_list;
		}
	}
	
	return array();
}
?>