<?php

//
// $Id: products.post.php 7174 2009-04-02 07:05:46Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

// Products search
if ($mode == 'results') {

	$product_count = $view->get_var('product_count');

	if (!empty($product_count)) {
		// Get all search params
		$search_params = $_REQUEST;
		unset($search_params['dispatch']);
		unset($search_params['page']);
		unset($search_params['result_ids']);
		unset($search_params['x']);
		unset($search_params['y']);
		$search_params['match'] = empty($search_params['match']) ? 'any' : $search_params['match']; // any, all, exact

		foreach ($search_params as $k => $v) {
			if (empty($v)) {
				unset($search_params[$k]);
				continue;
			}
			$search_params[$k] = $v;
		}

		ksort($search_params);
		$search_params = serialize($search_params);
		$md5_search_params = md5($search_params);

		// Save search params
		$sess_id = db_get_field("SELECT sess_id FROM ?:stat_sessions WHERE session = ?s AND expiry > ?i ORDER BY timestamp DESC LIMIT 1", Session::get_id(), TIME);
		if (!empty($sess_id)) {
			$record_exist = db_get_field("SELECT sess_id FROM ?:stat_product_search WHERE sess_id = ?i AND md5 = ?s", $sess_id, $md5_search_params);
			if (!$record_exist) {
				$_data = array(
					'sess_id' => $sess_id, 
					'search_string' => $search_params, 
					'md5' => $md5_search_params, 
					'quantity' => $product_count
				);
				db_query('INSERT INTO ?:stat_product_search ?e', $_data);
			}
		}
	}
}

?>
