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
// $Id: init.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

// types
define('NEWSLETTER_TYPE_NEWSLETTER', 'N');
define('NEWSLETTER_TYPE_TEMPLATE', 'T');
define('NEWSLETTER_TYPE_AUTORESPONDER', 'A');

// newsletter formats
define('NEWSLETTER_FORMAT_TXT', 1);
define('NEWSLETTER_FORMAT_HTML', 2);

// two modes for fn_update_subscriptions() function - first mode for 'my profile' page where
// unchecking mailing list checkbox unsubscribes you from the mailing list, and second mode for sidebar form,
// where unchecked mailing list checkbox means "don't touch anything except
define('NEWSLETTER_DELETE_UNCHECKED', 1);
define('NEWSLETTER_SAVE_UNCHECKED', 2);
define('NEWSLETTER_ONLY_CHECKED', 3);

fn_register_hooks(
	'is_accessible_discussion',
	'get_discussion_object_data',
	'get_discussion_objects',
	'get_block_locations',
	'localization_objects',
	'search_init',
	'save_log',
	'sitemap_item',
	'sitemap_link_object',
	'seo_is_indexed_page',
	'get_seo_vars',
	'seo_url',
	'validate_sef_object'
);

?>