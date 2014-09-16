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
// $Id: pages.php 10562 2010-09-01 08:17:50Z andyye $
//

if ( !defined('AREA') ) { die('Access denied'); }

/** Body **/

$page_id = isset($_REQUEST['page_id']) ? intval($_REQUEST['page_id']) : 0;

/* POST data processing */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$suffix = '';

	// Define trusted variables that shouldn't be stripped
	fn_trusted_vars('page_data');

	//
	// Processing additon of new page element
	//
	if ($mode == 'add') {
		if (!empty($_REQUEST['page_data']['page'])) {  // Checking for required fields for new page
			// Adding page record
			$page_id = fn_update_page($_REQUEST['page_data']);

			if (isset($_REQUEST['redirect_url'])) { 
				$_REQUEST['redirect_url'] .= '&get_tree=multi_level'; 
			}
			if (empty($page_id)) {
				$suffix = ".manage";
			} else {
				$suffix = ".update?page_id=$page_id";
			}
		} else  {
			$suffix = ".add?page_type={$_REQUEST['page_data']['page_type']}";
		}
	}

	//
	// Processing updating of page element
	//
	if ($mode == 'update') {
		$page_id = empty($_REQUEST['page_id']) ? $_REQUEST['page_data']['page_id'] : $_REQUEST['page_id'];

		if (!empty($_REQUEST['page_data']['page'])) {
			// Updating page record
			fn_update_page($_REQUEST['page_data'], $page_id, DESCR_SL);
		}
		if (isset($_REQUEST['redirect_url'])) {
			$_REQUEST['redirect_url'] .= '&get_tree=multi_level';
		}
		$suffix = ".update?page_id=$page_id" . (!empty($_REQUEST['page_data']['block_id']) ? "&selected_block_id=" . $_REQUEST['page_data']['block_id'] : "");
	}

	//
	// Processing mulitple addition of new page elements
	//
	if ($mode == 'm_add') {

		if (is_array($_REQUEST['page_data'])) {
			foreach ($_REQUEST['page_data'] as $k => $v) {
				if (!empty($v['page'])) {  // Checking for required fields for new page
					$added_pages[$k]['page_id'] = fn_update_page($v);
					$added_pages[$k]['page'] = $v['page'];
				}

			}

			$view->assign('added_pages', $added_pages);
			$_output = $view->display('pages_pages/pages_m_viewadded.tpl', false); // FIX-TEMPLATE
			fn_set_notification('N', fn_get_lang_var('notice'), $_output, true);
		}

		$suffix = ".manage?page_type=" . $_REQUEST['page_type'];
	}

	//
	// Processing multiple updating of page elements
	//
	if ($mode == 'm_update') {
		// Update multiple pages data
		foreach ($_REQUEST['pages_data'] as $page_id => $page_data) {
			if (!empty($page_data)) {
				fn_update_page($page_data, $page_id, DESCR_SL);
			}
		}

		$suffix = ".manage";
	}

	//
	// Processing deleting of multiple page elements
	//
	if ($mode == 'm_delete') {
		if (isset($_REQUEST['page_ids'])) {
			foreach ($_REQUEST['page_ids'] as $v) {
				fn_delete_page($v);
			}
		}
		unset($_SESSION['page_ids']);
		fn_set_notification('N', fn_get_lang_var('congratulations'), fn_get_lang_var('text_pages_have_been_deleted'));

		$suffix = ".manage?" . (empty($_REQUEST['page_type']) ? "" : ("page_type=" . $_REQUEST['page_type']));
	}

	//
	// Processing clonning of multiple page elements
	//
	if ($mode == 'm_clone') {
		$p_ids = array();
		if (!empty($_REQUEST['page_ids'])) {
			foreach ($_REQUEST['page_ids'] as $v) {
				$pdata = fn_clone_page($v);
				if (!empty($pdata)) {
					$p_ids[] = $pdata['page_id'];
				}
			}
			fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_pages_cloned'));
		}
		$suffix = ".manage?item_ids=" . implode(',', $p_ids);
		unset($_REQUEST['redirect_url'], $_REQUEST['page']); // force redirection
	}

	//
	// Storing selected fields for using in m_update mode
	//
	if ($mode == 'store_selection') {
		$_SESSION['page_ids'] = $_REQUEST['page_ids'];
		$_SESSION['selected_fields'] = $_REQUEST['selected_fields'];

		if (isset($_SESSION['page_ids'])) {
			$suffix = ".m_update";
		} else {
			$suffix = ".manage";
		}
	}

	//
	// This mode is using to send search data via POST method
	//
	if ($mode == 'search_pages') {
		$suffix = ".manage";
	}

	if (empty($suffix)) {
		$suffix = '.manage';
	}

	return array(CONTROLLER_STATUS_OK, "pages$suffix");
}
/* /POST data processing */

//
// 'Add new page' page
//
if ($mode == 'add') {

	$page_type = isset($_REQUEST['page_type']) ? $_REQUEST['page_type'] : PAGE_TYPE_TEXT;

	// [Breadcrumbs]
	fn_add_breadcrumb(fn_get_lang_var('pages'), "pages.manage");
	// [/Breadcrumbs]

	Registry::set('navigation.tabs', array(
		'basic' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'addons' => array (
			'title' => fn_get_lang_var('addons'),
			'js' => true
		),
	));

	if (!empty($_REQUEST['parent_id'])) {
		$page_data['parent_id'] = $_REQUEST['parent_id'];
		$view->assign('page_data', $page_data);
	}

	if ($page_type == PAGE_TYPE_LINK) {
		Registry::set('navigation.selected_tab', 'content');
		Registry::set('navigation.subsection', 'links');
	}

	list($pages_tree, $params) = fn_get_pages(array('get_tree' => 'plain'));

	$view->assign('all_pages_list', $pages_tree);
	$view->assign('page_type', $page_type);
	$view->assign('page_type_data', fn_get_page_object_by_type($page_type));
	$view->assign('companies', fn_get_short_companies());

//
// 'page update' page
//
} elseif ($mode == 'update') {

	Registry::set('navigation.tabs', array (
		'basic' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'blocks' => array (
			'title' => fn_get_lang_var('blocks'),
			'js' => true
		),
		'addons' => array (
			'title' => fn_get_lang_var('addons'),
			'js' => true
		),
	));

	// Get current page data
	$page_data = fn_get_page_data($page_id, DESCR_SL);

	if (!$page_data) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	// [Breadcrumbs]
	fn_add_breadcrumb(fn_get_lang_var('pages'), "pages.manage&get_tree=multi_level");
	// [/Breadcrumbs]

	if ($page_data['page_type'] == PAGE_TYPE_LINK) {
		Registry::set('navigation.selected_tab', 'content');
		Registry::set('navigation.subsection', 'links');
	}

	// [Block manager]
	list($blocks) = fn_get_blocks(array('location' => 'pages', 'block_type' => 'B', 'all' => true));
	if (!empty($blocks)) {
		$view->assign('blocks', $blocks);
		$view->assign('selected_block', fn_get_selected_block_data($_REQUEST, $blocks, $_REQUEST['page_id'], 'pages'));
		$view->assign('block_properties', fn_get_block_properties());
	}
	// [/Block manager]

	list($pages_tree, $params) = fn_get_pages(array('get_tree' => 'plain'));

	$view->assign('all_pages_list', $pages_tree);
	$view->assign('page_type', $page_data['page_type']);
	$view->assign('page_data', $page_data);
	$view->assign('page_type_data', fn_get_page_object_by_type($page_data['page_type']));
	$view->assign('companies', fn_get_short_companies());

//
// Delete page
//
} elseif ($mode == 'delete') {
	$suffix = '';
	if (!empty($page_id)) {
		if (!isset($_REQUEST['multi_level'])) {
			$page_type = db_get_field("SELECT page_type FROM ?:pages WHERE page_id = ?i", $page_id);
			$suffix = '?page_type=' . $page_type;
		} else {
			$suffix = '?get_tree=multi_level';
		}
		fn_delete_page($page_id);
		fn_set_notification('N', fn_get_lang_var('congratulations'), fn_get_lang_var('text_page_has_been_deleted'));
	}

	return array(CONTROLLER_STATUS_REDIRECT, "pages.manage$suffix");

//
// Clone page
//
} elseif ($mode == 'clone') {
	if (!empty($_REQUEST['page_id'])) {
		$pdata = fn_clone_page($_REQUEST['page_id']);
		$msg = fn_get_lang_var('page_cloned');
		$msg = str_replace('[page]', $pdata['orig_name'], $msg);
		fn_set_notification('N', fn_get_lang_var('notice'), $msg);

		return array(CONTROLLER_STATUS_REDIRECT, "pages.update?page_id=$pdata[page_id]");
	}
//
// 'Management' page
//
} elseif ($mode == 'manage' || $mode == 'picker') {

	$params = $_REQUEST;
	if ($mode == 'picker') {
		$params['skip_view'] = 'Y';
	}
	
	if (!empty($params['get_tree'])) { // manage page, show tree
		$total = db_get_field("SELECT COUNT(*) FROM ?:pages");
		if ($total > PAGE_THRESHOLD) {
			$params['get_children_count'] = true;
			$params['get_tree'] = '';
			$params['parent_id'] = !empty($params['parent_id']) ? $params['parent_id'] : 0;

			if (defined('AJAX_REQUEST')) {
				$view->assign('parent_id', $params['parent_id']);
				$view->assign('hide_header', true);
			}

			$view->assign('hide_show_all', true);
		}
		if ($total < PAGE_SHOW_ALL) {
			$view->assign('expand_all', true);
		}
	} else { // search page
		$params['paginate'] = true;
	}
	$params['add_root'] = !empty($_REQUEST['root']) ? $_REQUEST['root'] : '';

	list($pages, $params) = fn_get_pages($params, Registry::get('settings.Appearance.admin_pages_per_page'));

	$view->assign('pages_tree', $pages);
	$view->assign('search', $params);
	$view->assign('page_types', fn_get_page_object_by_type());

	if (!empty($_REQUEST['except_id'])) {
		$view->assign('except_id', $_REQUEST['except_id']);
	}

	if ($mode == 'picker') {
		$view->display('pickers/pages_picker_contents.tpl');
		exit;
	}
}

$view->assign('usergroups', fn_get_usergroups('C', DESCR_SL));
/* /Preparing page data for templates and performing simple actions*/


/** /Body **/

/* -------------------------------------- Related functions -------------------------------- */

//
// update by id or create new page
//
function fn_update_page($page_data, $page_id = 0, $lang_code = CART_LANGUAGE)
{
	if (!empty($page_data['avail_from_timestamp'])) {
		$page_data['avail_from_timestamp'] = fn_parse_date($page_data['avail_from_timestamp']);
	} else {
		$page_data['avail_from_timestamp'] = 0;
	}

	if (!empty($page_data['avail_till_timestamp'])) {
		$page_data['avail_till_timestamp'] = fn_parse_date($page_data['avail_till_timestamp']) + 86399;
	} else {
		$page_data['avail_till_timestamp'] = 0;
	}
	if (isset($page_data['usergroup_ids'])) {
		$page_data['usergroup_ids'] = empty($page_data['usergroup_ids']) ? '' : implode(',', $page_data['usergroup_ids']);
	}
	$_data = $page_data;

	if (isset($page_data['timestamp'])) {
		$_data['timestamp'] = fn_parse_date($page_data['timestamp']);
	}

	if (isset($_data['localization'])) {
		$_data['localization'] = empty($_data['localization']) ? '' : fn_implode_localizations($_data['localization']);
	}

	fn_set_company_id($_data);

	if (empty($page_id)) {
		// page title required
		if (empty($page_data['page'])) {
			return false;
		}

		// add new page
		$_data['page_id'] = $page_id = db_query('INSERT INTO ?:pages ?e', $_data);

		foreach ((array)Registry::get('languages') as $_data['lang_code'] => $v) {
			db_query('INSERT INTO ?:page_descriptions ?e', $_data);
		}

		// now we need to update 'id_path' field, as we know $page_id
		/* Generate id_path for page */
		$parent_id = intval($_data['parent_id']);
		if ($parent_id == 0) {
			$id_path = $page_id;
		} else {
			$id_path = db_get_row("SELECT id_path FROM ?:pages WHERE page_id = ?i", $parent_id);
			$id_path = $id_path['id_path'] . '/' . $page_id;
		}

		db_query('UPDATE ?:pages SET ?u WHERE page_id = ?i', array('id_path' => $id_path), $page_id);

	} else {

		if (!fn_check_company_id('pages', 'page_id', $page_id)) {
			fn_company_access_denied_notification();
			return false;
		}

		$old_company_id = fn_get_company_id('pages', 'page_id', $page_id);

		if ($_data['company_id'] != $old_company_id) {
			fn_change_page_company($page_id, $_data['company_id']);
		}
		
		// page title is not updated
		if (empty($page_data['page'])) {
			unset($page_data['page']);
		}

		// update existing page
		db_query('UPDATE ?:pages SET ?u WHERE page_id = ?i', $_data, $page_id);
		db_query('UPDATE ?:page_descriptions SET ?u WHERE page_id = ?i AND lang_code = ?s', $_data, $page_id, $lang_code);

		// regenerate id_path for child pages
		if (isset($page_data['parent_id'])) {
			fn_change_page_parent($page_id, $page_data['parent_id']);
		}
	}

	if (!empty($page_data['block_id'])) {
		fn_add_items_to_block($page_data['block_id'], $page_data['add_items'], $page_id, 'pages');
	}

	fn_set_hook('update_page', $page_data, $page_id, $lang_code);

	return $page_id;
}

/**
 * Fucntion changes company_id on child pages.
 *
 * @param integer $page_id
 * @param integer $new_company_id
 */
function fn_change_page_company($page_id, $new_company_id)
{
	$current_path = db_get_field("SELECT id_path FROM ?:pages WHERE page_id = ?i", $page_id);
	if (!empty($current_path)) {
		db_query("UPDATE ?:pages SET company_id = ?i WHERE id_path LIKE ?l", $new_company_id, "$current_path/%");
	}
}

//
// Change parent page field
//
function fn_change_page_parent($page_id, $new_parent_id)
{

	if (!empty($page_id)) {

		//$page_data['localization'] = empty($page_data['localization']) ? '' : fn_implode_localizations($page_data['localization']);

		$new_parent_path = db_get_field("SELECT id_path FROM ?:pages WHERE page_id = ?i", $new_parent_id);
		$current_path = db_get_field("SELECT id_path FROM ?:pages WHERE page_id = ?i", $page_id);

		if (!empty($new_parent_path) && !empty($current_path)) {
			db_query("UPDATE ?:pages SET parent_id = ?i, id_path = ?s WHERE page_id = ?i", $new_parent_id, "$new_parent_path/$page_id", $page_id);
			db_query("UPDATE ?:pages SET id_path = CONCAT(?s, SUBSTRING(id_path, ?i)) WHERE id_path LIKE ?l", "$new_parent_path/$page_id/", (strlen($current_path."/") + 1), "$current_path/%");

		} elseif (empty($new_parent_path) && !empty($current_path)) {
			db_query("UPDATE ?:pages SET parent_id = ?i, id_path = ?s WHERE page_id = ?i", $new_parent_id, $page_id, $page_id);
			db_query("UPDATE ?:pages SET id_path = CONCAT(?s, SUBSTRING(id_path, ?i)) WHERE id_path LIKE ?l", "$page_id/", (strlen($current_path."/") + 1), "$current_path/%");
		}
		return true;
	}

	return false;
}

function fn_clone_page($page_id)
{
	if (!fn_check_company_id('pages', 'page_id', $page_id)) {
		fn_company_access_denied_notification(false);
		return false;
	}
	
	// Clone main data
	$data = db_get_row("SELECT * FROM ?:pages WHERE page_id = ?i", $page_id);
	unset($data['page_id']);
	$data['status'] = 'D';

	$new_page_id = db_query("INSERT INTO ?:pages ?e", $data);

	// Update parent-child deps
	$id_path = explode('/', $data['id_path']);
	array_pop($id_path);
	$id_path[] = $new_page_id;
	db_query("UPDATE ?:pages SET id_path = ?s WHERE page_id = ?i", implode('/', $id_path), $new_page_id);

	// Clone descriptions
	$data = db_get_array("SELECT * FROM ?:page_descriptions WHERE page_id = ?i", $page_id);
	foreach ($data as $v) {
		$v['page_id'] = $new_page_id;
		if ($v['lang_code'] == CART_LANGUAGE) {
			$orig_name = $v['page'];
			$new_name = $v['page'] . ' [CLONE]';
		}

		$v['page'] .= ' [CLONE]';
		db_query("INSERT INTO ?:page_descriptions ?e", $v);
	}

	fn_clone_block_links('pages', $page_id, $new_page_id);

	fn_set_hook('clone_page', $page_id, $new_page_id);

	return array('page_id' => $new_page_id, 'orig_name' => $orig_name, 'page' => $new_name);
}

function fn_get_page_object_by_type($page_type = '')
{
	$types = array (
		PAGE_TYPE_TEXT => array(
			'single' => 'page',
			'name' => 'pages',
			'add_name' => 'add_page',
			'edit_name' => 'editing_page',
			'new_name' => 'new_page',
		),
		PAGE_TYPE_LINK => array(
			'single' => 'link',
			'name' => 'links',
			'add_name' => 'add_link',
			'edit_name' => 'editing_link',
			'new_name' => 'new_link',
		),
	);

	fn_set_hook('page_object_by_type', $types);

	return empty($page_type) ? $types : $types[$page_type];
}

function fn_get_pages_plain_list()
{
	$params = array(
		'get_tree' => 'plain'
	);

	list($pages) = fn_get_pages($params);

	return $pages;
}


/** /Functions **/

?>