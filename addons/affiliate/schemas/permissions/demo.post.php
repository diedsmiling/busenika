<?php

//
// $Id: demo.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['affiliate_plans'] = array (
	'restrict' => array ('POST')
);

$schema['banners_manager'] = array (
	'restrict' => array ('POST')
);

$schema['product_groups'] = array (
	'restrict' => array ('POST')
);

$schema['payouts'] = array (
	'restrict' => array ('POST')
);

?>
