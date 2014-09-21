<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:13
         compiled from addons/discussion/hooks/products/data_block.pre.tpl */ ?>

<?php if ($this->_tpl_vars['show_rating']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/views/discussion/components/average_rating.tpl", 'smarty_include_vars' => array('object_id' => $this->_tpl_vars['product']['product_id'],'object_type' => 'P')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>