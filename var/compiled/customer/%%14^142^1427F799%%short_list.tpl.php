<?php /* Smarty version 2.6.18, created on 2014-09-16 21:38:45
         compiled from blocks/product_list_templates/short_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'blocks/product_list_templates/short_list.tpl', 4, false),)), $this); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "blocks/list_templates/compact_list.tpl", 'smarty_include_vars' => array('show_name' => true,'show_sku' => true,'show_price' => true,'show_add_to_cart' => smarty_modifier_default(@$this->_tpl_vars['show_add_to_cart'], true),'but_role' => 'act','hide_form' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>