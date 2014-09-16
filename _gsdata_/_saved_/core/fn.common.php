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
// $Id: fn.common.php 10590 2010-09-03 08:37:58Z alexions $
//

if ( !defined('AREA') )	{ die('Access denied');	}

//
//  print_r wrapper
//
function fn_print_r()
{
	static $count = 0;
	$args = func_get_args();

	if (!empty($args)) {
		echo '<ol style="font-family: Courier; font-size: 12px; border: 1px solid #dedede; background-color: #efefef; float: left; padding-right: 20px;">';
		foreach ($args as $k => $v) {
			$v = htmlspecialchars(print_r($v, true));
			if ($v == '') {
				$v = '    ';
		}

			echo '<li><pre>' . $v . "\n" . '</pre></li>';
		}
		echo '</ol><div style="clear:left;"></div>';
	}
	$count++;
}

/**
* Redirect browser to the new location
*
* @param string location - destination of redirect
* @param bool no_delay - do not delay redirection if output was performed
* @param bool allow_external_redirect - allow redirection to external resource
* @return
*/
function fn_redirect($location, $no_delay = false, $allow_external_redirect = false)
{
	$external_redirect = false;
	$protocol = defined('HTTPS') ? 'https' : 'http';

	// Cleanup location from &amp; signs and call fn_url()
	$location = str_replace('&amp;', '&', fn_url(str_replace(array("\n", "\r"), array('', ''), $location)));

	// Convert absolute link with location to relative one
	if (strpos($location, '://') !== false || substr($location, 0, 7) == 'mailto:') {
		if (strpos($location, Registry::get('config.http_location')) !== false) {
			$location = str_replace(array(Registry::get('config.http_location') . '/', Registry::get('config.http_location')), '', $location);
			$protocol = 'http';

		} elseif (strpos($location, Registry::get('config.https_location')) !== false) {
			$location = str_replace(array(Registry::get('config.https_location') . '/', Registry::get('config.https_location')), '', $location);
			$protocol = 'https';

		} else {
			if ($allow_external_redirect == false) { // if external redirects aren't allowed, redirect to index script
				$location = INDEX_SCRIPT;
			} else {
				$external_redirect = true;
			}
		}

	// Convert absolute link without location to relative one
	} else {
		$_protocol = "";
		$http_path = Registry::get('config.http_path');
		$https_path = Registry::get('config.https_path');
		if (!empty($http_path) && substr($location, 0, strlen($http_path)) == $http_path) {
			$location = substr($location, strlen($http_path) + 1);
			$_protocol = 'http';

		} elseif (!empty($https_path) && substr($location, 0, strlen($https_path)) == $https_path) {
			$location = substr($location, strlen($https_path) + 1);
			$_protocol = 'https';
		}
		$protocol = (Registry::get('config.http_path') != Registry::get('config.https_path') && !empty($_protocol))? $_protocol : $protocol;
	}

	if ($external_redirect == false) {

		fn_set_hook('redirect', $location);

		$protocol_changed = (defined('HTTPS') && $protocol == 'http') || (!defined('HTTPS') && $protocol == 'https');

		// For correct redirection, location must be absolute with path
		$location = (($protocol == 'http') ? Registry::get('config.http_location') : Registry::get('config.https_location')) . '/' . ltrim($location, '/');

		// Parse the query string
		$query_array = array();
		if (strpos($location, '?') !== false) {
			$qs = substr($location, strpos($location, '?') + 1);
			$location = str_replace('?' . $qs, '', $location);
			parse_str($qs, $query_array);
		}

		if ($protocol_changed && (Registry::get('config.http_host') != Registry::get('config.https_host') || Registry::get('config.http_path') != Registry::get('config.https_path'))) {
			$query_array[SESS_NAME] = Session::get_id();
		}

		// If this is not ajax request, remove ajax specific parameters
		if (!defined('AJAX_REQUEST')) {
			unset($query_array['is_ajax']);
			unset($query_array['result_ids']);
		} else {
			$query_array['result_ids'] = implode(',', Registry::get('ajax')->result_ids);
			$query_array['is_ajax'] = Registry::get('ajax')->request_type;

			$ajax_assigned_vars = Registry::get('ajax')->get_assigned_vars();
			if (!empty($ajax_assigned_vars['html'])) {
				unset($ajax_assigned_vars['html']);
			}
			$query_array['_ajax_data'] = $ajax_assigned_vars;

			fn_define('AJAX_REDIRECT', true);
		}

		if (!empty($query_array)) {
			$location .= '?' . fn_build_query($query_array);
		}

		// Redirect from https to http location
		if ($protocol_changed && defined('HTTPS')) {
			$no_delay = true;

			fn_define('META_REDIRECT', true);
		}
	}

	if (!ob_get_contents() && !headers_sent() && !defined('META_REDIRECT')) {
		header('Location: ' . $location);
		exit;
	} else {
		if (defined('AJAX_REQUEST')) {
			die("AJAX REDIRECT AFTER OUTPUT");
		}
		$delay = ($no_delay == true) ? 0 : 10;
		if ($no_delay == false) {
			fn_echo('<br /><div style="margin-top: 20px; border: 1px solid #dadada; background-color: #fcffd8; padding: 15px; float: left;">');
			fn_echo(fn_get_lang_var('text_redirect_notice') . '&nbsp;');
			fn_echo('<a href="' . htmlspecialchars($location) . '" style="text-transform: lowercase;">' . fn_get_lang_var('continue') . '</a>');
			fn_echo('</div>');

		}

		fn_echo("<meta http-equiv=\"Refresh\" content=\"$delay;URL=" . htmlspecialchars($location) . "\" />");
	}

	fn_flush();
	exit;
}

/**
 * Set notification message
 *
 * @param string $type notification type (E - error, W - warning, N - notice, O - order error on checkout)
 * @param string $title notification title
 * @param string $message notification message
 * @param bool $save_state if true, notification will be displayed unless it's closed, if false - only once
 * @param mixed $extra extra data to save with notification
 * @return boolean always true
 */
function fn_set_notification($type, $title, $message, $save_state = false, $extra = '')
{
	if (empty($_SESSION['notifications'])) {
		$_SESSION['notifications'] = array();
	}

	$key = md5(uniqid());

	$_SESSION['notifications'][$key] = array(
		'type' => $type,
		'title' => $title,
		'message' => $message,
		'save_state' => $save_state,
		'new' => true,
		'extra' => $extra
	);

	return true;
}

/**
 * Set notification message
 *
 * @param string $extra condition for "extra" parameter
 * @return boolean always true
 */
function fn_delete_notification($extra)
{
	if (!empty($_SESSION['notifications'])) {
		foreach ($_SESSION['notifications'] as $k => $v) {
			if (!empty($v['extra']) && $v['extra'] == $extra) {
				unset($_SESSION['notifications'][$k]);
			}
		}
	}

	return true;
}

/**
 * Check for existing notification message
 *
 * @param string $type notification type - A - any, E - extra (in this case second "value" parameter is required)
 * @param string $value value of the "extra" parameter
 * @return boolean always true
 */
function fn_notification_exists($type, $value)
{
	if (!empty($_SESSION['notifications'])) {
		if ($type == 'A') {
			return true;
		}

		foreach ($_SESSION['notifications'] as $k => $v) {
			if (!empty($v['extra']) && $v['extra'] == $value) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Get notifications list
 *
 * @return array notifications list
 */
function fn_get_notifications()
{
	if (empty($_SESSION['notifications'])) {
		$_SESSION['notifications'] = array();
	}

	$_notifications = array();

	foreach ($_SESSION['notifications'] as $k => $v) {
		// Display notification if this is not ajax request, or ajax request and notifiactions was just set
		if (!defined('AJAX_REQUEST') || (defined('AJAX_REQUEST') && $v['new'] == true)) {
			$_notifications[$k] = $v;
		}

		if ($v['save_state'] == false) {
			unset($_SESSION['notifications'][$k]);
		} else {
			$_SESSION['notifications'][$k]['new'] = false; // preparing notification for display, reset new flag
		}
	}

	return $_notifications;
}

//
// Set all post data, excluding dispatch
//
function fn_save_post_data()
{
	unset($_POST['dispatch']);
	$_SESSION['saved_post_data'] = fn_strip_slashes($_POST);

	return true;
}

//
// Get language variable by its name
//
function fn_get_lang_var($var_name, $lang_code = CART_LANGUAGE)
{
	$lang_cache = & Registry::get('lang_cache');

	if (!is_array($lang_cache)) {
		$lang_cache = array();
	}

	if (!isset($lang_cache[$lang_code][$var_name])) {
		$lang_cache[$lang_code][$var_name] = db_get_field("SELECT value FROM ?:language_values WHERE lang_code = ?s AND name = ?s", $lang_code, $var_name);
	}

	if (is_null($lang_cache[$lang_code][$var_name])) {
		return '_' . $var_name;
	}

	if (Registry::get('settings.translation_mode') == 'Y') {
		return '[lang name=' . $var_name . (preg_match('/\[[\w]+\]/', $lang_cache[$lang_code][$var_name]) ? ' cm-pre-ajax' : '') . ']' . $lang_cache[$lang_code][$var_name] . '[/lang]';
	} else {
		return $lang_cache[$lang_code][$var_name];
	}
}
 
//
// Get language variables by prefix
//
function fn_get_lang_vars_by_prefix($prefix, $lang_code = CART_LANGUAGE)
{
	$lang_cache = & Registry::get('lang_cache');

	if (!is_array($lang_cache)) {
		$lang_cache = array();
	}

	$lang_vars = array();

	$result = db_get_hash_array("SELECT name, value FROM ?:language_values WHERE lang_code = ?s AND name LIKE ?l", 'name', $lang_code, $prefix . '%');
	
	if (!empty($result)) {
		foreach ($result as $var_name => $value_info) {
			$lang_cache[$lang_code][$var_name] = $value_info['value'];
			
			if (Registry::get('settings.translation_mode') == 'Y') {
				$lang_vars[$var_name] = '[lang name=' . $var_name . (preg_match('/\[[\w]+\]/', $lang_cache[$lang_code][$var_name]) ? ' cm-pre-ajax' : '') . ']' . $lang_cache[$lang_code][$var_name] . '[/lang]';
			} else {
				$lang_vars[$var_name] = $lang_cache[$lang_code][$var_name];
			}
		}
	}

	return $lang_vars;
}

function fn_preload_lang_vars($var_names, $lang_code = CART_LANGUAGE)
{
	$lang_cache = & Registry::get('lang_cache');

	if (!is_array($lang_cache)) {
		$lang_cache = array();
	}

	if (empty($lang_cache[$lang_code])) {
		$lang_cache[$lang_code] = array();
	}

	$var_names = array_diff($var_names, array_keys($lang_cache[$lang_code]));

	if (!empty($var_names)) {
		$lang_cache[$lang_code] = fn_array_merge($lang_cache[$lang_code], db_get_hash_single_array("SELECT name, value FROM ?:language_values WHERE lang_code = ?s AND name IN (?a)", array('name', 'value'), $lang_code, $var_names));

		return true;
	}

	return false;
}

function fn_update_lang_objects($tpl_var, &$value)
{
	static $translation_mode, $init = false;
	if (!$init) {
		$translation_mode = Registry::get('settings.translation_mode');
		$init = true;
	}
	
	if ($translation_mode == 'Y') {
		static $schema;
		if ($translation_mode == 'Y') {
			if (empty($schema)) {
				$schema = fn_get_schema('translate', 'schema');
			}

			if (!empty($schema[CONTROLLER][MODE])) {
				foreach ($schema[CONTROLLER][MODE] as $var_name => $var) {
					if ($tpl_var == $var_name) {
						fn_prepare_lang_objects($value, $var['dimension'], $var['fields'], $var['table_name'], $var['where_fields'], (isset($var['inner']) ? $var['inner'] : ''));
					}
				}
			}
			foreach ($schema['any']['any'] as $var_name => $var) {
				if ($tpl_var == $var_name) {
					fn_prepare_lang_objects($value, $var['dimension'], $var['fields'], $var['table_name'], $var['where_fields'], (isset($var['inner']) ? $var['inner'] : ''));
				}
			}
		}
	}
}

function fn_prepare_lang_objects(&$destination, $dimension, $fields, $table, $field_id, $inner = '')
{
	if ($dimension > 0) {
		foreach ($destination as $i => $v) {
			fn_prepare_lang_objects($destination[$i], $dimension - 1, $fields, $table, $field_id, $inner);
		}
	} else {
		foreach ($fields as $i => $v) {
			if (isset($destination[$v])) {
				$where_fields = '';
				foreach ($field_id as $to_name => $orig_name) {
					if (is_array($orig_name)) {
						foreach ($orig_name as $val) {
							if (!empty($destination[$val])) {
								$where_fields .= '-' . $to_name . '-' . $destination[$val];
							}
						}
					} else {
						$where_fields .= '-' . $to_name . '-' . $destination[$orig_name];
					}
				}
				$what = is_string($i) ? $i : $v;
				$destination[$v] = "[lang name=$table-$what$where_fields]$destination[$v][/lang]";
				if (!empty($inner) && isset($destination[$inner[0]])) {
					fn_prepare_lang_objects($destination[$inner[0]], $inner[1], $fields, $table, $field_id);
				}
			}
		}
	}
}

//
// Get setting language variable by its id
//
function fn_get_setting_description($object_id, $object_type = 'S', $lang_code = CART_LANGUAGE)
{
	return db_get_field("SELECT description FROM ?:settings_descriptions WHERE lang_code = ?s AND object_id = ?s AND object_type = ?s", $lang_code, $object_id, $object_type);
}

//
// Define and assign pages
//
function fn_paginate($page = 1, $total_items = 10, $items_per_page = 10, $get_limit = false)
{

	// Avoid meaningless string and zero values 
	$items_per_page = intval($items_per_page);
	if (empty($items_per_page)) {
		$items_per_page = 10;
	}

	$deviation = 7;
	$max_pages = $per_page = 10;
	$original_ipp = $items_per_page;
	$navi_ranges = array();

	if (!empty($_REQUEST['items_per_page'])) {
		$_SESSION['items_per_page'] = $_REQUEST['items_per_page'] > 0 ? $_REQUEST['items_per_page'] : 1;
	}

	if (!empty($_SESSION['items_per_page'])) {
	   $items_per_page = $_SESSION['items_per_page'];
	}
	
	$items_per_page = empty($items_per_page) ? $per_page : (int)$items_per_page;
	$total_pages = ceil((int)$total_items / $items_per_page);

	$page = (int) $page;
	if ($page < 1) {
		$page = 1;	
	}
	
	if ($get_limit == false) {
		if ($total_items == 0 || $page == 'full_list') {
			return '';
		}

		if ($page > $total_pages) {
			$page = 1;
		}
			
		// Pagination in other areas displayed as in any search engine
		$page_from = ($page - $deviation < 1) ? 1 : $page - $deviation;
		$page_to = ($page + $deviation > $total_pages) ? $total_pages : $page + $deviation;

		$pagination = array (
			'navi_pages' => range($page_from, $page_to),
			'prev_range' => ($page_from > 1) ? $page_from - 1 : 0,
			'next_range' => ($page_to < $total_pages) ? $page_to + 1: 0,
			'current_page' => $page,
			'prev_page' => ($page > 1) ? $page - 1 : 0,
			'next_page' => ($page < $total_pages) ? $page + 1 : 0,
			'total_pages' => $total_pages,
			'total_items' => $total_items,
			'navi_ranges' => $navi_ranges,
			'items_per_page' => $items_per_page,
			'per_page_range' => range($per_page, $per_page * $max_pages, $per_page)
		);

		// If items per page is greater than maximum value, add it too
		if ($original_ipp > $per_page * $max_pages) {
			$pagination['per_page_range'][] = $original_ipp;
		}

		Registry::get('view')->assign('pagination', $pagination);
	}

	return 'LIMIT ' . (($page - 1) * $items_per_page) . ", $items_per_page";
}

//
// This function splits the array into defined number of columns to
// show it in the frontend
// Params:
// $data - the array that should be splitted
// $size - number of columns/rows to split into
// Example:
// array (a, b, c, d, e, f, g, h, i, j, k);
// fn_split($array, 3);
// Result:
// 0 -> a, b, c, d
// 1 -> e, f, g, h
// 2 -> i, j, k
// ---------------------
// fn_split($array, 3, true)
// Result:
//

function fn_split($data, $size, $vertical_delimition = false, $size_is_horizontal = true)
{

	if ($vertical_delimition == false) {
		return array_chunk($data, $size);
	} else {

		$chunk_count = ($size_is_horizontal == true) ? ceil(count($data) / $size) : $size;
		$chunk_index = 0;
		$chunks = array();
		foreach ($data as $key => $value) {
			$chunks[$chunk_index][] = $value;
			if (++$chunk_index == $chunk_count) {
				$chunk_index = 0;
			}
		}
		return $chunks;
	}
}

//
// Advanced checking for variable emptyness
//
function fn_is_empty($var)
{
	if (!is_array($var)) {
		return (empty($var));
	} else {
		foreach ($var as $k => $v) {
			if (empty($v)) {
				unset($var[$k]);
				continue;
			}

			if (is_array($v) && fn_is_empty($v)) {
				unset($var[$k]);
			}
		}
		return (empty($var)) ? true : false;
	}
}

function fn_is_not_empty($var)
{
	return !fn_is_empty($var);
}

//
// Format price
//

function fn_format_price($price = 0, $currency = CART_PRIMARY_CURRENCY, $decimals = 0)
{
	if (empty($decimals)) {
		$decimals = Registry::get('currencies.' . $currency . '.decimals') + 0;
	}
	$price = (float)sprintf('%.' . $decimals . 'f', round((double) $price + 0.00000000001, $decimals));
	
	return $price;
}


//
// Parse email template and attach images
//
function fn_attach_images($body, &$mailer)
{
	$http_location = Registry::get('config.http_location');
	$https_location = Registry::get('config.https_location');
	$http_path = Registry::get('config.http_path');
	$https_path = Registry::get('config.https_path');

	$files = array();
	if (preg_match_all("/(?<=\ssrc=|\sbackground=)('|\")(.*)\\1/SsUi", $body, $matches)) {
		$files = fn_array_merge($files, $matches[2], false);
	}
	if (preg_match_all("/(?<=\sstyle=)('|\").*url\(('|\"|\\\\\\1)(.*)\\2\).*\\1/SsUi", $body, $matches)) {
		$files = fn_array_merge($files, $matches[3], false);
	}
	if (empty($files)) {
		return $body;
	} else {
		$files = array_unique($files);
		foreach ($files as $k => $_path) {
			$cid = 'csimg'.$k;
			$path = str_replace('&amp;', '&', $_path);

			$real_path = '';
			// Replace url path with filesystem if this url is NOT dynamic
			if (strpos($path, '?') === false && strpos($path, '&') === false) {
				if (($i = strpos($path, $http_location)) !== false) {
					$real_path = substr_replace($path, DIR_ROOT, $i, strlen($http_location));
				} elseif (($i = strpos($path, $https_location)) !== false) {
					$real_path = substr_replace($path, DIR_ROOT, $i, strlen($https_location));
				} elseif (!empty($http_path) && ($i = strpos($path, $http_path)) !== false) {
					$real_path = substr_replace($path, DIR_ROOT, $i, strlen($http_path));
				} elseif (!empty($https_path) && ($i = strpos($path, $https_path)) !== false) {
					$real_path = substr_replace($path, DIR_ROOT, $i, strlen($https_path));
				}
			}

			if (empty($real_path)) {
				$real_path = (strpos($path, '://') === false) ? $http_location .'/'. $path : $path;
			}

			list($width, $height, $mime_type) = fn_get_image_size($real_path);

			if (!empty($width)) {
				$cid .= '.' . fn_get_image_extension($mime_type);
				$content = fn_get_contents($real_path);
				$mailer->AddImageStringAttachment($content, $cid, 'base64', $mime_type);
				$body = preg_replace("/(['\"])" . str_replace("/", "\/", preg_quote($_path)) . "(['\"])/Ss", "\\1cid:" . $cid . "\\2", $body);
			}
		}
	}

	return $body;
}

//
// Send email
//
function fn_send_mail($to, $from, $subj, $body, $attachments = array(), $lang_code = CART_LANGUAGE, $reply_to = '', $is_html = true)
{
	$__from = array();
	$__to = array();

	fn_init_mailer();
	$mailer = & Registry::get('mailer');
	$languages = Registry::get('languages');

	if (!is_array($from)) {
		$__from['email'] = $from;
		$__from['name'] = Registry::get('settings.Company.company_name');
	} else {
		$__from = $from;
	}

	Registry::get('view_mail')->setLanguage($lang_code);

	$mailer->ClearAttachments();
	$mailer->From = $__from['email'];
	$mailer->FromName = $__from['name'];
	if (!empty($reply_to)) {
		$mailer->ClearReplyTos();
		$mailer->AddReplyTo($reply_to);
	}

	$mailer->IsHTML($is_html);
	$mailer->CharSet = CHARSET;
	$mailer->Subject = Registry::get('view_mail')->display($subj, false);
	$mailer->Subject = trim($mailer->Subject);
	$body = Registry::get('view_mail')->display($body, false);
	$mailer->Body = fn_attach_images($body, $mailer);

	if (!empty($attachments)) {
		foreach ($attachments as $name => $file) {
			$mailer->AddAttachment($file, $name);
		}
	}

	if (!is_array($to)) {
		$__to = str_replace(';', ',', $to);
		$__to = explode(',', $__to);
	} else {
		$__to = $to;
	}

	foreach ($__to as $v) {
		$mailer->ClearAddresses();
		$mailer->AddAddress(trim($v), '');
		$result = $mailer->Send(); 
		if (!$result) {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('error_message_not_sent') . ' ' . $mailer->ErrorInfo);
		}

		fn_set_hook('send_mail', $mailer);
	}
	
	return $result;
}

/**
 * Add new node the breadcrumbs
 *
 * @param string $lang_value name of language variable
 * @param string $link breadcrumb URL
 * @return boolean always true
 */
function fn_add_breadcrumb($lang_value, $link = '')
{
	$bc = Registry::get('view')->get_var('breadcrumbs');

	if (!empty($link)) {
		fn_set_hook('add_breadcrumb', $lang_value, $link);
	}

	$bc[] = array(
		'title' => fn_html_escape($lang_value, true),  // unescape value to avoid double escaping
		'link' => $link
	);

	Registry::get('view')->assign('breadcrumbs', $bc);

	return true;
}

/**
 * Merge several arrays preserving keys (recursivelly!) or not preserving
 *
 * @param array ... unlimited number of arrays to merge
 * @param bool ... if true, the array keys are preserved
 * @return array merged data
 */
function fn_array_merge()
{
	$arg_list = func_get_args();
	$preserve_keys = true;
	$result = array();
	if (is_bool(end($arg_list))) {
		$preserve_keys = array_pop($arg_list);
	}

	foreach ((array)$arg_list as $arg) {
		foreach ((array)$arg as $k => $v) {
			if ($preserve_keys == true) {
				$result[$k] = !empty($result[$k]) && is_array($result[$k]) ? fn_array_merge($result[$k], $v) : $v;
			} else {
				$result[] = $v;
			}
		}
	}

	return $result;
}

//
// Restore original variable content (unstripped)
// Parameters should be the variables names
// E.g. fn_trusted_vars("product_data","big_text","etcetc")
function fn_trusted_vars()
{
	$args = func_get_args();
	if (sizeof($args) > 0) {
		foreach ($args as $k => $v) {
			if (isset($_POST[$v])) {
				$_REQUEST[$v] = (!defined('QUOTES_ENABLED')) ? $_POST[$v] : fn_strip_slashes($_POST[$v]);
			} elseif (isset($_GET[$v])) {
				$_REQUEST[$v] = (!defined('QUOTES_ENABLED')) ? $_GET[$v] : fn_strip_slashes($_GET[$v]);
			}
		}
	}

	return true;
}

// EnCrypt text wrapper function
function fn_encrypt_text($text)
{
	if (!defined('CRYPT_STARTED')) {
		fn_init_crypt();
	}

	return base64_encode(Registry::get('crypt')->encrypt($text));
}

// DeCrypt text wrapper function
function fn_decrypt_text($text)
{

	if (!defined('CRYPT_STARTED')) {
		fn_init_crypt();
	}

	return Registry::get('crypt')->decrypt(base64_decode($text));
}

//
// Get settings
//
function fn_get_settings($section_id = '', $subsection_id = '')
{
	$settings = array();

	$condition = (!empty($section_id)) ? db_quote(" AND section_id = ?s", $section_id) : " AND is_global = 'Y'";
	$condition .= (!empty($subsection_id)) ? db_quote(" AND subsection_id = ?s", $subsection_id) : '';

	if ($_result = db_get_array("SELECT option_name, value, section_id, subsection_id, option_type FROM ?:settings WHERE 1 $condition")) {
		foreach ($_result as $_row) {
			if (!empty($_row['subsection_id'])) {
				if ($_row['option_type'] == 'M' || $_row['option_type'] == 'N') {
					parse_str($_row['value'], $settings[$_row['section_id']][$_row['subsection_id']][$_row['option_name']]);
				} else {
					$settings[$_row['section_id']][$_row['subsection_id']][$_row['option_name']] = $_row['value'];
				}
			} elseif (!empty($_row['section_id'])) {
				if ($_row['option_type'] == 'M' || $_row['option_type'] == 'N') {
					parse_str($_row['value'], $settings[$_row['section_id']][$_row['option_name']]);
				} else {
					$settings[$_row['section_id']][$_row['option_name']] = $_row['value'];
				}
			} else {
				$settings[$_row['option_name']] = $_row['value'];
			}
		}

		if (empty($section_id)) {
			return $settings;

		} elseif (!empty($section_id) && empty($subsection_id)) {
			return $settings[$section_id];

		} elseif (!empty($subsection_id)) {
			return $settings[$section_id][$subsection_id];
		}
	}

	return false;
}

// Start javascript autoscroller
function fn_start_scroller()
{
	if (defined('CONSOLE')) {
		return true;
	}

	echo "
		<html>
		<head><title>" . PRODUCT_NAME . "</title>
		<meta http-equiv='content-type' content='text/html; charset=" . CHARSET . "'>
		</head>
		<body>
		<script language='javascript'>
		loaded = false;
		function refresh() {
			var scroll_height = parseInt(document.body.scrollHeight);
			window.scroll(0, scroll_height + 99999);
			if (loaded == false) {
				setTimeout('refresh()', 1000);
			}
		}
		setTimeout('refresh()', 1000);
		</script>
	";
	fn_flush();
}

// Stop javascript autoscroller
function fn_stop_scroller()
{
	if (defined('CONSOLE')) {
		return true;
	}

	echo "
	<script language='javascript'>
		loaded = true;
	</script>
	</body>
	</html>
	";
	fn_flush();
}

function fn_recursive_makehash($tab)
{
	if (!is_array($tab)) {
		return $tab;
	}

	$p = '';
	foreach ($tab as $a => $b) {
		$p .= sprintf('%08X%08X', crc32($a), crc32(fn_recursive_makehash($b)));
	}
	return $p;
}

//
// Smart wrapper for PHP array_unique function
//
function fn_array_unique($input)
{
	$dumdum = array();
	foreach ($input as $a => $b) {
		$dumdum[$a] = fn_recursive_makehash($b);
	}
	$newinput = array();
	foreach (array_unique($dumdum) as $a => $b) {
		$newinput[$a] = $input[$a];
	}

	return $newinput;
}

//
// Get section data from static_data table
//
function fn_get_static_data_section($section = 'C', $get_params = false, $icon_name = '', $lang_code = CART_LANGUAGE)
{
	$params = array(
		'section' => $section,
		'get_params' => $get_params,
		'icon_name' => $icon_name,
		'multi_level' => true,
		'use_localization' => true,
		'status' => 'A'
	);

	return fn_get_static_data($params, $lang_code);
}

function fn_get_static_data($params, $lang_code = DESCR_SL)
{
	$default_params = array (
		'section' => 'C',
	);

	$params = array_merge($default_params, $params);
	
	$fields = array(
		'?:static_data.param_id',
		'?:static_data.param',
		'?:static_data_descriptions.descr'
	);

	$condition = '';
	$sorting = "?:static_data.position";

	if (!empty($params['multi_level'])) {
		$sorting = "?:static_data.parent_id, ?:static_data.position, ?:static_data_descriptions.descr";
	}

	if (!empty($params['status'])) {
		$condition .= db_quote(" AND ?:static_data.status = ?s", $params['status']);
	}

	if (!empty($params['use_localization'])) {
		$condition .= fn_get_localizations_condition('?:static_data.localization');
	}

	if (!empty($params['get_params'])) {
		$fields[] = "?:static_data.param_2";
		$fields[] = "?:static_data.param_3";
		$fields[] = "?:static_data.param_4";
		$fields[] = "?:static_data.param_5";
		$fields[] = "?:static_data.status";
		$fields[] = "?:static_data.position";
		$fields[] = "?:static_data.parent_id";
		$fields[] = "?:static_data.id_path";
	}

	$s_data = db_get_hash_array("SELECT " . implode(', ', $fields) . " FROM ?:static_data LEFT JOIN ?:static_data_descriptions ON ?:static_data.param_id = ?:static_data_descriptions.param_id AND ?:static_data_descriptions.lang_code = ?s WHERE ?:static_data.section = ?s ?p ORDER BY ?:static_data.position", 'param_id', $lang_code, $params['section'], $condition);

	if (!empty($params['icon_name'])) {
		foreach ($s_data as $k => $v) {
			$s_data[$k]['icon'] = fn_get_image_pairs($v['param_id'], $params['icon_name'], 'M', true, true, $lang_code);
		}
	}

	if (!empty($params['generate_levels'])) {
		foreach ($s_data as $k => $v) {
			if (!empty($v['id_path'])) {
				$s_data[$k]['level'] = substr_count($v['id_path'], '/');
			}
		}
	}

	if (!empty($params['multi_level'])) {
		$delete_keys = array();

		foreach ($s_data as $k => $v) {
			if (!empty($v['parent_id'])) {
				if (!empty($s_data[$v['parent_id']])) {
					$s_data[$v['parent_id']]['subitems'][$v['param_id']] = $v;
					$s_data[$k] = & $s_data[$v['parent_id']]['subitems'][$v['param_id']];
				}
				$delete_keys[] = $k;
			}
		}

		foreach ($delete_keys as $k) {
			unset($s_data[$k]);
		}
	}

	if (!empty($params['plain'])) {
		$s_data = fn_multi_level_to_plain($s_data, 'subitems');
	}

	return $s_data;
}


/**
 * Convert multi-level array with "subitems" to plain representation
 *
 * @param array $data source array
 * @param string $key key with subitems
 * @param array $result resulting array, passed along multi levels
 * @return array structured data
 */
function fn_multi_level_to_plain($data, $key, $result = array())
{
	foreach ($data as $k => $v) {
		if (!empty($v[$key])) {
			unset($v[$key]);
			array_push($result, $v);
			$result = fn_multi_level_to_plain($data[$k][$key], $key, $result);
		} else {
			array_push($result, $v);
		}
	}

	return $result;
}

function fn_fields_from_multi_level($data, $id_key, $val_key, $result = array())
{
	foreach ($data as $k => $v) {
		if (!empty($v[$id_key]) && !empty($v[$val_key])) {
			$result[$v[$id_key]] = $v[$val_key];
		}
	}

	return $result;
}

//
// Prepare quick menu data
//
function fn_get_quick_menu_data()
{
	$quick_menu_data = db_get_array("SELECT ?:quick_menu.*, ?:common_descriptions.description AS name FROM ?:quick_menu LEFT JOIN ?:common_descriptions ON ?:common_descriptions.object_id = ?:quick_menu.menu_id  AND ?:common_descriptions.object_holder = 'quick_menu' AND ?:common_descriptions.lang_code = ?s WHERE ?:quick_menu.user_id = ?i ORDER BY ?:quick_menu.parent_id, ?:quick_menu.position", CART_LANGUAGE, $_SESSION['auth']['user_id']);
	if (!empty($quick_menu_data)) {
		$quick_menu_sections = array();
		foreach ($quick_menu_data as $section) {
			if ($section['parent_id']) {
				$url = (strpos($section['url'], '[admin_index]') !== 0) ? $section['url'] : substr_replace($section['url'], INDEX_SCRIPT, 0, 13);
				$quick_menu_sections[$section['parent_id']]['subsection'][] = array('menu_id' => $section['menu_id'], 'name' => $section['name'], 'url' => $url, 'position' => $section['position'], 'parent_id' => $section['parent_id']);
			} else {
				$quick_menu_sections[$section['menu_id']]['section'] = array('menu_id' => $section['menu_id'], 'name' => $section['name'], 'position' => $section['position']);
			}
		}
		return $quick_menu_sections;
	} else {
		return array();
	}
}

//
// Get descriptions for all option variants in settings subject
//
function fn_get_settings_variants($option_name, $section_id, $subsection_id)
{
	$option_id = db_get_field("SELECT option_id FROM ?:settings WHERE option_name = ?s AND section_id = ?s AND subsection_id = ?s", $option_name, $section_id, $subsection_id);

	return db_get_hash_array("SELECT ?:settings_variants.variant_name, ?:settings_descriptions.description FROM ?:settings_variants LEFT JOIN ?:settings_descriptions ON ?:settings_descriptions.object_id = ?:settings_variants.variant_id AND ?:settings_descriptions.lang_code = ?s AND ?:settings_descriptions.object_type = 'V' WHERE option_id = ?i", 'variant_name', CART_LANGUAGE, $option_id);
}


function fn_array_multimerge($array1, $array2, $name)
{
	if (is_array($array2) && count($array2)) {
		foreach ($array2 as $k => $v) {
			if (is_array($v) && count($v)) {
				$array1[$k] = fn_array_multimerge(@$array1[$k], $v, $name);
			} else {
				$array1[$k][$name] = ($name == 'error') ? 0 : $v;
			}
		}
	} else {
		$array1 = $array2;
	}

	return $array1;
}


// Display database error message and/or backtrace
function fn_error($debug_data, $error = '', $is_db = true)
{
	$auth = & $_SESSION['auth'];

	$debug_data = array_reverse($debug_data, true);
	if (file_exists(DIR_ROOT . '/bug_report.php')) {
		$bug_report = true;
	}

	if (!empty($bug_report)) {
		ob_start();
	}

	if (!empty($error) && $is_db == true) {

		// Log database errors
		fn_log_event('database', 'error', array(
			'error' => $error,
			'backtrace' => $debug_data
		));

		echo <<< EOT
<p><b><span style='font-weight: bold; color: #000000; font-size: 13px; font-family: Courier;'>Database error:</span></b>&nbsp;$error[message]<br>
<b><span style='font-weight: bold; color: #000000; font-size: 13px; font-family: Courier;'>Invalid query:</span></b>&nbsp;$error[query]</p>
EOT;
	} elseif (!empty($error)) {
	echo <<< EOT
<p><b><span style='font-weight: bold; color: #000000; font-size: 13px; font-family: Courier;'>Error:</span></b>&nbsp;$error<br>
EOT;
	}

	echo <<< EOU
<hr noshade width='100%'>
<p><span style='font-weight: bold; color: #000000; font-size: 13px; font-family: Courier;'>Backtrace:</span>
<table cellspacing='1'>
EOU;
		$i = 0;
		if (!empty($debug_data)) {
			$func = '';
			foreach (array_reverse($debug_data) as $v) {
				if (empty($v['file'])) {
					$func = $v['function'];
					continue;
				} elseif (!empty($func)) {
					$v['function'] = $func;
					$func = '';
				}
				$i = ($i == 0) ? 1 : 0;
				$color = ($i == 0) ? "#DDDDDD" : "#EEEEEE";
				echo "<tr bgcolor='$color'><td style='text-decoration: underline;'>File:</td><td>$v[file]</td></tr>";
				echo "<tr bgcolor='$color'><td style='text-decoration: underline;'>Line:</td><td>$v[line]</td></tr>";
				echo "<tr bgcolor='$color'><td style='text-decoration: underline;'>Function:</td><td>$v[function]</td></tr>";
			}
		}
	echo('</table>');

	if (!empty($bug_report)) {
		$debug = ob_get_clean();
		include(DIR_ROOT . '/bug_report.php');
	}


	exit;
}

/**
* Validate email address
*
* @param string $email email
* @return boolean - is email correct?
*/
function fn_validate_email($email, $show_error = false) {

	$email_regular_expression = "^([\d\w-+=_][.\d\w-+=_]*)?[-\d\w]@([-!#\$%&*+\\/=?\w\d^_`{|}~]+\.)+[a-zA-Z]{2,6}$";

	if (preg_match("/" . $email_regular_expression . "/i", stripslashes($email))) {
		return true;
	} elseif ($show_error) {
		fn_set_notification('E', fn_get_lang_var('error'), str_replace('[email]', $email, fn_get_lang_var('text_not_valid_email')));
	}

	return false;
}

//
// Gets all available skins from skins_repository
//
function fn_get_available_skins($area = '')
{
	$sdir = 'var/skins_repository';
	if (!is_dir(DIR_ROOT . '/' . $sdir)) {
		$sdir = 'skins';
	}
	$skins = fn_get_dir_contents(DIR_ROOT . '/' . $sdir, true);
	sort($skins);
	$result = array();
	foreach ($skins as $v) {
		if (is_dir(DIR_ROOT . '/' . $sdir . '/' . $v) && $v != 'base') {
			$arr = @parse_ini_file(DIR_ROOT . '/' . $sdir . '/' . $v . '/' . SKIN_MANIFEST);
			if ((empty($area) || !empty($arr[$area])) && !empty($arr)) {
				$result[$v] = $arr;
			}
		}
	}

	return $result;
}


//
// Parses incoming data into proper SQL queries
// Based on PMA_splitSqlFile function from phpMyAdmin
// ------------
// Parameters:
// @ret - reference to array with parsed queries
// @sql - plain text data
function fn_parse_queries(&$ret, $sql)
{
	$sql_len      = strlen($sql);
	$char         = '';
	$string_start = '';
	$in_string    = FALSE;
	$time0        = time();

	$i = -1;
	while ($i < $sql_len) {
		$i++;
		if (!isset($sql[$i])) {
			return $sql;
		}
		$char = $sql[$i];


		// We are in a string, check for not escaped end of strings except for
		// backquotes that can't be escaped
		if ($in_string) {
			for (;;) {
				$i         = strpos($sql, $string_start, $i);
				// No end of string found -> add the current substring to the
				// returned array
				if (!$i) {
//                    $ret[] = $sql;
					return $sql;
				}
				// Backquotes or no backslashes before quotes: it's indeed the
				// end of the string -> exit the loop
				else if ($string_start == '`' || $sql[$i - 1] != '\\') {
					$string_start      = '';
					$in_string         = FALSE;
					break;
				}
				// one or more Backslashes before the presumed end of string...
				else {
					// ... first checks for escaped backslashes
					$j                     = 2;
					$escaped_backslash     = FALSE;
					while ($i- $j > 0 && $sql[$i - $j] == '\\') {
						$escaped_backslash = !$escaped_backslash;
						$j++;
					}
					// ... if escaped backslashes: it's really the end of the
					// string -> exit the loop
					if ($escaped_backslash) {
						$string_start  = '';
						$in_string     = FALSE;
						break;
					}
					// ... else loop
					else {
						$i++;
					}
				} // end if...elseif...else
			} // end for
		} // end if (in string)

		// We are not in a string, first check for delimiter...
		else if ($char == ';') {
			// if delimiter found, add the parsed part to the returned array
			$ret[]      = substr($sql, 0, $i);
			$sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
			$sql_len    = strlen($sql);
			if ($sql_len) {
				$i = -1;
			} else {
				// The submited statement(s) end(s) here
				return '';
			}
		} // end else if (is delimiter)

		// ... then check for start of a string,...
		else if (($char == '"') || ($char == '\'') || ($char == '`')) {
			$in_string    = TRUE;
			$string_start = $char;
		} // end else if (is start of string)

		// ... for start of a comment (and remove this comment if found)...
		else if ($char == '#' || ($i > 1 && $sql[$i - 2] . $sql[$i - 1] == '--')) {
			$sql = substr($sql, strpos($sql, "\n") + 1);
			$sql_len = strlen($sql);
			$i = -1;
		} // end else if (is comment)
	} // end for

	// add any rest to the returned array
	if (!empty($sql) && ereg('[^[:space:]]+', $sql)) {
		return $sql;
	}
	return '';
}

//
// Return the time of this day beginning
//
function fn_this_day_begin()
{
	$current_date = 0;
	$current_date = time();
	$_date_year = strftime("%Y", $current_date);
	$_date_month = strftime("%m", $current_date);
	$_date_day = strftime("%d", $current_date);
	return mktime(0, 0, 0, $_date_month, $_date_day, $_date_year);
}


function fn_flush()
{
	if (function_exists('ob_flush')) {
		@ob_flush();
	}

	flush();
}

function fn_echo($value)
{
	if (defined('CONSOLE')) {
		$value = str_replace(array('<br>', '<br />'), "\n", $value);
		$value = strip_tags($value);
	}

	echo $value;
	fn_flush();
}

//
// fn_print_r wrapper
// outputs variables data and dies
//
function fn_print_die()
{
	$args = func_get_args();
	call_user_func_array('fn_print_r', $args);
	die();
}

//
// Creates a new description for all languages
//
function fn_create_description($table_name, $id_name = '', $field_id = '',  $data)
{
	if (empty($field_id) || empty($data) || empty($id_name)) {
		return false;
	}

	$_data = fn_check_table_fields($data, $table_name);
	$_data[$id_name] = $field_id;

	foreach ((array)Registry::get('languages') as $_data['lang_code'] => $v) {
		db_query("REPLACE INTO ?:$table_name ?e", $_data);
	}

	return true;
}


function fn_js_escape($str)
{
	return strtr($str, array('\\' => '\\\\', "'" => "\\'", '"' => '\\"', "\r" => '\\r', "\n" => '\\n', '</' => '<\/'));
}

function fn_define($const, $value)
{
	if (!defined($const)) {
		define($const, $value);
	}
}

function fn_create_periods($params)
{
	$today = getdate(TIME);
	$period = !empty($params['period']) ? $params['period'] : null;

	$time_from = !empty($params['time_from']) ? fn_parse_date($params['time_from']) : 0;
	$time_to = !empty($params['time_to']) ? fn_parse_date($params['time_to'], true) : TIME;

	// Current dates
	if ($period == 'D') {
		$time_from = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);
		$time_to = TIME;

	} elseif ($period == 'W') {
		$wday = empty($today['wday']) ? "6" : (($today['wday'] == 1) ? "0" : $today['wday'] - 1);
		$wstart = getdate(strtotime("-$wday day"));
		$time_from = mktime(0, 0, 0, $wstart['mon'], $wstart['mday'], $wstart['year']);
		$time_to = TIME;

	} elseif ($period == 'M') {
		$time_from = mktime(0, 0, 0, $today['mon'], 1, $today['year']);
		$time_to = TIME;

	} elseif ($period == 'Y') {
		$time_from = mktime(0, 0, 0, 1, 1, $today['year']);
		$time_to = TIME;

	// Last dates
	} elseif ($period == 'LD') {
		$today = getdate(strtotime("-1 day"));
		$time_from = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);
		$time_to = mktime(23, 59, 59, $today['mon'], $today['mday'], $today['year']);

	} elseif ($period == 'LW') {
		$today = getdate(strtotime("-1 week"));
		$wday = empty($today['wday']) ? 6 : (($today['wday'] == 1) ? 0 : $today['wday'] - 1);
		$wstart = getdate(strtotime("-$wday day", mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year'])));
		$time_from = mktime(0, 0, 0, $wstart['mon'], $wstart['mday'], $wstart['year']);

		$wend = getdate(strtotime("+6 day", $time_from));
		$time_to = mktime(23, 59, 59, $wend['mon'], $wend['mday'], $wend['year']);

	} elseif ($period == 'LM') {
		$today = getdate(strtotime("-1 month"));
		$time_from = mktime(0, 0, 0, $today['mon'], 1, $today['year']);
		$time_to = mktime(23, 59, 59, $today['mon'], date('t', strtotime("-1 month")), $today['year']);

	} elseif ($period == 'LY') {
		$today = getdate(strtotime("-1 year"));
		$time_from = mktime(0, 0, 0, 1, 1, $today['year']);
		$time_to = mktime(23, 59, 59, 12, 31, $today['year']);

	// Last dates
	} elseif ($period == 'HH') {
		$today = getdate(strtotime("-23 hours"));
		$time_from = mktime($today['hours'], $today['minutes'], $today['seconds'], $today['mon'], $today['mday'], $today['year']);
		$time_to = TIME;

	} elseif ($period == 'HW') {
		$today = getdate(strtotime("-6 day"));
		$time_from = mktime($today['hours'], $today['minutes'], $today['seconds'], $today['mon'], $today['mday'], $today['year']);
		$time_to = TIME;

	} elseif ($period == 'HM') {
		$today = getdate(strtotime("-29 day"));
		$time_from = mktime($today['hours'], $today['minutes'], $today['seconds'], $today['mon'], $today['mday'], $today['year']);
		$time_to = TIME;

	} elseif ($period == 'HC') {
		$today = getdate(strtotime('-' . $params['last_days'] . ' day'));
		$time_from = mktime($today['hours'], $today['minutes'], $today['seconds'], $today['mon'], $today['mday'], $today['year']);
		$time_to = TIME;		
	}

	Registry::get('view')->assign('time_from', $time_from);
	Registry::get('view')->assign('time_to', $time_to);

	return array($time_from, $time_to);
}

function fn_parse_date($timestamp, $end_time = false)
{
	if (!empty($timestamp)) {
		if (is_numeric($timestamp)) {
			return $timestamp;
		}

		$ts = explode('/', $timestamp);
		if (count($ts) == 3) {
			list($h, $m, $s) = $end_time ? array(23, 59, 59) : array(0, 0, 0);
			if (Registry::get('settings.Appearance.calendar_date_format') == 'month_first') {
				$timestamp = mktime($h, $m, $s, $ts[0], $ts[1], $ts[2]);
			} else {
				$timestamp = mktime($h, $m, $s, $ts[1], $ts[0], $ts[2]);
			}
		} else {
			$timestamp = TIME;
		}
	}

	return !empty($timestamp) ? $timestamp : TIME;
}

//
// Set the cookie
// we use session.cookie_domain and session.cookie_path
//
function fn_set_cookie($var, $value, $expiry = 0)
{
	$_SESSION['settings'][$var] = array (
		'value' => $value
	);

	if (!empty($expiry)) {
		$_SESSION['settings'][$var]['expiry'] = TIME + $expiry;
	}
}

//
// Delete cookie
//
function fn_delete_cookies()
{
	$args = func_get_args();
	if (!empty($args)) {
		foreach ($args as $var) {
			unset($_SESSION['settings'][$var]);
		}

		return true;
	}

	return false;
}

//
// Get cookie value
//
function fn_get_cookie($var)
{
	if (!empty($_SESSION['settings'][$var]) && (empty($_SESSION['settings'][$var]['expiry']) ||  $_SESSION['settings'][$var]['expiry'] > TIME)) {
		
		return isset($_SESSION['settings'][$var]['value']) ? $_SESSION['settings'][$var]['value'] : '';
	} else {
		if (!empty($_SESSION['settings'][$var])) {
			unset($_SESSION['settings'][$var]);
		}

		return false;
	}
}

function fn_write_ini_file($path, $data)
{
	$content = '';
	foreach ($data as $k => $v) {
		if (is_array($v)) {
			$content .= "\n[{$k}]\n";
			foreach ($v as $_k => $_v) {
				if (is_numeric($_v) || is_bool($_v)) {
					$content .= "{$_k} = {$_v}\n";
				} else {
					$content .= "{$_k} = \"{$_v}\"\n";
				}
			}
		} else {
			if (is_numeric($v) || is_bool($v)) {
				$content .= "{$k} = {$v}\n";
			} else {
				$content .= "{$k} = \"{$v}\"\n";
			}
		}
	}

	if (!$handle = fopen($path, 'wb')) {
		return false;
	}

	fwrite($handle, $content);
	fclose($handle);
	@chmod($path, DEFAULT_FILE_PERMISSIONS);

	return true;
}

//
// The function returns Host IP and Proxy IP.
//
function fn_get_ip($return_int = false)
{
	$forwarded_ip = '';
	$fields = array(
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_FORWARDED_FOR',
		'HTTP_FORWARDED',
		'HTTP_forwarded_ip',
		'HTTP_X_COMING_FROM',
		'HTTP_COMING_FROM',
		'HTTP_CLIENT_IP',
		'HTTP_VIA',
		'HTTP_XROXY_CONNECTION',
		'HTTP_PROXY_CONNECTION');

	$matches = array();
	foreach ($fields as $f) {
		if (!empty($_SERVER[$f])) {
			preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $_SERVER[$f], $matches);
			if (!empty($matches) && !empty($matches[0]) && $matches[0] != $_SERVER['REMOTE_ADDR']) {
				$forwarded_ip = $matches[0];
				break;
			}
		}
	}

	$ip = array('host' => $forwarded_ip, 'proxy' => $_SERVER['REMOTE_ADDR']);

	if ($return_int) {
		foreach ($ip as $k => $_ip) {
			$ip[$k] = empty($_ip) ? 0 : sprintf("%u", ip2long($_ip));
		}
	}

	if (empty($ip['host']) || !fn_is_inet_ip($ip['host'], $return_int)) {
		$ip['host'] = $ip['proxy'];
		$ip['proxy'] = $return_int ? 0 : '';
	}

	return $ip;
}

//
// If there is IP address in address scope global then return true.
//
function fn_is_inet_ip($ip, $is_int = false)
{
	if ($is_int) {
		$ip = long2ip($ip);
	}
	$_ip = explode('.', $ip);
	return
		($_ip[0] == 10 ||
		($_ip[0] == 172 && $_ip[1] >= 16 && $_ip[1] <= 31) ||
		($_ip[0] == 192 && $_ip[1] == 168) ||
		($_ip[0] == 127 && $_ip[1] == 0 && $_ip[2] == 0 && $_ip[3] == 1) ||
		($_ip[0] == 255 && $_ip[1] == 255 && $_ip[2] == 255 && $_ip[3] == 255))
		? false : true;
}

//
// Converts unicode encoded strings like %u0414%u0430%u043D to correct utf8 representation.
//
function fn_unicode_to_utf8($str)
{
	preg_match_all("/(%u[0-9A-F]{4})/", $str, $subs);
	$utf8 = array();
	if (!empty($subs[1])) {
		foreach ($subs[1] as $unicode) {
			$_unicode = hexdec(substr($unicode, 2, 4));
			if ($_unicode < 128) {
				$_utf8 = chr($_unicode);
			} elseif ($_unicode < 2048) {
				$_utf8 = chr(192 +  (($_unicode - ($_unicode % 64)) / 64));
				$_utf8 .= chr(128 + ($_unicode % 64));
			} else {
				$_utf8 = chr(224 + (($_unicode - ($_unicode % 4096)) / 4096));
				$_utf8 .= chr(128 + ((($_unicode % 4096) - ($_unicode % 64)) / 64));
				$_utf8 .= chr(128 + ($_unicode % 64));
			}
			$utf8[$unicode] = $_utf8;
		}
	}
	if (!empty($utf8)) {
		foreach ($utf8 as $unicode => $_utf8) {
			$str = str_replace($unicode, $_utf8, $str);
		}
	}
	return $str;
}

function fn_image_verification($verification_id, $code)
{
	$auth = & $_SESSION['auth'];

	require (DIR_LIB . 'captcha/recaptchalib.php');
	
	if ($_POST["recaptcha_response_field"]) {
			$resp = recaptcha_check_answer ("6Lf1r8ISAAAAAJ9Tf6qko3-VTvH3pyt53u-fuGn1",
											$_SERVER["REMOTE_ADDR"],
											$_POST["recaptcha_challenge_field"],
											$_POST["recaptcha_response_field"]);

											return $resp->is_valid;
											
//			$error = $resp->error;
	}	
	
	return false;
}

function fn_needs_image_verification()
{
	$auth = & $_SESSION['auth'];

	return 
		!(Registry::get('config.tweaks.disable_captcha') == true || 
		(Registry::get('settings.Image_verification.hide_if_logged') == "Y" && $auth['user_id']) || 
		!empty($_SESSION['image_verification_ok']) ||
		(Registry::get('settings.Image_verification.hide_if_has_js') == "Y" && !empty($_SESSION['image_verification_js']))); // for future

}

function fn_array_key_intersect(&$a, &$b)
{
	$array = array();
	while (list($key, $value) = each($a)) {
		if (isset($b[$key])) {
			$array[$key] = $value;
		}
	}
	return $array;
}

// Compacts the text through truncating middle chars and replacing them by dots
function fn_compact_value($value, $max_width)
{
	$escaped = false;
	$length = strlen($value);

	$new_value = $value = fn_html_escape($value, true);
	if (strlen($new_value) != $length) {
		$escaped = true;
	}
	
	if ($length > $max_width) {
		$len_to_strip = $length - $max_width;
		$center_pos = $length / 2;
		$new_value = substr($value, 0, $center_pos - ($len_to_strip / 2)) . '...' . substr($value, $center_pos + ($len_to_strip / 2));
	}
	return ($escaped == true) ? fn_html_escape($new_value) : $new_value;
}



//
// Attach parameters to url. If parameter already exists, it removed.
//
function fn_link_attach($url, $attachment)
{
	$url = str_replace('&amp;', '&', $url);
	parse_str($attachment, $arr);

	$params = array_keys($arr);
	array_unshift($params, $url);
	$url = call_user_func_array('fn_query_remove', $params);
	$url = rtrim($url, '?&');
	$url .= ((strpos($url, '?') === false) ? '?' : '&') . $attachment;

	return str_replace('&', '&amp;', $url);
}

/**
 * Get views for the object
 *
 * @param string $object object to init view for
 * @return array views list
 */
function fn_get_views($object)
{
	return db_get_hash_array("SELECT name, view_id FROM ?:views WHERE object = ?s AND user_id = ?i", 'view_id', $object, $_SESSION['auth']['user_id']);
}

/**
 * Init search view
 *
 * @param string $object object to init view for
 * @param array $params request parameters
 * @return array filtered params
 */
function fn_init_view($object, $params)
{
	if (!empty($params['skip_view']) || AREA != 'A') {
		return $params;
	}

	$auth = & $_SESSION['auth'];

	// Save view
	if (ACTION == 'save_view' && !empty($params['new_view'])) {
		$name = $params['new_view'];
		$update_view_id = empty($params['update_view_id']) ? 0 : $params['update_view_id'];
		unset($params['dispatch'], $params['page'], $params['new_view'], $params['update_view_id']);
		$data = array (
			'object' => $object,
			'name' => $name,
			'params' => serialize($params),
			'user_id' => $auth['user_id']
		);

		if ($update_view_id) {
			db_query("UPDATE ?:views SET ?u WHERE view_id = ?i", $data, $update_view_id);
			$params['view_id'] = $update_view_id;
		} else {
			$params['view_id'] = db_query("REPLACE INTO ?:views ?e", $data);
		}
		$params['dispatch'] = CONTROLLER . '.' . MODE;

		fn_redirect(INDEX_SCRIPT . '?' . fn_build_query($params));

	} elseif (ACTION == 'delete_view' && !empty($params['view_id'])) {
		db_query("DELETE FROM ?:views WHERE view_id = ?i", $params['view_id']);

	} elseif (ACTION == 'reset_view') {
		db_query("UPDATE ?:views SET active = 'N' WHERE user_id = ?i AND object = ?s", $auth['user_id'], $object);
	}

	// Save search filter in session
	$condition = !empty($params['view_id']) ? db_quote("view_id = ?i", $params['view_id']) : db_quote("user_id = ?i AND object = ?s AND active = 'Y'", $auth['user_id'], $object);
	$data = db_get_row("SELECT params, view_id FROM ?:views WHERE ?p", $condition);

	if (!empty($data)) {
		$params['view_id'] = $data['view_id'];
		$params = fn_array_merge($params, unserialize($data['params']));

		db_query("UPDATE ?:views SET active = IF(view_id = ?i, 'Y', 'N') WHERE user_id = ?i AND object = ?s", $data['view_id'], $auth['user_id'], $object);
	}

	return $params;
}

function fn_init_last_view(&$params)
{
	$_actions = array('save_view', 'delete_view');
	$schema = fn_get_schema('last_view', 'view_conditions');

	if (!empty($params['return_to_list']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
		$params['redirect_url'] = CONTROLLER . '.' . (empty($schema[CONTROLLER]['list_mode']) ? 'manage' : $schema[CONTROLLER]['list_mode']) . '.last_view';
		if (!empty($schema[CONTROLLER]['selected_section'])) {
			$params['selected_section'] = $schema[CONTROLLER]['selected_section'];
		} elseif (!empty($schema[CONTROLLER]['update_mode']) && is_array($schema[CONTROLLER]['update_mode']) && isset($schema[CONTROLLER]['update_mode'][MODE]) && !empty($schema[CONTROLLER]['update_mode'][MODE]['selected_section'])) {
			$params['selected_section'] = $schema[CONTROLLER]['update_mode'][MODE]['selected_section'];
		} else {
			unset($params['selected_section']);
		}
		return;
	}
	$auth = & $_SESSION['auth'];
	if (isset($schema[CONTROLLER]) && ((!empty($schema[CONTROLLER]['list_mode']) && $schema[CONTROLLER]['list_mode'] == MODE) || MODE == 'manage') && (empty($schema[CONTROLLER]['update_mode']) || (!empty($schema[CONTROLLER]['update_mode']) && !is_array($schema[CONTROLLER]['update_mode']))) && isset($schema[CONTROLLER]['func'])) {
		if (ACTION == 'last_view' && empty($params['view_id'])) {
			$data = db_get_row("SELECT view_id, params, name FROM ?:views WHERE user_id = ?i AND object = ?s", $auth['user_id'], 'lv_' . CONTROLLER);
			if (!empty($data)) {
				db_query("UPDATE ?:views SET active = 'N' WHERE user_id = ?i AND object = ?s", $auth['user_id'], CONTROLLER);
				$params = fn_array_merge($params, unserialize($data['params']));
			}

		} elseif (!in_array(ACTION, $_actions)) {
			$store_data = $params;
			unset($store_data['dispatch'], $store_data['page']);
			$view_id = db_get_field("SELECT view_id FROM ?:views WHERE user_id = ?i AND object = ?s", $auth['user_id'], 'lv_' . CONTROLLER);
			$data = array (
				'object' => 'lv_' . CONTROLLER,
				'params' => serialize($store_data),
				'user_id' => $auth['user_id']
			);

			if (empty($view_id)) {
				db_query("INSERT INTO ?:views ?e", $data);
			} else {
				db_query("UPDATE ?:views SET ?u WHERE view_id = ?i", $data, $view_id);
			}
		}
	}
}

function fn_init_view_tools(&$params)
{
	$_actions = array('save_view', 'delete_view');
	$schema = fn_get_schema('last_view', 'view_conditions');

	$auth = & $_SESSION['auth'];
	if (isset($schema[CONTROLLER]) && ((empty($schema[CONTROLLER]['update_mode']) && MODE == 'update') || (!empty($schema[CONTROLLER]['update_mode']) && !is_array($schema[CONTROLLER]['update_mode']) && $schema[CONTROLLER]['update_mode'] == MODE)) && isset($schema[CONTROLLER]['item_id']) && isset($params[$schema[CONTROLLER]['item_id']])) {
		$view = & Registry::get('view');
		$condition = $schema[CONTROLLER];

		Registry::register_cache('last_view_item_ids', array('views'), CACHE_LEVEL_STATIC, true);

		if (Registry::is_exist('last_view_item_ids') != false) {
			$_ids = Registry::get('last_view_item_ids');
			$cur_key = array_search($params[$condition['item_id']], $_ids);
		}

		if (Registry::is_exist('last_view_item_ids') == false || (isset($cur_key) && ($cur_key === false || ($cur_key !== false && (!isset($_ids[$cur_key - 1]) || !isset($_ids[$cur_key + 1])))))) {
			$_ids = array();
			$data = db_get_field("SELECT params FROM ?:views WHERE user_id = ?i AND object = ?s AND active = 'Y'", $auth['user_id'], CONTROLLER);
			if (empty($data)) {
				$data = db_get_field("SELECT params FROM ?:views WHERE user_id = ?i AND object = ?s", $auth['user_id'], 'lv_' . CONTROLLER);
			}
			if (!empty($data)) {
				$data = unserialize($data);
				if (!empty($condition['additional_data'])) {
					$data = fn_array_merge($data, $condition['additional_data']);
				}

				if (!empty($condition['auth'])) {
					list($items, ) = $condition['func']($data, $auth);
				} else {
					list($items, ) = $condition['func']($data);
				}
				foreach ($items as $v) {
					$_ids[] = $v[$condition['item_id']];
				}

				$cur_key = array_search($params[$condition['item_id']], $_ids);
				if ($cur_key !== false) {
					if ($cur_key >= floor(MAX_LAST_VIEW_ITEMS / 2)) {
						Registry::set('last_view_item_ids', array_slice($_ids, $cur_key - floor(MAX_LAST_VIEW_ITEMS / 2), MAX_LAST_VIEW_ITEMS));
					} else {
						Registry::set('last_view_item_ids', array_slice($_ids, 0, $cur_key + floor(MAX_LAST_VIEW_ITEMS / 2)));
					}
				}
			}
		} else {
			$_ids = Registry::get('last_view_item_ids');
		}

		$cur_key = array_search($params[$condition['item_id']], $_ids);
		if ($cur_key !== false) {
			if (isset($_ids[$cur_key - 1])) {
				$view->assign('prev_id', $_ids[$cur_key - 1]);
			}
			if (isset($_ids[$cur_key + 1])) {
				$view->assign('next_id', $_ids[$cur_key + 1]);
			}
		}
	}
}

/**
 * Get all schema files (e.g. exim schemas, admin area menu)
 *
 * @param string $schema_dir schema name (subdirectory in /schema directory)
 * @param string $name file name/prefix
 * @param string $type schema type (php/xml)
 * @param bool $caching enable/disable schema caching
 * @param bool $force_addon_init initialize disabled addons also
 * @return array schema definition (if exists)
 */
function fn_get_schema($schema_dir, $name, $type = 'php', $caching = true, $force_addon_init = false)
{
	static $permission_schemas;
	
	if ($schema_dir == 'permissions' && !empty($permission_schemas[$name])) {
		return $permission_schemas[$name];
	}
	
	if ($caching == true) {
		Registry::register_cache('schema_' . $schema_dir . '_' . $name, array('settings', 'addons'), CACHE_LEVEL_STATIC); // FIXME: hardcoded for settings-based schemas
		if (Registry::is_exist('schema_' . $schema_dir . '_' . $name) == true) {
			return Registry::get('schema_' . $schema_dir . '_' . $name);
		}
	}

	$files = array();
	if (file_exists(DIR_SCHEMAS . $schema_dir . '/' . $name . '.' . $type)) {
		$files[] = DIR_SCHEMAS . $schema_dir . '/' . $name . '.' . $type;
	}

	$addons = Registry::get('addons');
	if (!empty($addons)) {
	foreach ($addons as $k => $v) {
		if ($v['status'] == 'D' && $force_addon_init && file_exists(DIR_ADDONS . $k . '/func.php')) { // force addon initialization
			include_once(DIR_ADDONS . $k . '/func.php');
		}

		if ($v['status'] == 'A' || $force_addon_init) {
			if (file_exists(DIR_ADDONS . $k . '/schemas/' . $schema_dir . '/' . $name . '.' . $type)) {
				array_unshift($files, DIR_ADDONS . $k . '/schemas/' . $schema_dir . '/' . $name . '.' . $type);
				continue;

			} elseif (file_exists(DIR_ADDONS . $k . '/schemas/' . $schema_dir . '/' . $name . '.post.' . $type)) {
				$files[] = DIR_ADDONS . $k . '/schemas/' . $schema_dir . '/' . $name . '.post.' . $type;
				continue;
			}
		}
	}
	}
	
	$schema = '';
	foreach ($files as $file) {
		if ($type == 'php') {
			include($file);
		} else {
			$schema .= file_get_contents($file);
		}
	}

	if ($caching == true) {
		Registry::set('schema_' . $schema_dir . '_' . $name, $schema);
	}

	if ($schema_dir == 'permissions') {
		$permission_schemas[$name] = $schema;
	}

	return $schema;
}

/**
 * Check access permissions for certain controller/modes
 *
 * @param string $controller controller to check permissions for
 * @param string $mode controller mode to check permissions for
 * @param string $schema_name permissions schema name (demo_mode/production)
 * @param string $request_method check permissions for certain method (POST/GET)
 * @return boolean true if access granted, false otherwise
 */
function fn_check_permissions($controller, $mode, $schema_name, $request_method = '', $request_variables = array(), $extra = '')
{
	$request_method = empty($request_method) ? $_SERVER['REQUEST_METHOD'] : $request_method;

	$schema = fn_get_schema('permissions', $schema_name);
	if ($schema_name == 'admin') {
		if (defined('COMPANY_ID')) {
			$_result = fn_check_vendor_permissions($controller, $mode, $request_method, $request_variables, $extra);
			if (!$_result) {
				return false; 
			}
		}
		
		return empty($_SESSION['auth']['usergroup_ids']) ? $_result : fn_check_admin_permissions($schema, $controller, $mode, $request_method, $request_variables);
	}
	if ($schema_name == 'demo') {

		if (isset($schema[$controller])) {
			if ((isset($schema[$controller]['restrict']) && in_array($request_method, $schema[$controller]['restrict'])) || (isset($schema[$controller]['modes'][$mode]) && in_array($request_method, $schema[$controller]['modes'][$mode]))) {
				return false;
			}
		}
	}

	if ($schema_name == 'trusted_controllers') {
		
		$allow = !empty($schema[$controller]['allow']) ? $schema[$controller]['allow'] : 0;
		if (!is_array($allow)) {
			return $allow;
		} else {
			return (!empty($allow[$mode]) ? $allow[$mode] : 0);	
		}
	}

	return true;
}
function fn_check_vendor_permissions($controller, $mode, $request_method = '', $request_variables = array(), $extra = '')
{
	$schema = fn_get_schema('permissions', 'vendor');
	$schema = $schema['controllers'];
	
	if (isset($schema[$controller])) {
		// Check if permissions set for certain mode
		if (isset($schema[$controller]['modes']) && isset($schema[$controller]['modes'][$mode])) {
			if (isset($schema[$controller]['modes'][$mode]['permissions'])) {
				$permission = is_array($schema[$controller]['modes'][$mode]['permissions']) ? $schema[$controller]['modes'][$mode]['permissions'][$request_method] : $schema[$controller]['modes'][$mode]['permissions'];
			} elseif (!empty($request_variables['table']) && isset($schema[$controller]['modes'][$mode]['param_permissions']['table_names'][$request_variables['table']])) {
				$permission = $schema[$controller]['modes'][$mode]['param_permissions']['table_names'][$request_variables['table']];
			} elseif (!empty($extra)) {
				if (isset($schema[$controller]['modes'][$mode]['param_permissions']['extra'][$extra])) {
					$permission = $schema[$controller]['modes'][$mode]['param_permissions']['extra'][$extra];
				} elseif (isset($schema[$controller]['modes'][$mode]['param_permissions']['permission'])) {
					$permission = $schema[$controller]['modes'][$mode]['param_permissions']['permission'];
				}
			}
		}

		// Check common permissions
		if (!isset($permission) && !empty($schema[$controller]['permissions'])) {
			$permission = is_array($schema[$controller]['permissions']) ? $schema[$controller]['permissions'][$request_method] : $schema[$controller]['permissions'];
		}
	}
	
	return isset($permission) ? $permission : false;
}

function fn_check_admin_permissions(&$schema, $controller, $mode, $request_method = '', $request_variables = array())
{
	if (isset($schema[$controller])) {
		// Check if permissions set for certain mode
		if (isset($schema[$controller]['modes']) && isset($schema[$controller]['modes'][$mode])) {
			if (isset($schema[$controller]['modes'][$mode]['permissions'])) {
				$permission = is_array($schema[$controller]['modes'][$mode]['permissions']) ? $schema[$controller]['modes'][$mode]['permissions'][$request_method] : $schema[$controller]['modes'][$mode]['permissions'];
				if (isset($schema[$controller]['modes'][$mode]['condition'])) {
					$condition = $schema[$controller]['modes'][$mode]['condition'];
				}

			} elseif (!empty($request_variables['table']) && isset($schema[$controller]['modes'][$mode]['param_permissions']['table_names'][$request_variables['table']])) {
				$permission = $schema[$controller]['modes'][$mode]['param_permissions']['table_names'][$request_variables['table']];
			}
		}

		// Check common permissions
		if (empty($permission) && !empty($schema[$controller]['permissions'])) {
			$permission = is_array($schema[$controller]['permissions']) ? $schema[$controller]['permissions'][$request_method] : $schema[$controller]['permissions'];
			if (isset($schema[$controller]['condition'])) {
				$condition = $schema[$controller]['condition'];
			}
		}

		if (empty($permission)) { // This controller does not have permission checking
			return true;
		} else {
			$exists = db_get_field("SELECT privilege FROM ?:usergroup_privileges WHERE usergroup_id IN(?n) AND privilege = ?s", $_SESSION['auth']['usergroup_ids'], $permission);

			$result = !empty($exists);
			
			if (isset($condition)) {
				if ($condition['operator'] == 'or') {
					return ($result || fn_execute_permission_condition($condition));
				} elseif ($condition['operator'] == 'and') {
					return ($result && fn_execute_permission_condition($condition));
				}
			}

			return $result;
		}
	}
	
	return true;
}

/**
 * Execute additional condition for permissions
 * Condition may be function or other conditions(will be implemented later)
 *
 * @param array $condition
 * 
 * @return boolean result of $condition
 */
function fn_execute_permission_condition($condition)
{
	if (isset($condition['function'])) {
		$func_name = array_shift($condition['function']);
		$params = $condition['function'];
		// here we can process parameters
		return call_user_func_array($func_name, $params);	
	}
	
	return false;
}

/**
 * Function checks do user want to manage his own profile
 *
 * @return boolean true, if user want to view/edit own profile, false otherwise.
 */
function fn_check_permission_manage_own_profile()
{
	return (empty($_REQUEST['user_id']) || $_REQUEST['user_id'] == $_SESSION['auth']['user_id']) ? true : false;
}
function fn_check_view_permissions($data, $request_method = 'POST', $extra = '')
{
	if ((!defined('RESTRICTED_ADMIN') && !defined('COMPANY_ID')) || !trim($data) || $data == 'submit') {
		return true;
	}

	if (!preg_match("/dispatch[=\[](\w+)\.(\w+)/", $data, $m)) {
		preg_match("/(\w+)\.?(\w+)?/", $data, $m);
	}

	return fn_check_permissions($m[1], $m[2], 'admin', $request_method, array(), $extra);
}
/**
 * This function searches placeholders in the text and converts the found data.
 *
 * @param string $text
 * @return changed text
 */

function fn_text_placeholders($text)
{
	static $placeholders = array(
		'price',
		'weight'
	);

	$pattern = '/%(\w+):(' . implode('|', $placeholders) . ')%/U';
	$text = preg_replace_callback($pattern, 'fn_apply_text_placeholders', $text);

	return $text;
}

function fn_apply_text_placeholders($matches)
{
	if (isset($matches[1]) && !empty($matches[2])) {
		if ($matches[2] == 'price') {
			$currencies = Registry::get('currencies');
			$currency = $currencies[CART_SECONDARY_CURRENCY];
			$value = fn_format_rate_value($matches[1], 'F', $currency['decimals'], $currency['decimals_separator'], $currency['thousands_separator'], $currency['coefficient']);

			return $currency['after'] == 'Y' ? $value . $currency['symbol'] : $currency['symbol'] . $value;
		} elseif ($matches[2] == 'weight') {

			return $matches[1] . '&nbsp;' . Registry::get('settings.General.weight_symbol');
		}
	}
}

function fn_generate_code($prefix = '', $length = 12)
{
	$postfix = '';
	$chars = implode('', range('0', '9')) . implode('', range('A', 'Z'));

	for ($i = 0; $i < $length; $i++) {
		$ratio = (strlen(str_replace('-', '', $postfix)) + 1) / 4;
		$postfix .= $chars[rand(0, strlen($chars) - 1)];
		$postfix .= ((ceil($ratio) == $ratio) && ($i < $length - 1)) ? '-' : '';
	}

	return (!empty($prefix)) ?  strtoupper($prefix) . '-' . $postfix : $postfix;
}

function fn_get_shipping_images()
{
	$data = db_get_array("SELECT ?:shippings.shipping_id, ?:shipping_descriptions.shipping FROM ?:shippings INNER JOIN ?:images_links ON ?:shippings.shipping_id = ?:images_links.object_id AND ?:images_links.object_type = 'shipping' LEFT JOIN ?:shipping_descriptions ON ?:shippings.shipping_id = ?:shipping_descriptions.shipping_id AND ?:shipping_descriptions.lang_code = ?s WHERE ?:shippings.status = 'A' ORDER BY ?:shippings.position, ?:shipping_descriptions.shipping", CART_LANGUAGE);

	if (empty($data)) {
		return array ();
	}

	$images = array ();

	foreach ($data as $key => $entry) {
		$image = fn_get_image_pairs($entry['shipping_id'], 'shipping', 'M');

		if (!empty($image['icon'])) {
			$image['icon']['alt'] = empty($image['icon']['alt']) ? $entry['shipping'] : $image['icon']['alt'];
			$images[] = $image['icon'];
		}
	}

	return $images;
}

function fn_get_payment_methods_images()
{
	$data = db_get_array("SELECT ?:payments.payment_id, ?:payment_descriptions.payment FROM ?:payments INNER JOIN ?:images_links ON ?:payments.payment_id = ?:images_links.object_id AND ?:images_links.object_type = 'payment' LEFT JOIN ?:payment_descriptions ON ?:payments.payment_id = ?:payment_descriptions.payment_id AND ?:payment_descriptions.lang_code = ?s WHERE ?:payments.status = 'A' ORDER BY ?:payments.position, ?:payment_descriptions.payment", CART_LANGUAGE);

	if (empty($data)) {
		return array ();
	}

	$images = array ();

	foreach ($data as $key => $entry) {
		$image = fn_get_image_pairs($entry['payment_id'], 'payment', 'M');

		if (!empty($image['icon'])) {
			$image['icon']['alt'] = empty($image['icon']['alt']) ? $entry['payment'] : $image['icon']['alt'];
			$images[] = $image['icon'];
		}
	}

	return $images;
}

function fn_get_credit_cards_images()
{
	$data = db_get_array("SELECT ?:static_data.param_id, ?:static_data_descriptions.descr  FROM ?:static_data INNER JOIN ?:images_links ON ?:static_data.param_id = ?:images_links.object_id AND ?:images_links.object_type = 'credit_card' LEFT JOIN ?:static_data_descriptions ON ?:static_data.param_id = ?:static_data_descriptions.param_id WHERE ?:static_data.status = 'A' AND ?:static_data.section = 'C' ORDER BY ?:static_data.position, ?:static_data_descriptions.descr ");

	if (empty($data)) {
		return array ();
	}

	$images = array ();

	foreach ($data as $key => $entry) {
		$image = fn_get_image_pairs($entry['param_id'], 'credit_card', 'M');

		if (!empty($image['icon'])) {
			$image['icon']['alt'] = empty($image['icon']['alt']) ? $entry['descr'] : $image['icon']['alt'];
			$images[] = $image['icon'];
		}
	}

	return $images;
}


//
// Get simple currencies list
//
function fn_get_simple_currencies($only_avail = true)
{
	$status_cond = ($only_avail) ? "WHERE status = 'A'" : '';

	return db_get_hash_single_array("SELECT a.*, b.description FROM ?:currencies as a LEFT JOIN ?:currency_descriptions as b ON a.currency_code = b.currency_code AND lang_code = ?s $status_cond ORDER BY a.position", array('currency_code' , 'description'), CART_LANGUAGE);
}

//
// Get simple languages list
//
function fn_get_simple_languages()
{
	return db_get_hash_single_array("SELECT lang_code, name FROM ?:languages WHERE status = 'A'", array('lang_code', 'name'));
}

function fn_html_to_pdf($html, $name)
{
	if (!fn_init_pdf()) {
		fn_redirect((!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : INDEX_SCRIPT);
	}

	$pipeline = PipelineFactory::create_default_pipeline('', '');

	if (!is_array($html)) {
		$html = array($html);
	}

	$pipeline->fetchers = array (
		new PdfFetcherMemory($html, Registry::get('config.current_location') . '/'),
		new FetcherURL(),
	);

	$pipeline->destination = new PdfDestinationDownload($name);

	$pipeline->data_filters = array (
		new DataFilterDoctype(),
		new DataFilterHTML2XHTML(),
	);

	$media = & Media::predefined('A4');
	$media->set_landscape(false);
	$media->set_margins(array('left' => 20, 'right' => 20, 'top' => 20, 'bottom' => 0));
	$media->set_pixels(600);

	$_config = array (
		'cssmedia' => 'print',
		'scalepoints' => '1',
		'renderimages' => true,
		'renderlinks' => true,
		'renderfields' => true,
		'renderforms' => false,
		'mode' => 'html',
		'encoding' => 'utf8',
		'debugbox' => false,
		'pdfversion' => '1.4',
		'draw_page_border' => false,
		'smartpagebreak' => true,
	);

	$pipeline->configure($_config);
	$pipeline->process_batch(array_keys($html), $media);
}

//
// Helper function: trims trailing and leading spaces
//
function fn_trim_helper(&$value)
{
	$value = is_string($value) ? trim($value) : $value;
}

/**
* Sort array by key 
*
* @param array $array - array for sorting
* @param string $key - key to sort by
* @param const $order - sort order (SORT_ASC/SORT_DESC)
* @return sorted array
*/
function fn_sort_array_by_key($array, $key, $order = SORT_ASC)
{
	uasort($array, create_function('$a, $b', "\$r = strnatcasecmp(\$a['$key'], \$b['$key']); return ($order == SORT_ASC) ? \$r : 0 - \$r ;"));
	return $array;
}

/**
* Explode string by delimiter and trim values
*
* @param string $delim - delimiter to explode by
* @param string $string - string to explode
* @return array
*/
function fn_explode($delim, $string)
{
	$a = explode($delim, $string);
	array_walk($a, 'fn_trim_helper');

	return $a;
}

/**
* Formats date using current language
*
* @param int $timestamp - timestamp of the date to format
* @param string $format - format string (see strftim)
* @return string formatted date
*/
function fn_date_format($timestamp, $format = '%b %e, %Y')
{
	if (substr(PHP_OS,0,3) == 'WIN') {
		$hours = strftime('%I', $timestamp);
		$short_hours = ($hours < 10) ? substr($hours, -1) : $hours;
		$_win_from = array ('%e', '%T', '%D', '%l');
		$_win_to = array ('%d', '%H:%M:%S', '%m/%d/%y', $short_hours);
		$format = str_replace($_win_from, $_win_to, $format);
	}

	$date = getdate($timestamp);
	$m = $date['mon'];
	$d = $date['mday'];
	$y = $date['year'];
	$wn = 0; // FIXME!!! get weeknumber
	$w = $date['wday'];
	$hr = $date['hours'];
	$pm = ($hr >= 12);
	$ir = ($pm) ? ($hr - 12) : $hr;
	$dy = $date['yday'];
	if ($ir == 0) {
		$ir = 12;
	}
	$min = $date['minutes'];
	$sec = $date['seconds'];

	// Preload language variables if needed
	$preload = array();
	if (strpos($format, '%a') !== false) {
		$preload[] = 'weekday_abr_' . $w;
	}
	if (strpos($format, '%A') !== false) {
		$preload[] = 'weekday_' . $w;
	}

	if (strpos($format, '%b') !== false) {
		$preload[] = 'month_name_abr_' . $m;
	}

	if (strpos($format, '%B') !== false) {
		$preload[] = 'month_name_' . $m;
	}

	fn_preload_lang_vars($preload);

	$s['%a'] = fn_get_lang_var('weekday_abr_'. $w); // abbreviated weekday name
	$s['%A'] = fn_get_lang_var('weekday_'. $w); // full weekday name
	$s['%b'] = fn_get_lang_var('month_name_abr_' . $m); // abbreviated month name
	$s['%B'] = fn_get_lang_var('month_name_' . $m); // full month name
	$s['%c'] = ''; // !!!FIXME: preferred date and time representation for the current locale
	$s['%C'] = 1 + floor($y / 100); // the century number
	$s['%d'] = ($d < 10) ? ('0' . $d) : $d; // the day of the month (range 01 to 31)
	$s['%e'] = $d; // the day of the month (range 1 to 31)
	$s['%ð'] = $s['%b'];
	$s['%H'] = ($hr < 10) ? ('0' . $hr) : $hr; // hour, range 00 to 23 (24h format)
	$s['%I'] = ($ir < 10) ? ('0' . $ir) : $ir; // hour, range 01 to 12 (12h format)
	$s['%j'] = ($dy < 100) ? (($dy < 10) ? ('00' . $dy) : ('0' . $dy)) : $dy; // day of the year (range 001 to 366)
	$s['%k'] = $hr;		// hour, range 0 to 23 (24h format)
	$s['%l'] = $ir;		// hour, range 1 to 12 (12h format)
	$s['%m'] = ($m < 10) ? ('0' . $m) : $m; // month, range 01 to 12
	$s['%M'] = ($min < 10) ? ('0' . $min) : $min; // minute, range 00 to 59
	$s['%n'] = "\n";		// a newline character
	$s['%p'] = $pm ? 'PM' : 'AM';
	$s['%P'] = $pm ? 'pm' : 'am';
	$s['%s'] = floor($timestamp / 1000);
	$s['%S'] = ($sec < 10) ? ('0' . $sec) : $sec; // seconds, range 00 to 59
	$s['%t'] = "\t";		// a tab character
	$s['%T'] = $s['%H'] .':'. $s['%M'] .':'. $s['%S'];
	$s['%U'] = $s['%W'] = $s['%V'] = ($wn < 10) ? ('0' . $wn) : $wn;
	$s['%u'] = $w + 1;	// the day of the week (range 1 to 7, 1 = MON)
	$s['%w'] = $w;		// the day of the week (range 0 to 6, 0 = SUN)
	$s['%y'] = substr($y, 2, 2); // year without the century (range 00 to 99)
	$s['%Y'] = $y;		// year with the century
	$s['%%'] = '%';		// a literal '%' character
	$s['%D'] = $s['%m'] .'/'. $s['%d'] .'/'. $s['%y'];// american date style: %m/%d/%y
	// FIXME: %x : preferred date representation for the current locale without the time
	// FIXME: %X : preferred time representation for the current locale without the date
	// FIXME: %G, %g (man strftime)
	// FIXME: %r : the time in am/pm notation %I:%M:%S %p
	// FIXME: %R : the time in 24-hour notation %H:%M
	return preg_replace("/(%.)/e", "\$s['\\1']", $format);
}

function fn_text_diff($source, $dest, $side_by_side = false)
{
	fn_init_diff();

	$diff = new Text_Diff('auto', array(explode("\n", $source), explode("\n", $dest)));
	$renderer = new Text_Diff_Renderer_inline();

	if ($side_by_side == false) {
		$renderer->_split_level = 'words';
	}

	$res = $renderer->render($diff);

	if ($side_by_side == true) {
		$res = $renderer->sideBySide($res);
	}

	return $res;
}

/**
 * Open/close store
 *
 * @param string $store_mode store operation mode: opened/closed
 * @return boolean always true
 */
function fn_set_store_mode($store_mode)
{
	if (Registry::get('settings.store_mode') != $store_mode) {
		db_query("UPDATE ?:settings SET value = ?s WHERE option_name = 'store_mode'", $store_mode);
		fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_store_mode_' . $store_mode));

		Registry::set('settings.store_mode', $store_mode);
		Registry::get('view')->assign('settings', Registry::get('settings'));
	}

	return true;
}

/**
 * Get addon option variants (similar to fn_get_settings_variants)
 *
 * @param string $addon addon to get option for
 * @param string $option_name option name
 * @param string $lang_code language code
 * @return array variants list
 */
function fn_get_addon_option_variants($addon, $option_name, $lang_code = CART_LANGUAGE)
{
	static $schema_loaded = false;

	if ($schema_loaded == false) {
		fn_get_schema('settings', 'variants', 'php', false, true);
		$schema_loaded = true;
	}

	$xml = simplexml_load_file(DIR_ADDONS . $addon . '/addon.xml');

	$variants = array();
	if (isset($xml->opt_settings)) {
		$sections_node = isset($xml->opt_settings->section) ? $xml->opt_settings->section : $xml->opt_settings;
		foreach ($sections_node as $section) {
			foreach ($section->item as $item) {
				if ((string)$item['id'] == $option_name) {
					$variant_names = array();
					if (isset($item->variants)) {
						foreach ($item->variants->item as $vitem) {
							$variant_names[] = (string)$vitem['id'];
						}
					}

					if (!empty($variant_names)) {
						$variants = db_get_hash_single_array("SELECT object_type, object_id, description FROM ?:addon_descriptions WHERE addon = ?s AND object_type = 'V' AND object_id IN (?a) AND lang_code = ?s", array('object_id', 'description'), $addon, $variant_names, $lang_code);
					}

					// Check if option has variants function
					$func = 'fn_settings_variants_addons_' . $addon . '_' . (string)$item['id'];
					if (function_exists($func)) {
						$variants = $func();
					}

					break;
				}
			}
		}
	}

	return $variants;
}

/**
 * Create array using $keys for keys and $value for values
 *
 * @param array $keys array keys
 * @param mixed $values if string/boolean, values array will be recreated with this value (e.g. $keys = array(1,2,3), $values = true => $values = array(0=>true,1=>true,2=>true)) 
 * @return array combined array
 */
function fn_array_combine($keys, $values)
{
	if (!is_array($values)) {
		$values = array_fill(0, sizeof($keys), $values);
	}

	return array_combine($keys, $values);
}

/**
 * Return cleaned text string (for meta description use)
 *
 * @param string $html - html code to generate description from
 * @param int $max_words - maximum words in description
 * @return string - cleaned text
 */
function fn_generate_meta_description($html, $max_words = 60) 
{
	$meta = array();
	if (!empty($html)) {
		$html = str_replace('&nbsp;', ' ', $html);
		$html = str_replace(array("\r\n", "\n", "\r"), ' ', html_entity_decode(trim($html), ENT_QUOTES, 'UTF-8'));
		$html = preg_replace('/\<br(\s*)?\/?\>/i', " ", $html);
		$html = strip_tags($html);
		$html = str_replace(array('.', ',', ':', ';', '`', '"', '~', '\'', '(', ')'), ' ', $html);
		$html = preg_replace('/\s+/', ' ', $html);
		$html = explode(' ', $html);
		foreach ($html as $k => $v) {
			if (fn_strlen($v) > 3) {
				$meta[] = $v;
			}
			if (count($meta) >= $max_words) {
				break;
			}
		}
	}

	return implode(' ', $meta);
}

/**
 * Calculate unsigned crc32 sum
 *
 * @param string $key - key to calculate sum for
 * @return int - crc32 sum
 */
function fn_crc32($key)
{
	return sprintf('%u', crc32($key));
}

/**
 * Check whether string is UTF-8 encoded
 *
 * @param string $str 
 * @return boolean
 */
function fn_is_utf8($str)
{
	$c = 0; $b = 0;
	$bits = 0;
	$len = strlen($str);
	for ($i = 0; $i < $len; $i++) {
		$c = ord($str[$i]);
		if ($c > 128) {
			if (($c >= 254)) {
				return false;
			} elseif ($c >= 252) {
				$bits = 6;
			} elseif ($c >= 248) {
				$bits = 5;
			} elseif ($c >= 240) {
				$bits = 4;
			} elseif ($c >= 224) {
				$bits = 3;
			} elseif ($c >= 192) {
				$bits = 2;
			} else {
				return false;
			}
			
			if (($i + $bits) > $len) {
				return false;
			}
			
			while ($bits > 1) {
				$i++;
				$b = ord($str[$i]);
				if ($b < 128 || $b > 191) {
					return false;
				}
				$bits--;
			}
		}
	}
	return true;
}

/**
 * Detect the cyrillic encoding of string
 *
 * @param string $str 
 * @return string cyrillic encoding
 */
function fn_detect_cyrillic_charset($str)
{
	fn_define('LOWERCASE', 3);
	fn_define('UPPERCASE', 1);
	
	$charsets = array(
		'KOI8-R' => 0,
		'CP1251' => 0,
		'CP866' => 0,
		'ISO-8859-5' => 0,
		'MAC-CYRILLIC' => 0
	);
					  
	for ($i = 0, $length = strlen($str); $i < $length; $i++) {
		$char = ord($str[$i]);
		//non-russian characters
		if ($char < 128 || $char > 256) {
			continue;
		}

		//CP866
		if (($char > 159 && $char < 176) || ($char > 223 && $char < 242)) {
			$charsets['CP866'] += LOWERCASE;
		}

		if (($char > 127 && $char < 160)) {
			$charsets['CP866'] += UPPERCASE;
		}

		//KOI8-R
		if (($char > 191 && $char < 223)) {
			$charsets['KOI8-R'] += LOWERCASE;
		}
		if (($char > 222 && $char < 256)) {
			$charsets['KOI8-R'] += UPPERCASE;
		}

		//CP1251
		if ($char > 223 && $char < 256) {
			$charsets['CP1251'] += LOWERCASE;
		}
		if ($char > 191 && $char < 224) {
			$charsets['CP1251'] += UPPERCASE;
		}

		//MAC-CYRILLIC
		if ($char > 221 && $char < 255) {
			$charsets['MAC-CYRILLIC'] += LOWERCASE;
		}
		if ($char > 127 && $char < 160) {
			$charsets['MAC-CYRILLIC'] += UPPERCASE;
		}

		//ISO-8859-5
		if ($char > 207 && $char < 240) {
			$charsets['ISO-8859-5'] += LOWERCASE;
		}
		if ($char > 175 && $char < 208) {
			$charsets['ISO-8859-5'] += UPPERCASE;
		}
	}

	arsort($charsets);
	return current($charsets) > 0 ? key($charsets) : '';
}

/**
 * Detect encoding by language
 *
 * @param string $resource string or file path
 * @param string $resource_type 'S' (string) or 'F' (file)
 * @param string $lang_code language of the file characters
 * @return string  detected encoding
 */

function fn_detect_encoding($resource, $resource_type = 'S', $lang_code = CART_LANGUAGE)
{
	$enc = '';
	$str = $resource;
	
	if ($resource_type == 'F') {
		$str = file_get_contents($resource);
		if ($str == false) {
			return $enc;
		}
	}
	
	if (!fn_is_utf8($str)) {
		$lang_code = strtolower($lang_code);
		
		if (in_array($lang_code, array('en', 'fr', 'es', 'it', 'nl', 'da', 'fi', 'sv', 'pt', 'nn', 'no'))) {
			$enc = 'ISO-8859-1';
		} elseif (in_array($lang_code, array('hu', 'cs', 'pl', 'bg', 'ro'))) {
			$enc = 'ISO-8859-2';					
		} elseif (in_array($lang_code, array('et', 'lv', 'lt'))) {
			$enc = 'ISO-8859-4';					
		} elseif ($lang_code == 'ru') {
			$enc = fn_detect_cyrillic_charset($str);
		} elseif ($lang_code == 'ar') {
			$enc = 'ISO-8859-6';					
		} elseif ($lang_code == 'el') {
			$enc = 'ISO-8859-7';					
		} elseif ($lang_code == 'he') {
			$enc = 'ISO-8859-8';					
		} elseif ($lang_code == 'tr') {
			$enc = 'ISO-8859-9';					
		}
	} else {
		$enc = 'UTF-8';
	}
	
	return $enc;
}

/**
 * Convert encoding of string or file
 *
 * @param string $from_enc  the encoding of the initial string/file
 * @param string $to_enc  the encoding of the result string/file 
 * @param string $resource string or file path 
 * @param string $resource_type 'S' (string) or 'F' (file)
 * @return string  string or file path
 */

function fn_convert_encoding($from_enc, $to_enc, $resource, $resource_type = 'S')
{
	if (empty($from_enc) || empty($to_enc) || ($resource_type == 'F' && empty($resource))) {
		return false;
	}
	
	if (strtoupper($from_enc) == strtoupper($to_enc)) {
		return $resource;
	}
	
	$str = $resource;
	if ($resource_type == 'F') {
		$str = file_get_contents($resource);
		if ($str == false) {
			return false;
		}
	}
	
	$_str = false;
	if (function_exists('iconv')) {
		$_str = iconv($from_enc, $to_enc, $str);
	} elseif (function_exists('mb_convert_encoding')) {
		$_str = mb_convert_encoding($str, $to_enc, $from_enc);
	}
	
	if ($resource_type == 'F') {
		if ($_str != false) {
			$f = fopen($resource, 'wb');
			if ($f) {
				fwrite($f, $_str);
				fclose($f);
				@chmod($resource, DEFAULT_FILE_PERMISSIONS);
			} else {
				$resource = false;
			}
		}
		
		return $resource;
	} else {
		return $_str;
	}
}

function fn_check_meta_redirect($url)
{
	if (empty($url)) {
		return false;
	}

	if (strpos($url, '://') && !(strpos($url, Registry::get('config.http_location')) === 0 || strpos($url, Registry::get('config.https_location')) === 0)) {
		return false;
	} else {
		return $url;
	}
}

function fn_get_notification_rules($params, $disable_notification = false)
{
	$force_notification = array();
	if ($disable_notification) {
		$force_notification = array('C' => false, 'A' => false);
	} else {
		if (!empty($params['notify_user']) || $params === true) {
			$force_notification['C'] = true;
		} else {
			if (AREA == 'A') {
				$force_notification['C'] = false;
			}
		}
		if (!empty($params['notify_department']) || $params === true) {
			$force_notification['A'] = true;
		} else {
			if (AREA == 'A') {
				$force_notification['A'] = false;
			}
		}
		if (!empty($params['notify_supplier']) || $params === true) {
			$force_notification['S'] = true;
		} else {
			if (AREA == 'A') {
				$force_notification['S'] = false;
			}
		}
	}

	return $force_notification;
}

/**
* Generate security hash to protect forms from CRSF attacks
*
* @return string salted hash
*/
function fn_generate_security_hash()
{
	if (empty($_SESSION['security_hash'])) {
		$_SESSION['security_hash'] = md5($config['crypt_key'] . Session::get_id());
	}

	return $_SESSION['security_hash'];
}

/**
 * substr() with full UTF-8 support
 *
 * @param string $string The input string.
 * @param integer $start If start  is non-negative, the returned string will start at the start 'th position in string , counting from zero. If start is negative, the returned string will start at the start 'th character from the end of string.
 * @param integer $length  If length  is given and is positive, the string returned will contain at most length  characters beginning from start  (depending on the length of string ). If length is given and is negative, then that many characters will be omitted from the end of string (after the start position has been calculated when a start is negative). If start denotes a position beyond this truncation, an empty string will be returned. 
 * @param integer $encoding The encoding parameter is the character encoding. If it is omitted, UTF-8 character encoding value will be used.
 * @return mixed Returns the extracted part of string or false if string is less than or equal to start characters long  
 */
function fn_substr($string, $start, $length = null, $encoding = 'UTF-8')
{
	if (empty($encoding)) {
		$encoding = 'UTF-8';	
	}
	
	if ($length === null) {
		return fn_substr($string, $start, fn_strlen($string, $encoding), $encoding);
	}
	
	if (function_exists('iconv_substr')) {
		// there was strange bug in iconv_substr when use negative length parameter
		// so we recalculate start and length here
		if ($length < 0) {
			$length = ceil($length);
			$len = iconv_strlen($string, $encoding);
			if ($start < 0) {
				$start += $len;	
			}
			$length += $len - $start;
		}
		
		return iconv_substr($string, $start, $length, $encoding);
	} elseif (function_exists('mb_substr')) {
		return mb_substr($string, $start, $length, $encoding);
	} else {
		preg_match_all('/./su', $string, $ar);
		return join('', array_slice($ar[0], $start, $length));
	}
}

/**
 * strlen() with full UTF-8 support
 *
 * @param string $string The string being measured for length.
 * @param string $encoding The encoding parameter is the character encoding. If it is omitted, UTF-8 character encoding value will be used.
 * @return integer The length of the string on success, and 0 if the string is empty.
 */
function fn_strlen($string, $encoding = 'UTF-8')
{
	if (empty($encoding)) {
		$encoding = 'UTF-8';	
	}
	
	if (function_exists('iconv_strlen')) {
		return iconv_strlen($string, $encoding);
	} elseif (function_exists('mb_strlen')) {
		return mb_strlen($string, $encoding);
	} else {
		preg_match_all('/./su', $string, $ar);
		return count($ar[0]);
	}
}

/**
 * Convert short urls to normal urls
 *
 * @param string $url url
 * @param string $area area
 * @param string prefix rel|http|https
 * @param string delimeter &|&amp;
 * @param string $lang_code language code
 * @return boolean always true
 */
function fn_url($url, $area = AREA, $prefix = 'rel', $delimeter = '&amp;', $lang_code = CART_LANGUAGE)
{
	static $init_vars = false;
	static $admin_index, $customer_index, $http_location, $https_location, $current_location;
	
	if (!$init_vars) {
		$admin_index = Registry::get('config.admin_index');
		$customer_index = Registry::get('config.customer_index');
		$http_location = Registry::get('config.http_location');
		$https_location = Registry::get('config.https_location');
		$current_location = Registry::get('config.current_location');
		
		$init_vars = true;
	}
	
	$url = str_replace('&amp;', '&', $url);
	$parced_url = parse_url($url);
	$no_shorted = false;

	if (!empty($parced_url['scheme']) || !empty($parced_url['host'])) {
		$no_shorted = true;
	} else {
		if (!empty($parced_url['path'])) {
			if (stripos($parced_url['path'], $admin_index) !== FALSE) {
				$area = 'A';
				$no_shorted = true;
			} elseif (stripos($parced_url['path'], $customer_index) !== FALSE) {
				$area = 'C';
				$no_shorted = true;
			}
		} else {
			$url = $_url = ($area == 'C') ? $customer_index : $admin_index;
			$no_shorted = true;
		}
	}

	$index_script = ($area == 'C') ? $customer_index : $admin_index;

	$_url = ($no_shorted)? $url : $index_script . '?dispatch=' . str_replace('?', $delimeter, $url);

	if (!empty($parced_url['query'])) {
		$split_query = explode('&', $parced_url['query']);
		$_url = str_replace($parced_url['query'], join($delimeter, $split_query), $_url);
	}

	if ($prefix != 'rel') {
		if ($prefix == 'http') {
			$_url = $http_location . '/' . $_url;
		} elseif ($prefix == 'https') {
			$_url = $https_location . '/' . $_url;
		}  elseif ($prefix == 'current') {
			$_url = $current_location . '/' . $_url;
		}
	}

	fn_set_hook('url', $_url, $area, $delimeter, $url, $lang_code);

	return $_url;
}

function fn_get_manifest_definition()
{
	$areas = array(
		'C' => array (
			'skin' => 'customer',
			'path' => 'customer',
			'name' => 'Customer_logo',
			'text' => 'text_customer_area_logo'
		),
		'M' => array (
			'skin' => 'customer',
			'path' => 'mail',
			'name' => 'Mail_logo',
			'text' => 'text_mail_area_logo'
		),
		'A' => array (
			'skin' => 'admin',
			'path' => 'admin',
			'name' => 'Admin_logo',
			'text' => 'text_admin_panel_logo'
		),
		'L' => array (
			'skin' => 'admin',
			'path' => 'admin',
			'name' => 'Signin_logo',
			'text' => 'text_signin_logo'
		)
	);

	fn_set_hook('get_manifest_definition', $areas);
	
	return $areas;
}

function fn_get_manifest($area, $lang_code = CART_LANGUAGE, $company_id = 0)
{
	$manifest = parse_ini_file(DIR_SKINS . Registry::get('settings.skin_name_' . $area) . '/' . SKIN_MANIFEST, true);

	$exclude_key = array (
		'description',
		'admin',
		'customer'
	);

	if (defined('COMPANY_ID')) {
		$company_id = COMPANY_ID;
	}

	if ($company_id) {
		$company_data = fn_get_company_data($company_id, $lang_code);
		$logos = !empty($company_data['logos']) ? unserialize($company_data['logos']) : array();
		$manifest = array_merge($manifest, $logos);
	}

	$alts = db_get_hash_single_array("SELECT object_holder, description FROM ?:common_descriptions WHERE object_id = ?i AND lang_code = ?s", array('object_holder', 'description'), $company_id, $lang_code);

	foreach ($manifest as $key => $val) {
		if (!in_array($key, $exclude_key) && isset($alts[$key])) {
			$manifest[$key]['alt'] = $alts[$key];
		}
	}

	return $manifest;
}

/**
 * Check for non empty string
 *
 * @param string $str string
 * @return boolean string is empty?
 */
function fn_string_no_empty($str)
{
	$result = (strlen((trim($str)))>0)? true : false;
	return $result;
}

/**
 * Check for number
 *
 * @param string $num number
 * @return boolean string is number?
 */
function fn_is_numeric($num)
{
	return is_numeric(trim($num));
}

/**
 * @Fancy recursive function to search for substring in an array 
 * @param string $neele
 * @param mixed $haystack
 * @return bool
 * @author andyye
 */
function fn_substr_in_array($what_str, $where_arr)
{
	foreach ($where_arr as $v) {
		if (is_array($v)) {
			$sub_arr = fn_substr_in_array($what_str, $v);
			if ($sub_arr) {
				return true;
			}
		} else {
			if (strpos($v, $what_str) !== false) {
				return true;
			}
		}
	}
	return false;
}

function fn_return_bytes($val)
{
	$last = strtolower($val{strlen($val)-1});
	switch($last) {
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}

	return $val;
}

/**
 * Funtion formats user-entered price into float.
 *
 * @param string $price
 * @param string $currency
 * @return float Well-formatted price.
 */
function fn_parse_price($price, $currency = CART_PRIMARY_CURRENCY)
{
	$decimals = Registry::get('currencies.' . $currency . '.decimals');
	$dec_sep = Registry::get('currencies.' . $currency . '.decimals_separator');
	$thous_sep = Registry::get('currencies.' . $currency . '.thousands_separator');

	if ($dec_sep == $thous_sep) {
		if (($last = strrpos($price, $dec_sep)) !== false) {
			if ($thous_sep == '.') {
				$price = str_replace('.', ',', $price);
			}
			$price = substr_replace($price, '.', $last, 1);
		}
	} else {
		if ($thous_sep == '.') {
			// is it really thousands separator?
			// if there is decimals separator, than we can replace ths_sep
			if (strpos($price, $dec_sep) !== false) {
				$price = str_replace($thous_sep, '', $price);
			} else {
				//if there are 3 digits rigth of the separator - it is ths_sep too.
				if (($last = strrpos($price, '.')) !== false) {
					$last_part = substr($price, $last);
					$last_part = preg_replace('/[^\d]/', '', $last_part);
					if (strlen($last_part) == 3 && $decimals != 3) {
						$price = str_replace($thous_sep, '', $price);
					}
				}
			}
		}

		if ($dec_sep != '.') {
			$price = str_replace($dec_sep, '.', $price);
		}
	}

	$price = preg_replace('/[^\d\.]/', '', $price);

	return round(floatval($price), $decimals);
}

/**
 * Function saves alternative text for logos
 *
 * @param mixed $areas List of manifest areas
 * @param integer $company_id Company ID for vendors, left 0 for main store.
 */
function fn_save_logo_alt($areas, $company_id = 0)
{
	foreach ($_REQUEST['logo_alt'] as $type => $alt) {
		$_data = array (
			'object_id' => $company_id,
			'description' => empty($alt) ? '' : trim($alt),
			'lang_code' => CART_LANGUAGE,
			'object_holder' => $areas[$type]['name']
		);
		db_query("REPLACE INTO ?:common_descriptions ?e", $_data);
	}
}

/**
 * Function replaces table prefix 'cscart_' to user's prefix.
 *
 * @param string $query Query
 * @return string Updated query
 */
function fn_check_db_prefix($query)
{
	$default_prefix = 'cscart_';
	if (TABLE_PREFIX != $default_prefix) {
		$pos = strpos($query, $default_prefix);
		if ($pos !== false) {
			$query = substr_replace($query, TABLE_PREFIX, $pos, strlen($default_prefix));
		}
	}
	return $query;
}

?>