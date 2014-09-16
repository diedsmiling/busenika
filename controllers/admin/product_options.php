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
// $Id: product_options.php 10184 2010-07-23 11:11:24Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

fn_define('KEEP_UPLOADED_FILES', true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$suffix = '';
	if ($mode == 'add_exceptions') {
		foreach ($_REQUEST['add_options_combination'] as $k => $v) {

			$exist = fn_check_combination($v, $_REQUEST['product_id']);
			$_data = array(
				'product_id' => $_REQUEST['product_id'],
				'combination' => serialize($v)
			);

			if (!$exist) {
				db_query("INSERT INTO ?:product_options_exceptions ?e", $_data);
			} else {
				fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('exception_exist'));
			}
		}

		fn_update_exceptions($_REQUEST['product_id']);

		$suffix = ".exceptions?product_id=$_REQUEST[product_id]";
	}

	if ($mode == 'delete_exceptions') {
		db_query("DELETE FROM ?:product_options_exceptions WHERE exception_id IN (?n)", $_REQUEST['exception_ids']);

		$suffix = ".exceptions?product_id=$_REQUEST[product_id]";
	}
	if ($mode == 'add_combinations') {
		if (is_array($_REQUEST['add_inventory'])) {
			foreach ($_REQUEST['add_inventory'] as $k => $v) {
				$combination_hash = fn_generate_cart_id($_REQUEST['product_id'], array('product_options' => $_REQUEST['add_options_combination'][$k]));

				$combination = fn_get_options_combination($_REQUEST['add_options_combination'][$k]);

				$_data = array(
					'product_id' => $_REQUEST['product_id'],
					'combination_hash' => $combination_hash,
					'combination' => $combination,
				);

				$_data = fn_array_merge($v, $_data);

				db_query("REPLACE INTO ?:product_options_inventory ?e", $_data);
			}
		}

		$suffix = ".inventory?product_id=$_REQUEST[product_id]";
	}

	if ($mode == 'update_combinations') {

		// Updating images
		fn_attach_image_pairs('combinations', 'product_option', 0, CART_LANGUAGE, array(), 'product', $_REQUEST['product_id']);

		if (!empty($_REQUEST['inventory'])) {
			foreach ($_REQUEST['inventory'] as $k => $v) {
				db_query("UPDATE ?:product_options_inventory SET ?u WHERE combination_hash = ?s", $v, $k);
			}
		}

		$suffix = ".inventory?product_id=$_REQUEST[product_id]";
	}

	if ($mode == 'delete_combinations') {
		foreach ($_REQUEST['combination_hashes'] as $v) {
			fn_delete_image_pairs($v, 'product_option');
			db_query("DELETE FROM ?:product_options_inventory WHERE combination_hash = ?i", $v);
		}

		$suffix = ".inventory?product_id=$_REQUEST[product_id]";
	}

	// Apply global options to the selected products
	if ($mode == 'apply') {
		if (!empty($_REQUEST['apply_options']['options'])) {

			$_data = $_REQUEST['apply_options'];

			foreach ($_data['options'] as $key => $value) {
				$products_ids = empty($_data['product_ids']) ? array() : explode(',', $_data['product_ids']);

				foreach ($products_ids as $k) {
					$updated_products[$k] = db_get_row("SELECT a.product_id, b.product FROM ?:products as a LEFT JOIN ?:product_descriptions as b ON a.product_id = b.product_id AND lang_code = ?s WHERE a.product_id = ?i", CART_LANGUAGE, $k);
					if ($_data['link'] == 'N') {
						fn_clone_product_options(0, $k, $value);
					} else {
						db_query("REPLACE INTO ?:product_global_option_links (option_id, product_id) VALUES (?i, ?i)", $value, $k);
					}
				}
			}

			if (!empty($updated_products)) {
				$view->assign('updated_products', $updated_products);
				$_output = $view->display('views/products/components/products_m_viewupdated.tpl', false);
				fn_set_notification('N', fn_get_lang_var('notice'), $_output, true);
			}
		}

		$suffix = ".apply";
	}

	if ($mode == 'update') {
		
		if (!empty($_REQUEST['option_id'])) {
			$condition = fn_get_company_condition();
			$option_id = db_get_field("SELECT option_id FROM ?:product_options WHERE option_id = ?i $condition", $_REQUEST['option_id']);
			if (empty($option_id)) {
				fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('access_denied'));
				return array(CONTROLLER_STATUS_REDIRECT, "product_options.manage");	
			}
		}

		fn_trusted_vars('option_data', 'regexp');
		
		$option_id = fn_update_product_option($_REQUEST['option_data'], $_REQUEST['option_id'], DESCR_SL);

		if (Registry::is_exist('revisions') && empty($_REQUEST['product_id'])) {
			$revision = db_get_field("SELECT MAX(revision) as revision FROM ?:product_options WHERE option_id = ?i", $option_id);
			$global_opt_data = db_get_row("SELECT * FROM ?:product_options WHERE option_id = ?i AND revision = ?i", $option_id, $revision);
			$global_opt_descr = db_get_row("SELECT * FROM ?:product_options_descriptions WHERE option_id = ?i AND revision = ?i AND lang_code = ?s", $option_id, $revision, DESCR_SL);

			$status = Registry::get('revisions.working');
			Registry::set('revisions.working', true);

			db_query("REPLACE INTO ?:product_options ?e", $global_opt_data);
			db_query("REPLACE INTO ?:product_options_descriptions ?e", $global_opt_descr);

			fn_update_product_option($_REQUEST['option_data'], $option_id, DESCR_SL);

			Registry::set('revisions.working', $status);
		}

		if (!empty($_REQUEST['product_id'])) { // FIXME (when assigning page and current url will be removed from ajax)
			return array(CONTROLLER_STATUS_OK, "products.update?product_id=$_REQUEST[product_id]&selected_section=options");
		}

		$suffix = ".manage";
	}

	return array(CONTROLLER_STATUS_OK, "product_options$suffix");
}

//
// Product options combination inventory tracking
//
if ($mode == 'inventory') {

	fn_add_breadcrumb(fn_get_product_name($_REQUEST['product_id']), "products.update?product_id=$_REQUEST[product_id]&selected_section=options");

	// I think this should be removed, not good, must be done on xml menu level
	Registry::set('navigation.selected_tab', 'catalog');
	Registry::set('navigation.subsection', 'products');


	$inv_count = db_get_field("SELECT COUNT(*) FROM ?:product_options_inventory WHERE product_id = ?i", $_REQUEST['product_id']);

	$limit = fn_paginate(empty($_REQUEST['page']) ? 1 : $_REQUEST['page'], $inv_count, Registry::get('settings.Appearance.admin_elements_per_page'));

	$inventory = db_get_array("SELECT * FROM ?:product_options_inventory WHERE product_id = ?i ORDER BY position $limit", $_REQUEST['product_id']);

	foreach ($inventory as $k => $v) {
		$inventory[$k]['combination'] = fn_get_product_options_by_combination($v['combination']);

		$inventory[$k]['image_pairs'] = fn_get_image_pairs($v['combination_hash'], 'product_option', 'M', true, true, DESCR_SL);
	}

	$product_options = fn_get_product_options($_REQUEST['product_id'], DESCR_SL, true, true);
	$view->assign('product_inventory', db_get_field("SELECT tracking FROM ?:products WHERE product_id = ?i", $_REQUEST['product_id']));
	$view->assign('product_options', $product_options);
	$view->assign('inventory', $inventory);
//
// Product options exceptions
//
} elseif ($mode == 'exceptions') {

	fn_add_breadcrumb(fn_get_product_name($_REQUEST['product_id']), "products.update?product_id=$_REQUEST[product_id]&selected_section=options");

	// I think this should be removed, not good, must be done on xml menu level
	Registry::set('navigation.selected_tab', 'catalog');
	Registry::set('navigation.subsection', 'products');

	$exceptions = fn_get_product_exceptions($_REQUEST['product_id']);
	$product_options = fn_get_product_options($_REQUEST['product_id'], DESCR_SL, true);
	$product_data = fn_get_product_data($_REQUEST['product_id'], $auth, DESCR_SL, '', true, true, true, true);

	$view->assign('product_options', $product_options);
	$view->assign('exceptions', $exceptions);
	$view->assign('product_data', $product_data);
//
// Options list
//
} elseif ($mode == 'manage') {
	$params = $_REQUEST;

	list($product_options, $search, $product_options_count) = fn_get_product_global_options($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

	$view->assign('product_options', $product_options);
	$view->assign('object', 'global');
	
	if (empty($product_options) && defined('AJAX_REQUEST')) {
		$ajax->assign('force_redirection', "product_options.manage");
	}

//
// Apply options to products
//
} elseif ($mode == 'apply') {

	$condition = fn_get_company_condition('p.company_id', true, '', true);
	$product_options = db_get_array("SELECT p.*, d.* FROM ?:product_options as p LEFT JOIN ?:product_options_descriptions as d ON d.option_id = p.option_id AND d.lang_code = ?s WHERE p.product_id = 0 $condition", DESCR_SL);

	$view->assign('product_options', $product_options);

//
// Update option
//
} elseif ($mode == 'update') {

	$o_data = fn_get_product_option_data($_REQUEST['option_id']);

	$view->assign('option_data', $o_data);
	$view->assign('option_id', $_REQUEST['option_id']);
	$view->assign('companies', fn_get_short_companies());
	

//
// Delete option
//
} elseif ($mode == 'delete') {
	if (!empty($_REQUEST['option_id'])) {
		$p_id = db_get_field("SELECT product_id FROM ?:product_options WHERE option_id = ?i", $_REQUEST['option_id']);

		if (!empty($_REQUEST['product_id']) && empty($p_id)) { // we're deleting global option from the product
			db_query("DELETE FROM ?:product_global_option_links WHERE product_id = ?i AND option_id = ?i", $_REQUEST['product_id'], $_REQUEST['option_id']);

		} else {
			fn_delete_product_option($_REQUEST['option_id']);
		}

		if (empty($_REQUEST['product_id']) && empty($p_id)) { // we're deleting global option itself
			db_query("DELETE FROM ?:product_global_option_links WHERE option_id = ?i", $_REQUEST['option_id']);
		}
	}
	if (!empty($_REQUEST['product_id'])) {
		$_options = fn_get_product_options($_REQUEST['product_id']);
		if (empty($_options)) {
			$view->display('views/product_options/manage.tpl');
		}
		exit();
	}
	return array(CONTROLLER_STATUS_REDIRECT, "product_options.manage");

} elseif ($mode == 'rebuild_combinations') {
	fn_rebuild_product_options_inventory($_REQUEST['product_id']);

	return array(CONTROLLER_STATUS_OK, "product_options.inventory?product_id=$_REQUEST[product_id]");

} elseif ($mode == 'delete_combination') {
	if (!empty($_REQUEST['combination_hashe'])) {
		fn_delete_image_pairs($_REQUEST['combination_hashe'], 'product_option');
		db_query("DELETE FROM ?:product_options_inventory WHERE combination_hash = ?i", $_REQUEST['combination_hashe']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "product_options.inventory?product_id=$_REQUEST[product_id]");
} elseif ($mode == 'delete_exception') {
	if (!empty($_REQUEST['exception_id'])) {
		db_query("DELETE FROM ?:product_options_exceptions WHERE exception_id = ?i", $_REQUEST['exception_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "product_options.exceptions?product_id=$_REQUEST[product_id]");
}


if (!empty($_REQUEST['product_id'])) {
	$view->assign('product_id', $_REQUEST['product_id']);
}

function fn_get_product_option_data($option_id, $lang_code = DESCR_SL)
{
	$extra_variant_fields = '';

	fn_set_hook('get_product_options', $extra_variant_fields);

	$opt = db_get_row("SELECT a.*, b.option_name, b.option_text, b.description, b.inner_hint, b.incorrect_message, b.comment FROM ?:product_options as a LEFT JOIN ?:product_options_descriptions as b ON a.option_id = b.option_id AND b.lang_code = ?s LEFT JOIN ?:product_global_option_links as c ON c.option_id = a.option_id WHERE a.option_id = ?i ORDER BY a.position", $lang_code, $option_id);

	if (!empty($opt)) {
		$_cond = ($opt['option_type'] == 'C') ? ' AND a.position = 1' : '';
		$opt['variants'] = db_get_hash_array("SELECT a.variant_id, a.position, a.modifier, a.modifier_type, a.weight_modifier, a.weight_modifier_type, a.status, $extra_variant_fields b.variant_name FROM ?:product_option_variants as a LEFT JOIN ?:product_option_variants_descriptions as b ON a.variant_id = b.variant_id AND b.lang_code = ?s WHERE a.option_id = ?i $_cond ORDER BY a.position", 'variant_id', $lang_code, $option_id);

		if (!empty($opt['variants'])) {
			foreach ($opt['variants'] as $k => $v) {
				$opt['variants'][$k]['image_pair'] = fn_get_image_pairs($v['variant_id'], 'variant_image', 'V', true, true, $lang_code);
			}
		}
	}

	return $opt;
}

/**
 * Function returns a array of product options using some params
 *
 * @param array $params - array of params
 * @param int $items_per_page - items per page
 * @param $lang_code - language code
 * @return array ($product_options, $params, $product_options_count)
 */
function fn_get_product_global_options($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
	// Init filter
	$params = fn_init_view('product_global_options', $params);

	$default_params = array(
		'product_id' => 0,
		'page' => 1
	);

	$params = array_merge($default_params, $params);

	$fields = array (
		'?:product_options.*',
		'?:product_options_descriptions.*',
	);

	$condition = $join = '';
	
	$condition .= fn_get_company_condition('company_id', true, '', true);

	$join .= db_quote("LEFT JOIN ?:product_options_descriptions ON ?:product_options_descriptions.option_id = ?:product_options.option_id AND ?:product_options_descriptions.lang_code = ?s ", $lang_code);

	$order = 'ORDER BY position';

	$params['product_id'] = !empty($params['product_id']) ? $params['product_id'] : 0;
	$condition .= db_quote(" AND ?:product_options.product_id = ?i", $params['product_id']);

	$limit = '';
	if (!empty($items_per_page)) {
		$total = db_get_field("SELECT COUNT(*) FROM ?:product_options $join WHERE 1 $condition");
		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}

	$data = db_get_array("SELECT " . implode(', ', $fields) . " FROM ?:product_options $join WHERE 1 $condition $order $limit ");

	return array($data, $params, $total);
}

?>