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
// $Id: block_manager.php 10488 2010-08-23 08:34:45Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	fn_trusted_vars(
		'block',
		'block_items'
	);
	
	$suffix = '';

	if ($mode == 'add') {
		if (!empty($_REQUEST['block'])) {
			$block = $_REQUEST['block'];
			$block['location'] = $_REQUEST['add_selected_section'];
			$bid = fn_update_block($block, $_REQUEST['add_selected_section'], $_REQUEST['add_selected_section']);
			// if the block doesn't require assigning to the one object (product, category etc)
			if (fn_check_static_location($block['location'])) {
				fn_assign_block(array ('block_id' => $bid, 'location' => $block['location'], 'status' => 'A'));
			}
		}

		$suffix = "&selected_section=$_REQUEST[add_selected_section]";
	}

	if ($mode == 'enable_disable' && !empty($_REQUEST['block_id'])) {
		fn_assign_block($_REQUEST);
		exit();
	}

	if ($mode == 'update') {
		fn_update_block($_REQUEST['block'], $_REQUEST['block']['location'], $_REQUEST['redirect_location']);
		$suffix .= "&selected_section=$_REQUEST[redirect_location]";
	}

	if ($mode == 'update_location') {
		fn_update_location($_REQUEST['block']);
		$suffix .= "&selected_section=" . $_REQUEST['block']['location'];
	}

	if ($mode == 'add_items') {
		fn_add_items_to_block($_REQUEST['block_id'], !empty($_REQUEST['block_items']) ? $_REQUEST['block_items'] : '', !empty($_REQUEST['object_id']) ? $_REQUEST['object_id'] : 0, $_REQUEST['block_location'], empty($_REQUEST['is_manage']), !empty($_REQUEST['page']) ? $_REQUEST['page'] : 0);

		$suffix .= "&selected_section=$_REQUEST[redirect_location]";
	}

	if ($mode == 'save_layout') {
		$positions = $_REQUEST['block_positions'];

		if (empty($_REQUEST['object_id']) && empty($_REQUEST['user_choice'])) {
			foreach ($positions as $_group) {
				if (!empty($_group)) {
					$block_ids = split(',', $_group);
					$custom_positions = db_get_array("SELECT position FROM ?:block_positions WHERE object_id != '0' AND location = ?s AND (block_id IN(?n) OR text_id IN(?a))", $_REQUEST['add_selected_section'], $block_ids, $block_ids);
					if (!empty($custom_positions)) {
						$ajax->assign('confirm_text', fn_get_lang_var('text_position_overwrite'));
						exit();
					}
				}
			}
		}
		fn_save_block_location($positions, $_REQUEST['object_id'], empty($_REQUEST['user_choice']) ? 'N' : $_REQUEST['user_choice'], $_REQUEST['add_selected_section']);
		exit();
	}

	return array(CONTROLLER_STATUS_OK, "block_manager.manage" . $suffix);
}

$selected_section = empty($_REQUEST['selected_section']) ? 'all_pages' : $_REQUEST['selected_section'];

$view->assign('block_settings', fn_get_all_blocks($selected_section));

if ($mode == 'delete') {
	if (!empty($_REQUEST['block_id'])) {
		fn_delete_block($_REQUEST['block_id']);
	}
	if (!empty($_REQUEST['redirect_url'])) {
		$url = '';
		unset($_REQUEST['selected_section']);
	} else {
		$url = "block_manager.manage";
	}

	return array(CONTROLLER_STATUS_OK, $url);

} elseif ($mode == 'bulk_actions') {
	fn_block_bulk_actions($_REQUEST['block_id'], ACTION);

	return array(CONTROLLER_STATUS_OK, "block_manager.manage");

} elseif ($mode == 'update_status') {
	// Check if global (all_pages) block is disabled for sub-location (products, categories etc.)
	if (!empty($_REQUEST['selected_location']) && $_REQUEST['selected_location'] != 'all_pages' && $_REQUEST['block_location'] == 'all_pages') {
		$disabled_locations = db_get_field("SELECT disabled_locations FROM ?:blocks WHERE block_id = ?i", $_REQUEST['id']);
		if (strpos($disabled_locations, $_REQUEST['selected_location']) === false && $_REQUEST['status'] != 'A') {
			// disable block
			$query = fn_add_to_set('disabled_locations', $_REQUEST['selected_location']);
			$_action = 'disabled';
		} else {
			// enable block
			$query = fn_remove_from_set('disabled_locations', $_REQUEST['selected_location']);
			$_action = 'enabled';
		}

		db_query("UPDATE ?:blocks SET disabled_locations = ?p WHERE block_id = ?i", $query, $_REQUEST['id']);
		fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var("block_$_action"));
		exit();
	}
}

if ($mode == 'manage') {

	$locations = fn_get_block_locations();

	// [Page sections]
	if (!empty($locations)) {
		foreach ($locations as $location => $_location) {
			Registry::set("navigation.tabs.$location", array (
				'title' => fn_get_lang_var($location),
				'href' => "block_manager.manage?selected_section=$location"
			));
		}
	}
	// [/Page sections]

	$selected_section = empty($_REQUEST['selected_section']) ? 'all_pages' : $_REQUEST['selected_section'];
	$view->assign('locations', $locations);
	list($blocks, $object_id) = fn_get_blocks(array('location' => $selected_section, 'all' => true), false, DESCR_SL);

	if ($selected_section !== 'all_pages') {
		list($all_blocks) = fn_get_blocks(array('location' => 'all_pages', 'all' => true, 'block_properties_location' => $selected_section), false);
		$blocks = fn_array_merge($blocks, $all_blocks, true);
	}

	$blocks = fn_sort_blocks($object_id, $selected_section, $blocks);
	$blocks = fn_check_blocks_availability($blocks, $view->get_var('block_settings'));

	$view->assign('avail_positions', fn_get_available_group($selected_section, 0, DESCR_SL));

	$view->assign('specific_settings', fn_process_specific_settings(fn_get_block_specific_settings()));
	$view->assign('location', $selected_section);
	$view->assign('blocks', $blocks);

} elseif ($mode == 'manage_items') {
	$view->assign('location', $_REQUEST['location']);
	$view->assign('block', fn_get_block_data($_REQUEST['block_id'], CART_LANGUAGE, false, $_REQUEST['location']));

	$object_id = empty($_REQUEST['object_id']) ? '' : $_REQUEST['object_id'];
	$view->assign('object_id', $object_id);

	$view->assign('redir_url', empty($_REQUEST['redir_url']) ? '' : $_REQUEST['redir_url']);

	$block_items = db_get_field("SELECT item_ids FROM ?:block_links WHERE block_id = ?i AND object_id = ?i", $_REQUEST['block_id'], $object_id);
	if (!empty($block_items)) {
		$items_ids = explode(',', $block_items);
		$page = empty($_REQUEST['page']) ? 1 : $_REQUEST['page'];
		$items_per_page = Registry::get('settings.Appearance.admin_elements_per_page');
		fn_paginate($page, count($items_ids), $items_per_page);
		if (!empty($_SESSION['items_per_page'])) {
			$items_per_page = $_SESSION['items_per_page'];
		}
		$start_pos = ($page - 1) * $items_per_page;
		$view->assign('start_position', $start_pos);
		$view->assign('block_items', array_slice($items_ids, $start_pos, $items_per_page));
	} else {
		list($blocks) = fn_get_blocks(array('location' => $_REQUEST['location']));
		if (!empty($blocks)) {
			$view->assign('block', fn_get_selected_block_data(array('selected_block_id' => $_REQUEST['block_id']), $blocks, $_REQUEST['object_id'], $_REQUEST['location']));
		}
	}

} elseif ($mode == 'specific_settings') {

	$specific_settings = fn_get_block_specific_settings();

	if (!empty($specific_settings[$_REQUEST['type']]) && !empty($specific_settings[$_REQUEST['type']][$_REQUEST['value']])) {
		$specific_settings = fn_process_specific_settings($specific_settings, $_REQUEST['type'], $_REQUEST['value']);
		$view->assign('spec_settings', $specific_settings[$_REQUEST['type']][$_REQUEST['value']]);
	}

	$view->assign('s_set_id', $_REQUEST['block_id'] . $_REQUEST['block_type'] . '_' . $_REQUEST['type']);

} elseif ($mode == 'assign_items') {
	$view->assign('location', $_REQUEST['location']);
	$view->assign('block', fn_get_block_data($_REQUEST['block_id'], CART_LANGUAGE, false, $_REQUEST['location']));

} elseif ($mode == 'update') {

	$block = fn_get_block_data($_REQUEST['block_id'], DESCR_SL, false, $_REQUEST['location']);
	$view->assign('block', $block);
	$view->assign('location', $_REQUEST['location']);
	$view->assign('specific_settings', fn_process_specific_settings(fn_get_block_specific_settings()));
	$view->assign('redirect_url', empty($_REQUEST['r_url']) ? '' : $_REQUEST['r_url']);
	
	$object_id = empty($_REQUEST['object_id']) ? 0 : $_REQUEST['object_id'];
	$view->assign('avail_positions', fn_get_available_group($_REQUEST['location'], $object_id, DESCR_SL));

	$block_parent = fn_get_parent_group($_REQUEST['block_id'], $object_id, $_REQUEST['location']);
	$view->assign('block_parent', $block_parent);
	$view->assign('object_id', $object_id);

} elseif ($mode == 'update_location') {

	$spec_settings = fn_get_block_specific_settings();
	$view->assign('location_properties', $spec_settings['properties']['location']);
	$view->assign('location', $_REQUEST['location']);
	$data = fn_get_location_data($_REQUEST['location'], false, DESCR_SL);
	$view->assign('block', $data);

} elseif ($mode == 'check_parent') {

	if (empty($_REQUEST['object_id'])) {
		$custom_positions = db_get_array("SELECT position FROM ?:block_positions WHERE object_id != '0' AND location = ?s AND block_id = ?i", $_REQUEST['location'], $_REQUEST['block_id']);
		if (!empty($custom_positions)) {
			$ajax->assign('confirm_text', fn_get_lang_var('text_position_overwrite'));
			exit();
		}
	}
	exit();
}

function fn_get_parent_group($block_id, $object_id, $location)
{
	return db_get_field("SELECT a.group_id FROM ?:block_positions as a WHERE a.block_id = ?i AND ((a.object_id = ?i AND a.location = ?s) OR (a.object_id = '0' AND a.location = ?s AND NOT EXISTS(SELECT * FROM ?:block_positions as b WHERE b.object_id = ?i AND b.location = ?s AND b.block_id = a.block_id AND b.text_id = a.text_id)) OR (a.object_id = '0' AND a.location = 'all_pages' AND NOT EXISTS(SELECT * FROM ?:block_positions as c WHERE (c.object_id = ?i OR c.object_id = '0') AND c.location = ?s AND c.block_id = a.block_id AND c.text_id = a.text_id)))", $block_id, $object_id, $location, $location, $object_id, $location, $object_id, $location);
}

/**
 * This function save selected block or add new
 *
 * @param array $block block data
 * @return int block id or false
 */
function fn_update_block($block, $selected_location = 'all_pages', $current_location = 'all_pages')
{
	// Add new block
	if (empty($block['block_id'])) {
		$_data = array (
			'block_type' => $block['block_type'],
			'location' => $selected_location
		);
		$block['block_id'] = $_block_id = db_query("INSERT INTO ?:blocks ?e", $_data);
	}

	$disallow_properties = array (
		'description',
		'block_id',
		'location',
		'block_type',
		'status',
		'group_id'
	);

	$object_id = empty($block['object_id']) ? 0 : $block['object_id'];
	$specific_settings = fn_get_block_specific_settings();
	$block_properties = array();
	foreach ($block as $setting_name => $value) {
		if (!in_array($setting_name, $disallow_properties)) {
			// Check previous settings
			$multilangual = false;

			if (!empty($block['list_object']) && isset($specific_settings['list_object'][$block['list_object']][$setting_name]['multilingual']) && $specific_settings['list_object'][$block['list_object']][$setting_name]['multilingual'] == true) {
				$_data = array (
					'block_id' => $block['block_id'],
					'object_id' => $object_id,
					'object_type' => 'P',
					'object_text_id' => $setting_name,
					'description' => $block[$setting_name],
					'lang_code' => DESCR_SL,
				);

				if (!empty($_block_id)) {
					foreach ((array)Registry::get('languages') as $_data['lang_code'] => $v) {
						db_query("REPLACE INTO ?:block_descriptions ?e", $_data);
					}
				} else {
					db_query("REPLACE INTO ?:block_descriptions ?e", $_data);
				}

				$multilangual = true;
			}
			if (!$multilangual) {
				$block_properties[$setting_name] = $block[$setting_name];
			}
		}
	}
	$block['properties'] = $block_properties;
	

	if (!empty($_block_id)) {
		$_data = array (
			'properties' => fn_serialize_block_properties(array(), $block['properties'], $selected_location, $block['block_type'])
		);
		db_query('UPDATE ?:blocks SET ?u WHERE block_id = ?i', $_data, $_block_id);
		$_data = array (
			'block_id' => $_block_id,
			'object_id' => $object_id,
			'object_type' => 'B',
			'description' => $block['description']
		);
		foreach ((array)Registry::get('languages') as $_data['lang_code'] => $v) {
			db_query("INSERT INTO ?:block_descriptions ?e", $_data);
		}
		$_data = array (
			'block_id' => $_block_id,
			'object_id' => $object_id,
			'location' => $selected_location,
			'group_id' => $block['group_id']
		);
		db_query("INSERT INTO ?:block_positions ?e", $_data);
	} else {
		// Check if list object was changed and delete all list items (for manual list filling)
		$old_block = db_get_row("SELECT text_id, block_type, location, properties FROM ?:blocks WHERE block_id = ?i", $block['block_id']);
		$old_block_properties = fn_unserialize_block_properties($old_block['properties'], $current_location, $old_block['block_type'], $old_block['text_id']);
		if (empty($old_block_properties['list_object']) || $old_block_properties['list_object'] != $block['list_object']) {
			db_query("UPDATE ?:block_links SET item_ids = '' WHERE block_id = ?i", $block['block_id']);
		}

		$_data = array (
			'block_type' => $block['block_type'],
			'location' => $selected_location,
			'properties' => fn_serialize_block_properties(unserialize($old_block['properties']), $block['properties'], $current_location, $block['block_type'], $old_block['text_id'])
		);
		db_query('UPDATE ?:blocks SET ?u WHERE block_id = ?i', $_data, $block['block_id']);
		$_data = array (
			'description' => empty($block['description']) ? '' : $block['description']
		);
		$_where = array (
			'block_id' => $block['block_id'],
			'object_id' => $object_id,
			'object_type' => 'B',
			'lang_code' => DESCR_SL
		);
		db_query("UPDATE ?:block_descriptions SET ?u WHERE ?w", $_data, $_where);

		if (!empty($block['group_id'])) {
			$current_group_id = fn_get_parent_group($block['block_id'], $object_id, $current_location);
			if (empty($current_group_id) || (!empty($current_group_id) && $current_group_id != $block['group_id'])) {
				if (isset($block['rewrite_positions']) && $block['rewrite_positions'] == 'Y' && empty($object_id)) {
					db_query("DELETE FROM ?:block_positions WHERE location = ?s AND object_id != '0'", $current_location);
				}
				db_query("DELETE FROM ?:block_positions WHERE object_id = ?i AND location = ?s AND block_id = ?i", $object_id, $current_location, $block['block_id']);

				// We need to make changes in the other locations (products, categories, news...)
				if ($current_location == 'all_pages') {
					db_query("DELETE FROM ?:block_positions WHERE object_id = '0' AND location != 'all_pages' AND block_id = ?i", $block['block_id']);
				}

				$_data = array (
					'block_id' => $block['block_id'],
					'text_id' => '',
					'object_id' => $object_id,
					'location' => $current_location,
					'group_id' => $block['group_id'],
					'position' => 0
				);
				db_query("REPLACE INTO ?:block_positions ?e", $_data);
				db_query("UPDATE ?:block_positions SET position = position + 1 WHERE object_id = ?i AND location = ?s", $object_id, $current_location);
			}
		}
	}

	return empty($block['block_id']) ? false : $block['block_id'];
}

function fn_update_location($data)
{
	$disallow_properties = array (
		'location',
	);
	$specific_settings = fn_get_block_specific_settings();
	$location_properties = $specific_settings['properties']['location'];
	foreach ($data as $setting_name => $value) {
		if (!in_array($setting_name, $disallow_properties)) {
			if (isset($location_properties[$setting_name]['multilingual']) && $location_properties[$setting_name]['multilingual'] == true) {
				$_data = array (
					'location' => $data['location'],
					'property' => $setting_name,
					'description' => $value,
					'lang_code' => DESCR_SL
				);

				db_query("REPLACE INTO ?:block_location_descriptions ?e", $_data);
			} else {
				$_data = array (
					'location' => $data['location'],
					'property' => $setting_name,
					'value' => $value
				);

				db_query("REPLACE INTO ?:block_location_properties ?e", $_data);
			}
		}
	}
	return true;
}

/**
 * Function delete selected block from database and templates
 *
 * @param int $block_id
 * @return bool true
 */

function fn_delete_block($block_id)
{
	$block_data = db_get_row("SELECT block_type, location FROM ?:blocks WHERE block_id = ?i", $block_id);
	if ($block_data['block_type'] == 'G') {
		$parent_group = db_get_field("SELECT group_id FROM ?:block_positions WHERE location = ?s AND object_id = '0' AND block_id = ?i", $block_data['location'], $block_id);
		$_data = array('group_id' => $parent_group);
		db_query("UPDATE ?:block_positions SET ?u WHERE group_id = ?i", $_data, $block_id);
	}

	db_query("DELETE FROM ?:blocks WHERE block_id = ?i", $block_id);
	db_query("DELETE FROM ?:block_descriptions WHERE block_id = ?i", $block_id);
	db_query("DELETE FROM ?:block_links WHERE block_id = ?i", $block_id);
	db_query("DELETE FROM ?:block_positions WHERE block_id = ?i", $block_id);

	return true;
}

/**
 * This function will save block positions the blocks location, if necessary
 *
 * @param array $positions - The array contains the block positions in the following format: ('left' => array (block ids), 'right' => array (...), 'center' => array (...))
 * @param string $section
 * @return bool true
 */

function fn_save_block_location($positions, $object_id = 0, $user_choice = 'N', $section = 'all_pages')
{
	$all_block_ids = split(',', join(',', array_values($positions)));

	if (empty($object_id) && $user_choice == 'Y') {
		db_query("DELETE FROM ?:block_positions WHERE location = ?s AND (block_id IN(?n) OR text_id IN(?a))", $section, $all_block_ids, $all_block_ids);
	}

	// We need to make changes in the other locations (products, categories, news...)
	if ($section == 'all_pages') {
		db_query("DELETE FROM ?:block_positions WHERE object_id = '0' AND location != 'all_pages' AND (block_id IN(?n) OR text_id IN(?a))", $all_block_ids, $all_block_ids);
	}

	foreach ($positions as $_group_id => $_ids) {
		$block_ids = split(',', $_ids);
		foreach ($block_ids as $pos => $blk_id) {
			$_data = array (
				'block_id' => is_numeric($blk_id) ? $blk_id : 0,
				'text_id' => is_numeric($blk_id) ? '' : $blk_id,
				'object_id' => $object_id,
				'location' => $section,
				'group_id' => $_group_id,
				'position' => $pos
			);
			db_query("REPLACE INTO ?:block_positions ?e", $_data);
		}
	}

	return true;
}

function fn_block_bulk_actions($block_id, $action)
{
	$schema = fn_get_schema('block_manager', 'structure');

	$block_data = fn_get_block_data($block_id);
	$o_id = $schema[$block_data['location']]['object_id'];

	if ($action == 'assign_to_all') {
		$exclude = db_get_fields("SELECT object_id FROM ?:block_links WHERE block_id = ?i AND location = ?s", $block_id, $block_data['location']);
		$where = empty($exclude) ? '' : db_quote("WHERE $o_id NOT IN(?n)", $exclude);
		$item_ids = db_query("REPLACE INTO ?:block_links (block_id, location, object_id, enable) SELECT ?i as block_id, ?s as location, $o_id as object_id, 'Y' as enable FROM ?:{$block_data['location']} ?p", $block_id, $block_data['location'], $where);

	} elseif ($action == 'remove_from_all') {
		db_query("DELETE FROM ?:block_links WHERE block_id = ?i AND location = ?s", $block_id, $block_data['location']);

	}

	return true;
}

/**
 * The function returns the selected block data used in the admin area
 *
 * @param int $block_id
 * @param string $lang_code
 * @param bool $descr if true, the function only returns the description of the block
 * @return array
 */
function fn_get_block_data($block_id, $lang_code = CART_LANGUAGE, $descr = false, $location = '')
{
	$block = db_get_row("SELECT ?:blocks.*, ?:block_descriptions.description FROM ?:blocks LEFT JOIN ?:block_descriptions ON ?:block_descriptions.block_id = ?:blocks.block_id AND ?:block_descriptions.object_type = 'B' AND ?:block_descriptions.lang_code = ?s WHERE ?:blocks.block_id = ?i", $lang_code, $block_id);

	if (empty($block)) {
		return false;
	}

	if ($descr == true) {
		return $block;
	}

	if (!empty($block['item_ids'])) {
		$block['items'] = explode(',', $block['item_ids']);
	}

	if (empty($location)) {
		$location = $block['location'];
	}
	$block['properties'] = fn_unserialize_block_properties($block['properties'], $location, $block['block_type'], $block['text_id']);

	$block_desc = db_get_array("SELECT object_text_id, description FROM ?:block_descriptions WHERE block_id = ?i AND object_id = '0' AND object_type = 'P' AND lang_code = ?s", $block_id, $lang_code);

	if (!empty($block_desc)) {
		foreach ($block_desc as $val) {
			$block['properties'][$val['object_text_id']] = $val['description'];
		}
	}

	return $block;
}

function fn_process_specific_settings($settings, $section = '', $object = '')
{
	foreach ($settings as $_section => $_objects) {
		if (!empty($section) && $_section == $section || empty($section)) {
			foreach ($_objects as $_object => $_options) {
				if (!empty($object) && $_object == $object || empty($object)) {
					if (is_array($_options)) {
						foreach ($_options as $k => $v) {
							if (!empty($v['data_function'])) {
								$df = $v['data_function'];
								$f = array_shift($df);
								$settings[$_section][$_object][$k]['values'] = call_user_func_array($f, $df);
							}
						}
					}
				}
			}
		}
	}

	return $settings;
}

?>