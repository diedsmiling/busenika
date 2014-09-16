<?php

	/**
	 * @param int $folderType
	 * @param int $protocol
	 * @return string
	 */
	function GetFolderImg($folderType, $protocol)
	{
		$dop_str = ($protocol == MAILPROTOCOL_IMAP4) ? '_sync' : '';

		switch ($folderType)
		{
			case FOLDERTYPE_Inbox:		return 'folder_inbox'.$dop_str.'.gif'; break;
			case FOLDERTYPE_SentItems:	return 'folder_send'.$dop_str.'.gif'; break;
			case FOLDERTYPE_Drafts:		return 'folder_drafts'.$dop_str.'.gif'; break;
			case FOLDERTYPE_Trash:		return 'folder_trash'.$dop_str.'.gif'; break;
		}
		return 'folder'.$dop_str.'.gif';
	}
	
	class SettingsPanel
	{
	
		/**
		 * @var PageBuilder
		 */
		var $_pagebuilder;
		
		/**
		 * @var BaseProcessor
		 */
		var $_proc;
		
		/**
		 * @var Object
		 */
		var $_main;
		
		/**
		 * @param PageBuilder $pageBuilder
		 * @return SettingsPanel
		 */
		function SettingsPanel(&$pagebuilder)
		{
			$this->_pagebuilder = &$pagebuilder;
			$this->_proc = &$pagebuilder->_proc;
			$this->_pagebuilder->AddJSText('var DataHelpUrl = "advanced-data-help.php";
function ResizeElements(mode) {}');
			switch ($this->_proc->sArray[SCREEN])
			{
				default:
				case SCREEN_SETTINGS:	
				case SET_COMMON:
					$this->_main = &new SettingsCommon($pagebuilder);
					break;
				case SET_ACCOUNT_PROF:	
				case SET_ACCOUNT_FILTERS:	
				case SET_ACCOUNT_SIGNATURE:	
				case SET_ACCOUNT_MFOLDERS:	
				case SET_ACCOUNT_ADDACC:
					$this->_main = &new SettingsAccount($pagebuilder);
					break;
				case SET_CONTACTS:	
					$this->_main = &new SettingsContacts($pagebuilder);
					break;
			}		
		}
		
		/**
		 * @return string
		 */
		function ToHTML()
		{
			return $this->_main->ToHTML();
		}
	}

	class SettingsCommon
	{
		/**
		 * @var PageBuilder
		 */
		var $_pagebuilder;
		
		/**
		 * @var BaseProcessor
		 */
		var $_proc;
		
		/**
		 * @var array
		 */
		var $data;
		
		/**
		 * @param PageBuilder $pagebuilder
		 * @return SettingsCommon
		 */
		function SettingsCommon(&$pagebuilder)
		{
			
			$this->data = array();
			$this->_pagebuilder = &$pagebuilder;
			$this->_proc = &$pagebuilder->_proc;
			
			$this->_pagebuilder->_js->AddText('
			
function SetAdvanced()
{
	var select = document.getElementById("str_date_format");
	if (select) select.value = "advanced";
}
			
function ChangeAdvanced(objSelect)
{
	var advInput = document.getElementById("strInputDateFormat");
	if (advInput && objSelect && objSelect.value != "advanced")
	{
		advInput.value = objSelect.value;
	}
}
			');
			
			$this->data['mails_per_page'] = $this->_proc->account->MailsPerPage;
			$this->data['int_disable_dhtml_editor'] = ($this->_proc->account->AllowDhtmlEditor) ? '' : 'checked="checked"';
			
			$this->data['int_use_preview_pane'] = ($this->_proc->account->ViewMode == VIEW_MODE_PREVIEW_PANE || $this->_proc->account->ViewMode == VIEW_MODE_PREVIEW_PANE_NO_IMG) ? 'checked="checked"' : '';
			$this->data['int_showimg'] = ($this->_proc->account->ViewMode == VIEW_MODE_PREVIEW_PANE || $this->_proc->account->ViewMode == VIEW_MODE_WITHOUT_PREVIEW_PANE) ? 'checked="checked"' : '';
			
			$this->data['skin_select'] = '';
			if ($this->_proc->settings->AllowUsersChangeSkin)
			{
				$this->data['skin_select'] = '<tr>
							<td class="wm_settings_title">'.JS_LANG_Skin.':</td>
							<td colspan="2">
							<select name="str_skin_path" id="skin_path">';
						
				$skinsList = &FileSystem::GetSkinsList();
				for ($i = 0, $c = count($skinsList); $i < $c; $i++)
				{
					$temp = ($this->_proc->account->DefaultSkin == $skinsList[$i]) ? 'selected="selected"' : '';
					$this->data['skin_select'] .= '<option value="'.ConvertUtils::AttributeQuote($skinsList[$i]).'" '. $temp .'> '.$skinsList[$i].'</option>'."\n";
				}
				$this->data['skin_select'] .= '</select></td></tr>';
			}
				
			$this->data['CharsetSelect'] = '';
			if ($this->_proc->settings->AllowUsersChangeCharset)
			{
				$this->data['CharsetSelect'] = '<tr>
						<td class="wm_settings_title">'.JS_LANG_DefCharsetOut.':</td>
						<td colspan="2">
							<select name="str_charset" id="strCharset">';
				global $CHARSETS;
				for ($i = 0, $c = count($CHARSETS); $i < $c; $i++)
				{
					$temp = ($this->_proc->account->DefaultOutCharset == $CHARSETS[$i][0]) ? 'selected="selected"' : '';
					$this->data['CharsetSelect'] .= '<option value="'.ConvertUtils::AttributeQuote($CHARSETS[$i][0]).'" '. $temp .'> '.$CHARSETS[$i][1].'</option>'."\n";
				}
				$this->data['CharsetSelect'] .= '</select></td></tr>';
			}
			
			$this->data['TimeZoneSelect'] = '';
			if ($this->_proc->settings->AllowUsersChangeTimeZone)
			{			
				$this->data['TimeZoneSelect'] = '<tr>
						<td class="wm_settings_title">'.JS_LANG_DefTimeOffset.':</td>
						<td colspan="2">
							<select name="str_time_zone" id="strTimeZone">';
				global $TIMEZONE;
				for ($i = 0, $c = count($TIMEZONE); $i < $c; $i++)
				{
					$temp = ($this->_proc->account->DefaultTimeZone == $i) ? 'selected="selected"' : '';
					$this->data['TimeZoneSelect'] .= '<option value="'.$i.'" '. $temp .'> '.$TIMEZONE[$i].'</option>'."\n";
				}
				$this->data['TimeZoneSelect'] .= '</select></td></tr>';
			}
			
			$this->data['LangSelect'] = '';
			if ($this->_proc->settings->AllowUsersChangeLanguage)
			{			

				$this->data['LangSelect'] = '<tr>
						<td class="wm_settings_title">'.JS_LANG_DefLanguage.':</td>
						<td colspan="2">
							<select name="str_def_language" id="strDefLanguage">';

				$langList = &FileSystem::GetLangList();
				for ($i = 0, $c = count($langList); $i < $c; $i++)
				{
					$temp = ($this->_proc->account->DefaultLanguage == $langList[$i]) ? 'selected="selected"' : '';
					$this->data['LangSelect'] .= '<option value="'.ConvertUtils::AttributeQuote($langList[$i]).'" '. $temp .'> '.$langList[$i].'</option>'."\n";
				}
				
				$this->data['LangSelect'] .= '</select>
						</td>
					</tr>';
			}
			
			$time = time();
			$day = (int) date('d', $time);
			$month = (int) date('m', $time);
			if ($day == $month)
			{
				$time = $time + (21 - $day) * 3600 * 24;
			}
			
			$data_format = $this->_proc->account->DefaultDateFormat;
			$time_format = $this->_proc->account->DefaultTimeFormat;
			$this->data['data_format'] = array('','','','','');
			$this->data['data_value'] = array(date('d/m/y', $time), date('m/d/y', $time), date('d M', $time));
						
			
			$this->data['time_format'] = array('','');
			$this->data['time_format'][$time_format] = 'checked="checked"';
			switch (strtolower($data_format))
			{
				default:
				case 'default': $this->data['data_format'][0] = 'selected="selected"'; break;
				case 'dd/mm/yy': $this->data['data_format'][1] = 'selected="selected"'; break;
				case 'mm/dd/yy': $this->data['data_format'][2] = 'selected="selected"'; break;
				case 'dd month': $this->data['data_format'][3] = 'selected="selected"'; break;
			}
			
			$this->data['data_format'][5] = $data_format;
			
		}
		
		function ToHTML()
		{

			return '
		<table class="wm_settings">
		<tr>
			<td class="wm_settings_nav">
				'.NavigationPrint($this->_pagebuilder, 0).'
			</td>
			<td class="wm_settings_cont">
				<form action="'.ACTIONFILE.'?action=save&req=commonset" method="POST">
				<table class="wm_settings_common">
					<tr>
						<td class="wm_settings_title">'.JS_LANG_MsgsPerPage.':</td>

						<td colspan="2">
							<input class="wm_input" type="text" size="2" id="mails_per_page" value="'.ConvertUtils::AttributeQuote($this->data['mails_per_page']).'" maxlength="2" name="mails_per_page"/>
						</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<input class="wm_checkbox" type="checkbox" name="int_disable_dhtml_editor" id="int_disable_dhtml_editor" value="1" '.$this->data['int_disable_dhtml_editor'].' />
							<label for="int_disable_dhtml_editor">'.JS_LANG_DisableRTE.'</label>
						</td>
					</tr>
					'
					.$this->data['skin_select']
					.$this->data['CharsetSelect']
					.$this->data['TimeZoneSelect']
					.$this->data['LangSelect']
					.'
					<tr>
						<td class="wm_settings_title">'.DefTimeFormat.':</td>
						<td>
							<input id="time_format_12" class="wm_checkbox" type="radio" value="1" name="time_format" '.$this->data['time_format'][1].' /><label for="time_format_12">1PM</label>
							&nbsp;
							<input id="time_format_24" class="wm_checkbox" type="radio" value="0" name="time_format" '.$this->data['time_format'][0].' /><label for="time_format_24">13:00</label>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">'.JS_LANG_DefDateFormat.':</td>
						<td colspan="2">
							<select name="str_date_format" id="str_date_format" onchange="ChangeAdvanced(this);">
								<option value="dd/mm/yy" '.$this->data['data_format'][1].'>'.$this->data['data_value'][0].'</option>
								<option value="mm/dd/yy" '.$this->data['data_format'][2].'>'.$this->data['data_value'][1].'</option>
								<option value="dd month" '.$this->data['data_format'][3].'>'.$this->data['data_value'][2].'</option>
								<option value="advanced" '.$this->data['data_format'][0].'>'.JS_LANG_DateAdvanced.'</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">'.JS_LANG_DateAdvanced.':</td>
						<td colspan="2">
							<input class="wm_input" type="text" name="str_date_format_input" id="strInputDateFormat" value="'.ConvertUtils::AttributeQuote($this->data['data_format'][5]).'"  maxlength="20" onchange="SetAdvanced();" />
							<img class="wm_settings_help" src="skins/'.$this->_pagebuilder->SkinName().'/icons/help.gif" onclick="PopupDataHelp();" />
						</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<input class="wm_checkbox" type="checkbox" name="int_use_preview_pane" id="int_use_preview_pane" value="1" '.$this->data['int_use_preview_pane'].' />
							<label for="int_use_preview_pane">'.JS_LANG_ShowViewPane.'</label>
						</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<input class="wm_checkbox" type="checkbox" name="int_showimg" id="int_showimg" value="1" '.$this->data['int_showimg'].' />
							<label for="int_showimg">'.AlwaysShowPictures.'</label>
						</td>
					</tr>
				</table>

				<table class="wm_settings_buttons" ID="Table4">
					<tr>
						<td>
							<input class="wm_button" type="submit" name="subm" value="'.ConvertUtils::AttributeQuote(JS_LANG_Save).'" ID="Submit1"/>
						</td>
					</tr>
				</table>
				</form>
			</td>
		</tr>
	</table>	
			';
		}
	}
	
	
	class SettingsAccount
	{
		/**
		 * @var PageBuilder
		 */
		var $_pagebuilder;
		
		/**
		 * @var BaseProcessor
		 */
		var $_proc;
		
		/**
		 * @var Account
		 */
		var $_editAccount = null;
		
		/**
		 * @var MailProcessor
		 */
		var $_editProccessor = null;
		
		/**
		 * @var FolderCollection
		 */
		var $_editFolders = null;
		
		/**
		 * @var array
		 */
		var $data;
		
		/**
		 * @var SettingsAccountForms
		 */
		var $_main;
		
		/**
		 * @param PageBuilder $pagebuilder
		 * @return SettingsAccount
		 */
		function SettingsAccount(&$pagebuilder)
		{
			$this->_pagebuilder = &$pagebuilder;
			$this->_proc = &$pagebuilder->_proc;
			$screen = $this->_proc->sArray[SCREEN];
			
			if ($this->_proc->sArray[EDIT_ACCOUNT_ID] != $this->_proc->account->Id)
			{
				if (array_key_exists($this->_proc->sArray[EDIT_ACCOUNT_ID], $this->_proc->accounts))
				{
					$this->_editAccount = &Account::LoadFromDb($this->_proc->sArray[EDIT_ACCOUNT_ID]);
					$this->_editProccessor = &new MailProcessor($this->_editAccount);
					$this->_editFolders = &$this->_editProccessor->GetFolders();
				}
				else 
				{
					$this->_editAccount = &$this->_proc->account;
					$this->_editProccessor = &$this->_proc->processor;
					$this->_editFolders = &$this->_proc->GetFolders();
				}
			}
			else 
			{
				$this->_editAccount = &$this->_proc->account;
				$this->_editProccessor = &$this->_proc->processor;
				$this->_editFolders = &$this->_proc->GetFolders();
			}
			
			$this->data['allowNewAccount'] = (!$this->_proc->settings->AllowUsersAddNewAccounts || !$this->_editAccount->AllowChangeSettings) ? 
				'' : '
		<input type="button" value="'.JS_LANG_AddNewAccount.'" 
			onclick="document.location=\''.BASEFILE.'?'.SCREEN.'='.SET_ACCOUNT_ADDACC.'\';"
			class="wm_button" ID="Button4" NAME="Button2"/>';
			
			$this->data['accountsTable'] = '';
			$accounts = &$this->_proc->GetAccounts();
	
			foreach ($accounts as $keyid => $value)
			{
				$isCurrent = ($keyid == $this->_editAccount->Id);
				$class = ($isCurrent) ? ' class="wm_settings_list_select"' : ' class="wm_control"';
				$name = ($isCurrent) ? '<b>'.$value[4].'</b>' : $value[4];
				//$name .= ($value[6]) ? ' (default)' : '';
				$onclick = ($isCurrent) ? '' : ' onclick="document.location=\''.BASEFILE.'?'.EDIT_ACCOUNT_ID.'='.$keyid.'\';"';

				$deleteHref = ($this->_proc->account->IsDemo)
					? '<a href="#" onclick="return DoAlert();">'.JS_LANG_Delete.'</a>'
					: '<a href="'.ACTIONFILE.'?action=delete&req=account&acctid='.$keyid.'" onclick="return confirm(\''.ConfirmDeleteAccount.'\');">'.JS_LANG_Delete.'</a>';

				$deleteHref = ($this->_editAccount->AllowChangeSettings) ? $deleteHref : '';	
				
				$this->data['accountsTable'] .= '
					<tr'.$class.'>
						<td'.$onclick.'>'.$name.'</td>
						<td style="width: 10px;">'.$deleteHref.'</td>
					</tr>';
			}	
			
			if ($screen == SET_ACCOUNT_ADDACC && !$this->_proc->settings->AllowUsersAddNewAccounts)
			{
				$screen = SET_ACCOUNT_PROF;
			}
			
			$this->_main = &new SettingsAccountForms($screen, $this);
			
			$this->data['tabSwitcher'] = '';
			
			if ($screen != SET_ACCOUNT_ADDACC)
			{
				$this->data['tabSwitcher'] .= '
				<div class="wm_settings_accounts_info">
					<div class="wm_settings_switcher_indent"></div>';
				$this->data['tabSwitcher'] .= ($screen == SET_ACCOUNT_MFOLDERS) ?
					'<div class="wm_settings_switcher_select_item">'.JS_LANG_ManageFolders.'</div>' :
					'<div class="wm_settings_switcher_item"><a href="'.BASEFILE.'?'.SCREEN.'='.SET_ACCOUNT_MFOLDERS.'">'.JS_LANG_ManageFolders.'</a></div>';
				$this->data['tabSwitcher'] .= ($screen == SET_ACCOUNT_SIGNATURE) ?
					'<div class="wm_settings_switcher_select_item">'.JS_LANG_Signature.'</div>' :
					'<div class="wm_settings_switcher_item"><a href="'.BASEFILE.'?'.SCREEN.'='.SET_ACCOUNT_SIGNATURE.'">'.JS_LANG_Signature.'</a></div>';					
				$this->data['tabSwitcher'] .= ($screen == SET_ACCOUNT_FILTERS) ?
					'<div class="wm_settings_switcher_select_item">&nbsp;'.JS_LANG_Filters.'&nbsp;</div>' :
					'<div class="wm_settings_switcher_item">&nbsp;<a href="'.BASEFILE.'?'.SCREEN.'='.SET_ACCOUNT_FILTERS.'">'.JS_LANG_Filters.'</a>&nbsp;</div>';

				if ($this->_editAccount->AllowChangeSettings)
				{
					$this->data['tabSwitcher'] .= ($screen == SET_ACCOUNT_PROF) ?
						'<div class="wm_settings_switcher_select_item">'.JS_LANG_Properties.'</div>' :
						'<div class="wm_settings_switcher_item"><a href="'.BASEFILE.'?'.SCREEN.'='.SET_ACCOUNT_PROF.'">'.JS_LANG_Properties.'</a></div>';						
				}
				$this->data['tabSwitcher'] .= '</div>';
			}
		}
		
		function ToHTML()
		{
				
			return '
<table class="wm_settings">
		<tr>
			<td class="wm_settings_nav">
				'.NavigationPrint($this->_pagebuilder, 1).'
			</td>
			<td class="wm_settings_cont">
				<table class="wm_settings_list" style="margin-bottom: 0px;">
				'.
				$this->data['accountsTable']
				.'				
				</table>

				<table class="wm_settings_add_account_button">
					<tr>
						<td>
							'.$this->data['allowNewAccount'].'
						</td>
					</tr>
				</table>
				
				'.
				$this->data['tabSwitcher']
				.
				$this->_main->ToHTML()
				.'
				
			</td>

		</tr>
	</table>
						
			';
		}
	}
	
	
	class SettingsAccountForms
	{
		
		/**
		 * @var SettingsAccount
		 */
		var $setaccount = null;
		
		/**
		 * @var string
		 */
		var $mainText = '';
		
		/**
		 * @param int $type
		 * @param SettingsAccount $setaccount
		 * @return SettingsAccountForms
		 */
		function SettingsAccountForms($type, &$setaccount)
		{
			$this->setaccount = &$setaccount;
			$account = &$setaccount->_editAccount;
					
			switch ($type)
			{
				default:
				case SET_ACCOUNT_PROF:
					$this->setaccount->_pagebuilder->AddJSFile('./classic/base.cnewaccountform.js');
					$this->setaccount->_pagebuilder->AddInitText('
newAccountForm = new CNewAccountForm();
newAccountForm.ShowPOP3AdvancedOptions();
newAccountForm.SetCheckFields();
');
				
					$checkeds = array();
					$checkeds[0] = ($account->DefaultAccount) ? 'checked="checked" ' : '';
					
					$values = array();
					$values[0] = $account->FriendlyName;
					$values[1] = $account->Email;
					$values[2] = $account->MailIncHost;
					$values[3] = $account->MailIncPort;
					
					switch ($account->MailProtocol)
					{
						default:
						case MAILPROTOCOL_POP3: 
							$values[4] = '<input type="hidden" id="fm_protocol" name="fm_protocol" value="pop">'.JS_LANG_Pop3; 
							break;
						case MAILPROTOCOL_IMAP4:
							$values[4] = '<input type="hidden" id="fm_protocol" name="fm_protocol" value="imap">'.JS_LANG_Imap4;
							break;
						case MAILPROTOCOL_WMSERVER:
							$values[4] = '<input type="hidden" id="fm_protocol" name="fm_protocol" value="wmserver">'.JS_LANG_Pop3;
							break;							
						
					}
					
					$values[5] = $account->MailIncLogin;
							
					$values[6] = $account->MailOutHost;
					$values[7] = $account->MailOutLogin;
					$values[8] = $account->MailOutPort;
					$values[9] = (strlen($account->MailOutPassword) > 0) ? DUMMYPASSWORD : '';
					$values[10] = (int) $account->MailsOnServerDays;
					
					$checkeds[1] = ($account->MailOutAuthentication) ? 'checked="checked" ' : '';
					$checkeds[2] = ($account->UseFriendlyName) ? 'checked="checked" ' : '';
					$checkeds[3] = ($account->GetMailAtLogin) ? 'checked="checked" ' : '';
					
					
					$checkArray = array('','','','', '');
					$typeSelected = array('', '', '');
					
if (isset($account->MailProtocol) && ($account->MailProtocol == MAILPROTOCOL_POP3 || $account->MailProtocol == MAILPROTOCOL_WMSERVER))
{	
	$mailprocessor = &new MailProcessor($account);
	$folders = &$mailprocessor->GetFolders();
	$inboxfolder = &$folders->GetFolderByType(FOLDERTYPE_Inbox);
	$folderSyncType = $inboxfolder->SyncType;
	
	if (isset($account->MailMode))
	{
		switch ($account->MailMode)
		{
			case MAILMODE_DeleteMessagesFromServer:
				$checkArray[0] = 'checked="checked"';
				break;
			case MAILMODE_LeaveMessagesOnServer:
				$checkArray[1] = 'checked="checked"';
				break;
			case MAILMODE_KeepMessagesOnServer:
				$checkArray[1] = 'checked="checked"';
				$checkArray[2] = 'checked="checked"';				
				break;
			case MAILMODE_DeleteMessageWhenItsRemovedFromTrash:
				$checkArray[1] = 'checked="checked"';
				$checkArray[3] = 'checked="checked"';				
				break;
			case MAILMODE_KeepMessagesOnServerAndDeleteMessageWhenItsRemovedFromTrash:
				$checkArray[1] = 'checked="checked"';
				$checkArray[2] = 'checked="checked"';
				$checkArray[3] = 'checked="checked"';
				break;
		}
	}
	
	$checkArray[4] = ($folderSyncType == FOLDERSYNC_AllHeadersOnly || $folderSyncType == FOLDERSYNC_AllEntireMessages) ? 'checked="checked"' : '';
	
	$typeSelected[0] = ($folderSyncType == FOLDERSYNC_NewHeadersOnly || $folderSyncType == FOLDERSYNC_AllHeadersOnly) ? 'selected="selected"' : '';
	$typeSelected[1] = ($folderSyncType == FOLDERSYNC_NewEntireMessages || $folderSyncType == FOLDERSYNC_AllEntireMessages) ? 'selected="selected"' : '';
	$typeSelected[2] = ($folderSyncType == FOLDERSYNC_DirectMode) ? 'selected="selected"' : '';
}
				

				$allowDM = ($this->setaccount->_editAccount->AllowDirectMode) ? '<option value="5" '.$typeSelected[2].'>'.JS_LANG_SyncTypeDirectMode.'</option>' : '';

				$blockDef = '';
				if ($this->setaccount->_editAccount->DefaultAccount && $this->setaccount->_proc->CountDefaultAccounts() < 2)
				{
					$blockDef = 'disabled="disabled"';
				}				
				
				$this->mainText = '			
					<form action="'.ACTIONFILE.'?action=update&req=account" id="accform" method="POST">	
					<table class="wm_settings_properties">
					<tr>
						<td colspan="3">
							<input class="wm_checkbox" type="checkbox" name="login_from_account" id="login_from_account" value="1" '.$checkeds[0].' '.$blockDef.'/>
							<label for="login_from_account">'.JS_LANG_UseForLogin.'</label>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">'.JS_LANG_MailFriendlyName.':</td>
						<td colspan="2">
							<input class="wm_input wm_settings_input" type="text" name="fm_friendly_name" value="'.ConvertUtils::AttributeQuote($values[0]).'" maxlength="65" id="fm_friendly_name" />
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">* '.JS_LANG_MailEmail.':</td>
						<td colspan="2">
							<input class="wm_input wm_settings_input" type="text" name="fm_email" value="'.ConvertUtils::AttributeQuote($values[1]).'" maxlength="255" id="fm_email" />
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">* '.JS_LANG_MailIncHost.':</td>
 						<td>
							<input class="wm_input" type="text" name="fm_inc_server" value="'.ConvertUtils::AttributeQuote($values[2]).'" maxlength="255" id="fm_inc_server"/>
							<input type="hidden" name="fm_incoming_protocol" value="'.ConvertUtils::AttributeQuote($account->MailProtocol).'" id="fm_incoming_protocol"/> '.$values[4].'
						</td>
						<td class="wm_settings_title">
							* '.JS_LANG_MailIncPort.': <input class="wm_input wm_port_input" type="text" size="3" name="fm_inc_server_port" id="fm_inc_server_port" value="'.ConvertUtils::AttributeQuote($values[3]).'" maxlength="5"/>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">* '.JS_LANG_MailIncLogin.':</td>
						<td colspan="2">
							<input class="wm_input wm_settings_input" type="text" name="fm_inc_login" value="'.ConvertUtils::AttributeQuote($values[5]).'" maxlength="255" id="fm_inc_login"/>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">* '.JS_LANG_MailIncPass.':</td>
						<td colspan="2">
							<input class="wm_input wm_settings_input" type="password" name="fm_inc_password" maxlength="255" value="'.ConvertUtils::AttributeQuote(DUMMYPASSWORD).'" id="fm_inc_password"/>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">* '.JS_LANG_MailOutHost.':</td>
						<td>
							<input class="wm_input" type="text" name="fm_smtp_server" value="'.ConvertUtils::AttributeQuote($values[6]).'" maxlength="255" id="fm_smtp_server"/>
						</td>
						<td class="wm_settings_title">
							* '.JS_LANG_MailOutPort.': <input class="wm_input wm_port_input" type="text" size="3" name="fm_smtp_server_port" value="'.ConvertUtils::AttributeQuote($values[8]).'" maxlength="5" id="fm_smtp_server_port"/>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">'.JS_LANG_MailOutLogin.':</td>
						<td colspan="2">
							<input class="wm_input wm_settings_input" type="text" name="fm_smtp_login" value="'.ConvertUtils::AttributeQuote($values[7]).'" maxlength="255" id="fm_smtp_login"/>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">'.JS_LANG_MailOutPass.':</td>
						<td colspan="2">
							<input class="wm_input wm_settings_input" type="password" name="fm_smtp_password" maxlength="255" id="fm_smtp_password" value="'.ConvertUtils::AttributeQuote($values[9]).'" />
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input class="wm_checkbox" type="checkbox" name="fm_smtp_authorisation" id="fm_smtp_authorisation" value="1" '.$checkeds[1].' />
							<label for="fm_smtp_authorisation">'.JS_LANG_MailOutAuth1.'</label><br/>
							<label for="fm_smtp_authorisation" class="wm_secondary_info wm_nextline_info">'.JS_LANG_MailOutAuth2.'</label>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input class="wm_checkbox" type="checkbox" name="fm_use_friendly_name" id="fm_use_friendly_name" value="1" '.$checkeds[2].' />
							<label for="fm_use_friendly_name">'.JS_LANG_UseFriendlyNm1.'</label><label class="wm_secondary_info wm_inline_info" for="fm_use_friendly_name">'.JS_LANG_UseFriendlyNm2.'</label>
						</td>
					</tr>
					
					<tr>
						<td colspan="3">
							<input class="wm_checkbox" type="checkbox" name="fm_getmail_at_login" id="fm_getmail_at_login" value="1" '.$checkeds[3].'/>
							<label for="fm_getmail_at_login">'.JS_LANG_GetmailAtLogin.'</label>
						</td>
					</tr>

					<tr id="pop_advanced">
						<td colspan="3">
							<input class="wm_checkbox" type="radio" value="1" name="fm_mail_management_mode" id="fm_mail_management_mode1" '.$checkArray[0].'/>
							<label for="fm_mail_management_mode1">'.JS_LANG_MailMode0.'</label><br />
							<input class="wm_checkbox" type="radio" value="2" name="fm_mail_management_mode" id="fm_mail_management_mode2" '.$checkArray[1].' />
							<label for="fm_mail_management_mode2">'.JS_LANG_MailMode1.'</label><br />
							<input class="wm_checkbox wm_settings_para" type="checkbox" name="fm_keep_for_x_days" id="fm_keep_for_x_days" value="1" '.$checkArray[2].'/>
							<label for="fm_keep_for_x_days">'.JS_LANG_MailMode2.'</label>
							<input class="wm_input" type="text" size="1" value="'.ConvertUtils::AttributeQuote($values[10]).'" maxlength="6" name="fm_keep_messages_days" id="fm_keep_messages_days"/>
							'.JS_LANG_MailsOnServerDays.'<br />
							<input class="wm_checkbox wm_settings_para" type="checkbox" name="fm_delete_messages_from_trash" id="fm_delete_messages_from_trash" value="1" '.$checkArray[3].'/>
							<label for="fm_delete_messages_from_trash">'.JS_LANG_MailMode3.'</label>
<!--						</td>
					</tr>
					<tr id="pop_advanced2">
						<td colspan="3">--><br /><br />
							'.JS_LANG_InboxSyncType.': 
							<select id="fm_inbox_sync" name="synchronizeSelect">
								<option value="1" '.$typeSelected[0].'>'.JS_LANG_Pop3SyncTypeEntireHeaders.'</option>
								<option value="3" '.$typeSelected[1].'>'.JS_LANG_Pop3SyncTypeEntireMessages.'</option>
								'.$allowDM.'
							</select>
<!--						</td>
					</tr>
					<tr id="pop_advanced3">
						<td colspan="3">--><br /><br />
							<input class="wm_checkbox" type="checkbox" name="fm_int_deleted_as_server" id="fm_int_deleted_as_server" value="1" '.$checkArray[4].'/>
							<label for="fm_int_deleted_as_server">'.JS_LANG_DeleteFromDb.'</label>
						</td>
					</tr>
				</table>


				<table class="wm_settings_buttons">
					<tr>
						<td class="wm_secondary_info">
							'.JS_LANG_InfoRequiredFields.'
						</td>
						<td>
							<input class="wm_button" type="submit" name="subm1" id="subm1" value="'.ConvertUtils::AttributeQuote(JS_LANG_Save).'" />
						</td>
					</tr>
				</table>
				</form>
					';
					break;
					
				case SET_ACCOUNT_ADDACC:
				
					$this->setaccount->_pagebuilder->AddJSFile('./classic/base.cnewaccountform.js');
					$this->setaccount->_pagebuilder->AddInitText('
newAccountForm = new CNewAccountForm();
newAccountForm.ShowPOP3AdvancedOptions();
newAccountForm.SetCheckFields();
');
					
					$allowDM = ($this->setaccount->_proc->settings->AllowDirectMode)
						? '<option value="5">'.JS_LANG_SyncTypeDirectMode.'</option>' : '';

					$this->mainText = '			
					<form action="'.ACTIONFILE.'?action=new&req=account" id="accform" method="POST">		
					<table class="wm_settings_properties">
					<tr>
						<td colspan="3">
							<input class="wm_checkbox" type="checkbox" name="login_from_account" id="login_from_account" value="1" />
							<label for="login_from_account">'.JS_LANG_UseForLogin.'</label>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">'.JS_LANG_MailFriendlyName.':</td>
						<td colspan="2">
							<input class="wm_input wm_settings_input" type="text" name="fm_friendly_name" value="" maxlength="100" id="fm_friendly_name"/>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">* '.JS_LANG_MailEmail.':</td>
						<td colspan="2">
							<input class="wm_input wm_settings_input" type="text" name="fm_email" value="" maxlength="100" id="fm_email"/>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">* '.JS_LANG_MailIncHost.':</td>
 						<td>
							<input class="wm_input" type="text" name="fm_inc_server" value="" maxlength="100" id="fm_inc_server"/>
							<select id="fm_protocol" name="fm_protocol">
								<option value = "pop">'.JS_LANG_Pop3.'</option>
								<option value = "imap">'.JS_LANG_Imap4.'</option>								
							</select>
						</td>
						<td class="wm_settings_title">
							* '.JS_LANG_MailIncPort.': <input class="wm_input wm_port_input" type="text" size="3" name="fm_inc_server_port" id="fm_inc_server_port" value="110" maxlength="5" />
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">* '.JS_LANG_MailIncLogin.':</td>
						<td colspan="2">
							<input class="wm_input wm_settings_input" type="text" name="fm_inc_login" value="" maxlength="100" id="fm_inc_login"/>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">* '.JS_LANG_MailIncPass.':</td>
						<td colspan="2">
							<input class="wm_input wm_settings_input" type="password" name="fm_inc_password" maxlength="100" value="" id="fm_inc_password"/>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">* '.JS_LANG_MailOutHost.':</td>
						<td>
							<input class="wm_input" type="text" name="fm_smtp_server" 
								onfocus="if (this.value.length == 0) { this.value = document.getElementById(\'fm_inc_server\').value; this.select(); }"
								value="" maxlength="100" id="fm_smtp_server"/>
						</td>
						<td class="wm_settings_title">
							* '.JS_LANG_MailOutPort.': <input class="wm_input wm_port_input" type="text" size="3" name="fm_smtp_server_port" value="25" maxlength="5" id="fm_smtp_server_port"/>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">'.JS_LANG_MailOutLogin.':</td>
						<td colspan="2">
							<input class="wm_input wm_settings_input" type="text" name="fm_smtp_login" value="" maxlength="100" id="fm_smtp_login"/>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">'.JS_LANG_MailOutPass.':</td>
						<td colspan="2">
							<input class="wm_input wm_settings_input" type="password" name="fm_smtp_password" maxlength="100" id="fm_smtp_password"/>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input class="wm_checkbox" type="checkbox" name="fm_smtp_authorisation" id="fm_smtp_authorisation" value="1" />
							<label for="fm_smtp_authorisation">'.JS_LANG_MailOutAuth1.'</label><br/>
							<label for="fm_smtp_authorisation" class="wm_secondary_info wm_nextline_info">'.JS_LANG_MailOutAuth2.'</label>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input class="wm_checkbox" type="checkbox" name="fm_use_friendly_name" id="fm_use_friendly_name" value="1" checked="checked" />
							<label for="fm_use_friendly_name">'.JS_LANG_UseFriendlyNm1.'</label><label class="wm_secondary_info wm_inline_info" for="fm_use_friendly_name">'.JS_LANG_UseFriendlyNm2.'</label>
						</td>
					</tr>
					
					<tr>
						<td colspan="3">
							<input class="wm_checkbox" type="checkbox" name="fm_getmail_at_login" id="fm_getmail_at_login" value="1" />
							<label for="fm_getmail_at_login">'.JS_LANG_GetmailAtLogin.'</label>
						</td>
					</tr>

					<tr id="pop_advanced">
						<td colspan="3">
							<input class="wm_checkbox" type="radio" value="1" name="fm_mail_management_mode" id="fm_mail_management_mode1" />
							<label for="fm_mail_management_mode1">'.JS_LANG_MailMode0.'</label><br />
							<input class="wm_checkbox" type="radio" value="2" name="fm_mail_management_mode" id="fm_mail_management_mode2" checked="checked" />
							<label for="fm_mail_management_mode2">'.JS_LANG_MailMode1.'</label><br />
							<input class="wm_checkbox wm_settings_para" type="checkbox" name="fm_keep_for_x_days" id="fm_keep_for_x_days" value="1" />
							<label for="fm_keep_for_x_days">'.JS_LANG_MailMode2.'</label>
							<input class="wm_input" type="text" size="1" value="7" maxlength="6" name="fm_keep_messages_days" id="fm_keep_messages_days"/>
							'.JS_LANG_MailsOnServerDays.'<br />
							<input class="wm_checkbox wm_settings_para" type="checkbox" name="fm_delete_messages_from_trash" id="fm_delete_messages_from_trash" value="1" />
							<label for="fm_delete_messages_from_trash">'.JS_LANG_MailMode3.'</label>
<!--						</td>
					</tr>
					<tr id="pop_advanced2">
						<td colspan="3">--><br /><br />
							'.JS_LANG_InboxSyncType.': 
							<select id="fm_inbox_sync" name="synchronizeSelect">
								<option value="1">'.JS_LANG_Pop3SyncTypeEntireHeaders.'</option>
								<option value="3" selected="selected">'.JS_LANG_Pop3SyncTypeEntireMessages.'</option>
								'.$allowDM.'
							</select>
<!--						</td>
					</tr>
					<tr id="pop_advanced3">
						<td colspan="3">--><br /><br />
							<input class="wm_checkbox" type="checkbox" name="fm_int_deleted_as_server" id="fm_int_deleted_as_server" value="1"/>
							<label for="fm_int_deleted_as_server">'.JS_LANG_DeleteFromDb.'</label>
						</td>
					</tr>
				</table>


				<table class="wm_settings_buttons">
					<tr>
						<td>
							<input class="wm_button" type="submit" name="subm1" value="'.ConvertUtils::AttributeQuote(JS_LANG_Save).'" />
						</td>
					</tr>
				</table>
				</form>
					';
					break;
					
					
				case SET_ACCOUNT_SIGNATURE:
					
					$checkeds = array('', '');
					$switcher = '';
					$checkeds[0] = ($account->SignatureOptions == SIGNATURE_OPTION_AddToAll || $account->SignatureOptions == SIGNATURE_OPTION_AddToNewOnly) ? 'checked="checked"' : '';
					$checkeds[1] = ($account->SignatureOptions == SIGNATURE_OPTION_AddToNewOnly) ? 'checked="checked"' : '';
					
					$signature = ($this->setaccount->_proc->account->AllowDhtmlEditor)
						? $account->Signature : strip_tags($account->Signature);
					
					if ($this->setaccount->_proc->account->AllowDhtmlEditor)
					{
						$this->setaccount->_pagebuilder->AddJSFile('class.html-editor.js');
						$this->setaccount->_pagebuilder->AddJSText(
						'
				function saveSignature() {
					var plainEditor = document.getElementById("editor_area");
					var hidekey = document.getElementById("isHtml");
					if (HTMLEditor._htmlMode) {
						plainEditor.value = HTMLEditor.GetText();
						hidekey.value = "1";
					} else {
						hidekey.value = "0";
					}
				}
				function EditAreaLoadHandler() { HTMLEditor.LoadEditArea();	}
				function CreateLinkHandler(url) { HTMLEditor.CreateLinkFromWindow(url); }
				function DesignModeOnHandler(rer) {
					HTMLEditor.Show();
					var sign = "'.ConvertUtils::ReBuildStringToJavaScript($signature, '"').'"; var signType = '.$account->SignatureType.'
					if (signType == 0) {
						HTMLEditor.SetText(sign);
					} else {
						if (sign.length == 0) sign = "<br/>";
						HTMLEditor.SetHtml(sign);
					}}
						');
						
						$this->setaccount->_pagebuilder->AddInitText('
				EditAreaUrl = "edit-area.php";
				HTMLEditor = new CHtmlEditorField(true);
				HTMLEditor.SetPlainEditor(document.getElementById("editor_area"), document.getElementById("editor_switcher"));
				HTMLEditor.Show();	
				HTMLEditor.Resize(684, 330);');
						
						$switcher = '<a class="wm_reg" href="#" id="editor_switcher">'.JS_LANG_SwitchToPlainMode.'</a>';
					}
					else 
					{
						$this->setaccount->_pagebuilder->AddJSText('function saveSignature() {}');
					}
					
			$this->setaccount->_pagebuilder->AddInitText('
if (!document.getElementById("add_signatures").checked) {
	document.getElementById("replies_forwards").disabled=true;
}
			');
					
					$this->mainText = '
					<form action="'.ACTIONFILE.'?action=update&req=signature" method="POST" onsubmit="if (!DoAlert()) return false; saveSignature();">
					<input type="hidden" name="isHtml" id="isHtml" value="0">
					<table class="wm_settings_signature">
					<tr id="plain_mess">
						<td>
							<div id="external_mess" class="wm_input wm_plain_editor_container">
								<textarea id="editor_area" class="wm_plain_editor_text" name="signature">'.$signature.'</textarea>
							</div>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">
							'.$switcher.'
						</td>
					</tr>
					<tr>
						<td>
							<input class="wm_checkbox" type="checkbox" value="1" onclick="(this.checked) ? document.getElementById(\'replies_forwards\').disabled=false:document.getElementById(\'replies_forwards\').disabled=true;" id="add_signatures" name="add_signatures" '.$checkeds[0].' />
							<label for="add_signatures">'.JS_LANG_AddSignatures.'</label>
						</td>
					<tr>
						<td>
							<input class="wm_checkbox wm_settings_para" type="checkbox" value="1" id="replies_forwards" name="replies_forwards" '.$checkeds[1].' />
							<label for="replies_forwards">'.JS_LANG_DontAddToReplies.'</label>
						</td>
					</tr>
  				</table>
  				
				<table class="wm_settings_buttons">
					<tr>
						<td>
							<input class="wm_button" type="submit" name="subm" value="'.ConvertUtils::AttributeQuote(JS_LANG_Save).'" />
						</td>
					</tr>
				</table></form>';
					
					break;
					
						
				case SET_ACCOUNT_FILTERS:
					
					$this->setaccount->_pagebuilder->AddJSText('
function ChangeAction()
{
	if (document.getElementById("actionfilter").value == '.FILTERACTION_MoveToFolder.') {
		document.getElementById("filterfolder").disabled = false;
	} else {
		document.getElementById("filterfolder").disabled=true;
	}
}

function CheckSubmit()
{
	var obj = document.getElementById("filter_text");
	if (obj && obj.value == "") {
		alert(Lang.WarningEmptyFilter);
		return false;
	}
	return true;
}
					');
					
					$checkeds = array('');
					$checkeds[0] = ($account->XSpam) ? 'checked="checked"' : '';
					$filterTable = '';

					$filters = &$this->setaccount->_proc->db->SelectFilters($account->Id);
					$editfilter = null;
			
					if ($filters != null)
					{
						foreach (array_keys($filters->Instance()) as $key)
						{
							$filter = &$filters->Get($key);
							$class = '';
							if ($filter->Id == Get::val('fedit', -1)) 
							{
								$editfilter = &$filter;
								$class = ' class="wm_settings_list_select"';
							}
							
							$field = '';
							switch ($filter->Field)
							{
								case FILTERFIELD_From: $field = JS_LANG_From; break;
								case FILTERFIELD_To: $field = JS_LANG_To; break;
								case FILTERFIELD_Subject: $field = JS_LANG_Subject; break;
							}
							$condition = '';
							switch ($filter->Condition)
							{
								case FILTERCONDITION_ContainSubstring: $condition = JS_LANG_ContainSubstring; break;
								case FILTERCONDITION_ContainExactPhrase: $condition = JS_LANG_ContainExactPhrase; break;
								case FILTERCONDITION_NotContainSubstring: $condition = JS_LANG_NotContainSubstring; break;
							}							
							
							$filterTable .= '
					<tr'.$class.'>
						<td>'.$condition.' <b>'.$filter->Filter.'</b> '.JS_LANG_FilterDesc_At.' '.$field.' '.JS_LANG_FilterDesc_Field.'</td>
						<td style="width: 10px;"><a href="'.BASEFILE.'?fedit='.$filter->Id.'">'.JS_LANG_EditFilter.'</a></td>
						<td style="width: 10px;"><a onclick="return confirm(Lang.ConfirmAreYouSure);" href="'.ACTIONFILE.'?action=delete&req=filter&fdel='.$filter->Id.'">'.JS_LANG_Delete.'</a></td>
					</tr>
							';
						}
					}
					
					$header = ($editfilter) ? JS_LANG_EditFilter : JS_LANG_NewFilter;
					$button = ($editfilter) ? JS_LANG_Save : JS_LANG_Add;
					
					$disabled = ' disabled="disabled"';
					
					$editData = array(
						array('','',''),
						array('','',''),
						'',
						array('','','','')
					);
					if ($editfilter)
					{
						$editData[0][$editfilter->Field] = ' selected="selected"'; 
						$editData[1][$editfilter->Condition] = ' selected="selected"';
						$editData[2] = $editfilter->Filter;
						$editData[3][$editfilter->Action] = ' selected="selected"';
						if ($editfilter->Action == FILTERACTION_MoveToFolder) $disabled = '';
					}
					
					$folderText = ($editfilter) ?
						$this->CreateSelectFolderTreeForFilters($this->setaccount->_editFolders, $editfilter->IdFolder) :
						$this->CreateSelectFolderTreeForFilters($this->setaccount->_editFolders);
					
					$filterId = ($editfilter) ? $editfilter->Id : -1;
					
					$this->mainText = '
				<table class="wm_settings_list">'.$filterTable.'</table>

				<table class="wm_settings_edit_filter">
					<tr>
						<td colspan="3" style="font-weight: bold;">'.$header.'</td>
					</tr>
					<tr>
						<td class="wm_settings_title">'.JS_LANG_Field.':</td>
						<td colspan="2">
							<form action="'.ACTIONFILE.'?action=update&req=filter" onsubmit="if (!DoAlert()) return false;return CheckSubmit();" method="POST">
							<input type="hidden" name="filterId" value="'.$filterId.'" />
							<select name="id_rule_value" name="id_rule_value">
								<option value="'.FILTERFIELD_From.'"'.$editData[0][FILTERFIELD_From].'>'.JS_LANG_From.'</option>
								<option value="'.FILTERFIELD_To.'"'.$editData[0][FILTERFIELD_To].'>'.JS_LANG_To.'</option>
								<option value="'.FILTERFIELD_Subject.'"'.$editData[0][FILTERFIELD_Subject].'>'.JS_LANG_Subject.'</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">'.JS_LANG_Condition.':</td>
						<td>
							<select name="fcontain">
								<option value="'.FILTERCONDITION_ContainSubstring.'"'.$editData[1][FILTERCONDITION_ContainSubstring].'>'.JS_LANG_ContainSubstring.'</option>
								<option value="'.FILTERCONDITION_ContainExactPhrase.'"'.$editData[1][FILTERCONDITION_ContainExactPhrase].'>'.JS_LANG_ContainExactPhrase.'</option>
								<option value="'.FILTERCONDITION_NotContainSubstring.'"'.$editData[1][FILTERCONDITION_NotContainSubstring].'>'.JS_LANG_NotContainSubstring.'</option>
							</select>
						</td>
						<td>
							<input class="wm_input wm_edit_filter_input" type="text" name="filter_text" id="filter_text" value="'.ConvertUtils::AttributeQuote($editData[2]).'" maxlength="99" />
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">'.JS_LANG_Action.':</td>
						<td>
							<select name="faction" id="actionfilter" onchange="document.getElementById(\'filterfolder\').disabled=(this.value=='.FILTERACTION_MoveToFolder.')?false:true;">
								<option value="'.ConvertUtils::AttributeQuote(FILTERACTION_DeleteFromServerImmediately).'"'.$editData[3][FILTERACTION_DeleteFromServerImmediately].'>'.JS_LANG_DeleteFromServer.'</option>
								<option value="'.ConvertUtils::AttributeQuote(FILTERACTION_MarkGrey).'"'.$editData[3][FILTERACTION_MarkGrey].'>'.JS_LANG_MarkGrey.'</option>
								<option value="'.ConvertUtils::AttributeQuote(FILTERACTION_MoveToFolder).'"'.$editData[3][FILTERACTION_MoveToFolder].'>'.JS_LANG_MoveToFolder.'</option>
							</select>
						</td>
						<td>
							<select name="ffolder" id="filterfolder" '.$disabled.'>
							'
							.
							$folderText
							.
							'
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="3" class="wm_settings_title">
							<hr>
							<input class="wm_button" type="button" onclick="document.location=\''.BASEFILE.'\'" value="'.ConvertUtils::AttributeQuote(JS_LANG_Cancel).'" />
							<input class="wm_button" type="submit" name="submitType" value="'.ConvertUtils::AttributeQuote($button).'" />
							</form>
						</td>
					</tr>
				</table>
				<table class="wm_settings_filters">
					<tr>
						<td class="wm_settings_header">'.JS_LANG_OtherFilterSettings.'</td>
					</tr>
					<tr>
						<td>
							<form action="'.ACTIONFILE.'?action=update&req=x-spam" onsubmit="return DoAlert();" method="POST">
							<input class="wm_checkbox" type="checkbox" value="1" id="checkbox_x-spam" name="x-spam" '.$checkeds[0].' />
							<label for="checkbox_x-spam">'.JS_LANG_ConsiderXSpam.'</label>
						</td>
					</tr>
					<tr>
						<td class="wm_settings_title">
							<hr><input class="wm_button" type="submit" value="'.ConvertUtils::AttributeQuote(JS_LANG_Apply).'" /></form>
						</td>
					</tr>
				</table>';
					break;
					
					
				case SET_ACCOUNT_MFOLDERS:

					$this->setaccount->_pagebuilder->AddJSText('
var folderInput, folderHref;
					
function EditFolder(folderId)
{
	if (folderHref && folderInput) {
		folderInput.className = "wm_hide";
		folderHref.className = "";
	}

	folderHref = document.getElementById("folder_a_" + folderId);
	folderInput = document.getElementById("folder_i_" + folderId);
	var folderForm = document.getElementById("folder_form_" + folderId);
	
	if (folderHref && folderInput && folderForm) {
		folderInput.className = "";
		folderHref.className = "wm_hide";
		folderInput.size = folderInput.value.length + 2;
		folderInput.onkeydown = function(ev)
		{
			if (isEnter(ev)) {
				if (folderInput.value != folderHref.innerHTML) {
					var val = new CValidate();
					if (val.IsCorrectFileName(folderInput.value)) {
						folderHref.innerHTML = folderInput.value;
						folderForm.submit();					
					} else {
						alert(Lang.WarningCantUpdateFolder);
					}
				}
				folderInput.className = "wm_hide";
				folderHref.className = "";
			}
		}
		folderInput.focus();
	}
	return false;
}

function DeleteFolders()
{
	if (confirm(Lang.ConfirmAreYouSure)) {
		var inputs = document.getElementsByTagName("input");
		var i, c;
		var form = CreateChildWithAttrs(document.body, "form", [["action", "'.ACTIONFILE.'?action=delete&req=folders"], ["method", "POST"]]);
		for (i = 0, c = inputs.length; i < c; i++) {
			if (inputs[i].type == "checkbox" && inputs[i].checked ) {
				CreateChildWithAttrs(form, "input", [["type", "hidden"], ["name", inputs[i].name], ["value", inputs[i].value]]);
			}
		}
		if (c > 1) {
			form.submit();
		}
	}
}

function SelectAllInputs(obj)
{
	var inputs = document.getElementsByTagName("input");
	var i, c;
	for (i = 0, c = inputs.length; i < c; i++) {
		if (inputs[i].type == "checkbox" && !inputs[i].disabled) {
			inputs[i].checked = obj.checked;
		}
	}
}

function formSubmit()
{
	var inputNewFolder = document.getElementById("newFolderName");
	var val = new CValidate();
	if (inputNewFolder && val.IsCorrectFileName(inputNewFolder.value)) {
		return true;
	} else {
		alert(Lang.WarningCantCreateFolder);
		return false;
	}
}
					');
					
					$messagesCount = $messagesSize = 0;
					$foldersLine = $this->CreateHtmlFolderTree($this->setaccount->_editFolders, $messagesCount, $messagesSize, $this->setaccount->_proc->account);

					$FolderHeaderTdWidth = '410px;';
					$SynkHeaderTd = 'td class="wm_hide"';
					$newFolderCreate = $infoTable = '';
					if ($this->setaccount->_editAccount->MailProtocol == MAILPROTOCOL_IMAP4)
					{
						$FolderHeaderTdWidth = '270px;"';
						$SynkHeaderTd = 'td style="width: 140px;"';
						$newFolderCreate = '
						<td rowspan="2" class="wm_settings_on_mailserver">
							<input type="radio" class="wm_checkbox" checked="checked" id="on_mail_server" name="create_folder" value="on_mail_server"/>
							<label for="on_mail_server">'.JS_LANG_OnMailServer.'</label><br />
							<input type="radio" class="wm_checkbox" id="in_webmail" name="create_folder" value="in_webmail"/>
							<label for="in_webmail">'.JS_LANG_InWebMail.'</label>
						</td>';
						$infoTable = '
						<table class="wm_secondary_info">
							<tr>
								<td class="wm_secondary_info">'.JS_LANG_InfoDeleteNotEmptyFolders.'</td>
							</tr>
						</table>';
					}

					$this->mainText ='
				<table class="wm_settings_manage_folders">
					<tr class="wm_settings_mf_headers" style="background: url(skins/'.$this->setaccount->_pagebuilder->SkinName().'/folders_back.gif) repeat-x; height: 20px;">
						<td style="width: 30px;"><input type="checkbox" id="ch_0" onclick="SelectAllInputs(this);" class="wm_checkbox" /></td>
						<td style="width: '.$FolderHeaderTdWidth.'" class="wm_settings_mf_folder">'.JS_LANG_Folder.'</td>
						<td style="width: 40px;">'.JS_LANG_Msgs.'</td>
						<td style="width: 40px;">'.JS_LANG_Size.'</td>
						<'.$SynkHeaderTd.'>'.JS_LANG_Synchronize.'</td>
						<td style="width: 100px;">'.JS_LANG_ShowThisFolder.'</td>
						<td style="width: 42px;"></td>
					</tr>
					'.
					$foldersLine[0]
					.'
					<tr class="wm_settings_mf_total" style="background: url(skins/'.$this->setaccount->_pagebuilder->SkinName().'/folders_back.gif) repeat-x; height: 20px;">
						<td></td>
						<td class="wm_settings_mf_folder">'.JS_LANG_Total.'</td>
						<td>'.$messagesCount.'</td>
						<td>'.GetFriendlySize($messagesSize).'</td>
						<td class="wm_settings_mf_page_switcher" colspan="3"></td>
					</tr>
				</table>
				'.$infoTable.'
				<table class="wm_settings_buttons">
					<tr>
						<td style="text-align: left;">
							<input class="wm_button" type="button" onclick="document.getElementById(\'new_folder\').className=\'\';" value="'.ConvertUtils::AttributeQuote(JS_LANG_AddNewFolder).'" />
							<input class="wm_button" type="button" onclick="if (!DoAlert()) return false; DeleteFolders();" value="'.ConvertUtils::AttributeQuote(JS_LANG_DeleteSelected).'" />
						</td>
					</tr>
				</table>	
				
				<div id="new_folder" class="wm_hide">
				<table class="wm_settings_part_info">
					<tr>
						<td>'.JS_LANG_NewFolder.'</td>
					</tr>
				</table>
				
				<table class="wm_settings_new_folder">
					<tr>
						<td class="wm_settings_title">
							'.JS_LANG_ParentFolder.':
						</td>
						<td>
							<form action="'.ACTIONFILE.'?action=new&req=folder" onsubmit="if (!DoAlert()) return false; return formSubmit()" method="POST">
							<select name="parentId">
								<option value="-1">'.JS_LANG_NoParent.'</option>
							'.
					$foldersLine[1]
							.'
							</select>
						</td>
					'.$newFolderCreate.'
					</tr>
					<tr>
						<td class="wm_settings_title">
							'.JS_LANG_FolderName.':
						</td>
						<td>
							<input class="wm_input" name="newFolderName" id="newFolderName" type="text" />
						</td>
					</tr>
				</table>

				<table class="wm_settings_buttons">
					<tr>
						<td>
							<input type="button" value="'.ConvertUtils::AttributeQuote(JS_LANG_Cancel).'" class="wm_button" onclick="document.getElementById(\'new_folder\').className=\'wm_hide\';" ID="Button3" NAME="Button1"/>
							<input type="submit" value="'.ConvertUtils::AttributeQuote(JS_LANG_OK).'" class="wm_button" ID="Button4" name="Button2"/>
							</form>
						</td>
					</tr>
				</table>
				</div>
				'	;
					break;
			}
			
		}
		
		/**
		 * @param FolderCollection $folders
		 * @param int $messagesCount
		 * @param int $messagesSize
		 * @param Account $account
		 * @return string
		 */
		function CreateHtmlFolderTree(&$folders, &$messagesCount, &$messagesSize, &$account)
		{
			$out = array('', '');
			for ($i = 0, $count = $folders->Count(); $i < $count; $i++)
			{
				$folder = &$folders->Get($i);
				if ($i > 0) $pr_folder = &$folders->Get($i - 1);
				if ($i < $count - 1) $nx_folder = &$folders->Get($i + 1);
				$messagesCount += $folder->MessageCount;
				$messagesSize += $folder->Size;
				$foldername = '';
				if (ConvertUtils::IsLatin($folder->Name))
				{
					$foldername = ConvertUtils::ConvertEncoding($folder->Name,
													CPAGE_UTF7_Imap,
													$this->setaccount->_pagebuilder->_proc->account->GetUserCharset());
				}
				else
				{
					$foldername = ConvertUtils::ConvertEncoding($folder->Name,
													$this->setaccount->_pagebuilder->_proc->account->DefaultIncCharset,
													$this->setaccount->_pagebuilder->_proc->account->GetUserCharset());
				}
				
				$folderUrl = 'skins/'.$this->setaccount->_pagebuilder->SkinName().'/folders/'.GetFolderImg($folder->Type, MAILPROTOCOL_POP3);
						
				$hasChild = ($folder->SubFolders != null);
				$hasMessages = ($folder->MessageCount > 0);
				$disab = ($hasChild || $hasMessages) ? 'disabled="disabled" ' : '';
				$disab = ($account->MailProtocol == MAILPROTOCOL_POP3) ? '' : $disab;
				$checkbox = ($folder->Type == FOLDERTYPE_Custom) ? '<input type="checkbox" id="ch_0" name="folders['.ConvertUtils::AttributeQuote($folder->IdDb).']" value="'.ConvertUtils::AttributeQuote($folder->FullName).'" class="wm_checkbox" '.$disab.'/>' : '';
				
				$uphref = ($i < 1) ? 
					'<img src="skins/'.$this->setaccount->_pagebuilder->SkinName().'/folders/up_inactive.gif" />' :
					'<a onclick="return DoAlert();" href="'.ACTIONFILE.'?action=update&req=folderorder&cf_id='.$folder->IdDb.'&rf_id='.$pr_folder->IdDb.'"><img
					src="skins/'.$this->setaccount->_pagebuilder->SkinName().'/folders/up.gif" /></a>';				
					
				$downhref = ($i > $count - 2) ? 
					'<img src="skins/'.$this->setaccount->_pagebuilder->SkinName().'/folders/down_inactive.gif" />' :
					'<a onclick="return DoAlert();" href="'.ACTIONFILE.'?action=update&req=folderorder&cf_id='.$folder->IdDb.'&rf_id='.$nx_folder->IdDb.'"><img
					src="skins/'.$this->setaccount->_pagebuilder->SkinName().'/folders/down.gif" /></a>';				
					
				$eyeimg = ($folder->Hide) ? 'hide.gif' : 'show.gif';
				
				switch ($folder->Type)
				{
					default:
					case FOLDERTYPE_Custom:		$foldername = ConvertUtils::WMHtmlSpecialChars($foldername); break;					
					case FOLDERTYPE_Inbox:		$foldername = FolderInbox; break;
					case FOLDERTYPE_Drafts:		$foldername = FolderDrafts; break;
					case FOLDERTYPE_SentItems:	$foldername = FolderSentItems; break;
					case FOLDERTYPE_Trash:		$foldername = FolderTrash; break;
				}
				
				$nameSting = ($folder->Type == FOLDERTYPE_Custom) ? 
					'<form action="'.ACTIONFILE.'?action=rename&req=folder" id="folder_form_'.$folder->IdDb.'" method="POST">
					<img src="'.$folderUrl.'" />
					<a href="#" onclick="EditFolder('.$folder->IdDb.');" id="folder_a_'.$folder->IdDb.'">'.$foldername.'</a>
					<input type="text" class="wm_hide" name="folderid" value="'.$folder->IdDb.'">
					<input type="text" class="wm_hide" name="fname" maxLength="30" id="folder_i_'.$folder->IdDb.'" value="'.ConvertUtils::AttributeQuote($foldername).'"/></form>' : '<img src="'.ConvertUtils::AttributeQuote($folderUrl).'" /> '.$foldername;
				
					
				$imap4SyncTd = ($this->setaccount->_editAccount->MailProtocol == MAILPROTOCOL_IMAP4) ?
						'
						<td><form id="syncform_'.$folder->IdDb.'" action="'.ACTIONFILE.'?action=update&req=foldersync" method="POST">
							<input type="hidden" name="folderid" value="'.$folder->IdDb.'">
							<select onchange="document.getElementById(\'syncform_'.$folder->IdDb.'\').submit();" name="synctype">
								<option value="'.FOLDERSYNC_DontSync.'" '.(($folder->SyncType == FOLDERSYNC_DontSync)?'selected="selected"':'').'>'.JS_LANG_SyncTypeNo.'</option>
								<option value="'.FOLDERSYNC_NewHeadersOnly.'" '.(($folder->SyncType == FOLDERSYNC_NewHeadersOnly)?'selected="selected"':'').'>'.JS_LANG_SyncTypeNewHeaders.'</option>
								<option value="'.FOLDERSYNC_AllHeadersOnly.'" '.(($folder->SyncType == FOLDERSYNC_AllHeadersOnly)?'selected="selected"':'').'>'.JS_LANG_SyncTypeAllHeaders.'</option>
								<option value="'.FOLDERSYNC_NewEntireMessages.'" '.(($folder->SyncType == FOLDERSYNC_NewEntireMessages)?'selected="selected"':'').'>'.JS_LANG_SyncTypeNewMessages.'</option>
								<option value="'.FOLDERSYNC_AllEntireMessages.'" '.(($folder->SyncType == FOLDERSYNC_AllEntireMessages)?'selected="selected"':'').'>'.JS_LANG_SyncTypeAllMessages.'</option>
								<option value="'.FOLDERSYNC_DirectMode.'" '.(($folder->SyncType == FOLDERSYNC_DirectMode)?'selected="selected"':'').'>'.JS_LANG_SyncTypeDirectMode.'</option>
							</select></form>
						</td>
							' : '<td class="wm_hide"></td>' ;
					
					
					
				$out[0] .= '
						<tr>
							<td>'.$checkbox.'</td>
							<td class="wm_settings_mf_folder">
							<div style="padding-left: '.($folder->Level * 8).'px;">
							'.$nameSting.'
							</div></td>
							<td>'.$folder->MessageCount.'</td>
							<td>'.GetFriendlySize($folder->Size).'</td>
							'.$imap4SyncTd.'
							<td><a href="#" onclick="if (!DoAlert()) return false; document.location=\''.ACTIONFILE.'?action=update&req=folderhide&folderid='.$folder->IdDb.'\'"><img class="wm_settings_mf_show_hide" src="skins/'.$this->setaccount->_pagebuilder->SkinName().'/folders/'.$eyeimg.'"></a>
							</td>
							<td class="wm_settings_mf_up_down">
								'.$uphref.$downhref.'
							</td>
						</tr>			
				';
				$out[1] .= '<option value="'.$folder->IdDb.'">'.(str_repeat('&nbsp;', $folder->Level*3)).$foldername.'</option>'."\r\n";
					
				if ($folder->SubFolders != null && $folder->SubFolders->Count() > 0)
				{
					$temp = $this->CreateHtmlFolderTree($folder->SubFolders, $messagesCount, $messagesSize, $account);
					$out[0] .= $temp[0];
					$out[1] .= $temp[1];
				}
			}
			return $out;
		}
		
		function CreateSelectFolderTreeForFilters(&$folders, $id = -1)
		{
			$out = '';
			
			for ($i = 0, $count = $folders->Count(); $i < $count; $i++)
			{
				$folder = &$folders->Get($i);
				if (ConvertUtils::IsLatin($folder->Name))
				{
					$foldername = ConvertUtils::ConvertEncoding($folder->Name,
													CPAGE_UTF7_Imap,
													$this->setaccount->_pagebuilder->_proc->account->GetUserCharset());
				}
				else
				{
					$foldername = ConvertUtils::ConvertEncoding($folder->Name,
													$this->setaccount->_pagebuilder->_proc->account->DefaultIncCharset,
													$this->setaccount->_pagebuilder->_proc->account->GetUserCharset());
				}	
				
				$out .= ($id == $folder->IdDb) ?
					'<option value="'.$folder->IdDb.'" selected="selected">'.(str_repeat('&nbsp;', $folder->Level*3)).$foldername.'</option>'."\r\n" :
					'<option value="'.$folder->IdDb.'">'.(str_repeat('&nbsp;', $folder->Level*3)).$foldername.'</option>';
				
				if ($folder->SubFolders != null && $folder->SubFolders->Count() > 0)
				{
					$out .= $this->CreateSelectFolderTreeForFilters($folder->SubFolders, $id);
				}	
			}
			return $out;
		}
		
		/**
		 * @return string
		 */
		function ToHTML()
		{
			
			
			$temp = '<script>function DoAlert(){return true;}</script>';
			
			
			return $temp.$this->mainText;
		}
	}
	
	class SettingsContacts
	{
		/**
		 * @var PageBuilder
		 */
		var $_pagebuilder;
		
		/**
		 * @var BaseProcessor
		 */
		var $_proc;
		
		/**
		 * @var array
		 */
		var $data;
		
		/**
		 * @param PageBuilder $pagebuilder
		 * @return SettingsAccount
		 */
		function SettingsContacts(&$pagebuilder)
		{
			$this->data = array();
			$this->_pagebuilder = &$pagebuilder;
			$this->_proc = &$pagebuilder->_proc;
			
			$this->data['contactsPerPage'] = $this->_proc->account->ContactsPerPage;
		}
		
		function ToHTML()
		{

			return '
<table class="wm_settings">
		<tr>
			<td class="wm_settings_nav">
				'.NavigationPrint($this->_pagebuilder, 2).'
			</td>
			<td class="wm_settings_cont">
				<table class="wm_settings_common">
					<tr>
						<td>
							<form action="'.ACTIONFILE.'?action=save&req=contactset" method="POST">
							'.JS_LANG_ContactsPerPage.': <input class="wm_input" type="text" size="2" id="contacts_per_page"
							name="contacts_per_page" value="'.ConvertUtils::AttributeQuote($this->data['contactsPerPage']).'" maxlength="2" />
						</td>
					</tr>
				</table>

				<table class="wm_settings_buttons">
					<tr>
						<td>
							<input class="wm_button" type="submit" name="subm" value="'.ConvertUtils::AttributeQuote(JS_LANG_Save).'" ID="Submit1"/>
							</form>
						</td>
					</tr>
				</table>

			</td>
		</tr>
	</table>
						
			';
		}
	}
	
	/**
	 * @param PageBuilder $pagebuilder
	 * @param int $id_selected
	 * @return string
	 */
	function NavigationPrint(&$pagebuilder, $id_selected)
	{
		$selectedArray = array('', '', '');
		if ($id_selected > -1 && $id_selected < 3)
		{
			$selectedArray[$id_selected] = ' class="wm_selected_settings_item"';
		}

		$temp = ($pagebuilder->_proc->account->AllowChangeSettings) ? SET_ACCOUNT_PROF : SET_ACCOUNT_FILTERS;
		
		return '
				<div'.$selectedArray[0].'>
					<nobr><img src="skins/'.$pagebuilder->SkinName().'/settings/menu_common.gif" /> <a href="'.BASEFILE.'?'.SCREEN.'='.SET_COMMON.'">'.JS_LANG_Common.'</a></nobr>
				</div>
				<div'.$selectedArray[1].'>
					<nobr><img src="skins/'.$pagebuilder->SkinName().'/settings/menu_accounts.gif" /> <a href="'.BASEFILE.'?'.SCREEN.'='.$temp.'">'.JS_LANG_EmailAccounts.'</a></nobr>
				</div>
				<div'.$selectedArray[2].'>
					<nobr><img src="skins/'.$pagebuilder->SkinName().'/settings/menu_contacts.gif" /> <a href="'.BASEFILE.'?'.SCREEN.'='.SET_CONTACTS.'">'.JS_LANG_Contacts.'</a></nobr>
				</div>
				';
	}
