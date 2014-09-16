<?php

	define('ErrorDesc', 'ErrorDesc');
	define('MEMORYLIMIT', '200M');
	define('TIMELIMIT', 3000);
	define('DEFAULT_SKIN', 'Hotmail_Style');
	define('SESSION_LANG', 'session_lang');
	define('MAILADMLOGIN', 'mailadm');
	define('WMVERSION', '4.2.21');
	
	define('ACCOUNT_ID', 'id_account');
	define('USER_ID', 'AUserId');
	
	define('DUMMYPASSWORD', '1111111111111111111111');
	
	define('USEIMAPTRASH', false);
	
	if (!defined('INFORMATION')) define('INFORMATION', 'information');
	if (!defined('ISINFOERROR')) define('ISINFOERROR', 'infoErr');
	
	define('DEMO_SES', 'demoses');
		define('DEMO_S_ContactsPerPage', 'contactsperpage');
		define('DEMO_S_MessagesPerPage', 'messagesperpage');
		define('DEMO_S_AllowDhtmlEditor', 'allowdhtmleditor');
		define('DEMO_S_DefaultSkin', 'defaultskin');
		define('DEMO_S_DefaultOutCharset', 'defaultoutcharset');
		define('DEMO_S_DefaultTimeZone', 'defaulttimezone');
		define('DEMO_S_DefaultLanguage', 'defaultlanguage');
		define('DEMO_S_DefaultDateFormat', 'defaultdateformat');
		define('DEMO_S_DefaultTimeFormat', 'defaulttimeformat');
		define('DEMO_S_ViewMode', 'viewmode');
	
	$CHARSETS = array(
		array('-1', 'Standard'),
		array('iso-8859-6', 'Arabic Alphabet (ISO)'),
		array('windows-1256', 'Arabic Alphabet (Windows)'),
		array('iso-8859-4', 'Baltic Alphabet (ISO)'),
		array('windows-1257', 'Baltic Alphabet (Windows)'),
		array('iso-8859-2', 'Central European Alphabet (ISO)'),
		array('windows-1250', 'Central European Alphabet (Windows)'),
		array('euc-cn', 'Chinese Simplified (EUC)'), //'51936'
		array('gb2312', 'Chinese Simplified (GB2312)'), // '936'
		array('big5', 'Chinese Traditional (Big5)'),
		array('iso-8859-5', 'Cyrillic Alphabet (ISO)'),
		array('koi8-r', 'Cyrillic Alphabet (KOI8-R)'),
		array('windows-1251', 'Cyrillic Alphabet (Windows)'),
		array('iso-8859-7', 'Greek Alphabet (ISO)'),
		array('windows-1253', 'Greek Alphabet (Windows)'),
		array('iso-8859-8', 'Hebrew Alphabet (ISO)'),
		array('windows-1255', 'Hebrew Alphabet (Windows)'),
		array('iso-2022-jp', 'Japanese'),
		array('shift-jis', 'Japanese (Shift-JIS)'),
		array('euc-kr', 'Korean (EUC)'),
		array('iso-2022-kr', 'Korean (ISO)'),
		array('iso-8859-3', 'Latin 3 Alphabet (ISO)'),
		array('windows-1254', 'Turkish Alphabet'),
		array('utf-7', 'Universal Alphabet (UTF-7)'),
		array('utf-8', 'Universal Alphabet (UTF-8)'),
		array('windows-1258', 'Vietnamese Alphabet (Windows)'),
		array('iso-8859-1', 'Western Alphabet (ISO)'),
		array('windows-1252', 'Western Alphabet (Windows)')
	);
	
	$TIMEZONE = array(
		'Default',
		'(GMT -12:00) Eniwetok, Kwajalein, Dateline Time',
		'(GMT -11:00) Midway Island, Samoa',
		'(GMT -10:00) Hawaii',
		'(GMT -09:00) Alaska',
		'(GMT -08:00) Pacific Time (US & Canada); Tijuana',
		'(GMT -07:00) Arizona',
		'(GMT -07:00) Mountain Time (US & Canada)',
		'(GMT -06:00) Central America',
		'(GMT -06:00) Central Time (US & Canada)',
		'(GMT -06:00) Mexico City, Tegucigalpa',
		'(GMT -06:00) Saskatchewan',
		'(GMT -05:00) Indiana (East)',
		'(GMT -05:00) Eastern Time (US & Canada)',
		'(GMT -05:00) Bogota, Lima, Quito',
		'(GMT -04:00) Santiago',
		'(GMT -04:00) Caracas, La Paz',
		'(GMT -04:00) Atlantic Time (Canada)',
		'(GMT -03:30) Newfoundland',
		'(GMT -03:00) Greenland',
		'(GMT -03:00) Buenos Aires, Georgetown',
		'(GMT -03:00) Brasilia',
		'(GMT -02:00) Mid-Atlantic',
		'(GMT -01:00) Cape Verde Is.',
		'(GMT -01:00) Azores',
		'(GMT) Casablanca, Monrovia',
		'(GMT) Dublin, Edinburgh, Lisbon, London',
		'(GMT +01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna',
		'(GMT +01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague',
		'(GMT +01:00) Brussels, Copenhagen, Madrid, Paris',
		'(GMT +01:00) Sarajevo, Skopje, Sofija, Vilnius, Warsaw, Zagreb',
		'(GMT +01:00) West Central Africa',
		'(GMT +02:00) Athens, Istanbul, Minsk',
		'(GMT +02:00) Bucharest',
		'(GMT +02:00) Cairo',
		'(GMT +02:00) Harare, Pretoria',
		'(GMT +02:00) Helsinki, Riga, Tallinn',
		'(GMT +02:00) Israel, Jerusalem Standard Time',
		'(GMT +03:00) Baghdad',
		'(GMT +03:00) Arab, Kuwait, Riyadh',
		'(GMT +03:00) Moscow, St. Petersburg, Volgograd',
		'(GMT +03:00) East Africa, Nairobi',
		'(GMT +03:30) Tehran',
		'(GMT +04:00) Abu Dhabi, Muscat',
		'(GMT +04:00) Baku, Tbilisi, Yerevan',
		'(GMT +04:30) Kabul',
		'(GMT +05:00) Ekaterinburg',
		'(GMT +05:00) Islamabad, Karachi, Sverdlovsk, Tashkent',
		'(GMT +05:30) Calcutta, Chennai, Mumbai, New Delhi, India Standard Time',
		'(GMT +05:45) Kathmandu, Nepal',
		'(GMT +06:00) Almaty, Novosibirsk, North Central Asia',
		'(GMT +06:00) Astana, Dhaka',
		'(GMT +06:00) Sri Jayawardenepura, Sri Lanka',
		'(GMT +06:30) Rangoon',
		'(GMT +07:00) Bangkok, Hanoi, Jakarta',
		'(GMT +07:00) Krasnoyarsk',
		'(GMT +08:00) Beijing, Chongqing, Hong Kong SAR, Urumqi',
		'(GMT +08:00) Irkutsk, Ulaan Bataar',
		'(GMT +08:00) Kuala Lumpur, Singapore',
		'(GMT +08:00) Perth, Western Australia',
		'(GMT +08:00) Taipei',
		'(GMT +09:00) Osaka, Sapporo, Tokyo',
		'(GMT +09:00) Seoul, Korea Standard time',
		'(GMT +09:00) Yakutsk',
		'(GMT +09:30) Adelaide, Central Australia',
		'(GMT +09:30) Darwin',
		'(GMT +10:00) Brisbane, East Australia',
		'(GMT +10:00) Canberra, Melbourne, Sydney, Hobart',
		'(GMT +10:00) Guam, Port Moresby',
		'(GMT +10:00) Hobart, Tasmania',
		'(GMT +10:00) Vladivostok',
		'(GMT +11:00) Magadan, Solomon Is., New Caledonia',
		'(GMT +12:00) Auckland, Wellington',
		'(GMT +12:00) Fiji Islands, Kamchatka, Marshall Is.',
		'(GMT +13:00) Nuku\'alofa, Tonga,'
	);
	
	/**
	 * @return string
	 */
	function getGlobalError()
	{
		return isset($GLOBALS[ErrorDesc]) ? $GLOBALS[ErrorDesc] : '';
	}
	
	/**
	 * @param string $errorString
	 */
	function setGlobalError($errorString)
	{
		$GLOBALS[ErrorDesc]	= $errorString;
	}