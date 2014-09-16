<?php
	
	header('Content-Type: text/html; charset=utf-8');
	
	@session_name('PHPWEBMAILSESSID');
	@session_start();

	function fixed_array_map_stripslashes($array)
	{
		if (is_array($array))
		{
			foreach ($array as $key => $value)
			{
				$array[$key] = (is_array($value))
						? @fixed_array_map_stripslashes($value)
						: @stripslashes($value);
			}
		}
		return $array;
	}
	
	function disable_magic_quotes_gpc()
	{
		if (@get_magic_quotes_gpc() == 1)
		{
			$_GET = fixed_array_map_stripslashes($_GET);
			$_POST = fixed_array_map_stripslashes($_POST);
		}
	}
	
	@disable_magic_quotes_gpc();
	
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	require_once(WM_ROOTPATH.'common/inc_constants.php');
	require_once(WM_ROOTPATH.'class_settings.php');
	
	$settings =& Settings::CreateInstance();
	
	if (!$settings || !$settings->isLoad)
	{
		exit('<script>parent.changeLocation("'.LOGINFILE.'?error=3");</script>');
	} 
	elseif (!$settings->IncludeLang())
	{
		exit('<script>parent.changeLocation("'.LOGINFILE.'?error=6");</script>');
	}
	
	require_once(WM_ROOTPATH.'class_account.php');
	require_once(WM_ROOTPATH.'classic/base_defines.php');
	require_once(WM_ROOTPATH.'common/class_log.php');
	
	require_once(WM_ROOTPATH.'classic/class_getmessagebase.php');
	
	$log =& CLog::CreateInstance();
	
	if (!Session::has(ACCOUNT_ID))
	{
		exit('<script>parent.changeLocation("'.LOGINFILE.'?error=1");</script>');
	}	
	
	$_SESSION['attachtempdir'] = Session::val('attachtempdir', md5(session_id()));
	$account = &Account::LoadFromDb(Session::val(ACCOUNT_ID, -1));
	
	if (!$account)
	{
		exit('<script>parent.changeLocation("'.LOGINFILE.'?error=2");</script>');
	}
	
	$isNull = false;

	$mes_id = Get::val('msg_id', '');
	$mes_uid = Get::val('msg_uid', '');
	$folder_id = Get::val('folder_id', '');
	$folder_name = Get::val('folder_fname', '');
	$mes_charset = Get::val('charset', -1);


	if ($mes_uid)
	{
		$message =& new GetMessageBase(	$account,
										$mes_id,
										$mes_uid,
										$folder_id ,
										$folder_name,
										$mes_charset);
										
		if (!$message->msg) 
		{
			$isNull = true;
		}
	}
	else 
	{
		$isNull = true;
	}
	
	if ($isNull)
	{
		exit('Null Message');
	}
	
	$fullBodyText = ($message->msg->HasHtmlText()) 
		? ConvertUtils::ReplaceJSMethod($message->PrintHtmlBody(true))
		: nl2br($message->PrintPlainBody());

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
	<head>
		<link rel="stylesheet" href="./skins/<?php echo $message->account->DefaultSkin;?>/styles.css" type="text/css" />
	</head>
	<body class="wm_body">
		<div align="center" class="wm_space_before">
			<table class="wm_print">
				<tr>
					<td class="wm_print_content" style="border-width: 0px 1px 1px 0px" width="60px">
					<?php echo JS_LANG_From;?>: 
					</td>
					<td class="wm_print_content" style="border-width: 0px 0px 1px 1px">
						<?php echo ConvertUtils::WMHtmlSpecialChars($message->PrintFrom(true)); ?>
					</td>
				</tr>
				<tr>
					<td class="wm_print_content" style="border-width: 0px 1px 1px 0px" width="60px">
						<?php echo JS_LANG_To;?>: 
					</td>
					<td class="wm_print_content" style="border-width: 0px 0px 1px 1px">
						<?php echo ConvertUtils::WMHtmlSpecialChars($message->PrintTo(true)); ?>
					</td>
				</tr>
				<tr>
					<td class="wm_print_content" style="border-width: 0px 1px 1px 0px" width="60px">
						<?php echo JS_LANG_Date;?>:
					</td>
					<td class="wm_print_content" style="border-width: 0px 0px 1px 1px">
						<?php echo ConvertUtils::WMHtmlSpecialChars($message->PrintDate());	?>
					</td>
				</tr>
				<tr>
					<td class="wm_print_content" style="border-width: 0px 1px 1px 0px" width="60px">
						<?php echo JS_LANG_Subject;?>:
					</td>
					<td class="wm_print_content" style="border-width: 0px 0px 1px 1px">
						<?php echo ConvertUtils::WMHtmlSpecialChars($message->PrintSubject(true)); ?>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="wm_print_content" style="border-width: 1px 0px 0px 0px">
						<div class="wm_space_before">
							<?php echo $fullBodyText; ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>