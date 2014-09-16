<?php



include 'WSS/engine.php';







define('AREA', 'C');



define('AREA_NAME' ,'customer');







require dirname(__FILE__) . '/prepare.php';



require dirname(__FILE__) . '/init.php';







define('INDEX_SCRIPT', Registry::get('config.customer_index'));







fn_dispatch();







?>

