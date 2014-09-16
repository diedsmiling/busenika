/*
Classes:
	CAccountsListScreenPart
*/

function CAccountsListScreenPart(skinName, manageFolders)
{
	this.Accounts = null;
	this._idAcct = -1;
	
	this._allowAddAccount = false;
	
	this._manageFoldersObj = manageFolders;

	this._mainContainer = null;

	this.shown = false;
}

CAccountsListScreenPart.prototype = {
	Show: function(idAcct)
	{
		if (!this.shown) {
			this.shown = true;
			this._mainContainer.className = 'wm_settings_list';
		}
		if (null == this.Accounts) {
			GetHandler(TYPE_ACCOUNTS_LIST, { }, [], '');
		} else {
			this.ChangeIdAcct(idAcct);
		}
	},//Show
	
	ChangeIdAcct: function (idAcct) {
		if (this.Accounts.LastId != idAcct) {
			this.Accounts.LastId = idAcct;
		}
		this.Fill();
	},
	
	Hide: function()
	{
		this.shown = false;
		this._mainContainer.className = 'wm_hide';
	},//Hide
	
	SetAccounts: function (accounts)
	{
		this.Accounts = accounts;
		var arrAccounts = this.Accounts.Items;
		var count = 0;
		for(var i in arrAccounts)
		{
			if (arrAccounts[i].DefAcct) count++;
		}
		return count;
	},//UpdateAccounts
	
	Fill: function ()
	{
		if (this.shown) {
			this._idAcct = this.Accounts.LastId;
			CleanNode(this._mainContainer);
			var tbl = CreateChild(this._mainContainer, 'table');
			var arrAccounts = this.Accounts.Items;
			var rowIndex = 0;
			for(var i in arrAccounts) {
				var account = arrAccounts[i];
				var tr = tbl.insertRow(rowIndex);
				var td = tr.insertCell(0);
				if (account.Id == this.Accounts.LastId) {
					tr.className = 'wm_settings_list_select';
					td.innerHTML = '<b>' + account.Email + '</b>';
					this._manageFoldersObj.UpdateProtocol(account.MailProtocol);
				} else {
					td.className = 'wm_control';
					td.innerHTML = account.Email;
					td.onclick = CreateAccountClickFunc(account.Id);
				}
				td = tr.insertCell(1);
				td.style.width = '10px';
				var a = CreateChildWithAttrs(td, 'a', [['href', '#']]);
				a.innerHTML = $Delete[DELETE];
				a.onclick = CreateRemoveClickFunc(account.Id);
				if (WebMail._isDemo)
				{
					a.onclick = function ()
					{
						WebMail.ShowReport(DemoWarning);
						return false;
					}
				}
				rowIndex++;
			}
		}
	},//Fill
	
	Build: function(container)
	{
		this._mainContainer = CreateChild(container, 'div');
	}//Build
}