<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_block_hook($params, $content, &$smarty)
{
	if (is_null($content)) {
		return;
	}

	return $content;
}

?>
