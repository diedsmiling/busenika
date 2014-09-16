<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	require_once(WM_ROOTPATH.'class_settings.php');
	require_once(WM_ROOTPATH.'class_dbstorage.php');
	require_once(WM_ROOTPATH.'class_account.php');
	
	$UpdateIsGood = true;
	
	$AddTableArray = array(
		DBTABLE_AWM_SENDERS,
		DBTABLE_AWM_COLUMNS,
		DBTABLE_CAL_USERS_DATA,
		DBTABLE_CAL_CALENDARS,
		DBTABLE_CAL_EVENTS	
	);
	
	$AddColumnArray = array(
		'awm_addr_book' =>
			array(
				array('use_frequency', 'int(11) NOT NULL default 0', '[int] NOT NULL DEFAULT (0)'),
				array('auto_create', 'tinyint(1) NOT NULL default 0', '[bit] NOT NULL DEFAULT (0)')
			),
			
		'awm_addr_groups' =>
			array(
				array('use_frequency', 'int(11) NOT NULL default 0', '[int] NOT NULL DEFAULT (0)'),
				array('email', 'varchar(255) default NULL', '[varchar] (255) NULL'),
				array('company', 'varchar(200) default NULL', '[varchar] (200) NULL'),
				array('street', 'varchar(255) default NULL', '[varchar] (255) NULL'),
				array('city', 'varchar(200) default NULL', '[varchar] (200) NULL'),
				array('state', 'varchar(200) default NULL', '[varchar] (200) NULL'),
				array('zip', 'varchar(10) default NULL', '[varchar] (10) NULL'),
				array('country', 'varchar(200) default NULL', '[varchar] (200) NULL'),
				array('phone', 'varchar(50) default NULL', '[varchar] (50) NULL'),
				array('fax', 'varchar(50) default NULL', '[varchar] (50) NULL'),
				array('web', 'varchar(255) default NULL', '[varchar] (255) NULL'),
				array('organization', 'tinyint(1) NOT NULL default 0', '[bit] NOT NULL DEFAULT (0)')
			)		
	);
	
	$null = null;
	$settings = &Settings::CreateInstance();
	
	if (!$settings || !$settings->isLoad || !$settings->IncludeLang('English'))
	{
		exit('<font color="red"><b>Warning!</b></font><br />Can\'t get settings!<br />');		
	}
	
	if ($settings->DbType != DB_MSSQLSERVER && $settings->DbType != DB_MYSQL)
	{
		exit('<font color="red"><b>Warning!</b></font><br />You can update only MySql or MsSql database!<br />');		
	}
	
	$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
	if (!$dbStorage->Connect())
	{
		$error = isset($GLOBALS[ErrorDesc]) ? '<b>ERROR:</b> '.getGlobalError() : '';
		exit('<font color="red"><b>Warning!</b></font><br />Connection Error!<br />'.$error);
	}
	
	$p = 0;
	
	if (count($AddTableArray) > 0)
	{
		foreach ($AddTableArray as $tabelName)
		{
			CreateTableOnUpdate($dbStorage, $settings, $tabelName, ++$p);
		}
	}
	
	echo '<br /><b>'.(++$p).'</b>. Start update tables: <br />';
	
	foreach ($AddColumnArray As $tableName => $ColumnNames)
	{
		
		echo '<br /> - Update '.$settings->DbPrefix.$tableName.': <br />';
		if ($dbStorage->IsTableExist($settings->DbPrefix, $tableName))		
		{
			$oldColumns = $dbStorage->GetTablesColumns($settings->DbPrefix, $tableName);
			if ($oldColumns)
			{
				foreach ($ColumnNames As $AddrBookArray)
				{
					if (!in_array($AddrBookArray[0], $oldColumns))
					{
						$isGood = false;
						echo ' - add <b>'.$AddrBookArray[0].'</b> column in table: ';
						switch ($settings->DbType)
						{
							case DB_MYSQL:
								$isGood = $dbStorage->_dbConnection->Execute('ALTER TABLE `'.$settings->DbPrefix.$tableName.'` ADD `'.$AddrBookArray[0].'` '.$AddrBookArray[1]);
								break;	
							case DB_MSSQLSERVER:
								$isGood = $dbStorage->_dbConnection->Execute('ALTER TABLE ['.$settings->DbPrefix.$tableName.'] ADD ['.$AddrBookArray[0].'] '.$AddrBookArray[2]);
								break;	
						}
						
						if ($isGood)
						{
							echo ' <font color="green"><b>done!</b></font><br />';
						}
						else
						{
							$error = isset($GLOBALS[ErrorDesc]) ? '<br /><b>ERROR:</b> '.getGlobalError() : '';
							echo ' <font color="red"><b>error!</b>'.$error.'</font><br />';
							$UpdateIsGood = false;
						}
						
					}
					else echo '<font color="grey"> - '.$AddrBookArray[0].' column already exist in table</font><br />';				
				}
			}
			else 
			{
				$error = isset($GLOBALS[ErrorDesc]) ? '<br /><b>ERROR:</b> '.getGlobalError() : '';
				echo '<font color="red"> - can\'t get '.$settings->DbPrefix.$tableName.' columns names'.$error.'</font><br />';
				$UpdateIsGood = false;
			}		
		}
		else 
		{
			echo '<font color="red"> - '.$settings->DbPrefix.$tableName.' not exist</font><br />';
			$UpdateIsGood = false;
		}
	}
	
	
	echo '<br /><b>'.(++$p).'</b>. Start create new index: <br />';
	
	$AddIndexArray = GetIndexsArray();
	foreach ($AddIndexArray as $tableName => $indexData) 
	{
		if (is_array($indexData))
		{
			foreach ($indexData as $fieldName) 
			{
				if ($dbStorage->CheckExistIndex($settings->DbPrefix, $tableName, $fieldName))
				{
					echo '<font color="grey"> - index on '.$fieldName.' already exist in '.$settings->DbPrefix.$tableName.' table</font><br />';
				}
				else 
				{
					if ($dbStorage->CreateIndex($settings->DbPrefix, $tableName, $fieldName))
					{
						echo ' - add index on '.$fieldName.' in '.$settings->DbPrefix.$tableName.' table: <font color="green"><b>done!</b></font><br />';
					}
					else 
					{
						$error = isset($GLOBALS[ErrorDesc]) ? '<br /><b>ERROR:</b> '.getGlobalError() : '';
						echo '<font color="red"> - can\'t create index for '.$fieldName.' in in '.$settings->DbPrefix.$tableName.' table'.$error.'</font><br />';
						$UpdateIsGood = false;
					}
				}
			}
		}
	}
	
	echo '<br /><b>'.(++$p).'</b>. Start update settings.xml file: ';
	if ($settings->SaveToXml())
	{
		 echo ' <font color="green"><b>done!</b></font><br />';
	}
	else
	{
		$error = isset($GLOBALS[ErrorDesc]) ? '<br /><b>ERROR:</b> '.getGlobalError() : '';
		echo '<font color="red"> - can\'t update settings.xml file'.$error.'</font><br />';
		$UpdateIsGood = false;
	}
	
	echo ($UpdateIsGood) ? '<br /><br /><br /><b>update done!</b></font>' :
					'<br /><br /><br /><font color="red"><b>update failed!</b></font></font>';

	/**
	 * @param obj $dbStorage
	 * @param obj $settings
	 * @param string $tableName
	 * @param int $number
	 */
	function CreateTableOnUpdate(&$dbStorage, &$settings, $tableName, $number)
	{
		global $UpdateIsGood;
		echo '<font color="black" sise="3" face="verdana"><br /><b>'.$number.'</b>. Start create <b>'.$settings->DbPrefix.$tableName.'</b> table: <br />';
	
		if (!$dbStorage->IsTableExist($settings->DbPrefix, $tableName))
		{
			if ($dbStorage->CreateOneTable($settings->DbPrefix, $tableName))
			{
				echo '<font color="green"> - '.$settings->DbPrefix.$tableName.' create successful</font><br />';
			}
			else 
			{
				$error = isset($GLOBALS[ErrorDesc]) ? '<br /><b>ERROR:</b> '.getGlobalError() : '';
				echo '<font color="red"> - '.$settings->DbPrefix.$tableName.' don\'t create'.$error.'</font><br />';
				$UpdateIsGood = false;
			}
		}
		else echo '<font color="grey"> - '.$settings->DbPrefix.$tableName.' already exist</font><br />';
			
	}
	
	
