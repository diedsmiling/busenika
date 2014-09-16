/*
 * Classes:
 *  CViewMessageScreen
 *  CMessageViewer
 */

function CreateAttachViewClick(href)
{
	return function () {
		var shown = window.open(href, 'Popup', 'toolbar=yes,status=no,scrollbars=yes,resizable=yes,width=760,height=480');
		shown.focus();
		return false;
	}
}

function CViewMessageScreen(skinName)
{
	this.Id = SCREEN_VIEW_MESSAGE;
	this.isBuilded = false;
	this.hasCopyright = false;
	this.BodyAutoOverflow = false;
	
	this._showTextLabels = true;
	this._allowContacts = true;
	this._replyNumber = REPLY;
	this._skinName = skinName;
	
	this._addToAddressBookImg = null;

	this._fromName = '';
	this._fromEmail = '';
	
	this._mainContainer = null;
	this._logo = null;
	this._accountsBar = null;
	this._toolBar = null;
	this._lowToolBar = null;
	this._mode = false;
	//logo + accountslist + toolbar + lowtoolbar
	this.ExternalHeight = 56 + 32 + 26 + 24;

	this._picturesControl = new CMessagePicturesController(this.ShowPictures, this);
	this._showPicturesTbl = null;
	this._completeHeadersTbl = null;
	this._shortHeadersTbl = null;
	this._isShort = false;
	this._rowPadding = 4;
	this._colPadding = 12;

	this._cFromObj = null;
	this._cToObj = null;
	this._cDateObj = null;
	this._CCObj = null;
	this._CCCont = null;
	this._BCCObj = null;
	this._BCCCont = null;
	this._replyToObj = null;
	this._replyToCont = null;
	this._cSubjectObj = null;
	this._charsetObj = null;
	this._charsetCont = null;
	this._showHeadersShower = null;
	this._sFromObj = null;
	this._sToObj = null;
	this._sDateObj = null;
	this._sSubjectObj = null;
	this._importanceImg = null;

	this._msgViewer = null;
	
	this._msgObj = null;
	this.msgId = -1;
	this.msgUid = '';
	this.FolderId = -1;
	this.FolderFullName = '';
	this.Charset = AUTOSELECT_CHARSET;
	this._needPlain = false;
	this._needHeaders = false;
	this._showHeaders = false;
	this._headersCont = null;
	this._headersObj = null;
	this._headersDiv = null;
	
	this._saveButton = null;
	this._printButton = null;
	this._prevButton = null;
	this._nextButton = null;

	this.TrashId = -1;
	this.TrashFullName = '';
	this.Protocol = POP3_PROTOCOL;
	
	this._messagesPerPage = 0;
	this._messagesList = null;
	this.MessageIndex = -1;
	this.NeedMessageIndex  = -1;
}

CViewMessageScreen.prototype = {
	PlaceData: function(Data)
	{
		var Type = Data.Type;
		switch (Type) {
			case TYPE_MESSAGE:
				this.Fill(Data);
			break;
			case TYPE_MESSAGES_LIST:
				this._messagesList = Data;
				if (this.NeedMessageIndex != -1) {
					if (this.NeedMessageIndex == 1) {
						this.NeedMessageIndex = this._messagesList.List.length - 1;
					};
					this.GetMessage(this.NeedMessageIndex);
					this.NeedMessageIndex = -1;
				};
			break;
			case TYPE_MESSAGES_OPERATION:
				SetHistoryHandler(
					{
						ScreenId: WebMail.ListScreenId
					}
				);
			break;
		}
	},
	
	Show: function (settings, historyArgs)
	{
		this.ParseSettings(settings);
		this._mainContainer.className = '';
		if (this._showTextLabels) {
			this._toolBar.ShowTextLabels();
		}
		else {
			this._toolBar.HideTextLabels();
		};
		if (this._allowContacts) {
			this._addToAddressBookImg.className = 'wm_add_address_book_img';
		}
		else {
			this._addToAddressBookImg.className = 'wm_hide';
		};
		this.ResizeBody();
	},
	
	RestoreFromHistory: function (historyArgs)
	{
	},
	
	ParseSettings: function (settings)
	{
		this._messagesPerPage = settings.MsgsPerPage;
		this._showTextLabels = settings.ShowTextLabels;
		this._allowContacts = settings.AllowContacts;
		this._replyNumber = settings.Reply;
		this.ChangeSkin(settings.DefSkin);
	},

	ChangeSkin: function (newSkin)
	{
		if (this._skinName != newSkin) {
			this._skinName = newSkin;
			if (this.isBuilded) {
				this._toolBar.ChangeSkin(newSkin);
				this._addToAddressBookImg.src = 'skins/' + this._skinName + '/contacts/save.gif';
			}
		}
	},

	Hide: function ()
	{
		this._mainContainer.className = 'wm_hide';
		this.HideFullHeaders();
	},
	
	ClickBody: function(ev)
	{
	},

	ResizeBody: function (mode)
	{
		if (this.isBuilded) {
			this.ResizeScreen(mode);
			if (!Browser.IE && mode == 'all') {
				this.ResizeScreen(mode);
			}
		}
	},
	
	ResizeScreen: function (mode)
	{
		var height = GetHeight() - this.GetExternalHeight() - this._rowPadding;
		var isAuto = false;
		if (height < 250 ) {
			height = 250;
			isAuto = true;
		};
		var width = GetWidth();
		if (width < 250 ) {
			width = 250;
			isAuto = true;
		};
		SetBodyAutoOverflow(isAuto);
		this._msgViewer.Resize(width, height);
	},
	
	GetExternalHeight: function()
	{
		var res = 0;
		var x;
		if (x = this._logo.offsetHeight) { res += x; }
		if (x = this._accountsBar.offsetHeight) { res += x; }
		if (x = this._toolBar.table.offsetHeight) { res += x; }
		if (x = this._showPicturesTbl.offsetHeight) { res += x; }
		if (this._isShort) {
			if (x = this._shortHeadersTbl.offsetHeight) { res += x; }
		}
		else {
			if (x = this._completeHeadersTbl.offsetHeight) { res += x; }
		};
		if (x = this._lowToolBar.offsetHeight) { res += x; }
		if (res != 0)
			this.ExternalHeight = res;
		return this.ExternalHeight;
	},
	
	HideFullHeaders: function ()
	{
		this._headersSwitcher.innerHTML = Lang.ShowFullHeaders;
		this._headersCont.className = 'wm_hide';
		this._showHeaders = false;
		this._needHeaders = false;
	},
	
	SetTrashParams: function (id, name, protocol)
	{
		this.TrashId = id;
		this.TrashFullName = name;
		this.Protocol = protocol;
	},
	
	GetNextMessage: function ()
	{
		var isLastMessage = (this.MessageIndex == this._messagesList.List.length - 1);
		if (isLastMessage) {
			this.NeedMessageIndex = 0;
			var xml = '<folder id="' + this._messagesList.FolderId + '"><full_name>' + GetCData(this._messagesList.FolderFullName) + '</full_name></folder>';
			xml += '<look_for fields="0">' + GetCData('') + '</look_for>';
			GetHandler(TYPE_MESSAGES_LIST, { Page: this._messagesList.Page+1, SortField: this._messagesList.SortField,
			 SortOrder: this._messagesList.SortOrder, FolderId: this._messagesList.FolderId,
			 FolderFullName: this._messagesList.FolderFullName, LookFor: '', SearchFields: 0 }, [], xml );
		}
		else {
			this.GetMessage(this.MessageIndex + 1);
		}
	},
	
	GetPrevMessage: function ()
	{
		if (this.MessageIndex == 0) {
			this.NeedMessageIndex = 1;
			var xml = '<folder id="' + this._messagesList.FolderId + '"><full_name>' + GetCData(this._messagesList.FolderFullName) + '</full_name></folder>';
			xml += '<look_for fields="0">' + GetCData('') + '</look_for>';
			GetHandler(TYPE_MESSAGES_LIST, { Page: this._messagesList.Page-1, SortField: this._messagesList.SortField,
			 SortOrder: this._messagesList.SortOrder, FolderId: this._messagesList.FolderId,
			 FolderFullName: this._messagesList.FolderFullName, LookFor: '', SearchFields: 0 }, [], xml );
		}
		else {
			this.GetMessage(this.MessageIndex - 1);
		}
	},
	
	GetMessage: function (index)
	{
		var msg = this._messagesList.List[index];
		SetHistoryHandler(
			{
				ScreenId: SCREEN_VIEW_MESSAGE,
				MsgId: msg.Id,
				MsgUid: msg.Uid,
				MsgFolderId: msg.FolderId,
				MsgFolderFullName: msg.FolderFullName,
				MsgCharset: msg.Charset,
				MsgParts: [PART_MESSAGE_HEADERS, PART_MESSAGE_HTML, PART_MESSAGE_ATTACHMENTS]
			}
		);
	},

	ShowPictures: function ()
	{
		var msg = this._msgObj;
		if (msg.Safety != 1) {
			msg.ShowPictures();
			this._msgViewer.Fill(msg);
			this.ResizeScreen();
		}
	},
	
	Fill: function (msg)
	{
		this._msgObj = msg;
		this.MessageIndex = this._messagesList.GetMessageIndex(msg);
		if (null == this._messagesList || this.MessageIndex == -1) {
			this._prevButton.MakeInActive('wm_toolbar_item_inactive', 'message_up_inactive.gif');
			this._nextButton.MakeInActive('wm_toolbar_item_inactive', 'message_down_inactive.gif');
		}
		else {
			var obj = this;
			if (this.MessageIndex == 0 && this._messagesList.Page == 1) {
				this._prevButton.MakeInActive('wm_toolbar_item_inactive', 'message_up_inactive.gif');
			}
			else {
				this._prevButton.MakeActive('wm_toolbar_item', 'wm_toolbar_item_over', 'message_up.gif', function () { obj.GetPrevMessage(); });
			};
			var isLastMessage = (this.MessageIndex == this._messagesList.List.length - 1);
			var isLastPage = (this._messagesPerPage*this._messagesList.Page >= this._messagesList.MessagesCount);
			if (isLastMessage && isLastPage) {
				this._nextButton.MakeInActive('wm_toolbar_item_inactive', 'message_down_inactive.gif');
			}
			else {
				this._nextButton.MakeActive('wm_toolbar_item', 'wm_toolbar_item_over', 'message_down.gif', function () { obj.GetNextMessage(); });
			}
		};
		if (this._needHeaders) {
			this._needHeaders = false;
			this._showHeaders = true;
			this._headersSwitcher.innerHTML = Lang.HideFullHeaders;
			var height = GetHeight();
			var width = GetWidth();
			var win_height = height*3/5;
			var win_width = width*3/5;
			this._headersCont.style.width = win_width + 'px';
			this._headersCont.style.height = win_height + 'px';
			this._headersCont.style.top = (height - win_height)/2 + 'px';
			this._headersCont.style.left = (width - win_width)/2 + 'px';
			this._headersDiv.style.width = win_width - 10 + 'px';
			this._headersDiv.style.height = win_height - 30 + 'px';
			this._headersCont.className = 'wm_headers';
			if (Browser.IE) {
				this._headersObj.innerText = msg.FullHeaders;
			}
			else {
				this._headersObj.textContent = msg.FullHeaders;
			}
		}
		else {
			this.HideFullHeaders();
		};
		this.msgId = msg.Id;
		this.msgUid = msg.Uid;
		this.FolderId = msg.FolderId;
		this.FolderFullName = msg.FolderFullName;
		this.Charset = msg.Charset;
		if (msg.Importance == 1)
			this._importanceImg.className = 'wm_importance_img';
		else
			this._importanceImg.className = 'wm_hide';
		this._cFromObj.innerHTML = msg.FromAddr;
		//email parts for adding to contacts
		var fromParts = GetEmailParts(HtmlDecode(msg.FromAddr));
		this._fromName = fromParts.Name;
		this._fromEmail = fromParts.Email;

		this._picturesControl.SetSafety(msg.Safety);
		switch (msg.Safety) {
			case (0):
				this._picturesControl.Show();
				this._picturesControl.SetFromAddr(this._fromEmail);
			break;
			case (1):
				this._picturesControl.Hide();
			break;
			case (2):
				this._picturesControl.Show();
			break;
		};

		this._cToObj.innerHTML = msg.ToAddr;
		this._cDateObj.innerHTML = msg.Date;
		if (msg.CCAddr.length != 0) {
			this._CCObj.innerHTML = msg.CCAddr;
			this._CCCont.className = '';
		}
		else {
			this._CCCont.className = 'wm_hide';
		};
		if (msg.BCCAddr.length != 0) {
			this._BCCObj.innerHTML = msg.BCCAddr;
			this._BCCCont.className = '';
		}
		else {
			this._BCCCont.className = 'wm_hide';
		};
		if (msg.ReplyToAddr.length != 0 && msg.ReplyToAddr != msg.FromAddr) {
			this._replyToObj.innerHTML = msg.ReplyToAddr;
			this._replyToCont.className = '';
		}
		else {
			this._replyToCont.className = 'wm_hide';
		};
		this._cSubjectObj.innerHTML = msg.Subject;
		if (msg.HasCharset && msg.Charset == -1) {
			this._charsetCont.className = 'wm_hide';
			this._showHeadersShower.className = 'wm_control_img';
		}
		else {
			this._charsetCont.className = '';
			this._showHeadersShower.className = 'wm_hide';
			this._charsetObj.value = msg.Charset;
			this._charsetObj.blur();
		};

		if (HtmlDecode(msg.FromAddr).length <= 20)
			this._sFromObj.innerHTML = '&nbsp;' + msg.FromAddr + '&nbsp;';
		else
			this._sFromObj.innerHTML = '&nbsp;' + HtmlEncode(HtmlDecode(msg.FromAddr).substr(0, 20)) + '...&nbsp;';
		if (HtmlDecode(msg.ToAddr).length <= 20)
			this._sToObj.innerHTML = '&nbsp;' + msg.ToAddr + '&nbsp;';
		else
			this._sToObj.innerHTML = '&nbsp;' + HtmlEncode(HtmlDecode(msg.ToAddr).substr(0, 20)) + '...&nbsp;';
		this._sDateObj.innerHTML = '&nbsp;' + msg.Date + '&nbsp;';
		if (HtmlDecode(msg.Subject).length <= 20)
			this._sSubjectObj.innerHTML = '&nbsp;' + msg.Subject + '&nbsp;';
		else
			this._sSubjectObj.innerHTML = '&nbsp;' + HtmlEncode(HtmlDecode(msg.Subject).substr(0, 20)) + '...&nbsp;';
		
		this._msgViewer.Fill(msg);
		if (msg.SaveLink == '#') {
			this._saveButton.onclick = function () {};
		}
		else {
			this._saveButton.onclick = this.CreateSaveLinkFunc(msg.SaveLink);
		}
		this._printButton.onclick = this.CreatePrintLinkFunc(msg);
		
		this.ResizeBody();
	},
	
	CreateSaveLinkFunc: function (link)
	{
		return function () { document.location = link; }
	},
	
	CreatePrintLinkFunc: function (msg)
	{
		var obj = this;
		return function () {
			var allHeight = GetHeight();
			var allWidth = GetWidth();
			var height = 480; if (height >= allHeight) height = Math.ceil(allHeight*2/3);
			var width = 640; if (width >= allWidth) width = Math.ceil(allWidth*2/3);
			var top = Math.ceil((allHeight - height)/2);
			var left = Math.ceil((allWidth - width)/2);
			var win = window.open('', 'Popup', 'toolbar=yes,status=no,scrollbars=yes,resizable=yes,width=' + width + ',height=' + height + ',top=' + top + ',left=' + left);
			if (Browser.Mozilla) { win.document.open(); };
			win.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />');
			win.document.write('<html><head><link rel="stylesheet" href="./skins/' + obj._skinName + '/styles.css" type="text/css" /></head>');
			win.document.write('<body class="wm_body"><div align="center" class="wm_space_before"><table class="wm_print">');
			win.document.write('<tr><td class="wm_print_content" style="border-width: 0px 1px 1px 0px" width="60px">' + Lang.From + ': </td><td class="wm_print_content" style="border-width: 0px 0px 1px 1px">' + msg.FromAddr + '</td></tr>');
			win.document.write('<tr><td class="wm_print_content" style="border-width: 0px 1px 1px 0px" width="60px">' + Lang.To + ': </td><td class="wm_print_content" style="border-width: 0px 0px 1px 1px">' + msg.ToAddr + '</td></tr>');
			win.document.write('<tr><td class="wm_print_content" style="border-width: 0px 1px 1px 0px" width="60px">' + Lang.Date + ': </td><td class="wm_print_content" style="border-width: 0px 0px 1px 1px">' + msg.Date + '</td></tr>');
			win.document.write('<tr><td class="wm_print_content" style="border-width: 0px 1px 1px 0px" width="60px">' + Lang.Subject + ': </td><td class="wm_print_content" style="border-width: 0px 0px 1px 1px">' + msg.Subject + '</td></tr>');
			if (msg.HasHtml) {
				var messageBody = msg.HtmlBody;
			}
			else {
				var messageBody = msg.PlainBody;
			};
			win.document.write('<tr><td colspan="2" class="wm_print_content" style="border-width: 1px 0px 0px 0px"><div class="wm_space_before">' + messageBody + '</div></td></tr></table></div></body></html>');
			if (Browser.Mozilla) { win.document.close(); };
		}
	},
	
	FillCharset: function (charset)
	{
		var sel = this._charsetObj;
		CleanNode(sel);
		var opt;
		var obj = this;
		for (var i in Charsets) {
			if (Charsets[i].Value == 0) {
				var value = AUTOSELECT_CHARSET;
			}
			else {
				var value = Charsets[i].Value;
			};
			opt = CreateChildWithAttrs(sel, 'option', [['value', value]]);
			opt.innerHTML = Charsets[i].Name;
			if (charset == value) {
				opt.selected = true;
			}
			else {
				opt.selected = false;
			}
		};
		sel.onchange = function () {
			SetHistoryHandler(
				{
						ScreenId: SCREEN_VIEW_MESSAGE,
						MsgId: obj.msgId,
						MsgUid: obj.msgUid,
						MsgFolderId: obj.FolderId,
						MsgFolderFullName: obj.FolderFullName,
						MsgCharset: this.value,
						MsgParts: [PART_MESSAGE_HEADERS, PART_MESSAGE_HTML, PART_MESSAGE_ATTACHMENTS]
				}
			);
		}
	},

	ShowCompleteHeaders: function ()
	{
		this._completeHeadersTbl.className = 'wm_view_message';
		this._shortHeadersTbl.className = 'wm_hide';
		this._isShort = false;
		this.ResizeBody();
	},
	
	ShowShortHeaders: function ()
	{
		this._completeHeadersTbl.className = 'wm_hide';
		this._shortHeadersTbl.className = 'wm_view_message';
		this._isShort = true;
		this.ResizeBody();
	},

	Build: function (container, accountsBar, PopupMenus, settings)
	{
		var obj = this;
		this.ParseSettings(settings);

		this._logo = document.getElementById('logo');
		this._accountsBar = accountsBar;

		this._mainContainer = CreateChild(container, 'div');
		this._mainContainer.className = 'wm_hide';

		this.BuildToolBar(PopupMenus);

		this._showPicturesTbl = this._picturesControl.Build(this._mainContainer);

		var tbl = CreateChild(this._mainContainer, 'table');
		this._completeHeadersTbl = tbl;
		tbl.className = 'wm_view_message';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.className = 'wm_view_message_title';
		td.innerHTML = Lang.From + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'From', ':');
		td = tr.insertCell(1);
		var span = CreateChild(td, 'span');
		this._cFromObj = span;
		var img = CreateChildWithAttrs(td, 'img', [['class', 'wm_add_address_book_img'], ['src', 'skins/' + this._skinName + '/contacts/save.gif'], ['title', Lang.AddToAddressBokk]]);
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
		};
		this._addToAddressBookImg = img;

		td = tr.insertCell(2);
		td.className = 'wm_headers_switcher';
		var nobr = CreateChild(td, 'nobr');
		var a = CreateChildWithAttrs(nobr, 'a', [['href', '#']]);
		a.onclick = function () {
			if (obj._showHeaders) {
				obj.HideFullHeaders();
			}
			else {
				obj._needHeaders = true;
				GetMessageHandler(obj.msgId, obj.msgUid, obj.FolderId, obj.FolderFullName, [PART_MESSAGE_FULL_HEADERS], obj.Charset);
			}
			return false;
		};
		a.innerHTML = Lang.ShowFullHeaders;
		this._headersSwitcher = a;
		
		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_view_message_title';
		td.innerHTML = Lang.To + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'To', ':');
		td = tr.insertCell(1);
		td.colSpan = 2;
		this._cToObj = td;

		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_view_message_title';
		td.innerHTML = Lang.Date + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Date', ':');
		td = tr.insertCell(1);
		td.colSpan = 2;
		this._cDateObj = td;

		tr = tbl.insertRow(3);
		tr.className = 'wm_hide';
		td = tr.insertCell(0);
		td.className = 'wm_view_message_title';
		td.innerHTML = Lang.CC + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'CC', ':');
		td = tr.insertCell(1);
		td.colSpan = 2;
		this._CCObj = td;
		this._CCCont = tr;

		tr = tbl.insertRow(4);
		tr.className = 'wm_hide';
		td = tr.insertCell(0);
		td.className = 'wm_view_message_title';
		td.innerHTML = Lang.BCC + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'BCC', ':');
		td = tr.insertCell(1);
		td.colSpan = 2;
		this._BCCObj = td;
		this._BCCCont = tr;

		tr = tbl.insertRow(5);
		tr.className = 'wm_hide';
		td = tr.insertCell(0);
		td.className = 'wm_view_message_title';
		td.innerHTML = Lang.ReplyTo + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'ReplyTo', ':');
		td = tr.insertCell(1);
		td.colSpan = 2;
		this._replyToObj = td;
		this._replyToCont = tr;

		tr = tbl.insertRow(6);
		td = tr.insertCell(0);
		td.className = 'wm_view_message_title';
		td.innerHTML = Lang.Subject + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Subject', ':');
		td = tr.insertCell(1);
		img = CreateChildWithAttrs(td, 'img', [['class', 'wm_importance_img'], ['src', 'skins/' + this._skinName + '/menu/priority_high.gif']]);
		this._importanceImg = img;
		span = CreateChild(td, 'span');
		this._cSubjectObj = span;
		td = tr.insertCell(2);
		td.className = 'wm_view_message_switcher';
		img = CreateChildWithAttrs(td, 'img', [['class', 'wm_control_img'], ['src', 'skins/' + this._skinName + '/menu/arrow_up.gif']]);
		img.onclick = function () { obj.ShowShortHeaders(); };
		this._showHeadersShower = img;

		tr = tbl.insertRow(7);
		td = tr.insertCell(0);
		td.className = 'wm_view_message_title';
		td.innerHTML = Lang.Charset + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Charset', ':');
		td = tr.insertCell(1);
		var sel = CreateChild(td, 'select');
		sel.className = 'wm_view_message_select';
		this._charsetObj = sel;
		this.FillCharset(AUTOSELECT_CHARSET);
		td = tr.insertCell(2);
		td.className = 'wm_view_message_switcher';
		img = CreateChildWithAttrs(td, 'img', [['class', 'wm_control_img'], ['src', 'skins/' + this._skinName + '/menu/arrow_up.gif']]);
		img.onclick = function () { obj.ShowShortHeaders(); };
		this._charsetCont = tr;
		
		tbl = CreateChild(this._mainContainer, 'table');
		this._shortHeadersTbl = tbl;
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.colSpan = 2;
		var font = CreateChild(td, 'font');
		font.innerHTML = Lang.From + ':';
		WebMail.LangChanger.Register('innerHTML', font, 'From', ':');
		span = CreateChild(td, 'span');
		this._sFromObj = span;
		font = CreateChild(td, 'font');
		font.innerHTML = Lang.To + ':';
		WebMail.LangChanger.Register('innerHTML', font, 'To', ':');
		span = CreateChild(td, 'span');
		this._sToObj = span;
		font = CreateChild(td, 'font');
		font.innerHTML = Lang.Date + ':';
		WebMail.LangChanger.Register('innerHTML', font, 'Date', ':');
		span = CreateChild(td, 'span');
		this._sDateObj = span;
		font = CreateChild(td, 'font');
		font.innerHTML = Lang.Subject + ':';
		WebMail.LangChanger.Register('innerHTML', font, 'Subject', ':');
		span = CreateChild(td, 'span');
		this._sSubjectObj = span;
		td = tr.insertCell(1);
		td.className = 'wm_view_message_switcher';
		img = CreateChildWithAttrs(td, 'img', [['class', 'wm_control_img'], ['src', 'skins/' + this._skinName + '/menu/arrow_down.gif']]);
		img.onclick = function () { obj.ShowCompleteHeaders(); };

		tbl = CreateChild(this._mainContainer, 'table');
		tbl.className = 'wm_message_viewer';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_message_viewer_cell';
		this._msgViewer = new CMessageViewer();
		this._msgViewer.Build(td, 15);

		tr = tbl.insertRow(1);
		this._lowToolBar = tr;
		tr.className = 'wm_hide';
		td = tr.insertCell(0);
		td.colSpan = 2;
		span = CreateChild(td, 'span');
		span.className = 'wm_lowtoolbar_plain_html';
		a = CreateChildWithAttrs(span, 'a', [['href', '#']]);
		a.innerHTML = Lang.SwitchToPlain;
		a.onclick = function () {
			var part = obj._msgViewer.GetMsgPart();
			GetMessageHandler(obj.msgId, obj.msgUid, obj.FolderId, obj.FolderFullName, [part], obj.Charset);
			return false;
		};

		this._msgViewer.SetSwitcher(tr, 'wm_lowtoolbar', a);
		
		div = CreateChild(document.body, 'div');
		this._headersCont = div;
		div.className = 'wm_hide';
		var div1 = CreateChild(div, 'div');
		this._headersDiv = div1;
		div1.className = 'wm_message_rfc822';
		var pre = CreateChild(div1, 'pre');
		this._headersObj = pre;
		div1 = CreateChild(div, 'div');
		div1.className = 'wm_hide_headers';
		a = CreateChildWithAttrs(div1, 'a', [['href', '#']]);
		a.onclick = function () {
			obj.HideFullHeaders();
			return false;
		};
		a.innerHTML = Lang.Close;
		WebMail.LangChanger.Register('innerHTML', a, 'Close', '');
		
		this.isBuilded = true;
	},

	BuildToolBar: function (PopupMenus)
	{
		var obj = this;
		var toolBar = new CToolBar(this._mainContainer, this._skinName);
		var item = toolBar.AddItem(TOOLBAR_BACK_TO_LIST, function () {
			SetHistoryHandler(
				{
					ScreenId: WebMail.ListScreenId,
					FolderId: null
				}
			);
		});
		item = toolBar.AddItem(TOOLBAR_NEW_MESSAGE, function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_NEW_MESSAGE
				}
			);
		});
		toolBar.AddReplyItem(this._replyNumber, PopupMenus);
		item = toolBar.AddItem(TOOLBAR_FORWARD, CreateReplyClick(FORWARD));
		this._printButton = toolBar.AddItem(TOOLBAR_PRINT_MESSAGE, function () {});
		this._saveButton = toolBar.AddItem(TOOLBAR_SAVE_MESSAGE, function () {});
		item = toolBar.AddItem(TOOLBAR_DELETE, function () {
			if (confirm(Lang.ConfirmAreYouSure)) {
				var operation = new COperationMessages();
				operation.FolderId = obj.FolderId;
				operation.FolderFullName = obj.FolderFullName;
				operation.Messages.setVal(obj.FolderId + obj.FolderFullName, {IdArray: [{Id: obj.msgId, Uid: obj.msgUid}], 
				FolderId: obj.FolderId, FolderFullName: obj.FolderFullName});
				var xml = operation.GetInXML();
				RequestHandler('operation_messages', OperationTypes[DELETE], xml);
			}
		});
		this._prevButton = toolBar.AddNextPrevItem(TOOLBAR_PREV_MESSAGE);
		this._nextButton = toolBar.AddNextPrevItem(TOOLBAR_NEXT_MESSAGE);
		this._toolBar = toolBar;
	}
};

function CMessageViewer() {
	this._mainContainer = null;
	this._attachCont = null;
	this._resizerCont = null;
	this._hasAttachments = false;
	this._msgCont = null;
	this._msgObj = null;
	this._resizerObj = null;
	this._attachWidth = 140;
	this._minAttachWidth = 10;
	this._minMessWidth = 40;
	this._resizerWidth = 2;
	this._msgPadding = 16;
	this._colPadding = 0;

	this._switcherCont = null;
	this._switcherClass = '';
	this._switcherObj = null;
	this._htmlMode = false;
	this._needPlain = false;
}

CMessageViewer.prototype = {
	GetMsgPart: function ()
	{
		if (this._htmlMode) {
			this._needPlain = true;
			return PART_MESSAGE_MODIFIED_PLAIN_TEXT;
		}
		else {
			this._needPlain = false;
			return PART_MESSAGE_HTML;
		}
	},
	
	Resize: function (width, height)
	{
		this.ResizeWidth(width);
		this.ResizeHeight(height)
	},
	
	ResizeWidth: function (width)
	{
		if (this._resizerObj != null) atWidth = this._resizerObj._leftShear;
		else atWidth = this._attachWidth;

		var maxAttachWidth = width - this._minMessWidth - this._resizerWidth - this._colPadding;
		if (atWidth > maxAttachWidth) atWidth = maxAttachWidth;
		
		this._mainContainer.style.width = width - this._colPadding + 'px';

		if (this._hasAttachments) {
			this._attachCont.style.left = '0px';
			this._attachCont.style.width = atWidth + 'px';
			this._resizerCont.style.left = atWidth + 'px';
			this._resizerCont.style.width = this._resizerWidth + 'px';
			this._msgCont.style.left = (atWidth + 2) + 'px';
			this._msgCont.style.width = (width - this._attachCont.offsetWidth - this._colPadding - this._msgPadding) + 'px';
		}
		else {
			this._msgCont.style.left = '0px';
			this._msgCont.style.width = (width - this._colPadding - this._msgPadding) + 'px';
		};
		var clientWidth = this._msgCont.clientWidth;
		if (clientWidth > 18) this._msgObj.style.width = (clientWidth - 18) + 'px';
	},
	
	ResizeHeight: function (height)
	{
		this._mainContainer.style.height = height + 'px';
		this._attachCont.style.height = height + 'px';
		this._resizerCont.style.height = height + 'px';
		this._msgCont.style.height = (height - this._msgPadding) + 'px';
	},
	
	Fill: function (msg)
	{
		CleanNode(this._attachCont);
		if (msg.Attachments.length == 0) {
			this._hasAttachments = false;
			this._attachCont.className = 'wm_hide';
			this._resizerCont.className = 'wm_hide';
		}
		else {
			this._hasAttachments = true;
			this._attachCont.className = 'wm_message_attachments';
			this._resizerCont.className = 'wm_vresizer_mess';
			for (var i in msg.Attachments) {
				var div = CreateChildWithAttrs(this._attachCont, 'div', [['style', 'float: left;']]);
				var fileName = msg.Attachments[i].FileName;
				var size = GetFriendlySize(msg.Attachments[i].Size);
				var params = GetFileParams(fileName);
				var imageName = params.image;
				var title = Lang.ClickToDownload + ' ' + fileName + ' (' + size + ')';
				if (fileName.length > 16) {
					fileName = fileName.substring(0, 15) + '&#8230;';
				};
				var a = CreateChildWithAttrs(div, 'a', [['href', msg.Attachments[i].Download], ['class', 'wm_attach_download_a']]);
				a.onfocus = function () { this.blur() };
				a.innerHTML = '<img src="images/icons/' + imageName + '" title="' + title + '" /><br /><span title="' + title + '">' + fileName + '</span>';
				if (params.view && msg.Attachments[i].View != '#') {
					br = CreateChild(div, 'br');
					var a = CreateChildWithAttrs(div, 'a', [['href', ''], ['class', 'wm_attach_view_a']]);
					a.innerHTML = Lang.View;
					a.onclick = CreateAttachViewClick(msg.Attachments[i].View);
				}
			};
			var div = CreateChildWithAttrs(this._attachCont, 'div', [['style', 'clear: left; height: 1px;']]);
		};
		
		CleanNode(this._msgObj);
		if (msg.HasHtml) {
			if (msg.HasPlain) {
				this._switcherCont.className = this._switcherClass;
				if (this._needPlain) {
					this._msgObj.innerHTML = msg.PlainBody;
					this._switcherObj.innerHTML = Lang.SwitchToHTML;
					this._htmlMode = false;
					this._needPlain = false;
				}
				else {
					this._msgObj.innerHTML = msg.HtmlBody;
					this._switcherObj.innerHTML = Lang.SwitchToPlain;
					this._htmlMode = true;
				};
			}
			else {
				this._msgObj.innerHTML = msg.HtmlBody;
				this._switcherCont.className = 'wm_hide';
				this._htmlMode = true;
			}
		}
		else {
			if (msg.HasPlain) {
				this._msgObj.innerHTML = msg.PlainBody;//tag pre was removed because server modifications
			}
			else {
				this._msgObj.innerHTML = '';
			};
			this._switcherCont.className = 'wm_hide';
			this._htmlMode = false;
		};
		return this._htmlMode;
	},
	
	Clean: function (strValue)
	{
		this._attachCont.innerHTML = '';
		this._attachCont.className = 'wm_hide';
		this._resizerCont.className = 'wm_hide';
		this._hasAttachments = false;
		if (typeof(strValue) != 'string') {
			strValue = '';
		};
		CleanNode(this._msgObj);
		this._msgObj.innerHTML = strValue;
		this._switcherCont.className = 'wm_hide';
		this._htmlMode = false;
		this._needPlain = false;
	},
	
	SetSwitcher: function (sCont, sClass, sObj)
	{
		this._switcherCont = sCont;
		this._switcherClass = sClass;
		this._switcherObj = sObj;
	},
	
	Build: function (container, colP)
	{
		this._colPadding = colP;
		
		var div = CreateChild(container, 'div');
		this._mainContainer = div;
		div.style.position = 'relative';

		var atDiv = CreateChild(div, 'div');
		this._attachCont = atDiv;
		atDiv.style.position = 'absolute';
		atDiv.style.top = '0px';
		atDiv.className = 'wm_hide';

		var resDiv = CreateChild(div, 'div');
		this._resizerCont = resDiv;
		resDiv.style.position = 'absolute';
		resDiv.style.top = '0px';
		resDiv.className = 'wm_hide';
		this._hasAttachments = false;

		var mesDiv = CreateChild(div, 'div');
		this._msgCont = mesDiv;
		mesDiv.style.position = 'absolute';
		mesDiv.style.top = '0px';
		mesDiv.className = 'wm_message';
		this._msgObj = CreateChild(mesDiv, 'div');

		this._resizerObj = new CVerticalResizer(resDiv, div, this._resizerWidth, this._minAttachWidth, this._minMessWidth, this._attachWidth, "WebMail.ResizeBody('all');", 1);
	}
};
