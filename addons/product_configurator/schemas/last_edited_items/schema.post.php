<?php

//
// $Id: schema.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['configurator.update_group'] = array(
	'func' => array('fn_product_configurator_get_group_name', '@group_id'),
	'text' => 'product_group'
);
$schema['configurator.update_class'] = array(
	'func' => array('fn_product_configurator_get_class_name', '@class_id'),
	'text' => 'compatible_class'
);

?>
