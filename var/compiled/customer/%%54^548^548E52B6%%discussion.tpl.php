<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:14
         compiled from addons/discussion/blocks/product_tabs/discussion.tpl */ ?>
<?php
fn_preload_lang_vars(array('discussion_title_product'));
?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/views/discussion/view.tpl", 'smarty_include_vars' => array('object_id' => $this->_tpl_vars['product']['product_id'],'object_type' => 'P','title' => fn_get_lang_var('discussion_title_product', $this->getLanguage()),'quicklink' => 'disussion_link','no_box' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>