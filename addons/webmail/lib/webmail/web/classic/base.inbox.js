/*
Classes:
	CMainContainer
	CFoldersPart
	CMessageList
	CMessageIframe
	CSelectionPart
	CSelection
	
	CVerticalResizer
	CHorizontalResizer
*/

function CMainContainer()
{
		this.table = document.getElementById("main_container");

		this.info = document.getElementById("info");
		this.hider = document.getElementById("hider");

		this.logo = document.getElementById("logo");
		this.accountslist = document.getElementById("accountslist");
		this.toolbar = document.getElementById("toolbar");
		this.lowtoolbar = document.getElementById("lowtoolbar");
		
		//logo + accountslist + toolbar + lowtoolbar
		this.external_height = 56 + 32 + 26 + 24;
		this.inner_height = 100;
}

CMainContainer.prototype =
{
	getExternalHeight: function()
	{
		var res = 0;
		var offsetHeight = 0;
		offsetHeight = this.logo.offsetHeight;         if (offsetHeight) { res += offsetHeight; }
		offsetHeight = this.accountslist.offsetHeight; if (offsetHeight) { res += offsetHeight; } else { res += 32; }
		offsetHeight = this.toolbar.offsetHeight;      if (offsetHeight) { res += offsetHeight; } else { res += 26; }
		offsetHeight = this.lowtoolbar.offsetHeight;   if (offsetHeight) { res += offsetHeight; } else { res += 24; }
		this.external_height = res;
		return this.external_height + 1;
	},
	
	showContent: function()
	{
		this.info.className = 'wm_hide';
		this.hider.className = '';
	},
	
	hideContent: function()
	{
		this.info.className = 'wm_information';
		this.hider.className = 'wm_hide';
	}
}

function CFoldersPart(mode, skinName)
{
	this.isPreviewPane = mode;
	this.container = document.getElementById('folders_part');
	this.width = 115;
	this.height = 100;
	this.realwidth = this.width;
	this.folders = document.getElementById('folders');
	this.folders_hide = document.getElementById('folders_hide');
	this.folders_hide_img = document.getElementById('folders_hide_img');
	this.manage_folders = document.getElementById('manage_folders');
	
	this.skinName = skinName;

	//border + manage folders + hide folders
	this.external_height = 1 + 22 + 20;
}

CFoldersPart.prototype =
{
	resizeElementsWidth: function(width)
	{
		this.width = width;
		//MovableVerticalDiv._leftPosition = width;
		this.container.style.width = this.width + 'px';
		this.folders.style.width = this.width + 'px';
		this.folders_hide.style.width = this.width + 'px';
		this.manage_folders.style.width = this.width + 'px';
	},
	
	resizeElementsHeight: function(height)
	{
		this.height = height;
		var bordersHeight = 1;
		this.container.style.height = (this.height - bordersHeight) + 'px';

		var hOffsetHeight = this.folders_hide.offsetHeight;   if (hOffsetHeight == 0) hOffsetHeight = 22;
		var mOffsetHeight = this.manage_folders.offsetHeight; if (mOffsetHeight == 0) mOffsetHeight = 21;
		this.external_height = hOffsetHeight + mOffsetHeight;
		this.folders.style.height = (this.height - this.external_height - bordersHeight) + 'px';
	},
	
	show: function()
	{
		this.width = this.realwidth;
		this.folders_hide_img.src = './skins/' + this.skinName + '/folders/hide_folders.gif';
		this.folders_hide_img.title = Lang.HideFolders;
		this.folders.className = 'wm_folders';
		this.manage_folders.className = 'wm_manage_folders';
		CreateCookie('wm_hide_folders', 0, 20);
	},
	
	hide: function()
	{
		this.realwidth = this.width;
		this.width = 18;
		this.folders_hide_img.src = './skins/' + this.skinName + '/folders/show_folders.gif';
		this.folders_hide_img.title = Lang.ShowFolders;
		if (this.isPreviewPane) {
			this.folders.className = 'wm_hide';
			this.manage_folders.className = 'wm_hide';
		} else {
			this.folders.className = 'wm_unvisible';
			this.manage_folders.className = 'wm_unvisible';
		}
		CreateCookie('wm_hide_folders', 1, 20);
	}
}

function CMessageList(mode)
{
	this.isPreviewPane = mode;
	this.page_switcher = PageSwitcher._mainCont;
	this.parent_table = document.getElementById('inbox_part');
	this.parent_div = document.getElementById('inbox_div');

	this.mess_container = document.getElementById('list_container');
	this.mess_table = document.getElementById('list');
	this.subject = document.getElementById('subject');
	
	this.inbox_headers = document.getElementById('inbox_headers');
	
	this.width = 350;
	this.height = 300;
}

CMessageList.prototype =
{
	resizeElementsHeight: function(height)
	{
		this.height = height;
		this.parent_table.style.height = this.height + 'px';
		this.parent_div.style.height = this.height + 'px';
		var ihHeight = this.inbox_headers.offsetHeight; if (ihHeight == 0) ihHeight = 22;
		var bordersHeight = 1;
		this.mess_container.style.height = (this.height - ihHeight - bordersHeight) + 'px';
	},
	
	resizeElementsWidth: function(width)
	{
		this.width = width;
		this.parent_table.style.width = this.width + 'px';
		this.parent_div.style.width = this.width + 'px';
		this.mess_container.style.width = this.width + 'px';
		this.mess_table.style.width = this.width + 'px';
		this.subject.style.width = (this.width - 404) + 'px';
	}
}

function CMessageIframe(mode)
{
	this.isPreviewPane = mode;
	this.container = document.getElementById('message_container_iframe');
	this.table = document.getElementById('iframe_td');
	this.table_tr = document.getElementById('iframe_tr');
	this.iframe = document.getElementById('iframe_container');
	this.hResizerCont = document.getElementById("hresizer_part");
	this.iframe.style.scrolling = 'no';

	this.document = (Browser.IE) ? this.iframe.document : this.iframe.contentDocument;
	
	this.width = 361;
	this.height = 361;
		
	//31 + infobar + border + inbox headers
	this.min_upper = 21 + 2;
	//2 borders + resizer + message headers + message
	this.min_lower = 2 + 4 + 45 + 100;
	
	if (!mode)
	{
		this.table.className = 'wm_hide';
		this.iframe.src = 'empty.html';
	}
}

CMessageIframe.prototype =
{
	resizeElementsHeight: function(height)
	{
		this.height = height;
		var bordersHeight = 3;
		this.table.style.height = (this.height - bordersHeight) + 'px';
		var resizerHeight = this.hResizerCont.offsetHeight;
		if (resizerHeight == 0) resizerHeight = 5;
		this.container.style.height = (this.height - resizerHeight - bordersHeight) + 'px';
		this.iframe.style.height = (this.height - resizerHeight - bordersHeight) + 'px';
	},
	
	resizeElementsWidth: function(width)
	{
		this.width = width;
		this.table.style.width = this.width + 'px';
		this.container.style.width = this.width + 'px';
		this.iframe.style.width = this.width + 'px';
	},
	
	hide: function()
	{
		if(Browser.IE) {this.iframe.className = 'wm_hide';}
	},
	
	show: function()
	{
		if(Browser.IE) 
		{
			this.iframe.className = '';
			this.resizeElementsWidth(this.width - 1);
		}
	}
}


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
		this._checkTd = collection[0];
		var checkboxcoll = this._checkTd.getElementsByTagName('input');
		if (checkboxcoll.length > 0) {
				this._checkbox = checkboxcoll[0];
		}
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
		this.AppleCheckBox(); 
	},

	Uncheck: function()
	{
		this.Checked = false;
		this.ApplyClassName();
		this.AppleCheckBox(); 
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
	
	AppleCheckBox: function ()
	{
		if (this._checkbox) this._checkbox.checked = (this.Checked);
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
	
	this.AllCheckBox = document.getElementById("allcheck"); 
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
		this.ReCheckAllBox(); 
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
		this.ReCheckAllBox(); 
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
		this.ReCheckAllBox(); 
	},
	
	UncheckAll: function ()
	{
		for (var i = this.length-1; i >= 0; i--) {
			this.lines[i].Uncheck();
		}
		this.prev = -1;
	},
	
	ReCheckAllBox: function()
	{
		var isAllCheck = true;
		for (var i = this.length-1; i >= 0; i--) {
			if (this.lines[i].Checked == false) { isAllCheck = false;}
		}
		if (this.AllCheckBox)
		{
			this.AllCheckBox.checked = isAllCheck;
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

		this.ReCheckAllBox();
	},
		
	CheckAllBox: function(objCheckbox)
	{
		for (var i = this.length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (objCheckbox.checked) {
				line.Check();
			} else {
				line.Uncheck();
			}
		}		
	}
}



function CVerticalResizer(DIVMovable, parentTable, divHSize, minLeftWidth, minRightWidth, leftPosition, endMoveHandler, type) {
	// set internal data by outside parameters
	
	this._type 			 = (type) ? type : 0;
	this._DIVMovable     = DIVMovable;
	this._parentTable    = parentTable;
	this._divHSize	     = divHSize;
	this._minLeftWidth   = minLeftWidth;
	this._minRightWidth  = minRightWidth;
	this._leftPosition   = leftPosition;
	this._beginPosition  = 0;
	this._endMoveHandler = endMoveHandler;

	// set some internal data by default values (this values must be overwritten)
	this._leftBorder  = 0;
	this._rightBorder = 600;
	this._leftLimit   = 80;
	this._rightLimit  = 550;

	this._divVSize = 1;

	with(this._DIVMovable.style)
	{
		width = this._divHSize + 'px';
		height = this._divVSize + 'px';
		cursor = 'e-resize';
	}
	switch (this._type) {
		case 0:
			this._leftShear = 0;
			with(this._DIVMovable.style)
			{
				background = 'none';
				left = '1px';
			}
			break;
		case 1:
			this._leftShear = leftPosition;
			break;
	}
	this._DIVMovable.innerHTML = '&nbsp;';
	
	// this handler is necessary to begins moving
	var obj = this;
	this._DIVMovable.onmousedown = function(e)
	{
		obj.beginMoving(e);
		return false; //don't select content in Opera
	}
}

CVerticalResizer.prototype = {
	updateVerticalSize: function(vert_size)
	{
		this._divVSize = vert_size;
		this._DIVMovable.style.height = this._divVSize + 'px';
	},
	
	beginMoving: function(e)
	{
		if (Iframe) Iframe.hide();
   		e = e ? e : event;
		this._beginPosition = e.clientX;
		if (this._type == 0) this._DIVMovable.style.background = '#979899';
		//don't select content in IE
		document.onselectstart = function() {return false;}
		document.onselect = function() {return false;}
		
		// calculate borders of this._parentTable
		var bounds = GetBounds(this._parentTable);
		this._leftBorder  = bounds.Left;
		this._rightBorder = bounds.Left + bounds.Width;

		// calculate moving limits (for center of movable td/div)
		this._leftLimit   = this._leftBorder  + this._minLeftWidth + (this._beginPosition - this._leftPosition) - this._leftBorder;
		this._rightLimit  = this._rightBorder - this._minRightWidth - ((this._leftPosition + 6) - this._beginPosition) - this._leftBorder;

		// hang moving handlers	
		var obj = this;
		this._parentTable.onmousemove = function(e)
		{
		    if ( arguments.length == 0 )
        		e = event;
			obj.processMoving( e.clientX ); 
		}
		
		this._parentTable.onmouseup = function()
		{
			obj.endMoving();
		}
		
		this._parentTable.onmouseout = function(e)
		{
		    if ( arguments.length == 0 )
        		e = event;

        	var b = GetBounds(this);
			var left_border   = b.Left;
			var top_border    = b.Top;
			var right_border  = left_border + b.Width;
			var bottom_border = top_border  + b.Height;
			
			// it is necessary to prevent incorrect action on mouseout event
			if( e.clientX<=left_border || e.clientX>=right_border ||
				e.clientY<=top_border  || e.clientY>=bottom_border )
			{
				obj.endMoving();
			}
		}
	},
	
	processMoving: function(mouse_x)	
	{
		// check and correct mouse_x if it is necessary
		if( mouse_x < this._leftLimit ){
			mouse_x = this._leftLimit;
		}
		if( mouse_x > this._rightLimit ){
			mouse_x = this._rightLimit;
		}
		switch (this._type) {
			case 0:
				this._DIVMovable.style.left = mouse_x - this._beginPosition + 1 + 'px';
				this._leftShear = mouse_x - this._beginPosition;
				break;
			case 1:
				var new_left = this._leftPosition + mouse_x - this._beginPosition;
				if( new_left < (this._leftLimit - (this._beginPosition - this._leftPosition)) ){
					new_left = this._leftLimit - (this._beginPosition - this._leftPosition);
				}
				if( new_left > this._rightLimit	 ){
					new_left = this._rightLimit + ((this._leftPosition + 6) - this._beginPosition);
				}
				this._leftShear = new_left;
				eval(this._endMoveHandler);
				break;
		}
	},
	
	endMoving: function()
	{
		document.onselectstart = function() {}
		document.onselect = function() {}
		this._parentTable.onmousemove = '';
		this._parentTable.onmouseup = '';
		this._parentTable.onmouseout = '';
		switch (this._type) {
			case 0:
				this._DIVMovable.style.background = 'none';
				this._DIVMovable.style.left = '1px';
				var new_left = this._leftPosition + this._leftShear;
				if( new_left < (this._leftLimit - (this._beginPosition - this._leftPosition)) ){
					new_left = this._leftLimit - (this._beginPosition - this._leftPosition);
				}
				if( new_left > this._rightLimit	 ){
					new_left = this._rightLimit + ((this._leftPosition + 6) - this._beginPosition);
				}
				this._leftPosition = new_left;
				this._leftShear = 0;
				eval(this._endMoveHandler);
				break;
			case 1:
				this._leftPosition = this._leftShear;
				break;
		}
		CreateCookie('wm_vert_resizer', this._leftPosition, 20);
	},
	
	free: function()
	{
		this._parentTable.onmousemove = '';
		this._parentTable.onmouseup = '';
		this._parentTable.onmouseout = '';
		this._DIVMovable.onmousedown = '';
		with(this._DIVMovable.style)
		{
			cursor = 'default';
		}		
	},
	
	busy: function(width)
	{
		this._leftPosition = width;
		
		with(this._DIVMovable.style)
		{
			cursor = 'e-resize';
		}		
		
		// this handler is necessary to begins moving
		var obj = this;
		this._DIVMovable.onmousedown = function(e)
		{
			obj.beginMoving(e);
			return false; //don't select content in Opera
		}	
	}
}

function CHorizontalResizer(DIVMovable, parentTable, divVSize, minUpperHeight, minLowerHeight, topPosition, endMoveHandler) {
	// set internal data by outside parameters
	this._DIVMovable     = DIVMovable;
	this._parentTable    = parentTable;// table (HTML Element) which contents all changable TRs
	this._divVSize	     = divVSize;// vertical size of movable TR/TD/DIV
	this._minUpperHeight = minUpperHeight;// minimal height when upper TR has good look
	this._minLowerHeight = minLowerHeight;// minimal height when lower TR has good look
	this._topPosition    = topPosition;
	this._topShear       = 0;
	this._beginPosition  = 0;
	this._endMoveHandler = endMoveHandler;
	
	// set some internal data by default values (this values must be overwritten)
	this._upperBorder  = 114;
	this._lowerBorder = 815;
	this._upperLimit   = 268;
	this._lowerLimit  = 665;

	this._divHSize = 1	;

	with(this._DIVMovable.style)
	{
		width = this._divHSize + 'px';
		height = this._divVSize + 'px';
		cursor = 's-resize';
		background = 'none';
		top = '0px';
	}
	this._DIVMovable.innerHTML = '&nbsp;';

	// this handler is necessary to begins moving
	var obj = this;
	this._DIVMovable.onmousedown = function(e)
	{
		obj.beginMoving(e);
		return false; //don't select content in Opera
	}
}

CHorizontalResizer.prototype = 
{
	updateHorizontalSize: function(horiz_size)
	{
		this._divHSize = horiz_size;
		this._DIVMovable.style.width = this._divHSize + 'px';
	},

	beginMoving: function(e)
	{
		Iframe.hide();
   		e = e ? e : event;
		this._beginPosition = e.clientY; 
		this._DIVMovable.style.background = '#979899';
		//don't select content in IE
		document.onselectstart = function() {return false;}
		document.onselect = function() {return false;}

		// calculate borders of this._parentTable
		var bounds = GetBounds(this._parentTable);
		this._upperBorder = bounds.Top;
		this._lowerBorder = bounds.Top + bounds.Height;

		// calculate moving limits (for center of movable td/div)
		this._upperLimit = this._upperBorder + this._minUpperHeight + (this._beginPosition - this._topPosition) - this._upperBorder;
		this._lowerLimit = this._lowerBorder - this._minLowerHeight - ((this._topPosition + 6) - this._beginPosition) - this._upperBorder;

		// hang moving handlers	
		var obj = this;
		this._parentTable.onmousemove = function(e)
		{
		    if ( arguments.length == 0 )
        		e = event;
			obj.processMoving( e.clientY ); 
		}

		this._parentTable.onmouseup = function()
		{
			obj.endMoving();
		}

		this._parentTable.onmouseout = function(e)
		{
		    if ( arguments.length == 0 )
        		e = event;

        	var b = GetBounds(this);
			var left_border   = b.Left;
			var top_border    = b.Top;
			var right_border  = left_border + b.Width;
			var bottom_border = top_border  + b.Height;
			
			// it is necessary to prevent incorrect action on mouseout event
			if( e.clientX<=left_border || e.clientX>=right_border ||
				e.clientY<=top_border  || e.clientY>=bottom_border )
			{
				obj.endMoving();
			}
		}
		
	},

	processMoving: function(mouse_y)	
	{
		// check and correct mouse_y if it is necessary
		if( mouse_y < this._upperLimit ){
			mouse_y = this._upperLimit;
		}
		if( mouse_y > this._lowerLimit ){
			mouse_y = this._lowerLimit;
		}
		this._DIVMovable.style.top = mouse_y - this._beginPosition + 'px';
		this._topShear = mouse_y - this._beginPosition;
	},
	
	endMoving: function()
	{
		this._DIVMovable.style.background = 'none';
		this._DIVMovable.style.top = '0px';
		document.onselectstart = function() {}
		document.onselect = function() {}
		var new_top = this._topPosition + this._topShear;
		if( new_top < (this._upperLimit - (this._beginPosition - this._topPosition)) ){
			new_top = this._upperLimit - (this._beginPosition - this._topPosition);
		}
		if( new_top > this._lowerLimit + ((this._topPosition + 6) - this._beginPosition) ){
			new_top = this._lowerLimit + ((this._topPosition + 6) - this._beginPosition);
		}
		this._topPosition = new_top;
		this._topShear = 0;
		this._parentTable.onmousemove = '';
		this._parentTable.onmouseup = '';
		this._parentTable.onmouseout = '';
		eval(this._endMoveHandler);
		CreateCookie('wm_horiz_resizer', this._topPosition, 20);
	},
	
	free: function()
	{
		this._parentTable.onmousemove = '';
		this._parentTable.onmouseup = '';
		this._parentTable.onmouseout = '';
		this._DIVMovable.onmousedown = '';
		with(this._DIVMovable.style)
		{
			cursor = 'default';
		}		
	}
}

