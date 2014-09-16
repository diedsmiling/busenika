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
// $Id: init.post.php 9820 2010-06-21 11:31:45Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$navigation = & Registry::get('navigation');
if (Registry::get('addons.discussion.home_page_testimonials') == 'D') {
	unset($navigation['static']['content']['discussion_title_home_page']);
}

?>