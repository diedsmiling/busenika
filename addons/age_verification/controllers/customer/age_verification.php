<?php

//
// $Id: age_verification.php 7502 2009-05-19 14:54:59Z zeke $
//

if (!defined('AREA')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($mode == 'verify') {
		if (!empty($_REQUEST['age'])) {
			$age = intval($_REQUEST['age']);

			if ($age < 0) {
				$age = 0;
			}

			$_SESSION['auth']['age'] = $age;

			if (!empty($_REQUEST['redirect_url'])) {
				return array (CONTROLLER_STATUS_OK, $_REQUEST['redirect_url']);
			}

			return array (CONTROLLER_STATUS_REDIRECT, '');
		}
	}
}

?>