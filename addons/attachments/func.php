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
// $Id: func.php 10229 2010-07-27 14:21:39Z 2tl $
//

if (!defined('AREA')) { die('Access denied'); }

function fn_get_attachments($object_type, $object_id, $type = 'M', $lang_code = CART_LANGUAGE, $revision = null, $revision_id = null)
{
	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working') && empty($revision_id)) {
		$revisions = Registry::get('revisions');

		if (!empty($revisions['objects'][$object_type]) && !empty($revisions['objects'][$object_type]['tables'])) {
			$object_data = $revisions['objects'][$object_type];

			if ($object_data['attachments']) {
				$rev_data = db_get_row("SELECT max(revision) as revision, revision_id FROM ?:revisions WHERE object = ?s AND object_id = ?i GROUP BY revision_id", $object_type, $object_id);

				if ($rev_data) {
					$revision = $rev_data['revision'];
					$revision_id = $rev_data['revision_id'];
				}
			}
		}
	}

	if ($revision_id) {
		$_ = 'rev_';
		$revision_condition = db_quote(" AND ?:rev_attachments.revision = ?s AND ?:rev_attachments.revision_id = ?i", $revision, $revision_id);
		$revision_join_condition = db_quote(" AND ?:rev_attachments.revision = ?:rev_attachment_descriptions.revision AND ?:rev_attachments.revision_id = ?:rev_attachment_descriptions.revision_id");
	} else {
		$_ = '';
		$revision_condition = '';
		$revision_join_condition = '';
	}

	if (AREA == 'A') {
		$data = db_get_array("SELECT ?:{$_}attachments.*, ?:{$_}attachment_descriptions.description FROM ?:{$_}attachments LEFT JOIN ?:{$_}attachment_descriptions ON ?:{$_}attachments.attachment_id = ?:{$_}attachment_descriptions.attachment_id AND lang_code = ?s ?p WHERE object_type = ?s AND object_id = ?i AND type = ?s ?p ORDER BY position", $lang_code, $revision_join_condition, $object_type, $object_id, $type, $revision_condition);
	} else {
		$auth = $_SESSION['auth'];

		$ug_cond = ' AND (' . fn_find_array_in_set($auth['usergroup_ids'], 'usergroup_ids', true) . ')';
		$data = db_get_array("SELECT ?:{$_}attachments.*, ?:{$_}attachment_descriptions.description FROM ?:{$_}attachments LEFT JOIN ?:{$_}attachment_descriptions ON ?:{$_}attachments.attachment_id = ?:{$_}attachment_descriptions.attachment_id AND lang_code = ?s ?p WHERE object_type = ?s AND object_id = ?i AND type = ?s AND filesize > 0 ?p ?p AND status = 'A' ORDER BY position", $lang_code, $revision_join_condition, $object_type, $object_id, $type, $ug_cond, $revision_condition);
	}

	return $data;
}

function fn_add_attachments($attachment_data, $object_type, $object_id, $type = 'M', $files = null)
{
	$object_id = intval($object_id);
	
	if (!fn_mkdir(DIR_ATTACHMENTS)) {
		return false;
	}

	$revision_id = 0;

	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working')) {
		$revisions = Registry::get('revisions');

		if (!empty($revisions['objects'][$object_type]) && !empty($revisions['objects'][$object_type]['tables'])) {
			$object_data = $revisions['objects'][$object_type];

			if ($object_data['attachments']) {
				$entry = array (
					$object_data['key'] => $object_id
				);

				list($revision, $revision_id) = fn_revisions_get_last($object_type, $entry, 0, 'attachments');
			}
		}
	}

	if ($revision_id) {
		$_ = 'rev_';
		$revision_condition = db_quote(" AND revision = ?s AND revision_id = ?i", $revision, $revision_id);
	} else {
		$_ = '';
		$revision_condition = '';
	}

	$directory = DIR_ATTACHMENTS . '/' . $object_type . ($revision_condition ? '_rev' : '') . '/' . $object_id;

	if (!fn_mkdir($directory)) {
		return false;
	}

	if ($files != null) {
		$uploaded_data = $files;
	} else {
		$uploaded_data = fn_filter_uploaded_data('attachment_files');
	}

	$rec = array (
		'object_type' => $object_type,
		'object_id' => $object_id,
		'usergroup_ids' => empty($attachment_data['usergroup_ids']) ? '' : implode(',', $attachment_data['usergroup_ids']),
		'position' => $attachment_data['position']
	);
	//$company_id = db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i ", $_REQUEST['object_id']);

	if ($type !== null) {
		$rec['type'] = $type;
	} elseif (!empty($attachment_data['type'])) {
		$rec['type'] = $attachment_data['type'];
	}

	if ($revision_id) {
		$rec['revision_id'] = $revision_id;
		$rec['revision'] = $revision;
	}

	$attachment_id = db_query("INSERT INTO ?:{$_}attachments ?e", $rec);

	if ($attachment_id) {
		foreach ((array)Registry::get('languages') as $lang_code => $v) {
			$rec = array (
				'attachment_id' => $attachment_id,
				'lang_code' => $lang_code,
				'description' => is_array($attachment_data['description']) ? $attachment_data['description'][$lang_code] : $attachment_data['description']
			);

			if ($revision_id) {
				$rec['revision_id'] = $revision_id;
				$rec['revision'] = $revision;
			}

			db_query("INSERT INTO ?:{$_}attachment_descriptions ?e", $rec);
		}
	}

	if ($attachment_id && !empty($uploaded_data[0]) && $uploaded_data[0]['size']) {
		$filename = $uploaded_data[0]['name'];

		$i = 1;
		while (is_file($directory . '/' . $filename)) {
			$filename = substr_replace($uploaded_data[0]['name'], sprintf('%03d', $i) . '.', strrpos($uploaded_data[0]['name'], '.'), 1);
			$i++;
		}

		fn_copy($uploaded_data[0]['path'], $directory . '/' . $filename);

		if (is_file($directory . '/' . $filename)) {
			$filesize = filesize($directory . '/' . $filename);

			db_query("UPDATE ?:{$_}attachments SET filename = ?s, filesize = ?i WHERE attachment_id = ?i ?p", $filename, $filesize, $attachment_id, $revision_condition);
		}
	}

	return true;
}

function fn_update_attachments($attachment_data, $attachment_id, $object_type, $object_id, $type = 'M', $lang_code = CART_LANGUAGE)
{
	$object_id = intval($object_id);

	if (!fn_mkdir(DIR_ATTACHMENTS)) {
		return false;
	}

	$revision_id = 0;

	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working')) {
		$revisions = Registry::get('revisions');

		if (!empty($revisions['objects'][$object_type]) && !empty($revisions['objects'][$object_type]['tables'])) {
			$object_data = $revisions['objects'][$object_type];

			if ($object_data['attachments']) {
				$entry = array (
					$object_data['key'] => $object_id
				);

				list($revision, $revision_id) = fn_revisions_get_last($object_type, $entry, 0, 'attachments');
			}
		}
	}

	if ($revision_id) {
		$_ = 'rev_';
		$revision_condition = db_quote(" AND revision = ?s AND revision_id = ?i", $revision, $revision_id);
	} else {
		$_ = '';
		$revision_condition = '';
	}

	$directory = DIR_ATTACHMENTS . '/' . $object_type . ($revision_condition ? '_rev' : '') . '/' . $object_id;

	if (!fn_mkdir($directory)) {
		return false;
	}

	$uploaded_data = fn_filter_uploaded_data('attachment_files');

	$rec = array (
		'usergroup_ids' => empty($attachment_data['usergroup_ids']) ? '' : implode(',', $attachment_data['usergroup_ids']),
		'position' => $attachment_data['position']
	);

	db_query("UPDATE ?:{$_}attachment_descriptions SET description = ?s WHERE attachment_id = ?i AND lang_code = ?s ?p", $attachment_data['description'], $attachment_id, $lang_code, $revision_condition);
	db_query("UPDATE ?:{$_}attachments SET ?u WHERE attachment_id = ?i AND object_type = ?s AND object_id = ?i AND type = ?s ?p", $rec, $attachment_id, $object_type, $object_id, $type, $revision_condition);

	if ($attachment_id && !empty($uploaded_data[$attachment_id]) && $uploaded_data[$attachment_id]['size']) {
		$filename = $uploaded_data[$attachment_id]['name'];

		$old_filename = db_get_field("SELECT filename FROM ?:{$_}attachments WHERE attachment_id = ?i ?p", $attachment_id, $revision_condition);

		if (!$revision_id && $old_filename && is_file($directory . '/' . $old_filename)) {
			unlink($directory . '/' . $old_filename);
		}

		$i = 1;
		while (is_file($directory . '/' . $filename)) {
			$filename = substr_replace($uploaded_data[$attachment_id]['name'], sprintf('%03d', $i) . '.', strrpos($uploaded_data[$attachment_id]['name'], '.'), 1);
			$i++;
		}

		fn_copy($uploaded_data[$attachment_id]['path'], $directory . '/' . $filename);

		if (is_file($directory . '/' . $filename)) {
			$filesize = filesize($directory . '/' . $filename);
			db_query("UPDATE ?:{$_}attachments SET filename = ?s, filesize = ?i WHERE attachment_id = ?i ?p", $filename, $filesize, $attachment_id, $revision_condition);
		}
	}

	return true;
}

function fn_delete_attachments($attachment_ids, $object_type, $object_id, $revision = null, $revision_id = null)
{
	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working') && empty($revision_id)) {
		$revisions = Registry::get('revisions');

		if (!empty($revisions['objects'][$object_type]) && !empty($revisions['objects'][$object_type]['tables'])) {
			$object_data = $revisions['objects'][$object_type];

			if ($object_data['attachments']) {
				$entry = array (
					$object_data['key'] => $object_id
				);

				list($revision, $revision_id) = fn_revisions_get_last($object_type, $entry, 0, 'attachments');
			}
		}
	}

	if ($revision_id) {
		$_ = 'rev_';
		$revision_condition = db_quote(" AND revision = ?s AND revision_id = ?i", $revision, $revision_id);
	} else {
		$_ = '';
		$revision_condition = '';
	}

	$data = db_get_array("SELECT * FROM ?:{$_}attachments WHERE attachment_id IN (?n) AND object_type = ?s AND object_id = ?i ?p", $attachment_ids, $object_type, $object_id, $revision_condition);

	foreach ($data as $entry) {
		$directory = DIR_ATTACHMENTS . '/' . $entry['object_type'] . ($revision_condition ? '_rev' : '') . '/' . $object_id;

		if ($entry['filename'] && is_file($directory . '/' . $entry['filename'])) {
			$count = db_get_field("SELECT COUNT(filename) FROM ?:{$_}attachments WHERE filename = ?s", $entry['filename']);

			if ($count == 1) {
				unlink($directory . '/' . $entry['filename']);
			}
		}
	}

	db_query("DELETE FROM ?:{$_}attachments WHERE attachment_id IN (?n) AND object_type = ?s AND object_id = ?i ?p", $attachment_ids, $object_type, $object_id, $revision_condition);
	db_query("DELETE FROM ?:{$_}attachment_descriptions WHERE attachment_id IN (?n) ?p", $attachment_ids, $revision_condition);

	return true;
}

function fn_get_attachment($attachment_id, $object_type = null, $object_id = null)
{
	if ($object_type === null) {
		$auth = $_SESSION['auth'];

		$ug_cond = ' AND (' . fn_find_array_in_set($auth['usergroup_ids'], 'usergroup_ids', true) . ')';
		$data = db_get_row("SELECT * FROM ?:attachments WHERE attachment_id = ?i ?p AND status = 'A'", $attachment_id, $ug_cond);

		if (!empty($data['filename'])) {
			$directory = DIR_ATTACHMENTS . '/' . $data['object_type'] . '/' . $data['object_id'];
			$data['path'] = $directory . '/' . $data['filename'];
		}

		return $data;
	}

	$revision_id = 0;

	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working')) {
		$revisions = Registry::get('revisions');

		if (!empty($revisions['objects'][$object_type]) && !empty($revisions['objects'][$object_type]['tables'])) {
			$object_data = $revisions['objects'][$object_type];

			if ($object_data['attachments']) {
				$rev_data = db_get_row("SELECT max(revision) as revision, revision_id FROM ?:revisions WHERE object = ?s AND object_id = ?i GROUP BY revision_id", $object_type, $object_id);

				if ($rev_data) {
					$revision = $rev_data['revision'];
					$revision_id = $rev_data['revision_id'];
				}
			}
		}
	}

	if ($revision_id) {
		$_ = 'rev_';
		$revision_condition = db_quote(" AND revision = ?s AND revision_id = ?i", $revision, $revision_id);
	} else {
		$_ = '';
		$revision_condition = '';
	}

	if (AREA == 'A') {
		$data = db_get_row("SELECT * FROM ?:{$_}attachments WHERE attachment_id = ?i ?p", $attachment_id, $revision_condition);
	}

	if (!empty($data['filename'])) {
		$directory = DIR_ATTACHMENTS . '/' . $data['object_type'] . ($revision_condition ? '_rev' : '') . '/' . $object_id;
		$data['path'] = $directory . '/' . $data['filename'];
	}

	return $data;
}

function fn_clone_attachments($object_type, $target_object_id, $object_id, $action = null)
{
	$revision_id = 0;
	$revision = null;

	if (AREA == 'A' && Registry::is_exist('revisions') && !Registry::get('revisions.working')) {
		$revisions = Registry::get('revisions');

		if (!empty($revisions['objects'][$object_type]) && !empty($revisions['objects'][$object_type]['tables'])) {
			$object_data = $revisions['objects'][$object_type];

			if ($object_data['attachments']) {
				$rev_data = db_get_row("SELECT max(revision) as revision, revision_id FROM ?:revisions WHERE object = ?s AND object_id = ?i GROUP BY revision_id", $object_type, $object_id);

				$revision = $rev_data['revision'];
				$revision_id = $rev_data['revision_id'];
			}
		}
	}

	if ($revision_id && $action != 'create') {
		$_ = 'rev_';
		$revision_condition = db_quote(" AND revision = ?s AND revision_id = ?i", $revision, $revision_id);
	} else {
		$_ = '';
		$revision_condition = '';
	}

	$data = db_get_array("SELECT * FROM ?:{$_}attachments WHERE object_type = ?s AND object_id = ?i ?p", $object_type, $object_id, $revision_condition);

	$files = array ();
	$add_data = array ();
	$descriptions = array ();
	$directory = DIR_ATTACHMENTS . '/' . $object_type . ($revision_condition ? '_rev' : '') . '/' . $target_object_id;

	$i = 1;

	if ($action == 'publish') {
		Registry::set('revisions.working', true);
	}

	foreach ($data as $entry) {
		$files = array();
		if (!empty($entry['filename'])) {
			$f_name = $directory . '/' . $entry['filename'];

			$files[0] = array (
				'path' => $f_name,
				'size' => filesize($f_name),
				'error' => 0,
				'name' => $entry['filename'],
			);
		}

		$add_data = array (
			'attachment_id' => $entry['attachment_id'],
			'usergroup_ids' => implode(',', $entry['usergroup_ids']),
			'position' => $entry['position'],
			'type' => $entry['type'],
			'description' => db_get_hash_single_array("SELECT * FROM ?:{$_}attachment_descriptions WHERE attachment_id = ?i ?p", array('lang_code', 'description'), $entry['attachment_id'], $revision_condition)
		);

		fn_add_attachments($add_data, $object_type, $target_object_id, $entry['type'], $files);
	}

	if ($action == 'publish') {
		Registry::set('revisions.working', false);
	}
}


function fn_attachments_revisions_publish($object, $entry)
{
	$revisions = Registry::get('revisions');

	if (empty($revisions['objects'][$object])) {
		return false;
	}

	$object_data = $revisions['objects'][$object];

	if ($object_data['attachments']) {
		$status = Registry::get('revisions.working');

		Registry::set('revisions.working', true);

		$object_id = $entry[$object_data['key']];

		$data = db_get_array("SELECT * FROM ?:attachments WHERE object_id = ?i AND object_type = ?s", $object_id, $object);

		foreach ($data as $entry) {
			fn_delete_attachments($entry['attachment_id'], $object, $object_id);
		}

		Registry::set('revisions.working', $status);

		fn_clone_attachments($object, $object_id, $object_id, 'publish');
	}

	return false;
}

function fn_attachments_revisions_delete_objects($object_type)
{
	$revisions = Registry::get('revisions');

	if (empty($revisions['objects'][$object_type])) {
		return false;
	}

	if ($revisions['objects'][$object_type]['attachments']) {
		$status = Registry::get('revisions.working');

		Registry::set('revisions.working', true);

		$ids = db_get_fields("SELECT attachment_id FROM ?:rev_attachments WHERE object_type = ?s GROUP BY attachment_id", $object_type);

		foreach ($ids as $id) {
			db_query("DELETE FROM ?:rev_attachment_descriptions WHERE attachment_id = ?i", $id);
		}

		db_query("DELETE FROM ?:rev_attachments WHERE object_type = ?s", $object_type);

		fn_rm(DIR_ATTACHMENTS . '/' . $object_type . '_rev');

		Registry::set('revisions.working', $status);

		return true;
	}

	return false;
}

function fn_attachments_revisions_create_objects($object_type, $object_id)
{
	$revisions = Registry::get('revisions');

	if (empty($revisions['objects'][$object_type])) {
		return false;
	}

	if ($revisions['objects'][$object_type]['attachments']) {
		fn_clone_attachments($object_type, $object_id, $object_id, 'create');
	}

	return true;
}

function fn_attachments_revisions_clone($object, $keys, $revision_id, $revision, $prev_revision)
{
	$revisions = Registry::get('revisions');

	if (empty($revisions['objects'][$object])) {
		return false;
	}

	if ($revisions['objects'][$object]['attachments']) {
		$data = db_get_array("SELECT * FROM ?:rev_attachments WHERE revision_id = ?i AND revision = ?i", $revision_id, $prev_revision);

		foreach ($data as $entry) {
			$entry['revision'] = $revision;

			db_query("INSERT INTO ?:rev_attachments ?e", $entry);

			$descriptions = db_get_array("SELECT * FROM ?:rev_attachment_descriptions WHERE attachment_id = ?i AND revision_id = ?i AND revision = ?i", $entry['attachment_id'], $revision_id, $prev_revision);

			foreach ($descriptions as $description) {
				$description['revision'] = $revision;

				db_query("INSERT INTO ?:rev_attachment_descriptions ?e", $description);
			}
		}
	}

	return true;
}

function fn_attachments_revisions_get_data(&$revision_data, $object_type, $revision, $object_id)
{
	$revisions = Registry::get('revisions');

	if (empty($revisions['objects'][$object_type])) {
		return false;
	}

	if ($revisions['objects'][$object_type]['attachments']) {
		$data = db_get_array("SELECT * FROM ?:rev_attachments WHERE object_type = ?s AND revision = ?i AND object_id = ?i", $object_type, $revision, $object_id);

		foreach ($data as $entry) {
			$revision_data['attachments'][md5($entry['attachment_id'] . ' ' . $entry['object_id'])] = $entry;

			$descriptions = db_get_array("SELECT * FROM ?:rev_attachment_descriptions WHERE attachment_id = ?i AND revision = ?i", $entry['attachment_id'], $revision);

			foreach ($descriptions as $description) {
				$revision_data['attachment_descriptions'][md5($description['attachment_id'] . ' ' . $description['lang_code'])] = $description;
			}
		}
	}
}

function fn_attachments_create_revision_tables()
{
	fn_create_revision_table('attachments');
	fn_create_revision_table('attachment_descriptions');
}

function fn_attachments_revisions_delete($object_id, $object, $revision, $revision_id)
{
	$data = db_get_array("SELECT * FROM ?:rev_attachments WHERE object_type = ?s AND object_id = ?i AND revision = ?i AND revision_id = ?i", $object, $object_id, $revision, $revision_id);

	foreach ($data as $entry) {
		fn_delete_attachments($entry['attachment_id'], $object, $object_id, $revision, $revision_id);
	}
}

?>