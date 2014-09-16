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
// $Id: gate2shop.php 0000 2010-03-01 07:57:34Z 2tl $
//

if (defined('PAYMENT_NOTIFICATION')) {

	if (!defined('AREA') ) { die('Access denied'); }

	fn_order_placement_routines($_REQUEST['order_id'], false);

} else {
	if (!defined('AREA')) {

		// response from payment
		DEFINE ('AREA', 'C');
		DEFINE ('AREA_NAME' ,'customer');

		require '../prepare.php';
		require '../init.php';

		if (!empty($_REQUEST['invoice_id'])) {
			$order_id = $_REQUEST['invoice_id'];
		} elseif (!empty($_REQUEST['ClientUniqueID'])) {
			$order_id = $_REQUEST['ClientUniqueID'];
		} elseif (!empty($_REQUEST['merchant_unique_id'])) {
			$order_id = $_REQUEST['merchant_unique_id'];
		} else {
			exit;
		}

		if (!fn_check_payment_script('gate2shop.php', $order_id, $processor_data)) {
			exit;
		}

		$keys = array('TransactionID', 'ErrCode', 'ExErrCode', 'Status');
		foreach ($keys as $k) {
			if (empty($_REQUEST[$k])) {
				$_REQUEST[$k] = '';
			}
		}

		$hashed = md5($processor_data['params']['secret_string'] . $_REQUEST['TransactionID'] . $_REQUEST['ErrCode'] . $_REQUEST['ExErrCode'] . $_REQUEST['Status']);

		$pp_response = array();

		if (!empty($_REQUEST['Status']) && $_REQUEST['Status'] == 'APPROVED' && !empty($_REQUEST['responsechecksum']) && $_REQUEST['responsechecksum'] == $hashed) {
			$pp_response['order_status']   = 'P';
			$pp_response['reason_text']    = fn_get_lang_var('transaction_approved');
			$pp_response['transaction_id'] = !empty($_REQUEST['TransactionID']) ? $_REQUEST['TransactionID'] : '';
		} else {
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text']  = '';
			if (!empty($_REQUEST['Reason'])) {
				$pp_response['reason_text'] .= ($_REQUEST['Reason'] . ' ;');
			}
			if (!empty($_REQUEST['ExErrCode']) && !empty($_REQUEST['error'])) {
				$pp_response['reason_text'] .= ($_REQUEST['ExErrCode'] . ': ' . $_REQUEST['error'] . ';');
			}
			$pp_response['reason_text'] .= fn_get_lang_var('md5_checksum_failed');
		}

		fn_finish_payment($order_id, $pp_response);

		$current_location = Registry::get('config.current_location');

		if (!empty($_REQUEST['unknownParameters']) && strpos($_REQUEST['unknownParameters'], 'admin') !== false) {
			$index_script = Registry::get('config.admin_index');
		} else {
			$index_script = Registry::get('config.customer_index');
		}

		echo "<html><body onLoad=\"javascript: self.location='$current_location/$index_script?dispatch=payment_notification.return&payment=gate2shop&order_id=$order_id'\"></body></html>";

	} else {

		if (!defined('AREA') ) { die('Access denied'); }

		$post = array();

		$post['merchant_site_id']   = $processor_data['params']['merchant_site_id'];
		$post['merchant_id']        = $processor_data['params']['merchant_id'];
		$post['time_stamp']         = gmdate('Y-m-d.H:i:s');
		$post['currency']           = $processor_data['params']['currency'];
		$post['total_amount']       = $order_info['total']; 
		$post['numberofitems']      = 1;
		$post['invoice_id']         = $order_id;
		$post['merchant_unique_id'] = $processor_data['params']['order_prefix'] . $order_id . ($order_info['repaid'] ? "_{$order_info['repaid']}" : '');

		$post['item_name_1']   = fn_get_lang_var('order_id') . ': ' . $processor_data['params']['order_prefix'] . $order_id . ($order_info['repaid'] ? "_{$order_info['repaid']}" : '');
		$post['item_name_1']   = fn_convert_encoding('UTF-8', 'ISO-8859-1', $post['item_name_1']);
		$post['item_number_1'] = 1;
		$post['item_quantity_1']    = 1;
		$post['item_amount_1'] = $post['total_amount'];
		$products2checksum = ($post['item_name_1'] . $post['item_amount_1'] . $post['item_quantity_1']);

		$post['first_name'] = $order_info['b_firstname'];
		$post['last_name']  = $order_info['b_lastname'];
		$post['email']      = $order_info['email'];
		$post['phone1']     = $order_info['phone'];
		$post['address1']   = $order_info['b_address'];
		$post['address2']   = $order_info['b_address_2'];
		$post['city']       = $order_info['b_city'];
		$post['state']      = $order_info['b_state'];
		$post['zip']        = $order_info['b_zipcode'];
		$post['country']    = $order_info['b_country'];

		$post['checksum'] = md5($processor_data['params']['secret_string'] . $post['merchant_id'] . $post['currency'] . $post['total_amount'] . $products2checksum . $post['time_stamp']);

		$post['version'] = '3.0.0';

		if (AREA == 'A') {
			$post['area'] = 'admin';
		}

		echo <<<EOT
<html>
<body onLoad="document.process.submit();">
<form method="post" action="https://secure.gate2shop.com/ppp/purchase.do" name="process">
EOT;

		foreach ($post as $name => $value) {
			echo "<input type=\"hidden\" name=\"$name\" value=\"$value\" />\n";
		}

		$msg = fn_get_lang_var('text_cc_processor_connection');
		$msg = str_replace('[processor]', 'Gate2Shop', $msg);
		echo "\n";
		echo <<<EOT
</form>
<p><div align=center>{$msg}</div></p>
</body>
</html>
EOT;
		exit;
	}
}

?>