<?php

//
// $Id: promotions.post.php 8413 2009-12-16 12:45:19Z imac $
//

if (!defined('AREA') ) { die('Access denied'); }

if ($mode == 'list') {
	$params['status'] = 'A';
	$params['date'] = true;
	$params['full_info'] = true;
	$params['promotions'] = true;
	
	$chains = fn_buy_together_get_chains($params, $auth);
	
	if (!empty($chains)) {
		$promotions = $view->get_var('promotions');
		$promotions['chains'] = $chains;
		
		$view->assign('promotions', $promotions);
	}

}

?>