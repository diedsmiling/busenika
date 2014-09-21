<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:44
         compiled from views/products/components/products_update_qty_discounts.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'views/products/components/products_update_qty_discounts.tpl', 3, false),array('function', 'math', 'views/products/components/products_update_qty_discounts.tpl', 51, false),array('function', 'cycle', 'views/products/components/products_update_qty_discounts.tpl', 52, false),array('modifier', 'fn_get_usergroups', 'views/products/components/products_update_qty_discounts.tpl', 4, false),array('modifier', 'default', 'views/products/components/products_update_qty_discounts.tpl', 26, false),array('modifier', 'fn_get_default_usergroups', 'views/products/components/products_update_qty_discounts.tpl', 35, false),array('modifier', 'escape', 'views/products/components/products_update_qty_discounts.tpl', 36, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('quantity','price','usergroup','all'));
?>

<?php echo smarty_function_script(array('src' => "js/picker.js"), $this);?>

<?php $this->assign('usergroups', fn_get_usergroups('C'), false); ?>
<div id="content_qty_discounts" class="hidden">
	<table cellpadding="0" cellspacing="0" border="0" class="table" width="100%">
	<tbody class="cm-first-sibling">
	<tr>
		<th><?php echo fn_get_lang_var('quantity', $this->getLanguage()); ?>
</th>
		<th><?php echo fn_get_lang_var('price', $this->getLanguage()); ?>
&nbsp;(<?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
)</th>
				<th width="100%"><?php echo fn_get_lang_var('usergroup', $this->getLanguage()); ?>
</th>
				<th width="1%">&nbsp;</th>
	</tr>
	</tbody>
	<tbody>
	<?php $_from = $this->_tpl_vars['product_data']['prices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['prod_prices'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['prod_prices']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['_key'] => $this->_tpl_vars['price']):
        $this->_foreach['prod_prices']['iteration']++;
?>
	<tr class="cm-row-item">
		<td>
			<?php if ($this->_tpl_vars['price']['lower_limit'] == '1' && $this->_tpl_vars['price']['usergroup_id'] == '0'): ?>
				&nbsp;<?php echo $this->_tpl_vars['price']['lower_limit']; ?>

			<?php else: ?>
			<input type="text" name="product_data[prices][<?php echo $this->_tpl_vars['_key']; ?>
][lower_limit]" value="<?php echo $this->_tpl_vars['price']['lower_limit']; ?>
" class="input-text-short" />
			<?php endif; ?></td>
		<td>
			<?php if ($this->_tpl_vars['price']['lower_limit'] == '1' && $this->_tpl_vars['price']['usergroup_id'] == '0'): ?>
				&nbsp;<?php echo smarty_modifier_default(@$this->_tpl_vars['price']['price'], "0.00"); ?>

			<?php else: ?>
			<input type="text" name="product_data[prices][<?php echo $this->_tpl_vars['_key']; ?>
][price]" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['price']['price'], "0.00"); ?>
" size="10" class="input-text-medium" />
			<?php endif; ?></td>
				<td>
			<?php if ($this->_tpl_vars['price']['lower_limit'] == '1' && $this->_tpl_vars['price']['usergroup_id'] == '0'): ?>
				&nbsp;<?php echo fn_get_lang_var('all', $this->getLanguage()); ?>

			<?php else: ?>
			<select id="usergroup_id" name="product_data[prices][<?php echo $this->_tpl_vars['_key']; ?>
][usergroup_id]">
				<?php $_from = fn_get_default_usergroups(""); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['usergroup']):
?>
					<option <?php if ($this->_tpl_vars['price']['usergroup_id'] == $this->_tpl_vars['usergroup']['usergroup_id']): ?>selected="selected"<?php endif; ?> value="<?php echo $this->_tpl_vars['usergroup']['usergroup_id']; ?>
"><?php echo smarty_modifier_escape($this->_tpl_vars['usergroup']['usergroup']); ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
				<?php $_from = $this->_tpl_vars['usergroups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['usergroup']):
?>
					<option <?php if ($this->_tpl_vars['price']['usergroup_id'] == $this->_tpl_vars['usergroup']['usergroup_id']): ?>selected="selected"<?php endif; ?> value="<?php echo $this->_tpl_vars['usergroup']['usergroup_id']; ?>
"><?php echo smarty_modifier_escape($this->_tpl_vars['usergroup']['usergroup']); ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
			<?php endif; ?></td>
				<td class="nowrap">
			<?php if ($this->_tpl_vars['price']['lower_limit'] == '1' && $this->_tpl_vars['price']['usergroup_id'] == '0'): ?>
			&nbsp;<?php else: ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/clone_delete.tpl", 'smarty_include_vars' => array('microformats' => "cm-delete-row",'no_confirm' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
	<?php echo smarty_function_math(array('equation' => "x+1",'x' => smarty_modifier_default(@$this->_tpl_vars['_key'], 0),'assign' => 'new_key'), $this);?>

	<tr <?php echo smarty_function_cycle(array('values' => "class=\"table-row\", ",'reset' => 1), $this);?>
 id="box_add_qty_discount">
		<td>
			<input type="text" name="product_data[prices][<?php echo $this->_tpl_vars['new_key']; ?>
][lower_limit]" value="" class="input-text-short" /></td>
		<td>
			<input type="text" name="product_data[prices][<?php echo $this->_tpl_vars['new_key']; ?>
][price]" value="0.00" size="10" class="input-text-medium" /></td>
				<td>
			<select id="usergroup_id" name="product_data[prices][<?php echo $this->_tpl_vars['new_key']; ?>
][usergroup_id]">
				<?php $_from = fn_get_default_usergroups(""); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['usergroup']):
?>
					<option value="<?php echo $this->_tpl_vars['usergroup']['usergroup_id']; ?>
"><?php echo smarty_modifier_escape($this->_tpl_vars['usergroup']['usergroup']); ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
				<?php $_from = $this->_tpl_vars['usergroups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['usergroup']):
?>
					<option value="<?php echo $this->_tpl_vars['usergroup']['usergroup_id']; ?>
"><?php echo smarty_modifier_escape($this->_tpl_vars['usergroup']['usergroup']); ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
		</td>
				<td class="right">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/multiple_buttons.tpl", 'smarty_include_vars' => array('item_id' => 'add_qty_discount')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</td>
	</tr>
	</tbody>
	</table>

</div>