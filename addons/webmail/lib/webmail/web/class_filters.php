<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	require_once(WM_ROOTPATH.'common/class_collectionbase.php');

	define('FILTERFIELD_From', 0);
	define('FILTERFIELD_To', 1);
	define('FILTERFIELD_Subject', 2);
	
	define('FILTERCONDITION_ContainSubstring', 0);
	define('FILTERCONDITION_ContainExactPhrase', 1);
	define('FILTERCONDITION_NotContainSubstring', 2);
	
	define('FILTERACTION_DoNothing', 0);
	define('FILTERACTION_DeleteFromServerImmediately', 1);
	define('FILTERACTION_MarkGrey', 2);
	define('FILTERACTION_MoveToFolder', 3);
	
	class Filter
	{
		/**
		 * @var int
		 */
		var $Id;
		
		/**
		 * @var int
		 */
		var $IdAcct;
		
		/**
		 * @var short
		 */
		var $Field;

		/**
		 * @var short
		 */
		var $Condition;
		
		/**
		 * @var string
		 */
		var $Filter;

		/**
		 * @var short
		 */
		var $Action;
		
		/**
		 * @var int
		 */
		var $IdFolder;
		
		/**
		 * @param WebMailMessage $message
		 * @return short
		 */
		function GetActionToApply(&$message)
		{
		
			switch ($this->Field)
			{
				case FILTERFIELD_From:
					$field = $message->GetFromAsString();
					break;
				case FILTERFIELD_To:
					$field = $message->GetToAsString();
					break;
				case FILTERFIELD_Subject:
					$field = $message->GetSubject();
					break;
				default:
					$field = null;
			}
			
			if ($field != null)
			{
				return $this->_processMessage($field);
			}
			return -1;
		}
		
		/**
		 * @access private
		 * @param string $field
		 * @return short
		 */
		function _processMessage($field)
		{
			$needToProcess = false;
			switch ($this->Condition)
			{
				case FILTERCONDITION_ContainSubstring:
					if (strpos($field, $this->Filter) !== false)
					{
						$needToProcess = true;
					}
					break;
				case FILTERCONDITION_ContainExactPhrase:
					if ($field == $this->Filter)
					{
						$needToProcess = true;
					}
					break;
				case FILTERCONDITION_NotContainSubstring:
					if (strpos($field, $this->Filter) === false)
					{
						$needToProcess = true;
					}
					break;
			}
			
			if ($needToProcess)
			{
				return $this->Action;
			}
			return -1;
			
		}
		
	}
	
	class FilterCollection extends CollectionBase
	{
		function FilterCollection()
		{
			CollectionBase::CollectionBase();
		}
		
		/**
		 * @param Filter $filter
		 */
		function Add(&$filter)
		{
			$this->List->Add($filter);
		}
		
		/**
		 * @param int $index
		 * @return Filter
		 */
		function &Get($index)
		{
			return $this->List->Get($index);
		}
		
	}
