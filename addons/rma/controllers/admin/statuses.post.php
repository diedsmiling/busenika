<?php
//
// $Id: statuses.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	return;
}

if ($mode == 'manage') {

	if ($_REQUEST['type'] == STATUSES_RETURN) {

		$view->assign('title', fn_get_lang_var('rma_request_statuses'));

		fn_rma_generate_sections('statuses');

		// I think this should be removed, not good, must be done on xml menu level
		Registry::set('navigation.selected_tab', 'orders');
		Registry::set('navigation.subsection', 'return_requests');
	}
}


?>
