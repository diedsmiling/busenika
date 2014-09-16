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
// $Id: view_conditions.php 9672 2010-05-31 12:55:46Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['rma'] = array (
	'list_mode' => 'returns',
	'update_mode' => 'details',
	'func' => 'fn_get_rma_returns',
	'item_id' => 'return_id'
);

?>