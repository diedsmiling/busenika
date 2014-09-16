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
// $Id: static_data.php 6923 2009-02-19 13:18:47Z zeke $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array(
	'C' => array( // credit cards
		'param' => 'card_code',
		'descr' => 'card_name',
		'add_title' => 'add_new_credit_cards',
		'edit_title' => 'editing_credit_card',
		'add_button' => 'add_credit_card',
		'mainbox_title' => 'credit_cards',
		'additional_params' => array(
			array(
				'title' => 'cvv2',
				'type' => 'checkbox',
				'name' => 'param_2'
			),
			array(
				'title' => 'start_date',
				'type' => 'checkbox',
				'name' => 'param_3'
			),
			array(
				'title' => 'issue_number',
				'type' => 'checkbox',
				'name' => 'param_4'
			),
		),
		'icon' => array(
			'title' => 'icon',
			'type' => 'credit_card'
		),
		'has_localization' => true,
	),
	'T' => array( // titles
		'param' => 'ID',
		'descr' => 'title',
		'add_title' => 'add_new_titles',
		'add_button' => 'add_title',
		'edit_title' => 'editing_title',
		'mainbox_title' => 'titles',
		'has_localization' => true,
	),
	'N' => array( // quick links
		'param' => 'url',
		'descr' => 'link_text',
		'add_title' => 'add_new_items',
		'add_button' => 'add_item',
		'edit_title' => 'editing_item',
		'mainbox_title' => 'quick_links',
		'has_localization' => true,
	),
	'A' => array( // top menu
		'param' => 'url',
		'descr' => 'link_text',
		'add_title' => 'add_new_items',
		'add_button' => 'add_item',
		'edit_title' => 'editing_item',
		'mainbox_title' => 'top_menu',
		'additional_params' => array(
			array(
				'title' => 'activate_menu_tab_for',
				'type' => 'input',
				'name' => 'param_2'
			),
			array(
				'title' => 'generate_submenu',
				'tooltip' => 'tts_generate_submenu',
				'type' => 'megabox', // :)
				'name' => 'param_3'
			),
			array(
				'title' => 'popup_direction',
				'type' => 'select',
				'name' => 'param_4',
				'values' => array(
					'right' => 'right',
					'left' => 'left'
				),
			),
		),
		'has_localization' => true,
		'multi_level' => true,
	),
);

?>