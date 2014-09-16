<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	define('START_PAGE_IS_MAILBOX', 0);
	define('START_PAGE_IS_NEW_MESSAGE', 1);
	define('START_PAGE_IS_SETTINGS', 2);
	define('START_PAGE_IS_CONTACTS', 3);
	
	header('Content-Type: text/html; charset=utf-8');

	@session_name('PHPWEBMAILSESSID');
	@session_start();
	
$check = isset($_GET['check']) ? $_GET['check'] : 0;
if ($check) 
{
	$getTemp = '';
	$start = isset($_GET['start']) ? $_GET['start'] : START_PAGE_IS_MAILBOX;
	$to = isset($_GET['to']) ? '&to='.trim($_GET['to']) : '';
	switch ($start)
	{
		default:
		case START_PAGE_IS_MAILBOX:		$getTemp = '?screen=mailbox';	break;
		case START_PAGE_IS_NEW_MESSAGE:	$getTemp = '?screen=new'.$to;		break;
		case START_PAGE_IS_SETTINGS:	$getTemp = '?screen=settings';	break;
		case START_PAGE_IS_CONTACTS:	$getTemp = '?screen=contacts';	break;
	}
	$webMailUrl = 'basewebmail.php'.$getTemp;
	require_once(WM_ROOTPATH.'check-mail-at-login.php');
}
else 
{

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

	require_once(WM_ROOTPATH.'classic/processor.php');
	require_once(WM_ROOTPATH.'classic/class_pagebuilder.php');

	$Proc = &new BaseProcessor();
	
	$Page = &new PageBuilder($Proc);
	
	echo $Page->ToHTML();
	echo '<!-- '.WMVERSION.' -->';
}
