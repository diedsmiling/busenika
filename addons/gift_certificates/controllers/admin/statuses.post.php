<?php

//
// $Id: statuses.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	return;
}

if ($mode == 'manage') {

	if ($_REQUEST['type'] == STATUSES_GIFT_CERTIFICATE) {

		$view->assign('title', fn_get_lang_var('gift_certificate_statuses'));

		fn_gift_certificates_generate_sections('statuses');


		// I think this should be removed, not good, must be done on xml menu level
		Registry::set('navigation.selected_tab', 'orders');
		Registry::set('navigation.subsection', 'gift_certificates');
	}
}

?>
