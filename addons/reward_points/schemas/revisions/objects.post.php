<?php

//
// $Id: objects.post.php 8091 2009-10-19 08:29:55Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// Add new tables
//
$schema['product']['database']['product_point_prices'] = array (
	'keys' => array ('point_price_id'),
	'parent' => array (),
	'children' => array (),
	'is_auto' => true,
	'auto_key' => 'point_price_id'
);
$schema['product']['database']['reward_points'] = array (
	'keys' => array ('reward_point_id'),
	'main_key' => array('object_id' => '#key', 'usergroup_id' => '#key', 'object_type' => 'P'),
	'parent' => array (),
	'children' => array (),
	'is_auto' => true,
	'auto_key' => 'reward_point_id'
);
$schema['category']['database']['reward_points'] = array (
	'keys' => array ('reward_point_id'),
	'main_key' => array('object_id' => '#key', 'usergroup_id' => '#key', 'object_type' => 'C'),
	'parent' => array (),
	'children' => array (),
	'is_auto' => true,
	'auto_key' => 'reward_point_id'
);

?>