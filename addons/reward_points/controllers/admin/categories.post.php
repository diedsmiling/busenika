<?php

//
// $Id: categories.post.php 8113 2009-10-22 09:00:54Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

	return;
}

if ($mode == 'update') {

	// Add new tab to page sections
	// [Page sections]
	// Add new tab to page sections
	Registry::set('navigation.tabs.reward_points', array (
		'title' => fn_get_lang_var('reward_points'),
		'js' => true
	));


	// [/Page sections]

	$view->assign('reward_points', fn_get_reward_points($_REQUEST['category_id'], CATEGORY_REWARD_POINTS));
	$view->assign('object_type', CATEGORY_REWARD_POINTS);	
	
} elseif ($mode == 'add') {

	// Add new tab to page sections
	// [Page sections]
	Registry::set('navigation.tabs.reward_points', array (
		'title' => fn_get_lang_var('reward_points'),
		'js' => true
	));
	// [/Page sections]
		
	$view->assign('object_type', CATEGORY_REWARD_POINTS);	
}

$view->assign('reward_usergroups', fn_array_merge(fn_get_default_usergroups(), fn_get_usergroups('C')));

/** /Body **/
?>
