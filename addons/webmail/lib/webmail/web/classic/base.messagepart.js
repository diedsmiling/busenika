/*
Classes:
	CHeaders
	CAttachments
	CMessage
	CVResizer
	CFullHeadersViewer
*/

function CHeaders()
{
	this.container = document.getElementById('message_headers');
	this.table = document.getElementById('td_message_headers');
	this.to = document.getElementById('message_to');
	this.date = document.getElementById('message_date');
	this.hider = document.getElementById('hider');
	this.width = this.table.offsetWidth;
	this.height = this.table.offsetHeight;
	
	this.imgHeight = 0;
	this.imgtable = document.getElementById('show_pictures_sender_table');
	if (this.imgtable && this.imgtable.offsetHeight > 0)
	{
		this.imgHeight = this.imgtable.offsetHeight;
	}
}

CHeaders.prototype = 
{
	updateWidth: function()
	{
		this.table.style.width = this.width + 'px';
		this.container.style.width = this.width + 'px';
	},
		
	updateHeight: function()
	{
		this.table.style.height = this.height + 'px';
		this.container.style.height = (this.height - this.imgHeight) + 'px';
	},
	
	updateSize: function()
	{
		this.updateWidth();
		this.updateHeight();
	}
}

function CAttachments(minVertical)
{
	if (!minVertical) minVertical = 140
	this.container = document.getElementById('attachments');
	this.table = document.getElementById('td_attachments');
	this.width = minVertical;
	this.height = this.table.offsetHeight;
}

CAttachments.prototype = 
{
	updateWidth: function()
	{
		parent.rVer = this.width;
		this.table.style.width = this.width + 'px';
		this.container.style.width = this.width + 'px';
	},
		
	updateHeight: function()
	{
		this.table.style.height = this.height + 'px';
		this.container.style.height = this.height + 'px';
	},
	
	updateSize: function()
	{
		this.updateWidth();
		this.updateHeight();
	}
}

function CMessage()
{
	this.container = document.getElementById('message');
	this.table = document.getElementById('td_message');
	this.width = this.table.offsetWidth;
	this.height = this.table.offsetHeight;
}

CMessage.prototype = 
{
	updateWidth: function()
	{
		this.table.style.width = this.width + 'px';
		this.container.style.width = this.width + 'px';
	},
		
	updateHeight: function()
	{
		this.table.style.height = this.height + 'px';
		this.container.style.height = this.height + 'px';
	},
	
	updateSize: function()
	{
		this.updateWidth();
		this.updateHeight();
	}
}

function CVResizer()
{
	this.container = document.getElementById('vert_resizer');
	this.table = document.getElementById('td_vert_resizer');	
	this.plain = document.getElementById('wm_mail_container');	
	this.width = 1;
	this.height = this.table.offsetHeight;
	this.x = 1;
	this.limit_left = 5;
	this.limit_right = 600;
	this.container.style.cursor = 'e-resize';
	this.container.innerHTML = '&nbsp;';
	
	var obj = this;
	this.table.onmousedown = function(e)
	{
		e = e ? e : window.event;
		obj.beginMoving(e);
		return false; //don't select content in Opera
	}
}

CVResizer.prototype = 
{
	updateWidth: function()
	{
		this.container.style.width = this.width + 'px';
		this.table.style.width = this.width + 'px';
	},
		
	updateHeight: function()
	{
		this.container.style.height = this.height + 'px';
		this.table.style.height = this.height + 'px';
	},
	
	updateSize: function()
	{
		this.updateWidth();
		this.updateHeight();
	},
	
	
	beginMoving: function(e)
	{
		//don't select content in IE
		document.onselectstart = function() {return false;}
		document.onselect = function() {return false;}
		this.x = e.clientX;
		var obj = this;
		this.plain.style.cursor = 'e-resize';
		
		this.plain.onmousemove = function(e) {
			e = e ? e : window.event;
			obj.processMoving(e.clientX);
		}
		this.plain.onmouseup = function() {	obj.endMoving(); }
		
		this.table.onmouseout = function(e)
		{
		  	e = e ? e : window.event;
			obj.processMoving(e.clientX);
		}
		
	},
	
	processMoving: function(mouse_x)	
	{
		if (mouse_x > this.limit_right) {
			this.x = this.limit_right;
		} else {
			this.x = (mouse_x <= this.limit_left) ? this.limit_left : mouse_x;
		}
		eval('ResizeElements(\'height\')');
	},
	
	endMoving: function()
	{
		document.onselectstart = function() {}
		document.onselect = function() {}
		this.plain.onmousemove = '';
		this.plain.onmouseup = '';
		this.table.onmouseout = '';
		this.plain.style.cursor = '';
		this.width = 1;
		this.updateWidth();
	}
	
}

function CFullHeadersViewer()
{
	this._headersCont = document.getElementById('headersCont');
	this._headersDiv = document.getElementById('headersDiv');
	this._control = document.getElementById('fullheadersControl');
	this._isShow = false;
}

CFullHeadersViewer.prototype = 
{
	Show: function()
	{
		if (!this._isShow)
		{
			var height = GetHeight();
            var width = GetWidth();
            
			var win_height = height*3/5;
			var win_width = width*3/5;		
			
			this._control.innerHTML = Lang.HideFullHeaders;
			this._headersCont.className = 'wm_headers';

			this._headersCont.style.width = win_width + 'px';
			this._headersCont.style.height = win_height + 'px';
			this._headersCont.style.top = (height - win_height)/2 + 'px';
			this._headersCont.style.left = (width - win_width)/2 + 'px';
			this._headersDiv.style.width = win_width - 10 + 'px';
			this._headersDiv.style.height = win_height - 30 + 'px';		
		
			var obj = this;
			this._control.onclick = function()
			{
				obj.Hide();
			}
			this._isShow = true;
		}
		return false;
	},
	
	Hide: function()
	{
		this._isShow = false;
		var obj = this;
		this._control.onclick = function()
		{
			obj.Show();
		}
		this._headersCont.className = 'wm_hide';
		this._control.innerHTML = Lang.ShowFullHeaders;
		return false;
	}
}