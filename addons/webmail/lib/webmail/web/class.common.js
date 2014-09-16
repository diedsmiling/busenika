/*
 * Classes, prototypes:
 *  CBrowser
 *  CPopupMenu
 *  CPopupMenus
 *  CSearchForm
 *  CError
 *  CReport
 *  ReportPrototype
 *  CInformation
 *  CFadeEffect
 *  CValidate
 * 
 *  CCheckMail
 * 
 *  CScriptLoader
 *  CNetLoader
 *  CDictionary
 */

function CBrowser()
{
	this.Init = function()
	{
		var len = this.Profiles.length;
		for (var i = 0; i < len; i++) {
			if (this.Profiles[i].Criterion) {
				this.Name = this.Profiles[i].Id;
				this.Version = this.Profiles[i].Version();
				this.Allowed = this.Version >= this.Profiles[i].AtLeast;
     			break;
			}
   		};
		this.IE = (this.Name == 'Microsoft Internet Explorer');
		this.Opera = (this.Name == 'Opera');
		this.Mozilla = (this.Name == 'Mozilla' || this.Name == 'Firefox' || this.Name == 'Netscape' || this.Name == 'Chrome');
		this.Safari = (this.Name == 'Safari');
		this.Gecko = (this.Opera || this.Mozilla);
	};

	this.Profiles = [
		{
			Id: 'Opera',
			Criterion: window.opera,
			AtLeast: 8,
			Version: function() {
				var r = navigator.userAgent;
				var start1 = r.indexOf('Opera/');
				var start2 = r.indexOf('Opera ');
				if (-1 == start1) {
					var start = start2 + 6;
					var end = r.length;
				}
				else {
					var start = start1 + 6;
					var end = r.indexOf(' ');
				};
				r = parseFloat(r.slice(start, end));
				return r;
			}
		},
		{
			Id: 'Chrome',
			Criterion:
			(
				(navigator.appCodeName.toLowerCase() == 'mozilla') &&
				(navigator.appName.toLowerCase() == 'netscape') &&
				(navigator.product.toLowerCase() == 'gecko') &&
				(navigator.userAgent.toLowerCase().indexOf('chrome') != -1)
			),
			AtLeast: 0,
			Version: function() {
				return parseFloat(navigator.userAgent.split('Chrome/').reverse().join('Chrome/'));
			}
		},
		{
			Id: 'Safari',
			Criterion:
			(
				(navigator.appCodeName.toLowerCase() == 'mozilla') &&
				(navigator.appName.toLowerCase() == 'netscape') &&
				(navigator.product.toLowerCase() == 'gecko') &&
				(navigator.userAgent.toLowerCase().indexOf('safari') != -1)
			),
			AtLeast: 1.2,
			Version: function() {
				var r = navigator.userAgent;
				return parseFloat(r.split('Version/').reverse().join(' '));
			}
		},
		{
			Id: 'Firefox',
			Criterion:
			(
				(navigator.appCodeName.toLowerCase() == 'mozilla') &&
				(navigator.appName.toLowerCase() == 'netscape') &&
				(navigator.product.toLowerCase() == 'gecko') &&
				(navigator.userAgent.toLowerCase().indexOf('firefox') != -1)
			),
			AtLeast: 1,
			Version: function() {
				return parseFloat(navigator.userAgent.split('Firefox/').reverse().join('Firefox/'));
			}
		},
		{
			Id: 'Netscape',
			Criterion:
			(
				(navigator.appCodeName.toLowerCase() == 'mozilla') &&
				(navigator.appName.toLowerCase() == 'netscape') &&
				(navigator.product.toLowerCase() == 'gecko') &&
				(navigator.userAgent.toLowerCase().indexOf('netscape') != -1)
			),
			AtLeast: 7,
			Version: function() {
				var r = navigator.userAgent.split(' ').reverse().join(' ');
				r = parseFloat(r.slice(r.indexOf('/')+1,r.indexOf(' ')));
				return r;
			}
		},
		{
			Id: 'Mozilla',
			Criterion:
			(
				(navigator.appCodeName.toLowerCase() == 'mozilla') &&
				(navigator.appName.toLowerCase() == 'netscape') &&
				(navigator.product.toLowerCase() == 'gecko') &&
				(navigator.userAgent.toLowerCase().indexOf('mozilla') != -1)
			),
			AtLeast: 1,
			Version: function() {
				var r = navigator.userAgent;
				return parseFloat(r.split('Firefox/').reverse().join('Firefox/'));
			}
		},
		{
			Id: 'Microsoft Internet Explorer',
			Criterion:
			(
				(navigator.appName.toLowerCase() == 'microsoft internet explorer') &&
				(navigator.appVersion.toLowerCase().indexOf('msie') != 0) &&
				(navigator.userAgent.toLowerCase().indexOf('msie') != 0) &&
				(!window.opera)
			),
			AtLeast: 5,
			Version: function() {
				var r = navigator.userAgent.toLowerCase();
				r = parseFloat(r.slice(r.indexOf('msie')+4,r.indexOf(';',r.indexOf('msie')+4)));
				return r;
			}
		}
	];

	this.Init();
}

function CPopupMenu(popup_menu, popup_control, menu_class, popup_move, popup_title, move_class, move_press_class, title_class, title_over_class)
{
	this.popup = popup_menu;
	this.control = popup_control;
	this.move = popup_move;
	this.title = popup_title;
	this.menu_class = menu_class;
	this.move_class = move_class;
	this.move_press_class = move_press_class;
	this.title_class = title_class;
	this.title_over_class = title_over_class;
}

function CPopupMenus()
{
	this.items = Array();
	this.isShown = 0;
}

CPopupMenus.prototype = {
	getLength: function()
	{
		return this.items.length;
	},
	
	addItem: function(popup_menu, popup_control, menu_class, popup_move, popup_title, move_class, move_press_class, title_class, title_over_class)
	{
		this.items.push(new CPopupMenu(popup_menu, popup_control, menu_class, popup_move, popup_title, move_class, move_press_class, title_class, title_over_class));
		this.hideItem(this.getLength() - 1);
	},
	
	showItem: function(item_id)
	{
		this.hideAllItems();
		var item = this.items[item_id];
		var bounds = GetBounds(this.items[item_id].move);
		item.popup.style.left = bounds.Left + 'px';
		item.popup.style.top = bounds.Top + bounds.Height + 'px';

		item.popup.className = item.menu_class;
		if (item.title_class && item.title_class != '') {
			item.control.className = item.title_class;
			item.title.className = item.title_class;
		};
		if (item.move_press_class && item.move_press_class != '')
			item.move.className = item.move_press_class;
		var obj = this;
		item.control.onclick = function() {
			obj.hideItem(item_id);
		};
		var borders = 1;
		if (item.title_over_class != '') {
			item.control.onmouseover = function(){};
			item.control.onmouseout = function(){};
			item.title.onmouseover = function(){};
			item.title.onmouseout = function(){};
			borders = 2;
		};
		this.isShown = 2;
		item.popup.style.width = 'auto';
		var pOffsetWidth = item.popup.offsetWidth;
		var cOffsetWidth = item.control.offsetWidth;
		if (item.control == item.title) {
			var tOffsetWidth = 0;
		}
		else {
			var tOffsetWidth = item.title.offsetWidth;
		};
		if (pOffsetWidth < (cOffsetWidth + tOffsetWidth - borders)) {
			item.popup.style.width = (cOffsetWidth + tOffsetWidth - borders) + 'px';
		}
		else {
			item.popup.style.width = (pOffsetWidth + borders) + 'px';
		};

		var pOffsetHeight = item.popup.offsetHeight;
		var height = GetHeight();
		if (pOffsetHeight > height*2/3)
			item.popup.style.height = Math.round(height*2/3) + 'px';
		
	},
	
	hideItem: function(item_id)
	{
		this.items[item_id].popup.className = 'wm_hide';
		if (this.items[item_id].move_class && this.items[item_id].move_class != '' && this.items[item_id].move.className != 'wm_hide')
			this.items[item_id].move.className = this.items[item_id].move_class;
		var obj = this;
		this.items[item_id].control.onclick = function() {
			obj.showItem(item_id);
		};
		if (obj.items[item_id].title_over_class != ''){
			this.items[item_id].control.onmouseover = function() {
				obj.items[item_id].title.className = obj.items[item_id].title_over_class; 
				obj.items[item_id].control.className = obj.items[item_id].title_over_class;
			};
			this.items[item_id].control.onmouseout = function() {
				obj.items[item_id].title.className = obj.items[item_id].title_class; 
				obj.items[item_id].control.className = obj.items[item_id].title_class; 
			};
			this.items[item_id].title.onmouseover = function() {
				obj.items[item_id].title.className = obj.items[item_id].title_over_class; 
			};
			this.items[item_id].title.onmouseout = function() {
				obj.items[item_id].title.className = obj.items[item_id].title_class; 
			}
		}
	},
	
	hideAllItems: function()
	{
		for (var i = this.getLength() - 1; i >= 0; i--) {
			this.hideItem(i);
		};
		this.isShown = 0;
	},
	
	checkShownItems: function()
	{
		if (this.isShown == 1) {
			this.hideAllItems()
		};
		if (this.isShown == 2) {
			this.isShown = 1;
		}
	}
};

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
			};
			this.control.onmouseover = function() {
				obj.control.className = 'wm_toolbar_search_item_over';
				obj.small_form.className = 'wm_toolbar_search_item_over';
			};
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
		this.control.onclick = function() {};
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
		};
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
			while(elem && elem.tagName != 'DIV') {
				if(elem.parentNode) {elem = elem.parentNode;}
				else {break;}
			};
			if (elem.id != this._bigFormId) {this.HideBigForm();}
		};
		if (this.isShown == 2)
			this.isShown = 1;
	}
};

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
	Show: function (msg, priorDelay)
	{
		this._messageObj.innerHTML = msg;
		this._controlObj.Show();
		this._controlObj.Resize();
		if (null != this._fadeObj) {
		    if (priorDelay) var interval = this._fadeObj.Go(this._containerObj, priorDelay);
		    else var interval = this._fadeObj.Go(this._containerObj, this._delay);
			if (this._name) {
				setTimeout(this._name + '.Hide()', interval);
			}
		}
		else {
			if (this._name) {
		        if (priorDelay) setTimeout(this._name + '.Hide()', priorDelay);
		        else setTimeout(this._name + '.Hide()', this._delay);
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
		if (null != this._fadeObj) {
			this._fadeObj.SetOpacity(1);
		}
	},
	
	Resize: function ()
	{
		this._controlObj.Resize();		
	}
};

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
		};
		tbl.style.left = (width - offsetWidth) + 'px';
		tbl.style.top = this.GetScrollY() + 'px';
	},

	GetScrollY: function()
	{
		var scrollY = 0;
		if (document.body && typeof document.body.scrollTop != 'undefined') {
			scrollY += document.body.scrollTop;
			if (scrollY == 0 && document.body.parentNode && typeof document.body.parentNode != 'undefined') {
				scrollY += document.body.parentNode.scrollTop;
			}
		}
		else if (typeof window.pageXOffset != 'undefined')  {
			scrollY += window.pageYOffset;
		};
		return scrollY;
	}
};

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
		for(var i=0; i<=iCount; i++) {
			setTimeout(this._name + '.SetOpacity('+ (1 - diff*i) +')', delay + interval*i);
		};
		return delay + interval*iCount;
	},
	
	SetOpacity: function (opacity)
	{
		var elem = this._elem;
		// Internet Exploder 5.5+
		if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) {
			opacity *= 100;
			var oAlpha = elem.filters['DXImageTransform.Microsoft.alpha'] || elem.filters.alpha;
			if (oAlpha) {
				oAlpha.opacity = opacity;
			}
			else {
				elem.style.filter += 'progid:DXImageTransform.Microsoft.Alpha(opacity='+opacity+')';
			}
		}
		else {
			elem.style.opacity = opacity;		// CSS3 compliant (Moz 1.7+, Safari 1.2+, Opera 9)
			elem.style.MozOpacity = opacity;	// Mozilla 1.6-, Firefox 0.8
			elem.style.KhtmlOpacity = opacity;	// Konqueror 3.1, Safari 1.1
		}
	}
};

function CValidate()
{
}

CValidate.prototype = 
{
    IsEmpty : function (strValue)
    {
        if(strValue.replace(/\s+/g,'') == '') {
            return true;
        };
        return false;
    },
    
    HasEmailForbiddenSymbols : function (strValue)
    {
        if(strValue.match(/[^A-Z0-9\"!#\$%\^\{\}`~&'\+-=_@\.]/i)) {
            return true;
        };
        return false;
    },
    
    IsCorrectEmail : function (strValue)
    {
        if(strValue.match(/^[A-Z0-9\"!#\$%\^\{\}`~&'\+-=_\.]+@[A-Z0-9\.-]+$/i)) {
            return true;
        };
        return false;
    },
    
    IsCorrectServerName : function (strValue)
    {
        if(!strValue.match(/[^A-Z0-9\.-]/i))
        {
            return true;
        }
        return false;
    },
    
    IsPositiveNumber : function (intValue)
    {
        if(isNaN(intValue) || intValue <= 0 || Math.round(intValue) != intValue) {
            return false;
        };
        return true;
    },
    
    IsPort : function (intValue)
    {
        if(this.IsPositiveNumber(intValue) && intValue <= 65535) {
            return true;
        };
        return false;
    },
    
    HasSpecSymbols : function (strValue)
    {
        if(strValue.match(/["\/\\*?<>|:]/)) {
            return true;
        };
        return false;
    },
    
    IsCorrectFileName : function (strValue)
    {
        if(!this.HasSpecSymbols(strValue)) {
            if(strValue.match(/^(CON|AUX|COM1|COM2|COM3|COM4|LPT1|LPT2|LPT3|PRN|NUL)$/i)) {
                return false;
            }
            else {
                return true;
            }
        };
        return false;
    },
    
    CorrectWebPage : function (strValue)
    {
        return strValue.replace(/^[\/;<=>\[\\#\?]+/g,'');
    },
    
    HasFileExtention : function (strValue, strExtension)
    {           
        if( strValue.substr(strValue.length - strExtension.length - 1,strExtension.length + 1).toLowerCase() == '.'+strExtension.toLowerCase()) {
            return true;
        };
        return false;
    }
};

function CCheckMail(type)
{
	this.isBuilded = false;
	if (type)
		this._type = type;
	else
		this._type = 0;
	this.started = false;
	
	this._url = CheckMailUrl;
	this._email = '';
	this._msgsCount = 0;
	this._preText = '';
	
	this._form = null;
	this._typeObj = null;
	
	this._mainContainer = null;
	this._infomation = null;
	this._message = null;
	this._progressBarUsed = null;
}

CCheckMail.prototype = {
	Start: function ()
	{
		if (this.isBuilded) {
			if (this._type == 0) this._infomation.Show();
		}
		else {
			this.Build();
		};
		this._preText = '';
		this.SetText(Lang.LoggingToServer);
		this._msgsCount = 1;
		this.UpdateProgressBar(0);
		this._msgsCount = 0;
		this._typeObj.value = this._type;
		this._form.action = this._url + '?param=' + Math.random();
		this._form.submit();
		this.started = true;
	},
	
	SetAccount: function (account)
	{
		this._email = account;
		this._mainContainer.className = 'wm_connection_information';
		this._preText = '<b>' + this._email + '</b><br/>';
	},

	SetFolder: function (folderName, msgsCount)
	{
		this._folderName = folderName;
		this._msgsCount = msgsCount;
		this._preText = '';
		if (this._email.length > 0) this._preText += '<b>' + this._email + '</b><br/>';
		this._preText += Lang.Folder + ' <b>' + this._folderName + '</b><br/>';
	},
	
	SetText: function (text)
	{
		this._message.innerHTML = this._preText + text;
		if (this._type == 0) this._infomation.Resize();
	},
	
	DeleteMsg: function (msgNumber) {
		if (msgNumber == -1) {
			this.SetText(Lang.DeletingMessages);
		}
		else {
			this.SetText(Lang.DeletingMessage + ' #' + msgNumber + ' ' + Lang.Of + ' ' + this._msgsCount);
			this.UpdateProgressBar(msgNumber);
		}
	},
	
	SetMsgNumber: function (msgNumber)
	{
		if (msgNumber <= this._msgsCount) {
			this.SetText(Lang.RetrievingMessage + ' #' + msgNumber + ' ' + Lang.Of + ' ' + this._msgsCount);
		};
		this.UpdateProgressBar(msgNumber);
	},
	
	UpdateProgressBar: function (msgNumber)
	{
		if (this._msgsCount > 0) {
			var percent = Math.ceil((msgNumber - 1)*100/this._msgsCount);
			if (percent < 0) { percent = 0; }
			else if (percent > 100) { percent = 100; }
			this._progressBarUsed.style.width = percent + 'px';
		}
	},
	
	End: function ()
	{
		if (this._type == 0) this._infomation.Hide();
		this.started = false;
	},
	
	Build: function ()
	{
		/* iframe block building */
		var iframe = CreateChildWithAttrs(document.body, 'iframe', [['id', 'CheckMailIframe'], ['name', 'CheckMailIframe'], ['src', EmptyHtmlUrl], ['class', 'wm_hide']]);
		var frm = CreateChildWithAttrs(document.body, 'form', [['action', this._url], ['target', 'CheckMailIframe'], ['method', 'post'], ['id', 'CheckMailForm'], ['name', 'CheckMailForm'], ['class', 'wm_hide']]);
		this._typeObj = CreateChildWithAttrs(frm, 'input', [['name', 'Type'], ['value', this._type]]);
		this._form = frm;
		/* information block building for type 0 */
		if (this._type == 0) {
			var tbl = CreateChild(document.body, 'table');
			tbl.className = 'wm_connection_information';
			with (tbl.style) {
				position = 'absolute';
				top = '0px';
				right = '0px';
			};
			var tr = tbl.insertRow(0);
			var td = tr.insertCell(0);
			td.className = 'wm_info_message';
			this._message = CreateChild(td, 'span');
			tr = tbl.insertRow(1);
			td = tr.insertCell(0);
			var div = CreateChildWithAttrs(td, 'div', [['align', 'center']]);
			var divPB = CreateChildWithAttrs(div, 'div', [['class', 'wm_progressbar']]);
			this._progressBarUsed = CreateChildWithAttrs(divPB, 'div', [['class', 'wm_progressbar_used']]);
			this._infomation = new CInformation(tbl, 'wm_connection_information');
		};
		/* information block building for type 1 */
		if (this._type == 1) {
			var parent = document.getElementById('content');
			if (parent) {
				var tbl = CreateChild(parent, 'table');
			}
			else {
				var tbl = CreateChild(document.body, 'table');
			};
			tbl.className = 'wm_hide';
			this._mainContainer = tbl;
			tbl.style.marginTop = '30px';
			var tr = tbl.insertRow(0);
			var td = tr.insertCell(0);
			td.className = 'wm_connection_header';
			td.colSpan = '3';
			td.innerHTML = Lang.Connection;
			tr = tbl.insertRow(1);
			td = tr.insertCell(0);
			td.className = 'wm_connection_icon';
			td = tr.insertCell(1);
			td.className = 'wm_connection_message';
			td.align = 'center';
			this._message = td;
			td = tr.insertCell(2);
			td.className = 'wm_connection_empty';
			tr = tbl.insertRow(2);
			td = tr.insertCell(0);
			td.className = 'wm_connection_progressbar';
			td.colSpan = 3;
			var div = CreateChildWithAttrs(td, 'div', [['align', 'center']]);
			var div1 = CreateChildWithAttrs(div, 'div', [['class', 'wm_progressbar']]);
			this._progressBarUsed = CreateChildWithAttrs(div1, 'div', [['class', 'wm_progressbar_used']]);
		};
		/* it's builded! */
		this.isBuilded = true;
	}
};

function CScriptLoader()
{
	this.onLoad = null;
	this.loadedCount = 0;
	this.scriptsCount = 0;
	this._onLoad = null;
	this._scripts = new CDictionary();
}

CScriptLoader.prototype = {
	Load: function(urlArray, loadHandler)
	{
		this.onLoad = loadHandler;
		this.loadedCount = 0;
		this.scriptsCount = urlArray.length;
		if (this.scriptsCount == 0)
			this.onLoad.call();
		for (var i in urlArray)
			this.LoadItem(urlArray[i], this.ScriptLoadHandler);
	},
	
	ScriptLoadHandler: function()
	{
		this.loadedCount++;
		if (this.loadedCount == this.scriptsCount)
			this.onLoad.call();
	},
	
	LoadItem: function(url, loadHandler)
	{
		this._onLoad = loadHandler;
		var script = document.createElement('script');
		script.setAttribute('type', 'text/javascript');
		var obj = this;
		if (Browser.IE) {
			script.onreadystatechange = function () {
				if (obj._scripts.exists(this.src)) {
					if (this.readyState == 'complete' || this.readyState == 'loaded') {
						obj._scripts.remove(this.src);
						obj._onLoad.call(obj);
					}
				}
			}
		}
		else {
			script.onload = function () {
				obj._scripts.remove(this.src);
				obj._onLoad.call(obj);
			}
		}
		this._scripts.add(url, true);
		script.src = url;
		var HeadElements = document.getElementsByTagName('head');
		HeadElements[0].appendChild(script);
	}
};

function CNetLoader()
{
	this.Url = null;
	this.onLoad = null;
	this.onError = null;
	this.responseXML = null;
	this.responseText = null;
	this.ErrorDesc = null;
	this.Request = null;
	this.Log = '';
}

CNetLoader.prototype = {
	GetTransport: function()
	{
		var transport = null;
		if(window.XMLHttpRequest) {
			transport = new XMLHttpRequest();
		}
		else {
			if(window.ActiveXObject) {
				try {
					transport = new ActiveXObject('Msxml2.XMLHTTP');
				}
				catch (err) {
					try {
						transport = new ActiveXObject('Microsoft.XMLHTTP');
					}
					catch (err2) {
					}
				}
			}
		};
		return transport;
	},

	LoadXMLDoc: function(Url, PostParams, onLoad, onError)
	{
		this.Url = Url;
		this.onLoad = onLoad;
		this.onError = onError;
		var Request = this.GetTransport();

		if(Request) {
			try {
				Request.open('POST', this.Url, true);
				Request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				var obj = this;
				Request.onreadystatechange = function() { obj.OnReadyState(Request); };
				Request.send(PostParams);
			}
			catch (err) {
				this.ErrorDesc = Lang.ErrorRequestFailed;
				this.onError.call(this);
			}
		}
		else {
			this.ErrorDesc = Lang.ErrorAbsentXMLHttpRequest;
			this.onError.call(this);
		}
		this.Request = Request;
	},
	
	OnReadyState: function(Request)
	{
		var ReadyStateComplete = 4;
		var Ready = Request.readyState;
		if(Ready == ReadyStateComplete) {
			var HttpStatus;
			try {
				if(Request.status != undefined) {
					HttpStatus = Request.status;
				}
				else {
					HttpStatus = 13030;
				}
			}
			catch(e) {
				// 13030 is the custom code to indicate the condition -- in Mozilla/FF --
				// when the o object's status and statusText properties are
				// unavailable, and a query attempt throws an exception.
				HttpStatus = 13030;
			};
			if(HttpStatus == 200 || HttpStatus == 0) {
				this.responseXML = Request.responseXML;
				this.responseText = Request.responseText;
				this.onLoad.call(this);
			}
			else if (HttpStatus != 13030) {
				this.ErrorDesc = Lang.ErrorConnectionFailed + '\n' + HttpStatus + '\n' + Request.responseText;
				this.onError.call(this);
			}
		}
	},
	
	CheckRequest: function ()
	{
		if (null != this.Request) {
			this.Request.onreadystatechange = null;
			this.Request.abort();
		}
	}
};

function CDictionary()
{
	this.count = 0;
	this.Obj = new Object();
}

CDictionary.prototype = {
	exists: function (sKey)
	{
		return (this.Obj[sKey])?true:false;
	},

	add: function (sKey, aVal)
	{
		var K = String(sKey);
		if(this.exists(K)) return false;
		this.Obj[K] = aVal;
		this.count++;
		return true;
	},

	remove: function (sKey)
	{
		var K = String(sKey);
		if (!this.exists(K)) return false;
		delete this.Obj[K];
		this.count--;
		return true;
	},

	removeAll: function ()
	{
		for(var key in this.Obj) delete this.Obj[key];
		this.count = 0;
	},

	values: function ()
	{
		var Arr = new Array();
		for(var key in this.Obj) Arr[Arr.length] = this.Obj[key];
		return Arr;
	},

	keys: function ()
	{
		var Arr = new Array();
		for (var key in this.Obj) Arr[Arr.length] = key;
		return Arr;
	},

	items: function ()
	{
		var Arr = new Array();
		for (var key in this.Obj) {
			var A = new Array(key,this.Obj[key]);
			Arr[Arr.length] = A;
		};
		return Arr;
	},

	getVal: function(sKey)
	{
		var K = String(sKey);
		return this.Obj[K];
	},

	setVal: function(sKey, aVal)
	{
		var K = String(sKey);
		if (this.exists(K))
			this.Obj[K] = aVal;
		else
			this.add(K,aVal);
	},

	setKey: function (sKey, sNewKey)
	{
		var K = String(sKey);
		var Nk = String(sNewKey);
		if (this.exists(K)) {
			if (!this.exists(Nk)) {
				this.add(Nk, this.getVal(K));
				this.remove(K);
			}
		}
		else if (!this.exists(Nk))
			this.add(Nk, null);
	}
};
