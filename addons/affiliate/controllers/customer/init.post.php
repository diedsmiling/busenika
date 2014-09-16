<?php

//
// $Id: init.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (Registry::get('addons.affiliate.show_affiliate_code') == 'Y' && !empty($_SESSION['partner_data']) && !empty($_SESSION['partner_data']['partner_id'])) {
	$view->assign('partner_code', fn_dec2any($_SESSION['partner_data']['partner_id']));
}

if (empty($auth['is_affiliate']) && in_array(CONTROLLER, Registry::get('affiliate_controllers'))) {
	return array(CONTROLLER_STATUS_REDIRECT, $index_script);
}

?>