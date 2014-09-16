<?php

//
// $Id: actions.post.php 7502 2009-05-19 14:54:59Z zeke $
//
if ( !defined('AREA') ) { die('Access denied'); }

/**
 * Reverse IP filter
 */
function fn_settings_actions_addons_access_restrictions_admin_reverse_ip_access(&$new_value, $old_value)
{
	$ip = fn_get_ip(true);

	if ($new_value == 'Y') {
		
		$ip_data = db_get_row("SELECT item_id, status FROM ?:access_restriction WHERE ip_from = ?i AND ip_to = ?i AND type IN ('aas', 'aab', 'aar')", $ip['host'], $ip['host']);

		if (empty($ip_data) || empty($ip_data['item_id'])) {	// Add IP
			$restrict_ip = array (
				'ip_from' => $ip['host'],
				'ip_to' => $ip['host'],
				'type' => 'aas',
				'timestamp' => TIME,
				'expires' => '0',
				'status' => 'A'
			);

			$__data = array();
			$__data['item_id'] = db_query("REPLACE INTO ?:access_restriction ?e", $restrict_ip);
			$__data['type'] = 'aas';

			foreach ((array)Registry::get('languages') as $__data['lang_code'] => $_v) {
				$__data['reason'] = fn_get_lang_var('store_admin', $__data['lang_code']);
				db_query("REPLACE INTO ?:access_restriction_reason_descriptions ?e", $__data);
			}
			
			fn_set_notification('W', fn_get_lang_var('warning'), str_replace('[ip]', long2ip($ip['host']), fn_get_lang_var('your_ip_added')));

		} elseif (empty($ip_data['status']) || $ip_data['status'] != 'A') { // Change IP status to available
			
			db_query("UPDATE ?:access_restriction SET ?u WHERE item_id = ?i", array('status' => 'A'), $ip_data['item_id']);
			fn_set_notification('W', fn_get_lang_var('warning'), str_replace('[ip]', long2ip($ip['host']), fn_get_lang_var('your_ip_enabled')));
		}
		
	} else {	// Delete IP
		
		$ips_data = db_get_array("SELECT item_id, type FROM ?:access_restriction WHERE ip_from <= ?i AND ip_to >= ?i AND type IN ('aas', 'aab', 'aar')", $ip['host'], $ip['host']);

		if (!empty($ips_data)) {
			foreach ($ips_data as $ip_data) {
				db_query("DELETE FROM ?:access_restriction WHERE item_id = ?i", $ip_data['item_id']);
				db_query("DELETE FROM ?:access_restriction_reason_descriptions WHERE item_id = ?i AND type = ?s", $ip_data['item_id'], $ip_data['type']);
			}
			fn_set_notification('W', fn_get_lang_var('warning'), str_replace('[ip]', long2ip($ip['host']), fn_get_lang_var('your_ip_removed')));
		}
		
	}

	return true;
}

?>