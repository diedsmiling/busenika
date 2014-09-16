function SetHistoryHandler(args)
{
	scrId = args.ScreenId;
	args = WebMail.CheckHistoryObject(args);
	if (null != args) {
		HistoryStorage.AddStep({FunctionName: 'WebMail.RestoreFromHistory', Args: args});
	}
}

/**********************************************/
function DblClickHandler()
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen)
	{
		if (screen.Id == SCREEN_MESSAGES_LIST_VIEW || screen.Id == SCREEN_MESSAGES_LIST)
		{
			var msg = new CMessage();
			msg.GetFromIdForList(screen._SEPARATOR, this.id);
			if (screen.IsDrafts())
			{
				var screenId = SCREEN_NEW_MESSAGE;
				var parts = [PART_MESSAGE_HEADERS, PART_MESSAGE_HTML, PART_MESSAGE_UNMODIFIED_PLAIN_TEXT, PART_MESSAGE_ATTACHMENTS];
				var fromDrafts = true;
			}
			else
			{
				var screenId = SCREEN_VIEW_MESSAGE;
				var parts = [PART_MESSAGE_HEADERS, PART_MESSAGE_HTML, PART_MESSAGE_ATTACHMENTS];
				var fromDrafts = false;
			}
			SetHistoryHandler(
				{
					ScreenId: screenId,
					FromDrafts: fromDrafts,
					MsgId: msg.Id,
					MsgUid: msg.Uid,
					MsgFolderId: msg.FolderId,
					MsgFolderFullName: msg.FolderFullName,
					MsgCharset: msg.Charset,
					MsgParts: parts
				}
			);
		}
	}
}

function ClickMessageHandler()
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen)
	{
		if (screen.Id == SCREEN_MESSAGES_LIST_VIEW)
		{
			screen._needPlain = false;
			var msg = new CMessage();
			msg.GetFromIdForList(screen._SEPARATOR, this.id);
			if (null == screen._msgObj || msg.Id != screen._msgObj.Id || msg.Uid != screen._msgObj.Uid ||
			 msg.FolderId != screen._msgObj.FolderId || msg.FolderFullName != screen._msgObj.FolderFullName ||
			 msg.Charset != screen._msgObj.Charset) {
				screen.CleanMessageBody();
				var parts = [PART_MESSAGE_HEADERS, PART_MESSAGE_HTML, PART_MESSAGE_ATTACHMENTS];
				if (screen.IsDrafts()) 
				{
					parts = [PART_MESSAGE_HEADERS, PART_MESSAGE_HTML, PART_MESSAGE_UNMODIFIED_PLAIN_TEXT, PART_MESSAGE_ATTACHMENTS]
				}
				var args = {
					ScreenId: screen.Id,
					FolderId: screen._folderId,
					FolderFullName: screen._folderFullName,
					Page: screen._page,
					SortField: screen._sortField,
					SortOrder: screen._sortOrder,
					LookForStr: screen._lookForStr,
					SearchMode: screen._searchMode,
					RedrawType: REDRAW_NOTHING,
					RedrawObj: null,
					MsgId: msg.Id,
					MsgUid: msg.Uid,
					MsgFolderId: msg.FolderId,
					MsgFolderFullName: msg.FolderFullName,
					MsgCharset: msg.Charset,
					MsgParts: parts
				}
				var check = WebMail.CheckHistoryObject(args, true);
				if (check != null)
				{
					SetHistoryHandler(args);
				}
				else if (null == screen._msgObj)
				{
					GetMessageHandler(args.MsgId, args.MsgUid, args.MsgFolderId, args.MsgFolderFullName, args.MsgParts, args.MsgCharset);
				}
			}
		}
	}
}

function SortMessagesHandler()
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen)
	{
		if (screen.Id == SCREEN_MESSAGES_LIST_VIEW || screen.Id == SCREEN_MESSAGES_LIST)
		{
			SetHistoryHandler(
				{
					ScreenId: screen.Id,
					FolderId: screen._folderId,
					FolderFullName: screen._folderFullName,
					Page: screen._page,
					SortField: this.SortField,
					SortOrder: this.SortOrder,
					LookForStr: screen._lookForStr,
					SearchMode: screen._searchMode,
					RedrawType: REDRAW_HEADER,
					RedrawObj: null,
					MsgId: null,
					MsgUid: null,
					MsgFolderId: null,
					MsgFolderFullName: null,
					MsgCharset: null,
					MsgParts: null
				}
			);
		}
	}
}

function ResizeMessagesTab(number)
{
	var screen = WebMail.Screens[WebMail.ScreenId];
	if (screen)
	{
		if (screen.Id == SCREEN_MESSAGES_LIST_VIEW || screen.Id == SCREEN_MESSAGES_LIST)
		{
			screen._inboxTable.ResizeColumnsWidth(number);
		}
	}	
}
/**********************************************/
function SetCookieSettingsHandler(hideFolders, horizResizer, vertResizer, mark, reply, columns) {
	var xml = '';
	var iCount = columns.length;
	for (var i=0; i<iCount; i++)
	{
		xml += '<column id="' + i + '" value="' + columns[i] + '"/>';
	}
	xml = '<columns>' + xml + '</columns>';
	var hf = hideFolders ? '1' : '0';
	WebMail.DataSource.Request({action: 'update', request: 'cookie_settings', hide_folders: hf,
		horiz_resizer: horizResizer, vert_resizer: vertResizer, mark: mark, reply: reply}, xml);
}

function ShowMessagesListHandler() {
	//WebMail.ResizeBody('all');
	WebMail.DataSource.Get(TYPE_ACCOUNTS_LIST, { }, [], '');
}

function GetFoldersListHandler(idAcct, sync) {
	WebMail.DataSource.Get(TYPE_FOLDERS_LIST, { IdAcct: idAcct, Sync: sync }, [], '' );
}

function GetMessagesListHandler(redrawIndex, redrawElement, folderId, folderFullName, sortField, sortOrder, page, lookFor, searchFields) {
	HistoryStorage.Log = '';
	var xml = '<folder id="' + folderId + '"><full_name>' + GetCData(folderFullName) + '</full_name></folder>';
	xml += '<look_for fields="' + searchFields + '">' + GetCData(lookFor) + '</look_for>';
	var screen = WebMail.Screens[WebMail.ListScreenId];
	if (screen) screen.RedrawControls(redrawIndex, redrawElement, sortField, sortOrder, page);
	WebMail.DataSource.Get(TYPE_MESSAGES_LIST, { Page: page, SortField: sortField, SortOrder: sortOrder, 
		FolderId: folderId, FolderFullName: folderFullName, LookFor: lookFor, SearchFields: searchFields }, [], xml );
}

function GetMessageHandler(messageId, messageUid, folderId, folderFullName, messageParts, charset) {
	var screen = WebMail.Screens[WebMail.ListScreenId];
	if (screen && null != screen._selection) {
		var msg = new CMessage();
		msg.Id = messageId;
		msg.Uid = messageUid;
		msg.FolderId = folderId;
		msg.FolderFullName = folderFullName;
		msg.Charset = charset;
		var msgId = msg.GetIdForList(screen._SEPARATOR, screen.Id);
		var readed = screen._selection.SetParams([msgId], 'Read', true, false);
		if (readed != 0) {
			var paramIndex = screen._folderId + screen._folderFullName;
			var params = screen._foldersParam[paramIndex];
			if (params) {
				params.Read(readed);
				WebMail.DataSource.Cache.SetMessagesCount(screen._folderId, screen._folderFullName, params.MsgsCount, params._newMsgsCount);
			}
		}
	}
	charset = charset ? charset : AUTOSELECT_CHARSET;
	var xml = '<param name="uid">' + GetCData(HtmlDecode(messageUid)) + '</param>';
	xml += '<folder id="' + folderId + '"><full_name>' + GetCData(folderFullName) + '</full_name></folder>';
	WebMail.DataSource.Get(TYPE_MESSAGE, {Id: messageId, Charset: charset, Uid: messageUid, FolderId: folderId, FolderFullName: folderFullName}, messageParts, xml );
}

function MoveToFolderHandler(id)
{
	var screenId = WebMail.ScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen && screenId == WebMail.ListScreenId) {
		var folderParams = id.split(screen._SEPARATOR);
		if (2 == folderParams.length)
			RequestMessagesOperationHandler(MOVE_TO_FOLDER, [], 0, folderParams[0], folderParams[1]);
	}
}

function RequestMessagesOperationHandler(type, idArray, fromMenu, toFolderId, toFolderFullName) {
	var screenId = WebMail.ScreenId;
	var screen = WebMail.Screens[screenId];
	if (type != -1 && screenId == WebMail.ListScreenId) {
		var xml = screen.GetXmlMessagesOperation(type, idArray, toFolderId, toFolderFullName);
	}//if (type != -1 && screenId == WebMail.ListScreenId)
}

function GetHandler(type, params, parts, xml) {
	WebMail.DataSource.Get(type, params, parts, xml);
}
/**********************************************/
function SelectScreenHandler(screenId) {
	WebMail.ScreenIdForLoad = screenId;
	ShowScreenHandler();
}

function ShowScreenHandler() {
	WebMail.ShowScreen(ShowScreenHandler);
}

function LoadHandler() {
	WebMail.HideInfo();
	WebMail.DataSource.ParseXML(this.responseXML, this.responseText);
}

function ErrorHandler() {
	WebMail.ShowError(this.ErrorDesc);
}

function InfoHandler() {
	WebMail.ShowInfo(this.Info);
	setTimeout("WebMail.HideInfo();", 10000);
}

function ShowLoadingInfoHandler() {
	WebMail.ShowInfo(Lang.Loading);
}

function TakeDataHandler() {
	if (this.Data) {
		WebMail.PlaceData(this.Data);
	}
}

function RequestHandler(action, request, xml) {
	WebMail.DataSource.Request({action: action, request: request}, xml);
}

function RequestUserSettings(xml) {
	WebMail.DataSource.Request({action: 'update', request: 'settings'}, xml);
}

function RequestAccountProperties(xml) {
	WebMail.DataSource.Request({action: 'update', request: 'account'}, xml);
}

function RequestAddAccountProperties(xml) {
	WebMail.DataSource.Request({action: 'new', request: 'account'}, xml);
}

function RemoveAccountHandle(id) {
	WebMail.DataSource.Request({action: 'delete', request: 'account', 'id_acct': id}, '');
}

function ResizeBodyHandler() {
	if (WebMail) {
		WebMail.ResizeBody('all');
	}
}

function ClickBodyHandler(ev) {
	if (WebMail) {
		WebMail.ClickBody(ev);
	}
}

/* html editor handlers */
function EditAreaLoadHandler() {
	if (WebMail)
		WebMail.LoadEditArea();
}

function CreateLinkHandler(url) {
	WebMail.CreateLink(url);
}

function DesignModeOnHandler(mode) {
	WebMail.DesignModeOn(mode);
}
/*-- html editor handlers */

function LoadAttachmentHandler(attachment) {
	WebMail.LoadAttachment(attachment);
}

function ImportContactsHandler(code, count) {
	switch (code) {
		case 0:
			this.ErrorDesc = Lang.ErrorImportContacts;
			ErrorHandler();
			break;
		case 2:
			count = 0;
		case 1:
			WebMail.ContactsImported(count);
			break;
		case 3:
			this.ErrorDesc = Lang.ErrorInvalidCSV;
			ErrorHandler();
			break;
	}
}

/* check mail handlers */
function SetStateTextHandler(text) {
	var screenId = WebMail.ListScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen) {
		screen._checkMail.SetText(text);
	}
}

function SetCheckingFolderHandler(folder, count) {
	var screenId = WebMail.ListScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen) {
		screen._checkMail.SetFolder(folder, count);
	}
}

function SetRetrievingMessageHandler(number) {
	var screenId = WebMail.ListScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen) {
		screen._checkMail.SetMsgNumber(number);
	}
}

function SetDeletingMessageHandler(number) {
	var screenId = WebMail.ListScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen) {
		screen._checkMail.DeleteMsg(number);
	}
}

function EndCheckMailHandler(error) {
	var screenId = WebMail.ListScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen) {
		screen._checkMail.End();
		GetFoldersListHandler(WebMail.Accounts.CurrId, 1);
	}
	if (error.length > 0) {
		if (error == 'session_error') {
			document.location = LoginUrl + '?error=1';
		} else {
			this.ErrorDesc = error;
			ErrorHandler.call(this);
		}
	}
}

function CheckEndCheckMailHandler() {
	var screenId = WebMail.ListScreenId;
	var screen = WebMail.Screens[screenId];
	if (screen && screen._checkMail.started) {
		screen._checkMail.End();
		GetFoldersListHandler(WebMail.Accounts.CurrId, 1);
		this.ErrorDesc = Lang.ErrorCheckMail;
		ErrorHandler.call(this);
	}
}
/*-- check mail handlers */

/* auto filling handlers */
function GetAutoFillingContactsHandler()
{
	var contactsGroups = new CContacts();
	contactsGroups.LookFor = this.Keyword;
	contactsGroups.SearchType = 1;
	GetHandler(TYPE_CONTACTS, 
	{
		Page: 1,
		SortField: SORT_FIELD_USE_FREQ,
		SortOrder: SORT_ORDER_ASC,
		IdGroup: -1,
		LookFor: this.Keyword
	}, [], contactsGroups.GetInXml());
}

function SelectSuggestionHandler()
{
	if (this.ContactGroup.IsGroup) {
		var screen = WebMail.Screens[SCREEN_NEW_MESSAGE];
		if (screen)
		{
			screen.AddSenderGroup(this.ContactGroup.Id);
		}
	}
}
/*-- auto filling handlers */