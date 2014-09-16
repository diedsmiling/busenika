<?php

//
// $Id: products.post.php 7538 2009-05-27 15:55:45Z lexa $
//

if (!defined('AREA')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	return;
}

if ($mode == 'update') {
	// Assign attachments files for products
	$attachments = fn_get_attachments('product', $_REQUEST['product_id'], 'M', CART_LANGUAGE, empty($_REQUEST['rev']['product']) ? null : $_REQUEST['rev']['product'], empty($_REQUEST['rev_id']['product']) ? null : $_REQUEST['rev_id']['product']);

	Registry::set('navigation.tabs.attachments', array (
		'title' => fn_get_lang_var('attachments'),
		'js' => true
	));

	$view->assign('attachments', $attachments);
}

?>
