<?php

//
// $Id: config.php 8137 2009-10-28 12:38:53Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

define('AF_ORDER_TOTAL_FACTOR', 2);
define('AF_COMPLETED_ORDERS_FACTOR', 2);
define('AF_FAILED_ORDERS_FACTOR', 1.5);
define('AF_ERROR_FACTOR', 5);

?>
