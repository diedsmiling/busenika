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
// $Id: xls.php 10545 2010-08-30 13:31:08Z alexions $
//

if ( !defined('AREA') ) { die('Access denied'); }

define('ITEMS_PER_PAGE', 50);
define('MAX_SIZE', 50);
define('CATEGORY_NAME_HEIGHT', 20);

// Field heading definition
define('FIELD_HEADING_HEIGHT', 20);
define('FIELD_HEADING_BOLD', 1);
define('FIELD_HEADING_FONT_SIZE', 8);
define('FIELD_HEADING_FONT_FAMILY', 'Arial');
define('FIELD_HEADING_ALIGN', 'vcenter');

// Category heading definition
define('CATEGORY_HEADING_BOLD', 1);
define('CATEGORY_HEADING_FONT_SIZE', 10);
define('CATEGORY_HEADING_FONT_FAMILY', 'Arial');
define('CATEGORY_HEADING_ALIGN', 'left');
define('CATEGORY_HEADING_FONT_COLOR', 'white');
define('CATEGORY_HEADING_BG_COLOR', 'gray');
define('CATEGORY_HEADING_MERGE', 1);

// Simple field definition
define('FIELD_BOLD', 0);
define('FIELD_FONT_SIZE', 8);
define('FIELD_FONT_FAMILY', 'Arial');
define('FIELD_BOTTOM_BORDER', 1);
define('FIELD_BOTTOM_BORDER_COLOR', 'black');
define('FIELD_ALIGN', 'left');
define('FIELD_TEXT_WRAP', 1);
define('FIELD_NUM_FORMAT', 0);
define('FIELD_BG_COLOR', 'silver');
define('FIELD_MWIDTH', 1.5);

// Show xml content
error_reporting(E_ERROR);
ini_set('display_errors', '1');

set_time_limit(0);

fn_price_list_timer(); // Start timer;

$filename = DIR_CACHE . '/price_list_' . CART_LANGUAGE . '.xls'; // Must be unique for each xls mode.

if (file_exists($filename)) {
	if (headers_sent()) {
		exit;
	}
	
	header('Content-Description: File Transfer');
	header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
	header('Pragma: public');
	header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	// force download dialog
	header('Content-Type: application/force-download');
	header('Content-Type: application/octet-stream', false);
	header('Content-Type: application/download', false);
	header("Content-Type: application/x-msexcel");
	// use the Content-Disposition header to supply a recommended filename
	header('Content-Disposition: attachment; filename="' . fn_get_lang_var('price_list') . '.xls";');
	header('Content-Transfer-Encoding: binary');
	
	echo file_get_contents($filename);
	
	exit;
	
} else {
	
	include_once DIR_ADDONS . "/price_list/lib/writeexcel/class.writeexcel_workbook.inc.php";
	include_once DIR_ADDONS . "/price_list/lib/writeexcel/class.writeexcel_worksheet.inc.php";
	
	include_once DIR_ADDONS . '/price_list/core/class.counter.php';
	//include_once DIR_ADDONS . '/price_list/core/class.convert_to_bmp.php'; // FIX ME! We do not need it until resolve the compability problem.
	//$bmp = new ConvertToBMP();
	if (isset($selected_fields['image'])) {
		unset($selected_fields['image']); //// FIX ME! The image compability problem
	}
	
	$col = 'A';
	$row = 1;
	
	$width = array();
	
	$counter = new Counter(100, '.');
	
	$workbook =& new writeexcel_workbook($filename);
	$worksheet =& $workbook->addworksheet(fn_get_lang_var('price_list'));
	
	$field_heading =& $workbook->addformat(array(
		bold => FIELD_HEADING_BOLD,
		size => FIELD_HEADING_FONT_SIZE,
		font => FIELD_HEADING_FONT_FAMILY,
		align => FIELD_HEADING_ALIGN,
	));

	$category_heading =& $workbook->addformat(array(
		bold => CATEGORY_HEADING_BOLD,
		size => CATEGORY_HEADING_FONT_SIZE,
		font => CATEGORY_HEADING_FONT_FAMILY,
		merge   => CATEGORY_HEADING_MERGE,
		bg_color => CATEGORY_HEADING_BG_COLOR,
		color => CATEGORY_HEADING_FONT_COLOR,
		align => CATEGORY_HEADING_ALIGN,
	));
	
	$field_simple =& $workbook->addformat(array(
		bold => FIELD_BOLD,
		size => FIELD_FONT_SIZE,
		font => FIELD_FONT_FAMILY,
		bottom => FIELD_BOTTOM_BORDER,
		bottom_color => FIELD_BOTTOM_BORDER_COLOR,
		align => FIELD_ALIGN,
		text_wrap => FIELD_TEXT_WRAP,
		num_format => FIELD_NUM_FORMAT,
	));
	
	$field_simple_odd =& $workbook->addformat(array(
		bold => FIELD_BOLD,
		size => FIELD_FONT_SIZE,
		font => FIELD_FONT_FAMILY,
		bottom => FIELD_BOTTOM_BORDER,
		bottom_color => FIELD_BOTTOM_BORDER_COLOR,
		align => FIELD_ALIGN,
		text_wrap => FIELD_TEXT_WRAP,
		num_format => FIELD_NUM_FORMAT,
		bg_color => FIELD_BG_COLOR,
	));
	
	if (Registry::get('addons.price_list.group_by_category') == "Y") {
		// Display products according to the categories names.
		fn_echo(fn_get_lang_var('generating_xls') . '<br />');
		
		// Group the products by categories
		// Prepare XLS data
		$categories = fn_get_plain_categories_tree(0, false);
		
		foreach ($categories as $category) {
			
			if ($category['product_count'] == 0) {
				continue;
			}
			
			fn_echo('<br />' . $category['category']);
			$counter->Clear();
			
			// Write category name
			$worksheet->set_row($row - 1, CATEGORY_NAME_HEIGHT);
			$worksheet->write($col . $row, array_pad(array(fn_price_list_build_category_name($category['id_path'])), count($selected_fields), ''), $category_heading);
			
			$worksheet->set_row($row, FIELD_HEADING_HEIGHT);
			
			$row++;
			
			foreach ($selected_fields as $field => $active) {
				if ($field == 'image') {
					continue; // FIX ME! Some problems with the compability of the Excel format
				}
				$worksheet->write($col . $row, $price_schema['fields'][$field]['title'], $field_heading);
				if (isset($width[$col])) {
					if ($width[$col] < strlen($price_schema['fields'][$field]['title'])) {
						$width[$col] = strlen($price_schema['fields'][$field]['title']);
					}
					
				} else {
					$width[$col] = strlen($price_schema['fields'][$field]['title']) * FIELD_MWIDTH;
				}
				$col++;
			}
			
			$col = 'A';
			$row++;
			
			$page = 1;
			$total = ITEMS_PER_PAGE;
			$fill = true;
			
			$params = $_REQUEST;
			$params['type'] = 'extended';
			$params['subcats'] = 'N';
			$params['sort_by'] = $price_schema['fields'][Registry::get('addons.price_list.price_list_sorting')]['sort_by'];
			$params['page'] = $page;
			$params['cid'] = $category['category_id'];

			while (ITEMS_PER_PAGE * ($params['page'] - 1) <= $total) {
				list($products, , $total) = fn_get_products($params, ITEMS_PER_PAGE);
				$params['page']++;

				// Write products information
				foreach ($products as $product) {
					if (Registry::get('addons.price_list.include_options') == 'Y') {
						fn_gather_additional_product_data($product, true, false, true, true, true);
					} else {
						fn_gather_additional_product_data($product, true, false, false, true, true);
					}
					
					if (Registry::get('addons.price_list.include_options') == 'Y' && $product['has_options']) {
						$product = fn_price_list_get_combination($product);
						
						foreach ($product['combinations'] as $c_id => $c_value) {
							$product['price'] = $product['combination_prices'][$c_id];
							$product['weight'] = $product['combination_weight'][$c_id];
							$product['amount'] = $product['combination_amount'][$c_id];
							$product['product_code'] = $product['combination_code'][$c_id];
							
							foreach ($selected_fields as $field_name => $active) {
								if ($field_name == 'image') {
									continue;
									
								} elseif ($field_name == 'product') {
									$options = array();
									
									foreach ($c_value as $option_id => $variant_id) {
										$options[] = $product['product_options'][$option_id]['option_name'] . ': ' . $product['product_options'][$option_id]['variants'][$variant_id]['variant_name'];
									}
									
									$options = implode("\n", $options);
									
									$worksheet->write($col . $row, $product[$field_name] . "\n" . $options, ($row % 2 == 0 ? $field_simple_odd : $field_simple));
								} else {
									$worksheet->write($col . $row, $product[$field_name] . "\n", ($row % 2 == 0 ? $field_simple_odd : $field_simple));
								}
								
								$col++;
							}
							
							$col = 'A';
							$row++;
						}
					} else {
						foreach (Registry::get('addons.price_list.price_list_fields') as $field => $active) {
							if ($field == 'image') {
								continue; // FIX ME! Some problems with the compability of the Excel format
								
								/*$bmp->convert($_SERVER['DOCUMENT_ROOT'] . '/' . $product['main_pair']['icon']['image_path']);
								$img_filename = tempnam(DIR_ROOT . '/var/cache/', '_bmp');
								$bmp->output($img_filename);
								list($width, $height, $type, $attr) = getimagesize($img_filename);
								
								$worksheet->insert_bitmap($col . $row, $img_filename);
								unlink($img_filename);
								
								$worksheet->set_row($row, $height);*/
								
							} else {
								$worksheet->write($col . $row, $product[$field], ($row % 2 == 0 ? $field_simple_odd : $field_simple));
								if (isset($width[$col])) {
									if ($width[$col] < strlen($product[$field])) {
										$width[$col] = strlen($product[$field]);
									}
									
								} else {
									$width[$col] = strlen($product[$field]);
								}
							}
							$col++;
						}
					
						$col = 'A';
						$row++;
					}
				}
				
				$counter->Out();
			}
		}
		
	} else {
		// Display full products list.
		fn_echo(fn_get_lang_var('generating_xls') . '<br />');
		
		// Prepare XLS data
		
		$worksheet->set_row(0, FIELD_HEADING_HEIGHT);
		
		foreach ($selected_fields as $field => $active) {
			if ($field == 'image') {
				//continue;
			}
			$worksheet->write($col . $row, $price_schema['fields'][$field]['title'], $field_heading);
			if (isset($width[$col])) {
				if ($width[$col] < strlen($price_schema['fields'][$field]['title'])) {
					$width[$col] = strlen($price_schema['fields'][$field]['title']);
				}
				
			} else {
				$width[$col] = strlen($price_schema['fields'][$field]['title']) * FIELD_MWIDTH;
			}
			
			$col++;
		}
		
		$col = 'A';
		$row++;
		
		$page = 1;
		$total = ITEMS_PER_PAGE;
		$fill = true;
		
		$params = $_REQUEST;
		$params['type'] = 'extended';
		$params['subcats'] = 'N';
		$params['sort_by'] = $price_schema['fields'][Registry::get('addons.price_list.price_list_sorting')]['sort_by'];
		$params['page'] = $page;

		while (ITEMS_PER_PAGE * ($params['page'] - 1) <= $total) {
			list($products, , $total) = fn_get_products($params, ITEMS_PER_PAGE);
			$params['page']++;

			// Write products information
			foreach ($products as $product) {
				if (Registry::get('addons.price_list.include_options') == 'Y') {
					fn_gather_additional_product_data($product, true, false, true, true, true);
				} else {
					fn_gather_additional_product_data($product, true, false, false, true, true);
				}
				
				if (Registry::get('addons.price_list.include_options') == 'Y' && $product['has_options']) {
					$product = fn_price_list_get_combination($product);
					
					foreach ($product['combinations'] as $c_id => $c_value) {
						$product['price'] = $product['combination_prices'][$c_id];
						$product['weight'] = $product['combination_weight'][$c_id];
						$product['amount'] = $product['combination_amount'][$c_id];
						$product['product_code'] = $product['combination_code'][$c_id];
						
						foreach ($selected_fields as $field_name => $active) {
							if ($field_name == 'image') {
								continue;
								
							} elseif ($field_name == 'product') {
								$options = array();
								
								foreach ($c_value as $option_id => $variant_id) {
									$options[] = $product['product_options'][$option_id]['option_name'] . ': ' . $product['product_options'][$option_id]['variants'][$variant_id]['variant_name'];
								}
								
								$options = implode("\n", $options);
								
								$worksheet->write($col . $row, $product[$field_name] . "\n" . $options, ($row % 2 == 0 ? $field_simple_odd : $field_simple));
							} else {
								$worksheet->write($col . $row, $product[$field_name] . "\n", ($row % 2 == 0 ? $field_simple_odd : $field_simple));
							}
							
							$col++;
						}
						
						$col = 'A';
						$row++;
					}
				} else {
					foreach (Registry::get('addons.price_list.price_list_fields') as $field => $active) {
						if ($field == 'image') {
							continue; // FIX ME! Some problems with the compability of the Excel format
							
							/*$bmp->convert($_SERVER['DOCUMENT_ROOT'] . '/' . $product['main_pair']['icon']['image_path']);
							$img_filename = tempnam(DIR_ROOT . '/var/cache/', '_bmp');
							$bmp->output($img_filename);
							list($width, $height, $type, $attr) = getimagesize($img_filename);
							
							$worksheet->insert_bitmap($col . $row, $img_filename);
							unlink($img_filename);
							
							$worksheet->set_row($row, $height);*/
							
						} else {
							$worksheet->write($col . $row, $product[$field], ($row % 2 == 0 ? $field_simple_odd : $field_simple));
							if (isset($width[$col])) {
								if ($width[$col] < strlen($product[$field])) {
									$width[$col] = strlen($product[$field]);
								}
								
							} else {
								$width[$col] = strlen($product[$field]);
							}
						}
						$col++;
					}
				
					$col = 'A';
					$row++;
				}
			}
			
			$counter->Out();
		}
	}
	
	foreach ($width as $col => $size) {
		if ($size > MAX_SIZE) {
			$size = MAX_SIZE;
		}
		$worksheet->set_column($col . ':' . $col, $size);
	}
	
	unset($bmp);

	//Close and output XLS document
	$workbook->close();

	fn_echo('<br />' . fn_get_lang_var('done'));
}

?>