/*
Classes:
	CContactsSettingsScreenPart
*/

function CContactsSettingsScreenPart()
{
	this._contSettings = new CContactsSettings();
	
	this.hasChanges = false;
	this.isSaveContactsSettings = false;

	this._contactsPerPageObj = null;
	this._whiteListingObj = null;
	this._mainTbl = null;
	this._buttonTbl = null;
}

CContactsSettingsScreenPart.prototype = {
	Show: function()
	{
		this.hasChanges = false;
		this._mainTbl.className = 'wm_settings_common';
		this._buttonTbl.className = 'wm_settings_buttons';
		if (this._contSettings.ContactsPerPage == -1)
			GetHandler(TYPE_CONTACTS_SETTINGS, { }, [], '');
	},//Show
	
	Hide: function()
	{
		if (this.hasChanges)
			if (confirm(Lang.ConfirmSaveContactsSettings))
				this.SaveChanges();
			else
				this.Fill();
		this.hasChanges = false;
		this._mainTbl.className = 'wm_hide';
		this._buttonTbl.className = 'wm_hide';
	},//Hide
	
	UpdateSettings: function (contSettings)
	{
		if (this.isSaveContactsSettings)
		{
			WebMail.ShowReport(Lang.ReportContactsSettingsUpdatedSuccessfuly);
			this.isSaveContactsSettings = false;
		}
		this._contSettings = contSettings;
		this.Fill();
	},//UpdateSettings
	
	Fill: function ()
	{
		var contSettings = this._contSettings;
		this._contactsPerPageObj.value = contSettings.ContactsPerPage;
		if (contSettings.WhiteListing)
			this._whiteListingObj.checked = true;
		else
			this._whiteListingObj.checked = false;
		this.hasChanges = false;
	},//Fill
	
	SaveChanges: function ()
	{
		var val = new CValidate();
		var conPerPageValue = Trim(this._contactsPerPageObj.value);
		if (val.IsEmpty(conPerPageValue) || !val.IsPositiveNumber(conPerPageValue))
		{
			alert(Lang.WarningContactsPerPage);
			return;
		}
		
		var xml = '';
		if (this._whiteListingObj.checked) {
			xml += '<param name="white_listing" value="1"/>';
		} else {
			xml += '<param name="white_listing" value="0"/>';
		}
		xml += '<param name="contacts_per_page" value="' + conPerPageValue + '"/>';
		RequestHandler('update', 'contacts_settings', xml);
		this.hasChanges = false;
		this.isSaveContactsSettings = true;
	},//SaveChanges

	Build: function(container)
	{
		var obj = this;
		
		var tbl = CreateChild(container, 'table');
		tbl.className = 'wm_hide';

		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		var span = CreateChild(td, 'span');
		span.innerHTML = Lang.ContactsPerPage + ':&nbsp;';
		WebMail.LangChanger.Register('innerHTML', span, 'ContactsPerPage', ':&nbsp;');
		var inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_input'], ['type', 'text'], ['size', '2'], ['maxlength', '2']]);
		inp.onchange = function () { obj.hasChanges = true; }
		this._contactsPerPageObj = inp;
		
		tr = tbl.insertRow(1);
		tr.className = 'wm_hide';
		td = tr.insertCell(0);
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['id', 'white_listing']]);
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'white_listing']]);
		lbl.innerHTML = Lang.WhiteList;
		WebMail.LangChanger.Register('innerHTML', lbl, 'WhiteList', '');
		inp.onchange = function () { obj.hasChanges = true; }
		this._whiteListingObj = inp;
		this._mainTbl = tbl;

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
