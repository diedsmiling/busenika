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
// $Id: worldpay.php 8163 2009-10-30 14:46:13Z lexa $
//

$avs_res = array(
	"0" => "Not Supported",
	"1" => "Not Checked",
	"2" => "Matched",
	"4" => "Not Matched",
	"8" => "Partially Matched"
);
$mode_test_declined = 101;
$mode_test = 100;
$mode_live = 0;
$card_holder_for_declined_test = "REFUSED";

if (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'notify') {
		fn_order_placement_routines($_REQUEST['order_id']);
	}

} elseif (!empty($_REQUEST['cartId']) && !empty($_REQUEST['transStatus'])) {

	DEFINE ('AREA', 'C');
	DEFINE ('AREA_NAME' ,'customer');
	require './../prepare.php';
	require './../init.php';

	$order_id = (strpos($_REQUEST['cartId'], '_')) ? substr($_REQUEST['cartId'], 0, strpos($_REQUEST['cartId'], '_')) : $_REQUEST['cartId'];

	$payment_id = db_get_field("SELECT ?:orders.payment_id FROM ?:orders WHERE ?:orders.order_id = ?i", $order_id);
	$processor_data = fn_get_payment_method_data($payment_id);

	$pp_response['order_status'] = ($_REQUEST['transStatus'] == 'Y' && (!empty($processor_data['params']['callback_password']) ? (!empty($_REQUEST['callbackPW']) && $_REQUEST['callbackPW'] == $processor_data['params']['callback_password']) : true)) ? 'P' : 'F';

	if ($_REQUEST['transStatus'] == 'Y') {
		$pp_response["reason_text"] = $_REQUEST['rawAuthMessage'];
		$pp_response["transaction_id"] = $_REQUEST['transId'];
		$pp_response['descr_avs'] = ("CVV (Security Code): " . $avs_res[substr($_REQUEST['AVS'], 0, 1)] . "; Postcode: " . $avs_res[substr($_REQUEST['AVS'], 1, 1)] . "; Address: " . $avs_res[substr($_REQUEST['AVS'], 2, 1)] . "; Country: " . $avs_res[substr($_REQUEST['AVS'], 3)]);
	}

	if (!empty($_REQUEST['testMode'])) {
		$pp_response["reason_text"] .= "; This a TEST Transaction";
	}

	fn_finish_payment($order_id, $pp_response, false);
	echo "<head><meta http-equiv='refresh' content='0; url=" . Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.notify&payment=worldpay&order_id=$order_id'></head><body><wpdisplay item=banner></body>";

} else {

	if ( !defined('AREA') ) { die('Access denied'); }

	$_order_id = ($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id;
	$s_id = Session::get_id();
	$sess_name = SESS_NAME;
	$card_holder = $processor_data['params']['test'] == $mode_test_declined ? $card_holder_for_declined_test : $order_info['b_firstname'] . ' ' . $order_info['b_lastname'];
	$test_mode_id = $processor_data['params']['test'] == $mode_test_declined ? $mode_test : $processor_data['params']['test'];
echo <<<EOT
<html>
<body onLoad="javascript: document.process.submit();">
<form method="post" action="https://secure.wp3.rbsworldpay.com/wcc/purchase" name="process">
	<input type="hidden" name="instId" value="{$processor_data['params']['account_id']}" />
	<input type="hidden" name="cartId" value="{$_order_id}" />
	<input type="hidden" name="amount" value="{$order_info['total']}" />
	<input type="hidden" name="currency" value="{$processor_data['params']['currency']}" />
	<input type="hidden" name="testMode" value="{$test_mode_id}" />
	<input type="hidden" name="authMode" value="{$processor_data['params']['authmode']}" />
	<input type="hidden" name="name" value="{$card_holder}" />
	<input type="hidden" name="tel" value="{$order_info['phone']}" />
	<input type="hidden" name="email" value="{$order_info['email']}" />
	<input type="hidden" name="address" value="{$order_info['b_address']} {$order_info['b_city']} {$order_info['b_state']} {$order_info['b_country']}" />
	<input type="hidden" name="postcode" value="{$order_info['b_zipcode']}" />
	<input type="hidden" name="country" value="{$order_info['b_country']}" /> 
	<input type="hidden" name="MC_{$sess_name}" value="{$s_id}" />
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'World Pay server', $msg);
echo <<<EOT
	</form>
 </body>
</html>
EOT;
exit;
}

?>

