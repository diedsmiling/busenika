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
// $Id: google.php 10572 2010-09-01 20:43:16Z andyye $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// Schema definition
//
$schema = array (
	'section' => 'products',
	'key' => array('product_id'),
	'enclosure' => '',
	'name' => fn_get_lang_var('google_base'),
	'pattern_id' => 'google',
	'table' => 'products',
	'condition' => array('status' => 'A'),
	'export_only' => true,
	'range_options' => array (
		'selector_url' => 'products.manage',
		'object_name' => fn_get_lang_var('products'),
	),
	'references' => array (
		'product_descriptions' => array (
			'reference_fields' => array ('product_id' => '#key', 'lang_code' => '@lang_code'),
			'join_type' => 'LEFT'
		),
		'product_prices' => array (
			'reference_fields' => array ('product_id' => '#key', 'lower_limit' => 1, 'usergroup_id' => 0),
			'join_type' => 'LEFT'
		),
		'images_links' => array (
			'reference_fields' => array('object_id' => '#key', 'object_type' => 'product', 'type' => 'M'),
			'join_type' => 'LEFT'
		),
	),
	'options' => array (
		'lang_code' => array (
			'title' => 'language',
			'type' => 'languages'
		),
		'product_type' => array (
			'title' => 'product_type',
			'type' => 'select',
			'variants_function' => 'fn_exim_google_get_types',
		),
		'condition' => array (
			'title' => 'condition',
			'type' => 'select',
			'variants_function' => 'fn_exim_google_get_conditions',
		),
		'discounts' => array (
			'title' => 'export_discounts',
			'type' => 'checkbox'
		),
	),
	'override_options' => array (
		'delimiter' => 'T',
		'output' => 'S',
	),
	'post_processing' => array ('fn_exim_transfer_google_file', '@filename'),
	'export_fields' => array (
		'id' => array (
			'db_field' => 'product_id',
			'required' => true,
			'alt_key' => true
		),
		'product_type' => array (
			'process_get' => array('fn_exim_google_format_field', '', 'product_type', '', '@product_type'),
			'linked' => false
		),
		'quantity' => array (
			'db_field' => 'amount',
			'process_get' => array('fn_exim_google_format_field', '#this', 'quantity', '#key', '')
		),
		'price' => array (
			'table' => 'product_prices',
			'db_field' => 'price',
			'process_get' => array('fn_exim_google_format_field', '#this', 'price', '#key', '@discounts')
		),
		'upc' => array (
			'db_field' => 'product_code'
		),
		'title' => array (
			'table' => 'product_descriptions',
			'db_field' => 'product',
			'process_get' => array('fn_exim_google_format_field', '#this', 'title'),
		),
		'description' => array (
			'table' => 'product_descriptions',
			'db_field' => 'full_description',
			'process_get' => array('fn_exim_google_format_field', '#this', 'description'),
		),
		'link' => array (
			'process_get' => array ('fn_exim_get_product_url', '#key', '@lang_code'),
			'linked' => false
		),
		'image_link' => array (
			'process_get' => array ('fn_exim_get_image_url', '#key', 'product', 'M', true, false, false),
			'db_field' => 'image_id',
			'table' => 'images_links',
		),
		'condition' => array (
			'process_get' => array('fn_exim_google_format_field', '', 'condition', '', '@condition'),
			'linked' => false,
			'required' => true,
		),
		'weight' => array (
			'db_field' => 'weight',
			'process_get' => array('fn_exim_google_format_field', '#this', 'weight', '', '')
		),
// Please uncomment this code if you want to export ISBN feature to the google
/*		'ISBN' => array (
			'process_get' => array ('fn_exim_get_feature', '#key', 'ISBN'),
			'linked' => false
		),*/
	),
);


// ------------- Utility functions ---------------

//
// This function get the feature value
function fn_exim_get_feature($product_id, $feature_name)
{
	$feature = db_get_field("SELECT value FROM ?:product_features_values as a LEFT JOIN ?:product_features_descriptions as b ON a.feature_id = b.feature_id AND b.lang_code = ?s WHERE b.description = ?s AND a.product_id = ?i AND a.lang_code = ?s", CART_LANGUAGE, $feature_name, $product_id, CART_LANGUAGE);

	return $feature;
}

//
// Formats field by its type
// Parameters:
// @data - field to be formatted
// @type - field type
function fn_exim_google_format_field($data, $type, $product_id = 0, $option = '')
{
	if ($type == 'product') {
		return substr(strip_tags($data), 0, 80);

	} elseif ($type == 'product_type') {
		return (!empty($option)) ? $option : 'other';

	} elseif ($type == 'condition') {
		return (!empty($option)) ? $option : 'New';
		
	} elseif ($type == 'quantity') {
		
		if (Registry::get('settings.General.inventory_tracking') != 'Y') {
			return '';
		}
		
		$tracking = db_get_field("SELECT tracking FROM ?:products WHERE product_id = ?i", $product_id);
		
		if ($tracking == 'D'){
			return '';
		}
		
		if ($tracking == 'O'){
			$data = db_get_field("SELECT SUM(amount) FROM ?:product_options_inventory WHERE product_id = ?i AND amount >= 0", $product_id);
			if (empty($data)){
				$data = 0;
			}
		}
		
		if ($data < 0 && Registry::get('settings.General.allow_negative_amount') == 'Y'){
			$data = '';
		}

		return $data;	

	} elseif ($type == 'price') {
		$_discount = 0;
		if ($option == 'Y') {
			$auth = fn_fill_auth();
			$product = fn_get_product_data($product_id, $auth, CART_LANGUAGE, true, true, false, false, false);
			fn_promotion_apply('catalog', $product, $auth);

			if (!empty($product['discount'])) {
				$_discount = $product['discount'];
			}
		}
		return fn_format_price($data - $_discount);

	} elseif ($type == 'weight') {
		
		$data = floatval($data);
		
		if ($data > 0){
			$weight_symbol = '';
			$gr_in_unit = Registry::get('settings.General.weight_symbol_grams');
			if (in_array(Registry::get('settings.General.weight_symbol'), fn_exim_google_get_weight_units())){
				$weight_symbol =  ' ' . Registry::get('settings.General.weight_symbol');
			}elseif (!empty($gr_in_unit) && (float) $gr_in_unit > 0){
				$weight_symbol = ' grams';
				$data = $data * (float) $gr_in_unit;
			}
			
			$w = fn_format_price($data);
			
			if (floor($w) == $w){
				$w = floor($w);
			}
			
			$data = $w . $weight_symbol;
		}else{
			$data = '';
		}
		
		return $data;
		
	} else {
		return strip_tags(str_replace( array('<br>','<br />', '<BR>', '<BR />'), "\\n", $data));
	}
}

//
// Transfers export file to Google base servers
// Parameters:
// @filename - file name to transfer

function fn_exim_transfer_google_file($filename)
{
	if (defined('COMPANY_ID')) {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('warning_exim_vendor_no_data_exported'));
	} else {
		if (function_exists('ftp_connect')) {
			$google_sets = fn_get_settings('Google');
			$object = 'google';

			$_ftp_host = $google_sets[$object . '_host'];
			$ftp_login = $google_sets[$object . '_login'];
			$ftp_pass =  $google_sets[$object . '_pass'];

			$__ftp_host = parse_url($_ftp_host);
			$ftp_host = empty($__ftp_host['host']) ? $__ftp_host['path'] : $__ftp_host['host'];

			if (empty($ftp_host) || empty($ftp_login) || empty($ftp_pass)) {
				$_hl = 'google_host,google_login,google_pass';
				$_set = "<a href=\"" . fn_url("settings.manage?section_id=Google&highlight=$_hl", 'A', 'http') . "\">Google base</a>";
				fn_set_notification('W', fn_get_lang_var('warning'), str_replace('[settings]', $_set, fn_get_lang_var('specify_ftp_parameters')));
				return false;
			}

			$ftp = @ftp_connect($ftp_host);
			if ($ftp && @ftp_login($ftp, $ftp_login, $ftp_pass)) {
				ftp_pasv($ftp, true);
				$fp = fopen(DIR_EXIM . $filename, "rb");
				if ($fp) {
					echo "<br> Copying file to the google server...";
					fn_flush();
					if (@ftp_fput($ftp, basename($filename), $fp, FTP_BINARY)) {
						fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('file_succ_transfered'));
					} else {
						fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('file_transfer_failed'));
						return false;
					}
					fclose($fp);
				} else {
					fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('file_not_found'));

					return false;
				}

				ftp_quit($ftp);
			} else {
				fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('ftp_connect_failed'));

				return false;
			}
		} else {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('php_do_not_support_ftp'));

			return false;
		}
	}
	return true;
}

function fn_exim_google_get_types()
{
	return array (
		'other' => 'other',
		'2-way radios' => '2-way radios',
		'accessories' => 'accessories',
		'adapters' => 'adapters',
		'adhesives' => 'adhesives',
		'air compressors' => 'air compressors',
		'air conditioners' => 'air conditioners',
		'air coolers' => 'air coolers',
		'air purifiers' => 'air purifiers',
		'airsoft' => 'airsoft',
		'alarms' => 'alarms',
		'am/fm radios' => 'am/fm radios',
		'amplifiers' => 'amplifiers',
		'analyzers' => 'analyzers',
		'antennas' => 'antennas',
		'antiques' => 'antiques',
		'appliances' => 'appliances',
		'archery bows' => 'archery bows',
		'aromatherapy' => 'aromatherapy',
		'art' => 'art',
		'atm machines' => 'atm machines',
		'attachments' => 'attachments',
		'autographs' => 'autographs',
		'automotive' => 'automotive',
		'baby gear' => 'baby gear',
		'backpacks' => 'backpacks',
		'bakeware' => 'bakeware',
		'balls' => 'balls',
		'bars' => 'bars',
		'barware' => 'barware',
		'baseball cards' => 'baseball cards',
		'baskets' => 'baskets',
		'batteries' => 'batteries',
		'beads' => 'beads',
		'bedding' => 'bedding',
		'beds' => 'beds',
		'belt buckles' => 'belt buckles',
		'belts' => 'belts',
		'benches' => 'benches',
		'bicycle parts' => 'bicycle parts',
		'billiards' => 'billiards',
		'binoculars' => 'binoculars',
		'bird feeders' => 'bird feeders',
		'blazers' => 'blazers',
		'blenders' => 'blenders',
		'blinds' => 'blinds',
		'board games' => 'board games',
		'bookcases' => 'bookcases',
		'bookends' => 'bookends',
		'books' => 'books',
		'boomboxes' => 'boomboxes',
		'bottles' => 'bottles',
		'bowls' => 'bowls',
		'boxes' => 'boxes',
		'boxing' => 'boxing',
		'bracelets' => 'bracelets',
		'brakes' => 'brakes',
		'briefcases' => 'briefcases',
		'brooches' => 'brooches',
		'brushes' => 'brushes',
		'building materials' => 'building materials',
		'bulbs' => 'bulbs',
		'bullion' => 'bullion',
		'buttons' => 'buttons',
		'cabinets' => 'cabinets',
		'cables' => 'cables',
		'calculators' => 'calculators',
		'calendars' => 'calendars',
		'calling cards' => 'calling cards',
		'camcorder bags' => 'camcorder bags',
		'camcorders' => 'camcorders',
		'camera bags' => 'camera bags',
		'cameras' => 'cameras',
		'camping gear' => 'camping gear',
		'candle holders' => 'candle holders',
		'candles' => 'candles',
		'caps' => 'caps',
		'car audio' => 'car audio',
		'car radar' => 'car radar',
		'car video' => 'car video',
		'cartridges' => 'cartridges',
		'carts' => 'carts',
		'cases' => 'cases',
		'cash registers' => 'cash registers',
		'cassette decks' => 'cassette decks',
		'casters' => 'casters',
		'cb radios' => 'cb radios',
		'cd players' => 'cd players',
		'ceramics' => 'ceramics',
		'chains' => 'chains',
		'chairs' => 'chairs',
		'chandeliers' => 'chandeliers',
		'change machines' => 'change machines',
		'chargers' => 'chargers',
		'charms' => 'charms',
		'chisels' => 'chisels',
		'christmas ornaments' => 'christmas ornaments',
		'circuit breakers' => 'circuit breakers',
		'circular saws' => 'circular saws',
		'clamps' => 'clamps',
		'clocks' => 'clocks',
		'clubs' => 'clubs',
		'clutches' => 'clutches',
		'coats' => 'coats',
		'coffee makers' => 'coffee makers',
		'coins' => 'coins',
		'collectibles' => 'collectibles',
		'comics' => 'comics',
		'compactors' => 'compactors',
		'compasses' => 'compasses',
		'computer cases' => 'computer cases',
		'computers' => 'computers',
		'concessions' => 'concessions',
		'connectors' => 'connectors',
		'consoles' => 'consoles',
		'control systems' => 'control systems',
		'controllers' => 'controllers',
		'conveyors' => 'conveyors',
		'cookers' => 'cookers',
		'cooking scales' => 'cooking scales',
		'cookware' => 'cookware',
		'copiers' => 'copiers',
		'cosmetics' => 'cosmetics',
		'costumes' => 'costumes',
		'covers' => 'covers',
		'crafts' => 'crafts',
		'credit card terminals' => 'credit card terminals',
		'cufflinks' => 'cufflinks',
		'cups' => 'cups',
		'curtains' => 'curtains',
		'cutlery' => 'cutlery',
		'cutting boards' => 'cutting boards',
		'desks' => 'desks',
		'diapering' => 'diapering',
		'dies' => 'dies',
		'diet' => 'diet',
		'digital video recorders' => 'digital video recorders',
		'dinnerware' => 'dinnerware',
		'dishes' => 'dishes',
		'dishwashers' => 'dishwashers',
		'disneyana' => 'disneyana',
		'display cases' => 'display cases',
		'dolls' => 'dolls',
		'door knockers' => 'door knockers',
		'dresses' => 'dresses',
		'drills' => 'drills',
		'drinkware' => 'drinkware',
		'drives' => 'drives',
		'drums' => 'drums',
		'dryers' => 'dryers',
		'dvd players' => 'dvd players',
		'earrings' => 'earrings',
		'ebook' => 'ebook',
		'equestrian' => 'equestrian',
		'equipment' => 'equipment',
		'exercise equipment' => 'exercise equipment',
		'fans' => 'fans',
		'fasteners' => 'fasteners',
		'fax machines' => 'fax machines',
		'feeding' => 'feeding',
		'figurines' => 'figurines',
		'files' => 'files',
		'filters' => 'filters',
		'fishing lures' => 'fishing lures',
		'fishing rods' => 'fishing rods',
		'fishing tackle' => 'fishing tackle',
		'flags' => 'flags',
		'flashes' => 'flashes',
		'flashlights' => 'flashlights',
		'flatware' => 'flatware',
		'flooring' => 'flooring',
		'floppy drives' => 'floppy drives',
		'flowers' => 'flowers',
		'food processors' => 'food processors',
		'food storage and handling' => 'food storage and handling',
		'footwear' => 'footwear',
		'fountains' => 'fountains',
		'fragrance' => 'fragrance',
		'frames' => 'frames',
		'fryers' => 'fryers',
		'furniture' => 'furniture',
		'gauges' => 'gauges',
		'gear bags' => 'gear bags',
		'gemstones' => 'gemstones',
		'generators' => 'generators',
		'gift baskets' => 'gift baskets',
		'gift sets' => 'gift sets',
		'glasses' => 'glasses',
		'glassware' => 'glassware',
		'gloves' => 'gloves',
		'go-karts' => 'go-karts',
		'goggles' => 'goggles',
		'golf bags' => 'golf bags',
		'golf balls' => 'golf balls',
		'golf carts' => 'golf carts',
		'golf clubs' => 'golf clubs',
		'gourmet food' => 'gourmet food',
		'gowns' => 'gowns',
		'gps devices' => 'gps devices',
		'graters' => 'graters',
		'grinders' => 'grinders',
		'gun parts' => 'gun parts',
		'hair care' => 'hair care',
		'hair jewelry' => 'hair jewelry',
		'ham radios' => 'ham radios',
		'hammers' => 'hammers',
		'hammocks' => 'hammocks',
		'handbags' => 'handbags',
		'hard drives' => 'hard drives',
		'hardware' => 'hardware',
		'hats' => 'hats',
		'headphones' => 'headphones',
		'headsets' => 'headsets',
		'heaters' => 'heaters',
		'helmets' => 'helmets',
		'hiking gear' => 'hiking gear',
		'hoists' => 'hoists',
		'home security' => 'home security',
		'home theater' => 'home theater',
		'hooks' => 'hooks',
		'hubs' => 'hubs',
		'hunting gear' => 'hunting gear',
		'hunting knives' => 'hunting knives',
		'hvac' => 'hvac',
		'hydration packs' => 'hydration packs',
		'ice cream machines' => 'ice cream machines',
		'ice hockey' => 'ice hockey',
		'ice machines' => 'ice machines',
		'ice skating' => 'ice skating',
		'infant clothing' => 'infant clothing',
		'jackets' => 'jackets',
		'jacks' => 'jacks',
		'jars' => 'jars',
		'jeans' => 'jeans',
		'jerseys' => 'jerseys',
		'jewelry boxes' => 'jewelry boxes',
		'jewelry sets' => 'jewelry sets',
		'joiners' => 'joiners',
		'joints' => 'joints',
		'juicers' => 'juicers',
		'karaoke' => 'karaoke',
		'key chains' => 'key chains',
		'keyboards' => 'keyboards',
		'kitchenware' => 'kitchenware',
		'kits' => 'kits',
		'knives' => 'knives',
		'lab supplies' => 'lab supplies',
		'ladders' => 'ladders',
		'lamps' => 'lamps',
		'lenses' => 'lenses',
		'light bulbs' => 'light bulbs',
		'lighters' => 'lighters',
		'lingerie' => 'lingerie',
		'locks' => 'locks',
		'loose diamonds' => 'loose diamonds',
		'luggage' => 'luggage',
		'magazines' => 'magazines',
		'manuals' => 'manuals',
		'markers' => 'markers',
		'martial arts' => 'martial arts',
		'mats' => 'mats',
		'mattresses' => 'mattresses',
		'memorabilia' => 'memorabilia',
		'memory' => 'memory',
		'meters' => 'meters',
		'microphones' => 'microphones',
		'microwaves' => 'microwaves',
		'mirrors' => 'mirrors',
		'modems' => 'modems',
		'money clips' => 'money clips',
		'monitors' => 'monitors',
		'monopods' => 'monopods',
		'motherboards' => 'motherboards',
		'motorcycles' => 'motorcycles',
		'motors' => 'motors',
		'mounts' => 'mounts',
		'movies' => 'movies',
		'mp3 players' => 'mp3 players',
		'mugs' => 'mugs',
		'music' => 'music',
		'music on vinyl' => 'music on vinyl',
		'music players' => 'music players',
		'musical instruments' => 'musical instruments',
		'nails' => 'nails',
		'natural therapies' => 'natural therapies',
		'necklaces' => 'necklaces',
		'nutrition' => 'nutrition',
		'oral care' => 'oral care',
		'organizers' => 'organizers',
		'oscilloscopes' => 'oscilloscopes',
		'outerwear' => 'outerwear',
		'ovens' => 'ovens',
		'packaging' => 'packaging',
		'packs' => 'packs',
		'pads' => 'pads',
		'pagers' => 'pagers',
		'paintball' => 'paintball',
		'paintings' => 'paintings',
		'paints' => 'paints',
		'pants' => 'pants',
		'parts' => 'parts',
		'pdas' => 'pdas',
		'pedals' => 'pedals',
		'pedestals' => 'pedestals',
		'pendants' => 'pendants',
		'pens' => 'pens',
		'pet supplies' => 'pet supplies',
		'phones' => 'phones',
		'photo printers' => 'photo printers',
		'pillows' => 'pillows',
		'pins' => 'pins',
		'planners' => 'planners',
		'planters' => 'planters',
		'plastics' => 'plastics',
		'pliers' => 'pliers',
		'plumbing' => 'plumbing',
		'pool liners' => 'pool liners',
		'portable cd players' => 'portable cd players',
		'portables' => 'portables',
		'pos systems' => 'pos systems',
		'postage meters' => 'postage meters',
		'postcards' => 'postcards',
		'posters' => 'posters',
		'pots' => 'pots',
		'pottery' => 'pottery',
		'power inverters' => 'power inverters',
		'pressure washers' => 'pressure washers',
		'printers' => 'printers',
		'prints' => 'prints',
		'processors' => 'processors',
		'projectors' => 'projectors',
		'protective gear' => 'protective gear',
		'pumps' => 'pumps',
		'punches' => 'punches',
		'puzzles' => 'puzzles',
		'racks' => 'racks',
		'racquets' => 'racquets',
		'radios' => 'radios',
		'range hoods' => 'range hoods',
		'ranges' => 'ranges',
		'raw materials' => 'raw materials',
		'reamers' => 'reamers',
		'receivers' => 'receivers',
		'recorders' => 'recorders',
		'refrigerators' => 'refrigerators',
		'remote controls' => 'remote controls',
		'replicas' => 'replicas',
		'ribbons' => 'ribbons',
		'rings' => 'rings',
		'robes' => 'robes',
		'robotics' => 'robotics',
		'rods' => 'rods',
		'roller hockey' => 'roller hockey',
		'routers' => 'routers',
		'rugs' => 'rugs',
		'safes' => 'safes',
		'safety glasses' => 'safety glasses',
		'salon equipment' => 'salon equipment',
		'satellite radios' => 'satellite radios',
		'saws' => 'saws',
		'scales' => 'scales',
		'scanners' => 'scanners',
		'scarves' => 'scarves',
		'scissors' => 'scissors',
		'scooters' => 'scooters',
		'scrapbooking' => 'scrapbooking',
		'screens' => 'screens',
		'screwdrivers' => 'screwdrivers',
		'screws' => 'screws',
		'seats' => 'seats',
		'security equipment' => 'security equipment',
		'seeds' => 'seeds',
		'sewing machines' => 'sewing machines',
		'sex toys' => 'sex toys',
		'shears' => 'shears',
		'sheet music' => 'sheet music',
		'sheets' => 'sheets',
		'shelving' => 'shelving',
		'shirts' => 'shirts',
		'shorts' => 'shorts',
		'shortwave radios' => 'shortwave radios',
		'shortwave scanners' => 'shortwave scanners',
		'shower curtains' => 'shower curtains',
		'shredders' => 'shredders',
		'signs' => 'signs',
		'sim cards' => 'sim cards',
		'sinks' => 'sinks',
		'skateboarding' => 'skateboarding',
		'skiing' => 'skiing',
		'skincare' => 'skincare',
		'skirts' => 'skirts',
		'sleeping bags' => 'sleeping bags',
		'sleepwear' => 'sleepwear',
		'slicers' => 'slicers',
		'slides' => 'slides',
		'slipcovers' => 'slipcovers',
		'snowboarding' => 'snowboarding',
		'sockets' => 'sockets',
		'socks' => 'socks',
		'software' => 'software',
		'soldering' => 'soldering',
		'speaker stands' => 'speaker stands',
		'speakers' => 'speakers',
		'sporting goods' => 'sporting goods',
		'stamps' => 'stamps',
		'stands' => 'stands',
		'stickers' => 'stickers',
		'storage' => 'storage',
		'stoves' => 'stoves',
		'subwoofers' => 'subwoofers',
		'suits' => 'suits',
		'sunglasses' => 'sunglasses',
		'supplements' => 'supplements',
		'supplies' => 'supplies',
		'suspension' => 'suspension',
		'sweaters' => 'sweaters',
		'sweatshirts' => 'sweatshirts',
		'swimwear' => 'swimwear',
		'switches' => 'switches',
		'swords' => 'swords',
		'tables' => 'tables',
		'tableware' => 'tableware',
		'tanks' => 'tanks',
		'tapes' => 'tapes',
		'televisions' => 'televisions',
		'tents' => 'tents',
		'testers' => 'testers',
		'thermometers' => 'thermometers',
		'throws' => 'throws',
		'tie clasps' => 'tie clasps',
		'ties' => 'ties',
		'tires' => 'tires',
		'toasters' => 'toasters',
		'toner' => 'toner',
		'tools' => 'tools',
		'tops' => 'tops',
		'towels' => 'towels',
		'toys' => 'toys',
		'transformers' => 'transformers',
		'trays' => 'trays',
		'treadmills' => 'treadmills',
		'tripods' => 'tripods',
		'turntables' => 'turntables',
		'tv stands' => 'tv stands',
		'typewriters' => 'typewriters',
		'umbrellas' => 'umbrellas',
		'underwear' => 'underwear',
		'uniforms' => 'uniforms',
		'usb adaptors' => 'usb adaptors',
		'utensils' => 'utensils',
		'vacuums' => 'vacuums',
		'vases' => 'vases',
		'vehicle parts' => 'vehicle parts',
		'veils' => 'veils',
		'vending machines' => 'vending machines',
		'vests' => 'vests',
		'video games' => 'video games',
		'videos' => 'videos',
		'vision care' => 'vision care',
		'voice recorders' => 'voice recorders',
		'wall decor' => 'wall decor',
		'wall paper' => 'wall paper',
		'wallets' => 'wallets',
		'washers' => 'washers',
		'watches' => 'watches',
		'water treatment' => 'water treatment',
		'wedding bands' => 'wedding bands',
		'wedding dresses' => 'wedding dresses',
		'welders' => 'welders',
		'wigs' => 'wigs',
		'winches' => 'winches',
		'wind chimes' => 'wind chimes',
		'wire' => 'wire',
		'word processors' => 'word processors',
		'wraps' => 'wraps',
	);
}

function fn_exim_google_get_conditions()
{
	return array (
		'New' => 'New',
		'Used' => 'Used',
		'Refurbished' => 'Refurbished',
	);
}

function fn_exim_google_get_weight_units()
{
	return array ('lbs', 'pounds', 'oz', 'ounces', 'g', 'grams', 'kg', 'kilograms');
}

?>