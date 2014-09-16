/*
 * Classes:
 *  CAccountPropertiesScreenPart
 */

function CAccountPropertiesScreenPart()
{
	this.AccountProperties = null;
	this.NewAccountProperties = null;

	this.hasChanges = false;
	this.hasForAccountsChanges = false;
	this.shown = false;
	this._idAcct = -1;
	this._mainForm = null;
	this._parent = null;
	this._allowDirectMode = true;
	this._directModeIsDefault = false;

	this._useForLoginObj = null;
	this._friendlyNmObj = null;
	this._EmailObj = null;
	this._mailIncHostObj = null;
	this._mailMode0Obj = null;
	this._mailMode1Obj = null;
	this._mailMode2Obj = null;
	this._mailMode3Obj = null;
	this._mailsOnServerDaysObj = null;
	this._mailProtocolSpan = null;
	this._mailProtocolObj = null;
	this._mailModeCont = null;
	this._mailIncPortObj = null;
	this._mailIncLoginObj = null;
	this._mailIncPassObj = null;
	this._mailOutHostObj = null;
	this._mailOutPortObj = null;
	this._mailOutLoginObj = null;
	this._mailOutPassObj = null;
	this._mailOutAuthObj = null;
	this._useFriendlyNmObj = null;
	this._getmailAtLoginObj = null;
	this._pop3InboxSyncTypeObj = null;
	this._pop3InboxSyncTypeCont = null;
	this._directModeOpt = null;
	this._deleteFromDbObj = null;
	this._deleteFromDbCont = null;
	this._btnCancel = null;
	
	this._defAccountCount = 1;
}

CAccountPropertiesScreenPart.prototype = {
	Show: function(settings)
	{
	    this.ParseSettings(settings);
		if (!this.shown) {
			this.shown = true;
			this.hasChanges = false;
			this._mainForm.className = '';
			this.Fill();
		}
	},
	
	ParseSettings: function (settings)
	{
		this._allowDirectMode = settings.AllowDirectMode;
		this._directModeIsDefault = settings.DirectModeIsDefault;
	},
	
	Hide: function()
	{
		this.shown = false;
		if (WebMail._isDemo) {
			this.Fill();
		}
		else if (this.hasChanges)
			if (confirm(Lang.ConfirmSaveAcctProp))
				this.SaveChanges();
		this.hasChanges = false;
		this._mainForm.className = 'wm_hide';
	},
	
	SetAccountProperties: function (acctProp, settings)
	{
	    if (settings) {
	        this.ParseSettings(settings);
	    };
		this.AccountProperties = acctProp;
		if (acctProp.Id == -1 && this._allowDirectMode && this._directModeIsDefault) {
		    this.AccountProperties.InboxSyncType = SYNC_TYPE_DIRECT_MODE;
		};
		this.Fill();
	},
	
	GetNewAccountProperties: function ()
	{
		var inboxSyncType = this.NewAccountProperties.InboxSyncType;
		var isDirectMode = false;
		if (SYNC_TYPE_DIRECT_MODE == inboxSyncType) {
			isDirectMode = true;
		};
		var acctProp = this.NewAccountProperties;
		this.AccountProperties = acctProp;
		if (this.hasForAccountsChanges) {
			GetHandler(TYPE_ACCOUNTS_LIST, { }, [], '');
			this.hasForAccountsChanges = false;
		};
		return isDirectMode;
	},
	
	SetDefAccountCount: function (defAccountCount)
	{
		this._defAccountCount = defAccountCount;
	},
	
	Fill: function ()
	{
		if (this.shown && null != this.AccountProperties) {
			var acctProp = this.AccountProperties;
			this._idAcct = acctProp.Id;
			if (acctProp.DefAcct) {
				this._useForLoginObj.checked = true;
				if (this._defAccountCount < 2) {
					this._useForLoginObj.disabled = true;
				}
				else {
					this._useForLoginObj.disabled = false;
				}
			}
			else {
				this._useForLoginObj.checked = false;
				this._useForLoginObj.disabled = false;
			}
			this._friendlyNmObj.value = HtmlDecode(acctProp.FriendlyNm);
			this._EmailObj.value = HtmlDecode(acctProp.Email);
			this._mailIncHostObj.value = HtmlDecode(acctProp.MailIncHost);
			if (acctProp.Id == -1) {
				this._btnCancel.className = 'wm_button';
				CleanNode(this._mailProtocolObj);
				var pop3Opt = CreateChildWithAttrs(this._mailProtocolObj, 'option', [['value', POP3_PROTOCOL]]);
				pop3Opt.innerHTML = Lang.Pop3;
				pop3Opt.selected = true;
				var imap4Opt = CreateChildWithAttrs(this._mailProtocolObj, 'option', [['value', IMAP4_PROTOCOL]]);
				imap4Opt.innerHTML = Lang.Imap4;
				this._mailProtocolSpan.className = 'wm_hide';
				this._mailProtocolObj.className = '';
				this._mailModeCont.className = '';
				this._pop3InboxSyncTypeCont.className = '';
				this._deleteFromDbCont.className = '';
			}
			else {
				this._btnCancel.className = 'wm_hide';
				if (acctProp.MailProtocol == IMAP4_PROTOCOL) {
					this._mailProtocolSpan.innerHTML = Lang.Imap4;
					this._mailModeCont.className = 'wm_hide';
					this._pop3InboxSyncTypeCont.className = 'wm_hide';
					this._deleteFromDbCont.className = 'wm_hide';
				}
				else {
					this._mailProtocolSpan.innerHTML = Lang.Pop3;
					this._mailModeCont.className = '';
					this._pop3InboxSyncTypeCont.className = '';
					this._deleteFromDbCont.className = '';
				}
				this._mailProtocolSpan.className = '';
				this._mailProtocolObj.className = 'wm_hide';
			}
			this._mailIncPortObj.value = acctProp.MailIncPort;
			this._mailIncLoginObj.value = HtmlDecode(acctProp.MailIncLogin);
			this._mailIncPassObj.value = HtmlDecode(acctProp.MailIncPass);

			this._mailOutHostObj.value = HtmlDecode(acctProp.MailOutHost);
			this._mailOutPortObj.value = acctProp.MailOutPort;
			this._mailOutLoginObj.value = HtmlDecode(acctProp.MailOutLogin);
			this._mailOutPassObj.value = HtmlDecode(acctProp.MailOutPass);
			this._mailOutAuthObj.checked = acctProp.MailOutAuth;
			this._useFriendlyNmObj.checked = acctProp.UseFriendlyNm;
			this._getmailAtLoginObj.checked = acctProp.GetMailAtLogin;
			switch (acctProp.MailMode) {
				case (0):
					this._mailMode0Obj.checked = true;
					this._mailMode1Obj.checked = false;
					this._mailMode2Obj.checked = false;
					this._mailMode3Obj.checked = false;
					break;
				case (1):
					this._mailMode0Obj.checked = false;
					this._mailMode1Obj.checked = true;
					this._mailMode2Obj.checked = false;
					this._mailMode3Obj.checked = false;
					break;
				case (2):
					this._mailMode0Obj.checked = false;
					this._mailMode1Obj.checked = true;
					this._mailMode2Obj.checked = true;
					this._mailMode3Obj.checked = false;
					break;
				case (3):
					this._mailMode0Obj.checked = false;
					this._mailMode1Obj.checked = true;
					this._mailMode2Obj.checked = false;
					this._mailMode3Obj.checked = true;
					break;
				case (4):
					this._mailMode0Obj.checked = false;
					this._mailMode1Obj.checked = true;
					this._mailMode2Obj.checked = true;
					this._mailMode3Obj.checked = true;
					break;
			}
			this._mailsOnServerDaysObj.value = acctProp.MailsOnServerDays;

			var type = acctProp.InboxSyncType;
			CleanNode(this._pop3InboxSyncTypeObj);
			var opt1 = CreateChildWithAttrs(this._pop3InboxSyncTypeObj, 'option', [['value', SYNC_TYPE_NEW_HEADERS]]);
			opt1.innerHTML = Lang.Pop3InboxSyncTypes[SYNC_TYPE_NEW_HEADERS];
			var opt3 = CreateChildWithAttrs(this._pop3InboxSyncTypeObj, 'option', [['value', SYNC_TYPE_NEW_MSGS]]);
			opt3.innerHTML = Lang.Pop3InboxSyncTypes[SYNC_TYPE_NEW_MSGS];
			if (this._allowDirectMode || SYNC_TYPE_DIRECT_MODE == type) {
				var opt5 = CreateChildWithAttrs(this._pop3InboxSyncTypeObj, 'option', [['value', SYNC_TYPE_DIRECT_MODE]]);
				opt5.innerHTML = Lang.Pop3InboxSyncTypes[SYNC_TYPE_DIRECT_MODE];
				this._directModeOpt = opt5;
			}

			switch (type) {
				case SYNC_TYPE_NEW_HEADERS:
					opt1.selected = true;
					this._deleteFromDbObj.checked = false;
					break;
				case SYNC_TYPE_ALL_HEADERS:
					opt1.selected = true;
					this._deleteFromDbObj.checked = true;
					type = SYNC_TYPE_NEW_HEADERS;
					break;
				case SYNC_TYPE_NEW_MSGS:
					opt3.selected = true;
					this._deleteFromDbObj.checked = false;
					break;
				case SYNC_TYPE_ALL_MSGS:
					opt3.selected = true;
					this._deleteFromDbObj.checked = true;
					type = SYNC_TYPE_NEW_MSGS;
					break;
				case SYNC_TYPE_DIRECT_MODE:
					opt5.selected = true;
					break;
			}
			this.SetDisabling(type);

			if (acctProp.MailProtocol == IMAP4_PROTOCOL) {
				this._pop3InboxSyncTypeCont.className = 'wm_hide';
			}
			else {
				this._pop3InboxSyncTypeCont.className = '';
			}
			this.hasChanges = false;
		}
	},//Fill
	
	SetDisabling: function (type)
	{
		this._mailMode0Obj.disabled = true;
		this._mailMode1Obj.disabled = true;
		this._mailMode2Obj.disabled = true;
		this._mailMode3Obj.disabled = true;
		this._mailsOnServerDaysObj.disabled = true;
		this._deleteFromDbObj.disabled = true;
		if (type == SYNC_TYPE_NEW_HEADERS || type == SYNC_TYPE_NEW_MSGS) {
			if (type == SYNC_TYPE_NEW_MSGS) {
				this._mailMode0Obj.disabled = false;
			}
			else {
				this._mailMode1Obj.checked = true;
			}
			this._mailMode1Obj.disabled = false;
			if (this._mailMode0Obj.checked || this._mailMode2Obj.checked) {
				this._deleteFromDbObj.checked = false;
			}
			else {
				this._deleteFromDbObj.disabled = false;
			}
			if (this._mailMode1Obj.checked) {
				this._mailMode2Obj.disabled = false;
				this._mailMode3Obj.disabled = false;
				if (this._mailMode2Obj.checked) {
					this._mailsOnServerDaysObj.disabled = false;
				}
			}
		}
		else {
			this._deleteFromDbObj.checked = false;
		}
	},
	
	SaveChanges: function ()
	{
		if (WebMail._isDemo)
		{
			WebMail.ShowReport(DemoWarning);
			return;
		}

		/* validation */
		var val = new CValidate();
		var emailValue = Trim(this._EmailObj.value);
		if (val.IsEmpty(emailValue)) {
			alert(Lang.WarningEmailFieldBlank);
			return;
		}
		if (!val.IsCorrectEmail(emailValue)) {
			alert(Lang.WarningCorrectEmail);
			return;
		}
		
		var incHostValue = Trim(this._mailIncHostObj.value);
		if (val.IsEmpty(incHostValue)) {
			alert(Lang.WarningIncServerBlank);
			return;
		}
		if (!val.IsCorrectServerName(incHostValue)) {
			alert(Lang.WarningCorrectIncServer);
			return;
		}
		
		var incPortValue = Trim(this._mailIncPortObj.value);
		if (val.IsEmpty(incPortValue)) {
			alert(Lang.WarningIncPortBlank);
			return;
		}
		if (!val.IsPort(incPortValue)) {
			alert(Lang.WarningIncPortNumber + Lang.DefaultIncPortNumber);
			return;
		}

		var incLoginValue = Trim(this._mailIncLoginObj.value);
		if (val.IsEmpty(incLoginValue)) {
			alert(Lang.WarningLoginFieldBlank);
			return;
		}

		var outHostValue = Trim(this._mailOutHostObj.value);
		if (val.IsEmpty(outHostValue)) {
			alert(Lang.WarningOutServerBlank);
			return;
		}
		if (!val.IsCorrectServerName(outHostValue)) {
			alert(Lang.WarningCorrectSMTPServer);
			return;
		}
		
		var outPortValue = Trim(this._mailOutPortObj.value);
		if (val.IsEmpty(outPortValue)) {
			alert(Lang.WarningOutPortBlank);
			return;
		}
		if (!val.IsPort(outPortValue)) {
			alert(Lang.WarningOutPortNumber + Lang.DefaultOutPortNumber);
			return;
		}

		var incPassValue = this._mailIncPassObj.value;
		if (incPassValue.length == 0) {
			alert(Lang.WarningIncPassBlank);
			return;
		}

		var mailsOnServerDaysValue = Trim(this._mailsOnServerDaysObj.value);
		if (val.IsEmpty(mailsOnServerDaysValue) || !val.IsPositiveNumber(mailsOnServerDaysValue)) {
			alert(Lang.WarningMailsOnServerDays);
			return;
		}

		/* saving */
		var acctProp = this.AccountProperties;
		var newAcctProp = new CAccountProperties();

		newAcctProp.Email = emailValue;
		newAcctProp.MailIncHost = incHostValue;
		newAcctProp.MailIncPort = incPortValue - 0;
		newAcctProp.MailIncLogin = incLoginValue;
		newAcctProp.MailOutPort = outPortValue - 0;
		newAcctProp.MailIncPass = incPassValue;

		newAcctProp.Id = acctProp.Id;
		newAcctProp.DefAcct = this._useForLoginObj.checked;
		if (-1 == acctProp.Id) newAcctProp.MailProtocol = this._mailProtocolObj.value - 0;
		else newAcctProp.MailProtocol = acctProp.MailProtocol;
		newAcctProp.MailOutAuth = this._mailOutAuthObj.checked;
		newAcctProp.UseFriendlyNm = this._useFriendlyNmObj.checked;
		if (this._mailMode1Obj.checked && this._mailMode2Obj.checked) {
			newAcctProp.MailsOnServerDays = mailsOnServerDaysValue - 0;
		}
		else {
			newAcctProp.MailsOnServerDays = acctProp.MailsOnServerDays;
		}
		if (this._mailMode0Obj.checked) {
			newAcctProp.MailMode = 0;
		}
		else {
			if (this._mailMode2Obj.checked && this._mailMode3Obj.checked) {
				newAcctProp.MailMode = 4;
			}
			else if (this._mailMode3Obj.checked) {
				newAcctProp.MailMode = 3;
			}
			else if (this._mailMode2Obj.checked) {
				newAcctProp.MailMode = 2;
			}
			else {
				newAcctProp.MailMode = 1;
			}
		}
		newAcctProp.GetMailAtLogin = this._getmailAtLoginObj.checked;
		if (acctProp.MailProtocol != IMAP4_PROTOCOL) {
			var value = this._pop3InboxSyncTypeObj.value - 0;
			switch (value) {
				case SYNC_TYPE_NEW_HEADERS:
					if (this._deleteFromDbObj.checked) value = SYNC_TYPE_ALL_HEADERS;
					break;
				case SYNC_TYPE_NEW_MSGS:
					if (this._deleteFromDbObj.checked) value = SYNC_TYPE_ALL_MSGS;
					break;
			}
			newAcctProp.InboxSyncType = value;
		}

		newAcctProp.FriendlyNm = this._friendlyNmObj.value;
		newAcctProp.MailOutHost = outHostValue;
		newAcctProp.MailOutLogin = this._mailOutLoginObj.value;
		newAcctProp.MailOutPass = this._mailOutPassObj.value;

		this.NewAccountProperties = newAcctProp;
		var xml = newAcctProp.GetInXML();
		if (-1 == newAcctProp.Id) {
			RequestHandler('new', 'account', xml);
		}
		else {
			RequestHandler('update', 'account', xml);
		}
		this.hasChanges = false;
	},

	Build: function(container, parent)
	{
		this._parent = parent;
		var obj = this;
		this._mainForm = CreateChild(container, 'form');
		this._mainForm.onsubmit = function () { return false; };
		this._mainForm.className = 'wm_hide';
		var tbl = CreateChild(this._mainForm, 'table');
		tbl.className = 'wm_settings_properties';

		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.colSpan = 3;
		var inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['id', 'def_acct'], ['value', '1']]);
		var lbl = CreateChildWithAttrs(td, 'label', [['for', 'def_acct']]);
		lbl.innerHTML = Lang.UseForLogin;
		WebMail.LangChanger.Register('innerHTML', lbl, 'UseForLogin', '');
		this._useForLoginObj = inp;
		this._useForLoginObj.onchange = function () { obj.hasChanges = true; };

		tr = tbl.insertRow(1);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = Lang.MailFriendlyName + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'MailFriendlyName', ':');
		td = tr.insertCell(1);
		td.colSpan = 2;
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_input wm_settings_input'], ['type', 'text'], ['maxlength', '65']]);
		inp.onchange = function () { obj.hasChanges = true; obj.hasForAccountsChanges = true; };
		this._friendlyNmObj = inp;
		
		tr = tbl.insertRow(2);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = '* ' + Lang.MailEmail + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'MailEmail', ':', '* ');
		td = tr.insertCell(1);
		td.colSpan = 2;
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_input wm_settings_input'], ['type', 'text'], ['maxlength', '255']]);
		inp.onchange = function () { obj.hasChanges = true; obj.hasForAccountsChanges = true; };
		this._EmailObj = inp;

		tr = tbl.insertRow(3);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = '* ' + Lang.MailIncHost + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'MailIncHost', ':', '* ');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_input'], ['type', 'text'], ['maxlength', '255']]);
		inp.onchange = function () { obj.hasChanges = true; };
		this._mailIncHostObj = inp;
		var span = CreateChild(td, 'span');
		span.innerHTML = '&nbsp;';
		var sel = CreateChild(td, 'select');
		sel.className = 'wm_hide';
		sel.onchange = function () {
			if (this.value - 0 == IMAP4_PROTOCOL) {
				obj._pop3InboxSyncTypeCont.className = 'wm_hide';
				obj._mailModeCont.className = 'wm_hide';
				obj._deleteFromDbCont.className = 'wm_hide';
				obj._mailIncPortObj.value = IMAP4_PORT;
			}
			else {
				obj._pop3InboxSyncTypeCont.className = '';
				obj._mailModeCont.className = '';
				obj._deleteFromDbCont.className = '';
				obj._mailIncPortObj.value = POP3_PORT;
			};
			obj.hasChanges = true;
		};
		this._mailProtocolObj = sel;
		span = CreateChild(td, 'span');
		span.className = 'wm_hide';
		this._mailProtocolSpan = span;
		td = tr.insertCell(2);
		span = CreateChild(td, 'span');
		span.innerHTML = '* ' + Lang.MailIncPort + ':';
		WebMail.LangChanger.Register('innerHTML', span, 'MailIncPort', ':', '* ');
		this._mailIncPortObj = CreateChildWithAttrs(td, 'input', [['class', 'wm_input wm_port_input'], ['type', 'text'], ['size', '3'], ['maxlength', '5']]);

		tr = tbl.insertRow(4);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = '* ' + Lang.MailIncLogin + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'MailIncLogin', ':', '* ');
		td = tr.insertCell(1);
		td.colSpan = 2;
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_input wm_settings_input'], ['type', 'text'], ['maxlength', '255']]);
		inp.onchange = function () { obj.hasChanges = true; };
		this._mailIncLoginObj = inp;

		tr = tbl.insertRow(5);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = '* ' + Lang.MailIncPass + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'MailIncPass', ':', '* ');
		td = tr.insertCell(1);
		td.colSpan = 2;
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_input wm_settings_input'], ['type', 'password'], ['maxlength', '255']]);
		inp.onchange = function () { obj.hasChanges = true; };
		this._mailIncPassObj = inp;

		tr = tbl.insertRow(6);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = '* ' + Lang.MailOutHost + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'MailOutHost', ':', '*');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_input'], ['type', 'text'], ['maxlength', '255']]);
		inp.onfocus = function () { if (this.value.length == 0) { this.value = obj._mailIncHostObj.value; this.select(); } };
		inp.onchange = function () { obj.hasChanges = true; };
		this._mailOutHostObj = inp;
		td = tr.insertCell(2);
		var span = CreateChild(td, 'span');
		span.innerHTML = '* ' + Lang.MailOutPort + ':';
		WebMail.LangChanger.Register('innerHTML', span, 'MailOutPort', ':', '* ');
		this._mailOutPortObj = CreateChildWithAttrs(td, 'input', [['class', 'wm_input wm_port_input'], ['type', 'text'], ['size', '3'], ['maxlength', '5']]);

		tr = tbl.insertRow(7);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = Lang.MailOutLogin + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'MailOutLogin', ':', '');
		td = tr.insertCell(1);
		td.colSpan = 2;
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_input wm_settings_input'], ['type', 'text'], ['maxlength', '255']]);
		inp.onchange = function () { obj.hasChanges = true; };
		this._mailOutLoginObj = inp;

		tr = tbl.insertRow(8);
		td = tr.insertCell(0);
		td.className = 'wm_settings_title';
		td.innerHTML = Lang.MailOutPass + ':';
		WebMail.LangChanger.Register('innerHTML', td, 'MailOutPass', ':', '');
		td = tr.insertCell(1);
		td.colSpan = 2;
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_input wm_settings_input'], ['type', 'password'], ['maxlength', '255']]);
		inp.onchange = function () { obj.hasChanges = true; };
		this._mailOutPassObj = inp;

		tr = tbl.insertRow(9);
		td = tr.insertCell(0);
		td.colSpan = 3;
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['id', 'mail_out_auth'], ['value', '1']]);
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'mail_out_auth']]);
		lbl.innerHTML = Lang.MailOutAuth1;
		WebMail.LangChanger.Register('innerHTML', lbl, 'MailOutAuth1', '', '');
		var br = CreateChild(td, 'br');
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'mail_out_auth']]);
		lbl.innerHTML = Lang.MailOutAuth2;
		lbl.className = 'wm_secondary_info wm_nextline_info';
		WebMail.LangChanger.Register('innerHTML', lbl, 'MailOutAuth2', '', '');
		inp.onchange = function () { obj.hasChanges = true; };
		this._mailOutAuthObj = inp;

		tr = tbl.insertRow(10);
		td = tr.insertCell(0);
		td.colSpan = 3;
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['id', 'use_friendly_nm'], ['value', '1']]);
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'use_friendly_nm']]);
		lbl.innerHTML = Lang.UseFriendlyNm1;
		WebMail.LangChanger.Register('innerHTML', lbl, 'UseFriendlyNm1', '', '');
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'use_friendly_nm']]);
		lbl.innerHTML = Lang.UseFriendlyNm2;
		lbl.className = 'wm_secondary_info wm_inline_info';
		WebMail.LangChanger.Register('innerHTML', lbl, 'UseFriendlyNm2', '', '');
		inp.onchange = function () { obj.hasChanges = true; obj.hasForAccountsChanges = true; };
		this._useFriendlyNmObj = inp;

		tr = tbl.insertRow(11);
		td = tr.insertCell(0);
		td.colSpan = 3;
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['id', 'getmail_at_login'], ['value', '1']]);
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'getmail_at_login']]);
		lbl.innerHTML = Lang.GetmailAtLogin;
		WebMail.LangChanger.Register('innerHTML', lbl, 'GetmailAtLogin', '', '');
		inp.onchange = function () { obj.hasChanges = true; };
		this._getmailAtLoginObj = inp;

		tr = tbl.insertRow(12);
		tr.className = 'wm_hide';
		td = tr.insertCell(0);
		td.colSpan = 3;
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox'], ['type', 'radio'], ['id', 'mail_mode_0'], ['name', 'mail_mode'], ['value', '1']]);
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'mail_mode_0']]);
		lbl.innerHTML = Lang.MailMode0;
		WebMail.LangChanger.Register('innerHTML', lbl, 'MailMode0', '', '');
		inp.onclick = function () {
			if (this.checked) {
				obj._mailMode2Obj.disabled = true;
				obj._mailMode3Obj.disabled = true;
				obj._mailsOnServerDaysObj.disabled = true;
				obj._deleteFromDbObj.disabled = true;
				obj._deleteFromDbObj.checked = false;
			};
			obj.hasChanges = true;
		};
		this._mailMode0Obj = inp;
		
		var br = CreateChild(td, 'br');
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox'], ['type', 'radio'], ['id', 'mail_mode_1'], ['name', 'mail_mode'], ['value', '1']]);
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'mail_mode_1']]);
		lbl.innerHTML = Lang.MailMode1;
		WebMail.LangChanger.Register('innerHTML', lbl, 'MailMode1', '', '');
		inp.onclick = function () {
			if (this.checked) {
				obj._mailMode2Obj.disabled = false;
				if (obj._mailMode2Obj.checked) {
					obj._mailsOnServerDaysObj.disabled = false;
					obj._deleteFromDbObj.disabled = true;
				}
				else {
					obj._mailsOnServerDaysObj.disabled = true;
					obj._deleteFromDbObj.disabled = false;
				};
				obj._mailMode3Obj.disabled = false;
			};
			obj.hasChanges = true;
		};
		this._mailMode1Obj = inp;
		
		br = CreateChild(td, 'br');
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox wm_settings_para'], ['type', 'checkbox'], ['id', 'mail_mode_2'], ['value', '1']]);
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'mail_mode_2']]);
		lbl.innerHTML = Lang.MailMode2 + ' ';
		WebMail.LangChanger.Register('innerHTML', lbl, 'MailMode2', ' ', '');
		inp.onclick = function () {
			if (this.checked) {
				obj._mailsOnServerDaysObj.disabled = false;
				obj._deleteFromDbObj.disabled = true;
				obj._deleteFromDbObj.checked = false;
			}
			else {
				obj._mailsOnServerDaysObj.disabled = true;
				obj._deleteFromDbObj.disabled = false;
			};
			obj.hasChanges = true;
		};
		this._mailMode2Obj = inp;
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_input'], ['type', 'text'], ['size', '1'], ['maxlength', '6']]);
		span = CreateChild(td, 'span');
		span.innerHTML = ' ' + Lang.MailsOnServerDays;
		WebMail.LangChanger.Register('innerHTML', span, 'MailsOnServerDays', '', ' ');
		inp.onchange = function () { obj.hasChanges = true; };
		this._mailsOnServerDaysObj = inp;
		br = CreateChild(td, 'br');
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox wm_settings_para'], ['type', 'checkbox'], ['id', 'mail_mode_3'], ['value', '1']]);
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'mail_mode_3']]);
		lbl.innerHTML = Lang.MailMode3;
		WebMail.LangChanger.Register('innerHTML', lbl, 'MailMode3', '', '');
		inp.onchange = function () { obj.hasChanges = true; };
		this._mailMode3Obj = inp;
		this._mailModeCont = tr;

		tr = tbl.insertRow(13);
		tr.className = 'wm_hide';
		td = tr.insertCell(0);
		td.colSpan = 3;
		span = CreateChild(td, 'span');
		span.innerHTML = Lang.InboxSyncType + ':&nbsp;';
		WebMail.LangChanger.Register('innerHTML', span, 'InboxSyncType', ':&nbsp;', '');
		sel = CreateChild(td, 'select');
		sel.onchange = function () {
			if (!obj._allowDirectMode && obj._directModeOpt != null) {
				obj._pop3InboxSyncTypeObj.removeChild(obj._directModeOpt);
				obj._directModeOpt = null;
			};
			obj.SetDisabling(this.value - 0);
			obj.hasChanges = true;
		};
		this._pop3InboxSyncTypeObj = sel;
		this._pop3InboxSyncTypeCont = tr;

		tr = tbl.insertRow(14);
		tr.className = 'wm_hide';
		td = tr.insertCell(0);
		td.colSpan = 3;
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_checkbox'], ['type', 'checkbox'], ['id', 'delete_from_db']]);
		lbl = CreateChildWithAttrs(td, 'label', [['for', 'delete_from_db']]);
		lbl.innerHTML = Lang.DeleteFromDb;
		WebMail.LangChanger.Register('innerHTML', lbl, 'DeleteFromDb', '', '');
		inp.onchange = function () { obj.hasChanges = true; };
		this._deleteFromDbObj = inp;
		this._deleteFromDbCont = tr;

		tbl = CreateChild(this._mainForm, 'table');
		tbl.className = 'wm_settings_buttons';
		tr = tbl.insertRow(0);
		td = tr.insertCell(0);
		td.className = 'wm_secondary_info';
		td.innerHTML = Lang.InfoRequiredFields;
		WebMail.LangChanger.Register('innerHTML', td, 'InfoRequiredFields', '');
		td = tr.insertCell(1);
		inp = CreateChildWithAttrs(td, 'input', [['class', 'wm_button'], ['type', 'button'], ['value', Lang.Save]]);
		WebMail.LangChanger.Register('value', inp, 'Save', '', '');
		inp.onclick = function () {
			obj.SaveChanges();
		};
		var span = CreateChild(td, 'span');
		span.innerHTML = ' ';
		span.className = 'wm_hide';
		inp = CreateChildWithAttrs(span, 'input', [['class', 'wm_button'], ['type', 'button'], ['value', Lang.Cancel]]);
		WebMail.LangChanger.Register('value', inp, 'Cancel', '', '');
		inp.onclick = function () {
			SetHistoryHandler(
				{
					ScreenId: SCREEN_USER_SETTINGS,
					SelectIdAcct: obj._idAcct,
					Entity: PART_ACCOUNT_PROPERTIES,
					NewMode: false
				}
			);
		};
		this._btnCancel = span;
	}//Build
};