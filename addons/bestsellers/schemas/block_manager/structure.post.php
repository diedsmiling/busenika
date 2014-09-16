<?php

//
// $Id: structure.post.php 7745 2009-07-21 07:15:15Z alexions $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['products']['fillings']['bestsellers'] = array (
	'params' => array (
		'bestsellers' => true,
		'sales_amount_from' => 1,
		'sort_by' => 'sales_amount',
		'sort_order' => 'desc',
		'type' => 'extended',
		'request' => array (
			'cid' => '%CATEGORY_ID'
		)
	)
);

?>