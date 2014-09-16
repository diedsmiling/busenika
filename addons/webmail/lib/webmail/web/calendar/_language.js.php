<?php

	@ob_start();

	header('Content-type: text/javascript; charset=utf-8');
	header('Pragma: cache');
	header('Cache-control: public'); 
	header('Expires: '.gmdate( "D, d M Y H:i:s", time() + 31536000).' GMT');
	
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
		
	@session_name('PHPWEBMAILSESSID');
	@session_start();

	require_once(WM_ROOTPATH.'class_settings.php');
	
	$lang = isset($_GET['lang']) ? $_GET['lang'] : '';
	
	$settings =& Settings::CreateInstance();
	if (!$settings || !$settings->isLoad || !$settings->IncludeLang($lang))
	{
		exit('ERROR');
	}
	
	function quot($jsString)
	{
		$deq = '\'';
		$jsString = str_replace('\\', '\\\\', $jsString);
		if ($deq !== null && strlen($deq) == 1)
		{
			$jsString = str_replace($deq, '\\'.$deq, $jsString);
		}
		return str_replace(array("\r", "\n"), ' ', trim($jsString));
	}
?>var Lang = {
	TitleDay: '<?php echo quot(TitleDay);?>',
	TitleWeek: '<?php echo quot(TitleWeek);?>',
	TitleMonth: '<?php echo quot(TitleMonth);?>',

	ErrorNotSupportBrowser: '<?php echo quot(ErrorNotSupportBrowser);?>',
	ErrorTurnedOffActiveX: '<?php echo quot(ErrorTurnedOffActiveX);?>',

	CalendarTableDayMonday: '<?php echo quot(CalendarTableDayMonday);?>',
	CalendarTableDayTuesday: '<?php echo quot(CalendarTableDayTuesday);?>',
	CalendarTableDayWednesday: '<?php echo quot(CalendarTableDayWednesday);?>',
	CalendarTableDayThursday: '<?php echo quot(CalendarTableDayThursday);?>',
	CalendarTableDayFriday: '<?php echo quot(CalendarTableDayFriday);?>',
	CalendarTableDaySaturday: '<?php echo quot(CalendarTableDaySaturday);?>',
	CalendarTableDaySunday: '<?php echo quot(CalendarTableDaySunday);?>',

	FullMonthJanuary: '<?php echo quot(FullMonthJanuary);?>',
	FullMonthFebruary: '<?php echo quot(FullMonthFebruary);?>',
	FullMonthMarch: '<?php echo quot(FullMonthMarch);?>',
	FullMonthApril: '<?php echo quot(FullMonthApril);?>',
	FullMonthMay: '<?php echo quot(FullMonthMay);?>',
	FullMonthJune: '<?php echo quot(FullMonthJune);?>',
	FullMonthJuly: '<?php echo quot(FullMonthJuly);?>',
	FullMonthAugust: '<?php echo quot(FullMonthAugust);?>',
	FullMonthSeptember: '<?php echo quot(FullMonthSeptember);?>',
	FullMonthOctober: '<?php echo quot(FullMonthOctober);?>',
	FullMonthNovember: '<?php echo quot(FullMonthNovember);?>',
	FullMonthDecember: '<?php echo quot(FullMonthDecember);?>',

	ShortMonthJanuary: '<?php echo quot(ShortMonthJanuary);?>',
	ShortMonthFebruary: '<?php echo quot(ShortMonthFebruary);?>',
	ShortMonthMarch: '<?php echo quot(ShortMonthMarch);?>',
	ShortMonthApril: '<?php echo quot(ShortMonthApril);?>',
	ShortMonthMay: '<?php echo quot(ShortMonthMay);?>',
	ShortMonthJune: '<?php echo quot(ShortMonthJune);?>',
	ShortMonthJuly: '<?php echo quot(ShortMonthJuly);?>',
	ShortMonthAugust: '<?php echo quot(ShortMonthAugust);?>',
	ShortMonthSeptember: '<?php echo quot(ShortMonthSeptember);?>',
	ShortMonthOctober: '<?php echo quot(ShortMonthOctober);?>',
	ShortMonthNovember: '<?php echo quot(ShortMonthNovember);?>',
	ShortMonthDecember: '<?php echo quot(ShortMonthDecember);?>',
	
	AltPrevMonth: '<?php echo quot(AltPrevMonth);?>',
	AltNextMonth: '<?php echo quot(AltNextMonth);?>',

	TabDay: '<?php echo quot(TabDay);?>',
	TabWeek: '<?php echo quot(TabWeek);?>',
	TabMonth: '<?php echo quot(TabMonth);?>',

	FullDayMonday: '<?php echo quot(FullDayMonday);?>',
	FullDayTuesday: '<?php echo quot(FullDayTuesday);?>',
	FullDayWednesday: '<?php echo quot(FullDayWednesday);?>',
	FullDayThursday: '<?php echo quot(FullDayThursday);?>',
	FullDayFriday: '<?php echo quot(FullDayFriday);?>',
	FullDaySaturday: '<?php echo quot(FullDaySaturday);?>',
	FullDaySunday: '<?php echo quot(FullDaySunday);?>',
	
	InfoSaving: '<?php echo quot(InfoSaving);?>',

	ButtonCancel: '<?php echo quot(ButtonCancel);?>',
	ButtonSave: '<?php echo quot(ButtonSave);?>',
	
	CalendarHeaderNew: '<?php echo quot(CalendarHeaderNew);?>',
	CalendarHeaderEdit: '<?php echo quot(CalendarHeaderEdit);?>',
	CalendarActionEdit: '<?php echo quot(CalendarActionEdit);?>',
	ConfirmAreYouSure: '<?php echo quot(JS_LANG_ConfirmAreYouSure);?>',
	ConfirmDeleteCalendar: '<?php echo quot(ConfirmDeleteCalendar);?>',
	InfoDeleting: '<?php echo quot(InfoDeleting);?>',
	WarningCalendarNameBlank: '<?php echo quot(WarningCalendarNameBlank);?>',
	ErrorCalendarNotCreated: '<?php echo quot(ErrorCalendarNotCreated);?>',
	WarningSubjectBlank: '<?php echo quot(WarningSubjectBlank);?>',
	WarningIncorrectTime: '<?php echo quot(WarningIncorrectTime);?>',
	WarningIncorrectFromTime: '<?php echo quot(WarningIncorrectFromTime);?>',
	WarningIncorrectTillTime: '<?php echo quot(WarningIncorrectTillTime);?>',
	WarningStartEndDate: '<?php echo quot(WarningStartEndDate);?>',
	WarningStartEndTime: '<?php echo quot(WarningStartEndTime);?>',
	WarningIncorrectDate: '<?php echo quot(WarningIncorrectDate);?>',
	InfoLoading: '<?php echo quot(InfoLoading);?>',
	EventCreate: '<?php echo quot(EventCreate);?>',
	CalendarHideOther: '<?php echo quot(CalendarHideOther);?>',
	CalendarShowOther: '<?php echo quot(CalendarShowOther);?>',
	CalendarRemove: '<?php echo quot(CalendarRemove);?>',
	EventHeaderNew: '<?php echo quot(EventHeaderNew);?>',
	EventHeaderEdit: '<?php echo quot(EventHeaderEdit);?>',
	
	DayToolMonday: '<?php echo  quot(DayToolMonday); ?>',
	DayToolTuesday: '<?php echo  quot(DayToolTuesday); ?>',
	DayToolWednesday: '<?php echo  quot(DayToolWednesday); ?>',
	DayToolThursday: '<?php echo  quot(DayToolThursday); ?>',
	DayToolFriday: '<?php echo  quot(DayToolFriday); ?>',
	DayToolSaturday: '<?php echo  quot(DayToolSaturday); ?>',
	DayToolSunday: '<?php echo  quot(DayToolSunday); ?>',

	ErrorLoadCalendar: '<?php echo  quot(ErrorLoadCalendar); ?>',
	ErrorLoadEvents: '<?php echo  quot(ErrorLoadEvents); ?>',
	ErrorUpdateEvent: '<?php echo  quot(ErrorUpdateEvent); ?>',
	ErrorDeleteEvent: '<?php echo  quot(ErrorDeleteEvent); ?>',
	ErrorUpdateCalendar: '<?php echo  quot(ErrorUpdateCalendar); ?>',
	ErrorDeleteCalendar: '<?php echo  quot(ErrorDeleteCalendar); ?>',
	ErrorGeneral: '<?php echo  quot(ErrorGeneral); ?>'
}