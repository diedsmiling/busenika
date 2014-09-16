/*
Classes:
	CSettings
	CAccountProperties
	CFilters
	CFilterProperties
	CXSpam
	CContactsSettings
*/

function CSettings()
{
	this.Type = TYPE_USER_SETTINGS;
	this.MsgsPerPage = null;
	this.DisableRte = null;
	this.CharsetInc = null;
	this.CharsetOut = null;
	this.TimeOffset = null;
	this.ViewMode = null;
	this.DefSkin = null;
	this.Skins = Array();
	this.DefLang = null;
	this.Langs = Array();
	this.DateFormat = null;
}

CSettings.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys
	
	GetInXML: function ()
	{
		var attrs = '';
		if (this.MsgsPerPage != null) {
			attrs += ' msgs_per_page="' + this.MsgsPerPage + '"';
		}
		if (this.DisableRte != null) {
			if (this.DisableRte) {
				attrs += ' allow_dhtml_editor="0"';
			} else {
				attrs += ' allow_dhtml_editor="1"';
			}
		}
		if (this.CharsetInc != null) {
			attrs += ' def_charset_inc="' + this.CharsetInc + '"';
		}
		if (this.CharsetOut != null) {
			attrs += ' def_charset_out="' + this.CharsetOut + '"';
		}
		if (this.TimeOffset != null) {
			attrs += ' def_timezone="' + this.TimeOffset + '"';
		}
		if (this.ViewMode != null) {
			attrs += ' view_mode="' + this.ViewMode + '"';
		}

		var nodes = '';
		if (this.DefSkin != null) {
			nodes += '<def_skin>' + GetCData(this.DefSkin) + '</def_skin>';
		}
		if (this.DefLang != null) {
			nodes += '<def_lang>' + GetCData(this.DefLang) + '</def_lang>';
		}
		if (this.DateFormat != null) {
			nodes += '<def_date_fmt>' + GetCData(this.DateFormat) + '</def_date_fmt>';
		}

		return '<settings' + attrs + '>' + nodes + '</settings>';
	},
	
	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('msgs_per_page');
		if (attr) this.MsgsPerPage = attr - 0;
		attr = RootElement.getAttribute('allow_dhtml_editor');
		if (attr) this.DisableRte = (attr == 1) ? false : true;
		var attr = RootElement.getAttribute('def_charset_inc');
		if (attr) this.CharsetInc = attr - 0;
		var attr = RootElement.getAttribute('def_charset_out');
		if (attr) this.CharsetOut = attr - 0;
		var attr = RootElement.getAttribute('def_timezone');
		if (attr) this.TimeOffset = attr - 0;
		attr = RootElement.getAttribute('view_mode');
		if (attr) this.ViewMode = attr - 0;
		var SettingsParts = RootElement.childNodes;
		var count = SettingsParts.length;
		for (var i=count-1; i>=0; i--) {
			var parts = SettingsParts[i].childNodes;
			var partsCount = parts.length;
			switch (SettingsParts[i].tagName) {
				case 'skins':
					for (var j=0; j<partsCount; j++) {
						var def = false;
						var skin = '';
						attr = parts[j].getAttribute('def');
						if (attr) def = (attr == 1) ? true : false;
						var part = parts[j].childNodes;
						if (part.length > 0 && parts[j].tagName == 'skin')
							skin = part[0].nodeValue;
						if (skin.length > 0) {
							this.Skins.push(skin);
							if (def) this.DefSkin = skin;
						}
					}
					break;
				case 'langs':
					for (var j=0; j<partsCount; j++) {
						var def = false;
						var lang = '';
						attr = parts[j].getAttribute('def');
						if (attr) def = (attr == 1) ? true : false;
						var part = parts[j].childNodes;
						if (part.length > 0 && parts[j].tagName == 'lang')
							lang = part[0].nodeValue;
						if (lang.length > 0) {
							this.Langs.push(lang);
							if (def) this.DefLang = lang;
						}
					}
					break;
				case 'def_date_fmt':
					if (partsCount > 0) {
						this.DateFormat = parts[0].nodeValue;
					} else {
						this.DateFormat = '';
					}
					break;
			}//switch
		}//for
	}//GetFromXML
}

function CAccountProperties()
{
	this.Type = TYPE_ACCOUNT_PROPERTIES;
	this.Id = -1;
	this.DefAcct = false;
	this.MailProtocol = POP3_PROTOCOL;
	this.MailIncPort = POP3_PORT;
	this.MailOutPort = SMTP_PORT;
	this.MailOutAuth = false;
	this.UseFriendlyNm = false;
	this.MailsOnServerDays = 1;
	this.MailMode = 1;
	this.GetMailAtLogin = false;
	this.InboxSyncType = SYNC_TYPE_NEW_MSGS;
	this.FriendlyNm = '';
	this.Email = '';
	this.MailIncHost = '';
	this.MailIncLogin = '';
	this.MailIncPass = '';
	this.MailOutHost = '';
	this.MailOutLogin = '';
	this.MailOutPass = '';
}

CAccountProperties.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ this.Id ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys
	
	GetInXML: function ()
	{
		var attrs = '';
		attrs += ' mail_inc_port="' + this.MailIncPort + '"';
		attrs += ' mail_out_port="' + this.MailOutPort + '"';
		if (this.Id != -1) attrs += ' id="' + this.Id + '"';
		if (this.DefAcct) attrs += ' def_acct="1"';
		else attrs += ' def_acct="0"';
		attrs += ' mail_protocol="' + this.MailProtocol + '"';
		if (this.MailOutAuth) attrs += ' mail_out_auth="1"';
		else attrs += ' mail_out_auth="0"';
		if (this.UseFriendlyNm) attrs += ' use_friendly_nm="1"';
		else attrs += ' use_friendly_nm="0"';
		attrs += ' mails_on_server_days="' + this.MailsOnServerDays + '"';
		attrs += ' mail_mode="' + this.MailMode + '"';
		if (this.GetMailAtLogin) attrs += ' getmail_at_login="1"';
		else attrs += ' getmail_at_login="0"';
		attrs += ' inbox_sync_type="' + this.InboxSyncType + '"';

		var nodes = '';
		nodes += '<friendly_nm>' + GetCData(this.FriendlyNm) + '</friendly_nm>';
		nodes += '<mail_out_host>' + GetCData(this.MailOutHost) + '</mail_out_host>';
		nodes += '<mail_out_login>' + GetCData(this.MailOutLogin) + '</mail_out_login>';
		nodes += '<mail_out_pass>' + GetCData(this.MailOutPass) + '</mail_out_pass>';
		nodes += '<email>' + GetCData(this.Email) + '</email>';
		nodes += '<mail_inc_host>' + GetCData(this.MailIncHost) + '</mail_inc_host>';
		nodes += '<mail_inc_login>' + GetCData(this.MailIncLogin) + '</mail_inc_login>';
		nodes += '<mail_inc_pass>' + GetCData(this.MailIncPass) + '</mail_inc_pass>';

		var xml = '<account' + attrs + '>' + nodes + '</account>';
		return xml;
	},

	GetFromXML: function (RootElement)
	{
		var attr = RootElement.getAttribute('id');
		if (attr) this.Id = attr - 0;
		attr = RootElement.getAttribute('def_acct');
		if (attr) this.DefAcct = (attr == 1) ? true : false;
		var attr = RootElement.getAttribute('mail_protocol');
		if (attr) this.MailProtocol = attr - 0;
		var attr = RootElement.getAttribute('mail_inc_port');
		if (attr) this.MailIncPort = attr - 0;
		var attr = RootElement.getAttribute('mail_out_port');
		if (attr) this.MailOutPort = attr - 0;
		attr = RootElement.getAttribute('mail_out_auth');
		if (attr) this.MailOutAuth = (attr == 1) ? true : false;
		attr = RootElement.getAttribute('use_friendly_nm');
		if (attr) this.UseFriendlyNm = (attr == 1) ? true : false;
		attr = RootElement.getAttribute('mails_on_server_days');
		if (attr) this.MailsOnServerDays = attr - 0;
		attr = RootElement.getAttribute('mail_mode');
		if (attr) this.MailMode = attr - 0;
		attr = RootElement.getAttribute('getmail_at_login');
		if (attr) this.GetMailAtLogin = (attr == 1) ? true : false;
		attr = RootElement.getAttribute('inbox_sync_type');
		if (attr) this.InboxSyncType = attr - 0;
		var SettingsParts = RootElement.childNodes;
		var count = SettingsParts.length;
		for (var i=count-1; i>=0; i--) {
			var parts = SettingsParts[i].childNodes;
			var partsCount = parts.length;
			if (partsCount > 0) {
				switch (SettingsParts[i].tagName) {
					case 'friendly_name':
						this.FriendlyNm = parts[0].nodeValue;
						break;
					case 'email':
						this.Email = parts[0].nodeValue;
						break;
					case 'mail_inc_host':
						this.MailIncHost = parts[0].nodeValue;
						break;
					case 'mail_inc_login':
						this.MailIncLogin = parts[0].nodeValue;
						break;
					case 'mail_inc_pass':
						this.MailIncPass = parts[0].nodeValue;
						break;
					case 'mail_out_host':
						this.MailOutHost = parts[0].nodeValue;
						break;
					case 'mail_out_login':
						this.MailOutLogin = parts[0].nodeValue;
						break;
					case 'mail_out_pass':
						this.MailOutPass = parts[0].nodeValue;
						break;
				}//switch
			}
		}//for
	}//GetFromXML
}

function CFilters() {
	this.Type = TYPE_FILTERS;
	this.Id = -1;
	this.Items = Array();
}

CFilters.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys

	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('id');
		if (attr) this.Id = attr - 0;
		var filters = RootElement.childNodes;
		var iCount = filters.length;
		for (var i=0; i<iCount; i++) {
			var filterProp = new CFilterProperties();
			filterProp.GetFromXML(filters[i]);
			this.Items.push(filterProp);
		}//for
	}//GetFromXML
}

function CFilterProperties() {
	this.Type = TYPE_FILTER_PROPERTIES;
	this.Id = -1;
	this.Field = 0;
	this.Condition = 0;
	this.Action = 2;
	this.IdFolder = -1;
	this.Value = '';
	this.Desc = '';
}

CFilterProperties.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ this.Id ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys
	
	GetInXML: function (idAcct)
	{
		var attrs = '';
		attrs += ' id_acct="' + idAcct + '"';
		if (this.Id != -1 ) attrs += ' id="' + this.Id + '"';
		attrs += ' field="' + this.Field + '"';
		attrs += ' condition="' + this.Condition + '"';
		attrs += ' action="' + this.Action + '"';
		attrs += ' id_folder="' + this.IdFolder + '"';

		var xml = '<filter' + attrs + '>' + GetCData(this.Value) + '</filter>';
		return xml;
	},

	GetFromXML: function (RootElement)
	{
		var attr = RootElement.getAttribute('id');    if (attr) this.Id = attr - 0;
		attr = RootElement.getAttribute('field');     if (attr) this.Field = attr - 0;
		attr = RootElement.getAttribute('condition'); if (attr) this.Condition = attr - 0;
		attr = RootElement.getAttribute('action');    if (attr) this.Action = attr - 0;
		attr = RootElement.getAttribute('id_folder'); if (attr) this.IdFolder = attr - 0;
		var filterNodes = RootElement.childNodes;
		if (filterNodes.length > 0)
			this.Value = filterNodes[0].nodeValue;
		var srtField = '';
		switch (this.Field) {
			case 0: srtField = Lang.From; break;
			case 1: srtField = Lang.To; break;
			case 2: srtField = Lang.Subject; break;
		}
		var srtCondition = '';
		switch (this.Condition) {
			case 0: srtCondition = Lang.ContainSubstring; break;
			case 1: srtCondition = Lang.ContainExactPhrase; break;
			case 2: srtCondition = Lang.NotContainSubstring; break;
		}
		this.Desc = srtCondition + ' <b>' + this.Value + '</b> ' + Lang.FilterDesc_At + ' ' + srtField + ' ' + Lang.FilterDesc_Field;
	}//GetFromXML
}

function CXSpam() {
	this.Type = TYPE_X_SPAM;
	this.Value = false;
}

CXSpam.prototype = {
	GetStringDataKeys: function (_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys
	
	GetInXML: function ()
	{
		if (this.Value) var xml = '<param name="x_spam" value="1"/>';
		else var xml = '<param name="x_spam" value="0"/>';
		return xml;
	},

	GetFromXML: function (RootElement)
	{
		var attr = RootElement.getAttribute('value') - 0;
		if (attr) this.Value = (attr == 1) ? true : false;
	}//GetFromXML
}

function CContactsSettings() {
	this.Type = TYPE_CONTACTS_SETTINGS;
	this.WhiteListing = false;
	this.ContactsPerPage = -1;
}

CContactsSettings.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys

	GetFromXML: function(RootElement)
	{
		var attr = RootElement.getAttribute('white_listing');
		if (attr) this.WhiteListing = (attr == '1') ? true : false;

		var attr = RootElement.getAttribute('contacts_per_page');
		if (attr) this.ContactsPerPage = attr - 0;
	}//GetFromXML
}

function CFolders() {
	this.Type = TYPE_FOLDERS;
	this.MsgsCount = 0;
	this.Size = 0;
	this.Items = Array();
}

CFolders.prototype = {
	GetStringDataKeys: function(_SEPARATOR)
	{
		var arDataKeys = [ ];
		return arDataKeys.join(_SEPARATOR);
	},//GetStringDataKeys

	GetFromXML: function(RootElement)
	{
		var attr;
		var msgsCount = 0;
		var totalSize = 0;
		
		var foldersNodes = RootElement.childNodes;
		count = foldersNodes.length;
		for (var i=0; i<count; i++) {
			var parts = foldersNodes[i].childNodes;
			var partsCount = parts.length;
			if (partsCount > 0) {
				if (foldersNodes[i].tagName == 'folder') {
					var id = foldersNodes[i].getAttribute('id') - 0;
					var idParent = foldersNodes[i].getAttribute('id_parent') - 0;
					var type = foldersNodes[i].getAttribute('type') - 0;
					var syncType = foldersNodes[i].getAttribute('sync_type') - 0;
					attr = foldersNodes[i].getAttribute('hide');
					var hide = (attr == '1') ? true : false;
					var fldOrder = foldersNodes[i].getAttribute('fld_order') - 0;
					var count = foldersNodes[i].getAttribute('count') - 0;
					msgsCount += count;
					var size = foldersNodes[i].getAttribute('size') - 0;
					totalSize += size;
					var level = foldersNodes[i].getAttribute('folder_level') - 0;
					var fullName = '';
					var name = '';
					var folderParts = foldersNodes[i].childNodes;
					var count = folderParts.length;
					for (var i=count-1; i>=0; i--) {
						var parts = folderParts[i].childNodes;
						var partsCount = parts.length;
						if (partsCount > 0) {
							switch (folderParts[i].tagName) {
								case 'full_name':
									fullName = parts[0].nodeValue;
									break;
								case 'name':
									name = parts[0].nodeValue;
									break;
							}//switch
						}
					}//for
					this.Items.push({ Id: id, IdParent: idParent, Type: type, SyncType: syncType, Hide: hide, FldOrder: fldOrder, Count: count, Size: size, Name: name, FullName: fullName, Level: level });
				}
			}
		}
	}//GetFromXML
}
