<?php
	@session_name('PHPWEBMAILSESSID');
	@session_start();
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	require_once(WM_ROOTPATH.'common/inc_constants.php');
	$lang = isset($_SESSION[SESSION_LANG]) ? $_SESSION[SESSION_LANG] : '';
	if ($lang && file_exists(WM_ROOTPATH.'lang/'.$lang.'.php'))
	{
		require_once(WM_ROOTPATH.'lang/'.$lang.'.php');
	}
	else 
	{
		header('Location: index.php?error=6');
		exit();
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<title><?php echo AdvancedDateHelpTitle;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body style="background-color:#ffffff; font: normal 13px Tahoma, Arial, Helvetica, sans-serif; margin:10px;">
<br />
<b><?php echo AdvancedDateHelpTitle;?></b>
<br />
<br />
<?php echo AdvancedDateHelpIntro;?><br />
<br />
<table style="font: normal 13px Tahoma, Arial, Helvetica, sans-serif;">
	<tr>
		<td><b>dd</b></td>
		<td><?php echo AdvancedDateHelpDayOfMonth;?> </td>
	</tr>
	<tr>
		<td><b>mm</b></td>
		<td><?php echo AdvancedDateHelpNumericMonth;?></td>
	</tr>
	<tr>
		<td><b>month</b></td>
		<td><?php echo AdvancedDateHelpTextualMonth;?></td>
	</tr>
	<tr>
		<td><b>yy</b></td>
		<td><?php echo AdvancedDateHelpYear2;?></td>
	</tr>
	<tr>
		<td><b>yyyy</b></td>
		<td><?php echo AdvancedDateHelpYear4;?></td>
	</tr>
	<tr>
		<td><b>y</b></td>
		<td><?php echo AdvancedDateHelpDayOfYear;?> </td>
	</tr>
	<tr>
		<td><b>q</b></td>
		<td><?php echo AdvancedDateHelpQuarter;?></td>
	</tr>
	<tr>
		<td><b>w</b></td>
		<td><?php echo AdvancedDateHelpDayOfWeek;?></td>
	</tr>
	<tr>
		<td><b>ww</b></td>
		<td><?php echo AdvancedDateHelpWeekOfYear;?></td>
	</tr>
</table>
<br />
<?php echo AdvancedDateHelpConclusion;?>
</body>
</html>
