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
// $Id: fn.cms.php 10558 2010-08-31 12:50:29Z klerik $
//

if ( !defined('AREA') )	{ die('Access denied');	}

// basic cms page types
define('PAGE_TYPE_LINK', 'L');
define('PAGE_TYPE_TEXT', 'T');


//
// Search pages by set of params
// Returns array(array of pages, params)
//
function fn_get_pages($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
	// Init filter
	$params = fn_init_view('pages', $params);

	$default_params = array(
		'page_id' => 0,
		'page' => 1,
		'visible' => false,
		'get_tree' => '',
		'items_per_page' => 0,
		'pdescr' => '',
		'subpages' => ''
	);

	$params = array_merge($default_params, $params);

	if (empty($params['pname']) && empty($params['pdescr']) && empty($params['subpages'])) {
		$params['pname'] = 'Y';
	}

	$fields = array (
		'?:pages.*',
		'?:page_descriptions.*'
	);

	// Define sort fields
	$sortings = array (
		'position' => array (
			'?:pages.position',
			'?:page_descriptions.page',
		),
        'name' => '?:page_descriptions.page',
        'timestamp' => '?:pages.timestamp',
        'type' => '?:pages.page_type',
		'multi_level' => array (
			'?:pages.parent_id',
			'?:pages.position',
			'?:page_descriptions.page',
		),
    );

    $directions = array (
        'asc' => 'asc',
        'desc' => 'desc'
    );

	$auth = & $_SESSION['auth'];

	$condition = '1';
	$join = $limit = $group_by = '';

	if (isset($params['q']) && fn_string_no_empty($params['q'])) {

		$params['q'] = trim($params['q']);
		if ($params['match'] == 'any') {
			$pieces = fn_explode(' ', $params['q']);
			$search_type = ' OR ';
		} elseif ($params['match'] == 'all') {
			$pieces = fn_explode(' ', $params['q']);
			$search_type = ' AND ';
		} else {
			$pieces = array($params['q']);
			$search_type = '';
		}

		$_condition = array();
		foreach ($pieces as $piece) {
			if (strlen($piece) == 0) {
				continue;
			}

			$tmp = array();
			if (!empty($params['pname']) && $params['pname'] == 'Y') {
				$tmp[] = db_quote("(?:page_descriptions.page LIKE ?l)", "%$piece%"); // check search words
			}

			if ($params['pdescr'] == 'Y') {
				$tmp[] = db_quote("?:page_descriptions.description LIKE ?l", "%$piece%");
			}

			if (!empty($tmp)) {
				$_condition[] = '(' . implode(' OR ', $tmp) . ')';
			}
		}
		if (!empty($_condition)) {
			$condition .= ' AND ' . implode($search_type, $_condition);
		}
	}

	$condition .= fn_get_company_condition('?:pages.company_id');

	if (!empty($params['page_type'])) {
		$condition .= db_quote(" AND ?:pages.page_type = ?s", $params['page_type']);
	}

	if (isset($params['parent_id']) && $params['parent_id'] !== '') {
		$p_ids = array();
		if ($params['subpages'] == 'Y') {
			$p_ids = db_get_fields("SELECT a.page_id FROM ?:pages as a LEFT JOIN ?:pages as b ON b.page_id = ?i WHERE a.id_path LIKE CONCAT(b.id_path, '/%')", $params['parent_id']);
		}
		$p_ids[] = $params['parent_id'];

		$condition .= db_quote(" AND ?:pages.parent_id IN (?n)", $p_ids);
	}

	if (!empty($params['from_page_id'])) {
		$from_id_path = db_get_field("SELECT id_path FROM ?:pages WHERE page_id = ?i", $params['from_page_id']);
		$condition .= db_quote(" AND ?:pages.id_path LIKE ?l", "$from_id_path/%");
	}

	if (!empty($params['status'])) {
		$condition .= db_quote(" AND ?:pages.status IN (?a)", $params['status']);
	}

	if (!empty($params['vendor_pages']) && empty($params['company_id'])) {
		return array(array(), $params);
	} elseif (!empty($params['company_id'])) {
		$condition .= db_quote(" AND ?:pages.company_id = ?i", $params['company_id']);
	}

	if (!empty($params['visible'])) {  // for pages tree: show visible branch only
		if (!empty($params['current_page_id'])) {
			$cur_id_path = db_get_field("SELECT id_path FROM ?:pages WHERE page_id = ?i", $params['current_page_id']);
			if (!empty($cur_id_path)) {
				$page_ids = explode('/', $cur_id_path);
			}
		}

		$page_ids[] = $params['page_id'];
		$condition .= db_quote(" AND ?:pages.parent_id IN (?n)", $page_ids);
	}

	if (!empty($params['period']) && $params['period'] != 'A') {
		list($params['time_from'], $params['time_to']) = fn_create_periods($params);
		$condition .= db_quote(" AND (?:pages.timestamp >= ?i AND ?:pages.timestamp <= ?i)", $params['time_from'], $params['time_to']);
	}

	if (!empty($params['item_ids'])) { // get only defined pages
		$condition .= db_quote(" AND ?:pages.page_id IN (?n)", explode(',', $params['item_ids']));
	}

	if (!empty($params['except_id']) && (empty($params['item_ids']) || !empty($params['item_ids']) && !in_array($params['except_id'], explode(',', $params['item_ids'])))) {
		$condition .= db_quote(' AND ?:pages.page_id != ?i AND ?:pages.parent_id != ?i', $params['except_id'], $params['except_id']);
	}

	if (AREA != 'A') {
		$condition .= " AND (" . fn_find_array_in_set($auth['usergroup_ids'], '?:pages.usergroup_ids', true) . ")";
		$condition .= fn_get_localizations_condition('?:pages.localization', true);
		$condition .= db_quote(" AND (use_avail_period = ?s OR (use_avail_period = ?s AND avail_from_timestamp >= ?i AND avail_till_timestamp <= ?i))", 'N', 'Y', TIME, TIME);
	}

	$join = db_quote('LEFT JOIN ?:page_descriptions ON ?:pages.page_id = ?:page_descriptions.page_id AND ?:page_descriptions.lang_code = ?s', $lang_code);

	if (!empty($params['b_id'])) {
		$join .= " LEFT JOIN ?:block_links ON ?:block_links.object_id = ?:pages.page_id AND ?:block_links.location = 'pages'";
		$condition .= db_quote(' AND ?:block_links.block_id = ?i', $params['b_id']);
	}

	if (!empty($params['limit'])) {
		$limit = db_quote(" LIMIT 0, ?i", $params['limit']);
	}

	fn_set_hook('get_pages', $params, $join, $condition, $fields, $group_by, $sortings);

	if (!empty($params['get_tree'])) {
		$params['sort_by'] = 'multi_level';
	}

    if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
        $params['sort_order'] = 'asc';
    }

    if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
        $params['sort_by'] = 'position';
    }

	$sorting = (is_array($sortings[$params['sort_by']]) ? implode(' ' . $directions[$params['sort_order']] . ', ', $sortings[$params['sort_by']]) : $sortings[$params['sort_by']]) . " " . $directions[$params['sort_order']];

    if (!empty($group_by)) {
    	$group_by = ' GROUP BY ' . $group_by;
    }

	// Reverse sorting (for usage in view)
	$params['sort_order'] = $params['sort_order'] == 'asc' ? 'desc' : 'asc';

	// Get search conditions
	if (!empty($params['get_conditions'])) {
		return array($fields, $join, $condition);
	}

	$total = 0;
	if (!empty($items_per_page) && !empty($params['paginate'])) {
		$total = db_get_field("SELECT COUNT(DISTINCT(?:pages.page_id)) FROM ?:pages ?p WHERE ?p ?p ORDER BY ?p", $join, $condition, $group_by, $sorting);
		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}

	$pages = db_get_hash_array("SELECT " . implode(', ', $fields) ." FROM ?:pages ?p WHERE ?p ?p ORDER BY ?p ?p", 'page_id', $join, $condition, $group_by, $sorting, $limit);

	if (!empty($pages)) {
		foreach ($pages as $k => $v) {
			$pages[$k]['level'] = substr_count($v['id_path'], '/');
		}

		if (!empty($params['get_tree'])) {
			$delete_keys = array();
			foreach ($pages as $k => $v) {
				if (!empty($v['parent_id']) && !empty($pages[$v['parent_id']])) {
					$pages[$v['parent_id']]['subpages'][$v['page_id']] = & $pages[$k];
					$delete_keys[] = $k;
				}

				if (!empty($v['parent_id']) && ((!isset($params['root_id']) && empty($pages[$v['parent_id']])) || (isset($params['root_id']) && $v['parent_id'] != $params['root_id'])) && (empty($params['from_page_id']) || $params['from_page_id'] != $v['parent_id'])) { // delete pages that don't have parent. FIXME: should be done on database layer
					$delete_keys[] = $k;
				}
			}

			foreach ($delete_keys as $k) {
				unset($pages[$k]);
			}
		} elseif (!empty($params['item_ids'])) {
			$pages = fn_sort_by_ids($pages, explode(',', $params['item_ids']), 'page_id');
		}

		if ($params['get_tree'] == 'plain') {
			$pages = fn_multi_level_to_plain($pages, 'subpages');
		}

		if (!empty($params['get_children_count'])) {
			$where_condition = !empty($params['except_id']) ? db_quote(' AND page_id != ?i', $params['except_id']) : '';
			if ($params['get_tree'] == 'plain') {
				$_page_ids = array();
				foreach ($pages as $_p) {
					$_page_ids[] = $_p['page_id'];
				}
			} else {
				$_page_ids = array_keys($pages);
			}
			$children = db_get_hash_single_array("SELECT parent_id, COUNT(page_id) as children FROM ?:pages WHERE parent_id IN (?n) ?p GROUP BY parent_id", array('parent_id', 'children'), $_page_ids, $where_condition);

			if (!empty($children)) {
				if ($params['get_tree'] == 'plain') {
					foreach ($pages as $_id => $_p) {
						if (!empty($children[$_p['page_id']])) {
							$pages[$_id]['has_children'] = true;
						}
					}
				} else {
					foreach ($children as $k => $v) {
						$pages[$k]['has_children'] = !empty($v);
					}
				}
			}
		}
	}

	if (!empty($params['add_root'])) {
		array_unshift($pages, array('page_id' => 0, 'page' => $params['add_root']));
	}

	fn_set_hook('post_get_pages', $pages, $params, $lang_code);

	return array($pages, $params);
}

function fn_get_page_data($page_id, $lang_code = CART_LANGUAGE, $preview = false)
{
	static $cache = array();

	if (empty($page_id)) {
		return false;
	}

	if (empty($cache[$page_id])) {
		$condition = '';

		$condition .= fn_get_company_condition('?:pages.company_id');

		if (AREA != 'A') {
			$condition .= " AND (" . fn_find_array_in_set($_SESSION['auth']['usergroup_ids'], '?:pages.usergroup_ids', true) . ")";
		}
		$cache[$page_id] = db_get_row("SELECT * FROM ?:pages INNER JOIN ?:page_descriptions ON ?:pages.page_id = ?:page_descriptions.page_id WHERE ?:pages.page_id = ?i AND ?:page_descriptions.lang_code = ?s ?p", $page_id, $lang_code, $condition);
		if (empty($cache[$page_id]) || (AREA != 'A' && ($cache[$page_id]['status'] == 'D' || $cache[$page_id]['use_avail_period'] == 'Y' && ($cache[$page_id]['avail_from_timestamp'] > TIME || $cache[$page_id]['avail_till_timestamp'] < TIME))) && empty($preview)) {
			return false;
		}
		fn_set_hook('get_page_data', $cache[$page_id], $lang_code);

		// Generate meta description automatically
		if (empty($cache[$page_id]['meta_description']) && defined('AUTO_META_DESCRIPTION') && AREA != 'A') {
			$cache[$page_id]['meta_description'] = fn_generate_meta_description($cache[$page_id]['description']);
		}
	}

	return (!empty($cache[$page_id]) ? $cache[$page_id] : false);
}

function fn_get_page_name($page_id, $lang_code = CART_LANGUAGE)
{
	if (!empty($page_id)) {
		if (is_array($page_id)) {
			return db_get_hash_single_array("SELECT page_id, page FROM ?:page_descriptions WHERE page_id IN (?n) AND lang_code = ?s", array('page_id', 'page'), $page_id, $lang_code);
		} else {
			return db_get_field("SELECT page FROM ?:page_descriptions WHERE page_id = ?i AND lang_code = ?s", $page_id, $lang_code);
		}
	}

	return false;
}

/** Block manager **/

function fn_get_block_properties($structure_section = '')
{
	static $schema;

	if (!isset($schema)) {

		$schema = fn_get_schema('block_manager', 'structure');

		if (AREA == 'A') {
			foreach ($schema as $k => $v) {
				foreach (array('fillings', 'positions', 'appearances', 'order') as $section_name) {
					if (!empty($v[$section_name])) {
						$_tmp = array();
						foreach ($v[$section_name] as $key => $val) {
							if (is_array($val) == true) {
								$_tmp[$key] = $val;
								$_tmp[$key]['name'] = ($section_name == 'appearances') ? fn_get_block_template_description($key) : fn_get_lang_var($key);
							} else {
								$_tmp[$val] = ($section_name == 'appearances') ? fn_get_block_template_description($val) : fn_get_lang_var($val);
							}
						}
						$schema[$k][$section_name] = $_tmp;
					}
				}
			}
		}
	}

	return (empty($structure_section) || !array_key_exists($structure_section, $schema)) ? $schema : $schema[$structure_section];
}

function fn_get_block_specific_settings()
{
	static $schema;

	if (!isset($schema)) {
		$schema = fn_get_schema('block_manager', 'specific_settings');
	}

	return $schema;
}

/**
 * The function returns the name of the template
 *
 * @param string $template path to template
 * @return string block name
 */

function fn_get_block_template_description($template)
{
	static $names;

	$path = DIR_SKINS . Registry::get('settings.skin_name_customer') . '/customer/';

	if (!isset($names[$template])) {

		$fd = @fopen($path . $template, 'r');
		if ($fd !== false){
			$counter = 1;
	
			while (($s = fgets($fd, 4096)) && ($counter < 3)) {
				preg_match('/\{\*\* block-description:(\w+) \*\*\}/i', $s, $matches);
				if (!empty($matches[1])) {
					$names[$template] = fn_get_lang_var($matches[1]);
					break;
				}
			}
	
			fclose($fd);
		}	

		// If no description available, set it to template name
		if (empty($names[$template])) {
			$names[$template] = basename($template);
		}
	}

	return $names[$template];
}

/**
 * The function returns the list of available block locations
 *
 * @return array of objects
 *
 */

function fn_get_block_locations()
{
	$locations = array (
		'all_pages' => 'all_pages',
		'products' => 'product_id',
		'categories' => 'category_id',
		'pages' => 'page_id',
		'index' => 'home_page',
		'cart' => 'cart',
		'checkout' => 'checkout',
		'order_landing_page' => 'order_landing_page'
	);

	fn_set_hook('get_block_locations', $locations);

	return $locations;
}

/**
 * The function returns the list of the blocks
 *
 * @param array $params
 * @param string $lang_code
 * @return array of blocks
 */

function fn_get_blocks($params = array(), $allow_sorting = true, $lang_code = CART_LANGUAGE)
{
	$condition = '';
	$_blocks = $block_ids = array();
	$object_id = 0;
	$location_properties = fn_get_block_locations();

	if (empty($params['all'])) {
		$condition .= " AND ?:blocks.status = 'A'";
	}

	$fields = array (
		'?:blocks.*',
		'?:block_descriptions.description',
		'?:block_links.item_ids',
		'?:block_links.enable AS assigned'
	);

	if (!empty($params)) {
		if (!empty($location_properties[$params['location']]) && !empty($params[$location_properties[$params['location']]])) {
			$object_id = (int)$params[$location_properties[$params['location']]];
		}

		if (!empty($params['location']) && $params['location'] == 'checkout' && MODE == 'cart') {
			$params['location'] = 'cart';
		}

		if (AREA == 'A') {
			$condition .= db_quote(" AND ?:blocks.location = ?s", $params['location']);
		} else {
			$condition .= db_quote(" AND ?:blocks.status = 'A'");
			$condition .= db_quote(" AND NOT FIND_IN_SET(?s, ?:blocks.disabled_locations)", $params['location']);

			if (!empty($object_id)) {
				$condition .= db_quote(" AND ((?:blocks.block_type = 'B' AND ?:block_links.enable = 'Y' AND (?:block_links.location = 'all_pages' OR (?:block_links.location = ?s AND (?:block_links.object_id = ?i OR ?:block_links.object_id = '0')))) OR ?:blocks.block_type = 'G')", $params['location'], $object_id);
			} else {
				$condition .= db_quote(" AND ((?:blocks.block_type = 'B' AND ?:block_links.enable = 'Y' AND (?:block_links.location = 'all_pages' OR ?:block_links.location = ?s)) OR ?:blocks.block_type = 'G')", $params['location']);
			}
		}
	}

	if (!empty($params['block_type'])) {
		$condition .= db_quote(" AND ?:blocks.block_type = ?s", $params['block_type']);
	}

	$blocks = db_get_hash_array('SELECT ' . implode(',', $fields) . " FROM ?:blocks LEFT JOIN ?:block_links ON ?:block_links.block_id = ?:blocks.block_id AND (?:block_links.object_id = ?i OR ?:block_links.object_id = '0') LEFT JOIN ?:block_descriptions ON ?:block_descriptions.block_id = ?:blocks.block_id AND ?:block_descriptions.object_type = 'B' AND ?:block_descriptions.lang_code = ?s WHERE 1 ?p", 'block_id', $object_id, $lang_code, $condition);

	if (!empty($blocks)) {
		$block_ids = array_keys($blocks);
	}

	$specific_settings = fn_get_block_specific_settings();

	if (!empty($blocks)) {
		foreach ($blocks as $block_id => $block) {
			if (!empty($block['properties'])) {
				$blocks[$block_id]['properties'] = fn_unserialize_block_properties($block['properties'], !empty($params['block_properties_location']) ? $params['block_properties_location'] : (!empty($params['location']) ? $params['location'] : $blocks[$block_id]['location']), $block['block_type'], $block['text_id']);
			}
		}

		if (AREA == 'A') {
			$assigned = db_get_hash_single_array("SELECT block_id, COUNT(*) as c FROM ?:block_links WHERE block_id IN (?n) AND enable = 'Y' GROUP BY block_id", array('block_id', 'c'), array_keys($blocks));
		}

		foreach ($blocks as $block_id => $block) {
			if (!empty($location_properties[$block['location']])) {
				$blocks[$block_id]['object_id'] = $location_properties[$block['location']];
			}

			if (isset($blocks[$block_id]['properties']['list_object']) && isset($specific_settings['list_object'][$blocks[$block_id]['properties']['list_object']]['settings'])) {
				foreach ($specific_settings['list_object'][$blocks[$block_id]['properties']['list_object']]['settings'] as $property => $value) {
					if (!is_array($value)) {
						$val = strtolower(str_replace('%', '', $value));
						if (isset($blocks[$block_id][$val])) {
							if (isset($_REQUEST[$blocks[$block_id][$val]])) {
								$value = $_REQUEST[$blocks[$block_id][$val]];
							}
						}
					}
					
					$blocks[$block_id]['properties'][$property] = $value;
				}
			}

			if (AREA == 'A') {
				if (!empty($blocks[$block_id]['properties']['list_object'])) {
					if (strpos($blocks[$block_id]['properties']['list_object'], '.tpl') === false) {
						$blocks[$block_id]['items_count'] = empty($block['item_ids']) ? 0 : (substr_count($block['item_ids'], ',') + 1);
						$blocks[$block_id]['properties']['content_name'] = fn_get_lang_var($blocks[$block_id]['properties']['list_object']);
					} else {
						$blocks[$block_id]['properties']['content_name'] = fn_get_block_template_description($blocks[$block_id]['properties']['list_object']);
					}
				}
				if (!fn_check_static_location($block['location'])) {
					$blocks[$block_id]['assigned_to'] = !empty($assigned[$block_id]) ? $assigned[$block_id] : 0;
				}
			}
		}
	}

	fn_set_hook('get_blocks', $blocks, $params, $lang_code);

	if (AREA == 'A' || AREA != 'A' && !empty($params['product_id'])) {
		fn_get_product_tabs_blocks($object_id, $blocks, $params, $lang_code);
	}

	if (!empty($allow_sorting)) {
		$blocks = fn_sort_blocks($object_id, $params['location'], $blocks);
	}

	return array($blocks, $object_id);
}

function fn_check_static_location($location)
{
	$static_locations = array ('all_pages', 'index', 'cart', 'checkout', 'order_landing_page');

	fn_set_hook('check_static_location', $static_locations);

	return in_array($location, $static_locations);
}

function fn_get_product_tabs_blocks($object_id, &$blocks, $params = array(), $lang_code = CART_LANGUAGE)
{
	if ($params['location'] == 'products') {
		// Get tabs blocks
		$base_dir = DIR_SKINS . Registry::get('settings.skin_name_customer') . '/customer/';
		$tabs_blocks = fn_get_dir_contents($base_dir . 'blocks/product_tabs', false, true, '.tpl', 'blocks/product_tabs/');

		// Now get tabs blocks from addons
		foreach (Registry::get('addons') as $addon => $v) {
			if ($v['status'] == 'A') {
				$_tabs_blocks = fn_get_dir_contents($base_dir . 'addons/' . $addon . '/blocks/product_tabs', false, true, '.tpl', 'addons/' . $addon . '/blocks/product_tabs/');
				if (!empty($_tabs_blocks)) {
					$tabs_blocks = fn_array_merge($tabs_blocks, $_tabs_blocks, false);
				}
			}
		}

		if (!empty($tabs_blocks)) {
			$group_id = db_get_field("SELECT block_id FROM ?:blocks WHERE text_id = 'product_details'");
			foreach ($tabs_blocks as $tpl) {
				$_id = basename($tpl, '.tpl');
				$blocks[$_id] = array(
					'block_id' => $_id,
					'description' => fn_get_block_template_description($tpl),
					'block_type' => 'B',
					'group_id' => $group_id,
					'status' => 'A',
					'location' => 'products',
					'properties' => array(
						'appearances' => $tpl,
						'list_object' => $tpl,
						'static_block' => true
					)
				);
			}
		}
	}
}

function fn_sort_blocks($object_id, $location, $blocks) {
	$exist = db_get_row("SELECT location FROM ?:block_positions WHERE object_id = ?i", $object_id);
	if (empty($exist)) {
		$object_id = 0;
	}

	$new_positions = db_get_hash_single_array("SELECT IF(a.block_id, a.block_id, a.text_id) as block_id, a.group_id FROM ?:block_positions as a WHERE (a.object_id = ?i AND a.location = ?s) OR (a.object_id = '0' AND a.location = ?s AND NOT EXISTS(SELECT * FROM ?:block_positions as b WHERE b.object_id = ?i AND b.location = ?s AND b.block_id = a.block_id AND b.text_id = a.text_id)) OR (a.object_id = '0' AND a.location = 'all_pages' AND NOT EXISTS(SELECT * FROM ?:block_positions as c WHERE (c.object_id = ?i OR c.object_id = '0') AND c.location = ?s AND c.block_id = a.block_id AND c.text_id = a.text_id)) ORDER BY position", array('block_id', 'group_id'), $object_id, $location, $location, $object_id, $location, $object_id, $location);

	if (!empty($new_positions)) {
		$sorted_blocks = array();

		foreach ($new_positions as $id => $group_id) {
			if (isset($blocks[$id])) {
				$sorted_blocks[$id] = $blocks[$id];
				$sorted_blocks[$id]['group_id'] = $group_id;
				unset($blocks[$id]);
			}
		}

		$blocks = $sorted_blocks + $blocks;
	}

	return $blocks;
}

function fn_get_block_items($block)
{
	$properties = fn_get_block_properties($block['properties']['list_object']);

	$params = $items = $data_modifier = array();

	if (!empty($block['properties'])) {
		foreach ($block['properties'] as $prop_name => $prop_val) {
			if (!empty($properties[$prop_name]) && !empty($properties[$prop_name][$prop_val]) && is_array($properties[$prop_name][$prop_val])) {
				// Settings for current element - appearance, filling etc
				$s_section = $properties[$prop_name][$prop_val];
				if (!empty($s_section['data_modifier'])) {
					$data_modifier = array_merge($s_section['data_modifier'], $data_modifier);
				}
				if (!empty($s_section['params'])) {
					$params = array_merge($s_section['params'], $params);
				}
			}
		}
	}

	// Collect data from $_REQUEST
	if (!empty($params['request'])) {
		foreach ($params['request'] as $param => $val) {
			$val = strtolower(str_replace('%', '', $val));
			if (isset($_REQUEST[$val])) {
				$params[$param] = $_REQUEST[$val];
			}
		}
		unset($params['request']);
	}
  
	// Collect data from $_SESSION !!! FIXME, merge with $_REQUEST
	if (!empty($params['session'])) {
		foreach ($params['session'] as $param => $val) {
			$val = strtolower(str_replace('%', '', $val));
			if (isset($_SESSION[$val])) {
				$params[$param] = $_SESSION[$val];
			}
		}
		unset($params['session']);
	}

	// Collect data from $auth !!! FIXME, merge with $_REQUEST
	if (!empty($params['auth'])) {
		foreach ($params['auth'] as $param => $val) {
			$val = strtolower(str_replace('%', '', $val));
			if (isset($_SESSION['auth'][$val])) {
				$params[$param] = $_SESSION['auth'][$val];
			}
		}
		unset($params['auth']);
	}


	$_params = $block['properties'];
	unset($_params['fillings'], $_params['list_object'], $_params['appearances'], $_params['order'], $_params['positions']);
	if (!empty($_params)) {
		$params = fn_array_merge($params, $_params);
	}

	if (!empty($block['properties']['fillings']) && $block['properties']['fillings'] == 'manually') {
		// Check items list
		if (empty($block['item_ids'])) {
			return array();
		} else {
			$params['item_ids'] = $block['item_ids'];
		}
	}

	if (!empty($properties['data_function'])) {
		$func = $properties['data_function'];
	} elseif (!empty($block['properties']['items_function'])) {
		$func = $block['properties']['items_function'];
		$params['block_data'] = $block;
	} else {
		$func = 'fn_get_' . $block['properties']['list_object'];
	}

	if (function_exists($func)) {
		@list($items, ) = $func($params);
	}

	// Picker values
	if (!empty($items)) {
		if (AREA == 'A' && !empty($properties['object_id'])) {
			$picker_ids = array();
			foreach ($items as $item) {
				$picker_ids[] = $item[$properties['object_id']];
			}
			$items = $picker_ids;
		} elseif (!empty($data_modifier)) {
			foreach ($items as $k => $_item) {
				foreach ($data_modifier as $_func => $_param) {
					$__params = array();
					foreach ($_param as $v) {
						if (is_string($v) && $v == '#this') {
							$__params[] = &$items[$k];
						} else {
							$__params[] = $v;
						}
					}
					call_user_func_array($_func, $__params);
				}
			}
		}
	}

	return $items;
}

function fn_get_selected_block_data($params, $blocks, $object_id = 0, $location = 'products')
{
	if (empty($blocks)) {
		return false;
	}

	$block_ids = array_keys($blocks);
	if (empty($params['selected_block_id']) || in_array($params['selected_block_id'], $block_ids) == false) {
		$selected_block_id = $block_ids[0];
	} else {
		$selected_block_id = $params['selected_block_id'];
	}

	if (!empty($object_id) || !empty($location)) {
		$link = db_get_row("SELECT link_id, item_ids, enable as assigned FROM ?:block_links WHERE block_id = ?i AND object_id = ?i AND location = ?s", $selected_block_id, $object_id, $location);

		// If now link found, cleanup existing data
		if (empty($link)) {
			$link = array(
				'link_id' => '',
				'item_ids' => '',
				'enable' => 'N'
			);
		}

		$data = $blocks[$selected_block_id];
		$data = array_merge($data, $link);

		if (!empty($data['properties']['fillings']) && $data['properties']['fillings'] == 'manually' && AREA == 'A') {
			if (!empty($data['item_ids'])) {
				$data['item_ids'] = explode(',', $data['item_ids']);
			}

			return $data;
		}

		$data['item_ids'] = fn_get_block_items($data);
	}

	return $data;
}

function fn_get_block_scroller_directions()
{
	$scroller_directions = array(
		'D' => 'down',
		'U' => 'up',
		'R' => 'right',
		'L' => 'left'
	);

	return $scroller_directions;
}

function fn_get_active_addons_skin_dir($relative = false)
{
	$skins_dir = array();

	foreach	((array)Registry::get('addons') as $addon_name => $data) {
		if (fn_load_addon($addon_name) == true && strpos($addon_name, '_opts') === false) {
			$skins_dir[] = ($relative == true) ? "addons/$addon_name" : DIR_SKINS . Registry::get('settings.skin_name_customer') . '/customer/addons/' . $addon_name;
		}
	}

	return $skins_dir;
}

function fn_get_blocks_location($section)
{
	static $schema;

	if (!isset($schema)) {
		$schema = fn_get_schema('block_manager', 'block_controllers');
	}

	if (AREA == 'C') {
		if (array_key_exists($section, $schema)) {
			if (is_array($schema[$section]) && !empty($schema[$section][MODE])) {
				$new_section = $schema[$section][MODE];
			} elseif (!is_array($schema[$section])) {
				$new_section = $schema[$section];
			} else {
				$new_section = 'all_pages';
			}
		} else {
			$new_section = 'all_pages';
		}
	} else {
		$new_section = $section;
	}

	return $new_section;
}

/**
 * Function delete object from the block (product, category, page etc)
 *
 * @param string $object the type of object
 * @param int $object_id
 *
 * @return true
 */

function fn_clean_block_items($object, $object_id)
{
	$blocks = db_get_array("SELECT block_id, text_id, block_type, location, properties FROM ?:blocks WHERE block_type = 'B'");
	$block_ids = array();
	foreach ($blocks as $block) {
		$block_prop = fn_unserialize_block_properties($block['properties'], $block['location'], $block['block_type'], $block['text_id']);
		if (!empty($block_prop['list_object']) && $block_prop['list_object'] == $object) {
			$block_ids[] = $block['block_id'];
		}
	}

	if (!empty($block_ids)) {
		$rm = fn_remove_from_set('?:block_links.item_ids', $object_id);
		db_query("UPDATE ?:block_links SET item_ids = ?p WHERE block_id IN (?n)", $rm, $block_ids);
	}

	return true;
}

/**
 * Function delete link from the block links table by object id and type
 *
 * @param string $object the type of object
 * @param int $object_id
 *
 * @return true
 */

function fn_clean_block_links($object, $object_id)
{

	db_query("DELETE FROM ?:block_links WHERE location = ?s AND object_id = ?i", $object, $object_id);

	return true;
}

function fn_clone_block_links($object, $object_id, $destination_id)
{
	$data = db_get_array("SELECT * FROM ?:block_links WHERE object_id = ?i AND location = ?s", $object_id, $object);
	foreach ($data as $v) {
		unset($v['link_id']);
		$v['object_id'] = $destination_id;
		db_query("INSERT INTO ?:block_links ?e", $v);
	}
}

function fn_add_items_to_block($block_id, $objects, $object_id = 0, $location = '', $add_vals = false, $page = 0)
{
	$objects = empty($objects) ? array() : $objects;
	$_objects = array();

	if (!empty($objects['block_data']) && is_array($objects['block_data'])) {
		$option = each($objects['block_data']);
		$_data = array (
			'block_id' => $block_id,
			'object_id' => $object_id,
			'object_type' => 'P',
			'object_text_id' => $option['key'],
			'description' => $option['value'],
			'lang_code' => DESCR_SL,
		);

		db_query("REPLACE INTO ?:block_descriptions ?e", $_data);

		unset($objects['block_data']);
	}

	$data = array (
		'block_id' => $block_id,
		'object_id' => $object_id,
		'location' => $location
	);

	$objects_set = '';

	if (empty($object_id)) {
		$data['location'] = db_get_field('SELECT location FROM ?:blocks WHERE block_id = ?i', $block_id);
	}
	
	if (!empty($objects)) {
		if (is_array($objects)) {
			$_objects = $objects;
			if ($add_vals == false) {
				asort($_objects);
				$_objects = array_keys($_objects);
			}
			$objects_set = empty($_objects) ? '' : implode(',', $_objects);
		} else {
			$objects_set = $objects;
			$objects = explode(',', $objects);
			$_objects = $objects;
		}
	} else {
		$_objects = array();
	}

	$current_items = db_get_field("SELECT item_ids FROM ?:block_links WHERE block_id = ?i AND object_id = ?i", $block_id, $object_id);
	if (!empty($current_items)) {
		$current_items = explode(',', $current_items);
		$delete_ids = array();
		if (empty($page) && $add_vals == false) {
			$delete_ids = array_diff($current_items, $_objects);
		} elseif (!empty($page)) {
			$items_per_page = !empty($_SESSION['items_per_page']) ? $_SESSION['items_per_page'] : Registry::get('settings.Appearance.admin_elements_per_page');
			$page_items = array_slice($current_items, ($page - 1) * $items_per_page, $items_per_page);
			if (count($page_items) > count($_objects)) {
				$delete_ids = array_diff($page_items, $_objects);
			}
		}
		if (!empty($delete_ids)) {
			$current_items = array_diff($current_items, $delete_ids);
		}
		if ($add_vals == false) {
			$key_items = array();
			foreach ($current_items as $id => $key) {
				$key_items[$key] = ($id + 1) * 10;
			}
			$objects = $objects + $key_items;
			asort($objects);
			$objects = array_keys($objects);
		} else {
			$objects = array_merge($objects, $current_items);
		}
		$objects = array_unique($objects);
		$objects_set = implode(',', $objects);
	}

	$link_id = fn_assign_block($data);

	db_query('UPDATE ?:block_links SET item_ids = ?s WHERE link_id = ?i', $objects_set, $link_id);

	return true;
}

function fn_assign_block($params)
{
	$w_params = array (
		'block_id' => $params['block_id'],
		'location' => empty($params['location']) ? '' : $params['location'],
		'object_id' => empty($params['object_id']) ? 0 : $params['object_id'],
	);

	$link_id = db_get_field('SELECT link_id FROM ?:block_links WHERE ?w', $w_params);

	if (empty($link_id)) {
		$link_id = db_query('INSERT INTO ?:block_links ?e', $params);
	} elseif (!empty($params['enable'])) {
		db_query('UPDATE ?:block_links SET enable = ?s WHERE link_id = ?i', $params['enable'], $link_id);
	}

	return $link_id;
}

function fn_get_html_content($params)
{
	$_condition = array(
		'block_id' => $params['block_data']['block_id'],
		'object_type' => 'P',
		'lang_code' => DESCR_SL,
	);

	if (isset($params['block_data']['properties']['object_id'])) {
		$_condition['object_id'] = $params['block_data']['properties']['object_id'];
	}

	$block_desc = db_get_array('SELECT object_text_id, description FROM ?:block_descriptions WHERE ?w', $_condition);
	$items = array();
	
	if (!empty($block_desc)) {
		foreach ($block_desc as $val) {
			$items[$val['object_text_id']] = $val['description'];
		}
	}

	return array($items, $params);
}

/**
 * Get all block settings
 *
 * @return array block settings
 */
function fn_get_all_blocks($selected_section = '')
{
	// Get core blocks
	$base_dir = DIR_SKINS . Registry::get('settings.skin_name_customer') . '/customer/';
	$blocks = fn_get_dir_contents($base_dir . 'blocks', false, true, '.tpl', 'blocks/');
	$wrappers = fn_get_dir_contents($base_dir . 'blocks/wrappers', false, true, '.tpl', 'blocks/wrappers/');

	// Now get blocks from addons
	foreach (Registry::get('addons') as $addon => $v) {
		if ($v['status'] == 'A') {
			$_blocks = fn_get_dir_contents($base_dir . 'addons/' . $addon . '/blocks', false, true, '.tpl', 'addons/' . $addon . '/blocks/');
			if (!empty($_blocks)) {
				$blocks = fn_array_merge($blocks, $_blocks, false);
			}
			$_wrappers = fn_get_dir_contents($base_dir . 'addons/' . $addon . '/blocks/wrappers', false, true, '.tpl', 'addons/' . $addon . '/blocks/wrappers/');
			if (!empty($_wrappers)) {
				$wrappers = fn_array_merge($wrappers, $_wrappers, false);
			}
		}
	}

	// Convert array with blocks to key=>value form
	$blocks = fn_array_combine($blocks, true);

	// Get block options
	$_structure = fn_get_block_properties();
	foreach ($_structure as $object => $data) {
		if (!empty($data['appearances'])) {
			foreach ($data['appearances'] as $tpl => $_data) {
				if (!empty($blocks[$tpl])) {
					unset($blocks[$tpl]);
				}
				
				if (isset($_data['conditions']['locations'])) {
					if (!in_array($selected_section, $_data['conditions']['locations'])) {
						unset($_structure[$object]['appearances'][$tpl]);
					}
				}
			}
		}
		
		
	}
	
	// Get blocks with the "settings"
	$specific_settings = fn_get_block_specific_settings();
	$additional_sections = array();
	
	if (!empty($specific_settings['list_object'])) {
		foreach ($specific_settings['list_object'] as $template => $block) {
			if (isset($block['settings'])) {
				if (!empty($blocks[$template])) {
					unset($blocks[$template]);
				}
				
				if (!empty($block['settings']['section'])) {
					if (isset($block['settings']['locations'])) {
						if (!in_array($selected_section, $block['settings']['locations'])) {
							continue;
						}
					}
					
					$additional_sections[$block['settings']['section']]['items'][] = array(
						'name' => fn_get_block_template_description($template),
						'template' => $template,
					);
				}
			}
		}
	}

	$_blocks = array();
	foreach ($blocks as $k => $v) {
		$_blocks[] = array (
			'name' => fn_get_block_template_description($k),
			'template' => $k
		);
	}

	$result = array(
		'dynamic' => $_structure,
		'static' => $_blocks,
		'wrappers' => $wrappers,
	);

	foreach ($additional_sections as $section => $data) {
		$result['additional'][$section]['items'] = $data['items'];
	}

	return $result;
}

function fn_check_blocks_availability($blocks, $block_settings)
{
	$block_settings = $block_settings['dynamic'];
	$disabled_blocks = array();

	foreach ($blocks as $k => $v) {
		if (!empty($v['properties']['list_object'])) {
			// First, check addon blocks and remove if addon is disabled
			if (strpos($v['properties']['list_object'], 'addons/') !== false) {
				$a = explode('/', $v['properties']['list_object']);
				if (fn_load_addon($a[1]) == false) {
					$blocks[$k]['disabled'] = true;
					if ($v['status'] != 'D') {
						$disabled_blocks[] = $k;
					}
					continue;
				}
			}

			// Now, check schema
			if (strpos($v['properties']['list_object'], '.tpl') === false) {
				if (!isset($block_settings[$v['properties']['list_object']])) {
					$blocks[$k]['disabled'] = true;
					if ($v['status'] != 'D') {
						$disabled_blocks[] = $k;
					}
					continue;
				}

				foreach (array('fillings', 'appearances') as $section_name) {
					if (!empty($v['properties'][$section_name])) {
						if ((!isset($block_settings[$v['properties']['list_object']][$section_name]) || !isset($block_settings[$v['properties']['list_object']][$section_name][$v['properties'][$section_name]])) && !isset($v['properties']['static_block'])) {
							$blocks[$k]['disabled'] = true;
							if ($v['status'] != 'D') {
								$disabled_blocks[] = $k;
							}
							break;
						}
					}
				}
			}
		}
	}

	if (!empty($disabled_blocks)) {
		db_query("UPDATE ?:blocks SET status = 'D' WHERE block_id IN (?n)", $disabled_blocks);
	}

	return $blocks;
}

function fn_get_available_group($selected_section, $object_id = 0, $lang_code = CART_LANGUAGE)
{
	return db_get_array("SELECT ?:blocks.block_id, ?:block_descriptions.description, ?:block_positions.group_id as parent_id FROM ?:blocks LEFT JOIN ?:block_descriptions ON ?:block_descriptions.block_id = ?:blocks.block_id AND ?:block_descriptions.object_type = 'B' AND ?:block_descriptions.lang_code = ?s LEFT JOIN ?:block_positions ON ?:block_positions.block_id = ?:blocks.block_id AND ?:block_positions.location = ?:blocks.location AND (?:block_positions.object_id = ?i OR (?:block_positions.object_id = '0' AND NOT EXISTS(SELECT * FROM ?:block_positions as bp WHERE bp.block_id = ?:blocks.block_id AND bp.location = ?:blocks.location AND bp.object_id = ?i))) WHERE ?:blocks.block_type = 'G' AND (?:blocks.location = ?s OR ?:blocks.location = 'all_pages')", $lang_code, $object_id, $object_id, $selected_section);
}

function fn_get_rss_feed($data)
{
	if (!empty($data['feed_url'])) {
		$data_key = 'rss_data_cache_' . (isset($data['block_data']['block_id']) ? $data['block_data']['block_id'] : 0);
		Registry::register_cache($data_key, SECONDS_IN_HOUR, CACHE_LEVEL_TIME);

		if (Registry::is_exist($data_key) == false) {
			$limit = !empty($data['max_item']) ? $data['max_item'] : 3;

			static $included;
			if (empty($included)) {
				require(DIR_CORE . 'class.rss_reader.php');
				$included = true;
			}

			$rss = new RssReader();
			$rss->loadData($data['feed_url']);
			$rss_items = $rss->getItems();

			if (!empty($rss_items)) {
				$rss_items = array_slice($rss_items, 0, $limit);
				$rss_chanel = $rss->getChannel();

				$rss_data = array(array($rss_items, $rss_chanel['link'], $data['feed_url']));
				Registry::set($data_key, $rss_data);

				return $rss_data;
			}
		} else {
			return Registry::get($data_key);
		}
	}

	return array();
}

function fn_get_location_data($location, $get_default_values = false, $lang_code = CART_LANGUAGE)
{
	$location_descriptions = db_get_hash_multi_array("SELECT location, property, description FROM ?:block_location_descriptions WHERE location IN (?a) AND lang_code =?s", array('location', 'property', 'description'), array($location, 'all_pages'), $lang_code);
	$location_properties = db_get_hash_multi_array("SELECT location, property, value FROM ?:block_location_properties WHERE location IN (?a)", array('location', 'property', 'value'), array($location, 'all_pages'));

	$data = fn_array_merge($location_descriptions, $location_properties);

	if ($get_default_values == true) {
		if ($location != 'all_pages' && isset($data[$location]) && isset($data['all_pages'])) {
			foreach ($data[$location] as $id => $v) {
				if (empty($v) && isset($data['all_pages'][$id])) {
					$data[$location][$id] = $data['all_pages'][$id];
				}
			}
			foreach ($data['all_pages'] as $id => $v) {
				if (!isset($data[$location][$id])) {
					$data[$location][$id] = $v;
				}
			}
		}
		return isset($data[$location]) ? $data[$location] : (isset($data['all_pages']) ? $data['all_pages'] : array());
	} else {
		return array('properties' => (isset($data[$location]) ? $data[$location] : array())); 
	}
}

/**
 * If properties for given location differs from the properties for all_pages location then function saves properties for this location
 * else function unsets old properties for given location and later they will be retrieved from all_pages properties.
 *
 * @param array $all_properties Array with properties for all locations
 * @param array $cur_properties Array with propertions for given location
 * @param string $location Current location
 * @return string Serialized properties for all locations.
 */
function fn_serialize_block_properties($all_properties, $cur_properties, $location, $block_type, $text_id = '')
{
	if (!empty($text_id) || $block_type == 'G') {
		if ($location != 'all_pages' && !empty($all_properties['all_pages']) && $all_properties['all_pages'] == $cur_properties) {
			unset($all_properties[$location]);
		} else {
			$all_properties[$location] = $cur_properties;
		}
		return serialize($all_properties);
	}
	return serialize($cur_properties);
}

/**
 * Returns properties for given location or for all_pages if given location properties are empty.
 *
 * @param string $ser_properties Serialized properties for all locations.
 * @param string $location Location
 * @return Array of properties for given location
 */
function fn_unserialize_block_properties($ser_properties, $location, $block_type, $text_id = '')
{
	$all_properties = unserialize($ser_properties);
	if (!empty($text_id) || $block_type == 'G') {
		return isset($all_properties[$location]) ? $all_properties[$location] : (!empty($all_properties['all_pages']) ? $all_properties['all_pages'] : array());
	}
	return $all_properties;
}

/**
 *  Delete page and its subpages
 *
 * @param int $page_id Page ID
 * @param boolean $recurse Delete page recursively or not
 * @return array Returns ids of deleted pages or false if function can't delete page
 */
function fn_delete_page($page_id, $recurse = true)
{

	if (!empty($page_id) && fn_check_company_id('pages', 'page_id', $page_id)) {

		// Delete all subpages
		if ($recurse == true) {
			$id_path = db_get_field("SELECT id_path FROM ?:pages WHERE page_id = ?i", $page_id);
			$page_ids	= db_get_fields("SELECT page_id FROM ?:pages WHERE page_id = ?i OR id_path LIKE ?l", $page_id, "$id_path/%");
		} else {
			$page_ids = array($page_id);
		}

		foreach ($page_ids as $v) {
			// Deleting page
			db_query("DELETE FROM ?:pages WHERE page_id = ?i", $v);
			db_query("DELETE FROM ?:page_descriptions WHERE page_id = ?i", $v);
			fn_set_hook('delete_page', $v);

			fn_clean_block_items('pages', $v);
			fn_clean_block_links('pages', $v);
		}

		return $page_ids; // Returns ids of deleted pages
	} else {
		return false;
	}
}

?>
