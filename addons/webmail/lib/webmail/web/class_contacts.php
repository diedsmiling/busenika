<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	require_once(WM_ROOTPATH.'common/class_collectionbase.php');

	define('PRIMARYEMAIL_Home', 0);
	define('PRIMARYEMAIL_Business', 1);
	define('PRIMARYEMAIL_Other', 2);

	class Contact
	{
		/**
		 * @var int
		 */
		var $Id;
		
		/**
		 * @var bool
		 */
		var $IsGroup;
		
		/**
		 * @var string
		 */
		var $Name;
		
		/**
		 * @var string
		 */
		var $Email;
		
		/**
		 * @var int
		 */
		var $Frequency;
		
		/**
		 * @var bool
		 */
		var $UseFriendlyName;
	}

	class ContactCollection extends CollectionBase
	{
		function ContactCollection()
		{
			CollectionBase::CollectionBase();
		}
		
		/**
		 * @param Contact $contact
		 */
		function Add(&$contact)
		{
			$this->List->Add($contact);
		}
		
		/**
		 * @param int $index
		 * @return Contact
		 */
		function &Get($index)
		{
			return $this->List->Get($index);
		}
	}
	
	class AddressGroup
	{
		/**
		 * @var int
		 */
		var $Id;
		
		/**
		 * @var bool
		 */
		var $IdUser;
		
		/**
		 * @var string
		 */
		var $Name;
		
		/**
		 * @var string
		 */
		var $Email;
		/**
		 * @var string
		 */
		var $Company;
		/**
		 * @var string
		 */
		var $Street;
		/**
		 * @var string
		 */
		var $City;
		/**
		 * @var string
		 */
		var $State;
		/**
		 * @var string
		 */
		var $Zip;
		/**
		 * @var string
		 */
		var $Country;
		/**
		 * @var string
		 */
		var $Phone;
		/**
		 * @var string
		 */
		var $Fax;
		/**
		 * @var string
		 */
		var $Web;
		/**
		 * @var bool
		 */
		var $IsOrganization = false;
		
		/**
		 * @return String
		 */
		function validateData()
		{
			
			$this->Name = trim($this->Name);
			
			if(empty($this->Name))
			{
				return JS_LANG_WarningGroupNotComplete;
			}
			
			$this->Web = Validate::cleanWebPage($this->Web);
			
			return true;
		}
	}
	
	class AddressBookRecord
	{
		/**
		 * @var long
		 */
		var $IdAddress;
		
		/**
		 * @var int
		 */
		var $IdUser;
		
		/**
		 * @var string
		 */
		var $HomeEmail;
		
		/**
		 * @var string
		 */
		var $FullName;

		/**
		 * @var string
		 */
		var $Notes;

		/**
		 * @var bool
		 */
		var $UseFriendlyName;

		/**
		 * @var string
		 */
		var $HomeStreet;

		/**
		 * @var string
		 */
		var $HomeCity;

		/**
		 * @var string
		 */
		var $HomeState;

		/**
		 * @var string
		 */
		var $HomeZip;

		/**
		 * @var string
		 */
		var $HomeCountry;

		/**
		 * @var string
		 */
		var $HomePhone;

		/**
		 * @var string
		 */
		var $HomeFax;

		/**
		 * @var string
		 */
		var $HomeMobile;

		/**
		 * @var string
		 */
		var $HomeWeb;

		/**
		 * @var string
		 */
		var $BusinessEmail;

		/**
		 * @var string
		 */
		var $BusinessCompany;

		/**
		 * @var string
		 */
		var $BusinessStreet;

		/**
		 * @var string
		 */
		var $BusinessCity;

		/**
		 * @var string
		 */
		var $BusinessState;

		/**
		 * @var string
		 */
		var $BusinessZip;

		/**
		 * @var string
		 */
		var $BusinessCountry;

		/**
		 * @var string
		 */
		var $BusinessJobTitle;

		/**
		 * @var string
		 */
		var $BusinessDepartment;

		/**
		 * @var string
		 */
		var $BusinessOffice;

		/**
		 * @var string
		 */
		var $BusinessPhone;

		/**
		 * @var string
		 */
		var $BusinessFax;

		/**
		 * @var string
		 */
		var $BusinessWeb;
		
		/**
		 * @var string
		 */
		var $OtherEmail;

		/**
		 * @var short
		 */
		var $PrimaryEmail;

		/**
		 * @var long
		 */
		var $IdPreviousAddress;
		
		/**
		 * @var bool
		 */
		var $Temp;
		
		/**
		 * @var short
		 */
		var $BirthdayDay;

		/**
		 * @var short
		 */
		var $BirthdayMonth;

		/**
		 * @var short
		 */
		var $BirthdayYear;
		
		/**
		 * @return String
		 */
		function validateData()
		{
			
			$this->PrimaryEmail = trim($this->PrimaryEmail);
			$this->HomeEmail = trim($this->HomeEmail);
			$this->BusinessEmail = trim($this->BusinessEmail);
			$this->OtherEmail = trim($this->OtherEmail);
			$this->FullName = trim($this->FullName);
			$this->BusinessStreet = trim($this->BusinessStreet);
			$this->HomeStreet = trim($this->HomeStreet);
			$this->BusinessCity = trim($this->BusinessCity);
			$this->HomeCity = trim($this->HomeCity);
			$this->BusinessState = trim($this->BusinessState);
			$this->HomeState = trim($this->HomeState);
			$this->BusinessZip = trim($this->BusinessZip);
			$this->HomeZip = trim($this->HomeZip);
			$this->BusinessCountry = trim($this->BusinessCountry);
			$this->HomeCountry = trim($this->HomeCountry);
			$this->BusinessFax = trim($this->BusinessFax);
			$this->HomeFax = trim($this->HomeFax);
			$this->BusinessPhone = trim($this->BusinessPhone);
			$this->HomePhone = trim($this->HomePhone);
			$this->BusinessWeb = trim($this->BusinessWeb);
			$this->HomeWeb = trim($this->HomeWeb);
			$this->HomeMobile = trim($this->HomeMobile);
			$this->BusinessCompany = trim($this->BusinessCompany);
			$this->BusinessDepartment = trim($this->BusinessDepartment);
			$this->BusinessJobTitle = trim($this->BusinessJobTitle);
			$this->BusinessOffice = trim($this->BusinessOffice);
			$this->Notes = trim($this->Notes);
			
			if(empty($this->PrimaryEmail) &&
				empty($this->BusinessEmail) &&
				empty($this->HomeEmail) &&
				empty($this->OtherEmail) &&
				empty($this->FullName))
			{
				return JS_LANG_WarningContactNotComplete;
			}
			
			$this->BusinessWeb = Validate::cleanWebPage($this->BusinessWeb);
			$this->HomeWeb = Validate::cleanWebPage($this->HomeWeb);
			
			return true;
		}
		
		function isOpen()
		{
			if (strlen(trim(
					$this->BusinessStreet.
					$this->HomeStreet.
					$this->BusinessCity.
					$this->HomeCity.
					$this->BusinessState.
					$this->HomeState.
					$this->BusinessZip.
					$this->HomeZip.
					$this->BusinessCountry.
					$this->HomeCountry.
					$this->BusinessFax.
					$this->HomeFax.
					$this->BusinessPhone.
					$this->HomePhone.
					$this->BusinessWeb.
					$this->HomeWeb.
					$this->HomeMobile.
					$this->BusinessCompany.
					$this->BusinessDepartment.
					$this->BusinessJobTitle.
					$this->BusinessOffice.
					$this->Notes)) > 0)
			{
				return true;
			}
			
			if ($this->BirthdayDay + $this->BirthdayMonth + $this->BirthdayYear > 0)
			{
				return true;
			}
			
			$cnt = 0;
			if (strlen($this->HomeEmail) > 0)
			{
				$cnt++;
			}
			if (strlen($this->BusinessEmail) > 0)
			{
				$cnt++;
			}
			if (strlen($this->OtherEmail) > 0)
			{
				$cnt++;
			}
			if ($cnt > 1) 
			{
				return true;
			}
			
			return false;	
		}
	}