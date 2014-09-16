/*
 * Classes:
 * 	CUserSettingsScreen
 */

function CreateRemoveClickFunc(id)
{
	return function () { if (confirm(Lang.ConfirmDeleteAccount)) { RemoveAccountHandle(id); }; return false; };
}

function CreateAccountClickFunc(id)
{
	return function () {
		SetHistoryHandler(
			{
				ScreenId: SCREEN_USER_SETTINGS,
				SelectIdAcct: id,
				Entity: PART_ACCOUNT_PROPERTIES,
				NewMode: false
			}
		);
		return false;
	}
}

function CUserSettingsScreen(skinName)
{
	this.Id = SCREEN_USER_SETTINGS;
	this.isBuilded = false;
	this.hasCopyright = true;
	this.BodyAutoOverflow = true;
	this.HistoryArgs = null;

	this._skinName = skinName;
	this._addAccountClass = 'wm_hide';
	this._allowChangeSettings = true;
	this._allowContacts = true;
	this._allowCalendar = true;

	this._idAcct = -1;
	
	this.Settings = null;
	this.NewSettings = this.Settings;

	this.Accounts = new CAccounts();
	this.AccountProperties = [];
	this.Signatures = [];
	this.Folders = [];

	this._settings = null;
	
	this._mainContainer = null;
	this._nav = null;
	this._cont = null;
	this._addAccountTbl = null;

	this._calendarSettingsDiv = null;
	this._contactsSettingsDiv = null;
	this._accountsSettingsDiv = null;
	this._commonSettingsDiv = null;

	this._menuCommonImg = null;
	this._menuAccountsImg = null;
	this._menuContactsImg = null;
	this._menuCalendarImg = null;
	
	this._commonSettingsObj = new CCommonSettingsScreenPart(skinName);
	this._accountPropertiesObj = new CAccountPropertiesScreenPart();
	this._filtersObj = new CFiltersScreenPart();
	this._signatureObj = new CSignatureScreenPart();
	this._manageFoldersObj = new CManageFoldersScreenPart(skinName);
	this._accountsListObj = new CAccountsListScreenPart(skinName, this._manageFoldersObj);
	this._contactsSettingsObj = new CContactsSettingsScreenPart();
	this._calendarSettingsObj = new CCalendarSettingsScreenPart();
	this._currPart = this._commonSettingsObj;

	this._manageFoldersSwitcher = null;
	this._signatureSwitcher = null;
	this._filtersSwitcher = null;
	this._propertiesSwitcher = null;

	this.SwitcherMode = 0;
	this._newMode = false;

	this._defAccountCount = 1;
}

CUserSettingsScreen.prototype = {
	PlaceData: function(Data)
	{
		if (Data) {
			var Type = Data.Type;
			switch (Type) {
				case TYPE_ACCOUNTS_LIST:
					if (this.Accounts.Count > 0 && Data.Count > this.Accounts.Count) {
						WebMail.ShowReport(Lang.ReportAccountCreatedSuccessfuly);
					};
					this.Accounts = Data;
					this._defAccountCount = this._accountsListObj.SetAccounts(Data);
					this._accountPropertiesObj.SetDefAccountCount(this._defAccountCount);
					if (this._accountsListObj.shown)
						this.ShowSettingsSwitcher(this.SwitcherMode);
					if (Data.HasAccount(this._idAcct) && !this._newMode) {
						this.ChangeAccountId(this._idAcct, true);
					}
					else {
						var flag = !(-1 == this._idAcct);
						if (-1 != Data.LastId) {
							this.ChangeAccountId(Data.LastId, true);
						}
						else {
							this.ChangeAccountId(Data.CurrId, true);
						}
					};
					break;
				case TYPE_FOLDERS_LIST:
				    this.Folders[Data.IdAcct] = Data;
					this._filtersObj.FillFolders(Data.Folders);
					this._manageFoldersObj.UpdateFolders(Data);
					break;
				case TYPE_USER_SETTINGS:
					this.Settings = Data;
					this._commonSettingsObj.SetSettings(Data);
					break;
				case TYPE_ACCOUNT_PROPERTIES:
					this._newMode = false;
					this.AccountProperties[Data.Id] = Data;
					if (!this._manageFoldersObj.shown) {
					    this._accountPropertiesObj.SetAccountProperties(this.AccountProperties[Data.Id], this._settings);
					    this.ChangeAccountId(Data.Id, false);
					    this._manageFoldersObj.UpdateProtocol(Data.MailProtocol);
					};
					break;
				case TYPE_FILTERS:
					this._filtersObj.SetFilters(Data.Items);
					break;
				case TYPE_X_SPAM:
					this._filtersObj.SetXSpam(Data);
					break;
				case TYPE_SIGNATURE:
					this.Signatures[Data.IdAcct] = Data;
					if (this._idAcct == Data.IdAcct)
						this._signatureObj.SetSignature(Data);
					break;
				case TYPE_CONTACTS_SETTINGS:
					this._contactsSettingsObj.UpdateSettings(Data);
					break;
				case TYPE_CALENDAR_SETTINGS:
					this._calendarSettingsObj.Fill(Data);
					break;
			}//switch
		}
	},
	
	UpdateAccountProperties: function ()
	{
		var isDirectMode = this._accountPropertiesObj.GetNewAccountProperties();
		var acctProp = this._accountPropertiesObj.AccountProperties;

		if (acctProp.DefAcct != this.AccountProperties[acctProp.Id].DefAcct) {
			if (acctProp.DefAcct == true) {
				this._defAccountCount++;
			}
			else {
				this._defAccountCount--;
			};
			this._accountPropertiesObj.SetDefAccountCount(this._defAccountCount);
		};

		this.AccountProperties[acctProp.Id] = acctProp;
		return {IsDirectMode: isDirectMode, AcctId: acctProp.Id};
	},

	GetNewSettings: function ()
	{
		this.Settings = this._commonSettingsObj.GetNewSettings();
		return this.Settings;
	},
	
	GetNewSignature: function ()
	{
		var signature =  this._signatureObj.GetNewSignature();
		this.Signatures[signature.IdAcct] = signature;
		return signature;
	},
	
	DesignModeOn: function ()
	{
		this._signatureObj.DesignModeOn();
	},
	
	SetHtmlEditorField: function (heField)
	{
		this._signatureObj.SetHtmlEditorField(heField);
	},
	
	ClickBody: function(ev)
	{
		this._signatureObj.ClickBody();
	},

	ResizeBody: function(mode)
	{
		if (this.isBuilded) {
			if (this._signatureObj.shown) this._signatureObj.ReplaceHtmlEditorField();
		}
	},
	
	ShowCommonSettings: function ()
	{
		this._commonSettingsDiv.className = 'wm_selected_settings_item';
		this._accountsSettingsDiv.className = '';
		if (this._allowContacts) {
			this._contactsSettingsDiv.className = '';
		}
		else {
			this._contactsSettingsDiv.className = 'wm_hide';
		};
		if (this._allowCalendar) {
			this._calendarSettingsDiv.className = '';
		}
		else {
			this._calendarSettingsDiv.className = 'wm_hide';
		};
		this._settingsSwitcher.className = 'wm_hide';
		this._accountsListObj.Hide();
		this._addAccountTbl.className = 'wm_hide';
		if (this._currPart != this._commonSettingsObj) {
			this._currPart.Hide();
			this._currPart = this._commonSettingsObj;
		};
		this._currPart.Show();
	},
	
	ChangeAccountId: function (id, showProp)
	{
		if (-1 != id) {
			this._idAcct = id;
			if (this.AccountProperties[id]) {
				this._accountsListObj.ChangeIdAcct(id);
				if (showProp) {
					this._accountPropertiesObj.SetAccountProperties(this.AccountProperties[id], this._settings);
				};
				if (this.Signatures[id])
					this._signatureObj.SetSignature(this.Signatures[id]);
			}
			else {
				if (showProp) {
					GetHandler(TYPE_ACCOUNT_PROPERTIES, { IdAcct: id }, [], '');
				}
			};
			if (this._manageFoldersObj.shown) {
				this._manageFoldersObj.Show(id, this._settings);
			}
		}
	},
	
	ShowAccounts: function ()
	{
		if (this._allowChangeSettings) {
			this._commonSettingsDiv.className = '';
			this._accountsSettingsDiv.className = 'wm_selected_settings_item';
			if (this._allowContacts) {
				this._contactsSettingsDiv.className = '';
			}
			else {
				this._contactsSettingsDiv.className = 'wm_hide';
			};
			if (this._allowCalendar) {
				this._calendarSettingsDiv.className = '';
			}
			else {
				this._calendarSettingsDiv.className = 'wm_hide';
			};
			if (this._currPart != this._accountPropertiesObj) {
				this._currPart.Hide();
			};
			this._currPart = this._accountPropertiesObj;
			this._accountsListObj.Show(this._idAcct, this._settings);
			this._addAccountTbl.className = this._addAccountClass;
			if (this._newMode) {
				this._settingsSwitcher.className = 'wm_hide';
				this._accountPropertiesObj.SetAccountProperties(new CAccountProperties(), this._settings);
			}
			else {
				if (this.AccountProperties[this._idAcct]) {
					this._accountPropertiesObj.SetAccountProperties(this.AccountProperties[this._idAcct], this._settings);
				}
				else if (-1 != this._idAcct) {
					GetHandler(TYPE_ACCOUNT_PROPERTIES, { IdAcct: this._idAcct }, [], '');
				};
				this.ShowSettingsSwitcher(0);
			};
			this._accountPropertiesObj.Show(this._settings);
		}
		else {
			this.ShowCommonSettings();
		}
	},//ShowAccounts
	
	ShowFilters: function ()
	{
		this._commonSettingsDiv.className = '';
		this._accountsSettingsDiv.className = 'wm_selected_settings_item';
		if (this._allowContacts) {
			this._contactsSettingsDiv.className = '';
		}
		else {
			this._contactsSettingsDiv.className = 'wm_hide';
		};
		if (this._allowCalendar) {
			this._calendarSettingsDiv.className = '';
		}
		else {
			this._calendarSettingsDiv.className = 'wm_hide';
		};
		this._accountsListObj.Show(this._idAcct, this._settings);
		if (this._currPart != this._filtersObj) {
			this._currPart.Hide();
			this._currPart = this._filtersObj;
			this._addAccountTbl.className = this._addAccountClass;
		};
		this.ShowSettingsSwitcher(1);
		this._currPart.Show(this._idAcct, this.Folders);
	},//ShowFilters

	ShowSignature: function ()
	{
		this._commonSettingsDiv.className = '';
		this._accountsSettingsDiv.className = 'wm_selected_settings_item';
		if (this._allowContacts) {
			this._contactsSettingsDiv.className = '';
		}
		else {
			this._contactsSettingsDiv.className = 'wm_hide';
		};
		if (this._allowCalendar) {
			this._calendarSettingsDiv.className = '';
		}
		else {
			this._calendarSettingsDiv.className = 'wm_hide';
		};
		this._accountsListObj.Show(this._idAcct, this._settings);
		this._addAccountTbl.className = this._addAccountClass;
		if (this._currPart != this._signatureObj) {
			this._currPart.Hide();
			this._currPart = this._signatureObj;
		};
		this.ShowSettingsSwitcher(2);
		this._currPart.Show(this._idAcct, this._settings);
	},//ShowSignature
	
	ShowManageFolders: function ()
	{
		this._commonSettingsDiv.className = '';
		this._accountsSettingsDiv.className = 'wm_selected_settings_item';
		if (this._allowContacts) {
			this._contactsSettingsDiv.className = '';
		}
		else {
			this._contactsSettingsDiv.className = 'wm_hide';
		};
		if (this._allowCalendar) {
			this._calendarSettingsDiv.className = '';
		}
		else {
			this._calendarSettingsDiv.className = 'wm_hide';
		};
		this._accountsListObj.Show(this._idAcct, this._settings);
		this._addAccountTbl.className = this._addAccountClass;
		if (this._currPart != this._manageFoldersObj) {
			this._currPart.Hide();
			this._currPart = this._manageFoldersObj;
		};
		this.ShowSettingsSwitcher(3);
		this._currPart.Show(this._idAcct, this._settings);
	},
	
	ShowContactsSettings: function ()
	{
		this._accountsListObj.Hide();
		this._addAccountTbl.className = 'wm_hide';
		this._commonSettingsDiv.className = '';
		this._accountsSettingsDiv.className = '';
		if (this._allowContacts) {
			this._contactsSettingsDiv.className = 'wm_selected_settings_item';
		}
		else {
			this._contactsSettingsDiv.className = 'wm_hide';
		};
		if (this._allowCalendar) {
			this._calendarSettingsDiv.className = '';
		}
		else {
			this._calendarSettingsDiv.className = 'wm_hide';
		};
		this._settingsSwitcher.className = 'wm_hide';
		if (this._currPart != this._contactsSettingsObj) {
			this._currPart.Hide();
			this._currPart = this._contactsSettingsObj;
		};
		this._currPart.Show();
	},

	ShowCalendarSettings: function ()
	{
		this._accountsListObj.Hide();
		this._addAccountTbl.className = 'wm_hide';
		this._commonSettingsDiv.className = '';
		this._accountsSettingsDiv.className = '';
		if (this._allowContacts) {
			this._contactsSettingsDiv.className = '';
		}
		else {
			this._contactsSettingsDiv.className = 'wm_hide';
		};
		if (this._allowCalendar) {
			this._calendarSettingsDiv.className = 'wm_selected_settings_item';
		}
		else {
			this._calendarSettingsDiv.className = 'wm_hide';
		};
		this._settingsSwitcher.className = 'wm_hide';
		if (this._currPart != this._calendarSettingsObj) {
			this._currPart.Hide();
			this._currPart = this._calendarSettingsObj;
		};
		this._currPart.Show();
	},
	
	ShowSettingsSwitcher: function(mode)
	{
		this.SwitcherMode = mode;
		var obj = this;
		
		this._settingsSwitcher.className = 'wm_settings_accounts_info';

		var div = this._manageFoldersSwitcher;
		div.innerHTML = '';
		if (mode == 3) {
			div.className = 'wm_settings_switcher_select_item';
			div.innerHTML = Lang.ManageFolders;
		}
		else {
			div.className = 'wm_settings_switcher_item';
			var a = CreateChildWithAttrs(div, 'a', [['href', '#']]);
			a.innerHTML = Lang.ManageFolders;
			a.onclick = function () {
				SetHistoryHandler(
					{
						ScreenId: SCREEN_USER_SETTINGS,
						SelectIdAcct: obj._idAcct,
						Entity: PART_MANAGE_FOLDERS,
						SetIdAcct: false,
						NewMode: false
					}
				);
				return false;
			}
		};

		div = this._signatureSwitcher;
		div.innerHTML = '';
		if (mode == 2) {
			div.className = 'wm_settings_switcher_select_item';
			div.innerHTML = Lang.Signature;
		}
		else {
			div.className = 'wm_settings_switcher_item';
			var a = CreateChildWithAttrs(div, 'a', [['href', '#']]);
			a.innerHTML = Lang.Signature;
			a.onclick = function () {
				SetHistoryHandler(
					{
						ScreenId: SCREEN_USER_SETTINGS,
						SelectIdAcct: obj._idAcct,
						Entity: PART_SIGNATURE,
						NewMode: false
					}
				);
				return false;
			}
		};

		div = this._filtersSwitcher;
		div.innerHTML = '';
		if (mode == 1) {
			div.className = 'wm_settings_switcher_select_item';
			div.innerHTML = Lang.Filters;
		}
		else {
			div.className = 'wm_settings_switcher_item';
			var a = CreateChildWithAttrs(div, 'a', [['href', '#']]);
			a.innerHTML = Lang.Filters;
			a.onclick = function () {
				SetHistoryHandler(
					{
						ScreenId: SCREEN_USER_SETTINGS,
						SelectIdAcct: obj._idAcct,
						Entity: PART_FILTERS,
						NewMode: false
					}
				);
				return false;
			}
		};

		div = this._propertiesSwitcher;
		div.innerHTML = '';
		if (!this._allowChangeSettings) {
			div.className = 'wm_hide';
			return;
		}
		if (mode == 0) {
			div.className = 'wm_settings_switcher_select_item';
			div.innerHTML = Lang.Properties;
		}
		else {
			div.className = 'wm_settings_switcher_item';
			var a = CreateChildWithAttrs(div, 'a', [['href', '#']]);
			a.innerHTML = Lang.Properties;
			a.onclick = function () {
				SetHistoryHandler(
					{
						ScreenId: SCREEN_USER_SETTINGS,
						SelectIdAcct: obj._idAcct,
						Entity: PART_ACCOUNT_PROPERTIES,
						NewMode: false
					}
				);
				return false;
			}
		};
	},//ShowSettingsSwitcher

	Show: function (settings, historyArgs)
	{
		this.ParseSettings(settings);
		if (this.isBuilded) {
			this._mainContainer.className = '';
			if (null != historyArgs) {
				this.RestoreFromHistory(historyArgs);
			}
			else {
				this.ShowCommonSettings();
			}
		};
		if (this._accountsListObj.shown)
			this._addAccountTbl.className = this._addAccountClass;
		if (-1 != this._idAcct && !this.Folders[this._idAcct]) {
			GetHandler(TYPE_FOLDERS_LIST, { IdAcct: this._idAcct, Sync: -1 }, [], '');
		}
	},
	
	RestoreFromHistory: function (historyArgs)
	{
		this.HistoryArgs = historyArgs;
		if ('undefined' != historyArgs.SelectIdAcct && null != historyArgs.SelectIdAcct &&
		 -1 != historyArgs.SelectIdAcct && historyArgs.SelectIdAcct != this._idAcct) {
			var showProp = false;
			if (PART_ACCOUNT_PROPERTIES == historyArgs.Entity) {
				showProp = true;
			};
			this.ChangeAccountId(historyArgs.SelectIdAcct, showProp);
		};
		switch (historyArgs.Entity) {
			case PART_COMMON_SETTINGS:
				this.ShowCommonSettings();
			break;
			case PART_ACCOUNT_PROPERTIES:
				this._newMode = historyArgs.NewMode;
				this.ShowAccounts();
			break;
			case PART_FILTERS:
				this.ShowFilters();
			break;
			case PART_SIGNATURE:
				this.ShowSignature();
			break;
			case PART_MANAGE_FOLDERS:
				this.ShowManageFolders();
			break;
			case PART_CONTACTS_SETTINGS:
				this.ShowContactsSettings();
			break;
			case PART_CALENDAR_SETTINGS:
				this.ShowCalendarSettings();
			break;
		}
		if (Browser.Mozilla) {
			var navHeight = this._nav.offsetHeight;
			var contHeight = this._cont.offsetHeight;
			if (navHeight > contHeight) {
				this._cont.style.height = navHeight + 'px';
			}
			else if (navHeight != contHeight) {
				this._nav.style.height = contHeight + 'px';
			};
			this._cont.style.height = 'auto';
			this._nav.style.height = 'auto';
		}
	},
	
	ParseSettings: function (settings)
	{
		this._allowContacts = settings.AllowContacts;
		if (!this._allowContacts) this._contactsSettingsDiv.className = 'wm_hide';
		this._allowCalendar = settings.AllowCalendar;
		if (!this._allowCalendar) this._calendarSettingsDiv.className = 'wm_hide';
		if (settings.AllowAddAccount && settings.AllowChangeSettings)
			this._addAccountClass = 'wm_settings_add_account_button';
		else
			this._addAccountClass = 'wm_hide';
		this._allowChangeSettings = settings.AllowChangeSettings;
		this.ChangeSkin(settings.DefSkin);
		this._settings = settings;
	},

	ChangeSkin: function (newSkin)
	{
		if (this._skinName != newSkin) {
			this._skinName = newSkin;
			if (this.isBuilded) {
				this._menuCommonImg.src = 'skins/' + this._skinName + '/settings/menu_common.gif';
				this._menuAccountsImg.src = 'skins/' + this._skinName + '/settings/menu_accounts.gif';
				this._menuContactsImg.src = 'skins/' + this._skinName + '/settings/menu_contacts.gif';
				this._menuCalendarImg.src = 'skins/' + this._skinName + '/settings/menu_calendar.gif';
				this._manageFoldersObj.ChangeSkin(newSkin);
			}
		}
	},
	
	Hide: function()
	{
		if (this.isBuilded) {
			this._commonSettingsObj.Hide();
			this._accountsListObj.Hide();
			this._contactsSettingsObj.Hide();
			this._calendarSettingsObj.Hide();
			this._accountPropertiesObj.Hide();
			this._filtersObj.Hide();
			this._signatureObj.Hide();
			this._manageFoldersObj.Hide();
			this._settingsSwitcher.className = 'wm_hide';
			this._mainContainer.className = 'wm_hide';
			this._addAccountTbl.className = 'wm_hide';
		}
	},
	
	Build: function(container, accountsBar, popupMenus, settings)
	{
		this._mainContainer = CreateChild(container, 'div');
		this._mainContainer.className = 'wm_hide';
		
		var tbl, tr, td;
		if (Browser.Mozilla) {
			tbl = CreateChild(this._mainContainer, 'div');
			tbl.style.display = 'table';
			tbl.className = 'wm_settings';
			tr = CreateChild(tbl, 'div');
			tr.style.display = 'table-row';
			td = CreateChild(tr, 'div');
			td.style.display = 'table-cell';
			td.style.position = 'relative';
			td.className = 'wm_settings_nav';
		}
		else {
			tbl = CreateChild(this._mainContainer, 'table');
			tbl.className = 'wm_settings';
			tr = tbl.insertRow(0);
			td = tr.insertCell(0);
			td.className = 'wm_settings_nav';
		};
		
		this._nav = td;
		var div = CreateChild(td, 'div');
		div.className = 'wm_selected_settings_item';
		var nobr = CreateChild(div, 'nobr');
		var img = CreateChildWithAttrs(nobr, 'img', [['src', 'skins/' + this._skinName + '/settings/menu_common.gif']]);
		this._menuCommonImg = img;
		var a = CreateChildWithAttrs(nobr, 'a', [['href', '#']]);
		a.onclick = function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_USER_SETTINGS,
					SelectIdAcct: obj._idAcct,
					Entity: PART_COMMON_SETTINGS,
					NewMode: false
				}
			);
			return false;
		};
		a.innerHTML = Lang.Common;
		WebMail.LangChanger.Register('innerHTML', a, 'Common', '');
		this._commonSettingsDiv = div;

		div = CreateChild(td, 'div');
		nobr = CreateChild(div, 'nobr');
		img = CreateChildWithAttrs(nobr, 'img', [['src', 'skins/' + this._skinName + '/settings/menu_accounts.gif']]);
		this._menuAccountsImg = img;
		a = CreateChildWithAttrs(nobr, 'a', [['href', '#']]);
		var obj = this;
		a.onclick = function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_USER_SETTINGS,
					SelectIdAcct: obj._idAcct,
					Entity: PART_ACCOUNT_PROPERTIES,
					NewMode: false
				}
			);
			return false;
		};
		a.innerHTML = Lang.EmailAccounts;
		WebMail.LangChanger.Register('innerHTML', a, 'EmailAccounts', '');
		this._accountsSettingsDiv = div;

		div = CreateChild(td, 'div');
		nobr = CreateChild(div, 'nobr');
		img = CreateChildWithAttrs(nobr, 'img', [['src', 'skins/' + this._skinName + '/settings/menu_contacts.gif']]);
		this._menuContactsImg = img;
		a = CreateChildWithAttrs(nobr, 'a', [['href', '#']]);
		a.onclick = function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_USER_SETTINGS,
					SelectIdAcct: obj._idAcct,
					Entity: PART_CONTACTS_SETTINGS,
					NewMode: false
				}
			);
			return false;
		};
		a.innerHTML = Lang.Contacts;
		WebMail.LangChanger.Register('innerHTML', a, 'Contacts', '');
		this._contactsSettingsDiv = div;

		div = CreateChild(td, 'div');
		nobr = CreateChild(div, 'nobr');
		img = CreateChildWithAttrs(nobr, 'img', [['src', 'skins/' + this._skinName + '/settings/menu_calendar.gif']]);
		this._menuCalendarImg = img;
		a = CreateChildWithAttrs(nobr, 'a', [['href', '#']]);
		a.onclick = function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_USER_SETTINGS,
					SelectIdAcct: obj._idAcct,
					Entity: PART_CALENDAR_SETTINGS,
					NewMode: false
				}
			);
			return false;
		};
		a.innerHTML = Lang.SettingsTabCalendar;
		WebMail.LangChanger.Register('innerHTML', a, 'SettingsTabCalendar', '');
		this._calendarSettingsDiv = div;

		if (Browser.Mozilla) {
			td = CreateChild(tr, 'div');
			td.style.display = 'table-cell';
			td.style.position = 'relative';
		}
		else {
			td = tr.insertCell(1);
		};
		td.className = 'wm_settings_cont';
		this._cont = td;

		this._commonSettingsObj.Build(td);

		this._accountsListObj.Build(td);

		var tbl_ = CreateChild(td, 'table');
		tbl_.className = 'wm_hide';
		this._addAccountTbl = tbl_;
		var tr_ = tbl_.insertRow(0);
		var td_ = tr_.insertCell(0);
		var inp = CreateChildWithAttrs(td_, 'input', [['class', 'wm_button'], ['type', 'button'], ['value', Lang.AddNewAccount]]);
		WebMail.LangChanger.Register('value', inp, 'AddNewAccount', '');
		inp.onclick = function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_USER_SETTINGS,
					SelectIdAcct: obj._idAcct,
					Entity: PART_ACCOUNT_PROPERTIES,
					NewMode: true
				}
			);
		};
		if (WebMail._isDemo) {
			inp.onclick = function () { WebMail.ShowReport(DemoWarning); }
		};

		var div = CreateChild(td, 'div');
		div.className = 'wm_hide';
		var div_ = CreateChild(div, 'div');
		div_.className = 'wm_settings_switcher_indent';
		this._manageFoldersSwitcher = CreateChild(div, 'div');
		this._signatureSwitcher = CreateChild(div, 'div');
		this._filtersSwitcher = CreateChild(div, 'div');
		this._propertiesSwitcher = CreateChild(div, 'div');
		this._settingsSwitcher = div;
		var obj = this;
		this._accountPropertiesObj.Build(td, obj);
		this._filtersObj.Build(td);
		this._signatureObj.Build(td);
		this._manageFoldersObj.Build(td);

		this._contactsSettingsObj.Build(td);
		
		this._calendarSettingsObj.Build(td);

		this.isBuilded = true;
	}//Build
};