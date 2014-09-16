<?php

//
// $Id: init.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	return;
}

$view->assign('online_time', SESSION_ONLINE);
$view->assign('users_online', db_get_field("SELECT COUNT(distinct ?:stat_requests.sess_id) FROM ?:stat_requests LEFT JOIN ?:stat_sessions ON ?:stat_requests.sess_id = ?:stat_sessions.sess_id WHERE ?:stat_requests.timestamp >= ?i AND ?:stat_requests.timestamp <= ?i AND ?:stat_sessions.client_type = 'U'", (TIME - SESSION_ONLINE), TIME)); // Count active connections in last 5 minutes 

?>
