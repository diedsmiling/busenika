<?php

//
// $Id: schema.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['conditions']['reward_points'] = array (
	'operators' => array ('eq', 'neq', 'lte', 'gte', 'lt', 'gt'),
	'type' => 'input',
	'field' => '@auth.points',
	'zones' => array('catalog', 'cart')
);

$schema['bonuses']['give_points'] = array (
	'type' => 'input',
	'function' => array('fn_reward_points_promotion_give_points', '#this', '@cart', '@auth', '@cart_products'),
	'zones' => array('cart'),
);


?>
