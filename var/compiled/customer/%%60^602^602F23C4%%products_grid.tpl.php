<?php /* Smarty version 2.6.18, created on 2014-09-22 22:56:06
         compiled from blocks/list_templates/products_grid.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'blocks/list_templates/products_grid.tpl', 5, false),array('function', 'split', 'blocks/list_templates/products_grid.tpl', 17, false),array('function', 'math', 'blocks/list_templates/products_grid.tpl', 18, false),array('modifier', 'sizeof', 'blocks/list_templates/products_grid.tpl', 14, false),array('modifier', 'default', 'blocks/list_templates/products_grid.tpl', 17, false),array('modifier', 'trim', 'blocks/list_templates/products_grid.tpl', 34, false),array('modifier', 'fn_url', 'blocks/list_templates/products_grid.tpl', 35, false),array('block', 'hook', 'blocks/list_templates/products_grid.tpl', 34, false),)), $this); ?>

<?php if ($this->_tpl_vars['products']): ?>

<?php echo smarty_function_script(array('src' => "js/exceptions.js"), $this);?>


<?php if (! $this->_tpl_vars['no_pagination']): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php if (! $this->_tpl_vars['no_sorting']): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/sorting.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php if (sizeof($this->_tpl_vars['products']) < $this->_tpl_vars['columns']): ?>
<?php $this->assign('columns', sizeof($this->_tpl_vars['products']), false); ?>
<?php endif; ?>
<?php echo smarty_function_split(array('data' => $this->_tpl_vars['products'],'size' => smarty_modifier_default(@$this->_tpl_vars['columns'], '3'),'assign' => 'splitted_products'), $this);?>

<?php echo smarty_function_math(array('equation' => "100 / x",'x' => smarty_modifier_default(@$this->_tpl_vars['columns'], '3'),'assign' => 'cell_width'), $this);?>

<?php if ($this->_tpl_vars['item_number'] == 'Y'): ?>
	<?php $this->assign('cur_number', 1, false); ?>
<?php endif; ?>
<table cellspacing="0" cellpadding="0" width="100%" border="0" class="fixed-layout multicolumns-list">
<?php $_from = $this->_tpl_vars['splitted_products']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['sprod'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['sprod']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['sproducts']):
        $this->_foreach['sprod']['iteration']++;
?>
<tr>
<?php $_from = $this->_tpl_vars['sproducts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['sproducts'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['sproducts']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['product']):
        $this->_foreach['sproducts']['iteration']++;
?>
	<?php $this->assign('obj_id', $this->_tpl_vars['product']['product_id'], false); ?>
	<?php $this->assign('obj_id_prefix', ($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['product']['product_id']), false); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/product_data.tpl", 'smarty_include_vars' => array('product' => $this->_tpl_vars['product'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<td class="product-spacer">&nbsp;</td>
	<td class="center image-border compact<?php if (! ($this->_foreach['sprod']['iteration'] == $this->_foreach['sprod']['total'])): ?> border-bottom<?php endif; ?>" valign="top" width="<?php echo $this->_tpl_vars['cell_width']; ?>
%">
	<?php if ($this->_tpl_vars['product']): ?>
		<?php $this->assign('form_open', "form_open_".($this->_tpl_vars['obj_id']), false); ?>
		<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['form_open']]; ?>

		<?php if ($this->_tpl_vars['addons']['age_verification']['status'] == 'A'): ?><?php ob_start(); $this->_in_capture[] = '6016b4d78df0ab212c056d8b478d5584';
$_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/age_verification/hooks/products/product_multicolumns_list.override.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
$this->_tpl_vars['addon_content'] = ob_get_contents(); ob_end_clean(); array_pop($this->_in_capture); if (!empty($this->_scripts['6016b4d78df0ab212c056d8b478d5584'])) { echo implode("\n", $this->_scripts['6016b4d78df0ab212c056d8b478d5584']); unset($this->_scripts['6016b4d78df0ab212c056d8b478d5584']); }
 ?><?php else: ?><?php $this->assign('addon_content', "", false); ?><?php endif; ?><?php if (trim($this->_tpl_vars['addon_content'])): ?><?php echo $this->_tpl_vars['addon_content']; ?>
<?php else: ?><?php $this->_tag_stack[] = array('hook', array('name' => "products:product_multicolumns_list")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<a href="<?php echo fn_url("products.view?product_id=".($this->_tpl_vars['product']['product_id'])); ?>
"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('obj_id' => $this->_tpl_vars['obj_id_prefix'],'images' => $this->_tpl_vars['product']['main_pair'],'object_type' => 'product','show_thumbnail' => 'Y','image_width' => $this->_tpl_vars['settings']['Thumbnails']['product_lists_thumbnail_width'],'image_height' => $this->_tpl_vars['settings']['Thumbnails']['product_lists_thumbnail_height'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></a>
	
		<p>
		<?php $this->assign('name', "name_".($this->_tpl_vars['obj_id']), false); ?><?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['name']]; ?>

		</p>
		
		<?php $this->assign('old_price', "old_price_".($this->_tpl_vars['obj_id']), false); ?>
		<?php if (trim($this->_smarty_vars['capture'][$this->_tpl_vars['old_price']])): ?><?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['old_price']]; ?>
&nbsp;<?php endif; ?>
		
		<?php $this->assign('price', "price_".($this->_tpl_vars['obj_id']), false); ?>
		<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['price']]; ?>

		
		<?php $this->assign('clean_price', "clean_price_".($this->_tpl_vars['obj_id']), false); ?>
		<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['clean_price']]; ?>

		
		<div class="buttons-container">
			<?php $this->assign('add_to_cart', "add_to_cart_".($this->_tpl_vars['obj_id']), false); ?>
			<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['add_to_cart']]; ?>

		</div>
		<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?>
		<?php $this->assign('form_close', "form_close_".($this->_tpl_vars['obj_id']), false); ?>
		<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['form_close']]; ?>

	<?php endif; ?>
	</td>
	<td class="product-spacer">&nbsp;</td>
<?php endforeach; endif; unset($_from); ?>
</tr>
<?php endforeach; endif; unset($_from); ?>
</table>

<?php if (! $this->_tpl_vars['no_pagination']): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php endif; ?>

<?php ob_start(); ?><?php echo $this->_tpl_vars['title']; ?>
<?php $this->_smarty_vars['capture']['mainbox_title'] = ob_get_contents(); ob_end_clean(); ?>