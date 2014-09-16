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
// $Id: actions.post.php 7502 2009-05-19 14:54:59Z zeke $
//
if ( !defined('AREA') ) { die('Access denied'); }

/**
 * Check if mod_rewrite is active and clean up templates cache
 */
function fn_settings_actions_addons_discussion_home_page_testimonials(&$new_value, $old_value)
{
	$dicussion = array();
	$discussion['type'] = $new_value;
	$discussion['object_type'] = 'E';
	$discussion['object_id'] = 0;

	fn_update_discussion($discussion);

	return true;
}

?>