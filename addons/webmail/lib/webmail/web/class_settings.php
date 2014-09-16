<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', dirname(__FILE__).'/');
		
	if (!defined('IS_SETTINGS_REQUIRE'))
	{
		if (file_exists(WM_ROOTPATH.'inc_settings_path.php'))
		{
			require_once(WM_ROOTPATH.'inc_settings_path.php');
			define('IS_SETTINGS_REQUIRE', 1);
		}
		else 
		{
			exit('<font color="red">Can\'t find <b>inc_settings_path.php</b> file</font>');
		}
	}

	$dataPath = isset($dataPath) ? str_replace('\\', '/', rtrim(trim($dataPath), '/\\')) : '';
	if ($dataPath)
	{
		$dPath = str_replace('\\', '/', rtrim(realpath($dataPath), '/\\'));
	}
	define('INI_DIR', ($dPath != '') ? $dPath : WM_ROOTPATH.$dataPath);
	
	require_once(WM_ROOTPATH.'common/class_xmldocument.php');
	require_once(WM_ROOTPATH.'common/class_convertutils.php');

	define('LOG_PATH', 'logs');
		
	define('DB_MSSQLSERVER', 1);
	define('DB_MYSQL', 3);
	
	class Settings
	{
		/**
		 * @var string
		 */
		var $WindowTitle;

		/**
		 * @var string
		 */
		var $LicenseKey;
	
		/**
		 * @var string
		 */
		var $AdminPassword;
	
		/**
		 * @var int
		 */
		var $DbType;
	
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
		var $DbHost;

		/**
		 * @var bool
		 */
		var $UseCustomConnectionString;
	
		/**
		 * @var string
		 */
		var $DbCustomConnectionString;
	
		/**
		 * @var string
		 */
		var $DbPrefix;
	
		/**
		 * @var int
		 */
		var $IncomingMailProtocol;
		
		/**
		 * @var string
		 */
		var $IncomingMailServer;
	
		/**
		 * @var int
		 */
		var $IncomingMailPort;
	
		/**
		 * @var string
		 */
		var $OutgoingMailServer;
	
		/**
		 * @var int
		 */
		var $OutgoingMailPort;
	
		/**
		 * @var bool
		 */
		var $ReqSmtpAuth;
	
		/**
		 * @var bool
		 */
		var $AllowAdvancedLogin;
	
		/**
		 * @var int
		 */
		var $HideLoginMode;
	
		/**
		 * @var string
		 */
		var $DefaultDomainOptional;
	
		/**
		 * @var bool
		 */
		var $ShowTextLabels;
	
		/**
		 * @var bool
		 */
		var $AutomaticCorrectLoginSettings;
	
		/**
		 * @var bool
		 */
		var $EnableLogging;
	
		/**
		 * @var bool
		 */
		var $DisableErrorHandling;
	
		/**
		 * @var bool
		 */
		var $AllowAjax;
	
		/**
		 * @var int
		 */
		var $MailsPerPage;
		
		/**
		 * @var bool
		 */
		var $EnableAttachmentSizeLimit;
	
		/**
		 * @var long
		 */
		var $AttachmentSizeLimit;
	
		/**
		 * @var bool
		 */
		var $EnableMailboxSizeLimit;
		
		/**
		 * @var long
		 */
		var $MailboxSizeLimit;
	
		/**
		 * @var bool
		 */
		var $AllowUsersChangeTimeZone;
	
		/**
		 * @var string
		 */
		var $DefaultUserCharset;
	
		/**
		 * @var bool
		 */
		var $AllowUsersChangeCharset;
	
		/**
		 * @var string
		 */
		var $DefaultSkin;
	
		/**
		 * @var bool
		 */
		var $AllowUsersChangeSkin;
	
		/**
		 * @var string
		 */
		var $DefaultLanguage;
	
		/**
		 * @var bool
		 */
		var $AllowUsersChangeLanguage;
	
		/**
		 * @var bool
		 */
		var $AllowDhtmlEditor;
	
		/**
		 * @var bool
		 */
		var $AllowUsersChangeEmailSettings;
	
		/**
		 * @var bool
		 */
		var $AllowDirectMode;
	
		/**
		 * @var bool
		 */
		var $DirectModeIsDefault;
	
		/**
		 * @var bool
		 */
		var $AllowNewUsersRegister;
	
		/**
		 * @var bool
		 */
		var $AllowUsersAddNewAccounts;
	
		/**
		 * @var bool
		 */
		var $StoreMailsInDb;
		
		/**
		 * @var bool
		 */
		var $EnableWmServer;
		
		/**
		 * @var string
		 */
		var $WmServerRootPath;
		
		/**
		 * @var string
		 */
		var $WmServerHost = '';
		
		/**
		 * @var bool
		 */
		var $WmAllowManageXMailAccounts = false;
		
		/**
		 * @var bool
		 */
		var $AllowContacts = true;
		
		/**
		 * @var bool
		 */
		var $AllowCalendar = true;
		
		/**
		 * @var int
		 */
		var $DefaultTimeZone;
		
		/**
		 * @var int
		 */
		var $Cal_DefaultTimeFormat = 1;
		
		/**
		 * @var int
		 */
		var $Cal_DefaultTimeZone = 38;
		
		/**
		 * @var int
		 */
		var $Cal_DefaultDateFormat = 1;
		
		/**
		 * @var bool
		 */
		var $Cal_ShowWeekends = true;
		
		/**
		 * @var int
		 */
		var $Cal_WorkdayStarts = 9;
		
		/**
		 * @var int
		 */
		var $Cal_WorkdayEnds = 18;
		
		/**
		 * @var int
		 */
		var $Cal_ShowWorkDay = 1;
		
		/**
		 * @var int
		 */
		var $Cal_WeekStartsOn = 0;

		/**
		 * @var int
		 */
		var $Cal_DefaultTab = 2;
		
		/**
		 * @var string
		 */
		var $Cal_DefaultCountry = 'US';
		
		/**
		 * @var bool
		 */
		var $Cal_AllTimeZones = false;
		
		/**
		 * @var bool
		 */
		var $isLoad = false;
		
		/**
		 * @var bool
		 */
		var $_langIsInclude = false;
		
		/**
		 * @static
		 * @return Settings
		 */
		function &CreateInstance()
		{
			static $instance;
    		if (!is_object($instance))
    		{
				$instance = new Settings(null);
    		}
    		return $instance;
		}
		
		/**
		* @access private
		*/
		function Settings($param = true)
		{
		    if (!is_null($param))
		    {
		    	die(CANT_CALL_CONSTRUCTOR);
		    }
		    
		    $xmlDocument = &new XmlDocument();
		    if ($xmlDocument->LoadFromFile(INI_DIR . '/settings/settings.xml'))
		    {
		    	$this->isLoad = true;
		    	$this->_loadFromXML($xmlDocument->XmlRoot);
		    }
		}
		
		/**
		 * @return bool
		 */
		function IncludeLang($langName = null)
		{
			if (!$this->isLoad)
			{
				return false;
			}
			
			if ($this->_langIsInclude)
			{
				return true;
			}
			
			if ($langName)
			{
				$lang = $langName;
			}
			else 
			{
				$lang = isset($_SESSION[SESSION_LANG]) ? $_SESSION[SESSION_LANG] : $this->DefaultLanguage;
			}
			
			$lang = ConvertUtils::ClearFileName($lang);
			
			if (file_exists(WM_ROOTPATH.'lang/'.$lang.'.php'))
			{
				include_once(WM_ROOTPATH.'lang/'.$lang.'.php');
				$this->_langIsInclude = true;
			}
			elseif (file_exists(WM_ROOTPATH.'lang/English.php'))
			{
				include_once(WM_ROOTPATH.'lang/English.php');
				$this->_langIsInclude = true;
			}
			
			return $this->_langIsInclude;
		}
		
		/**
		 * @access private
		 * @param XmlDomNode $xmlTree
		 */
		function _loadFromXML(&$xmlTree)
		{
			foreach ($xmlTree->Children as $node)
			{
				switch ($node->TagName)
				{
					case 'Common':
					case 'WebMail':
					case 'Calendar':
						if (count($node->Children) > 0)
						{						
							$this->_loadFromXML($node);
						}
						break;
					case 'SiteName':
						$this->WindowTitle = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;					
					case 'WindowTitle':
						$this->WindowTitle = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'AdminPassword':
						$this->AdminPassword = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'DBType':
						$this->DbType = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'DBLogin':
						$this->DbLogin = trim(ConvertUtils::WMBackHtmlSpecialChars($node->Value));
						break;
					case 'DBPassword':
						$this->DbPassword = trim(ConvertUtils::WMBackHtmlSpecialChars($node->Value));
						break;
					case 'DBName':
						$this->DbName = trim(ConvertUtils::WMBackHtmlSpecialChars($node->Value));
						break;
					case 'DBDSN':
						$this->DbDsn = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'DBHost':
						$this->DbHost = trim(ConvertUtils::WMBackHtmlSpecialChars($node->Value));
						break;
					case 'UseCustomConnectionString':
						$this->UseCustomConnectionString = (bool) $node->Value;
						break;
					case 'DBCustomConnectionString':
						$this->DbCustomConnectionString = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'DBPrefix':
						$this->DbPrefix = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'IncomingMailProtocol':
						$this->IncomingMailProtocol = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'IncomingMailServer':
						$this->IncomingMailServer = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'IncomingMailPort':
						$this->IncomingMailPort = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'OutgoingMailServer':
						$this->OutgoingMailServer = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'OutgoingMailPort':
						$this->OutgoingMailPort = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'ReqSmtpAuth':
						$this->ReqSmtpAuth = (bool) $node->Value;
						break;
					case 'AllowAdvancedLogin':
						$this->AllowAdvancedLogin = (bool) $node->Value;
						break;
					case 'HideLoginMode':
						$this->HideLoginMode = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'DefaultDomainOptional':
						$this->DefaultDomainOptional = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'ShowTextLabels':
						$this->ShowTextLabels = (bool) $node->Value;
						break;
					case 'AutomaticCorrectLoginSettings':
						$this->AutomaticCorrectLoginSettings = (bool) $node->Value;
						break;
					case 'EnableLogging':
						$this->EnableLogging = (bool) $node->Value;
						break;
					case 'DisableErrorHandling':
						$this->DisableErrorHandling = (bool) $node->Value;
						break;
					case 'AllowAjax':
						$this->AllowAjax = (bool) $node->Value;
						break;
					case 'MailsPerPage':
						$this->MailsPerPage = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'EnableAttachmentSizeLimit':
						$this->EnableAttachmentSizeLimit = (bool) $node->Value;
						break;						
					case 'AttachmentSizeLimit':
						$this->AttachmentSizeLimit = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'EnableMailboxSizeLimit':
						$this->EnableMailboxSizeLimit = (bool) $node->Value;
						break;
					case 'MailboxSizeLimit':
						$this->MailboxSizeLimit = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'AllowUsersChangeTimeZone':
						$this->AllowUsersChangeTimeZone = (bool) $node->Value;
						break;
					case 'DefaultUserCharset':
						$this->DefaultUserCharset = ConvertUtils::GetCodePageName($node->Value);
						break;
					case 'AllowUsersChangeCharset':
						$this->AllowUsersChangeCharset = (bool) ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'DefaultSkin':
						$this->DefaultSkin = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'AllowUsersChangeSkin':
						$this->AllowUsersChangeSkin = (bool) $node->Value;
						break;
					case 'DefaultLanguage':
						$this->DefaultLanguage =ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'AllowUsersChangeLanguage':
						$this->AllowUsersChangeLanguage = (bool) $node->Value;
						break;
					case 'AllowDHTMLEditor':
						$this->AllowDhtmlEditor = (bool) $node->Value;
						break;
					case 'AllowUsersChangeEmailSettings':
						$this->AllowUsersChangeEmailSettings = (bool) $node->Value;
						break;
					case 'AllowDirectMode':
						$this->AllowDirectMode = (bool) $node->Value;
						break;
					case 'DirectModeIsDefault':
						$this->DirectModeIsDefault = (bool) $node->Value;
						break;
					case 'AllowNewUsersRegister':
						$this->AllowNewUsersRegister = (bool) $node->Value;
						break;
					case 'AllowUsersAddNewAccounts':
						$this->AllowUsersAddNewAccounts = (bool) $node->Value;
						break;
					case 'StoreMailsInDb':
						$this->StoreMailsInDb = (bool) $node->Value;
						break;
					case 'EnableWmServer':
						$this->EnableWmServer = (bool) $node->Value;
						break;				
					case 'WmServerRootPath':
						$this->WmServerRootPath = rtrim(ConvertUtils::WMBackHtmlSpecialChars($node->Value), '\\/');
						break;
					case 'WmServerHost':
						$this->WmServerHost = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;	
					case 'WmAllowManageXMailAccounts':
						$this->WmAllowManageXMailAccounts = (bool) $node->Value;
						break;						
					case 'AllowContacts':
						$this->AllowContacts = (bool) $node->Value;
						break;	
					case 'AllowCalendar':
						$this->AllowCalendar = (bool) $node->Value;
						break;	
					case 'DefaultTimeZone':
						if ($xmlTree->TagName == 'Calendar')
						{
							$this->Cal_DefaultTimeZone = (int) $node->Value;
						}
						else 
						{
							$this->DefaultTimeZone = (int) $node->Value;
						}
						break;	
					case 'DefaultTimeFormat':
						$this->Cal_DefaultTimeFormat = (int) $node->Value;
						break;
					case 'DefaultDateFormat':
						$this->Cal_DefaultDateFormat = (int) $node->Value;
						break;
					case 'ShowWeekends':
						$this->Cal_ShowWeekends = (int) $node->Value;
						break;
					case 'WorkdayStarts':
						$this->Cal_WorkdayStarts = (int) $node->Value;
						break;
					case 'WorkdayEnds':
						$this->Cal_WorkdayEnds = (int) $node->Value;
						break;
					case 'ShowWorkDay':
						$this->Cal_ShowWorkDay = (int) $node->Value;
						break;
					case 'WeekStartsOn':
						$this->Cal_WeekStartsOn = (int) $node->Value;
						break;
					case 'DefaultTab':
						$this->Cal_DefaultTab = (int) $node->Value;
						break;
					case 'DefaultCountry':
						$this->Cal_DefaultCountry = ConvertUtils::WMBackHtmlSpecialChars($node->Value);
						break;
					case 'AllTimeZones':
						$this->Cal_AllTimeZones = (int) $node->Value;
						break;

				}
			}
		}
		
		/**
		 * @return bool
		 */
		function SaveToXml()
		{
			$xmlDocument = &new XmlDocument();
			$xmlDocument->CreateElement('Settings');
			$xmlDocument->XmlRoot->AppendAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
			$xmlDocument->XmlRoot->AppendAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
			
			$common = new XmlDomNode('Common');
			$common->AppendChild(new XmlDomNode('SiteName', ConvertUtils::WMHtmlSpecialChars($this->WindowTitle)));
			$common->AppendChild(new XmlDomNode('LicenseKey', ConvertUtils::WMHtmlSpecialChars($this->LicenseKey)));
			$common->AppendChild(new XmlDomNode('AdminPassword', ConvertUtils::WMHtmlSpecialChars($this->AdminPassword)));
			$common->AppendChild(new XmlDomNode('DBType', (int) $this->DbType));
			$common->AppendChild(new XmlDomNode('DBLogin', ConvertUtils::WMHtmlSpecialChars($this->DbLogin)));
			$common->AppendChild(new XmlDomNode('DBPassword', ConvertUtils::WMHtmlSpecialChars($this->DbPassword)));
			$common->AppendChild(new XmlDomNode('DBName', ConvertUtils::WMHtmlSpecialChars($this->DbName)));
			$common->AppendChild(new XmlDomNode('DBDSN', ConvertUtils::WMHtmlSpecialChars($this->DbDsn)));
			$common->AppendChild(new XmlDomNode('DBHost', ConvertUtils::WMHtmlSpecialChars($this->DbHost)));
			$common->AppendChild(new XmlDomNode('UseCustomConnectionString', (int) $this->UseCustomConnectionString));
			$common->AppendChild(new XmlDomNode('DBCustomConnectionString', ConvertUtils::WMHtmlSpecialChars($this->DbCustomConnectionString)));
			$common->AppendChild(new XmlDomNode('DBPrefix', ConvertUtils::WMHtmlSpecialChars($this->DbPrefix)));
			$common->AppendChild(new XmlDomNode('DefaultSkin', ConvertUtils::WMHtmlSpecialChars($this->DefaultSkin)));
			$common->AppendChild(new XmlDomNode('AllowUsersChangeSkin', (int) $this->AllowUsersChangeSkin));
			$common->AppendChild(new XmlDomNode('DefaultLanguage', ConvertUtils::WMHtmlSpecialChars($this->DefaultLanguage)));
			$common->AppendChild(new XmlDomNode('AllowUsersChangeLanguage', (int)$this->AllowUsersChangeLanguage));
			$xmlDocument->XmlRoot->AppendChild($common);
			
			$webmail = new XmlDomNode('WebMail');
			$webmail->AppendChild(new XmlDomNode('IncomingMailProtocol', (int) $this->IncomingMailProtocol));
			$webmail->AppendChild(new XmlDomNode('IncomingMailServer', ConvertUtils::WMHtmlSpecialChars($this->IncomingMailServer)));
			$webmail->AppendChild(new XmlDomNode('IncomingMailPort', (int) $this->IncomingMailPort));
			$webmail->AppendChild(new XmlDomNode('OutgoingMailServer', ConvertUtils::WMHtmlSpecialChars($this->OutgoingMailServer)));
			$webmail->AppendChild(new XmlDomNode('OutgoingMailPort', (int) $this->OutgoingMailPort));
			$webmail->AppendChild(new XmlDomNode('ReqSmtpAuth', (int) $this->ReqSmtpAuth));
			$webmail->AppendChild(new XmlDomNode('AllowAdvancedLogin', (int) $this->AllowAdvancedLogin));
			$webmail->AppendChild(new XmlDomNode('HideLoginMode', (int) $this->HideLoginMode));
			$webmail->AppendChild(new XmlDomNode('DefaultDomainOptional', ConvertUtils::WMHtmlSpecialChars($this->DefaultDomainOptional)));
			$webmail->AppendChild(new XmlDomNode('ShowTextLabels', (int)$this->ShowTextLabels));
			$webmail->AppendChild(new XmlDomNode('AutomaticCorrectLoginSettings', (int) $this->AutomaticCorrectLoginSettings));
			$webmail->AppendChild(new XmlDomNode('EnableLogging', (int) $this->EnableLogging));
			$webmail->AppendChild(new XmlDomNode('DisableErrorHandling', (int) $this->DisableErrorHandling));
			$webmail->AppendChild(new XmlDomNode('AllowAjax', (int) $this->AllowAjax));
			$webmail->AppendChild(new XmlDomNode('MailsPerPage', (int) $this->MailsPerPage));
			$webmail->AppendChild(new XmlDomNode('EnableAttachmentSizeLimit', (int) $this->EnableAttachmentSizeLimit));
			$webmail->AppendChild(new XmlDomNode('AttachmentSizeLimit', ConvertUtils::WMHtmlSpecialChars($this->AttachmentSizeLimit)));
			$webmail->AppendChild(new XmlDomNode('EnableMailboxSizeLimit', (int) $this->EnableMailboxSizeLimit));
			$webmail->AppendChild(new XmlDomNode('MailboxSizeLimit', ConvertUtils::WMHtmlSpecialChars($this->MailboxSizeLimit)));
			$webmail->AppendChild(new XmlDomNode('DefaultTimeZone', ConvertUtils::WMHtmlSpecialChars($this->DefaultTimeZone)));
			$webmail->AppendChild(new XmlDomNode('AllowUsersChangeTimeZone', (int) $this->AllowUsersChangeTimeZone));
			$webmail->AppendChild(new XmlDomNode('DefaultUserCharset', ConvertUtils::GetCodePageNumber($this->DefaultUserCharset)));
			$webmail->AppendChild(new XmlDomNode('AllowUsersChangeCharset',(int) $this->AllowUsersChangeCharset));
			$webmail->AppendChild(new XmlDomNode('AllowDHTMLEditor', (int) $this->AllowDhtmlEditor));
			$webmail->AppendChild(new XmlDomNode('AllowUsersChangeEmailSettings', (int) $this->AllowUsersChangeEmailSettings));
			$webmail->AppendChild(new XmlDomNode('AllowDirectMode', (int) $this->AllowDirectMode));
			$webmail->AppendChild(new XmlDomNode('DirectModeIsDefault', (int) $this->DirectModeIsDefault));
			$webmail->AppendChild(new XmlDomNode('AllowNewUsersRegister', (int) $this->AllowNewUsersRegister));
			$webmail->AppendChild(new XmlDomNode('AllowUsersAddNewAccounts', (int) $this->AllowUsersAddNewAccounts));
			$webmail->AppendChild(new XmlDomNode('StoreMailsInDb', (int) $this->StoreMailsInDb));
			$webmail->AppendChild(new XmlDomNode('EnableWmServer', (int) $this->EnableWmServer));
			$webmail->AppendChild(new XmlDomNode('WmServerRootPath', ConvertUtils::WMHtmlSpecialChars($this->WmServerRootPath)));
			$webmail->AppendChild(new XmlDomNode('WmServerHost', ConvertUtils::WMHtmlSpecialChars($this->WmServerHost)));
			$webmail->AppendChild(new XmlDomNode('WmAllowManageXMailAccounts', (int) $this->WmAllowManageXMailAccounts));
			$webmail->AppendChild(new XmlDomNode('AllowContacts', (int) $this->AllowContacts));
			$webmail->AppendChild(new XmlDomNode('AllowCalendar', (int) $this->AllowCalendar));
			$xmlDocument->XmlRoot->AppendChild($webmail);
			
			$calendar = new XmlDomNode('Calendar');
			$calendar->AppendChild(new XmlDomNode('DefaultTimeFormat', (int) $this->Cal_DefaultTimeFormat));
			$calendar->AppendChild(new XmlDomNode('DefaultDateFormat', (int) $this->Cal_DefaultDateFormat));
			$calendar->AppendChild(new XmlDomNode('ShowWeekends', (int) $this->Cal_ShowWeekends));
			$calendar->AppendChild(new XmlDomNode('WorkdayStarts', (int) $this->Cal_WorkdayStarts));
			$calendar->AppendChild(new XmlDomNode('WorkdayEnds', (int) $this->Cal_WorkdayEnds));
			$calendar->AppendChild(new XmlDomNode('ShowWorkDay', (int) $this->Cal_ShowWorkDay));
			$calendar->AppendChild(new XmlDomNode('WeekStartsOn', (int) $this->Cal_WeekStartsOn));
			$calendar->AppendChild(new XmlDomNode('DefaultTab', (int) $this->Cal_DefaultTab));
			$calendar->AppendChild(new XmlDomNode('DefaultCountry', ConvertUtils::WMHtmlSpecialChars($this->Cal_DefaultCountry)));
			$calendar->AppendChild(new XmlDomNode('DefaultTimeZone', (int) $this->Cal_DefaultTimeZone));
			$calendar->AppendChild(new XmlDomNode('AllTimeZones', (int) $this->Cal_AllTimeZones));
			$xmlDocument->XmlRoot->AppendChild($calendar);
		
			return $xmlDocument->SaveToFile(INI_DIR . '/settings/settings.xml');
		}
	}