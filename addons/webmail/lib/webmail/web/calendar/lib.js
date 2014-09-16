var mydate = new Date();
var weekdayz = new Array();
var showLimits = {day:null, weekFrom:null, weekTill:null, monthFrom:null, monthTill:null };
var miniLimits = {monthFrom:null, monthTill:null};
var drager = null;
var mycache;
var mycache_c;
var eventsChanged = true;
var minEventPos, maxEventPos;
var globalTimeTill;
var setcache;
var visar=new Array(); var viz=new Array(); var vizidz=new Array();
var shownfetch=0;
var cSt=0; var cEn=0; var cBk=new Array();
var QOpen=0;
var showHide=new Array();
var indrag=false;
var incros=new Array();

// Search for a DOM node location (par==0),
// its style (par==1) or value (par==2)

function findDOM(objectID,par) {
	if (par==1) idd = $(objectID).style;
	else idd = $(objectID);

	if (par==2) answ = idd.value;
	else answ = idd;
	return (answ);
}

function servErr(jsondata, strError) {
	if (jsondata === false) {
		HideInfo(); ShowError(strError);
		return true;
	}

	if ((jsondata['error']!=undefined)&&( jsondata['error']==true || jsondata['error']=="true" )) {
		HideInfo(); ShowError(jsondata['description']);
		return true;
	}
	return false;
}

function getClientHeight()
{ return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight; }

function getClientWidth()
{ return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth; }


function delarr(arr,ind,keep) {
	var ar2=new Array(); var j=0; var indx=Number(ind);
	for (var i in arr) {
		var ix=Number(i);
		if (ix!=indx) {
			if (keep){ ar2[i]=arr[i]; }
			else { ar2[j]=arr[i]; j++; }
		}
	};
	return(ar2);
}

function delarrid(arr,key,val) {
	var ar2=new Array(); var j=0;
	for (var i in arr) {
		 if (arr[i][key]!=val) { 
		 	if (typeof(arr[i])!="function") ar2[j]=arr[i]; j++; 
		}
	};
	return (ar2);
}

function getXMLHTTPRequest()
{
	var transport = null;
	if(window.XMLHttpRequest) {
		transport = new XMLHttpRequest();
	} else {
		if(window.ActiveXObject) {
			try
			{
				transport = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (err)
			{
				try
				{
					transport = new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (err2)
				{
				}
			}
		} else {
		}
	};
	return transport;
}

function evret(evt) {
	if (navigator.userAgent.indexOf("Gecko") >= 0) { var e = evt.target; } else { var e = evt.srcElement; };
	return (e);
}

function eventOn(ev,func,flag) {
	if (FireFoxDetect())
	{ document.addEventListener(ev,func,flag); }
	else
	{ document.attachEvent(("on"+ev),func); }
}

function eventOff(ev,func,flag) {
	if (FireFoxDetect())
	{ document.removeEventListener(ev,func,flag); }
	else 
	{ document.detachEvent(("on"+ev),func); }
}

function eventOnAny(obj,ev,func,flag) {
	if (FireFoxDetect())
	{ obj.addEventListener(ev,func,flag); }
	else
	{ obj.attachEvent(("on"+ev),func); }
}

function eventOffAny(obj,ev,func,flag) {
	if (FireFoxDetect())
	{ obj.removeEventListener(ev,func,flag); }
	else 
	{ obj.detachEvent(("on"+ev),func); }
}

function jscss(a,o,c1,c2)
{
  switch (a){
    case 'swap':
      o.className=!jscss('check',o,c1)?o.className.replace(c2,c1): o.className.replace(c1,c2);
    break;
    case 'add':
      if(!jscss('check',o,c1)){o.className+=o.className?' '+c1:c1;}
    break;
    case 'remove':
      var rep=o.className.match(' '+c1)?' '+c1:c1;
      o.className=o.className.replace(rep,'');
    break;
    case 'check':
      return new RegExp('\\b'+c1+'\\b').test(o.className);
    break;
  }
}

function boxcalc(md,ad) {
	var left=0;
	var top = 0;
	var logo = $("logo");
	var toolbar = $('toolbar');
	var accountslist = $('accountslist');
	var upper_indent = $('upper_indent');
	var day_headers_day = $('day_headers_day');
	var day_headers_week = $('day_headers_week');
	var day_headers_month = $('day_headers_month');

	top += (logo != null)? logo.offsetHeight : 0;
	top += (accountslist != null)? accountslist.offsetHeight: 0;
	top += (toolbar != null)? toolbar.offsetHeight: 0;
	top += (upper_indent != null)? upper_indent.offsetHeight: 0;
	if (view == DAY) {
		top += (day_headers_day!=null)? day_headers_day.offsetHeight: 0;
	} else if (view == WEEK) {
		top += (day_headers_week!=null)? day_headers_week.offsetHeight: 0;
	} else { //view == MONTH
		top += (day_headers_month!=null)? day_headers_month.offsetHeight: 0;
	};

	if (md<3) {
		left+=41; 
		if (ad==0) {
			var vnod=$("area_1_"+((md==2)?"week":"day")); 
			var vhig=cutpx(vnod.offsetHeight); vhig=Number(vhig)+2;
			top+=vhig;
		}
	};
	var type="2"; if ((ad==1)&&(md<3)) type="1";
	var cont="d"; if (md==2) cont="w"; if (md==3) cont="_month";
	var tnam="grid_"+type+cont;
	var ddom=$(tnam);
	var width=ddom.offsetWidth; var height=ddom.offsetHeight;
	return {top:top,left:left,width:width,height:height,name:tnam,dom:ddom};
}

function invoke(aEvent,evobj) {
	area.skip=true; indrag = false;
	$('edit_form').style.display='none';
	area.resizing = false;
	obj = $(evobj.id.substr(0,evobj.id.length - 4));
	idd=obj.id;
	modez=isplit(idd); vMid=Number(modez.id); vMmd=Number(modez.mode)+1; vMad=modez.allday;
	mybox=boxcalc(vMmd,vMad); inipos=coordss(aEvent,obj,vMmd,vMad,mybox,0);
	cew=(mybox.width/((vMmd==1)?1:7));
	pickobj={obj:obj,id:modez.id,md:vMmd,ad:vMad,h:obj.offsetHeight};
	stx=inipos.x; sty=inipos.y;

	evlink=mycache.ids[modez.id];  
	tf=gtime(dt2date(evlink["event_timefrom"]));
	//tt=gtime(dt2date(evlink["event_timetill"]),24);

	if ((vMmd==3)||(vMad==1)) {//month
		mox=pickobj.obj.offsetLeft;
	} else if (vMmd==1) {//day
		mox=0;
	} else if (vMmd==2) {//week
		wwd=tf.week; 
		if (setcache['weekstartson'] == 0) {
			if (wwd == 7) wwd = 0;
			mox=(mybox.width/7)*(wwd); 
		} else {//setcache['weekstartson'] == 1
			mox=(mybox.width/7)*(wwd-1);
		}
	}

	moy=pickobj.obj.offsetTop;
	if (evlink['event_allday']==1)
	{ 
		cellno=Math.floor((stx-mox)/cew);
		mox+=cellno*cew;
	};
	leftpos=mox+mybox.left;
	toppos=moy+mybox.top;
	if ((vMmd<3)&&(vMad!=1))
	{
		scrollbox='area_2_'+ ((vMmd==1)?"day":"week");
		ofs=($(scrollbox).scrollTop); 
		toppos-=ofs;
	};
	eventOff("mouseup",gotnew,false);
	eventOn("mouseup",gotcha,true);
	eventOn("mousemove",emovin,true);
}

function dragdisp(aEvent) {
	var myEvent = aEvent ? aEvent : window.event; 

	myEvent.cancelBubble=true;
	drager=document.createElement("DIV");
	drager['id']='drager';
	if (vMad==1) drager['id'] = 'drager allday';
	drager.style.display="none";
	drager.style.position="absolute";

	drager.style.borderWidth="3px";
	drager.style.borderColor="#CCCCCC";
	var row = mycache_c.calendars[evlink['calendar_id']];
	drager.className="eventcontainer_"+mycache_c.calendars[evlink['calendar_id']]['calendar_color'] + ' dad';
	if (vMad==1) {
		drager.className="eventcontainer_"+mycache_c.calendars[evlink['calendar_id']]['calendar_color'] + ' dad';
	} else {
		drager.className="eventcontainer_"+mycache_c.calendars[evlink['calendar_id']]['calendar_color'];
	}

	div1=document.createElement("DIV");
	div1['id']=mixid;
	div1.className="event";
	div1.style.width=cew+"px";
  
	div2=document.createElement("DIV");
	div2.className="a";

	div3=document.createElement("DIV");
	div3.className="b";

	div1.appendChild(div2);
	div1.appendChild(div3);

	div4=document.createElement("DIV");
	div4.className="event_middle";
	div4.style.height=(pickobj.obj.offsetHeight-4)+'px';

	div5=document.createElement("DIV");
	div5.className="event_text";

	div6=document.createElement("DIV");
	div6.className="time";

	if(setcache['timeformat'] == 1)
	{
		var tfDateObj = dt2date(evlink["event_timefrom"]);
		var ttDateObj = dt2date(evlink["event_timetill"]);
		var chour_f = tfDateObj.getHours();
		var cmin_f = tfDateObj.getMinutes();
		var chour_t = ttDateObj.getHours();
		var cmin_t = ttDateObj.getMinutes();
		tf = ((chour_f  == 0)? '12' : ((chour_f > 12)? chour_f -12 : chour_f)) + ((cmin_f == 0) ? ' ' : ':'+ cmin_f + ' ') + ((chour_f <12) ? "AM" : "PM");
		tt = ((chour_t  == 0)? '12' : ((chour_t > 12)? chour_t -12 : chour_t)) + ((cmin_t == 0) ? ' ' : ':'+ cmin_t + ' ') + ((chour_t <12) ? "AM" : "PM");
		titl= tf +' - '+ tt;
	} else { //setcache['timeformat'] == 2
		tf=gtime(dt2date(evlink["event_timefrom"]));
		tt=gtime(dt2date(evlink["event_timetill"]),24);
		titl=tf.time+' - '+tt.time;
	};
	if (vMad==1) titl=" ";
    
	tex1=document.createTextNode(titl); 
	if (vMad==1) div6.style.marginTop="3px";
	div6.appendChild(tex1); 

	div7=document.createElement("DIV"); 
	div7.className="text";
	tex2=document.createTextNode(evlink["event_name"]);
	div7.appendChild(tex2);
	div5.appendChild(div6); div5.appendChild(div7); 
	div4.appendChild(div5); div1.appendChild(div4); 
	div8=document.createElement("DIV"); div8.className="b"; div1.appendChild(div8); 
	div9=document.createElement("DIV"); div9.className="a"; div1.appendChild(div9);
	drager.appendChild(div1);
	document.body.appendChild(drager);

//  mybox.dom.appendChild(drager);
	drager.style.left=leftpos+"px";
	drager.style.top=toppos+"px";
	//drager.style.left=mox+"px"; drager.style.top=moy+"px"; 

	//drager.style.width=(mybox.width/7)+"px"; drager.style.height="4ex"; 
	drager.style.zIndex=65535;
	drager.style.display="inline";

	dbeg=coordss(aEvent,obj,vMmd,vMad,mybox,1,this); dhigh=dbeg;
	dragshow(dbeg,mybox,pickobj,vMmd,vMad);  
	pickobj.obj.style.display="none";

	document.onselectstart = function() {return false;};
	document.onselect = function() {return false;};
	
}

function dragshow(dcurr,mybox,pickobj,md,ad) {
	dragit=document.createElement("DIV");
	dragit.style.position="absolute";
	dragit.style.left=1+dcurr.xcol+"px";
	dragit.style.top=1+dcurr.xrow+"px";
	dragit.className="dragit_area";
	var wid=cutpx(drager.firstChild.style.width);

	var hig=0;
	if (md<3) { 
		if (ad!=1) hig=pickobj.obj.offsetHeight-1;
		else hig=mybox.height-1; 
	} else { hig=Math.floor(mybox.height/6)-1; }
	dragwide(mybox);
	dragit.style.height=hig+'px';
	dragit.style.zIndex=65500;
	dragit.style.display="block";
	dragit['id']='dragit';
	mybox.dom.appendChild(dragit);
}

function dragmove(dcurr,mybox,md,ad) {
	dragit.style.left=dcurr.xcol+1+"px";
	dragit.style.top=dcurr.xrow+1+"px";
	dragwide(mybox);
}

function dragwide(mybox) {
	var wid=Math.floor(cutpx(drager.firstChild.style.width));
	var wmax=Math.floor(mybox.width-cutpx(dragit.style.left));
	if (wid>wmax) wid=wmax;
	dragit.style.width=wid+'px';
}

function dragkill(mybox) {
	dragit.style.display='none';
	mybox.dom.removeChild(dragit);
}

function emovin(ev) {
	if(typeof span1_pres != "undefined" && span1_pres ){
		inline(ev,pickobj.obj);
	}else{
		zb=coordss(ev,pickobj.obj,pickobj.md,pickobj.ad,mybox,0);
		dsX=mybox.left+mox+zb.x-stx; dsY=mybox.top+moy+zb.y-sty;
		if ((!indrag)&&((Math.abs(inipos.x-zb.x)>5)||(Math.abs(inipos.y-zb.y)>5))) { dragdisp(ev); indrag=true; }
			if (indrag) {
				if ((pickobj.md<3)&&(pickobj.ad!=1)) {
				scrollbox='area_2_'+ ((pickobj.md==1)?"day":"week");
				ofs=($(scrollbox).scrollTop);
				dsY-=ofs;
			}

			drager.style.left=dsX+"px";
			drager.style.top=dsY+"px";
			dcurr=coordss(ev,pickobj.obj,pickobj.md,pickobj.ad,mybox,1,this,1,pickobj.h);
			spc=(inipos.x-zb.x)+' '+(inipos.y-zb.y)+' || ';
			spc+=dcurr.t+" $$ "+dcurr.d;
			var realhigh=pickobj.h;
			spc+=' | '+dcurr.xrow+' + '+realhigh+' ~ '+mybox.height;
			if ((dcurr.xrow+realhigh)<=mybox.height) {
                if ((dcurr.d!=dhigh.d)||(dcurr.t!=dhigh.t)) {
					dragmove(dcurr,mybox,pickobj.md,pickobj.ad);
					dhigh=dcurr;
				}
			}
		}
	}
}

function gotcha(ev) {
	area.pres = false;
	area.skip = false;
	eventOff("mouseup",gotnew,false);
	eventOff("mouseup",gotcha,true);
	eventOff("mousemove",emovin,true);
	ev.cancelBubble=true;
	eventOff("mouseup",gotcha,true);/*!!!*/
	eventOff("mousemove",emovin,true);/*!!!*/

	if (!indrag) {
		pickobj.obj.style.display="block";

		zb=coordss(ev,pickobj.obj,pickobj.md,pickobj.ad,mybox,0);/*!!!*/
		if ((Math.abs(inipos.x-zb.x)<3)&&(Math.abs(inipos.y-zb.y)<3)) {
			if(typeof span1_pres != "undefined" && span1_pres )
			{
				inline(ev,pickobj.obj);
			}else{
				choose(ev,pickobj.obj);
			}  
		}
		span1_pres = false;
	} else {
		ShowInfo(Lang.InfoSaving);
		document.body.removeChild(drager);
		dragkill(mybox);
		document.onselectstart = function() {};
		document.onselect = function() {};
		var id_calendar = mycache.ids[pickobj.id]["calendar_id"];
		var prevFromDate = dt2date(mycache.ids[pickobj.id]["event_timefrom"]);
		var prevTillDate = dt2date(mycache.ids[pickobj.id]["event_timetill"]);
		var eventLen = prevTillDate.getTime() - prevFromDate.getTime() ; //in milliseconds
		var date = dhigh.d.toString();
		if (dhigh.t == "xx:xx") {
			var time_from = fnum(prevFromDate.getHours(),2)+":"+fnum(prevFromDate.getMinutes(),2);
		} else {
			var time_from = dhigh.t;
		}
		var fromDate = new Date(date.substr(0,4), (date.substr(4,2)-1), date.substr(6,2), (time_from).substr(0,2), (time_from).substr(3,2));
		var tillDate = new Date(fromDate.getTime() + eventLen);

		var strTillTime = fnum(tillDate.getHours(),2) + ":" + fnum(tillDate.getMinutes(),2);
		var strTillDate = to8(tillDate);
		var url = processing_url+'?action=update_event&event_id='+pickobj.id+'&calendar_id='+id_calendar+'&from='+escape(dhigh.d)+'&time_from='+escape(time_from)+'&till='+strTillDate+'&time_till='+escape(strTillTime)+'&nocache=' + Math.random();

		var req = getXMLHTTPRequest();
		var res = '';
		if (req) {
			try {
				req.open("GET",url,false);
				req.send(null);
				res = req.responseText;
			} catch (e) { }
			var event = new Array(); 
			var result = res.parseJSON();
			if (servErr(result, Lang.ErrorUpdateEvent)) {
				event = mycache.ids[pickobj.id];
			}
			else {
				for(var i in result)
				{
					if (typeof (result[i]) == 'function') continue;
					event[i] = result[i];
				}
			};
			delevent(event);
			addevent(event,1);
			var wdd = 0;
			if ((dbeg.d>=showLimits.weekFrom) && (dbeg.d<=showLimits.weekTill)) {
				dotrender(mycache.w, mycache.days, mycache.ids, dbeg.d, 1, 7, wdd);
				wdd++;
			}
			if ((dhigh.d!=dbeg.d) && (dhigh.d>=showLimits.weekFrom) && (dhigh.d<=showLimits.weekTill)) dotrender(mycache.w, mycache.days, mycache.ids, dhigh.d, 1, 7, wdd);
			var ddd = 0;
			var gmydate = gtime(mydate);
			var my8 = Number(gmydate.to8);
			var dt1 = Number(dhigh.d);
			var dt0 = Number(dbeg.d);
			if (my8 == dt1) {
				dotrender( mycache.d,mycache.days,mycache.ids,dt1,0,1,ddd);
				ddd++;
			}

			if ((my8 == dt0) && (dt1!=dt0)) { dotrender(mycache.d, mycache.days, mycache.ids, dt0, 0, 1, ddd); }
			ReRenderMonthly();
			RecalcScrollArrows();
		}
		HideInfo();
		indrag=0;
	}
}

function delevent(event_data) {
	var eid = event_data['event_id'];
	var eold = mycache.ids[eid];
	if (typeof(eold) != 'undefined') {
		var ev_timefrom = dt2date(eold["event_timefrom"]); 
		var ev_timetill = dt2date(eold["event_timetill"]);
		var oldate=to8(ev_timefrom);
		var oltill=to8(ev_timetill);
		gett=gtime(ev_timetill);
		if (gett.fh==0) oltill=move8(oltill,-1);

		if ((mycache.d[eid]!=undefined)&&(mycache.d[eid])) {
			var edata=mycache.ids[eid];
			var ln=mycache.d[eid].length;
			for (i=0; i<ln; i++) {
				var mixid="event_"+edata["event_id"]+"_"+i+"_0_"+edata["event_allday"];
				var ntd=$(mixid);
				var ntp=ntd.parentNode;
				ntp.removeChild(ntd);
			};
			mycache.d=delarr(mycache.d,eid,true);
		};

		if ((mycache.w[eid]!=undefined)&&(mycache.w[eid])) {
			var edata=mycache.ids[eid];
			var ln=mycache.w[eid].length;
			for (i=0; i<ln; i++) {
				var mixid="event_"+edata["event_id"]+"_"+i+"_1_"+edata["event_allday"];
				var ntd=$(mixid);
				var ntp=ntd.parentNode;
				ntp.removeChild(ntd);
			};
			mycache.w=delarr(mycache.w,eid,true);
		};

		if ((mycache.m[eid]!=undefined)&&(mycache.m[eid])) {
			var edata=mycache.ids[eid];
			var ln=mycache.m[eid].length;
			for (i=0; i<ln; i++) {
				var mixid="event_"+edata["event_id"]+"_"+i+"_2_"+edata["event_allday"];
				var ntd=$(mixid);
				var ntp=ntd.parentNode;
				ntp.removeChild(ntd);
			};
			mycache.m=delarr(mycache.m,eid,true);
		};
		var cudate=oldate;
		
		do {
			mycache.days[cudate]=delarrid(mycache.days[cudate],"event_id",eid);
			cudate=move8(cudate,1);
		} while (cudate<=oltill)

		if (ev_timefrom == ev_timetill) {
			if (mycache.days[ev_timefrom].length==0) delete all_events_dates["'"+ev_timefrom+"'"];
		} else {
			var days = (ev_timetill.getTime() - ev_timefrom.getTime())/86400000;
			for (var i = 0; i<days; i++) {
				var inc = new Date(ev_timefrom.getTime() + 86400000*i);
				var formated_timefrom = to8(inc);
				if (typeof(mycache.days[formated_timefrom])=="undefined") {
					delete all_events_dates["'"+formated_timefrom+"'"];
				} else {
					if (mycache.days[formated_timefrom].length==0) delete all_events_dates["'"+formated_timefrom+"'"];
				}
			}
		}
	}
}

function addevent(dval,invis) {
	if (invis==undefined) invis = 0;  /*is global - ??*/
	var eid = Number(dval['event_id']);
	var timefrom_dateobj = dt2date(dval["event_timefrom"]);
	var timetill_dateobj = dt2date(dval["event_timetill"]);
	var dcdate=to8(timefrom_dateobj);
	var dctill=to8(timetill_dateobj);

	gett=gtime(timetill_dateobj);
	if (gett.fh==0) dctill=move8(dctill,-1);

	var cudate=dcdate; var zpi;
	do {
		zpi=0; 
		if (mycache.days[cudate]!=undefined) { 
			zpi=mycache.days[cudate].length; 
		} else { 
			mycache.days[cudate] = new Array(); 
		}
		mycache.days[cudate][zpi]=dval;
		cudate=move8(cudate,1);
	} while (cudate<=dctill)
	mycache.ids[eid]=dval;

	if (dcdate==showLimits.day) { render_event((mycache.d[eid]=render_calc_day_single(dval)),dval,0); }
	if (( (dcdate>=showLimits.weekFrom)&&(dcdate<=showLimits.weekTill) )||( (dctill>=showLimits.weekFrom)&&(dctill<=showLimits.weekTill) )||( (dcdate<=showLimits.weekFrom)&&(dctill>=showLimits.weekTill) )) { render_event((mycache.w[eid]=render_calc_week_single(dval)),dval,1); }
    if (( (dcdate>=showLimits.monthFrom)&&(dcdate<=showLimits.monthTill) )||( (dctill>=showLimits.monthFrom)&&(dctill<=showLimits.monthTill) )||( (dcdate<=showLimits.monthFrom)&&(dctill>=showLimits.monthTill) )) { render_event((mycache.m[eid]=render_calc_month_single(dval)),dval,2,invis); }  

	if (dcdate == dctill) {
		if (typeof(all_events_dates["'"+dcdate+"'"]) == "undefined") all_events_dates["'"+dcdate+"'"]=1;
	} else {
		days = (timetill_dateobj.getTime() - timefrom_dateobj.getTime())/86400000;
		for (var i = 0; i<days; i++) {
			var inc = new Date(timefrom_dateobj.getTime() + 86400000*i);
			var formated_timefrom=to8(inc);
			if (typeof(all_events_dates["'"+formated_timefrom+"'"]) == "undefined") all_events_dates["'"+formated_timefrom+"'"]=1;
		}
	};
	calendarInManager.RefreshCalendarSelector(mydate.getDate(), mydate.getMonth()+1, mydate.getFullYear());
}

function makevent(ev,md,ad){ 
	var myEvent = ev ? ev : window.event; 
	var e = evret(myEvent); eid = e.id; /*is global - ??*/
	if (eid!="") {
		evMd=md; evAd=ad;  /*is global - ??*/
	}
}

function gotnew(ev) { 
	ev.cancelBubble=true;
	eventOff("mouseup",gotnew,false); 
	$('edit_form').style.display='block';
	$('delbut').style.display="none"; 
	var ad=evAd; var md=evMd;
/*  if (view == MONTH) { 
    var evdat=ev.target.id.str.split("_"); var gdate=gtime(from8(evdat[1])); var codate=gtime.month+'/'+gtime.day+'/'+gtime.year; var cotime='xx:xx';
    var codate1=codate; var codate2=codate; 
    var cotime1=cotime; var cotime2=cotime;
  } else { }
 */
	var cz=coordss(ev,document,md,ad,boxcalc(md,ad),0);
	gdate=gtime(from8(cz.d)); codate=gdate.month+'/'+gdate.day+'/'+gdate.year; var codate1=codate; var codate2=codate; var cotime1=cz.t; var cotime2=cz.t;

	if (cz.t!='xx:xx') { tim1=Number(cz.t.substring(0,2))+(Number(cz.t.substring(3,5))/60); tim2=tim1+0.5; hrs=Math.floor(tim2); mins=Math.round((tim2-hrs)*60); cotime2=fnum(hrs,2)+':'+fnum(mins,2); }
	$('EventDateFrom').value=codate1; $('EventDateTill').value=codate2;
	$('EventTimeFrom').value=cotime1; $('EventTimeTill').value=cotime2;
	$('EventSubject').value=""; $('EventDescription').value=""; 
}

function docscroll() {
	if (window.pageYOffset != null)
	{ return window.pageYOffset; }
	if (document.body.scrollTop != null)
	{ return document.body.scrollTop; }
	return 0;
}

function coordss(aEvent,aObj,md,ad,box,modeFinal,finlink,xflag,xhigh) {
//if (modeFinal==undefined) { modeFinal=false; }
if (finlink==undefined) { finlink=document.body; }
if (xflag==undefined) { xflag=0; }
if (xhigh==undefined) { xhigh=0; }
var xc=0; var xr=0;

//if (aEvent && (md==3)) aEvent=this.parent.event;
    var myEvent = aEvent ? aEvent : window.event; 
	var e=aObj;
	var mmx=myEvent.clientX-box.left;
	var mmy=myEvent.clientY-box.top;
	if (md<3) {
		scrollbox='area_2_'+ ((md==1)?"day":"week");
		mmy+=($(scrollbox).scrollTop);
		dbs=docscroll();
		mmy+=dbs;
	};
	if (modeFinal == 1) {
		var pageX = myEvent.clientX;
		var pageY = myEvent.clientY;
		if (FireFoxDetect()) {
			pageX = myEvent.pageX;
			pageY = myEvent.pageY;
		};
		if (OperaDetect()) {
			pageX += document.documentElement.scrollLeft - document.documentElement.clientLeft;
			pageY += document.documentElement.scrollTop - document.documentElement.clientTop;
		};
        var devx = pageX - drager.offsetLeft;
        var devy = pageY - drager.offsetTop;
		mmx -= devx; mmy -= devy;
	};
	if (md==3) {
		var hgh=$('grid_2_month').offsetHeight; 
		if (xflag==0) 
			var corow=flor(mmy,hgh,6,modeFinal); 
		else
			var corow=flor(mmy+Math.round(xhigh/2),hgh,6,false);
		xr=corow*(hgh/6);
		var wdh=$('grid_2_month').offsetWidth;
		var colum=flor(mmx,wdh,7,modeFinal);
		xc=colum*(wdh/7);
		var coex=colum+7*corow;
		var cotime='xx:xx';
		var codate=move8(showLimits.monthFrom,coex);
	} else {
		var si=(md==2 ? 'w' : 'd');
		codate='unknown';
		if (ad==1) { 
			cotime='xx:xx'; 
		} else {
			var hgh=$('grid_2'+si).offsetHeight;
			var corow=flor(mmy,hgh,48,modeFinal);
			var xr=corow*(hgh/48);
			var hrs=fnum(Math.floor(corow/2),2);
			var mins=((corow%2)?"30":"00");
			var cotime=hrs+":"+mins;
		};
		if (md==1) {
			codate=to8(mydate);
		} else {
			var wdh=$('grid_2'+si).offsetWidth;
			var colum=flor(mmx,wdh,7,modeFinal);
			var xc=colum*(wdh/7);
			var codate=weekdayz[colum];
			if (e.id=='current_day_1' || e.id=='current_day_2') var codate=to8(window.nowDate);
		}
	};
	return{x:mmx,y:mmy,t:cotime,d:codate,id:e.id,xcol:xc,xrow:xr}
}

function flor(coord,full,parts,round) {
//var sdh=wdh/7; var colum = Math.floor(mmx/sdh);
//var colum=flor(mmx,wdh,7);
	var single=full/parts;
	if (!round) {
		var sector=Math.floor(coord/single);
	} else {
		var sector=Math.round(coord/single);
	};
	if (sector<0) sector=0;
	if (sector>=parts) sector=parts-1;
	return (sector);
}

function getleft(obj) {
	var ans=null;
	var domstyle=$(obj).style; 
	if (domstyle.left) var ans=domstyle.left;
	if (domstyle.pixelLeft) var ans=domstyle.pixelLeft;
	if (domstyle.offsetLeft) var ans=domstyle.offsetLeft;
	return(ans);
}

function manager_form_create()
{
	$('manager_window').style.display = 'block';
	if(SafariDetect().isSafari) { 
		$('manager_form').style.top = window.innerHeight / 2 + 'px';
	}
	$('ef_fulldate_calendar').innerHTML = Lang.CalendarHeaderNew;
	$('delbut_calendar').style.display="none";
	$('clndform_id').value = 0;
	var CalendarSubject = $('CalendarSubject');
	CalendarSubject.value = '';
	CalendarSubject.focus();
	CalendarSubject.select();

	var color = getColor();
	var color_header = getNumberOfColor(color);
	SelectColorForNewCalendar(color, color_header, $('color_'+color));
	$('CalendarDescription').value = '';
}

function manager_form_delete(obj)
{
	var confirm_window = $('confirm_window');
	var manager_window = $('manager_window');
	confirm_window.style.display='block';

	if (confirm(Lang.ConfirmDeleteCalendar + " "+mycache_c.calendars[obj]['calendar_name']+"?")) {
		ShowInfo(Lang.InfoDeleting);
		confirm_window.style.display='none';
		if(obj == undefined)  manager_window.style.display='none';

		var req = getXMLHTTPRequest();
		var res = '';
		var id_cal;
		if (obj == undefined) {
			id_cal = Number($('clndform_id').value);
		}
		else {
			id_cal = Number(obj);
		};
		if (isNaN(id_cal)) { manager_window.style.display = 'none';return; };

		var url = processing_url+'?action=delete_calendar&calendar_id=' + id_cal+'&nocache=' + Math.random();
		var calendar = new Array(); 
		if (req) {
			try {
				req.open("GET",url,false);
				req.send(null);
				res = req.responseText;
			} catch(e) { }
			var revent = res.parseJSON();
			if (!servErr(revent,Lang.ErrorDeleteCalendar)) {
				for (var i in revent) {
					if (typeof(revent[i])!="function") calendar[i]=revent[i];
				};
				id_cal = calendar['calendar_id'];
				var row = mycache_c.calendars[id_cal];
				//delete calendar from calendars cache
				var Arr = delarr(mycache_c.calendars, id_cal);
				mycache_c.calendars = new Array();
				for (var i in Arr){
					if(Arr[i]['calendar_id'] != undefined) mycache_c.calendars[Arr[i]['calendar_id']] = Arr[i];
				};
				//delete color number from colors cache
				var ar = delarr(mycache_c.clr, row['calendar_color']);
				mycache_c.clr = new Array();
				for (var i=0; i<ar.length; i++){
					if(typeof(ar[i])!="function") mycache_c.clr[ar[i]] = ar[i];
				};
				//delete event from events cache
				for (var i in mycache.ids)
				{
					if(mycache.ids[i]['event_id'] != undefined && mycache.ids[i]['calendar_id'] == id_cal){
						delevent(mycache.ids[i]);
						calendarInManager.RefreshCalendarSelector(mydate.getDate(), mydate.getMonth()+1, mydate.getFullYear());
						if(view == MONTH)  ReRenderMonthly();
					}
				};
				var mlist = $('manager_list');
				mlist.removeChild($('calendar_'+id_cal));
				cmScroll_resize (mlist);
			}
		};
		HideInfo();
	} else {
		confirm_window.style.display='none';
		return;
	};
	manager_window.style.display = 'none';
	HideInfo();
}

function getColor()
{
	var res = new Array();
	var cnt = 1;
	var calendar = mycache_c.clr;
	for(var c = 1; c < 13; c++)
	{
		if(calendar != undefined && calendar[c] != c) res[cnt++] = c;
	};
	if(res[1] == undefined)
	{
		mycache_c.clr = new Array;
		res[1] = 1;
	};
	return res[1];
}

function manager_form_save()
{
	ShowInfo(Lang.InfoSaving);
	var CalendarSubject		= $('CalendarSubject');
	var CalendarDescription = $('CalendarDescription');
	var calendarColorNumber = $('calendarColorNumber');

	CalendarSubject.value = Trim(CalendarSubject.value);
	CalendarDescription.value = Trim(CalendarDescription.value);
	var id_cal = $('clndform_id').value;
	var subject = encodeURIComponent(CalendarSubject.value.substr(0,50));
	var description = encodeURIComponent(CalendarDescription.value.substr(0,255));

	var row = mycache_c.calendars[id_cal];

	if(subject == ''){
		alert(Lang.WarningCalendarNameBlank);
		CalendarSubject.focus();
		return;
	};

	var req = getXMLHTTPRequest();
	var res = '';
	if(calendarColorNumber.value != '0')
	{
		var color = calendarColorNumber.value;
	}else{
		if (id_cal != 0){
			var color = row['calendar_color'];
		}else{
			var color = getColor();
		}
	};

	mycache_c.clr[color] = color;
	var url = processing_url+'?action=update_calendar&calendar_id=' + id_cal + '&name=' + subject + '&content=' + description +'&color_id=' + color+'&nocache=' + Math.random();
	if (req) {
		try {
			req.open("GET",url,false);
			req.send(null);
			res = req.responseText;
		} catch(e) {  }

		var result = res.parseJSON();
		if (!servErr(result,Lang.ErrorUpdateCalendar)) { 
			var calendar_arr = new Array();
			for(var i in result)
			{
				if (typeof(result[i]) == 'function') continue;
				calendar_arr[i] = result[i];
			};
			var calendar_color = calendar_arr['calendar_color'];
			var calendar_id = calendar_arr['calendar_id'];
			var zpi=0;

			if (typeof calendar_id != "undefined") {
				if (mycache_c.calendars[calendar_id]!=undefined)
				{ zpi=mycache_c.calendars[calendar_id].length; }
				else
				{ mycache_c.calendars[calendar_id] = new Array(); };
				mycache_c.calendars[calendar_id]=calendar_arr;/*!!!*/
				if ($('clndform_id').value == 0){
					render_show_calendar(calendar_arr);
					renderContainerForEvent(calendar_id, calendar_color);
					cmScroll_resize ();
					//SetMainDivHeight();
				}else{
					var CalText = $('calendar_'+calendar_id+'_text');
					CalText.innerHTML = HtmlEncode(CalendarSubject.value);
					CalText.title = CalendarSubject.value;
					changeColorContainer(calendar_id, calendar_color);
				}
			}
		}
	};
	$('manager_window').style.display = 'none';
	HideInfo();
}

function manager_form_cancel()
{
	$('manager_window').style.display='none'; 
}

function getClockTime()
{
	var hours, hours_till, minutes_till;
	var result = new Array();
	var time = new Date();
	var time_till = new Date();
	var minutes = time.getMinutes();

	time_till.setHours(time_till.getHours() + 1);

	if(minutes > 30){
		minutes = ':00';
		time.setHours(time.getHours() + 1);
	 	minutes_till = ':30';
	}else{
		minutes = ':30';
		minutes_till = ':00';
	};
	hours = time.getHours();
	hours_till = time_till.getHours();
	hours_till = (hours_till==0 && hours!=0)?24:hours_till;

	result[0] = fnum(hours,2) + minutes;
	result[1] = fnum(hours_till,2) + minutes_till;

	if(setcache['timeformat'] == 1){
		for (var i=0; i<timeFormat2.length; i++) {
			if (result[0] == timeFormat2[i].Value) {
				result[0] = timeFormat1[i].Value;
			}
			if (result[1] == timeFormat2[i].Value) {
				if (i==0) continue;
				result[1] = timeFormat1[i].Value; 
			}
		}
	};
	return result;
}

/*
* edit_form
*/
function evform_create(idc) {
	var date = mydate;
	var time = getClockTime();
	CreateTimeTill(time[1],time[0]);
	CreateTimeFrom(time[0]);
 
	tfrom = time[0]; 
	ttill = time[1];  
	
	if (tfrom == "00:00" || tfrom == "12 AM") {
		var curDate = new Date();
		if (curDate.getHours() == 23 && curDate.getMinutes()>30) {
			date = new Date(date.getFullYear(), date.getMonth(), date.getDate()+1);
		}
	};

	var editevent_form_data = {
			subject 	: '',
			timeFrom	: tfrom,
			timeTill	: ttill,
			fullFromDate: date,
			fullTillDate: date,
			description	: '',
			event_id	: 0 //0  if new event
		};
	ShowDiv(editevent_form_data, idc);
}

function evform_submit() {
/*--check data --*/
	var select_calendar = $('calen_sal').value;
	var EventSubject	= $('EventSubject');
	var EventDescription= $('EventDescription');

	if(select_calendar == '') {
		alert(Lang.ErrorCalendarNotCreated);
		return;
	};

	EventSubject.value		= Trim(EventSubject.value);
	EventDescription.value	= Trim(EventDescription.value);
	if(EventSubject.value == ''){
		alert(Lang.WarningSubjectBlank);
		EventSubject.focus();
		return;
	};
	var strTimeFrom = $('EventTimeFrom').value;
	var strTimeTill = $('EventTimeTill').value;
	var strDateFrom = $('EventDateFrom').value;
	var strDateTill = $('EventDateTill').value;

	var time_from = CheckTimeStr(strTimeFrom);
	var time_till = CheckTimeStr(strTimeTill);
	if (time_from == null || time_till == null) {
		alert(Lang.WarningIncorrectTime);
		return;
	} else {
		if (time_from.timeInterval == null) {//timeformat = 1
			if (time_from.hours>24 || (time_from.minutes!="30" &&  time_from.minutes!="00"))	{
				alert(Lang.WarningIncorrectFromTime);
				return;
			}
		} else {//timeformat = 2
			if (time_from.hours>12 || (time_from.minutes!="30" && time_from.minutes!=null))	{
				alert(Lang.WarningIncorrectFromTime);
				return;
			}
		};
		if (time_till.timeInterval == null) {//timeformat = 1
			if (time_till.hours>24 || (time_till.minutes!="30" &&  time_till.minutes!="00"))	{
				alert(Lang.WarningIncorrectTillTime);
				return;
			}
		} else {//timeformat = 2
			if (time_till.hours>12 || (time_till.minutes!="30" && time_till.minutes!=null))	{
				alert(Lang.WarningIncorrectTillTime);
				return;
			}
		}
	};

	var date_from = ConvertFromStrToDate(strDateFrom);
	var date_till = ConvertFromStrToDate(strDateTill);

	if (date_from == null || date_till == null) {
		alert(Lang.WarningIncorrectDate);
		return;
	} else {
		if(date_from.getTime() > date_till.getTime()) {
			alert(Lang.WarningStartEndDate);
			return;
		} else {
			var timeFromId = null, timeTillId = null, timeFromI, timeTillI;

			if (setcache['timeformat'] == 2) {
				var timeOptions = timeFormat2; //defined in _time_selector.js
				var time_f = time_from.hours+":"+time_from.minutes;
				var time_t = time_till.hours+":"+time_till.minutes;
			} else {//setcache['timeformat'] == 1
				var timeOptions = timeFormat1; //defined in _time_selector.js
				var time_f = time_from.hours+((time_from.minutes==null)?'':(':'+time_from.minutes))+" "+time_from.timeInterval;
				var time_t = time_till.hours+((time_till.minutes==null)?'':(':'+time_till.minutes))+" "+time_till.timeInterval;
			};

			//check fromtime field
			for (var i=0; i<timeOptions.length; i++) {
				if (time_f == timeOptions[i].Value) {
					timeFromId = timeOptions[i].Id;
					timeFromI = i;
					break;
				}
			};

			var begin = (setcache['timeformat'] == 1 && (date_from.getTime() == date_till.getTime()))?1:0;
			//check fromtime field
			for (var i=begin; i<timeOptions.length; i++) {
				if (time_t == timeOptions[i].Value) {
					timeTillId = timeOptions[i].Id;
					timeTillI = i;
					break;
				}
			};
			if (((timeFromId == null) || (timeTillId == null) || timeFromId>=timeTillId) && (date_from.getTime() == date_till.getTime())) {
				alert(Lang.WarningStartEndTime);
				return;
			}
		}
	};

	$('edit_window').style.display='none';

/*--save data--*/
	ShowInfo(Lang.InfoSaving);
	var subject		= encodeURIComponent(EventSubject.value.substr(0,50));
	var description = encodeURIComponent(EventDescription.value.substr(0,255));
	var select_calendar = $('id_calendar').value;

	time_from = timeFormat2[timeFromI].Value;
	time_till = timeFormat2[timeTillI].Value;

	var date_f = to8(date_from);
	var date_t = to8(date_till);

	if (time_till == "24:00") {
		time_till = "00:00";
		date_t = to8(new Date(date_till.getFullYear(), date_till.getMonth(), (date_till.getDate()+1)));
	};

	if ((date_from.getTime()<date_till.getTime()) && time_till != '00:00'){/*!!!*/
		var all_day = 1;
	}else{
		var all_day = 0;
	};

	var id_event = parseInt($('evform_id').value);
	var divCheckPanel = $("divCheckPanel"); 
	if (divCheckPanel != undefined) divCheckPanel.style.display = "none";

	var req = getXMLHTTPRequest();
	var res = '';
	var url = processing_url+'?action=update_event&event_id=' + id_event + '&name=' + subject + '&text=' + description + '&from=' + date_f + '&till=' + date_t + '&time_from=' + time_from + '&time_till=' + time_till+'&allday='+all_day+'&nocache=' + Math.random();
	if (id_event == 0) {
		url += '&calendar_id=' + select_calendar;
	} else {
		var old_calendar_id = mycache.ids[id_event]['calendar_id'];
		if (old_calendar_id != select_calendar) {
			url += '&new_calendar_id='+select_calendar + '&calendar_id=' + old_calendar_id;
		} else {
			url += '&calendar_id=' + select_calendar;
		}
		var old_allday_value = mycache.ids[id_event]['event_allday'];
	};
	
	if (req) {
		try {
			req.open("GET",url,false);
			req.send(null);
			res = req.responseText;
		} catch(e) { }
		var event = new Array(); 
		var result = res.parseJSON();

		if (!servErr(result,Lang.ErrorUpdateEvent)) {
			for(var i in result)
			{
				var eventvalue = result[i];
				if (typeof (eventvalue) == 'function') continue;
				event[i] = eventvalue;
			};
			var eid = event['event_id'];

			if (id_event==0) evlink={'event_timefrom':0,'event_timetill':null,'event_name':null,'event_text':null};
			if ((evlink['event_timefrom']!=event['event_timefrom'])||(evlink['event_timetill']!=event['event_timetill'])||(evlink['event_text']!=event['event_text'])||(evlink['calendar_id']!=event['calendar_id']))
			{
				if(id_event != 0)
				{
					delevent(event); 
					dff=mycache.ids[eid]['event_timefrom'];
					date_ff=to8(dt2date(dff));
					if ((date_ff>=showLimits.weekFrom)&&(date_ff<=showLimits.weekTill)){dotrender(mycache.w,mycache.days,mycache.ids,date_ff,1,7);}
					if (date_ff==showLimits.day) { dotrender(mycache.d,mycache.days,mycache.ids,date_ff,0,1); }
				};
				addevent(event);
				if ((date_f>=showLimits.weekFrom)&&(date_f<=showLimits.weekTill)) { dotrender(mycache.w,mycache.days,mycache.ids,date_f,1,7); }
				if (date_f==showLimits.day) { dotrender(mycache.d,mycache.days,mycache.ids,date_f,0,1); }
				if (area.id == 'grid_2_month') ReRenderMonthly();
			} else if (evlink['event_name']!=event['event_name']) {
				var ename=event['event_name']; 
				var edate=to8(dt2date(event['event_timefrom']));
				if ((mycache.d[eid]!=undefined)&&(mycache.d[eid])) { mycache.d[eid]['event_name']=ename; set_title(eid,ename,0); }
				if ((mycache.w[eid]!=undefined)&&(mycache.w[eid])) { mycache.w[eid]['event_name']=ename; set_title(eid,ename,1); }
				if ((mycache.m[eid]!=undefined)&&(mycache.m[eid])) { mycache.m[eid]['event_name']=ename; set_title(eid,ename,2); } 
				for (var mii in mycache.days[edate]) { 
					if (mycache.days[edate][mii]['event_id']==eid) mycache.days[edate][mii]['event_name']=ename; 
				};
				mycache.ids[eid]['event_name']=ename;
			};
			if ((id_event!=0 && old_allday_value == 1) || event['event_allday'] == 1) setWorkAreaOffset();
		};
	};
	RecalcScrollArrows();
	evform_clear();
	HideInfo();
}

function evform_clear() {
	calendarTableStart.Hide();
	calendarTableEnd.Hide();
	timeSelectorTill.Remove();
	timeSelectorFrom.Remove();
	$('edit_form').style.display='none';
}

function evform_cancel() {
	var divCheckPanel = $("divCheckPanel");
	if(divCheckPanel != undefined) divCheckPanel.style.display = "none";
	evform_clear();
}

function evform_delete() {
	var confirm_window = $('confirm_window');
	confirm_window.style.display='block';
	if (confirm(Lang.ConfirmAreYouSure)) {
		ShowInfo(Lang.InfoDeleting);
		confirm_window.style.display='none';
		$('edit_window').style.display='none';
		var id_event = parseInt($('evform_id').value);
		var etd=to8(dt2date(mycache.ids[id_event]["event_timefrom"]));
		var calendar_id = mycache.ids[id_event]["calendar_id"];

		var req = getXMLHTTPRequest();
		var res = '';
		var url = processing_url+'?action=delete_event&event_id=' + id_event + '&calendar_id=' + calendar_id+'&nocache=' + Math.random();

		if (req)
		{
			try {
				req.open("GET",url,false);
				req.send(null);
				res = req.responseText;
			} catch(e) { }
			var result = res.parseJSON();
			if (!servErr(result,Lang.ErrorDeleteEvent)) { 
				var event_arr = new Array();
				for(var i in result)
				{ 
					if (typeof(result[i]) == 'function') continue;
					event_arr[i] = result[i];
				}
				//if($('evform_id').value != 0){
				delevent(event_arr);
				calendarInManager.RefreshCalendarSelector(mydate.getDate(), mydate.getMonth()+1, mydate.getFullYear());
				ReRenderMonthly();
				if (etd==showLimits.day) dotrender( mycache.d,mycache.days,mycache.ids,etd,0,1);
				if ((etd>=showLimits.weekFrom)&&(etd<=showLimits.weekTill)) dotrender( mycache.w,mycache.days,mycache.ids,etd,1,7);
				//}
				if (mycache.ids[id_event]['event_allday'] == 1) setWorkAreaOffset();
			}
		};
		RecalcScrollArrows();
		evform_clear();
		HideInfo();
	} else {
		confirm_window.style.display='none';
		return;
	}
	var divCheckPanel = $("divCheckPanel");
	if(divCheckPanel != undefined) divCheckPanel.style.display = "none";
}

/*
* end of edit_form
*/
function set_title(eid,tit,typ) { 
	var evnt=mycache.ids[eid]; var ald=evnt['event_allday'];
	var indx=0; do {
	var idd=eid+'_'+indx+'_'+typ+'_'+ald;
	//alert('Stepped: '+idd); 
	var oid='event_'+idd; var tid=$('inline_'+oid);
	if (!$(oid)) { indx=-1; break; }
	// alert('Located: '+oid);

	var txt=$(oid).title;
	if ((tpos=txt.indexOf("]"))<0) var nt=tit;
	else var nt=txt.substr(0,tpos+2)+tit;
	$(oid).title=nt; 


	tid.value=tit;
	tid.parentNode.replaceChild(document.createTextNode(tid.value), tid.parentNode.firstChild);
	setHeightTextarea(tid);
	//getHeightTextarea(oid);

	if (typ==2) { // 'N more...' list
		var nid='event_'+eid+'_'+indx+'_3_'+ald;
		if ($(nid))	ChildMake($(nid),tit);
	}
	indx++; } while (indx>0)
}

function ChildKill(dl) { while (dl.childNodes.length > 0) { dl.removeChild(dl.firstChild); }  }
function ChildMake(nd,tx) {txn=document.createTextNode(tx); dl=$(nd); ChildKill(dl); dl.appendChild(txn); }
function alarm(tx) { ChildMake('dbug',tx); }
function mouse1() { setEventStyle(this.id, 'event_selected');}
function mouse0() { setEventStyle(this.id, 'event');}
function isplit(str) { spl=str.split("_"); return {type:spl[0], id:spl[1], box:spl[2], mode:spl[3], allday:spl[4]} }

function choose(ev,divv) { 
	//textfield = $('inline_'+divv.id);
	var iddata = isplit(divv.id);
	var evlink=mycache.ids[iddata.id];
	
	var tfDateObj = dt2date(evlink["event_timefrom"]);
	var ttDateObj = dt2date(evlink["event_timetill"]);
	var fullFromDate = tfDateObj;
	var fullTillDate = ttDateObj;
	var tf=gtime(fullFromDate); 
	var tt=gtime(fullTillDate,24);

	if(setcache['timeformat'] == 1)
	{
		var chour_f	= tfDateObj.getHours();
		var cmin_f	= tfDateObj.getMinutes();
		var chour_t	= ttDateObj.getHours();
		var cmin_t	= ttDateObj.getMinutes();

		var time_format_from = ((chour_f<12) ? "AM" : "PM");
		var time_format_till = ((chour_t<12) ? "AM" : "PM");

		if (chour_f==0) chour_f	=12;
		if (chour_t==0) chour_t	=12;
		if (chour_f>12) chour_f	-=12;
		if (chour_t>12) chour_t	-=12;
		
		tf.time = ((chour_f == 0)? '12' : chour_f) + ((cmin_f == 0) ? ' ' : ':'+ cmin_f + ' ') + time_format_from;
		tt.time = ((chour_t == 0)? '12' : chour_t) + ((cmin_t == 0) ? ' ' : ':'+ cmin_t + ' ') + time_format_till;
	};

	CreateTimeFrom(tf.time);
	if (fullFromDate.getTime() < fullTillDate.getTime()) {
		CreateTimeTill(tt.time);
	} else {
		CreateTimeTill(tt.time,tf.time);
	};

	var editevent_form_data = {
			subject 	: evlink["event_name"],
			timeFrom	: tf.time,
			timeTill	: tt.time,
			fullFromDate: fullFromDate,
			fullTillDate: fullTillDate,
			description	: evlink["event_text"],
			event_id	: iddata.id
		};
	ShowDiv(editevent_form_data);

	if(ev != null) ev.cancelBubble = true;
}

function cutpx(arg) { var arg=String(arg);
	return ( ((arp=arg.indexOf("px"))>=0) ? arg.substring(0,arp) : arg );
}

function cute(arg,sym) { var arg=String(arg);
	return ( ((arp=arg.indexOf(sym))>=0) ? arg.substring(0,arp) : arg );
}

function fnum(num,digits)
{
 var num=String(num);
 return (('00000000'.substr(0,digits-num.length))+num);
}

function dt2date(strr) { var str=String(strr);
	var zd=new Date(str.substring(0,4),str.substring(5,7)-1,str.substring(8,10),str.substring(11,13),str.substring(14,16),str.substring(17,19));
	return (zd);
}
/*
function date2dt(dt) {
	zd=fnum(dt.getFullYear(),4)+'-'+fnum(1+dt.getMonth(),2)+'-'+fnum(dt.getDate(),2)+' '+fnum(dt.getHours(),2)+':'+fnum(dt.getMinutes(),2)+':'+fnum(dt.getSeconds(),2);
	return (zd);
}*/

function from8(num) {
	var sof=String(num);
	var df8=new Date(sof.substring(0,4),sof.substring(4,6)-1,sof.substring(6,8),0,0,0,0);
	return(df8);
}

function to8(dt) {
	return(Number(fnum(dt.getFullYear(),4)+fnum(1+dt.getMonth(),2)+fnum(dt.getDate(),2)));
}

function range8(dt1,dt2) {
	var d1=from8(dt1);
	var d2=from8(dt2);  
	//r8=Math.ceil((d2.getTime()-d1.getTime())/86400000);
	var r8=Math.round((d2.getTime()-d1.getTime())/86400000);
	return(r8);
}

function move8(dat,ofs) {
	var date00 = from8(dat); 
	/*
	var date01 = new Date(date00.getTime()+(ofs*86400000));
	return(to8(date01));
	*/  
	var date01 = date00;
	date01.setDate(ofs+date00.getDate());
	return(to8(date01));
}

function gtime(dt,hz) {
	if (hz==undefined) var hz=0;
	var chour=dt.getHours(); var cmin=dt.getMinutes(); 
	if ((chour==0)&&(cmin==0)&&(hz==24)) { chour=24; dt.setTime(dt.getTime()-86400000); chour=24; }
	var cyear=dt.getFullYear(); var cmonth=1+dt.getMonth(); var cday=dt.getDate();
	var cdate=fnum(cday,2)+"/"+fnum(cmonth,2)+"/"+String(cyear);
	var climit=String(cyear)+fnum(cmonth,2)+fnum(cday,2);
	//var chour=dt.getHours(); var cmin=dt.getMinutes(); 
	var ctime=fnum(chour,2)+':'+fnum(cmin,2);

	var cfh=chour+(cmin/60); var cdatetime=cdate+' '+ctime;
	var cweek=dt.getDay(); if (cweek==0) cweek=7;
	var nmonth=[, Lang.FullMonthJanuary, Lang.FullMonthFebruary, Lang.FullMonthMarch, Lang.FullMonthApril,
	 Lang.FullMonthMay, Lang.FullMonthJune, Lang.FullMonthJuly, Lang.FullMonthAugust, Lang.FullMonthSeptember,
	 Lang.FullMonthOctober, Lang.FullMonthNovember, Lang.FullMonthDecember][cmonth];
 	var smonth=[, Lang.ShortMonthJanuary, Lang.ShortMonthFebruary, Lang.ShortMonthMarch, Lang.ShortMonthApril,
	 Lang.ShortMonthMay, Lang.ShortMonthJune, Lang.ShortMonthJuly, Lang.ShortMonthAugust, Lang.ShortMonthSeptember,
	 Lang.ShortMonthOctober, Lang.ShortMonthNovember, Lang.ShortMonthDecember][cmonth];

	var utime=ctime; if (setcache['timeformat']==1) utime = ((chour == 0)? '12' : ((chour > 12)? chour -12 : chour)) + ((cmin == 0) ? ' ' : ':'+ fnum(cmin,2) + ' ') + ((chour <12) ? "AM" : "PM");
	var udate=cdate; if (setcache['dateformat']==1) udate=fnum(cday,2)+"/"+fnum(cmonth,2)+"/"+String(cyear); 
	else if (setcache['dateformat']==3) udate=String(cyear)+'-'+fnum(cmonth,2)+"-"+fnum(cday,2); 
	else if (setcache['dateformat']==4) udate=smonth+' '+fnum(cday,2)+', '+String(cyear); 
	else {
	//setcache['dateformat']==5)
		udate=fnum(cday,2)+' '+smonth+' '+String(cyear);
	}

	var nweek=[, Lang.FullDayMonday, Lang.FullDayTuesday, Lang.FullDayWednesday, Lang.FullDayThursday,
	 Lang.FullDayFriday, Lang.FullDaySaturday, Lang.FullDaySunday][cweek];
	var inmonth=gdim(cmonth,cyear);
	var dobj=dt; var v8=to8(dt);
	return {day: cday, month: cmonth, year: cyear, date: cdate, limit: climit, hour: chour, min: cmin, time: ctime, fh: cfh, datetime: cdatetime, week: cweek, nweek: nweek, nmonth: nmonth, smonth: smonth, udate: udate, utime: utime, inmonth: inmonth, dobj: dobj, to8: v8 };
}

function gdim(month,year)
{
	var mnth = ( month<1 ) ? 0 : (( month>12 ) ? 11 : month - 1);
    var arDaysInMonth_Usual = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    var arDaysInMonth_Leap  = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    if ( (year % 4) == 0 && (year % 100) != 0 || (year % 400) == 0 )
      return arDaysInMonth_Leap[mnth];
    else
      return arDaysInMonth_Usual[mnth];
}

function dateBrowse(jd) {
	if(step == 0){ mydate.setMonth(mydate.getMonth() + jd); } else { mydate.setDate(mydate.getDate() + step*jd); };
	load_date();
	setWorkAreaOffset();
	calendarInManager.RefreshCalendarSelector(mydate.getDate(), mydate.getMonth()+1, mydate.getFullYear());
}

function switch2date(dt) {
	mydate = new Date(dt.substr(0,4), (Number(dt.substr(4,2))-1), dt.substr(6,2));
	load_date();
	change(view+1);
}

function load_date() {
	create_div_dynamic(mydate);
	fillEvents();
	if (view == MONTH) ReRenderMonthly();
	HideInfo();
}

function calendars_clear() {
	for (var i in mycache_c.calendars) {
		for (var j=1; j<=5; j++) {
			var evid = 'container_'+mycache_c.calendars[i]['calendar_id']+"_"+j;
			if(mycache_c.calendars[i]['calendar_id'] !=undefined){
				var evdom=$(evid);
				ChildKill(evdom);
			}
		}
	}
}

function ReRenderMonthly() {
	if (view==MONTH) { 
		var gmydate=gtime(mydate); var my8=gmydate.to8;
		var shar=new Array(); var h=new Array(); var shi=0; var ii=0; var jj=0; var hcol=0;
		glims=gridLimits(my8,false);
		msuf="_5"; vd=findDOM("area_2_month",0); vdh=cutpx(vd.offsetHeight); msuf="_5";
		vd=findDOM("area_2_month",0); vdh=cutpx(vd.offsetHeight); ceh=Math.floor(vdh/6-20);
		for (ii in mycache_c.calendars) {
			if(mycache_c.calendars[ii]['calendar_id'] != undefined){
				var calname='container_'+mycache_c.calendars[ii]['calendar_id']+msuf;
				var divcont=findDOM(calname,0); dchild=divcont.childNodes.length;
				for (ij=0; ij<dchild; ij++) {
					divc=divcont.childNodes[ij]; 
					iddata=isplit(divc['id']); divid=iddata.id; divbox=Number(iddata.box);
					evdata=mycache.ids[divid];
					evtime=dt2date(evdata["event_timefrom"]); evgtime=gtime(evtime); evtime=to8(evtime); evdate=evtime;
					allday=Number(evdata["event_allday"]);
					dayz=1;
					if (allday==1) { dayz=Math.round(cute(divc.style.width,"%")/(100/7)); } 
					if (divbox!="0") { evmove=(8-evgtime.week)+7*(Number(divbox)-1); evdate=move8(evtime,evmove); } 
					evtill=move8(evdate,dayz-1);
					shar[shi]={divc:divc, iddata:iddata, divid:divid, divbox:divbox, evdata:evdata, evtime:evtime, evgtime:evgtime, evdate:evdate, allday:allday, dayz:dayz, evtill:evtill}; shi++ ;
				}
			}
		}

		var shal=shar.length;
		for (ii=0; ii<(shal-1); ii++) {
			for (jj=(ii+1); jj<shal; jj++) {
			  var shi=shar[ii]; var shj=shar[jj];
			  if (shi.dayz<shj.dayz) 
				{ shar[jj]=shi; shar[ii]=shj; } 
			  else if ((!shi.allday)&&(!shj.allday)) {
				var shig=shi.evgtime; var shjg=shj.evgtime;
				if ((shig.date==shjg.date)&&(shig.fh>shjg.fh))
				   { shar[jj]=shi; shar[ii]=shj; } 
			  }
			}
		}
		for (ii in visar) { 
			vii=visar[ii]; if (typeof(vii)=='function') continue;
			vdel=$(vii.vistag);
			vpar=vdel.parentNode; vpar.removeChild(vdel);
		}
		visar=new Array(); viz=new Array(); vizidz=new Array();
		for (ii=0; ii<shar.length; ii++) {
			var h=shar[ii];
			hcol=1; var passed=false;
			while (!passed) {
				for (jj=0; jj<ii; jj++) {
					var z=shar[jj];
					if ( (hcol==z.col) && (mcros(h,z)) ) { hcol++; continue; }
				}
				passed=true;
			}
			shar[ii].col=hcol;
			offsetTop=Math.floor((range8(glims.from,h.evdate) )/7);
			offsetTop=Math.round((vdh/6)*offsetTop);
			offsetTop=20+27*(hcol-1)+offsetTop;
			h.divc.style.top=(offsetTop+"px");
			cdisp=(( (27*hcol) > ceh-15 )?"none":"inline");
			h.divc.style.display=cdisp;
			
			for (jj=0; jj<shar[ii].dayz; jj++) {
				daymor=move8(shar[ii].evdate,jj);		
				if (cdisp=='none') {
					if (viz[daymor]==undefined) viz[daymor]=1; else viz[daymor]=viz[daymor]+1;	
					if (vizidz[daymor]==undefined) { bindex=0; vizidz[daymor]=new Array(); }
					else { bindex=vizidz[daymor].length; }
					vizidz[daymor][bindex]=shar[ii].divid;
				}
			}
		}
		for (ii in viz) { 
			visits=viz[ii]; if (typeof(visits)=='function') continue;
			vrange=range8(glims.from,ii);
			visrow=Math.floor(vrange/7); viscol=vrange%7;
			
			vislef=(3+(100/7)*viscol)+"%"; vistop=((visrow+1)*(vdh/6)-16)+"px";
			vistag="more_"+ii; vistxt=visits+" more...";
			vv=document.createElement("DIV");
			vt=document.createTextNode(vistxt); vv.appendChild(vt);
			vv.style.position='absolute';
			vv.style.fontFamily='Tahoma,Arial,Helvetica,sans-serif'; vv.style.fontSize='11px'; vv.style.color='#797979'; 
			
			vv.style.top=vistop; vv.style.left=vislef;
			vv.style.cursor='pointer'; vv.id=vistag;
			vv.style.zIndex='15';//23000

			vv.onclick=showfetch; vd.appendChild(vv);
			visar[ii]={visits:visits,visrow:visrow,viscol:viscol,vislef:vislef,vistop:vistop,vistag:vistag,visdom:vd};
		}
	}
	hidefetch();
}

function mcros(h,z) {
	var A1=h.evdate; var A2=h.evtill; var B1=z.evdate; var B2=z.evtill;
	var ad1=h.allday; var ad2=z.allday;
	if (ad1+ad2==2) return ( ((A1>=B1)&&(A1<=B2)) || ((A2>=B1)&&(A2<=B2)) || ((B1>=A1)&&(B1<=A2)) || ((B2>=A1)&&(B2<=A2)) ); //return ( ((A1>=B1)&&(A2<=B2)) || ((B1>=A1)&&(B2<=A2)) || ((B1>A1)&&(B1<A2)) || ((B2>A1)&&(B2<A2)) || ((A1>B1)&&(A1<B2)) || ((A2>B1)&&(A2<B2)) );
	if (ad1+ad2==0) return (A1==B1);
	if (ad1==1) return ((B1>=A1)&&(B1<=A2));
	if (ad2==1) return ((A1>=B1)&&(A1<=B2));
}

function showfetch(e) {
	if ($('mfet')) { hidefetch(); return; }
	var thisid=this.id; var iddat=thisid.split("_"); var dateid=Number(iddat[1]); var mdata=vizidz[dateid]; var mlen=mdata.length;
	var morx=Number(cutpx(this.offsetLeft)); var mory=Number(cutpx(this.offsetTop)); var morw=Number(cutpx(this.offsetWidth));
	var vd=findDOM("area_2_month",0); 
	var vdh=cutpx(vd.offsetHeight); var ceh=Math.floor(vdh/6-20);
	var vdw=cutpx(vd.offsetWidth); var cew=Math.floor(vdw/7);

	hidefetch();
	var mf=document.createElement("DIV");

	showbox=boxcalc(3,1);
	vrange=range8(showLimits.monthFrom,dateid);
	showwid=1.5*cew; showhei=mlen*13+1; 

	showmax=3*Math.floor(vdh/6);
	if (showhei>showmax) { showhei=showmax; mf.style.overflowY='scroll'; }
	showtop=showbox.top+mory+2; showleft=showbox.left+morx+morw+2;
	fullvdh=Number(vdh)+Number(showbox.top); showover=Number(showtop)+Number(showhei)-fullvdh; if (showover>0) showtop-=showover;
	mf.style.top=showtop+"px"; mf.style.left=showleft+"px";
	mf.style.width=showwid+"px"; mf.style.height=showhei+"px";  
	mf.id='mfet'; mf.style.display="block";
	mf.className = 'event_select_box';
	var stx="";
	for (var i in mdata) {
		var cid=mdata[i];
		if (typeof(cid)!='function') {
			evlink=mycache.ids[cid]; ename=evlink['event_name'];
			stx+="\n"+ename; 
			mfd=document.createElement('SPAN');
			mfd.className = 'event_element_select_box';
			mfd.style.color = getNumberOfColor(mycache_c.calendars[evlink['calendar_id']]['calendar_color']);
			mfd.onmouseover=function() { this.style.textDecoration='underline'; };
			mfd.onmouseout =function() { this.style.textDecoration='none'; };

			mfd.unselectable="on";
			mfd.id="event_"+evlink["event_id"]+"_0_3_"+evlink["event_allday"];

			mtf=gtime(dt2date(evlink["event_timefrom"]));
			mtt=gtime(dt2date(evlink["event_timetill"]),24);
			if (evlink['event_allday']==0) mtitl=mtf.utime+' - '+mtt.utime+', '+mtf.udate;
			else { 
				mtr=range8(mtf.to8,mtt.to8)+1; 
				if (mtr==1) mtitl=mtf.udate; else mtitl=mtf.udate+' - '+mtt.udate+' ('+mtr+' days)';
			}
			mfd.title=mtitl; 
			msp=document.createElement("SPAN");
			mfd.appendChild(document.createTextNode(ename));

			mfd.style.MozUserSelect='none'; mfd.style.KhtmlUserSelect='mfd.style.'; mfd.style.userSelect='none';
			mfd.onmousedown=function(ev) { drag2start(ev,this); return false; };
			mf.appendChild(mfd); mf.appendChild(document.createElement("BR"));
		}
	}
	mbodyZ=document.getElementsByTagName("body");
	document.body.appendChild(mf);
	shownfetch=2;
}

function hidefetch(e) {
	if (shownfetch==2) { shownfetch=1; return; };
	if (shownfetch==0) return;
	vd=findDOM("area_2_month",0); vdh=cutpx(vd.offsetHeight); ceh=Math.floor(vdh/6-20);  
	var mel=$('mfet');
	if (mel!=null) document.body.removeChild(mel);
	shownfetch=0;
//  mbodyZ=document.getElementsByTagName("body"); mbody=mbodyZ[0]; eventOff('select',eventskip); eventOff('selectstart',eventskip); 
}

function drag2start(ev,obj) { indrag=true;
//  obj = $(evobj.id.substr(0,evobj.id.length - 4));
	evobj=obj; idd=evobj.id;
	modez=isplit(idd); vMid=Number(modez.id); //vMmd=Number(modez.mode)+1; vMad=modez.allday; 
	evlink=mycache.ids[vMid]; myname=evlink['event_name'];
	mybox=boxcalc(3,0); stc=coordss(ev,evobj,3,0,mybox,0);
	stx=stc.x; sty=stc.y; dragon=0;
	eventOn("mousemove",drag2move,true);
	eventOn("mouseup",drag2done,true);
	document.onselectstart = function() {return false;};
	document.onselect = function() {return false;};
}

function drag2move(ev) {
	mybox=boxcalc(3,0); mypos=coordss(ev,evobj,3,0,mybox,0);
	drag_w=Math.floor(mybox.width/7); drag_h=27; zone_w=mybox.width-drag_w; zone_h=mybox.height-drag_h;
	myx=mypos.x; if (myx<0) myx=0; if (myx>zone_w) myx=zone_w;
	myy=mypos.y; if (myy<0) myy=0; if (myy>zone_h) myy=zone_h; 
	if ((dragon==0)&&((Math.abs(myx-stx)>5)||(Math.abs(myy-sty)>5))) {
	hidefetch(); drag2show(); dragon=1; 
	} else if (dragon==1) { drag2show(); }
}

function drag2done() {
	eventOff("mouseup",drag2done,true);
	eventOff("mousemove",drag2move,true);
	document.onselectstart = function() {};
	document.onselect = function() {};
	if (dragon==0) {
		hidefetch(); 
		choose(null,evobj); 
	} else {
		ShowInfo(Lang.InfoSaving);
		drag2hide();
		enc=mypos;/*is global???*/

		var calendar_id = mycache.ids[vMid]["calendar_id"];
		var req = getXMLHTTPRequest();
		var res = '';

		var prevTillDate = dt2date(mycache.ids[vMid]["event_timetill"]);
		var time_till = fnum(prevTillDate.getHours(),2)+":"+fnum(prevTillDate.getMinutes(),2);
		var prevFromDate = dt2date(mycache.ids[vMid]["event_timefrom"]);
		var time_from = fnum(prevFromDate.getHours(),2)+":"+fnum(prevFromDate.getMinutes(),2);

		var newFromDate = from8(enc.d);//date format
		var diff = newFromDate.getTime() - prevFromDate.getTime();
		var newTillDate = new Date(diff + prevTillDate.getTime());//date format

		var url = processing_url+'?action=update_event&event_id='+vMid+'&calendar_id='+calendar_id+'&from='+escape(enc.d)+'&time_from='+escape(time_from)+'&till='+escape(to8(newTillDate))+'&time_till='+escape(time_till)+'&nocache=' + Math.random();
		if (req) {
			try {
				req.open("GET",url,false);
				req.send(null);
				res = req.responseText;
			} catch(e) { }

			var result = res.parseJSON();
			var dval = new Array();
			if (servErr(result,Lang.ErrorUpdateEvent)) {
				var dval = mycache.ids[pickobj.id];
			} else {
				for (var i in result) { if (typeof(result[i])!="function") dval[i]=result[i]; }
			};
			delevent(dval);
			addevent(dval,1);
			var wdd=0;
			if ((stc.d>=showLimits.weekFrom)&&(stc.d<=showLimits.weekTill)) {
				dotrender( mycache.w,mycache.days,mycache.ids,stc.d,1,7,wdd); wdd++;
			};
			if ((enc.d!=stc.d)&&(enc.d>=showLimits.weekFrom)&&(enc.d<=showLimits.weekTill))
				dotrender( mycache.w,mycache.days,mycache.ids,enc.d,1,7,wdd);
			var ddd=0;
			var gmydate=gtime(mydate);
			var my8=Number(gmydate.to8);
			var dt1=Number(enc.d);
			var dt0=Number(stc.d);
			if (my8==dt1) { dotrender( mycache.d,mycache.days,mycache.ids,dt1,0,1,ddd); ddd++; }
			if ((my8==dt0)&&(dt1!=dt0)) { dotrender( mycache.d,mycache.days,mycache.ids,dt0,0,1,ddd); }
			ReRenderMonthly();
		};
		HideInfo();
	}
	indrag=false;
}

function drag2show() {
if (dragon==0) {
  drager=document.createElement("DIV");
  drager['id']='drager';
  drager.style.display="none";
  drager.style.position="absolute";
  drager.style.borderWidth="3px";
  drager.style.borderColor="#CCCCCC";
  drager.className="eventcontainer_"+mycache_c.calendars[evlink['calendar_id']]['calendar_color'];


    div1=document.createElement("DIV"); div1.className="event";
    div1.style.width=(mybox.width/7)+"px";
    div2=document.createElement("DIV"); div2.className="a";
    div3=document.createElement("DIV"); div3.className="b";
      div1.appendChild(div2); div1.appendChild(div3);
    div4=document.createElement("DIV"); div4.className="event_middle"; div4.style.height='3.8ex';// div4.style.height="3.8ex";
    div5=document.createElement("DIV"); div5.className="event_text"; 
    div6=document.createElement("DIV"); div6.className="time"; 
    
    tf=gtime(dt2date(evlink["event_timefrom"]));
    tt=gtime(dt2date(evlink["event_timetill"]),24);
	if (evlink['event_allday']==0) titl=tf.utime+' - '+tt.utime+', '+tf.udate;
	else { 
	  tr=range8(tf.to8,tt.to8)+1; 
	  if (tr==1) titl=tf.udate; else titl=tf.udate+' - '+tt.udate+' ('+tr+' days)';
	}
    	
    tex1=document.createTextNode(titl);
    div6.appendChild(tex1); 
    
    div7=document.createElement("DIV"); div7.className="text";
      tex2=document.createTextNode(evlink["event_name"]);
    div7.appendChild(tex2);
      div5.appendChild(div6); div5.appendChild(div7); 
      div4.appendChild(div5); div1.appendChild(div4); 
    div8=document.createElement("DIV"); div8.className="c"; div1.appendChild(div8); 
    div9=document.createElement("DIV"); div9.className="a"; div1.appendChild(div9);
  drager.appendChild(div1);

  
 document.body.appendChild(drager);
 drager.style.zIndex=65535;

  dragit=document.createElement("DIV");
  dragit.style.position="absolute";
  dragit.className="dragit_area";
  dragit.style.height=(Math.floor(mybox.height/6)-1)+'px';
  dragit.style.width=(Math.floor(mybox.width/7)-1)+"px";
  dragit.style.zIndex=65500;
  dragit.style.display='none';
  dragit['id']='dragit';
  mybox.dom.appendChild(dragit);
}
  
  leftpos=myx+mybox.left;
  toppos=myy+mybox.top;
  
  drager.style.left=leftpos+"px";
  drager.style.top=toppos+"px";

  dragit.style.left=1+mypos.xcol+"px";
  dragit.style.top=1+mypos.xrow+"px";
  
  if (dragon==0) { drager.style.display="inline";  dragit.style.display="block"; }
}

function drag2hide() {
	document.body.removeChild(drager); mybox.dom.removeChild(dragit);
}

function gridLimits(dt8,glarge) {
	g8=gtime(from8(dt8)); 
	kt8=move8(dt8,-((g8.day)-1));
	kt=gtime(from8(kt8));
	kw=kt.week;
	km=kt.inmonth;
	lnums=1;
	for (var i=1; i<=km; i++) {
		kw++; 
		if (kw>7) { kw=1; if (i<km) lnums++; }
	};
	if (glarge) { 
		froff=-((6-lnums)*7+kt.week-1);
	} else {
		froff=-((( (lnums<6) && (kw!=1) )?7:0)+kt.week-1);
	};
	if (setcache['weekstartson'] == 0) {
		from0=move8(kt8,froff-1);
		till0=move8(kt8,froff+(glarge?48:41)-1);
	} else {//setcache['weekstartson'] == 1
		from0=move8(kt8,froff);
		till0=move8(kt8,froff+(glarge?48:41));
	};
	var m1=move8(dt8,-g8.day+1); var m2=move8(m1,g8.inmonth-1);
	var gm1=gtime(from8(m1)); var gm2=gtime(from8(m2));
	var li1=move8(m1,1-gm1.week-(1-setcache['weekstartson'])); var li2=move8(m2,7-gm2.week-(1-setcache['weekstartson']));

	miniLimits.monthFrom=li1;
	miniLimits.monthTill=li2;
	return { from: from0, till: till0 };
}

function getSettingsParametr()
{
	ShowInfo(Lang.InfoLoading);
	var scache = new Array();
	var res = '';
	var req = getXMLHTTPRequest();
	var url = processing_url + '?action=get_settings&nocache=' + Math.random();

	if (req)
	{
		try {
			req.open('GET', url, false);
			req.send(null);
			res = req.responseText;
		} catch(e) { }
		var setparams = res.parseJSON();
		if (!servErr(setparams,Lang.ErrorGeneral)) { 
			for(var i in setparams)
			{
				if (typeof(setparams[i]) == 'function') continue;
				scache[i] = setparams[i];
			}
		}
	};
	HideInfo();

	return scache;
}

function cacheLoadCalendar()
{
	ShowInfo(Lang.InfoLoading);
	var col = new Array();
	var dcache_c = new Array();
	var res = '';
	var req = getXMLHTTPRequest();
	var url = processing_url+'?action=get_calendars&nocache=' + Math.random();
	if (req)
	{
		try {
			req.open('GET', url, false);
			req.send(null);
			res = req.responseText;
		} catch(e) { }
		var dcalendar = res.parseJSON();
		if (!servErr(dcalendar,Lang.ErrorLoadCalendar)) { 
			for (var i in  dcalendar) {
				dval = dcalendar[i];
				if (typeof(dval)=="function") continue;
				var dcid = dval["calendar_id"];
				col[dval["calendar_color"]] = dval["calendar_color"];
				dcache_c[dcid] = dcalendar[i];
			};
		};
	};
	HideInfo();

	return {calendars:dcache_c, clr: col };
}

function cacheLoad(dt,large){
	gdt=gtime(dt);
	md8=gdt.to8;
	glim=gridLimits(md8,1);
	from0=glim.from;
	till0=glim.till;
	var cBk = {days:null, ids:null, d:null, w:null, m:null, c:null};

	ShowInfo(Lang.InfoLoading);
	var res = '';
	var req = getXMLHTTPRequest();
	var url = processing_url+'?action=get_events&from='+from0+'&till='+till0+'&nocache=' + Math.random();
	if (req)
	{
		if ( (from0!=cSt) || (till0!=cEn) ) {
			try {			
				req.open("GET",url,false);
				req.send(null);
				res = req.responseText;
			} catch(e) { }
			var devent = res.parseJSON();
			var dcache = new Array();
			if (!servErr(devent,Lang.ErrorLoadEvents)) { 
				for (var i in devent) {
					var dval=devent[i];
					if (typeof(dval)=="function") continue;
					if(mycache_c != undefined && mycache_c.calendars[dval["calendar_id"]] != undefined){ 
						dcdate=to8(dt2date(dval["event_timefrom"]));
						dctill=to8(dt2date(dval["event_timetill"]));
						gdc=gtime(dt2date(dval["event_timetill"]));
						if (gdc.fh==0) dctill=move8(dctill,-1);
						
						//if (dcdate<from0) dcdate=from0;
						//if (dctill>till0) dctill=till0;
						do {
							zpi=0;
							if (dcache[dcdate]!=undefined) {
								zpi=dcache[dcdate].length;
							} else {
								dcache[dcdate] = new Array();
							};
							dcache[dcdate][zpi]=dval; 
							dcdate=move8(dcdate,1);
						} while (dcdate<=dctill)
					}
				};
				cSt=from0;
				cEn=till0;
				cBk = {days:dcache, ids:devent, d:null, w:null, m:null, c:null};
			}
		} else {
			if (mycache != null) cBk=mycache;
		}
	}
	HideInfo();
	return(cBk);
}

function LoadAllYearEvents(todaydate) {
	ShowInfo(Lang.InfoLoading);
	var days = new Array();
	var date = fnum(todaydate.getFullYear(),4)+fnum((todaydate.getMonth()+1),2)+fnum(todaydate.getDate(),2);
	var res = '';
	var req = getXMLHTTPRequest();
	var url = processing_url+'?action=get_year_events&date='+date+'&nocache=' + Math.random();
	if (req)
	{
		try {
			req.open("GET",url,false);
			req.send(null);
			res = req.responseText;
		} catch(e) { }
		var parsed_data = res.parseJSON();
		if (!servErr(parsed_data,Lang.ErrorLoadEvents)) { 
			for (var i in parsed_data) {
				if (typeof(parsed_data[i])=="function" || parsed_data[i].search(/JSON/i)>-1) 
				{
					continue;
				} else {
					days["'"+parsed_data[i]+"'"] = 1;
				}
			}
		}
	};
	HideInfo();
	return days;
}

function mark_curdate_week() {

	var weekday_mydate = mydate.getDay();
	var todaydate = new Date();
	var weekday_todaydate = todaydate.getDay();

	var mydate1 = new Date(mydate.getTime() + (weekday_todaydate-weekday_mydate)*86400000);
	var weekday_mydate1 = mydate1.getDay();

	if (setcache['weekstartson'] == 0) {
		var wd = weekday_mydate1;
	} else { //setcache['weekstartson'] == 1
		var wd = (weekday_mydate1==0)?7:weekday_mydate1-1;
	};;

	var cd1=findDOM("current_day_1",1);
	var cd2=findDOM("current_day_2",1);

	cd1.left=((100/7)*wd)+"%"; cd1.display='block'; cd1.zIndex=2; 
	cd2.left=((100/7)*wd)+"%"; cd2.display='block'; cd2.zIndex=2; 

	if (to8(mydate1) == to8(todaydate)) {
		$("current_day_1").className = "current_day";
		$("current_day_2").className = "current_day";
	} else {
		$("current_day_1").className = "current_day_nocolor";
		$("current_day_2").className = "current_day_nocolor";
	}
}

function eventMouseDown(e)
{
	elem_id = this['id'].substr(0,this['id'].length - 2);
	var last_elem = elem_id.substring(elem_id.length - 1);
	if(last_elem == 0){
		area.resizing = true;
		elem_id = this['id'].substr(0,this['id'].length - 2);
		var event_id= $(elem_id);
		ev_mes = event_id;
		mid_div4 = $(elem_id+'_mid');
		div4.initialHeight = mid_div4.clientHeight;
		div4.initialHeight = Math.round(div4.initialHeight / 27) * 27;
		time_ev = $('time_'+elem_id);
		globalTimeTill=null;
	}
}

function resize_event(coor_event, y_start)
{
	var t_event, time_from, time_till, time_unit;

	var height = Math.ceil((coor_event - y_start)/ 27)* 27 + eval(div4.initialHeight);
	if(height < 27){
		height = 27;      
	};

	if(iScrollTop){
		t_event = getHorisontalColumn(Math.round((height - eval(div4.initialHeight) + y_start + eval(iScrollTop + (typeof windowScrollTop != "undefined" ? windowScrollTop : 0)))/27)*27);
	}else{
		t_event = getHorisontalColumn(Math.round((height - eval(div4.initialHeight) + y_start)/27)*27);
	};
	mid_div4.style.height = height - 3 + "px";

	time_till = getTimeEvent(t_event.Number - 1);
	globalTimeTill = time_till;
	if(setcache['timeformat'] == 1) {
		eid = elem_id.substring(6,elem_id.length-6);//is global ???
		eold = mycache.ids[eid];//is global ???
		var tfDateObj = dt2date(eold["event_timefrom"]);
		var chour_f = tfDateObj.getHours();
		var cmin_f = tfDateObj.getMinutes();
		time_from = ((chour_f  == 0)? '12' : ((chour_f>12)?(chour_f-12):chour_f)) + ((cmin_f == 0) ? ' ' : ':'+ cmin_f + ' ') + ((chour_f <12) ? "AM" : "PM");
		time_unit = document.createTextNode(time_from + ' - '+ time_till);
	} else { //setcache['timeformat'] == 2
		time_from = time_ev.innerHTML.substr(0,time_ev.innerHTML.length - 5);
		time_unit = document.createTextNode(time_from + time_till);
	};

	ChildKill(time_ev);
	time_ev.appendChild(time_unit);
}
function setHeightTextarea(obj) {
	obj.style.height = cute(obj.parentNode.parentNode.parentNode.parentNode.style.height, 'ex') - 1.9 + 'ex';
}
/** inline reducting */

function saveOnEnter(ev)
{
	var ev = (ev) ? ev : window.event;
	if(ev.keyCode == 13) manager_form_save();
}

function TextareaKeyDownHandler(ev, obj, count)
{
	ev = ev ? ev : window.event;
	var key = -1;
	if (window.event)
		  key = window.event.keyCode;
	else if (ev)
		  key = ev.which;
	if (key == 13) {
		if (Trim(obj.value) != "") {
			saveEventTitle(obj);
			obj.blur();
			return false; }
		else { obj.value=""; return false; }
	};
	if (key != 8 //delete
		&& key != 46 //backspace
		&& key != 16 //shift
		&& key != 17 //ctrl
		&& key != 18 //alt
		&& key != 35 //end
		&& key != 36 //home
		&& key != 37 //to the right
		&& key != 38 //up
		&& key != 39 //to the left
		&& key != 40) { //down
			if (!ev.ctrlKey && !ev.shiftKey) {
				if (obj.value.length >= count)
					return false;
			}
		};
	obj.parentNode.replaceChild(document.createTextNode(obj.value + 'w'), obj.parentNode.firstChild);
	return true;
}
function TextareaKeyUpHandler(ev, obj) {
	//obj.parentNode.replaceChild(document.createTextNode(obj.value), obj.parentNode.firstChild);
}
function saveEventTitle(textar)
{
// saving title for event
	ShowInfo(Lang.InfoSaving);
	var req = getXMLHTTPRequest();
	var res = '';
	var text = Trim(textar.value);
	var id_event = textar.id.substring(13, textar.id.length - 6);
	var calendar_id = mycache.ids[id_event]["calendar_id"];
	var url = processing_url+'?action=update_event&event_id='+id_event + '&calendar_id='+calendar_id+'&name='+encodeURIComponent(text.substr(0,50))+'&nocache=' + Math.random();
	if (req) {
		try {
			req.open("GET",url,false);
			req.send(null);
			res = req.responseText;
		} catch(e) { }
		var result = res.parseJSON();
		var event_value = new Array();
		if (servErr(result,Lang.ErrorUpdateEvent)) {
			event_value = mycache.ids[pickobj.id];
		} else {
			for (var i in result) {
				if (typeof(event_value[i])!="function") event_value[i]=result[i];
			};
			id_event = event_value['event_id'];
			var tx = event_value['event_name'];
			if ((mycache.d[id_event]!=undefined)&&(mycache.d[id_event])) {
				var edata=mycache.ids[id_event];
				var ln = mycache.d[id_event].length;
				for (var i=0; i<ln; i++) {
					var mixid="event_"+edata["event_id"]+"_"+i+"_0_"+edata["event_allday"];
					var ntd=$(mixid);
					//mycache.d=delarr(mycache.d,id_event,true);
				}
			};
			if ((mycache.w[id_event]!=undefined)&&(mycache.w[id_event])) {
				var edata=mycache.ids[id_event];
				var ln=mycache.w[id_event].length;
			};

			set_title(id_event,tx,0);
			set_title(id_event,tx,1);
			set_title(id_event,tx,2); 

			var dcdate=to8(dt2date(event_value["event_timefrom"]));
			var dctill=to8(dt2date(event_value["event_timetill"]));

			if (mycache.days[dcdate]!=undefined) {
				var zpi=-1;
				for (var mid in mycache.days[dcdate]) {
					if (mycache.days[dcdate][mid]['event_id']==id_event)
					  { zpi=mid; continue; }
				}
				if (zpi<0) zpi=mycache.days[dcdate].length;
			} else {
				var zpi=0;
				mycache.days[dcdate] = new Array(); 
			};
			
			mycache.days[dcdate][zpi]=event_value;
			
			mycache.ids[id_event]=event_value;
	//alert('tx: #'+tx+'#');
			}
	};
	HideInfo();
}

focusLock = false;
function editEventTitle(textar, span)
{
	if(navigator.userAgent.indexOf("MSIE") < 0)
	{
		focusLock = true;
		textar.focus();
		focusLock = false;
	}else{
		focusLock = true;
		textar.focus();
		//textar.select();
		focusLock = false;
	}
}

function fieldBlur(textar)
{
	var id_event = textar.id.substring(13, textar.id.length - 6);
	eold=mycache.ids[id_event];
	textar.value = eold["event_name"];
	textar.parentNode.replaceChild(document.createTextNode(textar.value), textar.parentNode.firstChild);
}

function inline(even,obj)
{
	var textar = $('inline_' + obj.id);
	var span = $('evspan_' + obj.id);
	editEventTitle(textar, span);
}
/*end inline reducting*/

/*arrow for day and week*/
function scrollArrowDown(maxEventPos1)
{
	if(view == WEEK) var par = 'week';
	if(view == DAY) var par = 'day';
	var arrow = $('area_2_' + par);
	arrow.scrollTop = maxEventPos1 + 24 - arrow.offsetHeight;
}

function scrollArrowUp(minEventPos1)
{
	if(view == WEEK) var par = 'week';
	if(view == DAY) var par = 'day';
	var arrow = $('area_2_' + par);
	arrow.scrollTop = minEventPos1 - 24;
}

function setEventStyle(idStr, classStr) {
	var parts = idStr.split("_");
	var i = 0;
	while(typeof(i) != 'undefined') {
		var id = parts[0]+"_"+parts[1]+"_"+i+"_"+parts[3]+"_"+parts[4];
		var obj = $(id);
		if (obj != null) obj.className = classStr;
		else {break;}
		i++;
	}
}


function render_event(evdata,evlink,evmode,invis) {
	if (invis==undefined) invis=false;
	var eventsChanged = true;
//var bazdom=findDOM(evdata[0].dom);
	var keepon=true;
	var jj=0;
	do {
		if (tdl=$("event_"+evlink["event_id"]+"_"+jj+"_"+evmode+"_0")) { 
			var pNod=tdl.parentNode;
			pNod.removeChild(tdl); 
			jj++;
		} else if (tdl=$("event_"+evlink["event_id"]+"_"+jj+"_"+evmode+"_1")) {
			var pNod=tdl.parentNode;
			pNod.removeChild(tdl); 
			jj++;
		} else keepon=false;
	} while (keepon);

	try {
		for (j=0; j<evdata.length; j++) {
		if ((evdata[j]!=null)&&(evdata[j]!=undefined)) {
			mixid="event_"+evlink["event_id"]+"_"+j+"_"+evmode+"_"+evlink["event_allday"];
			evitem=evdata[j];
			if (evitem==null) { 
				var sdd=document.createElement('DIV'); sdd.id=mixid; $('allspan').appendChild(sdd);
			} else {

			var visib="visible";
			if (evmode==2) visib="hidden";
			ari=evitem.arrindex;
			alld=1;
			if (ari<0) { ari=0; alld=0; }
			arsuf=["","_left","_right","_both"][ari];
			
			div1=document.createElement("DIV");
			div1['id']=mixid;
			div1.className="event";
			div1.style.marginTop=evitem.divOff+"px";
			if (invis) div1.style.display="none";
			div1.onmouseout=mouse0;
			div1.onmouseover=mouse1;
			div1.style.width=evitem.divWidth;
			div1.style.left=evitem.divLeft;
			div1.style.top=evitem.divTop;

			divtit=evlink["event_name"];
			div2=document.createElement("DIV");
			div2.className=(ari?("a"+arsuf+" a"):"a");

			div3=document.createElement("DIV");
			div3.className=(ari?("b"+arsuf+" b"):"b");
			
			div1.appendChild(div2);
			div1.appendChild(div3);	
			
			div4=document.createElement("DIV");
			div4.className="event_middle";
			div4.style.height=evitem.divHeight;
			div4.onmousedown=function(ev) {invoke(ev,this); return false;};
			div4.onmouseup=function(ev) { area.skip = false; };
			div4.id = mixid +'_mid';

			div5=document.createElement("DIV");
			div5.className="event_text"+arsuf;

			div6=document.createElement("DIV");
			div6.className=(ari?("time time"+arsuf):"time"); 
			div6.id = 'time_'+mixid;

			if (ari<=0) {
				var tfDateObj = dt2date(evlink["event_timefrom"]);
				var ttDateObj = dt2date(evlink["event_timetill"]);
				tf=gtime(tfDateObj);
				tt=gtime(ttDateObj,24);
				if(setcache['timeformat'] == 2) titl=tf.time+' - '+tt.time;
				if(setcache['timeformat'] == 1)
				{ 
					var chour_f = tfDateObj.getHours();
					var cmin_f = tfDateObj.getMinutes();
					var chour_t = ttDateObj.getHours();
					var cmin_t = ttDateObj.getMinutes();
					tf = ((chour_f  == 0)? '12' : ((chour_f > 12)? chour_f -12 : chour_f)) + ((cmin_f == 0) ? ' ' : ':'+ cmin_f + ' ') + ((chour_f <12) ? "AM" : "PM");
					tt = ((chour_t  == 0)? '12' : ((chour_t > 12)? chour_t -12 : chour_t)) + ((cmin_t == 0) ? ' ' : ':'+ cmin_t + ' ') + ((chour_t <12) ? "AM" : "PM");
					titl= tf +' - '+ tt;
				}
				if (alld) titl=" "; else divtit="["+titl+"] "+divtit;
				tex1=document.createTextNode(titl);
				//if (alld) div6.style.marginTop="3px";
				div6.appendChild(tex1);
			}
			div7=document.createElement("DIV");
			div7.className=(ari?("text text"+arsuf):"text");
			//if(navigator.userAgent.indexOf("MSIE") >= 0) div7.style.left = '3px';
			
			var textarea = document.createElement('textarea');
			textarea.id = "inline_" + mixid;
			
			span1 = document.createElement('span'); 
			span1.id = 'evspan_' + mixid;
			span1.style.cursor = 'text';
			
			tex2=document.createTextNode(evlink["event_name"]);
			tex3=document.createTextNode(evlink["event_name"]);
			textarea.appendChild(tex2);
			textarea.onblur = function() { return fieldBlur(this); };
			textarea.onkeydown = function (ev) { return TextareaKeyDownHandler(ev, this, 50); };
			textarea.onkeyup = function (ev) { return TextareaKeyUpHandler(ev, this); };
			textarea.onmousedown = function(ev){ span1_pres = true; };
			
			span1.appendChild(tex3);
			span1.appendChild(textarea);
			div7.appendChild(span1);
			div5.appendChild(div6);
			div5.appendChild(div7);
			div4.appendChild(div5);
			div1.appendChild(div4);
			div8=document.createElement("DIV");
			div8.id = mixid +'_c';
			if (evmode<2) { div8.onmousedown = eventMouseDown; }
			div8.className=((ari||(evmode==2)||(evlink['event_allday']=="1"))?("b"+arsuf+" b"):"b d");
			div1.appendChild(div8); 
			div9=document.createElement("DIV");
			div9.id = mixid +'_a';
			if (evmode<2) { div9.onmousedown = eventMouseDown; }
			div9.className=((ari||(evmode==2)||(evlink['event_allday']=="1"))?("a"+arsuf+" a"):"a d");
			div1.appendChild(div9); 
			div1.title=divtit;
			var basedom=findDOM(evitem.dom,0);
			basedom.appendChild(div1);
			setHeightTextarea(textarea);
			//getHeightTextarea(mixid);
			}
		}
		}
	} catch(e) { }
}

function render_calendar()
{ 
	mycache_c = cacheLoadCalendar(mydate,false);
	if (mycache_c.calendars!=undefined){
		var index = 10;
		for (var i in mycache_c.calendars)
		{ 
			var cl = mycache_c.calendars[i];
			if(cl!=undefined){
				render_show_calendar(mycache_c.calendars[i], index);
				renderContainerForEvent(mycache_c.calendars[i]['calendar_id'], mycache_c.calendars[i]['calendar_color']);
			}
		}
	}
}

function renderContainerForEvent(id, color)
{
	var div1 = document.createElement('div');
	div1.className = 'eventcontainer_'+color;
	div1.id = 'container_'+id+'_1';
	$('grid_1d').appendChild(div1);
	var div2 = document.createElement('div');
	div2.className = 'eventcontainer_'+color;
	div2.id = 'container_'+id+'_2';
	$('grid_2d').appendChild(div2);

	var div3 = document.createElement('div');
	div3.className = 'eventcontainer_'+color;
	div3.id = 'container_'+id+'_3';
	$('grid_1w').appendChild(div3);
	var div4 = document.createElement('div');
	div4.className = 'eventcontainer_'+color;
	div4.id = 'container_'+id+'_4';
	$('grid_2w').appendChild(div4);

	var div5 = document.createElement('div');
	div5.className = 'eventcontainer_'+color;
	div5.id = 'container_'+id+'_5';
	$('grid_2_month').appendChild(div5);
}

function render_calc_week() { 
	var rend=new Array();
	var gmydate=gtime(mydate);
	var my8=gmydate.to8;
	var wd=gmydate.week-1;
//	var w1=from8(wf8);
//	var df=gtime(w1);

	if (setcache['weekstartson'] == 0) {
		if (wd==6) wd = 0;
		else wd = wd + 1;
	}

	var wf8=move8(my8,-wd);
	var wt8=move8(my8,(6-wd));

	//var w2=from8(wt8);
	//var dt=gtime(w2);

	showLimits.weekFrom=wf8;
	showLimits.weekTill=wt8; 

	var cdi=wf8;
	do {
		if (mycache.days[cdi]!=undefined){
			for (var i=0; i<(mycache.days[cdi].length); i++)
			{
				if(typeof(mycache.days[cdi][i]) != 'function')
				{
					var ev = mycache.days[cdi][i];
					rend[ev['event_id']] = render_calc_week_single(ev);
				}
			}
		}
		cdi=move8(cdi,1);
	} while (cdi<=wt8)
	return(rend);
}

function render_calc_week_single(ev) {
	var ddr=new Array();
	var df=gtime(from8(showLimits.weekFrom));
	var dt=gtime(from8(showLimits.weekTill));

	var timefrom=dt2date(ev['event_timefrom']);
	var timetill=dt2date(ev['event_timetill']);

	var tf=gtime(timefrom);
	var tt=gtime(timetill,24);

	allday = (ev['event_allday']=="1");
	idsuf = (allday?"_3":"_4");
	idcal = ev['calendar_id'];
	baseid = mycache_c.calendars[ev['calendar_id']]['calendar_color'];
	basedom = "container_"+idcal+idsuf;
	dayz = 1;
	arrindex = -1;
	if (allday) {
		arrindex=0;
		if (tf.to8<df.to8) { tf=df; arrindex+=1; }
		if (tt.to8>dt.to8) { tt=dt; arrindex+=2; }
		dayz=Math.round(((tt.dobj.getTime()-tf.dobj.getTime())/86400000)+1);
		dayz=1+range8(tf.to8,tt.to8);
	};

	if (setcache['weekstartson'] == 0) {
		var divLeft = (tf.week == 7) ? 0 : (((100/7)*(tf.week))+"%");
	} else {//setcache['weekstartson'] == 1
		var divLeft = ((100/7)*(tf.week-1))+"%";
	};

	var divWidth=((100/7)*dayz)+"%";
	if (allday) {
		var divTop="0px";
		var divHeight="3.9ex";
	} else {
		var divTop=(9*tf.fh)+"ex";
		var divHeight=((9*(tt.fh-tf.fh))-0.6)+"ex";
	};
	var divOff=0;
	ddr[0]={dom:basedom, arrindex:arrindex, divWidth:divWidth, divHeight:divHeight, divLeft:divLeft, divTop:divTop, divOff:divOff};
	return(ddr);
}

function render_calc_day() {
	var rend=new Array();
	var gmydate=gtime(mydate);
	var my8=gmydate.to8;
	showLimits.day=my8;
	wd=gmydate.week-1; 
	if (mycache.days[my8]!=undefined){
		for (var i=0; i<(mycache.days[my8].length); i++) 
		{
			var ev=mycache.days[my8][i];
			rend[ev['event_id']]=render_calc_day_single(ev);
		}
	};
	return(rend);
}

function render_calc_day_single(ev) {
	var gmydate=gtime(mydate); var my8=gmydate.to8;
	ddr=new Array();
	timefrom=dt2date(ev['event_timefrom']); timetill=dt2date(ev['event_timetill']);
	tf=gtime(timefrom); tt=gtime(timetill,24);
	allday=(ev['event_allday']=="1"); idsuf=(allday?"_1":"_2");
	baseid=mycache_c.calendars[ev['calendar_id']]['calendar_color'];
	idcal = ev['calendar_id'];
	basedom="container_"+idcal+idsuf;
    arrindex=-1;
	if (allday) { 
		arrindex=0;
		if (tf.to8<my8) arrindex+=1;
		if (tt.to8>my8) arrindex+=2; 
	};

    var divLeft=0; var divWidth="100%";
    if (allday) {
    	var divTop="0px"; var divHeight="3.9ex";
    } else {
    	var divTop=(9*tf.fh)+"ex"; var divHeight=((9*(tt.fh-tf.fh))-0.6)+"ex";
    }
	var divOff=0;
	ddr[0]={dom:basedom, arrindex:arrindex, divWidth:divWidth, divHeight:divHeight, divLeft:divLeft, divTop:divTop, divOff:divOff};
return(ddr);
}

function render_calc_month() {
	var rend=new Array();
	var gmydate=gtime(mydate);
	var my8=gmydate.to8;
	wd=gmydate.week-1;
	myglim=gridLimits(my8,false);
	m_from=myglim.from;
	m_till=myglim.till;
	showLimits.monthFrom=myglim.from;
	showLimits.monthTill=myglim.till;
	colum=0; cline=0;
	cmi=m_from;
	do{
		if (mycache.days[cmi]!=undefined){
			for (var ii=0; ii<(mycache.days[cmi].length); ii++)
			{
				var ev=mycache.days[cmi][ii];
				rend[ev['event_id']]=render_calc_month_single(ev);
			}
		}
		rr = cmi;
		cmi=move8(cmi,1);
		colum++;
		if (colum==7) { colum=0; cline++; }
	} while (cmi<=m_till)
	return(rend);
}

function render_calc_month_single(ev) {
	var df=gtime(from8(showLimits.monthFrom));
	var dt=gtime(from8(showLimits.monthTill));
	ddr=new Array();
	timefrom=dt2date(ev['event_timefrom']);
	timetill=dt2date(ev['event_timetill']);
	tf=gtime(timefrom);
	tt=gtime(timetill);
	allday=(ev['event_allday']=="1"); 
	idcal = ev['calendar_id'];
	basedom="container_"+idcal+'_5';
	dayz=1; arrindex=-1;
	var colum=Math.round(range8(showLimits.monthFrom,tf.to8)%7);
/*	if (setcache['weekstartson'] == 0) {
		colum = (colum == 6) ? 0 : (colum+1);
	}
*/
/*    if (allday) { arrindex=0; //alert('violation @ '+ev['event_id']);
      if (tf.dobj<df.dobj) { tf=df; arrindex+=1; }
      if (tt.dobj>dt.dobj) { tt=dt; arrindex+=2; }
      dayz=parseInt((tt.dobj.getTime()-tf.dobj.getTime())/86400000)+1;
    }
    var colum=range8(showLimits.monthFrom,tf.to8)%7; //alert('Column: '+colum);
    var divLeft=((100/7)*colum)+"%";
    var divWidth=(100/7)+"%";
    var divTop="0px";
    var divHeight="3.9ex"; var divOff=0;
    ddr[0]={dom:basedom, arrindex:arrindex, divWidth:divWidth, divHeight:divHeight, divLeft:divLeft, divTop:divTop, divOff:divOff}
*/
	if (allday) {
		arrindex=0;
		var dind=0;
		while ((t8=tf.to8)<df.to8) {
			if (!arrindex) arrindex=1;
			t8=move8(t8,8-(tf.week)); tf=gtime(from8(t8)); ddr[dind]=null; dind++; // alert(dind+") "+tf.to8+" -- "+df.to8);
		}
		if (tt.dobj>dt.dobj) { tt=dt; arrindex+=2; }
		dayz=parseInt((tt.dobj.getTime()-tf.dobj.getTime())/86400000)+1;
		var colum=Math.round(range8(showLimits.monthFrom,tf.to8)%7);
		var dcur=tf.to8; var dfin=tt.to8; var decont=true;
		do {
			var dwid=7-colum; var dmov=move8(dcur,dwid-1);
			if (dmov>dfin) { dwid=1+range8(dcur,dfin); dmov=dfin; }
			var divLeft=((100/7)*colum)+"%";
			var divWidth=dwid*(100/7)+"%";
			var divTop="0px";
			var divHeight="3.9ex"; var divOff=0;
			var arri=arrindex;
			if ((arrindex==1)&&(dcur!=df.to8)) arri=0; //{ arri=0; alert(ev['event_id']+'/'+arrindex+'. '+dcur+' - '+df.to8); }
			if ((arrindex==2)&&(dmov!=dt.to8)) arri=0; //{ arri=0; alert(ev['event_id']+'/'+arrindex+'. '+dmov+' - '+dt.to8); }
			if ((arrindex==3)&&( (dmov!=dt.to8)||(dcur!=df.to8) )) arri=0; //{ arri=0; alert(ev['event_id']+'/'+arrindex+'. '+dcur+' - '+df.to8+' --- '+dmov+' - '+dt.to8); }
			ddr[dind]={dom:basedom, arrindex:arri, divWidth:divWidth, divHeight:divHeight, divLeft:divLeft, divTop:divTop, divOff:divOff, days:dwid};
			dind++; colum=0; dcur=move8(dcur,dwid);
			if ((dcur>dfin)||(dcur>showLimits.monthTill)) decont=false;
		} while (decont);
	} else {
		var divLeft=((100/7)*colum)+"%";
		var divWidth=(100/7)+"%";
		var divTop="0px";
		var divHeight="3.9ex"; var divOff=0;
		ddr[0]={dom:basedom, arrindex:arrindex, divWidth:divWidth, divHeight:divHeight, divLeft:divLeft, divTop:divTop, divOff:divOff, days:1};
	}
	return(ddr);
}

function render_calc_month_height() {
  vd=findDOM("mainbody",1); vdh=vd.height;
  if ((ap=vdh.indexOf("px"))>=0) vdh=vdh.substring(0,ap);
  return(vdh);
}

/* cut start */

function copyar(arr) {
var tar=new Array();
for (var i in arr) { 
  var arit=arr[i]; var itype=typeof(arit); 
  if (itype=="object") arit=copyar(arr[i]);
  tar[i]=arit;
  }
return(tar);
}

function dotrender (myz,myd,myi,wk,sect,dayz,dub) {
	delete incros;
	incros = new Array;
	wk=Number(wk);
	if (dub==undefined) dub=0;

	var myc=copyar(myz);

	var i,gi,j,mit,tc,col,mcol,mycj,mywid,myleft,ofs;

	if (myd[wk]!=undefined) {
		var zi=0;
		var grp=new Array();
		for (j in myd[wk]) {
			mit=myd[wk][j]; 
		    if (typeof(mit)=="function") continue;
		    if (mit['event_allday']!='1') {
				var tc=tcalc(mit);
				mit['len']=tc.len;
				mit['wid']=1;
				grp[zi]=mit;
				zi++;
			}
		}

		if (zi>0){
			grp=tsort(grp,zi);
			mcol=1;
	
			for (var i=0; i<zi; i++) {
				col=1;
				for (var j=0; j<i; j++) {
					if (tcross(grp[j],grp[i])&&(grp[j]['col']>=col)) {
						col=1+grp[j]['col'];
						if (col>mcol) mcol=col;
					}
				}
			grp[i]['col']=col;
			}

			do {
				var gone=false;
				for (i=0; i<zi; i++) { 
					var movd=tmove(grp,i,zi,mcol); 
					if (movd.allow || (!(movd.colide)) ) {
						gone=true;
						tx=grp[i]['event_name']+'\n';
						grp[i]['wid']=grp[i]['wid']+1;
						for (j=0; j<movd.count; j++) {
							jk=movd.data[j];
							grp[jk]['col']=grp[jk]['col']+1;
							tx+=('\n'+grp[jk]['event_name']);
						}
					}
				}
			} while (gone);
	
			for (i=0; i<zi; i++) {
				gi=grp[i];
				j=gi['event_id'];
				mycj=myc[j][0];
				mywid=Number(cute(mycj.divWidth,"%"));
				myleft=Number(cute(mycj.divLeft,"%")); 
				myc[j][0].divRRR="123";
				myc[j][0].divWidth=gi['wid']*(mywid/mcol)+"%";
				ofs=(gi['col']-1)*(100/dayz/mcol);
				myc[j][0].divLeft=(ofs+myleft)+"%";
			}
		}//end if zi>0
	}
  
/* cut-in start */
	if (dub==0) {
 		if (sect==0) { 
			var wlim1=showLimits.day;
			var wlim2=wlim1; 
		} else {
			var wlim1=showLimits.weekFrom;
			var wlim2=showLimits.weekTill; 
		}
		var wchk=new Array();
  
		var za=0;
		var mad=new Array();
		var wz=wlim1;
		while (wz<=wlim2) {
			if (myd[wz]!=undefined) {
				for (j in myd[wz]) {
					mit=myd[wz][j]; 
        			if (typeof(mit)=="function") continue;
	        		if (mit['event_allday']=='1') { var eeid=mit['event_id'];
						if (wchk[eeid]==undefined) {
						atc=atcalc(mit);
						mit['len']=atc.len;
						mit['st']=atc.start;
						mit['en']=atc.end;
						mit['col']=1;
						mad[za]=mit;
						wchk[eeid]=true;
						za++;
					}  }
				}
			}
			wz=move8(wz,1);
		}
	
		amcol=1;
		if (za>0){ //??// dbar(mad,dayz,za);
			mad=tsort(mad,za);
			var amcol=1;
	
			for (ii=0; ii<za; ii++) {
				var h=mad[ii];
				hcol=1;
				var passed=false;
				while (!passed) {
					for (jj=0; jj<ii; jj++) {
						var z=mad[jj];
						if ((hcol==z.col)&&(amcros(h,z))) { hcol++; continue; }
					}
					passed=true;
				}
	
				mad[ii].col=hcol;
                if ( hcol > amcol) amcol=hcol;
				offsetTop=27*(hcol-1);
				j=mad[ii]['event_id'];
				try { myc[j][0].divTop=offsetTop+"px"; } catch (e) { }
			}
		}

		var fullhigh=$("mainbody").offsetHeight;
		fullhigh=Number(cutpx(fullhigh)); 
		fullhigh -= MSIEDetect() ? 0 : 1;
		var halfhigh=Math.floor(fullhigh/2)-10;
		
		var minhigh=53;
		
		var v=8+amcol*27; if (v<minhigh) v=minhigh;
		var vwin=v; if (vwin>halfhigh) vwin=halfhigh;
		$("grid_1"+((sect==1)?"w":"d")).style.height=v+"px";
		$("current_day_1").style.height=v+"px";
		$("area_1_"+((sect==1)?"week":"day")).style.height=vwin+"px";
		$("area_2_"+((sect==1)?"week":"day")).style.height=(fullhigh-28-vwin)+"px";
		
	}

/* cut-in finish */

	for (j in myd[wk]) {
		var mit=myd[wk][j]; 
    	if (typeof(mit)=="function") continue;
	    i=mit['event_id'];
	    if ((mit['event_allday']!='1')) render_event(myc[i],myi[i],sect);
    }
	
	if (dub==0) {
		var wz=wlim1;
		while (wz<=wlim2) {
			if (myd[wz]!=undefined) {
				for (j in myd[wz]) {
					var mit=myd[wz][j]; 
					if (typeof(mit)=="function") continue;
					i=mit['event_id']; 
					if (mit['event_allday']=='1') {
						render_event(myc[i],myi[i],sect);
					}
				}
			}
			wz=move8(wz,1);
		}
	}  
}

function amcros(h,z) {
  var A1=h.st; var A2=h.en; var B1=z.st; var B2=z.en;
  var rval = ( ((A1>=B1)&&(A1<=B2)) || ((A2>=B1)&&(A2<=B2)) || ((B1>=A1)&&(B1<=A2)) || ((B2>=A1)&&(B2<=A2)) );
  return rval;
}


////** Calendars **/////
function SelectColorForNewCalendar(numb, color, div )
{
	div.style.borderColor = '#000';
	for(i = 1; i<13; i++)
	{
		if($('color_'+i).id != div.id)
			$('color_'+i).style.borderColor = '#fff';
	}
	$("calendarColorNumber").value = numb;
	$('calendarcontainer').className = 'eventcontainer_'+ numb;
}
function correctManagerWindow(obj)
{
	var idc = obj['calendar_id'];
	var subj = obj['calendar_name'];
	var desc = mycache_c.calendars[idc];
	var CalendarSubject = $('CalendarSubject');

	$('delbut_calendar').style.display="";
	$('clndform_id').value = idc;
	$('manager_window').style.display='block'; 
	removeQuickMenu();
	$('ef_fulldate_calendar').innerHTML = Lang.CalendarHeaderEdit;
	var color_number = desc['calendar_color'];
	var color_header = getNumberOfColor(color_number);
	SelectColorForNewCalendar(color_number, color_header,$('color_'+color_number));
	CalendarSubject.value = desc['calendar_name'];
	CalendarSubject.focus();
	$('CalendarDescription').value = desc['calendar_description'];
}

// create calendar
function calendarVis (id, input, output) {
	for (var i=0; i<6; i++) {
		var el=$('container_'+id+'_'+i);
		if (el) {
			if (output!='visible') jscss('add',el,'hide');
			else jscss('remove',el,'hide');
		}
	}
}
function hideCalendars(id, color, teg)
{
	removeQuickMenu();
	if(showHide[id]==1){
		showHide[id]=0;
		$('checkbox_'+id).checked = true;
		calendarVis(id, color,'visible');
		showHidecalendars(id, false, color);
	}else{
		showHide[id]=1;
		showHidecalendars(id, true, color);
	}
}

function showHidecalendars(id, bool, color)
{
	if(bool)
		var vis = 'visible';
	else
		var vis = 'hidden';
	for (var i in mycache_c.calendars)
	{
		if(mycache_c.calendars[i] != undefined){
			var id_c = mycache_c.calendars[i]['calendar_id'];
			var color = mycache_c.calendars[i]['calendar_color'];
			if(id_c != id && id_c != undefined){
				$('checkbox_'+id_c).checked = bool;
				calendarVis(id_c, color,vis);
				showHide[id_c]=1;
			}
		}
	}
}

function checkedHide()
{
	for (var i in mycache_c.calendars)
	{
		if( mycache_c.calendars[i] != undefined){
			var id_c = mycache_c.calendars[i]['calendar_id'];
			if( id_c != undefined)
				showHide[id_c]=1;
		}
	}
}

//** create and remove calendar for Calendars manadger **//

function createCalendarSelect()
{
	if (mycache_c.calendars!=undefined)
	{
		var mas_cal = new Array();
		var id_calendar = $('id_calendar');

		for (var i in mycache_c.calendars)
		{
			var cl = mycache_c.calendars[i];
			if(cl!=undefined)
			{
				var option = document.createElement('div');
				option.id = 'clnd_'+ cl['calendar_id'];
				option.style.position = 'relative';
				var div_color = getNumberOfColor(cl['calendar_color']);
				var calendar_select_box = $('edit_select_box_list');
				option.onclick = function()
				{
					var new_calendar = mycache_c.calendars[this.id.substr(5, this.id.length)];
					var form_calendar_id = id_calendar.value;
					$('calen_sal').innerHTML = HtmlEncode(new_calendar['calendar_name']);
					$('eventcontainer').className = 'eventcontainer_'+new_calendar['calendar_color'];
					$('clnd_' + form_calendar_id).style.fontWeight = 'normal';
					id_calendar.value = new_calendar['calendar_id'];
					$('clnd_' + new_calendar['calendar_id']).style.fontWeight = 'bold';
					calendar_select_box.style.display = 'none';
					calendar_select_box.style.visibility = 'hidden';
					$('color_calendar_now').style.backgroundColor = getNumberOfColor(new_calendar['calendar_color']);
				};
				if(id_calendar.value == cl['calendar_id']) {option.style.fontWeight = 'bold';}
				option.onmouseover = function(){ this.style.backgroundColor = '#DDDDDD'};
				option.onmouseout = function(){ this.style.backgroundColor = '#FFFFFF'};
				option.innerHTML = '<div class="color_pick" style="border: 1px solid #fff; margin:1px 0px 0px 3px; position:relative; background-color: '+div_color+'"></div> '+ '&nbsp;&nbsp;' +  HtmlEncode(cl['calendar_name']);
				mas_cal[i] = option;
				calendar_select_box.appendChild(option);
			}
		}
	}
}

function ShowHideEditWinSelectList(){
	var selList = $('edit_select_box_list');
	if(typeof(selList.opened) == "undefined") selList.opened = false;
	if (selList.opened == false || selList.style.display=='none')
	{
		selList.style.display = 'block';
		selList.style.visibility = 'visible';
		selList.opened = true;
		var len=0;
		selList.style.width = "auto";
		var divs = selList.getElementsByTagName("div");
		if(FireFoxDetect())
		{
			selList.style.width = (selList.clientWidth>=109)?('auto'):("104px");
		}else{
			selList.style.width = (selList.clientWidth>=109)?((selList.clientWidth > 290) ? ((selList.clientWidth +15) + 'px') : 'auto'):("104px");
		};
	} else {
		selList.style.display = 'none';
		selList.style.visibility = 'hidden';
		selList.opened = false;
	}
}

function removeCalendarSelect()
{
	var calendar_select_box = $('edit_select_box_list');
	if (mycache_c.calendars!=undefined){
		var mas_cal = new Array();

		for (var i in mycache_c.calendars)
		{
			if(mycache_c.calendars[i] != undefined){
				var id_cal = mycache_c.calendars[i]['calendar_id'];
				calendar_select_box.style.display = 'none';
				calendar_select_box.style.visibility = 'hidden';
				while($('clnd_'+id_cal) != undefined){
					$('clnd_'+id_cal).parentNode.removeChild($('clnd_'+id_cal));
				}
			}
		}
	}
}

function render_show_calendar(calendar, index)
{
	var idc = calendar['calendar_id'];
	var color = calendar['calendar_color'];
	var conteiner = document.createElement('div');
	conteiner.className = "eventcontainer_"+calendar['calendar_color'];
	conteiner.id = 'calendar_'+idc;
	conteiner.style.zIndex = index;
	var formCheckBoks = document.createElement('form');
	var checkBox = document.createElement('input');
	checkBox.id = 'checkbox_'+idc;
	checkBox.type = 'checkbox';
	checkBox.onclick = function() 
	{
		if(this.checked){
			calendarVis(idc, color,'visible'),
			checkedHide()
		} else{
			calendarVis(idc, color,'hidden')
		}
	};
	formCheckBoks.appendChild(checkBox);

	var contSubject = document.createElement('div');
	contSubject.className = 'event';
	var div1 = document.createElement('div');
	div1.className = 'a';
	var div2 = document.createElement('div');
	div2.className = 'b';
	var div3 = document.createElement('div');
	div3.className = 'event_middle';
	div3.onmouseover = function() { 
		$('calendar_'+idc+'_text').style.textDecoration = 'underline'; 
		$("vis_check_"+idc).style.background='url(./calendar/skins/calendar/arrow_bottom.gif) no-repeat center';
	};
	div3.onmouseout = function() { 
		$('calendar_'+idc+'_text').style.textDecoration = 'none'; 
		$("vis_check_"+idc).style.background='url(./calendar/skins/calendar/arrow_bottom_light.gif) no-repeat center';
	};

	var div31 = document.createElement('div');
	div31.className = 'calendar_text';
	div31.id='calendar_'+idc+'_dtext';
	div31.onmousedown = function() { this.className = 'calendar_text' };
	div31.onmouseover = function() { this.className = 'calendar_text_hover' };
	div31.onclick= function() {
		if (mycache_c.calendars!=undefined){
			for (var i in mycache_c.calendars)
			{ 
				cl = mycache_c.calendars[i];
				if(cl!=undefined){
					var c = $('calendar_'+mycache_c.calendars[i]['calendar_id']);
					c.style.zIndex = index;
				}
			 }
		};
		var QW=QOpen; 
		if (QOpen>0) { 
			removeQuickMenu();
		};
		if (QW!=idc) {
			createQuickMenu(calendar, index+1);
			conteiner.style.zIndex = index+2;
			
		}
	};

	var divText = document.createElement('div');
	divText.className = 'text';

	var aText = document.createElement('a');
	aText.id = 'calendar_'+idc+'_text';
	aText.title = calendar['calendar_name'];
	aText.href="javascript:void(0);";
	aText.innerHTML = HtmlEncode(calendar['calendar_name']);
	divText.appendChild(aText);
	div31.appendChild(divText);

	var div32 = document.createElement('div');
	div32.className = 'vis_check';
	div32.id = "vis_check_"+idc;
	div32.onmouseover = function() { $('calendar_'+idc+'_text').style.textDecoration = 'underline'; this.style.background='url(./calendar/skins/calendar/arrow_bottom.gif) no-repeat center' };
	div32.onmouseout = function() { $('calendar_'+idc+'_text').style.textDecoration = 'none'; this.style.background='url(./calendar/skins/calendar/arrow_bottom_light.gif) no-repeat center' };

	div31.appendChild(div32);
	div3.appendChild(div31);
	var div4 = document.createElement('div');
	div4.className = 'b';
	var div5 = document.createElement('div');
	div5.className = 'a';
	contSubject.appendChild(div1);
	contSubject.appendChild(div2);
	contSubject.appendChild(div3);
	contSubject.appendChild(div4);
	contSubject.appendChild(div5);

	conteiner.appendChild(contSubject);
	conteiner.appendChild(formCheckBoks);

	$('manager_list').appendChild(conteiner);
	$('checkbox_'+idc)['checked']='checked';
}

function changeColorContainer(id, numb)
{
	var isVisible = $('checkbox_'+id).checked;
	for(var i=1; i<6; i++){
		var el=$('container_'+id+'_'+i);
		if (el){
			el.className=(isVisible)?('eventcontainer_'+numb):('eventcontainer_'+numb+" hide");
			$('calendar_'+id).className = 'eventcontainer_'+numb;
		}
		else
			$('calendar_'+id).className='eventcontainer_'+numb;
	};
	var row = mycache_c.calendars[id];
	var array = delarr(mycache_c.clr, row['calendar_color']);
	mycache_c.clr = new Array();
	for(var i=0; i<array.length; i++){
		if(typeof(array[i])!="function")
		 	mycache_c.clr[array[i]] = array[i];
	};
	mycache_c.clr[numb] = numb;
	row['calendar_color'] = numb;
}

function colorChange (id, numb) {
	ShowInfo(Lang.InfoSaving);
	changeColorContainer(id, numb);
	var url = processing_url+'?action=update_calendar&calendar_id=' + id +'&color_id=' + numb+'&nocache=' + Math.random();
	var req = getXMLHTTPRequest();
	var res = '';
	if (req) {
		try {
			req.open("GET",url,false);
			req.send(null);
			res = req.responseText;
		} catch(e) { }
	};
	var revent = res.parseJSON();
	if (servErr(revent,Lang.ErrorUpdateCalendar)) {
		return;
	};
	removeQuickMenu();
	HideInfo();
}


function moveQuickMenu() {
	var qm=$("quick_edit"); var qH=qm.offsetHeight; var pH=28;

	var ml=$("manager_list");
	var mainOf=ml.offsetTop;
	var ci=$("calendar_"+QOpen);
	var mscrol=ml.scrollTop;
	var cofset=ci.offsetTop;
    var fullH=mainOf+ml.offsetHeight;
	var tpos=cofset-mscrol; 
	//alert (tpos+' -- '+mainOf);
	if (tpos<0) tpos=0;
	var qmtop=mainOf+tpos-qH+(FireFoxDetect()?2:1); var qDT=1;
	
	var ddown=mainOf+cofset-mscrol+pH;
	if ((ddown+qH)<fullH) {	qmtop=ddown-(FireFoxDetect()?3:4); var qDT=2;};
//	alarm (qmtop+" ... "+mainOf);
//alarm ((qmtop+qH)+" ... "+fullH);
	
	var lin1=cofset-mscrol; var lin2=lin1+pH;
	if ((lin2<0)||(lin1>ml.offsetHeight)) removeQuickMenu();
	//if ( ((qDT=1)&&((qmtop+qH)>fullH)) || ((qmtop+qH)<mainOf) ) removeQuickMenu();
// menu will be removed in following cases:
// 1) drop down, menu top is above manager_list top
// 2) any drop, marker bottom is above list top
// 3) drop down, marker below list top
	else qm.style.top=qmtop+"px"; framesetQuickMenu(qDT);
	
}

function removeQuickMenu() {
	var qm=$("quick_edit");
	//if (qm) { ChildKill(qm); qm.parentNode.removeChild(qm); }
	qm.style.display = "none";
	QOpen=0;
	eventOffAny($("allspan"),"mouseup",clickOutside);
	eventOffAny($("manager_list"),"scroll",moveQuickMenu);
}

// create menu for calendar
function createQuickMenu(odj, index)
{
	var color = odj['calendar_color'];
	var idc = odj['calendar_id'];

	var divEdit = $('quick_edit');
	divEdit.innerHTML = "";
	openns = odj['calendar_id'];
	//divEdit.className = 'event edit_gray';
	divEdit.style.zIndex = index;

	var div1 = document.createElement('div');
	div1.className = 'a';
	var div2 = document.createElement('div');
	div2.className = 'b';
	
	var div3 = document.createElement('div');
	div3.className = 'event_middle';
	var div31 = document.createElement('div');
	div31.className = "calendar_text";
	var span31 = document.createElement('span');
	span31.className = 'text';
	var a31 = document.createElement('a');
	a31.href="javascript:void(0)";
	a31.onclick = function()
	{
		removeQuickMenu();
		$('calendarColorNumber').value = odj['calendar_color'];
		evform_create(idc);
	};
	a31.innerHTML = Lang.EventCreate;
	span31.appendChild(a31);
	div31.appendChild(span31);
	div3.appendChild(div31);

	var div4 = document.createElement('div');
	div4.className = 'event_middle';
	var div41 = document.createElement('div');
	div41.className = "calendar_text";
	var span41 = document.createElement('span');
	span41.className = 'text';
	var a41 = document.createElement('a');
	a41.href="javascript:void(0)";
	a41.id = 'showHide';
	a41.onclick = function(){ hideCalendars(idc, color, a41) };
	if (showHide[idc]==undefined) { showHide[idc]=1; }
	a41.innerHTML = showHide[idc]? Lang.CalendarHideOther : Lang.CalendarShowOther;
	span41.appendChild(a41);
	div41.appendChild(span41);
	div4.appendChild(div41);

	var div5 = document.createElement('div');
	div5.className = 'event_middle';
	var div51 = document.createElement('div');
	div51.className = "calendar_text";
	var span51 = document.createElement('span');
	span51.className = 'text';
	var a51 = document.createElement('a');
	a51.href="javascript:void(0)";
	a51.onclick=function() {correctManagerWindow(odj)};
	a51.innerHTML = ' ' + Lang.CalendarActionEdit + ' ';
	span51.appendChild(a51);
	div51.appendChild(span51);
	div5.appendChild(div51);

	var div6 = document.createElement('div');
	div6.className = 'event_middle';
	var div61 = document.createElement('div');
	div61.className = "calendar_text";
	var span61 = document.createElement('span');
	span61.className = 'text';
	var a61 = document.createElement('a');
	a61.href="javascript:void(0)";
	a61.onclick = function() { removeQuickMenu(); manager_form_delete(idc) };
	a61.innerHTML = Lang.CalendarRemove + ' ';
	span61.appendChild(a61);
	div61.appendChild(span61);
	div6.appendChild(div61);

	var div7 = document.createElement('div');
	div7.className = "event_middle evt_mid_color";
	var div71 = document.createElement('div');
	div71.className = "calendar_text";
	div71.style.backgroundColor = '#fff';
	var div = document.createElement('div');

	var divPink = createColorCell(idc, 1, div);
	var divGreen = createColorCell( idc, 2, div);
	var divBlue = createColorCell(idc, 3, div);
	var divColor1 = createColorCell(idc, 4, div);
	var divColor2 = createColorCell( idc, 5, div);
	var divColor3 = createColorCell(idc, 6, div);
	var divColor4 = createColorCell(idc, 7, div);
	var divColor5 = createColorCell(idc, 8, div);
	var divColor6 = createColorCell(idc, 9, div);
	var divColor7 = createColorCell(idc, 10, div);
	var divColor8 = createColorCell(idc, 11, div);
	var divColor9 = createColorCell(idc, 12, div);

	div71.appendChild(div);
	div7.appendChild(div71);

	var div8 = document.createElement('div');
	div8.className = 'b';
	var div9 = document.createElement('div');
	div9.className = 'a';
	divEdit.appendChild(div1);
	divEdit.appendChild(div2);
	divEdit.appendChild(div3);
	divEdit.appendChild(div4);
	divEdit.appendChild(div5);
	divEdit.appendChild(div6);
	divEdit.appendChild(div7);
	divEdit.appendChild(div8);
	divEdit.appendChild(div9);


//	var mainOf=($("logo").offsetHeight+$("toolbar").offsetHeight+$("upper_indent").offsetHeight+$("manager_list").offsetTop + $('accountslist').offsetHeight); 
	var ml=$("manager_list");
	var mainOf=ml.offsetTop;
	var ci=$("calendar_"+idc);
	var mscrol=ml.scrollTop;
	var cofset=ci.offsetTop;


	var tpos=cofset-mscrol;
	if (tpos<0) tpos=0;

	divEdit.style.display = "block";
	
	divEdit.style.top=(mainOf+tpos-divEdit.offsetHeight+(FireFoxDetect()?2:1))+"px";
	//divEdit.style.top=(ml.offsetTop)+"px";
	
	var ddown=mainOf+cofset-mscrol+28; var qDT=1;
	if ((ddown+divEdit.offsetHeight)<(mainOf+ml.offsetHeight)) { 
		divEdit.style.top=(ddown-(FireFoxDetect()?3:4))+"px"; 
		qDT=2;
	};

	
	framesetQuickMenu(qDT);
	QOpen=idc;
	eventOnAny(ml,"scroll",moveQuickMenu);

	eventOnAny($("allspan"),"mouseup",clickOutside);
}

function clickOutside(evt) { if (QOpen==0) return(null);
  var even = window.event ? window.event.srcElement : evt.target;
  if (even.id) { if ((even.id==('calendar_'+QOpen+'_text'))||(even.id=='calendar_'+QOpen+'_dtext')||(even.id==("vis_check_"+QOpen))) return(null); }
  removeQuickMenu();
}

function framesetQuickMenu(menumode) {
  var qedit=$("quick_edit");
  if (menumode==2) {
  qedit.childNodes[0].className="a qmenu1"; qedit.childNodes[1].className="a qmenu1"; qedit.childNodes[7].className="b"; qedit.childNodes[8].className="a";
  } else {
  qedit.childNodes[0].className="a"; qedit.childNodes[1].className="b"; qedit.childNodes[7].className="a qmenu1"; qedit.childNodes[8].className="a qmenu1";
  }
}

function createColorCell(idc, colorNumber, div)
{
	var bgColor = getNumberOfColor(colorNumber);
	var divColor = document.createElement('div');
	divColor.className = "color_pick";
	divColor.style.backgroundColor = bgColor;
	divColor.onclick= function(){colorChange(idc, colorNumber); removeQuickMenu(); };
	divColor.onmouseover= function() { this.className='color_pick_hover' };
	divColor.onmouseout=function() { this.className='color_pick' };
	div.appendChild(divColor);
}

function getNumberOfColor(color_id)
{
	color_id = Number(color_id);
	var calendar_color;
	switch (color_id) {
		case (1):
			calendar_color = "#ef9554";
			break;
		case (2):
			calendar_color = "#f58787";
			break;
		case (3):
			calendar_color = "#6fd0ce"; 
			break;
		case (4):
			calendar_color = "#90bbe0";
			break;
		case (5):
			calendar_color = "#baa2f3";
			break;
		case (6):
			calendar_color = "#f68bcd";
			break;
		case (7):
			calendar_color = "#d987da";
			break;
		case (8):
			calendar_color = "#4affb8";
			break;
		case (9):
			calendar_color = "#9f9fff";
			break;
		case (10):
			calendar_color = "#5cc9c9";
			break;
		case (11):
			calendar_color = "#76cb76";
			break;
		case (12):
			calendar_color = "#aec9c9";
			break;
		default:
			calendar_color = "#ef9554";
			break;
	};
	return calendar_color;
}

//***///

function render_show(sect) {
	var cach;
	if (sect==1) { var wk=showLimits.weekFrom; var w0=showLimits.weekTill; var dayz=7;}
	if (sect==0) { var wk=showLimits.day; var w0=wk; var dayz=1; }
	if (sect<2) {
		var ddd=0;
		while (wk<=w0) {
			cach=((sect==1)?mycache.w:mycache.d);
			dotrender( cach,mycache.days,mycache.ids,wk,sect,dayz,ddd);
			wk=move8(wk,1);
		}
	} else {
		for (var i in mycache.m) {
			if (typeof(i)!="function") render_event(mycache.m[i],mycache.ids[i],sect);
		}
	}
}

function tmove(arr,i,zi,mcol) { var mallow=true; var mdata=new Array(); var mcoun=0; var colide=true;
  var gi=arr[i]; var col=gi['col']; var wid=gi['wid'];
  if ((col+wid)>mcol) mallow=false; else { colide=false;
	for (var j=0; j<zi; j++) { var gj=arr[j]; 
      if ((i!=j) && (gj['col']==(col+wid)) && (tcross(gi,gj))) colide=true;
	}
    for (var j=0; j<zi; j++) { var gj=arr[j]; 
      var cj=gj['col']; var wj=gj['wid'];
      if ((i==j) || (cj<=col) || (!tcross(gi,gj))) continue;
      if ((cj+wj)>mcol) { var mallow=false; break; }
      var ca=tmove(arr,j,zi,mcol); if  (!ca.allow) { var mallow=false; break; }
      mdata[mcoun]=j; mcoun++;
    }
  };
return { colide: colide, allow: mallow, data: mdata, count: mcoun};
}


function tcalc(obj) {
  obj1=gtime(dt2date(obj['event_timefrom'])); obj2=gtime(dt2date(obj['event_timetill']),24);
  return {len:(obj2.fh-obj1.fh), start:obj1.fh, end:obj2.fh};
}

function tcross(objA,objB) {
var aID=objA['event_id']; var bID=objB['event_id'];
if (incros[aID]==undefined)
	incros[aID] = { 0: gtime(dt2date(objA['event_timefrom'])), 1: gtime(dt2date(objA['event_timetill']),24) };
var A1=incros[aID][0].fh; var A2=incros[aID][1].fh; 
if (incros[bID]==undefined)
	incros[bID] = { 0: gtime(dt2date(objB['event_timefrom'])), 1: gtime(dt2date(objB['event_timetill']),24) };
var B1=incros[bID][0].fh; var B2=incros[bID][1].fh; 
  return ( ((A1>=B1)&&(A2<=B2)) || ((B1>=A1)&&(B2<=A2)) || ((B1>A1)&&(B1<A2)) || ((B2>A1)&&(B2<A2)) || ((A1>B1)&&(A1<B2)) || ((A2>B1)&&(A2<B2)) );
}

function tsort(arr,lenn) {
	if (lenn==undefined) lenn=arr.length;
	for (var i=0; i<(lenn-1); i++) {
		for (var j=(i+1); j<lenn; j++) { 
			if (arr[i]['len']<arr[j]['len']) { //alert('match');
				var buf=arr[j]; arr[j]=arr[i]; arr[i]=buf;
			}
		}
	};
	return (arr);
}
/* cut end */

function atcalc(obj) {
  obj1=gtime(dt2date(obj['event_timefrom'])); obj2=gtime(dt2date(obj['event_timetill']),24);
  var st=obj1.to8; var en=obj2.to8; var le=1+range8(st,en);
  return {len:le, start:st, end:en};
}

function fillEvents() {
	calendars_clear();
	mycache=cacheLoad(mydate,false);
	if (mycache.days == null) return;
	var gmydate=gtime(mydate);
	var my8=gmydate.to8;
	var wd=gmydate.week-1;
	mark_curdate_week();
	mycache.w = render_calc_week();
	mycache.d = render_calc_day();
	mycache.m = render_calc_month();
	render_show(0);
	render_show(1); 
	render_show(2);
	var bb=$("allspan");
	bb.onclick=function(e) { hidefetch(); };

	RecalcScrollArrows();
}

 /* --- */
 
function getDayName(day)
{
	var weekDay = new Array(7);
	weekDay[1] = Lang.FullDayMonday; 
	weekDay[2] = Lang.FullDayTuesday;
	weekDay[3] = Lang.FullDayWednesday;
	weekDay[4] = Lang.FullDayThursday;
	weekDay[5] = Lang.FullDayFriday;
	weekDay[6] = Lang.FullDaySaturday;
	weekDay[0] = Lang.FullDaySunday;

	return weekDay[day];
}

function getDayShortName(day)
{
	var weekDay = new Array(7);
	weekDay[1] = Lang.DayToolMonday;
	weekDay[2] = Lang.DayToolTuesday;
	weekDay[3] = Lang.DayToolWednesday;
	weekDay[4] = Lang.DayToolThursday;
	weekDay[5] = Lang.DayToolFriday;
	weekDay[6] = Lang.DayToolSaturday;
	weekDay[0] = Lang.DayToolSunday;

	return weekDay[day];
}

function getMonthName(month)
{
	var ar = new Array(12);
	ar[0] = Lang.FullMonthJanuary;
	ar[1] = Lang.FullMonthFebruary;
	ar[2] = Lang.FullMonthMarch;
	ar[3] = Lang.FullMonthApril;
	ar[4] = Lang.FullMonthMay;
	ar[5] = Lang.FullMonthJune;
	ar[6] = Lang.FullMonthJuly;
	ar[7] = Lang.FullMonthAugust;
	ar[8] = Lang.FullMonthSeptember;
	ar[9] = Lang.FullMonthOctober;
	ar[10] = Lang.FullMonthNovember;
	ar[11] = Lang.FullMonthDecember;
  
	return ar[month];
}

/***/

function getVerticalColumn(row)
{
	var horizontalColumnWidth = 0;
	if (view == WEEK) {
		horizontalColumnWidth = Math.round($("grid_2w").clientWidth/7);
	} else {
		horizontalColumnWidth = Math.round($("grid_2_month").clientWidth/7);
	};

	if (view == DAY) {
		ColumnNumber = 1;
	} else {
		var ar = new Array();
		for(var i=0; i<7; i++){
			ar[i] = horizontalColumnWidth*i;
		};
		var len=1;
		while(row>ar[len]){
			len++;
		};
		ColumnNumber = len;
	};
	return {Number:ColumnNumber, Width:horizontalColumnWidth};
}

function getHorisontalColumn(mouseHorisCoord){
	var columnHeight = (view == MONTH) ? ($("grid_2_month").clientHeight/6) : 27;
	var columnNumber = Math.floor(mouseHorisCoord / columnHeight);
	return {Number:(columnNumber + 1), Height:(columnNumber * columnHeight)};
}

//row - number of cursor mouse cell
function getTimeEvent(row){
	var time;
	var minutes = row*30;
	var t_minut = minutes%60;
	var t_hour = Math.floor(minutes/60);

	if(setcache['timeformat'] == 1)
	{
		var bonus = 1;
		if(t_hour == 12)
			bonus = 2;

		if(t_hour == 24 || t_hour == 0)
			t_hour = 12;

		if(t_hour > 12)
		{
			t_hour = t_hour - 12;
			bonus = 2;
		}

		t_minut = t_minut!=30? '': (':'+t_minut);
		time = t_hour + t_minut + ((bonus == 1) ? " AM" : " PM");
	} else { //setcache['timeformat'] == 2
		t_hour = fnum(t_hour, 2); 
		t_minut = (t_minut!=30)?'00': t_minut;
		time = t_hour + ':' + t_minut;
	};

	return time;
}
function setWorkAreaOffset() {
	if(typeof(area) != "undefined") {
		WorkAreaOffsetLeft = findPosX(area); //areaX
		WorkAreaOffsetTop = findPosY(area);  //areaY
	}
}

/*
*calendar_id - optional
*/
function ShowDiv(event_data, calendar_id) {
	var calendar, color, color_number, calendar_name, calendar_id;
	var EventSubject = $('EventSubject');
	removeCalendarSelect();
	EventSubject.value			= event_data.subject;
	$('EventTimeFrom').value	= event_data.timeFrom;
	$('EventTimeTill').value	= event_data.timeTill;
	$('EventDateFrom').value	= ConvertFromDateToStr(event_data.fullFromDate);
	$('EventDateTill').value	= ConvertFromDateToStr(event_data.fullTillDate);
	$('EventDescription').value	= event_data.description;
	$('evform_id').value		= event_data.event_id;

	if (event_data.event_id == 0) {
		if (calendar_id == null) {
			calendar = getColorFromCalendar();
		} else {
			if (typeof(mycache_c.calendars)!="undefined"){
				calendar = mycache_c.calendars[calendar_id];
			}else{
				calendar = getColorFromCalendar();
			}
		};
		$('ef_fulldate').innerHTML = Lang.EventHeaderNew;
		$('delbut').style.display = "none";
	} else {
		var event_calendar = mycache.ids[event_data.event_id];
		if (typeof(mycache_c.calendars)!="undefined"){
			for (i in mycache_c.calendars)
			{ 
				if(typeof(mycache_c.calendars[i])!="undefined"){
					if (mycache_c.calendars[i]['calendar_id'] == event_calendar["calendar_id"]) {
						calendar = mycache_c.calendars[i]; 
					}
				}
			}
		};
		$('ef_fulldate').innerHTML = Lang.EventHeaderEdit;
		$('delbut').style.display = "inline";
	};

	color_number	= calendar['calendar_color'];
	color			= getNumberOfColor(calendar['calendar_color']);
	calendar_name	= HtmlEncode(calendar['calendar_name']);
	calendar_id		= calendar['calendar_id'];

	$('eventcontainer').className = 'eventcontainer_'+color_number;
	$('color_calendar_now').style.backgroundColor = color;
	$('id_calendar').value = calendar_id;
	$('calen_sal').innerHTML = calendar_name;
	setMaskHeight();
	$('edit_form').style.display = 'block';
	$('edit_window').style.display='block';
	if(SafariDetect().isSafari) { 
		$('edit_form').style.top = window.innerHeight / 2 + 'px';
	}

	createCalendarSelect();

	var tbl_start = $("st_currentMonth").value.split("_");

	if ( (tbl_start[0] != (event_data.fullFromDate.getMonth()+1)) ||  tbl_start[1] !=  event_data.fullFromDate.getFullYear()) {
		calendarTableStart.RefreshCalendarSelector(1, (event_data.fullFromDate.getMonth()+1), event_data.fullFromDate.getFullYear());
	};
	var tbl_end = $("en_currentMonth").value.split("_");
	if ( (tbl_end[0] != (event_data.fullTillDate.getMonth()+1)) ||  tbl_end[1] !=  event_data.fullTillDate.getFullYear()) {
		calendarTableEnd.RefreshCalendarSelector(1, (event_data.fullTillDate.getMonth()+1), event_data.fullTillDate.getFullYear());
	};

	EventSubject.focus();
	EventSubject.select();
}

function Resize(column, iAy, iBy)//iAy - topHorCoord iBy - bottomHorColumn
{
	if(typeof oDivCheckPanel != "undefined" ) {
		oDivCheckPanel.style.width  = column.Width + 'px';
		oDivCheckPanel.style.display = "block"; 
		if(view == MONTH)
		{
			oDivCheckPanel.style.height = "16.6667%";
			oDivCheckPanel.style.top = (iBy.Number - 1)*16.6667 + '%';
		}
		else
		{
			var heig = 27 + Math.abs(iBy.Height - iAy);
			oDivCheckPanel.style.top = Math.min(iAy, iBy.Height) + 'px';
			oDivCheckPanel.style.height = heig + 'px';
		};

		if(view == DAY){
			oDivCheckPanel.style.left = 0 + '%';
			oDivCheckPanel.style.width = 100 + '%';
		}else{
			oDivCheckPanel.style.left = (100/7)*(column.Number-1) + '%';
			oDivCheckPanel.style.width = column.Width + 'px';
		}
	}
}

var verticalColumn, mouseDownScrolledHorisColumn, mouseDownWorkAreaHorCursor, WorkAreaOffsetLeft, WorkAreaOffsetTop; //,mouseDownHorisColumn
function getCoords() {
	area.pres = false;
	setWorkAreaOffset();
	//WorkAreaOffsetLeft = findPosX(area); //areaX
	//WorkAreaOffsetTop = findPosY(area);  //areaY
	area.onmousemove = moveDot;
	area.onmouseover = moveDot;

	oDivCheckPanel = document.createElement('DIV');  
	oDivCheckPanel.id = "divCheckPanel";
	oDivCheckPanel.style.position = 'absolute';
	oDivCheckPanel.className = 'select_area';
	oDivCheckPanel.style.zIndex = 12;
	oDivCheckPanel.style.display = 'none';
	area.appendChild(oDivCheckPanel);


	area.onmousedown = function(){
		if (typeof WorkAreaVertCursor == "undefined") return;
		area.pres = true;
		verticalColumn = getVerticalColumn(WorkAreaVertCursor); //global variable
		//mouseDownHorisColumn = getHorisontalColumn(WorkAreaHorCursor);//yy = (coorY) - global variable
		mouseDownScrolledHorisColumn = getHorisontalColumn(WorkAreaScrolledHorCursor); //y_vert = (coorYY)
		mouseDownWorkAreaHorCursor = WorkAreaHorCursor;//coorY
	};
	area.onmouseup = function(evt)
	{
		area.pres = false;
		if (area.resizing)
		{
			area.style.cursor = 'default';
			area.resizing = false;

			var res = '';
			var req = getXMLHTTPRequest();

			if (req && (globalTimeTill!=null))
			{
				ShowInfo(Lang.InfoSaving);
				mid_div4.style.height = mid_div4.style.height.substr(0, mid_div4.style.height.length - 2) + "px";
				ev_mes.className = 'event';
				mid_div4.style.cursor = 'default';
				var id_event = Number(mid_div4.id.substring(6,mid_div4.id.length - 10));
				var ttill = globalTimeTill; //cursor data
				if (!isNaN(id_event)) {
					var id_calendar = mycache.ids[id_event]["calendar_id"];
					var datetime = dt2date(mycache.ids[id_event]["event_timetill"]); //data from db
					if (datetime.getHours() == 0 && datetime.getMinutes() == 0) {
						datetime = new Date(datetime.getFullYear(), datetime.getMonth(), (datetime.getDate() - 1));
					};

					if ((setcache['timeformat'] == 2) && (ttill == "00:00")) {
						datetime = new Date(datetime.getFullYear(), datetime.getMonth(), (datetime.getDate() + 1));
					};
					var date_t = to8(datetime);
					if(setcache['timeformat'] == 1)
					{
						for (var i=1; i<timeFormat1.length; i++) {
							if (ttill == timeFormat1[i].Value) {
								ttill = timeFormat2[i].Value;
								break;
							}
						}
					};

					var url = processing_url+'?action=update_event&calendar_id='+id_calendar+'&event_id='+id_event+'&till='+escape(date_t)+'&time_till='+escape(ttill)+'&nocache=' + Math.random();
					try {
						req.open('GET', url, false);
						req.send(null);
						res = req.responseText;
					} catch(e) { }
					var revent = res.parseJSON();
					if (!servErr(revent,Lang.ErrorUpdateEvent)) { 
						var dval = new Array();
						for (var i in revent) {
							if (typeof(revent[i])!="function") dval[i]=revent[i]; 
						};
						delevent(dval);
						addevent(dval);
						dotrender( mycache.w,mycache.days,mycache.ids,date_t,1,7);
						var gmydate=gtime(mydate);
						var my8=Number(gmydate.to8);
						var dt1=Number(date_t);
						if (my8==dt1)
						{
							dotrender(mycache.d,mycache.days,mycache.ids,dt1,0,1); 
						};
						RecalcScrollArrows();
					}
				};
				HideInfo();
			}
		}
		else if (!indrag)
		{
			var even = window.event ? window.event.srcElement : evt.target;

			if( ((typeof(span1_pres) == "undefined") || (typeof(span1_pres) != "undefined" && !span1_pres)) && (even.id != '') ){

				if ($("divCheckPanel").style.display == 'none'&& typeof(mouseDownScrolledHorisColumn) != "undefined" && typeof(mouseDownScrolledHorisColumn.Height) != "undefined" )
				{
					EndScrolledHorColumn = mouseDownScrolledHorisColumn;
					if(mycache_c.calendars != '') {
						Resize(verticalColumn, mouseDownScrolledHorisColumn.Height, mouseDownScrolledHorisColumn); 
					}
				};
				var subEvenId = even.id.substr(0, 5);
				if ((subEvenId != "more_") && mycache_c.calendars != '' && typeof(mouseDownScrolledHorisColumn) != "undefined" && typeof(EndScrolledHorColumn) != "undefined"){
					
					if( (mouseDownScrolledHorisColumn.Number-1) >= EndScrolledHorColumn.Number)
					{
						var tfrom = getTimeEvent(EndScrolledHorColumn.Number - 1);
						var ttill = getTimeEvent(mouseDownScrolledHorisColumn.Number);
					} else {
						var tfrom = getTimeEvent(mouseDownScrolledHorisColumn.Number-1);
						var ttill = getTimeEvent(EndScrolledHorColumn.Number);
					};
					
					var d;
					if (view == WEEK)
					{
						var select_date = new Date(mydate);
						var day = (select_date.getDay() == 0)?7:select_date.getDay();
						if (setcache['weekstartson']==0) {
							d = new Date(select_date.setDate(select_date.getDate() - select_date.getDay() + verticalColumn.Number - 1)); 
						} else {//setcache['weekstartson']==1
							d = new Date(select_date.setDate(select_date.getDate() - day + verticalColumn.Number)); 
						}
					} else if (view == DAY){
						d = mydate;
					} else {//view == MONTH
						tfrom = getTimeEvent(0);
						ttill = getTimeEvent(1);
						d = getFirstLastDayInMonthView(new Date(mydate.getFullYear(),mydate.getMonth(),1))[0];
						d.setDate(d.getDate() + (mouseDownScrolledHorisColumn.Number - 1) * 7 + verticalColumn.Number - 1);
					};
					var fullFromDate = d;
					var fullTillDate = d;

					CreateTimeTill(ttill,tfrom);
					CreateTimeFrom(tfrom);

					var editevent_form_data = {
						subject 	: '',
						timeFrom	: tfrom,
						timeTill	: ttill,
						fullFromDate: fullFromDate,
						fullTillDate: fullTillDate,
						description	: '',
						event_id	: 0 // if 0 - new event
					};

					ShowDiv(editevent_form_data);
				}
			}
		}
	}
}


function getColorFromCalendar()
{
	if (typeof(mycache_c.calendars)!="undefined")
	{
		for (var i in mycache_c.calendars) 
		{
			if(typeof(mycache_c.calendars[i])!="undefined"){
				var calendar = mycache_c.calendars[i];
				return calendar;
			}
		}
	}
}


function DailyScrollHandler(e)
{
	var e = e || window.event;

	if(OperaDetect()){
		var ev = (e.wheelDelta ) ? e.wheelDelta  : 0;
		iScrollTop = obj_daily.scrollTop + ev ;
		
		if (obj_daily.scrollTop < 120)
		{
			iScrollTop = 0;
		};
		/* Mousewheel DOWN*/
		if (ev > 0 && obj_daily.scrollTop == '0') {
			iScrollTop = ev;
		};
		var version_brouse = window.navigator.appVersion;
		if(version_brouse.substr(0, version_brouse.lastIndexOf(" (")) == '9.00')
			obj_daily.scrollTop = iScrollTop;
	}else{
		iScrollTop = obj_daily.scrollTop;
	};
	ShowScrollArrows();
}

function WeeklyScrollHandler(e)
{
    var e = e || window.event;
	if(OperaDetect())
	{
		var ev = (e.wheelDelta ) ? e.wheelDelta  : 0;
		iScrollTop = obj_weekly.scrollTop + ev ;

		/* Mousewheel UP */
		if (obj_weekly.scrollTop < 120)
		{
			iScrollTop = 0;
		};
		/* Mousewheel DOWN*/
		if (ev > 0 && obj_weekly.scrollTop == '0')
		{
			iScrollTop = ev;
		};
		var version_brouse = window.navigator.appVersion;
		if(version_brouse.substr(0, version_brouse.lastIndexOf(" (")) == '9.00')
			obj_weekly.scrollTop = iScrollTop;
	}else{
		iScrollTop = obj_weekly.scrollTop;
	};
	ShowScrollArrows();
}

function WindowScrollHandler(e)
{
	var e = e || window.event;
	if(OperaDetect())
	{
		windowScrollTop = window.pageYOffset;
	}else{
		windowScrollTop = window.scrollY;
	};
	ShowScrollArrows();
}

function Traverse(item, className)
{
	var res = new Array();
	var resCnt = 0;

	for (var it = item.firstChild;it;it = it.nextSibling)
	{
		if (it.className == className){
			res[resCnt++] = it;
		}
		var res1 = Traverse(it, className);
		var it1;
		for(var i = 0; it1 = res1[i]; i++){
			res[resCnt++] = it1;
		}
	};
	return res;
}

function getEventsPos(events)
{
	var minEventPos2 = -1;
	var maxEventPos2 = 0;
	var event;
	for(var i = 0; event = events[i]; i++)
	{
		var top = event.offsetTop;
		var bottom = top + event.offsetHeight;
		if (maxEventPos2 < bottom)
			maxEventPos2 = bottom;
		if (minEventPos2 > top || minEventPos2 == -1)
			minEventPos2 = top;
	};
	return {minEv:minEventPos2, maxEv:maxEventPos2}
}

function ShowArrows(arrowUp, arrowDown, obj, e)
{
	var top = obj.scrollTop;
	var bottom = top + obj.offsetHeight;
	var w = maxEventPos;
	var v = minEventPos;

	if(navigator.userAgent.indexOf("MSIE") < 0)
	{
		arrowUp = $('arrow_layer_week_'+e).childNodes[1];
		arrowDown = $('arrow_layer_week_'+e).childNodes[3];
	}else
	{
		arrowUp = $('arrow_layer_week_'+e).childNodes[0];
		arrowDown = $('arrow_layer_week_'+e).childNodes[1];
	};
	arrowUp.onclick = function() {scrollArrowUp(v)};
	arrowDown.onclick = function() {scrollArrowDown(w)};

	arrowDown.style.display = bottom < w? 'block' : 'none';

	if(top > v && v!=(-1))
		arrowUp.style.display = 'block';
	else
		arrowUp.style.display = 'none';
}

function eventsInDay(events, left)
{
	var event;
	var res = new Array();
	var resCnt = 0;
	var lef = Math.floor(left - 14.2857);
	left = Math.floor(left);
	for(var i = 0; event = events[i]; i++)
	{
		var even_left = Math.floor(event.style.left.substring(0, event.style.left.length-1));
		if(lef <=even_left && even_left < left)
			res[resCnt++] = event;
	};

	return res;
}
	var masEvents = new Array();

function ShowScrollArrows()
{
	if(view == WEEK) var par = 'week';
	if(view == DAY) var par = 'day';
	var area = $('area_2_' + par);
	var arrow_up = $('arrow_up_' + par);
	var arrow_down = $('arrow_down_' + par);
	var arrow_layer = new Array();

	if(view == DAY)
	{
		if (eventsChanged)
		{
			var vl = 14.2857;
			var events = Traverse(area, "event");
			var val = getEventsPos(events);
			maxEventPos = val.maxEv;
			minEventPos = val.minEv;

			eventsChanged = false;

			arrow_up.onclick = function() {scrollArrowUp(minEventPos)};
			arrow_down.onclick = function() {scrollArrowDown(maxEventPos)};	
		};

		var top = area.scrollTop;
		var bottom = top + area.offsetHeight;

		arrow_down.style.display = bottom < maxEventPos ? 'block' : 'none';
		arrow_up.style.display = (top > minEventPos && minEventPos!=(-1)) ? 'block' : 'none';
		arrow_layer[1]	= $('arrow_layer_day');
	};
	if(view == WEEK)
	{
		if (eventsChanged)
		{
			var vl = 14.2857;
			var events = Traverse(area, "event");
			for(var e = 1; e < 8; e++)
			{
				masEvents[e] = eventsInDay(events, vl);
				vl += 14.2857;
			};
			eventsChanged = false;
		};
		for(var e = 1; e < 8; e++)
		{
			var val = getEventsPos(masEvents[e]);
			maxEventPos = val.maxEv;
			minEventPos = val.minEv;
			ShowArrows(arrow_up, arrow_down, area, e);
		};
		for(var i = 1; i<8; i++) arrow_layer[i] = $('arrow_layer_week_'+i);
	};
	if (arrow_layer !== null && arrow_layer.length!=0) {
		for (var i = 1; resCntArrow = arrow_layer[i]; i++){
			resCntArrow.style.height = area.clientHeight + 'px';
		};
	};
}

function RecalcScrollArrows()
{
	eventsChanged = true;
	ShowScrollArrows();
}
var WorkAreaHorCursor, WorkAreaVertCursor, WorkAreaScrolledHorCursor, EndScrolledHorColumn;
function moveDot(cursor) {
	if(typeof(area) != "undefined") {
		if (area.resizing)
		{
			area.style.cursor = 'n-resize';   
			ev_mes.className = 'event_selected';
			if(!cursor) var cursor = window.event;
			coor_event = cursor.clientY - WorkAreaOffsetTop;
			resize_event(coor_event, mouseDownWorkAreaHorCursor);
		}
		else
		{
			if (area.skip) return;
			if(!cursor) var cursor = window.event;
			WorkAreaHorCursor = cursor.clientY - WorkAreaOffsetTop; //global variable - coorY
   
			WorkAreaVertCursor = cursor.clientX;
			if (typeof WorkAreaOffsetLeft == "undefined") 
				return;
			else {
				WorkAreaVertCursor -= WorkAreaOffsetLeft
			};

			WorkAreaScrolledHorCursor = cursor.clientY - WorkAreaOffsetTop;//areaY;
			if (typeof iScrollTop != "undefined") WorkAreaScrolledHorCursor += iScrollTop;
			if (typeof windowScrollTop != "undefined") WorkAreaScrolledHorCursor += windowScrollTop-3; //coorYY = y1;

			if (!area.pres) return;
			EndScrolledHorColumn = getHorisontalColumn(Math.round((WorkAreaScrolledHorCursor*27)/27)); //position of bottom border of selected area
			if(mycache_c.calendars != '') Resize(verticalColumn, mouseDownScrolledHorisColumn.Height, EndScrolledHorColumn); 
		}
	}
}

function findPosX(obj) {
	var currleft = 0; 
	if (obj.offsetParent) {
		while (obj.offsetParent) {      
			currleft += obj.offsetLeft;
			obj = obj.offsetParent;
		}
	} else if (obj.x) currleft += obj.x;
	return currleft;
}

function findPosY(obj) {
	var currtop = 0;
	if (obj.offsetParent)
		while (obj.offsetParent) {
			currtop += obj.offsetTop;
			obj = obj.offsetParent;
	}
	else if (obj.y) currtop += obj.y;
	return currtop;
}

var obj_weekly;
var obj_daily;
var iScrollTop = 0;
var windowScrollTop = 0;

//function InitScroll(now)
function InitScroll()
{
	obj_weekly = $('area_2_week');
	obj_weekly.onmousewheel = WeeklyScrollHandler;
	obj_weekly.onscroll = WeeklyScrollHandler;
	window.onscroll = WindowScrollHandler;
	window.onmousewheel = WindowScrollHandler;
	//wdate = now;
}

function areaChoice(numb)
{
	switch(numb)
	{
		case (1):
		  area = $('grid_2d');
		  break;
		case (2):
		  area = $('grid_2w');
		  break;
		case (3):
		  area = $('grid_2_month');
		  break;
		default: 
		  area = $('grid_2w');
	};
	var divSelectArea = $("divCheckPanel");
	if (divSelectArea != undefined)
	{
		divSelectArea.parentNode.removeChild(divSelectArea);
	};
	area.resizing = false;
	area.onmouseover = getCoords;

	RecalcScrollArrows();
	scrollArea();
}

/***/

function getFirstDayWeek(firstdayDate)
{
	now1 = new Date(firstdayDate);
	var d1 = new Date(firstdayDate);
	if (setcache['weekstartson'] == 0) {
		d1.setDate(now1.getDate() - now1.getDay());
	} else { //setcache['weekstartson'] == 1
		d1.setDate(now1.getDate() - now1.getDay() + 1);
	};
	return d1;
}

function getLastDayWeek(lastdayDate)
{
	now2 = new Date(lastdayDate);
	var d1 = new Date(lastdayDate);
	if (setcache['weekstartson']==0) {
		d1.setDate(now2.getDate() - now2.getDay());
	} else {//setcache['weekstartson']==1
		d1.setDate(now2.getDate() - now2.getDay() + 1); 
	};
	var d2 = new Date(d1);
	d2.setDate(d1.getDate() + 6);
	return d2;
}

function DaysDiff(D1, D2) 
{ return Math.round((D1-D2)/864e5) }


function changeMonthWeekends(nowDate)
{
	var day = nowDate.getDay();
	var year = nowDate.getFullYear();
	var month = nowDate.getMonth();
	var date = nowDate.getDate();

	var todayDateFull = new Date();
	var todayMonth = todayDateFull.getMonth();
	var todayYear = todayDateFull.getFullYear();
	var todayDate = todayDateFull.getDate();

	var days_in_month = gdim(month+1, year);
	var dates = getFirstLastDayInMonthView(new Date(year,month,1));
	var d = dates[0];
	var ld = dates[1];

	while(d<=ld)
	{
		var ds = d.getDay();
		var ys = d.getFullYear();
		var ms = d.getMonth();
		var dds = d.getDate();
		if(ds == 0 || ds == 6){
		var cell = $('cel_'+ to8(d));
			if(setcache['showweekends'] == 1){
				cell.className="cell_weekend";
				cell.style.backgroundColor = ((todayDate == dds && todayMonth == ms && todayYear == ys) ? "#ffffe5" :"");
			} else {//setcache['showweekends'] == 0
				cell.className="cell";
				cell.style.backgroundColor = ((todayDate == dds && todayMonth == ms && todayYear == ys) ? "#ffffe5" :"#fcffff");
			}
		};
		d.setDate(d.getDate() + 1);
	}
}

function createWorkdayHours(){

	var top = 0;
	var left = 0;
	var q=0;

	for(var j=1; j<=48; j++){
		var div1 = document.createElement('div');
		div1.className = "hrule";
		div1.style.top = j*4.5 +"ex";
		div1.id = "r"+j;
		$("grid_2w").appendChild(div1);

		var div4 = document.createElement('div');
		div4.className = "hrule";
		div4.style.top = j*4.5 +"ex";
		div4.id = "r"+j;
		$("grid_2d").appendChild(div4);
	};

	for(var i=0; i<=6; i++){
		var div2 = document.createElement('div');
		div2.className = 'vrule';
		div2.id = 'c'+(i+1);
		div2.style.left = left + '%';
		$("grid_2w").appendChild(div2);

		var div3 = document.createElement('div');
		div3.className = "vrule";
		div3.id = 'c'+(i+1);
		div3.style.height = '53px';
		div3.style.left = left+"%";
		$("grid_1w").appendChild(div3);

		left=left+14.2857;
	}
}


function scrollArea()
{
	if(view == WEEK) var par = 'week';
	if(view == DAY) var par = 'day';
	if(view != MONTH){
		var obj = $('area_2_' + par);
		var events = Traverse(obj, "event");
		var val = getEventsPos(events);
		if(val.minEv > (setcache['workdaystarts']*54) || val.minEv == -1){
			obj.scrollTop = setcache['workdaystarts']*54;
		}else{
			obj.scrollTop = val.minEv;
		}
	}
}

function createRheadersPanel()
{
	if($("rhead1") != undefined)
	{
		ChildKill($("rheaders_2"));
		ChildKill($("rheaders_1"));
	};

	for(var n=1; n<=4; n++){
		for(var i=0; i<=48; i++){
			if($("notworkday_"+i) != undefined){
				$("notworkday_"+i).parentNode.removeChild($("notworkday_"+i));
			}
		}
	};

	for(var n=1; n<=2; n++){
		var top = 0;
		var left = 0;
		var q=0;

		for(var j=0; j<=23; j++){
			var div4 = document.createElement('div');
			top = j*9;
			if(setcache['showworkday'] == 1 && (setcache['workdaystarts'] > q || q >= setcache['workdayends']))
			{
				var div11 = document.createElement('div');
				div11.className = "notworkday";
				div11.style.top = q*9 +"ex";
				div11.id = "notworkday_" + j;
				$("grid_2w").appendChild(div11);

				var div44 = document.createElement('div');
				div44.className = "notworkday";
				div44.style.top = q*9 +"ex";
				div44.id = "notworkday_" + j;
				$("grid_2d").appendChild(div44);
			};
			var div2 = document.createElement('div');

			if(setcache['timeformat'] == 1)
			{
				div2.innerHTML = ((q <= 12)? ((q == 0)? '12' : q) : q - 12)+(q<12 ? " AM" : " PM" );
			} else {//setcache['timeformat'] == 2
				div2.innerHTML = ((q<10)? '0'+q : q) + ":00";
			};

			if(setcache['showworkday'] == 1 && (setcache['workdaystarts'] > q || q >= setcache['workdayends']))
			{
				div4.className = "rhead_notworkday";
			} else
				div4.className = "rhead";
			if(q == 0)
			{
				div4.style.borderTop = '1px solid #fff';
			};
			div4.style.top = top +"ex";
			div4.id = "rhead"+(j);
			div4.appendChild(div2);

			$("rheaders_"+n).appendChild(div4);
			q++;
		}
	}
}

function fillWeekHeaders(now)
{
	var this_date = new Date();
	var this_date_8format = to8(this_date);
	var s = new Date(now);
	var left = 0;
	if (setcache['weekstartson'] == 0) {
		var daysbefore = s.getDay();
		var rew = new Date(s.setDate(s.getDate() - daysbefore));
	} else {//setcache['weekstartson'] == 1
		var daysbefore = (s.getDay()==0)?7:s.getDay();
		var rew = new Date(s.setDate(s.getDate() - daysbefore + 1));
	};
	var week_headers_container = $("day_headers_week");
	CleanNode(week_headers_container);

	var div1 = document.createElement('div');
	div1.className = "day_headers_outer";
	div1.style.width = "40px";
	div1.style.left = "-40px";
	var div12 = document.createElement('div');
	div12.className = "day_headers_inner";
	div12.style.height = "24px";
	div1.appendChild(div12);
	week_headers_container.appendChild(div1);

	var div2 = document.createElement('div');
	div2.className = "day_headers_outer";
	div2.style.width = "16px";
	div2.style.left = "100%";
	var div22 = document.createElement('div');
	div22.className = "day_headers_inner";
	div12.style.height = "24px";
	div2.appendChild(div22);
	week_headers_container.appendChild(div2);

	for(var i=0; i<7; i++){ 
		var dw = new Date(s);
		dw.setDate(s.getDate() + i);
		weekdayz[i]=to8(dw);

		var div4 = document.createElement('div');
		div4.className = "day_headers_outer";
		div4.style.left=left+"%";
		div4.setAttribute('id', i+1);

		var div5 = document.createElement('div');
		div5.className = ((this_date_8format==weekdayz[i])?"day_headers_inner_today":"day_headers_inner");

		var div3 = document.createElement('div');

		var span_week = document.createElement('span');
		span_week.setAttribute('unselectable', "on");

		if (dw.getMonth()<9){
			var m = "0"+ (dw.getMonth() + 1);
		} else {
			var m = dw.getMonth() + 1;
		};

		if((dw.getDay() == 6 || dw.getDay() == 0) && setcache['showweekends'] == 1)
		{
			if ($('weekend_2_6') == null || $('weekend_2_0') == null) {
				var weekend_1 = document.createElement('div');
				weekend_1.id = 'weekend_1_'+dw.getDay();
				weekend_1.className = 'weekend_day';
				$('grid_1w').appendChild(weekend_1);
				var weekend_2 = document.createElement('div');
				weekend_2.className = 'weekend_day';
				weekend_2.id = 'weekend_2_'+dw.getDay();
				$('grid_2w').appendChild(weekend_2);
			} else {
				var weekend_1 = $('weekend_1_'+dw.getDay());
				var weekend_2 = $('weekend_2_'+dw.getDay());
			};
			weekend_1.style.left = left+"%"; 
			weekend_2.style.left = left+"%";
		};

		if(setcache['dateformat'] == 1)
		{
			var viewDate = m + "/" + dw.getDate();
		}
		else if (setcache['dateformat'] == 2)
		{
			var viewDate = dw.getDate() + "/" +m;
		}
		else if (setcache['dateformat'] == 3)
		{
			var viewDate = m + "-" + dw.getDate();
		}
		else if (setcache['dateformat'] == 4)
		{
			var viewDate = getNonthNameByNumber(m) + " " + dw.getDate();
		}
		else //setcache['dateformat'] == 5
		{
			var viewDate = dw.getDate() + " " + getNonthNameByNumber(m);
		}

		var tit4 = (getDayShortName(dw.getDay()) + " " + viewDate );
		var text4 = document.createTextNode(tit4);

		span_week.appendChild(text4);
		div5.appendChild(div3);
		div5.appendChild(span_week);
		div4.appendChild(div5);

		week_headers_container.appendChild(div4);
		left=left+14.2857;
	}
}

function createDateTitels(dateObj)
{
	var day = dateObj.getDay();
	var year = dateObj.getFullYear();
	var month = dateObj.getMonth();
	var date = dateObj.getDate();
	var title1 = $("time_title_1"); ChildKill(title1);
	var title2 = $("time_title_2"); ChildKill(title2);
	var title3 = $("time_title_3"); ChildKill(title3);
	
	var monthName = getMonthName(month);
	var tit2 = date + " " + monthName + " " + year;
	var text2=document.createTextNode(tit2);
	var strong_day = document.createElement('strong');
	strong_day.appendChild(text2);
	title1.appendChild(strong_day);
	
//for week
	
	var d1 = new Date(dateObj);
	if (setcache['weekstartson'] == 0) {
		d1.setDate(date - day); 
	} else {//setcache['weekstartson'] == 1
		var day = (day==0)?7:day; 
		d1.setDate(date - day + 1); 
	};
	var d2 = new Date(d1);
	d2.setDate(d1.getDate() + 6); 
	if(d1.getMonth() == d2.getMonth() && d1.getYear() == d2.getYear()){
		var w = getMonthName(d2.getMonth()); 
		var tit3 = d1.getDate() + " - " + d2.getDate() + " " +w + " " + d1.getFullYear();
	}else{
		var t = getMonthName(d1.getMonth()); 
		var q = getMonthName(d2.getMonth()); 
		var tit3 = d1.getDate() + " " + t + " " + d1.getFullYear() + " - " + d2.getDate() + " " + q + " " + d2.getFullYear();   
	};
	var text3 = document.createTextNode(tit3);
	var strong_week = document.createElement('strong');
	strong_week.appendChild(text3);
	title2.appendChild(strong_week);
//for month

	var tit5 = monthName + " " + year; 
	var text5 = document.createTextNode(tit5);
	var strong_month = document.createElement('strong');
	strong_month.appendChild(text5);
	title3.appendChild(strong_month);
	$('toolbar_interval_switch').style.display='block';
}

function getFirstLastDayInMonthView(date)
{
	var d = new Date(getFirstDayWeek(date));
	var days_in_month = gdim(date.getMonth()+1, date.getYear());
	var lastdayDate = new Date(date.getFullYear(),date.getMonth(),days_in_month); //last day of month
	var ld = new Date(getLastDayWeek(lastdayDate));
	var num_week = (DaysDiff(ld, d)+1)/7;

	if(num_week == 5){
		d.setDate(d.getDate() - 7);
	}
	else if(num_week == 4){
		d.setDate(d.getDate() - 7);
		ld.setDate(ld.getDate() + 7);
	};
	return new Array(d, ld);
}
function fillMonthHeaders () {
	var weekdayNames=[Lang.FullDaySunday, Lang.FullDayMonday, Lang.FullDayTuesday, Lang.FullDayWednesday,
	 Lang.FullDayThursday, Lang.FullDayFriday, Lang.FullDaySaturday];
	var span_arr = $('day_headers_month').getElementsByTagName('span');
	var k;
	for (var i=0; i<weekdayNames.length; i++) {
		if (setcache['weekstartson'] == 0) {
			k=i;
		} else {//setcache['weekstartson'] == 1
			k=((i+1)==7)?0:(i+1);
		};
		span_arr[i].innerHTML = weekdayNames[k];
	};
}

function create_div_dynamic(dateObj){
	InitScroll();
	createRheadersPanel();
	createDateTitels(dateObj);

// DAY
//fillDayHeaders
	CleanNode($("dh1"));
	var div1 = document.createElement('div');
	div1.className="day_headers_inner";
	var span = document.createElement('span');
	span.setAttribute('unselectable', "on"); 

	var dayName = getDayName(dateObj.getDay());  
	var tit1 = dayName;
	var text1=document.createTextNode(tit1);
	span.appendChild(text1);  
	div1.appendChild(span);
	$("dh1").appendChild(div1); 
 
//WEEK
	fillWeekHeaders(dateObj);

//MONTH
	fillMonthHeaders();
	var todayDateFull = new Date();
	var todayMonth = todayDateFull.getMonth();
	var todayYear = todayDateFull.getFullYear();
	var todayDate = todayDateFull.getDate();

	ChildKill($('month_cell_container'));
	var dates = getFirstLastDayInMonthView(new Date(dateObj.getFullYear(),dateObj.getMonth(),1));
	var d = dates[0];
	var ld = dates[1];

	var tek_day = 1;
	var pos_left = 0;
	var top = 0;
  
	while(d<=ld)
	{
		var ds = d.getDay();
		var ys = d.getFullYear();
		var ms = d.getMonth();
		var dds = d.getDate();
		var text6 = document.createTextNode(d.getDate());
		var div7 = document.createElement('div');
		if(todayMonth == ms && todayYear == ys && todayDate == dds){
				div7.style.height = "20px";
				div7.style.textAlign = "right";
				div7.style.padding = "0px 4px";
				div7.style.lineHeight = "16px";
				div7.style.borderBottom = "0px";
				div7.style.zIndex = "30";
				div7.style.backgroundColor = "#ffffd7";
			};
		div7.className = (ms==dateObj.getMonth() && ys==dateObj.getFullYear())?"header":"header header_inactiv";
        
		div7.appendChild(text6);
		var div6 = document.createElement('div');
		var cel = 'cel_'+ to8(d);   // +tek_day

		div7.setAttribute('id',cel+'_head');
		div6.setAttribute('id',cel);
		div6.style.top = top+'%';
		if (setcache['weekstartson'] == 0) {
			pos_left = (ds == 0)? 0:(ds*14.2857);
			if (ds==6) top=top+16.6667;
		};

		div6.style.left = pos_left + "%";

		if (setcache['weekstartson'] == 1) {
			pos_left = (ds == 0)? 0:(ds*14.2857);
			if (ds==0) top=top+16.6667;
		};

		if ( (ds == 0 || ds == 6) && (setcache['showweekends'] == 1)) {
	        div6.style.backgroundColor = ((todayDate == dds && todayMonth == ms && todayYear == ys)?"#ffffe5":"");
			div6.className = "cell_weekend";
		} else {
			div6.style.backgroundColor = ((todayDate == dds && todayMonth == ms && todayYear == ys)?"#ffffe5":"#fcffff");
			div6.className = "cell";
		};

		tek_day++;

		div6.appendChild(div7);
		$("month_cell_container").appendChild(div6); // grid_2 -> grid_2_month
		d.setDate(d.getDate() + 1);
	}
}