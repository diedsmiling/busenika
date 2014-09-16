<?php

//
// $Id: actions.post.php 7502 2009-05-19 14:54:59Z zeke $
//
if ( !defined('AREA') ) { die('Access denied'); }

/**
 * Check if mod_rewrite is active and clean up templates cache
 */
function fn_settings_actions_addons_seo(&$new_value, $old_value)
{
	if ($new_value == 'A') {
		$result = fn_http_request('GET', Registry::get('config.http_location') . '/catalog.html?version');
		if (strpos($result[0]['RESPONSE'], '200 OK') === false) {
			$new_value = 'D';
			fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('warning_seo_urls_disabled'));
		}
	}

	fn_rm(DIR_COMPILED . 'customer');

	return true;
}

/*
 * Check if "catalog" directory is writable
 */
function fn_settings_actions_addons_seo_html_catalog(&$new_value, $old_value)
{
	if ($new_value == 'Y') {
		if (!is_writable(DIR_ROOT . '/catalog')) {
			$s = fn_get_lang_var('text_cannot_write_directory');
			fn_set_notification('E', fn_get_lang_var('error'), str_replace('[directory]', DIR_ROOT . '/catalog', $s));
			$new_value = 'N';
		}
	}

	return true;
}

?>