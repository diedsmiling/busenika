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
// $Id: mcpe.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

// User Return URL = https://mydomain.com/store/index.php?dispatch=payment_notification.notify
// Payment Response Notification (PRN) URL = https://mydomain.com/store/index.php?dispatch=payment_notification.set


// Return from paypal website
if (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'notify') {
		fn_order_placement_routines($_REQUEST['order_id']);
	}

} else {
	$test = $processor_data['params']['mode'];
	$_order_id = ($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id;
echo <<<EOT
<html>
<body onLoad="document.process.submit();">
<form method="post" action="https://secure.metacharge.com/mcpe/purser" name="process"> 
EOT;
if (!empty($processor_data['params']['mode'])) {
echo '<input type="hidden" name="intTestMode" value="'.$test.'">'; 
}
echo <<<EOT
	<input type="hidden" name="intInstID" value="{$processor_data['params']['merchant_id']}" />
	<input type="hidden" name="strCartID" value="{$_order_id}" />
	<input type="hidden" name="fltAmount" value="{$order_info['total']}" />
	<input type="hidden" name="strCurrency" value="{$processor_data['params']['currency']}" />
	<input type="hidden" name="strDesc" value="Payment for Order {$order_id}" />
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'metacharge.com', $msg);
echo <<<EOT
	</form>
	<p><div align=center>{$msg}</div></p>
 </body>
</html>
EOT;

}
exit;
?>
