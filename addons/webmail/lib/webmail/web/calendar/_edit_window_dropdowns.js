/*
* CTimeSelector
* CCalendarTable
*/
var timeFormat1 = [
	{Id:0,		Value:"12 AM"},
	{Id:0.5,	Value:"12:30 AM"},
	{Id:1,		Value:"1 AM"},
	{Id:1.5,	Value:"1:30 AM"},
	{Id:2,		Value:"2 AM"},
	{Id:2.5,	Value:"2:30 AM"},
	{Id:3,		Value:"3 AM"},
	{Id:3.5,	Value:"3:30 AM"},
	{Id:4,		Value:"4 AM"},
	{Id:4.5,	Value:"4:30 AM"},
	{Id:5,		Value:"5 AM"},
	{Id:5.5,	Value:"5:30 AM"},
	{Id:6,		Value:"6 AM"},
	{Id:6.5,	Value:"6:30 AM"},
	{Id:7,		Value:"7 AM"},
	{Id:7.5,	Value:"7:30 AM"},
	{Id:8,		Value:"8 AM"},
	{Id:8.5,	Value:"8:30 AM"},
	{Id:9,		Value:"9 AM"},
	{Id:9.5,	Value:"9:30 AM"},
	{Id:10,		Value:"10 AM"},
	{Id:10.5,	Value:"10:30 AM"},
	{Id:11,		Value:"11 AM"},
	{Id:11.5,	Value:"11:30 AM"},
	{Id:12,		Value:"12 PM"},
	{Id:12.5,	Value:"12:30 PM"},
	{Id:13,		Value:"1 PM"},
	{Id:13.5,	Value:"1:30 PM"},
	{Id:14,		Value:"2 PM"},
	{Id:14.5,	Value:"2:30 PM"},
	{Id:15,		Value:"3 PM"},
	{Id:15.5,	Value:"3:30 PM"},
	{Id:16,		Value:"4 PM"},
	{Id:16.5,	Value:"4:30 PM"},
	{Id:17,		Value:"5 PM"},
	{Id:17.5,	Value:"5:30 PM"},
	{Id:18,		Value:"6 PM"},
	{Id:18.5,	Value:"6:30 PM"},
	{Id:19,		Value:"7 PM"},
	{Id:19.5,	Value:"7:30 PM"},
	{Id:20,		Value:"8 PM"},
	{Id:20.5,	Value:"8:30 PM"},
	{Id:21,		Value:"9 PM"},
	{Id:21.5,	Value:"9:30 PM"},
	{Id:22,		Value:"10 PM"},
	{Id:22.5,	Value:"10:30 PM"},
	{Id:23,		Value:"11 PM"},
	{Id:23.5,	Value:"11:30 PM"},
	{Id:24,		Value:"12 AM"}
];

var timeFormat2 = [
	{Id:0,		Value:"00:00"},
	{Id:0.5,	Value:"00:30"},
	{Id:1,		Value:"01:00"},
	{Id:1.5,	Value:"01:30"},
	{Id:2,		Value:"02:00"},
	{Id:2.5,	Value:"02:30"},
	{Id:3,		Value:"03:00"},
	{Id:3.5,	Value:"03:30"},
	{Id:4,		Value:"04:00"},
	{Id:4.5,	Value:"04:30"},
	{Id:5,		Value:"05:00"},
	{Id:5.5,	Value:"05:30"},
	{Id:6,		Value:"06:00"},
	{Id:6.5,	Value:"06:30"},
	{Id:7,		Value:"07:00"},
	{Id:7.5,	Value:"07:30"},
	{Id:8,		Value:"08:00"},
	{Id:8.5,	Value:"08:30"},
	{Id:9,		Value:"09:00"},
	{Id:9.5,	Value:"09:30"},
	{Id:10,		Value:"10:00"},
	{Id:10.5,	Value:"10:30"},
	{Id:11,		Value:"11:00"},
	{Id:11.5,	Value:"11:30"},
	{Id:12,		Value:"12:00"},
	{Id:12.5,	Value:"12:30"},
	{Id:13,		Value:"13:00"},
	{Id:13.5,	Value:"13:30"},
	{Id:14,		Value:"14:00"},
	{Id:14.5,	Value:"14:30"},
	{Id:15,		Value:"15:00"},
	{Id:15.5,	Value:"15:30"},
	{Id:16,		Value:"16:00"},
	{Id:16.5,	Value:"16:30"},
	{Id:17,		Value:"17:00"},
	{Id:17.5,	Value:"17:30"},
	{Id:18,		Value:"18:00"},
	{Id:18.5,	Value:"18:30"},
	{Id:19,		Value:"19:00"},
	{Id:19.5,	Value:"19:30"},
	{Id:20,		Value:"20:00"},
	{Id:20.5,	Value:"20:30"},
	{Id:21,		Value:"21:00"},
	{Id:21.5,	Value:"21:30"},
	{Id:22,		Value:"22:00"},
	{Id:22.5,	Value:"22:30"},
	{Id:23,		Value:"23:00"},
	{Id:23.5,	Value:"23:30"},
	{Id:24,		Value:"24:00"}
];

function CTimeSelector (timeSelector_id,thisTime,fromTime)
{
	this.container = null;
	this.parentObj = null;
	this.settingsTimeFormat = null;
	this.timeSelector_id = timeSelector_id;
	this.startOptionNumber = 0;
	this.Initialize(thisTime,fromTime);
}
 
CTimeSelector.prototype = {
	Initialize: function(thisTime,fromTime)
	{
		this.container = $(this.timeSelector_id);
		this.container.className = 'wm_hide';
		this.settingsTimeFormat = setcache['timeformat'];

		var div1, timeOptions, pattern, startOptionValue;

		if (this.settingsTimeFormat == 1) {
			pattern = /^1?[0-9](:[03]0)?(\s+(AM|PM))$/;
			timeOptions = timeFormat1;
			this.container.style.textAlign = "left";
			this.container.style.paddingLeft = "4px";
		} else {
			pattern = /^([0-2])?[0-9](:[03]0)$/;
			timeOptions = timeFormat2;
		}

		if (fromTime != null && pattern.test(fromTime)) {
			startOptionValue = fromTime;
		} else {
			startOptionValue = null;
		}

		var len = (this.timeSelector_id=="EventTimeFrom_dropdown")?(timeOptions.length-1):timeOptions.length;
		for (i=0; i<len; i++) {
			if (startOptionValue != null) {
				if (startOptionValue == timeOptions[i].Value) {this.startOptionNumber = i+1;};
				if ( i<this.startOptionNumber || this.startOptionNumber == 0) continue;
			};

			div1 = document.createElement('div');
			div1.className	= 'ts_text';
			div1.innerHTML	= timeOptions[i].Value;
			div1.onmouseover= function () { this.className = 'ts_text_hover'; };
			div1.onmouseout	= function () { this.className = 'ts_text'; };
			var timeObj		= {Id:timeOptions[i].Id, Value:timeOptions[i].Value};
			div1.onclick	= CreateTimeFunc(this, timeObj);

			if (thisTime == timeOptions[i].Value) div1.style.fontWeight = "bold";
			this.container.appendChild(div1);

		}
	},

	Show: function (obj, event)
	{
		this.StopEvents(event);
		this.parentObj = obj;
		this.container.className = 'time_selector';
		this.container.style.position = 'absolute';
		this.container.style.zIndex = '2001';
		this.container.scrollTop = this.ScrollValue(obj);
	},

	Hide: function ()
	{
		this.container.className = 'wm_hide';
	},

	ScrollValue : function(obj)
	{
		var timeOptions, v = 0; //vertical offset
		var ThisTime = Trim(obj.value);

		if (this.settingsTimeFormat == 1) {
			timeOptions = timeFormat1;
		} else {
			timeOptions = timeFormat2;
		};

		var len = (this.timeSelector_id=="EventTimeFrom_dropdown")?(timeOptions.length-1):timeOptions.length;
		for (var i=this.startOptionNumber; i<len; i++)
		{
			if (ThisTime == timeOptions[i].Value) return (v*16); //16-height of 1 option
			v++;
		}
	},

	StopEvents: function(event)
	{
		var event = event || window.event;
		if (event.stopPropagation) {
			event.stopPropagation();
		} else {
			event.cancelBubble = true;
		}
	},

	Remove: function()
	{
		CleanNode(this.container);
	}
};

function CreateTimeFunc(obj, timeObj)
{ 
	return function () {
		obj.Hide();
		obj.onpicktime(timeObj);
	}
}

//start_print_time - optional
// if start_print_time == mull then timetill list starts from 00:00 or 12AM
function CreateTimeTill(till_time, start_print_time)
{
	var timetill_id = "EventTimeTill_dropdown";
	timeSelectorTill = new CTimeSelector(timetill_id, till_time, start_print_time);
	var input_timeFrom = $('EventTimeFrom');
	var input_timeTill = $('EventTimeTill');

	timeSelectorTill.onpicktime = function(timeObj)
	{
		input_timeTill.value = timeObj.Value;
		timeSelectorTill.Remove();
		CreateTimeTill(input_timeTill.value,Trim(input_timeFrom.value));
	};
	input_timeTill.onfocus = function(event) {
		if (typeof(calendarTableStart) != 'undefined') calendarTableStart.Hide();
		if (typeof(calendarTableEnd) != 'undefined') calendarTableEnd.Hide();
		timeSelectorTill.Hide();
		timeSelectorTill.Show(this, event)
	}
}

function CreateTimeFrom(from_time)
{
	var timefrom_id = "EventTimeFrom_dropdown";
	timeSelectorFrom = new CTimeSelector(timefrom_id, from_time);
	var input_timeFrom = $('EventTimeFrom');
	var input_timeTill = $('EventTimeTill');
	var input_dateFrom = $('EventDateFrom');
	var input_dateTill = $('EventDateTill');
	
	timeSelectorFrom.onpicktime = function(timeObj)
	{
		var tillTimeValue, tillTimeId, timeOptions, startTimeTill = null;

		if(setcache['timeformat'] == 1){
			pattern = /^1?[0-9](:[03]0)?(\s+(AM|PM))$/;
			timeOptions = timeFormat1;
		} else {
			pattern = /^([0-2])?[0-9](:[03]0)$/;
			timeOptions = timeFormat2;
		};
		//check time till value in input field
		if (pattern.test(Trim(input_timeTill.value))) {
			tillTimeValue = Trim(input_timeTill.value);
			for (i=0; i<timeOptions.length; i++) {
				if (tillTimeValue == timeOptions[i].Value) {
					tillTimeId = timeOptions[i].Id;
					break;
				}
			}
		} else {
			tillTimeId = (timeObj.Id + 1);
			for (i=0; i<timeOptions.length; i++) {
				if (tillTimeId == timeOptions[i].Id) {
					tillTimeValue = timeOptions[i].Value;
					break;
				}
			}
		};
		startTimeTill = timeObj.Value;

		if (tillTimeId<=timeObj.Id) {
			//check date values
			var dateFrom = ConvertFromStrToDate(Trim(input_dateFrom.value));
			var dateTill = ConvertFromStrToDate(Trim(input_dateTill.value));

			//redefine date values if it's wrong
			if (dateFrom == null) {
				dateFrom = (dateTill == null)? (new Date()) : dateTill;
			};
			if (dateTill == null) {
				dateTill = (dateFrom == null)? (new Date()) : dateFrom;
			};

			//define time till value
			for (i=0; i<timeOptions.length; i++) {
				if (timeObj.Value == timeOptions[i].Value) {
					tillTimeValue = timeOptions[i+1].Value;
					break;
				}
			};
			if (dateFrom.getTime() >= dateTill.getTime()) {
				input_dateTill.value = ConvertFromDateToStr(dateFrom);
			} else {
				startTimeTill = null;
			}
		};

		input_timeFrom.value = timeObj.Value;
		input_timeTill.value = tillTimeValue; 

		timeSelectorTill.Remove();
		CreateTimeTill(tillTimeValue, startTimeTill);

		timeSelectorFrom.Remove();
		CreateTimeFrom(timeObj.Value);
	};
	input_timeFrom.onfocus = function(evt) {
		var evt = (evt)?evt:window.event;
		var tgt = window.event ? window.event.srcElement : evt.target;
		var select_box_list = $('edit_select_box_list');
		if (tgt.id != 'calen_sal' && tgt.id != 'calendar_arrow' && select_box_list.style.display != 'none'){
			select_box_list.style.display = 'none';
		};
		if (typeof(calendarTableStart) != 'undefined') calendarTableStart.Hide();
		if (typeof(calendarTableEnd) != 'undefined') calendarTableEnd.Hide();
		timeSelectorTill.Hide();
		timeSelectorFrom.Show(this, evt);
	}
}

var all_events_dates = new Array();

function CCalendarTable (parent)
{
	this.WeekDaysNames = [
		Lang.CalendarTableDayMonday,
		Lang.CalendarTableDayTuesday,
		Lang.CalendarTableDayWednesday,
		Lang.CalendarTableDayThursday,
		Lang.CalendarTableDayFriday,
		Lang.CalendarTableDaySaturday,
		Lang.CalendarTableDaySunday
	];
	this.m_names = [
		Lang.FullMonthJanuary,
		Lang.FullMonthFebruary,
		Lang.FullMonthMarch,
		Lang.FullMonthApril,
		Lang.FullMonthMay,
		Lang.FullMonthJune,
		Lang.FullMonthJuly,
		Lang.FullMonthAugust,
		Lang.FullMonthSeptember,
		Lang.FullMonthOctober,
		Lang.FullMonthNovember,
		Lang.FullMonthDecember
	];
	this.dms = 86400000; // 86400000 milliseconds in one day
	this.weeksnum = 7;

	this.SelectedDate		= null;
	this.HtmlContainer		= null;
	this.prevMonthSwitcher	= null;
	this.nextMonthSwitcher	= null;
	this.middleTdSelector	= null;
	this.monthsList			= null;
	this.currentMonth		= null;

	if (parent) {
		this.Create(parent);
		this.SetDateFromPicker = function (day,month,year) { }
	} else {
		this.Create(document.body);
		this.SetDateFromPicker = function (day,month,year) { this.Hide(); }
	}
}

CCalendarTable.prototype = {
	//Show Calendar after onClick on img
	Create: function (parent)
	{
		this.HtmlContainer = CreateChild(parent, 'div');
		this.HtmlContainer.className = 'wm_hide';

		date = new Date();
		all_events_dates = LoadAllYearEvents(date);

		// create TABLE
		var m = CreateChild(this.HtmlContainer, 'table');
		m.className = 'Calendar_Title';
		var m_tr_2 = m.insertRow(0);
		var m_td_21 = m_tr_2.insertCell(0);
		var m_td_22 = m_tr_2.insertCell(1);
		var m_td_23 = m_tr_2.insertCell(2);

		m_td_21.style.width = '32px';
		m_td_21.style.textAlign = 'right';

		m_td_23.style.width = '32px';
		m_td_23.style.textAlign = 'left';

		m_td_22.style.textAlign = 'center';
		m_td_22.style.width = '120px';
		m_td_22.style.zIndex = '15'; 
		m_td_22.style.display = 'block';
		m_td_22.style.position = 'relative';

		// Month Row
		var obj = this;
		this.prevMonthSwitcher = CreateChild(m_td_21, 'a');
		this.prevMonthSwitcher.href = 'javascript:void(0);';
		var img = CreateChild(this.prevMonthSwitcher, 'img');
		img.alt = Lang.AltPrevMonth;
		img.title = Lang.AltPrevMonth;
		img.style.border = '0';
		img.src = './calendar/skins/calendar/minicalendar_arrow_left_activ.gif';
		img.style.width = "11px";
		img.style.height = "11px";

		// td_middle_place
		var middleTdSelector_ = document.createElement("div");
		middleTdSelector_.className = 'middleTdSelector_';
		this.middleTdSelector = document.createElement("div");
		this.middleTdSelector.id = "middleTdSelector";
		this.middleTdSelector.className = 'middleTdSelector';

		this.currentMonth = document.createElement("span");
		this.currentMonth.id = "MonthSelector";
		this.middleTdSelector.appendChild(this.currentMonth);

		var imgSelector = document.createElement("img");
		imgSelector.id = "imgSelector";
		imgSelector.alt = '';
		imgSelector.style.border = '0';
		imgSelector.src = './calendar/skins/calendar/minicalendar_arrow_bottom.gif';
		imgSelector.style.padding = '0 0 0 4px';
		imgSelector.style.width = "11px";
		imgSelector.style.height = "7px";

		this.middleTdSelector.onmouseover = function() {
			//imgSelector.src = './calendar/skins/calendar/minicalendar_arrow_bottom_activ.gif';
			obj.middleTdSelector.className = 'middleTdSelector_over';
		};
		this.middleTdSelector.onmouseout = function() {
			//imgSelector.src = './calendar/skins/calendar/minicalendar_arrow_bottom.gif';
			if (obj.monthsList.hidden) {
				obj.middleTdSelector.className = 'middleTdSelector';
			}
		};
		this.middleTdSelector.appendChild(imgSelector);
		middleTdSelector_.appendChild(this.middleTdSelector);
		m_td_22.appendChild(middleTdSelector_);

		this.monthsList = document.createElement("div");
		this.monthsList.id = "monthsList";
		this.monthsList.className = "event edit_gray monthsList";
		this.monthsList.hidden = true;
		this.middleTdSelector.onclick = function() {
			if(obj.monthsList.hidden) {
				obj.monthsList.style.display = 'block';
			} else {
				obj.monthsList.style.display = 'none';
			};
			obj.monthsList.hidden = !obj.monthsList.hidden;
		};

		for (i=-6; i<7; i++) {
			evt_middle = document.createElement("div");
			evt_middle.className = 'event_middle';
			cal_txt = document.createElement("div");
			cal_txt.className = 'calendar_text';
			txt = document.createElement("span");
			txt.className = 'text';

			a = document.createElement("a");
			a.href = 'javascript:void(0);';
			a.id = "dd_month_"+i;

			txt.appendChild(a);
			cal_txt.appendChild(txt);
			evt_middle.appendChild(cal_txt);
			this.monthsList.appendChild(evt_middle);
		};
		var tempDiv = document.createElement("div");
		tempDiv.className = 'c';
		tempDiv.height = "1px !important";
		this.monthsList.appendChild(tempDiv);
		tempDiv = document.createElement("div");
		tempDiv.className = 'a';
		this.monthsList.appendChild(tempDiv);

		m_td_22.appendChild(this.monthsList);


//td_middle_place_end
		this.nextMonthSwitcher = document.createElement("a");
		this.nextMonthSwitcher.href = 'javascript:void(0);';
		img = CreateChild(this.nextMonthSwitcher, 'img');
		img.alt = Lang.AltNextMonth;
		img.title = Lang.AltNextMonth;
		img.style.border = '0';
		img.src = './calendar/skins/calendar/minicalendar_arrow_right_activ.gif';
		img.style.width = "11px";
		img.style.height = "11px";

		m_td_23.appendChild(this.nextMonthSwitcher);

		var Table = CreateChild(this.HtmlContainer, 'table');
		Table.cellPadding = '0';
		Table.cellSpacing = '0';
		Table.valign = 'middle';
		Table.className = 'calendar_block';

		var TR = Table.insertRow(0);
		TR.id = "calendarWeekdayHeaders";
		for(wd = 0; wd < 7; wd++)
		{
			var TD = TR.insertCell(wd);
			TD.className = 'title';
		};

		for(w = 1; w <= this.weeksnum; w++){
			var TR = Table.insertRow(w);
			TR.id = "sc_week_"+w;
			for(k = 1; k<=7; k++){
				var TD = TR.insertCell(-1);
				TD.valign = 'middle';
			}
		};

	},

	Show: function (obj)
	{
		this.HtmlContainer.className = 'box';
		if (obj) {
			var bounds = GetBounds(obj);
			with (this.HtmlContainer.style) {
				position = 'absolute';
				top = bounds.Top + bounds.Height + 'px';
				left = bounds.Left + 1 + 'px';
				zIndex = '21';
			}
		}
	},

	Hide: function ()
	{
		this.HtmlContainer.className = 'wm_hide';
	},

	//Return days in month
	GetDaysInMonth: function (month,year)
	{
		month = ( month<1 ) ? 0 : (( month>12 ) ? 11 : month - 1);
		var arDaysInMonth_Usual = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
		var arDaysInMonth_Leap 	= [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
		if ( (year % 4) == 0 && (year % 100) != 0 || (year % 400) == 0 ) 
			return arDaysInMonth_Leap[month];
		else
			return arDaysInMonth_Usual[month];
	},

	// Return day in week: 0 - Mo, 1 - Tu ...
	GetDay: function ( cDate )
	{
		var wDay = cDate.getDay() - 1;
		if ( wDay == -1 ) wDay = 6;
		return wDay;
	},

	GetWeeksNum: function ( firstshowdayDate, lastshowdayDate )
	{
		var weeksnum = Math.ceil(((lastshowdayDate - firstshowdayDate) / this.dms) / 7);
		return weeksnum;
	},

	RefreshCalendarSelector: function(day,month,year) //fill calendar with new data
	{
		var todayDate = new Date();
		var obj = this;

		// checking input data
		if( month == 0 || day == 0 || year == 0 )
		{
			month = todayDate.getMonth()+1;
			day = todayDate.getDate();
			year = todayDate.getFullYear();
		};
		// For Previous Month
		var PM_prev_month = month -1;
		var PM_prev_year = year;
		if (PM_prev_month == 0)
		{
			PM_prev_month = 12;
			PM_prev_year = year -1;
		};
		// For Next Month
		var NM_next_month = Number(month) +1;
		var NM_next_year = year;
	    
		if (NM_next_month == 13)
		{
			NM_next_month = 1;
			NM_next_year = Number(year) + 1;
		};

		//Define drop down select
		this.prevMonthSwitcher.onclick = CreateChangeMonthFunc(obj, PM_prev_month, PM_prev_year);
		this.nextMonthSwitcher.onclick = CreateChangeMonthFunc(obj, NM_next_month, NM_next_year);
		this.currentMonth.innerHTML = this.m_names[month - 1] + '&nbsp;' + year + '&nbsp;';

		var k=0, m_num, evt_middle, cal_txt, txt;
		for (i=-6; i<7; i++) {
			a = $("dd_month_"+i);
			m_num = Number(month)-1 + i;
			if (m_num<0) {
				a.onclick = CreateChangeMonthFunc(obj, (12 + m_num + 1), (year-1));
				a.innerHTML = this.m_names[12 + m_num] +  '&nbsp;' + (year-1);
			}
			else if (m_num>=0 && m_num<=11) {
				a.onclick = CreateChangeMonthFunc(obj, (m_num+1), year);
				if ((m_num+1) == Number(month)) a.style.fontWeight = 'bold';
				a.innerHTML = this.m_names[m_num] +  '&nbsp;' + year;
			}
			else {
				a.onclick = CreateChangeMonthFunc(obj, (k+1), (year+1));
				a.innerHTML = this.m_names[k] +  '&nbsp;' + (year+1);
				k++;
			}
		};

		//fill calendar table headers with values of week days
		var tdHeaders = $('calendarWeekdayHeaders').getElementsByTagName("td");
		for(i = 0; i<tdHeaders.length; i++)
		{
			if (setcache['weekstartson'] == 0) {
				tdHeaders[i].innerHTML = (i==0)?this.WeekDaysNames[6]:this.WeekDaysNames[i-1];	
			} else {
				tdHeaders[i].innerHTML = this.WeekDaysNames[i]; // 'Mo','Tu','We','Th','Fr','Sa','Su'
			}
		};

		//Define days in calendar table
		var days_in_month = this.GetDaysInMonth(month,year);
		month = month - 1;

		var mDate = new Date(year,month,day);
		//load _alladys_events
		if (year != todayDate.getFullYear() && (month == 11 || month == 0)) {
			var new_dates = LoadAllYearEvents(mDate);
			for (i in new_dates) {
				if (typeof(new_dates[i])=="function" || i.search(/JSON/i)>-1) 
				{ 
					continue;
				} else {
					if (typeof(all_events_dates["'"+i+"'"]) == "undefined") all_events_dates[i] = 1;
				}
			}
		};

		var firstdayDate = new Date(mDate.getFullYear(),mDate.getMonth(),1); // first day of month

		var lastdayDate = new Date(mDate.getFullYear(),mDate.getMonth(),days_in_month);	// last day of month
		var firstshowdayDate = firstdayDate.setTime(firstdayDate.getTime() - this.dms * this.GetDay(firstdayDate)); //in millisecconds
		var lastshowdayDate  = lastdayDate.setTime(lastdayDate.getTime() + (this.dms * ( 6 - this.GetDay(lastdayDate) ))); //in milliseconds

		this.weeksnum = this.GetWeeksNum(firstshowdayDate, lastshowdayDate);
		var firstAddWeeks;
		var lastAddWeeks;
		switch (this.weeksnum)
		{
			case 4:
				firstAddWeeks = 1;
				lastAddWeeks  = 2;
				break;
			case 5:
				firstAddWeeks = 1;
				lastAddWeeks  = 1;
				break;
			case 6:
				firstAddWeeks = 0;
				lastAddWeeks  = 1;
				break;
			default:
				firstAddWeeks = 0;
				lastAddWeeks  = 0;
		};
		//recalc first and last days in table
		var Gfd=gtime(firstdayDate);
		var Gld=gtime(lastdayDate); 
		firstshowdayDate = new Date(from8(move8(Gfd.to8,1-Gfd.week-7 * firstAddWeeks - (1-setcache['weekstartson']))));
		lastshowdayDate = new Date(from8(move8(Gld.to8,7-Gld.week+7 * lastAddWeeks - (1-setcache['weekstartson']))));

		//recalc numers of weeks in table
		this.weeksnum = this.GetWeeksNum(firstshowdayDate, lastshowdayDate);
		this_date = new Date(firstshowdayDate);

		for(w = 1; w <= this.weeksnum; w++){
			var cur_week = $("sc_week_"+w);
			cur_day = cur_week.getElementsByTagName("td");
			for(k = 1; k <=7; k++){
				TD = cur_day[k-1];

				td8=to8(this_date);
				if(this_date.getMonth() == todayDate.getMonth() && this_date.getFullYear() == todayDate.getFullYear() && this_date.getDate() == todayDate.getDate()){
					TD.className = 'today';
				} else if (view == DAY && (td8 == showLimits.day)) {
					TD.className = 'select';
				} else if (view==WEEK && (td8 >= showLimits.weekFrom && td8 <= showLimits.weekTill )) {
					TD.className = 'select';
				} else if (view==MONTH && (td8 >= miniLimits.monthFrom && td8 <= miniLimits.monthTill)) {
					TD.className = 'select';
				} else if (setcache['showweekends'] == 1){
					if ((k==6 || k==7) && setcache['weekstartson'] == 1) TD.className = 'weekend';
					else if ((k==1 || k==7) && setcache['weekstartson'] == 0) TD.className = 'weekend';
					else TD.className = 'basic';
				} else {
					TD.className = 'basic';
				};
				
				var link_class = 'CalLink';
				if (this_date.getMonth() != month) {
					if(setcache['showweekends'] == 1) link_class = (k==6 || k==7)? 'CalLinkInactiveWeekend':'CalLinkInactive';
					else if (setcache['showweekends'] == 0) link_class = (k==1 || k==7)? 'CalLinkInactiveWeekend':'CalLinkInactive';
				};
				var day_const = fnum(this_date.getFullYear(),4)+fnum((this_date.getMonth()+1),2)+fnum(this_date.getDate(),2);
				CleanNode(TD);
				var a = CreateChild(TD, 'a');
				a.id = day_const;
				a.className = link_class;
				a.href = 'javascript:void(1);';
				var obj = this;
				var this_month = this_date.getMonth();
				/*if ((view == DAY && (td8 != showLimits.day)) || 
					(view == WEEK && !(td8 >= showLimits.weekFrom && td8 <= showLimits.weekTill)) || 
					(view == MONTH && this_month!= month)
				)
				{*/
					a.onclick = function() { switch2date(this.id); }
				//}

				a.innerHTML = this_date.getDate();

				var evex=(typeof(all_events_dates["'"+day_const+"'"]) != "undefined"); 

				a.style.fontWeight=(evex?"bold":"normal");
				a.style.fontStyle="normal";

				this_date.setDate(this_date.getDate() + 1);
			}
		}
	}

};//CCalendarTable.prototype

/********************/

function CCalendarTableEvent (parent_id)
{
	this.WeekDaysNames = [
		Lang.CalendarTableDayMonday,
		Lang.CalendarTableDayTuesday,
		Lang.CalendarTableDayWednesday,
		Lang.CalendarTableDayThursday,
		Lang.CalendarTableDayFriday,
		Lang.CalendarTableDaySaturday,
		Lang.CalendarTableDaySunday
	];
	this.m_names = [
		Lang.FullMonthJanuary,
		Lang.FullMonthFebruary,
		Lang.FullMonthMarch,
		Lang.FullMonthApril,
		Lang.FullMonthMay,
		Lang.FullMonthJune,
		Lang.FullMonthJuly,
		Lang.FullMonthAugust,
		Lang.FullMonthSeptember,
		Lang.FullMonthOctober,
		Lang.FullMonthNovember,
		Lang.FullMonthDecember
	];
	this.dms = 86400000; // 86400000 milliseconds in one day
	this.weeksnum = 7;

	this.SelectedDate		= null;
	this.HtmlContainer		= null;
	this.prevMonthSwitcher	= null;
	this.nextMonthSwitcher	= null;
	this.currentMonth		= null;
	this.weekday_Table		= null;
	this.parent_id			= parent_id;
	this.inp				= null;

	this.Create();
	this.SetDateFromPicker = function (day,month,year) { this.Hide(); }

}


CCalendarTableEvent.prototype = {
	//Show Calendar after onClick on img
	Create: function ()
	{
		this.HtmlContainer = $(this.parent_id);
		this.HtmlContainer.className = 'wm_hide';

		// create TABLE
		var m = CreateChild(this.HtmlContainer, 'table');
		m.className = 'Calendar_Title';
		m.style.margin = '0px';
		m.style.borderColor = '#696969';
		m.style.color = '#696969';
		var m_tr_2 = m.insertRow(0);
		var m_td_21 = m_tr_2.insertCell(0);
		var m_td_22 = m_tr_2.insertCell(1);
		var m_td_23 = m_tr_2.insertCell(2);

		m_td_21.style.width = '32px';
		m_td_21.style.textAlign = 'right';

		m_td_23.style.width = '32px';
		m_td_23.style.textAlign = 'left';

		m_td_22.style.textAlign = 'center';
		m_td_22.style.width = '40%';

		// Month Row
		var obj = this;
		var img = CreateChild(m_td_21, 'img');
		img.id = this.parent_id + "_prevMonth";
		img.alt = Lang.AltPrevMonth;
		img.title = Lang.AltPrevMonth;
		img.style.border = '0';
		img.src = './calendar/skins/calendar/minicalendar_arrow_left_activ.gif';
		img.style.width = "11px";
		img.style.height = "11px";
		img.style.cursor = "pointer";
		this.prevMonthSwitcher = img;

		// td_middle_place
		this.currentMonth = m_td_22;

		img = CreateChild(m_td_23, 'img');
		img.id = this.parent_id + "_nextMonth";
		img.alt = Lang.AltNextMonth;
		img.title = Lang.AltNextMonth;
		img.style.border = '0';
		img.src = './calendar/skins/calendar/minicalendar_arrow_right_activ.gif';
		img.style.width = "11px";
		img.style.height = "11px";
		img.style.cursor = "pointer";
		this.nextMonthSwitcher = img;
		this.inp = CreateChildWithAttrs(this.HtmlContainer, 'input', [['type', 'hidden'], ['name', this.parent_id+"_currentMonth"], ['id', this.parent_id+"_currentMonth"]]);


		this.weekday_Table = CreateChild(this.HtmlContainer, 'table');
		this.weekday_Table.cellPadding = '0';
		this.weekday_Table.cellSpacing = '0';
		this.weekday_Table.valign = 'middle';
		this.weekday_Table.className = 'calendar_block';
		this.weekday_Table.style.borderColor = '#696969';

		var TR = this.weekday_Table.insertRow(0);
		for(wd = 0; wd < 7; wd++)
		{
			var TD = TR.insertCell(wd);
			TD.className = 'title';
		};

		for(w = 1; w <= this.weeksnum; w++){
			var TR = this.weekday_Table.insertRow(w);
			for(k = 1; k<=7; k++){
				var TD = TR.insertCell(-1);
				TD.valign = 'middle';
			}
		};

		this.RefreshCalendarSelector(0,0,0);
	},


	Show: function ()
	{
		this.HtmlContainer.className = 'box';
		this.HtmlContainer.style.position = 'absolute';
		this.HtmlContainer.style.zIndex = '2001';
	},
	
	Hide: function ()
	{
		this.HtmlContainer.className = 'wm_hide';
	},

	//Return days in month
	GetDaysInMonth: function (month,year)
	{
		month = ( month<1 ) ? 0 : (( month>12 ) ? 11 : month - 1);
		var arDaysInMonth_Usual = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
		var arDaysInMonth_Leap 	= [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
		if ( (year % 4) == 0 && (year % 100) != 0 || (year % 400) == 0 ) 
			return arDaysInMonth_Leap[month];
		else
			return arDaysInMonth_Usual[month];
	},
	
	// Return day in week: 0 - Mo, 1 - Tu ...
	GetDay: function ( cDate )
	{
		var wDay = cDate.getDay() - 1;
		if ( wDay == -1 ) wDay = 6;
		return wDay;
	},

	GetWeeksNum: function (firstshowdayDate, lastshowdayDate)
	{
		var weeksnum = Math.ceil(((lastshowdayDate - firstshowdayDate) / this.dms) / 7);
		return weeksnum;
	},


	RefreshCalendarSelector: function(day,month,year) //fill calendar with new data
	{
		var todayDate = new Date();
		var obj = this;

		// checking input data
		if( month == 0 || day == 0 || year == 0 )
		{
			month = todayDate.getMonth()+1;
			day = todayDate.getDate();
			year = todayDate.getFullYear();
		};
		// For Previous Month
		var PM_prev_month = month -1;
		var PM_prev_year = year;
		if (PM_prev_month == 0)
		{
			PM_prev_month = 12;
			PM_prev_year = year -1;
		};
		// For Next Month
		var NM_next_month = Number(month) +1;
		var NM_next_year = year;

		if (NM_next_month == 13)
		{
			NM_next_month = 1;
			NM_next_year = Number(year) + 1;
		};

		//Define drop down select
		this.prevMonthSwitcher.onclick = CreateChangeMonthFunc(obj, PM_prev_month, PM_prev_year);
		this.nextMonthSwitcher.onclick = CreateChangeMonthFunc(obj, NM_next_month, NM_next_year);

		this.currentMonth.innerHTML = this.m_names[month - 1] + '&nbsp;' + year;
		this.inp.value = month+'_'+year;

		//Define days in calendar table
		var days_in_month = this.GetDaysInMonth(month,year);
		month = month - 1;

		var mDate = new Date(year,month,day);

		var firstdayDate = new Date(mDate.getFullYear(),mDate.getMonth(),1); // first day of month
		var lastdayDate = new Date(mDate.getFullYear(),mDate.getMonth(),days_in_month);	// last day of month

		var firstshowdayDate = firstdayDate.setTime(firstdayDate.getTime() - this.dms * this.GetDay(firstdayDate)); //in millisecconds
		var lastshowdayDate  = lastdayDate.setTime(lastdayDate.getTime() + (this.dms * ( 6 - this.GetDay(lastdayDate) ))); //in milliseconds

		this.weeksnum = this.GetWeeksNum(firstshowdayDate, lastshowdayDate);
		var firstAddWeeks;
		var lastAddWeeks;
		switch (this.weeksnum)
		{
			case 4:
				firstAddWeeks = 1;
				lastAddWeeks  = 2;
				break;
			case 5:
				firstAddWeeks = 1;
				lastAddWeeks  = 1;
				break;
			case 6:
				firstAddWeeks = 0;
				lastAddWeeks  = 1;
				break;
			default:
				firstAddWeeks = 0;
				lastAddWeeks  = 0;
		};
		//recalc first and last days in table
		var Gfd=gtime(firstdayDate);
		var Gld=gtime(lastdayDate); 
		firstshowdayDate = new Date(from8(move8(Gfd.to8,1-Gfd.week-7 * firstAddWeeks - (1-setcache['weekstartson']))));
		lastshowdayDate = new Date(from8(move8(Gld.to8,7-Gld.week+7 * lastAddWeeks - (1-setcache['weekstartson']))));

		//recalc numers of weeks in table
		this.weeksnum = this.GetWeeksNum(firstshowdayDate, lastshowdayDate);
		firstdayDate = (mDate.getTime()-((mDate.getDate()-1)*this.dms)); // first day of month
		lastdayDate = (mDate.getTime()+((days_in_month-mDate.getDate())*this.dms));	// last day of month

		this_date = new Date(firstshowdayDate);

		var cur_week = this.weekday_Table.getElementsByTagName("tr");
		for(w = 0; w <= this.weeksnum; w++){
			if (w==0) {
				tdHeaders = cur_week[w].getElementsByTagName("td");
				for(i = 0; i<tdHeaders.length; i++)
				{
					if (setcache['weekstartson'] == 0) {
						tdHeaders[i].innerHTML = (i==0)?this.WeekDaysNames[6]:this.WeekDaysNames[i-1];
					} else {
						tdHeaders[i].innerHTML = this.WeekDaysNames[i]; // 'Mo','Tu','We','Th','Fr','Sa','Su'
					}
				}
			} else {
				cur_day = cur_week[w].getElementsByTagName("td");
				for(k = 1; k <=7; k++){
					TD = cur_day[k-1];

					td8=to8(this_date);
					if(this_date.getMonth() == todayDate.getMonth() && this_date.getFullYear() == todayDate.getFullYear() && this_date.getDate() == todayDate.getDate()){
						TD.className = 'today';
					} else {
						TD.className = 'basic';
					};

					var link_class = 'CalLink';
					if (this_date.getMonth() != month) {
						if(setcache['showweekends'] == 1) link_class = (k==6 || k==7)? 'CalLinkInactiveWeekend':'CalLinkInactive';
						else if (setcache['showweekends'] == 0) link_class = (k==1 || k==7)? 'CalLinkInactiveWeekend':'CalLinkInactive';
					};
					var day_const = fnum(this_date.getFullYear(),4)+fnum((this_date.getMonth()+1),2)+fnum(this_date.getDate(),2);
					CleanNode(TD);
					var a = CreateChild(TD, 'a');
					a.id = this.parent_id+"_"+day_const;
					a.className = link_class;
					a.href = 'javascript:void(1);';
					var obj = this;
					a.onclick = function() {
						EventCreatePickerFunc(obj, this.id.slice((this.id.indexOf("_")+1),this.id.length));
					};
					a.innerHTML = this_date.getDate();

					this_date.setDate(this_date.getDate() + 1);
				}//end for
			}
		}
	}
};//CCalendarTableEvent.prototype

function EventCreatePickerFunc(obj, dt){
	date = new Date(dt.substr(0,4), (Number(dt.substr(4,2))-1), dt.substr(6,2));
	obj.onpickdate(date);
	obj.SetDateFromPicker(date.getDate(), date.getMonth(), date.getFullYear());
}
/*
function CreatePickerFunc(obj, this_date) {
	return function () {
		obj.SetDateFromPicker(this_date.getDate(), this_date.getMonth(), this_date.getFullYear());
		switch2date(this_date); 
	}
}*/

function CreateChangeMonthFunc(obj, PM_prev_month, PM_prev_year) {
	return function () {
		obj.RefreshCalendarSelector(1, PM_prev_month, PM_prev_year);
	}
}

function CreateCalendar() {
	var calendarParent = $('mini_calendar_box');
	var till = $('EventDateTill');
	var from = $('EventDateFrom');
	var input_timeFrom = $('EventTimeFrom');
	var input_timeTill = $('EventTimeTill');


	calendarTableStart = new CCalendarTableEvent("st");
	calendarTableStart.onpickdate = function(date)
	{
		from.value = ConvertFromDateToStr(date);
		till.value = from.value;
		CreateTimeTill(input_timeTill.value, input_timeFrom.value);
	};

	calendarTableEnd = new CCalendarTableEvent("en");
	calendarTableEnd.onpickdate = function(date)
	{
		var date_from = ConvertFromStrToDate(from.value);
		if(date <= date_from){
			till.value = from.value;
			CreateTimeTill(input_timeTill.value, input_timeFrom.value);
		}else{
			till.value = ConvertFromDateToStr(date);
			CreateTimeTill(input_timeTill.value);
		}
	};
	till.onfocus = function() {
		calendarTableStart.Hide();
		calendarTableEnd.Show();
		if (timeSelectorTill != null) {
			timeSelectorTill.Hide();
		};
		if (timeSelectorFrom != null) {
			timeSelectorFrom.Hide();
		}
	};
	from.onfocus = function() {
		calendarTableStart.Show();
		calendarTableEnd.Hide();
		if (timeSelectorTill != null) {
			timeSelectorTill.Hide();
		};
		if (timeSelectorFrom != null) {
			timeSelectorFrom.Hide();
		}
	};

	calendarInManager = new CCalendarTable(calendarParent);
	calendarInManager.Hide();
	calendarInManager.Show();
}