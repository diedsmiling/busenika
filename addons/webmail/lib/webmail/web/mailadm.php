<?php

	header('Content-type: text/html; charset=utf-8');
	define('MES_SAVESUCCESSFUL', '<font color=green><b>Save successful!</b></font>');
	define('MES_SAVESUCCESSFULBUT', '<font color=green><b>Save successful, but the Admin password has not been changed because
the password and its confirmation don\'t match!</b></font>');
	define('MES_SAVESUCCESSFULBUT2', '<font color=green><b>Save successful, but the Mail Server Integration Admin password has
not been changed because the password and its confirmation don\'t match!</b></font>');
	define('MES_ERROR', '<font color=red><b>Error</b></font>');
	define('MES_LOGCLEARSUCCESSFUL', '<font color=green><b>Log clear successful!</b></font>');
	define('MES_CONNECTERROR', '<font color=red><b>Connect error!</b></font>');
	define('MES_DOMAINADDSUCCESSFUL', '<font color=green><b>Domain has been added!</b></font>');
	define('MES_DOMAINDELETESUCCESSFUL', '<font color=green><b>Domain has been deleted!</b></font>');
	
	@session_start();	
	
	$divMessage = '';
	if (isset($_SESSION['divmess']) && strlen($_SESSION['divmess']) > 0)
	{
		$divMessage = $_SESSION['divmess'];
		unset($_SESSION['divmess']);
	}
	
	function GetFriendlySize($byteSize)
	{
		$size = ceil($byteSize / 1024);
		$mbSize = $size / 1024;
		$size = ($mbSize > 1) ? (ceil($mbSize*10)/10).'MB' : $size.'KB';
		return $size;
	}
		
	function disable_magic_quotes_gpc()
	{
		if (@get_magic_quotes_gpc() == 1)
		{
			$_GET = array_map ('stripslashes' , $_GET);
			$_POST = array_map ('stripslashes' , $_POST);
		}
	}
	
	@disable_magic_quotes_gpc();
	
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	$isconfig = true;
	
	require_once(WM_ROOTPATH.'class_settings.php');
	require_once(WM_ROOTPATH.'common/class_convertutils.php');
	require_once(WM_ROOTPATH.'common/inc_constants.php');
	$settings = &Settings::CreateInstance();
	if(!$settings || !$settings->isLoad || !$settings->IncludeLang('English'))
	{
		$isconfig = false;
	}
	else 
	{
		require_once(WM_ROOTPATH.'class_filesystem.php');
		require_once(WM_ROOTPATH.'class_dbstorage.php');
		require_once(WM_ROOTPATH.'class_account.php');
		require_once(WM_ROOTPATH.'class_folders.php');
		require_once(WM_ROOTPATH.'wmserver/class_wmserver.php');
	}
	
	$mode = isset($_GET['mode']) ? $_GET['mode'] : 'login';
	$null = null;
	$navId = 3;
	$isCorrect = false;
	
	if (!$isconfig)
	{
		?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Cache-Control" content="private,max-age=1209600" />
	<title>WebMail probably not configured</title>
	<link rel="stylesheet" href="skins/Hotmail_Style/styles.css" type="text/css" />
</head>
<body>
<div align="center" id="content" class="wm_content">
	<div class="wm_logo" id="logo" tabindex="-1" onfocus="this.blur();">
		<span><?php echo StoreWebmail;?></span>
		<a class="header" target="_top" href="<?php echo !empty($_SESSION['cart_url']) ? $_SESSION['cart_url'] : '../../../../../admin.php'; ?>"><?php echo BackToCart;?></a>
	</div>
	<div class="wm_login_error">
		WebMail is not configured properly,<br />
		i.e. the path to the data folder (in&nbsp;inc_settings_path.php) is not correct<br />
		or<br />
		web service has no permission to write into the data folder and all its subfolders<br />
		or<br />
		settings file (data/settings/settings.xml) or language file (web/lang/English.php) doesn't exist<br />
		<br/>To learn how to grant the appropriate permission, please refer to WebMail documentation:<br/><br/><a href='help/installation_instructions_win.html'>Installation Instructions for Windows</a><br/>
		<a href='help/installation_instructions_unix.html'>Installation Instructions for Unix</a>
	</div>
	<div class="wm_copyright" id="copyright">
		<?php @require('inc.footer.php'); ?>
	</div>
<div>
<?php die(); 
	}
	
	$skins = &FileSystem::GetSkinsList();
	$deff = '';
	
	foreach ($skins as $skinName)
	{
		if ($skinName == $settings->DefaultSkin)
		{
			$deff = $settings->DefaultSkin;
			break;
		}
	}
	if ($deff == '')
	{
		$deff = (count($skins) > 0) ? $skins[0] : 'Hotmail_Style';
	}
	
	$skinPath = './skins/'.$deff;
	$ref = '';
	
	if ($mode == 'enter')
	{
		if (isset($_POST['login']) && isset($_POST['password']) &&
					strtolower($_POST['login']) == MAILADMLOGIN &&
					$_POST['password'] == $settings->AdminPassword)
		{
			$_SESSION['passwordIsCorrect'] = 15;	
			$isCorrect = true;
			$mode = 'wm_settings';
		}
		else 
		{
			$mode = 'login';
			$_GET['error'] = 3;
		}
	} 
		
	$isCorrect = (isset($_SESSION['passwordIsCorrect']) && (int) $_SESSION['passwordIsCorrect'] == 15);
	
	if ($isCorrect)
	{
		if ($mode == 'clearlog')
		{
			if (file_exists(INI_DIR.'/'.LOG_PATH.'/'.LOG_FILENAME))
			{
				$_SESSION['divmess'] = (@unlink(INI_DIR.'/'.LOG_PATH.'/'.LOG_FILENAME))
											? MES_LOGCLEARSUCCESSFUL
											: MES_ERROR;
			}
			else
			{
				$_SESSION['divmess'] = MES_LOGCLEARSUCCESSFUL;
			}
		
			$ref = 'mailadm.php?mode=wm_debug';
		}
		
		if ($mode == 'wm_delete')
		{
			if (isset($_GET['uid']) && $_GET['uid'] > -1)
			{
				$account  = &Account::LoadFromDb($_GET['uid']);
				$account->DeleteFromDb($_GET['uid'], true);
				
				$fs = new FileSystem(INI_DIR.'/mail', $account->Email, $account->Id);
				$fs->DeleteAccountDirs();
							
				$fs2 = new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
				$fs2->DeleteAccountDirs();
				unset($fs, $fs2);
			}
			
			$ref = 'mailadm.php?mode=wm_users';
		}
	
		if ($mode == 'save')
		{
			$ref_mode = '';
			$form_id = isset($_POST['form_id']) ? $_POST['form_id'] : 'error';
			switch ($form_id)
			{
				case 'error' : 
					$mode = 'login';
					break;
					
				case 'db' : 
					$settings->DbType = isset($_POST['intDbType']) ? (int) $_POST['intDbType'] : -1;

					$settings->DbHost = isset($_POST['txtSqlSrc']) ? $_POST['txtSqlSrc'] : $settings->DbHost;
					$settings->DbLogin = isset($_POST['txtSqlLogin']) ? $_POST['txtSqlLogin'] : $settings->DbLogin;
					$settings->DbPassword = isset($_POST['txtSqlPassword']) ? $_POST['txtSqlPassword'] : $settings->DbPassword;
					$settings->DbName = isset($_POST['txtSqlName']) ? $_POST['txtSqlName'] : $settings->DbName;
					$settings->DbDsn = isset($_POST['txtSqlDsn']) ? $_POST['txtSqlDsn'] : $settings->DbDsn;
					
					$settings->DbCustomConnectionString = isset($_POST['odbcConnectionString']) ? $_POST['odbcConnectionString'] : $settings->DbCustomConnectionString;
					
					$settings->UseCustomConnectionString = (isset($_POST['useCS']) && (int) $_POST['useCS'] == 1);
					
					$_SESSION['divmess'] = ($settings->SaveToXml()) ? MES_SAVESUCCESSFUL : MES_ERROR.getError();
	
					$ref = 'mailadm.php?mode=wm_db';
					break;

				case 'calendar' : 
				
					$settings->Cal_DefaultTimeFormat = isset($_POST['defTimeFormat']) ? $_POST['defTimeFormat'] : $settings->Cal_DefaultTimeFormat;
					$settings->Cal_DefaultDateFormat = isset($_POST['defDateFormat']) ? $_POST['defDateFormat'] : $settings->Cal_DefaultDateFormat;
					$settings->Cal_ShowWeekends = ((isset($_POST['showWeekends']) && (int) $_POST['showWeekends'] == 1) ? 1 : 0);
					$settings->Cal_WorkdayStarts = isset($_POST['WorkdayStarts']) ? $_POST['WorkdayStarts'] : $settings->Cal_WorkdayStarts;																
					$settings->Cal_WorkdayEnds = isset($_POST['WorkdayEnds']) ? $_POST['WorkdayEnds'] : $settings->Cal_WorkdayEnds;	
					$settings->Cal_ShowWorkDay = ((isset($_POST['showWorkDay']) && (int) $_POST['showWorkDay'] == 1) ? 1 : 0);
					$settings->Cal_WeekStartsOn = isset($_POST['weekStartsOn']) ? $_POST['weekStartsOn'] : $settings->Cal_WeekStartsOn;
					$settings->Cal_DefaultTab = isset($_POST['defTab']) ? $_POST['defTab'] : $settings->Cal_DefaultTab;
					$settings->Cal_DefaultCountry = isset($_POST['defCountry']) ? $_POST['defCountry'] : $settings->Cal_DefaultCountry;
					$settings->Cal_DefaultTimeZone = isset($_POST['defTimeZone']) ? $_POST['defTimeZone'] : $settings->Cal_DefaultTimeZone;																
					$settings->Cal_AllTimeZones = ((isset($_POST['allTimeZones']) && (int) $_POST['allTimeZones'] == 1)? 1 : 0);
				
					$_SESSION['divmess'] = ($settings->SaveToXml()) ? MES_SAVESUCCESSFUL : MES_ERROR.getError();
	
					$ref = 'mailadm.php?mode=wm_cal';
					break;
					
				case 'settings' :
					$settings->WindowTitle = isset($_POST['txtSiteName']) ? $_POST['txtSiteName'] : $settings->WindowTitle;
					$settings->LicenseKey = isset($_POST['txtLicenseKey']) ? $_POST['txtLicenseKey'] : $settings->LicenseKey;
					$settings->IncomingMailProtocol = isset($_POST['intIncomingMailProtocol']) ? (int) $_POST['intIncomingMailProtocol'] : $settings->IncomingMailProtocol;
					$settings->IncomingMailServer = isset($_POST['txtIncomingMail']) ? $_POST['txtIncomingMail'] : $settings->IncomingMailServer;
					$settings->IncomingMailPort = isset($_POST['intIncomingMailPort']) ? $_POST['intIncomingMailPort'] : $settings->IncomingMailPort;
					$settings->OutgoingMailServer = isset($_POST['txtOutgoingMail']) ? $_POST['txtOutgoingMail'] : $settings->OutgoingMailServer;
					$settings->OutgoingMailPort = isset($_POST['intOutgoingMailPort']) ? $_POST['intOutgoingMailPort'] : $settings->OutgoingMailPort;
					
					$settings->ReqSmtpAuth = (isset($_POST['intReqSmtpAuthentication']) && (int) $_POST['intReqSmtpAuthentication'] == 1);
					$settings->AllowDirectMode = (isset($_POST['intAllowDirectMode']) && (int) $_POST['intAllowDirectMode'] == 1);
					$settings->DirectModeIsDefault = (isset($_POST['intDirectModeIsDefault']) && (int) $_POST['intDirectModeIsDefault']);
	
					$settings->AttachmentSizeLimit = isset($_POST['intAttachmentSizeLimit']) ? abs((int) $_POST['intAttachmentSizeLimit']) : $settings->AttachmentSizeLimit;
					$settings->MailboxSizeLimit = isset($_POST['intMailboxSizeLimit']) ? abs((int) $_POST['intMailboxSizeLimit']) : $settings->MailboxSizeLimit;

					$settings->EnableAttachmentSizeLimit = (isset($_POST['intEnableAttachSizeLimit']) && $_POST['intEnableAttachSizeLimit'] == '1');
					$settings->EnableMailboxSizeLimit = (isset($_POST['intEnableMailboxSizeLimit']) && $_POST['intEnableMailboxSizeLimit'] == '1');
					
					$settings->AllowUsersChangeEmailSettings = (isset($_POST['intAllowUsersChangeEmailSettings']) && (int) $_POST['intAllowUsersChangeEmailSettings'] == 1);
					$settings->AllowNewUsersRegister = (isset($_POST['intAllowNewUsersRegister']) && (int) $_POST['intAllowNewUsersRegister'] == 1);
					$settings->AllowUsersAddNewAccounts = (isset($_POST['intAllowUsersAddNewAccounts']) && (int) $_POST['intAllowUsersAddNewAccounts'] == 1);
					
					$settings->DefaultUserCharset = isset($_POST['txtDefaultUserCharset']) ? $_POST['txtDefaultUserCharset'] : $settings->DefaultUserCharset;
					$settings->AllowUsersChangeCharset = (isset($_POST['intAllowUsersChangeCharset']) && (int) $_POST['intAllowUsersChangeCharset'] == 1);
					
					$settings->DefaultTimeZone = isset($_POST['txtDefaultTimeZone']) ? (int) $_POST['txtDefaultTimeZone'] : $settings->DefaultTimeZone;
					$settings->AllowUsersChangeTimeZone = (isset($_POST['intAllowUsersChangeTimeZone']) && (int) $_POST['intAllowUsersChangeTimeZone']);
					
					$temp = MES_SAVESUCCESSFUL;
					if (isset($_POST['txtPassword1']) && isset($_POST['txtPassword2']) && $_POST['txtPassword1'] != '***') 
					{
						if ($_POST['txtPassword1'] === $_POST['txtPassword2'])
						{
							$settings->AdminPassword = $_POST['txtPassword1'];	
						}
						else 
						{
							$temp = MES_SAVESUCCESSFULBUT;
						}
					}				
					
					$_SESSION['divmess'] = ($settings->SaveToXml()) ? $temp : MES_ERROR.getError();
					$ref = 'mailadm.php?mode=wm_settings';
					break;
					
				case 'interface' :
					$settings->MailsPerPage = isset($_POST['intMailsPerPage']) ? (int) $_POST['intMailsPerPage'] : $settings->MailsPerPage;
					if ($settings->MailsPerPage < 1) $settings->MailsPerPage = 1;
					$settings->DefaultSkin = isset($_POST['txtDefaultSkin']) ? $_POST['txtDefaultSkin'] : $settings->DefaultSkin;
					$settings->DefaultLanguage = isset($_POST['txtDefaultLanguage']) ? $_POST['txtDefaultLanguage'] : $settings->DefaultLanguage;
					
					$settings->AllowUsersChangeSkin = (isset($_POST['intAllowUsersChangeSkin']) && (int) $_POST['intAllowUsersChangeSkin'] == 1);
					$settings->AllowUsersChangeLanguage = (isset($_POST['intAllowUsersChangeLanguage']) && (int) $_POST['intAllowUsersChangeLanguage'] == 1);
					$settings->ShowTextLabels = (isset($_POST['intShowTextLabels']) && (int) $_POST['intShowTextLabels'] == 1);
					$settings->AllowDhtmlEditor = (isset($_POST['intAllowDHTMLEditor']) && (int) $_POST['intAllowDHTMLEditor'] == 1);				
					$settings->AllowAjax = (isset($_POST['intAllowAjaxVeersion']) && (int) $_POST['intAllowAjaxVeersion'] == 1);				
					
					$settings->AllowContacts = (isset($_POST['intAllowContacts']) && (int) $_POST['intAllowContacts'] == 1);				
					$settings->AllowCalendar = (isset($_POST['intAllowCalendar']) && (int) $_POST['intAllowCalendar'] == 1);				
					
					$_SESSION['divmess'] = ($settings->SaveToXml()) ? MES_SAVESUCCESSFUL : MES_ERROR.getError();
					$ref = 'mailadm.php?mode=wm_interface';
					break;
					
				case 'debug' :
					$settings->EnableLogging = (isset($_POST['intEnableLogging']) && (int) $_POST['intEnableLogging'] == 1);
					$settings->DisableErrorHandling = (isset($_POST['intDisableErrorHandling']) && (int) $_POST['intDisableErrorHandling'] == 1);
					
					$_SESSION['divmess'] = ($settings->SaveToXml()) ? MES_SAVESUCCESSFUL : MES_ERROR.getError();
					$ref = 'mailadm.php?mode=wm_debug';
					break;
				
				case 'wmserver' :
					
					$ref = 'mailadm.php?mode=wm_server';
					$isError = false;
					
					$settings->EnableWmServer = (isset($_POST['intEnableMwServer']) && (int) $_POST['intEnableMwServer'] == 1);
					if (!$settings->EnableWmServer && $settings->IncomingMailProtocol == MAILPROTOCOL_WMSERVER)
					{
						$settings->IncomingMailProtocol = MAILPROTOCOL_POP3;
					}
					
					$settings->WmAllowManageXMailAccounts = (isset($_POST['intWmAllowManageXMailAccounts']) && (int) $_POST['intWmAllowManageXMailAccounts'] == 1);
					
					$settings->WmServerRootPath = isset($_POST['txtWmServerRootPath']) ? $_POST['txtWmServerRootPath'] : $settings->WmServerRootPath;
					$settings->WmServerHost = isset($_POST['txtWmServerHostName']) ? $_POST['txtWmServerHostName'] : $settings->WmServerHost;
					
					if ($settings->EnableWmServer)
					{
						if (isset($_POST['txtWmServerRootPath']))
						{
							$RootPath = str_replace('\\', '/', rtrim(trim($_POST['txtWmServerRootPath']), '\\/'));
							if (strlen($RootPath) > 2)
							{
								if (!is_dir($RootPath.'/domains'))
								{
									$isError = true;
									setGlobalError('Server Root Path '.$RootPath.' incorrect');
								}
								if (!file_exists($RootPath.'/wm.tab'))
								{
									$isError = true;
									setGlobalError('Can\'t find '.$RootPath.'/wm.tab');
								}
							}
							else 
							{
								$isError = true;
								setGlobalError('Server Root Path not set!');
							}
						}
						else 
						{
							$isError = true;
							setGlobalError('Server Root Path not set!');							
						}
						
						if (!$isError)
						{						
							$WMServer = new CWmServerConsole();
							$req = $WMServer->Connect();
							
							if ($req !== true)
							{
								$isError = true;
								setGlobalError('Connect unsuccessful! '.str_replace('"', '\'', str_replace(array("\r", "\t", "\n"), '', $WMServer->GetError())));
							}
						}
					}

					if ($isError)
					{
						$_SESSION['divmess'] = MES_ERROR.getError();
					}
					else 
					{
						$_SESSION['divmess'] = ($settings->SaveToXml()) ? MES_SAVESUCCESSFUL : MES_ERROR.getError();
					}
					
					break;
					
				case 'login' :
					$settings->AllowAdvancedLogin = (isset($_POST['intAllowAdvancedLogin']) && (int) $_POST['intAllowAdvancedLogin'] == 1);
					$settings->AutomaticCorrectLoginSettings = (isset($_POST['intAutomaticHideLogin']) && (int) $_POST['intAutomaticHideLogin'] == 1);
					
					$settings->DefaultDomainOptional = isset($_POST['txtUseDomain']) ? $_POST['txtUseDomain'] : $settings->DefaultDomainOptional;
					$hideLoginMode = 0;
	
					if (isset($_POST['hideLoginRadionButton']))
					{
						switch ($_POST['hideLoginRadionButton'])
						{
							case '0': break;
							case '1':
								$hideLoginMode = 10;
								if (isset($_POST['hideLoginSelect']) && $_POST['hideLoginSelect'] == '1') $hideLoginMode++;
								break;
							case '2':
								$hideLoginMode = 20;
								if (isset($_POST['intDisplayDomainAfterLoginField']) && (int) $_POST['intDisplayDomainAfterLoginField'] == 1) $hideLoginMode++;
								if (isset($_POST['intLoginAsConcatination']) && (int) $_POST['intLoginAsConcatination'] == 1) $hideLoginMode = $hideLoginMode + 2;
								break;
						}
					}
				
					$settings->HideLoginMode = $hideLoginMode;
					
					$_SESSION['divmess'] = ($settings->SaveToXml()) ? MES_SAVESUCCESSFUL : MES_ERROR.getError();
					$ref = 'mailadm.php?mode=wm_domain';
					break;
					
				case 'account' :
					$settings->EnableLogging = (isset($_POST['intEnableLogging']) && (int) $_POST['intEnableLogging'] == 1) ? true : false;
					$settings->DisableErrorHandling = (isset($_POST['intDisableErrorHandling']) && (int) $_POST['intDisableErrorHandling'] == 1) ? true : false;
					
					$_SESSION['divmess'] = ($settings->SaveToXml()) ? MES_SAVESUCCESSFUL : MES_ERROR.getError();
					$ref = 'mailadm.php?mode=wm_debug';
					break;
					
				case 'edit' :
					
					$account = null;
					$dbstor = &DbStorageCreator::CreateDatabaseStorage($account);
				
					if (isset($_POST['uid']) && $_POST['uid'] > -1)
					{
						if ($dbstor->Connect())
						{
							$account = &$dbstor->SelectAccountData($_POST['uid']);
													
							$account->MailboxLimit = isset($_POST['int_limit_mailbox']) ? $_POST['int_limit_mailbox'] : $account->MailboxLimit;
							$account->FriendlyName = isset($_POST['fm_friendly_name']) ? $_POST['fm_friendly_name'] : $account->FriendlyName;
						
							$account->Email = isset($_POST['fm_email']) ? $_POST['fm_email'] : $account->Email;
							
							$account->Email = ConvertUtils::ClearFileName($account->Email);
							
							$account->MailIncHost = isset($_POST['fm_incoming_server']) ? $_POST['fm_incoming_server'] : $account->MailIncHost;
							$account->MailIncLogin = isset($_POST['fm_incoming_login']) ? $_POST['fm_incoming_login'] : $account->MailIncLogin;
							
							$newPassword = isset($_POST['fm_incoming_password']) ? $_POST['fm_incoming_password'] : DUMMYPASSWORD;
							
							if ($newPassword != DUMMYPASSWORD)
							{
								$account->MailIncPassword = $newPassword;
							}
							
							$account->MailIncPort = isset($_POST['fm_incoming_server_port']) ? (int) $_POST['fm_incoming_server_port'] : $account->MailIncPort;
							$account->MailOutHost = isset($_POST['fm_smtp_server']) ? $_POST['fm_smtp_server'] : $account->MailOutHost;
							if ($account->MailOutHost == '') $account->MailOutHost = $account->MailIncHost;
							$account->MailOutLogin = isset($_POST['fm_smtp_login']) ? $_POST['fm_smtp_login'] : $account->MailOutLogin;
							
							$new2Password = isset($_POST['fm_smtp_password']) ? $_POST['fm_smtp_password'] : DUMMYPASSWORD;
							if ($new2Password != DUMMYPASSWORD)
							{
								$account->MailOutPassword = $new2Password;
							}							

							$account->MailOutPort = isset($_POST['fm_smtp_server_port']) ? (int) $_POST['fm_smtp_server_port'] : $account->MailOutPort;
							
							$account->MailProtocol = isset($_POST['fm_incoming_protocol']) ? (int) $_POST['fm_incoming_protocol'] : $account->MailProtocol;
							
							$account->MailOutAuthentication = (isset($_POST['fm_smtp_authorisation']));
							$account->UseFriendlyName = (isset($_POST['fm_use_friendly_name']));
							$account->GetMailAtLogin = (isset($_POST['fm_getmail_at_login']));
							$account->AllowDirectMode = (isset($_POST['fm_allow_direct_mode']));
							$account->AllowChangeSettings = (isset($_POST['fm_allow_user_change_email_settings']));
							
							$synchronize = isset($_POST['synchronizeSelect']) ? $_POST['synchronizeSelect'] : FOLDERSYNC_NewHeadersOnly;
	
							if ($account->MailProtocol == MAILPROTOCOL_POP3 || $account->MailProtocol == MAILPROTOCOL_WMSERVER)
							{
								$account->MailsOnServerDays = isset($_POST['fm_keep_messages_days']) ? (int) $_POST['fm_keep_messages_days'] : $account->MailsOnServerDays;
								
								if (isset($_POST['fm_int_deleted_as_server']) && (int) $_POST['fm_int_deleted_as_server'] == 1)
								{
									if ($synchronize == FOLDERSYNC_NewHeadersOnly || $synchronize == FOLDERSYNC_NewEntireMessages)
									{
										$synchronize++;
									}
								}
								
								$mailmode = MAILMODE_LeaveMessagesOnServer;
								
								if (isset($_POST['fm_mail_management_mode']))
								{
									if ((int) $_POST['fm_mail_management_mode'] == 1)
									{
										$mailmode = MAILMODE_DeleteMessagesFromServer;
									}
									else 
									{
										$p = 0;
										if (isset($_POST['fm_keep_for_x_days']) && (int) $_POST['fm_keep_for_x_days'] == 1)
										{
											$mailmode = MAILMODE_KeepMessagesOnServer;
											$p++;
										}
										if (isset($_POST['fm_delete_messages_from_trash']) && (int) $_POST['fm_delete_messages_from_trash'] == 1)
										{
											$mailmode = MAILMODE_DeleteMessageWhenItsRemovedFromTrash;
											$p++;
										}
										if ($p == 2)
										{
											$mailmode = MAILMODE_KeepMessagesOnServerAndDeleteMessageWhenItsRemovedFromTrash;
										}
									}
								}
								
								$account->MailMode = $mailmode;
							}
							
							$_SESSION['divmess'] = ($account->Update($synchronize)) ? MES_SAVESUCCESSFUL : MES_ERROR.getError();
							
						}
						else 
						{
							$_SESSION['divmess'] = MES_CONNECTERROR;
						}
						$ref = 'mailadm.php?mode=wm_edit&uid='.$_POST['uid'];
					}
					else 
					{
						$account = &new Account();
	
						$account->MailboxSize = 0;
						$account->MailboxLimit = isset($_POST['int_limit_mailbox']) ? $_POST['int_limit_mailbox'] : $account->MailboxLimit;
						$account->FriendlyName = isset($_POST['fm_friendly_name']) ? $_POST['fm_friendly_name'] : $account->FriendlyName;
					
						$account->Email = isset($_POST['fm_email']) ? $_POST['fm_email'] : $account->Email;
						
						$account->MailIncHost = isset($_POST['fm_incoming_server']) ? $_POST['fm_incoming_server'] : $account->MailIncHost;
						$account->MailIncLogin = isset($_POST['fm_incoming_login']) ? $_POST['fm_incoming_login'] : $account->MailIncLogin;
						$account->MailIncPassword = isset($_POST['fm_incoming_password']) ? $_POST['fm_incoming_password'] : $account->MailIncPassword;
						$account->MailIncPort = isset($_POST['fm_incoming_server_port']) ? (int) $_POST['fm_incoming_server_port'] : $account->MailIncPort;
						
						$account->MailOutHost = isset($_POST['fm_smtp_server']) ? $_POST['fm_smtp_server'] : $account->MailOutHost;
						$account->MailOutLogin = isset($_POST['fm_smtp_login']) ? $_POST['fm_smtp_login'] : $account->MailOutLogin;
						$account->MailOutPassword = isset($_POST['fm_smtp_password']) ? $_POST['fm_smtp_password'] : $account->MailOutPassword;
						$account->MailOutPort = isset($_POST['fm_smtp_server_port']) ? (int) $_POST['fm_smtp_server_port'] : $account->MailOutPort;
						
						$account->MailProtocol = isset($_POST['fm_incoming_protocol']) ? (int) $_POST['fm_incoming_protocol'] : $account->MailProtocol;
						
						if ($settings->EnableWmServer)
						{
							$WMConsole = new CWmServerConsole();	
							if ($WMConsole->Connect())
							{
								$domains = $WMConsole->DomainList();
								$domain = EmailAddress::GetDomainFromEmail($account->Email);
								if (in_array($domain, $domains))
								{
									$account->MailProtocol = MAILPROTOCOL_WMSERVER;
									$account->MailOutLogin = $account->Email;
									$account->MailOutPassword = $account->MailIncPassword;
									$account->MailOutHost = $settings->WmServerHost;
									$account->MailOutPort = $WMConsole->Settings->OutPort;
								}
								$WMConsole->Disconnect();
							}
							else 
							{
								$_SESSION['divmess'] = '<font color="red"><b>'.getGlobalError().'</b></font>';
								$ref = 'mailadm.php?mode=wm_edit&uid=-1';
								break;
							}
						}

						$account->MailOutAuthentication = (isset($_POST['fm_smtp_authorisation']));
						$account->UseFriendlyName = (isset($_POST['fm_use_friendly_name']));
						$account->HideContacts = (isset($_POST['fm_hide_contacts']));
						$account->GetMailAtLogin = (isset($_POST['fm_getmail_at_login']));
						$account->AllowChangeSettings = (isset($_POST['fm_allow_user_change_email_settings']));
						
						$synchronize = isset($_POST['synchronizeSelect']) ? $_POST['synchronizeSelect'] : FOLDERSYNC_NewHeadersOnly;
	
						if ($account->MailProtocol == MAILPROTOCOL_POP3 || $account->MailProtocol == MAILPROTOCOL_WMSERVER)
						{
							$account->MailsOnServerDays = isset($_POST['fm_keep_messages_days']) ? (int) $_POST['fm_keep_messages_days'] : $account->MailsOnServerDays;
							
							if (isset($_POST['fm_int_deleted_as_server']) && (int) $_POST['fm_int_deleted_as_server'] == 1)
							{
								if ($synchronize == FOLDERSYNC_NewHeadersOnly || $synchronize == FOLDERSYNC_NewEntireMessages)
								{
									$synchronize++;
								}
							}
							
							$mailmode = MAILMODE_LeaveMessagesOnServer;
							
							
							if (isset($_POST['fm_mail_management_mode']))
							{
								if ((int) $_POST['fm_mail_management_mode'] == 1)
								{
									$mailmode = MAILMODE_DeleteMessagesFromServer;
								}
								else 
								{
									$p = 0;
									if (isset($_POST['fm_keep_for_x_days']) && (int) $_POST['fm_keep_for_x_days'] == 1)
									{
										$mailmode = MAILMODE_KeepMessagesOnServer;
										$p++;
									}
									if (isset($_POST['fm_delete_messages_from_trash']) && (int) $_POST['fm_delete_messages_from_trash'] == 1)
									{
										$mailmode = MAILMODE_DeleteMessageWhenItsRemovedFromTrash;
										$p++;
									}
									if ($p == 2)
									{
										$mailmode = MAILMODE_KeepMessagesOnServerAndDeleteMessageWhenItsRemovedFromTrash;
									}
								}
							}
							
							$account->MailMode = $mailmode;
						}
						
						$user = &User::CreateUser($account);
						if (!$user)
						{
								$_SESSION['divmess'] = '<font color="red"><b>'.getGlobalError().'</b></font>';
								$ref = 'mailadm.php?mode=wm_edit&uid=-1';							
						}
						else 
						{
							$account->IdUser = $user->Id;
							$account->DefaultAccount = true;
						
							$folderSync = FOLDERSYNC_AllEntireMessages;
							if ($account->MailProtocol == MAILPROTOCOL_IMAP4)
							{
								$folderSync = FOLDERSYNC_AllHeadersOnly;
							}
							$folderSync = ($settings->AllowDirectMode && $settings->DirectModeIsDefault) ?
								FOLDERSYNC_DirectMode : $folderSync;
							if (!$user->CreateAccount($account, $folderSync))
							{
								User::DeleteUserSettings($user->Id);
								$_SESSION['divmess'] = '<font color="red"><b>'.getGlobalError().'</b></font>';
								$ref = 'mailadm.php?mode=wm_edit&uid=-1';
							}
							else 
							{
								$ref = 'mailadm.php?mode=wm_users';
							}
						}
					}
					
					break;
			}
		}
		
		if (isset($ref) && strlen($ref) > 0)
		{
			header('Location: '.$ref);
			exit();
		}
			
		switch ($mode)
		{
			case 'showlog':
				header('Content-Type: text/plain');
				$minisize = 50000;
				if (file_exists(INI_DIR.'/'.LOG_PATH.'/'.LOG_FILENAME))
				{
					$size = @filesize(INI_DIR.'/'.LOG_PATH.'/'.LOG_FILENAME);
					if ($size && $size > 0)
					{
						$fh = @fopen(INI_DIR.'/'.LOG_PATH.'/'.LOG_FILENAME, 'rb');
						if ($fh)
						{
							if (isset($_GET['t']) && $_GET['t'] == '1')
							{
								if ($size > $minisize)
								{
									@fseek($fh, $size - $minisize);
									$text = @fread($fh, $minisize);
								}
								else 
								{
									$text = @fread($fh, $size);
								}
							}
							else 
							{
								$text = @fread($fh, $size);
							}
						}
						else 
						{
							$text = 'log file can\'t be read';
						}
					}
				}	
				else 
				{
					$text = 'log file is empty';
				}
				
				echo ($text) ? $text : 'log file is empty';
				break;
				
			case 'test_connection':
				echo '<script type="text/javascript">';
				
				if (!isset($_POST['intDbType']))
				{		
					echo 'parent.clearDiv();';
					echo 'parent.writeDiv("'.MES_ERROR.'");';
					echo '</script>';
					die();
				}
				
				$null = null;
				$bool = false;
				
				$settings->DbType = (int) $_POST['intDbType'];
				$settings->DbHost = (isset($_POST['txtSqlSrc'])) ? addslashes($_POST['txtSqlSrc']) : '';
				$settings->DbName = (isset($_POST['txtSqlName'])) ? addslashes($_POST['txtSqlName']) : '';
				$settings->DbLogin = (isset($_POST['txtSqlLogin'])) ? addslashes($_POST['txtSqlLogin']) : '';
				$settings->DbPassword = (isset($_POST['txtSqlPassword'])) ? addslashes($_POST['txtSqlPassword']) : '';
				$settings->UseCustomConnectionString = (isset($_POST['useCS']) && (int) $_POST['useCS'] = 1);
				
				$instance = &DbStorageCreator::CreateDatabaseStorage($null);
				
				if (!isset($instance))
				{	
					echo 'parent.clearDiv();';	
					echo 'parent.writeDiv("'.MES_ERROR.'");';
					echo 'parent.clearDiv()';
					echo '</script>';
					die();
				}
				else 
				{
					$bool = true;
				}
						
				$mtime = getmicrotime();
				$req = ($bool) ? (bool) $instance->_dbConnection->Connect() : false;
				$mtime = getmicrotime() - $mtime;
				
				if ($req === true)
				{
					echo 'parent.clearDiv();';
					printf('parent.writeDiv("<font color=\'green\'><b>Connect successful! (%.4f sec)</b></font>");', $mtime);
				}
				else 
				{
					echo 'parent.clearDiv();';
					echo 'parent.writeDiv("<font color=\"red\"><b>Connect unsuccessful!</b></font>'.str_replace('"', '\'', getError()).'");';
				}
				echo '</script>';
				
				break;
				
			case 'server_connection':
				
				$isError = false;
				$msgText = '';
				
				if (isset($_POST['txtWmServerRootPath']))
				{
					$RootPath = rtrim(trim($_POST['txtWmServerRootPath']), '\\/');
					if (strlen($RootPath) > 2)
					{
						if (!is_dir($RootPath.'/domains'))
						{
							$isError = true;
							$msgText = '<b>Connect unsuccessful!</b><br />Server Root Path '.addslashes($RootPath).' incorrect';
						}
					}
					else 
					{
						$isError = true;
						$msgText = '<b>Connect unsuccessful!</b><br />Server Root Path not set!';
					}
				}			
				else 
				{
					$isError = true;
					$msgText = '<b>Connect unsuccessful!</b><br />Server Root Path not set!';
				}
				
				if (!$isError && $_POST['txtWmServerHostName'] && trim($_POST['txtWmServerHostName']))
				{
					$settings->WmServerRootPath = $RootPath; 
					$WMServer = new CWmServerConsole(trim($_POST['txtWmServerHostName']));
					$mtime = getmicrotime();
					$req = $WMServer->Connect();
					$mtime = getmicrotime() - $mtime;
					
					if ($req === true)
					{
						$msgText = sprintf('<b>Connect successful! (%.4f sec)</b>', $mtime);
					}
					else 
					{
						$isError = true;
						$msgText = '<b>Connect unsuccessful!</b><br />'.str_replace('"', '\'', str_replace(array("\r", "\t", "\n"), '', $WMServer->GetError()));
					}					
				}
				
				echo '<script type="text/javascript">parent.clearDiv();'."\r\n".'parent.writeDiv("';
				echo ($isError) ? '<font color=\"red\">' : '<font color=\"green\">';
				echo $msgText.'</font>");</script>';
				
				break;
			
			// interface
			case 'wm_edit':
				$navId = 2;
				$null = null;
				$folderSyncType = FOLDERSYNC_NewEntireMessages;
				
				$dbstor = &DbStorageCreator::CreateDatabaseStorage($null);
	
				if (isset($_GET['uid']) && $_GET['uid'] > -1)
				{
					if ($dbstor->Connect())
					{
						if ($editAccount = $dbstor->SelectAccountData($_GET['uid']))
						{
							$dbstor->Account = $editAccount;
							$folderSyncType = $dbstor->GetFolderSyncType(FOLDERTYPE_Inbox);
							$anotherAccounts = &$dbstor->SelectAccounts($editAccount->IdUser);							
						}
						else
						{
							header('Location: mailadm.php?mode=wm_users');
						}
					}
					else 
					{
						header('Location: mailadm.php?mode=wm_users');
					}
				}
				else 
				{
					if (isset($_GET['user_id']) && $_GET['user_id'] > -1)
					{
						//$editAccount = 
					}
					else 
					{		
						$editAccount = &new Account();
					}
				}
				
				require_once(WM_ROOTPATH.'admin/main-top.php');
				require_once(WM_ROOTPATH.'admin/main-left.php');
				require_once(WM_ROOTPATH.'admin/main-center-edit.php');
				require_once(WM_ROOTPATH.'admin/main-foot.php');
				break;
				
			case 'info':		
				echo '<center><b>ver. '.WMVERSION.'</b><br />'.INI_DIR.'<br />'.__FILE__.'<br />'.'<br /><br /></center>';
				phpinfo();
				exit();
				break;
				
			case 'wm_users':
				$navId = 2;
				require_once(WM_ROOTPATH.'admin/main-top.php');
				require_once(WM_ROOTPATH.'admin/main-left.php');
				require_once(WM_ROOTPATH.'admin/main-center-users.php');
				require_once(WM_ROOTPATH.'admin/main-foot.php');
				break;
				
			case 'wm_cal':
				$navId = 8;
				require_once(WM_ROOTPATH.'admin/main-top.php');
				require_once(WM_ROOTPATH.'admin/main-left.php');
				require_once(WM_ROOTPATH.'admin/main-center-calendar.php');
				require_once(WM_ROOTPATH.'admin/main-foot.php');
				break;
				
			case 'wm_debug':
				$navId = 6;
				require_once(WM_ROOTPATH.'admin/main-top.php');
				require_once(WM_ROOTPATH.'admin/main-left.php');
				require_once(WM_ROOTPATH.'admin/main-center-debug.php');
				require_once(WM_ROOTPATH.'admin/main-foot.php');
				break;
				
			case 'wm_interface':
				$navId = 4;
				require_once(WM_ROOTPATH.'admin/main-top.php');
				require_once(WM_ROOTPATH.'admin/main-left.php');
				require_once(WM_ROOTPATH.'admin/main-center-interface.php');
				require_once(WM_ROOTPATH.'admin/main-foot.php');
				break;
				
			case 'wm_domain':
				$navId = 5;
				$checkmass = array();
				
				$settings->HideLoginMode = $settings->HideLoginMode.'';
				
				if (strlen($settings->HideLoginMode) > 0)
				{
					$checkmass[$settings->HideLoginMode{0}] = 'checked="checked"';
				}
				
				if (strlen($settings->HideLoginMode) > 1 && $settings->HideLoginMode{0} == '1')
				{
					switch ($settings->HideLoginMode{1})
					{
						case '0': $checkmass[3] = 'selected="selected"'; break;
						case '1': $checkmass[4] = 'selected="selected"'; break;
					}
				}
				elseif($settings->HideLoginMode == 0)
				{
					$checkmass[0] = 'checked="checked"';	
				}
				
				if (strlen($settings->HideLoginMode) > 1 && $settings->HideLoginMode{0} == '2')
				{
					switch ($settings->HideLoginMode{1})
					{
						case '1': $checkmass[5] = 'checked="checked"'; break;
						case '2': $checkmass[6] = 'checked="checked"'; break;
						case '3':
							$checkmass[5] = 'checked="checked"';
							$checkmass[6] = 'checked="checked"';
							break;
					}
				}
				
				require_once(WM_ROOTPATH.'admin/main-top.php');
				require_once(WM_ROOTPATH.'admin/main-left.php');
				require_once(WM_ROOTPATH.'admin/main-center-login.php');
				require_once(WM_ROOTPATH.'admin/main-foot.php');
				break;			
				
			case 'wm_settings':
				$navId = 3;
				require_once(WM_ROOTPATH.'admin/main-top.php');
				require_once(WM_ROOTPATH.'admin/main-left.php');
				require_once(WM_ROOTPATH.'admin/main-center-settings.php');
				require_once(WM_ROOTPATH.'admin/main-foot.php');
				break;
				
			case 'wm_db':
				$navId = 1;
				$checkmass = array();
				$checkmass[$settings->DbType] = 'checked="checked"';
				$isMySQLWork = function_exists('mysql_connect');
				$isMsSQLWork = function_exists('mssql_connect');
				$isOdbcWork = function_exists('odbc_connect');
				require_once(WM_ROOTPATH.'admin/main-top.php');
				require_once(WM_ROOTPATH.'admin/main-left.php');
				require_once(WM_ROOTPATH.'admin/main-center-db.php');
				require_once(WM_ROOTPATH.'admin/main-foot.php');
				break;
			case 'wm_server':
				$navId = 7;
				require_once(WM_ROOTPATH.'admin/main-top.php');
				require_once(WM_ROOTPATH.'admin/main-left.php');
				require_once(WM_ROOTPATH.'admin/main-center-wmserver.php');
				require_once(WM_ROOTPATH.'admin/main-foot.php');				
				break;
	
			case 'logout':
			case 'login':
				$mode = 'login';
				break;
			default:
				$_GET['error'] = 2;
				$mode = 'login';
				break;
		}
	}
	else 
	{
		if (strlen($mode) > 0 && $mode != 'login') 
		{
			$_GET['error'] = (isset($_SESSION['passwordIsCorrect'])) ? 2 : 1;
		}
		$mode = 'login';
	}
		
	if ($mode == 'login')
	{
		if (session_id()) session_destroy();
		
		$errorCode = isset($_GET['error']) ? (int) $_GET['error'] : -1;
		switch ($errorCode)
		{
			default:	$errorText = ''; break;
			case 1:	$errorText = 'The previous session was terminated due to a timeout.'; break;
			case 2:	$errorText = 'An attempt of unauthorized access.'; break;
			case 3:	$errorText = 'Wrong login and/or password. Authentication failed.'; break;
		}
		
		$errorDiv = (strlen($errorText) > 0) ? '<div class="wm_login_error" id="login_error">'.$errorText.'</div>' : '';
		require_once(WM_ROOTPATH.'admin/login.php');
	}
	
	/**
	 * @return string
	 */
	function getError()
	{
		return isset($GLOBALS[ErrorDesc]) ? '<br /><font color="red">'.ConvertUtils::WMHtmlSpecialChars(getGlobalError()).'</font>' : '';
	}
	
	/**
	 * @param string $str
	 */
	function setAdmError($str)
	{
		$GLOBALS[ErrorDesc] = $str;
	}
