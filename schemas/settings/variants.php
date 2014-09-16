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
// $Id: variants.php 10229 2010-07-27 14:21:39Z 2tl $
//
if ( !defined('AREA') ) { die('Access denied'); }

/**
 * Get languages list for customer language
 */
function fn_settings_variants_appearance_customer_default_language()
{
	return fn_get_simple_languages();
}

/**
 * Get languages list for admin language
 */
function fn_settings_variants_appearance_admin_default_language()
{
	return fn_get_simple_languages();
}

/**
 * Get available formats, supported by GD library
 */
function fn_settings_variants_thumbnails_convert_to()
{
	return fn_check_gd_formats();
}

/**
 * Get list of objects, available to search through
 */
function fn_settings_variants_general_search_objects()
{
	return fn_search_get_objects();
}

/**
 * Get list of objects, available for revisioning
 */
function fn_settings_variants_general_active_revisions_objects()
{
	include_once(DIR_CORE . 'fn.revisions.php');
	fn_init_revisions();

	$revisions = Registry::get('revisions');

	if (empty($revisions['objects'])) {
		return array ();
	}

	$data = array ();
	foreach ($revisions['objects'] as $object => $entry) {
		$data[$object] = fn_get_lang_var($entry['title']);
	}

	return $data;
}

function fn_settings_variants_appearance_default_products_sorting()
{
	return fn_get_products_sorting(true);
}

function fn_settings_variants_appearance_default_products_layout()
{
	return fn_get_products_views(true, true);
}

function fn_settings_variants_appearance_default_products_layout_templates()
{
	return fn_get_products_views(true);
}

function fn_settings_variants_appearance_default_product_details_layout()
{
	return fn_get_product_details_views();
}
?>