<?php
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));

	require_once(WM_ROOTPATH.'common/class_log.php');
	require_once(WM_ROOTPATH.'common/inc_constants.php');

	class DbGeneralSql
	{
		/**
		 * @access private
		 * @var resource
		 */
		var $_conectionHandle;
		
		/**
		 * @access private
		 * @var resource
		 */
		var $_resultId;
		
		/**
		 * @var int
		 */
		var $ErrorCode;

		/**
		 * @var string
		 */
		var $ErrorDesc = '';
		
		/**
		 * @access protected
		 * @var CLog
		 */
		var $_log;
		
	}
	
	class DbSql extends DbGeneralSql
	{
		/**
		 * @access private
		 * @var string
		 */
		var $_host;
		
		/**
		 * @access private
		 * @var string
		 */
		var $_user;
		
		/**
		 * @access private
		 * @var string
		 */
		var $_password;
		
		/**
		 * @access private
		 * @var string
		 */
		var $_dbName;
		
	}