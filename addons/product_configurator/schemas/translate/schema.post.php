<?php

//
// $Id: schema.post.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema['configurator'] = array (
	'manage' => array (
		'product_classes' => array (
			'dimension' => 1,
			'table_name' => 'conf_class_descriptions',
			'fields' => array ('class_name'),
			'where_fields' => array(
				'class_id' => 'class_id'
			)
		),
		'configurator_steps' => array (
			'dimension' => 1,
			'table_name' => 'conf_step_descriptions',
			'fields' => array ('step_name'),
			'where_fields' => array(
				'step_id' => 'step_id'
			)
		),
		'configurator_groups' => array (
			'dimension' => 1,
			'table_name' => 'conf_group_descriptions',
			'fields' => array ('configurator_group_name', 'full_description'),
			'where_fields' => array(
				'group_id' => 'group_id'
			)
		)
	),
	'update_class' => array (
		'product_class' => array (
			'dimension' => 0,
			'table_name' => 'conf_class_descriptions',
			'fields' => array ('class_name'),
			'where_fields' => array(
				'class_id' => 'class_id'
			)
		),
		'configurator_groups' => array (
			'dimension' => 1,
			'table_name' => 'conf_group_descriptions',
			'fields' => array ('configurator_group_name', 'full_description'),
			'where_fields' => array(
				'group_id' => 'group_id'
			)
		)
	),
	'update_group' => array (
		'configurator_steps' => array (
			'dimension' => 1,
			'table_name' => 'conf_step_descriptions',
			'fields' => array ('step_name'),
			'where_fields' => array(
				'step_id' => 'step_id'
			)
		),
		'configurator_group' => array (
			'dimension' => 0,
			'table_name' => 'conf_group_descriptions',
			'fields' => array ('configurator_group_name', 'full_description'),
			'where_fields' => array(
				'group_id' => 'group_id'
			)
		)
	)
);

?>
