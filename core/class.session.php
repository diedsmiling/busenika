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
// $Id: class.session.php 10499 2010-08-24 11:51:00Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

// Custom session handling functions.

class Session {
	// session-lifetime
	static $lifetime;

	// standard php session handler
	static function open($save_path, $sess_name)
	{
		// get session-lifetime
		self::$lifetime = SESSION_ALIVE_TIME;
	}

	// standard php session handler
	static function close()
	{
		return true;
	}

	// standard php session handler
	static function read($sess_id)
	{
		$session = db_get_row('SELECT * FROM ?:sessions WHERE session_id = ?s AND area = ?s', $sess_id, AREA);

		if (empty($session) || $session['expiry'] < TIME) {

			if (!empty($session)) {
				// the session did not have time to get in "stored_sessions" and got out of date, it is necessary to return only settings
				db_query('DELETE FROM ?:sessions WHERE session_id = ?s AND area = ?s', $sess_id, AREA);
				$session = self::decode($session['data']);
				return self::encode(array ('settings' => !empty($session['settings']) ? $session['settings'] : array()));
			}

			$stored_data = db_get_field("SELECT data FROM ?:stored_sessions WHERE session_id = ?s AND area = ?s", $sess_id, AREA);

			if (!empty($stored_data)) {

				db_query('DELETE FROM ?:stored_sessions WHERE session_id = ?s AND area = ?s', $sess_id, AREA);

				$current = array();
				$_stored = self::decode($stored_data);
				$_current['settings'] = $_stored['settings'];

				return self::encode($_current);
			}

		} else {
			return $session['data'];
		}

		return false;
	}

	// standard php session handler
	static function write($sess_id, $sess_data)
	{
		return self::save($sess_id, $sess_data);
	}

	static function save($sess_id, $sess_data, $area = AREA)
	{
		static $saved = false;

		if ($saved == true) {
			return true;
		}

		// if used not by standard session handler, can accept data in array, not in serialized array
		if (is_array($sess_data)) {
			$sess_data = self::encode($sess_data);
		}

		// new session-expire-time
		$new_expire = TIME + self::$lifetime;

		$_row = array(
			'session_id' => $sess_id,
			'area' => $area,
			'expiry' => $new_expire,
			'data' => $sess_data
		);
		
		db_query("REPLACE INTO ?:sessions ?e", $_row);
		$saved = true;
		return $saved;

	}

	// we can't do just 'serialize' when we save array in session table.
	// http://ru2.php.net/session_encode
	static private function encode($data)
	{

		$raw = '' ;
		$line = 0 ;
		$keys = array_keys($data) ;

		foreach ($keys as $key) {
			$value = $data[$key] ;
			$line++;

			$raw .= $key . '|' . serialize($value);

		}

		return $raw ;

	}

	static private function decode($string)
	{
		$data = array ();

	    $vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

    	for ($i = 0; !empty($vars[$i]); $i++) {
    		$data[$vars[$i++]] = unserialize($vars[$i]);
    	}

    	return $data;
	}

	// standard php session handler
	// if you need to "logout" user you must use Session::reset_id
	static function destroy($sess_id)
	{
		db_query("DELETE FROM ?:sessions WHERE session_id = ?s", $sess_id);

		return true;
	}

	// garbage collector for old sessions
	static function gc($max_lifetime)
	{
		// Move expired sessions to sessions storage
		db_query('REPLACE INTO ?:stored_sessions SELECT * FROM ?:sessions WHERE expiry < ?i', TIME);

		$sessions = db_get_array('SELECT * FROM ?:sessions WHERE expiry < ?i', TIME);

		if ($sessions) {
			foreach ($sessions as $entry) {
				fn_log_user_logout($entry, self::decode($entry['data']));
			}

			// delete old sessions
			db_query("DELETE FROM ?:sessions WHERE expiry < ?i", TIME);
		}

		// Delete custom files (garbage) from unlogged customers
		$files = fn_get_dir_contents(DIR_CUSTOM_FILES . 'sess_data', false, true);

		if (!empty($files)) {
			foreach ($files as $file) {
				$fdate = fileatime(DIR_CUSTOM_FILES . 'sess_data/' . $file);
				
				if ($fdate < (TIME - SESSIONS_STORAGE_ALIVE_TIME)) {
					fn_rm(DIR_CUSTOM_FILES . 'sess_data/' . $file);
				}
			}
		}

		// Cleanup sessions storage
		db_query('DELETE FROM ?:stored_sessions WHERE expiry < ?i', TIME - SESSIONS_STORAGE_ALIVE_TIME);

		return true;
	}

	// get session variable name, PHPSESSID by default
	static function get_name()
	{
		return session_name();
	}

	// get current session id, smth like 32r23mfewnfiwefni32uf32ui
	static function get_id()
	{
		return session_id();
	}

	static function set_id($sess_id)
	{
		return session_id($sess_id);
	}

	// re-creates session, returns new session id
	// you can pass specific session id to use
	static function reset_id($id = null)
	{
		if ($id == self::get_id()) {
			return $id;
		}

		session_destroy();
		// session_destroy kills our handlers,
		// http://bugs.php.net/bug.php?id=32330
		// so we set them again
		self::set_handlers();
		if (!empty($id)) {
			self::set_id($id);
		}

		self::start();
		return self::get_id();
	}

	// set session handlers
	static function set_handlers()
	{
		session_set_save_handler(
			array('Session', 'open'),
			array('Session', 'close'),
			array('Session', 'read'),
			array('Session', 'write'),
			array('Session', 'destroy'),
			array('Session', 'gc')
		);
	}

	static function start()
	{
		// Force transfer session id to cookies if it passed via url
		if (!empty($_REQUEST[SESS_NAME])) {
			self::set_id($_REQUEST[SESS_NAME]);
		}

		session_name(SESS_NAME);
		session_start();

		// Session checker (for external services, returns "OK" if session exists, empty - otherwise)
		if (!empty($_REQUEST['check_session'])) {
			die(!empty($_SESSION) ? 'OK' : '');
		}

		// Validate session
		if (!defined('SKIP_SESSION_VALIDATION')) {
			$validator_data = self::get_validator_data();
			if (!isset($_SESSION['_validator_data'])) {
				$_SESSION['_validator_data'] = $validator_data;
			} else {
				if ($_SESSION['_validator_data'] != $validator_data) {
					session_regenerate_id();
					$_SESSION = array();
				}
			}
		}

		// _SESSION superglobal variable populates here, so remove it from global scope if needed
		if (fn_get_ini_param('register_globals')) {
			fn_unregister_globals('_SESSION');
		}

	}

	static function set_params()
	{
		$host = defined('HTTPS') ? Registry::get('config.https_host') : Registry::get('config.http_host');

		if (strpos($host, '.') !== false) {
			// Check if host has www prefix and remove it
			$host = strpos($host, 'www.') === 0 ? substr($host, 3) : '.' . $host;
		} else {
			// For local hosts set this to empty value
			$host = '';
		}

		ini_set('session.cookie_lifetime', SESSIONS_STORAGE_ALIVE_TIME);
		if (!preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $host, $matches)) {
			ini_set('session.cookie_domain', $host);
		}
		$current_path = Registry::get('config.current_path');
		ini_set('session.cookie_path', ((!empty($current_path))? $current_path : '/'));
		ini_set('session.gc_divisor', 10); // probability is 10% that garbage collector starts
	}

    static function get_validator_data()
    {
		$data = array();

		if (defined('SESS_VALIDATE_IP')) {
			$ip = fn_get_ip();
			$data['ip'] = $ip['host'];
		}

		if (defined('SESS_VALIDATE_UA')) {
			$data['ua'] = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		}

        return $data;
    }

	static function init()
	{
		if (!empty($_REQUEST['no_session'])) {
			fn_define('NO_SESSION', true);
		}

		if (!defined('NO_SESSION')) {
			Session::set_params();
			Session::set_handlers();
			Session::start();

			// we don't need to register shutdown function if it is ajax request,
			// because ajax request session manipulations are done in ob_handler.
			// ajax ob_handlers are lauched AFTER session_close so all session changes by ajax
			// will be unsaved.
			// so we call session_write_close() directly in our ajax ob_handler
			if (!defined('AJAX_REQUEST')) {
				register_shutdown_function('session_write_close');
			}

			return true;
		}

		return false;
	}
}

?>