<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));

	require_once(WM_ROOTPATH.'class_settings.php');

	define('WEBMAILCONFIGTAB', 'wm.tab');

class WMSettings
{
	/**
	 * @access public
	 * @var string
	 */
	var $Host = '127.0.0.1';
	
	/**
	 * @access public
	 * @var int
	 */
	var $AdminPort;
	
	/**
	 * @access public
	 * @var string
	 */
	var $AdminLogin;
	
	/**
	 * @access public
	 * @var string
	 */
	var $AdminPassword;
	
	/**
	 * @access public
	 * @var int
	 */
	var $OutPort = 25;
	
	/**
	 * @access public
	 * @var Settings
	 */
	var $Settings;
	
	/**
	 * @access public
	 * @var bool
	 */
	var $IsLoad = false;
	
	/**
	 * @access private
	 * @return WMSettings
	 */
	function WMSettings($param = true)
	{
	 	if (!is_null($param))
	    {
	    	die('error: WMSettings::CreateInstance()');
	    }
		
		$this->Settings =& Settings::CreateInstance();
		$this->IsLoad = $this->Settings->isLoad;

		$this->Host = $this->Settings->WmServerHost;
		   
		$this->IsLoad &= $this->_parse($this->Settings->WmServerRootPath.'/'.WEBMAILCONFIGTAB);
	}
	
	/**
	 * @static
	 * @access public
	 * @return WMSettings
	 */
	function &CreateInstance()
	{
		static $instance;
    	if (!is_object($instance))
    	{
			$instance = new WMSettings(null);
    	}
    	return $instance;
	}

	/**
	 * @access private
	 * @return bool
	 */
	function _parse($fileName)
	{
		if (file_exists($fileName))
		{
			$file = @file($fileName);
			if ($file)
			{
				foreach ($file as $fileLine)
				{
					$fileLine = trim($fileLine);
					if (strlen($fileLine) == 0 || $fileLine{0} == '#')
					{
						continue;
					}
					
					$array = explode("\t", trim($fileLine));
					if (is_array($array) && count($array) > 1)
					{
						$name = trim($array[0], '"');
						$value = trim($array[1], '"');
						
						switch ($name)
						{
							case 'CtrlPort':
								$this->AdminPort = (int) $value;
								break;
							case 'SmtpPort':
								$this->OutPort = (int) $value;
								break;
							case 'Login':
								$this->AdminLogin = $value;
								break;
							case 'Password':
								$this->AdminPassword = ConvertUtils::WmServerDeCrypt($value);
								break;	
						}
					}
				}
				return true;
			}
		}
		else
		{
			setGlobalError('Can\'t find file: '.$fileName);
		}
		return false;
	}
}