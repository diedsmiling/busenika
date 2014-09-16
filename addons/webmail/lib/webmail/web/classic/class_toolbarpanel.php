<?php
	
	define('BUTTONTYPE_NEWMESSAGE', 1);
	define('BUTTONTYPE_CHECKMAIL', 2);
	define('BUTTONTYPE_FORWARD', 3);
	define('BUTTONTYPE_DELETE', 4);
	define('BUTTONTYPE_SAVE', 5);
	define('BUTTONTYPE_PRINT', 6);
	define('BUTTONTYPE_SEND', 12);
	
	define('BUTTONTYPE_RELOADFOLDER', 10);
	define('BUTTONTYPE_EMPTYTRASH', 11);
	
	define('BUTTONTYPE_BACK', 14);
	
	define('BUTTONTYPE_NEWGROUP', 7);
	define('BUTTONTYPE_NEWCONTACT', 8);
	define('BUTTONTYPE_IMPORTCONTACTS', 9);
	
	define('BUTTONTYPE_TEST', 99);

	class ToolbarPanel
	{
		/**
		 * @var BaseProcessor
		 */
		var $_proc;
		
		/**
		 * @var PageBuilder
		 */
		var $_pagebuilder;
			
		/**
		 * @var AccounSelectDiv
		 */
		var $_accountSelect;
		
		/**
		 * @var ButtonToolBar
		 */
		var $_buttontoolbar = null;
		
		/**
		 * @param PageBuilder $pageBuilder
		 * @return ToolbarPanel
		 */
		function ToolbarPanel(&$pagebuilder)
		{
			$this->_pagebuilder = &$pagebuilder;
			$this->_proc = &$pagebuilder->_proc;
			$this->_accountSelect = &new AccountSelectDiv($pagebuilder);
			
			$screen = $this->_proc->sArray[SCREEN];
			if ($screen == SET_COMMON || $screen == SET_ACCOUNT_PROF || $screen == SET_ACCOUNT_ADDACC ||
				$screen == SET_ACCOUNT_FILTERS || $screen == SET_ACCOUNT_SIGNATURE ||
				$screen == SET_ACCOUNT_MFOLDERS ||  $screen == SET_CONTACTS || $screen == SCREEN_SETTINGS)
			{}
			else 
			{
				$this->_buttontoolbar = &new ButtonToolBar($pagebuilder);
			}
		}
		
		function ToHTML()
		{
			$hideSettings = '
				<span class="wm_accountslist_settings">
					<a href="'.BASEFILE.'?'.SCREEN.'='.SCREEN_SETTINGS.'">'.JS_LANG_Settings.'</a>
				</span>';
			
			$hideContacts = (!$this->_proc->settings->AllowContacts) ? '' :
				'<span class="wm_accountslist_contacts">
					<a href="'.BASEFILE.'?'.SCREEN.'='.SCREEN_CONTACTS.'">'.JS_LANG_Contacts.'</a>
				</span>';
				
			$buttonstool = ($this->_buttontoolbar) ? $this->_buttontoolbar->ToHTML() : '';
			
			return '
		<table class="wm_accountslist" id="accountslist">
		  <tr>
			<td>
			'
			.
			$this->_accountSelect->doTitle()
			.
			$hideContacts
			.
			'	<span class="wm_accountslist_logout">
					<a href="'.LOGINFILE.'?mode=logout" onclick="EraseCookie(\'awm_autologin_data\'); EraseCookie(\'awm_autologin_id\');">'.JS_LANG_Logout.'</a>
				</span>
				'
				.
				$hideSettings
				.
				'
			</td>
		  </tr>
		</table>'. $buttonstool;
			
		}
	}

	class AccountSelectDiv
	{
		/**
		 * @var PageBuilder
		 */
		var $_pagebuilder;
		
		/**
		 * @var array
		 */
		var $_accounts;
		
		/**
		 * @param PageBuilder $pageBuilder
		 * @return AccounSelectDiv
		 */
		function AccountSelectDiv(&$pagebuilder)
		{
			$this->_pagebuilder = &$pagebuilder;
			$this->_accounts = $this->_pagebuilder->_proc->GetAccounts();
			
			$array1 = $array2 = $this->_accounts;
			foreach ($array1 as $key1 => $value1)
			{
				foreach ($array2 as $key2 => $value2)
				{
					if ($value2[4] == $value1[4] && $key2 != $key1)
					{
						//$temp = ($value1[0] == MAILPROTOCOL_POP3) ? ' (Pop3)' : ' (Imap4)';
						$temp = '';
						$this->_accounts[$key1][4] = $this->_accounts[$key1][4].$temp;
					}
				}
			}

			$this->ToHideDiv();
			if ($this->Count() > 1)
			{		
				$this->_pagebuilder->AddInitText('PopupMenu.addItem(document.getElementById("popup_menu_1"), document.getElementById("popup_control_1"), "wm_account_menu", document.getElementById("popup_replace_1"), document.getElementById("popup_replace_1"), "", "", "", "");');
			}
		}
		
		function Count()
		{
			return count($this->_accounts);
		}
		
		function doTitle()
		{
			$class = ($this->Count() < 2) ? 'class="wm_hide"' : 'class="wm_accounts_arrow"';
	
			return '
				<span class="wm_accountslist_email" id="popup_replace_1">
					<a href="'.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX.'">'.ConvertUtils::WMHtmlSpecialChars($this->_accounts[$this->_pagebuilder->_proc->account->Id][4]).'</a>
				</span>			
				<span class="wm_accountslist_selection">
					<img '.$class.' id="popup_control_1" src="skins/'.$this->_pagebuilder->SkinName().'/menu/accounts_arrow.gif"
					onmousedown="this.src=\'skins/'.$this->_pagebuilder->SkinName().'/menu/accounts_arrow_down.gif\'" onmouseup="this.src=\'skins/'.$this->_pagebuilder->SkinName().'/menu/accounts_arrow_over.gif\'"
					onmouseover="this.src=\'skins/'.$this->_pagebuilder->SkinName().'/menu/accounts_arrow_over.gif\'" onmouseout="this.src=\'skins/'.$this->_pagebuilder->SkinName().'/menu/accounts_arrow.gif\'" />
				</span>	';
		}
		
		function ToHideDiv()
		{
			$out = '';
			if ($this->Count() > 1)
			{
				$out .= '<div class="wm_hide" id="popup_menu_1" >';
				foreach ($this->_accounts as $acct_id => $acctArray)
				{
					if ($acct_id != $this->_pagebuilder->_proc->account->Id)
					{
						$out .= '<div class="wm_account_item" onclick="document.location.replace(\''.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX.'&'.CHANGE_ACCID.'='.$acct_id.'\');"
								onmouseover="this.className=\'wm_account_item_over\';" onmouseout="this.className=\'wm_account_item\';">'.ConvertUtils::WMHtmlSpecialChars($acctArray[4]).'</div>';	
					}
				}
				$out.= '</div>';
			}
			
			$this->_pagebuilder->AddHideDiv($out);
		}
	}

	class ButtonToolBar
	{
		
		/**
		 * @var PageBuilder
		 */
		var $_pagebuilder;
		
		/**
		 * @var array
		 */
		var $_buttons;
	
		/**
		 * @var SearchFormClass
		 */
		var $_searchform = null; 
		
		/**
		 * @param PageBuilder $pagebuilder
		 * @return ButtonToolBar
		 */
		function ButtonToolBar(&$pagebuilder)
		{
			$this->_pagebuilder = &$pagebuilder; 
			$screen = $this->_pagebuilder->_proc->sArray[SCREEN];
			
			if (!$this->_pagebuilder->_proc->settings->ShowTextLabels)
			{
				$this->_pagebuilder->AddInitText('
var ImgKey = document.getElementById("checkImgId");
var isDisplay;
if(ImgKey) {
	if (isDisplay = ReadStyle(ImgKey, "display"))	{
		if (isDisplay && isDisplay == "none") {
			var toolbartable = document.getElementById("toolbar");
			if (toolbartable) {
				var spans = toolbartable.getElementsByTagName("span");
				var i, c;
				if (spans) {
					for (i = 0, c = spans.length; i < c; i++) {
						spans[i].className = "";
					}
				}
			}
		}
	}
}
				');
			}
			
			switch ($screen)
			{
				default:
				case SCREEN_MAILBOX:
					$this->AddSimpleButton(BUTTONTYPE_NEWMESSAGE);
					$this->AddSimpleButton(BUTTONTYPE_CHECKMAIL);
					
					if ($this->_pagebuilder->_proc->account->MailProtocol == MAILPROTOCOL_IMAP4)
							$this->AddSimpleButton(BUTTONTYPE_RELOADFOLDER);
					
					if ($this->_pagebuilder->_proc->currentFolder && $this->_pagebuilder->_proc->currentFolder->Type != FOLDERTYPE_Drafts)
					{
						$this->_buttons[] = &new ReplyButton($pagebuilder); 
						$this->AddSimpleButton(BUTTONTYPE_FORWARD);
					}
					
					if ($this->_pagebuilder->_proc->currentFolder && 
						$this->_pagebuilder->_proc->currentFolder->SyncType == FOLDERSYNC_DirectMode)
					{
						if ($this->_pagebuilder->_proc->account->MailProtocol == MAILPROTOCOL_IMAP4)
						{
							$this->_buttons[] = &new MarkButton($pagebuilder);
							$this->_buttons[] = &new MoveToFolderButton($pagebuilder); 							
						}
						else 
						{
							$this->_buttons[] = &new MoveToFolderButton($pagebuilder); 							
						}
					}
					else
					{
						$this->_buttons[] = &new MarkButton($pagebuilder);
						$this->_buttons[] = &new MoveToFolderButton($pagebuilder); 
					}
					
					if (USEIMAPTRASH)
					{
						$this->AddSimpleButton(BUTTONTYPE_DELETE);
						if ($this->_pagebuilder->_proc->currentFolder && 
							$this->_pagebuilder->_proc->currentFolder->Type == FOLDERTYPE_Trash)
						{
							$this->AddSimpleButton(BUTTONTYPE_EMPTYTRASH);
						}
					}
					else
					{
						if ($this->_pagebuilder->_proc->account->MailProtocol == MAILPROTOCOL_IMAP4)
						{
							$this->_buttons[] = &new IMAP_Delete($pagebuilder);	
						}
						else
						{
							$this->AddSimpleButton(BUTTONTYPE_DELETE);
						}
						
						if ($this->_pagebuilder->_proc->currentFolder && 
							$this->_pagebuilder->_proc->currentFolder->Type == FOLDERTYPE_Trash &&
							$this->_pagebuilder->_proc->account->MailProtocol != MAILPROTOCOL_IMAP4)
						{
							$this->AddSimpleButton(BUTTONTYPE_EMPTYTRASH);
						}
					}
					
					if ($screen != SCREEN_NEWOREDIT)
					{
						if ($this->_pagebuilder->_proc->currentFolder)
						{
							if ($this->_pagebuilder->_proc->currentFolder->SyncType != FOLDERSYNC_DirectMode)
							{
								$this->_searchform = &new SearchFormClass($pagebuilder); 
							}
						}						
						else
						{
							$this->_searchform = &new SearchFormClass($pagebuilder); 
						}
					}
					
					break;
					
				case SCREEN_CONTACTS:
					$this->AddSimpleButton(BUTTONTYPE_BACK);
					$this->AddSimpleButton(BUTTONTYPE_NEWMESSAGE);
					$this->AddSimpleButton(BUTTONTYPE_NEWCONTACT);
					$this->AddSimpleButton(BUTTONTYPE_NEWGROUP);
					
					$this->_buttons[] = &new AddContactToButton($pagebuilder); 
					
					$this->AddSimpleButton(BUTTONTYPE_DELETE);
					$this->AddSimpleButton(BUTTONTYPE_IMPORTCONTACTS);
					
					$this->_searchform = &new SearchFormContactsClass($pagebuilder); 
					break;
				case SCREEN_FULLSCREEN:
					$this->AddSimpleButton(BUTTONTYPE_BACK);
					$this->AddSimpleButton(BUTTONTYPE_NEWMESSAGE);

					$this->_buttons[] = &new ReplyButton($pagebuilder); 
					$this->AddSimpleButton(BUTTONTYPE_FORWARD);
					
					$this->AddSimpleButton(BUTTONTYPE_PRINT);
					$this->AddSimpleButton(BUTTONTYPE_SAVE);
					$this->AddSimpleButton(BUTTONTYPE_DELETE);
					
					break;
				case SCREEN_NEWOREDIT:
					$this->AddSimpleButton(BUTTONTYPE_BACK);
					$this->AddSimpleButton(BUTTONTYPE_SEND);
					$this->AddSimpleButton(BUTTONTYPE_SAVE);
					$this->_buttons[] = &new PriorityButton($pagebuilder); 
					break;
			}
			
		}
		
		/**
		 * @param int $type
		 */
		function AddSimpleButton($type)
		{
			$this->_buttons[] = &new ToolbarButton($this->_pagebuilder, $type);
		}
		
		/**
		* @return string
		*/
		function ButtonsToHtml()
		{
			$out = '';
		
			for ($i = 0, $c = count($this->_buttons); $i < $c; $i++)
			{
				$button = &$this->_buttons[$i];
				$out .= $button->ToHTML();
			}
		
			return $out;
		} 
		
		function ToHTML()
		{
			$search = ($this->_searchform) ? $this->_searchform->ToHTML() : '';
			return '
<table class="wm_toolbar" id="toolbar">
  <tr>
	<td>'
		.
		$this->ButtonsToHtml()
		.
		$search
		.
		'
	</td>
  </tr>
</table>
'; 
		}
		
	}

class SearchFormClass
{
	/**
	 * @var PageBuilder
	 */
	var $_pagebuilder;
	
	/**
	 * @var string
	 */
	var $_hidePart = '';
	
	function SearchFormClass(&$pagebuilder)
	{
		$this->_pagebuilder = &$pagebuilder;
		$this->_hidePart = $this->ToHideDiv();
		$this->_pagebuilder->AddJSText('var SearchForm;');
		$this->_pagebuilder->AddInitText('
SearchForm = new CSearchForm(document.getElementById("search_form"), document.getElementById("search_small_form"), document.getElementById("search_control"), document.getElementById("search_control_img"), "search_form", document.getElementById("bigLookFor"), document.getElementById("smallLookFor"), "'.$this->_pagebuilder->SkinName().'");
SearchForm.Show();
		');
		$this->_pagebuilder->_top->AddOnClick('SearchForm.checkVisibility(event, Browser.Mozilla);');
	}
	
	/**
	 * @param FolderCollection $folderCollection
	 * @param int $selectId
	 * @return string
	 */
	function CreateFolderTree($folderCollection, $selectId = -1)
	{
		$out = '';
		for ($i = 0, $c = $folderCollection->Count(); $i < $c; $i++)
		{
			$folder = &$folderCollection->Get($i);
			
			$foldername = '';
			
			if (ConvertUtils::IsLatin($folder->Name))
			{
				$foldername = ConvertUtils::ConvertEncoding($folder->Name,
												CPAGE_UTF7_Imap,
												$this->_pagebuilder->_proc->account->GetUserCharset());
			}
			else
			{
				$foldername = ConvertUtils::ConvertEncoding($folder->Name,
												$this->_pagebuilder->_proc->account->DefaultIncCharset,
												$this->_pagebuilder->_proc->account->GetUserCharset());
			}

			if (!$folder->Hide && $folder->SyncType != FOLDERSYNC_DirectMode)
			{
				$isSelect = ($folder->IdDb == $selectId) ? 'selected="selected"' : '';
				$out .= '<option value="'.$folder->IdDb.'" '.$isSelect.'>'.str_repeat('&nbsp;', ((int) $folder->Level) * 3);
				
				switch ($folder->Type)
				{
					default:
					case FOLDERTYPE_Custom:		$out .= ConvertUtils::WMHtmlSpecialChars($foldername); break;					
					case FOLDERTYPE_Inbox:		$out .= FolderInbox; break;
					case FOLDERTYPE_Drafts:		$out .= FolderDrafts; break;
					case FOLDERTYPE_SentItems:	$out .= FolderSentItems; break;
					case FOLDERTYPE_Trash:		$out .= FolderTrash; break;
				}				
				
				$out .= '</option>'."\r\n";
				
				if ($folder->SubFolders != null && $folder->SubFolders->Count() > 0)
				{
					$out .= $this->CreateFolderTree($folder->SubFolders, $selectId);
				}	
			}
		}
		return $out;
	}
	
	function ToHTML()
	{
		$currentFolderId = ($this->_pagebuilder->_proc->currentFolder) ? $this->_pagebuilder->_proc->currentFolder->IdDb : -1;
		$text = (isset($this->_pagebuilder->_proc->sArray[SEARCH_ARRAY][S_TEXT])) ? $this->_pagebuilder->_proc->sArray[SEARCH_ARRAY][S_TEXT] : '';
		return '
<div class="wm_toolbar_search_item" style="margin-left: 0px;" id="search_control">
	<img class="wm_search_arrow" id="search_control_img" src="skins/'
		.$this->_pagebuilder->SkinName().'/menu/arrow_down.gif" />
</div>
<div class="wm_toolbar_search_item" onmouseover="this.className=\'wm_toolbar_search_item_over\'" 
onmouseout="this.className=\'wm_toolbar_search_item\'" id="search_small_form" style="margin-right: 0px;">
	<form action="?'.S_GETMODE.'=mini" method="POST" id="searchformmini">
	<input type="hidden" id="qfolder" name="qfolder"  value="'.$currentFolderId.'"/>
	<input type="text" id="smallLookFor" name="smallLookFor" class="wm_search_input" value="'.ConvertUtils::AttributeQuote($text).'" >
	<img class="wm_menu_small_search_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/search_button.gif" 
	onclick="document.getElementById(\'searchformmini\').submit();" >
	</form>
</div>	
'. $this->_hidePart;
	}
	
	function ToHideDiv()
	{
		$currentFolderId = ($this->_pagebuilder->_proc->currentFolder) ? $this->_pagebuilder->_proc->currentFolder->IdDb : -1;
		return '
<div class="wm_hide" id="search_form">
<form action="?'.S_GETMODE.'=big" method="POST" id="searchformbig">
	<table>
		<tr>
			<td class="wm_search_title">
				'.JS_LANG_LookFor.'
			</td>
			<td class="wm_search_value">
				<input type="text" id="bigLookFor" name="bigLookFor" class="wm_search_input" value="" />
				<img class="wm_menu_big_search_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/search_button_big.gif" 
					onclick="document.getElementById(\'searchformbig\').submit();"
				/>
			</td>
		</tr>
		<tr>
			<td class="wm_search_title">
				'.JS_LANG_SearchIn.'
			</td>
			<td class="wm_search_value">
				<select name="qfolder">
					<option value="-2" selected="selected">'.JS_LANG_AllMailFolders.'</option>
					'.
					$this->CreateFolderTree($this->_pagebuilder->_proc->GetFolders(), $currentFolderId)
					.'
				</select>
			</td>
		</tr>
		<tr>
			<td class="wm_search_value" colspan="2">
				<input type="radio" name="qmmode" id="qmmode1" value="onlyheaders" checked="checked" class="wm_checkbox" />
				<label for="qmmode1">'.JS_LANG_QuickSearch.'</label>
			</td>
		</tr>
  		<tr>
  			<td class="wm_search_value" colspan="2">
  				<input type="radio" name="qmmode" id="qmmode2" value="allmessage" class="wm_checkbox" />
  				<label for="qmmode2">'.JS_LANG_SlowSearch.'</label>
  			</td>
  		</tr>
	</table>
</form>
</div>';
	}
	
} 

class ToolbarButton
{
	
	/**
	 * @var string
	 */
	var $_skinName;
	
	/**
	 * @var string
	 */
	var $_imgfile;
	
	/**
	 * @var string
	 */
	var $_onclick;
	
	/**
	 * @var string
	 */
	var $_title;
	
	/**
	 * @var string
	 */
	var $_class = '';
	
	/**
	 * @var bool
	 */
	var $_withText = true;
	
	var $_buttonType;
	
	/**
	 * @param PageBuilder $pagebuilder
	 * @param int $buttonType
	 * @return ToolbarButton
	 */
	function ToolbarButton(&$pagebuilder, $buttonType)
	{
		$this->_skinName = $pagebuilder->SkinName();
		$this->_withText = $pagebuilder->_proc->settings->ShowTextLabels;
		$this->_buttonType = $buttonType;
		
		switch ($buttonType)
		{
			default:
			case BUTTONTYPE_NEWMESSAGE:
				$this->_imgfile = 'new_message.gif';
				$this->_onclick = 'DoNewMessageButton();';
				$this->_title = JS_LANG_NewMessage;
				$this->_class = 'wm_menu_new_message_img';
				break;
			case BUTTONTYPE_CHECKMAIL:
				$this->_imgfile = 'check_mail.gif';
				$this->_onclick = 'CheckMail.Start();';
				$this->_title = JS_LANG_CheckMail;
				$this->_class = 'wm_menu_check_mail_img';
				break;
			case BUTTONTYPE_FORWARD:
				$this->_imgfile = 'forward.gif';
				$this->_onclick = 'DoForwardButton();';
				$this->_title = JS_LANG_Forward;
				$this->_class = 'wm_menu_forward_img';
				break;
			case BUTTONTYPE_DELETE:
				$this->_imgfile = 'delete.gif';
				$this->_onclick = 'DoDeleteButton();';
				$this->_title = JS_LANG_Delete;
				$this->_class = 'wm_menu_delete_img';
				break;
			
			case BUTTONTYPE_EMPTYTRASH:
				$fid = ($pagebuilder->_proc->currentFolder) ? $pagebuilder->_proc->currentFolder->IdDb : '-1';
				$this->_imgfile = 'empty_trash.gif';
				$this->_onclick = 'confirm(\''.JS_LANG_ConfirmAreYouSure.'\') ? document.location=\''.ACTIONFILE.'?action=delete&req=trash&f='.$fid.'\' : \'\';';
				$this->_title = JS_LANG_EmptyTrash;
				$this->_class = 'wm_menu_empty_trash_img';
				break;
			case BUTTONTYPE_SAVE:
				$this->_imgfile = 'save.gif';
				$this->_onclick = 'DoSaveButton();';
				$this->_title = JS_LANG_SaveMessage;
				$this->_class = 'wm_menu_save_message_img';
				break;
			case BUTTONTYPE_SEND:
				$this->_imgfile = 'send.gif';
				$this->_onclick = 'DoSendButton();';
				$this->_title = JS_LANG_SendMessage;
				$this->_class = 'wm_menu_send_message_img';
				break;	
			case BUTTONTYPE_PRINT:
				$this->_imgfile = 'print.gif';
				$this->_onclick = 'DoPrintButton();';
				$this->_title = JS_LANG_Print;
				$this->_class = 'wm_menu_print_message_img';
				break;
			case BUTTONTYPE_RELOADFOLDER:
				$this->_imgfile = 'reload_folders.gif';
				$this->_onclick = 'DoReloadFolderButton();';
				$this->_title = JS_LANG_ReloadFolders;
				$this->_class = 'wm_menu_reload_folders_img';
				break;
			case BUTTONTYPE_BACK:
				$this->_imgfile = 'back_to_list.gif';
				$this->_onclick = 'document.location=\''.BASEFILE.'?'.SCREEN.'='.SCREEN_MAILBOX.'\'';
				$this->_title = BackToList;
				$this->_class = 'wm_menu_back_to_list_img';
				break;
				
			/* contacts */
			case BUTTONTYPE_NEWGROUP:
				$this->_imgfile = 'new_group.gif';
				$this->_onclick = 'document.location=\''.BASEFILE.'?'.CONTACT_MODE.'='.G_NEW.'\'';
				$this->_title = JS_LANG_NewGroup;
				$this->_class = 'wm_menu_new_group_img';
				break;
			case BUTTONTYPE_NEWCONTACT:
				$this->_imgfile = 'new_contact.gif';
				$this->_onclick = 'document.location=\''.BASEFILE.'?'.CONTACT_MODE.'='.C_NEW.'\'';
				$this->_title = JS_LANG_NewContact;
				$this->_class = 'wm_menu_new_contact_img';
				break;
			case BUTTONTYPE_IMPORTCONTACTS:
				$this->_imgfile = 'import_contacts.gif';
				$this->_onclick = 'document.location=\''.BASEFILE.'?'.CONTACT_MODE.'='.C_IMPORT.'\'';
				$this->_title = JS_LANG_ImportContacts;
				$this->_class = 'wm_menu_import_contacts_img';
				break;	
		}
	}
	
	/**
	 * @return string
	 */
	function ToHTML()
	{
		$idForCheck = ($this->_buttonType == BUTTONTYPE_SEND || $this->_buttonType == BUTTONTYPE_NEWMESSAGE) 
			? 'id="checkImgId"' : '';
		$textClass = ($this->_withText) ? '' : 'wm_hide';
		return '
<div class="wm_toolbar_item" 
	onmouseover="this.className=\'wm_toolbar_item_over\'" 
	onmouseout="this.className=\'wm_toolbar_item\'"
	onclick="'.$this->_onclick.'">
		<img '.$idForCheck.' class="'.$this->_class.'" src="skins/'.$this->_skinName.'/menu/'
		.$this->_imgfile.'" title="'.ConvertUtils::AttributeQuote($this->_title).'" /><span class="'.$textClass.'">'.$this->_title.'</span></div>';
	}
	
}

class AddContactToButton
{
	/**
	 * @var PageBuilder
	 */
	var $_pagebuilder;
	
	/**
	 * @param PageBuilder $pagebuilder
	 * @return AddContactToButton
	 */
	function AddContactToButton(&$pagebuilder)
	{
		$this->_pagebuilder = &$pagebuilder;
		$this->ToHideDiv();
		$this->_pagebuilder->AddInitText('PopupMenu.addItem(document.getElementById("popup_menu_7"), document.getElementById("popup_control_7"), "wm_popup_menu", document.getElementById("popup_control_7"), document.getElementById("popup_control_7"), "wm_toolbar_item", "wm_toolbar_item_press", "wm_toolbar_item", "wm_toolbar_item_over");');
	}
	
	function ToHTML()
	{
		$textClass = ($this->_pagebuilder->_proc->settings->ShowTextLabels) ? '' : 'wm_hide';
		return '
<div class="wm_toolbar_item" id="popup_control_7"> 
	<img class="wm_menu_add_contacts_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/add_contacts_to.gif" />
	<span class="'.$textClass.'">'.JS_LANG_AddContactsTo.'</span>
	<img class="wm_menu_move_control_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/popup_menu_arrow.gif" />
</div>
		';
	}
	
	/**
	 * @return String
	 */
	function CreateGroupsHtml()
	{
		$out = '';
		$groupNames = &$this->_pagebuilder->_proc->db->SelectUserAddressGroupNames();
		foreach ($groupNames as $id => $name)
		{
			$out .= '<div onclick="AddContactsToGroup('.$id.', \''.
									ConvertUtils::AttributeQuote(
										ConvertUtils::ClearJavaScriptString($name, '\'')
										).'\');" class="wm_menu_item" onmouseover="this.className=\'wm_menu_item_over\';" onmouseout="this.className=\'wm_menu_item\';">'.ConvertUtils::WMHtmlSpecialChars($name).'</div>';
		}
		return $out;
	}
	
	function ToHideDiv()
	{
		$this->_pagebuilder->AddHideDiv('
<div class="wm_hide" id="popup_menu_7">'
	.
	$this->CreateGroupsHtml()
	.
	'
</div>');
	}
}

class MoveToFolderButton
{
	/**
	 * @var PageBuilder
	 */
	var $_pagebuilder;
	
	/**
	 * @param PageBuilder $pagebuilder
	 * @return MoveToFolderButton
	 */
	function MoveToFolderButton(&$pagebuilder)
	{
		$this->_pagebuilder = &$pagebuilder;
		$this->ToHideDiv();
		$this->_pagebuilder->AddInitText('PopupMenu.addItem(document.getElementById("popup_menu_2"), document.getElementById("popup_control_2"), "wm_popup_menu", document.getElementById("popup_control_2"), document.getElementById("popup_control_2"), "wm_toolbar_item", "wm_toolbar_item_press", "wm_toolbar_item", "wm_toolbar_item_over");');
	}
	
	function ToHTML()
	{
		$textClass = ($this->_pagebuilder->_proc->settings->ShowTextLabels) ? '' : 'wm_hide';
		return '
<div class="wm_toolbar_item" id="popup_control_2"> 
	<img class="wm_menu_move_to_folder_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/move_to_folder.gif" />
	<span class="'.$textClass.'">'.JS_LANG_MoveToFolder.'</span>
	<img class="wm_menu_move_control_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/popup_menu_arrow.gif" />
</div>
		';
	}
	
	/**
	 * @param FolderCollection $folderCollection
	 * @return String
	 */
	function CreateFolderTree($folderCollection)
	{
		$out = '';
		for ($i = 0, $c = $folderCollection->Count(); $i < $c; $i++)
		{
			$folder = &$folderCollection->Get($i);
			
			if ($folder->Hide) continue;
			if ($folder->SyncType == FOLDERSYNC_DirectMode && $this->_pagebuilder->_proc->account->MailProtocol == MAILPROTOCOL_POP3) continue;
			
			$foldername = '';
			
			if (ConvertUtils::IsLatin($folder->Name))
			{
				$foldername = ConvertUtils::ConvertEncoding($folder->Name,
												CPAGE_UTF7_Imap,
												$this->_pagebuilder->_proc->account->GetUserCharset());
			}
			else
			{
				$foldername = ConvertUtils::ConvertEncoding($folder->Name,
												$this->_pagebuilder->_proc->account->DefaultIncCharset,
												$this->_pagebuilder->_proc->account->GetUserCharset());
			}
			
			$out .= '<div onclick="MoveToFolder(\''.$folder->IdDb.'\');" class="wm_menu_item" onmouseover="this.className=\'wm_menu_item_over\';" onmouseout="this.className=\'wm_menu_item\';">'.str_repeat('&nbsp;', ((int) $folder->Level) * 4);
			switch ($folder->Type)
			{
				default:
				case FOLDERTYPE_Custom:		$out .= ConvertUtils::WMHtmlSpecialChars($foldername); break;					
				case FOLDERTYPE_Inbox:		$out .= FolderInbox; break;
				case FOLDERTYPE_Drafts:		$out .= FolderDrafts; break;
				case FOLDERTYPE_SentItems:	$out .= FolderSentItems; break;
				case FOLDERTYPE_Trash:		$out .= FolderTrash; break;
			}
			
			$out .= '</div>';
			
			if ($folder->SubFolders != null && $folder->SubFolders->Count() > 0)
			{
				$out .= $this->CreateFolderTree($folder->SubFolders);
			}				
		}
		return $out;
	}
	
	function ToHideDiv()
	{
		$this->_pagebuilder->AddHideDiv('
<div class="wm_hide" id="popup_menu_2">'
	.
	$this->CreateFolderTree($this->_pagebuilder->_proc->GetFolders())
	.
	'
</div>');
	}
}

class MarkButton
{
	
	/**
	 * @var PageBuilder
	 */
	var $_pagebuilder;
	
	/**
	 * @param PageBuilder $pagebuilder
	 * @return MarkButton
	 */
	function MarkButton(&$pagebuilder)
	{
		$this->_pagebuilder = &$pagebuilder;
		$this->isSearch = (isset($pagebuilder->_proc->sArray[SEARCH_ARRAY]) && strlen($pagebuilder->_proc->sArray[SEARCH_ARRAY][S_TEXT]) > 0);
		$this->_pagebuilder->AddInitText('PopupMenu.addItem(document.getElementById("popup_menu_3"), document.getElementById("popup_control_3"), "wm_popup_menu", document.getElementById("popup_replace_3"), document.getElementById("popup_title_3"), "wm_tb", "wm_tb_press", "wm_toolbar_item", "wm_toolbar_item_over");');
	}
	
	function ToHTML()
	{
		$textClass = ($this->_pagebuilder->_proc->settings->ShowTextLabels) ? '' : 'wm_hide';
		$temp = ($this->isSearch) ? '' : '
		<div class="wm_menu_separate"></div>
	<div onclick="document.location=\''.ACTIONFILE.'?action=groupoperation&req=mark_all_read&f='.$this->_pagebuilder->_proc->sArray[FOLDER_ID].'\'" class="wm_menu_item" onmouseover="this.className=\'wm_menu_item_over\';" onmouseout="this.className=\'wm_menu_item\';">
		<img class="wm_menu_mark_all_read_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/mark_all_read.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_MarkAllRead).'" />
		<span class="'.$textClass.'">'.JS_LANG_MarkAllRead.'</span>
	</div>
	<div onclick="document.location=\''.ACTIONFILE.'?action=groupoperation&req=mark_all_unread&f='.$this->_pagebuilder->_proc->sArray[FOLDER_ID].'\'" class="wm_menu_item" onmouseover="this.className=\'wm_menu_item_over\';" onmouseout="this.className=\'wm_menu_item\';">
		<img class="wm_menu_mark_all_unread_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/mark_all_unread.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_MarkAllUnread).'" />
		<span class="'.$textClass.'">'.JS_LANG_MarkAllUnread.'</span>
	</div>';
		
		return '
<div id="popup_replace_3" class="wm_tb">
	<div class="wm_toolbar_item" id="popup_title_3" onclick="DoReadMessages();">
		<img class="wm_menu_mark_read_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/mark_as_read.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_MarkAsRead).'" />
		<span class="'.$textClass.'">'.JS_LANG_MarkAsRead.'</span>
	</div>
	<div class="wm_toolbar_item" id="popup_control_3">
		<img class="wm_menu_control_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/popup_menu_arrow.gif" />
	</div>
</div>
<div class="wm_hide" id="popup_menu_3">
	<div onclick="DoUnReadMessages();" class="wm_menu_item" onmouseover="this.className=\'wm_menu_item_over\';" onmouseout="this.className=\'wm_menu_item\';">
		<img class="wm_menu_mark_unread_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/mark_as_unread.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_MarkAsUnread).'" />
		<span class="'.$textClass.'">'.JS_LANG_MarkAsUnread.'</span>
	</div>
	<div onclick="DoFlagMessages();" class="wm_menu_item" onmouseover="this.className=\'wm_menu_item_over\';" onmouseout="this.className=\'wm_menu_item\';">
		<img class="wm_menu_flag_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/flag.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_MarkFlag).'" />
		<span class="'.$textClass.'">'.JS_LANG_MarkFlag.'</span>
	</div>
	<div onclick="DoUnFlagMessages();" class="wm_menu_item" onmouseover="this.className=\'wm_menu_item_over\';" onmouseout="this.className=\'wm_menu_item\';">
		<img class="wm_menu_unflag_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/unflag.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_MarkFlag).'" />
		<span class="'.$textClass.'">'.JS_LANG_MarkUnflag.'</span>
	</div>
	'.$temp.'
</div>
		';
	}	
					
}


class ReplyButton
{
	/**
	 * @var PageBuilder
	 */
	var $_pagebuilder;
	
	/**
	 * @param PageBuilder $pagebuilder
	 * @return ReplyButton
	 */
	function ReplyButton(&$pagebuilder)
	{
		$this->_pagebuilder = &$pagebuilder;
		$this->_pagebuilder->AddInitText('PopupMenu.addItem(document.getElementById("popup_menu_4"), document.getElementById("popup_control_4"), "wm_popup_menu", document.getElementById("popup_replace_4"), document.getElementById("popup_title_4"), "wm_tb", "wm_tb_press", "wm_toolbar_item", "wm_toolbar_item_over");');
	}
	
	function ToHTML()
	{
		$textClass = ($this->_pagebuilder->_proc->settings->ShowTextLabels) ? '' : 'wm_hide';
		return '
<div id="popup_replace_4" class="wm_tb">
	<div class="wm_toolbar_item" id="popup_title_4" onclick="DoReplyButton();">
		<img class="wm_menu_reply_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/reply.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_Reply).'" />&nbsp;
		<span class="'.$textClass.'">'.JS_LANG_Reply.'</span>
	</div>
	<div class="wm_toolbar_item" id="popup_control_4">
		<img class="wm_menu_control_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/popup_menu_arrow.gif" />
	</div>
</div>
<div class="wm_hide" id="popup_menu_4">
	<div onclick="DoReplyAllButton();" class="wm_menu_item" onmouseover="this.className=\'wm_menu_item_over\';" onmouseout="this.className=\'wm_menu_item\';">
		<img class="wm_menu_replyall_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/replyall.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_ReplyAll).'" />
		<span class="'.$textClass.'">'.JS_LANG_ReplyAll.'</span>
	</div>
</div>
		';
	}	
} 



class IMAP_Delete
{
	/**
	 * @var PageBuilder
	 */
	var $_pagebuilder;
	
	/**
	 * @var bool
	 */
	var $isSearch = false;
	
	/**
	 * @param PageBuilder $pagebuilder
	 * @return ReplyButton
	 */
	function IMAP_Delete(&$pagebuilder)
	{
		$this->_pagebuilder = &$pagebuilder;
		$this->isSearch = (isset($pagebuilder->_proc->sArray[SEARCH_ARRAY]) && strlen($pagebuilder->_proc->sArray[SEARCH_ARRAY][S_TEXT]) > 0);
		$this->_pagebuilder->AddInitText('PopupMenu.addItem(document.getElementById("popup_menu_13"), document.getElementById("popup_control_13"), "wm_popup_menu", document.getElementById("popup_replace_13"), document.getElementById("popup_title_13"), "wm_tb", "wm_tb_press", "wm_toolbar_item", "wm_toolbar_item_over");');
	}
	
	function ToHTML()
	{
		$textClass = ($this->_pagebuilder->_proc->settings->ShowTextLabels) ? '' : 'wm_hide';
		$fid = ($this->_pagebuilder->_proc->currentFolder) ? $this->_pagebuilder->_proc->currentFolder->IdDb : '-1';
		$temp = ($this->isSearch) ? '' : 
		'<div onclick="confirm(\''.
			ConvertUtils::AttributeQuote(
				ConvertUtils::ClearJavaScriptString(JS_LANG_ConfirmAreYouSure, '\'')).'\') ? document.location=\''.ACTIONFILE.'?action=delete&req=purge&f='.$fid.'\' : \'\';" class="wm_menu_item" onmouseover="this.className=\'wm_menu_item_over\';" onmouseout="this.className=\'wm_menu_item\';">
		<img class="wm_menu_purge_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/purge.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_PurgeDeleted).'" />
		<span class="'.$textClass.'">'.JS_LANG_PurgeDeleted.'</span>
	</div>';
		
		return '
<div id="popup_replace_13" class="wm_tb">
	<div class="wm_toolbar_item" id="popup_title_13" onclick="DoDeleteButton();" >
		<img class="wm_menu_delete_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/delete.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_Delete).'" />&nbsp;
		<span class="'.$textClass.'">'.JS_LANG_Delete.'</span>
	</div>
	<div class="wm_toolbar_item" id="popup_control_13">
		<img class="wm_menu_control_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/popup_menu_arrow.gif" />
	</div>
</div>
<div class="wm_hide" id="popup_menu_13">
	<div onclick="DoUnDeleteButton();" class="wm_menu_item" onmouseover="this.className=\'wm_menu_item_over\';" onmouseout="this.className=\'wm_menu_item\';">
		<img class="wm_menu_delete_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/delete.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_Undelete).'" />
		<span class="'.$textClass.'">'.JS_LANG_Undelete.'</span>
	</div>
	'.$temp.'
</div>
		';
	}	
} 

class PriorityButton
{
	/**
	 * @var PageBuilder
	 */
	var $_pagebuilder;
	
	/**
	 * @param PageBuilder $pagebuilder
	 * @return PriorityButton
	 */
	function PriorityButton(&$pagebuilder)
	{
		$this->_pagebuilder = &$pagebuilder;
		
		$this->_pagebuilder->AddInitText('
PriorityImg = document.getElementById("priority_img");
PriorityText = document.getElementById("priority_text");
PriorityInput = document.getElementById("priority_input");
		');
		
		$this->_pagebuilder->AddJSText('
var PriorityImg, PriorityText, PriorityInput;

function SetPriority(value)
{
	switch (value) {
		case 5:
			PriorityInput.value = 5;
			PriorityImg.src = "skins/'.$this->_pagebuilder->SkinName().'/menu/priority_low.gif";
			PriorityText.innerHTML = "'.JS_LANG_Low.'";
		break;
		default:
		case 3:
			PriorityInput.value = 3;
			PriorityImg.src = "skins/'.$this->_pagebuilder->SkinName().'/menu/priority_normal.gif";
			PriorityText.innerHTML = "'.JS_LANG_Normal.'";
		break;
		case 1:
			PriorityInput.value = 1;
			PriorityImg.src = "skins/'.$this->_pagebuilder->SkinName().'/menu/priority_high.gif";
			PriorityText.innerHTML = "'.JS_LANG_High.'";
		break;
	}
}

function ChangePriority()
{
	switch (PriorityInput.value) {
		case "5":
			SetPriority(3);
		break;
		case "3":
			SetPriority(1);
		break;
		case "1":
			SetPriority(5);
		break;
	}
}
		');
	}
	
	function ToHTML()
	{
		$textClass = ($this->_pagebuilder->_proc->settings->ShowTextLabels) ? '' : 'wm_hide';
		return '
<div onclick="ChangePriority();" class="wm_toolbar_item" onmouseover="this.className=\'wm_toolbar_item_over\';" onmouseout="this.className=\'wm_toolbar_item\';">
	<img id="priority_img" class="wm_menu_priority_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/priority_normal.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_Importance).'" />
	<span id="priority_text" class="'.$textClass.'">'.JS_LANG_Normal.'</span>
</div>
		';
	}	
} 

class SearchFormContactsClass
{
	/**
	 * @var PageBuilder
	 */
	var $_pagebuilder;
	
	/**
	 * @var string
	 */
	var $_hidePart = '';
	
	function SearchFormContactsClass(&$pagebuilder)
	{
		$this->_pagebuilder = &$pagebuilder;
		$this->_hidePart = $this->ToHideDiv();
		$this->_pagebuilder->AddJSText('var SearchForm;');
		$this->_pagebuilder->AddInitText(
		'
SearchForm = new CSearchForm(document.getElementById("search_form"), document.getElementById("search_small_form"), document.getElementById("search_control"), document.getElementById("search_control_img"), "search_form", document.getElementById("bigLookFor"), document.getElementById("smallLookFor"), "'.$this->_pagebuilder->SkinName().'");
SearchForm.Show();
		');
		$this->_pagebuilder->_top->AddOnClick('SearchForm.checkVisibility(event, Browser.Mozilla);');
	}
	
	/**
	 * @return String
	 */
	function CreateGroupsHtml()
	{
		$out = '';
		$groupNames = &$this->_pagebuilder->_proc->db->SelectUserAddressGroupNames();
		foreach ($groupNames as $id => $name)
		{
			$out .= '<option value="'.$id.'">'.ConvertUtils::WMHtmlSpecialChars($name).'</option>';
		}
		return $out;
	}
	
	function ToHTML()
	{
		$text = (isset($this->_pagebuilder->_proc->sArray[SEARCH_ARRAY][S_TEXT])) ? $this->_pagebuilder->_proc->sArray[SEARCH_ARRAY][S_TEXT] : '';
		return '
<div class="wm_toolbar_search_item" style="margin-left: 0px;" id="search_control">
	<img class="wm_search_arrow" id="search_control_img" src="skins/'
		.$this->_pagebuilder->SkinName().'/menu/arrow_down.gif" />
</div>
<div class="wm_toolbar_search_item" onmouseover="this.className=\'wm_toolbar_search_item_over\'" 
onmouseout="this.className=\'wm_toolbar_search_item\'" id="search_small_form" style="margin-right: 0px;">
	<form action="?'.S_GETMODECONTACT.'=mini" method="POST" id="searchformmini">
	<input type="text" id="smallLookFor" name="smallLookFor" class="wm_search_input" value="'.ConvertUtils::AttributeQuote($text).'" >
	<img class="wm_menu_small_search_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/search_button.gif" 
	onclick="document.getElementById(\'searchformmini\').submit();" >
	</form>
</div>	
'.$this->_hidePart;
	}
	
	function ToHideDiv()
	{
		return '
<div class="wm_hide" id="search_form">
<form action="?'.S_GETMODECONTACT.'=big" method="POST" id="searchformbig">
	<table>
		<tr>
			<td class="wm_search_title">
				'.JS_LANG_LookFor.'
			</td>
			<td class="wm_search_value">
				<input type="text" id="bigLookFor" name="bigLookFor" class="wm_search_input" value="" />
				<img class="wm_menu_big_search_img" src="skins/'.$this->_pagebuilder->SkinName().'/menu/search_button_big.gif" 
				onclick="document.getElementById(\'searchformbig\').submit();"
				/>
			</td>
		</tr>
		<tr>
			<td class="wm_search_title">
				'.JS_LANG_SearchIn.'
			</td>
			<td class="wm_search_value">
				<select name="'.CONTACT_ID.'">
					<option value="-2" selected="selected">'.JS_LANG_AllGroups.'</option>
					'.
					$this->CreateGroupsHtml()
					.'
				</select>
			</td>
		</tr>
	</table>
</form>
</div>';
	}
	
} 
