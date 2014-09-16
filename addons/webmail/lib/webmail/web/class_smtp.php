<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	require_once(WM_ROOTPATH.'common/class_log.php');
	require_once(WM_ROOTPATH.'class_webmailmessages.php');
	require_once(WM_ROOTPATH.'class_account.php');
	require_once(WM_ROOTPATH.'common/inc_constants.php');

	/**
	 * @static 
	 */
	class CSmtp
	{
		/**
		 * @param Account $account
		 * @param WebMailMessage $message
		 * @param string $from
		 * @param string $to
		 * @return bool
		 */
		function SendMail(&$account, &$message, $from, $to)
		{
			$link = null;
			$log =& CLog::CreateInstance();
			$result = CSmtp::Connect($link, $account, $log);
			
			if ($result)
			{
				if ($from == null)
				{
					$fromAddr = $message->GetFrom();
					$from = $fromAddr->Email;
				}

				if ($to == null)
				{
					$to = $message->GetAllRecipientsEmailsAsString();
				}

				$result = CSmtp::Send($link, $account, $message, $from, $to, $log);
				if ($result)
				{
					$result = CSmtp::Disconnect($link, $log);
				}
			}
			else 
			{
				setGlobalError(ErrorSMTPConnect);
			}
			
			return $result;
		}
		
		
		/**
		 * @access private
		 * @param resource $link
		 * @param Account $account
		 * @param CLog $log
		 * @return bool
		 */
		function Connect(&$link, &$account, &$log)
		{
			$setings =& Settings::CreateInstance();
			
			if ($account->MailProtocol == MAILPROTOCOL_WMSERVER)
			{
				
			}
			else
			{
				$outHost = (strlen($account->MailOutHost) > 0) ? $account->MailOutHost : $account->MailIncHost;
				$errno = $errstr = null;
				
				$log->WriteLine('[Connecting to server '. $outHost.' on port '.$account->MailOutPort.']');
				
				$isSsl = ((strlen($outHost) > 6) && strtolower(substr($outHost, 0, 6)) == 'ssl://');
				if (function_exists('openssl_open') && ($isSsl || $account->MailOutPort == 465))
				{
					if (!$isSsl)
					{
						$outHost = 'ssl://'.$outHost;
					}
				}
				else 
				{
					if ($isSsl)
					{
						$outHost = substr($outHost, 6);
					}
				}
				
				$link = @fsockopen($outHost, $account->MailOutPort, $errno, $errstr, 10);
				if(!$link)
				{
					setGlobalError('SMTP Error: '.$errstr);
					$log->WriteLine(getGlobalError());
					return false;
				} else {
					@socket_set_timeout($link, 10);
					return CSmtp::IsSuccess($link, $log);
				}
			}
		}
		
		/**
		 * @access private
		 * @param resource $link
		 * @param CLog $log
		 * @return bool
		 */
		function Disconnect(&$link, &$log)
		{
			return CSmtp::ExecuteCommand($link, 'QUIT', $log);
		}
		
		/**
		 * @access private
		 * @param resource $link
		 * @param Account $account
		 * @param WebMailMessage $message
		 * @param string $from
		 * @param string $to
		 * @param CLog $log
		 * @return bool
		 */
		function Send(&$link, &$account, &$message, $from, $to, &$log)
		{
			$ehloMsg = trim(EmailAddress::GetDomainFromEmail($account->Email));
			$ehloMsg = strlen($ehloMsg) > 0 ? $ehloMsg : $account->MailOutHost;
			$result = CSmtp::ExecuteCommand($link, 'EHLO ' . $ehloMsg, $log);
			if (!$result) 
			{
				$result = CSmtp::ExecuteCommand($link, 'HELO '. $ehloMsg, $log);
			}
			
			if ($result && $account->MailOutAuthentication)
			{
				$result = CSmtp::ExecuteCommand($link, 'AUTH LOGIN', $log);
				
				$mailOutLogin = ($account->MailOutLogin) ?
						$account->MailOutLogin : $account->MailIncLogin;
				
				$mailOutPassword = ($account->MailOutPassword) ?
						$account->MailOutPassword : $account->MailIncPassword;

				if ($result)
				{
					$log->WriteLine('[Sending encoded login]');
					$result = CSmtp::ExecuteCommand($link, base64_encode($mailOutLogin), $log);
				}

				if ($result)
				{
					$log->WriteLine('[Sending encoded password]');
					$result = CSmtp::ExecuteCommand($link, base64_encode($mailOutPassword), $log);
				}
			}
			
			if ($result)
			{
				$result = CSmtp::ExecuteCommand($link, 'MAIL FROM:<'.$from.'>', $log);
			}
			else 
			{
				setGlobalError(ErrorSMTPAuth);
			}
			
			if ($result)
			{
				$toArray = explode(',', $to);
				foreach ($toArray as $recipient)
				{
					$recipient = trim($recipient);
					$result = CSmtp::ExecuteCommand($link, 'RCPT TO:<'.$recipient.'>', $log);
					if (!$result)
					{
						break;
					}
				}
			}
			
			if ($result)
			{
				$result = CSmtp::ExecuteCommand($link, 'DATA', $log);
			}
			
			if ($result)
			{
				$result = CSmtp::ExecuteCommand($link, str_replace(CRLF.'.', CRLF.'..', $message->TryToGetOriginalMailMessage()).CRLF.'.', $log);
			}
			
			return $result;
		}
		
		/**
		 * @access private
		 * @param resource $link
		 * @param string $command
		 * @param CLog $log
		 * @return bool
		 */
		function ExecuteCommand(&$link, $command, &$log)
		{
			$log->WriteLine('SMTP >>: '. $command);
			@fputs($link, $command.CRLF);
			return CSmtp::IsSuccess($link, $log);
		}
		
		/**
		 * @access private
		 * @param resource $link
		 * @param CLog $log
		 * @return bool
		 */
		function IsSuccess(&$link, &$log)
		{
			$result = true;
			do
			{
				$line = @fgets($link, 1024);
				if ($line === false)
				{
					$result = false;
					setGlobalError('SMTP IsSuccess fgets error');
					break;
				}
				else
				{
					$line = str_replace("\r", '', str_replace("\n", '', str_replace(CRLF, '', $line)));
					if (substr($line, 0, 1) != '2' && substr($line, 0, 1) != '3')
					{
						$result = false;
						$error = '[SMTP] Error <<: ' . $line;
						setGlobalError($error);
						break;
					}
				}
			  
			} while(substr($line, 3, 1) == '-');
			
			if (!$result)
			{
				$log->WriteLine(getGlobalError());
			}
			
			return $result;
		}
	}