/*
Classes, prototypes:
	CError
	CReport
	ReportPrototype
	CInformation
	CFadeEffect
*/

function CError(name, skinName)
{
	this._skinName = skinName;
	this._name = name;
	this._containerObj = null;
	this._messageObj = null;
	this._imgObj = null;
	this._controlObj = null;
	this._fadeObj = null;
	this._delay = 10000;

	this.ChangeSkin = function (newSkin)
	{
		this._skinName = newSkin;
		this._imgObj.src = 'skins/' + this._skinName + '/error.gif';
	},
	
	this.Build = function ()
	{
		var tbl = CreateChild(document.body, 'table');
		tbl.className = 'wm_hide';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.className = 'wm_info_image';
		var img = CreateChild(td, 'img');
		img.src = 'skins/' + this._skinName + '/error.gif';
		this._imgObj = img;
		td = tr.insertCell(1);
		td.className = 'wm_info_message';
		this._containerObj = tbl;
		this._messageObj = CreateChild(td, 'span');
		this._controlObj = new CInformation(tbl, 'wm_error_information');
	}
}

function CReport(name)
{
	this._name = name;
	this._containerObj = null;
	this._messageObj = null;
	this._controlObj = null;
	this._fadeObj = null;
	this._delay = 5000;

	this.Build = function ()
	{
		var tbl = CreateChild(document.body, 'table');
		tbl.className = 'wm_hide';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.className = 'wm_info_message';
		this._containerObj = tbl;
		this._messageObj = CreateChild(td, 'span');
		this._controlObj = new CInformation(tbl, 'wm_report_information');
	}
}

ReportPrototype = 
{
	Show: function (msg)
	{
		this._messageObj.innerHTML = msg;
		this._controlObj.Show();
		this._controlObj.Resize();
		if (null != this._fadeObj)
		{
			var interval = this._fadeObj.Go(this._containerObj, this._delay);
			if (this._name)
			{
				setTimeout(this._name + '.Hide()', interval);
			}
		}
	},
	
	SetFade: function (fadeObj)
	{
		this._fadeObj = fadeObj;
	},
	
	Hide: function ()
	{
		this._controlObj.Hide();
		if (null != this._fadeObj)
		{
			this._fadeObj.SetOpacity(1);
		}
	},
	
	Resize: function ()
	{
		this._controlObj.Resize();		
	}
}

CReport.prototype = ReportPrototype;
CError.prototype = ReportPrototype;

/* for control placement and displaying of information block */
function CInformation(cont, cls)
{
	this._mainContainer = cont;
	this._containerClass = cls;
}

CInformation.prototype = {
	Show: function ()
	{
		this._mainContainer.className = this._containerClass;
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

function CFadeEffect(name)
{
	this._name = name;
	this._elem = null;
}

CFadeEffect.prototype = 
{
	Go: function (elem, delay)
	{
		this._elem = elem;
		var interval = 50;
		var iCount = 10;
		var diff = 1/iCount;
		for(var i=0; i<=iCount; i++)
		{
			setTimeout(this._name + '.SetOpacity('+ (1 - diff*i) +')', delay + interval*i);
		}
		return delay + interval*iCount;
	},
	
	SetOpacity: function (opacity)
	{
		var elem = this._elem;
		// Internet Exploder 5.5+
		if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5)
		{
			opacity *= 100;
			var oAlpha = elem.filters['DXImageTransform.Microsoft.alpha'] || elem.filters.alpha;
			if (oAlpha)
			{
				oAlpha.opacity = opacity;
			}
			else
			{
				elem.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity="+opacity+")";
			}
		}
		else
		{
			elem.style.opacity = opacity;		// CSS3 compliant (Moz 1.7+, Safari 1.2+, Opera 9)
			elem.style.MozOpacity = opacity;	// Mozilla 1.6-, Firefox 0.8
			elem.style.KhtmlOpacity = opacity;	// Konqueror 3.1, Safari 1.1
		}
	}
}
