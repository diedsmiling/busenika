<?php

//
// $Id: pages.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

/* POST data processing */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//
	// Processing multiple updating of page elements
	//
	/*if ($mode == 'm_update') {
		if (is_array($pages_description)) {
			foreach ($pages_description as $k => $v) {
				if (!empty($pages_description[$k]['page']) && isset($pages_description[$k]['seo_name'])) {
					$page_data = fn_get_page_data($k, CART_LANGUAGE);
					$object_name = (!empty($pages_description[$k]['seo_name'])) ? $pages_description[$k]['seo_name'] : $pages_description[$k]['page'];
					fn_create_seo_name($page_data['page_inner_id'], "a", $object_name);
				}
			}
		}
	}*/
}

if ($mode == 'm_update') {
	if (!empty($selected_fields['seo_name'])) {
		$field_groups['A']['seo_name'] = 'pages_description';
		$filled_groups['A']['seo_name'] = fn_get_lang_var('seo_name');
	}
}
/* /POST data processing */

?>
