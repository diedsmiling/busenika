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
// $Id: mysqli.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

//
// Database function wrappers (MySQLi extension)
//

// Fix this bug http://bugs.php.net/bug.php?id=33772
// 1st problem: session writes to database after connection was closed
// 2nd problem: ajax handler uses database after connection was closed
class cmysqli extends mysqli {
	function __destruct()
	{
		$GLOBALS['db_conn'] = $this;
		if (defined('AJAX_REQUEST')) {
			Registry::get('ajax')->__destruct();
		}
		session_write_close();
	}
}

// Returns connection ID or false on failure
// clean parameter is needed to initialize base mysqli class
function driver_db_connect($db_host, $db_user, $db_password)
{
	$db_conn = new cmysqli($db_host, $db_user, $db_password);

	return $db_conn;
}

// Returns connection ID or false on failure
function driver_db_select($db_name)
{
	global $db_conn;

	if (@mysqli_select_db($db_conn, $db_name)) {
		return $db_conn;
	}

	return false;
}

function driver_db_create($db_name)
{
	return driver_db_query("CREATE DATABASE IF NOT EXISTS `$db_name`");
}

function driver_db_query($query)
{
	global $db_conn;
	static $reconnect_attempts = 0;

	$result = mysqli_query($db_conn, $query);

	if (empty($result)) {
		// Lost connection, try to reconnect (max - 3 times)
		if ((driver_db_errno() == 2013 || driver_db_errno() == 2006) && $reconnect_attempts < 3) {
			$db_conn = db_initiate(Registry::get('config.db_host'), Registry::get('config.db_user'), Registry::get('config.db_password'), Registry::get('config.db_name'));
			$reconnect_attempts++;
			$result = driver_db_query($query);

		// Assume that the table is broken
		// Try to repair
		} elseif (preg_match("/'(\S+)\.(MYI|MYD)/", driver_db_error(), $matches)) {
			$result = mysqli_query("REPAIR TABLE $matches[1]");
		}
	}

	return $result;
}

function driver_db_result($result, $offset)
{
	return mysqli_field_seek($result, $offset);
}

function driver_db_fetch_row($result)
{
	return mysqli_fetch_row($result);
}

function driver_db_fetch_array($result)
{
	return mysqli_fetch_array($result, MYSQLI_ASSOC);
}

function driver_db_free_result($result)
{
	mysqli_free_result($result);
}

function driver_db_num_rows($result)
{
	return mysqli_num_rows($result);
}

function driver_db_insert_id()
{
	global $db_conn;

	return mysqli_insert_id($db_conn);
}

function driver_db_affected_rows()
{
	global $db_conn;

	return mysqli_affected_rows($db_conn);
}

function driver_db_errno()
{
	global $db_conn;

	static $skip_error_codes = array (
		1091, // column exists/does not exist during alter table
		1176, // key does not exist during alter table
		1050, // table already exist 
		1060  // column exists
	);

	$errno = mysqli_errno($db_conn);

	return in_array($errno, $skip_error_codes) ? 0 : $errno;
}

function driver_db_error()
{
	global $db_conn;

	return mysqli_error($db_conn);
}

function driver_db_close()
{
	global $db_conn;
	return @mysqli_close($db_conn);
}

?>