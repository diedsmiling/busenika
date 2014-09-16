<?php
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	require_once(WM_ROOTPATH.'class_mailstorage.php');
	require_once(WM_ROOTPATH.'class_commandcreator.php');
	require_once(WM_ROOTPATH.'class_filesystem.php');
	require_once(WM_ROOTPATH.'class_filters.php');
	require_once(WM_ROOTPATH.'common/class_convertutils.php');

	define('DBTABLE_A_USERS', 'a_users');
	define('DBTABLE_AWM_SETTINGS', 'awm_settings');
	define('DBTABLE_AWM_MESSAGES', 'awm_messages');
	define('DBTABLE_AWM_MESSAGES_BODY', 'awm_messages_body');
	define('DBTABLE_AWM_READS', 'awm_reads');
	define('DBTABLE_AWM_ACCOUNTS', 'awm_accounts');
	define('DBTABLE_AWM_ADDR_GROUPS', 'awm_addr_groups');
	define('DBTABLE_AWM_ADDR_BOOK', 'awm_addr_book');
	define('DBTABLE_AWM_ADDR_GROUPS_CONTACTS', 'awm_addr_groups_contacts');
	define('DBTABLE_AWM_FOLDERS', 'awm_folders');
	define('DBTABLE_AWM_FOLDERS_TREE', 'awm_folders_tree');
	define('DBTABLE_AWM_FILTERS', 'awm_filters');
	define('DBTABLE_AWM_TEMP', 'awm_temp');
	define('DBTABLE_AWM_SENDERS', 'awm_senders');
	define('DBTABLE_AWM_COLUMNS', 'awm_columns');
	
	define('DBTABLE_CAL_USERS_DATA', 'acal_users_data');
	define('DBTABLE_CAL_CALENDARS', 'acal_calendars');
	define('DBTABLE_CAL_EVENTS', 'acal_events');

	define('DBTABLE_AWM_MESSAGES_INDEX', 'awm_messages_index');
	define('DBTABLE_AWM_MESSAGES_BODY_INDEX', 'awm_messages_body_index');
	
	/**
	 * @return array
	 */
	function GetTablesArray($pref)
	{
		return array(
				$pref.DBTABLE_A_USERS, $pref.DBTABLE_AWM_SETTINGS, $pref.DBTABLE_AWM_MESSAGES, 
				$pref.DBTABLE_AWM_MESSAGES_BODY, $pref.DBTABLE_AWM_READS, $pref.DBTABLE_AWM_ACCOUNTS,
				$pref.DBTABLE_AWM_ADDR_GROUPS, $pref.DBTABLE_AWM_ADDR_BOOK,
				$pref.DBTABLE_AWM_ADDR_GROUPS_CONTACTS, $pref.DBTABLE_AWM_FOLDERS,
				$pref.DBTABLE_AWM_FOLDERS_TREE, $pref.DBTABLE_AWM_FILTERS, $pref.DBTABLE_AWM_TEMP,
				$pref.DBTABLE_AWM_SENDERS, $pref.DBTABLE_AWM_COLUMNS,
				
				$pref.DBTABLE_CAL_USERS_DATA, $pref.DBTABLE_CAL_CALENDARS, $pref.DBTABLE_CAL_EVENTS,
				
				$pref.DBTABLE_AWM_MESSAGES_INDEX, $pref.DBTABLE_AWM_MESSAGES_BODY_INDEX
			);
	}
	
	/**
	 * @return array
	 */
	function GetIndexsArray()
	{
		return array(
			'awm_settings' => array('id_user'),
			'awm_reads' => array('id_acct'),
			'awm_columns' => array('id_user', 'id_column'),  
			'awm_messages' => array('id_folder_srv', 'id_folder_db'),
			'awm_senders' => array('id_user'),
			'awm_accounts' => array('id_user'),
			'awm_addr_groups' => array('id_user'),	  
			'awm_addr_book' => array('id_user'),
			'awm_folders' => array('id_acct', 'id_parent'),
			'awm_folders_tree' => array('id_folder', 'id_parent'),
			'awm_filters' => array('id_acct', 'id_folder')
		);
	}
	
	/**
	 * @abstract
	 */
	class DbStorage extends MailStorage
	{
		/**
		 * @access private
		 * @var short
		 */
		var $_escapeType;
		
		/**
		 * @access protected
		 * @var DbMySql
		 */
		var $_dbConnection;
		
		/**
		 * @access protected
		 * @var MySqlCommandCreator
		 */
		var $_commandCreator;
		
		
		/**
		 * @param Account $account
		 * @return MailServerStorage
		 */
		function DbStorage(&$account)
		{
			MailStorage::MailStorage($account);
		}
		
		/**
		 * @return bool
		 */
		function Connect()
		{
			if ($this->_dbConnection->_conectionHandle != null)
			{
				register_shutdown_function(array(&$this, 'Disconnect'));
				return true;
			}
			
			if ($this->_dbConnection->Connect())
			{
				return true;
			}
			else 
			{
				setGlobalError(PROC_CANT_LOAD_DB);
				return false;
			}
		}
		
		/**
		 * @return bool
		 */
		function Disconnect()
		{
			return  $this->_dbConnection->Disconnect();
		}
		
		/**
		 * @param string $pref
		 * @return bool
		 */
		function CheckExistTable($pref)
		{
			$tableArray = GetTablesArray($pref);
			
			if (!$this->_dbConnection->Execute($this->_commandCreator->AllTableNames()))
			{
				return false;
			}
			
			while ($array = $this->_dbConnection->GetNextArrayRecord())
			{
				if ($array && $array[0] && in_array($array[0], $tableArray))
				{
					return $array[0];
				}
			}
			
			return true;
		}
		
		function CreateAllIndex($pref)
		{
			$AddIndexArray = GetIndexsArray();
			foreach ($AddIndexArray as $tableName => $indexData) 
			{
				if (is_array($indexData))
				{
					foreach ($indexData as $fieldName) 
					{
						if (!$this->CheckExistIndex($pref, $tableName, $fieldName))
						{
							if (!$this->CreateIndex($pref, $tableName, $fieldName))
							{
								return false;
							}
						}
					}
				}
			}
			return true;
		}
		
		/**
		 * @param string $pref
		 * @param string $TableName
		 * @param string $fieldName
		 * @return bool
		 */
		function CheckExistIndex($pref, $TableName, $fieldName)
		{
			$indexArray = $this->GetIndexsOfTable($pref, $TableName);
			if (is_array($indexArray))
			{
				return in_array($fieldName, $indexArray);
			}
			
			return false;	
		}

		/**
		 * @param string $pref
		 * @param string $tableName
		 * @param string $fieldName
		 * @return bool
		 */
		function CreateIndex($pref, $tableName, $fieldName)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->CreateIndex(trim($pref), $tableName, $fieldName));
		}
		
		/**
		 * @param string $pref
		 * @param string $tablename
		 * @return bool
		 */
		function IsTableExist($pref, $tablename)
		{
		
			if (!$this->_dbConnection->Execute($this->_commandCreator->AllTableNames()))
			{
				return false;
			}
			
			while ($array = $this->_dbConnection->GetNextArrayRecord())
			{
				if ($array && $array[0] && $array[0] == $pref.$tablename)
				{
					return true;
				}
			}
			
			return false;
		}
		
		/**
		 * @param string $pref
		 * @return array
		 *//*
		function GetTablesNames($pref)
		{
			$returnArray = array();
			
			if (!$this->_dbConnection->Execute($this->_commandCreator->AllTableNames()))
			{
				return false;
			}
			
			while ($array = $this->_dbConnection->GetNextArrayRecord())
			{
				if ($array && $array[0])
				{
					$returnArray[] = $array[0];
				}
			}
			
			return $returnArray;
		}
		*/
		
		function CreateOneTable($pref, $tableName)
		{
			$sql = trim($this->_commandCreator->CreateTable($tableName, $pref));
			if (!$sql || $sql == '') return false;
			if (!$this->_dbConnection->Execute($sql))
			{
				return false;
			}		
			return true;

		}
		
		/**
		 * @param string $pref
		 * @param string $tableName
		 * @return array/bool
		 */
		function GetTablesColumns($pref, $tableName)
		{
			$returnArray = array();
			
			if (!$this->_dbConnection->Execute($this->_commandCreator->GetTablesColumns($pref, $tableName)))
			{
				return false;
			}
			
			while ($array = $this->_dbConnection->GetNextArrayRecord())
			{
				if ($array && $array[0])
				{
					$returnArray[] = $array[0];
				}
			}
			
			return $returnArray;			
		}
		
		/**
		 * @param array $emailsString
		 * @return array/bool
		 */	
		function SelectExistEmails(&$account, $emailsArray)
		{
			$returnArray = array();
			
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectExistEmails($account, $emailsArray)))
			{
				return false;
			}
			
			while ($array = $this->_dbConnection->GetNextArrayRecord())
			{
				if (is_array($array))
				{
					//h_email, b_email, other_email
					if ($array['h_email'] != '')
					{
						$returnArray[] = $array['h_email'];
					}
					if ($array['b_email'] != '')
					{
						$returnArray[] = $array['b_email'];
					}
					if ($array['other_email'] != '')
					{
						$returnArray[] = $array['other_email'];
					}
				}
			}
			return $returnArray;
		}
		
		
		function SelectSenderSafetyByEmail($email, $idUser)
		{
			if ($this->_dbConnection->Execute($this->_commandCreator->SelectSendersByEmail($email, $idUser)))
			{
				if ($row = $this->_dbConnection->GetNextRecord(true))
				{
					return (bool) $row->safety;
				}
			}
			return false;
		}
		
		/**
		 * @param string $email
		 * @param bool $safety
		 * @param int $idUser
		 * @return bool
		 */
		function SetSenders($email, $safety, $idUser)
		{
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectSendersByEmail($email, $idUser)))
			{
				return false;
			}
			
			if ($this->_dbConnection->ResultCount() > 0)
			{
				$row = &$this->_dbConnection->GetNextRecord(true);
				if (is_object($row) && isset($row->safety) && $row->safety != $safety)
				{
					if (!$this->_dbConnection->Execute($this->_commandCreator->UpdateSenders($email, $safety, $idUser)))
					{
						return false;
					}
				}
			}
			else 
			{
				if (!$this->_dbConnection->Execute($this->_commandCreator->InsertSenders($email, $safety, $idUser)))
				{
					return false;
				}
			}
			
			return true;
		}
		
		/**
		 * @param string $pref
		 * @return bool
		 */
		function CreateTables($pref)
		{
			$tableArray = GetTablesArray($pref);
			$original = GetTablesArray('');
			foreach ($tableArray as $key => $tname)			
			{
				$sql = trim($this->_commandCreator->CreateTable($original[$key], $pref));
				if (!$sql || $sql == '') return false;
				if (!$this->_dbConnection->Execute($sql))
				{
					return $tname;	
				}				
			}

			return true;
		}
		
		/**
		 * @param Account $account
		 * @return bool
		 */
		function InsertAccountData(&$account)
		{
			if (!$this->_dbConnection->Execute($this->_commandCreator->InsertAccount($account)))
			{
				return false;
			}	
			
			$account->Id = $this->_dbConnection->GetLastInsertId();
			
			return true;
			//$this->_dbConnection->Execute($this->_commandCreator->InsertSettings($account));
		}
		
		/**
		 * @param int $userId
		 * @return Array
		 */
		function &SelectAccounts($userId)
		{
			$outArray = null;
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectAccounts($userId)))
			{
				return $outArray;
			}
			
			$outArray = array();
			
			while ($row = $this->_dbConnection->GetNextRecord())
			{
				$outArray[$row->id_acct] = array($row->mail_protocol, $row->def_order, $row->use_friendly_nm,
													$row->friendly_nm, $row->email,
													(bool) abs($row->getmail_at_login), (bool) abs($row->def_acct));
			}
			
			return $outArray;
		}
		
		/**
		 * @return int
		 */
		function SelectAccountsCount($searchText)
		{
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectAccountsCount($searchText)))
			{
				return 0;
			}
			
			$count = $this->_dbConnection->ResultCount();
			
			return ($count)?$count:0;
			
			/*			
			$row = $this->_dbConnection->GetNextRecord();
			if ($row)
			{
				return (int) $row->account_count;
			}*/
		}
		
		/**
		 * @return int
		 */
		function SelectUsersCount()
		{
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectUsersCount()))
			{
				return 0;
			}
					
			$row = $this->_dbConnection->GetNextRecord();
			if ($row)
			{
				return (int) $row->cnt_user;
			}
			return 0;
		}

		
		/**
		 * @param string $email
		 * @return Array
		 */
		function &SelectAccountDataOnlyByEmail($email)
		{
			$resArray = null;
			if ($this->_dbConnection->Execute($this->_commandCreator->SelectAccountDataOnlyByEmail($email)))
			{
				$row = $this->_dbConnection->GetNextRecord();
				if (is_object($row))
				{
					$resArray = array($row->id_acct, $row->mail_inc_pass, $row->def_acct, $row->id_user, $row->mail_inc_login);
					$this->_dbConnection->FreeResult();
				}
			}
			return $resArray;
		}

		/**
		 * @param string $email
		 * @param string $login
		 * @param bool $onlyDef
		 * @return Array
		 */
		function &SelectAccountDataByLogin($email, $login, $onlyDef = false)
		{
			$resArray = null;

			$result = ($onlyDef)
				? $this->_dbConnection->Execute($this->_commandCreator->SelectDefAccountDataByLogin($email, $login))
				: $this->_dbConnection->Execute($this->_commandCreator->SelectAccountDataByLogin($email, $login));
			
			if ($result)
			{
				$row = $this->_dbConnection->GetNextRecord();
				if (is_object($row))
				{
					$resArray = array($row->id_acct, $row->mail_inc_pass, $row->def_acct, $row->id_user);
					$this->_dbConnection->FreeResult();
				}
			}
			
			return $resArray;
		}

		/**
		 * @param int $accountId
		 * @param int $newAccountId
		 * @return bool
		 */
		function IsAccountInRing($accountId, $newAccountId)
		{
			if ($accountId == $newAccountId)
			{
				return true;
			}
			
			$result = false;
			if ($this->_dbConnection->Execute($this->_commandCreator->SelectIsAccountInRing($accountId, $newAccountId)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$result = $row->acct_count > 0;
				}
			}
			
			return $result;
		}
		
		/**
		 * @param string $email
		 * @param string $login
		 * @param bool $onlyDef
		 * @return int
		 */
		function SelectAccountsCountByLogin($email, $login, $onlyDef = false, $isAcct = -1)
		{
			$count = 0;
			$result = ($onlyDef)
				? $this->_dbConnection->Execute($this->_commandCreator->SelectDefAccountsCountByLogin($email, $login, $isAcct))
				: $this->_dbConnection->Execute($this->_commandCreator->SelectAccountsCountByLogin($email, $login));
				
			if ($result)
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$count = $row->acct_count;
				}
			}
			
			return $count;
		}
		
		/**
		 * @param int $id
		 * @return Account
		 */
		function &SelectAccountData($id)
		{
			$null = null;
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectAccountData($id)))
			{
				return $null;
			}
			
			$account = &new Account();
			
			if ($row = $this->_dbConnection->GetNextRecord())
			{
				$account->Id = (int) $row->id_acct;
				$account->IdUser = (int) $row->id_user;
				$account->DefaultAccount = (bool) abs($row->def_acct);
				$account->Deleted = (bool) abs($row->deleted);
				$account->Email = $row->email;
				$account->MailProtocol = (int) $row->mail_protocol;
				$account->MailIncHost = $row->mail_inc_host;
				$account->MailIncLogin = $row->mail_inc_login;
				$account->MailIncPort = (int) $row->mail_inc_port;
				$account->MailOutHost = $row->mail_out_host;
				$account->MailOutLogin = $row->mail_out_login;
				$account->MailOutPort = (int) $row->mail_out_port;
				$account->MailOutAuthentication = (int) $row->mail_out_auth;
				$account->FriendlyName = $row->friendly_nm;
				$account->UseFriendlyName = (bool) abs($row->use_friendly_nm);
				$account->DefaultOrder = (int) $row->def_order;
				$account->GetMailAtLogin = (bool) abs($row->getmail_at_login);
				$account->MailMode = (int) $row->mail_mode;
				$account->MailsOnServerDays = (int) $row->mails_on_server_days;
				$account->Signature = $row->signature;
				$account->SignatureType = (int) $row->signature_type;
				$account->SignatureOptions = (int) $row->signature_opt;
				$account->HideContacts = (bool) abs($row->hide_contacts);
				$account->MailsPerPage = ((int) $row->msgs_per_page > 0) ? (int) $row->msgs_per_page : 20;
				$account->Delimiter = $row->delimiter;
				$account->WhiteListing = (bool) abs($row->white_listing);
				$account->XSpam = (bool) abs($row->x_spam);
				$account->LastLogin = (int) $row->last_login;
				$account->LoginsCount = (int) $row->logins_count;
				$account->DefaultSkin = $row->def_skin;
				$account->DefaultLanguage = $row->def_lang;
				$account->DefaultIncCharset = ConvertUtils::GetCodePageName((int)$row->def_charset_inc);
				$account->DefaultOutCharset = ConvertUtils::GetCodePageName((int)$row->def_charset_out);
				$account->DefaultTimeZone = (int) $row->def_timezone;
				
				$account->DefaultDateFormat = CDateTime::GetDateFormatFromBd($row->def_date_fmt);
				$account->DefaultTimeFormat = CDateTime::GetTimeFormatFromBd($row->def_date_fmt);
				
				$account->HideFolders = (bool) abs($row->hide_folders);
				$account->MailboxLimit = (int) $row->mailbox_limit;
				$account->MailboxSize = (int) $row->mailbox_size;
				$account->AllowChangeSettings = (bool) abs($row->allow_change_settings);
				$account->AllowDhtmlEditor = (bool) abs($row->allow_dhtml_editor);
				$account->AllowDirectMode = (bool) abs($row->allow_direct_mode);
				$account->DbCharset = ConvertUtils::GetCodePageName((int) $row->db_charset);
				$account->HorizResizer = (int) $row->horiz_resizer;
				$account->VertResizer = (int) $row->vert_resizer;
				$account->Mark = (int) $row->mark;
				$account->Reply = (int) $row->reply;
				$account->ContactsPerPage = ((int) $row->contacts_per_page > 0) ? (int) $row->contacts_per_page : 20;
				$account->ViewMode = (int) $row->view_mode;				
				$account->MailIncPassword = ConvertUtils::DecodePassword($row->mail_inc_pass, $account);
				$account->MailOutPassword = ConvertUtils::DecodePassword($row->mail_out_pass, $account);

				$this->_dbConnection->FreeResult();
			}
			else
			{
				$account = $null;
			}
			
			if (!is_object($account) || !$this->_dbConnection->Execute($this->_commandCreator->SelectAccountColumnsData($account->IdUser)))
			{
				return $null;
			}
			
			while ($row = $this->_dbConnection->GetNextRecord()) 
			{
				if (!is_object($row)) continue;
				$account->Columns[(int) $row->id_column] = $row->column_value;
			}
			
			return $account;
		}
		
		/**
		 * @param string $email
		 * @param string $login
		 * @return Account
		 */
		function &SelectAccountFullDataByLogin($email, $login)
		{
			$null = null;
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectAccountFullDataByLogin($email, $login)))
			{
				return $null;
			}
			
			$account = &new Account();
			
			if ($row = $this->_dbConnection->GetNextRecord())
			{
				$account->Id = (int) $row->id_acct;
				$account->IdUser = (int) $row->id_user;
				$account->DefaultAccount = (bool) $row->def_acct;
				$account->Deleted = (bool) $row->deleted;
				$account->Email = $row->email;
				$account->MailProtocol = (int) $row->mail_protocol;
				$account->MailIncHost = $row->mail_inc_host;
				$account->MailIncLogin = $row->mail_inc_login;
				$account->MailIncPort = (int) $row->mail_inc_port;
				$account->MailOutHost = $row->mail_out_host;
				$account->MailOutLogin = $row->mail_out_login;
				$account->MailOutPort = (int) $row->mail_out_port;
				$account->MailOutAuthentication = (int) $row->mail_out_auth;
				$account->FriendlyName = $row->friendly_nm;
				$account->UseFriendlyName = (bool) $row->use_friendly_nm;
				$account->DefaultOrder = (int) $row->def_order;
				$account->GetMailAtLogin = (bool) $row->getmail_at_login;
				$account->MailMode = (int) $row->mail_mode;
				$account->MailsOnServerDays = (int) $row->mails_on_server_days;
				$account->Signature = $row->signature;
				$account->SignatureType = (int) $row->signature_type;
				$account->SignatureOptions = (int) $row->signature_opt;
				$account->HideContacts = (bool) $row->hide_contacts;
				$account->MailsPerPage = (int) $row->msgs_per_page;
				$account->Delimiter = $row->delimiter;
				$account->WhiteListing = (bool) $row->white_listing;
				$account->XSpam = (bool) $row->x_spam;
				$account->LastLogin = (int) $row->last_login;
				$account->LoginsCount = (int) $row->logins_count;
				$account->DefaultSkin = $row->def_skin;
				$account->DefaultLanguage = $row->def_lang;
				$account->DefaultIncCharset = ConvertUtils::GetCodePageName((int)$row->def_charset_inc);
				$account->DefaultOutCharset = ConvertUtils::GetCodePageName((int)$row->def_charset_out);
				$account->DefaultTimeZone = (int) $row->def_timezone;
				$account->DefaultDateFormat = $row->def_date_fmt;
				$account->HideFolders = (bool) $row->hide_folders;
				$account->MailboxLimit = (int) $row->mailbox_limit;
				$account->MailboxSize = (int) $row->mailbox_size;
				$account->AllowChangeSettings = (bool) $row->allow_change_settings;
				$account->AllowDhtmlEditor = (bool) $row->allow_dhtml_editor;
				$account->AllowDirectMode = (bool) $row->allow_direct_mode;
				$account->DbCharset = ConvertUtils::GetCodePageName((int) $row->db_charset);
				$account->HorizResizer = (int) $row->horiz_resizer;
				$account->VertResizer = (int) $row->vert_resizer;
				$account->Mark = (int) $row->mark;
				$account->Reply = (int) $row->reply;
				$account->ContactsPerPage = (int) $row->contacts_per_page;
				$account->ViewMode = (int) $row->view_mode;
				
				$account->MailIncPassword = ConvertUtils::DecodePassword($row->mail_inc_pass, $account);
				$account->MailOutPassword = ConvertUtils::DecodePassword($row->mail_out_pass, $account);

				$this->_dbConnection->FreeResult();
			}
			else
			{
				$account = $null;
			}
			
			return $account;
		}
		
		/**
		 * @param Account $account
		 * @param int $userId
		 * @return bool
		 */
		function SelectSetings(&$account, $userId)
		{
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectSetings($userId)))
			{
				return false;
			}
				
			while ($row = $this->_dbConnection->GetNextRecord())
			{
				$account->IdUser = $row->id_user;
				$account->HideContacts = $row->hide_contacts;
				$account->MailsPerPage = $row->msgs_per_page;
				$account->Delimiter = $row->delimiter;
				$account->WhiteListing = $row->white_listing;
				$account->XSpam = $row->x_spam;
				$account->LastLogin = $row->last_login;
				$account->LoginsCount = $row->logins_count;
				$account->DefaultSkin = $row->def_skin;
				$account->DefaultLanguage = $row->def_lang;
				$account->DefaultIncCharset = ConvertUtils::GetCodePageName($row->def_charset_inc);
				$account->DefaultOutCharset = ConvertUtils::GetCodePageName($row->def_charset_out);
				$account->DefaultTimeZone = $row->def_timezone;
				$account->DefaultDateFormat = $row->def_date_fmt;
				$account->HideFolders = $row->hide_folders;
				$account->MailboxLimit = $row->mailbox_limit;
				$account->MailboxSize = $row->mailbox_size;
				$account->AllowChangeSettings = $row->allow_change_settings;
				$account->AllowDhtmlEditor = $row->allow_dhtml_editor;
				$account->AllowDirectMode = $row->allow_direct_mode;
				$account->DbCharset = ConvertUtils::GetCodePageName($row->db_charset);
				$account->HorizResizer = $row->horiz_resizer;
				$account->VertResizer = $row->vert_resizer;
				$account->Mark = $row->mark;
				$account->Reply = $row->reply;
				$account->ContactsPerPage = $row->contacts_per_page;
				$account->ViewMode = $row->view_mode;
				
			}
			
			return true;
		}
		
		/**
		 * @return array
		 */
		function &SelectAllAccounts($pageNumber, $accountPerPage, $sortField, $sortOrder, $searchText)
		{
			$mailBoxSizes = array();
			if ($this->_dbConnection->Execute($this->_commandCreator->CountAllMailboxSizes()))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$mailBoxSizes[$row->id_user] = $row->mailboxes_size;
				}
			}
			
			$outarray = array();
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectAllAccounts($pageNumber, $accountPerPage, $sortField, $sortOrder, $searchText)))
			{
				return $outarray;
			}
			
			while ($row = $this->_dbConnection->GetNextRecord())
			{
				if ($row)
				{
					$temp = array();
					$temp['Id'] = (int) $row->id_acct;
					$temp['IdUser'] = (int) $row->id_user;
					$temp['Deleted'] = $row->deleted;
					$temp['Email'] = $row->email;
					$temp['MailIncHost'] = $row->mail_inc_host;
					$temp['MailOutHost'] = $row->mail_out_host;
					$temp['LastLogin'] = $row->nlast_login;
					$temp['LoginsCount'] = (int) $row->logins_count;
					$temp['MailboxSize'] = $row->mailbox_size;
					$temp['MailboxLimit'] = $row->mailbox_limit;
					$temp['UserMailboxSize'] = isset($mailBoxSizes[$row->id_user]) ? $mailBoxSizes[$row->id_user] : 0;
					$temp['DefAcct'] = $row->def_acct;
					
					$outarray[] = $temp;
				}
			}
			
			$this->_dbConnection->FreeResult();
		
			return $outarray;
		}
		
		
		/**
		 * @param int $groupId
		 * @return AddressGroup
		 */
		function SelectGroupById($groupId)
		{
			$group = null;

			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectGroupById($groupId)))
			{
				return $group;
			}			
			
			if ($row = $this->_dbConnection->GetNextRecord())
			{			
				$group = &new AddressGroup();
				$group->Id = $groupId;	
				$group->IdUser = $row->id_user;
				$group->Name = $row->group_nm;
				$group->Email = $row->email;
				$group->Company = $row->company;
				$group->Street = $row->street;
				$group->City = $row->city;
				$group->State = $row->state;
				$group->Zip = $row->zip;
				$group->Country = $row->country;
				$group->Phone = $row->phone;
				$group->Fax = $row->fax;
				$group->Web = $row->web;
				$group->IsOrganization = (bool) $row->organization;
				
				$this->_dbConnection->FreeResult();
			}

			return $group;
		}
		
		/**
		 * @param array $arrIds
		 * @return bool
		 */
		function UpdateGroupsFrequency($arrIds)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->UpdateGroupsFrequency($arrIds));
		}
		
		/**
		 * @param Account $account
		 * @return bool
		 */
		function UpdateAccountData(&$account)
		{
			if (!$this->_dbConnection->Execute($this->_commandCreator->UpdateAccount($account)) ||
				!$this->_dbConnection->Execute($this->_commandCreator->UpdateSettings($account)))
			{
				return false;
			}
			
			return $this->UpdateColumns($account);
		}
		
		/**
		 * @param Account $account
		 * @return bool
		 */
		function UpdateColumns(&$account)
		{
			$existColumns = array();
			if (is_array($account->Columns) && count($account->Columns) > 0)
			{
				if (!$this->_dbConnection->Execute($this->_commandCreator->SelectAccountColumnsData($account->IdUser)))
				{
					return false;
				}
				else 
				{
					while ($row = $this->_dbConnection->GetNextRecord())
					{
						if (is_object($row))
						{
							$existColumns[(int) $row->id_column] = $row->column_value;
						}
					}
				}
				
				$cnt = count($existColumns);
				foreach ($account->Columns As $id_column => $colun_value)
				{
					if ($cnt > 0)
					{
						if (key_exists($id_column, $existColumns))
						{
							if ($existColumns[$id_column] != $colun_value)
							{
								$result = $this->_dbConnection->Execute($this->_commandCreator->UpdateColumnData($account->IdUser, $id_column, $colun_value));
								if (!$result)
								{
									return false;
								}
							}
						}
						else
						{
							$result = $this->_dbConnection->Execute($this->_commandCreator->InsertColumnData($account->IdUser, $id_column, $colun_value));
							if (!$result)
							{
								return false;
							}							
						}
					}
					else 
					{
						$result = $this->_dbConnection->Execute($this->_commandCreator->InsertColumnData($account->IdUser, $id_column, $colun_value));
						if (!$result)
						{
							return false;
						}	
					}
				}
			}
			return true;
		}
		
		
		/**
		 * @param Array $messageIndexSet
		 * @param Boolean $indexAsUid
		 * @param Folder $folder
		 * @param Int $flags
		 * @param Account $account
		 * @return unknown
		 */
		function UpdateMessageFlags($messageIndexSet, $indexAsUid, &$folder, $flags, &$account)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->UpdateMessageFlags($messageIndexSet, $indexAsUid, $folder, $flags, $account));					
		}
	
		/**
		 * @param int $userId
		 * @return bool
		 */
		function UpdateLastLoginAndLoginsCount($userId)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->UpdateLastLoginAndLoginsCount($userId));
		}
		
		/**
		 * @param array $emailArray
		 * @return bool
		 */
		function DeleteAccountsDataByEmailsForWmServer($emailArray)
		{
			$id_accts = array();
			
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectAccountsIdByEmails($emailArray)))
			{
				return false;
			}
			
			while ($row = $this->_dbConnection->GetNextRecord())
			{
				$id_accts[] = (int) $row->id_acct;
			}
			$return = true;
			
			if (count($id_accts) > 0)
			{
				foreach ($id_accts as $id_acct) 
				{
					$return &= $this->DeleteAccountData($id_acct);
				}
			}
			
			return $return;
		}
		
		/**
		 * @param int $id
		 * @return bool
		 */
		function DeleteAccountData($id)
		{
			$count = 0;
			
			if (!$this->_dbConnection->Execute($this->_commandCreator->CountAccounts($id)))
			{
				return false;
			}
			
			while ($row = $this->_dbConnection->GetNextRecord())
			{
				$count = $row->count;
				$id_user = $row->id_user;
			}
			
			$result = true;

			if ($count > 0)
			{
				$sql = 'DELETE FROM %sawm_accounts WHERE id_acct = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id);
				$result = $this->_dbConnection->Execute($query);
				
				$sql = 'DELETE FROM %sawm_messages WHERE id_acct = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id);
				$result &= $this->_dbConnection->Execute($query);
				
				$sql = 'DELETE FROM %sawm_messages_body WHERE id_acct = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id);
				$result &= $this->_dbConnection->Execute($query);
	
				$sql = 'DELETE FROM %sawm_filters WHERE id_acct = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id);
				$result &= $this->_dbConnection->Execute($query);
				
				$sql = 'DELETE FROM %sawm_reads WHERE id_acct = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id);
				$result &= $this->_dbConnection->Execute($query);
			
				$result &= $this->_dbConnection->Execute($this->_commandCreator->DeleteFolderTreeById($id));
				
				$sql = 'DELETE FROM %sawm_folders WHERE id_acct = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id);
				$result &= $this->_dbConnection->Execute($query);
			}
			
			//last account
			if ($count == 1)
			{
				$sql = 'DELETE FROM %sawm_addr_book WHERE id_user = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id_user);
				$result &= $this->_dbConnection->Execute($query);
				
				$sql = 'DELETE FROM %sawm_settings WHERE id_user = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id_user);
				$result &= $this->_dbConnection->Execute($query);
				
				// contacts
				$result &= $this->_dbConnection->Execute($this->_commandCreator->DeleteAddrGroupsContactsById($id));

				$sql = 'DELETE FROM %sawm_addr_groups WHERE id_user = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id_user);
				$result &= $this->_dbConnection->Execute($query);
				
				$sql = 'DELETE FROM %sawm_columns WHERE id_user = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id_user);
				$result &= $this->_dbConnection->Execute($query);
				
				$sql = 'DELETE FROM %sawm_senders WHERE id_user = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id_user);
				$result &= $this->_dbConnection->Execute($query);
				
				$sql = 'UPDATE %sa_users SET deleted = 1 WHERE id_user = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix , $id_user);
				$result &= $this->_dbConnection->Execute($query);
				
				//calendar
				$result &= $this->_dbConnection->Execute($this->_commandCreator->DeleteCalendarEvents($id_user));
				
				$sql = 'DELETE FROM %sacal_calendars WHERE user_id = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id_user);
				$result &= $this->_dbConnection->Execute($query);
								
				$sql = 'DELETE FROM %sacal_users_data WHERE user_id = %d';
				$query = sprintf($sql, $this->_settings->DbPrefix, $id_user);
				$result &= $this->_dbConnection->Execute($query);
				
			}
			
			return $result;
		}
		
		/**
		 * @param int $id_user
		 * @return bool
		 */
		function DeleteSettingsData($id_user)
		{
			$sql = 'DELETE FROM %sawm_settings WHERE id_user = %d';
			$query = sprintf($sql, $this->_settings->DbPrefix, $id_user);
			$result = $this->_dbConnection->Execute($query);
			
			$sql = 'DELETE FROM %sa_users WHERE id_user = %d';
			$query = sprintf($sql, $this->_settings->DbPrefix, $id_user);
			$result &= $this->_dbConnection->Execute($query);
			return $result;
		}
		
		/**
		 * @param User $user
		 * @return bool
		 */
		function InsertUserData(&$user)
		{
			if (!$this->_dbConnection->Execute($this->_commandCreator->InsertUserData($user->Deleted)))
			{
				return false;
			}
			
			$user->Id = $this->_dbConnection->GetLastInsertId();

			return true;
		}

		/**
		 * @param CalendarUser $calUser
		 * @return bool
		 */
		function InsertCalendarSettings(&$calUser)
		{
			if (!$this->_dbConnection->Execute($this->_commandCreator->InsertCalendarSettings($calUser)))
			{
				return false;
			}
			return true;
		}
		
		/**
		 * @param int $id
		 * @return bool
		 */
		function DeleteUserData($id)
		{
			$sql = 'UPDATE %sa_users SET deleted = 1 WHERE id_user = %d';
			$query = sprintf($sql, $this->_settings->DbPrefix, $id);

			return $this->_dbConnection->Execute($query);
		}

		/**
		 * @param Account $account
		 * @return bool
		 */
		function InsertSettings(&$account)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->InsertSettings($account));
		}
		
		/**
		 * @param FolderCollection $folders
		 * @return bool
		 */
		function CreateFolders(&$folders)
		{
			$result = true;
			if ($folders == null)
			{
				return $result;
			}
			
			for ($i = 0, $count = $folders->Count(); $i < $count; $i++)
			{
				$folder = &$folders->Get($i);

				$result &= $this->CreateFolder($folder);
				
				if (!is_null($folder->SubFolders))
				{
					for ($j = 0; $j < count($folder->SubFolders->Instance()); $j++)
					{
						$subFolder = &$folder->SubFolders->Get($j);
						$subFolder->IdParent = $folder->IdDb;
					}
					$result &= $this->CreateFolders($folder->SubFolders);
				}
							
			}

			return $result;										
		}
		
		/**
		 * @return FolderCollection
		 */
		function &GetFolders()
		{
			if (!$this->_dbConnection->Execute($this->_commandCreator->GetFolders($this->Account->Id)))
			{
				$null = null;
				return $null;
			}
			
			$folders = array();
			while ($row = $this->_dbConnection->GetNextRecord())
			{
				$folder = &new Folder($this->Account->Id, (int) $row->id_folder,
										substr($row->full_path, 0, -1), substr($row->name, 0, -1));
				$folder->IdParent = $row->id_parent;
				$folder->Type = (int) $row->type;
				$folder->SyncType = (int) $row->sync_type;
				$folder->Hide = (bool) abs($row->hide);
				$folder->FolderOrder = (int) $row->fld_order;
				$folder->MessageCount = (int) $row->message_count;
				$folder->UnreadMessageCount = (int) $row->unread_message_count;
				$folder->Size = (int) $row->folder_size;
				$folder->Level = (int) $row->level;
				$folders[] = &$folder;
			}
			
			$folderCollection = &new FolderCollection();
			
			$this->_addLevelToFolderTree($folderCollection, $folders);
			return $folderCollection;
		}
		
		/**
		 * @param Folder $folder
		 * @return bool
		 */
		function DeleteFolder(&$folder)
		{
			$result = true;
			$result &= $this->_dbConnection->Execute($this->_commandCreator->DeleteFolder($folder));
			$result &= $this->_dbConnection->Execute($this->_commandCreator->DeleteFolderTree($folder));
			return $result;
		}

		/**
		 * @param Folder $folder
		 * @param string $newName
		 * @return bool
		 */
		function RenameFolder(&$folder, $newName)
		{
			$result = $this->_dbConnection->Execute($this->_commandCreator->RenameFolder($folder, $newName));
			
			//$newSubPath = substr($folder->FullName, 0,
			//			strrpos(trim($folder->FullName, $this->Account->Delimiter), $this->Account->Delimiter));
			//$newSubPath .= $newName;
			
			$foldersId = array();
			if ($this->_dbConnection->Execute($this->_commandCreator->SelectSubFoldersId($folder)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$foldersId[] = $row->id_folder;
				}
			}
			
			if (count($foldersId) > 0)
			{
				$result &= $this->_dbConnection->Execute($this->_commandCreator->RenameSubFoldersPath($folder, $foldersId, $newName));
			}
			
			return $result; 
		}
		
		/**
		 * @param Folder $folder
		 * @return bool
		 */
		function UpdateFolder(&$folder)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->UpdateFolder($folder));
		}
		
		/**
		 * @param Folder $folder
		 * @return bool
		 */
		function GetFolderMessageCount(&$folder)
		{
		
			if (!$this->_dbConnection->Execute($this->_commandCreator->GetFolderMessageCountAll($folder)))
			{
				return false;
			}
			
			if ($row = $this->_dbConnection->GetNextRecord())
			{
				$folder->MessageCount = ($row->message_count) ? $row->message_count : 0;
			}
			else 
			{
				$folder->MessageCount = 0;
			}
			
			if (!$this->_dbConnection->Execute($this->_commandCreator->GetFolderMessageCountUnread($folder)))
			{
				return false;
			}
			
			if ($row = $this->_dbConnection->GetNextRecord())
			{
				$folder->UnreadMessageCount = ($row->unread_message_count) ? $row->unread_message_count : 0;
			}
			else 
			{
				$folder->UnreadMessageCount = 0;
			}
			
			return true;
		}
		
		/**
		 * @param Folder $folder
		 */
		function GetFolderInfo(&$folder)
		{
			if (!$folder || !$this->_dbConnection->Execute($this->_commandCreator->GetFolderInfo($folder)))
			{
				return;
			}
			
			if ($row = $this->_dbConnection->GetNextRecord())
			{
				$folder->FullName = substr($row->full_path, 0, -1);
				$folder->Name = substr($row->name, 0, -1);
				$folder->Type = $row->type;
				$folder->SyncType = $row->sync_type;
				$folder->Hide = (bool) abs($row->hide);
				$folder->FolderOrder = (int) $row->fld_order;
				$folder->IdParent = (int) $row->id_parent;
			}
		}
		
		/**
		 * @param Folder $folder
		 * @return int
		 */
		function GetFolderChildCount(&$folder)
		{
			$result = -1;
			if ($this->_dbConnection->Execute($this->_commandCreator->GetFolderChildCount($folder)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$result = ($row->child_count != null)?$row->child_count:0;
				}
			}

			return $result;
		}

		/**
		 * @param short $type
		 * @return short
		 */
		function GetFolderSyncType($type)
		{
			$result = -1;
			if (!$this->_dbConnection->Execute($this->_commandCreator->GetFolderSyncType($this->Account->Id, $type)))
			{
				return $result;
			}
			
			if ($row = $this->_dbConnection->GetNextRecord())
			{
				$result = $row->sync_type;
			}
			return $result;
		}
		
		/**
		 * @access private
		 * @param FolderCollection $folderCollection
		 * @param Array $folders
		 * @param string $rootPrefix optional
		 */
		function _addLevelToFolderTree(&$folderCollection, &$folders, $rootPrefix = '', $isToFolder = false)
		{
			$prefixLen = strlen($rootPrefix);
			$foldersCount = count($folders);
			for ($i = 0; $i < $foldersCount; $i++)
			{
				$folderFullName = $folders[$i]->FullName;
				if ($rootPrefix != $folderFullName && strlen($folderFullName) > $prefixLen &&
					substr($folderFullName, 0, $prefixLen) == $rootPrefix &&
					strpos($folderFullName, $this->Account->Delimiter, $prefixLen + 1) === false)
				{
					$folderObj = &$folders[$i];
					$isTo = ($isToFolder || $folderObj->Type == FOLDERTYPE_Drafts || $folderObj->Type == FOLDERTYPE_SentItems);
					
					$folderObj->ToFolder = $isTo;
					$folderCollection->Add($folderObj);
					
					$newCollection = &new FolderCollection();
					$this->_addLevelToFolderTree($newCollection, $folders, $folderFullName.$this->Account->Delimiter, $isTo);
					if ($newCollection->Count() > 0)
					{
						$folderObj->SubFolders = &$newCollection;
					}
				}
			}
		}
		
		/**
		 * @param WebMailMessage $message
		 * @param Folder $folder
		 * @param bool $downloaded
		 * @return bool
		 */
		function SaveMessageHeader(&$message, &$folder, $downloaded)
		{
			$result = $this->_dbConnection->Execute($this->_commandCreator->SaveMessageHeader($message, $folder, $downloaded, $this->Account));
			
			$message->IdDb = $this->_dbConnection->GetLastInsertId();

			return $result;
		}
		
		/**
		 * @param WebMailMessage $message
		 * @param Folder $folder
		 * @return bool
		 */
		function UpdateMessageHeader(&$message, &$folder)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->UpdateMessageHeader($message, $folder, $this->Account));
		}
		
		/**
		 * @param WebMailMessageCollection $messages
		 * @param Folder $folder
		 * @param bool $downloaded
		 * @return bool
		 */
		function SaveMessageHeaders(&$messages, &$folder, $downloaded)
		{
			$result = true;

			for ($i = 0, $count = $messages->Count(); $i < $count; $i++)
			{
				$result &= $this->SaveMessageHeader($messages->Get($i), $folder, $downloaded);
			}

			return $result;
		}
		
		/**
		 * @param WebMailMessage $message
		 * @param Folder $folder
		 * @return int|false
		 */
		function MessageSize(&$message, &$folder)
		{
			$result = -1;
			if (!$this->_dbConnection->Execute($this->_commandCreator->GetMessageSize($message, $folder, $this->Account->Id)))
			{
				return false;
			}
			
			if ($row = $this->_dbConnection->GetNextRecord())
			{
				$result = $row->size;
			}
			return $result;
		}
		
		/**
		 * @param WebMailMessage $message
		 * @param Folder $folder
		 * @return bool
		 */
		function UpdateMessage(&$message, &$folder)
		{
			if (!$this->UpdateMessageHeader($message, $folder, true))
			{
				return false;
			}
			
			$result = true;
			
			if ($this->_settings->StoreMailsInDb)
			{
				$result = $this->_dbConnection->Execute($this->_commandCreator->UpdateBody($message, $this->Account->Id));
			}
			else
			{
				$fs = &new FileSystem(INI_DIR.'/mail', $this->Account->Email, $this->Account->Id);
				$result = $fs->UpdateMessage($message, $folder);
			}
			
			if (!$result)
			{
				setGlobalError(PROC_CANT_SAVE_MSG);
			}
			
			return $result;
		}
		
		/**
		 * @param WebMailMessage $message
		 * @param Folder $folder
		 * @return bool
		 */
		function SaveMessage(&$message, &$folder)
		{
			$lastIdMsg = $this->SelectLastIdMsg();
			$lastIdMsg += rand(1, 10);
			$message->IdMsg = $lastIdMsg;

			if (!$this->SaveMessageHeader($message, $folder, true))
			{
				return false;
			}
			
			$result = true;
			
			if ($this->_settings->StoreMailsInDb)
			{
				//save body				
				$result = $this->_dbConnection->Execute($this->_commandCreator->SaveBody($message, $this->Account->Id));
			}
			else
			{
				$fs = &new FileSystem(INI_DIR.'/mail', $this->Account->Email, $this->Account->Id);
				$result = $fs->SaveMessage($message, $folder);
			}
			
			if (!$result)
			{
				setGlobalError(PROC_CANT_SAVE_MSG);
				$tempArray = array($message->IdMsg);
				$this->DeleteMessages($tempArray, false, $folder);
			}
			
			return $result;
		}
		
		/**
		 * @param WebMailMessageCollection $messages
		 * @param Folder $folder
		 * @return bool
		 */
		function SaveMessages(&$messages, &$folder)
		{
			$result = true;
			for ($i = 0, $count = $messages->Count(); $i < $count; $i++)
			{
				$mess =& $messages->Get($i);
				if ($mess) 
				{
					$result &= $this->SaveMessage($mess, $folder);
				}
				else 
				{
					$result = false;
				}
			}
			return $result;
		}
		
		/**
		 * @param int $pageNumber
		 * @param Folder $folder
		 * @return WebMailMessageCollection
		 */
		function &LoadMessageHeaders($pageNumber, &$folder)
		{
			$mailCollection = null;
			if (!$this->_dbConnection->Execute($this->_commandCreator->LoadMessageHeaders($pageNumber, $folder, $this->Account)))
			{
				return $mailCollection;
			}
		
			$mailCollection = &new WebMailMessageCollection();
			
			while ($row = $this->_dbConnection->GetNextRecord())
			{
				$msg = &new WebMailMessage();
				$msg->SetFromAsString($row->from_msg);
				$msg->SetToAsString($row->to_msg);
				$msg->SetCcAsString($row->cc_msg);
				$msg->SetBccAsString($row->bcc_msg);
				
				$date = &new CDateTime();
				$date->SetFromANSI($row->nmsg_date);
				$date->TimeStamp += $date->GetServerTimeZoneOffset();
				$msg->SetDate($date);
				
				$msg->SetSubject($row->subject);
				
				$msg->IdMsg = $row->id_msg;
				$msg->IdFolder = $row->id_folder_db;
				$msg->Uid = $row->uid;
				$msg->Size = $row->size;
				$msg->DbPriority = $row->priority;
				$msg->DbXSpam = (bool) abs($row->x_spam);
				
				$msg->DbHasAttachments = $row->attachments;
				
				$msg->Flags = 0;
				
				if ($row->seen)
				{
					$msg->Flags |= MESSAGEFLAGS_Seen;
				}
				if ($row->flagged)
				{
					$msg->Flags |= MESSAGEFLAGS_Flagged;
				}
				if ($row->deleted)
				{
					$msg->Flags |= MESSAGEFLAGS_Deleted;
				}
				if ($row->replied)
				{
					$msg->Flags |= MESSAGEFLAGS_Answered;
				}
				if ($row->forwarded)
				{
					$msg->Flags |= MESSAGEFLAGS_Forwarded;
				}
				if ($row->grayed)
				{
					$msg->Flags |= MESSAGEFLAGS_Grayed;
				}

				$msg->Charset = $row->charset;
				
				$mailCollection->Add($msg);
			}

			return $mailCollection;	
		}
		
		
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @return WebMailMessageCollection
		 */
		function &LoadMessages(&$messageIndexSet, $indexAsUid, &$folder)
		{
			
			$mailCollection = &new WebMailMessageCollection();
			if ($this->_settings->StoreMailsInDb)
			{
				//read messages from db
				if (!$this->_dbConnection->Execute($this->_commandCreator->LoadMessagesFromDB($messageIndexSet, $indexAsUid, $folder, $this->Account)))
				{
					return null;
				}
			
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$msg = &new WebMailMessage();
					$msg->LoadMessageFromRawBody($row->msg);
					$msg->IdMsg = $row->id_msg;
					$msg->Uid = $row->uid;
					$msg->DbPriority = $row->priority;
					$msg->Flags = $row->flags;
					
					$mailCollection->Add($msg);
				}

			}
			else
			{
				$fs = &new FileSystem(INI_DIR.'/mail', $this->Account->Email, $this->Account->Id);
				
				//read messages from file system
				if (!$this->_dbConnection->Execute($this->_commandCreator->LoadMessagesFromFileSystem($messageIndexSet, $indexAsUid, $folder, $this->Account)))
				{
					return null;
				}

				while ($row =  $this->_dbConnection->GetNextRecord())
				{
					$msg = &$fs->LoadMessage($row->id_msg, $folder);
					if ($msg != null)
					{
						$msg->IdMsg = $row->id_msg;
						$msg->Uid = $row->uid;
						$msg->DbPriority = $row->priority;
						$msg->Flags = $row->flags;

						$mailCollection->Add($msg);
					}
				}
			}

			return $mailCollection;	
		}
		
		/**
		 * @param string $messageIndex
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @return WebMailMessage
		 */
		function &LoadMessage($messageIndex, $indexAsUid, &$folder)
		{
			$messageIndexArray = array($messageIndex);
			if ($this->_settings->StoreMailsInDb)
			{
				$message = null;
				//read messages from db
				if (!$this->_dbConnection->Execute($this->_commandCreator->LoadMessagesFromDB($messageIndexArray, $indexAsUid, $folder, $this->Account)))
				{
					return $message;
				}
			
				if ($row = $this->_dbConnection->GetNextRecord())
				{
					$message = &new WebMailMessage();
					
					$message->LoadMessageFromRawBody($row->msg, true);
					$message->IdMsg = $row->id_msg;
					$message->Uid = $row->uid;
					$message->DbPriority = $row->priority;
					$message->Flags = $row->flags;
				}
				else 
				{
					setGlobalError(PROC_MSG_HAS_DELETED);
				}
			}
			else
			{
				$fs = &new FileSystem(INI_DIR.'/mail', $this->Account->Email, $this->Account->Id);
				
				//read messages from file system
				if (!$this->_dbConnection->Execute($this->_commandCreator->LoadMessagesFromFileSystem($messageIndexArray, $indexAsUid, $folder, $this->Account)))
				{
					return null;
				}

				if ($row = $this->_dbConnection->GetNextRecord())
				{
					$message = &$fs->LoadMessage($row->id_msg, $folder);
					if ($message != null)
					{
						$message->IdMsg = $row->id_msg;
						$message->Uid = $row->uid;
						$message->DbPriority = $row->priority;
						$message->Flags = $row->flags;
					}
					else 
					{
						setGlobalError(PROC_MSG_HAS_DELETED);
					}
				}
			}
			$this->_dbConnection->FreeResult();

			return $message;	
		}

		/**
		 * @param Folder $folder
		 * @return Array
		 */
		function &SelectIdMsgAndUidByIdMsgDesc(&$folder)
		{
			$outData = array();
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectIdMsgAndUid($folder, $this->Account)))
			{
				return $outData;
			}

			while ($row = $this->_dbConnection->GetNextRecord())
			{
				$outData[] = array($row->id_msg, $row->uid, $row->flag);
			}
		
			return $outData;
		}

		
		/**
		 * @return int
		 */
		function SelectLastIdMsg()
		{
			$idMsg = null;
			if ($this->_dbConnection->Execute($this->_commandCreator->SelectLastIdMsg($this->Account->Id)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$idMsg = $row->nid_msg;
				}
			}
			
			return ($idMsg == null) ? 0 : $idMsg;
		}
		
		/**
		 * @param int $messageId
		 * @param Folder $folder
		 * @return bool
		 */
		function GetMessageDownloadedFlag($messageId, &$folder)
		{
			$downloaded = false;
			if ($this->_dbConnection->Execute($this->_commandCreator->GetMessageDownloadedFlag($messageId, $folder, $this->Account->Id)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$downloaded = (bool) abs($row->downloaded);
				}
			}
			return $downloaded;
		}
		
		/**
		 * @param int $msgId
		 * @param int $charset
		 * @param WebMailMessage $message
		 * @return bool
		 */
		function UpdateMessageCharset($msgId, $charset, &$message)
		{
			$this->_dbConnection->Execute(
				$this->_commandCreator->UpdateMessageCharset($this->Account, $msgId, $charset, $message));
		}
		
		/**
		 * @param int $userId
		 * @return array
		 */
		function GetAccountListByUserId($userId)
		{
			$out = array();
			
			if ($this->_dbConnection->Execute($this->_commandCreator->GetAccountListByUserId($userId)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$out[] = (int) $row->id_acct;
				}
			}
			
			return $out;
		}
				
		/**
		 * @param int $userId
		 * @return array
		 */
		function GetFullAccountListByUserId($userId)
		{
			$out = array();
			
			if ($this->_dbConnection->Execute($this->_commandCreator->GetFullAccountListByUserId($userId)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$temp = array();
					$temp[] = (int) $row->id_acct;
					$temp[] = (bool) abs($row->def_acct);
					$out[] = $temp;
				}
			}
			
			return $out;
		}
		
		/**
		 * @param AddressBookRecord $addressBookRecordRecord
		 * @return bool
		 */
		function InsertAddressBookRecord(&$addressBookRecordRecord)
		{
			if (!$this->_dbConnection->Execute($this->_commandCreator->InsertAddressBookRecord($addressBookRecordRecord)))
			{
				return false;
			}
			
			$addressBookRecordRecord->IdAddress = $this->_dbConnection->GetLastInsertId();
			
			return true;
		}
		
		/**
		 * @param AddressBookRecord $addressBookRecordRecord
		 * @return bool
		 */
		function UpdateAddressBookRecord(&$addressBookRecordRecord)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->UpdateAddressBookRecord($addressBookRecordRecord));
		}
		
		/**
		 * @param long $idAddress
		 * @return bool
		 */
		function DeleteAddressBookRecord($idAddress)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->DeleteAddressBookRecord($idAddress)) &&
					$this->_dbConnection->Execute($this->_commandCreator->DeleteAddressGroupsContactsByIdAddress($idAddress));
		}
		
		/**
		 * @param int $idAddress
		 * @return bool
		 */
		function DeleteAddressGroup($idGroup)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->DeleteAddressGroup($idGroup)) &&
					$this->_dbConnection->Execute($this->_commandCreator->DeleteAddressGroupsContactsByIdGroup($idGroup));
		}
		
		/**
		 * @param AddressGroup $group
		 * @return bool
		 */
		function InsertAddressGroup(&$group)
		{
			if (!$this->_dbConnection->Execute($this->_commandCreator->InsertAddressGroup($group)))
			{
				return false;
			}
			
			$group->Id = $this->_dbConnection->GetLastInsertId();
			
			return true;
		}

		/**
		 * @param AddressGroup $group
		 * @return bool
		 */
		function UpdateAddressGroup(&$group)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->UpdateAddressGroup($group));
		}
		
		/**
		 * @param long $idAddress
		 * @return AddressBookRecord
		 */
		function &SelectAddressBookRecord($idAddress)
		{
			
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectAddressBookRecord($idAddress, $this->Account->IdUser)))
			{
				return null;
			}

			$addressBookRecord = null;
			if ($row = $this->_dbConnection->GetNextRecord())
			{
				$addressBookRecord = &new AddressBookRecord();
				
				$addressBookRecord->IdAddress = $row->id_addr;
				$addressBookRecord->IdUser = $row->id_user;
				$addressBookRecord->HomeEmail = $row->h_email;
				$addressBookRecord->FullName = $row->fullname;
				$addressBookRecord->Notes = $row->notes;
				$addressBookRecord->UseFriendlyName = (bool) abs($row->use_friendly_nm);
				$addressBookRecord->HomeStreet = $row->h_street;
				$addressBookRecord->HomeCity = $row->h_city;
				$addressBookRecord->HomeState = $row->h_state;
				$addressBookRecord->HomeZip = $row->h_zip;
				$addressBookRecord->HomeCountry = $row->h_country;
				$addressBookRecord->HomePhone = $row->h_phone;
				$addressBookRecord->HomeFax = $row->h_fax;
				$addressBookRecord->HomeMobile = $row->h_mobile;
				$addressBookRecord->HomeWeb = $row->h_web;
				$addressBookRecord->BusinessEmail = $row->b_email;
				$addressBookRecord->BusinessCompany = $row->b_company;
				$addressBookRecord->BusinessStreet = $row->b_street;
				$addressBookRecord->BusinessCity = $row->b_city;
				$addressBookRecord->BusinessState = $row->b_state;
				$addressBookRecord->BusinessZip = $row->b_zip;
				$addressBookRecord->BusinessCountry = $row->b_country;
				$addressBookRecord->BusinessJobTitle = $row->b_job_title;
				$addressBookRecord->BusinessDepartment = $row->b_department;
				$addressBookRecord->BusinessOffice = $row->b_office;
				$addressBookRecord->BusinessPhone = $row->b_phone;
				$addressBookRecord->BusinessFax = $row->b_fax;
				$addressBookRecord->BusinessWeb = $row->b_web;
				$addressBookRecord->OtherEmail = $row->other_email;
				$addressBookRecord->PrimaryEmail = (int) $row->primary_email;
				$addressBookRecord->IdPreviousAddress = (int) $row->id_addr_prev;
				$addressBookRecord->Temp = (bool) abs($row->tmp);
				$addressBookRecord->BirthdayDay = $row->birthday_day;
				$addressBookRecord->BirthdayMonth = $row->birthday_month;
				$addressBookRecord->BirthdayYear = $row->birthday_year;
			}
			
			return $addressBookRecord;
		}

		/**
		 * @param int $idAddress
		 * @return Array
		 */
		function &SelectAddressGroupContact($idAddress)
		{
			$outData = array();
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectAddressGroupContact($idAddress)))
			{
				return $outData;
			}

			$outData = array();
			while ($row = $this->_dbConnection->GetNextRecord())
			{
				$outData[$row->group_id] = $row->group_nm;
			}
		
			return $outData;
		}
		
		/**
		 * @param int $idGroup
		 * @return ContactCollection
		 */
		function &SelectAddressGroupContacts($idGroup)
		{
			$contacts = null;
			if ($this->_dbConnection->Execute($this->_commandCreator->SelectAddressGroupContacts($idGroup)))
			{
				$contacts = &new ContactCollection();
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$contact = &new Contact();
					$contact->Id = $row->id;
					$contact->Name = $row->fullname;
					$contact->Email = $row->email;
					$contact->UseFriendlyName = (bool) $row->usefriendlyname;
					
					$contacts->Add($contact);
				}
			}

			return $contacts;
		}
		
		
		/**
		 * @param int $idAddress
		 * @param int $idGroup
		 * @return bool
		 */
		function InsertAddressGroupContact($idAddress, $idGroup)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->InsertAddressGroupContact($idAddress, $idGroup));
		}
		
		/**
		 * @param int $idAddress
		 * @return bool
		 */
		function DeleteAddressGroupsContactsByIdAddress($idAddress)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->DeleteAddressGroupsContactsByIdAddress($idAddress));
		}
		
		/**
		 * @param int $idGroup
		 * @return bool
		 */
		function DeleteAddressGroupsContactsByIdGroup($idGroup)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->DeleteAddressGroupsContactsByIdGroup($idGroup));
		}

		/**
		 * @param long $idAddress
		 * @param int $idGroup
		 * @return bool
		 */
		function DeleteAddressGroupsContacts($idAddress, $idGroup)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->DeleteAddressGroupsContacts($idAddress, $idGroup));
		}
		
		/**
		 * @param int $idGroup
		 * @return Array
		 */
		function &SelectAddressContactsAndGroupsCount($lookForType, $idUser, $condition = null, $idGroup = null)
		{
			$outArray = array(0, 0);
			if ($this->_dbConnection->Execute($this->_commandCreator->SelectAddressContactsCount($lookForType, $idUser, $condition, $idGroup)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$outArray[0] = $row->contacts_count;
				}
			}

			if ($this->_dbConnection->Execute($this->_commandCreator->SelectAddressGroupsCount($lookForType, $idUser, $condition)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$outArray[1] = $row->groups_count;
				}
			}
			return $outArray;
		}

		
		/**
		 * @param int $idGroup
		 * @return string
		 */
		function SelectAddressGroupName($idGroup)
		{
			$groupName = '';
			if ($this->_dbConnection->Execute($this->_commandCreator->SelectAddressGroupName($idGroup)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$groupName = $row->group_nm;
				}
			}

			return $groupName;
		}
		
		/**
		 * @param String $strGroupName
		 * @param int $idAcct
		 * @return bool
		 */
		function CheckExistsAddresGroupByName($strGroupName, $idUser)
		{
			if($this->_dbConnection->Execute($this->_commandCreator->checkExistsGroupByName($strGroupName, $idUser)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$count = $row->mcount;
					if($count > 0)
					{
						return true;	
					}
					
					return false;
				}
				
				return false;
			}
			
			return false;
		}
		
		/**
		 * @return Array
		 */
		function &SelectUserAddressGroupNames()
		{
			$groupsArray = array();
			if ($this->_dbConnection->Execute($this->_commandCreator->SelectUserAddressGroupNames($this->Account->IdUser)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$groupsArray[$row->id_group] = $row->group_nm;
				}
			}
			
			return $groupsArray;
		}
		
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @param int $flags
		 * @param short $action
		 * @return bool
		 */
		function SetMessagesFlags(&$messageIndexSet, $indexAsUid, &$folder, $flags, $action)
		{
		
			return $this->_dbConnection->Execute(
					$this->_commandCreator->SetMessagesFlags($messageIndexSet, $indexAsUid, $folder,
																$flags, $action, $this->Account));
		}
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $fromFolder
		 * @param Folder $toFolder
		 * @return bool
		 */
		function MoveMessages(&$messageIndexSet, $indexAsUid, &$fromFolder, &$toFolder)
		{
			$result = true;
			if (!$this->_settings->StoreMailsInDb &&
					$this->_dbConnection->Execute(
						$this->_commandCreator->SelectDownloadedMessagesIdSet($messageIndexSet, $indexAsUid,
																				$this->Account)))
			{
				$downloadedMsgIdSet = array();

				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$downloadedMsgIdSet[] = $row->id_msg;
				}

				if (count($downloadedMsgIdSet) > 0)													
				{
					$fs = &new FileSystem(INI_DIR.'/mail', $this->Account->Email, $this->Account->Id);
					/*
					$result = $fs->MoveMessages($downloadedMsgIdSet, $fromFolder, $toFolder);
					
					if(!$result)
					{	
						$this->_log->WriteLine("ERROR: Can't move message on file system");	
						return false;
					}
					*/
					
					if(!$fs->MoveMessages($downloadedMsgIdSet, $fromFolder, $toFolder))
					{	
						$this->_log->WriteLine("ERROR: Can't move message on file system");	
						// return false;
					}
					
				}	
			}
			
			if($result)
			{
				$result = $this->_dbConnection->Execute(
							$this->_commandCreator->MoveMessages($messageIndexSet, $indexAsUid, $fromFolder,
																	$toFolder, $this->Account));															
			}
			else 
			{
				$this->_log->WriteLine("ERROR: Can't save message to DB");	
			}
		
			return $result;
		}
		
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder optional
		 * @return bool
		 */
		function DeleteMessages(&$messageIndexSet, $indexAsUid, &$folder)
		{
			if ($this->_settings->StoreMailsInDb)
			{
				$result = true;
				//remove messages from db
				$this->_dbConnection->Execute(
								$this->_commandCreator->DeleteMessagesBody($messageIndexSet, $indexAsUid, $folder, $this->Account));
				$result &= $this->_dbConnection->Execute(
								$this->_commandCreator->DeleteMessagesHeaders($messageIndexSet, $indexAsUid, $folder, $this->Account));
				return $result;
			}
			
			$fs = &new FileSystem(INI_DIR.'/mail', $this->Account->Email, $this->Account->Id);
			$fs->DeleteMessages($messageIndexSet, $folder);
			
			return $this->_dbConnection->Execute($this->_commandCreator->DeleteMessagesHeaders($messageIndexSet, $indexAsUid, $folder, $this->Account));
		}
		
		/**
		 * @param Folder $folder
		 * @return bool
		 */
		function PurgeFolder(&$folder, $pop3EmptyTrash = false)
		{
			$result = true;

			if ($this->_settings->StoreMailsInDb)
			{
				//remove messages from db
				
				//read messages from file system
				if (!$this->_dbConnection->Execute($this->_commandCreator->SelectDeletedMessagesId($folder, $this->Account, $pop3EmptyTrash)))
				{
					return false;
				}
	
				$msgIdSet = array();
				while ($row =  $this->_dbConnection->GetNextRecord())
				{
					$msgIdSet[] = $row->id_msg;
				}
				
				if(count($msgIdSet) > 0)
				{
					$result &= $this->_dbConnection->Execute(
										$this->_commandCreator->PurgeAllMessagesBody($msgIdSet, $this->Account->Id));
					$result &= $this->_dbConnection->Execute(
										$this->_commandCreator->PurgeAllMessageHeaders($folder, $this->Account, $pop3EmptyTrash));
										
					return $result;
				}
				else 
				{
					return true;
				}
			}
				
			//read messages from file system
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectAllDeletedMsgId($folder, $this->Account, $pop3EmptyTrash)))
			{
				return false;
			}

			$messageIdSet = array();
			while ($row =  $this->_dbConnection->GetNextRecord())
			{
				$messageIdSet[] = $row->id_msg;
			}

			if (count($messageIdSet) > 0)
			{
				$fs = &new FileSystem(INI_DIR.'/mail', $this->Account->Email, $this->Account->Id);
				$result &= $fs->DeleteMessages($messageIdSet, $folder);
			}
			
			return $result && $this->_dbConnection->Execute(
						$this->_commandCreator->PurgeAllMessageHeaders($folder, $this->Account, $pop3EmptyTrash));
		}
		
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @return string
		 */
		function &SelectDownloadedMessagesIdSet($messageIndexSet, $indexAsUid)
		{
			$messagesIdSet = array();
			if ($this->_dbConnection->Execute(
					$this->_commandCreator->SelectDownloadedMessagesIdSet($messageIndexSet, $indexAsUid, $this->Account)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$messagesIdSet[] = $row->id_msg;
				}
			}
			
			return $messagesIdSet;
		}
		
		/**
		 * @param Folder $folder
		 * @return Array
		 */
		function &SelectAllMessagesUidSetByFolder(&$folder)
		{
			$messagesUidSet = array();
			if ($this->_dbConnection->Execute(
					$this->_commandCreator->SelectAllMessagesUidSetByFolder($folder, $this->Account)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$messagesUidSet[] = $row->uid;
				}
			}
			
			return $messagesUidSet;
		}
		
		/**
		 * @param int $accountId
		 * @return FilterCollection
		 */
		function &SelectFilters($accountId)
		{
			$filters = null;
			if ($this->_dbConnection->Execute($this->_commandCreator->SelectFilters($accountId)))
			{
				$filters = &new FilterCollection();
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$filter = &new Filter();
					$filter->Id = $row->id_filter;
					$filter->IdAcct = $accountId;
					$filter->Field = $row->field;
					$filter->Condition = $row->condition;
					$filter->Filter = $row->filter;
					$filter->Action = $row->action;
					$filter->IdFolder = $row->id_folder;
					
					$filters->Add($filter);
				}
			}

			return $filters;
		}
		
		/**
		 * @param Filter $filter
		 * @return bool
		 */
		function InsertFilter(&$filter)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->InsertFilter($filter));
		}
		
		/**
		 * @param Filter $filter
		 * @return bool
		 */
		function UpdateFilter(&$filter)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->UpdateFilter($filter));
		}

		/**
		 * @param int $filterId
		 * @param int $accountId
		 * @return bool
		 */
		function DeleteFilter($filterId, $accountId)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->DeleteFilter($filterId, $accountId));
		}
		
		/**
		 * @param int $folderId
		 * @param int $accountId
		 * @return bool
		 */
		function DeleteFolderFilters($folderId, $accountId)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->DeleteFolderFilters($folderId, $accountId));
		}
		
		/**
		 * @param int $filterId
		 * @param int $accountId
		 * @return Filter
		 */
		/*function &GetFilter($filterId, $accountId)
		{
			$filter = null;
			if ($this->_dbConnection->Execute(
					$this->_commandCreator->GetFilter($filterId, $accountId)))
			{
				$filter = &new Filter();
				
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$filter->Id = $filterId;
					$filter->IdAcct = $accountId;
					$filter->Field = $row->field;
					$filter->Condition = $row->condition;
					$filter->Filter = $row->filter;
					$filter->Action = $row->action;
					$filter->IdFolder = $row->id_folder;
				}
			}
			
			return $filter;
		}*/
		
		/**
		 * @param string $condition
		 * @param FolderCollection $folders
		 * @param bool $inHeadersOnly
		 * @return int
		 */
		function SearchMessagesCount($condition, &$folders, $inHeadersOnly)
		{
			$mailCollectionCount = 0;
			
			if ($this->_dbConnection->Execute(
					$this->_commandCreator->SearchMessagesCount(
						$condition, $folders->CreateFolderListFromTree(), $inHeadersOnly, $this->Account)))
			{
				
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$mailCollectionCount = $row->msg_count;
				}
			}
			
			return $mailCollectionCount;
		}
		
		/**
		 * @param int $pageNumber
		 * @param string $condition
		 * @param FolderCollection $folders
		 * @param bool $inHeadersOnly
		 * @return WebMailMessageCollection
		 */
		function &SearchMessages($pageNumber, $condition, &$folders, $inHeadersOnly, $countMessages = 0)
		{
			$mailCollection = null;
			if ($this->_dbConnection->Execute(
					$this->_commandCreator->SearchMessages(
						$pageNumber, $condition, $folders->CreateFolderListFromTree(), $inHeadersOnly, $this->Account)))
			{
				$mailCollection = &new WebMailMessageCollection();
				
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$msg = &new WebMailMessage();
					$msg->SetFromAsString($row->from_msg);
					$msg->SetToAsString($row->to_msg);
					$msg->SetCcAsString($row->cc_msg);
					$msg->SetBccAsString($row->bcc_msg);
					
					$date = &new CDateTime();
					$date->SetFromANSI($row->nmsg_date);
					$msg->SetDate($date);
					
					$msg->SetSubject($row->subject);
					
					$msg->IdMsg = $row->id_msg;
					$msg->IdFolder = $row->id_folder_db;
					$msg->Uid = $row->uid;
					$msg->Size = $row->size;
					$msg->DbPriority = $row->priority;
					$msg->DbXSpam = (bool) abs($row->x_spam);
					
					$msg->DbHasAttachments = $row->attachments;
					
					$msg->Flags = 0;
					
					if ($row->seen)
					{
						$msg->Flags |= MESSAGEFLAGS_Seen;
					}
					if ($row->flagged)
					{
						$msg->Flags |= MESSAGEFLAGS_Flagged;
					}
					if ($row->deleted)
					{
						$msg->Flags |= MESSAGEFLAGS_Deleted;
					}
					if ($row->replied)
					{
						$msg->Flags |= MESSAGEFLAGS_Answered;
					}
					if ($row->forwarded)
					{
						$msg->Flags |= MESSAGEFLAGS_Forwarded;
					}
					if ($row->grayed)
					{
						$msg->Flags |= MESSAGEFLAGS_Grayed;
					}
	
					$mailCollection->Add($msg);
				}
			}

			return $mailCollection;	
		}
		
		/**
		 * @return Array
		 */
		function &SelectReadsRecords()
		{
			$readsRecords = array();
			if ($this->_dbConnection->Execute($this->_commandCreator->SelectReadsRecords($this->Account->Id)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$readsRecords[$row->uid] = '';
				}
			}

			return $readsRecords;
		}

		/**
		 * @return bool
		 */
		function DeleteReadsRecords()
		{
			return $this->_dbConnection->Execute($this->_commandCreator->DeleteReadsRecords($this->Account->Id));
		}
		
		/**
		 * @param array $uids
		 * @return bool
		 */
		function DeleteReadsRecordsByUids($uids)
		{
			return $this->_dbConnection->Execute($this->_commandCreator->DeleteReadsRecordsByUid($this->Account->Id, $uids));
		}
		
		/**
		 * @param bool $sortOrder
		 * @return bool
		 */
		function InsertReadsRecords($uidArray)
		{
			$result = true;
			
			foreach ($uidArray as $uid)
			{
				$result &= $this->_dbConnection->Execute($this->_commandCreator->InsertReadsRecord($this->Account->Id, $uid));
			}
			return $result;
		}
		
		/**
		 * @param int $pageNumber
		 * @param string $sortField
		 * @param bool $sortOrder
		 * @return ContactCollection
		 */
		function &LoadContactsAndGroups($pageNumber, $sortField, $sortOrder)
		{
			$contacts = null;
			if ($this->_dbConnection->Execute(
					$this->_commandCreator->LoadContactsAndGroups($pageNumber, $sortField, $sortOrder, $this->Account)))
			{
				$contacts = &new ContactCollection();
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$contact = &new Contact();
					$contact->Id = $row->id;
					$contact->IsGroup = $row->is_group;
					$contact->Name = $row->name;
					$contact->Email = $row->email;
					$contact->UseFriendlyName = (bool) $row->usefriendlyname;
					
					$contacts->Add($contact);
				}
			}

			return $contacts;
		}

		/**
		 * @param Array $contactIds
		 * @return ContactCollection
		 */
		function &LoadContactsById(&$contactIds)
		{
			$contacts = null;
			if ($this->_dbConnection->Execute(
					$this->_commandCreator->LoadContactsById($contactIds, $this->Account)))
			{
				$contacts = &new ContactCollection();
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$contact = &new Contact();
					$contact->Id = $row->id;
					$contact->IsGroup = $row->is_group;
					$contact->Name = $row->name;
					$contact->Email = $row->email;
					$contact->Frequency = $row->frequency;
					$contact->UseFriendlyName = (bool) $row->usefriendlyname;
					
					$contacts->Add($contact);
				}
			}

			return $contacts;
		}
		
		
		/**
		 * @param int $pageNumber
		 * @param string $condition
		 * @param int $groupId
		 * @param string $sortField
		 * @param bool $sortOrder
		 * @return ContactCollection
		 */
		function &SearchContactsAndGroups($pageNumber, $condition, $groupId, $sortField, $sortOrder, $lookForType)
		{
			$contacts = null;
			if ($this->_dbConnection->Execute(
					$this->_commandCreator->SearchContactsAndGroups($pageNumber, $condition,
											$groupId, $sortField, $sortOrder, $this->Account, $lookForType)))
			{
				$contacts = &new ContactCollection();
				
				$k = 0;
				
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					if($lookForType == 1 && $k > SUGGESTCONTACTS)
					{
						$this->_dbConnection->FreeResult();
						break;	
					}
									
					$contact = &new Contact();
					$contact->Id = $row->id;
					$contact->IsGroup = $row->is_group;
					$contact->Name = $row->name;
					$contact->Email = $row->email;
					$contact->Frequency = $row->frequency;
					$contact->UseFriendlyName = (bool) $row->usefriendlyname;
					
					$contacts->Add($contact);
					$k++;
				}
			}
			
			return $contacts;
		}
		
		/**
		 * @return bool
		 */
		function UpdateMailboxSize()
		{
			$mailBoxSize = 0;
			if ($this->_dbConnection->Execute($this->_commandCreator->CountMailboxSize($this->Account->Id)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$mailBoxSize = $row->mailbox_size;
				}
			}

			return $this->_dbConnection->Execute(
						$this->_commandCreator->UpdateMailboxSize($mailBoxSize, $this->Account->Id));
		}
		
		/**
		 * @return int
		 */
		function SelectMailboxesSize()
		{
			$mailBoxesSize = 0;
			if ($this->_dbConnection->Execute($this->_commandCreator->SelectMailboxesSize($this->Account->IdUser)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$mailBoxesSize = (int) $row->mailboxes_size;
				}
			}
			
			return $mailBoxesSize;
		}
		
		/**
		 * @return Array
		 */
		function &SelectExpiredMessageUids()
		{
			$expiredUids = array();

			if ($this->_dbConnection->Execute($this->_commandCreator->SelectExpiredMessageUids($this->Account)))
			{
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$expiredUids[] = $row->str_uid;
				}
			}
			
			return $expiredUids;
		}
		
		/**
		 * @param Folder $folder
		 * @return bool
		 */
		function CreateFolder(&$folder)
		{
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectForCreateFolder($folder)))
			{
				return false;
			}
			else 
			{
				$row = $this->_dbConnection->GetNextRecord();
				$folder->FolderOrder = ($row && isset($row->norder)) ? (int) ($row->norder + 1) : 0;
			}
			
			if (!$this->_dbConnection->Execute($this->_commandCreator->CreateFolder($folder)))
			{
				return false;
			}
					
			$folder->IdDb = $this->_dbConnection->GetLastInsertId();

			if (!$this->_dbConnection->Execute($this->_commandCreator->CreateFolderTree($folder)))
			{
				return false;
			}
			
			if (!$this->_dbConnection->Execute($this->_commandCreator->SelectForCreateFolderTree($folder)))
			{
				return false;
			}
			else 
			{
				$result = array(); 
				while ($row = $this->_dbConnection->GetNextRecord())
				{
					$IdParent = ($row && isset($row->id_parent)) ? (int) $row->id_parent : -1;
					$Level = ($row && isset($row->folder_level)) ? (int) ($row->folder_level + 1) : 0;
					
					$result[] = array($IdParent, $Level);
				}
				
				if ($result)
				{
					foreach ($result as $folderData)
					{
						if (!is_array($folderData)) continue;
						$folder->IdParent = $folderData[0];
						$folder->Level = $folderData[1];
						if(!$this->_dbConnection->Execute($this->_commandCreator->CreateSelectFolderTree($folder)))
						{
							return false;
						}	
					}
					
				}
			}

			return true;
		}
		
		/**
		 * @param Account $account
		 * @param Array $arrayEmails
		 * @return bool
		 */
		function UpdateSuggestTable(&$account, $arrayEmailsWithFName)
		{
			$arrayEmails = array_keys($arrayEmailsWithFName);
						
			$DBEmails = $this->SelectExistEmails($account, $arrayEmails);
			
			if($DBEmails === false)
			{
				return false;
			}
		
			$arrayEmails = array_unique($arrayEmails);
			$DBEmails = array_unique($DBEmails);
			
			$NewEmails = array_diff($arrayEmails, $DBEmails);
			
			$UpdateEmails = $arrayEmails;
			
			if(count($UpdateEmails) > 0)
			{
				if(!$this->_dbConnection->Execute($this->_commandCreator->UpdateContactFrequencyByEmail($account, $UpdateEmails)))
				{
					return false;	
				}
			}
			
			if(count($NewEmails) > 0)	
			{
				foreach ($NewEmails as $key) 
				{
					if(strlen($key) > 0)
					{
						//$arrayEmailsWithFName[$key]
						if(!$this->_dbConnection->Execute($this->_commandCreator->InsertAutoCreateContact($account, $key, $arrayEmailsWithFName[$key])))
						{
							return false;	
						}
					}
				}
			}
			
			return true;
		}
	}
	
	class MySqlStorage extends DbStorage
	{
		/**
		 * @param Account $account
		 * @return MySqlStorage
		 */
		function MySqlStorage(&$account)
		{
			DbStorage::DbStorage($account);
			$this->_escapeType = QUOTE_ESCAPE;
			$this->_commandCreator = &new MySqlCommandCreator();
			
			if ($this->_settings->UseCustomConnectionString)
			{
				require_once(WM_ROOTPATH.'db/class_dbodbc.php');
				$this->_dbConnection = &new DbOdbc($this->_settings->DbCustomConnectionString, DB_MYSQL);
			}
			else
			{
				require_once(WM_ROOTPATH.'db/class_dbmysql.php');
				$this->_dbConnection = &new DbMySql($this->_settings->DbHost, $this->_settings->DbLogin,
													$this->_settings->DbPassword, $this->_settings->DbName);
			}
		}
		
		/**
		 * @param string $pref
		 * @param string $tableName
		 * @return array/bool
		 */
		function GetIndexsOfTable($pref, $tableName)
		{
			$returnArray = array();
			if (!$this->_dbConnection->Execute($this->_commandCreator->GetIndexsOfTable($pref, $tableName)))
			{
				return false;
			}

			while ($array = $this->_dbConnection->GetNextArrayRecord())
			{
				if ($array && count($array) > 4)
				{
					$returnArray[] = trim($array[4]);
				}
			}
			
			return $returnArray;
		}
	}
		

	class MsSqlStorage extends DbStorage
	{
		/**
		 * @param Account $account
		 * @return MsSqlStorage
		 */
		function MsSqlStorage(&$account)
		{
			DbStorage::DbStorage($account);
			$this->_escapeType = QUOTE_DOUBLE;
			$this->_commandCreator = &new MsSqlCommandCreator();

			if ($this->_settings->UseCustomConnectionString)
			{
				require_once(WM_ROOTPATH.'db/class_dbodbc.php');
				$this->_dbConnection = &new DbOdbc($this->_settings->DbCustomConnectionString, DB_MSSQLSERVER);
			}
			else
			{
				require_once(WM_ROOTPATH.'db/class_dbmssql.php');
				$this->_dbConnection = &new DbMsSql($this->_settings->DbHost, $this->_settings->DbLogin,
													$this->_settings->DbPassword, $this->_settings->DbName);
			}
		}
		
		/**
		 * @param string $pref
		 * @param string $tableName
		 * @return array/bool
		 */
		function GetIndexsOfTable($pref, $tableName)
		{
			$returnArray = array();
			if (!$this->_dbConnection->Execute($this->_commandCreator->GetIndexsOfTable($pref, $tableName)))
			{
				return false;
			}

			while ($array = $this->_dbConnection->GetNextArrayRecord())
			{
				if ($array && count($array) > 2)
				{
					$temp = explode(',', trim($array[2]));
					if (is_array($temp))
					{
						if (count($temp) > 1)
						{
							foreach ($temp as $value)
							{
								$returnArray[] = trim($value);;
							}
						}
						else if (count($temp) == 1)
						{
							$returnArray[] = trim($temp[0]);
						}
					}
				}
			}
			
			return $returnArray;
		}
	}