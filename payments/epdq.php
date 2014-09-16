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
// $Id: epdq.php 7502 2009-05-19 14:54:59Z zeke $
//

if (!defined('AREA')) {
	if (!empty($_REQUEST['transactionstatus'])) {
		DEFINE ('AREA', 'C');
		DEFINE ('AREA_NAME' ,'customer');
		require './../prepare.php';
		require './../init.php';

		$order_id = (strpos($_REQUEST['oid'], '_')) ? substr($_REQUEST['oid'], 0, strpos($_REQUEST['oid'], '_')) : $_REQUEST['oid'];
		$order_info = fn_get_order_info($order_id);
		$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $order_id);
		$processor_data = fn_get_payment_method_data($payment_id);

		$_hash = md5($processor_data['params']['client_id'] . $processor_data['params']['password']);
		$pp_response['order_status'] = ($_hash == $_REQUEST['hash'] && $_REQUEST['transactionstatus'] == 'Success') ? 'P' : 'F';
		$pp_response["reason_text"] = $_REQUEST['transactionstatus'];
		fn_finish_payment($order_id, $pp_response);
		exit;

	} elseif (!empty($_REQUEST['oid'])) {
		// return
		DEFINE ('AREA', 'C');
		DEFINE ('AREA_NAME' ,'customer');
		require './../prepare.php';
		require './../init.php';

		$order_id = (strpos($_REQUEST['oid'], '_')) ? substr($_REQUEST['oid'], 0, strpos($_REQUEST['oid'], '_')) : $_REQUEST['oid'];

		fn_redirect(Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.notify&payment=epdq&order_id=$order_id");
		exit();

	} else { 
		die('Access denied'); 
	}
}

if (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'notify') {
		fn_order_placement_routines($_REQUEST['order_id']);
	}

} else {

	// define the remote cgi in readiness to call pullpage function
	$server="secure2.epdq.co.uk";
	$url="/cgi-bin/CcxBarclaysEpdqEncTool.e";

	// the following parameters have been obtained earlier in the merchant's webstore #clientid, passphrase, oid, currencycode, total
	$post = array();
	$post[] = "clientid=".$processor_data['params']['client_id'];
	$post[] = "password=".$processor_data['params']['password'];
	$post[] = "oid=".(($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id);
	$post[] = "chargetype=Auth";//.$processor_data['params']['type'];
	$post[] = "currencycode=".$processor_data['params']['currency'];
	$post[] = "total=".$order_info['total'];

	// perform the HTTP Post
	$response = fn_https_request("POST", $server.$url, $post, "&");
	$epdqdata = $response['1'];

	$return = Registry::get('config.current_location') . '/payments/epdq.php';
	$state['b_name'] = ($order_info['b_country'] == 'US') ? 'bstate' : 'bprovince';
	$state['b_value'] = ($order_info['b_country'] == 'US') ? $order_info['b_state'] : $order_info['b_state_descr'];
	$state['s_name'] = ($order_info['s_country'] == 'US') ? 'sstate' : 'sprovince';
	$state['s_value'] = ($order_info['s_country'] == 'US') ? $order_info['s_state'] : $order_info['s_state_descr'];

echo <<<EOT
<html>
 <body onLoad="javascript: document.process.submit();">
   <form method="post" action="https://secure2.epdq.co.uk/cgi-bin/CcxBarclaysEpdq.e" name="process">
	{$epdqdata}
	<input type="hidden" name="returnurl" value="{$return}" />
	<input type="hidden" name="merchantdisplayname" value="{$processor_data['params']['merchant_name']}" />
	<input type="hidden" name="cpi_logo" value="{$processor_data['params']['logo']}">
	<input type="hidden" name="email" value="{$order_info['email']}" />
	<input type="hidden" name="baddr1" value="{$order_info['b_address']}" />
	<input type="hidden" name="baddr2" value="{$order_info['b_address_2']}" />
	<input type="hidden" name="bcity" value="{$order_info['b_city']}" />
	<input type="hidden" name="{$state['b_name']}" value="{$state['b_value']}" />
	<input type="hidden" name="bcountry" value="{$order_info['b_country']}" />
	<input type="hidden" name="bpostalcode" value="{$order_info['b_zipcode']}" />
	<input type="hidden" name="saddr1" value="{$order_info['s_address']}">
	<input type="hidden" name="saddr2" value="{$order_info['s_address_2']}">
	<input type="hidden" name="scity" value="{$order_info['s_city']}">
	<input type="hidden" name="{$state['s_name']}" value="{$state['s_value']}" />
	<input type="hidden" name="scountry" value="{$order_info['s_country']}">
	<input type="hidden" name="spostalcode" value="{$order_info['s_zipcode']}">
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'ePDQ server', $msg);
echo <<<EOT
   </form>
 </body>
</html>
EOT;
}
exit;
?>
