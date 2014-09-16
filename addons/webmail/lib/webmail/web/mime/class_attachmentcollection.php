<?php

	/**
	 * @package WebMailPro
	 * @subpackage Mime
	 */

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));

	require_once(WM_ROOTPATH.'mime/inc_constants.php');
	require_once(WM_ROOTPATH.'mime/class_attachment.php');


	class AttachmentCollection extends CollectionBase
	{
		/**
		 * @access private
		 * @var string
		 */
		var $_htmlText = '';
		
		/**
		 * @param MailMessage $mailMessage
		 * @return AttachmentCollection
		 */
		function AttachmentCollection(&$mailMessage)
		{
			CollectionBase::CollectionBase();
			
			if ($mailMessage != null) 
			{
				//$this->_htmlText = &$mailMessage->TextBodies->HtmlTextBodyPart;
				$this->SearchAttachParts($mailMessage);
			}
		}
		
		/**
		 * @param MimePart $mimePart
		 */
		function AddToCollection(&$mimePart)
		{

			$bool = false;
			
			if ($this->_htmlText)
			{
/*				if (strpos($this->_htmlText, trim($mimePart->GetContentID())) !== false)
				{
					$bool = true;
				}
				else
				{
					$bool = strpos($this->_htmlText, trim($mimePart->GetContentLocation())) !== false;
				}*/
			}
			
			$this->List->Add(new Attachment($mimePart, $bool));
		}
		
		/**
		 * @param int $index
		 * @return Attachment
		 */
		function &Get($index)
		{
			return $this->List->Get($index);
		}
		
		/**
		 * @return Attachment
		 */
		function &GetLast()
		{
			return $this->List->Get($this->List->Count()-1);
		}
		
		/**
		 * @param MimePart $mimePart
		 */
		function SearchAttachParts(&$mimePart)
		{
			if ($mimePart->_subParts == null)
			{
				if ($mimePart->IsMimePartAttachment())
				{
					$this->AddToCollection($mimePart);
				}
			}
			else
			{
				for ($i = 0, $c = $mimePart->_subParts->List->Count(); $i < $c; $i++)
				{
					$subPart = &$mimePart->_subParts->List->Get($i);
					$this->SearchAttachParts($subPart);
				}
			}
		}
		
		/**
		 * @return bool
		 */
		function AddFromFile($filepath, $attachname, $mimetype, $isInline = false)
		{
			$data = '';
			$handle = @fopen($filepath, 'rb');
			if ($handle)
			{
				$size = @filesize($filepath);
				$data = ($size) ? @fread($handle, $size) : '';
				@fclose($handle);
			}
			else 
			{
				setGlobalError(' can\'t open '.$filepath);
				return false;
			}
		
			if ($this->AddFromBinaryBody($data, $attachname, $mimetype, $isInline))
			{
				return true;
			}
			return false;
			
		}
		
		function AddFromBinaryBody($bbody, $attachname, $mimetype, $isInline)
		{
			if ($bbody)
			{
				$AttachType = ($isInline) ? MIMEConst_InlineLower : MIMEConst_AttachmentLower;
				
				$attachname = ConvertUtils::EncodeHeaderString($attachname, $GLOBALS[MailInputCharset], $GLOBALS[MailOutputCharset]);
				$mimePart = new MimePart();
				$mimePart->Headers->SetHeaderByName(MIMEConst_ContentType, $mimetype.';'.CRLF."\t".MIMEConst_NameLower.'="'.$attachname.'"', false);
				$mimePart->Headers->SetHeaderByName(MIMEConst_ContentTransferEncoding, MIMEConst_Base64Lower, false);			
				$mimePart->Headers->SetHeaderByName(MIMEConst_ContentDisposition, $AttachType.';'.CRLF."\t".MIMEConst_FilenameLower.'="'.$attachname.'"', false);

				$mimePart->_body = ConvertUtils::base64WithLinebreak($bbody);
				
				$this->List->Add(new Attachment($mimePart));
				return true;
			}
			return false;
		}
		
/*		function SaveToFolderWithMd5filename($path)
		{
			$out = array();
			foreach (array_keys($this->List) as $keys)
			{
				$attach = &$this->Get($keys);
				$md5name = md5(time().$attach->Filename.'.tmp');
				$attach->SaveToFile($path.'/'.$md5name);
				$out[] = $md5name;
			}
			return $out;
		}*/
	}