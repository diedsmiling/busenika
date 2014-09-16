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
// $Id: fn.requests.php 10579 2010-09-02 11:32:23Z zeke $
//

if ( !defined('AREA') )	{ die('Access denied');	}

/**
 * Check if headers were passed using CURL
 *
 * @return mixed
 */
function fn_curl_headers()
{
	static $headers = '';

	$args = func_get_args();
	if (count($args) == 1) {
		$return = '';
		if ($args[0] == true) {
			$return = $headers;
		}
		$headers = '';
		return $return;
	}

	if (trim($args[1]) != '') {
		$headers .= $args[1];
	}
	return strlen($args[1]);
}

/**
 * Performs https request using curl
 *
 * @param string $method request method (post/get)
 * @param string $url request url
 * @param array $data data to pass with request
 * @param string $cookie cookies to pass with request
 * @param string $conttype request content type
 * @param string $referer referer
 * @param string $cert path to ssl cerficate
 * @param string $kcert path to ssl certificate key file
 * @param string $headers additional headers to pass with request
 * @param array $basic_auth username/password for basic authentication
 * @return array response
 */
function fn_https_request($method, $url, $data = array(), $join = '&', $cookie = '', $conttype = 'application/x-www-form-urlencoded', $referer = '', $cert = '', $kcert = '', $headers = '', $basic_auth = array())
{
	$result = array();
	$_curl = fn_check_curl();

	if( $_curl === false ) {
		$result = array('0', 'HTTPS: libcurl is not supported');
	}

	if(($method != 'POST') && ($method != 'GET')) {
		$result = array('0', 'HTTPS: Invalid method');
	}

	if (empty($result)) {
		$headers[] = 'Content-Type: '. addslashes($conttype);

		$ch = curl_init();

		$req_settings = fn_get_request_settings();
		if (!empty($req_settings['proxy_host'])) {
			curl_setopt ($ch, CURLOPT_PROXY, $req_settings['proxy_host'] . ':' . (empty($req_settings['proxy_port']) ? 3128 : $req_settings['proxy_port']));
			if (!empty($req_settings['proxy_user'])) {
				curl_setopt ($ch, CURLOPT_PROXYUSERPWD, $req_settings['proxy_user'] . (empty($req_settings['proxy_password']) ? '' : ':' . $req_settings['proxy_password']));
			}
		}

		if (!empty($basic_auth)) {
			curl_setopt ($ch, CURLOPT_USERPWD, implode(':', $basic_auth));
		}

		curl_setopt ($ch, CURLOPT_URL, $url);
		if (!empty($referer)) {
			curl_setopt ($ch, CURLOPT_REFERER, $referer);
		}
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		if (!empty($cert)) {
			curl_setopt ($ch, CURLOPT_SSLCERT, $cert);
			if(!empty($kcert)) {
				curl_setopt ($ch, CURLOPT_SSLKEY, $kcert);
			}
		}

		if ($_curl['verify_peer'] == false) {
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 1);
		}

		if ($method == 'GET') {
			curl_setopt ($ch, CURLOPT_HTTPGET, 1);
		} else {
			curl_setopt ($ch, CURLOPT_POST, 1);
			if (!empty($data) && !empty($join)) {
				foreach ($data as $k => $v) {
					list($a,$b) = explode('=', trim($v),2);
					$data[$k] = $a . '=' . rawurlencode($b);
				}
			}

			if (is_array($data)) {
				$data = implode($join, $data);
			}

			curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_HEADERFUNCTION, 'fn_curl_headers');
		fn_curl_headers(false);

		$body = curl_exec ($ch);
		$errno = curl_errno ($ch);
		$error = curl_error ($ch);
		curl_close ($ch);
		if (empty($error)) {
			$result = array(fn_curl_headers(true), $body);
		} else {
			$result = array('0', "HTTPS: libcurl error($errno): $error");
		}
	}

	// Log event http/https requests
	fn_log_event('requests', 'http', array(
		'url' => $url,
		'data' => var_export($data, true),
		'response' => $result[1],
	));

	return $result;
}

/**
 * Test if curl active and operational
 *
 * @return mixed boolean false if curl is not active, array with curl supported params
 */
function fn_check_curl()
{
	$result = array();
	$result['curl_exists'] = function_exists('curl_init');
	if ($result['curl_exists'] == false) {
		return false;
	}

	$ver = curl_version();
	$result['verify_peer'] = true;
	if (is_array($ver)) {
		$version = 'libcurl/'.$ver['version'];
		$result['ssl_support'] = !empty($ver['ssl_version']) ? true : false;
	} else {
		$version = $ver;
		$result['ssl_support'] = (strpos($ver, 'SSL') !== false) ? true : false;
	}

	if (preg_match('/libcurl\/(\d+\.\d+\.\d+)/', $version, $matches)) {
		if (version_compare('7.10.0', $matches[1], '<') == 1) {
			$result['verify_peer'] = false;
		}
	}

	return $result;
}

/**
 * Get curl information
 *
 * @param string $object object name to generate message for is case of error
 * @return string message
 */
function fn_get_curl_info($object = '')
{
	$curl = fn_check_curl();
	$result = '';
	if ($curl == false) {
		$msg = fn_get_lang_var('error_curl_not_exists');
	} elseif ($curl['ssl_support'] == false) {
		$msg = fn_get_lang_var('error_curl_ssl_not_exists');
	}

	if (!empty($msg)) {
		$msg = str_replace('[method]', $object, $msg);
		$result = "<p>$msg</p><hr />";
	}

	return $result;
}

/**
 * Perform http request using sockets
 *
 * @param string $method request method (post/get)
 * @param string $url request url
 * @param array $data data to pass with request
 * @param array $cookies cookies to pass with request
 * @param array $basic_auth username/password for basic authentication
 * @return array response
 */
function fn_http_request($method, $url, $data = array(), $cookies = array(), $basic_auth = array())
{
	$result = '';
	$header_passed = false;
	$parsed_data = parse_url($url);
	$current_url = Registry::get('config.current_url');

	// Set default http port (if not set)
	if (empty($parsed_data['port'])) {
		$parsed_data['port'] = 80;
	}

	// Attach query string to data
	if (!empty($parsed_data['query'])) {
		$_data = array();
		parse_str($parsed_data['query'], $_data);
		if (!empty($_data)) {
		    $data = fn_array_merge($data, $_data);
		}
	}

	if (empty($parsed_data['path'])) {
		$parsed_data['path'] = '';
	}

	// Proxy settings
	$req_settings = fn_get_request_settings();

	$fp = @fsockopen(empty($req_settings['proxy_host']) ? $parsed_data['host'] : $req_settings['proxy_host'], empty($req_settings['proxy_host']) ? $parsed_data['port'] : (empty($req_settings['proxy_port']) ? 3128 : $req_settings['proxy_port']), $errno, $errstr, 30);
	if (!$fp) {
		$result = '';
		$http_header = '';
	} else {
		if (!empty($data)) {
			$query_string = fn_build_query($data);
		} else {
			$query_string = '';
		}

		if ($method == 'GET') {
			$post_url = (!empty($query_string)) ? $parsed_data['path'] . '?' . $query_string : $parsed_data['path'];
		} else {
			$post_url = $parsed_data['path'];
		}

		$request_url = empty($req_settings['proxy_host']) ? $post_url : $url . (empty($query_string) ? '' : "?$query_string");

		fputs($fp, "$method $request_url HTTP/1.0\r\n");
		fputs($fp, "Referer: $current_url/\r\n");
		fputs($fp, "Host: $parsed_data[host]\r\n");

		if (!empty($req_settings['proxy_user'])) {
			fputs($fp, "Proxy-Authorization: Basic " . base64_encode($req_settings['proxy_user'] . ':' . $req_settings['proxy_password']) . "\r\n");
		}

		if (!empty($basic_auth)) {
			fputs($fp, "Authorization: Basic " . base64_encode(implode(':', $basic_auth)) . "\r\n");
		}

		fputs($fp, "User-Agent: Mozilla/4.5 [en]\r\n");
		if (!empty($cookies)) {
			fputs($fp, 'Cookie: ' . join('; ', $cookies) . "\r\n");
		}

		if ($method == 'POST') {
			fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-Length: " . strlen($query_string) ."\r\n");
			fputs($fp, "\r\n");
			fputs($fp, $query_string);
		} else {
			fputs($fp, "\r\n");
		}

		$http_header = array(
			'RESPONSE' => rtrim(fgets($fp, 4096)),
		);

		while (!feof($fp)) {

			if (!$header_passed) {
				$header = fgets($fp, 4096);
			} else {
				$result .= fread($fp, 65536);
			}

			if ($header_passed == false && ($header == "\n" || $header == "\r\n")) {
				$header_passed = true;
				continue;
			}

			if ($header_passed == false) {
				$header_line = explode(": ", $header, 2);
				$http_header[strtoupper($header_line[0])] = rtrim($header_line[1]);
			}
		}

		fclose($fp);
	}

	// Log http/https requests
	fn_log_event('requests', 'http', array(
		'url' => $url,
		'data' => var_export($data, true),
		'response' => $result,
	));

	return array($http_header, $result);
}

/**
 * Remove parameter from the URL query part
 *
 * @param string ... query
 * @param string ... parameters to remove
 * @return string modified query
 */
function fn_query_remove()
{
	$args = func_get_args();
	$query = array_shift($args);

	if (!empty($args)) {
		foreach ($args as $param_name) {
			if (preg_match_all("/(&amp;|&|\?|^)+($param_name(\[\w*\])*(=?[\.\w-%\d]*)*(&amp;|&|\?|$)+)/", $query, $matches)) {
				foreach ($matches[2] as $m2) {
					$query = str_replace($m2, '', $query);
					if (substr($query, -5) == '&amp;') {
						$query = substr($query, 0, -5);
					}
					if (substr($query, -1) == '&') {
						$query = substr($query, 0, -1);
					}
					$query = str_replace($m2, '', $query);
				}
			}
		}
	}

	return $query;
}

/**
 * Build query from the array
 *
 * @param array $array data to build query from
 * @param string $query part of query to attach new data
 * @param string $prefix prefix
 * @return string well-formed query
 */
function fn_build_query($array, $query = '', $prefix = '')
{
	if (!is_array($array)) {
		return false;
	}

	foreach ($array as $k => $v) {
		if (is_array($v)) {
			$query = fn_build_query($v, $query, rawurlencode(empty($prefix) ? "$k" : $prefix . "[$k]"));
		} else {
			$query .= (!empty($query) ? '&' : '') . (empty($prefix) ? $k : $prefix . rawurlencode("[$k]")). '=' . rawurlencode($v);
		}
	}

	return $query;
}

/**
 * Function returns the proxy settings
 *
 * @return array
 */
function fn_get_request_settings()
{
	return array (
		'proxy_host' => Registry::get('settings.General.proxy_host'),
		'proxy_port' => Registry::get('settings.General.proxy_port'),
		'proxy_user' => Registry::get('settings.General.proxy_user'),
		'proxy_password' => Registry::get('settings.General.proxy_password'),
	);
}

?>