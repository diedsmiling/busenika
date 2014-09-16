<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));
	
	require_once(WM_ROOTPATH.'class_settings.php');

	define('QUOTE_ESCAPE', 1);
	define('QUOTE_DOUBLE', 2);
	define('DEFAULTORDER_DateDesc', 0);
	define('DEFAULTORDER_Date', 1);
	define('DEFAULTORDER_FromDesc', 2);
	define('DEFAULTORDER_From', 3);
	define('DEFAULTORDER_ToDesc', 4);
	define('DEFAULTORDER_To', 5);
	define('DEFAULTORDER_SizeDesc', 6);
	define('DEFAULTORDER_Size', 7);
	define('DEFAULTORDER_SubjDesc', 8);
	define('DEFAULTORDER_Subj', 9);
	define('DEFAULTORDER_AttachDesc', 10);
	define('DEFAULTORDER_Attach', 11);
	define('DEFAULTORDER_FlagDesc', 12);
	define('DEFAULTORDER_Flag', 13);
	
	class CommandCreator
	{
		/**
		 * @access private
		 * @var Settings
		 */
		var $_settings;
		
		/**
		 * @access private
		 * @var short
		 */
		var $_escapeType;
		
		/**
		 * Class Constructor
		 *
		 * @return CommandCreator
		 */
		function CommandCreator($escapeType)
		{
			$this->_settings =& Settings::CreateInstance();
			$this->_escapeType = $escapeType;
		}
		
		/**
		 * @access protected
		 * @param string $str
		 * @return string
		 */
		function _escapeString($str)
		{
			if(!$str) return "''";
			switch ($this->_escapeType)
			{
				case QUOTE_ESCAPE:
					return "'".addslashes($str)."'";
				case QUOTE_DOUBLE:
					return "'".str_replace("'", "''", $str)."'";
				default:
					return "'".$str."'";
			}
		}
		
		/**
		 * @param short $value
		 * @return short
		 */
		function Bool2Bool($value)
		{
			return $value;
			//return (get_class($this) == 'MsAccessCommandCreator')?-$value:$value;
		}

		/**
		 * @access protected
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param short $mailProtocol
		 * @return string
		 */
		function _quoteUids(&$messageIndexSet, $indexAsUid, $mailProtocol)
		{
			//prepare struids
			if ($indexAsUid && ($mailProtocol == MAILPROTOCOL_POP3 || $mailProtocol == MAILPROTOCOL_WMSERVER))
			{
				return "'".implode("','", $messageIndexSet)."'";
			}
			return implode(',', $messageIndexSet);
		}
		
		/**
		 * @access protected
		 * @param bool $indexAsUid
		 * @param short $mailProtocol
		 * @return string
		 */
		function _getMsgIdUidFieldName($indexAsUid, $mailProtocol)
		{
			if (!$indexAsUid)
			{
				return 'id_msg';
			}
				
			switch ($mailProtocol)
			{
				default:
				case MAILPROTOCOL_POP3:
					return 'str_uid';
					
				case MAILPROTOCOL_IMAP4:
					return 'int_uid';
				
				case MAILPROTOCOL_WMSERVER:
					return 'str_uid';

			}
			
			return '';
		}
		
		/**
		 * @access protected
		 * @param int $order
		 * @param string $filter
		 * @param bool $asc
		 */
		function _setSortOrder($order, &$filter, &$asc)
		{
			switch ($order)
			{
				case DEFAULTORDER_Date:
					$filter = 'msg_date';
					$asc = true;
					break;
				case DEFAULTORDER_DateDesc:
					$filter = 'msg_date';
					$asc = false;
					break;
				case DEFAULTORDER_From:
					$filter = 'from_msg';
					$asc = true;
					break;
				case DEFAULTORDER_FromDesc:
					$filter = 'from_msg';
					$asc = false;
					break;
				case DEFAULTORDER_To:
					$filter = 'to_msg';
					$asc = true;
					break;
				case DEFAULTORDER_ToDesc:
					$filter = 'to_msg';
					$asc = false;
					break;
				case DEFAULTORDER_Size:
					$filter = 'size';
					$asc = true;
					break;
				case DEFAULTORDER_SizeDesc:
					$filter = 'size';
					$asc = false;
					break;
				case DEFAULTORDER_Subj:
					$filter = 'subject';
					$asc = true;
					break;
				case DEFAULTORDER_SubjDesc:
					$filter = 'subject';
					$asc = false;
					break;
				case DEFAULTORDER_Attach:
					$filter = 'attachments';
					$asc = true;
					break;
				case DEFAULTORDER_AttachDesc:
					$filter = 'attachments';
					$asc = false;
					break;
				case DEFAULTORDER_Flag:
					$filter = 'flagged';
					$asc = true;
					break;
				case DEFAULTORDER_FlagDesc:
					$filter = 'flagged';
					$asc = false;
					break;
			}
		}
		
		/**
		 * @param string $email
		 * @return string
		 */
		function SelectSendersByEmail($email, $idUser)
		{
			$sql = 'SELECT safety FROM %sawm_senders WHERE id_user = %d AND email = %s';
			
			return sprintf($sql, $this->_settings->DbPrefix, $idUser, $this->_escapeString($email));
		}
		
		/**
		 * @param String $strGroupName
		 * @return string
		 */
		function checkExistsGroupByName($strGroupName, $idUser)
		{
			$sql = 'SELECT COUNT(*) as mcount FROM %sawm_addr_groups WHERE id_user = %d AND group_nm LIKE %s';
			
			return sprintf($sql, $this->_settings->DbPrefix, $idUser, $this->_escapeString($strGroupName));
		}

		
		/**
		 * @param string $email
		 * @param int $safety
		 * @return string
		 */
		function UpdateSenders($email, $safety, $idUser)
		{
			$sql = 'UPDATE %sawm_senders 
						SET safety = %d
						WHERE id_user = %d AND email = %s';
			
			return sprintf($sql, $this->_settings->DbPrefix, $safety, $idUser, $this->_escapeString($email));
		}
		
		/**
		 * @param string $email
		 * @param int $safety
		 * @return string
		 */
		function InsertSenders($email, $safety, $idUser)
		{
			$sql = 'INSERT INTO %sawm_senders (id_user, email, safety) VALUES (%d, %s, %d)';
			
			return sprintf($sql, $this->_settings->DbPrefix, $idUser, $this->_escapeString($email), (int) $safety);			
		}

		/**
		 * @param Folder $folder
		 * @return string
		 */
		function GetFolderMessageCountAll(&$folder)
		{
			$sql = 'SELECT Count(id) AS message_count FROM %sawm_messages
					WHERE id_acct=%d AND id_folder_db=%d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $folder->IdAcct, $folder->IdDb);			
		}

		/**
		 * @param Folder $folder
		 * @return string
		 */
		function GetFolderMessageCountUnread(&$folder)
		{
			$sql = 'SELECT Count(id) AS unread_message_count FROM %sawm_messages
					WHERE id_acct=%d AND id_folder_db=%d AND seen = 0';
			
			return sprintf($sql, $this->_settings->DbPrefix, $folder->IdAcct, $folder->IdDb);			
		}
		
		/**
		 * @param Account $account
		 * @return string
		 */
		function InsertAccount(&$account)
		{
			$sql = 'INSERT INTO %sawm_accounts (id_user, def_acct, deleted, email, mail_protocol,
							mail_inc_host, mail_inc_login, mail_inc_pass, mail_inc_port, mail_out_host,
							mail_out_login, mail_out_pass, mail_out_port, mail_out_auth, friendly_nm,
							use_friendly_nm, def_order, getmail_at_login, mail_mode, mails_on_server_days,
							signature, signature_type, signature_opt, delimiter)
					VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s, %d, %s, %s,
							%s, %s,	%d, %s)';
			
			return sprintf($sql, $this->_settings->DbPrefix,
									(int) $account->IdUser,
									$this->Bool2Bool((int) $account->DefaultAccount),
									$this->Bool2Bool((int) $account->Deleted),
									$this->_escapeString($account->Email),
									$this->Bool2Bool((int) $account->MailProtocol),
									$this->_escapeString($account->MailIncHost),
									$this->_escapeString($account->MailIncLogin),
									$this->_escapeString(ConvertUtils::EncodePassword($account->MailIncPassword, $account)),
									(int) $account->MailIncPort,
									$this->_escapeString($account->MailOutHost),
									$this->_escapeString($account->MailOutLogin),
									$this->_escapeString(ConvertUtils::EncodePassword($account->MailOutPassword, $account)),
									(int) $account->MailOutPort,
									$this->Bool2Bool((int) $account->MailOutAuthentication),
									$this->_escapeString($account->FriendlyName),
									$this->Bool2Bool((int) $account->UseFriendlyName),
									$account->DefaultOrder,
									$this->Bool2Bool((int) $account->GetMailAtLogin),
									(int) $account->MailMode,
									(int) $account->MailsOnServerDays,
									$this->_escapeString($account->Signature),
									(int) $account->SignatureType,
									(int) $account->SignatureOptions,
									$this->_escapeString($account->Delimiter));
		}
		
		/**
		 * @param int $userId
		 * @return string
		 */
		/*function CreateSettings($userId)
		{
			$sql = 'INSERT INTO %sawm_settings (id_user) VALUES(%d)';
			return sprintf($sql, $this->_settings->DbPrefix, $userId);
		}*/
		
		/**
		 * @param Account $account
		 * @return string
		 */
		function InsertSettings(&$account)
		{
			$sql = 'INSERT INTO %sawm_settings (id_user, msgs_per_page, white_listing, x_spam, last_login,
							logins_count, def_skin, def_lang, def_charset_inc, def_charset_out,
							def_timezone, def_date_fmt, hide_folders, mailbox_limit, allow_change_settings,
							allow_dhtml_editor, allow_direct_mode, hide_contacts, db_charset,
							horiz_resizer, vert_resizer, mark, reply, contacts_per_page, view_mode)
					VALUES(%d, %d, %s, %s, %s, %s, %s, %s, %d, %d, %s, %s,
							%d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d)';
			
			$date = &new CDateTime(time());
			return sprintf($sql, $this->_settings->DbPrefix,
									$account->IdUser,
									(int) $account->MailsPerPage,
									$this->Bool2Bool((int) $account->WhiteListing),
									$this->Bool2Bool((int) $account->XSpam),
									$this->_escapeString($date->ToANSI()),
									(int) $account->LoginsCount,
									$this->_escapeString($account->DefaultSkin),
									$this->_escapeString($account->DefaultLanguage),
									ConvertUtils::GetCodePageNumber($account->DefaultIncCharset),
									ConvertUtils::GetCodePageNumber($account->DefaultOutCharset),
									(int) $account->DefaultTimeZone,
									$this->_escapeString($account->DefaultDateFormat),
									$this->Bool2Bool((int) $account->HideFolders),
									(int) $account->MailboxLimit,
									$this->Bool2Bool((int) $account->AllowChangeSettings),
									$this->Bool2Bool((int) $account->AllowDhtmlEditor),
									$this->Bool2Bool((int) $account->AllowDirectMode),
									$this->Bool2Bool((int) $account->HideContacts),
									ConvertUtils::GetCodePageNumber($account->DbCharset),
									$account->HorizResizer,
									$account->VertResizer,
									$account->Mark,
									$account->Reply,
									$account->ContactsPerPage,
									$account->ViewMode);
		}
		
		/**
		 * @param CalendarUser $calUser
		 * @return string
		 */
		function InsertCalendarSettings($calUser)
		{
			$sql = 'INSERT INTO %sacal_users_data (user_id, timeformat, dateformat, 
						showweekends, workdaystarts, workdayends, showworkday, 
						defaulttab, country, timezone, alltimezones)	
					VALUES (%d, %d, %d, %d, %d, %d, %d, %d, %s, %d, %d)';
			
			return sprintf($sql, $this->_settings->DbPrefix,
									$calUser->Id, 
									$calUser->TimeFormat,
									$calUser->DateFormat,
									(int) $calUser->ShowWeekends,
									$calUser->WorkdayStarts,
									$calUser->WorkdayEnds,
									(int) $calUser->ShowWorkday,
									$calUser->DefaultTab,
									$this->_escapeString($calUser->Country),
									$calUser->TimeZone,
									(int) $calUser->AllTimeZones
									);
		}
		

		/**
		 * @param Account $account
		 * @param array $emailsArray
		 * @return string
		 */
		function SelectExistEmails(&$account, $emailsArray)
		{
			$emailsArray = array_map(array(&$this, '_escapeString'), $emailsArray);
			$emailsString = implode(', ', $emailsArray);
			
			$sql = 'SELECT h_email, b_email, other_email
					FROM %sawm_addr_book
					WHERE id_user = %d AND (h_email IN (%s) OR b_email IN (%s) OR other_email IN (%s))';
			
			return sprintf($sql, $this->_settings->DbPrefix, $account->IdUser, $emailsString, $emailsString, $emailsString);
			
		}
		
		/**
		 * @param Account $account
		 * @param string $email
		 * @param string $name
		 * @return string
		 */
		function InsertAutoCreateContact(&$account, $email, $name = '')
		{
			$sql = 'INSERT INTO %sawm_addr_book
					(id_user, h_email, fullname, primary_email, auto_create) 
					VALUES (%d, %s, %s, 0, 1)';
			
			return sprintf($sql, $this->_settings->DbPrefix, $account->IdUser, $this->_escapeString($email), $this->_escapeString($name));	
		}
		
		/**
		 * @param Account $account
		 * @param array $emailsArray
		 * @return string
		 */
		function UpdateContactFrequencyByEmail(&$account, $emailsArray)
		{
			$emailsArray = array_map(array(&$this, '_escapeString'), $emailsArray);
			$emailsString = implode(', ', $emailsArray);
					
			$sql = 'UPDATE %sawm_addr_book
					SET use_frequency = use_frequency + 1
					WHERE id_user = %d AND (h_email IN (%s) OR b_email IN (%s) OR other_email IN (%s))';
			
			return sprintf($sql, $this->_settings->DbPrefix, $account->IdUser, $emailsString, $emailsString, $emailsString);	
		}
		
		
		/**
		 * @param int $idUser
		 * @return string
		 */
		function SelectAccountColumnsData($idUser)
		{
			$sql = 'SELECT id_column, column_value FROM %sawm_columns WHERE id_user = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $idUser);
		}
		
		/**
		 * @param int $idUser
		 * @param int $id_column
		 * @param int $value_column
		 * @return string
		 */
		function UpdateColumnData($idUser, $id_column, $value_column)
		{
			$sql = 'UPDATE %sawm_columns SET column_value = %d
						WHERE id_user = %d AND id_column = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $value_column, $idUser, $id_column);			
		}
		
		/**
		 * @param int $idUser
		 * @param int $id_column
		 * @param int $value_column
		 * @return string
		 */		
		function InsertColumnData($idUser, $id_column, $value_column)
		{
			$sql = 'INSERT INTO %sawm_columns (id_user, id_column, column_value)
						VALUES (%d, %d, %d)';
			return sprintf($sql, $this->_settings->DbPrefix, $idUser, $id_column, $value_column);
		}

		/**
		 * @param array $emailsArray
		 * @return string
		 */
		function SelectAccountsIdByEmails($emailsArray)
		{
			$emailsArray = array_map(array(&$this, '_escapeString'), $emailsArray);
			$emailsString = implode(',', $emailsArray);
			
			$sql = 'SELECT id_acct
					FROM %sawm_accounts
					WHERE mail_protocol = 2 AND email IN (%s)';
			
			return sprintf($sql, $this->_settings->DbPrefix, $emailsString);
		}
		
		
		/**
		 * @param int $userId
		 * @return string
		 */
		function SelectAccounts($userId)
		{
			$sql = 'SELECT id_acct, mail_protocol, def_order, use_friendly_nm,
							friendly_nm, email, getmail_at_login, def_acct
					FROM %sawm_accounts
					WHERE id_user = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $userId);
		}
		
		/** 
		 * @param string $email
		 * @param string $login
		 * @return string
		 */
		function SelectAccountDataByLogin($email, $login)
		{
			$sql = 'SELECT id_acct, id_user, mail_inc_pass, def_acct
					FROM %sawm_accounts
					WHERE email = %s AND mail_inc_login = %s';
			
			return sprintf($sql, $this->_settings->DbPrefix,
									$this->_escapeString($email),
									$this->_escapeString($login));
		}
		
		/** 
		 * @param string $email
		 * @param string $login
		 * @return string
		 */
		function SelectDefAccountDataByLogin($email, $login)
		{
			$sql = 'SELECT id_acct, id_user, mail_inc_pass, def_acct
					FROM %sawm_accounts
					WHERE email = %s AND mail_inc_login = %s AND def_acct = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix,
									$this->_escapeString($email),
									$this->_escapeString($login),
									$this->Bool2Bool(1));
		}
		
		/** 
		 * @param string $email
		 * @param string $login
		 * @return string
		 */
		function SelectAccountDataOnlyByEmail($email)
		{
			$sql = 'SELECT id_acct, id_user, mail_inc_pass, def_acct, mail_inc_login
					FROM %sawm_accounts
					WHERE email = %s AND def_acct = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix,
									$this->_escapeString($email), $this->Bool2Bool(1));
		}
		
		/**
		 * @param array $arrIds
		 * @return string
		 */
		function UpdateGroupsFrequency($arrIds)
		{
			$strIds = (is_array($arrIds) && count($arrIds) > 0) ? implode(',', $arrIds) : '-1';
			
			$sql = 'UPDATE %sawm_addr_groups
					SET use_frequency = use_frequency + 1
					WHERE id_group IN (%s)';
			
					
			return sprintf($sql, $this->_settings->DbPrefix, $strIds);
		}
		
		/** 
		 * @param string $email
		 * @param string $login
		 * @return string
		 */
		function SelectAccountsCountByLogin($email, $login)
		{
			$sql = 'SELECT COUNT(id_acct) AS acct_count
					FROM %sawm_accounts
					WHERE email = %s AND mail_inc_login = %s';
			
			return sprintf($sql, $this->_settings->DbPrefix,
									$this->_escapeString($email),
									$this->_escapeString($login));
		}
		
		/** 
		 * @param string $email
		 * @param string $login
		 * @return string
		 */
		function SelectDefAccountsCountByLogin($email, $login, $idAcct = null)
		{
			$temp = ($idAcct !== null) ? ' AND id_acct <> '.(int) $idAcct : '';
			
			$sql = 'SELECT COUNT(id_acct) AS acct_count
					FROM %sawm_accounts
					WHERE email = %s AND mail_inc_login = %s AND def_acct = %d' . $temp;
			
			return sprintf($sql, $this->_settings->DbPrefix,
									$this->_escapeString($email),
									$this->_escapeString($login), 
									$this->Bool2Bool(1));
		}
		
		/**
		 * @param int $accountId
		 * @param int $newAccountId
		 * @return string
		 */
		function SelectIsAccountInRing($accountId, $newAccountId)
		{
			$sql = 'SELECT COUNT(a.id_acct) AS acct_count
					FROM %sawm_accounts AS a
					INNER JOIN %sawm_accounts AS b ON a.id_user = b.id_user
					WHERE a.id_acct = %d AND b.id_acct = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $this->_settings->DbPrefix,
								$accountId, $newAccountId);
		}
		

		/**
		 * @param int $accountId
		 * @return string
		 */
		function SelectAccountData($accountId)
		{
			$sql = 'SELECT id_acct, acct.id_user as id_user, def_acct, deleted, email, mail_protocol,
						mail_inc_host, mail_inc_login, mail_inc_pass, mail_inc_port, mail_out_host,
						mail_out_login, mail_out_pass, mail_out_port, mail_out_auth, friendly_nm,
						use_friendly_nm, def_order,	getmail_at_login, mail_mode, mails_on_server_days,
						signature, signature_type, signature_opt, delimiter,
						msgs_per_page, white_listing, x_spam, last_login, logins_count,	def_skin,
						def_lang, def_charset_inc, def_charset_out, def_timezone, def_date_fmt,
						hide_folders, mailbox_limit, mailbox_size, allow_change_settings,
						allow_dhtml_editor,	allow_direct_mode, hide_contacts, db_charset,
						horiz_resizer, vert_resizer, mark, reply, contacts_per_page, view_mode
					FROM %sawm_accounts AS acct
					INNER JOIN %sawm_settings AS sett ON acct.id_user = sett.id_user
					WHERE id_acct = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $this->_settings->DbPrefix, $accountId);
		}
		
		/** 
		 * @param string $email
		 * @param string $login
		 * @return string
		 */
		function SelectAccountFullDataByLogin($email, $login)
		{
			$sql = 'SELECT id_acct, acct.id_user as id_user, def_acct, deleted, email, mail_protocol,
						mail_inc_host, mail_inc_login, mail_inc_pass, mail_inc_port, mail_out_host,
						mail_out_login, mail_out_pass, mail_out_port, mail_out_auth, friendly_nm,
						use_friendly_nm, def_order,	getmail_at_login, mail_mode, mails_on_server_days,
						signature, signature_type, signature_opt, delimiter,
						msgs_per_page, white_listing, x_spam, last_login, logins_count,	def_skin,
						def_lang, def_charset_inc, def_charset_out, def_timezone, def_date_fmt,
						hide_folders, mailbox_limit, mailbox_size, allow_change_settings,
						allow_dhtml_editor,	allow_direct_mode, hide_contacts, db_charset,
						horiz_resizer, vert_resizer, mark, reply, contacts_per_page, view_mode
					FROM %sawm_accounts AS acct
					INNER JOIN %sawm_settings AS sett ON acct.id_user = sett.id_user
					WHERE email = %s AND mail_inc_login = %s';
			
			return sprintf($sql, $this->_settings->DbPrefix, $this->_settings->DbPrefix, 
								$this->_escapeString($email), $this->_escapeString($login));
		}
		
		/**
		 * @param int $userId
		 * @return string
		 */
		function SelectSetings($userId)
		{
			$sql = 'SELECT * FROM %sawm_settings WHERE id_user = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $userId);		
		}

		/**
		 * @param Account $account
		 * @return string
		 */
		function UpdateAccount(&$account)
		{
			$sql = 'UPDATE %sawm_accounts SET
						def_acct = %s, deleted = %s, email = %s, mail_protocol = %s,
						mail_inc_host = %s, mail_inc_login = %s, mail_inc_pass = %s, mail_inc_port = %s,
						mail_out_host = %s, mail_out_login = %s, mail_out_pass = %s, mail_out_port = %s,
						mail_out_auth = %s, friendly_nm = %s, use_friendly_nm = %s, def_order = %s,
						getmail_at_login = %s, mail_mode = %s, mails_on_server_days = %s,
						signature = %s, signature_type = %s, signature_opt = %s, 
						delimiter = %s, mailbox_size = %d
					WHERE id_acct = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix,
									$this->Bool2Bool((int) $account->DefaultAccount),
									$this->Bool2Bool((int) $account->Deleted),
									$this->_escapeString($account->Email),
									(int) $account->MailProtocol,
									$this->_escapeString($account->MailIncHost),
									$this->_escapeString($account->MailIncLogin),
									$this->_escapeString(ConvertUtils::EncodePassword($account->MailIncPassword, $account)),
									(int) $account->MailIncPort,
									$this->_escapeString($account->MailOutHost),
									$this->_escapeString($account->MailOutLogin),
									$this->_escapeString(ConvertUtils::EncodePassword($account->MailOutPassword, $account)),
									(int) $account->MailOutPort,
									$this->Bool2Bool((int) $account->MailOutAuthentication),
									$this->_escapeString($account->FriendlyName),
									$this->Bool2Bool((int) $account->UseFriendlyName),
									(int) $account->DefaultOrder,
									$this->Bool2Bool((int) $account->GetMailAtLogin),
									(int) $account->MailMode,
									(int) $account->MailsOnServerDays,
									$this->_escapeString($account->Signature),
									(int) $account->SignatureType,
									(int) $account->SignatureOptions,
									$this->_escapeString($account->Delimiter),
									$account->MailboxSize,
									$account->Id);		
		}	
		
		/**
		 * @param Account $account
		 * @return string
		 */
		function UpdateSettings(&$account)
		{
			$sql = 'UPDATE %sawm_settings SET
						msgs_per_page = %s, white_listing = %s, x_spam = %s,
						def_skin = %s, def_lang = %s, def_charset_inc = %d,
						def_charset_out = %d, def_timezone = %d, def_date_fmt = %s,
						hide_folders = %d, mailbox_limit = %d, allow_change_settings = %d,
						allow_dhtml_editor = %d, allow_direct_mode = %d, hide_contacts = %d,
						db_charset = %d, horiz_resizer = %d, vert_resizer = %d, mark = %d,
						reply = %d, contacts_per_page = %d, view_mode = %d
					WHERE id_user = %d';

			return sprintf($sql, $this->_settings->DbPrefix,
									((int) $account->MailsPerPage > 0) ? (int) $account->MailsPerPage : 20,
									(int) $account->WhiteListing,
									(int) $account->XSpam,
									$this->_escapeString($account->DefaultSkin),
									$this->_escapeString($account->DefaultLanguage),
									ConvertUtils::GetCodePageNumber($account->DefaultIncCharset),
									ConvertUtils::GetCodePageNumber($account->DefaultOutCharset),
									$account->DefaultTimeZone,
									$this->_escapeString(CDateTime::GetDbDateFormat($account->DefaultDateFormat, $account->DefaultTimeFormat)),
									$this->Bool2Bool((int) $account->HideFolders),
									$account->MailboxLimit,
									$this->Bool2Bool((int) $account->AllowChangeSettings),
									$this->Bool2Bool((int) $account->AllowDhtmlEditor),
									$this->Bool2Bool((int) $account->AllowDirectMode),
									$this->Bool2Bool((int) $account->HideContacts),
									ConvertUtils::GetCodePageNumber($account->DbCharset),
									$account->HorizResizer,
									$account->VertResizer,
									$account->Mark,
									$account->Reply,
									((int) $account->ContactsPerPage > 0) ? (int) $account->ContactsPerPage : 20,
									(int) $account->ViewMode,
									$account->IdUser);
		}
		
		/**
		 * @param int $userId
		 * @return string
		 */
		function UpdateLastLoginAndLoginsCount($userId)
		{
			$sql = 'UPDATE %sawm_settings
						SET last_login = %s, logins_count = logins_count + 1
					WHERE id_user = %d';

			$date = &new CDateTime(time());
			return sprintf($sql, $this->_settings->DbPrefix,
							$this->_escapeString($date->ToANSI()), $userId);
		}
		
		/**
		 * @param int $accountId
		 * @param int $msgId
		 * @param string $subject
		 * @param bool $hasAttach
		 * @return string
		 */
	/*	function UpdateMessageHeader($accountId, $msgId, $subject, $hasAttach)
		{
			$sql = 'UPDATE %sawm_messages
						SET subject = %s, attachments = %d
					WHERE id_acct = %d AND id_msg = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $subject,
									(int) $hasAttach, (int) $accountId, (int) $msgId);
		}*/

		/**
		 * @param Account $account
		 * @param int $msgId
		 * @param int $charset
		 * @param WebMailMessage $message
		 * @return string
		 */
		function UpdateMessageCharset(&$account, $msgId, $charset, &$message)
		{
			$sql = 'UPDATE %sawm_messages
						SET charset = %d, from_msg = %s, to_msg = %s, cc_msg = %s, bcc_msg = %s, subject = %s
					WHERE id_acct = %d AND id_msg = %d';
			
			$from = &new I18nString($message->GetFromAsString(), $account->DbCharset);
			$to = &new I18nString($message->GetToAsString(), $account->DbCharset);
			$cc = &new I18nString($message->GetCcAsString(), $account->DbCharset);
			$bcc = &new I18nString($message->GetBccAsString(), $account->DbCharset);
			$subject = &new I18nString($message->GetSubject(), $account->DbCharset);

			return sprintf($sql, $this->_settings->DbPrefix, (int) $charset,
									$this->_escapeString($from->Truncate(255)),
									$this->_escapeString($to->Truncate(255)),
									$this->_escapeString($cc->Truncate(255)),
									$this->_escapeString($bcc->Truncate(255)),
									$this->_escapeString($subject->Truncate(255)),
									(int) $account->Id, (int) $msgId);
		}
		
		/**
		 * @param Array $messageIndexSet
		 * @param Boolean $indexAsUid
		 * @param Folder $folder
		 * @param int $flags
		 * @param Account $account
		 * @return String
		 */
		function UpdateMessageFlags($messageIndexSet, $indexAsUid, &$folder, $flags, &$account)
		{
			
			$sql = 'UPDATE %sawm_messages
					SET flags = %d'; // set new flags

			if (($flags & MESSAGEFLAGS_Seen) == MESSAGEFLAGS_Seen)
			{
				$sql .= ', seen = '.$this->Bool2Bool(1);
			}
			if (($flags & MESSAGEFLAGS_Flagged) == MESSAGEFLAGS_Flagged)
			{
				$sql .= ', flagged = '.$this->Bool2Bool(1);
			}
			if (($flags & MESSAGEFLAGS_Deleted) == MESSAGEFLAGS_Deleted)
			{
				$sql .= ', deleted = '.$this->Bool2Bool(1);
			}
			if (($flags & MESSAGEFLAGS_Answered) == MESSAGEFLAGS_Answered)
			{
				$sql .= ', replied = '.$this->Bool2Bool(1);
			}
			if (($flags & MESSAGEFLAGS_Answered) == MESSAGEFLAGS_Answered)
			{
				$sql .= ', replied = '.$this->Bool2Bool(1);
			}
			if (($flags & MESSAGEFLAGS_Forwarded) == MESSAGEFLAGS_Forwarded)
			{
				$sql .= ', forwarded = '.$this->Bool2Bool(1);
			}
			if (($flags & MESSAGEFLAGS_Grayed) == MESSAGEFLAGS_Grayed)
			{
				$sql .= ', grayed = '.$this->Bool2Bool(1);
			}
			
			if ($messageIndexSet != null)
			{
				$sql .= ' WHERE id_acct = %d AND id_folder_db = %d AND %s IN (%s)';
				
				return sprintf($sql, $this->_settings->DbPrefix, $flags, $account->Id, $folder->IdDb,
								$this->_getMsgIdUidFieldName($indexAsUid, $account->MailProtocol),
								$this->_quoteUids($messageIndexSet, $indexAsUid, $account->MailProtocol));
			}
			
			$sql .= ' WHERE id_acct = %d AND id_folder_db = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $flags, $account->Id, $folder->IdDb);

		}
		
		/** 
		 * @param int $accountId
		 * @return string
		 */
		function CountAccounts($accountId)
		{
			//check is this last account or no
			$sql = 'SELECT COUNT(t1.id_acct) AS count, t1.id_user AS id_user
					FROM %sawm_accounts AS t1
					INNER JOIN %sawm_accounts AS t2 ON t1.id_user = t2.id_user
					WHERE t1.id_acct = %d
					GROUP BY t1.id_user';
			
			return sprintf($sql, $this->_settings->DbPrefix, $this->_settings->DbPrefix, $accountId);
		}
		
		/**
		 * @param bool $deleted
		 * @return string
		 */
		function InsertUserData($deleted)
		{
			$sql = 'INSERT INTO %sa_users (deleted) VALUES (%d)';

			return sprintf($sql, $this->_settings->DbPrefix, $this->Bool2Bool((int) $deleted));
		}
		
	
		/**
		 * @param Folder $folder
		 * @return string
		 */
		function CreateFolderTree(&$folder)
		{
			$sql = 'INSERT INTO %sawm_folders_tree (id_folder, id_parent, folder_level)	
					VALUES (%d, %d, 0)';

			return sprintf($sql, $this->_settings->DbPrefix, $folder->IdDb, $folder->IdDb);
		}
		
		/**
		 * @param Folder $folder
		 * @return string
		 */
		function CreateSelectFolderTree(&$folder)
		{
			$sql = 'INSERT INTO %sawm_folders_tree (id_folder, id_parent, folder_level)	
					VALUES (%d, %d, %d)';

			return sprintf($sql, $this->_settings->DbPrefix, $folder->IdDb,
									$folder->IdParent, $folder->Level);			
		}		
		
		/**
		 * @param Folder $folder
		 * @return string
		 */
		function SelectForCreateFolderTree(&$folder)
		{
			$sql = 'SELECT id_parent, folder_level
					FROM %sawm_folders_tree
					WHERE id_folder = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $folder->IdParent);		
		}
		
		/**
		 * @param Folder $folder
		 * @param string $newName
		 * @return string
		 */
		function RenameFolder(&$folder, $newName)
		{
			$sql = 'UPDATE %sawm_folders
					SET full_path = %s
					WHERE id_acct = %d AND id_folder = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $this->_escapeString($newName.'#'),
								$folder->IdAcct, $folder->IdDb);
		}
		
		/**
		 * @param Folder $folder
		 * @param Array $foldersId
		 * @param string $newName
		 * @return string
		 */
		function RenameSubFoldersPath(&$folder, &$foldersId, $newSubPath)
		{
			$sql = 'UPDATE %sawm_folders
					SET full_path = CONCAT("%s", SUBSTRING(full_path, %d))
					WHERE id_acct = %d AND id_folder IN (%s) AND id_folder <> %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $newSubPath, strlen($folder->FullName)+1,
								$folder->IdAcct, implode(',', $foldersId), $folder->IdDb);
		}
		
		/**
		 * @param Folder $folder
		 * @return string
		 */
		function DeleteFolder(&$folder)
		{
			$sql = 'DELETE FROM %sawm_folders
					WHERE %sawm_folders.id_folder = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $this->_settings->DbPrefix, $folder->IdDb);
		}
		
		/**
		 * @param Folder $folder
		 * @return string
		 */
		function DeleteFolderTree(&$folder)
		{
			$sql = 'DELETE FROM %sawm_folders_tree
					WHERE %sawm_folders_tree.id_folder = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $this->_settings->DbPrefix, $folder->IdDb);
		}
		
		/**
		 * @param Folder $folder
		 * @return string
		 */
		function GetFolderInfo(&$folder)
		{
			$sql = 'SELECT full_path, name, type, sync_type, hide, fld_order, id_parent FROM %sawm_folders
					WHERE id_acct = %d AND id_folder = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $folder->IdAcct, $folder->IdDb);
		}
		
		/**
		 * @param Folder $folder
		 * @return string
		 */
		function GetFolderChildCount(&$folder)
		{
			$sql = 'SELECT COUNT(child.id_folder) AS child_count
					FROM %sawm_folders AS parent
					INNER JOIN %sawm_folders AS child ON parent.id_folder = child.id_parent
					WHERE parent.id_acct = %d AND parent.id_folder = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix,
								$this->_settings->DbPrefix, $folder->IdAcct, $folder->IdDb);
		}

		/**
		 * @param Folder $folder
		 * @return string
		 */
		function UpdateFolder(&$folder)
		{
			$sql = 'UPDATE %sawm_folders
					SET name = %s, type = %d, sync_type = %d, hide = %d, fld_order = %d
					WHERE id_acct = %d AND id_folder = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, 
								$this->_escapeString($folder->Name.'#'),
								$folder->Type, $folder->SyncType,
								(int) $folder->Hide, (int) $folder->FolderOrder,
								$folder->IdAcct, $folder->IdDb);
		}

		/**
		 * @param int $accountId
		 * @param short $type
		 * @return string
		 */
		function GetFolderSyncType($accountId, $type)
		{
			$sql = 'SELECT sync_type FROM %sawm_folders WHERE id_acct = %d AND type = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $accountId, $type);
		}
		
		/**
		 * @param WebMailMessage $message
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function UpdateMessageHeader(&$message, &$folder, &$account)
		{
			$sql = 'UPDATE %sawm_messages SET
			 			from_msg = %s, to_msg = %s, cc_msg = %s, bcc_msg = %s, subject = %s,
						msg_date = %s, attachments = %d, size = %d, x_spam = %d,
						seen = %d, flagged = %d, deleted = %d, replied = %d, grayed = %d,
						flags= %d, priority = %d, body_text = %s
					WHERE id_msg = %d AND id_folder_db = %d AND id_acct = %d';
			
			$date = &$message->GetDate();
			$from = &new I18nString($message->GetFromAsString(), $account->DbCharset);
			$to = &new I18nString($message->GetToAsString(), $account->DbCharset);
			$cc = &new I18nString($message->GetCcAsString(), $account->DbCharset);
			$bcc = &new I18nString($message->GetBccAsString(), $account->DbCharset);
			$subject = &new I18nString($message->GetSubject(), $account->DbCharset);
			
			return sprintf($sql, $this->_settings->DbPrefix,
									$this->_escapeString($from->Truncate(255)),
									$this->_escapeString($to->Truncate(255)),
									$this->_escapeString($cc->Truncate(255)),
									$this->_escapeString($bcc->Truncate(255)),
									$this->_escapeString($subject->Truncate(255)),
									
									$this->_escapeString($date->ToANSI()),
									$this->Bool2Bool((int) $message->HasAttachments()),
									(int) $message->GetMailSize(),
									$this->Bool2Bool((int) $message->GetXSpamStatus()),
									(int) (($message->Flags & MESSAGEFLAGS_Seen) == MESSAGEFLAGS_Seen),
									(int) (($message->Flags & MESSAGEFLAGS_Flagged) == MESSAGEFLAGS_Flagged),
									(int) (($message->Flags & MESSAGEFLAGS_Deleted) == MESSAGEFLAGS_Deleted),
									(int) (($message->Flags & MESSAGEFLAGS_Answered) == MESSAGEFLAGS_Answered),
									(int) (($message->Flags & MESSAGEFLAGS_Grayed) == MESSAGEFLAGS_Grayed),
									$message->Flags,
									$message->GetPriorityStatus(),
									$this->_escapeString(ConvertUtils::mainClear(substr($message->GetPlainLowerCaseBodyText(), 0, 500000))),
									$message->IdMsg, $folder->IdDb, $account->Id									
									);
		}
		
		/**
		 * @param WebMailMessage $message
		 * @param Folder $folder
		 * @param bool $downloaded
		 * @param Account $account
		 * @return string
		 */
		function SaveMessageHeader(&$message, &$folder, $downloaded, &$account)
		{
			//save message header
			$sql = 'INSERT INTO %sawm_messages (id_msg, id_acct, id_folder_srv, id_folder_db,
								%s, from_msg, to_msg, cc_msg, bcc_msg, subject,
								msg_date, attachments, size, downloaded, x_spam,
								seen, flagged, deleted, replied, grayed, flags, priority, body_text)
					VALUES (%d, %d,	%d, %d, %s, %s, %s, %s, %s, %s, %s, %d, %d, %d,	%d, %d, %d, %d, %d, %d, %d, %d, %s)';
			
			$date = &$message->GetDate();
			$from = &new I18nString($message->GetFromAsString(), $account->DbCharset);
			$to = &new I18nString($message->GetToAsString(), $account->DbCharset);
			$cc = &new I18nString($message->GetCcAsString(), $account->DbCharset);
			$bcc = &new I18nString($message->GetBccAsString(), $account->DbCharset);
			$subject = &new I18nString($message->GetSubject(), $account->DbCharset);
			
			$uid = ($account->MailProtocol == MAILPROTOCOL_IMAP4) 
						? (int) $message->Uid : $this->_escapeString($message->Uid);

			return sprintf($sql, $this->_settings->DbPrefix,
									$this->_getMsgIdUidFieldName(true, $account->MailProtocol),
									$message->IdMsg,
									$account->Id,
									$folder->IdDb, $folder->IdDb,
									$uid,
									
									$this->_escapeString($from->Truncate(255)),
									$this->_escapeString($to->Truncate(255)),
									$this->_escapeString($cc->Truncate(255)),
									$this->_escapeString($bcc->Truncate(255)),
									$this->_escapeString($subject->Truncate(255)),
									
									$this->_escapeString($date->ToANSI()),
									$this->Bool2Bool((int) $message->HasAttachments()),
									(int) $message->GetMailSize(),
									$this->Bool2Bool((int) $downloaded),
									$this->Bool2Bool((int) $message->GetXSpamStatus()),
									(int) (($message->Flags & MESSAGEFLAGS_Seen) == MESSAGEFLAGS_Seen),
									(int) (($message->Flags & MESSAGEFLAGS_Flagged) == MESSAGEFLAGS_Flagged),
									(int) (($message->Flags & MESSAGEFLAGS_Deleted) == MESSAGEFLAGS_Deleted),
									(int) (($message->Flags & MESSAGEFLAGS_Answered) == MESSAGEFLAGS_Answered),
									(int) (($message->Flags & MESSAGEFLAGS_Grayed) == MESSAGEFLAGS_Grayed),
									$message->Flags,
									$message->GetPriorityStatus(),
									$this->_escapeString(ConvertUtils::mainClear(substr($message->GetPlainLowerCaseBodyText(), 0, 500000))));
		}
		
		
		function GetMessageSize(&$message, &$folder, $accountId)
		{
			$sql = 'SELECT size FROM %sawm_messages
					WHERE id_msg = %d AND id_folder_db = %d AND id_acct = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $message->IdMsg, $folder->IdDb, $accountId);
		}
		
		/**
		 * @param WebMailMessage $message
		 * @param string $AccountId
		 * @return string
		 */
		function SaveBody(&$message, $accountId)
		{
			//save body
			$sql = 'INSERT INTO %sawm_messages_body (id_acct, id_msg, msg)
					VALUES (%d, %d, %s)';
				
			return sprintf($sql, $this->_settings->DbPrefix, $accountId,
										$message->IdMsg, $this->_escapeString($message->TryToGetOriginalMailMessage()));
		}				

		/**
		 * @param WebMailMessage $message
		 * @param string $AccountId
		 * @return string
		 */
		function UpdateBody(&$message, $accountId)
		{
			$sql = 'UPDATE %sawm_messages_body SET msg = %s
					WHERE msgs.id_acct = %d AND msgs.id_msg = %d';
				
			return sprintf($sql, $this->_settings->DbPrefix,
						$this->_escapeString($message->TryToGetOriginalMailMessage()),
						$accountId,	$message->IdMsg);
		}
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function LoadMessagesFromDB(&$messageIndexSet, $indexAsUid, &$folder, &$account)
		{
			//read messages from db
			$sql = 'SELECT msgs.id_msg AS id_msg, %s AS uid, msgs.priority, msgs.flags, body.msg
					FROM %sawm_messages AS msgs
					INNER JOIN %sawm_messages_body AS body ON msgs.id_msg = body.id_msg AND
								msgs.id_acct = body.id_acct
					WHERE msgs.id_acct = %d AND msgs.id_folder_db = %d AND msgs.%s IN (%s)';

			return sprintf($sql, $this->_getMsgIdUidFieldName(true, $account->MailProtocol),
							$this->_settings->DbPrefix, $this->_settings->DbPrefix,
							$account->Id, $folder->IdDb, $this->_getMsgIdUidFieldName($indexAsUid, $account->MailProtocol),
							$this->_quoteUids($messageIndexSet, $indexAsUid, $account->MailProtocol));
		}

		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function LoadMessagesFromFileSystem(&$messageIndexSet, $indexAsUid, &$folder, &$account)
		{
			//read messages from the file system
			$sql = 'SELECT id_msg, %s AS uid, priority, flags
					FROM %sawm_messages AS msg
					WHERE id_acct = %d AND id_folder_db = %d AND %s IN (%s)';

			return sprintf($sql, $this->_getMsgIdUidFieldName(true, $account->MailProtocol), $this->_settings->DbPrefix, $account->Id, 
							$folder->IdDb, $this->_getMsgIdUidFieldName($indexAsUid, $account->MailProtocol),
							$this->_quoteUids($messageIndexSet, $indexAsUid, $account->MailProtocol));
		}
		
		/**
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function SelectIdMsgAndUid(&$folder, &$account)
		{
			$sql = 'SELECT id_msg, %s AS uid, flags AS flag
					FROM %sawm_messages
					WHERE id_acct = %d AND id_folder_srv = %d
					ORDER BY id_msg DESC';
			
			return sprintf($sql, $this->_getMsgIdUidFieldName(true, $account->MailProtocol),
							$this->_settings->DbPrefix, $account->Id, $folder->IdDb);
		}
		
		/**
		 * @param int $accountId
		 * @return string
		 */
		function SelectLastIdMsg($accountId)
		{
			$sql = 'SELECT MAX(id_msg) AS nid_msg
					FROM %sawm_messages
					WHERE id_acct = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $accountId);
		}
		
		/**
		 * @param int $messageId
		 * @param Folder $folder
		 * @param int $accountId
		 * @return string
		 */
		function GetMessageDownloadedFlag($messageId, &$folder, $accountId)
		{
			$sql = 'SELECT downloaded FROM %sawm_messages
					WHERE id_acct = %d AND id_folder_db = %d AND id_msg = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $accountId, $folder->IdDb, $messageId);
		}
		
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function DeleteMessagesHeaders(&$messageIndexSet, $indexAsUid, &$folder, &$account)
		{

			$sql = 'DELETE FROM %sawm_messages 
					WHERE id_acct = %d AND id_folder_db = %d AND %s IN (%s)';
			
			return sprintf($sql, $this->_settings->DbPrefix, $account->Id, $folder->IdDb, 
							$this->_getMsgIdUidFieldName($indexAsUid, $account->MailProtocol),
							$this->_quoteUids($messageIndexSet, $indexAsUid, $account->MailProtocol));
		}
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $fromFolder
		 * @param Folder $toFolder
		 * @param Account $account
		 * @return string
		 */
		function MoveMessages(&$messageIndexSet, $indexAsUid, &$fromFolder, &$toFolder, &$account)
		{
			$sql = 'UPDATE %sawm_messages
					SET id_folder_db = %d
					WHERE id_acct = %d AND id_folder_db = %d  AND %s IN (%s)';
			
			return sprintf($sql, $this->_settings->DbPrefix, $toFolder->IdDb,
								$account->Id, $fromFolder->IdDb,
								$this->_getMsgIdUidFieldName($indexAsUid, $account->MailProtocol),
								$this->_quoteUids($messageIndexSet, $indexAsUid, $account->MailProtocol));
								
		}
		
		/**
		 * @param int $idAddress
		 * @return string
		 */
		function SelectAddressBookRecord($idAddress, $idUser)
		{
			$sql = 'SELECT id_addr, id_user, h_email, fullname,
						notes, use_friendly_nm, h_street, h_city, h_state, h_zip, h_country,
						h_phone, h_fax, h_mobile, h_web, b_email, b_company, b_street, b_city,
						b_state, b_zip, b_country, b_job_title, b_department, b_office, b_phone,
						b_fax, b_web, other_email, primary_email, id_addr_prev, tmp, birthday_day, 
						birthday_month, birthday_year
					FROM %sawm_addr_book WHERE id_addr = %d AND id_user = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $idAddress, $idUser);
		}
		
		/**
		 * @param AddressBookRecord $addressBook
		 * @return string
		 */
		function InsertAddressBookRecord(&$addressBookRecord)
		{
			$sql = 'INSERT INTO %sawm_addr_book (id_user, h_email, fullname,
									notes, use_friendly_nm, h_street, h_city, h_state, h_zip, h_country,
									h_phone, h_fax, h_mobile, h_web, b_email, b_company, b_street, b_city,
									b_state, b_zip, b_country, b_job_title, b_department, b_office, b_phone,
									b_fax, b_web, other_email, primary_email, id_addr_prev, tmp,
									birthday_day, birthday_month, birthday_year)
					VALUES (%d, %s, %s, %s, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s,
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d)';
			
			return sprintf($sql, $this->_settings->DbPrefix,
									(int) $addressBookRecord->IdUser,
									$this->_escapeString($addressBookRecord->HomeEmail),
									$this->_escapeString($addressBookRecord->FullName),
									$this->_escapeString($addressBookRecord->Notes),
									(int) $addressBookRecord->UseFriendlyName,
									$this->_escapeString($addressBookRecord->HomeStreet),
									$this->_escapeString($addressBookRecord->HomeCity),
									$this->_escapeString($addressBookRecord->HomeState),
									$this->_escapeString($addressBookRecord->HomeZip),
									$this->_escapeString($addressBookRecord->HomeCountry),
									$this->_escapeString($addressBookRecord->HomePhone),
									$this->_escapeString($addressBookRecord->HomeFax),
									$this->_escapeString($addressBookRecord->HomeMobile),
									$this->_escapeString($addressBookRecord->HomeWeb),
									$this->_escapeString($addressBookRecord->BusinessEmail),
									$this->_escapeString($addressBookRecord->BusinessCompany),
									$this->_escapeString($addressBookRecord->BusinessStreet),
									$this->_escapeString($addressBookRecord->BusinessCity),
									$this->_escapeString($addressBookRecord->BusinessState),
									$this->_escapeString($addressBookRecord->BusinessZip),
									$this->_escapeString($addressBookRecord->BusinessCountry),
									$this->_escapeString($addressBookRecord->BusinessJobTitle),
									$this->_escapeString($addressBookRecord->BusinessDepartment),
									$this->_escapeString($addressBookRecord->BusinessOffice),
									$this->_escapeString($addressBookRecord->BusinessPhone),
									$this->_escapeString($addressBookRecord->BusinessFax),
									$this->_escapeString($addressBookRecord->BusinessWeb),
									$this->_escapeString($addressBookRecord->OtherEmail),
									(int) $addressBookRecord->PrimaryEmail,
									(int) $addressBookRecord->IdPreviousAddress,
									(int) $addressBookRecord->Temp,
									(int) $addressBookRecord->BirthdayDay,
									(int) $addressBookRecord->BirthdayMonth,
									(int) $addressBookRecord->BirthdayYear);
		}
		
		/**
		 * @param AddressBookRecord $addressBookRecord
		 * @return string
		 */
		function UpdateAddressBookRecord(&$addressBookRecord)
		{
			$sql = 'UPDATE %sawm_addr_book
					SET h_email = %s, fullname = %s,
						notes = %s, use_friendly_nm = %d, h_street = %s, h_city = %s, h_state = %s,
						h_zip = %s, h_country = %s, h_phone = %s, h_fax = %s, h_mobile = %s, h_web = %s,
						b_email = %s, b_company = %s, b_street = %s, b_city = %s, b_state = %s,
						b_zip = %s, b_country = %s, b_job_title = %s, b_department = %s, b_office = %s,
						b_phone = %s, b_fax = %s, b_web = %s, other_email = %s,
						primary_email = %d, id_addr_prev = %d, tmp = %d,
						birthday_day = %d, birthday_month = %d, birthday_year = %d
					WHERE id_addr = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix,
									$this->_escapeString($addressBookRecord->HomeEmail),
									$this->_escapeString($addressBookRecord->FullName),
									$this->_escapeString($addressBookRecord->Notes),
									(int) $addressBookRecord->UseFriendlyName,
									$this->_escapeString($addressBookRecord->HomeStreet),
									$this->_escapeString($addressBookRecord->HomeCity),
									$this->_escapeString($addressBookRecord->HomeState),
									$this->_escapeString($addressBookRecord->HomeZip),
									$this->_escapeString($addressBookRecord->HomeCountry),
									$this->_escapeString($addressBookRecord->HomePhone),
									$this->_escapeString($addressBookRecord->HomeFax),
									$this->_escapeString($addressBookRecord->HomeMobile),
									$this->_escapeString($addressBookRecord->HomeWeb),
									$this->_escapeString($addressBookRecord->BusinessEmail),
									$this->_escapeString($addressBookRecord->BusinessCompany),
									$this->_escapeString($addressBookRecord->BusinessStreet),
									$this->_escapeString($addressBookRecord->BusinessCity),
									$this->_escapeString($addressBookRecord->BusinessState),
									$this->_escapeString($addressBookRecord->BusinessZip),
									$this->_escapeString($addressBookRecord->BusinessCountry),
									$this->_escapeString($addressBookRecord->BusinessJobTitle),
									$this->_escapeString($addressBookRecord->BusinessDepartment),
									$this->_escapeString($addressBookRecord->BusinessOffice),
									$this->_escapeString($addressBookRecord->BusinessPhone),
									$this->_escapeString($addressBookRecord->BusinessFax),
									$this->_escapeString($addressBookRecord->BusinessWeb),
									$this->_escapeString($addressBookRecord->OtherEmail),
									$addressBookRecord->PrimaryEmail,
									$addressBookRecord->IdPreviousAddress,
									(int) $addressBookRecord->Temp,
									$addressBookRecord->BirthdayDay,
									$addressBookRecord->BirthdayMonth,
									$addressBookRecord->BirthdayYear,
									$addressBookRecord->IdAddress);
		}

		/**
		 * @param long $idAddress
		 * @return string
		 */
		function DeleteAddressBookRecord($idAddress)
		{
			$sql = 'DELETE FROM %sawm_addr_book WHERE id_addr = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $idAddress);
		}
		
		/**
		 * @param int $idAddress
		 * @return string
		 */
		function DeleteAddressGroup($idGroup)
		{
			$sql = 'DELETE FROM %sawm_addr_groups WHERE id_group = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $idGroup);
		}

		/**
		 * @param int $idGroup
		 * @return string
		 */
		function DeleteAddressGroupsContactsByIdGroup($idGroup)
		{
			$sql = 'DELETE FROM %sawm_addr_groups_contacts WHERE id_group = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $idGroup);
		}
		
		/**
		 * @param long $idAddress
		 * @return string
		 */
		function DeleteAddressGroupsContactsByIdAddress($idAddress)
		{
			$sql = 'DELETE FROM %sawm_addr_groups_contacts WHERE id_addr = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $idAddress);
		}

		/**
		 * @param long $idAddress
		 * @param int $idGroup
		 * @return string
		 */
		function DeleteAddressGroupsContacts($idAddress, $idGroup)
		{
			$sql = 'DELETE FROM %sawm_addr_groups_contacts WHERE id_addr = %d AND id_group = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $idAddress, $idGroup);
		}

		/**
		 * @param int $idAddress
		 * @return string
		 */
		function SelectAddressGroupContact($idAddress)
		{
			$sql = 'SELECT groups.id_group AS group_id, group_nm
					FROM %sawm_addr_groups AS groups
					INNER JOIN %sawm_addr_groups_contacts AS grcont ON groups.id_group = grcont.id_group
					WHERE grcont.id_addr = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $this->_settings->DbPrefix, $idAddress);
		}
		
		/**
		 * @param int $idGroup
		 * @return string
		 */
		function SelectAddressGroupContacts($idGroup)
		{
			$sql = 'SELECT book.id_addr AS id, fullname,
						CASE primary_email
							WHEN %s THEN h_email
							WHEN %s THEN b_email
							WHEN %s THEN other_email
						END AS email, book.use_friendly_nm AS usefriendlyname
					FROM %sawm_addr_book AS book
					INNER JOIN %sawm_addr_groups_contacts AS grcont ON book.id_addr = grcont.id_addr
					WHERE id_group = %d';
			
			return sprintf($sql, PRIMARYEMAIL_Home, PRIMARYEMAIL_Business, PRIMARYEMAIL_Other,
								$this->_settings->DbPrefix, $this->_settings->DbPrefix, $idGroup);
		}
		
		/**
		 * @param int $idGroup
		 * @return string
		 */
		function SelectGroupById($idGroup)
		{
			$sql = 'SELECT id_user, group_nm, email, company, street, city, state, zip, country, phone, fax, web, organization
					FROM %sawm_addr_groups
					WHERE id_group = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $idGroup);			
		}

		/**
		 * @param int $idAddress
		 * @param int $idGroup
		 * @return string
		 */
		function InsertAddressGroupContact($idAddress, $idGroup)
		{
			$sql = 'INSERT INTO %sawm_addr_groups_contacts(id_addr, id_group) VALUES (%d, %d)';
			return sprintf($sql, $this->_settings->DbPrefix, $idAddress, $idGroup);
		}
		
		/**
		 * @param AddressGroup $group
		 * @return string
		 */
		function InsertAddressGroup(&$group)
		{
			$sql = 'INSERT INTO %sawm_addr_groups (id_user, group_nm, email, company, street, city, state, zip, country, phone, fax, web, organization) 
					VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d)';
			return sprintf($sql, $this->_settings->DbPrefix, $group->IdUser,
																$this->_escapeString($group->Name),
																$this->_escapeString($group->Email),
																$this->_escapeString($group->Company),
																$this->_escapeString($group->Street),
																$this->_escapeString($group->City),
																$this->_escapeString($group->State),
																$this->_escapeString($group->Zip),
																$this->_escapeString($group->Country),
																$this->_escapeString($group->Phone),
																$this->_escapeString($group->Fax),
																$this->_escapeString($group->Web),
																$this->Bool2Bool($group->IsOrganization));
		}

		/**
		 * @param AddressGroup $group
		 * @return string
		 */
		function UpdateAddressGroup(&$group)
		{
			$sql = 'UPDATE %sawm_addr_groups
					SET group_nm = %s, email = %s, company = %s, street = %s, city = %s, state = %s,
					zip = %s, country = %s, phone = %s, fax = %s, web = %s, organization = %d
					WHERE id_user = %d AND id_group = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $this->_escapeString($group->Name),
																$this->_escapeString($group->Email),
																$this->_escapeString($group->Company),
																$this->_escapeString($group->Street),
																$this->_escapeString($group->City),
																$this->_escapeString($group->State),
																$this->_escapeString($group->Zip),
																$this->_escapeString($group->Country),
																$this->_escapeString($group->Phone),
																$this->_escapeString($group->Fax),
																$this->_escapeString($group->Web),
																$this->Bool2Bool($group->IsOrganization),
								$group->IdUser, $group->Id);
		}
		
		/**
		 * @param int $idGroup
		 * @return string
		 */
		function SelectAddressContactsCount($lookForType, $idUser, $condition = null, $idGroup = null)
		{
			$temp = '';
			if ($condition) $condition = ($lookForType == 1) ? $this->_escapeString($condition.'%') : $this->_escapeString('%'.$condition.'%');
			if ($idGroup && $idGroup > -1) $temp = 
				' INNER JOIN '.$this->_settings->DbPrefix.'awm_addr_groups_contacts AS gr_cont ON (gr_cont.id_addr = abook.id_addr AND
								id_group = '.$idGroup.')';
			
			$sql = 'SELECT COUNT(abook.id_addr) AS contacts_count
					FROM %sawm_addr_book AS abook
					%s
					WHERE abook.id_user = %d';
			
			$sql = sprintf($sql, $this->_settings->DbPrefix, $temp, $idUser);
			if ($condition) $sql .= ' AND (fullname LIKE '.$condition.' OR h_email LIKE '.$condition.' OR b_email LIKE '.$condition.' OR other_email LIKE '.$condition.')';
			
			return $sql;
		}
		
		/**
		 * @param int $idGroup
		 * @return string
		 */
		function SelectAddressGroupsCount($lookForType, $idUser, $condition = null)
		{
			if ($condition) $condition = $condition = ($lookForType == 1) ? $this->_escapeString($condition.'%') : $this->_escapeString('%'.$condition.'%');
			$sql = 'SELECT COUNT(id_group) AS groups_count
					FROM %sawm_addr_groups
					WHERE id_user = %d';
			
			$sql = sprintf($sql, $this->_settings->DbPrefix, $idUser);
			
			if ($condition) $sql .= ' AND group_nm LIKE '.$condition;
			
			return $sql;
		}
		
		
		/**
		 * @param int $idGroup
		 * @return string
		 */
		function SelectAddressGroupName($idGroup)
		{
			$sql = 'SELECT group_nm FROM %sawm_addr_groups WHERE id_group = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $idGroup);
		}

		/**
		 * @param int $idUser
		 * @return string
		 */
		function SelectUserAddressGroupNames($idUser)
		{
			$sql = 'SELECT id_group, group_nm FROM %sawm_addr_groups WHERE id_user = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $idUser);
		}
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @param int $flags
		 * @param short $action
		 * @param Account $account
		 * @return string
		 */
		function SetMessagesFlags($messageIndexSet, $indexAsUid, &$folder, $flags, $action, &$account)
		{
			switch ($action)
			{
				case ACTION_Set:
					$sql = 'UPDATE %sawm_messages
							SET flags = (flags | %d) & ~768'; // remove non-Imap flags

					if (($flags & MESSAGEFLAGS_Seen) == MESSAGEFLAGS_Seen)
					{
						$sql .= ', seen = '.$this->Bool2Bool(1);
					}
					if (($flags & MESSAGEFLAGS_Flagged) == MESSAGEFLAGS_Flagged)
					{
						$sql .= ', flagged = '.$this->Bool2Bool(1);
					}
					if (($flags & MESSAGEFLAGS_Deleted) == MESSAGEFLAGS_Deleted)
					{
						$sql .= ', deleted = '.$this->Bool2Bool(1);
					}
					if (($flags & MESSAGEFLAGS_Answered) == MESSAGEFLAGS_Answered)
					{
						$sql .= ', replied = '.$this->Bool2Bool(1);
					}
					if (($flags & MESSAGEFLAGS_Answered) == MESSAGEFLAGS_Answered)
					{
						$sql .= ', replied = '.$this->Bool2Bool(1);
					}
					if (($flags & MESSAGEFLAGS_Forwarded) == MESSAGEFLAGS_Forwarded)
					{
						$sql .= ', forwarded = '.$this->Bool2Bool(1);
					}
					if (($flags & MESSAGEFLAGS_Grayed) == MESSAGEFLAGS_Grayed)
					{
						$sql .= ', grayed = '.$this->Bool2Bool(1);
					}
					break;

				case ACTION_Remove:
					$sql = 'UPDATE %sawm_messages
							SET flags = (flags & ~%d) & ~768'; // remove non-Imap flags
					
					if (($flags & MESSAGEFLAGS_Seen) == MESSAGEFLAGS_Seen)
					{
						$sql .= ', seen = 0';
					}
					if (($flags & MESSAGEFLAGS_Flagged) == MESSAGEFLAGS_Flagged)
					{
						$sql .= ', flagged = 0';
					}
					if (($flags & MESSAGEFLAGS_Deleted) == MESSAGEFLAGS_Deleted)
					{
						$sql .= ', deleted = 0';
					}
					if (($flags & MESSAGEFLAGS_Answered) == MESSAGEFLAGS_Answered)
					{
						$sql .= ', replied = 0';
					}
					if (($flags & MESSAGEFLAGS_Forwarded) == MESSAGEFLAGS_Forwarded)
					{
						$sql .= ', forwarded = 0';
					}
					if (($flags & MESSAGEFLAGS_Grayed) == MESSAGEFLAGS_Grayed)
					{
						$sql .= ', grayed = 0';
					}
					break;
			}
			
			if ($messageIndexSet != null)
			{
				$sql .= ' WHERE id_acct = %d AND id_folder_db = %d AND %s IN (%s)';
				return sprintf($sql, $this->_settings->DbPrefix, $flags, $account->Id, $folder->IdDb,
								$this->_getMsgIdUidFieldName($indexAsUid, $account->MailProtocol),
								$this->_quoteUids($messageIndexSet, $indexAsUid, $account->MailProtocol));
			}
			
			$sql .= ' WHERE id_acct = %d AND id_folder_db = %d';
			return sprintf($sql, $this->_settings->DbPrefix, $flags, $account->Id, $folder->IdDb);

		}
		
		/**
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function SelectAllDeletedMsgId(&$folder, &$account, $pop3EmptyTrash = false)
		{
			$sql = 'SELECT id_msg FROM %sawm_messages
							WHERE id_acct = -%d AND id_folder_db = %d AND downloaded = '.$this->Bool2Bool(1);
			switch ($account->MailProtocol)
			{
				case MAILPROTOCOL_POP3:
				case MAILPROTOCOL_WMSERVER:
					$sql = 'SELECT id_msg FROM %sawm_messages
							WHERE id_acct = %d AND id_folder_db = %d AND downloaded = '.$this->Bool2Bool(1);
					break;
					
				case MAILPROTOCOL_IMAP4:
					if ($pop3EmptyTrash)
					{
						$sql = 'SELECT id_msg FROM %sawm_messages
								WHERE id_acct = %d AND id_folder_db = %d AND downloaded = '.$this->Bool2Bool(1);
					}
					else
					{
						$sql = 'SELECT id_msg FROM %sawm_messages
								WHERE id_acct = %d AND id_folder_db = %d AND
									deleted = '.$this->Bool2Bool(1).' AND downloaded = '.$this->Bool2Bool(1);
					}
					break;
			}
			return sprintf($sql, $this->_settings->DbPrefix, $account->Id, $folder->IdDb);
		}

		/**
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function PurgeAllMessageHeaders(&$folder, &$account, $pop3EmptyTrash = false)
		{
			$sql = 'DELETE FROM %sawm_messages WHERE id_acct = -%d AND id_folder_db = %d';
			switch ($account->MailProtocol)
			{
				case MAILPROTOCOL_POP3:
				case MAILPROTOCOL_WMSERVER:
					$sql = 'DELETE FROM %sawm_messages WHERE id_acct = %d AND id_folder_db = %d';
					break;
					
				case MAILPROTOCOL_IMAP4:
					if ($pop3EmptyTrash)
					{
						$sql = 'DELETE FROM %sawm_messages WHERE id_acct = %d AND id_folder_db = %d';
					}
					else
					{
						$sql = 'DELETE FROM %sawm_messages WHERE id_acct = %d AND id_folder_db = %d AND deleted = '.$this->Bool2Bool(1);
					}
					break;
			}
			
			return sprintf($sql, $this->_settings->DbPrefix, $account->Id, $folder->IdDb);
		}
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Account $account
		 * @return string
		 */
		function SelectDownloadedMessagesIdSet(&$messageIndexSet, $indexAsUid, &$account)
		{
			$sql = 'SELECT id_msg FROM %sawm_messages
					WHERE id_acct = %d AND downloaded = '.$this->Bool2Bool(1).' AND %s IN (%s)';
			
			return sprintf($sql, $this->_settings->DbPrefix, $account->Id,
									$this->_getMsgIdUidFieldName($indexAsUid, $account->MailProtocol),
									$this->_quoteUids($messageIndexSet, $indexAsUid, $account->MailProtocol));
		}
		
		/**
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function SelectAllMessagesUidSetByFolder(&$folder, &$account)
		{
			$sql = 'SELECT %s AS uid FROM %sawm_messages
					WHERE id_acct = %d AND id_folder_db = %d';
			return sprintf($sql, $this->_getMsgIdUidFieldName(true, $account->MailProtocol),
									$this->_settings->DbPrefix, $account->Id, $folder->IdDb);
		}
		
		/**
		 * @param int $accountId
		 * @return string
		 */
		function SelectFilters($accountId)
		{
			$sql = 'SELECT id_filter, field, condition, filter, action, id_folder
					FROM %sawm_filters
					WHERE id_acct = %d
					ORDER BY action';
			return sprintf($sql, $this->_settings->DbPrefix, $accountId);
		}
		
		/**
		 * @param Filter $filter
		 * @return string
		 */
		function InsertFilter(&$filter)
		{
			$sql = 'INSERT INTO %sawm_filters (id_acct, field, condition, filter, action, id_folder)
					VALUES (%d, %d, %d, %s, %d, %d)';
					
			return sprintf($sql, $this->_settings->DbPrefix, $filter->IdAcct,
									$filter->Field, $filter->Condition,
									$this->_escapeString($filter->Filter),
									$filter->Action, $filter->IdFolder);
		}
		
		/**
		 * @param Filter $filter
		 * @return string
		 */
		function UpdateFilter(&$filter)
		{
			$sql = 'UPDATE %sawm_filters SET
						field = %d, condition = %d, filter = %s, action = %d,
						id_folder = %d
					WHERE id_filter = %d AND id_acct = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $filter->Field,
									$filter->Condition,	$this->_escapeString($filter->Filter),
									$filter->Action, $filter->IdFolder,
									$filter->Id, $filter->IdAcct);			
		}

		/**
		 * @param int $filterId
		 * @param int $accountId
		 * @return string
		 */
		function DeleteFilter($filterId, $accountId)
		{
			$sql = 'DELETE FROM %sawm_filters
					WHERE id_filter = %d AND id_acct = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $filterId, $accountId);
		}
		
		/**
		 * @param int $folderId
		 * @param int $accountId
		 * @return string
		 */
		function DeleteFolderFilters($folderId, $accountId)
		{
			$sql = 'DELETE FROM %sawm_filters
					WHERE id_folder = %d AND id_acct = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $folderId, $accountId);
		}

		/**
		 * @param int $accountId
		 * @return string
		 */
		function SelectReadsRecords($accountId)
		{
			$sql = 'SELECT str_uid AS uid
					FROM %sawm_reads
					WHERE id_acct = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $accountId);
		}
		
		/**
		 * @param int $accountId
		 * @param string $pop3Uid
		 * @return string
		 */
		function InsertReadsRecord($accountId, $pop3Uid)
		{
			$sql = 'INSERT INTO %sawm_reads (id_acct, str_uid, tmp) VALUES(%d, %s, %d)';

			return sprintf($sql, $this->_settings->DbPrefix, $accountId, $this->_escapeString($pop3Uid), $this->Bool2Bool(0));
		}
		
		/**
		 * @param int $accountId
		 * @return string
		 */
		function DeleteReadsRecords($accountId)
		{
			$sql = 'DELETE FROM %sawm_reads WHERE id_acct = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $accountId);
		}
		
		/**
		 * @param int $accountId
		 * @param array $uids
		 * @return string
		 */
		function DeleteReadsRecordsByUid($accountId, $uids)
		{
			$uids = array_map(array(&$this, '_escapeString'), $uids);
			$uids = implode(',', $uids);
			$sql = 'DELETE FROM %sawm_reads WHERE id_acct = %d AND str_uid IN (%s)';

			return sprintf($sql, $this->_settings->DbPrefix, $accountId, $uids);
		}

		
		/**
		 * @param int $accountId
		 * @return string
		 */
		function CountMailboxSize($accountId)
		{
			$sql = 'SELECT SUM(size) AS mailbox_size
					FROM %sawm_messages WHERE id_acct = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $accountId);
		}
		
		/**
		 * @param int $size
		 * @param int $accountId
		 * @return string
		 */
		function UpdateMailboxSize($size, $accountId)
		{
			$sql = 'UPDATE %sawm_accounts
					SET mailbox_size = %d
					WHERE id_acct = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $size, $accountId);
		}

		/**
		 * @param int $accountId
		 * @return string
		 */
		function SelectMailboxesSize($userId)
		{
			$sql = 'SELECT SUM(mailbox_size) AS mailboxes_size
					FROM %sawm_accounts WHERE id_user = %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $userId);
		}
		
		/**
		 * @return string
		 */
		function CountAllMailboxSizes()
		{
			$sql = 'SELECT id_user, SUM(mailbox_size) AS mailboxes_size
					FROM %sawm_accounts
					GROUP BY id_user
					ORDER BY id_user';
			
			return sprintf($sql, $this->_settings->DbPrefix);
		}

		/**
		 * @param int $userId
		 */
		function GetAccountListByUserId($userId)
		{
			$sql = 'SELECT id_acct FROM %sawm_accounts WHERE id_user = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $userId);
		}
		
		/**
		 * @param int $userId
		 */
		function GetFullAccountListByUserId($userId)
		{
			$sql = 'SELECT id_acct, def_acct FROM %sawm_accounts WHERE id_user = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $userId);
		}
		
		/**
		 * @param string $fieldName
		 * @return string
		 */
		function GetDateFormat($fieldName)
		{
			return CDateTime::GetMySqlDateFormat($fieldName);
		}
		
		/**
		 * @param int $pageNumber
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function LoadMessageHeaders($pageNumber, &$folder, &$account)
		{
	  		$filter = '';
	  		$asc = true;
	  		
	  		$this->_setSortOrder($account->DefaultOrder, $filter, $asc);
	  		
			//read messages from db
			$sql = 'SELECT id_msg, %s AS uid, id_folder_db, from_msg, to_msg, cc_msg,
							bcc_msg, subject, %s AS nmsg_date, size, priority, x_spam,
							attachments, seen, flagged, deleted, replied, forwarded, grayed, charset
					FROM %sawm_messages
					WHERE id_acct = %d AND id_folder_db = %d
					ORDER BY %s %s
					LIMIT %d, %d';

			return sprintf($sql, $this->_getMsgIdUidFieldName(true, $account->MailProtocol),
								CDateTime::GetMySqlDateFormat('msg_date'),
								$this->_settings->DbPrefix,
								$account->Id, $folder->IdDb,
								$filter, ($asc)?'ASC':'DESC',
								($pageNumber - 1) * $account->MailsPerPage, $account->MailsPerPage);
		}
		
		/**
		 * @param string $condition
		 * @param FolderCollection $folders
		 * @param bool $inHeadersOnly
		 * @param Account $account
		 * @return string
		 */
		function SearchMessagesCount($condition, &$folders, $inHeadersOnly, &$account, $countMessages = 0)
		{
			$foldersId = '';
			foreach (array_keys($folders->Instance()) as $key)
			{
				$folder = &$folders->Get($key);
				$foldersId .= ($foldersId == '')?$folder->IdDb:','.$folder->IdDb;
			}
			
	  		$filter = '';
	  		$asc = true;
	  		
	  		$this->_setSortOrder($account->DefaultOrder, $filter, $asc);
			
	  		$condition = $this->_escapeString('%'.$condition.'%');
	  		
			if ($inHeadersOnly)
			{
				$sql = 'SELECT COUNT(*) AS msg_count
						FROM %sawm_messages
						WHERE id_acct = %d AND id_folder_db IN (%s) AND	
							(from_msg LIKE %s OR to_msg LIKE %s OR cc_msg LIKE %s OR bcc_msg
							LIKE %s OR subject LIKE %s)';
				
				return sprintf($sql, $this->_settings->DbPrefix,
									$account->Id, $foldersId,
									$condition, $condition, $condition, $condition, $condition);
			}
			else
			{
				$sql = 'SELECT COUNT(*) AS msg_count
						FROM %sawm_messages
						WHERE id_acct = %d AND id_folder_db IN (%s) AND	
							(from_msg LIKE %s OR to_msg LIKE %s OR cc_msg LIKE %s OR bcc_msg
							LIKE %s OR subject LIKE %s OR body_text LIKE %s)';
				
				return sprintf($sql, $this->_settings->DbPrefix,
									$account->Id, $foldersId,
									$condition, $condition, $condition, $condition, $condition, $condition);
			}
		}
		
		/**
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function SelectDeletedMessagesId(&$folder, &$account, $pop3EmptyTrash = false)
		{
			$sql = 'SELECT id_msg FROM %sawm_messages
							WHERE id_acct = -%d AND id_folder_db = %d';
			
			switch ($account->MailProtocol)
			{
				case MAILPROTOCOL_WMSERVER:
				case MAILPROTOCOL_POP3:
					$sql = 'SELECT id_msg FROM %sawm_messages
							WHERE id_acct = %d AND id_folder_db = %d';
					break;
					
				case MAILPROTOCOL_IMAP4:
					if ($pop3EmptyTrash)
					{
						$sql = 'SELECT id_msg FROM %sawm_messages
								WHERE id_acct = %d AND id_folder_db = %d';
					}
					else
					{
						$sql = 'SELECT id_msg FROM %sawm_messages
								WHERE id_acct = %d AND id_folder_db = %d AND deleted = 1';
					}
					break;
			}
			
			return sprintf($sql, $this->_settings->DbPrefix, $account->Id, $folder->IdDb);
		}
		
		/**
		 * @param int $accountId
		 * @param array $msgIds
		 * @return string
		 */
		function PurgeAllMessagesBody(&$msgIds, $accountId)
		{
			$sql = 'DELETE FROM %sawm_messages_body
					WHERE id_acct = %d AND id_msg IN (%s)';
			
			return sprintf($sql, $this->_settings->DbPrefix, $accountId, implode(',', $msgIds));
		}
		
		/**
		 * @param Array $contactIds
		 * @param Account $account
		 */
		function LoadContactsById(&$contactIds, &$account)
		{
			
			$sql = 'SELECT id_addr AS id, fullname AS name,
						CASE primary_email
							WHEN %s THEN h_email
							WHEN %s THEN b_email
							WHEN %s THEN other_email
						END AS email, 0 AS is_group, use_frequency AS frequency, use_friendly_nm AS usefriendlyname
					FROM %sawm_addr_book
					WHERE id_user = %d AND id_addr IN (%s)';
			
			return sprintf($sql, PRIMARYEMAIL_Home, PRIMARYEMAIL_Business, PRIMARYEMAIL_Other,
								$this->_settings->DbPrefix, $account->IdUser,
								implode(',', $contactIds));
		}
		
		/**
		 * @param string $conditon
		 * @param int $groupId
		 * @param Account $account
		 */
		function SearchContactsAndGroupsCount($condition, $groupId, &$account)
		{
	  		$condition = $this->_escapeString('%'.$condition.'%');
			
			if ($groupId == -1)
			{
				$sql = 'SELECT count(id_addr) AS countId
						FROM %sawm_addr_book
						WHERE id_user = %d AND (fullname LIKE %s OR h_email LIKE %s OR b_email LIKE %s OR other_email LIKE %s)
						UNION
						SELECT count(id_group) AS countId
						FROM %sawm_addr_groups
						WHERE id_user = %d AND group_nm LIKE %s
						';
				
				return sprintf($sql, $this->_settings->DbPrefix, $account->IdUser, $condition, $condition, $condition, $condition,
									$this->_settings->DbPrefix, $account->IdUser, $condition);
			}
			else
			{
				$sql = 'SELECT count(id_addr) AS countId
						FROM %sawm_addr_book
						WHERE id_user = %d AND id_group = %d AND (fullname LIKE %s OR h_email LIKE %s OR b_email LIKE %s OR other_email LIKE %s)';
				
				return sprintf($sql, $this->_settings->DbPrefix, $account->IdUser, $groupId, $condition, $condition, $condition, $condition);
			}
		}
		
		/**
		 * @param Folder $folder
		 * @return string
		 */
		function SelectForCreateFolder(&$folder)
		{
			$sql = 'SELECT MAX(fld_order) AS norder
					FROM %sawm_folders
					WHERE id_parent = %d';
       
			return sprintf($sql, $this->_settings->DbPrefix, $folder->IdParent);
		}
		
		/**
		 * @param Folder $folder
		 * @return string
		 */
		function CreateFolder(&$folder)
		{
			$sql = 'INSERT INTO %sawm_folders (id_acct, id_parent, type, name, full_path, 
							sync_type, hide, fld_order)
					VALUES (%d, %d, %d, %s, %s, %d, %d, %d)';
			
			return sprintf($sql, $this->_settings->DbPrefix, $folder->IdAcct,
									$folder->IdParent, $folder->Type,
									$this->_escapeString($folder->Name.'#'),
									$this->_escapeString($folder->FullName.'#'),
									$folder->SyncType, (int) $folder->Hide,
									$folder->FolderOrder);
		}
		
		/**
		 * @return string
		 */
		function SelectUsersCount()
		{
			return sprintf('SELECT COUNT(id_user) AS cnt_user FROM %sawm_settings', $this->_settings->DbPrefix);
		}
		
	}
		
	class MySqlCommandCreator extends CommandCreator
	{
		function MySqlCommandCreator()
		{
			CommandCreator::CommandCreator(QUOTE_ESCAPE);
		}
		
		
		/**
		 * @param string $fieldName
		 * @return string
		 */
		function GetDateFormat($fieldName)
		{
			return CDateTime::GetMySqlDateFormat($fieldName);
		}
		
		/**
		 * @return string
		 */
		function AllTableNames()
		{
			return 'show tables';
		}
		
		/**
		 * @return string
		 */
		function GetIndexsOfTable($pref, $tableName)
		{
			return 'SHOW INDEX FROM `'.$pref.$tableName.'`';	
		}	
		
		/**
		 * @param string $pref
		 * @param string $tableName
		 * @return string
		 */
		function GetTablesColumns($pref, $tableName)
		{
			return 'SHOW COLUMNS FROM `'.$pref.$tableName.'`';
		}
		
		/**
		 * @param string $pref
		 * @param string $tableName
		 * @param string $fieldName
		 * @return string
		 */
		function CreateIndex($pref, $tableName, $fieldName)
		{
			$temp = (strlen($pref) > 0) ? $pref.'_' : '';
			return 'CREATE INDEX '.strtoupper($temp.$tableName.'_'.$fieldName).'_INDEX 
						ON '.$pref.$tableName.'('.$fieldName.')';
		}
		
		/**
		 * @param string $original
		 * @param string $pref
		 * @return string
		 */
		function CreateTable($original, $pref)
		{
			$pref = ($pref) ? $pref : '';
			switch ($original)
			{
				case DBTABLE_A_USERS:
					return '
CREATE TABLE `'.$pref.'a_users` (
  `id_user` int(11) NOT NULL auto_increment,
  `deleted` tinyint(1) NOT NULL default 0,
  PRIMARY KEY  (`id_user`)
)';
					break;
				case DBTABLE_AWM_ACCOUNTS:
					return '
CREATE TABLE `'.$pref.'awm_accounts` (
  `id_acct` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default 0,
  `def_acct` tinyint(1) NOT NULL default 0,
  `deleted` tinyint(1) NOT NULL default 0,
  `email` varchar(255) NOT NULL default \'\',
  `mail_protocol` tinyint(1) NOT NULL default 0,
  `mail_inc_host` varchar(255) default NULL,
  `mail_inc_login` varchar(255) default NULL,
  `mail_inc_pass` varchar(255) default NULL,
  `mail_inc_port` int(11) NOT NULL default 110,
  `mail_out_host` varchar(255) default NULL,
  `mail_out_login` varchar(255) default NULL,
  `mail_out_pass` varchar(255) default NULL,
  `mail_out_port` int(11) NOT NULL default 25,
  `mail_out_auth` tinyint(1) NOT NULL default 1,
  `friendly_nm` varchar(200) default NULL,
  `use_friendly_nm` tinyint(1) NOT NULL default 1,
  `def_order` tinyint(4) NOT NULL default 0,
  `getmail_at_login` tinyint(1) NOT NULL default 0,
  `mail_mode` tinyint(4) NOT NULL default 1,
  `mails_on_server_days` smallint(6) NOT NULL,
  `signature` text,
  `signature_type` tinyint(4) NOT NULL default 1,
  `signature_opt` tinyint(4) NOT NULL default 0,
  `delimiter` char(1) NOT NULL default \'/\',
  `mailbox_size` bigint(20) NOT NULL default 0,
  PRIMARY KEY  (`id_acct`)
)';
					break;
				case DBTABLE_AWM_ADDR_BOOK:
					return '
CREATE TABLE `'.$pref.'awm_addr_book` (
  `id_addr` bigint(20) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default 0,
  `h_email` varchar(255) default NULL,
  `fullname` varchar(255) default NULL,
  `notes` varchar(255) default NULL,
  `use_friendly_nm` tinyint(1) NOT NULL default 1,
  `h_street` varchar(255) default NULL,
  `h_city` varchar(200) default NULL,
  `h_state` varchar(200) default NULL,
  `h_zip` varchar(10) default NULL,
  `h_country` varchar(200) default NULL,
  `h_phone` varchar(50) default NULL,
  `h_fax` varchar(50) default NULL,
  `h_mobile` varchar(50) default NULL,
  `h_web` varchar(255) default NULL,
  `b_email` varchar(255) default NULL,
  `b_company` varchar(200) default NULL,
  `b_street` varchar(255) default NULL,
  `b_city` varchar(200) default NULL,
  `b_state` varchar(200) default NULL,
  `b_zip` varchar(10) default NULL,
  `b_country` varchar(200) default NULL,
  `b_job_title` varchar(100) default NULL,
  `b_department` varchar(200) default NULL,
  `b_office` varchar(200) default NULL,
  `b_phone` varchar(50) default NULL,
  `b_fax` varchar(50) default NULL,
  `b_web` varchar(255) default NULL,
  `other_email` varchar(255) default NULL,
  `primary_email` tinyint(4) default NULL,
  `id_addr_prev` bigint(20) NOT NULL default 0,
  `tmp` tinyint(1) NOT NULL default 0,
  `use_frequency` int(11) NOT NULL default 0,
  `auto_create` tinyint(1) NOT NULL default 0,
  `birthday_day` tinyint(4) NOT NULL default 0,
  `birthday_month` tinyint(4) NOT NULL default 0,
  `birthday_year` smallint(6) NOT NULL default 0,
  PRIMARY KEY  (`id_addr`)
)';
					break;
				case DBTABLE_AWM_ADDR_GROUPS:
					return '
CREATE TABLE `'.$pref.'awm_addr_groups` (
  `id_group` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default 0,
  `group_nm` varchar(255) default NULL,
  `use_frequency` int(11) NOT NULL default 0,
  `email` varchar(255) default NULL,
  `company` varchar(200) default NULL,
  `street` varchar(255) default NULL,
  `city` varchar(200) default NULL,
  `state` varchar(200) default NULL,
  `zip` varchar(10) default NULL,
  `country` varchar(200) default NULL,
  `phone` varchar(50) default NULL,
  `fax` varchar(50) default NULL,
  `web` varchar(255) default NULL,
  `organization` tinyint(1) NOT NULL default 0,
  PRIMARY KEY  (`id_group`)
)';
					break;
				case DBTABLE_AWM_ADDR_GROUPS_CONTACTS:
					return '
CREATE TABLE `'.$pref.'awm_addr_groups_contacts` (
  `id_addr` bigint(20) NOT NULL default 0,
  `id_group` int(11) NOT NULL default 0
)';
					break;
				case DBTABLE_AWM_FILTERS:
					return '
CREATE TABLE `'.$pref.'awm_filters` (
  `id_filter` int(11) NOT NULL auto_increment,
  `id_acct` int(11) NOT NULL default 0,
  `field` tinyint(4) NOT NULL default 0,
  `condition` tinyint(4) NOT NULL default 0,
  `filter` varchar(255) default NULL,
  `action` tinyint(4) NOT NULL default 0,
  `id_folder` bigint(20) NOT NULL default 0,
  PRIMARY KEY  (`id_filter`)
)';
					break;
				case DBTABLE_AWM_FOLDERS:
					return '
CREATE TABLE `'.$pref.'awm_folders` (
  `id_folder` bigint(20) NOT NULL auto_increment,
  `id_acct` int(11) NOT NULL default 0,
  `id_parent` bigint(20) NOT NULL default 0,
  `type` smallint(6) NOT NULL default 0,
  `name` varchar(100) default NULL,
  `full_path` varchar(255) default NULL,
  `sync_type` tinyint(4) NOT NULL default 0,
  `hide` tinyint(1) NOT NULL default 0,
  `fld_order` smallint(6) NOT NULL default 1,
  PRIMARY KEY  (`id_folder`)
)';
					break;
				case DBTABLE_AWM_FOLDERS_TREE:
					return '
CREATE TABLE `'.$pref.'awm_folders_tree` (
  `id` int(11) NOT NULL auto_increment,
  `id_folder` bigint(20) NOT NULL default 0,
  `id_parent` bigint(20) NOT NULL default 0,
  `folder_level` tinyint(4) NOT NULL default 0,
  PRIMARY KEY  (`id`)
)';
					break;
				case DBTABLE_AWM_MESSAGES:
					return '
CREATE TABLE `'.$pref.'awm_messages` (
  `id` bigint(20) NOT NULL auto_increment PRIMARY KEY,
  `id_msg` int(11) NOT NULL default 0,
  `id_acct` int(11) NOT NULL default 0,
  `id_folder_srv` bigint(20) NOT NULL,
  `id_folder_db` bigint(20) NOT NULL,
  `str_uid` varchar(255) default NULL,
  `int_uid` bigint(20) NOT NULL default 0,
  `from_msg` varchar(255) default NULL,
  `to_msg` varchar(255) default NULL,
  `cc_msg` varchar(255) default NULL,
  `bcc_msg` varchar(255) default NULL,
  `subject` varchar(255) default NULL,
  `msg_date` datetime default NULL,
  `attachments` tinyint(1) NOT NULL default 0,
  `size` bigint(20) NOT NULL,
  `seen` tinyint(1) NOT NULL default 1,
  `flagged` tinyint(1) NOT NULL default 0,
  `priority` tinyint(4) NOT NULL default 3,
  `downloaded` tinyint(1) NOT NULL default 1,
  `x_spam` tinyint(1) NOT NULL default 0,
  `rtl` tinyint(1) NOT NULL default 0,
  `deleted` tinyint(1) NOT NULL default 0,
  `is_full` tinyint(1) default 1,
  `replied` tinyint(1) default NULL,
  `forwarded` tinyint(1) default NULL,
  `flags` tinyint(4) default NULL,
  `body_text` longtext,
  `grayed` tinyint(1) default 0 NOT NULL,
  `charset` int(11) NOT NULL default -1
)';
					break;
					
				case DBTABLE_AWM_MESSAGES_INDEX:
					return '
CREATE INDEX '.$pref.'DBTABLE_AWM_MESSAGES_INDEX ON '.$pref.'awm_messages(id_acct, id_msg)';
					
					
				case DBTABLE_AWM_MESSAGES_BODY:
					return '
CREATE TABLE `'.$pref.'awm_messages_body` (
  `id` bigint(20) NOT NULL auto_increment PRIMARY KEY,
  `id_msg` bigint(20) NOT NULL default 0,
  `id_acct` int(11) NOT NULL default 0,
  `msg` longblob
 )';
					
				case DBTABLE_AWM_MESSAGES_BODY_INDEX:
					return '
CREATE UNIQUE INDEX '.$pref.'DBTABLE_AWM_MESSAGES_INDEX ON '.$pref.'awm_messages_body(id_acct, id_msg)';
					
					break;
				case DBTABLE_AWM_READS:
					return '
CREATE TABLE `'.$pref.'awm_reads` (
  `id_read` bigint(20) NOT NULL auto_increment,
  `id_acct` int(11) NOT NULL default 0,
  `str_uid` varchar(255) default NULL,
  `tmp` tinyint(1) NOT NULL default 0,
  PRIMARY KEY  (`id_read`)
)';
					break;
				case DBTABLE_AWM_SETTINGS:
					return '
CREATE TABLE `'.$pref.'awm_settings` (
  `id_setting` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default 0,
  `msgs_per_page` smallint(6) NOT NULL default 20,
  `white_listing` tinyint(1) NOT NULL default 0,
  `x_spam` tinyint(1) NOT NULL default 0,
  `last_login` datetime default NULL,
  `logins_count` int(11) NOT NULL default 0,
  `def_skin` varchar(255) NOT NULL default \''.DEFAULT_SKIN.'\',
  `def_lang` varchar(50) default NULL,
  `def_charset_inc` int(11) NOT NULL default 1250,
  `def_charset_out` int(11) NOT NULL default 1250,
  `def_timezone` smallint(6) NOT NULL default 0,
  `def_date_fmt` varchar(20) NOT NULL default \'MM/DD/YY\',
  `hide_folders` tinyint(1) NOT NULL default 0,
  `mailbox_limit` bigint(20) NOT NULL default 1000000000,
  `allow_change_settings` tinyint(1) NOT NULL default 1,
  `allow_dhtml_editor` tinyint(1) NOT NULL default 1,
  `allow_direct_mode` tinyint(1) NOT NULL default 1,
  `hide_contacts` tinyint(1) NOT NULL default 0,
  `db_charset` int(11) NOT NULL default 65001,
  `horiz_resizer` smallint(6) NOT NULL default 150,
  `vert_resizer` smallint(6) NOT NULL default 115,
  `mark` tinyint(4) NOT NULL default 0,
  `reply` tinyint(4) NOT NULL default 0,
  `contacts_per_page` smallint(6) NOT NULL default 20,
  `view_mode` tinyint(4) NOT NULL default 1,
  PRIMARY KEY  (`id_setting`),
  UNIQUE KEY `id_user` (`id_user`)
)';
					break;
				case DBTABLE_AWM_TEMP:
					return '
CREATE TABLE `'.$pref.'awm_temp` (
  `id_temp` bigint(20) NOT NULL auto_increment,
  `id_acct` int(11) NOT NULL default 0,
  `data_val` text,
  PRIMARY KEY  (`id_temp`)
)';
					break;
				case DBTABLE_AWM_SENDERS:
					return '
CREATE TABLE `'.$pref.'awm_senders` (
  `id` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL default 0,
  `email` varchar(255) NOT NULL,
  `safety`  tinyint(4) NOT NULL default 0,
  PRIMARY KEY  (`id`)
)';
					break;
				case DBTABLE_AWM_COLUMNS:
					return '
CREATE TABLE `'.$pref.'awm_columns` (
  `id` int(11) NOT NULL auto_increment,
  `id_column` int(11) NOT NULL default 0,
  `id_user` int(11) NOT NULL default 0,
  `column_value` int(11) NOT NULL default 0,
  PRIMARY KEY  (`id`)
)';
					break;
				// Calendar	
				case DBTABLE_CAL_USERS_DATA:
					return '
CREATE TABLE `'.$pref.'acal_users_data` (
  `settings_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default 0,
  `timeformat` tinyint(1) NOT NULL default 1,
  `dateformat` tinyint(1) NOT NULL default 1,
  `showweekends` tinyint(1) NOT NULL default 0,
  `workdaystarts` tinyint(2) NOT NULL default 0,
  `workdayends` tinyint(2) NOT NULL default 1,
  `showworkday` tinyint(1) NOT NULL default 0,
  `weekstartson` tinyint(1) NOT NULL default 0,
  `defaulttab` tinyint(1) NOT NULL default 1,
  `country` varchar(2) NOT NULL,
  `timezone` smallint(3) NULL,
  `alltimezones` tinyint(1) NOT NULL default 0,
  PRIMARY KEY  (`settings_id`)
)';
					break;
				case DBTABLE_CAL_CALENDARS:
					return '
CREATE TABLE `'.$pref.'acal_calendars` (
  `calendar_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default 0,
  `calendar_name` varchar(100) NOT NULL default \'\',
  `calendar_description` varchar(510) default NULL,
  `calendar_color` int(11) NOT NULL default 0,
  `calendar_active` tinyint(1) NOT NULL default 0,
  PRIMARY KEY  (`calendar_id`)
)';
					break;
				case DBTABLE_CAL_EVENTS:
					return '
CREATE TABLE `'.$pref.'acal_events` (
  `event_id` int(11) NOT NULL auto_increment,
  `calendar_id` int(11) NOT NULL default 0,
  `event_timefrom` datetime default NULL,
  `event_timetill` datetime default NULL, 
  `event_allday` tinyint(1) NOT NULL default 0,
  `event_name` varchar(100) NOT NULL default \'\',
  `event_text` varchar(510) default NULL,
  `event_priority` tinyint(4) NULL,
  PRIMARY KEY  (`event_id`)
)';
					break;

				default: return '';	break;
			}
			return '';
		}
		
		/**
		 * @param Filter $filter
		 * @return string
		 */
		function InsertFilter(&$filter)
		{
			$sql = 'INSERT INTO %sawm_filters (id_acct, `field`, `condition`, filter, `action`, id_folder)
					VALUES (%d, %d, %d, %s, %d, %d)';
					
			return sprintf($sql, $this->_settings->DbPrefix, $filter->IdAcct,
									$filter->Field, $filter->Condition,
									$this->_escapeString($filter->Filter),
									$filter->Action, $filter->IdFolder);
		}
		
		/**
		 * @param Filter $filter
		 * @return string
		 */
		function UpdateFilter(&$filter)
		{
			$sql = 'UPDATE %sawm_filters SET
						`field` = %d, `condition` = %d, filter = %s, `action` = %d,
						id_folder = %d
					WHERE id_filter = %d AND id_acct = %d';

			return sprintf($sql, $this->_settings->DbPrefix, $filter->Field,
									$filter->Condition,	$this->_escapeString($filter->Filter),
									$filter->Action, $filter->IdFolder,
									$filter->Id, $filter->IdAcct);			
		}
		
		/**
		 * @param string $accountId
		 * @return string
		 */
		function GetFolders($accountId)
		{

			$sql = 'SELECT p.id_folder, p.id_parent, p.type, p.name, p.full_path, p.sync_type, p.hide, p.fld_order,
							COUNT(messages.id) AS message_count, COUNT(messages_unread.seen) AS unread_message_count,
							SUM(messages.size) AS folder_size, MAX(folder_level) AS level
					FROM (%sawm_folders as n, %sawm_folders_tree as t, %sawm_folders as p)
					LEFT OUTER JOIN %sawm_messages AS messages ON p.id_folder = messages.id_folder_db
					LEFT OUTER JOIN %sawm_messages AS messages_unread ON
							p.id_folder = messages_unread.id_folder_db AND 
							messages.id = messages_unread.id AND messages_unread.seen = 0
					WHERE n.id_parent = -1
					     AND n.id_folder = t.id_parent
					     AND t.id_folder = p.id_folder
					     AND p.id_acct = %d
					GROUP BY p.id_folder
					ORDER BY p.fld_order';			
			
			return sprintf($sql, $this->_settings->DbPrefix, $this->_settings->DbPrefix, $this->_settings->DbPrefix,
									$this->_settings->DbPrefix, $this->_settings->DbPrefix, $accountId);
		}
		
		/**
		 * @param Folder $folder
		 * @return string
		 */
		function SelectSubFoldersId(&$folder)
		{
			$sql = 'SELECT c.id_folder
					FROM (%sawm_folders AS n, %sawm_folders_tree AS t, %sawm_folders AS c)
					WHERE n.id_folder = %d AND n.id_folder = t.id_parent AND t.id_folder = c.id_folder';

			return sprintf($sql, $this->_settings->DbPrefix, $this->_settings->DbPrefix, 
									$this->_settings->DbPrefix, $folder->IdDb);
		}
		
		/**
		 * @param int $pageNumber
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function LoadMessageHeaders($pageNumber, &$folder, &$account)
		{
	  		$filter = '';
	  		$asc = true;
	  		
	  		$this->_setSortOrder($account->DefaultOrder, $filter, $asc);
	  		
			//read messages from db
			$sql = 'SELECT id_msg, %s AS uid, id_folder_db, from_msg, to_msg, cc_msg,
							bcc_msg, subject, %s AS nmsg_date, size, priority, x_spam,
							attachments, seen, flagged, deleted, replied, forwarded, grayed, charset
					FROM %sawm_messages
					WHERE id_acct = %d AND id_folder_db = %d
					ORDER BY %s %s
					LIMIT %d, %d';

			return sprintf($sql, $this->_getMsgIdUidFieldName(true, $account->MailProtocol),
								CDateTime::GetMySqlDateFormat('msg_date'),
								$this->_settings->DbPrefix,
								$account->Id, $folder->IdDb,
								$filter, ($asc)?'ASC':'DESC',
								($pageNumber - 1) * $account->MailsPerPage, $account->MailsPerPage);
		}
		
		/**
		 * @param int $pageNumber
		 * @param string $condition
		 * @param FolderCollection $folders
		 * @param bool $inHeadersOnly
		 * @param Account $account
		 * @return string
		 */
		function SearchMessages($pageNumber, $condition, &$folders, $inHeadersOnly, &$account)
		{
			
			$foldersId = '';
			foreach (array_keys($folders->Instance()) as $key)
			{
				$folder = &$folders->Get($key);
				if (!$folder->Hide)
				{
					$foldersId .= ($foldersId == '')?$folder->IdDb:','.$folder->IdDb;
				}
			}
			
	  		$filter = '';
	  		$asc = true;
	  		
	  		$this->_setSortOrder($account->DefaultOrder, $filter, $asc);
			
	  		$condition = $this->_escapeString('%'.$condition.'%');
	  		
			if ($inHeadersOnly)
			{
				$sql = 'SELECT id_msg, %s AS uid, id_folder_db, from_msg, to_msg, cc_msg,
							bcc_msg, subject, %s AS nmsg_date, size, priority, x_spam,
							attachments, seen, flagged, deleted, replied, forwarded, grayed
						FROM %sawm_messages
						WHERE id_acct = %d AND id_folder_db IN (%s) AND	
							(from_msg LIKE %s OR to_msg LIKE %s OR cc_msg LIKE %s OR bcc_msg
							LIKE %s OR subject LIKE %s)
						ORDER BY %s %s
						LIMIT %d, %d';
				
				return sprintf($sql, $this->_getMsgIdUidFieldName(true, $account->MailProtocol),
									CDateTime::GetMySqlDateFormat('msg_date'),
									$this->_settings->DbPrefix,
									$account->Id, $foldersId,
									$condition, $condition, $condition, $condition, $condition,
									$filter, ($asc)?'ASC':'DESC',
									($pageNumber - 1) * $account->MailsPerPage, $account->MailsPerPage);
			}
			else
			{
				$sql = 'SELECT id_msg, %s AS uid, id_folder_db, from_msg, to_msg, cc_msg,
							bcc_msg, subject, %s AS nmsg_date, size, priority, x_spam,
							attachments, seen, flagged, deleted, replied, forwarded, grayed
						FROM %sawm_messages
						WHERE id_acct = %d AND id_folder_db IN (%s) AND	
							(from_msg LIKE %s OR to_msg LIKE %s OR cc_msg LIKE %s OR bcc_msg
							LIKE %s OR subject LIKE %s OR body_text LIKE %s)
						ORDER BY %s %s
						LIMIT %d, %d';
				
				return sprintf($sql, $this->_getMsgIdUidFieldName(true, $account->MailProtocol),
									CDateTime::GetMySqlDateFormat('msg_date'),
									$this->_settings->DbPrefix,
									$account->Id, $foldersId,
									$condition, $condition, $condition, $condition, $condition, $condition,
									$filter, ($asc)?'ASC':'DESC',
									($pageNumber - 1) * $account->MailsPerPage, $account->MailsPerPage);
			}
		}
		
		/**
		 * @param int $accountId
		 * @return string
		 */
		function SelectFilters($accountId)
		{
			$sql = 'SELECT `id_filter`, `field`, `condition`, `filter`, `action`, `id_folder`
					FROM `%sawm_filters`
					WHERE `id_acct` = %d
					ORDER BY `action`';
			return sprintf($sql, $this->_settings->DbPrefix, $accountId);
		}
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function DeleteMessagesBody(&$messageIndexSet, $indexAsUid, &$folder, &$account)
		{
			$sql = 'DELETE %sawm_messages_body
					FROM %sawm_messages_body, %sawm_messages
					WHERE %sawm_messages.id_acct = %d AND %sawm_messages.id_folder_db = %d 
							AND %sawm_messages_body.id_acct = %sawm_messages.id_acct 
							AND %sawm_messages_body.id_msg = %sawm_messages.id_msg 
							AND %sawm_messages.%s IN (%s)';
			
			return sprintf($sql, $this->_settings->DbPrefix, $this->_settings->DbPrefix,
								 $this->_settings->DbPrefix, $this->_settings->DbPrefix,
								$account->Id, $this->_settings->DbPrefix, $folder->IdDb,
								$this->_settings->DbPrefix, $this->_settings->DbPrefix,
								$this->_settings->DbPrefix, $this->_settings->DbPrefix,
								$this->_settings->DbPrefix,
								$this->_getMsgIdUidFieldName($indexAsUid, $account->MailProtocol),
								$this->_quoteUids($messageIndexSet, $indexAsUid, $account->MailProtocol));
		}
		
		/**
		 * @param int $pageNumber
		 * @param short $sortField
		 * @param bool $sortOrder
		 * @param Account $account
		 */
		function LoadContactsAndGroups($pageNumber, $sortField, $sortOrder, &$account)
		{
			switch ($sortField)
			{
				default:
				case 0:
					$filter = 'is_group';
					break;
				case 1:
					$filter = 'name';
					break;
				case 2:
					$filter = 'email';
					break;
				case 3:
					$filter = 'frequency';
					break;
			}
			
			$order = ($sortOrder)?'DESC':'ASC';
			
			$sql = 'SELECT id_addr AS id, fullname AS name, 
						CASE primary_email
							WHEN %s THEN h_email
							WHEN %s THEN b_email
							WHEN %s THEN other_email
						END AS email, 0 AS is_group, use_frequency AS frequency, use_friendly_nm AS usefriendlyname
					FROM %sawm_addr_book
					WHERE id_user = %d
					UNION
					SELECT id_group AS id, group_nm AS name, \'\' AS email, 1 AS is_group, use_frequency AS frequency, 1 AS usefriendlyname
					FROM %sawm_addr_groups
					WHERE id_user = %d
					ORDER BY %s %s, name %s
					LIMIT %d, %d';
			
			return sprintf($sql, PRIMARYEMAIL_Home, PRIMARYEMAIL_Business, PRIMARYEMAIL_Other,
								$this->_settings->DbPrefix, $account->IdUser,
								$this->_settings->DbPrefix, $account->IdUser,
							$filter, $order, $order,
							($pageNumber - 1) * $account->ContactsPerPage, $account->ContactsPerPage);
		}
		
		/**
		 * @param int $pageNumber
		 * @param string $conditon
		 * @param int $groupId
		 * @param short $sortField
		 * @param bool $sortOrder
		 * @param Account $account
		 */
		function SearchContactsAndGroups($pageNumber, $condition, $groupId, $sortField, $sortOrder, &$account, $lookForType)
		{
			switch ($sortField)
			{
				default:	
				case 0:
					$filter = 'is_group';
					break;
				case 1:
					$filter = 'name';
					break;
				case 2:
					$filter = 'email';
					break;
				case 3:
					$filter = 'frequency';
					break;
			}
			
	  		$condition = ($lookForType == 1) ? $this->_escapeString($condition.'%') : $this->_escapeString('%'.$condition.'%');
			
			$order = ($sortOrder)?'DESC':'ASC';
			
			$contactsResultCount = ($lookForType == 1) ? SUGGESTCONTACTS : $account->ContactsPerPage;
			
			if ($groupId == -1)
			{
				$sql = 'SELECT id_addr AS id, fullname AS name,
							CASE primary_email
								WHEN %s THEN h_email
								WHEN %s THEN b_email
								WHEN %s THEN other_email
							END AS email, 0 AS is_group, use_frequency AS frequency, use_friendly_nm AS usefriendlyname
						FROM %sawm_addr_book
						WHERE id_user = %d AND (fullname LIKE %s OR h_email LIKE %s OR b_email LIKE %s OR other_email LIKE %s)
						UNION
						SELECT id_group AS id, group_nm AS name, \'\' AS email, 1 AS is_group, use_frequency AS frequency, 1 AS usefriendlyname
						FROM %sawm_addr_groups
						WHERE id_user = %d AND group_nm LIKE %s
						ORDER BY %s %s, name %s
						LIMIT %d, %d';
				
				return sprintf($sql, PRIMARYEMAIL_Home, PRIMARYEMAIL_Business, PRIMARYEMAIL_Other,
									$this->_settings->DbPrefix, $account->IdUser, $condition, $condition, $condition, $condition,
									$this->_settings->DbPrefix, $account->IdUser, $condition,
								$filter, $order, $order,
								($pageNumber - 1) * $contactsResultCount, $contactsResultCount);
			}
			else
			{
				$sql = 'SELECT book.id_addr AS id, fullname AS name,
							CASE primary_email
								WHEN %s THEN h_email
								WHEN %s THEN b_email
								WHEN %s THEN other_email
							END AS email, 0 AS is_group, use_frequency AS frequency, use_friendly_nm AS usefriendlyname
						FROM %sawm_addr_book AS book
						INNER JOIN %sawm_addr_groups_contacts AS gr_cont ON gr_cont.id_addr = book.id_addr AND
								id_group = %d
						WHERE id_user = %d AND (fullname LIKE %s OR h_email LIKE %s OR b_email LIKE %s OR other_email LIKE %s)
						ORDER BY %s %s, name %s
						LIMIT %d, %d';
				
				return sprintf($sql, PRIMARYEMAIL_Home, PRIMARYEMAIL_Business, PRIMARYEMAIL_Other,
									$this->_settings->DbPrefix, $this->_settings->DbPrefix,
									$groupId, $account->IdUser, $condition, $condition, $condition, $condition,
								$filter, $order, $order,
								($pageNumber - 1) * $contactsResultCount, $contactsResultCount);
				
			}
		}

		/**
		 * @param int $id
		 * @return string
		 */
		function DeleteFolderTreeById($id)
		{
			$sql = 'DELETE %1$sawm_folders_tree 
						FROM %1$sawm_folders, %1$sawm_folders_tree
						WHERE %1$sawm_folders.id_folder = %1$sawm_folders_tree.id_folder 
						AND %1$sawm_folders.id_acct = %2$d';

			return sprintf($sql, $this->_settings->DbPrefix, $id);
		}
		
		/**
		 * @param int $id
		 * @return string
		 */
		function DeleteAddrGroupsContactsById($id)
		{
			$sql = 'DELETE %1$sawm_addr_groups_contacts
						FROM %1$sawm_addr_groups_contacts, %1$sawm_addr_groups
						WHERE %1$sawm_addr_groups_contacts.id_group = %1$sawm_addr_groups.id_group 
						AND %1$sawm_addr_groups.id_user = %2$d';

			return sprintf($sql, $this->_settings->DbPrefix, $id);
		}
		
		/**
		 * @param int $id
		 * @return string
		 */
		function DeleteCalendarEvents($id)
		{
			$sql = 'DELETE %1$sacal_events
						FROM %1$sacal_events, %1$sacal_calendars
						WHERE %1$sacal_events.calendar_id = %1$sacal_calendars.calendar_id
						AND %1$sacal_calendars.user_id = %2$d';

			return sprintf($sql, $this->_settings->DbPrefix, $id);
		}
		
		/**
		 * @return string
		 */
		function SelectAccountsCount($searchText)
		{
			if (strlen($searchText) > 0)
			{
				$sql = 'SELECT id_acct, email, mail_inc_host, mail_out_host, %s AS nlast_login, logins_count,
						mailbox_size, mailbox_limit
					FROM %3$sawm_accounts AS acct
					INNER JOIN %3$sawm_settings AS sett ON acct.id_user = sett.id_user
					WHERE (id_acct LIKE %1$s OR email LIKE %1$s OR %2$s LIKE %1$s
							OR logins_count LIKE %1$s OR mail_inc_host LIKE %1$s
							OR mail_out_host LIKE %1$s)';
				$sql = sprintf($sql, $this->_escapeString('%'.$searchText.'%'),
							CDateTime::GetMySqlDateFormat('last_login'),
							$this->_settings->DbPrefix);
			}
			else 
			{
				$sql = sprintf('SELECT id_acct FROM %sawm_accounts', $this->_settings->DbPrefix);
			}
			
			
			return $sql;
		}
		
		/**
		 * @return string
		 */
		function SelectAllAccounts($pageNumber, $accountPerPage, $sortField, $sortOrder, $searchText)
		{
			$nom = ($pageNumber > 0) ? ($pageNumber - 1) * $accountPerPage : 0;
			$search = '';
			$searchText = trim($searchText);
			
			if (strlen($searchText) > 0)
			{
				$search = ' WHERE (id_acct LIKE %1$s OR email LIKE %1$s OR %2$s LIKE %1$s
							OR logins_count LIKE %1$s OR mail_inc_host LIKE %1$s
							OR mail_out_host LIKE %1$s) ';
				
				$search = sprintf($search, $this->_escapeString('%'.$searchText.'%'),
							CDateTime::GetMySqlDateFormat('last_login'));
			}
			
			$sql = 'SELECT id_acct, acct.id_user as id_user, deleted, email,
						mail_inc_host, mail_out_host, %s AS nlast_login, logins_count,
						mailbox_size, mailbox_limit, def_acct
					FROM %sawm_accounts AS acct
					INNER JOIN %sawm_settings AS sett ON acct.id_user = sett.id_user
					%s
					ORDER BY %s %s, def_acct DESC
					LIMIT %s, %s';
					
			return sprintf($sql, CDateTime::GetMySqlDateFormat('last_login'), 
					$this->_settings->DbPrefix, $this->_settings->DbPrefix, $search, 
					$sortField, ($sortOrder)?'DESC':'ASC', $nom, $accountPerPage);		
		}

		/**
		 * @param Account $account
		 * @return string
		 */
		function SelectExpiredMessageUids(&$account)
		{
			$sql = 'SELECT str_uid FROM %sawm_messages
					WHERE id_acct = %d AND DATE_ADD(msg_date, INTERVAL %d DAY) < CURDATE()';
			
			return sprintf($sql, $this->_settings->DbPrefix, $account->Id, $account->MailsOnServerDays);
		}
		
	}

	class MsSqlCommandCreator extends CommandCreator
	{
		function MsSqlCommandCreator()
		{
			CommandCreator::CommandCreator(QUOTE_DOUBLE);
		}
		
		/**
		 * @return string
		 */
		function AllTableNames()
		{
			return 'SELECT [name] AS tableNames FROM sysobjects o WHERE xtype = \'U\' AND OBJECTPROPERTY(o.id, N\'IsMSShipped\')!=1';
		}

		/**
		 * @return string
		 */
		function GetIndexsOfTable($pref, $tableName)
		{
			return 'sp_helpindex \''.$pref.$tableName.'\'';	
		}
		
		/**
		 * @param string $pref
		 * @param string $tableName
		 * @return string
		 */
		function GetTablesColumns($pref, $tableName)
		{
			return 'SELECT INFORMATION_SCHEMA.COLUMNS.COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
						WHERE TABLE_NAME=\''.$pref.$tableName.'\'';
		}
		
		/**
		 * @param string $fieldName
		 * @return string
		 */
		function GetDateFormat($fieldName)
		{
			return CDateTime::GetMsSqlDateFormat($fieldName);
		}
		
		/**
		 * @param string $pref
		 * @param string $tableName
		 * @param string $fieldName
		 * @return string
		 */
		function CreateIndex($pref, $tableName, $fieldName)
		{
			$temp = (strlen($pref) > 0) ? $pref.'_' : '';
			return 'CREATE INDEX ['.strtoupper($temp.$tableName.'_'.$fieldName).'_INDEX] 
						ON ['.$pref.$tableName.'](['.$fieldName.'])';
		}
		
		/**
		 * @param string $original
		 * @param string $pref
		 * @return string
		 */
		function CreateTable($original, $pref)
		{
			$pref = ($pref) ? $pref : '';
			switch ($original)
			{
				case DBTABLE_A_USERS:
					return '
CREATE TABLE ['.$pref.'a_users] (
	[id_user] [int] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[deleted] [bit] NOT NULL DEFAULT (0)
) ON [PRIMARY]';
					break;
				case DBTABLE_AWM_ACCOUNTS:
					return '
CREATE TABLE ['.$pref.'awm_accounts] (
	[id_acct] [int] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[id_user] [int] NOT NULL DEFAULT (0),
	[def_acct] [bit] NOT NULL DEFAULT (0),
	[deleted] [bit] NOT NULL DEFAULT (0),
	[email] [varchar] (255)  NOT NULL DEFAULT (\'\'),
	[mail_protocol] [smallint] NOT NULL DEFAULT (0),
	[mail_inc_host] [varchar] (255)  NULL ,
	[mail_inc_login] [varchar] (255)  NULL ,
	[mail_inc_pass] [varchar] (255)  NULL ,
	[mail_inc_port] [int] NOT NULL DEFAULT (110),
	[mail_out_host] [varchar] (255)  NULL ,
	[mail_out_login] [varchar] (255)  NULL ,
	[mail_out_pass] [varchar] (255)  NULL ,
	[mail_out_port] [int] NOT NULL DEFAULT (25),
	[mail_out_auth] [bit] NOT NULL DEFAULT (1),
	[friendly_nm] [varchar] (200)  NULL,
	[use_friendly_nm] [bit] NOT NULL DEFAULT (1),
	[def_order] [tinyint] NOT NULL DEFAULT (0),
	[getmail_at_login] [bit] NOT NULL DEFAULT (0),
	[mail_mode] [tinyint] NOT NULL DEFAULT (1),
	[mails_on_server_days] [smallint] NOT NULL ,
	[signature] [text]  NULL ,
	[signature_type] [tinyint] NOT NULL DEFAULT (1),
	[signature_opt] [tinyint] NOT NULL DEFAULT (0),
	[delimiter] [char] (1)  NOT NULL DEFAULT (\'/\'),
	[mailbox_size] [bigint] NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]';
					break;
				case DBTABLE_AWM_ADDR_BOOK:
					return '
CREATE TABLE ['.$pref.'awm_addr_book] (
	[id_addr] [bigint] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[id_user] [int] NOT NULL DEFAULT (0),
	[h_email] [varchar] (255)  NULL ,
	[fullname] [varchar] (255)  NULL ,
	[notes] [varchar] (255)  NULL ,
	[use_friendly_nm] [bit] NOT NULL DEFAULT (1),
	[h_street] [varchar] (255)  NULL ,
	[h_city] [varchar] (200)  NULL ,
	[h_state] [varchar] (200)  NULL ,
	[h_zip] [varchar] (10)  NULL ,
	[h_country] [varchar] (200)  NULL ,
	[h_phone] [varchar] (50)  NULL ,
	[h_fax] [varchar] (50)  NULL ,
	[h_mobile] [varchar] (50)  NULL ,
	[h_web] [varchar] (255)  NULL ,
	[b_email] [varchar] (255)  NULL ,
	[b_company] [varchar] (200)  NULL ,
	[b_street] [varchar] (255)  NULL ,
	[b_city] [varchar] (200)  NULL ,
	[b_state] [varchar] (200)  NULL ,
	[b_zip] [varchar] (10)  NULL ,
	[b_country] [varchar] (200)  NULL ,
	[b_job_title] [varchar] (100)  NULL ,
	[b_department] [varchar] (200)  NULL ,
	[b_office] [varchar] (200)  NULL ,
	[b_phone] [varchar] (50)  NULL ,
	[b_fax] [varchar] (50)  NULL ,
	[b_web] [varchar] (255)  NULL ,
	[other_email] [varchar] (255)  NULL ,
	[primary_email] [tinyint] NULL ,
	[id_addr_prev] [bigint] NOT NULL DEFAULT (0),
	[tmp] [bit] NOT NULL DEFAULT (0),
	[use_frequency] [int] NOT NULL DEFAULT (0),
	[auto_create] [bit] NOT NULL DEFAULT (0),
	[birthday_day] [tinyint] NOT NULL DEFAULT (0),
	[birthday_month] [tinyint] NOT NULL DEFAULT (0),
	[birthday_year] [smallint] NOT NULL DEFAULT (0)
) ON [PRIMARY]';
					break;
				case DBTABLE_AWM_ADDR_GROUPS:
					return '
CREATE TABLE ['.$pref.'awm_addr_groups] (
	[id_group] [int] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[id_user] [int] NOT NULL DEFAULT (0),
	[group_nm] [varchar] (255)  NULL,
	[use_frequency] [int] NOT NULL DEFAULT (0),
	
	[email] [varchar] (255) NULL,
	[company] [varchar] (200) NULL,
	[street] [varchar] (255) NULL,
	[city] [varchar] (200) NULL,
	[state] [varchar] (200) NULL,
	[zip] [varchar] (10) NULL,
	[country] [varchar] (200) NULL,
	[phone] [varchar] (50) NULL,
	[fax] [varchar] (50) NULL,
	[web] [varchar] (255) NULL,
	[organization] [bit] NOT NULL DEFAULT (0)
) ON [PRIMARY]';
					break;
				case DBTABLE_AWM_ADDR_GROUPS_CONTACTS:
					return '
CREATE TABLE ['.$pref.'awm_addr_groups_contacts] (
	[id_addr] [bigint] NOT NULL,
	[id_group] [int] NOT NULL
) ON [PRIMARY]';
					break;
				case DBTABLE_AWM_FILTERS:
					return '
CREATE TABLE ['.$pref.'awm_filters] (
	[id_filter] [int] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[id_acct] [int] NOT NULL DEFAULT (0),
	[field] [tinyint] NOT NULL DEFAULT (0),
	[condition] [tinyint] NOT NULL DEFAULT (0),
	[filter] [varchar] (255)  NULL ,
	[action] [tinyint] NOT NULL DEFAULT (0),
	[id_folder] [bigint] NOT NULL DEFAULT (0)
) ON [PRIMARY]';
					break;
				case DBTABLE_AWM_FOLDERS:
					return '
CREATE TABLE ['.$pref.'awm_folders] (
	[id_folder] [bigint] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[id_acct] [int] NOT NULL DEFAULT (0),
	[id_parent] [bigint] NOT NULL DEFAULT ((-1)),
	[type] [smallint] NOT NULL DEFAULT (0),
	[name] [varchar] (100)  NULL ,
	[full_path] [varchar] (255)  NULL ,
	[sync_type] [tinyint] NOT NULL DEFAULT (0),
	[hide] [bit] NOT NULL DEFAULT (0),
	[fld_order] [smallint] NOT NULL DEFAULT (0)
) ON [PRIMARY]';
					break;
				case DBTABLE_AWM_FOLDERS_TREE:
					return '
CREATE TABLE ['.$pref.'awm_folders_tree] (
	[id] [bigint] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[id_folder] [bigint] NOT NULL DEFAULT (0),
	[id_parent] [bigint] NOT NULL DEFAULT (0),
	[folder_level] [tinyint] NOT NULL DEFAULT (0)
) ON [PRIMARY]';
					break;
				case DBTABLE_AWM_MESSAGES:
					return '
CREATE TABLE ['.$pref.'awm_messages] (
	[id] [bigint] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[id_msg] [int] NOT NULL DEFAULT (0),
	[id_acct] [int] NOT NULL DEFAULT (0),
	[id_folder_srv] [bigint] NOT NULL DEFAULT (0),
	[id_folder_db] [bigint] NOT NULL DEFAULT (0),
	[str_uid] [varchar] (255) NULL ,
	[int_uid] [bigint] NOT NULL DEFAULT (0),
	[from_msg] [varchar] (255) NULL ,
	[to_msg] [varchar] (255) NULL ,
	[cc_msg] [varchar] (255) NULL ,
	[bcc_msg] [varchar] (255) NULL ,
	[subject] [varchar] (255) NULL ,
	[msg_date] [datetime] NULL ,
	[attachments] [bit] NOT NULL DEFAULT (0),
	[size] [bigint] NOT NULL ,
	[seen] [bit] NOT NULL DEFAULT (1),
	[flagged] [bit] NOT NULL DEFAULT (0),
	[priority] [tinyint] NOT NULL DEFAULT (3),
	[downloaded] [bit] NOT NULL DEFAULT (1),
	[x_spam] [bit] NOT NULL DEFAULT (0),
	[rtl] [bit] NOT NULL DEFAULT (0),
	[deleted] [bit] NOT NULL DEFAULT (0),
	[is_full] [bit] NULL DEFAULT (1),
	[replied] [bit] NULL ,
	[forwarded] [bit] NULL ,
	[flags] [tinyint] NULL ,
	[body_text] [text] NULL ,
	[grayed] [bit] NOT NULL DEFAULT (0),
	[charset] [int] NOT NULL DEFAULT ((-1))
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]';
					break;
					
				case DBTABLE_AWM_MESSAGES_INDEX:
					return '
CREATE INDEX ['.$pref.'DBTABLE_AWM_MESSAGES_INDEX] ON ['.$pref.'awm_messages]([id_acct], [id_msg]) ON [PRIMARY]';
					
				case DBTABLE_AWM_MESSAGES_BODY:
					return '
CREATE TABLE ['.$pref.'awm_messages_body] (
	[id] [bigint] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[id_acct] [int] NOT NULL DEFAULT (0),
	[id_msg] [int] NOT NULL DEFAULT (0),
	[msg] [image] NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]';
					break;
					
				case DBTABLE_AWM_MESSAGES_BODY_INDEX:
					return '
CREATE UNIQUE INDEX ['.$pref.'DBTABLE_AWM_MESSAGES_INDEX] ON ['.$pref.'awm_messages_body]([id_acct], [id_msg]) ON [PRIMARY]';
					break;

				case DBTABLE_AWM_READS:
					return '
CREATE TABLE ['.$pref.'awm_reads] (
	[id_read] [bigint] PRIMARY KEY IDENTITY (1, 1) NOT NULL,
	[id_acct] [int] NOT NULL DEFAULT (0),
	[str_uid] [varchar] (255) NOT NULL DEFAULT (\'\'),
	[tmp] [bit] NOT NULL DEFAULT (0)
) ON [PRIMARY]';
					break;
					
				case DBTABLE_AWM_SETTINGS:
					return '
CREATE TABLE ['.$pref.'awm_settings] (
	[id_setting] [int] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[id_user] [int] NOT NULL DEFAULT (0),
	[msgs_per_page] [smallint] NOT NULL DEFAULT (20),
	[white_listing] [bit] NOT NULL DEFAULT (0),
	[x_spam] [bit] NOT NULL DEFAULT (0),
	[last_login] [datetime] NULL ,
	[logins_count] [int] NOT NULL DEFAULT (0),
	[def_skin] [varchar] (255) NOT NULL DEFAULT (\''.DEFAULT_SKIN.'\'),
	[def_lang] [varchar] (50) NULL ,
	[def_charset_inc] [int] NULL ,
	[def_charset_out] [int] NULL ,
	[def_timezone] [smallint] NOT NULL DEFAULT (0),
	[def_date_fmt] [varchar] (20) NOT NULL DEFAULT (\'MM/DD/YY\'),
	[hide_folders] [bit] NOT NULL DEFAULT (0),
	[mailbox_limit] [bigint] NOT NULL DEFAULT (10000000),
	[allow_change_settings] [bit] NOT NULL DEFAULT (1),
	[allow_dhtml_editor] [bit] NOT NULL DEFAULT (1),
	[allow_direct_mode] [bit] NOT NULL DEFAULT (1),
	[hide_contacts] [bit] NOT NULL DEFAULT (0),
	[db_charset] [int] NOT NULL DEFAULT (65001),
	[horiz_resizer] [smallint] NOT NULL DEFAULT (150),
	[vert_resizer] [smallint] NULL DEFAULT (115),
	[mark] [tinyint] NOT NULL DEFAULT (0),
	[reply] [tinyint] NOT NULL DEFAULT (0),
	[contacts_per_page] [smallint] NOT NULL DEFAULT (20),
	[view_mode] [tinyint] NOT NULL DEFAULT (1)
) ON [PRIMARY]';
					break;
				case DBTABLE_AWM_TEMP:
					return '
CREATE TABLE ['.$pref.'awm_temp] (
	[id_temp] [bigint] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[id_acct] [int] NOT NULL DEFAULT (0),
	[data_val] [text]  NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]';
					break;
					
				case DBTABLE_AWM_SENDERS:
					return '
CREATE TABLE ['.$pref.'awm_senders] (
	[id] [int] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[id_user] [int] NOT NULL DEFAULT (0),
	[email] [varchar] (255) NOT NULL DEFAULT (\'\'),
	[safety] [tinyint] NOT NULL DEFAULT (0)
) ON [PRIMARY]';
					break;
					
				case DBTABLE_AWM_COLUMNS:
					return '
CREATE TABLE ['.$pref.'awm_columns] (
	[id] [int] PRIMARY KEY IDENTITY (1, 1) NOT NULL ,
	[id_user] [int] NOT NULL DEFAULT (0),
	[id_column] [int] NOT NULL DEFAULT (0),
	[column_value] [int] NOT NULL DEFAULT (0)
) ON [PRIMARY]';
					break;

				// Calendar	
				case DBTABLE_CAL_USERS_DATA:
					return '
CREATE TABLE ['.$pref.'acal_users_data] (
	[settings_id] [int] PRIMARY KEY IDENTITY (1, 1) NOT NULL,
	[user_id] [int] NOT NULL DEFAULT (0),
	[timeformat] [tinyint] NOT NULL DEFAULT (1),
	[dateformat] [tinyint] NOT NULL DEFAULT (1),
	[showweekends]  [tinyint] NOT NULL DEFAULT (0),
	[workdaystarts]  [tinyint] NOT NULL DEFAULT (0),
	[workdayends] [tinyint] NOT NULL DEFAULT (1),
	[showworkday] [tinyint] NOT NULL DEFAULT (0),
	[weekstartson] [tinyint] NOT NULL default (0),
	[defaulttab] [tinyint] NOT NULL DEFAULT (1),
	[country] [varchar] (2) NULL,
	[timezone] [smallint] NULL,
	[alltimezones] [tinyint] NOT NULL DEFAULT (0) 
) ON [PRIMARY]';
	
					break;
				case DBTABLE_CAL_CALENDARS:
					return '
CREATE TABLE ['.$pref.'acal_calendars] (
	[calendar_id] [int] PRIMARY KEY IDENTITY (1, 1) NOT NULL,
	[user_id] [int] NOT NULL DEFAULT (0),
	[calendar_name] [varchar] (100)  NOT NULL DEFAULT (\'\'),
	[calendar_description] [varchar] (510) NOT NULL DEFAULT (\'\'),
	[calendar_color] [int] NOT NULL DEFAULT (0),
	[calendar_active] [bit] NOT NULL DEFAULT (0)
) ON [PRIMARY]';
					break;
				case DBTABLE_CAL_EVENTS:
					return '
CREATE TABLE ['.$pref.'acal_events] (
	[event_id] [int] PRIMARY KEY IDENTITY(1, 1) NOT NULL,
	[calendar_id] [int] NOT NULL DEFAULT (0),
	[event_timefrom] [datetime] NOT NULL,
	[event_timetill] [datetime] NOT NULL,
	[event_allday] [bit] NOT NULL DEFAULT (0),
	[event_name] [varchar] (100) NOT NULL DEFAULT (\'\'),
	[event_text] [varchar] (510) NULL,
	[event_priority] [tinyint] NULL DEFAULT (0)
) ON [PRIMARY]';
					break;
					
				default: return '';	break;
			}
			return '';
		}
		
		/**
		 * @param int $pageNumber
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function LoadMessageHeaders($pageNumber, &$folder, &$account)
		{
	  		$filter = '';
	  		$tempstr = '';
	  		$asc = true;
	  		
	  		$this->_setSortOrder($account->DefaultOrder, $filter, $asc);
	  		
	  		if (($pageNumber - 1) * $account->MailsPerPage > 0)
	  		{
	  			$tempstr = ' AND id_msg NOT IN 
						(SELECT TOP %d id_msg FROM %sawm_messages WHERE id_acct = %d AND id_folder_db = %d ORDER BY %s %s)';
	  			
				$tempstr = sprintf($tempstr,
					($pageNumber - 1) * $account->MailsPerPage, 
					$this->_settings->DbPrefix,
					$account->Id, $folder->IdDb,
					$filter, ($asc)?'ASC':'DESC');
	  		}
	  		
			//read messages from db
			$sql = 'SELECT TOP %d id_msg, %s AS uid, id_folder_db, from_msg, to_msg, cc_msg,
							bcc_msg, subject, %s AS nmsg_date, size, priority, x_spam,
							attachments, seen, flagged, deleted, replied, forwarded, grayed, charset
					FROM %sawm_messages
					WHERE id_acct = %d AND id_folder_db = %d%s
					ORDER BY %s %s';
			
			return sprintf($sql,  $account->MailsPerPage, $this->_getMsgIdUidFieldName(true, $account->MailProtocol),
					CDateTime::GetMsSqlDateFormat('msg_date'),
					$this->_settings->DbPrefix,
					$account->Id, $folder->IdDb,
					$tempstr,
					$filter, ($asc)?'ASC':'DESC');
		}
		
		/**
		 * @param int $pageNumber
		 * @param string $condition
		 * @param FolderCollection $folders
		 * @param bool $inHeadersOnly
		 * @param Account $account
		 * @return WebMailMessageCollection
		 */
		function SearchMessages($pageNumber, $condition, &$folders, $inHeadersOnly, &$account)
		{
			$tempstr = '';
			$foldersId = '';
			foreach (array_keys($folders->Instance()) as $key)
			{
				$folder = &$folders->Get($key);
				$foldersId .= ($foldersId == '')?$folder->IdDb:','.$folder->IdDb;
			}
			
	  		$filter = '';
	  		$asc = true;
	  		
	  		$this->_setSortOrder($account->DefaultOrder, $filter, $asc);
			
	  		$condition = str_replace('[', '[[]', $condition);
	  		
	  		$condition = $this->_escapeString('%'.$condition.'%');

	  		if ($inHeadersOnly)
			{
				if (($pageNumber - 1) * $account->MailsPerPage > 0)
		  		{
		  			$tempstr = ' AND id_msg NOT IN 
								(SELECT TOP %d id_msg FROM %sawm_messages 
								WHERE id_acct = %d AND id_folder_db IN (%s) AND	
								(from_msg LIKE %s OR to_msg LIKE %s OR cc_msg LIKE %s OR bcc_msg
								LIKE %s OR subject LIKE %s) ORDER BY %s %s)';
		  			
					$tempstr = sprintf($tempstr,
									($pageNumber - 1) * $account->MailsPerPage,
									$this->_settings->DbPrefix,
									$account->Id, $foldersId,
									$condition, $condition, $condition, $condition, $condition,
									$filter, ($asc)?'ASC':'DESC');
		  		}
				$sql = 'SELECT TOP %d id_msg, %s AS uid, id_folder_db, from_msg, to_msg, cc_msg,
							bcc_msg, subject, %s AS nmsg_date, size, priority, x_spam,
							attachments, seen, flagged, deleted, replied, forwarded, grayed
						FROM %sawm_messages
						WHERE id_acct = %d AND id_folder_db IN (%s) AND	
							(from_msg LIKE %s OR to_msg LIKE %s OR cc_msg LIKE %s OR bcc_msg
							LIKE %s OR subject LIKE %s)%s
						ORDER BY %s %s';
				
				return sprintf($sql, $account->MailsPerPage, $this->_getMsgIdUidFieldName(true, $account->MailProtocol),
									CDateTime::GetMsSqlDateFormat('msg_date'),
									$this->_settings->DbPrefix,
									$account->Id, $foldersId,
									$condition, $condition, $condition, $condition, $condition,
									$tempstr,
									$filter, ($asc)?'ASC':'DESC');
			}
			else
			{
				if (($pageNumber - 1) * $account->MailsPerPage > 0)
		  		{
		  			$tempstr = ' AND id_msg NOT IN 
							(SELECT TOP %d id_msg FROM %sawm_messages
							WHERE id_acct = %d AND id_folder_db IN (%s) AND	
								(from_msg LIKE %s OR to_msg LIKE %s OR cc_msg LIKE %s OR bcc_msg
								LIKE %s OR subject LIKE %s OR body_text LIKE %s) ORDER BY %s %s)';
		  			
					$tempstr = sprintf($tempstr,
									($pageNumber - 1) * $account->MailsPerPage,
									$this->_settings->DbPrefix,
									$account->Id, $foldersId,
									$condition, $condition, $condition, $condition, $condition, $condition,
									$filter, ($asc)?'ASC':'DESC');
		  		}				

				$sql = 'SELECT TOP %d id_msg, %s AS uid, id_folder_db, from_msg, to_msg, cc_msg,
							bcc_msg, subject, %s AS nmsg_date, size, priority, x_spam,
							attachments, seen, flagged, deleted, replied, forwarded, grayed
						FROM %sawm_messages
						WHERE id_acct = %d AND id_folder_db IN (%s) AND	
							(from_msg LIKE %s OR to_msg LIKE %s OR cc_msg LIKE %s OR bcc_msg
							LIKE %s OR subject LIKE %s OR body_text LIKE %s)%s
						ORDER BY %s %s';
				
				return sprintf($sql, $account->MailsPerPage, $this->_getMsgIdUidFieldName(true, $account->MailProtocol),
									CDateTime::GetMsSqlDateFormat('msg_date'),
									$this->_settings->DbPrefix,
									$account->Id, $foldersId,
									$condition, $condition, $condition, $condition, $condition, $condition,
									$tempstr,
									$filter, ($asc)?'ASC':'DESC');
			}
		}
		
		/**
		 * @param string $accountId
		 * @return string
		 */
		function GetFolders($accountId)
		{
			
			$sql = 'SELECT p.id_folder, p.id_parent, p.type, p.name, p.full_path, p.sync_type, p.hide, p.fld_order,
							COUNT(messages.id) AS message_count, COUNT(messages_unread.seen) AS unread_message_count,
							SUM(messages.size) AS folder_size, MAX(folder_level) AS level
					FROM %sawm_folders as n, %sawm_folders_tree as t, %sawm_folders as p
					LEFT OUTER JOIN %sawm_messages AS messages ON p.id_folder = messages.id_folder_db
					LEFT OUTER JOIN %sawm_messages AS messages_unread ON
							p.id_folder = messages_unread.id_folder_db AND 
							messages.id = messages_unread.id AND messages_unread.seen = 0
					WHERE n.id_parent = -1
					     AND n.id_folder = t.id_parent
					     AND t.id_folder = p.id_folder
					     AND p.id_acct = %d
					GROUP BY p.id_folder, p.id_parent, p.type, p.name, p.full_path, p.sync_type, p.hide, p.fld_order
					ORDER BY p.fld_order';			
			
			return sprintf($sql, $this->_settings->DbPrefix, $this->_settings->DbPrefix, $this->_settings->DbPrefix,
									$this->_settings->DbPrefix,	$this->_settings->DbPrefix, $accountId);
		}
		
		/**
		 * @param Folder $folder
		 * @return string
		 */
		function SelectSubFoldersId(&$folder)
		{
			$sql = 'SELECT c.id_folder
					FROM %sawm_folders AS n, %sawm_folders_tree AS t, %sawm_folders AS c
					WHERE n.id_folder = %d AND n.id_folder = t.id_parent AND t.id_folder = c.id_folder';

			return sprintf($sql, $this->_settings->DbPrefix, $this->_settings->DbPrefix, 
									$this->_settings->DbPrefix, $folder->IdDb);
		}
		
		/**
		 * @param Folder $folder
		 * @param Array $foldersId
		 * @param string $newName
		 * @return string
		 */
		function RenameSubFoldersPath(&$folder, &$foldersId, $newSubPath)
		{
			$sql = 'UPDATE %sawm_folders
					SET full_path = \'%s\' + SUBSTRING(full_path, %d, LEN(full_path)-%d+1)
					WHERE id_acct = %d AND id_folder IN (%s) AND id_folder <> %d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $newSubPath, strlen($folder->FullName)+1,
					strlen($folder->FullName)+1, $folder->IdAcct, implode(',', $foldersId), $folder->IdDb);
		}
		
		/**
		 * @param WebMailMessage $message
		 * @param string $AccountId
		 * @return string
		 */
		function UpdateBody(&$message, $accountId)
		{
			$sql = 'UPDATE %sawm_messages_body SET msg = %s
					WHERE msgs.id_acct = %d AND msgs.id_msg = %d';
				
			return sprintf($sql, $this->_settings->DbPrefix,
						"0x".bin2hex($message->TryToGetOriginalMailMessage()),
						$accountId,	$message->IdMsg);
		}
		
		/**
		 * @param WebMailMessage $message
		 * @param string $AccountId
		 * @return string
		 */
		function SaveBody(&$message, $accountId)
		{
			//save body
			$sql = 'INSERT INTO %sawm_messages_body (id_acct, id_msg, msg)
					VALUES (%d, %d, %s)';
				
			return sprintf($sql, $this->_settings->DbPrefix, $accountId, $message->IdMsg, "0x".bin2hex($message->TryToGetOriginalMailMessage()));
		}	
		
		/**
		 * @param Array $messageIndexSet
		 * @param bool $indexAsUid
		 * @param Folder $folder
		 * @param Account $account
		 * @return string
		 */
		function DeleteMessagesBody(&$messageIndexSet, $indexAsUid, &$folder, &$account)
		{
			
			$sql = "DELETE
					FROM %1\$sawm_messages_body
					FROM %1\$sawm_messages AS msgs
					WHERE msgs.id_acct = %2\$d AND msgs.id_folder_db = %3\$d 
							AND	%1\$sawm_messages_body.id_acct = msgs.id_acct 
							AND %1\$sawm_messages_body.id_msg = msgs.id_msg
							AND msgs.%4\$s IN (%5\$s)";
			
			return sprintf($sql, $this->_settings->DbPrefix,
								$account->Id, $folder->IdDb,
								$this->_getMsgIdUidFieldName($indexAsUid, $account->MailProtocol),
								$this->_quoteUids($messageIndexSet, $indexAsUid, $account->MailProtocol));
		}
		
		/**
		 * @param int $pageNumber
		 * @param short $sortField
		 * @param bool $sortOrder
		 * @param Account $account
		 */
		function LoadContactsAndGroups($pageNumber, $sortField, $sortOrder, &$account)
		{
			$dopstr = '';
			switch ($sortField)
			{
				default:
				case 0:
					$filter = 'is_group';
					$temp = ($sortOrder)?'DESC':'ASC';
					$dopstr = ', name '.$temp;
					break;
				case 1:
					$filter = 'name';
					break;
				case 2:
					$filter = 'email';
					break;
				case 3:
					$filter = 'frequency';
					break;
			}
			
			$str = '';
			$nom = ($pageNumber - 1) * $account->ContactsPerPage;
			
			if ($nom)
			{
				$str = ' WHERE union_tbl.nuid NOT IN 
					(SELECT TOP %d nuid FROM
					(SELECT id_addr AS [id], fullname AS [name],
						CASE primary_email
							WHEN %s THEN h_email
							WHEN %s THEN b_email
							WHEN %s THEN other_email
						END AS email, 0 AS is_group,
						id_addr AS nuid, use_frequency AS frequency, use_friendly_nm AS usefriendlyname
					FROM %sawm_addr_book WHERE id_user = %d
					UNION
					SELECT id_group AS [id], group_nm AS [name], \'\' AS email, 1 AS is_group,
					-id_group AS nuid, use_frequency AS frequency, 1 AS usefriendlyname
					FROM %sawm_addr_groups
					WHERE id_user = %d) AS union_tbl2 ORDER BY %s %s %s)';
				
				$str = sprintf($str, $nom, 
								PRIMARYEMAIL_Home, PRIMARYEMAIL_Business, PRIMARYEMAIL_Other,
								$this->_settings->DbPrefix, $account->IdUser,
								$this->_settings->DbPrefix, $account->IdUser,
								$filter, ($sortOrder)?'DESC':'ASC', $dopstr);
			}
			
			$sql = 'SELECT TOP %d * FROM
					(SELECT id_addr AS [id], fullname AS [name],
						CASE primary_email
							WHEN %s THEN h_email
							WHEN %s THEN b_email
							WHEN %s THEN other_email
						END AS email, 0 AS is_group, 
						id_addr AS nuid, use_frequency AS frequency, use_friendly_nm AS usefriendlyname
					FROM %sawm_addr_book
					WHERE id_user = %d
					UNION
					SELECT id_group AS [id], group_nm AS [name], \'\' AS email, 1 AS is_group,
					-id_group AS nuid, use_frequency AS frequency, 1 AS usefriendlyname
					FROM %sawm_addr_groups
					WHERE id_user = %d) AS union_tbl%s					
					ORDER BY %s %s %s';

			return sprintf($sql, $account->ContactsPerPage,
								PRIMARYEMAIL_Home, PRIMARYEMAIL_Business, PRIMARYEMAIL_Other,
								$this->_settings->DbPrefix, $account->IdUser,
								$this->_settings->DbPrefix, $account->IdUser,
								$str,								
								$filter, ($sortOrder)?'DESC':'ASC', $dopstr);
		}
		

		/**
		 * @param int $pageNumber
		 * @param string $conditon
		 * @param int $groupId
		 * @param short $sortField
		 * @param bool $sortOrder
		 * @param Account $account
		 */
		function SearchContactsAndGroups($pageNumber, $condition, $groupId, $sortField, $sortOrder, &$account, $lookForType)
		{
			$dopstr = '';
			switch ($sortField)
			{
				case 0:
					$filter = 'is_group';
					$temp = ($sortOrder)?'DESC':'ASC';
					$dopstr = ', name '.$temp;
					break;
				default:	
				case 1:
					$filter = 'name';
					break;
				case 2:
					$filter = 'email';
					break;
				case 3:
					$filter = 'frequency';
					break;
			}
			
			$str = '';
			$accountPerPage = ($lookForType == 1) ? SUGGESTCONTACTS : $account->ContactsPerPage;
			$nom = ($pageNumber - 1) * $accountPerPage;
			
	  		$condition = str_replace('[', '[[]', $condition);
			
	  		$condition = ($lookForType == 1) ? $this->_escapeString($condition.'%') : $this->_escapeString('%'.$condition.'%');
			
			if ($groupId == -1)
			{
				if ($nom)
				{
					$str = ' WHERE union_tbl.nuid NOT IN 
						(SELECT TOP %d nuid FROM
						(SELECT id_addr AS [id], fullname AS [name],
							CASE primary_email
								WHEN %s THEN h_email
								WHEN %s THEN b_email
								WHEN %s THEN other_email
							END AS email, 0 AS is_group,
							id_addr AS nuid, use_frequency AS frequency, use_friendly_nm AS usefriendlyname
						FROM %sawm_addr_book WHERE id_user = %d AND
							(fullname LIKE %s OR h_email LIKE %s OR b_email LIKE %s OR other_email LIKE %s)
						UNION
						SELECT id_group AS [id], group_nm AS [name], \'\' AS email, 1 AS is_group,
						-id_group AS nuid, use_frequency AS frequency, 1 AS usefriendlyname
						FROM %sawm_addr_groups
						WHERE id_user = %d AND group_nm LIKE %s) AS union_tbl2 ORDER BY %s %s %s)';
					
					$str = sprintf($str, $nom, 
									PRIMARYEMAIL_Home, PRIMARYEMAIL_Business, PRIMARYEMAIL_Other,
									$this->_settings->DbPrefix, $account->IdUser, $condition, $condition, $condition, $condition,
									$this->_settings->DbPrefix, $account->IdUser, $condition,
									$filter, ($sortOrder)?'DESC':'ASC', $dopstr);
				}
					
				$sql = 'SELECT TOP %d * FROM
						(SELECT id_addr AS [id], fullname AS [name],
							CASE primary_email
								WHEN %s THEN h_email
								WHEN %s THEN b_email
								WHEN %s THEN other_email
							END AS email, 0 AS is_group, 
							id_addr AS nuid, use_frequency AS frequency, use_friendly_nm AS usefriendlyname
						FROM %sawm_addr_book
						WHERE id_user = %d AND
							(fullname LIKE %s OR h_email LIKE %s OR b_email LIKE %s OR other_email LIKE %s)
						UNION
						SELECT id_group AS [id], group_nm AS [name], \'\' AS email, 1 AS is_group,
						-id_group AS nuid, use_frequency AS frequency, 1 AS usefriendlyname
						FROM %sawm_addr_groups
						WHERE id_user = %d AND group_nm LIKE %s) AS union_tbl%s					
						ORDER BY %s %s %s';

				return sprintf($sql, $accountPerPage,
								PRIMARYEMAIL_Home, PRIMARYEMAIL_Business, PRIMARYEMAIL_Other,
								$this->_settings->DbPrefix, $account->IdUser, $condition, $condition, $condition, $condition,
								$this->_settings->DbPrefix, $account->IdUser, $condition,
								$str,								
								$filter, ($sortOrder)?'DESC':'ASC', $dopstr);
			}
			else
			{
				if ($nom)
				{
					$str = ' WHERE union_tbl.nuid NOT IN 
						(SELECT TOP %d nuid FROM
						(SELECT book.id_addr AS [id], fullname AS [name],
							CASE primary_email
								WHEN %s THEN h_email
								WHEN %s THEN b_email
								WHEN %s THEN other_email
							END AS email, 0 AS is_group,
							book.id_addr AS nuid, use_frequency AS frequency, use_friendly_nm AS usefriendlyname
						FROM %sawm_addr_book AS book
						INNER JOIN %sawm_addr_groups_contacts AS gr_cont ON gr_cont.id_addr = book.id_addr AND
								id_group = %d
						WHERE id_user = %d AND
							(fullname LIKE %s OR h_email LIKE %s OR b_email LIKE %s OR other_email LIKE %s)
						) AS union_tbl2 ORDER BY %s %s %s)';
					
					$str = sprintf($str, $nom, 
									PRIMARYEMAIL_Home, PRIMARYEMAIL_Business, PRIMARYEMAIL_Other,
									$this->_settings->DbPrefix, $this->_settings->DbPrefix, $groupId,
									$account->IdUser, $condition, $condition, $condition, $condition,
									$filter, ($sortOrder)?'DESC':'ASC', $dopstr);
				}
					
				$sql = 'SELECT TOP %d * FROM
						(SELECT book.id_addr AS [id], fullname AS [name],
							CASE primary_email
								WHEN %s THEN h_email
								WHEN %s THEN b_email
								WHEN %s THEN other_email
							END AS email, 0 AS is_group, 
							book.id_addr AS nuid, use_frequency AS frequency, use_friendly_nm AS usefriendlyname
						FROM %sawm_addr_book AS book
						INNER JOIN %sawm_addr_groups_contacts AS gr_cont ON gr_cont.id_addr = book.id_addr AND
								id_group = %d
						WHERE id_user = %d AND
							(fullname LIKE %s OR h_email LIKE %s OR b_email LIKE %s OR other_email LIKE %s)
						) AS union_tbl%s					
						ORDER BY %s %s %s';

				return sprintf($sql, $accountPerPage,
								PRIMARYEMAIL_Home, PRIMARYEMAIL_Business, PRIMARYEMAIL_Other,
								$this->_settings->DbPrefix, $this->_settings->DbPrefix, $groupId,
								$account->IdUser, $condition, $condition, $condition, $condition,
								$str,								
								$filter, ($sortOrder)?'DESC':'ASC', $dopstr);
			}

		}
		
		/**
		 * @param int $id
		 * @return string
		 */
		function DeleteFolderTreeById($id)
		{
			$sql = 'DELETE FROM %1$sawm_folders_tree
						FROM %1$sawm_folders
						WHERE %1$sawm_folders.id_folder = %1$sawm_folders_tree.id_folder
						AND %1$sawm_folders.id_acct = %2$d';
			
			return sprintf($sql, $this->_settings->DbPrefix, $id);
		}
		
		
		/**
		 * @param int $id
		 * @return string
		 */
		function DeleteAddrGroupsContactsById($id)
		{
			$sql = 'DELETE FROM %1$sawm_addr_groups_contacts
						FROM %1$sawm_addr_groups
						WHERE %1$sawm_addr_groups_contacts.id_group = %1$sawm_addr_groups.id_group
						AND %1$sawm_addr_groups.id_user = %2$d';

			return sprintf($sql, $this->_settings->DbPrefix, $id);
		}
		
		/**
		 * @param int $id
		 * @return string
		 */
		function DeleteCalendarEvents($id)
		{
			$sql = 'DELETE FROM %1$sacal_events
						FROM %1$sacal_calendars
						WHERE %1$sacal_events.calendar_id = %1$sacal_calendars.calendar_id
						AND %1$sacal_calendars.user_id = %2$d';

			return sprintf($sql, $this->_settings->DbPrefix, $id);
		}
		
		/**
		 * @return string
		 */
		function SelectAccountsCount($searchText)
		{
			if (strlen($searchText) > 0)
			{
				$sql = 'SELECT id_acct, email, mail_inc_host, mail_out_host, %s AS nlast_login, logins_count,
						mailbox_size, mailbox_limit
					FROM %3$sawm_accounts AS acct
					INNER JOIN %3$sawm_settings AS sett ON acct.id_user = sett.id_user
					WHERE (id_acct LIKE %1$s OR email LIKE %1$s OR %2$s LIKE %1$s
							OR logins_count LIKE %1$s OR mail_inc_host LIKE %1$s
							OR mail_out_host LIKE %1$s)';
				$sql = sprintf($sql, $this->_escapeString('%'.$searchText.'%'),
							CDateTime::GetMsSqlDateFormat('last_login'),
							$this->_settings->DbPrefix);
			}
			else 
			{
				$sql = sprintf('SELECT id_acct FROM %sawm_accounts', $this->_settings->DbPrefix);
			}
			
			
			return $sql;
		}		
		
		/** 
		 * @return string
		 */
		function SelectAllAccounts($pageNumber, $accountPerPage, $sortField, $sortOrder, $searchText)
		{
			$nom = ($pageNumber > 0) ? ($pageNumber - 1) * $accountPerPage : 0;
			$dopstr = '';
			$search = trim($searchText);
			
			if (strlen($searchText) > 0)
			{
				$search = ' AND (id_acct LIKE %1$s OR email LIKE %1$s OR %2$s LIKE %1$s
							OR logins_count LIKE %1$s OR mail_inc_host LIKE %1$s
							OR mail_out_host LIKE %1$s) ';
				
				$search = sprintf($search, $this->_escapeString('%'.$searchText.'%'),
							CDateTime::GetMsSqlDateFormat('last_login'));
			}
			
			if ($nom > 0)
			{
				$dopstr = ' AND id_acct NOT IN
						(SELECT id_acct FROM
						(SELECT TOP %d id_acct, acct1.id_user as id_user, deleted, email,
						mail_inc_host, mail_out_host, %s AS nlast_login, logins_count,
						mailbox_size, mailbox_limit, def_acct
						FROM %sawm_accounts AS acct1
						INNER JOIN %sawm_settings AS sett1 ON acct1.id_user = sett1.id_user
						WHERE id_acct > -1 %s
						ORDER BY %s %s, def_acct DESC) AS stable) ';
				
				$dopstr = sprintf($dopstr, $nom, CDateTime::GetMsSqlDateFormat('last_login'),
						$this->_settings->DbPrefix, $this->_settings->DbPrefix, $search,
						$sortField, ($sortOrder)?'DESC':'ASC');
			}
		
			$sql = 'SELECT TOP %d id_acct, acct.id_user as id_user, deleted, email,
						mail_inc_host, mail_out_host, %s AS nlast_login, logins_count,
						mailbox_size, mailbox_limit, def_acct
					FROM %sawm_accounts AS acct
					INNER JOIN %sawm_settings AS sett ON acct.id_user = sett.id_user
					WHERE id_acct > -1 %s%s
					ORDER BY %s %s, def_acct DESC';
					
			
			return sprintf($sql, $accountPerPage, CDateTime::GetMsSqlDateFormat('last_login'), 
					$this->_settings->DbPrefix, $this->_settings->DbPrefix, $search, $dopstr, 
					$sortField, ($sortOrder)?'DESC':'ASC');		
		} 
		
		/**
		 * @param Account $account
		 * @return string
		 */
		function SelectExpiredMessageUids(&$account)
		{
			$sql = 'SELECT str_uid FROM %sawm_messages
					WHERE id_acct = %d AND DATEADD(day, %d, msg_date) < GETDATE()';
			
			return sprintf($sql, $this->_settings->DbPrefix, $account->Id, $account->MailsOnServerDays);
		}
	}
