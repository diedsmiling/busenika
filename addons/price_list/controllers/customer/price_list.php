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
// $Id: price_list.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($mode == 'view') {
	if (empty($_REQUEST['display'])) {
		die('Access denied');
	}
	
	$types = array('pdf', 'xls');
	
	$price_schema = fn_get_schema('price_list', 'schema', 'php', false);
	$selected_fields = Registry::get('addons.price_list.price_list_fields');
	
	$modes = fn_price_list_get_pdf_layouts();
	
	// Check the available libs
	foreach ($types as $type) {
		if (!empty($modes[$type])) {
			foreach ($modes[$type] as $f_mode) {
				if ($_REQUEST['display'] == basename($f_mode, '.php')) {
					include_once DIR_ADDONS . '/price_list/templates/' . $type . '/' . $f_mode;

					$meta_redirect_url = urlencode(fn_url('price_list.view?display=' . basename($f_mode, '.php'), 'C', 'http', '&'));
					
					if (!empty($_REQUEST['return_url'])) {
						$base_url = $_REQUEST['return_url'];
					} else {
						$base_url = (empty($_SERVER['HTTP_REFERER']) ? $index_script . '?' : $_SERVER['HTTP_REFERER']);
						$base_url = fn_query_remove($base_url, 'cc');

						if (strpos('?', $base_url) === false) {
							$base_url .= '?';
						}
					}
					
					return array(CONTROLLER_STATUS_REDIRECT, $base_url . '&meta_redirect_url=' . $meta_redirect_url);
				}
			}
		}
	}
}

?>