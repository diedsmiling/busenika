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
// $Id: class.cache_backend_sqlite.php 10502 2010-08-24 14:17:24Z zeke $
//

if (!defined('AREA')) { die('Access denied'); }

/**
 * Cache backend class, implements 4 methods: 
 * get - get data from the cache storage
 * set - set data to the cache storage
 * clear - clear expired data
 * cleanup - delete all cached data
 */
class Cache_backend_sqlite {

	static private $_cache_handlers = array();
	static private $db;

	static function set($name, $data, $condition, $cache_level = NULL)
	{
		$fname = $name . '.' . $cache_level;

		if (!empty($data)) {

			$expiry = ($cache_level == CACHE_LEVEL_TIME) ? TIME + $condition : 0;
			self::$db->query("REPLACE INTO cache (name, data, expiry, tags) VALUES ('$fname', '" . sqlite_escape_string(serialize($data)) . "', '" . $expiry . "', '$name')");

			if ($cache_level != CACHE_LEVEL_TIME) {
				foreach ($condition as $table) {
					if (empty(self::$_cache_handlers[$table])) {
						self::$_cache_handlers[$table] = array();
					}

					self::$_cache_handlers[$table][$name] = true;
				}
			}
		}

	}

	static function get($name, $cache_level = NULL)
	{
		$_lifetime = 0;
		$fname = $name . '.' . $cache_level;

		$res = self::$db->query("SELECT data, expiry FROM cache WHERE name = '$fname'" . ($cache_level == CACHE_LEVEL_TIME ? ' AND expiry > ' . TIME : ''));

		if (!empty($name) && !empty($res)) {
			$fe = $res->fetch(SQLITE_ASSOC);

			$_cache_data = (!empty($fe['data'])) ? @unserialize($fe['data']) : false;
			if ($_cache_data !== false) {
				return array($_cache_data);
			}
			
			// clean up the cache
			self::$db->query("DELETE FROM cache WHERE name = '$fname'");
		}

		return false;
	}

	static function clear($changed_tables)
	{
		$tags = array();
		foreach ($changed_tables as $table => $flag) {
			if (!empty(self::$_cache_handlers[$table])) {
				$tags = fn_array_merge($tags, array_keys(self::$_cache_handlers[$table]), false);
			}
		}

		self::$db->query("DELETE FROM cache WHERE tags IN ('" . implode("', '", $tags) ."')");

		self::$db->query("REPLACE INTO cache (name, data) VALUES ('cache_handlers', '" . sqlite_escape_string(serialize(self::$_cache_handlers)) . "')");

		return true;
	}

	static function cleanup()
	{
		return true;
	}

	static function init()
	{
		$init_db = false;
		if (!file_exists(DIR_CACHE . 'cache.db')) {
			$init_db = true;
		}

		if (!class_exists('SQLiteDatabase')) {
			die('SQLITE cache data storage is not supported. Please choose another one.');
		}

		self::$db = new SQLiteDatabase(DIR_CACHE . 'cache.db');

		if ($init_db == true) { 
			self::$db->query('CREATE TABLE cache (name varchar(128), data text, expiry int, tags varchar(64), PRIMARY KEY(name))');
			self::$db->query('CREATE INDEX tags ON cache (tags)');
			self::$db->query('CREATE INDEX exp ON cache (name, expiry)');
		}

		$res = self::$db->query("SELECT data FROM cache WHERE name = 'cache_handlers'");
		$fe = $res->fetch(SQLITE_ASSOC);
		self::$_cache_handlers = !empty($fe['data']) ? @unserialize($fe['data']) : array();

		return true;
	}
}

?>
