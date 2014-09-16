<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	require_once(WM_ROOTPATH.'common/class_log.php');
	require_once(WM_ROOTPATH.'mime/class_emailaddress.php');
	require_once(WM_ROOTPATH.'wmserver/class_wmsettings.php');
	
	define('TABCHAR', "\t");
	define('CRLFCHARS', "\r\n");

class CWmServerConsole
{
	/**
	 * @access private
	 * @var string
	 */
	var $_admHost;
	
	/**
	 * @access private
	 * @var int
	 */
	var $_admPort;
	
	/**
	 * @access private
	 * @var string
	 */
	var $_admLogin;

	/**
	 * @var string
	 */
	var $_admPassword;
	
	/**
	 * @access private
	 * @var resourse
	 */
	var $_socket;
	
	/**
	 * @access private
	 * @var array
	 */
	var $_socket_status;
	
	/**
	 * @access private
	 * @var string
	 */
	var $_error;
	
	/**
	 * @access private
	 * @var CLog
	 */
	var $_log;

	/**
	 * @access public
	 * @var WMSettings
	 */
	var $Settings;
	
	/**
	 * @access public
	 * @param string $host[optional] = null
	 * @return CWmServerConsole
	 */
	function CWmServerConsole($host = null)
	{	
		$this->Settings =& WMSettings::CreateInstance();
		
		$this->_admHost = ($host !== null) ? $host : $this->Settings->Settings->WmServerHost;
		$this->_admPort = $this->Settings->AdminPort;
		$this->_admLogin = $this->Settings->AdminLogin;
		$this->_admPassword = $this->Settings->AdminPassword;
		$this->_log =& CLog::CreateInstance();
		
		if (!$this->Settings->IsLoad)
		{
			$this->_setError(str_replace('\\', '/', getGlobalError()));
		}
	}
	
	/**
	 * @access public
	 * @return bool
	 */
	function Connect()
	{
		return $this->OnlyConnect() && $this->AdmLogin();
	}
	
	/**
	 * @access public
	 * @return bool
	 */
	function Disconnect()
	{
		if ($this->_socket == false)
		{
			return true;
		}
		$this->Logout();
		@fclose($this->_socket);
		$this->_socket = false;
		return true;
	}
	
	/**
	 * @access public
	 * @return bool
	 */
	function Logout()
	{
		$this->_write('quit');
		return $this->_checkResponse($this->_readline(), 'Logout()', __LINE__);
	}
	
	/**
	 * @access public
	 * @return bool
	 */
	function OnlyConnect()
	{
		$errstr = '';
		$errno = 0;
		$connect_timeout = 10;
		
		if(!$this->_socket = @fsockopen($this->_admHost, $this->_admPort, $errno, $errstr, $connect_timeout))
		{
			$this->_setError('Connect() - Error: Can\'t connect to WebMail Server('. $this->_admHost.':'. $this->_admPort.'). '.CRLFCHARS.$errstr.' ('.$errno.')', __LINE__);
			return false;
		}	
		
		if (!$this->_checkResponse($this->_readline(), 'Connect()', __LINE__))
		{
			return false;
		}
		
 		@socket_set_timeout($this->_socket, 10, 0);
		@socket_set_blocking($this->_socket, true);
		
		return true;
	}

	/**
	 * @access public
	 * @return bool
	 */
	function AdmLogin()
	{
		$this->_write($this->_admLogin.TABCHAR.$this->_admPassword);
		return $this->_checkResponse($this->_readline(), 'AdmLogin()', __LINE__);
	}
	
	/**
	 * @param Account $account
	 * @return bool
	 */
	function UserConnect(&$account)
	{
		$domain = EmailAddress::GetDomainFromEmail($account->Email);
		$login = EmailAddress::GetAccountNameFromEmail($account->MailIncLogin);
		
		$usersArray = $this->UserList($domain, $login);
		
		return (count($usersArray) > 0 && count($usersArray[0]) > 3 && 
					$usersArray[0][0] == $domain && $usersArray[0][1] == $login &&
					$usersArray[0][2] == $account->MailIncPassword && $usersArray[0][3] == 'U');
	}
	
	/**
	 * @param Account $account
	 * @return bool
	 */
	function UserConnectAll(&$account)
	{
		//"userauth"[TAB]"domain"[TAB]"username"[TAB]"password"
		$domain = EmailAddress::GetDomainFromEmail($account->Email);
		$login = EmailAddress::GetAccountNameFromEmail($account->MailIncLogin);
		$this->_write('userauth'.TABCHAR.$domain.TABCHAR.$login.TABCHAR.$account->MailIncPassword);
		return $this->_checkResponse($this->_readline(), 'UserConnect()', __LINE__);
	}

	/**
	 * @access public
	 * @param string $newDomain
	 * @return bool
	 */
	function AddDomain($newDomainName)
	{
		//"domainadd"[TAB]"domain"
		$this->_write('domainadd'.TABCHAR.$newDomainName);
		return $this->_checkResponse($this->_readline(), 'AddDomain()', __LINE__);
	}
	
	/**
	 * @access public
	 * @param string $domain
	 * @return bool
	 */
	function DeleteDomain($domain)
	{
		//"domaindel"[TAB]"domain"
		$this->_write('domaindel'.TABCHAR.$domain);
		return $this->_checkResponse($this->_readline(), 'DeleteDomain()', __LINE__);
	}
	
	/**
	 * @access public
	 * @param string $domain
	 * @return bool
	 */
	function DomainExist($domain)
	{
		$domailArray = $this->DomainList();
		return in_array($domain, $domailArray);
	}
	
	/**
	 * @access public
	 * @return array
	 */
	function DomainList()
	{
		//"domainlist"
		$domainsArray = array();
		$this->_write('domainlist');
		$resp = trim($this->_readline());
		if ($this->_checkResponse($resp, 'DomainList()', __LINE__))
		{
			do 
			{
				$response = $this->_readline();
				if ($response === false)
				{
					break;
				}
				$response = trim($response);
				if ($response != '.')
				{
					$domainsArray[] = trim($response, '"');
				}
				else break;			
			} while ($response != '.');
		}
		
		return $domainsArray;
	}

	/**
	 * @access public
	 * @param string $domainName
	 * @param string $userName[optional] = null
	 * @return array
	 */
	function UserList($domainName, $userName = null)
	{
		$userName = ($userName) ? EmailAddress::GetAccountNameFromEmail($userName) : null;
		//"userlist"
		$usersArray = array();
		if ($domainName && $userName)
		{
			$this->_write('userlist'.TABCHAR.$domainName.TABCHAR.$userName);	
		}
		else
		{
			$this->_write('userlist'.TABCHAR.$domainName);
		}

		$resp = trim($this->_readline());
		if ($this->_checkResponse($resp, 'UserList()', __LINE__))
		{
			do 
			{
				$response = trim($this->_readline());
				if ($response != '.')
				{
					$tempUserArray = explode(TABCHAR, $response);
					$usersArray[] = array_map('myWmServerTrim', $tempUserArray);
				}
				else break;			
			} while ($response != '.');
		}
		
		return $usersArray;		
	}
	
	/**
	 * @access public
	 * @param string $domain
	 * @param string $username
	 * @param int $newSize
	 * @return bool
	 */
	function ChangeUserMaxMailBoxSize($domain, $username, $newSizeMb)
	{
		$username = EmailAddress::GetAccountNameFromEmail($username);
		if ($this->UserExist($domain, $username))
		{
			//"uservarsset"[TAB]"domain"[TAB]"username"[TAB]"varname"[TAB]"varvalue" ... <CR><LF>
			$this->_write('uservarsset'.TABCHAR.$domain.TABCHAR.$username.TABCHAR.'MaxMBSize'.TABCHAR.$newSizeMb);
			return $this->_checkResponse($this->_readline(), 'ChangeUserMaxMailBoxSize()', __LINE__);
		}
		else 
		{
			return false;
		}
	}
	
	/**
	 * @access public
	 * @param string $domain
	 * @param string $username
	 * @return bool
	 */
	function UserExist($domain, $username)
	{
		$username = EmailAddress::GetAccountNameFromEmail($username);
		$usersArray = $this->UserList($domain, $username);
		return (count($usersArray) > 0 && count($usersArray[0]) > 1 && 
					$usersArray[0][0] == $domain && $usersArray[0][1] == $username);
	}
	
	/**
	 * @access public
	 * @param string $domain
	 * @param string $username
	 * @param string $password
	 * @param string $usertype[optional] = "U"
	 * @return bool
	 */
	function AddUser($domain, $username, $password, $usertype = 'U')
	{
		$username = EmailAddress::GetAccountNameFromEmail($username);
		//"useradd"[TAB]"domain"[TAB]"username"[TAB]"password"[TAB]"usertype"
		$this->_write('useradd'.TABCHAR.$domain.TABCHAR.$username.TABCHAR.$password.TABCHAR.$usertype);
		return $this->_checkResponse($this->_readline(), 'AddUser()', __LINE__);		
	}
	
	/**
	 * @access public
	 * @param string $domain
	 * @param string $username
	 * @return bool
	 */
	function DeleteUser($domain, $username)
	{
		$username = EmailAddress::GetAccountNameFromEmail($username);
		//"userdel"[TAB]"domain"[TAB]"username"
		$this->_write('userdel'.TABCHAR.$domain.TABCHAR.$username);
		return $this->_checkResponse($this->_readline(), 'DeleteUser()', __LINE__);		
	}
	
	/**
	 * @access public
	 * @param string $domain
	 * @param string $username
	 * @param string $newPassword
	 * @return bool
	 */
	function ChangeUserPass($domain, $username, $newPassword)
	{
		$username = EmailAddress::GetAccountNameFromEmail($username);
		//"userpasswd"[TAB]"domain"[TAB]"username"[TAB]"password"
		$this->_write('userpasswd'.TABCHAR.$domain.TABCHAR.$username.TABCHAR.$newPassword);
		return $this->_checkResponse($this->_readline(), 'ChangeUserPass()', __LINE__);			
	}

	/**
	 * @return int/false
	 */
	function GetSMTPPort()
	{
		$var = $this->GetConfigFileVarValue('server.tab', 'SmtpPort');
		return ($var === false) ? false : (int) $var;
	}
	
	/**
	 * @access public
	 * @param string $file
	 * @param string $varname
	 * @return string/false
	 */
	function GetConfigFileVarValue($file, $varname)
	{
		//"cfgfileget"[TAB]"relative-file-path"
		$value = '';
		$this->_write('cfgfileget'.TABCHAR.$file);
		$resp = $this->_readline();
		if ($this->_checkResponse($resp, 'GetConfigFileVar()', __LINE__))
		{
			$lines = array();
			do 
			{
				$response = $this->_readline();
				if ($response === false)
				{
					break;
				}
				$response = trim($response);
				if ($response != '.')
				{
					$array = explode("\t", $response);
					if (is_array($array) && count($array) > 1 && trim($array[0], '"') == $varname)
					{
						$value = trim($array[1], '"');
					}
				}
				else break;			
			} while ($response != '.');
			
			return $value;
		}
		
		return false;
	}
	

	/**
	 * @access private
	 */
	function _cleanup()
	{
		if (is_resource($this->_socket))
		{
			@socket_set_blocking($this->_socket, false);
			@fclose($this->_socket);
			$this->_socket = false;
		}
	}
	
	/**
	 * @access private
	 * @param string $string
	 * @return bool
	 */
	function _write($string)
	{
		$this->_log->WriteLine('WMSERVER >>: '.$string);
		if(!@fwrite($this->_socket , $string.CRLFCHARS , strlen($string.CRLFCHARS)))
		{
			$this->_setError('_write() - Error while send "'.$string.'". Connection closed.', __LINE__);
			$this->_cleanup();
			return false;
		}

		return true;
	}
	
	/**
	 * @access private
	 * @param int $buffer_size[optional] = 512
	 * @return string
	 */
	function _readline($buffer_size = 512)
	{
		$buffer = @fgets( $this->_socket , $buffer_size );
		$this->_log->WriteLine('WMSERVER <<: '.trim($buffer));

		$this->socket_status = @socket_get_status($this->_socket);
		if(isset($this->socket_status["timed_out"]) && $this->socket_status["timed_out"])
		{
			$this->_setError('_readline() - Socket_Timeout_reached.', __LINE__);
			$this->_cleanup();
		    return false;
		}
		$this->socket_status = false;
		
		return $buffer;
	}
	
	/**
	 * @access private
	 * @param string $response
	 * @param string $functionName
	 * @return bool
	 */
	function _checkResponse($response, $functionName, $lineIsNeed = null)
	{
		if (strlen($response) < 1) 
		{
			$this->_setError($functionName.' - Error: response is null', $lineIsNeed);
			$this->_cleanup();			
			return false;
		}
		if (substr($response,0,1) == '-' )
		{
			$this->_setError($functionName.' - Error: '.$response, $lineIsNeed);
			$this->_cleanup();	
			return false;
		}
		else if (substr($response,0,1) == '+' )
		{
			return true;
		}
		else 
		{
			$this->_setError($functionName.' - Unknown Error: '.$response, $lineIsNeed);
			$this->_cleanup();
			return false;
		}
	}
	
	/**
	 * @access private
	 * @param string $errorDesc
	 */
	function _setError($errorDesc, $line = null)
	{
		$this->_error = ($this->_error) ? $this->_error : $errorDesc;
		$file = __FILE__;
		$line = ($line !== null) ? ' (~'.$line.')' : '';
		$this->_log->WriteLine('WMSERVER Error: '.$file.$line.CRLFCHARS.$errorDesc);
	}
	
	/**
	 * @access public
	 * @return string
	 */
	function GetError()
	{
		return $this->_error;
	}
}

	/**
	 * @access public
	 * @param string $string
	 * @return string
	 */
	function myWmServerTrim($string)
	{
		return trim($string, '"');
	}
	