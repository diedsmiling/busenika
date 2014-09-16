<?php

if(!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__) . '/'));
require_once (WM_ROOTPATH . 'libs/class_imap.php');
require_once (WM_ROOTPATH . 'class_webmailmessages.php');
require_once (WM_ROOTPATH . 'class_folders.php');
require_once (WM_ROOTPATH . 'class_mailstorage.php');

class ImapStorage extends MailServerStorage
{
	/**
	 * @access private
	 * @var IMAPMAIL
	 */
	var $_imapMail;

	/**
	 * @param Account $account
	 * @return ImapStorage
	 */
	function ImapStorage(&$account)
	{
		MailServerStorage::MailServerStorage($account);
		$this->_imapMail = &new IMAPMAIL();
		$this->_imapMail->host = $account->MailIncHost;
		$this->_imapMail->port = $account->MailIncPort;
		$this->_imapMail->user = $account->MailIncLogin;
		$this->_imapMail->password = $account->MailIncPassword;
	}

	/**
	 * @param $arg[optional] = false
	 * @return bool
	 */
	function Connect($arg = false)
	{
		if($this->_imapMail->connection != false)
		{
			return true;
		}
		register_shutdown_function(array(&$this, 'Disconnect'));
		
		if (!$this->_imapMail->open())
		{
			setGlobalError(ErrorIMAP4Connect);
			return false;
		}
		
		if (!$this->_imapMail->login($this->Account->MailIncLogin, $this->Account->MailIncPassword))
		{
			setGlobalError(ErrorPOP3IMAP4Auth);
			return false;				
		}

		return true;
	}

	/**
	 * @return bool
	 */
	function Disconnect()
	{
		if($this->_imapMail->connection == false)
		{
			return true;
		}
		return $this->_imapMail->close();
	}

	/**
	 * @param Array $messageIndexSet
	 * @param bool $indexAsUid
	 * @param Folder $folder
	 * @return WebMailMessageCollection
	 */
	function LoadMessages(&$messageIndexSet, $indexAsUid, &$folder, $imapUids = null, $imapUidFlags = null, $imapUidSizes = null)
	{
		$messageCollection = null;
		if($this->_imapMail->examine_mailbox($folder->FullName))
		{
			$_imapUids = array();
			$_imapUidFlags = array();
			$_imapUidSizes = array();
				
			if ($imapUids == null)
			{
				//Get uid, flags and size from imap Server
				$paramsMessages = $this->_imapMail->getParamsMessages();
				if (!is_array($paramsMessages))
				{
					return $messageCollection;
				}
				
				foreach($paramsMessages as $key => $value)
				{
					$_imapUids[$key] = $value["uid"];
					$_imapUidFlags[$value["uid"]] = $value["flag"];
					$_imapUidSizes[$value["uid"]] = $value["size"];
				}
			}
			
			$messageCollection = &new WebMailMessageCollection();
			foreach($messageIndexSet as $idx)
			{
				if($this->DownloadedMessagesHandler != null)
				{
					ShowDownloadedMessageNumber();
				}
				
				$response = $this->_imapMail->get_message($idx, $indexAsUid);
				if($response)
				{
		
					$log =& CLog::CreateInstance();
					$log->WriteLine('::::MSG:::: ' . $response);
					$msg = &new WebMailMessage();
					$msg->LoadMessageFromRawBody($response, true);
					if($indexAsUid)
					{
						$msg->Uid = $idx;
					} 
					else
					{
						if ($imapUids == null) 
						{
							$imapUids = $_imapUids;
						}
						$msg->Uid = $imapUids[$idx];
					}
					
					if ($imapUidSizes == null)
					{
						$imapUidSizes = $_imapUidSizes;
					}					
					$msg->Size = $imapUidSizes[$msg->Uid];
					
					if ($imapUidFlags == null)
					{
						$imapUidFlags = $_imapUidSizes;
					}
					
					$this->_setMessageFlags($msg, $imapUidFlags[$idx]);
					$messageCollection->Add($msg);
				}
			}
			if($messageCollection->Count() > 0)
			{
				return $messageCollection;
			}
		}
		return $messageCollection;
	}


	/**
	 * @param string $messageIndex
	 * @param bool $indexAsUid
	 * @param Folder $folder
	 * @return WebMailMessage
	 */
	function LoadMessage($messageIndex, $indexAsUid, &$folder)
	{
		$msg = null;
		if($this->_imapMail->select_mailbox($folder->FullName))
		{
			$paramsMessages = &$this->_imapMail->getParamsMessages();
			$imapFlags = array();
			$imapUids = array();
			if (!is_array($paramsMessages))
			{
				return $msg;
			}
			
			foreach($paramsMessages as $key => $value)
			{
				$imapFlags[$key] = $value["flag"];
				$imapUids[$key] = $value["uid"];
			}
			
			//$imapFlags = &$this->_imapMail->get_flaglist();
			//$imapUids = &$this->_imapMail->get_uidlist();
			$response = $this->_imapMail->get_message($messageIndex, $indexAsUid);
			if($response)
			{
				$msg = &new WebMailMessage();
				$msg->LoadMessageFromRawBody($response, true);
				if($indexAsUid)
				{
					$msg->Uid = $messageIndex;
					foreach($imapUids as $key => $value)
					{
						if($value == $messageIndex)
						{
							$idx = $key;
							break;
						}
					}
				} 
				else
				{
					$idx = $messageIndex;
					$msg->Uid = $imapUids[$messageIndex];
				}
				$this->_setMessageFlags($msg, $imapFlags[$idx]);
				return $msg;
			}
			else 
			{
				setGlobalError(PROC_MSG_HAS_DELETED);
			}
		}
		return $msg;
	}

	/**
	 * @param int $pageNumber
	 * @param Folder $folder
	 * @return WebMailMessageCollection
	 */
	function &LoadMessageHeaders($pageNumber, &$folder)
	{
		$webMailMessageCollection = null;
		if($this->_imapMail->examine_mailbox($folder->FullName))
		{
			//$imapUids = &$this->_imapMail->get_uidlist();
			$paramsMessages = $this->_imapMail->getParamsMessages();
			$imapFlags = array();
			$imapUids = array();
			$imapSizes = array();
			if (!is_array($paramsMessages))
			{
				return $webMailMessageCollection;
			}
			
			foreach($paramsMessages as $key => $value)
			{
				$imapFlags[$key] = $value["flag"];
				$imapUids[$key] = $value["uid"];
				$imapSizes[$key] = $value["size"];
			}

			//$imapUids = array_reverse($imapUids);
			//$imapFlags = array_reverse($imapFlags);
			//$imapSizes = array_reverse($imapSizes);
			
			if(count($paramsMessages) < 1)
			{
				$newcoll =& new WebMailMessageCollection();
				return $newcoll;
			}
			
			$msgCount = count($imapUids);
			$messageIndexSet = array();
			for($i = $msgCount - ($pageNumber - 1) * $this->Account->MailsPerPage; $i > $msgCount - $pageNumber * $this->Account->MailsPerPage; $i--)
			{
				if($i == 0) break;
				$messageIndexSet[] = $imapUids[$i];
				$imapNFlags[$imapUids[$i]] = $imapFlags[$i];
				$imapNSizes[$imapUids[$i]] = $imapSizes[$i];
			}
			//$messageIndexSet = $imapUids;
			$webMailMessageCollection = &$this->_loadMessageHeaders($messageIndexSet, $imapUids, $imapNFlags, $imapNSizes);
			return $webMailMessageCollection;
		}
		return $webMailMessageCollection;
	}

	/**
	 * @access private
	 * @param Array $messageIndexSet
	 * @param bool $indexAsUid
	 * @param Folder $folder
	 * @return WebMailMessageCollection
	 */
	function _loadMessages($messageIndexSet, &$imapUids)
	{

		$messageCollection = &new WebMailMessageCollection();
		$imapFlags = &$this->_imapMail->get_flaglist();
		foreach($messageIndexSet as $idx)
		{
			$response = $this->_imapMail->get_message($idx);
			if($response)
			{
				$msg = &new WebMailMessage();
				$msg->LoadMessageFromRawBody($response);
				$msg->Uid = $imapUids[$idx];
				$msg->Size = strlen($response);
				$this->_setMessageFlags($msg, $imapFlags[$idx]);
				$messageCollection->Add($msg);
			}
		}
		if($messageCollection->Count() > 0)
		{
			return $messageCollection;
		}
		return null;
	}

	/**
	 * @param array $messageIndexSet
	 * @param array $imapUids
	 * @param array $imapFlags
	 * @param array $imapSizes
	 * @return messageCollection
	 */
	function _loadMessageHeaders(&$messageIndexSet, &$imapUids, &$imapUidFlags, &$imapUidSizes)
	{
		$messageCollection = &new WebMailMessageCollection();
		//$imapFlags = &$this->_imapMail->get_flaglist();
		foreach($messageIndexSet as $idx)
		{
			if($this->DownloadedMessagesHandler != null)
			{
				call_user_func($this->DownloadedMessagesHandler);
			}
			$response = $this->_imapMail->get_message_header($idx, true);
			if($response)
			{
				$msg = &new WebMailMessage();
				$msg->LoadMessageFromRawBody($response);
				$msg->IdMsg = $idx;
				$msg->Uid = $idx;
				$msg->Size = $imapUidSizes[$idx];
				$this->_setMessageFlags($msg, $imapUidFlags[$idx]);
				$messageCollection->Add($msg);
			}
		}
		
		if($messageCollection->Count() > 0)
		{
			return $messageCollection;
		}
		$messageCollection = null;
		
		return $messageCollection;
	}

	/**
	 * @param FolderCollection $folders
	 * @return bool
	 */
	function SynchronizeFolder(&$folder)
	{

		$dbStorage = &DbStorageCreator::CreateDatabaseStorage($this->Account);
		if($dbStorage->Connect())
		{
			$lastIdMsg = $dbStorage->SelectLastIdMsg();
			return $this->_synchronizeFolderWithOpenDbConnection($folder, $dbStorage, $lastIdMsg);
		}
		return false;
	}

	/**
	 * @param FolderCollection $folders
	 * @return bool
	 */
	function Synchronize(&$folders)
	{
		$result = true;
		$dbStorage = &DbStorageCreator::CreateDatabaseStorage($this->Account);
		if($dbStorage->Connect())
		{
			$lastIdMsg = $dbStorage->SelectLastIdMsg();
			$folderList = $folders->CreateFolderListFromTree(); //copy tree object here
			for($i = 0, $icount = $folderList->Count(); $i < $icount; $i++)
			{
				$folder = &$folderList->Get($i);
				$result &= $this->_synchronizeFolderWithOpenDbConnection($folder, $dbStorage, $lastIdMsg);
			}
			return $result;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	function SynchronizeFolders()
	{
		$result = true;
		$dbStorage = &DbStorageCreator::CreateDatabaseStorage($this->Account);
		$serverFoldersTree = &$this->GetFolders();
		if($serverFoldersTree != null && $dbStorage->Connect())
		{
			$dbFoldersTree = &$dbStorage->GetFolders();
			$serverFoldersList = &$serverFoldersTree->CreateFolderListFromTree();
			$dbFoldersList = &$dbFoldersTree->CreateFolderListFromTree();
			$delimiter = $this->Account->Delimiter;
			foreach(array_keys($serverFoldersList->Instance()) as $mkey)
			{
				$mailFolder = &$serverFoldersList->Get($mkey);
				$folderExist = false;
				foreach(array_keys($dbFoldersList->Instance()) as $skey)
				{
					$dbFolder = &$dbFoldersList->Get($skey);
					if(trim($mailFolder->FullName, $delimiter) == trim($dbFolder->FullName, $delimiter))
					{
						$folderExist = true;
						if($mailFolder->SubFolders != null)
						{
							foreach(array_keys($mailFolder->SubFolders->Instance()) as $subkey)
							{
								$subFld =& $mailFolder->SubFolders->Get($subkey);
								$subFld->IdParent = $dbFolder->IdDb;
							}
						}
						
						if ($dbFolder->Type == FOLDERTYPE_Custom && $dbFolder->Hide != $mailFolder->Hide)
						{
							$dbFolder->Hide = $mailFolder->Hide;
							$dbStorage->UpdateFolder($dbFolder);
						}
						
						break;
					}
				}
				if(!$folderExist)
				{
					if ($this->_settings->AllowDirectMode && $this->_settings->DirectModeIsDefault)
					{
						$mailFolder->SyncType = FOLDERSYNC_DirectMode;
					}
					$result &= $dbStorage->CreateFolder($mailFolder);
				}
			}
			foreach(array_keys($dbFoldersList->Instance()) as $skey)
			{
				$dbFolder = &$dbFoldersList->Get($skey);
				$folderExist = false;
				foreach(array_keys($serverFoldersList->Instance()) as $mkey)
				{
					$mailFolder = &$serverFoldersList->Get($mkey);
					if(trim($mailFolder->FullName, $delimiter) == trim($dbFolder->FullName, $delimiter))
					{
						$folderExist = true;
						break;
					}
				}
				
				if(!$folderExist && $dbFolder->SyncType != FOLDERSYNC_DontSync)
				{
					$dbFolder->SyncType = FOLDERSYNC_DontSync;
					$dbStorage->UpdateFolder($dbFolder);
				}
			}
		}
		return $result;
	}

	/**
	 * @param Folder $folders
	 * @param DbStorage $dbStorage
	 * @param int $lastIdMsg
	 * @return bool
	 */
	function _synchronizeFolderWithOpenDbConnection(&$folder, &$dbStorage, $lastIdMsg)
	{
		$result = true;
		if($folder->SyncType == FOLDERSYNC_DontSync || $folder->SyncType == FOLDERSYNC_DirectMode || $folder->Hide)
		{
			return true;
		}
		
		if(!$this->_imapMail->examine_mailbox($folder->FullName))
		{
			return false;
		}
		
		//Get uid, flags and size from imap Server
		$paramsMessages = $this->_imapMail->getParamsMessages();
		$imapFlags = array();
		$imapUids = array();
		$imapSizes = array();
		$dbUids = array();
		$imapUidFlags = array();

		if (!is_array($paramsMessages))
		{
			return false;
		}
		
		foreach($paramsMessages as $key => $value)
		{
			$imapFlags[$key] = $value["flag"];
			$imapUids[$key] = $value["uid"];
			$imapSizes[$key] = $value["size"];
			$imapUidFlags[$value["uid"]] = $value["flag"];
			$imapUidSizes[$value["uid"]] = $value["size"];
		}
		
		$dbUidsIdMsgsFlags = &$dbStorage->SelectIdMsgAndUidByIdMsgDesc($folder);
		
		foreach($dbUidsIdMsgsFlags as $value)
		{
			$dbUidsFlag[$value[1]] = $value[2];
			$dbUids[] = $value[1];
		}
		
		//Array need added to DB
		$newUids = array_diff($imapUids, $dbUids);
		//Array delete from DB
		$uidsToDelete = array_diff($dbUids, $imapUids);
		//Intersect uids
		$currentUids = array_intersect($imapUids, $dbUids);
		if($folder->SyncType == FOLDERSYNC_AllHeadersOnly || $folder->SyncType == FOLDERSYNC_AllEntireMessages)
		{
			//Update messages whith different flags
			foreach($currentUids as $currentUid)
			{
				$flagBD = $dbUidsFlag[$currentUid];
				$flagImap = $this->getIntFlags($imapUidFlags[$currentUid]);
				if($flagBD != $flagImap)
				{
					$dbStorage->UpdateMessageFlags(array($currentUid), true, $folder, $flagImap, $this->Account);
				}
			}
		}
		
		if($this->DownloadedMessagesHandler != null)
		{
			if(ConvertUtils::IsLatin($folder->Name))
			{
				$foldername = ConvertUtils::ConvertEncoding($folder->Name, CPAGE_UTF7_Imap, $this->Account->GetUserCharset());
			} 
			else
			{
				$foldername = ConvertUtils::ConvertEncoding($folder->Name, $this->Account->DefaultIncCharset, $this->Account->GetUserCharset());
			}
			
			ShowDownloadedMessageNumber($foldername, count($newUids));
			ShowDownloadedMessageNumber();
		}
		
		//Delete from DB
		if(count($uidsToDelete) > 0 && ($folder->SyncType == FOLDERSYNC_AllHeadersOnly || $folder->SyncType == FOLDERSYNC_AllEntireMessages))
		{			
			//$result &= $dbStorage->DeleteMessages($uidsToDelete, true, $folder);
			$result &= $dbStorage->SetMessagesFlags($uidsToDelete, true, $folder, MESSAGEFLAGS_Deleted, ACTION_Set);
		}
		
		$result &= $dbStorage->UpdateMailboxSize();
		$maxEnvelopesPerSession = 1;
		
		//Get size all messages in DB
		$mailBoxesSize = $dbStorage->SelectMailboxesSize();
		
		$syncCycles = ceil(count($newUids) / $maxEnvelopesPerSession);
		for($i = 0; $i < $syncCycles; $i++)
		{
			$mailBoxesSize += $imapSizes[$i+1];
				
			if ($this->_settings->EnableMailboxSizeLimit && $this->Account->MailboxLimit < $mailBoxesSize)
			{
				$result = false;
				setGlobalError(ErrorGetMailLimit);
				break;
			}	
				
			$listPartToDownload = ($i != $syncCycles - 1) ? array_slice($newUids, $i * $maxEnvelopesPerSession, $maxEnvelopesPerSession) : array_slice($newUids, $i * $maxEnvelopesPerSession);
			
			//Synchronize
			if($folder->SyncType == FOLDERSYNC_NewEntireMessages || $folder->SyncType == FOLDERSYNC_AllEntireMessages)
			{
				$mailMessageCollection = &$this->LoadMessages($listPartToDownload, true, $folder, $imapUids, $imapUidFlags, $imapUidSizes);
			} 
			elseif($folder->SyncType == FOLDERSYNC_NewHeadersOnly || $folder->SyncType == FOLDERSYNC_AllHeadersOnly)
			{
				$mailMessageCollection = &$this->_loadMessageHeaders($listPartToDownload, $imapUids, $imapUidFlags, $imapUidSizes);
			}
			
			//Write to DB
			if($mailMessageCollection != null && $mailMessageCollection->Count() > 0)
			{
				if(!$this->ApplyFilters($mailMessageCollection, $dbStorage, $folder))
				{
					$result = false;
					break;
				}
			}
		}
		
		$result &= $dbStorage->UpdateMailboxSize();
		return $result;
	}
	
	/**
	 * @param WebMailMessageCollection $messageCollection
	 * @param DbStorage $dbStorage
	 * @param Folder $folder
	 * @return bool
	 */	
	function ApplyFilters(&$messageCollection, &$dbStorage, &$folder)
	{
		$filters = &$dbStorage->SelectFilters($this->Account->Id);
		$lastIdMsg = $dbStorage->SelectLastIdMsg();
		
		$mailBoxesSize = $dbStorage->SelectMailboxesSize();
		
		$result = true;
		
		foreach (array_keys($messageCollection->Instance()) as $key)
		{
			$message =& $messageCollection->Get($key);
			$needToSave = true;
			
			if ($this->_settings->EnableMailboxSizeLimit &&
				$this->Account->MailboxLimit < $mailBoxesSize + $message->Size)
			{
				setGlobalError(ErrorGetMailLimit);
				return false;
			}
			
			$mailBoxesSize += $message->Size;
			
			$lastIdMsg += rand(1, 10);
			$message->IdMsg = $lastIdMsg;
			
			if ($folder->SyncType == FOLDERSYNC_NewEntireMessages ||
				$folder->SyncType == FOLDERSYNC_AllEntireMessages)
			{
				$result &= $dbStorage->SaveMessage($message, $folder);
			}
			elseif ($folder->SyncType == FOLDERSYNC_NewHeadersOnly ||
					$folder->SyncType == FOLDERSYNC_AllHeadersOnly)
			{
				$result &= $dbStorage->SaveMessageHeader($message, $folder, false);
			}
		}
			
		if ($folder->Type == FOLDERTYPE_Inbox && $result && isset($GLOBALS['useFilters']))
		{
			$mailProcessor = new MailProcessor($this->Account);
			
			foreach (array_keys($filters->Instance()) as $key)
			{
				$filter = &$filters->Get($key);
				$action = $filter->GetActionToApply($message);

				$uidArray = array($message->Uid);
				$messageIdUidSet[$message->IdMsg] = $message->Uid;
				
				switch ($action)
				{
					case FILTERACTION_DeleteFromServerImmediately:
						$result &= $mailProcessor->DeleteMessages($messageIdUidSet, $folder);
						break;
						
					case FILTERACTION_MoveToFolder:

						$toFolder = &new Folder($filter->IdAcct, $filter->IdFolder, '');
						$dbStorage->GetFolderInfo($toFolder);
							
						$result &= $mailProcessor->MoveMessages($messageIdUidSet, $folder, $toFolder);
						break;
						
					case FILTERACTION_MarkGrey:
						$result &= $mailProcessor->SetFlags($messageIdUidSet, $folder, MESSAGEFLAGS_Grayed, ACTION_Set);
						break;
				}
			}
		}
		
		return $result;
	}

	/**
	 * @return FolderCollection
	 */
	function GetFolders()
	{
		$folderCollection = &new FolderCollection();
		$folders = &$this->_imapMail->list_mailbox($this->Account->Delimiter);
		$subs_folders = $this->_imapMail->list_subscribed_mailbox($this->Account->Delimiter);
		$this->_addLevelToFolderTree($folderCollection, $folders, $subs_folders);
		return $folderCollection;
	}

	/**
	 * @param Folder $folder
	 * @return bool
	 */
	function CreateFolder(&$folder)
	{
		if ($this->_imapMail->create_mailbox($folder->FullName))
		{
			if (!$folder->Hide)
			{
				$this->_imapMail->subscribe_mailbox($folder->FullName);
			}
			return true;
		}
		return false;
	}
	
	function SubscribeFolder(&$folder, $isHide = false)
	{
		if ($isHide)
		{
			$this->_imapMail->unsubscribe_mailbox($folder->FullName);
		}
		else
		{
			$this->_imapMail->subscribe_mailbox($folder->FullName);
		}
	}

	/**
	 * @param Folder $folder
	 * @return bool
	 */
	function DeleteFolder(&$folder)
	{
		return $this->_imapMail->delete_mailbox($folder->FullName);
	}

	/**
	 * @param Folder $folder
	 * @package string $newName
	 * @return bool
	 */
	function RenameFolder(&$folder, $newName)
	{
		return $this->_imapMail->rename_mailbox($folder->FullName, $newName);
	}

	/**
	 * @param WebMailMessage $message
	 * @param Folder $folder
	 * @return bool
	 */
	function SaveMessage(&$message, &$folder)
	{

		$flagsStr = '';
		if(($message->Flags & MESSAGEFLAGS_Seen) == MESSAGEFLAGS_Seen)
		{
			$flagsStr .= ' \Seen';
		}
		if(($message->Flags & MESSAGEFLAGS_Flagged) == MESSAGEFLAGS_Flagged)
		{
			$flagsStr .= ' \Flagged';
		}
		if(($message->Flags & MESSAGEFLAGS_Deleted) == MESSAGEFLAGS_Deleted)
		{
			$flagsStr .= ' \Deleted';
		}
		if(($message->Flags & MESSAGEFLAGS_Answered) == MESSAGEFLAGS_Answered)
		{
			$flagsStr .= ' \Answered';
		}
		return $this->_imapMail->append_mail($folder->FullName, $flagsStr, $message->TryToGetOriginalMailMessage());
	}

	/**
	 * @param WebMailMessageCollection $messages
	 * @param Folder $folder
	 * @return bool
	 */
	function SaveMessages(&$messages, &$folder)
	{

		$result = true;
		for($i = 0; $i < $messages->Count(); $i++)
		{
			$result &= $this->SaveMessage($messages->Get($i), $folder);
		}
		return $result;
	}

	/**
	 * @access private
	 * @param FolderCollection $folderCollection
	 * @param array $folders
	 * @param array $subsfolders
	 * @param string $rootPrefix optional
	 */
	function _addLevelToFolderTree(&$folderCollection, &$folders, $subsfolders, $rootPrefix = '', $isInbox = false)
	{
		static $InboxAdd = false;
		static $SentAdd = false;
		static $DraftsAdd = false;
		static $TrashAdd = false;
		
		$prefixLen = strlen($rootPrefix);
		$foldersCount = count($folders);
		for($i = 0; $i < $foldersCount; $i++)
		{
			$folderFullName = $folders[$i];
			if($rootPrefix != $folderFullName && strlen($folderFullName) > $prefixLen && substr($folderFullName, 0, $prefixLen) == $rootPrefix && strpos($folderFullName, $this->Account->Delimiter, $prefixLen + 1) === false)
			{
				$strLen = ($prefixLen == 0) ? $prefixLen : $prefixLen - 1;
				$name = trim(substr($folderFullName, $strLen), $this->Account->Delimiter);
				$names = explode($this->Account->Delimiter, $name);
				$folderObj =& new Folder($this->Account->Id, -1, $folderFullName, $names[0]);
				
				if ($prefixLen == 0 || $isInbox)
				{
					switch ($folderObj->Type)
					{
						case FOLDERTYPE_Inbox:
							if ($InboxAdd) $folderObj->Type = FOLDERTYPE_Custom;
							$InboxAdd = true;
							break;
						case FOLDERTYPE_SentItems:
							if ($SentAdd) $folderObj->Type = FOLDERTYPE_Custom;
							$SentAdd = true;
							break;
						case FOLDERTYPE_Drafts:
							if ($DraftsAdd) $folderObj->Type = FOLDERTYPE_Custom;
							$DraftsAdd = true;
							break;
						case FOLDERTYPE_Trash:
							if (USEIMAPTRASH)
							{
								if ($TrashAdd) $folderObj->Type = FOLDERTYPE_Custom;
								$TrashAdd = true;
							}
							else
							{
								$folderObj->Type = FOLDERTYPE_Custom;
							}
							break;					
					}
				}
				else 
				{
					$folderObj->Type = FOLDERTYPE_Custom;
				}
				
				$folderObj->Hide = !in_array($folderObj->FullName, $subsfolders);
				if ($folderObj->Type != FOLDERTYPE_Custom)
				{
					$folderObj->Hide = false;
				}
				
				$folderCollection->Add($folderObj);
				$newCollection =& new FolderCollection();
				if ($folderObj->Type == FOLDERTYPE_Inbox)
				{
					$this->_addLevelToFolderTree($newCollection, $folders, $subsfolders, $folderFullName.$this->Account->Delimiter, true);	
				}
				else 
				{
					$this->_addLevelToFolderTree($newCollection, $folders, $subsfolders, $folderFullName.$this->Account->Delimiter);	
				}
				
				if($newCollection->Count() > 0)
				{
					$folderObj->SubFolders = &$newCollection;
				}
			}
		}
	}

	/**
	 * @access private
	 * @param Array $uidList
	 * @param string $uid
	 * @return int
	 */
	function _getMessageIndexFromUid(&$uidList, $uid)
	{

		foreach($uidList as $id => $strUid)
		{
			if($strUid == $uid)
			{
				return $id;
			}
		}
		return -1;
	}

	/**
	 * @param Array $messageIndexSet
	 * @param bool $indexAsUid
	 * @param Folder $folder
	 * @param int $flags
	 * @param short $action
	 * @return bool
	 */
	function SetMessagesFlags(&$messageIndexSet, $indexAsUid, &$folder, $flags, $action)
	{

		if($this->_imapMail->select_mailbox($folder->FullName))
		{
			$flagsStr = '';
			if(($flags & MESSAGEFLAGS_Seen) == MESSAGEFLAGS_Seen)
			{
				$flagsStr .= ' \Seen';
			}
			if(($flags & MESSAGEFLAGS_Flagged) == MESSAGEFLAGS_Flagged)
			{
				$flagsStr .= ' \Flagged';
			}
			if(($flags & MESSAGEFLAGS_Deleted) == MESSAGEFLAGS_Deleted)
			{
				$flagsStr .= ' \Deleted';
			}
			if(($flags & MESSAGEFLAGS_Answered) == MESSAGEFLAGS_Answered)
			{
				$flagsStr .= ' \Answered';
			}
			switch($action)
			{
				case ACTION_Set:
					$actionName = '+FLAGS';
					break;
				case ACTION_Remove:
					$actionName = '-FLAGS';
					break;
			}
			if($messageIndexSet == null)
			{
				$messageIndexes = '1:*';
				$indexAsUid = false;
			} else
			{
				$messageIndexes = implode(',', $messageIndexSet);
			}
			if($indexAsUid)
			{
				return $this->_imapMail->uid_store_mail_flag($messageIndexes, $actionName, $flagsStr);
			} else
			{
				return $this->_imapMail->store_mail_flag($messageIndexes, $actionName, $flagsStr);
			}
		}
		return false;
	}

	/**
	 * @param Array $messageIndexSet
	 * @param bool $indexAsUid
	 * @param Folder $folder
	 * @return bool
	 */
	function DeleteMessages(&$messageIndexSet, $indexAsUid, &$folder)
	{
		return $this->SetMessagesFlags($messageIndexSet, $indexAsUid, $folder, MESSAGEFLAGS_Deleted, ACTION_Set);
	}
	
	/**
	 * @param Array $messageIndexSet
	 * @param bool $indexAsUid
	 * @param Folder $folder
	 * @param Folder $toFolder
	 * @return bool
	 */
	function MoveMessages(&$messageIndexSet, $indexAsUid, &$folder, &$toFolder)
	{
		return $this->CopyMessages($messageIndexSet, $indexAsUid, $folder, $toFolder) &
					$this->DeleteMessages($messageIndexSet, $indexAsUid, $folder) &
					$this->PurgeFolder($folder);
	}

	/**
	 * @param Folder $folder
	 * @return bool
	 */
	function PurgeFolder(&$folder)
	{
		return $this->_imapMail->select_mailbox($folder->FullName) && $this->_imapMail->expunge_mailbox();
	}
	
	/**
	 * @param Folder $folder
	 * @param Array $arrayUids
	 */
	function PurgeUidFolder(&$folder, $arrayUids)
	{
		if(is_array($arrayUids) && count($arrayUids) > 0)
		{
			$strUids = implode(',', $arrayUids);
			return $this->_imapMail->select_mailbox($folder->FullName) && $this->_imapMail->expunge_uid_mailbox($strUids);
		}
		
		return true;
	}
	
	/**
	 * @param Array $messageIndexSet
	 * @param bool $indexAsUid
	 * @param Folder $fromFolder
	 * @param Folder $toFolder
	 * @return bool
	 */
	function CopyMessages(&$messageIndexSet, $indexAsUid, &$fromFolder, &$toFolder)
	{
		$messageIndexes = implode(',', $messageIndexSet);
		if($this->_imapMail->select_mailbox($fromFolder->FullName))
		{
			if($indexAsUid)
			{
				return $this->_imapMail->uid_copy_mail($messageIndexes, $toFolder->FullName);
			} 
			else
			{
				return $this->_imapMail->copy_mail($messageIndexes, $toFolder->FullName);
			}
		}
		return false;
	}

	/**
	 * @param Folder $folder
	 * @return bool
	 */
	function GetFolderMessageCount(&$folder)
	{
		$countArray = $this->_imapMail->get_all_and_unnread_msg_count($folder->FullName);
		if($countArray == null)
		{
			return false;
		}
		$folder->MessageCount = $countArray[HKC_ALL_MSG];
		$folder->UnreadMessageCount = $countArray[HKC_UNSEEN_MSG];
		return true;
	}

	/**
	 * @access private
	 * @param WebMailMessage $message
	 * @param string $flags
	 */
	function _setMessageFlags(&$message, $flags)
	{
		$message->Flags = 0;
		if(strpos($flags, 'Seen'))
		{
			$message->Flags |= MESSAGEFLAGS_Seen;
		}
		if(strpos($flags, 'Answered'))
		{
			$message->Flags |= MESSAGEFLAGS_Answered;
		}
		if(strpos($flags, 'Flagged'))
		{
			$message->Flags |= MESSAGEFLAGS_Flagged;
		}
		if(strpos($flags, 'Deleted'))
		{
			$message->Flags |= MESSAGEFLAGS_Deleted;
		}
		if(strpos($flags, 'Draft'))
		{
			$message->Flags |= MESSAGEFLAGS_Draft;
		}
		if(strpos($flags, 'Recent'))
		{
			$message->Flags |= MESSAGEFLAGS_Recent;
		}
	}

	/**
	 * @param String $strFlags
	 * @return Integer
	 */
	function getIntFlags($flags)
	{
		$intFlags = 0;
		if(strpos($flags, 'Seen'))
		{
			$intFlags |= MESSAGEFLAGS_Seen;
		}
		if(strpos($flags, 'Answered'))
		{
			$intFlags |= MESSAGEFLAGS_Answered;
		}
		if(strpos($flags, 'Flagged'))
		{
			$intFlags |= MESSAGEFLAGS_Flagged;
		}
		if(strpos($flags, 'Deleted'))
		{
			$intFlags |= MESSAGEFLAGS_Deleted;
		}
		if(strpos($flags, 'Draft'))
		{
			$intFlags |= MESSAGEFLAGS_Draft;
		}
		if(strpos($flags, 'Recent'))
		{
			$intFlags |= MESSAGEFLAGS_Recent;
		}
		return $intFlags;
	}
}