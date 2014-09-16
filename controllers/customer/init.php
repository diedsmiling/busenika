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
// $Id: init.php 10512 2010-08-25 14:35:32Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$view->assign('index_script', $index_script);
$view_mail->assign('index_script', $index_script);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	return;
}

//
// Check if store is closed
//
if (Registry::get('settings.store_mode') == 'closed') {
	if (!empty($_REQUEST['store_access_key'])) {
		$_SESSION['store_access_key'] = $_GET['store_access_key'];
	}
	var_dump($_SESSION['store_access_key']);
	if (empty($_SESSION['store_access_key']) || $_SESSION['store_access_key'] != Registry::get('settings.General.store_access_key')) {
		return array(CONTROLLER_STATUS_REDIRECT, Registry::get('config.current_location') . '/store_closed.html');
	}
}

if (empty($_REQUEST['product_id']) && empty($_REQUEST['category_id'])) {
	unset($_SESSION['current_category_id']);
}

fn_add_breadcrumb(fn_get_lang_var('home'), $index_script);

$request_params = $_REQUEST;
$request_params['location'] = fn_get_blocks_location(CONTROLLER);
list($blocks) = fn_get_blocks($request_params);
$view->assign('blocks', $blocks);
$view->assign('location_data', fn_get_location_data($request_params['location'], true));

// Get quick links
Registry::register_cache('quick_links', array('static_data'), CACHE_LEVEL_LOCALE);
if (Registry::is_exist('quick_links') == false) {
	Registry::set('quick_links', fn_get_static_data_section('N'));
}

// Get top menu
Registry::register_cache('top_menu', array('static_data', 'categories', 'pages'), CACHE_LEVEL_LOCALE_AUTH);
if (Registry::is_exist('top_menu') == false) {
	Registry::set('top_menu', fn_top_menu_form(fn_get_static_data_section('A', true)));
}

$quick_links = & Registry::get('quick_links');
$top_menu = & Registry::get('top_menu');

$top_menu = fn_top_menu_select($top_menu, $controller, $mode, Registry::get('current_url'));

// Init cart if not set
if (empty($_SESSION['cart'])) {
	fn_clear_cart($_SESSION['cart']);
}

// Display products in comparison list
if (!empty($_SESSION['comparison_list'])) {
	$compared_products = array();
	$_products = db_get_hash_array("SELECT product_id, product FROM ?:product_descriptions WHERE product_id IN (?n) AND lang_code = ?s", 'product_id', $_SESSION['comparison_list'], CART_LANGUAGE);
	foreach ($_SESSION['comparison_list'] as $k => $p_id) {
		if (empty($_products[$p_id])) {
			unset($_SESSION['comparison_list'][$k]);
			continue;
		}
		$compared_products[] = $_products[$p_id];
	}
	$view->assign('compared_products', $compared_products);
}

$view->assign('quick_links', $quick_links);
$view->assign('top_menu', $top_menu);

/**
 * Form top menu
 *
 * @param array $top_menu top menu data from the database
 * @return array formed top menu
 */
function fn_top_menu_form($top_menu)
{
	foreach ($top_menu as $k => $v) {
		if (!empty($v['param_3'])) { // get extra items
			list($type, $id, $use_name) = fn_explode(':', $v['param_3']);
			if ($type == 'C') { // categories
				$cats = fn_get_categories_tree($id, true);
				$v['subitems'] = fn_array_merge(fn_top_menu_standardize($cats, 'category_id', 'category', 'subcategories', 'categories.view?category_id=', $v['param_4']), !empty($v['subitems']) ? $v['subitems'] : array(), false);

				if ($use_name == 'Y' && !empty($id)) {
					$v['descr'] = fn_get_category_name($id);
					$v['param'] = 'categories.view?category_id=' . $id;
				}
			} elseif ($type == 'A') { // pages
				$params = array(
					'from_page_id' => $id,
					'get_tree' => 'multi_level',
					'status' => 'A'
				);
				list($pages) = fn_get_pages($params);

				$v['subitems'] = fn_array_merge(fn_top_menu_standardize($pages, 'page_id', 'page', 'subpages', 'pages.view?page_id=', $v['param_4']), !empty($v['subitems']) ? $v['subitems'] : array(), false);

				if ($use_name == 'Y' && !empty($id)) {
					$v['descr'] = fn_get_page_name($id);
					$v['param'] = 'pages.view?page_id=' . $id;
				}
			} else { // for addons
				fn_set_hook('top_menu_form', $v, $type, $id, $use_name);
			}
		}

		if (!empty($v['subitems'])) {
			$top_menu[$k]['subitems'] = fn_top_menu_form($v['subitems']);
		}

		$top_menu[$k]['item'] = $v['descr'];
		$top_menu[$k]['href'] = $v['param'];

		unset($top_menu[$k]['descr'], $top_menu[$k]['param']);
	}

	return $top_menu;
}

/**
 * Select active tab in top menu
 *
 * @param array $top_menu top menu data from the database
 * @param string $controller current controller
 * @param string $mode current mode
 * @param string $current_url current URL
 * @param mixed $child_key key of selected child
 * @return array formed top menu
 */
function fn_top_menu_select($top_menu, $controller, $mode, $current_url, &$child_key = NULL)
{
	$selected_key = NULL;
	foreach ($top_menu as $k => $v) {
		if (!empty($v['param_2'])) { // get currently selected item
			$d = fn_explode(',', $v['param_2']);
			foreach ($d as $p) {
				if (strpos($p, '.') !== false) {
					list($c, $m) = fn_explode('.', $p);
				} else {
					$c = $p;
					$m = '';
				}

				if ($controller == $c && (empty($m) || $m == $mode)) {
					$selected_key = $k;
				}
			}
		} elseif (!empty($v['href'])) { // if url is not empty, get selected tab by it
			parse_str(substr($v['href'], strpos($v['href'], '?') + 1), $a);

			$equal = true;
			foreach ($a as $_k => $_v) {
				if (!isset($_REQUEST[$_k]) || $_REQUEST[$_k] != $_v) {
					$equal = false;
					break;
				}
			}

			if ($equal == true) {
				$selected_key = $k;
			}
		}

		if ($selected_key === NULL && !empty($v['subitems'])) {
			$c_key = NULL;
			$top_menu[$k]['subitems'] = fn_top_menu_select($v['subitems'], $controller, $mode, $current_url, $c_key);

			if ($c_key !== NULL) {
				$selected_key = $k;
			}
		}

		if ($selected_key !== NULL) {
			$top_menu[$selected_key]['selected'] = true;
			$child_key = true;
			break;
		}
	}

	return $top_menu;
}

/**
 * Standardize data for usage in top menu
 *
 * @param array $items data to standartize
 * @param string $id_name key with item ID
 * @param string $name key with item name
 * @param string $children_name key with subitems
 * @param string $href_prefix URL prefix
 * @return array standardized data
 */
function fn_top_menu_standardize($items, $id_name, $name, $children_name, $href_prefix, $dir)
{
	$result = array();
	foreach ($items as $v) {
		$result[$v[$id_name]] = array(
			'descr' => $v[$name],
			'param' => empty($v['link']) ? $href_prefix . $v[$id_name] : $v['link'],
			'param_4' => $dir,
			'new_window' => isset($v['new_window']) ? $v['new_window'] : 0
		);

		if (!empty($v[$children_name])) {
			$result[$v[$id_name]]['subitems'] = fn_top_menu_standardize($v[$children_name], $id_name, $name, $children_name, $href_prefix, $dir);
		}
	}

	return $result;
}

?>
