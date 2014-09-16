<?php
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	require_once(WM_ROOTPATH.'class_settings.php');
	$settings =& Settings::CreateInstance();
	if (!$settings || !$settings->isLoad)
	{
		header('Location: index.php?error=3');
		exit();
	}
	define('defaultTitle', $settings->WindowTitle);
	define('defaultSkin', $settings->DefaultSkin);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<title><?php echo defaultTitle?></title>
	<link rel="stylesheet" href="skins/<?php echo defaultSkin?>/styles.css" type="text/css" id="skin" />
	<script type="text/javascript" src="_language.js.php"></script>
	<script type="text/javascript" src="_defines.js"></script>
	<script type="text/javascript" src="_functions.js"></script>
	<script type="text/javascript" src="class.common.js"></script>
	<script type="text/javascript">
		var checkMail;
		var WebMailUrl = '<?php echo $webMailUrl?>';
		var LoginUrl = 'index.php';
		var CheckMailUrl = 'check-mail.php';
		var EmptyHtmlUrl = 'empty.html';
		var Browser = new CBrowser();

		function Init()
		{
			checkMail = new CCheckMail(1);
			checkMail.Start();
		}
		
		function SetCheckingAccountHandler(accountName)
		{
			checkMail.SetAccount(accountName);
		}
		
		function SetStateTextHandler(text) {
			checkMail.SetText(text);
		}
		
		function SetCheckingFolderHandler(folder, count) {
			checkMail.SetFolder(folder, count);
		}
		
		function SetRetrievingMessageHandler(number) {
			checkMail.SetMsgNumber(number);
		}
		
		function SetDeletingMessageHandler(number) {
			checkMail.DeleteMsg(number);
		}
		
		function EndCheckMailHandler(error) {
			if (error == 'session_error') {
				document.location = LoginUrl + '?error=1';
			} else {
				document.location = WebMailUrl;
			}
		}
		
		function CheckEndCheckMailHandler() {
			if (checkMail.started) {
				document.location = WebMailUrl;
			}
		}
	</script>
</head>
<body onload="Init();">
<div align="center" id="content" class="wm_content">
	<div class="wm_logo" id="logo" tabindex="-1" onfocus="this.blur();">
		<span><?php echo StoreWebmail;?></span>
		<a class="header" target="_top" href="<?php echo !empty($_SESSION['cart_url']) ? $_SESSION['cart_url'] : '../../../../../admin.php'; ?>"><?php echo BackToCart;?></a>
	</div>
</div>
<div class="wm_copyright" id="copyright">
	<?php require('inc.footer.php'); ?>
</div>
</body>
</html>