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
// $Id: config.php 7777 2009-05-19 14:54:59Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

define('SMS_NOTIFICATIONS_SMS_LENGTH_UNICODE', 70);
define('SMS_NOTIFICATIONS_SMS_LENGTH', 159); // usually 160, but the euro sign and some others will be coded as 2 characters.
define('SMS_NOTIFICATIONS_SMS_LENGTH_CONCAT', 7); // If a message is concatenated, it reduces the number of characters contained in each message by 7

?>