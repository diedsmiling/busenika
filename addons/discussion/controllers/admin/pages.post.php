<?php

//
// $Id: pages.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	return;
}
if ($mode == 'update') {
	$page =  $view->get_var('page_data');
	$discussion = fn_get_discussion($_REQUEST['page_id'], 'A');	
	
	if (!empty($discussion) && $discussion['type'] != 'D' && $page['page_type'] != PAGE_TYPE_LINK) {
		Registry::set('navigation.tabs.discussion', array (
			'title' => fn_get_lang_var('discussion_title_page'),
			'js' => true
		));
		
		$view->assign('discussion', $discussion);
	}

} elseif ($mode == 'm_update') {
	if ($selected_fields['discussion_type'] == 'Y') {
		$field_names['discussion_type'] = fn_get_lang_var('discussion_title_page');
		$fields2update[] = 'discussion_type';
	}
}

?>
