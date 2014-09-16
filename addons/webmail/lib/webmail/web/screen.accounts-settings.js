/*
 * Classes:
 *  CAccountsListScreenPart
 *  CFiltersScreenPart
 *  CSignatureScreenPart
 *  CManageFoldersScreenPart
 */

function CAccountsListScreenPart(skinName, manageFolders)
{
	this.Accounts = null;
	this._idAcct = -1;
	this._allowChangeSettings = true;
	this._manageFoldersObj = manageFolders;
	this._mainContainer = null;
	this.shown = false;
}

CAccountsListScreenPart.prototype = {
	Show: function(idAcct, settings)
	{
		this.ParseSettings(settings);
		if (!this.shown) {
			this.shown = true;
			this._mainContainer.className = 'wm_settings_list';
		};
		if (null == this.Accounts) {
			GetHandler(TYPE_ACCOUNTS_LIST, { }, [], '');
		}
		else {
			this.ChangeIdAcct(idAcct);
		}
	},
	
	ParseSettings: function (settings)
	{
		this._allowChangeSettings = settings.AllowChangeSettings;
	},

	ChangeIdAcct: function (idAcct) {
		if (this.Accounts.LastId != idAcct) {
			this.Accounts.LastId = idAcct;
		};
		this.Fill();
	},
	
	Hide: function()
	{
		this.shown = false;
		this._mainContainer.className = 'wm_hide';
	},//Hide
	
	SetAccounts: function (accounts)
	{
		this.Accounts = accounts;
		var arrAccounts = this.Accounts.Items;
		var count = 0;
		for(var i in arrAccounts) {
			if (arrAccounts[i].DefAcct) count++;
		};
		return count;
	},//UpdateAccounts
	
	Fill: function ()
	{
		if (this.shown) {
			this._idAcct = this.Accounts.LastId;
			CleanNode(this._mainContainer);
			var tbl = CreateChild(this._mainContainer, 'table');
			var arrAccounts = this.Accounts.Items;
			var rowIndex = 0;
			for (var i in arrAccounts) {
				var account = arrAccounts[i];
				var tr = tbl.insertRow(rowIndex++);
				var td = tr.insertCell(0);
				if (account.Id == this.Accounts.LastId) {
					tr.className = 'wm_settings_list_select';
					td.innerHTML = '<b>' + account.Email + '</b>';
					this._manageFoldersObj.UpdateProtocol(account.MailProtocol);
				}
				else {
					td.className = 'wm_control';
					td.innerHTML = account.Email;
					td.onclick = CreateAccountClickFunc(account.Id);
				};
				if (!this._allowChangeSettings) continue;
				td = tr.insertCell(1);
				td.style.width = '10px';
				var a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
				a.innerHTML = $Delete[DELETE];
				a.onclick = CreateRemoveClickFunc(account.Id);
				if (WebMail._isDemo) {
					a.onclick = function () {
						WebMail.ShowReport(DemoWarning);
						return false;
					}
				};
			}
		}
	},//Fill
	
	Build: function(container)
	{
		this._mainContainer = CreateChild(container, 'div');
	}//Build
};

function CreateDeleteFilterClickFunc(id, idAcct)
{
	return function () {
		var xml = '<param name="id_filter" value="' + id + '"/>';
		xml += '<param name="id_acct" value="' + idAcct + '"/>';
		RequestHandler('delete', 'filter', xml);
		return false;
	}
}

function CreateEditFilterClickFunc(id, obj)
{
	return function () {
		obj.SetFilterProperties(id);
		return false;
	}
}

function CFiltersScreenPart()
{
	this._filters = Array();
	this._filterProperties = new CFilterProperties();
	this._xSpam = new CXSpam();
	
	this.hasXSpamChanges = false;
	this.hasPropChanges = false;
	this.shown = false;
	this.isSaveFilters = false;

	this._idAcct = -1;
	this._foldersIdAcct = -1;
	
	this._filtersTbl = null;
	this._editFilterTbl = null;
	this._xSpamTbl = null;

	this._changeNewFilterText = null;
	this._fieldObj = null;
	this._fieldOpts = Array();
	this._conditionObj = null;
	this._conditionOpts = Array();
	this._filterObj = null;
	this._actionObj = null;
	this._actionOpts = Array();
	this._folderObj = null;
	this._folderOpts = Array();
	this._changeNewFilterButton = null;
	this._cancelEditFilterButton = null;
	this._xSpamObj = null;

	this._inboxLangNumber = false;
	this._sentLangNumber = false;
	this._draftsLangNumber = false;
	this._trashLangNumber = false;
}

CFiltersScreenPart.prototype = {
	Show: function(idAcct, folders)
	{
		if (!this.shown) {
			this.shown = true;
			this._filtersTbl.className = 'wm_settings_list';
			this._editFilterTbl.className = 'wm_settings_edit_filter';
			this._xSpamTbl.className = 'wm_settings_filters';
			this._folderObj.className = '';
		};
		if (this._idAcct != idAcct) {
			this._idAcct = idAcct;
			GetHandler(TYPE_FILTERS, { IdAcct: this._idAcct }, [], '');
			GetHandler(TYPE_X_SPAM, { IdAcct: this._idAcct }, [], '');
		};
		if (this._foldersIdAcct != idAcct) {
			this._foldersIdAcct = idAcct;
			if (folders[idAcct]) {
			    this.FillFolders(folders[idAcct].Folders);
			}
			else {
				GetHandler(TYPE_FOLDERS_LIST, { IdAcct: this._idAcct, Sync: -1 }, [], '');
			}
		}
	},
	
	Hide: function()
	{
		this.shown = false;
		if (WebMail._isDemo) {
			this.FillFilterProperties();
			this.FillXSpam();
		}
		else {
			if (this.hasPropChanges)
				if (confirm(Lang.ConfirmSaveFilter))
					this.SavePropChanges();
				else
					this.FillFilterProperties();
			if (this.hasXSpamChanges)
				if (confirm(Lang.ConfirmSaveFilter))
					this.SaveXSpamChanges();
				else
					this.FillXSpam();
		};
		this.hasXSpamChanges = false;
		this.hasPropChanges = false;
		this._filtersTbl.className = 'wm_hide';
		this._editFilterTbl.className = 'wm_hide';
		this._xSpamTbl.className = 'wm_hide';
		this._folderObj.className = 'wm_hide';
	},
	
	SetFilters: function (filters)
	{
		if (this.isSaveFilters) {
			WebMail.ShowReport(Lang.ReportFiltersUpdatedSuccessfuly);
			this.isSaveFilters = false;
		};
		this._filters = filters;
		this.FillFilters();
	},
	
	SetFilterProperties: function (id)
	{
		var iCount = this._filters.length;
		for (var i=0; i<iCount; i++) {
			if (this._filters[i].Id == id) {
				this._filterProperties = this._filters[i];
				this.FillFilterProperties();
			}
		}
	},

	SetXSpam: function (xSpam)
	{
		if (this.isSaveFilters) {
			WebMail.ShowReport(Lang.ReportFiltersUpdatedSuccessfuly);
			this.isSaveFilters = false;
		};
		this._xSpam = xSpam;
		this.FillXSpam();
	},

	FillFolders: function (folders)
	{
		CleanNode(this._folderObj);
		this._folderOpts = Array();
		var iCount = folders.length;
		var folder, strIndent, opt, j;
		for (var i=0; i<iCount; i++) {
			folder = folders[i];
			for (j=0, strIndent = ''; j<folder.Level; j++) strIndent += '&nbsp;&nbsp;&nbsp;&nbsp;';
			opt = CreateChild(this._folderObj, 'option');
			switch (folder.Type) {
				case FOLDER_TYPE_INBOX:
					opt.innerHTML = strIndent + Lang.FolderInbox;
					this._inboxLangNumber = WebMail.LangChanger.Register('innerHTML', opt, 'FolderInbox', strIndent, '', this._inboxLangNumber);
				break;
				case FOLDER_TYPE_SENT:
					opt.innerHTML = strIndent + Lang.FolderSentItems;
					this._sentLangNumber = WebMail.LangChanger.Register('innerHTML', opt, 'FolderSentItems', strIndent, '', this._sentLangNumber);
				break;
				case FOLDER_TYPE_DRAFTS:
					opt.innerHTML = strIndent + Lang.FolderDrafts;
					this._draftsLangNumber = WebMail.LangChanger.Register('innerHTML', opt, 'FolderDrafts', strIndent, '', this._draftsLangNumber);
				break;
				case FOLDER_TYPE_TRASH:
					opt.innerHTML = strIndent + Lang.FolderTrash;
					this._trashLangNumber = WebMail.LangChanger.Register('innerHTML', opt, 'FolderTrash', strIndent, '', this._trashLangNumber);
				break;
				default:
					opt.innerHTML = strIndent + folder.Name;
				break;
			};
			opt.value = folder.Id;
			this._folderOpts[folder.Id] = opt;
		};
		if (!this.shown) this._folderObj.className = 'wm_hide';
	},
	
	FillFilters: function ()
	{
		if (this.shown) {
			var filterProp = this._filterProperties;
			var rowIndex = 0;
			var tbl = this._filtersTbl;
			CleanNode(tbl);
			for (var i in this._filters) {
				var filter = this._filters[i];
				var tr = tbl.insertRow(rowIndex);
				var td = tr.insertCell(0);
				td.innerHTML = filter.Desc;
				td = tr.insertCell(1);
				td.style.width = '10px';
				var nobr = CreateChild(td, 'nobr');
				var a = CreateChildWithAttrs(nobr, 'a', [['href', '#']]);
				a.innerHTML = Lang.EditFilter;
				var obj = this;
				a.onclick = CreateEditFilterClickFunc(filter.Id, obj);
				td = tr.insertCell(2);
				td.style.width = '10px';
				nobr = CreateChild(td, 'nobr');
				a = CreateChildWithAttrs(nobr, 'a', [['href', '#']]);
				a.innerHTML = $Delete[DELETE];
				a.onclick = CreateDeleteFilterClickFunc(filter.Id, this._idAcct);
				rowIndex++;
			};
			this._filterProperties = new CFilterProperties();
			this.FillFilterProperties();
		}
	},
	
	FillFilterProperties: function ()
	{
		if (this.shown) {
			var filterProp = this._filterProperties;
			if (filterProp.Id == -1 ) {
				this._changeNewFilterText.innerHTML = Lang.NewFilter;
				this._changeNewFilterButton.value = Lang.Add;
        		this._cancelEditFilterButton.className = 'wm_hide';
			}
			else {
				this._changeNewFilterText.innerHTML = Lang.EditFilter;
				this._changeNewFilterButton.value = Lang.Save;
				this._cancelEditFilterButton.className = '';
			};
			this._fieldOpts[filterProp.Field].selected = true;
			this._conditionOpts[filterProp.Condition].selected = true;
			this._filterObj.value = HtmlDecode(filterProp.Value);
			this._actionOpts[filterProp.Action].selected = true;
			if (filterProp.Action == 3)
				this._folderObj.disabled = false;
			else
				this._folderObj.disabled = true;
			if (this._folderOpts[filterProp.IdFolder])
				this._folderOpts[filterProp.IdFolder].selected = true;
			this.hasPropChanges = false;
		}
	},
	
	FillXSpam: function ()
	{
		if (this.shown) {
			this._xSpamObj.checked = this._xSpam.Value;
			this.hasXSpamChanges = false;
		}
	},
	
	Fill: function ()
	{
		this.FillFilters();
		this.FillFilterProperties();
		this.FillXSpam();
	},
	
    CancelPropChanges: function ()
    {
		this._filterProperties = new CFilterProperties();
		this.FillFilterProperties();
    },
	
	SavePropChanges: function ()
	{
		if (WebMail._isDemo) {
			WebMail.ShowReport(DemoWarning);
			return;
		};

		var val = new CValidate();
		var filterValue = this._filterObj.value;
		if (val.IsEmpty(filterValue)) {
			alert(Lang.WarningEmptyFilter);
			return;
		};

		var filterProp = this._filterProperties;
		var newFilterProp = new CFilterProperties();
		newFilterProp.Id = filterProp.Id;
		newFilterProp.Field = this._fieldObj.value;
		newFilterProp.Condition = this._conditionObj.value;
		newFilterProp.Action = this._actionObj.value;
		newFilterProp.IdFolder = this._folderObj.value;
		newFilterProp.Value = filterValue;
		if (this._idAcct != -1) {
			var xml = newFilterProp.GetInXML(this._idAcct);
			if (newFilterProp.Id != -1 )
				RequestHandler('update', 'filter', xml);
			else
				RequestHandler('new', 'filter', xml);
			this.hasPropChanges = false;
			this.isSaveFilters = true;
		}
	},

	SaveXSpamChanges: function ()
	{
		if (WebMail._isDemo) {
			WebMail.ShowReport(DemoWarning);
			return;
		};

		var newXSpam = new CXSpam();
		newXSpam.Value = this._xSpamObj.checked;
		var xml = newXSpam.GetInXML();
		RequestHandler('update', 'x_spam', xml);
		this.hasXSpamChanges = false;
		this.isSaveFilters = true;
	},

	Build: function(container)
	{
		var obj = this;
		var tbl = CreateChild(container, 'table');
		tbl.className = 'wm_hide';
		this._filtersTbl = tbl;

		tbl = CreateChild(container, 'table');
		this._editFilterTbl = tbl;
		tbl.className = 'wm_hide';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.colSpan = 3;
		var b = CreateChild(td, 'b');
		b.innerHTML = Lang.NewFilter;
		WebMail.LangChanger.Register('innerHTML', b, 'NewFilter', '');
		this._changeNewFilterText = b;

		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = Lang.Field + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Field', ':');
		td = tr.insertCell(1);
		td.colSpan = 2;
		var sel = CreateChild(td, 'select');
		sel.onchange = function () { obj.hasPropChanges = true; };
		var opt = CreateChildWithAttrs(sel, 'option', [['value', '0']]);
		opt.innerHTML = Lang.From;    this._fieldOpts[0] = opt;
		WebMail.LangChanger.Register('innerHTML', opt, 'From', '');
		opt = CreateChildWithAttrs(sel, 'option', [['value', '1']]);
		opt.innerHTML = Lang.To;      this._fieldOpts[1] = opt;
		WebMail.LangChanger.Register('innerHTML', opt, 'To', '');
		opt = CreateChildWithAttrs(sel, 'option', [['value', '2']]);
		opt.innerHTML = Lang.Subject; this._fieldOpts[2] = opt;
		WebMail.LangChanger.Register('innerHTML', opt, 'Subject', '');
		this._fieldObj = sel;
		
		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = Lang.Condition + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Condition', ':');
		td = tr.insertCell(1);
		sel = CreateChild(td, 'select');
		sel.onchange = function () { obj.hasPropChanges = true; };
		opt = CreateChildWithAttrs(sel, 'option', [['value', '0']]);
		opt.innerHTML = Lang.ContainSubstring;    this._conditionOpts[0] = opt;
		WebMail.LangChanger.Register('innerHTML', opt, 'ContainSubstring', '');
		opt = CreateChildWithAttrs(sel, 'option', [['value', '1']]);
		opt.innerHTML = Lang.ContainExactPhrase;  this._conditionOpts[1] = opt;
		WebMail.LangChanger.Register('innerHTML', opt, 'ContainExactPhrase', '');
		opt = CreateChildWithAttrs(sel, 'option', [['value', '2']]);
		opt.innerHTML = Lang.NotContainSubstring; this._conditionOpts[2] = opt;
		WebMail.LangChanger.Register('innerHTML', opt, 'NotContainSubstring', '');
		this._conditionObj = sel;
		td = tr.insertCell(2);
		var inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_input wm_edit_filter_input'], ['type', 'text'], ['maxlength', '85']]);
		inp.onchange = function () { obj.hasPropChanges = true; };
		this._filterObj = inp;
		
		tr = tbl.insertRow(3);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = Lang.Action + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'Action', '');
		td = tr.insertCell(1);
		sel = CreateChild(td, 'select');
		sel.onchange = function () {
			if (this.value == '3')
				obj._folderObj.disabled = false;
			else
				obj._folderObj.disabled = true;
			obj.hasPropChanges = true;
		};
		var opt = CreateChildWithAttrs(sel, 'option', [['value', '1']]);
		opt.innerHTML = Lang.DeleteFromServer; this._actionOpts[1] = opt;
		WebMail.LangChanger.Register('innerHTML', opt, 'DeleteFromServer', '');
		opt = CreateChildWithAttrs(sel, 'option', [['value', '2']]);
		opt.innerHTML = Lang.MarkGrey;         this._actionOpts[2] = opt;
		WebMail.LangChanger.Register('innerHTML', opt, 'MarkGrey', '');
		opt = CreateChildWithAttrs(sel, 'option', [['value', '3']]);
		opt.innerHTML = Lang.MoveToFolder;     this._actionOpts[3] = opt;
		WebMail.LangChanger.Register('innerHTML', opt, 'MoveToFolder', '');
		this._actionObj = sel;
		td = tr.insertCell(2);
		sel = CreateChild(td, 'select');
		sel.onchange = function () { obj.hasPropChanges = true; };
		sel.disabled = true;
		this._folderObj = sel;

		tr = tbl.insertRow(4);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.colSpan = 4;
		var hr = CreateChild(td, 'hr');
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_button'], ['type', 'button'], ['value', Lang.Add]]);
		WebMail.LangChanger.Register('value', inp, 'Add', '');
		inp.onclick = function () { obj.SavePropChanges(); };
		this._changeNewFilterButton = inp;
		var span = CreateChild(td, 'span');
		span.className = 'wm_hide';
		CreateTextChild(span, ' ');
		inp = CreateChildWithAttrs(span, 'input', [['class', 'wm_button'], ['type', 'button'], ['value', Lang.Cancel]]);
		WebMail.LangChanger.Register('value', inp, 'Cancel', '');
		inp.onclick = function () { obj.CancelPropChanges(); };
		this._cancelEditFilterButton = span;
		
		tbl = CreateChild(container, 'table');
		this._xSpamTbl = tbl;
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		b = CreateChild(td, 'b');
		b.innerHTML = Lang.OtherFilterSettings;
		WebMail.LangChanger.Register('innerHTML', b, 'OtherFilterSettings', '');
		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['id', 'x_spam']]);
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'x_spam']]);
		lbl.innerHTML = Lang.ConsiderXSpam;
		WebMail.LangChanger.Register('innerHTML', lbl, 'ConsiderXSpam', '');
		inp.onchange = function () { obj.hasXSpamChanges = true; };
		this._xSpamObj = inp;
		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.colSpan = 4;
		var hr = CreateChild(td, 'hr');
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_button'], ['type', 'button'], ['value', Lang.Apply]]);
		WebMail.LangChanger.Register('value', inp, 'Apply', '');
		inp.onclick = function () { obj.SaveXSpamChanges(); };
	}//Build
};

function CSignatureScreenPart()
{
	this._signature = null;
	this._newSignature = null;
	
	this.hasChanges = false;
	this._allowDhtmlEditor = false;
	this.shown = false;

	this._plainEditorObj = null;
	this._plainEditorDiv = null;
	this._htmlEditorField = null;
	this._modeSwitcher = null;
	this._modeSwitcherCont = null;
	this._allowDhtmlEditor = true;

	this._idAcct = -1;
	
	this._mainTbl = null;
	this._opt1Obj = null;
	this._opt2Obj = null;
	this._buttonTbl = null;
}

CSignatureScreenPart.prototype = {
	Show: function(idAcct, settings)
	{
		this.ParseSettings(settings);
		this.hasChanges = false;
		this._mainTbl.className = 'wm_settings_signature';
		this._buttonTbl.className = 'wm_settings_buttons';

		var width = 684;
		var height = 330;
		this._plainEditorDiv.style.height = height + 'px';
		this._plainEditorDiv.style.width = width + 'px';

		if (null != this._htmlEditorField) {
			this._htmlEditorField.Show();
			this._htmlEditorField.SetPlainEditor(this._plainEditorObj, this._modeSwitcher);
			this._htmlEditorField.Resize(width, height);
		}
		else {
			this._plainEditorObj.style.height = (height - 1) + 'px';
			this._plainEditorObj.style.width = (width - 2) + 'px';
		};
		this.shown = true;
		if (this._idAcct != idAcct) {
			this._idAcct = idAcct;
			GetHandler(TYPE_SIGNATURE, { IdAcct: this._idAcct }, [], '');
		}
		else {
			this.Fill();
		};
		if (this._allowDhtmlEditor) {
			this._modeSwitcherCont.className = '';
		}
		else {
			this._modeSwitcherCont.className = 'wm_hide';
			if (null != this._htmlEditorField) {
				this._htmlEditorField.Hide();
			}
		}
	},//Show
	
	ClickBody: function (ev)
	{
		if (null != this._htmlEditorField) {
			this._htmlEditorField.ClickBody();
		}
	},//ClickBody
	
	ReplaceHtmlEditorField: function ()
	{
		if (null != this._htmlEditorField) {
			this._htmlEditorField.Replace();
		}
	},

	ParseSettings: function (settings)
	{
		if (this._allowDhtmlEditor != settings.AllowDhtmlEditor) {
			this._allowDhtmlEditor = settings.AllowDhtmlEditor;
			if (null == this._htmlEditorField) {
				this._allowDhtmlEditor = false;
			};
			if (!this._allowDhtmlEditor) {
				this._htmlEditorField = null;
			}
		}
	},

	Hide: function()
	{
		this.shown = false;
		if (WebMail._isDemo) {
			this.Fill();
		}
		else if (this.hasChanges) {
			if (confirm(Lang.ConfirmSaveSignature)) {
				this.SaveChanges();
			}
			else {
				this.Fill();
			}
		}
		this.hasChanges = false;
		if (this._allowDhtmlEditor && this._htmlEditorField != null) {
			this._htmlEditorField.Hide();
		}
		this._mainTbl.className = 'wm_hide';
		this._buttonTbl.className = 'wm_hide';
	},//Hide
	
	DesignModeOn: function ()
	{
		this._modeSwitcherCont.className = '';
		this._htmlEditorField.SetWaitHtml();
	},

	SetHtmlEditorField: function (heField)
	{
		this._htmlEditorField = heField;
	},
	
	GetNewSignature: function ()
	{
		var signature = new CSignature();
		signature.isHtml = this._newSignature.isHtml;
		signature.Value  = this._newSignature.Value;
		signature.Opt    = this._newSignature.Opt;
		signature.IdAcct = this._newSignature.IdAcct;
		this._signature = signature;
		return signature;
	},

	SetSignature: function (signature)
	{
		this._signature = signature;
		this._idAcct = signature.IdAcct;
		this.Fill();
	},//UpdateSignature

	Fill: function ()
	{
		if ((null != this._signature) && this.shown) {
			var signature = this._signature;
			if (this._allowDhtmlEditor) {
				if (signature.isHtml) {
					this._htmlEditorField.SetHtml(signature.Value);
					this._htmlEditorField.Show();
				}
				else {
					this._htmlEditorField.SetText(signature.Value);
				}
			}
			else {
				if (signature.isHtml) {
					this._plainEditorObj.value = HtmlDecode(signature.Value.replace(/<br *\/{0,1}>/gi, '\n').replace(/<[^>]*>/g, ''));
				}
				else {
					this._plainEditorObj.value = signature.Value;
				}
			};
			switch (signature.Opt) {
				case 0:
					this._opt1Obj.checked = false;
					this._opt2Obj.checked = false;
					this._opt2Obj.disabled = true;
					break;
				case 1:
					this._opt1Obj.checked = true;
					this._opt2Obj.checked = false;
					this._opt2Obj.disabled = false;
					break;
				case 2:
					this._opt1Obj.checked = true;
					this._opt2Obj.checked = true;
					this._opt2Obj.disabled = false;
					break;
			}
		}
	},//Fill
	
	SaveChanges: function ()
	{
		if (WebMail._isDemo) {
			WebMail.ShowReport(DemoWarning);
			return;
		}

		var signature = new CSignature();
		if (null != this._htmlEditorField && this._htmlEditorField._htmlMode) {
			signature.isHtml = true;
			signature.Value = this._htmlEditorField.GetText();
		}
		else {
			signature.isHtml = false;
			signature.Value = this._plainEditorObj.value;
		}
		if (this._opt1Obj.checked) {
			if (this._opt2Obj.checked) {
				signature.Opt = 2;
			}
			else {
				signature.Opt = 1;
			}
		}
		else {
			signature.Opt = 0;
		}
		signature.IdAcct = this._idAcct;
		this._newSignature = signature;
		var xml = signature.GetInXML();
		RequestHandler('update', 'signature', xml);
		this.hasChanges = false;
	},//SaveChanges
	
	AddToolBarItem: function (parent, image, title)
	{
		var child = CreateChild(parent, 'div');
		child.className = 'wm_toolbar_item';
		child.onmouseover = function () { this.className='wm_toolbar_item_over'; };
		child.onmouseout = function () { this.className='wm_toolbar_item'; };
		var img = CreateChildWithAttrs(child, 'img', [['src', 'images/html_editor/' + image], ['title', title]]);
		return child;
	},
	
	AddToolBarSeparate: function (parent)
	{
		var child = CreateChild(parent, 'div');
		child.className = 'wm_toolbar_separate';
		var img = CreateChildWithAttrs(child, 'img', [['src', 'images/1x1.gif']]);
		return child;
	},

	Build: function(container)
	{
		var obj = this;
		var tbl = CreateChild(container, 'table');
		tbl.className = 'wm_hide';
		this._mainTbl = tbl;

		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		var div = CreateChild(td, 'div');
		div.className = 'wm_input wm_plain_editor_container';
		var txt = CreateChild(div, 'textarea');
		txt.className = 'wm_plain_editor_text';
		this._plainEditorObj = txt;
		this._plainEditorDiv = div;

		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		var a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
		a.className = '';
		a.innerHTML = Lang.SwitchToPlainMode;
		this._modeSwitcher = a;
		this._modeSwitcherCont = tr;
		
		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['id', 'opt1']]);
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'opt1']]);
		lbl.innerHTML = Lang.AddSignatures;
		WebMail.LangChanger.Register('innerHTML', lbl, 'AddSignatures', '');
		inp.onclick = function () {
			if (this.checked)
				obj._opt2Obj.disabled = false;
			else
				obj._opt2Obj.disabled = true;
			obj.hasChanges = true;
		};
		this._opt1Obj = inp;
		tr = tbl.insertRow(3);
		td = tr.insertCell(0);
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox wm_settings_para'], ['type', 'checkbox'], ['id', 'opt2']]);
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'opt2']]);
		lbl.innerHTML = Lang.DontAddToReplies;
		WebMail.LangChanger.Register('innerHTML', lbl, 'DontAddToReplies', '');
		inp.onchange = function () { obj.hasChanges = true; };
		this._opt2Obj = inp;

		tbl = CreateChild(container, 'table');
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_button'], ['type', 'button'], ['value', Lang.Save]]);
		WebMail.LangChanger.Register('value', inp, 'Save', '');
		inp.onclick = function () {
			obj.SaveChanges();
		};
		this._buttonTbl = tbl;
	}//Build
};

function CManageFoldersScreenPart(skinName)
{
	this._skinName = skinName;
	this._folders = new CFoldersList();
	this.FoldersList = Array();
	this.Folders = [];
	this._SEPARATOR = '#@%';
	
	this.loadFolders = false;
	this.hasChanges = false;
	this.isSaveFolders = false;
	this.shown = false;
	this._allowDirectMode = true;
	this.disableCount = 0;

	this._idAcct = -1;
	this._changedIdAcct = false;
	this._protocol = POP3_PROTOCOL;
	
	this._mainCont = null;
	this._infoTbl = null;
	this._buttonsTbl = null;
	this._newFolderDiv = null;
	this._foldersSelObj = null;
	this._noParentObj = null;
	this._onMailServerTd = null;
	this._onMailServerObj = null;
	this._inWebMailObj = null;
	this._nameObj = null;
	this._checkAllObj = null;
}

CManageFoldersScreenPart.prototype = {
	Show: function (idAcct, settings)
	{
		this._allowDirectMode = settings.AllowDirectMode;
		this.shown = true;
		this.hasChanges = false;
		this._mainCont.className = '';
		this._buttonsTbl.className = 'wm_settings_buttons';
		if (this.disableCount > 0) this._infoTbl.className = 'wm_secondary_info';
		this.CloseNewFolder();
		if (this._idAcct != idAcct) {
			this._idAcct = idAcct;
			if (this.Folders[idAcct]) {
			    this._folders = this.Folders[idAcct];
			    this.Fill();
			}
			else {
			    GetHandler(TYPE_FOLDERS_LIST, { IdAcct: this._idAcct, Sync: -1 }, [], '');
			}
		}
		else {
			this.Fill();
		}
	},//Show
	
	Hide: function ()
	{
		this.shown = false;
		if (WebMail._isDemo) {
			this.Fill();
		}
		else if (this.hasChanges) {
			if (confirm(Lang.ConfirmSavefolders)) {
				this.SaveChanges();
			}
			else {
				this.Fill();
			}
		};
		this.hasChanges = false;
		this._mainCont.className = 'wm_hide';
		this._buttonsTbl.className = 'wm_hide';
		this._infoTbl.className = 'wm_hide';
		this.CloseNewFolder();
	},//Hide
	
	ChangeSkin: function (newSkin)
	{
		this._skinName = newSkin;
	},
	
	AddNewFolder: function ()
	{
		if (this.hasChanges)
			if (confirm(Lang.ConfirmAddFolder))
				this.SaveChanges();
			else
				this.Fill();
		this._noParentObj.selected = true;
		this._onMailServerObj.checked = true;
		this._nameObj.value = '';
		this._newFolderDiv.className = '';
		this._foldersSelObj.className = '';
		this._noParentObj.selected = true;
		if (this._protocol == IMAP4_PROTOCOL)
			this._onMailServerTd.className = '';
		else
			this._onMailServerTd.className = 'wm_hide';
	},
	
	SaveNewFolder: function ()
	{
		if (WebMail._isDemo) {
			WebMail.ShowReport(DemoWarning);
			this.CloseNewFolder();
			return;
		};

		var val = new CValidate();
		var folderName = this._nameObj.value;
		if (val.IsEmpty(folderName)) {
			alert(Lang.WarningEmptyFolderName);
			return;
		};
		if (!val.IsCorrectFileName(folderName) || val.HasSpecSymbols(folderName))
		{
			alert(Lang.WarningCorrectFolderName);
			return;
		};

		var xml = '<param name="id_acct" value="' + this._idAcct + '"/>';
		var values = this._foldersSelObj.value.split(this._SEPARATOR);
		var idParent = values[0];
		var fullNameParent = values[1];
		xml += '<param name="id_parent" value="' + idParent + '"/>';
		xml += '<param name="full_name_parent">' + GetCData(fullNameParent) + '</param>';
		xml += '<param name="name">' + GetCData(this._nameObj.value) + '</param>';
		if (this._protocol == IMAP4_PROTOCOL) {
			if (this._onMailServerObj.checked)
				xml += '<param name="create" value="1"/>';
			else
				xml += '<param name="create" value="0"/>';
		} else {
			xml += '<param name="create" value="0"/>';
		};
		RequestHandler('new', 'folder', xml);
		this.CloseNewFolder();
	},
	
	CloseNewFolder: function ()
	{
		this._newFolderDiv.className = 'wm_hide';
	},
	
	UpdateFolders: function (folders)
	{
		if (this.isSaveFolders) {
			WebMail.ShowReport(Lang.ReportFoldersUpdatedSuccessfuly);
			this.isSaveFolders = false;
		};
		this.Folders[folders.IdAcct] = folders;
		this._folders = folders;
		this.loadFolders = true;
		this._changedIdAcct = false;
		this.Fill();
	},//UpdateFolders

	UpdateProtocol: function (protocol)
	{
		this._protocol = protocol;
		if (this._protocol == IMAP4_PROTOCOL)
			this._onMailServerTd.className = '';
		else
			this._onMailServerTd.className = 'wm_hide';
	},//UpdateProtocol

	CheckAll: function (value)
	{
		this._checkAllObj.checked = value;
		var count = this.FoldersList.length;
		for (var i=0; i<count; i++) {
			this.FoldersList[i].SetChecked(value);
		}
	},
	
	DeleteSelected: function ()
	{
		if (this.hasChanges)
			if (confirm(Lang.ConfirmAddFolder))
				this.SaveChanges();
			else
				this.Fill();
		var id;
		var idArray = Array();
		var count = this.FoldersList.length;
		var xml = '<param name="id_acct" value="' + this._idAcct + '"/>';
		var folders = '';
		for (var i=0; i<count; i++) {
			folders += this.FoldersList[i].GetCheckedXml();
		};
		xml += '<folders>' + folders + '</folders>';
		if (folders != '')
			RequestHandler('delete', 'folders', xml);
	},

	Fill: function ()
	{
		if (this._folders.IdAcct == this._idAcct) {
			var fldSel = this._foldersSelObj;
			CleanNode(fldSel);
			var opt = CreateChildWithAttrs(fldSel, 'option', [['value', '-1' + this._SEPARATOR]]);
			opt.innerHTML = Lang.NoParent;
			this._noParentObj = opt;
	
			CleanNode(this._mainCont);
			var tbl = CreateChild(this._mainCont, 'table');
			tbl.className = 'wm_settings_manage_folders';
			this.BuildHeader(tbl);
	
			var rowIndex = 1;
			var colIndex;
			var folders = this._folders.Folders;
			var tr, td, fold, indent, width, span;
			var checkInp = null;
			var syncSel = null;
			var nameTd, nameImg, spanA, nameA, nameInp, hideImg, upImg, downImg;
			var count = 0;
			var size = 0;
			this.FoldersList = Array();
			var prevFoldIndexes = Array();
			var foldLine = null;
			var hasNext = false;
			var iCount = folders.length;
			this.disableCount = 0;
			for (var i=0; i<iCount; i++) {
				fold = folders[i];
				colIndex = 0;
	
				tr = tbl.insertRow(rowIndex++);
				td = tr.insertCell(colIndex++);
				checkInp = CreateChildWithAttrs(td, 'input', [['type', 'checkbox'], ['class', 'wm_checkbox']]);
	
				td = tr.insertCell(colIndex++);
				nameTd = td;
				td.className = 'wm_settings_mf_folder';
				nameImg = CreateChildWithAttrs(td, 'img', [['src', 'skins/' + this._skinName + '/folders/' + FolderImages[fold.Type]]]);
				spanA = CreateChild(td, 'span');
				spanA.innerHTML = '&nbsp;';
				nameA = CreateChildWithAttrs(td, 'a', [['href', '#'], ['onclick', 'return false;']]);
				nameA.innerHTML = HtmlEncode(fold.Name);
				width = (indent < 190) ? (240 - indent) : 50;
				nameInp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_hide'], ['style', 'width: ' + width + 'px;'], ['maxlength', '30']]);
	
				var countTd = tr.insertCell(colIndex++);
				countTd.innerHTML = fold.MsgCount;
				count += fold.MsgCount;
	
				var sizeTd = tr.insertCell(colIndex++);
				sizeTd.innerHTML = GetFriendlySize(fold.Size);
				size += fold.Size;
				
				if (this._protocol == IMAP4_PROTOCOL) {
					td = tr.insertCell(colIndex++);
					syncSel = CreateChild(td, 'select');
				};
	
				td = tr.insertCell(colIndex++);
				if (fold.Hide)
					hideImg = CreateChildWithAttrs(td, 'img', [['src', 'skins/' + this._skinName + '/folders/hide.gif'], ['class', 'wm_settings_mf_show_hide wm_control_img']]);
				else
					hideImg = CreateChildWithAttrs(td, 'img', [['src', 'skins/' + this._skinName + '/folders/show.gif'], ['class', 'wm_settings_mf_show_hide wm_control_img']]);
	
				td = tr.insertCell(colIndex++);
				td.className = 'wm_settings_mf_up_down';
	
				upImg = CreateChildWithAttrs(td, 'img', [['src', 'skins/' + this._skinName + '/folders/up_inactive.gif']]);
				downImg = CreateChildWithAttrs(td, 'img', [['src', 'skins/' + this._skinName + '/folders/down_inactive.gif']]);
	
				opt = CreateChildWithAttrs(fldSel, 'option', [['value', fold.Id + this._SEPARATOR + fold.FullName]]);
				opt.innerHTML = fold.Name;
				var obj = this;
				if (typeof(prevFoldIndexes[fold.IdParent]) == 'number') {
					var prevIndex = prevFoldIndexes[fold.IdParent];
					this.FoldersList[prevIndex].SetNextFoldLine(i);
				}
				else {
					var prevIndex = -1;
				};
				foldLine = new CFolderLine(fold, nameTd, checkInp, nameImg, spanA, nameA, nameInp, syncSel, hideImg, this._skinName, opt, obj, prevIndex, i, upImg, downImg, countTd, sizeTd, this._allowDirectMode);
	
				this.disableCount += foldLine.checkDisable;
				this.FoldersList[i] = foldLine;
				prevFoldIndexes[fold.IdParent] = i;
			};
	
			if (this.disableCount > 0 && this.shown) this._infoTbl.className = 'wm_secondary_info';
			else this._infoTbl.className = 'wm_hide';
			this.BuildTotal(tbl, rowIndex, count, size);
			this.hasChanges = false;
	
			this.CloseNewFolder();
		}
	},//Fill
	
	ChangeFoldersPlaces: function (prevIndex, index)
	{
		var fold = this.FoldersList[index];
		var prop = fold.GetProperties();
		var fldOrder = prop.FldOrder;
		var nextIndex = prop.NextIndex;
		var prevFold = this.FoldersList[prevIndex];
		var prevProp = prevFold.GetProperties();
		prop.FldOrder = prevProp.FldOrder;
		prevProp.FldOrder = fldOrder;
		if (nextIndex != -1) {
			var nextFold = this.FoldersList[nextIndex];
			var nextProp = nextFold.GetProperties();
		};
		var foldPrevIndex = prevProp.PrevIndex;
		var prevFoldPrevIndex = prop.PrevIndex;
		var prevFoldNextIndex = prop.NextIndex;
		
		var i, childFold, childProp;
		var prevChilds = Array();
		for (i=prevIndex+1; i<index; i++) {
			childFold = this.FoldersList[i];
			childProp = childFold.GetProperties();
			prevChilds.push(childProp);
		};
		var prevChCount = prevChilds.length;

		var childs = Array();
		var flag = true;
		var idParent = prop.IdParent;
		var level = prop.Level;
		for (i=index+1; flag; i++) {
			if (i == nextIndex) {
				flag = false;
			}
			else {
				if (childFold = this.FoldersList[i]) {
					childProp = childFold.GetProperties();
					if (idParent == childProp.IdParent || level >= childProp.Level) {
						flag = false;
					}
					else {
						childs.push(childProp);
					}
				}
				else {
					flag = false;
				}
			}
		};
		var chCount = childs.length;
		
		var newIndex = prevIndex + chCount + 1;
		
		prop.PrevIndex = foldPrevIndex;
		prop.Index = prevIndex;
		prop.NextIndex = newIndex;
		this.FoldersList[prevIndex].SetProperties(prop);
		
		for(i=0; i<chCount; i++) {
			if (childs[i].PrevIndex != -1)
				childs[i].PrevIndex = childs[i].PrevIndex - prevChCount - 1;
			childs[i].Index = childs[i].Index - prevChCount - 1;
			if (childs[i].NextIndex != -1)
				childs[i].NextIndex = childs[i].NextIndex - prevChCount - 1;
			this.FoldersList[prevIndex + 1 + i].SetProperties(childs[i]);
		};
		
		prevProp.PrevIndex = prevFoldPrevIndex;
		prevProp.Index = newIndex;
		prevProp.NextIndex = prevFoldNextIndex;
		this.FoldersList[newIndex].SetProperties(prevProp);

		for(i=0; i<prevChCount; i++) {
			if (prevChilds[i].PrevIndex != -1)
				prevChilds[i].PrevIndex = prevChilds[i].PrevIndex + chCount + 1;
			prevChilds[i].Index = prevChilds[i].Index + chCount + 1;
			if (prevChilds[i].NextIndex != -1)
				prevChilds[i].NextIndex = prevChilds[i].NextIndex + chCount + 1;
			this.FoldersList[newIndex + 1 + i].SetProperties(prevChilds[i]);
		};

		if (nextIndex != -1) {
			nextProp.PrevIndex = newIndex;
			this.FoldersList[nextIndex].SetProperties(nextProp);
		};
		this.hasChanges = true;
	},//ChangeFoldersPlaces
	
	SaveChanges: function ()
	{
		if (WebMail._isDemo) {
			WebMail.ShowReport(DemoWarning);
			return;
		};

		var nodes = '';
		var count = this.FoldersList.length;
		for (var i=0; i<count; i++) {
			nodes += this.FoldersList[i].GetInXml();
		};
		var xml = '<param name="id_acct" value="' + this._idAcct + '"/>';
		xml += '<folders>' + nodes + '</folders>';
		RequestHandler('update', 'folders', xml);
		this.hasChanges = false;
		this.isSaveFolders = true;
	},//SaveChanges
	
	BuildHeader: function (tbl)
	{
		var obj = this;
		var colIndex = 0;
		var tr = tbl.insertRow(0);
		tr.className = 'wm_settings_mf_headers';
		var td = tr.insertCell(colIndex++);
		td.style.width = '30px';
		var inp = CreateChildWithAttrs(td, 'input', [['type', 'checkbox'], ['class', 'wm_checkbox']]);
		inp.onclick = function () { obj.CheckAll(this.checked); };
		this._checkAllObj = inp;

		td = tr.insertCell(colIndex++);
		if (this._protocol == IMAP4_PROTOCOL) {
			td.style.width = '270px';
		}
		else {
			td.style.width = (270 + 140) + 'px';
		};
		td.className = 'wm_settings_mf_folder';
		td.innerHTML = Lang.Folder;

		td = tr.insertCell(colIndex++);
		td.style.width = '40px';
		td.innerHTML = Lang.Msgs;

		td = tr.insertCell(colIndex++);
		td.style.width = '40px';
		td.innerHTML = Lang.Size;

		if (this._protocol == IMAP4_PROTOCOL) {
			td = tr.insertCell(colIndex++);
			td.style.width = '140px';
			td.innerHTML = Lang.Synchronize;
		};

		td = tr.insertCell(colIndex++);
		td.style.width = '100px';
		td.innerHTML = Lang.ShowThisFolder;

		td = tr.insertCell(colIndex++);
		td.style.width = '42px';
	},
	
	BuildTotal: function (tbl, index, totalCount, totalSize)
	{
		var tr = tbl.insertRow(index);
		tr.className = 'wm_settings_mf_total';
		var td = tr.insertCell(0);
		
		td = tr.insertCell(1);
		td.className = 'wm_settings_mf_folder';
		td.innerHTML = Lang.Total;

		td = tr.insertCell(2);
		td.innerHTML = totalCount;

		td = tr.insertCell(3);
		td.innerHTML = GetFriendlySize(totalSize);

		td = tr.insertCell(4);
		if (this._protocol == IMAP4_PROTOCOL) {
			td.colSpan = 3;
		}
		else {
			td.colSpan = 2;
		};
		td.className = 'wm_settings_mf_page_switcher';
	},
	
	Build: function(container)
	{
		var obj = this;
		this._mainCont = CreateChild(container, 'div');
		this._mainCont.className = 'wm_hide';

		tbl = CreateChild(container, 'table');
		tbl.className = 'wm_hide';
		this._infoTbl = tbl;
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.className = 'wm_secondary_info';
		td.innerHTML = Lang.InfoDeleteNotEmptyFolders;
		WebMail.LangChanger.Register('innerHTML', td, 'InfoDeleteNotEmptyFolders', '');
		
		tbl = CreateChild(container, 'table');
		tbl.className = 'wm_hide';
		this._buttonsTbl = tbl;
		var tr = tbl.insertRow(0);
		tr.className = '';
		var td = tr.insertCell(0);
		td.className = 'wm_delete_button';
		var inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.AddNewFolder]]);
		WebMail.LangChanger.Register('value', inp, 'AddNewFolder', '');
		inp.onclick = function () { obj.AddNewFolder(); };
		var span = CreateChild(td, 'span'); span.innerHTML = '&nbsp;';
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.DeleteSelected]]);
		WebMail.LangChanger.Register('value', inp, 'DeleteSelected', '');
		inp.onclick = function () { obj.DeleteSelected(); };
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.Save]]);
		WebMail.LangChanger.Register('value', inp, 'Save', '');
		inp.onclick = function () { obj.SaveChanges(); };

		var div = CreateChild(container, 'div');
		div.className = 'wm_hide';
		this._newFolderDiv = div;

		tbl = CreateChild(div, 'table');
		tbl.className = 'wm_settings_part_info';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.innerHTML = Lang.NewFolder;
		WebMail.LangChanger.Register('innerHTML', td, 'NewFolder', '');

		tbl = CreateChild(div, 'table');
		tbl.className = 'wm_settings_new_folder';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = Lang.ParentFolder + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'ParentFolder', ':');
		td = tr.insertCell(1);
		var sel = CreateChild(td, 'select');
		this._foldersSelObj = sel;
		var opt = CreateChildWithAttrs(sel, 'option', [['value', '0']]);
		opt.innerHTML = Lang.NoParent;
		this._noParentObj = inp;

		td = tr.insertCell(2);
		this._onMailServerTd = td;
		td.className = 'wm_settings_on_mailserver';
		td.rowSpan = 2;
		inp = CreateChildWithAttrs(td, 'input', [['type', 'radio'], ['class', 'wm_checkbox'], ['id', 'on_mail_server'], ['name', 'on_mail_server']]);
		inp.checked = true;
		this._onMailServerObj = inp;
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'on_mail_server']]);
		lbl.innerHTML = Lang.OnMailServer;
		WebMail.LangChanger.Register('innerHTML', lbl, 'OnMailServer', '');
		var br = CreateChild(td, 'br');
		inp = CreateChildWithAttrs(td, 'input', [['type', 'radio'], ['class', 'wm_checkbox'], ['id', 'in_webmail'], ['name', 'on_mail_server']]);
		this._inWebMailObj = inp;
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'in_webmail']]);
		lbl.innerHTML = Lang.InWebMail;
		WebMail.LangChanger.Register('innerHTML', lbl, 'InWebMail', '');

		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = Lang.FolderName + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'FolderName', '');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'text'], ['class', 'wm_input'], ['maxlength', '30']]);
		this._nameObj = inp;

		tbl = CreateChild(div, 'table');
		tbl.className = 'wm_settings_buttons';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.OK]]);
		WebMail.LangChanger.Register('value', inp, 'OK', '');
		inp.onclick = function () { obj.SaveNewFolder(); };
		CreateTextChild(td, ' ');
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.Cancel]]);
		WebMail.LangChanger.Register('value', inp, 'Cancel', '');
		inp.onclick = function () { obj.CloseNewFolder(); };
	}//Build
};