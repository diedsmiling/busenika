<?php

	/**
	 * @package WebMailPro
	 * @subpackage Mime
	 */

	class Attachment
	{
		/**
		 * @var string $Filename
		 */
		var $Filename;
		
		/**
		 * @var MimePart
		 */
		var $MimePart;
		
		/**
		 * @var bool
		 */
		var $IsInline = false; 
		
		/**
		 * Gets the Content-ID value of the attachment.
		 * @return string
		 */
		function GetContentID()
		{
			return $this->MimePart->GetContentID();
		}
		
		function GetBinaryBody()
		{
			return $this->MimePart->GetBinaryBody();
		}
		
		/**
		 * Gets the content location of the attachment.
		 * @return string
		 */
		function GetContentLocation()
		{
			return $this->MimePart->GetContentLocation();
		}
		
		/**
		 * Gets the content type of the attachment.
		 * @return string
		 */
		function GetContentType()
		{
			return $this->MimePart->GetContentType();
		}
		
		/**
		 * Gets the description of the attachment as a string.
		 * @return string
		 */
		function GetDescription()
		{
			return $this->MimePart->GetDescription();
		}
		
		/**
		 * Gets the filename of the attachment.
		 * @return string
		 */
		function GetFilenameFromMime()
		{
			$filename = $this->MimePart->GetFilename();
			$result = '';
			if ($filename == '')
			{
				$contentName = $this->MimePart->GetContentTypeName();
				if ($contentName)
				{
					$result = $contentName;
				}
				else 
				{
					$contentType = strtolower($this->GetContentType());
					$result = (strpos($contentType, 'image') !== false || strpos($contentType, 'message') !== false)
								? str_replace(array('/', '\\'), '.', $contentType)
								: 'no_name_attachment.tmp';
				}
			} 
			else 
			{
				$result = $filename;
			}
			return $result;
		}
		
		function GetFilename()
		{
			return $this->Filename;
		}
		
		/**
		 * Gets the collection of the attachment headers.
		 * @return HeaderCollection
		 */
		function &GetHeaders()
		{
			return $this->MimePart->GetHeaders();
		}
		
		/**
		 * @param MimePart $mimePart
		 * @return Attachment
		 */
		function Attachment(&$mimePart, $isInline = false)
		{
			$this->MimePart = &$mimePart;
			$this->Filename = $this->GetFilenameFromMime();
			$this->IsInline = $isInline;
		}
		
		/**
		 * @param string $filename
		 * @return bool
		 */
		function SaveToFile($filename)
		{
			$returnBool = true;
			if ($fh = @fopen($filename, 'wb'))
			{
				if (!@fwrite($fh, $this->GetBinaryBody()))
				{
					setGlobalError('Can\'t write file: '.$filename);
					$returnBool = false;
				}
				@fclose($fh);
			}
			else 
			{
				setGlobalError('Can\'t open file(wb): '.$filename);
				$returnBool = false;
			}
			return $returnBool;
		}
		
		/**
		 * @return string
		 */
		function GetTempName()
		{
			$name = $this->GetFilenameFromMime();
			$exe = array_pop(explode('.', $name));
			$exe = ($exe && $exe != $name) ? $exe : 'tmp';
			if (strlen($exe) > 7) $exe = substr($exe, 0, 7);
			return md5($name).'.'.$exe;
		}
		
	}