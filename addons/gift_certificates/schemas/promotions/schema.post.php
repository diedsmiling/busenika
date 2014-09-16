<?php

//
// $Id: schema.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['bonuses']['gift_certificate'] = array (
	'type' => 'input',
	'function' => array('fn_gift_certificates_promotion_gift_certificate', '#this', '@cart', '@auth', '@cart_products'),
	'zones' => array('cart'),
);


?>
