/*
Classes:
	CCheckMail
*/

function CCheckMail(type)
{
	this.isBuilded = false;
	if (type)
		this._type = type;
	else
		this._type = 0;
	this.started = false;
	
	this._url = CheckMailUrl;
	this._email = '';
	this._msgsCount = 0;
	this._preText = '';
	
	this._form = null;
	this._typeObj = null;
	this._folderHandler = null;
	this._msgNumberHandler = null;
	this._endHandler = null;
	
	this._mainContainer = null;
	this._infomation = null;
	this._message = null;
	this._progressBarUsed = null;
}

CCheckMail.prototype = {
	Start: function ()
	{
		if (this.isBuilded) {
			if (this._type == 0) this._infomation.Show();
		} else {
			this.Build();
		}
		this._preText = '';
		this.SetText(Lang.LoggingToServer);
		this._msgsCount = 1;
		this.UpdateProgressBar(0);
		this._msgsCount = 0;
		this._typeObj.value = this._type;
		this._form.action = this._url + '?param=' + Math.random();
		this._form.submit();
		this.started = true;
	},
	
	SetAccount: function (account)
	{
		this._email = account;
		this._mainContainer.className = 'wm_connection_information';
		this._preText = '<b>' + this._email + '</b><br/>';
	},

	SetFolder: function (folderName, msgsCount)
	{
		this._folderName = folderName;
		this._msgsCount = msgsCount;
		this._preText = '';
		if (this._email.length > 0) this._preText += '<b>' + this._email + '</b><br/>';
		this._preText += 'Folder <b>' + this._folderName + '</b><br/>';
	},
	
	SetText: function (text)
	{
		this._message.innerHTML = this._preText + text;
		if (this._type == 0) this._infomation.Resize();
	},
	
	DeleteMsg: function (msgNumber) {
		if (msgNumber == -1) {
			this.SetText(Lang.DeletingMessages);
		} else {
			this.SetText(Lang.DeletingMessage + ' #' + msgNumber + ' ' + Lang.Of + ' ' + this._msgsCount);
			this.UpdateProgressBar(msgNumber);
		}
	},
	
	SetMsgNumber: function (msgNumber)
	{
		if (msgNumber <= this._msgsCount) {
			this.SetText(Lang.RetrievingMessage + ' #' + msgNumber + ' ' + Lang.Of + ' ' + this._msgsCount);
		}
		this.UpdateProgressBar(msgNumber);
	},
	
	UpdateProgressBar: function (msgNumber)
	{
		if (this._msgsCount > 0) {
			var percent = Math.ceil((msgNumber - 1)*100/this._msgsCount);
			if (percent < 0) { percent = 0; }
			else if (percent > 100) { percent = 100; }
			this._progressBarUsed.style.width = percent + 'px';
		}
	},
	
	End: function ()
	{
		if (this._type == 0) this._infomation.Hide();
		this.started = false;
	},
	
	Build: function ()
	{
		/* iframe block building */
		var iframe = CreateChildWithAttrs(document.body, 'iframe', [['id', 'CheckMailIframe'], ['name', 'CheckMailIframe'], ['src', EmptyHtmlUrl], ['class', 'wm_hide']]);
		/*with (iframe.style) {
			position = 'absolute';
			top = '0px';
			left = '0px';
			width = '500px';
			height = '500px';
			zIndex = '5';
		}*/
		var frm = CreateChildWithAttrs(document.body, 'form', [['action', this._url], ['target', 'CheckMailIframe'], ['method', 'post'], ['id', 'CheckMailForm'], ['name', 'CheckMailForm'], ['class', 'wm_hide']]);
		this._typeObj = CreateChildWithAttrs(frm, 'input', [['name', 'Type'], ['value', this._type]]);
		this._folderHandler = CreateChildWithAttrs(frm, 'input', [['name', 'SetFolderHandler'], ['value', 'SetFolderHandler']]);
		this._msgNumberHandler = CreateChildWithAttrs(frm, 'input', [['name', 'SetMsgNumberHandler'], ['value', 'SetMsgNumberHandler']]);
		this._endHandler = CreateChildWithAttrs(frm, 'input', [['name', 'EndHandler'], ['value', 'EndCheckMailHandler']]);
		if (this._type == 1) {
			this._accountHandler = CreateChildWithAttrs(frm, 'input', [['name', 'SetAccountHandler'], ['value', 'SetAccountHandler']]);
		}
		this._form = frm;
		/* information block building for type 0 */
		if (this._type == 0) {
			var tbl = CreateChild(document.body, 'table');
			tbl.className = 'wm_connection_information';
			with (tbl.style) {
				position = 'absolute';
				top = '0px';
				right = '0px';
			}
			var tr = tbl.insertRow(0);
			var td = tr.insertCell(0);
			td.className = 'wm_info_message';
			this._message = CreateChild(td, 'span');
			tr = tbl.insertRow(1);
			td = tr.insertCell(0);
			var div = CreateChildWithAttrs(td, 'div', [['align', 'center']]);
			var divPB = CreateChildWithAttrs(div, 'div', [['class', 'wm_progressbar']]);
			//if (Browser.IE) divPB.style.height = '9px';
			this._progressBarUsed = CreateChildWithAttrs(divPB, 'div', [['class', 'wm_progressbar_used']]);
			this._infomation = new CInformation(tbl, 'wm_connection_information');
		}
		/* information block building for type 1 */
		if (this._type == 1) {
			var parent = document.getElementById('content');
			if (parent) {
				var tbl = CreateChild(parent, 'table');
			} else {
				var tbl = CreateChild(document.body, 'table');
			}
			tbl.className = 'wm_hide';
			this._mainContainer = tbl;
			tbl.style.marginTop = '30px';
			var tr = tbl.insertRow(0);
			var td = tr.insertCell(0);
			td.className = 'wm_connection_header';
			td.colSpan = '3';
			td.innerHTML = Lang.Connection;
			tr = tbl.insertRow(1);
			td = tr.insertCell(0);
			td.className = 'wm_connection_icon';
			td = tr.insertCell(1);
			td.className = 'wm_connection_message';
			td.align = 'center';
			this._message = td;
			td = tr.insertCell(2);
			td.className = 'wm_connection_empty';
			tr = tbl.insertRow(2);
			td = tr.insertCell(0);
			td.className = 'wm_connection_progressbar';
			td.colSpan = 3;
			var div = CreateChildWithAttrs(td, 'div', [['align', 'center']]);
			var div1 = CreateChildWithAttrs(div, 'div', [['class', 'wm_progressbar']]);
			this._progressBarUsed = CreateChildWithAttrs(div1, 'div', [['class', 'wm_progressbar_used']]);
		}
		/* it's builded! */
		this.isBuilded = true;
	}
}