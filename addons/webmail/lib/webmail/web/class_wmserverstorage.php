<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	require_once(WM_ROOTPATH.'wmserver/class_wmserverFS.php');
	require_once(WM_ROOTPATH.'class_mailstorage.php');
	require_once(WM_ROOTPATH.'class_account.php');
	require_once(WM_ROOTPATH.'class_webmailmessages.php');
	
	class WMserverStorage extends MailServerStorage
	{
		/**
		 * @access private
		 * @var XmailFS
		 */
		var $_wmserver;
		
		/**
		 * @access private
		 * @var CWmServerConsole
		 */
		var $_wmadmin;
		
		/**
		 * @access private
		 * @var CLog
		 */
		var $_log;
		
		/**
		 * @access public
		 * @var string
		 */
		var $DownloadedMessagesHandler = null;
		
		/**
		 * @access public
		 * @param Account $account
		 * @return XmailStorage
		 */
		function WMserverStorage(&$account)
		{
			MailServerStorage::MailServerStorage($account);
			
			$email = $account->Email;
			$basePath = $this->_settings->WmServerRootPath;
			
			$this->_wmserver = new WMserverFS($basePath, $email);
			$this->_wmadmin = new CWmServerConsole();
			$this->_log =& CLog::CreateInstance();
		}
		
		/**
		 * @param $arg[optional] = false
		 * @return bool
		 */
		function Connect($arg = false)
		{
			if($this->_wmadmin->_socket != false || !$arg)
			{
				return true;
			}

			if (!is_dir($this->_wmserver->basePath) || !$this->_wmadmin->Connect())
			{
				setGlobalError(ErrorPOP3Connect);
				return false;
			}
			else
			{
				register_shutdown_function(array(&$this, 'Disconnect'));
			}

			if (!$this->_wmadmin->UserConnect($this->Account))
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
			return $this->_wmadmin->Disconnect();
		}
		
		/**
		 * @access public
		 * 
		 * @param array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @return WebMailMessageCollection
		 */
		function &LoadMessages($messageIndexSet, $indexAsUid, &$folder)
		{
			$messageCollection = &new WebMailMessageCollection();
			$uids = $this->_wmserver->getAllMessagesNames();
			$idx = 0;
			
			if(!is_array($messageIndexSet))
			{
				$messageIndexSet = array($messageIndexSet);
			}
			
			foreach ($messageIndexSet as $index)
			{
				if ($idx < 0 || $idx > count($uids))
				{
					continue;
				}
				
				if ($this->DownloadedMessagesHandler != null)
				{
					call_user_func($this->DownloadedMessagesHandler);
				}
				
				$msgText = $this->_wmserver->getMessage($index);
				if (!$msgText)
				{
					continue;
				}
				
				$message = &new WebMailMessage();
				$message->LoadMessageFromRawBody($msgText, true);
				unset($msgText);
				$message->IdMsg = $idx;
				$message->Uid = $index;
				$message->Size = $this->_wmserver->getSizeMessage($index);
				
				$messageCollection->Add($message);
				
				$idx++;
			}
			
			return $messageCollection;
		}
		
		/**
		 * @access public
		 * 
		 * @param string $index
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @return WebMailMessageCollection
		 */
		function &LoadMessage(&$index, $indexAsUid, &$folder)
		{
			$message = null;
			
			//ShowDownloadedMessageNumber();
			$msgText = $this->_wmserver->getMessage($index);
				
			if (!$msgText)
			{
				return $message;
			}
				
			$message = &new WebMailMessage();
			$message->LoadMessageFromRawBody($msgText, true);
			unset($msgText);
			$message->IdMsg = $index;
			$message->Uid = $index;
			$message->Size = $this->_wmserver->getSizeMessage($index);
				
			return $message;
		}
		
		
		/**
		 * @access public
		 * 
		 * @param int $pageNumber
		 * @param Folder $folder
		 * @return WebMailMessageCollection
		 */
		function &LoadMessageHeaders($pageNumber, &$folder)
		{
			$messageIndexSet = array();
			$uids = &$this->_wmserver->getAllMessagesNames();
			$msgCount = count($uids);
			
	  		for ($i = $msgCount - ($pageNumber - 1) * $this->Account->MailsPerPage; $i >= $msgCount - $pageNumber * $this->Account->MailsPerPage; $i--)
	  		{
	  			if ($i == 0)
	  			{
	  				break;
	  			}
	  			
	  			if(isset($uids[$i-1]))
	  			{
	  				$messageIndexSet[] = $uids[$i-1];
	  			}
	  		}
	  		
	  		$messageCollection = $this->getHeaders($messageIndexSet, $folder);
	  		
	  		return $messageCollection;
		}
		
		/**
		 * @access public
		 *
		 * @param array $uids
		 * @param Folder $folder
		 * @return WebMailMessageCollection
		 */
		function getHeaders($uids, &$folder)
		{
			$messageCollection = &new WebMailMessageCollection();
			
			if(!is_array($uids))
			{
				$uids = array($uids);
			}
			
			$idx = 0;
			
			foreach ($uids as $uid)
			{	
				if ($this->DownloadedMessagesHandler != null)
				{
					@call_user_func($this->DownloadedMessagesHandler);
				}
				
				$msgText = $this->_wmserver->getHeader($uid);
				
				$message = &new WebMailMessage();
				$message->LoadMessageFromRawBody($msgText);
				$message->IdMsg = $idx;
				$message->Uid = $uid;
				$message->Size = $this->_wmserver->getSizeMessage($uid);
					
				if ($folder->SyncType == FOLDERSYNC_DirectMode)
				{
					$message->Flags |= MESSAGEFLAGS_Seen;
				}
	
				$messageCollection->Add($message);	
				
				$idx++;
			}	
					
			return $messageCollection;
		}
		
	
		/**
		 * @param FolderCollection $folders
		 * @return bool
		 */
		function Synchronize(&$folders)
		{
			$folderList = $folders->CreateFolderListFromTree(); //copy tree object here

			$inboxFolder = &$folderList->GetFolderByType(FOLDERTYPE_Inbox);
			if ($inboxFolder == null)
			{
				return true;
			}
			
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($this->Account);
			
			if (!$dbStorage->Connect())
			{
				return false;
			}

			return $this->_synchronizeFolderWithOpenDbConnection($inboxFolder, $dbStorage, -1);
		}
		
		/**
		 * @param Folder $folders
		 * @param DbStorage $dbStorage
		 * @return bool
		 */
		function _synchronizeFolderWithOpenDbConnection(&$folder, &$dbStorage, $lastIdMsg)
		{
			$result = true;
			
			if ($folder->SyncType == FOLDERSYNC_DontSync || 
				$folder->SyncType == FOLDERSYNC_DirectMode)
			{
				return true;
			}
			
			//get uids from pop3 server
			$serverUids = $this->_wmserver->getAllMessagesNames();
			
			//get uids from DB
			$dbUids = &$dbStorage->SelectReadsRecords();
			$dbUids = array_keys($dbUids);
			
			//get only new messages from wm server
			$newUids = array_diff($serverUids, $dbUids);
			
			//get deleted uids from wm server
			$uidsToDelete = array_diff($dbUids, $serverUids);
			
			//get size all messages in DB
			$mailBoxesSize = $dbStorage->SelectMailboxesSize();
			
			if ($this->DownloadedMessagesHandler != null)
			{
				ShowDownloadedMessageNumber($folder->Name, count($newUids));
				call_user_func($this->DownloadedMessagesHandler);
			}
			
			foreach ($newUids as $key => $newUid)
			{
				if ($this->Account->MailboxLimit > $mailBoxesSize + $this->_wmserver->getSizeMessage($newUid))
				{
					//Check sync mode
					if ($folder->SyncType == FOLDERSYNC_NewEntireMessages ||
						$folder->SyncType == FOLDERSYNC_AllEntireMessages)
					{
						//Entire Message
						$mailMessageCollection = &$this->LoadMessages($newUid, false, $folder);
					}
					elseif ($folder->SyncType == FOLDERSYNC_NewHeadersOnly ||
							$folder->SyncType == FOLDERSYNC_AllHeadersOnly)
					{
						//Entire Header
						$mailMessageCollection = &$this->getHeaders($newUid, $folder);
					}
					
					//Apply filters and save message in DB
					if (!$this->ApplyFilters($mailMessageCollection, $dbStorage, $folder))
					{
						$result = false;
						break;
					}	
					
					//Check mailmode to delete from server
					if($this->Account->MailMode == MAILMODE_DeleteMessagesFromServer)
					{
						//Delete received messages from server
						$this->_wmserver->deleteMessage($newUid);
					}
					
					//Save uid to reads table
					$dbStorage->InsertReadsRecords(array($newUid));	
				}
				else 
				{
					$result = false;
					setGlobalError(ErrorGetMailLimit);
					break;
				}	
			}
			
			if($folder->SyncType == FOLDERSYNC_AllHeadersOnly ||
			   $folder->SyncType == FOLDERSYNC_AllEntireMessages)
			{

				$index = $this->_getMessageIndexFromUid($uidsToDelete, $deleteUid);
				
				//delete from DB
				$result &= $dbStorage->DeleteMessages($uidsToDelete, true, $folder);
			}	
			
			if (($this->Account->MailMode == MAILMODE_KeepMessagesOnServer ||
					$this->Account->MailMode == MAILMODE_KeepMessagesOnServerAndDeleteMessageWhenItsRemovedFromTrash) &&
					$this->Account->MailsOnServerDays > 0)
			{
				$expiredUids = &$dbStorage->SelectExpiredMessageUids();
				
				$result &= $this->DeleteMessages($expiredUids, true, $folder);
			}
			
			if(!empty($uidsToDelete))
			{
				$result &= $dbStorage->DeleteReadsRecordsByUids($uidsToDelete);
			}
			
			$result &= $dbStorage->UpdateMailboxSize();
			
			return $result;
		}
	
		/**
		 * @access private
		 * 
		 * @param array $uidList
		 * @param string $uid
		 * @return int
		 */
		function _getMessageIndexFromUid(&$uidList, $uid)
		{
			if ($uidList != null)
			{
				foreach ($uidList as $id => $strUid)
				{
					if ($strUid == $uid)
					{
						return $id;
					}
				}
			}
			return -1;
		}
	
	/**
	 * @access public
	 *
	 * @return int
	 */
	function getSizeAllMessages()
	{
		return $this->_wmserver->getAllMessagesSize();
	}	
	
	/**
	 * @access public
	 *
	 * @return int
	 */
	function getAllMessagesCount()
	{
		return $this->_wmserver->getAllMessagesCount();
	}
	
	/**
	 * @access public
	 * 
	 * @param array $messageIndexSet
	 * @param bool $indexAsUid
	 * @param Folder $folder
	 * @return bool
	 */
	function DeleteMessages(&$messageIndexSet, $indexAsUid, &$folder)
	{
		$result = true;
		
		if ($this->DownloadedMessagesHandler != null)
		{
			ShowDeletingMessageNumber(true);
		}
			
		foreach ($messageIndexSet as $index)
		{
			if ($this->DownloadedMessagesHandler != null)
			{
				ShowDeletingMessageNumber();
			}
				
			$result &= $this->_wmserver->deleteMessage($index);
		}
		
		return $result;
	}	
	
}