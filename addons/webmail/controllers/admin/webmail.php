<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2009 Simbirsk Technologies Ltd. All rights reserved.    *
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
// $Id: webmail.php 7915 2009-08-28 13:14:45Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$webmail_path = Registry::get('config.http_location') . '/addons/webmail/lib/webmail/web/';

if ($mode == 'manage') {
	return array(CONTROLLER_STATUS_REDIRECT, $webmail_path . 'webmail.php');
}

?>