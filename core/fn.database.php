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
// $Id: fn.database.php 10478 2010-08-20 13:10:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

/**
 * Execute query and format result as associative array with column names as keys
 *
 * @param string $query unparsed query
 * @param mixed ... unlimited number of variables for placeholders
 * @return array structured data
 */
function db_get_array($query)
{
	$args = func_get_args();
	
	if ($cached_result = db_get_cached_result($query, $args)) {
		return $cached_result;
	}

	if ($_result = call_user_func_array('db_query', $args)) {

		while ($arr = driver_db_fetch_array($_result)) {
			$result[] = $arr;
		}

		driver_db_free_result($_result);
		
		if (Registry::get('runtime.database.long_query') && !empty($result)) {
			db_cache_query($query, $result, $args);
		}
	}

	return !empty($result) ? $result : array();
}

/**
 * Execute query and format result as associative array with column names as keys and index as defined field
 *
 * @param string $query unparsed query
 * @param string $field field for array index
 * @param mixed ... unlimited number of variables for placeholders
 * @return array structured data
 */
function db_get_hash_array($query, $field)
{
	$args = array_slice(func_get_args(), 2);
	array_unshift($args, $query);

	if ($cached_result = db_get_cached_result($query, $args)) {
		return $cached_result;
	}

	if ($_result = call_user_func_array('db_query', $args)) {
		while ($arr = driver_db_fetch_array($_result)) {
			if (isset($arr[$field])) {
				$result[$arr[$field]] = $arr;
			}
		}

		driver_db_free_result($_result);

		if (Registry::get('runtime.database.long_query') && !empty($result)) {
			db_cache_query($query, $result, $args);
		}
	}

	return !empty($result) ? $result : array();
}

/**
 * Execute query and format result as associative array with column names as keys and then return first element of this array
 *
 * @param string $query unparsed query
 * @param mixed ... unlimited number of variables for placeholders
 * @return array structured data
 */
function db_get_row($query)
{
	$args = func_get_args();

	if ($cached_result = db_get_cached_result($query, $args)) {
		return $cached_result;
	}

	if ($_result = call_user_func_array('db_query', $args)) {

		$result = driver_db_fetch_array($_result);

		driver_db_free_result($_result);

		if (Registry::get('runtime.database.long_query')) {
			db_cache_query($query, $result, $args);
		}

	}

	return is_array($result) ? $result : array();
}

/**
 * Execute query and returns first field from the result
 *
 * @param string $query unparsed query
 * @param mixed ... unlimited number of variables for placeholders
 * @return array structured data
 */
function db_get_field($query)
{
	$args = func_get_args();

	if ($cached_result = db_get_cached_result($query, $args)) {
		return $cached_result;
	}

	if ($_result = call_user_func_array('db_query', $args)) {
	
		$result = driver_db_fetch_row($_result);

		driver_db_free_result($_result);

		if (Registry::get('runtime.database.long_query')) {
			db_cache_query($query, is_array($result) ? $result[0] : NULL, $args);
		}

	}

	return is_array($result) ? $result[0] : NULL;
}

/**
 * Execute query and format result as set of first column from all rows
 *
 * @param string $query unparsed query
 * @param mixed ... unlimited number of variables for placeholders
 * @return array structured data
 */
function db_get_fields($query)
{
	$args = func_get_args();

	if ($cached_result = db_get_cached_result($query, $args)) {
		return $cached_result;
	}

	if ($__result = call_user_func_array('db_query', $args)) {

		$_result = array();
		while ($arr = driver_db_fetch_array($__result)) {
			$_result[] = $arr;
		}

		driver_db_free_result($__result);

		if (is_array($_result)) {
			$result = array();
			foreach ($_result as $k => $v) {
				array_push($result, reset($v));
			}
		}

		if (Registry::get('runtime.database.long_query')) {
			db_cache_query($query, $result, $args);
		}

	}

	return is_array($result) ? $result : array();
}

/**
 * Execute query and format result as one of: field => array(field_2 => value), field => array(field_2 => row_data), field => array([n] => row_data)
 *
 * @param string $query unparsed query
 * @param array $params array with 3 elements (field, field_2, value)
 * @param mixed ... unlimited number of variables for placeholders
 * @return array structured data
 */
function db_get_hash_multi_array($query, $params)
{
	@list($field, $field_2, $value) = $params;

	$args = array_slice(func_get_args(), 2);
	array_unshift($args, $query);

	if ($cached_result = db_get_cached_result($query, $args)) {
		return $cached_result;
	}

	if ($_result = call_user_func_array('db_query', $args)) {
		while ($arr = driver_db_fetch_array($_result)) {
			if (!empty($field_2)) {
				$result[$arr[$field]][$arr[$field_2]] = !empty($value) ? $arr[$value] : $arr;
			} else {
				$result[$arr[$field]][] = $arr;
			}
		}

		driver_db_free_result($_result);

		if (Registry::get('runtime.database.long_query')) {
			db_cache_query($query, $result, $args);
		}

	}

	return !empty($result) ? $result : array();
}

/**
 * Execute query and format result as key => value array
 *
 * @param string $query unparsed query
 * @param array $params array with 2 elements (key, value)
 * @param mixed ... unlimited number of variables for placeholders
 * @return array structured data
 */
function db_get_hash_single_array($query, $params)
{
	@list($key, $value) = $params;

	$args = array_slice(func_get_args(), 2);
	array_unshift($args, $query);

	if ($cached_result = db_get_cached_result($query, $args)) {
		return $cached_result;
	}

	if ($_result = call_user_func_array('db_query', $args)) {
		while ($arr = driver_db_fetch_array($_result)) {
			$result[$arr[$key]] = $arr[$value];
		}

		driver_db_free_result($_result);

		if (Registry::get('runtime.database.long_query')) {
			db_cache_query($query, $result, $args);
		}
	}

	return !empty($result) ? $result : array();
}

/**
 * Execute query
 *
 * @param string $query unparsed query
 * @param mixed ... unlimited number of variables for placeholders
 * @return boolean always true, dies if problem occured
 */

function db_query($query)
{
	Registry::set('runtime.database.long_query', false);

	$args = func_get_args();
	$query = db_process($query, array_slice($args, 1));


	
	if (empty($query)) {
		return false;
	}

	if (defined('DEBUG_QUERIES')) {
		fn_print_r($query);
	}
	
	$time_start = microtime(true);
	$result = driver_db_query($query);
	$time_exec = microtime(true) - $time_start;
	
	if (defined('PROFILER')) {
		Profiler::set_query($query, $time_exec);
	}
		
	// Check if query updates data in the database

	if ($time_exec > 30.003) {
		Registry::set('runtime.database.long_query', true);
		Registry::set('runtime.database.last_query', $query);
	}

	if ($result === true) { // true returns for success insert/update/delete query
	
		// Check if it was insert statement with auto_increment value
		if (Registry::is_exist('revisions') && ($i_id = Registry::get('revisions.db_insert_id')) && !Registry::get('revisions.working')) {
			Registry::set('revisions.db_insert_id', null);
			return $i_id;

		} elseif ($i_id = driver_db_insert_id()) {
			return $i_id;
		}
	}

	db_error($result, $query);

	return $result;
}

/**
 * Parse query and replace placeholders with data
 *
 * @param string $query unparsed query
 * @param mixed ... unlimited number of variables for placeholders
 * @return parsed query
 */
function db_quote()
{
	$args = func_get_args();
	$pattern = array_shift($args);

	return db_process($pattern, $args, false);
}

/**
 * Parse query and replace placeholders with data
 *
 * @param string $query unparsed query
 * @param array $data data for placeholders
 * @return parsed query
 */
function db_process($pattern, $data = array(), $replace = true)
{
	static $session_vars_updated = false;
	$command = 'get';
	$group_concat_len = 3000; // 3Kb

	// Check if query updates data in the database
	if (preg_match("/^(UPDATE|INSERT INTO|REPLACE INTO|DELETE FROM) \?\:(\w+) /", $pattern, $m)) {
		$table_name = $m[2];//str_replace(TABLE_PREFIX, '', $m[2]);
		Registry::set_changed_tables($table_name);

		Registry::register_cache('cached_queries', array(), CACHE_LEVEL_STATIC, true);
		$cached_queries = Registry::if_get('cached_queries', array());	

		if (!empty($cached_queries)) {
			foreach ($cached_queries as $cquery => $ctables) {
				if (in_array($table_name, $ctables)) {
					unset($cached_queries[$cquery]);
				}
			}
			Registry::set('cached_queries', (empty($cached_queries) ? array(0 => array()) : $cached_queries));
		}

		$command = ($m[1] == 'DELETE FROM') ? 'delete' : 'set';
		
	}

	if (strpos($pattern, 'GROUP_CONCAT(') !== false && $session_vars_updated == false) {
		db_query('SET SESSION group_concat_max_len = ?i', $group_concat_len);
		$session_vars_updated = true;
	}

	if (!empty($data) && preg_match_all("/\?(i|s|l|d|a|n|u|e|p|w|f)+/", $pattern, $m)) {
		$offset = 0;
		foreach ($m[0] as $k => $ph) {
			if ($ph == '?u' || $ph == '?e') {
				$data[$k] = fn_check_table_fields($data[$k], $table_name);

				if (empty($data[$k])) {
					return false;
				}
			}

			if ($ph == '?i') { // integer
				$pattern = db_str_replace($ph, db_intval($data[$k]), $pattern, $offset); // Trick to convert int's and longint's

			} elseif ($ph == '?s') { // string
				$pattern = db_str_replace($ph, "'" . addslashes($data[$k]) . "'", $pattern, $offset);

			} elseif ($ph == '?l') { // string for LIKE operator
				$pattern = db_str_replace($ph, "'" . addslashes(str_replace("\\", "\\\\", $data[$k])) . "'", $pattern, $offset);

			} elseif ($ph == '?d') { // float
				$pattern = db_str_replace($ph, sprintf('%01.2f', $data[$k]), $pattern, $offset);

			} elseif ($ph == '?a') { // array FIXME: add trim
				$data[$k] = !is_array($data[$k]) ? array($data[$k]) : $data[$k];
				$pattern = db_str_replace($ph, "'" . implode("', '", array_map('addslashes', $data[$k])) . "'", $pattern, $offset);

			} elseif ($ph == '?n') { // array of integer FIXME: add trim
				$data[$k] = !is_array($data[$k]) ? array($data[$k]) : $data[$k];
				$pattern = db_str_replace($ph, !empty($data[$k]) ? implode(', ', array_map('db_intval', $data[$k])) : "''", $pattern, $offset);

			} elseif ($ph == '?u' || $ph == '?w') { // update/condition with and
				$q = '';
				$clue = ($ph == '?u') ? ', ' : ' AND ';
				foreach($data[$k] as $field => $value) {
					$q .= ($q ? $clue : '') . '`' . db_field($field) . "` = '" . addslashes($value) . "'";
				}
				$pattern = db_str_replace($ph, $q, $pattern, $offset);

			} elseif ($ph == '?e') { // insert
				$pattern = db_str_replace($ph, '(`' . implode('`, `', array_map('addslashes', array_keys($data[$k]))) . "`) VALUES ('" . implode("', '", array_map('addslashes', array_values($data[$k]))) . "')", $pattern, $offset);

			} elseif ($ph == '?f') { // field/table/database name
				$pattern = db_str_replace($ph, db_field($data[$k]), $pattern, $offset);

			} elseif ($ph == '?p') { // prepared statement
//				$pattern = db_str_replace($ph, str_replace('?:', TABLE_PREFIX, $data[$k]), $pattern, $offset);
				$pattern = db_str_replace($ph, $data[$k], $pattern, $offset);
			}
		}
	}

	if ($replace) {

		if (Registry::is_exist('revisions') && !Registry::get('revisions.working')) {
			if (strpos($pattern, 'SELECT') === 0) {
				fn_revisions_process_select($pattern);
			}

			if (strpos($pattern, 'UPDATE') === 0) {
				fn_revisions_process_update($pattern);
			}

			if ((strpos($pattern, 'INSERT') === 0 || strpos($pattern, 'REPLACE') === 0)) {
				Registry::set('revisions.db_insert_id', 0);
				fn_revisions_process_insert($pattern);
			}

			if (strpos($pattern, 'DELETE') === 0) {
				fn_revisions_process_delete($pattern);
			}
		}

		// Replace table prefixes
		$pattern = str_replace('?:', TABLE_PREFIX, $pattern);	
	}

	return $pattern;
}

/**
 * Placeholder replace helper
 *
 * @param string $needle string to replace
 * @param string $replacement replacement
 * @param string $subject string to search for replace
 * @param int $offset offset to search from
 * @return string with replaced fragment
 */
function db_str_replace($needle, $replacement, $subject, &$offset)
{
	$pos = strpos($subject, $needle, $offset);
	$offset = $pos + strlen($replacement);
	return substr_replace($subject, $replacement, $pos, 2);
}

/**
 * Convert variable to int/longint type
 *
 * @param mixed $int variable to convert
 * @return mixed int/intval variable
 */
function db_intval($int)
{
	return $int + 0;
}

/**
 * Check if variable is valid database table name, table field or database name
 *
 * @param mixed $int variable to convert
 * @return mixed int/intval variable
 */
function db_field($field)
{
	if (preg_match("/([\w]+)/", $field, $m) && $m[0] == $field) {
		return $field;
	}

	return '';
}

/**
 * Get column names from table
 *
 * @param string $table_name table name
 * @param array $exclude optional array with fields to exclude from result
 * @param boolean $wrap_quote optional parameter, if true, the fields will be enclosed in quotation marks
 * @return array columns array
 */
function fn_get_table_fields($table_name, $exclude = array(), $wrap = false)
{	
	static $table_fields_cache = array();
	
	if (!isset($table_fields_cache[$table_name])) {
		$table_fields_cache[$table_name] = db_get_fields("SHOW COLUMNS FROM ?:$table_name");
	}
	
	$fields = $table_fields_cache[$table_name];
	if (!$fields) {
		return false;
	}
	
	if ($exclude) {
		$fields = array_diff($fields, $exclude);	
	}
	
	if ($wrap) {
		foreach($fields as &$v) {
			$v = "`$v`"; 	
		}
	}
	
	return $fields;
}

/**
 * Check if passed data corresponds columns in table and remove unnecessary data
 *
 * @param array $data data for compare
 * @param array $table_name table name
 * @return mixed array with filtered data or false if fails
 */
function fn_check_table_fields($data, $table_name)
{
	$_fields = fn_get_table_fields($table_name);
	if (is_array($_fields)) {
		foreach ($data as $k => $v) {
			if (!in_array($k, $_fields)) {
				unset($data[$k]);
			}
		}
		if (func_num_args() > 2) {
			for ($i = 2; $i < func_num_args(); $i++) {
				unset($data[func_get_arg($i)]);
			}
		}
		return $data;
	}
	return false;
}

/**
 * Remove value from set (e.g. remove 2 from "1,2,3" results in "1,3")
 *
 * @param string $field table field with set
 * @param string $value value to remove
 * @return string database construction for removing value from set
 */
function fn_remove_from_set($field, $value)
{
	return db_quote("TRIM(BOTH ',' FROM REPLACE(CONCAT(',', $field, ','), CONCAT(',', ?s, ','), ','))", $value);
}

/**
 * Add value to set (e.g. add 2 from "1,3" results in "1,3,2")
 *
 * @param string $field table field with set
 * @param string $value value to add
 * @return string database construction for add value to set
 */
function fn_add_to_set($field, $value)
{
	return db_quote("TRIM(BOTH ',' FROM CONCAT_WS(',', ?p, ?s))", fn_remove_from_set($field, $value), $value);
}

/**
 * Create set from php array
 *
 * @param array $set_data values array
 * @return string database construction for creating set
 */
function fn_create_set($set_data = array())
{
	return empty($set_data) ? '' : implode(',', $set_data);
}

function fn_find_array_in_set($arr, $set, $find_empty = false)
{
	$conditions = array();
	if ($find_empty) {
		$conditions[] = "$set = ''";
	}
	if (!empty($arr)) {
		foreach ($arr as $val) {
			$conditions[] = db_quote("FIND_IN_SET(?i, $set)", $val);
		}
	}

	return empty($conditions) ? '' : implode(' OR ', $conditions);
}

/**
 * Display database error
 *
 * @param resource $result result, returned by database server
 * @param string $query SQL query, passed to server
 * @return mixed false if no error, dies with error message otherwise
 */
function db_error($result, $query)
{
	if (!empty($result) || driver_db_errno() == 0) {
		// it's ok
	} else {
		$error = array (
			'message' => driver_db_error() . ' <b>(' . driver_db_errno() . ')</b>',
			'query' => $query,
		);

		if (Registry::get('runtime.database.skip_errors') == true) {
			Registry::push('runtime.database.errors', $error);
		} else {
			fn_error(debug_backtrace(), $error);
		}
	}

	return false;
}

/**
 * Connect to database server and select database
 *
 * @param string $host database host
 * @param string $user database user
 * @param string $password database password
 * @param string $name database name
 * @return resource database connection identifier, false if error occured
 */
function db_initiate($host, $user, $password, $name)
{
	global $db_conn;
	driver_db_close();
	$db_conn = driver_db_connect($host, $user, $password);
	if (!empty($db_conn)) {
		db_query("SET NAMES 'utf8'");
		Registry::set('runtime.database.skip_errors', false);
		return driver_db_select($name) ? $db_conn : false;
	}

	return false;
}

/**
 * Get the number of found rows from the last query
 * 
 */
function db_get_found_rows()
{

	if (Registry::get('runtime.database.long_query')) {

		$last_query = Registry::get('runtime.database.last_query');
		preg_match_all("/".TABLE_PREFIX."(\w+)/", $last_query, $m);
		Registry::register_cache('cquery_count_' . md5($last_query), array_unique($m[1]), CACHE_LEVEL_STATIC);

		if (Registry::is_exist('cquery_count_' . md5($last_query)) == true) {
			$count = Registry::get('cquery_count_' . md5($last_query));
		}else {
			$count = db_get_field("SELECT FOUND_ROWS()");
			Registry::set('cquery_count_' . md5($last_query), $count);	
		}
	} else {
		$count = db_get_field("SELECT FOUND_ROWS()");
	}

	return $count;

}

/**
 * Get cached resul from query
 * 
 * @param string $query cached query
 */
function db_get_cached_result($query, $args)
{
	return array();
			$memc = new Memcache;
$memc->connect('127.0.0.1', 11211) or die ("Could not connect");
	if($memc == null){
		//return false;
	}
	else{
		$cache_name = serialize(array('query' => $query, 'args' => $args));
		$key = "c".md5($cache_name);
		$var_key = $memc->get($key);
		$result = unserialize($var_key);
		//var_dump("111111".$key);
		//if(!empty($var_key)) var_dump(11111111);
		return !empty($result) ? $result : array();
	}
	$memc->close();
	
	
	if (Registry::get('runtime.database.skip_cache') != true) {
		$cache_name = serialize(array('query' => $query, 'args' => $args));
		Registry::register_cache('cached_queries', array(), CACHE_LEVEL_STATIC, true);
		$cached_queries = Registry::if_get('cached_queries', array());	

		if (!empty($cached_queries['cquery_' . md5($cache_name)])) {
			Registry::register_cache('cquery_' . md5($cache_name), array(), CACHE_LEVEL_STATIC);
		
			if (Registry::is_exist('cquery_' . md5($cache_name)) == true) {
				$result = Registry::get('cquery_' . md5($cache_name));
				$query = db_process($query, array_slice($args, 1));
				Registry::set('runtime.database.last_query', $query);
				Registry::set('runtime.database.long_query', true);
			}
		}
	}

	return !empty($result) ? $result : array();

}

/**
 * Cache query
 * 
 * @param string $query query which must be cached
 * @param string $value value received after the query was fulfilled
 */
function db_cache_query($query, $value, $args)
{

	$memc = new Memcache;
$memc->connect('127.0.0.1', 11211) or die ("Could not connect");
	
	$cache_name = serialize(array('query' => $query, 'args' => $args));

	preg_match_all("/\?:(\w+)/", $query, $m);

	$key = "c".md5($cache_name);
	$memc->set($key, serialize($value), false, 60); 
	$memc->close();
	return $value;



	Registry::register_cache('cquery_' . md5($cache_name), array_unique($m[1]), CACHE_LEVEL_STATIC);
	Registry::set('cquery_' . md5($cache_name), $value);
	Registry::register_cache('cached_queries', array(), CACHE_LEVEL_STATIC, true);

	$cached_queries = Registry::if_get('cached_queries', array());	

	if (!isset($cached_queries['cquery_' . md5($cache_name)])) {
		$cache_handler = array (
				'cquery_' . md5($cache_name) => array_unique($m[1]),
			);
		$cached_queries = array_merge($cached_queries, $cache_handler);
		Registry::set('cached_queries', $cached_queries);
	}

	return $value;
}

/**
 * Fuctnions parses SQL file and import data from it
 *
 * @param string $file File for import
 * @param integer $buffer Buffer size for fread function
 * @param booleand $show_status Show or do not show process by printing ' .'
 * @param integer $show_create_table 0 - Do not print the name of created table, 1 - Print name and get lang_var('create_table'), 2 - Print name without getting lang_var
 * @param boolean $check_prefix Check table prefix and replace it with the installed in config.php
 * @param boolean $track Use queries cache. Do not execute queries that already are executed.
 * @return false, if file is not accessible
 */
function db_import_sql_file($file, $buffer = 16384, $show_status = true, $show_create_table = 1, $check_prefix = false, $track = false, $skip_errors = false)
{
	if (file_exists($file)) {
		
		$path = dirname($file);
		$file_name = basename($file);
		$tmp_file = $path . "/$file_name.tmp";

		$executed_queries = array();
		if ($track && file_exists($tmp_file)) {
			$executed_queries = unserialize(fn_get_contents($tmp_file));
		}

		if ($skip_errors) {
			$_skip_errors = Registry::get('runtime.database.skip_errors');
			Registry::set('runtime.database.skip_errors', true);
		}

		$fd = fopen($file, 'r');
		if ($fd) {
			$ret = array();
			$rest = '';
			while (!feof($fd)) {
				$str = $rest.fread($fd, $buffer);
				$rest = fn_parse_queries($ret, $str);

				if (!empty($ret)) {
					foreach ($ret as $query) {
						if (!in_array($query, $executed_queries)) {
							if ($show_create_table && preg_match('/CREATE\s+TABLE\s+`?(\w+)`?/i', $query, $matches)) {
								if ($show_create_table == 1) {
									$_text = fn_get_lang_var('creating_table');
								} elseif ($show_create_table == 2) {
									$_text = 'Creating table';
								}
								$table_name = $check_prefix ? fn_check_db_prefix($matches[1]) : $matches[1];
								fn_echo('<br />' . $_text . ': <b>' . $table_name . '</b>');
							}

							if ($check_prefix) {
								$query = fn_check_db_prefix($query);
							}
							db_query($query);

							if ($track) {
								$executed_queries[] = $query;
								fn_put_contents($tmp_file, serialize($executed_queries));
							}

							if ($show_status) {
								fn_echo(' .');
							}
						}
					}
					$ret = array();
				}
			}

			fclose($fd);
			return true;
		}

		if ($skip_errors) {
			Registry::set('runtime.database.skip_errors', $_skip_errors);
		}
	}

	return false;
}

?>
