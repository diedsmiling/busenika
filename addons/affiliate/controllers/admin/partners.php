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
// $Id: partners.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	fn_trusted_vars('update_data');

	if ($mode == 'm_update') {
		if (!empty($_REQUEST['update_data']) && is_array($_REQUEST['update_data'])) {
			foreach ($_REQUEST['update_data'] as $partner_id => $p_data) {
				fn_update_partner_profile($partner_id, $p_data);
			}
		}

	}

	if ($mode == 'm_approve') {
		if (!empty($_REQUEST['partner_ids'])) {
			$view_mail->assign('reason_approved', $_REQUEST['action_reason_approved']);
			foreach ($_REQUEST['partner_ids'] as $partner_id) {

				$_data = fn_get_partner_data($partner_id);

				if (empty($_data['approved']) || $_data['approved'] != 'A') {
					$p_data = array('approved' => 'A');
					fn_update_partner_profile($partner_id, $p_data);
					$user_data = fn_get_user_info($partner_id);
					$view_mail->assign('user_data', $user_data);

					// Send notification to partners
					fn_send_mail($user_data['email'], Registry::get('settings.Company.company_users_department'), 'affiliate/approved_subj.tpl', 'affiliate/approved_body.tpl');
				}
			}
		}

	}

	if ($mode == 'm_decline') {
		if (!empty($_REQUEST['partner_ids'])) {
			$view_mail->assign('reason_declined', $_REQUEST['action_reason_declined']);
			foreach ($_REQUEST['partner_ids'] as $partner_id) {

				$_data = fn_get_partner_data($partner_id);

				if (empty($_data['approved']) || $_data['approved'] != 'D') {
					$p_data = array('approved' => 'D');
					$update_result = fn_update_partner_profile($partner_id, $p_data);
					if ($update_result) {
						$user_data = fn_get_user_info($partner_id, false);
						$view_mail->assign('user_data', $user_data);

						// Send notification to partners
						fn_send_mail($user_data['email'], Registry::get('settings.Company.company_users_department'), 'affiliate/declined_subj.tpl', 'affiliate/declined_body.tpl');
					}
				}
			}
		}
	}

	return array(CONTROLLER_STATUS_REDIRECT, "partners.manage");
}

if ($mode == 'update' && !empty($_REQUEST['user_id'])) {

	$partner_data = fn_get_partner_data($_REQUEST['user_id']);

	if (empty($partner_data)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	// [Breadcrumbs]
	fn_add_breadcrumb(fn_get_lang_var('affiliates'), "partners.manage.reset_view");
	fn_add_breadcrumb(fn_get_lang_var('search_results'), "partners.manage.last_view");
	// [/Breadcrumbs]

	$titles = fn_get_static_data_section('T');
	if (!empty($titles) && is_array($titles)) {
		foreach ($titles as $t) {
			if (!empty($t['param']) && $t['param'] == @$partner_data['title']) {
				$partner_data['title_descr'] = $t['descr'];
			}
		}
	}
	$partner_data['total_payouts'] = db_get_field("SELECT SUM(amount) FROM ?:affiliate_payouts WHERE partner_id = ?i", $_REQUEST['user_id']);
	$cnt_period = Registry::if_get('addons.affiliate.number_last_periods', 10);
	$start_date = fn_get_date_of_payment_period($cnt_period);
	if (!empty($start_date)) {
		$last_payouts = array();
		$k = $cnt_period;
		$max_amount = 0;
		$cur_date = getdate(TIME);
		$checkpoint_1 = 1;
		$checkpoint_2 = 16;
		$checkpoint_return = ($cur_date['mday']<$checkpoint_2) ? false : true;
		while (!empty($k)) {
			switch (Registry::get('addons.affiliate.payment_period')) {
				case '1w':
					$end_date = $start_date + 7 * 24 * 3600;
					break;
				case '2w':
					if ($checkpoint_return) {
						$_date = getdate($start_date);
						$end_date = mktime(0, 0, 0, $_date['mon'], $checkpoint_1, $_date['year']);
						$end_date = strtotime('+1 month', $end_date);
					} else {
						$end_date = $start_date + 15 * 24 * 3600;
					}
					$checkpoint_return = !$checkpoint_return;
					break;
				case '1m':
					$end_date = strtotime("+1 month", $start_date);
					break;
			}
			$last_payouts[$k]['amount'] = db_get_field("SELECT SUM(amount) FROM ?:aff_partner_actions WHERE partner_id = ?i AND approved = 'Y' AND date >= ?i AND date < ?i", $_REQUEST['user_id'], $start_date, $end_date);
			if ($max_amount < $last_payouts[$k]['amount']) {
				$max_amount = $last_payouts[$k]['amount'];
			}
			$last_payouts[$k]['range']['start'] = $start_date;
			$last_payouts[$k]['range']['end'] = $end_date - 1;
			$start_date = $end_date;
			--$k;
		}
	}

	if (empty($max_amount)) {
		$max_amount = 1;
	}

	$view->assign('total_commissions', db_get_field("SELECT SUM(amount) FROM ?:aff_partner_actions WHERE partner_id = ?i AND approved = 'Y'", $_REQUEST['user_id']));
	$view->assign('max_amount', $max_amount);
	$view->assign('last_payouts', $last_payouts);
	$view->assign('partner', $partner_data);

	$partners = fn_get_partners_tree($_REQUEST['user_id']);
	$view->assign('partners', $partners);
	$view->assign('affiliate_plans', fn_get_affiliate_plans_list(DESCR_SL));

} elseif ($mode == 'tree') {

	// [Breadcrumbs]
	fn_add_breadcrumb(fn_get_lang_var('affiliates'), "partners.manage");
	// [/Breadcrumbs]

	$partners = fn_get_partners_tree();
	$view->assign('partners', $partners);

	$view->assign('affiliate_plans', fn_get_affiliate_plans_list(DESCR_SL));

} elseif ($mode == 'manage') {

	$params = $_REQUEST;
	$params['user_type'] = 'P';
	list($partners, $search) = fn_get_users($params, $auth, Registry::get('settings.Appearance.admin_elements_per_page'), 'affiliates');

	$view->assign('search', $search);
	$view->assign('partners', $partners);

	$view->assign('affiliate_plans', fn_get_affiliate_plans_list(DESCR_SL));
	$view->assign('countries', fn_get_countries(CART_LANGUAGE, true));
	$view->assign('states', fn_get_all_states());
	$view->assign('cur_date', TIME);
}

/** /Body **/

?>