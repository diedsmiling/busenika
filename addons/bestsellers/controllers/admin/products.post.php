<?php

//
// $Id: products.post.php 7745 2009-07-21 07:15:15Z alexions $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	return;
}

if ($mode == 'manage') {
	
	$selected_fields = $view->get_var('selected_fields');

	$selected_fields[] = array(
		'name' => '[data][sales_amount]',
		'text' => fn_get_lang_var('sales_amount')
	);

	$view->assign('selected_fields', $selected_fields);

} elseif ($mode == 'm_update') {

	$selected_fields = $_SESSION['selected_fields'];

	$field_groups = $view->get_var('field_groups');
	$filled_groups = $view->get_var('filled_groups');
	$field_names = $view->get_var('field_names');

	if (!empty($selected_fields['data']['sales_amount'])) {
		$field_groups['B']['sales_amount'] = 'products_data';
		$filled_groups['B']['sales_amount'] = fn_get_lang_var('sales_amount');
	}

	if (isset($field_names['sales_amount'])) {
		unset($field_names['sales_amount']);
	}

	$view->assign('field_groups', $field_groups);
	$view->assign('filled_groups', $filled_groups);
	$view->assign('field_names', $field_names);
}

?>