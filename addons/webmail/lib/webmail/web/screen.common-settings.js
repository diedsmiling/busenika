/*
 * Classes:
 *  CCommonSettingsScreenPart
 *  CContactsSettingsScreenPart
 *  CCalendarSettingsScreenPart
 * Functions:
 *  fnum
 *  getSettingsParametr
 *  String.prototype.parseJSON
 */

function CCommonSettingsScreenPart(skinName)
{
	this._mainForm = null;
	
	this._skinName = skinName;
	this._settings = null;
	this._newSettings = null;
	
	this.hasChanges = false;
	this._shown = false;
	
	this._messPerPageObj = null;;
	this._messPerPageCont = null;
	this._disableRteObj = null;
	this._disableRteCont = null;
	this._skinObj = null;
	this._skinBuilded = false;
	this._skinCont = null;
	this._charsetIncObj = null;
	this._charsetIncBuilded = false;
	this._charsetIncCont = null;
	this._charsetOutObj = null;
	this._charsetOutBuilded = false;
	this._charsetOutCont = null;
	this._timeOffsetObj = null;
	this._timeOffsetBuilded = false;
	this._timeOffsetCont = null;
	this._languageObj = null;
	this._languageBuilded = false;
	this._languageCont = null;
	this._dateFormatObj = null;
	this._dateFormatBuilded = false;
	this._dateFormatCont = null;
	this._dateFormatInp = null;
	this._dateFormatAdvCont = null;
	this._12TimeFormatObj = null;
	this._24TimeFormatObj = null;
	this._TimeFormatCont = null;
	this._viewPaneObj = null;
	this._viewPaneCont = null;
	this._viewPicturesObj = null;
	this._viewPicturesCont = null;

	this._popupDataHelpImg = null;
}

CCommonSettingsScreenPart.prototype = {
	Show: function()
	{
		this.hasChanges = false;
		this._mainForm.className = '';
		this._shown = true;
		if (this._settings == null) {
			GetHandler(TYPE_USER_SETTINGS, { }, [], '');
		}
		else {
			this.Fill();
		}
	},//Show
	
	Hide: function()
	{
		if (this.hasChanges) {
			if (confirm(Lang.ConfirmSaveSettings)) {
				this.SaveChanges();
			}
			else {
				this.Fill();
			}
		};
		this._mainForm.className = 'wm_hide';
		this.hasChanges = false;
		this._shown = false;
	},//Hide
	
	ChangeSkin: function (newSkin)
	{
		if (this._skinName != newSkin) {
			this._skinName = newSkin;
			this._popupDataHelpImg.src = 'skins/' + this._skinName + '/icons/help.gif';
		}
	},
	
	SetSettings: function (settings)
	{
		this._settings = settings;
		this.Fill();
		this.ChangeSkin(settings.DefSkin);
	},
	
	GetNewSettings: function ()
	{
		var showPic = this._settings.ViewMode & VIEW_MODE_SHOW_PICTURES;
		var newShowPic = this._newSettings.ViewMode & VIEW_MODE_SHOW_PICTURES;
		if (showPic != newShowPic && newShowPic == VIEW_MODE_SHOW_PICTURES) {
			WebMail.DataSource.Cache.SetMessageSafety(-1, '', -1, '', 1, true);
		};
		this._settings = this._newSettings;
		this.Fill();
		this.ChangeSkin(this._settings.DefSkin);
		return this._settings;
	},
	
	Fill: function ()
	{
		if (this._shown) {
			this.hasChanges = false;
			var settings = this._settings;
			if (settings.MsgsPerPage != null) {
				this._messPerPageObj.value = settings.MsgsPerPage;
				this._messPerPageCont.className = '';
			}
			else {
				this._messPerPageCont.className = 'wm_hide';
			};
			if (settings.DisableRte != null) {
				this._disableRteObj.checked = settings.DisableRte;
				this._disableRteCont.className = '';
			}
			else {
				this._disableRteCont.className = 'wm_hide';
			};
			if (settings.CharsetInc != null) {
			    if (!this._charsetIncBuilded) {
				    for (var i in Charsets) {
					    var opt = CreateChildWithAttrs(this._charsetIncObj, 'option', [['value', Charsets[i].Value]]);
					    opt.innerHTML = Charsets[i].Name;
				    };
    				this._charsetIncBuilded = true;
				};
				this._charsetIncObj.value = settings.CharsetInc;
				//this._charsetIncCont.className = '';
			}
			else {
				this._charsetIncCont.className = 'wm_hide';
			};
			if (settings.CharsetOut != null) {
			    if (!this._charsetOutBuilded) {
				    for (var i in Charsets) {
					    var opt = CreateChildWithAttrs(this._charsetOutObj, 'option', [['value', Charsets[i].Value]]);
					    opt.innerHTML = Charsets[i].Name;
					    if (Charsets[i].Value == '0') {
					        WebMail.LangChanger.Register('innerHTML', opt, 'CharsetDefault', '');
					    }
				    };
    				this._charsetOutBuilded = true;
				};
				this._charsetOutObj.value = settings.CharsetOut;
				this._charsetOutCont.className = '';
			}
			else {
				this._charsetOutCont.className = 'wm_hide';
			};
			if (settings.TimeOffset != null) {
			    if (!this._timeOffsetBuilded) {
				    for (var i in TimeOffsets) {
					    var opt = CreateChildWithAttrs(this._timeOffsetObj, 'option', [['value', TimeOffsets[i].Value]]);
					    opt.innerHTML = TimeOffsets[i].Name;
					    if (TimeOffsets[i].Value == '0') {
					        WebMail.LangChanger.Register('innerHTML', opt, 'TimeDefault', '');
					    }
				    };
    				this._timeOffsetBuilded = true;
				};
				this._timeOffsetObj.value = settings.TimeOffset;
				this._timeOffsetCont.className = '';
			}
			else {
				this._timeOffsetCont.className = 'wm_hide';
			};
			if (settings.ViewMode != null) {
				this._viewPaneObj.checked = settings.ViewMode & VIEW_MODE_WITH_PANE;
				this._viewPicturesObj.checked = settings.ViewMode & VIEW_MODE_SHOW_PICTURES;
				this._viewPaneCont.className = '';
				this._viewPicturesCont.className = '';
			}
			else {
				this._viewPaneCont.className = 'wm_hide';
				this._viewPicturesCont.className = 'wm_hide';
			};
			var skins = settings.Skins;
			if (settings.DefSkin != null) {
			    if (!this._skinBuilded) {
				    for (var i in skins) {
					    var opt = CreateChildWithAttrs(this._skinObj, 'option', [['value', skins[i]]]);
					    opt.innerHTML = skins[i];
				    };
    				this._skinBuilded = true;
				};
				this._skinObj.value = settings.DefSkin;
				this._skinCont.className = '';
			}
			else {
				this._skinCont.className = 'wm_hide';
			};
			var langs = settings.Langs;
			if (settings.DefLang != null) {
			    if (!this._languageBuilded) {
				    for (var i in langs) {
					    var opt = CreateChildWithAttrs(this._languageObj, 'option', [['value', langs[i]]]);
					    opt.innerHTML = langs[i];
				    };
    				this._languageBuilded = true;
				};
				this._languageObj.value = settings.DefLang;
				this._languageCont.className = '';
			}
			else {
				this._languageCont.className = 'wm_hide';
			};
			if (settings.DateFormat != null) {
			    this._dateFormatCont.className = '';
			    this._dateFormatAdvCont.className = '';
			    this.SetDateFormat(HtmlDecode(settings.DateFormat));
			}
			else {
	            this._dateFormatCont.className = 'wm_hide';
	            this._dateFormatAdvCont.className = 'wm_hide';
			};
			if (settings.TimeFormat != null) {
			    if (settings.TimeFormat == 0) {
		            this._24TimeFormatObj.checked = true;
		        }
		        else {
		            this._12TimeFormatObj.checked = true;
		        };
		        this._TimeFormatCont.className = '';
		    }
		    else {
		        this._TimeFormatCont.className = 'wm_hide';
		    }
		}
	},//Fill
	
	SetDateFormat: function (dateFormat)
	{
        if (!this._dateFormatBuilded) {
	        var sel = this._dateFormatObj;
	        var day = this.GetDate();
	        var opt = CreateChildWithAttrs(sel, 'option', [['value', 'mm/dd/yy']]);
	        opt.innerHTML = day.Month + '/' + day.Date + '/' + day.Year;
	        opt = CreateChildWithAttrs(sel, 'option', [['value', 'dd/mm/yy']]);
	        opt.innerHTML = day.Date + '/' + day.Month + '/' + day.Year;
	        opt = CreateChildWithAttrs(sel, 'option', [['value', 'dd month']]);
	        opt.innerHTML = day.Date + ' ' + day.WordMonth;
	        opt = CreateChildWithAttrs(sel, 'option', [['value', 'advanced']]);
	        opt.innerHTML = Lang.DateAdvanced;
	        WebMail.LangChanger.Register('innerHTML', opt, 'DateAdvanced', '');
	        this._dateFormatBuilded = true;
	    };
	    dateFormat = dateFormat.toLowerCase();
		if (dateFormat == 'default') dateFormat = 'mm/dd/yy';
	    if (dateFormat == 'mm/dd/yy' || dateFormat == 'dd/mm/yy' || dateFormat == 'dd month') {
	        this._dateFormatObj.value = dateFormat;
            this._dateFormatInp.value = dateFormat;
	    }
	    else {
	        this._dateFormatObj.value = 'advanced';
	        if ('advanced' != dateFormat) {
	            this._dateFormatInp.value = dateFormat;
	        }
	    }
	},
	
	GetDate: function ()
	{
	    var today = new Date();
	    var year = today.getFullYear().toString().substring(2);
	    var date = today.getDate();
	    var month = today.getMonth() + 1;
	    if (date == month) date = 21;
	    var wordMonth = 'Jan';
	    switch (month) {
	        case 2: wordMonth = 'Feb'; break;
	        case 3: wordMonth = 'Mar'; break;
	        case 4: wordMonth = 'Apr'; break;
	        case 5: wordMonth = 'May'; break;
	        case 6: wordMonth = 'Jun'; break;
	        case 7: wordMonth = 'Jul'; break;
	        case 8: wordMonth = 'Aug'; break;
	        case 9: wordMonth = 'Sep'; break;
	        case 10: wordMonth = 'Oct'; break;
	        case 11: wordMonth = 'Nov'; break;
	        case 12: wordMonth = 'Dec'; break;
	    };
	    if (date < 10) date = '0' + date;
	    else date = date.toString();
	    if (month < 10) month = '0' + month;
	    else month = month.toString();
	    return {Date: date, Month: month, WordMonth: wordMonth, Year: year};
	},
	
	SaveChanges: function ()
	{
		var val = new CValidate();
		var messPerPageValue = Trim(this._messPerPageObj.value);
		if (val.IsEmpty(messPerPageValue) || !val.IsPositiveNumber(messPerPageValue)) {
			alert(Lang.WarningMessagesPerPage);
			return;
		};
		var advValue = this._dateFormatInp.value;
		if (val.IsEmpty(Trim(advValue))) {
			alert(Lang.WarningAdvancedDateFormat);
			return;
		};
		var settings = this._settings;
		var newSettings = new CSettings();
		var warning = false;
		if (settings.MsgsPerPage != null) {
			newSettings.MsgsPerPage = messPerPageValue - 0;
		};
		if (settings.DisableRte != null) {
			newSettings.DisableRte = this._disableRteObj.checked;
		};
		if (settings.CharsetInc != null) {
			newSettings.CharsetInc = this._charsetIncObj.value - 0;
		};
		if (settings.CharsetOut != null) {
			newSettings.CharsetOut = this._charsetOutObj.value - 0;
		};
		if (settings.TimeOffset != null) {
			newSettings.TimeOffset = this._timeOffsetObj.value - 0;
		};
		if (settings.ViewMode != null) {
			newSettings.ViewMode = this._viewPaneObj.checked * VIEW_MODE_WITH_PANE | this._viewPicturesObj.checked * VIEW_MODE_SHOW_PICTURES;
		};
		if (settings.DefSkin != null) {
			newSettings.Skins = settings.Skins;
			newSettings.DefSkin = this._skinObj.value;
		};
		if (settings.DefLang != null) {
			newSettings.Langs = settings.Langs;
			newSettings.DefLang = this._languageObj.value;
		};
		if (settings.TimeFormat != null) {
			newSettings.TimeFormat = (this._12TimeFormatObj.checked) ? 1 : 0;
		};
		if (settings.DateFormat != null) {
		    newSettings.DateFormat = advValue;
		};
		var xml = newSettings.GetInXML();
		RequestHandler('update', 'settings', xml);

		this._newSettings = newSettings;
		this.hasChanges = false;
	},//SaveChanges

	Build: function(container)
	{
		var obj = this;
		this._mainForm = CreateChild(container, 'form');
		this._mainForm.onsubmit = function () { return false; };
		this._mainForm.className = 'wm_hide';
		var tbl_ = CreateChild(this._mainForm, 'table');
		tbl_.className = 'wm_settings_common';

		var tr_ = tbl_.insertRow(0);
		tr_.className = 'wm_hide';
		var td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.MsgsPerPage + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'MsgsPerPage', ':');
		td_ = tr_.insertCell(1);
		td_.colSpan = 2;
		this._messPerPageObj = CreateChildWithAttrs(td_, 'input', [['class', 'wm_input'], ['type', 'text'], ['size', '2'], ['maxlength', '2']]);
		this._messPerPageObj.onchange = function () { obj.hasChanges = true; };
		this._messPerPageCont = tr_;
		
		tr_ = tbl_.insertRow(1);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_ = tr_.insertCell(1);
		td_.colSpan = 2;
		var inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['id', 'disable_rte'], ['value', '1']]);
		var lbl = CreateChildWithAttrs(td_, 'label', [['for', 'disable_rte']]);
		lbl.innerHTML = Lang.DisableRTE;
		WebMail.LangChanger.Register('innerHTML', lbl, 'DisableRTE', '');
		this._disableRteObj = inp;
		this._disableRteObj.onchange = function () { obj.hasChanges = true; };
		this._disableRteCont = tr_;

		tr_ = tbl_.insertRow(2);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.Skin + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'Skin', ':');
		td_ = tr_.insertCell(1);
		td_.colSpan = 2;
		var sel = CreateChild(td_, 'select');
		this._skinObj = sel;
		this._skinObj.onchange = function () { obj.hasChanges = true; };
		this._skinCont = tr_;

		tr_ = tbl_.insertRow(3);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.DefCharsetInc + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'DefCharsetInc', ':');
		td_ = tr_.insertCell(1);
		td_.colSpan = 2;
		sel = CreateChild(td_, 'select');
		this._charsetIncObj = sel;
		this._charsetIncObj.onchange = function () { obj.hasChanges = true; };
		this._charsetIncCont = tr_;

		tr_ = tbl_.insertRow(4);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.DefCharset + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'DefCharset', ':');
		td_ = tr_.insertCell(1);
		td_.colSpan = 2;
		sel = CreateChild(td_, 'select');
		this._charsetOutObj = sel;
		this._charsetOutObj.onchange = function () { obj.hasChanges = true; };
		this._charsetOutCont = tr_;

		tr_ = tbl_.insertRow(5);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.DefTimeOffset + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'DefTimeOffset', ':');
		td_ = tr_.insertCell(1);
		td_.colSpan = 2;
		sel = CreateChild(td_, 'select');
		this._timeOffsetObj = sel;
		this._timeOffsetObj.onchange = function () { obj.hasChanges = true; };
		this._timeOffsetCont = tr_;

		tr_ = tbl_.insertRow(6);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.DefLanguage + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'DefLanguage', ':');
		td_ = tr_.insertCell(1);
		td_.colSpan = 2;
		sel = CreateChild(td_, 'select');
		this._languageObj = sel;
		this._languageObj.onchange = function () { obj.hasChanges = true; };
		this._languageCont = tr_;

		var rowIndex = 7;
		tr_ = tbl_.insertRow(rowIndex++);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.DefTimeFormat + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'DefTimeFormat', ':');
		td_ = tr_.insertCell(1);
		inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_checkbox'], ['type', 'radio'], ['name', 'time_format'], ['id', 'time_format_12'], ['value', '1']]);
		inp.onchange = function () { obj.hasChanges = true; };
		var lbl = CreateChildWithAttrs(td_, 'label', [['for', 'time_format_12']]);
		lbl.innerHTML = '1PM';
		this._12TimeFormatObj = inp;
		var span = CreateChild(td_, span);
		span.innerHTML = '&nbsp&nbsp';
		inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_checkbox'], ['type', 'radio'], ['name', 'time_format'], ['id', 'time_format_24'], ['value', '0']]);
		inp.onchange = function () { obj.hasChanges = true; };
		lbl = CreateChildWithAttrs(td_, 'label', [['for', 'time_format_24']]);
		lbl.innerHTML = '13:00';
		this._24TimeFormatObj = inp;
		this._TimeFormatCont = tr_;

		tr_ = tbl_.insertRow(rowIndex++);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.DefDateFormat + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'DefDateFormat', ':');
		td_ = tr_.insertCell(1);
		td_.colSpan = 2;
		sel = CreateChild(td_, 'select');
	    sel.onchange = function () {
	        obj.SetDateFormat(this.value);
	        obj.hasChanges = true;
	    };
		this._dateFormatObj = sel;
		this._dateFormatCont = tr_;

		tr_ = tbl_.insertRow(rowIndex++);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.DateAdvanced + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'DateAdvanced', ':');
		td_ = tr_.insertCell(1);
		td_.colSpan = 2;
		var inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_input'], ['type', 'text'], ['maxlength', '20']]);
	    inp.onchange = function () {
	        obj.SetDateFormat(this.value);
	        obj.hasChanges = true;
	    };
		this._dateFormatInp = inp;
		img = CreateChildWithAttrs(td_, 'img', [['class', 'wm_settings_help'], ['src', 'skins/' + this._skinName + '/icons/help.gif']]);
		img.onclick = PopupDataHelp;
		this._popupDataHelpImg = img;
		this._dateFormatAdvCont = tr_;

		tr_ = tbl_.insertRow(rowIndex++);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_ = tr_.insertCell(1);
		td_.colSpan = 2;
		var inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['id', 'view_pane']]);
		lbl = CreateChildWithAttrs(td_, 'label', [['for', 'view_pane']]);
		lbl.innerHTML = Lang.ShowViewPane;
		WebMail.LangChanger.Register('innerHTML', lbl, 'ShowViewPane', '');
		inp.onchange = function () {
			obj.hasChanges = true;
		};
		this._viewPaneObj = inp;
		this._viewPaneCont = tr_;

		tr_ = tbl_.insertRow(rowIndex++);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_ = tr_.insertCell(1);
		td_.colSpan = 2;
		var inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['id', 'view_pictures']]);
		var lbl = CreateChildWithAttrs(td_, 'label', [['for', 'view_pictures']]);
		lbl.innerHTML = Lang.AlwaysShowPictures;
		WebMail.LangChanger.Register('innerHTML', lbl, 'AlwaysShowPictures', '');
		inp.onchange = function ()  {
			obj.hasChanges = true;
		};
		this._viewPicturesObj = inp;
		this._viewPicturesCont = tr_;

		tbl_ = CreateChild(this._mainForm, 'table');
		tbl_.className = 'wm_settings_buttons';
		tr_ = tbl_.insertRow(0);
		td_ = tr_.insertCell(0);
		inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_button'], ['type', 'button'], ['value', Lang.Save]]);
		WebMail.LangChanger.Register('value', inp, 'Save', '');
		inp.onclick = function () {
			obj.SaveChanges();
		}
	}//Build
};

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
		if (this.isSaveContactsSettings) {
			WebMail.ShowReport(Lang.ReportContactsSettingsUpdatedSuccessfuly);
			this.isSaveContactsSettings = false;
		};
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
		if (val.IsEmpty(conPerPageValue) || !val.IsPositiveNumber(conPerPageValue)) {
			alert(Lang.WarningContactsPerPage);
			return;
		};
		
		var xml = '';
		if (this._whiteListingObj.checked) {
			xml += '<param name="white_listing" value="1"/>';
		}
		else {
			xml += '<param name="white_listing" value="0"/>';
		};
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
		inp.onchange = function () { obj.hasChanges = true; };
		this._contactsPerPageObj = inp;
		
		tr = tbl.insertRow(1);
		tr.className = 'wm_hide';
		td = tr.insertCell(0);
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['id', 'white_listing']]);
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'white_listing']]);
		lbl.innerHTML = Lang.WhiteList;
		WebMail.LangChanger.Register('innerHTML', lbl, 'WhiteList', '');
		inp.onchange = function () { obj.hasChanges = true; };
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
		};
		this._buttonTbl = tbl;
	}//Build
};

var setcache = null;

function fnum(num, digits)
{
	num = String(num);
	while (num.length < digits) {
		num = '0' + num;
	};
	return(num);
}

function getSettingsParametr()
{
	WebMail.ShowInfo(Lang.InfoLoading);
	var scache = new Array();
	var res = '{}';
	var netLoader = new CNetLoader();
	var req = netLoader.GetTransport();
	var url = CalendarProcessingUrl + '?action=get_settings&nocache=' + Math.random();
	if (req != null) {
	  req.open('GET', url, false);
	  req.send(null);
	  res = req.responseText;
	};
	WebMail.HideInfo();
	var setparams;
	setparams = res.parseJSON();
	if (setparams == false) {
		return null;
	};
	for(i in setparams) { 
		setval = setparams[i]; 
		if (typeof(setval) == 'function') continue;
		scache[i] = setval;
	};
	return scache;
}

/*
 * Based on json.js (2007-07-03)
 * Modified by AfterLogic Corporation
 */
String.prototype.parseJSON = function (filter) {
    var j;

    function walk(k, v) {
        var i;
        if (v && typeof v === 'object') {
            for (i in v) {
                if (v.hasOwnProperty(i)) {
                    v[i] = walk(i, v[i]);
                }
            }
        };
        return filter(k, v);
    }

    if (/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]+$/.test(this.
            replace(/\\./g, '@').
            replace(/"[^"\\\n\r]*"/g, ''))) {

        j = eval('(' + this + ')');
        if (typeof filter === 'function') {
            j = walk('', j);
        };
    	if (j['error'] == 'true') {
    		WebMail.ShowError(j['description']);
    		return false;
    	};
        return j;
    };

    WebMail.ShowError(Lang.ErrorGeneral);
    return false;
};


function CCalendarSettingsScreenPart()
{
	this._mainForm = null;
	this._buttonsTbl = null;
	
	this.hasChanges = false;
	
	this._timeFormat		= Array();
	this._defTimeFormatCont	= null;
	this._defDateFormat		= null;
	this._defDateFormatCont	= null;
	this._defDateFormatBuilded = false;
	this._showWeekends		= null;
	this._showWeekendsCont	= null;
	this._WorkdayStarts		= null;
	this._WorkdayEnds		= null;
	this._WorkdayCont		= null;
	this._ShowWorkday		= null;
	this._ShowWorkdayCont	= null;
	this._tabCont			= null;
	this._tab 				= Array();	
	this._Country			= null;
	this._CountryCont		= null;
	this._UserTimeZone		= null;
	this._UserTimeZoneCont	= null;
	this._AllTimeZones		= null;
	this._AllTimeZonesCont	= null;
	this._CalncelBtn 		= null;
	this._SaveBtn			= null;
	this.defTimeZone		= null;
	this.settingsTimeZone	= null;
	this._displayName		= null;
	this._displayNameCont	= null;
	this._weekStartsOn		= null;
	this._weekStartsOnCont	= null;
	this._weekStartsOnBuilded = false;

	this._tabs = [
		{ NameField: 'TabDay', Value: '1',  Id: 'set_tab_0'},
		{ NameField: 'TabWeek', Value: '2', Id: 'set_tab_1'},
		{ NameField: 'TabMonth', Value: '3', Id: 'set_tab_2'}
		];
	
	var d = new Date();
	
	var MonField = 'ShortMonthJanuary'; //month
	switch (d.getMonth()+1) {
		case 1: MonField = 'ShortMonthJanuary'; break;
		case 2: MonField = 'ShortMonthFebruary'; break;
		case 3: MonField = 'ShortMonthMarch'; break;
		case 4: MonField = 'ShortMonthApril'; break;
		case 5: MonField = 'ShortMonthMay'; break;
		case 6: MonField = 'ShortMonthJune'; break;
		case 7: MonField = 'ShortMonthJuly'; break;
		case 8: MonField = 'ShortMonthAugust'; break;
		case 9: MonField = 'ShortMonthSeptember'; break;
		case 10: MonField = 'ShortMonthOctober'; break;
		case 11: MonField = 'ShortMonthNovember'; break;
		case 12: MonField = 'ShortMonthDecember'; break;
	};
	
	this._dayFormat = [
		{Name: fnum((d.getMonth()+1),2)+"/"+fnum(d.getDate(),2)+"/"+d.getFullYear(), Value: '1', Id: 'date_0'},
		{Name: fnum(d.getDate(),2)+"/"+fnum((d.getMonth()+1),2)+"/"+d.getFullYear(), Value: '2', Id: 'date_1'},
		{Name: d.getFullYear()+"-"+fnum((d.getMonth()+1),2)+"-"+fnum(d.getDate(),2), Value: '3', Id: 'date_2'},
		{Name: null, NameField: MonField, NameBefore: '', NameAfter: ' ' + d.getDate() + ', ' + d.getFullYear(), Value: '4', Id: 'date_3'},
		{Name: null, NameField: MonField, NameBefore: d.getDate() + ' ', NameAfter: ' ' + d.getFullYear(), Value: '5', Id: 'date_4'}
		];
	
	this._firstWeekDay = [
		{NameField: 'FullDaySunday', Value: '0', Id: 'first_week_day_0'},
		{NameField: 'FullDayMonday', Value: '1', Id: 'first_week_day_1'}
		];
}

CCalendarSettingsScreenPart.prototype =
{
	Show: function ()
	{
		this._mainForm.className = '';
		this._buttonsTbl.className = 'wm_settings_buttons';
		if (setcache == null) {
			setcache = getSettingsParametr();
		};
		this.Fill();
	},
	
	Hide: function ()
	{
		if (this.hasChanges) {
			if (confirm(Lang.ConfirmSaveSettings)) {
				this.SaveChanges();
			}
			else {
				this.Fill();
			}
		};
		this._mainForm.className = 'wm_hide';
		this._buttonsTbl.className = 'wm_hide';
		this.hasChanges = false;
	},
	
	SetTimeFormat: function(WorkdayContainer, WorkdayValue, TimeFormat)
	{
		var k = 0;
		var hour = "";
		var time = "";
		
		for (i=WorkdayContainer.options.length-1; i>=0; i--) {
			WorkdayContainer.options[i] = null;
		};

		for (var i=0; i<24; i++) {
			opt = document.createElement("option");
			if (TimeFormat == 1) {
				if (i==12) k=0;
				if (k==0) hour=12;
				else hour=k;
						
				time = hour + ((i<12) ? " AM" : " PM");
					
				k++;
			}
			else if (TimeFormat == 2) {
				time = (i<10) ? ("0"+i+":00") : (i+":00");
			};
					
			opt.value = i;
			WorkdayContainer.appendChild(opt);
			opt.text = time;
		};
		setTimeout( function(){WorkdayContainer.options[WorkdayValue].selected=true;}, 1);
	},
	
	LoadTimeZones: function(TimeZoneCont, allTimeZones)
	{
		CleanNode(TimeZoneCont);
        var _defTimeZoneVal = "<select id='defTimeZone' style='width: 300px;' name='defTimeZone'>";
        var i = "", index;
		var tmp_zone = "";
        var code = this._Country.value;
        if(allTimeZones==1) {

            if(this.defTimeZone != null) {
				this.settingsTimeZone = this.defTimeZone.options[this.defTimeZone.selectedIndex].value;
			};
			for(i in timeZoneForCountry[code]) {
				if (timeZoneForCountry[code][i] == this.settingsTimeZone) {
					tmp_zone = this.settingsTimeZone;
					break;
				}
				else {
					tmp_zone = timeZoneForCountry[code][0];
				};
			};
			this.settingsTimeZone = tmp_zone;
		    TimeZoneCont.innerHTML = allTimeZone;
            this.defTimeZone = document.getElementById("defTimeZone");
            this.defTimeZone.selectedIndex = parseInt(this.settingsTimeZone, 10) - 1;
        }
        else {
            for(i in timeZoneForCountry[code]) {
                index = timeZoneForCountry[code][i];
                var timeZoneValue = AllTimeZonesArr[index];
				if (typeof(index)=="function") continue;

				if (this.settingsTimeZone == index) {
                    _defTimeZoneVal += "<option value='" + index + "' selected='selected'>" + timeZoneValue + "</option>\r\n";
                }
                else {
                    _defTimeZoneVal += "<option value='" + index + "'>" + timeZoneValue + "</option>\r\n";
                }
            };
            _defTimeZoneVal += "</select>";
			TimeZoneCont.innerHTML = _defTimeZoneVal;
            this.defTimeZone = document.getElementById("defTimeZone");
			this.settingsTimeZone = this.defTimeZone.options[this.defTimeZone.selectedIndex].value;
        }
	},

	Fill: function ()
	{
			if (setcache == null) return;
			this.hasChanges = false;
			
			if (setcache['timeformat'] != null) {
				for ( var i in this._timeFormat) {
					var tfObj = this._timeFormat[i];
					if (setcache['timeformat'] == tfObj.Value) {
						tfObj.Obj.checked = true;
					}
				}
				this._defTimeFormatCont.className = "";
			}
			else {
				this._defTimeFormatCont.className = "wm_hide";
			};


			if (setcache['dateformat'] != null) {
				var sel = this._defDateFormat;
				if (!this._defDateFormatBuilded) {
					for(var i=0; i<this._dayFormat.length; i++) {
						var dayFormat = this._dayFormat[i];
						var opt = CreateChildWithAttrs(sel, 'option', [['value', dayFormat.Value]]);
						if (dayFormat.Name != null) {
							opt.innerHTML = dayFormat.Name;
						}
						else {
							opt.innerHTML = dayFormat.NameBefore + Lang[dayFormat.NameField] + dayFormat.NameAfter;
							WebMail.LangChanger.Register('innerHTML', opt, dayFormat.NameField, dayFormat.NameAfter, dayFormat.NameBefore);
						}
					};
					this._defDateFormatBuilded = true;
				};
				this._defDateFormat.value = setcache['dateformat'];
				this._defDateFormatCont.className = '';
			}
			else {
				this._defDateFormatCont.className = 'wm_hide';
			};
			
			if (setcache['showweekends'] != null) {
				if (setcache['showweekends'] == 1) this._showWeekends.checked = true;
				this._showWeekendsCont.className = '';
			}
			else {
				this._showWeekendsCont.className = 'wm_hide';
			};
			
			if (setcache['workdaystarts'] != null) {
				this.SetTimeFormat(this._WorkdayStarts, setcache['workdaystarts'], setcache['timeformat']);
				if (setcache['workdayends'] != null) this.SetTimeFormat(this._WorkdayEnds, setcache['workdayends'], setcache['timeformat']);
				this._WorkdayCont.className = '';
			}
			else {
				this._WorkdayCont.className = 'wm_hide';
			};
				
			if (setcache['showworkday'] != null) {
				if (setcache['showworkday'] == 1) this._ShowWorkday.checked = true;
				this._ShowWorkdayCont.className = '';
			}
			else {
				this._ShowWorkdayCont.className = 'wm_hide';
			};

			if (setcache['weekstartson'] != null) {
				var sel = this._weekStartsOn;
				if (!this._weekStartsOnBuilded) {
					for(var i=0; i<this._firstWeekDay.length; i++) {
						var opt = CreateChildWithAttrs(sel, 'option', [['value', this._firstWeekDay[i].Value]]);
						opt.innerHTML = Lang[this._firstWeekDay[i].NameField];
						WebMail.LangChanger.Register('innerHTML', opt, this._firstWeekDay[i].NameField, '', ' ');
					};
					this._weekStartsOnBuilded = true;
				};
				this._weekStartsOn.value = setcache['weekstartson'];
				this._weekStartsOnCont.className = '';				
			}
			else {
				this._weekStartsOnCont.className = 'wm_hide';
			};

			if (setcache['defaulttab'] != null)	 {
				for ( var i=0; i<this._tab.length; i++) {
					var tabObj = this._tab[i];
					if (setcache['defaulttab'] == tabObj.Value) {
						tabObj.Obj.checked = true;
					}
				};
				this._tabCont.className = "";
			}
			else {
				this._tabCont.className = "wm_hide";
			};

			if (setcache['country'] != null) {
				CleanNode(this._Country);
				for (var i=0; i<Countries.length; i++) {
					var opt = CreateChildWithAttrs(this._Country, 'option', [['value', Countries[i].Value]]);
					if (Countries[i].Value == setcache['country'])
						opt.selected = true;
					opt.innerHTML = Countries[i].Name;
				};
				this._CountryCont.className = '';
			}
			else {
				this._CountryCont.className = 'wm_hide';
			};
			
			if (setcache['timezone'] != null && setcache['alltimezones'] != null) {
				this.settingsTimeZone = setcache['timezone'];
				CleanNode(this._UserTimeZoneTd);
				this.LoadTimeZones(this._UserTimeZoneTd, setcache['alltimezones'], setcache['timezone']);
				this._UserTimeZoneCont.className = '';
			}
			else {
				this._UserTimeZoneCont.className = 'wm_hide';
			};	
			
			if (setcache['alltimezones'] != null) {
				if (setcache['alltimezones'] == 1) this._AllTimeZones.checked = true;
				this._AllTimeZonesCont.className = '';
			}
			else {
				this._AllTimeZonesCont.className = 'wm_hide';
			};
			
			if (setcache['displayname'] != null) {
				this._displayName.value = setcache['displayname'];
				this._displayNameCont.className = '';
			}
			else {
				this._displayNameCont.className = 'wm_hide';
			};
	},//Fill
	
	SaveChanges: function() {
		
		//_timeFormat
		var _timeFormat = 1;
		for (var i=0; i<this._timeFormat.length; i++) {
			var tfObj = this._timeFormat[i];
			if (tfObj.Obj.checked) {
				_timeFormat = tfObj.Value;
			}
		};
		
		//_showWeekends
		var _showWeekends = (this._showWeekends.checked == true) ? 1 : 0;
		
		//_ShowWorkday
		var _ShowWorkday = (this._ShowWorkday.checked == true) ? 1 : 0;
		
		//_AllTimeZones
		var _AllTimeZones = (this._AllTimeZones.checked == true) ? 1 : 0;
		
		//defTab
		var _defTab = 1;
		for (var i=0; i < this._tab.length; i++) {
			if (this._tab[i].Obj.checked) {
				_defTab = this._tab[i].Value;
			}
		};
			
		var _defTimeZone = 0;
		if (this.defTimeZone != null) {
			_defTimeZone = this.defTimeZone.value;
		};
		var _displayName = encodeURIComponent(Trim(this._displayName.value));

		var str = "&timeFormat="+_timeFormat+
		"&dateFormat="+this._defDateFormat.value+
		"&showWeekends="+_showWeekends+
		"&workdayStarts="+this._WorkdayStarts.value+
		"&WorkdayEnds="+this._WorkdayEnds.value+
		"&showWorkday="+_ShowWorkday+
		"&weekstartson="+this._weekStartsOn.value+
		"&tab="+_defTab+
		"&country="+this._Country.value+
		"&TimeZone="+_defTimeZone+
		"&AllTimeZones="+_AllTimeZones+
		"&displayName="+_displayName;

		var netLoader = new CNetLoader();
		var req = netLoader.GetTransport();
		var url = CalendarProcessingUrl+'?action=update_settings'+ str + '&nocache=' + Math.random();
		var res = '{}';
		if (req != null) {
			WebMail.ShowInfo(Lang.InfoSaving);
			req.open("GET", url, false);
			req.send(null);
			var res = req.responseText;
		};
		
		var settingsFromDb;
		settingsFromDb = res.parseJSON();
		if (settingsFromDb == false) {
			WebMail.HideInfo();
			return;
		};
		for (i in settingsFromDb) { 
			setval = settingsFromDb[i]; 
			if (typeof(setval) == 'function') continue;
			setcache[i] = settingsFromDb[i];
		};
		WebMail.HideInfo();
		WebMail.ShowReport(Lang.ReportSettingsUpdated);
		if (this.hasChanges) {
			var screen = WebMail.Screens[SCREEN_CALENDAR];
			if (screen) screen.NeedReload();
			this.hasChanges = false;
		}
	},
	
	Build: function(container)
	{
		var obj = this;
		this._mainForm = CreateChild(container, 'form');
		this._mainForm.onsubmit = function () { return false; };
		var tbl_ = CreateChild(this._mainForm, 'table');
		tbl_.className = 'wm_settings_common';

		var tr_ = tbl_.insertRow(0);
		tr_.className = 'wm_hide';
		var td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.SettingsDisplayName + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'SettingsDisplayName', ':');
		td_ = tr_.insertCell(1);
		var inp = CreateChildWithAttrs(td_, 'input', [['type', 'text'], ['name', 'DisplayName'], ['id', 'DisplayName'], ['value', ''], ['maxlength', '255']]);
		inp.onchange = function () {
			obj.hasChanges = true;
		};
		this._displayName = inp;
		this._displayNameCont = tr_;
		this._displayName.onblur = function() {
			obj._displayName.value = Trim(obj._displayName.value);
		};

		tr_ = tbl_.insertRow(1);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.SettingsTimeFormat + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'SettingsTimeFormat', ':');
		td_ = tr_.insertCell(1);
		this._timeFormat = Array();
		inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_checkbox'], ['type', 'radio'], ['name', 'defTimeFormat'], ['id', '_defTimeFormat_0'], ['value', '1']]);
		inp.onchange = function () { obj.hasChanges = true; };
		inp.onclick = function() {
			obj.SetTimeFormat(obj._WorkdayStarts, obj._WorkdayStarts.value, 1);
		 	obj.SetTimeFormat(obj._WorkdayEnds, obj._WorkdayEnds.value, 1);	
		};
		var lbl = CreateChildWithAttrs(td_, 'label', [['for', '_defTimeFormat_0']]);
		lbl.innerHTML = '1PM&nbsp;&nbsp;&nbsp;';
		this._timeFormat.push({Obj: inp, Value:1});
		inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_checkbox'], ['type', 'radio'], ['name', 'defTimeFormat'], ['id', '_defTimeFormat_1'], ['value', '2']]);
		inp.onchange = function () { obj.hasChanges = true; };
		inp.onclick = function() {
			obj.SetTimeFormat(obj._WorkdayStarts, obj._WorkdayStarts.value, 2);
		 	obj.SetTimeFormat(obj._WorkdayEnds, obj._WorkdayEnds.value, 2);	
		};
		var lbl = CreateChildWithAttrs(td_, 'label', [['for', '_defTimeFormat_1']]);
		lbl.innerHTML = '13:00';
		this._timeFormat.push({Obj: inp, Value:2});
		this._defTimeFormatCont = tr_;
		
		tr_ = tbl_.insertRow(2);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.SettingsDateFormat + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'SettingsDateFormat', ':');
		td_ = tr_.insertCell(1);
		sel = CreateChild(td_, 'select');
		this._defDateFormat = sel;
		this._defDateFormat.onchange = function () { obj.hasChanges = true; };
		this._defDateFormatCont = tr_;
		
		tr_ = tbl_.insertRow(3);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_ = tr_.insertCell(1);
		var inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['name', 'showWeekends'], ['id', 'showWeekends'], ['value', '1']]);
		this._showWeekends = inp;
		this._showWeekends.onchange = function () { obj.hasChanges = true; };
		var lbl = CreateChildWithAttrs(td_, 'label', [['for', 'showWeekends']]);
		lbl.innerHTML = Lang.SettingsShowWeekends;
		WebMail.LangChanger.Register('innerHTML', lbl, 'SettingsShowWeekends', '');
		this._showWeekendsCont = tr_;

		tr_ = tbl_.insertRow(4);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.SettingsWorkdayStarts + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'SettingsWorkdayStarts', ':');
		td_ = tr_.insertCell(1);
		sel1 = CreateChild(td_, 'select');
		sel1.style.width = "100px";
		this._WorkdayStarts = sel1;
		this._WorkdayStarts.onchange = function () { obj.hasChanges = true; };
		var span = CreateChild(td_, 'span');
		span.innerHTML = '&nbsp;&nbsp;' + Lang.SettingsWorkdayEnds + ': ';
		WebMail.LangChanger.Register('innerHTML', span, 'SettingsWorkdayEnds', ': ', '&nbsp;&nbsp;');
		sel2 = CreateChild(td_, 'select');
		sel2.style.width = "100px";
		this._WorkdayEnds = sel2;
		this._WorkdayEnds.onchange = function () { obj.hasChanges = true; };
		this._WorkdayCont = tr_;
		
		tr_ = tbl_.insertRow(5);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_ = tr_.insertCell(1);
		var inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['name', 'showWorkday'], ['id', 'showWorkday'], ['value', '1']]);
		var lbl = CreateChildWithAttrs(td_, 'label', [['for', 'showWorkday']]);
		lbl.innerHTML = Lang.SettingsShowWorkday;
		WebMail.LangChanger.Register('innerHTML', lbl, 'SettingsShowWorkday', '');
		inp.onchange = function () {
			obj.hasChanges = true;
		};
		this._ShowWorkday = inp;
		this._ShowWorkdayCont = tr_;
		
		
		tr_ = tbl_.insertRow(6);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.SettingsWeekStartsOn + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'SettingsWeekStartsOn', ':');
		td_ = tr_.insertCell(1);
		sel = CreateChild(td_, 'select');
		this._weekStartsOn = sel;
		this._weekStartsOn.onchange = function () { obj.hasChanges = true; };
		this._weekStartsOnCont = tr_;
		

		tr_ = tbl_.insertRow(7);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.SettingsDefaultTab + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'SettingsDefaultTab', ':');
		td_ = tr_.insertCell(1);
		this._tab = Array();
		for (var i=0; i<this._tabs.length; i++) {
			inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_checkbox'], ['type', 'radio'], ['name', 'defTab'], ['id', this._tabs[i].Id], ['value', this._tabs[i].Value]]);
			inp.onchange = function () { obj.hasChanges = true; };
			var lbl = CreateChildWithAttrs(td_, 'label', [['for', this._tabs[i].Id]]);
			lbl.innerHTML = Lang[this._tabs[i].NameField] + '&nbsp;&nbsp;&nbsp;';
			WebMail.LangChanger.Register('innerHTML', lbl, this._tabs[i].NameField, '&nbsp;&nbsp;&nbsp;', '');
			this._tab.push({Obj: inp, Value: this._tabs[i].Value});
		};
		this._tabCont = tr_;

		tr_ = tbl_.insertRow(8);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.SettingsCountry + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'SettingsCountry', ':');
		td_ = tr_.insertCell(1);
		sel = CreateChild(td_, 'select');
		sel.style.width = "300px";
		this._Country = sel;
		this._Country.onchange = function () {
			obj.hasChanges = true; 
			/*reload timezones when change country*/	
			var allZones = (obj._AllTimeZones.checked)?1:0;
			obj.LoadTimeZones(obj._UserTimeZoneTd, allZones);
		};
		this._CountryCont = tr_;
		
		tr_ = tbl_.insertRow(9);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_.className = 'wm_settings_title';
		td_.innerHTML = Lang.SettingsTimeZone + ':';
		WebMail.LangChanger.Register('innerHTML', td_, 'SettingsTimeZone', ':');
		td_ = tr_.insertCell(1);
		this._UserTimeZoneTd = td_;
		this._UserTimeZoneCont = tr_;
		
		tr_ = tbl_.insertRow(10);
		tr_.className = 'wm_hide';
		td_ = tr_.insertCell(0);
		td_ = tr_.insertCell(1);
		var inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['name', '_AllTimeZones'], ['id', 'AllTimeZones'], ['value', '0']]);
		var lbl = CreateChildWithAttrs(td_, 'label', [['for', 'AllTimeZones']]);
		lbl.innerHTML = Lang.SettingsAllTimeZones;
		WebMail.LangChanger.Register('innerHTML', lbl, 'SettingsAllTimeZones', '');
		inp.onchange = function () {
			obj.hasChanges = true;
		};
		inp.onclick = function() {
			var allZones = (this.checked)?1:0;
			obj.LoadTimeZones(obj._UserTimeZoneTd, allZones);
		};
		this._AllTimeZones = inp;
		this._AllTimeZonesCont = tr_;	
	
		tbl_ = CreateChild(this._mainForm, 'table');
		tbl_.className = 'wm_hide';
		tr_ = tbl_.insertRow(0);
		td_ = tr_.insertCell(0);
		
		inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_button'], ['type', 'button'], ['value', Lang.ButtonSave]]);
		WebMail.LangChanger.Register('value', inp, 'ButtonSave', '');
		inp.onclick = function () {
			if (parseInt(obj._WorkdayStarts.value) >= parseInt(obj._WorkdayEnds.value)) {
				alert(Lang.WarningWorkdayStartsEnds)
			}
			else {
				obj.SaveChanges();
			}
		};
		this._SaveBtn = inp;

		this._buttonsTbl = tbl_;
	}//Build
};