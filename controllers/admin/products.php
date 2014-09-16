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
// $Id: products.php 10598 2010-09-03 11:59:33Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$_REQUEST['product_id'] = empty($_REQUEST['product_id']) ? 0 : $_REQUEST['product_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$suffix = '';

	// Define trusted variables that shouldn't be stripped
	fn_trusted_vars (
		'product_data',
		'override_products_data',
		'product_files_descriptions',
		'add_product_files_descriptions',
		'products_data',
		'product_file'
	);

	//
	// Processing additon of new product element
	//
	if ($mode == 'add') {
		if (!empty($_REQUEST['product_data']['product']) && !empty($_REQUEST['product_data']['main_category'])) {  // Checking for required fields for new product

			// Adding product record
			$product_id = fn_update_product($_REQUEST['product_data']);
			
			if (!empty($product_id)) {
				// Attach main product images pair
				fn_attach_image_pairs('product_main', 'product', $product_id, DESCR_SL);

				// Attach additional product images
				fn_attach_image_pairs('product_add_additional', 'product', $product_id, DESCR_SL);

				$_data = array (
					'product_id' => $product_id,
					'link_type' => 'M',
					'category_id' => $_REQUEST['product_data']['main_category']
				);
				db_query("INSERT INTO ?:products_categories ?e", $_data);
				fn_update_product_count(array($_REQUEST['product_data']['main_category']));

				if (!empty($_REQUEST['product_data']['add_categories'])) {
					$_data = array (
						'product_id' => $product_id,
						'link_type' => 'A',
					);

					$_add_categories = explode(',', $_REQUEST['product_data']['add_categories']);
					foreach ($_add_categories as $c_id) {
						// check if main category already exists
						if (is_numeric($c_id)) {
							$is_ex = db_get_field("SELECT COUNT(*) FROM ?:products_categories WHERE product_id = ?i AND category_id = ?i", $product_id, $c_id);
							if (!empty($is_ex)) {
								continue;
							}
							$_data['category_id'] = $c_id;
							db_query('INSERT INTO ?:products_categories ?e', $_data);
						}
					}

					fn_update_product_count($_add_categories);
				}
			}

			// -----------------------
			$suffix = ".update?product_id=$product_id";
		} else  {
			$suffix = ".add";
		}
	}

	//
	// Apply Global Option
	//
	if ($mode == 'apply_global_option') {

		if ($_REQUEST['global_option']['link'] == 'N') {
			fn_clone_product_options(0, $_REQUEST['product_id'], $_REQUEST['global_option']['id']);
		} else {
			db_query("REPLACE INTO ?:product_global_option_links (option_id, product_id) VALUES(?i, ?i)", $_REQUEST['global_option']['id'], $_REQUEST['product_id']);
		}
		$suffix = ".update?product_id=$_REQUEST[product_id]";
	}
	//
	// Processing updating of product element
	//
	if ($mode == 'update') {
		if (!empty($_REQUEST['product_data']['product'])) {
			// Updating product record
			fn_update_product($_REQUEST['product_data'], $_REQUEST['product_id'], DESCR_SL);

			// Updating product associations with additional categories

			$_main_category = db_get_field("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = 'M'", $_REQUEST['product_id']);
			$_add_categories = db_get_fields("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = 'A'", $_REQUEST['product_id']);

			if ($_REQUEST['product_data']['main_category'] != $_main_category) {
				db_query("DELETE FROM ?:products_categories WHERE product_id = ?i AND category_id = ?i", $_REQUEST['product_id'], $_REQUEST['product_data']['main_category']);
				db_query("UPDATE ?:products_categories SET ?u WHERE product_id = ?i AND link_type = 'M'", array('category_id' => $_REQUEST['product_data']['main_category'], 'position' => 0), $_REQUEST['product_id']);
				fn_update_product_count(array($_main_category, $_REQUEST['product_data']['main_category']));
			}

			sort($_add_categories, SORT_NUMERIC);

			$add_categories = (!empty($_REQUEST['product_data']['add_categories'])) ? explode(',', $_REQUEST['product_data']['add_categories']) : array();
			sort($add_categories, SORT_NUMERIC);

			if (in_array(0, $add_categories)) {
				array_splice($add_categories, array_search(0, $add_categories), 1);
			}

			if ($add_categories != $_add_categories) {
				$delete_ids = array_diff($add_categories, $_add_categories);
				if (empty($delete_ids)) {
					$delete_ids = array_diff($_add_categories, $add_categories);
				}
				db_query("DELETE FROM ?:products_categories WHERE product_id = ?i AND category_id IN (?n) AND link_type = 'A'", $_REQUEST['product_id'], $delete_ids);
				fn_update_product_count($delete_ids);

				$new_ids = array_diff($add_categories, $_add_categories);
				$_data = array (
					'product_id' => $_REQUEST['product_id'],
					'link_type' => 'A',
				);

				foreach ($new_ids as $c_id) {
					// check if main category already exists
					$is_ex = db_get_field("SELECT COUNT(*) FROM ?:products_categories WHERE product_id = ?i AND category_id = ?i", $_REQUEST['product_id'], $c_id);
					if (!empty($is_ex)) {
						continue;
					}
					$_data['category_id'] = $c_id;
					db_query("INSERT INTO ?:products_categories ?e", $_data);
				}
				fn_update_product_count($new_ids);
			}

			// Update main images pair
			fn_attach_image_pairs('product_main', 'product', $_REQUEST['product_id'], DESCR_SL);

			// Update additional images
			fn_attach_image_pairs('product_additional', 'product', $_REQUEST['product_id'], DESCR_SL);

			// Adding new additional images
			fn_attach_image_pairs('product_add_additional', 'product', $_REQUEST['product_id'], DESCR_SL);
		}

		$suffix = ".update?product_id=$_REQUEST[product_id]" . (!empty($_REQUEST['product_data']['block_id']) ? "&selected_block_id=" . $_REQUEST['product_data']['block_id'] : "");
	}

	//
	// Processing mulitple addition of new product elements
	//
	if ($mode == 'm_add') {

		if (is_array($_REQUEST['products_data'])) {
			$p_ids = array();
			foreach ($_REQUEST['products_data'] as $k => $v) {
				if (!empty($v['product']) && !empty($v['main_category'])) {  // Checking for required fields for new product
					$p_id = fn_update_product($v);
					if (!empty($p_id)) {
						$p_ids[] = $p_id;

						// Adding association with main category for product
						$_data = array (
							'product_id' => $p_id,
							'link_type' => 'M',
							'category_id' => $v['main_category'],
							'position' => $v['position'],
						);
						db_query("INSERT INTO ?:products_categories ?e", $_data);
						fn_update_product_count(array($v['main_category']));

						unset($_data);
					}
				}
			}

			if (!empty($p_ids)) {
				fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_products_added'));
			}
		}
		$suffix = ".manage" . (empty($p_ids) ? "" : "?pid[]=" . implode('&pid[]=', $p_ids));
	}

	//
	// Processing multiple updating of product elements
	//
	if ($mode == 'm_update') {
		// Update multiple products data

		if (!empty($_REQUEST['products_data'])) {
			// Update images
			fn_attach_image_pairs('product_main', 'product', 0, DESCR_SL);

			foreach ($_REQUEST['products_data'] as $k => $v) {
				if (!empty($v['product'])) {  // Checking for required fields for new product
					fn_update_product($v, $k, DESCR_SL);

					// Updating product association with main category
					if (!empty($v['main_category'])) {
						$main_category_id = db_get_field("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = 'M'", $k);

						if ($v['main_category'] != $main_category_id) {
							db_query("DELETE FROM ?:products_categories WHERE product_id = ?i AND category_id = ?i", $k, $v['main_category']);
							db_query("UPDATE ?:products_categories SET ?u WHERE product_id = ?i AND link_type = 'M'", array('category_id' => $v['main_category'], 'position' => 0), $k);
							fn_update_product_count(array($main_category_id, $v['main_category']));
						}
					}

					// Updating product association with secondary categories
					if (isset($v['add_categories'])) {
						$secondary_category_ids = db_get_fields("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = 'A'", $k);

						@sort($secondary_category_ids, SORT_NUMERIC);

						if (!empty($v['add_categories'])) {
							$v['add_categories'] = explode(',', $v['add_categories']);
							sort($v['add_categories'], SORT_NUMERIC);
						}

						if ($v['add_categories'] != $secondary_category_ids) {
							$delete_ids = array_diff((array)$secondary_category_ids, (array)$v['add_categories']);
							db_query("DELETE FROM ?:products_categories WHERE product_id = ?i AND category_id IN (?n) AND link_type = 'A'", $k, $delete_ids);
							fn_update_product_count($delete_ids);

							$new_ids = array_diff((array)$v['add_categories'], (array)$secondary_category_ids);
							$_data = array (
								'product_id' => $k,
								'link_type' => 'A',
							);
							foreach ($new_ids as $c_id) {
								// check if main category already exists
								$is_ex = db_get_field("SELECT COUNT(*) FROM ?:products_categories WHERE product_id = ?i AND category_id = ?i", $k, $c_id);
								if (!empty($is_ex)) {
									continue;
								}
								$_data['category_id'] = $c_id;
								db_query("INSERT INTO ?:products_categories ?e", $_data);
							}
							fn_update_product_count($new_ids);
						}
					}


					// Updating products position in category
					if (isset($v['position']) && !empty($_REQUEST['category_id'])) {
						db_query("UPDATE ?:products_categories SET position = ?i WHERE category_id = ?i AND product_id = ?i", $v['position'], $_REQUEST['category_id'], $k);
					}
				}
			}
		}
		$suffix = ".manage";
	}

	//
	// Processing global updating of product elements
	//

	if ($mode == 'global_update') {

		fn_global_update($_REQUEST['update_data']);

		$suffix = '.global_update';
	}

	//
	// Override multiple products with the one value
	//
	if ($mode == 'm_override') {
		// Update multiple products data
		if (!empty($_SESSION['product_ids'])) {

			$product_data = !empty($_REQUEST['override_products_data']) ? $_REQUEST['override_products_data'] : array();
			if (isset($product_data['avail_since'])) {
				$product_data['avail_since'] = fn_parse_date($product_data['avail_since']);
			}
			if (isset($product_data['timestamp'])) {
				$product_data['timestamp'] = fn_parse_date($product_data['timestamp']);
			}

			fn_define('KEEP_UPLOADED_FILES', true);
			foreach ($_SESSION['product_ids'] as $_o => $p_id) {

				// Update product
				fn_update_product($product_data, $p_id, DESCR_SL);

				// Updating product association with main category
				if (!empty($product_data['main_category'])) {

					$main_cat_id = db_get_field("SELECT category_id FROM ?:products_categories WHERE product_id = ?i AND link_type = 'M'", $p_id);

					if ($product_data['main_category'] != $main_cat_id) {
						db_query("DELETE FROM ?:products_categories WHERE product_id = ?i AND category_id = ?i AND link_type = 'A'", $p_id, $product_data['main_category']);
						db_query("UPDATE ?:products_categories SET category_id = ?i WHERE product_id = ?i AND link_type = 'M'", $product_data['main_category'], $p_id);
						fn_update_product_count(array($main_cat_id, $product_data['main_category']));
					}
				}

				// Updating product association with secondary categories
				if (isset($product_data['add_categories'])) {
					db_query("DELETE FROM ?:products_categories WHERE product_id = ?i AND link_type = 'A'", $p_id);
					$_data = array (
						'product_id' => $p_id,
						'link_type' => 'A'
					);

					if (!empty($product_data['add_categories'])) {

						$_cids = explode(',', $product_data['add_categories']);

						foreach ($_cids as $c_id) {
							if (!empty($product_data['main_category']) && $product_data['main_category'] == $c_id) {
								continue;
							}
							$_data['category_id'] = $c_id;
							db_query("REPLACE INTO ?:products_categories ?e", $_data);
						}

						fn_update_product_count($_cids);
					}
				}

				// Updating images
				fn_attach_image_pairs('product_main', 'product', $p_id, DESCR_SL);
			}

		}
	}


	//
	// Processing deleting of multiple product elements
	//
	if ($mode == 'm_delete') {
		if (isset($_REQUEST['product_ids'])) {
			foreach ($_REQUEST['product_ids'] as $v) {
				fn_delete_product($v);
			}
		}
		unset($_SESSION['product_ids']);
		fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_products_have_been_deleted'));
		$suffix = ".manage";

	}
	//
	// Processing clonning of multiple product elements
	//
	if ($mode == 'm_clone') {
		$p_ids = array();
		if (!empty($_REQUEST['product_ids'])) {
			foreach ($_REQUEST['product_ids'] as $v) {
				$pdata = fn_clone_product($v);
				$p_ids[] = $pdata['product_id'];
			}

			fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_products_cloned'));
		}
		$suffix = ".manage?pid[]=" . implode('&pid[]=', $p_ids);
		unset($_REQUEST['redirect_url'], $_REQUEST['page']); // force redirection
	}

	//
	// Storing selected fields for using in m_update mode
	//
	if ($mode == 'store_selection') {

		if (!empty($_REQUEST['product_ids'])) {
			$_SESSION['product_ids'] = $_REQUEST['product_ids'];
			$_SESSION['selected_fields'] = $_REQUEST['selected_fields'];

			unset($_REQUEST['redirect_url']);

			$suffix = ".m_update";
		} else {
			$suffix = ".manage";
		}
	}

	//
	// Add edp files to the product
	//
	if ($mode == 'update_file') {

		$uploaded_data = fn_filter_uploaded_data('base_file');
		$uploaded_preview_data = fn_filter_uploaded_data('file_preview');

		db_query("UPDATE ?:products SET is_edp = 'Y' WHERE product_id = ?i", $_REQUEST['product_id']);


		$revisions = Registry::get('revisions');

		if (!empty($revisions['objects']['product']['tables'])) {
			$revision_subdir = '_rev';
		} else {
			$revision_subdir = '';
		}

		if (!is_dir(substr(DIR_DOWNLOADS, 0, -1) . $revision_subdir . '/' . $_REQUEST['product_id'])) {
			if (fn_mkdir(substr(DIR_DOWNLOADS, 0, -1) . $revision_subdir . '/' . $_REQUEST['product_id']) == false) {
				$msg = str_replace('[directory]', substr(DIR_DOWNLOADS, 0, -1) . $revision_subdir . '/' . $_REQUEST['product_id'], fn_get_lang_var('text_cannot_create_directory'));
				fn_set_notification('E',fn_get_lang_var('error'), $msg);
			}
		}

		$_file_id = empty($_REQUEST['file_id']) ? 0 : $_REQUEST['file_id'];

		$product_file = $_REQUEST['product_file'];

		if (!empty($uploaded_data[$_file_id])) {
			$product_file['file_name'] = empty($product_file['file_name']) ? $uploaded_data[$_file_id]['name'] : $product_file['file_name'];
		}

		// Update file data
		if (empty($_file_id)) {
			$product_file['product_id'] = $_REQUEST['product_id'];
			$product_file['file_id'] = $file_id = db_query('INSERT INTO ?:product_files ?e', $product_file);

			foreach ((array)Registry::get('languages') as $product_file['lang_code'] => $v) {
				db_query('INSERT INTO ?:product_file_descriptions ?e', $product_file);
			}
		} else {
			db_query('UPDATE ?:product_files SET ?u WHERE file_id = ?i', $product_file, $_file_id);
			db_query('UPDATE ?:product_file_descriptions SET ?u WHERE file_id = ?i AND lang_code = ?s', $product_file, $_file_id, DESCR_SL);
			$file_id = $_file_id;
		}


		// Copy base file
		if (!empty($uploaded_data[$_file_id])) {
			fn_copy_product_files($file_id, $uploaded_data[$_file_id], $_REQUEST['product_id']);
		}

		// Copy preview file
		if (!empty($uploaded_preview_data[$_file_id])) {
			fn_copy_product_files($file_id, $uploaded_preview_data[$_file_id], $_REQUEST['product_id'], 'preview');
		}

		$suffix = ".update?product_id=$_REQUEST[product_id]";
	}

	if ($mode == 'export_range') {
		if (!empty($_REQUEST['product_ids'])) {
			if (empty($_SESSION['export_ranges'])) {
				$_SESSION['export_ranges'] = array();
			}

			if (empty($_SESSION['export_ranges']['products'])) {
				$_SESSION['export_ranges']['products'] = array('pattern_id' => 'products');
			}

			$_SESSION['export_ranges']['products']['data'] = array('product_id' => $_REQUEST['product_ids']);

			unset($_REQUEST['redirect_url']);
			return array(CONTROLLER_STATUS_REDIRECT, "exim.export?section=products&pattern_id=" . $_SESSION['export_ranges']['products']['pattern_id']);
		}
	}

	return array(CONTROLLER_STATUS_OK, "products$suffix");
}

//
// 'Management' page
//
if ($mode == 'manage') {
	unset($_SESSION['product_ids']);
	unset($_SESSION['selected_fields']);

	$params = $_REQUEST;
	$params['type'] = 'extended';

	list($products, $search, $product_count) = fn_get_products($params, Registry::get('settings.Appearance.admin_products_per_page'), DESCR_SL);
	//делаем проверку на наличие изображения и добавляем новое значение в массив	
	foreach($products as $k=>$pr)
		{		
				$sql_check = db_query("SELECT * FROM  `cscart_images_links` WHERE  `object_id` = ?i", $pr['product_id']);		
				$sql_rows = mysql_num_rows($sql_check);
					if($sql_rows>0)
						 {
							  $s_image_id = mysql_result($sql_check, 0, 3); 
							  if($s_image_id != 0)
								{
									$has_image = 1;		
								}
							  else
								{
									$has_image = 0;	
								}	
							  $s_image_id = mysql_result($sql_check, 0, 4); 
							  if($s_image_id != 0)
								{
									$has_image_detailed = 1;		
								}
							  else
								{
									$has_image_detailed = 0;	
								}		
								
						 }	 
				
				
				$products[$k]['hasimg'] = $has_image;
				$products[$k]['hasimg_detailed'] = $has_image_detailed;
		
		}	
	
	$view->assign('products', $products);
	$view->assign('search', $search);
	$view->assign('companies', fn_get_short_companies());

	if (!empty($_REQUEST['redirect_if_one']) && $product_count == 1) {
		return array(CONTROLLER_STATUS_REDIRECT, "products.update?product_id={$products[0]['product_id']}");
	}

	$selected_fields = array(
		array(
			'name' => '[data][popularity]',
			'text' => fn_get_lang_var('popularity')
		),
		array(
			'name' => '[data][status]',
			'text' => fn_get_lang_var('status'),
			'disabled' => 'Y'
		),
		array(
			'name' => '[data][product]',
			'text' => fn_get_lang_var('product_name'),
			'disabled' => 'Y'
		),
		array(
			'name' => '[data][price]',
			'text' => fn_get_lang_var('price')
		),
		array(
			'name' => '[data][list_price]',
			'text' => fn_get_lang_var('list_price')
		),
		array(
			'name' => '[data][short_description]',
			'text' => fn_get_lang_var('short_description')
		),
		array(
			'name' => '[main_category]',
			'text' => fn_get_lang_var('main_category')
		),
		array(
			'name' => '[add_categories]',
			'text' => fn_get_lang_var('additional_categories')
		),
		array(
			'name' => '[data][full_description]',
			'text' => fn_get_lang_var('full_description')
		),
		array(
			'name' => '[data][search_words]',
			'text' => fn_get_lang_var('search_words')
		),
		array(
			'name' => '[data][meta_keywords]',
			'text' => fn_get_lang_var('meta_keywords')
		),
		array(
			'name' => '[data][meta_description]',
			'text' => fn_get_lang_var('meta_description')
		),
		array(
			'name' => '[data][usergroup_ids]',
			'text' => fn_get_lang_var('usergroups')
		),
		array(
			'name' => '[main_pair]',
			'text' => fn_get_lang_var('image_pair')
		),
		array(
			'name' => '[data][min_qty]',
			'text' => fn_get_lang_var('min_order_qty')
		),
		array(
			'name' => '[data][max_qty]',
			'text' => fn_get_lang_var('max_order_qty')
		),
		array(
			'name' => '[data][qty_step]',
			'text' => fn_get_lang_var('quantity_step')
		),
		array(
			'name' => '[data][list_qty_count]',
			'text' => fn_get_lang_var('list_quantity_count')
		),
		array(
			'name' => '[data][product_code]',
			'text' => fn_get_lang_var('product_code')
		),
		array(
			'name' => '[data][weight]',
			'text' => fn_get_lang_var('weight')
		),
		array(
			'name' => '[data][shipping_freight]',
			'text' => fn_get_lang_var('shipping_freight')
		),
		array(
			'name' => '[data][is_edp]',
			'text' => fn_get_lang_var('downloadable')
		),
		array(
			'name' => '[data][edp_shipping]',
			'text' => fn_get_lang_var('edp_enable_shipping')
		),
		array(
			'name' => '[data][tracking]',
			'text' => fn_get_lang_var('inventory')
		),
		array(
			'name' => '[data][free_shipping]',
			'text' => fn_get_lang_var('free_shipping')
		),
		array(
			'name' => '[data][feature_comparison]',
			'text' => fn_get_lang_var('feature_comparison')
		),
		array(
			'name' => '[data][zero_price_action]',
			'text' => fn_get_lang_var('zero_price_action')
		),
		array(
			'name' => '[data][taxes]',
			'text' => fn_get_lang_var('taxes')
		),
		array(
			'name' => '[data][features]',
			'text' => fn_get_lang_var('features')
		),
		array(
			'name' => '[data][page_title]',
			'text' => fn_get_lang_var('page_title')
		),
		array(
			'name' => '[data][timestamp]',
			'text' => fn_get_lang_var('created_date')
		),
		array(
			'name' => '[data][amount]',
			'text' => fn_get_lang_var('quantity')
		),
		array(
			'name' => '[data][avail_since]',
			'text' => fn_get_lang_var('available_since')
		),
		array(
			'name' => '[data][buy_in_advance]',
			'text' => fn_get_lang_var('buy_in_advance')
		),
		array(
			'name' => '[data][localization]',
			'text' => fn_get_lang_var('localization')
		),
		array(
			'name' => '[data][details_layout]',
			'text' => fn_get_lang_var('product_details_layout')
		)
	);

	if (PRODUCT_TYPE == 'PROFESSIONAL' && Registry::get('settings.Suppliers.enable_suppliers') == 'Y') {
		$selected_fields[] = array(
			'name' => '[data][company_id]',
			'text' => fn_get_lang_var('supplier')
		);
	}

	$view->assign('selected_fields', $selected_fields);
	$filter_params = array(
		'get_fields' => true,
		'get_variants' => true
	);
	list($filters) = fn_get_product_filters($filter_params);
	$view->assign('filter_items', $filters);
	$feature_params = array(
		'get_fields' => true,
		'plain' => true,
		'variants' => true,
		'exclude_group' => true,
		'exclude_filters' => true
	);
	list($features) = fn_get_product_features($feature_params);

	$view->assign('feature_items', $features);
	$view->assign('product_count', $product_count);
}
//
// 'Global update' page
//
if ($mode == 'global_update') {
	fn_add_breadcrumb(fn_get_lang_var('products'), "products.manage");	
//
// 'Add new product' page
//
} elseif ($mode == 'add') {

	$view->assign('taxes', fn_get_taxes());
	$view->assign('companies', fn_get_short_companies());

	// [Breadcrumbs]
	fn_add_breadcrumb(fn_get_lang_var('products'), "products.manage.reset_view");
	fn_add_breadcrumb(fn_get_lang_var('search_results'), "products.manage.last_view");
	// [/Breadcrumbs]

	// [Page sections]
	Registry::set('navigation.tabs', array (
		'detailed' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'images' => array (
			'title' => fn_get_lang_var('images'),
			'js' => true
		),
		'categories' => array (
			'title' => fn_get_lang_var('additional_categories'),
			'js' => true
		),
		'qty_discounts' => array (
			'title' => fn_get_lang_var('qty_discounts'),
			'js' => true
		),
		'addons' => array (
			'title' => fn_get_lang_var('addons'),
			'js' => true
		),
	));
	// [/Page sections]

//
// 'Multiple products addition' page
//
} elseif ($mode == 'm_add') {

//
// 'product update' page
//
} elseif ($mode == 'update') {

	$selected_section = (empty($_REQUEST['selected_section']) ? 'detailed' : $_REQUEST['selected_section']);

	// Get current product data
	$product_data = fn_get_product_data($_REQUEST['product_id'], $auth, DESCR_SL, '', true, true, true, true);

	if (empty($product_data)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	fn_add_breadcrumb(fn_get_lang_var('products'), "products.manage.reset_view");

	fn_add_breadcrumb(fn_get_lang_var('search_results'), "products.manage.last_view");

	fn_add_breadcrumb(fn_get_lang_var('category') . ': ' . fn_get_category_name($product_data['main_category']), "products.manage.reset_view?cid=$product_data[main_category]");

	$taxes = fn_get_taxes();

	$view->assign('product_data', $product_data);
	$view->assign('taxes', $taxes);
	$view->assign('companies', fn_get_short_companies());

	$product_options = fn_get_product_options($_REQUEST['product_id'], DESCR_SL);
	if (!empty($product_options)) {
		$has_inventory = false;
		foreach ($product_options as $p) {
			if ($p['inventory'] == 'Y') {
				$has_inventory = true;
				break;
			}
		}
		$view->assign('has_inventory', $has_inventory);
	}
	$view->assign('product_options', $product_options);
	$view->assign('global_options', fn_get_product_options(0));

	// If the product is electronnicaly distributed, get the assigned files
	$view->assign('product_files', fn_get_product_files($_REQUEST['product_id']));

	// [Page sections]
	Registry::set('navigation.tabs', array (
		'detailed' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'images' => array (
			'title' => fn_get_lang_var('images'),
			'js' => true
		),
		'categories' => array (
			'title' => fn_get_lang_var('additional_categories'),
			'js' => true
		),
		'options' => array (
			'title' => fn_get_lang_var('options'),
			'js' => true
		),
		'qty_discounts' => array (
			'title' => fn_get_lang_var('qty_discounts'),
			'js' => true
		),
		'files' => array (
			'title' => fn_get_lang_var('files'),
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
	// [/Page sections]

	// If we have some additional product fields, lets add a tab for them
	if (!empty($product_data['product_features'])) {
		Registry::set('navigation.tabs.features', array (
			'title' => fn_get_lang_var('features'),
			'js' => true
		));
	}

	// [Block manager]
	$block_settings = fn_get_all_blocks('products');
	$view->assign('block_settings', $block_settings);
	list($blocks, $object_id) = fn_get_blocks(array('location' => 'products', 'all' => true, 'product_id' => $_REQUEST['product_id']), false, DESCR_SL);
	list($all_blocks) = fn_get_blocks(array('location' => 'all_pages', 'all' => true, 'block_properties_location' => 'products'), false);
	$blocks = fn_array_merge($blocks, $all_blocks, true);
	$blocks = fn_sort_blocks($object_id, 'products', $blocks);
	$blocks = fn_check_blocks_availability($blocks, $block_settings);
	$view->assign('location', $selected_section);
	$view->assign('blocks', $blocks);

	$view->assign('avail_positions', fn_get_available_group('products', $_REQUEST['product_id'], DESCR_SL));
	// [/Block manager]

//
// 'Mulitple products updating' page
//
} elseif ($mode == 'm_update') {

	if (empty($_SESSION['product_ids']) || empty($_SESSION['selected_fields']) || empty($_SESSION['selected_fields']['object']) || $_SESSION['selected_fields']['object'] != 'product') {
		return array(CONTROLLER_STATUS_REDIRECT, "products.manage");
	}

	fn_add_breadcrumb(fn_get_lang_var('products'), "products.manage");

	$product_ids = $_SESSION['product_ids'];
	$selected_fields = $_SESSION['selected_fields'];

	$field_groups = array (
		'A' => array ( // inputs
			'product' => 'products_data',
			'product_code' => 'products_data',
			'page_title' => 'products_data',
		),

		'B' => array ( // short inputs
			'price' => 'products_data',
			'list_price' => 'products_data',
			'amount' => 'products_data',
			'min_qty' => 'products_data',
			'max_qty' => 'products_data',
			'weight' => 'products_data',
			'shipping_freight' => 'products_data',
			'qty_step' => 'products_data',
			'list_qty_count' => 'products_data',
			'popularity' => 'products_data'
		),

		'C' => array ( // checkboxes
			'is_edp' => 'products_data',
			'edp_shipping' => 'products_data',
			'free_shipping' => 'products_data',
			'feature_comparison' => 'products_data',
			'buy_in_advance' => 'products_data'
		),

		'D' => array ( // textareas
			'short_description' => 'products_data',
			'full_description' => 'products_data',
			'meta_keywords' => 'products_data',
			'meta_description' => 'products_data',
			'search_words' => 'products_data',
		),
		'T' => array( // dates
			'timestamp' => 'products_data',
			'avail_since' => 'products_data',
		),
		'S' => array ( // selectboxes
			'status' => array (
				'name' => 'products_data',
				'variants' => array (
					'A' => 'active',
					'D' => 'disabled',
					'H' => 'hidden'
				),
			),
			'tracking' => array (
				'name' => 'products_data',
				'variants' => array (
					'O' => 'track_with_options',
					'B' => 'track_without_options',
					'D' => 'dont_track'
				),
			),
			'zero_price_action' => array (
				'name' => 'products_data',
				'variants' => array (
					'R' => 'zpa_refuse',
					'P' => 'zpa_permit',
					'A' => 'zpa_ask_price'
				),
			),
		),
		'E' => array ( // categories
			'main_category' => 'products_data',
			'add_categories' => 'products_data'
		),
		'L' => array( // miltiple selectbox (localization)
			'localization' => array(
				'name' => 'localization'
			),
		),
		'W' => array( // Product details layout
			'details_layout' => 'products_data'
		)
	);

	$data = array_keys($selected_fields['data']);
	$get_main_category = false;
	$get_add_categories = false;
	$get_main_pair = false;
	$get_taxes = false;

	$fields2update = $data;

	// Process fields that are not in products or product_descriptions tables
	if (!empty($selected_fields['main_category']) && $selected_fields['main_category'] == 'Y') {
		$get_main_category = true;
		$fields2update[] = 'main_category';
	}
	if (!empty($selected_fields['add_categories']) && $selected_fields['add_categories'] == 'Y') {
		$get_add_categories = true;
		$fields2update[] = 'add_categories';
	}
	if (!empty($selected_fields['main_pair']) && $selected_fields['main_pair'] == 'Y') {
		$get_main_pair = true;
		$fields2update[] = 'main_pair';
	}
	if (!empty($selected_fields['data']['taxes']) && $selected_fields['data']['taxes'] == 'Y') {
		$view->assign('taxes', fn_get_taxes());
		$fields2update[] = 'taxes';
		$get_taxes = true;
	}
	if (!empty($selected_fields['data']['features']) && $selected_fields['data']['features'] == 'Y') {
		$fields2update[] = 'features';

		// get features for categories of selected products only
		$id_paths = db_get_fields("SELECT ?:categories.id_path FROM ?:products_categories LEFT JOIN ?:categories ON ?:categories.category_id = ?:products_categories.category_id WHERE product_id IN (?n)", $product_ids);

		$_params = array(
			'variants' => true, 
			'category_ids' => array_unique(explode('/', implode('/', $id_paths)))
		);

		list($all_product_features) = fn_get_product_features($_params, 0, DESCR_SL);
		$view->assign('all_product_features', $all_product_features);
	}

	foreach($product_ids as $value){
		$products_data[$value] = fn_get_product_data($value, $auth, DESCR_SL, '?:products.*, ?:product_descriptions.*', false, $get_main_pair, $get_taxes);
	}

	$filled_groups = array();
	$field_names = array();

	foreach ($fields2update as $k => $field) {
		if ($field == 'main_pair') {
			$desc = 'image_pair';
		} elseif ($field == 'tracking') {
			$desc = 'inventory';
		} elseif ($field == 'edp_shipping') {
			$desc = 'downloadable_shipping';
		} elseif ($field == 'is_edp') {
			$desc = 'downloadable';
		} elseif ($field == 'timestamp') {
			$desc = 'created_date';
		} elseif ($field == 'add_categories') {
			$desc = 'additional_categories';
		} elseif ($field == 'status') {
			$desc = 'status';
		} elseif ($field == 'avail_since') {
			$desc = 'available_since';
		} elseif ($field == 'min_qty') {
			$desc = 'min_order_qty';
		} elseif ($field == 'max_qty') {
			$desc = 'max_order_qty';
		} elseif ($field == 'qty_step') {
			$desc = 'quantity_step';
		} elseif ($field == 'list_qty_count') {
			$desc = 'list_quantity_count';
		} elseif ($field == 'usergroup_ids') {
			$desc = 'usergroups';
		} elseif ($field == 'details_layout') {
			$desc = 'product_details_layout';
		} else {
			$desc = $field;
		}

		if (!empty($field_groups['A'][$field])) {
			$filled_groups['A'][$field] = fn_get_lang_var($desc);
			continue;
		} elseif (!empty($field_groups['B'][$field])) {
			$filled_groups['B'][$field] = fn_get_lang_var($desc);
			continue;
		} elseif (!empty($field_groups['C'][$field])) {
			$filled_groups['C'][$field] = fn_get_lang_var($desc);
			continue;
		} elseif (!empty($field_groups['D'][$field])) {
			$filled_groups['D'][$field] = fn_get_lang_var($desc);
			continue;
		} elseif (!empty($field_groups['S'][$field])) {
			$filled_groups['S'][$field] = fn_get_lang_var($desc);
			continue;
		} elseif (!empty($field_groups['T'][$field])) {
			$filled_groups['T'][$field] = fn_get_lang_var($desc);
			continue;
		} elseif (!empty($field_groups['E'][$field])) {
			$filled_groups['E'][$field] = fn_get_lang_var($desc);
			continue;
		} elseif (!empty($field_groups['L'][$field])) {
			$filled_groups['L'][$field] = fn_get_lang_var($desc);
			continue;
		} elseif (!empty($field_groups['W'][$field])) {
			$filled_groups['W'][$field] = fn_get_lang_var($desc);
			continue;
		}

		$field_names[$field] = fn_get_lang_var($desc);
	}


	ksort($filled_groups, SORT_STRING);

	$view->assign('field_groups', $field_groups);
	$view->assign('filled_groups', $filled_groups);

	$view->assign('field_names', $field_names);
	$view->assign('products_data', $products_data);

//
// Delete product
//
} elseif ($mode == 'delete') {

	if (!empty($_REQUEST['product_id'])) {
		$result = fn_delete_product($_REQUEST['product_id']);
		if ($result) {
			fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_product_has_been_deleted'));
		} else {
			return array(CONTROLLER_STATUS_REDIRECT, "products.update?product_id=$_REQUEST[product_id]");
		}
	}
	return array(CONTROLLER_STATUS_REDIRECT, "products.manage");

} elseif ($mode == 'getfile') {

	if (!empty($_REQUEST['file_id'])) {
		$revisions = Registry::get('revisions');

		if (!empty($revisions['objects']['product']['tables'])) {
			$revision_subdir = '_rev';
		} else {
			$revision_subdir = '';
		}
		
		if (empty($_REQUEST['file_type'])) {
			$column = 'file_path';
		} else {
			$column = 'preview_path';
		}
		
		$file_path = db_get_row("SELECT $column, product_id FROM ?:product_files WHERE file_id = ?i", $_REQUEST['file_id']);
		fn_get_file(DIR_DOWNLOADS . $file_path['product_id'] . '/' . $file_path[$column]);
	}

} elseif ($mode == 'clone') {
	if (!empty($_REQUEST['product_id'])) {
		$pid = $_REQUEST['product_id'];
		$pdata = fn_clone_product($pid);
		if (!empty($pdata['product_id'])) {
			$pid = $pdata['product_id'];
			$msg = fn_get_lang_var('text_product_cloned');
			$msg = str_replace('[product]', $pdata['orig_name'], $msg);
			fn_set_notification('N', fn_get_lang_var('notice'), $msg);
		}

		return array(CONTROLLER_STATUS_REDIRECT, "products.update?product_id=$pid");
	}

} elseif ($mode == 'delete_file') {

	if (!empty($_REQUEST['file_id'])) {
		$files_path = db_get_row("SELECT file_path, preview_path, product_id FROM ?:product_files WHERE file_id = ?i", $_REQUEST['file_id']);
		if (!empty($files_path['file_path'])) {
			unlink(DIR_DOWNLOADS . $files_path['product_id'] . '/' . $files_path['file_path']);
		}

		if (!empty($files_path['preview_path'])) {
			unlink(DIR_DOWNLOADS . $files_path['product_id'] . '/' . $files_path['preview_path']);
		}

		db_query("DELETE FROM ?:product_files WHERE file_id = ?i", $_REQUEST['file_id']);
		db_query("DELETE FROM ?:product_file_descriptions WHERE file_id = ?i", $_REQUEST['file_id']);

		$_files = fn_get_product_files($files_path['product_id']);
		if (empty($_files)) {
			$view->display('views/products/components/products_update_files.tpl');
		}
	}
	exit;
}elseif ($mode == 'dev_mode') {
	die('ada');

}

// ---------------------------------------------------- Related functions --------------------------------
//
// Add or update product
//
function fn_update_product($product_data, $product_id = 0, $lang_code = CART_LANGUAGE)
{
	$_data = $product_data;

	if (!empty($product_data['timestamp'])) {
		$_data['timestamp'] = fn_parse_date($product_data['timestamp']); // Minimal data for product record
	}

	if (!empty($product_data['avail_since'])) {
		$_data['avail_since'] = fn_parse_date($product_data['avail_since']);
	}

	if (isset($product_data['tax_ids'])) {
		$_data['tax_ids'] = empty($product_data['tax_ids']) ? '' : fn_create_set($product_data['tax_ids']);
	}

	if (isset($product_data['localization'])) {
		$_data['localization'] = empty($product_data['localization']) ? '' : fn_implode_localizations($_data['localization']);
	}
	
	if (isset($product_data['usergroup_ids'])) {
		$_data['usergroup_ids'] = empty($product_data['usergroup_ids']) ? '' : implode(',', $_data['usergroup_ids']);
	}

	if (Registry::get('settings.General.allow_negative_amount') == 'N' && isset($_data['amount'])) {
		$_data['amount'] = abs($_data['amount']);
	}

	// add new product
	if (empty($product_id)) {
		$create = true;
		// product title can't be empty
		if(empty($product_data['product'])) {
			return false;
		}

		$product_id = db_query("INSERT INTO ?:products ?e", $_data);

		if (empty($product_id)) {
			return false;
		}

		//
		// Adding same product descriptions for all cart languages
		//
		$_data = $product_data;
		$_data['product_id'] =	$product_id;
		$_data['product'] = trim($_data['product'], " -");

		foreach ((array)Registry::get('languages') as $_data['lang_code'] => $_v) {
			db_query("INSERT INTO ?:product_descriptions ?e", $_data);
		}

	// update product
	} else {
		if (isset($product_data['product']) && empty($product_data['product'])) {
			unset($product_data['product']);
		}

		db_query("UPDATE ?:products SET ?u WHERE product_id = ?i", $_data, $product_id);

		$_data = $product_data;
		if (!empty($_data['product'])){
			$_data['product'] = trim($_data['product'], " -");
		}
		db_query("UPDATE ?:product_descriptions SET ?u WHERE product_id = ?i AND lang_code = ?s", $_data, $product_id, $lang_code);
	}

	// Log product add/update
	fn_log_event('products', !empty($create) ? 'create' : 'update', array(
		'product_id' => $product_id
	));

	if (!empty($product_data['product_features'])) {
		$i_data = array(
			'product_id' => $product_id,
			'lang_code' => $lang_code
		);


		foreach ($product_data['product_features'] as $feature_id => $value) {

			// Check if feature is applicable for this product
			$id_paths = db_get_fields("SELECT ?:categories.id_path FROM ?:products_categories LEFT JOIN ?:categories ON ?:categories.category_id = ?:products_categories.category_id WHERE product_id = ?i", $product_id);

			$_params = array(
				'category_ids' => array_unique(explode('/', implode('/', $id_paths))),
				'feature_id' => $feature_id
			);
			list($_feature) = fn_get_product_features($_params);

			if (empty($_feature)) {
				$_feature = db_get_field("SELECT description FROM ?:product_features_descriptions WHERE feature_id = ?i AND lang_code = ?s", $feature_id, CART_LANGUAGE);
				$_product = db_get_field("SELECT product FROM ?:product_descriptions WHERE product_id = ?i AND lang_code = ?s", $product_id, CART_LANGUAGE);
				fn_set_notification('E', fn_get_lang_var('error'), str_replace(array('[feature_name]', '[product_name]'), array($_feature, $_product), fn_get_lang_var('product_feature_cannot_assigned')));
				continue;
			}

			$i_data['feature_id'] = $feature_id;
			unset($i_data['value']);
			unset($i_data['variant_id']);
			unset($i_data['value_int']);
			$feature_type = db_get_field("SELECT feature_type FROM ?:product_features WHERE feature_id = ?i", $feature_id);

			// Delete variants in current language
			if ($feature_type == 'T') {
				db_query("DELETE FROM ?:product_features_values WHERE feature_id = ?i AND product_id = ?i AND lang_code = ?s", $feature_id, $product_id, $lang_code);
			} else {
				db_query("DELETE FROM ?:product_features_values WHERE feature_id = ?i AND product_id = ?i", $feature_id, $product_id);
			}

			if ($feature_type == 'D') {
				$i_data['value_int'] = fn_parse_date($value);
			} elseif ($feature_type == 'M') {
				if (!empty($product_data['add_new_variant'][$feature_id]['variant'])) {
					$value = empty($value) ? array() : $value;
					$value[] = fn_add_feature_variant($feature_id, $product_data['add_new_variant'][$feature_id]);
				}
				if (!empty($value)) {
					foreach ($value as $variant_id) {
						foreach (Registry::get('languages') as $i_data['lang_code'] => $_d) { // insert for all languages
							$i_data['variant_id'] = $variant_id;
							db_query("REPLACE INTO ?:product_features_values ?e", $i_data);
						}
					}
				}
				continue;
			} elseif (in_array($feature_type, array('S', 'N', 'E'))) {
				if (!empty($product_data['add_new_variant'][$feature_id]['variant'])) {
					$i_data['variant_id'] = fn_add_feature_variant($feature_id, $product_data['add_new_variant'][$feature_id]);
				
				} elseif (!empty($value) && $value != 'disable_select') {
					if ($feature_type == 'N') {
						$i_data['value_int'] = db_get_field("SELECT variant FROM ?:product_feature_variant_descriptions WHERE variant_id = ?i AND lang_code = ?s", $value, CART_LANGUAGE);
					}
					$i_data['variant_id'] = $value;
				} else {
					continue;
				}
			} else {
				if ($value == '') {
					continue;
				}
				if ($feature_type == 'O') {
					$i_data['value_int'] = $value;
				} else {
					$i_data['value'] = $value;
				}
			}

			if ($feature_type != 'T') { // feature values are common for all languages, except text (T)
				foreach (Registry::get('languages') as $i_data['lang_code'] => $_d) {
					db_query("REPLACE INTO ?:product_features_values ?e", $i_data);
				}
			} else { // for text feature, update current language only
				$i_data['lang_code'] = $lang_code;
				db_query("INSERT INTO ?:product_features_values ?e", $i_data);
			}
		}
	}

	// Update product prices
	if (isset($product_data['price'])) {
		if (!isset($product_data['prices'])) {
			$product_data['prices'] = array();
			$skip_price_delete = true;
		}
		$_price = array (
			'price' => abs($product_data['price']),
			'lower_limit' => 1,
		);

		array_unshift($product_data['prices'], $_price);
	}

	if (!empty($product_data['prices'])) {
		if (empty($skip_price_delete)) {
			db_query("DELETE FROM ?:product_prices WHERE product_id = ?i", $product_id);
		}

		foreach ($product_data['prices'] as $v) {
			if (!empty($v['lower_limit'])) {
				$v['product_id'] = $product_id;
				db_query("REPLACE INTO ?:product_prices ?e", $v);
			}
		}
	}

	if (!empty($product_data['popularity'])) {
		$_data = array (
			'product_id' => $product_id,
			'total' => intval($product_data['popularity'])
		);
		
		db_query("INSERT INTO ?:product_popularity ?e ON DUPLICATE KEY UPDATE total = ?i", $_data, $product_data['popularity']);
	}

	fn_set_hook('update_product', $product_data, $product_id, $lang_code);

	return $product_id;
}

function fn_clone_product($product_id)
{
	// Clone main data
	$data = db_get_row("SELECT * FROM ?:products WHERE product_id = ?i", $product_id);
	unset($data['product_id']);
	$data['status'] = 'D';
	$pid = db_query("INSERT INTO ?:products ?e", $data);

	// Clone descriptions
	$data = db_get_array("SELECT * FROM ?:product_descriptions WHERE product_id = ?i", $product_id);
	foreach ($data as $v) {
		$v['product_id'] = $pid;
		if ($v['lang_code'] == CART_LANGUAGE) {
			$orig_name = $v['product'];
			$new_name = $v['product'].' [CLONE]';
		}

		$v['product'] .= ' [CLONE]';
		db_query("INSERT INTO ?:product_descriptions ?e", $v);
	}

	// Clone prices
	$data = db_get_array("SELECT * FROM ?:product_prices WHERE product_id = ?i", $product_id);
	foreach ($data as $v) {
		$v['product_id'] = $pid;
		unset($v['price_id']);
		db_query("INSERT INTO ?:product_prices ?e", $v);
	}

	// Clone categories links
	$data = db_get_array("SELECT * FROM ?:products_categories WHERE product_id = ?i", $product_id);
	$_cids = array();
	foreach ($data as $v) {
		$v['product_id'] = $pid;
		db_query("INSERT INTO ?:products_categories ?e", $v);
		$_cids[] = $v['category_id'];
	}
	fn_update_product_count($_cids);

	// Clone product options
	fn_clone_product_options($product_id, $pid);

	// Clone global linked options
	$gl_options = db_get_fields("SELECT option_id FROM ?:product_global_option_links WHERE product_id = ?i", $product_id);
	if (!empty($gl_options)) {
		foreach ($gl_options as $v) {
			db_query("INSERT INTO ?:product_global_option_links (option_id, product_id) VALUES (?i, ?i)", $v, $pid);
		}
	}

	// Clone product features
	$data = db_get_array("SELECT * FROM ?:product_features_values WHERE product_id = ?i", $product_id);
	foreach ($data as $v) {
		$v['product_id'] = $pid;
		db_query("INSERT INTO ?:product_features_values ?e", $v);
	}

	// Clone blocks
	fn_clone_block_links('products', $product_id, $pid);

	// Clone addons
	fn_set_hook('clone_product', $product_id, $pid);

	// Clone images
	fn_clone_image_pairs($pid, $product_id, 'product');

	// Clone product files
	if (is_dir(DIR_DOWNLOADS . $product_id)) {
		$data = db_get_array("SELECT * FROM ?:product_files WHERE product_id = ?i", $product_id);
		foreach ($data as $v) {
			$v['product_id'] = $pid;
			$old_file_id = $v['file_id'];
			unset($v['file_id']);
			
			$file_id = db_query("INSERT INTO ?:product_files ?e", $v);
			
			$file_descr = db_get_row("SELECT * FROM ?:product_file_descriptions WHERE file_id = ?i", $old_file_id);
			$file_descr['file_id'] = $file_id;
			
			db_query("INSERT INTO ?:product_file_descriptions ?e", $file_descr);
		}
		
		fn_copy(DIR_DOWNLOADS . $product_id, DIR_DOWNLOADS . $pid);
	}

	fn_build_products_cache(array($pid));
	return array('product_id'=>$pid, 'orig_name'=>$orig_name, 'product'=>$new_name);
}

//
// Product glodal update
//
function fn_global_update($update_data)
{
	$table = $field = $value = $type = array();
	$msg = '';
	$currencies = Registry::get('currencies');

	if (!empty($update_data['product_ids'])) {
		$update_data['product_ids'] = explode(',', $update_data['product_ids']);
	}

	// Update prices
	if (!empty($update_data['price'])) {
		$table[] = '?:product_prices';
		$field[] = 'price';
		$value[] = $update_data['price'];
		$type[] = $update_data['price_type'];

		$msg .= ($update_data['price'] > 0 ? fn_get_lang_var('price_increased') : fn_get_lang_var('price_decreased')) . ' ' . abs($update_data['price']) . ($update_data['price_type'] == 'A' ? $currencies[CART_PRIMARY_CURRENCY]['symbol'] : '%') . '.<br />';
	}

	// Update list prices
	if (!empty($update_data['list_price'])) {
		$table[] = '?:products';
		$field[] = 'list_price';
		$value[] = $update_data['list_price'];
		$type[] = $update_data['list_price_type'];

		$msg .= ($update_data['list_price'] > 0 ? fn_get_lang_var('list_price_increased') : fn_get_lang_var('list_price_decreased')) . ' ' . abs($update_data['list_price']) . ($update_data['list_price_type'] == 'A' ? $currencies[CART_PRIMARY_CURRENCY]['symbol'] : '%') . '.<br />';
	}

	// Update amount
	if (!empty($update_data['amount'])) {

		$table[] = '?:products';
		$field[] = 'amount';
		$value[] = $update_data['amount'];
		$type[] = 'A';

		$table[] = '?:product_options_inventory';
		$field[] = 'amount';
		$value[] = $update_data['amount'];
		$type[] = 'A';

		$msg .= ($update_data['amount'] > 0 ? fn_get_lang_var('amount_increased') : fn_get_lang_var('amount_decreased')) .' ' . abs($update_data['amount']) . '.<br />';
	}

	fn_set_hook('global_update', $table, $field, $value, $type, $msg, $update_data);

	$where = !empty($update_data['product_ids']) ? db_quote(" WHERE product_id IN (?n)", $update_data['product_ids']) : '';

	foreach ($table as $k => $v) {
		$_value = db_quote("?d", $value[$k]);
		$sql_expression = $type[$k] == 'A' ? ($field[$k] . ' + ' . $_value) : ($field[$k] . ' * (1 + ' . $_value . '/ 100)');

		db_query("UPDATE $v SET " . $field[$k] . " = IF($sql_expression < 0, 0, $sql_expression) $where");
	}

	if (empty($update_data['product_ids'])) {
		fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('all_products_have_been_updated') . '<br />' . $msg);
	} else {
		$products = fn_get_product_name($update_data['product_ids']);
		$msg = fn_get_lang_var('text_products_updated') . '<br />' . implode('<br />', $products) . '<br /><br />' . $msg;
		fn_set_notification('N', fn_get_lang_var('notice'), $msg);
	}

	return true;
}

function fn_copy_product_files($file_id, $file, $product_id, $var_prefix = 'file')
{
	$revisions = Registry::get('revisions');

	if (!empty($revisions['objects']['product']['tables'])) {
		$revision = true;
	} else {
		$revision = false;
	}

	if ($revision) {
		$filename = $file['name'];

		$i = 1;
		while (is_file(substr(DIR_DOWNLOADS, 0, -1) . ($revision ? '_rev' : '') . '/' . $product_id . '/' . $filename)) {
			$filename = substr_replace($file['name'], sprintf('%03d', $i) . '.', strrpos($file['name'], '.'), 1);
			$i++;
		}
	} else {
		$filename = $file['name'];
	}

	$_data = array();
	$_data[$var_prefix . '_path'] = $filename;
	$_data[$var_prefix . '_size'] = $file['size'];

	list($new_file, $_data[$var_prefix . '_path']) = fn_generate_file_name(substr(DIR_DOWNLOADS, 0, -1) . ($revision ? '_rev' : '') . '/' . $product_id . '/', $_data[$var_prefix . '_path']);
	
	if (fn_copy($file['path'], $new_file) == false) {
		$_msg = fn_get_lang_var('cannot_write_file');
		$_msg = str_replace('[file]', $new_file, $_msg);
		fn_set_notification('E', fn_get_lang_var('error'), $_msg);
		return false;
	}

	db_query('UPDATE ?:product_files SET ?u WHERE file_id = ?i', $_data, $file_id);

	return true;
}

// Add feature variants
function fn_add_feature_variant($feature_id, $variant)
{
	if (empty($variant['variant'])) {
		return false;
	}

	$variant['feature_id'] = $feature_id;
	$variant['variant_id'] = db_query("INSERT INTO ?:product_feature_variants ?e", $variant);

	foreach (Registry::get('languages') as $variant['lang_code'] => $_d) {
		db_query("INSERT INTO ?:product_feature_variant_descriptions ?e", $variant);
	}

	return $variant['variant_id'];
}

?>
