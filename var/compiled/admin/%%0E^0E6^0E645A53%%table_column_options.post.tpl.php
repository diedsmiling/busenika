<?php /* Smarty version 2.6.18, created on 2014-09-15 23:39:46
         compiled from addons/buy_together/hooks/product_picker/table_column_options.post.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'addons/buy_together/hooks/product_picker/table_column_options.post.tpl', 5, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('by_fixed','to_fixed','by_percentage','to_percentage','by_fixed','to_fixed','by_percentage','to_percentage'));
?>

<?php if (( $this->_tpl_vars['controller'] == 'buy_together' || $this->_tpl_vars['extra_mode'] == 'buy_together' ) && $this->_tpl_vars['product_info']): ?>
	<td>
		<input type="hidden" id="item_price_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
_<?php echo $this->_tpl_vars['delete_id']; ?>
" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['product_info']['price'], 0); ?>
" />
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['product_info']['price'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
	<td>
		<select name="<?php echo $this->_tpl_vars['input_name']; ?>
[modifier_type]" id="item_modifier_type_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
_<?php echo $this->_tpl_vars['delete_id']; ?>
">
			<option value="by_fixed" <?php if ($this->_tpl_vars['product_info']['modifier_type'] == 'by_fixed'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('by_fixed', $this->getLanguage()); ?>
</option>
			<option value="to_fixed" <?php if ($this->_tpl_vars['product_info']['modifier_type'] == 'to_fixed'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('to_fixed', $this->getLanguage()); ?>
</option>
			<option value="by_percentage" <?php if ($this->_tpl_vars['product_info']['modifier_type'] == 'by_percentage'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('by_percentage', $this->getLanguage()); ?>
</option>
			<option value="to_percentage" <?php if ($this->_tpl_vars['product_info']['modifier_type'] == 'to_percentage'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('to_percentage', $this->getLanguage()); ?>
</option>
		</select>
	</td>
	<td>
		<input type="hidden" class="cm-chain-<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" value="<?php echo $this->_tpl_vars['delete_id']; ?>
" />
		<input type="text" name="<?php echo $this->_tpl_vars['input_name']; ?>
[modifier]" id="item_modifier_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
_<?php echo $this->_tpl_vars['delete_id']; ?>
" size="4" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['product_info']['modifier'], 0); ?>
" class="input-text" />
	</td>
	<td>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['product_info']['discounted_price'],'span_id' => "item_discounted_price_bt_".($this->_tpl_vars['item']['chain_id'])."_".($this->_tpl_vars['delete_id'])."_")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
	
<?php elseif (( $this->_tpl_vars['controller'] == 'buy_together' || $this->_tpl_vars['extra_mode'] == 'buy_together' ) && $this->_tpl_vars['clone']): ?>
	<td>
		<input type="text" class="hidden" id="item_price_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
_<?php echo $this->_tpl_vars['ldelim']; ?>
bt_id<?php echo $this->_tpl_vars['rdelim']; ?>
" value="<?php echo $this->_tpl_vars['ldelim']; ?>
price<?php echo $this->_tpl_vars['rdelim']; ?>
" />
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('span_id' => "item_display_price_bt_".($this->_tpl_vars['item']['chain_id'])."_".($this->_tpl_vars['ldelim'])."bt_id".($this->_tpl_vars['rdelim'])."_")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
	<td>
		<select name="<?php echo $this->_tpl_vars['input_name']; ?>
[modifier_type]" id="item_modifier_type_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
_<?php echo $this->_tpl_vars['ldelim']; ?>
bt_id<?php echo $this->_tpl_vars['rdelim']; ?>
">
			<option value="by_fixed"><?php echo fn_get_lang_var('by_fixed', $this->getLanguage()); ?>
</option>
			<option value="to_fixed"><?php echo fn_get_lang_var('to_fixed', $this->getLanguage()); ?>
</option>
			<option value="by_percentage"><?php echo fn_get_lang_var('by_percentage', $this->getLanguage()); ?>
</option>
			<option value="to_percentage"><?php echo fn_get_lang_var('to_percentage', $this->getLanguage()); ?>
</option>
		</select>
	</td>
	<td>
		<input type="text" class="cm-chain-<?php echo $this->_tpl_vars['item']['chain_id']; ?>
 hidden" value="<?php echo $this->_tpl_vars['ldelim']; ?>
bt_id<?php echo $this->_tpl_vars['rdelim']; ?>
" />
		<input type="text" class="hidden" id="<?php echo $this->_tpl_vars['ldelim']; ?>
bt_id<?php echo $this->_tpl_vars['rdelim']; ?>
" value="<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" />
		<input type="text" name="<?php echo $this->_tpl_vars['input_name']; ?>
[modifier]" id="item_modifier_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
_<?php echo $this->_tpl_vars['ldelim']; ?>
bt_id<?php echo $this->_tpl_vars['rdelim']; ?>
" size="4" value="0" class="input-text" />
	</td>
	<td>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('span_id' => "item_discounted_price_bt_".($this->_tpl_vars['item']['chain_id'])."_".($this->_tpl_vars['ldelim'])."bt_id".($this->_tpl_vars['rdelim'])."_")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
<?php endif; ?>