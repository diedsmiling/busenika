<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	require_once(WM_ROOTPATH.'class_settings.php');
	require_once(WM_ROOTPATH.'class_mailstorage.php');
	require_once(WM_ROOTPATH.'class_imapstorage.php');
	require_once(WM_ROOTPATH.'class_pop3storage.php');
	require_once(WM_ROOTPATH.'class_folders.php');
	require_once(WM_ROOTPATH.'common/class_i18nstring.php');
	require_once(WM_ROOTPATH.'common/class_datetime.php');
	require_once(WM_ROOTPATH.'wmserver/class_wmserver.php');
	require_once(WM_ROOTPATH.'class_validate.php');
	
	define('MAILPROTOCOL_POP3', 0);
	define('MAILPROTOCOL_IMAP4', 1);
	define('MAILPROTOCOL_WMSERVER', 2);

	define('MAILMODE_DeleteMessagesFromServer', 0);
	define('MAILMODE_LeaveMessagesOnServer', 1);
	define('MAILMODE_KeepMessagesOnServer', 2);
	define('MAILMODE_DeleteMessageWhenItsRemovedFromTrash', 3);
	define('MAILMODE_KeepMessagesOnServerAndDeleteMessageWhenItsRemovedFromTrash', 4);

	define('SIGNATURE_OPTION_DontAdd', 0);
	define('SIGNATURE_OPTION_AddToAll', 1);
	define('SIGNATURE_OPTION_AddToNewOnly', 2);
	
	define('VIEW_MODE_WITHOUT_PREVIEW_PANE_NO_IMG', 0);
	define('VIEW_MODE_PREVIEW_PANE_NO_IMG', 1);
	define('VIEW_MODE_WITHOUT_PREVIEW_PANE', 2);
	define('VIEW_MODE_PREVIEW_PANE', 3);
	
	define('SUGGESTCONTACTS', 20);
	
	define('DEMOACCOUNTEMAIL', 'xxx@xxx');
	define('DEMOACCOUNTPASS', 'xxxxxx');
	define('DEMOACCOUNTALLOW', 0);
	
	
	class Account
	{
		/**
		 * @var int
		 */
		var $Id;

		/**
		 * @var int
		 */
		var $IdUser = 0;

		/**
		 * @var bool
		 */
		var $DefaultAccount = false;

		/**
		 * @var bool
		 */
		var $Deleted = false;
    
		/**
		 * @var string
		 */
		var $Email;
    
		/**
		 * @var short
		 */
		var $MailProtocol = MAILPROTOCOL_POP3;

		/**
		 * @var string
		 */
    	var $MailIncHost;

		/**
		 * @var string
		 */
		var $MailIncLogin;

		/**
		 * @var string
		 */
		var $MailIncPassword;
    
		/**
		 * @var short
		 */
		var $MailIncPort = 110;
    
		/**
		 * @var string
		 */
		var $MailOutHost;

		/**
		 * @var string
		 */
		var $MailOutLogin = '';

		/**
		 * @var string
		 */
		var $MailOutPassword = '';

		/**
		 * @var short
		 */
		var $MailOutPort = 25;
    
		/**
		 * @var bool
		 */
		var $MailOutAuthentication = 1;

		/**
		 * @var string
		 */
		var $FriendlyName;

		/**
		 * @var bool
		 */
		var $UseFriendlyName = 1;

		/**
		 * @var int
		 */
		var $DefaultOrder = 0;

		/**
		 * @var bool
		 */
		var $GetMailAtLogin = true;

		/**
		 * @var short
		 */
		var $MailMode = MAILMODE_LeaveMessagesOnServer;

		/**
		 * @var short
		 */
		var $MailsOnServerDays = 7;

		/**
		 * @var string
		 */
		var $Signature;

		/**
		 * @var short
		 */
		var $SignatureType = 1;

		/**
		 * @var short
		 */
		var $SignatureOptions = 0;

		/**
		 * @var bool
		 */
		var $HideContacts;

		/**
		 * @var string
		 */
		var $Delimiter = '/';

		/**
		 * @var short
		 */
		var $MailsPerPage;

		/**
		 * @var bool
		 */
		var $WhiteListing = false;

		/**
		 * @var bool
		 */
		var $XSpam = false;

		/**
		 * @var CDateTime
		 */
		var $LastLogin;

		/**
		 * @var int
		 */
		var $LoginsCount = 0;

		/**
		 * @var string
		 */
		var $DefaultSkin = DEFAULT_SKIN;

		/**
		 * @var string
		 */
		var $DefaultLanguage;

		/**
		 * @var string
		 */
		var $DefaultIncCharset = CPAGE_ISO8859_1;

		/**
		 * @var string
		 */
		var $DefaultOutCharset = CPAGE_ISO8859_1;

		/**
		 * @var short
		 */
		var $DefaultTimeZone;

		/**
		 * @var string
		 */
		var $DefaultDateFormat = 'Default';
		
		/**
		 * @var int
		 */
		var $DefaultTimeFormat = 1; // 0/1 - 24/12

		/**
		 * @var bool
		 */
		var $HideFolders;

		/**
		 * @var long
		 */
		var $MailboxLimit;

		/**
		 * @var long
		 */
		var $MailboxSize = 0;

		/**
		 * @var bool
		 */
		var $AllowChangeSettings = true;

		/**
		 * @var bool
		 */
		var $AllowDhtmlEditor = true;

		/**
		 * @var bool
		 */
		var $AllowDirectMode;
		
		/**
		 * @var string
		 */
		var $DbCharset = CPAGE_UTF8;
		
		/**
		 * @var int
		 */
		var $HorizResizer = 150;
		
		/**
		 * @var int
		 */
		var $VertResizer = 115;
		
		/**
		 * @var int
		 */
		var $Mark;
		
		/**
		 * @var int
		 */
		var $Reply;
		
		/**
		 * @var int
		 */
		var $ContactsPerPage = 20;
		
		/**
		 * @var short
		 */
		var $ViewMode = VIEW_MODE_PREVIEW_PANE_NO_IMG;
		
		/**
		 * @var array
		 */
		var $Columns;
		
		/**
		 * @var bool
		 */
		var $IsDemo = false;
		
		/**
		 * @return Account
		 */
		function Account()
		{
			$settings =& Settings::CreateInstance();
			
			$this->MailsPerPage = ((int) $settings->MailsPerPage > 0) ? (int) $settings->MailsPerPage : 20;
			$this->DefaultSkin = $settings->DefaultSkin;
			$this->DefaultLanguage = $settings->DefaultLanguage;
			$this->DefaultTimeZone = $settings->DefaultTimeZone;
			$this->MailboxLimit = $settings->MailboxSizeLimit;
			$this->AllowDirectMode = $settings->AllowDirectMode;
			$this->AllowChangeSettings = $settings->AllowUsersChangeEmailSettings;
			
			$this->MailIncHost = $settings->IncomingMailServer;
			$this->MailIncPort = $settings->IncomingMailPort;
			$this->MailOutHost = $settings->OutgoingMailServer;
			$this->MailOutPort = $settings->OutgoingMailPort;
			
			$this->MailProtocol = $settings->IncomingMailProtocol;
			
			$this->DefaultIncCharset = $settings->DefaultUserCharset;
			$this->DefaultOutCharset = $settings->DefaultUserCharset;
			
			$this->MailOutAuthentication = $settings->ReqSmtpAuth;
			
			$this->Columns = array();
			
			$this->IsDemo = false;
			
			$this->AllowDhtmlEditor = $settings->AllowDhtmlEditor;
		}
		
		/**
		 * @return string
		 */
		function GetDefaultIncCharset()
		{
			if ($this->DefaultIncCharset == 'default')
			{
				return CPAGE_ISO8859_1;
			}
			return $this->DefaultIncCharset;
		}
		
		/**
		 * @return string
		 */
		function GetDefaultOutCharset()
		{
			if ($this->DefaultOutCharset == 'default')
			{
				return CPAGE_UTF8;
			}
			return $this->DefaultOutCharset;
		}

		/**
		 * @return string
		 */
		function GetUserCharset()
		{
			return CPAGE_UTF8;
		}
		
		/**
		 * @return short
		 */
		function GetDefaultTimeOffset($otherTimeZone = null)
		{
			$timeArray = localtime(time(), true);
			
			$daylightSaveMinutes = $timeArray['tm_isdst']*60;
			
			$timeOffset = 0;
			
			$varForSwitch = ($otherTimeZone !== null)  ? $otherTimeZone : $this->DefaultTimeZone;
			
			switch ($varForSwitch)
			{
				default:
				case 0:
					return (ConvertUtils::GmtMkTime()-mktime())/60;
					break;
				case 1:
					$timeOffset =  -12*60;
					break;
				case 2:
					$timeOffset =  -11*60;
					break;
				case 3:
					$timeOffset =  -10*60;
					break;
				case 4:
					$timeOffset =  -9*60;
					break;
				case 5:
					$timeOffset =  -8*60;
					break;
				case 6:
				case 7:
					$timeOffset =  -7*60;
					break;
				case 8:
				case 9:
				case 10:
				case 11:
					$timeOffset =  -6*60;
					break;
				case 12:
				case 13:
				case 14:
					$timeOffset =  -5*60;
					break;
				case 15:
				case 16:
				case 17:
					$timeOffset =  -4*60;
					break;
				case 18:
					$timeOffset =  -3.5*60;
					break;
				case 19:
				case 20:
				case 21:
					$timeOffset =  -3*60;
					break;
				case 22:
					$timeOffset =  -2*60;
					break;
				case 23:
				case 24:
					$timeOffset =  -60;
					break;
				case 25:
				case 26:
					$timeOffset =  0;
					break;
				case 27:
				case 28:
				case 29:
				case 30:
				case 31:
					$timeOffset =  60;
					break;
				case 32:
				case 33:
				case 34:
				case 35:
				case 36:
				case 37:
					$timeOffset =  2*60;
					break;
				case 38:
				case 39:
				case 40:
				case 41:
					$timeOffset =  3*60;
					break;
				case 42:
					$timeOffset =  3.5*60;
					break;
				case 43:
				case 44:
					$timeOffset =  4*60;
					break;
				case 45:
					$timeOffset =  4.5*60;
					break;
				case 46:
				case 47:
					$timeOffset =  5*60;
					break;
				case 48:
					$timeOffset =  5.5*60;
					break;
				case 49:
					$timeOffset =  5*60+45;
					break;
				case 50:
				case 51:
				case 52:
					$timeOffset =  6*60;
					break;
				case 53:
					$timeOffset =  6.5*60;
				case 54:
				case 55:
					$timeOffset =  7*60;
					break;
				case 56:
				case 57:
				case 58:
				case 59:
				case 60:
					$timeOffset =  8*60;
					break;
				case 61:
				case 62:
				case 63:
					$timeOffset =  9*60;
					break;
				case 64:
				case 65:
					$timeOffset =  9.5*60;
					break;
				case 66:
				case 67:
				case 68:
				case 69:
				case 70:
					$timeOffset =  10*60;
					break;
				case 71:
					$timeOffset =  11*60;
					break;
				case 72:
				case 73:
					$timeOffset =  12*60;
					break;
				case 74:
					$timeOffset =  13*60;
					break;
			}
			
			return $timeOffset + $daylightSaveMinutes;
			
		}

		/**
		 * @param short $pop3InboxSyncType optional
		 * @return bool
		 */
		function Update($pop3InboxSyncType = null)
		{
			if ($this->IsDemo)
			{
				return true;
			}
			
			$result = true;
			
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($this);
			
			if ($dbStorage->Connect())
			{
				if (($this->MailProtocol == MAILPROTOCOL_POP3 && $pop3InboxSyncType != null) || ($this->MailProtocol == MAILPROTOCOL_WMSERVER && $pop3InboxSyncType != null))
				{
					$folders = &$dbStorage->GetFolders();
					$inboxFolder = &$folders->GetFolderByType(FOLDERTYPE_Inbox);
					$inboxFolder->SyncType = $pop3InboxSyncType;
					$result = $dbStorage->UpdateFolder($inboxFolder);
				}
				
				if ($this->DefaultAccount && 
					$dbStorage->SelectAccountsCountByLogin($this->Email, $this->MailIncLogin, true, $this->Id) > 0)
				{
					setGlobalError(ACCT_CANT_UPD_TO_DEF_ACCT);
					return false;
				}
				elseif (!$this->DefaultAccount)
				{
					$accounts = $dbStorage->SelectAccounts($this->IdUser);
					if (is_array($accounts) && count($accounts) > 0)
					{
						$defArray = array();
						foreach ($accounts As $id_acct => $mainAcct)
						{
							if ($mainAcct[6])
							{
								$defArray[] = $id_acct;
							}
						}
						
						if (count($defArray) < 2)
						{
							if (in_array($this->Id, $defArray))	
							{
								$this->DefaultAccount = true;
							}
						}
					}					
				}
				
				if ($this->MailProtocol == MAILPROTOCOL_WMSERVER)
				{
					$settings =& Settings::CreateInstance();
					if ($settings->EnableWmServer)
					{
						$WMServer = new CWmServerConsole();
						$domainName = ConvertUtils::ParseEmail($this->Email);
						if ($domainName)
						{
							if ($WMServer->Connect() && $WMServer->ChangeUserPass($domainName[1], $this->MailIncLogin, $this->MailIncPassword) && 
								$WMServer->ChangeUserMaxMailBoxSize($domainName[1], 
								EmailAddress::GetAccountNameFromEmail($this->MailIncLogin), $this->MailboxLimit)) 
							{} else 
							{
								setGlobalError(PROC_CANT_UPDATE_ACCT.' '.$WMServer->GetError());
								return false;
							}
						}
					}
					else 
					{
						setGlobalError(ServerIsDisable);
						return false;						
					}
				}
				
				if ($dbStorage->UpdateAccountData($this))
				{
					return $result;
				}
			}
			return false;
		}

		/**
		 * @static 		 
		 * @param int $id
		 * @return Account
		 */
		function &LoadFromDb($id)
		{
			$account = null;
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
			
			if ($dbStorage->Connect())
			{
				$account = &$dbStorage->SelectAccountData($id);
				
				if ($account && strtolower($account->Email) == DEMOACCOUNTEMAIL)
				{
					$account->IsDemo = DEMOACCOUNTALLOW;
					if (isset($_SESSION[DEMO_SES]))
					{
						$acctDArray = $_SESSION[DEMO_SES];
						
						$account->MailsPerPage = isset($acctDArray[DEMO_S_MessagesPerPage]) ? $acctDArray[DEMO_S_MessagesPerPage] : $account->MailsPerPage;
						$account->AllowDhtmlEditor = isset($acctDArray[DEMO_S_AllowDhtmlEditor]) ? $acctDArray[DEMO_S_AllowDhtmlEditor] : $account->AllowDhtmlEditor;
						$account->DefaultSkin = isset($acctDArray[DEMO_S_DefaultSkin]) ? $acctDArray[DEMO_S_DefaultSkin] : $account->DefaultSkin;
						$account->DefaultOutCharset = isset($acctDArray[DEMO_S_DefaultOutCharset]) ? $acctDArray[DEMO_S_DefaultOutCharset] : $account->DefaultOutCharset;
						$account->DefaultTimeZone = isset($acctDArray[DEMO_S_DefaultTimeZone]) ? $acctDArray[DEMO_S_DefaultTimeZone] : $account->DefaultTimeZone;
						$account->DefaultLanguage = isset($acctDArray[DEMO_S_DefaultLanguage]) ? $acctDArray[DEMO_S_DefaultLanguage] : $account->DefaultLanguage;
						$account->DefaultDateFormat = isset($acctDArray[DEMO_S_DefaultDateFormat]) ? $acctDArray[DEMO_S_DefaultDateFormat] : $account->DefaultDateFormat;						
						$account->DefaultTimeFormat = isset($acctDArray[DEMO_S_DefaultTimeFormat]) ? $acctDArray[DEMO_S_DefaultTimeFormat] : $account->DefaultTimeFormat;						
						$account->ViewMode = isset($acctDArray[DEMO_S_ViewMode]) ? $acctDArray[DEMO_S_ViewMode] : $account->ViewMode;						
						
						$account->ContactsPerPage = isset($acctDArray[DEMO_S_ContactsPerPage]) ? $acctDArray[DEMO_S_ContactsPerPage] : $account->ContactsPerPage;
					}
				}
			}
			
			return $account;
		}

		/**
		 * @static 
		 * @param string $email
		 * @param string $login
		 * @return Array
		 */
		function &LoadFromDbByLogin($email, $login)
		{
			$null = null;
			$false = false;
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
			if ($dbStorage->Connect())
			{
				$array = &$dbStorage->SelectAccountDataByLogin($email, $login, true);
				return $array;
			}
			else
			{
				return $false;		
			}
			return $null;
		}
		
		/**
		 * @static 
		 * @param string $email
		 * @return Array
		 */
		function &LoadFromDbOnlyByEmail($email)
		{
			$null = null;
			$false = false;
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
			if ($dbStorage->Connect())
			{
				$array = &$dbStorage->SelectAccountDataOnlyByEmail($email);
				return $array;
			}
			else
			{
				return $false;		
			}
			return $null;
		}
		
		/**
		 * @static 
		 * @param int $id
		 * @return bool
		 */
		function DeleteFromDb($id, $deleteDemo = false)
		{
			$account = &Account::LoadFromDb($id);
			if (!$deleteDemo && $account->IsDemo)
			{
				return true;
			}
			$null = null;
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
			if ($dbStorage->Connect())
			{
				$settings =& Settings::CreateInstance();
				if ($settings->EnableWmServer && $settings->WmAllowManageXMailAccounts)
				{
					if ($account && $account->MailProtocol == MAILPROTOCOL_WMSERVER)
					{
						$WMConsole = new CWmServerConsole();
						
						if (!$WMConsole->Connect())
						{
							setGlobalError(PROC_CANT_DEL_ACCT_BY_ID);
							return false;							
						}
						$domain = ConvertUtils::ParseEmail($account->Email);
						if ($domain)
						{
							$WMConsole->DeleteUser($domain[1], EmailAddress::GetAccountNameFromEmail($account->MailIncLogin));
						}
					}
				}
				
				if ($dbStorage->DeleteAccountData($id))
				{
					return true;
				}
				else 
				{
					setGlobalError(PROC_CANT_DEL_ACCT_BY_ID);
				}
			}
			return false;
		}
		
		/**
		 * @param int $id
		 * @return bool
		 */
		function LoadUserSettings($userId)
		{
			$null = null;
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
			return $dbStorage->Connect() && $dbStorage->SelectSetings($this, $userId);
		}
		
		/**
		 * @return string/boot
		 */
		function ValidateData()
		{
			
			if (!ConvertUtils::CheckFileName($this->Email))
			{
				return JS_LANG_WarningCorrectEmail;
			}
			elseif(empty($this->Email))
			{
				return JS_LANG_WarningEmailFieldBlank;
			}
			elseif(!Validate::checkEmail($this->Email))
			{
				return JS_LANG_WarningCorrectEmail;
			}
			elseif(empty($this->MailIncLogin))
			{
				return WarningLoginFieldBlank;
			}
			elseif(empty($this->MailIncPassword))
			{
				return WarningPassBlank;
			}
			elseif(empty($this->MailIncHost))
			{
				return JS_LANG_WarningIncServerBlank;
			}
			elseif(!Validate::checkServerName($this->MailIncHost))
			{
				return WarningCorrectIncServer;
			}
			elseif(empty($this->MailIncPort))
			{
				return JS_LANG_WarningIncPortBlank;
			}
			elseif(!Validate::checkPort($this->MailIncPort))
			{
				return JS_LANG_WarningIncPortNumber.' '.JS_LANG_DefaultIncPortNumber;
			}
			elseif(empty($this->MailOutHost))
			{
				return WarningCorrectSMTPServer;
			}
			elseif(!Validate::checkServerName($this->MailOutHost))
			{
				return WarningCorrectSMTPServer;
			}
			elseif(empty($this->MailOutPort))
			{
				return JS_LANG_WarningOutPortBlank;
			}
			elseif(!Validate::checkPort($this->MailOutPort))
			{
				return JS_LANG_WarningOutPortNumber.' '.JS_LANG_DefaultOutPortNumber;
			}				
			return true;	
		}
	}
	
	class CalendarUser
	{

		/**
		 * @var int
		 */
		var $Id;
		
		/**
		 * @var int => 1-1PM 2-13:00
		 */
		var $TimeFormat;

		/**
		 * @var int 1-m/d/Y 2-d/m/Y 3-Y-m-d
		 */		
		var $DateFormat;

		/**
		 * @var bool
		 */		
		var $ShowWeekends;
		
		/**
		 * @var int 0-23
		 */
		var $WorkdayStarts;
				
		/**
		 * @var int 0-23
		 */
		var $WorkdayEnds;
		
		/**
		 * @var bool
		 */		
		var $ShowWorkday;
		
		/**
		 * @var int 0-Sunday 1-Monday
		 */
		var $WeekStartsOn;
				
		/**
		 * @var int 1-day 2-week 3-month
		 */
		var $DefaultTab;
		
		/**
		 * @var string
		 */
		var $Country;
			
		/**
		 * @var int
		 */
		var $TimeZone;
		
		/**
		 * @var bool
		 */		
		var $AllTimeZones;
		
		/**
		 * @return CalendarUser
		 */
		function CalendarUser()
		{
			$settings =& Settings::CreateInstance();
			if (!$settings || !$settings->isLoad) 
			{
				return null;
			}
			
			$this->TimeFormat = $settings->Cal_DefaultTimeFormat;
			$this->DateFormat = $settings->Cal_DefaultDateFormat;
			$this->ShowWeekends = $settings->Cal_ShowWeekends;
			$this->WorkdayStarts = $settings->Cal_WorkdayStarts;
			$this->WorkdayEnds = $settings->Cal_WorkdayEnds;
			$this->ShowWorkday = $settings->Cal_ShowWorkDay;
			$this->WeekStartsOn = $settings->Cal_WeekStartsOn;
			$this->DefaultTab = $settings->Cal_DefaultTab;
			$this->Country = $settings->Cal_DefaultCountry;
			$this->TimeZone = $settings->Cal_DefaultTimeZone;
			$this->AllTimeZones = $settings->Cal_AllTimeZones;
		}
	}
  
	class User
	{
		/**
		 * @var int
		 */
		var $Id;	
		
		/**
		 * @var bool
		 */
		var $Deleted = false;
		
		/**
		 * @static
		 * @param Account $account
		 * @return User
		 */
		function &CreateUser($account = null)
		{
			$user = &new User();
			//$calUser = new CalendarUser();

			$null = null;
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
			if ($dbStorage->Connect())
			{
				if ($dbStorage->InsertUserData($user))
				{
					if (!$account)
					{
						$account = &new Account();
					}
					$account->IdUser = $user->Id;
					$calUser->Id = $user->Id;
					
					if ($dbStorage->InsertSettings($account))
					{
						return $user;
					}
				}
			}
			return $null;
		}
		
		/**
		 * @param Account $account
		 * @param short $pop3InboxSyncType optional
		 * @return bool
		 */
		function CreateAccount(&$account, $inboxSyncType = FOLDERSYNC_NewEntireMessages)
		{
			$account->IdUser = $this->Id;
			$result = false;
			setGlobalError(PROC_ERROR_ACCT_CREATE);
			
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
			if ($dbStorage->Connect())
			{
				$defaultAccount = &$dbStorage->SelectAccountDataByLogin($account->Email, $account->MailIncLogin, true);
				
				if ($account->DefaultAccount && $defaultAccount != null && $defaultAccount[2] == 1)
				{
					setGlobalError(ACCT_CANT_ADD_DEF_ACCT);
					return false;
				}
				
				$settings =& Settings::CreateInstance();
				
				//Load or create folder tree here
				switch ($account->MailProtocol)
				{
					case MAILPROTOCOL_WMSERVER:
						
						if (!$settings->EnableWmServer)
						{
							setGlobalError(PROC_ERROR_ACCT_CREATE);
							return false;
						}
												
						$emailArray = ConvertUtils::ParseEmail($account->Email);
						if (!$emailArray)
						{
							setGlobalError(JS_LANG_WarningCorrectEmail);
							return false;
						}
						
						$WMConsole = new CWmServerConsole();
						
						if (!$WMConsole->Connect())
						{
							setGlobalError(PROC_ERROR_ACCT_CREATE.'<br />'.$WMConsole->GetError());
							return false;							
						}
						
						if ($settings->WmAllowManageXMailAccounts)
						{
							if ($WMConsole->DomainExist($emailArray[1]))
							{
								if (!$WMConsole->UserExist($emailArray[1], $account->MailIncLogin))
								{
									if (!$WMConsole->AddUser($emailArray[1], EmailAddress::GetAccountNameFromEmail($account->MailIncLogin), $account->MailIncPassword))
									{
										setGlobalError(PROC_ERROR_ACCT_CREATE.'<br />'.$WMConsole->GetError());
										return false;
									}								
								}
	
								if (!$WMConsole->ChangeUserMaxMailBoxSize($emailArray[1], EmailAddress::GetAccountNameFromEmail($account->MailIncLogin), $account->MailboxLimit))
								{
									setGlobalError(PROC_ERROR_ACCT_CREATE.'<br />'.$WMConsole->GetError());
									return false;
								}
							}
							else 
							{
								setGlobalError(DomainDosntExist.'<br />'.$WMConsole->GetError());
								return false;						
							}
						}
						
						$result = $dbStorage->InsertAccountData($account); 
						$folders = &new FolderCollection();		
						if ($result)	
						{						
							$inboxFolder = &new Folder($account->Id, -1, FOLDERNAME_Inbox, FOLDERNAME_Inbox);
							if ($settings->AllowDirectMode && $settings->DirectModeIsDefault) 
							{
								$inboxSyncType = FOLDERSYNC_DirectMode;
							}
							
							$inboxFolder->SyncType = $inboxSyncType;
							$folders->Add($inboxFolder);
							
							$folders->Add(new Folder($account->Id, -1, FOLDERNAME_SentItems, FOLDERNAME_SentItems, FOLDERSYNC_DontSync));
							$folders->Add(new Folder($account->Id, -1, FOLDERNAME_Drafts, FOLDERNAME_Drafts, FOLDERSYNC_DontSync));
							$folders->Add(new Folder($account->Id, -1, FOLDERNAME_Trash, FOLDERNAME_Trash, FOLDERSYNC_DontSync));					
						}
						break;
						
					case MAILPROTOCOL_POP3:
						
						$result = $dbStorage->InsertAccountData($account); 
						$folders = &new FolderCollection();
						if ($result)
						{
							$inboxFolder = &new Folder($account->Id, -1, FOLDERNAME_Inbox, FOLDERNAME_Inbox);
							if ($settings->AllowDirectMode && $settings->DirectModeIsDefault) 
							{
								$inboxSyncType = FOLDERSYNC_DirectMode;
							}
							
							$inboxFolder->SyncType = $inboxSyncType;
							$folders->Add($inboxFolder);
							
							$folders->Add(new Folder($account->Id, -1, FOLDERNAME_SentItems, FOLDERNAME_SentItems, FOLDERSYNC_DontSync));
							$folders->Add(new Folder($account->Id, -1, FOLDERNAME_Drafts, FOLDERNAME_Drafts, FOLDERSYNC_DontSync));
							$folders->Add(new Folder($account->Id, -1, FOLDERNAME_Trash, FOLDERNAME_Trash, FOLDERSYNC_DontSync));
						}
						break;
						
					case MAILPROTOCOL_IMAP4:
						setGlobalError(ACCT_CANT_CREATE_IMAP_ACCT);
						
						$mailStorage = &new ImapStorage($account);
						if ($mailStorage->Connect())
						{
							$result = $dbStorage->InsertAccountData($account);
							if (!$result)
							{
								return false;
							}
							$folders =& $mailStorage->GetFolders();
							
							$folders->SetSyncTypeToAll($inboxSyncType);
							
							if ($folders && $settings->AllowDirectMode && $settings->DirectModeIsDefault) 
							{
								$folders->SetSyncTypeToAll(FOLDERSYNC_DirectMode);
							}
							
							$result &= $account->Update();
							$result &= $folders != null;
							
							$mailStorage->Disconnect();
							
							if (!$result)
							{
								return false;
							}
							
							$hasSentItems = false;
							$hasDrafts = false;
							$hasTrash = false;
							
							$s = $d = $t = null;
							
							$s =& $folders->GetFolderByType(FOLDERTYPE_SentItems);
							$d =& $folders->GetFolderByType(FOLDERTYPE_Drafts);
							if (USEIMAPTRASH)
							{
								$t =& $folders->GetFolderByType(FOLDERTYPE_Trash);
							}
							
							/*
							for ($i = 0, $c = $folders->Count(); $i < $c; $i++)
							{
								$folder = &$folders->Get($i);
								if (strtolower($folder->FullName) == strtolower(FOLDERNAME_Inbox) && $folder->SubFolders != null)
								{
									for ($j = 0, $k = $folder->SubFolders->Count(); $j < $k; $j++)
									{
										$inboxFolder =& $folder->SubFolders->Get($j);
										switch(strtolower($inboxFolder->Name))
										{
											case strtolower(FOLDERNAME_Sent):
											case strtolower(FOLDERNAME_SentItems):
												$hasSentItems = true;
												break;
											case strtolower(FOLDERNAME_Drafts):
												$hasDrafts = true;
												break;
											case strtolower(FOLDERNAME_Trash):
												$hasTrash = true;
												break;
										}
									}
								}
								
								switch(strtolower($folder->FullName))
								{
									case strtolower(FOLDERNAME_Sent):
									case strtolower(FOLDERNAME_SentItems):
										$hasSentItems = true;
										break;
									case strtolower(FOLDERNAME_Drafts):
										$hasDrafts = true;
										break;
									case strtolower(FOLDERNAME_Trash):
										$hasTrash = true;
										break;
								}
								
								if ($hasSentItems && $hasDrafts)
								{
									if (USEIMAPTRASH)
									{
										if ($hasTrash)
										{
											break;
										}
									}
									else
									{
										break;
									}
								}
							}
							
							if (!$hasSentItems)
							{
								$sentFolder =& new Folder($account->Id, -1, FOLDERNAME_SentItems, FOLDERNAME_SentItems, FOLDERSYNC_DontSync);
								$folders->Add($sentFolder);
							}

							if (!$hasDrafts)
							{
								$draftsFolder =& new Folder($account->Id, -1, FOLDERNAME_Drafts, FOLDERNAME_Drafts, FOLDERSYNC_DontSync);
								$folders->Add($draftsFolder);
							}
							
							if (USEIMAPTRASH && !$hasTrash)
							{
								$draftsFolder =& new Folder($account->Id, -1, FOLDERNAME_Trash, FOLDERNAME_Trash, FOLDERSYNC_DontSync);
								$folders->Add($draftsFolder);
							}*/
							
							if ($s === null)
							{
								$sentFolder =& new Folder($account->Id, -1, FOLDERNAME_SentItems, FOLDERNAME_SentItems, FOLDERSYNC_DontSync);
								$folders->Add($sentFolder);
							}
							
							if ($d === null)
							{
								$draftsFolder =& new Folder($account->Id, -1, FOLDERNAME_Drafts, FOLDERNAME_Drafts, FOLDERSYNC_DontSync);
								$folders->Add($draftsFolder);
							}
							
							if (USEIMAPTRASH && $t === null)
							{
								$trashFolder =& new Folder($account->Id, -1, FOLDERNAME_Trash, FOLDERNAME_Trash, FOLDERSYNC_DontSync);
								$folders->Add($trashFolder);
							}
						}
						else
						{
							return false;
						}
						break;
						
					default:
						return false;
				}
				
				if ($result)
				{
					$folders = $folders->SortRootTree();
					$result &= $dbStorage->CreateFolders($folders);
				}	
			}
			if ($result) setGlobalError('');
			return $result;
		}
		
		/**
		 * @static
		 * @param int $id
		 * @return bool
		 */
		function DeleteUser($id)
		{
			$null = null;
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
			if ($dbStorage->Connect())
			{
				if ($dbStorage->DeleteUserData($id))
				{
					return true;
				}
			}
			return false;
		}
		
		/**
		 * @static
		 * @param int $id
		 * @return bool
		 */
		function DeleteUserSettings($id)
		{
			$null = null;
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
			if ($dbStorage->Connect())
			{
				if ($dbStorage->DeleteSettingsData($id))
				{
					return true;
				}
			}
			return false;
		}
	}