<?php

//
// $Id: orders.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'update_details') {
		if (!empty($_REQUEST['posts']) && is_array($_REQUEST['posts'])) {

			foreach ($_REQUEST['posts'] as $p_id => $data) {
				db_query("UPDATE ?:discussion_posts SET ?u WHERE post_id = ?i", $data, $p_id);
				db_query("UPDATE ?:discussion_messages SET ?u WHERE post_id = ?i", $data, $p_id);
				db_query("UPDATE ?:discussion_rating SET ?u WHERE post_id = ?i", $data, $p_id);
			}
		}

		if (!empty($_REQUEST['discussion'])) {
			$discussion = fn_get_discussion($_REQUEST['discussion']['object_id'], $_REQUEST['discussion']['object_type']);

			if (!empty($discussion['thread_id']) && $discussion['type'] != $_REQUEST['discussion']['type']) {
				db_query('UPDATE ?:discussion SET ?u WHERE thread_id = ?i', $_REQUEST['discussion'], $discussion['thread_id']);
				if ($_REQUEST['discussion']['type'] != 'D') {
					$_REQUEST['selected_section'] = 'discussion';
				}
			} elseif (empty($discussion['thread_id'])) {
				db_query("REPLACE INTO ?:discussion ?e", $_REQUEST['discussion']);
				if ($_REQUEST['discussion']['type'] != 'D') {
					$_REQUEST['selected_section'] = 'discussion';
				}
			}
		}
	}
}

if ($mode == 'details') {

	$discussion = fn_get_discussion($_REQUEST['order_id'], 'O');
	if (!empty($discussion) && $discussion['type'] != 'D') {
		Registry::set('navigation.tabs.discussion', array (
			'title' => fn_get_lang_var('communication'),
			'js' => true
		));

		$view->assign('discussion', $discussion);
	}
}

?>
