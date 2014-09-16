<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	require_once(WM_ROOTPATH.'common/class_collectionbase.php');
	
	define('FOLDERTYPE_Inbox', 1);
	define('FOLDERTYPE_SentItems', 2);
	define('FOLDERTYPE_Drafts', 3);
	define('FOLDERTYPE_Trash', 4);
	define('FOLDERTYPE_Custom', 10);

	define('FOLDERSYNC_DontSync', 0);
	define('FOLDERSYNC_NewHeadersOnly', 1);
	define('FOLDERSYNC_AllHeadersOnly', 2);
	define('FOLDERSYNC_NewEntireMessages', 3);
	define('FOLDERSYNC_AllEntireMessages', 4);
	define('FOLDERSYNC_DirectMode', 5);
	
	define('FOLDERNAME_Inbox', 'Inbox');
	define('FOLDERNAME_SentItems', 'Sent Items');
	define('FOLDERNAME_Sent', 'Sent');
	define('FOLDERNAME_Drafts', 'Drafts');
	define('FOLDERNAME_Trash', 'Trash');
	
	class Folder
	{
		/**
		 * @var int
		 */
		var $IdDb;
		
		/**
		 * @var int
		 */
		var $IdAcct;
		
		/**
		 * @var int
		 */
		var $IdParent = -1;
		
		/**
		 * @var short
		 */
		var $Type;

		/**
		 * @var string
		 */
		var $Name;
		
		/**
		 * @var string
		 */
		var $FullName;
		
		/**
		 * @var short
		 */
		var $SyncType;
		
		/**
		 * @var bool
		 */
		var $Hide = false;
		
		/**
		 * @var int
		 */
		var $FolderOrder;
		
		/**
		 * @var int
		 */
		var $MessageCount = 0;

		/**
		 * @var int
		 */
		var $UnreadMessageCount = 0;
		
		/**
		 * @var int
		 */
		var $Size = 0;
		
		/**
		 * @var FolderCollection
		 */
		var $SubFolders = null;
		
		/**
		 * @var int
		 */
		var $Level;
		
		/**
		 * @var bool
		 */
		var $ToFolder = false;
		
		/**
		 * @param string $name
		 * @param string $fullName
		 * @param string $name optional
		 * @return Folder
		 */
		function Folder($idAcct, $idDb, $fullName, $name = null, $syncType = FOLDERSYNC_DontSync)
		{
			$this->IdAcct = $idAcct;
			$this->IdDb = $idDb;
			$this->FullName = $fullName;

			if ($name != null)
			{
				$this->Name = $name;
				
				$this->SyncType = $syncType;
				
				switch(strtolower($name))
				{
					case strtolower(FOLDERNAME_Inbox):
						$this->Type = FOLDERTYPE_Inbox;
						break;
					case strtolower(FOLDERNAME_Sent):
					case strtolower(FOLDERNAME_SentItems):
						$this->Type = FOLDERTYPE_SentItems;
						break;
					case strtolower(FOLDERNAME_Drafts):
						$this->Type = FOLDERTYPE_Drafts;
						break;
					case strtolower(FOLDERNAME_Trash):
						$this->Type = FOLDERTYPE_Trash;
						break;
					default:
						$this->Type = FOLDERTYPE_Custom;
				}
			}
			
		}
		
		/**
		 * @return string/bool
		 */
		function ValidateData()
		{
			if (empty($this->Name))
			{
				return JS_LANG_WarningEmptyFolderName;
			}
			elseif(!ConvertUtils::CheckDefaultWordsFileName($this->Name) || Validate::HasSpecSymbols($this->Name))
			{
				return WarningCorrectFolderName;
			}
			
			return true;	
		}
	}
	
	class FolderCollection extends CollectionBase
	{
		function FolderCollection()
		{
			CollectionBase::CollectionBase();
		}
		
		/**
		 * @param Folder $folder
		 */
		function Add(&$folder)
		{
			$this->List->Add($folder);
		}
		
		/**
		 * @param int $index
		 * @return Folder
		 */
		function &Get($index)
		{
			return $this->List->Get($index);
		}
		
		/**
		 * @param short $type
		 * @return Folder
		 */
		function &GetFolderByType($type)
		{
			$null = null;
			for ($i = 0, $c = $this->Count(); $i < $c; $i++)
			{
				$folder = &$this->Get($i);
				if ($folder->Type == $type)
				{
					return $folder;
				}
				if ($folder->Type == FOLDERTYPE_Inbox && $folder->SubFolders != null)
				{
					$inboxSub =& $folder->SubFolders->GetFolderByType($type);
					if ($inboxSub) return $inboxSub;
				}
			}

			return $null;
		}
		
		/**
		 * @param string $name
		 * @return Folder
		 */
		function &GetFolderByName($name)
		{
			$null = null;
			for ($i = 0, $c = $this->Count(); $i < $c; $i++)
			{
				$folder = &$this->Get($i);
				if ($folder->Name == $name)
				{
					return $folder;
				}
			}

			return $null;
		}
		
		/**
		 * @param short $type
		 * @return Folder
		 */
		function &GetFirstNotHideFolder()
		{
			$null = null;
			for ($i = 0, $c = $this->Count(); $i < $c; $i++)
			{
				$folder = &$this->Get($i);
				if (!$folder->Hide)
				{
					return $folder;
				}
			}

			return $null;
		}
		
		/**
		 * @param int $type
		 * @return Folder
		 */
		function &GetFolderById($id)
		{
			$folders = &$this->CreateFolderListFromTree();
			$null = null;
			for ($i = 0, $c = $folders->Count(); $i < $c; $i++)
			{
				$curfolder = &$folders->Get($i);
				if ($curfolder->IdDb === $id)
				{
					return $curfolder;
				}
			}

			return $null;
		}
		
		/**
		 * @return FolderCollection
		 */
		function &CreateFolderListFromTree()
		{
			$folderList = &new FolderCollection();
			$this->_createFolderListFromTree($folderList);
			return $folderList;
		}

		/**
		 * @access private
		 * @param FolderCollection $folderList
		 */
		function _createFolderListFromTree(&$folderList)
		{
			for ($i = 0, $c = $this->Count(); $i < $c; $i++)
			{
				$folder = &$this->Get($i);
				$folderList->Add($folder);
				if (!is_null($folder->SubFolders) && $folder->SubFolders->Count() > 0)
				{
					$folder->SubFolders->_createFolderListFromTree($folderList);
				}
			}
		}
		
		function SetSyncTypeToAll($syncType)
		{
			for ($i = 0, $c = $this->Count(); $i < $c; $i++)
			{
				$folder =& $this->Get($i);
				$folder->SyncType = $syncType;
				
				if (!is_null($folder->SubFolders) && $folder->SubFolders->Count() > 0)
				{
					$folder->SubFolders->SetSyncTypeToAll($syncType);
				}
			}			
		}
		
		/**
		 * @param Folder $folder
		 */
		function InitToFolder(&$folder)
		{
			$sent =& $this->GetFolderByType(FOLDERTYPE_SentItems);
			if ($sent)
			{
				if ($sent->IdDb == $folder->IdDb)
				{
					$folder->ToFolder = true;
					return;
				}
				else if ($sent->SubFolders && $sent->SubFolders->Count() > 0)
				{
					$sent->SubFolders->_setToFolderInSentDrafts($folder);
				}
			}
			
			$drafts =& $this->GetFolderByType(FOLDERTYPE_Drafts);
			if ($drafts)
			{
				if ($drafts->IdDb == $folder->IdDb)
				{
					$folder->ToFolder = true;
					return;
				}
				else if ($drafts->SubFolders && $drafts->SubFolders->Count() > 0)
				{
					$drafts->SubFolders->_setToFolderInSentDrafts($folder);
				}
			}
		}
		
		/**
		 * @param Folder $initFolder
		 */
		function _setToFolderInSentDrafts(&$initFolder)
		{
			for ($i = 0, $c = $this->Count(); $i < $c; $i++)
			{
				$folder =& $this->Get($i);
				if ($folder)
				{
					if ($initFolder->IdDb == $folder->IdDb)
					{
						$initFolder->ToFolder = true;
						return;
					}
					else if ($folder->SubFolders && $folder->SubFolders->Count() > 0)
					{
						$folder->SubFolders->_setToFolderInSentDrafts($initFolder);
					}
				}
			}
		}
		
		/**
		 * @return FolderCollection $folders
		 */
		function SortRootTree()
		{
			return $this->_sortFolderCollection(true);
		}
		
		/**
		 * @param bool $sortSpecialFolders[optional] = false
		 * @return FolderCollection
		 */
		function _sortFolderCollection($sortSpecialFolders = false)
		{
			$newFoldersArray = $topArray = $footArray = $tempArray = array();
			$newFolders = new FolderCollection();
			
			foreach ($this->Instance() as $folder)
			{
				if (strlen($folder->Name) > 0 && $folder->Name[0] == '&')
				{
					$footArray[] = $folder->Name;
				}
				else 
				{
					$topArray[] = $folder->Name;
				}
			}
			unset($folder);
			
			natcasesort($topArray); 
			
			foreach ($topArray as $value)
			{
				$newFoldersArray[strtolower($value)] = $value;
			}
			foreach ($footArray as $value)
			{
				$newFoldersArray[strtolower($value)] = $value;
			}
			unset($topArray, $footArray);

			if ($sortSpecialFolders)
			{
				if (isset($newFoldersArray[strtolower(FOLDERNAME_Inbox)]))
				{
					$folder =& $this->GetFolderByName($newFoldersArray[strtolower(FOLDERNAME_Inbox)]);
					if ($folder)
					{
						if ($folder->SubFolders && $folder->SubFolders->Count() > 1)
						{
							$folder->SubFolders = $folder->SubFolders->_sortFolderCollection(true);
						}
						$newFolders->Add($folder);
						unset($newFoldersArray[strtolower(FOLDERNAME_Inbox)]);
					}
				}
				
				if (isset($newFoldersArray[strtolower(FOLDERNAME_SentItems)]))
				{
					$folder =& $this->GetFolderByName($newFoldersArray[strtolower(FOLDERNAME_SentItems)]);
					if ($folder)
					{
						if ($folder->SubFolders && $folder->SubFolders->Count() > 1)
						{
							$folder->SubFolders = $folder->SubFolders->_sortFolderCollection();
						}
						$newFolders->Add($folder);
						unset($newFoldersArray[strtolower(FOLDERNAME_SentItems)]);
					}
				}
				
				if (isset($newFoldersArray[strtolower(FOLDERNAME_Sent)]))
				{
					$folder =& $this->GetFolderByName($newFoldersArray[strtolower(FOLDERNAME_Sent)]);
					if ($folder)
					{
						if ($folder->SubFolders && $folder->SubFolders->Count() > 1)
						{
							$folder->SubFolders = $folder->SubFolders->_sortFolderCollection();
						}
						$newFolders->Add($folder);
						unset($newFoldersArray[strtolower(FOLDERNAME_Sent)]);
					}
				}
				
				if (isset($newFoldersArray[strtolower(FOLDERNAME_Drafts)]))
				{
					$folder =& $this->GetFolderByName($newFoldersArray[strtolower(FOLDERNAME_Drafts)]);
					if ($folder)
					{
						if ($folder->SubFolders && $folder->SubFolders->Count() > 1)
						{
							$folder->SubFolders = $folder->SubFolders->_sortFolderCollection();
						}
						$newFolders->Add($folder);
						unset($newFoldersArray[strtolower(FOLDERNAME_Drafts)]);
					}
				}
				
				if (isset($newFoldersArray[strtolower(FOLDERNAME_Trash)]))
				{
					$folder =& $this->GetFolderByName($newFoldersArray[strtolower(FOLDERNAME_Trash)]);
					if ($folder)
					{
						if ($folder->SubFolders && $folder->SubFolders->Count() > 1)
						{
							$folder->SubFolders = $folder->SubFolders->_sortFolderCollection();
						}
						$newFolders->Add($folder);
						unset($newFoldersArray[strtolower(FOLDERNAME_Trash)]);
					}
				}
			}
			
			foreach ($newFoldersArray as $folderName)
			{
				$folder =& $this->GetFolderByName($folderName);
				if ($folder)
				{
					if ($folder->SubFolders && $folder->SubFolders->Count() > 1)
					{
						$folder->SubFolders = $folder->SubFolders->_sortFolderCollection();
					}
					$newFolders->Add($folder);
				}
			}
			
			return $newFolders;
		}
	}