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
 * Name:     price<br>
 * Purpose:  getting formatted price with grouped thousands and 
 *           decimal separators
 * Example:  {$price|price:"2":".":","}
 * -------------------------------------------------------------
 */

function smarty_modifier_divide($obj, $by)
{
	return $obj / $by;
}

/* vim: set expandtab: */
?>