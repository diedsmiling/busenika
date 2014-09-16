<?php

//
// $Id: init.php 7502 2009-05-19 14:54:59Z zeke $
//

if (!defined('AREA')) { die('Access denied'); }

fn_register_hooks(
	'get_products', 
	'get_user_info', 
	'get_categories', 
	'update_profile'
);

?>