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
// $Id: class.cache_backend_shmem.php 10564 2010-09-01 08:45:35Z zeke $
//

if (!defined('AREA')) { die('Access denied'); }

/**
 * Cache backend class, implements 4 methods: 
 * get - get data from the cache storage
 * set - set data to the cache storage
 * clear - clear expired data
 * cleanup - delete all cached data
 */
class Cache_backend_shmem {
	static private $_id = 0;
	static private $_size = 1048576;

	static private $_cache_handlers = array();
	static private $shm;


	static function set($name, $data, $condition, $cache_level = NULL)
	{
		$fname = $name . '.' . $cache_level;

		$cache_data = shmop_read(self::$shm, self::$_id, self::$_size);
		$cache_data = @unserialize($cache_data);

		if (!empty($data)) {

			$expiry = ($cache_level == CACHE_LEVEL_TIME) ? TIME + $condition : 0;
			$cache_data[$name][$cache_level] = array(
				'data' => $data,
				'expiry' => $expiry
			);

			if ($cache_level != CACHE_LEVEL_TIME) {
				foreach ($condition as $table) {
					if (empty(self::$_cache_handlers[$table])) {
						self::$_cache_handlers[$table] = array();
					}

					self::$_cache_handlers[$table][$name] = true;
				}
			}

			shmop_write(self::$shm, serialize($cache_data), self::$_id);
		}
	}

	static function get($name, $cache_level = NULL)
	{
		$_lifetime = 0;
		$fname = $name . '.' . $cache_level;

		$_cache_data = shmop_read(self::$shm, self::$_id, self::$_size);
		$_cache_data = @unserialize($_cache_data);

		if (!empty($name) && !empty($_cache_data)) {
			if (!empty($_cache_data[$name][$cache_level]) && ($cache_level != CACHE_LEVEL_TIME || ($cache_level == CACHE_LEVEL_TIME && $_cache_data[$name][$cache_level]['expiry'] > TIME))) {
				return array($_cache_data[$name][$cache_level]['data']);

			} else { // clean up the cache
				unset($_cache_data[$name][$cache_level]);
				shmop_write(self::$shm, serialize($_cache_data), self::$_id);
			}
		}

		return false;
	}

	static function clear($changed_tables)
	{
		$cache_data = shmop_read(self::$shm, self::$_id, self::$_size);
		$cache_data = @unserialize($cache_data);

		foreach ($changed_tables as $table => $flag) {
			if (!empty(self::$_cache_handlers[$table])) {
				foreach (self::$_cache_handlers[$table] as $cache_name => $_d) {
					unset($cache_data[$cache_name]);
				}
			}
		}

		$cache_data['cache_handlers'] = self::$_cache_handlers;
		shmop_write(self::$shm, serialize($cache_data), self::$_id);

		return true;
	}

	static function cleanup()
	{
		self::init();
		shmop_delete(self::$shm);
		return true;
	}

	static function init()
	{
		if (!function_exists('shmop_open')) {
			die('Share memory cache data storage is not supported. Please choose another one.');
		}

		$ftok = ftok(__FILE__, 't');
		self::$shm = shmop_open($ftok, 'c', 0644, self::$_size);

		$cache_data = shmop_read(self::$shm, self::$_id, self::$_size);
		$cache_data = @unserialize($cache_data);

		if (!empty($cache_data) && !empty($cache_data['cache_handlers'])) {
			self::$_cache_handlers = $cache_data['cache_handlers'];
		}

		return true;
	}
}

?>
