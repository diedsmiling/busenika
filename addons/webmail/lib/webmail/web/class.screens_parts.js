/*
Classes:
	CVerticalResizer
	CHorizontalResizer
	CSelection
	CSearchForm
	CToolButton
	CToolBar
	CFolderParams
	CFolderLine
	CContactSelectionPart
	CContactsSelection
*/

function CVerticalResizer(DIVMovable, parentTable, divHSize, minLeftWidth, minRightWidth, leftPosition, endMoveHandler, type) {
	// set internal data by outside parameters
	if (type) {
		this._type       = type;
	} else {
		this._type       = 0;
	}
	switch (this._type)
	{
		case 0:
			this._class      = 'wm_vresizer';
			this._classPress = 'wm_vresizer_press';
		break;
		case 1:
			this._class      = 'wm_vresizer_mess';
			this._classPress = 'wm_vresizer_mess';
		break;
		case 2:
			this._class      = 'wm_inbox_headers_separate';
			this._classPress = 'wm_inbox_headers_separate';
		break;
	}
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

	this._divVSize = 2;
	this._divVSizePress = 2;
	
	with(this._DIVMovable.style)
	{
		width = this._divHSize + 'px';
		height = this._divVSize + 'px';
		cursor = 'e-resize';
	}
	switch (this._type) {
		case 0:
			this._leftShear = 0;
			this._DIVMovable.style.left = '1px';
			break;
		case 1:
		case 2:
			this._leftShear = leftPosition;
			break;
	}
	this._DIVMovable.className = this._class;
	if (this._type != 2)
	{
		this._DIVMovable.innerHTML = '&nbsp;';
	}
	
	// this handler is necessary to begins moving
	var obj = this;
	this._DIVMovable.onmousedown = function(e)
	{
		obj.beginMoving(e);
		return false; //don't select content in Opera
	}
}

CVerticalResizer.prototype = {
	updateVerticalSize: function(vert_size, vert_size_press)
	{
		this._divVSize = vert_size;
		this._DIVMovable.style.height = this._divVSize + 'px';
		if (this._type == 2)
		{
			this._divVSizePress = vert_size_press;
		}
		else
		{
			this._divVSizePress = vert_size;
		}
	},
	
	updateMinLeftWidth: function(minLeftWidth)
	{
		this._minLeftWidth = minLeftWidth;
	},
	
	updateMinRightWidth: function(minRightWidth)
	{
		this._minRightWidth = minRightWidth;
	},
	
	updateLeftPosition: function (leftPosition)
	{
		var diff = leftPosition - this._leftPosition;
		this._minLeftWidth += diff;
		this._leftPosition = leftPosition;
		this._DIVMovable.style.left = leftPosition + 'px';
	},

	beginMoving: function(e)
	{
   		e = e ? e : event;
		this._beginPosition = e.clientX;
		this._DIVMovable.className = this._classPress;
		if (this._type == 2)
		{
			this._DIVMovable.style.height = this._divVSizePress + 'px';
		}
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
				if (this._type == 1) eval(this._endMoveHandler);
				break;
			case 2:
				var new_left = this._leftPosition + mouse_x - this._beginPosition;
				if( new_left < (this._leftLimit - (this._beginPosition - this._leftPosition)) ){
					new_left = this._leftLimit - (this._beginPosition - this._leftPosition);
				}
				if( new_left > this._rightLimit	 ){
					new_left = this._rightLimit + ((this._leftPosition + 6) - this._beginPosition);
				}
				this._DIVMovable.style.left = new_left + 'px';
				this._leftShear = new_left;
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
		this._DIVMovable.className = this._class;
		if (this._type == 2)
		{
			this._DIVMovable.style.height = this._divVSize + 'px';
		}
		switch (this._type) {
			case 0:
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
			case 2:
				this._leftPosition = this._leftShear;
				eval(this._endMoveHandler);
				break;
		}
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

	this._class          = 'wm_hresizer';
	this._classPress     = 'wm_hresizer_press';
	
	// set some internal data by default values (this values must be overwritten)
	this._upperBorder  = 114;
	this._lowerBorder = 815;
	this._upperLimit   = 268;
	this._lowerLimit  = 665;

	this._divHSize = 2	;

	with(this._DIVMovable.style)
	{
		width = this._divHSize + 'px';
		height = this._divVSize + 'px';
		cursor = 's-resize';
		top = '0px';
	}
	this._DIVMovable.className = this._class;
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
   		e = e ? e : event;
		this._beginPosition = e.clientY;
		this._DIVMovable.className = this._classPress;
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
		this._DIVMovable.className = this._class;
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

function CSelectionPart(tr, skinName, messageObj, flagImg, flagTd, fromTd, subjTd)
{
	tr.onmousedown = function() {return false;}//don't select content in Opera
	tr.onselectstart = function() {return false;}//don't select content in IE
	tr.onselect = function() {return false;}//don't select content in IE
	this._tr = tr;
	this._className = tr.className;

	this.Id = tr.id;
	this.Checked = false;

	this._flagImg = flagImg;
	this._flagSrc = 'skins/' + skinName + '/menu/flag.gif';
	this._unflagSrc = 'skins/' + skinName + '/menu/unflag.gif';

	this._flagTd = flagTd;
	this._fromTd = fromTd;
	this._subjTd = subjTd;
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
	this.Length = 0;
	this.prev = -1;
}

CSelection.prototype = 
{
	Free: function ()
	{
		this.lines = Array();
		this.Length = 0;
		this.prev = -1;
	},
	
	AddLine: function (line)
	{
		this.lines.push(line);
		this.Length = this.lines.length;
	},
	
	SetParams: function (idArray, field, value, isAllMess)
	{
		var readed = 0;
		if (isAllMess)
			idArray = [-1];
		for (var i = this.Length-1; i >= 0; i--) {
			var line = this.lines[i];
			for (var j in idArray) {
				if (line.Id == idArray[j] || isAllMess) {
					switch (field) {
						case 'Read':
							if (line.Read == false && value == true)
								readed++;
							if (line.Read == true && value == false)
								readed--;
							line.Read = value;
							line.SetClassName();
							line.ApplyClassName();
							line.ApplyFlagTd();
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
		for (var i = this.Length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.MsgId == msg.Id && line.MsgUid == msg.Uid && line.MsgFolderId == msg.FolderId &&
			 line.MsgFolderFullName == msg.FolderFullName) {
				if (newId) {
					line.Id = newId;
					line.Node.id = newId;
				}
				line.MsgFromAddr = msg.FromAddr;
				line.MsgSubject = msg.Subject;
				line.ApplyFromSubj();
			}
		}
	},
	
	GetCheckedLines: function ()
	{
		var idArray = Array();
		var unreaded = 0;
		for (var i = this.Length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Checked == true) {
				if (!line.Read)
					unreaded++;
				idArray.push(line.Id);
			}
		}
		return {IdArray: idArray, Unreaded: unreaded};
	},
	
	CheckCtrlLine: function(id)
	{
		for (var i = this.Length-1; i >= 0; i--) {
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
		for (var i = this.Length-1; i >= 0; i--) {
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
		for (var i = this.Length-1; i >= 0; i--) {
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
		for (var i = this.Length-1; i >= 0; i--) {
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
			for (var i = 0; i < this.Length; i++) {
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
				if (this.prev == i && line.Id == id)
					isChecking = isChecking ? false : true;
			}
		}
	},
	
	UncheckAll: function ()
	{
		for (var i = this.Length-1; i >= 0; i--) {
			this.lines[i].Uncheck();
		}
		this.prev = -1;
	}
}

function CSearchForm(BigSearchForm, SmallSearchForm, SearchControl, SearchControlImg, bigFormId, bigLookFor, smallLookFor, skinName)
{
	this.form = BigSearchForm;
	this._bigFormId = bigFormId;
	this.small_form = SmallSearchForm;
	this.control = SearchControl;
	this.control_img = SearchControlImg;
	this._bigLookFor = bigLookFor;
	this._smallLookFor = smallLookFor;
	this.isShown = 0;
	this.shown = false;
	this._skinName = skinName;
	this._searchIn = null;
}

CSearchForm.prototype = 
{
	Show: function ()
	{
		if (!this.shown) {
			this.shown = true;
			this.small_form.className = 'wm_toolbar_search_item';
			this.control.className = 'wm_toolbar_search_item';
			var obj = this;
			this.control.onclick = function() {
				obj.ShowBigForm();
			}
			this.control.onmouseover = function() {
				obj.control.className = 'wm_toolbar_search_item_over';
				obj.small_form.className = 'wm_toolbar_search_item_over';
			}
			this.control.onmouseout = function() {
				obj.control.className = 'wm_toolbar_search_item';
				obj.small_form.className = 'wm_toolbar_search_item';
			}
		}
	},
	
	Hide: function ()
	{
		this.shown = false;
		this.small_form.className = 'wm_hide';
		this.control.className = 'wm_hide';
		this.form.className = 'wm_hide';
	},
	
	SetSearchIn: function (searchIn)
	{
		this._searchIn = searchIn;
	},
	
	ChangeSkin: function (newSkin)
	{
		this._skinName = newSkin;
	},
	
	ShowBigForm: function()
	{
		var bounds = GetBounds(this.small_form);
		this.form.style.top = bounds.Top + 'px';
		this.form.style.right = (GetWidth() - bounds.Left - bounds.Width) + 'px';
		this.form.className = 'wm_search_form';
		this.control.onclick = function() {}
		this.control_img.src = 'skins/' + this._skinName + '/menu/arrow_up.gif';
		this.isShown = 2;
		this._bigLookFor.value = this._smallLookFor.value;
		if (null != this._searchIn) {
			this._searchIn.className = '';
		}
	},
	
	HideBigForm: function()
	{
		this.form.className = 'wm_hide';
		var obj = this;
		this.control.onclick = function() {
			obj.ShowBigForm();
		}
		this.control_img.src = 'skins/' + this._skinName + '/menu/arrow_down.gif';
		this.isShown = 0;
		this._smallLookFor.value = this._bigLookFor.value;
		this._bigLookFor.blur();
		if (null != this._searchIn) {
			this._searchIn.className = 'wm_hide';
		}
	},
	
	checkVisibility: function(ev, isM)
	{
		if (this.isShown == 1) {
			var ev = ev ? ev : window.event;
			if (isM) {elem = ev.target;}
			else {elem = ev.srcElement;}
			while(elem && elem.tagName != 'DIV')
			{
				if(elem.parentNode) {elem = elem.parentNode;}
				else {break;}
			}
			if (elem.id != this._bigFormId) {this.HideBigForm();}
		}
		if (this.isShown == 2)
			this.isShown = 1;
	}
}

function CreateToolBarItemClick(type)
{
	return function () { RequestMessagesOperationHandler(type, [], 1); }
}

function CreateReplyClick(type)
{
	return function () {
		WebMail.ReplyClick(type);
	}
}

function CToolButton(container, path, text, title, imgFile, imgClass, langField, titleLangField)
{
	this.Cont = container;
	this._imgFile = imgFile;
	this._path = path;
	this._langField = langField;
	if (titleLangField) {
		this._titleLangField = titleLangField;
	} else {
		this._titleLangField = langField;
	}
	
	this.Img = null;
	this._text = null;
	
	this.Build(container, path, text, title, imgFile, imgClass);
}

CToolButton.prototype = {
	SetImgFile: function (imgFile) {
		this._imgFile = imgFile;
		this.SetImgSrc();
	},
	
	SetImgPath: function (path)
	{
		this._path = path;
		this.SetImgSrc();
	},
	
	SetImgSrc: function ()
	{
		this.Img.src = this._path + this._imgFile;
	},
	
	MakeActive: function (className, classNameOver, imgFile, clickHandler)
	{
		this.SetImgFile(imgFile);
		this.Cont.className = className;
		this.Cont.onclick = clickHandler;
		this.Cont.onmouseover = function () { this.className = classNameOver; }
		this.Cont.onmouseout = function () { this.className = className; }
	},

	MakeInActive: function (className, imgFile)
	{
		this.SetImgFile(imgFile);
		this.Cont.className = className;
		this.Cont.onclick = function () { };
		this.Cont.onmouseover = function () { }
		this.Cont.onmouseout = function () { }
	},
	
	ChangeLang: function (langField)
	{
		if (langField) {
			this._langField = langField;
		}
		if (this._langField.length > 0) {
			this.SetText(Lang[this._langField]);
		}
		if (this._titleLangField.length > 0) {
			this.Img.title = Lang[this._titleLangField];
		}
	},

	SetText: function (text)
	{
		this._text.innerHTML = text;
	},
	
	ShowText: function ()
	{
		this._text.className = '';
	},
	
	HideText: function ()
	{
		var prop = ReadStyle(this.Img, 'display');
		if (prop == 'none') {
			this._text.className = '';
		} else {
			this._text.className = 'wm_hide';
		}
	},
	
	Build: function (container, path, text, title, imgFile, imgClass)
	{
		this.Img = CreateChildWithAttrs(container, 'img', [['src', path + imgFile], ['class', imgClass], ['title', title]]);
		this._text = CreateChild(container, 'span');
		this.SetText(text);
	}
}

function CToolBar(parent, skinName)
{
	this._skinName = skinName;

	this.table = CreateChild(parent, 'table');
	this.table.className = 'wm_toolbar';
	var tr = this.table.insertRow(0);
	this._container = tr.insertCell(0);
	
	this._descriptions = Array();
	this._descriptions[TOOLBAR_NEW_MESSAGE] = {
		title: Lang.NewMessage,
		imgFile: 'new_message.gif',
		imgClass: 'wm_menu_new_message_img',
		langField: 'NewMessage'
	}
	this._descriptions[TOOLBAR_CHECK_MAIL] = {
		title: Lang.CheckMail,
		imgFile: 'check_mail.gif',
		imgClass: 'wm_menu_check_mail_img',
		langField: 'CheckMail'
	}
	this._descriptions[TOOLBAR_RELOAD_FOLDERS] = {
		title: Lang.ReloadFolders,
		imgFile: 'reload_folders.gif',
		imgClass: 'wm_menu_reload_folders_img',
		langField: 'ReloadFolders'
	}
	this._descriptions[TOOLBAR_DELETE] = {
		title: $Delete[DELETE],
		imgFile: 'delete.gif',
		imgClass: 'wm_menu_delete_img',
		langField: 'Delete'
	}
	this._descriptions[TOOLBAR_EMPTY_TRASH] = {
		title: Lang.EmptyTrash,
		imgFile: 'empty_trash.gif',
		imgClass: 'wm_menu_empty_trash_img',
		langField: 'EmptyTrash'
	}
	this._descriptions[TOOLBAR_FORWARD] = {
		title: Lang.Forward,
		imgFile: 'forward.gif',
		imgClass: 'wm_menu_forward_img',
		langField: 'Forward'
	}
	this._descriptions[TOOLBAR_NEW_CONTACT] = {
		title: Lang.NewContact,
		imgFile: 'new_contact.gif',
		imgClass: 'wm_menu_new_contact_img',
		langField: 'NewContact'
	}
	this._descriptions[TOOLBAR_NEW_GROUP] = {
		title: Lang.NewGroup,
		imgFile: 'new_group.gif',
		imgClass: 'wm_menu_new_group_img',
		langField: 'NewGroup'
	}
	this._descriptions[TOOLBAR_IMPORT_CONTACTS] = {
		title: Lang.ImportContacts,
		imgFile: 'import_contacts.gif',
		imgClass: 'wm_menu_import_contacts_img',
		langField: 'ImportContacts'
	}
	this._descriptions[TOOLBAR_SEND_MESSAGE] = {
		title: Lang.SendMessage,
		imgFile: 'send.gif',
		imgClass: 'wm_menu_send_message_img',
		langField: 'SendMessage'
	}
	this._descriptions[TOOLBAR_SAVE_MESSAGE] = {
		title: Lang.SaveMessage,
		imgFile: 'save.gif',
		imgClass: 'wm_menu_save_message_img',
		langField: 'SaveMessage'
	}
	this._descriptions[TOOLBAR_PRINT_MESSAGE] = {
		title: Lang.Print,
		imgFile: 'print.gif',
		imgClass: 'wm_menu_print_message_img',
		langField: 'Print'
	}
	
	this._buttons = Array();
	
	this._purgeTool = null;
	this._separatorAll = null;
	this._readAllTool = null;
	this._unreadAllTool = null;
}

CToolBar.prototype = {
	ShowTextLabels: function () {
		var iCount = this._buttons.length;
		for (var i=0; i<iCount; i++) {
			this._buttons[i].ShowText();
		}
	},
	
	HideTextLabels: function () {
		var iCount = this._buttons.length;
		for (var i=0; i<iCount; i++) {
			this._buttons[i].HideText();
		}
	},
	
	ChangeSkin: function (newSkin) {
		var iCount = this._buttons.length;
		for (var i=0; i<iCount; i++) {
			this._buttons[i].SetImgPath('skins/' + newSkin + '/menu/');
		}
		this._skinName = newSkin;
	},
	
	ChangeLang: function () {
		var iCount = this._buttons.length;
		for (var i=0; i<iCount; i++) {
			this._buttons[i].ChangeLang();
		}
	},

	AddItem: function(itemId, clickHandler, mode) {
		var div = CreateChild(this._container, 'div');
		div.onmouseover = function() { this.className = 'wm_toolbar_item_over'; }
		div.onmouseout = function() { this.className = 'wm_toolbar_item'; }
		if (mode) {
			div.className = 'wm_hide';
		} else {
			div.className = 'wm_toolbar_item';
		}
		var itemDesc = this._descriptions[itemId];
		
		var button = new CToolButton(div, 'skins/' + this._skinName + '/menu/', itemDesc.title, itemDesc.title, itemDesc.imgFile, itemDesc.imgClass, itemDesc.langField);
		this._buttons.push(button);
		
		div.onclick = clickHandler;
		return div;
	},
	
	AddNextPrevItem: function(itemId) {
		var div = CreateChild(this._container, 'div');

		if (TOOLBAR_PREV_MESSAGE == itemId) {
			var title = Lang.PreviousMsg;
			var titleLangField = 'PreviousMsg';
			var gif = 'message_up.gif';
		} else {
			var title = Lang.NextMsg;
			var titleLangField = 'NextMsg';
			var gif = 'message_down.gif';
		}
		
		var button = new CToolButton(div, 'skins/' + this._skinName + '/menu/', '', title, gif, 'wm_menu_next_prev_img', '', titleLangField);
		this._buttons.push(button);
		return button;
	},
	
	AddPriorityItem: function() {
		var div = CreateChild(this._container, 'div');
		var button = new CToolButton(div, 'skins/' + this._skinName + '/menu/', Lang.Normal, Lang.Importance, 'priority_normal.gif', 'wm_menu_priority_img', 'Normal', 'Importance');
		this._buttons.push(button);
		return button;
	},

	AddMarkItem: function(markNumber, popupMenus, mode) {
		var markMenu = CreateChild(document.body, 'div');
		markMenu.className = 'wm_hide';
		for (var i=0; i<$Mark.length; i++) {
			if (i != markNumber) {
				var item = CreateChild(markMenu, 'div');
				item.onmouseover = function() { this.className = 'wm_menu_item_over'; }
				item.onmouseout = function() { this.className = 'wm_menu_item'; }
				item.className = 'wm_menu_item';
	
				var button = new CToolButton(item, 'skins/' + this._skinName + '/menu/', $Mark[i], $Mark[i], Mark[i].Image, Mark[i].ImgClass, Mark[i].LangField);
				this._buttons.push(button);
	
				item.onclick = CreateToolBarItemClick(i);
				switch (i) {
					case UNFLAG:
						var item = CreateChild(markMenu, 'div');
						item.className = 'wm_menu_separate';
						this._separatorAll = item;
					break;
					case MARK_ALL_READ:
						this._readAllTool = item;
					break;
					case MARK_ALL_UNREAD:
						this._unreadAllTool = item;
					break;
				}
			}
		}

		var markReplace = CreateChild(this._container, 'div');
		if (mode) {
			markReplace.className = 'wm_hide';
		} else {
			markReplace.className = 'wm_tb';
		}
		markTitle = CreateChild(markReplace, 'div');
		markTitle.className = 'wm_toolbar_item';

		var button = new CToolButton(markTitle, 'skins/' + this._skinName + '/menu/', $Mark[markNumber], $Mark[markNumber], Mark[markNumber].Image, Mark[markNumber].ImgClass, Mark[markNumber].LangField);
		this._buttons.push(button);

		markTitle.onclick = CreateToolBarItemClick(markNumber);
		var markControl = CreateChild(markReplace, 'div');
		markControl.className = 'wm_toolbar_item';

		var button = new CToolButton(markControl, 'skins/' + this._skinName + '/menu/', '', '', 'popup_menu_arrow.gif', 'wm_menu_control_img', '');
		this._buttons.push(button);

		popupMenus.addItem(markMenu, markControl, 'wm_popup_menu', markReplace, markTitle, 'wm_tb', 'wm_tb_press', 'wm_toolbar_item', 'wm_toolbar_item_over');
		return markReplace;
	},
	
	AddMoveItem: function(popupMenus, field, moveMenu, mode, img, className) {
		var moveControl = CreateChild(this._container, 'div');
		if (mode) {
			moveControl.className = 'wm_hide';
		} else {
			moveControl.className = 'wm_toolbar_item';
		}
		
		var button = new CToolButton(moveControl, 'skins/' + this._skinName + '/menu/', Lang[field], Lang[field], img, className, field);
		this._buttons.push(button);

		var button = new CToolButton(moveControl, 'skins/' + this._skinName + '/menu/', '', '', 'popup_menu_arrow.gif', 'wm_menu_move_control_img', '');
		this._buttons.push(button);
		
		popupMenus.addItem(moveMenu, moveControl, 'wm_popup_menu', moveControl, moveControl, 'wm_toolbar_item', 'wm_toolbar_item_press', 'wm_toolbar_item', 'wm_toolbar_item_over');
		return moveControl;
	},
	
	AddReplyItem: function(replyNumber, popupMenus, mode) {
		var replyMenu = CreateChild(document.body, 'div');
		replyMenu.className = 'wm_hide';
		for (var i in $Reply) {
			if (i != replyNumber) {
				var item = CreateChild(replyMenu, 'div');
				item.onmouseover = function() { this.className = 'wm_menu_item_over'; }
				item.onmouseout = function() { this.className = 'wm_menu_item'; }
				item.className = 'wm_menu_item';

				var button = new CToolButton(item, 'skins/' + this._skinName + '/menu/', $Reply[i], $Reply[i], Reply[i].Image, 'wm_menu_replyall_img', Reply[i].LangField);
				this._buttons.push(button);

				item.onclick = CreateReplyClick(i);
			}
		}

		var replyReplace = CreateChild(this._container, 'div');
		if (mode) {
			replyReplace.className = 'wm_hide';
		} else {
			replyReplace.className = 'wm_tb';
		}
		var replyTitle = CreateChild(replyReplace, 'div');
		replyTitle.className = 'wm_toolbar_item';

		var button = new CToolButton(replyTitle, 'skins/' + this._skinName + '/menu/', $Reply[replyNumber], $Reply[replyNumber], Reply[replyNumber].Image, 'wm_menu_reply_img', Reply[replyNumber].LangField);
		this._buttons.push(button);

		replyTitle.onclick = CreateReplyClick(replyNumber);
		var replyControl = CreateChild(replyReplace, 'div');
		replyControl.className = 'wm_toolbar_item';

		var button = new CToolButton(replyControl, 'skins/' + this._skinName + '/menu/', '', '', 'popup_menu_arrow.gif', 'wm_menu_control_img', '');
		this._buttons.push(button);

		popupMenus.addItem(replyMenu, replyControl, 'wm_popup_menu', replyReplace, replyTitle, 'wm_tb', 'wm_tb_press', 'wm_toolbar_item', 'wm_toolbar_item_over');
		return replyReplace;
	},
	
	AddDeleteItem: function(deleteNumber, popupMenus, mode) {
		var deleteMenu = CreateChild(document.body, 'div');
		deleteMenu.className = 'wm_hide';
		for (var i=DELETE; i<=PURGE; i++) {
			if (i != deleteNumber) {
				var item = CreateChild(deleteMenu, 'div');
				item.onmouseover = function() { this.className = 'wm_menu_item_over'; }
				item.onmouseout = function() { this.className = 'wm_menu_item'; }
				item.className = 'wm_menu_item';

				var className = 'wm_menu_delete_img';
				if (i == PURGE)
				{
					className = 'wm_menu_purge_img';
				}
				var button = new CToolButton(item, 'skins/' + this._skinName + '/menu/', $Delete[i], $Delete[i], Delete[i].Image, className, Delete[i].LangField);
				this._buttons.push(button);

				item.onclick = CreateToolBarItemClick(i);
				if (i == PURGE) {
					this._purgeTool = item;
				}
			}
		}

		var deleteReplace = CreateChild(this._container, 'div');
		if (mode) {
			deleteReplace.className = 'wm_hide';
		} else {
			deleteReplace.className = 'wm_tb';
		}
		var deleteTitle = CreateChild(deleteReplace, 'div');
		deleteTitle.className = 'wm_toolbar_item';

		var button = new CToolButton(deleteTitle, 'skins/' + this._skinName + '/menu/', $Delete[deleteNumber], $Delete[deleteNumber], Delete[deleteNumber].Image, 'wm_menu_delete_img', Delete[deleteNumber].LangField);
		this._buttons.push(button);

		deleteTitle.onclick = CreateToolBarItemClick(deleteNumber);
		var deleteControl = CreateChild(deleteReplace, 'div');
		deleteControl.className = 'wm_toolbar_item';

		var button = new CToolButton(deleteControl, 'skins/' + this._skinName + '/menu/', '', '', 'popup_menu_arrow.gif', 'wm_menu_control_img', '');
		this._buttons.push(button);

		popupMenus.addItem(deleteMenu, deleteControl, 'wm_popup_menu', deleteReplace, deleteTitle, 'wm_tb', 'wm_tb_press', 'wm_toolbar_item', 'wm_toolbar_item_over');
		return deleteReplace;
	},

	AddSearchItems: function() {
		var searchControl = CreateChild(this._container, 'div');
		searchControl.className = 'wm_toolbar_search_item';
		searchControl.style.marginLeft = '0px';

		var controlButton = new CToolButton(searchControl, 'skins/' + this._skinName + '/menu/', '', '', 'arrow_down.gif', 'wm_search_arrow', '');
		this._buttons.push(controlButton);

		var smallSearchForm = CreateChild(this._container, 'div');
		smallSearchForm.onmouseover = function() {this.className = "wm_toolbar_search_item_over";}
		smallSearchForm.onmouseout = function() {this.className = "wm_toolbar_search_item";}
		smallSearchForm.className = 'wm_toolbar_search_item';
		smallSearchForm.style.marginRight = '0px';
		var lookFor = CreateChildWithAttrs(smallSearchForm, 'input', [['type', 'text'], ['class', 'wm_search_input'], ['maxlength', '255']]);

		var actionButton = new CToolButton(smallSearchForm, 'skins/' + this._skinName + '/menu/', '', '', 'search_button.gif', 'wm_menu_small_search_img', '');
		this._buttons.push(actionButton);

		return {Control: searchControl, ControlImg: controlButton.Img, SmallForm: smallSearchForm, ActionImg: actionButton.Img, LookFor: lookFor}
	},
	
	DisableInSearch: function (mode) {
		if (mode) {
			this._purgeTool.className = 'wm_hide';
			this._separatorAll.className = 'wm_hide';
			this._readAllTool.className = 'wm_hide';
			this._unreadAllTool.className = 'wm_hide';
		} else {
			this._purgeTool.className = 'wm_menu_item';
			this._separatorAll.className = 'wm_menu_separate';
			this._readAllTool.className = 'wm_menu_item';
			this._unreadAllTool.className = 'wm_menu_item';
		}
	}
}

function CFolderParams(type, syncType, count, newCount, name) {
	this._type = type;
	this._imgType = type;
	this._syncType = syncType;
	this._div = null;
	this.Page = 1;
	this.MsgsCount = count;
	this._newMsgsCount = newCount;
	this._name = name;
	this._skinName = '';
	this._toRemoveCount = 0;
	this._toAppendCount = 0;
	this._unreadedToRemoveCount = 0;
	this._unreadedToAppendCount = 0;
	this._toReadCount = 0;
	this._toUnreadCount = 0;
	this._title = null;
	this._clickHandler = null;
}

CFolderParams.prototype = {
	ChangeImgType: function ()
	{
		if (this._syncType != SYNC_TYPE_NO && this._syncType != SYNC_TYPE_DIRECT_MODE) {
			this._title = Lang.SyncFolder;
			switch (this._type){
				case FOLDER_TYPE_DEFAULT:
					this._imgType = FOLDER_TYPE_DEFAULT_SYNC;
					break;
				case FOLDER_TYPE_INBOX:
					this._imgType = FOLDER_TYPE_INBOX_SYNC;
					break;
				case FOLDER_TYPE_SENT:
					this._imgType = FOLDER_TYPE_SENT_SYNC;
					break;
				case FOLDER_TYPE_DRAFTS:
					this._imgType = FOLDER_TYPE_DRAFTS_SYNC;
					break;
				case FOLDER_TYPE_TRASH:
					this._imgType = FOLDER_TYPE_TRASH_SYNC;
					break;
			}//switch
		}
	},//ChangeImgType
	
	SetPage: function (page)
	{
		this.Page = page;
	},//SetPage
	
	SetDiv: function (div, skinName, clickHandler)
	{
		this._div = div;
		this._clickHandler = clickHandler;
		if (this._title != null) {
			this._div.title = this._title;
		}
		this._skinName = skinName;
		this.SetFolderNameText();
	},//SetDiv

	GetDiv: function ()
	{
		return this._div;
	},
	
	SetFolderNameText: function ()
	{
		CleanNode(this._div);
		var a = CreateChild(this._div, 'a');
		a.href = '#';
		a.onclick = this._clickHandler;
		var img = CreateChild(a, 'img');
		img.src = 'skins/' + this._skinName + '/folders/' + FolderImages[this._imgType];
		var span = CreateChild(a, 'span');
		var innerHtml = this._name;
		if (this._newMsgsCount != 0) {
			innerHtml += '&nbsp;(<span title="' + Lang.NewMessages + '">' + this._newMsgsCount + '</span>)';
		}
		span.innerHTML = innerHtml + '&nbsp;';
		if (this._syncType == SYNC_TYPE_DIRECT_MODE)
		{
			span = CreateChild(this._div, 'span');
			span.innerHTML = '&nbsp;' + Lang.DirectAccess + '&nbsp;';
			span.title = Lang.DirectAccessTitle;
			span.className = 'wm_folder_direct_mode';
		}
	},//SetFolderNameText
	
	ChangeMsgsCounts: function (count, newCount)
	{
		this.MsgsCount = count;
		this._newMsgsCount = newCount;
		this.SetFolderNameText();
	},//ChangeMsgsCounts
	
	AddToAppend: function (count, unreaded)
	{
		this._toAppendCount += count;
		this._unreadedToAppendCount += unreaded;
	},//AddToAppend

	AddToRemove: function (count, unreaded)
	{
		this._toRemoveCount += count;
		this._unreadedToRemoveCount += unreaded;
	},//AddToRemove
	
	Append: function ()
	{
		this.MsgsCount += this._toAppendCount;
		this._newMsgsCount += this._unreadedToAppendCount;
		this._toAppendCount = 0;
		this._unreadedToAppendCount = 0;
		this.SetFolderNameText();
	},//Append
	
	Remove: function ()
	{
		this.MsgsCount += -this._toRemoveCount;
		this._newMsgsCount += -this._unreadedToRemoveCount;
		this._toRemoveCount = 0;
		this._unreadedToRemoveCount = 0;
		this.SetFolderNameText();
	},//Remove
	
	AddAllToRead: function ()
	{
		this._toReadCount = this._newMsgsCount;
	},//AddAllToRead
	
	AddAllToUnread: function ()
	{
		this._toUnreadCount = this.MsgsCount - this._newMsgsCount;
	},//AddAllToUnread
	
	AddToRead: function (count)
	{
		this._toReadCount += count;
	},//AddToRead
	
	AddToUnread: function (count)
	{
		this._toUnreadCount += count;
	},//AddToUnread
	
	Read: function (count)
	{
		if (count)
			this._newMsgsCount += -count;
		else {
			this._newMsgsCount += -this._toReadCount;
			this._toReadCount = 0;
		}
		if (this._newMsgsCount < 0) this._newMsgsCount = 0;
		this.SetFolderNameText();
	},//Read
	
	Unread: function ()
	{
		this._newMsgsCount += this._toUnreadCount;
		this._toUnreadCount = 0;
		this.SetFolderNameText();
	}//Unread
}

function CFolderLine(fold, td, checkInp, nameImg, spanA, nameA, nameInp, syncSel, hideImg, skinName, opt, parent, prevIndex, index, upImg, downImg, countTd, sizeTd, allowDirectMode)
{
	this._fold = {};
	this._fold.Id = fold.Id;
	this._fold.IdParent = fold.IdParent;
	this._fold.Type = fold.Type;
	this._fold.SyncType = fold.SyncType;
	this._fold.Hide  = fold.Hide ;
	this._fold.FldOrder = fold.FldOrder;
	this._fold.hasChilds = fold.hasChilds;
	this._fold.MsgCount = fold.MsgCount;
	this._fold.NewMsgCount = fold.NewMsgCount;
	this._fold.Size = fold.Size;
	
	var fName = fold.Name;
	switch (fold.Type)
	{
		case FOLDER_TYPE_INBOX:
			fName = Lang.FolderInbox;
		break;
		case FOLDER_TYPE_SENT:
			fName = Lang.FolderSentItems;
		break;
		case FOLDER_TYPE_DRAFTS:
			fName = Lang.FolderDrafts;
		break;
		case FOLDER_TYPE_TRASH:
			fName = Lang.FolderTrash;
		break;
	}
	this._fold.Name = fName;
	this._fold.RealName = fold.Name;
	this._fold.FullName = fold.FullName;
	this._fold.Level = fold.Level;
	this._fold.Checked = false;
	this._fold.PrevIndex = prevIndex;
	this._fold.Index = index;
	this._fold.NextIndex = -1;
	
	this._container = td;
	this._checkInp = checkInp;
	this._nameImg = nameImg;
	this._nameA = nameA;
	this._spanA = spanA;
	this._nameInp = nameInp;
	this._syncSel = syncSel;
	this._hideImg = hideImg;
	this._skinName = skinName;
	this._opt = opt;
	this._parent = parent;
	this._upImg = upImg;
	this._downImg = downImg;
	this._countTd = countTd;
	this._sizeTd = sizeTd;
	this._allowDirectMode = allowDirectMode;
	this._directModeOpt = null;
	
	this.checkDisable = 0;

	this.Init();
}

CFolderLine.prototype = {
	Init: function () {
		var obj = this;
		
		indent = FOLDERS_TREES_INDENT * this._fold.Level;
		this._container.style.paddingLeft = indent + 'px';
		
		if (this._fold.Type == FOLDER_TYPE_DEFAULT) {
			if (null != this._syncSel && (this._fold.hasChilds || this._fold.MsgCount > 0)) {
				this._checkInp.checked = false;
				this._checkInp.disabled = true;
				this._checkInp.onchange = function () { }
				this.checkDisable = 1;
			} else {
				this._checkInp.checked = this._fold.Checked;
				this._checkInp.disabled = false;
				this._checkInp.onchange = function () { obj.SetChecked(this.checked); }
			}
			this._checkInp.className = 'wm_checkbox';
		} else {
			this._checkInp.className = 'wm_hide';
		}
		
		var strIndent = '';
		for (var j=0; j<this._fold.Level; j++) strIndent += '&nbsp;&nbsp;&nbsp;&nbsp;';
		this._opt.innerHTML = strIndent + this._fold.Name;
		if (this._fold.Type == FOLDER_TYPE_DEFAULT) {
			this._spanA.innerHTML = '&nbsp;';
			this._nameA.innerHTML = this._fold.Name;
			this._nameA.onclick = function () { obj.EditName(); return false; }
		} else {
			this._spanA.innerHTML = '&nbsp;' + this._fold.Name;
			this._nameA.innerHTML = '';
			this._nameA.onclick = function () { return false; }
		}
		this._nameImg.src = 'skins/' + this._skinName + '/folders/' + FolderImages[this._fold.Type];
		this._nameInp.onkeydown = function (ev) { if (isEnter(ev)) obj.SaveName(); }
		this._nameInp.onblur = function () { obj.StopEditName(); }
		
		this._countTd.innerHTML = this._fold.MsgCount;
		this._sizeTd.innerHTML = GetFriendlySize(this._fold.Size);

		if (this._syncSel != null) {
			this.FillSyncSel();
			this._syncSel.onchange = function () {
				if (!obj._allowDirectMode && obj._directModeOpt != null) {
					obj._syncSel.removeChild(obj._directModeOpt);
					obj._directModeOpt = null;
				}
				obj.SetSyncType(this.value);
			}
		}
			
		if (this._fold.Hide) this._hideImg.src = 'skins/' + this._skinName + '/folders/hide.gif';
		else this._hideImg.src = 'skins/' + this._skinName + '/folders/show.gif';
		this._hideImg.onclick = function () { obj.ChangeHide(); }
		
		if (this._fold.PrevIndex == -1) {
			this._upImg.className = '';
			this._upImg.src = 'skins/' + this._skinName + '/folders/up_inactive.gif';
		} else {
			this._upImg.className = 'wm_control_img';
			this._upImg.src = 'skins/' + this._skinName + '/folders/up.gif';
			var obj = this;
			this._upImg.onclick = function () { obj.ChangeWithPrev(); }
		}
	},
	
	SetNextFoldLine: function (index)
	{
		this._fold.NextIndex = index;
		if (this._fold.NextIndex == -1) {
			this._downImg.className = '';
			this._downImg.src = 'skins/' + this._skinName + '/folders/down_inactive.gif';
		} else {
			this._downImg.className = 'wm_control_img';
			this._downImg.src = 'skins/' + this._skinName + '/folders/down.gif';
			var obj = this;
			this._downImg.onclick = function () { obj.ChangeWithNext(); }
		}
	},
	
	ChangeWithPrev: function ()
	{
		this._parent.ChangeFoldersPlaces(this._fold.PrevIndex, this._fold.Index);
	},
	
	ChangeWithNext: function ()
	{
		this._parent.ChangeFoldersPlaces(this._fold.Index, this._fold.NextIndex);
	},

	SetChecked: function (value) {
		if (this._fold.Type == FOLDER_TYPE_DEFAULT && (null == this._syncSel || !this._fold.hasChilds && this._fold.MsgCount == 0)) {
			this._fold.Checked = value;
			this._checkInp.checked = value;
		}
	},
	
	GetCheckedXml: function () {
		if (this._fold.Type == FOLDER_TYPE_DEFAULT && this._fold.Checked)
		    return '<folder id="' + this._fold.Id + '"><full_name>' + GetCData(this._fold.FullName) + '</full_name></folder>';
		else
			return '';
	},
	
	EditName: function () {
		this._nameA.className = 'wm_hide';
		this._nameInp.value = HtmlDecode(this._fold.Name);
		this._nameInp.className = '';
		this._nameInp.focus();
	},
	
	SaveName: function () {
		var value = this._nameInp.value;
		if (Trim(value).length != 0 && this._fold.Name != value ) {
			if (!IsClearForFS(value)) {
				alert(Lang.WarningCantUpdateFolder);
			} else {
				this._fold.Name = value;
				var strIndent = '';
				for (var j=0; j<this._fold.Level; j++) strIndent += '&nbsp;&nbsp;&nbsp;&nbsp;';
				this._opt.innerHTML = strIndent + this._fold.Name;
				this._nameA.innerHTML = HtmlEncode(this._fold.Name);
				this._parent.hasChanges = true;
			}
		}
		this.StopEditName();
	},
	
	StopEditName: function () {
		this._nameInp.className = 'wm_hide';
		this._nameInp.blur();
		this._nameA.className = '';
	},
	
	SetSyncType: function (value) {
		this._fold.SyncType = value - 0;
		this._parent.hasChanges = true;
	},
	
	ChangeHide: function () {
		if (this._fold.Hide) {
			this._fold.Hide = false;
			this._hideImg.src = 'skins/' + this._skinName + '/folders/show.gif';
		} else {
			this._fold.Hide = true;
			this._hideImg.src = 'skins/' + this._skinName + '/folders/hide.gif';
		}
		this._parent.hasChanges = true;
	},
	
	GetProperties: function () {
		return this._fold;
	},
	
	SetProperties: function (fold) {
		this._fold = fold;
		this.Init();
		this.SetNextFoldLine(this._fold.NextIndex);
	},
	
	FillSyncSel: function () {
		var sel = this._syncSel;
		CleanNode(sel);
		opt = CreateChildWithAttrs(sel, 'option', [['value', SYNC_TYPE_NO]]); opt.innerHTML = Lang.SyncTypes[SYNC_TYPE_NO];
		if (this._fold.SyncType == SYNC_TYPE_NO) opt.selected = true;
		opt = CreateChildWithAttrs(sel, 'option', [['value', SYNC_TYPE_NEW_HEADERS]]); opt.innerHTML = Lang.SyncTypes[SYNC_TYPE_NEW_HEADERS];
		if (this._fold.SyncType == SYNC_TYPE_NEW_HEADERS) opt.selected = true;
		opt = CreateChildWithAttrs(sel, 'option', [['value', SYNC_TYPE_ALL_HEADERS]]); opt.innerHTML = Lang.SyncTypes[SYNC_TYPE_ALL_HEADERS];
		if (this._fold.SyncType == SYNC_TYPE_ALL_HEADERS) opt.selected = true;
		opt = CreateChildWithAttrs(sel, 'option', [['value', SYNC_TYPE_NEW_MSGS]]); opt.innerHTML = Lang.SyncTypes[SYNC_TYPE_NEW_MSGS];
		if (this._fold.SyncType == SYNC_TYPE_NEW_MSGS) opt.selected = true;
		opt = CreateChildWithAttrs(sel, 'option', [['value', SYNC_TYPE_ALL_MSGS]]); opt.innerHTML = Lang.SyncTypes[SYNC_TYPE_ALL_MSGS];
		if (this._fold.SyncType == SYNC_TYPE_ALL_MSGS) opt.selected = true;
		if (this._allowDirectMode || SYNC_TYPE_DIRECT_MODE == this._fold.SyncType) {
			opt = CreateChildWithAttrs(sel, 'option', [['value', SYNC_TYPE_DIRECT_MODE]]); opt.innerHTML = Lang.SyncTypes[SYNC_TYPE_DIRECT_MODE];
			if (this._fold.SyncType == SYNC_TYPE_DIRECT_MODE) opt.selected = true;
			this._directModeOpt = opt;
		}
	},
	
	GetInXml: function () {
		var attrs = '';
		attrs += ' id="' + this._fold.Id + '"';
		attrs += ' sync_type="' + this._fold.SyncType + '"';
		if (this._fold.Hide) {
			attrs += ' hide="1"';
		} else {
			attrs += ' hide="0"';
		}
		attrs += ' fld_order="' + this._fold.FldOrder + '"';
		var nodes = '';
		if (this._fold.Type == FOLDER_TYPE_INBOX || this._fold.Type == FOLDER_TYPE_SENT ||
		 this._fold.Type == FOLDER_TYPE_DRAFTS || this._fold.Type == FOLDER_TYPE_TRASH)
		{
			nodes += '<name>' + GetCData(this._fold.RealName) + '</name>';
		}
		else
		{
			nodes += '<name>' + GetCData(this._fold.Name) + '</name>';
		}
		nodes += '<full_name>' + GetCData(this._fold.FullName) + '</full_name>';
		return '<folder' + attrs + '>' + nodes + '</folder>';
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

function CContactsSelection()
{
	this.lines = Array();
	this.Length = 0;
	this.prev = -1;
}

CContactsSelection.prototype = 
{
	AddLine: function (line)
	{
		this.lines.push(line);
		this.Length = this.lines.length;
	},
	
	GetCheckedLines: function ()
	{
		var idArray = Array();
		for (var i = this.Length-1; i >= 0; i--) {
			var line = this.lines[i];
			if (line.Checked == true) {
				idArray.push(line.Id);
			}
		}
		return idArray;
	},
	
	CheckCtrlLine: function(id)
	{
		for (var i = this.Length-1; i >= 0; i--) {
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
		for (var i = this.Length-1; i >= 0; i--) {
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
			for (var i = 0; i < this.Length; i++) {
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
		for (var i = this.Length-1; i >= 0; i--) {
			this.lines[i].Uncheck();
		}
		this.prev = -1;
	}
}