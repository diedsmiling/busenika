<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2004 Simbirsk Technologies Ltd. All rights reserved.    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/


//
// $Id: discussion.post.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'add_post') {
		// Send notification
		$post_data = $_REQUEST['post_data'];
		$object = fn_discussion_get_object_by_thread($post_data['thread_id']);
		$discussion_settings = Registry::get('addons.discussion');
		$discussion_object_types = fn_get_discussion_objects();
		$object_name = $discussion_object_types[$object['object_type']];

		if ($object['object_type'] == 'G' && AREA == 'C') {
			$event = db_get_row("SELECT email, type, owner FROM ?:giftreg_events WHERE event_id = ?i", $object['object_id']);
			if ($event['type'] == 'U') {// private event - get access key
				$ekey = db_get_field("SELECT ekey FROM ?:ekeys WHERE object_id = ?i AND object_type = 'O'", $object['object_id']);
			}
			$email_to = $event['email'];
			$email_from = Registry::get('settings.Company.company_site_administrator');
			$view_mail->assign('url', fn_url("events.update" . (empty($ekey) ? "?event_id=$object[object_id]": "?access_key=$ekey"), 'C', 'http'));

			fn_send_mail($email_to, $email_from, 'addons/discussion/notification_subj.tpl', 'addons/discussion/notification.tpl');
		}

	}
}

?>