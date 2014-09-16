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
// $Id: ideal_xml.php 8138 2009-10-28 12:45:37Z zeke $
//

if (!defined('AREA')) { die('Access denied'); }

$responses = array(
	"100" => "The transaction has been approved by the credit company",
	"101" => "The transaction has been rejected by the credit company",
	"102" => "The transaction failed. A failure happened when processing at the credit company",
	"800" => "This iDeal-transaction has not been fully processed",
	"801" => "This iDeal-transaction is successfully processed",
	"802" => "This iDeal-transaction is canceled by the consumer",
	"803" => "This iDeal-transaction has not been processed within the allowed time",
	"804" => "This iDeal-transaction failed for an unknown reason with the bank",
	"810" => "Issuer (bank) is unknown.",
	"811" => "This iDeal-transaction failed by technical reasons",
	"812" => "The entrance code for this transaction is invalid",
	"813" => "Acquirer code is unknown (intern)",
	"814" => "System-failure. Status could not be updated. (no defined status).",
	"815" => "IDeal transaction not found (at XML-StatusRequest)"
);


if (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'notify') {
		$order_info = fn_get_order_info($_REQUEST['order_id']);
		$processor_data = fn_get_payment_method_data($order_info['payment_id']);

		$_order_id = ($order_info['repaid']) ? ($_REQUEST['order_id'] . '_' . $order_info['repaid']) : $_REQUEST['order_id'];
		$_order_total = $order_info['total'] * 100;

		$pp_response["transaction_id"] = $_REQUEST['bpe_trx'];
		$pp_response["reason_text"] = $responses[$_REQUEST['bpe_result']];
		$_signature = md5($_REQUEST['bpe_trx'] . $_REQUEST['bpe_timestamp'] . $processor_data["params"]["merchant_id"] . $_REQUEST['order_id'] . $_order_id .
		$processor_data["params"]["currency"] . $_order_total . $_REQUEST['bpe_result'] . $processor_data["params"]["test"] . $processor_data["params"]["merchant_key"]);

		if (in_array($_REQUEST['bpe_result'], array('801', '100')) && $_REQUEST['bpe_signature2'] == $_signature) {
			$pp_response['order_status'] = 'P';
		} elseif (in_array($_REQUEST['bpe_result'], array('811'))) {
			$pp_response['order_status'] = 'O'; // still waiting for the response
		} else {
			$pp_response['order_status'] = 'F';
		}

		fn_finish_payment($_REQUEST['order_id'], $pp_response, false);
		fn_order_placement_routines($_REQUEST['order_id']);
	}

} else {

$_order_id = ($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id;
$_order_total = $order_info['total'] * 100;
$return_url = Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.notify&payment=ideal_xml&order_id=$order_id";

$signature = md5($processor_data['params']['merchant_id'] . $_order_id . $_order_total . $processor_data['params']['currency'] . $processor_data['params']['test'] . $processor_data['params']['merchant_key']);

$post_url = ($processor_data['params']['payment_type'] == 'cc') ? "https://payment.buckaroo.nl/sslplus/request_for_authorization.asp" : "https://payment.buckaroo.nl/gateway/ideal_payment.asp";

echo <<<EOT
<html>
<body onLoad="javascript: document.process.submit();">
<form method="post" action="{$post_url}" name="process">
	<input type="hidden" name="BPE_Merchant" value="{$processor_data['params']['merchant_id']}">
	<input type="hidden" name="BPE_Signature2" value="{$signature}">
	<input type="hidden" name="BPE_Amount" value="{$_order_total}">
	<input type="hidden" name="BPE_Description" value="{$processor_data['params']['description']}">
	<input type="hidden" name="BPE_Currency" value="{$processor_data['params']['currency']}">
	<input type="hidden" name="BPE_Invoice" value="{$order_id}">
	<input type="hidden" name="BPE_Language" value="{$processor_data['params']['language']}">
	<input type="hidden" name="BPE_Mode" value="{$processor_data['params']['test']}">
	<input type="hidden" name="BPE_Reference" value="{$_order_id}">
	<input type="hidden" name="BPE_Return_Success" value="{$return_url}">
	<input type="hidden" name="BPE_Return_Reject" value="{$return_url}">
	<input type="hidden" name="BPE_Return_Error" value="{$return_url}">
	<input type="hidden" name="BPE_Autoclose_Popup" value="0">
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'Buckaroo server', $msg);
echo <<<EOT
	</form>
   <p><div align=center>{$msg}</div></p>
 </body>
</html>
EOT;
exit;

}
?>
