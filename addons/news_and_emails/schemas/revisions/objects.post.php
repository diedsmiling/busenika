<?php

//
// $Id: objects.post.php 7752 2009-07-24 12:56:42Z lexa $
//

if ( !defined('AREA') ) { die('Access denied'); }

//
// Add new tables
//
$schema['news'] = array(
	'primary_key' => 'news_id',
	'edit_link' => 'dispatch=news.update&news_id=',
	'manage_link' => 'dispatch=news.manage',
	'manage_name' => 'news',
	'has_images' => false,
	'has_attachments' => false,
	'database' => array (
		'news' => array (
			'keys' => array ('news_id'),
			'parent' => array (),
			'children' => array (),
			'is_auto' => true,
			'auto_key' => 'news_id'
		),

		'news_descriptions' => array (
			'keys' => array ('news_id', 'lang_code'),
			'sort_by' => array('news_id'),
			'sort_order' => array('DESC'),
			'parent' => array (),
			'children' => array (),
			'is_auto' => false
		)
	),
	'description' => array(
		'title' => 'news',
		'title_s' => 'news',
		'table' => 'news_descriptions',
		'field' => 'news',
		'object_name_function' => 'fn_get_news_name',
		'lang_code' => true
	)
);

?>