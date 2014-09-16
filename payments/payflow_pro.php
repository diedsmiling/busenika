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
// $Id: payflow_pro.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$payflow_username = $processor_data['params']['username'];
$payflow_vendor = $processor_data['params']['vendor'];
$payflow_partner = $processor_data['params']['partner'];
$payflow_password = $processor_data['params']['password'];

if ($processor_data['params']['mode'] == 'test') {
	$payflow_url = "pilot-payflowpro.paypal.com";
} else {
	$payflow_url = "payflowpro.paypal.com";
}

$items = '';
if (!empty($order_info['items'])) {
	foreach ($order_info['items'] as $k => $v) {
		$unit_price = fn_format_price($v['subtotal'] / $v['amount']);
		$items .= "<SKU>$v[product_code]</SKU>
					<Description>" . htmlentities($v['product']) . "</Description>
					<Quantity>$v[amount]</Quantity>
					<UnitPrice>$unit_price</UnitPrice>";
	}
}

if (!empty($order_info['taxes']) && Registry::get('settings.General.tax_calculation') == 'subtotal') {
	foreach ($order_info['taxes'] as $tax_id => $tax) {
		if ($tax['price_includes_tax'] == 'Y') {
			continue;
		}
		$unit_price = fn_format_price($tax['tax_subtotal']);
		$items .= "<SKU>" . htmlentities($tax['description']) . "</SKU>
			<Description>" . htmlentities($tax['description']) . "</Description>
			<Quantity>1</Quantity>
			<UnitPrice>$unit_price</UnitPrice>";
	}
}

$payflow_expire = '20' . $order_info['payment_info']['expiry_year'] . $order_info['payment_info']['expiry_month'];
$payflow_order_id = $processor_data['params']['order_prefix'].(($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id);
if (!empty($order_info['shipping']) && is_array($order_info['shipping'])) {
	$shipping_name = reset($order_info['shipping']);
} else {
	$shipping_name = array();
	$shipping_name['shipping'] = fn_get_lang_var('no_shipping_required');
}



$subtotal_discount = (floatval($order_info['subtotal_discount'])) ? fn_format_price($order_info['subtotal_discount']) : 0;

$post = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<XMLPayRequest Timeout='45' version="2.0">
	<RequestData>
		<Partner>$payflow_partner</Partner>
		<Vendor>$payflow_vendor</Vendor>
		<Transactions>
			<Transaction>
				<Sale>
					<PayData>
						<Invoice>
							<BillTo>
								<Name>$shipping_name[shipping]</Name>
								<Address>	
									<Street>$order_info[b_address]</Street>
									<City>$order_info[b_city]</City>
									<State>$order_info[b_state]</State>
									<Zip>$order_info[b_zipcode]</Zip>
									<Country>$order_info[b_country]</Country>
								</Address>
								<EMail>$order_info[email]</EMail>
								<Phone>$order_info[phone]</Phone>
								<Fax>$order_info[fax]</Fax>
							</BillTo>
							<ShipTo>
								<Address>
									<Street>$order_info[s_address]</Street>
									<City>$order_info[s_city]</City>
									<State>$order_info[s_state]</State>
									<Zip>$order_info[s_zipcode]</Zip>
									<Country>$order_info[s_country]</Country>
								</Address>
							</ShipTo>
							<DiscountAmt>$subtotal_discount</DiscountAmt>
							<TotalAmt>$order_info[total]</TotalAmt>
							<Comment>$payflow_order_id</Comment>
							<Items>$items</Items>
						</Invoice>
						<Tender>
							<Card>
								<CardNum>{$order_info['payment_info']['card_number']}</CardNum>
								<ExpDate>$payflow_expire</ExpDate>
								<NameOnCard>{$order_info['payment_info']['cardholder_name']}</NameOnCard>
								<CVNum>{$order_info['payment_info']['cvv2']}</CVNum>
							</Card>
						</Tender>
					</PayData>
				</Sale>
			</Transaction>
		</Transactions>
	</RequestData>
	<RequestAuth>
		<UserPass>
			<User>$payflow_username</User>
			<Password>$payflow_password</Password>
		</UserPass>
	</RequestAuth>
</XMLPayRequest>
XML;

$headers = array(
	"X-VPS-REQUEST-ID: $payflow_order_id",
	"X-VPS-VIT-CLIENT-CERTIFICATION-ID: 5b329b34269933161c60aeda0f14d0d8",
	"X-VPS-CLIENT-TIMEOUT: 45"
);

$post_url = "https://".$payflow_url.":443/transaction";

Registry::set('log_cut_data', array('CardNum', 'ExpDate', 'NameOnCard', 'CVNum'));
list ($_headers, $response_data) = fn_https_request("POST", $post_url, $post, "", "", "text/xml", "", "", "", $headers);

$pp_response = array();
$pp_response['reason_text'] = '';

preg_match("/<Result>(.*)<\/Result>/", $response_data, $_result);
if(!empty($_result[1])) {
	$pp_response['reason_text'] = "Result: ".$_result[1];
}

preg_match_all("/<Message>(.*?)<\/Message>/", $response_data, $_message);
if(!empty($_message[1])) {
	$pp_response['reason_text'] .= ("; " . end($_message[1]) . "; ");
}

preg_match("/<AuthCode>(.*)<\/AuthCode>/", $response_data, $_auth);
if(!empty($_auth[1])) {
	$pp_response['reason_text'] .= ("Auth Code: ".$_auth[1] . "; ");
}

preg_match('/<TransactionResult (?:.*) Duplicate="(.*)"/i', $response_data, $_duplicate);
if (!empty($_duplicate[1])) {
	$pp_response['reason_text'] .= ("Duplicate: " . $_duplicate[1] . "; ");
}

preg_match("/<PNRef>(.*)<\/PNRef>/", $response_data, $_transaction_id);
if(!empty($_transaction_id[1])) {
	$pp_response['transaction_id'] = $_transaction_id[1];
}

preg_match("/<IAVSResult>(.*)<\/IAVSResult>/", $response_data, $_avs);
if(!empty($_avs[1])) {
	$pp_response['descr_avs'] = $_avs[1];
}

preg_match("/<StreetMatch>(.*)<\/StreetMatch>/", $response_data, $_avs_street);
if(!empty($_avs_street[1])) {
	$pp_response['descr_avs'] =  "; Street Match: ". $_avs_street[1];
}

preg_match("/<PNZipMatchRef>(.*)<\/ZipMatch>/", $response_data, $_avs_zip);
if(!empty($_avs_zip[1])) {
	$pp_response['descr_avs'] =  "; Zip Match: ". $_avs_zip[1];
}

if ($_result[1] === '0') {
	$pp_response['order_status'] = 'P';
} else {
	$pp_response['order_status'] = 'F';
}
?>