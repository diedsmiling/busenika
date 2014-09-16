<?php
//
// $Id: products.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	return;
}

if ($mode == 'update') {
	$discussion = fn_get_discussion($_REQUEST['product_id'], 'P');
	if (!empty($discussion) && $discussion['type'] != 'D') {
		Registry::set('navigation.tabs.discussion', array (
			'title' => fn_get_lang_var('discussion_title_product'),
			'js' => true
		));
		
		$view->assign('discussion', $discussion);
	}

} elseif ($mode == 'manage') {
		
	$selected_fields = $view->get_var('selected_fields');

	$selected_fields[] = array(
		'name' => '[products_data][discussion_type]',	
		'text' => fn_get_lang_var('discussion_title_product')
	);

	$view->assign('selected_fields', $selected_fields);


} elseif ($mode == 'm_update') {

	$selected_fields = $_SESSION['selected_fields'];

	if (!empty($selected_fields['products_data'])) {

		$field_groups = $view->get_var('field_groups');
		$filled_groups = $view->get_var('filled_groups');

		
		$field_groups['S']['discussion_type'] = array(
			'name' => 'products_data',
				'variants' => array (
					'D' => fn_get_lang_var('disabled'),
					'C' => fn_get_lang_var('communication'),
					'R' => fn_get_lang_var('rating'),
					'B' => fn_get_lang_var('all')
				)
		);

		$filled_groups['S']['discussion_type'] = fn_get_lang_var('discussion_title_product');

		$view->assign('field_groups', $field_groups);
		$view->assign('filled_groups', $filled_groups);
	}
}


?>
