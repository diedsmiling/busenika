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
// $Id: class.profiler.php 9162 2010-03-26 08:44:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

class Profiler {
	static public $checkpoints = array();
	static public $queries = array();
	static public $queries_time = array();
	
	static function checkpoint($name)
	{
		if (!defined('PROFILER')) {
			return false;
		}

		self::$checkpoints[$name] = array(
			'time' => self::microtime(),
			'memory' => memory_get_usage(),
			'included_files' => count(get_included_files()),
			'queries' => count(self::$queries),
		);
	}

	static function microtime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	static function display()
	{
		if (!defined('PROFILER')) {
			return false;
		}
		
		if (defined('PROFILER_FOR_ADMIN') && empty($_SESSION['PROFILER_FOR_ADMIN'])) {
			$user_id = !empty($_SESSION['auth']['user_id']) ? $_SESSION['auth']['user_id'] : 0;
			if (isset($_REQUEST['PROFILER_FOR_ADMIN']) || $user_id == 1) {
				$_SESSION['PROFILER_FOR_ADMIN'] = true;
			} else {
				return false;
			}
		}
		
		$previous = array();
		$cummulative = array();

		$first = true;
		
		if (defined('PROFILER_SQL')) {
			echo '<ul style="list-style:none; border: 1px solid #cccccc; padding: 3px;">';
			foreach (self::$queries as $key => $query) {
				$time = self::$queries_time[$key];
				$color = ($time > LONG_QUERY_TIME) ? '#FF0000' : (($time > 0.2) ? '#FFFFCC' : '');
				echo '<li ' . ($color ? "style=\"background-color: $color\">" : ($key % 2 ? 'style="background-color: #eeeeee;">' : '>')) . $time . ' - ' . $query . '</li>';
			}
			echo '</ul>';
		}

		echo '<br />- Queries time: ' . sprintf("%.4f", array_sum(self::$queries_time)) . '<br />';
	
		foreach (self::$checkpoints as $name => $c) {
			echo '<br /><b>' . $name . '</b><br />';
			if ($first == false) {
				echo '- Memory: ' . (number_format($c['memory'] - $previous['memory'])) . ' (' . number_format($c['memory']) . ')' . '<br />';
				echo '- Files: ' . ($c['included_files'] - $previous['included_files']) . ' (' . $c['included_files'] . ')' . '<br />';
				echo '- Queries: ' . ($c['queries'] - $previous['queries']) . ' (' . $c['queries'] . ')' . '<br />';
				echo '- Time: ' . sprintf("%.4f", $c['time'] - $previous['time']) . ' (' . sprintf("%.4f", $c['time'] - $cummulative['time']) . ')' . '<br />';
			} else {
				echo '- Memory: ' . number_format($c['memory']) . '<br />';
				echo '- Files: ' . $c['included_files'] . '<br />';
				echo '- Queries: ' . $c['queries'] . '<br />';
				
				$first = false;
				$cummulative = $c;
			}
			$previous = $c;
		}
		echo '<br /><br />';
	}

	static function set_query($query, $time)
	{
		if (!defined('PROFILER')) {
			return false;
		}

		self::$queries[] = $query;
		self::$queries_time[] = sprintf('%.5f', $time);
	}

}

?>