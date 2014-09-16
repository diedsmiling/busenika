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
// $Id: itransact_split_form.php 0000 2007-09-14 07:57:34Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {
	
	// to avoid bug with the '?' sign
	foreach ($_REQUEST as $k => $v) {
		if (preg_match("/\?/", $v)) {
			$tmp = explode('?', $v);
			$_REQUEST[$k] = $tmp[0];
			$tmp = explode('=', $tmp[1]);
			$_REQUEST[$tmp[0]] = $tmp[1];
		}
	}
	
	$order_id = $_REQUEST['order_id'];

	if (!fn_check_payment_script('itransact_split_form.php', $order_id, $processor_data)) {
		exit;
	}
	
	$order_info = fn_get_order_info($order_id);
	
	$pp_response = array();
	
	$check_cntrl = md5('CNTRL_VALUE' . $order_id . $processor_data['params']['merchant_name'] . $order_info['total'] . $processor_data['params']['secret_key']);
	
	if (!empty($_REQUEST['xid']) && preg_match("/\d+/", $_REQUEST['xid']) && $check_cntrl == $_REQUEST['cntrl']) {
		$pp_response['order_status']   = 'P';
		$pp_response['reason_text']    = fn_get_lang_var('transaction_approved');
		$pp_response['transaction_id'] = $_REQUEST['xid'];
	} else {
		$pp_response['order_status']   = 'F';
		$pp_response['reason_text']    = fn_get_lang_var('transaction_declined');
	}
	
	fn_finish_payment($order_id, $pp_response);
	fn_order_placement_routines($order_id);
	exit;
	
} else {

	$post = array();
	
	$post['vendor_id'] = $processor_data['params']['vendor_id'];
	$post['mername']   = $processor_data['params']['merchant_name'];
	$post['cntrl']     = md5('CNTRL_VALUE' . $order_id . $post['mername'] . $order_info['total'] . $processor_data['params']['secret_key']);
	
	$images = array('visaimage', 'mcimage', 'ameximage', 'discimage', 'dinerimage');
	foreach ($images as $im) {
		$post[$im] = 1;
	}
	
	$current_location = Registry::get('config.current_location');
	$post['ret_addr']  = "$current_location/$index_script?dispatch=payment_notification.notify&payment=itransact_split_form&order_id=$order_id";
	
	// filling order cost
	$post['item_1_desc'] = fn_get_lang_var('order_id') . ': ' . $processor_data['params']['order_prefix'] . $order_id . ($order_info['repaid'] ? "_{$order_info['repaid']}" : '');
	$post['item_1_cost'] = $order_info['total'];
	$post['item_1_qty']  = 1;
			
	$post['first_name'] = $order_info['b_firstname'];
	$post['last_name']  = $order_info['b_lastname'];
	$post['address']    = $order_info['b_address'];
	if (!empty($order_info['b_address_2'])) {
		$post['address'] .= (' ' . $order_info['b_address_2']);
	}
	$post['city']    = $order_info['b_city'];
	$post['state']   = $order_info['b_state'];
	$post['zip']     = $order_info['b_zipcode'];
	$post['country'] = $order_info['b_country'];
	$post['phone']   = $order_info['phone'];
	$post['email']   = $order_info['email'];
	
	$post['sfname']  = $order_info['s_firstname'];
	$post['slname']  = $order_info['s_lastname'];
	$post['saddr']   = $order_info['s_address'];
	if (!empty($order_info['s_address_2'])) {
		$post['saddr'] .= (' ' . $order_info['s_address_2']);
	}
	$post['scity']   = $order_info['s_city'];
	$post['sstate']  = $order_info['s_state'];
	$post['szip']    = $order_info['s_zipcode'];
	$post['sctry']   = $order_info['s_country'];
	
	$post['showaddr']     = 1;
	$post['altaddr']      = 1;
	$post['showcvv']      = 1;
	$post['formtype']     = 1;
	$post['home_page']    = $current_location;
	$post['acceptcards']  = 1;
	$post['acceptchecks'] = 1;
	$post['accepteft']    = 1;
	$post['passback'] = 'cntrl';
	$post['lookup'] = 'xid';
	
	$lang_postfix = '';
	if (CART_LANGUAGE == 'ES') {
		$lang_postfix = '_esp';
	} elseif (CART_LANGUAGE == 'FR') {
		$lang_postfix = '_french';
	}
	$form_tag = '<form method="post" action="https://secure.paymentclearing.com/cgi-bin/mas/split' . $lang_postfix . '.cgi" name="process">';
	
	echo <<<EOT
<html>
<body onLoad="document.process.submit();">
{$form_tag}
EOT;

foreach ($post as $name => $value) {
	echo "<input type=\"hidden\" name=\"$name\" value=\"$value\" />\n";
}

	$msg = fn_get_lang_var('text_cc_processor_connection');
	$msg = str_replace('[processor]', 'iTransact', $msg);
	echo <<<EOT
</form>
<p><div align=center>{$msg}</div></p>
</body>
</html>
EOT;

	exit;
}

?>