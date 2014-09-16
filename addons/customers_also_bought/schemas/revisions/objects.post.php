<?php

//
// $Id: objects.post.php 7538 2009-05-27 15:55:45Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// Add new tables
//
$schema['product']['database']['also_bought_products'] = array (
	'keys' => array ('product_id'),
	'parent' => array (),
	'children' => array (),
	'is_auto' => false
);

?>