<?php
                                                       
//
// $Id: init.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

fn_register_hooks('place_order',
				  'update_profile',
				  'update_product_amount');
?>