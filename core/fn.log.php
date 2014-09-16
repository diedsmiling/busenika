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
// $Id: fn.log.php 10292 2010-08-02 10:22:33Z andyye $
//

if ( !defined('AREA') )	{ die('Access denied');	}

fn_define('LOG_MAX_DATA_LENGTH', 10000);

function fn_log_event($type, $action, $data = array())
{
	$update = false;
	$content = array();

	$actions = Registry::get('settings.Logging.log_type_' . $type);

	$cut_data = Registry::if_get('log_cut_data', false);
	Registry::del('log_cut_data');

	if (empty($actions) || ($action && !empty($actions) && empty($actions[$action]))) {
		return false;
	}

	if (!empty($_SESSION['auth']['user_id'])) {
		$user_id = $_SESSION['auth']['user_id'];
	} else {
		$user_id = 0;
	}

	if ($type == 'users' && $action == 'logout' && !empty($data['user_id'])) {
		$user_id = $data['user_id'];
	}

	if ($user_id) {
		$udata = db_get_row("SELECT firstname, lastname, email FROM ?:users WHERE user_id = ?i", $user_id);
	}

	$event_type = 'N'; // notice

	if (!empty($data['backtrace'])) {
		$_btrace = array();
		$func = '';
		foreach (array_reverse($data['backtrace']) as $v) {
			if (empty($v['file'])) {
				$func = $v['function'];
				continue;
			} elseif (!empty($func)) {
				$v['function'] = $func;
				$func = '';
			}

			$_btrace[] = array(
				'file' => !empty($v['file']) ? $v['file'] : '',
				'line' => !empty($v['line']) ? $v['line'] : '',
				'function' => $v['function'],
			);
		}

		$data['backtrace'] = serialize($_btrace);
	} else {
		$data['backtrace'] = '';
	}

	if ($type == 'orders') {

		$order_status_descr = fn_get_statuses(STATUSES_ORDER, true, true, true);

		$content = array (
			'order' => '# ' . $data['order_id'],
		);

		if ($action == 'status') {
			$content['status'] = $order_status_descr[$data['status_from']] . ' -> ' . $order_status_descr[$data['status_to']];
		}


	} elseif ($type == 'products') {

		$product = db_get_field("SELECT product FROM ?:product_descriptions WHERE product_id = ?i AND lang_code = ?s", $data['product_id'], Registry::get('settings.Appearance.admin_default_language'));
		$content = array (
			'product' => $product . ' (#' . $data['product_id'] . ')',
		);

		if ($action == 'low_stock') { // log stock - warning
			$event_type = 'W';
		}

	} elseif ($type == 'categories') {

		$category = db_get_field("SELECT category FROM ?:category_descriptions WHERE category_id = ?i AND lang_code = ?s", $data['category_id'], Registry::get('settings.Appearance.admin_default_language'));
		$content = array (
			'category' => $category . ' (#' . $data['category_id'] . ')',
		);

	} elseif ($type == 'database') {
		if ($action == 'error') {
			$content = array (
				'error' => $data['error']['message'],
				'query' => $data['error']['query'],
			);
			$event_type = 'E';
		}

	} elseif ($type == 'requests') {

		$long_data = (strlen($data['response']) > LOG_MAX_DATA_LENGTH);

		if (!empty($cut_data)) {
			$data['data'] = preg_replace("/\<(" . implode('|', $cut_data) . ")\>(.*?)\<\/(" . implode('|', $cut_data) . ")\>/s", '<${1}>******</${1}>', $data['data']);
			$data['data'] = preg_replace("/(" . implode('|', $cut_data) . ")=(.*?)(&)/s", '${1}=******${3}', $data['data']);
		}

		$content = array (
			'url' => $data['url'],
			'request' => $data['data'],
			'response' => ($long_data == false && preg_match('//u',$data['response'])) ? $data['response'] : '',
		);

	} elseif ($type == 'users') {

		if (!empty($data['time'])) {
			if (empty($_SESSION['log']['login_log_id'])) {
				return false;
			}

			$content = db_get_field('SELECT content FROM ?:logs WHERE log_id = ?i', $_SESSION['log']['login_log_id']);
			$content = unserialize($content);

			$minutes = ceil($data['time'] / 60);

			$hours = floor($minutes / 60);

			if ($hours) {
				$minutes -= $hours * 60;
			}

			if ($hours || $minutes) {
				$content['loggedin_time'] = ($hours ? $hours . ' |hours| ': '') . ($minutes ? $minutes . ' |minutes|' : '');
			}

			if (!empty($data['timeout']) && $data['timeout']) {
				$content['timeout'] = true;
			}

			$update = $_SESSION['log']['login_log_id'];

		} else {
			if (!empty($data['user_id'])) {
				$info = db_get_row("SELECT firstname, lastname, email FROM ?:users WHERE user_id = ?i", $data['user_id']);
				$content = array (
					'user' => $info['firstname'] . ($info['firstname'] || $info['lastname'] ? ' ' : '' ) . $info['lastname'] . '; ' . $info['email'] . ' (#' . $data['user_id'] . ')',
				);
			} elseif (!empty($data['user'])) {
				$content = array (
					'user' => $data['user'],
				);
			}

			if (in_array($action, array ('session', 'failed_login'))) {
				$ip = fn_get_ip();
				$content['ip_address'] = empty($data['ip']) ? $ip['host'] : $data['ip'];
			}
		}

		if ($action == 'failed_login') { // failed login - warning
			$event_type = 'W';
		}
	}

	fn_set_hook('save_log', $type, $action, $data, $user, $content, $event_type);

	$content = serialize($content);
	if ($update) {
		db_query('UPDATE ?:logs SET content = ?s WHERE log_id = ?i', $content, $update);

	} else {
		$row = array (
			'user_id' => $user_id,
			'timestamp' => TIME,
			'type' => $type,
			'action' => $action,
			'event_type' => $event_type,
			'content' => $content,
			'backtrace' => $data['backtrace']
		);

		$log_id = db_query("INSERT INTO ?:logs ?e", $row);

		if ($type = 'users' && $action == 'session') {
			$_SESSION['log']['login_log_id'] = $log_id;
		}
	}

	return true;
}

/**
 * Add a record to the log if the user session is expired
 *
 * @param array $entry - session record
 */
function fn_log_user_logout($entry, $data)
{
	if (!empty($data['auth']) && $data['auth']['user_id']) {
		$this_login = empty($data['auth']['this_login']) ? 0 : $data['auth']['this_login'];

		// Log user logout
		fn_log_event('users', 'session', array (
			'user_id' => $data['auth']['user_id'],
			'ip' => empty($data['auth']['ip']) ? '' : $data['auth']['ip'],
			'time' => ($entry['expiry'] - $this_login),
			'timeout' => true,
			'expiry' => $entry['expiry']
		));
	}

	return true;
}

?>