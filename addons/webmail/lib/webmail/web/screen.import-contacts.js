/*
Classes:
	CImportContactsScreenPart
*/

function CImportContactsScreenPart(skinName, isClassic)
{
	this._skinName = skinName;
	if (isClassic) {
		this.isClassic = true;
	} else {
		this.isClassic = false;
	}
	this._mainFrm = null;
	this._importFile = null;
	this._fileType1 = null;
	this._fileType2 = null;
}

CImportContactsScreenPart.prototype = {
	Show: function ()
	{
		this._mainFrm.className = 'wm_contacts_view';
	},
	
	Hide: function ()
	{
		this._mainFrm.className = 'wm_hide';
	},
	
	Build: function (container)
	{
		var obj = this;
		
		var importFrame = CreateChildWithAttrs(container, 'iframe', [['src', EmptyHtmlUrl], ['style', 'width:1px; height:1px; border:0px; display:none;'], ['name', 'ImportFrame'], ['id', 'ImportFrame']]);

		var frm = CreateChildWithAttrs(container, 'form', [['action', ImportUrl], ['method', 'post'], ['enctype', 'multipart/form-data'], ['target', 'ImportFrame']]);
		frm.onsubmit = function ()
		{
			var val = new CValidate();
			if (!obj._fileType1.checked && !obj._fileType2.checked )
			{
				alert(Lang.WarningImportFileType);
				return false;
			}
			if (val.IsEmpty(obj._importFile.value))
			{
				alert(Lang.WarningEmptyImportFile);
				return false;
			}
			if (!val.HasFileExtention(obj._importFile.value, 'csv'))
			{
				alert(Lang.WarningCsvExtention);
				return false;
			}
		}
		frm.className = 'wm_hide';
		this._mainFrm = frm;

		var tbl = CreateChild(frm, 'table');
		tbl.className = 'wm_contacts_view';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		var b = CreateChild(td, 'b');
		if (this.isClassic)
		{
			b.innerText = Lang.UseImportTo;
		}
		else
		{
			b.innerHTML = Lang.UseImportTo;
			WebMail.LangChanger.Register('innerHTML', b, 'UseImportTo', '');
		}
		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		this._fileType1 = CreateChildWithAttrs(td, 'input', [['type', 'radio'], ['class', 'wm_checkbox'], ['id', 'file_type_1'], ['name', 'file_type'], ['value', '0']]);
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'file_type_1']]);
		if (this.isClassic)
		{
			lbl.innerText = Lang.Outlook1;
		}
		else
		{
			lbl.innerHTML = Lang.Outlook1;
			WebMail.LangChanger.Register('innerHTML', lbl, 'Outlook1', '');
		}
		var br = CreateChild(td, 'br');
		this._fileType2 = CreateChildWithAttrs(td, 'input', [['type', 'radio'], ['class', 'wm_checkbox'], ['id', 'file_type_2'], ['name', 'file_type'], ['value', '1']]);
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'file_type_2']]);
		if (this.isClassic)
		{
			lbl.innerText = Lang.Outlook2;
		}
		else
		{
			lbl.innerHTML = Lang.Outlook2;
			WebMail.LangChanger.Register('innerHTML', lbl, 'Outlook2', '');
		}
		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		if (this.isClassic)
		{
			td.innerText = Lang.SelectImportFile + ':';
		}
		else
		{
			td.innerHTML = Lang.SelectImportFile + ':';
			WebMail.LangChanger.Register('innerHTML', td, 'SelectImportFile', ':');
		}
		tr = tbl.insertRow(3);
		td = tr.insertCell(0);
		this._importFile = CreateChildWithAttrs(td, 'input', [['type', 'file'], ['class', 'wm_file'], ['size', '30'], ['name', 'fileupload']]);
		
		tbl = CreateChild(frm, 'table');
		tbl.className = 'wm_contacts_view';
		tbl.style.width = '90%';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.style.textAlign = 'right';
		td.style.borderTop = 'solid 1px #8D8C89';
		var inp = CreateChildWithAttrs(td, 'input', [['type', 'submit'], ['class', 'wm_file'], ['value', Lang.Import]]);
		if (!this.isClassic) WebMail.LangChanger.Register('value', inp, 'Import', '');
	}
}

