/*
Classes:
	CContactsSelection
	CContactSelectionPart
	
	CContactsList
	
Functions:
	Init_contacts
	ParseCId
	
	ResizeElements
*/


function ParseCId(idstring)
{
	var IdArray = idstring.split('_');
	return (IdArray.length > 1) ? {type: IdArray[0], cid: IdArray[1]} : null;
}

function CContactsSelection()
{
	this.lines = Array();
	this.length = 0;
	this.prev = -1;
	
	this.list = document.getElementById("list");
	this.AllCheckBox = document.getElementById("allcheck"); 
}

CContactsSelection.prototype = 
{
	FillContacts: function ()
	{
		var tr_arr = this.list.getElementsByTagName("tr");
		var line;
		
		var obj = this;
		
		this.AllCheckBox.onclick = function(e) {
			obj.CheckAllBox(obj.AllCheckBox);
		};
		
		for (i=0; i < tr_arr.length; i++) {
			if (!tr_arr[i].id)  {
				continue;
			};
			line = document.getElementById(tr_arr[i].id);
			if (line) {
				this.AddLine(new CContactSelectionPart(tr_arr[i]));
		
				line.onclick = function(e) {
					e = e ? e : window.event;
					if(e.ctrlKey) {
						obj.CheckCtrlLine(this.id);
					}
					else if (e.shiftKey) {
						obj.CheckShiftLine(this.id);
					}
					else {
						if (Browser.Mozilla) {var elem = e.target;}
						else {var elem = e.srcElement;}
						
						if (!elem || elem.id == "none") {
							return false;
						};		
						
						var loverTag = elem.tagName.toLowerCase();
			
						if (loverTag == "input") {
							obj.CheckCBox(this.id);
						}
						else {
							obj.CheckLine(this.id);
							ViewAdressRecord(this.id);
						}
					}
				};
				
				line.ondblclick = function(e) {
					obj.CheckCtrlLine(this.id);
					e = e ? e : window.event;
					if (Browser.Mozilla) {var elem = e.target;}
					else {var elem = e.srcElement;}
					
					if (!elem || elem.id == "none" || elem.tagName.toLowerCase() == "input") {
						return false;
					};
					
					var id = ParseCId(this.id);
					if (id && id.type == 'g') {
						MailGroup(id.cid);
					}
					else {
						DoNewMessageButton(this.id);
					}
				}
			}
		}		
	},
	
	AddLine: function (line)
	{
		this.lines.push(line);
		this.length = this.lines.length;
	},
	
	GetCheckedLines: function ()
	{
		var idArray = Array();
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Checked == true) {
				idArray.push(line.Id);
			}
		};
		return idArray;
	},
	
	CheckCtrlLine: function(id)
	{

		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Id == id){
				if (line.Checked == false) {
					line.Check();
					this.prev = i;
				}
				else {
					line.Uncheck();
				}
			}
		};
		this.ReCheckAllBox();
	},
	
	CheckLine: function(id)
	{
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Id == id){
				line.Check();
				this.prev = i;
			}
			else {
				line.Uncheck();
			}
		};
		this.ReCheckAllBox();
	},
	
	CheckShiftLine: function(id)
	{

		if (this.prev == -1) {
			this.CheckLine(id);
		}
		else {
			var isChecking = false;
			var prev = this.prev;
			for (var i = 0; i < this.length; i++) {
				var line = this.lines[i];
				if (this.prev == i || line.Id == id)
					isChecking = isChecking ? false : true;
				if (line.Id == id)
					prev = i;
				if (isChecking || this.prev == i || line.Id == id) {
					line.Check();
				}
				else {
					line.Uncheck();
				}
			}
			//this.prev = prev;
		}
		this.ReCheckAllBox();
	},
	
	UncheckAll: function ()
	{
		for (var i = this.length-1; i >= 0; i--) {
			this.lines[i].Uncheck();
		};
		this.prev = -1;
	}, 
	
	ReCheckAllBox: function()
	{
		var isAllCheck = true;
		for (var i = this.length-1; i >= 0; i--) {
			if (this.lines[i].Checked == false) { isAllCheck = false;}
		};
		if (this.AllCheckBox) {
			this.AllCheckBox.checked = isAllCheck;
		}		
	},
	
	CheckCBox: function(id)
	{
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Id == id) {
				if (line.Checked == false) {
					line.Check();
					this.prev = i;
				}
				else {
					line.Uncheck();
				}
			}
		};

		this.ReCheckAllBox();
	},
		
	CheckAllBox: function(objCheckbox)
	{
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (objCheckbox.checked) {
				line.Check();
			}
			else {
				line.Uncheck();
			}
		}		
	}
}

function CContactSelectionPart(tr)
{
	tr.onmousedown = function () {return false;};//don't select content in Opera
	tr.onselectstart = function () {return false;};//don't select content in IE
	tr.onselect = function () {return false;};//don't select content in IE
	this._tr = tr;
	this._className = tr.className;
	this.Id = tr.id;
	this.Checked = false;
	
	var collection = this._tr.getElementsByTagName('td');
	if (collection.length > 1) {
		this._checkTd = collection[0];
		var checkboxcoll = this._checkTd.getElementsByTagName('input');
		if (checkboxcoll.length > 0) {
				this._checkbox = checkboxcoll[0];
		}
	};
	this.ApplyClassName();
}

CContactSelectionPart.prototype = {
	Check: function()
	{
		this.Checked = true;
		this.ApplyClassName();
		this.AppleCheckBox();
	},

	Uncheck: function()
	{
		this.Checked = false;
		this.ApplyClassName();
		this.AppleCheckBox();
	},
	
	ApplyClassName: function ()
	{
		if (this.Checked)
			this._tr.className = this._className + '_select';
		else
			this._tr.className = this._className;
	},
	
	AppleCheckBox: function ()
	{
		if (this._checkbox) this._checkbox.checked = (this.Checked);
	} 
}


function ResizeElements(mode)
{
	CContactsList.ResizeBody();
} 

function CContactsList()
{
	this._logo = document.getElementById('logo');
	this._accountsBar = document.getElementById('accountslist');
	this._toolBar = document.getElementById('toolbar');
	this._lowToolBar = document.getElementById('lowtoolbar');
	//logo + accountslist + toolbar + lowtoolbar
	this._externalHeight = 58 + 32 + 27 + 28 + 40;
	this._contactsHeadersWidth = 175;
	
	this._mainDiv = document.getElementById('main_contacts');
	this._leftDiv = document.getElementById('contacts');
	this._rightDiv = document.getElementById('contacts_viewer');

	this._contactListTbl = document.getElementById('list');
	this._contactListDiv = document.getElementById('contact_list_div');
	this._contactListHeaders = document.getElementById('contact_list_headers');
	this._emailObj = document.getElementById('emailobj');
	this._pageSwitcher = PageSwitcher;

	this._cardTable = document.getElementById('wm_contacts_card');
}

CContactsList.prototype =
{
	ResizeBody: function(mode)
	{
	    if (!Browser.IE || Browser.Version >= 7) {
		    var listBorderHeight = 1;
		    var height = GetHeight() - this.GetExternalHeight();
		    if (height < this.minListHeight) height = this.minListHeight;
		    var tableHeight = this._contactListHeaders.offsetHeight + this._contactListTbl.offsetHeight;
		    var cardHeight = 0;
		    if (this._cardTable != null) cardHeight = this._cardTable.offsetHeight;
		    if (height < tableHeight) height = tableHeight;
		    if (height < cardHeight) height = cardHeight;
            this._mainDiv.style.height = height + 'px';
            this._contactListDiv.style.height = height - listBorderHeight + 'px';
            
            var listWidth = this._leftDiv.offsetWidth;

           	this._contactListTbl.style.width = listWidth + 'px';
           	this.ResizeContactsTable(listWidth);
            
		    if (this._cardTable != null)  {
		        this._cardTable.style.width = 'auto';
		        var cardWidth = this._cardTable.offsetWidth;
		        var rightWidth = this._rightDiv.offsetWidth;
		        if (cardWidth < rightWidth) cardWidth = rightWidth;
		        this._cardTable.style.width = cardWidth - 1 + 'px';
		    }
	    }
	    else {
            this._mainDiv.style.width = ((document.documentElement.clientWidth || document.body.clientWidth) < 850) && (this._cardTable != null) ? '850px' : '100%';
		    var listWidth = this._leftDiv.offsetWidth;

           	this._contactListTbl.style.width = listWidth + 'px';
           	this.ResizeContactsTable(listWidth);

            if (this._cardTable != null) {
		        var width = GetWidth();
		        if (width < 850) width = 850;
		        this._cardTable.style.width = width - listWidth - 4 + 'px';
		    }
	    };
	    this._pageSwitcher.Replace(this._contactListHeaders);
	},
	
	ResizeContactsTable: function (listWidth)
	{
		var emailWidth = listWidth - this._contactsHeadersWidth;
	    if (this._emailObj != null && emailWidth > 0) {
		    this._emailObj.style.width = emailWidth + 'px';
	    }
    },
    
	GetExternalHeight: function()
	{
		var res = 0;
		var offsetHeight = this._logo.offsetHeight;    if (offsetHeight) { res += offsetHeight; };
		offsetHeight = this._accountsBar.offsetHeight; if (offsetHeight) { res += offsetHeight; } else { return this._externalHeight; };
		offsetHeight = this._toolBar.offsetHeight;     if (offsetHeight) { res += offsetHeight; } else { return this._externalHeight; };
		offsetHeight = this._lowToolBar.offsetHeight;  if (offsetHeight) { res += offsetHeight; } else { return this._externalHeight; };
		this._externalHeight = res;
		return this._externalHeight;
	}
}
