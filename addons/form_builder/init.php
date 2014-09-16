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
// $Id: init.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

// form page type
define('PAGE_TYPE_FORM', 'F');

// Form element types
define('FORM_CHECKBOX', 'C');
define('FORM_HEADER', 'H');
define('FORM_INPUT', 'I');
define('FORM_MULTIPLE_CB', 'N');
define('FORM_MULTIPLE_SB', 'M');
define('FORM_RADIO', 'R');
define('FORM_SELECT', 'S');
define('FORM_SEPARATOR', 'D');
define('FORM_TEXTAREA', 'T');
define('FORM_DATE', 'V');
define('FORM_EMAIL', 'Y');
define('FORM_EMAIL_CONFIRM', 'E');
define('FORM_NUMBER', 'Z');
define('FORM_PHONE', 'P');
define('FORM_COUNTRIES', 'X');
define('FORM_STATES', 'W');
define('FORM_FILE', 'F');
define('FORM_REFERER', 'A');
define('FORM_IP_ADDRESS', 'B');

// Special types
define('FORM_VARIANT', 'G');
define('FORM_RECIPIENT', 'J');
define('FORM_IS_SECURE', 'U');
define('FORM_SUBMIT', 'L');

fn_register_hooks(
	'delete_page',
	'update_page',
	'get_page_data',
	'page_object_by_type',
	'clone_page',
	'init_secure_controllers'
);




?>