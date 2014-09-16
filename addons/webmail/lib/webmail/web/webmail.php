<?php

	@ob_start();

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	require_once(WM_ROOTPATH.'common/inc_constants.php');

session_name('PHPWEBMAILSESSID');
if (!isset($_SESSION[ACCOUNT_ID]))
{
	@session_start();
	if (!isset($_SESSION[ACCOUNT_ID]))
	{
		@session_start();
	}
}

if (!isset($_SESSION[ACCOUNT_ID]))
{
	header('Location: index.php?error=1');
	exit;
}

if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

$check = isset($_GET['check']) ? $_GET['check'] : 0;
$start = isset($_GET['start']) ? $_GET['start'] : 0;
$to = isset($_GET['to']) ? $_GET['to'] : '';

if ($check)
{
	require_once(WM_ROOTPATH.'class_account.php');
	$account =& Account::LoadFromDb($_SESSION[ACCOUNT_ID]);
	if (!$account)
	{
		header('location: index.php?error=2');
		exit();
	}
	
	$_SESSION[SESSION_LANG] = $account->DefaultLanguage;	
	
	$webMailUrl = 'webmail.php?start='.$start.'&to='.$to;
	require_once(WM_ROOTPATH.'check-mail-at-login.php');
}
else 
{
	require_once(WM_ROOTPATH.'class_settings.php');
	require_once(WM_ROOTPATH.'class_account.php');
	require_once(WM_ROOTPATH.'class_filesystem.php');
	
	$settings =& Settings::CreateInstance();
	if (!$settings || !$settings->isLoad)
	{
		header('location: index.php?error=3');
		exit();		
	}
	elseif (!$settings->IncludeLang())
	{
		header('location: index.php?error=6');
		exit();			
	}
	
	$nAcct = isset($_GET['nacct']) ? $_GET['nacct'] : null;

	if ($nAcct !== null)
	{
		$dbStorage =& DbStorageCreator::CreateDatabaseStorage($null);
		if ($dbStorage->Connect() && $dbStorage->IsAccountInRing($_SESSION[ACCOUNT_ID], $nAcct))
		{
			$_SESSION[ACCOUNT_ID] = $nAcct;
		}
		else 
		{
			header('location: index.php?error=2');
			exit();	
		}
	}
	
	$account =& Account::LoadFromDb($_SESSION[ACCOUNT_ID]);
	if (!$account)
	{
		header('location: index.php?error=2');
		exit();
	}
	
	define('defaultTitle', $settings->WindowTitle);
	$skins = &FileSystem::GetSkinsList();

	$hasDefSettingsSkin = false;
	foreach ($skins as $skinName)
	{
		if ($skinName == $settings->DefaultSkin)
		{
			$hasDefSettingsSkin = true;
		}
		
		if ($skinName == $account->DefaultSkin)
		{
			define('defaultSkin', $account->DefaultSkin);
			break;
		}
	}
	
	if (!defined('defaultSkin'))
	{
		if ($hasDefSettingsSkin)
		{
			define('defaultSkin', $settings->DefaultSkin);
		}
		else
		{
			define('defaultSkin', $skins[0]);
		}
	}
	
	$expireTime = 31536000;
	header('Content-type: text/html; charset=utf-8');
	header('Content-script-type: text/javascript');
	header('Pragma: cache');
	header('Cache-control: public'); 
	header('Expires: '.gmdate( "D, d M Y H:i:s", time()+$expireTime ).' GMT');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html id="html">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Pragma" content="cache" />
	<meta http-equiv="Cache-Control" content="public" />
	<meta http-equiv="Expires" content="<?php echo gmdate( "D, d M Y H:i:s", time()+$expireTime ).' GMT'; ?>" />
	<title><?php echo defaultTitle; ?></title>
	<link rel="stylesheet" href="skins/<?php echo defaultSkin?>/styles.css" type="text/css" id="skin" />
	<script type="text/javascript">
		function ResizeBodyHandler() {}
	</script>
</head>

<body onresize="ResizeBodyHandler();" onload="Init();">
	<table class="wm_information" id="info_cont">
		<tr>
			<td class="wm_info_message" id="info_message">
				<?php echo JS_LANG_InfoWebMailLoading;?>
			</td>
		</tr>
	</table>
	<div align="center" id="content" class="wm_hide">
		<div class="wm_logo" id="logo" tabindex="-1" onfocus="this.blur();">
			<span><?php echo StoreWebmail;?></span>
			<a class="header" target="_top" href="<?php echo !empty($_SESSION['cart_url']) ? $_SESSION['cart_url'] : '../../../../../admin.php'; ?>"><?php echo BackToCart;?></a>
		</div>
	</div>
	<div id="spell_popup_menu" class="wm_hide"><?php echo SpellWait; ?></div>
	<div class="wm_hide" id="copyright">
		<?php require('inc.footer.php'); ?>
	</div>
	<iframe name="session_saver" id="session_saver" src="session-saver.php" class="wm_hide"></iframe>
</body>
<script type="text/javascript">
	var LoginUrl = 'index.php';
	var WebMailUrl = 'webmail.php';
	var BaseWebMailUrl = 'basewebmail.php';
	var ActionUrl = 'processing.php';
	var EditAreaUrl = 'edit-area.php';
	var EmptyHtmlUrl = 'empty.html';
	var UploadUrl = 'upload.php';
	var ImportUrl = 'import.php';
	var HistoryStorageUrl = 'history-storage.php';
	var CheckMailUrl = 'check-mail.php';
	var LanguageUrl = '_language.js.php';
	var DataHelpUrl = 'advanced-data-help.php';
	var SpellcheckerUrl = 'spellcheck.php';
	var CalendarUrl = 'calendar.php';
	var CalendarProcessingUrl = 'calendar/processing.php';
	var Title = "<?php echo ConvertUtils::ClearJavaScriptString(defaultTitle, '"'); ?>";
	var SkinName = "<?php echo ConvertUtils::ClearJavaScriptString(defaultSkin, '"'); ?>";
	var Start = <?php echo $start?>;
	var ToAddr = "<?php echo ConvertUtils::ClearJavaScriptString($to, '"'); ?>";
	var Browser;
	var WebMail, HistoryStorage;
</script>
<script type="text/javascript" src="_language.js.php?lang=<?php echo ConvertUtils::AttributeQuote($account->DefaultLanguage); ?>"></script>
<script type="text/javascript" src="_defines.js"></script>
<script type="text/javascript" src="class.common.js"></script>
<script type="text/javascript" src="_functions.js"></script>
<script type="text/javascript" src="class.webmail.js"></script>
<script type="text/javascript" src="class.webmail-parts.js"></script>
<script type="text/javascript" src="class.html-editor.js"></script>
<script type="text/javascript" src="class.xml-parsers.js"></script>
<script type="text/javascript" src="class.screens-parts.js"></script>
<script type="text/javascript" src="screen.messages-list.js"></script>
<script type="text/javascript" src="screen.view-message.js"></script>
<script type="text/javascript" src="screen.messages-list-view.js"></script>
<script type="text/javascript" src="screen.new-message.js"></script>
<script type="text/javascript" src="class.variable-table.js"></script>
<!-- scripts for settings-->
<script type="text/javascript" src="screen.user-settings.js"></script>
<script type="text/javascript" src="screen.common-settings.js"></script>
<script type="text/javascript" src="screen.accounts-settings.js"></script>
<script type="text/javascript" src="screen.account-properties.js"></script>
<script type="text/javascript" src="calendar/inc.calendar-settings.js"></script>
<!-- scripts for contacts-->
<script type="text/javascript" src="screen.contacts.js"></script>
<script type="text/javascript" src="screen.view-contact.js"></script>

<script type="text/javascript">
	function Init() {
		Browser = new CBrowser();
		var DataTypes = [
			new CDataType(TYPE_SETTINGS_LIST, false, 0, false, { }, 'settings_list' ),
			new CDataType(TYPE_ACCOUNTS_LIST, false, 0, false, { }, 'accounts' ),
			new CDataType(TYPE_FOLDERS_LIST, false, 0, false, { IdAcct: 'id_acct', Sync: 'sync' }, 'folders_list' ),
			new CDataType(TYPE_MESSAGES_LIST, true, 5, false, { Page: 'page', SortField: 'sort_field', SortOrder: 'sort_order' }, 'messages' ),
			new CDataType(TYPE_MESSAGES_OPERATION, false, 0, false, { }, '' ),
			new CDataType(TYPE_MESSAGE, true, 10, true, { Id: 'id', Charset: 'charset' }, 'message' ),
			new CDataType(TYPE_USER_SETTINGS, false, 0, false, { }, 'settings' ),
			new CDataType(TYPE_ACCOUNT_PROPERTIES, false, 0, false, { IdAcct: 'id_acct' }, 'account' ),
			new CDataType(TYPE_FILTERS, false, 0, false, { IdAcct: 'id_acct' }, 'filters' ),
			new CDataType(TYPE_FILTER_PROPERTIES, false, 0, false, { IdFilter: 'id_filter', IdAcct: 'id_acct' }, 'filter' ),
			new CDataType(TYPE_X_SPAM, false, 0, false, { IdAcct: 'id_acct' }, 'x_spam' ),
			new CDataType(TYPE_CONTACTS_SETTINGS, false, 0, false, { }, 'contacts_settings' ),
			new CDataType(TYPE_SIGNATURE, false, 0, false, { IdAcct: 'id_acct' }, 'signature' ),
			new CDataType(TYPE_FOLDERS, false, 0, false, { IdAcct: 'id_acct' }, 'folders' ),
			new CDataType(TYPE_CONTACTS, false, 0, false, { Page: 'page', SortField: 'sort_field', SortOrder: 'sort_order' }, 'contacts_groups' ),
			new CDataType(TYPE_CONTACT, false, 0, false, { IdAddr: 'id_addr' }, 'contact' ),
			new CDataType(TYPE_GROUPS, false, 0, false, { }, 'groups' ),
			new CDataType(TYPE_GROUP, false, 0, false, { IdGroup: 'id_group' }, 'group' ),
			new CDataType(TYPE_SPELLCHECK, false, 0, false, { Word: 'word' }, 'spellcheck')
		];
		WebMail = new CWebMail(Title, SkinName);
		WebMail.DataSource = new CDataSource( DataTypes, ActionUrl, ErrorHandler, InfoHandler, LoadHandler, TakeDataHandler, ShowLoadingInfoHandler );
		HistoryStorage = new CHistoryStorage(
				{
					Document: document,
					HistoryStorageObjectName: "HistoryStorage",
					PathToPageInIframe: HistoryStorageUrl,
					MaxLimitSteps: 50,
					Browser: Browser
				}
			);
		if (Start)
		{
			WebMail.SetStartScreen(Start);
		}
		WebMail.DataSource.Get(TYPE_SETTINGS_LIST, { }, [], '');
	}
</script>
</html>
<?php
echo '<!-- '.WMVERSION.' -->';
}