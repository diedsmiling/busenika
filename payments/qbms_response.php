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
// $Id: qbms_response.php 7502 2009-05-19 14:54:59Z zeke $
//
if ( !defined('AREA') ) {
	if (!empty($_REQUEST['conntkt'])) {
		
		// Set the connection ticket to the payment params
		DEFINE ('AREA', 'C');
		DEFINE ('AREA_NAME' ,'customer');
		require './../prepare.php';
		require './../init.php'; 
	
		$payments = db_get_fields("SELECT a.payment_id FROM ?:payments as a LEFT JOIN ?:payment_processors as b ON b.processor_id = a.processor_id WHERE b.processor_script = 'qbms.php'");
		foreach ($payments as $payment_id) {
			$processor_data = fn_get_payment_method_data($payment_id);
			if ($processor_data["params"]["app_id"] == $_REQUEST['appid']) {
				$processor_data["params"]["connection_ticket"] = $_REQUEST['conntkt'];
				$_data = array (
					'params' => serialize($processor_data['params']),
				);
				db_query("UPDATE ?:payments SET ?u WHERE payment_id = ?i", $_data, $payment_id);
			}
		}
	} else {
		die('Access denied'); 
	}
}

?>