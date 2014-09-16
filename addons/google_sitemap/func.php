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
// $Id: func.php 10055 2010-07-14 10:15:19Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_google_sitemap_generate_link($object, $value)
{
	$index_script = Registry::get('config.customer_index');
	$http_location = Registry::get('config.http_location');

	switch ($object) {
		case 'product':
			$link = htmlentities("products.view?product_id=" . $value);
			
			break;
		case 'category':
			$link = htmlentities("categories.view?category_id=" . $value);
			
			break;
		case 'page':
			$link = htmlentities("pages.view?page_id=" . $value);
			
			break;
		case 'extended':
			$link = htmlentities("product_features.view?variant_id=" . $value);
			
			break;
		default:
			fn_set_hook('sitemap_link_object', $link, $object, $value);
	}
	$link = fn_url($link, 'C', 'http');

	fn_set_hook('sitemap_link', $link);
	
	return $link;
}

function fn_google_sitemap_print_item_info($link, $lmod, $frequency, $priority)
{
$item = <<<ITEM
	<url>
		<loc>$link</loc>
		<lastmod>$lmod</lastmod>
		<changefreq>$frequency</changefreq>
		<priority>$priority</priority>
	</url>\n
ITEM;

	return $item;
}

function fn_google_sitemap_get_frequency()
{
	$frequency = array(
		'always' => fn_get_lang_var('always'),
		'hourly' => fn_get_lang_var('hourly'),
		'daily' => fn_get_lang_var('daily'),
		'weekly' => fn_get_lang_var('weekly'),
		'monthly' => fn_get_lang_var('monthly'),
		'yearly' => fn_get_lang_var('yearly'),
		'never' => fn_get_lang_var('never'),
	);
	
	return $frequency;
}

function fn_google_sitemap_get_priority()
{
	$priority = array();
	
	for ($i = 0.1; $i <= 1; $i += 0.1) {
		$priority[(string)$i] = (string)$i;
	}
	
	return $priority;
}

function fn_google_sitemap_clear_url_info()
{
	$search = array('[http_location]', '[admin_index]', '[customer_index]');
	$replace = array(Registry::get('config.http_location'), Registry::get('config.admin_index'), Registry::get('config.customer_index'));
	
	return str_replace($search, $replace, fn_get_lang_var('sitemap_clear_cache_info'));
}

function fn_google_sitemap_get_content($filename)
{
	define('ITEMS_PER_PAGE', 50);
	
	$file = fopen($filename, "wb");
	
	$sitemap_settings = Registry::get('addons.google_sitemap');
	$location = Registry::get('config.http_location');
	$lmod = date("Y-m-d", TIME);
	
	header("Content-Type: text/xml;charset=utf-8");

$head = <<<HEAD
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
			http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

	<url>
		<loc>$location/</loc>
		<lastmod>$lmod</lastmod>
		<changefreq>$sitemap_settings[site_change]</changefreq>
		<priority>$sitemap_settings[site_priority]</priority>
	</url>\n
HEAD;
	echo $head;
	fwrite($file, $head);

	if ($sitemap_settings['include_categories'] == "Y") {
		$categories = db_get_fields("SELECT category_id FROM ?:categories WHERE FIND_IN_SET(?i, usergroup_ids) AND status = 'A'", USERGROUP_ALL);
		
		//Add the all active categories
		foreach ($categories as $category) {
			$link = fn_google_sitemap_generate_link('category', $category);
			$item = fn_google_sitemap_print_item_info($link, $lmod, $sitemap_settings['categories_change'], $sitemap_settings['categories_priority']);
			
			echo $item;
			fwrite($file, $item);
		}
	}

	if ($sitemap_settings['include_products'] == "Y") {
		$page = 1;
		$total = ITEMS_PER_PAGE;
		
		$params = $_REQUEST;
		$params['type'] = 'extended';
		$params['subcats'] = 'N';
		$params['page'] = $page;
		
		while (ITEMS_PER_PAGE * ($params['page'] - 1) <= $total) {
			list($products, $search, $total) = fn_get_products($params, ITEMS_PER_PAGE);
			$params['page']++;
			
			foreach ($products as $product) {
				$link = fn_google_sitemap_generate_link('product', $product['product_id']);
				$item = fn_google_sitemap_print_item_info($link, $lmod, $sitemap_settings['products_change'], $sitemap_settings['products_priority']);

				echo $item;
				fwrite($file, $item);
			}
		}
	}

	if ($sitemap_settings['include_pages'] == "Y") {
		$pages = db_get_fields("SELECT page_id FROM ?:pages WHERE status = 'A'");
		
		//Add the all active pages
		foreach ($pages as $page) {
			$link = fn_google_sitemap_generate_link('page', $page);
			$item = fn_google_sitemap_print_item_info($link, $lmod, $sitemap_settings['pages_change'], $sitemap_settings['pages_priority']);
			
			echo $item;
			fwrite($file, $item);
		}
	}

	if ($sitemap_settings['include_extended'] == "Y") {
		$vars = db_get_fields("SELECT ?:product_feature_variants.variant_id FROM ?:product_feature_variants LEFT JOIN ?:product_features ON (?:product_feature_variants.feature_id = ?:product_features.feature_id) WHERE ?:product_features.feature_type = 'E' AND ?:product_features.status = 'A'");
	
		//Add the all active extended features
		foreach ($vars as $var) {
			$link = fn_google_sitemap_generate_link('extended', $var);
			$item = fn_google_sitemap_print_item_info($link, $lmod, $sitemap_settings['extended_change'], $sitemap_settings['extended_priority']);
			
			echo $item;
			fwrite($file, $item);
		}
	}
	
	fn_set_hook('sitemap_item', $sitemap_settings, $file, $lmod);

$foot = <<<FOOT

</urlset>
FOOT;
	echo $foot;
	fwrite($file, $foot);
	
	fclose($file);
	@chmod($filename, DEFAULT_FILE_PERMISSIONS);
	
	exit();
}

function fn_google_sitemap_get_rewrite_rules($rewrite_rules, $prefix)
{
	$rewrite_rules['!^(.*)?' . $prefix . '\/sitemap\.xml$!'] = '$customer_index?dispatch=xmlsitemap.view';
}

?>