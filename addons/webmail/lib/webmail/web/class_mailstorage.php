<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	require_once(WM_ROOTPATH.'class_settings.php');
	require_once(WM_ROOTPATH.'common/class_log.php');
	
	define('ACTION_Remove', 0);
	define('ACTION_Set', 1);
	
	/**
	 * @abstract
	 */
	class MailStorage
	{

		/**
		 * @access protected
		 * @var Account
		 */
		var $Account;
	
		/**
		 * @access protected
		 * @var Settings
		 */
		var $_settings;
	
		/**
		 * @access protected
		 * @var CLog
		 */
		var $_log;
		
		/**
		 * @access protected
		 * @var resource
		 */
		var $_connectionHandle = null;
		
		/**
		 * @var string
		 */
		var $DownloadedMessagesHandler = null;
		
		/**
		 * @param Account $account
		 * @return MailStorage
		 */
		function MailStorage(&$account)
		{
			$this->_settings =& Settings::CreateInstance();
			$this->_log =& CLog::CreateInstance();
			$this->Account =& $account;
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
		
	}
	
	/**
	 * @abstract
	 */
	class MailServerStorage extends MailStorage
	{
		/**
		 * @param Account $account
		 * @return MailServerStorage
		 */
		function MailServerStorage(&$account)
		{
			MailStorage::MailStorage($account);
		}
	}
	
	/**
	 * @static
	 */
	class DbStorageCreator
	{
		/**
		 * @param Account $account
		 * @return MySqlStorage
		 */
		function &CreateDatabaseStorage(&$account)
		{
			$settings =& Settings::CreateInstance();
			
			/**
			 * @var DbStorage
			 */
			static $instance;
			
    		if (is_object($instance))
    		{
    			$instance->Account = &$account;
    			return $instance;
    		}
			
			require_once(WM_ROOTPATH.'class_dbstorage.php');
    		
			switch ($settings->DbType)
			{
				default:
				case DB_MSSQLSERVER:
					$instance = &new MsSqlStorage($account);
					break;
				case DB_MYSQL:
					$instance = &new MySqlStorage($account);
					break;
			}
    		
			return $instance;
		}
	}
