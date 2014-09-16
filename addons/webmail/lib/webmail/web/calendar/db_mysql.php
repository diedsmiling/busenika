<?php

if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
require_once(WM_ROOTPATH.'common/class_log.php');

class DB 
{
        function connect($host, $dblogin, $dbpass, $dbname)
        {
			if (mysql_connect($host, $dblogin, $dbpass))  {
				mysql_select_db($dbname) or die("Could not select database");
				return true;
			} else {
				return false;
			}
        }

       function escape($query)
       {
			return mysql_real_escape_string($query);
       } 
        
        function execute($query)
        {
        	$log =& CLog::CreateInstance();
        	$log->WriteLine('calendar MYSQL: '.$query);
		    $res = mysql_query($query);
            if ($res === false) die(mysql_error());
            return $res;
        }
        
       function executeWithId($query)
        {
            $res=DB::execute($query);
            return mysql_insert_id();
        }        
        
        function query($query)
        {
        	$res = DB::execute($query);
            $data = array();
            while ($line = mysql_fetch_assoc($res))
            	$data[] = $line;
            return $data;
        }
		
		function convert_date($date){
			$res = 'DATE_FORMAT("'.$date.'", "%Y-%m-%d %T")';
			return $res;
		}

		function convert_date_as($date){
			return 'DATE_FORMAT('.$date.', "%Y-%m-%d %T") as '.$date;  
		}

		function select_users_sql($prefix, $nom, $accountPerPage, $sortField, $sortOrder, $searchText) {
			if (strlen($searchText) > 0)
			{
				$search = ' WHERE (usr.user_id LIKE %1$d OR usr.email LIKE "%1$s" OR usr.displayname LIKE "%1$s") ';
				$search = sprintf($search, '%'.$searchText.'%');
			} else {
				$search = '';
			}
			$sql = 'SELECT usr.user_id, usr.email, usr.displayname FROM '.$prefix.'acal_users_data AS usr 
				INNER JOIN '.$prefix.'a_users AS usr_common ON (usr.user_id = usr_common.id_user AND usr_common.deleted <> 1) '.$search.' 
				ORDER BY '.$sortField.' '.$sortOrder.', user_id DESC 
				LIMIT '.$nom.', '.$accountPerPage;
				
/*			$sql = 'SELECT *  
					FROM acal_users_data '.$search.'
					ORDER BY '.$sortField.' '.$sortOrder.', user_id DESC 
					LIMIT '.$nom.', '.$accountPerPage;*/
			return $sql;
		}
		
/**********************************/

		/**
		 * @return array
		 */
		function AllTableNames()
		{
			return 'show tables';
		}
		
		/**
		 * @param bool $autoFree optional
		 * @return array
		 */
		function GetNextArrayRecord($res)
		{
			if ($res)
			{ 
				$result = @mysql_fetch_array($res);
				if (!$result)
				{
					if (!@mysql_free_result($res))
					{
						return false;
					}
					else 
					{
						return null;
					}
					return true;

				}
				return $result;
			}
			else
			{ echo 'null';
				return $null;
			}		
		}
		
		function CreateTable($original, $pref)
		{
			$pref = ($pref) ? $pref : '';
			switch ($original)
			{
				case DBTABLE_A_USERS:
					return '
					CREATE TABLE `'.$pref.'a_users` (
					  `id_user` int(11) NOT NULL auto_increment,
					  `deleted` tinyint(1) NOT NULL default 0,
					  PRIMARY KEY  (`id_user`)
					) DEFAULT CHARSET=utf8';
					break;
				case DBTABLE_CAL_USERS_DATA:
					return '
					CREATE TABLE `'.$pref.'acal_users_data` (
					  `settings_id` int(11) NOT NULL auto_increment,
					  `user_id` int(11) NOT NULL default 0,
					  `timeformat` tinyint(1) NOT NULL default 1,
					  `dateformat` tinyint(1) NOT NULL default 1,
					  `showweekends` tinyint(1) NOT NULL default 0,
					  `workdaystarts` tinyint(2) NOT NULL default 0,
					  `workdayends` tinyint(2) NOT NULL default 1,
					  `showworkday` tinyint(1) NOT NULL default 0,
					  `weekstartson` tinyint(1) NOT NULL default 0,
					  `defaulttab` tinyint(1) NOT NULL default 1,
					  `country` varchar(2) NOT NULL,
					  `timezone` smallint(3) NULL,
					  `alltimezones` tinyint(1) NOT NULL default 0,
					  PRIMARY KEY  (`settings_id`)
					) DEFAULT CHARSET=utf8';
					break;
				case DBTABLE_CAL_CALENDARS:
					return "
					CREATE TABLE `".$pref."acal_calendars` (
					  `calendar_id` int(11) NOT NULL auto_increment,
					  `user_id` int(11) NOT NULL default 0,
					  `calendar_name` varchar(100) NOT NULL default '',
					  `calendar_description` varchar(510) default NULL,
					  `calendar_color` int(11) NOT NULL default 0,
					  `calendar_active` tinyint(1) NOT NULL default 0,
					  PRIMARY KEY  (`calendar_id`)
					) DEFAULT CHARSET=utf8";
					break;
				case DBTABLE_CAL_EVENTS:
					return "
					CREATE TABLE `".$pref."acal_events` (
					  `event_id` int(11) NOT NULL auto_increment,
					  `calendar_id` int(11) NOT NULL default 0,
					  `event_timefrom` datetime default NULL,
					  `event_timetill` datetime default NULL, 
					  `event_allday` tinyint(1) NOT NULL default 0,
					  `event_name` varchar(100) NOT NULL default '',
					  `event_text` varchar(510) default NULL,
					  `event_priority` tinyint(4) NULL,
					  PRIMARY KEY  (`event_id`)
					) DEFAULT CHARSET=utf8";
					break;
					
				default: return '';	break;
			}
			return '';
		}

		/**
		 * @return string
		 */
		function GetIndexsOfTable($pref, $tableName)
		{
			return 'SHOW INDEX FROM `'.$pref.$tableName.'`';	
		}	

		/**
		 * @param string $pref
		 * @param string $tableName
		 * @param string $fieldName
		 * @return string
		 */
		function CreateIndex($pref, $tableName, $fieldName)
		{
			$temp = (strlen($pref) > 0) ? $pref.'_' : '';
			return 'CREATE INDEX '.strtoupper($temp.$tableName.'_'.$fieldName).'_INDEX 
						ON '.$pref.$tableName.'('.$fieldName.')';
		}

}
?>