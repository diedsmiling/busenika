<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_block($params, &$smarty)
{
	static $blocks;

	$display = true;
	if (!isset($blocks)) {
		$blocks = $smarty->get_var('blocks');
	}

	$main_group_id = 0;

	if (empty($params['group']) && empty($params['id'])) {
		return false;

	} elseif (!empty($params['group'])) {
		foreach ($blocks as $_block_id => $_block_data) {
			if ($_block_data['text_id'] == $params['group']) {
				$main_group_id = $_block_id;
			}
		}
		if (empty($main_group_id)) {
			return false;
		}

		$block_content = smarty_function_group_output($blocks, $blocks[$main_group_id], $smarty);

	} else {
		if (empty($blocks[$params['id']])) {
			return false;
		}
		$block_data = $blocks[$params['id']];
		if (!empty($params['no_box'])) {
			unset($block_data['properties']['wrapper']);
		}
		$block_content = smarty_function_block_output($block_data, $smarty);
	}

	if (empty($params['assign'])) {
		return $block_content;
	} else {
		$smarty->assign($params['assign'], $block_content, false);
	}
}

function smarty_function_group_output($blocks, $group_data, &$smarty)
{
	$group_content = '';

	foreach ($blocks as $_block_id => $_block_data) {
		if ($_block_data['group_id'] == $group_data['block_id']) {
			if ($group_data['text_id'] == 'product_details') {
				continue;
			}
			if ($_block_data['block_type'] == 'G') {
				$_content = smarty_function_group_output($blocks, $_block_data, $smarty);
			} else {
				$_content = smarty_function_block_output($_block_data, $smarty);
			}
			if (!empty($_content) && $group_data['properties']['block_order'] == 'H') {
				$_width = empty($_block_data['properties']['width']) ? '' : ' width="' . $_block_data['properties']['width'] . ($_block_data['properties']['width_unit'] == 'P' ? '%' : '') . '"';
				$_content = (empty($group_content) ? '' : '<td width="10"></td>') . '<td' . $_width . '>' . $_content . '</td>';
			}
			$group_content .= $_content;
		}
	}

	if (!empty($group_content)) {
		if ($group_data['properties']['block_order'] == 'H') {
			$group_content = '<table class="fixed-layout" width="100%" cellspacing="0" cellpadding="0" border="0"><tr valign="top">' . $group_content . '</tr></table>';
		}
		if (!empty($group_data['properties']['wrapper'])) { // if group is wrapped, display wrapper
			if (!empty($smarty->_smarty_vars['capture']['hide_wrapper'])) {
				$smarty->assign('hide_wrapper', true);
				unset($smarty->_smarty_vars['capture']['hide_wrapper']); // remove this flag
			}
			$smarty->assign('title', $group_data['description']);
			$smarty->assign('content', $group_content, false);
			$group_content = $smarty->display($group_data['properties']['wrapper'], false);
		}
		return $group_content;
	} else {
		return '';
	}
}
function smarty_function_block_output($_block_data, &$smarty)
{
	$_tpl_vars = $smarty->_tpl_vars; // save state of original variables
	$display = true;
	if (!empty($_block_data['properties']['wrapper'])) { // if block is wrapped, display wrapper
		$display_tpl = $_block_data['properties']['wrapper'];
	}

	if (!empty($_block_data['text_id']) && $_block_data['text_id'] == 'central_content') {
		$block_content = $smarty->display($smarty->get_var('content_tpl'), false);
		if (!empty($display_tpl)) {
			if (!empty($smarty->_smarty_vars['capture']['hide_wrapper'])) {
				$smarty->assign('hide_wrapper', true);
				unset($smarty->_smarty_vars['capture']['hide_wrapper']); // remove this flag
			}
			$smarty->assign('title', !empty($smarty->_smarty_vars['capture']['mainbox_title']) ? $smarty->_smarty_vars['capture']['mainbox_title'] : '', false);
			$smarty->assign('content', $block_content, false);
			unset($block_content);

		} else {
			$display_tpl = $smarty->get_var('content_tpl');
		}

	} else {
		$_template = !empty($_block_data['properties']['appearances']) ? $_block_data['properties']['appearances'] : ((!empty($_block_data['properties']['list_object']) && strpos($_block_data['properties']['list_object'], '.tpl') !== false) ? $_block_data['properties']['list_object'] : '');

		if (empty($_template)) {
			return '';
		}

		// This block is not static, so it is necessary to find its items
		if (strpos($_block_data['properties']['list_object'], '.tpl') === false || !empty($_block_data['properties']['items_function'])) {
			$items = fn_get_block_items($_block_data);
			if (empty($items)) {
				$display = false;
			} else {
				$smarty->assign('items', $items);
			}
		}

		if ($display == true) {

			if ($smarty->template_exists($_template)) {
				if (strpos($_template, 'addons/') !== false) {
					$a = explode('/', $_template);
					if (fn_load_addon($a[1]) == false) { // do not display template of disabled addon
						$display = false;
					}
				}
			} else {
				$display = false;
			}

			if ($display == true) {
				//unset($blocks[$params['id']], $params['id'], $params['template']);

				$smarty->assign('block', $_block_data, false);
				// Pass extra parameters to smarty

				$block_content = $smarty->display($_template, false);
				if (!empty($display_tpl)) { // if wrapper exists, get block content
					if (trim($block_content)) {
						if (!empty($smarty->_smarty_vars['capture']['hide_wrapper'])) {
							$smarty->assign('hide_wrapper', true);
							unset($smarty->_smarty_vars['capture']['hide_wrapper']); // remove this flag
						}
						$smarty->assign('title', $_block_data['description']);
						$smarty->assign('content', $block_content, false);
						unset($block_content);
					} else {
						$display = false;
					}
				} else {
					$display_tpl = $_template;
				}
			}
		}
	}

	if ($display == true) {
		$block_content = !empty($block_content) ? $block_content : $smarty->display($display_tpl, false);
		$smarty->_tpl_vars = $_tpl_vars; // restore original vars again
		return trim($block_content);
	} else {
		return '';
	}
}
?>