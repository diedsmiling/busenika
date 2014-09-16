<?php

//
// $Id: products.post.php 8049 2009-10-02 10:24:55Z lexa $
//

if (!defined('AREA')) { die('Access denied'); }

if ($mode == 'view') {
	// Assign attachments files for products
	$product_id = !empty($_REQUEST['product_id']) ? intval($_REQUEST['product_id']) : 0;
	$attachments = fn_get_attachments('product', $product_id);

	if (!empty($attachments)) {
		$view->assign('attachments_data', $attachments);
	}
}
?>