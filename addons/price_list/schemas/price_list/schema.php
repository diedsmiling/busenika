<?php

//
// $Id: schema.php 8006 2009-09-17 07:17:39Z alexions $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array(
	'fields' => array(
		'product_id' => array('title' => fn_get_lang_var('product_id'), 'sort_by' => ''),
		'product' => array('title' => fn_get_lang_var('product_name'), 'sort_by' => 'product'),
		'min_qty' => array('title' => fn_get_lang_var('min_order_qty'), 'sort_by' => ''),
		'max_qty' => array('title' => fn_get_lang_var('max_order_qty'), 'sort_by' => ''),
		'product_code' => array('title' => fn_get_lang_var('product_code'), 'sort_by' => 'code'),
		'amount' => array('title' => fn_get_lang_var('amount'), 'sort_by' => 'amount'),
		'price' => array('title' => fn_get_lang_var('price'), 'sort_by' => 'price'),
		'weight' => array('title' => fn_get_lang_var('weight'), 'sort_by' => 'weight'),
		'image' => array('title' => fn_get_lang_var('image'), 'sort_by' => ''),
	),
);

?>