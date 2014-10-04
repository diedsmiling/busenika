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
// $Id: index.php 7636 2009-06-30 07:03:06Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// Forbid posts to index script
//
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	return array(CONTROLLER_STATUS_NO_PAGE);
}



?>
