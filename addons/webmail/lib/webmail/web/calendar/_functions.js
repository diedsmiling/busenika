/*
Classes, prototypes:
	CCalError
	CCalReport
	CCalInformation
Functions:
	BuildInformation
	ShowError
	ShowReport
	ShowInfo
	HideInfo
	SetMainDivHeight
	setMaskHeight
	GetHeight

	FireFoxDetect
	MSIEDetect
	OperaDetect
	SafariDetect
	OperaAlldayScroll

	$()
	isEnter

	EraseCookie
	CreateCookie

	CreateChild
	CreateChildWithAttrs

	CleanNode
	GetWidth
	GetBounds
	initSettingsCache
	initSettingsCache
	Trim
	HtmlEncode
	HtmlDecode
	ReplaceStr

	getNonthNameByNumber
	getMonthNumberByName
	CheckTimeStr
	ConvertFromStrToDate
	ConvertFromDateToStr

	JSON
*/

function CCalError(name)
{
	this._name = name;
	this._containerObj = null;
	this._messageObj = null;
	this._imgObj = null;
	this._controlObj = null;
	this._fadeObj = null;
	this._delay = 30000;

	this.Build = function ()
	{
		var tbl = CreateChild(document.body, 'table');
		tbl.style.visibility = 'hidden';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.className = 'wm_info_image';
		var img = CreateChild(td, 'img');
		img.src = './calendar/skins/error.gif';
		img.style.width = "20px";
		img.style.height = "26px";
		this._imgObj = img;
		td = tr.insertCell(1);
		td.className = 'wm_info_message';
		this._containerObj = tbl;
		this._messageObj = CreateChild(td, 'span');
		this._controlObj = new CCalInformation(tbl, 'wm_error_information');
	};
}

function CCalReport(name)
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
		tbl.style.visibility = 'hidden';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.className = 'wm_info_message';
		this._containerObj = tbl;
		this._messageObj = CreateChild(td, 'span');
		this._controlObj = new CCalInformation(tbl, 'wm_report_information');
	};
}

CCalReport.prototype = ReportPrototype;
CCalError.prototype = ReportPrototype;

/* for control placement and displaying of information block */
function CCalInformation(cont, cls)
{
	cont.className = cls;
	this._mainContainer = cont;
	this._containerClass = cls;
	this.Hide();
}

CCalInformation.prototype = {
	Show: function ()
	{
		this._mainContainer.style.visibility = 'visible';
	},
	
	Hide: function ()
	{
		this._mainContainer.style.visibility = 'hidden';
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
		if (document.body && typeof document.body.scrollTop != "undefined")
		{
			scrollY += document.body.scrollTop;
			if (scrollY == 0 && document.body.parentNode && typeof document.body.parentNode != "undefined")
			{
				scrollY += document.body.parentNode.scrollTop;
			}
		} else if (typeof window.pageXOffset != "undefined")  {
			scrollY += window.pageYOffset;
		};
		return scrollY;
	}
};//CCalInformation.prototype

function BuildInformation(tbl)
{
	_infoMessage = $('info_message');
	_infoObj = new CCalInformation(tbl, 'wm_information');

	_reportObj = new CCalReport('_reportObj');
	_fadeEffect = new CFadeEffect('_fadeEffect');
	_reportObj.Build();
	_reportObj.SetFade(_fadeEffect);

	_errorObj = new CCalError('_errorObj');
	_errorObj.Build();
	_errorObj.SetFade(_fadeEffect);
}

function ShowError(errorDesc)
{
	_errorObj.Show(errorDesc);
}

function ShowReport(report)
{
	_reportObj.Show(report);
}

function ShowInfo(Info)
{
	_infoMessage.innerHTML = Info;
	_infoObj.Show();
	_infoObj.Resize();
}

function HideInfo()
{
	_infoObj.Hide();
}


function SetMainDivHeight()
{
	var logo		= $('logo');
	var accountslist= $('accountslist');
	var toolbar		= $('toolbar');
	var upperIndent	= $('upper_indent');
	var lowerIndent = $('lower_indent');
	var calendarManager = $('right');
	//var arrow_layer = new Array();
	var dayBody1;
	var main_div;
	var dayHeaders;
	var resCntArrow;

	switch (view) {
		case (0):
			//arrow_layer[1]	= $('arrow_layer_day');
			dayBody1		= $('area_1_day');
			main_div		= $('area_2_day');
			dayHeaders		= $('day_headers_day');
		break;
		case (1):
			//for (var i = 1; i<8; i++) arrow_layer[i] = $('arrow_layer_week_'+i);
			dayBody1	= $('area_1_week');
			main_div	= $('area_2_week');
			dayHeaders	= $('day_headers_week');
		break;
		case (2):
			main_div	= $('area_2_month');
			dayHeaders	= $('day_headers_month');
		break;
		default:
			//for(var i = 1; i<8; i++) arrow_layer[i] = $('arrow_layer_week_'+i);
			dayBody1	= $('area_1_week');
			main_div	= $('area_2_week');
			dayHeaders	= $('day_headers_month');
	};

	var externalHeight = 0;

	externalHeight += (logo != null)?(logo.offsetHeight): 0;
	externalHeight += (accountslist != null)?(accountslist.offsetHeight): 0;
	externalHeight += (toolbar != null)?(toolbar.offsetHeight): 0;
	externalHeight += (upperIndent != null)?(upperIndent.offsetHeight): 0;
	var dhOffsetHeight = (dayHeaders != null)?(dayHeaders.offsetHeight):0;
	externalHeight += dhOffsetHeight;

	if(view == MONTH)
	{
		var db1offsetHeight = 0;
	} else {
		var db1offsetHeight = (dayBody1 != null)?(dayBody1.offsetHeight):0;
		externalHeight += db1offsetHeight;
	};

	externalHeight += (lowerIndent != null)?(lowerIndent.offsetHeight):0;

	if(!(typeof(main_div) == "undefined"))
	{
		var aHtmls = document.getElementsByTagName("HTML");
		Html = aHtmls[0];
		if(main_div)
		{
			var copyMargin = 0;
			var tdBorder = 1;
			externalHeight += copyMargin + tdBorder;
			var height = GetHeight() - externalHeight;
			if (height < 200) height = 200;
			main_div.style.height = height + 'px';
			
			/*for(var i = 1; resCntArrow = arrow_layer[i]; i++){
				resCntArrow.style.height = height + 'px';
			};*/

			var cmHeight = 360;
			if ((height + dhOffsetHeight + db1offsetHeight + tdBorder) < cmHeight) {
				height = (cmHeight - dhOffsetHeight - db1offsetHeight);
				main_div.style.height = height+"px";
				/*for(var i = 1; resCntArrow = arrow_layer[i]; i++){
					resCntArrow.style.height = height+"px";
				};*/
				if(!FireFoxDetect()) {
					Html.style.overflow = 'auto';
				} else {
					document.body.scroll = "yes";
					document.body.style.overflow = "auto";
				}
			} else {
				if(!FireFoxDetect()) {
					Html.style.overflow = 'hidden';
				} else {
					document.body.scroll = "no";
					document.body.style.overflow = "hidden";
				}
			};
			var mdh = height;//main div height - div with time or weeks(in month)
			var wto = mdh+db1offsetHeight+dhOffsetHeight; // area_2 + area_1 + days_headers

			var mlist=$("manager_list");
			var calhead1=$("calhead1").offsetHeight;
			var calhead2=$("calhead2").offsetHeight;
			var mincal=$("mini_calendar_box").offsetHeight;// mincal=189;
			var mold=parseInt(mlist.style.height);
			var mnew = (wto - calhead1 - calhead2 - mincal - 33); //if (mnew<65) mnew=65; mnew+="px";
			if (mold!=mnew) {
				mlist.style.height=mnew+"px";
				calendarManager.style.height=(wto-5)+"px";
			};
			cmScroll_resize (mlist);
		}
	}
}
function setMaskHeight(){
	if (MSIEDetect()){
		var oEditWindow = $('edit_window');
		var iBodyHeight = document.body.offsetHeight;
		oEditWindow.style.height = iBodyHeight + 'px';

		var oManagerWindow = $('manager_window');
		oManagerWindow.style.height = iBodyHeight + 'px';

		var oConfirmWindow = $('confirm_window');
		oConfirmWindow.style.height = iBodyHeight + 'px';
	}
}
function cmScroll_resize (mlist) {
	mlist = (!mlist) ? $("manager_list") : mlist;
	var scrolled=(mlist.offsetHeight<mlist.scrollHeight);
	var wid1=scrolled?140:155; wid1+="px";
	var wid2=scrolled?100:115; wid2+="px";
	var wid3=scrolled?114:125; wid3+="px";
	//var wid4=scrolled?(MSIEDetect()?48:52):41;   wid4+="px";
	var wid5=scrolled?3:0; wid5+="px";
	for (i in mlist.childNodes) { 
		cn=mlist.childNodes[i];
		if ((cn.nodeName=="DIV")&&(cn.className!="new_calendar")) { 
			cn.firstChild.style.width=wid1; 
			cn.firstChild.childNodes[2].firstChild.firstChild.style.width=wid2; 
		}
	};
	var qmenu=$("quick_edit");
	qmenu.style.width=wid3;
	//qmenu.style.right=wid4;
	qmenu.style.textIndent=wid5;
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

var IsFireFox = FireFoxDetect(); // is browser FireFox?

// Check Browser Functions
function FireFoxDetect()
{
	return navigator.userAgent.indexOf("Gecko") >= 0;
}
function MSIEDetect() 
{
	return navigator.userAgent.indexOf("MSIE") >= 0;
}
function OperaDetect() 
{
	return window.opera;
}
function SafariDetect() {
	var isSafari = (window.devicePixelRatio)?true:false;
	var r = navigator.userAgent.split(" ").reverse();
	var Name = r[0].slice(0, r[0].indexOf("/"));
	var Version = r[1].slice(r[1].indexOf("/")+1);
	return Obj={isSafari:isSafari, Name:Name, Version:Version};
}
function OperaAlldayScroll() {
	if (OperaDetect()) {
		var screens = new Array();
		screens.push($("area_1_day"),$("area_1_week"));
		for (i=0; i<screens.length; i++) {
			var AlldayArea = screens[i];
			var scrollWidth = AlldayArea.offsetWidth - AlldayArea.clientWidth - 1;
			if (scrollWidth<=0) {
				AlldayArea.style.overflow = "hidden";
				var AlldayAreaTbl = AlldayArea.getElementsByTagName("table");
				var AlldayAreaTblTr = AlldayAreaTbl[0].getElementsByTagName("tr");

				if (AlldayArea.style.height != null) {
					var alldayStyleHeight = parseInt(AlldayArea.style.height);
				} else {
					var alldayStyleHeight = AlldayArea.clientHeight;
				};
				if (AlldayArea.scrollHeight > alldayStyleHeight) {
					//del td
				} else {
					//add td
					var td = document.createElement("td");
					td.style.width = "16px";
					td.style.borderLeft = "1px solid #CDCDCD";
					if (setcache["showweekends"] == 1 && i==1) {
						td.className = "weekend_day";
					} else {
						td.className = "";
					};
					if (i==0) {
						$("grid_1d").style.overflow = "hidden";
					} else if (i==1) {
						$("grid_1w").style.overflow = "hidden";
					};
					AlldayAreaTblTr[0].appendChild(td);
				}
			}
		}//end for
	}

}// #Check Browser Functions#


function $(element) {
	if (arguments.length > 1) {
		for (var i = 0, elements = [], length = arguments.length; i < length; i++)
			elements.push($(arguments[i]));
		return elements;
	};
	if (typeof element == "string") element = document.getElementById(element);
	return element;
}

function isEnter(ev)
{
	var k = -1;
	k = ev ? ev.which :window.event.keyCode;

	if (k == 13) 
		return true;
	else
		return false;
}

// #Cookie Functions#
function EraseCookie(name) {
	CreateCookie(name, "", -1);
}

function CreateCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days*24*60*60*1000));
		var expires = "; expires=" + date.toGMTString();
	}
	else var expires = "";
	document.cookie = name + "=" + value + expires;
}// #Cookie Functions#


function CreateChild(oParentNode,sTag)
{
	var oNode = document.createElement(sTag);
	oParentNode.appendChild(oNode);
	return oNode;
}

function CreateChildWithAttrs(parent, tagName, arAttrs)
{
	if (MSIEDetect()) {
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
	} else {
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

function CleanNode(object)
{
	while (object.firstChild) object.removeChild(object.firstChild);
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

function GetBounds(object)
{
	var left = object.offsetLeft;
	var top = object.offsetTop;
	for (var parent = object.offsetParent; parent; parent = parent.offsetParent)
	{
		left += parent.offsetLeft;
		top += parent.offsetTop;
	}
	return {Left: left, Top: top, Width: object.offsetWidth, Height: object.offsetHeight};
}

function initSettingsCache()
{
	setcache = getSettingsParametr(); 
	render_calendar();
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


function ReplaceStr(source, search, replace)
{
	var result = '';
	if (source) {
		var i = source.indexOf(search);
		var searchLen = search.length;
		while ( i != -1){
			result += source.substring(0, i) + replace;
			source = source.substring(i + searchLen);
			i = source.indexOf(search);
		};
		result += source;
	};
	return result;
}


function getNonthNameByNumber(monthnumber) {
	switch (monthnumber) {
		case '01':
			monthname = Lang.ShortMonthJanuary; break;
		case '02':
			monthname = Lang.ShortMonthFebruary; break;
		case '03':
			monthname = Lang.ShortMonthMarch; break;
		case '04':
			monthname = Lang.ShortMonthApril; break;
		case '05':
			monthname = Lang.ShortMonthMay; break;
		case '06':
			monthname = Lang.ShortMonthJune; break;
		case '07':
			monthname = Lang.ShortMonthJuly; break;
		case '08':
			monthname = Lang.ShortMonthAugust; break;
		case '09':
			monthname = Lang.ShortMonthSeptember; break;
		case '10':
			monthname = Lang.ShortMonthOctober; break;
		case '11':
			monthname = Lang.ShortMonthNovember; break;
		case '12':
			monthname = Lang.ShortMonthDecember; break;
		default:
			monthname = null; break;
	};
	return monthname;
}

function getMonthNumberByName(monthname) {
	switch (monthname) {
		case Lang.ShortMonthJanuary:
			monthnumber = "01"; break;
		case Lang.ShortMonthFebruary:
			monthnumber = "02"; break;
		case Lang.ShortMonthMarch:
			monthnumber = "03"; break;
		case Lang.ShortMonthApril:
			monthnumber = "04"; break;
		case Lang.ShortMonthMay:
			monthnumber = "05"; break;
		case Lang.ShortMonthJune:
			monthnumber = "06"; break;
		case Lang.ShortMonthJuly:
			monthnumber = "07"; break;
		case Lang.ShortMonthAugust:
			monthnumber = "08"; break;
		case Lang.ShortMonthSeptember:
			monthnumber = "09"; break;
		case Lang.ShortMonthOctober:
			monthnumber = "10"; break;
		case Lang.ShortMonthNovember:
			monthnumber = "11"; break;
		case Lang.ShortMonthDecember:
			monthnumber = "12"; break;
		default:
			monthnumber = null; break;
	};
	return monthnumber;
}

function CheckTimeStr(time)
{
	var pattern;
	if (setcache['timeformat'] == 1){
		pattern = /^(\d{1,2}):?(\d{2})?\s+(AM|PM)$/;
	} else {
		pattern = /^(\d{1,2}):(\d{2})$/;
	};

	var arr =  pattern.exec(time);
	if (arr != null) {
		var timeObj = {};
		timeObj.time = arr[0];
		timeObj.hours = arr[1];
		timeObj.minutes = ((arr[2]!="") ? arr[2] : null);
		timeObj.timeInterval = ((arr[3]!="") ? arr[3] : null);
	} else {
		return null;
	};
	return timeObj;
}

function ConvertFromStrToDate(str) {
	var date, pattern, arr;
	if(setcache['dateformat'] == 1)
	{
		pattern = /^([01]?[0-9])\/([0-3]?[0-9])\/([12][0-9]{3})$/;
		arr = pattern.exec(str);
		if (arr != null) {
			return date = new Date(arr[3], (Number(arr[1])-1), arr[2]);
		} else {
			return null;
		}
	}
	else if (setcache['dateformat'] == 2)
	{
		pattern = /^([0-3]?[0-9])\/([01]?[0-9])\/([12][0-9]{3})$/;
		arr = pattern.exec(str);
		if (arr != null) {
			return date = new Date(arr[3], (Number(arr[2])-1), arr[1]);
		} else {
			return null;
		}
	}
	else if (setcache['dateformat'] == 3)
	{
		pattern = /^([12][0-9]{3})-([01]?[0-9])-([0-3]?[0-9])$/;
		arr = pattern.exec(str);
		if (arr != null) {
			return date = new Date(arr[1], (Number(arr[2])-1), arr[3]);
		} else {
			return null;
		}
	}
	else if (setcache['dateformat'] == 4)
	{
		pattern = /^([0-3]?[0-9])\s*,\s*([12][0-9]{3})$/;
		var month = Trim(str.substr(0, str.indexOf(" ")));
		var dayYear = Trim(str.substr(str.indexOf(" "), str.length));
		var monthnumber = getMonthNumberByName(month);
		if (monthnumber==null) return null;
		arr = pattern.exec(dayYear);
		if (arr != null) {
			return date = new Date(arr[2], (Number(monthnumber)-1), arr[1]);
		} else {
			return null;
		}
	}
	else //setcache['dateformat'] == 5
	{
		var arr = str.split(" ");
		var arr1 = new Array();
		for (i=0; i<arr.length; i++) {
			if (Trim(arr[i]).length != 0) arr1.push(Trim(arr[i]));
		};

		if (arr1.length == 3) {
			var monthnumber = getMonthNumberByName(arr1[1]);
			var pattern = /^([12][0-9]{3})$/;
			var res = pattern.exec(arr1[2]);
			if (!(Number(arr1[0])<1 || Number(arr1[0])>31) && 
				(monthnumber != null) && 
				(res != null)) {
					return date = new Date(res[0], (Number(monthnumber)-1), arr1[0]);
			}
		};
		return null;
	}
}

function ConvertFromDateToStr(date) {
	var str;
	if(setcache['dateformat'] == 1)
	{
		str = fnum((date.getMonth()+1),2)+"/"+fnum(date.getDate(),2)+"/"+date.getFullYear();
	}
	else if (setcache['dateformat'] == 2)
	{
		str = fnum(date.getDate(),2)+"/"+fnum((date.getMonth()+1),2)+"/"+date.getFullYear();
	}
	else if (setcache['dateformat'] == 3)
	{
		str = date.getFullYear()+"-"+fnum((date.getMonth()+1),2)+"-"+fnum(date.getDate(),2);
	}
	else if (setcache['dateformat'] == 4)
	{
		str = getNonthNameByNumber(fnum((date.getMonth()+1),2))+" "+date.getDate()+", "+date.getFullYear();
	}
	else //setcache['dateformat'] == 5
	{
		str = date.getDate()+" "+getNonthNameByNumber(fnum((date.getMonth()+1),2))+" "+date.getFullYear();
	}
	return str;
}

/*
 * Based on json.js (2007-07-03)
 * Modified by AfterLogic Corporation
 */
String.prototype.parseJSON = function (filter) {
	var j;

	function walk(k, v) {
		var i;
		if (v && typeof v === 'object') {
			for (i in v) {
				if (v.hasOwnProperty(i)) {
					v[i] = walk(i, v[i]);
				}
			}
		};
		return filter(k, v);
	}

	if (/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]+$/.test(this.
		replace(/\\./g, '@').
		replace(/"[^"\\\n\r]*"/g, ''))) {
			j = eval('(' + this + ')');
			if (typeof filter === 'function') {
				j = walk('', j);
			};
			return j;
	};

	return false;
};