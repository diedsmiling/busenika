<?php /* Smarty version 2.6.18, created on 2014-09-16 21:38:46
         compiled from addons/discussion/hooks/categories/view.post.tpl */ ?>
<?php
fn_preload_lang_vars(array('discussion_title_category'));
?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/views/discussion/view.tpl", 'smarty_include_vars' => array('object_id' => $this->_tpl_vars['category_data']['category_id'],'object_type' => 'C','title' => fn_get_lang_var('discussion_title_category', $this->getLanguage()),'wrap' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>