/*
Classes:
	CFiltersScreenPart
*/

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
	this._xSpamObj = null;

	this._inboxLangNumber = false;
	this._sentLangNumber = false;
	this._draftsLangNumber = false;
	this._trashLangNumber = false;
}

CFiltersScreenPart.prototype = {
	Show: function(idAcct)
	{
		if (!this.shown) {
			this.shown = true;
			this._filtersTbl.className = 'wm_settings_list';
			this._editFilterTbl.className = 'wm_settings_edit_filter';
			this._xSpamTbl.className = 'wm_settings_filters';
			this._folderObj.className = '';
		}
		if (this._idAcct != idAcct) {
			this._idAcct = idAcct;
			GetHandler(TYPE_FILTERS, { IdAcct: this._idAcct }, [], '');
			GetHandler(TYPE_X_SPAM, { IdAcct: this._idAcct }, [], '');
		}
		if (this._foldersIdAcct != idAcct) {
			this._foldersIdAcct = idAcct;
			GetHandler(TYPE_FOLDERS_LIST, { IdAcct: this._idAcct, Sync: -1 }, [], '');
		}
	},
	
	Hide: function()
	{
		this.shown = false;
		if (WebMail._isDemo)
		{
			this.FillFilterProperties();
			this.FillXSpam();
		}
		else
		{
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
		}
		this.hasXSpamChanges = false;
		this.hasPropChanges = false;
		this._filtersTbl.className = 'wm_hide';
		this._editFilterTbl.className = 'wm_hide';
		this._xSpamTbl.className = 'wm_hide';
		this._folderObj.className = 'wm_hide';
	},
	
	SetFilters: function (filters)
	{
		if (this.isSaveFilters)
		{
			WebMail.ShowReport(Lang.ReportFiltersUpdatedSuccessfuly);
			this.isSaveFilters = false;
		}
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
		if (this.isSaveFilters)
		{
			WebMail.ShowReport(Lang.ReportFiltersUpdatedSuccessfuly);
			this.isSaveFilters = false;
		}
		this._xSpam = xSpam;
		this.FillXSpam();
	},

	FillFolders: function (folders)
	{
		CleanNode(this._folderObj);
		this._folderOpts = Array();
		var iCount = folders.length
		var folder, strIndent, opt, j;
		for (var i=0; i<iCount; i++) {
			folder = folders[i];
			for (j=0, strIndent = ''; j<folder.Level; j++) strIndent += '&nbsp;&nbsp;&nbsp;&nbsp;';
			opt = CreateChild(this._folderObj, 'option');
			switch (folder.Type)
			{
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
			}
			opt.value = folder.Id;
			this._folderOpts[folder.Id] = opt;
		}
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
			}
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
			} else {
				this._changeNewFilterText.innerHTML = Lang.EditFilter;
				this._changeNewFilterButton.value = Lang.Save;
			}
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
	
	SavePropChanges: function ()
	{
		if (WebMail._isDemo)
		{
			WebMail.ShowReport(DemoWarning);
			return;
		}

		var val = new CValidate();
		var filterValue = this._filterObj.value;
		if (val.IsEmpty(filterValue))
		{
			alert(Lang.WarningEmptyFilter);
			return;
		}

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
		if (WebMail._isDemo)
		{
			WebMail.ShowReport(DemoWarning);
			return;
		}

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
		sel.onchange = function () { obj.hasPropChanges = true; }
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
		sel.onchange = function () { obj.hasPropChanges = true; }
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
		inp.onchange = function () { obj.hasPropChanges = true; }
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
		}
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
		sel.onchange = function () { obj.hasPropChanges = true; }
		sel.disabled = true;
		this._folderObj = sel;

		tr = tbl.insertRow(4);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.colSpan = 4;
		var hr = CreateChild(td, 'hr');
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_button'], ['type', 'button'], ['value', Lang.Add]]);
		WebMail.LangChanger.Register('value', inp, 'Add', '');
		inp.onclick = function () { obj.SavePropChanges(); }
		this._changeNewFilterButton = inp;
		
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
		inp.onchange = function () { obj.hasXSpamChanges = true; }
		this._xSpamObj = inp;
		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.colSpan = 4;
		var hr = CreateChild(td, 'hr');
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_button'], ['type', 'button'], ['value', Lang.Apply]]);
		WebMail.LangChanger.Register('value', inp, 'Apply', '');
		inp.onclick = function () { obj.SaveXSpamChanges(); }
	}//Build
}