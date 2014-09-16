/* for control placement and and displaying of information block */
function CInfo(cont, cls, msg, img, skinName)
{
	this._mainContainer = cont;
	this._message = msg;
	this._containerClass = cls;
	this._image = img;
	this._isError = false;
	this._skinName = skinName;
}

CInfo.prototype = {

	SetInfo: function (test)
	{
		if (this._isError)
		{
			this.Class('wm_error_information', 'wm_info_image', 'error.gif');
		}
		else
		{
			this.Class('wm_information', 'wm_info_image', 'info.gif');
		}
		this._message.innerHTML = test;
	},
	
	Class: function (newClassName, imageClass, imageName)
	{
		this._containerClass = newClassName;
		this._imageClass = imageClass;
		this._imageName = imageName;
	},

	Show: function ()
	{
		this._mainContainer.className = this._containerClass;
		this._image.className = this._imageClass;
		this._image.src = 'skins/' + this._skinName + '/' + this._imageName;
		this.Resize();
	},
	
	Hide: function ()
	{
		this._mainContainer.className = 'wm_hide';
	},

	Resize: function ()
	{
		var tbl = this._mainContainer;
		tbl.style.width = 'auto';
		var offsetWidth = tbl.offsetWidth;
		var width = GetWidth();
		if (offsetWidth >  0.4 * width) {
			tbl.style.width = '40%';
			offsetWidth = tbl.offsetWidth;
		}
		tbl.style.left = (width - offsetWidth) + 'px';
		tbl.style.top = this.GetScrollY() + 'px';
	},

	GetScrollY: function()
	{
		var scrollY = 0;
		if (document.body && typeof document.body.scrollTop != "undefined")
		{
			scrollY += document.body.scrollTop;
			if (scrollY == 0 && document.body.parentNode && typeof document.body.parentNode != "undefined")
			{
				scrollY += document.body.parentNode.scrollTop;
			}
		} else if (typeof window.pageXOffset != "undefined")  {
			scrollY += window.pageYOffset;
		}
		return scrollY;
	}
}