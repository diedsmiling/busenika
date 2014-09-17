<?php /* Smarty version 2.6.18, created on 2014-09-17 00:29:31
         compiled from blocks/grid_list.tpl */ ?>

<?php if ($this->_tpl_vars['block']['properties']['hide_add_to_cart_button'] == 'Y'): ?>
	<?php $this->assign('_show_add_to_cart', false, false); ?>
<?php else: ?>
	<?php $this->assign('_show_add_to_cart', true, false); ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "blocks/product_list_templates/products_grid.tpl", 'smarty_include_vars' => array('products' => $this->_tpl_vars['items'],'columns' => $this->_tpl_vars['block']['properties']['number_of_columns'],'no_sorting' => 'Y','obj_prefix' => ($this->_tpl_vars['block']['block_id'])."000",'item_number' => $this->_tpl_vars['block']['properties']['item_number'],'show_add_to_cart' => $this->_tpl_vars['_show_add_to_cart'],'no_pagination' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>