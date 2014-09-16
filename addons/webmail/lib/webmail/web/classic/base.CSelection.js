/*
Classes:
	CSelectionPart
	CSelection
*/

function CSelectionPart(tr, skinName, messageObj)
{
	tr.onmousedown = function() {return false;}//don't select content in Opera
	tr.onselectstart = function() {return false;}//don't select content in IE
	tr.onselect = function() {return false;}//don't select content in IE
	this._tr = tr;
	this._className = tr.className;

	this.Id = tr.id;
	this.Checked = false;



	var collection = this._tr.getElementsByTagName('td');
	if (collection.length > 6)
	{
		this._flagTd = collection[2];
		this._fromTd = collection[3];
		this._subjTd = collection[6];
	}
	collection = this._flagTd.getElementsByTagName('img');
	if (collection.length > 0)
	{
		this._flagImg = collection[0];
	}
	
	this._flagSrc = 'skins/' + skinName + '/menu/flag.gif';
	this._unflagSrc = 'skins/' + skinName + '/menu/unflag.gif';

	this._repliedRead = '<img class="wm_inbox_icon" src="skins/' + skinName + '/icons/replied_read.gif" />';
	this._repliedUnread = '<img class="wm_inbox_icon" src="skins/' + skinName + '/icons/replied_unread.gif" />';
	this._forwardedRead = '<img class="wm_inbox_icon" src="skins/' + skinName + '/icons/forwarded_read.gif" />';
	this._forwardedUnread = '<img class="wm_inbox_icon" src="skins/' + skinName + '/icons/forwarded_unread.gif" />';

	this.Read = messageObj.Read;
	this.Replied = messageObj.Replied;
	this.Forwarded = messageObj.Forwarded;
	this.Flagged = messageObj.Flagged;
	this.Deleted = messageObj.Deleted;
	this.Gray = messageObj.Gray;
	
	this.MsgId = messageObj.Id;
	this.MsgUid = messageObj.Uid;
	this.MsgFolderId = messageObj.FolderId;
	this.MsgFolderFullName = messageObj.FolderFullName;
	this.MsgFromAddr = messageObj.FromAddr;
	this.MsgSubject = messageObj.Subject;

	this.SetClassName();
	this.ApplyClassName();
}

CSelectionPart.prototype = {
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
	
	Flag: function ()
	{
		RequestMessagesOperationHandler(FLAG, [this.Id], 0);
	},
	
	Unflag: function ()
	{
		RequestMessagesOperationHandler(UNFLAG, [this.Id], 0);
	},
	
	SetClassName: function ()
	{
		if (this.Deleted)
			this._className = 'wm_inbox_deleted_item';
		else if (this.Read)
			this._className = 'wm_inbox_read_item';
		else
			this._className = 'wm_inbox_item';
	},
	
	ApplyClassName: function ()
	{
		var className = this._className;
		if (this.Checked)
			className += '_select';
		else if (this.Gray)
			className += ' wm_inbox_grey_item';
		this._tr.className = className;
	},
	
	ApplyFlagImg: function ()
	{
		if (this.Flagged) {
			this._flagImg.src = this._flagSrc;
		} else {
			this._flagImg.src = this._unflagSrc;
		}
	},

	ApplyFlagTd: function ()
	{
		var innerHtml = '';
		if (this.Replied)
			if (this.Read)
				innerHtml = this._repliedRead;
			else
				innerHtml = this._repliedUnread;
		if (this.Forwarded)
			if (this.Read)
				innerHtml = this._forwardedRead;
			else
				innerHtml = this._forwardedUnread;
		this._flagTd.innerHTML = innerHtml;
	},
	
	ApplyFromSubj: function ()
	{
		this._fromTd.innerHTML = '<nobr>' + this.MsgFromAddr + '</nobr>';
		this._subjTd.innerHTML = '<nobr>' + this.MsgSubject + '</nobr>';
	}
}

function CSelection()
{
	this.lines = Array();
	this.length = 0;
	this.prev = -1;
}

CSelection.prototype = 
{
	AddLine: function (line)
	{
		this.lines.push(line);
		this.length = this.lines.length;
	},
	
	SetParams: function (idArray, field, value, isAllMess)
	{
		var readed = 0;
		if (isAllMess)
			idArray = [-1];
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			for (var j in idArray) {
				if (line.Id == idArray[j] || isAllMess) {
					var LineArray = idArray[j].split(sep);
					folderId = (LineArray) ? LineArray[2] : '';
					switch (field) {
						case 'Read':
							if (line.Read == false && value == true)
							{
								readed++;
								var obj = document.getElementById('cnt_' + folderId);
								if (obj)
								{
									var cnt  = obj.innerHTML;
									if (cnt) 
									{	
										cnt = cnt.substring(1, cnt.length - 1);
										cnt = cnt - 1;
										obj.innerHTML = (cnt > 0) ? '(' + cnt + ')' : '';
									}
								}
							}
							if (line.Read == true && value == false)
							{
								readed--;
								var obj = document.getElementById('cnt_' + folderId);
								if (obj)
								{
									var cnt  = obj.innerHTML;
									if (cnt == "") cnt = '(0)';
									if (cnt) 
									{
										cnt = cnt.substring(1, cnt.length - 1);
										cnt = (cnt - 0) + 1;
										obj.innerHTML = (cnt > 0) ? '(' + cnt + ')' : '';
									}
								}								
							}
							line.Read = value;
							line.SetClassName();
							line.ApplyClassName();
						//	line.ApplyFlagTd();
							break;
						case 'Deleted':
							line.Deleted = value;
							line.SetClassName();
							line.ApplyClassName();
							break;
						case 'Flagged':
							line.Flagged = value;
							line.ApplyFlagImg();
							break;
						case 'Replied':
							line.Replied = value;
							line.ApplyFlagTd();
							break;
						case 'Forwarded':
							line.Forwarded = value;
							line.ApplyFlagTd();
							break;
						case 'Gray':
							line.Gray = value;
							line.ApplyClassName();
							break;
					}//switch field
				}//if
			}//for j
		}//for i
		return readed;
	},
	
	ChangeLineId: function (msg, newId)
	{
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.MsgId == msg.Id && line.MsgUid == msg.Uid && line.MsgFolderId == msg.FolderId &&
			 line.MsgFolderFullName == msg.FolderFullName) {
			 	alert(newId);
				line.Id = newId;
				line._tr.id = newId;
				line.MsgFromAddr = msg.FromAddr;
				line.MsgSubject = msg.Subject;
				line.ApplyFromSubj();
			}
		}
	},
	
	UpdateSubject: function(lineId, subj, from)
	{
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Id == lineId) {
				line.MsgFromAddr = from;
				line.MsgSubject = subj;
				line.ApplyFromSubj();
			}
		}
	},
		
	GetCheckedLines: function ()
	{
		var idArray = Array();
		var unreaded = 0;
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Checked == true) {
				if (!line.Read)
					unreaded++;
				idArray.push(line.Id);
			}
		}
		return {IdArray: idArray, Unreaded: unreaded};
	},
	
	GetCheckedLinesObj: function ()
	{
		var messArray = Array();
		for (var i = this.length-1; i >= 0; i--)
		{
			var line = this.lines[i];
			if (line.Checked == true) messArray.push(line);
		}
		return messArray;
	},
	
	GetLinesById: function (lineId)
	{
		for (var i = this.length-1; i >= 0; i--)
		{
			var line = this.lines[i];
			if (line.Id == lineId) return line;
		}
		return null;
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
	
	DragItemsNumber: function (id)
	{
		var findLine = null;
		var number = 0;
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Id == id){
				findLine = line;
			}
			if (line.Checked) {
				number++;
			}
		}
		if (null == findLine) {
			return 0;
		} else if (findLine.Checked) {
			return number;
		} else {
			this.CheckLine(id);
			return 1;
		}
	},
	
	FlagLine: function (id)
	{
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Id == id){
				if (line.Flagged) {
					line.Unflag();
				} else {
					line.Flag();
				}
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
