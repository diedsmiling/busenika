<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:17
         compiled from addons/buy_together/hooks/product_picker/table_rows.pre.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'addons/buy_together/hooks/product_picker/table_rows.pre.tpl', 8, false),array('modifier', 'round', 'addons/buy_together/hooks/product_picker/table_rows.pre.tpl', 29, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('by_fixed','to_fixed','by_percentage','to_percentage'));
?>

<?php if ($this->_tpl_vars['controller'] == 'buy_together' || $this->_tpl_vars['extra_mode'] == 'buy_together'): ?>

<?php if ($this->_tpl_vars['product_data']['min_qty'] == 0 || $this->_tpl_vars['item']['min_qty'] == 0): ?>
	<?php $this->assign('min_qty', '1', false); ?>
<?php else: ?>
	<?php $this->assign('min_qty', smarty_modifier_default(@$this->_tpl_vars['product_data']['min_qty'], @$this->_tpl_vars['item']['min_qty']), false); ?>
<?php endif; ?>

<tr>
	<td><?php echo smarty_modifier_default(@$this->_tpl_vars['item']['product_name'], @$this->_tpl_vars['product_data']['product']); ?>
</td>
	<td><?php echo $this->_tpl_vars['min_qty']; ?>
</td>
	<td>
		<input type="hidden" id="item_price_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" value="<?php echo smarty_modifier_default(smarty_modifier_default(@$this->_tpl_vars['item']['price'], @$this->_tpl_vars['product_data']['price']), '0'); ?>
" />
		<input type="hidden" name="item_data_bt_[amount]" id="item_amount_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" value="<?php echo $this->_tpl_vars['min_qty']; ?>
" />
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => smarty_modifier_default(@$this->_tpl_vars['item']['price'], @$this->_tpl_vars['product_data']['price']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
	<td>
		<select id="item_modifier_type_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" name="item_data[modifier_type]">
			<option value="by_fixed" <?php if ($this->_tpl_vars['item']['modifier_type'] == 'by_fixed'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('by_fixed', $this->getLanguage()); ?>
</option>
			<option value="to_fixed" <?php if ($this->_tpl_vars['item']['modifier_type'] == 'to_fixed'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('to_fixed', $this->getLanguage()); ?>
</option>
			<option value="by_percentage" <?php if ($this->_tpl_vars['item']['modifier_type'] == 'by_percentage'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('by_percentage', $this->getLanguage()); ?>
</option>
			<option value="to_percentage" <?php if ($this->_tpl_vars['item']['modifier_type'] == 'to_percentage'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('to_percentage', $this->getLanguage()); ?>
</option>
		</select>
	</td>
	<td>
		<input type="hidden" class="cm-chain-<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" value="<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" />
		<input type="text" name="item_data[modifier]" id="item_modifier_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" size="4" value="<?php echo round(smarty_modifier_default(@$this->_tpl_vars['item']['modifier'], 0), $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['decimals']); ?>
" class="input-text" />
	</td>
	<td><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => smarty_modifier_default(smarty_modifier_default(@$this->_tpl_vars['item']['discounted_price'], @$this->_tpl_vars['product_data']['price']), '0'),'span_id' => "item_discounted_price_bt_".($this->_tpl_vars['item']['chain_id'])."_".($this->_tpl_vars['item']['chain_id'])."_")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
	<td>&nbsp;</td>
</tr>
<?php endif; ?>