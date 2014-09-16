<?php

//
// $Id: init.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if (!empty($_REQUEST['discussion']) && !empty($_REQUEST['discussion']['object_id']) && !empty($_REQUEST['discussion']['object_type'])) {
		$discussion = fn_get_discussion($_REQUEST['discussion']['object_id'], $_REQUEST['discussion']['object_type']);
		if (!empty($discussion['thread_id'])) {
			db_query('UPDATE ?:discussion SET ?u WHERE thread_id = ?i', $_REQUEST['discussion'], $discussion['thread_id']);
		} else {
			db_query("REPLACE INTO ?:discussion ?e", $_REQUEST['discussion']);
		}
	}


	return;
}

?>