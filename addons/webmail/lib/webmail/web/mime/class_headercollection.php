<?php

	/**
	 * @package WebMailPro
	 * @subpackage Mime
	 */

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));

	require_once(WM_ROOTPATH.'common/class_collectionbase.php');
	require_once(WM_ROOTPATH.'mime/class_header.php');

	class HeaderCollection extends CollectionBase
	{
		/**
		 * @param string $rawData
		 * @return HeaderCollection
		 */
		function HeaderCollection($rawData = null)
		{
			CollectionBase::CollectionBase();
			if ($rawData != null)
			{
				$this->Parse($rawData);
			}
		}
			
		/**
		 * @param string $rawData
		 */
		function Parse($rawData)
		{
			$pos1 = (int) strpos($rawData, "\r");
			$pos2 = (int) strpos($rawData, "\n");
			
			$delimiter = ($pos1 > $pos2) ? "\r" : "\n";
			if ($pos1 > $pos2 && $pos1 + 1 == $pos2)
			{	
				$rawData = str_replace("\r", '', $rawData);
				$delimiter = "\n";
			}
			
			//$rawData = str_replace("\n", CRLF, str_replace(CRLF, "\n", $rawData));
			//$rawData = ereg_replace(CRLF."\t+", '',  ereg_replace(CRLF.' +', ' ', ereg_replace(' +', ' ', $rawData)));

			$preheaders = explode($delimiter, $rawData);
			
			$headers = array();
			foreach ($preheaders as $value)
			{
				if (empty($value)) continue; # fool protection
				$value = ereg_replace("\?=[ \s]{1,4}=\?", '?==?', $value);
				if (isset($value{0}) && ($value{0} === ' ' || $value{0} === "\t"))
				{
					$index = ((count($headers) - 1) < 0) ? 0 : count($headers) - 1; # fool protection
					$headers[$index] .= CRLF."\t".trim($value);
				} 
				else 
				{
					$headers[] = $value;
				}
			}
			unset($preheaders);
			
			foreach ($headers as $headerline)
			{
				$header = explode(':', $headerline, 2);
				if (count($header) == 2)
				{
					$this->List->Add(new Header(trim($header[0]), trim($header[1])));
				}
			}
		}
		
		/**
		 * @param string $name
		 * @return Header
		 */
		function &GetHeaderByName($name)
		{
			for ($i = 0, $count = $this->Count(); $i < $count; $i++)
			{
				$header = &$this->Get($i);
				if (strtolower($header->Name) == strtolower($name))
				{
					return $header;
				}
			}
			$null = null;
			return $null;
		}
		
		/**
		 * @param string $name
		 * @return string
		 */
		function GetHeaderValueByName($name)
		{
			for ($i = 0, $count = $this->Count(); $i < $count; $i++)
			{
				$header = &$this->Get($i);
				if (strtolower($header->Name) == strtolower($name))
				{
					return $header->Value;
				}
			}
			return '';
		}
		
		/**
		 * @param string $name
		 * @return array
		 */
		function GetHeadersValuesByName($name)
		{
			$result = array();
			for ($i = 0, $count = $this->Count(); $i < $count; $i++)
			{
				$header = &$this->Get($i);
				if (strtolower($header->Name) == strtolower($name))
				{
					$result[] = $header->Value;
				}
			}
			return $result;
		}
				
		/**
		 * @param string $name
		 * @return string
		 */
		function GetHeaderDecodedValueByName($name)
		{
			for ($i = 0, $count = $this->Count(); $i < $count; $i++)
			{
				$header = &$this->Get($i);
				if (strtolower($header->Name) == strtolower($name))
				{
					return $header->GetDecodedValue();
				}
			}
			return '';
		}
		
		/**
		 * @param string $name
		 * @param string $value
		 * @param int $isParsed
		 */
		function SetHeaderByName($name, $value, $isParsed = true)
		{
			for ($i = 0, $count = $this->Count(); $i < $count; $i++)
			{
				$header = $this->Get($i);
				if (strtolower($header->Name) == strtolower($name))
				{
					$this->Set($i, new Header($name, $value, $isParsed));
					return;
				}
			}
			$this->List->Add(new Header($name, $value, $isParsed));
		}
		
		/**
		 * @param string $name
		 */
		function DeleteHeaderByName($name)
		{
			$head = &$this->GetHeaderByName($name);
			$this->List->Remove($head);
		}

		/**
		 * @param Header $header
		 */
		function SetHeader(&$header)
		{
			for ($i = 0, $count = $this->Count(); $i < $count; $i++)
			{
				$currHeader = $this->Get($i);
				if (strtolower($currHeader->Name) == strtolower($header->Name))
				{
					$this->Set($i, $header);
					return;
				}
			}
			$this->List->Add($header);
		}

		/**
		 * @return string
		 */
		function ToString($withoutBcc = false)
		{
			$retval = '';
			for ($i = 0, $c = $this->Count(); $i < $c; $i++)
			{
				$header = &$this->Get($i);
				if (strtolower($header->Name) == MIMEConst_BccLower && $withoutBcc)
				{
					continue;
				}
				$retval .= $header->ToString().CRLF;
			}
			
			return $retval;
		}
		
		/**
		 * @param index $index
		 * @return Header
		 */
		function &Get($index)
		{
			return $this->List->Get($index);
		}
		
	}