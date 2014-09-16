<?php

//
// $Id: xmlsitemap.php 8134 2009-10-28 07:57:30Z alexions $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($mode == 'view') {
	$filename = DIR_CACHE . 'sitemap.xml';
	
	if (file_exists($filename)) {
		header("Content-Type: text/xml;charset=utf-8");
		
		readfile($filename);
		exit();
		
	} else {
		fn_google_sitemap_get_content($filename);
	}
}

?>