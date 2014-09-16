<?php

//
// $Id: products.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if (!defined('AREA')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	//
	// Update required products
	//

	if ($mode == 'update') {

		if (!empty($_REQUEST['product_id'])) {
			db_query('DELETE FROM ?:product_required_products WHERE product_id = ?i', $_REQUEST['product_id']);

			if (!empty($_REQUEST['required_products'])) {
				$required_products = explode(',', $_REQUEST['required_products']);

				$entry = array (
					'product_id' => $_REQUEST['product_id']
				);

				foreach ($required_products as $entry['required_id']) {
					if (empty($entry['required_id'])) {
						continue;
					}

					db_query('INSERT INTO ?:product_required_products ?e', $entry);
				}
			}
		}
	}
}

if ($mode == 'update') {
	$product_id = empty($_REQUEST['product_id']) ? 0 : intval($_REQUEST['product_id']);

	Registry::set('navigation.tabs.required_products', array (
		'title' => fn_get_lang_var('required_products'),
		'js' => true
	));

	$required_products = db_get_fields('SELECT required_id FROM ?:product_required_products WHERE product_id = ?i', $product_id);

	$view->assign('required_products', $required_products);
}
?>