<?php

	@session_name('PHPWEBMAILSESSID');
	@session_start();

	$dataPath = realpath(dirname(__FILE__) . '/../../../../../var/webmail');

	if (empty($_SESSION['cart_checked']) && !defined('AREA')) {
		if (fn_wm_check_session(__FILE__) == true) {
			$_SESSION['cart_checked'] = true;
			$_SESSION['cart_url'] = WM_CART_URL;
		} else {
			die('CS: ACCESS DENIED');
		}
	}

	function fn_wm_check_session($file)
	{
		$_root = realpath(dirname($file) . '/../../../../../');

		define('DIR_ROOT', $_root);
		define('AREA', 'A');
		include($_root . '/config.php');

		if (!isset($_COOKIE[SESS_NAME])) {
			return false;
		}

		define('WM_CART_URL', 'http://' . $config['http_host'] . $config['http_path'] . '/' . $config['admin_index']);

		$url = WM_CART_URL . '?check_session=1&' . SESS_NAME . '=' . $_COOKIE[SESS_NAME];

		list($_h, $_r) = fn_wm_http_request('GET', $url);

		return ($_r == 'OK');
	}

	function fn_wm_http_request($method, $url, $data = array(), $cookies = array())
	{
		$result = '';
		$header_passed = false;
		$parsed_data = parse_url($url);
		$current_url = '';

		// Set default http port (if not set)
		if (empty($parsed_data['port'])) {
			$parsed_data['port'] = 80;
		}

		// Attach query string to data
		if (!empty($parsed_data['query'])) {
			parse_str($parsed_data['query'], $data);
		}

		if (empty($parsed_data['path'])) {
			$parsed_data['path'] = '';
		}

		$fp = @fsockopen($parsed_data['host'], $parsed_data['port'], $errno, $errstr, 30);

		if (!$fp) {
			$result = '';
			$http_header = '';
		} else {
			$data = http_build_query($data);

			if ($method == 'GET') {
				$post_url = (!empty($data)) ? $parsed_data['path'] . '?' . $data : $parsed_data['path'];
			} else {
				$post_url = $parsed_data['path'];
			}

			$request_url = empty($proxy['host']) ? $post_url : $url . (empty($data) ? '' : "?$data");

			fputs($fp, "$method $request_url HTTP/1.0\r\n");
			//fputs($fp, "Referer: $current_url/\r\n");
			fputs($fp, "Host: $parsed_data[host]\r\n");
			if (!empty($proxy['user'])) {
				fputs($fp, "Proxy-Authorization: Basic " . base64_encode($proxy['user'] . ':' . $proxy['password']) . "\r\n");
			}
			fputs($fp, "User-Agent: Mozilla/4.5 [en]\r\n");
			if (!empty($cookies)) {
				fputs($fp, 'Cookie: ' . join('; ', $cookies) . "\r\n");
			}

			if ($method == 'POST') {
				fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
				fputs($fp, "Content-Length: " . strlen($data) ."\r\n");
				fputs($fp, "\r\n");
				fputs($fp, $data);
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

		return array($http_header, trim($result));
	}

?>