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
// $Id: site_layout.php 9938 2010-07-01 14:18:59Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	fn_trusted_vars("lang_data");

	$suffix = '';

	if ($mode == 'update_design_mode') {
		db_query("UPDATE ?:settings SET value = ?s WHERE option_name = ?s AND section_id = ?s", ($action == $_REQUEST['design_mode'] ? 'Y' : 'N'), $_REQUEST['design_mode'], '');
		if (!empty($_REQUEST['disable_mode'])) {
			db_query("UPDATE ?:settings SET value = 'N' WHERE option_name = ?s AND section_id = ?s", $_REQUEST['disable_mode'], '');
		}
		fn_rm(DIR_COMPILED . 'customer', false);
		fn_rm(DIR_COMPILED . 'admin', false);

		$suffix = '.design_mode';
	}

	if ($mode == 'update_logos') {
		$logos = fn_filter_uploaded_data('logotypes');

		$areas = fn_get_manifest_definition();

		fn_save_logo_alt($areas);

		// Update customer logotype
		if (!empty($logos)) {
			foreach ($logos as $type => $logo) {
				$area = $areas[$type];
				$manifest = parse_ini_file(DIR_SKINS . Registry::get('settings.skin_name_' . $area['skin']) . '/' . SKIN_MANIFEST, true);

				$filename = DIR_SKINS . Registry::get('settings.skin_name_' . $area['skin']) . '/' . $area['path'] . '/images/' . $logo['name'];

				if (fn_copy($logo['path'], $filename)) {
					list($w, $h, ) = fn_get_image_size($filename);

					$manifest[$area['name']]['filename'] = $logo['name'];
					$manifest[$area['name']]['width'] = $w;
					$manifest[$area['name']]['height'] = $h;

					fn_write_ini_file(DIR_SKINS . Registry::get('settings.skin_name_' . $area['skin']) . '/' . SKIN_MANIFEST, $manifest);
				} else {
					$text = fn_get_lang_var('text_cannot_create_file');
					$text = str_replace('[file]', $filename, $text);
					fn_set_notification('E', fn_get_lang_var('error'), $text);
				}
				@unlink($logo['path']);
			}
		}
		$suffix = '.logos';
	}

	return array(CONTROLLER_STATUS_OK, "site_layout" . $suffix);
}

if ($mode == 'logos') {

	$view->assign('manifest_definition', fn_get_manifest_definition());
	$view->assign('manifests', array(
		'customer' => fn_get_manifest('customer'),
		'admin' => $view->get_var('manifest')
	));

} elseif ($mode == 'design_mode') {


}

?>