<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_modifier_unescape($data)
{
	return fn_html_escape($data, true);
}

/* vim: set expandtab: */

?>
