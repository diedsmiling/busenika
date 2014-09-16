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
// $Id: access_restrictions.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'add') {
		// Add IPs to the Customer and admin area
		if ($_REQUEST['selected_section'] == 'ip' || $_REQUEST['selected_section'] == 'admin_panel') {
			$ip = ($_REQUEST['selected_section'] == 'ip') ? $_REQUEST['ip'] : $_REQUEST['admin_panel'];
			$visitor_ip = fn_get_ip(true);
			foreach ($ip as $k => $v) {
				$_data = array();
				if (!empty($v['range_from']) || !empty($v['range_to'])) {
					$range_from = (empty($v['range_from'])) ? $v['range_to'] : $v['range_from'];
					$range_to = (empty($v['range_to'])) ? $v['range_from'] : $v['range_to'];
					if (fn_validate_ip($range_from, true) && fn_validate_ip($range_to, true)) {
						$_data['ip_from'] = sprintf("%u", ip2long($range_from));
						$_data['ip_to'] = sprintf("%u", ip2long($range_to));
						$type_s = ($_REQUEST['selected_section'] == 'ip') ? 'ip' : 'aa';
						$_data['type'] = (($range_from == $range_to) ? ($type_s.'s') : ($type_s.'r')); // IP range or specific
						$_data['timestamp'] = TIME;
						$_data['status'] = $v['status'];

						if ($_REQUEST['selected_section'] == 'admin_panel' && Registry::get('addons.access_restrictions.admin_reverse_ip_access') != 'Y' && $_data['ip_from'] <= $visitor_ip['host'] && $_data['ip_to'] >= $visitor_ip['host']) {
							$msg = fn_get_lang_var('warning_of_ip_adding', DESCR_SL);
							$msg = str_replace('[entered_ip]', long2ip($_data['ip_from']) . ($_data['ip_from'] == $_data['ip_to'] ? '' : '-'.long2ip($_data['ip_to'])), $msg);
							$msg = str_replace('[your_ip]', long2ip($visitor_ip['host']), $msg);
							fn_set_notification('W', fn_get_lang_var('warning', DESCR_SL), $msg);
						} else {
							$_data['item_id'] = db_query("INSERT INTO ?:access_restriction ?e", $_data);
							$_data['reason'] = $v['reason'];
							foreach ((array)Registry::get('languages') as $_data['lang_code'] => $v) {
								db_query("INSERT INTO ?:access_restriction_reason_descriptions ?e", $_data);
							}
						}
					}
				}
			}

		// Add domains
		} elseif ($_REQUEST['selected_section'] == 'domain') {
			foreach ($_REQUEST['domain'] as $k => $v) {
				$_data = array();
				if (fn_validate_domain_name($v['value'], true)) {
					$_data['value']= $v['value'];
					$_data['status']= $v['status'];
					$_data['type'] = 'd'; // Domain
					$_data['timestamp'] = TIME;
					$_data['item_id'] = db_query("INSERT INTO ?:access_restriction ?e", $_data);
					$_data['reason'] = $v['reason'];
					foreach ((array)Registry::get('languages') as $_data['lang_code'] => $v) {
						db_query("INSERT INTO ?:access_restriction_reason_descriptions ?e", $_data);
					}
				}
			}

		// Add emails
		} elseif ($_REQUEST['selected_section'] == 'email') {
			foreach ($_REQUEST['email'] as $k => $v) {
				$_data = array();
				$_data['status']= $v['status'];
				if (strstr($v['value'], '@') && strpos($v['value'], '*@') !== 0) {
					if (fn_validate_email_name($v['value'], true) && fn_validate_domain_name(substr($v['value'], strpos($v['value'], '@')), true)) {
						$_data['value'] = $v['value'];
						$_data['type'] = 'es'; // specific E-Mail
						$_data['timestamp'] = TIME;
						$_data['item_id'] = db_query("INSERT INTO ?:access_restriction ?e", $_data);
						$_data['reason'] = $v['reason'];
						foreach ((array)Registry::get('languages') as $_data['lang_code'] => $v) {
							db_query("INSERT INTO ?:access_restriction_reason_descriptions ?e", $_data);
						}
					}
				} else {
					$_domain = (strpos($v['value'], '*@') === 0) ? substr($v['value'], 2) : $v['value'];
					if (fn_validate_domain_name($_domain, true)) {
						$_data['value'] = $v['value'];
						$_data['type'] = 'ed'; // E-Mail domain
						$_data['timestamp'] = TIME;
						$_data['item_id'] = db_query("INSERT INTO ?:access_restriction ?e", $_data);
						$_data['reason'] = $v['reason'];
						foreach ((array)Registry::get('languages') as $_data['lang_code'] => $v) {
							db_query("INSERT INTO ?:access_restriction_reason_descriptions ?e", $_data);
						}
					}
				}

			}

		// Add credit cards
		} elseif ($_REQUEST['selected_section'] == 'credit_card') {
			foreach ($_REQUEST['credit_card'] as $k => $v) {
				$_data = array();
				$v = str_replace(array("-", " "), "", $v);
				if (fn_validate_cc_number($v['value'], true)) {
					$_data['status']= $v['status'];
					$_data['value'] = $v['value'];
					$_data['type'] = 'cc'; // specific Credit Card Number
					$_data['timestamp'] = TIME;
					$_data['item_id'] = db_query("INSERT INTO ?:access_restriction ?e", $_data);
					$_data['reason'] = $v['reason'];
					foreach ((array)Registry::get('languages') as $_data['lang_code'] => $v) {
						db_query("INSERT INTO ?:access_restriction_reason_descriptions ?e", $_data);
					}
				}
			}
		}
	}

	if ($mode == 'update') {
		foreach ($_REQUEST['items_data'] as $k => $v) {
			db_query("UPDATE ?:access_restriction SET ?u WHERE item_id = ?i", $v, $k);
			db_query("UPDATE ?:access_restriction_reason_descriptions SET ?u WHERE item_id = ?i AND type = ?s AND lang_code = ?s", $v, $k, $v['type'], DESCR_SL);
		}
	}

	if ($mode == 'delete') {
		foreach ($_REQUEST['item_ids'] as $v) {
			db_query("DELETE FROM ?:access_restriction WHERE item_id = ?i", $v);
		}
	}

	if ($mode == 'make_permanent') {
		if ($_REQUEST['selected_section'] == 'ip' || $_REQUEST['selected_section'] == 'admin_panel') {
			$new_type = ($_REQUEST['selected_section'] == 'ip') ? 'ips' : 'aas';
			$old_type = ($_REQUEST['selected_section'] == 'ip') ? 'ipb' : 'aab';
			foreach ($_REQUEST['item_ids'] as $v) {
				if ($items_data[$v]['type'] == $old_type) {
					db_query("UPDATE ?:access_restriction SET ?u WHERE item_id = ?i", array('type' => $new_type, 'expires' => 0), $v);
					db_query("UPDATE ?:access_restriction_reason_descriptions SET ?u WHERE item_id = ?i AND type = ?s AND lang_code = ?s", array('type' => $new_type), $v, $old_type, DESCR_SL);
				}
			}
		}
	}

	return array(CONTROLLER_STATUS_OK, "access_restrictions.manage");
}

// ---------------------- GET routines ---------------------------------------

if ($mode == 'manage') {
	$prefix = "access_restrictions.manage?selected_section";

	Registry::set('navigation.tabs', array (
		'ip' => array (
			'href' => $prefix . '=ip',
			'title' => fn_get_lang_var('ip')
		),
		'domain' => array (
			'href' => $prefix . '=domain',
			'title' => fn_get_lang_var('domain')
		),
		'email' => array (
			'href' => $prefix . '=email',
			'title' => fn_get_lang_var('email')
		),
		'credit_card' => array (
			'href' => $prefix . '=credit_card',
			'title' => fn_get_lang_var('credit_card')
		),
		'admin_panel' => array (
			'href' => $prefix . '=admin_panel',
			'title' => fn_get_lang_var('admin_panel')
		),
	));

	$selected_section = (!empty($_REQUEST['selected_section'])) ? $_REQUEST['selected_section'] : 'ip';

	$types = array (
		'ip' => array("ips", "ipr", "ipb"),
		'domain' => array("d"),
		'email' => array("es", "ed"),
		'credit_card' => array("cc"),
		'admin_panel' => array("aas", "aar", "aab"),
		);

	// Select sorting
	if ($selected_section == 'ip' || $selected_section == 'admin_panel') {
		$sortings = array (
			'ip' => 'a.ip_from',
			'reason' => 'b.reason',
			'created' => 'a.timestamp',
			'expires' => 'a.expires',
			'status' => 'a.status'
		);
	} else {
		$sortings = array (
			'value' => 'a.value',
			'reason' => 'b.reason',
			'created' => 'a.timestamp',
			'status' => 'a.status'
		);
	}

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	$sort_order = !empty($_REQUEST['sort_order']) ? $_REQUEST['sort_order'] : '';
	$sort_by = !empty($_REQUEST['sort_by']) ? $_REQUEST['sort_by'] : '';

	if (empty($sort_order) || empty($directions[$sort_order])) {
		$sort_order = 'desc';
	}

	if (empty($sort_by) || empty($sortings[$sort_by])) {
		$sort_by = 'created';
	}

	$sort = $sortings[$sort_by]. " " .$directions[$sort_order];

	$items_per_page = Registry::get('settings.Appearance.admin_elements_per_page');
	$total_items = db_get_field("SELECT COUNT(a.item_id) FROM ?:access_restriction as a WHERE a.type IN (?a)", $types[$selected_section]);
	$limit = fn_paginate(@$_REQUEST['page'], $total_items, $items_per_page); // fixme

	$access[$selected_section] = db_get_array("SELECT a.*, b.reason FROM ?:access_restriction as a LEFT JOIN ?:access_restriction_reason_descriptions as b ON a.item_id = b.item_id AND b.type = a.type AND lang_code = ?s WHERE a.type IN (?a) ORDER BY $sort $limit", DESCR_SL, $types[$selected_section]);
	$ip = fn_get_ip(true);

	$view->assign('sort_order', (($sort_order == 'asc') ? 'desc' : 'asc'));
	$view->assign('sort_by', $sort_by);

	$view->assign('show_mp', db_get_field("SELECT item_id FROM ?:access_restriction WHERE type = ?s", (($selected_section == 'ip') ? 'ipb' : 'aab')));
	$view->assign('selected_section', $selected_section);
	$view->assign('access', $access);
	$view->assign('access_types', $types);
	$view->assign('host_ip', $ip['host']);

} elseif ($mode == 'delete') {
	if (!empty($_REQUEST['item_id'])) {
		db_query("DELETE FROM ?:access_restriction WHERE item_id = ?i", $_REQUEST['item_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "access_restrictions.manage?selected_section=$_REQUEST[selected_section]");
}
?>