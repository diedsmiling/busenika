function MakeOpaqueOnSelect(element)
{
	if (Browser.IE && Browser.Version < 7) {
		var iframe = CreateChildWithAttrs(element, 'iframe', 
			[
				['src', EmptyHtmlUrl],
				['scrolling', 'no'],
				['frameborder', '0'],
				['class', 'wm_for_ie_select']
			]
		);
	}
}

function GetBorderWidth(style, width)
{
	if (style == 'none') {
		return 0;
	}
	else {
		var floatWidth = parseFloat(width);
		if (isNaN(floatWidth)) {
			return 0;
		}
		else {
			return Math.round(floatWidth);
		}
	}
}

function GetBorders(element)
{
	if (Browser.Mozilla) {
		var top = GetBorderWidth(ReadStyle(element, 'border-top-style'), ReadStyle(element, 'border-top-width'));
		var right = GetBorderWidth(ReadStyle(element, 'border-right-style'), ReadStyle(element, 'border-right-width'));
		var bottom = GetBorderWidth(ReadStyle(element, 'border-bottom-style'), ReadStyle(element, 'border-bottom-width'));
		var left = GetBorderWidth(ReadStyle(element, 'border-left-style'), ReadStyle(element, 'border-left-width'));
	}
	else {
		var top = GetBorderWidth(ReadStyle(element, 'borderTopStyle'), ReadStyle(element, 'borderTopWidth'));
		var right = GetBorderWidth(ReadStyle(element, 'borderRightStyle'), ReadStyle(element, 'borderRightWidth'));
		var bottom = GetBorderWidth(ReadStyle(element, 'borderBottomStyle'), ReadStyle(element, 'borderBottomWidth'));
		var left = GetBorderWidth(ReadStyle(element, 'borderLeftStyle'), ReadStyle(element, 'borderLeftWidth'));
	};
	return {Top: top, Right: right, Bottom: bottom, Left: left}
}

function ReadStyle(element, property)
{
	if (element.style[property]) {
		return element.style[property];
	}
	else if (element.currentStyle) {
		return element.currentStyle[property];
	}
	else if (document.defaultView && document.defaultView.getComputedStyle) {
		var style = document.defaultView.getComputedStyle(element, null);
		return style.getPropertyValue(property);
	}
	else {
		return null;
	}
}

//email parts for adding to contacts
function GetEmailParts(fullEmail)
{
	var quote1 = fullEmail.indexOf('"');
	var quote2 = fullEmail.indexOf('"', quote1+1);
	var leftBrocket = fullEmail.indexOf('<', quote2);
	var prevLeftBroket = -1;
	while (leftBrocket != -1) {
		prevLeftBroket = leftBrocket;
		leftBrocket = fullEmail.indexOf('<', leftBrocket+1);
	};
	leftBrocket = prevLeftBroket;
	var rightBrocket = fullEmail.indexOf('>', leftBrocket+1);
	var name = '';
	var email = '';
	if (leftBrocket == -1) {
		email = Trim(fullEmail);
	}
	else {
		if (quote1 == -1) {
			name = Trim(fullEmail.substring(0, leftBrocket));
		}
		else {
			name = Trim(fullEmail.substring(quote1+1, quote2));
		};
		email = Trim(fullEmail.substring(leftBrocket+1, rightBrocket));
	};
	return {Name: name, Email: email, FullEmail: fullEmail}
}

function PopupDataHelp()
{
	PopupWindow(DataHelpUrl, 'PopupDataHelp', 500, 400);
	return false;
}

function PopupContacts(wUrl)
{
	PopupWindow(wUrl, 'popupContacts', 300, 400);
	return false;
}

function PopupWindow(wUrl, wName, wWidth, wHeight)
{
	var wLeft, wTop;
	if (window.screen) { wTop = (screen.height - wHeight) / 2; } else { wTop = 200; }
	if (window.screen) { wLeft = (screen.width - wWidth) / 2; } else { wLeft = 200; }
	var wArgs = 'toolbar=no,location=no,directories=no,copyhistory=no,';
	wArgs += 'status=yes,scrollbars=yes,resizable=yes,';
	wArgs += 'width=' + wWidth + ',height=' + wHeight + ',left=' + wLeft + ',top=' + wTop;
	var shown = window.open(wUrl, wName, wArgs);
	shown.focus();
}

function SetBodyAutoOverflow(isAuto)
{
	var OverFlow = 'hidden';
	var Scroll = 'no';
	if (isAuto) {
		OverFlow = 'auto';
		Scroll = 'yes';
	}
	if (Browser.IE) {
		WebMail._html.style.overflow = OverFlow;
	}
	else {
		document.body.scroll = Scroll;
		document.body.style.overflow = OverFlow;
	}
}

function OpenURL(strUrl)
{
	var val = new CValidate();
	strUrl = val.CorrectWebPage(Trim(strUrl));
	if (strUrl.length > 0) {
		var newWin, strProt;
		strProt = strUrl.substr(0,4);
		if (strProt != "http" && strProt != "ftp:")
			strUrl = "http://" + strUrl;
		newWin = window.open(encodeURI(strUrl), null,"toolbar=yes,location=yes,directories=yes,status=yes,scrollbars=yes,resizable=yes,copyhistory=yes");
		newWin.focus();
	}
}

function Trim(str) {
    return str.replace(/^\s+/, '').replace(/\s+$/, '');
}

function HtmlEncode(source)
{
	return source.replace(/&/g, '&amp;').replace(/>/g, '&gt;').replace(/</g, '&lt;');
}

function HtmlDecode(source)
{
	return source.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
}

function HtmlEncodeBody(source)
{
	return source.replace(/]]>/g, '&#93;&#93;&gt;');
}

function HtmlDecodeBody(source)
{
	return source.replace(/&#93;&#93;&gt;/g, ']]>');
}

function GetCData(source, isBody)
{
	if (isBody) {
		return '<![CDATA[' + HtmlEncodeBody(source) + ']]>';
	}
	else {
		return '<![CDATA[' + HtmlEncode(source) + ']]>';
	}
}

function isEnter(ev)
{
	var key = -1;
	if (window.event)
		key = window.event.keyCode;
	else if (ev)
		key = ev.which;
	if (key == 13)
		return true;
	else
		return false;
}

function TextAreaLimit(ev, obj, count)
{
	ev = ev ? ev : window.event;
	var key = -1;
	if (window.event)
		key = window.event.keyCode;
	else if (ev)
		key = ev.which;
	if (key != 8 //backspace
	 && key != 13 //enter
	 && key != 16 //shift
	 && key != 17 //ctrl
	 && key != 18 //alt
	 && key != 35 //end
	 && key != 36 //home
	 && key != 37 //to the right
	 && key != 38 //up
	 && key != 39 //to the left
	 && key != 40 //down
	 && key != 46) { //delete
		if (!ev.ctrlKey && !ev.shiftKey) {
			if (obj.value.length >= count)
				return false;
		}
	};
	return true;
}

function isRightClick(ev)
{
	var key = -1;
	if (window.event)
		key = window.event.button;
	else if (ev)
		key = ev.which;
	if (key == 3 || key == 2)
		return true;
	else
		return false;
}

function GetWidth()
{
	var width = 1024;
	if (document.documentElement && document.documentElement.clientWidth)
		width = document.documentElement.clientWidth;
	else if (document.body.clientWidth)
		width = document.body.clientWidth;
	else if (self.innerWidth)
		width = self.innerWidth;
	return width;
}

function GetHeight()
{
	var height = 768;
	if (self.innerHeight)
		height = self.innerHeight;
	else if (document.documentElement && document.documentElement.clientHeight)
		height = document.documentElement.clientHeight;
	else if (document.body.clientHeight)
		height = document.body.clientHeight;
	return height;
}

function CreateChild(parent, tagName)
{
	var node = document.createElement(tagName);
	parent.appendChild(node);
	return node;
}

function CreateTextChild(parent, text)
{
	var node = document.createTextNode(text);
	parent.appendChild(node);
	return node;
}
    
function CreateChildWithAttrs(parent, tagName, arAttrs)
{
	if (Browser.IE) {
		var strAttrs = '';
		var attrsLen = arAttrs.length;
		for (var i=attrsLen-1; i>=0; i--) {
			var t = arAttrs[i];
			var key = t[0];
			var val = t[1];
			strAttrs += ' ' + key + '="'+ val + '"';
		};
		tagName = '<' + tagName + strAttrs + '>';
		var node = document.createElement(tagName);
	}
	else {
		var node = document.createElement(tagName);
		var attrsLen = arAttrs.length;
		for (var i=attrsLen-1; i>=0; i--) {
			var t = arAttrs[i];
			var key = t[0];
			var val = t[1];
			node.setAttribute(key, val);
		}
	};
	parent.appendChild(node);
	return node;
}

function GetBounds(object)
{
	if (object == null) 
		return {Left: 0, Top: 0, Width: 0, Height: 0};
	var left = object.offsetLeft;
	var top = object.offsetTop;
	for (var parent = object.offsetParent; parent; parent = parent.offsetParent) {
		left += parent.offsetLeft;
		top += parent.offsetTop;
	};
	return {Left: left, Top: top, Width: object.offsetWidth, Height: object.offsetHeight};
}

function GetScrollY(object)
{
	if (object == null) 
		return 0;
    var scrollY = 0;
    if (object && typeof(object.scrollTop) != 'undefined') {
	    scrollY += object.scrollTop;
	    if (scrollY == 0 && object.parentNode && typeof(object.parentNode) != 'undefined') {
		    scrollY += object.parentNode.scrollTop;
	    }
    }
    else if (typeof object.pageXOffset != 'undefined') {
	    scrollY += object.pageYOffset;
    };
	return scrollY;
}

function CleanNode(object)
{
  while (object.firstChild)
    object.removeChild(object.firstChild);
}

function GetAppPath()
{
	var path = location.pathname;
	var dotIndex = path.lastIndexOf('.');
	var delimIndex = path.lastIndexOf('/');
	if (delimIndex < dotIndex || delimIndex == path.length - 1) {
		path = path.substring(0, delimIndex);
	};
	if (path.length == 0) {
		path = '/';
	};
	return path;
}

function CreateCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days*24*60*60*1000));
		var expires = "; expires=" + date.toGMTString();
	}
	else var expires = "";
	var path = "; path=" + GetAppPath();
	document.cookie = name + "=" + value + expires + path;
}

function ReadCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) {
			return c.substring(nameEQ.length, c.length);
		}
	};
	return null;
}

function EraseCookie(name) {
	CreateCookie(name, "", -1);
}

function HighlightMessageLine(source)
{
	return '<font>' + source + '</font>';
}

function HighlightContactLine(source)
{
	return '<b>' + source + '</b>';
}

String.prototype.PrepareForRegExp = function ()
{
	var search = this.replace(/\\/g, '\\\\').replace(/\^/g, '\\^').replace(/\$/g, '\\$');
	search = search.replace(/\./g, '\\.').replace(/\*/g, '\\*').replace(/\+/g, '\\+');
	search = search.replace(/\?/g, '\\?').replace(/\|/g, '\\|').replace(/\(/g, '\\(');
	search = search.replace(/\)/g, '\\)').replace(/\[/g, '\\[');
	return search;
}

String.prototype.ReplaceStr = function (search, replacement)
{
	return this.replace(new RegExp (search.PrepareForRegExp(), 'gi'), replacement);
}

function GetBirthDay(d, m, y)
{
	res = '';
	if (y != 0) {
		res += y;
		if (d != 0 || m != 0) res += ',';
	}
	if (d != 0) res += ' ' + d;
	switch (m) {
		case 1: res += ' ' + Lang.ShortMonthJanuary; break;
		case 2: res += ' ' + Lang.ShortMonthFebruary; break;
		case 3: res += ' ' + Lang.ShortMonthMarch; break;
		case 4: res += ' ' + Lang.ShortMonthApril; break;
		case 5: res += ' ' + Lang.ShortMonthMay; break;
		case 6: res += ' ' + Lang.ShortMonthJune; break;
		case 7: res += ' ' + Lang.ShortMonthJuly; break;
		case 8: res += ' ' + Lang.ShortMonthAugust; break;
		case 9: res += ' ' + Lang.ShortMonthSeptember; break;
		case 10: res += ' ' + Lang.ShortMonthOctober; break;
		case 11: res += ' ' + Lang.ShortMonthNovember; break;
		case 11: res += ' ' + Lang.ShortMonthDecember; break;
	}
	return res;
}
	
function GetFriendlySize(byteSize)
{
	var size = Math.ceil(byteSize / 1024);
	var mbSize = size / 1024;
	if (mbSize > 1) {
		size = Math.ceil(mbSize*10)/10 + Lang.Mb;
	}
	else {
		size = size + Lang.Kb;
	};
	return size;
}

function GetExtension(fileName)
{
	var ext = '';
	var dotPos = fileName.lastIndexOf('.');
	if (dotPos > -1) {
		ext = fileName.substr(dotPos + 1).toLowerCase();
	};
	return ext;
}

function GetFileParams(fileName)
{
	var ext = GetExtension(fileName);
	switch (ext) {
		case 'asp':
		case 'asa':
		case 'inc':
			return {image: 'application_asp.gif', view: false};
			break;
		case 'css':
			return {image: 'application_css.gif', view: false};
			break;
		case 'doc':
			return {image: 'application_doc.gif', view: false};
			break;
		case 'html':
		case 'shtml':
		case 'phtml':
		case 'htm':
			return {image: 'application_html.gif', view: false};
			break;
		case 'pdf':
			return {image: 'application_pdf.gif', view: false};
			break;
		case 'xls':
			return {image: 'application_xls.gif', view: false};
			break;
		case 'bat':
		case 'exe':
		case 'com':
			return {image: 'executable.gif', view: false};
			break;
		case 'bmp':
			return {image: 'image_bmp.gif', view: true};
			break;
		case 'gif':
			return {image: 'image_gif.gif', view: true};
			break;
		case 'jpg':
		case 'jpeg':
			return {image: 'image_jpeg.gif', view: true};
			break;
		case 'tiff':
		case 'tif':
			return {image: 'image_tiff.gif', view: true};
			break;
		case 'txt':
			return {image: 'text_plain.gif', view: false};
			break;
		default:
			return {image: 'attach.gif', view: false};
			break;
	}
}