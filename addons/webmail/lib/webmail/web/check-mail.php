<?php

	header('Content-type: text/html; charset=utf-8');

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	require_once(WM_ROOTPATH.'common/inc_constants.php');
	require_once(WM_ROOTPATH.'class_mailprocessor.php');
	require_once(WM_ROOTPATH.'class_settings.php');
	
	$errorDesc = '';
	
	@session_name('PHPWEBMAILSESSID');
	@session_start();
	
	$settings =& Settings::CreateInstance();
	if (!$settings || !$settings->isLoad || !$settings->IncludeLang())
	{
		?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
    <body onload="parent.CheckEndCheckMailHandler();">
	<script type="text/javascript">parent.EndCheckMailHandler("Can't Load Language file");</script>
</body>
</html><?php
		@ob_end_flush();
		exit;
	}
	
	@ob_start();
	@ob_end_flush();
	
	if (!isset($_SESSION[ACCOUNT_ID]))
	{
		?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
		<html>
			<meta http-equiv="Content-Script-Type" content="text/javascript" />
      		<body onload="parent.CheckEndCheckMailHandler();">
			<script type="text/javascript">parent.EndCheckMailHandler("session_error");</script>
		</body>
		</html>
		<?php
		@ob_end_flush();
		exit();
	}
	
	/**
	 * @param string $folderName
	 * @param int $messageCount
	 */
	function ShowDownloadedMessageNumber($folderName = '', $messageCount = -1) 
	{
		static $msgNumber = 0;

		@flush();
		if ($folderName != '' && $messageCount != -1)
		{
			$msgNumber = 0;
			echo '<script>';
			echo 'parent.SetCheckingFolderHandler("'.$folderName.'",'.$messageCount.');';
			echo '</script>'."\r\n";
		}
		else
		{
			$msgNumber++;
			echo '<script type="text/javascript">';
			echo 'parent.SetRetrievingMessageHandler('.$msgNumber.');';
			echo '</script>'."\r\n";
		}
		@ob_flush();
	}
	
	function ShowDeletingMessageNumber($resetCount = false)
	{
		static $msgNumber = 0;
		
		if ($resetCount)
		{
			$msgNumber = 0;
		}
		else
		{
			@flush();
			
			$msgNumber++;
			print '<script type="text/javascript">';
			print 'parent.SetDeletingMessageHandler('.$msgNumber.');';
			print '</script>'."\r\n";

			@ob_flush();
		}
	}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<body onload="parent.CheckEndCheckMailHandler();">
<?php

		$account = &Account::LoadFromDb($_SESSION[ACCOUNT_ID]);
		
		@ini_set('memory_limit', MEMORYLIMIT);
		@set_time_limit(TIMELIMIT);
		$GLOBALS['useFilters'] = true;
		if (isset($_POST['Type']) && $_POST['Type'] == 1)
		{
			$dbStorage = &DbStorageCreator::CreateDatabaseStorage($account);
			if ($dbStorage->Connect())
			{
				$accounts = &$dbStorage->SelectAccounts($account->IdUser);
				if ($accounts !== null)
				{
					foreach ($accounts as $acct_id => $acctArray)
					{
						if ($acctArray[5])
						{ 
							@flush();
							?>
							<script type="text/javascript">
							<?php echo 'parent.SetCheckingAccountHandler("'.$acctArray[4].'");'; ?>
     						</script>
							<?php
							@ob_flush();
							
							$newAcct = &Account::LoadFromDb($acct_id);
							
							@flush();
							?>
							<script type="text/javascript">
							parent.SetStateTextHandler(parent.Lang.LoggingToServer);
							</script>
							<?php
							@ob_flush();
							
							$processor = &new MailProcessor($newAcct);
				
							$folders = &$processor->GetFolders();
							
							$processor->MailStorage->DownloadedMessagesHandler = 'ShowDownloadedMessageNumber';
							
							@flush();
							?>
							<script type="text/javascript">
							parent.SetStateTextHandler(parent.Lang.GettingMsgsNum);
							</script>
							<?php
							@ob_flush();
								
							@flush();		
							
							if (!$processor->Synchronize($folders))
							{
								$errorDesc .= getGlobalError();
							}
							
							$processor->MailStorage->Disconnect();
							
							@ob_flush();
							
						}
					}
				}
			}
		}
		else
		{
			@flush();
			?>
			<script type="text/javascript">
			parent.SetStateTextHandler(parent.Lang.LoggingToServer);
			</script>
			<?php
			@ob_flush();

			$processor = &new MailProcessor($account);

			$folders = &$processor->GetFolders();
			
			$processor->MailStorage->DownloadedMessagesHandler = 'ShowDownloadedMessageNumber';
						
			@flush();
			?>
			<script type="text/javascript">
			parent.SetStateTextHandler(parent.Lang.GettingMsgsNum);
			</script>
			<?php
			@ob_flush();

			if (!$processor->Synchronize($folders))
			{
				$errorDesc = getGlobalError();
			}
		}

	@flush();
	?>
	<script type="text/javascript">
	<?php print 'parent.EndCheckMailHandler("'.str_replace('"', '\\"', trim($errorDesc)).'");'; 
	if ($errorDesc) SetError($errorDesc);
	?>
	</script>
	<?php @ob_flush(); ?>
</body>
</html>
<?php 
	@ob_end_flush();

	/**
	 * @param string $text
	 */
	function SetError($text)
	{
		$_SESSION[INFORMATION] = $text;
		$_SESSION[ISINFOERROR] = true;	
	}
