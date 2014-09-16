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
// $Id: class.cache_backend_mysql.php 10516 2010-08-25 15:42:17Z zeke $
//

if (!defined('AREA')) { die('Access denied'); }

/**
 * Cache backend class, implements 4 methods: 
 * get - get data from the cache storage
 * set - set data to the cache storage
 * clear - clear expired data
 * cleanup - delete all cached data
 */
class Cache_backend_mysql {

	static private $_cache_handlers = array();

	static function set($name, $data, $condition, $cache_level = NULL)
	{
		$fname = $name . '.' . $cache_level;

		if (!empty($data)) {
			db_query("REPLACE INTO ?:cache ?e", array(
				'name' => $fname, 
				'data' => serialize($data),
				'tags' => $name,
				'expiry' => ($cache_level == CACHE_LEVEL_TIME) ? TIME + $condition : 0
			));

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
		$fname = $name . '.' . $cache_level;

		Registry::set('runtime.database.skip_cache', true);
		$expiry_condition = ($cache_level == CACHE_LEVEL_TIME) ? db_quote(" AND expiry > ?i", TIME) : '';
		$res = db_get_row("SELECT data, expiry FROM ?:cache WHERE name = ?s ?p", $fname, $expiry_condition);
		Registry::set('runtime.database.skip_cache', false);

		if (!empty($name) && !empty($res)) {
			$_cache_data = (!empty($res['data'])) ? @unserialize($res['data']) : false;
			if ($_cache_data !== false) {
				return array($_cache_data);
			}

			// clean up the cache
			db_query("DELETE FROM ?:cache WHERE name = ?s", $fname);
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

		db_query("DELETE FROM ?:cache WHERE tags IN (?a)", $tags);

		db_query("REPLACE INTO ?:cache ?e", array(
			'name' => 'cache_handlers', 
			'data' => serialize(self::$_cache_handlers)
		));

		return true;
	}


	static function cleanup()
	{
		Registry::set('runtime.database.skip_errors', true);
		db_query("TRUNCATE ?:cache");
		Registry::set('runtime.database.skip_errors', false);

		return true;
	}


	static function init()
	{
		Registry::set('runtime.database.skip_cache', true);
		if (!db_get_field("SHOW TABLES LIKE '?:cache'")) {
			Registry::set('runtime.database.skip_errors', true);
			$res = db_query('CREATE TABLE ?:cache (name varchar(128), data varchar(21649), expiry int, tags varchar(64), PRIMARY KEY(name), KEY (tags), KEY (name, expiry)) Engine=MEMORY DEFAULT CHARSET UTF8');
			Registry::set('runtime.database.skip_errors', false);
			if ($res == false) {
				die('MySQL cache data storage is not supported. Please choose another one.');
			}
		}

		$ch = db_get_field("SELECT data FROM ?:cache WHERE name = 'cache_handlers'");
		self::$_cache_handlers = !empty($ch) ? @unserialize($ch) : array();
		Registry::set('runtime.database.skip_cache', false);

		return true;	
	}
}

?>
