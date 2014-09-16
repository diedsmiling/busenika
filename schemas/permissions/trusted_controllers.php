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
// $Id: trusted_controllers.php 0 2009-12-28 00:00:00Z 2tl $
//

$schema = array (
	'auth' => array (
		'allow' => true
	),
	'image' => array (
		'allow' => true
	),
	'payment_notification' => array (
		'allow' => true
	),
	'profiles' => array (
		'allow' => array (
			'password_reminder' => true,
		),
	),
);

?>