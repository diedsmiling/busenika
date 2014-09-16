<?php

//
// $Id: objects.post.php 8329 2009-11-27 10:00:40Z ivan $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// Add new tables
//
$schema['product']['database']['product_required_products'] = array (
	'keys' => array ('product_id', 'required_id'),
	'parent' => array (),
	'children' => array (),
	'is_auto' => false,
);

?>