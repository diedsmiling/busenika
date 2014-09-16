<?php
	
define('DBTABLE_A_USERS', 'a_users');
define('DBTABLE_CAL_USERS_DATA', 'acal_users_data');
define('DBTABLE_CAL_USERS_KEYS', 'acal_users_keys');
define('DBTABLE_CAL_CALENDARS', 'acal_calendars');
define('DBTABLE_CAL_EVENTS', 'acal_events');

if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

require_once(WM_ROOTPATH.'class_settings.php');
require_once(WM_ROOTPATH.'calendar/class_settings.php');

$wm_settings =& Settings::CreateInstance();
$settings = new CalSettings($wm_settings);

if ($settings->DbType  == DB_MYSQL)
{
	include_once("db_mysql.php");
}
else if ($settings->DbType == DB_MSSQLSERVER) 
{
	include_once("db_mssql.php");
}
//$host = $settings->DbHost;

class SQL
{
		//static $db;
		function init($host, $login, $password, $dbname)
        {
               	$db = new DB();
				return $db->connect($host, $login, $password, $dbname);
        }
	
		function Select($prefix, $Table, $Name, $Value)
        {
			if($Table == 'acal_events')
				return DB::query("SELECT *,".DB::convert_date_as("event_timefrom").",".DB::convert_date_as("event_timetill")." FROM ".$prefix.$Table." WHERE ".$Name."=".$Value."");
			else
        		return DB::query("SELECT * FROM ".$prefix.$Table." WHERE ".$Name."=".$Value."");
        }
        
        /*function SelectAll($prefix, Table)
		{
			if($Table == 'acal_events')
				return DB::query("SELECT *,".DB::convert_date_as("event_timefrom").",".DB::convert_date_as("event_timetill")." FROM ".$prefix.$Table);
			else
				return DB::query("SELECT * FROM ".$prefix.$Table);
        }*/

        function SelectEvents($prefix, $TimeFrom, $TimeTill, $UserID)
        {
			$sql = "SELECT *,".DB::convert_date_as("event_timefrom").",".DB::convert_date_as("event_timetill")." 
					FROM ".$prefix."acal_events 
					WHERE ((
						(event_timefrom>=".DB::convert_date($TimeFrom)." and event_timefrom<=".DB::convert_date($TimeTill).") 
						OR (event_timetill>=".DB::convert_date($TimeFrom)." and event_timetill<=".DB::convert_date($TimeTill).") 
						OR (event_timefrom<=".DB::convert_date($TimeFrom)." and event_timetill>=".DB::convert_date($TimeTill).")
						) and (
						calendar_id IN (SELECT calendar_id FROM ".$prefix."acal_calendars WHERE user_id=".$UserID.")
						))";
					
        	return DB::query($sql);
		}
		
		function SelectAllYearEvents($prefix,$Date,$UserID) {
			$sql = "SELECT DISTINCT ".DB::convert_date_as("event_timefrom").",".DB::convert_date_as("event_timetill")."   
					FROM ".$prefix."acal_events 
					WHERE (calendar_id IN (SELECT calendar_id FROM ".$prefix."acal_calendars WHERE user_id=".$UserID.") 
					and (YEAR(event_timefrom)=YEAR('".$Date."'))) ORDER BY event_timefrom ASC";
			return DB::query($sql);
		}

        function SelectCalendars($prefix, $UserID, $Active)
        {
                if(!$Active)
                {
                        return DB::query("SELECT * FROM ".$prefix."acal_calendars WHERE user_id=".$UserID."");
                }else
                {       
                        return DB::query("SELECT * FROM ".$prefix."acal_calendars WHERE user_id=".$UserID." and calendar_active='1'");               
                }
        }
        
		function SelectKeysForConfirm($prefix, $k) {
			return DB::query("SELECT user_id, ".DB::convert_date_as('str_date')." FROM ".$prefix."acal_users_keys WHERE str_key='".$k."'");
		}
        
		function InsertUpdateByID($prefix, $Table, &$ID, $Name, $Array)
        {
                if($ID == 0)
                {
                        $data   = array(); 
                        $values = array();
                        foreach($Array as $Key=>$Val)
                        {
                               $data[]   = $Key;
								if(substr($Key,'time') === true)								 	
                               		$values[] = "'".DB::convert_date(DB::escape($Val))."'";									
								else									
                               		$values[] = "'".DB::escape($Val)."'";
                        }
                        
                        $res    = implode(', ', $data);
                        $result = implode(', ', $values);        
                        $ID = DB::executeWithId("INSERT INTO ".$prefix.$Table." (".$res.") VALUES(".$result.")");
						return $ID;                  
                }
                else
                {
                        $data   = array(); 
                        $values = array();
                      
                        foreach($Array as $Key=>$Val)
                        {
                                if(substr($Key,'time') === true)
									$data[] = $Key."= ".DB::convert_date(DB::escape($Val))."";
								else
									$data[] = $Key."= '".DB::escape($Val)."'";								
                        }
                        $res = implode(', ', $data);
                        DB::execute("UPDATE ".$prefix.$Table." SET ".$res." WHERE ".$Name." =".$ID);
                }
        }
        
        /**
         * @param string $prefix
         * @param int $calendar_id
         * @param string $name
         * @param string $text
         * @param string $dbtime_from
         * @param string $dbtime_till
         * @param bool $allday
         * @param int $priority
         * @return int
         */
        function CreateEvent($prefix, $calendar_id, $name, $text, $dbtime_from, $dbtime_till, $allday, $priority)
        {
        	$sql = 'INSERT INTO %sacal_events (calendar_id, event_timefrom, event_timetill, event_allday,
        				event_name, event_text, event_priority) 
        				VALUES(%d, \'%s\', \'%s\', %d, \'%s\', \'%s\', %d)';
        	
        	$sql = sprintf($sql, $prefix, $calendar_id,
			        				DB::escape($dbtime_from),
			        				DB::escape($dbtime_till),
			        				(int) $allday,
			        				DB::escape($name),
			        				DB::escape($text),
			        				(int) $priority);	
        	
        	return DB::executeWithId($sql);
        }
        
        /**
         * @param string $prefix
         * @param int $event_id
         * @param int $calendar_id
         * @param string[optional] $name
         * @param string[optional] $text
         * @param string[optional] $dbtime_from
         * @param string[optional] $dbtime_till
         * @param bool[optional] $allday
         * @param int[optional] $priority
         * @param int[optional] $new_calendar_id
         */
        function UpdateEvent($prefix, $event_id, $calendar_id, $name = null, $text = null, $dbtime_from = null, $dbtime_till = null, $allday = null, $priority = null, $new_calendar_id = null)
        {
        	$data = array();
			if ($name !== null)
			{
				$data[] = 'event_name = \''.DB::escape($name).'\'';		
			}
			if ($text !== null)
			{
				$data[] = 'event_text = \''.DB::escape($text).'\'';		
			}
        	if ($dbtime_from !== null)
			{
				$data[] = 'event_timefrom = \''.DB::escape($dbtime_from).'\'';		
			}
        	if ($dbtime_till !== null)
			{
				$data[] = 'event_timetill = \''.DB::escape($dbtime_till).'\'';		
			}
    	    if ($allday !== null)
			{
				$data[] = 'event_allday = '.((int) $allday);		
			}
        	if ($priority !== null)
			{
				$data[] = 'event_priority = '.((int) $priority);		
			}
            if ($new_calendar_id !== null && $new_calendar_id != $calendar_id)
			{
				$data[] = 'calendar_id = '.((int) $new_calendar_id);		
			}

			if (count($data) > 0)
			{
	        	$sql = 'UPDATE %sacal_events SET %s WHERE event_id = %d AND calendar_id = %d';
		       	$sql = sprintf($sql, $prefix, implode(', ', $data), $event_id, $calendar_id);
		        DB::execute($sql);
			}
        }
        
        /**
         * @param string $prefix
         * @param int $user_id
         * @param int $calendar_id
         * @param int $color_id
         * @param string[optional] $name
         * @param string[optional] $content
         * @param bool[optional] $active
         */
        function UpdateCalendar($prefix, $user_id, $calendar_id, $color_id, $name = null, $content = null, $active = null)
        {
       		$data = array('calendar_color = '.$color_id);
			if ($name !== null)
			{
				$data[] = 'calendar_name = \''.DB::escape($name).'\'';		
			}
        	if ($content !== null)
			{
				$data[] = 'calendar_description = \''.DB::escape($content).'\'';		
			}
        	if ($active !== null)
			{
				$data[] = 'calendar_active = '.intval($active);		
			}

        	$sql = 'UPDATE %sacal_calendars SET %s WHERE calendar_id = %d AND user_id = %d';
        	$sql = sprintf($sql, $prefix, implode(', ', $data), $calendar_id, $user_id);
        	DB::execute($sql);
        }

        /**
         * @param string $prefix
         * @param int $user_id
         * @param int $calendar_id
         * @param int $color_id
         * @param string $name
         * @param string $content
         * @param bool $active
         * @return int
         */
        function CreateCalendar($prefix, $user_id, $color_id, $name, $content, $active)
        {
        	$sql = 'INSERT INTO %sacal_calendars (user_id, calendar_name, calendar_description, calendar_color, calendar_active) 
        				VALUES (%d, \'%s\', \'%s\', %d, %d)';
        	$sql = sprintf($sql, $prefix, $user_id, DB::escape($name), DB::escape($content), $color_id, (int) $active);
        	return DB::executeWithId($sql);
        }
        
        function InsertUpdate($prefix, &$ID, $Calendar, $Array)
        {
                if($ID == 0)
                {
                        $data   = array(); 
                        $values = array();
                        
                        foreach($Array as $Key=>$Arr)
                        {
                                $data[]   = $Key;
								if(substr($Key,'time') === true)								 	
                               		$values[] = "'".DB::convert_date(DB::escape($Arr))."'";									
								else
                                	$values[] = "'".DB::escape($Arr)."'";
                        }
                        
                        $res    = implode(', ', $data);
                        $result = implode(', ', $values);
                        
                        $ID = DB::executeWithId("INSERT INTO ".$prefix."acal_events (".$res.") VALUES(".$result.")");
						return $ID;
                }
                else
                {
                        $data   = array(); 
                        $values = array();
                        
                        foreach($Array as $Key=>$Arr)
                        {
                                if(substr($Key,'time') === true)
									$data[] = $Key."= ".DB::convert_date(DB::escape($Arr))."";
								else
									$data[] = $Key."= '".DB::escape($Arr)."'";
                        }
						                        
                        $res = implode(', ', $data);                        
                        DB::execute("UPDATE ".$prefix."acal_events SET ".$res." WHERE calendar_id=".$Calendar." and event_id=".$ID);                        
                }
        }
        
        function Delete($prefix, $Table, $Array) //, $UserID
        {
                $mas=array();
                foreach($Array as $Key=>$Val)
                {
					if(substr($Key,'time')=== true)
						$data[] = $Key."= '".DB::convert_date(DB::escape($Val))."'";
					else
						$data[] = $Key."= '".DB::escape($Val)."'";
					
                    $mas[]=$Key."='".DB::escape($Val)."'";
                }               
            	$res = implode(' and ', $mas);
                DB::execute("DELETE FROM ".$prefix.$Table." WHERE ".$res); //and user_id = ".$UserID);
        }
        
        function DeleteEvent($prefix, $event_id, $calendar_id)
        {
        	DB::execute('DELETE FROM '.$prefix.'acal_events WHERE calendar_id = '.$calendar_id.' AND event_id = '.$event_id);
        }

        function DeleteCalendar($prefix, $calendar_id, $user_id)
        {
        	DB::execute('DELETE FROM '.$prefix.'acal_events WHERE calendar_id = '.$calendar_id);
        	DB::execute('DELETE FROM '.$prefix.'acal_calendars WHERE calendar_id = '.$calendar_id.' AND user_id = '.$user_id);
        }
        
        function DeleteUserEvents($prefix, $Id)
        {
			$sql = "DELETE ".$prefix."acal_events
			FROM ".$prefix."acal_events, ".$prefix."acal_calendars 
			WHERE (".$prefix."acal_events.calendar_id = ".$prefix."acal_calendars.calendar_id AND ".$prefix."acal_calendars.user_id = ".$Id.")";
            DB::execute($sql);
        }
		
		function selectUsersCount($prefix,$searchText)
		{
			if (strlen($searchText) > 0)
			{
				$sql = 'SELECT usr.user_id FROM '.$prefix.'acal_users_data AS usr 
				INNER JOIN '.$prefix.'a_users AS usr_common ON (usr.user_id = usr_common.id_user AND usr_common.deleted <> 1)
				WHERE (usr.user_id LIKE "%1$d" OR usr.email LIKE "%1$s" OR usr.displayname LIKE "%1$s")';
				
				//$sql = 'SELECT * FROM acal_users_data AS usr
				//		WHERE (user_id LIKE %1$d OR email LIKE "%1$s" OR displayname LIKE "%1$s")';
				$sql = sprintf($sql, '%'.$searchText.'%');
			}
			else 
			{
				$sql = 'SELECT usr.user_id FROM '.$prefix.'acal_users_data AS usr INNER JOIN '.$prefix.'a_users AS usr_common ON (usr.user_id = usr_common.id_user AND usr_common.deleted <> 1)';
			}
			$res = DB::query($sql);
			return $res;
		}
		
		function select_users($prefix, $nom, $accountPerPage, $sortField, $sortOrder, $searchText) 
		{
			$search = trim($searchText);
			$sort = ($sortOrder)?"DESC":"ASC";
			$sql = DB::select_users_sql($prefix, $nom, $accountPerPage, $sortField, $sort, $search);
			$res = DB::query($sql);
			return $res;
		}
		
		function selectAllTablesFromDB() 
		{
			return DB::execute(DB::AllTableNames());
		}
		
		function CreateTable($original, $pref) 
		{
			//return DB::execute(DB::CreateTable($original, $pref));
		}
		
		function GetIndexsOfTable($pref, $tableName)
		{
			//return DB::execute(GetIndexsOfTable($pref, $tableName));
		}
		
		function CreateIndex($pref, $tableName, $fieldName) 
		{
			//return DB::execute(CreateIndex($pref, $tableName, $fieldName));
		}
		
		function CheckCalendarsByUserId($prefix, $cal_id, $user_id)
		{
			$res = DB::query('SELECT calendar_id FROM '.$prefix.'acal_calendars WHERE calendar_id = '.$cal_id.' AND user_id = '.$user_id);
			return ($res && count($res) > 0);
		}
}
?>