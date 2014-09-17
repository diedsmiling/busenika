<?php /* Smarty version 2.6.18, created on 2014-09-16 21:38:45
         compiled from blocks/list_templates/compact_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'blocks/list_templates/compact_list.tpl', 5, false),array('function', 'cycle', 'blocks/list_templates/compact_list.tpl', 31, false),array('modifier', 'fn_url', 'blocks/list_templates/compact_list.tpl', 14, false),array('modifier', 'trim', 'blocks/list_templates/compact_list.tpl', 30, false),array('block', 'hook', 'blocks/list_templates/compact_list.tpl', 30, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('product','product_code','price'));
?>

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

<form <?php if ($this->_tpl_vars['settings']['DHTML']['ajax_add_to_cart'] == 'Y'): ?>class="cm-ajax"<?php endif; ?> action="<?php echo fn_url(""); ?>
" method="post" name="short_list_form<?php echo $this->_tpl_vars['obj_prefix']; ?>
">
<input type="hidden" name="result_ids" value="cart_status,wish_list" />

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table">
<tr>
	<th>&nbsp;</th>
	<th><?php echo fn_get_lang_var('product', $this->getLanguage()); ?>
</th>
	<th><?php echo fn_get_lang_var('product_code', $this->getLanguage()); ?>
</th>
	<th><?php echo fn_get_lang_var('price', $this->getLanguage()); ?>
</th>
	<?php if ($this->_tpl_vars['show_add_to_cart']): ?><th>&nbsp;</th><?php endif; ?>
</tr>

<?php $_from = $this->_tpl_vars['products']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['products'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['products']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['product']):
        $this->_foreach['products']['iteration']++;
?>
	<?php $this->assign('obj_id', $this->_tpl_vars['product']['product_id'], false); ?>
	<?php $this->assign('obj_id_prefix', ($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['product']['product_id']), false); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/product_data.tpl", 'smarty_include_vars' => array('product' => $this->_tpl_vars['product'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php if ($this->_tpl_vars['addons']['age_verification']['status'] == 'A'): ?><?php ob_start(); $this->_in_capture[] = '52c2d40435636f7bf743000e0d2d54a9';
$_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/age_verification/hooks/products/product_compact_list.override.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
$this->_tpl_vars['addon_content'] = ob_get_contents(); ob_end_clean(); array_pop($this->_in_capture); if (!empty($this->_scripts['52c2d40435636f7bf743000e0d2d54a9'])) { echo implode("\n", $this->_scripts['52c2d40435636f7bf743000e0d2d54a9']); unset($this->_scripts['52c2d40435636f7bf743000e0d2d54a9']); }
 ?><?php else: ?><?php $this->assign('addon_content', "", false); ?><?php endif; ?><?php if (trim($this->_tpl_vars['addon_content'])): ?><?php echo $this->_tpl_vars['addon_content']; ?>
<?php else: ?><?php $this->_tag_stack[] = array('hook', array('name' => "products:product_compact_list")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<tr <?php echo smarty_function_cycle(array('values' => ",class=\"table-row\""), $this);?>
 valign="middle">
		<td class="product-image">
			<a href="<?php echo fn_url("products.view?product_id=".($this->_tpl_vars['product']['product_id'])); ?>
"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('image_width' => '40','images' => $this->_tpl_vars['product']['main_pair'],'object_type' => 'product','obj_id' => $this->_tpl_vars['obj_id_prefix'],'show_thumbnail' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></a>
		</td>
		<td class="compact">
			<?php $this->assign('name', "name_".($this->_tpl_vars['obj_id']), false); ?><?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['name']]; ?>

		</td>
		<td class="center">
			<?php $this->assign('sku', "sku_".($this->_tpl_vars['obj_id']), false); ?><?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['sku']]; ?>

		</td>
		<td class="center">
			<?php $this->assign('price', "price_".($this->_tpl_vars['obj_id']), false); ?><?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['price']]; ?>

		</td>
		<?php if ($this->_tpl_vars['show_add_to_cart']): ?>
		<td class="center nowrap">
			<?php $this->assign('add_to_cart', "add_to_cart_".($this->_tpl_vars['obj_id']), false); ?><?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['add_to_cart']]; ?>

		</td>
		<?php endif; ?>
	</tr>
	<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
</table>

</form>
<?php if (! $this->_tpl_vars['no_pagination']): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php endif; ?>