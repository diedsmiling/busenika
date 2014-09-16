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
// $Id: class.registry.php 10566 2010-09-01 11:08:47Z 2tl $
//

if (!defined('AREA')) { die('Access denied'); }

class Registry {

	static private $_storage = array();
	static private $_cached_keys = array();
	static private $_changed_tables = array();
	static private $_cache_backend = '';

	/**
	 * Put variable to registry
	 *
	 * @param string $key key name
	 * @param mixed $value key value
	 * @param boolean $no_cache if set to true, data won't be cache even if it's registered in the cache
     *
	 * @return boolean always true
	 */
	static function set($key, $value, $no_cache = false)
	{
		$var = & self::_get_var_by_key($key, true);
		$var = $value;

		if ($no_cache == false && isset(self::$_cached_keys[$key]) && self::$_cached_keys[$key]['track'] == false) { // save cache immediatelly
			self::_set_cache($key, $var, self::$_cached_keys[$key]['condition'], self::$_cached_keys[$key]['cache_level']);
			unset(self::$_cached_keys[$key]);
		}

		return true;
	}


	/**
	 * Get variable from registry (value can be returned by reference)
	 *
	 * @param string $key key name
     *
	 * @return mixed key value
	 */
	static function & get($key)
	{
		return self::_get_var_by_key($key);
	}


	/**
	 * Push data to array
	 *
	 * @param string $key key name
	 * @paramN mixed values to push to the key value
     *
	 * @return boolean always true
	 */
	static function push()
	{
		$args = func_get_args();

		$data = & self::get(array_shift($args));
		if (!is_array($data)) {
			$data = array();
		}

		$data =	array_merge($data, $args);

		return true;
	}


	/**
	 * Delete key from registry
	 *
	 * @param string $key key name
     *
	 * @return boolean true if key found, false - otherwise
	 */
	static function del($key)
	{
		if ($var = & self::_get_var_by_key($key)) {
			$var = NULL;

			return true;
		}

		return false;
	}

	/**
	 * Private: get value of complex key (key.name.part)
	 *
	 * @param string $key key name
	 * @param boolean $create if true, key will be created in registry
     *
	 * @return mixed key value
	 */
	private static function & _get_var_by_key($key, $create = false)
	{
		static $_storage_cache = array();
		
		if (!isset($_storage_cache[$key])) {
			$null = NULL;
			if (strpos($key, '.') !== false) {
				$parts = explode('.', $key);
				$part = array_shift($parts);
				if (empty(self::$_storage[$part])) {
					if ($create == true) {
						self::$_storage[$part] = array();
					} else {
						$_storage_cache[$key] = $null;
						return $null;
					}
				}

				$piece = & self::$_storage[$part];
				foreach ($parts as $part) {
					if (!is_array($piece)) {
						if ($create == true) {
							$piece = array();
						} else {
							$_storage_cache[$key] = $null;
							return $null;
						}
					}

					$piece = & $piece[$part];
				}
				
				$_storage_cache[$key] = $piece;
				return $piece;
			}
		} else {
			return $_storage_cache[$key];
		}

		if (!isset(self::$_storage[$key]) && $create == true) {
			self::$_storage[$key] = array();
		}

		return self::$_storage[$key];
	}


	/**
	 * Conditional get, returns default value if key does not exist in registry
	 *
	 * @param string $key key name
	 * @param mixed $default default value
	 *
	 * @return mixed key value if exist, default value otherwise
	 */
	static function if_get($key, $default)
	{
		$var = self::_get_var_by_key($key);

		return !empty($var) ? $var : $default;
	}


	/**
	 * Check if key exists in the registry
	 *
	 * @param string $key key name
	 *
	 * @return boolean true if key exists, false otherwise
	 */
	static function is_exist($key)
	{
		$var = self::_get_var_by_key($key);

		return $var !== NULL;
	}



	/**
	 * Mark that table was changed
	 *
	 * @param string $table table name
	 *
	 * @return boolean always true
	 */
	static function set_changed_tables($table)
	{
		self::$_changed_tables[$table] = true;

		return true;
	}


	/**
	 * Register variable in the cache
	 *
	 * @param string $key key name
	 * @param mixed $condition cache reset condition - array with table names of expiration time (int)
	 * @param string $cache_level indicates the cache dependencies on controller, language, user group, etc
	 * @param bool $track if set to true, cache data will be collection during script execution and saved when it finished
	 *
	 * @return boolean true if data is cached and valid, false - otherwise
	 */
	static function register_cache($key, $condition, $cache_level = NULL, $track = false)
	{
		static $init = false;

		if ($init == false) {
			self::_cache_actions('init');
			$init = true;
		}

		if (empty(self::$_cached_keys[$key])) {
			self::$_cached_keys[$key] = array(
				'condition' => $condition,
				'cache_level' => $cache_level,
				'track' => $track,
				'hash' => ''
			);

			if (!Registry::is_exist($key) && ($val = self::_get_cache($key, $cache_level)) !== NULL) {
				Registry::set($key, $val, true);

				// Get hash of original value for tracked data
				if ($track == true) {
					self::$_cached_keys[$key]['hash'] = md5(serialize($val));
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Call cache actions - get, set, clear
	 *
	 * @param string $action action name
	 * @param array $params action parameters
	 *
	 * @return mixed data depend on action
	 */
	static function _cache_actions($action, $params = array())
	{
		if (empty(self::$_cache_backend)) {
			self::$_cache_backend = Registry::if_get('config.cache_backend', 'file');
		}

		if (!class_exists('Cache_backend_' . self::$_cache_backend)) {
			include(DIR_CORE . 'cache/class.cache_backend_' . self::$_cache_backend . '.php');

			if (!class_exists('Cache_backend_' . self::$_cache_backend)) { 
				die('Cache backend class ("' . self::$_cache_backend .'") was not found.');
			}
		}

		return call_user_func_array(array('Cache_backend_' . self::$_cache_backend, $action), $params);
	}


	/**
	 * Set cache
	 *
	 * @param string $key key name
	 * @param mixed $data cached data
	 * @param mixed $condition cache reset condition - array with table names of expiration time (int)
	 * @param string $cache_level indicates the cache dependencies on controller, language, user group, etc
	 *
	 * @return mixed data depend on action
	 */
	private static function _set_cache($key, $data, $condition, $cache_level = NULL)
	{
		return self::_cache_actions('set', array($key, $data, $condition, $cache_level));
	}

	/**
	 * Get cached data
	 *
	 * @param string $key key name
	 * @param string $cache_level indicates the cache dependencies on controller, language, user group, etc
	 *
	 * @return mixed cached data if exist, NULL otherwise
	 */
	private static function _get_cache($key, $cache_level = NULL)
	{
		$data = self::_cache_actions('get', array($key, $cache_level));

		if ($data !== false) {
			list($value) = $data;
		}

		return $data !== false ? $value : NULL;
	}

	/**
	 * Save tracked cached data and clear expired cache
	 *
	 * @return boolean always true
	 */
	static function save()
	{
		foreach (self::$_cached_keys as $key => $arg) {
			if (isset(self::$_storage[$key]) && $arg['track'] == true && $arg['hash'] != md5(serialize(self::$_storage[$key]))) {
				self::_set_cache($key, self::$_storage[$key], $arg['condition'], $arg['cache_level']);
			}
		}

		// Clear expired cache
		if (!empty(self::$_changed_tables)) {
			return self::_cache_actions('clear', array(self::$_changed_tables));
		}

		return true;

	}

	/**
	 * Clean up cache data
	 *
	 * @return boolean always true
	 */
	static function cleanup()
	{
		return self::_cache_actions('cleanup');
	}
}

?>