<?php

//
// $Id: specific_settings.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['fillings']['also_bought'] = array (
	'limit' => array (
		'type' => 'input',
		'default_value' => 3,
	)
);

?>