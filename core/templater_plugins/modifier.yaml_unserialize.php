<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier<br>
 * Name:     yaml_unserialize<br>
 * Purpose:  converts yaml string to array 
 * Example:  {$a|yaml_unserialize}
 * -------------------------------------------------------------
 */

function smarty_modifier_yaml_unserialize($data)
{
	fn_init_yaml();
	return YAML_Parser::unserialize("{" . $data . "}");
}

/* vim: set expandtab: */
?>