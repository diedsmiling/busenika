/*
 * Classes:
 *  CNewMessageScreen
 */

function CNewMessageScreen(skinName)
{
	this.Id = SCREEN_NEW_MESSAGE;
	this.isBuilded = false;
	this.hasCopyright = true;
	this.BodyAutoOverflow = true;
	this._skinName = skinName;
	
	this.shown = false;
	this._idAcct = -1;
	
	this._msgObj = new CMessage();
	this._newMessage = true;
	this._fromField = '';
	this._sendersGroups = Array();

	this._showTextLabels = true;
	this._allowDhtmlEditor = true;
	this._htmlEditorField = null;

	this._mainContainer = null;
	this._toolBar = null;
	this._logo = null;
	this._accountsBar = null;
	this._headersTbl = null;
	this._uploadTbl = null;
	//logo + accountslist + toolbar
	this.ExternalHeight = 56 + 32 + 26 + 24;

	this._bccSwitcher = null;
	this._hasBcc = false;
	this._bccCont = null;

	this._fromObj = null;
	this._toObj = null;
	this._ccObj = null;
	this._bccObj = null;
	this._subjectObj = null;
	this._priorityButton = null;
	this._priority = 3;

	this.AutoFilling = null;
	this.PopupContacts = null;
	
	this._modeSwitcher = null;
	this._modeSwitcherCont = null;
	this._mode = true;
	this._plainEditorObj = null;
	this._plainEditorDiv = null;
	this._plainEditorCont = null;
	
	this._uploadForm = null;
	this._attachments = Array();
	this._rowIndex = 0;
	this._attachmentsTbl = null;
	
	this._signature = null;

	this._picturesControl = new CMessagePicturesController(this.ShowPictures, this);
	this._showPicturesTbl = null;
	
	this._saving = false;
	this._sending = false;
}

CNewMessageScreen.prototype = {
	PlaceData: function (Data)
	{
		if (Data != null) {
			var Type = Data.Type;
			switch (Type){
				case TYPE_SIGNATURE:
					this._signature = Data;
					if (this._newMessage) {
						this.ChangeSignature();
						this.Fill();
					}
				break;
				case TYPE_CONTACTS:
					var iCount = Data.List.length;
					for (var i=0; i<iCount; i++) {
						if (Data.List[i].IsGroup) {
							Data.List[i].ImgSrc = 'skins/' + this._skinName + '/contacts/group.gif';
						}
						else {
							Data.List[i].ImgSrc = '';
						}
					};
					if (Data.LookFor == '') {
					    if (iCount == 0) {
					        this.PopupContacts.Hide();
						    WebMail.ShowReport(Lang.InfoNoContactsGroups);
					    }
					    else {
						    this.PopupContacts.Fill(Data.List, 'skins/' + this._skinName + '/contacts/close.gif');
					    }
					}
					else {
					    if (iCount == 0) {
					        this.AutoFilling.Hide();
					    }
					    else if (Data.Count > iCount) {
						    this.AutoFilling.Fill(Data.List, Data.LookFor, Lang.InfoListNotContainAddress);
					    }
					    else {
						    this.AutoFilling.Fill(Data.List, Data.LookFor, '');
					    }
					}
				break;
			}
		}
	},//PlaceData
	
	AddSenderGroup: function (id)
	{
		var hasValue = false;
		var iCount = this._sendersGroups.length;
		for (var i=0; i<iCount; i++) {
			if (this._sendersGroups[i] == id) {
				hasValue = true;
			}
		};
		if (!hasValue) {
			this._sendersGroups[iCount] = id;
		}
	},
	
	SetErrorHappen: function ()
	{
		this._saving = false;
		this._sending = false;
	},
	
	SetMessageId: function (id, uid)
	{
		this._saving = false;
		this._msgObj.Id = id;
		this._msgObj.Uid = uid;
	},
	
	Show: function (settings)
	{
		this._saving = false;
		this._sending = false;
		this._sendersGroups = Array();
		this.ParseSettings(settings);
		this._mainContainer.className = '';
		if (this._showTextLabels) {
			this._toolBar.ShowTextLabels();
		}
		else {
			this._toolBar.HideTextLabels();
		}
		if (this._allowDhtmlEditor) {
			this._htmlEditorField.Show(7);
			this._htmlEditorField.SetPlainEditor(this._plainEditorObj, this._modeSwitcher);
			this._htmlEditorField.Replace();
		}
		this.shown = true;
		this.Fill();
		this.ResizeBody();
		if (this._allowDhtmlEditor) {
			this._modeSwitcherCont.className = '';
		}
		else {
			this._modeSwitcherCont.className = 'wm_hide';
		}
	},//Show
	
	ParseSettings: function (settings)
	{
		if (this._allowDhtmlEditor != settings.AllowDhtmlEditor) {
			this._allowDhtmlEditor = settings.AllowDhtmlEditor;
			if (null == this._htmlEditorField) {
				this._allowDhtmlEditor = false;
			}
		}
		this._showTextLabels = settings.ShowTextLabels;
		this.ChangeSkin(settings.DefSkin);
	},//ParseSettings
	
	ChangeSkin: function (newSkin)
	{
		if (this._skinName != newSkin) {
			this._skinName = newSkin;
			if (this.isBuilded) {
				this._toolBar.ChangeSkin(newSkin);
			}
		}
	},

	RestoreFromHistory: function (historyArgs)
	{
	},

	Hide: function ()
	{
		this.shown = false;
		this.SetNewMessage();
		if (this._allowDhtmlEditor) {
			this._htmlEditorField.Hide();
		}
		this.PopupContacts.Hide();
		this.AutoFilling.Hide();
		this._mainContainer.className = 'wm_hide';
	},
	
	ClickBody: function (ev)
	{
		if (this._allowDhtmlEditor && this._mode) {
			this._htmlEditorField.ClickBody();
		}
		this.PopupContacts.ClickBody(ev);
		this.AutoFilling.ClickBody(ev);
	},

	GetExternalHeight: function()
	{
		var res = 0;
		var x;
		if (x = this._logo.offsetHeight) { res += x; }
		if (x = this._accountsBar.offsetHeight) { res += x; }
		if (x = this._toolBar.table.offsetHeight) { res += x; }
		if (x = this._headersTbl.offsetHeight) { res += x; }
		if (x = this._plainEditorCont.offsetHeight) { res -= x; }
		if (x = this._attachmentsTbl.offsetHeight) { res += x; }
		if (x = this._uploadTbl.offsetHeight) { res += x; }
		if (res != 0)
			this.ExternalHeight = res;
		return this.ExternalHeight;
	},
	
	ResizeBody: function (mode)
	{
		if (this.isBuilded) {
			var width = GetWidth();
			if (width < 684)
				width = 684;
			width = width - 40;
			var height = GetHeight() - this.GetExternalHeight() - 40;
			if (height < 200) height = 200;

			this._plainEditorDiv.style.height = height + 'px';
			this._plainEditorDiv.style.width = width + 'px';

			if (null != this._htmlEditorField) {
				this._htmlEditorField.Resize(width, height);
			}
			else {
				this._plainEditorObj.style.height = (height - 1) + 'px';
				this._plainEditorObj.style.width = (width - 2) + 'px';
			}
			this.PopupContacts.Replace();
			this.AutoFilling.Replace();
		}
	},
	
	SetNewMessage: function ()
	{
		this._newMessage = true;
		this._msgObj = new CMessage();
		this.ChangeFromAddr();
		this.ChangeSignature();
	},
	
	ChangeFromAddr: function ()
	{
		this._msgObj.FromAddr = this._fromField;
	},
	
	ChangeSignature: function ()
	{
		var value = '';
		if (null != this._signature && this._signature.Opt != 0) {
			value = this._signature.Value;
		}
		var prefix = '';
		if (value.length > 0) {
			if (this._allowDhtmlEditor && this._msgObj.HasHtml) {
				prefix = '<br/><br/>';
			}
			else {
				prefix = '\n\n';
			}
		}
		else if (Browser.Mozilla && this._allowDhtmlEditor && this._msgObj.HasHtml) {
			prefix = '<br/>';
		}
		if (this._allowDhtmlEditor && this._msgObj.HasHtml) {
			this._msgObj.HtmlBody = prefix + value;
		}
		else {
			if (null != this._signature && this._signature.isHtml) {
				this._msgObj.PlainBody = prefix + value.replace(/<br *\/{0,1}>/gi, '\n').replace(/<[^>]*>/g, '');
			}
			else {
				this._msgObj.PlainBody = prefix + value;
			}
			this._msgObj.HasHtml = false;
			this._msgObj.HasPlain = true;
		}
	},//ChangeSignature
	
	SetFromField: function (value)
	{
		this._fromField = value;
		this.ChangeFromAddr();
		this.Fill();
	},
	
	SetIdAccount: function (idAcct)
	{
		if (this._idAcct != idAcct) {
			this._idAcct = idAcct;
			GetHandler(TYPE_SIGNATURE, { IdAcct: this._idAcct }, [], '');
		}
	},
	
	UpdateMessageForReply: function (message, replyAction)
	{
		this._newMessage = false;
		Screens[SCREEN_NEW_MESSAGE].ShowHandler = '';
		this._msgObj = new CMessage();
		this._msgObj.PrepareForReply(message, replyAction, this._fromField);
		this._msgObj.FromAddr = this._fromField;
		this.Fill();
	},
	
	UpdateMessageFromContacts: function (toField)
	{
		Screens[SCREEN_NEW_MESSAGE].ShowHandler = '';
		this.SetNewMessage();
		this._msgObj.ToAddr = toField;
		this.Fill();
	},

	UpdateMessage: function (message)
	{
		this._newMessage = false;
		Screens[SCREEN_NEW_MESSAGE].ShowHandler = '';
		this._msgObj = new CMessage();
		this._msgObj.PrepareForEditing(message);
		this.Fill();
	},

	ShowPictures: function ()
	{
		if (this._msgObj.Safety == 0)
		{
			this._msgObj.ShowPictures();
			if (this._msgObj.HasHtml) {
				this._htmlEditorField.SetHtml(this._msgObj.HtmlBody);
			}
			else {
				this._htmlEditorField.SetText(this._msgObj.PlainBody);
			}
			this.ResizeBody();
		}
	},

	Fill: function ()
	{
		if ((null != this._msgObj) && this.shown) {
			var msg = this._msgObj;
			this.SetPriority(msg.Importance);
			
			if (msg.Safety != 0 || !this._allowDhtmlEditor) {
				this._picturesControl.Hide();
			}
			else {
				this._picturesControl.Show();
			};

			this._fromObj.value = msg.FromAddr;
			this._toObj.value = msg.ToAddr;
			this._ccObj.value = msg.CCAddr;
			this._bccObj.value = msg.BCCAddr;
			if (msg.BCCAddr.length == 0) {
				this._bccSwitcher.innerHTML = Lang.ShowBCC;
				this._hasBcc = false;
				this._bccCont.className = 'wm_hide';
			}
			else {
				this._bccSwitcher.innerHTML = Lang.HideBCC;
				this._hasBcc = true;
				this._bccCont.className = '';
			};
			this._subjectObj.value = msg.Subject;

			if (this._allowDhtmlEditor) {
				if (msg.HasHtml) {
					this._htmlEditorField.SetHtml(HtmlDecodeBody(msg.HtmlBody));
					this._htmlEditorField.Show(7);
					this._plainEditorObj.tabIndex = -1;
				}
				else {
					this._htmlEditorField.SetText(HtmlDecodeBody(msg.PlainBody));
					this._plainEditorObj.tabIndex = 6;
				}
			}
			else {
				if (msg.HasPlain) {
					this._plainEditorObj.value = HtmlDecodeBody(msg.PlainBody);
				}
				else {
					this._plainEditorObj.value = HtmlDecode(HtmlDecodeBody(msg.HtmlBody).replace(/<br *\/{0,1}>/gi, '\n').replace(/<[^>]*>/g, ''));
				};
				this._plainEditorObj.tabIndex = 6;
			};
			this.RedrawAttachments(msg.Attachments);
			this.RebuildUploadForm();
		}
	},//Fill
	
	RedrawAttachments: function (attachs)
	{
		CleanNode(this._attachmentsTbl);
		this._attachments = Array();
		this._rowIndex = 0;
		for (var i in attachs) {
			this.LoadAttachment(attachs[i]);
		}
	},//RedrawAttachments
	

//mode = 0 - send message
//mode = 1 - save message
	SaveChanges: function (mode)
	{
		if (this._sending && mode == 0) return;
		if (this._saving && mode == 1) return;
		var fromValue = Trim(this._fromObj.value);
		if (mode == 0 && fromValue.length < 4) {
			alert(Lang.WarningFromBlank);
			return;
		};
		var toValue = this._toObj.value;
		var ccValue = this._ccObj.value;
		var bccValue = this._bccObj.value;
		if (mode == 0 && toValue.length < 4 && ccValue.length < 4 && bccValue.length < 4) {
			alert(Lang.WarningToBlank);
			return;
		};
		var subjectValue = this._subjectObj.value;
		var save_anyway = true;
		if (mode == 0 && subjectValue.length == 0) {
		    save_anyway = confirm(Lang.ConfirmEmptySubject);
		}
		if (save_anyway) {
			var newMsg = new CMessage();
			newMsg.FromAddr = this._fromObj.value;
			newMsg.ToAddr = toValue;
			newMsg.CCAddr = ccValue;
			if (this._hasBcc) newMsg.BCCAddr = this._bccObj.value;
			newMsg.Subject = subjectValue;
			newMsg.Importance = this._priority;
			
			if (this._allowDhtmlEditor && this._htmlEditorField._htmlMode) {
				var value = this._htmlEditorField.GetText();
				if (value != false) {
					newMsg.HasHtml = true;
					newMsg.HtmlBody = value;
				}
			}
			else {
				newMsg.HasHtml = false;
				newMsg.PlainBody = this._plainEditorObj.value;
			};
			
			newMsg.Attachments = this._attachments;

			newMsg.Id = this._msgObj.Id;
			newMsg.Uid = this._msgObj.Uid;
			newMsg.SendersGroups = this._sendersGroups;

			var xml = newMsg.GetInXML();
			switch(mode) {
			    case (0):
			        RequestHandler('send', 'message', xml);
				    this._sending = true;
				    break;
				case (1):
				    RequestHandler('save', 'message', xml);
				    this._saving = true;
				    break;
			}
		}
	},//SaveChanges
	
	ReplaceHtmlEditorField: function ()
	{
		if (this._allowDhtmlEditor && null != this._htmlEditorField) {
			var bounds = GetBounds(this._plainEditorDiv);
			if (Browser.IE || Browser.Opera)
				this._htmlEditorField.Replace(bounds.Left, bounds.Top);
			else
				this._htmlEditorField.Replace(bounds.Left + 1, bounds.Top + 1);
		}
	},
	
	SwitchBccMode: function ()
	{
		if (this._hasBcc) {
			this._bccSwitcher.innerHTML = Lang.ShowBCC;
			this._hasBcc = false;
			this._bccCont.className = 'wm_hide';
		}
		else {
			this._bccSwitcher.innerHTML = Lang.HideBCC;
			this._hasBcc = true;
			this._bccCont.className = '';
		}
		if (null != this._htmlEditorField) {
			this._htmlEditorField.Replace();
		}
	},
	
	DesignModeOn: function ()
	{
		this._modeSwitcherCont.className = '';
		this._htmlEditorField.SetWaitHtml();
	},
	
	SetHtmlEditorField: function (heField)
	{
		this._htmlEditorField = heField;
	},
	
	CreateDeleteAttachmentClick: function (index, obj)
	{
		return function () { obj.DeleteAttachment(index); return false; }
	},
	
	LoadAttachment: function (attachment)
	{
		var obj = this;
		var tbl = this._attachmentsTbl;
		var tr = tbl.insertRow(this._rowIndex);
		var td = tr.insertCell(0);
		td.className = 'wm_attachment';
		var params = GetFileParams(attachment.FileName);
		var img = CreateChildWithAttrs(td, 'img', [['src', 'images/icons/' + params.image]]);
		var span = CreateChild(td, 'span');
		span.innerHTML = attachment.FileName + '&nbsp;(' + GetFriendlySize(attachment.Size) + ')&nbsp;';
		var a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		a.onclick = this.CreateDeleteAttachmentClick(this._rowIndex, obj);
		a.innerHTML = Lang.Delete;
		this._attachments[this._rowIndex] = attachment;
		this._rowIndex++;
		this.RebuildUploadForm();
	},//LoadAttachment
	
	DeleteAttachment: function (index)
	{
		delete this._attachments[index];
		var attachs = this._attachments;
		this.RedrawAttachments(attachs);
	},//DeleteAttachment
	
	ChangePriority: function ()
	{
		var pr = this._priority;
		switch (pr) {
			case (5): this.SetPriority(3); break;
			case (3): this.SetPriority(1); break;
			case (1): this.SetPriority(5); break;
		}
	},//ChangePriority
	
	SetPriority: function (pr)
	{
		switch (pr) {
			case (5):
				this._priority = 5;
				this._priorityButton.SetImgFile('priority_low.gif');
				this._priorityButton.SetText(Lang.Low);
			break;
			case (3):
				this._priority = 3;
				this._priorityButton.SetImgFile('priority_normal.gif');
				this._priorityButton.SetText(Lang.Normal);
			break;
			case (1):
				this._priority = 1;
				this._priorityButton.SetImgFile('priority_high.gif');
				this._priorityButton.SetText(Lang.High);
			break;
		}
	},//SetPriority
	
	BuildToolBar: function ()
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
		item = toolBar.AddItem(TOOLBAR_SEND_MESSAGE, function () { obj.SaveChanges(0); });
		item = toolBar.AddItem(TOOLBAR_SAVE_MESSAGE, function () { obj.SaveChanges(1); });
		this._priorityButton = toolBar.AddPriorityItem();
		this._priorityButton.MakeActive('wm_toolbar_item', 'wm_toolbar_item_over', 'priority_normal.gif', function () { obj.ChangePriority(); });
		this._toolBar = toolBar;
	},//BuildToolBar

	RebuildUploadForm: function ()
	{
		var form = this._uploadForm;
		CleanNode(form);
		var span = CreateChild(form, 'span');
		span.innerHTML = Lang.AttachFile + ':&nbsp;';
		var inp = CreateChildWithAttrs(form, 'input', [['type', 'file'], ['class', 'wm_file'], ['name', 'fileupload']]);
		this._uploadFile = inp;
		span = CreateChild(form, 'span');
		span.innerHTML = '&nbsp;';
		inp = CreateChildWithAttrs(form, 'input', [['type', 'submit'], ['class', 'wm_button'], ['value', Lang.Attach]]);
	},//RebuildUploadForm
	
	Build: function (container, accountsBar, popupMenus, settings)
	{
		var obj = this;
		this._showTextLabels = settings.ShowTextLabels;
		this._allowDhtmlEditor = settings.AllowDhtmlEditor;

		this._mainContainer = CreateChild(container, 'div');
		this._mainContainer.className = 'wm_hide';

		this._logo = document.getElementById('logo');
		this._accountsBar = accountsBar;

		this.BuildToolBar();
		
		this._showPicturesTbl = this._picturesControl.Build(this._mainContainer);
		var tbl = CreateChild(this._mainContainer, 'table');
		this._headersTbl = tbl;
		tbl.className = 'wm_new_message';
		var RowIndex = 0;
		
		var tr = tbl.insertRow(RowIndex++);
		var td = tr.insertCell(0);
		td.className = 'wm_new_message_title';
		td.innerHTML = Lang.From + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'From', ':');
		td = tr.insertCell(1);
		var inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '93'], ['maxlength', '255']]);
		inp.tabIndex = 1;
		this._fromObj = inp;
		
		tr = tbl.insertRow(RowIndex++);
		td = tr.insertCell(0);
		td.className = 'wm_new_message_title';
		var a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
        CreateTextChild(a, Lang.To);
        CreateTextChild(td, ':');
		WebMail.LangChanger.Register('innerHTML', td, 'To', ':');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '93']]);
		inp.tabIndex = 2;
		inp.onfocus = function () {
			obj.AutoFilling.SetSuggestInput(this);
		};
		this._toObj = inp;
		a.onclick = function () {
		    obj.PopupContacts.ControlClick(obj._toObj, this);
		    return false;
		};
		
		tr = tbl.insertRow(RowIndex++);
		td = tr.insertCell(0);
		td.className = 'wm_new_message_title';
		a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
        CreateTextChild(a, Lang.CC);
        CreateTextChild(td, ':');
		WebMail.LangChanger.Register('innerHTML', td, 'CC', ':');
		td = tr.insertCell(1);
		nobr = CreateChild(td, 'nobr');
		inp = CreateChildWithAttrs(nobr, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '93']]);
		inp.tabIndex = 3;
		inp.onfocus = function () {
			obj.AutoFilling.SetSuggestInput(this);
		};
		this._ccObj = inp;
		a.onclick = function () {
		    obj.PopupContacts.ControlClick(obj._ccObj, this);
		    return false;
		};

		var span = CreateChild(nobr, 'span');
		span.innerHTML = '&nbsp;';
		a = CreateChildWithAttrs(nobr, 'a', [['href', '#']]);
		a.onclick = function () { obj.SwitchBccMode(); return false; };
		a.innerHTML = Lang.ShowBCC;
		WebMail.LangChanger.Register('innerHTML', a, 'ShowBCC', '');
		a.tabIndex = -1;
		this._bccSwitcher = a;
		this._hasBcc = false;

		tr = tbl.insertRow(RowIndex++);
		tr.className = 'wm_hide';
		td = tr.insertCell(0);
		td.className = 'wm_new_message_title';
		a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
        CreateTextChild(a, Lang.BCC);
        CreateTextChild(td, ':');
		WebMail.LangChanger.Register('innerHTML', td, 'BCC', ':');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '93']]);
		inp.tabIndex = 4;
		inp.onfocus = function () {
			obj.AutoFilling.SetSuggestInput(this);
		};
		this._bccObj = inp;
		this._bccCont = tr;
		a.onclick = function () {
		    obj.PopupContacts.ControlClick(obj._bccObj, this);
		    return false;
		};

		tr = tbl.insertRow(RowIndex++);
		td = tr.insertCell(0);
		td.className = 'wm_new_message_title';
		td.innerHTML = Lang.Subject + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Subject', ':');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['size', '93'], ['maxlength', '255']]);
		inp.tabIndex = 5;
		inp.onfocus = function () {
			obj.AutoFilling.Hide();
		};
		this._subjectObj = inp;

		tr = tbl.insertRow(RowIndex++);
		td = tr.insertCell(0);
		td.colSpan = 2;
		var div = CreateChild(td, 'div');
		div.className = 'wm_input wm_plain_editor_container';
		var txt = CreateChild(div, 'textarea');
		txt.className = 'wm_plain_editor_text';
		txt.tabIndex = 6;
		this._plainEditorObj = txt;
		this._plainEditorDiv = div;
		this._plainEditorCont = td;

		tr = tbl.insertRow(RowIndex++);
		td = tr.insertCell(0);
		td = tr.insertCell(1);
		td.className = 'wm_html_editor_switcher';
		a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		a.innerHTML = Lang.SwitchToPlainMode;
		this._modeSwitcher = a;
		this._modeSwitcherCont = tr;

		tbl = CreateChild(this._mainContainer, 'table');
		tbl.className = 'wm_new_message';
		this._attachmentsTbl = tbl;

		tbl = CreateChild(this._mainContainer, 'table');
		tbl.className = 'wm_new_message';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_attach';
		var uploadFrame = CreateChildWithAttrs(td, 'iframe', [['src', EmptyHtmlUrl], ['name', 'UploadFrame'], ['id', 'UploadFrame'], ['class', 'wm_hide']]);
		this._uploadForm = CreateChildWithAttrs(td, 'form', [['action', UploadUrl], ['method', 'post'], ['enctype', 'multipart/form-data'], ['target', 'UploadFrame'], ['id', 'UploadForm']]);
		this.RebuildUploadForm();
		var obj = this;
		this._uploadForm.onsubmit = function () {
			if (obj._uploadFile.value.length == 0) return false;
		};
		this._uploadTbl = tbl;

        this.PopupContacts = new CPopupContacts(GetAutoFillingContactsHandler, SelectSuggestionHandler);
		this.AutoFilling = new CPopupAutoFilling(GetAutoFillingContactsHandler, SelectSuggestionHandler);
		
		this.isBuilded = true;
	}//Build
};

