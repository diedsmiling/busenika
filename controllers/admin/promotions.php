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
// $Id: promotions.php 10490 2010-08-23 11:30:06Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

$_REQUEST['promotion_id'] = empty($_REQUEST['promotion_id']) ? 0 : $_REQUEST['promotion_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	fn_trusted_vars('promotion_data');
	$suffix = '';

	//
	// Update promotion
	//
	if ($mode == 'update') {

		$promotion_id = fn_update_promotion($_REQUEST['promotion_data'], $_REQUEST['promotion_id'], DESCR_SL);

		$suffix = ".update?promotion_id=$promotion_id";
	}

	//
	// Multiple promotion update
	//
	if ($mode == 'm_update') {

		if (!empty($_REQUEST['promotions']) && is_array($_REQUEST['promotions'])) {
			foreach ($_REQUEST['promotions'] as $pr_id => $v) {
				db_query("UPDATE ?:promotions SET ?u WHERE promotion_id = ?i", $v, $pr_id);
				db_query("UPDATE ?:promotion_descriptions SET ?u WHERE promotion_id = ?i AND lang_code = ?s", $v, $pr_id, DESCR_SL);
			}
		}

		$suffix = ".manage";
	}

	//
	// Delete selected promotions
	//
	if ($mode == 'delete') {

		if (!empty($_REQUEST['promotion_ids'])) {
			fn_delete_promotions($_REQUEST['promotion_ids']);
		}

		$suffix = ".manage";
	}

	return array(CONTROLLER_STATUS_OK, "promotions$suffix");
}

// ----------------------------- GET routines -------------------------------------------------

// promotion data
if ($mode == 'update') {

	fn_add_breadcrumb(fn_get_lang_var('promotions'), "promotions.manage");

	Registry::set('navigation.tabs', array (
		'details' => array (
			'title' => fn_get_lang_var('general'),
			'href' => "promotions.update?promotion_id=$_REQUEST[promotion_id]&selected_section=details",
			'js' => true
		),
		'conditions' => array (
			'title' => fn_get_lang_var('conditions'),
			'href' => "promotions.update?promotion_id=$_REQUEST[promotion_id]&selected_section=conditions",
			'js' => true
		),
		'bonuses' => array (
			'title' => fn_get_lang_var('bonuses'),
			'href' => "promotions.update?promotion_id=$_REQUEST[promotion_id]&selected_section=bonuses",
			'js' => true
		),
	));

	$promotion_data = fn_get_promotion_data($_REQUEST['promotion_id']);

	if (empty($promotion_data)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	$view->assign('promotion_data', $promotion_data);

	$view->assign('zone', $promotion_data['zone']);
	$view->assign('schema', fn_promotion_get_schema());

// Add promotion
} elseif ($mode == 'add') {

	fn_add_breadcrumb(fn_get_lang_var('promotions'), "promotions.manage");

	Registry::set('navigation.tabs', array (
		'details' => array (
			'title' => fn_get_lang_var('general'),
			'href' => "promotions.add?selected_section=details",
			'js' => true
		),
		'conditions' => array (
			'title' => fn_get_lang_var('conditions'),
			'href' => "promotions.add?selected_section=conditions",
			'js' => true
		),
		'bonuses' => array (
			'title' => fn_get_lang_var('bonuses'),
			'href' => "promotions.add?selected_section=bonuses",
			'js' => true
		),
	));

	$view->assign('zone', !empty($_REQUEST['zone']) ? $_REQUEST['zone'] : 'catalog');
	$view->assign('schema', fn_promotion_get_schema());

} elseif ($mode == 'dynamic') {
	$view->assign('schema', fn_promotion_get_schema());
	$view->assign('prefix', $_REQUEST['prefix']);
	$view->assign('elm_id', $_REQUEST['elm_id']);

	if (!empty($_REQUEST['zone'])) {
		$view->assign('zone', $_REQUEST['zone']);
	}

	if (!empty($_REQUEST['condition'])) {
		$view->assign('condition_data', array('condition' => $_REQUEST['condition']));

	} elseif (!empty($_REQUEST['bonus'])) {
		$view->assign('bonus_data', array('bonus' => $_REQUEST['bonus']));
	}


// promotions list
} elseif ($mode == 'manage') {

	list($promotions, $search) = fn_get_promotions($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

	$view->assign('search', $search);
	$view->assign('promotions', $promotions);

// Delete selected promotions
} elseif ($mode == 'delete') {

	if (!empty($_REQUEST['promotion_id'])) {
		fn_delete_promotions($_REQUEST['promotion_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "promotions.manage");
}

function fn_update_promotion($data, $promotion_id, $lang_code = DESCR_SL)
{
	if (!empty($data['conditions']['conditions'])) {
		$data['conditions_hash'] = fn_promotion_serialize($data['conditions']['conditions']);
	} else {
		$data['conditions_hash'] = '';
	}

	$data['conditions'] = empty($data['conditions']) ? array() : $data['conditions'];
	$data['bonuses'] = empty($data['bonuses']) ? array() : $data['bonuses'];

	fn_promotions_check_group_conditions($data['conditions']);

	if ($data['bonuses']) {
		foreach($data['bonuses'] as $k => $v) {
			if (empty($v['bonus'])) {
				unset($data['bonuses'][$k]);
			}
		}
	}

	$data['conditions'] = serialize($data['conditions']);
	$data['bonuses'] = serialize($data['bonuses']);

	$from_date = $data['from_date'];
	$to_date = $data['to_date'];

	$data['from_date'] = !empty($from_date) ? fn_parse_date($from_date) : 0;
	$data['to_date'] = !empty($to_date) ? fn_parse_date($to_date, true) : 0;

	if (!empty($data['to_date']) && $data['to_date'] < $data['from_date']) { // protection from incorrect date range (special for isergi :))
		$data['from_date'] = fn_parse_date($to_date);
		$data['to_date'] = fn_parse_date($from_date, true);
	}
	
	if (!empty($promotion_id)) {
		db_query("UPDATE ?:promotions SET ?u WHERE promotion_id = ?i", $data, $promotion_id);
		db_query('UPDATE ?:promotion_descriptions SET ?u WHERE promotion_id = ?i AND lang_code = ?s', $data, $promotion_id, $lang_code);
	} else {
		$promotion_id = $data['promotion_id'] = db_query("REPLACE INTO ?:promotions ?e", $data);

		foreach ((array)Registry::get('languages') as $data['lang_code'] => $_v) {
			db_query("REPLACE INTO ?:promotion_descriptions ?e", $data);
		}
	}

	return $promotion_id;
}

function fn_promotions_check_group_conditions(&$conditions, $parents = array())
{
	static $schema = array();

	if (empty($schema)) {
		$schema = fn_promotion_get_schema();
	}

	if (!empty($conditions['set'])) {
		if (!empty($conditions['conditions'])) {
			$parents[] = array(
				'set_value' => $conditions['set_value'],
				'set' => $conditions['set']
			);

			fn_promotions_check_group_conditions($conditions['conditions'], $parents);
		}
	} else {
		foreach ($conditions as $k => $c) {
			if (!empty($c['conditions'])) {
				fn_promotions_check_group_conditions($conditions[$k]['conditions'], fn_array_merge($parents, array('set_value' => $c['set_value'], 'set' => $c['set']), false));
				
				if (!$c['conditions']) {
					unset($c['conditions']);
				}
			} elseif(empty($c['condition']) || !isset($c['value'])) {
				unset($conditions[$k]);
			} elseif (!empty($schema['conditions'][$c['condition']]['applicability']['group'])) {
				foreach ($parents as $_c) {
					if ($_c['set_value'] != $schema['conditions'][$c['condition']]['applicability']['group']['set_value']) {
						$msg = fn_get_lang_var('warning_promotions_incorrect_condition');
						$msg = str_replace(array('[condition]', '[set_value]'), array(fn_get_lang_var('promotion_cond_' . $c['condition']), fn_get_lang_var($schema['conditions'][$c['condition']]['applicability']['group']['set_value'] == true ? 'true': 'false')), $msg);
						fn_set_notification('W', fn_get_lang_var('warning'), $msg);
						unset($conditions[$k]);
					}
				}
			}
		}
	}
}

?>