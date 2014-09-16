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
// $Id: affiliate_plans.php 10264 2010-07-29 08:09:07Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

	fn_trusted_vars('add_affiliate_plans', 'affiliate_plans_data', 'affiliate_plan', 'levels', 'commissions_ids');
	$suffix = '';

	//
	// Delete selected plans
	//
	if ($mode == 'delete') {
		if (!empty($_REQUEST['plan_ids'])) {
			fn_delete_affiliate_plans($_REQUEST['plan_ids']);
		}

		$suffix = '.manage';
	}

	//
	// Update/add plan
	//
	if ($mode == 'update') {
		$plan_id = fn_update_affiliate_plan($_REQUEST['affiliate_plan'], $_REQUEST['plan_id'], DESCR_SL);

		$suffix = ".update?plan_id=$plan_id";
	}

	//
	// Update commissions
	//
	if ($mode == 'update_commissions') {
		if (!empty($_REQUEST['affiliate_plan'])) {
			$affiliate_plan = $_REQUEST['affiliate_plan'];
			foreach ($affiliate_plan['commissions'] as $key => $commission) {
				$commission = floatval($commission);
				$affiliate_plan['commissions'][$key] = ($commission>100) ? 100 : (($commission < 0) ? 0 : $commission);
			}

			$plan_data = fn_get_affiliate_plan_data($_REQUEST['plan_id'], DESCR_SL);
			$plan_data['commissions'] = $affiliate_plan['commissions'];
			$plan_id = fn_update_affiliate_plan($plan_data, $_REQUEST['plan_id'], DESCR_SL);
		}

		$suffix = ".update?plan_id=$plan_id";
	}

	//
	// Add commissions
	//
	if ($mode == 'add_commissions') {
		if (!empty($_REQUEST['levels'])) {
			$plan_data = fn_get_affiliate_plan_data($_REQUEST['plan_id'], DESCR_SL);
			if (!empty($plan_data)) {
				foreach ($_REQUEST['levels'] as $level_data) {
					if (!empty($level_data['commission'])) {
						$level_data['commission'] = floatval($level_data['commission']);
						$level_data['commission'] = ($level_data['commission'] > 100) ? 100 : (($level_data['commission']<0)?0:$level_data['commission']);
						$plan_data['commissions'][] = $level_data['commission'];
					}
				}
				$plan_id = fn_update_affiliate_plan($plan_data, $_REQUEST['plan_id'], DESCR_SL);
			}
		}

		$suffix = ".update?plan_id=$plan_id";
	}

	//
	// Delete commissions
	//
	if ($mode == 'delete_commissions') {
		if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['commission_ids'])) {
			$plan_id = fn_delete_affiliate_commissions($_REQUEST['commission_ids'], $_REQUEST['plan_id']);
		}

		$suffix = ".update?plan_id=$plan_id";
	}

	//
	// Add products
	//
	if ($mode == 'add_products') {
		if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['add_products_ids'])) {

			$plan_data = fn_get_affiliate_plan_data($_REQUEST['plan_id'], DESCR_SL);
			$products_ids = $plan_data['product_ids'];

			if (empty($products_ids)) {
				$products_ids = array();
			}

			foreach ($_REQUEST['add_products_ids'] as $prod_id) {
				if (!isset($products_ids[$prod_id])) {
					$products_ids[$prod_id] = $plan_data['payout_types']['sale'];
				}
			}
			$plan_data['product_ids'] = $products_ids;
			$plan_id = fn_update_affiliate_plan($plan_data, $_REQUEST['plan_id'], DESCR_SL);
		}

		$suffix = ".update?plan_id=$plan_id";
	}

	//
	// Delete products
	//
	if ($mode == 'delete_products') {
		if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['product_ids'])) {
			$plan_id = fn_delete_affiliate_products($_REQUEST['product_ids'], $_REQUEST['plan_id']);
		}

		$suffix = ".update?plan_id=$plan_id";
	}

	//
	// Update products
	//
	if ($mode == 'update_products') {
		if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['sales'])) {
			$plan_data = fn_get_affiliate_plan_data($_REQUEST['plan_id'], DESCR_SL);
			$products_ids = $plan_data['product_ids'];
			foreach ($_REQUEST['sales'] as $prod_id => $sale) {
				if (isset($products_ids[$prod_id])) {
					$products_ids[$prod_id] = $sale;
				}
			}
			$plan_data['product_ids'] = $products_ids;
			$plan_id = fn_update_affiliate_plan($plan_data, $_REQUEST['plan_id'], DESCR_SL);
		}

		$suffix = ".update?plan_id=$plan_id";
	}

	//
	// Add categories
	//
	if ($mode == 'add_categories') {
		if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['categories_ids'])) {
			$plan_data = fn_get_affiliate_plan_data($_REQUEST['plan_id'], DESCR_SL);
			$categories_ids = empty($plan_data['category_ids']) ? array() : $plan_data['category_ids'];
			$add_categories_ids = array_keys($_REQUEST['categories_ids']);
			foreach ($add_categories_ids as $k => $cat_id) {
				if (!isset($categories_ids[$cat_id])) {
					$categories_ids[$cat_id] = $plan_data['payout_types']['sale'];
				}
			}
			$plan_data['category_ids'] = $categories_ids;
			$plan_id = fn_update_affiliate_plan($plan_data, $_REQUEST['plan_id'], DESCR_SL);
		}

		$suffix = ".update?plan_id=$plan_id";
	}

	//
	// Delete categories
	//
	if ($mode == 'delete_categories') {
		if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['category_ids'])) {
			$plan_id = fn_delete_affiliate_categories($_REQUEST['category_ids'], $_REQUEST['plan_id']);
		}

		$suffix = ".update?plan_id=$plan_id";
	}

	//
	// Update categories
	//
	if ($mode == 'update_categories') {
		if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['sales'])) {
			$plan_data = fn_get_affiliate_plan_data($_REQUEST['plan_id'], DESCR_SL);
			$categories_ids = $plan_data['category_ids'];
			foreach ($_REQUEST['sales'] as $cat_id=>$sale) {
				if (isset($categories_ids[$cat_id])) {
					$categories_ids[$cat_id] = $sale;
				}
			}
			$plan_data['category_ids'] = $categories_ids;
			$plan_id = fn_update_affiliate_plan($plan_data, $_REQUEST['plan_id'], DESCR_SL);
		}

		$suffix = ".update?plan_id=$plan_id";
	}

	//
	// Add coupons
	//
	if ($mode == 'add_coupons') {
		if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['promotion_ids'])) {
			$plan_data = fn_get_affiliate_plan_data($_REQUEST['plan_id'], DESCR_SL);
			$promotion_ids = $plan_data['promotion_ids'];
			$add_promotion_ids = $_REQUEST['promotion_ids'];
			if (empty($promotion_ids)) {
				$promotion_ids = array();
			}
			if (empty($add_promotion_ids)) {
				$add_promotion_ids = array();
			}
			foreach ($add_promotion_ids as $promotion_id) {
				if (!isset($promotion_ids[$promotion_id])) {
					$promotion_ids[$promotion_id] = $_REQUEST['coupons'][$promotion_id];
				} else {
					unset($add_promotion_ids[$promotion_id]);
				}
			}
			$plan_data['promotion_ids'] = $promotion_ids;
			$plan_id = fn_update_affiliate_plan($plan_data, $_REQUEST['plan_id'], DESCR_SL);
		}

		$suffix = ".update?plan_id=$plan_id";
	}

	//
	// Delete coupons
	//
	if ($mode == 'delete_coupons') {
		if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['promotion_ids'])) {
			$plan_id = fn_delete_affiliate_coupons($_REQUEST['promotion_ids'], $_REQUEST['plan_id']);
		}

		$suffix = ".update?plan_id=$plan_id";
	}

	//
	// Update coupons
	//
	if ($mode == 'update_coupons') {
		if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['coupons'])) {
			$plan_data = fn_get_affiliate_plan_data($_REQUEST['plan_id'], DESCR_SL);
			$promotion_ids = $plan_data['promotion_ids'];
			foreach ($_REQUEST['coupons'] as $promotion_id => $coupon) {
				if (isset($promotion_ids[$promotion_id])) {
					$promotion_ids[$promotion_id] = $coupon;
				} else {
					unset($_REQUEST['coupons'][$promotion_id]);
				}
			}
			$plan_data['promotion_ids'] = $promotion_ids;
			$plan_id = fn_update_affiliate_plan($plan_data, $_REQUEST['plan_id'], DESCR_SL);
		}

		$suffix = ".update?plan_id=$plan_id";
	}

	return array(CONTROLLER_STATUS_OK, "affiliate_plans$suffix");
}


// ---------------------- GET routines ---------------------------------------

if ($mode == 'update') {
	$affiliate_plan = fn_get_affiliate_plan_data($_REQUEST['plan_id'], DESCR_SL);

	if (empty($affiliate_plan)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	// [Breadcrumbs]
	fn_add_breadcrumb(fn_get_lang_var('plans'), "affiliate_plans.manage");
	// [/Breadcrumbs]

	// [Page sections]
	Registry::set('navigation.tabs', array (
		'general' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'linked_products' => array (
			'title' => fn_get_lang_var('products'),
			'js' => true
		),
		'linked_categories' => array (
			'title' => fn_get_lang_var('categories'),
			'js' => true
		),
		'coupons' => array (
			'title' => fn_get_lang_var('coupons'),
			'js' => true
		),
		'multi_tier_affiliates' => array (
			'title' => fn_get_lang_var('multi_tier_affiliates'),
			'js' => true
		),
	));
	// [/Page sections]

	$linked_products = array();

	foreach ($affiliate_plan['product_ids'] as $prod_id => $sale) {
		$linked_products[$prod_id] = fn_get_product_data($prod_id, $auth, DESCR_SL);
		$linked_products[$prod_id]['sale'] = $sale;
	}
	$view->assign('linked_products', $linked_products);

	$linked_categories = array();
	foreach ($affiliate_plan['category_ids'] as $cat_id => $sale) {
		$linked_categories[$cat_id]['category'] = fn_get_category_name($cat_id, DESCR_SL);
		$linked_categories[$cat_id]['category_id'] = $cat_id;
		$linked_categories[$cat_id]['sale'] = $sale;
	}
	$view->assign('linked_categories', $linked_categories);

	$params = array (
		'promotion_id' => empty($affiliate_plan['promotion_ids']) ? array('0' => 0) : array_keys($affiliate_plan['promotion_ids'])
	);
	list($affiliate_plan['coupons']) = fn_get_promotions($params);

	foreach ($affiliate_plan['coupons'] as $promotion_id => $coupon_data) {
		if (isset($affiliate_plan['promotion_ids'][$promotion_id])) {
			$affiliate_plan['coupons'][$promotion_id]['use_coupon'] = $affiliate_plan['promotion_ids'][$promotion_id];
		}
	}

	$params = array (
		'coupons' => true
	);
	list($coupons) = fn_get_promotions($params);

	foreach (array_keys($affiliate_plan['promotion_ids']) as $promotion_id) {
		unset($coupons[$promotion_id]);
	}

	$view->assign('coupons', $coupons);

	$view->assign('affiliate_plan', $affiliate_plan);
	$view->assign('payout_types', Registry::get('payout_types'));

} elseif ($mode == 'add') {

	// [Breadcrumbs]
	fn_add_breadcrumb(fn_get_lang_var('plans'), "affiliate_plans.manage");
	// [/Breadcrumbs]

	// [Page sections]
	Registry::set('navigation.tabs', array (
		'general' => array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		),
		'linked_products' => array (
			'title' => fn_get_lang_var('products'),
			'js' => true
		),
		'linked_categories' => array (
			'title' => fn_get_lang_var('categories'),
			'js' => true
		),
		'coupons' => array (
			'title' => fn_get_lang_var('coupons'),
			'js' => true
		),
		'multi_tier_affiliates' => array (
			'title' => fn_get_lang_var('multi_tier_affiliates'),
			'js' => true
		),
	));
	// [/Page sections]

	$view->assign('payout_types', Registry::get('payout_types'));

} elseif ($mode == 'manage') {

	$plans = fn_get_affiliate_plans($_REQUEST, DESCR_SL);

	$view->assign('affiliate_plans', $plans);

} elseif ($mode == 'delete_commission') {
	if (!empty($_REQUEST['plan_id']) && isset($_REQUEST['commission_id'])) {
		$plan_id = fn_delete_affiliate_commissions((array)$_REQUEST['commission_id'], $_REQUEST['plan_id']);
		return array(CONTROLLER_STATUS_REDIRECT, "affiliate_plans.update?plan_id=$plan_id&selected_section=multi_tier_affiliates");
	}
	return array(CONTROLLER_STATUS_REDIRECT, "affiliate_plans.manage");

} elseif ($mode == 'delete_product') {
	if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['product_id'])) {
		$plan_id = fn_delete_affiliate_products((array)$_REQUEST['product_id'], $_REQUEST['plan_id']);
		return array(CONTROLLER_STATUS_REDIRECT, "affiliate_plans.update?plan_id=$plan_id&selected_section=linked_products");
	}
	return array(CONTROLLER_STATUS_REDIRECT, "affiliate_plans.manage");

} elseif ($mode == 'delete_category') {
	if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['category_id'])) {
		$plan_id = fn_delete_affiliate_categories((array)$_REQUEST['category_id'], $_REQUEST['plan_id']);
		return array(CONTROLLER_STATUS_REDIRECT, "affiliate_plans.update?plan_id=$plan_id&selected_section=linked_categories");
	}
	return array(CONTROLLER_STATUS_REDIRECT, "affiliate_plans.manage");

} elseif ($mode == 'delete_coupon') {
	if (!empty($_REQUEST['plan_id']) && !empty($_REQUEST['promotion_id'])) {
		$plan_id = fn_delete_affiliate_coupons((array)$_REQUEST['promotion_id'], $_REQUEST['plan_id']);
		return array(CONTROLLER_STATUS_REDIRECT, "affiliate_plans.update?plan_id=$plan_id&selected_section=coupons");
	}
	return array(CONTROLLER_STATUS_REDIRECT, "affiliate_plans.manage");

} elseif ($mode == 'delete') {
	if (!empty($_REQUEST['plan_id'])) {
		fn_delete_affiliate_plans((array)$_REQUEST['plan_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "affiliate_plans.manage");
}

//
// [Functions]
//

function fn_delete_affiliate_coupons($promotion_ids, $plan_id)
{
	$plan_data = fn_get_affiliate_plan_data($plan_id, DESCR_SL);
	$_promotion_ids = $plan_data['promotion_ids'];
	foreach ($_promotion_ids as $promotion_id => $prod_name) {
		if (in_array($promotion_id, $promotion_ids)) {
			unset($_promotion_ids[$promotion_id]);
		}
	}
	$plan_data['promotion_ids'] = $_promotion_ids;
	return fn_update_affiliate_plan($plan_data, $plan_id, DESCR_SL);
}

function fn_delete_affiliate_categories($category_ids, $plan_id)
{
	$plan_data = fn_get_affiliate_plan_data($plan_id, DESCR_SL);
	$_categories_ids = $plan_data['category_ids'];
	if (!empty($_categories_ids) && is_array($_categories_ids)) {
		foreach ($_categories_ids as $cat_id=>$prod_name) {
			if (in_array($cat_id, $category_ids)) {
				unset($_categories_ids[$cat_id]);
			}
		}
	}
	$plan_data['category_ids'] = $_categories_ids;
	return fn_update_affiliate_plan($plan_data, $plan_id, DESCR_SL);
}

function fn_delete_affiliate_products($product_ids, $plan_id)
{
	$plan_data = fn_get_affiliate_plan_data($plan_id, DESCR_SL);
	$_products_ids = $plan_data['product_ids'];
	if (!empty($_products_ids) && is_array($_products_ids)) {
		foreach ($_products_ids as $prod_id => $prod_name) {
			if (in_array($prod_id, $product_ids)) {
				unset($_products_ids[$prod_id]);
			}
		}
	}
	$plan_data['product_ids'] = $_products_ids;
	return fn_update_affiliate_plan($plan_data, $plan_id, DESCR_SL);
}

function fn_delete_affiliate_commissions($commission_ids, $plan_id)
{
	$plan_data = fn_get_affiliate_plan_data($plan_id, DESCR_SL);
	if (!empty($plan_data)) {
		foreach ($commission_ids as $com_id) {
			unset($plan_data['commissions'][$com_id]);
		}

		return fn_update_affiliate_plan($plan_data, $plan_id, DESCR_SL);
	}
	return $plan_id;
}

//
// Delete affiliate plans
//
function fn_delete_affiliate_plans($affiliate_plans_ids)
{
	if (!empty($affiliate_plans_ids)) {
		if (!is_array($affiliate_plans_ids)) {
			$affiliate_plans_ids = explode(',', $affiliate_plans_ids);
		}
		db_query("DELETE FROM ?:common_descriptions WHERE object_holder = 'affiliate_plans' AND object_id IN (?n)", $affiliate_plans_ids);
		db_query("DELETE FROM ?:affiliate_plans WHERE plan_id IN (?n)", $affiliate_plans_ids);

		return true;
	}
	return false;
}

//
// Update affiliate plan
//
function fn_update_affiliate_plan($data, $plan_id, $lang_code = DESCR_SL)
{
	if (isset($data['commissions'])) {
		$data['commissions'] = array_map('floatval', $data['commissions']);
		$data['commissions'] = implode(';', $data['commissions']);
	}

	if (isset($data['payout_types'])) {
		$data['payout_types'] = fn_check_payout_value($data['payout_types']);
		$data['payout_types'] = serialize($data['payout_types']);
	}

	if (isset($data['product_ids'])) {
		$data['product_ids'] = fn_check_payout_value($data['product_ids']);
		$data['product_ids'] = serialize($data['product_ids']);
	}

	if (isset($data['category_ids'])) {
		$data['category_ids'] = fn_check_payout_value($data['category_ids']);
		$data['category_ids'] = serialize($data['category_ids']);
	}

	if (isset($data['promotion_ids'])) {
		$data['promotion_ids'] = fn_check_payout_value($data['promotion_ids']);
		$data['promotion_ids'] = serialize($data['promotion_ids']);
	}

	if (!empty($plan_id)) {
		db_query("UPDATE ?:affiliate_plans SET ?u WHERE plan_id = ?i", $data, $plan_id);
		$data['object'] = $data['name'];
		db_query("UPDATE ?:common_descriptions SET ?u WHERE object_id = ?i AND object_holder = 'affiliate_plans' AND lang_code = ?s", $data, $plan_id, $lang_code);
	} else {
		$plan_id = $data['plan_id'] = db_query("INSERT INTO ?:affiliate_plans ?e", $data);

		if (!empty($plan_id)) {
			$_data = array(
				'object' => $data['name'],
				'description' => $data['description'],
				'object_id' => $plan_id,
				'object_holder' => 'affiliate_plans'
			);

			foreach ((array)Registry::get('languages') as $_data['lang_code'] => $_ldata) {
				db_query("INSERT INTO ?:common_descriptions ?e", $_data);
			}
		}
	}

	return $plan_id;
}

//
// value can not be less than 0
// if value type is percent then value can not be greater than 100
//
function fn_check_payout_value($values)
{
	if (!empty($values) && is_array($values)) {
		foreach ($values as $key => $item) {
			$values[$key]['value'] = floatval($item['value']);
			if ($values[$key]['value'] < 0) {
				$values[$key]['value'] = 0;
			}
			if (!empty($item['value_type']) && $item['value_type'] == 'P' && $values[$key]['value'] > 100) {
				$values[$key]['value'] = 100;
			}
		}
	}
	return $values;
}

//
// Get data of affiliate plans
//
function fn_get_affiliate_plans($params, $lang_code = CART_LANGUAGE)
{
	$params['page'] = empty($params['page']) ? 1 : $params['page'];

	$total = db_get_field("SELECT COUNT(*) FROM ?:affiliate_plans as plans LEFT JOIN ?:common_descriptions as com_descr ON object_holder = 'affiliate_plans' AND object_id = plan_id AND lang_code = ?s WHERE 1 ORDER BY object", $lang_code);

	$items_per_page = (AREA == 'A') ? Registry::get('settings.Appearance.admin_elements_per_page') : Registry::get('settings.Appearance.elements_per_page');
	$limit = fn_paginate($params['page'], $total, $items_per_page);

	return db_get_hash_array("SELECT plans.plan_id, object as name, COUNT(user_id) as count_partners, status FROM ?:affiliate_plans as plans LEFT JOIN ?:common_descriptions as com_descr ON object_holder = 'affiliate_plans' AND object_id = plans.plan_id AND lang_code = ?s LEFT JOIN ?:aff_partner_profiles ON plans.plan_id = ?:aff_partner_profiles.plan_id AND ?:aff_partner_profiles.approved = 'A' WHERE 1 GROUP BY plans.plan_id ORDER BY object $limit ", 'plan_id', $lang_code);
}

//
// [/Functions]
//
?>