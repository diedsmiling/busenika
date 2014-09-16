<?php /* Smarty version 2.6.18, created on 2014-09-15 23:39:45
         compiled from views/products/components/products_update_features.tpl */ ?>
<?php
fn_preload_lang_vars(array('no_items'));
?>

<div id="content_features" class="hidden">

<?php if ($this->_tpl_vars['product_data']['product_features']): ?>
<fieldset>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_assign_features.tpl", 'smarty_include_vars' => array('product_features' => $this->_tpl_vars['product_data']['product_features'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</fieldset>
<?php else: ?>
<p class="no-items"><?php echo fn_get_lang_var('no_items', $this->getLanguage()); ?>
</p>
<?php endif; ?>
</div>