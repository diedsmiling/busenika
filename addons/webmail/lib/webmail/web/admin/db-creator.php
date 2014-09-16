<?php

	@session_start();
	$isCorrect = (isset($_SESSION['passwordIsCorrect']) && (int) $_SESSION['passwordIsCorrect'] == 15);
	if (!$isCorrect) exit('session error');

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
			$_REQUEST = fixed_array_map_stripslashes($_REQUEST);
			$_POST = fixed_array_map_stripslashes($_POST);
		}
	}
	
	@disable_magic_quotes_gpc();
	
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	require_once(WM_ROOTPATH.'class_settings.php');
	require_once(WM_ROOTPATH.'class_filesystem.php');
	require_once(WM_ROOTPATH.'class_dbstorage.php');
	require_once(WM_ROOTPATH.'class_account.php');

	$null = null;
	$settings = &Settings::CreateInstance();
	$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
	if (!$dbStorage->Connect())
	{
		$error = isset($GLOBALS[ErrorDesc]) ? '<b>ERORR:</b> '.$GLOBALS[ErrorDesc] : '';
		echo '<font color="red"><b>Warning!</b></font><br />
				Connection Error!<br />'.$error;
		exit();
	}

	$step = isset($_GET['step']) ? $_GET['step'] : 1;
	$mode = isset($_GET['mode']) ? $_GET['mode'] : 'start';

	$nextButton = '<input type="button" value="Next Step" style="font-weight: bold" />';
	switch ($step)
	{	
		default:
		case 1:
			$is_good = $dbStorage->CheckExistTable($settings->DbPrefix);
			if ($is_good === true)
			{
				$nextButton = '<input type="button" value="Next Step" onclick="document.location=\'?step=2\'" style="font-weight: bold" />';
				$pref = ($settings->DbPrefix && strlen($settings->DbPrefix) > 0) ?
					'All tables being created should have <b>'.$settings->DbPrefix.'</b> prefix.<br />' : '';
				$bodyText = '
				<h1>Step 1</h1><br />
				<b>Warning!</b><br />
				Here tables of the database (<b>'.$settings->DbName.'</b>) will be created.<br />
				'.$pref.'<br />
				Press <b>Next Step</b> button to start the table creation.
				';			
			}
			elseif ($is_good && strlen($is_good) > 0)
			{
				$nextButton = '<input type="button" value="Next Step" style="font-weight: bold" disabled="disabled"/>';
				$bodyText = '
				<h1>Step 1</h1><br />
				<font color="red"><b>Warning!</b></font><br />
				The database contains the table ('.$is_good.'),<br />
				needed for handling with WEBMAIL PRO 4.0<br /><br />
				For continuation, this table should be removed or other prefix should be set. 
				';			
			}
			else 
			{
				$error = isset($GLOBALS[ErrorDesc]) ? '<b>ERORR:</b> '.$GLOBALS[ErrorDesc] : '';
				$nextButton = '<input type="button" value="Next Step" style="font-weight: bold" disabled="disabled"/>';
				$bodyText = '
				<h1>Step 1</h1><br />
				<font color="red"><b>Warning!</b></font><br />
				When operating the database, an error occured.<br />
				Possibly, it is the connection error or database name is not correctly signed.<br />
				'.$error.'<br /><br />
				Try to close the window and change database connection settings. 
				';			
			}
			
			break;
			
		case 2:
			$is_good = $dbStorage->CreateTables($settings->DbPrefix);
			if ($is_good === true)
			{
				$nextButton = '<input type="button" value="Close" style="font-weight: bold" disabled="disabled"/>';
				$bodyText = '
				<h1>Step 2</h1><br />
				<b>Congratulation!</b><br />
				All tables were created successfully.
				';			
			}
			elseif($is_good && strlen($is_good) > 0)
			{
				$error = isset($GLOBALS[ErrorDesc]) ? '<b>ERORR:</b> '.$GLOBALS[ErrorDesc] : '';
				$nextButton = '<input type="button" value="Close" style="font-weight: bold" disabled="disabled"/>';
				$bodyText = '
				<h1>Step 2</h1><br />
				<font color="red"><b>Warning!</b></font><br />
				When creating the table (<b>'.$is_good.'</b>) , an error in database occured!'.$error.'
				';	
			}
			else 
			{
				$error = isset($GLOBALS[ErrorDesc]) ? '<b>ERORR:</b> '.$GLOBALS[ErrorDesc] : '';
				$nextButton = '<input type="button" value="Close" style="font-weight: bold" disabled="disabled"/>';
				$bodyText = '
				<h1>Step 2</h1><br />
				<font color="red"><b>Warning!</b></font><br />
				When creating the table, an error in database occured!<br />'.$error.'
				';	
			}
			break;
	}
	

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>MailBee WebMail PRO</title>
</head>
<body style="font-family: Tahoma, Verdana;">
<table width="100%" height="100%">
<tr><td>
<?php echo $nextButton; ?>
</td></tr>
<!-- hr -->
<tr><td colspan="2"><hr size="1"></td></tr>
<tr><td>
<?php echo $bodyText; ?>
</td></tr>
</table>
</body>
</html>	