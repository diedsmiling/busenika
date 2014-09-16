<?php

if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

require_once(WM_ROOTPATH.'common/inc_constants.php');

session_name('PHPWEBMAILSESSID');
if (!isset($_SESSION[USER_ID]))
{
	@session_start();
	if (!isset($_SESSION[USER_ID]))
	{
		@session_start();
	}
}

if (!isset($_SESSION[USER_ID]) || !isset($_SESSION[ACCOUNT_ID]))
{
	exit('<script type="text/javascript">if (parent) { parent.HideCalendar(\'error\', 1); } else { document.write("session error")}</script>');
}

$id_user = (int) $_SESSION[USER_ID];
$id_acct = (int) $_SESSION[ACCOUNT_ID];

require_once(WM_ROOTPATH.'class_settings.php');
require_once(WM_ROOTPATH.'calendar/class_settings.php');
require_once(WM_ROOTPATH.'calendar/class_calendar_account.php');
require_once(WM_ROOTPATH.'calendar/class_accountmenu.php');

$wm_settings = &Settings::CreateInstance();
if (!$wm_settings || !$wm_settings->isLoad || !$wm_settings->IncludeLang())
{
	exit('<script type="text/javascript">if (parent) { parent.HideCalendar(\'error\', 3); } else { document.write("settings error")}</script>');
}

$settings = new CalSettings($wm_settings);

SQL::init($settings->DbHost, $settings->DbLogin, $settings->DbPassword, $settings->DbName);

$user = new CalendarUser();

if (!$user->CheckUserExist($id_user))
{
		$arr = array('user_id' => $id_user, 
					'timeformat' => $settings->DefaultTimeFormat, 
					'dateformat' => $settings->DefaultDateFormat, 
					'showweekends' => $settings->ShowWeekends,
					'workdaystarts' => $settings->WorkdayStarts,
					'workdayends' => $settings->WorkdayEnds,
					'showworkday' => $settings->ShowWorkDay,
					'weekstartson' => $settings->WeekStartsOn,
					'defaulttab' => $settings->DefaultTab,
					'country' => $settings->DefaultCountry,
					'timezone' => $settings->DefaultTimeZone,
					'alltimezones' => $settings->AllTimeZones
					);
		$user->Id = 0;
		$user->CreateUpdateUserSettings($arr);
}

if (isset($_SESSION[DEMO_SES][DEMO_S_DefaultLanguage]) && isset($_SESSION[DEMO_SES][DEMO_S_DefaultSkin]))
{
	$skin = $_SESSION[DEMO_SES][DEMO_S_DefaultSkin];
	$lang = $_SESSION[DEMO_SES][DEMO_S_DefaultLanguage];
}
else
{
	list($skin, $lang) = $user->GetLiteAccountDataByUserId($id_user);
}

$accountDiv = new AccountDiv($id_user, $id_acct, $skin);

$hideContacts = (!$wm_settings->AllowContacts) ? '' :
	'<span class="wm_accountslist_contacts">
		<a href="#" onclick="parent.HideCalendar(\'contacts\'); return false;">'.JS_LANG_Contacts.'</a>
	</span>';

	@header('Content-type: text/html; charset=utf-8');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<title><?php echo $settings->SiteName;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link type="text/css" rel="stylesheet" href="./calendar/skins/<?php echo $skin; ?>/calendar_styles.css" />
	<link type="text/css" rel="stylesheet" href="./skins/<?php echo $skin; ?>/styles.css" />
	<script type="text/javascript">
		var DAY = 0, WEEK = 1, MONTH = 2;
		var view = WEEK;
		var isLoaded = false;
		var SITE_NAME = "<?php echo $settings->SiteName; ?>";
		var processing_url = "./calendar/processing.php";
	</script>
	<script type="text/javascript" src="./calendar/_language.js.php?lang=<?php echo $lang; ?>"></script>
	<script type="text/javascript" src="./calendar/lib.js"></script>
	<script type="text/javascript" src="./class.common.js"></script>
	<script type="text/javascript" src="./calendar/_functions.js"></script>
	<script type="text/javascript" src="./calendar/_edit_window_dropdowns.js"></script>

	<script type="text/javascript">
		var calendarTableStart, calendarTableEnd, timeSelector;

		function strip(divName, step, currentClass) {
			var even = false;
			var evenClass = currentClass + "_odd";
			var oddClass = currentClass;
			var mainDiv = $(divName);
			var firstClass;
			if (!mainDiv) { return; }
			var divs = mainDiv.getElementsByTagName("div");
			for (var i = 0; i < divs.length;) {
				firstClass = divs[i].getAttributeNode("class").value;
				if (firstClass == currentClass) {
					if (even) {
						divs[i].className = evenClass;
					}
				}
				even = !even;
				i = i+step;
			}
		}
		function change(tmp_var) {
			$('edit_form').style.display='none';
			$('main_block').style.display='block';

			if(tmp_var == undefined) {
				switch (setcache['defaulttab'])
				{
					case '1':
						tmp_var = 1;
					break;
					case '2':
						tmp_var = 2;
					break;
					case '3':
						tmp_var = 3;
					break;
				}
			}

			switch (tmp_var)
			{
				case 1:
					view = DAY;
					obj_daily = $('area_2_day');
					iScrollTop = obj_daily.scrollTop;
					iScrollTop += (typeof windowScrollTop != "undefined" ? windowScrollTop : 0);
					obj_daily.onscroll = DailyScrollHandler;
					obj_daily.onmousewheel = DailyScrollHandler;
					if (parent) parent.WebMail.SetTitle(Lang.TitleDay);
					$('tab_1').className='time_tabs_outer_activ';
					$('tab_2').className='time_tabs_outer';
					$('tab_3').className='time_tabs_outer';
					$('work_area_day').style.display='block';
					$('work_area_week').style.display='none';
					$('work_area_month').style.display='none';
					$('time_title_1').style.display='block';
					$('time_title_2').style.display='none';
					$('time_title_3').style.display='none';
					strip('grid_2d', 1, 'hrule');
					calendarInManager.RefreshCalendarSelector(mydate.getDate(), mydate.getMonth()+1, mydate.getFullYear());
					step = 1;
					areaChoice(1);
					SetMainDivHeight();
					$('grid_2d').onmousedown=function(aEvent) { makevent(aEvent,1,0); }
					$('grid_1d').onmousedown=function(aEvent) { makevent(aEvent,1,1); }
				break;
				case 2:
					view = WEEK;
					iScrollTop = obj_weekly.scrollTop;
					iScrollTop += (typeof windowScrollTop != "undefined" ? windowScrollTop : 0);
					if (parent) parent.WebMail.SetTitle(Lang.TitleWeek);
					$('tab_2').className='time_tabs_outer_activ';
					$('tab_1').className='time_tabs_outer';
					$('tab_3').className='time_tabs_outer';
					$('work_area_day').style.display='none';
					$('work_area_week').style.display='block';
					$('work_area_month').style.display='none';
					$('time_title_1').style.display='none';
					$('time_title_2').style.display='block';
					$('time_title_3').style.display='none';
					strip("grid_2w", 1, 'hrule');
					calendarInManager.RefreshCalendarSelector(mydate.getDate(), mydate.getMonth()+1, mydate.getFullYear());
					step = 7;
					areaChoice(2);
					SetMainDivHeight();
					$('grid_2w').onmousedown=function(aEvent) { makevent(aEvent,1,0); }
					$('grid_1w').onmousedown=function(aEvent) { makevent(aEvent,1,1); }
				break;
				case 3:
					view = MONTH;
					if (parent) parent.WebMail.SetTitle(Lang.TitleMonth);
					$('tab_3').className='time_tabs_outer_activ';
					$('tab_1').className='time_tabs_outer';
					$('tab_2').className='time_tabs_outer';
					$('work_area_day').style.display='none';
					$('work_area_week').style.display='none';
					$('work_area_month').style.display='block';
					$('time_title_1').style.display='none';
					$('time_title_2').style.display='none';
					$('time_title_3').style.display='block';
					calendarInManager.RefreshCalendarSelector(mydate.getDate(), mydate.getMonth()+1, mydate.getFullYear());
					step = 0;
					areaChoice(3);
					SetMainDivHeight();
					ReRenderMonthly();
					$('grid_2_month').onmousedown=function(aEvent) { makevent(aEvent,3,0); }
					iScrollTop = 0;
				break;
				default:
					view = WEEK;
					if (parent) parent.WebMail.SetTitle(Lang.TitleWeek);
					$('tab_2').className='time_tabs_outer_activ';
					$('tab_1').className='time_tabs_outer';
					$('tab_3').className='time_tabs_outer';
					$('work_area_day').style.display='none';
					$('work_area_week').style.display='block';
					$('work_area_month').style.display='none';
					$('time_title_1').style.display='none';
					$('time_title_2').style.display='block';
					$('time_title_3').style.display='none';
					if (view == WEEK) {
						strip("grid_2w", 1, 'hrule');
					} else if (view == DAY) {
						strip('grid_2d', 1, 'hrule');
					}
					step = 7;
					areaChoice(2);
					SetMainDivHeight();
					$('grid_2w').onmousedown=function(aEvent) { makevent(aEvent,2,0); }
					$('grid_1w').onmousedown=function(aEvent) { makevent(aEvent,2,1); }
			}
		}

		function addWindowEventHandler(evnt, handler){
			if (window.addEventListener)
				window.addEventListener(evnt, handler, false);
			else if (window.attachEvent)
				window.attachEvent("on"+evnt, handler);
		}

		function removeWindowEventHandler(evnt, handler){
			if (window.removeEventListener)
				window.removeEventListener(evnt, handler, false);
			else if (window.attachEvent)
				window.detachEvent("on"+evnt, handler);
		}

		function init() {
			if (parent) parent.DisplayCalendarHandler();
			/************** IE 6.0 hack to prevent flickering ************************/
			(function(){
			/*Use Object Detection to detect IE6*/
			var  m = document.uniqueID /*IE*/
			&& document.compatMode  /*>=IE6*/
			&& !window.XMLHttpRequest /*<=IE6*/
			&& document.execCommand ;
			try{
				if(!!m){
					m("BackgroundImageCache", false, true) /* = IE6 only */
				}
			}catch(oh){};
			})();
			/************** IE 6.0 hack to prevent flickering ************************/
			PopupMenu = new CPopupMenus();
			<?php
			
			if ($accountDiv->Count() > 1)
			{
				echo 'PopupMenu.addItem(document.getElementById("popup_menu_1"), document.getElementById("popup_control_1"), "wm_account_menu", document.getElementById("popup_replace_1"), document.getElementById("popup_replace_1"), "", "", "", "");';
			}
			
			?>
			
			var transport = getXMLHTTPRequest();
			if (!transport) {
				document.location = "";
			} else {
				var tbl = $('info_cont');
				BuildInformation(tbl);
				initSettingsCache();
				CreateCalendar();
				window.nowDate = new Date();
				create_div_dynamic(mydate);
				change();
				OperaAlldayScroll();
				fillEvents();
				calendarInManager.RefreshCalendarSelector(mydate.getDate(), mydate.getMonth()+1, mydate.getFullYear());
				SetMainDivHeight();
				ReRenderMonthly();
				scrollArea();
				document.onclick = documentOnClickHandler;
				isLoaded = true;
				HideInfo();
			}
		}
		function documentOnClickHandler(evt) {
			var tgt = window.event ? window.event.srcElement : evt.target;
			var monthsList = $("monthsList");
			var edit_form = $("edit_form");
			if (!monthsList.hidden && !(tgt.id == "monthsList" || tgt.id == "middleTdSelector" || tgt.id == "MonthSelector" || tgt.id == "imgSelector")){
				var middleTdSelector = $("middleTdSelector");
				monthsList.style.display = 'none';
				monthsList.hidden = true;
				middleTdSelector.className = 'middleTdSelector';
			}
			if (edit_form.style.display != "none") {
				var select_box_list = $('edit_select_box_list');
				if (tgt.id != "EventTimeFrom") timeSelectorFrom.Hide();
				if (tgt.id != "EventTimeTill") timeSelectorTill.Hide();
				if (tgt.id != 'calen_sal' && tgt.id != 'calendar_arrow' && select_box_list.style.display != 'none'){
					select_box_list.style.display = 'none';
				};
				if (tgt.id != 'EventDateFrom') {
					var stElements = $('st').getElementsByTagName(tgt.tagName);
					var exist = false;
					if (typeof(stElements) != 'undefined') {
						for (var i=0;i < stElements.length;i++) {
							if (stElements[i] == tgt) {
								exist = true; break;
							}
						}
					};
					if (stElements.length == 0 || !exist) {
						calendarTableStart.Hide();
					}
				};

				if (tgt.id != 'EventDateTill') {
					var enElements = $('en').getElementsByTagName(tgt.tagName);
					var exist = false;
					if (typeof(enElements) != 'undefined') {
						for (var i=0;i < enElements.length;i++) {
							if (enElements[i] == tgt) {
								exist = true; break;
							}
						}
					};
					if (enElements.length == 0 || !exist) {
						calendarTableEnd.Hide();
					}
				}
			}
		}

		function resizer(evt) {
			SetMainDivHeight();
			if (QOpen!=0) moveQuickMenu(); 
			ReRenderMonthly();
			RecalcScrollArrows();
			setMaskHeight();
		}
window.onresize = resizer;
</script>
</head>
<body onload="init();" onclick="PopupMenu.checkShownItems();">

<table class="wm_information" id="info_cont">
	<tr>
		<td class="wm_info_message" id="info_message">
			<?php echo InfoLoading;?>
		</td>
	</tr>
</table>
<span id="allspan">
	<div class="wm_content">
		<textarea style="height:0px; top: -10; left: -10; border: overflow:hidden; position:absolute; visibility:hidden;" id="ev_inline" ></textarea>
		<div id="drager" style="position: absolute; top: 0px; left: 0px; border: 3px solid #cccccc; display:none;"> </div>
		<div id="dbug" style="position: absolute; top: 0px; left: 0px;"></div> 

		<div class="wm_logo" id="logo" tabindex="-1" onfocus="this.blur();">
			<span><?php echo StoreWebmail;?></span>
			<a class="header" target="_top" href="<?php echo !empty($_SESSION['cart_url']) ? $_SESSION['cart_url'] : '../../../../../admin.php'; ?>"><?php echo BackToCart;?></a>
		</div>
		
		<table class="wm_accountslist" id="accountslist">
			<tr>
				<td>
					<?php
					echo $accountDiv->doTitle();
					echo $accountDiv->ToHideDiv();
					echo $hideContacts;
					?>
					<span class="wm_accountslist_calendar">
						<a href="javascript:void(0)"><?php echo Calendar;?></a>
					</span>
					<span class="wm_accountslist_logout">
						<a href="#" onclick="parent.HideCalendar('logout'); return false;"><?php echo JS_LANG_Logout;?></a>
					</span>
					<span id="settings" class="wm_accountslist_settings">
						<a href="#" onclick="parent.HideCalendar('settings'); return false;"><?php echo JS_LANG_Settings;?></a>
					</span>
				</td>
			</tr>
		</table>

		<table class="wm_toolbar cal_toolbar" id="toolbar">
			<tr>
				<td>
					<div style="position:relative; margin:0px;">
						<div id="tab_3" class="time_tabs_outer" style="right:210px;"><div onclick="change(3);"><?php echo TabMonth;?></div></div>
						<div id="tab_2" class="time_tabs_outer" style="right:346px;"><div onclick="change(2);"><?php echo TabWeek;?></div></div>
						<div id="tab_1" class="time_tabs_outer_activ" style="right:482px;"><div onclick="change(1);"><?php echo TabDay;?></div></div>
					</div>
					<div class="wm_toolbar_item" id="toolbar_new_event" onmouseover="if(mycache_c.calendars != '') this.className='wm_toolbar_item_over'" 
						onmouseout="if(mycache_c.calendars != '') this.className='wm_toolbar_item'" 
						onclick="if(mycache_c.calendars != '') {$('calendarColorNumber').value = 0; evform_create();}">
						<img src="./calendar/skins/<?php echo $skin; ?>/menu/new_event.gif" alt="<?php echo AltNewEvent;?>" title="<?php echo AltNewEvent;?>"/>
						<span><?php echo ToolNewEvent;?></span>
					</div>
					<div class="wm_toolbar_item" id="toolbar_back" onmouseover="this.className='wm_toolbar_item_over'" 
							onmouseout="this.className='wm_toolbar_item'" onclick="HideSettings(); ReRenderMonthly();" style="display:none">
						<img src="./calendar/skins/<?php echo $skin; ?>/menu/back.gif" alt="<?php echo AltBack;?>" title="<?php echo AltBack;?>"/>
						<span><?php echo ToolBack;?></span>
					</div>
					<div class="wm_toolbar_item" id="toolbar_today" onmouseover="this.className='wm_toolbar_item_over'" 
						onmouseout="this.className='wm_toolbar_item'" onclick="mydate=new Date();dt=String(to8(mydate));view=DAY;switch2date(dt);">
						<img src="./calendar/skins/<?php echo $skin; ?>/menu/today.gif" alt="<?php echo AltToday;?>" title="<?php echo AltToday;?>" />
						<span><?php echo ToolToday;?></span>
					</div>

					<div id="toolbar_interval_switch">
						<div style="float:left; width:10px;"></div>
						<span class="wm_toolbar_item" onmouseover="this.className='wm_toolbar_item_over'" 
								onmouseout="this.className='wm_toolbar_item'">
										<div onclick="dateBrowse(-1);" class="calendar_arrow_left"></div>
						</span>

						<div id="time_title_1"></div>
						<div id="time_title_2"></div>
						<div id="time_title_3"></div>

						<span class="wm_toolbar_item" onmouseover="this.className='wm_toolbar_item_over'" 
								onmouseout="this.className='wm_toolbar_item'">
									<div onclick="dateBrowse(1);" class="calendar_arrow_right"></div>
						</span>
					</div>
				</td>
			</tr>
		</table>

		<div class="main_block" id="main_block">
			<div id="upper_indent" class="upper_indent"></div>
			<table width="100%"  border="0" cellspacing="0" cellpadding="0" style="border-collapse:separate; ">
			<tr>
				<td class="mainbody">
					<div id="mainbody">
						<div id="work_area_day">
							<div id="day_headers_day" class="day_headers_day">
										<div class="day_headers_outer" style="width:40px; left: -40px;"><div class="day_headers_inner"></div></div>
										<div class="day_headers_outer" style="left: 0%; width:100%" id="dh1"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
										<div class="day_headers_outer" style="left: 100%; width:16px"><div class="day_headers_inner"></div></div>
								</div>
								<div id="area_1_day">
								<div class="calowner_1">
									<table>
										<tr>
											<td style="width:40px"></td>
											<td>
												<div id="grid_1d" class="grid_1">
													<div style="left:0%; height:100%;" id="c0" class="vrule"></div>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div id="area_2_day">
								<div class="calowner_2">
									<table>
										<tr>
											<td style="width:40px;">
												<div id="rheaders_1" class="rheaders_2"></div>
											</td>
											<td>
												<div id="grid_2d" class="grid_2">
													<div style="left: 0%;" id="c1" class="vrule"></div>
													<div style="top:4.5ex;" id="r1" class="hrule"></div>
													<div style="top:9ex;" id="r2" class="hrule"></div>
													<div style="top:13.5ex;" id="r3" class="hrule"></div>
													<div style="top:18ex;" id="r4" class="hrule"></div>
													<div style="top:22.5ex;" id="r5" class="hrule"></div>
													<div style="top:27ex;" id="r6" class="hrule"></div>
													<div style="top:31.5ex;" id="r7" class="hrule"></div>
													<div style="top:36ex;" id="r8" class="hrule"></div>
													<div style="top:40.5ex;" id="r9" class="hrule"></div>
													<div style="top:45ex;" id="r10" class="hrule"></div>
													<div style="top:49.5ex;" id="r11" class="hrule"></div>
													<div style="top:54ex;" id="r12" class="hrule"></div>
													<div style="top:58.5ex;" id="r13" class="hrule"></div>
													<div style="top:63ex;" id="r14" class="hrule"></div>
													<div style="top:67.5ex;" id="r15" class="hrule"></div>
													<div style="top:72ex;" id="r16" class="hrule"></div>
													<div style="top:76.5ex;" id="r17" class="hrule"></div>
													<div style="top:81ex;" id="r18" class="hrule"></div>
													<div style="top:85.5ex;" id="r19" class="hrule"></div>
													<div style="top:90ex;" id="r20" class="hrule"></div>
													<div style="top:94.5ex;" id="r21" class="hrule"></div>
													<div style="top:99ex;" id="r22" class="hrule"></div>
													<div style="top:103.5ex;" id="r23" class="hrule"></div>
													<div style="top:108ex;" id="r24" class="hrule"></div>
													<div style="top:112.5ex;" id="r25" class="hrule"></div>
													<div style="top:117ex;" id="r26" class="hrule"></div>
													<div style="top:121.5ex;" id="r27" class="hrule"></div>
													<div style="top:126ex;" id="r28" class="hrule"></div>
													<div style="top:130.5ex;" id="r29" class="hrule"></div>
													<div style="top:135ex;" id="r30" class="hrule"></div>
													<div style="top:139.5ex;" id="r31" class="hrule"></div>
													<div style="top:144ex;" id="r32" class="hrule"></div>
													<div style="top:148.5ex;" id="r33" class="hrule"></div>
													<div style="top:153ex;" id="r34" class="hrule"></div>
													<div style="top:157.5ex;" id="r35" class="hrule"></div>
													<div style="top:162ex;" id="r36" class="hrule"></div>
													<div style="top:166.5ex;" id="r37" class="hrule"></div>
													<div style="top:171ex;" id="r38" class="hrule"></div>
													<div style="top:175.5ex;" id="r39" class="hrule"></div>
													<div style="top:180ex;" id="r40" class="hrule"></div>
													<div style="top:184.5ex;" id="r41" class="hrule"></div>
													<div style="top:189ex;" id="r42" class="hrule"></div>
													<div style="top:193.5ex;" id="r43" class="hrule"></div>
													<div style="top:198ex;" id="r44" class="hrule"></div>
													<div style="top:202.5ex;" id="r45" class="hrule"></div>
													<div style="top:207ex;" id="r46" class="hrule"></div>
													<div style="top:211.5ex;" id="r47" class="hrule"></div>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div id="arrow_layer_day" class="arrow_layer" style="bottom:0px; z-index:2000; right:20px; height:500px; width:0px;">
								<div id="arrow_up_day" class="arrow_up" style=" display:none"
									onmouseover="this.className='arrow_up_hover'"
									onmouseout="this.className='arrow_up'"></div>

								<div id="arrow_down_day" class="arrow_down" style=" display:none"
									onmouseover="this.className='arrow_down_hover'"
									onmouseout="this.className='arrow_down'"></div>
							</div>
						</div><!--#work_area_day-->
						<div id="work_area_week">
							<div id="day_headers_week" class="day_headers_week">
							</div>
							<div id="area_1_week">
							 	<div class="calowner_1">
									<table>
										<tr>
											<td style="width:40px;"></td>
											<td>
												<div id="grid_1w" class="grid_1">
													<div style="left:0%; height:100%;" class="vrule"></div>
													<div style="left:14.2857%; height:100%;" class="vrule"></div>
													<div style="left:28.5714%; height:100%;" class="vrule"></div>
													<div style="left:42.8571%; height:100%;" class="vrule"></div>
													<div style="left:57.1429%; height:100%;" class="vrule"></div>
													<div style="left:71.4286%; height:100%;" class="vrule"></div>
													<div style="left:85.7143%; height:100%;" class="vrule"></div>
													<!--div class="select_area" style="width: 14.2857%; left: 14.2857%; top: 0ex; height: 53px;"></div-->
													<div id="current_day_1" class="current_day" style="left: 28.5714%; height:100%;"></div>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div id="area_2_week">
								<div class="calowner_2">
									<table>
										<tr>
											<td style="width:40px">
												<div id="rheaders_2" class="rheaders_2"></div>
											</td>
											<td>
												<div id="grid_2w" class="grid_2">
														<div style="left: 0%;" id="c1" class="vrule"></div>
														<div style="left: 14.2857%;" id="c2" class="vrule"></div>
														<div style="left: 28.5714%;" id="c3" class="vrule"></div>
														<div style="left: 42.8571%;" id="c4" class="vrule"></div>
														<div style="left: 57.1429%;" id="c5" class="vrule"></div>
														<div style="left: 71.4286%;" id="c6" class="vrule"></div>
														<div style="left: 85.7143%;" id="c7" class="vrule"></div>
														<div style="top:4.5ex;" id="r1" class="hrule"></div>
														<div style="top:9ex;" id="r2" class="hrule"></div>
														<div style="top:13.5ex;" id="r3" class="hrule"></div>
														<div style="top:18ex;" id="r4" class="hrule"></div>
														<div style="top:22.5ex;" id="r5" class="hrule"></div>
														<div style="top:27ex;" id="r6" class="hrule"></div>
														<div style="top:31.5ex;" id="r7" class="hrule"></div>
														<div style="top:36ex;" id="r8" class="hrule"></div>
														<div style="top:40.5ex;" id="r9" class="hrule"></div>
														<div style="top:45ex;" id="r10" class="hrule"></div>
														<div style="top:49.5ex;" id="r11" class="hrule"></div>
														<div style="top:54ex;" id="r12" class="hrule"></div>
														<div style="top:58.5ex;" id="r13" class="hrule"></div>
														<div style="top:63ex;" id="r14" class="hrule"></div>
														<div style="top:67.5ex;" id="r15" class="hrule"></div>
														<div style="top:72ex;" id="r16" class="hrule"></div>
														<div style="top:76.5ex;" id="r17" class="hrule"></div>
														<div style="top:81ex;" id="r18" class="hrule"></div>
														<div style="top:85.5ex;" id="r19" class="hrule"></div>
														<div style="top:90ex;" id="r20" class="hrule"></div>
														<div style="top:94.5ex;" id="r21" class="hrule"></div>
														<div style="top:99ex;" id="r22" class="hrule"></div>
														<div style="top:103.5ex;" id="r23" class="hrule"></div>
														<div style="top:108ex;" id="r24" class="hrule"></div>
														<div style="top:112.5ex;" id="r25" class="hrule"></div>
														<div style="top:117ex;" id="r26" class="hrule"></div>
														<div style="top:121.5ex;" id="r27" class="hrule"></div>
														<div style="top:126ex;" id="r28" class="hrule"></div>
														<div style="top:130.5ex;" id="r29" class="hrule"></div>
														<div style="top:135ex;" id="r30" class="hrule"></div>
														<div style="top:139.5ex;" id="r31" class="hrule"></div>
														<div style="top:144ex;" id="r32" class="hrule"></div>
														<div style="top:148.5ex;" id="r33" class="hrule"></div>
														<div style="top:153ex;" id="r34" class="hrule"></div>
														<div style="top:157.5ex;" id="r35" class="hrule"></div>
														<div style="top:162ex;" id="r36" class="hrule"></div>
														<div style="top:166.5ex;" id="r37" class="hrule"></div>
														<div style="top:171ex;" id="r38" class="hrule"></div>
														<div style="top:175.5ex;" id="r39" class="hrule"></div>
														<div style="top:180ex;" id="r40" class="hrule"></div>
														<div style="top:184.5ex;" id="r41" class="hrule"></div>
														<div style="top:189ex;" id="r42" class="hrule"></div>
														<div style="top:193.5ex;" id="r43" class="hrule"></div>
														<div style="top:198ex;" id="r44" class="hrule"></div>
														<div style="top:202.5ex;" id="r45" class="hrule"></div>
														<div style="top:207ex;" id="r46" class="hrule"></div>
														<div style="top:211.5ex;" id="r47" class="hrule"></div>
														<!--div style="top:216ex;" id="r48" class="hrule"></div>
														<div style="top:220.5ex;" id="r49" class="hrule"></div-->

														<!--div class="select_area" style="left: 14.2857%; top:63ex; width:14.2857%; height:18ex;"></div-->
														<div id="current_day_2" class="current_day" style="left: 28.5714%; height:216ex;"></div>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>

							<div style="position:absolute; bottom:0px; height:0px; right:0px; width:100%">
								<div style="margin:0px 16px 0px 40px;  width:auto; ">
									<div style=" width:100%; position:relative;">

										<div id="arrow_layer_week_1" class="arrow_layer" style="bottom:0px;  z-index:15; left:14.2857%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>

										<div id="arrow_layer_week_2" class="arrow_layer" style="bottom:0px;  z-index:15; left:28.5714%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>

										<div id="arrow_layer_week_3" class="arrow_layer" style="bottom:0px;  z-index:15; left:42.8571%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none;"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>

										<div id="arrow_layer_week_4" class="arrow_layer" style="bottom:0px;  z-index:15; left:57.1429%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>

										<div id="arrow_layer_week_5" class="arrow_layer" style="bottom:0px;  z-index:15; left:71.4286%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>

										<div id="arrow_layer_week_6" class="arrow_layer" style="bottom:0px;  z-index:15; left:85.7143%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none;"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>

										<div id="arrow_layer_week_7" class="arrow_layer" style="bottom:0px;  z-index:15; left:100%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>
									</div>
								</div>
							</div>

						</div><!--#work_area_week-->
						<div id="work_area_month">
							<div id="day_headers_month" class="day_headers_month" style="margin:0px; ">
									<div class="day_headers_outer" style="left: 0%;" id="dh1"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
									<div class="day_headers_outer" style="left: 14.285%;" id="dh2"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
									<div class="day_headers_outer" style="left: 28.5714%;" id="dh3"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
									<div class="day_headers_outer" style="left: 42.8571%;" id="dh4"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
									<div class="day_headers_outer" style="left: 57.1429%;" id="dh5"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
									<div class="day_headers_outer" style="left: 71.4286%;" id="dh6"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
									<div class="day_headers_outer" style="left: 85.7143%;" id="dh7"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
							</div>
							<div id="area_2_month">
									<div class="calowner_2">
											<div id="grid_2_month" class="grid_2_month" style=" height:100%">
													<div style="left: 14.2857%; height: 100%;" id="c0" class="vrule"></div>
													<div style="left: 28.5714%; height: 100%;" id="c1" class="vrule"></div>
													<div style="left: 42.8571%; height: 100%;" id="c2" class="vrule"></div>
													<div style="left: 57.1429%; height: 100%;" id="c3" class="vrule"></div>
													<div style="left: 71.4286%; height: 100%;" id="c4" class="vrule"></div>
													<div style="left: 85.7143%; height: 100%;" id="c5" class="vrule"></div>
													<div id="month_cell_container"></div>
											</div>
									</div>
							</div>
					</div><!--#work_area_month-->
					</div><!--#mainbody-->
				</td>
				<td  class="spacer">&nbsp;</td>
				<td id="right" class="right">
					<div class="calendar_header" id="calhead1">
						<div class="calendar_header_text"><?php echo CalendarHeader;?></div>
						<div class="arrow"></div>
					</div>
					
					<div class="small_calendars" id="mini_calendar_box"></div>
					
					<div class="calendar_manager" id="manager_box" style="position:relative">
						<div class="calendar_header" id="calhead2">
							<div class="calendar_header_text"><?php echo CalendarsManager;?></div>
							<div class="arrow"></div>
						</div>
						<div id='manager_list' style='height: 20px; position: relative; overflow: auto; overflow-x: hidden; overflow-y: auto; width: 200px;'>
							<div class="new_calendar"><a href="javascript:void(0);"  onclick="manager_form_create();"><span>+</span>&nbsp;<?php echo CalendarActionNew;?></a></div>
						</div>
						<div id="quick_edit" class="event edit_gray" style="width: 114px; text-indent: 2px; margin-left:0px;margin-top:0px; left: 35px; right:0px; z-index: 1024;"></div>
					</div>
				</td>
			</tr>
		</table>
			<div id="lower_indent" class="lower_indent"></div>
		</div><!--#main_block-->

		<div id="edit_window">
			<div class="mask"></div>
			<!-- edit form -->
			<div id="edit_form" class="edit_form" style="top:50%; left:50%;">
				<div style="height:auto; position:relative;left:-195px; top: -125px;">
					<input type="hidden" name="evform_id" id="evform_id" value="0" />
					<input type="hidden" id="id_calendar" value="0" />
					<div class="event_black">
						<div class="a"></div>
						<div class="b"></div>
						<div class="event_middle"></div>
						<div class="b"></div>
						<div class="a"></div>
					</div>
					<div class="eventcontainer_2" id="eventcontainer" style="position:relative;">
						<div class="event_edit">
						<div class="a"></div>
						<div class="b"></div>
						<div class="event_middle" style="">
							<div class="event_text" style="">
								<div class="time" id="ef_fulldate"><?php echo EventHeaderNew;?></div>
								<div class="text">
									<div class="em_spacer"></div>
									<form onsubmit="">
										<div class="row_title"><?php echo EventSubject;?></div>
										<div><input id="EventSubject" class="input" name="EventSubject" style="width:338px;" maxlength="50" tabIndex="1" /></div>
										<div class="em_spacer"></div>
										<div class="row_title"><?php echo EventCalendar;?></div>
										<div class="eventcontainer_bw" id="edit_select_box" style="top:4em; left:68px; z-index:3001;">
											<div class="a"></div>
											<div class="b"></div>
											<div class="event_middle">
												<div class="calendar_text" style="cursor:pointer; width:136px; height:100%;">
													<div id="color_calendar_now" class="color_pick" style="border: 1px solid #fff; margin:2px 0px 0px 1px; background-color:#FFFFFF;"></div>
													<div id="calen_sal" class="text" onclick="ShowHideEditWinSelectList();"></div>
													<div id="calendar_arrow" style="right:0px; border-left:1px solid #696969;" class="vis_check" onclick="ShowHideEditWinSelectList();"></div>
												</div>
											</div>
											<div class="b"></div>
											<div class="a"></div>
										</div>
										<div style="position:absolute;left:5.7em" id="edit_select_box_list"></div>
										<div class="em_spacer" style="height:0px;"></div>
										<table border="0" cellpadding="0" cellspacing="0" style="float:left;margin-top:0.6em" >
											<tr>
												<td id="tmp_id" class="row_title"><?php echo EventFrom;?></td>
												<td>
													<input id="EventTimeFrom" name="EventTimeFrom" style="display:block;width:60px; border:1px solid #696969;color: #696969; text-align: center; margin-right:4px; padding:0px" value="" tabIndex="2" />
													<div style="position:absolute;" id="EventTimeFrom_dropdown"></div>
												</td>
												<td>
													<input id="EventDateFrom" name="EventDateFrom" style="display:block;width:74px; border:1px solid #696969;color: #696969; text-align: center;padding:0px" value="" tabIndex="3" />
													<div style="position:absolute;margin-top:-1px;" id="st"></div>
												</td>
											</tr>
										</table>
										<table border="0" cellpadding="0" cellspacing="0" style="float:left;margin-top:0.6em">
											<tr>
												<td class="row_title" style="text-align:center; float:none"><?php echo EventTill;?></td>
												<td>
													<input id="EventTimeTill" name="EventTimeTill" style="display:block;width:60px; border:1px solid #696969;color: #696969; text-align: center; margin-right:4px;padding:0px" value="" tabIndex="4" />
													<div style="position:absolute;" id="EventTimeTill_dropdown"></div>
												</td>
												<td>
													<input id="EventDateTill" name="EventDateTill" style="display:block; width:74px; border:1px solid #696969;color: #696969; text-align: center;padding:0px" value="" tabIndex="5" />
													<div style="position:absolute;overflow:visible;margin-top:-1px;" id="en"></div>
												</td>
											</tr>
										</table>
										<div class="em_spacer"></div>
										<div class="row_title"><?php echo CalendarDescription;?></div>
										<div><textarea name="EventDescription" id="EventDescription" style="background-color:white; color:#696969; border:1px solid #696969; width:338px; height:4em; padding-left:3px;" tabIndex="6" onfocus='calendarTableStart.Hide();calendarTableEnd.Hide();'></textarea></div>
										<div class="em_spacer" style="height:0px"></div>
										<div class="row_title" style="height:3em;"></div>
										<input type="button" style="display:inline; margin:0.6em 10px 0px 0px;" id="button_save" value="<?php echo ButtonSave;?>" class="wm_button" onclick=" evform_submit();" tabIndex="7" />&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="button" style="display:inline; margin:0.6em 10px 0px 0px;" value="<?php echo ButtonCancel;?>" class="wm_button" onclick="$('edit_window').style.display='none'; evform_cancel();" tabIndex="8" />&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="button" style="display:inline; margin:0.6em 10px 0px 0px;" name="delbut" id="delbut" value="<?php echo ButtonDelete;?>" class="wm_button" onclick="evform_delete();" tabIndex="9" />
										<div class="em_spacer"></div>
									</form>
									<br>
								</div>
							</div>
						</div>
						<div class="b"></div>
						<div class="a"></div>
					</div>
					</div>
				</div>
			</div>
		</div><!--#edit_window-->

		<div id="manager_window">
			<div class="mask"></div>
			<!-- manager form -->
			<div id="manager_form" class="edit_form">
				<div style="height:auto; position:relative;left:-195px; top: -125px;">
					<input type="hidden" name="clndform_id" id="clndform_id" value="0" />
					<input type="hidden" id="calendarColorNumber" value="0"/>
					<div class="event_black">
							<div class="a"></div>
							<div class="b"></div>
							<div class="event_middle"></div>
							<div class="b"></div>
							<div class="a"></div>
					</div>
					<div class="eventcontainer_2" id="calendarcontainer" style="position:relative">
						<div class="event_edit">
							<div class="a"></div>
							<div class="b"></div>
							<div class="event_middle">
								<div class="event_text">
									<div class="time" style="text-indent:92px;" id="ef_fulldate_calendar"><?php echo CalendarHeaderNew;?></div>
									<div class="text">
										<div class="em_spacer"></div>
										<div class="row_title" style="width:8em"><?php echo CalendarName;?></div>
										<div><input id="CalendarSubject" name="CalendarSubject" onkeypress="saveOnEnter(event);" maxlength="50" class="input" style="width:270px;" tabIndex="1" /></div>
										<div class="em_spacer"></div>
										<div class="row_title" style="width:8em"><?php echo CalendarDescription;?></div>
										<div><textarea name="CalendartDescription" id="CalendarDescription" class="input" style="width:270px; height:50px;" tabIndex="2"></textarea></div>
										<div class="em_spacer"></div>
										<div class="row_title" style="width:8em"><?php echo CalendarColor;?></div>
										<div class="calendar_text" style=" width:105px;border:1px solid #696969; height: 35px;">
											<div id="color_1" class="color_pick" style="background-color: #ef9554;" 
													onclick="SelectColorForNewCalendar(1, '#ef9554', this);"
													onmouseover="this.className='color_pick_hover'" 
													onmouseout="this.className='color_pick'"></div>

											<div id="color_2" class="color_pick" style="background-color: #f58787;"
													onclick="SelectColorForNewCalendar(2, '#f58787', this);"
													onmouseover="this.className='color_pick_hover'" 
													onmouseout="this.className='color_pick'"></div>

											<div id="color_3" class="color_pick" style="background-color: #6fd0ce;"
													onclick="SelectColorForNewCalendar(3, '#6fd0ce', this);"   
													onmouseover="this.className='color_pick_hover'" 
													onmouseout="this.className='color_pick'"></div>

											<div id="color_4" class="color_pick" style="background-color: #90bbe0;"
													onclick="SelectColorForNewCalendar(4, '#90bbe0', this);"
													onmouseover="this.className='color_pick_hover'"
													onmouseout="this.className='color_pick'"></div>

											<div id="color_5" class="color_pick" style="background-color: #baa2f3;"
													onclick="SelectColorForNewCalendar(5, '#baa2f3', this);"
													onmouseover="this.className='color_pick_hover'" 
													onmouseout="this.className='color_pick'"></div>

											<div id="color_6" class="color_pick" style="background-color: #f68bcd;"
													onclick="SelectColorForNewCalendar(6, '#f68bcd', this);"
													onmouseover="this.className='color_pick_hover'" 
													onmouseout="this.className='color_pick'"></div>

											<div id="color_7" class="color_pick" style="background-color: #d987da;"
													onclick="SelectColorForNewCalendar(7, '#d987da', this);"
													onmouseover="this.className='color_pick_hover'" 
													onmouseout="this.className='color_pick'"></div>

											<div id="color_8" class="color_pick" style="background-color: #4affb8;"
													onclick="SelectColorForNewCalendar(8, '#4affb8', this);"
													onmouseover="this.className='color_pick_hover'" 
													onmouseout="this.className='color_pick'"></div>

											<div id="color_9" class="color_pick" style="background-color: #9f9fff;"
													onclick="SelectColorForNewCalendar(9, '#9f9fff', this);"
													onmouseover="this.className='color_pick_hover'" 
													onmouseout="this.className='color_pick'"></div>

											<div id="color_10" class="color_pick" style="background-color: #5cc9c9;"
													onclick="SelectColorForNewCalendar(10, '#5cc9c9', this);"   
													onmouseover="this.className='color_pick_hover'" 
													onmouseout="this.className='color_pick'"></div>

											<div id="color_11" class="color_pick" style="background-color: #76cb76;"
													onclick="SelectColorForNewCalendar(11, '#76cb76', this);"
													onmouseover="this.className='color_pick_hover'" 
													onmouseout="this.className='color_pick'"></div>

											<div id="color_12" class="color_pick" style="background-color: #aec9c9;"
													onclick="SelectColorForNewCalendar(12, '#aec9c9',this);"
													onmouseover="this.className='color_pick_hover'" 
													onmouseout="this.className='color_pick'"></div>
										</div>
										<div class="em_spacer" style="height:0px"></div>
										<div class="row_title" style="width:8em; height:3em;"></div>
										<input type="button" id="button_save" value="<?php echo ButtonSave;?>" class="wm_button" style="display:inline; margin:0.6em 10px 0px 0px;" onclick="manager_form_save(this);" tabIndex="3" />&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="button" value="<?php echo ButtonCancel;?>" class="wm_button" style="display:inline; margin:0.6em 10px 0px 0px;" onclick="manager_form_cancel();" tabIndex="4" />&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="button" name="delbut" id="delbut_calendar" value="<?php echo ButtonDelete;?>" class="wm_button" style="display:inline; margin:0.6em 10px 0px 0px;" onclick="manager_form_delete($('clndform_id').value);" tabIndex="5" />
										<div class="em_spacer"></div>
									</div>
								</div>
							</div>
							<div class="b"></div>
							<div class="a"></div>
						</div><!--#event_edit-->
					</div>
				</div>
			</div><!--#manager_form-->
		</div><!--#manager_window-->

		<div id="confirm_window">
			<div class="mask"></div>
		</div>


	</div><!--#wm_content-->
</span>
</body>
</html>