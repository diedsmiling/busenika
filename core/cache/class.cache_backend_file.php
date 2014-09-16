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
// $Id: class.cache_backend_file.php 10515 2010-08-25 15:30:50Z zeke $
//

if (!defined('AREA')) { die('Access denied'); }

/**
 * Cache backend class, implements 4 methods: 
 * get - get data from the cache storage
 * set - set data to the cache storage
 * clear - clear expired data
 * cleanup - delete all cached data
 */
class Cache_backend_file {
	static private $_handlers_name = 'cache_update_handlers.csc';
	static private $_cache_handlers = array();

	static function set($name, $data, $condition, $cache_level = NULL)
	{
		if (!empty($data)) {
			if (!is_dir(DIR_CACHE . $name)) {
				fn_mkdir(DIR_CACHE . $name);
			}
			fn_put_contents(DIR_CACHE . $name . '/' . $cache_level . '.csc', serialize(array(
				'data' => $data,
				'expiry' => $cache_level == CACHE_LEVEL_TIME ? TIME + $condition : 0
			)));

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
		$fname = $name . '/' . $cache_level . '.csc';

		if (!empty($name) && is_readable(DIR_CACHE . $fname)) {
			$_cache_data = @unserialize(fn_get_contents(DIR_CACHE . $fname));
			if (!empty($_cache_data) && ($cache_level != CACHE_LEVEL_TIME || ($cache_level == CACHE_LEVEL_TIME && $_cache_data['expiry'] > TIME))) {

				return array($_cache_data['data']);

			} else { // clean up the cache
				fn_rm(DIR_CACHE . $fname);
			}
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

		foreach ($tags as $tag) {
			fn_rm(DIR_CACHE . $tag, true);
		}

		fn_put_contents(DIR_CACHE . self::$_handlers_name, serialize(
			self::$_cache_handlers
		));

		return true;
	}

	static function cleanup()
	{
		return true;
	}

	static function init()
	{
		if (!is_dir(DIR_CACHE)) {
			fn_mkdir(DIR_CACHE);
		}

		$ch = fn_get_contents(DIR_CACHE . self::$_handlers_name);
		self::$_cache_handlers = !empty($ch) ? @unserialize($ch) : array();

		return true;
	}

}

?>