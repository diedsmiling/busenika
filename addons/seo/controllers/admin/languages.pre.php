<?php

//
// $Id: languages.pre.php 10230 2010-07-27 14:31:55Z klerik $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Delete seo names
	if ($mode == 'delete_languages') {
		if (!empty($_REQUEST['lang_codes'])) {
			foreach ((array)$_REQUEST['lang_codes'] as $v) {
				db_query("DELETE FROM ?:seo_names WHERE lang_code = ?s", $v);
			}
		}
	}

	// Add static seo rules
	if ($mode == 'add_languages') {
		$new_language = $_REQUEST['new_language'];
		if (!empty($new_language['lang_code']) && !empty($new_language['name'])) {
			$is_exists = db_get_field("SELECT COUNT(*) FROM ?:seo_names WHERE lang_code = ?s", $new_language['lang_code']);
			if (empty($is_exists)) {
				$global_total = db_get_fields("SELECT dispatch FROM ?:seo_names WHERE object_id = '0' AND type = 's' GROUP BY dispatch");
				foreach ($global_total as $disp) {
					fn_create_seo_name(0, 's', str_replace('.', '-', $disp), 0, $disp, $new_language['lang_code']);
 				}
			}
		}
	}
}

// Delete seo names
if ($mode == 'delete_language') {
	if (!empty($_REQUEST['lang_code'])) {
		db_query("DELETE FROM ?:seo_names WHERE lang_code = ?s", $_REQUEST['lang_code']);
	}
}

?>
