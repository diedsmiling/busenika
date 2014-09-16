<?php

//
// $Id: structure.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['products']['fillings']['also_bought'] = array (
	'params' => array (
		'sort_by' => 'amnt',
		'request' => array(
			'also_bought_for_product_id' => '%PRODUCT_ID%'
		),
	),
	'locations' => array( // applicable to these locations only
		'products'
	)
);

?>