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
// $Id: feedback.php 10491 2010-08-23 12:58:00Z imac $
//

if ( !defined('AREA') )	{ die('Access denied');	}

$fdata = array();
$fdata['tracks']['version'] = PRODUCT_VERSION;
$fdata['tracks']['type'] = PRODUCT_TYPE;
$fdata['tracks']['domain'] = Registry::get('config.http_host');

// Sales reports usage
$fdata['general']['sales_reports'] = db_get_field("SELECT COUNT(*) FROM ?:sales_reports");
$fdata['general']['sales_tables'] = db_get_field("SELECT COUNT(*) FROM ?:sales_reports_tables");
// Affiliate usage
$fdata['general']['affiliate_plans'] = db_get_field("SELECT COUNT(*) FROM ?:affiliate_plans");
// Localizations
$fdata['general']['localizations'] = db_get_field("SELECT COUNT(*) FROM ?:localizations WHERE status='A'");

// Languages usage
$fdata['languages'] = db_get_array("SELECT lang_code, status FROM ?:languages");

// Payments info. Here we get information about how many payments are used and whether surcharges were set.
$fdata['payments'] = db_get_array("SELECT payment_id, a.processor_id, processor_script, status, IF(a_surcharge<>0 OR p_surcharge<>0, 'Y', 'N') as surcharge_exists FROM ?:payments a LEFT JOIN ?:payment_processors USING(processor_id)");

// Currencies info. 
$fdata['currencies'] = db_get_array("SELECT currency_code, is_primary, decimals_separator, thousands_separator, status FROM ?:currencies");

// Exclude options that contain private information
$exclude_options = array('option_name', 'company_state', 'company_city', 'company_address', 'company_phone', 'company_phone_2', 'company_fax', 'company_name', 'company_website', 'company_zipcode', 'company_country', 'company_users_department', 'company_site_administrator', 'company_orders_department', 'company_support_department', 'company_newsletter_email', 'company_start_year', 'google_host', 'google_login', 'google_pass', 'delivery_confirmation_cost', 'delivery_confirmation_international_cost', 'account_number', 'password', 'username', 'access_key', 'username', 'password', 'username', 'mailer_smtp_host', 'mailer_smtp_auth', 'mailer_smtp_username', 'mailer_smtp_password', 'fedex_meter_name', 'fedex_meter_street', 'fedex_meter_city', 'fedex_meter_state', 'fedex_meter_zipcode', 'fedex_meter_country', 'fedex_meter_phone', 'fedex_meter_get', 'system_id', 'password', 'account_number', 'ship_key', 'additional_protection', 'intl_ship_key', 'proxy_host', 'proxy_port', 'proxy_user', 'proxy_password', 'merchant_id', 'pp_additional_insurance', 'pp_bulky_goods', 'pp_manual_processing', 'pp_cash_on_delivery', 'pc_manual_handling', 'pc_fragile', 'pc_signature', 'pc_assurance', 'pc_personal', 'pc_cash_on_delivery', 'cron_password', 'ftp_password', 'ftp_username', 'ftp_directory', 'ftp_hostname');
// Settings info
$fdata['settings'] = db_get_array("SELECT CONCAT(section_id,IF(subsection_id<>'',CONCAT('[',subsection_id,']'), ''),'.',option_name) as name, value FROM ?:settings WHERE option_name NOT IN (?a) ORDER BY name", $exclude_options);

// Users quantity
$fdata['users']['customers'] =  db_get_field("SELECT COUNT(*) FROM ?:users WHERE user_type='C' AND status='A'");
$fdata['users']['admins'] =  db_get_field("SELECT COUNT(*) FROM ?:users WHERE user_type='A' AND status='A'");
$fdata['users']['affiliates'] =  db_get_field("SELECT COUNT(*) FROM ?:users WHERE user_type='P' AND status='A'");
$fdata['users']['suppliers'] =  db_get_field("SELECT COUNT(*) FROM ?:users WHERE user_type='S' AND status='A'");
$fdata['users']['admin_usergroups'] = db_get_field("SELECT COUNT(*) FROM ?:usergroups WHERE type='A' AND status='A'");
$fdata['users']['customer_usergroups'] = db_get_field("SELECT COUNT(*) FROM ?:usergroups WHERE type='C' AND status='A'");

// Taxes info
$fdata['taxes'] = db_get_array("SELECT address_type, price_includes_tax FROM ?:taxes WHERE status='A'");

// Shippings
$fdata['shippings'] = db_get_array("SELECT rate_calculation, localization, a.service_id, carrier FROM ?:shippings a LEFT JOIN ?:shipping_services USING(service_id) WHERE a.status='A'");

// Destinations
$fdata['general']['destinations'] = db_get_field("SELECT COUNT(*) FROM ?:destinations WHERE status='A'");

// Blocks
$fdata['general']['blocks'] = db_get_field("SELECT COUNT(*) FROM ?:blocks");
$fdata['general']['block_links'] = db_get_field("SELECT COUNT(*) FROM ?:block_links");

// Images
$fdata['general']['images'] = db_get_field("SELECT COUNT(*) FROM ?:images");

// Product items
$fdata['products_stat']['total'] = db_get_field("SELECT COUNT(*) as amount FROM ?:products");
$fdata['products_stat']['prices'] = db_get_field("SELECT COUNT(*) FROM ?:product_prices");
$fdata['products_stat']['features'] = db_get_field("SELECT COUNT(*) FROM ?:product_features WHERE status='A'");
$fdata['products_stat']['features_values'] = db_get_field("SELECT COUNT(*) FROM ?:product_features_values");
$fdata['products_stat']['files'] = db_get_field("SELECT COUNT(*) FROM ?:product_files");
$fdata['products_stat']['options'] = db_get_field("SELECT COUNT(*) FROM ?:product_options");
$fdata['products_stat']['global_options'] = db_get_field("SELECT COUNT(*) FROM ?:product_options WHERE product_id='0'");
$fdata['products_stat']['option_variants'] = db_get_field("SELECT COUNT(*) FROM ?:product_option_variants");
$fdata['products_stat']['options_inventory'] = db_get_field("SELECT COUNT(*) FROM ?:product_options_inventory");
$fdata['products_stat']['configurable'] = db_get_field("SELECT COUNT(*) FROM ?:products WHERE product_type = 'C'");
$fdata['products_stat']['edp'] = db_get_field("SELECT COUNT(*) FROM ?:products WHERE is_edp = 'Y'");
$fdata['products_stat']['free_shipping'] = db_get_field("SELECT COUNT(*) FROM ?:products WHERE free_shipping = 'Y'");
$fdata['products_stat']['options_exceptions'] = db_get_field("SELECT COUNT(*) FROM ?:product_options_exceptions");
$fdata['products_stat']['filters'] = db_get_field("SELECT COUNT(*) FROM ?:product_filters WHERE status='A'");

// Promotions
$fdata['promotions'] = db_get_array("SELECT stop, zone, status FROM ?:promotions");

// Addons
$fdata['addons'] = db_get_array("SELECT addon, status, priority FROM ?:addons ORDER BY addon");

// Addon options
$allowed_addons = array('access_restrictions', 'affiliate', 'discussion', 'gift_certificates', 'gift_registry', 'google_sitemap', 'live_help', 'barcode', 'polls', 'quickbooks', 'reward_points', 'rma', 'seo', 'tags');
$_addon_options = db_get_hash_single_array("SELECT addon, options FROM ?:addons WHERE addon IN (?a)", array('addon', 'options'), $allowed_addons);
if (is_array($fdata['addons'])) {
	foreach ($fdata['addons'] as $k => $data) {
		if ($mode == 'prepare') {
			// This line is to display addon options
			if (!empty($_addon_options[$data['addon']])) {
				$fdata[fn_get_lang_var('options_for') . ' ' . $data['addon']] = unserialize($_addon_options[$data['addon']]);
			}
		} else {
			// This line is to send addon options
			$fdata['addons'][$k]['options'] = (!empty($_addon_options[$data['addon']])) ? $_addon_options[$data['addon']]: array();
		}
	}
}

if ($mode == 'prepare') {
	$view->assign("fdata", $fdata);

} elseif ($mode == 'send') {
	list($headers, $result) = fn_https_request('POST', "http://anonym.to/?http://helpdesk.cs-cart.com/index.php?target=feedback", http_build_query(array('fdata' => $fdata)), '', '', 'application/x-www-form-urlencoded', '', '', '', array('Expect: '));
	/* NULLED BY FLIPMODE! @ 2010/09/06 */
	if (!empty($_REQUEST['action']) &&  $_REQUEST['action'] == 'auto') {
		db_query("UPDATE ?:settings SET value = ?i WHERE option_name = 'send_feedback'", mktime(0,0,0,date("n")+1, date("j"), date("Y")));
	} else {
		// Even if there is any problem we do not set the error.
		fn_set_notification('N', fn_get_lang_var('notice'), fn_get_lang_var('feedback_is_sent_successfully'));
	}
	return array(CONTROLLER_STATUS_REDIRECT, "$index_script");
}

?>