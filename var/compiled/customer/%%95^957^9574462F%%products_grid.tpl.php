<?php /* Smarty version 2.6.18, created on 2014-09-17 00:29:31
         compiled from blocks/product_list_templates/products_grid.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'blocks/product_list_templates/products_grid.tpl', 4, false),)), $this); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "blocks/list_templates/products_grid.tpl", 'smarty_include_vars' => array('show_trunc_name' => true,'show_sku' => true,'show_rating' => true,'show_old_price' => true,'show_price' => true,'show_clean_price' => true,'show_add_to_cart' => smarty_modifier_default(@$this->_tpl_vars['show_add_to_cart'], true),'show_list_buttons' => true,'but_role' => 'action','separate_buttons' => true,'no_pagination' => $this->_tpl_vars['no_pagination'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>