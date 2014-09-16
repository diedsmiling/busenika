<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	require_once(WM_ROOTPATH.'inc_settings_path.php');
	require_once(WM_ROOTPATH.'class_settings.php');

	// Use default formatting.
	define ('LOG_FORMAT_OPTIONS_NONE', 0);
	
	// Include the current date in the timestamp.
	define ('LOG_FORMAT_OPTIONS_ADD_DATE', 1);
	
	define('LOG_FILENAME', 'log_'.date('Y-m-d').'.txt');
	
	class Logger
	{
		/**
		 * @var bool
		 */
		var $Enabled;
		
		/**
		 * LOG_FORMAT_OPTIONS_NONE or LOG_FORMAT_OPTIONS_ADD_DATE
		 * @var short
		 */
		var $Format = LOG_FORMAT_OPTIONS_ADD_DATE;
		
		/**
		 * @var string
		 */
		var $LogFilePath;
		
		/**
		 * @return Logger
		 */
		function Logger()
		{
			$settings = &Settings::CreateInstance();
			$this->Enabled = $settings->EnableLogging;

			$this->LogFilePath = INI_DIR.'/'.LOG_FILENAME;
			
		}
		
		/**
		 * @param string $errorDesc
		 */
		function WriteLine($errorDesc)
		{
			if (!$this->Enabled) return;
			
			$date = date('H:i:s');
			
			if (($this->Format & LOG_FORMAT_OPTIONS_ADD_DATE) == LOG_FORMAT_OPTIONS_ADD_DATE)
			{
				$date = date('m/d/Y H:i:s');
			}
			
			@error_log('['.$date.'] ' . $errorDesc ."\r\n", 3, $this->LogFilePath);
		}
	
	
	
	}
?>
