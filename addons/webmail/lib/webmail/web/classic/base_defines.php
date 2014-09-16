<?php
	
	define('BASEFILE', 'basewebmail.php');
	define('ACTIONFILE', 'actions.php');
	define('LOGINFILE', 'index.php');
	
	if (!defined('INFORMATION')) define('INFORMATION', 'information');
	if (!defined('ISINFOERROR')) define('ISINFOERROR', 'infoErr');
	
	define('REPORT', 'action_report');

	define('CHANGE_ACCID', 'nacc');
	define('ATTACH_DIR', 'attachtempdir');
	define('SARRAY', 'sarray');
	
	define('SCREEN', 'screen');
	define('FOLDER_ID', 'cfolder');
	define('FOLDER_FULLNAME', 'cfolder_fn');
	define('PAGE', 'page');

	define('SEARCH_ARRAY', 'searchvars');
	define('S_GETMODE', 'smode');
	define('S_FOLDER', 's_folder');
	define('S_TEXT', 's_text');
	define('S_MODE', 's_mode');
	
	define('EDIT_ACCOUNT_ID', 'editacct');

	define('SCREEN_MAILBOX', 'mailbox');
	define('SCREEN_NEWOREDIT', 'new');
	define('SCREEN_SETTINGS', 'settings');
	define('SCREEN_CONTACTS', 'contacts');
	define('SCREEN_FULLSCREEN', 'full');
	
	define('GOTOFOLDER', 'gtf');
	
	define('SET_COMMON', 'scommon');
		define('SET_ACCOUNT_PROF', 'saccount');
		define('SET_ACCOUNT_FILTERS', 'sfilters');
		define('SET_ACCOUNT_SIGNATURE', 'ssignature');
		define('SET_ACCOUNT_MFOLDERS', 'smfolders');
		define('SET_ACCOUNT_ADDACC', 'saddaccount');
	define('SET_CONTACTS', 'scontacts');
	
	
	define('S_GETMODECONTACT', 'sc_mode');
	define('CONTACT_ORD', 'c_ord');
	define('CONTACT_FLD', 'c_fld');
	define('CONTACT_PAGE', 'c_page');
	define('CONTACT_MODE', 'c_mode');
		define('C_NONE', 0);
		define('C_VIEW', 1);
		define('C_NEW', 2);
		define('G_VIEW', 3);
		define('G_NEW', 4);
		define('C_IMPORT', 5);
	define('CONTACT_ID', 'cid');
	
	// static
	class Post
	{
		function has($key) { return isset($_POST[$key]); }
		function val($key, $default = null) { return Post::has($key) ? $_POST[$key] : $default;	}
	}
	
	// static
	class Get
	{
		function has($key) { return isset($_GET[$key]); }
		function val($key, $default = null)	{ return Get::has($key) ? $_GET[$key] : $default; }
	}
	
	// static
	class Session
	{
		function has($key) { return isset($_SESSION[$key]); }
		function val($key, $default = null)	{ return Session::has($key) ? $_SESSION[$key] : $default; }
	}
	
	function GetFriendlySize($byteSize)
	{
		$size = ceil($byteSize / 1024);
		$mbSize = $size / 1024;
		$size = ($mbSize > 1) ? (ceil($mbSize*10)/10).''.JS_LANG_Mb: $size.''.JS_LANG_Kb;
		return $size;
	}
	
	/**
	 * @param string $text
	 * @param string $url
	 */
	function SetError($text, $url = null)
	{
		$_SESSION[INFORMATION] = $text;
		$_SESSION[ISINFOERROR] = true;	

		if ($url)
		{
			header('Location: '.$url);
		}
		else 
		{
			header('Location: '.BASEFILE);
		}
		exit();
	}

	/**
	 * @param string $text
	 */
	function SetReport($text)
	{
		$_SESSION[REPORT] = $text;
	}
	
	/**
	 * @param string $text
	 */
	function SetOnlineError($text = null)
	{
		if ($text === null)
		{
			$_SESSION[INFORMATION] = getGlobalError();	
		}
		else {
			$_SESSION[INFORMATION] = $text;
			if (isset($GLOBALS[ErrorDesc])) 
			{
				$_SESSION[INFORMATION] .= "\r\n".getGlobalError();
			}
		}
		$_SESSION[ISINFOERROR] = true;	
	}

	function GetAttachImg($filename)
	{		
		$filename = strtolower($filename);
		$pos = strrpos($filename,'.');
		$ex = @substr($filename, $pos+1, strlen($filename)-$pos+1);
		switch ($ex)
		{
			case 'asp':
			case 'asa':
			case 'inc':
				return 'application_asp.gif';
				break;
			case 'css':
				return 'application_css.gif';
				break;
			case 'doc':
				return 'application_doc.gif';
				break;
			case 'html':
			case 'shtml':
			case 'phtml':
			case 'htm':
				return 'application_html.gif';
				break;
			case 'pdf':
				return 'application_pdf.gif';
				break;
			case 'xls':
				return 'application_xls.gif';
				break;
			case 'bat':
			case 'exe':
			case 'com':
				return 'executable.gif';
				break;
			case 'bmp':
				return 'image_bmp.gif';
				break;
			case 'gif':
				return 'image_gif.gif';
				break;
			case 'jpg':
			case 'jpeg':
				return 'image_jpeg.gif';
				break;
			case 'tiff':
			case 'tif':
				return 'image_tiff.gif';
				break;
			case 'txt':
				return 'text_plain.gif';
				break;
			default:
				return 'attach.gif';
				break;
		}
	}
