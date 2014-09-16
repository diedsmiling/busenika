<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2009 Simbirsk Technologies Ltd. All rights reserved.    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/


//
// $Id: func.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }


function fn_webmail_init_templater(&$view, &$view_mail)
{
	if (AREA == 'A') {
		$view->register_prefilter('fn_webmail_convert_mailto');
	}
}

function fn_webmail_convert_mailto($tpl_source, &$view)
{
	if (preg_match_all('/href="mailto:\{(.*?)\}"/i', $tpl_source, $matches)) {
		foreach ($matches[0] as $k => $m) {
			$tpl_source = str_replace($m, 'href="{$config.http_location}/addons/webmail/lib/webmail/web/webmail.php?start=1&amp;to={' . $matches[1][$k] . '|escape:url}"', $tpl_source);
		}
	}

	return $tpl_source;
}

?>