<?php

//
// $Id: admin.php 8051 2009-10-02 12:54:14Z alexions $
//

define('AREA', 'A');
define('AREA_NAME' ,'admin');

require dirname(__FILE__) . '/prepare.php';
require dirname(__FILE__) . '/init.php';

define('INDEX_SCRIPT',  Registry::get('config.admin_index'));

fn_dispatch();

?>