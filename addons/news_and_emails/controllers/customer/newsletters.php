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
// $Id: newsletters.php 9088 2010-03-15 10:40:51Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Add email to maillist
	if ($mode == 'add_subscriber') {

		if (empty($_REQUEST['subscribe_email']) || fn_validate_email($_REQUEST['subscribe_email']) == false) {
			$msg = fn_get_lang_var('error_invalid_emails');
			$msg = str_replace('[emails]', $_REQUEST['subscribe_email'], $msg);
			fn_set_notification('E', fn_get_lang_var('error'), $msg);
		} elseif (empty($_REQUEST['mailing_lists'])) {
			$msg = fn_get_lang_var('warning_subscr_need_list');
			$msg = str_replace('[email]', $_REQUEST['subscribe_email'], $msg);
			fn_set_notification('W', fn_get_lang_var('warning'), $msg);
		} else {
			// First check if subscriber's email already in the list
			$subscriber = db_get_row("SELECT * FROM ?:subscribers WHERE email=?s", $_REQUEST['subscribe_email']);
			if (empty($subscriber)) {
				$_data = array(
					'email' => $_REQUEST['subscribe_email'],
					'timestamp' => TIME,
				);

				$subscriber_id = db_query("INSERT INTO ?:subscribers ?e", $_data);

				$subscriber = db_get_row("SELECT * FROM ?:subscribers WHERE subscriber_id = ?i", $subscriber_id);
			} else {
				$subscriber_id = $subscriber['subscriber_id'];
			}

			// update subscription data. If there is no any registration autoresponders, we set confirmed=1
			// so user doesn't need to activate subscription
			fn_update_subscriptions($subscriber_id, $_REQUEST['mailing_lists'], $_REQUEST['newsletter_format'], NEWSLETTER_SAVE_UNCHECKED, NULL, fn_get_notification_rules(true));

			fn_set_notification('N', fn_get_lang_var('congratulations'), fn_get_lang_var('text_subscriber_added'));

			/*} else {
				fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('error_email_already_subscribed'));
			}*/
		}
	}

	return array(CONTROLLER_STATUS_REDIRECT);
}

if ($mode == 'unsubscribe') {
	if (!empty($_REQUEST['key']) && !empty($_REQUEST['list_id']) && !empty($_REQUEST['s_id'])) {
		if (!empty($_REQUEST['list_id'])) {
			$num = db_get_field("SELECT COUNT(*) FROM ?:user_mailing_lists WHERE unsubscribe_key = ?s AND list_id = ?i AND subscriber_id = ?i", $_REQUEST['key'], $_REQUEST['list_id'], $_REQUEST['s_id']);

			if (!empty($num)) {
				db_query("DELETE FROM ?:user_mailing_lists WHERE unsubscribe_key = ?s AND list_id = ?i AND subscriber_id = ?i", $_REQUEST['key'], $_REQUEST['list_id'], $_REQUEST['s_id']);

				// if this subscription was the only one, we delete the subscriber as each subscruber
				// must have at least one subscription
				$left = db_get_field("SELECT COUNT(*) FROM ?:user_mailing_lists WHERE subscriber_id = ?i", $_REQUEST['s_id']);
				if (empty($left)) {
					db_query("DELETE FROM ?:subscribers WHERE subscriber_id = ?i", $_REQUEST['s_id']);
				}

				fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_subscriber_removed'));
			}
		}
	}

	return array(CONTROLLER_STATUS_REDIRECT, $index_script);

} elseif ($mode == 'activate') {
	if (!empty($_REQUEST['key']) && !empty($_REQUEST['list_id']) && !empty($_REQUEST['s_id'])) {
		if (!empty($_REQUEST['list_id'])) {
			$num = db_get_field("SELECT COUNT(*) FROM ?:user_mailing_lists WHERE activation_key = ?s AND list_id = ?i AND subscriber_id = ?i", $_REQUEST['key'], $_REQUEST['list_id'], $_REQUEST['s_id']);

			if (!empty($num)) {
				db_query('UPDATE ?:user_mailing_lists SET ?u WHERE activation_key = ?s AND list_id = ?i AND subscriber_id = ?i', array('confirmed' => 1), $_REQUEST['key'], $_REQUEST['list_id'], $_REQUEST['s_id']);

				fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_subscriber_activated'));
			}
		}
	}

	return array(CONTROLLER_STATUS_REDIRECT, $index_script);

} elseif ($mode == 'track') {
	if (!empty($_REQUEST['link'])) {
		list($link_id, $newsletter_id, $campaign_id) = explode('-', $_REQUEST['link']);

		$_where = array(
			'link_id' => (int) $link_id,
			'newsletter_id' => (int) $newsletter_id,
			'campaign_id' => (int) $campaign_id
		);

		$link = db_get_row("SELECT * FROM ?:newsletter_links WHERE ?w", $_where);

		if (!empty($link)) {
			$link['clicks']++;
			db_query("UPDATE ?:newsletter_links SET clicks=?i WHERE ?w", $link['clicks'], $_where);
		}

		return array(CONTROLLER_STATUS_REDIRECT, $link['url'], true);
	}
}

?>