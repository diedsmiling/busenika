?php
/
//
// $Id: usergroups.php 7502 2009-05-19 14:54:59Z zeke $
//

if ( !defined('AREA') )	{ die('Access denied');	}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	return;
}

if ($mode == 'manage') {
	Registry::set('navigation.tabs.recurring_plan_0', array (
		'title' => fn_get_lang_var('rb_recurring_plans'),
		'js' => true
	)); 

} elseif ($mode == 'update') {
	$usergroup = $view->get_var('usergroup');
	$usergroup['recurring_plans_ids'] = db_get_field("SELECT recurring_plans_ids FROM ?:usergroups WHERE usergroup_id = ?i", $_REQUEST['usergroup_id']);
	$view->assign('usergroup', $usergroup);

	Registry::set('navigation.tabs.recurring_plan_' . $_REQUEST['usergroup_id'], array (
		'title' => fn_get_lang_var('rb_recurring_plans'),
		'js' => true
	)); 
}

?>
