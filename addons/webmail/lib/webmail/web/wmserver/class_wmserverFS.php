<?php

class WMserverFS
{
	/**
	 * @access private
	 * @var string
	 */
	var $basePath = '';
	
	/**
	 * @access private
	 * @var string
	 */
	var $domain = '';
	
	/**
	 * @access private
	 * @var string
	 */
	var $path = '';
	var $pathnew = '';
	var $pathcur = '';
	
	/**
	 * @access private
	 * @var string
	 */
	var $userName = '';
	
	/**
	 * @access private
	 * @var bool
	 */
	var $isUnix;
	
	/**
	 * @access private
	 * @var CLog
	 */
	var $_log;
	
	/**
	 * @access public
	 * @param string $basePath
	 * @param string $email
	 * @return WMserverFS
	 */
	function WMserverFS($basePath, $email)
	{
		$parsedEmail = ConvertUtils::ParseEmail($email);
		
		$this->userName = trim($parsedEmail[0]);
		$this->domain = trim($parsedEmail[1]);
		
		$this->basePath = rtrim($basePath, '/\\');
		if (is_dir($this->basePath.'/domains/'.$this->domain.'/'.$this->userName.'/mailbox/'))
		{
			$this->path = $this->basePath.'/domains/'.$this->domain.'/'.$this->userName.'/mailbox/';
			$this->isUnix = false;	
		}
		else
		{
			$this->path = $this->basePath.'/domains/'.$this->domain.'/'.$this->userName.'/Maildir/';
			$this->pathnew = $this->path.'new/';
			$this->pathcur = $this->path.'cur/';
			$this->isUnix = true;
		}
		
		$this->_log =& CLog::CreateInstance();
	}
	
	/**
	 * @access public
	 * @return array/false
	 */
	function getAllMessagesNames()
	{
		$names = array();
		if ($this->isUnix)
		{
			if (@is_dir($this->pathnew))
			{
				$temp = $this->_getAllMessagesNamesByPath($this->pathnew);
				if (is_array($temp))
				{
					$names = array_merge($names, $temp);
					unset($temp);
				}
			}
			else $this->_log('can\'t get xmail messages names '.$this->pathnew);
			
			if (@is_dir($this->pathcur))
			{
				$temp = $this->_getAllMessagesNamesByPath($this->pathcur);
				if (is_array($temp))
				{
					$names = array_merge($names, $temp);
					unset($temp);
				}
			}
			else $this->_log('can\'t get xmail messages names '.$this->pathcur);
		}
		else
		{
			if (@is_dir($this->path))
			{
				$temp = $this->_getAllMessagesNamesByPath($this->path);
				if (is_array($temp))
				{
					$names = array_merge($names, $temp);
					unset($temp);
				}
			}
			else $this->_log('can\'t get xmail messages names '.$this->path);
		}

		if (is_array($names))
		{
			asort($names);
			return array_keys($names);
		}
		
		return false;
	}

	/**
	 * @access private
	 * @param string $path
	 * @return array/false
	 */
	function _getAllMessagesNamesByPath($path)
	{
		$names = array();
		if ($dir = @dir($path))
		{
			while (false !== ($entry = $dir->read()))
			{
				if($entry != '.' && $entry != '..')
				{	
					$names[$entry] = @filemtime($path.$entry);
				}
			}

			return $names;
		}

		$this->_log('can\'t get xmail messages names '.$path);
		return false;		
	}
	 
	/**
	 * @access public
	 * @return int
	 */
	function getAllMessagesSize()
	{
		$size = 0;
		if ($this->isUnix)
		{
			if (is_dir($this->pathnew))
			{
				$size  += $this->_getAllMessagesSizeByPath($this->pathnew);
			}
			else
			{
				$this->_log('can\'t get xmail messages size '.$this->pathnew);
			}
			
			if (is_dir($this->pathcur))
			{
				$size  += $this->_getAllMessagesSizeByPath($this->pathcur);
			}
			else
			{
				$this->_log('can\'t get xmail messages size '.$this->pathcur);
			}
		}
		else
		{
			if (is_dir($this->path))
			{
				$size  += $this->_getAllMessagesSizeByPath($this->path);
			}
			else
			{
				$this->_log('can\'t get xmail messages size '.$this->path);
			}
		}	

		return $size;	
	}
	
	/**
	 * @access private
	 * @param string $path
	 * @return int
	 */
	function _getAllMessagesSizeByPath($path)
	{
		$size = 0;
		if ($dir = @dir($path))
		{
			while (false !== ($entry = $dir->read()))
			{
				if($entry != '.' && $entry != '..')
				{	
					$size += filesize($path.$entry);
				}
			}
			return $size;
		}

		$this->_log('can\'t get xmail messages size '.$path);
		return $size;
	}
	
	/**
	 * @access public
	 * @return int
	 */
	function getAllMessagesCount()
	{
		$count = 0;
		if ($this->isUnix)
		{
			if (is_dir($this->pathnew))
			{
				$count  += $this->_getAllMessagesCountByPath($this->pathnew);
			}
			else
			{
				$this->_log('can\'t get xmail messages count '.$this->pathnew);
			}
			
			if (is_dir($this->pathcur))
			{
				$count  += $this->_getAllMessagesCountByPath($this->pathcur);
			}
			else
			{
				$this->_log('can\'t get xmail messages count '.$this->pathcur);
			}
		}
		else
		{
			if (is_dir($this->path))
			{
				$count  += $this->_getAllMessagesCountByPath($this->path);
			}
			else
			{
				$this->_log('can\'t get xmail messages count '.$this->path);
			}
		}	

		return $count;	
	}
	
	/**
	 * @access private
	 * @param string $path
	 * @return int
	 */
	function _getAllMessagesCountByPath($path)
	{
		$count = 0;
		if ($dir = @dir($path))
		{
			while (false !== ($entry = $dir->read()))
			{
				if($entry != '.' && $entry != '..')
				{	
					$count++;
				}
			}
			return $count;
		}

		$this->_log('can\'t get xmail messages count '.$path);
		return $count;
	}
	
	/**
	 * @access public
	 * @param string $uid
	 * @return string/false
	 */
	function getMessage($uid)
	{
		$path = false;
		if ($this->isUnix)
		{
			if (file_exists($this->pathnew.$uid))
			{
				$path = $this->pathnew.$uid;
			}
			elseif (file_exists($this->pathcur.$uid))
			{
				$path = $this->pathcur.$uid;
			}
		}
		else
		{
			if (file_exists($this->path.$uid))
			{
				$path = $this->path.$uid;
			}
		}	

		if ($path !== false)
		{
			return $this->_getMessageByPath($path);
		}
		
		$this->_log('can\'t get message '.$this->path.$uid);
		return false;
	}
	
	/**
	 * @access private
	 * @param string $path
	 * @return string/false
	 */
	function _getMessageByPath($path)
	{
		if (file_exists($path))
		{
			$raw = @file_get_contents($path);
			if ($raw !== false)
			{
				$this->_log('get message '.$path);
				return $raw;
			}
		}
		
		$this->_log('can\'t get message '.$path);
		return false;
	}
	
	/**
	 * @access public
	 * @param string $uid
	 * @return bool
	 */
	function deleteMessage($uid)
	{
		if ($this->isUnix)
		{
			if (file_exists($this->pathnew.$uid))
			{
				if (@unlink($this->pathnew.$uid))
				{
					$this->_log('delete message '.$this->pathnew.$uid);
					return true;
				}
			}
			elseif (file_exists($this->pathcur.$uid))
			{
				if (@unlink($this->pathcur.$uid))
				{
					$this->_log('delete message '.$this->pathcur.$uid);
					return true;
				}
			}
		}
		else
		{
			if (@unlink($this->path.$uid))
			{
				$this->_log('delete message '.$this->path.$uid);
				return true;
			}
		}
		
		$this->_log('can\'t delete message '.$this->path.$uid);
		return false;
	}
	
	
	/**
	 * @access public
	 * @param string $uid
	 * @return int/false
	 */
	function getSizeMessage($uid)
	{
		if ($this->isUnix)
		{
			if (file_exists($this->pathnew.$uid))
			{
				$size = @filesize($this->pathnew.$uid);
				if ($size !== false)
				{
					return $size;
				}
			}
			elseif (file_exists($this->pathcur.$uid))
			{
				$size = @filesize($this->pathcur.$uid);
				if ($size !== false)
				{
					return $size;
				}
			}
		}
		else
		{
			if (file_exists($this->path.$uid))
			{
				$size = @filesize($this->path.$uid);
				if ($size !== false)
				{
					return $size;
				}
			}
		}

		$this->_log('can\'t get message size '.$this->path.$uid);
		return false;
	}
	
	/**
	 * @access public
	 * @param string $uid
	 * @return string/false
	 */
	function getHeader($uid)
	{
		if ($this->isUnix)
		{
			if (file_exists($this->pathnew.$uid))
			{
				return $this->_getHeaderByPath($this->pathnew.$uid);
			}
			elseif (file_exists($this->pathcur.$uid))
			{
				return $this->_getHeaderByPath($this->pathcur.$uid);
			}
		}
		else
		{
			return $this->_getHeaderByPath($this->path.$uid);
		}
		
		return false;
	}
	
	/**
	 * @access private
	 * @param string $uid
	 * @return string/false
	 */
	function _getHeaderByPath($path)
	{
		if(file_exists($path))
		{
			$this->_log('get message header ('.$path.')');
			$header = '';
			$handle = @fopen($path, 'r');
			if ($handle !== false)
			{
				while (!@feof($handle))
				{
				    $buffer = @fgets($handle, 4096);
				    if($buffer == "\r\n" || $buffer === false)
				    {
						break;
				    }
					else
					{
						$header .= $buffer;
					}
				}
				@fclose($handle);
				return $header;
			}
		}
		
		$this->_log('can\'t get message header '.$path);		
		return false;
	}
	

	/**
	 * @access private
	 * @param unknown_type $string
	 */
	function _log($string)
	{
		$this->_log->WriteLine('Xmail: '.$string);
	}
};
