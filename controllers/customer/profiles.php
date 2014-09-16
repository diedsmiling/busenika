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
// $Id: profiles.php 10229 2010-07-27 14:21:39Z 2tl $
//
if ( !defined('AREA') )	{ die('Access denied');	}

if (Registry::get('settings.General.secure_auth') == 'Y' && !defined('HTTPS')) {
	return array(CONTROLLER_STATUS_REDIRECT, Registry::get('config.https_location') . '/' . Registry::get('config.current_url'));
}

if (!empty($auth['user_id']) && $mode == 'add') {
	return array(CONTROLLER_STATUS_REDIRECT, "profiles.update");
}

if (empty($auth['user_id']) && $mode == 'update') {
	return array(CONTROLLER_STATUS_REDIRECT, "auth.login_form?return_url=".urlencode(Registry::get('config.current_url')));
}

?>