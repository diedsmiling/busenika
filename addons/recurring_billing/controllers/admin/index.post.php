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
// $Id: index.php 7688 2009-07-10 05:58:05Z zeke $
//

if ( !defined('AREA') )	{ die('Access denied');	}

// Generate dashboard
if ($mode == 'index') {

	$events = fn_get_recurring_events();

	if (!fn_is_empty($events)) {
		$msg = fn_get_lang_var('rb_have_events');
		$msg = str_replace('[link]', fn_url("subscriptions.events"), $msg);
		fn_delete_notification('rb_events');
		fn_set_notification('N', fn_get_lang_var('notice'), $msg, true, 'rb_events');
	}

}

?>