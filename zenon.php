<?php
ini_set('display_errors', 0);
//
// $Id: admin.php 8051 2009-10-02 12:54:14Z alexions $
//
//var_dump('new');
define('AREA', 'A');
define('AREA_NAME' ,'admin');

require dirname(__FILE__) . '/prepare.php';
require dirname(__FILE__) . '/init.php';
require DIR_LIB. 'god/god.lib.php';
$god = new God();

define('INDEX_SCRIPT',  Registry::get('config.admin_index'));

fn_dispatch();