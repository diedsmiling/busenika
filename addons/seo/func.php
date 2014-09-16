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
// $Id: func.php 10222 2010-07-27 10:12:00Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_delete_seo_name($object_id, $object_type, $dispatch = '')
{
	db_query("DELETE FROM ?:seo_names WHERE object_id = ?i AND type = ?s AND dispatch = ?s", $object_id, $object_type, $dispatch);

	return true;
}

function fn_seo_delete_product($product_id)
{
	return fn_delete_seo_name($product_id, 'p');
}

function fn_seo_delete_category($category_id)
{
	return fn_delete_seo_name($category_id, 'c');
}

function fn_seo_delete_page($page_id)
{
	return fn_delete_seo_name($page_id, 'a');
}

function fn_seo_delete_news($news_id)
{
	return fn_delete_seo_name($news_id, 'n');
}

function fn_create_seo_name($object_id, $object_type, $object_name, $index = 0, $dispatch = '', $lang_code = CART_LANGUAGE)
{
	$_object_name = fn_generate_name($object_name);
	if (empty($_object_name)) {
		$__name = fn_get_seo_vars($object_type);
		$_object_name = $__name['description'] . '-' . (empty($object_id) ? $dispatch : $object_id);
	}

	$exist = db_get_field("SELECT name FROM ?:seo_names WHERE name = ?s AND (object_id != ?i OR type != ?s OR dispatch != ?s OR lang_code != ?s)", $_object_name, $object_id, $object_type, $dispatch, $lang_code);
	if (!$exist) {
		$_data = array(
			'name' => $_object_name,
			'type' => $object_type,
			'object_id' => $object_id,
			'dispatch' => $dispatch,
			'lang_code' => $lang_code
		);
		db_query("REPLACE INTO ?:seo_names ?e", $_data);
	} else {
		$index ++;
		$_object_name = fn_create_seo_name($object_id, $object_type, $object_name . SEO_DELIMITER . ($index == 1 ? strtolower($lang_code) : $index), $index, $dispatch, $lang_code);
	}

	return $_object_name;
}

function fn_get_corrected_seo_lang_code($lang_code)
{
	return (Registry::get('addons.seo.single_url') == 'Y')? Registry::get('settings.Appearance.customer_default_language') : $lang_code;
}

function fn_seo_get_product_data($product_id, &$field_list, &$join, $auth, $lang_code)
{
	$field_list .= ', ?:seo_names.name as seo_name';
	$join .= db_quote(" LEFT JOIN ?:seo_names ON ?:seo_names.object_id = ?i AND ?:seo_names.type = 'p' AND ?:seo_names.dispatch = '' AND ?:seo_names.lang_code = ?s", $product_id, fn_get_corrected_seo_lang_code($lang_code));

	return true;
}

function fn_seo_get_products(&$params, &$fields, &$sortings, &$condition, &$join, $sorting, $group_by, $lang_code)
{
	$fields[] = '?:seo_names.name as seo_name';
	$join .= db_quote(" LEFT JOIN ?:seo_names ON ?:seo_names.object_id = products.product_id AND ?:seo_names.type = 'p' AND ?:seo_names.dispatch = '' AND ?:seo_names.lang_code = ?s", $lang_code);
}
function fn_seo_get_categories($params, $join, $condition, $fields, $group_by, $sortings)
{
	$fields[] = '?:seo_names.name as seo_name';
	$join .= db_quote(" LEFT JOIN ?:seo_names ON ?:seo_names.object_id = ?:categories.category_id AND ?:seo_names.type = 'c' AND ?:seo_names.dispatch = '' AND ?:seo_names.lang_code = ?s", CART_LANGUAGE);
}
function fn_seo_get_news($params, &$fields, &$join, $condition, $sorting, $limit, $lang_code)
{
	$fields[] = '?:seo_names.name as seo_name';
	$join .= db_quote(" LEFT JOIN ?:seo_names ON ?:seo_names.object_id = ?:news.news_id AND ?:seo_names.type = 'n' AND ?:seo_names.dispatch = '' AND ?:seo_names.lang_code = ?s", $lang_code);
}
function fn_seo_get_category_data($category_id, &$field_list, &$join, $lang_code)
{
	$field_list .= ', ?:seo_names.name as seo_name';
	$join .= db_quote(" LEFT JOIN ?:seo_names ON ?:seo_names.object_id = ?i AND ?:seo_names.type = 'c' AND ?:seo_names.dispatch = '' AND ?:seo_names.lang_code = ?s", $category_id, fn_get_corrected_seo_lang_code($lang_code));
	return true;
}

function fn_seo_get_page_data(&$page_data, $lang_code)
{
	$page_data['seo_name'] = db_get_field("SELECT name FROM ?:seo_names WHERE object_id = ?i AND type = 'a' AND ?:seo_names.dispatch = '' AND ?:seo_names.lang_code = ?s", $page_data['page_id'], fn_get_corrected_seo_lang_code($lang_code));
	return true;
}

function fn_get_seo_vars($type = '')
{
	$seo = array(
		'p' => array(
			'table' => '?:product_descriptions',
			'description' => 'product',
			'dispatch' => 'products.view',
			'item' => 'product_id',
		),
		'c' => array(
			'table' => '?:category_descriptions',
			'description' => 'category',
			'dispatch' => 'categories.view',
			'item' => 'category_id',
		),
		'a' => array(
			'table' => '?:page_descriptions',
			'description' => 'page',
			'dispatch' => 'pages.view',
			'item' => 'page_id',
		),
		'e' => array(
			'table' => '?:product_feature_variant_descriptions',
			'description' => 'variant',
			'dispatch' => 'product_features.view',
			'item' => 'variant_id',
		),
		's' => array(
			'table' => '?:seo_names',
			'description' => 'name',
			'dispatch' => '',
			'item' => 'object_id',
		),
	);

	fn_set_hook('get_seo_vars', $seo);

	return (!empty($type)) ? $seo[$type] : $seo;
}

function fn_get_rewrite_rules()
{
	$customer_index = Registry::get('config.customer_index');

	$prefix = (Registry::get('addons.seo.html_catalog') == 'Y') ? '\/(catalog)' : '()';
	$prefix .= (Registry::get('addons.seo.seo_language') == 'Y') ? '\/([a-z]{2})' : '()';

	$rewrite_rules = array();

	fn_set_hook('get_rewrite_rules', $rewrite_rules, $prefix);

	$extension = str_replace('.', '', SEO_FILENAME_EXTENSION);

	$rewrite_rules['!^(.*)?' . $prefix . '\/(.*\/)?([^\/]+)-page-([0-9]+|full_list)\.(' . $extension . ')$!'] = 'object_name=$matches[5]&page=$matches[6]&sl=$matches[3]&extension=$matches[7]';
	$rewrite_rules['!^(.*)?' . $prefix . '\/(.*\/)?([^\/]+)\.(' . $extension . ')$!'] = 'object_name=$matches[5]&sl=$matches[3]&extension=$matches[6]';
	if (Registry::get('addons.seo.seo_language') == 'Y') {

		$rewrite_rules['!^(.*)?' . $prefix . '\/?$!'] = '$customer_index?sl=$matches[3]';
	}
	if (Registry::get('addons.seo.seo_category_type') != 'file') {
		$rewrite_rules['!^(.*)?' . $prefix . '\/(.*\/)?([^\/]+)\/page-([0-9]+|full_list)(\/)?$!'] = 'object_name=$matches[5]&page=$matches[6]&sl=$matches[3]';
	}
	$rewrite_rules['!^(.*)?' . $prefix . '\/(.*\/)?([^\/?]+)\/?$!'] = 'object_name=$matches[5]&sl=$matches[3]';

	$rewrite_rules['!^(.*)?' . $prefix . '/$!'] = '';

	return $rewrite_rules;
}

function fn_seo_get_route(& $req)
{
	$config = & Registry::get('config');
	$seo_settings = & Registry::get('addons.seo');

	if ((AREA == 'C') && !empty($req['sef_rewrite'])) {

		// Remove web directory from request
		if (!preg_match('!^(.*)?/('.$config['customer_index'].')(.*)$!', $_SERVER['REQUEST_URI'])) {

			$url_pattern = parse_url($_SERVER['REQUEST_URI']);
			$rewrite_rules = fn_get_rewrite_rules();

			foreach ($rewrite_rules as $pattern => $query) {
				if (preg_match($pattern, $url_pattern['path'], $matches) || preg_match($pattern, urldecode($query), $matches)) {
					$_query = preg_replace("!^.+\?!", '', $query);
					parse_str($_query, $objects);
					$result_values = 'matches';
					$url_query = "";
					foreach ($objects as $key => $value) {
						preg_match('!^.+\[([0-9])+\]$!', $value, $_id);
						$objects[$key] = (substr($value, 0, 1) == '$') ? ${$result_values}[$_id[1]] : $value;
					}

					// For the locations wich names stored in the table
					if (!empty($objects) && !empty($objects['object_name'])) {
						$_seo = db_get_row("SELECT * FROM ?:seo_names WHERE name = ?s", $objects['object_name']);

						if (empty($_seo) && !empty($objects['extension'])) {
							$_seo = db_get_row("SELECT * FROM ?:seo_names WHERE name = ?s", $objects['object_name'] . '.' . $objects['extension']);
						}

						if (!empty($_seo) && ($_seo['type'] == 's' && !empty($objects['extension']) && strpos($_seo['name'], '.' . $objects['extension']) === false || Registry::get('addons.seo.seo_category_type') == 'file' && $_seo['type'] == 'c' && empty($objects['extension']))) {
							$_seo = array();
							$objects['object_name'] = '_wrong_path_';
						}
						if (!empty($_seo)) {

							if (Registry::get('addons.seo.single_url') == 'Y') {
								$objects['sl'] = (Registry::get('addons.seo.seo_language') == 'Y')? $objects['sl']: '';
								$objects['sl'] = !empty($req['sl'])? $req['sl'] : $objects['sl'];
							} else {
								$objects['sl'] = !empty($objects['sl'])? $objects['sl'] : $_seo['lang_code'];
							}

							$req['sl'] = strtoupper($objects['sl']);

							if (fn_seo_validate_object($_seo, $url_pattern['path'], $objects) == false) {
								$req = array(
									'dispatch' => '_no_page'
								);
								return false;
							}

							$_seo_vars = fn_get_seo_vars($_seo['type']);
							if ($_seo['type'] == 's') {
								$url_query = 'dispatch=' . $_seo['dispatch'];
								$req['dispatch'] = $_seo['dispatch'];
							} else {
								$page_suffix = (!empty($objects['page'])) ? ('&page=' . $objects['page']) : '';
								$url_query = 'dispatch=' . $_seo_vars['dispatch'] .'&'. $_seo_vars['item'] .'='. $_seo['object_id'] . $page_suffix;

								$req['dispatch'] = $_seo_vars['dispatch'];
							}
							$req[$_seo_vars['item']] = $_seo['object_id'];

						} elseif (!strstr($config['http_path'], $objects['object_name']) || strlen($objects['object_name']) == 2) {
							$req = array(
								'dispatch' => '_no_page'
							);
							return false;
						}

					// For the locations wich names are not in the table
					} elseif (!empty($objects)) {
						//$url_query = empty($objects['dispatch']) ? '' : "dispatch=$objects[dispatch]";
						if (empty($objects['dispatch'])) {
							if (!empty($req['dispatch'])) {
								$req['dispatch'] = is_array($req['dispatch']) ? key($req['dispatch']) : $req['dispatch'];
								$url_query = 'dispatch=' . $req['dispatch'];
							}
						} else {
							$url_query = 'dispatch=' . $objects['dispatch'];
							$req['dispatch'] = $objects['dispatch'];
						}
						if (!empty($objects['sl'])) {
							$req['sl'] = strtoupper($objects['sl']);
						}

					// Empty query
					} else {
						$url_query = '';
					}

					if (!empty($objects['page'])) {
						$req['page'] = $objects['page'];
					}

					Registry::set('seo_url', array(
						'page' => substr($_SERVER['REQUEST_URI'], strlen((defined('HTTPS') ? $config['https_path'] : $config['http_path'])) + 1),
						'href' => $_SERVER['REQUEST_URI'],
						'query' => substr(strstr($_SERVER['REQUEST_URI'], '?'), 1),
						'static' => (Registry::get('addons.seo.html_catalog') == 'Y')
					));

					$_SERVER['REQUEST_URI'] = $config['customer_index'] . '?' . $url_query;
					$_SERVER['QUERY_STRING'] = $url_query . (!empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '');
					break;
				}
			}

			if ($su = Registry::get('seo_url')) {
				if ($su['static']) {
					Registry::set('seo_path', $url_pattern['path']);
					// Disable dynamic options
					Registry::set('settings.DHTML.customer_ajax_based_pagination', 'N');
					Registry::set('settings.DHTML.ajax_add_to_cart', 'N');
					Registry::set('settings.DHTML.ajax_comparison_list', 'N');
					// Disable session
					fn_define('NO_SESSION', true);

					// Capture page output
					ob_start('fn_seo_generate_page');
				}
			} else {
				$req = array(
					'dispatch' => '_no_page'
				);
			}
		}

		unset($req['sef_rewrite']);
		$_SERVER['QUERY_STRING'] = fn_query_remove($_SERVER['QUERY_STRING'], 'sef_rewrite');
	}
}
function fn_seo_init_templater(&$view, &$view_mail)
{
/*
	if (AREA == 'C' && Registry::get('addons.seo.html_catalog') == 'Y') {
		$view->register_prefilter('fn_seo_parse_tpls');
		if ($su = Registry::get('seo_url')) {
			if (!empty($su['static'])) {
				$view->assign('stay_in_cart', true);
			}
		}
	}
*/
}
function fn_seo_parse_tpls($tpl_source, &$view)
{
	fn_set_hook('seo_parse_tpls', $tpl_source);

	if (Registry::get('addons.seo.html_catalog') == 'Y') {
		// Remove dynamic blocks
		if (preg_match_all("/<\!--dynamic:([\w]*)-->(?:(?>[^<]+)|<(?!--dynamic\b[^>]*>))*?<\!--\/dynamic-->/", $tpl_source, $matches)) {
			foreach ($matches[0] as $_ind => $match) {
				if (Registry::get('addons.seo.seo_language') == 'Y' && $matches[1][$_ind] == 'languages') {
					continue;
				}
				$tpl_source = str_replace($match, '{if !$seo_url.static}' . $match . '{/if}', $tpl_source);
			}
		}
	}

	return $tpl_source;
}

function fn_seo_generate_page($text)
{
	if (!empty($text) && strpos($text, '<meta http-equiv="Refresh"') === false) { // do not create the page if redirect occurs
		$path = Registry::get('seo_path');
		$path = substr($path, strlen((defined('HTTPS') ? Registry::get('config.https_path') : Registry::get('config.http_path'))) + 1); // remove path prefix
		$path = substr($path, strlen('catalog') + 1); // remove catalog prefix
		$path = rtrim($path, '/'); // remove trailing slash

		if (strpos($path, SEO_FILENAME_EXTENSION) !== false) {
			$file = basename($path);
			$dir = dirname($path);
		} else {
			$file = 'index'.SEO_FILENAME_EXTENSION;
			$dir = $path;
		}

		// Redirect to http location if opened via https
		$script = "<script type=\"text/javascript\">\n" .
			"//<![CDATA[\n" .
			"if (document.location.protocol == 'https:') {\n" .
			"	document.location.replace('" . Registry::get('config.http_location') . '/' . Registry::get('seo_url.page')  . "');\n" .
			"}\n" .
			"//]]>\n" .
			"</script>\n";
		$text = str_replace('<base href=', $script . '<base href=', $text);

		fn_mkdir(DIR_ROOT . '/catalog/' . $dir);
		fn_put_contents(DIR_ROOT . '/catalog/' . $dir . '/' . $file, $text);
	}
	return $text;
}


function fn_seo_update_category($category_data, $category_id, $lang_code)
{
	if (isset($category_data['seo_name'])) {
		fn_create_seo_name($category_id, 'c', (!empty($category_data['seo_name'])) ? $category_data['seo_name'] : $category_data['category'], 0, '', fn_get_corrected_seo_lang_code($lang_code));
	}
}

function fn_seo_update_product($product_data, $product_id, $lang_code)
{
	if (isset($product_data['seo_name'])) {
		fn_create_seo_name($product_id, 'p', (!empty($product_data['seo_name'])) ? $product_data['seo_name'] : $product_data['product'], 0, '', fn_get_corrected_seo_lang_code($lang_code));
	}
}

function fn_seo_update_page($page_data, $page_id, $lang_code)
{
	if (!empty($page_data['page']) && !empty($page_id)) {  // Checking for required fields for new page
		$object_name = (!empty($page_data['seo_name'])) ? $page_data['seo_name'] : $page_data['page'];
		fn_create_seo_name($page_id, 'a', $object_name, 0, '', fn_get_corrected_seo_lang_code($lang_code));
	}
}

function fn_seo_update_product_feature($feature_data, $feature_id, $deleted_variants, $lang_code)
{
	if ($feature_data['feature_type'] == 'E' && !empty($feature_data['variants'])) {
		foreach ($feature_data['variants'] as $v) {
			if (!empty($v['variant_id'])) {
				fn_create_seo_name($v['variant_id'], 'e', (!empty($v['seo_name'])) ? $v['seo_name'] : $v['variant'], 0, '', fn_get_corrected_seo_lang_code($lang_code));
			}
		}

		if (!empty($deleted_variants)) {
			db_query("DELETE FROM ?:seo_names WHERE object_id IN (?n) AND type = ?s AND dispatch = ''", $deleted_variants, 'e');
		}
	}
}

function fn_seo_get_product_feature_variants(&$fields, &$join, &$condition, $group_by, $sorting, $lang_code)
{
	$fields[] = '?:seo_names.name as seo_name';
	$join .= db_quote(" LEFT JOIN ?:seo_names ON ?:seo_names.object_id = ?:product_feature_variants.variant_id AND ?:seo_names.type = 'e' AND ?:seo_names.dispatch = '' AND ?:seo_names.lang_code = ?s", fn_get_corrected_seo_lang_code($lang_code));
}

function fn_seo_get_news_data(&$fields, &$join, &$condition, $lang_code)
{
	$fields[] = '?:seo_names.name as seo_name';
	$join .= db_quote(" LEFT JOIN ?:seo_names ON ?:seo_names.object_id = ?:news.news_id AND ?:seo_names.type = 'n' AND ?:seo_names.dispatch = '' AND ?:seo_names.lang_code = ?s", fn_get_corrected_seo_lang_code($lang_code));
}

function fn_seo_update_news($news_data, $news_id, $lang_code)
{
	if (!empty($news_data['news']) && !empty($news_id)) {
		$object_name = (!empty($news_data['seo_name'])) ? $news_data['seo_name'] : $news_data['news'];
		fn_create_seo_name($news_id, 'n', $object_name, 0, '', fn_get_corrected_seo_lang_code($lang_code));
	}
}

function fn_seo_validate_object($seo, $path, $objects)
{
	$result = true;
	if (Registry::get('addons.seo.single_url') == 'Y' && $seo['lang_code'] != Registry::get('settings.Appearance.customer_default_language')) {
		return false;
	}

	if (!empty($objects['sl']) && strtoupper($objects['sl']) != $seo['lang_code'] && Registry::get('addons.seo.single_url') != 'Y') {
		return false;
	}

	$path = substr($path, strlen((defined('HTTPS') ? Registry::get('config.https_path') : Registry::get('config.http_path'))) + 1); // remove path prefix
	$path = substr_replace($path, '', strrpos($path, $objects['object_name'])); // remove object from path

	if (Registry::get('addons.seo.html_catalog') == 'Y') {
		$path = substr($path, strlen('catalog') + 1); // remove catalog prefix
	}

	if (Registry::get('addons.seo.seo_language') == 'Y') {
		$path = substr($path, 3); // remove language prefix
		
	}

	$path = rtrim($path, '/'); // remove trailing slash

	// check parent objects
	$vars = fn_get_seo_vars($seo['type']);
	$id_path = '';
	if ($seo['type'] == 'p') {
		if (Registry::get('addons.seo.seo_product_type') == 'product_category') {
			$id_path = db_get_field("SELECT id_path FROM ?:categories as c LEFT JOIN ?:products_categories as p ON p.category_id = c.category_id WHERE p.product_id = ?i AND p.link_type = 'M'", $seo['object_id']);
		}
		$result = fn_seo_validate_parents($path, $id_path, 'c', false, $seo['lang_code']);

	} elseif ($seo['type'] == 'c') {
		$id_path = db_get_field("SELECT id_path FROM ?:categories WHERE category_id = ?i AND parent_id != 0", $seo['object_id']);
		$result = Registry::get('addons.seo.seo_category_type') == 'root_category' ? true : fn_seo_validate_parents($path, $id_path, 'c', true, $seo['lang_code']);
	} elseif ($seo['type'] == 'a') {
		if (Registry::get('addons.seo.seo_product_type') == 'product_category') {
			$id_path = db_get_field("SELECT id_path FROM ?:pages WHERE page_id = ?i AND parent_id != 0", $seo['object_id']);
		}
		$result = fn_seo_validate_parents($path, $id_path, 'a', true, $seo['lang_code']);
	}

	// check for .html extension for the current object
	if ((in_array($seo['type'], array('p', 'a')) && empty($objects['extension'])) || ($seo['type'] == 'c' && Registry::get('addons.seo.seo_category_type') != 'file' && !empty($objects['extension']))) {
		$result = false;
	}

	fn_set_hook('validate_sef_object', $path, $seo, $vars, $result, $objects);

	return $result;
}

function fn_seo_validate_parents($path, $id_path, $parent_type, $trim_last = false, $lang_code = CART_LANGUAGE)
{
	$result = true;

	if (!empty($id_path)) {
		if ($trim_last == true) {
			$id_path = explode('/', $id_path);
			array_pop($id_path);
		}

		$parent_names = explode('/', $path);
		$parent_ids = is_array($id_path) ? $id_path : explode('/', $id_path);

		if (count($parent_ids) == count($parent_names)) {
			$parents = db_get_hash_single_array("SELECT object_id, name FROM ?:seo_names WHERE name IN (?a) AND type = ?s AND lang_code = ?s", array('object_id', 'name'), $parent_names, $parent_type, $lang_code);

			foreach ($parent_ids as $k => $id) {
				if (empty($parents[$id]) || $parent_names[$k] != $parents[$id]) {
					$result = false;
					break;
				}
			}
		} else {
			$result = false;
		}
	} elseif (!empty($path)) { // if we have no parents, but some was passed via URL
		$result = false;
	}

	return $result;
}

function fn_seo_html_catalog_info()
{
	$index_script = INDEX_SCRIPT;
	$text = fn_get_lang_var('text_seo_html_catalog_notice');

	return str_replace('[url]', "$index_script?dispatch=html_catalog.clean_up&cc", $text);
}

/**
 * Define whether current page should be indexed
 * 
 * $indexed_dispatches's element structure: 
 * 'dipatch' => array( 'index' => array('param1','param2'),
 * 						'noindex' => array('param3'),
 * 					)
 * the page can be indexed only if the current dispatch is in keys of the $indexed_dispatches array.
 * If so, the page is indexed only if param1 and param2 are the keys of the $_REQUEST array and param3 is not.
 * @param array $request
 * @return bool $index_page  indicate whether indexed or not
 */
function fn_seo_is_indexed_page($request)
{
	if (defined('HTTPS')) {
		return false;
	}
	
	$indexed_dispatches = array(
		'index.index' => array(),
		'sitemap.view' => array(),
		'products.view' => array('index' => array('product_id')),
		'categories.catalog'  => array(),
		'categories.view' => array(
			'index' => array('category_id'),
			'noindex' => array() // replace array() with array('features_hash') for the system not to index the feature filters' pages on a category page
		),
		'pages.view' => array('index' => array('page_id'))
	);
	
	fn_set_hook('seo_is_indexed_page', $indexed_dispatches);
	$index_page = false;
	if (isset($indexed_dispatches[CONTROLLER . '.' . MODE]) && is_array($indexed_dispatches[CONTROLLER . '.' . MODE])) {
		
		$_dispatch = $indexed_dispatches[CONTROLLER . '.' . MODE];
		
		if (empty($_dispatch['index']) && empty($_dispatch['noindex'])) {
			$index_page = true;
		} else {
			$index_cond = true;
			if (!empty($_dispatch['index']) && is_array($_dispatch['index'])) {
				$index_cond = false;
				if (sizeof(array_intersect($_dispatch['index'], array_keys($request))) == sizeof($_dispatch['index'])) {
					$index_cond = true;
				}
			}
			
			$noindex_cond = true;
			if (!empty($_dispatch['noindex']) && is_array($_dispatch['noindex'])) {
				$noindex_cond = false;
				if (sizeof(array_intersect($_dispatch['noindex'], array_keys($request))) == 0) {
					$noindex_cond = true;
				}
			}
			$index_page = $index_cond && $noindex_cond;
		}
	}
	
	return $index_page;
}
/**
 * Create cache for static items
 * @param string $lang_code language code
 * @return boolean always true
*/
function fn_seo_cache_static_create($lang_code = CART_LANGUAGE)
{
	Registry::register_cache('seo', array('seo_names'), $lang_code);
	// Get and cache names for pages, extended features and static names
	if (Registry::is_exist('seo') == false) {
		$object_types = array(
			'a' => array('object_id', 'name'),
			's' => array('dispatch', 'name'),
			'e' => array('object_id', 'name'),
		);
		fn_set_hook('seo_static_cache', $object_types, $lang_code);
		foreach ($object_types as $k => $v) {
			$names = db_get_hash_single_array("SELECT ?p FROM ?:seo_names WHERE type = ?s AND lang_code = ?s", $v, implode(',', $v) , $k, $lang_code);
			Registry::set('seo.' . $k, $names);
		}
	}
	return true;
}

/**
 * Cache news names
 * @param array $news news
 * @return boolean always true
 */
function fn_seo_get_news_post($news)
{
	if (AREA == 'C') {
		foreach ($news as $k => $n) {
			fn_seo_cache_name('n', $n['news_id'], $n['seo_name'], CART_LANGUAGE);
		}
	}
	return true;
}

/**
 * Cache products names
 * @param array $products products
 * @return boolean always true
 */
function fn_seo_get_products_post($products)
{
	if (AREA == 'C') {
		foreach ($products as $k => $product) {
			fn_seo_cache_name('p', $product['product_id'], $product['seo_name'], CART_LANGUAGE);
		}
	}
	return true;
}

/**
 * Cache categories names
 * @param array $categories categories
 * @return boolean always true
 */
function fn_seo_get_categories_post($categories)
{
	if (AREA == 'C') {
		foreach ($categories as $k => $category) {
			fn_seo_cache_parent_items_path('c', $category['category_id'], $category['id_path']);
			fn_seo_cache_name('c', $category['category_id'], $category['seo_name'], CART_LANGUAGE);
		}
	}
	return true;
}

/**
 * Cache categories names and pathes
 * @param array $category_data category data
 * @return boolean always true
 */
function fn_seo_get_category_data_post($category_data)
{
	if (!empty($category_data)) {
		fn_seo_cache_parent_items_path('c', $category_data['category_id'], $category_data['id_path']);
		fn_seo_cache_name('c', $category_data['category_id'], $category_data['seo_name'], CART_LANGUAGE);
	}
	return true;
}

/**
 * Cache parent items path of names for seo object
 * @param string $object_type object type of seo object
 * @param string $object_id object id of seo object
 * @param string $id_path id path for seo object
 * @return boolean always true
 */
function fn_seo_cache_parent_items_path($object_type, $object_id, $id_path)
{
	$reg = Registry::get("runtime.seo.parent_items");
	$reg[$object_type][$object_id] = $id_path;
	Registry::set("runtime.seo.parent_items", $reg);
	return true;
}

/**
 * Get parent items path of names for seo object
 * @param string $object_type object type of seo object
 * @param string $object_id object id of seo object
 * @param boolean $is_pop - skip current object name
 * @param string $lang_code language code
 * @return array parent items path of names
 */
function fn_seo_get_parent_items_path($object_type, $object_id, $is_pop = false, $lang_code = CART_LANGUAGE)
{
	$id_path = Registry::get('runtime.seo.parent_items.' . $object_type);
	$id_path = isset($id_path[$object_id])? $id_path[$object_id] : false;

 	if (empty($id_path)) {
		if ($object_type == 'p') {
			$id_path = db_get_field("SELECT c.id_path FROM ?:products_categories as pc LEFT JOIN ?:categories as c ON pc.category_id = c.category_id WHERE pc.product_id = ?i AND pc.link_type = 'M'", $object_id);
 		} elseif ($object_type == 'c') {
			$id_path = db_get_field("SELECT id_path FROM ?:categories WHERE category_id = ?i", $object_id);
		} elseif ($object_type == 'a') {
			$id_path = db_get_field("SELECT id_path FROM ?:pages WHERE page_id = ?i", $object_id);
		}
		fn_set_hook('seo_get_parent_items_path', $object_type, $object_id, $id_path);
		fn_seo_cache_parent_items_path($object_type, $object_id, $id_path);
	}
	
	$parent_item_names = array();

	if (!empty($id_path)) {
		$path_ids = explode("/", $id_path);
		
		if ($is_pop) {
			array_pop($path_ids);
		}

		foreach ($path_ids as $v) {
			$object_type_for_name = ($object_type == 'p')? 'c' : $object_type;
			$parent_item_names[] = fn_seo_get_name($object_type_for_name, $v, '', $lang_code);
		}
		return $parent_item_names;
	}
	return array();
}

/**
 * Cache name for seo object
 * @param string $object_type object type of seo object
 * @param string $object_id object id of seo object
 * @param string $object_name  dispatch of seo object
 * @param string $lang_code language code
 * @return boolean always true
 */
function fn_seo_cache_name($object_type, $object_id, $object_name, $lang_code)
{
	$reg = Registry::get("runtime.seo");
	$reg[$lang_code][$object_type][$object_id] = $object_name;
	Registry::set("runtime.seo", $reg);
	return true;
}

/**
 * Get name for seo object
 * @param string $object_type object type of seo object
 * @param string $object_id object id of seo object
 * @param string $dispatch  dispatch of seo object
 * @param string $lang_code language code
 * @return string name for seo object
 */
function fn_seo_get_name($object_type, $object_id = 0, $dispatch = '', $lang_code = CART_LANGUAGE)
{
	$lang_code = fn_get_corrected_seo_lang_code($lang_code);
	$names = Registry::get('runtime.seo.' . $lang_code . '.' . $object_type);

	$name = isset($names[$object_id])? $names[$object_id] : false;
	$_object_id = !empty($object_id)? $object_id : $dispatch;
	
	if (empty($name)) {
		$names = Registry::get('seo');
		$name = (isset($names[$object_type][$_object_id]) && $lang_code == CART_LANGUAGE)? $names[$object_type][$_object_id] : false;

		if (empty($name) && !($object_type == 's' && $lang_code == CART_LANGUAGE)) {
			$where_params = array(
				'object_id'=> $object_id,
				'type'=> $object_type,
				'dispatch'=> $dispatch,
				'lang_code'=> $lang_code,
			);
			$name = db_get_field("SELECT name FROM ?:seo_names WHERE ?w", $where_params);

			if (empty($name)) {
				if ($object_type == 's') {
					$alt_name = db_get_field("SELECT name FROM ?:seo_names WHERE object_id = ?i AND type = ?s AND dispatch = ?s", $object_id, $object_type, $dispatch);
					if (!empty($alt_name)) {
						$name = fn_create_seo_name($object_id, $object_type, str_replace('.', '-', $dispatch), 0, $dispatch, $lang_code);
					}
				} else {
					$_seo = fn_get_seo_vars($object_type);

					$object_name = '';
					// Get object name from its descriptions
					if (!empty($_seo['table'])) {
						$object_name = db_get_field("SELECT $_seo[description] FROM $_seo[table] WHERE lang_code = ?s AND $_seo[item] = ?i", $lang_code, $object_id);
					}
					$name = fn_create_seo_name($object_id, $object_type, $object_name, 0, $dispatch, $lang_code);
				}
			}
		}

		fn_seo_cache_name($object_type, $_object_id, $name, $lang_code);
	}
	
	return $name;
}

/**
 * Create cache
 * @return boolean always true
 */
function fn_seo_before_dispatch()
{
	fn_seo_cache_static_create();
	return true;
}

/**
 * Get seo url
 * @param string $url url
 * @param string $area area for area
 * @param string $original_url original url from fn_url
 * @param string $lang_code language code
 * @return string seo url
 */
function fn_seo_url(&$url, $area = AREA, $delimeter, $original_url = '', $lang_code = CART_LANGUAGE)
{
	if ($area != 'C') {
		return $url;
	}

	$d = SEO_DELIMITER;
	$url = str_replace('&amp;', '&', $url);
	$parced_url = parse_url($url);

	$index_script = Registry::get('config.customer_index');
	$http_path = Registry::get('config.http_path');
	$https_path = Registry::get('config.https_path');

	$seo_settings = &Registry::get('addons.seo');

	$current_path = '';
	if (empty($parced_url['scheme'])) {
		$current_path = (defined('HTTPS')) ? $https_path . '/' : $http_path . '/';
	}

	$html_catalog = $html_catalog_slashed = '';
	/*
	$html_catalog = (Registry::get('addons.seo.html_catalog') == 'Y') ? 'catalog' : '';
	$html_catalog_slashed = (Registry::get('addons.seo.html_catalog') == 'Y') ? 'catalog/' : '';
	*/

	if (!empty($parced_url['scheme']) && ($parced_url['scheme'] != 'http' && $parced_url['scheme'] != 'https')) {
		return $url;  // This is no http/https url like mailto:, ftp:
	} elseif (!empty($parced_url['scheme'])) {
		if (!empty($parced_url['host']) && ($parced_url['host'] != Registry::get('config.http_host') && $parced_url['host'] != Registry::get('config.https_host'))) {
			return $url;  // This is external link
		} elseif (!empty($parced_url['path']) && ((!empty($http_path) && stripos($parced_url['path'], $http_path) === false) || (!empty($https_path) && stripos($parced_url['path'], $http_path) === false))) {
			return $url;  // This is external link
		} else {
			if (rtrim($url, '/') == Registry::get('config.http_location') || rtrim($url, '/') == Registry::get('config.https_location')) {
				$url = rtrim($url, '/') . "/" . $index_script;
				$parced_url['path'] = rtrim($parced_url['path'], '/') . "/" . $index_script;
			}
		}
	}

	if (!empty($parced_url['path']) && empty($parced_url['query']) && $parced_url['path'] == $index_script) {
		$url = $html_catalog . $current_path . ((Registry::get('addons.seo.seo_language') == 'Y')?  strtolower($lang_code) . '/' : '');
		return $url;
	}

	$path = str_replace($index_script, $html_catalog_slashed, $parced_url['path'], $count);

	if ($count == 0) {
		return $url; // This is currently seo link
	}

	$link_parts = array(
		'scheme' => !empty($parced_url['scheme']) ? $parced_url['scheme'].'://' : '',
		'host'   => !empty($parced_url['host']) ? $parced_url['host'] : '',
		'path'   => $current_path . $path,
		'lang_code' => (Registry::get('addons.seo.seo_language') == 'Y')? strtolower($lang_code) . '/' : '',
		'parent_items_names' => '',
		'name'   => '',
		'page'   => '',
		'extension' => '',
		'fragment'  => !empty($parced_url['fragment']) ? '#'.$parced_url['fragment'] : '',
	);

	$parced_query = array();
	$lang_code_is_unset = false;
	if (!empty($parced_url['query'])) {
		parse_str($parced_url['query'], $parced_query);

		if (!empty($parced_query['sl'])) {
			$lang_code = strtoupper($parced_query['sl']);

			if (Registry::get('addons.seo.single_url') != 'Y') {
				$unset_lang_code = $parced_query['sl'];
				unset($parced_query['sl']);
			}

			if (Registry::get('addons.seo.seo_language') == 'Y') {
				$link_parts['lang_code'] = strtolower($lang_code) . '/';
				$unset_lang_code = isset($parced_query['sl'])? $parced_query['sl'] : $unset_lang_code;
				unset($parced_query['sl']);
			}
		}

		$lang_code = fn_get_corrected_seo_lang_code($lang_code);
		if (!empty($parced_query['dispatch']) && is_string($parced_query['dispatch'])) {

			if (!empty($original_url) && (stripos($parced_query['dispatch'], '/') !== false || substr($parced_query['dispatch'], -1 * strlen(SEO_FILENAME_EXTENSION)) == SEO_FILENAME_EXTENSION)) {
				$url = $original_url;
				return $url; // This is currently seo link
			}

			// Convert products links
			if ($parced_query['dispatch'] == 'products.view' && !empty($parced_query['product_id'])) {
				if ($seo_settings['seo_product_type'] == 'product_category') {
					$parent_item_names = fn_seo_get_parent_items_path('p', $parced_query['product_id'], false, $lang_code);
					$link_parts['parent_items_names'] = !empty($parent_item_names)? join('/', $parent_item_names) . "/" : "";
				}

				$link_parts['name'] = fn_seo_get_name('p', $parced_query['product_id'], '', $lang_code);
				$link_parts['extension'] = SEO_FILENAME_EXTENSION;
				
				fn_seo_parced_query_unset($parced_query, 'product_id');

			// Convert categories links
			} elseif ($parced_query['dispatch'] == 'categories.view' && !empty($parced_query['category_id'])) {
				if ($seo_settings['seo_category_type'] != 'root_category') {
					$parent_item_names = fn_seo_get_parent_items_path('c', $parced_query['category_id'], true, $lang_code);
					$link_parts['parent_items_names'] = !empty($parent_item_names)? join('/', $parent_item_names) . "/" : "";
				}

				$link_parts['name'] = fn_seo_get_name('c', $parced_query['category_id'], '', $lang_code);

				$page = isset($parced_query['page'])? $parced_query['page'] : 0;
				if ($seo_settings['seo_category_type'] != 'file') {
					$link_parts['name'] .= '/';
					if (!empty($page) &&  $page != '1') {
						$link_parts['name'] .= 'page' . $d . $page . '/';
					}
					unset($parced_query['page']);
				} else {
					$link_parts['extension'] = SEO_FILENAME_EXTENSION;
					if (!empty($page) && $page != '1') {
						$link_parts['name'] .= $d . 'page' . $d . $page;
					}
					unset($parced_query['page']);
				}

				fn_seo_parced_query_unset($parced_query, 'category_id');

			//Convert pages links
			} elseif ($parced_query['dispatch'] == 'pages.view' && !empty($parced_query['page_id'])) {
				
				if ($seo_settings['seo_product_type'] == 'product_category') {
					$parent_item_names = fn_seo_get_parent_items_path('a', $parced_query['page_id'], true, $lang_code);
					$link_parts['parent_items_names'] = !empty($parent_item_names)? join('/', $parent_item_names) . "/" : "";
				}

				$link_parts['name'] = fn_seo_get_name('a', $parced_query['page_id'], '', $lang_code);
				$link_parts['extension'] = SEO_FILENAME_EXTENSION;

				fn_seo_parced_query_unset($parced_query, 'page_id');

			// Convert extended features links
			} elseif ($parced_query['dispatch'] == 'product_features.view' && !empty($parced_query['variant_id'])) {

				$link_parts['name'] = fn_seo_get_name('e', $parced_query['variant_id'], '', $lang_code);
				$link_parts['extension'] = SEO_FILENAME_EXTENSION;

				fn_seo_parced_query_unset($parced_query, 'variant_id');

			// Other conversions
			} else {
				fn_set_hook('seo_url', $seo_settings, $url, $parced_url, $link_parts, $parced_query, $lang_code);
				// Convert static links
				if (empty($link_parts['name'])) {
					$name = fn_seo_get_name('s', 0, $parced_query['dispatch'], $lang_code);
					if (!empty($name)) {
						$link_parts['name'] = $name;
						fn_seo_parced_query_unset($parced_query);
					} else {
						// for non-rewritten links
						$link_parts['path'] .= $index_script;
						$link_parts['lang_code'] = '';
						if (!empty($unset_lang_code)) {
							$parced_query['sl'] = $unset_lang_code;
						}
					}
				}
			}
		} elseif (Registry::get('addons.seo.seo_language') != 'Y' && !empty($unset_lang_code)) {
			$parced_query['sl'] = $unset_lang_code;
		}
	}

	$url = join('', $link_parts);
	if (!empty($parced_query)) {
		$url .= '?' . http_build_query($parced_query, '', $delimeter);
	}
	

	return $url;
}

/**
 * Unset some keys in parced_query array
 * @param array $parts_array link parts
 * @param mixed $keys keys for unseting
 * @return string name for seo object
 */
function fn_seo_parced_query_unset(&$parts_array, $keys = array())
{
	$keys = is_array($keys)? $keys : array($keys);
	$keys[] = 'dispatch';

	foreach ($keys as $v) {
		unset($parts_array[$v]);
	}

	return true;
}

?>
