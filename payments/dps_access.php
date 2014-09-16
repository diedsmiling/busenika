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
// $Id: dps_access.php 9088 2010-03-15 10:40:51Z 2tl $
//

if (defined('PAYMENT_NOTIFICATION')) {
	if (!defined('AREA') ) { die('Access denied'); }

	if ($mode == 'notify') {
		
		$result = $_SESSION['dps_access']['result'];
		unset($_SESSION['dps_access']);
		
		include(DIR_PAYMENT_FILES . 'dps_files/pxaccess.inc');

		$payment_id = db_get_field("SELECT ?:payments.payment_id FROM ?:payments LEFT JOIN ?:payment_processors ON ?:payment_processors.processor_id = ?:payments.processor_id WHERE ?:payment_processors.processor_script = 'dps_access.php'");
		$processor_data = fn_get_payment_method_data($payment_id);
		
		$PxAccess_Url    = "https://www.paymentexpress.com/pxpay/pxpay.aspx";
		$PxAccess_Userid = $processor_data["params"]["user_id"]; //Change to your user ID
		$PxAccess_Key    =  $processor_data["params"]["key"]; //Your DES Key from DPS
		$Mac_Key = $processor_data["params"]["mac_key"]; //Your MAC key from DPS
		
		$pxaccess = new PxAccess($PxAccess_Url, $PxAccess_Userid, $PxAccess_Key, $Mac_Key);
		$enc_hex = $result;
		
		//getResponse method in PxAccess object returns PxPayResponse object
		//which encapsulates all the response data
		$rsp = $pxaccess->getResponse($enc_hex);
		$order_alias = $rsp->getMerchantReference();
		$order_id = (strpos($order_alias, '_')) ? substr($order_alias, 0, strpos($order_alias, '_')) : $order_alias;
		$pp_response = array();
		$pp_response['order_status'] = ($rsp->getSuccess() == "1") ? 'P' : 'F';
		$pp_response['reason_text'] = $rsp->getResponseText();
		if ($pp_response['order_status'] == 'P') {
			$pp_response['reason_text'] .= ("; Auth code: " . $rsp->getAuthCode());  // from bank
		}
		$pp_response['transaction_id'] = $rsp->getDpsTxnRef();
		
		if (fn_check_payment_script('dps_access.php', $order_id)) {
			fn_finish_payment($order_id, $pp_response, false);
		}
		
		fn_order_placement_routines($order_id);
	}

} elseif (isset($_REQUEST['result'])) {
	
	DEFINE('AREA', 'C');
	DEFINE('AREA_NAME', 'customer');

	require './../prepare.php';
	require './../init.php';
		
	$_SESSION['dps_access']['result'] = $_REQUEST['result'];
	fn_redirect(Registry::get('config.current_location') . '/' . Registry::get('config.customer_index') . "?dispatch=payment_notification.notify&payment=dps_access&order_id={$_SESSION['dps_access']['order_id']}");

} else {
	if (!defined('AREA') ) { die('Access denied'); }

	// This file is a SAMPLE showing redirect to Payments Page from PHP.
	//Inlcude PxAccess Objects
	include(DIR_PAYMENT_FILES . 'dps_files/pxaccess.inc');

	$PxAccess_Url    = "https://www.paymentexpress.com/pxpay/pxpay.aspx";
	$PxAccess_Userid = $processor_data["params"]["user_id"]; //Change to your user ID
	$PxAccess_Key    =  $processor_data["params"]["key"]; //Your DES Key from DPS
	$Mac_Key = $processor_data["params"]["mac_key"]; //Your MAC key from DPS

	$pxaccess = new PxAccess($PxAccess_Url, $PxAccess_Userid, $PxAccess_Key, $Mac_Key);

	$request = new PxPayRequest();
	$script_url = Registry::get('config.current_location') . '/payments/dps_access.php';
	$_order_id = ($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id;
	$_SESSION['dps_access']['order_id'] = $order_id;

	//Set up PxPayRequest Object
	$request->setAmountInput($order_info['total']);
	$request->setTxnData1("");	// whatever you want to appear
	$request->setTxnData2("");	// whatever you want to appear
	$request->setTxnData3("");	// whatever you want to appear
	$request->setTxnType("Purchase");
	$request->setInputCurrency($processor_data["params"]["currency"]);
	$request->setMerchantReference($_order_id); // fill this with your order number
	$request->setEmailAddress($order_info['email']);
	$request->setUrlFail($script_url);
	$request->setUrlSuccess($script_url);

	//Call makeResponse of PxAccess object to obtain the 3-DES encrypted payment request
	$request_string = $pxaccess->makeRequest($request);

echo <<<EOT
<html>
<body onLoad="javascript: self.location='{$request_string}';">
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'DPS Sever', $msg);
echo <<<EOT
   <p><div align=center>{$msg}</div></p>
 </body>
</html>
EOT;
exit;

}
?>