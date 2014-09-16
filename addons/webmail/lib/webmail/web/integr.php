<?php

	@session_name('PHPWEBMAILSESSID');
	@session_start();

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	require_once(WM_ROOTPATH.'class_account.php');
	require_once(WM_ROOTPATH.'class_settings.php');
	require_once(WM_ROOTPATH.'class_mailprocessor.php');
	
	if (!isset($_SESSION[SESSION_LANG])) $_SESSION[SESSION_LANG] = 'English';
	
	if (file_exists(WM_ROOTPATH.'/lang/'.$_SESSION[SESSION_LANG].'.php'))
	{
		require_once(WM_ROOTPATH.'/lang/'.$_SESSION[SESSION_LANG].'.php');
	}
	else 
	{
		die('Cat\'t read '.$_SESSION[SESSION_LANG].'.php file');
	}
	
	define('START_PAGE_IS_MAILBOX', 0);
	define('START_PAGE_IS_NEW_MESSAGE', 1);
	define('START_PAGE_IS_SETTINGS', 2);
	define('START_PAGE_IS_CONTACTS', 3);
	define('START_PAGE_IS_CALENDAR', 4);
	
class CIntegration
{
	/**
	 * @var Account
	 */
	var $Account = null;
	
	/**
	 * @var string
	 */
	var $_webmailroot;
	
	/**
	 * @var string
	 */
	var $_errorMessage = '';
	
	function CIntegration($webmailrootpath = '')
	{
		$this->_webmailroot = (trim($webmailrootpath)) ? rtrim(trim($webmailrootpath), '/\\').'/' : '';
	}
	
	/**
	 * @param int $id
	 * @return Account
	 */
	function GetAccountById($id)
	{
		$acct = null;
		$dbStorage = &DbStorageCreator::CreateDatabaseStorage($acct);
			
		if ($dbStorage->Connect())
		{
			$acct = &$dbStorage->SelectAccountData($id);
			if ($acct)
			{
				$this->Account = &$acct;
			}
			else 
			{
				$this->SetError();
			}
		}
		else 
		{
			$this->SetError();
		}
		
		return $acct; 
	}
	
	/**
	 * @param string $email
	 * @param string $login
	 * @return Account
	 */	
	function GetAccountByMailLogin($email, $login)
	{
		$acct = null;
		$dbStorage = &DbStorageCreator::CreateDatabaseStorage($acct);
			
		if ($dbStorage->Connect())
		{
			$acct = &$dbStorage->SelectAccountFullDataByLogin($email, $login);
			if ($acct)
			{
				$this->Account = &$acct;
			}
			else 
			{
				$this->SetError();
			}
		}
		else 
		{
			$this->SetError();
		}
		
		return $acct; 
	}
	
	/**
	 * @param string $email
	 * @param string $login
	 * @param string $password
	 * @return bool
	 */
	function CreateUser($email, $login, $password)
	{
		$null = null;
			
		$account = &new Account();
		$account->Email = $email;
		$account->MailIncLogin = $login;
		$account->MailIncPassword = $password;
		$account->DefaultAccount = true;
		
		$this->Account = &$account;			
		
		$processor = &new MailProcessor($account);
		
		if ($processor && $processor->MailStorage->Connect())
		{
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
			
			if ($dbStorage && $dbStorage->Connect())
			{
				$validate = $account->ValidateData();
				if ($validate !== true)
				{
					$this->SetError($validate);
					return false;
				}	
				else 
				{
					$inboxSync = ($account->MailProtocol == MAILPROTOCOL_IMAP4)
						? FOLDERSYNC_AllHeadersOnly : FOLDERSYNC_NewEntireMessages;
					
					$user = &User::CreateUser();
					if ($user == null)
					{
						$this->SetError();
						return false;
					}
					else if ($user->CreateAccount($account, $inboxSync))
					{
						return true;	
					}
					else
					{
						User::DeleteUserSettings($user->Id);
					}
				}
			}				
		}

		$this->SetError();
		return false;
	}
	
	/**
	 * @param Account $account
	 * @return bool
	 */
	function CreateUserFromAccount(&$account)
	{
		$account->DefaultAccount = true;
		
		$null = null;
		$this->Account = &$account;
					
		$processor = &new MailProcessor($account);
		if ($processor && $processor->MailStorage->Connect())
		{
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
			if ($dbStorage && $dbStorage->Connect())
			{
				$validate = $account->ValidateData();
				if ($validate !== true)
				{
					$this->SetError($validate);
					return false;
				}
				else 
				{
					$inboxSync = ($account->MailProtocol == MAILPROTOCOL_IMAP4)
						? FOLDERSYNC_AllHeadersOnly : FOLDERSYNC_NewEntireMessages;					
					
					$user = &User::CreateUser();
					if ($user == null)
					{
						$this->SetError();
						return false;
					}
					else if ($user->CreateAccount($account, $inboxSync))
					{
						return true;
					}
					else
					{
						User::DeleteUserSettings($user->Id);
					}
				}
			}
		}
		$this->SetError();
		return false;	
	}
	
	/**
	 * @param string $email
	 * @param string $login
	 * @param int $startPage
	 * @param string $password optional
	 * @return bool
	 */
	function UserLoginByEmail($email, $login, $startPage = START_PAGE_IS_MAILBOX, $password = null, $toEmail = null)
	{
		$newAccount = new Account();
		$settings =& Settings::CreateInstance();
		if (!$settings || !$settings->isLoad) 
		{
			$this->SetError(PROC_CANT_GET_SETTINGS);
			return false;
		}
		$getTemp = '';
		switch ($startPage)
		{
			default:
				$getTemp = '&start='.START_PAGE_IS_MAILBOX; 
				break;
			case START_PAGE_IS_NEW_MESSAGE:
				if ($toEmail && strlen($toEmail) > 0)
				{
					$getTemp = '&start='.START_PAGE_IS_NEW_MESSAGE.'&to='.$toEmail; 
				}
				else
				{
					$getTemp = '&start='.START_PAGE_IS_NEW_MESSAGE;
				}
				break;
			case START_PAGE_IS_MAILBOX:
			case START_PAGE_IS_SETTINGS:
			case START_PAGE_IS_CONTACTS:
			case START_PAGE_IS_CALENDAR:
				$getTemp = '&start='.$startPage;
				break;
		}
		
		$loginArray = &Account::LoadFromDbByLogin($email, $login);
		if ($loginArray != null)
		{
			if ($loginArray[2] == '1')
			{
				if ($password == null)
				{
					$_SESSION[ACCOUNT_ID] = $loginArray[0];
					$_SESSION[USER_ID] = $loginArray[3];
					$this->ChangeLocation($settings, $getTemp);
					return true;
				}	
				else if ($password == ConvertUtils::DecodePassword($loginArray[1], $newAccount))
				{
					$_SESSION[ACCOUNT_ID] = $loginArray[0];
					$_SESSION[USER_ID] = $loginArray[3];
					$this->ChangeLocation($settings, $getTemp);
					return true;
				}
				else
				{
					
					$account =& Account::LoadFromDb($loginArray[0]);;
					$account->MailIncPassword = $password;

					$newprocessor = &new MailProcessor($account);
					
					if ($newprocessor->MailStorage->Connect())
					{
						$_SESSION['id_account'] = $loginArray[0];
						$_SESSION[SESSION_LANG] = $account->DefaultLanguage;
					
						if ($account->Update())
						{
							$this->ChangeLocation($settings, $getTemp);
							return true;
						}
						else 
						{
							$this->SetError(getGlobalError());
							return false;
						}
					}
					else 
					{
						$this->SetError(PROC_WRONG_ACCT_PWD);
						return false;
					}
				}
			}
			else 
			{
				$this->SetError(PROC_CANT_LOG_NONDEF);
				return false;
			}
		}

		if ($this->_errorMessage == '') $this->SetError();
		return false;
	}
	
	/**
	 * @param string $email
	 * @param string $login
	 * @return bool
	 */
	function UserExists($email, $login)
	{
		if ($this->GetAccountByMailLogin($email, $login))
		{
			return true;		
		}
		else 
		{
			$this->SetError();
			return false;
		}
	}
	
	/**
	 * @param string $settings
	 * @param string $getTemp
	 */
	function ChangeLocation(&$settings, $getTemp)
	{
		if ($settings->AllowAjax)
		{
			header('Location: '.$this->_webmailroot.'webmail.php?check=1'.$getTemp);
		}
		else 
		{
			header('Location: '.$this->_webmailroot.'basewebmail.php?check=1'.$getTemp);
		}
	}
	
	/**
	 * @return string
	 */
	function GetErrorString()
	{
		return $this->_errorMessage;
	}
	
	/**
	 * @param string $string
	 */
	function SetError($string = null)
	{
		$this->_errorMessage = ($string) ? $string : getGlobalError();
	}	
}
