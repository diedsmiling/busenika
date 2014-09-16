<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	require_once(WM_ROOTPATH.'class_settings.php');
	require_once(WM_ROOTPATH.'calendar/class_settings.php');
	require_once(WM_ROOTPATH.'calendar/db_query.php');	

	class CalendarUser
	{
		/**
		 * @var string
		 */
		var $Email;	
		
		/**
		 * @var string
		 */
		var $CalendarPassword;
		
		/**
		 * @var string
		 */
		var $DisplayName;

		/**
		 * @var int => 1-1PM 2-13:00
		 */
		var $TimeFormat;

		/**
		 * @var int 1-m/d/Y 2-d/m/Y 3-Y-m-d
		 */		
		var $DateFormat;

		/**
		 * @var int 0-not 1-show
		 */		
		var $ShowWeekends;
		
		/**
		 * @var int 0-23
		 */
		var $WorkdayStarts;
				
		/**
		 * @var int 0-23
		 */
		var $WorkdayEnds;
		
		/**
		 * @var int 0-23
		 */		
		var $ShowWorkday;
		
		/**
		 * @var int 0-Sunday 1-Monday
		 */
		var $WeekStartsOn;
				
		/**
		 * @var int 1-day 2-week 3-month
		 */
		var $DefaultTab;
		
		/**
		 * @var string
		 */
		var $Country;
			
		/**
		 * @var int
		 */
		var $TimeZone;
		
		/**
		 * @var int 0-do not allow 1-allow
		 */		
		var $AllTimeZones;
				
		/**
		 * @var int
		 */
		var $Id;
		
		var $_settings;
		
		function CalendarUser() {
			$wm_settings =& Settings::CreateInstance();
			if (!$wm_settings || !$wm_settings->isLoad) 
			{
				return false;
			}
			
			$this->_settings = new CalSettings($wm_settings);
		}
	
		/**
		 * @return User
		 */
		function SelectDataUser($id){
			$account = SQL::Select($this->_settings->DbPrefix, "acal_users_data", 'user_id', $id);
			if ($account !== null) {
				$this->Email			= $account[0]['email'];
				$this->CalendarPassword	= $account[0]['password'];
				return $this;
			} else {
				return null;
			}
		}
		
		function CheckUserExist($id)
		{
			$account = SQL::Select($this->_settings->DbPrefix, "acal_users_data", 'user_id', $id);
			return ($account !== null && count($account) > 0);
		}

		/**
		 * @param int $id
		 */
		function SelectAccounts($id)
		{
			$accounts = array();
			$res = SQL::Select($this->_settings->DbPrefix, 'awm_accounts', 'id_user', $id);
			if ($res !== null) 
			{
				for($i = 0, $c = count($res); $i < $c; $i++) 
				{
				 	$accounts[$res[$i]['id_acct']] = $res[$i]['email'];
				}
			}
			return $accounts;
		}
		
		/**
		 * @param int $id
		 * @return array
		 */
		function GetLiteAccountDataByUserId($id)
		{
			$res = SQL::Select($this->_settings->DbPrefix, 'awm_settings', 'id_user', $id);
			if (is_array($res) && count($res) > 0)
			{
				//return array($res[0]['def_skin'], $res[0]['def_lang']);
				return array('cart', $res[0]['def_lang']);
			}
			return array('Hotmail_Style', 'English');
		}
		
		/**
		 * @return User
		 */
		function &SelectDataUserByEmail($email)
		{
			$null = null;
			$res = SQL::Select($this->_settings->DbPrefix, "acal_users_data", 'email', "'".$email."'");
			if (is_array($res) && count($res) > 0) {
				$this->Id = $res[0]['user_id'];
				$this->Email = $res[0]['email'];
				$this->CalendarPassword = $res[0]['password'];
				$this->DisplayName = $res[0]['displayname'];
				return $this;
			} else {
				return $null;
			}
		}
		/**
		 * @param array $array
		 * 
		 */		
		function CreateUpdateUser($Array) {
			SQL::InsertUpdateByID($this->_settings->DbPrefix, "acal_users_data", $this->Id, 'user_id', $Array);
		}
		
		/**
		 * @param int $id
		 */
		function DeleteUser($id)
		{
			$this->Id = $id;
			SQL::InsertUpdateByID($this->_settings->DbPrefix, "a_users", $this->Id, 'id_user', array('deleted'=>'1'));
			SQL::DeleteUserEvents($this->Id);
			SQL::Delete($this->_settings->DbPrefix, "acal_calendars", array('user_id' => $id));
			SQL::Delete($this->_settings->DbPrefix, "acal_users_keys", array('user_id' => $id));
			SQL::Delete($this->_settings->DbPrefix, "acal_users_data", array('user_id' => $id));
	
		}
		
		function CreateUpdateUserSettings($Array) 
		{
			$user_id = $this->Id;
			SQL::InsertUpdateByID($this->_settings->DbPrefix, "acal_users_data", $this->Id, 'user_id', $Array); //insert new user settings
			if ($user_id == 0) 
			{
				SQL::InsertUpdateByID($this->_settings->DbPrefix, "acal_calendars", $user_id, 'calendar_id', array('calendar_name' => CalendarDefaultName, 'calendar_description' => '', 'user_id' => $Array['user_id'], 'calendar_color'=> '1' ,'calendar_active'=> '1' ));

			}
		}
	}
  
?>