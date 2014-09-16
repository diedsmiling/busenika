<?php

//
// $Id: schema.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['newsletters.update'] = array(
	'func' => array('fn_get_newsletter_name', '@newsletter_id'),
	'text' => 'newsletter'
);
$schema['news.update'] = array(
	'func' => array('fn_get_news_name', '@news_id'),
	'text' => 'news'
);
$schema['mailing_lists.manage'] = array(
	'text' => 'mailing_lists'
);
$schema['subscribers.manage'] = array(
	'text' => 'subscribers'
);
$schema['campaigns.manage'] = array(
	'text' => 'campaigns'
);

?>
