/*
Classes:
	CMessagesListViewScreen
*/

function CreateMoveActionFunc(id, fullName)
{
	return function() { RequestMessagesOperationHandler(MOVE_TO_FOLDER, [], 0, id, fullName) }
}

function CreateFolderClickFunc(id, fullName, obj, element)
{
	return function() {
		obj.FolderClick(id, fullName, element);
		return false;
	}
}

function CMessagesListViewScreen(skinName)
{
	this.Id = SCREEN_MESSAGES_LIST_VIEW;
	this.isBuilded = false;
	this.hasCopyright = false;
	this.SearchFormId = 'search_form' + this.Id;
	this.shown = false;
	
	this._checkMail = new CCheckMail();

	this._showTextLabels = true;
	this._hideContacts = false;
	this._enableMailboxSizeLimit = true;
	this._mailboxLimit = 0;
	this._mailboxSize = 0;
	this._isDisplayFolders = null;
	this._defaultFoldersWidth = 115;
	this._markNumber = MARK_AS_READ;
	this._replyNumber = REPLY;
	this._skinName = skinName;
	this._timeOffset = 0;
	this._dateFormat = '';
	this._messagesPerPage = 20;
	this.isDirectMode = false;

	this._page = 1;
	this._lookForStr = '';
	this._searchMode = 0;
	this._folderId = -1;
	this._folderFullName = '';
	this._sortField = SORT_FIELD_DATE;
	this._sortOrder = SORT_ORDER_DESC;
	this._deleteNumber = DELETE;
	this._removeCount = 0;

	this._SEPARATOR = '@#%';
	this.Protocol = POP3_PROTOCOL;
	this.isInboxDirectMode = false;

	this._mainContainer = null;

	this.SearchFormObj = null;
	this._lookForSmallObj = null;
	this._bigSearchForm = null;
	this._lookForBigObj = null;
	this._searchButtonImg = null;
	this._searchIn = null;
	this._quickSearch = null;
	this._slowSearch = null;

	this._toolBar = null;
	this._pop3DeleteTool = null;
	this._emptyTrashTool = null;
	this._reloadFoldersTool = null;
	this._imap4DeleteTool = null;
	this._markTool = null;
	this._moveTool = null;
	this._moveMenu = null;
	this._inboxMoveItem = null;

	this._foldersContainer = null;
	this._foldersHide = null;
	this._foldersHideImg = null;
	this._foldersList = null;
	this._manageFolders = null;
	this._foldersWidth = this._defaultFoldersWidth;
	this._foldersBordersWidth = 1;
	this._foldersObj = null;
	this._foldersObjWait = null;//folders list, wich was update in manage folders, so it's necessary to fill it
	this._wasRestored = false;
	this._foldersParam = Array();
	this.InboxId = -1;
	this.InboxFullName = '';
	this.SentId = -1;
	this.SentFullName = '';
	this.DraftsId = -1;
	this.DraftsFullName = '';
	this.TrashId = -1;
	this.TrashFullName = '';
	this._currFolder = null;

	this._vertResizerObj = null;

	this._pageSwitcher = null;

	this._inboxContainer = null;
	this._inboxTable = null;
	this._fromToColumn = null;
	this._inboxLines = null;
	this._selection = new CSelection();
	this._dragNDrop = new CDragNDrop('Messages', 'msg_drop.gif', 'msg_not_drop.gif', skinName);
	this._dragNDrop.SetSelection(this._selection);
	this._inboxWidth = 361;
	this._inboxBordersWidth = 1;

	this._messagesObj = null;//object CMessage, that is replaced in preview pane

	this._msgViewer = null;

	this.ShowPictures = function ()
	{
		var msg = this._msgObj;
		if (msg.Safety != 1)
		{
			msg.ShowPictures();
			this._msgViewer.Fill(msg);
			this.ResizeScreen();
		}
	}

	this._picturesControl = new CMessagePicturesController(this.ShowPictures, this);
	this._showPicturesTbl = null;

	this._messageId = -1;
	this._messageUid = '';
	this._msgCharset = AUTOSELECT_CHARSET;
	this._msgObj = null;
	this.msgForView = null;
	this.forEditParams = [];
	this.fromDraftsParams = [];
	
	this._messagesInFolder = null;
	this._spaceInfo = null;
	this._spaceProgressBar = null;

	//logo + accountslist + toolbar + lowtoolbar
	this._externalHeight = 56 + 32 + 26 + 24;
	this._logo = null;
	this._accountsBar = null;
	this._lowToolBar = null;

	//manage folders + hide folders
	this._foldersExternalHeight = 22 + 20;
	this._foldersHeight = 100;
	this._foldersBordersHeight = 1;
	
	this._hResizerHeight = 4;
	this._horizResizerObj = null;
	
	this._inboxHeight = 100;
	this._inboxHeadersHeight = 21;
	this._inboxAllBordersHeight = 3;
	//border + inbox headers
	this._defaultInboxHeight = 150;
	this._minUpper = 1 + this._inboxHeadersHeight;

	this._replyTool = null;
	this._forwardTool = null;

	this._addToAddressBookImg = null;
	this._fromName = '';
	this._fromEmail = '';

	this._messageHeadersHeight = 48;
	this._messagePadding = 16;
	//2 borders + resizer + message headers + message
	this._minLower = 2 + this._hResizerHeight + this._messageHeadersHeight + 100;
	this._minLower = 200;
	this._messageHeadersContainer = null;
	this._messageFrom = null;
	this._messageAddFrom = null;
	this._messageSwitcher = null;
	this._messageTo = null;
	this._messageDate = null;
	this._messageCC = null;
	this._messageBCC = null;
	this._copies = null;
	this._messageSubject = null;
	this._messageCharset = null;
	
	this.Debug = '';

	this.PlaceData = function(Data)
	{
		var Type = Data.Type;
		switch (Type){
			case TYPE_FOLDERS_LIST:
				this.PlaceFoldersList(Data);
				break;
			case TYPE_MESSAGES_LIST:
				this.PlaceMessagesList(Data);
				break;
			case TYPE_MESSAGE:
				this._msgObj = Data;
				var id = Data.Id;
				var folderId = Data.FolderId;
				var folderFullName = Data.FolderFullName;
				var uid = Data.Uid;
				var charset = Data.Charset;
				if (null != this.msgForView && this.msgForView.Id == id && this.msgForView.Uid == uid &&
				 this.msgForView.FolderId == folderId && this.msgForView.FolderFullName == folderFullName &&
				 this.msgForView.Charset == charset) {
					SelectScreenHandler(SCREEN_VIEW_MESSAGE);
					this.msgForView = null;
				}
				this._messageId = id;
				this._messageUid = uid;
				this._msgCharset = charset;
				this.FillByMessage();
				break;
			case TYPE_MESSAGES_OPERATION:
				this.PlaceMessagesOperation(Data);
				break;
		}
	}
	
	this.ResizeBody = function (mode)
	{
		if (this.isBuilded) {
			this.ResizeScreen(mode);
			if (!Browser.IE && mode == 'all') {
				this.ResizeScreen(mode);
			}
		}
	}
	
	this.ResizeScreen = function (mode)
	{
		if (mode == 'height')
			CreateCookie('wm_horiz_resizer', this._horizResizerObj._topPosition, COOKIE_STORAGE_DAYS);
		if (mode == 'width' && this._isDisplayFolders == true)
		{
			CreateCookie('wm_vert_resizer', this._vertResizerObj._leftPosition, COOKIE_STORAGE_DAYS);
		}
		var isAuto = false;
		var height = GetHeight();
		var innerHeight = height - this.GetExternalHeight();
		if (innerHeight < 300) {
			innerHeight = 300;
			isAuto = true;
		}

		if (mode != 'width') {
			this._inboxHeight = this._horizResizerObj._topPosition - this._minUpper;
			this.ResizeFoldersHeight(innerHeight);
			this._vertResizerObj.updateVerticalSize(innerHeight);
			this.ResizeInboxHeight(innerHeight);
		}
		
		var resizerWidth = 6;
		var width = GetWidth();
		if (mode != 'height') {
			var fpWidth = this._foldersWidth;
			var validator = new CValidate();
			var leftPos = this._vertResizerObj._leftPosition;
			if (this._isDisplayFolders == true && validator.IsPositiveNumber(leftPos) && leftPos >=18) {
				fpWidth = leftPos;
			}
			var ipWidth = width - fpWidth - resizerWidth;
			if (ipWidth < 550) {
				ipWidth = 550;
				fpWidth = width - ipWidth - resizerWidth;
				if (this._isDisplayFolders == true) {
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
			this._foldersWidth = fpWidth;
			if (this._isDisplayFolders == true) this._defaultFoldersWidth = fpWidth;
			this.ResizeInboxContainerWidth(ipWidth);
			if (this._isDisplayFolders == true)
				this._vertResizerObj._leftPosition = fpWidth;
			this.ResizeFoldersWidth(fpWidth);
			this._horizResizerObj.updateHorizontalSize(ipWidth);
		}

		this.ResizeInboxWidth();
		if (mode != 'height') { this.ResizeMessageWidth(); }
		else { this._msgViewer.ResizeWidth(this._inboxWidth); }
		if (mode != 'width') {this.ResizeInboxHeight(innerHeight);}
		if (null != this._pageSwitcher) this._pageSwitcher.Replace(this._inboxLines);
//		if (Browser.Safari) {
//			isAuto = true;
//		}
		SetBodyAutoOverflow(isAuto);
		this._dragNDrop.Resize();
	}
	
	this.GetExternalHeight = function()
	{
		var x = this._logo.offsetHeight + this._accountsBar.offsetHeight + this._toolBar.table.offsetHeight + this._lowToolBar.offsetHeight;
		if (x != 0)
			this._externalHeight = x;
		return this._externalHeight;
	}
	
	this.ResizeFoldersHeight = function(height)
	{
		var validator = new CValidate();
		if (validator.IsPositiveNumber(height) && height >=100)
		{
			this._foldersHeight = height;
			var allHeight = this._foldersHeight - this._foldersBordersHeight;
			var externalHeight = this._foldersHide.offsetHeight + this._manageFolders.offsetHeight;
			if (externalHeight != 0)
				this._foldersExternalHeight = externalHeight;
			this._foldersContainer.style.height = allHeight + 'px';
			this._foldersList.style.height = (allHeight - this._foldersExternalHeight) + 'px';
		}
	}
	
	this.ResizeMessageWidth = function()
	{
		var clientWidth, rightPartClientWidth;
		if (!(rightPartClientWidth = this._messageSwitcher.clientWidth))
			rightPartClientWidth = 150;
		rightPartClientWidth = rightPartClientWidth + 17 + 32;
		this._messageFrom.style.width = 'auto';
		if (clientWidth = this._messageFrom.clientWidth) {
			if (clientWidth > (this._inboxWidth - rightPartClientWidth))
				this._messageFrom.style.width = (this._inboxWidth - rightPartClientWidth) + 'px';
		}
		if (!(rightPartClientWidth = this._messageDate.clientWidth))
			rightPartClientWidth = 150;
		rightPartClientWidth = rightPartClientWidth + 24;
		this._messageTo.style.width = 'auto';
		if (clientWidth = this._messageTo.clientWidth) {
			if (clientWidth > (this._inboxWidth - rightPartClientWidth))
				this._messageTo.style.width = (this._inboxWidth - rightPartClientWidth) + 'px';
		}
		if (this._copies.className == '') {
			if (this._messageCC.innerHTML == '') {
				this._messageBCC.style.width = this._inboxWidth - 12;
			} else if (this._messageBCC.innerHTML == '') {
				this._messageCC.style.width = this._inboxWidth - 12;
			} else {
				var halfWidth = Math.ceil(this._inboxWidth) - 12;
				this._messageCC.style.width = 'auto';
				if (this._messageCC.clientWidth > halfWidth) {
					this._messageCC.style.width = halfWidth;
					this._messageBCC.style.width = halfWidth;
				}
			}
		}
		rightPartClientWidth = this._messageCharset.clientWidth;
		this._messageSubject.style.width = 'auto';
		if (rightPartClientWidth != 0) {
			rightPartClientWidth = rightPartClientWidth + 17;
			if (clientWidth = this._messageSubject.clientWidth) {
				if (clientWidth > (this._inboxWidth - rightPartClientWidth))
					this._messageSubject.style.width = (this._inboxWidth - rightPartClientWidth) + 'px';
			}
		}
		this._msgViewer.ResizeWidth(this._inboxWidth);
	}
	
	this.GetMessageExternalHeight = function()
	{
		var inboxHeight = this._inboxTable.GetHeight();
		offsetHeight = this._messageHeadersContainer.offsetHeight;
		if (offsetHeight)
		{
			this._messageHeadersHeight = offsetHeight;
		}
		offsetHeight = this._showPicturesTbl.offsetHeight;
		if (offsetHeight)
		{
			this._messageHeadersHeight += offsetHeight;
		}
		return inboxHeight + this._inboxAllBordersHeight + this._hResizerHeight + this._messageHeadersHeight;
	}
	
	this.ResizeInboxHeight = function(height)
	{
		var validator = new CValidate();
		if (validator.IsPositiveNumber(height) && height >=100)
		{
			var messInnerHeight = height - this.GetMessageExternalHeight();
			if (messInnerHeight < 100) {
				this._inboxHeight -= 100 - messInnerHeight;
				messInnerHeight = 100;
			}
			if (this._inboxHeight < 100) {
				this._inboxHeight = 100;
			}
			this._inboxTable.SetLinesHeight(this._inboxHeight);
			this._horizResizerObj._topPosition = this._inboxHeight + this._minUpper;
			this._msgViewer.ResizeHeight(messInnerHeight);
		}
	}

	this.ParseSettings = function (settings)
	{
		this._hideContacts = settings.HideContacts;
		this._showTextLabels = settings.ShowTextLabels;
		this._enableMailboxSizeLimit = settings.EnableMailboxSizeLimit;
		this._mailboxLimit = settings.MailboxLimit;
		this._mailboxSize = settings.MailboxSize;
		if (null == this._isDisplayFolders)
			this._isDisplayFolders = (settings.HideFolders) ? false : true;
		this._defaultInboxHeight = settings.HorizResizer;
		this._defaultFoldersWidth = settings.VertResizer;
		this._markNumber = settings.Mark;
		this._replyNumber = settings.Reply;
		this.ChangeSkin(settings.DefSkin);
		if (this.isBuilded) {
			this.FillSpaceInfo(settings.MailBoxLimit, settings.MailBoxSize);
		}
		if (!settings.AllowChangeSettings && null != this._manageFolders)
			this._manageFolders.className = 'wm_hide';
		if (this._timeOffset != settings.TimeOffset ||
		this._dateFormat != settings.DateFormat ||
		this._messagesPerPage != settings.MsgsPerPage) {
			if (this._messagesPerPage != settings.MsgsPerPage) {
				this._page = 1;
			}
			this._timeOffset = settings.TimeOffset;
			this._dateFormat = settings.DateFormat;
			this._messagesPerPage = settings.MsgsPerPage;
			if (this.isBuilded) {
				SetHistoryHandler(
					{
						ScreenId: this.Id,
						FolderId: this._folderId,
						FolderFullName: this._folderFullName,
						Page: this._page,
						SortField: this._sortField,
						SortOrder: this._sortOrder,
						LookForStr: '',
						SearchMode: 0,
						RedrawType: REDRAW_NOTHING,
						RedrawObj: null,
						MsgId: null,
						MsgUid: null,
						MsgFolderId: null,
						MsgFolderFullName: null,
						MsgCharset: null,
						MsgParts: null
					}
				);
			} else {
				this.RedrawPages(this._page);
			}
		} else {
			this.RedrawPages(this._page);
		}
	}
	
	this.ChangeSkin = function (newSkin)
	{
		if (this._skinName != newSkin) {
			this._skinName = newSkin;
			if (this.isBuilded) {
				this._toolBar.ChangeSkin(newSkin);
				if (null != this.SearchFormObj) {
					this.SearchFormObj.ChangeSkin(newSkin);
				}
				if (this._isDisplayFolders)
					this._foldersHideImg.src = './skins/' + newSkin + '/folders/hide_folders.gif';
				else
					this._foldersHideImg.src = './skins/' + newSkin + '/folders/show_folders.gif';
				this._searchButtonImg.src = 'skins/' + newSkin + '/menu/search_button_big.gif';
				this._addToAddressBookImg.src = 'skins/' + this._skinName + '/contacts/save.gif';
				this.CleanFoldersList();
				this.FillByFolders();
				this._inboxTable.SetSort(this._sortField, this._sortOrder);
				this._inboxTable.ChangeSkin(newSkin);
				this.RedrawPages(this._page);
				this.FillByMessages();
			}
		}
	}
	
	this.Build = function(container, accountsBar, PopupMenus, settings)
	{
		this.ParseSettings(settings);
		this._logo = document.getElementById('logo');
		this._mainContainer = CreateChild(container, 'div');
		this._mainContainer.className = 'wm_hide';
		this._accountsBar = accountsBar;

		this.BuildAdvancedSearchForm();
		this.BuildToolBar(PopupMenus);

		var div = CreateChild(this._mainContainer, 'div');
		div.className = 'wm_background';
		var tbl = CreateChild(div, 'table');
		tbl.className = 'wm_mail_container';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.rowSpan = 3;
		this.BuildFoldersPart(td);

		td = tr.insertCell(1);
		td.rowSpan = 3;
		var VResizer = CreateChild(td, 'div');
		VResizer.className = 'wm_vresizer';
		div = CreateChild(td, 'div');
		div.className = 'wm_vresizer_width';

		this._inboxContainer = tr.insertCell(2);
		var obj = this;
		this._inboxContainer.onmousedown = function (ev)
		{
			if (obj._selection.Length > 0 && isRightClick(ev))
			{
				obj._selection.UncheckAll();
			}
			return false;
		}
		this.BuildInboxTable();

		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		div = CreateChild(td, 'div');
		div.className = 'wm_hresizer_height';
		var HResizer = CreateChild(td, 'div');
		HResizer.className = 'wm_hresizer';
		div = CreateChild(td, 'div');
		div.className = 'wm_hresizer_height';

		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		this.BuildMessageContainer(td);

		tr = tbl.insertRow(3);
		this._lowToolBar = tr.insertCell(0);
		this._lowToolBar.colSpan = 3;
		this._lowToolBar.className = 'wm_lowtoolbar';
		this._messagesInFolder = CreateChild(this._lowToolBar, 'span');
		this._messagesInFolder.className = 'wm_lowtoolbar_messages';
		this.WriteMsgsCountInFolder(0);

		if (this._enableMailboxSizeLimit)
		{
			this._spaceInfo = CreateChild(this._lowToolBar, 'span');
			this._spaceInfo.className = 'wm_lowtoolbar_space_info';
			div = CreateChild(this._spaceInfo, 'div');
			div.className = 'wm_progressbar';
			this._spaceProgressBar = CreateChild(div, 'div');
			this._spaceProgressBar.className = 'wm_progressbar_used';
			this.FillSpaceInfo(settings.MailBoxLimit, settings.MailBoxSize);
		}

		this._vertResizerObj = new CVerticalResizer(VResizer, this._mainContainer, 2, 81, 551, this._defaultFoldersWidth, 'WebMail.ResizeBody(\'width\');');
		this._horizResizerObj = new CHorizontalResizer(HResizer, this._mainContainer, 2, this._minUpper + 100, this._minLower, this._defaultInboxHeight, 'WebMail.ResizeBody(\'height\');');
		if (this._isDisplayFolders == false)
			this._vertResizerObj.free();
		this.isBuilded = true;
	}//Build
	
	this.RedrawFolderControls = function (redrawElement, id, fullName)
	{
		if (redrawElement) {
			if (this._currFolder) this._currFolder.className = '';
			redrawElement.className = 'wm_select_folder';
			this._currFolder = redrawElement;
			this.CleanMessageBody();
		}
		if (id && fullName) {
			this.ChangeFolder(id, fullName);
			if (this.TrashFullName == fullName && this.TrashId == id) {
				this._emptyTrashTool.className = 'wm_toolbar_item';
			} else {
				this._emptyTrashTool.className = 'wm_hide';
			}
		}
		if (id == -1 && fullName == '') {
			if (this._currFolder) this._currFolder.className = '';
			this.ChangeFolder(id, fullName);
		}
	}
	
	this.ChangeFolder = function (id, fullName)
	{
		this._folderId = id;
		this._folderFullName = fullName;
		this.CleanMessageBody();
		this._searchIn.value = this._folderId + this._SEPARATOR + this._folderFullName;
	}

	this.BuildToolBar = function (PopupMenus)
	{
		var obj = this;
		var toolbar = new CToolBar(this._mainContainer, this._skinName);
		//new message tool
		var item = toolbar.AddItem(TOOLBAR_NEW_MESSAGE, function () { SetHistoryHandler({ ScreenId: SCREEN_NEW_MESSAGE }); }, false);
		//check mail tool
		item = toolbar.AddItem(TOOLBAR_CHECK_MAIL, function () { obj._checkMail.Start(); }, false);
		//reload folders list tool; only imap
		this._reloadFoldersTool = toolbar.AddItem(TOOLBAR_RELOAD_FOLDERS, function () { GetFoldersListHandler(WebMail.Accounts.CurrId, 2); }, true);
		//reply tool (reply, reply all); absent in drafts
		this._replyTool = toolbar.AddReplyItem(this._replyNumber, PopupMenus, false);
		//forward tool; absent in drafts
		this._forwardTool = toolbar.AddItem(TOOLBAR_FORWARD, CreateReplyClick(FORWARD), false);
		//mark tool; absent in inbox in direct mode in pop3
		this._markTool = toolbar.AddMarkItem(this._markNumber, PopupMenus, true);
		//move to folder tool; absent in inbox in direct mode in pop3
		var div = CreateChild(document.body, 'div');
		this._moveMenu = div;
		div.className = 'wm_hide';
		this._moveTool = toolbar.AddMoveItem(PopupMenus, 'MoveToFolder', div, true, 'move_to_folder.gif', 'wm_menu_move_to_folder_img');
		//delete tools
		this._pop3DeleteTool = toolbar.AddItem(TOOLBAR_DELETE, function () { RequestMessagesOperationHandler(DELETE, [], 0); }, true);
		this._emptyTrashTool = toolbar.AddItem(TOOLBAR_EMPTY_TRASH, function () { RequestMessagesOperationHandler(PURGE, [], 0); }, true);
		this._imap4DeleteTool = toolbar.AddDeleteItem(this._deleteNumber, PopupMenus, true);
		
		var searchParts = toolbar.AddSearchItems();
		this.SearchFormObj = new CSearchForm(this._bigSearchForm, searchParts.SmallForm, searchParts.Control, searchParts.ControlImg, this.SearchFormId, this._lookForBigObj, searchParts.LookFor, this._skinName);
		if (null != this._searchIn) {
			this.SearchFormObj.SetSearchIn(this._searchIn);
		}
		this._lookForSmallObj = searchParts.LookFor;
		var obj = this;
		searchParts.LookFor.onkeypress = function (ev) {
			if (isEnter(ev)) {
				obj.GetSearchResultsFromSmall();
			}
		}
		searchParts.ActionImg.onclick = function () {
			obj.GetSearchResultsFromSmall();
		}
		this._toolBar = toolbar;
	}
	
	this.CleanMessageBody = function ()
	{
		this._msgObj = null;
		this._messageFrom.innerHTML = '';
		this._messageAddFrom.className = 'wm_hide';
		this._messageTo.innerHTML = '';
		this._messageDate.innerHTML = '';
		this._messageCC.innerHTML = '';
		this._messageBCC.innerHTML = '';
		this._copies.className = 'wm_hide';
		this._messageSubject.innerHTML = '';
		this._messageCharset.innerHTML = '';
		this._msgViewer.Clean();
		this._picturesControl.Hide();
		this.ResizeBody('all');
		this._msgObj = null;
	}
	
	this.FillByMessage = function()
	{
		var thisMessage = this._msgObj;

		var from = this._msgObj.FromAddr;
		if (from) this._messageFrom.innerHTML = '<font>' + Lang.From + ':</font>' + from;
		else this._messageFrom.innerHTML = '<font>' + Lang.From + ':</font>';
		var to = this._msgObj.ToAddr;
		if (to) this._messageTo.innerHTML = '<font>' + Lang.To + ':</font>' + to;
		else this._messageTo.innerHTML = '<font>' + Lang.To + ':</font>';
		var date = this._msgObj.Date;
		if (date) this._messageDate.innerHTML = '<font>' + Lang.Date + ':</font>' + date;
		else this._messageDate.innerHTML = '<font>' + Lang.Date + ':</font>';

		//email parts for adding to contacts
		var fromParts = GetEmailParts(HtmlDecode(from));
		this._fromName = fromParts.Name;
		this._fromEmail = fromParts.Email;
		this._messageAddFrom.className = 'wm_message_left';
		
		this._copies.className = 'wm_hide';
		var cc = this._msgObj.CCAddr;
		if (cc) {
			this._messageCC.innerHTML = '<font>' + Lang.CC + ':</font>' + cc;
			this._copies.className = '';
		}
		var bcc = this._msgObj.BCCAddr;
		if (bcc) {
			this._messageBCC.innerHTML = '<font>' + Lang.BCC + ':</font>' + bcc;
			this._copies.className = '';
		}

		var subject = this._msgObj.Subject;
		if (subject) this._messageSubject.innerHTML = '<font>' + Lang.Subject + ':</font>' + subject;
		else this._messageSubject.innerHTML = '<font>' + Lang.Subject + ':</font>';
		if (this._msgObj.HasCharset && this._msgObj.Charset == -1) {
			CleanNode(this._messageCharset);
		} else {
			this.FillMessageCharset(this._msgObj.Charset);
		}

		this._picturesControl.SetSafety(thisMessage.Safety);
		switch (thisMessage.Safety)
		{
			case 0:
				this._picturesControl.Show();
				var fromParts = GetEmailParts(HtmlDecode(thisMessage.FromAddr));
				this._picturesControl.SetFromAddr(fromParts.Email);
			break;
			case 1:
				this._picturesControl.Hide();
			break;
			case 2:
				this._picturesControl.Show();
			break;
		}

		this._msgViewer.Fill(thisMessage);
		this.ResizeBody('all');
		if (null != this._pageSwitcher) this._pageSwitcher.Replace(this._inboxLines);
	}//FillByMessage

	this.FillMessageCharset = function (charset) {
		var charset = this._msgObj.Charset;
		var charsetCont = this._messageCharset;
		CleanNode(charsetCont);
		var font = CreateChild(charsetCont, 'font');
		font.innerHTML = Lang.Charset + ':';
		var sel = CreateChild(charsetCont, 'select');
		sel.onchange = function () {
			SetHistoryHandler(
				{
					ScreenId: obj.Id,
					FolderId: obj._folderId,
					FolderFullName: obj._folderFullName,
					Page: obj._page,
					SortField: obj._sortField,
					SortOrder: obj._sortOrder,
					LookForStr: obj._lookForStr,
					SearchMode: obj._searchMode,
					RedrawType: REDRAW_NOTHING,
					RedrawObj: null,
					MsgId: obj._messageId,
					MsgUid: obj._messageUid,
					MsgFolderId: obj._folderId,
					MsgFolderFullName: obj._folderFullName,
					MsgCharset: this.value,
					MsgParts: [PART_MESSAGE_HEADERS, PART_MESSAGE_HTML, PART_MESSAGE_ATTACHMENTS]
				}
			);
		}
		var opt;
		var obj = this;
		for (var i in Charsets) {
			if (Charsets[i].Value == 0) {
				var value = AUTOSELECT_CHARSET;
			} else {
				var value = Charsets[i].Value;
			}
			opt = CreateChildWithAttrs(sel, 'option', [['value', value]]);
			opt.innerHTML = Charsets[i].Name;
			if (charset == value) {
				opt.selected = true;
			} else {
				opt.selected = false;
			}
		}
	}
	
	this.BuildMessageContainer = function(td)
	{
		var obj = this;
		var div = CreateChild(td, 'div');
		div.className = 'wm_message_container';
		
		var tbl = CreateChild(div, 'table');
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);

		this._showPicturesTbl = this._picturesControl.Build(td);

		this._messageHeadersContainer = CreateChild(td, 'div');
		this._messageHeadersContainer.className = 'wm_message_headers';

		var div2 = CreateChild(this._messageHeadersContainer, 'div');
		this._messageFrom = CreateChild(div2, 'span');
		this._messageFrom.className = 'wm_message_left wm_message_resized';
		var span = CreateChild(div2, 'span');
		span.className = 'wm_hide';
		var img = CreateChildWithAttrs(span, 'img', [['class', 'wm_add_address_book_img'], ['src', 'skins/' + this._skinName + '/contacts/save.gif'], ['title', Lang.AddToAddressBokk]]);
		WebMail.LangChanger.Register('title', img, 'AddToAddressBokk', '');
		img.onclick = function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_CONTACTS,
					Entity: PART_NEW_CONTACT,
					Name: obj._fromName,
					Email: obj._fromEmail
				}
			);
		}
		this._addToAddressBookImg = img;
		this._messageAddFrom = span;
		
		this._messageSwitcher = CreateChild(div2, 'span');
		this._messageSwitcher.className = 'wm_message_right';
		var a = CreateChildWithAttrs(this._messageSwitcher, 'a', [['href', '']]);
		a.innerHTML = Lang.SwitchToPlain;
		a.onclick = function () {
			var part = obj._msgViewer.GetMsgPart();
			GetMessageHandler(obj._messageId, obj._messageUid, obj._folderId, obj._folderFullName, [part], obj._msgCharset);
			return false;
		}

		div2 = CreateChild(this._messageHeadersContainer, 'div');
		this._messageTo = CreateChild(div2, 'span');
		this._messageTo.className = 'wm_message_left wm_message_resized';
		this._messageDate = CreateChild(div2, 'span');
		this._messageDate.className = 'wm_message_left';

		div2 = CreateChild(this._messageHeadersContainer, 'div');
		div2.className = 'wm_hide';
		this._messageCC = CreateChild(div2, 'span');
		this._messageCC.className = 'wm_message_left wm_message_resized';
		this._messageBCC = CreateChild(div2, 'span');
		this._messageBCC.className = 'wm_message_left';
		this._copies = div2;

		div2 = CreateChild(this._messageHeadersContainer, 'div');
		this._messageSubject = CreateChild(div2, 'span');
		this._messageSubject.className = 'wm_message_left wm_message_resized';
		this._messageCharset = CreateChild(div2, 'span');
		this._messageCharset.className = 'wm_message_right';

		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		this._msgViewer = new CMessageViewer();
		this._msgViewer.Build(td, 0);
		this._msgViewer.SetSwitcher(this._messageSwitcher, 'wm_message_right', a);
	}
}

CMessagesListViewScreen.prototype = MessagesListPrototype;
