<?php

	header('Content-type: text/html; charset=utf-8');
	
 	$Error_Desc = '';
	$ErrorInt = 1;
	$contactsCount = 0;
	$ContactArray = array();
		
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	require_once(WM_ROOTPATH.'class_settings.php');
	require_once(WM_ROOTPATH.'class_account.php');
	require_once(WM_ROOTPATH.'class_mailprocessor.php');
	require_once(WM_ROOTPATH.'class_contacts.php');
	require_once(WM_ROOTPATH.'class_validate.php');
	require_once(WM_ROOTPATH.'common/class_convertutils.php');
		
	session_name('PHPWEBMAILSESSID');
	session_start();
	
	if (!isset($_SESSION['attachtempdir'])) $_SESSION['attachtempdir'] = md5(session_id());
	
	ob_start();
	
	$account =& Account::LoadFromDb($_SESSION[ACCOUNT_ID]);
	$settings =& Settings::CreateInstance();
	
	$fs = &new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
	$attfolder = &new Folder($_SESSION[ACCOUNT_ID], -1, $_SESSION['attachtempdir']);
	
	if (!$settings || !$settings->isLoad)
	{
		$Error_Desc = 'Can\'t Load Settings file';
	}
	
	if (!$settings->IncludeLang())
	{
		$Error_Desc = 'Can\'t Load Language file';
	}
	
	if (empty($Error_Desc))
	{
		if (isset($_FILES['fileupload']))
		{
			$tempname = basename($_FILES['fileupload']['tmp_name']);
			//$filename = basename($_FILES['fileupload']['name']);
			
			$fs->CreateFolder($attfolder);
			if (!move_uploaded_file($_FILES['fileupload']['tmp_name'], $fs->GetFolderFullPath($attfolder).'/'.$tempname))
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
		else 
		{
			$postsize = @ini_get('upload_max_filesize');
			$Error_Desc = ($postsize) ? FileLargerThan.$postsize : FileIsTooBig;
			
		}
	
		if (empty($Error_Desc))
		{
			$isNullFile = true;
			$handle = @fopen($fs->GetFolderFullPath($attfolder).'/'.$tempname, 'rb');

			if (isset($filesize) && $filesize > 0)
			{
				$isNullFile = false;	
			}
			
			$getdelimiter = fread($handle, 20);
			rewind($handle);
			
			$pos1 = (int) strpos($getdelimiter, ',');
			$pos2 = (int) strpos($getdelimiter, ';');
			
			$delimiter = ($pos1 > $pos2) ? ',' : ';';
			
			$expArray = array(
						'e-mail address' 			=> 'HomeEmail',
						'e-mailaddress' 			=> 'HomeEmail',
						'emailaddress'	 			=> 'HomeEmail',
						'e-mail'		 			=> 'HomeEmail',
						'email'			 			=> 'HomeEmail',
						'notes' 					=> 'Notes',
						'homeaddress' 				=> 'HomeStreet',
						'home street' 				=> 'HomeStreet',
						'homestreet' 				=> 'HomeStreet',
						'home city' 				=> 'HomeCity',
						'homecity' 					=> 'HomeCity',
						'home postal code'			=> 'HomeZip',
						'zip'						=> 'HomeZip',
						'home state'				=> 'HomeState',
						'homestate'					=> 'HomeState',
						'home country/region'		=> 'HomeCountry',
						'home country'				=> 'HomeCountry',
						'homecountry'				=> 'HomeCountry',
						'home phone'				=> 'HomePhone',
						'homephone'					=> 'HomePhone',
						'home fax'					=> 'HomeFax',
						'homefax'					=> 'HomeFax',
						'mobile phone'				=> 'HomeMobile',
						'mobilephone'				=> 'HomeMobile',
						'personal web page'			=> 'HomeWeb',
						'personalwebpage'			=> 'HomeWeb',
						'web page'					=> 'HomeWeb',
						'webpage'					=> 'HomeWeb',
						'company'					=> 'BusinessCompany',
						'business street'			=> 'BusinessStreet',
						'businessstreet'			=> 'BusinessStreet',
						'business city'				=> 'BusinessCity',
						'businesscity'				=> 'BusinessCity',
						'business state'			=> 'BusinessState',
						'businessstate'				=> 'BusinessState',
						'business postal code'		=> 'BusinessZip',
						'business country/region'	=> 'BusinessCountry',
						'business country'			=> 'BusinessCountry',
						'job title'					=> 'BusinessJobTitle',
						'jobtitle'					=> 'BusinessJobTitle',
						'department'				=> 'BusinessDepartment',
						'office location'			=> 'BusinessOffice',
						'officelocation'			=> 'BusinessOffice',
						'business phone'			=> 'BusinessPhone',
						'businessphone'				=> 'BusinessPhone',
						'business fax'				=> 'BusinessFax',
						'businessfax'				=> 'BusinessFax',
						'business web page'			=> 'BusinessWeb',
						'businesswebpage'			=> 'BusinessWeb'
					);
			
			while (($data = fgetcsv($handle, 2000, $delimiter)) !== FALSE)
			{
				$num = count($data);
				$contactsCount++;
				
				if (count($data) < 2)
				{
					$contactsCount = ($contactsCount == 1) ? 0 : $contactsCount;
					continue;
				}
				
				if ($contactsCount === 1)
				{
					$headerArray = $data;
					continue;
				}
				
				if ($contactsCount > 1)
				{
					$newContact = new AddressBookRecord();
					$firstName = '';
					$lastName = '';
					$middleName = '';
					$nickName = '';
					$name = '';
					
					for ($c = 0; $c < $num; $c++)
					{
						if (strlen($data[$c]) == 0) 
						{
							continue;
						}
						$thisHeader = strtolower(trim($headerArray[$c]));

						if ($thisHeader ==  'first name' || $thisHeader ==  'firstname')
						{
							$firstName = $data[$c];
							continue;
						}
						if ($thisHeader ==  'last name' || $thisHeader ==  'lastname')
						{
							$lastName = $data[$c];
							continue;
						}
						if ($thisHeader ==  'middle name' || $thisHeader ==  'middlename')
						{
							$middleName = $data[$c];
							continue;
						}
						if ($thisHeader ==  'nickname')
						{
							$nickName = $data[$c];
							continue;
						}
						if ($thisHeader ==  'name')
						{
							$name = $data[$c];
							continue;
						}
						if ($thisHeader ==  'birthday')
						{
							$pos1 = (int) strrpos($data[$c], '.');
							$pos2 = (int) strrpos($data[$c], '/');
							
							$dateDelimiter = ($pos1 > $pos2) ? '.' : '/';							
							
							$timeArray = explode($dateDelimiter, $data[$c]);
							$cnt = count($timeArray);
							
							if ($cnt >= 3)
							{
								$Month = ((int) $timeArray[$cnt-3] > 0) ? (int) $timeArray[$cnt-3] : null;
								$Day = ((int) $timeArray[$cnt-2] > 0) ? (int) $timeArray[$cnt-2] : null;
								$Year = ((int) $timeArray[$cnt-1] > 0) ? (int) $timeArray[$cnt-1] : null;
								
								if ($Month > 12)
								{
									$temp1 = $Day;
									$Day = $Month;
									$Month = $temp1;
								}
								
								$lenYear = strlen($Year);
								if ($lenYear <= 2)
								{
									if ($Year && $lenYear == 1)
									{
										$Year = (int) '200'.$Year;
									}
									elseif ($Year)
									{
										if ($Year > (int) date('y', time()))
										{
											$Year = (int) '19'.$Year;
										}
										else 
										{
											$Year = (int) '20'.$Year;
										}
									}
								}
								$newContact->BirthdayMonth = $Month;
								$newContact->BirthdayDay = $Day;
								$newContact->BirthdayYear = $Year;
							}

							continue;
						}

						if (key_exists($thisHeader, $expArray))
						{
							$newContact->$expArray[$thisHeader] = trim($data[$c]);
						}
							
					}
					
					$firstName = ($firstName) ? $firstName : $name;
					$newContact->FullName = trim($firstName);
					$newContact->FullName .= ' '.trim($middleName);
					$newContact->FullName = trim($newContact->FullName).' '.trim($lastName);
					$newContact->FullName = ($nickName) ? trim($newContact->FullName).' ('.trim($nickName).')' : trim($newContact->FullName);
					$newContact->FullName = trim($newContact->FullName, ',');
					
					$newContact->IdUser = $account->IdUser;
										
					if($newContact->validateData() === true)
					{
						$ContactArray[] = $newContact;
					}	
				}
			}
			@fclose($handle);
		}
		else 
		{
			$ErrorInt = 0;
		}
	
		$contactsCount = count($ContactArray);
		if ($contactsCount > 0)
		{
			$insertResult = true;
			
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
			if ($dbStorage->Connect())
			{
				for ($i = 0; $i < $contactsCount; $i++)
				{
					$insertResult &= $dbStorage->InsertAddressBookRecord($ContactArray[$i]);
				}
				$dbStorage->Disconnect();
			}
			else 
			{
				$ErrorInt = 0;
			}
			
			if ($insertResult)
			{
				$_SESSION['action_report'] = JS_LANG_InfoHaveImported.' '.$contactsCount.' '.JS_LANG_InfoNewContacts;
			}
			else
			{
				$ErrorInt = 0;
			}
		}
		else 
		{
			if ($isNullFile)
			{
				$ErrorInt = ($ErrorInt == 1) ? 2 : $ErrorInt;
			}
			else 
			{
				$ErrorInt = 3;
			}
		}
	}
	else 
	{
		die('<script language="JavaScript" type="text/javascript">alert("'.ConvertUtils::ClearJavaScriptString(str_replace('"', '\\"', $Error_Desc), '"').'");</script>');
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<title></title>
</head>
<body>
	<script language="JavaScript" type="text/javascript">
		parent.ImportContactsHandler(<?php echo $ErrorInt;?>, <?php echo $contactsCount;?>);
	</script>
</body>
</html>
<?php @ob_end_flush();