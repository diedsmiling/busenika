/*
Classes:
	CVerticalResizer
	CHorizontalResizer
*/

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


