<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	require_once(WM_ROOTPATH.'libs/class_pop3.php');

	class Pop3Storage extends MailServerStorage
	{
		/**
		 * @access private
		 * @var CPOP3
		 */
		var $_pop3Mail;
		
		/**
		 * @access private
		 * @var Array
		 */
		var $_pop3Uids = null;
		
		/**
		 * @access private
		 * @var Array
		 */
		var $_pop3Sizes = null;
		
		/**
		 * @param Account $account
		 * @return Pop3Storage
		 */
		function Pop3Storage(&$account)
		{
			MailServerStorage::MailServerStorage($account);
			$this->_pop3Mail = &new CPOP3();
		}
		
		/**
		 * @param $arg[optional] = false
		 * @return bool
		 */
		function Connect($arg = false)
		{
			if ($this->_pop3Mail->socket != false)
			{
				return true;
			}
			
			if (!$this->_pop3Mail->connect($this->Account->MailIncHost, $this->Account->MailIncPort))
			{
				setGlobalError(ErrorPOP3Connect);
				return false;
			}
			else
			{
				register_shutdown_function(array(&$this, 'Disconnect'));
			}
			
			if (!$this->_pop3Mail->login($this->Account->MailIncLogin, $this->Account->MailIncPassword))
			{
				/*$err = getGlobalError();
				if (strlen($err) > 5 && strtolower(substr($err, 0, 4)) == '-err')
				{
					setGlobalError(trim(substr($err, 4)));
				}*/
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
			if ($this->_pop3Mail->socket == false)
			{
				return true;
			}
			return $this->_pop3Mail->close();
		}
		
		/**
		 * @param Folder $folders
		 * @return bool
		 */
		function SynchronizeFolder(&$folder)
		{
			if ($folder->Type != FOLDERTYPE_Inbox)
			{
				return true;
			}
			
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($this->Account);

			if (!$dbStorage->Connect())
			{
				return false;
			}
			
			//$lastIdMsg = $dbStorage->SelectLastIdMsg();
			
			return $this->_synchronizeFolderWithOpenDbConnection($folder, $dbStorage);
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
			
			return $this->_synchronizeFolderWithOpenDbConnection($inboxFolder, $dbStorage);
		}
		
		/**
		 * @param Folder $folders
		 * @param DbStorage $dbStorage
		 * @return bool
		 */
		function _synchronizeFolderWithOpenDbConnection(&$folder, &$dbStorage)
		{
			$result = true;
			
			if ($folder->SyncType == FOLDERSYNC_DontSync || 
				$folder->SyncType == FOLDERSYNC_DirectMode)
			{
				return true;
			}
			
			//get uids from pop3 server
			$pop3Uids = &$this->_getPop3Uids(true);
			
			//get uids from DB
			$dbUids = &$dbStorage->SelectReadsRecords();
			$dbUids = array_keys($dbUids);
			
			//get only new messages from pop3 server
			$newUids = array_diff($pop3Uids, $dbUids);
			
			//get deletd uids from pop3 server
			$uidsToDelete = array_diff($dbUids, $pop3Uids);
			
			//get Array sizes all messages on pop3 server
			$pop3Sizes = &$this->_getPop3Sizes(true);
			
			//get size all messages in DB
			$mailBoxesSize = $dbStorage->SelectMailboxesSize();
			
			if ($this->DownloadedMessagesHandler != null)
			{
				ShowDownloadedMessageNumber($folder->Name, count($newUids));
				call_user_func($this->DownloadedMessagesHandler);
			}
						
			foreach ($newUids as $newUid)
			{
				//get id message from uid pop3 server
				$index = $this->_getMessageIndexFromUid($pop3Uids, $newUid);
				
				$indexArray = array($index);
				
				$mailBoxesSize += $pop3Sizes[$index];
				
				if (!$this->_settings->EnableMailboxSizeLimit || $this->Account->MailboxLimit > $mailBoxesSize)
				{
					//Check sync mode
					if ($folder->SyncType == FOLDERSYNC_NewEntireMessages ||
						$folder->SyncType == FOLDERSYNC_AllEntireMessages)
					{
						//Entire Message
						$mailMessageCollection = &$this->LoadMessages($indexArray, false, $folder);
					}
					elseif ($folder->SyncType == FOLDERSYNC_NewHeadersOnly ||
							$folder->SyncType == FOLDERSYNC_AllHeadersOnly)
					{
						//Entire Header
						$mailMessageCollection = &$this->_loadMessageHeaders($indexArray, false, $folder);
					}
					
					//Apply filters and save message in DB
					if (!$this->ApplyFilters($mailMessageCollection, $dbStorage, $folder))
					{
						return false;
						//$result = false;
						//break;
					}	
					
					//Check mailmode to delete from server
					if($this->Account->MailMode == MAILMODE_DeleteMessagesFromServer)
					{
						//Delete received messages from server
						$this->_pop3Mail->delete_mail($index);
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
			
			//delete from DB
			if (count($uidsToDelete) > 0)
			{
				if($folder->SyncType == FOLDERSYNC_AllHeadersOnly ||
				   $folder->SyncType == FOLDERSYNC_AllEntireMessages)
				{
					$result &= $dbStorage->DeleteMessages($uidsToDelete, true, $folder);
				}	
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
		 * @param string $messageIndex
		 * @param bool $indexAsUid
		 * @return WebMailMessage
		 */
		function &LoadMessage($messageIndex, $indexAsUid)
		{
			$uids = &$this->_getPop3Uids();
			$message = null;

			if ($indexAsUid)
			{
				$idx = $this->_getMessageIndexFromUid($uids, $messageIndex);
			}
			else
			{
				$idx = $messageIndex;
			}

			if ($idx < 0 || $idx > count($uids))
			{
				setGlobalError(PROC_MSG_HAS_DELETED);
				return $message;
			}
			
			$msgText = $this->_pop3Mail->get_mail($idx);
			if (!$msgText)
			{
				return $message;
			}
			
			$message = &new WebMailMessage();
			$message->LoadMessageFromRawBody($msgText, true);
			$message->Uid = $uids[$idx];
			$size = &$this->_getPop3Sizes();
			$message->Size = $size[$idx];
			
			return $message;

		}

		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @return WebMailMessageCollection
		 */
		function &LoadMessages(&$messageIndexSet, $indexAsUid, &$folder)
		{
			$messageCollection = &new WebMailMessageCollection();
			$uids = &$this->_getPop3Uids();
			foreach ($messageIndexSet as $index)
			{
				if ($indexAsUid)
				{
					$idx = $this->_getMessageIndexFromUid($uids, $index);
				}
				else
				{
					$idx = $index;
				}

				if ($idx < 0 || $idx > count($uids))
				{
					continue;
				}
				
				if ($this->DownloadedMessagesHandler != null)
				{
					call_user_func($this->DownloadedMessagesHandler);
				}
				
				$msgText = $this->_pop3Mail->get_mail($idx);
				if (!$msgText)
				{
					continue;
				}
				
				$message = &new WebMailMessage();
				$message->LoadMessageFromRawBody($msgText, true);
				$message->Uid = $uids[$idx];
				$size = &$this->_getPop3Sizes();
				$message->Size = $size[$idx];
				
				$messageCollection->Add($message);
				
			}
			
			return $messageCollection;

		}

		/**
		 * @param int $pageNumber
		 * @param Folder $folder
		 * @return WebMailMessageCollection
		 */
		function &LoadMessageHeaders($pageNumber, &$folder)
		{
			$messageIndexSet = array();
			$uids = &$this->_getPop3Uids();
			$msgCount = count($uids);
			
	  		for ($i = $msgCount - ($pageNumber - 1) * $this->Account->MailsPerPage; $i >= $msgCount - $pageNumber * $this->Account->MailsPerPage; $i--)
	  		{
	  			if ($i == 0)
	  			{
	  				break;
	  			}
	  			
	  			$messageIndexSet[] = $i;
	  		}
	  		$messageCollection = &$this->_loadMessageHeaders($messageIndexSet, false, $folder);
	  		return $messageCollection;
		}
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @return bool
		 */
		function DeleteMessages(&$messageIndexSet, $indexAsUid, &$folder)
		{
			$result = true;
			$uids = &$this->_getPop3Uids(true);
			
			if ($this->DownloadedMessagesHandler != null)
			{
				ShowDeletingMessageNumber(true);
			}
			
			foreach ($messageIndexSet as $index)
			{
				if ($indexAsUid)
				{
					$idx = $this->_getMessageIndexFromUid($uids, $index);
				}
				else
				{
					$idx = $index;
				}

				if ($idx < 0 || $idx > count($uids))
				{
					continue;
				}
				
				if ($this->DownloadedMessagesHandler != null)
				{
					ShowDeletingMessageNumber();
				}
				
				$result &= $this->_pop3Mail->delete_mail($idx);
				
			}
			return $result;
		}
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @param Folder $toFolder
		 * @return bool
		 */
		function MoveMessages(&$messageIndexSet, $indexAsUid, &$fromFolder, &$toFolder)
		{
			return true;
		}
		
		/**
		 * @access private
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @return WebMailMessageCollection
		 */
		function &_loadMessageHeaders(&$messageIndexSet, $indexAsUid, &$folder)
		{
			$messageCollection = &new WebMailMessageCollection();
			$uids = &$this->_getPop3Uids();
			$size = &$this->_getPop3Sizes();
			
			foreach ($messageIndexSet as $index)
			{
				if ($indexAsUid)
				{
					$idx = $this->_getMessageIndexFromUid($uids, $index);
				}
				else
				{
					$idx = $index;
				}

				if ($idx < 0 || $idx > count($uids))
				{
					continue;
				}
				
				if ($this->DownloadedMessagesHandler != null)
				{
					call_user_func($this->DownloadedMessagesHandler);
				}	
				
				$msgText = $this->_pop3Mail->get_top($idx);
				if (!$msgText)
				{
					continue;
				}
				
				$message = &new WebMailMessage();
				$message->LoadMessageFromRawBody($msgText);
				$message->IdMsg = $idx;
				$message->Uid = $uids[$idx];
				$size = &$this->_getPop3Sizes();
				$message->Size = $size[$idx];
				
				if ($folder->SyncType == FOLDERSYNC_DirectMode)
				{
					$message->Flags |= MESSAGEFLAGS_Seen;
				}

				$messageCollection->Add($message);
				
			}
			return $messageCollection;
		}

		/**
		 * @param string $messageIndex
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @return WebMailMessage
		 */
		function &LoadMessageHeader($messageIndex, $indexAsUid, &$folder)
		{
			$uids = &$this->_getPop3Uids();

			if ($indexAsUid)
			{
				$idx = $this->_getMessageIndexFromUid($uids, $messageIndex);
			}
			else
			{
				$idx = $messageIndex;
			}

			if ($idx < 0 || $idx > count($uids))
			{
				return null;
			}
			
			$msgText = $this->_pop3Mail->get_top($idx);
			
			if (!$msgText)
			{
				return null;
			}
			
			$message = &new WebMailMessage();
			$message->LoadMessageFromRawBody($msgText);
			$message->IdMsg = $idx;
			$message->Uid = $uids[$idx];
			$size = &$this->_getPop3Sizes();
			$message->Size = $size[$idx];
			
			return $message;
		}
		
		
		/**
		 * @access private
		 * @return Array
		 */
		function &_getPop3Uids()
		{
			if (is_null($this->_pop3Uids))
			{
				$this->_pop3Uids = $this->_pop3Mail->uidl();
			}
			$uids = &$this->_pop3Uids;
			return $uids;
		}
		
		/**
		 * @access private
		 * @return Array
		 */
		function &_getPop3Sizes()
		{
			if (is_null($this->_pop3Sizes))
			{
				$this->_pop3Sizes = $this->_pop3Mail->msglist();
			}
			$size = &$this->_pop3Sizes;
			return $size;
		}

		/**
		 * @access private
		 * @param Array $uidList
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
		
	}