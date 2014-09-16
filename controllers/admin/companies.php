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
// $Id: companies.php 9088 2010-03-15 10:40:51Z 2tl $
//
if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$suffix = '';

	// Define trusted variables that shouldn't be stripped
	fn_trusted_vars (
		'company_data'
	);
	
	
	//
	// Processing additon of new product element
	//
	if ($mode == 'add') {
		if (!empty($_REQUEST['company_data']['company'])) {  // Checking for required fields for new company

			// Adding company record
			
			$company_id = fn_update_company($_REQUEST['company_data']);

			if (!empty($company_id)) {
				if (PRODUCT_TYPE == 'MULTIVENDOR' && !empty($_REQUEST['company_data']['vendor_admin']) && $_REQUEST['company_data']['vendor_admin'] == 'Y') {
					// Add company's administrator
					if (fn_is_restricted_admin($_REQUEST) == true) {
						return array(CONTROLLER_STATUS_DENIED);
					}
					$user_data = array();
					
					if (!empty($_REQUEST['company_data']['admin_username'])) {
						$user_data['user_login'] = $_REQUEST['company_data']['admin_username'];
					}
					else {
						$user_data['user_login'] = $_REQUEST['company_data']['email'];
					}
					$user_data['user_type'] = 'A';
					$user_data['password1'] = fn_generate_password();
					$user_data['password2'] = $user_data['password1'];
					$user_data['status'] = $_REQUEST['company_data']['status'];
					$user_data['company_id'] = $company_id;
					$user_data['email'] = $_REQUEST['company_data']['email'];
					$user_data['company'] = $_REQUEST['company_data']['company']; 
					// Create new user, avoiding switching to the vendor admin's session ($null in the 3rd argument)
					fn_update_user(0, $user_data, $null, false, true, true);
				}
				$suffix = ".update?company_id=$company_id";
			} else {
				$suffix = ".add";
			}
		} else  {
			$suffix = ".add";
		}
	}

	//
	// Processing updating of company element
	//
	if ($mode == 'update') {
		if (!empty($_REQUEST['company_data']['company'])) {
			if (!empty($_REQUEST['company_id']) && defined('COMPANY_ID') && COMPANY_ID != $_REQUEST['company_id']) {
				fn_company_access_denied_notification();
			} else {
				// Updating company record
				fn_update_company($_REQUEST['company_data'], $_REQUEST['company_id'], DESCR_SL);
			}
		}

		$suffix = ".update?company_id=$_REQUEST[company_id]";
	}
	
	if ($mode == 'm_delete') {

		if (!empty($_REQUEST['company_ids'])) {
			foreach ($_REQUEST['company_ids'] as $v) {
				fn_delete_company($v);
			}
		}

		return array(CONTROLLER_STATUS_OK, "companies.manage");
	} 
	
	return array(CONTROLLER_STATUS_OK, "companies$suffix");
}

if ($mode == 'manage') {

	list($companies, $search) = fn_get_companies($_REQUEST, $auth, Registry::get('settings.Appearance.admin_elements_per_page'));
	
	$view->assign('companies', $companies);
	$view->assign('search', $search);

	$view->assign('countries', fn_get_countries(CART_LANGUAGE, true));
	$view->assign('states', fn_get_all_states());

} elseif ($mode == 'delete') {

	fn_delete_company($_REQUEST['company_id']);

	return array(CONTROLLER_STATUS_REDIRECT);
	
} elseif ($mode == 'update' || $mode == 'add') {

	$company_id = !empty($_REQUEST['company_id']) ? $_REQUEST['company_id'] : 0;
	$company_data = !empty($company_id) ? fn_get_company_data($company_id) : array();
	
	if ($mode == 'update' && empty($company_data)) {
		return array(CONTROLLER_STATUS_NO_PAGE);	
	}

	if (!empty($_SESSION['saved_post_data']['company_data'])) {
		foreach ((array)$_SESSION['saved_post_data'] as $k => $v) {
			$view->assign($k, $v);
		}

		$company_data = $_SESSION['saved_post_data']['company_data'];
		unset($_SESSION['saved_post_data']['company_data']);

	} else {
		$view->assign('company_data', $company_data);
	}
	
	$view->assign('countries', fn_get_countries(CART_LANGUAGE, true));
	$view->assign('states', fn_get_all_states());
	
	$manifest_definition = fn_companies_get_manifest_definition();
	$view->assign('manifest_definition', $manifest_definition);

	$view->assign('manifests', array(
		'customer' => fn_get_manifest('customer', CART_LANGUAGE, $company_id),
		'admin' => fn_get_manifest('admin', CART_LANGUAGE, $company_id),
	));

	// [Breadcrumbs]
	if (PRODUCT_TYPE == 'MULTIVENDOR') {
		$lang_var = 'vendors';
	} else {
		$lang_var = 'suppliers';
	}
	fn_add_breadcrumb(fn_get_lang_var($lang_var), 'companies.manage');
	// [/Breadcrumbs]

	// [Page sections]
	$tabs['detailed'] = array (
		'title' => fn_get_lang_var('general'),
		'js' => true
	);
	if (PRODUCT_TYPE == 'MULTIVENDOR') {
		$tabs['description'] = array (
			'title' => fn_get_lang_var('description'),
			'js' => true
		);
		$tabs['logos'] = array (
				'title' => fn_get_lang_var('logos'),
				'js' => true
		);
		$tabs['categories'] = array (
				'title' => fn_get_lang_var('categories'),
				'js' => true
		);
	};
	Registry::set('navigation.tabs', $tabs);

	if (!defined('COMPANY_ID')) {
		$shippings = db_get_hash_single_array("SELECT a.shipping_id, b.shipping FROM ?:shippings as a LEFT JOIN ?:shipping_descriptions as b ON a.shipping_id = b.shipping_id AND b.lang_code = ?s WHERE a.status = 'A' AND company_id = 0 ORDER BY a.position", array('shipping_id', 'shipping'), DESCR_SL);
		$view->assign('shippings', $shippings);

		$tabs['shipping_methods'] = array (
			'title' => fn_get_lang_var('shipping_methods'),
			'js' => true
		);
		Registry::set('navigation.tabs', $tabs);
	}
	// [/Page sections]
}

?>