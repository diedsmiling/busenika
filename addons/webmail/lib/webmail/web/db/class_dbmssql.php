<?php
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	require_once(WM_ROOTPATH.'db/class_dbsql.php');

	class DbMsSql extends DbSql
	{
		/**
		 * @param string $host
		 * @param string $user
		 * @param string $password
		 * @param string $dbName
		 * @return DbMsSql
		 */
		function DbMsSql($host, $user, $password, $dbName)
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
			//if ($this->_conectionHandle != false) return true;
			if (!extension_loaded('mssql'))
			{
				$this->ErrorDesc = 'Can\'t load MsSQL extension.';
				setGlobalError($this->ErrorDesc);
				$this->_log->WriteLine($this->ErrorDesc);
				return false;
			}
			
			$ti = getmicrotime();
			$this->_conectionHandle = @mssql_connect($this->_host, $this->_user, $this->_password);
			$this->_log->WriteLine('>> CONNECT TIME - '. (getmicrotime()-$ti));
			
			if($this->_conectionHandle)
			{
				if(strlen($this->_dbName) > 0)
				{
					$dbselect = @mssql_select_db($this->_dbName, $this->_conectionHandle);
					if(!$dbselect)
					{
						$this->_setSqlError();
						$this->_conectionHandle = $dbselect;
						@mssql_close($this->_conectionHandle);
						return false;
					}
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
			if($this->_conectionHandle)
			{
				if($this->_resultId)
				{
					@mssql_free_result($this->_resultId);
					$this->_resultId = null;
				}
				$result = @mssql_close($this->_conectionHandle);
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
		
			$ti = getmicrotime();
			$this->_resultId = @mssql_query($query, $this->_conectionHandle);
			$this->_log->WriteLine('>> Execute TIME - '. (getmicrotime()-$ti));

			if($this->_resultId === false)
			{
				$this->_setSqlError();
				return false;
			}
			else 
			{
				return $this->_resultId;
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
				$result = @mssql_fetch_object($this->_resultId);
				if (!$result && $autoFree)
				{
					$this->FreeResult();
				}
				
				if ($result) //MSSQL-PHP Empty field bug fix. see http://bugs.php.net/bug.php?id=26315
				{
					$fields = array_keys(get_object_vars($result));
					foreach ($fields as $name)
					{
						if ($result->$name == ' ')
						{
							$result->$name = '';
						}
					}
					unset($fields);
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
			if($this->_resultId)
			{
				$result = @mssql_fetch_array($this->_resultId);
				if (!$result && $autoFree)
				{
					$this->FreeResult();
				}
				
				if ($result) //MSSQL-PHP Empty field bug fix. see http://bugs.php.net/bug.php?id=26315
				{
					$fields = array_keys($result);
					foreach ($fields as $name)
					{
						if ($result[$name] == ' ')
						{
							$result[$name] = '';
						}
					}
					unset($fields);
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
		 * @return int
		 */
		function GetLastInsertId()
		{
			if ($this->Execute('SELECT SCOPE_IDENTITY() AS [identity]'))
			{
				$insertId = -1;
				while ($row = &$this->GetNextRecord())
				{
					$insertId = $row->identity;
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
				if (!@mssql_free_result($this->_resultId))
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
		    return @mssql_num_rows($this->_resultId);
		}
		
		/**
		 * @access private
		 */
		function _setSqlError($errmess = '')
		{
			$this->ErrorCode = 0;
			$this->ErrorDesc = @mssql_get_last_message();
			
			setGlobalError($this->ErrorDesc);
			$this->_log->WriteLine('SQL Error: '.$this->ErrorDesc);
		}
		
	}