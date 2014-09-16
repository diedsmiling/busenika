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
// $Id: admin.php 9088 2010-03-15 10:40:51Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array (
	'orders' => array (
		'modes' => array (
			'update_status' => array (
				'permissions' => 'change_order_status'
			),
			'delete_orders' => array (
				'permissions' => 'delete_orders'
			),
			'delete' => array (
				'permissions' => 'delete_orders'
			),
			'bulk_print' => array (
				'permissions' => 'view_orders'
			),
			'remove_all_cc_info' => array (
				'permissions' => 'edit_orders'
			),
			'remove_cc_info' => array (
				'permissions' => 'edit_orders'
			),
		),
		'permissions' => 'view_orders'
	),
	'taxes' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_taxes'
			),
		),
		'permissions' => array ('GET' => 'view_taxes', 'POST' => 'manage_taxes'),
	),
	'sitemap' => array (
		'permissions' => 'manage_sitemap',
	),
	'database' => array (
		'permissions' => 'database_maintenance',
	),
	'product_options' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_catalog'
			)
		),
		'permissions' => array ('GET' => 'view_catalog', 'POST' => 'manage_catalog'),
	),
	'products' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_catalog'
			),
			'clone' => array (
				'permissions' => 'manage_catalog'
			),
			'add' => array (
				'permissions' => 'manage_catalog'
			),
			'manage' => array (
				'permissions' => 'view_catalog'
			),
		),
		'permissions' => array ('GET' => 'view_catalog', 'POST' => 'manage_catalog'),
	),
	'product_filters' => array(
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_catalog'
			),
		),
		'permissions' => array ('GET' => 'view_catalog', 'POST' => 'manage_catalog'),
	),
	'shippings' => array (
		'modes' => array (
			'delete_shipping' => array (
				'permissions' => 'manage_shipping'
			),
		),
		'permissions' => array ('GET' => 'view_shipping', 'POST' => 'manage_shipping'),
	),
	'usergroups' => array (
		'modes' => array (
			'update_status' => array (
				'permissions' => 'manage_usergroups'
			),
			'delete' => array (
				'permissions' => 'manage_usergroups'
			),
		),
		'permissions' => array ('GET' => 'view_usergroups', 'POST' => 'manage_usergroups'),
	),
	'site_layout' => array (
		'permissions' => 'manage_site_layout',
	),
	'profiles' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_users'
			),
			'delete_profile' => array (
				'permissions' => 'manage_users'
			),
			'm_delete' => array (
				'permissions' => 'manage_users'
			),
			'add' => array (
				'permissions' => 'manage_users'
			),
			'update' => array (
				'permissions' => array('GET' => 'view_users', 'POST' => 'manage_users'),
				'condition' => array(
					'operator' => 'or',
					'function' => array('fn_check_permission_manage_own_profile'),
				),
			),
			'update_status' => array (
				'permissions' => 'manage_users'
			),
			'manage' => array (
				'permissions' => 'view_users'
			),
			'export_range' => array (
				'permissions' => 'exim_access'
			),
			'act_as_user' => array(
				'permissions' => 'manage_users'
			)
		),
	),
	'cart' => array (
		'permissions' => array ('GET' => 'view_users', 'POST' => 'manage_users'),
	),
	'pages' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_pages'
			)
		),
		'permissions' => array ('GET' => 'view_pages', 'POST' => 'manage_pages'),
	),
	'profile_fields' => array (
		'permissions' => array ('GET' => 'view_users', 'POST' => 'manage_users'),
	),
	'logs' => array (
		'modes' => array (
			'clean' => array (
				'permissions' => 'delete_logs'
			)
		),
		'permissions' => 'view_logs',
	),
	'categories' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_catalog'
			)
		),
		'permissions' => array ('GET' => 'view_catalog', 'POST' => 'manage_catalog'),
	),
	'settings' => array (
		'permissions' => array ('GET' => 'view_settings', 'POST' => 'update_settings'),
	),
	'upgrade_center' => array (
		'permissions' => 'upgrade_store',
	),
	'payments' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_payments'
			)
		),
		'permissions' => array ('GET' => 'view_payments', 'POST' => 'manage_payments'),
	),
	'currencies' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_currencies'
			)
		),
		'permissions' => array ('GET' => 'view_currencies', 'POST' => 'manage_currencies'),
	),
	'destinations' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_locations'
			)
		),
		'permissions' => array ('GET' => 'view_locations', 'POST' => 'manage_locations'),
	),
	'localizations' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_locations'
			)
		),
		'permissions' => array ('GET' => 'view_locations', 'POST' => 'manage_locations'),
	),
	'exim' => array (
		'permissions' => 'exim_access',
	),
	'languages' => array (
		'modes' => array (
			'delete_variable' => array (
				'permissions' => 'manage_languages'
			),
			'delete_language' => array (
				'permissions' => 'manage_languages'
			)
		),
		'permissions' => array ('GET' => 'view_languages', 'POST' => 'manage_languages'),
	),
	'product_features' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_catalog'
			)
		),
		'permissions' => array ('GET' => 'view_catalog', 'POST' => 'manage_catalog'),
	),
	'static_data' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_static_data'
			)
		),
		'permissions' => array ('GET' => 'view_static_data', 'POST' => 'manage_static_data'),
	),
	'skin_selector' => array (
		'permissions' => 'select_skins',
	),
	'statuses' => array (
		'permissions' => 'manage_order_statuses',
	),
	'sales_reports' => array (
		'modes' => array (
			'reports_list' => array (
				'permissions' => 'manage_reports'
			),
			'report' => array (
				'permissions' => 'manage_reports'
			),
			'table' => array (
				'permissions' => 'manage_reports'
			),
			'reports_view' => array (
				'permissions' => 'view_reports'
			),
			'reports' => array (
				'permissions' => 'view_reports'
			),
		),
	),
	'settings_dev' => array (
		'permissions' => 'update_settings',
	),
	'addons' => array (
		'permissions' => 'update_settings',
	),
	'states' => array (
		'modes' => array (
			'delete' => array (
				'permissions' => 'manage_locations'
			)
		),
		'permissions' => array ('GET' => 'view_locations', 'POST' => 'manage_locations'),
	),
	'countries' => array (
		'permissions' => array ('GET' => 'view_locations', 'POST' => 'manage_locations'),
	),
	'order_management' => array (
		'modes' => array (
			'edit' => array (
				'permissions' => 'edit_order'
			),
			'new' => array (
				'permissions' => 'create_order'
			),
		),
	),
	'template_editor' => array (
		'permissions' => 'edit_templates',
	),
	'block_manager' => array (
		'permissions' => 'edit_templates',
	),
	'promotions' => array (
		'permissions' => 'manage_promotions',
	),
	'revisions' => array (
		'permissions' => 'manage_revisions',
	),
	'revisions_workflow' => array (
		'permissions' => 'manage_revisions',
	),
	'shipments' => array (
		'modes' => array (
			'manage' => array (
				'permissions' => 'view_orders',
			),
			'delete' => array (
				'permissions' => 'edit_order',
			),
			'picker' => array (
				'permissions' => 'edit_order',
			),
		),
		'permissions' => 'view_orders',
	),
	'tools' => array (
		'modes' => array (
			'update_status' => array (
				'param_permissions' => array (
					'table_names' => array (
						'categories' => 'manage_catalog',
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
						'shippings' => 'manage_shipping',
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
					)
				)
			)
		)
	)
);

?>