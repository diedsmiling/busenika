<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	require_once(WM_ROOTPATH.'libs/class_xmlsaxyliteparser.php');
	require_once(WM_ROOTPATH.'common/inc_constants.php');

	class XmlDomNode
	{
		/**
		 * @var string
		 */
		var $TagName;
		
		/**
		 * @var string
		 */
		var $Value;
		
		/**
		 * @var Array
		 */
		var $Attributes = array();
		
		/**
		 * @var Array
		 */
		var $Children = array();
		
		/**
		 * @param string $tagName
		 * @param string $value optional
		 * @param bool $isCDATA optional
		 * @return XmlDomNode
		 */
		function XmlDomNode($tagName, $value = null, $isCDATA = false, $isSimpleCharsCode = false)
		{
			$value = ($value) ? ConvertUtils::ClearUtf8($value) : $value;
			
			$this->TagName = $tagName;
			$this->Value = $value;
			
			if ($isCDATA)
			{
				$value = ($isSimpleCharsCode) ? ConvertUtils::WMHtmlNewCode($value) : ConvertUtils::WMHtmlSpecialChars($value);
				$this->Value = '<![CDATA['.$value.']]>';
			}
		}
		
		/**
		 * @param XmlDomNode $node
		 */
		function AppendChild(&$node)
		{
			if ($node) $this->Children[] = &$node;
		}
		
		/**
		 * @param string $name
		 * @param string $value
		 */
		function AppendAttribute($name, $value)
		{
			$this->Attributes[$name] = $value;
		}
		
		/**
		 * @param string $tagName
		 * @return XmlDomNode
		 */
		function &GetChildNodeByTagName($tagName)
		{
			$XmlDomNode = null;
			foreach (array_keys($this->Children) as $nodeKey)
			{
				if ($this->Children[$nodeKey]->TagName == $tagName)
				{
					$XmlDomNode = &$this->Children[$nodeKey];
				}
			}
			return $XmlDomNode;
		}
		
		/**
		 * @param string $tagName
		 * @param bool $decode optional
		 * @return string
		 */
		function GetChildValueByTagName($tagName, $decode = false)
		{
			$node = &$this->GetChildNodeByTagName($tagName);
			if ($node != null)
			{
				return (!$decode)?$node->Value:ConvertUtils::WMBackHtmlSpecialChars($node->Value);
			}
			return '';
		}
		
		
		/**
		 * @param bool $splitLines
		 * @return string
		 */
		function ToString($splitLines)
		{
			$attributes = '';
			foreach ($this->Attributes as $name => $value)
			{
				$attributes .= sprintf(' %s="%s"', $name, $value);
			}
			
			$childs = '';
			if (count($this->Children) > 0)
			{
				foreach (array_keys($this->Children) as $index)
				{
					$childs .= $this->Children[$index]->ToString($splitLines);
					if ($splitLines)
					{
						$childs .= "\r\n";
					}
				}
				
				// shift lines by tabs
				if ($splitLines)
				{
					$lines = explode("\r\n", $childs);
					$childs = '';
					foreach ($lines as $line)
					{
						$childs .= ($line != '')?sprintf("\t%s\r\n", $line):'';
					}
				}
			}
			
			if ($childs == '' && $this->Value === null)
			{
				$outStr = sprintf('<%s%s />', $this->TagName, $attributes);
				if ($splitLines)
				{
					$outStr .= "\r\n";
				}
				return $outStr;
			}
			
			$value = ($this->Value !== null)?trim($this->Value):'';
			
			if ($splitLines)
			{
				if ($value != '' && $childs == '')
				{
					return sprintf('<%s%s>%s</%s>', $this->TagName, $attributes, $value, $this->TagName);
				}
                /*if ($value == '' && $childs != '' )
                {
                     return sprintf('<%s%s>%s</%s>', $this->TagName, $attributes, $childs, $this->TagName);
                }*/
                if ($value == '' && $childs == '' )
                {
                     return sprintf('<%s%s />', $this->TagName, $attributes,$this->TagName);
                }
				return sprintf("<%s%s>%s\r\n%s</%s>\r\n", $this->TagName, $attributes, $value, $childs, $this->TagName);
			}
			return sprintf('<%s%s>%s%s</%s>', $this->TagName, $attributes, $value, $childs, $this->TagName);
		}
		
		/**
		 * @param string $name
		 * @param mix $default
		 * @return string
		 */
		function GetAttribute($name, $default = null)
		{
			return isset($this->Attributes[$name]) ? $this->Attributes[$name] : $default;
		}
	}


	class XmlDocument
	{
		/**
		 * @var XmlDomNode
		 */
		var $XmlRoot = null;
		
		/**
		 * @param string $name
		 * @param string $value
		 */
		function CreateElement($name, $value = null)
		{
			$this->XmlRoot = &new XmlDomNode($name, $value);
		}
		
		/**
		 * @param string $xmlText
		 * @return bool
		 */
		function ParseFromString($xmlText)
		{
			if (extension_loaded('xml'))
			{
				$parser = xml_parser_create();
				xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
				xml_set_element_handler($parser, array(&$this, '_startElement'), array(&$this, '_endElement'));
				xml_set_character_data_handler($parser, array(&$this, '_charData'));
				$result = xml_parse($parser, $xmlText);
				xml_parser_free($parser);
			}
			else
			{
			    $parser = &new SAXY_Lite_Parser();
			    $parser->xml_set_element_handler(array(&$this, '_startElement'), array(&$this, '_endElement'));
				$parser->xml_set_character_data_handler(array(&$this, '_charData'));
			    $parser->parse($xmlText);
			    $result = true;
			}
			return $result;
		}

		/**
		 * @param bool $splitLines
		 * @return string
		 */
		function ToString($splitLines = false)
		{
			$outStr = '<?xml version="1.0" encoding="utf-8"?>';
			if ($splitLines)
			{
				$outStr .= "\r\n";
			}
			
			if ($this->XmlRoot != null)
			{
				$outStr .= $this->XmlRoot->ToString($splitLines);
			}
			
			return $outStr;
		}
		
		/**
		 * @param string $fileName
		 * @return bool
		 */
		function LoadFromFile($fileName)
		{
			$xmlData = @file_get_contents($fileName);
			if ($xmlData == false)
			{
				setGlobalError('Can\'t load '.$fileName.' ('.__FILE__.' - '.__LINE__.')');
				return false;
			}
			return $this->ParseFromString($xmlData);
		}
		
		/**
		 * @param string $fileName
		 * @return bool
		 */
		function SaveToFile($fileName)
		{
			$fstat = '';
			$fp = @fopen($fileName, 'wb');
			if ($fp)
			{
				$result = @fwrite($fp, $this->ToString(true)) !== false;
				$result &= @fclose($fp);
				return $result;
			}
			else 
			{
				$fstat = @substr(sprintf('%o', fileperms($fileName)), -4);
				if (!$fstat) $fstat = 'error';
				setGlobalError($fileName .' ('.$fstat.') permission denied');
			}
			return false;
		}
		
		/**
		 * @param string $name
		 * @return string
		 */
		function GetParamValueByName($name)
		{
			if ($this->XmlRoot && is_array($this->XmlRoot->Children))
			{
				foreach (array_keys($this->XmlRoot->Children) as $nodeKey)
				{
					if ($this->XmlRoot->Children[$nodeKey]->TagName == 'param' &&
						isset($this->XmlRoot->Children[$nodeKey]->Attributes['name']) &&
						$this->XmlRoot->Children[$nodeKey]->Attributes['name'] == $name)
					{
						return $this->XmlRoot->Children[$nodeKey]->Attributes['value'];
					}
				}
			}
			return '';
		}
		
		/**
		 * @param string $name
		 * @param bool $isCDATA optional
		 * @return string
		 */
		function GetParamTagValueByName($name, $decode = false)
		{
			foreach (array_keys($this->XmlRoot->Children) as $nodeKey)
			{
				if ($this->XmlRoot->Children[$nodeKey]->TagName == 'param' &&
					isset($this->XmlRoot->Children[$nodeKey]->Attributes['name']) &&
					$this->XmlRoot->Children[$nodeKey]->Attributes['name'] == $name)
				{
					return ($decode) ? $this->XmlRoot->Children[$nodeKey]->Value :
						ConvertUtils::WMBackHtmlSpecialChars($this->XmlRoot->Children[$nodeKey]->Value);
				}
			}
			return '';
		}

		/**
		 * @access private
		 */
		function _startElement($parser, $name, $attributes)
		{
			$node = &new XmlDomNode($name);
			$node->Attributes = $attributes;
			if ($this->XmlRoot == null)
			{
				$this->XmlRoot = &$node;
			}
			else
			{
				$rootNode = &$this->_stack[count($this->_stack)-1];
				$rootNode->Children[] = &$node;
			}
			$this->_stack[] = &$node;
		}
		
		/**
		 * @access private
		 */
		function _endElement($parser, $name) 
		{
			array_pop($this->_stack);
		}
		
		/**
		 * @access private
		 */
		function _charData($parser, $text)
		{
			$node = &$this->_stack[count($this->_stack)-1];
			if ($node->Value == null)
			{
				$node->Value = '';
			}
			$node->Value .= $text;
		}		

	}
