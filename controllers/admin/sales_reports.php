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
// $Id: sales_reports.php 10028 2010-07-09 11:17:28Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

require(DIR_CORE . 'fn.sales_reports.php');

$order_status_descr = fn_get_statuses(STATUSES_ORDER, true, true, true);
$view->assign('order_status_descr', $order_status_descr);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$suffix = '';

	//
	// Reports list routines
	//
	if ($mode == 'reports_list') {
		if ($action == 'add') {
			if (!fn_is_empty($_REQUEST['add_report'])) {
				foreach ($_REQUEST['add_report'] as $k => $v) {
					if (!empty($v['description'])) {
						$v['type'] = 'O';
						$v['period'] = 'Y';
						list($v['time_from'], $v['time_to']) = fn_create_periods($v);
						$report_id = db_query("INSERT INTO ?:sales_reports ?e", $v);

						fn_create_description('sales_reports_descriptions', "report_id", $report_id, array("description" => $v['description']));
					}
				}
			}
		}

		if ($action == 'update') {
			foreach ($_REQUEST['update_reports'] as $k => $v) {
				db_query("UPDATE ?:sales_reports SET ?u WHERE report_id = ?i", $v, $v['report_id']);
				db_query("UPDATE ?:sales_reports_descriptions SET ?u WHERE report_id = ?i AND lang_code = ?s", $v, $v['report_id'], DESCR_SL);
			}
		}

		if ($action == 'delete') {
			foreach ($_REQUEST['report_ids'] as $v) {
				fn_delete_report_data('report', $v);
			}
		}

		$suffix = ".reports_list";
	}

	//
	// Report view routines
	//
	if ($mode == 'reports_view') {

		$_suffix = '';

		if ($action == 'update_report') {
			$data = array (
				'period' => empty($_REQUEST['period']) ? 'C' : $_REQUEST['period'],
			);
			if (!empty($_REQUEST['period']) && $_REQUEST['period'] != 'A') {
				list($data['time_from'], $data['time_to']) = fn_create_periods($_REQUEST);
			} else {
				$data['time_from'] = $data['time_to'] = 0;
			}

			db_query("UPDATE ?:sales_reports SET ?u WHERE report_id = ?i", $data, $_REQUEST['report_id']);

			if (!empty($_REQUEST['selected_section'])) { // FIXME!!! Bad style
				$_suffix = "&table_id=" . str_replace('table_', '', $_REQUEST['selected_section']);
			}
		}

		$suffix = ".reports?report_id=$_REQUEST[report_id]" . $_suffix;
	}

	//
	// Report routines
	//
	if ($mode == 'report') {

		if ($action == 'clone_table') {
			foreach ($_REQUEST['del'] as $k => $v) {
				fn_report_table_clone($_REQUEST['report_id'], $k);
			}
		}

		if ($action == 'update') {
			db_query('UPDATE ?:sales_reports SET ?u WHERE report_id = ?i', $_REQUEST['report'], $_REQUEST['report_id']);
			db_query('UPDATE ?:sales_reports_descriptions SET ?u WHERE report_id = ?i AND lang_code = ?s', $_REQUEST['report_description'], $_REQUEST['report_id'], DESCR_SL);
			foreach ($_REQUEST['tables'] as $k => $value) {
				if (!extension_loaded('gd') && $value['type'] != 'T') {
					if (empty($_flag)) {
						fn_set_notification('W',fn_get_lang_var('warning'), fn_get_lang_var('text_gd_not_avail'));
					}
					$_flag = true;
					$value['type'] = 'T';
				}
				db_query("UPDATE ?:sales_reports_tables SET ?u WHERE table_id = ?i", $value, $k);
				db_query('UPDATE ?:sales_reports_table_descriptions SET ?u WHERE table_id = ?i AND lang_code = ?s', $_REQUEST['table_description'][$k], $k, DESCR_SL);
				if ($value['type'] == 'P' || $value['type'] == 'C') {
					db_query("UPDATE ?:sales_reports_tables SET interval_id = 1 WHERE table_id = ?i", $k);
				}
			}
		}

		if ($action == 'delete_table') {
			foreach ($_REQUEST['del'] as $k => $v) {
				fn_delete_report_data('table', $k);
			}
		}

		$suffix = ".report?report_id=$_REQUEST[report_id]";
	}

	//
	// Tables routines
	//
	if ($mode == 'table') {

		$suffix = ".table.edit?report_id=$_REQUEST[report_id]&table_id=$_REQUEST[table_id]";

		// ************************************ TABLE *************************** //
		if ($action == 'add') {
			// Add table
			$table = $_REQUEST['table'];
			if (empty($table['description'])) {
				return array(CONTROLLER_STATUS_REDIRECT, "sales_reports.table.add?report_id=$_REQUEST[report_id]");
			}

			if ($table['type'] == 'P' || $table['type'] == 'C') {
				$table['interval_id'] = '1';
			}

			$table['report_id'] = $_REQUEST['report_id'];
			$table_id = db_query("REPLACE INTO ?:sales_reports_tables ?e", $table);

			fn_create_description('sales_reports_table_descriptions', "table_id", $table_id, array('description' => $table['description']));

			// Create parameters
			$_data = fn_check_table_fields($_REQUEST['update_element'], 'sales_reports_table_elements');
			$_data['table_id'] = $table_id;
			$_data['report_id'] = $_REQUEST['report_id'];
			$_data['element_hash'] = fn_generate_element_hash($table_id, $_data['element_id'], '');
			db_query("INSERT INTO ?:sales_reports_table_elements ?e", $_data);

			foreach ($_REQUEST['conditions'] as $section => $ids) {
				db_query("DELETE FROM ?:sales_reports_table_conditions WHERE table_id = ?i AND code = ?s", $table_id, $section);
				$object_ids = is_array($ids) ? $ids : (empty($ids) ? array() : explode(',', $ids));
				foreach ($object_ids as $o_id) {
					$data = array (
						'sub_element_id' => $o_id,
						'table_id' => $table_id,
						'code' => $section
					);

					db_query('REPLACE INTO ?:sales_reports_table_conditions ?e', $data);
				}
			}

			$suffix = ".table.edit?report_id=$_REQUEST[report_id]&table_id=$table_id";
		}

		if ($action == 'update') {

			db_query('UPDATE ?:sales_reports_tables SET ?u WHERE table_id = ?i', $_REQUEST['table'], $_REQUEST['table_id']);
			db_query('UPDATE ?:sales_reports_table_descriptions SET ?u WHERE table_id = ?i AND lang_code = ?s', $_REQUEST['table'], $_REQUEST['table_id'], DESCR_SL);

			if ($_REQUEST['table']['type'] == 'P' || $_REQUEST['table']['type'] == 'C') {
				db_query("UPDATE ?:sales_reports_tables SET interval_id = 1 WHERE table_id = ?i", $_REQUEST['table_id']);
			}

			// Update parameters
			foreach ($_REQUEST['update_element'] as $k => $v) {
				if ($v['element_id'] == '4' && $_REQUEST['table']['interval_id'] != '1') {
					db_query("UPDATE ?:sales_reports_tables SET interval_id = 1 WHERE table_id = ?i", $_REQUEST['table_id']);
					fn_set_notification('W',fn_get_lang_var('warning'), fn_get_lang_var('text_status_is_float'));
				}

				db_query('UPDATE ?:sales_reports_table_elements SET ?u WHERE table_id = ?i AND element_hash = ?s', $_REQUEST['update_element'][$k], $_REQUEST['table_id'], $k);
				if ($_REQUEST['table']['type'] != 'T' && $v['limit_auto'] > 25) {
					db_query("UPDATE ?:sales_reports_table_elements SET limit_auto = 25 WHERE table_id = ?i AND element_hash = ?s", $_REQUEST['table_id'], $k);
					fn_set_notification('W',fn_get_lang_var('warning'), fn_get_lang_var('text_max_limit_of_parameters'));
				}
			}

			foreach ($_REQUEST['conditions'] as $section => $ids) {
				db_query("DELETE FROM ?:sales_reports_table_conditions WHERE table_id = ?i AND code = ?s", $_REQUEST['table_id'], $section);
				$object_ids = is_array($ids) ? $ids : (empty($ids) ? array() : explode(',', $ids));
				foreach ($object_ids as $o_id) {
					$data = array (
						'sub_element_id' => $o_id,
						'table_id' => $_REQUEST['table_id'],
						'code' => $section
					);

					db_query('REPLACE INTO ?:sales_reports_table_conditions ?e', $data);
				}
			}
		}
	}

	return array(CONTROLLER_STATUS_OK, "sales_reports$suffix");
}



$depend_items = fn_get_depended();

//
// The list of all reports
//
if ($mode == 'reports_list') {

	if ($action == 'delete') {
		if (!empty($_REQUEST['report_id'])) {
			fn_delete_report_data('report', $_REQUEST['report_id']);
		}

		return array(CONTROLLER_STATUS_REDIRECT, "sales_reports.reports_list");
	}

	$view->assign('reports', fn_get_order_reports());

//
// Edit report
//
} elseif ($mode == 'report') {

	if ($action == 'delete_table') {
		if (!empty($_REQUEST['table_id'])) {
			fn_delete_report_data('table', $_REQUEST['table_id']);
		}

		return array(CONTROLLER_STATUS_REDIRECT, "sales_reports.report.edit?report_id=$_REQUEST[report_id]");
	}

	fn_add_breadcrumb(fn_get_lang_var('reports'), "sales_reports.reports_list");

	$report_data = fn_get_report_data($_REQUEST['report_id']);
	$view->assign('report', $report_data);

//
// Manage tables
//
} elseif ($mode == 'table') {

	if ($action == 'clear_conditions') {
		db_query("DELETE FROM ?:sales_reports_table_conditions WHERE table_id = ?i", $_REQUEST['table_id']);

		return array(CONTROLLER_STATUS_REDIRECT, "sales_reports.table.edit?report_id=$_REQUEST[report_id]&table_id=$_REQUEST[table_id]");
	}

	if ($action == 'add') {
		// [Page sections]
		Registry::set('navigation.tabs.general', array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		));

		foreach ($depend_items as $value) {
			Registry::set('navigation.tabs.' . $value['code'], array (
				'title' => fn_get_lang_var('reports_parameter_'.$value['element_id']),
				'js' => true
			));
		}

		// [/Page sections]
		$view->assign('search_condition', true);
		$view->assign('intervals', db_get_array("SELECT a.* FROM ?:sales_reports_intervals as a ORDER BY a.interval_id"));
	}

	// **************** Conditions properites *******************
	if ($action == 'edit' || $action == 'add' || $action == 'parameter_details') {
		// Payments
		$view->assign('payment_processors', db_get_array("SELECT processor_id, processor FROM ?:payment_processors"));
		$view->assign('payments', db_get_array("SELECT ?:payments.*, ?:payment_descriptions.* FROM ?:payments LEFT JOIN ?:payment_descriptions ON ?:payment_descriptions.payment_id = ?:payments.payment_id AND ?:payment_descriptions.lang_code = ?s ORDER BY ?:payments.position", DESCR_SL));
		// Users Location
		$view->assign('usergroups', fn_get_usergroups('C', CART_LANGUAGE));
		$view->assign('countries', fn_get_countries(CART_LANGUAGE, true));
		$view->assign('states', fn_get_all_states());
		$view->assign('destinations', fn_get_destinations(CART_LANGUAGE));

		// Locations
		$view->assign('destinations', fn_get_destinations(CART_LANGUAGE));
	}

	// ********************** Edit tables ***********************
	if ($action == 'edit') {
		$table_data = fn_get_report_data($_REQUEST['report_id'], $_REQUEST['table_id']);
		$conditions = fn_get_table_condition($_REQUEST['table_id']);

		if (empty($conditions)) {
			$conditions = array();
		}
		$view->assign('conditions', $conditions);
		// [Page sections]
		Registry::set('navigation.tabs.general', array (
			'title' => fn_get_lang_var('general'),
			'js' => true
		));

		foreach ($depend_items as $value) {
			Registry::set('navigation.tabs.' . $value['code'], array (
				'title' => fn_get_lang_var('reports_parameter_' . $value['element_id']),
				'js' => true
			));
		}
		// [/Page sections]
		$view->assign('search_condition', true);
		$intervals = db_get_array("SELECT a.* FROM ?:sales_reports_intervals as a ORDER BY a.interval_id");
		$view->assign('intervals', $intervals);
	}

	$report_data = fn_get_report_data($_REQUEST['report_id']);

	fn_add_breadcrumb($report_data['description'], "sales_reports.report?report_id=$_REQUEST[report_id]");
	if (!empty($table_data)) {
		$view->assign('table', $table_data);
	}

//
// View reports
//
} elseif ($mode == 'reports') {
	
	$report_id = empty($_REQUEST['report_id']) ? db_get_field("SELECT report_id FROM ?:sales_reports WHERE status = 'A' ORDER BY position ASC LIMIT 1") : $_REQUEST['report_id'];
	$table_id = empty($_REQUEST['table_id']) ? db_get_field("SELECT table_id FROM ?:sales_reports_tables WHERE report_id = ?i ORDER BY position ASC LIMIT 1", $report_id) : intval($_REQUEST['table_id']);

	$reports = fn_get_order_reports(true, $report_id);

	// If some reports defined calculate data for them
	if (!empty($reports)) {
		$report = $reports[$report_id];

		// Get report data for each table;
		if (!empty($report['tables'])) {
			if (isset($report['tables'][$table_id])) {
				$table = $report['tables'][$table_id];
				if (!empty($table['elements']) && !empty($table['intervals']) && $table['type'] == 'T') {
					$_table_cond = fn_get_table_condition($table['table_id']);

					if (!empty($_table_cond)) {
						$table_conditions[$table['table_id']] = fn_reports_get_conditions($_table_cond);
					}
					$_values = fn_get_report_statistics($table);
					$report['tables'][$table_id]['values'] = $_values;

					// Calculate totals
					$report['tables'][$table_id]['totals'] = array();
					foreach ($_values as $v) {
						foreach ($v as $_k => $_v) {
							$report['tables'][$table_id]['totals'][$_k] = empty($report['tables'][$table_id]['totals'][$_k]) ? $_v : ($report['tables'][$table_id]['totals'][$_k] + $_v);
						}
					}

					$_element_id =  db_get_field("SELECT element_id FROM ?:sales_reports_table_elements WHERE table_id = ?i", $table['table_id']);
					if (!empty($_element_id)) {
						$report['tables'][$table_id]['parameter'] = fn_get_lang_var("reports_parameter_$_element_id");
					}
					if (fn_is_empty($_values)) {
						$report['tables'][$table_id]['empty_values'] = 'Y';
					}
					// Find max value
					$_max = 0;
					foreach(@$_values as $kkk => $vvv) {
						foreach ($vvv as $kk => $vv) {
							if ($vv > $_max) {
								$_max = $vv;
							}
						}
					}
					$report['tables'][$table_id]['max_value'] = $_max;
					if ($table['type'] == 'B' && $intervals_limits[count($table['elements'])] < count($table['intervals'])) {
						$report['tables'][$table_id]['pages'] = ceil(count($table['intervals']) / $intervals_limits[count($table['elements'])]);
					}
				// Chart and Pie
				} elseif (!empty($table['elements']) && !empty($table['intervals']) && ($table['type'] == 'C' || $table['type'] == 'P')) {
					$_values = fn_get_report_statistics($table);
					foreach ($table['elements'] as $key => $value) {
						foreach ($_values[$value['element_hash']] as $k => $v) {
							$_new_array[$key] = array(
								'title' => $value['description'],
								'value' => $v
							);
							$__new_array[$key] = array(
								'label' => $value['description'],
								'count' => $v
							);
						}
					}
					$new_array[$table['description']] = $__new_array;
					$new_array['pie_data'] = fn_amcharts_data('pie', $_new_array);
					$new_array['pie_height'] = fn_calc_height_ampie($_new_array, 20);
					$view->assign('new_array', $new_array);
				// Bar
				} elseif (!empty($table['elements']) && !empty($table['intervals']) && ($table['type'] == 'B')) {
					$_values = fn_get_report_statistics($table);
					foreach ($table['elements'] as $key => $value) {
						foreach ($_values[$value['element_hash']] as $k => $v) {
							$_new_array[$key][$k] = array(
									'title' => $value['description'],
									'value' => $v
								);
							$__new_array[$key][$k] = array(
									'label' => $value['description'],
									'count' => $v
								);
						}
					}
					$new_array[$table['description']] = $__new_array;
					$new_array['column_data'] = fn_amcharts_data('column', $_new_array, $table['intervals']);
					$new_array['column_height'] = 450;//fn_calc_height_amcolumn($_new_array);
					if (count($table['intervals']) > 1) {
						$new_array['column_width'] = count($table['intervals']) * count($table['elements']) * 20 + 100;
						$new_array['column_width'] = ($new_array['column_width'] > 650) ? $new_array['column_width'] : 650;
					}
					$new_array['column_height'] = 450;//fn_calc_height_amcolumn($_new_array);
					$view->assign('new_array', $new_array);
				}

			}
		}

		if (!empty($table_conditions)) {
			$view->assign('table_conditions', $table_conditions);
		}
		// Periods

		$intervals = db_get_array("SELECT a.* FROM ?:sales_reports_intervals as a ORDER BY a.interval_id");

		// [Page sections]
		foreach($reports as $key => $value) {
			Registry::set('navigation.dynamic.sections.' . $key, array (
				'title' => $value['description'],
				'href' => "sales_reports.reports?report_id=$key",
				'ajax' => true
			));
		}

		Registry::set('navigation.dynamic.active_section', $report_id);

		foreach ($reports[$report_id]['tables'] as $key => $value) {
			Registry::set('navigation.tabs.table_' . $value['table_id'], array (
				'title' => $value['description'],
				'href' => "sales_reports.reports?report_id=$report_id&table_id=" . $value['table_id'],
				'ajax' => true
			));
		}
		// [/Page sections]

		$view->assign('report_id', $report_id);
		$view->assign('intervals', $intervals);
		$view->assign('table', $report['tables'][$table_id]); // FIX IT
		$view->assign('report', $report);
	}
}

// ********************************* // ********************************
if (!empty($_REQUEST['report_id'])) {
	$view->assign('report_elements', fn_get_parameters($_REQUEST['report_id']));
	$view->assign('report_id', $_REQUEST['report_id']);
}

$colors = array('pink', 'peru', 'plum', 'azure', 'aquamarine', 'blueviolet', 'firebrick', 'royalblue', 'darkgreen', 'darkorange', 'deepskyblue', 'gold ', 'darkseagreen ', 'tomato', 'wheat', 'seagreen');

$view->assign('colors', $colors);
$view->assign('depend_items', $depend_items);

?>
