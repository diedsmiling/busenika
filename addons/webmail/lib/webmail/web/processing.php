<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	header('Content-type: text/xml; charset=utf-8');

	function disable_magic_quotes_gpc()
	{
		if (@get_magic_quotes_gpc() == 1)
		{
			$_GET = array_map('stripslashes' , $_GET);
			$_POST = array_map('stripslashes' , $_POST);
		}
	}

	@disable_magic_quotes_gpc();

	require_once(WM_ROOTPATH.'common/class_xmldocument.php');
	require_once(WM_ROOTPATH.'class_account.php');
	require_once(WM_ROOTPATH.'class_webmailmessages.php');
	require_once(WM_ROOTPATH.'class_mailprocessor.php');
	require_once(WM_ROOTPATH.'class_filters.php');
	require_once(WM_ROOTPATH.'class_contacts.php');
	require_once(WM_ROOTPATH.'class_filesystem.php');
	require_once(WM_ROOTPATH.'common/class_i18nstring.php');
	require_once(WM_ROOTPATH.'common/class_convertutils.php');
	require_once(WM_ROOTPATH.'common/inc_constants.php');
	require_once(WM_ROOTPATH.'class_smtp.php');
	require_once(WM_ROOTPATH.'class_validate.php');

	$null = null;
	$log =& CLog::CreateInstance();
	
	@ob_start('obLogResponse');
	
	$xmlRes = &new XmlDocument();
	$xmlRes->CreateElement('webmail');

	$xml = isset($_POST['xml']) ? $_POST['xml'] : '';
	$log->WriteLine("<<<[not_parsed_from_client]<<<\r\n".$xml);
	
	$xmlObj = &new XmlDocument();
	$xmlObj->ParseFromString($xml);
	$log->WriteLine("<<<[to_server]<<<\r\n".$xmlObj->ToString(true));
	
	$settings = &Settings::CreateInstance();
	if (!$settings || !$settings->isLoad)
	{
		printErrorAndExit('', $xmlRes, 3);
	}
	
	session_name('PHPWEBMAILSESSID');
	@session_start();
	
	if (!isset($_SESSION[ACCOUNT_ID]))
	{
		@session_start();
		if (!isset($_SESSION[ACCOUNT_ID]) && $xmlObj->GetParamValueByName('action') != 'login')
		{
			$xmlRes->XmlRoot->AppendChild(new XmlDomNode('session_error'));
			printXML($xmlRes);
		}
	}

	$_SESSION['attachtempdir'] = isset($_SESSION['attachtempdir']) ? $_SESSION['attachtempdir'] : md5(session_id());
	
	if (isset($_SESSION[ACCOUNT_ID])) 
	{
		$accountId = $_SESSION[ACCOUNT_ID];
	}
		
	if (!$settings->IncludeLang())
	{
		printErrorAndExit('', $xmlRes, 6);
	}

	switch ($xmlObj->GetParamValueByName('action'))
	{
		case 'login':
	
			$isNoLoginField = false;
			$sendSettingsList = false;
				
			$xmlEmail = trim($xmlObj->GetParamTagValueByName('email'));
			$xmlLogin = trim($xmlObj->GetParamTagValueByName('mail_inc_login'));
			$xmlPass = $xmlObj->GetParamTagValueByName('mail_inc_pass');
			$xmlAdvancedLogin = (bool) $xmlObj->GetParamValueByName('advanced_login');
				
			if ($xmlAdvancedLogin && $settings->AllowAdvancedLogin)
			{
				$email = $xmlEmail;
				$login = $xmlLogin;
			}
			else
			{
				switch ($settings->HideLoginMode)
				{
					case 0:
						$email = $xmlEmail;
						$login = $xmlLogin;
						break;
							
					case 10:
						$email = $xmlEmail;
						$isNoLoginField = true;
	
						$emailAddress = &new EmailAddress();
						$emailAddress->SetAsString($email);
	
						$optLogin = $emailAddress->GetAccountName();
						break;
	
					case 11:
						$email = $xmlEmail;
						$isNoLoginField = true;
	
						$optLogin = $email;
						break;
	
					case 20:
					case 21:
						$login = $xmlLogin;
						$email = $login.'@'.$settings->DefaultDomainOptional;
						break;
	
					case 22:
					case 23:
						$login = $xmlLogin.'@'.$settings->DefaultDomainOptional;
						$email = $login;
				}
			}
	
			if ($isNoLoginField)
			{
				$loginArray = &Account::LoadFromDbOnlyByEmail($email);
				if (is_array($loginArray) && count($loginArray) > 3)
				{
					$eAccount = &Account::LoadFromDb((int) $loginArray[0]);
					if ($eAccount)
					{
						$mailIncPass = $xmlPass;
						$login = (ConvertUtils::DecodePassword($loginArray[1], $eAccount) == $mailIncPass)
									? $loginArray[4] : $optLogin;
					}
					else
					{
						$login = $optLogin;
					}
				}
				else
				{
					$login = $optLogin;
				}
			}
			else
			{
				$loginArray = &Account::LoadFromDbByLogin($email, $login);
			}
	
			if ($loginArray === false)
			{
				printErrorAndExit(getGlobalError(), $xmlRes);
			}
			else if ($loginArray === null)
			{
				if ($settings->AllowNewUsersRegister)
				{
					$account = &new Account();
					$account->DefaultAccount = true;
					$account->Email = $email;
					$account->MailIncLogin = $login;
					$account->MailIncPassword = $xmlPass;
						
					if ($xmlAdvancedLogin && $settings->AllowAdvancedLogin)
					{
						$account->MailProtocol = (int) $xmlObj->GetParamValueByName('mail_protocol');
						$account->MailIncPort = (int) $xmlObj->GetParamValueByName('mail_inc_port');
						$account->MailOutPort = (int) $xmlObj->GetParamValueByName('mail_out_port');
						$account->MailOutAuthentication = (bool) $xmlObj->GetParamValueByName('mail_out_auth');
						$account->MailIncHost = $xmlObj->GetParamTagValueByName('mail_inc_host');
						$account->MailOutHost = $xmlObj->GetParamTagValueByName('mail_out_host');
					}
					else
					{
						$account->MailProtocol = (int) $settings->IncomingMailProtocol;
						$account->MailIncPort = (int) $settings->IncomingMailPort;
						$account->MailOutPort = (int) $settings->OutgoingMailPort;
						$account->MailOutAuthentication = (bool) $settings->ReqSmtpAuth;
						$account->MailIncHost = $settings->IncomingMailServer;
						$account->MailOutHost = $settings->OutgoingMailServer;
					}
						
					if (DEMOACCOUNTALLOW && $email == DEMOACCOUNTEMAIL)
					{
						$account->MailIncPassword = DEMOACCOUNTPASS;
					}
						
					if ($settings->EnableWmServer)
					{
						$WMConsole = new CWmServerConsole();
						if ($WMConsole->Connect())
						{
							$domains = $WMConsole->DomainList();
							$domain = EmailAddress::GetDomainFromEmail($account->Email);
							if (in_array($domain, $domains))
							{
								$account->MailProtocol = MAILPROTOCOL_WMSERVER;
								$account->MailOutLogin = $account->Email;
								$account->MailOutPassword = $account->MailIncPassword;
								$account->MailOutHost = $settings->WmServerHost;
								$account->MailOutPort = $WMConsole->Settings->OutPort;
							}
							$WMConsole->Disconnect();
						}
						else
						{
							printErrorAndExit($WMConsole->GetError(), $xmlRes);
						}
					}
					
					$validate = $account->ValidateData();
					if ($validate !== true)
					{
						printErrorAndExit($validate, $xmlRes);
					}
					else
					{
						$processor = &new MailProcessor($account);
	
						if (($account->MailProtocol == MAILPROTOCOL_WMSERVER && $settings->WmAllowManageXMailAccounts) || $processor->MailStorage->Connect(true))
						{
							$user = &User::CreateUser();
	
							if ($user != null)
							{
								$account->IdUser = $user->Id;
							}
	
							$folderSync = FOLDERSYNC_AllEntireMessages;
							if ($account->MailProtocol == MAILPROTOCOL_IMAP4)
							{
								$folderSync = FOLDERSYNC_AllHeadersOnly;
							}
								
							$inboxSyncType = ($settings->AllowDirectMode &&	$settings->DirectModeIsDefault)?
								FOLDERSYNC_DirectMode : $folderSync;
							if ($user != null && $user->CreateAccount($account, $inboxSyncType))
							{
								$_SESSION[ACCOUNT_ID] = $account->Id;
								$_SESSION[USER_ID] = $account->IdUser;
								$_SESSION[SESSION_LANG] = $account->DefaultLanguage;
								$sendSettingsList = true;
							}
							else
							{
								if ($user != null)
								{
									User::DeleteUserSettings($user->Id);
								}
								printErrorAndExit(CantCreateUser, $xmlRes);
							}
						}
						else
						{
							printErrorAndExit(getGlobalError(), $xmlRes);
						}
					}
				}
				else
				{
					printErrorAndExit(CantCreateAccount, $xmlRes);
				}
			}
			elseif ($loginArray[2] == 0)
			{
				printErrorAndExit(PROC_CANT_LOG_NONDEF, $xmlRes);
			}
			else
			{
				$newAccount = &Account::LoadFromDb($loginArray[0]);
				if (!$newAccount)
				{
					printErrorAndExit(getGlobalError(), $xmlRes);
				}
				else
				{
					$mailIncPass = $xmlPass;
	
					if (DEMOACCOUNTALLOW && $email == DEMOACCOUNTEMAIL)
					{
						$mailIncPass = DEMOACCOUNTPASS;
					}
						
					if (ConvertUtils::DecodePassword($loginArray[1], $newAccount) == $mailIncPass)
					{
						$_SESSION[ACCOUNT_ID] = $loginArray[0];
						$_SESSION[USER_ID] = $loginArray[3];
	
						$account = &$newAccount;
	
						$fs = &new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
						$attfolder = &new Folder($_SESSION[ACCOUNT_ID], -1, $_SESSION['attachtempdir']);
						$fs->DeleteDir($attfolder);
						unset($fs, $attfolder);
							
						$_SESSION[SESSION_LANG] = $account->DefaultLanguage;
	
						$sendSettingsList = true;
					}
					else
					{
						$account = &$newAccount;
	
						$account->MailIncPassword = $mailIncPass;
						$newprocessor = &new MailProcessor($account);
						if ($newprocessor->MailStorage->Connect(true))
						{
							$_SESSION[ACCOUNT_ID] = $loginArray[0];
							$_SESSION[USER_ID] = $loginArray[3];
								
							$fs = &new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
							$attfolder = &new Folder($_SESSION[ACCOUNT_ID], -1, $_SESSION['attachtempdir']);
							$fs->DeleteDir($attfolder);
							unset($fs, $attfolder);
								
							$_SESSION[SESSION_LANG] = $account->DefaultLanguage;
	
							$sendSettingsList = true;
	
							if (!$account->Update())
							{
								printErrorAndExit(PROC_WRONG_ACCT_PWD, $xmlRes);
							}
						}
						else
						{
							printErrorAndExit(PROC_WRONG_ACCT_PWD, $xmlRes);
						}
					}
				}
			}
				
			if ($sendSettingsList)
			{
				$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
				if (!$dbStorage || !$dbStorage->Connect() || !$dbStorage->UpdateLastLoginAndLoginsCount($account->IdUser))
				{
					$sendSettingsList = false;
					printErrorAndExit(getGlobalError(), $xmlRes);
				}
			}
	
			if ($sendSettingsList)
			{
				$loginNode = &new XmlDomNode('login');
	
				if ($xmlObj->GetParamValueByName('sign_me'))
				{
					$loginNode->AppendAttribute('id_acct', $account->Id);
					$loginNode->AppendChild(new XmlDomNode('hash',
					md5(ConvertUtils::EncodePassword($account->MailIncPassword, $account)), true));
				}
	
				$xmlRes->XmlRoot->AppendChild($loginNode);
			}
				
			printXML($xmlRes);
			break;
				
		case 'new':
	
			switch ($xmlObj->GetParamValueByName('request'))
			{
				case 'account':
					$newAccount = &new Account();
					
					UpdateAccountFromRequest($xmlObj->XmlRoot, $newAccount);
						
					$account = &Account::LoadFromDb($accountId);
					if (!$account->AllowChangeSettings || !$settings->AllowUsersAddNewAccounts)
					{	
						printErrorAndExit(PROC_ERROR_ACCT_CREATE, $xmlRes);
					}
										
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
					
					if ($account->IsDemo)
					{
						GetAccountList($account, $account->Id);
						break;
					}
						
					$accountNode = $xmlObj->XmlRoot->GetChildNodeByTagName('account');
	
					$user = &new User();
					$user->Id = $account->IdUser;
					$newAccount->IdUser = $account->IdUser;
	
					$folderSync = $accountNode->GetAttribute('inbox_sync_type', FOLDERSYNC_AllEntireMessages);
					if ($newAccount->MailProtocol == MAILPROTOCOL_IMAP4)
					{
						$folderSync = FOLDERSYNC_AllHeadersOnly;
					}
	
					$inboxSyncType = ($newAccount->MailProtocol == MAILPROTOCOL_IMAP4 && $settings->AllowDirectMode &&	$settings->DirectModeIsDefault) ?
					FOLDERSYNC_DirectMode : $folderSync;
						
					$validatedError = $newAccount->ValidateData();
						
					if($validatedError !== true)
					{
						printErrorAndExit($validatedError, $xmlRes);
					}
					elseif ($user->CreateAccount($newAccount, $inboxSyncType))
					{
						GetAccountList($account, $newAccount->Id);
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
					break;
									
				case 'filter':
						
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
						
					if ($dbStorage->Connect())
					{
						$filter = &GetFilterFromRequest($xmlObj->XmlRoot);
	
						$editAccount = &Account::LoadFromDb($filter->IdAcct);
						if (!$editAccount)
						{
							printErrorAndExit('', $xmlRes, 2);
						}
	
						if ($editAccount->IsDemo)
						{
							GetFiltersList($filter->IdAcct);
							break;
						}
							
						if ($dbStorage->IsAccountInRing($_SESSION[ACCOUNT_ID], $filter->IdAcct))
						{
							if (empty($filter->Filter))
							{
								printErrorAndExit(JS_LANG_WarningEmptyFilter, $xmlRes);
							}
							elseif ($dbStorage->InsertFilter($filter))
							{
								GetFiltersList($filter->IdAcct);
							}
							else
							{
								printErrorAndExit(PROC_CANT_INS_NEW_FILTER, $xmlRes);
							}
						}
						else
						{
							printErrorAndExit(PROC_WRONG_ACCT_ACCESS, $xmlRes);
						}
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
					break;
									
				case 'folder':
						
					$acctId = (int) $xmlObj->GetParamValueByName('id_acct');
					$account = &Account::LoadFromDb($acctId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
	
					if ($dbStorage->Connect())
					{
						if (!$dbStorage->IsAccountInRing($_SESSION[ACCOUNT_ID], $acctId))
						{
							printErrorAndExit(PROC_WRONG_ACCT_ACCESS, $xmlRes);
						}
	
						$parentId = $xmlObj->GetParamValueByName('id_parent');
	
						$folderName = ConvertUtils::ConvertEncoding(
						$xmlObj->GetParamTagValueByName('name'),
						$account->GetUserCharset(), CPAGE_UTF7_Imap);
	
						$parentPath = ($parentId == -1) ? '' : $xmlObj->GetParamTagValueByName('full_name_parent').$account->Delimiter;
	
						$create = (bool) $xmlObj->GetParamValueByName('create');
	
						if ($account->MailProtocol == MAILPROTOCOL_IMAP4)
						{
							$folderSync = ($account->AllowDirectMode && $settings->DirectModeIsDefault)
								? FOLDERSYNC_DirectMode : FOLDERSYNC_AllHeadersOnly;
	
							$folder = &new Folder($acctId, -1, $parentPath.$folderName, $folderName,
								($create) ? $folderSync : FOLDERSYNC_DontSync);
						}
						else
						{
							$folder = &new Folder($acctId, -1, $parentPath.$folderName, $folderName);
						}
	
						$folder->IdParent = $parentId;
						$folder->Type = FOLDERTYPE_Custom;
						$folder->Hide = false;
	
						$validate = $folder->ValidateData();
						if ($validate !== true)
						{
							printErrorAndExit($validate, $xmlRes);
						}
	
						$processor = &new MailProcessor($account);
	
						$folders = &$processor->GetFolders();
	
						$folderList = &$folders->CreateFolderListFromTree();
	
						$folderExist = false;
						foreach (array_keys($folderList->Instance()) as $key)
						{
							$listFolder = &$folderList->Get($key);
								
							if (strtolower($listFolder->FullName) == strtolower($folder->FullName))
							{
								$folderExist = true;
								break;
							}
						}
	
						if ($folderExist)
						{
							printErrorAndExit(PROC_FOLDER_EXIST, $xmlRes);
						}
						elseif ($account->IsDemo || $processor->CreateFolder($folder, $create))
						{
							$folders = &$processor->GetFolders();
								
							$foldersList = &new XmlDomNode('folders_list');
							$foldersList->AppendAttribute('sync', -1);
							$foldersList->AppendAttribute('id_acct', $acctId);
								
							GetFoldersTreeXml($folders, $foldersList, $processor);
							$xmlRes->XmlRoot->AppendChild($foldersList);
						}
						else
						{
							printErrorAndExit(PROC_CANT_CREATE_FLD, $xmlRes);
						}
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
	
					break;
	
				case 'contact':
					
					if (!$settings->AllowContacts)
					{
						printErrorAndExit(PROC_CANT_INS_NEW_CONTS, $xmlRes);
					}
					
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
					if ($dbStorage->Connect())
					{
						$addressBookRecord = &new AddressBookRecord();
	
						UpdateContactFromRequest($xmlObj->XmlRoot, $addressBookRecord, $accountId);
	
						$validatedError = $addressBookRecord->validateData();
	
						if($validatedError !== true)
						{
							printErrorAndExit($validatedError, $xmlRes);
						}
	
						if ($dbStorage->InsertAddressBookRecord($addressBookRecord))
						{
							$contactNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('contact');
								
							$groupsNode = &$contactNode->GetChildNodeByTagName('groups');
								
							$result = true;
	
							foreach (array_keys($groupsNode->Children) as $key)
							{
								$result &= $dbStorage->InsertAddressGroupContact($addressBookRecord->IdAddress, $groupsNode->Children[$key]->Attribute['id']);
							}
								
							GetContactList($accountId);
						}
						else
						{
							printErrorAndExit(PROC_CANT_INS_NEW_CONTS, $xmlRes);
						}
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
						
					break;
						
				case 'group':
					
					if (!$settings->AllowContacts)
					{
						printErrorAndExit(PROC_CANT_INS_NEW_GROUP, $xmlRes);
					}
					
					$account = &Account::LoadFromDb($accountId);
					
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
					if ($dbStorage->Connect())
					{
						$groupNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('group');
	
						$group = &new AddressGroup();
	
						$group->IdUser = $account->IdUser;
						$group->Name = $groupNode->GetChildValueByTagName('name', true);
	
						$group->IsOrganization = false;
						if (isset($groupNode->Attributes['organization']))
						{
							$group->IsOrganization = (bool) $groupNode->Attributes['organization'];
						}
	
						if ($group->IsOrganization)
						{
							$group->Email = $groupNode->GetChildValueByTagName('email', true);
							$group->Company = $groupNode->GetChildValueByTagName('company', true);
							$group->Street = $groupNode->GetChildValueByTagName('street', true);
							$group->City = $groupNode->GetChildValueByTagName('city', true);
							$group->State = $groupNode->GetChildValueByTagName('state', true);
							$group->Zip = $groupNode->GetChildValueByTagName('zip', true);
							$group->Country = $groupNode->GetChildValueByTagName('country', true);
							$group->Phone = $groupNode->GetChildValueByTagName('phone', true);
							$group->Fax = $groupNode->GetChildValueByTagName('fax', true);
							$group->Web = $groupNode->GetChildValueByTagName('web', true);
						}
	
						$result = false;
	
						$validatedError = $group->validateData();
	
						if($validatedError !== true)
						{
							printErrorAndExit($validatedError, $xmlRes);
						}
	
						if($dbStorage->CheckExistsAddresGroupByName($group->Name, $account->IdUser))
						{
							printErrorAndExit(WarningGroupAlreadyExist, $xmlRes);
						}
							
						if ($dbStorage->InsertAddressGroup($group))
						{
							$result = true;
								
							$contactsNode = &$groupNode->GetChildNodeByTagName('contacts');
	
							foreach (array_keys($contactsNode->Children) as $key)
							{
								$result &= $dbStorage->InsertAddressGroupContact(
								$contactsNode->Children[$key]->Attributes['id'], $group->Id);
							}
	
							$contactsNode = &$groupNode->GetChildNodeByTagName('new_contacts');
	
							foreach (array_keys($contactsNode->Children) as $key)
							{
								$personalNode = &$contactsNode->Children[$key]->GetChildNodeByTagName('personal');
	
								$addressBookRecord = &new AddressBookRecord();
								$addressBookRecord->IdUser = $account->IdUser;
								$addressBookRecord->HomeEmail = $personalNode->GetChildValueByTagName('email');
								$addressBookRecord->PrimaryEmail = PRIMARYEMAIL_Home;
	
								$result &= $dbStorage->InsertAddressBookRecord($addressBookRecord) &&
								$dbStorage->InsertAddressGroupContact($addressBookRecord->IdAddress, $group->Id);
							}
						}
	
						if ($result)
						{
							GetContactList($accountId);
						}
						else
						{
							printErrorAndExit(PROC_CANT_INS_NEW_GROUP, $xmlRes);
						}
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
					break;						
			}
							
			printXML($xmlRes);
			break;
	
		case 'set':
			switch ($xmlObj->GetParamValueByName('request'))
			{
				case 'sender':
				
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
					if ($dbStorage->Connect())
					{
						$value = (int) $xmlObj->GetParamValueByName('safety');
						$emailString = trim($xmlObj->GetParamTagValueByName('sender'));
						$emailObj = &new EmailAddress();
						$emailObj->Parse($emailString);
						if ($emailObj->Email)
						{
							$dbStorage->SetSenders($emailObj->Email, $value, $account->IdUser);
						}
					}
	
					$updateNode = &new XmlDomNode('update');
					$updateNode->AppendAttribute('value', 'set_sender');
					$xmlRes->XmlRoot->AppendChild($updateNode);
					break;
			}
	
			printXML($xmlRes);
			break;
									
		case 'add':
			switch ($xmlObj->GetParamValueByName('request'))
			{
				case 'contacts':
					
					if (!$settings->AllowContacts)
					{
						printErrorAndExit(PROC_CANT_ADD_NEW_CONT_TO_GRP, $xmlRes);
					}
					
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
					if ($dbStorage->Connect())
					{
						$groupId = $xmlObj->GetParamValueByName('id_group');
	
						$contactsNode = $xmlObj->XmlRoot->GetChildNodeByTagName('contacts');
	
						$result = true;
						foreach (array_keys($contactsNode->Children) as $key)
						{
							$result &= $dbStorage->DeleteAddressGroupsContacts(
							$contactsNode->Children[$key]->Attributes['id'], $groupId) &&
							$dbStorage->InsertAddressGroupContact(
							$contactsNode->Children[$key]->Attributes['id'], $groupId);
						}
						if ($result)
						{
							$updateNode = &new XmlDomNode('update');
							$updateNode->AppendAttribute('value', 'group');
							$xmlRes->XmlRoot->AppendChild($updateNode);
						}
						else
						{
							printErrorAndExit(PROC_CANT_ADD_NEW_CONT_TO_GRP, $xmlRes);
						}
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
					break;
			}
				
			printXML($xmlRes);
			break;
											
		case 'update':
			switch ($xmlObj->GetParamValueByName('request'))
			{
				case 'cookie_settings':
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$account->HideFolders = $xmlObj->GetParamValueByName('hide_folders');
					$account->HorizResizer = $xmlObj->GetParamValueByName('horiz_resizer');
					$account->VertResizer = $xmlObj->GetParamValueByName('vert_resizer');
					$account->Mark = $xmlObj->GetParamValueByName('mark');
					$account->Reply = $xmlObj->GetParamValueByName('reply');
						
					$columnsNode = $xmlObj->XmlRoot->GetChildNodeByTagName('columns');
	
					if ($columnsNode != null)
					{
						foreach (array_keys($columnsNode->Children) as $key)
						{
							$id = isset($columnsNode->Children[$key]->Attributes['id']) ? (int) $columnsNode->Children[$key]->Attributes['id'] : -1;
							$value = isset($columnsNode->Children[$key]->Attributes['value']) ? (int) $columnsNode->Children[$key]->Attributes['value'] : -1;
							if ($id > -1 && $value > -1)
							{
								$account->Columns[$id] = $value;
							}
						}
					}
						
					$account->Update();
						
					$settingsNode1 = &new XmlDomNode('update');
					$settingsNode1->AppendAttribute('value', 'cookie_settings');
					$xmlRes->XmlRoot->AppendChild($settingsNode1);
					break;
						
				case 'settings':
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
	
					$settingsReqNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('settings');
						
					$MailsPerPage = (int) $settingsReqNode->GetAttribute('msgs_per_page', $account->MailsPerPage);
					if ($MailsPerPage < 1)
					{
						$MailsPerPage = 1;
					}
						
					$AllowDhtmlEditor = (bool) $settingsReqNode->GetAttribute('allow_dhtml_editor', $account->AllowDhtmlEditor);
						
					$DefaultOutCharset = $account->DefaultOutCharset;
					if ($settings->AllowUsersChangeCharset)
					{
						$DefaultOutCharset = ConvertUtils::GetCodePageName($settingsReqNode->GetAttribute('def_charset_out', 0));
					}
	
					$DefaultTimeZone = $account->DefaultTimeZone;
					if ($settings->AllowUsersChangeTimeZone)
					{
						$DefaultTimeZone = $settingsReqNode->GetAttribute('def_timezone', $account->DefaultTimeZone);
					}
						
					$ViewMode = (int) $settingsReqNode->GetAttribute('view_mode', $account->ViewMode);
						
					$DefaultSkin = $account->DefaultSkin;
					if ($settings->AllowUsersChangeSkin)
					{
						$DefaultSkin = $settingsReqNode->GetChildValueByTagName('def_skin');
					}
						
					$DefaultLanguage = $account->DefaultLanguage;
					if ($settings->AllowUsersChangeLanguage)
					{
						$DefaultLanguage = $settingsReqNode->GetChildValueByTagName('def_lang');
						$_SESSION[SESSION_LANG] = $DefaultLanguage;
					}
						
					$dateFormat = $settingsReqNode->GetChildValueByTagName('def_date_fmt');
					if($dateFormat == '')
					{
						$dateFormat = $account->DefaultDateFormat;
					}
						
					$timeFormat = $settingsReqNode->GetAttribute('time_format', $account->DefaultTimeFormat);
						
					if ($account->IsDemo)
					{
						$_SESSION[DEMO_SES][DEMO_S_MessagesPerPage] = $MailsPerPage;
						$_SESSION[DEMO_SES][DEMO_S_AllowDhtmlEditor] = $AllowDhtmlEditor;
						$_SESSION[DEMO_SES][DEMO_S_DefaultOutCharset] = $DefaultOutCharset;
						$_SESSION[DEMO_SES][DEMO_S_DefaultTimeZone] = $DefaultTimeZone;
						$_SESSION[DEMO_SES][DEMO_S_ViewMode] = $ViewMode;
						$_SESSION[DEMO_SES][DEMO_S_DefaultSkin] = $DefaultSkin;
						$_SESSION[DEMO_SES][DEMO_S_DefaultLanguage] = $DefaultLanguage;
						$_SESSION[DEMO_SES][DEMO_S_DefaultDateFormat] = $dateFormat;
						$_SESSION[DEMO_SES][DEMO_S_DefaultTimeFormat] = $timeFormat;
					}
					else
					{
						$account->MailsPerPage = $MailsPerPage;
						$account->AllowDhtmlEditor = $AllowDhtmlEditor;
						$account->DefaultOutCharset = $DefaultOutCharset;
						$account->DefaultTimeZone = $DefaultTimeZone;
						$account->ViewMode = $ViewMode;
						$account->DefaultSkin = $DefaultSkin;
						$account->DefaultLanguage = $DefaultLanguage;
						$account->DefaultDateFormat = $dateFormat;
						$account->DefaultTimeFormat = $timeFormat;
					}
	
					$validate = $account->ValidateData();
					if ($validate !== true)
					{
						printErrorAndExit($validate, $xmlRes);
					}
					elseif ($account->Update(null))
					{
						$updateNode = &new XmlDomNode('update');
						$updateNode->AppendAttribute('value', 'settings');
						$xmlRes->XmlRoot->AppendChild($updateNode);
					}
					else
					{
						printErrorAndExit(PROC_ERROR_ACCT_UPDATE, $xmlRes);
					}
	
					break;
						
				case 'contacts_settings':
					
					if (!$settings->AllowContacts)
					{
						printErrorAndExit(PROC_CANT_UPDATE_CONT_SETTINGS, $xmlRes);
					}
					
					$account = &Account::LoadFromDb($accountId);
						
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
	
					$ContactsPerPage = (int) $xmlObj->GetParamValueByName('contacts_per_page');
						
					if ($account->IsDemo)
					{
						$_SESSION[DEMO_SES][DEMO_S_ContactsPerPage] = $ContactsPerPage;
					}
					else
					{
						$account->ContactsPerPage = $ContactsPerPage;
					}
						
					$validate = $account->ValidateData();
					if ($validate !== true)
					{
						printErrorAndExit($validate, $xmlRes);
					}
					elseif ($account->Update())
					{
						$contactSettingsNode = &new XmlDomNode('contacts_settings');
						$contactSettingsNode->AppendAttribute('contacts_per_page', $ContactsPerPage);
						$xmlRes->XmlRoot->AppendChild($contactSettingsNode);
					}
					else
					{
						printErrorAndExit(PROC_CANT_UPDATE_CONT_SETTINGS, $xmlRes);
					}
					break;
	
				case 'account':
					$accountNode = $xmlObj->XmlRoot->GetChildNodeByTagName('account');
					$idAcct = $accountNode->GetAttribute('id', -1);
						
					$account = &Account::LoadFromDb($idAcct);
					
					if (!$account->AllowChangeSettings)
					{
						printErrorAndExit(PROC_CANT_UPDATE_ACCT, $xmlRes);
					}
					
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$oldEmail = $account->Email;
						
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
					if ($dbStorage->Connect() && $dbStorage->IsAccountInRing($_SESSION[ACCOUNT_ID], $idAcct))
					{
	
						UpdateAccountFromRequest($xmlObj->XmlRoot, $account);
	
						//Validation rules
						$validate = $account->ValidateData();
						if ($validate !== true)
						{
							printErrorAndExit($validate, $xmlRes);
						}
						else
						{
							if (!$settings->StoreMailsInDb && $account->Email != $oldEmail)
							{
								$fs = &new FileSystem(INI_DIR.'/mail', $oldEmail, $account->Id);
								if (!$fs->MoveFolders($account->Email))
								{
									printErrorAndExit(PROC_CANT_UPDATE_ACCT, $xmlRes);
								}
							}
							
							if ($account->Update($accountNode->Attributes['inbox_sync_type']))
							{
								$updateNode = &new XmlDomNode('update');
								$updateNode->AppendAttribute('value', 'account');
								$xmlRes->XmlRoot->AppendChild($updateNode);
							}
							else
							{
								if (isset($GLOBALS[ErrorDesc]))
								{
									printErrorAndExit(getGlobalError(), $xmlRes);
								}
								else
								{
									printErrorAndExit(PROC_CANT_UPDATE_ACCT, $xmlRes);
								}
							}
						}
					}
					else
					{
						printErrorAndExit(PROC_WRONG_ACCT_ACCESS, $xmlRes);
					}
						
					break;
						
				case 'x_spam':
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
	
					$account->XSpam = (bool) $xmlObj->GetParamValueByName('x_spam');
						
					$account->Update(null);
						
					$xSpamNode = &new XmlDomNode('x_spam');
					$xSpamNode->AppendAttribute('value', (int) $account->XSpam);
					$xmlRes->XmlRoot->AppendChild($xSpamNode);
					break;
	
				case 'signature':
						
					$idAcct = $xmlObj->GetParamValueByName('id_acct');
						
					$account = &Account::LoadFromDb($idAcct);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
						
					if ($dbStorage->Connect() && $dbStorage->IsAccountInRing($_SESSION[ACCOUNT_ID], $idAcct))
					{
						$signatureNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('signature');
						$account->SignatureType = $signatureNode->GetAttribute('type', $account->SignatureType);
						$account->SignatureOptions = $signatureNode->GetAttribute('opt', $account->SignatureOptions);
						$account->Signature = ConvertUtils::WMBackHtmlSpecialChars($signatureNode->Value);
	
						$account->Update(null);
	
						$updateNode = &new XmlDomNode('update');
						$updateNode->AppendAttribute('value', 'signature');
						$xmlRes->XmlRoot->AppendChild($updateNode);
					}
					else
					{
						printErrorAndExit(PROC_WRONG_ACCT_ACCESS, $xmlRes);
					}
					break;
						
				case 'filter':
						
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
					if ($dbStorage->Connect())
					{
						$filterNode = $xmlObj->XmlRoot->GetChildNodeByTagName('filter');
						$idAcct = $filterNode->GetAttribute('id_acct', -1);
						$editAccount = &Account::LoadFromDb($idAcct);
	
						if ($dbStorage->IsAccountInRing($_SESSION[ACCOUNT_ID], $idAcct))
						{
							$filter = &GetFilterFromRequest($xmlObj->XmlRoot);
							if (empty($filter->Filter))
							{
								printErrorAndExit(JS_LANG_WarningEmptyFilter, $xmlRes);
							}
							elseif ($editAccount->IsDemo || $dbStorage->UpdateFilter($filter))
							{
								GetFiltersList($filter->IdAcct);
							}
							else
							{
								printErrorAndExit(PROC_CANT_INS_NEW_FILTER, $xmlRes);
							}
						}
						else
						{
							printErrorAndExit(PROC_WRONG_ACCT_ACCESS, $xmlRes);
						}
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
					break;
	
				case 'folders':
						
					$acctId = $xmlObj->GetParamValueByName('id_acct');
						
					$account =& Account::LoadFromDb($acctId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
						
					if ($dbStorage->Connect() && $dbStorage->IsAccountInRing($_SESSION[ACCOUNT_ID], $acctId))
					{
						$processor = &new MailProcessor($account);
	
						$foldersNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('folders');
	
						$result = true;
	
						if (!$processor->MailStorage->Connect())
						{
							printErrorAndExit(getGlobalError(), $xmlRes);
						}
	
						$serverFoldersName = array();
	
						if($account->MailProtocol == MAILPROTOCOL_IMAP4)
						{
							$tempFolders = $processor->MailStorage->GetFolders();
							$serverFolders = $tempFolders->CreateFolderListFromTree();
							$serverFoldersArray = $serverFolders->Instance();
								
							foreach ($serverFoldersArray AS $sFolder)
							{
								$serverFoldersName[] = strtolower($sFolder->FullName);
							}
								
							unset($tempFolders, $serverFolders, $serverFoldersArray, $sFolder);
						}
	
						for ($key = count($foldersNode->Children) - 1; $key >= 0; $key--)
						{
							//foreach (array_keys($foldersNode->Children) as $key)
							//{
							$folderNode =& $foldersNode->Children[$key];
	
							if (!ConvertUtils::CheckDefaultWordsFileName($folderNode->GetChildValueByTagName('name')) ||
							!ConvertUtils::CheckFileName($folderNode->GetChildValueByTagName('name')))
							{
								printErrorAndExit(PROC_CANT_UPD_FLD, $xmlRes);
							}
								
							$newFolderName = ConvertUtils::ConvertEncoding(
								ConvertUtils::ClearFileName(
								ConvertUtils::WMBackHtmlSpecialChars($folderNode->GetChildValueByTagName('name'))),
								$account->GetUserCharset(), CPAGE_UTF7_Imap);
							$newFolderHide = (bool) $folderNode->GetAttribute('hide', false);
	
							$fullFolderName = $folderNode->GetChildValueByTagName('full_name');
								
							$folder = &new Folder($acctId, $folderNode->GetAttribute('id', -1), $fullFolderName);
							$processor->GetFolderInfo($folder);
								
							$isRename = false;
							if ($folder->Name != $newFolderName)
							{
								$oldName = $folder->Name;
								$folder->Name = $newFolderName;
								$validate = $folder->ValidateData();
								if ($validate !== true)
								{
									printErrorAndExit($validate, $xmlRes);
								}
								else
								{
									$folder->Name = $oldName;
								}
	
								$result &= ($account->IsDemo) ? true : $processor->RenameFolder($folder, $newFolderName, $account->Delimiter);
								$isRename = true;
							}
							
							if ($folder->Hide != $newFolderHide)
							{
								$folder->Hide = $newFolderHide;
								$processor->SetHide($folder, $newFolderHide);
							}
							
							$folder->Name = $newFolderName;
							$folder->SyncType = $folderNode->GetAttribute('sync_type', FOLDERSYNC_DontSync);
							$folder->FolderOrder = $folderNode->GetAttribute('fld_order', 0);
	
							$create = $folderNode->GetChildValueByTagName('full_name');
								
							if (!$isRename && $account->MailProtocol == MAILPROTOCOL_IMAP4 &&
								!in_array(strtolower($folder->FullName), $serverFoldersName) &&
								$folder->SyncType != FOLDERSYNC_DontSync)
							{
								$result &= $processor->MailStorage->CreateFolder($folder);
							}
								
							if ($result)
							{
								$result &= ($account->IsDemo) ? true : $processor->DbStorage->UpdateFolder($folder);
							}
							else
							{
								printErrorAndExit(PROC_CANT_UPD_FLD, $xmlRes);
							}
						}
	
						if ($result)
						{
							$folders = &$processor->GetFolders();
								
							$foldersList = &new XmlDomNode('folders_list');
							$foldersList->AppendAttribute('sync', -1);
							$foldersList->AppendAttribute('id_acct', $acctId);
								
							GetFoldersTreeXml($folders, $foldersList, $processor);
							$xmlRes->XmlRoot->AppendChild($foldersList);
						}
						else
						{
							printErrorAndExit(PROC_CANT_UPD_FLD, $xmlRes);
						}
					}
					else
					{
						printErrorAndExit(PROC_WRONG_ACCT_ACCESS, $xmlRes);
					}
						
					break;
						
				case 'contact':
					
					if (!$settings->AllowContacts)
					{
						printErrorAndExit(PROC_CANT_UPDATE_CONT, $xmlRes);
					}
						
					$result = true;
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
					if ($dbStorage->Connect())
					{
						$addressBookRecord = &new AddressBookRecord();
	
						UpdateContactFromRequest($xmlObj->XmlRoot, $addressBookRecord, $accountId);
	
						$validatedError = $addressBookRecord->validateData();
	
						if($validatedError !== true)
						{
							printErrorAndExit($validatedError, $xmlRes);
						}
							
	
						if ($dbStorage->UpdateAddressBookRecord($addressBookRecord))
						{
							$contactNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('contact');
								
							$groupsNode = &$contactNode->GetChildNodeByTagName('groups');
								
							if ($addressBookRecord->IdAddress)
							{
								$result &= $dbStorage->DeleteAddressGroupsContactsByIdAddress($addressBookRecord->IdAddress);
							}
							foreach (array_keys($groupsNode->Children) as $key)
							{
								$result &= $dbStorage->InsertAddressGroupContact($addressBookRecord->IdAddress, $groupsNode->Children[$key]->Attributes['id']);
							}
								
							GetContactList($accountId);
						}
						else
						{
							printErrorAndExit(PROC_CANT_UPDATE_CONT, $xmlRes);
						}
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
						
					break;
						
				case 'group':
					
					if (!$settings->AllowContacts)
					{
						printErrorAndExit(PROC_CANT_UPDATE_CONT, $xmlRes);
					}
					
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
	
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
					if ($dbStorage->Connect())
					{
						$groupNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('group');
							
						$group = &new AddressGroup();
						$group->Id = $groupNode->GetAttribute('id', -1);
						$group->IdUser = $account->IdUser;
	
						$group->Name = $groupNode->GetChildValueByTagName('name', true);
						$group->IsOrganization = (bool) $groupNode->GetAttribute('organization', false);
	
						$group->Email = $groupNode->GetChildValueByTagName('email', true);
						$group->Company = $groupNode->GetChildValueByTagName('company', true);
						$group->Street = $groupNode->GetChildValueByTagName('street', true);
						$group->City = $groupNode->GetChildValueByTagName('city', true);
						$group->State = $groupNode->GetChildValueByTagName('state', true);
						$group->Zip = $groupNode->GetChildValueByTagName('zip', true);
						$group->Country = $groupNode->GetChildValueByTagName('country', true);
						$group->Phone = $groupNode->GetChildValueByTagName('phone', true);
						$group->Fax = $groupNode->GetChildValueByTagName('fax', true);
						$group->Web = $groupNode->GetChildValueByTagName('web', true);
	
						$result = false;
	
						$validatedError = $group->validateData();
	
						if($validatedError !== true)
						{
							printErrorAndExit($validatedError, $xmlRes);
						}
						elseif ($dbStorage->UpdateAddressGroup($group))
						{
							$contactsNode = &$groupNode->GetChildNodeByTagName('contacts');
								
							$result = $dbStorage->DeleteAddressGroupsContactsByIdGroup($group->Id);
	
							foreach (array_keys($contactsNode->Children) as $key)
							{
								$result &= $dbStorage->InsertAddressGroupContact(
								$contactsNode->Children[$key]->GetAttribute('id', -1), $group->Id);
							}
	
							$contactsNode = &$groupNode->GetChildNodeByTagName('new_contacts');
	
							foreach (array_keys($contactsNode->Children) as $key)
							{
								$personalNode = &$contactsNode->Children[$key]->GetChildNodeByTagName('personal');
	
								$addressBookRecord = &new AddressBookRecord();
								$addressBookRecord->IdUser = $account->IdUser;
								$addressBookRecord->HomeEmail = $personalNode->GetChildValueByTagName('email');
								$addressBookRecord->PrimaryEmail = PRIMARYEMAIL_Home;
	
								$result &= $dbStorage->InsertAddressBookRecord($addressBookRecord) &&
								$dbStorage->InsertAddressGroupContact($addressBookRecord->IdAddress, $group->Id);
							}
						}
	
						if ($result)
						{
							GetContactList($accountId);
						}
						else
						{
							printErrorAndExit(PROC_CANT_INS_NEW_CONTS, $xmlRes);
						}
	
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
						
					break;
			}
				
			printXML($xmlRes);
			break;
													
		case 'get':
			switch ($xmlObj->GetParamValueByName('request'))
			{
				case 'accounts':
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
	
					GetAccountList($account, -1);
					break;
						
				case 'account':
					$account = &Account::LoadFromDb($xmlObj->GetParamValueByName('id_acct'));
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
					
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
					if ($dbStorage->Connect() && $dbStorage->IsAccountInRing($_SESSION[ACCOUNT_ID], $account->Id))
					{
						$accountNode = &new XmlDomNode('account');
						$accountNode->AppendAttribute('id', $account->Id);
						$accountNode->AppendAttribute('def_acct', (int) $account->DefaultAccount);
						$accountNode->AppendAttribute('mail_protocol', $account->MailProtocol);
						$accountNode->AppendAttribute('mail_inc_port', $account->MailIncPort);
						$accountNode->AppendAttribute('mail_out_port', $account->MailOutPort);
						$accountNode->AppendAttribute('mail_out_auth', (int) $account->MailOutAuthentication);
							
						$accountNode->AppendAttribute('use_friendly_nm', (int) $account->UseFriendlyName);
						$accountNode->AppendAttribute('mails_on_server_days', $account->MailsOnServerDays);
						$accountNode->AppendAttribute('mail_mode', $account->MailMode);
						$accountNode->AppendAttribute('getmail_at_login', (int) $account->GetMailAtLogin);
							
						if ($account->MailProtocol == MAILPROTOCOL_POP3 || $account->MailProtocol == MAILPROTOCOL_WMSERVER)
						{
							$processor = &new MailProcessor($account);
							$folders = &$processor->GetFolders();
							$inboxFolder = &$folders->GetFolderByType(FOLDERTYPE_Inbox);
							$accountNode->AppendAttribute('inbox_sync_type', $inboxFolder->SyncType);
						}
		
						$accountNode->AppendChild(new XmlDomNode('friendly_name', '<![CDATA['.$account->FriendlyName.']]>'));
						$accountNode->AppendChild(new XmlDomNode('email', '<![CDATA['.$account->Email.']]>'));
						$accountNode->AppendChild(new XmlDomNode('mail_inc_host', '<![CDATA['.$account->MailIncHost.']]>'));
						$accountNode->AppendChild(new XmlDomNode('mail_inc_login', '<![CDATA['.$account->MailIncLogin.']]>'));
						$accountNode->AppendChild(new XmlDomNode('mail_inc_pass', ($account->MailIncPassword == '')?'':DUMMYPASSWORD, true));
						$accountNode->AppendChild(new XmlDomNode('mail_out_host', '<![CDATA['.$account->MailOutHost.']]>'));
						$accountNode->AppendChild(new XmlDomNode('mail_out_login', '<![CDATA['.$account->MailOutLogin.']]>'));
						$accountNode->AppendChild(new XmlDomNode('mail_out_pass', ($account->MailOutPassword == '')?'':DUMMYPASSWORD, true));
							
						$xmlRes->XmlRoot->AppendChild($accountNode);
					}
					else
					{
						printErrorAndExit(PROC_WRONG_ACCT_ACCESS, $xmlRes);
					}
					break;
	
				case 'folders_list':
						
					$syncType = $xmlObj->GetParamValueByName('sync');
					$idAcct = $xmlObj->GetParamValueByName('id_acct');
					$changeAccount = false;
						
					if ($syncType != -1)
					{
						$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
	
						if ($idAcct != $_SESSION[ACCOUNT_ID] && $dbStorage->Connect() && $dbStorage->IsAccountInRing($_SESSION[ACCOUNT_ID], $idAcct))
						{
							$changeAccount = true;
							$oldaccount = &Account::LoadFromDb($_SESSION[ACCOUNT_ID]);
							$fs = &new FileSystem(INI_DIR.'/temp', $oldaccount->Email, $oldaccount->Id);
							$attfolder = &new Folder($_SESSION[ACCOUNT_ID], -1, $_SESSION['attachtempdir']);
							$fs->DeleteDir($attfolder);
							unset($fs, $attfolder);
								
							$_SESSION[ACCOUNT_ID] = $idAcct;
						}
						else
						{
							$idAcct = $_SESSION[ACCOUNT_ID];
						}
	
					}
					$account = &Account::LoadFromDb($idAcct);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
	
					if ($changeAccount)
					{
						$fs = &new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
						$attfolder = &new Folder($idAcct, -1, $_SESSION['attachtempdir']);
						$fs->ClearDir($attfolder);
						unset($fs, $attfolder);
					}
						
					$processor = &new MailProcessor($account);
					$folders = &$processor->GetFolders();
						
					$syncRes = true;
					switch ($syncType)
					{
						case -1:
						case 0:
							break;
	
						case 1:
							$folders = &$processor->GetFolders();
							break;
								
						case 2:
							$syncRes = $processor->SynchronizeFolders();
							if ($syncRes)
							{
								$folders = &$processor->GetFolders();
							}
							break;
					}
						
					if (!$syncRes)
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
					elseif ($folders != null)
					{
						$foldersList = &new XmlDomNode('folders_list');
						$foldersList->AppendAttribute('sync', $syncType);
						$foldersList->AppendAttribute('id_acct', $idAcct);
	
						GetFoldersTreeXml($folders, $foldersList, $processor);
						$xmlRes->XmlRoot->AppendChild($foldersList);
					}
					else
					{
						printErrorAndExit(PROC_CANT_GET_FLDS, $xmlRes);
					}
					break;
	
				case 'messages':
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
	
					$processor = &new MailProcessor($account);
						
					$folderNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('folder');
						
					$folder = &new Folder($accountId, $folderNode->Attributes['id'], $folderNode->GetChildValueByTagName('full_name'));
						
					$searchNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('look_for');
						
					$sortField = $xmlObj->GetParamValueByName('sort_field');
					$sortOrder = $xmlObj->GetParamValueByName('sort_order');
						
					$account->DefaultOrder = $sortField + $sortOrder;
						
					$account->Update();
						
					if ($searchNode->Value == '')
					{
						$processor->GetFolderInfo($folder);
						$processor->GetFolderMessageCount($folder);
	
						if (ceil($folder->MessageCount/$account->MailsPerPage) < (int) $xmlObj->GetParamValueByName('page'))
						{
							$page = $xmlObj->GetParamValueByName('page') - 1;
							$page = ($page < 1) ? 1 : $page;
						}
						else
						{
							$page = $xmlObj->GetParamValueByName('page');
						}
						$messageCollection = &$processor->GetMessageHeaders($page, $folder);
					}
					else
					{
						if ($folder->IdDb == -1)
						{
							$folders = &$processor->GetFolders();
						}
						else
						{
							$processor->GetFolderInfo($folder);
								
							$folders = &new FolderCollection();
							$folders->Add($folder);
						}
	
						$page = $xmlObj->GetParamValueByName('page');
	
						$folder->MessageCount = $processor->SearchMessagesCount(
						ConvertUtils::ConvertEncoding($searchNode->Value,
						$account->GetUserCharset(), $account->DbCharset),
						$folders, (bool) !$searchNode->Attributes['fields']);
	
						$messageCollection = &$processor->SearchMessages($page,
						ConvertUtils::ConvertEncoding($searchNode->Value,
						$account->GetUserCharset(), $account->DbCharset),
						$folders, (bool) !$searchNode->Attributes['fields'], $folder->MessageCount);
					}
	
					if ($messageCollection != null)
					{
						$msgsNode = &new XmlDomNode('messages');
						$msgsNode->AppendAttribute('page', $page);
						$msgsNode->AppendAttribute('sort_field', $sortField);
						$msgsNode->AppendAttribute('sort_order', $sortOrder);
						$msgsNode->AppendAttribute('count', $folder->MessageCount);
						$msgsNode->AppendAttribute('count_new', $folder->UnreadMessageCount);
	
						$folderOutNode = &new XmlDomNode('folder', $folder->FullName, true);
						$folderOutNode->AppendAttribute('id', $folder->IdDb);
	
						$msgsNode->AppendChild($folderOutNode);
						$msgsNode->AppendChild($xmlObj->XmlRoot->GetChildNodeByTagName('look_for'));
	
						for ($i = 0; $i < $messageCollection->Count(); $i++)
						{
							$msg = &$messageCollection->Get($i);
							$msgNode = &new XmlDomNode('message');
							$msgNode->AppendAttribute('id', $msg->IdMsg);
							$msgNode->AppendAttribute('has_attachments', (int) $msg->HasAttachments());
							$msgNode->AppendAttribute('priority', $msg->GetPriorityStatus());
							$msgNode->AppendAttribute('size', $msg->Size);
							$msgNode->AppendAttribute('flags', $msg->Flags);
							$msgNode->AppendAttribute('charset', $msg->Charset);
								
							if ($searchNode->Value == '')
							{
								$msgNode->AppendChild($folderOutNode);
							}
							else
							{
								$msgFolder = &new Folder($accountId, $msg->IdFolder, null);
								$processor->GetFolderInfo($msgFolder);
	
								$folderOutMsgNode = &new XmlDomNode('folder', $msgFolder->FullName, true);
								$folderOutMsgNode->AppendAttribute('id', $msgFolder->IdDb);
	
								$msgNode->AppendChild($folderOutMsgNode);
							}
								
							$msgNode->AppendChild(new XmlDomNode('from', $msg->GetFromAsStringForSend(), true));
							$msgNode->AppendChild(new XmlDomNode('to', $msg->GetToAsStringForSend(), true));
							$msgNode->AppendChild(new XmlDomNode('reply_to', $msg->GetReplyToAsStringForSend(), true));
							$msgNode->AppendChild(new XmlDomNode('cc', $msg->GetCcAsStringForSend(), true));
							$msgNode->AppendChild(new XmlDomNode('bcc', $msg->GetBccAsStringForSend(), true));
								
							$msgNode->AppendChild(new XmlDomNode('subject', $msg->GetSubject(true), true));
	
							$date = &$msg->GetDate();
							$date->FormatString = $account->DefaultDateFormat;
							$date->TimeFormat = $account->DefaultTimeFormat;
								
							if ($settings->AllowUsersChangeTimeZone)
							{
								$msgNode->AppendChild(new XmlDomNode('date', $date->GetFormattedDate($account->GetDefaultTimeOffset()), true));
							}
							else
							{
								$msgNode->AppendChild(new XmlDomNode('date', $date->GetFormattedDate($account->GetDefaultTimeOffset($settings->DefaultTimeZone)), true));
							}
								
							$msgNode->AppendChild(new XmlDomNode('uid', $msg->Uid, true));
								
							$msgsNode->AppendChild($msgNode);
						}
	
						$xmlRes->XmlRoot->AppendChild($msgsNode);
	
					}
					else
					{
						printErrorAndExit(PROC_CANT_GET_MSG_LIST, $xmlRes);
					}
	
					break;
								
				case 'message':
	
					$safety = true;
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$charsetNum = $xmlObj->GetParamValueByName('charset');
					
					if ($charsetNum > 0)
					{
						$account->DefaultIncCharset = ConvertUtils::GetCodePageName($charsetNum);
						$GLOBALS[MailInputCharset] = $account->DefaultIncCharset;
						$account->Update();
					}
					
					$processor = &new MailProcessor($account);
						
					$folderNodeRequest = &$xmlObj->XmlRoot->GetChildNodeByTagName('folder');
						
					$folder =& new Folder($accountId, $folderNodeRequest->Attributes['id'], $folderNodeRequest->GetChildValueByTagName('full_name'));
						
					$processor->GetFolderInfo($folder);
						
					$msgId = $xmlObj->GetParamValueByName('id');
					$msgUid = $xmlObj->GetParamTagValueByName('uid');
						
					$msgIdUid = array();
					$msgIdUid[$msgId] = $msgUid;
						
					$processor->SetFlags($msgIdUid, $folder, MESSAGEFLAGS_Seen, ACTION_Set);
						
					$message =& $processor->GetMessage($msgId, $msgUid, $folder);
						
					if ($message != null)
					{
						$fromObj = new EmailAddress();
						$fromObj->Parse($message->GetFromAsString(true));
	
						$isFromSave = false;
						if ($fromObj->Email)
						{
							$isFromSave = $processor->DbStorage->SelectSenderSafetyByEmail($fromObj->Email, $account->IdUser);
						}
	
						if ($folder->SyncType != FOLDERSYNC_DirectMode && $processor->DbStorage->Connect())
						{
							$processor->DbStorage->UpdateMessageCharset($msgId, $charsetNum, $message);
						}
	
						$mode = (int) $xmlObj->GetParamValueByName('mode');
	
						$messageNode =& new XmlDomNode('message');
	
						$messageClassType = $message->TextBodies->ClassType();
	
						$messageNode->AppendAttribute('id', $xmlObj->GetParamValueByName('id'));
						$messageNode->AppendAttribute('html', (int) (($messageClassType & 2) == 2));
						$messageNode->AppendAttribute('plain', (int) (($messageClassType & 1) == 1));
						$messageNode->AppendAttribute('importance', $message->GetPriorityStatus());
						$messageNode->AppendAttribute('mode', $mode);
						$messageNode->AppendAttribute('charset', $charsetNum);
						$messageNode->AppendAttribute('has_charset', (int) $message->HasCharset);
	
						$messageNode->AppendChild(new XmlDomNode('uid', $msgUid, true));
	
						$folderNode = &new XmlDomNode('folder', $folder->FullName, true);
						$folderNode->AppendAttribute('id', $folder->IdDb);
						$messageNode->AppendChild($folderNode);
	
						$signature_html = '';
						$signature_plain = '';
	
						if ($account->SignatureOptions == SIGNATURE_OPTION_AddToAll)
						{
							if ($account->SignatureType == 1)
							{
								$signature_html = '<br />'.$account->Signature;
	
								require_once(WM_ROOTPATH.'libs/class_converthtml.php');
								$pars = &new convertHtml($account->Signature, false);
								$signature_plain = CRLF.$pars->get_text();
							}
							else
							{
								$signature_plain = CRLF.$account->Signature;
								$signature_html = '<br />'.nl2br($account->Signature);
							}
							$signature_plain = ConvertUtils::WMHtmlSpecialChars($signature_plain);
							$signature_html = $signature_html;
						}
	
						if (($mode & 1) == 1)
						{
							$headersNode = &new XmlDomNode('headers');
							$headersNode->AppendChild(new XmlDomNode('from', $message->GetFromAsString(true), true));
							
							$headersNode->AppendChild(new XmlDomNode('to', $message->GetToAsString(true), true));
							$headersNode->AppendChild(new XmlDomNode('cc', $message->GetCcAsString(true), true));
							$headersNode->AppendChild(new XmlDomNode('bcc', $message->GetBccAsString(true), true));
							
							$headersNode->AppendChild(new XmlDomNode('reply_to', $message->GetReplyToAsString(true), true));
							$headersNode->AppendChild(new XmlDomNode('subject', $message->GetSubject(true), true));
								
							$date = &$message->GetDate();
							$date->FormatString = $account->DefaultDateFormat;
							$date->TimeFormat = $account->DefaultTimeFormat;
								
							if ($settings->AllowUsersChangeTimeZone)
							{
								$headersNode->AppendChild(new XmlDomNode('date', $date->GetFormattedDate($account->GetDefaultTimeOffset()), true));
							}
							else
							{
								$headersNode->AppendChild(new XmlDomNode('date', $date->GetFormattedDate($account->GetDefaultTimeOffset($settings->DefaultTimeZone)), true));
							}
								
							$messageNode->AppendChild($headersNode);
						}
	
						if (($mode & 2) == 2 && ($messageClassType & 2) == 2)
						{
							if (($account->ViewMode == VIEW_MODE_PREVIEW_PANE_NO_IMG ||
							$account->ViewMode == VIEW_MODE_WITHOUT_PREVIEW_PANE_NO_IMG) && !$isFromSave)
							{
								$messageNode->AppendChild(new XmlDomNode('html_part', ConvertUtils::HtmlBodyWithoutImages(ConvertUtils::ReplaceJSMethod($message->GetCensoredHtmlWithImageLinks(true))), true, true));
								if (isset($GLOBALS[GL_WITHIMG]) && $GLOBALS[GL_WITHIMG])
								{
									$GLOBALS[GL_WITHIMG] = false;
									$safety = false;
								}
							}
							else
							{
								$messageNode->AppendChild(new XmlDomNode('html_part', ConvertUtils::ReplaceJSMethod($message->GetCensoredHtmlWithImageLinks(true)), true, true));
							}
						}
							
						if (($mode & 4) == 4 || ($mode & 2) == 2 && ($messageClassType & 1) == 1)
						{
							$messageNode->AppendChild(new XmlDomNode('modified_plain_text', $message->GetCensoredTextBody(true), true, true));
						}
	
						if (($mode & 8) == 8)
						{
							if (($account->ViewMode == VIEW_MODE_PREVIEW_PANE_NO_IMG ||
							$account->ViewMode == VIEW_MODE_WITHOUT_PREVIEW_PANE_NO_IMG) && !$isFromSave)
							{
								$messageNode->AppendChild(new XmlDomNode('reply_html', ConvertUtils::HtmlBodyWithoutImages(ConvertUtils::ReplaceJSMethod($signature_html.$message->GetRelpyAsHtml(true))), true, true));
								if (isset($GLOBALS[GL_WITHIMG]) && $GLOBALS[GL_WITHIMG])
								{
									$GLOBALS[GL_WITHIMG] = false;
									$safety =  false;
								}
							}
							else
							{
								$messageNode->AppendChild(new XmlDomNode('reply_html', ConvertUtils::ReplaceJSMethod($signature_html.$message->GetRelpyAsHtml(true)), true, true));
							}
						}
	
						if (($mode & 16) == 16)
						{
							$messageNode->AppendChild(new XmlDomNode('reply_plain', $signature_plain.$message->GetRelpyAsPlain(true), true, true));
						}
	
						if (($mode & 32) == 32)
						{
							if (($account->ViewMode == VIEW_MODE_PREVIEW_PANE_NO_IMG ||
							$account->ViewMode == VIEW_MODE_WITHOUT_PREVIEW_PANE_NO_IMG) && !$isFromSave)
							{
								$messageNode->AppendChild(new XmlDomNode('forward_html', ConvertUtils::HtmlBodyWithoutImages(ConvertUtils::ReplaceJSMethod($signature_html.$message->GetRelpyAsHtml(true))), true, true));
								if (isset($GLOBALS[GL_WITHIMG]) && $GLOBALS[GL_WITHIMG])
								{
									$GLOBALS[GL_WITHIMG] = false;
									$safety =  false;
								}
							}
							else
							{
								$messageNode->AppendChild(new XmlDomNode('forward_html', ConvertUtils::ReplaceJSMethod($signature_html.$message->GetRelpyAsHtml(true)), true, true));
							}
						}
	
						if (($mode & 64) == 64)
						{
							$messageNode->AppendChild(new XmlDomNode('forward_plain', $signature_plain.$message->GetRelpyAsPlain(true), true, true));
						}
	
						if (($mode & 128) == 128)
						{
							$messageNode->AppendChild(new XmlDomNode('full_headers',
							$message->ClearForSend(ConvertUtils::ConvertEncoding(
							$message->OriginalHeaders, $GLOBALS[MailInputCharset], $account->GetUserCharset())), true, true));
						}
	
						$messageNode->AppendAttribute('safety', (int) $safety);
	
						if (($mode & 256) == 256 || ($mode & 32) == 32 || ($mode & 8) == 8)
						{
							$attachments = &$message->Attachments;
							if ($attachments != null && $attachments->Count() > 0)
							{
								$attachmentsNode = &new XmlDomNode('attachments');
	
								foreach (array_keys($attachments->Instance()) as $key)
								{
									$attachment = &$attachments->Get($key);
									$tempname = $message->IdMsg.'-'.$key.'_'.ConvertUtils::ClearFileName($attachment->GetTempName());
									$filename = ConvertUtils::ClearFileName(ConvertUtils::ClearUtf8($attachment->GetFilenameFromMime(), $GLOBALS[MailInputCharset], $account->GetUserCharset()));

									$fs = &new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
									$attfolder = &new Folder($accountId, -1, $_SESSION['attachtempdir']);
									$fs->SaveAttach($attachment, $attfolder, $tempname);

									$attachNode = &new XmlDomNode('attachment');
									//$attachNode->AppendAttribute('id', $key);
									$attachNode->AppendAttribute('size', strlen($attachment->MimePart->GetBinaryBody()));
									$attachNode->AppendAttribute('inline', ($attachment->IsInline) ? '1': '0');
										
									$attachNode->AppendChild(new XmlDomNode('filename', $filename, true));
									$attachNode->AppendChild(new XmlDomNode('view', 'view-image.php?tn='.urlencode($tempname), true));
									$attachNode->AppendChild(new XmlDomNode('download', 'attach.php?tn='.urlencode($tempname).'&filename='.urlencode($filename), true));
										
									$attachNode->AppendChild(new XmlDomNode('tempname', $tempname, true));
									$attachNode->AppendChild(new XmlDomNode('mime_type', ConvertUtils::GetContentTypeFromFileName($filename), true));
										
									$attachmentsNode->AppendChild($attachNode);
								}
	
								$messageNode->AppendChild($attachmentsNode);
							}
						}
	
						if (($mode & 512) == 512)
						{
							$messageNode->AppendChild(new XmlDomNode('unmodified_plain_text', $message->GetNotCensoredTextBody(true), true, true));
						}
	
						$messageNode->AppendChild(new XmlDomNode('save_link',
							'attach.php?msg_id='.$msgId.'&msg_uid='.urlencode($msgUid).
							'&folder_id='.$folder->IdDb.'&folder_fname='.urlencode($folder->FullName), true));
	
						$xmlRes->XmlRoot->AppendChild($messageNode);
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
	
					break;
								
				case 'settings':
						
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					if ($account != null)
					{
						$settingsNode = &new XmlDomNode('settings');
						$settingsNode->AppendAttribute('msgs_per_page', $account->MailsPerPage);
						$settingsNode->AppendAttribute('allow_dhtml_editor', (int) $account->AllowDhtmlEditor);
	
						if ($settings->AllowUsersChangeCharset)
						{
							$settingsNode->AppendAttribute('def_charset_inc', ConvertUtils::GetCodePageNumber($account->DefaultIncCharset));
							$settingsNode->AppendAttribute('def_charset_out', ConvertUtils::GetCodePageNumber($account->DefaultOutCharset));
						}
	
						if ($settings->AllowUsersChangeTimeZone)
						{
							$settingsNode->AppendAttribute('def_timezone', (int) $account->DefaultTimeZone);
						}
	
						$settingsNode->AppendAttribute('view_mode', (int) $account->ViewMode);
	
						if ($settings->AllowUsersChangeSkin)
						{
							$skinsNode = &new XmlDomNode('skins');
								
							$skinsList = &FileSystem::GetSkinsList();
								
							foreach ($skinsList as $skin)
							{
								$skinNode = &new XmlDomNode('skin', $skin, true);
								$skinNode->AppendAttribute('def', (int) (strtolower($account->DefaultSkin) == strtolower($skin)));
	
								$skinsNode->AppendChild($skinNode);
							}
								
							$settingsNode->AppendChild($skinsNode);
						}
	
						if ($settings->AllowUsersChangeLanguage)
						{
							$langsNode = &new XmlDomNode('langs');
								
							$langList = &FileSystem::GetLangList();
								
							foreach ($langList as $lang)
							{
								$langNode = &new XmlDomNode('lang', $lang, true);
								$langNode->AppendAttribute('def', (int) (strtolower($account->DefaultLanguage) == strtolower($lang)));
	
								$langsNode->AppendChild($langNode);
							}
	
							$settingsNode->AppendChild($langsNode);
						}
	
						$settingsNode->AppendChild(new XmlDomNode('def_date_fmt', $account->DefaultDateFormat));
						$settingsNode->AppendAttribute('time_format', $account->DefaultTimeFormat);
	
						$xmlRes->XmlRoot->AppendChild($settingsNode);
					}
					else
					{
						printErrorAndExit(PROC_CANT_GET_SETTINGS, $xmlRes);
					}
						
					break;
						
				case 'contacts_settings':
					
					if (!$settings->AllowContacts)
					{
						printErrorAndExit(PROC_CANT_GET_SETTINGS, $xmlRes);
					}
					
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$contactSettingsNode = &new XmlDomNode('contacts_settings');
					$contactSettingsNode->AppendAttribute('contacts_per_page', $account->ContactsPerPage);
					$xmlRes->XmlRoot->AppendChild($contactSettingsNode);
	
					break;
						
				case 'x_spam':
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$xSpamNode = &new XmlDomNode('x_spam');
					$xSpamNode->AppendAttribute('value', (int) $account->XSpam);
					$xmlRes->XmlRoot->AppendChild($xSpamNode);
						
					break;
						
				case 'signature':
					$account = &Account::LoadFromDb($xmlObj->GetParamValueByName('id_acct'));
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$signatureNode = &new XmlDomNode('signature', $account->Signature, true);
					$signatureNode->AppendAttribute('id_acct', $account->Id);
					$signatureNode->AppendAttribute('type', $account->SignatureType);
					$signatureNode->AppendAttribute('opt', $account->SignatureOptions);
					$xmlRes->XmlRoot->AppendChild($signatureNode);
						
					break;
						
				case 'filters':
					GetFiltersList($xmlObj->GetParamValueByName('id_acct'));
					break;
						
				case 'contacts_groups':
					if (!$settings->AllowContacts)
					{
						printErrorAndExit(PROC_CANT_GET_CONTS_FROM_DB, $xmlRes);
					}
					GetContactList($accountId);
					break;
	
				case 'contact':
					if (!$settings->AllowContacts)
					{
						printErrorAndExit(PROC_CANT_GET_CONT_FROM_DB, $xmlRes);
					}
					
					$idAddress = (int) $xmlObj->GetParamValueByName('id_addr');
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
					if ($dbStorage->Connect())
					{
						$addressBookRecord = &$dbStorage->SelectAddressBookRecord($idAddress);
	
						if ($addressBookRecord != null)
						{
							$contactNode = &new XmlDomNode('contact');
							$contactNode->AppendAttribute('id', $idAddress);
							$contactNode->AppendAttribute('primary_email', $addressBookRecord->PrimaryEmail);
							$contactNode->AppendAttribute('use_friendly_name', (int) $addressBookRecord->UseFriendlyName);
	
							$contactNode->AppendChild(new XmlDomNode('fullname', $addressBookRecord->FullName, true));
	
							$birthdayNode = &new XmlDomNode('birthday');
								
							$birthdayNode->AppendAttribute('day', $addressBookRecord->BirthdayDay);
							$birthdayNode->AppendAttribute('month', $addressBookRecord->BirthdayMonth);
							$birthdayNode->AppendAttribute('year', $addressBookRecord->BirthdayYear);
								
							$contactNode->AppendChild($birthdayNode);
								
							$personalNode = &new XmlDomNode('personal');
							$personalNode->AppendChild(new XmlDomNode('email', $addressBookRecord->HomeEmail, true));
							$personalNode->AppendChild(new XmlDomNode('street', $addressBookRecord->HomeStreet, true));
							$personalNode->AppendChild(new XmlDomNode('city', $addressBookRecord->HomeCity, true));
							$personalNode->AppendChild(new XmlDomNode('state', $addressBookRecord->HomeState, true));
							$personalNode->AppendChild(new XmlDomNode('zip', $addressBookRecord->HomeZip, true));
							$personalNode->AppendChild(new XmlDomNode('country', $addressBookRecord->HomeCountry, true));
							$personalNode->AppendChild(new XmlDomNode('fax', $addressBookRecord->HomeFax, true));
							$personalNode->AppendChild(new XmlDomNode('phone', $addressBookRecord->HomePhone, true));
							$personalNode->AppendChild(new XmlDomNode('mobile', $addressBookRecord->HomeMobile, true));
							$personalNode->AppendChild(new XmlDomNode('web', $addressBookRecord->HomeWeb, true));
								
							$contactNode->AppendChild($personalNode);
								
							$businessNode = &new XmlDomNode('business');
							$businessNode->AppendChild(new XmlDomNode('email', $addressBookRecord->BusinessEmail, true));
							$businessNode->AppendChild(new XmlDomNode('company', $addressBookRecord->BusinessCompany, true));
							$businessNode->AppendChild(new XmlDomNode('job_title', $addressBookRecord->BusinessJobTitle, true));
							$businessNode->AppendChild(new XmlDomNode('department', $addressBookRecord->BusinessDepartment, true));
							$businessNode->AppendChild(new XmlDomNode('office', $addressBookRecord->BusinessOffice, true));
							$businessNode->AppendChild(new XmlDomNode('street', $addressBookRecord->BusinessStreet, true));
							$businessNode->AppendChild(new XmlDomNode('city', $addressBookRecord->BusinessCity, true));
							$businessNode->AppendChild(new XmlDomNode('state', $addressBookRecord->BusinessState, true));
							$businessNode->AppendChild(new XmlDomNode('zip', $addressBookRecord->BusinessZip, true));
							$businessNode->AppendChild(new XmlDomNode('country', $addressBookRecord->BusinessCountry, true));
							$businessNode->AppendChild(new XmlDomNode('fax', $addressBookRecord->BusinessFax, true));
							$businessNode->AppendChild(new XmlDomNode('phone', $addressBookRecord->BusinessPhone, true));
							$businessNode->AppendChild(new XmlDomNode('web', $addressBookRecord->BusinessWeb, true));
								
							$contactNode->AppendChild($businessNode);
								
							$otherNode = &new XmlDomNode('other');
							$otherNode->AppendChild(new XmlDomNode('email', $addressBookRecord->OtherEmail, true));
							$otherNode->AppendChild(new XmlDomNode('notes', $addressBookRecord->Notes, true));
	
							$contactNode->AppendChild($otherNode);
	
							$groupsNode = &new XmlDomNode('groups');
								
							$groupsArray = &$dbStorage->SelectAddressGroupContact($idAddress);
								
							foreach ($groupsArray as $id => $value)
							{
								$groupNode = &new XmlDomNode('group');
								$groupNode->AppendAttribute('id', $id);
								$groupNode->AppendChild(new XmlDomNode('name', $value, true));
	
								$groupsNode->AppendChild($groupNode);
							}
								
							$contactNode->AppendChild($groupsNode);
								
							$xmlRes->XmlRoot->AppendChild($contactNode);
						}
						else
						{
							printErrorAndExit(PROC_CANT_GET_CONT_FROM_DB, $xmlRes);
						}
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
					break;
						
				case 'group':
					if (!$settings->AllowContacts)
					{
						printErrorAndExit(PROC_CANT_GET_CONTS_FROM_DB, $xmlRes);
					}				
					$groupId = (int) $xmlObj->GetParamValueByName('id_group');
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
					if ($dbStorage->Connect() && $group = &$dbStorage->SelectGroupById($groupId))
					{
						$groupNode = &new XmlDomNode('group');
						$groupNode->AppendAttribute('id', $groupId);
						$groupNode->AppendAttribute('organization', (int) $group->IsOrganization);
	
						$groupNode->AppendChild(new XmlDomNode('name', $group->Name, true));
						$groupNode->AppendChild(new XmlDomNode('email', $group->Email, true));
						$groupNode->AppendChild(new XmlDomNode('company', $group->Company, true));
						$groupNode->AppendChild(new XmlDomNode('street', $group->Street, true));
						$groupNode->AppendChild(new XmlDomNode('city', $group->City, true));
						$groupNode->AppendChild(new XmlDomNode('state', $group->State, true));
						$groupNode->AppendChild(new XmlDomNode('zip', $group->Zip, true));
						$groupNode->AppendChild(new XmlDomNode('country', $group->Country, true));
						$groupNode->AppendChild(new XmlDomNode('phone', $group->Phone, true));
						$groupNode->AppendChild(new XmlDomNode('fax', $group->Fax, true));
						$groupNode->AppendChild(new XmlDomNode('web', $group->Web, true));
	
						$contacts = &$dbStorage->SelectAddressGroupContacts($groupId);
	
						$contactsNode = &new XmlDomNode('contacts');
	
						if ($contacts != null)
						{
							foreach (array_keys($contacts->Instance()) as $key)
							{
								$contact = &$contacts->Get($key);
	
								$contactNode = &new XmlDomNode('contact');
								$contactNode->AppendAttribute('id', $contact->Id);
								$contactNode->AppendChild(new XmlDomNode('fullname', $contact->Name, true));
								$contactNode->AppendChild(new XmlDomNode('email', $contact->Email, true));
	
								$contactsNode->AppendChild($contactNode);
	
							}
							$groupNode->AppendChild($contactsNode);
	
							$xmlRes->XmlRoot->AppendChild($groupNode);
						}
						else
						{
							printErrorAndExit(PROC_CANT_GET_CONTS_FROM_DB, $xmlRes);
						}
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
					break;
						
				case 'groups':
					if (!$settings->AllowContacts)
					{
						printErrorAndExit(PROC_CANT_GET_CONTS_FROM_DB, $xmlRes);
					}
					
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
					if ($dbStorage->Connect())
					{
						$groupsNode = &new XmlDomNode('groups');
	
						$groupNames = &$dbStorage->SelectUserAddressGroupNames();
	
						foreach ($groupNames as $id => $name)
						{
							$groupNode = &new XmlDomNode('group');
							$groupNode->AppendAttribute('id', $id);
							$groupNode->AppendChild(new XmlDomNode('name', $name, true));
								
							$groupsNode->AppendChild($groupNode);
						}
	
						$xmlRes->XmlRoot->AppendChild($groupsNode);
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
					break;
						
				case 'settings_list':
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					$mailBoxesSize = 0;
						
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
					if (!$dbStorage->Connect())
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
					else
					{
						$mailBoxesSize = $dbStorage->SelectMailboxesSize();
							
						$settingsListNode = &new XmlDomNode('settings_list');
						$settingsListNode->AppendAttribute('show_text_labels', (int) $settings->ShowTextLabels);
						$settingsListNode->AppendAttribute('allow_change_settings', (int) $account->AllowChangeSettings);
						$settingsListNode->AppendAttribute('allow_dhtml_editor', (int) $account->AllowDhtmlEditor);
						$settingsListNode->AppendAttribute('allow_add_account', (int) $settings->AllowUsersAddNewAccounts);
						$settingsListNode->AppendAttribute('msgs_per_page', (int) $account->MailsPerPage);
						$settingsListNode->AppendAttribute('contacts_per_page', (int) $account->ContactsPerPage);
						$settingsListNode->AppendAttribute('mailbox_limit', (int) $account->MailboxLimit);
						$settingsListNode->AppendAttribute('enable_mailbox_size_limit', (int) $settings->EnableMailboxSizeLimit);
						$settingsListNode->AppendAttribute('mailbox_size', (int) $mailBoxesSize);
						$settingsListNode->AppendAttribute('hide_folders', (int) $account->HideFolders);
						$settingsListNode->AppendAttribute('horiz_resizer', (int) $account->HorizResizer);
						$settingsListNode->AppendAttribute('vert_resizer', (int) $account->VertResizer);
						$settingsListNode->AppendAttribute('mark', (int) $account->Mark);
						$settingsListNode->AppendAttribute('reply', (int) $account->Reply);
						$settingsListNode->AppendAttribute('view_mode', (int) $account->ViewMode);
						$settingsListNode->AppendAttribute('def_timezone', $account->DefaultTimeZone);
						$settingsListNode->AppendAttribute('allow_direct_mode', (int) $account->AllowDirectMode);
						$settingsListNode->AppendAttribute('direct_mode_is_default', (int) $settings->DirectModeIsDefault);
						$settingsListNode->AppendAttribute('allow_contacts', (int) $settings->AllowContacts);
						$settingsListNode->AppendAttribute('allow_calendar', (int) $settings->AllowCalendar);
	
						$skin = '';
	
						$skins = &FileSystem::GetSkinsList();
							
						$hasDefSettingsSkin = false;
						foreach ($skins as $skinName)
						{
							if ($skinName == $settings->DefaultSkin)
							{
								$hasDefSettingsSkin = true;
							}
								
							if ($skinName == $account->DefaultSkin)
							{
								$skin = $account->DefaultSkin;
								break;
							}
						}
	
						if ($skin == '')
						{
							$skin = ($hasDefSettingsSkin) ? $settings->DefaultSkin : $skins[0];
						}
	
						$settingsListNode->AppendChild(new XmlDomNode('def_skin', $skin, true));
	
						$settingsListNode->AppendChild(new XmlDomNode('def_lang', $account->DefaultLanguage, true));
						$settingsListNode->AppendChild(new XmlDomNode('def_date_fmt', $account->DefaultDateFormat, true));
						$settingsListNode->AppendAttribute('time_format', $account->DefaultTimeFormat);
	
						if (is_array($account->Columns) && count($account->Columns) > 0)
						{
							$columnsNode = &new XmlDomNode('columns');
							foreach ($account->Columns AS $id_column => $column_value)
							{
								$columnNode = new XmlDomNode('column');
								$columnNode->AppendAttribute('id', $id_column);
								$columnNode->AppendAttribute('value', $column_value);
								$columnsNode->AppendChild($columnNode);
								unset($columnNode);
							}
							$settingsListNode->AppendChild($columnsNode);
						}
	
						$xmlRes->XmlRoot->AppendChild($settingsListNode);
					}
					break;
			}
					
			printXML($xmlRes);
			break;
															
		case 'delete':
			switch ($xmlObj->GetParamValueByName('request'))
			{
				case 'account':
					$account = &Account::LoadFromDb($accountId);
					if (!$account->AllowChangeSettings)
					{
						printErrorAndExit(PROC_CANT_DEL_ACCT_BY_ID, $xmlRes);
					}
					
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
						
					if ($account->IsDemo)
					{
						printErrorAndExit(PROC_CANT_DEL_ACCT_BY_ID, $xmlRes);
						break;
					}
						
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
					if ($dbStorage->Connect())
					{
						$accountIds = $dbStorage->GetAccountListByUserId($account->IdUser);
					}
					else
					{
						$accountIds = array();
					}
						
					if ((int) $xmlObj->GetParamValueByName('id_acct') == $accountId)
					{
						$c = count($accountIds);
						if ($c > 1)
						{
							for ($i = 0; $i < $c; $i++)
							{
								if ($accountId != $accountIds[$i])
								{
									$_SESSION[ACCOUNT_ID] = $accountIds[$i];
								}
							}
						}
	
						$idAcct = $xmlObj->GetParamValueByName('id_acct');
						if ($dbStorage->Connect() && $dbStorage->IsAccountInRing($_SESSION[ACCOUNT_ID], $idAcct) && Account::DeleteFromDb($idAcct))
						{
							$fs = new FileSystem(INI_DIR.'/mail', $account->Email, $account->Id);
							$fs->DeleteAccountDirs();
								
							$fs2 = new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
							$fs2->DeleteAccountDirs();
							unset($fs, $fs2);
								
							GetAccountList($account, -1, $_SESSION[ACCOUNT_ID]);
						}
						else
						{
							printErrorAndExit(PROC_CANT_DEL_ACCT_BY_ID, $xmlRes);
						}
					}
					else
					{
						$idAcct = $xmlObj->GetParamValueByName('id_acct');
						if ($dbStorage->Connect() && $dbStorage->IsAccountInRing($_SESSION[ACCOUNT_ID], $idAcct) && Account::DeleteFromDb($idAcct))
						{
							$fs = new FileSystem(INI_DIR.'/mail', $account->Email, $account->Id);
							$fs->DeleteAccountDirs();
								
							$fs2 = new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
							$fs2->DeleteAccountDirs();
							unset($fs, $fs2);
								
							GetAccountList($account, -1);
						}
						else
						{
							printErrorAndExit(PROC_CANT_DEL_ACCT_BY_ID, $xmlRes);
						}
	
					}
					break;
						
				case 'filter':
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
					if ($dbStorage->Connect())
					{
						$acctId = $xmlObj->GetParamValueByName('id_acct');
						$editAccount = &Account::LoadFromDb($acctId);
	
						if ($editAccount->IsDemo || $dbStorage->DeleteFilter($xmlObj->GetParamValueByName('id_filter'), $acctId))
						{
							GetFiltersList($acctId);
						}
						else
						{
							printErrorAndExit(PROC_CANT_DEL_FILTER_BY_ID, $xmlRes);
						}
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
					break;
	
				case 'folders':
					$acctId = $xmlObj->GetParamValueByName('id_acct');
						
					$account = &Account::LoadFromDb($acctId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
	
					$processor = &new MailProcessor($account);
						
					$foldersNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('folders');
						
					$result = true;
						
					foreach (array_keys($foldersNode->Children) as $key)
					{
						$folder = &new Folder($acctId, $foldersNode->Children[$key]->Attributes['id'],
						$foldersNode->Children[$key]->GetChildValueByTagName('full_name'));
	
						$processor->GetFolderInfo($folder);
						$processor->GetFolderMessageCount($folder);
	
						$childCount = $processor->DbStorage->GetFolderChildCount($folder);
	
						if ($account->MailProtocol == MAILPROTOCOL_IMAP4 &&	($folder->MessageCount > 0 || $childCount != 0))
						{
							$result = false;
						}
						else
						{
							$result &= ($account->IsDemo) ? true : $processor->DeleteFolder($folder);
						}
					}
						
					if ($result)
					{
						$folders = &$processor->GetFolders();
	
						$foldersList = &new XmlDomNode('folders_list');
						$foldersList->AppendAttribute('sync', -1);
						$foldersList->AppendAttribute('id_acct', $acctId);
	
						GetFoldersTreeXml($folders, $foldersList, $processor);
						$xmlRes->XmlRoot->AppendChild($foldersList);
	
					}
					else
					{
						printErrorAndExit(PROC_ERROR_DEL_FLD, $xmlRes);
					}
					break;
						
				case 'contacts':
					if (!$settings->AllowContacts)
					{
						printErrorAndExit(PROC_CANT_DEL_CONT_GROUPS, $xmlRes);
					}
	
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
					if ($dbStorage->Connect())
					{
						$result = true;
	
						$contactsNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('contacts');
						foreach (array_keys($contactsNode->Children) as $key)
						{
							$result &= $dbStorage->DeleteAddressBookRecord($contactsNode->Children[$key]->Attributes['id']);
						}
	
						$groupsNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('groups');
						foreach (array_keys($groupsNode->Children) as $key)
						{
							$result &= $dbStorage->DeleteAddressGroup($groupsNode->Children[$key]->Attributes['id']);
						}
	
						if ($result)
						{
							GetContactList($accountId);
						}
						else
						{
							printErrorAndExit(PROC_CANT_DEL_CONT_GROUPS, $xmlRes);
						}
	
					}
					else
					{
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
						
					break;
			}
				
			printXML($xmlRes);
			break;
																
		case 'operation_messages':
	
			$account = &Account::LoadFromDb($accountId);
			if (!$account)
			{
				printErrorAndExit('', $xmlRes, 2);
			}
				
			$processor = &new MailProcessor($account);
				
			$messagesRequestNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('messages');
	
			$folderNodeRequest = &$messagesRequestNode->GetChildNodeByTagName('folder');
				
			$folder = &new Folder($accountId, $folderNodeRequest->Attributes['id'],
			ConvertUtils::WMBackHtmlSpecialChars($folderNodeRequest->GetChildValueByTagName('full_name')));
				
			$processor->GetFolderInfo($folder);
				
			$toFolderNodeRequest = &$messagesRequestNode->GetChildNodeByTagName('to_folder');
				
			$toFolder = &new Folder($accountId, $toFolderNodeRequest->Attributes['id'],
			ConvertUtils::WMBackHtmlSpecialChars($toFolderNodeRequest->GetChildValueByTagName('full_name')));
				
			$processor->GetFolderInfo($toFolder);
	
			$operationNode = &new XmlDomNode('operation_messages');
	
			$toFolderNode = &new XmlDomNode('to_folder', '<![CDATA['.$toFolder->FullName.']]>');
			$toFolderNode->AppendAttribute('id', $toFolder->IdDb);
			$operationNode->AppendChild($toFolderNode);
	
			$folderNode = &new XmlDomNode('folder', $folder->FullName, true);
			$folderNode->AppendAttribute('id', $folder->IdDb);
			$operationNode->AppendChild($folderNode);
				
			$messagesNode = &new XmlDomNode('messages');
				
			$messageIdUidSet = array();
				
			$folders = array();
				
			foreach (array_keys($messagesRequestNode->Children) as $nodeKey)
			{
				$messageNode = &$messagesRequestNode->Children[$nodeKey];
	
				if ($messageNode->TagName != 'message')
				{
					continue;
				}
	
				$msgId = $messageNode->Attributes['id'];
				$msgUid = $messageNode->GetChildValueByTagName('uid', true);
	
				$msgFolder = &$messageNode->GetChildNodeByTagName('folder');
	
				$msgFolderId = $msgFolder->Attributes['id'];
				$folders[$msgFolderId] = $msgFolder->GetChildValueByTagName('full_name', true);
	
				$messageIdUidSet[$msgFolderId][$msgId] = $msgUid;
	
				$message = &new XmlDomNode('message');
				$message->AppendAttribute('id', $msgId);
				$message->AppendChild(new XmlDomNode('uid', $msgUid, true));
	
				$msgFolderNode = &new XmlDomNode('folder', $folders[$msgFolderId], true);
				$msgFolderNode->AppendAttribute('id', $msgFolderId);
	
				$message->AppendChild($msgFolderNode);
	
				$messagesNode->AppendChild($message);
			}
				
			$operationNode->AppendChild($messagesNode);
				
			$errorNode = null;
	
			switch ($xmlObj->GetParamValueByName('request'))
			{
				case 'mark_all_read':
					$messageIdUidSet = null;
					if ($processor->SetFlags($messageIdUidSet, $folder, MESSAGEFLAGS_Seen, ACTION_Set))
					{
						$operationNode->AppendAttribute('type', 'mark_all_read');
						$xmlRes->XmlRoot->AppendChild($operationNode);
					}
					else
					{
						$errorNode = &new XmlDomNode('error', PROC_CANT_MARK_ALL_MSG_READ, true);
					}
					break;
				case 'mark_all_unread':
					$messageIdUidSet = null;
					if ($processor->SetFlags($messageIdUidSet, $folder, MESSAGEFLAGS_Seen, ACTION_Remove))
					{
						$operationNode->AppendAttribute('type', 'mark_all_unread');
					}
					else
					{
						$errorNode = &new XmlDomNode('error', PROC_CANT_MARK_ALL_MSG_UNREAD, true);
					}
					break;
				case 'purge':
					if (USEIMAPTRASH && $account->MailProtocol == MAILPROTOCOL_IMAP4 && $folder->Type == FOLDERTYPE_Trash)
					{
						if ($processor->EmptyFolder($folder))
						{
							$operationNode->AppendAttribute('type', 'purge');
						}
						else
						{
							$errorNode = &new XmlDomNode('error', PROC_CANT_PURGE_MSGS, true);
						}
					}
					else
					{
						if ($processor->PurgeFolder($folder))
						{
							$operationNode->AppendAttribute('type', 'purge');
						}
						else
						{
							$errorNode = &new XmlDomNode('error', PROC_CANT_PURGE_MSGS, true);
						}
					}
					break;
			}
				
			foreach ($folders as $idFolder => $fullNameFolder)
			{
				$folder = &new Folder($accountId, $idFolder, $fullNameFolder);
				$processor->GetFolderInfo($folder);
	
				switch ($xmlObj->GetParamValueByName('request'))
				{
					case 'delete':
						if ($processor->DeleteMessages($messageIdUidSet[$idFolder], $folder))
						{
							$operationNode->AppendAttribute('type', 'delete');
						}
						else
						{
							$errorNode = &new XmlDomNode('error', PROC_CANT_DEL_MSGS, true);
						}
						break;
					case 'undelete':
						if ($processor->SetFlags($messageIdUidSet[$idFolder], $folder, MESSAGEFLAGS_Deleted, ACTION_Remove))
						{
							$operationNode->AppendAttribute('type', 'undelete');
						}
						else
						{
							$errorNode = &new XmlDomNode('error', PROC_CANT_UNDEL_MSGS, true);
						}
						break;
					case 'mark_read':
						if ($processor->SetFlags($messageIdUidSet[$idFolder], $folder, MESSAGEFLAGS_Seen, ACTION_Set))
						{
							$operationNode->AppendAttribute('type', 'mark_read');
						}
						else
						{
							$errorNode = &new XmlDomNode('error', PROC_CANT_MARK_MSGS_READ, true);
						}
						break;
					case 'mark_unread':
						if ($processor->SetFlags($messageIdUidSet[$idFolder], $folder, MESSAGEFLAGS_Seen, ACTION_Remove))
						{
							$operationNode->AppendAttribute('type', 'mark_unread');
						}
						else
						{
							$errorNode = &new XmlDomNode('error', PROC_CANT_MARK_MSGS_UNREAD, true);
						}
						break;
					case 'flag':
						if ($processor->SetFlags($messageIdUidSet[$idFolder], $folder, MESSAGEFLAGS_Flagged, ACTION_Set))
						{
							$operationNode->AppendAttribute('type', 'flag');
						}
						else
						{
							$errorNode = &new XmlDomNode('error', PROC_CANT_SET_MSG_FLAGS, true);
						}
						break;
					case 'unflag':
						if ($processor->SetFlags($messageIdUidSet[$idFolder], $folder, MESSAGEFLAGS_Flagged, ACTION_Remove))
						{
							$operationNode->AppendAttribute('type', 'unflag');
						}
						else
						{
							$errorNode = &new XmlDomNode('error', PROC_CANT_REMOVE_MSG_FLAGS, true);
						}
						break;
					case 'move_to_folder':
						if ($processor->MoveMessages($messageIdUidSet[$idFolder], $folder, $toFolder))
						{
							$operationNode->AppendAttribute('type', 'move_to_folder');
						}
						else
						{
							$errorNode = &new XmlDomNode('error', PROC_CANT_CHANGE_MSG_FLD, true);
						}
						break;
				}
	
				if ($errorNode != null)
				{
					break;
				}
			}
			
			if ($errorNode == null)
			{
				$xmlRes->XmlRoot->AppendChild($operationNode);
			}
			else
			{
				$xmlRes->XmlRoot->AppendChild($errorNode);
			}
	
			printXML($xmlRes);
			break;
																				
		case 'send':
			switch ($xmlObj->GetParamValueByName('request'))
			{
				case 'message':
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
	
					$message = &CreateMessage($account, $xmlObj);
						
					$processor = &new MailProcessor($account);
					$folders = &$processor->GetFolders();
					$folder = &$folders->GetFolderByType(FOLDERTYPE_SentItems);
						
					$message->OriginalMailMessage = $message->ToMailString(true);
					$message->Flags |= MESSAGEFLAGS_Seen;
						
					$from = &$message->GetFrom();
	
					$result = true;
					$needToDelete = ($message->IdMsg != -1);
					$idtoDelete = $message->IdMsg;
					if (CSmtp::SendMail($account, $message, null, null))
					{
						$messageNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('message');
						$attachmentsNode = &$messageNode->GetChildNodeByTagName('attachments');
						
						if ($attachmentsNode != null)
						{
							$filesArray = array();
							foreach(array_keys($attachmentsNode->Children) as $key)
							{
								$attachNode = &$attachmentsNode->Children[$key];
								$tempName = $attachNode->GetChildValueByTagName('temp_name');
								if ($tempName)
								{
									$filesArray[] = $tempName;
								}
							}
							
							if (count($filesArray) > 0)
							{
								$fs = &new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
								$fs->DeleteTempFilesByArray($filesArray, $_SESSION['attachtempdir']);
								unset($fs);
							}
						}
						
						if ($processor->DbStorage->Connect())
						{
							if ($needToDelete)
							{
								$draftsFolder = &$folders->GetFolderByType(FOLDERTYPE_Drafts);
								if (!$processor->SaveMessage($message, $folder, $draftsFolder))
								{
									$needToDelete = false;
								}
							}
							else
							{
								if (!$processor->SaveMessage($message, $folder))
								{
									$needToDelete = false;
								}
							}
	
							//Suggestion
							$mNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('message');
							$hNode = &$mNode->GetChildNodeByTagName('headers');
							$gNode = &$hNode->GetChildNodeByTagName('groups');
							$toNode = &$hNode->GetChildNodeByTagName('to');
							$ccNode = &$hNode->GetChildNodeByTagName('cc');
							$bccNode = &$hNode->GetChildNodeByTagName('bcc');
							$emailsString = '';
							$gids = array();
								
							if ($gNode != null && $gNode->Value != null)
							{
								if (count($gNode->Children) > 0)
								{
									foreach(array_keys($gNode->Children) as $key)
									{
										$oneGNode = &$gNode->Children[$key];
										$gids[] = isset($oneGNode->Attributes['id']) ? (int) $oneGNode->Attributes['id'] : -1;
									}
								}
							}
								
							if($toNode != null && $toNode->Value != null)
							{
								$emailsString .= ConvertUtils::WMBackHtmlSpecialChars($toNode->Value) . ', ';
							}
								
							if($ccNode != null && $ccNode->Value != null)
							{
								$emailsString .= ConvertUtils::WMBackHtmlSpecialChars($ccNode->Value) . ', ';
							}
								
							if($bccNode != null && $bccNode->Value != null)
							{
								$emailsString .= ConvertUtils::WMBackHtmlSpecialChars($bccNode->Value);
							}
								
							$emailsString = trim(trim($emailsString), ',');
								
							$emailsCollection = new EmailAddressCollection($emailsString);
								
							$arrEmails = array();
								
							for($l = 0; $l < $emailsCollection->Count(); $l++)
							{
								$emailObj = &$emailsCollection->Get($l);
	
								if(trim($emailObj->Email))
								{
									$arrEmails[$emailObj->Email] = trim($emailObj->DisplayName);
								}
							}
								
							if (count($gids) > 0)
							{
								$processor->DbStorage->UpdateGroupsFrequency($gids);
							}
								
							$processor->DbStorage->UpdateSuggestTable($account, $arrEmails);
							//End suggestion
								
							if ($needToDelete)
							{
								$messageIdSet = array($idtoDelete);
								if ($account->MailProtocol == MAILPROTOCOL_IMAP4)
								{
									if ($processor->PurgeFolder($draftsFolder))
									{
										$processor->DbStorage->DeleteMessages($messageIdSet, false, $draftsFolder);
									}
								}
								else
								{
									$processor->DbStorage->DeleteMessages($messageIdSet, false, $draftsFolder);
								}
							}
								
							$processor->DbStorage->UpdateMailboxSize();
						}
							
						$result = true;
					}
					else
					{
						$result = false;
					}
						
					if ($result)
					{
						$updateNode = &new XmlDomNode('update');
						$updateNode->AppendAttribute('value', 'send_message');
						$xmlRes->XmlRoot->AppendChild($updateNode);
					}
					else
					{
						printErrorAndExit(PROC_CANT_SEND_MSG.' '.getGlobalError(), $xmlRes);
					}
	
					printXML($xmlRes);
					break;
			}
			break;
	
		case 'save':
			switch ($xmlObj->GetParamValueByName('request'))
			{
				case 'message':
					$account = &Account::LoadFromDb($accountId);
					if (!$account)
					{
						printErrorAndExit('', $xmlRes, 2);
					}
					
					$message = &CreateMessage($account, $xmlObj);
						
					$mNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('message');
					$hNode = &$mNode->GetChildNodeByTagName('headers');
					$gNode = &$hNode->GetChildNodeByTagName('groups');
					$toNode = &$hNode->GetChildNodeByTagName('to');
					$ccNode = &$hNode->GetChildNodeByTagName('cc');
					$bccNode = &$hNode->GetChildNodeByTagName('bcc');
					$emailsString = '';
					$gids = array();
						
					if ($gNode != null)
					{
						foreach(array_keys($gNode->Children) as $key)
						{
							$oneGNode = &$gNode->Children[$key];
							$gids[] = isset($oneGNode->Attributes['id']) ? (int) $oneGNode->Attributes['id'] : -1;
						}
					}
						
					$result = true;
						
					$processor = &new MailProcessor($account);
					if(!$processor->DbStorage->Connect())
					{
						$result = false;
						printErrorAndExit(getGlobalError(), $xmlRes);
					}
						
					// Update group frequency
					$processor->DbStorage->UpdateGroupsFrequency($gids);
						
					$folders = &$processor->GetFolders();
					$folder = &$folders->GetFolderByType(FOLDERTYPE_Drafts);
						
					$from = &$message->GetFrom();
					$message->OriginalMailMessage = $message->ToMailString();
					$message->Flags |= MESSAGEFLAGS_Seen;
						
					$messageIdUidSet = array();
					$messageIdUidSet[$message->IdMsg] = $message->Uid;
						
					if ($message->IdMsg != -1)
					{
						$messageIdSet = array($message->IdMsg);
					}
					
					if ($result)
					{
						$result = ($message->IdMsg != -1)
								? $processor->UpdateMessage($message, $folder)
								: $processor->SaveMessage($message, $folder);
						
						if ($result)
						{
							if ($processor->SetFlags($messageIdUidSet, $folder, MESSAGEFLAGS_Seen, ACTION_Set));
							{
								if ($message->IdMsg != -1 && $account->MailProtocol == MAILPROTOCOL_IMAP4)
								{
									if ($processor->PurgeFolder($folder))
									{
										$processor->DbStorage->DeleteMessages($messageIdSet, false, $folder);
									}
								}
							}
						}
						$processor->DbStorage->UpdateMailboxSize();
					}
					else
					{
						$result = false;
					}
						
					if ($result)
					{
						$updateNode = new XmlDomNode('update');
						$updateNode->AppendAttribute('value', 'save_message');
						if ($message)
						{
							$updateNode->AppendAttribute('id', $message->IdMsg);
							$uidNode = new XmlDomNode('uid', $message->Uid, true);
							$updateNode->AppendChild($uidNode);
						}
						$xmlRes->XmlRoot->AppendChild($updateNode);
					}
					else
					{
						printErrorAndExit(PROC_CANT_SAVE_MSG.' '.getGlobalError(), $xmlRes);
					}
	
					printXML($xmlRes);
					break;
			}
			break;
	}
	
	$log->WriteLine('>>> EMPTY XML PACK');
	
	/**
	 * @param FolderCollection $folders
	 * @param XmlDomNode $nodeTree
	 * @param MailProcessor $processor
	 */
	function GetFoldersTreeXml(&$folders, &$nodeTree, &$processor)
	{
		for ($i = 0, $count = $folders->Count(); $i < $count; $i++)
		{
			$folder = &$folders->Get($i);
			$folderNode = &new XmlDomNode('folder');
			$folderNode->AppendAttribute('id', $folder->IdDb);
			$folderNode->AppendAttribute('id_parent', $folder->IdParent);
			$folderNode->AppendAttribute('type', $folder->Type);
			$folderNode->AppendAttribute('sync_type', $folder->SyncType);
			$folderNode->AppendAttribute('hide', (int) $folder->Hide);
			$folderNode->AppendAttribute('fld_order', $folder->FolderOrder);
				
			if ($folder->SyncType == FOLDERSYNC_DirectMode)
			{
				$processor->GetFolderMessageCount($folder);
			}
				
			$folderNode->AppendAttribute('count', $folder->MessageCount);
			$folderNode->AppendAttribute('count_new', $folder->UnreadMessageCount);
			$folderNode->AppendAttribute('size', $folder->Size);
				
			if (ConvertUtils::IsLatin($folder->Name))
			{
				$folderNode->AppendChild(new XmlDomNode('name',
				ConvertUtils::ConvertEncoding($folder->Name,
				CPAGE_UTF7_Imap, CPAGE_UTF8), true));
			}
			else
			{
				$folderNode->AppendChild(new XmlDomNode('name',
				ConvertUtils::ConvertEncoding($folder->Name,
				$processor->_account->DefaultIncCharset, CPAGE_UTF8), true));
			}
	
			$folderNode->AppendChild(new XmlDomNode('full_name', $folder->FullName, true));
				
			if ($folder->SubFolders != null && $folder->SubFolders->Count() > 0)
			{
				$foldersNode = &new XmlDomNode('folders');
				GetFoldersTreeXml($folder->SubFolders, $foldersNode, $processor);
				$folderNode->AppendChild($foldersNode);
			}
				
			$nodeTree->AppendChild($folderNode);
		}
	}
	
	/**
	 * @param Account $account
	 * @param int $lastId
	 */
	function GetAccountList(&$account, $lastId, $currId = '')
	{
		global $xmlRes;
	
		$currId = ($currId) ? $currId : $account->Id;
	
		$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
		if ($dbStorage->Connect())
		{
			$accounts = &$dbStorage->SelectAccounts($account->IdUser);
			if ($accounts !== null)
			{
				$acctsNode = &new XmlDomNode('accounts');
				$acctsNode->AppendAttribute('last_id', $lastId);
				$acctsNode->AppendAttribute('curr_id', $currId);
				foreach ($accounts as $acct_id => $acctArray)
				{
					$acct = &new XmlDomNode('account');
					$acct->AppendAttribute('id', $acct_id);
					$acct->AppendAttribute('mail_protocol', $acctArray[0]);
					$acct->AppendAttribute('def_order', $acctArray[1]);
					$acct->AppendAttribute('use_friendly_nm', $acctArray[2]);
					$acct->AppendAttribute('def_acct', intval((bool) $acctArray[6]));
						
					$acct->AppendChild(new XmlDomNode('friendly_name', '<![CDATA['.$acctArray[3].']]>'));
					$acct->AppendChild(new XmlDomNode('email', '<![CDATA['.$acctArray[4].']]>'));
						
					$acctsNode->AppendChild($acct);
				}
				$xmlRes->XmlRoot->AppendChild($acctsNode);
			}
			else
			{
				printErrorAndExit(PROC_CANT_GET_ACCT_LIST, $xmlRes);
			}
		}
		else
		{
			printErrorAndExit(getGlobalError(), $xmlRes);
		}
	}
	
	/**
	 * @param int $accountId
	 */
	function GetContactList($accountId)
	{
		global $xmlObj, $xmlRes;
	
		$account = &Account::LoadFromDb($accountId);
		if (!$account) printErrorAndExit('', $xmlRes, 2);
	
		$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
		if ($dbStorage->Connect())
		{
			$pageNumber = $xmlObj->GetParamValueByName('page');
			$sortField = $xmlObj->GetParamValueByName('sort_field');
			$sortOrder = (bool) $xmlObj->GetParamValueByName('sort_order');
	
			$idGroup = $xmlObj->GetParamValueByName('id_group');
			$lookForNode = $xmlObj->XmlRoot->GetChildNodeByTagName('look_for');
			$lookForField = $xmlObj->XmlRoot->GetChildValueByTagName('look_for', true);
				
			if ($lookForNode)
			{
				$lookForType = isset($lookForNode->Attributes['type']) ? $lookForNode->Attributes['type'] : 0;
			}
			else
			{
				$lookForType = 0;
			}
	
			if ($lookForField == '')
			{
				$countArray = &$dbStorage->SelectAddressContactsAndGroupsCount($lookForType, $account->IdUser);
			}
			else
			{
				if ($idGroup == -1)
				{
					$countArray = &$dbStorage->SelectAddressContactsAndGroupsCount($lookForType, $account->IdUser, $lookForField);
				}
				else
				{
					$countArray = &$dbStorage->SelectAddressContactsAndGroupsCount($lookForType, $account->IdUser, $lookForField, $idGroup);
				}
			}
				
			$contactsNode = &new XmlDomNode('contacts_groups');
			$contactsNode->AppendAttribute('contacts_count', $countArray[0]);
			$contactsNode->AppendAttribute('groups_count', $countArray[1]);
				
			$contactsNode->AppendAttribute('page', $pageNumber);
			$contactsNode->AppendAttribute('sort_field', $sortField);
			$contactsNode->AppendAttribute('sort_order', (int) $sortOrder);
				
			$contactsNode->AppendAttribute('id_group', (int) $idGroup);
				
			$newLookForNode = new XmlDomNode('look_for', $lookForField, true);
			$newLookForNode->AppendAttribute('type', $lookForType);
				
			$contactsNode->AppendChild($newLookForNode);
				
			$contacts = null;
				
			$countContactsAndGroups = $countArray[0] + $countArray[1];
				
			if($countContactsAndGroups < ($pageNumber-1) * $account->ContactsPerPage)
			{
				$pageNumber = 1;
			}
				
			if ($lookForField == '')
			{
				$contacts = &$dbStorage->LoadContactsAndGroups($pageNumber, $sortField, $sortOrder);
			}
			else
			{
				if($countContactsAndGroups)
				{
					$contacts = &$dbStorage->SearchContactsAndGroups($pageNumber, $lookForField, $idGroup, $sortField, $sortOrder, $lookForType);
				}
			}
				
			if ($contacts != null)
			{
				foreach (array_keys($contacts->Instance()) as $key)
				{
					$contact = &$contacts->Get($key);
						
					$contactNode = &new XmlDomNode('contact_group');
					$contactNode->AppendAttribute('id', $contact->Id);
					$contactNode->AppendAttribute('is_group', (int) $contact->IsGroup);
					$contactNode->AppendChild(new XmlDomNode('name', $contact->Name, true));
						
					if ($contact->IsGroup)
					{
						$emailsOfGroup = '';
						$groupContacts = &$dbStorage->SelectAddressGroupContacts($contact->Id);
	
						for ($i = 0, $c = $groupContacts->Count(); $i < $c; $i++)
						{
							$contactOfGroup = $groupContacts->Get($i);
							if (strlen($contactOfGroup->Email) > 0)
							{
								$emailsOfGroup .= (strlen($contactOfGroup->Name) > 0)
								? ($contactOfGroup->UseFriendlyName)
								? '"'.$contactOfGroup->Name.'" <'.$contactOfGroup->Email . '>, '
								: $contactOfGroup->Email . ', '
								: $contactOfGroup->Email . ', ';
							}
						}
						$contactNode->AppendChild(new XmlDomNode('email', trim(trim($emailsOfGroup), ','), true));
	
					}
					else
					{
						$contactNode->AppendChild(new XmlDomNode('email', $contact->Email, true));
					}
					$contactsNode->AppendChild($contactNode);
				}
			}
	
			$xmlRes->XmlRoot->AppendChild($contactsNode);
				
		}
		else
		{
			printErrorAndExit(getGlobalError(), $xmlRes);
		}
	}
	
	/**
	 * @param int $accountId
	 */
	function GetFiltersList($accountId)
	{
		global $xmlObj, $xmlRes;
	
		$null = null;
	
		$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
		if ($dbStorage->Connect())
		{
			$filters = &$dbStorage->SelectFilters($accountId);
				
			if ($filters != null)
			{
				$filtersNode = &new XmlDomNode('filters');
				$filtersNode->AppendAttribute('id_acct', $accountId);
	
				foreach (array_keys($filters->Instance()) as $key)
				{
					$filter = &$filters->Get($key);
					$filterNode = &new XmlDomNode('filter', '<![CDATA['.$filter->Filter.']]>');
					$filterNode->AppendAttribute('id', $filter->Id);
					$filterNode->AppendAttribute('field', $filter->Field);
					$filterNode->AppendAttribute('condition', $filter->Condition);
					$filterNode->AppendAttribute('action', $filter->Action);
					$filterNode->AppendAttribute('id_folder', $filter->IdFolder);
					$filtersNode->AppendChild($filterNode);
				}
				$xmlRes->XmlRoot->AppendChild($filtersNode);
			}
			else
			{
				printErrorAndExit(PROC_CANT_GET_FILTER_LIST, $xmlRes);
			}
				
		}
		else
		{
			printErrorAndExit(getGlobalError(), $xmlRes);
		}
	}
	
	/**
	 * @param XmlDomNode $xmlObj
	 * @return Filter
	 */
	function &GetFilterFromRequest(&$xmlObj)
	{
		$filterNode = &$xmlObj->GetChildNodeByTagName('filter');
	
		$filter = &new Filter();
		$filter->IdAcct = $filterNode->Attributes['id_acct'];
		if (isset($filterNode->Attributes['id']))
		{
			$filter->Id = $filterNode->Attributes['id'];
		}
		$filter->Field = $filterNode->Attributes['field'];
		$filter->Condition = $filterNode->Attributes['condition'];
		$filter->Action = $filterNode->Attributes['action'];
		$filter->IdFolder = $filterNode->Attributes['id_folder'];
		$filter->Filter = $filterNode->Value;
	
		return $filter;
	}
	
	/**
	 * @param XmlDomNode $xmlObj
	 * @param Account $account
	 */
	function UpdateAccountFromRequest(&$xmlObj, &$account)
	{
		$accountNode = $xmlObj->GetChildNodeByTagName('account');
	
		$account->DefaultAccount = (bool) $accountNode->Attributes['def_acct'];
		$account->MailProtocol = $accountNode->Attributes['mail_protocol'];
		$account->MailIncPort = $accountNode->Attributes['mail_inc_port'];
		$account->MailOutPort = $accountNode->Attributes['mail_out_port'];
		$account->MailOutAuthentication = $accountNode->Attributes['mail_out_auth'];
		$account->UseFriendlyName = (bool) $accountNode->Attributes['use_friendly_nm'];
		$account->MailsOnServerDays = $accountNode->Attributes['mails_on_server_days'];
		$account->MailMode = $accountNode->Attributes['mail_mode'];
		$account->GetMailAtLogin = (bool) $accountNode->Attributes['getmail_at_login'];
	
		$account->FriendlyName = $accountNode->GetChildValueByTagName('friendly_nm');
	
		$account->Email = $accountNode->GetChildValueByTagName('email');
			
		$account->MailIncHost = $accountNode->GetChildValueByTagName('mail_inc_host');
		$account->MailIncLogin = $accountNode->GetChildValueByTagName('mail_inc_login');
	
		$mailIncPass = $accountNode->GetChildValueByTagName('mail_inc_pass');
		if ($mailIncPass != DUMMYPASSWORD)
		{
			$account->MailIncPassword = $mailIncPass;
		}
	
		$account->MailOutHost = $accountNode->GetChildValueByTagName('mail_out_host');
		$account->MailOutLogin = $accountNode->GetChildValueByTagName('mail_out_login');
	
		$mailOutPass = $accountNode->GetChildValueByTagName('mail_out_pass');
		if ($mailOutPass != DUMMYPASSWORD)
		{
			$account->MailOutPassword = $mailOutPass;
		}
	}
	
	/**
	 * @param XmlDomNode $xmlObj
	 * @param AddressBookRecord $addressBookRecord
	 * @param int $accountId
	 */
	function UpdateContactFromRequest(&$xmlObj, &$addressBookRecord, $accountId)
	{
		$contactNode = &$xmlObj->GetChildNodeByTagName('contact');
	
		$account = &Account::LoadFromDb($accountId);
	
		$addressBookRecord->IdUser = $account->IdUser;
		if (array_key_exists('id', $contactNode->Attributes))
		{
			$addressBookRecord->IdAddress = $contactNode->Attributes['id'];
		}
	
		$addressBookRecord->PrimaryEmail = $contactNode->Attributes['primary_email'];
		$addressBookRecord->UseFriendlyName = (bool) $contactNode->Attributes['use_friendly_nm'];
		$addressBookRecord->FullName = $contactNode->GetChildValueByTagName('fullname', true);
	
		$birthdayNode = &$contactNode->GetChildNodeByTagName('birthday');
	
		$personalNode = &$contactNode->GetChildNodeByTagName('personal');
		$addressBookRecord->HomeEmail = $personalNode->GetChildValueByTagName('email', true);
		$addressBookRecord->HomeStreet = $personalNode->GetChildValueByTagName('street', true);
		$addressBookRecord->HomeCity = $personalNode->GetChildValueByTagName('city', true);
		$addressBookRecord->HomeState = $personalNode->GetChildValueByTagName('state', true);
		$addressBookRecord->HomeZip = $personalNode->GetChildValueByTagName('zip', true);
		$addressBookRecord->HomeCountry = $personalNode->GetChildValueByTagName('country', true);
		$addressBookRecord->HomeFax = $personalNode->GetChildValueByTagName('fax', true);
		$addressBookRecord->HomePhone = $personalNode->GetChildValueByTagName('phone', true);
		$addressBookRecord->HomeMobile = $personalNode->GetChildValueByTagName('mobile', true);
		$addressBookRecord->HomeWeb = $personalNode->GetChildValueByTagName('web', true);
	
		$businessNode = &$contactNode->GetChildNodeByTagName('business', true);
		$addressBookRecord->BusinessEmail = $businessNode->GetChildValueByTagName('email', true);
		$addressBookRecord->BusinessCompany = $businessNode->GetChildValueByTagName('company', true);
		$addressBookRecord->BusinessJobTitle = $businessNode->GetChildValueByTagName('job_title', true);
		$addressBookRecord->BusinessDepartment = $businessNode->GetChildValueByTagName('department', true);
		$addressBookRecord->BusinessOffice = $businessNode->GetChildValueByTagName('office', true);
		$addressBookRecord->BusinessStreet = $businessNode->GetChildValueByTagName('street', true);
		$addressBookRecord->BusinessCity = $businessNode->GetChildValueByTagName('city', true);
		$addressBookRecord->BusinessState = $businessNode->GetChildValueByTagName('state', true);
		$addressBookRecord->BusinessZip = $businessNode->GetChildValueByTagName('zip', true);
		$addressBookRecord->BusinessCountry = $businessNode->GetChildValueByTagName('country', true);
		$addressBookRecord->BusinessFax = $businessNode->GetChildValueByTagName('fax', true);
		$addressBookRecord->BusinessPhone = $businessNode->GetChildValueByTagName('phone', true);
		$addressBookRecord->BusinessWeb = $businessNode->GetChildValueByTagName('web', true);
	
		$otherNode = &$contactNode->GetChildNodeByTagName('other', true);
		$addressBookRecord->OtherEmail = $otherNode->GetChildValueByTagName('email', true);
		$addressBookRecord->Notes = $otherNode->GetChildValueByTagName('notes', true);
	
		$addressBookRecord->BirthdayDay = $birthdayNode->Attributes['day'];
		$addressBookRecord->BirthdayMonth = $birthdayNode->Attributes['month'];
		$addressBookRecord->BirthdayYear = $birthdayNode->Attributes['year'];
	
	}
	
	/**
	 * @param Account $account
	 * @param XmlDocument $xmlObj
	 * @return WebMailMessage
	 */
	function &CreateMessage(&$account, &$xmlObj)
	{
		global $log;
	
		$messageNode = &$xmlObj->XmlRoot->GetChildNodeByTagName('message');
		$headersNode = &$messageNode->GetChildNodeByTagName('headers');
	
		$message = &new WebMailMessage();
		$GLOBALS[MailDefaultCharset] = $account->GetUserCharset();
		$GLOBALS[MailInputCharset] = $account->GetUserCharset();
		$GLOBALS[MailOutputCharset] = $account->GetDefaultOutCharset();
	
		$message->Headers->SetHeaderByName(MIMEConst_MimeVersion, '1.0');
		$message->Headers->SetHeaderByName(MIMEConst_XMailer, 'MailBee WebMail Pro PHP');
		$message->Headers->SetHeaderByName(MIMEConst_XOriginatingIp, isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0');
	
		$message->IdMsg = $messageNode->GetAttribute('id', -1);
		$message->SetPriority($messageNode->GetAttribute('priority', 3));
	
		$message->Uid = $messageNode->GetChildValueByTagName('uid');
	
		$serverAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['SERVER_NAME'] : 'cantgetservername';
		$message->Headers->SetHeaderByName(MIMEConst_MessageID,
						'<'.substr(session_id(), 0, 7).'.'.md5(time()).'@'. $serverAddr .'>');
	
		$temp = $headersNode->GetChildValueByTagName('from');
		if ($temp)
		{
			$message->SetFromAsString(ConvertUtils::WMBackHtmlSpecialChars($temp));
		}
		$temp = $headersNode->GetChildValueByTagName('to');
		if ($temp)
		{
			$message->SetToAsString(ConvertUtils::WMBackHtmlSpecialChars($temp));
		}
		$temp = $headersNode->GetChildValueByTagName('cc');
		if ($temp)
		{
			$message->SetCcAsString(ConvertUtils::WMBackHtmlSpecialChars($temp));
		}
		$temp = $headersNode->GetChildValueByTagName('bcc');
		if ($temp)
		{
			$message->SetBccAsString(ConvertUtils::WMBackHtmlSpecialChars($temp));
		}
		$message->SetSubject(ConvertUtils::WMBackHtmlSpecialChars($headersNode->GetChildValueByTagName('subject')));
	
		$message->SetDate(new CDateTime(time()));
	
		$bodyNode = &$messageNode->GetChildNodeByTagName('body');
		if ($bodyNode->Attributes['is_html'])
		{
			$message->TextBodies->HtmlTextBodyPart =
				str_replace("\n", CRLF,
				str_replace("\r", '', ConvertUtils::WMBackHtmlNewCode($bodyNode->Value)));
		}
		else
		{
			$message->TextBodies->PlainTextBodyPart =
				str_replace("\n", CRLF,
				str_replace("\r", '', ConvertUtils::WMBackHtmlNewCode($bodyNode->Value)));
		}
	
		$attachmentsNode = &$messageNode->GetChildNodeByTagName('attachments');
	
		if ($attachmentsNode != null)
		{
			$fs = &new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
			$attfolder = &new Folder($_SESSION[ACCOUNT_ID], -1, $_SESSION['attachtempdir']);
				
			foreach(array_keys($attachmentsNode->Children) as $key)
			{
				$attachNode = &$attachmentsNode->Children[$key];
	
				$attachCid = 'attach.php?tn='.$attachNode->GetChildValueByTagName('temp_name');
				$replaceCid = md5(time().$attachNode->GetChildValueByTagName('name'));
	
				$mime_type = $attachNode->GetChildValueByTagName('mime_type');
				if ($mime_type == '')
				{
					$mime_type = ConvertUtils::GetContentTypeFromFileName(
					$attachNode->GetChildValueByTagName('name'));
				}
	
				if (!$message->Attachments->AddFromFile($fs->GetFolderFullPath($attfolder).'/'.$attachNode->GetChildValueByTagName('temp_name'),
				$attachNode->GetChildValueByTagName('name'), $mime_type, (bool) $attachNode->Attributes['inline']))
				{
					$log->WriteLine('Error Get tempfile for Attachment: '.getGlobalError());
				}
	
				if ($bodyNode->Attributes['is_html'])
				{
					if (strpos($message->TextBodies->HtmlTextBodyPart, $attachCid) !== false)
					{
						$attachment = &$message->Attachments->GetLast();
						$attachment->MimePart->Headers->SetHeaderByName(MIMEConst_ContentID, '<'.$replaceCid.'>');
						$message->TextBodies->HtmlTextBodyPart = str_replace($attachCid, 'cid:'.$replaceCid, $message->TextBodies->HtmlTextBodyPart);
							
						$attachname = ConvertUtils::EncodeHeaderString($attachNode->GetChildValueByTagName('name'), $account->GetUserCharset(), $GLOBALS[MailOutputCharset]);
						$attachment->MimePart->Headers->SetHeaderByName(MIMEConst_ContentDisposition, MIMEConst_InlineLower.';'.CRLF."\t".MIMEConst_FilenameLower.'="'.$attachname.'"', false);
					}
				}
			}
		}
		return $message;
	}
	
	/**
	 * @param XmlDocument $xmlObj
	 */
	function printXML(&$xmlObj)
	{
		if (isset($_SESSION[ISINFOERROR], $_SESSION[INFORMATION]) && $_SESSION[ISINFOERROR])
		{
			$xmlNode =& new XmlDomNode('error');
			$xmlNode->Value = $_SESSION[INFORMATION];
			$xmlObj->XmlRoot->AppendChild($xmlNode);
			
			unset($_SESSION[ISINFOERROR], $_SESSION[INFORMATION]);
		}
		exit($xmlObj->ToString());
	}
	
	/**
	 * @param string $errorString
	 * @param XmlDocument $xmlObj
	 * @param int $code
	 */
	function printErrorAndExit($errorString, &$xmlObj, $code = null)
	{
		$errorNote = new XmlDomNode('error', $errorString, true);
		if ($code !== null)
		{
			$errorNote->AppendAttribute('code', (int) $code);
		}
		$xmlObj->XmlRoot->AppendChild($errorNote);
		printXML($xmlObj);
	}
	
	/**
	 * @param string $string
	 * @return string
	 */
	function obLogResponse($string)
	{
		global $log;
		$log->WriteLine(">>>[from_server]>>>\r\n".$string);
		return $string;
	}