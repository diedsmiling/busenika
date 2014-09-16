<?php

//
// $Id: init.php 7538 2009-05-27 15:55:45Z lexa $
//

if (!defined('AREA')) { die('Access denied'); }

fn_register_hooks(
	'revisions_publish',
	'revisions_delete_objects',
	'revisions_create_objects',
	'revisions_clone',
	'revisions_get_data',
	'create_revision_tables',
	'revisions_delete'
);

?>