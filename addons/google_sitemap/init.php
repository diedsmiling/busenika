<?php

//
// $Id: init.php 8320 2009-11-25 11:12:21Z alexions $
//

if ( !defined('AREA') ) { die('Access denied'); }

fn_register_hooks(
	'get_rewrite_rules'
);

?>