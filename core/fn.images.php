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
// $Id: fn.images.php 10608 2010-09-06 11:34:15Z andyye $
//

if ( !defined('AREA') )	{ die('Access denied');	}

//
// Get image
//
function fn_get_image($image_id, $object_type, $rev_data = array(), $lang_code = CART_LANGUAGE)
{
	$table = 'images';
	$cond = '';
	$path = $object_type;

	if (!empty($rev_data)) {
		$table = 'rev_images';
		$cond = db_quote(" AND ?:$table.revision = ?s AND ?:$table.revision_id = ?i", $rev_data['revision'], $rev_data['revision_id']);
		$path .= '_rev';
	}

	$path .= "/" . floor($image_id / MAX_FILES_IN_DIR);

	if (!empty($image_id) && !empty($object_type)) {
		$image_data = db_get_row("SELECT ?:$table.image_path, ?:common_descriptions.description as alt, ?:$table.image_x, ?:$table.image_y FROM ?:$table LEFT JOIN ?:common_descriptions ON ?:common_descriptions.object_id = ?:$table.image_id AND ?:common_descriptions.object_holder = 'images' AND ?:common_descriptions.lang_code = ?s  WHERE ?:$table.image_id = ?i ?p", $lang_code, $image_id, $cond);

		if (!empty($image_data['image_path'])) {
			if (fn_get_file_ext($image_data['image_path']) == 'swf') { // FIXME, dirty
				$image_data['is_flash'] = true;
			}

			$image_data['http_image_path'] = Registry::get('config.http_images_path') . $path . '/' . $image_data['image_path'];
			$image_data['absolute_path'] = DIR_IMAGES . $path . '/' . $image_data['image_path'];
			$image_data['image_path'] = Registry::get('config.images_path') . $path . '/' . $image_data['image_path'];
		}
	}
	
	return (!empty($image_data) ? $image_data : false);
}

//
// Create/Update image
//
function fn_update_image($image_data, $image_id = '0', $image_type = 'product', $rev_data = array (), $lang_code = CART_LANGUAGE)
{
	$table = 'images_links';
	$itable = 'images';
	$images_path = $image_type . '/';
	$cond = '';
	$_data = array();

	if (!empty($rev_data)) {
		$table = 'rev_images_links';
		$itable = 'rev_images';
		$images_path = $image_type . '_rev/';
		$cond = db_quote(" AND revision = ?s AND revision_id = ?i", $rev_data['revision'], $rev_data['revision_id']);
		$_data['revision'] = $rev_data['revision'];
		$_data['revision_id'] = $rev_data['revision_id'];
	}

	if (empty($image_id)) {
		$max_id = db_get_field("SELECT MAX(image_id) FROM ?:$itable");
		$images_path .= floor($max_id / MAX_FILES_IN_DIR) . "/";
	} else {
		$images_path .= floor($image_id / MAX_FILES_IN_DIR) . "/";
	}

	if (!fn_mkdir(DIR_IMAGES . $images_path)) {
		return false;
	}

	list($_data['image_x'], $_data['image_y'], $mime_type) = fn_get_image_size($image_data['path']);

	// Get the real image type
	$ext = fn_get_image_extension($mime_type);
	if (strpos($image_data['name'], '.') !== false) {
		$image_data['name'] = substr_replace($image_data['name'], $ext, strrpos($image_data['name'], '.') + 1);
	} else {
		$image_data['name'] .= '.' . $ext;
	}

	$fd = fopen($image_data['path'], "rb", true);
	if (!empty($fd)) {
		// Check if image path already set
		$image_path = db_get_field("SELECT image_path FROM ?:$itable WHERE image_id = ?i ?p", $image_id, $cond);

		// Delete image file if already exists
		if ($image_path != $image_data['name'] && empty($rev_data)) {
			fn_delete_file(DIR_IMAGES . $images_path . $image_path);
		}

		// Generate new filename if file with the same name is already exists
		if (file_exists(DIR_IMAGES . $images_path . $image_data['name']) && $image_path != $image_data['name']) {
			$image_data['name'] = substr_replace($image_data['name'], uniqid(time()) . '.', strrpos($image_data['name'], '.'), 1);
		}

		$_data['image_path'] = $image_data['name'];
		if (@fn_rename($image_data['path'], DIR_IMAGES . $images_path . $image_data['name']) == false) {
			fn_copy($image_data['path'], DIR_IMAGES . $images_path . $image_data['name']);
			@unlink($image_data['path']);
		}

		fclose($fd);
	}

	$_data['image_size'] = $image_data['size'];
	$_data['image_path'] = empty($_data['image_path']) ? '' : fn_normalize_path($_data['image_path']);

	if (!empty($image_id)) {
		db_query("UPDATE ?:$itable SET ?u WHERE image_id = ?i ?p", $_data, $image_id, $cond);
	} else {
		$image_id = db_query("INSERT INTO ?:$itable ?e", $_data);
	}

	return $image_id;
}

//
// Delete image
//
function fn_delete_image($image_id, $pair_id, $object_type = 'product', $rev_data = array())
{
	$table = 'images_links';
	$itable = 'images';
	$cond = '';
	$path = DIR_IMAGES . $object_type . '/';

	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working')) {
		$revisions = Registry::get('revisions');
		$_img_data = db_get_row("SELECT object_type, object_id FROM ?:rev_images_links WHERE pair_id = ?i ORDER BY revision DESC LIMIT 1", $pair_id);

		if (!empty($_img_data['object_type']) && !empty($revisions['objects'][$_img_data['object_type']]) && !empty($revisions['objects'][$_img_data['object_type']]['tables'])) {
			$object_data = $revisions['objects'][$_img_data['object_type']];

			if ($object_data['images']) {
				if (empty($rev_data)) {
					$entry = array (
						$object_data['key'] => $_img_data['object_id']
					);

					list($revision, $revision_id) = fn_revisions_get_last($_img_data['object_type'], $entry, 0, $itable);
				} else {
					$revision = $rev_data['revision'];
					$revision_id = $rev_data['revision_id'];
				}
				if (!empty($revision_id)) {
					$table = 'rev_images_links';
					$itable = 'rev_images';
					$cond = db_quote(" AND revision = ?s AND revision_id = ?i", $revision, $revision_id);
					$path = DIR_IMAGES . $object_type . '_rev/';
				}
			}
		}
	}

	$path .= floor($image_id / MAX_FILES_IN_DIR) . "/";

	$_image_file = db_get_field("SELECT image_path FROM ?:$itable WHERE image_id = ?i ?p", $image_id, $cond);

	if (!empty($_image_file)) {

		if (!empty($revision_id)) {
			$use_count = db_get_field("SELECT COUNT(image_path) FROM ?:$itable WHERE image_id = ?i AND image_path = ?s", $image_id, $_image_file);
			if ($use_count == 1) {
				fn_delete_file($path . $_image_file);
			}
		} else {
			fn_delete_file($path . $_image_file);
		}
	}
	$dir_content = fn_get_dir_contents($path, true, true);
	if (empty($dir_content)) {
		fn_rm($path);
	}

	db_query("DELETE FROM ?:$itable WHERE image_id = ?i ?p", $image_id, $cond);
	db_query("DELETE FROM ?:common_descriptions WHERE object_id = ?i AND object_holder = 'images'", $image_id);
	db_query("UPDATE ?:$table SET " . ($object_type == 'detailed' ? 'detailed_id' : 'image_id') . " = '0' WHERE pair_id = ?i ?p", $pair_id, $cond);

	$_ids = db_get_row("SELECT image_id, detailed_id FROM ?:$table WHERE pair_id = ?i ?p", $pair_id, $cond);

	if (empty($_ids['image_id']) && empty($_ids['detailed_id'])) {
		db_query("DELETE FROM ?:$table WHERE pair_id = ?i ?p", $pair_id, $cond);
	}

	return true;
}

//
// Get image pair(s)
//
// @object_id - object id
// @object_type - type of object (product, category etc...)
// @pair_type - main (M) or additional (A)
//
function fn_get_image_pairs($object_id, $object_type, $pair_type, $get_icon = true, $get_detailed = true, $lang_code = CART_LANGUAGE)
{
	$table = 'images_links';
	$itable = 'images';
	$cond = '';
	$rev_data = array();

	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working')) {
		$revisions = Registry::get('revisions');

		if (!empty($object_type)) {
			if (!empty($revisions['objects'][$object_type]) && !empty($revisions['objects'][$object_type]['tables'])) {
				$object_data = $revisions['objects'][$object_type];
			}
			$parent_object_type = '';
			$parent_object_id = 0;
			foreach ($revisions['objects'] as $_obj_type => $_obj_desc) {
				if (isset($_obj_desc['image_objects']) && !empty($_obj_desc['image_objects'][$object_type])) {
					$object_data = $_obj_desc;
					$parent_object_type = $_obj_type;
					$parent_object_id = db_get_field("SELECT " . $_obj_desc['key'] . " FROM ?:rev_" . $_obj_desc['image_objects'][$object_type]['main_table'] . " WHERE " . $_obj_desc['image_objects'][$object_type]['key'] . " = ?i", $object_id);
					break;
				}
			}

			if (isset($object_data) && $object_data['images']) {
				if (empty($_REQUEST['rev'][$object_type]) && empty($_REQUEST['rev_id'][$object_type])) {
					$rev_data = db_get_row("SELECT MAX(revision) as revision, revision_id FROM ?:revisions WHERE object = ?s AND object_id = ?i GROUP BY revision_id", empty($parent_object_type) ? $object_type : $parent_object_type, empty($parent_object_id) ? $object_id : $parent_object_id);
				} else {
					$rev_data = array(
						'revision' => $_REQUEST['rev'][$object_type],
						'revision_id' => $_REQUEST['rev_id'][$object_type]
					);
				}

				if (!empty($rev_data)) {
					$table = 'rev_images_links';
					$itable = 'rev_images';
					$cond = db_quote(" AND revision = ?s AND revision_id = ?i", $rev_data['revision'], $rev_data['revision_id']);
				}
			}
		}
	}

	$pair_data = db_get_hash_array("SELECT pair_id, image_id, detailed_id FROM ?:$table WHERE object_id = ?i AND object_type = ?s AND type = ?s ?p", 'pair_id', $object_id, $object_type, $pair_type, $cond);

	if (empty($pair_data)) {
		return array();
	}

	foreach ($pair_data as $pair_id => $p_data) {
		if (!empty($p_data['image_id']) && $get_icon == true) {
			$pair_data[$pair_id]['icon'] = fn_get_image($p_data['image_id'], $object_type, $rev_data, $lang_code);
		}
		if (!empty($p_data['detailed_id']) && $get_detailed == true) {
			$pair_data[$pair_id]['detailed'] = fn_get_image($p_data['detailed_id'], 'detailed', $rev_data, $lang_code);
		}
	}

	return ($pair_type == 'A') ? $pair_data : array_pop($pair_data);
}


//
// Create/Update image pairs (icon -> detailed image)
//
function fn_update_image_pairs($icons, $detailed, $pairs_data, $object_id = 0, $object_type = 'product_lists', $object_ids = array (), $parent_object_type = '', $parent_object_id = 0, $update_alt_desc = true, $force_create_thumbnail = false, $lang_code = CART_LANGUAGE)
{
	
	
	$_otype = !empty($parent_object_type) ? $parent_object_type : $object_type;

	$thumbnail_width = Registry::get("settings.Thumbnails.{$_otype}_thumbnail_width");
	$thumbnail_height = Registry::get("settings.Thumbnails.{$_otype}_thumbnail_height");
	$thumbnail_bg_color = Registry::get('settings.Thumbnails.thumbnail_background_color');

	$pair_ids = $rev_data = array();
	$table = 'images_links';
	$itable = 'images';
	$cond = '';

	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working')) {
		$revisions = Registry::get('revisions');

		if (!empty($_otype) && !empty($revisions['objects'][$_otype]) && !empty($revisions['objects'][$_otype]['tables'])) {
			$object_data = $revisions['objects'][$_otype];

			if ($object_data['images']) {
				$entry = array (
					$object_data['key'] => !empty($parent_object_id) ? $parent_object_id : $object_id
				);

				list($revision, $revision_id) = fn_revisions_get_last($_otype, $entry, 0, $table);
				if (!empty($revision_id)) {
					$table = 'rev_images_links';
					$itable = 'rev_images';
					$rev_data = array (
						'revision' => $revision,
						'revision_id' => $revision_id
					);
					$cond = db_quote(" AND revision = ?s AND revision_id = ?i", $revision, $revision_id);
				}
			}
		}
	}
	
	if (!empty($pairs_data)) {
		
		foreach ($pairs_data as $k => $p_data) {
			$data = array();
			$pair_id = !empty($p_data['pair_id']) ? $p_data['pair_id'] : 0;
			$o_id = !empty($object_id) ? $object_id : ((!empty($p_data['object_id'])) ? $p_data['object_id'] : 0);

			if ($o_id == 0 && !empty($object_ids[$k])) {
				$o_id = $object_ids[$k];
			} elseif (!empty($object_ids) && empty($object_ids[$k])) {
				continue;
			}

			// Check if main pair is exists
			if (empty($pair_id) && $p_data['type'] == 'M') {
				$pair_data = db_get_row("SELECT pair_id, image_id, detailed_id FROM ?:$table WHERE object_id = ?i AND object_type = ?s AND type = ?s ?p", $o_id, $object_type, $p_data['type'], $cond);
				$pair_id = !empty($pair_data['pair_id']) ? $pair_data['pair_id'] : 0;
			} else {
				$pair_data = db_get_row("SELECT image_id, detailed_id FROM ?:$table WHERE pair_id = ?i ?p", $pair_id, $cond);
				if (empty($pair_data)) {
					$pair_id = 0;
				}
			}


			// Update detailed image
			if (!empty($detailed[$k]) && !empty($detailed[$k]['size'])) {
				if (fn_get_image_size($detailed[$k]['path'])) {
					// Create thumbnail if not exists
					if ($force_create_thumbnail && empty($icons[$k]) && isset($thumbnail_width) && isset($thumbnail_height)) {
						fn_create_thumbnail($icons[$k], $detailed[$k], $thumbnail_width, $thumbnail_height, $thumbnail_bg_color);
					}
					$data['detailed_id'] = fn_update_image($detailed[$k], !empty($pair_data['detailed_id']) ? $pair_data['detailed_id'] : 0, 'detailed', $rev_data);
				}
			}

			// Update icon
			if (!empty($icons[$k]) && !empty($icons[$k]['size'])) {
				//$tessss = fn_get_image_size($icons[$k]['path']);
				//var_dump($tessss);
				
				
				if (fn_get_image_size($icons[$k]['path'])) {
					if ($force_create_thumbnail && Registry::get('settings.Thumbnails.resize_thumbnail') == 'Y' &&  isset($thumbnail_width) && isset($thumbnail_height)) {
						fn_resize_image($icons[$k]['path'], $icons[$k]['path'], $thumbnail_width, $thumbnail_height, true, $thumbnail_bg_color);
					}
					$data['image_id'] = fn_update_image($icons[$k], !empty($pair_data['image_id']) ? $pair_data['image_id'] : 0, $object_type, $rev_data);
					
				}
			}

			// Update alt descriptions
			if (((empty($data) && !empty($pair_id)) || !empty($data)) && $update_alt_desc == true) {
				$image_ids = array();
				if (!empty($pair_id)) {
					$image_ids = db_get_row("SELECT image_id, detailed_id FROM ?:$table WHERE pair_id = ?i ?p", $pair_id, $cond);
				}

				$image_ids = fn_array_merge($image_ids, $data);

				if (!empty($image_ids['detailed_id'])) {
					if (!is_array($p_data['detailed_alt'])) {
						$p_data['detailed_alt'] = array($lang_code => $p_data['detailed_alt']);
					}
					foreach ($p_data['detailed_alt'] as $lc => $_v) {
						fn_update_image_alt_text($image_ids['detailed_id'], $_v, $lc);
					}
				}
				if (!empty($image_ids['image_id'])) {
					if (!is_array($p_data['image_alt'])) {
						$p_data['image_alt'] = array($lang_code => $p_data['image_alt']);
					}
					foreach ($p_data['image_alt'] as $lc => $_v) {
						fn_update_image_alt_text($image_ids['image_id'], $_v, $lc);
					}
				}
			}

			if (empty($data)) {
				continue;
			}

			if (!empty($revision_id)) {
				$data['revision_id'] = $revision_id;
				$data['revision'] = $revision;
			}
			
			// Pair is exists
			if (!empty($pair_id)) {
				db_query("UPDATE ?:$table SET ?u WHERE pair_id = ?i ?p", $data, $pair_id, $cond);
			} else {
				$data['type'] = $p_data['type']; // set link type
				$data['object_id'] = $o_id; // assign pair to object
				$data['object_type'] = $object_type;
				$pair_id = db_query("INSERT INTO ?:$table ?e", $data);
			}
			
			$pair_ids[] = $pair_id;
		}
	}
	//var_dump($pair_ids);
	return $pair_ids;
	
}

function fn_update_image_alt_text($object_id, $alt_text, $lang_code = CART_LANGUAGE)
{
	$_data = array (
		'object_id' => $object_id,
		'description' => empty($alt_text) ? '' : trim($alt_text),
		'lang_code' => $lang_code,
		'object_holder' => 'images'
	);
	db_query("REPLACE INTO ?:common_descriptions ?e", $_data);
}

function fn_delete_image_pairs($object_id, $object_type, $pair_type = '')
{
	$table = 'images_links';
	$itable = 'images';
	$rev_data = array ();
	$cond = '';

	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working')) {
		$revisions = Registry::get('revisions');

		if (!empty($object_type) && !empty($revisions['objects'][$object_type]) && !empty($revisions['objects'][$object_type]['tables'])) {
			$object_data = $revisions['objects'][$object_type];

			if ($object_data['images']) {
				$entry = array (
					$object_data['key'] => $object_id
				);

				list($revision, $revision_id) = fn_revisions_get_last($object_type, $entry, 0, $table);
				if (!empty($revision_id)) {
					$table = 'rev_images_links';
					$itable = 'rev_images';
					$rev_data = array (
						'revision' => $revision,
						'revision_id' => $revision_id
					);
					$cond = db_quote(" AND revision = ?s AND revision_id = ?i", $revision, $revision_id);
				}
			}
		}
	}

	$pair_ids = db_get_fields("SELECT pair_id FROM ?:$table WHERE object_id = ?i AND object_type = ?s ?p", $object_id, $object_type, $cond);

	foreach ($pair_ids as $pair_id) {
		fn_delete_image_pair($pair_id, $object_type);
	}

	return true;
}

//
// Delete image pair
//
function fn_delete_image_pair($pair_id, $object_type = 'product', $rev_data = array())
{
	$table = 'images_links';
	$cond = '';

	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working')) {
		$revisions = Registry::get('revisions');

		if (!empty($object_type) && !empty($revisions['objects'][$object_type]) && !empty($revisions['objects'][$object_type]['tables'])) {
			$object_data = $revisions['objects'][$object_type];

			if ($object_data['images']) {
				if (empty($rev_data)) {
					$object_id = db_get_field("SELECT object_id FROM ?:rev_images_links WHERE pair_id = ?i ORDER BY revision DESC LIMIT 1", $pair_id);
					$entry = array (
						$object_data['key'] => $object_id
					);

					list($rev_data['revision'], $rev_data['revision_id']) = fn_revisions_get_last($object_type, $entry, 0, $table);
				}
				if (!empty($rev_data['revision_id'])) {
					$table = 'rev_images_links';
					$cond = db_quote(" AND revision = ?s AND revision_id = ?i", $rev_data['revision'], $rev_data['revision_id']);
				}
			}
		}
	}

	if (!empty($pair_id)) {
		$images = db_get_row("SELECT image_id, detailed_id FROM ?:$table WHERE pair_id = ?i ?p", $pair_id, $cond);
		if (!empty($images)) {
			fn_delete_image($images['image_id'], $pair_id, $object_type, $rev_data);
			fn_delete_image($images['detailed_id'], $pair_id, 'detailed', $rev_data);
		}

		return true;
	}

	return false;
}

/**
 * Delete all images pairs for object
 */
function fn_clean_image_pairs($object_id, $object_type, $revision = null, $revision_id = null)
{
	$table = 'images_links';
	$itable = 'images';
	$cond = '';
	$rev_data = array ();

	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working') && $revision !== null) {
		$revisions = Registry::get('revisions');

		if (!empty($object_type) && !empty($revisions['objects'][$object_type]) && !empty($revisions['objects'][$object_type]['tables'])) {
			$object_data = $revisions['objects'][$object_type];

			if ($object_data['images']) {
				$entry = array (
					$object_data['key'] => $object_id
				);

				$rev_data = db_get_row("SELECT revision, revision_id FROM ?:revisions WHERE object = ?s AND object_id = ?i GROUP BY revision_id", $object_type, $object_id);

				$table = 'rev_images_links';
				$itable = 'rev_images';
				$rev_data = array (
					'revision' => $revision,
					'revision_id' => $revision_id
				);
				$cond = db_quote(" AND revision = ?i AND revision_id = ?i", $revision, $revision_id);
			}
		}
	}

	$pair_data = db_get_hash_array("SELECT pair_id, image_id, detailed_id, type FROM ?:$table WHERE object_id = ?i AND object_type = ?s ?p", 'pair_id', $object_id, $object_type, $cond);

	foreach ($pair_data as $pair_id => $p_data) {
		fn_delete_image_pair($pair_id, $object_type, $rev_data);
	}
}

//
// Clone image pairs
//
function fn_clone_image_pairs($target_object_id, $object_id, $object_type, $action = null, $parent_object_id = 0, $parent_object_type = '', $rev_data = array(), $lang_code = CART_LANGUAGE)
{
	$table = 'images_links';
	$itable = 'images';
	$cond = '';

	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working')) {
		if (!empty($rev_data)) {
			$cond = db_quote(" AND revision = ?s AND revision_id = ?i", $rev_data['revision'], $rev_data['revision_id']);
			$table = 'rev_images_links';
			$itable = 'rev_images';
		}
	}

	// Get all pairs
	$pair_data = db_get_hash_array("SELECT pair_id, image_id, detailed_id, type FROM ?:$table WHERE object_id = ?i AND object_type = ?s ?p", 'pair_id', $object_id, $object_type, $cond);

	if (empty($pair_data)) {
		return false;
	}

	$icons = $detailed = $pairs_data = array();

	foreach ($pair_data as $pair_id => $p_data) {
		if (!empty($p_data['image_id'])) {
			$icons[$pair_id] = fn_get_image($p_data['image_id'], $object_type, $rev_data, $lang_code);

			if (!empty($icons[$pair_id])) {
				$p_data['image_alt'] = empty($icons[$pair_id]['alt']) ? '' : $icons[$pair_id]['alt'];
				// Image is stored on the filesystem
				if (empty($icons[$pair_id]['image'])) {
					$path = str_replace(Registry::get('config.images_path'), DIR_IMAGES, $icons[$pair_id]['image_path']);
					$icons[$pair_id]['image'] = fn_get_contents($path);
					$name = ($action === null ? $target_object_id . '_' : '') . basename($path);
				} else {
					$name = ($action === null ? $target_object_id . '_' : '') . $object_type . '_image';
				}

				$tmp_name = tempnam(DIR_COMPILED, 'image_clone');
				fn_put_contents($tmp_name, $icons[$pair_id]['image']);

				$icons[$pair_id] = array(
					'path' => $tmp_name,
					'size' => filesize($tmp_name),
					'error' => 0,
					'name' => $name,
				);
			}
		}
		if (!empty($p_data['detailed_id'])) {
			$detailed[$pair_id] = fn_get_image($p_data['detailed_id'], 'detailed', $rev_data, $lang_code);
			if (!empty($detailed[$pair_id])) {
				$p_data['detailed_alt'] = empty($detailed[$pair_id]['alt']) ? '' : $detailed[$pair_id]['alt'];

				// Image is stored on the filesystem
				if (empty($detailed[$pair_id]['image'])) {
					$path = str_replace(Registry::get('config.images_path'), DIR_IMAGES, $detailed[$pair_id]['image_path']);
					$detailed[$pair_id]['image'] = fn_get_contents($path);
					$name = ($action === null ? $target_object_id . '_' : '') . basename($path);
				} else {
					$name = ($action === null ? $target_object_id . '_' : '') . '_detailed_image';
				}

				$tmp_name = tempnam(DIR_COMPILED, 'detailed_clone');
				fn_put_contents($tmp_name, $detailed[$pair_id]['image']);

				$detailed[$pair_id] = array(
					'path' => $tmp_name,
					'size' => filesize($tmp_name),
					'error' => 0,
					'name' => $name,
				);
			}
		}

		$pairs_data = array(
			$pair_id => array(
				'type' => $p_data['type'],
				'image_alt' => (!empty($p_data['image_alt'])) ? $p_data['image_alt'] : '',
				'detailed_alt' => (!empty($p_data['detailed_alt'])) ? $p_data['detailed_alt'] : '',
			)
		);

		if ($action == 'publish') {
			Registry::set('revisions.working', true);
		}

		fn_update_image_pairs($icons, $detailed, $pairs_data, $target_object_id, $object_type, array(), $parent_object_type, $parent_object_id, true, false, $lang_code);

		if ($action == 'publish') {
			Registry::set('revisions.working', false);
		}
	}
}

// ----------- Utility functions -----------------

//
// Resize image
//
function fn_resize_image($src, $dest, $new_width = 0, $new_height = 0, $make_box = false, $bg_color = '#ffffff')
{
	static $notification_set = false;
	static $gd_settings = array();

	if (file_exists($src) && !empty($dest) && (!empty($new_width) || !empty($new_height)) && extension_loaded('gd')) {

		$img_functions = array(
			'png' => function_exists('imagepng'),
			'jpg' => function_exists('imagejpeg'),
			'gif' => function_exists('imagegif'),
		);
		
		if (empty($gd_settings)) {
			$gd_settings = fn_get_settings('Thumbnails');
		}

		$dst_width = $new_width;
		$dst_height = $new_height;

		list($width, $height, $mime_type) = fn_get_image_size($src);
		if (empty($width) || empty($height)) {
			return false;
		}

		if ($width < $new_width) {
			$new_width = $width;
		}
		if ($height < $new_height) {
			$new_height = $height;
		}

		if ($dst_height == 0) { // if we passed width only, calculate height
			$new_height = $dst_height = ($height / $width) * $new_width;

		} elseif ($dst_width == 0) { // if we passed height only, calculate width
			$new_width = $dst_width = ($width / $height) * $new_height;

		} else { // we passed width and height, limit image by height! (hm... not sure we need it anymore?)
			if ($new_width * $height / $width > $dst_height) {
				$new_width = $width * $dst_height / $height;
			}
			$new_height = ($height / $width) * $new_width;
			if ($new_height * $width / $height > $dst_width) {
				$new_height = $height * $dst_width / $width;
			}
			$new_width = ($width / $height) * $new_height;
		}

		$w = number_format($new_width, 0, ',', '');
		$h = number_format($new_height, 0, ',', '');

		$ext = fn_get_image_extension($mime_type);

		if (!empty($img_functions[$ext])) {
			if ($make_box) {
				$dst = imagecreatetruecolor($dst_width, $dst_height);
			} else {
				$dst = imagecreatetruecolor($w, $h);
			}
			if (function_exists('imageantialias')) {
				imageantialias($dst, true);
			}
		} elseif ($notification_set == false) {
			$msg = fn_get_lang_var('error_image_format_not_supported');
			$msg = str_replace('[format]', $ext, $msg);
			fn_set_notification('E', fn_get_lang_var('error'), $msg);
			$notification_set = true;
			return false;
		}

		if ($ext == 'gif' && $img_functions[$ext] == true) {
			$new = imagecreatefromgif($src);
		} elseif ($ext == 'jpg' && $img_functions[$ext] == true) {
			$new = imagecreatefromjpeg($src);
		} elseif ($ext == 'png' && $img_functions[$ext] == true) {
			$new = imagecreatefrompng($src);
		} else {
			return false;
		}

		// Set transparent color to white
		// Not sure that this is right, but it works
		// FIXME!!!
		// $c = imagecolortransparent($new);

		list($r, $g, $b) = fn_parse_rgb($bg_color);
		$c = imagecolorallocate($dst, $r, $g, $b);
		//imagecolortransparent($dst, $c);
		if ($make_box) {
			imagefilledrectangle($dst, 0, 0, $dst_width, $dst_height, $c);
			$x = number_format(($dst_width - $w) / 2, 0, ',', '');
			$y = number_format(($dst_height - $h) / 2, 0, ',', '');
		} else {
			imagefilledrectangle($dst, 0, 0, $w, $h, $c);
			$x = 0;
			$y = 0;
		}
		imagecopyresampled($dst, $new, $x, $y, 0, 0, $w, $h, $width, $height);

		if ($gd_settings['convert_to'] == 'original') {
			$gd_settings['convert_to'] = $ext;
		}

		if (empty($img_functions[$gd_settings['convert_to']])) {
			foreach ($img_functions as $k => $v) {
				if ($v == true) {
					$gd_settings['convert_to'] = $k;
					break;
				}
			}
		}

		switch ($gd_settings['convert_to']) {
			case 'gif':
				imagegif($dst, $dest);
				break;
			case 'jpg':
				imagejpeg($dst, $dest, $gd_settings['jpeg_quality']);
				break;
			case 'png':
				imagepng($dst, $dest);
				break;
		}
		@chmod($dest, DEFAULT_FILE_PERMISSIONS);

		return true;
	}

	return false;
}

//
// Create thumbnails from detailed images
//
function fn_create_thumbnail(&$icon, &$detailed, $width = 0, $height = 0, $bg_color = '#ffffff')
{
	if (empty($width) || Registry::get('settings.Thumbnails.create_thumbnails') != 'Y' || empty($detailed)) {
		return false;
	}

	if (empty($icon) && fn_resize_image($detailed['path'], $detailed['path'].'.thmb', $width, $height, true, $bg_color)) {
		$icon = array(
			'name'  => 'thumbnail_'.$detailed['name'],
			'path'  => $detailed['path'].'.thmb',
			'size'  => filesize($detailed['path'].'.thmb'),
			'error' => 0,
		);
	}

	return true;
}

//
// Check supported GDlib formats
//
function fn_check_gd_formats()
{
	$avail_formats = array(
		'original' => fn_get_lang_var('same_as_source'),
	);

	if (function_exists('imagegif')) {
		$avail_formats['gif'] = 'GIF';
	}
	if (function_exists('imagejpeg')) {
		$avail_formats['jpg'] = 'JPEG';
	}
	if (function_exists('imagepng')) {
		$avail_formats['png'] = 'PNG';
	}

	return $avail_formats;
}

//
// Get image extension by MIME type
//
function fn_get_image_extension($image_type)
{
	static $image_types = array (
		'image/gif' => 'gif',
		'image/pjpeg' => 'jpg',
		'image/jpeg' => 'jpg',
		'image/png' => 'png',
		'application/x-shockwave-flash' => 'swf',
		'image/psd' => 'psd',
		'image/bmp' => 'bmp',
	);

	return isset($image_types[$image_type]) ? $image_types[$image_type] : false;
}

//
// Getimagesize wrapper
// Returns mime type instead of just image type
// And doesn't return html attributes
function fn_get_image_size($file)
{
	//var_dump($file);
	// File is url, get it and store in temporary directory
	if (strpos($file, '://') !== false) {
		$tmp = fn_create_temp_file();

		if (fn_put_contents($tmp, fn_get_contents($file)) == 0) {
			return false;
		}

		$file = $tmp;
	}

	list($w, $h, $t, $a) = @getimagesize($file);

	if (empty($w)) {
		return false;
	}

	$t = image_type_to_mime_type($t);

	return array($w, $h, $t);
}

function fn_attach_image_pairs($name, $object_type, $object_id = 0, $lang_code = CART_LANGUAGE, $object_ids = array (), $parent_object = '', $parent_object_id = 0)
{
	$icons = fn_filter_uploaded_data($name . '_image_icon');
	
	$detailed = fn_filter_uploaded_data($name . '_image_detailed');
	$pairs_data = !empty($_REQUEST[$name . '_image_data']) ? $_REQUEST[$name . '_image_data'] : array();

	return fn_update_image_pairs($icons, $detailed, $pairs_data, $object_id, $object_type, $object_ids, $parent_object, $parent_object_id, true, false, $lang_code);
}

function fn_generate_thumbnail($image_path, $width, $height = 0, $make_box = false)
{
	if (empty($image_path)) {
		return '';
	}
	
	if (strpos($image_path, '://') === false) {
		if (strpos($image_path, '/') !== 0) { // relative path
			$image_path = Registry::get('config.current_path') . '/' . $image_path;
		}
		$image_path = (defined('HTTPS') ? ('https://' . Registry::get('config.https_host')) : ('http://' . Registry::get('config.http_host'))) . $image_path;
	}

	$_path = str_replace(Registry::get('config.current_location') . '/', '', $image_path);

	$image_url = explode('/', $_path);
	$image_name = array_pop($image_url);
	$image_dir = array_pop($image_url);
	$image_dir .= '/' . $width . (empty($height) ? '' : '/' . $height);
	$filename = $image_dir . '/' . $image_name;
	$real_path = htmlspecialchars_decode(DIR_ROOT . '/' . $_path, ENT_QUOTES);
	$th_path = htmlspecialchars_decode(DIR_THUMBNAILS . $filename, ENT_QUOTES);

	if (!fn_mkdir(DIR_THUMBNAILS . $image_dir)) {
		return '';
	}

	if (!file_exists($th_path)) {
		if (fn_get_image_size($real_path)) {
			$image = fn_get_contents($real_path);
			fn_put_contents($th_path, $image);
			fn_resize_image($th_path, $th_path, $width, $height, $make_box);
		} else {
			return '';
		}
	}

	return Registry::get('config.thumbnails_path') . $filename;
}

function fn_parse_rgb($color)
{
	$r = hexdec(substr($color, 1, 2));
	$g = hexdec(substr($color, 3, 2));
	$b = hexdec(substr($color, 5, 2));
	return array($r, $g, $b);
}

?>
