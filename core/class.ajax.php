<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2004 Simbirsk Technologies Ltd. All rights reserved.    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/


//
// $Id: class.ajax.php 10229 2010-07-27 14:21:39Z 2tl $
//

if (!defined('AREA') ) { die('Access denied'); }

class Ajax
{
	private $_result = array();
	public $result_ids = array();
	public $content_type = "application/json";
	public $request_type = NULL;
	const REQUEST_XML = 1;
	const REQUEST_IFRAME = 2;

	/**
	 * Create new Ajax backend object and start output buffer (buffer will be catched in destructor)
	 */
	function __construct()
	{
		$this->result_ids = !empty($_REQUEST['result_ids']) ? explode(',', str_replace(' ', '', $_REQUEST['result_ids'])) : array();

		$this->_result = !empty($_REQUEST['_ajax_data']) ? $_REQUEST['_ajax_data'] : array();

		$this->request_type = (!empty($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == self::REQUEST_IFRAME) ? self::REQUEST_IFRAME : self::REQUEST_XML;

		// Start OB handling early.
		ob_start();

		// Check if headers are already sent (see Content-Type library usage).
		// If true - generate debug message and exit.
		$file = $line = null;
		if (headers_sent($file, $line)) {
			trigger_error(
				"HTTP headers are already sent" . ($line !== null? " in $file on line $line" : "") . ". "
				. "Possibly you have extra spaces (or newlines) before first line of the script or any library. "
				. "Please note that Subsys_Ajax uses its own Content-Type header and fails if "
				. "this header cannot be set. See header() function documentation for details",
				E_USER_ERROR
			);
			exit();
		}
	}

	/**
	 * Convert PHP scalar, array or hash to JS scalar/array/hash.
	 *
	 * @param mixed $a php variable to convert to javascript variable
	 * @return string json notation
	 */
	function php2js($a)
	{
		if (is_null($a)) {
			return 'null';
		}
		if ($a === false) {
			return 'false';
		}
		if ($a === true) {
			return 'true';
		}
		if (is_scalar($a)) {
			$a = addslashes($a);
			$a = str_replace("\n", '\n', $a);
			$a = str_replace("\r", '\r', $a);
			$a = preg_replace('{(</)(script)}i', "$1'+'$2", $a);
			return "'$a'";
		}
		$is_list = true;
		for ($i = 0, reset($a); $i<count($a); $i++, next($a)) {
			if (key($a) !== $i) {
				$is_list = false; break;
			}
		}
		$result = array();
		if ($is_list) {
			foreach ($a as $v) {
				$result[] = self::php2js($v);
			}
			return '[ ' . join(', ', $result) . ' ]';
		} else {
			foreach ($a as $k=>$v) {
				$result[] = self::php2js($k) . ': ' . self::php2js($v);
			}
			return '{ ' . join(', ', $result) . ' }';
		}
	}

	/**
	 * Destructor: cache output and display valid javascript code
	 */
	function __destruct()
	{
		static $called = false;

		if ($called == false && !defined('AJAX_REDIRECT')) {
			$called = true;

			$text = ob_get_clean();
			
			if (!empty($this->result_ids)) {
				foreach ($this->result_ids as $r_id) {
					if (strpos($text, ' id="' . $r_id . '">') !== false) {
						$start = strpos($text, ' id="'.$r_id.'">') + strlen(' id="' . $r_id . '">');
						$end = strpos($text, '<!--' . $r_id . '--></');
						$this->assign_html($r_id, substr($text, $start, $end - $start));
					}
				}

				$text = '';
			}

			$this->assign('notifications' , fn_get_notifications());

			// we call session saving directly
			session_write_close();

			if ($this->request_type == self::REQUEST_XML) {
				header('Content-type: ' . $this->content_type);
				// Return json object
				echo '{text: ' . $this->php2js(trim($text)) . ', data : ' . $this->php2js($this->_result) . '}';
			} else {
				// Return html textarea object
				echo '<textarea>' . fn_html_escape('{text: ' . $this->php2js(trim($text)) . ', data : ' . $this->php2js($this->_result) . '}') . '</textarea>';
			}


		}
	}

	/**
	 * Assign php variable to pass it to javascript
	 *
	 * @param string $var variable name
	 * @param mixed $value variable value
	 * @return nothing
	 */
	function assign($var, $value)
	{
		if($var == 'force_redirection') {
			//convert short urls
			$value = fn_url($value, AREA, 'rel', '&');
		}
		
		$this->_result[$var] = $value;
	}

	/**
	 * Assign html code for javascript backend
	 *
	 * @param string $id html element ID
	 * @param mixed $code html code
	 * @return nothing
	 */
	function assign_html($id, $code)
	{
		$this->_result['html'][$id] = $code;
	}

	function get_assigned_vars()
	{
		return $this->_result;
	}
}
?>