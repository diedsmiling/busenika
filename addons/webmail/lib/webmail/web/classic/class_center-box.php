<?php

class CenterPanel
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
	 * @var FolderList
	 */
	var $_folder_part;
	
	/**
	 * @var MessageListTable
	 */
	var $_messages_part;
	
	
	/**
	 * @param PageBuilder $pageBuilder
	 * @return CenterPanel
	 */
	function CenterPanel(&$pagebuilder)
	{
		$this->_pagebuilder = &$pagebuilder;
		$this->_proc = &$pagebuilder->_proc;
			
		$this->_pagebuilder->AddJSFile('./classic/base.inbox.js');
		$this->_pagebuilder->AddJSFile('./classic/base.cpageswitcher.js');
		
		$isPreview = ($this->_proc->account->ViewMode == VIEW_MODE_PREVIEW_PANE || $this->_proc->account->ViewMode == VIEW_MODE_PREVIEW_PANE_NO_IMG) ? 'true' : 'false';
		$AskForDel = array('', '');
		if (($this->_proc->currentFolder && $this->_proc->currentFolder->Type == FOLDERTYPE_Trash))
		{
			$AskForDel = array('if (confirm(\''.ConvertUtils::ClearJavaScriptString(JS_LANG_ConfirmAreYouSure, '\'').'\')) {', '}');
		}
		
		$flagjs = '
	function DoFlagOneMessage(lineobj)
	{
		if (!hiddeniframe) {
			hiddeniframe = CreateChildWithAttrs(document.body, "iframe", [["name", "hiddenframe"], ["class", "wm_hide"]]);
		}
		
		if (hiddenform) {
			hiddenform.action = (lineobj.Flagged) ?
				"'.ACTIONFILE.'?action=groupoperation&req=flag" :
				"'.ACTIONFILE.'?action=groupoperation&req=unflag";
			hiddenform.target = "hiddenframe";
		} else {
			hiddenform = (lineobj.Flagged) ?
				CreateChildWithAttrs(document.body, "form", [["action", "'.ACTIONFILE.'?action=groupoperation&req=flag"], ["target", "hiddenframe"], ["method", "POST"]]) :
				CreateChildWithAttrs(document.body, "form", [["action", "'.ACTIONFILE.'?action=groupoperation&req=unflag"], ["target", "hiddenframe"], ["method", "POST"]]);
		}

		CleanNode(hiddenform);
		CreateChildWithAttrs(hiddenform, "input", [
					["type", "hidden"],
					["name", "d_messages[" + lineobj.MsgFolderId + "][0]"],
					["value", lineobj.MsgFolderFullName]
					]);
		CreateChildWithAttrs(hiddenform, "input", [
					["type", "hidden"],
					["name", "d_messages[" + lineobj.MsgFolderId + "][1][" + lineobj.MsgId + "]"],
					["value", lineobj.MsgUid]
					]);

		hiddenform.submit();
	}
	
	function DoFlagMessages()
	{
		var idsArray = InboxLines.GetCheckedLines();
		InboxLines.SetParams(idsArray.IdArray, "Flagged", true, false);
		
		if (!hiddeniframe) {
			hiddeniframe = CreateChildWithAttrs(document.body, "iframe", [["name", "hiddenframe"], ["class", "wm_hide"]]);
		}
		
		if (hiddenform) {
			hiddenform.action = "'.ACTIONFILE.'?action=groupoperation&req=flag";
			hiddenform.target = "hiddenframe";
		} else {
			hiddenform = CreateChildWithAttrs(document.body, "form", [["action", "'.ACTIONFILE.'?action=groupoperation&req=flag"], ["target", "hiddenframe"], ["method", "POST"]]);
		}
		CleanNode(hiddenform);
		GroupOperation(hiddenform);	
	}
	
	function DoUnFlagMessages()
	{
		var idsArray = InboxLines.GetCheckedLines();
		InboxLines.SetParams(idsArray.IdArray, "Flagged", false, false);
		if (!hiddeniframe) {
			hiddeniframe = CreateChildWithAttrs(document.body, "iframe", [["name", "hiddenframe"], ["class", "wm_hide"]]);
		}
		
		if (hiddenform)	{
			hiddenform.action = "'.ACTIONFILE.'?action=groupoperation&req=unflag";
			hiddenform.target = "hiddenframe";
		} else {
			hiddenform = CreateChildWithAttrs(document.body, "form", [["action", "'.ACTIONFILE.'?action=groupoperation&req=unflag"], ["target", "hiddenframe"], ["method", "POST"]]);
		}
		CleanNode(hiddenform);
		GroupOperation(hiddenform);	
	}
';
		if ($this->_proc->account->MailProtocol != MAILPROTOCOL_IMAP4 &&
				$this->_proc->currentFolder && $this->_proc->currentFolder->SyncType == FOLDERSYNC_DirectMode)
		{
			$flagjs = '
function DoFlagOneMessage(lineobj) {}
function DoFlagMessages() {}
function DoUnFlagMessages() {}
';
		}
		
		$this->_pagebuilder->AddJSText('	
	var MovableVerticalDiv, MovableHorizontalDiv;
	var MainContainer, FoldersPart, MessList, Iflame, InboxLines;
	var vResizer, hResizer;
	var vResizerCont;
	var isDisplayFolders = true;
	var sep = "-----";
	var CheckMailUrl = "check-mail.php";
	var EmptyHtmlUrl = "empty.html";
	var isPreviewPane = '.$isPreview.';
	var hiddenform, hiddeniframe; 
	
	function GroupOperation(form)
	{
		var messagesArray = InboxLines.GetCheckedLinesObj();
		for (var i = 0; i < messagesArray.length; i++) {
			CreateChildWithAttrs(form, "input", [
					["type", "hidden"],
					["name", "d_messages[" + messagesArray[i].MsgFolderId + "][0]"],
					["value", messagesArray[i].MsgFolderFullName]
					]);
			CreateChildWithAttrs(form, "input", [
					["type", "hidden"],
					["name", "d_messages[" + messagesArray[i].MsgFolderId + "][1][" + messagesArray[i].MsgId + "]"],
					["value", messagesArray[i].MsgUid]
					]);
		}
		
		if (messagesArray.length > 0) {
			form.submit();
		} else {
			alert(Lang.WarningMarkListItem);
		}
	}
	
	function DoNewMessageButton()
	{
		document.location = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";
	}
	
	function MoveToFolder(idfolder)
	{
		if (hiddenform) {
			hiddenform.action = "'.ACTIONFILE.'?action=groupoperation&req=move_messages";
			hiddenform.target = "_self";
		} else {
			hiddenform = CreateChildWithAttrs(document.body, "form", [["action", "'.ACTIONFILE.'?action=groupoperation&req=move_messages"], ["method", "POST"]]);
		}
		
		CleanNode(hiddenform);
		CreateChildWithAttrs(hiddenform, "input", [["type", "hidden"], ["name", "tofolder"], ["value", idfolder]]);
		GroupOperation(hiddenform);
	}
	
	function DoDeleteButton()
	{
		'.$AskForDel[0].'
			if (hiddenform)	{
				hiddenform.action = "'.ACTIONFILE.'?action=groupoperation&req=delete_messages";
				hiddenform.target = "_self";
			} else {
				hiddenform = CreateChildWithAttrs(document.body, "form", [["action", "'.ACTIONFILE.'?action=groupoperation&req=delete_messages"], ["method", "POST"]]);
			}
			CleanNode(hiddenform);
			GroupOperation(hiddenform);
		'.$AskForDel[1].'
	}
	
	'.$flagjs.'

	function DoReadMessages()
	{
		var idsArray = InboxLines.GetCheckedLines();
		InboxLines.SetParams(idsArray.IdArray, "Read", true, false);
		if (!hiddeniframe) {
			hiddeniframe = CreateChildWithAttrs(document.body, "iframe", [["name", "hiddenframe"], ["class", "wm_hide"]]);
		}
		
		if (hiddenform) {
			hiddenform.action = "'.ACTIONFILE.'?action=groupoperation&req=read";
			hiddenform.target = "hiddenframe";
		} else {
			hiddenform = CreateChildWithAttrs(document.body, "form", [["action", "'.ACTIONFILE.'?action=groupoperation&req=read"], ["target", "hiddenframe"], ["method", "POST"]]);
		}
		CleanNode(hiddenform);
		GroupOperation(hiddenform);	
	}
	
	function DoUnReadMessages()
	{
		var idsArray = InboxLines.GetCheckedLines();
		InboxLines.SetParams(idsArray.IdArray, "Read", false, false);
		if (!hiddeniframe) {
			hiddeniframe = CreateChildWithAttrs(document.body, "iframe", [["name", "hiddenframe"], ["class", "wm_hide"]]);
		}
		
		if (hiddenform) {
			hiddenform.action = "'.ACTIONFILE.'?action=groupoperation&req=unread";
			hiddenform.target = "hiddenframe";
		} else {
			hiddenform = CreateChildWithAttrs(document.body, "form", [["action", "'.ACTIONFILE.'?action=groupoperation&req=unread"], ["target", "hiddenframe"], ["method", "POST"]]);
		}
		CleanNode(hiddenform);
		GroupOperation(hiddenform);	
	}
	
	function GetSelectMessages()
	{
		var obj = InboxLines.GetCheckedLinesObj();
		var messagesArray = Array();
		
		for (var i = 0; i < obj.IdArray.length; i++) {
			var temp = ParseLineId(obj.IdArray);
			if (temp) {
				messagesArray.push(temp);
			}
		}
		return messagesArray;
	}
	
	function ResizeElements(mode)
	{
		ResizeScreen(mode);
		if (!Browser.IE) {
			ResizeScreen(mode);
		}
	}

	function AddContact(contactString)
	{
		var obj = GetEmailParts(HtmlDecode(contactString));
		var hiddenform = CreateChildWithAttrs(document.body, "form", [["action", "'.BASEFILE.'?'.SCREEN.'='.SCREEN_CONTACTS.'&'.CONTACT_MODE.'='.C_NEW.'"], ["target", "_self"], ["method", "POST"]]);
		CreateChildWithAttrs(hiddenform, "input", [["type", "hidden"], ["name", "cdata"], ["value",  "1"]]);
		CreateChildWithAttrs(hiddenform, "input", [["type", "hidden"], ["name", "cemail"], ["value",  obj.Email]]);
		CreateChildWithAttrs(hiddenform, "input", [["type", "hidden"], ["name", "cfullname"], ["value",  obj.Name]]);
		hiddenform.submit();
	}
	
	function ResizeScreen(mode)
	{
		var isAuto = false;
		var height = GetHeight();
		var innerHeight = height - MainContainer.getExternalHeight();
		
		if (innerHeight < 300) {
			innerHeight = 300;
			isAuto = true;
		}
		
		if (isPreviewPane) {
			MainContainer.inner_height = innerHeight;
		}
			
		if (mode == "all") {
			MainContainer.hideContent();
		}
		
		if (mode != "width") {
			var listHeight = MovableHorizontalDiv._topPosition - Iframe.min_upper;
			MessList.height = listHeight;
			
			if (isPreviewPane) {
				var iframe_height = innerHeight - listHeight;
				if (iframe_height < Iframe.min_upper) {
					iframe_height = Iframe.min_upper;
				}

				MessList.resizeElementsHeight(listHeight);
				Iframe.resizeElementsHeight(iframe_height);
				FoldersPart.resizeElementsHeight(innerHeight);

			} else {
				//MessList.resizeElementsHeight(innerHeight);
			}
			MovableVerticalDiv.updateVerticalSize(MainContainer.inner_height);
		}
		
		var resizerWidth = vResizerCont.offsetWidth + 2;
		if (resizerWidth == 2) {
			resizerWidth = 6;
		}
		var width = GetWidth();
		if (mode != "height") {
			var fpWidth = FoldersPart.width;
			if (isDisplayFolders == true) {
				fpWidth = MovableVerticalDiv._leftPosition;
			}
			var ipWidth = width - fpWidth - resizerWidth;
			if (ipWidth < 550) {
				ipWidth = 550;
				fpWidth = width - ipWidth - resizerWidth;
				if (isDisplayFolders == true) {
					if (fpWidth < 80) {
						fpWidth = 80;
						isAuto = true;
					}
				} else {
					if (fpWidth < 18) {
						fpWidth = 18;
						isAuto = true;
					}
				}
			} else {
				fpWidth = width - ipWidth - resizerWidth;
			}
			FoldersPart.width = fpWidth;

			FoldersPart.resizeElementsWidth(fpWidth);
			MessList.resizeElementsWidth(ipWidth);
			Iframe.resizeElementsWidth(ipWidth);

			if (isPreviewPane) {
				MovableHorizontalDiv.updateHorizontalSize(ipWidth);
			}
		}
		
		if (mode == "all") {
			MainContainer.showContent();
		}
				
		PageSwitcher.Replace(MessList.parent_table);
		
		if (isPreviewPane) {
			Iframe.show();
		}
		
		SetBodyAutoOverflow(isAuto);
	} 

	function SetStateTextHandler(text) {
		if (CheckMail) CheckMail.SetText(text);
	}
	
	function SetCheckingFolderHandler(folder, count) {
		if (CheckMail) CheckMail.SetFolder(folder, count);
	}
	
	function SetRetrievingMessageHandler(number) {
		if (CheckMail) CheckMail.SetMsgNumber(number);
	}
	
	function SetDeletingMessageHandler(number) {
		if (CheckMail) CheckMail.DeleteMsg(number);
	}
	
	function EndCheckMailHandler(error)
	{
		if (CheckMail) CheckMail.End();
		if (error.length > 0) {
			if (error == "session_error") {
				document.location = "'.LOGINFILE.'?error=1";
			}
		}
	}

	function CheckEndCheckMailHandler() 
	{
		if (CheckMail && CheckMail.started) {
			CheckMail.End();
			InfoPanel._isError = true;
			InfoPanel.SetInfo(Lang.ErrorCheckMail);
			InfoPanel.Show();
		} else {
			document.location = "'.BASEFILE.'";
		}
	}
	');
		
		if ($this->_proc->account->MailProtocol == MAILPROTOCOL_IMAP4)
		{
			$this->_pagebuilder->AddJSText('
	
	function DoUnDeleteButton()
	{
		var idsArray = InboxLines.GetCheckedLines();
		InboxLines.SetParams(idsArray.IdArray, "Deleted", false, false);
		if (!hiddeniframe) {
			hiddeniframe = CreateChildWithAttrs(document.body, "iframe", [["name", "hiddenframe"], ["class", "wm_hide"]]);
		}
		
		if (hiddenform) {
			hiddenform.action = "'.ACTIONFILE.'?action=groupoperation&req=undelete_messages";
			hiddenform.target = "hiddenframe";
		} else {
			hiddenform = CreateChildWithAttrs(document.body, "form", [["action", "'.ACTIONFILE.'?action=groupoperation&req=undelete_messages"], ["target", "hiddenframe"], ["method", "POST"]]);
		}
		CleanNode(hiddenform);
		GroupOperation(hiddenform);
	}
	
	function DoReloadFolderButton()
	{
		document.location = "'.BASEFILE.'?reload=1";
	}
			');
		}
		
		$this->_pagebuilder->_top->AddOnResize('ResizeElements(\'all\');');
		$this->_pagebuilder->AddInitText('CheckMail = new CCheckMail();');
		
		$resizer_left = $this->_proc->account->VertResizer;
		$resizer_top = $this->_proc->account->HorizResizer;
		
		$this->_pagebuilder->AddInitText('
PageSwitcher = new CPageSwitcher("'.$this->_pagebuilder->SkinName().'");
PageSwitcher.Build();

MainContainer = new CMainContainer();
FoldersPart = new CFoldersPart(isPreviewPane, "'.$this->_pagebuilder->SkinName().'");

MessList = new CMessageList(isPreviewPane);
Iframe = new CMessageIframe(isPreviewPane);

InboxLines = new CSelection();

vResizer = document.getElementById("vert_resizer");
hResizer = document.getElementById("hor_resizer");
vResizerCont = document.getElementById("vresizer_part");

MovableVerticalDiv = new CVerticalResizer(
	vResizer, MainContainer.table, 2, 81, 551, '.$resizer_left.', \'ResizeElements("width")\', 0);

MovableHorizontalDiv = new CHorizontalResizer(
	hResizer, MainContainer.table, 2, Iframe.min_upper + 100, Iframe.min_lower, '.$resizer_top.', \'ResizeElements("height")\'); 
');
		
		$this->_folder_part = &new FolderList($pagebuilder);
		$this->_messages_part = &new MessageListTable($pagebuilder);
		
	}
	
	/**
	 * @return string
	 */
	function ToHTML()
	{
		$msgCount = (isset($this->_messages_part->messCount)) ?	(int) $this->_messages_part->messCount : '0';
		if (!$msgCount) $msgCount = '0';
		
		if (isset($this->_proc->sArray[SEARCH_ARRAY]) && strlen($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]))
		{
			$lowtoolbarText = '<span class="wm_lowtoolbar_messages">'. $msgCount.'&nbsp;'.JS_LANG_Messages.'</span>';
		}
		else 
		{
			$lowtoolbarText = '<span class="wm_lowtoolbar_messages">'. $msgCount.'&nbsp;'.JS_LANG_MessagesInFolder.'</span>';
		}
		
		$boxPercentage = (int) ceil(($this->_proc->account->MailboxSize/$this->_proc->account->MailboxLimit)*100);
		$boxPercentage = ($boxPercentage) ? $boxPercentage : 0;
		$boxPercentage = ($boxPercentage > 100) ? 100 : $boxPercentage;
		
		$spaceTitle = JS_LANG_YouUsing.' '.$boxPercentage.'% '.JS_LANG_OfYour.' '.GetFriendlySize($this->_proc->account->MailboxLimit);
		
		$progressbarClass = ($this->_proc->settings->EnableMailboxSizeLimit) ? 'wm_lowtoolbar_space_info' : 'wm_hide';
		
			return '
<div class="wm_hide" align="center" id="hider">
		
<div class="wm_background">
	<table class="wm_mail_container" id="main_container">
		<tr>
			<td rowspan="3">
				<div class="wm_folders_part" id="folders_part">
					<div class="wm_folders_hide_show" id="folders_hide">
						<a href="#" onclick="ChangeFoldersMode(); return false;">
							<img id="folders_hide_img" src="./skins/'.$this->_pagebuilder->SkinName().'/folders/hide_folders.gif" title="'.ConvertUtils::AttributeQuote(JS_LANG_HideFolders).'" />
						</a>
					</div>
					<div id="folders" class="wm_folders">
'
.
$this->_folder_part->ToHTML()
.
'
					</div>
					<div class="wm_manage_folders" id="manage_folders" align="center">
						<a href="'.BASEFILE.'?'.SCREEN.'='.SET_ACCOUNT_MFOLDERS.'">'.JS_LANG_ManageFolders.'</a>
					</div>
				</div>
			</td>
			
			<td class="wm_vresizer_part" id="vresizer_part" rowspan="3"><div class="wm_vresizer_width"></div><div class="wm_vresizer" id="vert_resizer"></div><div class="wm_vresizer_width"></div></td>
			<td id="inbox_part">
				<div class="wm_inbox" id="inbox_div">
				'
.
$this->_messages_part->MessageTableHeaders()
.
'
					<div class="wm_inbox_lines" id="list_container">
'
.
$this->_messages_part->ToHTML()
.
'
					</div>
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="wm_hresizer_part" id="hresizer_part"><div class="wm_hresizer_height"></div><div class="wm_hresizer" id="hor_resizer"></div><div class="wm_hresizer_height"></div></td>
		</tr>
		<tr id="iframe_tr">
			<td id="iframe_td">
				<div name="message_container_iframe" id="message_container_iframe" class="wm_message_container">
					<iframe name="iframe_container" frameborder="0" id="iframe_container" src="base-iframe.php?mode=preview"></iframe>
				</div>
			</td>
		</tr>
		<tr>
		
			<td class="wm_lowtoolbar" colspan="3" id="lowtoolbar">
			'.$lowtoolbarText.'
				<span class="'.$progressbarClass.'" title="'.ConvertUtils::AttributeQuote($spaceTitle).'">
					<div class="wm_progressbar">
						<div class="wm_progressbar_used" style="width: '.$boxPercentage.'px;"></div>
					</div>
				</span>
			</td>
		</tr>
	</table>
</div>
</div>
		';
	}
}

class FolderList
{
	
	/**
	 * @var PageBuilder
	 */
	var $_pagebuilder;
	
	/**
	 * @var string
	 */
	var $text = '';
	
	/**
	 * @param PageBuilder $pageBuilder
	 * @return FolderList
	 */	
	function FolderList(&$pageBuilder)
	{
		$this->_pagebuilder = &$pageBuilder;
		$this->_pagebuilder->AddJSText('
function ChangeFoldersMode()
{
	if (isDisplayFolders == true){
		isDisplayFolders = false;
		FoldersPart.hide();
		MovableVerticalDiv.free();
	} else {
		isDisplayFolders = true;
		FoldersPart.show();
		MovableVerticalDiv.busy(FoldersPart.width);
	}
	ResizeElements("width");
} 
');
		
		if ($this->_pagebuilder->_proc->account->HideFolders)
		{
			$this->_pagebuilder->AddInitText('isDisplayFolders = false; FoldersPart.hide(); MovableVerticalDiv.free();');
		}
		
		if (Get::val('reload', '-1') == '1' && $this->_pagebuilder->_proc->account->MailProtocol == MAILPROTOCOL_IMAP4)
		{
			$syncRes = $this->_pagebuilder->_proc->processor->SynchronizeFolders();
			if ($syncRes)
			{
				$this->_pagebuilder->_proc->SetFolders();
			}
		}
		$this->text = $this->CreateHtmlFolderTree($this->_pagebuilder->_proc->GetFolders());
	}

	
	/**
	 * @param FolderCollection $folderCollection
	 * @return String
	 */
	function CreateHtmlFolderTree($folderCollection)
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
			
			$isselect = '';
			
			if ($this->_pagebuilder->_proc->currentFolder && $this->_pagebuilder->_proc->currentFolder->IdDb == $folder->IdDb)
			{
				$isselect = 'class="wm_select_folder"';
			}

			if (!$folder->Hide)
			{
				$out .= '<div style="padding-left: '.($folder->Level * 8).'px;" '.$isselect.'>';
				$out .= '<a href="?'.FOLDER_ID.'='.$folder->IdDb.'">';
				$out .= '<img src="./skins/'.$this->_pagebuilder->SkinName().'/folders/'.$this->GetFolderImg($folder).'" />';
								
				switch ($folder->Type)
				{
					default:
					case FOLDERTYPE_Custom:		$out .= ConvertUtils::WMHtmlSpecialChars($foldername); break;					
					case FOLDERTYPE_Inbox:		$out .= FolderInbox; break;
					case FOLDERTYPE_Drafts:		$out .= FolderDrafts; break;
					case FOLDERTYPE_SentItems:	$out .= FolderSentItems; break;
					case FOLDERTYPE_Trash:		$out .= FolderTrash; break;
				}
								
				if ($folder->SyncType == FOLDERSYNC_DirectMode)				
				{
					$out .= '&nbsp;<span id="cnt_'.$folder->IdDb.'" class="wm_hide"></span></a>';
					$out .= '&nbsp;<span title="'.ConvertUtils::AttributeQuote(DirectAccessTitle).'" class="wm_folder_direct_mode">&nbsp;'.DirectAccess.'&nbsp;</span>';
				}
				else 
				{
					$out .= ($folder->UnreadMessageCount > 0) ? 
						'&nbsp;<span id="cnt_'.$folder->IdDb.'">('.$folder->UnreadMessageCount.')</span></a>' :
						'&nbsp;<span id="cnt_'.$folder->IdDb.'"></span></a>';
				}
				
				$out .= '</div>';
				
				if ($folder->SubFolders != null && $folder->SubFolders->Count() > 0)
				{
					$out .= $this->CreateHtmlFolderTree($folder->SubFolders);
				}
			}
			

			
		}
		return $out;
	}
	
	/**
	 * @param Folder $folder
	 * @return String
	 */
	function GetFolderImg($folder)
	{
		$sync_str = ($this->_pagebuilder->_proc->account->MailProtocol == MAILPROTOCOL_IMAP4 &&
			$folder->SyncType != FOLDERSYNC_DirectMode && $folder->SyncType != FOLDERSYNC_DontSync) ? '_sync' : '';

		switch ($folder->Type)
		{
			case FOLDERTYPE_Inbox:		return 'folder_inbox'.$sync_str.'.gif';		break;
			case FOLDERTYPE_SentItems:	return 'folder_send'.$sync_str.'.gif';		break;
			case FOLDERTYPE_Drafts:		return 'folder_drafts'.$sync_str.'.gif';	break;
			case FOLDERTYPE_Trash:		return 'folder_trash'.$sync_str.'.gif';		break;
			default:					return 'folder'.$sync_str.'.gif';		break;
		}
	}
	
	/**
	 * @return String
	 */
	function ToHTML()
	{
		return $this->text;
	}
}

class MessageListTable
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
	 * @var Folder
	 */
	var $folder;
	
	/**
	 * @var FolderCollection
	 */
	var $folders;
	
	/**
	 * @var int
	 */
	var $sortField;
	
	/**
	 * @var int
	 */
	var $sortOrder;
	
	/**
	 * @var int
	 */
	var $messCount = 0;
	
	/**
	 * @var int
	 */
	var $page = 1;
	
	/**
	 * @var WebMailMessageCollection
	 */
	var $messageCollection;
	
	/**
	 * @param PageBuilder $pageBuilder
	 * @return MessageListTable
	 */	
	function MessageListTable(&$pagebuilder)
	{
		$this->_pagebuilder = &$pagebuilder;
		$this->_proc = &$pagebuilder->_proc;

		$this->sortField = Get::val('s_fld', 0);
		$this->sortOrder = Get::val('s_ord', 0);
		
		$this->page = $this->_proc->sArray[PAGE];
		
		$this->_proc->account->DefaultOrder = $this->sortField + $this->sortOrder;
		
		$this->folders = &$this->_proc->GetFolders();
		
		if (isset($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]) && strlen($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]) > 0)
		{
			if ($this->_proc->sArray[SEARCH_ARRAY][S_FOLDER] > -2)
			{
				$this->folder = &$this->folders->GetFolderById((int) $this->_proc->sArray[FOLDER_ID]);
				$this->_proc->processor->GetFolderInfo($this->folder);
				$this->folders = &new FolderCollection();
				$this->folders->Add($this->folder);
			}
			else 
			{
				$this->folder = null;
			}
			
			$field = ($this->_proc->sArray[SEARCH_ARRAY][S_MODE] == 'onlyheaders');
			$condition = ConvertUtils::ConvertEncoding($this->_proc->sArray[SEARCH_ARRAY][S_TEXT],
													$this->_proc->account->GetUserCharset(),
													$this->_proc->account->DbCharset);
			$this->messCount = (int) $this->_proc->processor->SearchMessagesCount($condition, $this->folders, $field);
			$this->messageCollection = &$this->_proc->processor->SearchMessages($this->page, $condition,
													$this->folders, $field, $this->messCount);

		}
		else 
		{
			$cfolder = &$this->_proc->GetCurrentFolder();
			if ($cfolder)
			{
				$this->folder = &$cfolder;
				$this->messCount = (int) $this->folder->MessageCount;
				
				if ($this->_proc->account->MailsPerPage*($this->page - 1) >= $this->messCount)
				{
					$this->page = (int) ceil($this->messCount/$this->_proc->account->MailsPerPage);
				}
				$this->page = ($this->page < 1) ? $this->page = 1 : $this->page;
				$this->messageCollection = &$this->_proc->processor->GetMessageHeaders($this->page, $this->folder);				
			}
			else 
			{
				$this->folder = null;
				$this->messCount = 0;
				$this->page = 1;
				$this->messageCollection = &new WebMailMessageCollection();
			}
		}
		
		if ($this->folder && $this->folders)
		{
			$this->folders->InitToFolder($this->folder);
		}
		
		if ($this->messageCollection === null)
		{
			$this->folder = null;
			$this->messCount = 0;
			$this->page = 1;
			$this->messageCollection = &new WebMailMessageCollection();		
			SetOnlineError(PROC_CANT_GET_MSG_LIST);	
		}
		
		$jsTempString = ($this->_proc->currentFolder && $this->_proc->currentFolder->Type == FOLDERTYPE_Drafts)
				? 'BaseForm.Form.action = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";'
				: 'BaseForm.Form.action = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_FULLSCREEN.'";';
				
		$flagjs = '
		var line = InboxLines.GetLinesById(id);
		if (line.Flagged) {
			InboxLines.SetParams([id], "Flagged", false, false);
		} else {
			InboxLines.SetParams([id], "Flagged", true, false);
		}
		DoFlagOneMessage(line);
';
		if ($this->_proc->account->MailProtocol != MAILPROTOCOL_IMAP4 &&
			$this->_proc->currentFolder && $this->_proc->currentFolder->SyncType == FOLDERSYNC_DirectMode)
		{
			$flagjs = '';
		}
		
		$this->_pagebuilder->AddJSText('
		
function CheckThisLine(e, trobj)
{
	var id = trobj.id;

	e = e ? e : window.event;
	if (e.ctrlKey) {
		InboxLines.CheckCtrlLine(id);
	} else if (e.shiftKey) {
		InboxLines.CheckShiftLine(id);
	} else {
		if (Browser.Mozilla) {var elem = e.target;}
		else {var elem = e.srcElement;}
		
		if (!elem || id == "" || elem.id == "none") {
			return false;
		}
		
		var loverTag = elem.tagName.toLowerCase();
		
		if (loverTag == "a") {
			LoadMessageFull(id);
		} else if (loverTag == "input") {
			InboxLines.CheckCBox(id);
		} else if (loverTag == "img") {
			'.$flagjs.'
		} else if (isPreviewPane) {
			InboxLines.CheckLine(id);
			LoadMessage(id);
		}
	}
}		

function CheckThisLineDb(e, trobj)
{
	var id = trobj.id;

	e = e ? e : window.event;

	if (Browser.Mozilla) {
		var elem = e.target;
	} else {
		var elem = e.srcElement;
	}
	
	if (!elem || id == "" || elem.id == "none" || elem.tagName.toLowerCase() == "input") {
		return false;
	}
	LoadMessageFull(id);
}

function LoadMessageFull(lineid)
{
	var parseObj = ParseLineId(lineid);
	var obj = InboxLines.GetLinesById(lineid);

	'.$jsTempString.'
	BaseForm.Form.target = "_self";
	BaseForm.MessId.value = obj.MsgId;
	BaseForm.MessUid.value = obj.MsgUid;
	BaseForm.FolderId.value = obj.MsgFolderId;
	BaseForm.FolderName.value = obj.MsgFolderFullName;
	BaseForm.Charset.value = parseObj.charset;
	BaseForm.Plain.value = "-1";
	BaseForm.Form.submit();
}
	
function LoadMessage(lineid)
{
	if (tempReq != lineid){
		InfoPanel._isError = false;
		InfoPanel.SetInfo(Lang.Loading);
		InfoPanel.Show();
		
		tempReq = lineid;
		var parseObj = ParseLineId(lineid);
		var obj = InboxLines.GetLinesById(lineid);
		
		BaseForm.MessId.value = obj.MsgId;
		BaseForm.MessUid.value = obj.MsgUid;
		BaseForm.FolderId.value = obj.MsgFolderId;
		BaseForm.FolderName.value = obj.MsgFolderFullName;
		BaseForm.Charset.value = parseObj.charset;
		BaseForm.Plain.value = "-1";
		BaseForm.Form.submit();
	}
}

function DoForwardButton()
{
	var lineobjs = InboxLines.GetCheckedLinesObj();
	if (lineobjs && lineobjs.length == 1) {
		var obj = lineobjs[0];
		var parseObj = ParseLineId(obj.Id);

		BaseForm.Form.action = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";
		BaseForm.Form.target = "_self";
		BaseForm.MessId.value = obj.MsgId;
		BaseForm.MessUid.value = obj.MsgUid;
		BaseForm.FolderId.value = obj.MsgFolderId;
		BaseForm.FolderName.value = obj.MsgFolderFullName;
		BaseForm.Charset.value = parseObj.charset;
		BaseForm.Plain.value = "-1";
		BaseForm.Type.value = "forward";
		BaseForm.Form.submit();
	}
}

function DoReplyButton()
{
	var lineobjs = InboxLines.GetCheckedLinesObj();
	if (lineobjs && lineobjs.length == 1) {
		var obj = lineobjs[0];
		var parseObj = ParseLineId(obj.Id);

		BaseForm.Form.action = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";
		BaseForm.Form.target = "_self";
		BaseForm.MessId.value = obj.MsgId;
		BaseForm.MessUid.value = obj.MsgUid;
		BaseForm.FolderId.value = obj.MsgFolderId;
		BaseForm.FolderName.value = obj.MsgFolderFullName;
		BaseForm.Charset.value = parseObj.charset;
		BaseForm.Plain.value = "-1";
		BaseForm.Type.value = "reply";
		BaseForm.Form.submit();
	}
}

function DoReplyAllButton()
{
	var lineobjs = InboxLines.GetCheckedLinesObj();
	if (lineobjs && lineobjs.length == 1) {
		var obj = lineobjs[0];
		var parseObj = ParseLineId(obj.Id);

		BaseForm.Form.action = "'.BASEFILE.'?'.SCREEN.'='.SCREEN_NEWOREDIT.'";
		BaseForm.Form.target = "_self";
		BaseForm.MessId.value = obj.MsgId;
		BaseForm.MessUid.value = obj.MsgUid;
		BaseForm.FolderId.value = obj.MsgFolderId;
		BaseForm.FolderName.value = obj.MsgFolderFullName;
		BaseForm.Charset.value = parseObj.charset;
		BaseForm.Plain.value = "-1";
		BaseForm.Type.value = "replytoall";
		BaseForm.Form.submit();
	}
}


function ChangeCharset(newCharset)
{
	var idline = BaseForm.MessId.value + sep + BaseForm.MessUid.value + sep + BaseForm.FolderId.value + sep + BaseForm.Charset.value + sep;
	var newidline = BaseForm.MessId.value + sep + BaseForm.MessUid.value + sep + BaseForm.FolderId.value + sep + newCharset + sep;
	BaseForm.Charset.value = newCharset;
	
	for (var i=0; i<InboxLines.length; i++) {
		if (InboxLines.lines[i].Id == idline) {
			InboxLines.lines[i].Id = newidline;
			InboxLines.lines[i]._tr.id = newidline;
		}
	}
} 

function ParseLineId(lineid)
{
	var IdArray = lineid.split(sep);
	if (IdArray.length > 3) {
		var objcharset = (IdArray[3]) ? IdArray[3] : -1;
		return {id: IdArray[0], uid: IdArray[1], folder_id: IdArray[2], charset: objcharset}
	}
	return null;
}

	');
		
		$this->_pagebuilder->AddInitText('	
PageSwitcher.Show('. $this->page .', '. $this->_proc->account->MailsPerPage .', '. $this->messCount .', "document.location.replace(\'?s_ord='.$this->sortOrder.'&s_fld='.$this->sortField.'&page=", "\');");
tempReq = "";

function CBaseForm()
{
	this.Form = document.getElementById("messform");
	this.MessId = document.getElementById("m_id");
	this.MessUid = document.getElementById("m_uid");
	this.FolderId = document.getElementById("f_id");
	this.FolderName = document.getElementById("f_name");
	this.Charset = document.getElementById("charset");
	this.Plain = document.getElementById("plain");
	this.Type = document.getElementById("mtype");
}
BaseForm = new CBaseForm();
');
	}
		
	/**
	 * @return string
	 */
	function MessageTableHeaders()
	{
		$hasSort = true;
		$pref = ($this->sortOrder) ? 'up' : 'down';
		$orderImg = '<img src="./skins/'.$this->_pagebuilder->SkinName().'/menu/order_arrow_'.$pref.'.gif">';
		
		$ord = ((bool)$this->sortOrder) ? 0 : 1;
		
		if ($this->_proc->currentFolder && $this->_proc->currentFolder->SyncType == FOLDERSYNC_DirectMode)
		{
			$hasSort = false;
			$orderImg = '';
		}
		
		if (!$this->messageCollection || $this->messageCollection->Count() < 1)
		{
			$hasSort = false;
			$orderImg = '';
		}
		
		$controlHand = ($hasSort) ? 'wm_control' : '';
		$out = '	<div class="wm_inbox_headers" id="inbox_headers">
						<div style="left: 0px; width: 21px; text-align:center;"><input type="checkbox" id="allcheck" onclick="InboxLines.CheckAllBox(this)" /></div>
						<div style="left: 22px; width: 1px;" class="wm_inbox_headers_separate_noresize"></div>';
		
		$onclickString = ($hasSort) ? 
			($this->sortField == DEFAULTORDER_AttachDesc) ? 
				'onclick="document.location.replace(\'?s_fld=10&s_ord='.$ord.'\')";' :
				'onclick="document.location.replace(\'?s_fld=10&s_ord=0\')";' : '';
				
		$out .= '<div style="left: 23px; width: 17px;" class="'.$controlHand.'" '.$onclickString.' >';
				
		$out .= ($this->sortField == DEFAULTORDER_AttachDesc) ? $orderImg : '<img src="./skins/'.$this->_pagebuilder->SkinName().'/menu/attachment.gif" />';
		
		$out .= '</div><div style="left: 41px; width: 1px;" class="wm_inbox_headers_separate_noresize"></div>';
		
		
		$onclickString = ($hasSort) ? 
			($this->sortField == DEFAULTORDER_FlagDesc) ? 
				'onclick="document.location.replace(\'?s_fld=12&s_ord='.$ord.'\')";' :
				'onclick="document.location.replace(\'?s_fld=12&s_ord=0\')";' : '';
		
		$out .= '<div style="left: 43px; width: 17px;" class="'.$controlHand.'" '.$onclickString.'>';
		$out .= ($this->sortField == DEFAULTORDER_FlagDesc) ? $orderImg : '<img src="./skins/'.$this->_pagebuilder->SkinName().'/menu/flag.gif" />';
		$out .= '					
						</div>
						<div style="left: 61px; width: 1px;" class="wm_inbox_headers_separate_noresize"></div>';
						
		if ($this->folder && $this->folder->ToFolder)
		{
			$onclickString = ($hasSort) ? 
				($this->sortField == DEFAULTORDER_ToDesc) ? 
					'onclick="document.location.replace(\'?s_fld=4&s_ord='.$ord.'\')";' :
					'onclick="document.location.replace(\'?s_fld=4&s_ord=0\')";' : '';						
			$out .= '				
							<div style="left: 63px; width: 147px;" class="wm_inbox_headers_from_subject '.$controlHand.'"
							'.$onclickString.'
							>
								'.JS_LANG_To;

			$out .= ($this->sortField == DEFAULTORDER_ToDesc) ? $orderImg : '';		
		}
		else 
		{
			$onclickString = ($hasSort) ? 
				($this->sortField == DEFAULTORDER_FromDesc) ? 
					'onclick="document.location.replace(\'?s_fld=2&s_ord='.$ord.'\')";' :
					'onclick="document.location.replace(\'?s_fld=2&s_ord=0\')";' : '';						
			$out .= '				
							<div style="left: 63px; width: 147px;" class="wm_inbox_headers_from_subject '.$controlHand.'"
							'.$onclickString.'
							>
								'.JS_LANG_From;
	
			$out .= ($this->sortField == DEFAULTORDER_FromDesc) ? $orderImg : '';
		}
		
		$onclickString = ($hasSort) ? 
			($this->sortField == DEFAULTORDER_DateDesc) ? 
				'onclick="document.location.replace(\'?s_fld=0&s_ord='.$ord.'\')";' :
				'onclick="document.location.replace(\'?s_fld=0&s_ord=0\')";' : '';
		
		$out .= '
						</div>
						<div style="left: 211px; width: 1px;" class="wm_inbox_headers_separate_noresize"></div>
						<div style="left: 213px; width: 137px;" class="'.$controlHand.'"
						'.$onclickString.'
						>
							'.JS_LANG_Date;
		$out .= ($this->sortField == DEFAULTORDER_DateDesc) ? $orderImg : '';
		
		
		$onclickString = ($hasSort) ? 
			($this->sortField == DEFAULTORDER_SizeDesc) ? 
				'onclick="document.location.replace(\'?s_fld=6&s_ord='.$ord.'\')";' :
				'onclick="document.location.replace(\'?s_fld=6&s_ord=0\')";' : '';
		
		$out .= '
						</div>
						<div style="left: 351px; width: 1px;" class="wm_inbox_headers_separate_noresize"></div>
						<div style="left: 353px; width: 47px;" class="'.$controlHand.'"
						'.$onclickString.'
						>
							'.JS_LANG_Size;

		$out .= ($this->sortField == DEFAULTORDER_SizeDesc) ? $orderImg : '';
		
		
		$onclickString = ($hasSort) ? 
			($this->sortField == DEFAULTORDER_SubjDesc) ? 
				'onclick="document.location.replace(\'?s_fld=8&s_ord='.$ord.'\')";' :
				'onclick="document.location.replace(\'?s_fld=8&s_ord=0\')";' : '';
		
		$out .= '
						</div>
						<div style="left: 401px; width: 1px;" class="wm_inbox_headers_separate_noresize"></div>
						<div style="left: 404px; width: 147px;" class="wm_inbox_headers_from_subject '.$controlHand.'"
						'.$onclickString.'
						>
							'.JS_LANG_Subject;
		
		$out .= ($this->sortField == DEFAULTORDER_SubjDesc) ? $orderImg : '';
		$out .= '
						</div>
					</div>';
		
		return $out;
		
	}

	/**
	 * @return string
	 */
	function MessageListTr()
	{
		$out = '';
		if (!$this->messageCollection) return '';
		$c = $this->messageCollection->Count();
		
		$stylewidth = array(
			array('', ' style="width: 21px; text-align: center;"'),
			array('', ' style="width: 20px;"'),
			array('', ' style="width: 20px;"'),
			array('', ' style="width: 150px;"'),
			array('', ' style="width: 140px;"'),
			array('', ' style="width: 48px;"'),
			array('', ' style="width: 148px;"')
		);
		
		$atemp_1 = $atemp_2 = '';
		
		if ($this->_proc->account->ViewMode == VIEW_MODE_WITHOUT_PREVIEW_PANE || $this->_proc->account->ViewMode == VIEW_MODE_WITHOUT_PREVIEW_PANE_NO_IMG)
		{
			$atemp_1 = '<a href="#">';
			$atemp_2 = '</a>';
		}
		
		for ($i = 0;  $i < $c; $i++)
		{
			$msg = &$this->messageCollection->Get($i);

			if (!$msg) continue;

			$isRead = (($msg->Flags & MESSAGEFLAGS_Seen) == MESSAGEFLAGS_Seen) ? 'true' : 'false';
			$flaggedImg = '';
			$handclass = ($this->_proc->account->MailProtocol != MAILPROTOCOL_IMAP4 &&
					$this->_proc->currentFolder && $this->_proc->currentFolder->SyncType == FOLDERSYNC_DirectMode)
					? '' : 'wm_control';
					
			if (($msg->Flags & MESSAGEFLAGS_Flagged) == MESSAGEFLAGS_Flagged)
			{
				$isFlagged = 'true';
				$flaggedImg = '<img class="'.$handclass.'" src="skins/'.$this->_pagebuilder->SkinName().'/menu/flag.gif" />';
			}
			else 
			{
				$isFlagged = 'false';
				$flaggedImg = '<img class="'.$handclass.'" src="skins/'.$this->_pagebuilder->SkinName().'/menu/unflag.gif" />';
			}
			
			if ($msg->IdFolder == -1 && $this->folder && $this->folder->SyncType == FOLDERSYNC_DirectMode)
			{
				$msg->IdFolder = $this->folder->IdDb;
			}
			
			$isForwarded = (($msg->Flags & MESSAGEFLAGS_Forwarded) == MESSAGEFLAGS_Forwarded) ? 'true' : 'false';
			$isDeleted = (($msg->Flags & MESSAGEFLAGS_Deleted) == MESSAGEFLAGS_Deleted) ? 'true' : 'false';
			$isGrey = (($msg->Flags & MESSAGEFLAGS_Grayed) == MESSAGEFLAGS_Grayed) ? 'true' : 'false';
			$isReplied = 'false';
			
			$date = &$msg->GetDate();
			$date->FormatString = $this->_proc->account->DefaultDateFormat;
			$date->TimeFormat = $this->_proc->account->DefaultTimeFormat;
			
			$sep = '-----';
			$char = ($msg->Charset > -1) ? ConvertUtils::GetCodePageName($msg->Charset) : -1;
			$idString =	$msg->IdMsg . $sep . $msg->Uid . $sep . $msg->IdFolder . $sep . $char . $sep;
			
			$folderName = ($this->folder) ? $this->folder->FullName : '';
			
			$this->_pagebuilder->AddInitText('messObj = {Read: '. $isRead .', Replied: '. $isReplied 
				.', Forwarded: '. $isForwarded .', Flagged: '. $isFlagged .', Deleted: '. $isDeleted
				.', Gray: '. $isGrey .', Id: '. (int) $msg->IdMsg .', Uid: "'. ConvertUtils::ClearJavaScriptString($msg->Uid, '"')
				.'", FolderId: '. $msg->IdFolder .', FolderFullName: "'. ConvertUtils::ClearJavaScriptString($folderName, '"').'", FromAddr: "", Subject: ""}');
				
			$this->_pagebuilder->AddInitText('InboxLines.AddLine(new CSelectionPart(document.getElementById("'.ConvertUtils::ClearJavaScriptString($idString, '"').'"), "'.$this->_pagebuilder->SkinName().'" , messObj));');
			
			$from = ConvertUtils::WMHtmlSpecialChars($msg->GetFromAsStringForSend());
			$to = ConvertUtils::WMHtmlSpecialChars($msg->GetAllRecipientsEmailsAsString(true));
			$subject = ConvertUtils::WMHtmlSpecialChars($msg->GetSubject(true));
			
			if (isset($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]) && strlen($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]) > 0)
			{
				$from = preg_replace('/'.preg_quote($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]).'/i',
							'<font>$0</font>', $from);
							
				$to = preg_replace('/'.preg_quote($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]).'/i',
							'<font>$0</font>', $to);
							
				$subject = preg_replace('/'.preg_quote($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]).'/i',
							'<font>$0</font>', $subject);
			}

			$subjectId = ($i == 0) ? 'id="subject"' : '';

			$out .= '
			<tr onclick="CheckThisLine(event, this);" ondblclick="CheckThisLineDb(event, this);" id="'.ConvertUtils::AttributeQuote($idString).'">
			<td'.$stylewidth[0][(int) ($i == 0)].' id="none"><input type="checkbox" /></td>
			<td'.$stylewidth[1][(int) ($i == 0)].'>';
			$out .= ((int) $msg->HasAttachments() == 1) ? '<img src="skins/'.$this->_pagebuilder->SkinName().'/menu/attachment.gif" />' : '';
			$out .= '</td><td'.$stylewidth[2][(int) ($i == 0)].'>'. $flaggedImg;
			
			$tempFromTo = ($this->folder && $this->folder->ToFolder) ? $to : $from;
			$out .= '</td><td'.$stylewidth[3][(int) ($i == 0)].' class="wm_inbox_from_subject"><nobr>'.$atemp_1.$tempFromTo.$atemp_2.'</nobr></td>
			<td'.$stylewidth[4][(int) ($i == 0)].'><nobr>'.
			$date->GetFormattedDate($this->_proc->account->GetDefaultTimeOffset())
			.'</nobr></td><td'.$stylewidth[5][(int) ($i == 0)].'><nobr>'.GetFriendlySize($msg->Size).'</nobr></td>
			<td'.$stylewidth[6][(int) ($i == 0)].' class="wm_inbox_from_subject" '.$subjectId.'><nobr>'.$atemp_1.$subject.$atemp_2.'</nobr></td>
			</tr>';
		}
		
		if (isset($this->_proc->sArray[SEARCH_ARRAY]) && isset($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]) && strlen($this->_proc->sArray[SEARCH_ARRAY][S_TEXT]) > 0 && $c == 0)
		{
				$out = '<tr><td colspan="6" style="width: 404px;"></td>';
				$out .= '<td style="width: 150px;" id="subject"></td></tr>
				<tr><td colspan="7"><div class="wm_inbox_info_message">'.InfoNoMessagesFound.'</div></td></tr>
				';			
		} 
		else if ($c == 0)
		{
				$out = '<tr><td colspan="6" style="width: 404px;"></td>';
				$out .= '<td style="width: 150px;" id="subject"></td></tr>
				<tr><td colspan="7"><div class="wm_inbox_info_message">'.JS_LANG_InfoEmptyFolder.'</div></td></tr>
				';
		}
		
		return $out;
		
	}

	/**
	 * @return string
	 */
	function ToHTML()
	{
		return 
		'<table id="list">'
		.
		$this->MessageListTr()
		.
		'</table>
<form name="messform" id="messform" action="base-iframe.php?mode=preview" target="iframe_container" method="POST">
<input type="hidden" name="m_id" id="m_id" value="" />
<input type="hidden" name="m_uid" id="m_uid" value="" />
<input type="hidden" name="f_id" id="f_id" value="" />
<input type="hidden" name="f_name" id="f_name" value="" />
<input type="hidden" name="charset" id="charset" value="" />
<input type="hidden" name="plain" id="plain" value="-1" />
<input type="hidden" name="mtype" id="mtype" value="msg" />
</form>
';
	}
	
}
