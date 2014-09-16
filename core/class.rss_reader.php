<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2004 Simbirsk Technologies Ltd. All rights reserved.    *
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
// $Id: class.rss_reader.php 9162 2010-03-26 08:44:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

require(DIR_LIB . 'rss_php/rss_php.php');

class RssReader extends rss_php
{
	public function loadData($url)
	{
		if(!empty($url)) {
			$this->loadRSS(fn_get_contents($url));
		}
	}
}

?>