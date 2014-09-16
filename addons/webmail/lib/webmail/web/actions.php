<?php

	@session_name('PHPWEBMAILSESSID');
	@session_start();
	
	function fixed_array_map_stripslashes($array)
	{
		if (is_array($array))
		{
			foreach ($array as $key => $value)
			{
				$array[$key] = (is_array($value))
						? @fixed_array_map_stripslashes($value)
						: @stripslashes($value);
			}
		}
		return $array;
	}
	
	function disable_magic_quotes_gpc()
	{
		if (@get_magic_quotes_gpc() == 1)
		{
			$_GET = fixed_array_map_stripslashes($_GET);
			$_POST = fixed_array_map_stripslashes($_POST);
		}
	}
	
	@disable_magic_quotes_gpc();
	
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	require_once(WM_ROOTPATH.'common/inc_constants.php');
	require_once(WM_ROOTPATH.'class_settings.php');
	
	$settings =& Settings::CreateInstance();
	if(!$settings || !$settings->isLoad)
	{
		header('Location: index.php?error=3');
		exit();
	}
	
	if(!$settings->IncludeLang())
	{
		header('Location: index.php?error=6');
		exit();
	}

	require_once(WM_ROOTPATH.'classic/base_defines.php');
	require_once(WM_ROOTPATH.'class_mailprocessor.php');
	require_once(WM_ROOTPATH.'class_contacts.php');
	require_once(WM_ROOTPATH.'class_smtp.php');
	require_once(WM_ROOTPATH.'class_validate.php');

	$ACTION = Get::val('action', 'none');
	$REQ = Get::val('req', 'none');
	$null = null;
	
	$Account = &Account::LoadFromDb(Session::val(ACCOUNT_ID, '-1'));
	$Processor = &new MailProcessor($Account);
	if ($Processor->DbStorage->Connect())
	{
		$Accounts = &$Processor->DbStorage->GetAccountListByUserId($Account->IdUser);
	}
	else 
	{
		SetError(JS_LANG_ErrorConnectionFailed);
	}	

	$sarray = Session::val(SARRAY);

	if (!$Account)
	{
		header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX);
		exit();
	}
	
	switch ($ACTION)
	{
		case 'groupoperation':
			switch ($REQ)
			{
				case 'mark_all_read':
					$messageIdUidSet = null;
					$folder = &new Folder($Account->Id, Get::val('f', -1), '123');
					$Processor->GetFolderInfo($folder);
					
					if (!$Processor->SetFlags($messageIdUidSet, $folder, MESSAGEFLAGS_Seen, ACTION_Set))
					{
						SetError(PROC_CANT_MARK_ALL_MSG_READ);
					}
					header('Location: '.BASEFILE);
					break;
					
				case 'mark_all_unread':
					$messageIdUidSet = null;
					$folder = &new Folder($Account->Id, Get::val('f', -1), '123');
					$Processor->GetFolderInfo($folder);
					
					if (!$Processor->SetFlags($messageIdUidSet, $folder, MESSAGEFLAGS_Seen, ACTION_Remove))
					{
						SetError(PROC_CANT_MARK_ALL_MSG_UNREAD);
					}
					header('Location: '.BASEFILE);
					break;
				
				case 'flag':

					if (!Post::has('d_messages')) SetError(PROC_CANT_SET_MSG_FLAGS);
					
					foreach (Post::val('d_messages', array()) as $folderid => $data)
					{
						$folder = &new Folder($Account->Id, $folderid,
							ConvertUtils::WMBackHtmlSpecialChars($data[0]));
						
						$Processor->GetFolderInfo($folder);
					
						if (!$Processor->SetFlags($data[1], $folder,  MESSAGEFLAGS_Flagged, ACTION_Set))
						{
							SetError(PROC_CANT_SET_MSG_FLAGS);
						}
					}
					exit();
					break;

				case 'unflag':

					if (!Post::has('d_messages')) SetError(PROC_CANT_REMOVE_MSG_FLAGS);
					
					foreach (Post::val('d_messages', array()) as $folderid => $data)
					{
						$folder = &new Folder($Account->Id, $folderid,
							ConvertUtils::WMBackHtmlSpecialChars($data[0]));
						
						$Processor->GetFolderInfo($folder);
					
						if (!$Processor->SetFlags($data[1], $folder,  MESSAGEFLAGS_Flagged, ACTION_Remove))
						{
							SetError(PROC_CANT_REMOVE_MSG_FLAGS);
						}
					}
					exit();
					break;
					
				case 'read':

					if (!Post::has('d_messages')) SetError(PROC_CANT_MARK_MSGS_READ);
					
					foreach (Post::val('d_messages', array()) as $folderid => $data)
					{
						$folder = &new Folder($Account->Id, $folderid,
							ConvertUtils::WMBackHtmlSpecialChars($data[0]));
						
						$Processor->GetFolderInfo($folder);
					
						if (!$Processor->SetFlags($data[1], $folder,  MESSAGEFLAGS_Seen, ACTION_Set))
						{
							SetError(PROC_CANT_MARK_MSGS_READ);
						}
					}
					exit();
					break;

				case 'unread':
					
					if (!Post::has('d_messages')) SetError(PROC_CANT_MARK_MSGS_UNREAD);
					
					foreach (Post::val('d_messages', array()) as $folderid => $data)
					{
						$folder = &new Folder($Account->Id, $folderid,
							ConvertUtils::WMBackHtmlSpecialChars($data[0]));
							
						$Processor->GetFolderInfo($folder);
					
						if (!$Processor->SetFlags($data[1], $folder,  MESSAGEFLAGS_Seen, ACTION_Remove))
						{
							SetError(PROC_CANT_MARK_MSGS_UNREAD);
						}
					}
					exit();
					break;
					

				case 'move_messages':

					if (!Post::has('d_messages')) SetError(PROC_CANT_CHANGE_MSG_FLD);
					
					$toFolder = &new Folder($Account->Id, Post::val('tofolder', -1), '123');
					if ($toFolder->IdDb == -1) SetError(PROC_CANT_CHANGE_MSG_FLD.' ('.$toFolder->IdDb.')');
					
					$Processor->GetFolderInfo($toFolder);
					
					$foreachArray = Post::val('d_messages', array());
					
					foreach ($foreachArray as $folderid => $data)
					{
						$fromFolder = &new Folder($Account->Id, $folderid, $data[0]);
						$Processor->GetFolderInfo($fromFolder);
						if (!$Processor->MoveMessages($data[1], $fromFolder, $toFolder))
						{
							SetError(PROC_CANT_CHANGE_MSG_FLD);
						}
					}
					
					header('Location: '.BASEFILE);
					break;
					
				case 'delete_messages':

					if (!Post::has('d_messages')) SetError(PROC_CANT_DEL_MSGS);
					
					$foreachArray = Post::val('d_messages', array());
					
					foreach ($foreachArray as $folderid => $data)
					{
						$folder = &new Folder($Account->Id, $folderid,
							ConvertUtils::WMBackHtmlSpecialChars($data[0]));
						
						$Processor->GetFolderInfo($folder);
					
						if (!$Processor->DeleteMessages($data[1], $folder))
						{
							SetError(PROC_CANT_DEL_MSGS);
						}
					}
					
					header('Location: '.BASEFILE);
					break;

			case 'undelete_messages':

					if (!Post::has('d_messages')) SetError(PROC_CANT_UNDEL_MSGS);
					
					$foreachArray = Post::val('d_messages', array());
					
					foreach ($foreachArray as $folderid => $data)
					{
						$folder = &new Folder($Account->Id, $folderid,
							ConvertUtils::WMBackHtmlSpecialChars($data[0]));
							
						$Processor->GetFolderInfo($folder);
						
						if (!$Processor->SetFlags($data[1], $folder, MESSAGEFLAGS_Deleted, ACTION_Remove))
						{
							SetError(PROC_CANT_UNDEL_MSGS);
						}
					}
					
					header('Location: '.BASEFILE);
					break;
					
				default:
				case 'none': header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX); break;
			}
			break;
			
		case 'set':
			switch ($REQ)
			{
				case 'sender':
					
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($Account);
					if ($dbStorage->Connect())
					{
						$value = (int) Post::val('safety', 0);
						$emailString = trim(Post::val('sender', ''));
						if ($emailString)
						{
							$emailObj = &new EmailAddress();
							$emailObj->Parse($emailString);
							if ($emailObj->Email)
							{
								$dbStorage->SetSenders($emailObj->Email, $value, $Account->IdUser);
							}
						}
					}					
					break;
			}
			
			break;
		
		case 'new':
			switch ($REQ)
			{
				
				case 'folder':
										
					if ($sarray && in_array($sarray[EDIT_ACCOUNT_ID], $Accounts))
					{
						$editAccount = &Account::LoadFromDb($sarray[EDIT_ACCOUNT_ID]);
						
						if ($editAccount->IsDemo)
						{
							header('Location: '.BASEFILE);
							exit();
						}
						
						$editProcessor = &new MailProcessor($editAccount);
						
						If (!ConvertUtils::CheckDefaultWordsFileName(Post::val('newFolderName', 'errorName')) || 
							!ConvertUtils::CheckFileName(Post::val('newFolderName', 'errorName')))
						{
							SetError(PROC_CANT_CREATE_FLD);
						}
						
						$folderName = ConvertUtils::ConvertEncoding(
										ConvertUtils::ClearFileName(Post::val('newFolderName', 'errorName')),
									$editAccount->GetUserCharset(), CPAGE_UTF7_Imap);
						
											
						$parentFolder = &new Folder($editAccount->Id, (int) Post::val('parentId', -1), '-');
						$parentPath = '';
						if (Post::val('parentId', -1) > -1 && $parentFolder)
						{
							$editProcessor->GetFolderInfo($parentFolder);
							$parentPath .= $parentFolder->FullName.$editAccount->Delimiter;
							$parentId = $parentFolder->IdDb;
						}
						else 
						{
							$parentFolder = null;
							$parentId = -1;
						}
						
						$create = true;
						if (Post::val('create_folder', 'in_webmail') == 'in_webmail')
						{
							$create = false;
						}
						
						if ($editAccount->MailProtocol == MAILPROTOCOL_IMAP4)
						{
							$folderSync = ($editAccount->AllowDirectMode && $settings->DirectModeIsDefault)
								? FOLDERSYNC_DirectMode : FOLDERSYNC_DontSync;
								
							$folder = &new Folder($editAccount->Id, -1, $parentPath.$folderName, $folderName,
												($create) ? $folderSync : FOLDERSYNC_DontSync);	
						}
						else 
						{
							$folder = &new Folder($editAccount->Id, -1, $parentPath.$folderName, $folderName);
						}
						
						$folder->IdParent = $parentId;
						$folder->Type = FOLDERTYPE_Custom;
						$folder->Hide = false;		
						
						$folders = &$editProcessor->GetFolders();						
						$folderList = &$folders->CreateFolderListFromTree();
						
						$hasError = false;
						foreach (array_keys($folderList->Instance()) as $key)
						{
							$listFolder = &$folderList->Get($key);
							
							if ($listFolder->FullName == $folder->FullName)
							{
								$hasError = true;
								break;
							}
						}
						
						if ($hasError)
						{
							SetError(PROC_FOLDER_EXIST);
						}
						elseif ($Account->IsDemo)
						{
							
						}
						elseif (!$editProcessor->CreateFolder($folder, $create))
						{
							SetError(PROC_CANT_CREATE_FLD);
						}			
						header('Location: '.BASEFILE);
					} else header('Location: '.BASEFILE);
					break;
					
					
				case 'account':

					if ($Account->IsDemo)
					{
						header('Location: '.BASEFILE.'?'.SCREEN.'='.SET_ACCOUNT_PROF);						
						exit();
					}

					if ($Account->AllowChangeSettings && $settings->AllowUsersAddNewAccounts) {}
						else SetError(PROC_ERROR_ACCT_CREATE);	
							
					if ($sarray)
					{
						$newAccount = &new Account();
						
						$user = &new User();
						$user->Id = $Account->IdUser;
						$newAccount->IdUser = $Account->IdUser;
						
						$newAccount->DefaultAccount = (bool) Post::val('login_from_account', false);

						$newAccount->FriendlyName = Post::val('fm_friendly_name', '');

						$isGood = true;
						if (Post::val('fm_email', '') == '' || Post::val('fm_inc_server', '') == '' ||
							Post::val('fm_inc_server_port', '') == '' || Post::val('fm_inc_login', '') == '')
						{
							SetError(PROC_CANT_LEAVE_BLANK);
						}
						
						$newAccount->Email = Post::val('fm_email', $newAccount->Email);
						$newAccount->MailIncHost = Post::val('fm_inc_server', $newAccount->MailIncHost);
						$newAccount->MailIncPort = (int) Post::val('fm_inc_server_port', $newAccount->MailIncPort);
						$newAccount->MailIncLogin = Post::val('fm_inc_login', $newAccount->MailIncLogin);
						$newAccount->MailIncPassword = Post::val('fm_inc_password', '');

						$newAccount->MailOutHost = (strlen(Post::val('fm_smtp_server', '')) > 0) 
							? Post::val('fm_smtp_server', $newAccount->MailOutHost)
							: $newAccount->MailIncHost;
						$newAccount->MailOutPort = Post::val('fm_smtp_server_port', $newAccount->MailOutPort);
						$newAccount->MailOutLogin = Post::val('fm_smtp_login', $newAccount->MailOutLogin);
						$newAccount->MailOutPassword = Post::val('fm_smtp_password', '');
						
						$newAccount->MailOutAuthentication = (bool) Post::val('fm_smtp_authorisation', false);
						$newAccount->UseFriendlyName = (bool) Post::val('fm_use_friendly_name', false);
						$newAccount->GetMailAtLogin = (bool) Post::val('fm_getmail_at_login', false);

						If (!ConvertUtils::CheckFileName($newAccount->Email))
						{
							SetError(CantCreateAccount);
						}
						
						if (Post::val('fm_protocol', 'imap') == 'pop' || Post::val('fm_protocol', 'imap') == 'wmserver')
						{
							switch (Post::val('fm_protocol', 'pop'))
							{
								default:
								case 'pop' : $newAccount->MailProtocol = MAILPROTOCOL_POP3; break;
								case 'wmserver' : $newAccount->MailProtocol = MAILPROTOCOL_WMSERVER; break;
							}
							
							$newAccount->MailsOnServerDays = (int) Post::val('fm_keep_messages_days', 7);
							
							$synchronize = Post::val('synchronizeSelect', FOLDERSYNC_AllEntireMessages);
							
							if ((bool) Post::val('fm_int_deleted_as_server', false))
							{
								if ($synchronize == FOLDERSYNC_NewHeadersOnly || $synchronize == FOLDERSYNC_NewEntireMessages)
								{
									$synchronize++;
								}
							}
							
							$mailmode = MAILMODE_LeaveMessagesOnServer;	
							
							if ((int) Post::val('fm_mail_management_mode', false) == 1)
							{
								$mailmode = MAILMODE_DeleteMessagesFromServer;
							}
							else 
							{
								$p = 0;
								if (Post::val('fm_keep_for_x_days', false))
								{
									$mailmode = MAILMODE_KeepMessagesOnServer;
									$p++;
								}
								if (Post::val('fm_delete_messages_from_trash', false))
								{
									$mailmode = MAILMODE_DeleteMessageWhenItsRemovedFromTrash;
									$p++;
								}
								if ($p == 2)
								{
									$mailmode = MAILMODE_KeepMessagesOnServerAndDeleteMessageWhenItsRemovedFromTrash;
								}
							}
							
							$newAccount->MailMode = $mailmode;		
							
							$validatedError = $newAccount->ValidateData();
							if($validatedError !== true)
							{
								SetError($validatedError);
							}
							
							if (!$user->CreateAccount($newAccount, $synchronize)) 
							{
								SetError(getGlobalError());
							}
							else 
							{
								SetReport(ReportAccountCreatedSuccessfuly);
							}
						}
						else 
						{
							$folderSync = FOLDERSYNC_AllHeadersOnly;
							$folderSync = ($settings->AllowDirectMode && $settings->DirectModeIsDefault) ?
									FOLDERSYNC_DirectMode : $folderSync;
							
							$newAccount->MailProtocol = MAILPROTOCOL_IMAP4;
							
							$validatedError = $newAccount->ValidateData();
							if($validatedError !== true)
							{
								SetError($validatedError);
							}
							
							if (!$user->CreateAccount($newAccount, $folderSync)) 
							{
								SetError(getGlobalError());
							}
							else 
							{
								SetReport(ReportAccountCreatedSuccessfuly);
							}
						}
						
						$_SESSION[SARRAY][EDIT_ACCOUNT_ID] = $newAccount->Id;
						header('Location: '.BASEFILE.'?'.SCREEN.'='.SET_ACCOUNT_PROF);	
					}		
					break;
				case 'group':
					
					if (!$settings->AllowContacts) SetError(PROC_CANT_INS_NEW_GROUP);

					$group = &new AddressGroup();
					$group->IdUser = $Account->IdUser;
					$group->Name = Post::val('groupname', '');
					
					$group->IsOrganization = false;
					if (Post::val('isorganization', 0))
					{
						$group->IsOrganization = (bool) Post::val('isorganization', 0);
					}
					
					if ($group->IsOrganization)
					{
						$group->Email = Post::val('gemail', '');
						$group->Company = Post::val('gcompany', '');
						$group->Street = Post::val('gstreet', '');
						$group->City = Post::val('gcity', '');
						$group->State = Post::val('gstate', '');
						$group->Zip = Post::val('gzip', '');
						$group->Country = Post::val('gcountry', '');
						$group->Phone = Post::val('gphone', '');
						$group->Fax = Post::val('gfax', '');
						$group->Web = Post::val('gweb', '');
					}					
					
					if (!$group->Name)
					{
						SetError(JS_LANG_WarningGroupNotComplete,
							BASEFILE.'?'.CONTACT_MODE.'='.G_NEW);
					}
					
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($Account);
					if ($dbStorage->Connect())
					{
						$result = false;

						if ($dbStorage->InsertAddressGroup($group))
						{
							$result = true;
							
							$contacts = explode(',', trim(trim(Post::val('contactsEmails', '')), ','));
							
							if ($contacts && count($contacts) > 0)
							{
								foreach ($contacts as $values)
								{
									$values = trim($values);
									if (!$values) continue;
									$addressBookRecord = &new AddressBookRecord();
									$addressBookRecord->IdUser = $Account->IdUser;
									$addressBookRecord->HomeEmail = $values;
									$addressBookRecord->PrimaryEmail = PRIMARYEMAIL_Home;
	
									$result &= $dbStorage->InsertAddressBookRecord($addressBookRecord) &&
											$dbStorage->InsertAddressGroupContact($addressBookRecord->IdAddress, $group->Id);
								}
							}
						}
						
						if (isset($_SESSION[SARRAY][SEARCH_ARRAY])) unset($_SESSION[SARRAY][SEARCH_ARRAY]);
						

						if (!$result)
						{
							SetError(PROC_CANT_INS_NEW_CONTS."<br />\r\n".getGlobalError());
						}
						else 
						{
							SetReport(ReportGroupSuccessfulyAdded1.' "'.Post::val('groupname', '').'" '.ReportGroupSuccessfulyAdded2);
						}
						
					}
					else
					{
						SetError(getGlobalError());
					}
					header('Location: '.BASEFILE);
					break;

				default:
				case 'none': header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX); break;
			}
			break;
			
		case 'move':
			switch ($REQ)
			{
				case 'contacts':
					
					if (!$settings->AllowContacts) SetError(PROC_CANT_UPDATE_CONT);
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
					if ($dbStorage->Connect())
					{
						$result = true;
						$groupId = Post::val('groupId', -1);
						$groupName = Post::val('groupName', '');
						foreach (array_keys(Post::val('contacts', array())) as $key)
						{
							$result &= $dbStorage->DeleteAddressGroupsContacts($key, $groupId) &&
										$dbStorage->InsertAddressGroupContact($key, $groupId);
						}

						if (!$result)
						{
							SetError(PROC_CANT_ADD_NEW_CONT_TO_GRP);
						}
						else 
						{
							SetReport(ReportContactAddedToGroup.' "'.$groupName.'"');
						}
						
					}
					else
					{
						SetError(getGlobalError());
					}
					header('Location: '.BASEFILE);
					break;
					
				default:
				case 'none': header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_CONTACTS); break;
			}
			break;
			
		case 'rename':
			switch ($REQ)
			{			
				case 'folder':
					if ($sarray && in_array($sarray[EDIT_ACCOUNT_ID], $Accounts))
					{
						$editAccount = &Account::LoadFromDb($sarray[EDIT_ACCOUNT_ID]);

						if ($editAccount->IsDemo)
						{
							header('Location: '.BASEFILE);
							exit();
						}
						
						$editProcessor = &new MailProcessor($editAccount);	
						
						If (!ConvertUtils::CheckDefaultWordsFileName(Post::val('fname', 'errorName')) || 
							!ConvertUtils::CheckFileName(Post::val('fname', 'erorrName')))
						{
							SetError(PROC_CANT_UPD_FLD);
						}
						
						$folderName = ConvertUtils::ConvertEncoding(
									ConvertUtils::ClearFileName(Post::val('fname', 'errorName')),
									$editAccount->GetUserCharset(), CPAGE_UTF7_Imap);
						
						$folders = &$editProcessor->GetFolders();
						$folderList = &$folders->CreateFolderListFromTree();
						
						$folder = &new Folder($editAccount->Id, Post::val('folderid', -1), '-');
						$editProcessor->GetFolderInfo($folder);
											
						$hasError = false;
						foreach (array_keys($folderList->Instance()) as $key)
						{
							$listFolder = &$folderList->Get($key);
							if ($listFolder->Name == $folderName)
							{
								$hasError = true;
								break;
							}
						}
						
						if ($hasError)
						{
							SetError(PROC_FOLDER_EXIST);
						}
						
						$dbstor = &DbStorageCreator::CreateDatabaseStorage($editAccount);
						if ($dbstor->Connect() && $editAccount)
						{
							$folder->Name = $folderName;
							if (!$editProcessor->RenameFolder($folder, $folderName, $editAccount->Delimiter) ||
										!$dbstor->UpdateFolder($folder))
							{
								SetError(PROC_CANT_UPD_FLD);			
							}
						}
						else
						{ 
							SetError(PROC_CANT_UPD_FLD);		
						}
							
						header('Location: '.BASEFILE);
					} header('Location: '.BASEFILE);
					break;
					
				default:
				case 'none': header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX); break;
			}
			break;
			
		case 'update':
			switch ($REQ)
			{
				
				case 'account':
					if (!$Account->AllowChangeSettings) SetError(PROC_CANT_UPDATE_ACCT);
					if ($sarray && in_array($sarray[EDIT_ACCOUNT_ID], $Accounts))
					{
						$editAccount = &Account::LoadFromDb($sarray[EDIT_ACCOUNT_ID]);
						$oldEmail = $editAccount->Email;
						
						$editProcessor = &new MailProcessor($editAccount);

						$editAccount->DefaultAccount = (bool) Post::val('login_from_account', false);
						$editAccount->FriendlyName = Post::val('fm_friendly_name', '');

						$editAccount->Email = Post::val('fm_email', $editAccount->Email);
						$editAccount->MailIncHost = Post::val('fm_inc_server', $editAccount->MailIncHost);
						$editAccount->MailIncPort = (int) Post::val('fm_inc_server_port', $editAccount->MailIncPort);
						$editAccount->MailIncLogin = Post::val('fm_inc_login', $editAccount->MailIncLogin);
						if (Post::val('fm_inc_password', DUMMYPASSWORD) != DUMMYPASSWORD)
						{
							$editAccount->MailIncPassword = Post::val('fm_inc_password', '');
						}

						$editAccount->MailOutHost = Post::val('fm_smtp_server', $editAccount->MailOutHost);
						$editAccount->MailOutPort = Post::val('fm_smtp_server_port', $editAccount->MailOutPort);
						$editAccount->MailOutLogin = Post::val('fm_smtp_login', $editAccount->MailOutLogin);
						
						if (Post::val('fm_smtp_password', DUMMYPASSWORD) != DUMMYPASSWORD)
						{
							$editAccount->MailOutPassword = Post::val('fm_smtp_password', '');
						}
						
						$editAccount->MailOutAuthentication = (bool) Post::val('fm_smtp_authorisation', false);
						$editAccount->UseFriendlyName = (bool) Post::val('fm_use_friendly_name', false);
						$editAccount->GetMailAtLogin = (bool) Post::val('fm_getmail_at_login', false);
						
						if ($editAccount->MailProtocol == MAILPROTOCOL_POP3 || $editAccount->MailProtocol == MAILPROTOCOL_WMSERVER)
						{
							$editAccount->MailsOnServerDays = (int) Post::val('fm_keep_messages_days', $editAccount->MailsOnServerDays);
							
							$synchronize = Post::val('synchronizeSelect', FOLDERSYNC_NewHeadersOnly);
							
							if ((bool) Post::val('fm_int_deleted_as_server', false))
							{
								if ($synchronize == FOLDERSYNC_NewHeadersOnly || $synchronize == FOLDERSYNC_NewEntireMessages)
								{
									$synchronize++;
								}
							}
							
							$mailmode = MAILMODE_LeaveMessagesOnServer;	
							
							if ((int) Post::val('fm_mail_management_mode', false) == 1)
							{
								$mailmode = MAILMODE_DeleteMessagesFromServer;
							}
							else 
							{
								$p = 0;
								if (Post::val('fm_keep_for_x_days', false))
								{
									$mailmode = MAILMODE_KeepMessagesOnServer;
									$p++;
								}
								if (Post::val('fm_delete_messages_from_trash', false))
								{
									$mailmode = MAILMODE_DeleteMessageWhenItsRemovedFromTrash;
									$p++;
								}
								if ($p == 2)
								{
									$mailmode = MAILMODE_KeepMessagesOnServerAndDeleteMessageWhenItsRemovedFromTrash;
								}
							}
							
							$editAccount->MailMode = $mailmode;							
							
							//Validation rules
							$validate = $editAccount->ValidateData();
							if ($validate !== true)
							{
								SetError($validate);
							}
							else 
							{
								if ($editAccount->Email != $oldEmail)
								{
									$fs = &new FileSystem(INI_DIR.'/mail', $oldEmail, $editAccount->Id);
									$fs->MoveFolders($editAccount->Email);
								}
							
								if (!$editAccount->Update($synchronize)) 
								{
									if (isset($GLOBALS[ErrorDesc]))
									{
										SetError(getGlobalError());
									}
									else
									{
										SetError(PROC_CANT_UPDATE_ACCT);
									}
								}
								else 
								{
									SetReport(ReportAccountUpdatedSuccessfuly);
								}
							}
						}
						else 
						{
							//Validation rules
							$validate = $editAccount->ValidateData();
							if ($validate !== true)
							{
								SetError($validate);
							}
							else 
							{
								if ($editAccount->Email != $oldEmail)
								{
									$fs = &new FileSystem(INI_DIR.'/mail', $oldEmail, $editAccount->Id);
									$fs->MoveFolders($editAccount->Email);
								}
							
								if (!$editAccount->Update(null)) 
								{
									if (isset($GLOBALS[ErrorDesc]))
									{
										SetError(getGlobalError());
									}
									else
									{
										SetError(PROC_CANT_UPDATE_ACCT);
									}
								}
								else 
								{
									SetReport(ReportAccountUpdatedSuccessfuly);
								}
							}
						}
					}
					else
					{
						SetError(PROC_CANT_UPDATE_ACCT);
					}
					header('Location: '.BASEFILE);
					break;
					
				case 'folderhide':
					if ($sarray && in_array($sarray[EDIT_ACCOUNT_ID], $Accounts))
					{
						$editAccount = &Account::LoadFromDb($sarray[EDIT_ACCOUNT_ID]);
						if ($editAccount->IsDemo)
						{
							header('Location: '.BASEFILE);
							exit();
						}
						
						$editProcessor = &new MailProcessor($editAccount);
						$folder = &new Folder($editAccount->Id, Get::val('folderid', -1), '-');
						$editProcessor->GetFolderInfo($folder);
						$dbstor = &DbStorageCreator::CreateDatabaseStorage($editAccount);
						if ($dbstor->Connect() && $editAccount)
						{
							$folder->Hide = !$folder->Hide;
							if (!$dbstor->UpdateFolder($folder))
							{
								SetError(PROC_CANT_UPD_FLD);		
							}
							else
							{
								$editProcessor->SetHide($folder, $folder->Hide);
							}
						}
					}
					header('Location: '.BASEFILE);	
					break;
					
				case 'foldersync':
					if ($sarray && in_array($sarray[EDIT_ACCOUNT_ID], $Accounts))
					{
						$editAccount = &Account::LoadFromDb($sarray[EDIT_ACCOUNT_ID]);
						
						if ($editAccount->IsDemo)
						{
							header('Location: '.BASEFILE);
							exit();
						}
						
						$editProcessor = &new MailProcessor($editAccount);
						$folder = &new Folder($editAccount->Id, Post::val('folderid', -1), '-');
						$editProcessor->GetFolderInfo($folder);

						if (!$editProcessor->MailStorage->Connect())
						{
							SetError(PROC_CANT_UPD_FLD);
							header('Location: '.BASEFILE);					
							break;
						}
						
						$tempFolders = $editProcessor->MailStorage->GetFolders();
						$serverFolders = $tempFolders->CreateFolderListFromTree();
						$serverFoldersArray = $serverFolders->Instance();
						$serverFoldersName = array();
						foreach ($serverFoldersArray AS $sFolder)
						{
							$serverFoldersName[] = strtolower($sFolder->Name);
						}
						unset($tempFolders, $serverFolders, $serverFoldersArray, $sFolder);	
						
						$result = true;
						if ($editAccount->MailProtocol == MAILPROTOCOL_IMAP4 &&
								!in_array(strtolower($folder->Name), $serverFoldersName) &&
								(int) Post::val('synctype', $folder->SyncType) != FOLDERSYNC_DontSync)
						{
							$result = $editProcessor->MailStorage->CreateFolder($folder);
						}						
						
						if ($result)
						{
							$dbstor = &DbStorageCreator::CreateDatabaseStorage($editAccount);
							if ($dbstor->Connect() && $editAccount)
							{
								$folder->SyncType = (int) Post::val('synctype', $folder->SyncType);
								if (!$dbstor->UpdateFolder($folder))
								{
									SetError(PROC_CANT_UPD_FLD);		
								}
							}
						}
						else 
						{
							SetError(PROC_CANT_UPD_FLD);		
						}
					}
					header('Location: '.BASEFILE);					
					break;
					
				case 'folderorder':
					if ($sarray && in_array($sarray[EDIT_ACCOUNT_ID], $Accounts))
					{
						$editAccount = &Account::LoadFromDb($sarray[EDIT_ACCOUNT_ID]);
						
						if ($editAccount->IsDemo)
						{
							header('Location: '.BASEFILE);
							exit();
						}
						
						$editProcessor = &new MailProcessor($editAccount);
						$currenfolder = &new Folder($editAccount->Id, Get::val('cf_id', -1), '-');
						$replacefolder = &new Folder($editAccount->Id, Get::val('rf_id', -1), '-');
						$editProcessor->GetFolderInfo($currenfolder);
						$editProcessor->GetFolderInfo($replacefolder);
						$dbstor = &DbStorageCreator::CreateDatabaseStorage($editAccount);
						if ($dbstor->Connect() && $editAccount)
						{
							$ord = $currenfolder->FolderOrder;
							$currenfolder->FolderOrder = $replacefolder->FolderOrder;
							$replacefolder->FolderOrder = $ord;
							
							if(!$dbstor->UpdateFolder($currenfolder) || !$dbstor->UpdateFolder($replacefolder))
							{
								SetError(PROC_CANT_UPD_FLD);	
							}
						}
					}	
					header('Location: '.BASEFILE);							
					break;
					
				case 'x-spam':
					if ($sarray && in_array($sarray[EDIT_ACCOUNT_ID], $Accounts))
					{
						$editAccount = &Account::LoadFromDb($sarray[EDIT_ACCOUNT_ID]);
						if ($editAccount->IsDemo)
						{
							header('Location: '.BASEFILE);
							exit();
						}
						
						$editAccount->XSpam = (bool) Post::val('x-spam', false);
						if (!$editAccount->Update(null))
						{
							SetError(PROC_CANT_UPDATE_ACCT);	
						}
					}
					header('Location: '.BASEFILE);		
					break;
					
				case 'filter':
					if ($sarray && in_array($sarray[EDIT_ACCOUNT_ID], $Accounts))
					{
						$editAccount = &Account::LoadFromDb($sarray[EDIT_ACCOUNT_ID]);

						if ($editAccount->IsDemo)
						{
							header('Location: '.BASEFILE);
							exit();
						}
						
						$dbstor = &DbStorageCreator::CreateDatabaseStorage($editAccount);
						if (!$dbstor->Connect()) 
						{
							header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX);
							exit();
						}
						$filter = &new Filter();
						$filter->Id = Post::val('filterId', -1);
						$filter->IdAcct = $editAccount->Id;
						$filter->Field = Post::val('id_rule_value', FILTERFIELD_From);
						$filter->Condition = Post::val('fcontain', FILTERCONDITION_ContainSubstring);
						$filter->Filter = Post::val('filter_text', '');
						$filter->Action = Post::val('faction', FILTERACTION_DeleteFromServerImmediately);
						$filter->IdFolder = Post::val('ffolder');
							
						if (Post::val('submitType', 'Save') == JS_LANG_Save)
						{
							if (!$dbstor->UpdateFilter($filter))
							{
								SetError(PROC_CANT_UPD_FILTER);	
							}
							else 
							{
								SetReport(ReportFiltersUpdatedSuccessfuly);
							}
						}
						else 
						{
							if (!$dbstor->InsertFilter($filter))
							{
								SetError(PROC_CANT_INS_NEW_FILTER);
							}
						}
					}
					header('Location: '.BASEFILE);	
					break;

				case 'group':
					
					if (!$settings->AllowContacts) SetError(PROC_CANT_INS_NEW_CONT);

					require_once(WM_ROOTPATH.'class_contacts.php');
					
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($Account);
					if ($dbStorage->Connect())
					{
						$group = &new AddressGroup();
						$group->Id = Post::val('gid', -1);
						$group->IdUser = $Account->IdUser;
						$group->Name = Post::val('gname');

						$group->IsOrganization = (bool) Post::val('isorganization', 0);
						$group->Email = Post::val('gemail', '');
						$group->Company = Post::val('gcompany', '');
						$group->Street = Post::val('gstreet', '');
						$group->City = Post::val('gcity', '');
						$group->State = Post::val('gstate', '');
						$group->Zip = Post::val('gzip', '');
						$group->Country = Post::val('gcountry', '');
						$group->Phone = Post::val('gphone', '');
						$group->Fax = Post::val('gfax', '');
						$group->Web = Post::val('gweb', '');
						
						$result = false;

						if ($dbStorage->UpdateAddressGroup($group))
						{
							$result = $dbStorage->DeleteAddressGroupsContactsByIdGroup($group->Id);

							foreach (Post::val('contactsIds', array()) as $key => $value)
							{
								$result &= $dbStorage->InsertAddressGroupContact(
											$value, $group->Id);
							}

							$contacts = explode(',', trim(trim(Post::val('emails', '')), ','));
							
							if ($contacts && count($contacts) > 0)
							{
								foreach ($contacts as $values)
								{
									$values = trim($values);
									if (!$values) continue;
									$addressBookRecord = &new AddressBookRecord();
									$addressBookRecord->IdUser = $Account->IdUser;
									$addressBookRecord->HomeEmail = $values;
									$addressBookRecord->PrimaryEmail = PRIMARYEMAIL_Home;
	
									$result &= $dbStorage->InsertAddressBookRecord($addressBookRecord) &&
											$dbStorage->InsertAddressGroupContact($addressBookRecord->IdAddress, $group->Id);
								}
							}
						}

						if (!$result)
						{
							SetError(PROC_CANT_ADD_NEW_CONT_TO_GRP);
						}
						else 
						{
							SetReport(ReportGroupUpdatedSuccessfuly);
						}
						
					}
					else
					{
						SetError(getGlobalError());
					}

					header('Location: '.BASEFILE);
					break;
					
				case 'contact':
					
					if (!$settings->AllowContacts) SetError(PROC_CANT_UPDATE_CONT);
						
					$addressBookRecord = &GetContactFromPost();
					$addressBookRecord->IdUser = $Account->IdUser;

					if ($addressBookRecord->FullName == '')
					{
						$email = '';
						switch ($addressBookRecord->PrimaryEmail)
						{
							case 0: $email = $addressBookRecord->HomeEmail; break;
							case 1: $email = $addressBookRecord->BusinessEmail; break;
							case 2: $email = $addressBookRecord->OtherEmail; break;	
							default: $email = ''; break;	
						}
						
						if (!$email)
						{
							header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_CONTACTS.'&'.CONTACT_MODE.'='.C_NEW); exit();
						}
					}
					
					$result = true;
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
					if ($dbStorage->Connect())
					{
						if ((bool) Post::val('isNewContact', 0))
						{
							if ($dbStorage->InsertAddressBookRecord($addressBookRecord))
							{
								if (Post::has('groupsIds') && count(Post::val('groupsIds', array())) > 0)
								{
									foreach (Post::val('groupsIds', array()) as $key => $value)
									{
										$dbStorage->InsertAddressGroupContact($addressBookRecord->IdAddress, $value);
									}
								}
								
								if (isset($_SESSION[SARRAY][SEARCH_ARRAY])) unset($_SESSION[SARRAY][SEARCH_ARRAY]);	
								
								SetReport(ReportContactSuccessfulyAdded);
							}
							else
							{
								SetError(PROC_CANT_INS_NEW_CONT);
							}
						}
						else 
						{
							if ($dbStorage->UpdateAddressBookRecord($addressBookRecord))
							{
								if ($addressBookRecord->IdAddress)
								{
									$dbStorage->DeleteAddressGroupsContactsByIdAddress($addressBookRecord->IdAddress);
									if (Post::has('groupsIds') && count(Post::val('groupsIds', array())) > 0)
									{
										foreach (Post::val('groupsIds', array()) as $key => $value)
										{
											$dbStorage->InsertAddressGroupContact($addressBookRecord->IdAddress, $value);
										}
									}		
									SetReport(ReportContactUpdatedSuccessfuly);
								}
								else 
								{
									SetError(PROC_CANT_UPDATE_CONT);
								}
							}
							else
							{
								SetError(PROC_CANT_UPDATE_CONT."<br />\r\n".getGlobalError());
							}
						}
					}
					else
					{
						SetError(getGlobalError());
					}						
						
					header('Location: '.BASEFILE); break;
					break;
					
				case 'signature':
					
					if ($sarray && in_array($sarray[EDIT_ACCOUNT_ID], $Accounts))
					{
						$editAccount = &Account::LoadFromDb($sarray[EDIT_ACCOUNT_ID]);
						if ($editAccount->IsDemo)
						{
							header('Location: '.BASEFILE);
							exit();
						}
						
						$signatureOptions = 0;
						$signatureOptions += (int) Post::val('add_signatures', 0);
						$signatureOptions += (int) Post::val('replies_forwards', 0);	

						$editAccount->SignatureType = (int) Post::val('isHtml', 0);
						$editAccount->SignatureOptions = $signatureOptions;
						$editAccount->Signature = ($editAccount->SignatureType == 0)
							? ConvertUtils::WMHtmlSpecialChars(strip_tags(Post::val('signature', ''))) : Post::val('signature', '');
						
						if (!$editAccount->Update(null))
						{
							SetError(PROC_CANT_UPDATE_ACCT);
						}
						else 
						{
							SetReport(ReportSignatureUpdatedSuccessfuly);
						}
					}

					header('Location: '.BASEFILE);					
					break;
					
				default:	
				case 'none': header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX); break;
			}
			break;
			
		case 'delete':
			switch ($REQ)
			{
				case 'message':

					$folder = &new Folder($Account->Id, Post::val('folderId', -1), '');
					$Processor->GetFolderInfo($folder);
					$messageIdUidSet = array(Post::val('messageId', -1) => Post::val('messageUid', ''));
					if (!$Processor->DeleteMessages($messageIdUidSet, $folder))
					{
						SetError(PROC_CANT_DEL_MSGS, BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX);
					}
					header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX);
					break;
				
				case 'folders':
					if ($sarray && in_array($sarray[EDIT_ACCOUNT_ID], $Accounts))
					{
						$editAccount = &Account::LoadFromDb($sarray[EDIT_ACCOUNT_ID]);
						if ($editAccount->IsDemo)
						{
							header('Location: '.BASEFILE);
							exit();
						}
						
						$editProcessor = &new MailProcessor($editAccount);
						$result = true;
						if (isset($_POST['folders']) && is_array($_POST['folders']))
						{
							foreach ($_POST['folders'] as $key => $value)
							{
								$folder = &new Folder($editAccount->Id, $key, $value);
								$editProcessor->GetFolderInfo($folder);
								$editProcessor->GetFolderMessageCount($folder);
								
								$childCount = $editProcessor->DbStorage->GetFolderChildCount($folder);
		
								if ($Account->MailProtocol == MAILPROTOCOL_IMAP4 &&	($folder->MessageCount > 0 || $childCount != 0))
								{
									$result = false;
								}
								else
								{
									$result &= $editProcessor->DeleteFolder($folder);
								}
							}
						}
						
						if (!$result)
						{
							SetError(PROC_ERROR_DEL_FLD);
						}
					}

					header('Location: '.BASEFILE);
					break;
					
				case 'purge':
					$folder = &new Folder($Account->Id, Get::val('f', -1), '123');
					$Processor->GetFolderInfo($folder);
					if (!$Processor->PurgeFolder($folder))
					{
						SetError(PROC_CANT_PURGE_MSGS);
					}
					header('Location: '.BASEFILE);
					break;
					
				case 'trash':
					$folder = &new Folder($Account->Id, Get::val('f', -1), '123');
					$Processor->GetFolderInfo($folder);
					if ($folder->Type == FOLDERTYPE_Trash)
					{
						if (!$Processor->EmptyFolder($folder))
						{
							SetError(PROC_CANT_DEL_MSGS);
						}
					}
					header('Location: '.BASEFILE);
					break;
				
				case 'account':
					if (!$Account->AllowChangeSettings) SetError(PROC_CANT_DEL_ACCT_BY_ID);
					if ($sarray)
					{
						$deleteId = Get::val('acctid', -1);
						
						if ($deleteId == -1)
						{
							SetError(PROC_CANT_DEL_ACCT_BY_ID);
						}
						
						$dbStorage = &DbStorageCreator::CreateDatabaseStorage($Account);
						if ($dbStorage->Connect())
						{
							$accounts = &$dbStorage->SelectAccounts($Account->IdUser);
							if (!$accounts)
							{
								SetError(PROC_CANT_GET_ACCT_LIST);
							}
							
							if (!$dbStorage->IsAccountInRing($deleteId, $Account->Id))
							{
								SetError(PROC_WRONG_ACCT_ACCESS);
							}
							
						}
						else 
						{
							SetError(getGlobalError());
						}
						
						$is_def = false;
						$is_last = false;
						$is_lastdef = false;
						$is_edit = false;
						
						$c = count($accounts);
						if ($c > 1)
						{
							foreach ($accounts AS $id => $currAccount)
							{
								if ($id == $deleteId && $currAccount[6])
								{
									$is_def = true;
								}
							}
							if ($is_def)
							{
								$is_lastdef = true;
								foreach ($accounts AS $id => $currAccount)
								{
									if ($id != $deleteId && $currAccount[6])
									{
										$is_lastdef = false	;
									}
								}
							}
							if ($sarray[EDIT_ACCOUNT_ID] == $deleteId) $is_edit = true;
						}
						elseif ($c == 1) 
						{
							if (isset($accounts[$deleteId]))
							{
								$is_last = true;
								$is_edit = true;
							}
						}
						else 
						{
							SetError(PROC_CANT_DEL_ACCT_BY_ID);
						}
						
						if ($c > 1)
						{
							if ($is_lastdef) SetError(ACCT_CANT_DEL_LAST_DEF_ACCT);
						}
						else 
						{
							if (!$Account->DeleteFromDb($deleteId))
							{
								SetError(PROC_CANT_DEL_ACCT_BY_ID);
							}
							@session_destroy();
							header('Location: '.LOGINFILE);
							exit();
						}
							
						if (Session::val(ACCOUNT_ID, -1) == $deleteId)
						{
							foreach ($accounts AS $id => $currAccount)
							{
								if ($id != $deleteId && $currAccount[6])
								{
									$_SESSION[ACCOUNT_ID] = $id;
									unset($_SESSION[SARRAY][FOLDER_ID], $_SESSION[SARRAY][PAGE]);
									break;
								}
							}
								
							if ($_SESSION[ACCOUNT_ID] == $deleteId)
							{
								foreach ($accounts AS $id => $currAccount)
								{
									if ($id != $deleteId)
									{
										$_SESSION[ACCOUNT_ID] = $id;
										unset($_SESSION[SARRAY][FOLDER_ID], $_SESSION[SARRAY][PAGE]);
										break;
									}
								}			
							}
							
							if (!Account::DeleteFromDb($deleteId))
							{
								SetError(PROC_CANT_DEL_ACCT_BY_ID);
							}
						}
						else 
						{
							if ($is_edit)
							{
								foreach ($accounts AS $id => $currAccount)
								{
									if ($id != $deleteId && $currAccount[6])
									{
										$_SESSION[SARRAY][EDIT_ACCOUNT_ID] = $id;
										break;
									}
								}
								
								if ($_SESSION[SARRAY][EDIT_ACCOUNT_ID] == $deleteId)
								{
									foreach ($accounts AS $id => $currAccount)
									{
										if ($id != $deleteId)
										{
											$_SESSION[SARRAY][EDIT_ACCOUNT_ID] = $id;
											break;
										}
									}			
								}
							}
							
							if (!Account::DeleteFromDb($deleteId))
							{
								SetError(PROC_CANT_DEL_ACCT_BY_ID);
							}
							
						}
						header('Location: '.BASEFILE);
					}
					break;

				case 'filter':
					if ($sarray && in_array($sarray[EDIT_ACCOUNT_ID], $Accounts))
					{
						$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
			
						if ($dbStorage->Connect())
						{
							if (Get::val('fdel', -1) == -1 || !$dbStorage->DeleteFilter(Get::val('fdel', -1), $sarray[EDIT_ACCOUNT_ID]))
							{
								SetError(PROC_CANT_DEL_FILTER_BY_ID);
							}
						}
						else
						{
							SetError(getGlobalError());
						}
					}
					header('Location: '.BASEFILE);
					break;
				
				case 'contacts':
					if (!$settings->AllowContacts) SetError(PROC_CANT_DEL_CONT_GROUPS);
					$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
					if ($dbStorage->Connect())
					{
						$result = true;
						
						foreach (array_keys(Post::val('contacts', array())) as $key)
						{
							$result &= $dbStorage->DeleteAddressBookRecord($key, -1);
						}
						
						foreach (array_keys(Post::val('groups', array())) as $key)
						{
							$result &= $dbStorage->DeleteAddressGroup($key, -1);
						}
						
						if (!$result)
						{
							SetError(PROC_CANT_DEL_CONT_GROUPS);
						}
					}
					else
					{
						SetError(getGlobalError());
					}
					
					header('Location: '.BASEFILE);
					break;

				default:			
				case 'none': header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX); break;
			}
			break;
			
		case 'send':
			switch ($REQ)
			{
				case 'message':
					$message = &CreateMessageFromPost($Account);
					
					$folders = &$Processor->GetFolders();
					$folder = &$folders->GetFolderByType(FOLDERTYPE_SentItems);
					
					//$message->TextBodies->AddTextBannerToBodyText('Thanks for purchase (WebMail)');
					
					$message->OriginalMailMessage = $message->ToMailString(true);

					$fs = new FileSystem(INI_DIR.'/temp', $Account->Email, $Account->Id);
					$fs->DeleteAccountDirs();
					unset($fs);
					
					$message->Flags |= MESSAGEFLAGS_Seen;
					
					$from = &$message->GetFrom();

					$result = true;
					$needToDelete = ($message->IdMsg != -1);
					$idtoDelete = $message->IdMsg;
					if (CSmtp::SendMail($Account, $message, null, null))
					{
						if ($needToDelete)
						{
							$draftsFolder = &$folders->GetFolderByType(FOLDERTYPE_Drafts);
							if (!$Processor->SaveMessage($message, $folder, $draftsFolder))
							{
								$needToDelete = false;
							}
						}
						else 
						{
							if (!$Processor->SaveMessage($message, $folder))
							{
								$needToDelete = false;
							}
						}
						
						if ($needToDelete)
						{
							$messageIdSet = array($idtoDelete);
							if ($Account->MailProtocol == MAILPROTOCOL_IMAP4)	
							{
								if ($Processor->PurgeFolder($draftsFolder))
								{
									$Processor->DbStorage->DeleteMessages($messageIdSet, false, $draftsFolder);
								}
							}
							else 
							{
								$Processor->DbStorage->DeleteMessages($messageIdSet, false, $draftsFolder);
							}
						}
						$Processor->DbStorage->UpdateMailboxSize();
						
						$result = true;
					}
					else
					{
						if (!$needToDelete)
						{
							$draftsFolder = &$folders->GetFolderByType(FOLDERTYPE_Drafts);	
							if ($Processor->SaveMessage($message, $draftsFolder))			
							{
								$_SESSION[GOTOFOLDER] = FOLDERTYPE_Drafts;
							}
						}
						$result = false;
					}
							
					if (!$result)
					{
						SetError(PROC_CANT_SEND_MSG.' '.getGlobalError(), BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX);
					}
					else 
					{
						SetReport(ReportMessageSent);
					}
					header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX);	
					break;
					
				default:			
				case 'none': header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX); break;
			}
			break;
			
		case 'save':
			switch ($REQ)
			{
				case 'message':
					
					$message = &CreateMessageFromPost($Account);
					
					$folders = &$Processor->GetFolders();
					$folder = &$folders->GetFolderByType(FOLDERTYPE_Drafts);
					
					$from = &$message->GetFrom();
					$message->OriginalMailMessage = $message->ToMailString();
					
					$fs = new FileSystem(INI_DIR.'/temp', $Account->Email, $Account->Id);
					$fs->DeleteAccountDirs();
					unset($fs);
					
					$message->Flags |= MESSAGEFLAGS_Seen;
					
					$messageIdUidSet = array();
					$messageIdUidSet[$message->IdMsg] = $message->Uid;
					
					if ($message->IdMsg != -1)
					{
						$messageIdSet = array($message->IdMsg);
					}
					
					$result = ($message->IdMsg != -1)
									? $Processor->UpdateMessage($message, $folder)
									: $Processor->SaveMessage($message, $folder);
					
					if ($Account->MailProtocol == MAILPROTOCOL_POP3)
					{
						if ($result)
						{
							$Processor->SetFlags($messageIdUidSet, $folder, MESSAGEFLAGS_Seen, ACTION_Set);
							$Processor->DbStorage->UpdateMailboxSize();
							echo '<script>if (parent.Rep_m) { parent.Rep_m.Show("'.ConvertUtils::ClearJavaScriptString(ReportMessageSaved, '"').'"); }';
							if ($message && $message->IdMsg != -1)
							{
								echo "\r\n".'if (parent.UpdateIdUid) { parent.UpdateIdUid('.((int) $message->IdMsg).', "'.ConvertUtils::ClearJavaScriptString($message->Uid, '"').'"); }';
							}
							echo '</script>';
						}
						else
						{
							echo '<script>if (parent.Err_m) { parent.Err_m.Show("'.ConvertUtils::ClearJavaScriptString(PROC_CANT_SAVE_MSG.' '.getGlobalError(), '"').'"); }</script>';
						}
					}
					else
					{
						if ($result)
						{
							if ($Processor->SetFlags($messageIdUidSet, $folder, MESSAGEFLAGS_Seen, ACTION_Set))
							{
								if ($needToDelete)
								{
									if ($Processor->PurgeFolder($folder))
									{
										$Processor->DbStorage->DeleteMessages($messageIdSet, false, $folder);
									}
								}
							}
							$Processor->DbStorage->UpdateMailboxSize();
							SetReport(ReportMessageSaved);
						}
						else
						{
							SetError(PROC_CANT_SAVE_MSG.' '.getGlobalError());
						}
						
						header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX);	
					}
					break;
					
					/*
				case 'message_old':
					
					$message = &CreateMessageFromPost($Account);
					
					$folders = &$Processor->GetFolders();
					$folder = &$folders->GetFolderByType(FOLDERTYPE_Drafts);
					
					$from = &$message->GetFrom();
					$message->OriginalMailMessage = $message->ToMailString();
					
					$fs = new FileSystem(INI_DIR.'/temp', $Account->Email, $Account->Id);
					$fs->DeleteAccountDirs();
					unset($fs);
					
					$message->Flags |= MESSAGEFLAGS_Seen;
					
					$messageIdUidSet = array();
					$messageIdUidSet[$message->IdMsg] = $message->Uid;
					
					$result = true;
					
					$needToDelete = false;
					
					if ($message->IdMsg != -1)
					{
						$needToDelete = true;
						$messageIdSet = array($message->IdMsg);
					}
					
					$needToDelete = ($message->IdMsg != -1);
					if ($Processor->SaveMessage($message, $folder))
					{
						if ($Processor->SetFlags($messageIdUidSet, $folder, MESSAGEFLAGS_Seen, ACTION_Set))
						{
							if ($needToDelete)
							{
								if ($Account->MailProtocol == MAILPROTOCOL_IMAP4)
								{
									if ($Processor->PurgeFolder($folder))
									{
										$Processor->DbStorage->DeleteMessages($messageIdSet, false, $folder);
									}
								}
								else 
								{
									$Processor->DbStorage->DeleteMessages($messageIdSet, false, $folder);				
								}
							}
						}
						$Processor->DbStorage->UpdateMailboxSize();
						SetReport(ReportMessageSaved);
					}
					else
					{
						SetError(PROC_CANT_SAVE_MSG.' '.getGlobalError());
					}
					
					header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX);	
					break;
					*/

				case 'contactset':
					$ContactsPerPage = (int) Post::val('contacts_per_page', $Account->ContactsPerPage);
					$ContactsPerPage = ($ContactsPerPage < 1) ? 1 : $ContactsPerPage;

					if ($Account->IsDemo)
					{
						$_SESSION[DEMO_SES][DEMO_S_ContactsPerPage] = $ContactsPerPage;
					}
					else 
					{
						$Account->ContactsPerPage = $ContactsPerPage;
					}
					
					$validate = $Account->ValidateData();
					if ($validate !== true)
					{
						SetError(PROC_CANT_UPDATE_CONT_SETTINGS. ': ' .$validate);
					}
					elseif ($Account->Update(null))
					{
						SetReport(ReportContactsSettingsUpdatedSuccessfuly);
					}
					else 
					{
						SetError(PROC_CANT_UPDATE_CONT_SETTINGS. ': ' .getGlobalError());
					}
					header('Location: '.BASEFILE);
					break;
				
				case 'commonset':
					$MailsPerPage = (int) Post::val('mails_per_page', $Account->MailsPerPage);
					$MailsPerPage = ($MailsPerPage < 1) ? 1 : $MailsPerPage;
					$AllowDhtmlEditor = (bool) !Post::val('int_disable_dhtml_editor', false);
					$DefaultSkin = Post::val('str_skin_path', $Account->DefaultSkin);
					$DefaultOutCharset = Post::val('str_charset', $Account->DefaultOutCharset);
					$DefaultTimeZone = (int) Post::val('str_time_zone', $Account->DefaultTimeZone);
					$DefaultTimeZone = ($DefaultTimeZone) ? $DefaultTimeZone : 0;
					$DefaultLanguage = Post::val('str_def_language', $Account->DefaultLanguage);
					

					$AcctDateFormat = Post::val('str_date_format_input', $Account->DefaultDateFormat);
					if ($AcctDateFormat == '')	
					{
						SetError(WarningAdvancedDateFormat);
						header('Location: '.BASEFILE);
						break;
					}
					
					$timeFormat = (int) Post::val('time_format', '0');
					$AcctTimeFormat = ($timeFormat > 1 || $timeFormat < 0) ? 0 : $timeFormat;
					
					$ViewMode = ((int) Post::val('int_showimg', 0))
							? (int) Post::val('int_use_preview_pane', 0) + 2
							: (int) Post::val('int_use_preview_pane', 0);
							
					if ($Account->IsDemo)
					{
						$_SESSION[DEMO_SES][DEMO_S_MessagesPerPage] = $MailsPerPage;
						$_SESSION[DEMO_SES][DEMO_S_AllowDhtmlEditor] = $AllowDhtmlEditor;
						$_SESSION[DEMO_SES][DEMO_S_DefaultOutCharset] = $DefaultOutCharset;
						$_SESSION[DEMO_SES][DEMO_S_DefaultTimeZone] = $DefaultTimeZone;
						$_SESSION[DEMO_SES][DEMO_S_ViewMode] = $ViewMode;
						$_SESSION[DEMO_SES][DEMO_S_DefaultSkin] = $DefaultSkin;
						$_SESSION[DEMO_SES][DEMO_S_DefaultLanguage] = $DefaultLanguage;
						$_SESSION[DEMO_SES][DEMO_S_DefaultDateFormat] = $AcctDateFormat;
						$_SESSION[DEMO_SES][DEMO_S_DefaultTimeFormat] = $AcctTimeFormat;
					}
					else 
					{
						$Account->MailsPerPage = $MailsPerPage;
						$Account->AllowDhtmlEditor = $AllowDhtmlEditor;
						$Account->DefaultOutCharset = $DefaultOutCharset;
						$Account->DefaultTimeZone = $DefaultTimeZone;
						$Account->ViewMode = $ViewMode;
						$Account->DefaultSkin = $DefaultSkin;
						$Account->DefaultLanguage = $DefaultLanguage;
						$Account->DefaultDateFormat = $AcctDateFormat;
						$Account->DefaultTimeFormat = $AcctTimeFormat;
					}		
							
					$validate = $Account->ValidateData();
					if ($validate !== true)
					{
						SetError(PROC_CANT_UPDATE_ACCT.': '.$validate);
					}
					elseif ($Account->Update(null))
					{
						SetReport(ReportSettingsUpdatedSuccessfuly);
						$_SESSION[SESSION_LANG] = $DefaultLanguage;
					}
					else 
					{
						SetError(PROC_CANT_UPDATE_ACCT.': '.getGlobalError());
					}
					header('Location: '.BASEFILE);
					break;
				default:
				case 'none': header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX); break;
			}
			break;	
			
		default:
		case 'none': header('Location: '.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX); break;
	}
	
	/**
	 * @return AddressBookRecord
	 */
	function &GetContactFromPost()
	{
		$contact = &new AddressBookRecord();
		
		$contact->IdAddress = Post::val('contactId', '');
		
		$contact->BirthdayDay = Post::val('birthday_day', '');
		$contact->BirthdayDay = ($contact->BirthdayDay == 0) ? '' : $contact->BirthdayDay;
		$contact->BirthdayMonth = Post::val('birthday_month', '');
		$contact->BirthdayMonth = ($contact->BirthdayMonth == 0) ? '' : $contact->BirthdayMonth;
		$contact->BirthdayYear = Post::val('birthday_year', '');
		$contact->BirthdayYear = ($contact->BirthdayYear == 0) ? '' : $contact->BirthdayYear;
		
		$contact->PrimaryEmail = (int) Post::val('default_email_type', 0);
		
		$contact->UseFriendlyName = (bool) Post::val('use_friendly_name', 0);
		$contact->FullName = Post::val('c_fullname', '');
		
		$contact->HomeEmail = str_replace('\\"', '"', Post::val('personal_email', ''));
		$contact->HomeStreet = Post::val('personal_street', '');
		$contact->HomeCity = Post::val('personal_city', '');
		$contact->HomeFax = Post::val('personal_fax', '');
		$contact->HomeState = Post::val('personal_state', '');
		$contact->HomePhone = Post::val('personal_phone', '');
		$contact->HomeZip = Post::val('personal_zip', '');
		$contact->HomeMobile = Post::val('personal_mobile', '');
		$contact->HomeCountry = Post::val('personal_country', '');
		$contact->HomeWeb = Post::val('personal_web', '');
		
		$contact->BusinessEmail = Post::val('business_email', '');
		$contact->BusinessCompany = Post::val('business_company', '');
		$contact->BusinessJobTitle = Post::val('business_job', '');
		$contact->BusinessDepartment = Post::val('business_departament', '');
		$contact->BusinessOffice = Post::val('business_office', '');
		$contact->BusinessStreet = Post::val('business_street', '');
		$contact->BusinessCity = Post::val('business_city', '');
		$contact->BusinessFax = Post::val('business_fax', '');
		$contact->BusinessState = Post::val('business_state', '');
		$contact->BusinessPhone = Post::val('business_phone', '');
		$contact->BusinessZip = Post::val('business_zip', '');
		$contact->BusinessCountry = Post::val('business_country', '');
		$contact->BusinessWeb = Post::val('business_web', '');
		
		$contact->OtherEmail = Post::val('other_email', '');
		$contact->Notes = Post::val('other_notes', '');
		
		$temp = '';
		switch ($contact->PrimaryEmail)
		{
			case PRIMARYEMAIL_Home: $temp = $contact->HomeEmail; break;
			case PRIMARYEMAIL_Business: $temp = $contact->BusinessEmail; break;
			case PRIMARYEMAIL_Other: $temp = $contact->OtherEmail; break;
		}
		
		if (!$temp && Post::val('input_default_email', '') != '')
		{
			$temp = Post::val('input_default_email', '');
			switch ($contact->PrimaryEmail)
			{
				case PRIMARYEMAIL_Home: $contact->HomeEmail = $temp; break;
				case PRIMARYEMAIL_Business: $contact->BusinessEmail = $temp; break;
				case PRIMARYEMAIL_Other: $contact->OtherEmail = $temp; break;
			}
		}
	
		return $contact;		
	}

	/**
	 * @param Account $account
	 * @return WebMailMessage
	 */
	function &CreateMessageFromPost(&$account)
	{
		$message = &new WebMailMessage();
		$GLOBALS[MailDefaultCharset] = $account->GetUserCharset();
		$GLOBALS[MailInputCharset] = $account->GetUserCharset();
		$GLOBALS[MailOutputCharset] = $account->GetDefaultOutCharset();
		
		$message->Headers->SetHeaderByName(MIMEConst_MimeVersion, '1.0');
		$message->Headers->SetHeaderByName(MIMEConst_XMailer, 'MailBee WebMail Pro PHP');
		$message->Headers->SetHeaderByName(MIMEConst_XOriginatingIp, isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0');

		$message->IdMsg = Post::val('m_id', -1);
		$message->SetPriority(Post::val('priority_input', 3));
		$message->DbPriority = Post::val('priority_input', 3);
		$message->Uid = Post::val('m_uid', '');

		$message->Headers->SetHeaderByName(MIMEConst_MessageID, 
					'<'.substr(session_id(), 0, 7).'.'.md5(time()).'@'.$_SERVER['SERVER_NAME'].'>');
		
		$temp = Post::val('from', '');
		if ($temp)
		{
			$message->SetFromAsString($temp);	
		}
		$temp = Post::val('toemail', '');
		if ($temp)
		{
			$message->SetToAsString($temp);
		}
		$temp = Post::val('toCC', '');
		if ($temp)
		{
			$message->SetCcAsString($temp);
		}
		$temp = Post::val('toBCC', '');
		if ($temp)
		{
			$message->SetBccAsString($temp);
		}
		$temp = Post::val('subject', '');
		if ($temp)
		{
			$message->SetSubject($temp);
		}
		$message->SetDate(new CDateTime(time()));
		
		if (Post::val('ishtml', 0))
		{
			$message->TextBodies->HtmlTextBodyPart = ConvertUtils::BackImagesToHtmlBody(Post::val('message', ''));
		}
		else
		{
			$message->TextBodies->PlainTextBodyPart = ConvertUtils::BackImagesToHtmlBody(Post::val('message', ''));
		}
		
		$attachments = Post::val('attachments');
		
		if ($attachments && is_array($attachments))
		{
			$fs = &new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
			$attfolder = &new Folder($account->Id, -1, Session::val('attachtempdir'));
			
			foreach($attachments as $key => $value)
			{
				if (Session::val('attachtempdir'))
				{
					$attachCid = 'attach.php?tn='.$key;
					$replaceCid = md5(time().$value);
					
					$mime_type = ConvertUtils::GetContentTypeFromFileName($value);
					$message->Attachments->AddFromFile($fs->GetFolderFullPath($attfolder).'/'.$key, 
										$value, $mime_type, false);
					
					if (Post::val('ishtml', 0))
					{
						if (strpos($message->TextBodies->HtmlTextBodyPart, $attachCid) !== false)
						{
							$attachment = &$message->Attachments->GetLast();
							$attachment->MimePart->Headers->SetHeaderByName(MIMEConst_ContentID, '<'.$replaceCid.'>');
							$message->TextBodies->HtmlTextBodyPart = str_replace($attachCid, 'cid:'.$replaceCid, $message->TextBodies->HtmlTextBodyPart);
								
							$attachname = ConvertUtils::EncodeHeaderString($value, $account->GetUserCharset(), $GLOBALS[MailOutputCharset]);
							$attachment->MimePart->Headers->SetHeaderByName(MIMEConst_ContentDisposition, MIMEConst_InlineLower.';'.CRLF."\t".MIMEConst_FilenameLower.'="'.$attachname.'"', false);
						}
					}
				}
			}
		}
		
		return $message;
	}
