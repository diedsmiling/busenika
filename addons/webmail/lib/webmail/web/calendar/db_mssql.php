<?php

if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
require_once(WM_ROOTPATH.'common/class_log.php');

class DB
{
		function connect($host, $dblogin, $dbpass, $dbname)
        {
			if (mssql_connect($host, $dblogin, $dbpass))  {
				mssql_select_db($dbname) or die("Could not select database");
				mssql_query('set dateformat ymd');
				return true;
			} else {
				return false;
			}
        }

        function escape($query)
        {
			return str_replace('\'', '\'\'', $query);
        } 
        
        function execute($query)
        {
        	$log =& CLog::CreateInstance();
        	$log->WriteLine('calendar MSSQL: '.$query);
         	$res = mssql_query($query);
			if ($res === false) die(mssql_get_last_message());
        	return $res;
        }
        
        function executeWithId($query)
        {
			$res=DB::execute($query);
			$id = DB::query("SELECT @@IDENTITY");
			return $id[0]["computed"];
        }
        
        function query($query)
        {
                $res = DB::execute($query);
                $data = array();
                while ($line = mssql_fetch_assoc($res))
                    $data[] = $line;										
                return $data;
        }
		
		function convert_date($date){
			$res = 'CONVERT(VARCHAR, "'.$date.'", 120)';
			return $res;
		}
		
		function convert_date_as($date){
			$res = 'CONVERT(VARCHAR, '.$date.', 120) as '.$date;  
			return $res;
		}

		function select_users_sql($prefix, $nom, $accountPerPage, $sortField, $sortOrder, $searchText) {
			if (strlen($searchText) > 0)
			{
				$search = ' AND (user_id LIKE "%1$s" OR email LIKE "%1$s" OR displayname LIKE "%1$s") ';
				$search = sprintf($search, '%'.$searchText.'%');
			} else {
				$search = '';
			}

			$sql = 'SELECT TOP '.$accountPerPage.' * 
					FROM '.$prefix.'acal_users_data AS usr
					WHERE user_id > -1 '.$search.' AND user_id NOT IN
							  (SELECT user_id FROM
									  (SELECT TOP '.$nom.' * 
											FROM '.$prefix.'acal_users_data AS usr1
							   WHERE user_id > -1 '.$search.'
							   ORDER BY '.$sortField.' '.$sortOrder.') AS stable)
					ORDER BY '.$sortField.' '.$sortOrder;

			return $sql;
		}
/******************************/
		function AllTableNames()
		{
			return 'SELECT [name] AS tableNames FROM sysobjects o WHERE xtype = \'U\' AND OBJECTPROPERTY(o.id, N\'IsMSShipped\')!=1';
		}
		
		function GetNextArrayRecord($res)
		{
		
		}
		
		function CreateTable($original, $pref)
		{
			$pref = ($pref) ? $pref : '';
			switch ($original)
			{
				case DBTABLE_A_USERS:
					return 'CREATE TABLE [a_users] (
                                 [id_user] [int] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
                                 [deleted] [bit] NOT NULL DEFAULT (0),
                            ) ON [PRIMARY]';
					break;
				case DBTABLE_CAL_USERS_DATA:
					return "DECLARE @col_name NVARCHAR(50) DECLARE @qryString NVARCHAR(1000)
						SET @col_name =
                        (SELECT     CONVERT(nvarchar(50), SERVERPROPERTY('collation')))
						SET @qryString = 'CREATE TABLE [acal_users_data] ([settings_id] [int] PRIMARY KEY IDENTITY (1, 1) NOT NULL,[user_id] [int] NOT NULL DEFAULT (0),
	           			[timeformat] [tinyint] NOT NULL DEFAULT (1), [dateformat] [tinyint] NOT NULL DEFAULT (1), [showweekends]  [tinyint] NOT NULL DEFAULT (0),
                       [workdaystarts]  [tinyint] NOT NULL DEFAULT (0), [workdayends] [tinyint] NOT NULL DEFAULT (1), [showworkday] [tinyint] NOT NULL DEFAULT (0),
                       [weekstartson] [tinyint] NOT NULL default (0), [defaulttab]  [tinyint] NOT NULL DEFAULT (1), [country] [varchar] (2)  NULL, [timezone] [smallint] NULL,
                       [alltimezones] [tinyint] NOT NULL DEFAULT (0) ) ON [PRIMARY] '
                       EXEC (@qryString)";
					break;
				case DBTABLE_CAL_CALENDARS:
					return "DECLARE @col_name NVARCHAR(50) 
                        DECLARE @qryString NVARCHAR(1000)
                        SET @col_name = (SELECT     CONVERT(nvarchar(50), SERVERPROPERTY('collation')))
                             SET @qryString='CREATE TABLE [acal_calendars] (
                                 [calendar_id] [int] PRIMARY KEY IDENTITY (1, 1) NOT NULL,
                                 [user_id] [int] NOT NULL DEFAULT (0),
                                 [calendar_name] [nvarchar] (100) COLLATE '+@col_name+' NOT NULL DEFAULT (''''),
                                 [calendar_description] [nvarchar] (510) COLLATE '+@col_name+' NOT NULL DEFAULT (''''),
                                 [calendar_color] [int] NOT NULL DEFAULT (0),
                                 [calendar_active] [bit] NOT NULL DEFAULT (0)
                             ) ON [PRIMARY]'
                        EXEC(@qryString)";

					break;
				case DBTABLE_CAL_EVENTS:
					return "DECLARE @col_name NVARCHAR(50) 
                        DECLARE @qryString NVARCHAR(1000)
                        SET @col_name = (SELECT     CONVERT(nvarchar(50), SERVERPROPERTY('collation')))
                             SET @qryString='CREATE TABLE [acal_events] (
                                 [event_id] [int] PRIMARY KEY IDENTITY(1, 1) NOT NULL,
                                 [calendar_id] [int] NOT NULL DEFAULT (0),
                                 [event_timefrom] [datetime] NOT NULL,
                                 [event_timetill] [datetime] NOT NULL,
                                 [event_allday] [bit] NOT NULL DEFAULT (0),
                                 [event_name] [nvarchar] (100) COLLATE '+@col_name+' NOT NULL DEFAULT (''''),
                                 [event_text] [nvarchar] (510) COLLATE '+@col_name+' NULL,
                                 [event_priority] [tinyint] NULL DEFAULT (0)
                             ) ON [PRIMARY]'
                        EXEC(@qryString)";

					break;

				default: return '';	break;
			}
			return '';
		}

		
		function GetIndexsOfTable($pref, $tableName)
		{
		
		}
		
		function CreateIndex($pref, $tableName, $fieldName)
		{
		
		}		
}
?>