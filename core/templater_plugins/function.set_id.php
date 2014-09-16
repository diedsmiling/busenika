<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty id set function plugin
 *
 * Type:     function<br>
 * Name:     set_id<br>
 * Purpose:  function generate id for template 
 * @return string
 */

function smarty_function_set_id($param, &$smarty)
{
	$template_tree = $smarty->_smarty_customization_info;
	$tree = array();
	$count = count($template_tree) - 1;
	if ($template_tree[$count]['filename'] != $param['name']) {
		array_push($tree, $param['name']);
	}
	$depth = $template_tree[$count]['depth'] + 1;
	for ($i = $count; $i >= 0; $i--) {
		if ($template_tree[$i]['depth'] < $depth) {
			$depth = $template_tree[$i]['depth'];
			array_unshift($tree, $template_tree[$i]['filename']);
		}
		if ($depth == 0) {
			break;
		}
	}
	$cur_id = join(',', $tree);
	
	$id = '[tpl_id ' . $cur_id . ']';

	return $id;
}

?>
