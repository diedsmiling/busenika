<?php

//
// $Id: init.post.php 8570 2010-01-12 11:37:46Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	return;
}

$_SESSION['cart']['points_info']['reward'] = !empty($_SESSION['cart']['points_info']['reward']) ? $_SESSION['cart']['points_info']['reward'] : 0;

?>