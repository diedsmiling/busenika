<?php

//
// $Id: news.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	return;
}

if ($mode == 'update') {
	$discussion = fn_get_discussion($_REQUEST['news_id'], 'N');
	if (!empty($discussion) && $discussion['type'] != 'D') {
		Registry::set('navigation.tabs.discussion', array (
			'title' => fn_get_lang_var('discussion_title_news'),
			'js' => true
		));
			
		$view->assign('discussion', $discussion);
	}
}
?>
