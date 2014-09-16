<?php



//
// $Id: products.post.php 8049 2009-10-02 10:24:55Z lexa $
//

if (!defined('AREA')) { die('Access denied'); }

if ($mode == 'view') {
	$product_id = empty($_REQUEST['product_id']) ? 0 : $_REQUEST['product_id'];

	if ($product_id) {
		$ids = db_get_field('SELECT count(req_prod.required_id) FROM ?:product_required_products as req_prod LEFT JOIN ?:products ON req_prod.required_id = ?:products.product_id WHERE req_prod.product_id = ?i AND ?:products.status != ?s', $product_id, 'D');
	}
}

?>