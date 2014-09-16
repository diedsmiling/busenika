<?php 

//@ob_start('writeJsonResponse');

if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));

require_once(WM_ROOTPATH.'class_settings.php');
require_once(WM_ROOTPATH.'calendar/class_settings.php');
include_once(WM_ROOTPATH.'calendar/db_query.php');
require_once(WM_ROOTPATH.'calendar/lib.php');
require_once(WM_ROOTPATH.'calendar/class_calendar_account.php');
require_once(WM_ROOTPATH.'common/timezones.php');
require_once(WM_ROOTPATH.'common/class_log.php');

$log =& CLog::CreateInstance();

/**
 * @param string $response
 * @return string
 */
function writeJsonResponse($response)
{
	global $log;
	$log->WriteLine('<<< calendar JSON:'."\r\n".$response);
	return $response;
}

$wm_settings = &Settings::CreateInstance();
if (!$wm_settings || !$wm_settings->isLoad || !$wm_settings->IncludeLang()) 
{
	$log->WriteLine('calendar error: Can\'t get settings or language file.');
	exit(getErrorJson(0, ErrorGeneral));
}

$settings = new CalSettings($wm_settings);

SQL::init($settings->DbHost, $settings->DbLogin, $settings->DbPassword, $settings->DbName);

$action = isset($_GET['action']) ? $_GET['action'] : '';
if(isset($_GET['large']) && $_GET['large'] != 'LARGE')
{
	$action = 'large';
}

@session_name('PHPWEBMAILSESSID');
@session_start(); 

$user_id = 0; 
$userArray = array();
$tzone = 0;

$log =& CLog::CreateInstance();
$log->WriteLine('>>> calendar GET:'."\r\n".dumpGet());

if(isset($_SESSION[USER_ID])) 
{
	$user_id = (int) $_SESSION[USER_ID];
	$userArray = SQL::Select($settings->DbPrefix, 'acal_users_data', 'user_id', $user_id);
	if ($userArray && count($userArray) > 0 && count($userArray[0]) > 0)
	{
		$userArray = $userArray[0];
		$tz = isset($userArray['timezone'], $TimeZones[$userArray['timezone']][0]) ? $TimeZones[$userArray['timezone']][0] : null;
		$tzs = ($tz) ? explode(':', $tz) : null;
		if ($tzs && count($tzs) > 1)
		{ 
			$tzone = 60*(intval($tzs[1]) + 60*intval($tzs[0]));
		}
	}
	else
	{
		$user = new CalendarUser();
		$userArray = array('user_id' => $user_id, 
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
				'alltimezones' => $settings->AllTimeZones);
		$user->Id = 0;
		$user->CreateUpdateUserSettings($userArray);
		$tz = isset($userArray['timezone'], $TimeZones[$userArray['timezone']][0]) ? $TimeZones[$userArray['timezone']][0] : null;
		$tzs = ($tz) ? explode(':', $tz) : null;
		if ($tzs && count($tzs) > 1)
		{ 
			$tzone = 60*(intval($tzs[1]) + 60*intval($tzs[0]));
		}
		unset($userArray['user_id']);
	}
}
else
{
	$log->WriteLine('calendar error: '.PROC_SESSION_ERROR);
	exit(getErrorJson(0, PROC_SESSION_ERROR));
}

if (isset($_GET['calendar_id']))
{
	if (!checkCalendarId($settings->DbPrefix, $user_id, (int) $_GET['calendar_id']))
	{
		$log->WriteLine('calendar error: An attempt of unauthorized access to calendar of another user detected. (1-'.$user_id.'-'.$_GET['calendar_id'].')');
		exit(getErrorJson(0, 'An attempt of unauthorized access to calendar of another user detected.'));
	}
}

if (isset($_GET['new_calendar_id']))
{
	if (!checkCalendarId($settings->DbPrefix, $user_id, (int) $_GET['new_calendar_id']))
	{
		$log->WriteLine('calendar error: An attempt of unauthorized access to calendar of another user detected. (2-'.$user_id.'-'.$_GET['new_calendar_id'].')');
		exit(getErrorJson(0, 'An attempt of unauthorized access to calendar of another user detected.'));
	}
}

switch ($action)
{
	case 'get_settings':
		if ($userArray && count($userArray) > 0)
		{
			$response = array();
			foreach ($userArray as $key => $value)
			{
				$keyArray = array('timeformat', 'dateformat', 'showweekends', 
									'workdaystarts', 'workdayends', 'showworkday', 'weekstartson',
									'defaulttab', 'country', 'timezone', 'alltimezones');
				
				if (in_array($key, $keyArray))
				{
					$response[$key] = $value;
				}
			}
			echo json_encode($response);
		}
		else
		{
			$log->WriteLine('calendar error: Can\'t Get User Information.');
			echo getErrorJson(0, ErrorGeneral);
		}
		break;

	case 'update_settings':
		$errorCode = 0;
		if (isset($_GET['timeFormat'], $_GET['dateFormat'], $_GET['showWeekends'], $_GET['workdayStarts'], 
					$_GET['WorkdayEnds'], $_GET['showWorkday'], $_GET['weekstartson'], $_GET['tab'], 
					$_GET['country'], $_GET['TimeZone'], $_GET['AllTimeZones']))
		{
			if(controlData($_GET['timeFormat'], 1, 2) && controlData($_GET['dateFormat'], 1, 5) && controlData($_GET['showWeekends'], 0, 1) && controlData($_GET['workdayStarts'], 0, 23) && controlData($_GET['WorkdayEnds'], 0, 23) && controlData($_GET['showWorkday'], 0, 1) && controlData($_GET['TimeZone'], 1, 307) && controlData($_GET['tab'], 1, 3) && controlData($_GET['AllTimeZones'], 0, 1) && controlCountry($_GET['country']))
			{
				$settingsArray = array('timeformat' => $_GET['timeFormat'],
							 'dateformat' => $_GET['dateFormat'],
							 'showweekends' => $_GET['showWeekends'],
							 'workdaystarts' => $_GET['workdayStarts'],
							 'workdayends' => $_GET['WorkdayEnds'],
							 'showworkday' => $_GET['showWorkday'],
							 'weekstartson' => $_GET['weekstartson'],
							 'defaulttab' => $_GET['tab'],
							 'country' => $_GET['country'],
							 'timezone' => $_GET['TimeZone'],
							 'alltimezones' => $_GET['AllTimeZones']);

				SQL::InsertUpdateByID($settings->DbPrefix, 'acal_users_data', $user_id, 'user_id', $settingsArray);
			}
			else
			{
				$errorCode = 2;
			}
		}
		else
		{
			$errorCode = 1;
		}

		if ($errorCode === 0)
		{
			echo json_encode($settingsArray);
		}
		else
		{
			$log->WriteLine('calendar error: Update Settings Error ('.$errorCode.')');
			echo getErrorJson($errorCode, ErrorGeneral);
		}
		break;

	case 'get_calendars':
		$calendarArray = SQL::SelectCalendars($settings->DbPrefix, $user_id, 0);
		$response = array();

		foreach ($calendarArray as $value)
		{
			$calendar_id = intval($value['calendar_id']); 
			$keyArray = array('calendar_id', 'calendar_name', 'calendar_description', 'calendar_color', 'calendar_active');
			$valArray = array();
			foreach ($value as $key => $val) 
			{
				if (in_array($key, $keyArray))
				{
					$valArray[$key] = $val;
				}
			}
			$response[$calendar_id] = $valArray;
		}
		echo json_encode($response);
		break;

	case 'update_calendar':
		$errorCode = 0;
		if (isset($_GET['calendar_id'], $_GET['color_id']))
		{
			$calendar_id = (int) $_GET['calendar_id'];
			$color_id = (int) $_GET['color_id'];
			
			$name = (isset($_GET['name']) && strlen(trim($_GET['name'])) > 0) ? stripslashes(trim(decode_url($_GET['name']))) : null;
			$content = isset($_GET['content']) ? stripslashes(decode_url($_GET['content'])) : null;
			$active = isset($_GET['active']) ? (bool) $_GET['active'] : null;
	
			if ($calendar_id == 0)
			{
				$calendar_id = SQL::CreateCalendar($settings->DbPrefix, $user_id, $color_id, $name, $content, $active);
			}
			else
			{
				SQL::UpdateCalendar($settings->DbPrefix, $user_id, $calendar_id, $color_id, $name, $content, $active);
			}
		}
		else
		{
			$errorCode = 1;
		}
		
		if  ($errorCode === 0)
		{
			$select = SQL::Select($settings->DbPrefix, 'acal_calendars', 'calendar_id', $calendar_id);
			if ($select && count($select) > 0)
			{
				$keyArray = array('calendar_id', 'calendar_name', 'calendar_description', 'calendar_color', 'calendar_active');
				$valArray = array();
				foreach ($select[0] as $key => $val) 
				{
					if (in_array($key, $keyArray))
					{
						$valArray[$key] = $val;
					}
				}
				echo json_encode($valArray);
			}
			else
			{
				$log->WriteLine('calendar error: Update Calendar Error (2)');
				echo getErrorJson(2, ErrorUpdateCalendar);
			}
		}
		else
		{
			$log->WriteLine('calendar error: Update Calendar Error ('.$errorCode.')');
			echo getErrorJson($errorCode, ErrorUpdateCalendar);
		}
		break;

	case 'delete_calendar':
		$errorCode = 0;
		if (isset($_GET['calendar_id']))
		{
			$calendar_id = (int) $_GET['calendar_id'];
			SQL::DeleteCalendar($settings->DbPrefix, $calendar_id, $user_id);
		}
		else
		{
			$errorCode = 1;
		}
		
		if ($errorCode === 0)
		{
			echo json_encode(array('calendar_id' => $calendar_id));
		}
		else
		{
			$log->WriteLine('calendar error: Delete Calendar Error ('.$errorCode.')');
			echo getErrorJson($errorCode, ErrorDeleteCalendar);
		}
		break;

	case 'delete_event':
		$errorCode = 0;
		if (isset($_GET['event_id'], $_GET['calendar_id']))
		{
			$event_id = (int) $_GET['event_id'];
			$calendar_id = (int) $_GET['calendar_id'];
			
			SQL::DeleteEvent($settings->DbPrefix, $event_id, $calendar_id);
		}
		else
		{
			$errorCode = 1;
		}

		if ($errorCode === 0)
		{
			echo json_encode(array('event_id' => $event_id));
		}
		else
		{
			$log->WriteLine('calendar error: Delete Event Error ('.$errorCode.')');
			echo getErrorJson($errorCode, ErrorDeleteEvent);
		}
		break;

	case 'update_event':
		$errorCode = 0;
		if (isset($_GET['event_id'], $_GET['calendar_id']))
		{
			$event_id = (int) $_GET['event_id'];
			$calendar_id = (int) $_GET['calendar_id'];
			
			if ($event_id == 0)
			{
				if (isset($_GET['name'], $_GET['text'], $_GET['from'], $_GET['till'], $_GET['time_from'], $_GET['time_till'], $_GET['allday']) 
						&& strlen(trim($_GET['name'])) > 0 && strlen(trim($_GET['from'])) > 0 && strlen(trim($_GET['till'])) > 0 
						&& strlen(trim($_GET['time_from'])) > 0 && strlen(trim($_GET['time_till'])) > 0)
				{
					$name = stripslashes(decode_url(trim($_GET['name'])));
					$text = stripslashes(decode_url(trim($_GET['text'])));
					$from = decode_url($_GET['from']);
					$till = decode_url($_GET['till']);
					$time_from = str_replace('-', '', decode_url($_GET['time_from']));
					$time_till = str_replace('-', '', decode_url($_GET['time_till']));
					$allday = (int) $_GET['allday'];
					$priority = 0;
					
					$dbtime_from = tosql(mktime(substr($time_from, 0, 2), substr($time_from, 3, 2), 0, substr($from, 4, 2), substr($from, 6, 2), substr($from, 0, 4)) - $tzone);
					$dbtime_till = tosql(mktime(substr($time_till, 0, 2), substr($time_till, 3, 2), 0, substr($till, 4, 2), substr($till, 6, 2), substr($till, 0, 4)) - $tzone);
					
					$new_event_id = SQL::CreateEvent($settings->DbPrefix, $calendar_id, $name, $text, $dbtime_from, $dbtime_till, $allday, $priority);
					if ($new_event_id)
					{
						$event_id = $new_event_id;
					}
					else
					{
						$errorCode = 1;
					}
				}
				else
				{
					$errorCode = 2;
				}
			}
			else
			{
				if (isset($_GET['till'], $_GET['time_till']))
				{
					$from = (isset($_GET['from']) && strlen(trim($_GET['from'])) > 0) ? decode_url(trim($_GET['from'])) : null;
					$till = decode_url($_GET['till']);
					$time_from = (isset($_GET['time_from']) && strlen(trim($_GET['time_from'])) > 0) ? str_replace('-', '', trim(decode_url($_GET['time_from']))) : null;
					$time_till = str_replace('-', '', decode_url($_GET['time_till']));
					
					$name = (isset($_GET['name']) && strlen(trim($_GET['name'])) > 0) ? stripslashes(decode_url(trim($_GET['name']))) : null;	
					$text = isset($_GET['text']) ? stripslashes(decode_url($_GET['text'])) : null;
					
					$allday = isset($_GET['allday']) ? (int) $_GET['allday'] : null;
					$priority = isset($_GET['priority']) ? (int) $_GET['priority'] : null;
					
					$dbtime_from = (isset($from, $time_from) && strlen($from) == 8 && strlen($time_from) == 5)? tosql(mktime(substr($time_from, 0, 2), substr($time_from, 3, 2), 0, substr($from, 4, 2), substr($from, 6, 2), substr($from, 0, 4)) - $tzone) : null;
					$dbtime_till = (isset($till, $time_till) && strlen($till) == 8 && strlen($time_till) == 5)? tosql(mktime(substr($time_till, 0, 2), substr($time_till, 3, 2), 0, substr($till, 4, 2), substr($till, 6, 2), substr($till, 0, 4)) - $tzone) : null;
					
					$new_calendar_id = isset($_GET['new_calendar_id']) ? (int) $_GET['new_calendar_id'] : null;
					SQL::UpdateEvent($settings->DbPrefix, $event_id, $calendar_id, $name, $text, $dbtime_from, $dbtime_till, $allday, $priority, $new_calendar_id);
				}
				else if(isset($_GET['name']))
				{
					$name = strlen(trim($_GET['name'])) ? stripslashes(decode_url(trim($_GET['name']))) : null;
					if ($name !== null)
					{
						SQL::UpdateEvent($settings->DbPrefix, $event_id, $calendar_id, $name);
					}
				}
				else
				{
					$errorCode = 3;
				}
			}
		}
		else
		{
			$errorCode = 5;
		}

		if ($errorCode === 0)
		{
			$select = SQL::Select($settings->DbPrefix, 'acal_events', 'event_id', $event_id);
			if ($select && count($select) > 0)
			{
				$select[0]['event_timefrom'] = addsql($select[0]['event_timefrom'], $tzone);
				$select[0]['event_timetill'] = addsql($select[0]['event_timetill'], $tzone);
				echo json_encode($select[0]);
			}
			else
			{
				$log->WriteLine('calendar error: Update Event Error (6)');
				echo getErrorJson(6, ErrorUpdateEvent);
			}
		}
		else
		{
			$log->WriteLine('calendar error: Update Event Error ('.$errorCode.')');
			echo getErrorJson($errorCode, ErrorUpdateEvent);
		}
		break;

	case 'get_events':
		if (isset($_GET['from'], $_GET['till']))
		{
			$response = array();
			$from = (int) $_GET['from'];
			$till = (int) $_GET['till'];
			
			if ($till >= $from)
			{
				$eventeArray = SQL::SelectEvents($settings->DbPrefix, todt($from, false, -$tzone), todt($till, true, -$tzone), $user_id);
				foreach ($eventeArray as $event) 
				{
					$event_id = intval($event['event_id']); 
					$response[$event_id] = $event;  
					$response[$event_id]['event_timefrom'] = addsql($event['event_timefrom'], $tzone);
					$response[$event_id]['event_timetill'] = addsql($event['event_timetill'], $tzone);
				}
			}
			echo json_encode($response);
		}
		else
		{
			echo json_encode(array()); 
		}
		break;

	case 'get_year_events':

		if (isset($_GET["date"]))
		{
			$result = SQL::SelectAllYearEvents($settings->DbPrefix, (int) $_GET['date'], $user_id);
			$response = array();
			foreach ($result as $value) 
			{
				$from_timestamp = fromsql($value['event_timefrom']) + $tzone;
				$till_timestamp = fromsql($value['event_timetill']) + $tzone;
				$add_value_timefrom = date('Ymd', $from_timestamp);
				$add_value_timetill = date('Ymd', $till_timestamp);
			
				if ($add_value_timefrom == $add_value_timetill) 
				{ 
					if (!in_array($add_value_timetill, $response))
					{
						array_push($response, $add_value_timetill);
					}
				} 
				else
				{
					$days = floor(($till_timestamp - $from_timestamp)/86400);
					for ($i = 0; $i <= $days; $i++) 
					{
						$add_date = date('Ymd', ($from_timestamp + 86400*$i));
						if (!in_array($add_date, $response)) 
						{
							array_push($response, $add_date);
						}
					}
				}
			}
			echo json_encode($response);
		}
		else
		{
			echo json_encode(array()); 
		}
		break;
	
	default:
		
		$log->WriteLine('calendar error: NULL JSON');
		echo getErrorJson(0, ErrorGeneral);
		break;
}

/**
 * @param int $data
 * @param int $min
 * @param int $max
 * @return bool
 */
function controlData($data, $min, $max)
{
	$data = round($data);
	
	return (isset($min) && isset($max))
				? ($min <= $data && $data <= $max)
				: ($data > 0);
}

/**
 * @param string $country
 * @return bool
 */
function controlCountry($country)
{
	if (!$country) 
	{
		return false;
	}
	if (file_exists(INI_DIR.'/country/country.dat'))
	{
		$fp = @fopen(INI_DIR.'/country/country.dat','r');
		if ($fp)
		{
			while (!feof($fp))
			{
				$str = trim(fgets($fp));
				list($CCode) = split ('-', $str);
				if ($country == $CCode){
					@fclose($fp);
					return true;
				}
			}
			@fclose($fp); 
			return false;
		}
		else
		{
			$log =& CLog::CreateInstance();
			$log->WriteLine('Can\'t read '.INI_DIR.'/country/country.dat file.');
		}
	}
	else
	{
		$log =& CLog::CreateInstance();
		$log->WriteLine('Can\'t read '.INI_DIR.'/country/country.dat file.');
	}
	return false;
}

/**
 * @param string $action
 * @param int $code
 * @param string $reason
 * @return string
 */
function getErrorJson($code, $description)
{
	$array = array(
		'error' => 'true',
		'code' => $code,
		'description' => $description
	);
	return json_encode($array);
}

/**
 * @param string $prefix
 * @param int $user_id
 * @param int $calendar_id
 * @return bool
 */
function checkCalendarId($prefix, $user_id, $calendar_id)
{
	if ($calendar_id === 0) 
	{
		return true;
	}
	
	return SQL::CheckCalendarsByUserId($prefix, $calendar_id, $user_id);
}
