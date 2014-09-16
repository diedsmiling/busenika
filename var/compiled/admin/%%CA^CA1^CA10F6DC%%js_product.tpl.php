<?php /* Smarty version 2.6.18, created on 2014-09-15 23:39:46
         compiled from pickers/js_product.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'math', 'pickers/js_product.tpl', 5, false),array('modifier', 'is_array', 'pickers/js_product.tpl', 13, false),array('modifier', 'fn_url', 'pickers/js_product.tpl', 47, false),array('modifier', 'escape', 'pickers/js_product.tpl', 47, false),array('block', 'hook', 'pickers/js_product.tpl', 31, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('delete','delete'));
?>

<?php if ($this->_tpl_vars['type'] == 'options'): ?>
<tr <?php if (! $this->_tpl_vars['clone']): ?>id="<?php echo $this->_tpl_vars['root_id']; ?>
_<?php echo $this->_tpl_vars['delete_id']; ?>
" <?php endif; ?>class="cm-js-item<?php if ($this->_tpl_vars['clone']): ?> cm-clone hidden<?php endif; ?>">
<?php if ($this->_tpl_vars['position_field']): ?><td><input type="text" name="<?php echo $this->_tpl_vars['input_name']; ?>
[<?php echo $this->_tpl_vars['delete_id']; ?>
]" value="<?php echo smarty_function_math(array('equation' => "a*b",'a' => $this->_tpl_vars['position'],'b' => 10), $this);?>
" size="3" class="input-text-short" <?php if ($this->_tpl_vars['clone']): ?>disabled="disabled"<?php endif; ?> /></td><?php endif; ?>
<td>
	<ul>
		<li><?php echo $this->_tpl_vars['product']; ?>
</li>
		<?php if ($this->_tpl_vars['options']): ?>
		<li><?php echo $this->_tpl_vars['options']; ?>
</li>
		<?php endif; ?>
	</ul>
	<?php if (is_array($this->_tpl_vars['options_array'])): ?>
		<?php $_from = $this->_tpl_vars['options_array']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['option_id'] => $this->_tpl_vars['option']):
?>
		<input type="hidden" name="<?php echo $this->_tpl_vars['input_name']; ?>
[product_options][<?php echo $this->_tpl_vars['option_id']; ?>
]" value="<?php echo $this->_tpl_vars['option']; ?>
"<?php if ($this->_tpl_vars['clone']): ?> disabled="disabled"<?php endif; ?> />
		<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['product_id']): ?>
		<input type="hidden" name="<?php echo $this->_tpl_vars['input_name']; ?>
[product_id]" value="<?php echo $this->_tpl_vars['product_id']; ?>
"<?php if ($this->_tpl_vars['clone']): ?> disabled="disabled"<?php endif; ?> />
	<?php endif; ?>
	<?php if ($this->_tpl_vars['amount_input'] == 'hidden'): ?>
	<input type="hidden" name="<?php echo $this->_tpl_vars['input_name']; ?>
[amount]" value="<?php echo $this->_tpl_vars['amount']; ?>
"<?php if ($this->_tpl_vars['clone']): ?> disabled="disabled"<?php endif; ?> />
	<?php endif; ?>
</td>
	<?php if ($this->_tpl_vars['amount_input'] == 'text'): ?>
<td>
	<input type="text" name="<?php echo $this->_tpl_vars['input_name']; ?>
[amount]" value="<?php echo $this->_tpl_vars['amount']; ?>
" size="3" class="input-text-short"<?php if ($this->_tpl_vars['clone']): ?> disabled="disabled"<?php endif; ?> />
</td>
	<?php endif; ?>
	
	<?php $this->_tag_stack[] = array('hook', array('name' => "product_picker:table_column_options")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<?php if ($this->_tpl_vars['addons']['buy_together']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/buy_together/hooks/product_picker/table_column_options.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
	
<td class="nowrap">
	<?php if (! $this->_tpl_vars['hide_delete_button']): ?>
		<?php ob_start(); ?>
		<li><a onclick="jQuery.delete_js_item('<?php echo $this->_tpl_vars['root_id']; ?>
', '<?php echo $this->_tpl_vars['delete_id']; ?>
', 'p'); return false;"><?php echo fn_get_lang_var('delete', $this->getLanguage()); ?>
</a></li>
		<?php $this->_smarty_vars['capture']['tools_items'] = ob_get_contents(); ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/table_tools_list.tpl", 'smarty_include_vars' => array('prefix' => $this->_tpl_vars['category_id'],'tools_list' => $this->_smarty_vars['capture']['tools_items'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>&nbsp;<?php endif; ?>
</td>
</tr>

<?php elseif ($this->_tpl_vars['type'] == 'product'): ?>
	<tr <?php if (! $this->_tpl_vars['clone']): ?>id="<?php echo $this->_tpl_vars['root_id']; ?>
_<?php echo $this->_tpl_vars['delete_id']; ?>
" <?php endif; ?>class="cm-js-item<?php if ($this->_tpl_vars['clone']): ?> cm-clone hidden<?php endif; ?>">
		<?php if ($this->_tpl_vars['position_field']): ?><td><input type="text" name="<?php echo $this->_tpl_vars['input_name']; ?>
[<?php echo $this->_tpl_vars['delete_id']; ?>
]" value="<?php echo smarty_function_math(array('equation' => "a*b",'a' => $this->_tpl_vars['position'],'b' => 10), $this);?>
" size="3" class="input-text-short" <?php if ($this->_tpl_vars['clone']): ?>disabled="disabled"<?php endif; ?> /></td><?php endif; ?>
		<td><a href="<?php echo fn_url("products.update?product_id=".($this->_tpl_vars['delete_id'])); ?>
"><?php echo smarty_modifier_escape($this->_tpl_vars['product']); ?>
</a></td>
		<td>&nbsp;</td>
		<td class="nowrap"><?php if (! $this->_tpl_vars['hide_delete_button']): ?>
			<?php ob_start(); ?>
			<li><a onclick="jQuery.delete_js_item('<?php echo $this->_tpl_vars['root_id']; ?>
', '<?php echo $this->_tpl_vars['delete_id']; ?>
', 'p'); return false;"><?php echo fn_get_lang_var('delete', $this->getLanguage()); ?>
</a></li>
			<?php $this->_smarty_vars['capture']['tools_items'] = ob_get_contents(); ob_end_clean(); ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/table_tools_list.tpl", 'smarty_include_vars' => array('prefix' => $this->_tpl_vars['category_id'],'tools_list' => $this->_smarty_vars['capture']['tools_items'],'href' => "products.update?product_id=".($this->_tpl_vars['delete_id']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>&nbsp;<?php endif; ?></td>
	</tr>
<?php endif; ?>