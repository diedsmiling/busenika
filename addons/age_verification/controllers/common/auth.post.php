<?php

//
// $Id: auth.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if (!defined('AREA')) { die('Access denied');	}

if ($mode == 'login') {
	if (!empty($_SESSION['auth']['user_id'])) {
		$u_data = db_get_row("SELECT birthday, status FROM ?:users WHERE user_id = ?i", $_SESSION['auth']['user_id']);

		fn_age_verification_check_age($u_data);
	}
}

?>