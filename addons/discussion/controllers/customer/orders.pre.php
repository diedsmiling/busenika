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
// $Id: orders.pre.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($mode == 'initiate_discussion' && !empty($_REQUEST['order_id'])) {
	$_data = array (
		'object_id' => $_REQUEST['order_id'],
		'object_type' => 'O',
		'type' => 'C'
	);

	$discussion = fn_get_discussion($_REQUEST['order_id'], 'O');
	if (!empty($discussion['thread_id'])) {
		db_query("UPDATE ?:discussion SET ?u WHERE thread_id = ?i", $_data, $discussion['thread_id']);
	} else {
		db_query("REPLACE INTO ?:discussion ?e", $_data);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "orders.details?order_id=$_REQUEST[order_id]&selected_section=discussion");
}

?>