<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));

	require_once(WM_ROOTPATH.'libs/class_converthtml.php');
	
	/**
	 * @package WebMailPro
	 * @subpackage Mime
	 */
	
	class TextBodyCollection
	{
		/**
		 * @var string
		 */
		var $PlainTextBodyPart = '';
		
		/**
		 * @var string
		 */
		var $HtmlTextBodyPart = '';
		
		/**
		 * @param MimePart $mimePart
		 */
		function AddToCollection(&$mimePart)
		{
			$TEXTLIMIT = 150000;
			if ($mimePart->IsMimePartTextBody())
			{
				if ($mimePart->IsMimePartAttachment()) return;
				$contentType = strtolower($mimePart->GetContentType());
				$charset = new HeaderParameterCollection($contentType);
				$charset = $charset->GetByName(MIMEConst_CharsetLower);
				$contentCharset = ($charset) ? $charset->Value : '';
				
				if ($GLOBALS[MailInputCharset] == '')
				{
					$GLOBALS[MailInputCharset] = $contentCharset;
					$this->HasCharset = ($contentCharset);
				}

				if (strpos($contentType, MIMETypeConst_TextPlain) !== false || $contentType == '')
				{
					$this->PlainTextBodyPart .= trim($mimePart->GetBody($TEXTLIMIT));
				}
				elseif (strpos($contentType, MIMETypeConst_TextHtml) !== false)
				{
					$preStr = '/(<meta\s.*)(charset\s?=)([^"\'>\s]*)/i';
					$this->HtmlTextBodyPart .= trim(preg_replace($preStr, '$1$2'.$GLOBALS[MailOutputCharset], $mimePart->GetBody($TEXTLIMIT)));
				}
			}
		}
		
		/**
		 * @param MailMessage $mimePart
		 */
		function SearchMimeParts(&$mimePart)
		{
			if ($mimePart->GetSubParts() == null)
			{
				$this->AddToCollection($mimePart);
			}
			else
			{
				for ($i = 0, $c = $mimePart->_subParts->List->Count(); $i < $c; $i++)
				{
					$subPart = &$mimePart->_subParts->List->Get($i);
					$this->SearchMimeParts($subPart);
				}
			}
		}

		/**
		 * @return string
		 */
		function HtmlToPlain()
		{
			$pars = &new convertHtml($this->HtmlTextBodyPart);
			return $pars->get_text();
		}
		
		/**
		 * @param MailMessage $mailMessage
		 * @return TextBodyCollection
		 */
		function TextBodyCollection(&$mailMessage)
		{
			if ($mailMessage != null) $this->SearchMimeParts($mailMessage);
		}
		
		/**
		 * @return MimePart
		 */
		function ToPlainMime()
		{
			$newPlainMimePart = &new MimePart();
			$newPlainMimePart->Headers->SetHeaderByName(MIMEConst_ContentType, MIMETypeConst_TextPlain.'; '.MIMEConst_CharsetLower.'="'.$GLOBALS[MailOutputCharset].'"');
			$newPlainMimePart->Headers->SetHeaderByName(MIMEConst_ContentTransferEncoding, MIMEConst_QuotedPrintable);
			//$newPlainMimePart->Headers->SetHeaderByName(MIMEConst_ContentTransferEncoding, MIMEConst_Base64);			
			$newPlainMimePart->SetEncodedBodyFromText($this->PlainTextBodyPart);
			$newPlainMimePart->_sourceCharset = $GLOBALS[MailOutputCharset];
			return $newPlainMimePart;
		}

		/**
		 * @return MimePart
		 */
		function ToHtmlMime()
		{
			$newHtmlMimePart = &new MimePart();
			$newHtmlMimePart->Headers->SetHeaderByName(MIMEConst_ContentType, MIMETypeConst_TextHtml.'; '.MIMEConst_CharsetLower.'="'.$GLOBALS[MailOutputCharset].'"');
			$newHtmlMimePart->Headers->SetHeaderByName(MIMEConst_ContentTransferEncoding, MIMEConst_QuotedPrintable);
			//$newHtmlMimePart->Headers->SetHeaderByName(MIMEConst_ContentTransferEncoding, MIMEConst_Base64);
			$newHtmlMimePart->SetEncodedBodyFromText($this->HtmlTextBodyPart);
			$newHtmlMimePart->_sourceCharset = $GLOBALS[MailOutputCharset];
			return $newHtmlMimePart;
		}
		
		/**
		 * @return int
		 */
		function ClassType()
		{
			return (int) ($this->HtmlTextBodyPart != '') << 1 | (int) ($this->PlainTextBodyPart != '');
		}
		
		/**
		 * @param string $bannerText
		 */
		function AddTextBannerToBodyText($bannerText)
		{
			if ($this->HtmlTextBodyPart != '')
			{
				$this->HtmlTextBodyPart .= "\r\n".'<br /><br />'.$bannerText;
			}
			else if ($this->PlainTextBodyPart != '')
			{
				$this->PlainTextBodyPart .= "\r\n\r\n".$bannerText;
			}
		}
	}
