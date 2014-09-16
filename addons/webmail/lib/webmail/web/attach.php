<?php

	@error_reporting (E_ALL ^ E_NOTICE);

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	require_once(WM_ROOTPATH.'class_account.php');
	require_once(WM_ROOTPATH.'class_folders.php');
	require_once(WM_ROOTPATH.'class_mailprocessor.php');
	require_once(WM_ROOTPATH.'class_webmailmessages.php');
	
	@session_name('PHPWEBMAILSESSID');
	@session_start();
	
	function setContentLength($data) 
	{
		header('Content-Length: '.strlen($data));
		return $data;
	}
	
	@ob_start('setContentLength');

	if (!isset($_SESSION[ACCOUNT_ID]))
	{
		exit();
	}
	
	$account =& Account::LoadFromDb($_SESSION[ACCOUNT_ID]);
	if (!$account) exit();
	
	if (isset($_GET['msg_id'], $_GET['msg_uid'], $_GET['folder_id'], $_GET['folder_fname']))
	{
		$folder = &new Folder($_SESSION[ACCOUNT_ID], $_GET['folder_id'], $_GET['folder_fname']);
		
		$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
		
		if ($dbStorage->Connect())
		{
			$dbStorage->GetFolderInfo($folder);
		}
		
		$processor = &new MailProcessor($account);
		
		$message =& $processor->GetMessage($_GET['msg_id'], $_GET['msg_uid'], $folder);
		
		$data = $message->TryToGetOriginalMailMessage();
		$fileNameToSave = trim(ConvertUtils::ClearFileName($message->GetSubject()));
		if (empty($fileNameToSave))
		{
			$fileNameToSave = 'message';
		}
		
		// IE
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		
		header('Content-type: application/octet-stream; charset=utf-8');
		//header('Content-Type: application/force-download'); 
		header('Content-Type: application/download');
		header('Accept-Ranges: bytes');
		header('Content-Disposition: attachment; filename="'.$fileNameToSave.'.eml"');
		header('Content-Transfer-Encoding: binary');
	}
	elseif (isset($_SESSION[ACCOUNT_ID], $_GET['tn']))
	{
		$fs = &new FileSystem(INI_DIR.'/temp', $account->Email, $_SESSION[ACCOUNT_ID]);
		$folder = &new Folder($_SESSION[ACCOUNT_ID], -1, $_SESSION['attachtempdir']);
		$tempName = ConvertUtils::ClearFileName($_GET['tn']);
		
		$data = $fs->LoadBinaryAttach($folder, $tempName);
		
		if (isset($_GET['filename']))
		{
			$filename = ConvertUtils::ClearFileName(urldecode($_GET['filename']));
			$filename = ($filename) ? $filename : 'attachmentname';
			
			// IE
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		
			header('Content-Type: application/octet-stream; charset='.$account->GetUserCharset());
			header('Content-Type: application/download');
			header('Accept-Ranges: bytes');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Content-Transfer-Encoding: binary');
		}
		else 
		{
			header('Content-Type: '.ConvertUtils::GetContentTypeFromFileName($tempName));
		}
	}
	else
	{
		exit();
	}

	echo $data;
