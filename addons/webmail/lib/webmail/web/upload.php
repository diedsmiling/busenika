<?php

	header('Content-type: text/html; charset=utf-8');
	
	$errorSymbols = array('<', '>');

	$Error_Desc = '';
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	require_once(WM_ROOTPATH.'class_settings.php');
	require_once(WM_ROOTPATH.'class_account.php');
	require_once(WM_ROOTPATH.'common/class_log.php');
	require_once(WM_ROOTPATH.'common/class_convertutils.php');
		
	@session_name('PHPWEBMAILSESSID');
	@session_start();

	if (!isset($_SESSION['attachtempdir'])) $_SESSION['attachtempdir'] = md5(session_id());
	
	$log =& CLog::CreateInstance();
	
	ob_start();
	
	$settings =& Settings::CreateInstance();
	$account =& Account::LoadFromDb($_SESSION[ACCOUNT_ID]);
	
	$fs = &new FileSystem(INI_DIR.'/temp', $account->Email, $_SESSION[ACCOUNT_ID]);
	$attfolder = &new Folder($_SESSION[ACCOUNT_ID], -1, $_SESSION['attachtempdir']);
	
	if (!$settings || !$settings->isLoad)
	{
		$Error_Desc = 'Can\'t Load Settings file';
	}
	elseif (!$settings->IncludeLang())
	{
		$Error_Desc = 'Can\'t Load Language file';
	}
	else
	{
		$Error_Desc = getGlobalError();	
	}

	if (empty($Error_Desc))
	{
		if (isset($_FILES['fileupload']) && is_uploaded_file($_FILES['fileupload']['tmp_name']))
		{
			if ($settings->EnableAttachmentSizeLimit && ($_FILES['fileupload']['size'] > $settings->AttachmentSizeLimit))
			{
				$Error_Desc = FileLargerAttachment;
			}
			else 
			{
				$tempname = basename($_FILES['fileupload']['tmp_name']);
				$filename = basename($_FILES['fileupload']['name']);
				
				$fs->CreateFolder($attfolder);
				$tempPath = $fs->GetFolderFullPath($attfolder).'/';
				$idx = '';
				
				while (file_exists($tempPath.$idx.$tempname))
				{
					$idx = ($idx === '') ? 1 : (int) $idx + 1;
				}
				
				$tempname = $idx.$tempname;

				if (!move_uploaded_file($_FILES['fileupload']['tmp_name'], $tempPath.$tempname))
				{
					switch ($_FILES['fileupload']['error'])
					{
						case 1:
							$Error_Desc = FileIsTooBig;
							break;
						case 2:
							$Error_Desc = FileIsTooBig;
							break;
						case 3:
							$Error_Desc = FilePartiallyUploaded;
							break;
						case 4:
							$Error_Desc = NoFileUploaded;
							break;
						case 6:
							$Error_Desc = MissingTempFolder;
							break;
						default:
							$Error_Desc = UnknownUploadError;
							break;
					}
				} 
				else
				{
					$filesize = @filesize($fs->GetFolderFullPath($attfolder).'/'.$tempname);
					if ($filesize === false)
					{
						$Error_Desc = MissingTempFile;	
					}
				}
			}
		}
		else 
		{
			$postsize = @ini_get('upload_max_filesize');
			$Error_Desc = ($postsize) ? FileLargerThan.$postsize
						: FileIsTooBig;
			if (isset($_FILES['fileupload']) && $_FILES['fileupload']['size'] > $settings->AttachmentSizeLimit)
			{
				$Error_Desc = FileIsTooBig;
			}
		}
	}
	
?>
	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<title></title>
</head>
<body>
<?php
if ($Error_Desc != '')
{
	$log->WriteLine($Error_Desc);
?>
<script language="JavaScript" type="text/javascript">
	alert("<?php echo ConvertUtils::ClearJavaScriptString($Error_Desc, '"');?>");
</script>
<?php
}
else 
{
	$mime = trim($_FILES['fileupload']['type']);
	if ($mime = 'application/octet-stream')
	{
		$mime = ConvertUtils::GetContentTypeFromFileName($filename);
	}
?>
<script language="JavaScript" type="text/javascript">
	parent.LoadAttachmentHandler({FileName: '<?php echo ConvertUtils::ClearJavaScriptString($filename, '\'');?>', TempName: '<?php echo ConvertUtils::ClearJavaScriptString($tempname,'\'');?>', Size: <?php echo (int) $filesize;?>, MimeType: '<?php echo ConvertUtils::ClearJavaScriptString($mime,'\'');?>'});
</script>
<?php
}
?>
</body>
</html>
<?php @ob_end_flush();