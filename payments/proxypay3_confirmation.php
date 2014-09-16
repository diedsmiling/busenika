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
// $Id: proxypay3_confirmation.php 7502 2009-05-19 14:54:59Z zeke $
//

DEFINE ('AREA', 'C');
DEFINE ('AREA_NAME' ,'customer');

require './../prepare.php';
require './../init.php';

if (!empty($_REQUEST['Ref'])) {

	$order_id = (strpos($_REQUEST['Ref'], '_')) ? substr($_REQUEST['Ref'], 0, strpos($_REQUEST['Ref'], '_')) : $_REQUEST['Ref'];
	if (fn_check_payment_script('proxypay3.php', $order_id)) {
		fn_change_order_status($order_id, 'P', '', true);
		$pp_response = array();
		$pp_response['order_status'] = 'P';
		print '[OK]';
	} else {
		$pp_response['reason_text'] = 'Error in data confirmation'; // FIXME: this variable is not used
		print '[ERROR]';
	}
}
exit;
?>