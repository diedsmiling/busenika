<?php

//
// $Id: schema.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['banners.update'] = array(
	'func' => array('fn_get_banner_name', '@banner_id'),
	'text' => 'banners'
);

?>
