<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:45
         compiled from pickers/products_picker.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'math', 'pickers/products_picker.tpl', 3, false),array('function', 'script', 'pickers/products_picker.tpl', 8, false),array('modifier', 'default', 'pickers/products_picker.tpl', 5, false),array('modifier', 'is_array', 'pickers/products_picker.tpl', 10, false),array('modifier', 'explode', 'pickers/products_picker.tpl', 11, false),array('modifier', 'implode', 'pickers/products_picker.tpl', 16, false),array('modifier', 'fn_get_product_name', 'pickers/products_picker.tpl', 30, false),array('modifier', 'count', 'pickers/products_picker.tpl', 43, false),array('modifier', 'fn_get_product_options', 'pickers/products_picker.tpl', 63, false),array('modifier', 'fn_get_selected_product_options_info', 'pickers/products_picker.tpl', 70, false),array('modifier', 'escape', 'pickers/products_picker.tpl', 108, false),array('modifier', 'fn_url', 'pickers/products_picker.tpl', 128, false),array('block', 'hook', 'pickers/products_picker.tpl', 54, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('position_short','name','deleted_product','no_items','editing_defined_products','defined_items','name','quantity','options','any_option_combinations','deleted_product','no_items','add_products','add_products_and_close','add_products','add_products'));
?>

<?php echo smarty_function_math(array('equation' => "rand()",'assign' => 'rnd'), $this);?>

<?php $this->assign('data_id', ($this->_tpl_vars['data_id'])."_".($this->_tpl_vars['rnd']), false); ?>
<?php $this->assign('view_mode', smarty_modifier_default(@$this->_tpl_vars['view_mode'], 'mixed'), false); ?>
<?php $this->assign('start_pos', smarty_modifier_default(@$this->_tpl_vars['start_pos'], 0), false); ?>

<?php echo smarty_function_script(array('src' => "js/picker.js"), $this);?>


<?php if ($this->_tpl_vars['item_ids'] && ! is_array($this->_tpl_vars['item_ids']) && $this->_tpl_vars['type'] != 'table'): ?>
	<?php $this->assign('item_ids', explode(",", $this->_tpl_vars['item_ids']), false); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['view_mode'] != 'button'): ?>
<?php if ($this->_tpl_vars['type'] == 'links'): ?>
	<input type="hidden" id="p<?php echo $this->_tpl_vars['data_id']; ?>
_ids" name="<?php echo $this->_tpl_vars['input_name']; ?>
" value="<?php if ($this->_tpl_vars['item_ids']): ?><?php echo implode(",", $this->_tpl_vars['item_ids']); ?>
<?php endif; ?>" />
	<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['picker_view']): ?><div class="object-container"><?php endif; ?>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<?php if ($this->_tpl_vars['positions']): ?><th><?php echo fn_get_lang_var('position_short', $this->getLanguage()); ?>
</th><?php endif; ?>
		<th width="100%"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
	</tr>
	<tbody id="<?php echo $this->_tpl_vars['data_id']; ?>
"<?php if (! $this->_tpl_vars['item_ids']): ?> class="hidden"<?php endif; ?>>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/js_product.tpl", 'smarty_include_vars' => array('clone' => true,'product' => ($this->_tpl_vars['ldelim'])."product".($this->_tpl_vars['rdelim']),'root_id' => $this->_tpl_vars['data_id'],'delete_id' => ($this->_tpl_vars['ldelim'])."delete_id".($this->_tpl_vars['rdelim']),'type' => 'product','position_field' => $this->_tpl_vars['positions'],'position' => '0')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php if ($this->_tpl_vars['item_ids']): ?>
	<?php $_from = $this->_tpl_vars['item_ids']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['items'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['items']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['product']):
        $this->_foreach['items']['iteration']++;
?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/js_product.tpl", 'smarty_include_vars' => array('product' => smarty_modifier_default(fn_get_product_name($this->_tpl_vars['product']), fn_get_lang_var('deleted_product', $this->getLanguage())),'root_id' => $this->_tpl_vars['data_id'],'delete_id' => $this->_tpl_vars['product'],'type' => 'product','first_item' => ($this->_foreach['items']['iteration'] <= 1),'position_field' => $this->_tpl_vars['positions'],'position' => $this->_foreach['items']['iteration']+$this->_tpl_vars['start_pos'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>
	</tbody>
	<tbody id="<?php echo $this->_tpl_vars['data_id']; ?>
_no_item"<?php if ($this->_tpl_vars['item_ids']): ?> class="hidden"<?php endif; ?>>
	<tr class="no-items">
		<td colspan="<?php if ($this->_tpl_vars['positions']): ?>4<?php else: ?>3<?php endif; ?>"><p><?php echo smarty_modifier_default(@$this->_tpl_vars['no_item_text'], fn_get_lang_var('no_items', $this->getLanguage())); ?>
</p></td>
	</tr>
	</tbody>
	</table>
	<?php if ($this->_tpl_vars['picker_view']): ?></div><?php endif; ?>
	<?php $this->_smarty_vars['capture']['products_list'] = ob_get_contents(); ob_end_clean(); ?>
	<?php if ($this->_tpl_vars['picker_view']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => "inner_".($this->_tpl_vars['data_id']),'link_text' => count($this->_tpl_vars['item_ids']),'act' => 'edit','content' => $this->_smarty_vars['capture']['products_list'],'text' => (fn_get_lang_var('editing_defined_products', $this->getLanguage())).":",'link_class' => "text-button-edit",'picker_meta' => "cm-bg-close")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo fn_get_lang_var('defined_items', $this->getLanguage()); ?>

	<?php else: ?>
		<?php echo $this->_smarty_vars['capture']['products_list']; ?>

	<?php endif; ?>

<?php elseif ($this->_tpl_vars['type'] == 'table'): ?>

	<table class="table" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<th width="80%"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
</th>
		<th><?php echo fn_get_lang_var('quantity', $this->getLanguage()); ?>
</th>
		<?php $this->_tag_stack[] = array('hook', array('name' => "product_picker:table_header")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<?php if ($this->_tpl_vars['addons']['buy_together']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/buy_together/hooks/product_picker/table_header.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		<th>&nbsp;</th>
	</tr>
	<tbody id="<?php echo $this->_tpl_vars['data_id']; ?>
" class="<?php if (! $this->_tpl_vars['item_ids']): ?>hidden<?php endif; ?> cm-picker-options">
	<?php $this->_tag_stack[] = array('hook', array('name' => "product_picker:table_rows")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if ($this->_tpl_vars['addons']['buy_together']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/buy_together/hooks/product_picker/table_rows.pre.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
	<?php if ($this->_tpl_vars['item_ids']): ?>
	<?php $_from = $this->_tpl_vars['item_ids']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['product_id'] => $this->_tpl_vars['product']):
?>
		<?php ob_start(); ?>
			<?php $this->assign('prod_opts', fn_get_product_options($this->_tpl_vars['product']['product_id']), false); ?>
			<?php if ($this->_tpl_vars['prod_opts'] && ! $this->_tpl_vars['product']['product_options']): ?>
				<strong><?php echo fn_get_lang_var('options', $this->getLanguage()); ?>
: </strong>&nbsp;<?php echo fn_get_lang_var('any_option_combinations', $this->getLanguage()); ?>

			<?php elseif ($this->_tpl_vars['product']['product_options']): ?>
				<?php if ($this->_tpl_vars['product']['product_options_value']): ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/options_info.tpl", 'smarty_include_vars' => array('product_options' => $this->_tpl_vars['product']['product_options_value'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php else: ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/options_info.tpl", 'smarty_include_vars' => array('product_options' => fn_get_selected_product_options_info($this->_tpl_vars['product']['product_options']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php $this->_smarty_vars['capture']['product_options'] = ob_get_contents(); ob_end_clean(); ?>
		<?php if ($this->_tpl_vars['product']['product']): ?>
			<?php $this->assign('product_name', $this->_tpl_vars['product']['product'], false); ?>
		<?php else: ?>
			<?php $this->assign('product_name', smarty_modifier_default(fn_get_product_name($this->_tpl_vars['product']['product_id']), fn_get_lang_var('deleted_product', $this->getLanguage())), false); ?>
		<?php endif; ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/js_product.tpl", 'smarty_include_vars' => array('product' => $this->_tpl_vars['product_name'],'root_id' => $this->_tpl_vars['data_id'],'delete_id' => $this->_tpl_vars['product_id'],'input_name' => ($this->_tpl_vars['input_name'])."[".($this->_tpl_vars['product_id'])."]",'amount' => $this->_tpl_vars['product']['amount'],'amount_input' => 'text','type' => 'options','options' => $this->_smarty_vars['capture']['product_options'],'options_array' => $this->_tpl_vars['product']['product_options'],'product_id' => $this->_tpl_vars['product']['product_id'],'product_info' => $this->_tpl_vars['product'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>
	<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/js_product.tpl", 'smarty_include_vars' => array('clone' => true,'product' => ($this->_tpl_vars['ldelim'])."product".($this->_tpl_vars['rdelim']),'root_id' => $this->_tpl_vars['data_id'],'delete_id' => ($this->_tpl_vars['ldelim'])."delete_id".($this->_tpl_vars['rdelim']),'input_name' => ($this->_tpl_vars['input_name'])."[".($this->_tpl_vars['ldelim'])."product_id".($this->_tpl_vars['rdelim'])."]",'amount' => '1','amount_input' => 'text','type' => 'options','options' => ($this->_tpl_vars['ldelim'])."options".($this->_tpl_vars['rdelim']),'product_id' => "")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</tbody>
	<tbody id="<?php echo $this->_tpl_vars['data_id']; ?>
_no_item"<?php if ($this->_tpl_vars['item_ids']): ?> class="hidden"<?php endif; ?>>
	<tr class="no-items">
		<td colspan="<?php echo smarty_modifier_default(@$this->_tpl_vars['colspan'], '3'); ?>
"><p><?php echo smarty_modifier_default(@$this->_tpl_vars['no_item_text'], fn_get_lang_var('no_items', $this->getLanguage())); ?>
</p></td>
	</tr>
	</tbody>
	</table>
	<?php if (! $this->_tpl_vars['display']): ?>
		<?php $this->assign('display', 'options', false); ?>
	<?php endif; ?>
<?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['view_mode'] != 'list'): ?>

	<?php $this->assign('but_text', smarty_modifier_default(@$this->_tpl_vars['but_text'], fn_get_lang_var('add_products', $this->getLanguage())), false); ?>
	<?php if (! $this->_tpl_vars['no_container']): ?><div class="buttons-container"><?php endif; ?>
		<?php if ($this->_tpl_vars['picker_view']): ?>[<?php endif; ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_id' => "opener_picker_".($this->_tpl_vars['data_id']),'but_text' => $this->_tpl_vars['but_text'],'but_onclick' => "jQuery.show_picker('picker_".($this->_tpl_vars['data_id'])."', this.id);",'but_role' => 'add','but_meta' => "text-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php if ($this->_tpl_vars['picker_view']): ?>]<?php endif; ?>
	<?php if (! $this->_tpl_vars['no_container']): ?></div><?php endif; ?>

	<?php ob_start(); ?>
		
		<?php ob_start(); ?><?php echo $this->_tpl_vars['index_script']; ?>
?dispatch=products.picker<?php if ($this->_tpl_vars['display']): ?>&amp;display=<?php echo $this->_tpl_vars['display']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['holder_name']): ?>&amp;holder_name=<?php echo $this->_tpl_vars['holder_name']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['extra_var']): ?>&amp;extra=<?php echo smarty_modifier_escape($this->_tpl_vars['extra_var'], 'url'); ?>
<?php endif; ?><?php if ($this->_tpl_vars['checkbox_name']): ?>&amp;checkbox_name=<?php echo $this->_tpl_vars['checkbox_name']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['aoc']): ?>&amp;aoc=1<?php endif; ?><?php $this->_smarty_vars['capture']['iframe_url'] = ob_get_contents(); ob_end_clean(); ?>
		<div class="cm-picker-data-container" id="iframe_container_<?php echo $this->_tpl_vars['data_id']; ?>
"></div>
		<div class="buttons-container">
			<?php $this->assign('extra_buttons', "extra_buttons_".($this->_tpl_vars['rnd']), false); ?>
			<?php if (! $this->_tpl_vars['extra_var']): ?>
				<?php $this->assign('_but_text', fn_get_lang_var('add_products_and_close', $this->getLanguage()), false); ?>
				<?php $this->assign('_act', "#add_item_close", false); ?>
				<?php ob_start(); ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_type' => 'button','but_onclick' => "jQuery.submit_picker('#iframe_".($this->_tpl_vars['data_id'])."', '#add_item')",'but_text' => fn_get_lang_var('add_products', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php $this->_smarty_vars['capture'][$this->_tpl_vars['extra_buttons']] = ob_get_contents(); ob_end_clean(); ?>
			<?php else: ?>
				<?php $this->assign('_but_text', fn_get_lang_var('add_products', $this->getLanguage()), false); ?>
				<?php $this->assign('_act', "#add_item", false); ?>
			<?php endif; ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_type' => 'button','but_onclick' => "jQuery.submit_picker('#iframe_".($this->_tpl_vars['data_id'])."', '".($this->_tpl_vars['_act'])."')",'but_text' => $this->_tpl_vars['_but_text'],'cancel_action' => 'close','extra' => $this->_smarty_vars['capture'][$this->_tpl_vars['extra_buttons']])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	<?php $this->_smarty_vars['capture']['picker_content'] = ob_get_contents(); ob_end_clean(); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/picker_skin.tpl", 'smarty_include_vars' => array('picker_content' => $this->_smarty_vars['capture']['picker_content'],'data_id' => $this->_tpl_vars['data_id'],'but_text' => $this->_tpl_vars['but_text'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<script type="text/javascript">
	//<![CDATA[
		iframe_urls['<?php echo $this->_tpl_vars['data_id']; ?>
'] = '<?php echo smarty_modifier_escape(fn_url($this->_smarty_vars['capture']['iframe_url']), 'javascript'); ?>
';
		<?php if ($this->_tpl_vars['extra_var']): ?>
		iframe_extra['<?php echo $this->_tpl_vars['data_id']; ?>
'] = '<?php echo smarty_modifier_escape($this->_tpl_vars['extra_var'], 'javascript'); ?>
';
		<?php endif; ?>
	//]]>
	</script>
<?php endif; ?>