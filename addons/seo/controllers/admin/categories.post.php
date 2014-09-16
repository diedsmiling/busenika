<?php

//
// $Id: categories.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	return;
}


if ($mode == 'm_update') {
	$selected_fields = $_SESSION['selected_fields'];

	if (!empty($selected_fields['extra']) && in_array('seo_name', $selected_fields['extra'])) {

		$field_groups = $view->get_var('field_groups');
		$filled_groups = $view->get_var('filled_groups');

		$field_groups['A']['seo_name'] = 'categories_data';
		$filled_groups['A']['seo_name'] = fn_get_lang_var('seo_name');

		$view->assign('field_groups', $field_groups);
		$view->assign('filled_groups', $filled_groups);
	}
}

?>