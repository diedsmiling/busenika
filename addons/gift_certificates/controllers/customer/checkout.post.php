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
// $Id: checkout.post.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$gift_cert_code = empty($_REQUEST['gift_cert_code']) ? '' : strtoupper(trim($_REQUEST['gift_cert_code']));

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

	if ($mode == 'apply_certificate') {
		if (!empty($gift_cert_code)) {
			if (true == fn_check_gift_certificate_code($gift_cert_code, true)) {
				if (!isset($_SESSION['cart']['use_gift_certificates'][$gift_cert_code])) {
					$_SESSION['cart']['use_gift_certificates'][$gift_cert_code] = 'Y';
					fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('text_gift_cert_applied'));
				} else {
					fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('certificate_already_used'));
				}
			} else {
				$status = db_get_field("SELECT status FROM ?:gift_certificates WHERE gift_cert_code = ?s", $gift_cert_code);
				if (!empty($status) && !strstr('A', $status)) {
					fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('certificate_code_not_available'));
				} else {
					fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('certificate_code_not_valid'));			
				}
			}
		}

		return array(CONTROLLER_STATUS_REDIRECT, "checkout." . (!empty($_REQUEST['redirect_mode']) ? $_REQUEST['redirect_mode'] : 'checkout') . '.show_payment_options');
	}

	return;
}

if ($mode == 'delete_use_certificate' && !empty($gift_cert_code)) {
	fn_delete_gift_certificate_in_use($gift_cert_code, $_SESSION['cart']);
	
	return array(CONTROLLER_STATUS_REDIRECT, "checkout." . (!empty($_REQUEST['redirect_mode']) ? $_REQUEST['redirect_mode'] : 'checkout') . '.show_payment_options');
}

?>