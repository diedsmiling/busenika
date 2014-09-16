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
// $Id: spplus.php 10229 2010-07-27 14:21:39Z 2tl $
//

if (!defined('AREA') ) {
	if (!empty($_REQUEST['arg1'])) {
		define('AREA', 'C');
		define('AREA_NAME' ,'customer');
		require './../prepare.php';
		require './../init.php';

		$order_id = $_REQUEST['arg1'];
		$order_info = fn_get_order_info($order_id);
		$payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $order_id);
		$processor_data = fn_get_payment_method_data($payment_id);

		$pp_response = array();
		if (($_REQUEST['etat'] == '1' || $_REQUEST['etat'] == '99') && (html_entity_decode($_REQUEST['siret']) == $processor_data['params']['merchant_id']) && (fn_format_price($order_info['total']) == fn_format_price(html_entity_decode($_REQUEST['montant']))) && ($processor_data['params']['currency'] == html_entity_decode($_REQUEST['devise']))) {
			$pp_response['order_status'] = 'P';
			$pp_response['reason_text'] = fn_get_lang_var('approved');
			$pp_response['transaction_id'] = $_REQUEST['refsfp'];
			if ($_REQUEST['etat'] == '99') {
				$pp_response['reason_text'] .= '; ' . fn_get_lang_var('the_test_transaction');
			}
		} else {
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = fn_get_lang_var('declined');
		}

		if (fn_check_payment_script('spplus.php', $order_id)) {
			fn_finish_payment($order_id, $pp_response, false);
		}

		fn_redirect(Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.notify&payment=spplus&order_id=$order_id");
	}
	
	die('Access denied'); 
}

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'notify') {
		fn_order_placement_routines($_REQUEST['order_id']);
	}

} else {

	if (!extension_loaded('SPPLUS')) {
		die('SPPLUS extension (http://pecl.php.net/package/spplus) must be installed');
	}

	$clent = $processor_data['params']['clent'];
 	$codesiret = $processor_data['params']['merchant_id'];
   	$devise = $processor_data['params']['currency'];
   	$langue = $processor_data['params']['language'];

	$montant = $order_info['total'];
	$email = $order_info['email'];
   	$taxe = $order_info['tax_subtotal'];

	$reference = 'spp' . date('YmdHis');	
	$moyen = 'CBS';
   	$modalite = '1x';
	$arg1 = $order_id;

   	$calcul_hmac = calcul_hmac($clent, $codesiret, $reference, $langue, $devise, $montant, $taxe, $validite);

	$url_calcul_hmac = "https://www.spplus.net/paiement/init.do?siret=$codesiret&reference=$reference&langue=$langue&devise=$devise&montant=$montant&taxe=$taxe&hmac=$calcul_hmac&moyen=$moyen&modalite=$modalite";
   	$data = "siret=$codesiret&reference=$reference&langue=$langue&devise=$devise&montant=$montant&taxe=$taxe&moyen=$moyen&modalite=$modalite";
	$calculhmac = calculhmac($clent, $data);
   	$url_calculhmac = "https://www.spplus.net/paiement/init.do?siret=$codesiret&reference=$reference&langue=$langue&devise=$devise&montant=$montant&taxe=$taxe&moyen=$moyen&modalite=$modalite&hmac=$calculhmac";

   	$data = "$codesiret$reference$langue$devise$montant$taxe$moyen$modalite";
   	$nthmac = nthmac($clent,$data);
   	$url_nthmac = "https://www.spplus.net/paiement/init.do?siret=$codesiret&reference=$reference&langue=$langue&devise=$devise&montant=$montant&taxe=$taxe&moyen=$moyen&modalite=$modalite&hmac=$nthmac";

   	$url_signeurlpaiement = "https://www.spplus.net/paiement/init.do?siret=$codesiret&reference=$reference&langue=$langue&devise=$devise&montant=$montant&taxe=$taxe&moyen=$moyen&modalite=$modalite&arg1=$arg1";
   	$urlspplus = signeurlpaiement($clent, $url_signeurlpaiement);

	echo <<<EOT
<html>
<body onLoad="javascript: document.location='{$urlspplus}';">
</body>
</html>
EOT;
	exit;
}

?>