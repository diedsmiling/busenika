/*
Classes:
	CContactsSelectionMini
	CContactSelectionMiniPart
*/

function CContactsSelectionMini()
{
	this.lines = Array();
	this.length = 0;
	this.prev = -1;
	
	this.list = document.getElementById("list");
}

CContactsSelectionMini.prototype = 
{
	getContactsAsString: function ()
	{
		var VArray = Array();
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Checked == true) {
				VArray.push(line.Value);
			}
		}
		return VArray.join(", ");
	},
	
	FillContacts: function ()
	{
		var tr_arr = this.list.getElementsByTagName("tr");
		var line;
		
		for (i=0; i < tr_arr.length - 1; i++)
		{
			line = document.getElementById(tr_arr[i].id);
			if (line)
			{
				this.AddLine(new CContactSelectionMiniPart(tr_arr[i]));
		
				var obj = this;
				
				line.onclick = function(e)
				{
					e = e ? e : window.event;
					if(e.ctrlKey) {
						obj.CheckCtrlLine(this.id);
					} else if (e.shiftKey) {
						obj.CheckShiftLine(this.id);
					} else {
						if (Browser.Mozilla) {var elem = e.target;}
						else {var elem = e.srcElement;}
						
						if (!elem || elem.id == "none") {
							return false;
						}			
						
						var loverTag = elem.tagName.toLowerCase();
			
						if (loverTag == "input") {
							obj.CheckCBox(this.id);
						} else {
							obj.CheckLine(this.id);
						}
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
		}
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
				} else {
					line.Uncheck();
				}
			}
		}
	},
	
	CheckLine: function(id)
	{
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Id == id){
				line.Check();
				this.prev = i;
			} else {
				line.Uncheck();
			}
		}
	},
	
	CheckShiftLine: function(id)
	{

		if (this.prev == -1) {
			this.CheckLine(id);
		} else {
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
				} else {
					line.Uncheck();
				}
			}
			//this.prev = prev;
		}
	},
	
	CheckCBox: function(id)
	{
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Id == id){
				if (line.Checked == false) {
					line.Check();
					this.prev = i;
				} else {
					line.Uncheck();
				}
			}
		}
	}
}

function CContactSelectionMiniPart(tr)
{
	tr.onmousedown = function() {return false;}//don't select content in Opera
	tr.onselectstart = function() {return false;}//don't select content in IE
	tr.onselect = function() {return false;}//don't select content in IE
	this._tr = tr;
	this._className = tr.className;
	this.Id = tr.id;
	this.Checked = false;
	this.Value = "";
	var collection = this._tr.getElementsByTagName('input');
	if (collection.length > 1) {
		this._checkbox = collection[0];
		this.Value = collection[1].value;
	} 
	this.ApplyClassName();
}

CContactSelectionMiniPart.prototype = {
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