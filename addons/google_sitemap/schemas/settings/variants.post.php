<?php

//
// $Id: variants.post.php 8128 2009-10-27 12:25:57Z alexions $
//
if ( !defined('AREA') ) { die('Access denied'); }

function fn_settings_variants_addons_google_sitemap_site_change()
{
	return fn_google_sitemap_get_frequency();
}

function fn_settings_variants_addons_google_sitemap_site_priority()
{
	return fn_google_sitemap_get_priority();
}

function fn_settings_variants_addons_google_sitemap_products_change()
{
	return fn_google_sitemap_get_frequency();
}

function fn_settings_variants_addons_google_sitemap_products_priority()
{
	return fn_google_sitemap_get_priority();
}

function fn_settings_variants_addons_google_sitemap_categories_change()
{
	return fn_google_sitemap_get_frequency();
}

function fn_settings_variants_addons_google_sitemap_categories_priority()
{
	return fn_google_sitemap_get_priority();
}

function fn_settings_variants_addons_google_sitemap_pages_change()
{
	return fn_google_sitemap_get_frequency();
}

function fn_settings_variants_addons_google_sitemap_pages_priority()
{
	return fn_google_sitemap_get_priority();
}

function fn_settings_variants_addons_google_sitemap_news_change()
{
	return fn_google_sitemap_get_frequency();
}

function fn_settings_variants_addons_google_sitemap_news_priority()
{
	return fn_google_sitemap_get_priority();
}

function fn_settings_variants_addons_google_sitemap_extended_change()
{
	return fn_google_sitemap_get_frequency();
}

function fn_settings_variants_addons_google_sitemap_extended_priority()
{
	return fn_google_sitemap_get_priority();
}

?>