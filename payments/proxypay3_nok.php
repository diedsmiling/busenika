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
// $Id: proxypay3_nok.php 7502 2009-05-19 14:54:59Z zeke $
//

DEFINE ('AREA', 'C');
DEFINE ('AREA_NAME' ,'customer');

require './../prepare.php';
require './../init.php';

$ref = empty($_REQUEST['ref']) ? '0' : $_REQUEST['ref'];

$order_id = (strpos($ref, '_')) ? substr($ref, 0, strpos($ref, '_')) : $ref;

fn_redirect(Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.notify.nok&payment=proxypay3&order_id=$order_id");
?>
