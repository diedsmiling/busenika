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
// $Id: atos.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {

$processor_error['cvv_flag'] = array(
	"0" => "The cryptogram wasn’t recovered by the merchant.",
	"1" => "The cryptogram is present.",
	"2" => "The cryptogram is present on the porter’s card but is unreadable.",
	"9" => "The porter has informed the merchant that the cryptogram wasn’t printed on his card.",
);

$processor_error['cvv_response_code'] = array(
	"4E" => "Cryptogram incorrect",
	"4D" => "Cryptogram correct",
	"50" => "Cryptogram untreated",
	"53" => "The cryptogram is absent from the authorization request",
	"55" => "The bank of the internet user isn't certified, the control couldn’t be carried out",
	"??" => "The internet user’s bank or the merchant’s bank didn’t respond for the cryptogram.",
	"NO" => "No cryptogram on the card, e.g., AMEX.",
);

$processor_error['bank_response_code'] = array(
	"00" => "Transaction approved or handled successfully",
	"02" => "Contact card issuer",
	"03" => "Invalid acceptor",
	"04" => "Retain the card",
	"05" => "Don’t honor",
	"07" => "Retain the card, special conditions",
	"08" => "Approve after identification",
	"12" => "Invalid transaction",
	"13" => "Invalid sum",
	"14" => "Carrier number invalid",
	"15" => "Unknown card issuer",
	"30" => "Format error",
	"31" => "Acquirer organization identifier unknown",
	"33" => "Card validity date passed",
	"34" => "Fraud suspected",
	"41" => "Card lost",
	"43" => "Card stolen",
	"51" => "Insufficient credit or beyond credit limit",
	"54" => "Card validity date passed",
	"56" => "Card absent from file",
	"57" => "Transaction not permitted with carrier",
	"58" => "Transaction not allowed at terminal",
	"59" => "Fraud suspected",
	"60" => "Card acceptor must contact acquirer",
	"61" => "Over the withdrawal limit",
	"63" => "Security rules not respected",
	"68" => "No response or received too late",
	"90" => "System halted momentarily",
	"91" => "Card issuer inaccessible",
	"96" => "System malfunction",
	"97" => "Time out error",
	"98" => "Server unavailable, new network connection requested",
	"99" => "Incident in originator domain",
);

$processor_error['response_code'] = array(
	"00" => "Authorization accepted",
	"02" => "Authorization requested by telephone to bank due to an exceeded floor limit on the card ",
	"03" => "Remote sales contract non existent, contact your bank.",
	"05" => "Authorization refused",
	"12" => "Transaction invalid, verify the parameters send in the requ",
	"13" => "Invalid sum, verify the amount send in the request",
	"17" => "Cancelled by internet user",
	"30" => "Format error",
	"63" => "Security rules not respected, transaction stopped",
	"75" => "Exceeded number of attempts to enter card number",
	"90" => "Service temporarily unavailable",
);

	if ($mode == 'result') {

		$payment_id = db_get_field("SELECT ?:payments.payment_id FROM ?:payments LEFT JOIN ?:payment_processors ON ?:payment_processors.processor_id = ?:payments.processor_id WHERE ?:payment_processors.processor_script = 'atos.php' AND ?:payments.status = 'A'");
		$processor_data = fn_get_payment_method_data($payment_id);

		// R�up�ation de la variable crypt� DATA
		$message = "message=$_REQUEST[DATA]";

		// Initialisation du chemin du fichier pathfile (�modifier)
		//   ex :
		//    -> Windows : $pathfile="pathfile=c:\\repertoire\\pathfile";
		//    -> Unix    : $pathfile="pathfile=/home/repertoire/pathfile";

		$pathfile = "pathfile=".$processor_data['params']['atos_files']. '/pathfile';

		// Initialisation du chemin de l'executable response (�modifier)
		// ex :
		// -> Windows : $path_bin = "c:\\repertoire\\bin\\response";
		// -> Unix    : $path_bin = "/home/repertoire/bin/response";
		//

		$path_bin = $processor_data['params']['atos_files']. '/response';

		// Appel du binaire response

		$result = exec("$path_bin $pathfile $message");

		//	Sortie de la fonction : !code!error!v1!v2!v3!...!v29
		//		- code=0	: la fonction retourne les donn�s de la transaction dans les variables v1, v2, ...
		//				: Ces variables sont d�rites dans le GUIDE DU PROGRAMMEUR
		//		- code=-1 	: La fonction retourne un message d'erreur dans la variable error


		//	on separe les differents champs et on les met dans une variable tableau;
		$tableau = explode ("!", $result);

		//	R�up�ation des donn�s de la r�onse

		$code = $tableau[1];
		$error = $tableau[2];
		$merchant_id = $tableau[3];
		$merchant_country = $tableau[4];
		$amount = $tableau[5];
		$transaction_id = $tableau[6];
		$payment_means = $tableau[7];
		$transmission_date= $tableau[8];
		$payment_time = $tableau[9];
		$payment_date = $tableau[10];
		$response_code = $tableau[11];
		$payment_certificate = $tableau[12];
		$authorisation_id = $tableau[13];
		$currency_code = $tableau[14];
		$card_number = $tableau[15];
		$cvv_flag = $tableau[16];
		$cvv_response_code = $tableau[17];
		$bank_response_code = $tableau[18];
		$complementary_code = $tableau[19];
		$complementary_info = $tableau[20];
		$return_context = $tableau[21];
		$caddie = $tableau[22];
		$receipt_complement = $tableau[23];
		$merchant_language = $tableau[24];
		$language = $tableau[25];
		$customer_id = $tableau[26];
		$order_id = (strpos($tableau[27], '_')) ? substr($tableau[27], 0, strpos($tableau[27], '_')) : $tableau[27];
		$customer_email = $tableau[28];
		$customer_ip_address = $tableau[29];
		$capture_day = $tableau[30];
		$capture_mode = $tableau[31];
		$data = $tableau[32];

		//  analyse du code retour


		$pp_response = array();
		if (($code == "") && ($error == "")) {
			print ("<BR><CENTER>erreur appel response</CENTER><BR>");
			print ("executable response non trouve $path_bin");
		} else if ($code != 0 || $response_code != 0 || $bank_response_code != 0) {
			print ("<center><b><h2>Erreur appel API de paiement.</h2></center></b>");
			print ("<br><br><br>");
			print (" message erreur : $error <br>");
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = $error.' ';
		} elseif ($error == 0 && isset($amount)) {
			$pp_response['order_status'] = 'P';
			$pp_response['transaction_id'] = $transaction_id;
			$pp_response['reason_text'] = $error;
		}
		if (!empty($cvv_response_code)) {
			if (!empty($processor_error['cvv_response_code'][$cvv_response_code])) {
				$pp_response['descr_cvv'] = $processor_error['cvv_response_code'][$cvv_response_code];
			} else {
				$pp_response['descr_cvv'] = 'Response code: '.$cvv_response_code;
			}
		}
		if (!empty($cvv_flag)) {
			if (!empty($processor_error['cvv_flag'][$cvv_flag])) {
				$pp_response['descr_cvv'] .= ' ('.$processor_error['cvv_flag'][$cvv_flag].')';
			} else {
				$pp_response['descr_cvv'] .= ' (CVV flag: '.$cvv_flag.')';
			}
		}
		if (!empty($bank_response_code)) {
			if (!empty($processor_error['bank_response_code'][$bank_response_code])) {
				$pp_response['reason_text'] .= 'Bank response: '.$processor_error['bank_response_code'][$bank_response_code].'. ';
			} else {
				$pp_response['reason_text'] .= 'Bank response code: '.$bank_response_code.'. ';
			}
		}
		if (!empty($response_code)) {
			if (!empty($processor_error['response_code'][$response_code])) {
				$pp_response['reason_text'] .= ' Mercanet response: '.$processor_error['response_code'][$response_code];
			} else {
				$pp_response['reason_text'] .= ' Mercanet response code: '.$response_code;
			}
		}
		fn_finish_payment($order_id, $pp_response);
		exit;

	} else {
		$payment_id = db_get_field("SELECT ?:payments.payment_id FROM ?:payments LEFT JOIN ?:payment_processors ON ?:payment_processors.processor_id = ?:payments.processor_id WHERE ?:payment_processors.processor_script = 'atos.php' AND ?:payments.status = 'A'");
		$processor_data = fn_get_payment_method_data($payment_id);

		$message = "message=$_REQUEST[DATA]";
		$pathfile = "pathfile=".$processor_data['params']['atos_files'] . '/pathfile';
		$path_bin = $processor_data['params']['atos_files'] . '/response';
		$result = exec("$path_bin $pathfile $message");
		$tableau = explode ("!", $result);

		$order_id = (strpos($tableau[27], '_')) ? substr($tableau[27], 0, strpos($tableau[27], '_')) : $tableau[27];

		fn_order_placement_routines($order_id);
	}


} else {

	$pp_merchant = $processor_data['params']['merchant_id'];
	$pp_total = $order_info['total'] * 100;
	$pp_country = $processor_data['params']['country'];
	$pp_currency = $processor_data['params']['currency'];
	$pp_lang = $processor_data['params']['language'];

	$msg = fn_get_lang_var('text_cc_processor_connection');
	$msg = str_replace('[processor]', 'Atos server', $msg);

echo <<<EOT
   <p><div align=center>{$msg}</div></p>
EOT;
	$parm = "merchant_id=$pp_merchant";
	$parm = "$parm merchant_country=$pp_country";
	$parm = "$parm amount=$pp_total";
	$parm = "$parm currency_code=$pp_currency";


	// Initialisation du chemin du fichier pathfile (�modifier)
	    //   ex :
	    //    -> Windows : $parm="$parm pathfile=c:\\repertoire\\pathfile";
	    //    -> Unix    : $parm="$parm pathfile=/home/repertoire/pathfile";

	$parm = "$parm pathfile=".$processor_data['params']['atos_files']. '/pathfile';

	//		Si aucun transaction_id n'est affect� request en g��e
	//		un automatiquement �partir de heure/minutes/secondes
	//		R��ez vous au Guide du Programmeur pour
	//		les r�erves �ises sur cette fonctionnalit�	//
	//		$parm="$parm transaction_id=123456";

	//		Affectation dynamique des autres param�res
	// 		Les valeurs propos�s ne sont que des exemples
	// 		Les champs et leur utilisation sont expliqu� dans le Dictionnaire des donn�s
	//
			//$parm="$parm normal_return_url=$return_url";
			//$parm="$parm cancel_return_url=$return_url";
			//$parm="$parm automatic_response_url=$return_url";
			$parm = "$parm language=$pp_lang";
	//		$parm="$parm payment_means=CB,2,VISA,2,MASTERCARD,2";
	//		$parm="$parm header_flag=no";
	//		$parm="$parm capture_day=";
	//		$parm="$parm capture_mode=";
	//		$parm="$parm bgcolor=";
	//		$parm="$parm block_align=";
	//		$parm="$parm block_order=";
	//		$parm="$parm textcolor=";
	//		$parm="$parm receipt_complement=";
	//		$parm="$parm caddie=mon_caddie";
	//		$parm="$parm customer_id=";
			$parm = "$parm customer_email=$order_info[email]";
	//		$parm="$parm customer_ip_address=";
	//		$parm="$parm data=";
	//		$parm="$parm return_context=";
	//		$parm="$parm target=";
			$parm = "$parm order_id=".(($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id);


	//		Les valeurs suivantes ne sont utilisables qu'en pr�production
	//		Elles n�essitent l'installation de vos fichiers sur le serveur de paiement
	//
	// 		$parm="$parm normal_return_logo=";
	// 		$parm="$parm cancel_return_logo=";
	// 		$parm="$parm submit_logo=";
	// 		$parm="$parm logo_id=";
	// 		$parm="$parm logo_id2=";
	// 		$parm="$parm advert=";
	// 		$parm="$parm background_id=";
	// 		$parm="$parm templatefile=";


	//		insertion de la commande en base de donn�s (optionnel)
	//		A d�elopper en fonction de votre syst�e d'information

	// Initialisation du chemin de l'executable request (�modifier)
	// ex :
	// -> Windows : $path_bin = "c:\\repertoire\\bin\\request";
	// -> Unix    : $path_bin = "/home/repertoire/bin/request";
	//

	$path_bin = $processor_data['params']['atos_files']. '/request';

	//	Appel du binaire request

	$result = exec("$path_bin $parm");

	//	sortie de la fonction : $result=!code!error!buffer!
	//	    - code=0	: la fonction g��e une page html contenue dans la variable buffer
	//	    - code=-1 	: La fonction retourne un message d'erreur dans la variable error

	//On separe les differents champs et on les met dans une variable tableau

	$tableau = explode ("!", "$result");

	//	r�up�ation des param�res

	@$code = $tableau[1];
	@$error = $tableau[2];
	@$message = $tableau[3];

	//  analyse du code retour

	if (( $code == "" ) && ( $error == "" ) ) {
		print ("<BR><CENTER>erreur appel request</CENTER><BR>");
		print ("executable request non trouve $path_bin");
	} else if ($code != 0){
		print ("<center><b><h2>Erreur appel API de paiement.</h2></center></b>");
		print ("<br><br><br>");
		print (" message erreur : $error <br>");
	} else {
		print ("<br><br>");
		print ("  $message <br>");
	}
exit;
}
?>
