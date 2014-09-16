<?php

//
// $Id: products.post.php 8049 2009-10-02 10:24:55Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }

if (!empty($_SESSION['saved_post_data']['send_data'])) {
	$view->assign('send_data', $_SESSION['saved_post_data']['send_data']);
	unset($_SESSION['saved_post_data']['send_data']);
}


?>
