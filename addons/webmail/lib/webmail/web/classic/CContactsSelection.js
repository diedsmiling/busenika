function ParseCId(idstring)
{
	var IdArray = idstring.split('_');
	return (IdArray.length > 1) ?
		{type: IdArray[0], cid: IdArray[1]} : null;
	//return (IdArray.length > 3) ? {type: IdArray[0], cid: IdArray[1], c_name: IdArray[2], c_email: IdArray[3]} : null;
}

function Init_contacts()
{
	var list = document.getElementById("list");
	var tr_arr = list.getElementsByTagName("tr");
	
	selection = new CContactsSelection();
	
	for (i=0; i < tr_arr.length; i++)
	{
		this.line = document.getElementById(tr_arr[i].id);
		if (this.line)
		{
			selection.AddLine(new CContactSelectionPart(tr_arr[i]));
	
			var obj = this;
			
			this.line.onclick = function(e)
			{
				e = e ? e : window.event;
				if(e.ctrlKey) {
					selection.CheckCtrlLine(this.id);
				} else if (e.shiftKey) {
					selection.CheckShiftLine(this.id);
				} else {
					selection.CheckLine(this.id);
					ViewAdressRecord(this.id);
				}
			}
		}
	}
}

function CContactsSelection()
{
	this.lines = Array();
	this.length = 0;
	this.prev = -1;
}

CContactsSelection.prototype = 
{
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
	
	UncheckAll: function ()
	{
		for (var i = this.length-1; i >= 0; i--) {
			this.lines[i].Uncheck();
		}
		this.prev = -1;
	}
}

function CContactSelectionPart(tr)
{
	tr.onmousedown = function() {return false;}//don't select content in Opera
	tr.onselectstart = function() {return false;}//don't select content in IE
	tr.onselect = function() {return false;}//don't select content in IE
	this._tr = tr;
	this._className = tr.className;
	this.Id = tr.id;
	this.Checked = false;
	this.ApplyClassName();
}

CContactSelectionPart.prototype = {
	Check: function()
	{
		this.Checked = true;
		this.ApplyClassName();
	},

	Uncheck: function()
	{
		this.Checked = false;
		this.ApplyClassName();
	},
	
	ApplyClassName: function ()
	{
		if (this.Checked)
			this._tr.className = this._className + '_select';
		else
			this._tr.className = this._className;
	}
}
