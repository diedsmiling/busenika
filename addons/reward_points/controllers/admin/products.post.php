<?php
s
//
// $Id: products.post.php 8329 2009-11-27 10:00:40Z ivan $
//

if ( !defined('AREA') ) { die('Access denied'); }


/* POST data processing */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//
	// Processing multiple updating of product elements
	//	
	if ($mode == 'do_m_update') {
		
		if (isset($_REQUEST['reward_points'])) {
			foreach ((array)$_REQUEST['reward_points'] as $product_id => $v) {
				foreach ((array)$v as $usergroup_id => $amount) {
					$data = array(
						'amount' => $amount, 
						'usergroup_id' => $usergroup_id
					);
					fn_add_reward_points($data, $product_id, PRODUCT_REWARD_POINTS);
				}
			}
		}
		
	//
	// Override multiple products with the one value
	//
	} elseif ($mode == 'do_m_override') {
		if (!empty($_REQUEST['product_ids'])) {
			foreach ($_REQUEST['product_ids'] as $product_id => $value) {
				if (isset($_REQUEST['override_products_points']['point_price'])) {
					fn_add_price_in_points(array('point_price' => $_REQUEST['override_products_points']['point_price']), $product_id);
				}
				if (isset($_REQUEST['override_reward_points'])) {
					foreach ((array)$_REQUEST['override_reward_points'] as $usergroup_id => $amount) {
						$data = array(
							'amount' => $amount, 
							'usergroup_id' => $usergroup_id
						);
						fn_add_reward_points($data, $product_id, PRODUCT_REWARD_POINTS);
					}
				}
			}
		}
	}

	return;
}


//
// 'Management' page
//
if ($mode == 'manage') {

	$selected_fields = $view->get_var('selected_fields');

	$selected_fields[] = array('name' => '[data][is_pbp]', 'text' => fn_get_lang_var('pay_by_points'));
	if (Registry::get('addons.reward_points.auto_price_in_points') == 'Y') {
		$selected_fields[] = array('name' => '[data][is_oper]', 'text' => fn_get_lang_var('override_per'));
	}
	$selected_fields[] = array('name' => '[product_point_prices][point_price]', 'text' => fn_get_lang_var('price_in_points'));
	$selected_fields[] = array('name' => '[data][is_op]', 'text' => fn_get_lang_var('override_gc_points_brief'));
	//$selected_fields[] = array('name' => '[reward_points][amount]',	'text' => fn_get_lang_var('reward_points'));	

	$view->assign('selected_fields', $selected_fields);


} elseif ($mode == 'm_update') {

	$selected_fields = $_SESSION['selected_fields'];

	$field_groups = $view->get_var('field_groups');
	$filled_groups = $view->get_var('filled_groups');
	$field_names = $view->get_var('field_names');

	if (!empty($selected_fields['data']['is_pbp'])) {
		$field_groups['C']['is_pbp'] = 'products_data';
		$filled_groups['C']['is_pbp'] = fn_get_lang_var('pay_by_points');
	}
	
	if (!empty($selected_fields['data']['is_oper'])) {
		$field_groups['C']['is_oper'] = 'products_data';
		$filled_groups['C']['is_oper'] = fn_get_lang_var('override_per');
	}
	
	if (!empty($selected_fields['product_point_prices']['point_price'])) {
		$field_groups['B']['point_price'] = 'products_data';
		$filled_groups['B']['point_price'] = fn_get_lang_var('price_in_points');
	}
	
	if (!empty($selected_fields['data']['is_op'])) {
		$field_groups['C']['is_op'] = 'products_data';
		$filled_groups['C']['is_op'] = fn_get_lang_var('override_gc_points_brief');
	}	

	/*if (!empty($selected_fields['reward_points'])) {
		$usergroups = fn_get_usergroups('C', DESCR_SL);

		foreach ($usergroups as $usergroup_id => $v) {
			$field_names['reward_points'][$usergroup_id] = $v['usergroup'];
		}

		$products_data = $view->get_var('products_data');

		foreach($products_data as $key => $value){
			$products_data[$key]['reward_points'] = fn_get_reward_points($key, 'P');
		}

		$view->assign('products_data', $products_data);

	}*/

	if (isset($field_names['is_pbp'])) {
		unset($field_names['is_pbp']);
	}
	
	if (isset($field_names['is_oper'])) {
		unset($field_names['is_oper']);
	}	
	
	if (isset($field_names['is_op'])) {
		unset($field_names['is_op']);
	}

	$view->assign('field_groups', $field_groups);
	$view->assign('filled_groups', $filled_groups);
	$view->assign('field_names', $field_names);	
	
} elseif ($mode == 'update') {

	Registry::set('navigation.tabs.reward_points', array (
		'title' => fn_get_lang_var('reward_points'),
		'js' => true
	));

	$view->assign('reward_points', fn_get_reward_points($_REQUEST['product_id'], PRODUCT_REWARD_POINTS));
	$view->assign('object_type', PRODUCT_REWARD_POINTS);	



} elseif ($mode == 'add') {

	// Add new tab to page sections
	// [Page sections]
	Registry::set('navigation.tabs.reward_points', array (
		'title' => fn_get_lang_var('reward_points'),
		'js' => true
	));
	// [/Page sections]
		
	$view->assign('object_type', PRODUCT_REWARD_POINTS);	
	
}


$view->assign('reward_usergroups', fn_array_merge(fn_get_default_usergroups(), fn_get_usergroups('C')));

?>
