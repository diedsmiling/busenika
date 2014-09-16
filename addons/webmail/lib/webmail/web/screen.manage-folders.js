/*
Classes:
	CManageFoldersScreenPart
*/

function CManageFoldersScreenPart(skinName)
{
	this._skinName = skinName;
	this._folders = new CFoldersList();
	this.FoldersList = Array();
	this._SEPARATOR = '$#%';
	
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
		if (this.disableCount > 0) this._infoTbl.className = 'wm_settings_info';
		this.CloseNewFolder();
		if (this._idAcct != idAcct) {
			this._idAcct = idAcct;
			GetHandler(TYPE_FOLDERS_LIST, { IdAcct: this._idAcct, Sync: -1 }, [], '');
		} else {
			this.Fill();
		}
	},//Show
	
	Hide: function ()
	{
		this.shown = false;
		if (WebMail._isDemo)
		{
			this.Fill();
		}
		else if (this.hasChanges)
			if (confirm(Lang.ConfirmSavefolders))
				this.SaveChanges();
			else
				this.Fill();
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
//		if (this.hasChanges)
//			if (confirm(Lang.ConfirmAddFolder))
//				this.SaveChanges();
//			else
//				this.Fill();
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
		if (WebMail._isDemo)
		{
			WebMail.ShowReport(DemoWarning);
			this.CloseNewFolder();
			return;
		}

		var val = new CValidate();
		var folderName = this._nameObj.value;
		if (val.IsEmpty(folderName))
		{
			alert(Lang.WarningEmptyFolderName);
			return;
		}
		if (!val.IsCorrectFileName(folderName) || val.HasSpecSymbols(folderName))
		{
			alert(Lang.WarningCorrectFolderName);
			return;
		}

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
		}
		RequestHandler('new', 'folder', xml);
		this.CloseNewFolder();
	},
	
	CloseNewFolder: function ()
	{
		this._newFolderDiv.className = 'wm_hide';
	},
	
	UpdateFolders: function (folders)
	{
		if (this.isSaveFolders)
		{
			WebMail.ShowReport(Lang.ReportFoldersUpdatedSuccessfuly);
			this.isSaveFolders = false;
		}
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
		}
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
				}
	
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
				} else {
					var prevIndex = -1;
				}
				foldLine = new CFolderLine(fold, nameTd, checkInp, nameImg, spanA, nameA, nameInp, syncSel, hideImg, this._skinName, opt, obj, prevIndex, i, upImg, downImg, countTd, sizeTd, this._allowDirectMode);
	
				this.disableCount += foldLine.checkDisable;
				this.FoldersList[i] = foldLine;
				prevFoldIndexes[fold.IdParent] = i;
			}
	
			if (this.disableCount > 0 && this.shown) this._infoTbl.className = 'wm_settings_info';
			else this._infoTbl.className = 'wm_hide';
			this.BuildTotal(tbl, rowIndex, count, size);
			this.hasChanges = false;
	
			this.CloseNewFolder();
		}
	},//Fill
	
	AlertFolders: function()
	{
		var iCount = this.FoldersList.length;
		var str = '';
		for (var i=0; i<iCount; i++) {
			var fold = this.FoldersList[i];
			var prop = fold.GetProperties();
			str += prop.PrevIndex + ', ' + prop.Index + ', ' + prop.NextIndex + ', ' + prop.FldOrder + ', ' + prop.Name + '\n';
		}
		alert(str);
	},
	
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
		}
		var foldPrevIndex = prevProp.PrevIndex;
		var prevFoldPrevIndex = prop.PrevIndex;
		var prevFoldNextIndex = prop.NextIndex;
		
		var i, childFold, childProp;
		var prevChilds = Array();
		for (i=prevIndex+1; i<index; i++) {
			childFold = this.FoldersList[i];
			childProp = childFold.GetProperties();
			prevChilds.push(childProp);
		}
		var prevChCount = prevChilds.length;

		var childs = Array();
		var flag = true;
		var idParent = prop.IdParent;
		var level = prop.Level;
		for (i=index+1; flag; i++) {
			if (i == nextIndex) {
				flag = false;
			} else {
				if (childFold = this.FoldersList[i]) {
					childProp = childFold.GetProperties();
					if (idParent == childProp.IdParent || level >= childProp.Level) {
						flag = false;
					} else {
						childs.push(childProp);
					}
				} else {
					flag = false;
				}
			}
		}
		var chCount = childs.length;
		
		var newIndex = prevIndex + chCount + 1;
		
		prop.PrevIndex = foldPrevIndex
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
		}
		
		prevProp.PrevIndex = prevFoldPrevIndex
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
		}

		if (nextIndex != -1) {
			nextProp.PrevIndex = newIndex;
			this.FoldersList[nextIndex].SetProperties(nextProp);
		}
		this.hasChanges = true;
	},//ChangeFoldersPlaces
	
	SaveChanges: function ()
	{
		if (WebMail._isDemo)
		{
			WebMail.ShowReport(DemoWarning);
			return;
		}

		var nodes = '';
		var count = this.FoldersList.length;
		for (var i=0; i<count; i++) {
			nodes += this.FoldersList[i].GetInXml();
		}
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
		inp.onclick = function () { obj.CheckAll(this.checked); }
		this._checkAllObj = inp;

		td = tr.insertCell(colIndex++);
		if (this._protocol == IMAP4_PROTOCOL) {
			td.style.width = '270px';
		} else {
			td.style.width = (270 + 140) + 'px';
		}
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
		}

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
		} else {
			td.colSpan = 2;
		}
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
		td.className = 'wm_settings_info_cell';
		td.innerHTML = Lang.InfoDeleteNotEmptyFolders;
		
		tbl = CreateChild(container, 'table');
		tbl.className = 'wm_hide';
		this._buttonsTbl = tbl;
		var tr = tbl.insertRow(0);
		tr.className = '';
		var td = tr.insertCell(0);
		td.className = 'wm_delete_button';
		var inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.AddNewFolder]]);
		WebMail.LangChanger.Register('value', inp, 'AddNewFolder', '');
		inp.onclick = function () { obj.AddNewFolder(); }
		var span = CreateChild(td, 'span'); span.innerHTML = '&nbsp;';
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.DeleteSelected]]);
		WebMail.LangChanger.Register('value', inp, 'DeleteSelected', '');
		inp.onclick = function () { obj.DeleteSelected(); }
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.Save]]);
		WebMail.LangChanger.Register('value', inp, 'Save', '');
		inp.onclick = function () { obj.SaveChanges(); }

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
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.Cancel]]);
		WebMail.LangChanger.Register('value', inp, 'Cancel', '');
		inp.onclick = function () { obj.CloseNewFolder(); }
		var span = CreateChild(td, 'span');
		span.innerHTML = '&nbsp;';
		inp = CreateChildWithAttrs(td, 'input', [['type', 'button'], ['class', 'wm_button'], ['value', Lang.OK]]);
		WebMail.LangChanger.Register('value', inp, 'OK', '');
		inp.onclick = function () { obj.SaveNewFolder(); }
	}//Build
}

