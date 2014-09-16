<?php

	class CalSettings
	{
		/**
		 * @var int
		 */
		var $DbType; //1-mysql 2-mssql
	
		/**
		 * @var string
		 */
		var $DbLogin;
	
		/**
		 * @var string
		 */
		var $DbPassword;
	
		/**
		 * @var string
		 */
		var $DbName;
	
		/**
		 * @var string
		 */
		var $DbDsn;
		
		/**
		 * @var string
		 */
		var $DefaultSkin;
		/**
		 * @var string
		 */
		var $DbHost;
	
		/**
		 * @var string
		 */
		var $DbPrefix;
		
		/**
		 * @var string
		 */
		var $SiteName;

		/**
		 * @var short
		 */
		var $DefaultTimeFormat;

		/**
		 * @var short
		 */
		var $DefaultDateFormat;

		/**
		 * @var bool
		 */
		var $ShowWeekends;

		/**
		 * @var int
		 */
		var $WorkdayStarts;
		
		/**
		 * @var int
		 */
		var $WorkdayEnds;
		
		/**
		 * @var bool
		 */
		var $ShowWorkDay;
		
		/**
		* @var int
		*/
		var $WeekStartsOn;

		/**
		 * @var short
		 */
		var $DefaultTab;

		/**
		 * @var string
		 */
		var $DefaultCountry;

		/**
		 * @var short
		 */
		var $DefaultTimeZone;

		/**
		 * @var bool
		 */
		var $AllTimeZones;
		
		/**
		 * @var Settings
		 */
		var $_settings;
		
		/**
		 * @param Settings $settings
		 * @return CalSettings
		 */
		function CalSettings($settings)
		{
			if ($settings)
			{
				$this->_settings =& $settings;
				
				$this->DbType = $this->_settings->DbType; 
				$this->DbLogin = $this->_settings->DbLogin;
				$this->DbPassword = $this->_settings->DbPassword;
				$this->DbName = $this->_settings->DbName;
				$this->DbDsn = $this->_settings->DbDsn;
				$this->DbHost = $this->_settings->DbHost;
				$this->DbPrefix = $this->_settings->DbPrefix;
				$this->DefaultSkin = $this->_settings->DefaultSkin;
				$this->SiteName = $this->_settings->WindowTitle;
				$this->DefaultTimeFormat = $this->_settings->Cal_DefaultTimeFormat;
				$this->DefaultDateFormat = $this->_settings->Cal_DefaultDateFormat;
				$this->ShowWeekends = $this->_settings->Cal_ShowWeekends;
				$this->WorkdayStarts = $this->_settings->Cal_WorkdayStarts;
				$this->WorkdayEnds = $this->_settings->Cal_WorkdayEnds;
				$this->ShowWorkDay = $this->_settings->Cal_ShowWorkDay;
				$this->WeekStartsOn = $this->_settings->Cal_WeekStartsOn;
				$this->DefaultTab = $this->_settings->Cal_DefaultTab;
				$this->DefaultCountry = $this->_settings->Cal_DefaultCountry;
				$this->DefaultTimeZone = $this->_settings->Cal_DefaultTimeZone;
				$this->AllTimeZones = $this->_settings->Cal_AllTimeZones;
			}
		}
	}
?>