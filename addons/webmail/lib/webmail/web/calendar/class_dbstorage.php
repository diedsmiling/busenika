<?php

	define('DBTABLE_A_USERS', 'a_users');
	define('DBTABLE_CAL_USERS_DATA', 'acal_users_data');
	define('DBTABLE_CAL_CALENDARS', 'acal_calendars');
	define('DBTABLE_CAL_EVENTS', 'acal_events');

 
	function GetTablesArray($pref)
	{
		return array(
				$pref.DBTABLE_A_USERS, 
				$pref.DBTABLE_CAL_USERS_DATA, 
				$pref.DBTABLE_CAL_CALENDARS, 
				$pref.DBTABLE_CAL_EVENTS
			);
	}
	/**
	 * @return array
	 */
	function GetIndexsArray()
	{
		return array(
			'acal_users_data' => array('settings_id', 'user_id'),
			'acal_users_keys' => array('key_id', 'user_id'),  
			'acal_calendars' => array('calendar_id', 'user_id'),
			'acal_events' => array('event_id', 'calendar_id')
		);
	}


		function CreateDatabaseStorage()
		{
			$wm_settings = &Settings::CreateInstance();
			$settings = new CalSettings($wm_settings);

			static $instance;
			
    		if (is_object($instance))
    		{
    			return $instance;
    		}
			
			switch ($settings->DbType)
			{
				default:
				case DB_MSSQLSERVER:
					$instance = MsSqlStorage($settings->DbHost, $settings->DbLogin, $settings->DbPassword);
					break;
				case DB_MYSQL:
					$instance = MySqlStorage($settings->DbHost, $settings->DbLogin, $settings->DbPassword);
					break;
			}
    		
			return $instance;
		}
		

		function MsSqlStorage($host, $dblogin, $dbpass)
		{
			return (bool) (@mssql_connect($host, $dblogin, $dbpass));
		}

		function MySqlStorage($host, $dblogin, $dbpass)
		{
			return (bool)(@mysql_connect($host, $dblogin, $dbpass));
		}



	class DbStorage
	{
		/**
		 * @param string $pref
		 * @return bool
		 */
		function CheckExistTable($pref)
		{
			$tableArray = GetTablesArray($pref);
			$res = SQL::selectAllTablesFromDB();
			if (!$res)
			{
				return false;
			}

			while ($array = DB::GetNextArrayRecord($res))
			{
				if ($array && $array[0] && in_array($array[0], $tableArray))
				{
					return $array[0];
				}
			}
			
			return true;
		}
		

		/**
		 * @param string $pref
		 * @return bool
		 */
		function CreateTables($pref)
		{
			$tableArray = GetTablesArray($pref);
			$original = GetTablesArray('');
			foreach ($tableArray as $key => $tname)			
			{
				$sql = trim(DB::CreateTable($original[$key], $pref));
				if (!$sql || $sql == '') return false;
				
				if (!DB::execute($sql))
				{
					return $tname;	
				}
			}

			return true;
		}

		function CreateAllIndex($pref)
		{
			$AddIndexArray = GetIndexsArray();
			foreach ($AddIndexArray as $tableName => $indexData) 
			{
				if (is_array($indexData))
				{
					foreach ($indexData as $fieldName) 
					{
						if (!$this->CheckExistIndex($pref, $tableName, $fieldName))
						{
							if (!$this->CreateIndex($pref, $tableName, $fieldName))
							{
								return false;
							}
						}
					}
				}
			}
			return true;
		}
		
		/**
		 * @param string $pref
		 * @param string $TableName
		 * @param string $fieldName
		 * @return bool
		 */
		function CheckExistIndex($pref, $TableName, $fieldName)
		{
			$indexArray = $this->GetIndexsOfTable($pref, $TableName);
			if (is_array($indexArray))
			{
				return in_array($fieldName, $indexArray);
			}
			
			return false;	
		}
		
		/**
		 * @param string $pref
		 * @param string $tableName
		 * @return array/bool
		 */
		function GetIndexsOfTable($pref, $tableName)
		{
			$returnArray = array();
			$res = SQL::GetIndexsOfTable($pref, $tableName);
			if (!$res)
			{
				return false;
			}

			while ($array = DB::GetNextArrayRecord($res))
			{
				if ($array && count($array) > 4)
				{
					$returnArray[] = trim($array[4]);
				}
			}
			
			return $returnArray;
		}

		/**
		 * @param string $pref
		 * @param string $tableName
		 * @param string $fieldName
		 * @return bool
		 */
		function CreateIndex($pref, $tableName, $fieldName)
		{
			return SQL::CreateIndex(trim($pref), $tableName, $fieldName);
		}


}


?>