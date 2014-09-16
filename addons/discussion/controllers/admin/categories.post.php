<?php

//
// $Id: categories.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	return;
}

if ($mode == 'update') {

	$discussion = fn_get_discussion($_REQUEST['category_id'], 'C');
	if (!empty($discussion) && $discussion['type'] != 'D') {
		Registry::set('navigation.tabs.discussion', array (
			'title' => fn_get_lang_var('discussion_title_category'),
			'js' => true
		));

		$view->assign('discussion', $discussion);
	}

} elseif ($mode == 'm_update') {
	$selected_fields = $_SESSION['selected_fields'];

	if (!empty($selected_fields['extra']) && in_array('discussion_type', $selected_fields['extra'])) {

		$field_names = $view->get_var('field_names');
		$fields2update = $view->get_var('fields2update');

		$field_names['discussion_type'] = fn_get_lang_var('discussion_title_category');
		$fields2update[] = 'discussion_type';

		$view->assign('field_names', $field_names);
		$view->assign('fields2update', $fields2update);
	}
}

?>
