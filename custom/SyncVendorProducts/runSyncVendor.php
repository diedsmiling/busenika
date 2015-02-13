<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 04.02.15
 * Time: 17:26
 */
include_once "SyncVendor.php";
include_once "PHPExcel.php";

ini_set('max_execution_time', 0);

$svp = new SyncVendor();
$svp->run();