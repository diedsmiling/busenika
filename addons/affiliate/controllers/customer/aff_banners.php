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
// $Id: aff_banners.php 10229 2010-07-27 14:21:39Z 2tl $
//
if ( !defined('AREA') )	{ die('Access denied');	}

if ($mode == 'view') {

	if (!empty($_REQUEST['bid'])) {
		$banner = fn_get_aff_banner_data($_REQUEST['bid'], CART_LANGUAGE, true);
		$banner_correct = true;
		
		if (empty($banner) || $banner['status'] == 'D') {
			return array(CONTROLLER_STATUS_REDIRECT, Registry::get('config.http_location') . '/' . Registry::get('config.customer_index'));
		}

		if (!empty($banner['banner_id']) && !empty($_REQUEST['aff_id'])) {
			$_SESSION['partner_data'] = array(
				'banner_id' => $banner['banner_id'],
				'partner_id' => $_REQUEST['aff_id'],
				'is_payouts' => 'N',
				'product_id' => @$_REQUEST['product_id'], // FIXME: do we need it?
			);
		}

		if (!empty($banner['type']) && $banner['type'] == 'P') {
			if (!empty($_REQUEST['product_id'])) {
				if (!empty($banner['to_cart']) && $banner['to_cart'] == 'Y') {
					if (empty($_SESSION['cart'])) {
						fn_clear_cart($_SESSION['cart']);
					}

					fn_add_product_to_cart(array(
						$_REQUEST['product_id'] => array(
							'product_id' => $_REQUEST['product_id'],
							'amount' => 1
						)
					), $_SESSION['cart'], $auth);

					$redirect_url = "checkout.cart";
				} else {
					$redirect_url = "products.view?product_id=$_REQUEST[product_id]";
				}
			} else {
				$banner_correct = false;
				$banner['type'] = 'T';
				$banner['link_to'] = 'U';
				$banner['url'] = Registry::get('config.http_location');
			}
		}

		if (!empty($banner['link_to']) && $banner['type'] != 'P') {
			$link_to = $banner['link_to'];
			$data = &$banner;

			if ($link_to == 'G' && !empty($banner['group_id'])) {
				$group = fn_get_group_data($banner['group_id'], true);
				
				if (empty($group) || $group['status'] == 'D') {
					return array(CONTROLLER_STATUS_REDIRECT, Registry::get('config.http_location') . '/' . Registry::get('config.customer_index'));
				}
				
				$link_to = @$group['link_to'];
				if (!empty($group['product_ids'])) {
					$group['products'] = fn_get_product_name($group['product_ids']);
				}
				$data = &$group;
				
			} elseif ($link_to == 'G' && empty($banner['group_id'])) {
				return array(CONTROLLER_STATUS_REDIRECT, Registry::get('config.http_location') . '/' . Registry::get('config.customer_index'));
			}

			if ($link_to == 'U') {
				$redirect_url = empty($data['url']) ? '' : $data['url'];

			} elseif ($link_to == 'P') {
				if (empty($data['products'])) {
					$data['products'] = array();
				}

				if (count($data['products']) == 1) {
					$redirect_url = "products.view?product_id=" . key($data['products']);
				}
				$not_redirect = 'Y';

				$params = array (
					'pid' => array_keys($data['products']),
					'type' => 'extended',
					'sort_by' => empty($_REQUEST['sort_by']) ? '' : $_REQUEST['sort_by'],
					'sort_order' => empty($_REQUEST['sort_order']) ? '' : $_REQUEST['sort_order'],
				);

				list($products, $search) = fn_get_products($params, Registry::get('settings.Appearance.products_per_page'));

				if (!empty($products)) {
					foreach ($products as $k => $v) {
						fn_gather_additional_product_data($products[$k], true);
					}
				}

				$view->assign('products', $products);
				$view->assign('search', $search);
				
				$selected_layout = fn_get_products_layout($_REQUEST);
				$view->assign('selected_layout', $selected_layout);

			} elseif ($link_to == 'C') {
				if (!empty($data['categories']) && is_array($data['categories'])) {
					$first_category_id = key($data['categories']);
					if (count($data['categories']) == 1 && !empty($first_category_id)) {
						$redirect_url = "categories.view?category_id=" . key($data['categories']);
					} else {
						$b_categories = array();
						foreach ($data['categories'] as $category_id => $category_name) {
							$b_categories[$category_id] = fn_get_category_data($category_id, CART_LANGUAGE);
						}
						$not_redirect = 'Y';

						$view->assign('banner_categories', $b_categories);
					}
					unset($first_category_id);
				}
			}
		}

		if ((!empty($redirect_url) || !empty($not_redirect)) && !empty($banner['banner_id']) && !empty($_REQUEST['aff_id']) && $banner_correct) {
			fn_add_partner_action('click', $banner['banner_id'], $_REQUEST['aff_id'], $auth['user_id'], array('R' => @$_SERVER['HTTP_REFERER']));
		}

		if (!empty($redirect_url)) {
			return array(CONTROLLER_STATUS_REDIRECT, $redirect_url, true);
		}
	}
}

?>