<?php
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	require_once(WM_ROOTPATH.'db/class_dbsql.php');
	require_once(WM_ROOTPATH.'class_mailstorage.php');

	class DbOdbc extends DbGeneralSql
	{
		/**
		 * @access private
		 * @var string
		 */
		var $_dbCustomConnectionString;
		
		/**
		 * @access private
		 * @var short
		 */
		var $_dbType;
		
		/**
		 * @param string $customConnectionString
		 * @return DbOdbc
		 */
		function DbOdbc($customConnectionString, $dbType)
		{
			$this->_dbCustomConnectionString = $customConnectionString;
			$this->_dbType = $dbType;
			$this->_log =& CLog::CreateInstance();
		}
		
		/**
		 * @return bool
		 */
		function Connect()
		{
			if (!extension_loaded('odbc'))
			{
				$this->ErrorDesc = 'Can\'t load ODBC extension.';
				setGlobalError($this->ErrorDesc);
				$this->_log->WriteLine($this->ErrorDesc);
				return false;
			}
			$ti = getmicrotime();
			$this->_conectionHandle = @odbc_connect($this->_dbCustomConnectionString, '', '', 'SQL_CUR_USE_ODBC');
			$this->_log->WriteLine('>> CONNECT TIME - '. (getmicrotime()-$ti));
				
			if($this->_conectionHandle)
			{
				return true;
			}
			else 
			{
				$this->_setSqlError();
				return false;
			}
		}

		/**
		 * @return bool
		 */
		function Disconnect()
		{
			
			if($this->_conectionHandle)
			{
				if($this->_resultId)
				{
					@odbc_free_result($this->_resultId);
					$this->_resultId = null;
				}
				@odbc_close($this->_conectionHandle);
				$this->_conectionHandle = null;
				return true;
			}
			else
			{
				return false;
			}
		}
		
		/**
		 * @param string $query
		 * @return mixed
		 */
		function Execute($query)
		{
			$this->_log->WriteLine('SQL Query: '.$query);
			$ti = getmicrotime();
			$this->_resultId = @odbc_exec($this->_conectionHandle, $query);
			$this->_log->WriteLine('>> Execute TIME - '. (getmicrotime()-$ti) . ' :: result: '. (bool) $this->_resultId);
			
			if($this->_resultId)
			{
				return $this->_resultId !== false;
			}
			else 
			{
				$this->_setSqlError();
				return false;
			}
		}
			
		/**
		 * @param bool $autoFree optional
		 * @return object
		 */
		function &GetNextRecord($autoFree = true)
		{
			if($this->_resultId)
			{
				$result = @odbc_fetch_object($this->_resultId);
				if (!$result && $autoFree)
				{
					$this->FreeResult();
				}
				return $result;
			}
			else
			{
				$this->_setSqlError();
				return false;
			}		
		}
		
		/**
		 * @param bool $autoFree optional
		 * @return array
		 */
		function &GetNextArrayRecord($autoFree = true)
		{
			if ($this->_resultId)
			{
				$result = @odbc_fetch_array($this->_resultId);
				if (!$result && $autoFree)
				{
					$this->FreeResult();
				}
				return $result;
			}
			else
			{
				$null = null;
				$this->_setSqlError();
				return $null;
			}		
		}
		
		
		/**
		 * @return int
		 */
		function GetLastInsertId()
		{	
			switch ($this->_dbType)
			{
				case DB_MSSQLSERVER:
					$result = $this->Execute('SELECT SCOPE_IDENTITY() ident');
					break;
				case DB_MYSQL:
					$result = $this->Execute('SELECT LAST_INSERT_ID() AS ident');
					break;
				default:
					$result = $this->Execute('SELECT @@IDENTITY AS ident');
			}

			if ($result)
			{
				$insertId = -1;
				while ($row = &$this->GetNextRecord())
				{
					$insertId = $row->ident;
				}
				return $insertId;
			}
			else
			{
				$this->_setSqlError();
				return -1;
			}
		}
		
		/**
		 * @return bool
		 */
		function FreeResult()
		{
			if ($this->_resultId)
			{
				if (!@odbc_free_result($this->_resultId))
				{
					$this->_setSqlError();
					return false;
				}
				else 
				{
					$this->_resultId = null;
				}
				return true;
			}
			else 
			{
				return true;
			}
		}
		
		/**
		 * @return int
		 */
		function ResultCount()
		{
		    return @odbc_num_rows($this->_resultId);
		}
		
		/**
		 * @access private
		 */
		function _setSqlError($errorDesc = '')
		{
			if ($errorDesc)
			{
				$this->ErrorDesc = $errorDesc;
				$this->ErrorCode = 0;	
			}
			elseif ($this->_conectionHandle)
			{
				$this->ErrorDesc = @odbc_errormsg($this->_conectionHandle);
				$this->ErrorCode = @odbc_error($this->_conectionHandle);
			}
			else
			{
				$this->ErrorDesc = @odbc_errormsg();
				$this->ErrorCode = @odbc_error();
			}

			setGlobalError($this->ErrorDesc);
			$this->_log->WriteLine('ErrorDesc: '.$this->ErrorDesc."\tErrorCode".$this->ErrorCode);
		}
		
	}