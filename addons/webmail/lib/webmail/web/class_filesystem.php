<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	require_once(WM_ROOTPATH.'common/class_log.php');
	require_once(WM_ROOTPATH.'mime/inc_constants.php');
	
	define('ACCOUNT_HIERARCHY_DEPTH', 1);
	
	class FileSystem
	{
		/**
		 * @var string
		 */
		var $RootFolder;
		
		/**
		 * @var string
		 */
		var $AccountName;
		
		/**
		 * @access private
		 * @var int
		 */
		var $_accountId;
		
		/**
		 * @param string $accountName
		 * @param string $rootFolder
		 * @return FileSystem
		 */
		function FileSystem($rootFolder, $accountName, $accountId)
		{
			$this->_accountId = $accountId;
			$this->AccountName = $accountName.'.'.$accountId;
			$this->RootFolder = $rootFolder;
		}
		
		/**
		 * @param long $idMsg
		 * @param Folder $folder
		 * @return WebMailMessage
		 */
		function &LoadMessage($idMsg, &$folder)
		{
			$path = $this->_createFolderFullPath($folder->FullName);
			$msg = &new WebMailMessage();
			if ($msg->LoadMessageFromEmlFile($path.'/'.$idMsg.'.eml', true))
			{
				$msg->IdMsg = $idMsg;
				return $msg;
			}
			else
			{
				$log =& CLog::CreateInstance();
				$log->WriteLine('Can\'t load file '.$path.'/'.$idMsg.'.eml');
			}
			$msg = null;
			return $msg;
		}
		
		/**
		 * @param WebMailMessage $msg
		 * @param Folder $folder
		 * @return bool
		 */
		function SaveMessage(&$msg, &$folder)
		{
			$log =& CLog::CreateInstance();
			$path = $this->_createFolderFullPath($folder->FullName);
			
			$log->WriteLine('Save file '.$path.'/'.$msg->IdMsg.'.eml');
			return $this->CreateFolder($folder) && $msg->SaveMessage($path.'/'.$msg->IdMsg.'.eml');
		}

		/**
		 * @param WebMailMessage $msg
		 * @param Folder $folder
		 * @return bool
		 */
		function UpdateMessage(&$msg, &$folder)
		{
			$log =& CLog::CreateInstance();
			$path = $this->_createFolderFullPath($folder->FullName);
			
			$log->WriteLine('Update file '.$path.'/'.$msg->IdMsg.'.eml');
			return $this->CreateFolder($folder) && $msg->SaveMessage($path.'/'.$msg->IdMsg.'.eml');
		}
		
		/**
		 * @param Array $messageIdSet
		 * @param Folder $fromFolder
		 * @param Folder $toFolder
		 * @return bool
		 */
		function MoveMessages(&$messageIdSet, &$fromFolder, &$toFolder)
		{
			$fromPath = $this->_createFolderFullPath($fromFolder->FullName);
			$toPath = $this->_createFolderFullPath($toFolder->FullName);
			
			$result = $this->CreateFolder($toFolder);
			
			foreach ($messageIdSet as $idMsg)
			{
				$result &= @rename($fromPath.'/'.$idMsg.'.eml', $toPath.'/'.$idMsg.'.eml');
			}
			
			return $result;
		}
		
		/**
		 * @param Array $messageIdSet
		 * @param Folder $folder
		 * @return bool
		 */
		function DeleteMessages(&$messageIdSet, &$folder)
		{
			$result = true;
			$path = $this->_createFolderFullPath($folder->FullName);

			foreach ($messageIdSet as $idMsg)
			{
				if (file_exists($path.'/'.$idMsg.'.eml'))
				{
					$result &= @unlink($path.'/'.$idMsg.'.eml');
				}
			}
			
			return $result;
		}

		/**
		 * @param Folder $folder
		 * @return bool
		 */
		function CreateFolder(&$folder)
		{
			$path = rtrim($this->_createFolderFullPath($folder->FullName), '/');
			if (is_dir($path))
			{
				return true;
			}
			
			return $this->_createRecursiveFolderPath($path);
		}
		
		/**
		 * @param Folder $folder
		 * @return string
		 */
		function GetFolderFullPath(&$folder)
		{
			return rtrim($this->_createFolderFullPath($folder->FullName), '/');
		}
		

		/**
		 * @param string $string
		 * @return bool
		 */
		function CreateFolderFromString($string)
		{
			$path = rtrim($this->_createFolderFullPath($string), '/');
			if (is_dir($path))
			{
				return true;
			}
			
			return $this->_createRecursiveFolderPath($path);
		}

		/**
		 * @access private
		 * @param string $path
		 * @return bool
		 */
		function _createRecursiveFolderPath($path)
		{
			$result = true;
			$rootFolder = substr($path, 0, strrpos($path, '/'));
			if (!is_dir($rootFolder))
			{
				$result &= $this->_createRecursiveFolderPath($rootFolder);
			}
			$result &= @mkdir($path);
			return $result;
		}
		
		/**
		 * @access private
		 * @param string $folder
		 * @return string
		 */
		function _createFolderFullPath($folder)
		{
			$returnPath = $this->RootFolder.'/';

			for ($i = 0; $i <= ACCOUNT_HIERARCHY_DEPTH - 1; $i++)
			{
				$returnPath .= $this->AccountName{$i}.'/';
			}

			$returnPath .= $this->AccountName.'/'.$folder;
			return rtrim($returnPath, '/\\');
		}
		
		/**
		 * @static 
		 * @return Array
		 */
		function &GetSkinsList()
		{
			$dirList = array();
			$dir = WM_ROOTPATH.'skins';
			if (is_dir($dir))
			{
				if ($dh = opendir($dir))
				{
					while (($file = readdir($dh)) !== false)
					{
						if (is_dir(WM_ROOTPATH.'skins/'.$file) && $file{0} != '.')
						{
							$dirList[] = $file; 
						}
					}
					closedir($dh);
				}
			}
			return $dirList;
		}
		
		/**
		 * @static 
		 * @return Array
		 */
		function &GetLangList()
		{
			$langList = array();
			$dir = WM_ROOTPATH.'lang';
			if (is_dir($dir))
			{
				if ($dh = opendir($dir))
				{
					while (($file = readdir($dh)) !== false)
					{
						if (is_file(WM_ROOTPATH.'lang/'.$file) && strpos($file, '.php') != false)
						{
							$lang = strtolower(substr($file, 0, -4));
							if ($lang != 'index' && $lang != 'default')
							{
								$langList[] = substr($file, 0, -4);
							}
						}
					}
					closedir($dh);
				}
			}
			return $langList;
		}

		/**
		 * @param Attachment $attach
		 * @param Folder $folder
		 * @param string $tempname
		 * @return bool
		 */
		function SaveAttach(&$attach, &$folder, $tempname)
		{
			$path = $this->_createFolderFullPath($folder->FullName);
			return $this->CreateFolder($folder) && $attach->SaveToFile($path.'/'.$tempname);
		}
		
		/**
		 * @param Folder $folder
		 * @param string $tempname
		 * @return string
		 */
		function LoadBinaryAttach(&$folder, $tempname)
		{
			$data = '';
			$filename = $this->_createFolderFullPath($folder->FullName).'/'.$tempname;
			$handle = @fopen($filename, 'rb');
			if ($handle)
			{
				while (!feof($handle))
				{
					$temp = fread($handle, 8192);
					if (!$temp) break;
					$data .= $temp;
				}
				fclose($handle);
				return $data;
			}
			return '';
			
		}
		
		/**
		 * @param Folder $folder
		 */
		function ClearDir(&$folder)
		{
			$path = $this->_createFolderFullPath($folder->FullName);

			if (is_dir($path))
			{
				if ($dh = opendir($path))
				{
					while (($file = readdir($dh)) !== false)
					{
						if ($file != '.' && $file != '..')
						{ 
							unlink($path.'/'.$file);
						} 
					}
					closedir($dh);
				}
			}
		}
		
		/**
		 * @param Folder $folder
		 */
		function DeleteDir(&$folder)
		{
			$path = $this->_createFolderFullPath($folder->FullName);
			$count = 0;
			
			if (is_dir($path))
			{
				if ($dh = opendir($path))
				{
					while (($file = readdir($dh)) !== false)
					{
						if ($file != '.' && $file != '..')
						{ 
							$count++;
						} 
					}
					closedir($dh);
				}
				if ($count) $this->ClearDir($folder);
				@rmdir($path);
			}
		}
		
		/**
		 * @param string $newAccountName
		 * @return bool
		 */
		function MoveFolders($newAccountName)
		{
			$log =& CLog::CreateInstance();
			$oldFolderPath = rtrim($this->_createFolderFullPath(''), '/');
			
			if (!is_dir($oldFolderPath)) return true;
			
			$fs = &new FileSystem($this->RootFolder, $newAccountName, $this->_accountId);
			
			$newFolderPath = rtrim($fs->_createFolderFullPath(''), '/');
			
			$rootFolder = substr($newFolderPath, 0, strrpos($newFolderPath, '/'));
			
			if (!is_dir($rootFolder))
			{
				$this->_createRecursiveFolderPath($rootFolder);
			}
			
			if (!@rename($oldFolderPath, $newFolderPath))
			{
				$log->WriteLine('Error Move Folder: '.$oldFolderPath.' => '.$newFolderPath);
				return false;
			}
			
			return true;
		}
		
		/**
		 * @param string $folderPath
		 * @return bool
		 */
		function IsFolderExist($folderPath)
		{
			$accountPath = rtrim($this->_createFolderFullPath(''), '/');
			
			return is_dir($accountPath.'/'.$folderPath);
		}

		/**
		 * @param string $folderPath
		 * @return bool
		 */
		function MoveSubFolders($oldFolderPath, $newFolderPath)
		{
			$oldFullPath = rtrim($this->_createFolderFullPath($oldFolderPath), '/');
			$newFullPath = rtrim($this->_createFolderFullPath($newFolderPath), '/');
			
			return rename($oldFullPath, $newFullPath);
		}

		/**
		 * @param string[optional] $subfolder
		 * @return bool
		 */
		function DeleteAccountDirs($subfolder = '')
		{
			$path = $this->_createFolderFullPath($subfolder);
			
			if (is_dir($path))
			{
				if ($dh = opendir($path))
				{
					while (($file = readdir($dh)) !== false)
					{
						if ($file != '.' && $file != '..')
						{ 
							if (is_dir($path.'/'.$file))
							{
								$this->DeleteAccountDirs($file);
							}
							else 
							{
								@unlink($path.'/'.$file);
							}
						} 
					}
					@closedir($dh);
				}
				@rmdir($path);
			}
			
			return true;
		}

		/**
		 * @param array $filesArray
		 * @param string[optional] $subfolder
		 * @return bool
		 */
		function DeleteTempFilesByArray($filesArray, $subfolder = '')
		{
			global $log;
			if (!is_array($filesArray) || count($filesArray) < 1)
			{
				return true;
			}
			
			$path = $this->_createFolderFullPath($subfolder);

			if (is_dir($path))
			{
				if ($dh = @opendir($path))
				{
					while (($file = readdir($dh)) !== false)
					{
						if ($file != '.' && $file != '..')
						{ 
							if (is_dir($path.'/'.$file))
							{
								$this->DeleteTempFilesByArray($filesArray, $file);
							}
							else 
							{
								if (in_array($file, $filesArray))
								{
									$log->WriteLine($path.'/'.$file);
									@unlink($path.'/'.$file);
								}
							}
						} 
					}
					@closedir($dh);
				}
			}
		
			return true;
		}

	}
