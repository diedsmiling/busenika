<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	require_once(WM_ROOTPATH.'class_settings.php');

	define ('LOG_FORMAT_OPTIONS_NONE', 0);		// Use default formatting.
	define ('LOG_FORMAT_OPTIONS_ADD_DATE', 1);	// Include the current date in the timestamp.
	
	define('LOG_FILENAME', 'log_'.date('Y-m-d').'.txt');
	define('LOG_LINELIMIT', 4000);
	
	class CLog
	{
		/**
		 * @access public
		 * @var bool
		 */
		var $Enabled;
		
		/**
		 * @access public
		 * @var short
		 * @example 
		 *		$this->Format = LOG_FORMAT_OPTIONS_NONE;
		 *		$this->Format = LOG_FORMAT_OPTIONS_ADD_DATE;
		 */
		var $Format = LOG_FORMAT_OPTIONS_ADD_DATE;
		
		/**
		 * @access public
		 * @var string
		 */
		var $LogFilePath;
		
		/**
		 * @access private
		 * @param bool $param[optional] = true
		 * @return CLog
		 */
		function CLog($param = true)
		{
		    if (!is_null($param))
		    {
		    	die(CANT_CALL_CONSTRUCTOR);
		    }		
			
			$settings =& Settings::CreateInstance();
			$this->Enabled = $settings->EnableLogging;

			$this->LogFilePath = INI_DIR.'/'.LOG_PATH.'/'.LOG_FILENAME;
			if ($this->Enabled && !is_dir(INI_DIR.'/'.LOG_PATH))
			{
				@mkdir(INI_DIR.'/'.LOG_PATH);
			}
		}
		
		/**
		 * @static
		 * @access public
		 * @return CLog
		 */
		function &CreateInstance()
		{
			static $instance;
    		if (!is_object($instance))
    		{
				$instance = new CLog(null);
    		}
    		return $instance;
		}
		
		/**
		 * @access public
		 * @param string $errorDesc
		 * @param int $line[optional] = ''
		 */
		function WriteLine($errorDesc, $line = '')
		{
			if (!$this->Enabled) 
			{
				return;
			}
			
			if (LOG_LINELIMIT && strlen($errorDesc) > LOG_LINELIMIT*2)
			{
				$errorDesc = 
					substr($errorDesc, 0, LOG_LINELIMIT).
					"\r\n ----------- cut ------------ \r\n".
					substr($errorDesc, -ceil(LOG_LINELIMIT/2));
			}
			
			$date = (($this->Format & LOG_FORMAT_OPTIONS_ADD_DATE) == LOG_FORMAT_OPTIONS_ADD_DATE)
						? date('m/d/Y H:i:s') : date('H:i:s');
			
			$line = (strlen($line) > 0) ? '[line: '.$line.']' : '';
			@error_log('['.$date.']'.$line.' '.$errorDesc ."\r\n", 3, $this->LogFilePath);
		}
	}
