<?php

function GetStrReplacement($Str, &$Rep)
{
	static $Count = 0;
	$Rep[$Count] = stripslashes($Str);
	return "##string_replacement{".($Count++)."}##";
}

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	require_once(WM_ROOTPATH.'mime/class_mailmessage.php');
	require_once(WM_ROOTPATH.'common/class_collectionbase.php');
  
	define('MESSAGEFLAGS_None', 0);
	define('MESSAGEFLAGS_Seen', 1);
	define('MESSAGEFLAGS_Answered', 2);
	define('MESSAGEFLAGS_Flagged', 4);
	define('MESSAGEFLAGS_Deleted', 8);
	define('MESSAGEFLAGS_Draft', 16);
	define('MESSAGEFLAGS_Recent', 32);
	
	define('MESSAGEFLAGS_Forwarded', 256);
	define('MESSAGEFLAGS_Grayed', 512);
	
	define('MESSAGEPRIORITY_High', 1);
	define('MESSAGEPRIORITY_Normal', 3);
	define('MESSAGEPRIORITY_Low', 5);
	
	class WebMailMessage extends MailMessage
	{
		/**
		 * @var int
		 */
		var $IdMsg = -1;
		
		/**
		 * @var int
		 */
		var $IdFolder = -1;
		
		/**
		 * @var string
		 */
		var $Uid = -1;
		
		/**
		 * @var int
		 */
		var $Size;
		
		/**
		 * @var int
		 */
		var $Flags = MESSAGEFLAGS_None;
		
		/**
		 * @var bool
		 */
		var $DbHasAttachments = null;
		
		/**
		 * @var short
		 */
		var $DbPriority = 0;
		
		/**
		 * @var bool
		 */
		var $DbXSpam = null;
		
		/**
		 * @var int
		 */
		var $Charset = -1;
		
		/**
		 * @return short
		 */
		function GetPriorityStatus()
		{
			if ($this->DbPriority > 0)
			{
				return $this->DbPriority;
			}
			
			$priority = MailMessage::GetPriority();
			
			switch (strtolower($priority))
			{
				case 'high':
				case '1 (highest)':
				case '2 (high)':
				case '1':
				case '2':
					return MESSAGEPRIORITY_High;
					
				case 'low':
				case '4 (low)':
				case '5 (lowest)':
				case '4':
				case '5':
					return MESSAGEPRIORITY_Low;
			}
			
			return MESSAGEPRIORITY_Normal;
		}
		
		/**
		 * @return bool
		 */
		function GetXSpamStatus()
		{
			if ($this->DbXSpam !== null)
			{
				return $this->DbXSpam;
			}
			
			$xSpamValue = $this->Headers->GetHeaderValueByName(MIMEConst_XSpamLower);
			
			return (strtolower($xSpamValue) == 'probable spam' || strtolower($xSpamValue) == 'suspicious');
		}
		
		
		/**
		 * @return bool
		 */
		function HasAttachments()
		{
			if ($this->DbHasAttachments !== null)
			{
				return $this->DbHasAttachments;
			}
			
			return MailMessage::HasAttachments();
		}
		
		/**
		 * @param bool $isClear optional
		 * @return string
		 */
		function GetFromAsString($isClear = false)
		{
			$value = MailMessage::GetFromAsString();
			if ($isClear) 
			{	
				$value = WebMailMessage::ClearForSend($value);
			}
			return $value;
		}
		
		/**
		 * @param bool $isClear optional
		 * @return string
		 */
		function GetToAsString($isClear = false)
		{
			$emails = &$this->GetTo();
			$out = $emails->ToDecodedString();
			
			return ($isClear) ? WebMailMessage::ClearForSend($out) : $out;
		}
		
		/**
		 * @param bool $isClear optional
		 * @return string
		 */
		function GetToAsStringForSend($isClear = true)
		{
			$emails = &$this->GetTo();
			$out = $emails->ToFriendlyString();
			
			return ($isClear) ? WebMailMessage::ClearForSend($out) : $out;
		}
		
		
		/**
		 * @param bool $isClear optional
		 * @return string
		 */
		function GetCcAsString($isClear = false)
		{
			/*$value = MailMessage::GetCcAsString();
			if ($isClear) 
			{	
				$value = WebMailMessage::ClearForSend($value);
			}
			return $value;*/
			
			$emails = &$this->GetCc();
			$out = $emails->ToDecodedString();
			
			return ($isClear) ? WebMailMessage::ClearForSend($out) : $out;
		}
		
		/**
		 * @param bool $isClear optional
		 * @return string
		 */
		function GetCcAsStringForSend($isClear = true)
		{
			$emails = &$this->GetCc();
			$out = $emails->ToFriendlyString();
			
			return ($isClear) ? WebMailMessage::ClearForSend($out) : $out;
		}
		
		/**
		 * @param bool $isClear optional
		 * @return string
		 */
		function GetBccAsString($isClear = false)
		{
			/*$value = MailMessage::GetBccAsString();
			if ($isClear) 
			{	
				$value = WebMailMessage::ClearForSend($value);
			}
			return $value;*/
			
			$emails = &$this->GetBcc();
			$out = $emails->ToDecodedString();
			
			return ($isClear) ? WebMailMessage::ClearForSend($out) : $out;
		}

		/**
		 * @param bool $isClear optional
		 * @return string
		 */
		function GetBccAsStringForSend($isClear = true)
		{
			$emails = &$this->GetBcc();
			$out = $emails->ToFriendlyString();
			
			return ($isClear) ? WebMailMessage::ClearForSend($out) : $out;
		}
		
		/**
		 * @param bool $isClear optional
		 * @return string
		 */
		function GetSubject($isClear = false)
		{
			$value = MailMessage::GetSubject();
			if ($isClear) 
			{	
				$value = WebMailMessage::ClearForSend($value);
			}
			return $value;
		}
		
		/**
		 * @param bool $isClear optional
		 * @return string
		 */
		function GetFromAsStringForSend($isClear = true)
		{
			$email = &$this->GetFrom();
			$out = $email->ToFriendlyString();
			return ($isClear) ? WebMailMessage::ClearForSend($out) : $out;
		}
		
		/**
		 * @param bool $isClear optional
		 * @return string
		 */
		function GetReplyToAsString($isClear = false)
		{
			$value = MailMessage::GetReplyToAsString();
			if ($isClear) 
			{	
				$value = WebMailMessage::ClearForSend($value);
			}
			return $value;
		}
		
		/**
		 * @param bool $isClear optional
		 * @return string
		 */
		function GetReplyToAsStringForSend($isClear = true)
		{
			$emails = &$this->GetReplyTo();
			$out = $emails->ToFriendlyString();
			
			return ($isClear) ? WebMailMessage::ClearForSend($out) : $out;
		}				

		
		/**
		 * @param bool $onlyTo optional
		 * @return string
		 */
		function GetAllRecipientsEmailsAsString($onlyTo = false)
		{
			$emails = '';
			$emailCollection = &$this->GetAllRecipients($onlyTo);
			foreach (array_keys($emailCollection->Instance()) as $key)
			{
				$email = $emailCollection->Get($key);
				$emails .= ($emails == '') ? $email->Email : ', '.$email->Email;
			}
			
			return $emails;
		}
		
		/**
		 * @param string $value
		 * @return string
		 */
		function ClearForSend($value)
		{
			if ((isset($GLOBALS[MailOutputCharset]) ? $GLOBALS[MailOutputCharset] : '') == CPAGE_UTF8)
			{
				$value = ConvertUtils::ClearUtf8($value);
			}
			return $value;
		}
		
		/**
		 * @param bool $replaceSpecialHtmlChars optional
		 * @return string
		 */
		function GetCensoredHtmlBody($replaceSpecialHtmlChars = false)
		{
			$Body = $this->TextBodies->HtmlTextBodyPart;
			$ToRemoveArray = array (
				"'<!doctype[^>]*>'si",
				"'<html[^>]*>'si",
				"'</html>'si",
				"'<body[^>]*>'si",
				"'<link[^>]*>'si",
				"'</body>'si",
				"'<base[^>]*>'si",
				"'<title[^>]*>.*?</title>'si",
				"'<style[^>]*>.*?</style>'si",
				"'<script[^>]*>.*?</script>'si",
				"'</script>'si",
				"'<object[^>]*>.*?</object>'si",
				"'<embed[^>]*>.*?</embed>'si",
				"'<applet[^>]*>.*?</applet>'si",
				"'<mocha[^>]*>.*?</mocha>'si",
				"'<meta[^>]*>'si",
			);
			$Body = preg_replace($ToRemoveArray, '', $Body);
			$Body = preg_replace("|href=\"(.*)script:|i", 'href="php_mail_removed_script:', $Body);
			$Body = preg_replace("|<([^>]*)&{.*}([^>]*)>|i", "<&{;}\\3>", $Body);
			$Body = preg_replace("/\x0D\x0A\t+/", "\x0D\x0A", $Body);
			$Body = preg_replace_callback('/<a[^>]+/i', 'targetAdd', $Body);
			
			if ($replaceSpecialHtmlChars) 
			{
				if ($GLOBALS[MailOutputCharset] == CPAGE_UTF8)
				{
					$Body = ConvertUtils::ClearUtf8($Body);
				}
			}
			return $Body;
		}
		
		/**
		 * @param bool $replaceSpecialHtmlChars optional
		 * @return string
		 */
		function GetCensoredTextBody($replaceSpecialHtmlChars = false)
		{
			$Body = $this->TextBodies->PlainTextBodyPart;
			$Body = str_replace("\r", '', $Body);
			$ReplaceStrings = array();
			$Pattern = "/(http|https|ftp|telnet|gopher|news|file|wais):\/\/([a-zA-Z0-9+-=%&@:_\.~?]+[#a-zA-Z0-9+]*)/ie";
			$Replace = "GetStrReplacement('<a href=\"\\1://\\2\" target=\"_blank\">\\1://\\2</a>', \$ReplaceStrings)";
			$Body = preg_replace($Pattern, $Replace, $Body);
			$Body = htmlspecialchars($Body);
//			$Body = preg_replace("/([0-9a-zA-Z]([-+_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,})/", "<a href=\"mailto:\\1\" target=\"_blank\">\\1</a>", $Body); 
			$Body = preg_replace("/([0-9a-zA-Z_+\.\-]+@[0-9a-zA-Z_+\.\-]+\.[a-zA-Z]{2,})/", "<a href=\"mailto:\\1\" target=\"_blank\">\\1</a>", $Body);  
			for($i=0, $c=count($ReplaceStrings); $i<$c; $i++)
			{
				$Body = str_replace('##string_replacement{'.$i.'}##', $ReplaceStrings[$i], $Body);
			}
			$BodyArray = explode("\n", $Body);
			$Body = '';
			foreach ($BodyArray as $BodyPart)
			{
				if (preg_match("/^.{0,19}&gt;/", $BodyPart))
				{
					$BodyPart = '<font class="wm_message_body_quotation">'.trim($BodyPart).'</font>';
				}
				$Body .= trim($BodyPart).'<br />';
			}
			$Body = str_replace('  ', "&nbsp;&nbsp;", $Body);
			
			if ($replaceSpecialHtmlChars) 
			{
				$Body = WebMailMessage::ClearForSend($Body);
			}
			return $Body;
		}
		
		/**
		 * @param bool $replaceSpecialHtmlChars optional
		 * @return string
		 */
		function GetNotCensoredTextBody($replaceSpecialHtmlChars = false)
		{
			if ($this->HasPlainText())
			{
				$Body = $this->TextBodies->PlainTextBodyPart;	
			}
			else 
			{
				$Body = $this->TextBodies->HtmlToPlain();
			}
			
			if ($replaceSpecialHtmlChars) 
			{
				$Body = WebMailMessage::ClearForSend($Body);
			}
			return $Body;
		}
		
		/**
		 * @return string
		 */
		function GetPlainLowerCaseBodyText()
		{
			$mailText = '';
			if ($this->TextBodies->PlainTextBodyPart != '')
			{
				$mailText = $this->TextBodies->PlainTextBodyPart;
			}
			elseif ($this->TextBodies->HtmlTextBodyPart != '')
			{
				$mailText = $this->TextBodies->HtmlToPlain();
			}
			
			$mailText = preg_replace('/[\s]+/', ' ', str_replace(array(CRLF, "\t", "\r", "\n"), ' ', $mailText));
			
			$i18String = &new I18nString($mailText, $GLOBALS[MailOutputCharset]);
			return $i18String->ToLower($GLOBALS[MailOutputCharset]);
		}
		
		/**
		 * @param bool $replaceSpecialHtmlChars optional
		 * @return string
		 */
		function GetCensoredHtmlWithImageLinks($replaceSpecialHtmlChars = false)
		{
			$imgUrl = 'attach.php?tn=';

			$text = $this->GetCensoredHtmlBody($replaceSpecialHtmlChars);
			
			if ($this->Attachments->Count() == 0)
			{
				return $text;
			}
			
			for ($i = 0, $count = $this->Attachments->Count(); $i < $count; $i++)
			{
				$attach = &$this->Attachments->Get($i);
				$contentLocation = $attach->MimePart->GetContentLocation();
				$contentId = $attach->MimePart->GetContentID();

				$patternArray = array('cid:'.$contentId, 'CID:'.$contentId, 'Cid:'.$contentId);
				
				if ($contentId != '')
				{
					$text = str_replace($patternArray, $imgUrl.$this->IdMsg.'-'.$i.'_'.$attach->GetTempName(), $text);
				}
				
				if ($contentLocation != '' && $contentId == '')
				{
					$text = str_replace($contentLocation, $imgUrl.$this->IdMsg.'-'.$i.'_'.$attach->GetTempName(), $text);
				}
			}

			return $text;
		}
		
		/**
		 * @return int
		 */
		function GetMailSize()
		{
			return ($this->Size) ? $this->Size : strlen($this->TryToGetOriginalMailMessage());
		}
		
		/**
		 * @param bool $isClear optional
		 * @return string
		 */
		function GetRelpyAsHtml($isClear = false)
		{
			$result = '<br /><blockquote style="border-left: solid 2px #000000; margin-left: 5px; padding-left: 5px">';
			$result .= '---- Original Message ----<br />';
			$result .= '<b>From</b>: '.ConvertUtils::WMHtmlSpecialChars($this->GetFromAsString()).'<br />';
			$result .= '<b>To</b>: '.ConvertUtils::WMHtmlSpecialChars($this->GetToAsString()).'<br />';
			$cc = ConvertUtils::WMHtmlSpecialChars($this->GetCcAsString());
			if ($cc)
			{
				$result .= '<b>Cc</b>: '.$cc.'<br />';	
			}
			
			$date = &$this->GetDate();
			$result .= '<b>Sent</b>: '.$date->GetAsStr().'<br />';
			$result .= '<b>Subject</b>: '.ConvertUtils::WMHtmlSpecialChars($this->GetSubject()).'<br /><br />';
			
			if ($this->HasHtmlText())
			{
				$result .= $this->GetCensoredHtmlWithImageLinks(true).'</blockquote>';
			}
			else 
			{
				$result .= nl2br(ConvertUtils::WMHtmlSpecialChars($this->TextBodies->PlainTextBodyPart)).'</blockquote>';
			}	

			return ($isClear) ?  $this->ClearForSend($result) : $result;			
		}
			
		/**
		 * @param bool $replaceSpecialHtmlChars optional
		 * @return string
		 */
		function GetRelpyAsPlain($replaceSpecialHtmlChars = false)
		{
			$result = CRLF.'---- Original Message ----'.CRLF;
			$result .= 'From: '.$this->GetFromAsString().CRLF;
			$result .= 'To: '.$this->GetToAsString().CRLF;
			$cc = $this->GetCcAsString();
			if ($cc)
			{
				$result .= 'Cc: '.$cc.CRLF;	
			}
			
			$date = &$this->GetDate();
			$result .= 'Sent: '.$date->GetAsStr().CRLF;
			$result .= 'Subject: '.$this->GetSubject().CRLF.CRLF;
			if ($this->HasPlainText())
			{
				$result .= $this->TextBodies->PlainTextBodyPart;	
			}
			else 
			{
				$result .= $this->TextBodies->HtmlToPlain();
			}
			
			$result = str_replace("\n", "\n>", $result);
			
			if ($replaceSpecialHtmlChars)
			{
				$result = $this->ClearForSend($result);
			}
			
			return $result;	
		}
		
	}
	
	class WebMailMessageCollection extends CollectionBase
	{
		/**
		 * @return WebMailMessageCollection
		 */
		function WebMailMessageCollection()
		{
			CollectionBase::CollectionBase();
		}
		
		/**
		 * @param WebMailMessage $message
		 */
		function Add(&$message)
		{
			if ($message) $this->List->Add($message);
		}
	
		/**
		 * @param int $index
		 * @return WebMailMessage
		 */
		function &Get($index)
		{
			return $this->List->Get($index);
		}
		
	
	}
	
	function targetAdd($array)
	{
		if (is_array($array) && count($array) > 0)
		{
			$temp = $array[0];
			$tempLower = strtolower($temp);
			$sharpStart = strpos($tempLower, '#');
			$sharpHas = false;
			$equallyHas = false;
			$urlHas = false;
			
			$addTarget = true;
			
			if ($sharpStart !== false)
			{	
				$hrefStart = strpos($tempLower, 'href');
				if ($hrefStart !== false && $hrefStart > 0)
				{
					for ($i = $hrefStart + 4, $l = strlen($temp); $i < $l; $i++)
					{
						
						if ($equallyHas && $sharpHas && !$urlHas)
						{
							$addTarget = false;
							break;
						}
						
						$char = $temp{$i};		
						if (!$equallyHas && $char == '=') 
						{
							$equallyHas = true;
							continue;
						}
						
						if ($equallyHas && ($char != ' ' && $char != '"' && $char != '\'' && $char != '#'))
						{
							$urlHas = true;
							continue;
						}
						
						if ($equallyHas && $char == '#')
						{
							$sharpHas = true;
							continue;
						}
					}
				}
			}
			
			if ($addTarget)
			{
				return '<a target="_blank" '.substr($temp, 3);
			}
			else 
			{
				return $temp;
			}
		}
	}