<?php
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	require_once(WM_ROOTPATH.'db/class_dbsql.php');

	class DbMySql extends DbSql
	{
		
		/**
		 * @param string $host
		 * @param string $user
		 * @param string $password
		 * @param string $dbName
		 * @return DbMySql
		 */
		function DbMySql($host, $user, $password, $dbName)
		{
			$this->_host = $host;
			$this->_user = $user;
			$this->_password = $password;
			$this->_dbName = $dbName;
			$this->_log =& CLog::CreateInstance();
		}
		
		/**
		 * @return bool
		 */
		function Connect()
		{
			if (!extension_loaded('mysql'))
			{
				$this->ErrorDesc = 'Can\'t load MySQL extension.';
				setGlobalError($this->ErrorDesc);
				$this->_log->WriteLine($this->ErrorDesc);
				
				return false;
			}
			$ti = getmicrotime();

			$this->_conectionHandle = mysql_connect($this->_host, $this->_user, $this->_password);
			$this->_log->WriteLine('>> CONNECT TIME - '. (getmicrotime()-$ti));
		
			if ($this->_conectionHandle)
			{
				if(strlen($this->_dbName) > 0)
				{
					$dbselect = @mysql_select_db($this->_dbName, $this->_conectionHandle);
					if(!$dbselect)
					{
						$this->_setSqlError();
						$this->_conectionHandle = $dbselect;
						@mysql_close($this->_conectionHandle);
						return false;
					}
					//mysql_query('SET @@collation_connection = @@collation_database');
				}
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
			$result = true;
			if ($this->_conectionHandle)
			{
				if($this->_resultId)
				{
					@mysql_free_result($this->_resultId);
					$this->_resultId = null;
				}
				$result = @mysql_close($this->_conectionHandle);
				$this->_conectionHandle = null;
				return $result;
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
			$query = ConvertUtils::mainClear($query);
			$this->_log->WriteLine('SQL Query: '.$query);
			
			$mtime = getmicrotime();
			$this->_resultId = @mysql_query($query, $this->_conectionHandle);
			$this->_log->WriteLine('>> Execute TIME - '. (getmicrotime() - $mtime));

			if ($this->_resultId)
			{
				return $this->_resultId;
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
			if ($this->_resultId)
			{
				$result = @mysql_fetch_object($this->_resultId);
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
		 * @param bool $autoFree optional
		 * @return array
		 */
		function &GetNextArrayRecord($autoFree = true)
		{
			if ($this->_resultId)
			{
				$result = @mysql_fetch_array($this->_resultId);
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
			return @mysql_insert_id();
		}
		
		/**
		 * @return bool
		 */
		function FreeResult()
		{
			if ($this->_resultId)
			{
				if (!@mysql_free_result($this->_resultId))
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
		    return @mysql_num_rows($this->_resultId);
		}
		
		function _setSqlError()
		{
			if ($this->_conectionHandle)
			{
				$this->ErrorDesc = @mysql_error($this->_conectionHandle);
				$this->ErrorCode = @mysql_errno($this->_conectionHandle);
			}
			else
			{
				$this->ErrorDesc = @mysql_error();
				$this->ErrorCode = @mysql_errno();
			}

			setGlobalError($this->ErrorDesc);
			$this->_log->WriteLine('ErrorDesc ['.$this->ErrorCode.']: '.$this->ErrorDesc);
		}
		
	}