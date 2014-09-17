<?php /* Smarty version 2.6.18, created on 2014-09-16 21:38:45
         compiled from blocks/short_list.tpl */ ?>

<?php if ($this->_tpl_vars['block']['properties']['hide_add_to_cart_button'] == 'Y'): ?>
	<?php $this->assign('_show_add_to_cart', false, false); ?>
<?php else: ?>
	<?php $this->assign('_show_add_to_cart', true, false); ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "blocks/product_list_templates/short_list.tpl", 'smarty_include_vars' => array('products' => $this->_tpl_vars['items'],'no_sorting' => 'Y','no_pagination' => 'Y','obj_prefix' => ($this->_tpl_vars['block']['block_id'])."000",'show_add_to_cart' => $this->_tpl_vars['_show_add_to_cart'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>