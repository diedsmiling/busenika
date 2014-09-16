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
// $Id: vendor.php 9088 2010-03-15 10:40:51Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array (
	'controllers' => array (
		'auth' => array (
			'permissions' => true,
		),
		'index' => array (
			'permissions' => true,
		),
		'file_browser' => array (
			'permissions' => true,
		),
	
		'profiles' => array (
			'modes' => array (
				'update_cards' => array (
					'permissions' => false
				),
				'delete_profile' => array (
					'permissions' => false
				),
				'delete_card' => array (
					'permissions' => false
				),
				'request_usergroup' => array (
					'permissions' => false
				),
				'manage' => array (
					'param_permissions' => array(
						'extra' => array(
							'user_type=P' => false,
						),
						'permission' => true,
					),
				),
			),
			'permissions' => true,
		),
		'companies' => array (
			'modes' => array (
				'add' => array (
					'permissions' => false
				),
				'delete' => array (
					'permissions' => false
				),
			),
			'permissions' => true,
		),
		'profile_fields' => array (
			/*'modes' => array (
				'manage' => array (
					'permissions' => true
				),
			),*/
			'permissions' => false,
		),
		'usergroups' => array (
			/*'modes' => array (
				'manage' => array (
					'permissions' => true
				),
				'assign_privileges' => array (
					'permissions' => array ('GET' => true, 'POST' => false),
				),
				'update_status' => array (
					'permissions' => true,
				),
			),*/
			'permissions' => false,
		),
		
		'categories' => array (
			'modes' => array (
				'delete' => array (
					'permissions' => false
				),
				// Why .add was true ???
				'add' => array (
					'permissions' => false
				),
				'm_add' => array (
					'permissions' => false
				),
				'm_update' => array (
					'permissions' => false
				),
				'picker' => array (
					'permissions' => true
				),
			),
			'permissions' => array ('GET' => true, 'POST' => false),
		),
		
		'taxes' => array (
			'modes' => array(
				'update' => array(
					'permissions' => array ('GET' => true, 'POST' => false),
				),
				'manage' => array(
					'permissions' => true,
				),
			),
			'permissions' => false,
		),
		
		'states' => array (
			'modes' => array(
				'manage' => array(
					'permissions' => true,
				),
			),
			'permissions' => false,
		),
		
		'countries' => array (
			'modes' => array(
				'manage' => array(
					'permissions' => true,
				),
			),
			'permissions' => false,
		),
		
		'destinations' => array (
			'modes' => array(
				'update' => array(
					'permissions' => array ('GET' => true, 'POST' => false),
				),
				'manage' => array(
					'permissions' => true,
				),
			),
			'permissions' => false,
		),
		
		'localizations' => array (
			/*'modes' => array(
				'update' => array(
					'permissions' => array ('GET' => true, 'POST' => false),
				),
				'manage' => array(
					'permissions' => true,
				),
			),*/
			'permissions' => false,
		),
		
		'languages' => array (
			/*'modes' => array(
				'manage' => array(
					'permissions' => true,
				),
			),*/
			'permissions' => false,
		),
		
		'product_features' => array (
			'modes' => array(
				'update' => array(
					'permissions' => array ('GET' => true, 'POST' => false),
				),
				'manage' => array(
					'permissions' => true,
				),
			),
			'permissions' => false,
		),
		
		'statuses' => array (
			/*'modes' => array(
				'update' => array(
					'permissions' => array ('GET' => true, 'POST' => false),
				),
				'manage' => array(
					'permissions' => true,
				),
			),*/
			'permissions' => false,
		),
		
		'currencies' => array (
			'modes' => array(
				'update' => array(
					'permissions' => array ('GET' => true, 'POST' => false),
				),
				'manage' => array(
					'permissions' => true,
				),
			),
			'permissions' => false,
		),
		'exim' => array (
			'modes' => array(
				'export' => array(
					'permissions' => true,
				),
				'import' => array(
					'permissions' => true,
				),
			),
			'permissions' => true,
		),
		
		'product_filters' => array (
			'modes' => array(
				'update' => array(
					'permissions' => array ('GET' => true, 'POST' => false),
				),
				'manage' => array(
					'permissions' => true,
				),
			),
			'permissions' => true,
		),
		
		'orders' => array (
			'modes' => array(
				'details' => array(
					'permissions' => true,
				),
				'manage' => array(
					'permissions' => true,
				),
			),
			'permissions' => true,
		),
		
		'shippings' => array (
			'permissions' => true,
		),
		
		'tags' => array (
			'modes' => array(
				'list' => array(
					'permissions' => true,
				),
			),
			'permissions' => false,
		),

		'pages' => array (
			'modes' => array(
				/*'m_add' => array(
					'permissions' => false,
				),
				'm_update' => array(
					'permissions' => false,
				),*/
			),
			'permissions' => true,
		),

		'products' => array (
			'modes' => array(
				'm_add' => array(
					'permissions' => false,
				),
				'm_update' => array(
					'permissions' => false,
				),
			),
			'permissions' => true,
		),
		
		'product_options' => array (
			'permissions' => true,
		),
	
		
		'promotions' => array (
			'permissions' => true,
		),
		
		'shipments' => array (
			'permissions' => true,
		),
		
		'attachments' => array (
			'permissions' => true,
		),
		
		'block_manager' => array (
			'modes' => array(
				'manage' => array(
					'permissions' => false
				),
			),
			'permissions' => true,
		),
		
		'discussion_manager' => array (
			'modes' => array(
				'manage' => array(
					'permissions' => false
				),
			),
			'permissions' => true,
			//'permissions' => false,
		),

		'discussion' => array(
			'modes' => array(
				'update_posts' => array(
					'permissions' => true
				),
				'add_post' => array(
					'permissions' => true
				),
				'delete_posts' => array(
					'permissions' => true
				),
			),
			'permissions' => false,
		),
		
		'tools' => array (
			'modes' => array (
				'update_status' => array (
					'param_permissions' => array (
						'table_names' => array (
							'shippings' => true,
							'products' => true,
							'product_options' => true,
							'attachments' => true,
							'product_files' => true,
							//'users' => true,
							/*'categories' => 'manage_catalog',
							'states' => 'manage_locations',
							'usergroups' => 'manage_usergroups',
							'currencies' => 'manage_currencies',
							'blocks' => 'edit_templates',
							'pages' => 'manage_pages',
							'taxes' => 'manage_taxes',
							'promotions' => 'manage_promotions',
							'static_data' => 'manage_static_data',
							'statistics_reports' => 'manage_reports',
							'countries' => 'manage_locations',
							
							'languages' => 'manage_languages',
							'revisions_workflows' => 'manage_revisions',
							'revisions_workflows_queue' => 'manage_revisions',
							'sitemap_sections' => 'manage_sitemap',
							'localizations' => 'manage_locations',
							'products' => 'manage_catalog',
							'destinations' => 'manage_locations',
							'product_options' => 'manage_catalog',
							'product_features' => 'manage_catalog',
							'payments' => 'manage_payments',
							'product_filters' => 'manage_catalog',
							'product_files' => 'manage_catalog'
							*/
						)
					)
				)
			)
		)
	),
	
	'addons' => array (
		'affiliate' => array(
			'permission' => false,
		),
		'suppliers' => array(
			'permission' => false,
		),
		'access_restrictions' => array(
			'permission' => false,
		),
		'age_verification' => array(
			'permission' => false,
		),
		'anti_fraud' => array(
			'permission' => false,
		),
		'banners' => array(
			'permission' => false,
		),
		'bestsellers' => array(
			'permission' => false,
		),
		'customers_also_bought' => array(
			'permission' => false,
		),
		'form_builder' => array(
			'permission' => false,
		),
		'gift_registry' => array(
			'permission' => false,
		),
		'google_analytics' => array(
			'permission' => false,
		),
		'google_sitemap' => array(
			'permission' => false,
		),
		'hot_deals_block' => array(
			'permission' => false,
		),
		'live_help' => array(
			'permission' => false,
		),
		'news_and_emails' => array(
			'permission' => false,
		),
		'barcode' => array(
			'permission' => false,
		),
		'polls' => array(
			'permission' => false,
		),
		'quickbooks' => array(
			'permission' => false,
		),
		'reward_points' => array(
			'permission' => false,
		),
		'send_to_friend' => array(
			'permission' => false,
		),
		'sms_notifications' => array(
			'permission' => false,
		),
		'store_locator' => array(
			'permission' => false,
		),
		'webmail' => array(
			'permission' => false,
		),
	),
);

?>