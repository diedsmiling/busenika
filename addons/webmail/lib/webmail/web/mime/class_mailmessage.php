<?php

	/**
	 * @package WebMailPro
	 * @subpackage Mime
	 */

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	require_once(WM_ROOTPATH.'mime/inc_constants.php');
	require_once(WM_ROOTPATH.'mime/class_mimepart.php');
	require_once(WM_ROOTPATH.'mime/class_emailaddress.php');
	require_once(WM_ROOTPATH.'mime/class_emailaddresscollection.php');
	require_once(WM_ROOTPATH.'mime/class_attachmentcollection.php');
	require_once(WM_ROOTPATH.'mime/class_textbodycollection.php');
	require_once(WM_ROOTPATH.'common/class_datetime.php');
	require_once(WM_ROOTPATH.'common/class_i18nstring.php');

	
	class MailMessage extends MimePart
	{
		/**
		 * @var AttachmentCollection
		 */
		var $Attachments = null;
		
		/**
		 * @var TextBodyCollection
		 */
		var $TextBodies = null;
		
		/**
		 * @var bool
		 */
		var $IsMixed = false;
		
		/**
		 * @var bool
		 */
		var $IsAlternative = false;
		
		/**
		 * @var string
		 */
		var $OriginalMailMessage;
		
		/**
		 * @var bool
		 */
		var $HasCharset = false;
		
		/**
		 * @return EmailAddress
		 */
		function &GetFrom()
		{
			$emailAdress = new EmailAddress();
			$emailAdress->Parse($this->Headers->GetHeaderDecodedValueByName(MIMEConst_FromLower));
			return $emailAdress;
		}
		
		/**
		 * @return string
		 */
		function GetFromAsString()
		{
			return $this->Headers->GetHeaderDecodedValueByName(MIMEConst_FromLower);
		}
		

		/**
		 * @param EmailAddressCollection $value
		 */
		function SetFrom($value)
		{
			$this->Headers->SetHeaderByName(MIMEConst_From, $value->ToDecodedString());
		}
		
		/**
		 * @param string $value
		 */
		function SetFromAsString($value)
		{
			$this->Headers->SetHeader(new Header(MIMEConst_From, $value, true));
		}
		
		
		/**
		 * @return EmailAddressCollection
		 */
		function &GetTo()
		{
			$emails = &new EmailAddressCollection($this->Headers->GetHeaderValueByName(MIMEConst_ToLower));
			return $emails;
		}

		/**
		 * @return string
		 */
		function GetToAsString()
		{
			return $this->Headers->GetHeaderDecodedValueByName(MIMEConst_ToLower);
		}

		/**
		 * @param EmailAddressCollection $value
		 */
		function SetTo($value)
		{
			$this->Headers->SetHeaderByName(MIMEConst_To, $value->ToDecodedString());
		}
		
		/**
		 * @param string $value
		 */
		function SetToAsString($value)
		{
			$this->Headers->SetHeader(new Header(MIMEConst_To, $value, true));
		}
		
		/**
		 * @return EmailAddressCollection
		 */
		function &GetCc()
		{
			$emails = &new EmailAddressCollection($this->Headers->GetHeaderValueByName(MIMEConst_CcLower));
			return $emails;			
		}

		/**
		 * @return string
		 */
		function GetCcAsString()
		{
			return $this->Headers->GetHeaderDecodedValueByName(MIMEConst_CcLower);
		}
		
		/**
		 * @param EmailAddressCollection $value
		 */
		function SetCc($value)
		{
			$this->Headers->SetHeaderByName(MIMEConst_Cc, $value->ToDecodedString());
		}
		
		/**
		 * @param string $value
		 */
		function SetCcAsString($value)
		{
			$this->Headers->SetHeader(new Header(MIMEConst_Cc, trim($value), true));
		}
		

		/**
		 * @return EmailAddressCollection
		 */
		function &GetBcc()
		{
			$emails = &new EmailAddressCollection($this->Headers->GetHeaderValueByName(MIMEConst_BccLower));;
			return $emails;			
		}

		/**
		 * @return string
		 */
		function GetBccAsString()
		{
			return $this->Headers->GetHeaderDecodedValueByName(MIMEConst_BccLower);
		}

		/**
		 * @param EmailAddressCollection $value
		 */
		function SetBcc($value)
		{
			$this->Headers->SetHeaderByName(MIMEConst_Bcc, $value->ToDecodedString());
		}
		
		/**
		 * @param string $value
		 */
		function SetBccAsString($value)
		{
			$this->Headers->SetHeader(new Header(MIMEConst_Bcc, $value, true));
		}
		
		/**
		 * @return EmailAddressCollection
		 */
		function &GetReplyTo()
		{
			$emails = &new EmailAddressCollection($this->Headers->GetHeaderValueByName(MIMEConst_ReplyToLower));
			return $emails;
		}

		/**
		 * @return string
		 */
		function GetReplyToAsString()
		{
			return $this->Headers->GetHeaderDecodedValueByName(MIMEConst_ReplyToLower);
		}

		/**
		 * @param EmailAddressCollection $value
		 */
		function SetReplyTo($value)
		{
			$this->Headers->SetHeaderByName(MIMEConst_Bcc, $value->ToDecodedString());
		}
		
		/**
		 * @param string $value
		 */
		function SetReplyToAsString($value)
		{
			$this->Headers->SetHeader(new Header(MIMEConst_ReplyToLower, $value, true));
		}
		
		/**
		 * @return string
		 */
		function GetSubject()
		{
			return str_replace(array("\n","\r","\t"), '', $this->Headers->GetHeaderDecodedValueByName(MIMEConst_SubjectLower));
		}

		/**
		 * @param string $value
		 */
		function SetSubject($value)
		{
			$this->Headers->SetHeader(new Header(MIMEConst_Subject, $value, true));
		}
		
		/**
		 * @return DateTime
		 */
		function &GetDate()
		{
			$dt = $this->GetDateReceived();
			if ($dt == '')
			{
				$dt = $this->Headers->GetHeaderValueByName(MIMEConst_DateLower);
			}

			$return = &CDateTime::CreateFromStr(trim($dt));
			return $return;
		}
		
		/**
		 * @param CDateTime $date
		 */
		function SetDate(&$date)
		{
			$this->Headers->SetHeader(new Header(MIMEConst_Date, $date->GetAsStr(), true));
		}
		
		/**
		 * @return string
		 */
		function GetDateReceived()
		{
			$date = '';
			$receiv = $this->Headers->GetHeadersValuesByName(MIMEConst_Received);
			foreach ($receiv as $value)
			{
				if (strpos($value, ';') !== false)
				{
					$receivedArr = explode(';', $value);
					if (preg_match('/ [\d]{4} [\d]{2}:[\d]{2}:[\d]{2} /', $receivedArr[count($receivedArr)-1]))
					{
						$date = trim($receivedArr[count($receivedArr)-1]);
						break;
					}
				}
			}
			return $date;
		}

		/**
		 * @return string
		 */
		function GetPriority()
		{
			$header = &$this->Headers->GetHeaderByName(MIMEConst_XMSMailPriorityLower);
			if ($header != null)
			{
				return $header->Value;
			}

			$header = &$this->Headers->GetHeaderByName(MIMEConst_ImportanceLower);
			if ($header != null)
			{
				return $header->Value;
			}
			
			$header = &$this->Headers->GetHeaderByName(MIMEConst_XPriorityLower);
			if ($header != null)
			{
				return $header->Value;
			}
			
			return '';
		}
		
		/**
		 * @param int $value
		 */
		function SetPriority($value)
		{
			switch ($value)
			{
				case 1:
					$value .= ' (Highest)';
					break;
				case 2:
					$value .= ' (High)';
					break;
				case 3:
					$value .= ' (Normal)';
					break;
				case 4:
					$value .= ' (Low)';
					break;
				case 5:
					$value .= ' (Lowest)';
					break;
			}
			
			$this->Headers->SetHeaderByName(MIMEConst_XPriority, $value);
		}
		
		/**
		 * @param string $rawData
		 * @return MailMessage
		 */
		function MailMessage($rawData = null, $holdOriginalBody = false)
		{
			@ini_set('memory_limit', MEMORYLIMIT);
			@set_time_limit(TIMELIMIT);
			
			$GLOBALS[MailInputCharset] = (isset($GLOBALS[MailInputCharset])) ? $GLOBALS[MailInputCharset] : '';
			MimePart::MimePart($rawData);
			$null = null;
			$this->Attachments = new AttachmentCollection($null);
			$this->TextBodies = new TextBodyCollection($null);
			//$this->Headers->SetHeaderByName(MIMEConst_XMailer, 'MailBee WebMail Pro');
			
			$this->OriginalMailMessage = '';
			if ($rawData)
			{
				if ($holdOriginalBody)
				{
					$this->OriginalMailMessage =& $rawData;
				}
				$this->_setAllParams();
			}
		}
		
		/**
		 * @return EmailAddressCollection
		 */
		function &GetAllRecipients($onlyTo = false, $addReply = false)
		{
			$emails = array();
			$allRecipients = &new EmailAddressCollection();
			$toAddr = &$this->GetTo();
			foreach (array_keys($toAddr->Instance()) as $key)
			{
				$temp = $toAddr->Get($key);
				if ($temp && !in_array($temp->Email, $emails))
				{
					$emails[] = $temp->Email;
					$allRecipients->AddEmailAddress($temp);
				}
			}
			
			if ($allRecipients->Count() > 0 && $onlyTo)
			{
				return $allRecipients;
			}
			
			$toCc = &$this->GetCc();
			foreach (array_keys($toCc->Instance()) as $key)
			{
				$temp = $toCc->Get($key);
				if ($temp && !in_array($temp->Email, $emails))
				{
					$emails[] = $temp->Email;
					$allRecipients->AddEmailAddress($temp);
				}
			}
			
			$toBcc = &$this->GetBcc();
			foreach (array_keys($toBcc->Instance()) as $key)
			{
				$temp = $toBcc->Get($key);
				if ($temp && !in_array($temp->Email, $emails))
				{
					$emails[] = $temp->Email;
					$allRecipients->AddEmailAddress($temp);
				}
			}
			
			if ($addReply)
			{
				$toReply = &$this->GetReplyTo();
				if ($toReply->Count() > 0)
				{
					foreach (array_keys($toReply->Instance()) as $key)
					{
						$temp = $toReply->Get($key);
						if ($temp && !in_array($temp->Email, $emails))
						{
							$emails[] = $temp->Email;
							$allRecipients->AddEmailAddress($temp);
						}
					}				
				}
				else 
				{
					$toFrom =& $this->GetFrom();
					if ($toFrom && !in_array($toFrom->Email, $emails))
					{
						$emails[] = $toFrom->Email;
						$allRecipients->AddEmailAddress($toFrom);
					}
				}
			}
			
			return $allRecipients;
		}
		
		/**
		 * @return MimePartCollection
		 */
		function &GetSubParts()
		{
			return $this->_subParts;
		}
		
		/**
		 * Loads the message from the specified file.
		 * @param string $filename
		 * @param bool $holdOriginalBody optional
		 * @return bool
		 */
		function LoadMessageFromEmlFile($filename, $holdOriginalBody = false)
		{
			$handle = @fopen($filename, 'rb');
			if ($data = @fread($handle, 3))
			{
				if ($data === "\xEF\xBB\xBF")
				{
					$data = '';
					$GLOBALS[MailDefaultOriginalCharset] = $GLOBALS[MailDefaultCharset];
					$GLOBALS[MailDefaultCharset] = CPAGE_UTF8;
					$GLOBALS[MailInputCharset] = CPAGE_UTF8;
					$this->HasCharset = true;
				}
				elseif (isset($GLOBALS[MailDefaultOriginalCharset]))
				{
					$GLOBALS[MailDefaultCharset] = $GLOBALS[MailDefaultOriginalCharset];
				}
				
				$data .= @fread($handle, filesize($filename)-3);
				$this->OriginalMailMessage = '';
				if ($holdOriginalBody) 
				{
					$this->OriginalMailMessage = &$data;
				}
				$this->Parse($data);
				unset($data);
				@fclose($handle);
				$this->_setAllParams();
				return true;
			}
			return false;
		}
		
		/**
		 * Loads the message from the specified string.
		 * @param string $messageRawBody
		 * @param bool $holdOriginalBody optional
		 */
		function LoadMessageFromRawBody(&$messageRawBody, $holdOriginalBody = false)
		{
			if (substr($messageRawBody, 0, 3) === "\xEF\xBB\xBF")
			{
				$GLOBALS[MailDefaultOriginalCharset] = $GLOBALS[MailDefaultCharset];
				$GLOBALS[MailDefaultCharset] = CPAGE_UTF8;
				$messageRawBody = substr($messageRawBody, 3);
				$GLOBALS[MailInputCharset] = CPAGE_UTF8;
				$this->HasCharset = true;
			}
			elseif (isset($GLOBALS[MailDefaultOriginalCharset]))
			{
				$GLOBALS[MailDefaultCharset] = $GLOBALS[MailDefaultOriginalCharset];
			}
			
			$this->OriginalMailMessage = '';
			if ($holdOriginalBody)
			{
				$this->OriginalMailMessage = &$messageRawBody;
			}
		
			$this->Parse($messageRawBody);
			unset($messageRawBody);
			$this->_setAllParams();
		}
		
		/**
		 * @access private
		 */
		function _setAllParams()
		{
			$contentType = $this->GetContentTypeCharset();
			if ($contentType && strlen($contentType) > 0) $this->HasCharset = true;
			$GLOBALS[MailInputCharset] = (isset($GLOBALS[MailInputCharset]) && $GLOBALS[MailInputCharset] != '') ? $GLOBALS[MailInputCharset] : $this->GetContentTypeCharset();
			if ($GLOBALS[MailInputCharset]) $this->HasCharset = true;
			$GLOBALS[MailInputCharset] = ($GLOBALS[MailInputCharset]) ? $GLOBALS[MailInputCharset] : $GLOBALS[MailDefaultCharset];
			if ($this->IsMimeMail())
			{
				$this->ReparseAllHeader($GLOBALS[MailInputCharset]);
				$this->TextBodies = new TextBodyCollection($this);
				$this->Attachments = new AttachmentCollection($this);
			}
			else 
			{
				$this->ReparseAllHeader($GLOBALS[MailInputCharset]);
				
				preg_match('/\nbegin [\d]* ([.]*)/i', $this->_body, $preg);
				if (count($preg) > 0)
				{
					$firstBegin = strpos($this->_body, 'begin');
					$this->TextBodies->PlainTextBodyPart = ConvertUtils::ConvertEncoding(substr($this->_body, 0, $firstBegin-1), $GLOBALS[MailInputCharset], $GLOBALS[MailOutputCharset]);
					$parts = explode("\n".'end', substr($this->_body, $firstBegin));
					$this->_body = '';
					
					for ($i = 0, $c = count($parts); $i < $c; $i++)
					{
						$parts[$i] = trim($parts[$i]);
						if (strlen($parts[$i]) == '') continue;
						$startBody = strpos($parts[$i], CRLF);
						$firstLine = substr($parts[$i], 0, $startBody);
						//$filename = substr($firstLine, 10);
						$filename = preg_replace('/begin [\d]* ([.]*)/i', '\\1',$firstLine);

						$newMimePart = new MimePart();
						$newMimePart->Headers->SetHeaderByName(MIMEConst_ContentType, ConvertUtils::GetContentTypeFromFileName($filename).';'.CRLF."\t".MIMEConst_NameLower.'="'.$filename.'"');
						$newMimePart->Headers->SetHeaderByName(MIMEConst_ContentTransferEncoding, 'x-uue');
						$newMimePart->Headers->SetHeaderByName(MIMEConst_ContentDisposition, MIMEConst_AttachmentLower.';'.CRLF."\t".MIMEConst_FilenameLower.'="'.$filename.'"');
						$newMimePart->_body = $parts[$i].CRLF.'end'.CRLF;
						
						$this->Attachments->List->Add(new Attachment($newMimePart));
						unset($newMimePart, $parts[$i]);
					}
				}
				else
				{
					$TEXTLIMIT = 150000;
					/*if (strlen($this->_body) > $TEXTLIMIT)
					{
						$this->_body = substr($this->_body, 0, $TEXTLIMIT).
' <-------------------------------------->
Message text is truncated here because it\'s too long. If you need to
view full message text, you can dowload it locally by clicking "Save"
button in "Full View" mode and open via any local mailer (e.g. Outlook Express).';
					}*/
					
					$this->TextBodies->PlainTextBodyPart = ConvertUtils::ConvertEncoding($this->_body, $GLOBALS[MailInputCharset], $GLOBALS[MailOutputCharset]);
				}
			}
		}
			
		/**
		 * @return bool
		 */
		function IsMimeMail()
		{
			if ($this->GetContentType() == '' && $this->Headers->GetHeaderValueByName(MIMEConst_MimeVersionLower) == '')
			{
				return false;
			}
			return true;
		}
		
		/**
		 * @param string  $nputCharset
		 */
		function ReparseAllHeader($inputCharset)
		{
			for ($i =0, $c = $this->Headers->Count(); $i < $c; $i++)
			{
				$header = &$this->Headers->Get($i);
				if (!ConvertUtils::IsLatin($header->Value))
				{
					$header->Value = ConvertUtils::ConvertEncoding($header->Value, $inputCharset, $GLOBALS[MailOutputCharset]);
					$header->IsParsed = true;
				}
			}
		}

		/**
		 * @return bool
		 */
		function HasAttachments()
		{
			if ($this->Attachments->Count() > 0)
			{
				return true;
			}
			else 
			{
				if (strlen($this->OriginalMailMessage) > 0)
				{
					return false;
				}
				
				$content = strtolower($this->GetContentType());
				if (strpos($content, MIMEConst_BoundaryLower) !== false)
				{
					if (strpos($content, MIMETypeConst_MultipartMixed) !== false)
					{
						return true;
					}
					if (strpos($content, MIMETypeConst_MultipartRelated) !== false)
					{
						return true;
					}
					if (strpos($content, MIMETypeConst_MessageReport) !== false)
					{
						return true;
					}
					
				}
				return false;
			}
		}

		/**
		 * @return bool
		 */		
		function HasHtmlText()
		{
			return $this->TextBodies->HtmlTextBodyPart != '';
		}
		
		/**
		 * @return bool
		 */		
		function HasPlainText()
		{
			return $this->TextBodies->PlainTextBodyPart != '';
		}
		
		/**
		 * Saves a message into the specified file.
		 * @param string $filename
		 * @return bool
		 */
		function SaveMessage($filename)
		{
			$handle = @fopen($filename, 'wb');
			if ($handle)
			{
				$result = @fwrite($handle, $this->TryToGetOriginalMailMessage()) !== false;
				$result = @fclose($handle);
				return $result;
			}
			return false;
		}
		
		/**
		 * @return string
		 */
		function ToMailString($withoutBcc = false)
		{
			$this->SetMessageTypeFlags();
	
			if ($this->IsMixed)
			{
				$saveMail = &$this->CreateNewMixedMail($this);
				return $saveMail->ToString($withoutBcc);
			}			
			
			if ($this->IsAlternative)
			{
				$saveMail = &$this->CreateNewAlternativeMail($this);
				return $saveMail->ToString($withoutBcc);
			}
	
			if ($this->HasHtmlText() || $this->HasPlainText())
			{
				$saveMail = &$this->CreateNewTextMail($this);
				return $saveMail->ToString($withoutBcc);
			}
			return '';
		}
		
		/**
		 * @param MailMessage $paren
		 * @return MailMessage
		 */
		function &CreateNewMixedMail($paren = null)
		{
			$newMail = &new MailMessage();
			if ($paren)  $newMail->Headers = &$paren->Headers;
			$newMail->_subParts = new MimePartCollection($newMail);
			$newMail->_sourceCharset = $GLOBALS[MailOutputCharset];

			$newBoundary = '--=_NextPart_'.md5(rand(100000, 999999));
			$newMail->Headers->SetHeaderByName(MIMEConst_ContentType, MIMETypeConst_MultipartMixed.';'.CRLF."\t".MIMEConst_BoundaryLower.'="'.$newBoundary.'"');
			$newMail->Headers->DeleteHeaderByName(MIMEConst_ContentTransferEncoding);
			
			if ($paren->IsAlternative)
			{
				$alter = &$this->CreateNewAlternativeMail();
				$newMail->_subParts->Add($alter);
			}
			else 
			{
				if ($this->HasPlainText() || $this->HasHtmlText())
				{
					$newMail->_subParts->Add($this->CreateNewTextMail());
				}
			}
			
			if ($paren->HasAttachments())
			{
				$attachs = &$this->Attachments;
	
				foreach ($attachs->Instance() as $att)
				{
					$newMail->_subParts->Add($att->MimePart);
				}
			}			
			return $newMail;
		}
		
		/**
		 * @param MailMessage $paren
		 * @return MailMessage
		 */
		function &CreateNewAlternativeMail($paren = null)
		{
			$newMail = &new MimePart();
			if ($paren)  $newMail->Headers = &$paren->Headers;
			$newMail->_subParts = new MimePartCollection($newMail);
			$newMail->_sourceCharset = $GLOBALS[MailOutputCharset];

			$newBoundary = '--=_NextPart_'.md5(rand(100000, 999999));
			$newMail->Headers->SetHeaderByName(MIMEConst_ContentType, MIMETypeConst_MultipartAlternative.';'.CRLF."\t".MIMEConst_BoundaryLower.'="'.$newBoundary.'"');
			
			$newMail->_subParts->Add($this->TextBodies->ToPlainMime());
			$newMail->_subParts->Add($this->TextBodies->ToHtmlMime());
			
			return $newMail;
		}
		
		/**
		 * @param MailMessage $paren
		 * @return MailMessage
		 */
		function &CreateNewTextMail($paren = null)
		{
			$newMail = &new MimePart();
			if ($paren)  $newMail->Headers = &$paren->Headers;
			$newMail->_sourceCharset = $GLOBALS[MailOutputCharset];
			
			if ($this->HasPlainText())
			{
				$newMail->Headers->SetHeaderByName(MIMEConst_ContentType, MIMETypeConst_TextPlain.'; '.MIMEConst_CharsetLower.'="'.$GLOBALS[MailOutputCharset].'"');
				$newMail->SetEncodedBodyFromText($this->TextBodies->PlainTextBodyPart);
			}
			if ($this->HasHtmlText()) 
			{
				$newMail->Headers->SetHeaderByName(MIMEConst_ContentType, MIMETypeConst_TextHtml.'; '.MIMEConst_CharsetLower.'="'.$GLOBALS[MailOutputCharset].'"');
				$newMail->SetEncodedBodyFromText($this->TextBodies->HtmlTextBodyPart);
			}
			return $newMail;
		}
		
				
		function SetMessageTypeFlags()
		{
			if ($this->HasAttachments())
			{
				$this->IsMixed = true;
			}
			
			if ($this->TextBodies->HtmlTextBodyPart != '' && $this->TextBodies->PlainTextBodyPart != '')
			{
				$this->IsAlternative = true;
			}
		}
		
		/**
		 * @return bool
		 */
		function NeedToUpdateHeader()
		{
			if ($this->GetContentTypeCharset())
			{
				return false;
			}

			if ($GLOBALS[MailInputCharset] == $GLOBALS[MailDefaultCharset])
			{
				return false;
			}
		
			return true;
		}
		
		/**
		 * @return string
		 */
		function TryToGetOriginalMailMessage()
		{
			if ($this->OriginalMailMessage)
			{
				return $this->OriginalMailMessage;
			}
			else 
			{
				return $this->ToString();
			}
		}
		
	}
