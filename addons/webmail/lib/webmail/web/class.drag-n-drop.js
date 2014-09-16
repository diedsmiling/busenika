/*
Classes:
	CDragNDrop
*/

function CDragNDrop(langField, dropImg, notDropImg, skinName) {
	this._selection = null;
	this._langField = langField;
	this._dropImg = dropImg;
	this._notDropImg = notDropImg;
	this._skinName = skinName;
	this._dragObjects = [];
	this._dragCount = 0;
	this._dropObjects = [];
	this._dropCount = 0;
	this._handle = CreateChildWithAttrs(document.body, 'div', [['class', 'wm_hide']]);
	this._handleImg = CreateChildWithAttrs(this._handle, 'img', [['src', 'skins/' + this._skinName + '/' + this._notDropImg]]);
	this._dropHandleImg = CreateChildWithAttrs(this._handle, 'img', [['src', 'skins/' + this._skinName + '/' + this._dropImg], ['class', 'wm_hide']]);
	this._handleText = CreateChild(this._handle, 'span');
	this._dragId = '';
	this._dropId = '';
	this.doMoveToInbox = true;
	this._inboxId = '';
	this._x1 = 0;
	this._y1 = 0;
	this._x2 = 0;
	this._y2 = 0;
	this.first = true;
}

CDragNDrop.prototype = {
	SetMoveToInbox: function (doMoveToInbox)
	{
		this.doMoveToInbox = doMoveToInbox;
	},
	
	SetInboxId: function (id)
	{
		this._inboxId = id;
	},
	
	SetSelection: function (selection)
	{
		this._selection = selection;
		if (null == selection) {
			this._dragObjects = [];
			this._dragCount = 0;
		}
	},
	
	AddDragObject: function (element)
	{
		var obj = this;
		element.onmousedown = function(e)
		{
			e = e ? e : event;
			if (e.button != 2 && e.button != 3) {
				obj.RequestDrag(e, this);
			}
			return false;
		}
		this._dragObjects[this._dragCount] = element;
		this._dragCount++;
	},
	
	SetCoordinates: function (element)
	{
		var bounds = GetBounds(element);
		element._x1 = bounds.Left;
		element._y1 = bounds.Top;
		element._x2 = bounds.Left + bounds.Width;
		element._y2 = bounds.Top + bounds.Height;
		if (this._x1 == 0 && this._y1 == 0 && this._x2 == 0 && this._y2 == 0) {
			this._x1 = element._x1;
			this._y1 = element._y1;
			this._x2 = element._x2;
			this._y2 = element._y2;
		} else {
			if (this._x1 > element._x1) {
				this._x1 = element._x1;
			}
			if (this._y1 > element._y1) {
				this._y1 = element._y1;
			}
			if (this._x2 < element._x2) {
				this._x2 = element._x2;
			}
			if (this._y2 < element._y2) {
				this._y2 = element._y2;
			}
		}
	},
	
	AddDropObject: function (element)
	{
		this.SetCoordinates(element);
		this._dropObjects[this._dropCount] = element;
		this._dropCount++;
	},
	
	Resize: function ()
	{
		this._x1 = 0;
		this._y1 = 0;
		this._x2 = 0;
		this._y2 = 0;
		for (var i=0; i<this._dropCount; i++) {
			this.SetCoordinates(this._dropObjects[i]);
		}
	},
	
	CleanDropObjects: function ()
	{
		this._dropObjects = [];
		this._dropCount = 0;
	},
	
	Ready: function ()
	{
		if (null == this._selection) return false;
		if (0 == this._dragCount) return false;
		if (0 == this._dragId.length) return false;
		return true;
	},
	
	RequestDrag: function (e, element)
	{
		if (this.doMoveToInbox || -1 == element.id.indexOf(this._inboxId)) {
			if (!e.ctrlKey && !e.shiftKey) {
				this._dragId = element.id;
				element.blur();
				var obj = this;
				element.onmouseout = function (e)
				{
					e = e ? e : event;
					obj.StartDrag(e, this);
				}
			}
		}
	},
	
	StartDrag: function (e, element)
	{
		element.onmouseout = function () {}
		if (this.Ready()) {
			var number = this._selection.DragItemsNumber(this._dragId);
			var handle = this._handle;
			handle.className = 'wm_drag_handle';
			handle.style.top = (e.clientY + 5) + 'px';
			handle.style.left = (e.clientX + 5) + 'px';
			this._handleText.innerHTML = number + ' ' + Lang[this._langField];
			this._handleImg.src = 'skins/' + this._skinName + '/' + this._notDropImg;
			var obj = this;
			document.body.onmousemove = function(e)
			{
	   			e = e ? e : event;
				obj.ProcessDrag(e); 
			}
			document.body.onmouseup = function()
			{
				obj.EndDrag();
			}
		}
	},
	
	ProcessDrag: function (e)
	{
		var x = e.clientX;
		var y = e.clientY;
		with (this._handle.style) {
			top = (e.clientY + 5) + 'px';
			left = (e.clientX + 5) + 'px';
		}
		if (x > this._x1 && x < this._x2 && y > this._y1 && y < this._y2) {
			for (var i=0; i<this._dropCount; i++) {
				var element = this._dropObjects[i];
				if (x > element._x1 && x < element._x2 && y > element._y1 && y < element._y2) {
					if (-1 == this._dragId.indexOf(element.id) && (this.doMoveToInbox || this._inboxId != element.id)) {
						this._dropId = element.id;
						this._handleImg.src = 'skins/' + this._skinName + '/' + this._dropImg;
						document.body.style.cursor = 'pointer'; 
					} else {
						this._dropId = '';
						this._handleImg.src = 'skins/' + this._skinName + '/' + this._notDropImg;
						document.body.style.cursor = 'auto'; 
					}
				}
			}
		} else {
			this._dropId = '';
			this._handleImg.src = 'skins/' + this._skinName + '/' + this._notDropImg;
			document.body.style.cursor = 'auto'; 
		}
	},
	
	EndDrag: function ()
	{
		if (this._dropId.length > 0) {
			MoveToFolderHandler(this._dropId);
			this.first = false;
		}
		document.body.style.cursor = 'auto'; 
		this._handle.className = 'wm_hide';
		this._dragId = '';
		this._dropId = '';
		document.body.onmousemove = function () { }
		document.body.onmouseup = function () { }
	}
}