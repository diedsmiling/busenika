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
// $Id: func.php 10264 2010-07-29 08:09:07Z klerik $
//

if ( !defined('AREA') ) { die('Access denied'); }

function fn_statistics_init_secure_controllers($controllers)
{
	$controllers['statistics'] = 'passive';
}

//
// Search requests for LiveHelp
// $group_name - LH variable (visitors)
// $result - array entities of $group_name
function fn_statistics_livehelp_get_group_data($group_name, &$result)
{
	if ($group_name == 'visitors' && !empty($result)) {
		foreach ($result as $k => $v) {
			if (!empty($v['ip'])) {
				$sess_id = db_get_field("SELECT MAX(sess_id) FROM ?:stat_sessions WHERE host_ip = ?i", $v['ip']);
				$sess_data = db_get_row("SELECT CONCAT(?:stat_browsers.browser, ', ', ?:stat_browsers.version) AS browser, ?:stat_sessions.os, ?:stat_sessions.referrer, CONCAT(?:stat_ips.country_code, '|', ?:country_descriptions.country) AS country FROM ?:stat_sessions LEFT JOIN ?:stat_browsers ON ?:stat_sessions.browser_id = ?:stat_browsers.browser_id LEFT JOIN ?:stat_ips ON ?:stat_sessions.ip_id = ?:stat_ips.ip_id LEFT JOIN ?:country_descriptions ON ?:stat_ips.country_code = ?:country_descriptions.code AND ?:country_descriptions.lang_code = ?s WHERE sess_id = ?i", CART_LANGUAGE, $sess_id);
				if (!empty($sess_data)) {
					$result[$k]['browser'] = htmlentities($sess_data['browser']);
					$result[$k]['os'] = htmlentities($sess_data['os']);
					$result[$k]['referer'] = htmlentities($sess_data['referrer']);
					$result[$k]['country'] = htmlentities($sess_data['country']);
				}

				$req_id = db_get_field("SELECT MAX(req_id) FROM ?:stat_requests WHERE sess_id = ?i", $sess_id);
				$req_data = db_get_row("SELECT url, title FROM ?:stat_requests WHERE req_id = ?i", $req_id);
				if (!empty($req_data)) {
					$result[$k]['href'] = htmlentities($req_data['url']);
					$result[$k]['title'] = htmlentities($req_data['title']);
				}
			}
		}
	}

}

function fn_statistics_get_banners(&$banners)
{
	if (AREA == 'C' && !fn_is_empty($banners) && !defined('AJAX_REQUEST')) {
		foreach ($banners as $k => $v) {
			if ($v['type'] == 'T' && !empty($v['description'])) {
				$i = $pos = 0;
				$matches = array();
				while (preg_match('/href=([\'|"])(.*?)([\'|"])/i', $banners[$k]['description'], $matches, PREG_OFFSET_CAPTURE, $pos)) {
					$banners[$k]['description'] = substr_replace($banners[$k]['description'], fn_url("statistics.banners?banner_id=$v[banner_id]&amp;link=" . $i++ , 'C'), $matches[2][1], strlen($matches[2][0]));
					$pos = $matches[2][1];					
				}
			} elseif (!empty($v['url'])) {
				$banners[$k]['url'] = "statistics.banners?banner_id=$v[banner_id]";
			}

			db_query('INSERT INTO ?:stat_banners_log ?e', array('banner_id' => $v['banner_id'], 'type' => 'V', 'timestamp' => TIME));
		}
	} else {
		return false;
	}
}

function fn_statistics_delete_banners($banner_id)
{
	db_query("DELETE FROM ?:stat_banners_log WHERE banner_id = ?i", $banner_id);
}

function fn_statistics_search_by_objects($conditions)
{
	if (!empty($conditions['products'])) {
		$obj = $conditions['products'];
		$total = db_get_field("SELECT COUNT(DISTINCT($obj[table].$obj[key])) FROM ?:products as $obj[table] $obj[join] WHERE $obj[condition]");
		Registry::get('view')->assign('product_count', $total);
	}
}

function fn_statistics_init_templater(&$view)
{
	if (AREA == 'C' && USER_AGENT == 'crawler' && !empty($_SERVER['HTTP_USER_AGENT']) && !defined('AJAX_REQUEST')) {
		$view->register_outputfilter('fn_statistics_track_robots');
	}
}

function fn_statistics_track_robots($tpl_output, &$view)
{
	if (strpos($tpl_output, '<title>') === false) {
		return $tpl_output;
	}

	$sess_id = db_get_field('SELECT sess_id FROM ?:stat_sessions WHERE uniq_code = ?i AND timestamp > ?i', fn_crc32($_SERVER['HTTP_USER_AGENT']), TIME - (24 * 60 * 60));	
	
	if (empty($sess_id)) {
		$ip = fn_get_ip(true);
		$referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$parse_url = parse_url($referer);

		$stat_data = array(
			'user_agent' => $_SERVER['HTTP_USER_AGENT'],
			'host_ip' => $ip['host'],
			'proxy_ip' => $ip['proxy'],
			'client_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
			'ip_id' => fn_stat_ip_exist($ip),
			'client_type' => 'B',
			'robot' => CRAWLER,
			'referrer' => $referer,
			'timestamp' => TIME,
			'referrer_scheme' => empty($parse_url['scheme']) ? '' : $parse_url['scheme'],
			'referrer_host' => empty($parse_url['host']) ? '' : $parse_url['host'],
			'expiry' => 0,
			'uniq_code' => fn_crc32($_SERVER['HTTP_USER_AGENT'])
		);

		$request_type = STAT_LAST_REQUEST;
		$sess_id = db_query('INSERT INTO ?:stat_sessions ?e', $stat_data);
		$last_url = '';
	} else {
		$last_url = db_get_field("SELECT url FROM ?:stat_requests WHERE sess_id = ?i AND (request_type & ?i) = ?i", $sess_id, STAT_LAST_REQUEST, STAT_LAST_REQUEST);
		db_query("UPDATE ?:stat_requests SET request_type = request_type & ". STAT_ORDINARY_REQUEST ." WHERE sess_id = ?s", $sess_id);
		$request_type = STAT_END_REQUEST;
	}

	// Add to stat requests
	$this_url = fn_stat_prepare_url(REAL_URL);
	if ($last_url != $this_url) {
		$title = '';
		if (preg_match_all('/\<title\>(.*?)\<\/title\>/', $tpl_output, $m)) {
			$title = fn_html_escape($m[1][0], true);
		}
		
		$ve = array(
			'sess_id' => $sess_id,
			'timestamp' => TIME,
			'url' => $this_url,
			'title' => $title,
			'https' => defined('HTTPS') ? 'Y' : 'N',
			'loadtime' => microtime(true) - MICROTIME,
			'request_type' => $request_type
		);
		db_query("INSERT INTO ?:stat_requests ?e", $ve);
	}

	return $tpl_output;

}

//
// CHECK: Do IP exist?
//
function fn_stat_ip_exist($ip)
{
	if (!empty($ip['host']) && fn_is_inet_ip($ip['host'], true)) {
		$ip_num = $ip['host'];
	} elseif (!empty($ip['proxy']) && fn_is_inet_ip($ip['proxy'], true)) {
		$ip_num = $ip['proxy'];
	}
	$ip_id = isset($ip_num) ? db_get_field("SELECT ip_id FROM ?:stat_ips WHERE ip = ?i", $ip_num) : false;
	if (empty($ip_id) && !empty($ip_num)) {
		$ip_id = fn_stat_save_ip(array('ip' => $ip_num));
	}
	return empty($ip_id) ? false : $ip_id;
}

//
// Save IP data.
//
function fn_stat_save_ip($ip_data)
{
	if (!empty($ip_data['ip'])) {
		$ip_data['country_code'] = fn_get_country_by_ip(long2ip($ip_data['ip']));
		return db_query('INSERT INTO ?:stat_ips ?e', $ip_data);
	}

	return false;
}

function fn_stat_prepare_url($url)
{
	$url = fn_stat_cut_www($url);
	$location = fn_stat_cut_www(Registry::get('config.http_location'));
	$s_location = fn_stat_cut_www(Registry::get('config.https_location'));	

	// Remove url prefix
	if (strpos($url, $location) !== false) {
		$url = str_replace($location, '', $url);

	} elseif (strpos($url, $s_location) !== false) {
		$url = str_replace($s_location, '', $url);
	}

	return $url;
}

function fn_stat_cut_www($url)
{
	return str_replace('://www.', '://', $url);
}
?>