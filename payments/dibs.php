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
// $Id: dibs.php 9907 2010-06-30 06:44:18Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {

	$pp_response = array();

	$order_id = intval($_REQUEST['order_id']);

	if ($mode == 'accept') {

		$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id=?i", $order_id);
		$processor_data = fn_get_processor_data($payment_id);

		$amount = db_get_field("SELECT total FROM ?:orders WHERE order_id=?i", $order_id);
		$amount = str_replace('.', '', $amount);
		$_REQUEST['fee'] = (!empty($_REQUEST['fee']))? $_REQUEST['fee'] : 0;
		$amount_with_fee = $amount + $_REQUEST['fee'];
	
	 	if (!empty($_REQUEST['transact'])) {
			$key = md5($processor_data['params']['key2'] . md5($processor_data['params']['key1'] . 'transact=' . $_REQUEST['transact'] . '&amount=' . $amount . '&currency=' . $processor_data['params']['currency']));
			$key_with_fee = md5($processor_data['params']['key2'] . md5($processor_data['params']['key1'] . 'transact=' . $_REQUEST['transact'] . '&amount=' . $amount_with_fee . '&currency=' . $processor_data['params']['currency']));
		}

		if (!empty($_REQUEST['transact']) && ($_REQUEST['authkey'] == $key || $_REQUEST['authkey'] == $key_with_fee)) {
			$pp_response['order_status']   = 'P';
			$pp_response['reason_text']    = fn_get_lang_var('transaction_approved');
			$pp_response['transaction_id'] = $_REQUEST['transact'];
		} else {
			$pp_response['order_status']   = 'F';
			$pp_response['reason_text']    = fn_get_lang_var('transaction_declined');
		}
	} else {
		$pp_response['order_status'] = 'F';
		$pp_response['reason_text']  = fn_get_lang_var('transaction_declined');
	}

	if (fn_check_payment_script('dibs.php', $order_id)) {
		fn_finish_payment($order_id, $pp_response);
		fn_order_placement_routines($order_id);
	}
} else {

	$currencies = array(
		208 => 'DKK',
		978 => 'EUR',
		840 => 'USD',
		826 => 'GBP',
		752 => 'SEK',
		036 => 'AUD',
		124 => 'CAD',
		352 => 'ISK',
		392 => 'JPY',
		554 => 'NZD',
		578 => 'NOK',
		756 => 'CHF',
		949 => 'TRY'
	);
	$languages = array (
		"da",
		"sv",
		"no",
		"en",
		"nl",
		"de",
		"fr",
		"fi",
		"es",
		"it",
		"fo",
		"pl",
	);

	$post_address = "https://payment.architrade.com/paymentweb/start.action";
	$msg = fn_get_lang_var('text_cc_processor_connection');
	$msg = str_replace('[processor]', 'DIBS', $msg);

	$lang_code = Registry::get('settings.Appearance.admin_default_language');

	$post = array();

	$post['order_id'] = $processor_data['params']['order_prefix'].(($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id);
	$post['currency'] = $processor_data['params']['currency'];
	$post['amount'] = $order_info['total'] * 100;


	$post['accepturl'] = Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.accept&payment=dibs&order_id=$order_id";
	$post['cancelurl'] = Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.cancel&payment=dibs&order_id=$order_id";

	$post['lang'] = (in_array(strtolower(CART_LANGUAGE), $languages))? strtolower(CART_LANGUAGE) : $processor_data['params']['lang'];

	$post['calcfee'] = 'no';
	$post['skiplastpage'] = $processor_data['params']['skiplastpage'];

	$post['md5key'] = md5($processor_data['params']['key2'] . md5($processor_data['params']['key1'] . 'merchant=' . $processor_data['params']['merchant'] . '&orderid=' . $post['order_id'] . '&currency=' . $post['currency'] . '&amount=' . $post['amount']));

	echo <<<EOT
	<html>
	<body onLoad="document.process.submit();">
		<form action="{$post_address}" method="POST" name="process">
		<input type="hidden" name="merchant" value="{$processor_data['params']['merchant']}" />
		<input type="hidden" name="orderid" value="{$post['order_id']}" />
		<input type="hidden" name="currency" value="{$post['currency']}" />
		<input type="hidden" name="amount" value="{$post['amount']}" />
		<input type="hidden" name="accepturl" value="{$post['accepturl']}" />
		<input type="hidden" name="cancelurl" value="{$post['cancelurl']}" />
		<input type="hidden" name="uniqueoid" value="yes" />
		<input type="hidden" name="ip" value="{$order_info['ip_address']}" />
		<input type="hidden" name="paytype" value="ACCEPT,ACK,AMEX,AMEX(DK),BHBC,CCK,CKN,COBK,DIN,DIN(DK),DK,ELEC,VISA,EWORLD,FCC,FCK,FFK,FSC,FSBK,FSSBK,GSC,GRA,HBSBK,HMK,ICASBK,IBC,IKEA,JPSBK,JCB,LIC(DK),LIC(SE),MC,MC(DK),MC(SE),MTRO,MTRO(DK),MTRO(UK),MTRO(SOLO),MEDM,MERLIN(DK),MOCA,NSBK,OESBK,PGSBK,Q8SK,Q8LIC,RK,SLV,SBSBK,S/T,SBC,SBK,SEBSBK,TKTD,TUBC,TLK,VSC,V-DK,VEKO,VISA,VISA(DK),VISA(SE),ELEC,WOCO,AAK" />
		<input type="hidden" name="calcfee" value="{$post['calcfee']}" />
		<input type="hidden" name="skiplastpage" value="{$post['skiplastpage']}" />
		<input type="hidden" name="lang" value="{$post['lang']}" />
		<input type="hidden" name="color" value="{$processor_data['params']['color']}" />
		<input type="hidden" name="decorator" value="{$processor_data['params']['decorator']}" />

EOT;
	if ($processor_data['params']['test'] == 'test') {
	echo <<<EOT
		<input type="hidden" name="test" value="yes" />
EOT;
	}

	$all_fields = fn_get_profile_fields('O');
	$i = 1;
	foreach ($all_fields as $k => $fields) {
		if ($k == 'C') {
			$name = fn_get_lang_var('contact_information', $lang_code);
		} elseif ($k == 'B') {
			$name = fn_get_lang_var('billing_address', $lang_code);
		} elseif ($k == 'S') {
			$name = fn_get_lang_var('shipping_address', $lang_code);
		}
		echo '<input type="hidden" name="delivery'. $i .'.' .htmlspecialchars($name). "\" value=\" \" />\n";
		$i++;
		foreach ($fields as $kf => $field) {
			echo '<input type="hidden" name="delivery' . $i . '.' . htmlspecialchars($field['description']).'" value="' . $order_info[$field['field_name']] . "\" />\n";
			$i++;
		}
	}

	$post['ordline0-1'] = fn_get_lang_var('product_id', $lang_code);
	$post['ordline0-2'] = fn_get_lang_var('product_code', $lang_code);
	$post['ordline0-3'] = fn_get_lang_var('product_name', $lang_code);
	$post['ordline0-4'] = fn_get_lang_var('amount', $lang_code);
	$post['ordline0-5'] = fn_get_lang_var('price', $lang_code);

	echo <<<EOT
		<input type="hidden" name="ordline0-1" value="{$post['ordline0-1']}" />
		<input type="hidden" name="ordline0-2" value="{$post['ordline0-2']}" />
		<input type="hidden" name="ordline0-3" value="{$post['ordline0-3']}" />
		<input type="hidden" name="ordline0-4" value="{$post['ordline0-4']}" />
		<input type="hidden" name="ordline0-5" value="{$post['ordline0-5']}" />

EOT;
	$i = 1;
	foreach ($order_info['items'] as $k => $item) {
	echo <<<EOT
		<input type="hidden" name="ordline{$i}-1" value="{$item['product_id']}" />
		<input type="hidden" name="ordline{$i}-2" value="{$item['product_code']}" />
		<input type="hidden" name="ordline{$i}-3" value="{$item['product']}" />
		<input type="hidden" name="ordline{$i}-4" value="{$item['amount']}" />
		<input type="hidden" name="ordline{$i}-5" value="{$item['price']}" />

EOT;
	$i++;
	}
	
	if (!empty($order_info['taxes']) && Registry::get('settings.General.tax_calculation') == 'subtotal') {
		foreach ($order_info['taxes'] as $tax_id => $tax) {
		if ($tax['price_includes_tax'] == 'N') {
			continue;
		}
		echo <<<EOT
			<input type="hidden" name="ordline{$i}-1" value="{$tax_id}" />
			<input type="hidden" name="ordline{$i}-2" value="{$tax['regnumber']}" />
			<input type="hidden" name="ordline{$i}-3" value="{$tax['description']}" />
			<input type="hidden" name="ordline{$i}-4" value="1" />
			<input type="hidden" name="ordline{$i}-5" value="{$tax['tax_subtotal']}" />

EOT;
		$i++;
		}
	}

	echo <<<EOT
		<input type="hidden" name="md5key" value="{$post['md5key']}" />
	</form>
	<p>
	<div align=center>{$msg}</div>
	</p>
</body>
</html>
EOT;
}

exit;

?>