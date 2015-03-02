<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 04.02.15
 * Time: 17:26
 */
define('AREA', 'A');
define('AREA_NAME' ,'admin');
require dirname(__FILE__) . "/../.." . '/prepare.php';
require dirname(__FILE__) . "/../.." . '/init.php';

include_once "SyncVendor.php";
include_once "PHPExcel.php";

include_once DIR_SYNC_VENDORS . "SyncVendor.php";
include_once DIR_SYNC_VENDORS . "PHPExcel.php";

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

$svp = new SyncVendor();
$svp->downloadSheets();
$svp->run();