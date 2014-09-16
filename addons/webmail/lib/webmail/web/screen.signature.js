/*
Classes:
	CSignatureScreenPart
*/

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
		} else {
			this._plainEditorObj.style.height = (height - 1) + 'px';
			this._plainEditorObj.style.width = (width - 2) + 'px';
		}
		this.shown = true;
		if (this._idAcct != idAcct) {
			this._idAcct = idAcct;
			GetHandler(TYPE_SIGNATURE, { IdAcct: this._idAcct }, [], '');
		} else {
			this.Fill();
		}
		if (this._allowDhtmlEditor) {
			this._modeSwitcherCont.className = '';
		} else {
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
			}
			if (!this._allowDhtmlEditor) {
				this._htmlEditorField = null;
			}
		}
	},

	Hide: function()
	{
		this.shown = false;
		if (WebMail._isDemo)
		{
			this.Fill();
		}
		else if (this.hasChanges) {
			if (confirm(Lang.ConfirmSaveSignature)) {
				this.SaveChanges();
			} else {
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
				} else {
					this._htmlEditorField.SetText(signature.Value);
				}
			} else {
				if (signature.isHtml) {
					this._plainEditorObj.value = HtmlDecode(signature.Value.replace(/<br *\/{0,1}>/gi, '\n').replace(/<[^>]*>/g, ''));
				} else {
					this._plainEditorObj.value = signature.Value;
				}
			}
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
		if (WebMail._isDemo)
		{
			WebMail.ShowReport(DemoWarning);
			return;
		}

		var signature = new CSignature();
		if (null != this._htmlEditorField && this._htmlEditorField._htmlMode) {
			signature.isHtml = true;
			signature.Value = this._htmlEditorField.GetText();
		} else {
			signature.isHtml = false;
			signature.Value = this._plainEditorObj.value;
		}
		if (this._opt1Obj.checked) {
			if (this._opt2Obj.checked) {
				signature.Opt = 2;
			} else {
				signature.Opt = 1;
			}
		} else {
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
		child.onmouseover = function () { this.className='wm_toolbar_item_over'; }
		child.onmouseout = function () { this.className='wm_toolbar_item'; }
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
		}
		this._opt1Obj = inp;
		tr = tbl.insertRow(3);
		td = tr.insertCell(0);
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox wm_settings_para'], ['type', 'checkbox'], ['id', 'opt2']]);
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'opt2']]);
		lbl.innerHTML = Lang.DontAddToReplies;
		WebMail.LangChanger.Register('innerHTML', lbl, 'DontAddToReplies', '');
		inp.onchange = function () { obj.hasChanges = true; }
		this._opt2Obj = inp;

		tbl = CreateChild(container, 'table');
		tbl.className = 'wm_hide';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_button'], ['type', 'button'], ['value', Lang.Save]]);
		WebMail.LangChanger.Register('value', inp, 'Save', '');
		inp.onclick = function () {
			obj.SaveChanges();
		}
		this._buttonTbl = tbl;
	}//Build
}
