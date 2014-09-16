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
// $Id: sitemap.php 10229 2010-07-27 14:21:39Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

/** Inclusions **/
/** /Inclusions **/

/** Body **/

$section_id = isset($_REQUEST['section_id']) ? intval($_REQUEST['section_id']) : '0';
$link_id = isset($_REQUEST['link_id']) ? intval($_REQUEST['link_id']) : '0';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$suffix = '';

	//
	// Add section links
	//
	if ($mode == 'add_links') {
		if (isset($_REQUEST['add_link_data'])) {
			foreach ($_REQUEST['add_link_data'] as $k => $v) {
				if (!empty($v['link'])) {
					$v['section_id'] = $section_id;
					$__sid = db_query("INSERT INTO ?:sitemap_links ?e", $v);

					if (!empty($__sid)) {
						$_data = array(
							'object' => $v['link'],
							'object_id' => $__sid,
							'object_holder' => 'sitemap_links'
						);

						foreach ((array)Registry::get('languages') as $_data['lang_code'] => $_v) {
							db_query("INSERT INTO ?:common_descriptions ?e", $_data);
						}
					}
				}
			}
		}

		$suffix = ".update?section_id=$section_id";
	}
	//
	// Update section links
	//
	if ($mode == 'update_links') {
		if (isset($_REQUEST['link_data'])) {
			foreach ($_REQUEST['link_data'] as $k => $v) {
				$v['section_id'] = $section_id;
				db_query("UPDATE ?:sitemap_links SET ?u WHERE link_id = ?i", $v, $k);

				$v['object'] = $v['link'];
				db_query("UPDATE ?:common_descriptions SET ?u WHERE object_id = ?i AND lang_code = ?s AND object_holder = 'sitemap_links'", $v, $k, DESCR_SL);
			}
			unset($_data);
		}

		$suffix = ".update?section_id=$section_id";
	}
	//
	// Delete section links
	//
	if ($mode == 'delete_links') {
		if (!empty($_REQUEST['link_ids'])) {
			fn_delete_sitemap_links($_REQUEST['link_ids']);
		}

		$suffix = ".update?section_id=$section_id";
	}

	//
	// Add sitemap sections
	//
	if ($mode == 'add_sitemap_sections') {
		if (isset($_REQUEST['add_section_data'])) {
			foreach ($_REQUEST['add_section_data'] as $k => $v) {
				if (!empty($v['section'])) {
					$_sid = db_query("INSERT INTO ?:sitemap_sections ?e", $v);
					if (!empty($_sid)) {

						$_data = array(
							'object' => $v['section'],
							'object_id' => $_sid,
							'object_holder' => 'sitemap_sections'
						);

						foreach ((array)Registry::get('languages') as $_data['lang_code'] => $_v) {
							db_query("INSERT INTO ?:common_descriptions ?e", $_data);
						}
					}
				}
			}
		}

		$suffix = '.manage';
	}
	//
	// Update sitemap sections
	//
	if ($mode == 'update_sitemap_sections') {
		if (isset($_REQUEST['section_data'])) {
			foreach ($_REQUEST['section_data'] as $k => $v) {
				db_query("UPDATE ?:sitemap_sections SET ?u WHERE section_id = ?i", $v, $k);
				$v['object'] = $v['section'];
				db_query("UPDATE ?:common_descriptions SET ?u WHERE object_id = ?i AND lang_code = ?s AND object_holder = 'sitemap_sections'", $v, $k, DESCR_SL);
			}
			unset($_data);
		}

		$suffix = '.manage';
	}
	//
	// Delete sitemap sections
	//
	if ($mode == 'delete_sitemap_sections') {
		if (!empty($_REQUEST['section_ids'])) {
			fn_delete_sitemap_sections($_REQUEST['section_ids']);
		}
		$suffix = '.manage';
	}

	return array(CONTROLLER_STATUS_OK, "sitemap$suffix");
}

// -------------------------------------- GET requests -------------------------------

// Collect section methods data
if ($mode == 'update') {
	if (empty($_REQUEST['section_id'])) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	$section = db_get_row("SELECT s.*, c.object as section FROM ?:sitemap_sections as s LEFT JOIN ?:common_descriptions as c ON c.object_id = s.section_id AND object_holder = ?s AND lang_code = ?s WHERE s.section_id = ?i", 'affiliate_plans', DESCR_SL, $_REQUEST['section_id']);

	$view->assign('section', $section);


	$links = db_get_array("SELECT link_id, link_href, section_id, status, position, link_type, description, object as link FROM ?:sitemap_links LEFT JOIN ?:common_descriptions ON ?:common_descriptions.object_id = ?:sitemap_links.link_id AND ?:common_descriptions.object_holder = 'sitemap_links' AND ?:common_descriptions.lang_code = ?s WHERE section_id = ?i ORDER BY position, link", DESCR_SL, $section_id);
	$view->assign('links', $links);

	fn_add_breadcrumb(fn_get_lang_var('sitemap'),"sitemap.manage");

// Show all section methods
} elseif ($mode == 'manage') {

	$view->assign('sitemap_sections', db_get_array("SELECT *, object as section FROM ?:sitemap_sections LEFT JOIN ?:common_descriptions ON ?:common_descriptions.object_id = ?:sitemap_sections.section_id AND ?:common_descriptions.object_holder = 'sitemap_sections' AND ?:common_descriptions.lang_code = ?s ORDER BY position, section", DESCR_SL));

} elseif ($mode == 'delete_sitemap_section') {
	if (!empty($_REQUEST['section_id'])) {
		fn_delete_sitemap_sections((array)$_REQUEST['section_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "sitemap.manage");

} elseif ($mode == 'delete_link') {
	if (!empty($_REQUEST['link_id'])) {
		fn_delete_sitemap_links((array)$_REQUEST['link_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "sitemap.update?section_id=$section_id");
}

/** /Body **/

function fn_delete_sitemap_links($link_ids)
{
	db_query("DELETE FROM ?:sitemap_links WHERE link_id IN (?n)", $link_ids);
	db_query("DELETE FROM ?:common_descriptions WHERE object_holder = 'sitemap_links' AND object_id IN (?n)", $link_ids);
}

function fn_delete_sitemap_sections($section_ids)
{
	db_query("DELETE FROM ?:sitemap_sections WHERE section_id IN (?n)", $section_ids);
	db_query("DELETE FROM ?:common_descriptions WHERE object_holder = 'sitemap_sections' AND object_id IN (?n)", $section_ids);

	$links = db_get_fields("SELECT link_id FROM ?:sitemap_links WHERE section_id IN (?n)", $section_ids);
	if (!empty($links)) {
		db_query("DELETE FROM ?:sitemap_links WHERE section_id IN (?n)", $section_ids);
		db_query("DELETE FROM ?:common_descriptions WHERE object_holder = 'sitemap_links' AND object_id IN (?n)", $links);
	}
}

?>