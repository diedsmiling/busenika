<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	require_once(WM_ROOTPATH.'common/inc_constants.php');
	require_once(WM_ROOTPATH.'class_settings.php');
	
	if (!isset($_SESSION[ACCOUNT_ID]))
	{
		header('Location: index.php?error=1');
		exit();
	}
	
	$settings =& Settings::CreateInstance();
	if (!$settings || !$settings->isLoad)
	{
		header('Location: index.php?error=3');
		exit();		
	}

	if (!$settings->IncludeLang())
	{
		header('Location: index.php?error=6');
		exit();
	}
	
	require_once(WM_ROOTPATH.'classic/base_defines.php');
	require_once(WM_ROOTPATH.'class_mailprocessor.php');
	require_once(WM_ROOTPATH.'class_filesystem.php');
	
	class BaseProcessor
	{
		/**
		 * @var Account
		 */
		var $account = null;
		
		/**
		 * @var MailProcessor
		 */
		var $processor = null;
		
		/**
		 * @var DbStorage
		 */
		var $db = null;
		
		/**
		 * @var Settings
		 */
		var $settings;
		
		/**
		 * @var string;
		 */
		var $error = '';
		
		/**
		 * @var Array
		 */
		var $sArray;
		
		/**
		 * @var Folder
		 */
		var $currentFolder = null;
		
		/**
		 * @var FolderCollection
		 */
		var $folders = null;
		
		/**
		 * @var Array
		 */
		var $accounts;
		
		/**
		 * @var Account;
		 */
		var $editAccount = null;
		
		/**
		 * @return BaseProcessor
		 */
		function BaseProcessor()
		{
			if (!Session::has(ACCOUNT_ID))
			{
				$this->SetError(1);
			}
			
			$accountId = Session::val(ACCOUNT_ID);
			$this->sArray = Session::val(SARRAY, array());
			
			$this->settings =& Settings::CreateInstance();
			if (!$this->settings || !$this->settings->isLoad) $this->SetError(3);
			
			if ($accountId)
			{
				if (Get::has(CHANGE_ACCID))
				{
					$oldaccount = &Account::LoadFromDb(Session::val(ACCOUNT_ID, -1));
					$accountId = Get::val(CHANGE_ACCID);
					
					if (!isset($_SESSION['attachtempdir'])) $_SESSION['attachtempdir'] = md5(session_id());
					$fs = &new FileSystem(INI_DIR.'/temp', $oldaccount->Email, $oldaccount->Id);
					$attfolder = &new Folder($oldaccount->Id, -1, $_SESSION['attachtempdir']);
					$fs->DeleteDir($attfolder);
					unset($fs, $attfolder);					
					
					$this->sArray[ACCOUNT_ID] = $accountId;
					$this->account = &Account::LoadFromDb($accountId);
					
					if (!$this->account || $this->account->IdUser != $oldaccount->IdUser) 
					{
						$this->account = null;
					}
					else
					{
						$_SESSION[ACCOUNT_ID] = $accountId;
						unset($_SESSION[SARRAY]);
						$this->sArray = array();
					}
				}
				else 
				{
					$this->sArray[ACCOUNT_ID] = $accountId;
					$this->account = &Account::LoadFromDb($accountId);
				}
				
				if (!$this->account) 
				{
					$this->SetError(2);
				}
			}
			else 
			{
				$this->SetError(1);
			}

			if (!isset($this->sArray[ACCOUNT_ID]) || $this->sArray[ACCOUNT_ID] != $accountId)
			{
				$this->sArray[EDIT_ACCOUNT_ID] = $accountId;
			}
			
			$this->processor = &new MailProcessor($this->account);
			if (!$this->processor->DbStorage || !$this->processor->DbStorage->Connect()) $this->SetError(5);
			$this->db = &$this->processor->DbStorage;
			$this->accounts = &$this->GetAccounts();
			
			$skins = &FileSystem::GetSkinsList();
			$hasDefSettingsSkin = false;
			$normalSkin = false;
			
			foreach ($skins as $skinName)
			{
				if ($skinName == $this->settings->DefaultSkin)
				{
					$hasDefSettingsSkin = true;
				}
				
				if ($skinName == $this->account->DefaultSkin)
				{
					$normalSkin = true;
					break;
				}
			}
			
			if (!$normalSkin)
			{
				$this->account->DefaultSkin = ($hasDefSettingsSkin) 
						? $this->settings->DefaultSkin
						: $this->account->DefaultSkin = $skins[0];
			}
			
			$_SESSION[ATTACH_DIR] = Session::val(ATTACH_DIR, md5(session_id()));
			
			if (isset($this->sArray[SCREEN]))
			{
				$screen = Get::val(SCREEN, $this->sArray[SCREEN]);

				$this->sArray[SCREEN] = $screen;
				
				if ($this->account->AllowChangeSettings == false && 
						($screen == SET_ACCOUNT_PROF || $screen == SET_ACCOUNT_ADDACC))
				{
					$this->sArray[SCREEN] = SCREEN_MAILBOX;
				}

				if (!$this->settings->AllowContacts && $screen == SCREEN_CONTACTS)
				{
					$this->sArray[SCREEN] = SCREEN_MAILBOX;
				}
				
			} 
			else 
			{
				$this->sArray[SCREEN] = Get::val(SCREEN, SCREEN_MAILBOX);
			}
			
			if (isset($this->sArray[FOLDER_ID]))
			{
				$this->sArray[FOLDER_ID] = Get::val(FOLDER_ID, $this->sArray[FOLDER_ID]);
			} else $this->sArray[FOLDER_ID] = Get::val(FOLDER_ID, -1);
			
			if (Get::has(FOLDER_ID) || Get::has(SCREEN))
			{
				if (isset($this->sArray[SEARCH_ARRAY])) unset($this->sArray[SEARCH_ARRAY]);
			}
			
			if (Session::has(GOTOFOLDER))
			{
				$this->sArray[GOTOFOLDER] = Session::val(GOTOFOLDER, '');
				unset($_SESSION[GOTOFOLDER]);
			}
			
			if (isset($this->sArray[PAGE]))
			{
				$this->sArray[PAGE] = Get::val(PAGE, $this->sArray[PAGE]);
			} 
			else
			{
				$this->sArray[PAGE] = 1;
			}

			if (Get::has(S_GETMODE))
			{
				$this->sArray[SEARCH_ARRAY][S_TEXT] = (Get::val(S_GETMODE, 'mini') == 'mini') ?
						Post::val('smallLookFor', '') : Post::val('bigLookFor', '');
				
				if (!empty($this->sArray[SEARCH_ARRAY][S_TEXT]))
				{
					$this->sArray[SEARCH_ARRAY][S_FOLDER] = Post::val('qfolder', -2);
					$this->sArray[SEARCH_ARRAY][S_MODE] = Post::val('qmmode', 'onlyheaders');
					$this->sArray[FOLDER_ID] = $this->sArray[SEARCH_ARRAY][S_FOLDER];
					$this->sArray[PAGE] = 1;
				}
				else 
				{
					if (Post::val('qfolder', -2) < 1)
					{
						$this->sArray[FOLDER_ID] = -1;
					}		
								
					unset($this->sArray[SEARCH_ARRAY]);
					$this->sArray[PAGE] = 1;
				}
			}
			
			if (Get::has(S_GETMODECONTACT))
			{
				$this->sArray[SEARCH_ARRAY][S_TEXT] = (Get::val(S_GETMODECONTACT, 'mini') == 'mini') ?
						Post::val('smallLookFor', '') : Post::val('bigLookFor', '');
				
				$this->sArray[CONTACT_ID] = Post::val(CONTACT_ID, -1);
				$this->sArray[CONTACT_PAGE] = 1;
			}
			
			if (isset($this->sArray[SEARCH_ARRAY][S_FOLDER]))
			{
				$this->sArray[FOLDER_ID] = $this->sArray[SEARCH_ARRAY][S_FOLDER];
			}
			
			if (isset($this->sArray[EDIT_ACCOUNT_ID]))
			{
				$this->sArray[EDIT_ACCOUNT_ID] = Get::val(EDIT_ACCOUNT_ID, $this->sArray[EDIT_ACCOUNT_ID]);
			} else {
				$this->sArray[EDIT_ACCOUNT_ID] = $accountId;
			}
			if (Get::has(EDIT_ACCOUNT_ID))
			{
				$this->sArray[SCREEN] = ($this->sArray[SCREEN] == SET_ACCOUNT_ADDACC) ? SET_ACCOUNT_PROF : $this->sArray[SCREEN];
			}
			
			$this->sArray[CONTACT_PAGE] = (isset($this->sArray[CONTACT_PAGE])) ?
				Get::val(CONTACT_PAGE, $this->sArray[CONTACT_PAGE]) : Get::val(CONTACT_PAGE, 1);
			$this->sArray[CONTACT_ORD] = (isset($this->sArray[CONTACT_ORD])) ?
				Get::val(CONTACT_ORD, $this->sArray[CONTACT_ORD]) : Get::val(CONTACT_ORD, 0);
			
			if (isset($this->sArray[CONTACT_FLD]))
			{
				if (Get::val(CONTACT_FLD, $this->sArray[CONTACT_FLD]) != $this->sArray[CONTACT_FLD])
				{
					$this->sArray[CONTACT_ORD] = 0;
				}
				$this->sArray[CONTACT_FLD] = Get::val(CONTACT_FLD, $this->sArray[CONTACT_FLD]);
			}
			else 
			{
				$this->sArray[CONTACT_FLD] = Get::val(CONTACT_FLD, 0);
			}
			
			if (isset($_COOKIE['wm_vert_resizer']) || isset($_COOKIE['wm_horiz_resizer']) || isset($_COOKIE['wm_hide_folders']))
			{
				if (isset($_COOKIE['wm_vert_resizer']) && strlen($_COOKIE['wm_vert_resizer']) > 0)
				{
					$this->account->VertResizer = (int) $_COOKIE['wm_vert_resizer'];
					setcookie('wm_vert_resizer', '0', time() - (24*3600));
				}
				
				if (isset($_COOKIE['wm_horiz_resizer']) && strlen($_COOKIE['wm_horiz_resizer']) > 0)
				{
					$this->account->HorizResizer = (int) $_COOKIE['wm_horiz_resizer'];
					setcookie('wm_horiz_resizer', '0', time() -  (24*3600));
				}

				if (isset($_COOKIE['wm_hide_folders']) && strlen($_COOKIE['wm_hide_folders']) > 0)
				{
					$this->account->HideFolders = (bool) $_COOKIE['wm_hide_folders'];
					setcookie('wm_hide_folders', '0', time() - (24*3600));
				}
				
				$this->account->Update();				
			}
			
			$this->FillData();
			$this->UpdateSession();
		}
		
		/**
		 * @param string $text
		 */
		function SetError($errorCode)
		{
			header('location: '.LOGINFILE.'?error='.$errorCode);
			exit();
		}

		/**
		 * @return int
		 */
		function CountDefaultAccounts()
		{
			$return = 0;
			if (is_array($this->accounts) && count($this->accounts) > 0)
			{
				foreach ($this->accounts As $acct)
				{
					if ($acct && $acct[6] == true)
					{
						$return++;
					}
				}
			}
			return $return;
		}
		
		/**
		 * @return Folder
		 */
		function &GetCurrentFolder()
		{
			$folder = null;
			if ($this->currentFolder != null &&	$this->sArray[FOLDER_ID] == $this->currentFolder->IdDb)
			{
				return $this->currentFolder;
			}
			if ($this->sArray[FOLDER_ID] > -1)
			{
				$folderFullName = (isset($this->sArray[FOLDER_FULLNAME])) ? $this->sArray[FOLDER_FULLNAME] : 'temp_001';
				$folder = &new Folder($this->account->Id, $this->sArray[FOLDER_ID], $folderFullName);
				$this->processor->GetFolderInfo($folder);
				$this->currentFolder = &$folder;
			}
			return $folder;
		}
		
		/**
		 * @return array
		 */
		function &GetAccounts()
		{
			if (!$this->accounts)
			{
				$this->accounts = &$this->db->SelectAccounts($this->account->IdUser);
			}
			return $this->accounts;
		}
		
		function SetCurrentFolder()
		{
			$currentFolder = null;
			if (isset($this->sArray[GOTOFOLDER]))
			{
				$this->sArray[FOLDER_ID] = -1;
			}
			
			if ($this->sArray[FOLDER_ID] > -1)
			{
				$folderFullName = (isset($this->sArray[FOLDER_FULLNAME])) ? $this->sArray[FOLDER_FULLNAME] : 'temp_001';
				$folder = &new Folder($this->account->Id, $this->sArray[FOLDER_ID], $folderFullName);
				$this->processor->GetFolderInfo($folder);
				
				if ($folder && $folder->Hide)
				{
					$newfolder = ($this->folders) ? $this->folders->GetFirstNotHideFolder() : null;
					if ($newfolder)
					{
						$currentFolder =& $newfolder;
					}
				}
				else 
				{
					$currentFolder =& $folder;
				}
			}
			else 
			{
				if ($this->sArray[FOLDER_ID] == -1)
				{
					$folderType = FOLDERTYPE_Inbox;
					if (isset($this->sArray[GOTOFOLDER]))
					{
						$folderType = $this->sArray[GOTOFOLDER];
						unset($this->sArray[GOTOFOLDER]);
					}
					
					$folder = &$this->folders->GetFolderByType($folderType);
					$this->processor->GetFolderInfo($folder);
					
					if ($folder && $folder->Hide)
					{
						$newfolder = ($this->folders) ? $this->folders->GetFirstNotHideFolder() : null;
						if ($newfolder)
						{
							$currentFolder =& $newfolder;
						}
					}
					else 
					{
						$currentFolder =& $folder;
					}
				}
			}
			
			$this->currentFolder = &$currentFolder;
			if ($this->currentFolder) 
			{
				$this->processor->GetFolderMessageCount($this->currentFolder);
				$this->sArray[FOLDER_ID] = $this->currentFolder->IdDb;
			}
			else 
			{
				$this->sArray[FOLDER_ID] = -1;
			}
		}
		
		function SetFolders()
		{
			$this->folders = &$this->processor->GetFolders();
		}
		
		function FillData()
		{
			switch ($this->sArray[SCREEN])
			{
				case SCREEN_MAILBOX:
					$this->folders = &$this->GetFolders();
					$this->SetCurrentFolder();
					break;
				case SET_ACCOUNT_FILTERS:
				case SET_ACCOUNT_MFOLDERS:
				case SET_ACCOUNT_PROF:
				case SCREEN_SETTINGS:
					$this->folders = &$this->GetFolders();
					break;
				case SCREEN_CONTACTS:
					break;
			}
		}
		
		/**
		 * @return FolderCollection
		 */
		function &GetFolders()
		{
			if ($this->folders != null) 
			{
				return $this->folders;
			}
			$this->folders = &$this->processor->GetFolders();
			return $this->folders;
		}
		
		function UpdateSession()
		{
			$_SESSION[SARRAY] = $this->sArray;
			$_SESSION[ACCOUNT_ID] = $this->account->Id;
		}
	}